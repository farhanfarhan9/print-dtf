<?php

namespace App\Livewire\InternalProcess;

use Carbon\Carbon;
use Livewire\Component;
use WireUi\Traits\Actions;
use Livewire\WithPagination;
use App\Models\InternalProcess;

class HistoryInternalProcess extends Component
{
    use Actions;
    use WithPagination;

    public function render()
    {
        $today = Carbon::today();

        $internalsQuery = InternalProcess::whereHas('purchase_order', function ($query) {
            $query->where('status', '!=', 'cancel');
        })
          ->orderBy('execution_date', 'desc')->get();

        // Group the paginated internals by execution date
        $groupedInternals = $internalsQuery->groupBy(function($internal) {
            return $internal->execution_date; // Grouping by execution date
        });

        return view('livewire.internal-process.history-internal-process',[
            'internals'=>$groupedInternals
        ]);
    }
}
