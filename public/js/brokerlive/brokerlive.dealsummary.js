(function( $ ) {
    $.fn.dealname = function() {
        this.each(function() {
            var elem = $( this );
            elem.find('.btn-nameedit').on('click', function(){
                var deal_name =  elem.find("h1.name").text();
                elem.find("#deal-name").val(deal_name);
                elem.find(".deal-edit").hide();
                elem.find(".deal-save").show();
            });
            elem.find('.btn-namecancel').on('click', function(){
                elem.find(".deal-save").hide();
                elem.find(".deal-edit").show();
            });
            elem.find('.btn-namesave').on('click', function(){
                var deal_name = elem.find("#deal-name").val();
                var deal_id = $('input#deal-id').val();
                $.ajax({
                    url: '/deal/update/' + deal_id,
                    headers: {
                        'X-CSRF-Token': $('meta[name="_token"]').attr('content')
                    },
                    data: {
                        "name": deal_name
                    },
                    type: "POST",
                    success: function (data) {

                    },
                    error: function () {
                        brokerlive.notification
                            .showError('Unable to update deal');
                    },
                    dataType: 'json'
                });
                elem.find("h1.name").html(deal_name);
                elem.find(".deal-save").hide();
                elem.find(".deal-edit").show();
            });
        });
        return this;
    };
}( jQuery ));

(function( $ ) {
    $.fn.dealstatus = function() {
        this.each(function() {
            $(this).on('change', function(){
                var deal_id = $('input#deal-id').val();
                var status = $(this).val();
                $.ajax({
                    url: '/deal/update/' + deal_id,
                    headers: {
                        'X-CSRF-Token': $('meta[name="_token"]').attr('content')
                    },
                    data: {
                        'status' : status
                    },
                    type: 'POST'
                })
                .done(function(data){
                    brokerlive.notification
                        .showSuccess('Status updated successfully');
                })
                .fail(function(data){
                    brokerlive.notification
                        .showError('Error saving deal status');
                });
            });
        });
        return this;
    };
}( jQuery ));

(function( $ ) {
    $.fn.dealclone = function() {
        this.each(function() {
           $(this).on('click', function() {
                bootbox.prompt('What is the new deal name?', function(result){
                    if (result!== null) {
                        if (result !== '') {
                            $.ajax({
                                url: '/deal/clone/' + $('input#deal-id').val(),
                                headers: {
                                    'X-CSRF-Token': $('meta[name="_token"]').attr('content')
                                },
                                data: {
                                    'deal-name' : result
                                },
                                type: "POST",
                                success: function(data) {
                                    document.location.href ='/deal/index/' + data;
                                },
                                error: function() {
                                    brokerlive.notification
                                        .showError('Unable to create deal');
                                },
                                dataType: 'json'
                            });
                        } else {
                            brokerlive.notification
                                .showError('Cannot create a deal without a name.');
                        }
                    }
                });
            });
        });
    };
}( jQuery ));

(function( $ ) {
    $.fn.collapsible = function() {
        this.each(function () {
            var elem = $(this);
            elem.find('legend').first().append(' (<span style="font-family: monospace;">+</span>) ');
            elem.find('legend').first().click(function () {
                var $divs = $(this).siblings();
                $divs.toggle();

                $(this).find('span').text(function () {
                    return ($divs.is(':visible')) ? '-' : '+';
                });
            });
        });
    };
}( jQuery ));

