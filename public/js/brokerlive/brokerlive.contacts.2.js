$.widget('brklive.contactGroups', {
    
    _url: "/data/v1/contact",
    _filter: {},
       
    _create: function() {
        var _this = this;
        $('a.client-link').on('click', function(e){
            e.preventDefault();
            var path = $(this).attr('href');
            $.get(
                path,
                {},
                function(data) {
                    var formDiv = $('div.contact-form');
                    formDiv.html(data);
                    _this._bindFormEvents();
                },
                'html'
           );
        });
        $('a.client-link').first().trigger('click');
    },
    
    _bindFormEvents: function() {
        // Add slimscroll to element
        $('.slimscroll').slimscroll({
            height: '100%',
            wheelStep: 1
        });
        $('form[name="contactForm"]').on('submit', function(evt){
            evt.preventDefault();
            var $form = $(this);
            var email = $('input[name=email]').val();
            if ($('form[name="contactForm"]').parsley().validate())
            {
                $.ajax({
                    url: "/data/v1/contact",
                    data: {
                      "email": email  
                    },
                    type:"GET",
                    success: function(data)
                    {
                        if ( data.data && (data.data.length > 0) ) {
                            var message = "This Email is duplicate with information provided by ";
                            var names = [];
                            for(var i in data.data) {
                                names.push(data.data[i].firstname + (data.data[i].lastname ? " " + data.data[i].lastname : ''));
                            }
                            message += names.join(", ");
                            message += ". Process anyway?";
                            swal({
                                title: message,
                                text: "",
                                type: "warning",
                                showCancelButton: true,
                                confirmButtonColor: "#DD6B55",
                                confirmButtonText: "Yes",
                                closeOnConfirm: false
                            }, function () {
                                $form.unbind().submit();
                            });
                        } else {
                            $form.unbind().submit();
                        }
                    },
                    "createdRow": function ( row, data, index ) {
                        _this._bindRowEvents(row);
                    }
                });
            }
        });
    }
});
$(document).ready(function(){
    var w=eval($('.navbar-static-side').css('width').replace('px',''));
    if($(window).width() > 768){
        $('.create-contact-form').css('margin-left',($(window).width()-750-w)/2);
    }
    $('.nav-tabs li a').on('click',function(){
        $('.nav-tabs li').removeClass('active');
        $(this).parent().addClass('active');
        var id=$(this).prop('href').substr($(this).prop('href').indexOf('#')+1);
        if(id=='0'){location.href=$(this).prop('href');}
        $('.tab-content div').removeClass('active');
        $('.tab-content #'+id).addClass('active');
    });
    $('.page-heading .title-action').html('<a title="Add Contact" class="btn btn-white contactadd" href="#" onclick="alert(\'developing\');"><i class="fa fa-user"></i> Add Contact</a>');///contact/create

    var clientGetProcess;
    $(document).on('click', 'a.client-link', function(e){
        e.preventDefault();

        var path = $(this).attr('href');

        var tr = $(this).closest('tr');
        tr.parent().children('.selected').removeClass('selected');
        tr.addClass('selected');

        if (clientGetProcess) {
            clientGetProcess.abort();
        }

        var formDiv = $('div.contact-form');
        formDiv.find('input,button,select,textarea').prop('disabled', true);
        formDiv.find('a').addClass('disabled');
        clientGetProcess = $.get(
            path,
            {},
            function(data) {
                formDiv.html(data);
            },
            'html'
        );
    });

    $('#create_btn').on('click', function(){
        var formDiv = $('.create-contact-form');
        var form_data=new FormData();
        formDiv.find('input,button,select,textarea').each(function(){
            form_data.append($(this).prop('name'), $(this).val());
        });
        $.ajax({
            url: $('form[name="contactForm"]').prop('action'),
            data: form_data,
            cache: false,
            contentType: false,
            processData: false,
            type: 'POST',
            dataType: "html",
            success: function (response) {
                alert('Successful saved');
                location.href='/contact';
            },
            error: function (response) {
                formDiv.html(response);
            }
        });
    });

    var datepickerOptions = {
        format: 'dd M yyyy',
        autoclose: true
    };
    $('.datepicker').datepicker(datepickerOptions);
    
    var dataTable = {};
    var first = true;
    $('.table-contact').each(function (e) {
        var contactType = $(this).attr("contact_type");
        var table = $(this);
        dataTable[contactType] = table
            .DataTable( {
                "dom":  "<'row'<'col-sm-3'l><'col-sm-3'f><'col-sm-6'p>>" +
                        "<'row'<'col-sm-12'tr>>" +
                        "<'row'<'col-sm-5'i><'col-sm-7'p>>",
                "processing": true,
                "serverSide": true,
                "ajax": "/data/v1/contact/datatable?contact_type=" + contactType,
                "columns": [
                    { "data": "id" },
                    {
                        "data": "name",
                        "render": function(data, type, row, meta) {
                            return "<a href='/contact/edit/" + row.id + "' class='client-link'>" + (row.firstname + ' ' + row.lastname) + "</a>";
                        }
                    },
                    { "data": "company" },
                    {
                        "data": "phonemobile",
                        "render": function(data, type, row, meta) {
                            return (row.phonemobile != null && row.phonemobile.length > 0 ? "<a href='tel:"+ row.phonemobile + "'><i class='fa fa-mobile-phone'></i>" + row.phonemobile + "</a>" : "") +
                                (row.phonehome != null && row.phonehome.length > 0 ? "<br><a href='tel:"+ row.phonehome + "'><i class='fa fa-phone'></i>" + row.phonehome + "</a>" : "") +
                                (row.phonework != null && row.phonework.length > 0 ? "<br><a href='tel:"+ row.phonework + "'><i class='fa fa-phone'></i>" + row.phonework + "</a>" : "");
                        }
                    },
                    {
                        "data": "email",
                        "render": function(data, type, row, meta) {
                            return "<a href='mailto:"+ row.email + "'><i class='fa fa-envelope'></i>" + row.email + "</a>";
                        }
                    }
                ],
                "fnDrawCallback": function ( oSettings ) {
                    var id = $(".contact.active").attr("id");
                    var pattern = "contact_type=" + id;
                    if(oSettings.ajax.indexOf(pattern) != -1) {
                        $("#" + id).find('a.client-link').first().trigger('click');
                    }
                }
            } );
    });
    var searchForm = $("#contact-search");
    var autoCompleteInput = searchForm.find('input[name=s]');
    autoCompleteInput
        .autocomplete({
            source: '/data/v1/contact/autocomplete',
            minLength: 2,
            select: function (event, ui) {
                setTimeout(function() { searchForm.submit() });
            }
        })
        .autocomplete( "instance" )._renderItem = function( ul, item ) {
            return $( "<li></li>" )
                .data( "item.autocomplete", item )
                .append( $( "<div></div>" ).append( item.label ) )
                .appendTo( ul );
        };
    searchForm.on("submit", function (e) {
        e.preventDefault();
        $("a[href='#0']").trigger("click");
        $('#table-contact0').DataTable().search($(this).find("input[name='s']").val()).draw();
    });
});
function validateContact(id, href) {
    $.ajax({
        url: "/data/v1/contact",
        data: {
            "action": "validate",
            "contact_id": id
        },
        type:"GET",
        success: function(data)
        {
            if ( data.data ) {
                if(data.data.status == 0) {
                    swal({
                        title: data.data.message,
                        text: "",
                        type: "warning"
                    });
                } else {
                    location.href = href;
                }
            } else {
                location.href = href;
            }
        }
    });
    return false;
}
function deleteAddress(id) {
    $("#address" + id).remove();
}
function addAddress(id) {
    var temp = $(".addressTemplate").clone();
    temp.removeClass("addressTemplate");
    temp.show();
    var html = temp[0].outerHTML.replace(/\[template_id\]/g, id);
    $("#add-address").before(html);
    $("#add-address").find("a").attr("onclick", "addAddress(" + (id+1) + ")")
    $('.datepicker').datepicker(datepickerOptions);
}
function showAddress(i) {
    if($("#address" + i).attr("show") == 0) {
        $("#address" + i + " .address").show();
        $("#address" + i + " .address").attr("show", 1);
    } else {
        $("#address" + i + " .address").hide();
        $("#address" + i + " .address").attr("show", 0);
    }
}
function whenLoadEditForm(){
    $('#save_btn').on('click', function(){
        var formDiv = $('div.contact-form');
        var form_data=new FormData();
        formDiv.find('input,button,select,textarea').each(function(){
            form_data.append($(this).prop('name'), $(this).val());
        });
        $.ajax({
            url: $('form[name="contactForm"]').prop('action'),
            data: form_data,
            cache: false,
            contentType: false,
            processData: false,
            type: 'POST',
            dataType: "html",
            success: function (response) {
                alert('Successful saved');
                formDiv.html(response);
            },
            error: function (response) {
                formDiv.html(response);
            }
        });
    });
    $('.loansplit-form li').on('click',function(){
        $('.loansplit-form li').each(function(){
            $(this).removeClass('active');
        });
        $(this).addClass('active');
    });
}