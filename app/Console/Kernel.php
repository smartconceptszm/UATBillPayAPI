<?php

namespace App\Console;

use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Http\ScheduledTasks\RetryFailedTrasactions;
use App\Http\ScheduledTasks\ClearFailedJobs;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Facades\App;


class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // $schedule->command('inspire')->hourly();
        $schedule->call(function () {
            new ClearFailedJobs;
        })->timezone('Africa/Lusaka')->dailyAt("00:30");

        $schedule->call(function () {
            App::make(RetryFailedTrasactions::class);
        })->timezone('Africa/Lusaka')->dailyAt("01:00");

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
