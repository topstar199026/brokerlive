$.fn.widgetContacts = function() {
    this.each(function() {
        var elem = $( this );
        elem.find('a.link-new').on('click', function(e){
            e.preventDefault();
            var path = $(this).attr('href');
            $.get(
                path,
                {},
                function(data) {
                    var formDiv = elem.find('div.ibox-content.form-edit');
                    formDiv.html(data);
                    bind_contactFormEvents(formDiv.find('form'));
                    formDiv.slideDown(
                            'slow'
                            );
                },
                'html'
            );
        });
        bind_contactListEvents(elem);
    });
    return this;
};
function bind_contactListEvents(widget){
    $(widget).find('li a.link-edit').on('click', function(evt){
        evt.preventDefault();
        var formDiv = $(this).parents('li').find('div.form-edit');
        if (formDiv.is(":visible")) {
            formDiv.slideUp(
                    'slow',
                    function(){}
            );
        } else {
            formDiv.slideDown(
                    'slow',
                    function(){}
            );
        }
    });
    $(widget).find('form').each(function() {
        bind_contactFormEvents(this);
    });
    $(widget).find('li a.link-delete').on('click', function(evt){
        var _this = this;
        evt.preventDefault();
        bootbox.confirm('Are you sure you want to delete contact?', function(result){
                if (result !== null) {
                if (result === true) {
                    $.ajax({
                        url: $(_this).attr('href'),
                        type: 'GET'
                    })
                    .done(function(data){
                        brokerlive.notification
                            .showSuccess('Contact deleted');
                    })
                    .fail(function(data){
                        brokerlive.notification
                            .showError('Error deleting contact');
                    })
                    .always(function(){
                        get_contactList();
                    });
                }
            }
        });
    });
};
function bind_contactFormEvents(form) {
    var $form = $(form);
    $form.on('submit', function(evt){
        evt.preventDefault();
        $.ajax({
            type: 'POST',
            url: $(this).attr('action'),
            data: $(this).serialize() + '&deal_id=' +
                    encodeURIComponent($('#deal-id').val())
        })
        .done(function(data){
            brokerlive.notification
                .showSuccess('Contact saved successfully');

        })
        .fail(function(data){
            brokerlive.notification
                .showError('Error saving contact');
        })
        .always(function(){
            get_contactList();
            $(form).parents('div.form-edit').slideUp();
            $('html body').animate({scrollTop:0}, 'slow');
        });
    });
    $form.find('.autocomplete-name').autocomplete({
        source: '/data/v1/contact/autocomplete?user_id=' + $("#deal_user_id").val(),
        minLength: 2,
        select: function(event, ui) {
            $form.find('input[name=contact_id]').val(ui.item.data.id);
            $form.find('input[name=firstname]').val(ui.item.data.firstname);
            $form.find('input[name=lastname]').val(ui.item.data.lastname);
            $form.find('select[name=contacttype_id]').val(ui.item.data.contacttype_id);
            $form.find('input[name=company]').val(ui.item.data.company);
            $form.find('input[name=phonemobile]').val(ui.item.data.phonemobile);
            $form.find('input[name=phonehome]').val(ui.item.data.phonehome);
            $form.find('input[name=phonework]').val(ui.item.data.phonework);
            $form.find('input[name=email]').val(ui.item.data.email);
            $form.find('input[name=address1]').val(ui.item.data.address1);
            $form.find('input[name=address2]').val(ui.item.data.address2);
            $form.find('input[name=suburb]').val(ui.item.data.suburb);
            $form.find('input[name=state]').val(ui.item.data.state);
            $form.find('input[name=postcode]').val(ui.item.data.postcode);
            $form.find('input[name=notes]').val(ui.item.data.notes);
            return false;
        },
        create: function (event, ui) {
            // workaround to disable Chrome's autofill on this field
            $(event.target).attr('autocomplete', 'new-password');
        }
    })
    .autocomplete( "instance" )._renderItem = function( ul, item ) {
        return $( "<li></li>" )
            .data( "item.autocomplete", item )
            .append( $( "<div></div>" ).append( item.label ) )
            .appendTo( ul );
    };
    $form.find('.btn-cancel').on('click', function(evt){
        evt.preventDefault();
        $(form).parents('div.form-edit').slideUp();
    });
    $form.find('.btn-delete').hide();
};
function get_contactList() {
    var deal_id =  $("#deal-id").val();
    var url = '/dealContact/getlist';
    if ($("#deal-temp").val()) {
        url = '/report/dealcontactgetlist';
    }
    $.ajax({
        url: url,
        headers: {
            'X-CSRF-Token': $('meta[name="_token"]').attr('content')
        },
        data: {
            'deal_id': deal_id
        },
        type:"POST",
        success: function(msg)
        {
            $("#contactListDiv").html(msg);
            bind_contactListEvents($("#contactListDiv"));
        }
    });
};
