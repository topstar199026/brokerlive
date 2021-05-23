(function () {
  var DEFAULT_PAGE_LENGTH = 25,
    DEFAULT_DOM = '<"row"<"col-xs-12 col-sm-6"l><"col-xs-12 col-sm-6"f>>'+
      '<"row"<"col-xs-12 col-sm-6"B><"col-xs-12 col-sm-6"p>>tp'
    REFRESH_DELAY = 5; // seconds

  var config = {
    apiPath: '/data/v1/reports/',
    cacheClearParam: '?cache=false',
    statusParam : '?status=true'
  };

  var reports = {
    'NestedReferrer' : displayNestedReferrerTreeReport
  };

  var reportDataTable = {

  };

  var reportTree = {

  };

  var reportData = {

  };

  var statusInterval = {

  };

  $(function() {
    for (var key in reports) {
      var report = key;
      $('#' + report + 'Refresh')
        .data('reportName', report)
        .click(function () {
          // swal({
          //   title: "Refreshing could take a long time. Proceed?",
          //   text: "",
          //   type: "warning",
          //   showCancelButton: true,
          //   confirmButtonColor: "#DD6B55",
          //   confirmButtonText: "Yes",
          //   closeOnConfirm: true
          // }, function () {
            refreshReport($(this).data('reportName'), true);
          // });
          });
    }

    refreshReports(reports);
  });

  function refreshReports(reports) {
    for (var key in reports) {
      refreshReport(key);
    }
  }

  function cleanReport(reportName) {
    if (reportDataTable[reportName]) {
      reportDataTable[reportName].DataTable.destroy();
      $('#' + reportName + 'DataTable').empty();

      reportDataTable[reportName] = {};
    }

    if (reportTree[reportName]) {
      reportTree[reportName].jstree('destroy');
    }
  }

  function refreshReport(reportName, force) {
    cleanReport(reportName);

    $('#' + reportName + 'Refresh').prop('disabled', true);
    $('#' + reportName + 'Loading').show();
    $.get(config.apiPath + reportName + (force === true ? config.cacheClearParam : ''))
    .always(function (resp, textStatus) {
      updateStatus(reportName, resp, textStatus);
    });
  }

  function updateStatus(reportName, resp, textStatus) {
    var statusText = '';
    if (textStatus !== "success") {
      statusText = 'Request failed. Please try again later';
    } else {
      if (resp.status === "running") {
        statusText = 'Report is being refreshed. Please wait...' + (resp.details ? ' Triggered time: ' + resp.details.start_time : '');
        checkStatusInterval(reportName);
      } else if (resp.status === "finished") {
        $('#' + reportName + 'Refresh').prop('disabled', false);
        $('#' + reportName + 'Loading').hide();
        statusText = 'Last refresh: ' + resp.details.start_time

        reportData[reportName] =  {
          originalData: resp.data
        }
        displayReport(reportName);
      }
    }

    $('#' + reportName + 'Status').text(statusText);
  }

  function checkStatusInterval(reportName) {
    statusInterval[reportName] = statusInterval[reportName] || {};
    if (statusInterval[reportName].info) {
      statusInterval[reportName].info.remove();
    }

    statusInterval[reportName].count = REFRESH_DELAY;
    statusInterval[reportName].info = $('<span class="refresh-status">Status will be refreshed in ' + REFRESH_DELAY + ' seconds.</span>');
    $('#' + reportName + 'Status').after(statusInterval[reportName].info);
    clearInterval(statusInterval[reportName].interval);
    statusInterval[reportName].interval = setInterval(function () {
      statusInterval[reportName].count--;
      if (statusInterval[reportName].count == 0) {
        refreshReport(reportName);
        clearInterval(statusInterval[reportName].interval);
        statusInterval[reportName].info.remove();
      } else {
        statusInterval[reportName].info.text(
          'Status will be refreshed in ' +
          statusInterval[reportName].count +
          ' second' + (statusInterval[reportName].count == 1 ? '' : 's') +
          '.');
      }

    }, 1000);
  }

  function displayReport(reportName) {
    reports[reportName](reportName, reportData[reportName].originalData);
  }

  function displayDirectReferrerReport(reportName, data) {
    var table = $('<table class="report-table" />');
    reportDataTable[reportName] = {};
    reportDataTable[reportName].table = table.appendTo('#' + reportName + 'DataTable');

    reportDataTable[reportName].DataTable = table.DataTable({
      pageLength: DEFAULT_PAGE_LENGTH,
      dom: DEFAULT_DOM,
      bAutoWidth: false,
      order: [[1, 'asc']],
      columns: [
        {
          "className":      'details-control',
          "orderable":      false,
          "data":           null,
          "defaultContent": '<i class="fa fa-plus-circle"></i><i class="fa fa-minus-circle"></i>'
        },
        {
          title: 'Referrer',
          width: '30%',
          data:  function (row) {
            return row.referrer === null ? 'N/A' :
              '<a href="contact/edit/' + row.referrer_id + '">' + row.referrer + '</a>';
          }
        },
        {
          title: 'Branch value',
          width: '65%',
          data: function (row) {
            return formatDealValue(row);
          }
        }
      ],
      data: data
    });

    table.on('click', 'td.details-control', function () {
      var detailsTrigger = $(this),
        tr = $(this).closest('tr'),
        row = reportDataTable[reportName].DataTable.row(tr);

      if ( row.child.isShown() ) {
          // This row is already open - close it
          row.child.hide();
          detailsTrigger.removeClass('expanded');
      }
      else {
          // Open this row
          row.child(displaySubDeals(reportName, row.data()));
          row.child.show();
          detailsTrigger.addClass('expanded');
      }
    });
  }

  function displaySubDeals(reportName, data) {
    var table;
    if (reportDataTable[reportName].subDealTable && reportDataTable[reportName].subDealTable[data.referrer_id]) {
      // if the table is cached, use it
      table = reportDataTable[reportName].subDealTable[data.referrer_id];
    } else {
      // if not, generate it and cache it in memory
      table = $('<table class="report-child-table" />');
      reportDataTable[reportName].subDealTable = reportDataTable[reportName].subDealTable || {};
      reportDataTable[reportName].subDealTable[data.referrer_id] = table;

      var dtTable = table.DataTable({
        bAutoWidth: false,
        columns: [
          {
            title: 'Name',
            width: '40%',
            data: function (row) {
              return 'Deal: <a href="deal/index/' + row.id + '">' + row.name + '</a>';
            }
          },
          {
            title: 'Status',
            width: '5%',
            data: 'status'
          },
          {
            title: 'Deal value',
            width: '50%',
            data: function (row) {
              return formatDealValue(row);
            }
          }
        ],
        data: data.deals
      });

      dtTable.rows().every(function () {
        this.child(displaySubSplits(this.data()['splits'])).show();
      });
    }

    return table;
  }

  function displaySubSplits(data) {
    var table = $('<table class="report-child-table bg-white" />');
    table.DataTable({
      bAutoWidth: false,
      columns: [
        {
          title: 'Settlement date',
          width: '10%',
          data: 'settlement_date'
        },
        {
          title: 'Referrer',
          width: '10%',
          data: 'referrer'
        },
        {
          title: 'Status',
          width: '8%',
          data: function (row) {
            if (!row['deal_status']) {
              return 'N/A';
            }
            return row['deal_status'];
          }
        },
        {
          title: 'Upfront value',
          width: '15%',
          data: function (row) {
            return formatMoney(row['upfront']);
          }
        },
        {
          title: 'Trail value',
          width: '15%',
          data: function (row) {
            return formatMoney(row['trail']);
          }
        },
        {
          title: 'Deal',
          width: '42%',
          data: function (row) {
            return '<a href="deal/index/' + row.deal_id + '">' + row.deal_name + '</a>'
          }
        }
      ],
      data: data
    });

    return table;
  }

  function displayReferrer(referrer) {
    return referrer.referrer === null ? 'N/A' : '<a href="contact/edit/' + referrer.referrer_id + '">' + referrer.referrer + '</a>'
  }

  function formatMoney(value) {
    if (value === undefined || value === null) {
      return 'N/A';
    }
    return '$' + value.toFixed().replace(/(\d)(?=(\d{3})+(,|$))/g, '$1,');
  }

  function formatDealValue(value) {
    return '(' + value.split + ' split' + (value.split === 1 ? '' : 's') + ': ' +
        'upfront ' + formatMoney(value.upfront) + ', trail ' + formatMoney(value.trail) + ')';
  }

  function displayNestedReferrerTreeReport(reportName, data) {
    if (!reportData[reportName].filter) {
      // first call after ajax request
      nestedReferrerTreeFilter(reportName);
      // set the default filtered data to referre with split only
      data = reportData[reportName].filter.referrerWithSplit;
    }
    cleanReport(reportName);
    reportTree[reportName] = $('#NestedReferrerTree').jstree({
      core: {
        data: data,
        themes : {
          variant: "large",
          stripes: true,
          responsive: true
        }
      },
      types : {
        'referrer' : {
          'icon' : 'fa fa-user'
        },
        'referrer-plus' : {
          'icon' : 'fa fa-user-plus'
        },
        'split' : {
          'icon' : 'fa fa-dollar'
        }
      },
      plugins : [ 'types', 'search' ]
    });

    $('#NestedReferrerTree')
      .on('select_node.jstree', function (event, data) {
        selectNode(data);
      })
      .on('close_node.jstree', function (event, data) {
        closeNode(reportName, data);
      });
  }

  function nestedReferrerTreeFilter(reportName) {
    $('#' + reportName + 'Filter').off('change', 'input').on('change', 'input', function (e) {
      var type = $(this).val();
      switch (type) {
        case 'full':
          displayNestedReferrerTreeReport(reportName, reportData[reportName].originalData);
          break;
        case 'referrer-with-split':
          displayNestedReferrerTreeReport(reportName, reportData[reportName].filter.referrerWithSplit);
          break;
        case 'referrer-only':
          displayNestedReferrerTreeReport(reportName, reportData[reportName].filter.referrerOnly);
          break;
      }
    });
    $('#' + reportName + 'Search').off('keyup').on('keyup', _.debounce(searchTree, 200));
    $('#' + reportName + 'SearchClear').off('click').on('click', function() {
      $('#' + reportName + 'Search').val('');
      if (reportTree[reportName]) {
        reportTree[reportName].jstree(true).clear_search();
      }
    });

    reportData[reportName].filter = {
      referrerWithSplit : _.reduce(reportData[reportName].originalData, removeTypes(['referrer'], true), []),
      referrerOnly : _.reduce(reportData[reportName].originalData, removeTypes(['split'], false), [])
    };

    function removeTypes(filterTypes, excludeZero) {
      return function removeItem(list, item) {
        var clone = _.clone(item);
        // only keep non-zero split referrer if excludeZero is true
        if (!_.contains(filterTypes, clone.type) && (!excludeZero || (clone.trail + clone.upfront > 0))) {
          clone.children = _.reduce(clone.children, removeItem, []);
          list.push(clone);
        }
        return list;
      }
    }

    function searchTree() {
      if (!reportTree[reportName]) {
        // tree not initialised yet
        return;
      }
      var query = $('#' + reportName + 'Search').val();
      if (query.length < 3) {
        return;
      }
      reportTree[reportName].jstree(true).search(query);
    }
  }

  var prevNode;
  function selectNode(data) {
    if (prevNode) {
      prevNode.$ul.remove();
      if (prevNode.node === data.node) {
        prevNode = undefined;
        return;
      }
    }
    var rawData = data.node.original;
    $ul = $('<ul class="nested-referrer-tree-details"/>');
    if (rawData.deal_id) {
      var $li = $('<li>Deal: </li>');
      $('<a></a>')
        .text(rawData.deal_name)
        .attr('href', '/deal/index/' + rawData.deal_id)
        .appendTo($li);
      $li.appendTo($ul);
    }
    if (rawData.referrer_id) {
      var $li = $('<li>Referrer: </li>');
      $('<a></a>')
        .text(rawData.referrer_name)
        .attr('href', '/contact/edit/' + rawData.referrer_id)
        .appendTo($li);
      $li.appendTo($ul);
    }

    if ($ul.text() != '') {
      $(data.event.currentTarget).after($ul);
    }

    prevNode = {
      node: data.node,
      $ul: $ul
    }
  }

  function closeNode(reportName) {
    if (prevNode && reportTree[reportName].jstree('is_hidden', prevNode.node)) {
      prevNode.$ul.remove();
      prevNode = undefined;
    }
  }

  function exportCSV(data, level) {
    var csv = ['"' + (data.referrer === null ? 'N/A' : data.referrer) + '"', '"' + formatDealValue(data) + '"'].join(',');
    var i;
    csv = padCSVLine(level) + csv;
    // splits
    // csv += "\n" + padCSVLine(level) + ['"Settlement date"', '"Referrer"', '"Status"', '"Upfront value"', '"Trail value"', '"Deal"'].join(',');
    for (i = 0; i < data.splits.length; i++) {
      csv += "\n" + padCSVLine(level) + [
        '"' + data.splits[i].settlement_date + '"',
        '"' + data.splits[i].referrer + '"',
        '"' + data.splits[i].deal_status ? data.splits[i].deal_status : 'N/A' + '"',
        '"' + data.splits[i].upfront + '"',
        '"' + data.splits[i].trail + '"',
        '"' + data.splits[i].deal_name + '"',
      ].join(',');
    }

    for (i = 0; i < data.referrers.length; i++) {
      csv += "\n" + exportCSV(data.referrers[i], level + 1);
    }

    return csv;
  }

  function padCSVLine(count) {
    var pad = '';
    for (i = 0; i < count; i++) {
      // padding
      pad += '" ",';
    }
    return pad;
  }
})();
