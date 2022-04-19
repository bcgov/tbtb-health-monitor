<?php

namespace App\Console;

use App\Jobs\ProcessCleanTest;
use App\Jobs\ProcessSabcTest;
use App\Jobs\ProcessPtibTest;
use App\Jobs\ProcessJiraTest;
use App\Jobs\ProcessWdstTest;

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
        $schedule->job(new ProcessWdstTest)->everyThreeMinutes();
        $schedule->job(new ProcessSabcTest)->everyFourMinutes();
        $schedule->job(new ProcessPtibTest)->everyTenMinutes();
        $schedule->job(new ProcessJiraTest)->everyFifteenMinutes();
        $schedule->job(new ProcessCleanTest)->everyFiveMinutes();
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
