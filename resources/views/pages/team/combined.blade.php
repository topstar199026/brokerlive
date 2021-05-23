@extends('layouts.dashboard')

@section('content')
<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row">
        <div class="col-md-12">
            <div class="ibox m-b-xs  float-e-margins">
                <div class="ibox-title">
                    <div class="ibox-tools">
						<select id="{{$select->id}}" class="{{$select->class}}" multiple="multiple">
							@foreach($select->options as $option)
							@if(is_a($option, 'App\Datas\SelectOptionGroupData'))
							<optgroup label="{{$option->label}}">
							@foreach($option->options as $opt)
							@if($opt->selected)
							<option value="{{$opt->value}}" selected="selected">{{$opt->description}}</option>
							@else
							<option value="{{$opt->value}}">{{$opt->description}}</option>
							@endif
							@endforeach
							</optgroup>
							@else
							<option value="{{$option->value}}">{{$option->description}}</option>
							@endif
							@endforeach
                        </select>
						<input type="hidden" id="startDate" value="{{$filter->fromDate}}"/>
						<input type="hidden" id="endDate" value="{{$filter->toDate}}"/>
                        <div id="reportrange" class="selectbox">
                            <i class="fa fa-calendar"></i>
                            <span>{{$filter->fromDate}} - {{$filter->toDate}}</span>
                            <b class="caret"></b>
						</div>
						<a class="btn dropdown-toggle" id="dropdownMenuLink" data-toggle="dropdown" href="#" aria-expanded="false">
                            Export</a>
                        <ul class="dropdown-menu dropdown-user" aria-labelledby="dropdownMenuLink"> 
                            <li><a class="export-link" href="{{url('team/csv?type='.$page_type)}}">CSV</a></li>
                        </ul>
                    </div>
                    <div class="ibox-tools">
                        <a class="btn dropdown-toggle"  id="dropdownMenuLink" data-toggle="dropdown" href="#" aria-expanded="false">
                            Export
                        </a>
                        <ul class="dropdown-menu dropdown-user"  aria-labelledby="dropdownMenuLink">
                            <li><a class="export-link" href="{{url('team/csv?type='.$page_type)}}">CSV</a></li>
                        </ul>
                    </div>
                </div>
			</div>
			<div class="ibox m-b-xs float-e-margins">
                <div class="ibox-title">
                    <h5>Unconditional Approvals</h5>
                    <div class="ibox-tools">
                        <a class="export-link" href="{{url('team/csv?section=approved&type='.$page_type)}}">
                            <i class="fa fa-file-excel-o"></i>
                        </a>
                        <a class="collapse-link" data-tableid="1">
                            <i class="fa fa-chevron-up"></i>
                        </a>
                    </div>
                </div>
                <div class="ibox-content">
					@include('pages.team.table', ['section' => $approvedSection])
                </div>
			</div>
			<div class="ibox m-b-xs float-e-margins">
                <div class="ibox-title">
                    <h5>Pending Approval</h5>
                    <div class="ibox-tools">
						<a class="export-link" href="{{url('team/csv?section=pending&type='.$page_type)}}">
                            <i class="fa fa-file-excel-o"></i>
                        </a>
                        <a class="collapse-link" data-tableid="2">
                            <i class="fa fa-chevron-up"></i>
                        </a>
                    </div>
                </div>
                <div class="ibox-content">
                    @include('pages.team.table', ['section' => $pendingSection])
                </div>
            </div>
            <div class="ibox m-b-xs float-e-margins">
                <div class="ibox-title">
                    <h5>Approved in principle</h5>
                    <div class="ibox-tools">
						<a class="export-link" href="{{url('team/csv?section=aip&type='.$page_type)}}">
                            <i class="fa fa-file-excel-o"></i>
                        </a>
                        <a class="collapse-link" data-tableid="2">
                            <i class="fa fa-chevron-up"></i>
                        </a>
                    </div>
                </div>
                <div class="ibox-content">
                    @include('pages.team.table', ['section' => $aipSection])
                </div>
            </div>
            <div class="ibox m-b-xs float-e-margins">
                <div class="ibox-title">
                    <h5>Submitted</h5>
                    <div class="ibox-tools">
						<a class="export-link" href="{{url('team/csv?section=submitted&type='.$page_type)}}">
                            <i class="fa fa-file-excel-o"></i>
                        </a>
                        <a class="collapse-link" data-tableid="2">
                            <i class="fa fa-chevron-up"></i>
                        </a>
                    </div>
                </div>
                <div class="ibox-content">
                    @include('pages.team.table', ['section' => $submittedSection])
                </div>
            </div>
            <div class="ibox m-b-xs float-e-margins">
                <div class="ibox-title">
                    <h5>Settled</h5>
                    <div class="ibox-tools">
						<a class="export-link" href="{{url('team/csv?section=settled&type='.$page_type)}}">
                            <i class="fa fa-file-excel-o"></i>
                        </a>
                        <a class="collapse-link" data-tableid="2">
                            <i class="fa fa-chevron-up"></i>
                        </a>
                    </div>
                </div>
                <div class="ibox-content">
                    @include('pages.team.table', ['section' => $settledSection])
                </div>
            </div>
        </div>
    </div>
</div>
@endsection