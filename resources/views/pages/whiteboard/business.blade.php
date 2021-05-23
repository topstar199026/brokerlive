@inject('LoanSplitService', 'App\Services\LoanSplitService')
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
                                    <th>Borrower</th>
                                    <th>Initial Appointment</th>
                                    <th>Submitted</th>
                                    <th>Approved</th>
                                    <th>Settled</th>
                                    <th>Discharged Date</th>
                                    <th>Upfront Paid</th>
                                    <th>Trail Paid</th>
                                    <th>Appointment to Submission</th>
                                    <th>Submission to Approval</th>
                                    <th>Approval to Settlement</th>
                                    <th>Settled to Upfront</th>
                                    <th>Loan Life (from settlement to discharge date)</th>
                                    <th>Last activity</th>
                                </tr>
                            </thead>
                            <tfoot>
                                <tr>
                                    <th>Borrower</th>
                                    <th>Initial Appointment</th>
                                    <th>Submitted</th>
                                    <th>Approved</th>
                                    <th>Settled</th>
                                    <th>Discharged Date</th>
                                    <th>Upfront Paid</th>
                                    <th>Trail Paid</th>
                                    <th>Appointment to Submission</th>
                                    <th>Submission to Approval</th>
                                    <th>Approval to Settlement</th>
                                    <th>Settled to Upfront</th>
                                    <th>Loan Life (from settlement to discharge date)</th>
                                    <th>Last activity</th>
                                </tr>
                            </tfoot>
                            <tbody>
                                @foreach($rows as $row)
                                <tr>
                                    <td><a href="/deal/edit/{$row['id']}">{{$LoanSplitService->getName($row['borrower'])}}</a></td>
                                    <td>{{$row['appts']}}</td>
                                    <td>{{$row['submitted']}}</td>
                                    <td>{{$row['approved']}}</td>
                                    <td>{{$row['settled']}}</td>
                                    <td>{{$row['discharged']}}</td>
                                    <td>{{$row['upfront']}}</td>
                                    <td>{{$row['trail']}}</td>
                                    <td>{{$row['appts_submitted']}}</td>
                                    <td>{{$row['submitted_approved']}}</td>
                                    <td>{{$row['approved_settled']}}</td>
                                    <td>{{$row['settled_upfront']}}</td>
                                    <td>{{$row['loan_life']}}</td>
                                    <td>{{$row['last_activity']}}</td>
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