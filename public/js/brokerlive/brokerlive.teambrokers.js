$(document).ready(function() {
    var report = $('body').report({storage: 'whiteboard.team_brokers.filter'});
    $("#reportrange").daterangepicker({
            startDate: moment($("#startDate").val()).toDate(),
            endDate: moment($("#endDate").val()).toDate(),
            ranges:{
                "12 Months" :[moment().subtract(1, 'year'), moment()],
                "6 Months" :[moment().subtract(6, 'months'), moment()],
                "3 Months" :[moment().subtract(3, 'months'), moment()],
                "This Month" :[moment().startOf("month"), moment()]
            },
            "maxDate": moment(),
            opens: 'left',
            "locale": {
                "format": "DD MMM YYYY"
            },
            showDropdowns: true
        },
        function(start, end, label){
            $("#reportrange span").html(start.format("DD MMM YYYY") + " - " + end.format("DD MMM YYYY"));
            report.report(
                    'filterReport',
                    {
                        range: label,
                        from: start.format("DD MMM YYYY"),
                        to: end.format("DD MMM YYYY")
                    });
        }
    );
    $('select#team-filter-for').multiselect({
        nonSelectedText: 'All Teams',
        onChange: function(option, checked, select) {
            if ($('select#team-filter-for').val() !== null) {
                report.report(
                        'filterReport', 
                        {
                            team: $('select#team-filter-for').val().join(',')
                        });
            } else {
                report.report(
                        'filterReport', 
                        {
                            team: null 
                        });
            }
        }
    });
} );