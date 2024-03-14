<?php

namespace App\Livewire\Order\Po;

use App\Models\PurchaseOrder;
use Livewire\Component;

class AllPo extends Component
{
    public $purchase_orders;
    public $order;

    public function mount(){
        $this->purchase_orders =PurchaseOrder::where('purchase_id', $this->order)->get();
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

    public function render()
    {
        return view('livewire.order.po.all-po');
    }
}
