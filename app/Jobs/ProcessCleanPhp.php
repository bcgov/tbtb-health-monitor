<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessCleanPhp implements ShouldQueue
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
        Log::debug(' ');
        Log::debug('Starting Clean Php: ' . time());

        $this->killall('php');
        exec('/usr/sbin/apache2ctl restart');

    }

    private function killall($match) {
        if($match=='') return 'no pattern specified';
        $match = escapeshellarg($match);
        exec("ps x|grep $match|grep -v grep|awk '{print $1}'", $output, $ret);
        if($ret) return 'you need ps, grep, and awk installed for this to work';
        $killed = false;
        foreach($output as $out) {
            if(preg_match('/^([0-9]+)/', $out, $r)) {
                system('kill '. $r[1], $k);
                if(!$k) $killed = 1;
            }
        }
        if($killed) {
            return '';
        } else {
            return "$match: no process killed";
        }
    }
}
