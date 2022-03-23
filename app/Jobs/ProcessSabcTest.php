<?php

namespace App\Jobs;

use App\Models\TestCase as TbtbTest;
use Illuminate\Http\Request;
use App\Http\Controllers\SabcController;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessSabcTest implements ShouldQueue
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
        Log::debug('Starting SABC Tests: ' . time());
        $t = new SabcController();
        $request = new Request();

        $tests = TbtbTest::where('group', 'SABC')->where('paused', false)->get();
        foreach ($tests as $test){
            Log::debug("Start " . $test->env . " SABC Process: " . $test->cmd);
            $process = $t->runSabcTest($request, $test);
            Log::debug($process['status'] . " " . $process['result']);
            Log::debug("End " . $test->env . " SABC Process: " . $test->cmd);
        }

        Log::debug('############## ' . time() . ' ###############');
        Log::debug('Finished SABC Tests');
    }
}
