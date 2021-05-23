$(document).ready(function() {
    var totalRow = $('<tr class="total-row">\
        <td colspan="6">&nbsp;</td>\
        <td class="total-loan"></td>\
        <td class="total-actual"></td>\
        <td colspan="6">&nbsp;</td>\
    </tr>');

    var hiddenTables = storage.getItem('whiteboard-combined.hiddenTables');
    if (hiddenTables === null) {
        hiddenTables = [];
    }

    resetTables(hiddenTables);

    $('.table-whiteboard').DataTable( {
        "paging":   false,
        "searching":false,
        "info":     false,
        "order":    [],
        "columnDefs": [
            { "visible": false, "targets": 14 }
        ],
        "drawCallback": function(settings) {
            var api = this.api();
            var rows = api.rows( {page:'current'} ).nodes();
            var last = null;
            var subTotal = new Array();
            var groupID = -1;

            api.column(14, {page:'current'} ).data().each( function ( group, i ) {
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
                    if (typeof subTotal[groupID] == 'undefined') {
                        subTotal[groupID] = new Array();
                    }
                    if (typeof subTotal[groupID][colIndex] == 'undefined') {
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

            var totalLoan = $('thead .total-loan', this).attr('data-total');
            var totalActual = $('thead .total-actual', this).attr('data-total');

            totalRow.find('.total-loan').html('<strong>' + totalLoan + '</strong>');
            totalRow.find('.total-actual').html('<strong>' + totalActual + '</strong>');

            $('tbody tr:first', this).before(totalRow.clone());
            $('tbody tr:last', this).after(totalRow.clone());
        }
    } );

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
        function(a, b){
            var daterange = a.format("DD MMM YYYY")+" - "+b.format("DD MMM YYYY");
            $("#reportrange span").html(daterange);
            window.location.href = URI()
                    .query({ 'from': a.format("DD MMM YYYY"), 'to': b.format("DD MMM YYYY") })
                    .resource();
        }
    );

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

        storage.save('whiteboard-combined.hiddenTables', hiddenTables);
    });

    $('a.export-link').on('click', function(evt) {
        evt.preventDefault();

        var currentUri = new URI();
        var exportUri = new URI($(this).attr('href'));

        var currentFilter = currentUri.query();

        exportUri.addQuery(URI.parseQuery(currentFilter));

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
