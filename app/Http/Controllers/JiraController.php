<?php

namespace App\Http\Controllers;

use Auth;
use Response;
use App\Models\Crawl;
use App\Models\TestCase as TbtbTest;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

use Illuminate\Http\Request;
use App\Http\Requests\AjaxRequest;

class JiraController extends Controller
{


    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('home');
    }

    public function runJiraTest(Request $request, $test)
     {
         $environment = $test->env;
         $service = $test->cmd;
         $test->status = 'Pending';
         $test->save();

         $result = ['status' => 500, 'result' => false];

        if($service == 'Jira_Login_page' || $service == 'Wiki_Login_page'){
            $needle = 'my login on this computer';
            if($service == 'Wiki_Login_page'){
                $needle = 'Log in';
            }
            $url = 'http://dev.cms.studentaidbc.ca/api/curls';
            $postData = [
                'url' => $test->url
            ];
            $response = $this->makeApiCall($url, $postData);
            if($response['http_code'] == 200){
                if(strpos($response['body'], $needle) !== false && strpos($response['body'], $needle) > 0 ){
                    $result['status'] = 200;
                    $result['result'] = strpos($response['body'], 'my login on this computer');
                }else{
                    $result['status'] = 500;
                    $result['result'] = "Cannot find string (" . $needle . ")";
                }
            }else{
                $result['result'] = $response['curl_error'];
            }
        }

         if($service == 'Postgres_Database_Connection') {

             $url = 'http://dev.cms.studentaidbc.ca/api/dbs';
             $postData = [
                 'group' => $test->group,
                 "env" => $test->env,
                 'dbType' => 'Postgres'
             ];
             $response = $this->makeApiCall($url, $postData);
             //var_dump($response);
             if($response['curl_error'] == ''){
                 $result['status'] = $response['http_code'];
                 $result['result'] = $response['body'];
             }else{
                 $result['result'] = $response['curl_error'];
             }
         }

         //DUSK tests must be updated in the dusk test file
         if($test->dusk_test === false) {
             $test->status = $result['status'] == 200 ? 'Pass' : 'Fail';
             $test->response = $result['result'];

             if($result['status'] != 200){
                 Log::channel('monitor')->info(" ");
                 $attempt = $test->attempt+1;
                 Log::channel('monitor')->info("JIRA Test: " . $service . " on the env (" . $test->env . ") failed. Number of attempts: " . $attempt);
                 Log::channel('monitor')->info($result['result']);
                 Log::channel('monitor')->info(" ");
                 if( $test->mute == false ){
                     //if test failed 5+ times and testing is not paused
                     if($test->attempt >= 5){
                         //get Contact info related to the test for notification
                         Log::debug("JIRA Test: " . $service . " failed " . $test->attempt . " times. SMS User.");
                         $t = TbtbTest::where('id', $test->id)->with('contacts')->first();
                         foreach($t->contacts as $contact){
                             if($contact->mute == false && $contact->status == 'active'){
                                 $send_sms = $this->smsUser($contact->cell_number, $contact->name, $service, "FAILED on " . $test->env . " for 15+ minutes. Attempts " . $test->attempt_total);
                             }
                         }
                         $test->attempt = 0;
                     }else{
                         Log::debug("JIRA Test: " . $service . " failed " . $test->attempt . " times. ");
                         $test->attempt += 1;
                     }
                 }else{
                     Log::debug("JIRA Test: " . $service . " failed " . $test->attempt . " times. Service Muted");
                     $test->attempt += 1;
                 }
                 $test->attempt_total += 1;
             }else{
                 $test->attempt = 0;
                 $test->attempt_total = 0;
             }
         }


         $test->save();
         return $result;
    }

    public function fetchJiraTests(Request $request)
    {
        $tests = [
            'branch' => 'JIRA',
        ];

        $test_cases = TbtbTest::where('group', 'JIRA')->where('env', 'production')->with('contacts')->get();
        $tests['env']['production']['name'] = 'PRODUCTION';
        foreach ($test_cases as $test){
            $tests['env']['production']['cases'][$test->name] = $test;
            $tests['env']['production']['cases'][$test->name]['expanded'] = false;
            $tests['env']['production']['last_test'] = $test->updated_at->toDateTimeString();
        }

        $test_cases = TbtbTest::where('group', 'JIRA')->where('env', 'dev')->with('contacts')->get();
        $tests['env']['dev']['name'] = 'DEV';
        foreach ($test_cases as $test){
            $tests['env']['dev']['cases'][$test->name] = $test;
            $tests['env']['dev']['cases'][$test->name]['expanded'] = false;
            $tests['env']['dev']['last_test'] = $test->updated_at->toDateTimeString();
        }

        return Response::json(['status' => true, 'tests' => $tests, 'user_auth' => Auth::check()], 200); // Status code here

    }
}
