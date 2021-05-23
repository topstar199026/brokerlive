var brokerlive = brokerlive || {};
brokerlive.notification = new NotificationManager();

function NotificationManager() {
    
    this.options = {
        "closeButton": true,
        "debug": false,
        "progressBar": true,
        "positionClass": "toast-top-right",
        "onclick": null,
        "showDuration": "400",
        "hideDuration": "1000",
        "timeOut": "7000",
        "extendedTimeOut": "1000",
        "showEasing": "swing",
        "hideEasing": "linear",
        "showMethod": "fadeIn",
        "hideMethod": "fadeOut"
    }
    
    this.showMessage = function(notificationType, message) {
        toastr.options = this.options;
        switch(notificationType) {
            case "info":
                toastr['info'](message);
                break;
            case "success":
                toastr['success'](message);
                break;
            case "warning":
                toastr['warning'](message);
                break;
            case "error":
                toastr['error'](message);
                break;
        }
    };
    
    this.showInfo = function(message) {
        this.showMessage('info', message);
    };
    
    this.showSuccess = function(message) {
        this.showMessage('success', message);
    };
    
    this.showWarning = function(message) {
        this.showMessage('warning', message);
    };
    
    this.showError = function(message) {
        this.showMessage('error', message);
    };
}
//auto show message
if(typeof systemMessage == "object") {
    if(typeof systemMessage["type"] == "string" && typeof systemMessage["message"] == "string" && systemMessage["message"] != '') {
        if(systemMessage["type"] == "success") {
            brokerlive.notification.showSuccess(systemMessage["message"]);
        } else {
            brokerlive.notification.showError(systemMessage["message"]);
        }
    }
}

