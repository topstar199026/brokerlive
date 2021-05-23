@extends('layouts.dashboard')

@section('content')
<div class="wrapper wrapper-content">
    <div class="row">
        <div class="col-md-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <div class="ibox-tools">
                        <select id="journal-filter-by" class="multiselect" multiple="multiple">
                            @foreach ($teams as $key => $member)
                            <option value="{{$member['id']}}">{{$member['lastname']}}, {{$member['firstname']}}</option>
                            @endforeach
                        </select>
                        <input type="hidden" id="fromDate" value="{{$fromDate}}"/>
						<input type="hidden" id="toDate" value="{{$toDate}}"/>
                        <div id="reportrange" class="selectbox">
                            <i class="fa fa-calendar"></i>
                            <span>{{$fromDate}} - {{$toDate}}</span>
                            <b class="caret"></b>
                        </div>
                        <a class="btn" href="#">
                            Export
                        </a>
                    </div>
                    <div class="ibox-tools">
                        <a class="btn dropdown-toggle"  id="dropdownMenuLink" data-toggle="dropdown" href="#" aria-expanded="false">
                            Export
                        </a>
                        <ul class="dropdown-menu dropdown-user"  aria-labelledby="dropdownMenuLink">
                            <li>
                                <a class="export-link" href="#" onclick="journalTable.journalTable('csv', '{{url('journal/csv')}}')">CSV</a>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="ibox-content" >
                    <div role="grid" class="dataTables_wrapper">
                        <table class="table table-bordered table-striped table-hover data-table dataTable table-journal" style="width: 100%;">
                            <thead>
                                <tr>
                                    @foreach ($fields as $field)
                                    <th>{{$field}}</th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tfoot>
                                <tr>
                                    @foreach ($fields as $field)
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
