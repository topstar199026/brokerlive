@extends('layouts.dashboard')

@section('content')
<div class="wrapper wrapper-content">
    <div class="row">
        <div class="col-sm-6">
            <div class="ibox m-b-xs  float-e-margins">
                <div class="ibox-title">
                    <h5>Due finance clauses</h5>
                    <div class="ibox-tools">
                        <span class="label label-info pull-right">Next 7 days</span>
                    </div>
                </div>
                <div class="ibox-content">
                    <table class="table table-condensed">
                        <thead>
                            <tr>
                                <th>Deal</th>
                                <th>Due Date</th>
                                <th>Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($splitsFinancedue as $split)
                            <tr>
                                <td><a href="/deal/edit/{{$split->deal->id}}">{{$split->deal->name}}</a></td>
                                <td>{{$split->_financeduedate()}}</td>
                                <td>${{$split->subloan}}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-sm-6">
            <div class="ibox m-b-xs  float-e-margins">
                <div class="ibox-title">
                    <h5>Due settlements</h5>
                    <div class="ibox-tools">
                        <span class="label label-info pull-right">Next 7 days</span>
                    </div>
                </div>
                <div class="ibox-content">
                    <table class="table table-condensed">
                        <thead>
                            <tr>
                                <th>Deal</th>
                                <th>Due Date</th>
                                <th>Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($splitsSettlementdue as $split)
                            <tr>
                                <td><a href="/deal/edit/{{$split->deal->id}}">{{$split->deal->name}}</a></td>
                                <td>{{$split->_settlementdate()}}</td>
                                <td>${{$split->subloan}}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection