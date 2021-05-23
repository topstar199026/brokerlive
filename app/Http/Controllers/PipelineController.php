<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

//use Response;

use App\Http\Controllers\Util\UserUtil;
use App\Http\Controllers\Util\DealUtil;



class PipelineController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //$this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(Request $request)
    {
        $currentDate = date('d M Y');
        $dealStatus = DealUtil::getStatus();
        $brokers = UserUtil::getBrokers();

        return view('pages.pipeline.index', [
            'fromDate' => $currentDate,
            'toDate' => $currentDate,
            'dealStatus' => $dealStatus,
            'brokers' => $brokers
        ]);
    }
}
