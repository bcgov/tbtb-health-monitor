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
