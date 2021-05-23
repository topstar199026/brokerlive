<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Response;

use App\Http\Controllers\Util\UserUtil;

class TempController extends Controller
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
    public function show(Request $request)
    {
        $path = $request->route('path');
        $temp = str_replace('.html','',$request->route('temp'));
        return view('temps.'.$path.'.'.$temp)
            ->with('value', 'something')
            ->render();
    }

    public function checks()
    {
        return Response()->json(UserUtil::getUserInfo());
    }
}
