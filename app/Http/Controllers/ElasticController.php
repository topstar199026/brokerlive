<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Arr;

use Response;
use Artisan;

use App\Http\Controllers\Util\FormatUtil;
use App\Http\Controllers\Util\FileUtil;
use App\Http\Controllers\Util\UserUtil;
use App\Http\Controllers\Util\DealUtil;
use App\Http\Controllers\Util\LenderUtil;
use App\Http\Controllers\Util\LoanSplitUtil;
use App\Http\Controllers\Util\WhiteboardUtil;
use App\Http\Controllers\Util\CsvUtil;
use App\Http\Controllers\Util\SearchUtil;

use App\Datas\DashboardData;

class ElasticController extends Controller
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

    public function create(Request $request)
    {
        $data = Arr::except($request->all(), ['_token']);
        if(data_get($data, 'actionKey', null) !== SearchUtil::getActionKey()) return response()->json(['data'=> 'no']);
        Artisan::call('elastic:migrate');
        return response()->json(['data'=> 'ok']);
    }

    public function reset(Request $request)
    {
        $data = Arr::except($request->all(), ['_token']);
        if(data_get($data, 'actionKey', null) !== SearchUtil::getActionKey()) return response()->json(['data'=> 'no']);
        Artisan::call('elastic:migrate:reset');
        return response()->json(['data'=> 'ok']);
    }

    public function flush(Request $request)
    {
        $data = Arr::except($request->all(), ['_token']);
        if(data_get($data, 'actionKey', null) !== SearchUtil::getActionKey()) return response()->json(['data'=> 'no']);
        try {
            Artisan::call('scout:flush', ['model' => 'App\Models\Deal']);
        } catch (Exception $e) {

        }
        try {
            Artisan::call('scout:flush', ['model' => 'App\Models\DealContact']);
        } catch (Exception $e) {

        }
        try {
            Artisan::call('scout:flush', ['model' => 'App\Models\Reminder']);
        } catch (Exception $e) {

        }
        try {
            Artisan::call('scout:flush', ['model' => 'App\Models\JournalEntry']);
        } catch (Exception $e) {

        }

        return response()->json(['data'=> 'ok']);
    }

    public function import(Request $request)
    {
        $data = Arr::except($request->all(), ['_token']);
        if(data_get($data, 'actionKey', null) !== SearchUtil::getActionKey()) return response()->json(['data'=> 'no']);
        Artisan::call('scout:import', ['model' => 'App\Models\Deal']);
        Artisan::call('scout:import', ['model' => 'App\Models\DealContact']);
        Artisan::call('scout:import', ['model' => 'App\Models\Reminder']);
        Artisan::call('scout:import', ['model' => 'App\Models\JournalEntry']);
        return response()->json(['data'=> 'ok']);
    }

    public function full(Request $request)
    {
        $data = Arr::except($request->all(), ['_token']);
        if(data_get($data, 'actionKey', null) !== SearchUtil::getActionKey()) return response()->json(['data'=> 'no']);
        Artisan::call('elastic:migrate');
        Artisan::call('scout:import', ['model' => 'App\Models\Deal']);
        Artisan::call('scout:import', ['model' => 'App\Models\DealContact']);
        Artisan::call('scout:import', ['model' => 'App\Models\Reminder']);
        Artisan::call('scout:import', ['model' => 'App\Models\JournalEntry']);
        return response()->json(['data'=> 'ok']);
    }


    public function state(Request $request)
    {
        $model = $request->route('model');
        $data = Arr::except($request->all(), ['_token']);
        if(data_get($data, 'actionKey', null) !== SearchUtil::getActionKey()) return response()->json(['count'=> 0]);
        $state = SearchUtil::getState($model);
        return response()->json(['count'=>$state]);
    }

    public function cron(Request $request)
    {
        $data = Arr::except($request->all(), ['_token']);
        if(data_get($data, 'actionKey', null) !== SearchUtil::getActionKey()) return response()->json(['data'=> 'no']);
        try {
            Artisan::call('database:backup');
            FileUtil::dbUpload();
            return response()->json(['data'=> 'ok']);
        } catch (Exception $e) {
            return response()->json(['data'=> 'no']);
        }
    }

    public function list()
    {
        return FileUtil::dbList();
    }
}
