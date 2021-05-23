$.widget('brklive.report', {
    
    options: {
        storage: 'whiteboard.team_team.filter'
    },
    
    _filter: {},
    
    _create: function() {
        var _this = this;
        
        if (storage.getItem(this.options.storage) !== null) {
           _this._filter = storage.getItem(_this.options.storage);
        }
        this.resetPage();
    },
    
    resetPage : function () {
        var uri = new URI();
        if ((!$.isEmptyObject(this._filter)) && (uri.query() === '')) {
            this.reload();
        }
    },
    
    filter : function (data) { 
        var _this = this;
        if (data && data.constructor === Object) {
            $.extend(_this._filter, data);
        }
        return _this._filter;
    },
    
    filterReport : function (data) {
        var _this = this;
        $.extend(_this._filter, data);
        storage.save(_this.options.storage, _this._filter);
        _this.reload();
    },
    
    reload : function () {
        window.location.href = URI()
                .query(this._filter)
                .resource();
    }
});