<script type="text/javascript" src="/js/new_plugins/parsley/parsley.min.js?1591796224"></script>
<script type="text/javascript" src="/js/new_plugins/numeral/numeral.min.js"></script>
<script type="text/javascript" src="/js/new_plugins/uri/uri.js"></script>
<script type="text/javascript" src="/js/plugins/dropzone/dropzone.js"></script>
<script type="text/javascript" src="/js/new_plugins/sweetalert/sweetalert.min.js"></script>
<script type="text/javascript" src="/js/new_plugins/pupload/plupload.full.js"></script>
<script type="text/javascript" src="/js/brokerlive/brokerlive.userProfile.js"></script>

<script>
Dropzone.autoDiscover = false;
$(document).ready(function(){

    var myDropzone = new Dropzone("div#dropzoneForm",
        {
            url: "/configuration/profile/updateAvatar",
            paramName: "file",
            maxFilesize: 20, // MB
            addRemoveLinks: true,
            thumbnailWidth: 294,
            thumbnailHeight: 294,
            dictDefaultMessage: "<strong>Drop image here or click to upload. </strong></br> (File will be safe.)",
            init: function () {
                this.on("processing", function(file) {
                    $('.dz-image').css({"width":"294px", "height":"294px"});
                });
                this.on("uploadprogress", function(file, progress, bytesSent) {
                    $('.dz-image').last().find('img').attr({width: '294px', height: '294px'});
                    $('.dz-image').css({"width":"294px", "height":"294px"});
                });
                this.on("thumbnail", function(file, dataUrl) {
                    $('.dz-image').last().find('img').attr({width: '294px', height: '294px'});
                });
                this.on("success", function(file) {
                    $('.dz-image').css({"width":"294px", "height":"294px"});
                });
                this.on("sending", function (file, xhr, formData) {
                    formData.append("_token", $('meta[name="_token"]').attr('content'));
                    console.log(formData)
                });
                this.on("complete", function (file) {
                    brokerlive.notification.showSuccess('File saved');
                    $(".img-thumbnail").attr("src", "/configuration/profile/getAvatar?s=200");
                });
            }
        }
    );

    $('input.preferencetime').timepicker({'timeFormat': 'g:i a', 'interval': 15,
        dynamic: false,
        dropdown: true,
        scrollbar: true
    });
});



function _boolean(val){
    return val == 'true';
}

function modalPreferenceEdit(preference)
{
    console.log(preference)
    var name = preference.name.toLowerCase().replace(/\b[a-z]/g, function(letter) {
        return letter.toUpperCase();
    });

    if(preference.type == "BOOLEAN"){
        $("#BOOLEAN-value").prop('checked', _boolean(preference.value == null ? preference.default : preference.value));
        $("#BOOLEAN-key").val(preference.name);
    }

    if(preference.type == "NUMBER"){
        $("#NUMBER-value").val(preference.value == null ? preference.default : preference.value);
        $("#NUMBER-key").val(preference.name);
    }

    if(preference.type == "TIME"){
        // /console.log(moment(preference.value == null ? preference.default : preference.value, "HH:mm:ss").format('h:mm A').toString())
        $("#TIME-value").val(
            moment(preference.value == null ? preference.default : preference.value, "HH:mm:ss").format('hh:mm A').toString()
        );
        $("#TIME-key").val(preference.name);
    }

    $("#"+preference.type+"-Title").html("Change " + name + " Value");
}
</script>
