$(document).ready(function() {
    var totalRow = $('<tr class="total-row">\
        <td colspan="6">&nbsp;</td>\
        <td class="total-loan"></td>\
        <td class="total-actual"></td>\
        <td colspan="5">&nbsp;</td>\
    </tr>');
    
    var hiddenTables = storage.getItem('team-combined.hiddenTables');
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
            var last = null;
            var subTotal = new Array();
            var groupID = -1;
            
            var totalLoan = $('thead .total-loan', this).attr('data-total');
            var totalActual = $('thead .total-actual', this).attr('data-total');

            totalRow.find('.total-loan').html('<strong>' + totalLoan + '</strong>');
            totalRow.find('.total-actual').html('<strong>' + totalActual + '</strong>');
            
            $(rows).first().before(totalRow.clone());
            $(rows).last().after(totalRow.clone());

            api.column(12, { page:'current' } ).data().each( function ( group, i ) {
                if ( last !== group ) {
                    groupID++;
                    
                    $(rows).eq( i ).before(
                        '<tr class="group">\
                            <td colspan="6">' + group + '</td>\
                            <td class="subtotal-loan"></td>\
                            <td class="subtotal-actual"></td>\
                            <td colspan="5">&nbsp;</td>\
                        </tr>'
                    );
                    last = group;
                }

                var val = api.row(api.row($(rows).eq(i)).index()).data(); //Current order index
                $.each(val, function (colIndex, colValue) {
                    if (typeof subTotal[groupID] === 'undefined') {
                        subTotal[groupID] = new Array();
                    }
                    if (typeof subTotal[groupID][colIndex] === 'undefined') {
                        subTotal[groupID][colIndex] = 0;
                    }
                    var unformattedVal = colValue.replace(/[^\d\.-]/g,'');
                    value = colValue ? parseFloat(unformattedVal) : 0;
                    subTotal[groupID][colIndex] += value;
                });
            });

            this.find('.subtotal-loan').each(function (i, v) {
                var rowCount = $(this).nextUntil('.subtotal-loan').length;
                var subTotalInfo = "";
                subTotalInfo += subTotal[i][6].toFixed(0).replace(/./g, function(c, i, a) {
                    return i && c !== "." && ((a.length - i) % 3 === 0) ? ',' + c : c;
                });
                $(this).html('<strong>$' + subTotalInfo + '</strong>');
            });
            this.find('.subtotal-actual').each(function (i, v) {
                var rowCount = $(this).nextUntil('.subtotal-actual').length;
                var subTotalInfo = "";
                subTotalInfo += subTotal[i][7].toFixed(0).replace(/./g, function(c, i, a) {
                    return i && c !== "." && ((a.length - i) % 3 === 0) ? ',' + c : c;
                });
                $(this).html('<strong>$' + subTotalInfo + '</strong>');
            });
            
            //$('tbody tr:first', this.context).before(totalRow.clone());
            //$('tbody tr:last', this.context).after(totalRow.clone());
        }
    } );
    
    var report = $('body').report({storage: 'whiteboard.team_combined.filter'});

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
    
        storage.save('team-combined.hiddenTables', hiddenTables);
    });

    $('a.export-link').on('click', function(evt) {
        evt.preventDefault();

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