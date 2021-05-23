<input type='hidden' id="sysMsg" value={{ $success ?? 'none' }} />
<script type="text/javascript">
    $(function () {
        var i = -1;
        var toastCount = 0;
        var $toastlast;
        var sysMsg = '';
        var _flag =  {{ $success ?? null }};
        var getMessage = function () {
            var msg = '';
            if(_flag){
                msg = 'Login Successed!';
            }else{
                msg = 'Login Failed!';
            }
            return msg;
        };

        var getType = function () {
            var type = '';
            if(_flag){
                type = 'success';
            }else{
                type = 'error';
            }
            return type;
        };

        if(_flag === null || _flag === undefined){

        }else{
            setTimeout(() => {
                msg = getMessage();    
                type = getType();
                toastr.options = {
                    "closeButton": true,
                    "debug": false,
                    "progressBar": true,
                    "preventDuplicates": false,
                    "positionClass": "toast-top-right",
                    "onclick": null,
                    "showDuration": "400",
                    "hideDuration": "1000",
                    "timeOut": "5000",
                    "extendedTimeOut": "1000",
                    "showEasing": "swing",
                    "hideEasing": "linear",
                    "showMethod": "fadeIn",
                    "hideMethod": "fadeOut"
                };
                var $toast = toastr[type](msg, 'System Message'); 
            }, 300);
        }

        $('#asfdasdf').click(function () {   
            
        });
        function getLastToast(){
            return $toastlast;
        }
        $('#clearlasttoast').click(function () {
            toastr.clear(getLastToast());
        });
    })
</script>