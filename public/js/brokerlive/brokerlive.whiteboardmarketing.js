$(document).ready(function() {
    $("#reportrange").daterangepicker({
            startDate: moment($("#startDate").val()).toDate(),  
            endDate: moment($("#endDate").val()).toDate(),
            ranges:{
                "This Year" :[moment().startOf('year'), moment()],
                "5 Years" :[moment().startOf('year').subtract(5, 'years'), moment()],
                "10 Years" :[moment().startOf('year').subtract(10, 'years'), moment()]
            },
            "maxDate": moment(),
            opens: 'left',
            "locale": {
                "format": "DD MMM YYYY"
            },
            showDropdowns: true
        },
        function(a, b){
            var daterange = a.format("DD MMM YYYY")+" - "+b.format("DD MMM YYYY");
            $("#reportrange span").html(daterange);
            reload({'from': a.format("DD MMM YYYY"), 'to': b.format("DD MMM YYYY")});
        }
    );
    $('select#lvr-filter-for').multiselect({
        nonSelectedText: 'All LVR',
        onChange: function(option, checked, select) {
            var filterFor = '';
            if ($('select#lvr-filter-for').val() !== null) {
                filterFor = $('select#lvr-filter-for').val().join(',')
                replace_search("lvr", filterFor);
            }
        }
    });
    $('select#lender-filter-for').multiselect({
        nonSelectedText: 'All Lender',
        onChange: function(option, checked, select) {
            var filterFor = '';
            if ($('select#lender-filter-for').val() !== null) {
                filterFor = $('select#lender-filter-for').val().join(',')
                replace_search("lender", filterFor);
            }
        }
    });
    $('select#status-filter-for').multiselect({
        nonSelectedText: 'All Status',
        onChange: function(option, checked, select) {
            var filterFor = '';
            if ($('select#status-filter-for').val() !== null) {
                filterFor = $('select#status-filter-for').val().join(',')
                replace_search("status", filterFor);
            }
        }
    });
    $('select#tags-filter-for').multiselect({
        nonSelectedText: 'All Tags',
        onChange: function(option, checked, select) {
            var filterFor = '';
            if ($('select#tags-filter-for').val() !== null) {
                filterFor = $('select#tags-filter-for').val().join(',')
                replace_search("tags", filterFor);
            }
        }
    });

    $('a.export-link').on('click', function(evt) {
        evt.preventDefault();

        var currentUri = new URI();
        var exportUri = new URI($(this).attr('href'));

        var currentFilter = currentUri.query();

        exportUri.addQuery(URI.parseQuery(currentFilter));

        window.location.href = exportUri.resource();
    });
    $(".popup").popover({ trigger: "hover" , container: 'body'});
    $('.table-whiteboard').DataTable( {
        "paging":   false,
        "searching":false,
        "info":     false,
        "order":    [],
        "columnDefs": [
            { "visible": false, "targets": 8 }
        ],
    } );
} );
function getJsonFromUrl() {
    var query = location.search.substr(1);
    var result = {};
    query.split("&").forEach(function(part) {
        var item = part.split("=");
        if(item.length == 2) {
            result[item[0]] = decodeURIComponent(item[1]);
        }
    });
    console.log(result);
    return result;
}
function serialize(obj) {
    var str = [];
    for(var p in obj)
        if (obj.hasOwnProperty(p)) {
            str.push(p + "=" + obj[p]);
        }
    return str.join("&");
}
function replace_search(key, value) {
    var data = {};
    data[key] = value;
    reload(data);
}
function reload(data) {
    var params = getJsonFromUrl();
    for(var i in data) {
        params[i] = data[i];
    }
    var url = location.href;
    var parts = url.split("?");
    var query = serialize(params);
    window.location.href = parts[0] + ((query == "") ? "" : "?") + query;
}
