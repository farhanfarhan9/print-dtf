<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Products;

class ProductsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        products::create([
            'nama_produk' => 'Kain A',
            'detail_harga' => json_encode([
                'detail_1' => [
                    'range_awal' => 1,
                    'range_akhir' => 4,
                    'harga' => 10000
                ],
                'detail_2' => [
                    'range_awal' => 5,
                    'range_akhir' => 8,
                    'harga' => 20000
                ]
            ]),
            'stok' => 400,
        ]);
    }
}
