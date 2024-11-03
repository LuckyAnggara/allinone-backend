<?php

namespace Database\Seeders;

use App\Models\ItemCategory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategoriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */

    public function run()
    {
        // Data kategori contoh
        $categories = [
            [
                'name' => 'Uncategorized',
                'description' => 'Tidak memiliki kategori',
                'branch_id' => 1,
                'created_by' => 1,
            ],
            [
                'name' => 'Elektronik',
                'description' => 'Kategori produk elektronik',
                'branch_id' => 1,
                'created_by' => 1,
            ],
            [
                'name' => 'Fashion',
                'description' => 'Kategori produk fashion',
                'branch_id' => 1,
                'created_by' => 1,
            ],
            [
                'name' => 'Otomotif',
                'description' => 'Kategori produk otomotif',
                'branch_id' => 1,
                'created_by' => 1,
            ],
            // Tambahkan data kategori lain sesuai kebutuhan
        ];

        // Loop melalui array data kategori dan masukkan ke dalam tabel "categories"
        foreach ($categories as $category) {
            ItemCategory::create($category);
        }
    }
}
