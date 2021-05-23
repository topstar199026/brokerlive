<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Arr;

use Response;

use App\Http\Controllers\Util\ContactUtil;
use App\Http\Controllers\Util\SearchUtil;

use App\Datas\SelectData;

class SearchController extends Controller
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

    public function index(Request $request)
    {
        $resData = array();

        $data = Arr::except($request->all(), ['_token']);

        $q = data_get($data, 'q');

        $phone = ContactUtil::toSimplePhone(data_get($data, 'q-phone'));

        $deals = null;

        if($phone !== '')
        {
            //$q = "phone:{$phone}";
            $deals = SearchUtil::searchByPhone($phone);
            //dd($deals);
        }
        else
        {
            if ($q != '') {
                if (strpos($q, 'phone:') === 0) {
                    $arr = explode("phone:", $q);
                    if (count($arr) >1) {
                        unset($arr[0]);
                        $phone = implode("phone:", $arr);
                        $deals = SearchUtil::searchByPhone($phone);
                    }
                } else {
                    $deals = SearchUtil::searchByName($q);
                }
            }
        }

        $result = [];
        if($deals) $result = SearchUtil::searchDeal($deals);
        $resData['deals'] = $result;

        $resData['q'] = $q;
        $resData['q_phone'] = data_get($data, 'q-phone');

        return view('pages.search.index', $resData);
    }

    public function calendar(Request $request)
    {
        $data = Arr::except($request->all(), ['_token']);



        return $response;
    }
}
