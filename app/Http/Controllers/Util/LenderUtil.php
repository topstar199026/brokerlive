<?php

namespace App\Http\Controllers\Util;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Arr;

use DB;

use App\Models\Lender;


use App\Http\Controllers\Util\FormatUtil;
use App\Http\Controllers\Util\JournalUtil;

use App\Datas\JournalTemp;

class LenderUtil extends Controller
{
    public static function getLenders()
    {
        return Lender::orderBy('name')
            ->get();
    }

    public static function getLendersForSelect()
    {
        return array_reduce(Lender::orderBy('name')->get()->toArray(), function ($result, $item) {
            $result[$item['id']] = $item['name'];
            return $result;
        }, array());
    }
}
