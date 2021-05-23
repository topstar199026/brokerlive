	
var storage = new StorageManager();

function StorageManager() {

    this.clearAll = function () {
        localStorage.clear(); 
    };

    this.clear = function (startsWith) {
        var i = 0;
        var len = startsWith.length;
        while (i < localStorage.length) {
            var key = localStorage.key(i);

            if (key.length > len) {
                if (key.substr(0, len) === startsWith) {
                    localStorage.removeItem(key);
                    continue;
                }
            }
            i++;
        }
    };

    this.save = function (prefix, data) {
        var key = prefix;
        if (data) {
            if (data.Id) {
                key += ":" + data.Id;
            }
            var d = JSON.stringify(data);
            localStorage.setItem(key, d);
        } else {
            localStorage.removeItem(key);
        }
    };

    this.getItem = function (key) {
        var d = localStorage.getItem(key);
        if (d) {
            var result = JSON.parse(d);
            return result;
        } else {
            return null;
        }
    };
}