(function( $ ) {
    $.fn.dealadd = function() {
        this.each(function(){
            $(this).on('click', function() {
                if ($(this).attr('data-type') === 'broker') {
                    bootbox.form({
                        'title': 'Deal details',
                        'fields': {
                            'name': {
                                'label': 'Deal name',
                                'type': 'text'
                            }
                        },
                        callback: function(result){
                            if (result === null) {
                                return;
                            }
                            if (result['name'] === '') {
                                brokerlive.notification
                                    .showError('Cannot create a deal without a name.');
                                return;
                            }
                            $.ajax({
                                url: '/deal/create',
                                headers: {
                                    'X-CSRF-Token': $('meta[name="_token"]').attr('content')
                                },
                                data: {
                                    'deal-name': result['name'],
                                },
                                type: "POST",
                                success: function(data) {
                                    document.location.href = '/deal/index/' + data;
                                },
                                error: function() {
                                    brokerlive.notification
                                        .showError('Unable to create deal');
                                },
                                dataType: 'json'
                            });
                        }
                    });
                }
                if ($(this).attr('data-type') === 'assistant') {
                    bootbox.form({
                        'title': 'Deal details',
                        'fields': {
                            'name': {
                                'label': 'Deal name',
                                'type': 'text'
                            },
                            'broker': {
                                'label': 'Broker',
                                'type': 'select',
                                'options': brokers
                            }
                        },
                        callback: function(result){
                            if (result === null) {
                                return;
                            }
                            if (result['name'] === '') {
                                brokerlive.notification
                                    .showError('Cannot create a deal without a name.');
                                return;
                            }
                            $.ajax({
                                url: '/deal/create',
                                headers: {
                                    'X-CSRF-Token': $('meta[name="_token"]').attr('content')
                                },
                                data: {
                                    'deal-name': result['name'],
                                    'broker_id': result['broker']
                                },
                                type: "POST",
                                success: function(data) {
                                    document.location.href = '/deal/index/' + data;
                                },
                                error: function() {
                                    brokerlive.notification
                                        .showError('Unable to create deal');
                                },
                                dataType: 'json'
                            });
                        }
                    });
                }
            });          
        });
        return this;
    };
}( jQuery ));

$(document).ready(function(){
   $('.dealadd').dealadd(); 
});