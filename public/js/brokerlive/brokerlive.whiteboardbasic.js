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
            window.location.href = URI()
                    .query({ 'from': a.format("DD MMM YYYY"), 'to': b.format("DD MMM YYYY") })
                    .resource();
        }
    );

    $('a.export-link').on('click', function(evt) {
        evt.preventDefault();

        var currentUri = new URI();
        var exportUri = new URI($(this).attr('href'));

        var currentFilter = currentUri.query();

        exportUri.addQuery(URI.parseQuery(currentFilter));

        window.location.href = exportUri.resource();
    });
    $(".popup").popover({ trigger: "hover" , container: 'body'});
} );
