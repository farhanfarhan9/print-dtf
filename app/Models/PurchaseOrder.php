<?php

namespace App\Models;

use App\Livewire\Product;
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
        return $this->belongsTo(Ekspedisi::class, 'expedition_id', 'id')->withTrashed();
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function product()
    {
        return $this->belongsTo(Products::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class)->withTrashed();
    }

    public function internal_process()
    {
        return $this->hasOne(InternalProcess::class);
    }
}
