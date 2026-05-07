<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Makanan', 'description' => 'Produk makanan siap saji'],
            ['name' => 'Minuman', 'description' => 'Berbagai jenis minuman'],
            ['name' => 'Snack', 'description' => 'Makanan ringan dan cemilan'],
            ['name' => 'Elektronik', 'description' => 'Perangkat elektronik'],
            ['name' => 'Alat Tulis', 'description' => 'Perlengkapan tulis menulis'],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}
