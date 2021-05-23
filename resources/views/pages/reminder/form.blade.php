@inject('DashboardService', 'App\Services\DashboardService')
<form name="reminderForm" method="put" class="form-horizontal">
    {{ csrf_field() }}
    <div class="brokerlive-form">
        <input type="hidden" name="id" value="{{$reminder->id}}" />
        <input type="hidden" name="deal_id" value="{{$reminder->deal_id}}" />
        <input type="hidden" name="action" value="" />
        <div class="form-group row">
            <div class="col-md-4 reminder-date">
                <label for="duedate">Due Date:</label>
                <div class="input-group input-group-sm date datepicker" data-date="{{$reminder->duedate}}">
                    <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                    <input type="text" class="form-control input-sm" name="duedate" value="{{$reminder->duedate}}" />
                </div>
            </div>
            <div class="col-md-4 reminder-tags">
                <label for="starttime">Start Time:</label>
                <div class="input-group input-group-sm" data-date="{{$reminder->_starttime()}}">
                    <span class="input-group-addon"><i class="fa fa-clock-o"></i></span>
                    <input type="text" class="form-control input-sm starttime" name="starttime" onchange="selectStartTime()" value="{{$reminder->_starttime()}}" autocomplete="off" />
                </div>
            </div>
            <div class="col-md-4 reminder-for">
                <label for="timelength">End Time</label>
                <div class="input-group input-group-sm" data-date="{{$DashboardService->getAfterTime($reminder->_starttime(), $reminder->timelength)}}">
                    <span class="input-group-addon"><i class="fa fa-stop-circle"></i></span>
                    <input type="text" class="form-control input-sm time timelength" name="timelength" value="{{$DashboardService->getAfterTime($reminder->_starttime(), $reminder->timelength)}}" autocomplete="off" />
                </div>
            </div>
        </div>
        <div class="form-group row">
            <div class="col-md-4 reminder-tags">
                <label for="tags[]">Type: </label>
                <select multiple="multiple" name="tags[]" class="js-multiple-tags form-control input-sm tags" style="width:100%;">
                    @foreach ($tags as $key => $tag)
                    <option {{$reminder->hasTag($tag->name)}}>{{$tag->name}}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4 reminder-for">
                <label for="for[]">For: </label>
                <select multiple="multiple" name="for[]" class="js-multiple-for form-control input-sm tags" style="width:100%;">
                    <option value="bdo" {{$reminder->hasFor('bdo')}}>BDO</option>
                    <option value="broker" {{$reminder->hasFor('broker')}}>Broker</option>
                    <option value="pa" {{$reminder->hasFor('pa')}}>PA</option>
                    <option value="pa2" {{$reminder->hasFor('pa2')}}>PA2</option>
                    <option value="pa3" {{$reminder->hasFor('pa3')}}>PA3</option>
                    <option value="pa4" {{$reminder->hasFor('pa4')}}>PA4</option>
                    <option value="pa5" {{$reminder->hasFor('pa5')}}>PA5</option>
                    <option value="prospector" {{$reminder->hasFor('prospector')}}>Prospector</option>
                </select>
            </div>
            <div class="col-md-4 reminder-for">
                <label for="for[]">Lender: </label>
                <select name="lender_id" class="form-control input-sm lender_id" style="width:100%;">
                    <option disabled selected value>Select Lender</option>
                    @foreach($valuesLender as $key => $value)
                    <option value="{{$key}}" {{$value === 'OTHER' ? 'disabled' : null}} {{$reminder->lender_id && $reminder->lender_id === $key ? 'selected' : null}}>{{$value}}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="form-group reminder-details">
            <div class="col-md-12">
                <label for="details">Reminder: </label>
                <textarea class="form-control" name="details" placeholder="Details...">{{$reminder->details}}</textarea>
            </div>
        </div>
        <div class="form-group reminder-comment" style="display:none;">
            <div class="col-md-12">
                <label for="comments">Action Taken: </label>
                <textarea class="form-control" name="comments" placeholder="Comments" rows="3"></textarea>
            </div>
        </div>
        <div class="form-actions clearfix">
            <div class="pull-right">
                <a class="btn btn-info btn-xs btn-cancel" href="{{url('deal/index').'/'.$reminder->deal_id}}"><i class="fa fa-times-circle"></i> Cancel</a>
                <button type="submit" class="btn btn-primary btn-xs btn-save"><i class="fa fa-save"></i> Save</button>
                @if($reminder->id !='')
                <a class="btn btn-danger btn-xs btn-delete" href="{{url('reminder/delete').'/'.$reminder->id}}"><i class="fa fa-trash-o"></i> Delete</a>
                @endif
            </div>
        </div>
    </div>
</form>
<script>
    $('input.starttime').timepicker({
        'timeFormat': 'g:i a',
        'step': 15,
        dynamic: false,
        dropdown: true,
        scrollbar: true,
        change: selectStartTime
    });
    $('input.timelength').timepicker({
        'timeFormat': 'g:i a',
        'step': 15,
        dynamic: false,
        dropdown: true,
        scrollbar: true,
        'minTime': $('input.starttime').val(),//moment($('input.starttime').val(), 'hh:mm A').add(15, 'minutes').format('hh:mm a'),
        'maxTime': moment($('input.starttime').val(), 'hh:mm A').add(1425, 'minutes').format('hh:mm a'),
        'showDuration': true
    });
    function selectStartTime(){
        // console.log($('input.starttime').val())
        $('input.timelength').timepicker('remove');
        // console.log( moment($('input.starttime').val(), 'hh:mm A').add(1425, 'minutes').format('hh:mm a'))
        $('input.timelength').timepicker({
            'timeFormat': 'g:i a',
            'step': 15,
            dynamic: false,
            dropdown: true,
            scrollbar: true,
            'minTime': $('input.starttime').val(),//moment($('input.starttime').val(), 'hh:mm A').add(15, 'minutes').format('hh:mm a'),
            'maxTime': moment($('input.starttime').val(), 'hh:mm A').add(1425, 'minutes').format('hh:mm a'),
            'showDuration': true
        });
    }

</script>
