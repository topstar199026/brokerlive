@inject('DashboardService', 'App\Services\DashboardService')
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
                <div class="ibox-content">
					<div class="table-responsive">
						<table class="table table-bordered table-striped table-hover table-team m-b-xs clearfix">
							<thead>
								<tr>
									<th>Year</th>
                                    <th>Month</th>
                                    <th>Leads</th>
                                    <th></th>
                                    <th>Calls</th>
                                    <th></th>
                                    <th>Appts</th>
                                    <th></th>
                                    <th>Submissions</th>
                                    <th>Pre App</th>
                                    <th>Pending</th>
                                    <th><span class="badge badge-primary popup" data-content="Conversion from Submitted to Fully Approved" rel="popover" data-placement="bottom" data-trigger="hover"><i class="fa fa-info"></i></span></th>
                                    <th>Full App</th>
                                    <th><span class="badge badge-primary popup" data-content="Conversion from Fully Approved to Settled" rel="popover" data-placement="bottom" data-trigger="hover"><i class="fa fa-info"></i></span></th>
                                    <th>Settled</th>
								</tr>
							</thead>
							<tfoot>
								<tr>
									<th>Year</th>
                                    <th>Month</th>
                                    <th>Leads</th>
                                    <th></th>
                                    <th>Calls</th>
                                    <th></th>
                                    <th>Appts</th>
                                    <th></th>
                                    <th>Submissions</th>
                                    <th>Pre App</th>
                                    <th>Pending</th>
                                    <th></th>
                                    <th>Full App</th>
                                    <th></th>
                                    <th>Settled</th>
								</tr>
                            </tfoot>
                            <tbody>
                                @foreach($rows as $row)
                                <tr>
                                    <td>{{$row['Year']}}</td>
                                    <td>{{date("M", mktime(0, 0, 0, $row['Month'], 10))}}</td>
                                    <td>{{$row['Leads']}}</td>
                                    <td>{{$DashboardService->conversionRate($row['Calls'], $row['Leads'])}}%</td>
                                    <td>{{$row['Calls']}}</td>
                                    <td>{{$DashboardService->conversionRate($row['Appts'], $row['Calls'])}}%</td>
                                    <td>{{$row['Appts']}}</td>
                                    <td>{{$DashboardService->conversionRate($row['SubmissionsNumber'], $row['Appts'])}}%</td>
                                    <td>${{number_format($row['Submissions'])}}</td>
                                    <td>${{number_format($row['Preapp'])}}</td>
                                    <td>${{number_format($row['Pending'])}}</td>
                                    <td>{{$DashboardService->conversionRate($row['Fullapp'], $row['Submissions'])}}%</td>
                                    <td>${{number_format($row['Fullapp'])}}</td>
                                    <td>{{$DashboardService->conversionRate($row['Settled'], $row['Fullapp'])}}%</td>
                                    <td>${{number_format($row['Settled'])}}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection