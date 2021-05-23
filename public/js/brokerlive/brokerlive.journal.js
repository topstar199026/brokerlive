$.widget('brklive.journalTable', {

    _url: "/data/v1/journal/datatable",
    _filter: {
        fromdate: moment($("#fromDate").val()).format("DD MMM YYYY"),
        todate: moment($("#toDate").val()).format("DD MMM YYYY")
        // 'fromdate': moment().format("DD MMM YYYY"),
        // 'todate': moment().format("DD MMM YYYY")
    },

    _create: function() {
        var _this = this;
        this.datatable =
            this.element.dataTable( {
                "paginationType": "full_numbers",
                "processing": true,
                "serverSide": true,
                "filter": false,
                "info": true,
                "ajax": URI(_this._url).query(_this._filter).resource(),
                "columns": [
                    // { "data": "entrydate" },
                    // { "data": "user.username" },
                    // { "data": "deal.name" },
                    // { "data": "deal.status_description" },
                    // { "data": "notes" }
                    { "data": "_entrydate" },
                    { "data": "username" },
                    { "data": "deal_name" },
                    { "data": "status_description" },
                    { "data": "notes" }
                ],
                "order": [[ 0, "desc" ]]
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
    csv : function (url) {
        var _this = this;
        var filter = _this._filter;
        filter.currentApi = _this._url;
        location.href = URI(url).query(filter).resource();
    },
});

$(document).ready(function(){

    journalTable = $('table.table-journal').journalTable();

    $('select#journal-filter-by').multiselect({
        onChange: function(option, checked, select) {
            journalTable.journalTable('filter', { 'by': $('select#journal-filter-by').val().join(',') });
        }
    });

    $("#reportrange").daterangepicker({
            ranges:{
                "Today" :[moment(),moment()],
                "3 Days" :[moment().subtract(3, 'days'),moment()],
                "5 Days" :[moment().subtract(5, 'days'),moment()],
            },
            opens: 'left',
            format: 'DD MMM YYYY',
            showDropdowns: true,
            minDate:moment().subtract(2, 'weeks'),
            maxDate:moment(),
            startDate: moment($("#fromDate").val()).toDate(),
            endDate: moment($("#toDate").val()).toDate()
        },
        function(a, b){
            var daterange = a.format("DD MMM YYYY")+" - "+b.format("DD MMM YYYY");
            $("#reportrange span").html(daterange);
            journalTable.journalTable('filter', {
                'fromdate': a.format("DD MMM YYYY"),
                'todate': b.format("DD MMM YYYY")
            });
        }
    );
});
