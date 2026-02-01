<?php

namespace App\Livewire\Order;

use Livewire\Component;
use App\Models\Purchase;
use App\Models\Payment;
use Livewire\WithPagination;
use WireUi\Traits\Actions;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class AllOrder extends Component
{
    use WithPagination;
    use Actions;
    use WithFileUploads;

    public $search;
    public $selectedHistory;
    public $paymentHistories = [];
    public $paymentHistoryModal, $paymentModal, $additionalModal;
    public $selectedPurchase;

    // payment
    public $amount;
    public $file;
    public $maxAmount;
    public $bank_detail;

    public $deposit_opt = '';
    public $paymentCut;

    public $additional_amount;

    // public function rules()
    // {
    //     return [
    //         'amount' => 'required|numeric|max:' . $this->maxAmount,
    //         'file' => 'nullable|file|max:2000',
    //         'bank_detail' => 'required',
    //     ];
    // }

    public function printLabel($orderId)
    {
        // You might need to use the fully qualified route name depending on your Laravel version.
        return redirect()->route('print.shipping.label', ['orderId' => $orderId]);
    }

    public function printInvoice($orderId)
    {
        // You might need to use the fully qualified route name depending on your Laravel version.
        return redirect()->route('print.invoice.label', ['orderId' => $orderId]);
    }

    public function showPaymentHistory(Purchase $purchase)
    {
        $this->selectedHistory = $purchase;
        $this->paymentHistories = Payment::where('purchase_id', $purchase->id)->get();
        $this->paymentHistoryModal = 1;
    }

    public function updateAdditionalModal(Purchase $purchase)
    {
        $this->selectedPurchase = $purchase;
        $this->additionalModal = 1;
    }

    public function updateAdditional(Purchase $purchase)
    {
        $last_po = $purchase->purchase_orders->last();
        $purchase->update([
            'total_payment' => $purchase->total_payment + $this->additional_amount
        ]);
        $last_po->update([
            'additional_price' => $last_po->additional_price + $this->additional_amount,
            'total_price' => $last_po->total_price + $this->additional_amount,
        ]);

        $this->reset('additionalModal', 'additional_amount');
        $this->notification([
            'title' => 'Sukses',
            'description' => "'Berhasil menambahkan biaya tambahan pada INV' .$purchase->invoice_code",
            'icon' => 'success',
            'timeout' => 3000
        ]);
    }


    public function updatePaymentModal(Purchase $purchase)
    {
        $this->selectedPurchase = $purchase;
        $this->paymentModal = 1;
        $this->maxAmount = $purchase->total_payment - $purchase->payments->sum('amount');
    }

    public function updatePayment(Purchase $purchase)
    {
        // dd($this->maxAmount);
        // dd($remainingDebt);
        // dd( $purchase->purchase_orders->where('status', '!=', 'cancel')->sum('total_price'));
        // $this->validate();
        if ($this->deposit_opt == 'to_deposit') {
            $this->validate([
                'amount' => 'required|numeric',
                'file' => 'nullable|file|max:2000|mimes:jpg,jpeg,png,pdf',
                'bank_detail' => 'required',
            ]);
        } elseif ($this->deposit_opt == 'cut_deposit') {
            // dd($purchase->customer->deposit);
            // dd($this->maxAmount);
            $this->paymentCut = $this->maxAmount - $purchase->customer->deposit;
            if ($this->paymentCut > 0) {
                $this->validate([
                    'amount' => 'required|numeric|max:' . $this->paymentCut,
                    'file' => 'nullable|file|max:2000|mimes:jpg,jpeg,png,pdf',
                    'bank_detail' => 'required',
                ]);
            } else {
                $this->validate([
                    'amount' => 'nullable',
                    'file' => 'nullable|file|max:2000|mimes:jpg,jpeg,png,pdf',
                    'bank_detail' => 'required',
                ]);
            }
        } else {
            $this->validate([
                'amount' => 'required|numeric|max:' . $this->maxAmount,
                'file' => 'nullable|file|max:2000|mimes:jpg,jpeg,png,pdf',
                'bank_detail' => 'required',
            ]);
        }
        if ($this->file) {
            $this->file = $this->file->store('bukti_pembayaran', 'public');
        }

        if ($this->deposit_opt == 'to_deposit') {
            Payment::create([
                'purchase_id' => $purchase->id,
                'amount' => $this->amount,
                'is_dp' => 0,
                'file' => $this->file,
                'bank_detail' => $this->bank_detail,
                'to_deposit' => $this->amount - $this->maxAmount,
            ]);
        } elseif ($this->deposit_opt == 'cut_deposit') {
            Payment::create([
                'purchase_id' => $purchase->id,
                'amount' => $this->paymentCut > 0 ? $this->amount + $purchase->customer->deposit : $this->maxAmount,
                'is_dp' => 0,
                'file' => $this->file,
                'bank_detail' => $this->bank_detail,
            ]);
        } else {
            Payment::create([
                'purchase_id' => $purchase->id,
                'amount' => $this->amount,
                'is_dp' => 0,
                'file' => $this->file,
                'bank_detail' => $this->bank_detail,
            ]);
        }


        if ($this->deposit_opt == 'to_deposit') {
            $selectedDeposit = $purchase->customer->deposit;
            $purchase->customer->update([
                'deposit' => $selectedDeposit + ($this->amount - $this->maxAmount)
            ]);
        } elseif ($this->deposit_opt == 'cut_deposit') {
            $selectedDeposit = $purchase->customer->deposit;
            $purchase->customer->update([
                'deposit' => $this->paymentCut > 0 ? 0 : $selectedDeposit - $this->maxAmount,
            ]);
        }


        // if ($po->payments->sum('amount') >= $po->total_price) {
        //     $po->update([
        //         'po_status' => 'close'
        //     ]);
        // }

        $remainingDebt = $purchase->purchase_orders->where('status', '!=', 'cancel')->sum('total_price') - $purchase->payments->sum('amount');

        if ($remainingDebt <= 0) {
            $purchase->update([
                'payment_status' => 'close'
            ]);
        }

        $this->reset('paymentModal', 'amount', 'file', 'deposit_opt');
        $this->notification([
            'title' => 'Sukses',
            'description' => "'Berhasil menambahkan pembayaran pada INV' .$purchase->invoice_code",
            'icon' => 'success',
            'timeout' => 3000
        ]);
    }

    public function deleteDialog(Purchase $purchase)
    {
        $this->dialog()->confirm([
            'title' => 'Menghapus Order',
            'description' => 'Yakin Ingin Menghapus Order?',
            'acceptLabel' => 'Ya',
            'method' => 'deletePurchase',
            'params' => $purchase,
            'timeout' => 3000
        ]);
    }

    public function deletePurchase(Purchase $purchase)
    {
        $purchase->delete();
        $this->notification([
            'title' => 'Sukses',
            'description' => "Berhasil Menghapus Order",
            'icon' => 'success',
            'timeout' => 3000
        ]);
    }

    public function render()
    {
        $query = Purchase::query();

        // If search query is provided, filter by user name
        if ($this->search) {
            $query->whereHas('customer', function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%');
            });
        }

        // Order by created_at in descending order and paginate
        $purchases = $query->orderBy('created_at', 'desc')->paginate(10);

        return view('livewire.order.all-order', compact('purchases'));
    }
}
