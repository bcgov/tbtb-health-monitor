<?php

namespace App\Http\Controllers;


use danielme85\LaravelLogToDB\LogToDB;
use Illuminate\Http\Request;
use Response;

class HomeController extends Controller
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

    public function fetchLogs(Request $request){
        $logs = LogToDB::model()->where('level_name', '=', 'NOTICE')->orderBy('id', 'desc')->get();
        return Response::json(['logs' => $logs], 200);
    }
}
