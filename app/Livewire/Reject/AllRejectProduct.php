<?php

namespace App\Livewire\Reject;

use App\Models\RejectProduct;
use Livewire\Component;

class AllRejectProduct extends Component
{
    public function render()
    {
        return view('livewire.reject.all-reject-product', [
            'rejecteds' => RejectProduct::paginate(15)
        ]);
    }
}
