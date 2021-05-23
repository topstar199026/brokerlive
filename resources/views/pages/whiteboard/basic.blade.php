@inject('DashboardService', 'App\Services\DashboardService')
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
                            <span>{{$fromDate}}-{{$toDate}}</span>
                            <b class="caret"></b>
                        </div>
                        <a class="btn dropdown-toggle" data-toggle="dropdown" href="#" aria-expanded="false">
                            Export</a>
                        <ul class="dropdown-menu dropdown-user">
                            <li><a class="export-link" href="{{url('whiteboard/csv?type='.$page_type)}}">CSV</a></li>
                        </ul>
                    </div>
                </div>
                <div class="ibox-content">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-hover table-whiteboard m-b-xs clearfix">
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
                                    <th><span class="badge badge-primary popup" data-content="Conversion from Submitted to Fully Approved" rel="popover" data-placement="top" data-trigger="hover"><i class="fa fa-info"></i></span></th>
                                    <th>Full App</th>
                                    <th><span class="badge badge-primary popup" data-content="Conversion from Fully Approved to Settled" rel="popover" data-placement="top" data-trigger="hover"><i class="fa fa-info"></i></span></th>
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
                                    <td>{{$row['year']}}</td>
                                    <td>{{date("M", mktime(0, 0, 0, $row['month'], 10))}}</td>
                                    <td>{{$row['Leads']}}</td>
                                    <td>{{$DashboardService->formatRate($row['Calls'], $row['Leads'])}}%</td>
                                    <td>{{$row['Calls']}}</td>
                                    <td>{{$DashboardService->formatRate($row['Appts'], $row['Calls'])}}%</td>
                                    <td>{{$row['Appts']}}</td>
                                    <td>{{$DashboardService->formatRate($row['Splits'], $row['Appts'])}}%</td>
                                    <td>{{$DashboardService->formatNumber($row['Submissions'])}}</td>
                                    <td>{{$DashboardService->formatNumber($row['Preapp'])}}</td>
                                    <td>{{$DashboardService->formatNumber($row['Pending'])}}</td>
                                    <td>{{$DashboardService->formatRate($row['Fullapp'], $row['Submissions'])}}%</td>
                                    <td>{{$DashboardService->formatNumber($row['Fullapp'])}}</td>
                                    <td>{{$DashboardService->formatRate($row['Settled'], $row['Fullapp'])}}%</td>
                                    <td>{{$DashboardService->formatNumber($row['Settled'])}}</td>
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
