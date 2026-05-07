<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\StockLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class InventoryController extends Controller
{
    public function dashboard()
    {
        return inertia('Inventory/Dashboard', [
            'totalProducts' => Product::active()->count(),
            'totalCategories' => Category::count(),
            'lowStockCount' => Product::lowStock()->count(),
            'lowStockProducts' => Product::with('category')->lowStock()->limit(10)->get(),
            'recentStockLogs' => StockLog::with(['product', 'user'])->orderByDesc('created_at')->limit(10)->get(),
        ]);
    }

    public function realtimeData()
    {
        return response()->json([
            'totalProducts' => Product::active()->count(),
            'totalCategories' => Category::count(),
            'lowStockCount' => Product::lowStock()->count(),
            'lowStockProducts' => Product::with('category')->lowStock()->limit(10)->get(),
            'recentStockLogs' => StockLog::with(['product', 'user'])->orderByDesc('created_at')->limit(10)->get(),
        ]);
    }

    // Products
    public function products(Request $request)
    {
        $query = Product::with('category');
        if ($request->search) {
            $query->where(fn($q) => $q->where('name', 'like', "%{$request->search}%")->orWhere('sku', 'like', "%{$request->search}%"));
        }
        return inertia('Inventory/Products', [
            'products' => $query->paginate(20),
            'categories' => Category::all(['id', 'name']),
        ]);
    }

    public function storeProduct(Request $request)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'sku' => 'required|string|unique:products,sku',
            'barcode' => 'nullable|string|unique:products,barcode',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'stock_quantity' => 'required|integer|min:0',
            'min_stock' => 'required|integer|min:0',
            'purchase_price' => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:0',
        ]);

        DB::transaction(function () use ($validated) {
            $product = Product::create($validated);
            if ($validated['stock_quantity'] > 0) {
                StockLog::create([
                    'product_id' => $product->id,
                    'type' => 'IN',
                    'quantity' => $validated['stock_quantity'],
                    'stock_before' => 0,
                    'stock_after' => $validated['stock_quantity'],
                    'purchase_price' => $validated['purchase_price'],
                    'notes' => 'Stok awal produk',
                    'user_id' => Auth::id(),
                ]);
            }
        });

        return redirect()->route('inventory.products')->with('success', 'Produk berhasil ditambahkan');
    }

    public function updateProduct(Request $request, Product $product)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'sku' => 'required|string|unique:products,sku,' . $product->id,
            'barcode' => 'nullable|string|unique:products,barcode,' . $product->id,
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'min_stock' => 'required|integer|min:0',
            'purchase_price' => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:0',
        ]);
        $product->update($validated);
        return redirect()->route('inventory.products')->with('success', 'Produk berhasil diperbarui');
    }

    public function destroyProduct(Product $product)
    {
        $product->delete();
        return redirect()->route('inventory.products')->with('success', 'Produk berhasil dihapus');
    }

    // Categories
    public function categories()
    {
        return inertia('Inventory/Categories', [
            'categories' => Category::withCount('products')->paginate(20),
        ]);
    }

    public function storeCategory(Request $request)
    {
        $request->validate(['name' => 'required|string|unique:categories,name', 'description' => 'nullable|string']);
        Category::create($request->only('name', 'description'));
        return redirect()->route('inventory.categories')->with('success', 'Kategori berhasil ditambahkan');
    }

    public function updateCategory(Request $request, Category $category)
    {
        $request->validate(['name' => 'required|string|unique:categories,name,' . $category->id, 'description' => 'nullable|string']);
        $category->update($request->only('name', 'description'));
        return redirect()->route('inventory.categories')->with('success', 'Kategori berhasil diperbarui');
    }

    public function destroyCategory(Category $category)
    {
        $category->delete();
        return redirect()->route('inventory.categories')->with('success', 'Kategori berhasil dihapus');
    }

    // Stock In
    public function stockIn()
    {
        return inertia('Inventory/StockIn', ['products' => Product::active()->get(['id', 'name', 'stock_quantity'])]);
    }

    public function storeStockIn(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'purchase_price' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        DB::transaction(function () use ($validated) {
            $product = Product::findOrFail($validated['product_id']);
            $stockBefore = $product->stock_quantity;
            $stockAfter = $stockBefore + $validated['quantity'];
            $product->update(['stock_quantity' => $stockAfter, 'purchase_price' => $validated['purchase_price'] ?? $product->purchase_price]);
            StockLog::create([
                'product_id' => $validated['product_id'],
                'type' => 'IN',
                'quantity' => $validated['quantity'],
                'stock_before' => $stockBefore,
                'stock_after' => $stockAfter,
                'purchase_price' => $validated['purchase_price'] ?? $product->purchase_price,
                'notes' => $validated['notes'],
                'user_id' => Auth::id(),
            ]);
        });

        return redirect()->route('inventory.stock-in')->with('success', 'Stok masuk berhasil dicatat');
    }

    // Stock Out
    public function stockOut()
    {
        return inertia('Inventory/StockOut', ['products' => Product::active()->get(['id', 'name', 'stock_quantity'])]);
    }

    public function storeStockOut(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'notes' => 'required|string',
        ]);

        DB::transaction(function () use ($validated) {
            $product = Product::findOrFail($validated['product_id']);
            if ($product->stock_quantity < $validated['quantity']) {
                abort(422, 'Stok tidak mencukupi');
            }
            $stockBefore = $product->stock_quantity;
            $stockAfter = $stockBefore - $validated['quantity'];
            $product->update(['stock_quantity' => $stockAfter]);
            StockLog::create([
                'product_id' => $validated['product_id'],
                'type' => 'ADJUSTMENT',
                'quantity' => $validated['quantity'],
                'stock_before' => $stockBefore,
                'stock_after' => $stockAfter,
                'purchase_price' => $product->purchase_price,
                'notes' => $validated['notes'],
                'user_id' => Auth::id(),
            ]);
        });

        return redirect()->route('inventory.stock-out')->with('success', 'Stok keluar berhasil dicatat');
    }

    // Stock Logs
    public function stockLogs(Request $request)
    {
        $query = StockLog::with(['product', 'user']);
        if ($request->type) $query->where('type', $request->type);
        if ($request->product_id) $query->where('product_id', $request->product_id);

        return inertia('Inventory/StockLogs', [
            'stockLogs' => $query->orderByDesc('created_at')->paginate(20),
            'filters' => $request->only(['type', 'product_id']),
        ]);
    }

    // Low Stock
    public function lowStock()
    {
        return inertia('Inventory/LowStock', [
            'products' => Product::with('category')->lowStock()->get(),
        ]);
    }
}
