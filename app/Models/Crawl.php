<?php

namespace App\Models;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use App\Models\TestCase as TbtbTest;
use Illuminate\Database\Eloquent\Model;

class Crawl extends Model
{

    /**
     * A basic browser test example.
     *
     * @return array
     */
    public function runCrawler(TbtbTest $test)
    {
        $result = ['pass' => 500, 'error' => ''];
        Log::debug(' ');
        Log::debug(' ');
//        Log::debug('exec pwd: ' . exec("pwd"));
////        echo exec("pwd") . "\n\r";
        $path = exec("pwd");
        $artisan = file_exists($path . '/artisan') ? 'php artisan' : 'cd .. && php artisan';
//        echo "artisan: " . $artisan . "\n\r";
        Log::debug("artisan: " . $artisan);
        exec($artisan . " dusk:chrome-driver --detect");

        Log::debug("dusk cmd: " . $artisan . " dusk --filter=" . $test->filter_name . " 2>&1");
        $last_line = exec($artisan . " dusk --filter=" . $test->filter_name . " 2>&1", $output, $return_var);
//        echo "last_line = " . $last_line . "\n\r";
        Log::debug("last_line = " . $last_line);

        $errors = [];
        $error_string = "";
        for ($i=0; $i<sizeof($output); $i++){

            Log::debug("i = " . $i);
            Log::debug("line = " . $output[$i]);
//            echo "\n\r";
//            echo "i = " . $i . "\n\r";
//            echo "line = " . $output[$i] . "\n\r";
            //if string contains success test i.e: OK (1 test, 1 assertion)
            //or if string contains "OK, but incomplete, skipped, or risky tests!"
            if(strpos($output[$i], 'OK (') !== false || strpos($output[$i], 'OK, but incomplete') !== false){
                $result = ['pass' => 200, 'error' => null];
                break;
            }


            //if it is the first line of error
            if(strpos($output[$i], 'Tests\Browser') !== false){
                $error_string = $output[$i];
            }

            if($error_string != '' && $error_string != $output[$i] && strpos($output[$i], 'Failed asserting') === false){
                $error_string .= ' ' . $output[$i];
            }

            if(strpos($output[$i], 'Failed asserting') !== false){
                $error_string .= ' ' . $output[$i];
                $errors[] = $error_string;
                $error_string = '';
//                echo "==================>" . $error_string . "\n\r";
                Log::debug("error_string ==================>" . $error_string);

            }
//            echo ">>>>>>>>>>" . $i . ": " . $output[$i] . "\n\r";
            Log::debug("output[i] >>>>>>>>>>" . $i . ": " . $output[$i]);
        }

        if($result['pass'] === 200){
            Log::debug('Test Pass');
            $test->status = 'Pass';
            $test->attempt = 0;
            $test->response = '';
        }else{
            Log::debug('Test Fail');
            $test->status = 'Fail';
            $test->attempt += 1;
            $error = '';
            if(isset($errors[0])){
                $error = $errors[0];
            }
            $test->response = $error;

            $result = ['pass' => 500, 'error' => $error];
        }
        $test->save();
        $this->reportStatus($test);

        return $result;

    }


    private function reportStatus($test_case){
        $controller = new Controller();
        $test = TbtbTest::where('id', $test_case->id)->with('contacts')->first();
        if($test->status == 'Fail'){
            if( $test->mute == false ){
                //if test failed 5+ times and testing is not paused
                if($test->attempt >= 5){
                    //get Contact info related to the test for notification
                    Log::debug("SABC Dusk Test: " . $test->cmd . " failed " . $test->attempt . " times. SMS User.");
//                    echo "\n\rSABC Dusk Test: " . $test->cmd . " failed " . $test->attempt . " times. SMS User.\n\r";
                    //$contacts = Contact::where('group', 'SABC')->where('status', 'active')->where('mute', false)->with('testCases')->get();
                    foreach($test->contacts as $contact){
                        if($contact->mute == false && $contact->status == 'active'){
                            $send_sms = $controller->smsUser($contact->cell_number, $contact->name, $test->cmd, "Failure for this service on " . $test->env . " for 15+ minutes. Attempts " . $test->attempt);
                        }
                    }
                    $test->attempt = 0;
                }else{
                    Log::debug("SABC Dusk Test: " . $test->cmd . " failed " . $test->attempt . " times. ");
//                    echo "\n\rSABC Dusk Test: " . $test->cmd . " failed " . $test->attempt . " times.\n\r";
                    $test->attempt += 1;
                }
            }else{
                Log::debug("SABC Dusk Test: " . $test->cmd . " failed " . $test->attempt . " times. Service Muted");
//                echo "\n\rSABC Dusk Test: " . $test->cmd . " failed " . $test->attempt . " times. Service Muted\n\r";
                $test->attempt += 1;
            }

        }else{
            Log::debug("SABC Dusk Test: " . $test->cmd . " PASSED " . $test->attempt . " times. Service Muted");
//            echo "\n\rSABC Dusk Test: " . $test->cmd . " PASSED " . $test->attempt . " times. Service Muted\n\r";
            $test->attempt = 0;
        }
        $test->save();
    }
}




