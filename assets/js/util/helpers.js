const H = (function () {
    var helpers = {};

    helpers.flattenObject = function flattenObject(ob, keyFactory) {
        if (keyFactory === void 0) {
            keyFactory = null;
        }
        if (keyFactory === null) {
            keyFactory = function (previousKey, currentKey) {
                return previousKey + '.' + currentKey;
            };
        }
        var toReturn = {};
        for (var i in ob) {
            if (!ob.hasOwnProperty(i)) {
                continue;
            }
            if (typeof ob[i] === 'object') {
                var flatObject = flattenObject(ob[i]);
                for (var x in flatObject) {
                    if (!flatObject.hasOwnProperty(x)) {
                        continue;
                    }
                    toReturn[keyFactory(i, x)] = flatObject[x];
                }
            } else {
                toReturn[i] = ob[i];
            }
        }
        return toReturn;
    };

    helpers.regExpEscape = function (s) {
        return s.replace(/[-\\^$*+?.()|[\]{}]/g, '\\$&');
    };

    helpers.startsWith = function (text, input) {
        return RegExp('^' + this.regExpEscape(input.trim()), 'i').test(text);
    };

    helpers.contains = function (text, input) {
        return RegExp(this.regExpEscape(input.trim()), 'i').test(text);
    };

    helpers.siblingIndex = function (el) {
        for (var i = 0; (el = el.previousElementSibling); i++);
        return i;
    };

    return helpers;
})();

export default H;
