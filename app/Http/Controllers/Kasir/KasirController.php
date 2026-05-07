<?php

namespace App\Http\Controllers\Kasir;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\StockLog;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use App\Services\MidtransService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class KasirController extends Controller
{
    public function pos()
    {
        return inertia('Kasir/Pos', [
            'categories' => Category::with(['products' => fn($q) => $q->active()->orderBy('name')])->get(),
        ]);
    }

    public function searchProduct(Request $request)
    {
        $q = $request->input('q', '');
        return response()->json(
            Product::active()
                ->where(fn($query) => $query->where('name', 'like', "%{$q}%")->orWhere('barcode', $q)->orWhere('sku', 'like', "%{$q}%"))
                ->limit(10)
                ->get(['id', 'name', 'sku', 'barcode', 'selling_price', 'stock_quantity'])
        );
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'total_price' => 'required|numeric|min:0',
            'total_paid' => 'required|numeric|min:0',
            'change' => 'required|numeric|min:0',
            'payment_method' => 'required|in:CASH,MIDTRANS',
        ]);

        foreach ($validated['items'] as $item) {
            $product = Product::find($item['product_id']);
            if ($product->stock_quantity < $item['quantity']) {
                return back()->with('error', "Stok {$product->name} tidak cukup");
            }
        }

        return DB::transaction(function () use ($validated) {
            $invoiceNumber = 'INV-' . date('Ymd') . '-' . str_pad(Transaction::today()->count() + 1, 4, '0', STR_PAD_LEFT);

            $transaction = Transaction::create([
                'invoice_number' => $invoiceNumber,
                'user_id' => Auth::id(),
                'total_price' => $validated['total_price'],
                'total_paid' => $validated['total_paid'],
                'change' => $validated['change'],
                'payment_method' => $validated['payment_method'],
                'status' => 'COMPLETED',
            ]);

            foreach ($validated['items'] as $item) {
                $product = Product::find($item['product_id']);
                $subtotal = $product->selling_price * $item['quantity'];
                $stockBefore = $product->stock_quantity;
                $stockAfter = $stockBefore - $item['quantity'];

                TransactionDetail::create([
                    'transaction_id' => $transaction->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $product->selling_price,
                    'subtotal' => $subtotal,
                ]);

                $product->update(['stock_quantity' => $stockAfter]);

                StockLog::create([
                    'product_id' => $item['product_id'],
                    'type' => 'OUT',
                    'quantity' => $item['quantity'],
                    'stock_before' => $stockBefore,
                    'stock_after' => $stockAfter,
                    'purchase_price' => $product->purchase_price,
                    'notes' => "Penjualan via {$invoiceNumber}",
                    'user_id' => Auth::id(),
                ]);
            }

            return redirect()->route('kasir.pos')->with('receipt', $transaction->load('details.product'));
        });
    }

    public function transactions(Request $request)
    {
        $transactions = Transaction::where('user_id', Auth::id())
            ->orderByDesc('created_at')->paginate(20);

        return inertia('Kasir/Transactions', ['transactions' => $transactions]);
    }

    public function createMidtransToken(Request $request)
    {
        try {
            $validated = $request->validate([
                'items' => 'required|array|min:1',
                'items.*.product_id' => 'required|exists:products,id',
                'items.*.quantity' => 'required|integer|min:1',
                'total_price' => 'required|numeric|min:0',
            ]);

            // Generate unique invoice number with microseconds to avoid duplicates
            $invoiceNumber = 'INV-' . date('YmdHis') . '-' . substr(uniqid(), -4);

            $items = [];
            foreach ($validated['items'] as $item) {
                $product = Product::find($item['product_id']);
                $items[] = [
                    'id' => $product->id,
                    'price' => (int) $product->selling_price,
                    'quantity' => $item['quantity'],
                    'name' => $product->name,
                ];
            }

            $customerDetails = [
                'first_name' => Auth::user()->name,
                'email' => Auth::user()->email,
                'phone' => Auth::user()->phone ?? '08123456789',
            ];

            $midtrans = new MidtransService();
            $snapToken = $midtrans->getSnapToken(
                $invoiceNumber,
                (int) $validated['total_price'],
                $customerDetails,
                $items
            );

            return response()->json([
                'snap_token' => $snapToken,
                'invoice_number' => $invoiceNumber,
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Midtrans error: ' . $e->getMessage()], 500);
        }
    }

    public function storeMidtrans(Request $request)
    {
        $validated = $request->validate([
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'total_price' => 'required|numeric|min:0',
            'invoice_number' => 'required|string',
        ]);

        foreach ($validated['items'] as $item) {
            $product = Product::find($item['product_id']);
            if ($product->stock_quantity < $item['quantity']) {
                return response()->json(['error' => "Stok {$product->name} tidak cukup"], 400);
            }
        }

        return DB::transaction(function () use ($validated) {
            $transaction = Transaction::create([
                'invoice_number' => $validated['invoice_number'],
                'user_id' => Auth::id(),
                'total_price' => $validated['total_price'],
                'total_paid' => $validated['total_price'],
                'change' => 0,
                'payment_method' => 'MIDTRANS',
                'status' => 'COMPLETED',
            ]);

            foreach ($validated['items'] as $item) {
                $product = Product::find($item['product_id']);
                $subtotal = $product->selling_price * $item['quantity'];
                $stockBefore = $product->stock_quantity;
                $stockAfter = $stockBefore - $item['quantity'];

                TransactionDetail::create([
                    'transaction_id' => $transaction->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $product->selling_price,
                    'subtotal' => $subtotal,
                ]);

                $product->update(['stock_quantity' => $stockAfter]);

                StockLog::create([
                    'product_id' => $item['product_id'],
                    'type' => 'OUT',
                    'quantity' => $item['quantity'],
                    'stock_before' => $stockBefore,
                    'stock_after' => $stockAfter,
                    'purchase_price' => $product->purchase_price,
                    'notes' => "Penjualan via {$validated['invoice_number']}",
                    'user_id' => Auth::id(),
                ]);
            }

            return response()->json(['success' => true, 'transaction' => $transaction->load('details.product')]);
        });
    }

    public function void(Request $request, Transaction $transaction)
    {
        if ($transaction->user_id !== Auth::id()) abort(403);
        if ($transaction->status === 'VOID') return back()->with('error', 'Transaksi sudah dibatalkan');

        $request->validate(['notes' => 'required|string']);

        DB::transaction(function () use ($transaction, $request) {
            foreach ($transaction->details as $detail) {
                $product = $detail->product;
                $stockBefore = $product->stock_quantity;
                $stockAfter = $stockBefore + $detail->quantity;
                $product->update(['stock_quantity' => $stockAfter]);
                StockLog::create([
                    'product_id' => $detail->product_id,
                    'type' => 'VOID',
                    'quantity' => $detail->quantity,
                    'stock_before' => $stockBefore,
                    'stock_after' => $stockAfter,
                    'purchase_price' => $detail->unit_price,
                    'notes' => "Void {$transaction->invoice_number}: {$request->notes}",
                    'user_id' => Auth::id(),
                ]);
            }
            $transaction->update(['status' => 'VOID', 'notes' => $request->notes]);
        });

        return redirect()->route('kasir.transactions')->with('success', 'Transaksi berhasil dibatalkan');
    }
}
