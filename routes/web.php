<?php

use App\Http\Controllers\Owner\OwnerController;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Inventory\InventoryController;
use App\Http\Controllers\Kasir\KasirController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn() => redirect()->route('login'));

Route::middleware(['auth', 'verified'])->group(function () {

    // Redirect dashboard ke halaman sesuai role
    Route::get('/dashboard', function () {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }
        return match ($user->role) {

            'owner' => redirect()->route('owner.dashboard'),
            'inventory' => redirect()->route('inventory.dashboard'),
            'kasir' => redirect()->route('kasir.pos'),
            default => redirect()->route('login'),
        };
    })->name('dashboard');

    // ─── Owner ───────────────────────────────────────────────
    Route::prefix('owner')->name('owner.')->middleware('can:owner')->group(function () {
        Route::get('/dashboard', [OwnerController::class, 'dashboard'])->name('dashboard');

        Route::get('/cashiers', [OwnerController::class, 'cashiersCreate'])->name('cashiers');
        Route::post('/cashiers', [OwnerController::class, 'cashiersStore'])->name('cashiers.store');


        Route::get('/sales-report', [OwnerController::class, 'salesReport'])->name('sales-report');
        Route::get('/product-report', [OwnerController::class, 'productReport'])->name('product-report');
        Route::get('/cashier-report', [OwnerController::class, 'cashierReport'])->name('cashier-report');
        Route::get('/audit-trail', [OwnerController::class, 'auditTrail'])->name('audit-trail');
        Route::get('/export-sales', [OwnerController::class, 'exportSales'])->name('export-sales');
        Route::get('/realtime-data', [OwnerController::class, 'realtimeData'])->name('realtime-data');
    });

    // ─── Inventory ───────────────────────────────────────────
    Route::prefix('inventory')->name('inventory.')->middleware('can:inventory')->group(function () {
        Route::get('/dashboard', [InventoryController::class, 'dashboard'])->name('dashboard');
        Route::get('/realtime-data', [InventoryController::class, 'realtimeData'])->name('realtime-data');

        Route::get('/products', [InventoryController::class, 'products'])->name('products');
        Route::post('/products', [InventoryController::class, 'storeProduct'])->name('products.store');
        Route::put('/products/{product}', [InventoryController::class, 'updateProduct'])->name('products.update');
        Route::delete('/products/{product}', [InventoryController::class, 'destroyProduct'])->name('products.destroy');

        Route::get('/categories', [InventoryController::class, 'categories'])->name('categories');
        Route::post('/categories', [InventoryController::class, 'storeCategory'])->name('categories.store');
        Route::put('/categories/{category}', [InventoryController::class, 'updateCategory'])->name('categories.update');
        Route::delete('/categories/{category}', [InventoryController::class, 'destroyCategory'])->name('categories.destroy');

        Route::get('/stock-in', [InventoryController::class, 'stockIn'])->name('stock-in');
        Route::post('/stock-in', [InventoryController::class, 'storeStockIn'])->name('stock-in.store');

        Route::get('/stock-out', [InventoryController::class, 'stockOut'])->name('stock-out');
        Route::post('/stock-out', [InventoryController::class, 'storeStockOut'])->name('stock-out.store');

        Route::get('/stock-logs', [InventoryController::class, 'stockLogs'])->name('stock-logs');
        Route::get('/low-stock', [InventoryController::class, 'lowStock'])->name('low-stock');
    });

    // ─── Kasir ───────────────────────────────────────────────
    Route::prefix('kasir')->name('kasir.')->middleware('can:kasir')->group(function () {
        Route::get('/pos', [KasirController::class, 'pos'])->name('pos');
        Route::get('/search-product', [KasirController::class, 'searchProduct'])->name('search-product');
        Route::post('/transactions', [KasirController::class, 'store'])->name('transactions.store');
        Route::post('/midtrans/token', [KasirController::class, 'createMidtransToken'])->name('midtrans.token');
        Route::post('/midtrans/store', [KasirController::class, 'storeMidtrans'])->name('midtrans.store');
        Route::get('/transactions', [KasirController::class, 'transactions'])->name('transactions');
        Route::post('/transactions/{transaction}/void', [KasirController::class, 'void'])->name('transactions.void');
    });

    // ─── Profile ─────────────────────────────────────────────
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
