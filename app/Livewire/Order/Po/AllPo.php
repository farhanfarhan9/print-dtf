<?php

namespace App\Livewire\Order\Po;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\PurchaseOrder;

class AllPo extends Component
{
    use WithPagination;

    public $order;

    // public function mount()
    // {
    //     $this->purchase_orders = PurchaseOrder::where('purchase_id', $this->order)->orderBy('created_at', 'desc')->paginate(15);
    // }

    public function render()
    {
        return view('livewire.order.po.all-po',[
            'purchase_orders' => PurchaseOrder::where('purchase_id', $this->order)->orderBy('created_at', 'desc')->paginate(10),
        ]);
    }
}
