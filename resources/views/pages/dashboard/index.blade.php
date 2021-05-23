@inject('DashboardService', 'App\Services\DashboardService')

@extends('layouts.dashboard')

@section('content')
<div class="wrapper wrapper-content">
    <div class="row">
        <div class="col-md-12">
            <div class="ibox m-b-xs  float-e-margins">
                <div class="ibox-title">
                    <div class="row">
                        <div class="col-stat col-sm-2">
                            <h4>Leads</h4>
                        </div>
                        <div class="col-stat col-sm-2">
                            <h4>Calls</h4>
                        </div>
                        <div class="col-stat col-sm-2">
                            <h4>Appointments</h4>
                        </div>
                        <div class="col-stat col-sm-2">
                            <h4>Submitted</h4>
                        </div>
                        <div class="col-stat col-sm-2">
                            <span class="badge badge-info popup pull-right" data-content="Conversion from Submitted to Fully Approved" rel="popover" data-placement="top" data-trigger="hover" data-original-title="" title="">
                                <i class="fa fa-info"></i>
                            </span>
                            <h4>Pending</h4>
                        </div>
                        <div class="col-stat col-sm-2">
                            <span class="badge badge-info popup pull-right" data-content="Conversion from Fully Approved to Settled" rel="popover" data-placement="top" data-trigger="hover" data-original-title="" title="">
                                <i class="fa fa-info"></i>
                            </span>
                            <h4>Unconditional</h4>
                        </div>
                        <div class="col-stat col-sm-2">
                            <h4>Settled</h4>
                        </div>
                    </div>
                </div>
                <div class="ibox-content">
                    <div class="row">
                        <div class="col-stat col-sm-2">
                            <small class="stats-label pull-right" style="margin-top: 5px;">
                                {{$DashboardService->formatRate($statistic->calls, $statistic->leads)}}%
                            </small>
                            <h4>{{$DashboardService->formatNumber($statistic->leads)}}</h4>
                            <small class="stats-label">Current month</small>
                        </div>
                        <div class="col-stat col-sm-2">
                            <small class="stats-label pull-right" style="margin-top: 5px;">
                                {{$DashboardService->formatRate($statistic->appts, $statistic->calls)}}%
                            </small>
                            <h4>{{$DashboardService->formatNumber($statistic->calls)}}</h4>
                        </div>
                        <div class="col-stat col-sm-2">
                            <small class="stats-label pull-right" style="margin-top: 5px;">
                                {{$DashboardService->formatRate($statistic->submittedNumber, $statistic->appts)}}%
                            </small>
                            <h4>{{$DashboardService->formatNumber($statistic->appts)}}</h4>
                        </div>
                        <div class="col-stat col-sm-2">
                            <small class="stats-label pull-right" style="margin-top: 5px;">
                                {{$DashboardService->formatRate($statistic->pending, $statistic->submitted)}}%
                            </small>
                            <h4>{{$DashboardService->formatNumber($statistic->submitted)}}</h4>
                        </div>
                        <div class="col-stat col-sm-2">
                            <small class="stats-label pull-right" style="margin-top: 5px;">
                                {{$DashboardService->formatRate($statistic->unconditional, $statistic->submitted)}}%
                            </small>
                            <h4>{{$DashboardService->formatNumber($statistic->pending)}}</h4>
                        </div>
                        <div class="col-stat col-sm-2">
                            <small class="stats-label pull-right" style="margin-top: 5px;">
                                {{$DashboardService->formatRate($statistic->settled, $statistic->unconditional)}}%
                            </small>
                            <h4>{{$DashboardService->formatNumber($statistic->unconditional)}}</h4>
                        </div>
                        <div class="col-stat col-sm-2">
                            <h4>{{$DashboardService->formatNumber($statistic->settled)}}</h4>
                        </div>
                    </div>
                </div>
                <div class="ibox-content">
                    <div class="row">
                        <div class="col-stat col-sm-2">
                            <small class="stats-label pull-right conversion-leads" style="margin-top: 5px;" data-conversion="{{$DashboardService->formatRate($monthly->calls, $monthly->leads)}}">
                                {{$DashboardService->formatRate($monthly->calls, $monthly->leads)}}%
                            </small>
                            <h4 class="text-overflow">{{$DashboardService->formatNumber($monthly->leads / 6)}}</h4>
                            <small class="stats-label">Average, Last 6 Months</small>
                        </div>
                        <div class="col-stat col-sm-2">
                            <small class="stats-label pull-right conversion-leads" style="margin-top: 5px;" data-conversion="{{$DashboardService->formatRate($monthly->appts, $monthly->calls)}}">
                                {{$DashboardService->formatRate($monthly->appts, $monthly->calls)}}%
                            </small>
                            <h4>{{$DashboardService->formatNumber($monthly->calls / 6)}}</h4>
                        </div>
                        <div class="col-stat col-sm-2">
                            <small class="stats-label pull-right conversion-leads" style="margin-top: 5px;" data-conversion="{{$DashboardService->formatRate($monthly->submittedNumber, $monthly->appts)}}">
                                {{$DashboardService->formatRate($monthly->submittedNumber, $monthly->appts)}}%
                            </small>
                            <h4>{{$DashboardService->formatNumber($monthly->appts / 6)}}</h4>
                        </div>
                        <div class="col-stat col-sm-2">
                            <small class="stats-label pull-right conversion-leads" style="margin-top: 5px;" data-conversion="{{$DashboardService->formatRate($monthly->pending, $monthly->submitted)}}" data-number="{{$monthly->submittedNumber / 6}}"  data-avg="{{$DashboardService->devide($monthly->submitted, $monthly->submittedNumber)}}">
                                {{$DashboardService->formatRate($monthly->pending, $monthly->submitted)}}%
                            </small>
                            <h4>{{$DashboardService->formatNumber($monthly->submitted / 6)}}</h4>
                        </div>
                        <div class="col-stat col-sm-2">
                            <small class="stats-label pull-right conversion-leads" style="margin-top: 5px;" data-conversion="{{$DashboardService->formatRate($monthly->unconditional, $monthly->pending)}}">
                                {{$DashboardService->formatRate($monthly->unconditional, $monthly->submitted)}}%
                            </small>
                            <h4>{{$DashboardService->formatNumber($monthly->pending / 6)}}</h4>
                        </div>
                        <div class="col-stat col-sm-2">
                            <small class="stats-label pull-right conversion-leads" style="margin-top: 5px;" data-conversion="{{$DashboardService->formatRate($monthly->settled, $monthly->unconditional)}}">
                                {{$DashboardService->formatRate($monthly->settled, $monthly->unconditional)}}%
                            </small>
                            <h4>{{$DashboardService->formatNumber($monthly->unconditional / 6)}}</h4>
                        </div>
                        <div class="col-stat col-sm-2">
                            <h4>{{$DashboardService->formatNumber($monthly->settled / 6)}}</h4>
                        </div>
                    </div>
                </div>
                <div class="ibox-content">
                    <div class="row">
                        <div class="col-stat col-sm-2">
                            <h4 class="target-leads"></h4>
                            <small class="stats-label">Target</small>
                        </div>
                        <div class="col-stat col-sm-2">
                            <h4 class="target-calls"></h4>
                        </div>
                        <div class="col-stat col-sm-2">
                            <h4 class="target-appts"></h4>
                        </div>
                        <div class="col-stat col-sm-2">
                            <h4 class="target-submitted"></h4>
                        </div>
                        <div class="col-stat col-sm-2">
                            <h4 class="target-pending"></h4>
                        </div>
                        <div class="col-stat col-sm-2">
                            <h4 class="target-unconditional"></h4>
                        </div>
                        <div class="col-stat col-sm-2">
                            <input name="settled" class="form-control input-sm" value="" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-2">
            @include('pages.dashboard.infobox', ['title' => 'Leads', 'subtitle' => 'Avg. value','value' => $leadValue])
        </div>
        <div class="col-md-2">
            @include('pages.dashboard.infobox', ['title' => 'Calls', 'subtitle' => 'Avg. value','value' => $callValue])
        </div>
        <div class="col-md-2">
            @include('pages.dashboard.infobox', ['title' => 'Appointments', 'subtitle' => 'Avg. value','value' => $appointmentValue])
        </div>
        <div class="col-lg-6">
            @include('pages.dashboard.futureincome', ['results' => $incomeValue])
        </div>
    </div>
    <div class="row">
        <div class="col-sm-4">
            @include('pages.dashboard.infobox', ['title' => 'Initial appointment to submission', 'subtitle' => 'Avg. Days','value' => $avgappointmentsubmissionValue])
        </div>
        <div class="col-sm-4">
            @include('pages.dashboard.infobox', ['title' => 'Initial appointment to settlement', 'subtitle' => 'Avg. Days','value' => $avgappointmentsettledValue])
        </div>
        <div class="col-sm-4">
            @include('pages.dashboard.infobox', ['title' => 'Settlement till upfront comm paid', 'subtitle' => 'Avg. Days','value' => $avgsettlementcommissionValue])
        </div>
    </div>
    <div class="row">
        <div class="col-sm-6">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>My Loan Book</h5>
                    <div class="ibox-tools">
                        <span class="badge badge-info popup pull-right" data-content="All data is drawn from the 'Trail Value' from each loan split" rel="popover" data-placement="top" data-trigger="hover" data-original-title="" title="">
                            <i class="fa fa-info"></i>
                        </span>
                    </div>
                </div>
                <div class="ibox-content">
                    @include('pages.dashboard.myloanbook')
                </div>
            </div>
        </div>
        <div class="col-sm-6">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>My Loan Book - Last 12 months</h5>
                    <div class="ibox-tools">
                        <span class="badge badge-info popup pull-right" data-content="All data is drawn from the 'Trail Value' from each loan split that settled in the last 12 months" rel="popover" data-placement="top" data-trigger="hover" data-original-title="" title="">
                            <i class="fa fa-info"></i>
                        </span>
                    </div>
                </div>
                <div class="ibox-content">
                    @include('pages.dashboard.myloanbooksummary')
                </div>
            </div>
        </div>
    </div>
    <div class="row pie-chart">
        <div class="col-sm-4">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>Settled Loans - Last 12 months</h5>
                </div>
                <div class="ibox-content">
                <div id="chart-year-settle" action="yearsettleloan"></div>
                </div>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>Settled Loans</h5>
                </div>
                <div class="ibox-content">
                <div id="chart-settle" action="settleloan"></div>
                </div>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>Active Loans</h5>
                </div>
                <div class="ibox-content">
                <div id="chart-active" action="activeloan"></div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
