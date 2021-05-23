<?php

namespace App\Http\Controllers\Util;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

use App\Models\Aggregator;

class AggregatorUtil extends Controller
{
    /**
     * get aggregator by id
     *    url: "/configuration/profile/getAggregator",
     * Created on 07/14/2020 by Aleksey 
     */
    public static function getAggregatorById($id){
        return Aggregator::find($id);
    }    
    
    /**
     * get List of Aggregator in configuration/profile page
     *    url: "/data/v1/getaggregator",
     * Created on 07/13/2020 by Aleksey 
     */
    public static function getAggregator(Request $request){
        $start = $request->query("page");
        $length = $request->query("page_limit");
        $order_col = $request->query('sort_col');
        $order_dir = $request->query('sort_dir');
        $query = Aggregator::select();
        $totalData = $query->get()->count();
        if($length>0)$query=$query->limit($length);
        if($start>0)$query=$query->offset($start);
        $data = $query->get();
        for($i=0;$i<count($data);$i++)$data[$i]->stamp_created=$data[$i]->created_at->format('Y-m-d H:i:s');
        for($i=0;$i<count($data);$i++)$data[$i]->stamp_updated=$data[$i]->updated_at->format('Y-m-d H:i:s');
        $json_data = array(
            "page"            => $start,
            "recordsTotal"    => intval( $totalData ),  // total number of records
            "data"            => $data // total data array
        );
        return $json_data;
    }

    /**
     * save Aggregator in configuration/profile page
     *   url:  /configuration/aggregator/create
     * Created on 07/13/2020 by Aleksey 
     */
    public static function saveAggregator(Request $request){
        $row=new Aggregator;
        $row->name=$request->input('name');
        $row->created_by=Auth::id();
        $row->created_at=date("Y-m-d H:i:s");
        $row->updated_by=$row->created_by;
        $row->updated_at=$row->created_at;
        $row->save();
    }

    /**
     * save Aggregator in configuration/profile page
     *   url:  /configuration/aggregator/create
     * Created on 07/14/2020 by Aleksey 
     */
    public static function editAggregator($id, Request $request){
        $row=Aggregator::find($id);
        $row->name=$request->input('name');
        $row->updated_by=Auth::id();
        $row->updated_at=date("Y-m-d H:i:s");
        $row->save();
    }
}
