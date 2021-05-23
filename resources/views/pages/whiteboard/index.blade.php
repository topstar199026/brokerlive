@extends('layouts.dashboard')

@section('content')
<div class="wrapper wrapper-content  animated fadeInRight">
    <div class="row">
        <div class="col-md-12">
            <div class="ibox m-b-xs  float-e-margins">
                <div class="ibox-title">
                    <div class="ibox-tools">
                        <input type="hidden" id="startDate" value="{{$fromDate}}"/>
						<input type="hidden" id="endDate" value="{{$toDate}}"/>
                        <div id="reportrange" class="selectbox">
                            <i class="fa fa-calendar"></i>
                            <span>{{$fromDate}} - {{$toDate}}</span>
                            <b class="caret"></b>
                        </div>
                        <a class="btn dropdown-toggle" data-toggle="dropdown" href="#" aria-expanded="false">
                            Export</a>
                        <ul class="dropdown-menu dropdown-user">
                            <li><a class="export-link" href="{{url('whiteboard/csv?type='.$page_type)}}">CSV</a></li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="ibox m-b-xs float-e-margins">
                <div class="ibox-title">
                    <h5>Unconditional Approvals</h5>
                    <div class="ibox-tools">
                        <a class="export-link" href="{{url('whiteboard/csv?section=approved&type='.$page_type)}}">
                            <i class="fa fa-file-excel-o"></i>
                        </a>
                        <a class="collapse-link" data-tableid="1">
                            <i class="fa fa-chevron-up"></i>
                        </a>
                    </div>
                </div>
                <div class="ibox-content">
                    @include('pages.whiteboard.table', ['section' => $approvedSection])
                </div>
            </div>
            <div class="ibox m-b-xs float-e-margins">
                <div class="ibox-title">
                    <h5>Pending Approval</h5>
                    <div class="ibox-tools">
                        <a class="export-link" href="{{url('whiteboard/csv?section=pending&type='.$page_type)}}">
                            <i class="fa fa-file-excel-o"></i>
                        </a>
                        <a class="collapse-link" data-tableid="2">
                            <i class="fa fa-chevron-up"></i>
                        </a>
                    </div>
                </div>
                <div class="ibox-content">
                    @include('pages.whiteboard.table', ['section' => $pendingSection])
                </div>
            </div>
            <div class="ibox m-b-xs float-e-margins">
                <div class="ibox-title">
                    <h5>Approved in principle</h5>
                    <div class="ibox-tools">
                        <a class="export-link" href="{{url('whiteboard/csv?section=aip&type='.$page_type)}}">
                            <i class="fa fa-file-excel-o"></i>
                        </a>
                        <a class="collapse-link" data-tableid="3">
                            <i class="fa fa-chevron-up"></i>
                        </a>
                    </div>
                </div>
                <div class="ibox-content">
                    @include('pages.whiteboard.table', ['section' => $aipSection])           
                </div>
            </div>
            <div class="ibox m-b-xs float-e-margins">
                <div class="ibox-title">
                    <h5>Submitted</h5>
                    <div class="ibox-tools">
                        <a class="export-link" href="{{url('whiteboard/csv?section=submitted&type='.$page_type)}}">
                            <i class="fa fa-file-excel-o"></i>
                        </a>
                        <a class="collapse-link" data-tableid="4">
                            <i class="fa fa-chevron-up"></i>
                        </a>
                    </div>
                </div>
                <div class="ibox-content">
                    @include('pages.whiteboard.table', ['section' => $submittedSection])    
                </div>
            </div>
            <div class="ibox m-b-xs float-e-margins">
                <div class="ibox-title">
                    <h5>Committed Clients</h5>
                    <div class="ibox-tools">
                        <a class="export-link" href="{{url('whiteboard/csv?section=committed&type='.$page_type)}}">
                            <i class="fa fa-file-excel-o"></i>
                        </a>
                        <a class="collapse-link" data-tableid="5">
                            <i class="fa fa-chevron-up"></i>
                        </a>
                    </div>
                </div>
                <div class="ibox-content">
                    @include('pages.whiteboard.table', ['section' => $committedSection])   
                </div>
            </div>            
            <div class="ibox m-b-xs float-e-margins">
                <div class="ibox-title">
                    <h5>Hot Clients</h5>
                    <div class="ibox-tools">
                        <a class="export-link" href="{{url('whiteboard/csv?section=hot&type='.$page_type)}}">
                            <i class="fa fa-file-excel-o"></i>
                        </a>
                        <a class="collapse-link" data-tableid="5">
                            <i class="fa fa-chevron-up"></i>
                        </a>
                    </div>
                </div>
                <div class="ibox-content">
                    @include('pages.whiteboard.table', ['section' => $hotSection])   
                </div>
            </div>
            <div class="ibox m-b-xs float-e-margins">
                <div class="ibox-title">
                    <h5>Settled</h5>
                    <div class="ibox-tools">
                        <a class="export-link" href="{{url('whiteboard/csv?section=settled&type='.$page_type)}}">
                            <i class="fa fa-file-excel-o"></i>
                        </a>
                        <a class="collapse-link" data-tableid="5">
                            <i class="fa fa-chevron-up"></i>
                        </a>
                    </div>
                </div>
                <div class="ibox-content">
                    @include('pages.whiteboard.table', ['section' => $settledSection])                              
                </div>
            </div>
        </div>
    </div>
</div>
@endsection