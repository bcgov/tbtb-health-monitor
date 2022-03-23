<?php

namespace App\Console;

use App\Jobs\ProcessCleanTest;
use App\Jobs\ProcessSabcTest;
use App\Jobs\ProcessPtibTest;
use App\Jobs\ProcessJiraTest;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')->hourly();
        //$schedule->command('inspire')->everyMinute();
        $schedule->job(new ProcessSabcTest)->everyThreeMinutes();
        $schedule->job(new ProcessPtibTest)->everyTenMinutes();
        $schedule->job(new ProcessJiraTest)->everyFifteenMinutes();
        $schedule->job(new ProcessCleanTest)->hourlyAt('*/17');
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
