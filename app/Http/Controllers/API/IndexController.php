<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Response;

use App\Http\Controllers\Util\DealUtil;

use App\Http\Controllers\API\DealController;

class IndexController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $name = $request->route('name');
        $action = '';
        $actionId = '';

        

        try
        {
            $action = $request->route('action');
        }
        catch(Exception  $e)
        {
            $action = '';
        }
        try
        {
            $actionId = $request->route('id');
        } catch (Exception $e) {
            $actionId = '';
        }
 
        switch ($name) {
            case 'deal':
                switch ($action) {
                    case '':
                        return DealController::index($request);
                        break;
                    case 'notification':
                        switch ($actionId) {
                            case '':
                                break;
                            default :
                                return DealController::notification($request);
                                break;
                        }
                        break;
                }
                break;
            case 'reminder':
                return 'reminder empty';
                break;
        }

    }
}
