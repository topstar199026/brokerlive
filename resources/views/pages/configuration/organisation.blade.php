@extends('layouts.configuration')
@section('content')
@if(isset($error)&&$error!='')
<div class="wrapper wrapper-content  animated fadeInRight alert alert-{{$error_type}}" style="padding: 17px;">
    {{$error}}
</div>
@endif
<div class="wrapper wrapper-content  animated fadeInRight" style="padding-top: 0px">
    <div class="row">
        <div class="col-md-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title" style="border-top-width: 0px;">
                    <div class="ibox-tools" style="float:left;">
                        <h2 style="margin-bottom: 10px">Organisation</h2>
                    </div>
                    <div class="ibox-tools">
                        <a title="Add Organisation" class="btn btn-white useradd" href="/configuration/organisation/create"><i class="fa fa-archive"></i> Add Organisation</a>
                    </div>
                </div>
                <div class="ibox-content" >
                    <div role="grid" class="dataTables_wrapper">
                        <table class="table table-bordered table-striped table-hover data-table dataTable table-organisation" style="table-layout: fixed;overflow-x:auto;">
                            <thead>
                            <tr>
                                <th>Legal Name</th>
                                <th>Trading Name</th>
                                <th>Short Name</th>
                                <th>ACN</th>
                                <th>Address1</th>
                                <th>Address2</th>
                                <th>Suburb</th>
                                <th>State</th>
                                <th>PostCode</th>
                                <th>Country</th>
                                <th>Phone</th>
                                <th>Fax</th>
                            </tr>
                            </thead>
                            <tfoot>
                            <tr>
                                <th>Legal Name</th>
                                <th>Trading Name</th>
                                <th>Short Name</th>
                                <th>ACN</th>
                                <th>Address1</th>
                                <th>Address2</th>
                                <th>Suburb</th>
                                <th>State</th>
                                <th>PostCode</th>
                                <th>Country</th>
                                <th>Phone</th>
                                <th>Fax</th>
                            </tr>
                            </tfoot>
                        </table>
                    </div>
                    <div>Relation Tree</div>
                    <div id="tree"></div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection