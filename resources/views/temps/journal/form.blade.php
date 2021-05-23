<form class="form-horizontal brokerlive-form form-journal" id="journalForm-@{{id}}" name="journalForm-@{{id}}" method="post" role="form" action="/journal/add">
    @{{#id}}
    <input type="hidden" class="journal_id" name="journal_id" value="@{{id}}" />
    @{{/id}}
    <div class="form-group row">
        <label class="col-sm-2 col-form-label">Note Type</label>
        <div class="col-sm-12 col-md-6">
            <select multiple name="options[]" class="options journal-tag-select" required>
                @{{#availableTypes}}
                <option value="@{{id}}"@{{#isSelected}}@{{typeid}}|@{{id}}@{{/isSelected}}>@{{name}}</option>
                @{{/availableTypes}}
            </select>
        </div>
    </div>
    @{{^id}}
    <div class="form-group row">
        <div class="col-md-12">
            <textarea class="form-control journal-notes" name="notes" placeholder="Notes..." data-title="Invalid value" data-content="Please input some notes" data-placement="bottom"></textarea>
        </div>
    </div>
    @{{/id}}
    <div class="form-actions clearfix">
        <div class="pull-right">
            <button class="btn btn-info btn-xs btn-cancel"><i class="fa fa-times-circle"></i> Cancel</button>
            <button type="submit" class="btn btn-primary btn-xs"><i class="fa fa-save"></i> Save</button>
        </div>
    </div>
</form>
