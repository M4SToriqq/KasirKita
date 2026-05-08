<?php

namespace Database\Seeders;

use App\Models\StockLog;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class StockLogSeeder extends Seeder
{
    public function run(): void
    {
        // Stock IN - Initial stock
        $this->createStockLog(1, 'IN', 50, 0, 50, 12000, 4, Carbon::now()->subDays(30), 'Stok awal');
        $this->createStockLog(2, 'IN', 45, 0, 45, 10000, 4, Carbon::now()->subDays(30), 'Stok awal');
        $this->createStockLog(3, 'IN', 30, 0, 30, 15000, 4, Carbon::now()->subDays(30), 'Stok awal');
        $this->createStockLog(7, 'IN', 100, 0, 100, 2000, 4, Carbon::now()->subDays(30), 'Stok awal');
        $this->createStockLog(13, 'IN', 150, 0, 150, 8000, 4, Carbon::now()->subDays(30), 'Stok awal');

        // Stock IN - Restock minggu lalu
        $this->createStockLog(5, 'IN', 20, 5, 25, 12000, 4, Carbon::now()->subDays(7), 'Restock dari supplier');
        $this->createStockLog(11, 'IN', 30, 15, 45, 6000, 4, Carbon::now()->subDays(7), 'Restock dari supplier');
        $this->createStockLog(17, 'IN', 50, 30, 80, 7000, 4, Carbon::now()->subDays(7), 'Restock dari supplier');

        // Stock IN - Restock kemarin
        $this->createStockLog(5, 'IN', 5, 8, 13, 12000, 4, Carbon::yesterday(), 'Restock darurat');
        $this->createStockLog(11, 'IN', 10, 5, 15, 6000, 4, Carbon::yesterday(), 'Restock darurat');

        // Stock OUT - Manual
        $this->createStockLog(1, 'OUT', 5, 50, 45, 12000, 4, Carbon::now()->subDays(5), 'Rusak/kadaluarsa');
        $this->createStockLog(7, 'OUT', 10, 100, 90, 2000, 4, Carbon::now()->subDays(5), 'Rusak/kadaluarsa');
        $this->createStockLog(13, 'OUT', 20, 150, 130, 8000, 4, Carbon::now()->subDays(3), 'Promo/sample');

        // Stock ADJUSTMENT
        $this->createStockLog(2, 'ADJUSTMENT', -5, 50, 45, 10000, 4, Carbon::now()->subDays(3), 'Koreksi stok opname');
        $this->createStockLog(8, 'ADJUSTMENT', 10, 70, 80, 3000, 4, Carbon::now()->subDays(3), 'Koreksi stok opname');
        $this->createStockLog(14, 'ADJUSTMENT', -10, 130, 120, 5000, 4, Carbon::now()->subDays(2), 'Koreksi stok opname');

        // Stock VOID - Dari transaksi void
        $this->createStockLog(4, 'VOID', 2, 23, 25, 13000, 2, Carbon::now()->subHours(2), 'Void transaksi INV-' . date('Ymd') . '-004');
        $this->createStockLog(10, 'VOID', 1, 49, 50, 5000, 2, Carbon::now()->subHours(2), 'Void transaksi INV-' . date('Ymd') . '-004');
        $this->createStockLog(3, 'VOID', 2, 28, 30, 15000, 2, Carbon::yesterday()->setTime(16, 30), 'Void transaksi INV-' . Carbon::yesterday()->format('Ymd') . '-005');

        // Stock IN - Hari ini
        $this->createStockLog(29, 'IN', 50, 4, 54, 2000, 4, Carbon::now()->subHours(3), 'Restock penggaris');
        $this->createStockLog(23, 'IN', 20, 3, 23, 80000, 4, Carbon::now()->subHours(2), 'Restock power bank');
    }

    private function createStockLog(
        int $productId,
        string $type,
        int $quantity,
        int $stockBefore,
        int $stockAfter,
        ?float $purchasePrice,
        int $userId,
        Carbon $createdAt,
        string $notes
    ): void {
        StockLog::create([
            'product_id' => $productId,
            'type' => $type,
            'quantity' => $quantity,
            'stock_before' => $stockBefore,
            'stock_after' => $stockAfter,
            'purchase_price' => $purchasePrice,
            'user_id' => $userId,
            'notes' => $notes,
            'created_at' => $createdAt,
            'updated_at' => $createdAt,
        ]);
    }
}
