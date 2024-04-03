<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\InternalProcess;
use Carbon\Carbon;

class ChangeShiftNo extends Command
{
    protected $signature = 'internalprocess:change-shift-no';
    protected $description = 'Changes shift number from 2 to 1 where conditions are met';

    public function handle()
    {
        $today = Carbon::today()->toDateString();

        InternalProcess::where('execution_date', '=', $today)
            ->where('is_confirm', 0)
            ->where('shift_no', 1)
            ->update(['shift_no' => 2]);

        $this->info('Shift numbers updated successfully!');
    }
}
