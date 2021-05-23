var brokerlive = brokerlive || {};
// TODO: move this out to a separate file
brokerlive.Templates = function (names) {
    var loadSubscribers = [],
        readySubscribers = [],
        baseUrl = '/templates',
        ext = '.html';

    var self = {
        names: names,
        onLoad: subscribe(loadSubscribers),
        onReady: subscribe(readySubscribers),
        load: getTemplate,
        available: isAvailable,
        isReady: function() {
            return isAvailable(self.names);
        },

        render: function(name, data) {
            var args = Array.prototype.splice.call(arguments, 2);
            args.unshift(self[name], data);
            if (args.length > 2) {
                // we've got a nested template, try to resolve the name
                for (var templateName in args[2]) {
                    if (self[args[2][templateName]]) {
                        args[2][templateName] = self[args[2][templateName]];
                    }
                }
            }
            return Mustache.render.apply(null, args);
        }
    };

    //private
    function trigger(subscribers, arg1, arg2) {
        for (var i in subscribers) {
            if (typeof subscribers[i] === 'function') {
                subscribers[i](arg1, arg2);
            }
        }
    }

    function readyTriggerer(name, content) {
        self[name] = content;
        for (var name in names) {
            if (!self[names[name]]) {
                return;
            }
        }
        trigger(readySubscribers, this);
    }

    function subscribe(list) {
        return function (cb) {
            list.push(cb);
        }
    }

    //public
    function getTemplate(names) {
        if (typeof names === 'string') {
            names = [names];
        }

        for (var i in names) {
            var name = names[i];
            if (self[name]) {
                trigger(name, self[name]);
            }

            (function(name) {
                $.get(baseUrl + '/' + name + ext + '?_=' + new Date().getTime()).done(function (response) {
                    trigger(loadSubscribers, name, response);
                });
            })(name);
        }
        return self;
    }

    function isAvailable(names) {
        for (var key in names) {
            if (!self[names[key]]) {
                return false;
            }
        }
        return true;
    }

    subscribe(loadSubscribers)(readyTriggerer);
    getTemplate(names);

    return self;
};

brokerlive.helpers = {
    camelize: function (str) {
        // https://stackoverflow.com/questions/2970525/converting-any-string-into-camel-case
        return str.replace(/(?:^\w|[A-Z]|\b\w|\s+)/g, function(match, index) {
          if (+match === 0) return ""; // or if (/\s+/.test(match)) for white spaces
          return index == 0 ? match.toLowerCase() : match.toUpperCase();
        });
    },
    delay: function(callback, ms) {
      var timer = 0;
      return function() {
        var context = this, args = arguments;
        clearTimeout(timer);
        timer = setTimeout(function () {
          callback.apply(context, args);
        }, ms || 0);
      };
    }
};

brokerlive.config = {
  tinyMCE: {
    plugins: 'paste autosave',
    menubar: false,//'edit format autosave',
    toolbar: 'undo redo | bold italic underline | fontselect fontsizeselect | forecolor backcolor',
    autosave_ask_before_unload: true,
    width: '100%',
    height: 200,
    branding: false
  }
}

$(document).ready(function(){

    $( document ).ajaxError(function(event, jqXHR) {
        if (jqXHR.status === 401) {
            window.location.href = "http://www.brokerlive.com.au/authenticate/login";
        }
    });

    $.fn.dataTable.moment( 'DD/MM/YYYY' );
    $('[data-toggle="tooltip"]').tooltip();

    $('.navbar-expander').on('click', function(evt){
        evt.preventDefault();
        $("body").toggleClass("mini-navbar");
        SmoothlyMenu();

        $.ajax({
            url: '/user/preference/crumple',
            data: {
                'value': $('body').hasClass('mini-navbar')
            },
            type: "POST",
            dataType: 'json'
        });
    });
    $(".logout").click(function(e){
        e.preventDefault();
        clickedElement = this;
        swal({
            title: "You are going to log out. Proceed anyway?",
            text: "",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "Yes",
            closeOnConfirm: false
        }, function () {
            window.location.href = $(clickedElement).attr("href");
        });
    });
});
