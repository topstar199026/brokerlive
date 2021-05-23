<?php

namespace App\Http\Controllers\Util;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

use App\Models\Organisation;

class OrganisationUtil extends Controller
{
    public static function getOrganisation(Request $request){
        $data = Organisation::select()->get();
        if($request->input('tree')){
            $data=self::buildTree($data);
            return array(
                "data" => $data,
                'status' => 'success'
            );
        }
        return array(
            "data" => $data,
        );
    }

    public static function getOrganisationList(){
        return Organisation::select()->get();
    }
    public static function getOrganisationById($id){
        return Organisation::find($id);
    }

    public static function buildTree($list,$parent=NULL){
        $result = array();
        foreach ($list as $key => $item) {
            if(empty($item->parent) && $parent === NULL) {
                unset($list[$key]);
                $child = self::buildTree($list, $item->id);
                if(empty($child)) {
                    $result[] = array("text" => $item->legal_name);
                } else {
                    $result[] = array(
                        "text" => $item->legal_name,
                        "nodes" => $child
                    );
                }
            } elseif (!empty($parent)) {
                if($item->parent != $parent) {
                    //unset($list[$key]);
                } else {
                    unset($list[$key]);
                    $child = self::buildTree($list, $item->id);
                    if(empty($child)) {
                        $result[] = array("text" => $item->legal_name);
                    } else {
                        $result[] = array(
                            "text" => $item->legal_name,
                            "nodes" => $child
                        );
                    }
                }
            }
        }
        return $result;
    }

    public static function saveOrganisation(Request $request){
        $row=new Organisation;
        $row->legal_name=$request->input('legal_name');
        $row->trading_name=$request->input('trading_name');
        $row->short_name=$request->input('short_name');
        $row->acn=$request->input('acn');
        $row->address_line1=$request->input('address_line1');
        $row->address_line2=$request->input('address_line2');
        $row->suburb=$request->input('suburb');
        $row->state=$request->input('state');
        $row->postcode=$request->input('postcode');
        $row->country=$request->input('country');
        $row->phone_number=$request->input('phone_number');
        $row->fax_number=$request->input('fax_number');
        $row->parent=$request->input('parent');
        $row->created_by=Auth::id();
        $row->created_at=date("Y-m-d H:i:s");
        $row->updated_by=$row->created_by;
        $row->updated_at=$row->created_at;
        $row->save();
        return $row;
    }
    
    public static function editOrganisation($id, Request $request){
        $row=Organisation::find($id);
        if($row==null)return $row;
        $row->legal_name=$request->input('legal_name');
        $row->trading_name=$request->input('trading_name');
        $row->short_name=$request->input('short_name');
        $row->acn=$request->input('acn');
        $row->address_line1=$request->input('address_line1');
        $row->address_line2=$request->input('address_line2');
        $row->suburb=$request->input('suburb');
        $row->state=$request->input('state');
        $row->postcode=$request->input('postcode');
        $row->country=$request->input('country');
        $row->phone_number=$request->input('phone_number');
        $row->fax_number=$request->input('fax_number');
        $row->parent=$request->input('parent');
        $row->updated_by=Auth::id();
        $row->updated_at=date("Y-m-d H:i:s");
        $row->save();
        return $row;
    }
}
