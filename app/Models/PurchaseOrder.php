<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseOrder extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function purchase()
    {
        return $this->belongsTo(Purchase::class);
    }

    public function expedition()
    {
        return $this->belongsTo(ekspedisi::class, 'expedition_id', 'id');
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);

    }
}
