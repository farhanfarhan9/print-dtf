<?php

namespace App\Livewire\Order\Po;

use Livewire\Component;
use App\Models\Customer;
use App\Models\Products;
use App\Models\Purchase;
use App\Models\Ekspedisi;
use App\Models\PurchaseOrder;

class EditPo extends Component
{
    public $customer_id;
    public $expedition_id;
    public $exist_qty;
    public $qty;
    public $product_price = 0;
    public $total_price = 0;
    public $shipped_price = 0;
    public $deposit_cut = 0;
    public $new_deposit_cut = 0;
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
        $this->exist_qty = $po->qty;
        $this->qty = $po->qty;
        $this->expedition_id = $po->expedition_id;
        $this->product_price = $po->product_price;
        $this->total_price = $po->total_price;
        $this->deposit_cut = $po->deposit_cut;
    }

    public function rules()
    {
        return [
            'qty' => 'required',
            'expedition_id' => 'required',
        ];
    }

    public function save()
    {
        $this->validate();

        $this->po->update([
            // 'deposit_cut' => $this->deposit_cut == 0 ? $this->deposit_cut : $this->deposit_cut + $this->new_deposit_cut,
            'expedition_id' => $this->expedition_id,
            'expedition_price' => $this->expedition->ongkir,
            'product_price' => $this->product_price,
            'qty' => $this->qty,
            'total_price' => $this->total_price,
        ]);

        // if ($this->is_deposit) {
        //     $this->customer->update([
        //         'deposit' => $this->customer->deposit - $this->new_deposit_cut
        //     ]);
        // }

        if ($this->exist_qty > $this->qty) {
            $this->product->update([
                'stok' => $this->product->stok + ($this->exist_qty - $this->qty)
            ]);
        } elseif ($this->exist_qty < $this->qty) {
            $this->product->update([
                'stok' => $this->product->stok - ($this->qty - $this->exist_qty)
            ]);
        }

        session()->flash('poEdited', ['Sukses', 'Berhasil mengedit data', 'success']);

        $this->redirect(route('po.allPo', $this->order->id), navigate: true);
    }

    public function render()
    {
        $this->product = Products::first();
        $this->expedition = Ekspedisi::find($this->expedition_id);
        $price_range = json_decode($this->product['detail_harga'], true);
        foreach ($price_range as $range) {
            if ($this->qty >= $range['start'] && $this->qty <= $range['end']) {
                $this->product_price = $range['price'] * $this->qty;
                break;
            }
        }

        $this->shipped_price = $this->product_price - $this->deposit_cut + ($this->expedition ? $this->expedition->ongkir : 0);

        $this->total_price = $this->shipped_price;
        // if ($this->is_deposit && $this->customer) {
        //     $this->new_deposit_cut = min($this->shipped_price, $this->customer->deposit);
        //     $this->total_price -= $this->new_deposit_cut;
        // }


        return view('livewire.order.po.edit-po');
    }
}
