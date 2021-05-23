$(function () {
//TODO: this should be moved to another file
$.fn.selectText = function(){
  var doc = document;
  var element = this[0];
  //console.log(this, element);
  if (doc.body.createTextRange) {
      var range = document.body.createTextRange();
      range.moveToElementText(element);
      range.select();
  } else if (window.getSelection) {
      var selection = window.getSelection();
      var range = document.createRange();
      range.selectNodeContents(element);
      selection.removeAllRanges();
      selection.addRange(range);
  }
};
//*****/

  var _this = this,
    _urlScribble = '/data/v1/scribble',
    _max_category = 5;

  // load template file
  this.templates = new brokerlive.Templates({
    column: '/scribble/scribble-col',
    scribble: '/scribble/scribble'
  });

  // load all scribbles
  $.get(_urlScribble, function (response) {
    render(response);
  });

  // binding actions
  bindActions();

  // helpers
  function bindActions() {

    $('#scribble-wrapper').on('submit', 'form', function (e) {
      e.preventDefault();
    });

    $('#scribble-wrapper').on('click', '[data-action]', function (e) {
      var action = $(this).data('action'),
        isCancel = $(e.target).data('cancel');
      isCancel = isCancel === undefined ? false : isCancel;
      uiAction(action, $(this), isCancel);
    });

    $('#scribble-search-input').keyup(function () {
      var scribbles = $('.scribble'),
        text = $(this).val().trim().toLowerCase();
      scribbles.removeClass('glowing');
      if (text.length > 0) {
        scribbles.filter(function () {
          return $(this).text().toLowerCase().indexOf(text) >= 0;
        }).addClass('glowing');
      }
    });
  }

  function bindColumnEvents(rootEl) {
    rootEl
      .find('.sortable-list')
      .sortable({
        connectWith: '.sortable-list',
        start: function (e, ui) {
          ui.placeholder.height(ui.item.height());
        },
        stop: function (e, ui) {
          var catId = $(this).data('categoryId'),
            itemCatId = ui.item.data('categoryId');
          uiAction('sort', $(this));
        },
        receive: function (event, ui) {
          var catId = $(this).data('categoryId');
          ui.item.data('categoryId', catId);
          uiAction('update-category', ui.item);
          uiAction('sort', $(this));
        }
      });

    // bind key events for editable
    rootEl.find('.editable-container').on('keydown', '.value-container', function (e) {
      if (e.keyCode === 27 || e.keyCode === 13) {
        // escape and enter
        // trigger click on the action
        if (e.keyCode === 27) {
          $(this).text($(this).data('originalText'));
        }
        $(this).siblings('[data-action]').click();
        e.preventDefault();
      }
    });
  }

  function uiAction(action, actionEl, isCancel) {
    switch (action) {
      case 'new-category':
        addNewCategory(actionEl);
        break;
      case 'save-category':
        saveCategory(actionEl, isCancel);
        break;
      case 'inline-edit':
        inlineEdit(actionEl, isCancel);
        break;
      case 'add':
        addNewScribble(actionEl);
        break;
      case 'update':
        updateScribble(actionEl);
        break;
      case 'update-category':
        updateScribbleCategory(actionEl);
        break;
      case 'delete':
        deleteScribble(actionEl);
        break;
      case 'search':
        break;
      case 'sort':
        updateSortOrder(actionEl);
        break;
    }
  }

  function render(response) {
    response=JSON.parse(response);
    if (!_this.templates.isReady()) {
      // template hasn't loaded yet, delay the render;
      setTimeout(render.bind(_this, response), 100);
      return;
    }
    var $listContainer = $('#scribble-list'),
      categories = categorisedScribble(response.data);

    $listContainer.empty();

    for (var key in categories) {
      var category = categories[key],
        categoryColumn = $(_this.templates.render(_this.templates.names.column, {
          category: category
        }));

      if (category.id === 0) {
        // remove edit link
        categoryColumn.find('.scribble-category-header [data-action]').remove();
      }

      $listContainer.append(categoryColumn);

      categoryColumn
        .find('#scribble-list-' + category.id)
        .data('category', category)
        .append(_this.templates.render(_this.templates.names.scribble, {
          scribbles: category.scribbles
        }));
    }

    bindColumnEvents($listContainer);
    checkMaxCategory();
  }

  function categorisedScribble(data) {
    var categories = {
      0: {
        id: 0,
        name: 'Uncategorised'
      }
    };
    for (var i in data) {
    //for(var i=0;i<data.length;i++)  {
      var scribble = data[i],
        category;
      nomaliseScribble(scribble);
      if (categories[scribble.category_id]) {
        category = categories[scribble.category_id];
      } else {
        category = categories[scribble.category_id] = scribble.category;
      }
      delete scribble.category;

      category.scribbles = category.scribbles || [];
      category.scribbles.push(scribble);
    }

    return categories;
  }

  function checkMaxCategory() {
    if ($('#scribble-list').children().length >= _max_category) {
      $('#new-scribble-category').addClass('disabled').prop('disabled', true);
      return true;
    }
    return false;
  }

  function addNewCategory() {
    if (!checkMaxCategory()) {
      var newList = $(_this.templates.render(_this.templates.names.column));
      $('#scribble-list').append(newList);
      newList.find('input').focus();
    }
    checkMaxCategory();
  }

  function laddaLoading(button, method) {
    if (!button.data('lada')) {
      button.ladda();
    }
    button.ladda(method);
  }

  function saveCategory(actionEl) {
    var input = actionEl.closest('.input-group').find('input');
    if (input[0].validity.valid) {
      laddaLoading(actionEl, 'start');
      saveCategoryAPI({
        name: input.val()
      }, function(category, isError){
        if (!isError) {
          console.log('saveCategory', category);
          laddaLoading(actionEl, 'stop');
          input.val('');

          var $listContainer = $('#scribble-list');
          vactionEl.closest('.scribble-list-col').remove();
          var categoryColumn = $(_this.templates.render(_this.templates.names.column, {
            category: category
          }));
          $listContainer.append(categoryColumn);
        }
      });
    }
  }

  function inlineEdit(actionEl, isCancel) {
    var valueContainer = actionEl.siblings('.value-container'),
      entity = actionEl.data('entity'),
      id = actionEl.data('id'),
      field = actionEl.data('field'),
      editable = valueContainer.prop('contenteditable');

    if (isCancel) {
      valueContainer
        .text(valueContainer.data('originalText'))
        .prop('contenteditable', false);
      windows.getSelection().removeAllRanges();
    } else if (editable !== 'true') {
      valueContainer
        .data('originalText', valueContainer.text())
        .prop('contenteditable', true)
        .focus()
        .selectText();
    } else {
      var value = valueContainer.text().trim(),
        origText = valueContainer.data('originalText'),
        pattern = valueContainer.data('pattern');

      if (pattern && !value.match(pattern)) {
        valueContainer
          .popover({
            container: 'body'
          })
          .popover('show')
          .focus();
      } else {
        valueContainer.popover('hide');
        valueContainer.prop('contenteditable', false);

        if (value != origText) {
          saveInlineEdit({
            id: id,
            entity: entity,
            field: field,
            value: value
          }, actionEl);
        }
      }
    }
  }
  function saveInlineEdit(option, elem) {
    var object = {};
    object.id = option.id;
    object[option.field] = option.value;
    var url = _urlScribble;
    if (!url.endsWith('/' + option.entity)) {
      url += '/' + option.entity;
    }
    invokeUpdateApi(url, object, function (newEntity, isError) {
      if (!isError) {
        console.log(option, newEntity);
        if (option.entity === 'scribble') {
          // update the date
          var updatedField = elem.closest('[data-scribble-id]').find('[data-field=updated]');
          updatedField.text(
            moment(newEntity.stamp_updated, 'YYYY-MM-DD HH:mm:ss').format('DD-MM-YYYY hh:mm a')
          );
        }
      }
    });
  }

  function addNewScribble(actionEl) {
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
          //$list.prepend(_this.templates.render(_this.templates.names.scribble, {
          $list.append(_this.templates.render(_this.templates.names.scribble, {
            scribbles: [scribble]
          }));
        }
        laddaLoading(actionEl, 'stop');
        input.val('');
      });
    }
  }

  function updateScribble(actionEl) {
    var id = actionEl.data('scribbleId'),
      categoryId = actionEl.data('categoryId');
    invokeUpdateApi(_urlScribble + '/update', nomaliseScribbleForApi({
      id: id,
      category_id: categoryId
    }));
  }
  function deleteScribble(actionEl) {
    var id = actionEl.data('scribbleId');
    laddaLoading(actionEl, 'start');
    invokeUpdateApi(_urlScribble + '/delete', {
      id: id
    }, function (resp, isError) {
      if (!isError) {
        actionEl.closest('.scribble').remove();
      }
      laddaLoading(actionEl, 'stop');
    });
  }

  function updateSortOrder(actionEl) {
    var idArray = actionEl.sortable('toArray');
    var scribble = [];
    for (var i in idArray) {
      scribble.push(idArray[i].split('_')[1]);
    }
    invokeUpdateApi(_urlScribble + '/sort', {
      method: 'post',
      scribble: scribble
    });
  }

  function updateScribbleCategory(actionEl) {
    var id = actionEl.data('scribbleId'),
      categoryId = actionEl.data('categoryId');
    invokeUpdateApi(_urlScribble + '/update', nomaliseScribbleForApi({
      id: id,
      category_id: categoryId
    }));
  }

  function saveCategoryAPI(obj,cb){
    $.ajax({
      url: _urlScribble + '/category',
      method: 'post',
      headers: {
        'X-CSRF-Token': $('meta[name="_token"]').attr('content')
        },
      data: obj,
      success: function (resp) {
        if (typeof cb === 'function') {
          cb(resp, false);
        }
      },
      fail: function (resp) {
        if (typeof cb === 'function') {
          cb(resp, true);
        }
      }
    });
  }
  function invokeUpdateApi(url, obj, cb) {
    var method = 'get';
    if (obj) {
      if (obj.id) {
        method = 'put';
      } else {
        method = 'post';
      }

      if (obj.method) {
        method = obj.method;
        delete obj.method;
      }
    }

    $.ajax({
      url: url,
      method: method,
      headers: {
        'X-CSRF-Token': $('meta[name="_token"]').attr('content')
        },
      data: obj,
      success: function (resp) {
        if (typeof cb === 'function') {
          cb(resp, false);
        }
      },
      fail: function (resp) {
        if (typeof cb === 'function') {
          cb(resp, true);
        }
      }
    });
  }

  function nomaliseScribbleForApi(scribble) {
    scribble.category_id = scribble.category_id > 0 ? scribble.category_id : null;
    return scribble;
  }

  function nomaliseScribble(scribble) {
    scribble.category_id = scribble.category_id === null ? 0 : scribble.category_id;
    //alert(scribble.category_id);
    scribble.created = moment(scribble.stamp_created, 'YYYY-MM-DD HH:mm:ss').format('DD-MM-YYYY hh:mm a');
    scribble.updated = moment(scribble.stamp_updated, 'YYYY-MM-DD HH:mm:ss').format('DD-MM-YYYY hh:mm a');
    return scribble
  }
});
