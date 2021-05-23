@{{^id}}
<div id="newReminderForm" class="reminder-form collapsibleGroup collapse in multi-collapse show">
@{{/id}}
@{{#id}}
<div id="reminderForm-@{{id}}" class="reminder-form collapse">
@{{/id}}
    <form name="reminderForm" method="POST" action="" class="form-horizontal brokerlive-form form-reminder">
        <div class="brokerlive-form">
            @{{#id}}
            <input type="hidden" class="id" name="id" value="@{{id}}" />
            @{{/id}}
            <input type="hidden" name="deal_id" value="@{{deal_id}}" />
            <div class="form-group row">
                <div class="col-md-4 reminder-date">
                    <label for="duedate">Due Date:</label>
                    <div class="input-group input-group-sm date datepicker">
                        <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                        <input type="text" class="form-control input-sm" name="duedate" value="@{{_duedate}}" required>
                    </div>
                </div>
                <div class="col-md-4 reminder-tags">
                    <label for="duedate">Start Time:</label>
                    <div class="input-group input-group-sm">
                        <span class="input-group-addon"><i class="fa fa-clock-o"></i></span>
                        @{{^id}}
                        <input type="text" class="form-control input-sm starttime" id="starttime-0" name="starttime"
                        @{{/id}}
                        @{{#id}}
                        <input type="text" class="form-control input-sm starttime" id="starttime-@{{id}}" name="starttime"
                        @{{/id}}
                        value="@{{_startTime}}" autocomplete="off">
                    </div>
                </div>
                <div class="col-md-4 reminder-for">
                    <label for="duedate">End Time:</label>
                    <div class="input-group input-group-sm">
                        <span class="input-group-addon"><i class="fa fa-stop-circle"></i></span>
                        @{{^id}}
                        <input type="text" class="form-control input-sm timelength" id="timelength-0" name="timelength" value="@{{#getAfterTime}}@{{_startTime}}---@{{timelength}}@{{/getAfterTime}}" autocomplete="off" placeholder="30 Minutes">
                        @{{/id}}
                        @{{#id}}
                        <input type="text" class="form-control input-sm timelength" id="timelength-@{{id}}" name="timelength" value="@{{#getAfterTime}}@{{_startTime}}---@{{timelength}}@{{/getAfterTime}}" autocomplete="off" placeholder="30 Minutes">
                        @{{/id}}
                    </div>
                </div>
            </div>
            <div class="form-group row">
                <div class="col-md-4 reminder-tags">
                    <label for="tags[]">Type: </label>
                    <select multiple="multiple" name="tags[]" class="js-multiple-tags form-control input-sm tags"  style="width:100%;">
                        @{{#availableTags}}
                        <option value="@{{id}}"@{{#isSelected}}@{{tags}}|@{{name}}@{{/isSelected}}>@{{name}}</option>
                        @{{/availableTags}}
                    </select>
                </div>
                <div class="col-md-4 reminder-for">
                    <label for="for[]">For: </label>
                    <select multiple="multiple" name="who_for[]" class="js-multiple-for form-control input-sm tags"  required  style="width:100%;">
                        <option value="bdo"@{{#isSelected}}@{{who_for}}|bdo@{{/isSelected}}>BDO</option>
                        <option value="broker"@{{#isSelected}}@{{who_for}}|broker@{{/isSelected}}>Broker</option>
                        <option value="pa"@{{#isSelected}}@{{who_for}}|pa@{{/isSelected}}>PA</option>
                        <option value="pa2"@{{#isSelected}}@{{who_for}}|pa2@{{/isSelected}}>PA2</option>
                        <option value="pa3"@{{#isSelected}}@{{who_for}}|pa3@{{/isSelected}}>PA3</option>
                        <option value="pa4"@{{#isSelected}}@{{who_for}}|pa4@{{/isSelected}}>PA4</option>
                        <option value="pa5"@{{#isSelected}}@{{who_for}}|pa5@{{/isSelected}}>PA5</option>
                        <option value="prospector"@{{#isSelected}}@{{who_for}}|prospector@{{/isSelected}}>Prospector</option>
                    </select>
                </div>
                <div class="col-md-4 reminder-for">
                    <label>Lender: </label>
                    <select name="lender_id" class="form-control input-sm lender_id"  style="width:100%;">
                        <option disabled selected value>Select Lender</option>
                        @{{#lenders}}
                        <option value="@{{id}}" @{{#other}}@{{name}}@{{/other}} @{{#isSelected2}}@{{lender_id}}|@{{id}}@{{/isSelected2}}>
                            @{{name}}
                        </option>
                        @{{/lenders}}
                    </select>
                </div>
            </div>
            <div class="form-group reminder-details">
                <div class="col-md-12">
                    <label for="details">Reminder: </label>
                    <textarea class="form-control required" name="details" placeholder="Details..." data-title="Invalid value" data-content="Please input some data" data-placement="bottom">@{{{details}}}</textarea>
                </div>
            </div>
            <div class="form-group reminder-comment" style="display:none;">
                <div class="col-md-12">
                    <label for="comments">Action Taken: </label>
                    <textarea class="form-control" name="comments" placeholder="Comments" rows="3" data-title="Invalid value" data-content="Please input some data" data-placement="bottom"></textarea>
                </div>
            </div>
            <div class="form-actions clearfix">
                <div class="pull-right">
                    @{{^id}}
                    <a class="btn btn-info btn-xs btn-cancel" href="#newReminderNotificationFormWrapper" data-toggle="collapse"><i class="fa fa-times-circle"></i> Cancel</a>
                    @{{/id}}
                    @{{#id}}
                    <a class="btn btn-info btn-xs btn-cancel" href="#reminderForm-@{{id}}" data-toggle="collapse"><i class="fa fa-times-circle"></i> Cancel</a>
                    @{{/id}}
                    <button type="submit" class="btn btn-primary btn-xs btn-save"><i class="fa fa-save"></i> Save</button>
                </div>
            </div>
        </div>
    </form>
</div>
<script>
    @{{^id}}
    $('#starttime-0').timepicker({
    @{{/id}}
    @{{#id}}
    $('#starttime-@{{id}}').timepicker({
    @{{/id}}
        'timeFormat': 'g:i a',
        'step': 15,
        @{{^_startTime}}
        'minTime': '@{{defaulTime}}',
        @{{/_startTime}}
        @{{#_startTime}}
        'minTime': '@{{_startTime}}',
        @{{/_startTime}}
        dynamic: false,
        dropdown: true,
        scrollbar: true,
        // @{{^id}}
        // change: selectStartTime(0)
        // @{{/id}}
        // @{{#id}}
        // change: selectStartTime(@{{id}})
        // @{{/id}}

    });
    @{{^id}}
    $('#timelength-0').timepicker({
    @{{/id}}
    @{{#id}}
    $('#timelength-@{{id}}').timepicker({
    @{{/id}}
        'timeFormat': 'g:i a',
        'step': 15,
        dynamic: false,
        dropdown: true,
        scrollbar: true,
        @{{^id}}
        'minTime': $('#starttime-0').val(),
        @{{/id}}
        @{{#id}}
        'minTime': $('#starttime-@{{id}}').val(),
        @{{/id}}
        @{{^id}}
        'maxTime': moment($('#starttime-0').val(), 'hh:mm A').add(1425, 'minutes').format('hh:mm a'),
        @{{/id}}
        @{{#id}}
        'maxTime': moment($('#starttime-@{{id}}').val(), 'hh:mm A').add(1425, 'minutes').format('hh:mm a'),
        @{{/id}}

        'showDuration': true
    });

    @{{^id}}
    $('#starttime-0').on('change', function() {
    @{{/id}}
    @{{#id}}
    $('#starttime-@{{id}}').on('change', function() {
    @{{/id}}
        @{{^id}}
        $('#timelength-0').timepicker('remove');
        @{{/id}}
        @{{#id}}
        $('#timelength-@{{id}}').timepicker('remove');
        @{{/id}}

        @{{^id}}
        $('#timelength-0').timepicker({
        @{{/id}}
        @{{#id}}
        $('#timelength-@{{id}}').timepicker({
        @{{/id}}
            'timeFormat': 'g:i a',
            'step': 15,
            dynamic: false,
            dropdown: true,
            scrollbar: true,
            @{{^id}}
            'minTime': $('#starttime-0').val(),
            @{{/id}}
            @{{#id}}
            'minTime': $('#starttime-@{{id}}').val(),
            @{{/id}}
            @{{^id}}
            'maxTime': moment($('#starttime-0').val(), 'hh:mm A').add(1425, 'minutes').format('hh:mm a'),
            @{{/id}}
            @{{#id}}
            'maxTime': moment($('#starttime-@{{id}}').val(), 'hh:mm A').add(1425, 'minutes').format('hh:mm a'),
            @{{/id}}
            'showDuration': true
        });
    });
</script>
