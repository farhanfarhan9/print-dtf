<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{

    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        Commands\UpdateExecutionDate::class,
        Commands\ChangeShiftNo::class,
        // Any other commands you have
    ];

    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        $schedule->command('internalprocess:update-execution-date')
                 ->dailyAt('08:59');

        $schedule->command('internalprocess:change-shift-no')
                 ->dailyAt('16:59'); // or at whatever time is appropriate
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
