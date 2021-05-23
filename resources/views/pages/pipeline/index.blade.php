@extends('layouts.dashboard')

@section('content')
<div class="wrapper wrapper-content">
    <div class="row">
        <div class="col-lg-12">
            <div class="ibox">
                <div class="ibox-title">
                    <h5>Deals</h5>
                    <div class="ibox-tools">
                        @if($userRole->personalAssistant)
                        <select id="reminder-filter-broker" class="multiselect" multiple="multiple">
                            @foreach ($brokers as $key => $broker)
                            <option value="{{$broker['id']}}">{{$broker['firstname']}}&nbsp;{{$broker['lastname']}}</option>
                            @endforeach
                        </select>
                        @endif
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
                            <span></span>
                            <b class="caret"></b>
                        </div>
                        @if($userRole->broker)
                        <a title="Add Deal" class="btn dealadd" data-type="broker"><i class="fa fa-tag"></i> Add Deal</a>
                        @endif
                        @if($userRole->personalAssistant)
                        <a title="Add Deal" class="btn dealadd" data-type="assistant"><i class="fa fa-tag"></i> Add Deal</a>
                        @endif
                    </div>
                </div>
                <div class="ibox-content">
                    <div class="pipeline clearfix">
                        @foreach ($dealStatus as $key => $_status)
                        <div class="pipeline-col" data-status="{{$_status->id}}">
                            <div class="pipeline-col-title">
                                <span class="toggle" data-col="{{$key+1}}">
                                    <i class="fa fa-ellipsis-v"></i>
                                </span>
                                <div class="content">
                                    <div class="tools">
                                        <span class="label label-info">0</span>
                                        @if(count($dealStatus) !== $key + 1)
                                        <span class="chevron"></span>
                                        @endif
                                    </div>
                                    <h3>{{$_status->description}}</h3>
                                </div>
                            </div>
                            <ul class="sortable-list connect-list agile-list" data-status="{{$_status->id}}">
                            </ul>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script type="text/javascript">
        var brokers = [
            @foreach ($brokers as $key => $broker)
            { value: {{$broker['id']}}, text: '{{$broker["firstname"]}} {{$broker["lastname"]}}' }
            @if(count($brokers) !== $key + 1)
            ,
            @endif
            @endforeach
        ];
    </script>
</div>
@endsection
