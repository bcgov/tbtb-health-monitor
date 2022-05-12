<?php

namespace App\Http\Controllers;


use danielme85\LaravelLogToDB\LogToDB;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
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

        $day_group = [];
        $current_date = '';
        foreach ($logs as $log){
            if(!Str::startsWith($log->datetime, $current_date)){
                $current_date = explode(" ", $log->datetime);
                $current_date = $current_date[0];
            }

            $day_group[$current_date][] = $log;
        }
        return Response::json(['logs' => $day_group], 200);
    }
}
