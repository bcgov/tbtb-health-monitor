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

class PtibController extends Controller
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

//    public function fetchPtibStatus(Request $request, $environment, $service)
    public function runPtibTest(Request $request, $test)
     {
         $environment = $test->env;
         $service = $test->cmd;
         $test->status = 'Pending';
         $test->save();

         $result = ['status' => 500, 'result' => false];
        switch ($environment) {
            case 'production':
                $env_url = 'https://www.';
                $db_connection = "host=" . env("DB_PTIB_HOST_PROD") . " port=" . env("DB_PTIB_PORT_PROD") . " dbname=" . env("DB_PTIB_DATABASE_PROD") . " user=" . env("DB_PTIB_USERNAME_PROD") . " password=" . env("DB_PTIB_PASSWORD_PROD");
                $url = "https://logon.gov.bc.ca/clp-cgi/int/logon.cgi?TARGET=https://www.admin.privatetraininginstitutions.gov.bc.ca/&flags=1100:1,7&toggle=1";

                break;
            case 'uat':
                $env_url = 'http://uat.';
                $db_connection = "host=" . env("DB_PTIB_HOST_UAT") . " port=" . env("DB_PTIB_PORT_UAT") . " dbname=" . env("DB_PTIB_DATABASE_UAT") . " user=" . env("DB_PTIB_USERNAME_UAT") . " password=" . env("DB_PTIB_PASSWORD_UAT");
                $url = "https://logontest7.gov.bc.ca/clp-cgi/int/logon.cgi?TARGET=https://uat.admin.privatetraininginstitutions.gov.bc.ca/&flags=1100:1,7&toggle=1";
                break;
        }

        if($service == 'Landing_HTML_page') {

            $response = Http::withOptions(['verify' => false])->get($env_url . "privatetraininginstitutions.gov.bc.ca");
            if($response != false){
                $result['status'] = $response->status();
                $result['result'] = strpos($response->body(), 'Private Training Institutions Branch');
            }
        }

        if($service == 'Admin_Login_page'){
            $limit = 8;
            while((App::runningUnitTests())){
                sleep(5);
                if($limit == 0){
                    break;
                }
                if($limit > 0){
                    $limit--;
                }
            }

            $crawl = new Crawl();
            $response =  $crawl->runCrawler($test);

            $result['status'] = $response['pass'];
            $result['result'] = $response['error'];

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
                 Log::channel('monitor')->info("PTIB Test: " . $service . " on the env (" . $test->env . ") failed. Number of attempts: " . $attempt);
                 Log::channel('monitor')->info($result['result']);
                 Log::channel('monitor')->info(" ");
                 if( $test->mute == false ){
                     //if test failed 5+ times and testing is not paused
                     if($test->attempt >= 5){
                         //get Contact info related to the test for notification
                         Log::debug("PTIB Test: " . $service . " failed " . $test->attempt . " times. SMS User.");
                         $t = TbtbTest::where('id', $test->id)->with('contacts')->first();
                         foreach($t->contacts as $contact){
                             if($contact->mute == false && $contact->status == 'active'){
                                 $send_sms = $this->smsUser($contact->cell_number, $contact->name, $service, "Failure for this service on " . $test->env . " for 15+ minutes. Attempts " . $test->attempt);
                             }
                         }
                         $test->attempt = 0;
                     }else{
                         Log::debug("PTIB Test: " . $service . " failed " . $test->attempt . " times. ");
                         $test->attempt += 1;
                     }
                 }else{
                     Log::debug("PTIB Test: " . $service . " failed " . $test->attempt . " times. Service Muted");
                     $test->attempt += 1;
                 }
             }else{
                 $test->attempt = 0;
             }
         }


         $test->save();
         return $result;
    }

    public function fetchPtibTests(Request $request)
    {
        $tests = [
            'branch' => 'PTIB',
        ];

        $sabc_tests = TbtbTest::where('group', 'PTIB')->where('env', 'production')->with('contacts')->get();
        $tests['env']['production']['name'] = 'PRODUCTION';
        foreach ($sabc_tests as $test){
            $tests['env']['production']['cases'][$test->name] = $test;
            $tests['env']['production']['cases'][$test->name]['expanded'] = false;
            $tests['env']['production']['last_test'] = $test->updated_at;
        }


        $sabc_tests = TbtbTest::where('group', 'PTIB')->where('env', 'uat')->with('contacts')->get();
        $tests['env']['uat']['name'] = 'UAT';
        foreach ($sabc_tests as $test){
            $tests['env']['uat']['cases'][$test->name] = $test;
            $tests['env']['uat']['cases'][$test->name]['expanded'] = false;
            $tests['env']['uat']['last_test'] = $test->updated_at;
        }

        return Response::json(['status' => true, 'tests' => $tests, 'user_auth' => Auth::check()], 200); // Status code here
    }

}
