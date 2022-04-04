<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use App\Models\Crawl;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Response;
use App\Models\TestCase as TbtbTest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class TestCaseController extends Controller
{

    /**
     * Log the user out of the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }


    public function runServiceTest(Request $request, $test)
    {
        $result = ['status' => 500, 'result' => false];
        $test = TbtbTest::where('id', $test->id)->first();
        if($test->paused == true){
            $result['status'] = 200;
            $result['result'] = true;
            return $result;
        }

        $test->status = 'Pending';
        $test->save();


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


        //DUSK tests must be updated in the dusk test file
        if($test->test_type !== 'crawl') {
            $test->status = $result['status'] == 200 ? 'Pass' : 'Fail';
            $test->response = $result['result'];

            if($result['status'] != 200){
                Log::channel('monitor')->info(" ");
                $attempt = $test->attempt+1;
                Log::channel('monitor')->info($test->group . " Test: " . $test->cmd . " on the env (" . $test->env . ") failed. Number of attempts: " . $attempt);
                Log::channel('monitor')->info($result['result']);
                Log::channel('monitor')->info(" ");

                if( $test->mute == false ){
                    //if test failed 5+ times and testing is not paused
                    if($test->attempt >= 5){
                        //get Contact info related to the test for notification

                        Log::debug($test->group . " Test: " . $test->cmd . " failed " . $test->attempt . " times. SMS User.");
                        $t = TbtbTest::where('id', $test->id)->with('contacts')->first();
                        foreach($t->contacts as $contact){
                            if($contact->mute == false && $contact->status == 'active'){
                                $send_sms = $this->smsUser($contact->cell_number, $contact->name, $test->cmd, "FAILED on " . $test->env . " for 15+ minutes. Attempts " . $test->attempt_total);
                            }

                        }
                        $test->attempt = 0;
                    }else{
                        Log::debug($test->group . " Test: " . $test->cmd . " failed " . $test->attempt . " times. ");
                        $test->attempt += 1;
                    }
                }else{
                    Log::debug($test->group . " Test: " . $test->cmd . " failed " . $test->attempt . " times. Service Muted");
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

    public function rerunSingleTest(Request $request, $group, $env, $service)
    {
        $group = mb_strtoupper(filter_var(trim($group), FILTER_SANITIZE_STRING));
        $env = mb_strtolower(filter_var(trim($env), FILTER_SANITIZE_STRING));
        $service = filter_var(trim($service), FILTER_SANITIZE_STRING);

//        $group_class = new SabcController();
        $test = TbtbTest::where('group', mb_strtoupper($group))->where('env', $env)
            ->where('cmd', 'ilike', $service)
            ->first();
        if($test->paused == true){
            return Response::json(['status' => 'Paused', 'response' => ''], 200);
        }

        $process = $this->runServiceTest($request, $test);
        $retest = TbtbTest::where('group', mb_strtoupper($group))->where('env', $env)
            ->where('cmd', 'ilike', $service)
            ->first();
        return Response::json(['status' => $retest->status, 'response' => $retest->response], 200);
    }

    public function fetchSingleTest(Request $request, $group, $env, $service)
    {
        $group = mb_strtoupper(filter_var(trim($group), FILTER_SANITIZE_STRING));
        $env = mb_strtolower(filter_var(trim($env), FILTER_SANITIZE_STRING));
        $service = filter_var(trim($service), FILTER_SANITIZE_STRING);

        $test = TbtbTest::where('group', mb_strtoupper($group))->where('env', $env)->where('cmd', 'ilike', $service)->first();
        return Response::json(['status' => $test->status, 'response' => $test->response], 200);
    }


    public function pauseTest(Request $request, TbtbTest $test)
    {
        $test->paused = true;
        $test->save();
        return Response::json(['status' => $test->status, 'response' => $test->response], 200);
    }

    public function unpauseTest(Request $request, TbtbTest $test)
    {
        $test->paused = false;
        $test->save();
        return Response::json(['status' => $test->status, 'response' => $test->response], 200);
    }


    public function muteTest(Request $request, TbtbTest $test)
    {
        $test->mute = true;
        $test->save();
        return Response::json(['status' => $test->status, 'response' => $test->response], 200);
    }

    public function unmuteTest(Request $request, TbtbTest $test)
    {
        $test->mute = false;
        $test->save();
        return Response::json(['status' => $test->status, 'response' => $test->response], 200);
    }

    public function removeServiceContacts(Request $request, Contact $contact, TbtbTest $service)
    {
        $test = TbtbTest::find($service->id);
        $contact = Contact::find($contact->id);
        $test->contacts()->detach($contact);
        return Response::json(['status' => true], 200);
    }

    public function addServiceContacts(Request $request)
    {
        $validated = $request->validate([
            'frm' => 'required',
        ]);
        $formData = json_decode($request->frm, true);
        $test = TbtbTest::find($formData['service']['id']);
        $contact = Contact::find($formData['name']['id']);

        $test->contacts()->attach($contact);

        return Response::json(['status' => true], 200);
    }

    public function updateContacts(Request $request, Contact $contact)
    {
        $validated = $request->validate([
            'contact' => 'required',
        ]);
        $formData = json_decode($request->contact, true);

        $contact->name = Str::title($formData['name']);
        $contact->email = Str::lower($formData['email']);
        $contact->cell_number = $formData['cell_number'];
        $contact->level = Str::lower($formData['level']);
        $contact->status = Str::lower($formData['status']);
        $contact->sms_enabled = isset($formData['sms_enabled']) && $formData['sms_enabled'] == true;
        $contact->email_enabled = isset($formData['sms_enabled']) && $formData['email_enabled'] == true;

        $contact->save();

        return Response::json(['status' => true], 200);
    }


    public function updateServices(Request $request, TbtbTest $serv)
    {
        $validated = $request->validate([
            'serv' => 'required',
        ]);
        $formData = json_decode($request->serv, true);

        $name = Str::of($formData['name'])->trim();
        $serv->name = $name;
        $serv->group = Str::upper($formData['group']);
        $serv->env = Str::lower($formData['env']);
        $serv->cmd = Str::replace(" ", "_", $name);
        $serv->test_type = Str::lower($formData['test_type']);
        $serv->assert_text = isset($formData['assert_text']) ? Str::of($formData['assert_text'])->trim() : null;
        $serv->post_data = isset($formData['post_data']) ? Str::of($formData['post_data'])->trim() : null;
        $serv->url = isset($formData['url']) ? Str::of($formData['url'])->trim() : null;

        $serv->save();

        return Response::json(['status' => true], 200);
    }

    public function addContacts(Request $request)
    {
        $validated = $request->validate([
            'frm' => 'required',
        ]);
        $formData = json_decode($request->frm, true);

        $contact = new Contact();
        $contact->name = Str::title($formData['name']['txt']);
        $contact->email = Str::lower($formData['email']['txt']);
        $contact->cell_number = $formData['cell']['txt'];
        $contact->level = Str::lower($formData['lvl']['txt']);
        $contact->status = 'active';
        $contact->group = 'tbtb';
        $contact->last_message_date = ' ';
        $contact->last_message_text = ' ';
        $contact->sms_enabled = true;
        $contact->email_enabled = false;
        $contact->save();

        return Response::json(['status' => true], 200);
    }

    public function fetchServiceContacts(Request $request, $service = null)
    {
        $sabc = new SabcController();
        $sabc_tests = $sabc->fetchSabcTests($request);

        $jira = new JiraController();
        $jira_tests = $jira->fetchJiraTests($request);

        $ptib = new PtibController();
        $ptib_tests = $ptib->fetchPtibTests($request);

        $lists = [
            'sabc' => $sabc_tests->original['tests'],
            'jira' => $jira_tests->original['tests'],
            'ptib' => $ptib_tests->original['tests']
        ];
        $all_tests = [];
        $tests = TbtbTest::select('id', 'name', 'env', 'group')->orderBy('name')->get();
        foreach ($tests as $test){
            $all_tests[] = ['id' => $test->id, 'name' => $test->name . ' ' . $test->group . ' (' . $test->env . ')'];
        }
        $contacts = Contact::where('status', 'active')->orderBy('name')->get();

        return Response::json(['lists' => $lists, 'alltests' => $all_tests, 'contacts' => $contacts], 200);
    }


    public function fetchAccounts(Request $request, $account = null)
    {
        $accounts = Contact::orderBy('name')->get();
        return Response::json(['accounts' => $accounts], 200);
    }
}
