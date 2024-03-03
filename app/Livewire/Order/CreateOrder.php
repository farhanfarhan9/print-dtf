<?php

namespace App\Livewire\Order;

use Livewire\Component;
use App\Models\Customer;
use App\Models\products;
use App\Models\Purchase;
use App\Models\ekspedisi;
use App\Models\Payment;
use App\Models\PurchaseOrder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class CreateOrder extends Component
{
    public $customer_id;
    public $expedition_id;
    public $qty;
    public $product_price = 0;
    public $total_price = 0;
    public $shipped_price = 0;
    public $deposit_cut = 0;
    public $amount = 0;
    public $status;
    public $is_deposit;

    public $customer;
    public $expedition;
    public $product;


    public function rules()
    {
        return [
            'customer_id' => 'required',
            // 'qty' => 'required',
            // 'expedition_id' => 'required',
            // 'status' => 'required',
        ];
    }

    public function save()
    {
        // $this->validate();
        // try {
        $existingOpenOrder = Purchase::where('customer_id', $this->customer_id)->where('payment_status', 'open')->latest()->first();
        if ($existingOpenOrder) {
            dd('tes');
        } else {
            $purchase = Purchase::create([
                'customer_id' => $this->customer_id,
                'user_id' => Auth::id(),
                'payment_status' => 'open'
            ]);

            $purchaseOrder = PurchaseOrder::create([
                'purchase_id' => $purchase->id,
                'product_id' => $this->product->id,
                'expedition_id' => $this->expedition_id,
                'expedition_price' => $this->expedition->ongkir,
                'deposit_cut' => $this->deposit_cut,
                'product_price' => $this->product_price,
                'qty' => $this->qty,
                'status' => $this->status,
                'total_price' => $this->total_price,
            ]);
            
            if ($this->status == 'Cicil' && $this->amount != 0) {
                Payment::create([
                    'purchase_order_id' => $purchaseOrder->id,
                    'amount' => $this->amount,
                ]);
            }elseif($this->status == 'Lunas'){
                Payment::create([
                    'purchase_order_id' => $purchaseOrder->id,
                    'amount' => $this->total_price,
                ]);
            }

            if ($this->is_deposit) {
                $this->customer->update([
                    'deposit' => $this->customer->deposit - $this->deposit_cut
                ]);
            }

            $this->product->update([
                'stok' => $this->product->stok - $this->qty
            ]);
        }   
        // session()->flash('OrderCreated',['Sukses', 'Berhasil menambahkan data', 'success']);
        // $this->redirect(route('customer.index'), navigate: true);
        // } catch (\Exception $e) {
        //     DB::rollBack();
        //     return redirect()->back()->with('error', 'An error occurred while processing your request.');

        // }
    }

    public function render()
    {
        $this->product = products::first();
        $price_range = json_decode($this->product['detail_harga'], true);
        $this->customer = Customer::find($this->customer_id);
        $this->expedition = ekspedisi::find($this->expedition_id);

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
            'product' => $this->product
        ]);
    }
}
