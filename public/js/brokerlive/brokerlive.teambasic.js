$(document).ready(function() {
    var report = $('body').report({storage: 'whiteboard.team_basic.filter'});
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
    $('.table-team').DataTable( {
        "paging":   false,
        "searching":false,
        "info":     false,
        "order":    [],
        "columnDefs": [
            { "visible": true, "targets": 13 }
        ],
        "drawCallback": function(settings) {
            // var totalLoan = $('thead .total-loan', this.context).attr('data-total');
            // var totalActual = $('thead .total-actual', this.context).attr('data-total');
            //
            // totalRow.find('.total-loan').html('<strong>' + totalLoan + '</strong>');
            // totalRow.find('.total-actual').html('<strong>' + totalActual + '</strong>');
            //
            // $('tbody tr:first', this.context).before(totalRow.clone());
            // $('tbody tr:last', this.context).after(totalRow.clone());
        }
    } );
    $('a.export-link').on('click', function(evt) {
        evt.preventDefault();

        var exportUri = new URI($(this).attr('href'))
                .addQuery(report.report('filter'));
        window.location.href = exportUri.resource();
    });
    $(".popup").popover({ trigger: "hover" , container: 'body'});
} );
