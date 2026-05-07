<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            // Makanan (category_id: 1)
            ['category_id' => 1, 'sku' => 'MKN001', 'barcode' => '8991234567890', 'name' => 'Nasi Goreng', 'description' => 'Nasi goreng spesial', 'stock_quantity' => 50, 'min_stock' => 10, 'purchase_price' => 12000, 'selling_price' => 18000, 'is_active' => true],
            ['category_id' => 1, 'sku' => 'MKN002', 'barcode' => '8991234567891', 'name' => 'Mie Goreng', 'description' => 'Mie goreng pedas', 'stock_quantity' => 45, 'min_stock' => 10, 'purchase_price' => 10000, 'selling_price' => 15000, 'is_active' => true],
            ['category_id' => 1, 'sku' => 'MKN003', 'barcode' => '8991234567892', 'name' => 'Ayam Goreng', 'description' => 'Ayam goreng crispy', 'stock_quantity' => 30, 'min_stock' => 8, 'purchase_price' => 15000, 'selling_price' => 22000, 'is_active' => true],
            ['category_id' => 1, 'sku' => 'MKN004', 'barcode' => '8991234567893', 'name' => 'Soto Ayam', 'description' => 'Soto ayam kuah kuning', 'stock_quantity' => 25, 'min_stock' => 8, 'purchase_price' => 13000, 'selling_price' => 20000, 'is_active' => true],
            ['category_id' => 1, 'sku' => 'MKN005', 'barcode' => '8991234567894', 'name' => 'Bakso', 'description' => 'Bakso sapi kuah', 'stock_quantity' => 8, 'min_stock' => 10, 'purchase_price' => 12000, 'selling_price' => 18000, 'is_active' => true],
            ['category_id' => 1, 'sku' => 'MKN006', 'barcode' => '8991234567895', 'name' => 'Gado-Gado', 'description' => 'Gado-gado sayur', 'stock_quantity' => 20, 'min_stock' => 8, 'purchase_price' => 10000, 'selling_price' => 15000, 'is_active' => true],

            // Minuman (category_id: 2)
            ['category_id' => 2, 'sku' => 'MNM001', 'barcode' => '8991234567896', 'name' => 'Es Teh Manis', 'description' => 'Teh manis dingin', 'stock_quantity' => 100, 'min_stock' => 20, 'purchase_price' => 2000, 'selling_price' => 5000, 'is_active' => true],
            ['category_id' => 2, 'sku' => 'MNM002', 'barcode' => '8991234567897', 'name' => 'Es Jeruk', 'description' => 'Jeruk peras segar', 'stock_quantity' => 80, 'min_stock' => 20, 'purchase_price' => 3000, 'selling_price' => 7000, 'is_active' => true],
            ['category_id' => 2, 'sku' => 'MNM003', 'barcode' => '8991234567898', 'name' => 'Kopi Hitam', 'description' => 'Kopi hitam panas', 'stock_quantity' => 60, 'min_stock' => 15, 'purchase_price' => 3500, 'selling_price' => 8000, 'is_active' => true],
            ['category_id' => 2, 'sku' => 'MNM004', 'barcode' => '8991234567899', 'name' => 'Cappuccino', 'description' => 'Cappuccino premium', 'stock_quantity' => 50, 'min_stock' => 15, 'purchase_price' => 5000, 'selling_price' => 12000, 'is_active' => true],
            ['category_id' => 2, 'sku' => 'MNM005', 'barcode' => '8991234567900', 'name' => 'Jus Alpukat', 'description' => 'Jus alpukat segar', 'stock_quantity' => 5, 'min_stock' => 10, 'purchase_price' => 6000, 'selling_price' => 12000, 'is_active' => true],
            ['category_id' => 2, 'sku' => 'MNM006', 'barcode' => '8991234567901', 'name' => 'Milkshake', 'description' => 'Milkshake coklat', 'stock_quantity' => 40, 'min_stock' => 10, 'purchase_price' => 7000, 'selling_price' => 15000, 'is_active' => true],

            // Snack (category_id: 3)
            ['category_id' => 3, 'sku' => 'SNK001', 'barcode' => '8991234567902', 'name' => 'Keripik Kentang', 'description' => 'Keripik kentang original', 'stock_quantity' => 150, 'min_stock' => 30, 'purchase_price' => 8000, 'selling_price' => 12000, 'is_active' => true],
            ['category_id' => 3, 'sku' => 'SNK002', 'barcode' => '8991234567903', 'name' => 'Coklat Batang', 'description' => 'Coklat susu batang', 'stock_quantity' => 120, 'min_stock' => 25, 'purchase_price' => 5000, 'selling_price' => 8000, 'is_active' => true],
            ['category_id' => 3, 'sku' => 'SNK003', 'barcode' => '8991234567904', 'name' => 'Biskuit', 'description' => 'Biskuit rasa keju', 'stock_quantity' => 100, 'min_stock' => 20, 'purchase_price' => 6000, 'selling_price' => 10000, 'is_active' => true],
            ['category_id' => 3, 'sku' => 'SNK004', 'barcode' => '8991234567905', 'name' => 'Permen', 'description' => 'Permen rasa buah', 'stock_quantity' => 200, 'min_stock' => 40, 'purchase_price' => 500, 'selling_price' => 1000, 'is_active' => true],
            ['category_id' => 3, 'sku' => 'SNK005', 'barcode' => '8991234567906', 'name' => 'Kacang Goreng', 'description' => 'Kacang goreng asin', 'stock_quantity' => 7, 'min_stock' => 15, 'purchase_price' => 7000, 'selling_price' => 11000, 'is_active' => true],
            ['category_id' => 3, 'sku' => 'SNK006', 'barcode' => '8991234567907', 'name' => 'Wafer', 'description' => 'Wafer coklat', 'stock_quantity' => 90, 'min_stock' => 20, 'purchase_price' => 4000, 'selling_price' => 7000, 'is_active' => true],

            // Elektronik (category_id: 4)
            ['category_id' => 4, 'sku' => 'ELK001', 'barcode' => '8991234567908', 'name' => 'Baterai AA', 'description' => 'Baterai alkaline AA', 'stock_quantity' => 200, 'min_stock' => 50, 'purchase_price' => 3000, 'selling_price' => 5000, 'is_active' => true],
            ['category_id' => 4, 'sku' => 'ELK002', 'barcode' => '8991234567909', 'name' => 'Baterai AAA', 'description' => 'Baterai alkaline AAA', 'stock_quantity' => 180, 'min_stock' => 50, 'purchase_price' => 2500, 'selling_price' => 4500, 'is_active' => true],
            ['category_id' => 4, 'sku' => 'ELK003', 'barcode' => '8991234567910', 'name' => 'Kabel USB', 'description' => 'Kabel USB Type-C', 'stock_quantity' => 50, 'min_stock' => 10, 'purchase_price' => 15000, 'selling_price' => 25000, 'is_active' => true],
            ['category_id' => 4, 'sku' => 'ELK004', 'barcode' => '8991234567911', 'name' => 'Earphone', 'description' => 'Earphone stereo', 'stock_quantity' => 30, 'min_stock' => 8, 'purchase_price' => 25000, 'selling_price' => 40000, 'is_active' => true],
            ['category_id' => 4, 'sku' => 'ELK005', 'barcode' => '8991234567912', 'name' => 'Power Bank', 'description' => 'Power bank 10000mAh', 'stock_quantity' => 3, 'min_stock' => 5, 'purchase_price' => 80000, 'selling_price' => 120000, 'is_active' => true],
            ['category_id' => 4, 'sku' => 'ELK006', 'barcode' => '8991234567913', 'name' => 'Flashdisk 16GB', 'description' => 'USB Flashdisk 16GB', 'stock_quantity' => 25, 'min_stock' => 8, 'purchase_price' => 35000, 'selling_price' => 55000, 'is_active' => true],

            // Alat Tulis (category_id: 5)
            ['category_id' => 5, 'sku' => 'ATK001', 'barcode' => '8991234567914', 'name' => 'Pulpen Biru', 'description' => 'Pulpen tinta biru', 'stock_quantity' => 300, 'min_stock' => 50, 'purchase_price' => 1500, 'selling_price' => 3000, 'is_active' => true],
            ['category_id' => 5, 'sku' => 'ATK002', 'barcode' => '8991234567915', 'name' => 'Pensil 2B', 'description' => 'Pensil kayu 2B', 'stock_quantity' => 250, 'min_stock' => 50, 'purchase_price' => 1000, 'selling_price' => 2500, 'is_active' => true],
            ['category_id' => 5, 'sku' => 'ATK003', 'barcode' => '8991234567916', 'name' => 'Buku Tulis', 'description' => 'Buku tulis 38 lembar', 'stock_quantity' => 100, 'min_stock' => 20, 'purchase_price' => 3000, 'selling_price' => 5500, 'is_active' => true],
            ['category_id' => 5, 'sku' => 'ATK004', 'barcode' => '8991234567917', 'name' => 'Penghapus', 'description' => 'Penghapus putih', 'stock_quantity' => 200, 'min_stock' => 40, 'purchase_price' => 500, 'selling_price' => 1500, 'is_active' => true],
            ['category_id' => 5, 'sku' => 'ATK005', 'barcode' => '8991234567918', 'name' => 'Penggaris 30cm', 'description' => 'Penggaris plastik 30cm', 'stock_quantity' => 4, 'min_stock' => 15, 'purchase_price' => 2000, 'selling_price' => 4000, 'is_active' => true],
            ['category_id' => 5, 'sku' => 'ATK006', 'barcode' => '8991234567919', 'name' => 'Spidol Hitam', 'description' => 'Spidol permanent hitam', 'stock_quantity' => 80, 'min_stock' => 20, 'purchase_price' => 4000, 'selling_price' => 7000, 'is_active' => true],
        ];

        foreach ($products as $product) {
            Product::create($product);
        }
    }
}
