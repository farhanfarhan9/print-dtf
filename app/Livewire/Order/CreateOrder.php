<?php

namespace App\Livewire\Order;

use Carbon\Carbon;
use App\Models\Payment;
use Livewire\Component;
use App\Models\Customer;
use App\Models\Products;
use App\Models\Purchase;
use App\Models\Ekspedisi;
use Livewire\Attributes\On;
use App\Models\PurchaseOrder;
use Livewire\WithFileUploads;
use App\Models\InternalProcess;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class CreateOrder extends Component
{
    use WithFileUploads;

    public $customer_id;
    public $expedition_id;
    public $qty;
    public $product_price = 0;
    public $total_price = 0;
    public $shipped_price = 0;
    public $deposit_cut = 0;
    public $amount = 0;
    public $file;
    public $status;
    public $bank_detail;
    public $is_deposit;
    public $invoice_code;

    public $customer;
    public $expedition;
    public $product;

    public $customerModal;

    public $found;
    public $outOfStock;

    public $selectedProvinsi = null, $selectedKota = null, $selectedKecamatan = null, $selectedPostal = null;
    public $name, $city, $postal, $phone, $deposit, $address, $isReseller;

    public $additional_price = 0;
    public $discount = 0;


    public function mount()
    {
        $dateTime = Carbon::now();
        $timestamp = $dateTime->format('U'); // Get current Unix timestamp
        $randomSeed = $timestamp % 100000; // Ensure it's a 5-digit number
        $randomNumber = str_pad($randomSeed, 5, '0', STR_PAD_LEFT); // Ensure leading zeros if necessary

        $this->invoice_code = Carbon::now()->format('Y.m.d') . '.' . $randomNumber;
    }

    // public function rules()
    // {
    //     return [
    //         'customer_id' => 'required',
    //         'qty' => 'required',
    //         'expedition_id' => 'required',
    //         'status' => 'required',
    //         'bank_detail' => 'required',
    //         'file' => 'nullable|file|max:2000',
    //         'additional_price' => 'nullable',
    //         'discount' => 'nullable',
    //     ];
    // }

    public function addCustomerModal()
    {
        $this->customerModal = 1;
    }

    public function updateCities($value)
    {
        $this->selectedProvinsi = $value;
        // Assuming you might want to reset the city and district when the province changes
        $this->selectedKota = null;
        $this->selectedKecamatan = null;
        // Add logic here if you need to fetch cities based on the selected province
    }

    public function updateDistricts($value)
    {
        $this->selectedKota = $value;
        // Reset districts when the city changes or add logic to fetch new districts
        $this->selectedKecamatan = null;
    }

    public function updatePostal($value)
    {
        $this->selectedKecamatan = $value;
    }

    public function addUser()
    {
        $this->validate([
            'name' => 'required',
            'selectedProvinsi' => 'required',
            'selectedKota' => 'required',
            'selectedKecamatan' => 'required',
            'selectedPostal' => 'required|min:3|numeric',
            'phone' => 'required',
            'deposit' => 'nullable|numeric',
            'address' => 'required',
        ]);

        Customer::create([
            'name' => $this->name,
            'provinsi' => $this->selectedProvinsi,
            'city' => $this->selectedKota,
            'district' => $this->selectedKecamatan,
            'postal' => $this->selectedPostal,
            'phone' => $this->phone,
            'deposit' => $this->deposit ?: 0,
            'address' => $this->address,
            'is_reseller' => $this->isReseller ? true : false,
        ]);

        session()->flash('customerCreated', ['Sukses', 'Berhasil menambahkan data', 'success']);
        $this->customerModal = 0;
        $this->reset('customerModal', 'name', 'selectedProvinsi', 'selectedKota', 'selectedKecamatan', 'selectedPostal', 'phone', 'deposit', 'address', 'isReseller');
    }

    public function save()
    {
        $existingOpenOrder = Purchase::where('customer_id', $this->customer_id)->where('payment_status', 'open')->latest()->first();
        
        if($existingOpenOrder){
            $this->validate([
                'customer_id' => 'required',
                'qty' => 'required',
                'status' => 'required',
                'file' => 'nullable|file|max:2000',
                'additional_price' => 'nullable',
                'discount' => 'nullable',
            ]);
        }elseif ($this->status == 'Belum Bayar') {
            $this->validate([
                'customer_id' => 'required',
                'qty' => 'required',
                'expedition_id' => 'required',
                'status' => 'required',
                'file' => 'nullable|file|max:2000',
                'additional_price' => 'nullable',
                'discount' => 'nullable',
            ]);
        }else{
            $this->validate([
                'customer_id' => 'required',
                'qty' => 'required',
                'expedition_id' => 'required',
                'status' => 'required',
                'bank_detail' => 'required',
                'file' => 'nullable|file|max:2000',
                'additional_price' => 'nullable',
                'discount' => 'nullable',
            ]);
        }
        // dd($this->validate());

        $purchaseData = [
            'customer_id' => $this->customer_id,
            'user_id' => Auth::id(),
            'payment_status' => $this->status == 'Lunas' ? 'close' : 'open',
            'total_payment' => $this->total_price,
            'invoice_code' => $this->invoice_code,
        ];

        if ($existingOpenOrder) {
            // dd($existingOpenOrder->total_payment);
            $existingOpenOrder->update([
                'total_payment' => $existingOpenOrder->total_payment + $this->total_price
            ]);
            $purchase = $existingOpenOrder;
        } else {
            $purchase = Purchase::create($purchaseData);
        }

        $purchaseOrderData = [
            'invoice_code' => $this->invoice_code,
            'purchase_id' => $purchase->id,
            'product_id' => $this->product->id,
            'expedition_id' => $this->expedition_id ? $this->expedition_id : null ,
            'user_id' => Auth::id(),
            'expedition_price' => $this->expedition ? $this->expedition->ongkir : 0,
            'deposit_cut' => $this->deposit_cut,
            'product_price' => $this->product_price,
            'additional_price' => $this->additional_price,
            'discount' => $this->discount,
            'qty' => $this->qty,
            'status' => $this->status,
            'po_status' => 'open',
            'total_price' => $this->total_price,
        ];

        if ($this->status == 'Lunas') {
            $purchaseOrderData['po_status'] = 'close';
        }

        $purchaseOrder = PurchaseOrder::create($purchaseOrderData);

        InternalProcess::create([
            'purchase_order_id' => $purchaseOrder->id,
            'execution_date' => Carbon::now(),
        ]);

        $paymentAmount = $this->status == 'Cicil' && (int)$this->amount != 0 ? (int)$this->amount : $this->total_price;
        $is_dp = $this->status == 'Cicil' && (int)$this->amount != 0 ? 1 : 0;
        if ($this->file) {
            $this->file = $this->file->store('bukti_pembayaran', 'public');
        }

        if ($this->status == 'Cicil' && (int)$this->amount != 0) {
            Payment::create([
                'purchase_id' => $purchase->id,
                'amount' => $paymentAmount == 0 ? 0 : $paymentAmount,
                'is_dp' => $is_dp,
                'file' => $this->file,
                'bank_detail' => $this->bank_detail,
            ]);
        } elseif ($this->status == 'Lunas') {
            Payment::create([
                'purchase_id' => $purchase->id,
                'amount' => $this->total_price,
                'is_dp' => 0,
                'file' => $this->file,
                'bank_detail' => $this->bank_detail,
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
        session()->flash('orderCreated', ['Sukses', 'Berhasil menambahkan data', 'success']);
        $this->redirect(route('order.index'), navigate: true);
    }

    public function render()
    {

        $this->product = Products::first();
        $this->customer = Customer::find($this->customer_id);
        if ($this->customer) {

            if ($this->customer->is_reseller) {
                $price_range = json_decode($this->product['detail_harga_retail'], true);
            } else {
                $price_range = json_decode($this->product['detail_harga'], true);
            }


            $this->expedition = Ekspedisi::find($this->expedition_id);

            if ($this->qty <= $this->product->stok) {
                $this->outOfStock = false;
                foreach ($price_range as $range) {
                    $this->found = false; // Initialize the found flag to false for each iteration
                    if ($this->qty >= $range['start'] && $this->qty <= $range['end']) {
                        $this->product_price = $range['price'] * $this->qty;
                        $this->found = true;
                        break;
                    }
                }
            } else {
                $this->outOfStock = true;
                $this->found = true;
            }

            $this->shipped_price = $this->product_price + ($this->expedition ? $this->expedition->ongkir : 0) + $this->additional_price - $this->discount;
            $this->total_price = $this->shipped_price;

            if ($this->is_deposit && $this->customer) {
                $this->deposit_cut = min($this->shipped_price, $this->customer->deposit);
                $this->total_price -= $this->deposit_cut;
            }
        }


        return view('livewire.order.create-order', [
            'customer' => $this->customer,
            'expedition' => $this->expedition,
            'product' => $this->product
        ]);
    }
}
