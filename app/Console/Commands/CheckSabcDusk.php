<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\TestCase as TbtbTest;

class CheckSabcDusk extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sabc-dusk:check';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check SABC Dusk if it is running.';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $count = 0;
        $tests = TbtbTest::where(json_decode(env('SABC_DUSK_CHECK'), 1))->get();
        foreach ($tests as $test){
            if($test->status == 'Fail'){
                $count += $test->attempt_total;
            }
        }
        $this->info($count);
    }
}
