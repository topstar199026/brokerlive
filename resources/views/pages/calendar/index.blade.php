@extends('layouts.dashboard')

@section('content')
<div id="calendar-wrapper" class="calendar-wrapper wrapper wrapper-content animated fadeInRight">
    <input type="hidden" id="_starttimevalue" value="{{$preferences['starttime']['value'] ?? $preferences['starttime']['default']}}" />
    <input type="hidden" id="_endtimevalue" value="{{$preferences['endtime']['value'] ?? $preferences['endtime']['default']}}" />
    <div class="row">
        <div class="col-sm-3">
            <div class="ibox ">
                <div class="ibox-title">
                    <h5>Reminder List</h5>
                    <div class="ibox-tools">
                        <a class="collapse-link">
                            <i class="fa fa-chevron-up"></i>
                        </a>
                        <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                            <i class="fa fa-wrench"></i>
                        </a>
                        <ul class="dropdown-menu dropdown-user">
                            <li><a href="#" class="dropdown-item">Config option 1</a>
                            </li>
                            <li><a href="#" class="dropdown-item">Config option 2</a>
                            </li>
                        </ul>
                        <a class="close-link">
                            <i class="fa fa-times"></i>
                        </a>
                    </div>
                </div>
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
                    </div>
                </div>
                <div class="ibox-title">
                    <div class="ibox-tools">
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
                    <div id='external-events' class="reminderLists">
                        {{-- <p>Drag a event and drop into callendar.</p>
                        <div class='external-event navy-bg'>Go to shop and buy some products.</div>
                        <div class='external-event navy-bg'>Check the new CI from Corporation.</div>
                        <div class='external-event navy-bg'>Send documents to John.</div>
                        <div class='external-event navy-bg'>Phone to Sandra.</div>
                        <div class='external-event navy-bg'>Chat with Michael.</div>
                        <p class="m-t">
                            <input type='checkbox' id='drop-remove' class="i-checks" checked /> <label for='drop-remove'>remove after drop</label>
                        </p> --}}
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-9">
            <div class="ibox float-e-margins">
                <div style="display: none" id="css-section">

                </div>
                <div class="ibox-title">
                    <h5>Deal reminder count</h5>
                </div>
                <div class="ibox-content">
                    <div id="calendar" class="calendar-content loading">
                        <div class="spinner sk-spinner sk-spinner-wave">
                            <div class="sk-rect1"></div>
                            <div class="sk-rect2"></div>
                            <div class="sk-rect3"></div>
                            <div class="sk-rect4"></div>
                            <div class="sk-rect5"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
