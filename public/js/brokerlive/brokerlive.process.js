$.widget('brklive.processTable', {
    
    _url: "/data/v1/process",
    _filter: {},
      
    _create: function() {
        var _this = this;
        this.datatable = 
            this.element.dataTable( {
                "paginationType": "full_numbers",
                "filter": false,
                "info": false,
                "ajax": _this._url,
                "columns": [
                    {
                        "data": "name",
                        "render": function(data, type, row, meta) {
                            
                            return '<a href="/configuration/process/edit/' + row.id + '">' + row.name + '</a>';
                        }
                    },
                    {
                        "data": "stamp_created",
                        "render": function(data, type, row, meta) {

                            return '<a href="/configuration/process/edit/' + row.id + '">Edit</a> <br /><a href="/configuration/process/delete/' + row.id + '">Delete</a>';
                        }
                    }
                ]
            });
    },
    
    reload: function (url) {
        if (typeof url === "undefined") {
            this.datatable.api().ajax.reload();
        } else {
            this.datatable.api().ajax.url(url).load();
        }
    },
    
    filter : function (data) {
        var _this = this;
        $.extend(_this._filter, data);
        _this.reload(URI(_this._url).query(_this._filter).resource());
    }
});
function addSection() {
    var count = $(".tableSection").find(".section").length + 1;
    var section = $(".section-template").clone();
    section.removeClass("section-template").addClass("section").attr("id", "section" + count)
        .find(".section-name").val("Section" + count);
    $($(".tableSection").find("td")[0]).append(
        section
    );
}
$(document).ready(function(){
    $('table.table-process').processTable();
    $(".section-task-ul").sortable();
    $(".section-ul").sortable({connectWith: ".section-ul"});
    /*$(document).on("dragstart", '.section', function (event) {
        var dt = event.originalEvent.dataTransfer;
        dt.setData('Text', $(this).attr('id'));
    });
    $('.tableSection td').on("dragenter dragover drop", function (event) {
        event.preventDefault();
        if (event.type === 'drop') {
            var data = event.originalEvent.dataTransfer.getData('Text',$(this).attr('id'));
            de=$('#'+data).detach();
            de.appendTo($(this));
        };
    });
    $(document).on("dragstart", '.section-task-li', function (event) {
        event.stopPropagation();
        var dt = event.originalEvent.dataTransfer;
        dt.setData('Text', $(this).attr('id'));
    });
    $('.section-task-ul').on("dragenter dragover drop", function (event) {
        event.stopPropagation();
        event.preventDefault();
        if (event.type === 'drop') {
            var data = event.originalEvent.dataTransfer.getData('Text',$(this).attr('id'));
            de=$('#'+data).detach();
            de.appendTo($(this));
        };
    });*/
    $(document).on("keydown", '.section-add-task', function (event) {
        if(event.keyCode == 13) {
            event.preventDefault();
            var val = $(this).val();
            if(val != "") {
                $(this).val("");
                var count = $(this).parents(".section").find(".section-task ul li").length + 1;
                var task = $(".task-template").clone();
                task.removeClass("task-template").addClass("section-task-li").attr("id", "task_" + $(this).parents('.section').find(".section-name").val() + "_" + val)
                    .attr("value", val);
                task.find(".text").replaceWith(val);
                $(this).parents(".section").find(".section-task ul").append(task);

            }
            return false;
        }
    });
    $(document).on("click", '.section-remove', function (event) {
        id = $(this).parents(".section").attr("id").split("section")[1];
        $(this).parents(".section").remove();
    });
    $(document).on("click", '.task-remove', function (event) {
        $(this).parents("li").remove();
    });
    $("[name='btnSubmit']").click(function(e){
        var order = 0;
        var listSection = {};
        $(".section").each(function(ex){
            order++;
            listSection[order] = {
                id: $(this).find(".section-name").val(),
                dealStatus: $(this).parents("td").attr("deal-status")
            };
        });
        var json = JSON.stringify(listSection);
        $("[name='sections']").val(json);
        //get list task
        var listTask = {};
        order = 0;
        $(".section-task ul li").each(function(exx){
            order++;
            listTask[order] = {
                id: $(this).attr("value"),
                section: $(this).parents(".section").find(".section-name").val()
            };
        });
        json = JSON.stringify(listTask);
        $("[name='tasks']").val(json);
    });
});