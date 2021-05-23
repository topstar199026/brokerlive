$.widget('brklive.pipeline', {
	
    _url: "/data/v1/reminder",
    _filter: {},
    _columns: [],
    _hiddenCols: [],
    
    _range: [moment(), moment()],
    _ranges: {
        "Today" :[moment(),moment()],
        "This Week" :[moment().startOf("isoweek"),moment().endOf("isoweek")],
        "Next 14 Days" :[moment(),moment().add(13,"days")],
        "This Month" :[moment().startOf("month"),moment().endOf("month")]
    },
	
    _create: function() {
        var _this = this;
        
        if (storage.getItem('pipeline.filter') !== null) {
            _this._filter = storage.getItem('pipeline.filter');
            _this._updateFilter();
        }
        
        _this._hiddenCols = storage.getItem('pipeline.hiddenCols');
        if (_this._hiddenCols === null) {
            _this._hiddenCols = [];
        }
        
        _this._columns = $(_this.element).find('div.pipeline-col');
        
        $(_this.element).find(".sortable-list").sortable({
            connectWith: ".connect-list",
            receive: function( event, ui ) {
                $.ajax({
                    url: '/deal/update/' + ui.item.find('.deal-card').attr('data-dealid'),
                    headers: {
                        'X-CSRF-Token': $('meta[name="_token"]').attr('content')
                    },
                    data: {
                        'id': ui.item.find('.deal-card').attr('data-dealid'),
                        'status': ui.item.parents('.pipeline-col').attr('data-status')
                    },
                    type: 'POST',
                    success: function(msg) {
                    }
                });
            }
        }).disableSelection();
        
        _this._getDeals();
       
        $(_this.element).find('.droppable').droppable({
            accept: '.deal', hoverClass: 'story-active',      
            drop: function( event, ui ) {        
                $(this).append($('<div class="deal">'+ui.draggable.html()+'</div>').draggable({stack: '.deal',  revert: 'invalid'}));
                $(this).find('div[rel="tooltip"]').tooltip({
                    container: _this._selector()
                });
                ui.draggable.remove();
                $.ajax({
                    url: '/deal/update/' + ui.draggable.find('.deal-card').attr("data-dealid"),
                    headers: {
                        'X-CSRF-Token': $('meta[name="_token"]').attr('content')
                    },
                    data: {
                        'id': ui.draggable.find('.deal-card').attr("data-dealid"),
                        'status': $(this).attr("data-status")
                    },
                    type: 'POST',
                    success: function(msg) {
                    }
                });
            } 
        });
        $(_this.element).find('.toggle').click(function(evt){
            evt.preventDefault();
            var col = $(this).attr('data-col');
            _this.toggleColumn(col);
        }); 
    },
    
    _selector: function() {
        var selector = this.element.prop("tagName").toLowerCase();
        var id = this.element.attr("id");
        if (id) { 
          selector += "#"+ id;
        }
        var classNames = this.element.attr("class");
        if (classNames) {
          selector += "." + $.trim(classNames).replace(/\s/gi, ".");
        }
        return selector;
    },

    _getDeals: function() {
        
        var _this = this;
                
        _this._columns.each(function() {
            
            var col = $(this);
            var runningAjax = col.data('ajax-request');
            
            // get each status id
            var statusId = col.attr('data-status');
            _this._filter.deal_status = statusId;
            
            // remove current deal cards in the column
            // true means show loading div in col
            _this._clearColumn(col, true);

            if (runningAjax) {
                runningAjax.abort();
            }
                        
            // load each status deal list
            runningAjax = $.ajax({
                type: 'GET',
                url: '/data/v1/deal?new=1',
                data: _this._filter
            })
            .done(function(data){
                _this._loadDealCards(col, data.data);
                $('.ibox-content').height('auto');
            });

            col.data('ajax-request', runningAjax);
        });
        
    },
    
    _clearColumn: function(col, showLoading) {
        var colBody = col.find('ul');
        
        if (showLoading) {
            colBody.html('<div class="loading">Loading...</div>');
        } else {
            colBody.empty();
        }
        
        col.find('span.label-info').html('0');
    },
    
    _loadDealCards: function(col, deals) {
        var html = '';
        var _this = this;
        $.each(deals, function(index, value){
            var dealClass = '';
            if (value.first_reminder)
            {
                var startDate = moment.unix(value.first_reminder.duedate);
                var diff = moment().diff(startDate, 'days', true);
                if (diff < 0)
                {
                    dealClass = 'info-element';
                }
                else if (diff > 0 && diff < 1)
                {
                    // Current Date
                    dealClass = 'success-element';
                }
                else if(diff > 1 && diff < 2)
                {
                    // Over Due by less than 3 days
                    dealClass = 'warning-element';
                }
                else
                {
                    dealClass = 'danger-element';
                }
                if (value.first_reminder !== null) 
                {
                    if (value.first_reminder.tags !== null) 
                    {
                        if (value.first_reminder.tags.indexOf('Urgent') > -1 && value.notify == 0)
                        {
                            dealClass += " pulsate-danger-element";
                        }
                    }
                }
            }
            if (value.notify > 0) {
                dealClass += " pulsate-element";
            }
            value.dealClass = dealClass;
            value.icons = '';
            if (value.first_reminder) {
                value.first_reminder.duedate = moment.unix(value.first_reminder.duedate).format('DD-MM-YYYY');
                value.icons = _this._buildTagIcons(value.first_reminder.tags);
            }
            
            html += Mustache.render(deal_template, value);
        }); 
        var colBody = col.find('ul');
        colBody.html(html);
        
        col.find('span.label-info').html(col.find('li').length);
        
        $(this.element).find('div[rel="tooltip"]').tooltip({
            container: _this._selector()
        });
        $(this.element).find('.deal').draggable({
            stack: '.deal',  
            revert: 'invalid',
            start: function() {
                $(this).find('div[rel="tooltip"]').tooltip('destroy');
            }
        });  
    },
    
    _buildTagIcons : function (tags) {
        var tagString = '';
        if (tags) {
            var tag_array = tags.split(",");
            tag_array.forEach(function(tag){
                if (tag.indexOf('File Work') > -1) {
                    tagString += '<i class="fa fa-folder"></i> ';
                }
                if (tag.indexOf('Email') > -1) {
                    tagString += '<i class="fa fa-envelope"></i> ';
                }
                // 'Submit' replaced 'Lodge', but didn't update DB
                if (tag.indexOf('Lodge') > -1) {
                    tagString += '<i class="fa fa-external-link-square"></i> ';
                }
                if (tag.indexOf('Submit') > -1) {
                    tagString += '<i class="fa fa-external-link-square"></i> ';
                }
                //-----------
                if (tag.indexOf('Research') > -1) {
                    tagString += '<i class="fa fa-search"></i> ';
                }
                if (tag.indexOf('Note') > -1) {
                    tagString += '<i class="fa fa-pencil-square-o"></i> ';
                }
                if (tag.indexOf('Sales') > -1) {
                    tagString += '<i class="fa fa-bar-chart-o"></i> ';
                }
                if (tag.indexOf('Client') > -1) {
                    tagString += '<i class="fa fa-users"></i> ';
                }
                if (tag.indexOf('Prospecting') > -1) {
                    tagString += '<i class="fa fa-eye"></i> ';
                } else if (tag.indexOf('Database') > -1) {
                    tagString += '<i class="fa fa-database"></i> ';
                } else if (tag.indexOf('Call') > -1) {
                    tagString += '<i class="fa fa-phone"></i> ';
                }
            });
        }
        return tagString;
    },
    
    _updateFilter : function() {
        var _this = this;
        if (_this._filter === null) {
            _this._filter = {};
        }
        if (_this._filter.range) {
            if (_this._filter.range in _this._ranges) {
                _this._range = _this._ranges[_this._filter.range];
            } else {
                _this._range = [ moment(), moment(_this._filter.todate) ];
            }
        }
        _this._filter.todate = _this._range[1].format("DD MMM YYYY");
    },
    
    range : function() {
        return this._range;
    },
    
    ranges : function () {
        return this._ranges;
    },
    
    reset : function () {
        this._filter = {};
        this._getDeals();
    },

    filter : function (data) {
        var _this = this;
        $.extend(_this._filter, data);
        storage.save('pipeline.filter', _this._filter);
        this._getDeals();
    },
	
    toggleColumn: function(colIndex) {
        var _this = this;
        var col = _this.element.find('div.pipeline-col:nth-child(' + colIndex + ')');
        if (col.hasClass('collapsed')) {
            var position = _this._hiddenCols.indexOf(colIndex);
            if (~position) { _this._hiddenCols.splice(position, 1); }
            _this.showColumn(col);
        } else { 
            var position = _this._hiddenCols.indexOf(colIndex);
            if (!~position) { _this._hiddenCols.push(colIndex); }
            _this.hideColumn(col);
        }
        storage.save('pipeline.hiddenCols', _this._hiddenCols);
    },
    
    visibleColWidth: function () {
        var marginWidth = 0.2 * this._columns.length;
        var totalWidth = ((97 - marginWidth) - (1.5 * this._hiddenCols.length));
        var colWidth = Math.floor((totalWidth/(this._columns.length - this._hiddenCols.length))*100)/100;
        return colWidth;
    },
    
    showColumn: function(col) {
        var colWidth = this.visibleColWidth();
        col.animate(
                { width:colWidth+'%' }, 
                { 
                    duration: 500, 
                    queue: false, 
                    complete: function() {
                        col.removeClass('collapsed');
                    }
                });
        this.element
            .find('div.pipeline-col:not(.collapsed)')
            .animate({width:colWidth+'%'}, { duration: 500, queue: false });
    },
    
    hideColumn: function(col) {
        var colWidth = this.visibleColWidth();
        col.addClass('collapsed');
        col.animate({width:'1%'}, { duration: 500, queue: false });
        this.element
            .find('div.pipeline-col:not(.collapsed)')
            .animate({width:colWidth+'%'}, { duration: 500, queue: false });
    },
    
    resetColumns: function() {
        var _this = this;
        var hiddenCols = storage.getItem('pipeline.hiddenCols');
        if (hiddenCols !== null) {
            hiddenCols.forEach(function(colIndex) {
               _this.toggleColumn(colIndex);
            });
        }
    },

});

