$(document).ready(function(){
    var elem = $( "#avatar" );
    var uploader = new plupload.Uploader({
        runtimes: 'gears,html5,flash,silverlight,browserplus',
        browse_button: "action_upload",
        drop_element: "drop_file",
        max_file_size: '80mb',
        url: "/configuration/profile/updateAvatar",
        headers: {
            'X-CSRF-Token': $('meta[name="_token"]').attr('content')
        },
        multi_selection: true,
        flash_swf_url: '/js/plugins/pupload/plupload.flash.swf',
        silverlight_xap_url: '/js/plugins/pupload/plupload.silverlight.xap',
        multipart_params : {

        }
    });
    // Khi file duoc chon
    uploader.bind('FilesAdded', function(up, files)
    {
        // Reposition Flash/Silverlight
    });

    // Khi file duoc chon xong
    uploader.bind('QueueChanged', function(up)
    {
        // Bat dau upload
        uploader.start();
    });


    // Upload hoan thanh
    uploader.bind('FileUploaded', function(up, file, object)
    {
        var data = JSON.parse(object.response);
        if(data.result == 1) {
            $("#drop_file").css("background", "#f3f3f3 url('" + data.file + "?s=300" + "') no-repeat center center");
            $("#drop_file").css("background-size", "cover");
            $(".img-thumbnail").attr("src", data.file + "?s=200");
            $(".profile-element").find(".img-circle").attr("src", data.file + "?s=50");
        }
    });
    // Khoi dong uploader
    uploader.init();
});


/*========== user profile ============*/

function assistant_del(id)
{
    if(window.confirm('Are you sure to delete?'))
    {
          $.ajax({
              url :"/configuration/profile/deleteAssistant",
              type: "POST",
              headers: {
                'X-CSRF-Token': $('meta[name="_token"]').attr('content')
              },
              data:'id='+id,
              success:function(data){

                   if(data==1)
                   {
                     location.reload();
                   }

               }
            });
    }
}

