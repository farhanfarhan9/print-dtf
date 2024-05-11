<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class);
    }

    public function purchaseOrderId()
    {
        return $this->belongsTo(PurchaseOrder::class, 'purchase_id', 'purchase_id');
    }

    public function purchase()
    {
        return $this->belongsTo(Purchase::class);
    }

}
