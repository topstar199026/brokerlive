@extends('layouts.configuration')
@section('content')
<div class="wrapper wrapper-content  animated fadeInRight" style="padding-top: 0px;width:100%;">
    <div class="row">
        <div class="col-md-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title" style="border-top-width: 0px;">
                    <div class="ibox-tools" style="float:left;">
                        <h2 style="margin-bottom: 10px">System tasks</h2>
                    </div>
                    <div class="ibox-tools">
                        <a title="Schedule a task" class="btn btn-white useradd" href="#scheduleTaskModal" data-toggle="modal" data-target="#scheduleTaskModal"><i class="fa fa-tasks"></i> Schedule a task</a>
                    </div>
                </div>
                <div class="ibox-content">
                    <label class="auto-refresh-report-checkbox">
                        <input id="auto-refresh-report" type="checkbox">Auto refresh
                    </label>
                    <span id="report-refresh-status"></span>
                    <div id="system-task-table-wrapper" role="grid" class="dataTables_wrapper">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="scheduleTaskModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="myModalLabel">Schedule a task</h4>
            </div>

            <form class="form-horizontal" id="scheduleTaskForm" method="post" role="form" action="/admin/systemTasks/create">
                <div class="modal-body">
                    <div class="form-group row">
                        <label class="col-md-3 control-label">Task</label>
                        <div class="col-md-6">
                            <input type="hidden" name="_token" id="csrf-token" value="{{ Session::token() }}" />
                            <input type="text" class="form-control" name="taskName" id="taskName" required>
                            <sub>Task name, i.e.: RefreshNestedReferrerData</sub>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-md-3 control-label">Parameters</label>
                        <div class="col-md-6">
                            <input name="taskParameter" class="form-control" id="taskParameter">
                            <sub>Task Parameters, i.e.: --userId=2</sub>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Submit</button>
                </div>
            </form>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div>
@endsection