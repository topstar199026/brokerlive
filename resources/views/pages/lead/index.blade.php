@extends('layouts.dashboard')

@section('content')
<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row">
        <div class="col-md-12">
            <div class="ibox">
                <div class="ibox-title">
                    <div class="ibox-tools">
                        <span class="dropdown">
                            <a id="refererFilterDropdown" class="btn dropdown-toggle" data-toggle="dropdown" href="#" aria-expanded="false">
                                Selected referer <span id="selectedRefererCount" class="badge badge-primary">0</span>
                            </a>
                            <div class="dropdown-menu" aria-labelledby="refererFilterDropdown">
                                <select id="lead-filter-for" class="multiselect" multiple="multiple">
                                    @if(isset($listReferer))
                                    @foreach($listReferer as $item)
                                    <option value="{{$item['value']}}" {{$item['active'] == 1 ? 'selected' : ''}}>#{{$item['value']}} {{$item['name']}}</option>
                                    @endforeach
                                    @endif
                                </select>
                            </div>
                        </span>
                        <div id="reportrange" class="selectbox">
                            <i class="fa fa-calendar"></i>
                            <span>{{$fromDate}}-{{$toDate}}</span>
                            <b class="caret"></b>
                        </div>
                        <span class="dropdown">
                            <a class="btn dropdown-toggle" data-toggle="dropdown" href="#" aria-expanded="false">
                                Export
                            </a>
                            <div class="dropdown-menu dropdown-user">
                                <li><a id="exportLeadCSV" class="export-link" href="{{url('lead/csv')}}">CSV</a></li>
                            </div>
                        </span>
                    </div>
                </div>
                <div class="ibox-content">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-hover data-table dataTable table-leads">
                            <thead>
                                <tr>
                                    @foreach($fields as $field)
                                    <th>{{$field}}</th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tfoot>
                                <tr>
                                    @foreach($fields as $field)
                                    <th>{{$field}}</th>
                                    @endforeach
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
