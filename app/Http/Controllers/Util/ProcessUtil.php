<?php

namespace App\Http\Controllers\Util;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

use App\Models\WorkflowProcess;
use App\Models\Dealstatus;
use App\Models\WorkflowSection;
use App\Models\WorkflowTask;
use App\Models\Organisation;

class ProcessUtil extends Controller
{
    /**
     * get List of getProcess in configuration/process page
     *    url: "/data/v1/process",
     * Created on 07/15/2020 by Aleksey 
     */
    public static function getProcess(Request $request){
        $start = $request->query("page");
        $length = $request->query("page_limit");
        $order_col = $request->query('sort_col');
        $order_dir = $request->query('sort_dir');
        $query = WorkflowProcess::select();
        $totalData = $query->get()->count();
        if($length>0)$query=$query->limit($length);
        if($start>0)$query=$query->offset($start);
        $data = $query->get();
        for($i=0;$i<count($data);$i++)$data[$i]->stamp_created=$data[$i]->created_at->format('Y-m-d H:i:s');
        for($i=0;$i<count($data);$i++)$data[$i]->stamp_updated=$data[$i]->updated_at->format('Y-m-d H:i:s');
        $json_data = array(
            "page"            => $start,
            "recordsTotal"    => intval( $totalData ),
            "data"            => $data 
        );
        return $json_data;
    }

    /**
     * save Process in configuration/process page
     *   url:  /configuration/process/create
     * Created on 07/15/2020 by Aleksey 
     */
    public static function saveProcess(Request $request){
        $row=new WorkflowProcess;
        $row->name=$request->input('name');
        $row->description=$request->input('description');
        $row->created_by=Auth::id();
        $row->created_at=date("Y-m-d H:i:s");
        $row->updated_by=$row->created_by;
        $row->updated_at=$row->created_at;
        $row->save();
    }

    /**
     * get Dealstatus in configuration/process page
     * Created on 07/15/2020 by Aleksey 
     */
    public static function getDealstatus(){
        return Dealstatus::select()->get();
    }

    /**
     * get section in configuration/process page
     * Created on 07/16/2020 by Aleksey 
     */
    public static function getSections($id){
        return WorkflowSection::select()->where('process_id','=',$id)->get();
    }

    /**
     * get process in configuration/process page
     * Created on 07/16/2020 by Aleksey 
     */
    public static function getProcessById($id){
        return WorkflowProcess::find($id);
    }

    /**
     * get process in configuration/process page
     * Created on 07/16/2020 by Aleksey 
     */
    public static function getTasks($id){
        return WorkflowTask::select()->where('process_id','=',$id)->get();
    }
    
    /**
     * save process in configuration/process page
     *   url:  /configuration/process/edit
     * name: dfg
     * description: dfg
     * sections: {"1":{"id":"Section1","dealStatus":"1"}}
     * tasks: {}
     * Created on 07/16/2020 by Aleksey 
     */
    public static function editProcess($id, Request $request){
        $row=WorkflowProcess::find($id);
        $row->name=$request->input('name');
        $row->description=$request->input('description');
        $row->updated_by=Auth::id();
        $row->updated_at=date("Y-m-d H:i:s");
        $row->save();
        
        $sections = $request->input("sections");
        if(!empty($sections)) {
            WorkflowSection::select()->where('process_id', '=', $id)->delete();
            $sections = json_decode($sections, true);
            foreach ($sections as $sec) {
                $section_row=new WorkflowSection;
                $section_row->user_id=Auth::id();
                $section_row->process_id=$id;
                $section_row->deal_status=$sec["dealStatus"];
                $section_row->name=$sec["id"];
                $section_row->created_by=Auth::id();
                $section_row->created_at=date("Y-m-d H:i:s");
                $section_row->updated_by=$row->created_by;
                $section_row->updated_at=$row->created_at;
                $section_row->save();
            }
        }

        $tasks = $request->input("tasks");
        if(!empty($tasks)) {
            WorkflowTask::select()->where('process_id', '=', $id)->delete();
            $tasks = json_decode($tasks, true);
            foreach ($tasks as $task) {
                $task_row=new WorkflowTask;
                $task_row->user_id=Auth::id();
                $task_row->process_id=$id;
                $task_row->section_name=$task["section"];
                $task_row->name=$task["id"];
                $task_row->created_by=Auth::id();
                $task_row->created_at=date("Y-m-d H:i:s");
                $task_row->updated_by=$row->created_by;
                $task_row->updated_at=$row->created_at;
                $task_row->save();
            }
        }
    }
}
