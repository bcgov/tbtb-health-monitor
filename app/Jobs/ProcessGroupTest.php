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

class ProcessGroupTest implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $group;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($group)
    {
        $this->group = $group;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Log::debug('Starting ' . $this->group . ' Tests: ' . time());
        $t = new TestCaseController();
        $request = new Request();

        $tests = TbtbTest::where('group', $this->group)->where('paused', false)->get();
        foreach ($tests as $test){
            Log::debug('Starting ' . $this->group . ' Process: ' . $test->cmd);
            $process = $t->runServiceTest($request, $test);
            Log::debug($process['status']);
            Log::debug('End ' . $this->group . ' Process: ' . $test->cmd);
        }

        Log::debug('############## ' . time() . ' ###############');
        Log::debug('Finished ' . $this->group . ' Tests');
    }
}
