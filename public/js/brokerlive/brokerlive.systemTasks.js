$(function () {
  var DEFAULT_PAGE_LENGTH = 25,
    DEFAULT_DOM = '<"row"<"col-xs-12 col-sm-6"l><"col-xs-12 col-sm-6"f>>'+
      '<"row"<"col-xs-12 col-sm-6"B><"col-xs-12 col-sm-6"p>>tp',
    REFRESH_COUNTDOWN = 5;

  var _this = this,
    _urlTasks = '/data/v1/task',
    tableWrapper = $('#system-task-table-wrapper'),
    dataTable,
    autoRefreshFlag = false,
    refreshInterval,
    currentRefreshCount = REFRESH_COUNTDOWN;
  // load all scribbles
  $.get(_urlTasks, function (response) {
    render(response);
  });

  // binding actions
  bindActions();

  // helpers
  function bindActions() {
    $('#auto-refresh-report').click(function() {
      autoRefreshFlag = $(this).is(':checked');
      autoRefresh();
    });
    tableWrapper.click('.report-refresh-action', function(e) {
      var target = $(e.target),
        name = target.data('name'),
        index = target.data('index');
      target
        .addClass('loading')
        .prop('disabled', true);
      $.get(_urlTasks + '?name=' + name)
        .done(function (response) {
          dataTable.row(index).data(response.data[0]).invalidate();
        })
        .always(function () {
          target
            .removeClass('loading')
            .prop('disabled', false);
        });
    });

    $('#scheduleTaskForm').submit(function (e) {
      var taskName = $('#taskName').val(),
        taskParameter = $('#taskParameter').val();
        $.ajax({
            type: "POST",
            headers: {
            'X-CSRF-Token': $('meta[name="_token"]').attr('content')
            },
            url: _urlTasks,
            data: {
              taskName: taskName,
              taskParameter: taskParameter
            },
            success: function (id) {
              window.location.reload()
            }
        });
      
        e.preventDefault();
        return false;
    });
  }

  function render(response) {
    tableWrapper.empty();
    var table = $('<table class="table table-bordered table-striped table-hover"/>');

    table.appendTo(tableWrapper);
    dataTable = table.DataTable({
      pageLength: DEFAULT_PAGE_LENGTH,
      dom: DEFAULT_DOM,
      bAutoWidth: false,
      order: [[3, 'desc']],
      columns: [
        {
          title: '',
          width: '5%',
          orderable: false,
          render: function (data, type, row, otable) {
            return '<button class="report-refresh-action btn btn-primary btn-sm" title="Refresh"' +
            ' data-name="' + row.name + '"' +
            ' data-index="' + otable.row + '"' +
            + ((autoRefreshFlag || row.status === 'finished') ? ' disabled' : '') +
            '><i class="fa fa-refresh"></i></button>';
          }
        },
        {
          title: 'ID',
          width: '5%',
          data: 'id'
        },
        {
          title: 'Name',
          width: '10%',
          data: 'name'
        },
        {
          title: 'Start time',
          width: '15%',
          data: 'start_time'
        },
        {
          title: 'End time',
          width: '15%',
          data: 'end_time'
        },
        {
          title: 'Status',
          width: '5%',
          data: 'status'
        },
        {
          title: 'Result',
          width: '40%',
          data: 'result'
        },
        {
          title: 'Access Count',
          width: '5%',
          data: 'access_count'
        }
      ],
      data: response.data
    });
  }

  function autoRefresh() {
    clearInterval(refreshInterval);
    if (autoRefreshFlag) {
      $('.report-refresh-action')
        .addClass('loading')
        .prop('disabled', true);
        refreshInterval = setInterval(refreshData.bind(this), 1000);
        $('#report-refresh-status').text('Refresh in ' + REFRESH_COUNTDOWN + ' seconds');
    } else {
      $('#report-refresh-status').text('');
      $('.report-refresh-action')
        .removeClass('loading')
        .prop('disabled', false);
    }
  }

  function refreshData() {
    currentRefreshCount--;
    if (currentRefreshCount == 0) {
      // stop auto interval
      clearInterval(refreshInterval);
      $('#report-refresh-status').text('Refreshing');
      $.get(_urlTasks, function (response) {
        render(response);
        currentRefreshCount = REFRESH_COUNTDOWN;
        autoRefresh();
      });
    } else {
      $('#report-refresh-status').text('Refresh in ' + currentRefreshCount + ' second' + (currentRefreshCount == 1 ? '' : 's'));
    }
  }

  function addNewTask(actionEl) {
    var input = actionEl.closest('.input-group').find('input'),
      categoryId = actionEl.data('categoryId');
    if (input[0].validity.valid) {
      laddaLoading(actionEl, 'start');
      invokeUpdateApi(_urlScribble + '/create', nomaliseScribbleForApi({
        note: input.val(),
        category_id: categoryId > 0 ? categoryId : null
      }), function (scribble, isError) {
        if (!isError) {
          nomaliseScribble(scribble);
          var $list = $('#scribble-list-' + scribble.category_id);
          $list.prepend(_this.templates.render(_this.templates.names.scribble, {
            scribbles: [scribble]
          }));
        }
        laddaLoading(actionEl, 'stop');
        input.val('');
      });
    }
  }
});
