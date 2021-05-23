$.widget('brklive.reminder_list', $.brklive.base, {
    _templates: {
        main : 'reminder/main',
        list : 'reminder/list-reminder',
        form : 'reminder/form',
        notification : 'reminder/list-notification',
        tagBadges : 'tag/badges'
    },

    _datepickerOptions : {
        format: 'dd M yyyy',
        todayBtn: "linked",
        keyboardNavigation: false,
        forceParse: false,
        calendarWeeks: true,
        autoclose: true,
        startDate: "today"
    },

    _url: {
        reminder: '/data/v1/reminder',
        notification: '/data/v1/deal/notification',
    },

    _filter: "",

    _create : function() {
        if (!this.options.deal_id) {
            throw "Journal widget must be supplied a deal_id";
        }

        this._deal_id = this.options.deal_id;
        this._filter = { 'deal_id': this._deal_id };

        this._super();
    },

    _render: function() {
        // render main layout
        this.element.append(this.templates.render(this._templates.main));
        this.contentElem = this.element.find('.widget-reminder-content');
        this.listElem = this.contentElem.find('.reminder-list');
        this.newReminderNotificationWrapper = this.contentElem.find('.add-form');

        this._initialiseUIEvent();

        this.reloadReminders();
    },

    reloadReminders: function () {
        this._getNotification();
        this._getReminders();
    },

    refresh: function () {
        this.reloadReminders();
        this._resetNewForm();
        // refresh journal widget
        $('.widget-journal').journal_panel('refresh');
    },

    _initialiseUIEvent: function () {
        var _this = this;
        this._initialiseUIWidget(this.element);

        this.element.on('click', 'a', function (e) {
            var $this = $(this),
                action;
            if (action = $this.data('action')) {
                e.preventDefault();

                _this._performAction($this, action);
            }
        });

        this.element.on('submit', 'form', function (e) {
            e.preventDefault();
            var form = $(this),
                formData = form.serialize(),
                action = form.attr('action'),
                actionUrl,
                method = form.attr('method');

            if (action === 'notification') {
                actionUrl = _this._url.notification + '/' + _this._deal_id;
                action = 'New notification';
            } else {
                actionUrl = action && action != '' ? _this._url.reminder + '/' + action : _this._url.reminder;
                action = (action === '' ? 'New' : action) + ' reminder';
            }

            if (_this._validateTextArea(form)) {
                _this.showSpinner();
                _this._disableForm(form, true);
                $.ajax({
                    url: actionUrl,
                    headers: {
                        'X-CSRF-Token': $('meta[name="_token"]').attr('content')
                    },
                    method: method,
                    data: formData,
                    success: function (response) {
                        _this.refresh();
                        brokerlive.notification
                            .showSuccess('Action: "' + action + '" performed successfully');
                    },
                    fail: function () {
                        brokerlive.notification
                            .showError('An error has occurred, please try again later');
                    },
                    complete: function () {
                        _this.hideSpinner();
                        _this._disableForm(form, false);
                    }
                });
            }
        });
    },

    _performAction: function(actionElem, action) {
        var id = actionElem.parent().data('id'),
            formWrapper = $('#reminderForm-' + id),
            form = formWrapper.find('form');

        switch (action) {
            case 'acknowledge':
                this._acknowledgeNotification(id);
                break;
            case 'delete':
                this._deleteReminder(id);
                break;
            case 'complete':
            case 'repeat':
                form.find('.reminder-details').hide().find('textarea').removeClass('required');
                form.find('.reminder-comment').show();

                form.attr('action', action)
                    .attr('method', 'POST');

                if (action === 'repeat') {
                    form.find('.reminder-details').show().find('textarea').addClass('required');
                }

                formWrapper
                    .collapse('show');
                break;
        }
    },

    _resetNewForm: function () {
        this.newReminderNotificationWrapper.find('#form-toggle-reminder').trigger('click');
        this.newReminderNotificationWrapper.collapse('hide');
    },

    _getReminders: function () {
        var _this = this,
            url = URI(this._url.reminder).query(this._filter).resource();

        _this.showSpinner();
        $.get(url)
            .done(function(data) {
                _this._renderReminder(data);
            })
            .always(function () {
                _this.hideSpinner();
            });
    },

    _renderReminder: function(data) {
        //this._removeStyle(data, 'entries', 'details');
        data = this._decorateData(data);
        data.deal_id = this._deal_id;

        this.listElem
            .find('#reminderList')
            .empty()
            .append(this.templates.render(this._templates.list, data, { tagBadges: this._templates.tagBadges, form: this._templates.form }));

        this.newReminderNotificationWrapper.find('#newReminderForm').remove();
        this.newReminderNotificationWrapper
            .append(this.templates.render(this._templates.form, data));

        this._initialiseUIWidget(this.listElem);
        this._initialiseUIWidget(this.newReminderNotificationWrapper);
    },

    _deleteReminder: function (id) {
        var _this = this;
        this.showSpinner();
        $.ajax({
            url: this._url.reminder + '/delete/' + id,
            headers: {
                'X-CSRF-Token': $('meta[name="_token"]').attr('content')
            },
            method: 'DELETE',
            success: function (response) {
                _this.refresh();
                brokerlive.notification
                .showSuccess('Reminder deleted successfully');
            },
            fail: function () {
                brokerlive.notification
                    .showError('An error has occurred, please try again later');
            },
            always: function () {
                _this.hideSpinner();
            }
        })
    },

    _getNotification: function () {
        var _this = this;
        this.showSpinner();
        $.get(this._url.notification + '/' + this._deal_id)
            .done(function (response) {
                if (response.status === 'success' && response.data !== null) {
                    _this._renderNotification({ entries: [response.data] });
                    _this._updateNotificationForm(response.data);
                }
            })
            .always(function() {
                _this.hideSpinner();
            });
    },

    _renderNotification: function(data) {
        data = this._decorateData(data);
        this.listElem
            .find('#notificationList')
            .empty()
            .append(this.templates.render(this._templates.notification, data, { tagBadges: this._templates.tagBadges }));
    },

    _updateNotificationForm: function (notification) {
        var select = this.newReminderNotificationWrapper.find('#notification_for'),
            selector = '',
            whoFor = notification.who_for != null ? notification.who_for.split(',') : [];
        for (var i = 0; i < whoFor.length; i++) {
            selector += '[value=' + whoFor[i] + '],';
        }

        if (selector.length > 0 ) {
            selector = selector.slice(0, -1);
        }

        select.find(selector).prop('selected', true);
        select.trigger('change');
    },

    _acknowledgeNotification: function (id) {
        var _this = this;
        this.showSpinner();
        $.ajax({
            url: _this._url.notification + '/' + _this._deal_id,
            headers: {
                'X-CSRF-Token': $('meta[name="_token"]').attr('content')
            },
            method: 'DELETE',
            success: function () {
                _this.listElem.find('#notificationList').empty();
                brokerlive.notification
                    .showSuccess('Notification acknowledged');
            },
            fail: function () {
                brokerlive.notification
                    .showError('Error: notification cannot be acknowledged at the moment');
            },
            complete: function () {
                _this.hideSpinner();
            }
        })
    },

    _disableForm: function (form, disable) {
        var items = form.find('input,button,a,textarea,[contenteditable]');
        items.prop('disabled', disable);
        if (disable) {
            items.addClass('disabled');
        } else {
            items.removeClass('disabled');
        }
    },

    _decorateData: function (data) {
        data = this._super(data);

        data.day = function() {
            return function(text, render) {
                var date = render(text);
                return moment(date).format('DD');
            };
        };
        data.month = function() {
            return function(text, render) {
                var date = render(text);
                return moment(date).format('MMM');
            };
        };
        data.detailsformat = function () {
            return function(text, render) {
                return render(text).replace(/(?:\r\n|\r|\n)/g, '<br />');
            };
        };

        for (var i in data.entries) {
            data.entries[i].tagMap = {};
            pushToMap(data.entries[i].tagMap, data.entries[i].tags);
            pushToMap(data.entries[i].tagMap, data.entries[i].who_for);
        }

        function pushToMap(map, tags) {
            if (tags) {
                var split = tags.split(',');
                for (var i in split) {
                    map[brokerlive.helpers.camelize(split[i])] = true;
                }
            }
        }

        return data;
    },

    _initialiseUIWidget: function (elem) {
        elem.find('.datepicker').datepicker(this._datepickerOptions);
        elem.find('select').select2({ width: '95%' });
        elem.find('textarea').tinymce(
            brokerlive.config.tinyMCE,{

            }
        );
    }
});
