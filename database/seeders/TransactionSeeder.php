<?php

namespace Database\Seeders;

use App\Models\Transaction;
use App\Models\TransactionDetail;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class TransactionSeeder extends Seeder
{
    public function run(): void
    {
        // Transaksi hari ini
        $this->createTransaction('INV-' . date('Ymd') . '-001', 2, 'COMPLETED', 'CASH', Carbon::now()->subHours(5), [
            ['product_id' => 1, 'quantity' => 2],
            ['product_id' => 7, 'quantity' => 3],
        ]);

        $this->createTransaction('INV-' . date('Ymd') . '-002', 2, 'COMPLETED', 'QRIS', Carbon::now()->subHours(4), [
            ['product_id' => 3, 'quantity' => 1],
            ['product_id' => 8, 'quantity' => 2],
            ['product_id' => 13, 'quantity' => 1],
        ]);

        $this->createTransaction('INV-' . date('Ymd') . '-003', 3, 'COMPLETED', 'CASH', Carbon::now()->subHours(3), [
            ['product_id' => 2, 'quantity' => 3],
            ['product_id' => 9, 'quantity' => 2],
        ]);

        $this->createTransaction('INV-' . date('Ymd') . '-004', 2, 'VOID', 'CASH', Carbon::now()->subHours(2), [
            ['product_id' => 4, 'quantity' => 2],
            ['product_id' => 10, 'quantity' => 1],
        ], 'Pembatalan oleh pelanggan');

        $this->createTransaction('INV-' . date('Ymd') . '-005', 3, 'COMPLETED', 'DEBIT', Carbon::now()->subHours(1), [
            ['product_id' => 5, 'quantity' => 1],
            ['product_id' => 11, 'quantity' => 1],
            ['product_id' => 19, 'quantity' => 4],
        ]);

        // Transaksi kemarin
        $yesterday = Carbon::yesterday();
        $this->createTransaction('INV-' . $yesterday->format('Ymd') . '-001', 2, 'COMPLETED', 'CASH', $yesterday->setTime(9, 30), [
            ['product_id' => 6, 'quantity' => 2],
            ['product_id' => 12, 'quantity' => 2],
        ]);

        $this->createTransaction('INV-' . $yesterday->format('Ymd') . '-002', 3, 'COMPLETED', 'QRIS', $yesterday->setTime(11, 15), [
            ['product_id' => 1, 'quantity' => 3],
            ['product_id' => 7, 'quantity' => 4],
            ['product_id' => 14, 'quantity' => 2],
        ]);

        $this->createTransaction('INV-' . $yesterday->format('Ymd') . '-003', 2, 'COMPLETED', 'CASH', $yesterday->setTime(13, 45), [
            ['product_id' => 15, 'quantity' => 5],
            ['product_id' => 16, 'quantity' => 3],
        ]);

        $this->createTransaction('INV-' . $yesterday->format('Ymd') . '-004', 3, 'COMPLETED', 'DEBIT', $yesterday->setTime(15, 20), [
            ['product_id' => 21, 'quantity' => 1],
            ['product_id' => 25, 'quantity' => 10],
        ]);

        $this->createTransaction('INV-' . $yesterday->format('Ymd') . '-005', 2, 'VOID', 'CASH', $yesterday->setTime(16, 30), [
            ['product_id' => 3, 'quantity' => 2],
        ], 'Salah input');

        // Transaksi minggu lalu
        $lastWeek = Carbon::now()->subDays(7);
        $this->createTransaction('INV-' . $lastWeek->format('Ymd') . '-001', 2, 'COMPLETED', 'CASH', $lastWeek->setTime(10, 0), [
            ['product_id' => 2, 'quantity' => 2],
            ['product_id' => 8, 'quantity' => 3],
            ['product_id' => 13, 'quantity' => 2],
        ]);

        $this->createTransaction('INV-' . $lastWeek->format('Ymd') . '-002', 3, 'COMPLETED', 'QRIS', $lastWeek->setTime(14, 30), [
            ['product_id' => 4, 'quantity' => 1],
            ['product_id' => 9, 'quantity' => 2],
        ]);

        $this->createTransaction('INV-' . $lastWeek->format('Ymd') . '-003', 2, 'COMPLETED', 'CASH', $lastWeek->setTime(17, 0), [
            ['product_id' => 17, 'quantity' => 3],
            ['product_id' => 18, 'quantity' => 2],
        ]);

        // Transaksi bulan lalu
        $lastMonth = Carbon::now()->subMonth();
        $this->createTransaction('INV-' . $lastMonth->format('Ymd') . '-001', 2, 'COMPLETED', 'CASH', $lastMonth->setTime(11, 0), [
            ['product_id' => 1, 'quantity' => 5],
            ['product_id' => 7, 'quantity' => 5],
        ]);

        $this->createTransaction('INV-' . $lastMonth->format('Ymd') . '-002', 3, 'COMPLETED', 'DEBIT', $lastMonth->setTime(15, 0), [
            ['product_id' => 22, 'quantity' => 2],
            ['product_id' => 23, 'quantity' => 1],
        ]);
    }

    private function createTransaction(
        string $invoiceNumber,
        int $userId,
        string $status,
        string $paymentMethod,
        Carbon $createdAt,
        array $items,
        ?string $notes = null
    ): void {
        $totalPrice = 0;
        $details = [];

        foreach ($items as $item) {
            $product = Product::find($item['product_id']);
            if (!$product) continue;

            $subtotal = $product->selling_price * $item['quantity'];
            $totalPrice += $subtotal;

            $details[] = [
                'product_id' => $product->id,
                'quantity' => $item['quantity'],
                'unit_price' => $product->selling_price,
                'subtotal' => $subtotal,
            ];
        }

        $totalPaid = $totalPrice + rand(0, 10000);
        $change = $totalPaid - $totalPrice;

        $transaction = Transaction::create([
            'invoice_number' => $invoiceNumber,
            'user_id' => $userId,
            'total_price' => $totalPrice,
            'total_paid' => $totalPaid,
            'change' => $change,
            'payment_method' => $paymentMethod,
            'status' => $status,
            'notes' => $notes,
            'created_at' => $createdAt,
            'updated_at' => $createdAt,
        ]);

        foreach ($details as $detail) {
            TransactionDetail::create([
                'transaction_id' => $transaction->id,
                'product_id' => $detail['product_id'],
                'quantity' => $detail['quantity'],
                'unit_price' => $detail['unit_price'],
                'subtotal' => $detail['subtotal'],
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
            ]);
        }
    }
}
