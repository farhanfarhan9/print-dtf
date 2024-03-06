<?php

namespace App\Livewire\Order\Po;

use App\Models\Payment;
use Livewire\Component;
use WireUi\Traits\Actions;
use Livewire\WithPagination;
use App\Models\PurchaseOrder;

class AllPo extends Component
{
    use Actions;
    use WithPagination;

    public $order;
    public $paymentHistoryModal;

    public $paymentHistories = [];

    public $selectedPoHistory;

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

    public function render()
    {
        return view('livewire.order.po.all-po',[
            'purchase_orders' => PurchaseOrder::where('purchase_id', $this->order)->orderBy('created_at', 'desc')->paginate(10),
        ]);
    }
}
