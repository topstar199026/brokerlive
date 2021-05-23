@extends('layouts.dashboard')

@section('content')
<div id="calendar-wrapper" class="calendar-wrapper wrapper wrapper-content animated fadeInRight">
    <div class="row">
        <div class="col-sm-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>Search Results</h5>
                </div>
                <div class="ibox-content">
                    <div role="grid" class="dataTables_wrapper grid-reminder">
                        <table class="table table-bordered table-striped table-hover data-table dataTable table-reminders">
                            <thead>
                                <tr>
                                    <th class="col-md-2">Due Date</th>
                                    <th class="col-md-2">Deal</th>
                                    <th class="col-md-2">Deal Status</th>
                                    <th class="col-md-4">Reminder Description</th>
                                </tr>
                            </thead>
                            <tbody id="dealList">
                            @foreach($deals as $deal)
                                @php
                                $reminder = $deal->firstReminder();
                                @endphp
                                <tr class="row-reminder" data-reminder="{{$reminder?$reminder['id']:''}}">
                                    <td class="reminder-date">
                                        {{$reminder?date('d M Y',strtotime($reminder->duedate)):''}}
                                    </td>
                                    <td class="reminder-dealname">
                                        <a href="{{url('deal/index/'.$deal->id)}}">{{$deal->name}}</a>
                                    </td>
                                    <td class="reminder-dealstatus">
                                        {{$deal->dealstatus->description}}
                                    </td>
                                    <td class="reminder-details">
                                        <div class="reminder-tags">
                                            @if($reminder)
                                            @include('pages.search.badges', ['tags' => $reminder->arrayTag()])
                                            @endif
                                        </div>
                                        <div class="reminder-content">
                                            {!!$reminder?$reminder->details:''!!}
                                        </div>
                                    </td>
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
