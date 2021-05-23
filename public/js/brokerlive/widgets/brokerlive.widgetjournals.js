$.widget('brklive.journal_panel', $.brklive.base, {
  url: {
    journalEntry: '/data/v1/journalEntry?deal_id={deal_id}&page={page}&limit={limit}'
  },

  _templates: {
    main: 'journal/main',
    list: 'journal/list',
    form: 'journal/form',
    end_list: 'journal/end_list'
  },

  pagination: {
    page: 1,
    limit: 10
  },

  journalData: {},

  create: function () {
    if (!this.options.deal_id) {
      throw "Journal widget must be supplied a deal_id";
    }

    this._super();
  },

  _render: function () {
    if (!this.templates.isReady()) {
      setTimeout(this._render.bind(this), 100);
      return;
    }
    this._isReady = true;
    this.refresh();

    this._bindEvents();
  },

  _initUI: function () {
    // scroll
    var journal = storage.getItem('journal.resize');
    var _this = this;
    _this._startInfiniteScroll('.widget-journal-content');

    if (journal) {
      this._resize(journal);
    } else {
      this._resize({
        height: 500
      });
    }

    //select2
    this.contentElem.find(".journal-tag-select").select2({
      width: '100%'
    });

    this.newJournalElem.find('.journal-notes').tinymce(
      brokerlive.config.tinyMCE,{

      }
    );
},

_startInfiniteScroll: function (selector, params) {
  var _this = this;
  var $container = $(selector);
  var infinite = {
    path: _this.url.journalEntry.replace('{deal_id}', _this.options.deal_id)
      .replace('{page}', '{{#}}')
      .replace('{limit}', _this.pagination.limit),
    responseType: 'text',
    status: '.scroll-status',
    elementScroll: '.journal-wrapper',
    history: false
  };

  $.extend(infinite, params);

  $container
    .infiniteScroll(infinite)
    .on('load.infiniteScroll', function (event, response) {
      var data = JSON.parse(response);
      if (data.entries.length === 0) {
        _this._stopInfiniteScroll(selector);
        _this.listElem.append(_this.templates.render(_this.templates.names.end_list));
        _this.listElem.data('lastPageReached', true);
      } else {
        _this.show(data);
      }
    });

  // retain this reference to the current _this widget
  triggerLoadTillScroll.call(_this);

  function triggerLoadTillScroll() {
    // preload the journal entries till scrollbar appear
    if (!this.listElem.data('lastPageReached') && this.listElem[0].scrollHeight <= this.listElem.height()) {
      var _this = this;
      // namespace for safely call `one()`
      $container.one('load.infiniteScroll.init', function () {
        triggerLoadTillScroll.call(_this);
      });
      $container.infiniteScroll('loadNextPage');
    }
  }
},

_stopInfiniteScroll: function (selector) {
  var $container = $(selector);
  $container.infiniteScroll('destroy');
  $container.data('infiniteScroll', null);
  $container.off('load.infiniteScroll');
},

_resize: function (size) {
  var height = size.height - 89;

  if (height < 211) {
    height = 211;
  }

  // set the slimScroll div
  this.listElem.parent().height(height);
  storage.save('journal.resize', {
    height: height
  });
},

_disableResize: function (disable) {
  disable = !!disable;
  if (disable) {
    this.element.resizedHeight = this.element.height();
    this.element.css('height', 'auto');
    this.element.css('overflow', 'hidden');
  } else {
    this.element.resizedHeight = this.element.resizedHeight || this.element.height();
    this.element.height(this.element.resizedHeight);
    this.element.css('overflow', '');
    this._resize({
      height: this.element.resizedHeight
    });
  }

  this.element.resizable({
    disabled: disable
  });
},

_bindEvents: function () {
  var _this = this,
    elem = this.element;

  elem.resizable({
    alsoResize: $('.slimScrollDiv', elem),
    handles: 's',
    minHeight: 300,
    create: function (event, ui) {
      $('<i class="fa fa-ellipsis-h" aria-hidden="true"></i>')
        .appendTo($('div.ui-resizable-s'));
    },
    resize: function (event, ui) {
      _this._resize(ui.size);
    }
  });

  elem.find('a.link-new').on('click', function (e) {
    e.preventDefault();
    _this._show_form();
  });

  elem.on('click', '.journal-tags', function () {
    var id = $(this).data('id');
    $(".journal-form").hide();
    var editForm = $("#journal-edit-" + id).toggle('slow');
    _this.listElem.animate({
      scrollTop: _this.listElem.scrollTop() + editForm.offset().top - _this.listElem.offset().top - 50
    }, 500);
  });

  this._bindForm();
},

_bindForm: function () {
  var _this = this;
  var $element = this.element;

  $element.on('click', '.btn-cancel', function (evt) {
    evt.preventDefault();
    _this._reset_form();
  });

  $element.on('submit', 'form', function (e) {
    e.preventDefault();

    var noteElem = $(this).find(".journal-notes");

    var notes = '';
    if (noteElem.length > 0) {
      notes = noteElem.val();

      if (notes.length === 0) {
        noteElem
          .next()
          .popover({
            title: noteElem.data('title'),
            content: noteElem.data('content'),
            placement: noteElem.data('placement')
          })
          .popover('show');

        return false;
      }
    }

    var $form = $(this);

    var data = {
      "notes": notes,
      "deal_id": $("#deal-id").val(),
      "journal_id": $(this).find(".journal_id").length > 0 ? $(this).find(".journal_id").val() : 0,
      "entrydate": $(this).find(".entrydate").val(),
      "is_broker": $(this).find(".is_broker").is(':checked') ? "Y" : "N",
      "is_assistants": $(this).find(".is_assistants").is(':checked') ? "Y" : "N",
      "is_others": $(this).find(".is_others").is(':checked') ? "Y" : "N",
      "typeid": $(this).find(".options").val()
    };

    var url = _this.url.journalEntry.replace('{deal_id}', _this.options.deal_id)
      .replace('{page}', _this.pagination.page)
      .replace('{limit}', _this.pagination.limit);

    $.ajax({
      url: url,
      headers: {
        'X-CSRF-Token': $('meta[name="_token"]').attr('content')
      },
      data: data,
      type: "POST",
      success: function (msg) {
        if (msg === "success") {
          brokerlive.notification
            .showSuccess('Journal entry saved successfully');
          _this.refreshAfterCreate(data);
        } else {
          brokerlive.notification
            .showError('Journal entry saving failed');
        }
      },
      fail: function (msg) {
        brokerlive.notification
          .showError('Journal entry saving failed');
      },
      complete: function () {
        _this._reset_form();
      }
    });
  });
},

_show_form: function () {
  if (this._formIsVisible) {
    return;
  }
  var _this = this;
  this._formIsVisible = true;
  this.newJournalElem.slideDown('slow', function () {
    // fix the size of parent
    // add the height of the newJournalElement into it
    _this._resize({ height : _this.newJournalElem.parent().height() + _this.newJournalElem.height() });
  });
  this._disableResize(true);
},

_reset_form: function () {
  var _this = this;
  this.newJournalElem.slideUp();

  var $form = this.element.find('.form-edit');

  $form.find(".is_broker").attr('checked', false);
  $form.find(".is_assistants").attr('checked', false);
  $form.find(".is_others").attr('checked', false);
  $form.find(".entrydate").val('');

  this._disableResize(false);
  this._formIsVisible = false;
},

getTypeName: function(typeid) {
  if (typeof this.journalData.availableTypes != 'undefined') {
    for (var index = 0; index < this.journalData.availableTypes.length; index++) {
      if (this.journalData.availableTypes[index].id == typeid) {
        return this.journalData.availableTypes[index].name;
      }
    }
  }
  return '';
},

beforeRefresh: function() {
  this.pagination.page = 1;
  if (!this._isReady) {
    return;
  }
  if (!this.contentElem) {
    this.element.append(this.templates.render(this.templates.names.main));
    this.contentElem = this.element.find('.widget-journal-content');
    this.newJournalElem = this.element.find('.journal-create');
    this.listElem = this.element.find('#journal-entry-list');
  }
  if (this.contentElem.data('infiniteScroll')) {
    this._stopInfiniteScroll('.widget-journal-content');
  }
  this.newJournalElem.empty().hide();
  this.listElem.empty();
},

afterRefresh: function() {
  var _this = this;
  _this._initUI();
  _this._disableResize(false);
},

refreshAfterCreate: function (data) {
  var _this = this;
  _this.beforeRefresh();
  data.entrydate = _this._getCurrentTime();
  if ($.isArray(data.typeid) && data.typeid.length) {
    var typename = [];
    for (var index = 0; index < data.typeid.length; index++) {
      typename.push(_this.getTypeName(data.typeid[index]));
    }
    data.typename = typename.join(', ');
  }

  data.username = $('#username').val();
  data.user_id = $('#user_id').val();
  data.stamp_created = data.entrydate;

  if (!data.journal_id) {
    data.id = String(Date.now());
    _this.journalData.entries.unshift(data);
  } else {
    for (let index = 0; index < _this.journalData.entries.length; index++) {
      if (_this.journalData.entries[index].id == data.journal_id) {
        _this.journalData.entries[index].typeid = data.typeid;
        _this.journalData.entries[index].typename = data.typename;
        break;
      }
    }
  }

  //_this._removeStyle(_this.journalData, "entries", "notes");
  _this.listElem.append(_this.templates.render(_this.templates.names.list, _this._decorateData(_this.journalData), {
    form: _this.templates.names.form
  }));
  _this.newJournalElem.append(_this.templates.render(_this.templates.names.form, {
    availableTypes: _this.journalData.availableTypes
  }));

  _this.afterRefresh();
},

refresh: function () {
  var _this = this;
  _this.beforeRefresh();
  _this.showSpinner();
  var url = _this.url.journalEntry.replace('{deal_id}', _this.options.deal_id)
    .replace('{page}', _this.pagination.page)
    .replace('{limit}', _this.pagination.limit);
  $.get(url)
    .done(function (data) {
      _this.journalData = data;
      //_this._removeStyle(data, 'entries', 'notes');
      _this.listElem.append(_this.templates.render(_this.templates.names.list, _this._decorateData(data), {
        form: _this.templates.names.form
      }));
      _this.newJournalElem.append(_this.templates.render(_this.templates.names.form, {
        availableTypes: data.availableTypes
      }));
      _this.afterRefresh();
    })
    .always(function () {
      _this.hideSpinner();
    });
},

show: function (data) {
  //this._removeStyle(data, 'entries', 'notes');
  var renderedElem = $(this.templates.render(this.templates.names.list, this._decorateData(data), {
    form: this.templates.names.form
  })).appendTo(this.listElem);

  // initialised select2
  renderedElem.find(".journal-tag-select").select2({
    width: '100%'
  });
}
});
