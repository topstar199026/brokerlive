<?php

namespace App\Http\Controllers\Util;

//use App\Http\Controllers\Util\CommonTeamUtil;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Arr;
use DB;
use Response;

use App\Models\LoanSplit;

class TeamPipelineUtil extends CommonTeamUtil
{
    public static function getSettled($filter)
    {
        $query = self::baseQuery()
            ->whereNotNull('loansplits.settled');
        return self::executeQuery(
            $query, 
            $filter, 
            'loansplits.settled', 
            'loansplits.settlementdate'
        );
    }
    
    public static function getApproved($filter)
    {
        $query = self::baseQuery()
            ->whereNotNull('loansplits.approved')
            ->whereNull('loansplits.settled');
        return self::executeQuery(
            $query, 
            $filter, 
            'loansplits.approved', 
            'loansplits.settlementdate'
        );
    }
    
    public static function getPending($filter)
    {
        $query = self::baseQuery()
            ->whereNotNull('loansplits.conditional')
            ->whereNull('loansplits.approved')
            ->whereNull('loansplits.settled');
        return self::executeQuery(
            $query, 
            $filter, 
            'loansplits.conditional', 
            'loansplits.settlementdate'
        );
    }
    
    public static function getAip($filter)
    {
        $query = self::baseQuery()
            ->whereNotNull('loansplits.aip')
            ->whereNull('loansplits.conditional')
            ->whereNull('loansplits.approved')
            ->whereNull('loansplits.settled');
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
            ->whereNotNull('loansplits.submitted')
            ->whereNull('loansplits.aip')
            ->whereNull('loansplits.approved')
            ->whereNull('loansplits.settled')
            ->whereNull('loansplits.settled');
        return self::executeQuery(
            $query, 
            $filter, 
            'loansplits.submitted', 
            'loansplits.aip'
        );
    }

    public static function getCommitted($filter)
    {
        $query = self::baseQuery()
                ->where('loansplits.committedclient', '=', 1);
        $query = self::orderQuery($query, 'loansplits.submitted');        
        return $query->get();
    }
    
    public static function getHot($filter)
    {
        $query = self::baseQuery()
                ->where('loansplits.hotclient', '=', 1);
        $query = self::orderQuery($query, 'loansplits.submitted');        
        return $query->get();
    }

    
    private static function baseQuery()
    {
        return LoanSplit::leftJoin('deals', 'deals.id', '=', 'loansplits.deal_id')
            ->whereNull('loansplits.notproceeding');
    }
}


