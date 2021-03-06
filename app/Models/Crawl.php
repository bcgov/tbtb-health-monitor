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
//        Log::debug(' ');
//        Log::debug(' ');
        $path = exec("pwd");
        $artisan = file_exists($path . '/artisan') ? 'php artisan' : 'cd .. && php artisan';
        Log::channel('daily')->debug("artisan: " . $artisan);
        try {
            exec($artisan . " dusk:chrome-driver --detect");
        } catch (\Exception $e) {
            // do nothing, the pdf is probably generated
            Log::channel('daily')->debug("Failed to check artisan: " . $artisan);
            Log::channel('daily')->error("Failed to check artisan: " . $e);
        }

        $last_line = "";
        $output = [];
        Log::channel('daily')->debug("dusk cmd: " . $artisan . " dusk --filter=" . $test->filter_name . " 2>&1");
        try {
            $last_line = exec($artisan . " dusk --filter=" . $test->filter_name . " 2>&1", $output, $return_var);
        } catch (\Exception $e) {
            // do nothing, the pdf is probably generated
            Log::channel('daily')->debug("Failed to exec: " . $artisan . " dusk --filter=" . $test->filter_name . " 2>&1");
        }

        Log::channel('daily')->debug("last_line = " . $last_line);

        $errors = [];
        $error_string = "";
        for ($i=0; $i<sizeof($output); $i++){

            Log::channel('daily')->debug("i = " . $i);
            Log::channel('daily')->debug("line = " . $output[$i]);

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
                Log::channel('daily')->debug("error_string ==================>" . $error_string);

            }
            Log::channel('daily')->debug("output[i] >>>>>>>>>>" . $i . ": " . $output[$i]);
        }

        if($result['pass'] === 200){
            Log::channel('daily')->debug('Test Pass');
            $test->status = 'Pass';
            $test->attempt = 0;
            $test->response = '';
        }else{
            Log::channel('daily')->debug('Test Fail');
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
            $attempt = $test->attempt+1;
            if( $test->mute == false ){
                //if test failed 5+ times and testing is not paused
                if($test->attempt >= 5){
                    //get Contact info related to the test for notification
                    Log::channel('daily')->debug("SABC Dusk Test: " . $test->cmd . " failed " . $test->attempt . " times. SMS User.");
                    foreach($test->contacts as $contact){
                        if($contact->mute == false && $contact->status == 'active'){
                            $send_sms = $controller->smsUser($contact->cell_number, $contact->name, $test->cmd, "FAILED on " . $test->env . " for 15+ minutes. Attempts " . $test->attempt_total);
                        }
                    }
                    Log::channel('database')->notice($test->group . " Test: " . $service . " on the env (" . $test->env . ") failed. Number of attempts: " . $attempt);
                    Log::channel('database')->notice($test->response);
                    $test->attempt = 0;
                }else{
                    Log::channel('daily')->debug("SABC Dusk Test: " . $test->cmd . " failed " . $test->attempt . " times. ");
                    $test->attempt += 1;
                }
            }else{
                Log::channel('daily')->debug("SABC Dusk Test: " . $test->cmd . " failed " . $test->attempt . " times. Service Muted");
                $test->attempt += 1;
            }
            $test->attempt_total += 1;
        }else{
            Log::channel('daily')->debug("SABC Dusk Test: " . $test->cmd . " PASSED " . $test->attempt . " times. Service Muted");
            $test->attempt = 0;
            $test->attempt_total = 0;
        }
        $test->save();
    }
}