$.widget('brklive.userTable1', {

    _url: "/configuration/profile/getAssistantList",
    _filter: {},

    _create: function() {
        var _this = this;
        this.datatable =
            this.element.dataTable( {
                "paginationType": "full_numbers",
                "filter": false,
                "info": false,
                "ajax": _this._url,
                "order": [],
                "columns": [
                    {
                        "data": "firstname",
                        "render": function(data, type, row, meta) {
                            return ''+ row.firstname + ' ' + row.lastname + '';
                        }
                    },
                    {
                        "data": "email",
                        "render": function(data, type, row, meta) {
                            return '<a href="mailto:'+row.email+'">'+ row.email +'</a>';
                        }
                    },
                    {
                        "data": "phone_office",
                        "render": function(data, type, row, meta) {
                          var phone=row.phone_office==null?'':row.phone_office;
                            return ''+ phone +'';
                        }
                    },
                    {
                        "data": "role",
                        "render": function(data, type, row, meta)
                         {
                            var lavel='';

                            if(row.role_name=='admin')
                            {
                             lavel='Admin';
                             return '<div class="label-warning">'+lavel+'</div>';
                            }
                            else if(row.role_name=='PA')
                            {
                             lavel='Assistant';
                             return '<div class="label-plain">'+lavel+ '</div>';
                            }
                            else if(row.role_name=='Broker')
                            {
                             lavel='Broker';
                             return '<div class="label-success">'+lavel+'</div>';
                            }
                            else
                            {
                              lavel='';
                              return '<div class="">'+lavel+'</div>';
                            }


                        }
                    },
                    {
                        "data": "action",
                        "render": function(data, type, row, meta) {
                            if(row.ass_id && row.is_broker)
                            {
                            return '<a href="javascript:assistant_del('+row.ass_id+')" title="Remove"><i class="fa fa-trash-o"></i></a>';
                            }
                           else
                            return '';
                        }
                    },


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
$.widget('brklive.userFiles', {
    _url: "/data/v1/user/files",
    _filter: {},

    _create: function() {
        var _this = this;
        this.datatable =
            this.element.dataTable( {
                "ajax": _this._url,
                "processing": true,
                "serverSide": true,
                "order": [],
                "columns": [

                    {
                        "data": "deal_name",
                        "render": function(data, type, row, meta) {
                            return '<a href="/deal/index/'+row.deal_id+'">'+ row.deal_name +'</a>';
                        }
                    },
                    {
                        "data": "file_name"
                    },
                    {
                        "data": "file_size",
                        "type": "num",
                        "render": function(data, type, row, meta) {
                            if (type === 'display' || type === 'filter') {
                                return formatBytes(row.file_size);
                            }
                            return data;
                        }
                    },
                    {
                        "data": "user_name"
                    },
                    {
                        "type": "date",
                        "data": "date"
                    },
                    {
                        "data": "file_id",//
                        "render": function(data, type, row, meta) {
                            return '<a href="/fileManagement/delete/' + row.file_id + '" class="link-delete" title="Delete">' +
                                '<i class="fa fa-trash-o"></i>' +
                                '</a>';
                        }
                    }
                ]
            });
        this._bindDeleteLink();
    },

    _bindDeleteLink : function () {
        var _this = this;
        this.element.on('click', 'a.link-delete', function (evt) {
            var link = $(this),
                rowId = $(this).data('rowId');
            evt.preventDefault();
            bootbox.confirm('Are you sure you want to delete file?', function(result){
                 if (result !== null) {
                    if (result === true) {
                        $.ajax({
                            url: link.attr('href'),
                            type: 'GET'
                        })
                        .done(function(data){
                            brokerlive.notification
                                .showSuccess('File deleted');
                            _this.datatable.api().row(link.closest('tr')).remove().draw();
                        })
                        .fail(function(data){
                            brokerlive.notification
                                .showError('Error deleting file');
                        });
                    }
                }
            });
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
$.widget('brklive.userBrokerCode', {
    _url: "/configuration/profile/listBrokerCode",
    _filter: {},
    _lenders: [],
    _firstRow: "",
    
    _create: function() {
        var _this = this;
        $.ajax({
            type: "GET",
            url: "/configuration/profile/getLenders",
            dataType: "JSON",
            success: function (msg) {
                _this._lenders = msg;
                var lenders = "";
                for(var i in _this._lenders) {
                    lenders += "<option value='" + _this._lenders[i].id + "'>" + _this._lenders[i].name + "</option>";
                }
                var select = '<td><select name="lender_id" class="brokercodetemp">' + lenders + '</select></td>';
                var inputCode = '<td><input name="code" class="brokercodetemp" value="" /></td>';
                var inputPassword = '<td><input name="password" class="brokercodetemp" value="" /><a class="brokercodenew"><i class="fa fa-plus"></i></a></td>';
                _this._firstRow = "<tr class='brokercodetemplate'>" + select + inputCode + inputPassword + "</tr>";
                _this.datatable =
                    _this.element.dataTable( {
                        "fnDrawCallback": function( oSettings ) {
                            $(".brokercode").off("change");
                            $(".brokercode").on("change", _this.change);
                            _this.init();
                        },
                        'createdRow': function( row, data, dataIndex ) {
                            $(row).attr('id', data.id);
                        },
                        "paginationType": "full_numbers",
                        "filter": false,
                        "info": false,
                        "ajax": {
                            "url":_this._url,
                            //"type":'post',
                            data: {
                            //    'X-CSRF-Token': $('meta[name="_token"]').attr('content')
                            }
                        },
                        "order": [],
                        "columns": [

                            {
                                "data": "lender_id",
                                "render": function(data, type, row, meta) {
                                    var lenders = "";
                                    var selected = "";
                                    for(var i in _this._lenders) {
                                        if(_this._lenders[i].id == data) {
                                            selected = "selected";
                                        } else {
                                            selected = "";
                                        }
                                        lenders += "<option value='" + _this._lenders[i].id + "'" + selected + ">" + _this._lenders[i].name + "</option>";
                                    }
                                    return '<select name="lender_id" class="brokercode">' + lenders + '</select>';
                                }
                            },
                            {
                                "data": "code",
                                "render": function(data, type, row, meta) {
                                    return '<input name="code" class="brokercode" value="' + data + '" />';
                                }
                            },
                            {
                                "data": "password",
                                "render": function(data, type, row, meta) {
                                    return '<input name="password" class="brokercode" value="' + data + '" />';
                                }
                            }
                        ]
                    });
            }
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
    },
    change: function (e) {
        var target = $( event.target );
        var data = {};
        data["id"] = $(target.parents("tr")[0]).attr("id");
        data[target.attr("name")] = target.val();
        $.ajax({
            type: "POST",
            headers: {
                'X-CSRF-Token': $('meta[name="_token"]').attr('content')
            },
            //url: "/user/editBrokerCode",
            url: "/configuration/profile/editBrokerCode",
            data: data,
            success: function (msg) {
                
            }
        });
    },
    init: function () {
        var _this = this;
        $(".brokercodenew").off("click");
        $(_this._firstRow).prependTo(".table-brokercode tbody");
        $(".brokercodetemp").val("");
        $(".brokercodenew").on("click", function (e) {
            var target = $(".brokercodetemplate").find(".brokercodetemp");
            var data = {};
            target.each(function (e) {
                data[$(this).attr("name")] = $(this).val();
            });
            $.ajax({
                type: "POST",
                headers: {
                'X-CSRF-Token': $('meta[name="_token"]').attr('content')
                },
                url: "/configuration/profile/editBrokerCode",
                data: data,
                success: function (id) {
                    var row = $(".brokercodetemplate");
                    row.attr("id", id);
                    row.removeClass("brokercodetemplate");
                    row.find(".brokercodenew").remove();
                    row.find(".brokercodetemp").addClass("brokerCode").removeClass("brokercodetemp");
                    row.remove();
                    _this.init();
                    $('.table-brokercode tbody tr:first').after(row);
                    row.on("change", _this.change);
                    $(".brokercodetemp").val("");
                }
            });
        });
    }
});

function imgError(image) {
    image.onerror = "";
    image.src = "/img/avatar/avatar_default.png";
    return true;
}

$(document).ready(function(){
    $('table.table-profile').userTable1();
    $('table.table-files').userFiles();
    $('table.table-brokercode').userBrokerCode();

    var ajaxresponse='';
    var offset='';
    var width='';
    var thisthis='';

    $("body").on("keyup",".assistance",function(event){

         thisthis=$(this);
         keyword = $(this).val();
         ajaxresponse=$(this).attr('rel');
        offset = $(this).offset();
        width = $(this).width()-2;
        $(this).css("left",offset.left);
        $("#"+ajaxresponse).css("width",width);


         if(keyword.length)
         {
             if(event.keyCode != 40 && event.keyCode != 38 && event.keyCode != 13)
             {

                 $.ajax({
                   type: "POST",
                   //url: "/user/userautocomplete",
                   url: "/configuration/profile/userautocomplete",
                   headers: {
                    'X-CSRF-Token': $('meta[name="_token"]').attr('content')
                   },
                   data: "data="+keyword,
                   success: function(msg){
                    if(msg)
                    {
                        var message=msg;//$.parseJSON(msg);
                        var html='<ul class="list">';

                        $.each( message, function( key, value ) {

                                  var firstname =value.firstname ? value.firstname +' '+value.lastname:value.lastname;

                                  var final = '<span class="bold">'+firstname+'</span>';

                                  html+="<li><a class='selectclass' href='javascript:void(0);' rel='"+value.id+"'>"+final+"</a></li>";


                        });

                         html+="</ul>";



                      $("#ajaxresponse").fadeIn("slow").html(html);
                    }
                    else
                    {
                      //$("#"+ajaxresponse).fadeIn("slow");
                      //$("#"+ajaxresponse).html('<div style="text-align:left;">No Matches Found</div>');
                    }

                   }
                 });
             }
             else
             {
                switch (event.keyCode)
                {
                 case 40:
                 {
                      found = 0;
                      $("li").each(function(){
                         if($(this).attr("class") == "selected")
                            found = 1;
                      });
                      if(found == 1)
                      {
                        var sel = $("li[class='selected']");
                        sel.next().addClass("selected");
                        sel.removeClass("selected");
                      }
                      else
                        $("li:first").addClass("selected");
                     }
                 break;
                 case 38:
                 {
                      found = 0;
                      $("li").each(function(){
                         if($(this).attr("class") == "selected")
                            found = 1;
                      });
                      if(found == 1)
                      {
                        var sel = $("li[class='selected']");
                        sel.prev().addClass("selected");
                        sel.removeClass("selected");
                      }
                      else
                        $("li:last").addClass("selected");
                 }
                 break;
                 case 13:
                    $("#"+response).fadeOut("slow");
                    $(this).val($("li[class='selected'] a").text());
                 break;
                }
             }
         }
         else
            $("#ajaxresponse").fadeOut("slow");
    });

       $('body').on('mouseover',"#ajaxresponse",function()
       {

        $(this).find("li a:first-child").mouseover(function () {
            $(this).addClass("selected");
        });
        $(this).find("li a:first-child").mouseout(function () {
              $(this).removeClass("selected");
        });

        $(this).find("li a:first-child").click(function () {

              $(thisthis).val($(this).text());
              $("#ajaxresponse").fadeOut("slow");
        });
    });


    $("#add-assistant").click(function(){
        //check there is a valid assistant
        $(".error-mesg").fadeOut(30);
        if($("#ass_id").val()) {
            $.ajax({
                //url: '/user/addAssistant',
                url: '/configuration/profile/addAssistant',
                type: "POST",
                headers: {
                'X-CSRF-Token': $('meta[name="_token"]').attr('content')
                },
                data: $('#add-assistant-form').serialize(),
                success: function (data) {
                    var message = $.parseJSON(data);
                    if (message.status == 'SUCCESS') {

                        $(".error-mesg").html('<div class="alert alert-success">Added successfully.</div>');
                        setTimeout(function () {
                            location.reload();
                        }, 2000);
                    }
                    else {
                        $(".error-mesg").html('<div class="alert alert-danger">Some error!</div>');
                        setTimeout(function () {
                            $(".error-mesg").html("")
                        }, 3000);
                    }

                }
            });
        } else {
            $(".error-mesg").html('<div class="alert alert-danger">Please specify an existing assistant!</div>');
            $(".error-mesg").fadeIn(300);
        }

    });
    $(document).on('click', "a[data-target='#myModal']", function(e) {
        $($(this).data("target")).find(".error-mesg").hide();
    })

    $('body').on('click','.selectclass',function(){

       var selected_id = $(this).attr('rel');
           $("#ass_id").val(selected_id);
       $.ajax({
              //url :"/user/userdetails",
              url :"/configuration/profile/userdetails",
              type: "POST",
              headers: {
                'X-CSRF-Token': $('meta[name="_token"]').attr('content')
              },
              data:'selected_id='+selected_id,
              success:function(data){

                var message=data;//$.parseJSON(data);

                  if(message)
                  {
                     var name = message.firstname+" "+ message.lastname;
                     var email= message.email;
                     var phone= message.phone_office==null ? '':message.phone_office;

                     var details='<div class="alert alert-info"><b>Name:</b> '+name+' <b>&nbsp;&nbsp;Email:</b> '+email+'<b>&nbsp;&nbsp;Phone:</b>'+phone+'</div>'
                    $("#show-details").html(details);

                  }
                  else
                  {
                     $("#show-details").html('');
                  }

               }
            });
    });

});
