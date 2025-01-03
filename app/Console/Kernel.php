<?php

namespace App\Console;

use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
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
        // $schedule->call(function () {
        //     new \App\Http\ScheduledTasks\ClearFailedJobs();
        // })->timezone('Africa/Lusaka')->dailyAt("00:30");

        // $billpaySettings = \json_decode(cache('billpaySettings',\json_encode([])), true);
        // $schedule->call(
        //                 App::make(\App\Http\ScheduledTasks\GenerateDailyAnalytics::class)
        //             )->timezone('Africa/Lusaka')->dailyAt($billpaySettings['DAILY_ANALYTICS_TIME']); // 

        // $schedule->job(
        //                 new \App\Jobs\GenerateCloseOfDayAnalyticsJob('','','high')
        //             )->timezone('Africa/Lusaka')->dailyAt('08:28'); // $billpaySettings['DAILY_ANALYTICS_TIME']


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