var deal_template = 
        '<li class="pipeline-deal {{dealClass}}">' +
        '    <div class="deal-card" data-dealid="{{id}}" ' +
        '         {{#first_reminder}}' +
        '         rel="tooltip" data-toggle="tooltip" data-trigger="hover" data-placement="auto right" data-html="true" ' +
        '         data-title="<div class=\'reminder-date\'>{{duedate}}</span>' +
        '         <span class=\'reminder-details\'>{{details}}</span>"' +
        '         {{/first_reminder}}' +
        '         >' +
        '        <a href="/deal/index/{{id}}">' +
        '        {{#first_reminder}}' +
        '            <span class="reminder-icons pull-right">' +
        '                {{{icons}}}' +
        '            </span>' +
        '        {{/first_reminder}}' +
        '            <span class="deal-card-title">{{name}}</span>' +
        '        </a>' +
        '     </div>' +
        '</li>';

$(document).ready(function(){
    if($('.pipeline').length > 0) {
        var pipeline = $('.pipeline').pipeline();
        pipeline.pipeline('resetColumns');

        var filterBroker = $('select#reminder-filter-broker').multiselect({
            nonSelectedText: 'All brokers',
            onChange: function(option, checked, select) {
                var filterFor = '';
                if ($('select#reminder-filter-broker').val() !== null) {
                    filterFor = $('select#reminder-filter-broker').val().join(',')
                }
                pipeline.pipeline('filter', { 'broker': filterFor });
            }
        });

        var filterFor = $('select#reminder-filter-for').multiselect({
            onChange: function(option, checked, select) {
                var filterFor = '';
                if ($('select#reminder-filter-for').val() !== null) {
                    filterFor = $('select#reminder-filter-for').val().join(',')
                }
                pipeline.pipeline('filter', { 'for': filterFor });
            }
        });
        var filter = storage.getItem('pipeline.filter');
        if (filter !== null)
        {
            if ((filter.for !== undefined) && (filter.for !== null))
            {
                filterFor.multiselect('select', filter.for.split(","));
            }
            if ((filter.broker !== undefined) && (filter.broker !== null))
            {
                filterBroker.multiselect('select', filter.broker.split(","));
            }
        }

        $("#reportrange span").html( pipeline.pipeline('range')[0].format("DD MMM YYYY") + " - " + pipeline.pipeline('range')[1].format("DD MMM YYYY") );
        $("#reportrange").daterangepicker({
                "ranges": pipeline.pipeline('ranges'),
                "opens": 'left',
                "locale": {
                    "format": "DD MMM YYYY"
                },
                showDropdowns: true,
                minDate: moment().startOf('day'),
                startDate: pipeline.pipeline('range')[0],
                endDate: pipeline.pipeline('range')[1]
            },
            function(start, end, range){
                var daterange = start.format("DD MMM YYYY") + " - " + end.format("DD MMM YYYY");
                $("#reportrange span").html(daterange);
                pipeline.pipeline( 'filter', { 'todate': end.format("DD MMM YYYY"), 'range': range } );
            }
        );
    }
});
