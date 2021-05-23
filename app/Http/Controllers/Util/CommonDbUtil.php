<?php

namespace App\Http\Controllers\Util;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Arr;
use DB;

use App\Models\Deal;
use App\Models\Contact;
use App\Models\Dealstatus;
use App\Models\DealNotify;
use App\Models\DealContact;
use App\Models\ContactType;
use App\Models\ContentTag;
use App\Models\Role;
use App\Models\FileManagement;
use App\Models\PersonTitle;
use App\Models\LoanSplit;
use App\Models\LoanApplicant;
use App\Models\DocumentStatus;
use App\Models\Lender;

use App\Http\Controllers\Util\UserUtil;
use App\Http\Controllers\Util\FormatUtil;

use App\Datas\ModelTreeDeal;
use App\Datas\ContactAutoList;



class CommonDbUtil extends Controller
{
    public static function getSearch($db, $search, $columns)
    {
        return $db->where(function($query) use ($columns, $search) {
            foreach ($columns as $col) {
                $query->orWhere($col, 'like', '%' . $search . '%');
            }
        });
    }

    public static function getOrder($db, $orders, $columns)
    {
        foreach ($orders as $order) {
            $db = $db->orderBy($columns[$order['column']], $order['dir']);
        }
        return $db;
    }

    public static function getDataTable($db, $data, $columns)
    {
        $draw = data_get($data, 'draw', null);
        $start = data_get($data, 'start', null);
        $length = data_get($data, 'length', null);
        $searchParam = data_get($data, 'search', null);
        $search = is_array($searchParam) ? $searchParam['value'] : null;
        $order = data_get($data, 'order', null);

        $search && 
            $db = self::getSearch($db, $search, $columns);

        $totalData = $db->get()->count();
        $totalFiltered = $db->get()->count();
        ($order && is_array($order)) && 
            $db = self::getOrder($db, $order, $columns);
        
        $db = $db->skip($start)->take($length)->get();

        

        $tableData = array(
            "draw"            => intval( $draw ),   // for every request/draw by clientside , they send a number as a parameter, when they recieve a response/data they first check the draw number, so we are sending same number in draw.
            "recordsTotal"    => intval( $totalData ),  // total number of records
            "recordsFiltered" => intval( $totalFiltered ), // total number of records after searching, if there is no searching then totalFiltered = totalData
            "data"            => $db // total data array
        );

        return $tableData;
    }

    /**-------------------------------Aleksey------------------------------------ */
    /**
     * searchHelper will add in WHERE [column] like %[query]% to the query
     * accept an array of mix string|array of string... columns
     * example: array('id', array('firstname', 'lastname'), 'company', array('phonemobile', 'phonehome'))
     * @param $query
     * @param $columns
     * Created on 07/07/2020 by Aleksey 
     */
    public static function searchHelper($query, $sch, $columns) {
        if (empty($sch) || !isset($columns) || count($columns) == 0) {
            return $query;
        }
        if (is_array($columns)) {
            $query=$query->where(function ($query) use ($columns,$sch) {
                foreach ($columns as $col) {
                    if (is_array($col)) {
                        $query=$query->Where(function ($query) use ($col,$sch){
                            foreach ($col as $col_) {
                                $query=$query->orWhere($col_,'like', '%'.$sch.'%');
                            }
                        });
                    }else{
                        $query=$query->orWhere($col,'like', '%'.$sch.'%');
                    }
                }
            });
        }else{
            $query=$query->orWhere($columns,$sch);
        }
        return $query;
    }
    /**
     * Adding order by column to the query
     * @param array $columns
     * @param $direction
     * Created on 07/07/2020 by Aleksey 
     */
    public static function orderHelper($query, $columns, $direction) {
        if (!empty($columns)) {
            if (is_array($columns)) {
                foreach ($columns as $col) {
                    $query=self::orderHelper($query, $col, $direction);
                }
            } else {
                $query=$query->orderBy($columns, $direction);
            }
        }
        return $query;
    }
}
