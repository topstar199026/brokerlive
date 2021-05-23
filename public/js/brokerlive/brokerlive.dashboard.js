$.widget('brklive.drawchart', {
    url: "/data/v1/dashboard/",
    id: "",

    _create: function () {
        this.url += $(this.element).attr('action');
        this.id = $(this.element).attr('id');
        this.refresh();
    },

    refresh: function () {
        var _this = this;
        $.get(this.url)
            .done(function (response) {
                var dataTable = [
                    ['Lender', 'Settled']
                ];
                response.data.forEach(function (element) {
                    dataTable.push([element.lender, parseInt(element.count)]);
                });

                google.charts.setOnLoadCallback(drawChart);
                // Draw the chart and set the chart values
                function drawChart() {
                    var data = google.visualization.arrayToDataTable(dataTable);
                    // Optional; add a title and set the width and height of the chart
                    var options = {
                        height: 400,
                        legend: {
                            position: 'none'
                        }
                    };
                    var chart = new google.visualization.PieChart(document.getElementById(_this.id));
                    chart.draw(data, options);
                }
            });
    }
});

$(document).ready(function () {
    google.charts.load('current', {
        'packages': ['corechart']
    });
    $('#chart-year-settle').drawchart();
    $('#chart-settle').drawchart();
    $('#chart-active').drawchart();
});
