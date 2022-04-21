<?php

namespace App\Console;

use App\Jobs\ProcessCleanTest;
use App\Jobs\ProcessGroupTest;

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
        $schedule->job(new ProcessCleanTest)->everyTenMinutes();
        $schedule->job(new ProcessGroupTest('WDST'))->everyThreeMinutes();
        $schedule->job(new ProcessGroupTest('INFRASTRUCTURE'))->everyThreeMinutes();
        $schedule->job(new ProcessGroupTest('SABC'))->everyFourMinutes();
        $schedule->job(new ProcessGroupTest('PTIB'))->everyTenMinutes();
        $schedule->job(new ProcessGroupTest('JIRA'))->everyFifteenMinutes();
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
