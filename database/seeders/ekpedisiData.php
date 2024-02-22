<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Ekspedisi;

class ekpedisiData extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Ekspedisi::create(
            [
                'nama_ekspedisi' => 'JNT',
                'ongkir' => 4000,
            ],
        );
        Ekspedisi::create(
            [
                'nama_ekspedisi' => 'TiKi',
                'ongkir' => 5000,
            ],
        );
    }
}
