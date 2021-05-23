@extends('layouts.configuration')
@section('content')
<div class="wrapper wrapper-content  animated fadeInRight" style="padding-top: 0px;width:100%;">
    <div class="row">
        <div class="col-md-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title" style="border-top-width: 0px;">
                    <div class="ibox-tools" style="float:left;">
                        <h2 style="margin-bottom: 10px">Processes</h2>
                    </div>
                    <div class="ibox-tools">
                        <a title="Add Process" class="btn btn-white useradd" href="/configuration/process/create"><i class="fa fa-tasks"></i> Add</a>
                    </div>
                </div>
                <div class="ibox-content" >
                    <div role="grid" class="dataTables_wrapper">
                        <table class="table table-bordered table-striped table-hover data-table dataTable table-process">
                            <thead>
                            <tr>
                                <th>Name</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tfoot>
                            <tr>
                                <th>Name</th>
                                <th>Action</th>
                            </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection