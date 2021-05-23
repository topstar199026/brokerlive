$.widget('brklive.reminderTable', {

    _url: "/data/v1/reminder/datatable",
    _filter: { 'todate': moment().format("DD MMM YYYY") },

    _create: function() {
        var _this = this;
        this.datatable =
            this.element.dataTable( {
                "ajax": URI(_this._url).query(_this._filter).resource(),
                "processing": true,
                "serverSide": true,
                "columns": [
                    {
                        "data": "_duedate",
                        "type": "date"
                    },
                    {
                        "data": "name",
                        "render": function(data, type, row, meta) {
                            return Mustache.render(deal_template, row);
                        }
                    },
                    { "data": "deal_status" },
                    {
                        "data": "details",
                        "orderable": false,
                        "render": function(data, type, row, meta) {
                            row.taglabels = _this._buildTagLabels(row.tags);
                            return Mustache.render(details_template, row);
                        }
                    },
                    { "data": "who_for" },
                    {
                        "orderable": false,
                        "render": function(data, type, row, meta) {
                            return Mustache.render(buttons_template, row);
                        }
                    }
                ],
                "createdRow": function ( row, data, index ) {
                    _this._bindRowEvents(row);
                }
            });
    },

    reload: function (url) {
        if (typeof url === "undefined") {
            this.datatable.api().ajax.reload();
        } else {
            this.datatable.api().ajax.url(url).load();
        }
    },

    filter : function (data) {
        var _this = this;
        $.extend(_this._filter, data);
        _this.reload(URI(_this._url).query(_this._filter).resource());
    },

    _getForm: function (reminderRow, reminderId, type) {
        var _this = this;
        $.ajax({
            type: 'GET',
            url: '/reminder/form/' + reminderId
        })
        .done(function(data){
            var row = $('<tr class="row-reminderform" data-reminder="' + reminderId + '" style="display:none;" ><td colspan="6"><div class="col-md-6 col-md-offset-3">' + data + '</div></td></tr>');
            _this._setupForm(row, type);
            reminderRow.after(row);
            _this._bindFormEvents(row.find('form'));
            row.slideDown(
                'slow',
                function(){}
            );
        })
        .fail(function(){
            brokerlive.notification
                .showError('Error retrieving reminder form');
        });
    },

    _setupForm: function (formRow, type) {
        switch (type) {
            case 'complete':
                formRow.find('.reminder-date').hide();
                formRow.find('.reminder-details').hide();
                formRow.find('.reminder-tags').show();
                formRow.find('.reminder-comment').show();
                formRow.find('input[name=action]').val('complete');
                //formRow.find('form').attr('action', '/data/v1/reminder');
                formRow.find('form').attr('action', '/data/v1/reminder/complete');
                break;
            case 'repeat':
                formRow.find('.reminder-date').show();
                formRow.find('.reminder-details').show();
                formRow.find('.reminder-tags').show();
                formRow.find('.reminder-comment').show();
                formRow.find('input[name=action]').val('repeat');
                //formRow.find('form').attr('action', '/data/v1/reminder');
                formRow.find('form').attr('action', '/data/v1/reminder/repeat');
                break;
        };
    },

    _buildTagLabels: function (tags) {
        var tagString = '';
        if (tags) {
            var tag_array = tags.split(",");
            tag_array.forEach(function(tag){
                if (tag.indexOf('Urgent') > -1) {
                    tagString += '<span class="label label-danger">Urgent</span> ';
                }
                if (tag.indexOf('File Work') > -1) {
                    tagString += '<span class="label label-info"><i class="fa fa-folder"></i> File follow up</span> ';
                }
                if (tag.indexOf('Email') > -1) {
                    tagString += '<span class="label label-info"><i class="fa fa-envelope"></i> Email</span> ';
                }
                if (tag.indexOf('Lodge') > -1) {
                    tagString += '<span class="label label-info"><i class="fa fa-external-link-square"></i> Lodge</span> ';
                }
                if (tag.indexOf('Research') > -1) {
                    tagString += '<span class="label label-info"><i class="fa fa-search"></i> Research</span> ';
                }
                if (tag.indexOf('Note') > -1) {
                    tagString += '<span class="label label-info"><i class="fa fa-pencil-square-o"></i> Note</span> ';
                }
                if (tag.indexOf('Sales') > -1) {
                    tagString += '<span class="label label-info"><i class="fa fa-bar-chart-o"></i> Sales Meeting</span> ';
                }
                if (tag.indexOf('Client') > -1) {
                    tagString += '<span class="label label-info"><i class="fa fa-users"></i> Client Meeting</span> ';
                }
                if (tag.indexOf('Prospecting') > -1) {
                    tagString += '<span class="label label-info"><i class="fa fa-eye"></i> Prospecting Call</span> ';
                } else if (tag.indexOf('Database') > -1) {
                    tagString += '<span class="label label-info"><i class="fa fa-database"></i> Database Call</span> ';
                } else if (tag.indexOf('Call') > -1) {
                    tagString += '<span class="label label-info"><i class="fa fa-phone"></i> Call</span> ';
                }
            });
        }
        return tagString;
    },

    _bindRowEvents: function(row){
        var _this = this;
        $(row).find('a.btn-complete').on('click', function(evt){
            evt.preventDefault();
            var reminderId = $(this).attr('data-reminder');
            var formRow = $(this).parents('tbody').find('tr.row-reminderform[data-reminder="' + reminderId + '"]');
            if (formRow.length == 0) {
                _this._getForm($(this).parents('tr'), reminderId, 'complete');
            } else {
                _this._setupForm(formRow, 'complete');
                if (formRow.is(":visible")) {
                    formRow.slideUp(
                            'slow',
                            function(){}
                    );
                } else {
                    formRow.slideDown(
                            'slow',
                            function(){}
                    );
                }
            }
        });
        $(row).find('a.btn-repeat').on('click', function(evt){
            evt.preventDefault();
            var reminderId = $(this).attr('data-reminder');
            var formRow = $(this).parents('tbody').find('tr.row-reminderform[data-reminder="' + reminderId + '"]');
            if (formRow.length == 0) {
                _this._getForm($(this).parents('tr'), reminderId, 'repeat');
            } else {
                _this._setupForm(formRow, 'repeat');
                if (formRow.is(":visible")) {
                    formRow.slideUp(
                            'slow',
                            function(){}
                    );
                } else {
                    formRow.slideDown(
                            'slow',
                            function(){}
                    );
                }
            }
        });
        $(row).find('a.btn-delete').on('click', function(evt){
            evt.preventDefault();
            var reminderId = $(this).attr('data-reminder');
            bootbox.confirm('Are you sure you want to delete this reminder?', function(result){
                 if (result !== null) {
                    if (result === true) {
                        $.ajax({
                            url: '/data/v1/reminder/delete/' + reminderId,
                            headers: {
                                'X-CSRF-Token': $('meta[name="_token"]').attr('content')
                            },
                            type: 'DELETE'
                        })
                        .done(function(data){
                            brokerlive.notification
                                .showSuccess('Reminder deleted successfully');
                        })
                        .fail(function(data){
                            brokerlive.notification
                                .showError('Error deleting reminder');
                        })
                        .always(function(){
                            _this.reload();
                        });
                    }
                }
            });
        });
    },

    _bindFormEvents: function(form) {
        var _this = this;
        $(form).on('submit', function(evt){
            evt.preventDefault();
            $.ajax({
                type: 'POST',
                url: $(this).attr('action'),
                data: $(this).serialize(),
                beforeSend: function (XMLHttpRequest) {
                    XMLHttpRequest.setRequestHeader("X_HTTP_METHOD_OVERRIDE", "PUT");
                }
            })
            .done(function(data){
                brokerlive.notification
                    .showSuccess('Reminder saved successfully');
            })
            .fail(function(data){
                brokerlive.notification
                    .showError('Error saving reminder');
            })
            .always(function(){
                _this.reload();
                $(form).parents('div.reminder-form').slideUp();
            });
        });
        $(form).find('.btn-cancel').on('click', function(evt){
           evt.preventDefault();
           $(form).parents('tr.row-reminderform').slideUp();
        });
        $(form).find('.btn-delete').hide();
        $(form).find('.datepicker').datepicker({
            format: 'dd M yyyy',
            autoclose: true,
            startDate: "today"
        });
        $(form).find('select').select2();
        $(form).find('.reminder-details textarea').tinymce(
            brokerlive.config.tinyMCE,{

            }
        );
        $(form).find('.reminder-comment textarea').tinymce(
            brokerlive.config.tinyMCE,{
                
            }
        );
        $('.note-toolbar .note-insert, .note-toolbar .note-table, .note-toolbar .note-style:first, .note-toolbar .note-para', form).remove();
    }
});

var row_template = '<tr class="row-reminder" data-reminder="{{id}}">' +
        '    <td class="reminder-date">' +
        '        {{duedate}}' +
        '    </td>' +
        '    <td class="reminder-dealname">' +
        '        <a href="/deal/index/{{deal_id}}">{{name}}</a>' +
        '    </td>' +
        '    <td class="reminder-dealstatus">' +
        '        {{description}}' +
        '    </td>' +
        '    <td class="reminder-details">' +
        '        <div class="reminder-tags">' +
        '            {{tags}}' +
        '        </div>' +
        '        <div class="reminder-content">' +
        '            {{{details}}}' +
        '        </div>' +
        '    </td>' +
        '    <td class="reminder-for">' +
        '        {{for}}' +
        '    </td>' +
        '    <td class="reminder-actions">' +
        '        <div class="btn-group">' +
        '            <a class="btn btn-success btn-xs btn-complete" data-reminder="{{id}}" title="Complete"><i class="fa fa-check"></i></a>' +
        '            <a class="btn btn-info btn-xs btn-repeat" data-reminder="{{id}}" title="Repeat" ><i class="fa fa-repeat"></i></a>' +
        '            <a class="btn btn-danger btn-xs btn-delete" data-reminder="{{id}}" title="Delete" ><i class="fa fa-times"></i></a>' +
        '        </div>' +
        '    </td>' +
        '</tr>' +
        '<tr class="row-reminderform" data-reminder="{{id}}" style="display:none;" >' +
        '    <td colspan="5">' +
        '        <div class="col-md-6 col-md-offset-3">' +
        '        </div>' +
        '    </td>' +
        '</tr>';

var deal_template =
        '        <a href="/deal/index/{{deal_id}}">{{deal_name}}</a>';

var details_template =
        '        <div class="reminder-content">' +
        '            {{{details}}}' +
        '        </div>' +
        '        <div class="reminder-tags">' +
        '            {{{taglabels}}}' +
        '        </div>';

var buttons_template =
        '        <div class="btn-group">' +
        '            <a class="btn btn-success btn-xs btn-complete" data-reminder="{{id}}" title="Complete"><i class="fa fa-check"></i></a>' +
        '            <a class="btn btn-info btn-xs btn-repeat" data-reminder="{{id}}" title="Repeat" ><i class="fa fa-repeat"></i></a>' +
        '            <a class="btn btn-danger btn-xs btn-delete" data-reminder="{{id}}" title="Delete" ><i class="fa fa-times"></i></a>' +
        '        </div>';

function buildReminderLabels(tags) {
    var tagString = '';
    if (tags) {
        var tag_array = tags.split(",");
        tag_array.forEach(function(tag){
            if (tag.indexOf('Urgent') > -1) {
                tagString += '<span class="label label-danger">Urgent</span> ';
            }
            if (tag.indexOf('File Work') > -1) {
                tagString += '<span class="label label-info"><i class="fa fa-folder"></i> File follow up</span> ';
            }
            if (tag.indexOf('Email') > -1) {
                tagString += '<span class="label label-info"><i class="fa fa-envelope"></i> Email</span> ';
            }
            // 'Submit' replaced 'Lodge', but didn't update DB
            if (tag.indexOf('Lodge') > -1) {
                tagString += '<span class="label label-info"><i class="fa fa-external-link-square"></i> Submit</span> ';
            }
            if (tag.indexOf('Submit') > -1) {
                tagString += '<span class="label label-info"><i class="fa fa-external-link-square"></i> Submit</span> ';
            }
            if (tag.indexOf('Research') > -1) {
                tagString += '<span class="label label-info"><i class="fa fa-search"></i> Research</span> ';
            }
            if (tag.indexOf('Note') > -1) {
                tagString += '<span class="label label-info"><i class="fa fa-pencil-square-o"></i> Note</span> ';
            }
            if (tag.indexOf('Sales') > -1) {
                tagString += '<span class="label label-info"><i class="fa fa-bar-chart-o"></i> Sales Meeting</span> ';
            }
            if (tag.indexOf('Client') > -1) {
                tagString += '<span class="label label-info"><i class="fa fa-users"></i> Client Meeting</span> ';
            }
            if (tag.indexOf('Prospecting') > -1) {
                tagString += '<span class="label label-info"><i class="fa fa-eye"></i> Prospecting Call</span> ';
            } else if (tag.indexOf('Database') > -1) {
                tagString += '<span class="label label-info"><i class="fa fa-database"></i> Database Call</span> ';
            } else if (tag.indexOf('Call') > -1) {
                tagString += '<span class="label label-info"><i class="fa fa-phone"></i> Call</span> ';
            }
        });
    }
    return tagString;
}

