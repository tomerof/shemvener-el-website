function _defineProperty(obj, key, value) { if (key in obj) { Object.defineProperty(obj, key, { value: value, enumerable: true, configurable: true, writable: true }); } else { obj[key] = value; } return obj; }

function _typeof(obj) { "@babel/helpers - typeof"; if (typeof Symbol === "function" && typeof Symbol.iterator === "symbol") { _typeof = function _typeof(obj) { return typeof obj; }; } else { _typeof = function _typeof(obj) { return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; }; } return _typeof(obj); }


(function ($) {
    $(window).bind("load", function () {
        if (getCookie('underlineLink') == 'true') $('.underline-link').click();
        if (getCookie('contrastDrak') == 'true') $('.contrast-dark').click();
        if (getCookie('greyScale') == 'true') $('.grayscale').click();
        if (getCookie('readblefont') == 'true') $('.readable-font').click();
        if (getCookie('fontSize') == '3') $('.font-size-bigger').click();
        if (getCookie('fontSize') == '1') $('.font-size-smaller').click();
    });

    $.fn.accessPlug = function (options) {
        var languages = [];
        languages["en"] = {
            title: "Accessibility",
            fontSize: "Font Size",
            aPlus: "larger Font",
            aMinus: "Smaller Font",
            readableFont: 'Readable Font',
            contrastDark: "Contrast Dark",
            grayscale: "Grayscale",
            underlineLinks: "Underline Links",
            skipMenu: "Skip Menu",
            reset: "Reset",
            contrastDarkTitle: "Colors Contrast",
            underlineLinkkTitle: "Links",
            credit: 'Created by YaelGroup'
        };
        languages["he"] = {
            title: "סרגל נגישות",
            fontSize: "גודל פונט",
            aPlus: "הגדל פונט",
            aMinus: "הקטן פונט",
            readableFont: 'פונט קריא',
            contrastDark: "ניגודיות גבוהה",
            grayscale: "גווני אפור",
            underlineLinks: "הדגשת קישורים",
            skipMenu: "דלג לתוכן",
            reset: "אפס הכל",
            contrastDarkTitle: "ניגודיות צבעים",
            underlineLinkkTitle: "קישורים",
            credit: 'נגישות מבית יעל תוכנה'
        };
        languages["ar"] = {
            title: 'مِسْطَرَة التّسهيل',
            fontSize: "حجم الخطّ",
            aPlus: 'تكبير الخطّ',
            aMinus: 'تصغير الخطّ',
            readableFont: 'خطّ مقروء',
            contrastDark: 'حِدَّة عالية',
            grayscale: 'أنواع الرّماديّ',
            underlineLinks: 'تشديد الرّوابط',
            skipMenu: 'انتقل إلى المضمون',
            reset: 'اضبط الكُلّ من جديد',
            contrastDarkTitle: 'حِدَّة الألوان',
            underlineLinkkTitle: 'روابط',
            credit: 'נגישות מבית יעל תוכנה'
        }; // Future home of "Hello, World!"

        var settings = $.extend({
            prefix: 'accessibility-menu',
            fontSize: true,
            readableFont: true,
            contrastDark: true,
            contrastLight: true,
            grayscale: true,
            underlineLink: true,
            skipMenu: true,
            reset: true,
            lang: 'en',
            mainContentSelector: '#main',
            title: '',
            credit: true,
            responsive: '',
            loadCss: false
        }, options);
        var fontSize = 2;

        return this.each(function () {
            var div = $('<div/>', {
                "class": settings.prefix + '-wrapper' + ' ' + settings.lang,
                id: 'access-container',
                tabindex: 0,
                style: "font-size: 16px;"
            });
            div.on('focusin', function () {
                var self = this;
                setTimeout(function () {
                    $(self).closest('#access-plug').addClass('menu-open');
                }, 200);
            });
            div.on('focusout', function () {
                var self = this;
                setTimeout(function () {
                    $(self).closest('#access-plug').removeClass('menu-open');
                }, 200);
            });
            div.on('click', function (e) {
                e.stopPropagation();
                $(toggle).click();
            });
            div.on('keydown', function (e) {
                translateKeyDownToClick(this, e);
            });
            var fontSize, readableFont, contrastDark, contrastLight, grayscale, skipMenu, toggle, self, fontSizeSmaller, fontSizeBigger;
            toggle = $('<div class="' + settings.prefix + '-view-toggle">' + languages[settings.lang].title + '<i class="icon-universal-access"></i> </div>');
            div.append(toggle);
            div.append('<div class="' + settings.prefix + '-conatiner"><ul class="' + settings.prefix + '"></ul></div>');
            var list = div.find('.' + settings.prefix);

            if (settings.skipMenu) {
                skipMenu = $('<li class="' + settings.prefix + '-item skip-menu" tabindex="0"><i class="fa fa-list-alt"></i> ' + languages[settings.lang].skipMenu + '</li>');
                list.append(skipMenu);
            }

            if (settings.fontSize) {
                fontSizeTitle = $('<li class="accessibility-menu-item title">' + languages[settings.lang].fontSize + '</li>');
                fontSizeBigger = $('<li class="' + settings.prefix + '-item font-size-bigger " tabindex="0"><i class="fa fa-plus-square"></i> ' + languages[settings.lang].aPlus + '</li>');
                fontSizeSmaller = $('<li class="' + settings.prefix + '-item font-size-smaller "  tabindex="0"><i class="fa fa-minus-square"></i> ' + languages[settings.lang].aMinus + '</li>');
                list.append(fontSizeTitle);
                list.append(fontSizeBigger);
                list.append(fontSizeSmaller);
            }

            if (settings.readableFont) {
                readableFont = $('<li class="' + settings.prefix + '-item readable-font" tabindex="0"><i class="fa fa-universal-access"></i> ' + languages[settings.lang].readableFont + '</li>');
                list.append(readableFont);
            }

            if (settings.contrastDark) {
                contrastDarkTitle = $('<li class="accessibility-menu-item title">' + languages[settings.lang].contrastDarkTitle + '</li>');
                contrastDark = $('<li class="' + settings.prefix + '-item contrast-dark" tabindex="0"><i class="fa fa-adjust"></i> ' + languages[settings.lang].contrastDark + '</li>');
                list.append(contrastDarkTitle);
                list.append(contrastDark);
            }

            if (settings.contrastLight) {
                contrastLight = $('<li class="' + settings.prefix + '-item contrast-light" tabindex="0"><i class="fa fa-sun-o"></i> ' + languages[settings.lang].contrastLight + '</li>');
                list.append(contrastLight);
            }

            if (settings.grayscale) {
                grayscale = $('<li class="' + settings.prefix + '-item grayscale" tabindex="0"><i class="fa fa-universal-access"></i> ' + languages[settings.lang].grayscale + '</li>');
                list.append(grayscale);
            }

            if (settings.underlineLink) {
                underlineLinkkTitle = $('<li class="accessibility-menu-item title">' + languages[settings.lang].underlineLinkkTitle + '</li>');
                underlineLink = $('<li class="' + settings.prefix + '-item underline-link" tabindex="0"><i class="fa fa-paint-brush"></i> ' + languages[settings.lang].underlineLinks + '</li>');
                list.append(underlineLinkkTitle);
                list.append(underlineLink);
            }

            if (settings.reset) {
                resetSetting = $('<li class="' + settings.prefix + '-item reset-setting" tabindex="0"><i class="fa fa-refresh"></i> ' + languages[settings.lang].reset + '</li>');
                list.append(resetSetting);
            }

            if (true) {
                credit = $('<li class="accessibility-menu-item credit"><i class="yael-logo"></i> ' + languages[settings.lang].credit + '</li>');
                list.append(credit);
            }

            if (settings.responsive) {
                var mobilePos = settings.responsive.mobilePos;
                var desktopPos = settings.responsive.desktopPos;
                setResponsivePosition(mobilePos, desktopPos);
            }

            if (settings.loadCss) {
                $('body').append('<link rel="stylesheet" href="http://dev.intigo.co.il/cdn/accessibility/access-plugin.css" />');
            }

            $(list).children().on('keydown', function (e) {
                translateKeyDownToClick(this, e);
            }); // $(fontCon).children().on('keydown',function(e){
            //     translateKeyDownToClick(this,e);
            // });

            function translateKeyDownToClick(self, e) {
                e.stopPropagation();
                e.stopImmediatePropagation();

                switch (e.keyCode) {
                    case 13:
                        $(self).trigger("click");
                        break;

                    case 32:
                        $(self).trigger("click");
                        break;

                    default:
                }
            }

            div.appendTo(this);
            toggle.on('click', toggleMenu); //fontSize

            resetSetting.on('click', reset);
            readableFont.on('click', readblefont);
            contrastDark.on('click', contrastdark);
            grayscale.on('click', greyscalef);
            underlineLink.on('click', underlinef);
            fontSizeBigger.on('click', biggerFont);
            fontSizeSmaller.on('click', smallerFont);
            skipMenu.on('click', {
                "extra": settings.mainContentSelector
            }, skipMenuF);
            fontSizeTitle.on('click', function (e) {
                e.stopPropagation();
            });
            contrastDarkTitle.on('click', function (e) {
                e.stopPropagation();
            });
            underlineLinkkTitle.on('click', function (e) {
                e.stopPropagation();
            });
            credit.on('click', function (e) {
                e.stopPropagation();
            });
        });

        function debug() {
            console.log(this);
        }

        function toggleMenu(e, self) {
            e.stopPropagation();
            $(this).closest('#access-plug').toggleClass('menu-open');
        }

        function contrastdark(e) {
            e.stopPropagation();

            if (!$('body').hasClass('contrast-css')) {
                contrastDrakOn(e);
            } else {
                contrastDrakOff(e);
            }
        }

        function contrastDrakOff(e) {
            e.stopPropagation();
            $('.accessibility-menu-item.contrast-dark').removeClass('active');
            $('body').removeClass('contrast-css');
            deleteCookie('contrastDrak');
        }

        function contrastDrakOn(e) {
            e.preventDefault();
            e.stopPropagation();
            contrastLightOff(e);
            greyScaleOff(e);
            $('.accessibility-menu-item.contrast-dark').addClass('active');
            $('body').addClass('contrast-css');
            setCookie('contrastDrak', 'true');
        }

        function greyscalef(e) {
            e.stopPropagation();

            if (!$('body').hasClass('grayscale-css')) {
                greyScaleOn(e);
            } else {
                greyScaleOff(e);
            }
        }

        function biggerFont(e) {
            e.stopPropagation();

            if (fontSize == 1) {
                $('html').css('font-size', '10px');
                fontSize = 2;
                deleteCookie('fontSize');
                setCookie('fontSize', '2');
            } else if (fontSize == 2) {
                $('html').css('font-size', '12px');
                fontSize = 3;
                deleteCookie('fontSize');
                setCookie('fontSize', '3');
            } else {
                return;
            }
        }

        function smallerFont(e) {
            e.stopPropagation();

            if (fontSize == 3) {
                $('html').css('font-size', '10px');
                fontSize = 2;
                deleteCookie('fontSize');
                setCookie('fontSize', '2');
            } else if (fontSize == 2) {
                $('html').css('font-size', '8px');
                fontSize = 1;
                deleteCookie('fontSize');
                setCookie('fontSize', '1');
            } else {
                return;
            }
        }

        function resetFont(e) {
            e.stopPropagation();
            fontSize = 2;
            $('html').css('font-size', '10px');
            deleteCookie('fontSize');
        }

        function greyScaleOn(e) {
            e.preventDefault();
            e.stopPropagation();
            contrastLightOff(e);
            contrastDrakOff(e);
            $('.accessibility-menu-item.grayscale').addClass('active');
            $('body').addClass('grayscale-css');
            setCookie('greyScale', 'true');
        }

        function greyScaleOff(e) {
            e.stopPropagation();
            $('.accessibility-menu-item.grayscale').removeClass('active');
            $('body').removeClass('grayscale-css');
            deleteCookie('greyScale');
        }

        function contrastLight(e) {
            e.stopPropagation();

            if ($('body').hasClass('acp-bright')) {
                contrastLightOn();
            } else {
                contrastLightOff();
            }
        }

        function contrastLightOff(e) {
            e.stopPropagation();
            $('.accessibility-menu-item.contrast-light').removeClass('active');
            $('body').removeClass('acp-bright');
        }

        function contrastLightOn(e) {
            e.stopPropagation();
            contrastDrakOff(e);
            greyScaleOff(e);
            $('.accessibility-menu-item.contrast-light').addClass('active');
            $('body').addClass('acp-bright');
        }

        function readblefont(e) {
            e.stopPropagation();
            if (!$('body').hasClass('readable-font')) readableFontOn(); else {
                readableFontOff();
            }
        }

        function readableFontOn(e) {
            $('body').addClass('readable-font');
            $('.accessibility-menu-item.readable-font').addClass('active');
            setCookie('readblefont', 'true');
        }

        function readableFontOff(e) {
            $('body').removeClass('readable-font');
            $('.accessibility-menu-item.readable-font').removeClass('active');
            deleteCookie('readblefont');
        }

        function underlinef(e) {
            e.stopPropagation();

            if (!$('body').hasClass('underline-link')) {
                underlineLinkOn(e);
            } else {
                underlinelinkOff(e);
            }
        }

        function underlineLinkOn(e) {
            e.stopPropagation();
            $('.accessibility-menu-item.underline-link').addClass('active');
            $('body').addClass('underline-link');
            setCookie('underlineLink', 'true');
        }

        function underlinelinkOff(e) {
            e.stopPropagation();
            $('.accessibility-menu-item.underline-link').removeClass('active');
            $('body').removeClass('underline-link');
            deleteCookie('underlineLink');
        }

        function skipMenuF(e) {
            e.stopPropagation();
            $('body,html').animate({
                scrollTop: $(e.data.extra).position().top
            }, 200);
            $(e.data.extra).attr('tabindex', '0');
            $(e.data.extra).focus();
        }

        function reset(e) {
            underlinelinkOff(e);
            resetFont(e);
            contrastDrakOff(e);
            contrastLightOff(e);
            greyScaleOff(e);
            readableFontOff(e);
            deleteCookie('underlineLink');
            deleteCookie('fontSize');
            deleteCookie('contrastDrak');
            deleteCookie('greyScale');
            deleteCookie('readblefont');
        }

        function setResponsivePosition(mobilePos, desktopPos) {
            $(window).on('resize load', function () {
                if ($(window).width() <= 768) {
                    $("#access-plug").prependTo(mobilePos);
                } else {
                    $("#access-plug").prependTo(desktopPos);
                }
            });
        }
    };
})(jQuery);

/**************************************************************************/
// Get cookie Function

var getCookie = function getCookie(cname) {
    var name = cname + "=";
    var ca = document.cookie.split(';');

    for (var i = 0; i < ca.length; i++) {
        var c = ca[i];

        while (c.charAt(0) == ' ') {
            c = c.substring(1);
        }

        if (c.indexOf(name) == 0) {
            return c.substring(name.length, c.length);
        }
    }
 
    return "";
}; // Set cookie Function

/*function setCookie(cname, cvalue) {
    document.cookie = cname +'='+ cvalue +'; Path=/;';
}*/

function setCookie(cname, cvalue, session) {
    var d = new Date();
    d.setTime(d.getTime() + 1 * 24 * 60 * 60 * 1000);
    var expires = session ? "" : "expires=" + d.toUTCString();
    document.cookie = cname + "=" + cvalue + "; Path=/;" + expires;
} // Delete cookie Function

function deleteCookie(cname) {
    document.cookie = cname + '=;expires=Thu, 01 Jan 1970 00:00:01 GMT; Path=/;';
}

/**
* jquery-match-height 0.7.0 by @liabru
* http://brm.io/jquery-match-height/
* License: MIT
*/

;

