<?php

namespace App\Http\Controllers\Util;

//use App\Http\Controllers\Util\CommonTeamUtil;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Arr;
use DB;
use Response;

use App\Models\LoanSplit;

class TeamStatsUtil extends CommonTeamUtil
{
    public static function getSettled($filter)
    {
        $query = self::baseQuery()
                ->whereNotNull('loansplits.settled');
        return self::executeQuery(
            $query, 
            $filter, 
            'loansplits.settled', 
            'loansplits.settled'
        );
    }

    public static function getApproved($filter)
    {
        $query = self::baseQuery()
            ->whereNotNull('loansplits.approved');
        return self::executeQuery(
            $query, 
            $filter, 
            'loansplits.approved', 
            'loansplits.approved'
        );
    }

    public static function getPending($filter)
    {
        $query = self::baseQuery()
            ->whereNotNull('loansplits.conditional');
        return self::executeQuery(
            $query, 
            $filter, 
            'loansplits.conditional', 
            'loansplits.conditional'
        );
    }

    public static function getAip($filter)
    {
        $query = self::baseQuery()
            ->whereNotNull('loansplits.aip');
        return self::executeQuery(
            $query, 
            $filter, 
            'loansplits.aip', 
            'loansplits.aip'
        );
    }

    public static function getSubmitted($filter)
    {
        $query = self::baseQuery()
            ->whereNotNull('loansplits.submitted');
        return self::executeQuery(
            $query, 
            $filter, 
            'loansplits.submitted', 
            'loansplits.submitted'
        );
    }
    
    public static function getCommitted($filter)
    {
        return array();
    }

    public static function getHot($filter)
    {
        return array();
    }
    
    private static function baseQuery()
    {
        return LoanSplit::leftJoin('deals', 'deals.id', '=', 'loansplits.deal_id');
    }   
}