$(document).ready(function(){

    var reminderTable = $('table.table-reminders').reminderTable();
    $('select#reminder-filter-status').multiselect({
        nonSelectedText: 'All statuses',
        allSelectedText: 'All statuses',
        onChange: function(option, checked, select) {
            reminderTable.reminderTable('filter', { 'status': $('select#reminder-filter-status').val().join(',') });
        }
    });
    $('select#reminder-filter-lender').multiselect({
        nonSelectedText: 'All lenders',
        allSelectedText: 'All lenders',
        onChange: function(option, checked, select) {
            reminderTable.reminderTable('filter', { 'lender': $('select#reminder-filter-lender').val().join(',') });
        }
    });

    $('select#reminder-filter-tag').multiselect({
        nonSelectedText: 'All tags',
        allSelectedText: 'Urgent reminders',
        onChange: function(option, checked, select) {
            reminderTable.reminderTable('filter', { 'tags': $('select#reminder-filter-tag').val().join(',') });
        }
    });

    $('select#reminder-filter-for').multiselect({
        nonSelectedText: 'All users',
        allSelectedText: 'All users',
        onChange: function(option, checked, select) {
            reminderTable.reminderTable('filter', { 'for': $('select#reminder-filter-for').val().join(',') });
        }
    });

    $("#reportrange").daterangepicker({
            ranges:{
                "Today" :[moment(),moment()],
                "This Week" :[moment().startOf("isoweek"),moment().endOf("isoweek")],
                "Next 14 Days" :[moment(),moment().add("days",13)],
                "This Month" :[moment().startOf("month"),moment().endOf("month")]
            },
            opens: 'left',
            format: 'DD MMM YYYY',
            showDropdowns: true,
            minDate:moment().startOf('day'),
            maxDate:moment().add("days",29).endOf('day')
        },
        function(a, b){
            var daterange = a.format("DD MMM YYYY")+" - "+b.format("DD MMM YYYY");
            $("#reportrange span").html(daterange);
            reminderTable.reminderTable('filter', { 'todate': b.format("DD MMM YYYY") });
        }
    );
});
