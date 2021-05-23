<?php

namespace App\Http\Controllers\Util;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Arr;
use DB;
use Response;

class CommonTeamUtil extends Controller
{
    protected static function executeQuery($query, $filter, $filter_column, $order_column)
    {
        $query = self::filterQuery($query, $filter, $filter_column);
        $query = self::orderQuery($query, $order_column);
        return $query->get();
    }

    protected static function filterQuery($query, $filter, $date_column)
    {
        $teamBrokers = $filter->team_brokers();
        if ($teamBrokers) {
            $query = $query->whereIn('deals.user_id', $teamBrokers);
        } else {
            $query = $query->whereNull('deals.user_id');
        }

        if ($filter->fromDate) {
            $query = $query->where($date_column, '>=', date('Y-m-d 00:00:00', strtotime($filter->fromDate)));
        }
        if ($filter->toDate) {
            $query = $query->where($date_column, '<=', date('Y-m-d 23:59:59', strtotime($filter->toDate)));
        }
        return $query;
    }
    
    protected static function orderQuery($query, $date_column)
    {
        return $query
                ->orderBy(DB::raw('CASE WHEN ' . $date_column . ' IS NULL THEN 1 ELSE 0 END'))
                ->orderBy($date_column, 'desc');
                
    }    
}


