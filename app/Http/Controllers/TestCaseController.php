<?php

namespace App\Http\Controllers;

use App\Models\Contact;
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

        switch ($test->group){
            case "SABC": $group_class = new SabcController(); $process = $group_class->runSabcTest($request, $test); break;
            case "PTIB": $group_class = new PtibController(); $process = $group_class->runPtibTest($request, $test); break;
            case "JIRA": $group_class = new JiraController(); $process = $group_class->runJiraTest($request, $test); break;
        }
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
