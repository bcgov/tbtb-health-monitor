<?php

namespace App\Jobs;

use App\Models\TestCase as TbtbTest;
use Illuminate\Http\Request;
use App\Http\Controllers\TestCaseController;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessJiraTest implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Log::debug('Starting JIRA Tests: ' . time());
        $t = new TestCaseController();
        $request = new Request();

        $tests = TbtbTest::where('group', 'JIRA')->where('paused', false)->get();
        foreach ($tests as $test){
            Log::debug('Starting JIRA Process: ' . $test->cmd);
            $process = $t->runServiceTest($request, $test);
//            Log::debug($process['status'] . " " . $process['result']);
            Log::debug($process['status']);
            Log::debug('End JIRA Process: ' . $test->cmd);
        }

        Log::debug('############## ' . time() . ' ###############');
        Log::debug('Finished JIRA Tests');
    }
}
