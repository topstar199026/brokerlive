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
                        <select id="lender-filter-for" class="multiselect" multiple="multiple">
                            @if(isset($lenders))
                            @foreach($lenders as $lender)
                            <option value="{{$lender->id}}" {{isset($filter['lender']) && in_array($lender->id, $filter['lender']) ? 'selected' : ''}}>
                                {{$lender->name}}
                            </option>
                            @endforeach
                            @endif()
                        </select>
                        <select id="lvr-filter-for" class="multiselect" multiple="multiple">
                            <option value="1" {{isset($filter["lvr"]) && in_array(1, $filter["lvr"]) ? "selected" : ''}}> {{'<80%'}}' </option>
                            <option value="2" {{isset($filter["lvr"]) && in_array(2, $filter["lvr"]) ? "selected" : ''}}> {{'80% - 90%'}} </option>
                            <option value="3" {{isset($filter["lvr"]) && in_array(3, $filter["lvr"]) ? "selected" : ''}}> {{'>90%'}} </option>
                        </select>
                        <select id="status-filter-for" class="multiselect" multiple="multiple">
                            @if(isset($dealStatus))
                            @foreach($dealStatus as $status)
                            <option value="{{$status->id}}" {{isset($filter['status']) && in_array($status->id, $filter['status']) ? 'selected' : ''}}>
                                {{$status->description}}
                            </option>
                            @endforeach
                            @endif()
                        </select>
                        <a class="btn dropdown-toggle" id="dropdownMenuLink" data-toggle="dropdown" href="#" aria-expanded="false">
                            Export</a>
                        <ul class="dropdown-menu dropdown-user" aria-labelledby="dropdownMenuLink">
                            <li><a class="export-link" href="{{url('whiteboard/csv?type='.$page_type)}}">CSV</a></li>
                        </ul>
                    </div>
                    <div class="ibox-tools">
                        <style>
                            ul.dropdown-menu {
                                left: -104px !important;
                            }
                        </style>
                        <a class="btn dropdown-toggle"  id="dropdownMenuLink" data-toggle="dropdown" href="#" aria-expanded="false">
                            Export
                        </a>
                        <ul class="dropdown-menu dropdown-user"  aria-labelledby="dropdownMenuLink">
                            <li><a class="export-link" href="{{url('whiteboard/csv?type='.$page_type)}}">CSV</a></li>
                        </ul>
                    </div>
                </div>
                <div class="ibox-content">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-hover table-whiteboard m-b-xs clearfix">
                            <thead>
                                <tr>
                                    <th>Borrower</th>
                                    <th>Submitted Date</th>
                                    <th>Settlement Date</th>
                                    <th>Lender</th>
                                    <th>Loan Amount</th>
                                    <th>LVR</th>
                                    <th>Pipeline Status</th>
                                    <th>Type</th>
                                    <th>Email Address</th>
                                    <th>Mobile Number</th>
                                    <th>Postal Address</th>
                                </tr>
                            </thead>
                            <tfoot>
                                <tr>
                                    <th>Borrower</th>
                                    <th>Submitted Date</th>
                                    <th>Settlement Date</th>
                                    <th>Lender</th>
                                    <th>Loan Amount</th>
                                    <th>LVR</th>
                                    <th>Pipeline Status</th>
                                    <th>Type</th>
                                    <th>Email Address</th>
                                    <th>Mobile Number</th>
                                    <th>Postal Address</th>
                                </tr>
                            </tfoot>
                            <tbody>
                                @foreach($rows as $row)
                                <tr>
                                    <td><a href="/deal/edit/{{$row['id']}}">{{$DashboardService->getName($row['borrower'])}}</a></td>
                                    <td>{{$row['submitted']}}</td>
                                    <td>{{$row['settled']}}</td>
                                    <td>{{$row['lender']}}</td>
                                    <td>{{$row['amount']}}</td>
                                    <td>{{$row['lvr']}}</td>
                                    <td>{{$row['status']}}</td>
                                    <td>{{$row['type']}}</td>
                                    <td>{{$row['email']}}</td>
                                    <td>{{$row['phone']}}</td>
                                    <td>{{$row['postal']}}</td>
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
