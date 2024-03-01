<?php

namespace App\Livewire\Order;

use App\Models\Customer;
use App\Models\ekspedisi;
use App\Models\products;
use Livewire\Component;

class CreateOrder extends Component
{
    public $customer_id;
    public $ekspedition_id;
    public $qty;
    public $product_price = 0;
    public $total_price = 0;
    public $shipped_price = 0;
    public $deposit_cut = 0;
    public $payment_status;
    public $is_deposit;

    public $customer;
    public $expedition;

    public function render()
    {
        $product = products::first();
        $price_range = json_decode($product['detail_harga'], true);
        $this->customer = Customer::find($this->customer_id);
        $this->expedition = ekspedisi::find($this->ekspedition_id);

        foreach ($price_range as $range) {
            if ($this->qty >= $range['start'] && $this->qty <= $range['end']) {
                $this->product_price = $range['price'] * $this->qty;
                break;
            }
        }

        $this->shipped_price = $this->product_price + ($this->expedition ? $this->expedition->ongkir : 0);
        $this->total_price = $this->shipped_price;

        if ($this->is_deposit && $this->customer) {
            $this->deposit_cut = min($this->shipped_price, $this->customer->deposit);
            $this->total_price -= $this->deposit_cut;
        }

        return view('livewire.order.create-order', [
            'customer' => $this->customer,
            'expedition' => $this->expedition,
            'product' => $product
        ]);
    }
}
