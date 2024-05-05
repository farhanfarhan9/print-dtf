<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Products extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_produk',
        'detail_harga',
        'detail_harga_retail',
        'stok',
    ];

    protected $casts = [
        'detail_harga' => 'json',
        'detail_harga_retail' => 'json',
    ];
}
