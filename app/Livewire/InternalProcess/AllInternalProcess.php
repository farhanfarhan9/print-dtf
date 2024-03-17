<?php

namespace App\Livewire\InternalProcess;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\InternalProcess;

class AllInternalProcess extends Component
{
    use WithPagination;

    // public $data;
    // public function mount()
    // {
    //     $this->data = InternalProcess::whereHas('purchase_order', function ($query) {
    //         $query->where('status', '!=', 'cancel');
    //     })->get();
    // }

    public function render()
    {
        return view('livewire.internal-process.all-internal-process',[
            // 'internals' => InternalProcess::whereHas('purchase_order', function ($query) {
            //     $query->where('status', '!=', 'cancel');
            // })->paginate(10)
            'internals'=>InternalProcess::whereHas('purchase_order', function ($query) {
                $query->where('status', '!=', 'cancel');
            })->get()->groupBy(function($internal) {
                return $internal->execution_date; // Grouping by creation date
            })
        ]);
    }
}
