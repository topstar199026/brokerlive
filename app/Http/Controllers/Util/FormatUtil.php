<?php

namespace App\Http\Controllers\Util;

use DateTime;
use Illuminate\Support\Carbon;
class FormatUtil
{
    // http: //kohana.top/3.1/guide/api/Num

    public static function numberFormat($number, $places, $monetary = FALSE)
    {
        $info = localeconv();

        if ($monetary) {
            $decimal = $info['mon_decimal_point'];
            $thousands = $info['mon_thousands_sep'];
        } else {
            $decimal = $info['decimal_point'];
            $thousands = $info['thousands_sep'];
        }

        //return number_format($number, $places, $decimal, $thousands);
        return number_format($number, $places, '.', ', ');
    }

    public static function checkStringEmpty($str)
    {
        if($str === null || $str === NULL || trim($str) === '') return true; else return false;
    }

    public static function regularToNumber($str)
    {
        return floatval(preg_replace("/[^-0-9\.]/", "", $str));
    }

    public static function formatDateTime($str)
    {
        if($str) return date('Y-m-d H:i:s', strtotime($str));
        else return null;
    }

    public static function formatStartTime($str)
    {
        if($str) return date('H:i:s', strtotime($str));
        else return null;
    }

    public static function formatDateTime2($str)
    {
        if($str) return date('d M Y', strtotime($str));
        else return '';
    }

    public static function formatDateTime3($str)
    {
        if($str) return date('d/m/Y', strtotime($str));
        else return '';
    }

    public static function getDuration($start, $end)
    {
        if($end == null) return 30;
        $_start = new DateTime('2000/01/20 '.$start);
        $_end = new DateTime('2000/01/20 '.$end);
        if($_start > $_end){
            $end = '2000/01/21 '.$end;
        }else if($_start < $_end){
            $end = '2000/01/20 '.$end;
        }else{
            return 30;
        }

        $_start = Carbon::parse('2000/01/20 '.$start);
        $_end = Carbon::parse($end);

        return $duration = $_end->diffInMinutes($_start);
    }
}
