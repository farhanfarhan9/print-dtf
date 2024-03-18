<?php

namespace App\Livewire\InternalProcess;

use Carbon\Carbon;
use Livewire\Component;
use WireUi\Traits\Actions;
use Livewire\WithPagination;
use App\Models\InternalProcess;
use Livewire\Attributes\Validate;

class AllInternalProcess extends Component
{
    use Actions;
    use WithPagination;

    // public $data;
    // public function mount()
    // {
    //     $this->data = InternalProcess::whereHas('purchase_order', function ($query) {
    //         $query->where('status', '!=', 'cancel');
    //     })->get();
    // }

    public $selectedData;
    public $ripModal;

    #[Validate('required')] 
    public $machineNo;

    public function ripDialog(InternalProcess $internal)
    {
        $this->selectedData = $internal;
        $this->ripModal = 1;
    }

    public function addMachineNo()
    {
        $currentTime = Carbon::now();
    
        $startWorkingTime = Carbon::today()->hour(9)->minute(0)->second(0);
        $endWorkingTime = Carbon::today()->hour(17)->minute(0)->second(0);

        if ($currentTime->between($startWorkingTime, $endWorkingTime)) {
            $shift = 1; 
        } else {
            $shift = 2; 
        }
        $this->validate();
        // $existingDeposit = $this->editedUser->deposit;
        $this->selectedData->update([
            'machine_no' => $this->machineNo,
            'shift_no' => $shift,
        ]);
        
        $this->notification([
            'title'       => 'Sukses',
            'description' => 'Berhasil Menambahkan Nomor Mesin',
            'icon'        => 'success',
            'timeout'     => 3000
        ]);

        $this->reset('selectedData', 'ripModal', 'machineNo');
    }

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