var oldHTML;
var deal_id;
function checkReminder() {
    var result = false;

    return result;
}
$(document).ready(function(){

    deal_id = $("#deal-id").val();

    $('fieldset.collapsible').collapsible();

    $(".btn-editnote").on("click", function() {
        oldHTML = $(".deal-notes").val();
        $(".deal-notes").tinymce($.extend({},
            brokerlive.config.tinyMCE,
            {
                height: 400
            }
        ));
        $(".notes-edit").hide();
        $(".notes-save").show();

        editor_id = $(".deal-notes").attr('id');
        tinymce.get(editor_id).show();
    });

    $(".btn-cancelnote").on("click", function() {
        editor_id = $(".deal-notes").attr('id');
        tinymce.get(editor_id).hide();

        $(".deal-notes").css('visibility', 'inherit');

        $(".deal-notes").val('');
        $(".deal-notes").html(oldHTML);
        $(".notes-save").hide();
        $(".notes-edit").show();
    });

    $(".btn-savenote").on("click", function() {
        var aHTML = $(".deal-notes").val();
        $.ajax({
            url: '/deal/update/' + deal_id,
            headers: {
                'X-CSRF-Token': $('meta[name="_token"]').attr('content')
            },
            data: {
                'notes': aHTML
            },
            type: "POST",
            success: function (data) {

            },
            error: function () {
                brokerlive.notification
                    .showError('Unable to update deal');
            },
            dataType: 'json'
        });
        editor_id = $(".deal-notes").attr('id');
        tinymce.get(editor_id).hide();

        $(".deal-notes").css('visibility', 'inherit');
        $(".notes-save").hide();
        $(".notes-edit").show();
    });

    $('div.deal-name').dealname();
    $('.deal-status').dealstatus();
    $('.btn-clone').dealclone();

    // $('.linked-deals').on('change', function(){
    //     document.location.href ='/deal/index/' + $(this).val();
    // });

    $('div.widget-contacts').widgetContacts();
    $('div.widget-files').widgetFiles();
    $('div.widget-splitloan').widgetSplit();
    //$('div.widget-reminders').widgetReminders();
    $('div.widget-reminders').reminder_list({deal_id : deal_id});
    $('div.widget-journal').journal_panel({deal_id : deal_id});
    $(window).bind('beforeunload', function(){
        var check = $("#reminderList .reminder").length == 0 && $("#reminderGrid").data("prompt") != 1;
        if(check){
            return "You are closing this client card without an active reminder. Proceed anyway?";
        } else {
            // this throw will always be thrown when there is at least 1 reminder, funny huh?
            //throw new Error;
        }
    });
    $(".stop-loading-page").click(function(e){
        clickedElement = this;
        var check = $("#reminderList .reminder").length == 0;
        if(check) {
            e.preventDefault();
            swal({
                title: "You are closing this client card without an active reminder. Proceed anyway?",
                text: "",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Yes",
                closeOnConfirm: false
            }, function () {
                $("#reminderList").data("prompt", 1);
                window.location = $(clickedElement).attr("href");
            });
        }
    });
    $(document).on("click", "form[name='splitForm'] .btn-save", function(e){
        if($(this).parents("form").find("select[name='lender_id']").val() == $("input[name='other_lender_id']").val() && !$(this).parents("form").find("input[name='other_lender']").val()){
            e.preventDefault();
            swal({
                title: "Please type in the Lender name before saving",
                text: "",
                type: "warning",
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Yes",
                closeOnConfirm: true
            });
        }
    });
    /*$(document).on("click", "form[name='journalForm'] button[type='submit']", function(e){
        var message = "";
        var viewAccess = false;
        $(this).parents("form").find(".myCheckbox").each(function(e){
            if($(this).prop("checked")) {
                viewAccess = true;
            }
        });
        if(!viewAccess){
            message = "Please choose View Access type";
        }
        if(message.length == 0){
            var noteType = $(this).parents("form").find("select[name='options[]']").val() != null;
            if(!noteType) {
                message = "Please choose Note Type";
            }
        }
        if(message.length == 0){
            var notes = $(this).parents("form").find(".note-editable").html() != '';
            if(!notes) {
                message = "Please type Notes";
            }
        }

        if(message.length > 0){
            e.preventDefault();
            swal({
                title: message,
                text: "",
                type: "warning",
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Yes",
                closeOnConfirm: true
            });
        }
    });*/
    //register the lender change
    $(document).on("change", "[name=lender_id]", function (e) {
        var value = $(this).val();
        if(value == $("input[name='other_lender_id']").val()) {
            $("[name=other_lender]").show();
        } else {
            $("[name=other_lender]").hide();
        }
    });
    $("#notify-filter-for").multiselect({
        onChange: function(option, checked, select) {
            $.ajax({
                type: 'POST',
                url: '/deal/notify/' + $("#deal-id").val(),
                data: { 'types': $("#notify-filter-for").val() ? $("#notify-filter-for").val().join(",") : '' }
            })
        },
        buttonText: function(options, select) {
            return 'Notify';
        },
        buttonClass: 'btn btn-default btn-sm'
    });

    //-- Setup the linked deal tree
    $('div.deal-tree').jstree({
        core: {
            themes: {
                icons : false
            }
        }
    }).on("click.jstree", ".jstree-anchor", function (e) {
        document.location = this.href;
    });
});
