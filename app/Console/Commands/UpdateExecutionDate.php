<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\InternalProcess;
use Carbon\Carbon;

class UpdateExecutionDate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'internalprocess:update-execution-date';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Updates the execution date for internal processes';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $today = Carbon::today()->toDateString();

        InternalProcess::where('execution_date', '<>', $today)
            ->whereNull('machine_no')
            ->whereNull('shift_no')
            ->whereNull('print_no')
            ->where('is_done', 0)
            ->where('is_confirm', 0)
            ->update(['execution_date' => $today]);

        InternalProcess::where('execution_date', '<>', $today)
            ->whereNull('shift_no')
            ->whereNull('print_no')
            ->where('is_done', 0)
            ->where('is_confirm', 0)
            ->update(['execution_date' => $today]);

        InternalProcess::where('execution_date', '<>', $today)
            ->whereNull('print_no')
            ->where('is_done', 0)
            ->where('is_confirm', 0)
            ->where('shift_no', 2)
            ->update(['shift_no' => 1, 'execution_date' => $today]);

        InternalProcess::where('execution_date', '<>', $today)
            ->where('is_done', 0)
            ->where('is_confirm', 0)
            ->where('shift_no', 2)
            ->update(['shift_no' => 1, 'execution_date' => $today]);

        InternalProcess::where('execution_date', '<>', $today)
            ->where('is_confirm', 0)
            ->where('shift_no', 2)
            ->update(['shift_no' => 1, 'execution_date' => $today]);

        $this->info('Execution dates updated successfully!');
    }
}
