<?php
namespace App\Http\Controllers\Util;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

use App\Models\Scribble;
use App\Models\ScribbleCategory;

class ScribbleUtil extends Controller
{
    public static function scribble()
    {
        $id=Auth::id();
        $data=Scribble::select('id','user_id','category_id','note','position','created_at as stamp_created','updated_at as stamp_updated')
        ->where('user_id','=',$id)
        ->orderBy('position')
        ->orderBy('updated_at','desc')
        ->orderBy('created_at','desc')
        ->get();
        for($i=0;$i<count($data);$i++)$data[$i]['category']=ScribbleCategory::find($data[$i]['category_id']);
        return json_encode(array(
            'data'=>$data,
            'error'=>null,
            'status'=>'success'
        ));
    }

    public static function editScribble(Request $request){
        $row=Scribble::find($request->input('id'));
        $row->note=$request->input('note');
        $row->updated_by=Auth::id();
        $row->updated_at=date("Y-m-d H:i:s");
        $row->save();
        return self::scribble();
    }

    public static function deleteScribble(Request $request){
        Scribble::find($request->input('id'))->delete();
        return self::scribble();
    }

    public static function createScribble(Request $request){
        $id=Auth::id();
        $cid=$request->input('category_id');
        if($cid==null)$pos=1000;
        else{
            $data=Scribble::select()
            ->where('user_id','=',$id)
            ->where('category_id','=',$cid)
            ->get();
            $pos=count($data)+1;
        }
        $row=new Scribble;
        $row->user_id=Auth::id();
        $row->position=$pos;
        $row->note=$request->input('note');
        $row->category_id=$cid;
        $row->updated_by=Auth::id();
        $row->updated_at=date("Y-m-d H:i:s");
        $row->created_by=$row->updated_by;
        $row->created_at=$row->updated_at;
        $row->save();
        $row->stamp_created=$row->created_at;
        $row->stamp_updated=$row->updated_at;
        return $row;
    }

    public static function updateScribble(Request $request){
        $row=Scribble::find($request->input('id'));
        $row->category_id=$request->input('category_id');
        $row->updated_by=Auth::id();
        $row->updated_at=date("Y-m-d H:i:s");
        $row->save();
        return self::scribble();
    }

    public static function sortScribble(Request $request){
        $pos=1;
        foreach($request->input('scribble') as $id){
            $row=Scribble::find($id);
            $row->position=$pos++;
            $row->updated_by=Auth::id();
            $row->updated_at=date("Y-m-d H:i:s");
            $row->save();
        }
        return self::scribble();
    }
    
    public static function saveCategory(Request $request){
        $row=new ScribbleCategory;
        $row->name=$request->input('name');
        $row->updated_by=Auth::id();
        $row->updated_at=date("Y-m-d H:i:s");
        $row->created_by=$row->updated_by;
        $row->created_at=$row->updated_at;
        $row->save();
        $row->stamp_created=$row->created_at;
        $row->stamp_updated=$row->updated_at;

        return $row;  
    }

    public static function editCategory(Request $request){
        $row=ScribbleCategory::find($request->input('id'));
        $row->name=$request->input('name');
        $row->updated_by=Auth::id();
        $row->updated_at=date("Y-m-d H:i:s");
        $row->save();
        $row->stamp_created=$row->created_at;
        $row->stamp_updated=$row->updated_at;
        return $row;  
    }
}
