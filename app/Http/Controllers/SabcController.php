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

class SabcController extends Controller
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


    public function runSabcTest(Request $request, $test)
    {
        $result = ['status' => 500, 'result' => false];
        $test = TbtbTest::where('id', $test->id)->first();
        if($test->paused == true){
            $result['status'] = 200;
            $result['result'] = true;
            return $result;
        }

        $environment = $test->env;
        $service = $test->cmd;
        $test->status = 'Pending';
        $test->save();

        $env_url = '';
        $params = [];
        switch (strtolower($environment)) {
            case 'production':
                $params = ['applicationNumber' => "".env('PROD_SABC_WSDL_APP')."", 'userGUID' => "".env('PROD_SABC_WSDL_GUID').""];
                $env_url = '';
                break;
            case 'uat':
                $params = ['applicationNumber' => "".env('UAT_SABC_WSDL_APP')."", 'userGUID' => "".env('UAT_SABC_WSDL_GUID').""];
                $env_url = 'uat.';
                break;
            case 'dev':
                $params = ['applicationNumber' => "".env('DEV_SABC_WSDL_APP')."", 'userGUID' => "".env('DEV_SABC_WSDL_GUID').""];
                $env_url = 'dev.';
                break;
        }


        if($test->test_type == 'wsdl'){
            $response = $this->makeApiCall($test->url, $test->post_data);
            if($response['curl_error'] == ''){
                $obj = json_decode($response['body']);
//                var_dump($obj);
                if($obj->res->fail == true){
                    $result['status'] = 500;
                    $result['result'] = $obj->res->faultstring;
                }else{
                    $result['status'] = $response['http_code'];
                    $result['result'] = $response['body'];
                }
            }else{
                $result['result'] = $response['curl_error'];
            }
        }

        if($test->test_type == 'curl' || $test->test_type == 'db'){
            $response = $this->makeApiCall($test->url, $test->post_data);

            if($response['curl_error'] == ''){
                $result['status'] = $response['http_code'];
                $result['result'] = $response['body'];
            }else{
                $result['result'] = $response['curl_error'];
            }
        }

        if($test->test_type == 'html'){
            $response = Http::withOptions(['verify' => false])->get($test->url);
            //try again if false
            if($response == false){
                sleep(3);
                $response = Http::withOptions(['verify' => false])->get($test->url);
            }
            if($response != false){
                $result['status'] = $response->status();
                $result['result'] = strpos($response->body(), $test->assert_text);
            }

            if($response == false || $response->status() == 500){
                $result['status'] = 500;
                $result['result'] = "Failed to connect to: " . $test->url;
            }
        }

        if($test->test_type == 'crawl'){
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


//
//
//
//        if($service == 'Weblogic_WSDL_Services_getApplicationDetails'){
//            $url = 'http://dev.cms.studentaidbc.ca/api/wsdls';
//            $postData = [
//                'action' => 'getApplicationDetails',
//                "wsdl" => $test->url,
//                'params' => $params
//            ];
//            $response = $this->makeApiCall($url, $postData);
//            //var_dump($response);
//            if($response['curl_error'] == ''){
//                $obj = json_decode($response['body']);
//                if($obj->res->fail == true){
//                    $result['status'] = 500;
//                    $result['result'] = $obj->res->faultstring;
//                }else{
//                    $result['status'] = $response['http_code'];
//                    $result['result'] = $response['body'];
//                }
//            }else{
//                $result['result'] = $response['curl_error'];
//            }
//        }
//
//
//        if($service == 'E_Services_REST_Services'){
//            $url = 'http://dev.cms.studentaidbc.ca/api/curls';
//            $postData = [
//                "url" => "https://" . $env_url . "studentaidbc.ca/rest/services/SABC_StudentLoan_APIs/Data/GetActiveProgramYears"
//            ];
//            $response = $this->makeApiCall($url, $postData);
//
//            if($response['curl_error'] == ''){
//                $result['status'] = $response['http_code'];
//                $result['result'] = $response['body'];
//            }else{
//                $result['result'] = $response['curl_error'];
//            }
//        }
//
//        if($service == 'AEM_Applications_20_21') {
//            $response = Http::withOptions(['verify' => false])->get("https://" . $env_url . "studentaidbc.ca/lc/content/xfaforms/profiles/default.html?contentRoot=repository%3A%2F%2F%2FApplications%2FSABC_StudentLoan%2F2020.2021%2FForms&template=StudentLoanApplication.xdp&submitUrl=http%3A%2F%2Flocalhost%3A8080%2Frest%2Fservices%2FSABC_StudentLoan%2FServices%2FApplication%2FIntake%2FUpdateHTML%3A2021.2022&dataRef=http%3A%2F%2Flocalhost%3A8080%2Frest%2Fservices%2FSABC_StudentLoan%2FServices%2FApplication%2FFill%2FUpdate%3A2020.2021?");
//            //try again if false
//            if($response == false){
//                sleep(3);
//                $response = Http::withOptions(['verify' => false])->get("https://" . $env_url . "studentaidbc.ca/lc/content/xfaforms/profiles/default.html?contentRoot=repository%3A%2F%2F%2FApplications%2FSABC_StudentLoan%2F2020.2021%2FForms&template=StudentLoanApplication.xdp&submitUrl=http%3A%2F%2Flocalhost%3A8080%2Frest%2Fservices%2FSABC_StudentLoan%2FServices%2FApplication%2FIntake%2FUpdateHTML%3A2021.2022&dataRef=http%3A%2F%2Flocalhost%3A8080%2Frest%2Fservices%2FSABC_StudentLoan%2FServices%2FApplication%2FFill%2FUpdate%3A2020.2021?");
//            }
//
//            if($response != false){
//                $result['status'] = $response->status();
//                $result['result'] = strpos($response->body(), 'Loading');
//            }
//
//            if($response == false || $response->status() == 500){
//                $result['status'] = 500;
//                $result['result'] = "Failed to connect to: https://" . $env_url . "studentaidbc.ca/lc/content/xfaforms/profiles/default.html?contentRoot=repository%3A%2F%2F%2FApplications%2FSABC_StudentLoan%2F2020.2021%2FForms&template=StudentLoanApplication.xdp&submitUrl=http%3A%2F%2Flocalhost%3A8080%2Frest%2Fservices%2FSABC_StudentLoan%2FServices%2FApplication%2FIntake%2FUpdateHTML%3A2021.2022&dataRef=http%3A%2F%2Flocalhost%3A8080%2Frest%2Fservices%2FSABC_StudentLoan%2FServices%2FApplication%2FFill%2FUpdate%3A2020.2021?";
//            }
//        }
//
//        if($service == 'AEM_Applications_21_22') {
//            $response = Http::withOptions(['verify' => false])->get("https://" . $env_url . "studentaidbc.ca/lc/content/xfaforms/profiles/default.html?contentRoot=repository%3A%2F%2F%2FApplications%2FSABC_StudentLoan%2F2021.2022%2FForms&template=StudentLoanApplication.xdp&submitUrl=http%3A%2F%2Flocalhost%3A8080%2Frest%2Fservices%2FSABC_StudentLoan%2FServices%2FApplication%2FIntake%2FUpdateHTML%3A2021.2022&dataRef=http%3A%2F%2Flocalhost%3A8080%2Frest%2Fservices%2FSABC_StudentLoan%2FServices%2FApplication%2FFill%2FUpdate%3A2021.2022?");
//            //try again if false
//            if($response == false) {
//                sleep(3);
//                $response = Http::withOptions(['verify' => false])->get("https://" . $env_url . "studentaidbc.ca/lc/content/xfaforms/profiles/default.html?contentRoot=repository%3A%2F%2F%2FApplications%2FSABC_StudentLoan%2F2020.2021%2FForms&template=StudentLoanApplication.xdp&submitUrl=http%3A%2F%2Flocalhost%3A8080%2Frest%2Fservices%2FSABC_StudentLoan%2FServices%2FApplication%2FIntake%2FUpdateHTML%3A2021.2022&dataRef=http%3A%2F%2Flocalhost%3A8080%2Frest%2Fservices%2FSABC_StudentLoan%2FServices%2FApplication%2FFill%2FUpdate%3A2020.2021?");
//            }
//            if($response != false){
//                $result['status'] = $response->status();
//                $result['result'] = strpos($response->body(), 'Loading');
//            }
//
//            if($response == false || $response->status() == 500){
//                $result['status'] = 500;
//                $result['result'] = "Failed to connect to: https://" . $env_url . "studentaidbc.ca/lc/content/xfaforms/profiles/default.html?contentRoot=repository%3A%2F%2F%2FApplications%2FSABC_StudentLoan%2F2021.2022%2FForms&template=StudentLoanApplication.xdp&submitUrl=http%3A%2F%2Flocalhost%3A8080%2Frest%2Fservices%2FSABC_StudentLoan%2FServices%2FApplication%2FIntake%2FUpdateHTML%3A2021.2022&dataRef=http%3A%2F%2Flocalhost%3A8080%2Frest%2Fservices%2FSABC_StudentLoan%2FServices%2FApplication%2FFill%2FUpdate%3A2021.2022?";
//            }
//
//        }
//
//        if($service == 'Dashboard_Login_HTML_page') {
//
//            $response = Http::withOptions(['verify' => false])->get("https://" . $env_url . "studentaidbc.ca/dashboard/login");
//            if($response != false){
//                $result['status'] = $response->status();
//                $result['result'] = strpos($response->body(), 'dashboard-login');
//            }
//        }
//
//
//        if($service == 'Dashboard_Success_Login') {
//            $limit = 8;
//            while((App::runningUnitTests())){
//                sleep(5);
//                if($limit == 0){
//                    break;
//                }
//                if($limit > 0){
//                    $limit--;
//                }
//            }
//
//            $crawl = new Crawl();
//            $response =  $crawl->runCrawler($test);
//
//            $result['status'] = $response['pass'];
//            $result['result'] = $response['error'];
//        }
//
//        if($service == 'CDS_Login_Page') {
//            $limit = 8;
//            while((App::runningUnitTests())){
//                sleep(5);
//                if($limit == 0){
//                    break;
//                }
//                if($limit > 0){
//                    $limit--;
//                }
//            }
//
//            $crawl = new Crawl();
//            $response =  $crawl->runCrawler($test);
//
//            $result['status'] = $response['pass'];
//            $result['result'] = $response['error'];
//        }
//
//        if($service == 'Postgres_Database_Connection') {
//            $url = 'http://dev.cms.studentaidbc.ca/api/dbs';
//            $postData = [
//                'group' => $test->group,
//                "env" => $test->env,
//                'dbType' => 'Postgres'
//            ];
//            $response = $this->makeApiCall($url, $postData);
//            //var_dump($response);
//            if($response['curl_error'] == ''){
//                $result['status'] = $response['http_code'];
//                $result['result'] = $response['body'];
//            }else{
//                $result['result'] = $response['curl_error'];
//            }
//
//        }

        //DUSK tests must be updated in the dusk test file
        if($test->test_type !== 'crawl') {
            $test->status = $result['status'] == 200 ? 'Pass' : 'Fail';
            $test->response = $result['result'];

            if($result['status'] != 200){
                Log::channel('monitor')->info(" ");
                $attempt = $test->attempt+1;
                Log::channel('monitor')->info($test->group . " Test: " . $service . " on the env (" . $test->env . ") failed. Number of attempts: " . $attempt);
                Log::channel('monitor')->info($result['result']);
                Log::channel('monitor')->info(" ");

                if( $test->mute == false ){
                    //if test failed 5+ times and testing is not paused
                    if($test->attempt >= 5){
                        //get Contact info related to the test for notification

                        Log::debug($test->group . " Test: " . $service . " failed " . $test->attempt . " times. SMS User.");
                        $t = TbtbTest::where('id', $test->id)->with('contacts')->first();
                        foreach($t->contacts as $contact){
                            if($contact->mute == false && $contact->status == 'active'){
                                $send_sms = $this->smsUser($contact->cell_number, $contact->name, $service, "FAILED on " . $test->env . " for 15+ minutes. Attempts " . $test->attempt_total);
                            }

                        }
                        $test->attempt = 0;
                    }else{
                        Log::debug($test->group . " Test: " . $service . " failed " . $test->attempt . " times. ");
                        $test->attempt += 1;
                    }
                }else{
                    Log::debug($test->group . " Test: " . $service . " failed " . $test->attempt . " times. Service Muted");
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

    public function fetchSabcTests(Request $request)
    {
        $tests = [
            'branch' => 'SABC',
        ];

        $sabc_tests = TbtbTest::where('group', 'SABC')->where('env', 'production')->with('contacts')->get();
        $tests['env']['production']['name'] = 'PRODUCTION';
        foreach ($sabc_tests as $test){
            $tests['env']['production']['cases'][$test->name] = $test;
            $tests['env']['production']['cases'][$test->name]['expanded'] = false;
            $tests['env']['production']['last_test'] = $test->updated_at->toDateTimeString();
        }

        $sabc_tests = TbtbTest::where('group', 'SABC')->where('env', 'dev')->with('contacts')->get();
        $tests['env']['dev']['name'] = 'DEV';
        foreach ($sabc_tests as $test){
            $tests['env']['dev']['cases'][$test->name] = $test;
            $tests['env']['dev']['cases'][$test->name]['expanded'] = false;
            $tests['env']['dev']['last_test'] = $test->updated_at->toDateTimeString();
        }

        $sabc_tests = TbtbTest::where('group', 'SABC')->where('env', 'uat')->with('contacts')->get();
        $tests['env']['uat']['name'] = 'UAT';
        foreach ($sabc_tests as $test){
            $tests['env']['uat']['cases'][$test->name] = $test;
            $tests['env']['uat']['cases'][$test->name]['expanded'] = false;
            $tests['env']['uat']['last_test'] = $test->updated_at->toDateTimeString();
        }

        return Response::json(['status' => true, 'tests' => $tests, 'user_auth' => Auth::check()], 200); // Status code here
    }

}
