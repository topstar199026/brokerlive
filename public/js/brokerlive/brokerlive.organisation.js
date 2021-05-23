$.widget('brklive.organisationTable', {
    
    _url: "/data/v1/organisation",
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
                            return '<a href="/configuration/organisation/edit/' + row.id + '">' + (row.legal_name !== '' ? row.legal_name : '...') + '</a>';
                        }
                    },
                    { "data": "trading_name" },
                    { "data": "short_name" },
                    { "data": "acn" },
                    { "data": "address_line1" },
                    { "data": "address_line2" },
                    { "data": "suburb" },
                    { "data": "state" },
                    { "data": "postcode" },
                    { "data": "country" },
                    { "data": "phone_number" },
                    { "data": "fax_number" }
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
    $('table.table-organisation').organisationTable();
    if($('#tree').length>0){
        $.ajax({
            url: "/data/v1/organisation",
            data: { tree: 1 },
            dataType: "JSON",
            success: function( msg ) {
                if(msg.status == "success") {
                    $('#tree').treeview({ data: transformData(msg.data) });
                }
            }
        });
    }

    function transformData(data) {
        for (var i = 0; i < data.length; i++) {
            if (data[i].text === '') {
                data[i].text = '...';
            }
            if (data.nodes) {
                data.nodes = transformData(data.nodes);
            }
        }
        return data;
    }
});
