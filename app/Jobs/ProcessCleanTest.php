<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessCleanTest implements ShouldQueue
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
        Log::debug('Starting Clean Tests: ' . time());
//        echo "\n\rStarting Clean Tests\n\r";


        exec('rm -R ' . public_path() . '/../tests/Browser/screenshots/*.png');
        //exec('rm -R ' . public_path() . '/../storage/logs/laravel-*.log');
        exec('rm -R /tmp/.com.google.Chrome.*');
        exec('rm -R /tmp/php*');
        exec('> /var/log/apache2/error*');
        exec('> /var/log/apache2/access*');

        $this->killall('chromium');
        $this->killall('apache2');
        exec('/usr/sbin/apache2ctl restart');
        Log::debug('Finished Clean Tests: ' . time());
//        $this->killall('sh');
//        $this->killall('php');
//        exec('/var/www/html/artisan queue:listen --memory=1028 --sleep=5 --timeout=400 --tries=3 > /dev/null &');
//        exec('/var/www/html/artisan schedule:list > /dev/null &');
//        exec('/var/www/html/artisan schedule:work > /dev/null &');
//        exec('/usr/sbin/apache2ctl restart');
        //show all memory consumption
//        ps -e -o pid,vsz,comm= | sort -n -k 2

    }
//
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
