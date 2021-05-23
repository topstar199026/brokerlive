$.widget('brklive.leadTable', {

    _url: "/data/v1/lead/datatable",
    _filter: {
        'fromdate': $('#fromdate').val(),
        'todate': $('#todate').val(),
        'for': $('select#lead-filter-for').val() != null ? $('select#lead-filter-for').val().join(',') : ''
    },

    _create: function() {
        var _this = this;
        if (storage.getItem('lead.filter') !== null) {
            _this._filter = storage.getItem('lead.filter');
            _this._filter.type = 'leads';
        }
        this.datatable =
            this.element.dataTable( {
                "processing": true,
                "serverSide": true,
                "paginationType": "full_numbers",
		        "filter": false,
                "info": true,
                "ajax": URI(_this._url).query(_this._filter).resource(),
                "order": [],
                "columns": [
                    { "data": "received_date" },
                    {
                        "data": "lead_name",
                        "render": function(data, type, row, meta) {
                            return '<a href="/deal/index/' + row.id + '">' + row.lead_name + '</a>';
                        }
                    },
                    {
                        "data": "contact_number",
                        "render": function (data, type, row, meta) {
                            if (!row.contact_number || row.contact_number.length === 0) {
                                return '';
                            }
                            var phones = row.contact_number.split(', ')
                                phoneHtml = '';

                            for (var i = 0; i < phones.length; i++) {
                                phones[i] = phones[i].trim();
                                if (phones[i].length > 0) {
                                    phoneHtml += '<br><a href="tel:' + phones[i] + '">' + phones[i] + '</a>';
                                }
                            }
                            return phoneHtml.replace('<br>', '');
                        }
                    },
                    {
                        "data": "email",
                        "render": function(data, type, row, meta) {
                            if (row.email !== null) {
                                return '<a href="mailto:' + row.email + '">' + row.email + '</a>';
                            }
                            return '';
                        }
                    },
                    { "data": "notes" },
                    { "data": "referrer" },
                    { "data": "status_description" }
                ]
            });
    },

    _getContact(contactString, type) {
        if (!contactString) {
            return null;
        }
        var contacts = contactString.split(',');
        for (var i = 0; i < contacts.length; i++) {
            if (contacts[i].indexOf(type) === 0) {
                var fields = contacts[i].split('|');
                return {
                    firstname: fields[2],
                    lastname: fields[3],
                    email: fields[4],
                    phonehome: fields[5],
                    phonemobile: fields[6],
                    phonework: fields[7]
                }
            }
        }
        return null;
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
        storage.save('lead.filter', _this._filter);
        _this.reload(URI(_this._url).query(_this._filter).resource());
    },
    csv : function (url) {
        var _this = this;
        var filter = _this._filter;
        filter.currentApi = _this._url;

        location.href = URI(url).query(filter).resource();
    },
});


$(document).ready(function(){

    var leadTable = $('table.table-leads').leadTable(),
        refererSelect = $('#lead-filter-for');

    refererSelect
        .select2({
            width: "100%",
            placeholder: "Choose referer",
            allowClear: true
        })
        .change(function(e) {
            var data = refererSelect.select2('data').map(function (item) { return item.id; });
            leadTable.leadTable('filter', { 'for': data.length > 0 ? data.join(',') : '' });
            $('#selectedRefererCount').text(data.length);
        });

    // prevent dropdown to be closed on click
    refererSelect.parent().on('click', function (e) {
        e.stopPropagation();
    });

    // auto focus on select2
    $('#refererFilterDropdown').parent().on('shown.bs.dropdown', function () {
        refererSelect.select2('open');
    });

    // export link
    $('#exportLeadCSV').click(function (e) {
        e.preventDefault();
        leadTable.leadTable('csv', this.href);
        return false;
    });

    var ranges = {
        "12 Months" :[moment().subtract(1, 'year'), moment()],
        "6 Months" :[moment().subtract(6, 'months'), moment()],
        "3 Months" :[moment().subtract(3, 'months'), moment()],
        "This Month" :[moment().startOf("month"), moment()]
    };
    var range = [ moment(), moment() ];
    var filter = storage.getItem('lead.filter');
    if (filter !== null) {
        if (filter.for) {
            refererSelect.val(filter.for.split(',')).trigger('change');
        }
        if (filter.range) {
            if (filter.range in ranges) {
                range = ranges[filter.range];
            } else {
                range = [ moment(), moment(filter.todate) ];
            }
        }
    }
    $("#reportrange span").html( range[0].format("DD MMM YYYY") + " - " + range[1].format("DD MMM YYYY") );
    $("#reportrange").daterangepicker({
            startDate: $('#fromdate').val(),
            endDate: $('#todate').val(),
            ranges: ranges,
            "maxDate": moment(),
            opens: 'left',
            "locale": {
                "format": "DD MMM YYYY"
            },
            showDropdowns: true
        },
        function(a, b, range){
            var daterange = a.format("DD MMM YYYY")+" - "+b.format("DD MMM YYYY");
            $("#reportrange span").html(daterange);
            leadTable.leadTable('filter', { 'fromdate': a.format("DD MMM YYYY"), 'todate': b.format("DD MMM YYYY"), 'range': range });
        }
    );
});
