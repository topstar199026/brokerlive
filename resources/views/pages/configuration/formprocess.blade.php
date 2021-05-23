@extends('layouts.configuration')
@section('content')
<div class="row" style="width:100%;">
    <div class="col-md-12">
        <div class="ibox float-e-margins">
            @if(isset($process)&&isset($process->id))
                <div class="ibox-title" style="border-top-width: 0px;">
                    <div class="ibox-tools" style="float:left;">
                        <h2 style="margin-bottom: 10px">Edit - {{$process->name}}</h2>
                    </div>
                </div>
            @else
                <div class="ibox-title" style="border-top-width: 0px;">
                    <div class="ibox-tools" style="float:left;">
                        <h2 style="margin-bottom: 10px">Create Process</h2>
                    </div>
                </div>
            @endif

            <div class="wrapper wrapper-content  animated fadeInRight" style="padding: 0px;">
                <div class="ibox-content ibox">
                    @if(isset($errors)&&$errors!="")
                        <div class="alert alert-warning col-md-5 col-md-offset-2">
                            {{$errors}}
                        </div>
                    @endif

                    <form class="form-horizontal" method="post" role="form">
                        <div class="form-group row">
                            <label class="col-md-3 control-label">Name</label>
                            <div class="col-md-6">
                                <input type="hidden" name="_token" id="csrf-token" value="{{ Session::token() }}" />
                                <input type="text" class="form-control" name="name" required value="{{isset($process)?$process->name:''}}"/>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-3 control-label">Description</label>
                            <div class="col-md-6">
                                <textarea name="description" rows="10" cols="63">{{isset($process)?$process->description:''}}</textarea>
                            </div>
                        </div>
                        @if(isset($dealStatus))
                        <div class="form-group">
                            <div class="wrapper wrapper-content  animated fadeInRight" style="padding-top: 0px">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="ibox float-e-margins">
                                            <div class="ibox-title" style="border-top-width: 0px;">
                                                <div class="ibox-tools">
                                                    <a title="Add Section" class="btn btn-white" href="javascript:void(0)" onclick="addSection()">Add Section</a>
                                                </div>
                                            </div>
                                            <div class="ibox-content" >
                                                <table class="table table-bordered tableSection">
                                                    <thead>
                                                    <tr>
                                                        @foreach ($dealStatus as $status)
                                                            <th style="text-align: center;">{{$status->description}}</th>
                                                        @endforeach
                                                    </tr>
                                                    </thead>
                                                    <tbody>
                                                    <tr>
                                                        @foreach ($dealStatus as $status)
                                                            <td height="200" deal-status="{{$status->id}}">
                                                                <ul class="section-ul">
                                                                    @if(isset($sections))
                                                                        @foreach ($sections as $section)
                                                                            @if($section->deal_status == $status->id)
                                                                                <li class="section" id="{{$section->name}}">
                                                                                    <i class="fa fa-remove section-remove "></i>
                                                                                    <i title="Move Section" class="fa fa-arrows-alt section-move "></i>
                                                                                    <input class="section-name" value="{{$section->name}}" placeholder="name"/>
                                                                                    <div>
                                                                                        <input class="section-add-task" value="" placeholder="Add Task">
                                                                                    </div>

                                                                                    <span class="section-task section-dropdown" section="{{$section->name}}">Tasks
                                                                                        <ul class="section-task-ul">
                                                                                            @if(isset($tasks))
                                                                                                @foreach ($tasks as $task)
                                                                                                    @if($task->section_name == $section->name)
                                                                                                        <li class="section-task-li" id="task_{{$section->name}}_{{$task->name}}" value="{{$task->name}}">
                                                                                                        {{$task->name}}<i class="fa fa-remove task-remove "></i></li>
                                                                                                    @endif
                                                                                                @endforeach
                                                                                            @endif
                                                                                        </ul>
                                                                                    </span>
                                                                                 </li>
                                                                            @endif
                                                                        @endforeach
                                                                    @endif
                                                                </ul>
                                                            </td>
                                                        @endforeach
                                                    </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif
                        <input type="hidden" name="sections" value="">
                        <input type="hidden" name="tasks" value="">
                        <div class="form-group form-buttons">
                            <div class="col-md-6 col-md-offset-3">
                                <button type="submit" class="btn btn-primary" name="btnSubmit" value="save"><span class="fa fa-save"></span> Save</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<li class="section-template">
    <i class="fa fa-remove section-remove "></i>
    <i title="Move Section" class="fa fa-arrows-alt section-move "></i>
    <input class="section-name"  placeholder="name" value="Section1">
    <div>
        <input class="section-add-task" value="" placeholder="Add Task">
    </div>

    <span class="section-task">Tasks</span>

</li>
<li class="task-template" ><span class="text"></span><i class="fa fa-remove task-remove "></i></li>
@endsection