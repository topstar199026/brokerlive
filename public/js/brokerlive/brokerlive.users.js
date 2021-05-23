$.widget('brklive.userTable', {
    _url: "/data/v1/user",
    _filter: {},
    _create: function() {
        var _this = this;
        this.datatable =
            this.element.dataTable( {
                "paginationType": "full_numbers",
                "filter": false,
                "info": false,
                "ajax": _this._url,
                "columns": [
                    {
                        "data": "username",
                        "render": function(data, type, row, meta) {
                            return '<a href="/configuration/user/edit/' + row.id + '">' + row.username + '</a>';
                        }
                    },
                    {
                        "data": "firstname",
                        "render": function(data, type, row, meta) {
                            return '<a href="/configuration/user/edit/' + row.id + '">' + row.lastname + ', ' + row.firstname + '</a>';
                        }
                    },
                    { "data": "email" },
                    {
                        "data": "role",
                        "render": function(data, type, row, meta)
                         {
                            var lavel='';
                            if(row.role.admin==true)
                            {
                             lavel+='<div class="label label-warning"> Admin </div>';
                            }
                            if(row.role.personalAssistant==true)
                            {
                             lavel+='<div class="label label-plain"> Assistant </div>';
                            }
                            if(row.role.broker==true)
                            {
                             lavel+='<div class="label label-success"> Broker </div>';
                            }
                            else
                            {
                              lavel+='';
                            }

                            return lavel;
                        }
                    },
                    { "data": "login_count" }
                ]
            });
    },

    reload: function (url) {
        if (typeof url === "undefined") {
            this.datatable.api().ajax.reload();
        } else {
            this.datatable.api().ajax.url(url).load();
        }
    },

    filter : function (data) {
        var _this = this;
        $.extend(_this._filter, data);
        _this.reload(URI(_this._url).query(_this._filter).resource());
    }
});
$(document).ready(function(){
    //$('table.table-users').userTable();
    $('#update_password_btn').on('click',function(e){
        if($('#password').val()!=$('#password_confirm').val()){
            //alert("confirm password mismatch");
            $('#password').val('');
            $('#password_confirm').val('');
            e.preventDefault();
            $('#change_pass_error').fadeIn();
            $('#change_pass_error').fadeOut(5000);
        }
    });
    $('select#roles').select2({
        width: '100%'
    });
    
    var source =
    {
        dataType: "json",
        dataFields: [
            { name: 'id', type: 'number' },
            { name: 'full_name', type: 'string' },
            { name: 'username', type: 'string' },
            { name: 'email', type: 'string' },
            { name: 'login_count', type: 'number' },
            { name: 'is_broker', type: 'boolean' },
            { name: 'is_assitant', type: 'boolean' },
            { name: 'children', type: 'array' }
        ],
        hierarchy:
        {
            root: 'children'
        },
        url: '/data/v1/usertree/'
    };
    var dataAdapter = new $.jqx.dataAdapter(source);
    // create Tree Grid
    $("#user_grid").jqxTreeGrid({
        width: '100%',
        source: dataAdapter,
        sortable: true,
        columns: [
            { text: 'Full Name', dataField: 'full_name', width: 300 },
            { text: 'Username', dataField: 'username', width: 150 },
            { text: 'Email', dataField: 'email' },
            {
                text: 'Role', dataField: 'role', width: 100,
                cellsRenderer: function (rowKey, dataField, value, data) {
                    var label = '';

                    if ( data.is_assisstant == false && data.is_broker == false && data.username == "admin" ) {
                        label += '<div class="label label-warning"> Admin </div>';
                    }
                    if ( data.is_assisstant == true ) {
                        label += '<div class="label label-plain"> Assistant </div>';
                    }
                    if ( data.is_broker == true ) {
                        label += '<div class="label label-success"> Broker </div>';
                    } else {
                        label += '';
                    }
                    return label;
                }
            },
            { text: 'Logins', dataField: 'login_count', width: 80 }
        ]
    });
    $("#user_grid").on('rowClick', function (event) {
        var args = event.args;
        if ( args.row.username ) {
            document.location.href = '/configuration/user/edit/' + args.row.id;
        }
    });

    $("#cloneForm").submit(function(e) {
        e.preventDefault();
        $(".cloneStatus").hide();
        var self = this;
        var email = $("#cloneForm").find("input[name='email']").val();
        var reason = $("#cloneForm").find("textarea[name='reason']").val();
        var id = $("#cloneForm").find("input[name='id']").val();

        $("#cloneForm").find("button[type=submit]").prop("disabled", true);
        $.ajax({
            type: "POST",
            url: $(this).attr("action"),
            headers: {
                'X-CSRF-Token': $('meta[name="_token"]').attr('content')
            },
            data: {email: email, reason: reason, id: id},
            cache: false,
            dataType: "JSON"
        }).done(function(result) {
          if(result.result == 0) {
              $("#cloneError").text(result.message);
              $("#cloneError").show();
          } else {
              $("#cloneSuccess").show();
          }
        }).always(function() {
            closeCloneDialog();
        });
    });

    function closeCloneDialog() {
        this.closeCount = this.closeCount || 6;
        this.closeCount--;
        $('#cloneClosingMessage').text('Auto close in ' + this.closeCount + ' second' + (this.closeCount !== 1 ? 's' : ''));
        if (this.closeCount > 0) {
            setTimeout(closeCloneDialog.bind(this), 1000);
            return;
        }
        $(".cloneStatus").hide();
        $("#cloneForm")
            .trigger('reset')
            .find("button[type=submit]").prop("disabled", false);
        $('#cloneModal').modal('hide');
    }
});

function lockout(element) {
    $.ajax({
        type: 'POST',
        url: "/configuration/user/lockout",
        headers: {
            'X-CSRF-Token': $('meta[name="_token"]').attr('content')
        },
        data: {userId: $(element).attr("user_id")},
        dataType: "JSON"
    }).done(function(data){
        if(data.result == 1) {
            $("#lock_" + data.userId).text("Unlock");
            $("#lock_" + data.userId).attr("onclick", "unlock(this)");
        }
    })
}

function unlock(element) {
    $.ajax({
        type: 'POST',
        url: "/configuration/user/unlock",
        headers: {
            'X-CSRF-Token': $('meta[name="_token"]').attr('content')
        },
        data: {userId: $(element).attr("user_id")},
        dataType: "JSON"
    }).done(function(data) {
        if(data.result == 1) {
            $("#lock_" + data.userId).text("Lock out");
            $("#lock_" + data.userId).attr("onclick", "lockout(this)");
        }
    })
}


$("#add-new-user").click(function (e) {
    if(document.getElementById("add-user-form").checkValidity()) {
      e.preventDefault();
      $.ajax({
        url: '/configuration/user/create',
        type: "POST",
        data: $('#add-user-form').serialize(),
        dataType: "JSON",
        success: function (data) {alert(data+','+data.id +','+data.email );
          if (data.id > 0) {
            window.location = '/configuration/user';
            return false;
          } else{
            for (var field in data) {
                showModalError("user-"+field+"-error", data[field]);
            }
          }
        }
      });
      return false;
    }
  });
  