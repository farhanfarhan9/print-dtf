<?php

namespace App\Livewire\Order\Po;

use App\Models\Payment;
use Livewire\Component;
use WireUi\Traits\Actions;
use Livewire\WithPagination;
use App\Models\PurchaseOrder;
use Livewire\WithFileUploads;

class AllPo extends Component
{
    use Actions;
    use WithPagination;
    use WithFileUploads;

    public $order;
    public $paymentHistoryModal;
    public $paymentModal;

    public $paymentHistories = [];

    public $selectedPoHistory;
    public $selectedPo;

    // Payment
    public $amount;
    public $file;

    public function rules()
    {
        return [
            'amount' => 'required',
            'file' => 'nullable|file|max:2000'
        ];
    }

    // public function mount()
    // {
    //     $this->purchase_orders = PurchaseOrder::where('purchase_id', $this->order)->orderBy('created_at', 'desc')->paginate(15);
    // }
    public function showPaymentHistory(PurchaseOrder $po)
    {

        $this->selectedPoHistory = $po;
        $this->paymentHistories = Payment::where('purchase_order_id', $po->id)->get();
        $this->paymentHistoryModal = 1;
    }
    
    public function updatePaymentModal(PurchaseOrder $po)
    {
        $this->selectedPo = $po;
        $this->paymentModal = 1;
    }

    public function updatePayment(PurchaseOrder $po)
    {
        $this->validate();

        if ($this->file) {
            $this->file = $this->file->store('bukti_pembayaran', 'public');
        }

        Payment::create([
            'purchase_order_id' => $po->id,
            'amount' => $this->amount,
            'is_dp' => 0,
            'file' => $this->file,
        ]);

        $this->reset('paymentModal', 'amount', 'file');
        $this->notification([
            'title'       => 'Sukses',
            'description' => "'Berhasil menambahkan pembayaran pada INV' .$po->invoice_code",
            'icon'        => 'success',
            'timeout'     => 3000
        ]);

    }


    public function render()
    {
        return view('livewire.order.po.all-po', [
            'purchase_orders' => PurchaseOrder::where('purchase_id', $this->order)->orderBy('created_at', 'desc')->paginate(10),
        ]);
    }
}
