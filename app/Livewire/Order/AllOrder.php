<?php

namespace App\Livewire\Order;

use Livewire\Component;
use App\Models\Purchase;
use Livewire\WithPagination;

class AllOrder extends Component
{
    use WithPagination;
    public $search;

    public function render()
    {
        return view('livewire.order.all-order',[
            'purchases' => Purchase::paginate(15)
        ]);
    }
}
