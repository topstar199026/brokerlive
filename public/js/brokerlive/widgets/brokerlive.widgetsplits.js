(function ($) {
  $.fn.widgetSplit = function () {
    this.each(function () {
      var elem = $(this);
      elem.find('a.link-new').on('click', function (e) {
        e.preventDefault();
        var path = $(this).attr('href');
        $.get(
          path,
          {},
          function (data) {
            var formDiv = elem.find('div.ibox-content.form-edit');
            formDiv.html(data);
            bind_splitFormEvents(formDiv.find('form'));
            formDiv.slideDown(
              'slow'
            );
          },
          'html'
        );
      });
      bind_splitListEvents(elem);
    });
    return this;
  };

  function bind_splitListEvents(widget) {
    $(widget).find('div.loan-split > a.link-edit').on('click', function (evt) {
      evt.preventDefault();
      var formDiv = $(this).parents('div.loan-split').find('div.form-edit');
      if (formDiv.is(":visible")) {
        formDiv.slideUp(
          'slow',
          function () { }
        );
      } else {
        formDiv.slideDown(
          'slow',
          function () { }
        );
      }
    });
    widget.find('form').each(function () {
      bind_splitFormEvents(this);
    });
    widget.find(".select2").select2();
  };

  function bind_splitFormEvents(form) {
    $(form).off('submit');
    $(form).on('submit', function (evt) {
      evt.preventDefault();
      $.ajax({
        type: 'POST',
        url: $(this).attr('action'),
        data: $(this).serialize() + '&deal_id=' +
         encodeURIComponent($('#deal-id').val())
        })
        .done(function (data) {
          brokerlive.notification
            .showSuccess('Split loan saved successfully');
        })
        .fail(function (data) {
          brokerlive.notification
            .showError('Error saving split loan');
        })
        .always(function () {
          get_splitList();
          $('div.widget-journal').journal_panel("refresh");
          get_contactList();
          $(form).parents('div.form-edit').slideUp();
          $('html body').animate({ scrollTop: 0 }, 'slow');
        });
    });
    $(form).find('.btn-cancel').off('click');
    $(form).find('.btn-cancel').on('click', function (evt) {
      evt.preventDefault();
      $(form).parents('div.form-edit').slideUp();
    });
    $(form).on('click', '.btn-remove-applicant', function (evt) {
      evt.preventDefault();
      removeApplicant(this);
    });
    $(form).find('.btn-delete').off('click');
    $(form).find('.btn-delete').on('click', function (evt) {
      var _this = this;
      evt.preventDefault();
      bootbox.confirm('Are you sure you want to delete this loan split?', function (result) {
        if (result !== null) {
          if (result === true) {
            $.ajax({
              type: 'GET',
              url: $(_this).attr('href')
            })
              .done(function (data) {
                brokerlive.notification
                  .showSuccess('Split loan saved successfully');
              })
              .fail(function (data) {
                brokerlive.notification
                  .showError('Error deleteing split loan');
              })
              .always(function () {
                get_splitList();
                $(form).parents('div.form-edit').slideUp();
                $('html body').animate({ scrollTop: 0 }, 'slow');
              });
          }
        }
      });
    });
    $(form).find('.autocomplete-name').autocomplete({
      source: '/data/v1/contact/autocomplete',
      minLength: 2,
      select: function (event, ui) {
        $(form).find('input[name=referrer_id]').val(ui.item.data.id);
      }
    })
      .autocomplete("instance")._renderItem = function (ul, item) {
        return $("<li></li>")
          .data("item.autocomplete", item)
          .append($("<div></div>").append(item.label))
          .appendTo(ul);
      };

    $(form).find('.autocomplete-applicant').autocomplete({
      source: '/data/v1/contact/dealcontact?deal_id=' + $("#deal-id").val(),
      minLength: 2,
      select: function (event, ui) {
        $(this).siblings('input[name="new_applicant_id"]').val(ui.item.data.id);
        addApplicant(form, ui.item);
        event.preventDefault();
        return false;
      }
    })
    .autocomplete("instance")._renderItem = function (ul, item) {
      return $("<li></li>")
        .data("item.autocomplete", item)
        .append($("<div></div>").append(item.label))
        .appendTo(ul);
    };

    $(form).find('.datepicker').datepicker(datepickerOptions);
    $(form).find('input[name=whiteboardhide]').on('change', function (evt) {
      toggle_notproceeding_field(form);
    });
    toggle_notproceeding_field(form);

    $(form).find("input[name='commission_trail_applicable']").off('click');
    $("input[name='commission_trail_applicable']", form).click(function (e) {
      if ($(this).is(':checked')) {
        $("input[name='commission_paid_trail']", form)
          .attr('disabled', false);
        return;
      }
      var val = $("input[name='commission_paid_trail']", form).val();
      if (val === '') {
        $("input[name='commission_paid_trail']", form)
          .attr('disabled', true);
        return;
      }

      bootbox.confirm(
        "You have already set the trail paid date. <br/> This operation will remove that date. <br/> Are you sure?",
        function (result) {
          if (result === false) {
            return;
          }
          $("input[name='commission_paid_trail']", form)
            .val('')
            .attr('disabled', true);
          $("input[name='commission_trail_applicable']", form)
            .prop('checked', false);
        });
      return false;
    });
    $(form).find("input[name='commission_value_applicable']").off('click');
    $("input[name='commission_value_applicable']", form).click(function (e) {
      if ($(this).is(':checked')) {
        $("input[name='commission_paid_value']", form)
          .attr('disabled', false);
        return;
      }
      var val = $("input[name='commission_paid_value']", form).val();
      if (val === '') {
        $("input[name='commission_paid_value']", form)
          .attr('disabled', true);
        return;
      }

      bootbox.confirm(
        "You have already set the upfront paid date. <br/> This operation will remove that date. <br/> Are you sure?",
        function (result) {
          if (result === false) {
            return;
          }
          $("input[name='commission_paid_value']", form)
            .val('')
            .attr('disabled', true);
          $("input[name='commission_value_applicable']", form)
            .prop('checked', false);
        });
      return false;
    });
  };

  function get_splitList() {
    var deal_id = $("#deal-id").val();
    var url = '/loansplit/getlist';
    if ($("#deal-temp").val()) {
      url = '/report/loansplitgetlist';
    }
    $.ajax({
      url: url,
      headers: {
        'X-CSRF-Token': $('meta[name="_token"]').attr('content')
      },
      data: {
        'deal_id': deal_id
      },
      type: "POST",
      success: function (msg) {
        $("#loanSplitListDiv").html(msg);
        bind_splitListEvents($("#loanSplitListDiv"));
      }
    });
  };

  function toggle_notproceeding_field(form) {
    if ($(form).find('input[name=whiteboardhide]').is(':checked')) {
      $(form).find("input[name=notproceeding]").prop('disabled', false);
      $(form).find('.notproceeding').datepicker(datepickerOptions);
    }
    else {
      $(form).find("input[name=notproceeding]").prop('disabled', true);
      $(form).find('.notproceeding').datepicker('destroy');
    }
  }

  function addApplicant(form, item) {
    if ($(form).find('.applicant-item.new input[value=' + item.data.id + ']').length > 0) {
      // already in the list
      return;
    }

    $(form).find('.new-applicant-item input[name="new_applicant"]').val('');
    $(form).find('.new-applicant-item input[name="new_applicant_id"]').val('');

    var newApplicant = $(
    '<div class="applicant-item new input-group input-group-sm">' +
      '<input type="hidden" class="applicant-id" name="applicant_ids[]" value="<?= $dealContact->contact_id ?>" />' +
      '<input class="applicant-name form-control input-sm" type="text" readonly />' +
      '<span class="input-group-btn">' +
          '<button type="button" class="btn-remove-applicant btn btn-danger">' +
              '<i class="fa fa-trash" aria-label="delete"></i> Delete' +
          '</button>' +
      '</span>' +
    '</div>');
    newApplicant.find('.applicant-name').val(item.value);
    newApplicant.find('.applicant-id').val(item.data.contact_id);
    $(form).find('.applicant-item:last').after(newApplicant);
  }
  function removeApplicant(applicantItem) {
    $(applicantItem).closest('.applicant-item').remove();
  }
}(jQuery));