(function (factory) {
    // eslint-disable-line no-extra-semi
    'use strict';

    if (typeof define === 'function' && define.amd) {
        // AMD
        define(['jquery'], factory);
    } else if (typeof module !== 'undefined' && module.exports) {
        // CommonJS
        module.exports = factory(require('jquery'));
    } else {
        // Global
        factory(jQuery);
    }
})(function ($) {
    /*
    *  internal
    */
    var _previousResizeWidth = -1,
        _updateTimeout = -1;
    /*
    *  _parse
    *  value parse utility function
    */

    var _parse = function _parse(value) {
        // parse value and convert NaN to 0
        return parseFloat(value) || 0;
    };
    /*
    *  _rows
    *  utility function returns array of jQuery selections representing each row
    *  (as displayed after float wrapping applied by browser)
    */

    var _rows = function _rows(elements) {
        var tolerance = 1,
            $elements = $(elements),
            lastTop = null,
            rows = []; // group elements by their top position

        $elements.each(function () {
            var $that = $(this),
                top = $that.offset().top - _parse($that.css('margin-top')),
                lastRow = rows.length > 0 ? rows[rows.length - 1] : null;

            if (lastRow === null) {
                // first item on the row, so just push it
                rows.push($that);
            } else {
                // if the row top is the same, add to the row group
                if (Math.floor(Math.abs(lastTop - top)) <= tolerance) {
                    rows[rows.length - 1] = lastRow.add($that);
                } else {
                    // otherwise start a new row group
                    rows.push($that);
                }
            } // keep track of the last row top

            lastTop = top;
        });
        return rows;
    };
    /*
    *  _parseOptions
    *  handle plugin options
    */

    var _parseOptions = function _parseOptions(options) {
        var opts = {
            byRow: true,
            property: 'height',
            target: null,
            remove: false
        };

        if (_typeof(options) === 'object') {
            return $.extend(opts, options);
        }

        if (typeof options === 'boolean') {
            opts.byRow = options;
        } else if (options === 'remove') {
            opts.remove = true;
        }

        return opts;
    };
    /*
    *  matchHeight
    *  plugin definition
    */

    var matchHeight = $.fn.matchHeight = function (options) {
        var opts = _parseOptions(options); // handle remove

        if (opts.remove) {
            var that = this; // remove fixed height from all selected elements

            this.css(opts.property, ''); // remove selected elements from all groups

            $.each(matchHeight._groups, function (key, group) {
                group.elements = group.elements.not(that);
            }); // TODO: cleanup empty groups

            return this;
        }

        if (this.length <= 1 && !opts.target) {
            return this;
        } // keep track of this group so we can re-apply later on load and resize events

        matchHeight._groups.push({
            elements: this,
            options: opts
        }); // match each element's height to the tallest element in the selection

        matchHeight._apply(this, opts);

        return this;
    };
    /*
    *  plugin global options
    */

    matchHeight.version = '0.7.0';
    matchHeight._groups = [];
    matchHeight._throttle = 80;
    matchHeight._maintainScroll = false;
    matchHeight._beforeUpdate = null;
    matchHeight._afterUpdate = null;
    matchHeight._rows = _rows;
    matchHeight._parse = _parse;
    matchHeight._parseOptions = _parseOptions;
    /*
    *  matchHeight._apply
    *  apply matchHeight to given elements
    */

    matchHeight._apply = function (elements, options) {
        var opts = _parseOptions(options),
            $elements = $(elements),
            rows = [$elements]; // take note of scroll position

        var scrollTop = $(window).scrollTop(),
            htmlHeight = $('html').outerHeight(true); // get hidden parents

        var $hiddenParents = $elements.parents().filter(':hidden'); // cache the original inline style

        $hiddenParents.each(function () {
            var $that = $(this);
            $that.data('style-cache', $that.attr('style'));
        }); // temporarily must force hidden parents visible

        $hiddenParents.css('display', 'block'); // get rows if using byRow, otherwise assume one row

        if (opts.byRow && !opts.target) {
            // must first force an arbitrary equal height so floating elements break evenly
            $elements.each(function () {
                var $that = $(this),
                    display = $that.css('display'); // temporarily force a usable display value

                if (display !== 'inline-block' && display !== 'flex' && display !== 'inline-flex') {
                    display = 'block';
                } // cache the original inline style

                $that.data('style-cache', $that.attr('style'));
                $that.css({
                    'display': display,
                    'padding-top': '0',
                    'padding-bottom': '0',
                    'margin-top': '0',
                    'margin-bottom': '0',
                    'border-top-width': '0',
                    'border-bottom-width': '0',
                    'height': '100px',
                    'overflow': 'hidden'
                });
            }); // get the array of rows (based on element top position)

            rows = _rows($elements); // revert original inline styles

            $elements.each(function () {
                var $that = $(this);
                $that.attr('style', $that.data('style-cache') || '');
            });
        }

        $.each(rows, function (key, row) {
            var $row = $(row),
                targetHeight = 0;

            if (!opts.target) {
                // skip apply to rows with only one item
                if (opts.byRow && $row.length <= 1) {
                    $row.css(opts.property, '');
                    return;
                } // iterate the row and find the max height

                $row.each(function () {
                    var $that = $(this),
                        style = $that.attr('style'),
                        display = $that.css('display'); // temporarily force a usable display value

                    if (display !== 'inline-block' && display !== 'flex' && display !== 'inline-flex') {
                        display = 'block';
                    } // ensure we get the correct actual height (and not a previously set height value)

                    var css = {
                        'display': display
                    };
                    css[opts.property] = '';
                    $that.css(css); // find the max height (including padding, but not margin)

                    if ($that.outerHeight(false) > targetHeight) {
                        targetHeight = $that.outerHeight(false);
                    } // revert styles

                    if (style) {
                        $that.attr('style', style);
                    } else {
                        $that.css('display', '');
                    }
                });
            } else {
                // if target set, use the height of the target element
                targetHeight = opts.target.outerHeight(false);
            } // iterate the row and apply the height to all elements

            $row.each(function () {
                var $that = $(this),
                    verticalPadding = 0; // don't apply to a target

                if (opts.target && $that.is(opts.target)) {
                    return;
                } // handle padding and border correctly (required when not using border-box)

                if ($that.css('box-sizing') !== 'border-box') {
                    verticalPadding += _parse($that.css('border-top-width')) + _parse($that.css('border-bottom-width'));
                    verticalPadding += _parse($that.css('padding-top')) + _parse($that.css('padding-bottom'));
                } // set the height (accounting for padding and border)

                $that.css(opts.property, targetHeight - verticalPadding + 'px');
            });
        }); // revert hidden parents

        $hiddenParents.each(function () {
            var $that = $(this);
            $that.attr('style', $that.data('style-cache') || null);
        }); // restore scroll position if enabled

        if (matchHeight._maintainScroll) {
            $(window).scrollTop(scrollTop / htmlHeight * $('html').outerHeight(true));
        }

        return this;
    };
    /*
    *  matchHeight._applyDataApi
    *  applies matchHeight to all elements with a data-match-height attribute
    */

    matchHeight._applyDataApi = function () {
        var groups = {}; // generate groups by their groupId set by elements using data-match-height

        $('[data-match-height], [data-mh]').each(function () {
            var $this = $(this),
                groupId = $this.attr('data-mh') || $this.attr('data-match-height');

            if (groupId in groups) {
                groups[groupId] = groups[groupId].add($this);
            } else {
                groups[groupId] = $this;
            }
        }); // apply matchHeight to each group

        $.each(groups, function () {
            this.matchHeight(true);
        });
    };
    /*
    *  matchHeight._update
    *  updates matchHeight on all current groups with their correct options
    */

    var _update = function _update(event) {
        if (matchHeight._beforeUpdate) {
            matchHeight._beforeUpdate(event, matchHeight._groups);
        }

        $.each(matchHeight._groups, function () {
            matchHeight._apply(this.elements, this.options);
        });

        if (matchHeight._afterUpdate) {
            matchHeight._afterUpdate(event, matchHeight._groups);
        }
    };

    matchHeight._update = function (throttle, event) {
        // prevent update if fired from a resize event
        // where the viewport width hasn't actually changed
        // fixes an event looping bug in IE8
        if (event && event.type === 'resize') {
            var windowWidth = $(window).width();

            if (windowWidth === _previousResizeWidth) {
                return;
            }

            _previousResizeWidth = windowWidth;
        } // throttle updates

        if (!throttle) {
            _update(event);
        } else if (_updateTimeout === -1) {
            _updateTimeout = setTimeout(function () {
                _update(event);

                _updateTimeout = -1;
            }, matchHeight._throttle);
        }
    };
    /*
    *  bind events
    */
    // apply on DOM ready event

    $(matchHeight._applyDataApi); // update heights on load and resize events

    $(window).bind('load', function (event) {
        matchHeight._update(false, event);
    }); // throttled update heights on resize events

    $(window).bind('resize orientationchange', function (event) {
        matchHeight._update(true, event);
    });
});
/*
 jCanvas v16.07.03
 Copyright 2016 Caleb Evans
 Released under the MIT license
*/
(function (g, U, J) {
    "object" === (typeof module === "undefined" ? "undefined" : _typeof(module)) && "object" === _typeof(module.exports) ? module.exports = function (g, U) {
        return J(g, U);
    } : J(g, U);
})("undefined" !== typeof window ? window.jQuery : {}, "undefined" !== typeof window ? window : void 0, function (g, U) {
    function J(d) {
        for (var c in d) {
            d.hasOwnProperty(c) && (this[c] = d[c]);
        }

        return this;
    }

    function na() {
        Z(this, na.baseDefaults);
    }

    function ja(d) {
        return "string" === aa(d);
    }

    function va(d) {
        return !isNaN(wa(d)) && !isNaN(ba(d));
    }

    function L(d) {
        return d && d.getContext ? d.getContext("2d") : null;
    }

    function ka(d) {
        var c, a, b;

        for (c in d) {
            d.hasOwnProperty(c) && (b = d[c], a = aa(b), "string" === a && va(b) && "text" !== c && (d[c] = ba(b)));
        }

        void 0 !== d.text && (d.text = String(d.text));
    }

    function la(d) {
        d = Z({}, d);
        d.masks = d.masks.slice(0);
        return d;
    }

    function fa(d, c) {
        var a;
        d.save();
        a = la(c.transforms);
        c.savedTransforms.push(a);
    }

    function xa(d, c, a, b) {
        a[b] && (da(a[b]) ? c[b] = a[b].call(d, a) : c[b] = a[b]);
    }

    function R(d, c, a) {
        xa(d, c, a, "fillStyle");
        xa(d, c, a, "strokeStyle");
        c.lineWidth = a.strokeWidth;
        a.rounded ? c.lineCap = c.lineJoin = "round" : (c.lineCap = a.strokeCap, c.lineJoin = a.strokeJoin, c.miterLimit = a.miterLimit);
        a.strokeDash || (a.strokeDash = []);
        c.setLineDash && c.setLineDash(a.strokeDash);
        c.webkitLineDash = a.strokeDash;
        c.lineDashOffset = c.webkitLineDashOffset = c.mozDashOffset = a.strokeDashOffset;
        c.shadowOffsetX = a.shadowX;
        c.shadowOffsetY = a.shadowY;
        c.shadowBlur = a.shadowBlur;
        c.shadowColor = a.shadowColor;
        c.globalAlpha = a.opacity;
        c.globalCompositeOperation = a.compositing;
        a.imageSmoothing && (c.imageSmoothingEnabled = c.mozImageSmoothingEnabled = a.imageSmoothingEnabled);
    }

    function ya(d, c, a) {
        a.mask && (a.autosave && fa(d, c), d.clip(), c.transforms.masks.push(a._args));
    }

    function W(d, c, a) {
        a.closed && c.closePath();
        a.shadowStroke && 0 !== a.strokeWidth ? (c.stroke(), c.fill(), c.shadowColor = "transparent", c.shadowBlur = 0, c.stroke()) : (c.fill(), "transparent" !== a.fillStyle && (c.shadowColor = "transparent"), 0 !== a.strokeWidth && c.stroke());
        a.closed || c.closePath();
        a._transformed && c.restore();
        a.mask && (d = H(d), ya(c, d, a));
    }

    function Q(d, c, a, b, f) {
        a._toRad = a.inDegrees ? E / 180 : 1;
        a._transformed = !0;
        c.save();
        a.fromCenter || a._centered || void 0 === b || (void 0 === f && (f = b), a.x += b / 2, a.y += f / 2, a._centered = !0);
        a.rotate && za(c, a, null);
        1 === a.scale && 1 === a.scaleX && 1 === a.scaleY || Aa(c, a, null);
        (a.translate || a.translateX || a.translateY) && Ba(c, a, null);
    }

    function H(d) {
        var c = ca.dataCache,
            a;
        c._canvas === d && c._data ? a = c._data : (a = g.data(d, "jCanvas"), a || (a = {
            canvas: d,
            layers: [],
            layer: {
                names: {},
                groups: {}
            },
            eventHooks: {},
            intersecting: [],
            lastIntersected: null,
            cursor: g(d).css("cursor"),
            drag: {
                layer: null,
                dragging: !1
            },
            event: {
                type: null,
                x: null,
                y: null
            },
            events: {},
            transforms: la(oa),
            savedTransforms: [],
            animating: !1,
            animated: null,
            pixelRatio: 1,
            scaled: !1
        }, g.data(d, "jCanvas", a)), c._canvas = d, c._data = a);
        return a;
    }

    function Ca(d, c, a) {
        for (var b in Y.events) {
            Y.events.hasOwnProperty(b) && (a[b] || a.cursors && a.cursors[b]) && Da(d, c, a, b);
        }

        c.events.mouseout || (d.bind("mouseout.jCanvas", function () {
            var a = c.drag.layer,
                b;
            a && (c.drag = {}, O(d, c, a, "dragcancel"));

            for (b = 0; b < c.layers.length; b += 1) {
                a = c.layers[b], a._hovered && d.triggerLayerEvent(c.layers[b], "mouseout");
            }

            d.drawLayers();
        }), c.events.mouseout = !0);
    }

    function Da(d, c, a, b) {
        Y.events[b](d, c);
        a._event = !0;
    }

    function Ea(d, c, a) {
        var b, f, e;

        if (a.draggable || a.cursors) {
            b = ["mousedown", "mousemove", "mouseup"];

            for (e = 0; e < b.length; e += 1) {
                f = b[e], Da(d, c, a, f);
            }

            a._event = !0;
        }
    }

    function pa(d, c, a, b) {
        d = c.layer.names;
        b ? void 0 !== b.name && ja(a.name) && a.name !== b.name && delete d[a.name] : b = a;
        ja(b.name) && (d[b.name] = a);
    }

    function qa(d, c, a, b) {
        d = c.layer.groups;
        var f, e, h, g;
        if (!b) b = a; else if (void 0 !== b.groups && null !== a.groups) for (e = 0; e < a.groups.length; e += 1) {
            if (f = a.groups[e], c = d[f]) {
                for (g = 0; g < c.length; g += 1) {
                    if (c[g] === a) {
                        h = g;
                        c.splice(g, 1);
                        break;
                    }
                }

                0 === c.length && delete d[f];
            }
        }
        if (void 0 !== b.groups && null !== b.groups) for (e = 0; e < b.groups.length; e += 1) {
            f = b.groups[e], c = d[f], c || (c = d[f] = [], c.name = f), void 0 === h && (h = c.length), c.splice(h, 0, a);
        }
    }

    function ra(d, c, a, b, f) {
        b[a] && c._running && !c._running[a] && (c._running[a] = !0, b[a].call(d[0], c, f), c._running[a] = !1);
    }

    function O(d, c, a, b, f) {
        if (!(a.disableEvents || a.intangible && -1 !== g.inArray(b, Ua))) {
            if ("mouseout" !== b) {
                var e;
                a.cursors && (e = a.cursors[b]);
                -1 !== g.inArray(e, V.cursors) && (e = V.prefix + e);
                e && d.css({
                    cursor: e
                });
            }

            ra(d, a, b, a, f);
            ra(d, a, b, c.eventHooks, f);
            ra(d, a, b, Y.eventHooks, f);
        }
    }

    function N(d, c, a, b) {
        var f,
            e = c._layer ? a : c;
        c._args = a;
        if (c.draggable || c.dragGroups) c.layer = !0, c.draggable = !0;
        c._method || (c._method = b ? b : c.method ? g.fn[c.method] : c.type ? g.fn[X.drawings[c.type]] : function () { });

        if (c.layer && !c._layer) {
            if (a = g(d), b = H(d), f = b.layers, null === e.name || ja(e.name) && void 0 === b.layer.names[e.name]) ka(c), e = new J(c), e.canvas = d, e.layer = !0, e._layer = !0, e._running = {}, e.data = null !== e.data ? Z({}, e.data) : {}, e.groups = null !== e.groups ? e.groups.slice(0) : [], pa(a, b, e), qa(a, b, e), Ca(a, b, e), Ea(a, b, e), c._event = e._event, e._method === g.fn.drawText && a.measureText(e), null === e.index && (e.index = f.length), f.splice(e.index, 0, e), c._args = e, O(a, b, e, "add");
        } else c.layer || ka(c);

        return e;
    }

    function Fa(d, c) {
        var a, b;

        for (b = 0; b < V.props.length; b += 1) {
            a = V.props[b], void 0 !== d[a] && (d["_" + a] = d[a], V.propsObj[a] = !0, c && delete d[a]);
        }
    }

    function Va(d, c, a) {
        var b, f, e, h;

        for (b in a) {
            if (a.hasOwnProperty(b) && (f = a[b], da(f) && (a[b] = f.call(d, c, b)), "object" === aa(f) && Ga(f))) {
                for (e in f) {
                    f.hasOwnProperty(e) && (h = f[e], void 0 !== c[b] && (c[b + "." + e] = c[b][e], a[b + "." + e] = h));
                }

                delete a[b];
            }
        }

        return a;
    }

    function Ha(d) {
        var c,
            a,
            b = [],
            f = 1;
        "transparent" === d ? d = "rgba(0, 0, 0, 0)" : d.match(/^([a-z]+|#[0-9a-f]+)$/gi) && (a = Ia.head, c = a.style.color, a.style.color = d, d = g.css(a, "color"), a.style.color = c);
        d.match(/^rgb/gi) && (b = d.match(/(\d+(\.\d+)?)/gi), d.match(/%/gi) && (f = 2.55), b[0] *= f, b[1] *= f, b[2] *= f, b[3] = void 0 !== b[3] ? ba(b[3]) : 1);
        return b;
    }

    function Wa(d) {
        var c = 3,
            a;
        "array" !== aa(d.start) && (d.start = Ha(d.start), d.end = Ha(d.end));
        d.now = [];
        if (1 !== d.start[3] || 1 !== d.end[3]) c = 4;

        for (a = 0; a < c; a += 1) {
            d.now[a] = d.start[a] + (d.end[a] - d.start[a]) * d.pos, 3 > a && (d.now[a] = Xa(d.now[a]));
        }

        1 !== d.start[3] || 1 !== d.end[3] ? d.now = "rgba( " + d.now.join(",") + " )" : (d.now.slice(0, 3), d.now = "rgb( " + d.now.join(",") + " )");
        d.elem.nodeName ? d.elem.style[d.prop] = d.now : d.elem[d.prop] = d.now;
    }

    function Ya(d) {
        X.touchEvents[d] && (d = X.touchEvents[d]);
        return d;
    }

    function Za(d) {
        Y.events[d] = function (c, a) {
            function b(a) {
                h.x = a.offsetX;
                h.y = a.offsetY;
                h.type = f;
                h.event = a;
                c.drawLayers({
                    resetFire: !0
                });
                a.preventDefault();
            }

            var f, e, h;
            h = a.event;
            f = "mouseover" === d || "mouseout" === d ? "mousemove" : d;
            e = Ya(f);
            a.events[f] || (e !== f ? c.bind(f + ".jCanvas " + e + ".jCanvas", b) : c.bind(f + ".jCanvas", b), a.events[f] = !0);
        };
    }

    function T(d, c, a) {
        var b, f, e, h;
        if (a = a._args) d = H(d), b = d.event, null !== b.x && null !== b.y && (e = b.x * d.pixelRatio, h = b.y * d.pixelRatio, f = c.isPointInPath(e, h) || c.isPointInStroke && c.isPointInStroke(e, h)), c = d.transforms, a.eventX = b.x, a.eventY = b.y, a.event = b.event, b = d.transforms.rotate, e = a.eventX, h = a.eventY, 0 !== b ? (a._eventX = e * M(-b) - h * P(-b), a._eventY = h * M(-b) + e * P(-b)) : (a._eventX = e, a._eventY = h), a._eventX /= c.scaleX, a._eventY /= c.scaleY, f && d.intersecting.push(a), a.intersects = !!f;
    }

    function za(d, c, a) {
        c._toRad = c.inDegrees ? E / 180 : 1;
        d.translate(c.x, c.y);
        d.rotate(c.rotate * c._toRad);
        d.translate(-c.x, -c.y);
        a && (a.rotate += c.rotate * c._toRad);
    }

    function Aa(d, c, a) {
        1 !== c.scale && (c.scaleX = c.scaleY = c.scale);
        d.translate(c.x, c.y);
        d.scale(c.scaleX, c.scaleY);
        d.translate(-c.x, -c.y);
        a && (a.scaleX *= c.scaleX, a.scaleY *= c.scaleY);
    }

    function Ba(d, c, a) {
        c.translate && (c.translateX = c.translateY = c.translate);
        d.translate(c.translateX, c.translateY);
        a && (a.translateX += c.translateX, a.translateY += c.translateY);
    }

    function Ja(d) {
        for (; 0 > d;) {
            d += 2 * E;
        }

        return d;
    }

    function Ka(d, c, a, b) {
        var f, e, h, g, p, v, z;
        a === b ? z = v = 0 : (v = a.x, z = a.y);
        b.inDegrees || 360 !== b.end || (b.end = 2 * E);
        b.start *= a._toRad;
        b.end *= a._toRad;
        b.start -= E / 2;
        b.end -= E / 2;
        p = E / 180;
        b.ccw && (p *= -1);
        f = b.x + b.radius * M(b.start + p);
        e = b.y + b.radius * P(b.start + p);
        h = b.x + b.radius * M(b.start);
        g = b.y + b.radius * P(b.start);
        ga(d, c, a, b, f, e, h, g);
        c.arc(b.x + v, b.y + z, b.radius, b.start, b.end, b.ccw);
        f = b.x + b.radius * M(b.end + p);
        p = b.y + b.radius * P(b.end + p);
        e = b.x + b.radius * M(b.end);
        h = b.y + b.radius * P(b.end);
        ha(d, c, a, b, e, h, f, p);
    }

    function La(d, c, a, b, f, e, h, g) {
        var p, v;
        b.arrowRadius && !a.closed && (v = $a(g - e, h - f), v -= E, d = a.strokeWidth * M(v), p = a.strokeWidth * P(v), a = h + b.arrowRadius * M(v + b.arrowAngle / 2), f = g + b.arrowRadius * P(v + b.arrowAngle / 2), e = h + b.arrowRadius * M(v - b.arrowAngle / 2), b = g + b.arrowRadius * P(v - b.arrowAngle / 2), c.moveTo(a - d, f - p), c.lineTo(h - d, g - p), c.lineTo(e - d, b - p), c.moveTo(h - d, g - p), c.lineTo(h + d, g + p), c.moveTo(h, g));
    }

    function ga(d, c, a, b, f, e, h, g) {
        b._arrowAngleConverted || (b.arrowAngle *= a._toRad, b._arrowAngleConverted = !0);
        b.startArrow && La(d, c, a, b, f, e, h, g);
    }

    function ha(d, c, a, b, f, e, h, g) {
        b._arrowAngleConverted || (b.arrowAngle *= a._toRad, b._arrowAngleConverted = !0);
        b.endArrow && La(d, c, a, b, f, e, h, g);
    }

    function Ma(d, c, a, b) {
        var f, e, h;
        f = 2;
        ga(d, c, a, b, b.x2 + a.x, b.y2 + a.y, b.x1 + a.x, b.y1 + a.y);

        for (void 0 !== b.x1 && void 0 !== b.y1 && c.moveTo(b.x1 + a.x, b.y1 + a.y); ;) {
            if (e = b["x" + f], h = b["y" + f], void 0 !== e && void 0 !== h) c.lineTo(e + a.x, h + a.y), f += 1; else break;
        }

        --f;
        ha(d, c, a, b, b["x" + (f - 1)] + a.x, b["y" + (f - 1)] + a.y, b["x" + f] + a.x, b["y" + f] + a.y);
    }

    function Na(d, c, a, b) {
        var f, e, h, g, p;
        f = 2;
        ga(d, c, a, b, b.cx1 + a.x, b.cy1 + a.y, b.x1 + a.x, b.y1 + a.y);

        for (void 0 !== b.x1 && void 0 !== b.y1 && c.moveTo(b.x1 + a.x, b.y1 + a.y); ;) {
            if (e = b["x" + f], h = b["y" + f], g = b["cx" + (f - 1)], p = b["cy" + (f - 1)], void 0 !== e && void 0 !== h && void 0 !== g && void 0 !== p) c.quadraticCurveTo(g + a.x, p + a.y, e + a.x, h + a.y), f += 1; else break;
        }

        --f;
        ha(d, c, a, b, b["cx" + (f - 1)] + a.x, b["cy" + (f - 1)] + a.y, b["x" + f] + a.x, b["y" + f] + a.y);
    }

    function Oa(d, c, a, b) {
        var f, e, h, g, p, v, z, D;
        f = 2;
        e = 1;
        ga(d, c, a, b, b.cx1 + a.x, b.cy1 + a.y, b.x1 + a.x, b.y1 + a.y);

        for (void 0 !== b.x1 && void 0 !== b.y1 && c.moveTo(b.x1 + a.x, b.y1 + a.y); ;) {
            if (h = b["x" + f], g = b["y" + f], p = b["cx" + e], v = b["cy" + e], z = b["cx" + (e + 1)], D = b["cy" + (e + 1)], void 0 !== h && void 0 !== g && void 0 !== p && void 0 !== v && void 0 !== z && void 0 !== D) c.bezierCurveTo(p + a.x, v + a.y, z + a.x, D + a.y, h + a.x, g + a.y), f += 1, e += 2; else break;
        }

        --f;
        e -= 2;
        ha(d, c, a, b, b["cx" + (e + 1)] + a.x, b["cy" + (e + 1)] + a.y, b["x" + f] + a.x, b["y" + f] + a.y);
    }

    function Pa(d, c, a) {
        c *= d._toRad;
        c -= E / 2;
        return a * M(c);
    }

    function Qa(d, c, a) {
        c *= d._toRad;
        c -= E / 2;
        return a * P(c);
    }

    function Ra(d, c, a, b) {
        var f, e, h, g, p, v, z;
        a === b ? p = g = 0 : (g = a.x, p = a.y);
        f = 1;
        e = g = v = b.x + g;
        h = p = z = b.y + p;
        ga(d, c, a, b, e + Pa(a, b.a1, b.l1), h + Qa(a, b.a1, b.l1), e, h);

        for (void 0 !== b.x && void 0 !== b.y && c.moveTo(e, h); ;) {
            if (e = b["a" + f], h = b["l" + f], void 0 !== e && void 0 !== h) g = v, p = z, v += Pa(a, e, h), z += Qa(a, e, h), c.lineTo(v, z), f += 1; else break;
        }

        ha(d, c, a, b, g, p, v, z);
    }

    function sa(d, c, a) {
        isNaN(wa(a.fontSize)) || (a.fontSize += "px");
        c.font = a.fontStyle + " " + a.fontSize + " " + a.fontFamily;
    }

    function ta(d, c, a, b) {
        var f, e;
        f = ca.propCache;
        if (f.text === a.text && f.fontStyle === a.fontStyle && f.fontSize === a.fontSize && f.fontFamily === a.fontFamily && f.maxWidth === a.maxWidth && f.lineHeight === a.lineHeight) a.width = f.width, a.height = f.height; else {
            a.width = c.measureText(b[0]).width;

            for (e = 1; e < b.length; e += 1) {
                f = c.measureText(b[e]).width, f > a.width && (a.width = f);
            }

            c = d.style.fontSize;
            d.style.fontSize = a.fontSize;
            a.height = ba(g.css(d, "fontSize")) * b.length * a.lineHeight;
            d.style.fontSize = c;
        }
    }

    function Sa(d, c) {
        var a = c.maxWidth,
            b = String(c.text).split("\n"),
            f = [],
            e,
            h,
            g,
            p,
            v;

        for (g = 0; g < b.length; g += 1) {
            p = b[g];
            v = p.split(" ");
            e = [];
            h = "";
            if (1 === v.length || d.measureText(p).width < a) e = [p]; else {
                for (p = 0; p < v.length; p += 1) {
                    d.measureText(h + v[p]).width > a && ("" !== h && e.push(h), h = ""), h += v[p], p !== v.length - 1 && (h += " ");
                }

                e.push(h);
            }
            f = f.concat(e.join("\n").replace(/( (\n))|( $)/gi, "$2").split("\n"));
        }

        return f;
    }

    var Ia = U.document,
        Ta = U.Image,
        ab = U.getComputedStyle,
        ea = U.Math,
        wa = U.Number,
        ba = U.parseFloat,
        ma,
        Z = g.extend,
        ia = g.inArray,
        aa = function aa(d) {
            return Object.prototype.toString.call(d).slice(8, -1).toLowerCase();
        },
        da = g.isFunction,
        Ga = g.isPlainObject,
        E = ea.PI,
        Xa = ea.round,
        bb = ea.abs,
        P = ea.sin,
        M = ea.cos,
        $a = ea.atan2,
        ua = U.Array.prototype.slice,
        cb = g.event.fix,
        X = {},
        ca = {
            dataCache: {},
            propCache: {},
            imageCache: {}
        },
        oa = {
            rotate: 0,
            scaleX: 1,
            scaleY: 1,
            translateX: 0,
            translateY: 0,
            masks: []
        },
        V = {},
        Ua = "mousedown mousemove mouseup mouseover mouseout touchstart touchmove touchend".split(" "),
        Y = {
            events: {},
            eventHooks: {},
            future: {}
        };

    na.baseDefaults = {
        align: "center",
        arrowAngle: 90,
        arrowRadius: 0,
        autosave: !0,
        baseline: "middle",
        bringToFront: !1,
        ccw: !1,
        closed: !1,
        compositing: "source-over",
        concavity: 0,
        cornerRadius: 0,
        count: 1,
        cropFromCenter: !0,
        crossOrigin: "Anonymous",
        cursors: null,
        disableEvents: !1,
        draggable: !1,
        dragGroups: null,
        groups: null,
        data: null,
        dx: null,
        dy: null,
        end: 360,
        eventX: null,
        eventY: null,
        fillStyle: "transparent",
        fontStyle: "normal",
        fontSize: "12pt",
        fontFamily: "sans-serif",
        fromCenter: !0,
        height: null,
        imageSmoothing: !0,
        inDegrees: !0,
        intangible: !1,
        index: null,
        letterSpacing: null,
        lineHeight: 1,
        layer: !1,
        mask: !1,
        maxWidth: null,
        miterLimit: 10,
        name: null,
        opacity: 1,
        r1: null,
        r2: null,
        radius: 0,
        repeat: "repeat",
        respectAlign: !1,
        restrictDragToAxis: null,
        rotate: 0,
        rounded: !1,
        scale: 1,
        scaleX: 1,
        scaleY: 1,
        shadowBlur: 0,
        shadowColor: "transparent",
        shadowStroke: !1,
        shadowX: 0,
        shadowY: 0,
        sHeight: null,
        sides: 0,
        source: "",
        spread: 0,
        start: 0,
        strokeCap: "butt",
        strokeDash: null,
        strokeDashOffset: 0,
        strokeJoin: "miter",
        strokeStyle: "transparent",
        strokeWidth: 1,
        sWidth: null,
        sx: null,
        sy: null,
        text: "",
        translate: 0,
        translateX: 0,
        translateY: 0,
        type: null,
        visible: !0,
        width: null,
        x: 0,
        y: 0
    };
    ma = new na();
    J.prototype = ma;

    Y.extend = function (d) {
        d.name && (d.props && Z(ma, d.props), g.fn[d.name] = function a(b) {
            var f, e, h, g;

            for (e = 0; e < this.length; e += 1) {
                if (f = this[e], h = L(f)) g = new J(b), N(f, g, b, a), R(f, h, g), d.fn.call(f, h, g);
            }

            return this;
        }, d.type && (X.drawings[d.type] = d.name));
        return g.fn[d.name];
    };

    g.fn.getEventHooks = function () {
        var d;
        d = {};
        0 !== this.length && (d = this[0], d = H(d), d = d.eventHooks);
        return d;
    };

    g.fn.setEventHooks = function (d) {
        var c, a;

        for (c = 0; c < this.length; c += 1) {
            g(this[c]), a = H(this[c]), Z(a.eventHooks, d);
        }

        return this;
    };

    g.fn.getLayers = function (d) {
        var c,
            a,
            b,
            f,
            e = [];
        if (0 !== this.length) if (c = this[0], a = H(c), a = a.layers, da(d)) for (f = 0; f < a.length; f += 1) {
            b = a[f], d.call(c, b) && e.push(b);
        } else e = a;
        return e;
    };

    g.fn.getLayer = function (d) {
        var c, a, b, f;
        if (0 !== this.length) if (c = this[0], a = H(c), c = a.layers, f = aa(d), d && d.layer) b = d; else if ("number" === f) 0 > d && (d = c.length + d), b = c[d]; else if ("regexp" === f) for (a = 0; a < c.length; a += 1) {
            if (ja(c[a].name) && c[a].name.match(d)) {
                b = c[a];
                break;
            }
        } else b = a.layer.names[d];
        return b;
    };

    g.fn.getLayerGroup = function (d) {
        var c,
            a,
            b,
            f = aa(d);
        if (0 !== this.length) if (c = this[0], "array" === f) b = d; else if ("regexp" === f) for (a in c = H(c), c = c.layer.groups, c) {
            if (a.match(d)) {
                b = c[a];
                break;
            }
        } else c = H(c), b = c.layer.groups[d];
        return b;
    };

    g.fn.getLayerIndex = function (d) {
        var c = this.getLayers();
        d = this.getLayer(d);
        return ia(d, c);
    };

    g.fn.setLayer = function (d, c) {
        var a, b, f, e, h, K, p;

        for (b = 0; b < this.length; b += 1) {
            if (a = g(this[b]), f = H(this[b]), e = g(this[b]).getLayer(d)) {
                pa(a, f, e, c);
                qa(a, f, e, c);
                ka(c);

                for (h in c) {
                    c.hasOwnProperty(h) && (K = c[h], p = aa(K), "object" === p && Ga(K) ? (e[h] = Z({}, K), ka(e[h])) : "array" === p ? e[h] = K.slice(0) : "string" === p ? 0 === K.indexOf("+=") ? e[h] += ba(K.substr(2)) : 0 === K.indexOf("-=") ? e[h] -= ba(K.substr(2)) : !isNaN(K) && va(K) && "text" !== h ? e[h] = ba(K) : e[h] = K : e[h] = K);
                }

                Ca(a, f, e);
                Ea(a, f, e);
                !1 === g.isEmptyObject(c) && O(a, f, e, "change", c);
            }
        }

        return this;
    };

    g.fn.setLayers = function (d, c) {
        var a, b, f, e;

        for (b = 0; b < this.length; b += 1) {
            for (a = g(this[b]), f = a.getLayers(c), e = 0; e < f.length; e += 1) {
                a.setLayer(f[e], d);
            }
        }

        return this;
    };

    g.fn.setLayerGroup = function (d, c) {
        var a, b, f, e;

        for (b = 0; b < this.length; b += 1) {
            if (a = g(this[b]), f = a.getLayerGroup(d)) for (e = 0; e < f.length; e += 1) {
                a.setLayer(f[e], c);
            }
        }

        return this;
    };

    g.fn.moveLayer = function (d, c) {
        var a, b, f, e, h;

        for (b = 0; b < this.length; b += 1) {
            if (a = g(this[b]), f = H(this[b]), e = f.layers, h = a.getLayer(d)) h.index = ia(h, e), e.splice(h.index, 1), e.splice(c, 0, h), 0 > c && (c = e.length + c), h.index = c, O(a, f, h, "move");
        }

        return this;
    };

    g.fn.removeLayer = function (d) {
        var c, a, b, f, e;

        for (a = 0; a < this.length; a += 1) {
            if (c = g(this[a]), b = H(this[a]), f = c.getLayers(), e = c.getLayer(d)) e.index = ia(e, f), f.splice(e.index, 1), delete e._layer, pa(c, b, e, {
                name: null
            }), qa(c, b, e, {
                groups: null
            }), O(c, b, e, "remove");
        }

        return this;
    };

    g.fn.removeLayers = function (d) {
        var c, a, b, f, e, h;

        for (a = 0; a < this.length; a += 1) {
            c = g(this[a]);
            b = H(this[a]);
            f = c.getLayers(d);

            for (h = 0; h < f.length; h += 1) {
                e = f[h], c.removeLayer(e), --h;
            }

            b.layer.names = {};
            b.layer.groups = {};
        }

        return this;
    };

    g.fn.removeLayerGroup = function (d) {
        var c, a, b, f;
        if (void 0 !== d) for (a = 0; a < this.length; a += 1) {
            if (c = g(this[a]), H(this[a]), c.getLayers(), b = c.getLayerGroup(d)) for (b = b.slice(0), f = 0; f < b.length; f += 1) {
                c.removeLayer(b[f]);
            }
        }
        return this;
    };

    g.fn.addLayerToGroup = function (d, c) {
        var a,
            b,
            f,
            e = [c];

        for (b = 0; b < this.length; b += 1) {
            a = g(this[b]), f = a.getLayer(d), f.groups && (e = f.groups.slice(0), -1 === ia(c, f.groups) && e.push(c)), a.setLayer(f, {
                groups: e
            });
        }

        return this;
    };

    g.fn.removeLayerFromGroup = function (d, c) {
        var a, b, f, e, h;

        for (b = 0; b < this.length; b += 1) {
            a = g(this[b]), f = a.getLayer(d), f.groups && (h = ia(c, f.groups), -1 !== h && (e = f.groups.slice(0), e.splice(h, 1), a.setLayer(f, {
                groups: e
            })));
        }

        return this;
    };

    V.cursors = ["grab", "grabbing", "zoom-in", "zoom-out"];

    V.prefix = function () {
        var d = ab(Ia.documentElement, "");
        return "-" + (ua.call(d).join("").match(/-(moz|webkit|ms)-/) || "" === d.OLink && ["", "o"])[1] + "-";
    }();

    g.fn.triggerLayerEvent = function (d, c) {
        var a, b, f;

        for (b = 0; b < this.length; b += 1) {
            a = g(this[b]), f = H(this[b]), (d = a.getLayer(d)) && O(a, f, d, c);
        }

        return this;
    };

    g.fn.drawLayer = function (d) {
        var c, a, b;

        for (c = 0; c < this.length; c += 1) {
            b = g(this[c]), (a = L(this[c])) && (a = b.getLayer(d)) && a.visible && a._method && (a._next = null, a._method.call(b, a));
        }

        return this;
    };

    g.fn.drawLayers = function (d) {
        var c,
            a,
            b = d || {},
            f,
            e,
            h,
            K,
            p,
            v,
            z,
            D;
        (K = b.index) || (K = 0);

        for (c = 0; c < this.length; c += 1) {
            if (d = g(this[c]), a = L(this[c])) {
                p = H(this[c]);
                !1 !== b.clear && d.clearCanvas();
                a = p.layers;

                for (h = K; h < a.length; h += 1) {
                    if (f = a[h], f.index = h, b.resetFire && (f._fired = !1), v = d, z = f, e = h + 1, z && z.visible && z._method && (z._next = e ? e : null, z._method.call(v, z)), f._masks = p.transforms.masks.slice(0), f._method === g.fn.drawImage && f.visible) {
                        D = !0;
                        break;
                    }
                }

                if (D) break;
                f = p;
                var m;
                v = null;

                for (z = f.intersecting.length - 1; 0 <= z; --z) {
                    if (v = f.intersecting[z], v._masks) {
                        for (m = v._masks.length - 1; 0 <= m; --m) {
                            if (e = v._masks[m], !e.intersects) {
                                v.intersects = !1;
                                break;
                            }
                        }

                        if (v.intersects && !v.intangible) break;
                    }
                }

                v && v.intangible && (v = null);
                f = v;
                v = p.event;
                z = v.type;

                if (p.drag.layer) {
                    var B = d;
                    e = p;
                    var r = z,
                        t,
                        l,
                        x;
                    l = e.drag;
                    x = (m = l.layer) && m.dragGroups || [];
                    t = e.layers;

                    if ("mousemove" === r || "touchmove" === r) {
                        if (l.dragging || (l.dragging = !0, m.dragging = !0, m.bringToFront && (t.splice(m.index, 1), m.index = t.push(m)), m._startX = m.x, m._startY = m.y, m._endX = m._eventX, m._endY = m._eventY, O(B, e, m, "dragstart")), l.dragging) for (r = m._eventX - (m._endX - m._startX), t = m._eventY - (m._endY - m._startY), m.dx = r - m.x, m.dy = t - m.y, "y" !== m.restrictDragToAxis && (m.x = r), "x" !== m.restrictDragToAxis && (m.y = t), O(B, e, m, "drag"), B = 0; B < x.length; B += 1) {
                            if (r = x[B], t = e.layer.groups[r], m.groups && t) for (r = 0; r < t.length; r += 1) {
                                t[r] !== m && ("y" !== m.restrictDragToAxis && "y" !== t[r].restrictDragToAxis && (t[r].x += m.dx), "x" !== m.restrictDragToAxis && "x" !== t[r].restrictDragToAxis && (t[r].y += m.dy));
                            }
                        }
                    } else if ("mouseup" === r || "touchend" === r) l.dragging && (m.dragging = !1, l.dragging = !1, O(B, e, m, "dragstop")), e.drag = {};
                }

                e = p.lastIntersected;
                null === e || f === e || !e._hovered || e._fired || p.drag.dragging || (p.lastIntersected = null, e._fired = !0, e._hovered = !1, O(d, p, e, "mouseout"), d.css({
                    cursor: p.cursor
                }));
                f && (f[z] || X.mouseEvents[z] && (z = X.mouseEvents[z]), f._event && f.intersects && (p.lastIntersected = f, !(f.mouseover || f.mouseout || f.cursors) || p.drag.dragging || f._hovered || f._fired || (f._fired = !0, f._hovered = !0, O(d, p, f, "mouseover")), f._fired || (f._fired = !0, v.type = null, O(d, p, f, z)), !f.draggable || f.disableEvents || "mousedown" !== z && "touchstart" !== z || (p.drag.layer = f)));
                null !== f || p.drag.dragging || d.css({
                    cursor: p.cursor
                });
                h === a.length && (p.intersecting.length = 0, p.transforms = la(oa), p.savedTransforms.length = 0);
            }
        }

        return this;
    };

    g.fn.addLayer = function (d) {
        var c, a;

        for (c = 0; c < this.length; c += 1) {
            if (a = L(this[c])) a = new J(d), a.layer = !0, N(this[c], a, d);
        }

        return this;
    };

    V.props = ["width", "height", "opacity", "lineHeight"];
    V.propsObj = {};

    g.fn.animateLayer = function () {
        function d(a, b, c) {
            return function () {
                var d, f;

                for (f = 0; f < V.props.length; f += 1) {
                    d = V.props[f], c[d] = c["_" + d];
                }

                for (var h in c) {
                    c.hasOwnProperty(h) && -1 !== h.indexOf(".") && delete c[h];
                }

                b.animating && b.animated !== c || a.drawLayers();
                c._animating = !1;
                b.animating = !1;
                b.animated = null;
                e[4] && e[4].call(a[0], c);
                O(a, b, c, "animateend");
            };
        }

        function c(a, b, c) {
            return function (d, f) {
                var h,
                    g,
                    t = !1;
                "_" === f.prop[0] && (t = !0, f.prop = f.prop.replace("_", ""), c[f.prop] = c["_" + f.prop]);
                -1 !== f.prop.indexOf(".") && (h = f.prop.split("."), g = h[0], h = h[1], c[g] && (c[g][h] = f.now));
                c._pos !== f.pos && (c._pos = f.pos, c._animating || b.animating || (c._animating = !0, b.animating = !0, b.animated = c), b.animating && b.animated !== c || a.drawLayers());
                e[5] && e[5].call(a[0], d, f, c);
                O(a, b, c, "animate", f);
                t && (f.prop = "_" + f.prop);
            };
        }

        var a,
            b,
            f,
            e = ua.call(arguments, 0),
            h,
            K;
        "object" === aa(e[2]) ? (e.splice(2, 0, e[2].duration || null), e.splice(3, 0, e[3].easing || null), e.splice(4, 0, e[4].complete || null), e.splice(5, 0, e[5].step || null)) : (void 0 === e[2] ? (e.splice(2, 0, null), e.splice(3, 0, null), e.splice(4, 0, null)) : da(e[2]) && (e.splice(2, 0, null), e.splice(3, 0, null)), void 0 === e[3] ? (e[3] = null, e.splice(4, 0, null)) : da(e[3]) && e.splice(3, 0, null));

        for (b = 0; b < this.length; b += 1) {
            if (a = g(this[b]), f = L(this[b])) f = H(this[b]), (h = a.getLayer(e[0])) && h._method !== g.fn.draw && (K = Z({}, e[1]), K = Va(this[b], h, K), Fa(K, !0), Fa(h), h.style = V.propsObj, g(h).animate(K, {
                duration: e[2],
                easing: g.easing[e[3]] ? e[3] : null,
                complete: d(a, f, h),
                step: c(a, f, h)
            }), O(a, f, h, "animatestart"));
        }

        return this;
    };

    g.fn.animateLayerGroup = function (d) {
        var c,
            a,
            b = ua.call(arguments, 0),
            f,
            e;

        for (a = 0; a < this.length; a += 1) {
            if (c = g(this[a]), f = c.getLayerGroup(d)) for (e = 0; e < f.length; e += 1) {
                b[0] = f[e], c.animateLayer.apply(c, b);
            }
        }

        return this;
    };

    g.fn.delayLayer = function (d, c) {
        var a, b, f, e;
        c = c || 0;

        for (b = 0; b < this.length; b += 1) {
            if (a = g(this[b]), f = H(this[b]), e = a.getLayer(d)) g(e).delay(c), O(a, f, e, "delay");
        }

        return this;
    };

    g.fn.delayLayerGroup = function (d, c) {
        var a, b, f, e, h;
        c = c || 0;

        for (b = 0; b < this.length; b += 1) {
            if (a = g(this[b]), f = a.getLayerGroup(d)) for (h = 0; h < f.length; h += 1) {
                e = f[h], a.delayLayer(e, c);
            }
        }

        return this;
    };

    g.fn.stopLayer = function (d, c) {
        var a, b, f, e;

        for (b = 0; b < this.length; b += 1) {
            if (a = g(this[b]), f = H(this[b]), e = a.getLayer(d)) g(e).stop(c), O(a, f, e, "stop");
        }

        return this;
    };

    g.fn.stopLayerGroup = function (d, c) {
        var a, b, f, e, h;

        for (b = 0; b < this.length; b += 1) {
            if (a = g(this[b]), f = a.getLayerGroup(d)) for (h = 0; h < f.length; h += 1) {
                e = f[h], a.stopLayer(e, c);
            }
        }

        return this;
    };

    (function (d) {
        var c;

        for (c = 0; c < d.length; c += 1) {
            g.fx.step[d[c]] = Wa;
        }
    })("color backgroundColor borderColor borderTopColor borderRightColor borderBottomColor borderLeftColor fillStyle outlineColor strokeStyle shadowColor".split(" "));

    X.touchEvents = {
        mousedown: "touchstart",
        mouseup: "touchend",
        mousemove: "touchmove"
    };
    X.mouseEvents = {
        touchstart: "mousedown",
        touchend: "mouseup",
        touchmove: "mousemove"
    };

    (function (d) {
        var c;

        for (c = 0; c < d.length; c += 1) {
            Za(d[c]);
        }
    })("click dblclick mousedown mouseup mousemove mouseover mouseout touchstart touchmove touchend contextmenu".split(" "));

    g.event.fix = function (d) {
        var c, a;
        d = cb.call(g.event, d);
        if (c = d.originalEvent) if (a = c.changedTouches, void 0 !== d.pageX && void 0 === d.offsetX) {
            if (c = g(d.currentTarget).offset()) d.offsetX = d.pageX - c.left, d.offsetY = d.pageY - c.top;
        } else a && (c = g(d.currentTarget).offset()) && (d.offsetX = a[0].pageX - c.left, d.offsetY = a[0].pageY - c.top);
        return d;
    };

    X.drawings = {
        arc: "drawArc",
        bezier: "drawBezier",
        ellipse: "drawEllipse",
        "function": "draw",
        image: "drawImage",
        line: "drawLine",
        path: "drawPath",
        polygon: "drawPolygon",
        slice: "drawSlice",
        quadratic: "drawQuadratic",
        rectangle: "drawRect",
        text: "drawText",
        vector: "drawVector",
        save: "saveCanvas",
        restore: "restoreCanvas",
        rotate: "rotateCanvas",
        scale: "scaleCanvas",
        translate: "translateCanvas"
    };

    g.fn.draw = function c(a) {
        var b,
            f,
            e = new J(a);
        if (X.drawings[e.type] && "function" !== e.type) this[X.drawings[e.type]](a); else for (b = 0; b < this.length; b += 1) {
            if (g(this[b]), f = L(this[b])) e = new J(a), N(this[b], e, a, c), e.visible && e.fn && e.fn.call(this[b], f, e);
        }
        return this;
    };

    g.fn.clearCanvas = function a(b) {
        var f,
            e,
            h = new J(b);

        for (f = 0; f < this.length; f += 1) {
            if (e = L(this[f])) null === h.width || null === h.height ? (e.save(), e.setTransform(1, 0, 0, 1, 0, 0), e.clearRect(0, 0, this[f].width, this[f].height), e.restore()) : (N(this[f], h, b, a), Q(this[f], e, h, h.width, h.height), e.clearRect(h.x - h.width / 2, h.y - h.height / 2, h.width, h.height), h._transformed && e.restore());
        }

        return this;
    };

    g.fn.saveCanvas = function b(f) {
        var e, h, g, p, v;

        for (e = 0; e < this.length; e += 1) {
            if (h = L(this[e])) for (p = H(this[e]), g = new J(f), N(this[e], g, f, b), v = 0; v < g.count; v += 1) {
                fa(h, p);
            }
        }

        return this;
    };

    g.fn.restoreCanvas = function f(e) {
        var h, g, p, v, z;

        for (h = 0; h < this.length; h += 1) {
            if (g = L(this[h])) for (v = H(this[h]), p = new J(e), N(this[h], p, e, f), z = 0; z < p.count; z += 1) {
                var D = g,
                    m = v;
                0 === m.savedTransforms.length ? m.transforms = la(oa) : (D.restore(), m.transforms = m.savedTransforms.pop());
            }
        }

        return this;
    };

    g.fn.rotateCanvas = function e(g) {
        var K, p, v, z;

        for (K = 0; K < this.length; K += 1) {
            if (p = L(this[K])) z = H(this[K]), v = new J(g), N(this[K], v, g, e), v.autosave && fa(p, z), za(p, v, z.transforms);
        }

        return this;
    };

    g.fn.scaleCanvas = function h(g) {
        var p, v, z, D;

        for (p = 0; p < this.length; p += 1) {
            if (v = L(this[p])) D = H(this[p]), z = new J(g), N(this[p], z, g, h), z.autosave && fa(v, D), Aa(v, z, D.transforms);
        }

        return this;
    };

    g.fn.translateCanvas = function K(g) {
        var v, z, D, m;

        for (v = 0; v < this.length; v += 1) {
            if (z = L(this[v])) m = H(this[v]), D = new J(g), N(this[v], D, g, K), D.autosave && fa(z, m), Ba(z, D, m.transforms);
        }

        return this;
    };

    g.fn.drawRect = function p(g) {
        var z, D, m, B, r, t, l, x, F;

        for (z = 0; z < this.length; z += 1) {
            if (D = L(this[z])) m = new J(g), N(this[z], m, g, p), m.visible && (Q(this[z], D, m, m.width, m.height), R(this[z], D, m), D.beginPath(), m.width && m.height && (B = m.x - m.width / 2, r = m.y - m.height / 2, (x = bb(m.cornerRadius)) ? (t = m.x + m.width / 2, l = m.y + m.height / 2, 0 > m.width && (F = B, B = t, t = F), 0 > m.height && (F = r, r = l, l = F), 0 > t - B - 2 * x && (x = (t - B) / 2), 0 > l - r - 2 * x && (x = (l - r) / 2), D.moveTo(B + x, r), D.lineTo(t - x, r), D.arc(t - x, r + x, x, 3 * E / 2, 2 * E, !1), D.lineTo(t, l - x), D.arc(t - x, l - x, x, 0, E / 2, !1), D.lineTo(B + x, l), D.arc(B + x, l - x, x, E / 2, E, !1), D.lineTo(B, r + x), D.arc(B + x, r + x, x, E, 3 * E / 2, !1), m.closed = !0) : D.rect(B, r, m.width, m.height)), T(this[z], D, m), W(this[z], D, m));
        }

        return this;
    };

    g.fn.drawArc = function v(g) {
        var D, m, B;

        for (D = 0; D < this.length; D += 1) {
            if (m = L(this[D])) B = new J(g), N(this[D], B, g, v), B.visible && (Q(this[D], m, B, 2 * B.radius), R(this[D], m, B), m.beginPath(), Ka(this[D], m, B, B), T(this[D], m, B), W(this[D], m, B));
        }

        return this;
    };

    g.fn.drawEllipse = function z(g) {
        var m, B, r, t, l;

        for (m = 0; m < this.length; m += 1) {
            if (B = L(this[m])) r = new J(g), N(this[m], r, g, z), r.visible && (Q(this[m], B, r, r.width, r.height), R(this[m], B, r), t = 4 / 3 * r.width, l = r.height, B.beginPath(), B.moveTo(r.x, r.y - l / 2), B.bezierCurveTo(r.x - t / 2, r.y - l / 2, r.x - t / 2, r.y + l / 2, r.x, r.y + l / 2), B.bezierCurveTo(r.x + t / 2, r.y + l / 2, r.x + t / 2, r.y - l / 2, r.x, r.y - l / 2), T(this[m], B, r), r.closed = !0, W(this[m], B, r));
        }

        return this;
    };

    g.fn.drawPolygon = function D(g) {
        var B, r, t, l, x, F, y, A, n, k;

        for (B = 0; B < this.length; B += 1) {
            if (r = L(this[B])) if (t = new J(g), N(this[B], t, g, D), t.visible) {
                Q(this[B], r, t, 2 * t.radius);
                R(this[B], r, t);
                x = 2 * E / t.sides;
                F = x / 2;
                l = F + E / 2;
                y = t.radius * M(F);
                r.beginPath();

                for (k = 0; k < t.sides; k += 1) {
                    A = t.x + t.radius * M(l), n = t.y + t.radius * P(l), r.lineTo(A, n), t.concavity && (A = t.x + (y + -y * t.concavity) * M(l + F), n = t.y + (y + -y * t.concavity) * P(l + F), r.lineTo(A, n)), l += x;
                }

                T(this[B], r, t);
                t.closed = !0;
                W(this[B], r, t);
            }
        }

        return this;
    };

    g.fn.drawSlice = function m(B) {
        var r, t, l, x, F;

        for (r = 0; r < this.length; r += 1) {
            if (g(this[r]), t = L(this[r])) l = new J(B), N(this[r], l, B, m), l.visible && (Q(this[r], t, l, 2 * l.radius), R(this[r], t, l), l.start *= l._toRad, l.end *= l._toRad, l.start -= E / 2, l.end -= E / 2, l.start = Ja(l.start), l.end = Ja(l.end), l.end < l.start && (l.end += 2 * E), x = (l.start + l.end) / 2, F = l.radius * l.spread * M(x), x = l.radius * l.spread * P(x), l.x += F, l.y += x, t.beginPath(), t.arc(l.x, l.y, l.radius, l.start, l.end, l.ccw), t.lineTo(l.x, l.y), T(this[r], t, l), l.closed = !0, W(this[r], t, l));
        }

        return this;
    };

    g.fn.drawLine = function B(g) {
        var t, l, x;

        for (t = 0; t < this.length; t += 1) {
            if (l = L(this[t])) x = new J(g), N(this[t], x, g, B), x.visible && (Q(this[t], l, x), R(this[t], l, x), l.beginPath(), Ma(this[t], l, x, x), T(this[t], l, x), W(this[t], l, x));
        }

        return this;
    };

    g.fn.drawQuadratic = function r(g) {
        var l, x, F;

        for (l = 0; l < this.length; l += 1) {
            if (x = L(this[l])) F = new J(g), N(this[l], F, g, r), F.visible && (Q(this[l], x, F), R(this[l], x, F), x.beginPath(), Na(this[l], x, F, F), T(this[l], x, F), W(this[l], x, F));
        }

        return this;
    };

    g.fn.drawBezier = function t(g) {
        var x, F, y;

        for (x = 0; x < this.length; x += 1) {
            if (F = L(this[x])) y = new J(g), N(this[x], y, g, t), y.visible && (Q(this[x], F, y), R(this[x], F, y), F.beginPath(), Oa(this[x], F, y, y), T(this[x], F, y), W(this[x], F, y));
        }

        return this;
    };

    g.fn.drawVector = function l(g) {
        var F, y, A;

        for (F = 0; F < this.length; F += 1) {
            if (y = L(this[F])) A = new J(g), N(this[F], A, g, l), A.visible && (Q(this[F], y, A), R(this[F], y, A), y.beginPath(), Ra(this[F], y, A, A), T(this[F], y, A), W(this[F], y, A));
        }

        return this;
    };

    g.fn.drawPath = function x(g) {
        var y, A, n, k, w;

        for (y = 0; y < this.length; y += 1) {
            if (A = L(this[y])) if (n = new J(g), N(this[y], n, g, x), n.visible) {
                Q(this[y], A, n);
                R(this[y], A, n);
                A.beginPath();

                for (k = 1; ;) {
                    if (w = n["p" + k], void 0 !== w) w = new J(w), "line" === w.type ? Ma(this[y], A, n, w) : "quadratic" === w.type ? Na(this[y], A, n, w) : "bezier" === w.type ? Oa(this[y], A, n, w) : "vector" === w.type ? Ra(this[y], A, n, w) : "arc" === w.type && Ka(this[y], A, n, w), k += 1; else break;
                }

                T(this[y], A, n);
                W(this[y], A, n);
            }
        }

        return this;
    };

    g.fn.drawText = function F(y) {
        var A, n, k, w, C, u, G, S, I, H;

        for (A = 0; A < this.length; A += 1) {
            if (g(this[A]), n = L(this[A])) if (k = new J(y), w = N(this[A], k, y, F), k.visible) {
                n.textBaseline = k.baseline;
                n.textAlign = k.align;
                sa(this[A], n, k);
                C = null !== k.maxWidth ? Sa(n, k) : k.text.toString().split("\n");
                ta(this[A], n, k, C);
                w && (w.width = k.width, w.height = k.height);
                Q(this[A], n, k, k.width, k.height);
                R(this[A], n, k);
                G = k.x;
                "left" === k.align ? k.respectAlign ? k.x += k.width / 2 : G -= k.width / 2 : "right" === k.align && (k.respectAlign ? k.x -= k.width / 2 : G += k.width / 2);
                if (k.radius) for (G = ba(k.fontSize), null === k.letterSpacing && (k.letterSpacing = G / 500), w = 0; w < C.length; w += 1) {
                    n.save();
                    n.translate(k.x, k.y);
                    u = C[w];
                    k.flipArcText && (u = u.split(""), u.reverse(), u = u.join(""));
                    S = u.length;
                    n.rotate(-(E * k.letterSpacing * (S - 1)) / 2);

                    for (H = 0; H < S; H += 1) {
                        I = u[H], 0 !== H && n.rotate(E * k.letterSpacing), n.save(), n.translate(0, -k.radius), k.flipArcText && n.scale(-1, -1), n.fillText(I, 0, 0), "transparent" !== k.fillStyle && (n.shadowColor = "transparent"), 0 !== k.strokeWidth && n.strokeText(I, 0, 0), n.restore();
                    }

                    k.radius -= G;
                    k.letterSpacing += G / (1E3 * E);
                    n.restore();
                } else for (w = 0; w < C.length; w += 1) {
                    u = C[w], S = k.y + w * k.height / C.length - (C.length - 1) * k.height / C.length / 2, n.shadowColor = k.shadowColor, n.fillText(u, G, S), "transparent" !== k.fillStyle && (n.shadowColor = "transparent"), 0 !== k.strokeWidth && n.strokeText(u, G, S);
                }
                S = 0;
                "top" === k.baseline ? S += k.height / 2 : "bottom" === k.baseline && (S -= k.height / 2);
                k._event && (n.beginPath(), n.rect(k.x - k.width / 2, k.y - k.height / 2 + S, k.width, k.height), T(this[A], n, k), n.closePath());
                k._transformed && n.restore();
            }
        }

        ca.propCache = k;
        return this;
    };

    g.fn.measureText = function (g) {
        var y, A;
        y = this.getLayer(g);
        if (!y || y && !y._layer) y = new J(g);
        if (g = L(this[0])) sa(this[0], g, y), A = Sa(g, y), ta(this[0], g, y, A);
        return y;
    };

    g.fn.drawImage = function y(A) {
        function n(k, n, y, q, u) {
            return function () {
                var w = g(k);
                null === q.width && null === q.sWidth && (q.width = q.sWidth = I.width);
                null === q.height && null === q.sHeight && (q.height = q.sHeight = I.height);
                u && (u.width = q.width, u.height = q.height);
                null !== q.sWidth && null !== q.sHeight && null !== q.sx && null !== q.sy ? (null === q.width && (q.width = q.sWidth), null === q.height && (q.height = q.sHeight), q.cropFromCenter && (q.sx += q.sWidth / 2, q.sy += q.sHeight / 2), 0 > q.sy - q.sHeight / 2 && (q.sy = q.sHeight / 2), q.sy + q.sHeight / 2 > I.height && (q.sy = I.height - q.sHeight / 2), 0 > q.sx - q.sWidth / 2 && (q.sx = q.sWidth / 2), q.sx + q.sWidth / 2 > I.width && (q.sx = I.width - q.sWidth / 2), Q(k, n, q, q.width, q.height), R(k, n, q), n.drawImage(I, q.sx - q.sWidth / 2, q.sy - q.sHeight / 2, q.sWidth, q.sHeight, q.x - q.width / 2, q.y - q.height / 2, q.width, q.height)) : (Q(k, n, q, q.width, q.height), R(k, n, q), n.drawImage(I, q.x - q.width / 2, q.y - q.height / 2, q.width, q.height));
                n.beginPath();
                n.rect(q.x - q.width / 2, q.y - q.height / 2, q.width, q.height);
                T(k, n, q);
                n.closePath();
                q._transformed && n.restore();
                ya(n, y, q);
                q.layer ? O(w, y, u, "load") : q.load && q.load.call(w[0], u);
                q.layer && (u._masks = y.transforms.masks.slice(0), q._next && w.drawLayers({
                    clear: !1,
                    resetFire: !0,
                    index: q._next
                }));
            };
        }

        var k,
            w,
            C,
            u,
            G,
            S,
            I,
            E,
            M,
            P = ca.imageCache;

        for (w = 0; w < this.length; w += 1) {
            if (k = this[w], C = L(this[w])) u = H(this[w]), G = new J(A), S = N(this[w], G, A, y), G.visible && (M = G.source, E = M.getContext, M.src || E ? I = M : M && (P[M] && P[M].complete ? I = P[M] : (I = new Ta(), M.match(/^data:/i) || (I.crossOrigin = G.crossOrigin), I.src = M, P[M] = I)), I && (I.complete || E ? n(k, C, u, G, S)() : (I.onload = n(k, C, u, G, S), I.src = I.src)));
        }

        return this;
    };

    g.fn.createPattern = function (y) {
        function A() {
            u = k.createPattern(C, w.repeat);
            w.load && w.load.call(n[0], u);
        }

        var n = this,
            k,
            w,
            C,
            u,
            G;
        (k = L(n[0])) ? (w = new J(y), G = w.source, da(G) ? (C = g("<canvas />")[0], C.width = w.width, C.height = w.height, y = L(C), G.call(C, y), A()) : (y = G.getContext, G.src || y ? C = G : (C = new Ta(), G.match(/^data:/i) || (C.crossOrigin = w.crossOrigin), C.src = G), C.complete || y ? A() : (C.onload = A, C.src = C.src))) : u = null;
        return u;
    };

    g.fn.createGradient = function (g) {
        var A,
            n = [],
            k,
            w,
            C,
            u,
            G,
            H,
            I;
        g = new J(g);

        if (A = L(this[0])) {
            g.x1 = g.x1 || 0;
            g.y1 = g.y1 || 0;
            g.x2 = g.x2 || 0;
            g.y2 = g.y2 || 0;
            A = null !== g.r1 && null !== g.r2 ? A.createRadialGradient(g.x1, g.y1, g.r1, g.x2, g.y2, g.r2) : A.createLinearGradient(g.x1, g.y1, g.x2, g.y2);

            for (u = 1; void 0 !== g["c" + u]; u += 1) {
                void 0 !== g["s" + u] ? n.push(g["s" + u]) : n.push(null);
            }

            k = n.length;
            null === n[0] && (n[0] = 0);
            null === n[k - 1] && (n[k - 1] = 1);

            for (u = 0; u < k; u += 1) {
                if (null !== n[u]) {
                    H = 1;
                    I = 0;
                    w = n[u];

                    for (G = u + 1; G < k; G += 1) {
                        if (null !== n[G]) {
                            C = n[G];
                            break;
                        } else H += 1;
                    }

                    w > C && (n[G] = n[u]);
                } else null === n[u] && (I += 1, n[u] = w + (C - w) / H * I);

                A.addColorStop(n[u], g["c" + (u + 1)]);
            }
        } else A = null;

        return A;
    };

    g.fn.setPixels = function A(g) {
        var k, w, C, u, G, H, I, E, M;

        for (w = 0; w < this.length; w += 1) {
            if (k = this[w], C = L(k)) {
                u = new J(g);
                N(k, u, g, A);
                Q(this[w], C, u, u.width, u.height);
                if (null === u.width || null === u.height) u.width = k.width, u.height = k.height, u.x = u.width / 2, u.y = u.height / 2;

                if (0 !== u.width && 0 !== u.height) {
                    H = C.getImageData(u.x - u.width / 2, u.y - u.height / 2, u.width, u.height);
                    I = H.data;
                    M = I.length;
                    if (u.each) for (E = 0; E < M; E += 4) {
                        G = {
                            r: I[E],
                            g: I[E + 1],
                            b: I[E + 2],
                            a: I[E + 3]
                        }, u.each.call(k, G, u), I[E] = G.r, I[E + 1] = G.g, I[E + 2] = G.b, I[E + 3] = G.a;
                    }
                    C.putImageData(H, u.x - u.width / 2, u.y - u.height / 2);
                    C.restore();
                }
            }
        }

        return this;
    };

    g.fn.getCanvasImage = function (g, n) {
        var k,
            w = null;
        0 !== this.length && (k = this[0], k.toDataURL && (void 0 === n && (n = 1), w = k.toDataURL("image/" + g, n)));
        return w;
    };

    g.fn.detectPixelRatio = function (A) {
        var n, k, w, C, u, G, E;

        for (k = 0; k < this.length; k += 1) {
            n = this[k], g(this[k]), w = L(n), E = H(this[k]), E.scaled || (C = U.devicePixelRatio || 1, u = w.webkitBackingStorePixelRatio || w.mozBackingStorePixelRatio || w.msBackingStorePixelRatio || w.oBackingStorePixelRatio || w.backingStorePixelRatio || 1, C /= u, 1 !== C && (u = n.width, G = n.height, n.width = u * C, n.height = G * C, n.style.width = u + "px", n.style.height = G + "px", w.scale(C, C)), E.pixelRatio = C, E.scaled = !0, A && A.call(n, C));
        }

        return this;
    };

    Y.clearCache = function () {
        for (var g in ca) {
            ca.hasOwnProperty(g) && (ca[g] = {});
        }
    };

    g.support.canvas = void 0 !== g("<canvas />")[0].getContext;
    Z(Y, {
        defaults: ma,
        setGlobalProps: R,
        transformShape: Q,
        detectEvents: T,
        closePath: W,
        setCanvasFont: sa,
        measureText: ta
    });
    g.jCanvas = Y;
    g.jCanvasObject = J;
});
/*! Magnific Popup - v1.1.0 - 2016-02-20
* http://dimsemenov.com/plugins/magnific-popup/
* Copyright (c) 2016 Dmitry Semenov; */

!function (a) {
    "function" == typeof define && define.amd ? define(["jquery"], a) : a("object" == (typeof exports === "undefined" ? "undefined" : _typeof(exports)) ? require("jquery") : window.jQuery || window.Zepto);
}(function (a) {
    var b,
        c,
        d,
        e,
        f,
        g,
        h = "Close",
        i = "BeforeClose",
        j = "AfterClose",
        k = "BeforeAppend",
        l = "MarkupParse",
        m = "Open",
        n = "Change",
        o = "mfp",
        p = "." + o,
        q = "mfp-ready",
        r = "mfp-removing",
        s = "mfp-prevent-close",
        t = function t() { },
        u = !!window.jQuery,
        v = a(window),
        w = function w(a, c) {
            b.ev.on(o + a + p, c);
        },
        x = function x(b, c, d, e) {
            var f = document.createElement("div");
            return f.className = "mfp-" + b, d && (f.innerHTML = d), e ? c && c.appendChild(f) : (f = a(f), c && f.appendTo(c)), f;
        },
        y = function y(c, d) {
            b.ev.triggerHandler(o + c, d), b.st.callbacks && (c = c.charAt(0).toLowerCase() + c.slice(1), b.st.callbacks[c] && b.st.callbacks[c].apply(b, a.isArray(d) ? d : [d]));
        },
        z = function z(c) {
            return c === g && b.currTemplate.closeBtn || (b.currTemplate.closeBtn = a(b.st.closeMarkup.replace("%title%", b.st.tClose)), g = c), b.currTemplate.closeBtn;
        },
        A = function A() {
            a.magnificPopup.instance || (b = new t(), b.init(), a.magnificPopup.instance = b);
        },
        B = function B() {
            var a = document.createElement("p").style,
                b = ["ms", "O", "Moz", "Webkit"];
            if (void 0 !== a.transition) return !0;

            for (; b.length;) {
                if (b.pop() + "Transition" in a) return !0;
            }

            return !1;
        };

    t.prototype = {
        constructor: t,
        init: function init() {
            var c = navigator.appVersion;
            b.isLowIE = b.isIE8 = document.all && !document.addEventListener, b.isAndroid = /android/gi.test(c), b.isIOS = /iphone|ipad|ipod/gi.test(c), b.supportsTransition = B(), b.probablyMobile = b.isAndroid || b.isIOS || /(Opera Mini)|Kindle|webOS|BlackBerry|(Opera Mobi)|(Windows Phone)|IEMobile/i.test(navigator.userAgent), d = a(document), b.popupsCache = {};
        },
        open: function open(c) {
            var e;

            if (c.isObj === !1) {
                b.items = c.items.toArray(), b.index = 0;
                var g,
                    h = c.items;

                for (e = 0; e < h.length; e++) {
                    if (g = h[e], g.parsed && (g = g.el[0]), g === c.el[0]) {
                        b.index = e;
                        break;
                    }
                }
            } else b.items = a.isArray(c.items) ? c.items : [c.items], b.index = c.index || 0;

            if (b.isOpen) return void b.updateItemHTML();
            b.types = [], f = "", c.mainEl && c.mainEl.length ? b.ev = c.mainEl.eq(0) : b.ev = d, c.key ? (b.popupsCache[c.key] || (b.popupsCache[c.key] = {}), b.currTemplate = b.popupsCache[c.key]) : b.currTemplate = {}, b.st = a.extend(!0, {}, a.magnificPopup.defaults, c), b.fixedContentPos = "auto" === b.st.fixedContentPos ? !b.probablyMobile : b.st.fixedContentPos, b.st.modal && (b.st.closeOnContentClick = !1, b.st.closeOnBgClick = !1, b.st.showCloseBtn = !1, b.st.enableEscapeKey = !1), b.bgOverlay || (b.bgOverlay = x("bg").on("click" + p, function () {
                b.close();
            }), b.wrap = x("wrap").attr("tabindex", -1).on("click" + p, function (a) {
                b._checkIfClose(a.target) && b.close();
            }), b.container = x("container", b.wrap)), b.contentContainer = x("content"), b.st.preloader && (b.preloader = x("preloader", b.container, b.st.tLoading));
            var i = a.magnificPopup.modules;

            for (e = 0; e < i.length; e++) {
                var j = i[e];
                j = j.charAt(0).toUpperCase() + j.slice(1), b["init" + j].call(b);
            }

            y("BeforeOpen"), b.st.showCloseBtn && (b.st.closeBtnInside ? (w(l, function (a, b, c, d) {
                c.close_replaceWith = z(d.type);
            }), f += " mfp-close-btn-in") : b.wrap.append(z())), b.st.alignTop && (f += " mfp-align-top"), b.fixedContentPos ? b.wrap.css({
                overflow: b.st.overflowY,
                overflowX: "hidden",
                overflowY: b.st.overflowY
            }) : b.wrap.css({
                top: v.scrollTop(),
                position: "absolute"
            }), (b.st.fixedBgPos === !1 || "auto" === b.st.fixedBgPos && !b.fixedContentPos) && b.bgOverlay.css({
                height: d.height(),
                position: "absolute"
            }), b.st.enableEscapeKey && d.on("keyup" + p, function (a) {
                27 === a.keyCode && b.close();
            }), v.on("resize" + p, function () {
                b.updateSize();
            }), b.st.closeOnContentClick || (f += " mfp-auto-cursor"), f && b.wrap.addClass(f);
            var k = b.wH = v.height(),
                n = {};

            if (b.fixedContentPos && b._hasScrollBar(k)) {
                var o = b._getScrollbarSize();

                o && (n.marginRight = o);
            }

            b.fixedContentPos && (b.isIE7 ? a("body, html").css("overflow", "hidden") : n.overflow = "hidden");
            var r = b.st.mainClass;
            return b.isIE7 && (r += " mfp-ie7"), r && b._addClassToMFP(r), b.updateItemHTML(), y("BuildControls"), a("html").css(n), b.bgOverlay.add(b.wrap).prependTo(b.st.prependTo || a(document.body)), b._lastFocusedEl = document.activeElement, setTimeout(function () {
                b.content ? (b._addClassToMFP(q), b._setFocus()) : b.bgOverlay.addClass(q), d.on("focusin" + p, b._onFocusIn);
            }, 16), b.isOpen = !0, b.updateSize(k), y(m), c;
        },
        close: function close() {
            b.isOpen && (y(i), b.isOpen = !1, b.st.removalDelay && !b.isLowIE && b.supportsTransition ? (b._addClassToMFP(r), setTimeout(function () {
                b._close();
            }, b.st.removalDelay)) : b._close());
        },
        _close: function _close() {
            y(h);
            var c = r + " " + q + " ";

            if (b.bgOverlay.detach(), b.wrap.detach(), b.container.empty(), b.st.mainClass && (c += b.st.mainClass + " "), b._removeClassFromMFP(c), b.fixedContentPos) {
                var e = {
                    marginRight: ""
                };
                b.isIE7 ? a("body, html").css("overflow", "") : e.overflow = "", a("html").css(e);
            }

            d.off("keyup" + p + " focusin" + p), b.ev.off(p), b.wrap.attr("class", "mfp-wrap").removeAttr("style"), b.bgOverlay.attr("class", "mfp-bg"), b.container.attr("class", "mfp-container"), !b.st.showCloseBtn || b.st.closeBtnInside && b.currTemplate[b.currItem.type] !== !0 || b.currTemplate.closeBtn && b.currTemplate.closeBtn.detach(), b.st.autoFocusLast && b._lastFocusedEl && a(b._lastFocusedEl).focus(), b.currItem = null, b.content = null, b.currTemplate = null, b.prevHeight = 0, y(j);
        },
        updateSize: function updateSize(a) {
            if (b.isIOS) {
                var c = document.documentElement.clientWidth / window.innerWidth,
                    d = window.innerHeight * c;
                b.wrap.css("height", d), b.wH = d;
            } else b.wH = a || v.height();

            b.fixedContentPos || b.wrap.css("height", b.wH), y("Resize");
        },
        updateItemHTML: function updateItemHTML() {
            var c = b.items[b.index];
            b.contentContainer.detach(), b.content && b.content.detach(), c.parsed || (c = b.parseEl(b.index));
            var d = c.type;

            if (y("BeforeChange", [b.currItem ? b.currItem.type : "", d]), b.currItem = c, !b.currTemplate[d]) {
                var f = b.st[d] ? b.st[d].markup : !1;
                y("FirstMarkupParse", f), f ? b.currTemplate[d] = a(f) : b.currTemplate[d] = !0;
            }

            e && e !== c.type && b.container.removeClass("mfp-" + e + "-holder");
            var g = b["get" + d.charAt(0).toUpperCase() + d.slice(1)](c, b.currTemplate[d]);
            b.appendContent(g, d), c.preloaded = !0, y(n, c), e = c.type, b.container.prepend(b.contentContainer), y("AfterChange");
        },
        appendContent: function appendContent(a, c) {
            b.content = a, a ? b.st.showCloseBtn && b.st.closeBtnInside && b.currTemplate[c] === !0 ? b.content.find(".mfp-close").length || b.content.append(z()) : b.content = a : b.content = "", y(k), b.container.addClass("mfp-" + c + "-holder"), b.contentContainer.append(b.content);
        },
        parseEl: function parseEl(c) {
            var d,
                e = b.items[c];

            if (e.tagName ? e = {
                el: a(e)
            } : (d = e.type, e = {
                data: e,
                src: e.src
            }), e.el) {
                for (var f = b.types, g = 0; g < f.length; g++) {
                    if (e.el.hasClass("mfp-" + f[g])) {
                        d = f[g];
                        break;
                    }
                }

                e.src = e.el.attr("data-mfp-src"), e.src || (e.src = e.el.attr("href"));
            }

            return e.type = d || b.st.type || "inline", e.index = c, e.parsed = !0, b.items[c] = e, y("ElementParse", e), b.items[c];
        },
        addGroup: function addGroup(a, c) {
            var d = function d(_d) {
                _d.mfpEl = this, b._openClick(_d, a, c);
            };

            c || (c = {});
            var e = "click.magnificPopup";
            c.mainEl = a, c.items ? (c.isObj = !0, a.off(e).on(e, d)) : (c.isObj = !1, c.delegate ? a.off(e).on(e, c.delegate, d) : (c.items = a, a.off(e).on(e, d)));
        },
        _openClick: function _openClick(c, d, e) {
            var f = void 0 !== e.midClick ? e.midClick : a.magnificPopup.defaults.midClick;

            if (f || !(2 === c.which || c.ctrlKey || c.metaKey || c.altKey || c.shiftKey)) {
                var g = void 0 !== e.disableOn ? e.disableOn : a.magnificPopup.defaults.disableOn;
                if (g) if (a.isFunction(g)) {
                    if (!g.call(b)) return !0;
                } else if (v.width() < g) return !0;
                c.type && (c.preventDefault(), b.isOpen && c.stopPropagation()), e.el = a(c.mfpEl), e.delegate && (e.items = d.find(e.delegate)), b.open(e);
            }
        },
        updateStatus: function updateStatus(a, d) {
            if (b.preloader) {
                c !== a && b.container.removeClass("mfp-s-" + c), d || "loading" !== a || (d = b.st.tLoading);
                var e = {
                    status: a,
                    text: d
                };
                y("UpdateStatus", e), a = e.status, d = e.text, b.preloader.html(d), b.preloader.find("a").on("click", function (a) {
                    a.stopImmediatePropagation();
                }), b.container.addClass("mfp-s-" + a), c = a;
            }
        },
        _checkIfClose: function _checkIfClose(c) {
            if (!a(c).hasClass(s)) {
                var d = b.st.closeOnContentClick,
                    e = b.st.closeOnBgClick;
                if (d && e) return !0;
                if (!b.content || a(c).hasClass("mfp-close") || b.preloader && c === b.preloader[0]) return !0;

                if (c === b.content[0] || a.contains(b.content[0], c)) {
                    if (d) return !0;
                } else if (e && a.contains(document, c)) return !0;

                return !1;
            }
        },
        _addClassToMFP: function _addClassToMFP(a) {
            b.bgOverlay.addClass(a), b.wrap.addClass(a);
        },
        _removeClassFromMFP: function _removeClassFromMFP(a) {
            this.bgOverlay.removeClass(a), b.wrap.removeClass(a);
        },
        _hasScrollBar: function _hasScrollBar(a) {
            return (b.isIE7 ? d.height() : document.body.scrollHeight) > (a || v.height());
        },
        _setFocus: function _setFocus() {
            (b.st.focus ? b.content.find(b.st.focus).eq(0) : b.wrap).focus();
        },
        _onFocusIn: function _onFocusIn(c) {
            return c.target === b.wrap[0] || a.contains(b.wrap[0], c.target) ? void 0 : (b._setFocus(), !1);
        },
        _parseMarkup: function _parseMarkup(b, c, d) {
            var e;
            d.data && (c = a.extend(d.data, c)), y(l, [b, c, d]), a.each(c, function (c, d) {
                if (void 0 === d || d === !1) return !0;

                if (e = c.split("_"), e.length > 1) {
                    var f = b.find(p + "-" + e[0]);

                    if (f.length > 0) {
                        var g = e[1];
                        "replaceWith" === g ? f[0] !== d[0] && f.replaceWith(d) : "img" === g ? f.is("img") ? f.attr("src", d) : f.replaceWith(a("<img>").attr("src", d).attr("class", f.attr("class"))) : f.attr(e[1], d);
                    }
                } else b.find(p + "-" + c).html(d);
            });
        },
        _getScrollbarSize: function _getScrollbarSize() {
            if (void 0 === b.scrollbarSize) {
                var a = document.createElement("div");
                a.style.cssText = "width: 99px; height: 99px; overflow: scroll; position: absolute; top: -9999px;", document.body.appendChild(a), b.scrollbarSize = a.offsetWidth - a.clientWidth, document.body.removeChild(a);
            }

            return b.scrollbarSize;
        }
    }, a.magnificPopup = {
        instance: null,
        proto: t.prototype,
        modules: [],
        open: function open(b, c) {
            return A(), b = b ? a.extend(!0, {}, b) : {}, b.isObj = !0, b.index = c || 0, this.instance.open(b);
        },
        close: function close() {
            return a.magnificPopup.instance && a.magnificPopup.instance.close();
        },
        registerModule: function registerModule(b, c) {
            c.options && (a.magnificPopup.defaults[b] = c.options), a.extend(this.proto, c.proto), this.modules.push(b);
        },
        defaults: {
            disableOn: 0,
            key: null,
            midClick: !1,
            mainClass: "",
            preloader: !0,
            focus: "",
            closeOnContentClick: !1,
            closeOnBgClick: !0,
            closeBtnInside: !0,
            showCloseBtn: !0,
            enableEscapeKey: !0,
            modal: !1,
            alignTop: !1,
            removalDelay: 0,
            prependTo: null,
            fixedContentPos: "auto",
            fixedBgPos: "auto",
            overflowY: "auto",
            closeMarkup: '<button title="%title%" type="button" class="mfp-close">&#215;</button>',
            tClose: "Close (Esc)",
            tLoading: "Loading...",
            autoFocusLast: !0
        }
    }, a.fn.magnificPopup = function (c) {
        A();
        var d = a(this);
        if ("string" == typeof c) {
            if ("open" === c) {
                var e,
                    f = u ? d.data("magnificPopup") : d[0].magnificPopup,
                    g = parseInt(arguments[1], 10) || 0;
                f.items ? e = f.items[g] : (e = d, f.delegate && (e = e.find(f.delegate)), e = e.eq(g)), b._openClick({
                    mfpEl: e
                }, d, f);
            } else b.isOpen && b[c].apply(b, Array.prototype.slice.call(arguments, 1));
        } else c = a.extend(!0, {}, c), u ? d.data("magnificPopup", c) : d[0].magnificPopup = c, b.addGroup(d, c);
        return d;
    };

    var C,
        D,
        E,
        F = "inline",
        G = function G() {
            E && (D.after(E.addClass(C)).detach(), E = null);
        };

    a.magnificPopup.registerModule(F, {
        options: {
            hiddenClass: "hide",
            markup: "",
            tNotFound: "Content not found"
        },
        proto: {
            initInline: function initInline() {
                b.types.push(F), w(h + "." + F, function () {
                    G();
                });
            },
            getInline: function getInline(c, d) {
                if (G(), c.src) {
                    var e = b.st.inline,
                        f = a(c.src);

                    if (f.length) {
                        var g = f[0].parentNode;
                        g && g.tagName && (D || (C = e.hiddenClass, D = x(C), C = "mfp-" + C), E = f.after(D).detach().removeClass(C)), b.updateStatus("ready");
                    } else b.updateStatus("error", e.tNotFound), f = a("<div>");

                    return c.inlineElement = f, f;
                }

                return b.updateStatus("ready"), b._parseMarkup(d, {}, c), d;
            }
        }
    });

    var H,
        I = "ajax",
        J = function J() {
            H && a(document.body).removeClass(H);
        },
        K = function K() {
            J(), b.req && b.req.abort();
        };

    a.magnificPopup.registerModule(I, {
        options: {
            settings: null,
            cursor: "mfp-ajax-cur",
            tError: '<a href="%url%">The content</a> could not be loaded.'
        },
        proto: {
            initAjax: function initAjax() {
                b.types.push(I), H = b.st.ajax.cursor, w(h + "." + I, K), w("BeforeChange." + I, K);
            },
            getAjax: function getAjax(c) {
                H && a(document.body).addClass(H), b.updateStatus("loading");
                var d = a.extend({
                    url: c.src,
                    success: function success(d, e, f) {
                        var g = {
                            data: d,
                            xhr: f
                        };
                        y("ParseAjax", g), b.appendContent(a(g.data), I), c.finished = !0, J(), b._setFocus(), setTimeout(function () {
                            b.wrap.addClass(q);
                        }, 16), b.updateStatus("ready"), y("AjaxContentAdded");
                    },
                    error: function error() {
                        J(), c.finished = c.loadError = !0, b.updateStatus("error", b.st.ajax.tError.replace("%url%", c.src));
                    }
                }, b.st.ajax.settings);
                return b.req = a.ajax(d), "";
            }
        }
    });

    var L,
        M = function M(c) {
            if (c.data && void 0 !== c.data.title) return c.data.title;
            var d = b.st.image.titleSrc;

            if (d) {
                if (a.isFunction(d)) return d.call(b, c);
                if (c.el) return c.el.attr(d) || "";
            }

            return "";
        };

    a.magnificPopup.registerModule("image", {
        options: {
            markup: '<div class="mfp-figure"><div class="mfp-close"></div><figure><div class="mfp-img"></div><figcaption><div class="mfp-bottom-bar"><div class="mfp-title"></div><div class="mfp-counter"></div></div></figcaption></figure></div>',
            cursor: "mfp-zoom-out-cur",
            titleSrc: "title",
            verticalFit: !0,
            tError: '<a href="%url%">The image</a> could not be loaded.'
        },
        proto: {
            initImage: function initImage() {
                var c = b.st.image,
                    d = ".image";
                b.types.push("image"), w(m + d, function () {
                    "image" === b.currItem.type && c.cursor && a(document.body).addClass(c.cursor);
                }), w(h + d, function () {
                    c.cursor && a(document.body).removeClass(c.cursor), v.off("resize" + p);
                }), w("Resize" + d, b.resizeImage), b.isLowIE && w("AfterChange", b.resizeImage);
            },
            resizeImage: function resizeImage() {
                var a = b.currItem;

                if (a && a.img && b.st.image.verticalFit) {
                    var c = 0;
                    b.isLowIE && (c = parseInt(a.img.css("padding-top"), 10) + parseInt(a.img.css("padding-bottom"), 10)), a.img.css("max-height", b.wH - c);
                }
            },
            _onImageHasSize: function _onImageHasSize(a) {
                a.img && (a.hasSize = !0, L && clearInterval(L), a.isCheckingImgSize = !1, y("ImageHasSize", a), a.imgHidden && (b.content && b.content.removeClass("mfp-loading"), a.imgHidden = !1));
            },
            findImageSize: function findImageSize(a) {
                var c = 0,
                    d = a.img[0],
                    e = function e(f) {
                        L && clearInterval(L), L = setInterval(function () {
                            return d.naturalWidth > 0 ? void b._onImageHasSize(a) : (c > 200 && clearInterval(L), c++, void (3 === c ? e(10) : 40 === c ? e(50) : 100 === c && e(500)));
                        }, f);
                    };

                e(1);
            },
            getImage: function getImage(c, d) {
                var e = 0,
                    f = function f() {
                        c && (c.img[0].complete ? (c.img.off(".mfploader"), c === b.currItem && (b._onImageHasSize(c), b.updateStatus("ready")), c.hasSize = !0, c.loaded = !0, y("ImageLoadComplete")) : (e++, 200 > e ? setTimeout(f, 100) : g()));
                    },
                    g = function g() {
                        c && (c.img.off(".mfploader"), c === b.currItem && (b._onImageHasSize(c), b.updateStatus("error", h.tError.replace("%url%", c.src))), c.hasSize = !0, c.loaded = !0, c.loadError = !0);
                    },
                    h = b.st.image,
                    i = d.find(".mfp-img");

                if (i.length) {
                    var j = document.createElement("img");
                    j.className = "mfp-img", c.el && c.el.find("img").length && (j.alt = c.el.find("img").attr("alt")), c.img = a(j).on("load.mfploader", f).on("error.mfploader", g), j.src = c.src, i.is("img") && (c.img = c.img.clone()), j = c.img[0], j.naturalWidth > 0 ? c.hasSize = !0 : j.width || (c.hasSize = !1);
                }

                return b._parseMarkup(d, {
                    title: M(c),
                    img_replaceWith: c.img
                }, c), b.resizeImage(), c.hasSize ? (L && clearInterval(L), c.loadError ? (d.addClass("mfp-loading"), b.updateStatus("error", h.tError.replace("%url%", c.src))) : (d.removeClass("mfp-loading"), b.updateStatus("ready")), d) : (b.updateStatus("loading"), c.loading = !0, c.hasSize || (c.imgHidden = !0, d.addClass("mfp-loading"), b.findImageSize(c)), d);
            }
        }
    });

    var N,
        O = function O() {
            return void 0 === N && (N = void 0 !== document.createElement("p").style.MozTransform), N;
        };

    a.magnificPopup.registerModule("zoom", {
        options: {
            enabled: !1,
            easing: "ease-in-out",
            duration: 300,
            opener: function opener(a) {
                return a.is("img") ? a : a.find("img");
            }
        },
        proto: {
            initZoom: function initZoom() {
                var a,
                    c = b.st.zoom,
                    d = ".zoom";

                if (c.enabled && b.supportsTransition) {
                    var e,
                        f,
                        g = c.duration,
                        j = function j(a) {
                            var b = a.clone().removeAttr("style").removeAttr("class").addClass("mfp-animated-image"),
                                d = "all " + c.duration / 1e3 + "s " + c.easing,
                                e = {
                                    position: "fixed",
                                    zIndex: 9999,
                                    left: 0,
                                    top: 0,
                                    "-webkit-backface-visibility": "hidden"
                                },
                                f = "transition";
                            return e["-webkit-" + f] = e["-moz-" + f] = e["-o-" + f] = e[f] = d, b.css(e), b;
                        },
                        k = function k() {
                            b.content.css("visibility", "visible");
                        };

                    w("BuildControls" + d, function () {
                        if (b._allowZoom()) {
                            if (clearTimeout(e), b.content.css("visibility", "hidden"), a = b._getItemToZoom(), !a) return void k();
                            f = j(a), f.css(b._getOffset()), b.wrap.append(f), e = setTimeout(function () {
                                f.css(b._getOffset(!0)), e = setTimeout(function () {
                                    k(), setTimeout(function () {
                                        f.remove(), a = f = null, y("ZoomAnimationEnded");
                                    }, 16);
                                }, g);
                            }, 16);
                        }
                    }), w(i + d, function () {
                        if (b._allowZoom()) {
                            if (clearTimeout(e), b.st.removalDelay = g, !a) {
                                if (a = b._getItemToZoom(), !a) return;
                                f = j(a);
                            }

                            f.css(b._getOffset(!0)), b.wrap.append(f), b.content.css("visibility", "hidden"), setTimeout(function () {
                                f.css(b._getOffset());
                            }, 16);
                        }
                    }), w(h + d, function () {
                        b._allowZoom() && (k(), f && f.remove(), a = null);
                    });
                }
            },
            _allowZoom: function _allowZoom() {
                return "image" === b.currItem.type;
            },
            _getItemToZoom: function _getItemToZoom() {
                return b.currItem.hasSize ? b.currItem.img : !1;
            },
            _getOffset: function _getOffset(c) {
                var d;
                d = c ? b.currItem.img : b.st.zoom.opener(b.currItem.el || b.currItem);
                var e = d.offset(),
                    f = parseInt(d.css("padding-top"), 10),
                    g = parseInt(d.css("padding-bottom"), 10);
                e.top -= a(window).scrollTop() - f;
                var h = {
                    width: d.width(),
                    height: (u ? d.innerHeight() : d[0].offsetHeight) - g - f
                };
                return O() ? h["-moz-transform"] = h.transform = "translate(" + e.left + "px," + e.top + "px)" : (h.left = e.left, h.top = e.top), h;
            }
        }
    });

    var P = "iframe",
        Q = "//about:blank",
        R = function R(a) {
            if (b.currTemplate[P]) {
                var c = b.currTemplate[P].find("iframe");
                c.length && (a || (c[0].src = Q), b.isIE8 && c.css("display", a ? "block" : "none"));
            }
        };

    a.magnificPopup.registerModule(P, {
        options: {
            markup: '<div class="mfp-iframe-scaler"><div class="mfp-close"></div><iframe class="mfp-iframe" src="//about:blank" frameborder="0" allowfullscreen></iframe></div>',
            srcAction: "iframe_src",
            patterns: {
                youtube: {
                    index: "youtube.com",
                    id: "v=",
                    src: "//www.youtube.com/embed/%id%?autoplay=1"
                },
                vimeo: {
                    index: "vimeo.com/",
                    id: "/",
                    src: "//player.vimeo.com/video/%id%?autoplay=1"
                },
                gmaps: {
                    index: "//maps.google.",
                    src: "%id%&output=embed"
                }
            }
        },
        proto: {
            initIframe: function initIframe() {
                b.types.push(P), w("BeforeChange", function (a, b, c) {
                    b !== c && (b === P ? R() : c === P && R(!0));
                }), w(h + "." + P, function () {
                    R();
                });
            },
            getIframe: function getIframe(c, d) {
                var e = c.src,
                    f = b.st.iframe;
                a.each(f.patterns, function () {
                    return e.indexOf(this.index) > -1 ? (this.id && (e = "string" == typeof this.id ? e.substr(e.lastIndexOf(this.id) + this.id.length, e.length) : this.id.call(this, e)), e = this.src.replace("%id%", e), !1) : void 0;
                });
                var g = {};
                return f.srcAction && (g[f.srcAction] = e), b._parseMarkup(d, g, c), b.updateStatus("ready"), d;
            }
        }
    });

    var S = function S(a) {
        var c = b.items.length;
        return a > c - 1 ? a - c : 0 > a ? c + a : a;
    },
        T = function T(a, b, c) {
            return a.replace(/%curr%/gi, b + 1).replace(/%total%/gi, c);
        };

    a.magnificPopup.registerModule("gallery", {
        options: {
            enabled: !1,
            arrowMarkup: '<button title="%title%" type="button" class="mfp-arrow mfp-arrow-%dir%"></button>',
            preload: [0, 2],
            navigateByImgClick: !0,
            arrows: !0,
            tPrev: "Previous (Left arrow key)",
            tNext: "Next (Right arrow key)",
            tCounter: "%curr% of %total%"
        },
        proto: {
            initGallery: function initGallery() {
                var c = b.st.gallery,
                    e = ".mfp-gallery";
                return b.direction = !0, c && c.enabled ? (f += " mfp-gallery", w(m + e, function () {
                    c.navigateByImgClick && b.wrap.on("click" + e, ".mfp-img", function () {
                        return b.items.length > 1 ? (b.next(), !1) : void 0;
                    }), d.on("keydown" + e, function (a) {
                        37 === a.keyCode ? b.prev() : 39 === a.keyCode && b.next();
                    });
                }), w("UpdateStatus" + e, function (a, c) {
                    c.text && (c.text = T(c.text, b.currItem.index, b.items.length));
                }), w(l + e, function (a, d, e, f) {
                    var g = b.items.length;
                    e.counter = g > 1 ? T(c.tCounter, f.index, g) : "";
                }), w("BuildControls" + e, function () {
                    if (b.items.length > 1 && c.arrows && !b.arrowLeft) {
                        var d = c.arrowMarkup,
                            e = b.arrowLeft = a(d.replace(/%title%/gi, c.tPrev).replace(/%dir%/gi, "left")).addClass(s),
                            f = b.arrowRight = a(d.replace(/%title%/gi, c.tNext).replace(/%dir%/gi, "right")).addClass(s);
                        e.click(function () {
                            b.prev();
                        }), f.click(function () {
                            b.next();
                        }), b.container.append(e.add(f));
                    }
                }), w(n + e, function () {
                    b._preloadTimeout && clearTimeout(b._preloadTimeout), b._preloadTimeout = setTimeout(function () {
                        b.preloadNearbyImages(), b._preloadTimeout = null;
                    }, 16);
                }), void w(h + e, function () {
                    d.off(e), b.wrap.off("click" + e), b.arrowRight = b.arrowLeft = null;
                })) : !1;
            },
            next: function next() {
                b.direction = !0, b.index = S(b.index + 1), b.updateItemHTML();
            },
            prev: function prev() {
                b.direction = !1, b.index = S(b.index - 1), b.updateItemHTML();
            },
            goTo: function goTo(a) {
                b.direction = a >= b.index, b.index = a, b.updateItemHTML();
            },
            preloadNearbyImages: function preloadNearbyImages() {
                var a,
                    c = b.st.gallery.preload,
                    d = Math.min(c[0], b.items.length),
                    e = Math.min(c[1], b.items.length);

                for (a = 1; a <= (b.direction ? e : d); a++) {
                    b._preloadItem(b.index + a);
                }

                for (a = 1; a <= (b.direction ? d : e); a++) {
                    b._preloadItem(b.index - a);
                }
            },
            _preloadItem: function _preloadItem(c) {
                if (c = S(c), !b.items[c].preloaded) {
                    var d = b.items[c];
                    d.parsed || (d = b.parseEl(c)), y("LazyLoad", d), "image" === d.type && (d.img = a('<img class="mfp-img" />').on("load.mfploader", function () {
                        d.hasSize = !0;
                    }).on("error.mfploader", function () {
                        d.hasSize = !0, d.loadError = !0, y("LazyLoadError", d);
                    }).attr("src", d.src)), d.preloaded = !0;
                }
            }
        }
    });
    var U = "retina";
    a.magnificPopup.registerModule(U, {
        options: {
            replaceSrc: function replaceSrc(a) {
                return a.src.replace(/\.\w+$/, function (a) {
                    return "@2x" + a;
                });
            },
            ratio: 1
        },
        proto: {
            initRetina: function initRetina() {
                if (window.devicePixelRatio > 1) {
                    var a = b.st.retina,
                        c = a.ratio;
                    c = isNaN(c) ? c() : c, c > 1 && (w("ImageHasSize." + U, function (a, b) {
                        b.img.css({
                            "max-width": b.img[0].naturalWidth / c,
                            width: "100%"
                        });
                    }), w("ElementParse." + U, function (b, d) {
                        d.src = a.replaceSrc(d, c);
                    }));
                }
            }
        }
    }), A();
});

(function (factory) {
    if (typeof define === 'function' && define.amd) {
        // AMD. Register as an anonymous module.
        define(['jquery'], factory);
    } else if ((typeof exports === "undefined" ? "undefined" : _typeof(exports)) === 'object') {
        // Node/CommonJS style for Browserify
        module.exports = factory(require('jquery'));
    } else {
        // Browser globals
        factory(jQuery);
    }
})(function ($) {
    $.fn.jScrollPane = function (settings) {
        // JScrollPane "class" - public methods are available through $('selector').data('jsp')
        function JScrollPane(elem, s) {
            var settings,
                jsp = this,
                pane,
                paneWidth,
                paneHeight,
                container,
                contentWidth,
                contentHeight,
                percentInViewH,
                percentInViewV,
                isScrollableV,
                isScrollableH,
                verticalDrag,
                dragMaxY,
                verticalDragPosition,
                horizontalDrag,
                dragMaxX,
                horizontalDragPosition,
                verticalBar,
                verticalTrack,
                scrollbarWidth,
                verticalTrackHeight,
                verticalDragHeight,
                arrowUp,
                arrowDown,
                horizontalBar,
                horizontalTrack,
                horizontalTrackWidth,
                horizontalDragWidth,
                arrowLeft,
                arrowRight,
                reinitialiseInterval,
                originalPadding,
                originalPaddingTotalWidth,
                previousContentWidth,
                wasAtTop = true,
                wasAtLeft = true,
                wasAtBottom = false,
                wasAtRight = false,
                originalElement = elem.clone(false, false).empty(),
                mwEvent = $.fn.mwheelIntent ? 'mwheelIntent.jsp' : 'mousewheel.jsp';

            if (elem.css('box-sizing') === 'border-box') {
                originalPadding = 0;
                originalPaddingTotalWidth = 0;
            } else {
                originalPadding = elem.css('paddingTop') + ' ' + elem.css('paddingRight') + ' ' + elem.css('paddingBottom') + ' ' + elem.css('paddingLeft');
                originalPaddingTotalWidth = (parseInt(elem.css('paddingLeft'), 10) || 0) + (parseInt(elem.css('paddingRight'), 10) || 0);
            }

            function initialise(s) {
                var
                    /*firstChild, lastChild, */
                    isMaintainingPositon,
                    lastContentX,
                    lastContentY,
                    hasContainingSpaceChanged,
                    originalScrollTop,
                    originalScrollLeft,
                    maintainAtBottom = false,
                    maintainAtRight = false;
                settings = s;

                if (pane === undefined) {
                    originalScrollTop = elem.scrollTop();
                    originalScrollLeft = elem.scrollLeft();
                    elem.css({
                        overflow: 'hidden',
                        padding: 0
                    }); // TODO: Deal with where width/ height is 0 as it probably means the element is hidden and we should
                    // come back to it later and check once it is unhidden...

                    paneWidth = elem.innerWidth() + originalPaddingTotalWidth;
                    paneHeight = elem.innerHeight();
                    elem.width(paneWidth);
                    pane = $('<div class="jspPane" />').css('padding', originalPadding).append(elem.children());
                    container = $('<div class="jspContainer" />').css({
                        'width': paneWidth + 'px',
                        'height': paneHeight + 'px'
                    }).append(pane).appendTo(elem);
                    /*
                    // Move any margins from the first and last children up to the container so they can still
                    // collapse with neighbouring elements as they would before jScrollPane
                    firstChild = pane.find(':first-child');
                    lastChild = pane.find(':last-child');
                    elem.css(
                      {
                        'margin-top': firstChild.css('margin-top'),
                        'margin-bottom': lastChild.css('margin-bottom')
                      }
                    );
                    firstChild.css('margin-top', 0);
                    lastChild.css('margin-bottom', 0);
                    */
                } else {
                    elem.css('width', '');
                    maintainAtBottom = settings.stickToBottom && isCloseToBottom();
                    maintainAtRight = settings.stickToRight && isCloseToRight();
                    hasContainingSpaceChanged = elem.innerWidth() + originalPaddingTotalWidth != paneWidth || elem.outerHeight() != paneHeight;

                    if (hasContainingSpaceChanged) {
                        paneWidth = elem.innerWidth() + originalPaddingTotalWidth;
                        paneHeight = elem.innerHeight();
                        container.css({
                            width: paneWidth + 'px',
                            height: paneHeight + 'px'
                        });
                    } // If nothing changed since last check...

                    if (!hasContainingSpaceChanged && previousContentWidth == contentWidth && pane.outerHeight() == contentHeight) {
                        elem.width(paneWidth);
                        return;
                    }

                    previousContentWidth = contentWidth;
                    pane.css('width', '');
                    elem.width(paneWidth);
                    container.find('>.jspVerticalBar,>.jspHorizontalBar').remove().end();
                }

                pane.css('overflow', 'auto');

                if (s.contentWidth) {
                    contentWidth = s.contentWidth;
                } else {
                    contentWidth = pane[0].scrollWidth;
                }

                contentHeight = pane[0].scrollHeight;
                pane.css('overflow', '');
                percentInViewH = contentWidth / paneWidth;
                percentInViewV = contentHeight / paneHeight;
                isScrollableV = percentInViewV > 1;
                isScrollableH = percentInViewH > 1; //console.log(paneWidth, paneHeight, contentWidth, contentHeight, percentInViewH, percentInViewV, isScrollableH, isScrollableV);

                if (!(isScrollableH || isScrollableV)) {
                    elem.removeClass('jspScrollable');
                    pane.css({
                        top: 0,
                        left: 0,
                        width: container.width() - originalPaddingTotalWidth
                    });
                    removeMousewheel();
                    removeFocusHandler();
                    removeKeyboardNav();
                    removeClickOnTrack();
                } else {
                    elem.addClass('jspScrollable');
                    isMaintainingPositon = settings.maintainPosition && (verticalDragPosition || horizontalDragPosition);

                    if (isMaintainingPositon) {
                        lastContentX = contentPositionX();
                        lastContentY = contentPositionY();
                    }

                    initialiseVerticalScroll();
                    initialiseHorizontalScroll();
                    resizeScrollbars();

                    if (isMaintainingPositon) {
                        _scrollToX(maintainAtRight ? contentWidth - paneWidth : lastContentX, false);

                        _scrollToY(maintainAtBottom ? contentHeight - paneHeight : lastContentY, false);
                    }

                    initFocusHandler();
                    initMousewheel();
                    initTouch();

                    if (settings.enableKeyboardNavigation) {
                        initKeyboardNav();
                    }

                    if (settings.clickOnTrack) {
                        initClickOnTrack();
                    }

                    observeHash();

                    if (settings.hijackInternalLinks) {
                        hijackInternalLinks();
                    }
                }

                if (settings.autoReinitialise && !reinitialiseInterval) {
                    reinitialiseInterval = setInterval(function () {
                        initialise(settings);
                    }, settings.autoReinitialiseDelay);
                } else if (!settings.autoReinitialise && reinitialiseInterval) {
                    clearInterval(reinitialiseInterval);
                }

                originalScrollTop && elem.scrollTop(0) && _scrollToY(originalScrollTop, false);
                originalScrollLeft && elem.scrollLeft(0) && _scrollToX(originalScrollLeft, false);
                elem.trigger('jsp-initialised', [isScrollableH || isScrollableV]);
            }

            function initialiseVerticalScroll() {
                if (isScrollableV) {
                    container.append($('<div class="jspVerticalBar" />').append($('<div class="jspCap jspCapTop" />'), $('<div class="jspTrack" />').append($('<div class="jspDrag" />').append($('<div class="jspDragTop" />'), $('<div class="jspDragBottom" />'))), $('<div class="jspCap jspCapBottom" />')));
                    verticalBar = container.find('>.jspVerticalBar');
                    verticalTrack = verticalBar.find('>.jspTrack');
                    verticalDrag = verticalTrack.find('>.jspDrag');

                    if (settings.showArrows) {
                        arrowUp = $('<a class="jspArrow jspArrowUp" />').bind('mousedown.jsp', getArrowScroll(0, -1)).bind('click.jsp', nil);
                        arrowDown = $('<a class="jspArrow jspArrowDown" />').bind('mousedown.jsp', getArrowScroll(0, 1)).bind('click.jsp', nil);

                        if (settings.arrowScrollOnHover) {
                            arrowUp.bind('mouseover.jsp', getArrowScroll(0, -1, arrowUp));
                            arrowDown.bind('mouseover.jsp', getArrowScroll(0, 1, arrowDown));
                        }

                        appendArrows(verticalTrack, settings.verticalArrowPositions, arrowUp, arrowDown);
                    }

                    verticalTrackHeight = paneHeight;
                    container.find('>.jspVerticalBar>.jspCap:visible,>.jspVerticalBar>.jspArrow').each(function () {
                        verticalTrackHeight -= $(this).outerHeight();
                    });
                    verticalDrag.hover(function () {
                        verticalDrag.addClass('jspHover');
                    }, function () {
                        verticalDrag.removeClass('jspHover');
                    }).bind('mousedown.jsp', function (e) {
                        // Stop IE from allowing text selection
                        $('html').bind('dragstart.jsp selectstart.jsp', nil);
                        verticalDrag.addClass('jspActive');
                        var startY = e.pageY - verticalDrag.position().top;
                        $('html').bind('mousemove.jsp', function (e) {
                            _positionDragY2(e.pageY - startY, false);
                        }).bind('mouseup.jsp mouseleave.jsp', cancelDrag);
                        return false;
                    });
                    sizeVerticalScrollbar();
                }
            }

            function sizeVerticalScrollbar() {
                verticalTrack.height(verticalTrackHeight + 'px');
                verticalDragPosition = 0;
                scrollbarWidth = settings.verticalGutter + verticalTrack.outerWidth(); // Make the pane thinner to allow for the vertical scrollbar

                pane.width(paneWidth - scrollbarWidth - originalPaddingTotalWidth); // Add margin to the left of the pane if scrollbars are on that side (to position
                // the scrollbar on the left or right set it's left or right property in CSS)

                try {
                    if (verticalBar.position().left === 0) {
                        pane.css('margin-left', scrollbarWidth + 'px');
                    }
                } catch (err) { }
            }

            function initialiseHorizontalScroll() {
                if (isScrollableH) {
                    container.append($('<div class="jspHorizontalBar" />').append($('<div class="jspCap jspCapLeft" />'), $('<div class="jspTrack" />').append($('<div class="jspDrag" />').append($('<div class="jspDragLeft" />'), $('<div class="jspDragRight" />'))), $('<div class="jspCap jspCapRight" />')));
                    horizontalBar = container.find('>.jspHorizontalBar');
                    horizontalTrack = horizontalBar.find('>.jspTrack');
                    horizontalDrag = horizontalTrack.find('>.jspDrag');

                    if (settings.showArrows) {
                        arrowLeft = $('<a class="jspArrow jspArrowLeft" />').bind('mousedown.jsp', getArrowScroll(-1, 0)).bind('click.jsp', nil);
                        arrowRight = $('<a class="jspArrow jspArrowRight" />').bind('mousedown.jsp', getArrowScroll(1, 0)).bind('click.jsp', nil);

                        if (settings.arrowScrollOnHover) {
                            arrowLeft.bind('mouseover.jsp', getArrowScroll(-1, 0, arrowLeft));
                            arrowRight.bind('mouseover.jsp', getArrowScroll(1, 0, arrowRight));
                        }

                        appendArrows(horizontalTrack, settings.horizontalArrowPositions, arrowLeft, arrowRight);
                    }

                    horizontalDrag.hover(function () {
                        horizontalDrag.addClass('jspHover');
                    }, function () {
                        horizontalDrag.removeClass('jspHover');
                    }).bind('mousedown.jsp', function (e) {
                        // Stop IE from allowing text selection
                        $('html').bind('dragstart.jsp selectstart.jsp', nil);
                        horizontalDrag.addClass('jspActive');
                        var startX = e.pageX - horizontalDrag.position().left;
                        $('html').bind('mousemove.jsp', function (e) {
                            _positionDragX2(e.pageX - startX, false);
                        }).bind('mouseup.jsp mouseleave.jsp', cancelDrag);
                        return false;
                    });
                    horizontalTrackWidth = container.innerWidth();
                    sizeHorizontalScrollbar();
                }
            }

            function sizeHorizontalScrollbar() {
                container.find('>.jspHorizontalBar>.jspCap:visible,>.jspHorizontalBar>.jspArrow').each(function () {
                    horizontalTrackWidth -= $(this).outerWidth();
                });
                horizontalTrack.width(horizontalTrackWidth + 'px');
                horizontalDragPosition = 0;
            }

            function resizeScrollbars() {
                if (isScrollableH && isScrollableV) {
                    var horizontalTrackHeight = horizontalTrack.outerHeight(),
                        verticalTrackWidth = verticalTrack.outerWidth();
                    verticalTrackHeight -= horizontalTrackHeight;
                    $(horizontalBar).find('>.jspCap:visible,>.jspArrow').each(function () {
                        horizontalTrackWidth += $(this).outerWidth();
                    });
                    horizontalTrackWidth -= verticalTrackWidth;
                    paneHeight -= verticalTrackWidth;
                    paneWidth -= horizontalTrackHeight;
                    horizontalTrack.parent().append($('<div class="jspCorner" />').css('width', horizontalTrackHeight + 'px'));
                    sizeVerticalScrollbar();
                    sizeHorizontalScrollbar();
                } // reflow content

                if (isScrollableH) {
                    pane.width(container.outerWidth() - originalPaddingTotalWidth + 'px');
                }

                contentHeight = pane.outerHeight();
                percentInViewV = contentHeight / paneHeight;

                if (isScrollableH) {
                    horizontalDragWidth = Math.ceil(1 / percentInViewH * horizontalTrackWidth);

                    if (horizontalDragWidth > settings.horizontalDragMaxWidth) {
                        horizontalDragWidth = settings.horizontalDragMaxWidth;
                    } else if (horizontalDragWidth < settings.horizontalDragMinWidth) {
                        horizontalDragWidth = settings.horizontalDragMinWidth;
                    }

                    horizontalDrag.width(horizontalDragWidth + 'px');
                    dragMaxX = horizontalTrackWidth - horizontalDragWidth;

                    _positionDragX(horizontalDragPosition); // To update the state for the arrow buttons

                }

                if (isScrollableV) {
                    verticalDragHeight = Math.ceil(1 / percentInViewV * verticalTrackHeight);

                    if (verticalDragHeight > settings.verticalDragMaxHeight) {
                        verticalDragHeight = settings.verticalDragMaxHeight;
                    } else if (verticalDragHeight < settings.verticalDragMinHeight) {
                        verticalDragHeight = settings.verticalDragMinHeight;
                    }

                    verticalDrag.height(verticalDragHeight + 'px');
                    dragMaxY = verticalTrackHeight - verticalDragHeight;

                    _positionDragY(verticalDragPosition); // To update the state for the arrow buttons

                }
            }

            function appendArrows(ele, p, a1, a2) {
                var p1 = "before",
                    p2 = "after",
                    aTemp; // Sniff for mac... Is there a better way to determine whether the arrows would naturally appear
                // at the top or the bottom of the bar?

                if (p == "os") {
                    p = /Mac/.test(navigator.platform) ? "after" : "split";
                }

                if (p == p1) {
                    p2 = p;
                } else if (p == p2) {
                    p1 = p;
                    aTemp = a1;
                    a1 = a2;
                    a2 = aTemp;
                }

                ele[p1](a1)[p2](a2);
            }

            function getArrowScroll(dirX, dirY, ele) {
                return function () {
                    arrowScroll(dirX, dirY, this, ele);
                    this.blur();
                    return false;
                };
            }

            function arrowScroll(dirX, dirY, arrow, ele) {
                arrow = $(arrow).addClass('jspActive');

                var eve,
                    scrollTimeout,
                    isFirst = true,
                    doScroll = function doScroll() {
                        if (dirX !== 0) {
                            jsp.scrollByX(dirX * settings.arrowButtonSpeed);
                        }

                        if (dirY !== 0) {
                            jsp.scrollByY(dirY * settings.arrowButtonSpeed);
                        }

                        scrollTimeout = setTimeout(doScroll, isFirst ? settings.initialDelay : settings.arrowRepeatFreq);
                        isFirst = false;
                    };

                doScroll();
                eve = ele ? 'mouseout.jsp' : 'mouseup.jsp';
                ele = ele || $('html');
                ele.bind(eve, function () {
                    arrow.removeClass('jspActive');
                    scrollTimeout && clearTimeout(scrollTimeout);
                    scrollTimeout = null;
                    ele.unbind(eve);
                });
            }

            function initClickOnTrack() {
                removeClickOnTrack();

                if (isScrollableV) {
                    verticalTrack.bind('mousedown.jsp', function (e) {
                        if (e.originalTarget === undefined || e.originalTarget == e.currentTarget) {
                            var clickedTrack = $(this),
                                offset = clickedTrack.offset(),
                                direction = e.pageY - offset.top - verticalDragPosition,
                                scrollTimeout,
                                isFirst = true,
                                doScroll = function doScroll() {
                                    var offset = clickedTrack.offset(),
                                        pos = e.pageY - offset.top - verticalDragHeight / 2,
                                        contentDragY = paneHeight * settings.scrollPagePercent,
                                        dragY = dragMaxY * contentDragY / (contentHeight - paneHeight);

                                    if (direction < 0) {
                                        if (verticalDragPosition - dragY > pos) {
                                            jsp.scrollByY(-contentDragY);
                                        } else {
                                            _positionDragY2(pos);
                                        }
                                    } else if (direction > 0) {
                                        if (verticalDragPosition + dragY < pos) {
                                            jsp.scrollByY(contentDragY);
                                        } else {
                                            _positionDragY2(pos);
                                        }
                                    } else {
                                        cancelClick();
                                        return;
                                    }

                                    scrollTimeout = setTimeout(doScroll, isFirst ? settings.initialDelay : settings.trackClickRepeatFreq);
                                    isFirst = false;
                                },
                                cancelClick = function cancelClick() {
                                    scrollTimeout && clearTimeout(scrollTimeout);
                                    scrollTimeout = null;
                                    $(document).unbind('mouseup.jsp', cancelClick);
                                };

                            doScroll();
                            $(document).bind('mouseup.jsp', cancelClick);
                            return false;
                        }
                    });
                }

                if (isScrollableH) {
                    horizontalTrack.bind('mousedown.jsp', function (e) {
                        if (e.originalTarget === undefined || e.originalTarget == e.currentTarget) {
                            var clickedTrack = $(this),
                                offset = clickedTrack.offset(),
                                direction = e.pageX - offset.left - horizontalDragPosition,
                                scrollTimeout,
                                isFirst = true,
                                doScroll = function doScroll() {
                                    var offset = clickedTrack.offset(),
                                        pos = e.pageX - offset.left - horizontalDragWidth / 2,
                                        contentDragX = paneWidth * settings.scrollPagePercent,
                                        dragX = dragMaxX * contentDragX / (contentWidth - paneWidth);

                                    if (direction < 0) {
                                        if (horizontalDragPosition - dragX > pos) {
                                            jsp.scrollByX(-contentDragX);
                                        } else {
                                            _positionDragX2(pos);
                                        }
                                    } else if (direction > 0) {
                                        if (horizontalDragPosition + dragX < pos) {
                                            jsp.scrollByX(contentDragX);
                                        } else {
                                            _positionDragX2(pos);
                                        }
                                    } else {
                                        cancelClick();
                                        return;
                                    }

                                    scrollTimeout = setTimeout(doScroll, isFirst ? settings.initialDelay : settings.trackClickRepeatFreq);
                                    isFirst = false;
                                },
                                cancelClick = function cancelClick() {
                                    scrollTimeout && clearTimeout(scrollTimeout);
                                    scrollTimeout = null;
                                    $(document).unbind('mouseup.jsp', cancelClick);
                                };

                            doScroll();
                            $(document).bind('mouseup.jsp', cancelClick);
                            return false;
                        }
                    });
                }
            }

            function removeClickOnTrack() {
                if (horizontalTrack) {
                    horizontalTrack.unbind('mousedown.jsp');
                }

                if (verticalTrack) {
                    verticalTrack.unbind('mousedown.jsp');
                }
            }

            function cancelDrag() {
                $('html').unbind('dragstart.jsp selectstart.jsp mousemove.jsp mouseup.jsp mouseleave.jsp');

                if (verticalDrag) {
                    verticalDrag.removeClass('jspActive');
                }

                if (horizontalDrag) {
                    horizontalDrag.removeClass('jspActive');
                }
            }

            function _positionDragY2(destY, animate) {
                if (!isScrollableV) {
                    return;
                }

                if (destY < 0) {
                    destY = 0;
                } else if (destY > dragMaxY) {
                    destY = dragMaxY;
                } // allow for devs to prevent the JSP from being scrolled

                var willScrollYEvent = new $.Event("jsp-will-scroll-y");
                elem.trigger(willScrollYEvent, [destY]);

                if (willScrollYEvent.isDefaultPrevented()) {
                    return;
                }

                var tmpVerticalDragPosition = destY || 0;
                var isAtTop = tmpVerticalDragPosition === 0,
                    isAtBottom = tmpVerticalDragPosition == dragMaxY,
                    percentScrolled = destY / dragMaxY,
                    destTop = -percentScrolled * (contentHeight - paneHeight); // can't just check if(animate) because false is a valid value that could be passed in...

                if (animate === undefined) {
                    animate = settings.animateScroll;
                }

                if (animate) {
                    jsp.animate(verticalDrag, 'top', destY, _positionDragY, function () {
                        elem.trigger('jsp-user-scroll-y', [-destTop, isAtTop, isAtBottom]);
                    });
                } else {
                    verticalDrag.css('top', destY);

                    _positionDragY(destY);

                    elem.trigger('jsp-user-scroll-y', [-destTop, isAtTop, isAtBottom]);
                }
            }

            function _positionDragY(destY) {
                if (destY === undefined) {
                    destY = verticalDrag.position().top;
                }

                container.scrollTop(0);
                verticalDragPosition = destY || 0;
                var isAtTop = verticalDragPosition === 0,
                    isAtBottom = verticalDragPosition == dragMaxY,
                    percentScrolled = destY / dragMaxY,
                    destTop = -percentScrolled * (contentHeight - paneHeight);

                if (wasAtTop != isAtTop || wasAtBottom != isAtBottom) {
                    wasAtTop = isAtTop;
                    wasAtBottom = isAtBottom;
                    elem.trigger('jsp-arrow-change', [wasAtTop, wasAtBottom, wasAtLeft, wasAtRight]);
                }

                updateVerticalArrows(isAtTop, isAtBottom);
                pane.css('top', destTop);
                elem.trigger('jsp-scroll-y', [-destTop, isAtTop, isAtBottom]).trigger('scroll');
            }

            function _positionDragX2(destX, animate) {
                if (!isScrollableH) {
                    return;
                }

                if (destX < 0) {
                    destX = 0;
                } else if (destX > dragMaxX) {
                    destX = dragMaxX;
                } // allow for devs to prevent the JSP from being scrolled

                var willScrollXEvent = new $.Event("jsp-will-scroll-x");
                elem.trigger(willScrollXEvent, [destX]);

                if (willScrollXEvent.isDefaultPrevented()) {
                    return;
                }

                var tmpHorizontalDragPosition = destX || 0;
                var isAtLeft = tmpHorizontalDragPosition === 0,
                    isAtRight = tmpHorizontalDragPosition == dragMaxX,
                    percentScrolled = destX / dragMaxX,
                    destLeft = -percentScrolled * (contentWidth - paneWidth);

                if (animate === undefined) {
                    animate = settings.animateScroll;
                }

                if (animate) {
                    jsp.animate(horizontalDrag, 'left', destX, _positionDragX, function () {
                        elem.trigger('jsp-user-scroll-x', [-destLeft, isAtLeft, isAtRight]);
                    });
                } else {
                    horizontalDrag.css('left', destX);

                    _positionDragX(destX);

                    elem.trigger('jsp-user-scroll-x', [-destLeft, isAtLeft, isAtRight]);
                }
            }

            function _positionDragX(destX) {
                if (destX === undefined) {
                    destX = horizontalDrag.position().left;
                }

                container.scrollTop(0);
                horizontalDragPosition = destX || 0;
                var isAtLeft = horizontalDragPosition === 0,
                    isAtRight = horizontalDragPosition == dragMaxX,
                    percentScrolled = destX / dragMaxX,
                    destLeft = -percentScrolled * (contentWidth - paneWidth);

                if (wasAtLeft != isAtLeft || wasAtRight != isAtRight) {
                    wasAtLeft = isAtLeft;
                    wasAtRight = isAtRight;
                    elem.trigger('jsp-arrow-change', [wasAtTop, wasAtBottom, wasAtLeft, wasAtRight]);
                }

                updateHorizontalArrows(isAtLeft, isAtRight);
                pane.css('left', destLeft);
                elem.trigger('jsp-scroll-x', [-destLeft, isAtLeft, isAtRight]).trigger('scroll');
            }

            function updateVerticalArrows(isAtTop, isAtBottom) {
                if (settings.showArrows) {
                    arrowUp[isAtTop ? 'addClass' : 'removeClass']('jspDisabled');
                    arrowDown[isAtBottom ? 'addClass' : 'removeClass']('jspDisabled');
                }
            }

            function updateHorizontalArrows(isAtLeft, isAtRight) {
                if (settings.showArrows) {
                    arrowLeft[isAtLeft ? 'addClass' : 'removeClass']('jspDisabled');
                    arrowRight[isAtRight ? 'addClass' : 'removeClass']('jspDisabled');
                }
            }

            function _scrollToY(destY, animate) {
                var percentScrolled = destY / (contentHeight - paneHeight);

                _positionDragY2(percentScrolled * dragMaxY, animate);
            }

            function _scrollToX(destX, animate) {
                var percentScrolled = destX / (contentWidth - paneWidth);

                _positionDragX2(percentScrolled * dragMaxX, animate);
            }

            function _scrollToElement(ele, stickToTop, animate) {
                var e,
                    eleHeight,
                    eleWidth,
                    eleTop = 0,
                    eleLeft = 0,
                    viewportTop,
                    viewportLeft,
                    maxVisibleEleTop,
                    maxVisibleEleLeft,
                    destY,
                    destX; // Legal hash values aren't necessarily legal jQuery selectors so we need to catch any
                // errors from the lookup...

                try {
                    e = $(ele);
                } catch (err) {
                    return;
                }

                eleHeight = e.outerHeight();
                eleWidth = e.outerWidth();
                container.scrollTop(0);
                container.scrollLeft(0); // loop through parents adding the offset top of any elements that are relatively positioned between
                // the focused element and the jspPane so we can get the true distance from the top
                // of the focused element to the top of the scrollpane...

                while (!e.is('.jspPane')) {
                    eleTop += e.position().top;
                    eleLeft += e.position().left;
                    e = e.offsetParent();

                    if (/^body|html$/i.test(e[0].nodeName)) {
                        // we ended up too high in the document structure. Quit!
                        return;
                    }
                }

                viewportTop = contentPositionY();
                maxVisibleEleTop = viewportTop + paneHeight;

                if (eleTop < viewportTop || stickToTop) {
                    // element is above viewport
                    destY = eleTop - settings.horizontalGutter;
                } else if (eleTop + eleHeight > maxVisibleEleTop) {
                    // element is below viewport
                    destY = eleTop - paneHeight + eleHeight + settings.horizontalGutter;
                }

                if (!isNaN(destY)) {
                    _scrollToY(destY, animate);
                }

                viewportLeft = contentPositionX();
                maxVisibleEleLeft = viewportLeft + paneWidth;

                if (eleLeft < viewportLeft || stickToTop) {
                    // element is to the left of viewport
                    destX = eleLeft - settings.horizontalGutter;
                } else if (eleLeft + eleWidth > maxVisibleEleLeft) {
                    // element is to the right viewport
                    destX = eleLeft - paneWidth + eleWidth + settings.horizontalGutter;
                }

                if (!isNaN(destX)) {
                    _scrollToX(destX, animate);
                }
            }

            function contentPositionX() {
                return -pane.position().left;
            }

            function contentPositionY() {
                return -pane.position().top;
            }

            function isCloseToBottom() {
                var scrollableHeight = contentHeight - paneHeight;
                return scrollableHeight > 20 && scrollableHeight - contentPositionY() < 10;
            }

            function isCloseToRight() {
                var scrollableWidth = contentWidth - paneWidth;
                return scrollableWidth > 20 && scrollableWidth - contentPositionX() < 10;
            }

            function initMousewheel() {
                container.unbind(mwEvent).bind(mwEvent, function (event, delta, deltaX, deltaY) {
                    if (!horizontalDragPosition) horizontalDragPosition = 0;
                    if (!verticalDragPosition) verticalDragPosition = 0;
                    var dX = horizontalDragPosition,
                        dY = verticalDragPosition,
                        factor = event.deltaFactor || settings.mouseWheelSpeed;
                    jsp.scrollBy(deltaX * factor, -deltaY * factor, false); // return true if there was no movement so rest of screen can scroll

                    return dX == horizontalDragPosition && dY == verticalDragPosition;
                });
            }

            function removeMousewheel() {
                container.unbind(mwEvent);
            }

            function nil() {
                return false;
            }

            function initFocusHandler() {
                pane.find(':input,a').unbind('focus.jsp').bind('focus.jsp', function (e) {
                    _scrollToElement(e.target, false);
                });
            }

            function removeFocusHandler() {
                pane.find(':input,a').unbind('focus.jsp');
            }

            function initKeyboardNav() {
                var keyDown,
                    elementHasScrolled,
                    validParents = [];
                isScrollableH && validParents.push(horizontalBar[0]);
                isScrollableV && validParents.push(verticalBar[0]); // IE also focuses elements that don't have tabindex set.

                pane.bind('focus.jsp', function () {
                    elem.focus();
                });
                elem.attr('tabindex', 0).unbind('keydown.jsp keypress.jsp').bind('keydown.jsp', function (e) {
                    if (e.target !== this && !(validParents.length && $(e.target).closest(validParents).length)) {
                        return;
                    }

                    var dX = horizontalDragPosition,
                        dY = verticalDragPosition;

                    switch (e.keyCode) {
                        case 40: // down

                        case 38: // up

                        case 34: // page down

                        case 32: // space

                        case 33: // page up

                        case 39: // right

                        case 37:
                            // left
                            keyDown = e.keyCode;
                            keyDownHandler();
                            break;

                        case 35:
                            // end
                            _scrollToY(contentHeight - paneHeight);

                            keyDown = null;
                            break;

                        case 36:
                            // home
                            _scrollToY(0);

                            keyDown = null;
                            break;
                    }

                    elementHasScrolled = e.keyCode == keyDown && dX != horizontalDragPosition || dY != verticalDragPosition;
                    return !elementHasScrolled;
                }).bind('keypress.jsp', // For FF/ OSX so that we can cancel the repeat key presses if the JSP scrolls...
                    function (e) {
                        if (e.keyCode == keyDown) {
                            keyDownHandler();
                        } // If the keypress is not related to the area, ignore it. Fixes problem with inputs inside scrolled area. Copied from line 955.

                        if (e.target !== this && !(validParents.length && $(e.target).closest(validParents).length)) {
                            return;
                        }

                        return !elementHasScrolled;
                    });

                if (settings.hideFocus) {
                    elem.css('outline', 'none');

                    if ('hideFocus' in container[0]) {
                        elem.attr('hideFocus', true);
                    }
                } else {
                    elem.css('outline', '');

                    if ('hideFocus' in container[0]) {
                        elem.attr('hideFocus', false);
                    }
                }

                function keyDownHandler() {
                    var dX = horizontalDragPosition,
                        dY = verticalDragPosition;

                    switch (keyDown) {
                        case 40:
                            // down
                            jsp.scrollByY(settings.keyboardSpeed, false);
                            break;

                        case 38:
                            // up
                            jsp.scrollByY(-settings.keyboardSpeed, false);
                            break;

                        case 34: // page down

                        case 32:
                            // space
                            jsp.scrollByY(paneHeight * settings.scrollPagePercent, false);
                            break;

                        case 33:
                            // page up
                            jsp.scrollByY(-paneHeight * settings.scrollPagePercent, false);
                            break;

                        case 39:
                            // right
                            jsp.scrollByX(settings.keyboardSpeed, false);
                            break;

                        case 37:
                            // left
                            jsp.scrollByX(-settings.keyboardSpeed, false);
                            break;
                    }

                    elementHasScrolled = dX != horizontalDragPosition || dY != verticalDragPosition;
                    return elementHasScrolled;
                }
            }

            function removeKeyboardNav() {
                elem.attr('tabindex', '-1').removeAttr('tabindex').unbind('keydown.jsp keypress.jsp');
                pane.unbind('.jsp');
            }

            function observeHash() {
                if (location.hash && location.hash.length > 1) {
                    var e,
                        retryInt,
                        hash = escape(location.hash.substr(1)) // hash must be escaped to prevent XSS
                        ;

                    try {
                        e = $('#' + hash + ', a[name="' + hash + '"]');
                    } catch (err) {
                        return;
                    }

                    if (e.length && pane.find(hash)) {
                        // nasty workaround but it appears to take a little while before the hash has done its thing
                        // to the rendered page so we just wait until the container's scrollTop has been messed up.
                        if (container.scrollTop() === 0) {
                            retryInt = setInterval(function () {
                                if (container.scrollTop() > 0) {
                                    _scrollToElement(e, true);

                                    $(document).scrollTop(container.position().top);
                                    clearInterval(retryInt);
                                }
                            }, 50);
                        } else {
                            _scrollToElement(e, true);

                            $(document).scrollTop(container.position().top);
                        }
                    }
                }
            }

            function hijackInternalLinks() {
                // only register the link handler once
                if ($(document.body).data('jspHijack')) {
                    return;
                } // remember that the handler was bound

                $(document.body).data('jspHijack', true); // use live handler to also capture newly created links

                $(document.body).delegate('a[href*="#"]', 'click', function (event) {
                    // does the link point to the same page?
                    // this also takes care of cases with a <base>-Tag or Links not starting with the hash #
                    // e.g. <a href="index.html#test"> when the current url already is index.html
                    var href = this.href.substr(0, this.href.indexOf('#')),
                        locationHref = location.href,
                        hash,
                        element,
                        container,
                        jsp,
                        scrollTop,
                        elementTop;

                    if (location.href.indexOf('#') !== -1) {
                        locationHref = location.href.substr(0, location.href.indexOf('#'));
                    }

                    if (href !== locationHref) {
                        // the link points to another page
                        return;
                    } // check if jScrollPane should handle this click event

                    hash = escape(this.href.substr(this.href.indexOf('#') + 1)); // find the element on the page

                    element;

                    try {
                        element = $('#' + hash + ', a[name="' + hash + '"]');
                    } catch (e) {
                        // hash is not a valid jQuery identifier
                        return;
                    }

                    if (!element.length) {
                        // this link does not point to an element on this page
                        return;
                    }

                    container = element.closest('.jspScrollable');
                    jsp = container.data('jsp'); // jsp might be another jsp instance than the one, that bound this event
                    // remember: this event is only bound once for all instances.

                    jsp.scrollToElement(element, true);

                    if (container[0].scrollIntoView) {
                        // also scroll to the top of the container (if it is not visible)
                        scrollTop = $(window).scrollTop();
                        elementTop = element.offset().top;

                        if (elementTop < scrollTop || elementTop > scrollTop + $(window).height()) {
                            container[0].scrollIntoView();
                        }
                    } // jsp handled this event, prevent the browser default (scrolling :P)

                    event.preventDefault();
                });
            } // Init touch on iPad, iPhone, iPod, Android

            function initTouch() {
                var startX,
                    startY,
                    touchStartX,
                    touchStartY,
                    moved,
                    moving = false;
                container.unbind('touchstart.jsp touchmove.jsp touchend.jsp click.jsp-touchclick').bind('touchstart.jsp', function (e) {
                    var touch = e.originalEvent.touches[0];
                    startX = contentPositionX();
                    startY = contentPositionY();
                    touchStartX = touch.pageX;
                    touchStartY = touch.pageY;
                    moved = false;
                    moving = true;
                }).bind('touchmove.jsp', function (ev) {
                    if (!moving) {
                        return;
                    }

                    var touchPos = ev.originalEvent.touches[0],
                        dX = horizontalDragPosition,
                        dY = verticalDragPosition;
                    jsp.scrollTo(startX + touchStartX - touchPos.pageX, startY + touchStartY - touchPos.pageY);
                    moved = moved || Math.abs(touchStartX - touchPos.pageX) > 5 || Math.abs(touchStartY - touchPos.pageY) > 5; // return true if there was no movement so rest of screen can scroll

                    return dX == horizontalDragPosition && dY == verticalDragPosition;
                }).bind('touchend.jsp', function (e) {
                    moving = false;
                    /*if(moved) {
                      return false;
                    }*/
                }).bind('click.jsp-touchclick', function (e) {
                    if (moved) {
                        moved = false;
                        return false;
                    }
                });
            }

            function _destroy() {
                var currentY = contentPositionY(),
                    currentX = contentPositionX();
                elem.removeClass('jspScrollable').unbind('.jsp');
                pane.unbind('.jsp');
                elem.replaceWith(originalElement.append(pane.children()));
                originalElement.scrollTop(currentY);
                originalElement.scrollLeft(currentX); // clear reinitialize timer if active

                if (reinitialiseInterval) {
                    clearInterval(reinitialiseInterval);
                }
            } // Public API

            $.extend(jsp, {
                // Reinitialises the scroll pane (if it's internal dimensions have changed since the last time it
                // was initialised). The settings object which is passed in will override any settings from the
                // previous time it was initialised - if you don't pass any settings then the ones from the previous
                // initialisation will be used.
                reinitialise: function reinitialise(s) {
                    s = $.extend({}, settings, s);
                    initialise(s);
                },
                // Scrolls the specified element (a jQuery object, DOM node or jQuery selector string) into view so
                // that it can be seen within the viewport. If stickToTop is true then the element will appear at
                // the top of the viewport, if it is false then the viewport will scroll as little as possible to
                // show the element. You can also specify if you want animation to occur. If you don't provide this
                // argument then the animateScroll value from the settings object is used instead.
                scrollToElement: function scrollToElement(ele, stickToTop, animate) {
                    _scrollToElement(ele, stickToTop, animate);
                },
                // Scrolls the pane so that the specified co-ordinates within the content are at the top left
                // of the viewport. animate is optional and if not passed then the value of animateScroll from
                // the settings object this jScrollPane was initialised with is used.
                scrollTo: function scrollTo(destX, destY, animate) {
                    _scrollToX(destX, animate);

                    _scrollToY(destY, animate);
                },
                // Scrolls the pane so that the specified co-ordinate within the content is at the left of the
                // viewport. animate is optional and if not passed then the value of animateScroll from the settings
                // object this jScrollPane was initialised with is used.
                scrollToX: function scrollToX(destX, animate) {
                    _scrollToX(destX, animate);
                },
                // Scrolls the pane so that the specified co-ordinate within the content is at the top of the
                // viewport. animate is optional and if not passed then the value of animateScroll from the settings
                // object this jScrollPane was initialised with is used.
                scrollToY: function scrollToY(destY, animate) {
                    _scrollToY(destY, animate);
                },
                // Scrolls the pane to the specified percentage of its maximum horizontal scroll position. animate
                // is optional and if not passed then the value of animateScroll from the settings object this
                // jScrollPane was initialised with is used.
                scrollToPercentX: function scrollToPercentX(destPercentX, animate) {
                    _scrollToX(destPercentX * (contentWidth - paneWidth), animate);
                },
                // Scrolls the pane to the specified percentage of its maximum vertical scroll position. animate
                // is optional and if not passed then the value of animateScroll from the settings object this
                // jScrollPane was initialised with is used.
                scrollToPercentY: function scrollToPercentY(destPercentY, animate) {
                    _scrollToY(destPercentY * (contentHeight - paneHeight), animate);
                },
                // Scrolls the pane by the specified amount of pixels. animate is optional and if not passed then
                // the value of animateScroll from the settings object this jScrollPane was initialised with is used.
                scrollBy: function scrollBy(deltaX, deltaY, animate) {
                    jsp.scrollByX(deltaX, animate);
                    jsp.scrollByY(deltaY, animate);
                },
                // Scrolls the pane by the specified amount of pixels. animate is optional and if not passed then
                // the value of animateScroll from the settings object this jScrollPane was initialised with is used.
                scrollByX: function scrollByX(deltaX, animate) {
                    var destX = contentPositionX() + Math[deltaX < 0 ? 'floor' : 'ceil'](deltaX),
                        percentScrolled = destX / (contentWidth - paneWidth);

                    _positionDragX2(percentScrolled * dragMaxX, animate);
                },
                // Scrolls the pane by the specified amount of pixels. animate is optional and if not passed then
                // the value of animateScroll from the settings object this jScrollPane was initialised with is used.
                scrollByY: function scrollByY(deltaY, animate) {
                    var destY = contentPositionY() + Math[deltaY < 0 ? 'floor' : 'ceil'](deltaY),
                        percentScrolled = destY / (contentHeight - paneHeight);

                    _positionDragY2(percentScrolled * dragMaxY, animate);
                },
                // Positions the horizontal drag at the specified x position (and updates the viewport to reflect
                // this). animate is optional and if not passed then the value of animateScroll from the settings
                // object this jScrollPane was initialised with is used.
                positionDragX: function positionDragX(x, animate) {
                    _positionDragX2(x, animate);
                },
                // Positions the vertical drag at the specified y position (and updates the viewport to reflect
                // this). animate is optional and if not passed then the value of animateScroll from the settings
                // object this jScrollPane was initialised with is used.
                positionDragY: function positionDragY(y, animate) {
                    _positionDragY2(y, animate);
                },
                // This method is called when jScrollPane is trying to animate to a new position. You can override
                // it if you want to provide advanced animation functionality. It is passed the following arguments:
                //  * ele          - the element whose position is being animated
                //  * prop         - the property that is being animated
                //  * value        - the value it's being animated to
                //  * stepCallback - a function that you must execute each time you update the value of the property
                //  * completeCallback - a function that will be executed after the animation had finished
                // You can use the default implementation (below) as a starting point for your own implementation.
                animate: function animate(ele, prop, value, stepCallback, completeCallback) {
                    var params = {};
                    params[prop] = value;
                    ele.animate(params, {
                        'duration': settings.animateDuration,
                        'easing': settings.animateEase,
                        'queue': false,
                        'step': stepCallback,
                        'complete': completeCallback
                    });
                },
                // Returns the current x position of the viewport with regards to the content pane.
                getContentPositionX: function getContentPositionX() {
                    return contentPositionX();
                },
                // Returns the current y position of the viewport with regards to the content pane.
                getContentPositionY: function getContentPositionY() {
                    return contentPositionY();
                },
                // Returns the width of the content within the scroll pane.
                getContentWidth: function getContentWidth() {
                    return contentWidth;
                },
                // Returns the height of the content within the scroll pane.
                getContentHeight: function getContentHeight() {
                    return contentHeight;
                },
                // Returns the horizontal position of the viewport within the pane content.
                getPercentScrolledX: function getPercentScrolledX() {
                    return contentPositionX() / (contentWidth - paneWidth);
                },
                // Returns the vertical position of the viewport within the pane content.
                getPercentScrolledY: function getPercentScrolledY() {
                    return contentPositionY() / (contentHeight - paneHeight);
                },
                // Returns whether or not this scrollpane has a horizontal scrollbar.
                getIsScrollableH: function getIsScrollableH() {
                    return isScrollableH;
                },
                // Returns whether or not this scrollpane has a vertical scrollbar.
                getIsScrollableV: function getIsScrollableV() {
                    return isScrollableV;
                },
                // Gets a reference to the content pane. It is important that you use this method if you want to
                // edit the content of your jScrollPane as if you access the element directly then you may have some
                // problems (as your original element has had additional elements for the scrollbars etc added into
                // it).
                getContentPane: function getContentPane() {
                    return pane;
                },
                // Scrolls this jScrollPane down as far as it can currently scroll. If animate isn't passed then the
                // animateScroll value from settings is used instead.
                scrollToBottom: function scrollToBottom(animate) {
                    _positionDragY2(dragMaxY, animate);
                },
                // Hijacks the links on the page which link to content inside the scrollpane. If you have changed
                // the content of your page (e.g. via AJAX) and want to make sure any new anchor links to the
                // contents of your scroll pane will work then call this function.
                hijackInternalLinks: $.noop,
                // Removes the jScrollPane and returns the page to the state it was in before jScrollPane was
                // initialised.
                destroy: function destroy() {
                    _destroy();
                }
            });
            initialise(s);
        } // Pluginifying code...

        settings = $.extend({}, $.fn.jScrollPane.defaults, settings); // Apply default speed

        $.each(['arrowButtonSpeed', 'trackClickSpeed', 'keyboardSpeed'], function () {
            settings[this] = settings[this] || settings.speed;
        });
        return this.each(function () {
            var elem = $(this),
                jspApi = elem.data('jsp');

            if (jspApi) {
                jspApi.reinitialise(settings);
            } else {
                $("script", elem).filter('[type="text/javascript"],:not([type])').remove();
                jspApi = new JScrollPane(elem, settings);
                elem.data('jsp', jspApi);
            }
        });
    };

    $.fn.jScrollPane.defaults = {
        showArrows: true,
        maintainPosition: true,
        stickToBottom: false,
        stickToRight: false,
        clickOnTrack: true,
        autoReinitialise: false,
        autoReinitialiseDelay: 500,
        verticalDragMinHeight: 0,
        verticalDragMaxHeight: 99999,
        horizontalDragMinWidth: 0,
        horizontalDragMaxWidth: 99999,
        contentWidth: undefined,
        animateScroll: false,
        animateDuration: 300,
        animateEase: 'linear',
        hijackInternalLinks: false,
        verticalGutter: 4,
        horizontalGutter: 4,
        mouseWheelSpeed: 3,
        arrowButtonSpeed: 0,
        arrowRepeatFreq: 50,
        arrowScrollOnHover: false,
        trackClickSpeed: 0,
        trackClickRepeatFreq: 70,
        verticalArrowPositions: 'split',
        horizontalArrowPositions: 'split',
        enableKeyboardNavigation: true,
        hideFocus: false,
        keyboardSpeed: 0,
        initialDelay: 300,
        // Delay before starting repeating
        speed: 30,
        // Default speed when others falsey
        scrollPagePercent: .8 // Percent of visible area scrolled when pageUp/Down or track area pressed

    };
});
/*!
 * jQuery Mousewheel 3.1.12
 *
 * Copyright 2014 jQuery Foundation and other contributors
 * Released under the MIT license.
 * http://jquery.org/license
 */

(function (factory) {
    if (typeof define === 'function' && define.amd) {
        // AMD. Register as an anonymous module.
        define(['jquery'], factory);
    } else if ((typeof exports === "undefined" ? "undefined" : _typeof(exports)) === 'object') {
        // Node/CommonJS style for Browserify
        module.exports = factory;
    } else {
        // Browser globals
        factory(jQuery);
    }
})(function ($) {
    var toFix = ['wheel', 'mousewheel', 'DOMMouseScroll', 'MozMousePixelScroll'],
        toBind = 'onwheel' in document || document.documentMode >= 9 ? ['wheel'] : ['mousewheel', 'DomMouseScroll', 'MozMousePixelScroll'],
        slice = Array.prototype.slice,
        nullLowestDeltaTimeout,
        lowestDelta;

    if ($.event.fixHooks) {
        for (var i = toFix.length; i;) {
            $.event.fixHooks[toFix[--i]] = $.event.mouseHooks;
        }
    }

    var special = $.event.special.mousewheel = {
        version: '3.1.12',
        setup: function setup() {
            if (this.addEventListener) {
                for (var i = toBind.length; i;) {
                    this.addEventListener(toBind[--i], handler, false);
                }
            } else {
                this.onmousewheel = handler;
            } // Store the line height and page height for this particular element

            $.data(this, 'mousewheel-line-height', special.getLineHeight(this));
            $.data(this, 'mousewheel-page-height', special.getPageHeight(this));
        },
        teardown: function teardown() {
            if (this.removeEventListener) {
                for (var i = toBind.length; i;) {
                    this.removeEventListener(toBind[--i], handler, false);
                }
            } else {
                this.onmousewheel = null;
            } // Clean up the data we added to the element

            $.removeData(this, 'mousewheel-line-height');
            $.removeData(this, 'mousewheel-page-height');
        },
        getLineHeight: function getLineHeight(elem) {
            var $elem = $(elem),
                $parent = $elem['offsetParent' in $.fn ? 'offsetParent' : 'parent']();

            if (!$parent.length) {
                $parent = $('body');
            }

            return parseInt($parent.css('fontSize'), 10) || parseInt($elem.css('fontSize'), 10) || 16;
        },
        getPageHeight: function getPageHeight(elem) {
            return $(elem).height();
        },
        settings: {
            adjustOldDeltas: true,
            // see shouldAdjustOldDeltas() below
            normalizeOffset: true // calls getBoundingClientRect for each event

        }
    };
    $.fn.extend({
        mousewheel: function mousewheel(fn) {
            return fn ? this.bind('mousewheel', fn) : this.trigger('mousewheel');
        },
        unmousewheel: function unmousewheel(fn) {
            return this.unbind('mousewheel', fn);
        }
    });

    function handler(event) {
        var orgEvent = event || window.event,
            args = slice.call(arguments, 1),
            delta = 0,
            deltaX = 0,
            deltaY = 0,
            absDelta = 0,
            offsetX = 0,
            offsetY = 0;
        event = $.event.fix(orgEvent);
        event.type = 'mousewheel'; // Old school scrollwheel delta

        if ('detail' in orgEvent) {
            deltaY = orgEvent.detail * -1;
        }

        if ('wheelDelta' in orgEvent) {
            deltaY = orgEvent.wheelDelta;
        }

        if ('wheelDeltaY' in orgEvent) {
            deltaY = orgEvent.wheelDeltaY;
        }

        if ('wheelDeltaX' in orgEvent) {
            deltaX = orgEvent.wheelDeltaX * -1;
        } // Firefox < 17 horizontal scrolling related to DOMMouseScroll event

        if ('axis' in orgEvent && orgEvent.axis === orgEvent.HORIZONTAL_AXIS) {
            deltaX = deltaY * -1;
            deltaY = 0;
        } // Set delta to be deltaY or deltaX if deltaY is 0 for backwards compatabilitiy

        delta = deltaY === 0 ? deltaX : deltaY; // New school wheel delta (wheel event)

        if ('deltaY' in orgEvent) {
            deltaY = orgEvent.deltaY * -1;
            delta = deltaY;
        }

        if ('deltaX' in orgEvent) {
            deltaX = orgEvent.deltaX;

            if (deltaY === 0) {
                delta = deltaX * -1;
            }
        } // No change actually happened, no reason to go any further

        if (deltaY === 0 && deltaX === 0) {
            return;
        } // Need to convert lines and pages to pixels if we aren't already in pixels
        // There are three delta modes:
        //   * deltaMode 0 is by pixels, nothing to do
        //   * deltaMode 1 is by lines
        //   * deltaMode 2 is by pages

        if (orgEvent.deltaMode === 1) {
            var lineHeight = $.data(this, 'mousewheel-line-height');
            delta *= lineHeight;
            deltaY *= lineHeight;
            deltaX *= lineHeight;
        } else if (orgEvent.deltaMode === 2) {
            var pageHeight = $.data(this, 'mousewheel-page-height');
            delta *= pageHeight;
            deltaY *= pageHeight;
            deltaX *= pageHeight;
        } // Store lowest absolute delta to normalize the delta values

        absDelta = Math.max(Math.abs(deltaY), Math.abs(deltaX));

        if (!lowestDelta || absDelta < lowestDelta) {
            lowestDelta = absDelta; // Adjust older deltas if necessary

            if (shouldAdjustOldDeltas(orgEvent, absDelta)) {
                lowestDelta /= 40;
            }
        } // Adjust older deltas if necessary

        if (shouldAdjustOldDeltas(orgEvent, absDelta)) {
            // Divide all the things by 40!
            delta /= 40;
            deltaX /= 40;
            deltaY /= 40;
        } // Get a whole, normalized value for the deltas

        delta = Math[delta >= 1 ? 'floor' : 'ceil'](delta / lowestDelta);
        deltaX = Math[deltaX >= 1 ? 'floor' : 'ceil'](deltaX / lowestDelta);
        deltaY = Math[deltaY >= 1 ? 'floor' : 'ceil'](deltaY / lowestDelta); // Normalise offsetX and offsetY properties

        if (special.settings.normalizeOffset && this.getBoundingClientRect) {
            var boundingRect = this.getBoundingClientRect();
            offsetX = event.clientX - boundingRect.left;
            offsetY = event.clientY - boundingRect.top;
        } // Add information to the event object

        event.deltaX = deltaX;
        event.deltaY = deltaY;
        event.deltaFactor = lowestDelta;
        event.offsetX = offsetX;
        event.offsetY = offsetY; // Go ahead and set deltaMode to 0 since we converted to pixels
        // Although this is a little odd since we overwrite the deltaX/Y
        // properties with normalized deltas.

        event.deltaMode = 0; // Add event and delta to the front of the arguments

        args.unshift(event, delta, deltaX, deltaY); // Clearout lowestDelta after sometime to better
        // handle multiple device types that give different
        // a different lowestDelta
        // Ex: trackpad = 3 and mouse wheel = 120

        if (nullLowestDeltaTimeout) {
            clearTimeout(nullLowestDeltaTimeout);
        }

        nullLowestDeltaTimeout = setTimeout(nullLowestDelta, 200);
        return ($.event.dispatch || $.event.handle).apply(this, args);
    }

    function nullLowestDelta() {
        lowestDelta = null;
    }

    function shouldAdjustOldDeltas(orgEvent, absDelta) {
        // If this is an older event and the delta is divisable by 120,
        // then we are assuming that the browser is treating this as an
        // older mouse wheel event and that we should divide the deltas
        // by 40 to try and get a more usable deltaFactor.
        // Side note, this actually impacts the reported scroll distance
        // in older browsers and can cause scrolling to be slower than native.
        // Turn this off by setting $.event.special.mousewheel.settings.adjustOldDeltas to false.
        return special.settings.adjustOldDeltas && orgEvent.type === 'mousewheel' && absDelta % 120 === 0;
    }
});
