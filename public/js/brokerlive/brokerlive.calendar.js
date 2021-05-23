$.widget('brklive.reminderTable', {

    _url: "/data/v1/reminder/list",
    _filter: { 'todate': moment().format("DD MMM YYYY"), 'fromdate': moment().format("DD MMM YYYY") },

    _create: function() {
        var _this = this;
        _this.reload(_this._url);
    },

    reload: function (url) {
        var _this = this;
        $.ajax({
            type: 'GET',
            url: url,
            beforeSend: function(){
                $("#external-events").html("");
            },
        })
        .done(function(data){

            var addHtml = new Promise((resolve, reject) => {
                data.length > 0 && data.forEach(
                    (value, key, map) => {
                        //console.log(key + "," + value)
                        $("#external-events").append(
                            "<div class='external-event navy-bg' style='display: flex;'>"+
                                '<input type="hidden" id="reminder_id" value="'+value.id+'">'+
                                '<input type="hidden" id="deal_name" value="'+value.deal_name+'">'+
                                '<input type="hidden" id="deal_id" value="'+value.deal_id+'">'+
                                '<div class="col-3 date" style="padding: 5px; display: grid;">'+
                                    // '<i class="fa fa-briefcase"></i>'+
                                    '<small class="text-navy" style="color: white !important;">'+value._duedate+'</small>'+
                                '</div>'+
                                '<div class="col content no-top-border" style="padding: 5px; display: grid; text-align: center;">'+
                                    // '<div>&nbsp;'+
                                    //     //value.deal_name+
                                    // '</div>'+
                                    '<div>'+
                                        value.deal_name+
                                    '</div>'+
                                    '<div>'+
                                        value.deal_status+
                                    '</div>'+
                                '</div>'+
                               // value.deal_name+
                            "</div>"
                        );
                        if (key === map.length -1) resolve();
                    }
                );
            });

            addHtml.then(()=>{
                $('#external-events div.external-event').each(function() {

                    // store data so the calendar knows to render an event upon drop
                    $(this).data('event', {
                        id: 'reminder_'+$(this).find('#deal_id').val(),
                        title: 'hiihi',//$.trim($(this).text()), // use the element's text as the event title,
                        deal_name: $(this).find('#deal_name').val(),
                        stick: true, // maintain when user navigates (see docs on the renderEvent method),
                        count: '5',
                        type: 2,
                        allDay: false,
                        //editable: false,
                        defaultEventMinutes: 15
                    });

                    // make the event draggable using jQuery UI
                    $(this).draggable({
                        zIndex: 1111999,
                        revert: true,      // will cause the event to go back to its
                        revertDuration: 0,  //  original position after the drag
                        cursonAt: { left: 5,top:5 },
                        helper: 'clone',
                        // start: function( event, ui ) {
                        //     console.log(event,ui)
                        //     $(this).css({
                        //         "position": "absolute",
                        //         // "margin-left": ui.offset.left,
                        //         // "margin-top": ui.offset.top
                        //     });
                        // },
                        // stop: function( event, ui ) {
                        //     $(this).css({
                        //         "position": "relative",
                        //         "margin-left": 0,
                        //         "margin-top": 0
                        //     });
                        // }
                    });

                });
            });


        })
        .fail(function(){
            brokerlive.notification
                .showError('Error retrieving reminder list');
        });
    },

    filter : function (data) {
        var _this = this;
        $.extend(_this._filter, data);
        _this.reload(URI(_this._url).query(_this._filter).resource());
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
    }
});



