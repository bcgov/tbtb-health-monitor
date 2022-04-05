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
        $path = exec("pwd");
        $artisan = file_exists($path . '/artisan') ? 'php artisan' : 'cd .. && php artisan';
        Log::debug("artisan: " . $artisan);
        try {
            exec($artisan . " dusk:chrome-driver --detect");
        } catch (\Exception $e) {
            // do nothing, the pdf is probably generated
            Log::debug("Failed to check artisan: " . $artisan);
            Log::error("Failed to check artisan: " . $e);
        }

        $last_line = "";
        $output = [];
        Log::debug("dusk cmd: " . $artisan . " dusk --filter=" . $test->filter_name . " 2>&1");
        try {
            $last_line = exec($artisan . " dusk --filter=" . $test->filter_name . " 2>&1", $output, $return_var);
        } catch (\Exception $e) {
            // do nothing, the pdf is probably generated
            Log::debug("Failed to exec: " . $artisan . " dusk --filter=" . $test->filter_name . " 2>&1");
        }

        Log::debug("last_line = " . $last_line);

        $errors = [];
        $error_string = "";
        for ($i=0; $i<sizeof($output); $i++){

            Log::debug("i = " . $i);
            Log::debug("line = " . $output[$i]);

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
                Log::debug("error_string ==================>" . $error_string);

            }
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
        $service = $test->cmd;
        if($test->status == 'Fail'){
            Log::channel('monitor')->info(" ");
            $attempt = $test->attempt+1;
            Log::channel('monitor')->info($test->group . " Test: " . $service . " on the env (" . $test->env . ") failed. Number of attempts: " . $attempt);
            Log::channel('monitor')->info($test->response);
            Log::channel('monitor')->info(" ");
            if( $test->mute == false ){
                //if test failed 5+ times and testing is not paused
                if($test->attempt >= 5){
                    //get Contact info related to the test for notification
                    Log::debug("SABC Dusk Test: " . $test->cmd . " failed " . $test->attempt . " times. SMS User.");
                    foreach($test->contacts as $contact){
                        if($contact->mute == false && $contact->status == 'active'){
                            $send_sms = $controller->smsUser($contact->cell_number, $contact->name, $test->cmd, "FAILED on " . $test->env . " for 15+ minutes. Attempts " . $test->attempt_total);
                        }
                    }
                    $test->attempt = 0;
                }else{
                    Log::debug("SABC Dusk Test: " . $test->cmd . " failed " . $test->attempt . " times. ");
                    $test->attempt += 1;
                }
            }else{
                Log::debug("SABC Dusk Test: " . $test->cmd . " failed " . $test->attempt . " times. Service Muted");
                $test->attempt += 1;
            }
            $test->attempt_total += 1;
        }else{
            Log::debug("SABC Dusk Test: " . $test->cmd . " PASSED " . $test->attempt . " times. Service Muted");
            $test->attempt = 0;
            $test->attempt_total = 0;
        }
        $test->save();
    }
}




