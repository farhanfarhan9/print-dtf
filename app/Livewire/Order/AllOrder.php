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
    public $paymentHistoryModal, $paymentModal;
    public $selectedPurchase;

    // payment
    public $amount;
    public $file;
    public $maxAmount;
    public $bank_detail;

    public function rules()
    {
        return [
            'amount' => 'required|numeric|max:' . $this->maxAmount,
            'file' => 'nullable|file|max:2000',
            'bank_detail' => 'required',
        ];
    }

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
        $this->validate();

        if ($this->file) {
            $this->file = $this->file->store('bukti_pembayaran', 'public');
        }

        Payment::create([
            'purchase_id' => $purchase->id,
            'amount' => $this->amount,
            'is_dp' => 0,
            'file' => $this->file,
            'bank_detail' => $this->bank_detail,
        ]);

        // if ($po->payments->sum('amount') >= $po->total_price) {
        //     $po->update([
        //         'po_status' => 'close'
        //     ]);
        // }

        $remainingDebt = $purchase->purchase_orders->where('status', '!=', 'cancel')->sum('total_price') - $purchase->payments->sum('amount');

        if ($this->amount >= $remainingDebt) {
            $purchase->update([
                'payment_status' => 'close'
            ]);
        }

        $this->reset('paymentModal', 'amount', 'file');
        $this->notification([
            'title'       => 'Sukses',
            'description' => "'Berhasil menambahkan pembayaran pada INV' .$purchase->invoice_code",
            'icon'        => 'success',
            'timeout'     => 3000
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
