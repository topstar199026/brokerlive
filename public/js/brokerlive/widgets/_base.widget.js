$.widget('brklive.base', {

  _spinner: '<div class="sk-spinner sk-spinner-three-bounce">' +
    '   <div class="sk-bounce1"></div>' +
    '   <div class="sk-bounce2"></div>' +
    '   <div class="sk-bounce3"></div>' +
    '</div>',

  _isReady: false,

  _initialised: false,

  _create: function () {
    this.showSpinner();

    this._spinnerElem = $(this._spinner);

    this._loadTemplates();

    this._initialised = true;
  },

  showSpinner: function () {
    if (this.contentElem) {
      this.contentElem.prepend(this._spinnerElem);
    } else {
      this.element.prepend(this._spinnerElem);
    }
  },

  hideSpinner: function () {
    this._spinnerElem.remove();
  },

  _loadTemplates: function () {
    // load template file
    this.templates = new brokerlive.Templates(this._templates);
    this.templates.onReady(this._templateReady.bind(this));
  },

  _templateReady: function () {
    this.hideSpinner();
    this._render();
  },

  _render: function () {
    //place holder
  },

  _decorateData: function (data) {
    data = data || {};
    data.isSelected = function () {
      return function (text, render) {
        var splits = render(text).split('|'),
          selectedIds = splits[0].split(','),
          typeId = splits[1];
        if (selectedIds.indexOf(typeId) >= 0) {
          return 'selected';
        }
        return null;
      };
    };

    data.isSelected2 = function () {
        return function (text, render) {
          var splits = render(text).split('|');
            typeId = splits[1];
          if (splits[0] === splits[1]) {
            return 'selected';
          }
          return null;
        };
    };


    data.formatDateTime = function () {
      return function (text, render) {
        var date = render(text);
        return moment(date).format('DD MMM YYYY hh:mm:ss a');
      };
    };

    data.other = function () {
        return function (text, render) {
            var _select = render(text);
            if(_select == 'OTHER') return 'disabled'; else return '';
        };
    };

    data.getAfterTime = function () {
        return function (text, render) {

            var _str = render(text).split('---');
            var start = _str[0] ?? null;
            var length = _str[1] ?? null;

            if(start == null || length == null || start == '' || length == '' ) return null;
            return moment(start, 'hh:mm A').add(length, 'minutes').format('hh:mm a');
        };
    };

    return data;
  },

  _validateTextArea(form) {
    var textElems = form.find('textarea.required:not(.note-codable)'),
      valid = true;

    for (var i = 0; i < textElems.length; i++) {
      var $this = $(textElems[i]),
        textareaId = $this.attr('id'),
        content = tinyMCE.get(textareaId).initialized ?
          tinyMCE.get(textareaId).getContent() :
          $this.val();

      if (content.trim() === '') {
        $this.next()
          .popover({
            title: $this.data('title'),
            content: $this.data('content'),
            placement: $this.data('placement')
          })
          .popover('show');
        valid = false;
      }
    }
    return valid;
  },

  _removeStyle: function (data, entryName, entryAttr) {
    if (entryName in data) {
      for (var i = 0; i < data.entries.length; i++) {
        if (data[entryName][i][entryAttr]) {
          data[entryName][i][entryAttr] = data[entryName][i][entryAttr].replace(/style="[^"]*"/g, "");
        }
      }
    }
  },

  _getCurrentTime: function() {
    var d = new Date();
    var date_format_str = d.getFullYear().toString()+"-"+
      ((d.getMonth()+1).toString().length==2?(d.getMonth()+1).toString():"0"+
      (d.getMonth()+1).toString())+"-"+(d.getDate().toString().length==2?d.getDate().toString():"0"+
      d.getDate().toString())+" "+(d.getHours().toString().length==2?d.getHours().toString():"0"+
      d.getHours().toString())+":"
      +((parseInt(d.getMinutes()/5)*5).toString().length==2?(parseInt(d.getMinutes()/5)*5).toString():"0"+
      (parseInt(d.getMinutes()/5)*5).toString())+":00";
    return date_format_str;
  }
});
