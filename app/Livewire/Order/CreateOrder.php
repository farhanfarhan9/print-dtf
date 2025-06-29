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
    public $qty = 0;
    public $product_price = 0;
    public $total_price = 0;
    public $shipped_price = 0;
    public $deposit_cut = 0;
    public $amount = 0;
    public $file;
    public $status;
    public $bank_detail;
    public $is_deposit;
    public $to_deposit;
    public $invoice_code;
    public $without_dtf;

    public $customer;
    public $expedition;
    public $products;
    public $product_id;

    public $customerModal;

    public $found;
    public $outOfStock;

    public $selectedProvinsi = null, $selectedKota = null, $selectedKecamatan = null, $selectedPostal = null;
    public $name, $city, $postal, $phone, $deposit, $address, $isReseller, $expedition_customer;

    public $additional_price = 0;
    public $discount = 0;

    public $paid_amount = 0;

    public $isExpeditionManuallySet = false;

    public $selectedProduct;


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
            'selectedKecamatan' => 'nullable',
            'selectedPostal' => 'nullable|min:3|numeric',
            'phone' => 'required',
            'deposit' => 'nullable|numeric',
            'address' => 'required',
            'expedition_customer' => 'nullable',
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
            'id_ekspedisi' => $this->expedition_customer,
            'is_reseller' => $this->isReseller ? true : false,
        ]);

        session()->flash('customerCreated', ['Sukses', 'Berhasil menambahkan data', 'success']);
        $this->customerModal = 0;
        $this->reset('customerModal', 'name', 'selectedProvinsi', 'selectedKota', 'selectedKecamatan', 'selectedPostal', 'phone', 'deposit', 'address', 'isReseller');
    }

    public function save()
    {
        $existingOpenOrder = Purchase::where('customer_id', $this->customer_id)->where('payment_status', 'open')->latest()->first();

        if ($existingOpenOrder) {
            $this->validate([
                'customer_id' => 'required',
                'qty' => $this->without_dtf ? 'nullable' : 'required',
                'status' => 'required',
                'file' => 'nullable|file|max:2000',
                'additional_price' => $this->without_dtf ? 'nullable' : 'required',
                'discount' => 'nullable',
            ]);
        } elseif ($this->status == 'Belum Bayar') {
            $this->validate([
                'customer_id' => 'required',
                'qty' => $this->without_dtf ? 'nullable' : 'required',
                'expedition_id' => 'required',
                'status' => 'required',
                'file' => 'nullable|file|max:2000',
                'additional_price' => $this->without_dtf ? 'nullable' : 'required',
                'discount' => 'nullable',
            ]);
        } else {
            $this->validate([
                'customer_id' => 'required',
                'qty' => $this->without_dtf ? 'nullable' : 'required',
                'expedition_id' => 'required',
                'status' => 'required',
                'bank_detail' => 'required',
                'file' => 'nullable|file|max:2000',
                'additional_price' => $this->without_dtf ? 'nullable' : 'required',
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
        if ($this->status == 'Lunas' && $this->to_deposit) {
            $purchaseData['total_payment'] = $this->paid_amount;
        }

        if ($existingOpenOrder) {
            // dd($existingOpenOrder->total_payment);
            if ($this->status == 'Lunas' && $this->to_deposit) {
                $existingOpenOrder->update([
                    'total_payment' => $existingOpenOrder->total_payment + $this->paid_amount
                ]);
            } else {
                $existingOpenOrder->update([
                    'total_payment' => $existingOpenOrder->total_payment + $this->total_price
                ]);
            }
            $purchase = $existingOpenOrder;
        } else {
            $purchase = Purchase::create($purchaseData);
        }


        $purchaseOrderData = [
            'invoice_code' => $this->invoice_code,
            'purchase_id' => $purchase->id,
            'product_id' => $this->without_dtf ? null : $this->product_id,
            'expedition_id' => $this->expedition_id ? $this->expedition_id : null,
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

        if ($this->status == 'Lunas' && $this->to_deposit) {
            $purchaseOrderData['total_price'] = $this->paid_amount;
            $purchaseOrderData['to_deposit'] = $this->paid_amount - $this->total_price;
            $purchaseOrderData['po_status'] = 'close';
        } elseif ($this->status == 'Lunas') {
            $purchaseOrderData['po_status'] = 'close';
        }

        if ($this->to_deposit) {
            $selectedDeposit = $this->customer->deposit;
            $this->customer->update([
                'deposit' => $selectedDeposit + $purchaseOrderData['to_deposit']
            ]);
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
        } elseif ($this->status == 'Lunas' && $this->to_deposit) {
            Payment::create([
                'purchase_id' => $purchase->id,
                'amount' => $this->paid_amount,
                'is_dp' => 0,
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
        if ($this->selectedProduct) {
            $this->selectedProduct->update([
                'stok' => $this->selectedProduct->stok - $this->qty
            ]);
        }

        session()->flash('orderCreated', ['Sukses', 'Berhasil menambahkan data', 'success']);
        $this->redirect(route('order.index'), navigate: true);
    }

    public function updatedExpeditionId($value)
    {
        $this->isExpeditionManuallySet = true;
    }

    public function render()
    {

        $this->products = Products::get();
        $this->customer = Customer::find($this->customer_id);
        $this->selectedProduct = Products::find($this->product_id);
        // if ($this->customer) {
        //     if (!$this->isExpeditionManuallySet) {
        //         if ($this->customer->ekspedisis) {
        //             $this->expedition_id = $this->customer->ekspedisis->id;
        //         };
        //     }
        //     $this->expedition = Ekspedisi::find($this->expedition_id);
        //     if ($this->without_dtf) {
        //         $this->qty = 0;
        //     }
        //     if ($this->selectedProduct) {
        //         # code...
        //         if ($this->customer->is_reseller) {
        //             $price_range = json_decode($this->selectedProduct['detail_harga_retail'], true);
        //         } else {
        //             $price_range = json_decode($this->selectedProduct['detail_harga'], true);
        //         }



        //         if ($this->qty <= $this->selectedProduct->stok) {
        //             $this->outOfStock = false;
        //             foreach ($price_range as $range) {
        //                 $this->found = false; // Initialize the found flag to false for each iteration
        //                 if ($this->qty >= $range['start'] && $this->qty <= $range['end']) {
        //                     $this->product_price = $range['price'] * $this->qty;
        //                     $this->found = true;
        //                     break;
        //                 }
        //             }
        //         } else {
        //             $this->outOfStock = true;
        //             $this->found = true;
        //         }
        //     }


        //     $this->shipped_price = $this->selectedProduct ? $this->product_price : 0 + ($this->expedition ? $this->expedition->ongkir : 0) + $this->additional_price - $this->discount;
        //     $this->total_price = $this->shipped_price;

        //     if ($this->is_deposit && $this->customer) {
        //         $this->deposit_cut = min($this->shipped_price, $this->customer->deposit);
        //         $this->total_price -= $this->deposit_cut;
        //     }
        // }

        if ($this->customer) {
            // Auto-assign expedition if not set manually
            if (!$this->isExpeditionManuallySet && $this->customer->ekspedisis) {
                $this->expedition_id = $this->customer->ekspedisis->id;
            }

            $this->expedition = Ekspedisi::find($this->expedition_id);

            // Reset qty if without DTF
            if ($this->without_dtf) {
                $this->qty = 0;
            }

            // Price calculation only when product is selected
            if ($this->selectedProduct) {
                if ($this->customer->is_reseller) {
                    $price_range = json_decode($this->selectedProduct['detail_harga_retail'], true);
                } else {
                    $price_range = json_decode($this->selectedProduct['detail_harga'], true);
                }

                // Only calculate price if enough stock
                if ($this->qty <= $this->selectedProduct->stok) {
                    $this->outOfStock = false;
                    $this->found = false;

                    foreach ($price_range as $range) {
                        if ($this->qty >= $range['start'] && $this->qty <= $range['end']) {
                            $this->product_price = $range['price'] * $this->qty;
                            $this->found = true;
                            break;
                        }
                    }
                } else {
                    $this->outOfStock = true;
                    $this->found = true;
                    $this->product_price = 0; // ensure price is 0 when out of stock
                }
            } else {
                $this->product_price = 0; // ensure price is reset if no product
            }

            // Calculate total price
            $this->shipped_price =
                ($this->selectedProduct ? $this->product_price : 0)
                + ($this->expedition ? $this->expedition->ongkir : 0)
                + $this->additional_price
                - $this->discount;

            $this->total_price = $this->shipped_price;

            // Apply deposit if any
            if ($this->is_deposit && $this->customer) {
                $this->deposit_cut = min($this->shipped_price, $this->customer->deposit);
                $this->total_price -= $this->deposit_cut;
            }
        }




        return view('livewire.order.create-order', [
            'customer' => $this->customer,
            'expedition' => $this->expedition,
            'products' => $this->products
        ]);
    }
}
