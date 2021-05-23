var datepickerOptions = {
  format: 'dd M yyyy',
  todayBtn: "linked",
  keyboardNavigation: false,
  forceParse: false,
  calendarWeeks: true,
  autoclose: true
};

function formatBytes(bytes, decimals) {
  if (bytes == 0) return '0 Bytes';
  var k = 1000,
    dm = decimals || 2,
    sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'],
    i = Math.floor(Math.log(bytes) / Math.log(k));
  return parseFloat((bytes / Math.pow(k, i)).toFixed(dm)) + ' ' + sizes[i];
}

function bind_fileListEvents(widget) {
  $(widget).find('a.link-delete').on('click', function (evt) {
    var _this = this;
    evt.preventDefault();
    bootbox.confirm('Are you sure you want to delete file?', function (result) {
      if (result !== null) {
        if (result === true) {
          $.ajax({
              url: $(_this).attr('href'),
              type: 'GET'
            })
            .done(function (data) {
              brokerlive.notification
                .showSuccess('File deleted');
            })
            .fail(function (data) {
              brokerlive.notification
                .showError('Error deleting file');
            })
            .always(function () {
              get_fileList();
            });
        }
      }
    });
  });
};
$(document).ready(function () {

  setTimeout(function () {
    $('select.tags').select2({
      width: '95%'
    });
  }, 0);
  //$('.datepicker').datepicker(datepickerOptions);
  //$('.selectpicker').selectpicker();

  $(".onblur").blur(function () {

    var firstname = $("#firstname").val();
    var lastname = $("#lastname").val();

    if (firstname && lastname) {
      var res = firstname.charAt(0) + "" + lastname;

      $("#username").val(res.toLowerCase());
    }

  });

  $("#password-change").click(function () {

    var newpassword = $("#newpassword").val();
    var password_length = newpassword.length;
    var cpassword = $("#password_confirm").val();
    var checkPwd = checkPassword(newpassword);


    if (!$("#password").val()) {
      $("#password").focus();
    } else if (!$("#newpassword").val()) {
      $("#newpassword").focus();
    } else if (!$("#password_confirm").val()) {
      $("#password_confirm").focus();
    } else if (newpassword && password_length < 8) {
      $("#change-password-error").html('<div class="alert alert-danger">New Password must be 8 characters or longer</div>');
      setTimeout(function () {
        $("#change-password-error").html("")
      }, 3000);
    } else if (newpassword && !checkPwd) {
      $("#change-password-error").html('<div class="alert alert-danger">Password must contain letters and numbers</div>');
      setTimeout(function () {
        $("#change-password-error").html("")
      }, 3000);
    } else if (newpassword != cpassword) {
      $("#change-password-error").html('<div class="alert alert-danger">confirm password mismatch</div>');
      setTimeout(function () {
        $("#change-password-error").html("")
      }, 3000);
    } else {

      $.ajax({
        url: '/user/passwordChangeSuccess',
        headers: {
          'X-CSRF-Token': $('meta[name="_token"]').attr('content')
        },
        type: "POST",
        data: $('#password-change-form').serialize(),
        success: function (data) {

          var message = $.parseJSON(data);

          if (message.status == 'SUCCESS') {
            $("#change-password-error").html('<div class="alert alert-success">' + message.msg + '</div>');
            setTimeout(function () {
              location.reload();
            }, 3000);
          } else {
            $("#change-password-error").html('<div class="alert alert-danger">' + message.msg + '</div>');

            setTimeout(function () {
              $("#change-password-error").html("")
            }, 3000);
          }

        }
      });
    }

  });

  $("#reset-password-btn").click(function () {
    var newpassword = $("#password").val();
    if (newpassword && newpassword.length < 8) {
      showModalError("reset-password-error", "New Password must be 8 characters or longer")
      return false;
    } else if (newpassword && !checkPassword(newpassword)) {
      showModalError("reset-password-error", "Password must contain letters and numbers")
      return false;
    }
  });


  $("#edit-profile").click(function () {
    var firstname = $("#firstname").val();
    var lastname = $("#lastname").val();
    var email = $("#email").val();
    var phone_office = $("#phone_office").val();
    var phone_mobile = $("#phone_mobile").val();

    if (!$("#firstname").val()) {
      $("#firstname").focus();
    } else if (!$("#lastname").val()) {
      $("#lastname").focus();
    } else if (!$("#email").val()) {
      $("#email").focus();
    } else if (email && !isEmail(email)) {
      $(".edit-error").html('<div class="alert alert-danger">Enter valid email address.</div>');
      $("#email").focus();
      setTimeout(function () {
        $(".edit-error").html("")
      }, 3000);
    } else if (phone_office && isNaN(phone_office)) {
      $(".edit-error").html('<div class="alert alert-danger">Phone number must be numeric.</div>');
      $("#phone_office").focus();
      setTimeout(function () {
        $(".edit-error").html("")
      }, 3000);
    } else if (phone_mobile && isNaN(phone_mobile)) {
      $(".edit-error").html('<div class="alert alert-danger">Mobile number must be numeric.</div>');
      $("#phone_mobile").focus();
      setTimeout(function () {
        $(".edit-error").html("")
      }, 3000);
    } else {

      $.ajax({
        url: '/configuration/profile/updateProfileDetails',
        headers: {
          'X-CSRF-Token': $('meta[name="_token"]').attr('content')
        },
        type: "POST",
        data: $('#edit-profile-form').serialize(),
        beforeSend: function () {
          $("#editModal").hide();
          $("#profile-loading").css('display', 'block');
        },
        success: function (data) {
          var message = $.parseJSON(data);
          if (message.status == 'SUCCESS') {
            //$(".edit-error").html('<div class="alert alert-success">'+message.msg+'</div>');
            location.reload();
            //$("#profile-edit-msg").html('<div class="alert alert-success">'+message.msg+'</div>');
            //setTimeout(function(){$("#profile-edit-msg").html('');}, 10000);
          } else {
            $(".edit-error").html('<div class="alert alert-danger">' + message.msg + '</div>');

            setTimeout(function () {
              $(".edit-error").html("")
            }, 3000);
          }
        }
      });
    }
  });
});

function isEmail(email) {
  var regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
  return regex.test(email);
}

function showModalError(errorDivId, message) {
  $("#" + errorDivId).html('<div class="alert alert-danger">' + message + '</div>');
  setTimeout(function () {
    $("#" + errorDivId).html("")
  }, 3000);
}

function checkPassword(str) {

  if (str.search(/\d/) == -1) {
    return false;
  } else if (str.search(/[a-zA-Z]/) == -1) {
    return false;
  }

  return true;
}
