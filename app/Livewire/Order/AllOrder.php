<?php

namespace App\Livewire\Order;

use App\Models\Purchase;
use Livewire\Component;

class AllOrder extends Component
{
    public $search;

    public function render()
    {
        return view('livewire.order.all-order',[
            'purchases' => Purchase::paginate(15)
        ]);
    }
}
