<?php

namespace App\Livewire\Order\Po;

use Livewire\Component;
use App\Models\Customer;
use App\Models\products;
use App\Models\Purchase;
use App\Models\ekspedisi;
use App\Models\PurchaseOrder;

class EditPo extends Component
{
    public $customer_id;
    public $expedition_id;
    public $qty;
    public $product_price;
    public $total_price;
    public $shipped_price;
    public $deposit_cut;
    public $amount;
    public $status;
    public $is_deposit;
    public $invoice_code;

    public $customer;
    public $expedition;
    public $product;

    // parameter variable
    public $order;
    public $po;



    public function mount(Purchase $order, PurchaseOrder $po)
    {
        $this->customer_id = $order->customer_id;
        $this->customer = Customer::find($order->customer_id);
        $this->qty = $po->qty;
        $this->expedition_id = $po->expedition_id;
    }

    public function render()
    {
        $this->product = products::first();
        $this->expedition = ekspedisi::find($this->expedition_id);
        $price_range = json_decode($this->product['detail_harga'], true);
        foreach ($price_range as $range) {
            if ($this->qty >= $range['start'] && $this->qty <= $range['end']) {
                $this->product_price = $range['price'] * $this->qty;
                break;
            }
        }
        $this->shipped_price = $this->product_price + ($this->expedition ? $this->expedition->ongkir : 0);
        $this->total_price = $this->shipped_price;


        return view('livewire.order.po.edit-po');
    }
}
