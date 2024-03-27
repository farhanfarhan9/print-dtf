<?php

namespace App\Livewire;

use App\Models\Customer;
use App\Models\InternalProcess;
use App\Models\PurchaseOrder;
use App\Models\User;
use Carbon\Carbon;
use Livewire\Component;

class Dashboard extends Component
{
    public function render()
    {
        return view('livewire.dashboard',[
            'count_user' => Customer::get()->count(),
            'count_po' => PurchaseOrder::get()->count(),
            'count_process' => InternalProcess::where('execution_date', Carbon::today())->get()->count(),
        ]);
    }
}
