<?php

namespace App\Http\Controllers;


use Illuminate\Support\Facades\Log;
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

    public function duskStatus(Request $request){
        $path = exec("pwd");
        $artisan = file_exists($path . '/artisan') ? 'php artisan' : 'cd .. && php artisan';
        exec($artisan . " dusk:chrome-driver --detect 2>&1", $out, $ret);

        foreach ($out as $o){
            if(!mb_strpos($o, 'ErrorException')){
                return Response::json(['status' => true, 'out' => $out, 'ret' => $ret], 500); // Status code here
            }
        }
        return Response::json(['status' => true, 'out' => $out, 'ret' => $ret], 200); // Status code here
    }

}
