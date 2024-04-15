<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $guarded = [];

    public function purchases()
    {
        return $this->hasMany(Purchase::class);
    }

    public function province()
    {
        return $this->belongsTo(EcProvinsi::class, 'provinsi', 'prov_id');
    }

    public function kota()
    {
        return $this->belongsTo(EcKota::class, 'city', 'city_id');
    }

    public function kecamatans()
    {
        return $this->belongsTo(EcKecamatan::class, 'district', 'dis_id');
    }
}