$(function () {
    var CALENDAR_URL = '/data/v1/calendar';

    var _eventCache = {};

    $('#calendar').fullCalendar({
        header: {
            left: 'prev,next today',
            center: 'title',
            right: 'month,agendaWeek,agendaDay'
        },
        editable: true,
        droppable: true,
        eventDurationEditable: true,
        slotDuration: '00:15:00',
        defaultTimedEventDuration: '00:15:00',
        eventLimit: true,
        businessHours: {
            dow: [ 1, 2, 3, 4, 5 ],
            start: moment($("#_starttimevalue").val(), 'hh:mm A').format('HH:mm').toString(),
            end: moment($("#_endtimevalue").val(), 'hh:mm A').format('HH:mm').toString()
        },
        scrollTime: moment($("#_starttimevalue").val(), 'hh:mm A').format('HH:mm:ss').toString(),
        drop: function() {
            //reminderEvent($(this));
            $(this).remove();
        },

        // eventAllow: function(dropInfo, draggedEvent) {
        //     console.log(dropInfo, draggedEvent)
        //     return true;
        // },

        events: function (start, end, timezone, callback) {
            start = start.format('YYYY-MM-DD');
            end = end.format('YYYY-MM-DD');

            var cacheKey = start + '_' + end;

            if (_eventCache[cacheKey]) {
                callback(_eventCache[cacheKey]);
            }

            $('#calendar').addClass('loading');

            $.get(CALENDAR_URL, {
                start: start,
                end: end
            })
            .done(function (data) {
                _eventCache[cacheKey] = transformEvents(data);
                callback(_eventCache[cacheKey]);
            })
            .always(function () {
                $('#calendar').removeClass('loading');
            });
        },
        eventRender: function (event, el) {
             console.log(event);
            el.empty();
            var count;
            if (moment().diff(event.start, 'days') <= 0) {
                el
                .addClass('overdue')
                .attr('title', 'Overdue');
                count = event.incompleted;
            } else {
                el
                .addClass('completed')
                .attr('title', 'Completed');
                count = event.completed;
            }

            if (count === 0) {
                el.hide();
            }

            el.append(renderTitle(event.id, count, event.type ?? 1, event.title ?? null, event.deal_name?? null));
        },
        eventReceive: function(info) {
            // if (!confirm("is this okay?")) {
            //   info.revert();
            // }
            var view = $('#calendar').fullCalendar('getView');
            if(view.name == "month") info.allDay =true ;
            reminderEvent(info);
        },
        eventDrop: function(info) {
            reminderEvent(info);
        },
        eventResize: function(info) {
            reminderEvent(info);
        }
    });



    function getData(info) {
        var id, allDay, start, end, length = null;
        id = info.id.replace('reminder_', '');
        allDay = info.allDay;
        start = info.start;
        duedate = moment(start).format('MM/DD/YYYY');
        starttime = moment(start).format('HH:mm:ss');
        end = info.end;
        if(!allDay){
            if(end == null) length = 15 ;
            else{
                //length = moment.duration(moment(start).diff(moment(end)));
                length = moment.duration(moment(end).diff(moment(start))).asMinutes();
            }
        }
        return $.ajax({
          url: "/data/v1/calendar/event",
          type: 'GET',
          data: {
              id: id,
              deal_name: info.deal_name,
              allDay: info.allDay,
              start: moment(info.start).format(),
              end: end == null ? end : moment(info.end).format(),
              duedate: duedate,
              starttime: starttime,
              length: length
            //   end: info.end
          }
        });
    };

    async function reminderEvent(info) {
        var view = $('#calendar').fullCalendar('getView');
        //console.log(info, view);
        // var result = await $.ajax({
        //     type: 'GET',
        //     url: "/data/v1/reminder/event",
        //     beforeSend: function(){

        //     },
        // });



        try {
            $('#calendar').fullCalendar('option', {
                editable: false,
                droppable: false,
                durationEditable : false,
                eventDurationEditable: false,
                dragScroll: false,

            });

            //$(".fc-resizer").attr('class', 'newClass');

            $("#css-section").html('<style>'+
            '.fc-allow-mouse-resize .fc-resizer {'+
                'display: none !important;'+
            '}'+
            '</style>')
            //info.editable = false;
            const res = await getData(info);
            $('#calendar').fullCalendar('option', {
                editable: true,
                droppable: true,
                durationEditable : true,
                eventDurationEditable: true,
                dragScroll: true,

            });
            //info.editable = true;
            //$(".newClass").attr('class', 'fc-resizer fc-end-resizer');
            $("#css-section").html('');
            //console.log('res', res);
        } catch (error) {
            //console.error(error);
        }


        // revertFunc();

            //console.log(info.event.title + " end is now " + info.event.end.toISOString());
            // alert(info.event.title + " end is now " + info.event.end.toISOString());
            // if (!confirm("is this okay?")) {
            //   info.revert();
            // }
    }

    function transformEvents(data) {
        var events = [];
        if (data.length > 0) {
            // first item is overdue details
            var start = 0;

            if (data[0].overdue_count) {
                events.push({
                id: 'overdue',
                allDay: true,
                draggable: false,
                editable: false,
                incompleted: data[0].overdue_count,
                start: data[0].duedate
                });
                start = 1;
            }

            for (var i = start; i < data.length; i++) {
                if(data[i].hasOwnProperty('starttime'))
                {
                    events.push(toEvent2(data[i]));
                }else
                    for (var who in data[i].who) {
                        events.push(toEvent(who, data[i]));
                    }
            }
        }
        return events;
    }

    function toEvent(who, day) {
        return {
            id: who,
            title: who,
            allDay: true,
            draggable: false,
            editable: false,
            start: new Date(day.duedate),
            completed: day.who[who].completed,
            incompleted: day.who[who].incompleted
        };
    }

    function toEvent2(data) {
        return {
            id: 'reminder_'+data.id,
            type: 3,
            title: data.name,
            allDay: data.timelength == null ? true : false,
            draggable: true,
            editable: true,
            start: new Date(data.starttime == null ? data.duedate : data.duedate + ' ' + data.starttime),
            end: data.timelength == null ? null : moment(data.duedate + ' ' + data.starttime).add(data.timelength, 'minutes')
        };
    }

    function renderTitle(id, count, type=1,title=null, deal_name=null) {
        var name;
        switch (id) {
        case 'bdo':
            name = 'BDO';
            break;
        case 'broker':
            name = 'Broker';
            break;
        case 'pa':
            name = 'PA';
            break;
        case 'pa2':
            name = 'PA2';
            break;
        case 'pa3':
            name = 'PA3';
            break;
        case 'pa4':
            name = 'PA4';
            break;
        case 'pa5':
            name = 'PA5';
            break;
        case 'prospector':
            name = 'Prospector';
            break;
        case 'overdue':
            name = 'Overdue';
            break;
        }

        switch (type) {
            case 1:
                return '<span class="event-tag text-primary">' + name + '</span><span class="event-count text-info">' + count + '</span>';
                break;
            case 2:
                //name = title;
                return '<div class="fc-content drop-reminder-body"><span class="event-tag text-success">' +
                    deal_name +
                    '</span><span class="event-count text-info"></span></div>'+
                    '<div class="fc-bg drop-reminder-over"></div><div class="fc-resizer fc-end-resizer"></div>';
                break;
            case 3:
                    //name = title;
                return '<div class="fc-content drop-reminder-body"><span class="event-tag text-success">' +
                    title +
                    '</span><span class="event-count text-info"></span></div>'+
                    '<div class="fc-bg drop-reminder-over"></div><div class="fc-resizer fc-end-resizer"></div>';
                break;
        }


    }
});



$(document).ready(function(){



    var reminderList = $('#external-events').reminderTable();
    $('select#reminder-filter-status').multiselect({
        nonSelectedText: 'All statuses',
        allSelectedText: 'All statuses',
        onChange: function(option, checked, select) {
            reminderList.reminderTable('filter', { 'status': $('select#reminder-filter-status').val().join(',') });
        }
    });
    $('select#reminder-filter-lender').multiselect({
        nonSelectedText: 'All lenders',
        allSelectedText: 'All lenders',
        onChange: function(option, checked, select) {
            reminderList.reminderTable('filter', { 'lender': $('select#reminder-filter-lender').val().join(',') });
        }
    });

    $('select#reminder-filter-tag').multiselect({
        nonSelectedText: 'All tags',
        allSelectedText: 'Urgent reminders',
        onChange: function(option, checked, select) {
            reminderList.reminderTable('filter', { 'tags': $('select#reminder-filter-tag').val().join(',') });
        }
    });

    $('select#reminder-filter-for').multiselect({
        nonSelectedText: 'All users',
        allSelectedText: 'All users',
        onChange: function(option, checked, select) {
            reminderList.reminderTable('filter', { 'for': $('select#reminder-filter-for').val().join(',') });
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
            reminderList.reminderTable('filter', { 'todate': b.format("DD MMM YYYY") });
        }
    );


    $('#external-events div.external-event').each(function() {

        // store data so the calendar knows to render an event upon drop
        $(this).data('event', {
            title: $.trim($(this).text()), // use the element's text as the event title
            stick: true // maintain when user navigates (see docs on the renderEvent method)
        });

        // make the event draggable using jQuery UI
        $(this).draggable({
            zIndex: 1111999,
            revert: true,      // will cause the event to go back to its
            revertDuration: 0  //  original position after the drag
        });

    });
});

