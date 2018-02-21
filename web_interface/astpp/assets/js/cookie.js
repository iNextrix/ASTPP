/*
* jQuery Cookie Plugin 1.3.1
* https://github.com/blueimp/jQuery-Cookie
*
* Copyright 2010, Sebastian Tschan
* https://blueimp.net
*
* Licensed under the MIT license:
* http://creativecommons.org/licenses/MIT/
*
* Based on
* Cookie plugin
* Copyright (c) 2006 Klaus Hartl (stilbuero.de)
* http://plugins.jquery.com/files/jquery.cookie.js.txt
*/

/*jslint unparam: true */
/*global document, jQuery */

(function ($) {
    'use strict';
    
    var getCookie = function (key, options) {
            options = options || {};
            var result = new RegExp('(?:^|; )' + encodeURIComponent(key) +
                    '=([^;]*)').exec(document.cookie),
                decode = options.raw ? String : decodeURIComponent;
            return result !== null ? decode(result[1]) : null;
        },
        
        getCookies = function (options) {
            var cookies = document.cookie.split('; '),
                list = [];
            $.each(cookies, function (index, cookie) {
                var name = cookie.split('=')[0];
                if (name) {
                    list.push({name: name, value: getCookie(name, options)});
                }
            });
            return list;
        },

        setCookie = function (key, value, options) {
            options = options || {};
            if ($.type(options.path) === 'undefined') {
                options.path = '/';
            }
            if (value === null) {
                options.expires = -1;
                value = '';
            }
            if ($.type(options.expires) === 'number') {
                var days = options.expires;
                options.expires = new Date();
                options.expires.setDate(options.expires.getDate() + days);
            }
            return (document.cookie = [
                encodeURIComponent(key), '=',
                options.raw ? String(value) : encodeURIComponent(String(value)),
                options.expires ? '; expires=' + options.expires.toUTCString() : '',
                options.path ? '; path=' + options.path : '',
                options.domain ? '; domain=' + options.domain : '',
                options.secure ? '; secure' : ''
            ].join(''));
        };
    
    $.cookie = function (key, value, options) {
        if (arguments.length === 0 || $.type(key) === 'object') {
            return getCookies(key);
        }
        if (arguments.length > 1 && (value === null || $.type(value) !== 'object')) {
            return setCookie(key, value, options);
        }
        return getCookie(key, value);
    };

}(jQuery));
