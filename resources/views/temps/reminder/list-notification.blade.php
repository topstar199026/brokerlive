@{{#entries}}
<div class="reminder">
    <div class="reminder-body">
        <div class="notification-bell">
            <span class="text-success">
                <i class="fa fa-bell faa-ring animated"></i>
            </span>
        </div>
        <div class="notification-content">
            <p class="notifcation-header"><strong>Notification:</strong></p>
            <div class="reminder-tags">
                @{{> tagBadges }}
            </div>
        </div>
        <div class="notification-actions" data-id="@{{id}}">
            <a class="btn btn-success btn-circle btn-outline btn-acknowledged" title="Turn Off" data-action="acknowledge"><i class="fa fa-check"></i></a>
        </div>
    </div>
</div>    
@{{/entries}}