@extends('layouts.configuration')
@section('content')
<div class="wrapper wrapper-content  animated fadeInRight" style="padding-top: 0px; width:100%;">
    <div class="row">
        <div class="col-md-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title" style="border-top-width: 0px;">
                    <div class="ibox-tools" style="float:left;">
                        <h2 style="margin-bottom: 10px">Aggregators</h2>
                    </div>
                    <div class="ibox-tools">
                        <a title="Add Aggregator" class="btn btn-white useradd" href="/configuration/aggregator/create"><i class="fa fa-archive"></i> Add Aggregator</a>
                    </div>
                </div>
                <div class="ibox-content" >
                    <div role="grid" class="dataTables_wrapper">
                        <table class="table table-bordered table-striped table-hover data-table dataTable table-aggregator">
                            <thead>
                            <tr>
                                <th>Name</th>
                                <th>Created At</th>
                                <th>Updated At</th>
                            </tr>
                            </thead>
                            <tfoot>
                            <tr>
                                <th>Name</th>
                                <th>Created At</th>
                                <th>Updated At</th>
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