$(document).ready(function() {
    var totalRow = $('<tr class="total-row">\
        <td colspan="6">&nbsp;</td>\
        <td class="total-loan"></td>\
        <td class="total-actual"></td>\
        <td colspan="5">&nbsp;</td>\
    </tr>');
    
    var report = $('body').report({storage: 'whiteboard.team_pipeline.filter'});
    
    var hiddenTables = storage.getItem('team.hiddenTables');
    if (hiddenTables === null) {
        hiddenTables = [];
    }
    
    resetTables(hiddenTables);

    $('.table-team').DataTable( {
        "paging":   false,
        "searching":false,
        "info":     false,
        "order":    [],
        "columnDefs": [
            { "visible": false, "targets": 12 }
        ],
        "drawCallback": function(settings) {
            var api = this.api();
            var rows = api.rows( { page:'current' } ).nodes();
            
            var totalLoan = $('thead .total-loan', this).attr('data-total');
            var totalActual = $('thead .total-actual', this).attr('data-total');

            totalRow.find('.total-loan').html('<strong>' + totalLoan + '</strong>');
            totalRow.find('.total-actual').html('<strong>' + totalActual + '</strong>');

            $(rows).first().before(totalRow.clone());
            $(rows).last().after(totalRow.clone());
        }
    } );
    
    $("#reportrange").daterangepicker({
            startDate: moment($("#startDate").val()).toDate(),
            endDate: moment($("#endDate").val()).toDate(),
            ranges:{
                "10 Years" :[moment().subtract(10, 'years'), moment()],
                "5 Years" :[moment().subtract(5, 'years'), moment()],
                "1 Year" :[moment().subtract(1, 'year'), moment()],
                "This Year" :[moment().startOf("year"), moment()]
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
    
    $('.collapse-link').unbind('click').click( function() {
        var ibox = $(this).closest('div.ibox');
        var button = $(this).find('i');
        var content = ibox.find('div.ibox-content');
        var tableId = $(this).attr('data-tableid');

        if (content.is(':visible')) {
            var position = hiddenTables.indexOf(tableId);
            if (!~position) { hiddenTables.push(tableId); }
        } else { 
            var position = hiddenTables.indexOf(tableId);
            if (~position) { hiddenTables.splice(position, 1); }
        }
        
        content.slideToggle(200);
        button.toggleClass('fa-chevron-up').toggleClass('fa-chevron-down');
        ibox.toggleClass('').toggleClass('border-bottom');
        setTimeout(function () {
            ibox.resize();
            ibox.find('[id^=map-]').resize();
        }, 50);
    
        storage.save('team.hiddenTables', hiddenTables);
    });

    $('a.export-link').on('click', function(evt) {
        evt.preventDefault();
        console.log(report)
        console.log(report.report('filter'))
        var exportUri = new URI($(this).attr('href'))
                .addQuery(report.report('filter'));
        window.location.href = exportUri.resource();
    });
} );

function resetTables(tables) {
    jQuery.each(tables, function(index, value) {
        var collapse = $('.collapse-link[data-tableid=' + value + ']'); 
        var ibox = collapse.closest('div.ibox');
        var button = collapse.find('i');
        var content = ibox.find('div.ibox-content');
        
        content.slideToggle(200);
        button.toggleClass('fa-chevron-up').toggleClass('fa-chevron-down');
        ibox.toggleClass('').toggleClass('border-bottom');
    });
}