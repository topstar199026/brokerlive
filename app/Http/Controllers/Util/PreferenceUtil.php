<?php

namespace App\Http\Controllers\Util;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

use DB;

use App\Models\Preference;
use App\Models\PreferenceName;

use App\Http\Controllers\Util\FormatUtil;
use App\Http\Controllers\Util\CommonDbUtil;
use App\Http\Controllers\Util\JournalUtil;


use App\Datas\JournalTemp;

class PreferenceUtil extends Controller
{
    public static function getPreference($perferenceName)
    {
        $preference = Preference::where('name', '=', $perferenceName)
            ->where('user_id', '=', Auth::id())->first();
        return $preference && $preference->value ? $preference->value : 0;
    }

   public static function getDefaultTime()
   {
       $defaultTime = self::getPreference('starttime');
       if($defaultTime != 0)
       {
            return $defaultTime;
       }
       else
       {
            $_val = PreferenceName::where('name', '=', 'starttime')->first();
            return $_val->default;
       }
   }

   public static function getPreferenceList()
   {
        $subPreference = Preference::where('user_id', '=', Auth::id())->select('id', 'name', 'value');

        return $preferenceList = PreferenceName::leftJoin(DB::raw('('.$subPreference->toSql().') as subPreference'), function($join){
                $join->on('subPreference.name', '=', 'preference_name.name');
            })
            ->mergeBindings($subPreference->getQuery())
            ->select(
                'preference_name.id as _id',
                'subPreference.id',
                'preference_name.name',
                'preference_name.type',
                'preference_name.default',
                'subPreference.value'
            )
            ->get()
            ->toArray();
   }

   public static function getPreferenceJson()
   {
        $preferenceList = self::getPreferenceList();
        $res = [];
        foreach($preferenceList as $preference)
        {
            $res = Arr::add($res, $preference['name'], $preference);
        }
        return $res;
   }
}
