$.widget('brklive.aggregatorTable', {
    
    _url: "/data/v1/aggregator",
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
                            
                            return '<a href="/configuration/aggregator/edit/' + row.id + '">' + row.name + '</a>';
                        }
                    },
                    { "data": "stamp_created" },
                    { "data": "stamp_updated" }
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

$(document).ready(function(){
    $('table.table-aggregator').aggregatorTable();
});