<?php

namespace App\Http\Controllers\Util;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Arr;

use DB;

use App\Models\CacheWhiteboardBasic;
use App\Models\UserRelation;
use App\Models\Organisation;


use App\Http\Controllers\Util\FormatUtil;
use App\Http\Controllers\Util\UserUtil;

use App\Datas\JournalTemp;

class WhiteboardUtil extends Controller
{
    public static function getWhiteboardRow($fromDate, $toDate)
    {
        // $rows = DB::query(Database::SELECT,
        //     '
        // SELECT 
        //     Year,
        //     Month,
        //     SUM(Leads) AS Leads,
        //     SUM(Calls) AS Calls,
        //     SUM(Appts) AS Appts,
        //     SUM(Splits) AS Splits,
        //     SUM(Submissions) AS Submissions,
        //     SUM(Preapp) AS Preapp,
        //     SUM(Pending) AS Pending,
        //     SUM(Fullapp) AS Fullapp,
        //     SUM(Settled) AS Settled
        // FROM cache_whiteboard_basic
        // WHERE user_id IN (\''.implode('\',\'', $this->getListBroker()).'\')
        // AND record_date >= \''.date('Y-m-d', strtotime($fromDate)).'\'
        // AND record_date <= \''.date('Y-m-d', strtotime($toDate)).'\'
        // GROUP BY Year, Month
        // ORDER BY record_date DESC
        //             ')->execute();

        //         return $rows;
        return CacheWhiteboardBasic::whereIn('user_id', self::getBrokerList())
            ->where('record_date', '>=', date('Y-m-d', strtotime($fromDate)))
            ->where('record_date', '<', date('Y-m-d', strtotime($toDate)))
            ->groupBy('year', 'month')
            ->orderBy('record_date', 'DESC')
            ->select(
                'year', 
                'month', 
                DB::raw('SUM(Leads) AS Leads'),
                DB::raw('SUM(Calls) AS Calls'),
                DB::raw('SUM(Appts) AS Appts'),
                DB::raw('SUM(Splits) AS Splits'),
                DB::raw('SUM(Submissions) AS Submissions'),
                DB::raw('SUM(Preapp) AS Preapp'),
                DB::raw('SUM(Pending) AS Pending'),
                DB::raw('SUM(Fullapp) AS Fullapp'),
                DB::raw('SUM(Settled) AS Settled')
            )
            ->get();
    }

    public static function getBrokerList()
    {
        $listBroker = array();
        if(Auth::user()->isHeadBroker() || Auth::user()->isOrganisationManager()) 
        {
            if(Auth::user()->isHeadBroker()) 
            {
                $relation = UserRelation::where('relation_id', '=', Auth::id())
                    ->where('type', '=', 2)
                    ->get();
                if(!empty($relation))
                {
                    $listBroker = array();
                    $relations = $relation->as_array();
                    foreach ($relations as $relation) 
                    {
                        $userId = $relation->user_id;
                        $relationUser = UserUtil::getUserById($userId);
                        if($relationUser->isBroker())$listBroker[] = $userId;
                    }
                }
            }
            if(Auth::user()->isOrganisationManager()) 
            {
                $relation = UserRelation::where('relation_id', '=', Auth::id())
                    ->where('type', '=', 3)
                    ->get();
                $listOrg = array();
                $organisations = Organisation::get()->toArray();
                if(!empty($relation)) 
                {
                    foreach ($relations as $relation) 
                    {
                        $listOrg = array_merge($listOrg, buildOrganisationTree($organisations,$re->relation_id));
                    }
                }
                $listOrg = array_unique($listOrg);
                $relation = UserRelation::whereIn('relation_id', $listOrg)
                    ->where('type', '=', 3)
                    ->get();
                foreach ($relation as $re) {
                    $userId = $relation->user_id;
                    $relationUser = UserUtil::getUserById($userId);
                    if($relationUser->isBroker())$listBroker[] = $userId;
                }
            }
            
            if(!empty($listBroker)) 
            {
                $listBroker = array_unique($listBroker);
            }
            if(empty($listBroker)) {
                $listBroker = array(-1);
            }
        }
        else
        {
            $listBroker = UserUtil::getBrockerIds();
        }
        return $listBroker;
    }

    private static function buildOrganisationTree($list, $parent = NULL) {
        $result = array();
        if(!empty($parent)) {
            $result[] = $parent;
        }
        foreach ($list as $key => $item) {
            if(empty($item->parent) && $parent === NULL) {
                unset($list[$key]);
                $child = self::buildOrganisationTree($list, $item->id);
                if(empty($child)) {
                    $result[] = $item->id;
                } else {
                    $result[] = $item->id;
                    $result = array_merge($result, $child);
                }
            } elseif (!empty($parent)) {
                if($item->parent != $parent) {
                    unset($list[$key]);
                } else {
                    unset($list[$key]);
                    $child = self::buildOrganisationTree($list, $item->id);
                    if(empty($child)) {
                        $result[] = $item->id;
                    } else {
                        $result[] = $item->id;
                        $result = array_merge($result, $child);
                    }
                }
            }
        }
        return $result;
    }
}
