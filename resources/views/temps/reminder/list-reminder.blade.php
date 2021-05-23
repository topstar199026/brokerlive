@{{#entries}}
<div class="reminder">
    <form>
        <input type="hidden" name="id" value="@{{id}}" />
        <div class="reminder-body">
            <div class="reminder-date">
                <span class="day">@{{#day}}@{{duedate}}@{{/day}}</span>
                @{{#month}}@{{duedate}}@{{/month}}
            </div>
            <div class="reminder-content">
                @{{{details}}}
                <div class="reminder-tags">
                    @{{> tagBadges }}
                </div>
            </div>
            <div class="reminder-actions" data-id="@{{id}}">
                <a class="btn btn-success btn-circle btn-outline btn-complete" title="Complete" data-action="complete"><i class="fa fa-check"></i></a>
                <a class="btn btn-info btn-circle btn-outline btn-repeat" title="Repeat" data-action="repeat"><i class="fa fa-repeat"></i></a>
                <a class="btn btn-danger btn-circle btn-outline btn-delete" title="Delete" data-action="delete"><i class="fa fa-times"></i></a>
            </div>
        </div>
    </form>
    @{{> form}}
</div>
@{{/entries}}
