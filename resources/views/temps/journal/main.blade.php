<div class="ibox ibox-brokerlive">
    <div class="ibox-title">
        <h5>Journal</h5>
        <div class="tools">
            <a class="btn btn-default btn-xs btn-outline link-new" href="/journal/edit"><i class="fa fa-plus"></i> Add</a>
        </div>
    </div>
    <div class="ibox-content widget-journal-content">
        <div class="journal-form journal-create">
            @{{> form }}
        </div>

        <div id="journal-entry-list" class="journal-wrapper">
            @{{> list}}
        </div>

        <div class="scroll-status" style="display: none">
            <div class="sk-spinner sk-spinner-three-bounce">
                <div class="sk-bounce1"></div>
                <div class="sk-bounce2"></div>
                <div class="sk-bounce3"></div>
            </div>
        </div>
    </div>
</div>

