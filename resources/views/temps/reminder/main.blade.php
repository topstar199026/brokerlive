<div class="ibox ibox-brokerlive">
    <div class="ibox-title">
        <h5>Reminders / Notifications</h5>
        <div class="tools">
            <a class="btn btn-default btn-xs btn-outline" href="#newReminderNotificationFormWrapper" data-toggle="collapse"><i class="fa fa-plus"></i> Add</a>
        </div>
    </div>
    <div class="ibox-content no-padding widget-reminder-content" >
        <div id="newReminderNotificationFormWrapper" class="add-form collapse">
            <div class="toggle-form">
                <h5>Create new</h5>
                <!-- <div class="btn-group btn-form-toggle" data-toggle="buttons">
                    <label class="btn btn-xs btn-primary btn-reminder active" data-toggle="collapse" data-target=".multi-collapse" aria-controls="newReminderForm newNotificationForm">
                        <input type="radio" name="form-toggle" id="form-toggle-reminder" autocomplete="off" checked/> Reminder
                    </label>
                    <label class="btn btn-xs btn-notification btn-primary" data-toggle="collapse" data-target=".collapsibleGroup.in, #newNotificationForm">
                        <input type="radio" name="form-toggle" id="form-toggle-notification" autocomplete="off"/> Notification
                    </label>
                </div> -->
        <div class="switch" style="display: flex;">
            <div>Reminder&nbsp;&nbsp;</div>
            <div class="onoffswitch">
                <input type="checkbox" name="form-toggle" checked class="onoffswitch-checkbox" id="example1">
                <label class="onoffswitch-label" for="example1" data-toggle="collapse" data-target=".multi-collapse" aria-controls="newReminderForm newNotificationForm">
                    <span class="onoffswitch-inner customOn"></span>
                    <span class="onoffswitch-switch customOff" style="height:20px;"></span>
                </label>
            </div>
            <div>&nbsp;&nbsp;Notification</div>
        </div>
            </div>
            <div id="newNotificationForm" class="notification-form collapsibleGroup collapse multi-collapse">
                <form name="notificationForm" action="notification" method="post" class="form-horizontal brokerlive-form">
                    <div class="form-group">
                        <div class="col-md-4 notification-for">
                            <label for="notification_for">For: </label>
                            <select id="notification_for" multiple="multiple" name="for[]" class="form-control input-sm tags" required>
                                <option value="bdo">BDO</option>
                                <option value="broker">Broker</option>
                                <option value="pa">PA</option>
                                <option value="pa2">PA2</option>
                                <option value="pa3">PA3</option>
                                <option value="pa4">PA4</option>
                                <option value="pa5">PA5</option>
                                <option value="prospector">Prospector</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-actions clearfix">
                        <div class="pull-right">
                            <a class="btn btn-info btn-xs btn-cancel" href="#newReminderNotificationFormWrapper" data-toggle="collapse"><i class="fa fa-times-circle"></i> Cancel</a>
                            <button type="submit" class="btn btn-primary btn-xs btn-save"><i class="fa fa-save"></i> Save</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <div class="reminder-list">
            <div id="notificationList">
                @{{> listNotification}}
            </div>
            <div id="reminderList">
                @{{> listReminder }}
            </div>
        </div>
    </div>
</div>

