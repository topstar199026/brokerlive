(function( $ ) {
    $.fn.widgetFiles = function() {
        this.each(function() {
            var elem = $( this );
            bind_fileListEvents(elem);

            var myDropzone = new Dropzone("div#dropzoneForm", 
                {
                    url: "/fileManagement/upload",
                    paramName: "file", // The name that will be used to transfer the file
                    maxFilesize: 2, // MB
                    dictDefaultMessage: "<strong>Drop files here or click to upload. </strong></br> (File will be safe.)",
                    init: function () {
                        this.on("sending", function (file, xhr, formData) {
                            formData.append("deal_id", $("input[name=deal_id]").val());
                            formData.append("_token", $('meta[name="_token"]').attr('content'));
                            console.log(formData)
                        });
                        this.on("complete", function (file) { 
                            brokerlive.notification
                                .showSuccess('File saved');
                            setTimeout(function () { myDropzone.removeFile(file); }, 1000);                            
                            get_fileList();
                        });
                    }
                }
            );

        });
        
        get_fileList();
        return this;
    }

    function bind_fileListEvents(elem){
        elem.on('click', 'a.link-delete', function(evt){
            var _this = this;
            evt.preventDefault();
            bootbox.confirm('Are you sure you want to delete file?', function(result){
                 if (result !== null) {
                    if (result === true) {
                        $.ajax({
                            url: $(_this).attr('href'),
                            type: 'GET'
                        })
                        .done(function(data){
                            brokerlive.notification
                                .showSuccess('File deleted');
                        })
                        .fail(function(data){
                            brokerlive.notification
                                .showError('Error deleting file');
                        })
                        .always(function(){
                            get_fileList();
                        });
                    }
                }
            });
        });        
    }

    function get_fileList() {
        var deal_id =  $("#deal-id").val();
        $.ajax({
            headers: {
                'X-CSRF-Token': $('meta[name="_token"]').attr('content')
            },
            url: '/fileManagement/getlist',
            data: {
                'deal_id': deal_id
            },
            type:"POST",
            success: function(msg)
            {
                $("#fileListDiv").html(msg);
            }
        });
    }

    function get_accessToken() {
        $.ajax({
            url: '/fileManagement/getlist',
            data: {
                'deal_id': deal_id
            },
            type:"POST",
            success: function(msg)
            {
                $("#fileListDiv").html(msg);
                bind_fileListEvents($("#fileListDiv"));
            }
        });
    }

}( jQuery ));
