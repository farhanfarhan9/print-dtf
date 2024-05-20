<?php

namespace App\Livewire\InternalProcess;

use Carbon\Carbon;
use Livewire\Component;
use WireUi\Traits\Actions;
use Livewire\WithPagination;
use App\Models\InternalProcess;
use Livewire\Attributes\Validate;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;

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
            'description' => 'Berhasil Menambahkan Mesin Untuk Invoice' . $this->selectedData->purchase_order->invoice_code,
            'icon'        => 'success',
            'timeout'     => 3000
        ]);

        $this->reset('selectedData', 'ripModal', 'machineNo');
    }

    public function addPrintNo(InternalProcess $internal)
    {
        $printNo = $this->printNos[$internal->id] ?? null;

        // Manually validate $printNo
        $validator = Validator::make(['printNo' => $printNo], [
            'printNo' => 'required',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $internal->update([
            'print_no' => $printNo
        ]);

        $this->notification([
            'title'       => 'Sukses',
            'description' => 'Berhasil Menambahkan Nomor Urut Print Untuk Invoice' . $internal->purchase_order->invoice_code,
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
            'description' => 'Proses Print Selesai Untuk Invoice' . $internal->purchase_order->invoice_code,
            'icon'        => 'success',
            'timeout'     => 3000
        ]);
    }
    public function packingProcess(InternalProcess $internal)
    {
        $internal->update([
            'is_packing' => 1
        ]);

        $this->notification([
            'title'       => 'Sukses',
            'description' => 'Proses Packing Selesai Untuk Invoice' . $internal->purchase_order->invoice_code,
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
            'description' => 'Proses Terkonfirmasi Untuk Invoice' . $internal->purchase_order->invoice_code,
            'icon'        => 'success',
            'timeout'     => 3000
        ]);
    }

    public function render()
    {
        $today = Carbon::today();
        // dd($today);
        return view('livewire.internal-process.all-internal-process', [
            // 'internals' => InternalProcess::whereHas('purchase_order', function ($query) {
            //     $query->where('status', '!=', 'cancel');
            // })->paginate(10)
            'internals' => InternalProcess::whereHas('purchase_order', function ($query) {
                $query->where('status', '!=', 'cancel');
            })->where('execution_date', $today)->get()->sortByDesc('execution_date')->groupBy(function ($internal) {
                return $internal->execution_date; // Grouping by creation date
            })
        ]);
    }
}
