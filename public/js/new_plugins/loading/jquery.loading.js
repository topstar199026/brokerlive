(function ($) {
    $.fn.mask = function (options) {

        return this.each(function () {
            var settings = $.extend({
                stop: false,
                mask: false,
                label: 'Loading...'
            }, options);

            if (!settings.stop) {
                var loadingDiv = $('<div class="ajax-mask"><div class="loading"><div class="loading-spinner"><i class="fa fa-spinner fa-spin"></i></div></div></div>')
                  .css({
                      'position': 'absolute',
                      'top': 0,
                      'left': 0,
                      'width': '100%',
                      'height': '100%'
                  });
                if (settings.label !== '') {
                    loadingDiv.find('div.loading').append('<div class="label">' + settings.label + '</div>');
                }
                if (settings.mask) {
                    loadingDiv.addClass('mask');
                }
                $(this).css({ 'position': 'relative' }).append(loadingDiv);
            } else {
                $('.ajax-mask').remove();
            }
        });
    };
})(jQuery);