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

}
