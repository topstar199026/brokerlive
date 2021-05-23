@extends('layouts.dashboard')

@section('content')
<div class="wrapper wrapper-content">
    <div class="row">
        <div class="col-md-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <div class="ibox-tools">
                        @include('common.form.select', ['deal' => null, 'attributes' => $attributesStatus, 'values' => $valuesStatus, 'multiple' => 'multiple'])
                        @include('common.form.select', ['deal' => null, 'attributes' => $attributesLender, 'values' => $valuesLender, 'multiple' => 'multiple'])


                        <select id="reminder-filter-tag" class="multiselect" multiple="multiple">
                            <option value="file work">File Work</option>
                            <option value="note">Note</option>
                            <option value="call">Call</option>
                            <option value="email">Email</option>
                            <option value="prospecting call">Prospecting Call</option>
                            <option value="sales meeting">Sales Meeting</option>
                            <option value="client meeting">Client Meeting</option>
                            <option value="urgent">Urgent</option>
                            <option value="submit">Submit</option>
                            <option value="research">Research</option>
                            <option value="database call">Database Call</option>
                        </select>
                        <select id="reminder-filter-for" class="multiselect" multiple="multiple">
                            <option value="bdo">BDO</option>
                            <option value="broker">Broker</option>
                            <option value="pa">PA</option>
                            <option value="pa2">PA2</option>
                            <option value="pa3">PA3</option>
                            <option value="pa4">PA4</option>
                            <option value="pa5">PA5</option>
                            <option value="prospector">Prospector</option>
                        </select>
                        <div id="reportrange" class="selectbox">
                            <i class="fa fa-calendar"></i>
                            <span>{{$fromDate}} - {{$toDate}}</span>
                            <b class="caret"></b>
                        </div>
                    </div>
                </div>
                <div class="ibox-content">
                    <div role="grid" class="dataTables_wrapper">
                        <table class="table table-bordered table-striped table-hover data-table dataTable table-reminders" style="width: 100%;">
                            <thead>
                                <tr>
                                    <th>Due Date</th>
                                    <th >Deal</th>
                                    <th>Deal Status</th>
                                    <th>Reminder Description</th>
                                    <th>For</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tfoot>
                                <tr>
                                    <th>Due Date</th>
                                    <th >Deal</th>
                                    <th>Deal Status</th>
                                    <th>Reminder Description</th>
                                    <th>For</th>
                                    <th>Action</th>
                                </tr>
                            </tfoot>
                            <!-- <tfoot>
                                <tr>
                                    <th class="col-md-1">Due Date</th>
                                    <th class="col-md-2">Deal</th>
                                    <th class="col-md-2">Deal Status</th>
                                    <th class="col-md-4">Reminder Description</th>
                                    <th class="col-md-1">For</th>
                                    <th class="col-md-2">Action</th>
                                </tr>
                            </tfoot> -->
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
