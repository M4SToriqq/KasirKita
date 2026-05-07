<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\StockLog;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use App\Models\User;
use App\Exports\TransactionsExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ProductsExport;
use Illuminate\Support\Facades\Response;

class OwnerController extends Controller
{
    public function dashboard(Request $request)
    {
        $dateFrom = $request->input('date_from', now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->input('date_to', now()->endOfMonth()->format('Y-m-d'));

        $summary = [
            'totalSales' => Transaction::completed()->whereBetween('created_at', [$dateFrom, $dateTo])->sum('total_price'),
            'totalTransactions' => Transaction::completed()->whereBetween('created_at', [$dateFrom, $dateTo])->count(),
            'totalProfit' => $this->calculateProfit($dateFrom, $dateTo),
            'totalItemsSold' => TransactionDetail::whereHas('transaction', fn($q) => $q->completed()->whereBetween('created_at', [$dateFrom, $dateTo]))->sum('quantity'),
        ];

        $salesByPayment = Transaction::completed()
            ->whereBetween('created_at', [$dateFrom, $dateTo])
            ->select('payment_method', DB::raw('SUM(total_price) as total'))
            ->groupBy('payment_method')->get();

        $topProducts = TransactionDetail::whereHas('transaction', fn($q) => $q->completed()->whereBetween('created_at', [$dateFrom, $dateTo]))
            ->select('product_id', DB::raw('SUM(quantity) as total_sold'), DB::raw('SUM(subtotal) as total_revenue'))
            ->groupBy('product_id')->orderByDesc('total_sold')->limit(5)->get()
            ->map(fn($item) => tap($item, fn($i) => $i->product_name = Product::find($i->product_id)?->name ?? 'Dihapus'));

        return inertia('Owner/Dashboard', [
            'summary' => $summary,
            'salesByPayment' => $salesByPayment,
            'todaySales' => Transaction::today()->completed()->sum('total_price'),
            'todayTransactions' => Transaction::today()->completed()->count(),
            'lowStockProducts' => Product::lowStock()->count(),
            'recentTransactions' => Transaction::with(['user', 'details'])->completed()->orderByDesc('created_at')->limit(10)->get(),
            'topProducts' => $topProducts,
            'filters' => compact('dateFrom', 'dateTo'),
        ]);
    }

    public function salesReport(Request $request)
    {
        $dateFrom = $request->input('date_from', now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->input('date_to', now()->endOfMonth()->format('Y-m-d'));

        $transactions = Transaction::completed()->whereBetween('created_at', [$dateFrom, $dateTo])
            ->with(['user', 'details'])->orderByDesc('created_at')->get();

        return inertia('Owner/SalesReport', [
            'transactions' => $transactions,
            'totalRevenue' => $transactions->sum('total_price'),
            'totalProfit' => $this->calculateProfit($dateFrom, $dateTo),
            'filters' => compact('dateFrom', 'dateTo'),
        ]);
    }

    public function productReport(Request $request)
    {
        $dateFrom = $request->input('date_from', now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->input('date_to', now()->endOfMonth()->format('Y-m-d'));

        $products = Product::with('category')->active()->get()->map(function ($p) use ($dateFrom, $dateTo) {
            $soldQty = TransactionDetail::whereHas('transaction', fn($q) => $q->completed()->whereBetween('created_at', [$dateFrom, $dateTo]))->where('product_id', $p->id)->sum('quantity');
            $revenue = TransactionDetail::whereHas('transaction', fn($q) => $q->completed()->whereBetween('created_at', [$dateFrom, $dateTo]))->where('product_id', $p->id)->sum('subtotal');
            $p->sold_qty = $soldQty;
            $p->revenue = $revenue;
            $p->profit = ($p->selling_price - $p->purchase_price) * $soldQty;
            return $p;
        })->sortByDesc('sold_qty')->values();

        return inertia('Owner/ProductReport', ['products' => $products, 'filters' => compact('dateFrom', 'dateTo')]);
    }

    public function cashierReport(Request $request)
    {
        $dateFrom = $request->input('date_from', now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->input('date_to', now()->endOfMonth()->format('Y-m-d'));

        $cashiers = Transaction::completed()->whereBetween('created_at', [$dateFrom, $dateTo])
            ->select('user_id', DB::raw('COUNT(*) as total_transactions'), DB::raw('SUM(total_price) as total_sales'))
            ->groupBy('user_id')->get()
            ->map(fn($item) => tap($item, fn($i) => $i->cashier_name = User::find($i->user_id)?->name ?? 'Dihapus'));

        return inertia('Owner/CashierReport', ['cashiers' => $cashiers, 'filters' => compact('dateFrom', 'dateTo')]);
    }

    public function auditTrail(Request $request)
    {
        $dateFrom = $request->input('date_from', now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->input('date_to', now()->endOfMonth()->format('Y-m-d'));

        return inertia('Owner/AuditTrail', [
            'voidedTransactions' => Transaction::voided()->whereBetween('created_at', [$dateFrom, $dateTo])->with(['user', 'details'])->orderByDesc('created_at')->get(),
            'stockAdjustments' => StockLog::where('type', 'ADJUSTMENT')->whereBetween('created_at', [$dateFrom, $dateTo])->with(['product', 'user'])->orderByDesc('created_at')->get(),
            'filters' => compact('dateFrom', 'dateTo'),
        ]);
    }

    private function calculateProfit(string $dateFrom, string $dateTo): float
    {
        return Transaction::completed()->whereBetween('created_at', [$dateFrom, $dateTo])->with('details.product')->get()
            ->sum(fn($t) => $t->details->sum(fn($d) => $d->product ? ($d->unit_price - $d->product->purchase_price) * $d->quantity : 0));
    }

    public function exportSales(Request $request)
    {
        $dateFrom = (string) $request->input('date_from', now()->startOfMonth()->format('Y-m-d'));
        $dateTo = (string) $request->input('date_to', now()->endOfMonth()->format('Y-m-d'));

        return Excel::download(
            new TransactionsExport($dateFrom, $dateTo),
            'laporan-penjualan-' . date('Y-m-d') . '.xlsx'
        );
    }

    public function exportProducts()
    {
        return Excel::download(new ProductsExport(), 'data-produk-' . date('Y-m-d') . '.xlsx');
    }

    public function realtimeData(Request $request)
    {
        $dateFrom = $request->input('date_from', now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->input('date_to', now()->endOfMonth()->format('Y-m-d'));

        return response()->json([
            'summary' => [
                'totalSales' => Transaction::completed()->whereBetween('created_at', [$dateFrom, $dateTo])->sum('total_price'),
                'totalTransactions' => Transaction::completed()->whereBetween('created_at', [$dateFrom, $dateTo])->count(),
                'totalProfit' => $this->calculateProfit($dateFrom, $dateTo),
                'totalItemsSold' => TransactionDetail::whereHas('transaction', fn($q) => $q->completed()->whereBetween('created_at', [$dateFrom, $dateTo]))->sum('quantity'),
            ],
            'todaySales' => Transaction::today()->completed()->sum('total_price'),
            'todayTransactions' => Transaction::today()->completed()->count(),
            'lowStockProducts' => Product::lowStock()->count(),
            'recentTransactions' => Transaction::with(['user', 'details'])->completed()->orderByDesc('created_at')->limit(10)->get(),
        ]);
    }
}
