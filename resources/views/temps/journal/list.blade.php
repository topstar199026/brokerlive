<div class="feed-activity-list">
    @{{#entries}}
    <div class="feed-element journal-element" id="@{{id}}">
        <div class="media-body ">
            <div class="journal-item-header clearfix">
                <div class="author">
                    <strong>@{{username}}</strong><br/>
                    <small class="text-muted">@{{#formatDateTime}}@{{entrydate}}@{{/formatDateTime}}</small>
                </div>
                <span class="journal-tags" data-id="@{{id}}">
                    <span class="label label-brokerlive">@{{userRole}}</span>&nbsp;
                    @{{#typename}}
                    <span class="label label-brokerlive">@{{.}}</span>
                    @{{/typename}}
                    @{{^typename}}
                    <span class="label label-brokerlive label-default">Add tags</span>
                    @{{/typename}}
                </span>
            </div>
            <div class="well clearfix">
                @{{{notes}}}
            </div>
        </div>
        <div id="journal-edit-@{{id}}" class="form-edit journal-form journal-edit">
            @{{> form}}
        </div>
    </div>
    @{{/entries}}
</div>
