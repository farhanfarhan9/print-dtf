<?php

namespace App\Livewire\InternalProcess;

use Carbon\Carbon;
use Livewire\Component;
use WireUi\Traits\Actions;
use Livewire\WithPagination;
use App\Models\InternalProcess;
use Livewire\Attributes\Validate;
use Illuminate\Support\Facades\Gate;

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

    public $printNos = [];

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
            'description' => 'Berhasil Menambahkan Nomor Mesin Untuk Invoice'.$this->selectedData->purchase_order->invoice_code,
            'icon'        => 'success',
            'timeout'     => 3000
        ]);

        $this->reset('selectedData', 'ripModal', 'machineNo');
    }

    public function addPrintNo(InternalProcess $internal)
    {
        $printNo = $this->printNos[$internal->id] ?? null;
        
        $internal->update([
            'print_no' => $printNo
        ]);

        $this->notification([
            'title'       => 'Sukses',
            'description' => 'Berhasil Menambahkan Nomor Urut Print Untuk Invoice'.$internal->purchase_order->invoice_code,
            'icon'        => 'success',
            'timeout'     => 3000
        ]);

        $this->printNos = [];
    }

    public function doneProcess(InternalProcess $internal)
    {
        $internal->update([
            'is_done' => 1
        ]);

        $this->notification([
            'title'       => 'Sukses',
            'description' => 'Proses Selesai Untuk Invoice'.$internal->purchase_order->invoice_code,
            'icon'        => 'success',
            'timeout'     => 3000
        ]);
    }

    public function confirmProcess(InternalProcess $internal)
    {
        $internal->update([
            'is_confirm' => 1
        ]);

        $this->notification([
            'title'       => 'Sukses',
            'description' => 'Proses Terkonfirmasi Untuk Invoice'.$internal->purchase_order->invoice_code,
            'icon'        => 'success',
            'timeout'     => 3000
        ]);
    }

    public function render()
    {
        return view('livewire.internal-process.all-internal-process',[
            // 'internals' => InternalProcess::whereHas('purchase_order', function ($query) {
            //     $query->where('status', '!=', 'cancel');
            // })->paginate(10)
            'internals'=>InternalProcess::whereHas('purchase_order', function ($query) {
                $query->where('status', '!=', 'cancel');
            })->get()->sortByDesc('execution_date')->groupBy(function($internal) {
                return $internal->execution_date; // Grouping by creation date
            })
        ]);
    }
}
