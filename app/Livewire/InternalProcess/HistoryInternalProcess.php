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
        $thirtyDaysAgo = $today->subDays(15);

        $internalsQuery = InternalProcess::whereHas('purchase_order', function ($query) {
            $query->where('status', '!=', 'cancel');
        })
        ->where('execution_date', '>=', $thirtyDaysAgo) // Only records from the last 30 days
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
