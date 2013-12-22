 /*!
 * jQuery Cookie Plugin v1.4.0
 * https://github.com/carhartl/jquery-cookie
 *
 * Copyright 2013 Klaus Hartl
 * Released under the MIT license
 */
(function (factory) {
    if (typeof define === 'function' && define.amd) {
        // AMD. Register as anonymous module.
        define(['jquery'], factory);
    } else {
        // Browser globals.
        factory(jQuery);
    }
}(function ($) {

    var pluses = /\+/g;

    function encode(s) {
        return config.raw ? s : encodeURIComponent(s);
    }

    function decode(s) {
        return config.raw ? s : decodeURIComponent(s);
    }

    function stringifyCookieValue(value) {
        return encode(config.json ? JSON.stringify(value) : String(value));
    }

    function parseCookieValue(s) {
        if (s.indexOf('"') === 0) {
            // This is a quoted cookie as according to RFC2068, unescape...
            s = s.slice(1, -1).replace(/\\"/g, '"').replace(/\\\\/g, '\\');
        }

        try {
            // Replace server-side written pluses with spaces.
            // If we can't decode the cookie, ignore it, it's unusable.
            s = decodeURIComponent(s.replace(pluses, ' '));
        } catch(e) {
            return;
        }

        try {
            // If we can't parse the cookie, ignore it, it's unusable.
            return config.json ? JSON.parse(s) : s;
        } catch(e) {}
    }

    function read(s, converter) {
        var value = config.raw ? s : parseCookieValue(s);
        return $.isFunction(converter) ? converter(value) : value;
    }

    var config = $.cookie = function (key, value, options) {

        // Write
        if (value !== undefined && !$.isFunction(value)) {
            options = $.extend({}, config.defaults, options);

            if (typeof options.expires === 'number') {
                var days = options.expires, t = options.expires = new Date();
                t.setDate(t.getDate() + days);
            }

            return (document.cookie = [
                encode(key), '=', stringifyCookieValue(value),
                options.expires ? '; expires=' + options.expires.toUTCString() : '', // use expires attribute, max-age is not supported by IE
                options.path    ? '; path=' + options.path : '',
                options.domain  ? '; domain=' + options.domain : '',
                options.secure  ? '; secure' : ''
            ].join(''));
        }

        // Read

        var result = key ? undefined : {};

        // To prevent the for loop in the first place assign an empty array
        // in case there are no cookies at all. Also prevents odd result when
        // calling $.cookie().
        var cookies = document.cookie ? document.cookie.split('; ') : [];

        for (var i = 0, l = cookies.length; i < l; i++) {
            var parts = cookies[i].split('=');
            var name = decode(parts.shift());
            var cookie = parts.join('=');

            if (key && key === name) {
                // If second argument (value) is a function it's a converter...
                result = read(cookie, value);
                break;
            }

            // Prevent storing a cookie that we couldn't decode.
            if (!key && (cookie = read(cookie)) !== undefined) {
                result[name] = cookie;
            }
        }

        return result;
    };

    config.defaults = {};

    $.removeCookie = function (key, options) {
        if ($.cookie(key) !== undefined) {
            // Must not alter options, thus extending a fresh object...
            $.cookie(key, '', $.extend({}, options, { expires: -1 }));
            return true;
        }
        return false;
    };

}));
/*! Html5 Storage jQuery Plugin - v1.0 - 2013-01-19
 * https://github.com/artberri/jquery-html5storage
 * Copyright (c) 2013 Alberto Varela; Licensed MIT */
/*
* $.localStorage.setItem('key_name', 'Key Value');
* $.localStorage.getItem('key_name');
* $.localStorage.removeItem('key_name');
* $.localStorage.clear();
* var foo = {1: [1, 2, 3]};
* localStorage.setItem('foo', JSON.stringify(foo));
* var fooFromLS = JSON.parse(localStorage.getItem('foo'));
* */
 (function(e,t){"use strict";var n=["localStorage","sessionStorage"],r=[];t.each(n,function(n,i){try{r[i]=i in e&&e[i]!==null}catch(s){r[i]=!1}t[i]={settings:{cookiePrefix:"html5fallback:"+i+":",cookieOptions:{path:"/",domain:document.domain,expires:"localStorage"===i?{expires:365}:undefined}},getItem:function(n){var s;return r[i]?s=e[i].getItem(n):s=t.cookie(this.settings.cookiePrefix+n),s},setItem:function(n,s){return r[i]?e[i].setItem(n,s):t.cookie(this.settings.cookiePrefix+n,s,this.settings.cookieOptions)},removeItem:function(n){if(r[i])return e[i].removeItem(n);var s=t.extend(this.settings.cookieOptions,{expires:-1});return t.cookie(this.settings.cookiePrefix+n,null,s)},clear:function(){if(r[i])return e[i].clear();var n=new RegExp("^"+this.settings.cookiePrefix,""),s=t.extend(this.settings.cookieOptions,{expires:-1});document.cookie&&document.cookie!==""&&t.each(document.cookie.split(";"),function(e,r){n.test(r=t.trim(r))&&t.cookie(r.substr(0,r.indexOf("=")),null,s)})}}})})(window,jQuery);
