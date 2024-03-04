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

    public function render()
    {
        return view('livewire.order.po.all-po');
    }
}
