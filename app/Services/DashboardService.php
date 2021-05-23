<?php

namespace App\Services;

use App\Http\Controllers\Controller;

use App\Http\Controllers\Util\FormatUtil;

class DashboardService  extends Controller
{
    public static function conversionRate($val1, $val2)
    {
        return $val2 == 0 ? 0 : ( $val1 / $val2 ) * 100;
    }

    public static function devide($val1, $val2)
    {
        return $val2 == 0 ? 0 : $val1 / $val2;
    }

    public static function formatRate($val1, $val2)
    {
        return FormatUtil::numberFormat(self::conversionRate($val1, $val2), 0);
    }

    public static function formatNumber($val)
    {
        return FormatUtil::numberFormat($val, 0);
    }

    public static function getName($name)
    {
        return strlen($name) > 20 ? substr($name, 0, 20) . "..." : $name;
    }

    public static function getAfterTime($start, $length)
    {
        if($start == null || $length == null ) return null;
        $time = strtotime($start);
        return date("h:i a", strtotime('+'.$length.' minutes', $time));
    }
}
