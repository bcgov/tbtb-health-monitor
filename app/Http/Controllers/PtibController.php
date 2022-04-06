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

    public function fetchPtibTests(Request $request)
    {
        $tests = [
            'branch' => 'PTIB',
        ];
        $test_cases = TbtbTest::where('group', 'PTIB')->whereIn('env', ['production','uat'])->with('contacts')->get();
        $tests['env']['production']['name'] = 'PRODUCTION';
        $tests['env']['uat']['name'] = 'UAT';
        foreach ($test_cases as $test){
            $tests['env'][$test->env]['cases'][$test->name] = $test;
            $tests['env'][$test->env]['cases'][$test->name]['expanded'] = false;
            $tests['env'][$test->env]['last_test'] = $test->updated_at->toDateTimeString();
        }

        return Response::json(['status' => true, 'tests' => $tests, 'user_auth' => Auth::check()], 200); // Status code here
    }

}
