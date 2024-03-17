<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InternalProcess extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function purchase_order()
    {
        return $this->belongsTo(PurchaseOrder::class);
    }
}
