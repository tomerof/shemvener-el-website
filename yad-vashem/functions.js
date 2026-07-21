
function inArray(needle, haystack) {
    var length = haystack.length;
    for (var i = 0; i < length; i++) {
        if (haystack[i] == needle) return true;
    }
    return false;
}

function langValidation() {
    if ($('body').hasClass('ltr')) {
        var enLangPopup = function enLangPopup() {
            $('#main').addClass('lang-validation');
            setTimeout(function () {
                $('#main').removeClass('lang-validation');
            }, 1000);
        };

        $("input").keydown(function (event) {
            if (event.key >= 'א' & event.key <= 'פֿ') {
                enLangPopup();
                return false;
            }
        });
    }

    if (is_rtl()) {
        var _enLangPopup = function _enLangPopup() {
            $('#main').addClass('lang-validation');
            setTimeout(function () {
                $('#main').removeClass('lang-validation');
            }, 1000);
        };

        $('input:not([name="more_info"]):not([name="uploader_email"]):not([name="input_5"]):not([name="input_2"]):not([name="donation_fullname"]):not([type="email"]):not([name="phone"])').keydown(function (event) {
            if (event.key == 'a' || event.key == 'A') {
                _enLangPopup();

                return false;
            }

            if (event.key == 'b' || event.key == 'B') {
                _enLangPopup();

                return false;
            }

            if (event.key == 'c' || event.key == 'C') {
                _enLangPopup();

                return false;
            }

            if (event.key == 'd' || event.key == 'D') {
                _enLangPopup();

                return false;
            }

            if (event.key == 'e' || event.key == 'E') {
                _enLangPopup();

                return false;
            }

            if (event.key == 'f' || event.key == 'F') {
                _enLangPopup();

                return false;
            }

            if (event.key == 'g' || event.key == 'G') {
                _enLangPopup();

                return false;
            }

            if (event.key == 'h' || event.key == 'H') {
                _enLangPopup();

                return false;
            }

            if (event.key == 'i' || event.key == 'I') {
                _enLangPopup();

                return false;
            }

            if (event.key == 'j' || event.key == 'J') {
                _enLangPopup();

                return false;
            }

            if (event.key == 'k' || event.key == 'K') {
                _enLangPopup();

                return false;
            }

            if (event.key == 'l' || event.key == 'L') {
                _enLangPopup();

                return false;
            }

            if (event.key == 'm' || event.key == 'M') {
                _enLangPopup();

                return false;
            }

            if (event.key == 'n' || event.key == 'N') {
                _enLangPopup();

                return false;
            }

            if (event.key == 'o' || event.key == 'O') {
                _enLangPopup();

                return false;
            }

            if (event.key == 'p' || event.key == 'P') {
                _enLangPopup();

                return false;
            }

            if (event.key == 'q' || event.key == 'Q') {
                _enLangPopup();

                return false;
            }

            if (event.key == 'r' || event.key == 'R') {
                _enLangPopup();

                return false;
            }

            if (event.key == 's' || event.key == 'S') {
                _enLangPopup();

                return false;
            }

            if (event.key == 't' || event.key == 'T') {
                _enLangPopup();

                return false;
            }

            if (event.key == 'u' || event.key == 'U') {
                _enLangPopup();

                return false;
            }

            if (event.key == 'v' || event.key == 'V') {
                _enLangPopup();

                return false;
            }

            if (event.key == 'w' || event.key == 'W') {
                _enLangPopup();

                return false;
            }

            if (event.key == 'x' || event.key == 'X') {
                _enLangPopup();

                return false;
            }

            if (event.key == 'y' || event.key == 'Y') {
                _enLangPopup();

                return false;
            }

            if (event.key == 'z' || event.key == 'Z') {
                _enLangPopup();

                return false;
            }
        });
    }
}

function toggleMenu() {
    if (isOpen) {
        $('body').removeClass('show-menu');
    } else {
        $('body').addClass('show-menu');
    }

    isOpen = !isOpen;
}

function handleMiniCart() {
    jQuery(document).on('click', '.js-toggle-mini-cart', function (e) {
        e.preventDefault();
        toggleMinicart();
    });
}

function touchStart() {
    if( $('.menu-item-has-children').length && $('.mobile-menu li').length) {
        $('.menu-item-has-children').append('<i></i>');
        $('.mobile-menu li').each(function (i) {
            if ($(this).hasClass("current-menu-ancestor") == true) {
                $(this).addClass('open_menu');
            }
        });
    }
}

function sectionHeader() { }

function sectionBanner() { }

function sectionPreCreateLabel() {
    var sections = $('.section-pre-create-label');
    sections.each(function (index, el) {
        var section = $(el);
        var preForm = section.find('.pre-inputs');
        var preFormInputs = preForm.find('input');
        var href = window.location.href;

        var items = section.find('[data-match]');
        $(items).matchHeight();
        var $button = section.find('button');

        $button.on('click', function (event) {
            if ($('input[name="firstname"]').val() != '' && $('input[name="lastname"]').val() != '') {
                $('.exist-deceased-popup').hide();
            }
        });
        section.on('click', '.open-video', function (event) {
            event.preventDefault();
            /* Act on the event */

            var movieUrl = $(this).attr('href');
            $.magnificPopup.open({
                items: {
                    src: movieUrl
                },
                type: 'iframe'
            });
        });

        section.on('keyup', 'input', function (event) {
            /* Act on the event */
            var value = $(this).val();
            var target = $(this).attr('data-target');
            $('[name=' + target + ']').val(value).trigger('keyup');
        }); //exsist person

        var blockedOnce = false;

        $('.check-validation').on('click', function (event) {
            $container = $('.section-create-label-form');

            if (getUrlParameter('nosearch') && !blockedOnce) {
                blockedOnce = true;
                $('.section-create-label-form').removeClass('cant-edit');
                return false;
            }

            if ($("input[name='firstname']").val() != '' && $("input[name='lastname']").val() != '') {
                appendBlockLoader($container, true);
                var string = $("input[name='firstname']").val() + "," + $("input[name='lastname']").val();

                var data = {
                    action: 'get_plugin_search',
                    lang: info.lang,
                    string: string
                };

                $.ajax({
                    url: info.ajaxUrl,
                    type: 'POST',
                    data: data,
                    dataType: 'json',
                    success: function success(data) {
                        if (data != null && data.results.length > 0) {
                            var existDeceasedPopup = $('.exist-deceased-popup');
                            var existDeceasedPopupContent = existDeceasedPopup.find('.content');
                            existDeceasedPopup.show();
                            $('.top-menu').addClass('active-popup');
                            existDeceasedPopupContent.html('');
                            existDeceasedPopup.find('.content-wrapper').addClass('min-height');
                            setTimeout(() => {
                                existDeceasedPopupContent.jScrollPane();
                            }, 500);
                            $.each(data.results, function (index, value) {
                                var bornString = '';

                                if (is_rtl()) {
                                    if (value.birth_country) {
                                        var bornString = "נולד/ה ב" + value.birth_country + ', ';
                                    }
                                }

                                if ($('body').hasClass('ltr')) {
                                    if (value.birth_country) {
                                        var bornString = "Born In" + value.birth_country + ', ';
                                    }
                                }

                                var occupationString = '';
                                if (value.occupation) {
                                    var occupationString = value.occupation + ', ';
                                }

                                if (is_rtl()) {
                                    var sonOfString = '';

                                    if (value.gender == 'male' && (value.father_name || value.mother_name)) {
                                        if (value.father_name || value.mother_name) {
                                            sonOfString = 'בן ' + value.father_name + ', ' + value.mother_name + ', ';
                                        }
                                    }

                                    if (value.gender == 'female' && (value.father_name || value.mother_name)) {
                                        if (value.father_name || value.mother_name) {
                                            sonOfString = 'בת ' + value.father_name + ', ' + value.mother_name + ', ';
                                        }
                                    }
                                }

                                if ($('body').hasClass('ltr')) {
                                    var sonOfString = '';

                                    if (value.gender == 'male' && (value.father_name || value.mother_name)) {
                                        if (value.father_name || value.mother_name) {
                                            sonOfString = 'Son of ' + value.father_name + ', ' + value.mother_name + ', ';
                                        }
                                    }

                                    if (value.gender == 'female' && (value.father_name || value.mother_name)) {
                                        if (value.father_name || value.mother_name) {
                                            sonOfString = 'Daughter of ' + value.father_name + ', ' + value.mother_name + ', ';
                                        }
                                    }
                                }

                                if (is_rtl()) {
                                    var husbandWife = '';

                                    if (value.gender == 'male' && value.partner_name) {
                                        if (value.partner_name) {
                                            husbandWife = 'נשוי ל' + value.partner_name + ', ';
                                        }
                                    }

                                    if (value.gender == 'female' && value.partner_name) {
                                        if (value.partner_name) {
                                            husbandWife = 'אשת ' + value.partner_name + ', ';
                                        }
                                    }
                                }

                                if ($('body').hasClass('ltr')) {
                                    var husbandWife = '';

                                    if (value.gender == 'male' && value.partner_name) {
                                        if (value.partner_name) {
                                            husbandWife = 'Married to ' + value.partner_name + ', ';
                                        }
                                    }
                                }

                                var familyString = '';

                                if (husbandWife.length < 1 && sonOfString.length < 1) {
                                    familyString = value.family + ', ';
                                }

                                var diedIn = '';
                                diedIn = value.place_of_death;
                                /*
                                if (is_rtl()) {
                                    if (value.gender == 'male' && value.place_of_death != '') {
                                        if (value.place_of_death.indexOf('נספה ב') == 0) {
                                            value.place_of_death = value.place_of_death.replace('נספה ב', '');
                                        }

                                        diedIn = 'נספה ב' + value.place_of_death;
                                    }

                                    if (value.gender == 'female' && value.place_of_death != '') {
                                        if (value.place_of_death.indexOf('נספתה ב') == 0) {
                                            value.place_of_death = value.place_of_death.replace('נספתה ב', '');
                                        }

                                        diedIn = 'נספתה ב' + value.place_of_death;
                                    }
                                }

                                if ($('body').hasClass('ltr')) {
                                    if (value.gender == 'male' && value.place_of_death != '') {
                                        if (value.place_of_death.indexOf('Perished in') == 0) {
                                            value.place_of_death = value.place_of_death.replace('Perished in', '');
                                        }

                                        diedIn = 'Perished in ' + value.place_of_death;
                                    }
                                }*/

                                var chooseButton = '';

                                if (is_rtl()) {
                                    chooseButton = 'בחר';
                                }

                                if ($('body').hasClass('ltr')) {
                                    chooseButton = 'Choose';
                                }

                                var printString = '';

                                if (is_rtl()) {
                                    printString = 'הדפסת תווית';
                                }

                                if ($('body').hasClass('ltr')) {
                                    printString = 'Print Label';
                                }

                                var moreInfoString = '';

                                if (is_rtl()) {
                                    moreInfoString = 'למידע נוסף על ' + value.first_name;
                                }

                                if ($('body').hasClass('ltr')) {
                                    moreInfoString = 'More About ' + value.first_name;
                                }

                                if (is_rtl()) {
                                    var hommoreInfoStringeUrl = window.location.origin + '/?person_id=';
                                }

                                if ($('body').hasClass('ltr')) {
                                    var homeUrl = window.location.origin + '/en/?person_id=';
                                }

                                let label_info_page_url = info.label_info_page.url + '?person_id=' + value.ID + '&details=1';

                                if (value.more_info.length > 0) {
                                    var more_info_string = '<a class="person-more-info" href="' + value.more_info + '" target="_blank"><span class="icon-link"></span><span class="info-word">' + moreInfoString + '</span> </a>';
                                } else {
                                    more_info_string = '';
                                }

                                var countrycity ='';
                                var fsuff = '';
                                if(value.gender == 'female'){
                                    fsuff = '_female';
                                }
                                if (value.birth_country && value.born_city) {
                                    countrycity = sticker.summary['city_country_sum'+fsuff];
                                } else if (value.born_city) {
                                    countrycity = sticker.summary['city_sum'+fsuff];
                                } else if (value.birth_country) {
                                    countrycity = sticker.summary['country_sum'+fsuff];
                                }
                                if(countrycity != '' && (value.born_city != '' || value.birth_country != '')){
                                    countrycity = countrycity.replace('%%city%%',value.born_city);
                                    countrycity = countrycity.replace('%%country%%',value.birth_country)+' ';
                                }
                                var occupation_text = value.occupation;
                                if(occupation_text != ''){
                                    occupation_summary = sticker.summary.occupation_sum;
                                    occupation_text = occupation_summary.replace('%%occupation%%',occupation_text)+' ';
                                }
                                var pod = value.place_of_death;
                                if(pod != ''){
                                    //pod = pod.replace('נספה ב', '');
                                    //pod = pod.replace('נספתה ב', '');
                                    //pod = pod.replace('Perished in', '');
                                    pod_summary = sticker.summary['pod_sum'+fsuff];
                                    pod = pod_summary.replace('%%place_of_death%%',pod);
                                }


                                if(value.ID >= 262194){
                                    var yob = value.year_of_birth;
                                    var yod = value.year_of_death;
                                    var age = 0;
                                    if(yod && yob){
                                        var age = yod-yob;
                                    }
                                    var familystring = build_fstring_dyn('summary',age,value.gender,value.father_name,value.mother_name,value.partner_name,value.personal_status,value.number_of_kids,value.notice);
                                    familystring = familystring.replace('##lb##','</p><p>');
                                    var singleDeceased = '<div class="single-deceased"><div class="deceased-info"><div class="full-name">' + value.first_name + ' ' + value.last_name + '</div><div class="dates"><span class="date">' + value.year_of_birth + '</span> - <span class="date">' + value.year_of_death + '</span></div><div class="info-line">' + countrycity + '' + familystring + '' + occupation_text + '' + pod + '</div></div><div class="deceased_buttons"><a class="choose-person" href="' + label_info_page_url + '">' + chooseButton + '</a>  <a class="print-person" href="' + label_info_page_url + '&hide-form=1"><span class="icon-printer-text"></span><span class="print-word">' + printString + '</span> </a>  ' + more_info_string + '</div></div>';

                                }else{
                                    var singleDeceased = '<div class="single-deceased"><div class="deceased-info"><div class="full-name">' + value.first_name + ' ' + value.last_name + '</div><div class="dates"><span class="date">' + value.year_of_birth + '</span> - <span class="date">' + value.year_of_death + '</span></div><div class="info-line">' + countrycity + '' + sonOfString + '' + husbandWife + '' + familyString + '' + occupation_text + '' + pod + '</div></div><div class="deceased_buttons"><a class="choose-person" href="' + label_info_page_url + '">' + chooseButton + '</a>  <a class="print-person" href="' + label_info_page_url + '&hide-form=1"><span class="icon-printer-text"></span><span class="print-word">' + printString + '</span> </a>  ' + more_info_string + '</div></div>';
                                }

                                existDeceasedPopupContent.append(singleDeceased);
                            });
                        } else {
                            $('.exist-deceased-popup').hide();
                            $('.section-create-label-form').removeClass('cant-edit');
                        }

                        unBlockLoader($container);
                    },
                    error: function error() {
                    }
                });
            }
        });

        $('.close-exist-popup').on('click', function (event) {
            var emptyFirstName = $('[name="firstname"]').val();
            var emptyLastName = $('[name="lastname"]').val();

            if (is_rtl()) {
                var homeUrl = window.location.origin;
                //window.location.href = homeUrl + '?&first_empty=' + emptyFirstName + '&last_empty=' + emptyLastName;
            }

            if ($('body').hasClass('ltr')) {
                var homeUrl = window.location.origin;
                //window.location.href = homeUrl + '/en/' + '?&first_empty=' + emptyFirstName + '&last_empty=' + emptyLastName;
            }

            $('.exist-deceased-popup').hide();
            $('.section-create-label-form').removeClass('cant-edit');
        });

        $('.second-word-close').on('click', function (event) {
            // var homeUrl = window.location.origin;
            // var emptyFirstName = $('[name="firstname"]');
            // var emptyLastName = $('[name="lastname"]');
            // window.location.href = homeUrl+'?person_id=0'+'&first_empty='+emptyFirstName+'&last_empty='+emptyLastName;
            $('.close-exist-popup').trigger('click');
            $('.exist-deceased-popup').hide();
            $('.section-create-label-form').removeClass('cant-edit');
        }); //search name validation

    });
}

function printCanvas($myCanvas) {
    if ($('#record-status').val() !== 'publish') {
        add_not_approved_popup($myCanvas);
    }

    var dataUrl = $myCanvas.getCanvasImage(); //attempt to save base64 string to server using this var

    var windowContent = '<!DOCTYPE html>';
        windowContent += '<html>';
        windowContent += '<head><title>הדפסת תוית</title></head>';
        windowContent += '<body>';
        windowContent += '<div style="margin:auto;">';
        windowContent += '<img style="width:17.4cm; height:5.4cm" src="' + dataUrl + '"></img>';
        windowContent += '</div>';
        windowContent += '</body>';
        windowContent += '</html>';
    var printWin = window.open('', '', 'width=1200,height=500');

    if (printWin) {
        printWin.document.open();
        printWin.document.write(windowContent);
        printWin.document.close();
        printWin.focus();
        setPrinted();

        setTimeout(function () {
            printWin.print();
            printWin.close();
        }, 150);
    }
}

function shareCanvasFB($myCanvas, site_url) {
    var dataUrl = $myCanvas.getCanvasImage(); //attempt to save base64 string to server using this var

    var printWin = window.open(site_url + '/', '_blank', 'width=1200,height=500');
    printWin.document.open();
    // printWin.document.write(windowContent);
    // printWin.document.close();
    printWin.focus();

}

function printSelectCanvas($myCanvas) {
    var ltr = '';
    var page_title = 'הדפסת תוית';
    if (!is_rtl()) {
        ltr = 'ltr';
        page_title = 'Print Label';
    }
    if ($myCanvas.length > 0) {
        var dataUrl = '';

        for (var i = 0; i < $myCanvas.length; i++) {
            dataUrl = dataUrl + $myCanvas[i]; //attempt to save base64 string to server using this var
        }

        var windowContent = '<!DOCTYPE html>';
        windowContent += '<html>';
        windowContent += '<head>'; // windowContent += '<link href="../assets/print.css" rel="stylesheet" type="text/css">';
        windowContent += '<title> ' + page_title + '</title>';
        windowContent += '<style>@import url("https://fonts.googleapis.com/css2?family=Assistant:wght@200..800&display=swap");body{ font-family: "Assistant", sans-serif;width:100%;}#label-print{display:block;max-width:658px;margin:1rem auto;position:relative;}img{width:100%;height:auto}#wrap-qr_code{position:absolute;left:262px;bottom:0px;width:45px}#wrap-qr_code img{width:100%;}.inner-text{text-align: center; line-height: 12px; position: absolute; color: #000; left: 5.4cm; right: 10cm; font-size: 10px; top: 35px; width: 4.3cm; margin: 0 auto; letter-spacing: -0.2px;}h3{font-size: 18px; font-weight: 600; line-height: 18px; margin: 0;}p{line-height:1.25; margin: 0;}p.years {padding: 4px 0 4px 0; font-size: 10px; line-height: 1.12; margin: 0;}.ltr p{font-size: 9.7px;line-height: 1.14;}.ltr p.years{padding:2px 0 2px 0;} .wrap-all-d { display:block !important;}</style>';
        windowContent += '</head>';

        windowContent += '<body class="' + ltr + '">';
        windowContent += '<div class="wrap-all-d" style="margin:auto; display:none">';
        windowContent += dataUrl;
        windowContent += '</div>';
        windowContent += '</body>';
        windowContent += '</html>';

        var printWin = window.open('', '', 'width=1200,height=500');
        printWin.document.open();
        printWin.document.write(windowContent);
        printWin.document.close();

        printWin.focus();

        setTimeout(function () {
            printWin.print();
            // printWin.close();
            // setPrinted();
        }, 930);
    }
}

function setPrinted() {
    if (!getCookie('printed')) {
        setCookie('printed', 'true', true);
        setTimeout(function () {
            showPrintedThankyouMessage();
        }, 930);
    }
}

function showPrintedThankyouMessage() {

    var content = info.print_popup_content;
    var url = $('a.donation-page').attr('href');

    var buttons = "<div class='buttons'>" +
        "<a href='" + url + "' class='yellow-button button-general' type='button'>" + info.print_popup_donate_button_text + "</a>" +
        "<input class='blank-button button-general close-popup' type='button' value='" + info.print_popup_close_button_text + "'/>" +
        "</div>";

    content = content + buttons;

    showModal(info.print_popup_title, content);
}

function showModal(title, content) {
    var modalId = '#main-popup-template';
    var $popup = $(modalId);

    $popup.find('.entry-title').html(title);
    $popup.find('.entry-text').html(content);

    $.magnificPopup.open({
        items: {
            src: modalId,
            type: 'inline'
        },
        callbacks: {
            close: function () {
            }
        }
    });

    $(document.body).on('click', '.close-popup', function () {
        $.magnificPopup.close();
    });
}

function getParameterByName(name, url) {
    if (!url) {
        url = window.location.href;
    }

    name = name.replace(/[\[\]]/g, "\\$&");
    var regex = new RegExp("[?&]" + name + "(=([^&#]*)|&|#|$)"),
        results = regex.exec(url);
    if (!results) return null;
    if (!results[2]) return '';
    return decodeURIComponent(results[2].replace(/\+/g, " "));
}

String.prototype.replaceArray = function (find, replace) {
    var replaceString = this;

    for (var i = 0; i < find.length; i++) {
        replaceString = replaceString.replace(find[i], replace[i]);
    }

    return replaceString;
};

function searchAndFilters() {
    var sections = $('.search-and-filters');

    sections.each(function (index, el) {
        /* Reset Url */
        var section = $(el);
        var postType = section.find('input[name="post_type"]').val();
        var templatePart = section.find('input[name="template_part"]').val();
        var randNameButton = section.find('.random-name');
        var fields = [];
        var meta_query = {};

        setLinkFieldArg();
        section.find('.equal').matchHeight();

        function clearAllCheckedFIlters(section) {
            section.find('[data-term-id].active, [data-field-value].active').removeClass('active');
        }

        function reset_url_from_parameters(parameter_type) {
            var SourceUrl = window.location.href;

            if (SourceUrl.indexOf(parameter_type) > -1) {
                empty_url = SourceUrl.slice(0, SourceUrl.indexOf(parameter_type) - 1);
                return empty_url;
            }
        }
        /* Use in search page in meta */

        function getAllUrlParameter(arr) {
            if (arr === 'array') {
                var Rparams = [];
            } else {
                var Rparams = {};
            }

            var sPageURL = window.location.search.substring(1);
            var sURLVariables = sPageURL.split('&');

            for (var i = 0; i < sURLVariables.length; i++) {
                var sParameterName = sURLVariables[i].split('=');
                var par = sParameterName[0];
                var name = sParameterName[1];

                if (par != 'search_term' && par != 'page_number' && par != 'link') {
                    if (arr === 'array') {
                        Rparams.push([name, par]);
                    } else {
                        Rparams[par] = name;
                    }
                }
            }

            return Rparams;
        }
        /* Use in search page in meta */

        function returnBaseUrl(url) {
            var url = typeof url !== 'undefined' ? url : false;

            if (url == false) {
                url = window.location.href;
            }

            var tempArray = url.split("?");
            var baseURL = tempArray[0];
            return baseURL;
        }
        /* update url parameter */

        function updateURLParameter(url, param, paramVal) {
            var TheAnchor = null;
            var newAdditionalURL = "";
            var tempArray = url.split("?");
            var baseURL = tempArray[0];
            var additionalURL = tempArray[1];
            var temp = "";

            if (additionalURL) {
                var tmpAnchor = additionalURL.split("#");
                var TheParams = tmpAnchor[0];
                TheAnchor = tmpAnchor[1];
                if (TheAnchor) additionalURL = TheParams;
                tempArray = additionalURL.split("&");

                for (i = 0; i < tempArray.length; i++) {
                    if (tempArray[i].split('=')[0] != param) {
                        newAdditionalURL += temp + tempArray[i];
                        temp = "&";
                    }
                }
            } else {
                var tmpAnchor = baseURL.split("#");
                var TheParams = tmpAnchor[0];
                TheAnchor = tmpAnchor[1];
                if (TheParams) baseURL = TheParams;
            }

            if (TheAnchor) paramVal += "#" + TheAnchor;
            var rows_txt = temp + "" + param + "=" + paramVal;
            return baseURL + "?" + newAdditionalURL + rows_txt;
        }
        /* remove url parameter */

        function removeURLParameter(url, parameter) {
            var fragment = url.split('#');
            var urlparts = fragment[0].split('?');

            if (urlparts.length >= 2) {
                var urlBase = urlparts.shift(); //get first part, and remove from array

                var queryString = urlparts.join("?"); //join it back up

                var prefix = encodeURIComponent(parameter) + '=';
                var pars = queryString.split(/[&;]/g);

                for (var i = pars.length; i-- > 0;) {
                    //reverse iteration as may be destructive
                    if (pars[i].lastIndexOf(prefix, 0) !== -1) {
                        //idiom for string.startsWith
                        pars.splice(i, 1);
                    }
                }

                url = urlBase + '?' + pars.join('&');

                if (fragment[1]) {
                    url += "#" + fragment[1];
                }
            }

            return url;
        }

        function add_active_class_to_url_params() {
            href = window.location.href;
            params = getAllUrlParameter('array');
            var tmpParams = params;
            tmpParams.push([getUrlParameter('link'), 'link']);

            for (var i = 0; i < tmpParams.length; i++) {
                var taxName = tmpParams[i][1];
                var termId = tmpParams[i][0];

                if (termId) {
                    var termIdsArray = termId.split(",");

                    for (var j = 0; j < termIdsArray.length; j++) {
                        $('[data-tax-name="' + taxName + '"]').first().find('[data-term-id="' + termIdsArray[j] + '"]').addClass('active');
                        $('[data-field-name="' + taxName + '"]').first().find('[data-field-value="' + termIdsArray[j] + '"]').addClass('active');
                        $('[data-tax-name="' + taxName + '"]').first().find('[data-term-range*="' + termIdsArray[j] + '"]').addClass('active');
                    }
                }
            }

            params = getAllUrlParameter();
            search = getUrlParameter('search_term');
            paged = getUrlParameter('page_number');

            if (search) {
                $('.results-title').addClass('active');
            } else {
                $('.results-title').removeClass('active');
            }

            ajaxGetItems(params, search, paged);
        }

        function ajaxGetItems(taxonomies, search, pageNumber, args) {
            if (search) {
                var search = decodeURIComponent(search);
            }

            data = {
                action: 'ajax_get_posts',
                search_term: search,
                taxonomies: taxonomies,
                paged: pageNumber,
                post_type: postType,
                nonce: info.get_posts_nonce,
                template_part: templatePart,
                fields: fields,
                posts_per_page: 10,
                lang: info.lang,
                meta_query: meta_query
            };
            $.extend(data, args);
            $.ajax({
                url: info.ajaxUrl,
                type: 'POST',
                dataType: 'json',
                data: data,
                beforeSend: function beforeSend(xhr) {
                    $('.results').addClass('loading');
                }
            }).done(function (res) {
                $('.results').removeClass('loading');
                $('.results').html(res.html);
                $('.num-of-results').html(res.total_results);
                $('.page-num').html(res.current_paged);
                $('.total-pages').html(res.numbers_of_pages);
                totalPages = res.numbers_of_pages;
                drawPagination(res.current_paged, res.numbers_of_pages, 7);
                randNameButton.removeClass('disable');
            });

        }

        function Pagination() {
            $('.pagination').on('click', 'li', function (event) {
                event.preventDefault();
                /* Act on the event */

                buttonText = $(this).attr('data-nav-to');

                if ($(this).hasClass('current')) {
                    return false;
                }

                if (buttonText == 'next') {
                    paged++;
                } else if (buttonText == 'prev') {
                    paged--;
                } else if (buttonText == 'first') {
                    paged = 1;
                } else if (buttonText == 'last') {
                    paged = totalPages;
                } else {
                    paged = buttonText;
                }

                href = window.location.href;
                var new_url = updateURLParameter(href, 'page_number', paged);
                history.pushState('', 'New Page Title', new_url);
                ajaxGetItems(paramsObj, search, paged);
            });
        }

        function drawPagination(currentPage, totalPages, countButtonsToDisplay) {
            var pagination = $('.pagination');
            pagination.html('');
            var currentPage, totalPages, countButtonsToDisplay;

            if (totalPages <= countButtonsToDisplay) {
                //draw totalPages buttons
                drawButtons(1, totalPages, currentPage);
            } else {
                if (currentPage - 1 <= countButtonsToDisplay / 2) {
                    //draw first countButtonsToDisplay buttons
                    drawButtons(1, countButtonsToDisplay, currentPage); //draw next button

                    drawLastButton();
                } else if (currentPage > totalPages - Math.round(countButtonsToDisplay / 2)) {
                    //draw last countButtonsToDisplay buttons
                    drawButtons(totalPages + 1 - countButtonsToDisplay, totalPages, currentPage); //draw prev button

                    drawFirstButton();
                } else {
                    //draw curerrentPage button with roundDown(countButtonsToDisplay/2) buttons before and after
                    drawButtons(currentPage - Math.floor(countButtonsToDisplay / 2), parseInt(currentPage) + Math.floor(countButtonsToDisplay / 2), currentPage); //draw prev and next buttons

                    drawLastButton();
                    drawFirstButton();
                }
            }

            var test = totalPages + 1 - countButtonsToDisplay / 2;

            function drawButtons(first, last, current) {
                for (var i = first; i <= last; i++) {
                    var $button;

                    if (i == current) {
                        $button = '<li class="current" data-nav-to="' + i + '">' + i + '</li>';
                    } else {
                        $button = '<li data-nav-to="' + i + '">' + i + '</li>';
                    }

                    pagination.append($button);
                }
            }

            function drawLastButton() {
                var direction = info.rtl ? 'left' : 'right';
                pagination.append('<li data-nav-to="last"><span class="icon-angle-double-' + direction + '"></span></li>');
            }

            function drawFirstButton() {
                var direction = info.rtl ? 'right' : 'left';
                pagination.prepend('<li data-nav-to="first"><span class="icon-angle-double-' + direction + '"></span></li>');
            }
        }

        var params, taxonomies, href;
        var paramsObj = {};
        var paramsArr = [];
        Pagination(); // add_active_class_to_url_params();

        $('.tags-wrap').jScrollPane();
        var paramsObj = getAllUrlParameter();
        var search = getUrlParameter('search_term');
        var paged = getUrlParameter('page_number'); // ajaxGetItems(paramsObj,search,paged);

        var rand = getUrlParameter('rand');

        if (rand == 'yes') {
            section.find('.random-name').click();
        } else {
            add_active_class_to_url_params();
        }

        $('.search-submit').on('click', function (event) {
            event.preventDefault();
            href = window.location.href;
            search = $('.search-field').val();
            $('.results-title span').html(search);
            var new_url = removeURLParameter(href, 'page_number');
            new_url = updateURLParameter(new_url, 'search_term', search);
            history.pushState('', 'New Page Title', new_url);
            var paramsObj = getAllUrlParameter();
            search = getUrlParameter('search_term');
            paged = getUrlParameter('page_number');
            add_active_class_to_url_params();
        });
        $('.tags-selector').on('click', 'a[data-term-id]', function (event) {
            event.preventDefault();
            $(this).toggleClass('active');
            href = window.location.href;
            var parent = $(this).closest('ul');
            var taxName = parent.attr('data-tax-name');
            var activeTerms = parent.find('.active');
            var termIdsArray = [];

            for (var i = 0; i < activeTerms.length; i++) {
                termIdsArray.push($(activeTerms[i]).attr('data-term-id'));
            }

            termIdsString = termIdsArray.toString();
            var new_url = updateURLParameter(href, taxName, termIdsString);
            history.pushState('', 'New Page Title', new_url);
            paramsObj = getAllUrlParameter();

            if (paramsObj['page_number'] != 'undefined') {
                delete paramsObj['page_number'];
                paged = 1;
                new_url = removeURLParameter(new_url, 'page_number');
                history.pushState('', 'New Page Title', new_url);
            }

            if (paramsObj[taxName].length === 0) {
                delete paramsObj[taxName];
                new_url = removeURLParameter(new_url, taxName);
                history.pushState('', 'New Page Title', new_url);
            }

            search = getUrlParameter('search_term');
            paged = getUrlParameter('page_number');
            taxonomies = paramsObj;
            ajaxGetItems(paramsObj, search, paged);
            mark_parent_active($(this));
        });
        $('.tags-selector').on('click', 'a[data-field-value]', function (event) {
            event.preventDefault();
            $(this).toggleClass('active');
            href = window.location.href;
            var parent = $(this).closest('ul');
            var fieldName = parent.attr('data-field-name');
            var activeTerms = parent.find('.active');
            var termIdsArray = [];

            for (var i = 0; i < activeTerms.length; i++) {
                termIdsArray.push($(activeTerms[i]).attr('data-field-value'));
            }

            termIdsString = termIdsArray.toString();
            var new_url = updateURLParameter(href, fieldName, termIdsString);
            history.pushState('', 'New Page Title', new_url);
            paramsObj = getAllUrlParameter();

            if (paramsObj['page_number'] != 'undefined') {
                delete paramsObj['page_number'];
                paged = 1;
                new_url = removeURLParameter(new_url, 'page_number');
                history.pushState('', 'New Page Title', new_url);
            }

            var field = getUrlParameter(fieldName);

            if (field.length === 0 || field == 'have,have-not') {
                new_url = removeURLParameter(new_url, fieldName);
                history.pushState('', 'New Page Title', new_url);
            }

            search = getUrlParameter('search_term');
            paged = getUrlParameter('page_number');
            taxonomies = paramsObj;
            var comapre = '!=';

            if (field == 'have-not') {
                comapre = '=';
            }

            var args = {};

            if (field == 'have' || field == 'have-not') {
                var args = {
                    meta_query: {
                        relation: 'AND',
                        0: {
                            key: fieldName,
                            compare: comapre,
                            value: null
                        }
                    }
                };
                meta_query = {
                    relation: 'AND',
                    0: {
                        key: fieldName,
                        compare: comapre,
                        value: null
                    }
                };
            } else {
                meta_query = {};
            }

            ajaxGetItems(paramsObj, search, paged, args);
        });
    });
}

function setLinkFieldArg() {
    var fieldName = 'link';
    var field = getUrlParameter(fieldName);

    if (field == 'have' || field == 'have-not') {
        var comapre = '!=';

        if (field == 'have-not') {
            comapre = '=';
        }

        var args = {
            meta_query: {
                relation: 'AND',
                0: {
                    key: fieldName,
                    compare: comapre,
                    value: null
                }
            }
        };
        meta_query = {
            relation: 'AND',
            0: {
                key: fieldName,
                compare: comapre,
                value: null
            }
        };
    } else {
        meta_query = {};
    }
}

async function getQrImageSource(text) {
    if (!text) return null;

    // qrcodejs API: new QRCode(element, options)
    if (typeof window.QRCode !== "function") {
        throw new Error("QRCode library is not loaded (expected qrcodejs constructor on window.QRCode).");
    }

    return await new Promise((resolve, reject) => {
        const tmp = document.createElement("div");
        tmp.style.position = "fixed";
        tmp.style.left = "-99999px";
        tmp.style.top = "-99999px";
        tmp.style.width = "0";
        tmp.style.height = "0";
        tmp.style.overflow = "hidden";
        document.body.appendChild(tmp);

        try {
            new window.QRCode(tmp, {
                text,
                width: 180,
                height: 180,
                correctLevel: window.QRCode.CorrectLevel ? window.QRCode.CorrectLevel.M : undefined,
            });

            // Let qrcodejs actually render into tmp
            setTimeout(() => {
                try {
                    const canvas = tmp.querySelector("canvas");
                    if (canvas) {
                        const dataUrl = canvas.toDataURL("image/png");
                        tmp.remove();
                        resolve(dataUrl);
                        return;
                    }

                    const img = tmp.querySelector("img");
                    if (img && img.src) {
                        tmp.remove();
                        resolve(img.src); // often already a data: URL
                        return;
                    }

                    tmp.remove();
                    resolve(null);
                } catch (e) {
                    tmp.remove();
                    reject(e);
                }
            }, 0);
        } catch (e) {
            tmp.remove();
            reject(e);
        }
    });
}

function searchAndFiltersNew() {
    $(document).on('click', '.links .checkbox', function () {
        // $('.links .checkbox').on('click',function(event){
        $(this).toggleClass("selected");
        $(this).find('span').toggle();
    });

    $(document).on('click', '.js-print-labels', async function () { // .btn-full-print
        var div = $('inner-tav');
        var img = info.label_image;//$(this).data('back-img'); // var img = $('.btn-full-print').data('back-img');

        var wishlist_list = info.wishlist_full;
        wishlist_list.forEach(item=>{
            $.ajax({
                url:info.ajaxUrl,
                type: 'POST',
                dataType: 'json',
                data:{
                    'action':'print_count',
                    'person_id':item.ID,
                    'lang':item.language,
                    'print_count':'1'
                }
            })
        })
        var list = new Array();

       for(const value of wishlist_list) {
           if (!img) continue;
           var qr_code = value.url ? value.url : '';
           var first_name = value.first_name ? value.first_name : ' ';
           var last_name = value.last_name ? value.last_name : ' ';

           var years = value.year_of_birth ? value.year_of_birth : '';
           years += years ? '  -  ' + value.year_of_death : value.year_of_death;

           var birth_country_city = value.born_city ? value.born_city + ', ' + value.birth_country : value.birth_country;
           var occupation = value.occupation ? value.occupation : ' ';
           var diedIn = value.place_of_death ? value.place_of_death : ' ';
           var html = '<div id="label-print"><img style="width:17.4cm; height:5.4cm" src="' + img + '">';

           var sonOf = get_son_of_text(value.gender, value.father_name, value.mother_name);
           var marriedTo = get_married_to_text(value.gender, value.partner_name, value.personal_status);
           var fatherTo = get_parent_to(value.gender, value.number_of_kids);

           var family = build_family_string(sonOf, marriedTo, fatherTo, value.notice, "<br/>");

           var countrycity ='';
           var fsuff = '';
           if(value.gender == 'female'){
               fsuff = '_female';
           }
           if (value.birth_country && value.born_city) {
               countrycity = sticker.label['city_country_lbl'+fsuff];
           } else if (value.born_city) {
               countrycity = sticker.label['city_lbl'+fsuff];
           } else if (value.birth_country) {
               countrycity = sticker.label['country_lbl'+fsuff];
           }
           if(countrycity != '' && (value.born_city != '' || value.birth_country != '')){
               countrycity = countrycity.replace('%%city%%',value.born_city);
               countrycity = countrycity.replace('%%country%%',value.birth_country)+' ';
           }
           var occupation_text = occupation;
           if(occupation_text != ''){
               occupation_label = sticker.label.occupation_lbl;
               occupation_text = occupation_label.replace('%%occupation%%',occupation_text)+' ';
           }
           var pod = value.place_of_death;
           if(pod != '') {
               pod_label = sticker.label['pod_lbl'+fsuff];
               pod = pod_label.replace('%%place_of_death%%',pod);
           }

           console.log({ID: value.ID})
           if(value.ID >= 262194){
               var yob = value.year_of_birth;
               var yod = value.year_of_death;
               var age = 0;
               if(yod && yob){
                   var age = yod-yob;
               }

               // New QR code generator
               var skip_qr_code = false;
               var qr_file_path;
               console.log('qr_code: '+qr_code+' skip_qr_code '+skip_qr_code);
               if( qr_code && qr_code != '') {
                   try {
                       qr_file_path = await getQrImageSource(qr_code)
                   } catch(e) {
                       console.log(e);
                   }
               }

               var familystring = build_fstring_dyn('label',age,value.gender,value.father_name,value.mother_name,value.partner_name,value.personal_status,value.number_of_kids,value.notice);
               familystring = familystring.replace('##lb##','</p><p>');
               familystring = familystring.replace(/\.$/, "");
               if(!skip_qr_code){
                   html = html + '<div id="wrap-qr_code"><img src="'+ qr_file_path +'" alt=""></div>';
               }
               html = html + '<div class="inner-text">';
               html = html + '<h3 class="first_name">' + first_name + '</h3>';
               html = html + '<h3 class="last_name">' + last_name + '</h3>';
               html = html + '<p class="years smalltext">' + years + '</p>';
               html = html + '<p class="birth_country_city smalltext">' + countrycity + '</p>';
               html = html + '<p class="family smalltext">' + familystring + '</p>';
               html = html + '<p class="occupation smalltext">' + occupation_text + '</p>';
               html = html + '<p class="diedin smalltext">' + pod + '</p>';
               html = html + '</div></div>';

           } else {
               // New QR code generator
               var qr_file_path;
               var skip_qr_code = false;
               console.log('qr_code: '+qr_code+' skip_qr_code '+skip_qr_code);
               if( qr_code && qr_code != '') {
                   try {
                       qr_file_path = await getQrImageSource(qr_code)
                   } catch(e) {
                       console.log(e);
                   }
               }

               if(!skip_qr_code){
                   html = html + '<div id="wrap-qr_code"><img src="'+ qr_file_path +'" alt=""></div>';
               }
               html = html + '<div class="inner-text">';
               html = html + '<h3 class="first_name">' + first_name + '</h3>';
               html = html + '<h3 class="last_name">' + last_name + '</h3>';
               html = html + '<p class="years smalltext">' + years + '</p>';
               html = html + '<p class="birth_country_city smalltext">' + countrycity + '</p>';
               html = html + '<p class="family smalltext">' + family + '</p>';
               html = html + '<p class="diedin smalltext">' + pod + '</p>';

               html = html + '</div></div>';
           }
           list.push(html);
        }
        printSelectCanvas(list);
    });

    var sections = $('.search-and-filters-deceaseds');

    sections.each(function (index, el) {
        /* Reset Url */
        var section = $(el);
        var postType = section.find('input[name="post_type"]').val();
        var templatePart = section.find('input[name="template_part"]').val();
        var randNameButton = section.find('.random-name');
        var fields = [];
        var meta_query = {};
        var randomPrint = getUrlParameter('print');
        var params, taxonomies, href;
        var paramsObj = {};
        var paramsArr = [];
        var language = '';

        setLinkFieldArg();
        section.find('.equal').matchHeight();

        if (!is_rtl()) {
            language = 'en';
        } else {
            language = 'he';
        }

        var data = {
            action: 'get_random_deceased',
            language: language
        };

        section.on('click', '.random-name', function (event) {
            event.preventDefault();

            if (!$(this).hasClass('disable')) {
                randNameButton.addClass('disable'); //ajaxGetItems(null,null,null,args);

                $.ajax({
                    url: info.ajaxUrl + "?rand=" + makeid(5),
                    type: 'POST',
                    dataType: 'json',
                    data: data,
                    beforeSend: function beforeSend(xhr) {
                        $('.results').addClass('loading');
                    },
                    success: function success(res) {
                        $('.results').removeClass('loading');

                        $('.count_results').html('1');
                        $('.search-and-filters-deceaseds main .main-row .results-col .pagination-wrap .wrap_btn').addClass('show');

                        var single_deceased_row = typeof 'undefined' !== res[0] ? get_deceased_row_html(res[0]) : '';

                        $('.results').html(single_deceased_row);

                        //auto print
                        if (randomPrint) {
                            var url = $('a.print-person').first().attr('href');
                            window.location = url;
                            return false;
                        }

                        randNameButton.removeClass('disable');
                    }
                });
                href = returnBaseUrl(); // var new_url = updateURLParameter(href,'page_number',paged);

                clearAllCheckedFIlters(section);
                history.pushState('', 'New Page Title', href);
            }
        });


        Pagination_d(); // add_active_class_to_url_params2();

        $('.tags-wrap').jScrollPane();

        var paramsObj = getAllUrlParameter2(); // var paramsObj = getAllUrlParameter2('array');
        var search_first_name = getUrlParameter2('search_first_name');
        var search_family_name = getUrlParameter2('search_family_name');
        var search_qccupation = getUrlParameter2('search_qccupation');
        var search_birth_city = getUrlParameter2('search_birth_city');
        var search_birth_country = getUrlParameter2('search_birth_country');
        var paged = getUrlParameter2('page_number'); // ajaxGetItems(paramsObj,search,paged);
        var rand = getUrlParameter2('rand');
        var search_first_name = getUrlParameter2('search_first_name');
        var search_family_name = getUrlParameter2('search_family_name');

        if (search_first_name || search_family_name || search_qccupation || search_birth_country || search_birth_city) {
            var search = [];
            search.push(search_first_name);
            search.push(search_family_name);
            if (search_qccupation != 'null') {
                search.push(search_qccupation);
            }
            if (search_birth_country != 'null') {
                search.push(search_birth_country);
            }
            search.push(search_birth_city);
        }

        if (rand == 'yes') {
            section.find('.random-name').click();
        } else {

            if ((search_first_name && search_first_name.length > 0) || (search_family_name && search_family_name.length > 0)) {
                add_active_class_to_url_params2();
            }
        }

        $('.multiple-search-submit').on('click', function (event) {
            event.preventDefault(); // search = $('.search-field').val();

            search_first_name = $('input[name="search_first_name"]').val();
            search_family_name = $('input[name="search_family_name"]').val();
            search_qccupation = $('input[name="search_qccupation"]').val();//$('select[name = "search_qccupation"]').val();
            search_birth_city = $('input[name="search_birth_city"]').val(); // search = $('input[name="search_first_name"]').val
            search_birth_country = $('select[name="search_birth_country"]').val(); // search = $('input[name="search_first_name"]').val();

            if (search_qccupation == 'null' || search_qccupation == null) {
                search_qccupation = '';
            }
            if (search_birth_country == 'null' || search_birth_country == null) {
                search_birth_country = '';
            }

            let full_name = search_first_name + ' ' + search_family_name;
            let search_kyes = [];
            if (search_first_name || search_family_name) {
                search_kyes.push(full_name);
            }
            if (search_qccupation) {
                search_kyes.push(search_qccupation);
            }
            if (search_birth_city) {
                search_kyes.push(search_birth_city);
            }
            if (search_birth_country) {
                search_kyes.push(search_birth_country);
            }

            $('.results-title h1 span').html(search_kyes.join(', '));
            href = window.location.href;
            var new_url = removeURLParameter(href, 'page_number');
            new_url = updateURLParameter(new_url, 'search_first_name', search_first_name);
            href = window.location.href;
            new_url = updateURLParameter(new_url, 'search_family_name', search_family_name);
            href = window.location.href;
            new_url = updateURLParameter(new_url, 'search_qccupation', search_qccupation);
            href = window.location.href;
            new_url = updateURLParameter(new_url, 'search_birth_country', search_birth_country);
            new_url = updateURLParameter(new_url, 'search_birth_city', search_birth_city);
            history.pushState('', 'New Page Title', new_url);
            var paramsObj = getAllUrlParameter2('array');
            search_first_name = getUrlParameter2('search_first_name');
            search_family_name = getUrlParameter2('search_family_name');
            search_qccupation = getUrlParameter2('search_qccupation');
            search_birth_country = getUrlParameter2('search_birth_country');
            search_birth_city = getUrlParameter2('search_birth_city');
            paged = getUrlParameter2('page_number');
            add_active_class_to_url_params2(); // ajax_to_search_d(search);
        });

        $('.tags-selector_d').on('click', 'a[data-parameter-id]', function (event) {
            event.preventDefault();
            $(this).toggleClass('active');
            href = window.location.href;
            var parent = $(this).closest('ul');
            var taxName = parent.attr('data-tax-name');
            var activeTerms = parent.find('.active');
            var termIdsArray = [];

            for (var i = 0; i < activeTerms.length; i++) {
                termIdsArray.push($(activeTerms[i]).attr('data-parameter-id'));
            }

            termIdsString = termIdsArray.toString();
            var new_url = updateURLParameter(href, taxName, termIdsString);
            history.pushState('', 'New Page Title', new_url);
            paramsObj = getAllUrlParameter2();

            if (paramsObj['page_number'] != 'undefined') {
                delete paramsObj['page_number'];
                paged = 1;
                new_url = removeURLParameter(new_url, 'page_number');
                history.pushState('', 'New Page Title', new_url);
            } // if($ [paramsObj[taxName]] | length === 0){

            if (paramsObj[taxName]) {
                if (paramsObj[taxName].length === 0) {
                    delete paramsObj[taxName];
                    new_url = removeURLParameter(new_url, taxName);
                    history.pushState('', 'New Page Title', new_url);
                }
            } // search = getUrlParameter2('search_term');

            search_first_name = getUrlParameter2('search_first_name');
            search_family_name = getUrlParameter2('search_family_name');
            search_qccupation = getUrlParameter2('search_qccupation');
            search_birth_city = getUrlParameter2('search_birth_city');
            search_birth_country = getUrlParameter2('search_birth_country');

            if (search_first_name || search_family_name || search_qccupation || search_birth_country || search_birth_city) {
                search = [];
                search.push(search_first_name);
                search.push(search_family_name);
                if (search_qccupation != 'null') {
                    search.push(search_qccupation);
                }
                if (search_birth_country != 'null') {
                    search.push(search_birth_country);
                }
                search.push(search_birth_city);
            } // search[search_first_name,search_family_name,search_qccupation,search_birth_country]

            paged = getUrlParameter2('page_number');
            taxonomies = paramsObj; // ajaxGetItems(paramsObj,search,paged);

            paramsObj = getAllUrlParameter2('array');

            setTimeout(function () {
                ajax_to_search_d(paramsObj, search, paged);
            }, 300);

            mark_parent_active($(this));
        });

        $('.tags-selector_d').on('click', 'a[data-field-value]', function (event) {
            event.preventDefault();
            $(this).toggleClass('active');
            href = window.location.href;
            var parent = $(this).closest('ul');
            var fieldName = parent.attr('data-field-name');
            var activeTerms = parent.find('.active');
            var termIdsArray = [];

            for (var i = 0; i < activeTerms.length; i++) {
                termIdsArray.push($(activeTerms[i]).attr('data-field-value'));
            }

            termIdsString = termIdsArray.toString();
            var new_url = updateURLParameter(href, fieldName, termIdsString);
            history.pushState('', 'New Page Title', new_url);
            paramsObj = getAllUrlParameter2('array');

            if (paramsObj['page_number'] != 'undefined') {
                delete paramsObj['page_number'];
                paged = 1;
                new_url = removeURLParameter(new_url, 'page_number');
                history.pushState('', 'New Page Title', new_url);
            }

            var field = getUrlParameter2(fieldName);

            if (field.length === 0 || field == 'have,have-not') {
                new_url = removeURLParameter(new_url, fieldName);
                history.pushState('', 'New Page Title', new_url);
            } // search = getUrlParameter2('search_term');

            search_first_name = getUrlParameter2('search_first_name');
            search_family_name = getUrlParameter2('search_family_name');
            search_qccupation = getUrlParameter2('search_qccupation');
            search_birth_city = getUrlParameter2('search_birth_city');
            search_birth_country = getUrlParameter2('search_birth_country');

            if (search_first_name || search_family_name || search_qccupation || search_birth_country || search_birth_city) {
                search = [];
                search.push(search_first_name);
                search.push(search_family_name);
                if (search_qccupation != 'null') {
                    search.push(search_qccupation);
                }
                if (search_birth_country != 'null') {
                    search.push(search_birth_country);
                }
                search.push(search_birth_city);
                paged = getUrlParameter2('page_number');
                taxonomies = paramsObj;
            }

            var comapre = '!=';

            if (field == 'have-not') {
                comapre = '=';
            }

            var args = {};

            if (field == 'have' || field == 'have-not') {
                var args = {
                    meta_query: {
                        relation: 'AND',
                        0: {
                            key: fieldName,
                            compare: comapre,
                            value: null
                        }
                    }
                };
                meta_query = {
                    relation: 'AND',
                    0: {
                        key: fieldName,
                        compare: comapre,
                        value: null
                    }
                };
            } else {
                meta_query = {};
            }

            setTimeout(function () {
                ajax_to_search_d(paramsObj, search, paged);
            }, 300);
        });
    });
}


function setLinkFieldArg() {
    var fieldName = 'link';
    var field = getUrlParameter2(fieldName);

    if (field == 'have' || field == 'have-not') {
        var comapre = '!=';

        if (field == 'have-not') {
            comapre = '=';
        }

        var args = {
            meta_query: {
                relation: 'AND',
                0: {
                    key: fieldName,
                    compare: comapre,
                    value: null
                }
            }
        };
        meta_query = {
            relation: 'AND',
            0: {
                key: fieldName,
                compare: comapre,
                value: null
            }
        };
    } else {
        meta_query = {};
    }
}

function makeid(length) {
    var result = '';
    var characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
    var charactersLength = characters.length;

    for (var i = 0; i < length; i++) {
        result += characters.charAt(Math.floor(Math.random() * charactersLength));
    }

    return result;
}


function clearAllCheckedFIlters(section) {
    section.find('[data-parameter-id].active, [data-field-value].active').removeClass('active');
}

function reset_url_from_parameters(parameter_type) {
    var SourceUrl = window.location.href;

    if (SourceUrl.indexOf(parameter_type) > -1) {
        empty_url = SourceUrl.slice(0, SourceUrl.indexOf(parameter_type) - 1);
        return empty_url;
    }
}

function returnBaseUrl(url) {
    var url = typeof url !== 'undefined' ? url : false;

    if (url == false) {
        url = window.location.href;
    }

    var tempArray = url.split("?");
    var baseURL = tempArray[0];
    return baseURL;
}

/* update url parameter */
function updateURLParameter(url, param, paramVal) {
    if (paramVal != '') {
        var TheAnchor = null;
        var newAdditionalURL = "";
        var tempArray = url.split("?");
        var baseURL = tempArray[0];
        var additionalURL = tempArray[1];
        var temp = "";

        if (additionalURL) {
            var tmpAnchor = additionalURL.split("#");
            var TheParams = tmpAnchor[0];
            TheAnchor = tmpAnchor[1];
            if (TheAnchor) additionalURL = TheParams;
            tempArray = additionalURL.split("&");

            for (i = 0; i < tempArray.length; i++) {
                if (tempArray[i].split('=')[0] != param) {
                    newAdditionalURL += temp + tempArray[i];
                    temp = "&";
                }
            }
        } else {
            var tmpAnchor = baseURL.split("#");
            var TheParams = tmpAnchor[0];
            TheAnchor = tmpAnchor[1];
            if (TheParams) baseURL = TheParams;
        }

        if (TheAnchor) paramVal += "#" + TheAnchor;
        var rows_txt = temp + "" + param + "=" + paramVal;
        return baseURL + "?" + newAdditionalURL + rows_txt;
    } else {
        return removeURLParameter(url, param);
    }
}

/* remove url parameter */
function removeURLParameter(url, parameter) {
    var fragment = url.split('#');
    var urlparts = fragment[0].split('?');

    if (urlparts.length >= 2) {
        var urlBase = urlparts.shift(); //get first part, and remove from array

        var queryString = urlparts.join("?"); //join it back up

        var prefix = encodeURIComponent(parameter) + '=';
        var pars = queryString.split(/[&;]/g);

        for (var i = pars.length; i-- > 0;) {
            //reverse iteration as may be destructive
            if (pars[i].lastIndexOf(prefix, 0) !== -1) {
                //idiom for string.startsWith
                pars.splice(i, 1);
            }
        }

        url = urlBase + '?' + pars.join('&');

        if (fragment[1]) {
            url += "#" + fragment[1];
        }
    }

    return url;
}

function add_active_class_to_url_params2() {
    href = window.location.href;
    params = getAllUrlParameter2('array');
    var tmpParams = params;
    tmpParams.push([getUrlParameter2('link'), 'link']);

    for (var i = 0; i < tmpParams.length; i++) {
        var taxName = tmpParams[i][1];
        var termId = tmpParams[i][0];

        if (termId) {
            var termIdsArray = termId.split(",");

            for (var j = 0; j < termIdsArray.length; j++) {
                $('[data-tax-name="' + taxName + '"]').first().find('[data-parameter-id="' + termIdsArray[j] + '"]').addClass('active');
                $('[data-field-name="' + taxName + '"]').first().find('[data-field-value="' + termIdsArray[j] + '"]').addClass('active');
                $('[data-tax-name="' + taxName + '"]').first().find('[data-term-range*="' + termIdsArray[j] + '"]').addClass('active');
            }
        }
    }

    params = getAllUrlParameter2('array');
    search_first_name = getUrlParameter2('search_first_name');
    search_family_name = getUrlParameter2('search_family_name');
    search_qccupation = getUrlParameter2('search_qccupation');
    search_birth_city = getUrlParameter2('search_birth_city');
    search_birth_country = getUrlParameter2('search_birth_country');

    if (search_birth_country == 'null' || search_birth_country == null || typeof search_birth_country == 'undefined') {
        search_birth_country = '';
    }

    if (search_first_name || search_family_name || search_qccupation || search_birth_country || search_birth_city) {
        var search = [];
        search.push(search_first_name);
        search.push(search_family_name);
        if (search_qccupation != 'null') {
            search.push(search_qccupation);
        }
        search.push(search_birth_country);
        search.push(search_birth_city);
    }

    paged = getUrlParameter2('page_number');

    if (search) {
        $('.results-title').addClass('active');
    } else {
        $('.results-title').removeClass('active');
    } // ajaxGetItems(params,search,paged);

    setTimeout(function () {
        ajax_to_search_d(params, search, paged);
    }, 300);
}

function ajaxGetItems(taxonomies, search, pageNumber, args) {
    if (search) {
        var search = decodeURIComponent(search);
    }

    data = {
        action: 'ajax_get_posts',
        search_term: search,
        taxonomies: taxonomies,
        paged: pageNumber,
        post_type: postType,
        nonce: info.get_posts_nonce,
        template_part: templatePart,
        fields: fields,
        posts_per_page: 10,
        lang: info.lang,
        meta_query: meta_query
    };

    $.extend(data, args);

    $.ajax({
        url: info.ajaxUrl,
        type: 'POST',
        dataType: 'json',
        data: data,
        beforeSend: function beforeSend(xhr) {
            $('.results').addClass('loading');
        }
    }).done(function (res) {
        $('.results').removeClass('loading');
        $('.results').html(res.html);
        $('.num-of-results').html(res.total_results);
        $('.page-num').html(res.current_paged);
        $('.total-pages').html(res.numbers_of_pages);
        totalPages = res.numbers_of_pages;
        drawPagination_d(res.current_paged, res.numbers_of_pages, 7);
        randNameButton.removeClass('disable');
    });
}

function Pagination_d() {
    $('.pagination_d').on('click', 'li', function (event) {
        event.preventDefault();
        /* Act on the event */
        paramsObj = getAllUrlParameter2('array');
        // search = getUrlParameter2('search_term');

        buttonText = $(this).attr('data-nav-to');

        if ($(this).hasClass('current')) {
            return false;
        }

        if (buttonText == 'next') {
            paged++;
        } else if (buttonText == 'prev') {
            paged--;
        } else if (buttonText == 'first') {
            paged = 1;
        } else if (buttonText == 'last') {
            paged = totalPages;
        } else {
            paged = buttonText;
        }

        href = window.location.href;
        var new_url = updateURLParameter(href, 'page_number', paged);
        history.pushState('', 'New Page Title', new_url); // ajaxGetItems(paramsObj,search,paged);

        setTimeout(function () {
            var search_first_name = getUrlParameter2('search_first_name');
            var search_family_name = getUrlParameter2('search_family_name');
            var search_qccupation = getUrlParameter2('search_qccupation');
            var search_birth_city = getUrlParameter2('search_birth_city');
            var search_birth_country = getUrlParameter2('search_birth_country');

            if (search_first_name || search_family_name || search_qccupation || search_birth_country || search_birth_city) {
                var search = [];
                search.push(search_first_name);
                search.push(search_family_name);
                if (search_qccupation != 'null') {
                    search.push(search_qccupation);
                }
                if (search_birth_country != 'null') {
                    search.push(search_birth_country);
                }
                search.push(search_birth_city);
            }

            ajax_to_search_d(paramsObj, search, paged);
        }, 300);
    });
}

/* Use in search page in meta */
function getAllUrlParameter2(arr) {
    if (arr === 'array') {
        var Rparams = [];
    } else {
        var Rparams = {};
    }

    var sPageURL = window.location.search.substring(1);
    var sURLVariables = sPageURL.split('&');

    for (var i = 0; i < sURLVariables.length; i++) {
        var sParameterName = sURLVariables[i].split('=');
        var par = sParameterName[0];
        var name = sParameterName[1];

        if (par != 'search_first_name' && par != 'search_family_name' && par != 'search_qccupation' && par != 'search_birth_country' && par != 'search_birth_city' && par != 'page_number' && par != 'link') {
            if (arr === 'array') {
                Rparams.push([name, par]);
            } else {
                Rparams[par] = name;
            }
        }
    }

    return Rparams;
}

/* Use in search page in meta */
function getUrlParameter2(sParam) {
    var sPageURL = window.location.search.substring(1);
    var sURLVariables = sPageURL.split('&');

    for (var i = 0; i < sURLVariables.length; i++) {
        var sParameterName = sURLVariables[i].split('=');

        if (sParameterName[0] == sParam) {
            return sParameterName[1];
        }
    }
}

function drawPagination_d(currentPage, totalPages, countButtonsToDisplay) {
    var pagination = $('.pagination_d');
    pagination.html('');

    if (totalPages <= countButtonsToDisplay) {
        //draw totalPages buttons
        drawButtons(1, totalPages, currentPage);
    } else {
        if (currentPage - 1 <= countButtonsToDisplay / 2) {
            //draw first countButtonsToDisplay buttons
            drawButtons(1, countButtonsToDisplay, currentPage); //draw next button

            drawLastButton();
        } else if (currentPage > totalPages - Math.round(countButtonsToDisplay / 2)) {
            //draw last countButtonsToDisplay buttons
            drawButtons(totalPages + 1 - countButtonsToDisplay, totalPages, currentPage); //draw prev button

            drawFirstButton();
        } else {
            //draw curerrentPage button with roundDown(countButtonsToDisplay/2) buttons before and after
            drawButtons(currentPage - Math.floor(countButtonsToDisplay / 2), parseInt(currentPage) + Math.floor(countButtonsToDisplay / 2), currentPage); //draw prev and next buttons

            drawLastButton();
            drawFirstButton();
        }
    }

    var test = totalPages + 1 - countButtonsToDisplay / 2;

    function drawButtons(first, last, current) {
        for (var i = first; i <= last; i++) {
            var $button;

            if (i == current) {
                $button = '<li class="current" data-nav-to="' + i + '">' + i + '</li>';
            } else {
                $button = '<li data-nav-to="' + i + '">' + i + '</li>';
            }

            pagination.append($button);
        }
    }

    function drawLastButton() {
        var direction = info.rtl ? 'left' : 'right';
        pagination.append('<li data-nav-to="last"><span class="icon-angle-double-' + direction + '"></span></li>');
    }

    function drawFirstButton() {
        var direction = info.rtl ? 'right' : 'left';
        pagination.prepend('<li data-nav-to="first"><span class="icon-angle-double-' + direction + '"></span></li>');
    }
}

function show_no_results_add_text() {
    $('.add-item-wrapper .has-results').hide();
    $('.add-item-wrapper .no-results').show();
}

function show_has_results_add_text() {
    $('.add-item-wrapper .has-results').show();
    $('.add-item-wrapper .no-results').hide();
}

jQuery(document).on('click', '.print_label_btn', function (e) {
    var lang = 'en';
    if(is_rtl()){
        lang = 'he';
    }
    var personId = jQuery(this).data('person-id');
    var goToUrl = jQuery(this).data('url');
    personId = personId.toString();
    $.ajax({
        url:info.ajaxUrl,
        type: 'POST',
        dataType: 'json',
        data:{
            'action':'print_count',
            'person_id':personId,
            'lang':lang,
            'print_count':'1'
        },
        success:function(){
            window.location = goToUrl;
            return false;
        }
    })
}),

function build_fstring_dyn(dtype,age,gender,fatherName,motherName,partnerName,personal_status,kids,notice){
    if(!gender && !fatherName && !motherName && !partnerName && !personal_status && !kids && !age ){
        return '';
    }
    var strtemplatedata = sticker[dtype];
    var strtemplate;

    var keybuild = '';
    if(fatherName != ''){
        keybuild = 'father';
    }
    if(motherName != ''){
        if(keybuild == ''){
            keybuild = 'mother';
        }else{
            keybuild += '_mother';
        }
    }
    if(partnerName != ''){
        if(keybuild == ''){
            keybuild = 'spouse';
        }else{
            keybuild += '_spouse';
        }
    }
    if(kids != ''){
        if(keybuild == ''){
            keybuild = 'children';
        }else{
            keybuild += '_children';
        }
    }
    if(fatherName == '' && motherName == '' && partnerName == '' && personal_status == '' && kids != ''){
        strtemplate = strtemplatedata[gender+'_'+dtype]['children_only'];
    }else{
        strtemplate = strtemplatedata[gender+'_'+dtype]['blank'][keybuild];
    }
    if(age > 16){
        if(personal_status != '' && personal_status != null){
            var statusbuild = '';
            if(personal_status == 'רווק'){
                statusbuild = 'adult_single';
            }
            if(personal_status == 'unknown'){
                statusbuild = 'unknown';
            }
            if(personal_status == 'נשוי'){
                statusbuild = 'married';
            }
            if(personal_status == 'אלמן'){
                statusbuild = 'widow';
            }
            if(personal_status == 'גרוש'){
                statusbuild = 'divorcee';
            }

            strtemplate = strtemplatedata[gender+'_'+dtype][statusbuild][keybuild];

        }
    }else{
        if(personal_status != '' && personal_status != null){
            var statusbuild = '';
            if(personal_status == 'רווק'){
                statusbuild = 'adult_single';
            }
            if(personal_status == 'unknown'){
                statusbuild = 'unknown';
            }
            if(personal_status == 'נשוי'){
                statusbuild = 'married';
            }
            if(personal_status == 'אלמן'){
                statusbuild = 'widow';
            }
            if(personal_status == 'גרוש'){
                statusbuild = 'divorcee';
            }

            strtemplate = strtemplatedata[gender+'_'+dtype][statusbuild][keybuild];
        }else{
            strtemplate = strtemplatedata[gender+'_'+dtype]['blank'][keybuild];
        }
    }

    if(strtemplate){
        strtemplate = strtemplate.replace('%%father_name%%',fatherName);
        strtemplate = strtemplate.replace('%%mother_name%%',motherName);
        strtemplate = strtemplate.replace('%%partner_name%%',partnerName);
        strtemplate = strtemplate.replace('%%number_of_kids%%',kids);
        return strtemplate;
    }else{
        return notice;
    }


}

/**
 * returns html template to display on deceaseds list
 * @param {obj} deceased
 * @param {string} bornString
 * @param {string} sonOfString
 * @param {string} husbandWife
 * @param {string} familyString
 * @param {string} occupation
 * @param {string} diedIn
 * @param {string} checkbox_selected
 * @param {string} label_info_page_url
 */
function get_single_deceased_list_template(deceased, bornString, sonOfString, husbandWife, familyString, occupation, diedIn, checkbox_selected, label_info_page_url) {
    bornString = bornString?bornString:'';
    sonOfString = sonOfString?sonOfString:'';
    husbandWife = husbandWife?husbandWife:'';
    familyString = familyString?familyString.replace(/(^,)|(, $)/g, "")+'. ':'';
    occupation = occupation?occupation:'';
    diedIn = diedIn?diedIn:'';

    if (is_rtl()) {
        printString = 'הדפסת תווית';
        add_to_cart_text = "הוספה לרשימה";
        homeUrl = window.location.origin + '/?person_id=';
        moreInfoString = 'למידע נוסף על ' + deceased.first_name;
    } else {
        printString = 'Print Label';
        add_to_cart_text = "Add to list";
        homeUrl = window.location.origin + '/en/?person_id=';
        moreInfoString = 'More About ' + deceased.first_name;
    }
    var fsuff = '';
    if(deceased.gender == 'female'){
        fsuff = '_female';
    }
    var countrycity ='';
    if (deceased.birth_country && deceased.born_city) {
        countrycity = sticker.summary['city_country_sum'+fsuff];
    } else if (deceased.born_city) {
        countrycity = sticker.summary['city_sum'+fsuff];
    } else if (deceased.birth_country) {
        countrycity = sticker.summary['country_sum'+fsuff];
    }
    if(countrycity != '' && (deceased.born_city != '' || deceased.birth_country != '')){
        countrycity = countrycity.replace('%%city%%',deceased.born_city);
        countrycity = countrycity.replace('%%country%%',deceased.birth_country)+' ';
    }
    var occupation_text = occupation;
    occupation_text = occupation_text.replace(/.\s*$/, "");
    if(occupation_text != ''){
        occupation_label = sticker.summary.occupation_sum;
        occupation_text = occupation_label.replace('%%occupation%%',occupation_text)+' ';
    }
    var pod = deceased.place_of_death;
    if(pod != ''){
        //pod = pod.replace('נספה ב', '');
        //pod = pod.replace('נספתה ב', '');
        //pod = pod.replace('Perished in', '');
        pod_label = sticker.summary['pod_sum'+fsuff];
        pod = pod_label.replace('%%place_of_death%%',pod);
    }


    var moreInfo = deceased.more_info ? '<a class="person-more-info" href="' + replace_more_info_link(deceased.more_info) + '" target="_blank"><svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" aria-hidden="true" focusable="false" width="1em" height="1em" style="-ms-transform: rotate(360deg); -webkit-transform: rotate(360deg); transform: rotate(360deg);" preserveAspectRatio="xMidYMid meet" viewBox="0 0 36 36"><path d="M17.6 24.32l-2.46 2.44a4 4 0 0 1-5.62 0a3.92 3.92 0 0 1 0-5.55l4.69-4.65a4 4 0 0 1 5.62 0a3.86 3.86 0 0 1 1 1.71a2 2 0 0 0 .27-.27l1.29-1.28a5.89 5.89 0 0 0-1.15-1.62a6 6 0 0 0-8.44 0l-4.7 4.69a5.91 5.91 0 0 0 0 8.39a6 6 0 0 0 8.44 0l3.65-3.62h-.5a8 8 0 0 1-2.09-.24z" class="clr-i-outline clr-i-outline-path-1" fill="#FBB72C"/><path d="M28.61 7.82a6 6 0 0 0-8.44 0l-3.65 3.62h.49a8 8 0 0 1 2.1.28l2.46-2.44a4 4 0 0 1 5.62 0a3.92 3.92 0 0 1 0 5.55l-4.69 4.65a4 4 0 0 1-5.62 0a3.86 3.86 0 0 1-1-1.71a2 2 0 0 0-.28.23l-1.29 1.28a5.89 5.89 0 0 0 1.15 1.62a6 6 0 0 0 8.44 0l4.69-4.65a5.92 5.92 0 0 0 0-8.39z" class="clr-i-outline clr-i-outline-path-2" fill="#FBB72C"/></svg>' + moreInfoString + '</a>' : '';


    var oldhtml = '<div class="blogroll-type1">' +
        '<div class="item-body">' +
        '<div class="item-title">' +
        '<div class="name">' +
        '<span class="first_name">' + deceased.first_name + '</span> ' +
        '<span class="last_name">' + deceased.last_name + '</span>' +
        '</div> ' +
        '<div class="years"> ' + deceased.year_of_birth + ' - ' + deceased.year_of_death + '</div> ' +
        '</div> ' +
        '<div class="item-content">' + countrycity + '<span class="family">' + sonOfString + '' + husbandWife + '' + familyString + '</span>' +
        '<span class="occupation">' + occupation_text + '</span>' +
        '<span class="diedIn">' + pod + '</span>' +
        '</div>' +
        '</div>' +
        '<div class="links">' +
        '<div class="checkbox-warpper">' +
        '<div class="checkbox js-toggle-wishlist ' + checkbox_selected + '" data-id="' + deceased.ID + '" data-qr_code="' + label_info_page_url + '">' +
        '<span class="icon-checkmark"><span>' +
        '</div>' + add_to_cart_text +
        '</div>' +
        '<a class="print-person print_label_btn" data-person-id="'+deceased.ID+'" data-url="' + label_info_page_url + '&hide-form=1" href="#">' +
        '<svg width="28" height="28" viewBox="0 0 28 28" fill="none" xmlns="http://www.w3.org/2000/svg">' +
        '<path d="M21 19.8332C22.0872 19.8332 22.6308 19.8332 23.0596 19.6556C23.6313 19.4188 24.0854 18.9644 24.3223 18.3927C24.4999 17.9639 24.5 17.4208 24.5 16.3336V11.8964C24.5 10.5922 24.5 9.93907 24.2459 9.44043C24.0222 9.00138 23.6643 8.64469 23.2253 8.42098C22.7568 8.18226 22.1531 8.16762 21 8.16672M21 8.16672C20.9247 8.16667 20.847 8.16667 20.7669 8.16667H7.23356C7.15328 8.16667 7.07547 8.16667 7 8.16673M21 8.16672L7 8.16673M21 8.16672V7.22973C21 5.92549 21 5.2724 20.7459 4.77376C20.5222 4.33472 20.1643 3.97802 19.7253 3.75432C19.2262 3.5 18.5737 3.5 17.2669 3.5H10.7336C9.42677 3.5 8.77289 3.5 8.27376 3.75432C7.83472 3.97802 7.47802 4.33472 7.25432 4.77376C7 5.27289 7 5.92677 7 7.23356V8.16673M7 8.16673C5.84702 8.16763 5.24223 8.18229 4.77376 8.42098C4.33472 8.64469 3.97802 9.00138 3.75432 9.44043C3.5 9.93956 3.5 10.5934 3.5 11.9002V16.3336C3.5 17.4208 3.5 17.9639 3.67761 18.3927C3.91443 18.9644 4.36837 19.4188 4.9401 19.6556C5.3689 19.8332 5.9125 19.8332 6.9997 19.8332M11.6667 12.8333H16.3333M10.5 17.5H17.5C18.5872 17.5 19.1308 17.5 19.5596 17.6776C20.1313 17.9144 20.5854 18.3684 20.8223 18.9401C20.9999 19.3689 20.9999 19.9128 20.9999 21C20.9999 22.0872 20.9999 22.6308 20.8223 23.0596C20.5854 23.6313 20.1313 24.0854 19.5596 24.3223C19.1308 24.4999 18.5872 24.5 17.5 24.5H10.5C9.4128 24.5 8.8689 24.4999 8.4401 24.3223C7.86837 24.0854 7.41443 23.6316 7.17761 23.0599C7 22.6311 7 22.0869 7 20.9997C7 19.9125 7 19.3689 7.17761 18.9401C7.41443 18.3684 7.86837 17.9144 8.4401 17.6776C8.8689 17.5 9.4128 17.5 10.5 17.5Z" stroke="#3C3C3C" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>' +
        '</svg>' + printString +
        '</a>  ' + moreInfo +
        '</div> ' +
        '</div>';

        var yob = deceased.year_of_birth;
        var yod = deceased.year_of_death;
        var age = 0;
        if(yod && yob){
            var age = yod-yob;
        }
        var familystring = build_fstring_dyn('summary',age,deceased.gender,deceased.father_name,deceased.mother_name,deceased.partner_name,deceased.personal_status,deceased.number_of_kids,deceased.notice)+' ';

        var newhtml = '<div class="blogroll-type1">' +
        '<div class="item-body">' +
        '<div class="item-title">' +
        '<div class="name">' +
        '<span class="first_name">' + deceased.first_name + '</span> ' +
        '<span class="last_name">' + deceased.last_name + '</span>' +
        '</div> ' +
        '<div class="years"> ' + deceased.year_of_birth + ' - ' + deceased.year_of_death + '</div> ' +
        '</div> ' +
        '<div class="item-content">' + countrycity + '<span class="family">' + familystring + '</span>' +
        '<span class="occupation">' + occupation_text + '</span>' +
        '<span class="diedIn">' + pod + '</span>' +
        '</div>' +
        '</div>' +
        '<div class="links">' +
        '<div class="checkbox-warpper">' +
        '<div class="checkbox js-toggle-wishlist ' + checkbox_selected + '" data-id="' + deceased.ID + '" data-qr_code="' + label_info_page_url + '">' +
        '<span class="icon-checkmark"><span>' +
        '</div>' + add_to_cart_text +
        '</div>' +
        '<a class="print-person print_label_btn" data-person-id="'+deceased.ID+'" data-url="' + label_info_page_url + '&hide-form=1" href="#">' +
        '<svg width="28" height="28" viewBox="0 0 28 28" fill="none" xmlns="http://www.w3.org/2000/svg">' +
        '<path d="M21 19.8332C22.0872 19.8332 22.6308 19.8332 23.0596 19.6556C23.6313 19.4188 24.0854 18.9644 24.3223 18.3927C24.4999 17.9639 24.5 17.4208 24.5 16.3336V11.8964C24.5 10.5922 24.5 9.93907 24.2459 9.44043C24.0222 9.00138 23.6643 8.64469 23.2253 8.42098C22.7568 8.18226 22.1531 8.16762 21 8.16672M21 8.16672C20.9247 8.16667 20.847 8.16667 20.7669 8.16667H7.23356C7.15328 8.16667 7.07547 8.16667 7 8.16673M21 8.16672L7 8.16673M21 8.16672V7.22973C21 5.92549 21 5.2724 20.7459 4.77376C20.5222 4.33472 20.1643 3.97802 19.7253 3.75432C19.2262 3.5 18.5737 3.5 17.2669 3.5H10.7336C9.42677 3.5 8.77289 3.5 8.27376 3.75432C7.83472 3.97802 7.47802 4.33472 7.25432 4.77376C7 5.27289 7 5.92677 7 7.23356V8.16673M7 8.16673C5.84702 8.16763 5.24223 8.18229 4.77376 8.42098C4.33472 8.64469 3.97802 9.00138 3.75432 9.44043C3.5 9.93956 3.5 10.5934 3.5 11.9002V16.3336C3.5 17.4208 3.5 17.9639 3.67761 18.3927C3.91443 18.9644 4.36837 19.4188 4.9401 19.6556C5.3689 19.8332 5.9125 19.8332 6.9997 19.8332M11.6667 12.8333H16.3333M10.5 17.5H17.5C18.5872 17.5 19.1308 17.5 19.5596 17.6776C20.1313 17.9144 20.5854 18.3684 20.8223 18.9401C20.9999 19.3689 20.9999 19.9128 20.9999 21C20.9999 22.0872 20.9999 22.6308 20.8223 23.0596C20.5854 23.6313 20.1313 24.0854 19.5596 24.3223C19.1308 24.4999 18.5872 24.5 17.5 24.5H10.5C9.4128 24.5 8.8689 24.4999 8.4401 24.3223C7.86837 24.0854 7.41443 23.6316 7.17761 23.0599C7 22.6311 7 22.0869 7 20.9997C7 19.9125 7 19.3689 7.17761 18.9401C7.41443 18.3684 7.86837 17.9144 8.4401 17.6776C8.8689 17.5 9.4128 17.5 10.5 17.5Z" stroke="#3C3C3C" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>' +
        '</svg>' + printString +
        '</a>  ' + moreInfo +
        '</div> ' +
        '</div>';

        if(deceased.ID >= 262194){
            return newhtml;
        }else{
            return oldhtml;
        }
}


/**
 * Main search function
 * @param {array} filters
 * @param {string} search
 * @param {int} pageNumber
 */
function ajax_to_search_d(filters, search, pageNumber) {
    if (search) {
        search = decodeURIComponent(search);
    } else if (!filters) {
        return false;
    }

    var data = {
        action: 'get_plugin_search_deceased',
        string: search,
        search: search,
        filters: filters,
        paged: pageNumber,
        posts_per_page: 10,
        lang: info.lang
    };

    removeAllSelectedCheckbox();
    ajax_search_deceaseds(data);
}

/**
 * search and get html row templates
 * @param {object} data search parameters
 */
function ajax_search_deceaseds(data) {
    $.ajax({
        url: info.ajaxUrl,
        type: 'POST',
        data: data,
        dataType: 'json',
        beforeSend: function beforeSend(xhr) {
            $('.results').addClass('loading');
        },
        success: function success(result) {
            var divResults = $('.results');
            var filter1;
            var filter2;
            var no_r;
            var filters = data.filters;
            var total_results = result.total_results ? result.total_results : 0;
            var search = data.search;
            var randNameButton = $('.random-name');

            if ('undefined' !== typeof filters[0] && filters[0].length) {
                filter1 = filters[0][0];
                filter2 = filters[0][1];
            }

            divResults.html('');

            $('.count_results').html(0);
            $('.tooltip-cart-checkbox').hide();

            show_no_results_add_text();

            if (total_results > 0) {
                $('.search-and-filters-deceaseds main .main-row .results-col .pagination-wrap .wrap_btn').addClass('show');
                $('.count_results').html(total_results);

                show_has_results_add_text();

                $('.tooltip-cart-checkbox').show();

                setTimeout(() => {
                    CalculateSelectedCheckbox();
                }, 1000);

                $('.results-title').addClass('active');
            }

            if ((search != undefined || filter1 && filter2) && (result['data'] == null || result['data'].length == 0)) {

                if (is_rtl()) {
                    no_r = '<div class="no-results">השם שחיפשת לא נמצא במאגר</div>';
                } else {
                    no_r = '<div class="no-results">No Names Found</div>';
                }

                $('.results').removeClass('loading');

                divResults.append(no_r);

                $('.pagination_d').html('');
            } else if ((search == '' || search == null) && (filters == '' || filters == [] || filter1 == '')) {

            } else if (result['data'] != null) {
                var current_paged = result['page_number'];
                var numbers_of_pages = Math.floor(total_results / 10);
                var array_d = result.data;

                if (total_results % 10 != 0) {
                    numbers_of_pages++;
                }

                if (array_d.length) {
                    for (i = 0; i < array_d.length; i++) {

                        var deceased = array_d[i];
                        var single_deceased_row = get_deceased_row_html(deceased);

                        divResults.append(single_deceased_row);
                    }
                }

                $('.results').removeClass('loading');
                $('.num-of-results').html(total_results);
                $('.page-num').html(current_paged);
                $('.total-pages').html(numbers_of_pages);

                totalPages = numbers_of_pages;

                drawPagination_d(current_paged, numbers_of_pages, 7);
            }

            randNameButton.removeClass('disable');

            $('.add-item-wrapper').css('display', 'flex');

            appendPropsToAddItemLink();
        }
    });
}
/**
 * Get the row html template
 * @param {object} deceased
 */
function get_deceased_row_html(deceased) {
    var familyString;
    var label_info_page_url = info.label_info_page.url + '?person_id=' + deceased.ID; // &details=1
    var checkbox_selected = info.wishlist.includes(String(deceased.ID)) ? 'selected' : '';
    var bornString = get_deceased_born_string(deceased);
    var sonOfString = get_deceased_son_of_string(deceased);
    var husbandWife = get_deceased_husband_wife(deceased);
    var diedIn = get_deceased_died_in_string(deceased);
    var diedIn = deceased.place_of_death;
    var occupation = get_deceased_occupation_string(deceased);

    familyString = deceased.family ? deceased.family + ', ' : '';
    var row_template = get_single_deceased_list_template(deceased, bornString, sonOfString, husbandWife, familyString, occupation, diedIn, checkbox_selected, label_info_page_url);

    return row_template;
}

/**
 * Build the born string to display on deceaseds lst
 * @param {object} deceased
 */
function get_deceased_born_string(deceased) {
    var bornString = "";
    var selectedCountry = $('[name=search_birth_country]').val();
    var selectedCity = $('[name=search_birth_city]').val();

    if (deceased.born_city || deceased.birth_country) {
        if (is_rtl()) {
            bornString = "נולד/ה ב" + "<span class='birth_country_city'>" + deceased.born_city + (deceased.born_city && deceased.birth_country ? ', ' : '') + (deceased.birth_country ? deceased.birth_country + '</span>. ' : '</span>. ');
        } else {
            bornString = "Born In " + "<span class='birth_country_city'>" + deceased.born_city + (deceased.born_city && deceased.birth_country ? ', ' : '') + (deceased.birth_country ? deceased.birth_country + '</span>. ' : '</span>. ');
        }
    }

    //make the country bold if searched by
    if (selectedCountry.constructor === Array) {
        if (selectedCountry && selectedCountry.length) {
            $.each(selectedCountry, function (k, v) {
                if (bornString.indexOf(v) > -1) {
                    bornString = bornString.replace(v, '<strong>' + v + '</strong>');
                    return false;
                }
            });
        }
    } else {
        bornString = selectedCountry ? bornString.qsReplaceAll(selectedCountry, '<strong>' + selectedCountry + '</strong>') : bornString;
    }
    //make the city bold if searched by
    bornString = selectedCity ? bornString.qsReplaceAll(selectedCity, '<strong>' + selectedCity + '</strong>') : bornString;

    return bornString;
}

/**
 * Build the son of string to display on deceaseds lst
 * @param {object} deceased
 */
function get_deceased_son_of_string(deceased) {
    var sonOfString = "";

    if (is_rtl()) {
        if (deceased.gender == 'male' && (deceased.father_name || deceased.mother_name)) {
            sonOfString = 'בן ' + (deceased.father_name ? deceased.father_name + (deceased.mother_name ? ' ו' : '') : '') + deceased.mother_name + (deceased.partner_name ? ', ' : '. ');
        } else if (deceased.gender == 'female' && (deceased.father_name || deceased.mother_name)) {
            sonOfString = 'בת ' + (deceased.father_name ? deceased.father_name + (deceased.mother_name ? ' ו' : '') : '') + deceased.mother_name + (deceased.partner_name ? ', ' : '. ');
        }
    } else {
        if (deceased.gender == 'male' && (deceased.father_name || deceased.mother_name)) {
            sonOfString = 'Son of ' + (deceased.father_name ? deceased.father_name + (deceased.mother_name ? ' & ' : '') : '') + deceased.mother_name + (deceased.partner_name ? ', ' : '. ');
        } else if (deceased.gender == 'female' && (deceased.father_name || deceased.mother_name)) {
            sonOfString = 'Daughter of ' + (deceased.father_name ? deceased.father_name + (deceased.mother_name ? ' & ' : '') : '') + deceased.mother_name + (deceased.partner_name ? ', ' : '. ');
        }
    }

    return sonOfString;
}

/**
 * Build the husband/wife string to display on deceaseds lst
 * @param {object} deceased
 */
function get_deceased_husband_wife(deceased) {
    var husbandWife = "";

    if (is_rtl()) {
        if (deceased.gender == 'male' ) {
            if (deceased.personal_status === 'אלמן' && deceased.partner_name) {
                prefix = 'התאלמן מ'
            } else if (deceased.personal_status === 'גרוש') {
                return deceased.personal_status;
            } else if( deceased.partner_name ) {
                prefix = 'נשוי ל'
            }else{
                return deceased.personal_status;
            }
            husbandWife = deceased.partner_name ? prefix + deceased.partner_name + (deceased.occupation ? '. ' : '. ') : '';
        } else if (deceased.gender == 'female' ) {
            if (deceased.personal_status === 'אלמן' && deceased.partner_name) {
                prefix = 'התאלמנה מ'
            } else if (deceased.personal_status === 'גרוש') {
                return 'גרושה';
            } else if( deceased.partner_name ){
                prefix = 'אשת ';
            }else{
                prefix = '';
            }
            husbandWife = deceased.partner_name ? prefix + deceased.partner_name + '. ' : prefix;
        }
    } else if (deceased.gender == 'male' && deceased.partner_name) {
        husbandWife = 'Married to ' + deceased.partner_name + (deceased.occupation ? '. ' : '. ');
    }

    return husbandWife;
}

/**
 * Build the died in string to display on deceaseds lst
 * @param {object} deceased
 */
function get_deceased_died_in_string(deceased) {
    var diedIn = "";
    return deceased.place_of_death;
    if (is_rtl()) {
        if (deceased.gender == 'male' && deceased.place_of_death != "") {
            if (deceased.place_of_death.indexOf('נספה ב') == 0) {
                deceased.place_of_death = deceased.place_of_death.replace('נספה ב', '');
            }

            diedIn = 'נספה ב' + deceased.place_of_death;
        } else if (deceased.gender == 'female' && deceased.place_of_death != '') {
            if (deceased.place_of_death.indexOf('נספתה ב') == 0) {
                deceased.place_of_death = deceased.place_of_death.replace('נספתה ב', '');
            }

            diedIn = 'נספתה ב' + deceased.place_of_death;
        }
    } else {
        if (deceased.gender == 'male' && deceased.place_of_death != '') {
            if (deceased.place_of_death.indexOf('Perished in') == 0) {
                deceased.place_of_death = deceased.place_of_death.replace('Perished in', '');
            }

            diedIn = 'Perished in ' + deceased.place_of_death;
        }
    }

    return diedIn;
}

/**
 * Build the occupation string to display on deceaseds lst
 * @param {object} deceased
 */
function get_deceased_occupation_string(deceased) {
    var occupation = "";
    var selectedOccupations = $('[name=search_qccupation]').val();

    if (deceased.occupation) {
        occupation = deceased.occupation + '. ';
    }
    //make the occupation bold if it was searched by
    if (selectedOccupations.constructor === Array) {
        if (selectedOccupations && selectedOccupations.length) {
            $.each(selectedOccupations, function (k, v) {
                if (occupation.indexOf(v) > -1) {
                    occupation = occupation.replace(v, '<strong>' + v + '</strong>');
                    return false;
                }
            });
        }
    } else {
        occupation = selectedOccupations ? occupation.qsReplaceAll(selectedOccupations, '<strong>' + selectedOccupations + '</strong>') : occupation;
    }
    return occupation;
}

function sectionFooter() { }

function sectionPageTitle() { }

function sectionRichText() { }

function sectionMovies() {
    var sections = $('.section-movies');
    sections.each(function (index, el) {
        var section = $(el);
        var links = section.find('a');
        links.magnificPopup({
            type: 'iframe',
            iframe: {
                markup: '<div class="mfp-iframe-scaler">' +
                    '<div class="mfp-close"></div>' +
                    '<iframe class="mfp-iframe" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>' +
                    '</div>'
            }
        });
    });
}

function sectionGoldPartners() {
    var sections = $('.section-gold-partners');
    sections.each(function (index, el) {
        var section = $(el);
        section.find('.wrap .content').matchHeight();
    });
}

function mark_parent_active($clicked_filter) {
    var $parent_filter = $clicked_filter.parents('li');

    if ($parent_filter.find('.active').length) {
        $parent_filter.addClass('active-search');
    } else {
        $parent_filter.removeClass('active-search');
    }
}

function sectionSilverPartners() { }

function sectionThanks() { }

function sectionQuote() { }

function sectionFaq() {
    var sections = $('.section-faq');
    sections.each(function (index, el) {
        var section = $(el);
        var zones = section.find('.part');
        zones.each(function (index, el) {
            var zone = $(el);
            zone.on('click', '.question', function (event) {
                event.preventDefault();
                /* Act on the event */

                var qustion = $(this).closest('.faq');
                var answer = qustion.find('.answer');
                var icon = qustion.find('.icon >span');
                answer.stop().slideToggle(500);
                qustion.toggleClass('active');
                icon.toggleClass('icon-minus icon-plus');
            });
        });
    });
}

function sectionGallery() {
    var sections = $('.section-gallery');
    sections.each(function (index, el) {
        var section = $(el);

        section.magnificPopup({
            callbacks: {
                elementParse: function (item) {
                    // the class name
                    if (item.el.hasClass('video-link')) {
                        item.type = 'iframe';

                    } else {
                        item.type = 'image';
                    }
                },
                markupParse: function (template, values, item) {
                    // Function will fire for each target element
                    // "item.el" is a target DOM element (if present)
                    // "item.src" is a source that you may modify
                    this.selectedImageIndex = $(item.el).index();
                },
                imageLoadComplete: function () {
                    set_magnific_thumbnails_display(this.selectedImageIndex, this.title);
                }
            },
            delegate: 'a',
            type: 'image',
            gallery: {
                enabled: true
            },
            image: {
                titleSrc: function titleSrc( item) {
                    this.title = item.el.parents('.section-gallery').find('.image-title');
                    var title = item.el.find('img').length ? item.el.find('img').attr('title') : item.el.attr('title');
                    title_html = '<h3>' + title + '</h3>';
                    var thumbnails_html = $('.gallery-wrap').length ? get_gallery_thumbnails(item, this.title) : '';
                    return title_html + thumbnails_html;
                }
            }
        });
    });
}

function set_magnific_thumbnails_display(imageIndex, title) {
    image_width = $('.dots li').first().width();

    offset = imageIndex * image_width;

    $('.dots').css('transform', 'translateX(' + offset + 'px)');
}

function changeSlide(i) {
    $('.gallery').magnificPopup('goTo', i);
}

function get_gallery_thumbnails(item, title) {
    var $gallery = item.el.parents('.section-gallery');

    if ($gallery.find('a').length > 0) {
        $result = '<div class="mfp-pager">' +
            '<div class="mfp-pager-content">' +
            '<div class="mfp-pager-content-inner">' + title.html() + '</div>' +
            '</div>' +
            '<div class="dots-wrap">' +
            '<ul class="dots" style="display: inline-block;">';

        $.each($gallery.find('a'), function (i, v) {
            var $cl_active = item.index == i ? ' class="active"' : '';
            var thumb = $(this).data('thumbnail');
            $result += '<li' + $cl_active + '>' +
                '<button type="button" onclick="javascript:changeSlide(' + i + ');return false;"><img src="' + thumb + '" width="50"></button>' +
                '</li>';
        });

        $result += '</ul></div>' +
            '</div>';
    }
    return $result;
}

function sectionContactForm() { }

function sectionBoxes() { }

function blogrollArticle() { }

function blogrollLesson() { }

function sectionBannerWithButton() { }

function magnificExtension() {
    $.extend(true, $.magnificPopup.defaults, {
        tClose: 'Close (Esc)',
        // Alt text on close button
        tLoading: 'Loading...',
        // Text that is displayed during loading. Can contain %curr% and %total% keys
        gallery: {
            tPrev: 'Previous (Left arrow key)',
            // Alt text on left arrow
            tNext: 'Next (Right arrow key)',
            // Alt text on right arrow
            tCounter: '%curr% מתוך %total%' // Markup for "1 of 7" counter
        },
        image: {
            tError: '<a href="%url%">The image</a> could not be loaded.' // Error message when image could not be loaded

        },
        ajax: {
            tError: '<a href="%url%">The content</a> could not be loaded.' // Error message when ajax request failed

        }
    });
}

function offCanvas() {
    $('[data-off-canvas-button]').click(function (event) {
        /* Act on the event */
        event.preventDefault();
        var position = $(this).attr('data-off-canvas-button');
        $('[data-off-canvas-button="' + position + '"]').toggleClass('active');
        $('#off-canvas-wrapper').toggleClass('canvas-open-' + position);
        $('.mobile_menu_shad').toggleClass('show');
        $('body').toggleClass('overflow_hidden');
    });
}

function headerMobile() { }

function verticalMenu() { }

function sectionContentWithReadMoreCollapse() { }

function deceasedDetails() {
    $(document).on('click', '.open-video', function (event) {
        event.preventDefault();
        /* Act on the event */

        var movieUrl = $(this).attr('href');
        $.magnificPopup.open({
            items: {
                src: movieUrl
            },
            type: 'iframe'
        });
    });
}

function generalPopup() {
    // open main popup
    if ($('#main-general-popup').length) {
        setTimeout(function () {
            $.magnificPopup.open({
                items: {
                    src: '#main-general-popup',
                    type: 'inline'
                },
                callbacks: {
                    close: function () {
                    }
                }
            });
        }, 4000);
    }

    // Dont display popup checkbox on checked
    $(document).on('change', "#dont-display-checkbox", function () {
        if (this.checked) {
            $.ajax({
                url: info.ajaxUrl,
                type: 'POST',
                dataType: 'json',
                data: {
                    action: 'dont_display_popup_session',
                    hide_popup_session: true,
                }
            }).done(function (res) {
                $.magnificPopup.close()
            });
        }
    });
}

function donationPopup() {
    $('.popup_donation_pop').magnificPopup({
        type: 'inline',
        preloader: false,
    });
}

function initSelect2() {
    if ($("#occupationMulty:not(.select2-hidden-accessible)").length) {
        var select2 = $("#occupationMulty").select2({
            tags: $("#occupationMulty").hasClass('no-tags') ? false : true,
            placeholder: $("#occupationMulty").attr('data-placeholder') ? $("#occupationMulty").attr('data-placeholder') : '',
            maximumSelectionLength: 2,
            allowClear: true,
            createTag: function (params) {
                var term = jQuery.trim(params.term);

                if (term === '') {
                    return null;
                }

                return {
                    id: term,
                    text: term,
                }
            },
            dir: "rtl",
            tokenSeparators: [',']

        });
    }

    if ($("#search_birth_country:not(.select2-hidden-accessible)").length) {
        $("#search_birth_country").select2({
            placeholder: $("#search_birth_country").attr('data-placeholder') ? $("#search_birth_country").attr('data-placeholder') : '',
            allowClear: true,
            dir: "rtl",
        });
    }
    getRidOffAutocomplete();
}

function replace_more_info_link(more_info) {
    if (isNaN(more_info)) {
        return more_info;
    }
    url = new URL(info.yadvashem_link);
    url.searchParams.append('itemId', more_info);
    return url;
}

function replace_more_info_link2(more_info) {
    var yadvashem = info.yadvashem_link;
    var split = more_info.split('?');

    if (split[0] === yadvashem || split[1]) {
        return yadvashem + '?' + split[1];
    } else {
        return more_info;
    }
}

function occupation_array_to_string(occupation_array) {

    var new_occupation = '';
    occupation_array.forEach((item, i) => {
        if (i === (occupation_array.length - 1)) {
            new_occupation += item.id;
        } else if (item.id === occupation_array[1].id) {
            new_occupation += ', ' + item.id;

        } else {
            new_occupation += item.id + ', ';

        }
    });
    return new_occupation;
}

function removeAllSelectedCheckbox() {
    jQuery('.js-toggle-bulk-wishlist').removeClass('selected');
}

function CalculateSelectedCheckbox() {
    let total = jQuery('.checkbox.js-toggle-wishlist').length;
    jQuery('.checkbox.js-toggle-wishlist.selected').each(function (i, v) {
        total = total - 1;
    });
    if (0 === total) {
        jQuery('.js-toggle-bulk-wishlist').addClass('selected');
    }
}

String.prototype.qsReplaceAll = function (strReplace, strWith) {
    // See http://stackoverflow.com/a/3561711/556609
    var esc = strReplace.replace(/[-\/\\^$*+?.()|[\]{}]/g, '\\$&');
    var reg = new RegExp(esc, 'ig');
    return this.replace(reg, strWith);
};

function getUrlParameter(sParam) {
    var sPageURL = window.location.search.substring(1);
    var sURLVariables = sPageURL.split('&');

    for (var i = 0; i < sURLVariables.length; i++) {
        var sParameterName = sURLVariables[i].split('=');

        if (sParameterName[0] == sParam) {
            return sParameterName[1];
        }
    }
}

function isEmail(email) {
    var regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
    return regex.test(email);
}

function selectPlaceholders() {
    jQuery('select.non-active').on('change', function () {
        if (jQuery(this).val()) {
            jQuery(this).removeClass('non-active')
        } else {
            jQuery(this).addClass('non-active')
        }

    });
}

function handleBannerVideo() {
    if (jQuery('#youtube-player').length > 0) {
        var tag = document.createElement('script');
        tag.src = "https://www.youtube.com/iframe_api";
        var firstScriptTag = document.getElementsByTagName('script')[0];
        firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);
    }
    if (jQuery('#video-player').length > 0) {
        player = document.getElementById("video-player");
    }
}

function onYouTubeIframeAPIReady() {
    player = new YT.Player('youtube-player', {
        height: '100%',
        width: '100%',
        videoId: info.youtube_banner_id,
        playerVars: {
            autoplay: 1,
            controls: 0,
            disablekb: 1,
            fs: 0,
            loop: 1,
            modestbranding: 1,
            rel: 0,
            showinfo: 0,
            mute: 1,
            autohide: 1
        },
        events: {
            'onReady': onPlayerReady,
            //'onStateChange': onPlayerStateChange
        }
    });
}

function onPlayerReady(event) {
    event.target.mute();
    event.target.playVideo();
    videoHandleButtons('play');
}

function onPlayerStateChange(event) {
    if (event.data == YT.PlayerState.PLAYING && !done) {
        setTimeout(stopVideo, 6000);
        done = true;
    }
}

function stopVideo() {
    videoHandleButtons('pause');
    if (jQuery('#youtube-player').length > 0) {
        player.stopVideo();
    } else {
        player.pause();
    }
}

function startVideo() {
    videoHandleButtons('play');
    if (jQuery('#youtube-player').length > 0) {
        player.startVideo();
    } else {
        player.play();
    }
}
function pauseVideo() {
    videoHandleButtons('pause');
    if (jQuery('#youtube-player').length > 0) {
        player.pauseVideo();
    } else {
        player.pause();
    }
}
function videoHandleButtons(action) {
    let $wrapperButtons = jQuery('.video-control');
    if ('pause' === action) {
        $wrapperButtons.find('.pause').removeClass('show');
        $wrapperButtons.find('.play').addClass('show');
    } else {
        $wrapperButtons.find('.play').removeClass('show');
        $wrapperButtons.find('.pause').addClass('show');
    }
}
function VideoHandleButtons(action) {
    let $wrapperButtons = jQuery('.video-control');
    if ('pause' === action) {
        $wrapperButtons.find('.pause').removeClass('show');
        $wrapperButtons.find('.play').addClass('show');
    } else {
        $wrapperButtons.find('.play').removeClass('show');
        $wrapperButtons.find('.pause').addClass('show');
    }
}

function initLogoSlider() {
    if( jQuery('.logo-list').length) {
        jQuery('.logo-list').slick({
            dots: false,
            infinite: true,
            speed: 500,
            autoplay: true,
            autoplaySpeed: 2000,
            slidesToScroll: 1,
            slidesToShow: 4,
            arrows: false,
            rtl: !!info.rtl,
            responsive: [
                {
                    breakpoint: 1300,
                    settings: {
                        slidesToShow: 4,
                    }
                },
                {
                    breakpoint: 600,
                    settings: {
                        slidesToShow: 2
                    }
                },
                {
                    breakpoint: 480,
                    settings: {
                        slidesToShow: 1,
                        autoplaySpeed: 3000,
                    }
                }
            ]
        });
    }
}
function initStipSlider() {
    if( jQuery('.js-strip-slider').length) {
        jQuery('.js-strip-slider').slick({
            dots: false,
            infinite: true,
            speed: 500,
            autoplay: false,
            autoplaySpeed: 2000,
            slidesToShow: jQuery('.js-strip-slider').data('stripslidescount'),
            slidesToScroll: 1,
            rtl: !!info.rtl,
            responsive: [
                {
                    breakpoint: 1300,
                    settings: {
                        slidesToShow: 4,
                    }
                },
                {
                    breakpoint: 1100,
                    settings: {
                        slidesToShow: 3,
                    }
                },
                {
                    breakpoint: 600,
                    settings: {
                        slidesToShow: 2
                    }
                },
                {
                    breakpoint: 480,
                    settings: {
                        slidesToShow: 1,
                        autoplaySpeed: 3000,
                    }
                }
            ]
        });
    }
}

function blocks_wrapper_slider_mob() {
    if( jQuery('.blocks-wrapper-slider_mob').length) {
        jQuery('.blocks-wrapper-slider_mob').slick({
            dots: false,
            infinite: true,
            speed: 300,
            autoplay: false,
            autoplaySpeed: 5000,
            slidesToShow: 1,
            slidesToScroll: 1,
            rtl: !!info.rtl,
            arrows: false
        });
    }
}

function initSectionContentSlider() {
    if( jQuery('.js-section-content-slider').length) {
        jQuery('.js-section-content-slider').slick({
            dots: false,
            speed: 300,
            autoplay: false,
            autoplaySpeed: 3000,
            slidesToShow: 1,
            rtl: !!info.rtl,
            slidesToScroll: 1
        });
        jQuery('.js-section-content-slider').on('afterChange', function (event, slick, currentSlide) {
            let slider_content = jQuery('.js-section-content-slider .slide-item[data-slide-index="' + currentSlide + '"] .content-area').html();
            jQuery('.section-container-slider > .content-area').html(slider_content);
        });
    }
}

function handleUpload() {
    jQuery(document).on('click', '.trigger_upload', function(e) {
        e.preventDefault();
        jQuery('.attach_wrap').find('.apply_file').click();
    });

    jQuery('.apply_file').on('change', function() {
        var thisElement = jQuery(this);
        if (thisElement.attr('type') === 'file') {
            if (thisElement.val() !== '') {
                jQuery('.filename_place').html(thisElement[0].files[0].name).addClass('has-content');
                jQuery('.filename_place').append('<a href="#" class="remove_file"></a>');
                jQuery('.filename_place').addClass('show');
            } else {
                jQuery('.remove_file').remove();
            }
        }
    });

    jQuery(document).on('click', '.remove_file', function (e) {
        e.preventDefault();
        jQuery(".apply_file ").val("");
        jQuery('.filename_place').html("");
        jQuery('.remove_file').remove();
        jQuery('.filename_place').removeClass('show');
    });
}

jQuery(document).on('click', '.js-toggle-mini-cart', function (e) {
    e.preventDefault();
    toggleMinicart();
});

function toggleMinicart() {
    if (jQuery('.header-mini-cart').hasClass('active')) {
        closeMiniCart();
        destroy_js_panel();
    } else {
        jQuery('.header-mini-cart').addClass('active').find('.mini-cart-wrapper').slideDown(250);
        create_js_panel();
    }
}

function closeMiniCart() {
    jQuery('.header-mini-cart').removeClass('active').find('.mini-cart-wrapper').slideUp(250);
}

function handleFilter() {
    const urlParams = new URLSearchParams(window.location.search);

    jQuery('.tags-selector_d>li .tags-wrap, .tags-selector>li .tags-wrap').slideToggle(250);

    jQuery(document).on('click', '.js-toggle-filter-dropdown', function (e) {
        jQuery(this).parent().find('.tags-wrap').slideToggle(250);
    });

    if (urlParams.get('random')) {
        jQuery('.random-name').trigger('click');
    }
}

function handleFixedHeader() {
    if( jQuery('body').hasClass('fixed-header')) {
        window.addEventListener('scroll', () => {
            let scroll = this.scrollY;
            const $sectionHeader = jQuery('.section-header');
            if (scroll > 100) {
                $sectionHeader.addClass('scroll');
            } else {
                $sectionHeader.removeClass('scroll');
            }
        });
    }
}

/**
 * Handle Wishlist functionality
 */
function handleWishlist() {
    jQuery(document).on('click', '.js-toggle-wishlist', function (e) {
        let post_id = jQuery(this).attr('data-id');
        let $wrapper = jQuery(this);
        appendBlockLoader($wrapper, true);
        despatchAjaxWishlistAction(post_id, $wrapper);
    });

    jQuery(document).on('click', '.js-toggle-bulk-wishlist', function (e) {
        e.preventDefault();

        let $wrapper = jQuery(this);
        let $checkboxs;

        appendBlockLoader($wrapper, true);

        if (jQuery(this).hasClass('selected')) {
            $checkboxs = jQuery('.checkbox.js-toggle-wishlist.selected');
        } else {
            $checkboxs = jQuery('.checkbox.js-toggle-wishlist:not(.selected)');
        }

        let count = $checkboxs.length - 1;

        $checkboxs.each(function (i, v) {
            let id = jQuery(this).attr('data-id');
            if (count == i) { // on last trigger ajax call
                despatchAjaxWishlistAction(id, $wrapper);
            } else {
                handleJSWishlistActions(id);
            }
            jQuery('.checkbox.js-toggle-wishlist[data-id=' + id + ']').toggleClass('selected');
        });
    });

    jQuery(document).on('click', '.js-reset-mini-cart', function (e) {
        let $wrapper = jQuery(this);
        despatchAjaxWishlistAction('reset', $wrapper);
        toggleMinicart();
    });
}

function destroy_js_panel() {
    var panel = jQuery('.mini-cart-list').jScrollPane();
    var api = panel.data('jsp');

    api.destroy();
}

function create_js_panel() {
    jQuery('.mini-cart-list').jScrollPane()
}

function despatchAjaxWishlistAction(post_id, $wrapper) {
    let data = {
        'action': 'despatchWishlistAction',
        'post_id': post_id,
        'update': handleJSWishlistActions(post_id),
        'lang': info.lang,
    };
    removeAllSelectedCheckbox();
    jQuery.ajax({
        url: info.ajaxUrl,
        data: data,
        cache: false,
        type: 'POST',
        success: function (response) {
            unBlockLoader($wrapper);

            if ($wrapper.hasClass('delete')) {
                destroy_js_panel();
            }

            let notify_status = "warn";

            if (response.fragments) {
                QSUpdateFragments(response.fragments);
            }
            if (response.success) {
                notify_status = 'success';
            }
            if (response.full_list) {
                info.wishlist_full = response.full_list;
            }
            if (response.notify) {
                jQuery.notify(response.notify, {
                    globalPosition: 'bottom right',
                    className: notify_status
                });
            }

            if ($wrapper.hasClass('delete')) {
                jQuery('.js-toggle-wishlist[data-id=' + post_id + ']').removeClass('selected');
                create_js_panel();
            }

            setTimeout(() => {
                CalculateSelectedCheckbox();
            }, 1000);
        }
    });
}

function handleJSWishlistActions(post_id) {
    let result;
    let action_type = '';
    const exists = info.wishlist.includes(post_id);
    if ('reset' === post_id) {
        action_type = 'reset';
        info.wishlist = [];
    } else {
        if (exists) {
            info.wishlist = info.wishlist.filter((item) => { return item !== post_id })
            action_type = 'remove';
        } else {
            result = info.wishlist.push(post_id)
            action_type = 'add';
        }
    }
    return {
        "type": action_type,
        "current_wishlist": info.wishlist
    }
}

/**
 * Handle Fragments updates on the dom - usually triggered after ajax call
 */
function QSUpdateFragments(fragments) {
    for (const [key, value] of Object.entries(fragments)) {
        if (typeof value === 'object') {
            for (const [method, value2] of Object.entries(value)) {
                if (method == 'toggleClass') {
                    jQuery(key).toggleClass(value2);
                } else if (method == 'addClass') {
                    jQuery(key).addClass(value2);
                } else if (method == 'removeClass') {
                    jQuery(key).removeClass(value2);
                } else if (method == 'html') {
                    jQuery(key).html(value2);
                }
            }
        } else {
            jQuery(key).html(value);
        }
    }
}

/**
 * Retrieve global loader template
 * @return {string} - the loader
 */
function getLoaderTemplate() {
    return '<div class="qs-loader-wrapper"><div class="loader-icon"></div></div>'
}

/**
 * Append loader inside an Element
 * @param {Dom_Element} thisElement - where to append the loader
 * @param boolean absolute - whether to append the loader as an absolute or relative
 */
function appendBlockLoader(thisElement, absolute = false) {
    let $loaderElement = jQuery(getLoaderTemplate());
    if (absolute) {
        $loaderElement.addClass('absolute');
    }
    if (typeof thisElement == 'string') {
        thisElement = jQuery(thisElement);
    }

    if (thisElement.find('.qs-loader-wrapper').length <= 0) {
        thisElement.append($loaderElement);
    }
}
/**
 * Remove the exist loader
 * @param {Dom_Element} thisElement - from where to delete the loader
 */
function unBlockLoader(thisElement) {
    if (typeof thisElement == 'string') {
        thisElement = jQuery(thisElement);
    }
    var $loader = thisElement.find('.qs-loader-wrapper');
    $loader.fadeOut(250, function (e) {
        $loader.remove();
    });
}

/**
 * Check if in mobile screen by screen size
 */
function is_mobile() {
    return window_width < 767;
}

function show_desceased_form_loader() {

}

function show_desceased_form_loader() {

}

function appendPropToInput() {
    if (jQuery('body').hasClass('js-append-prop-to-input')) {
        const $inpustWrapper = jQuery('.pre-inputs');
        if ($inpustWrapper.length > 0) {
            let $first_name = $inpustWrapper.find('input[name="firstname"]');
            let $last_name = $inpustWrapper.find('input[name="lastname"]');

            $first_name.val($first_name.attr('data-get-value')).trigger('keyup');
            $last_name.val($last_name.attr('data-get-value')).trigger('keyup');
        }
    }
}

function appendPropsToAddItemLink() {
    const urlParams = new URLSearchParams(window.location.search);

    let first_name = urlParams.get('search_first_name');
    let last_name = urlParams.get('search_family_name');
    let $item_link = jQuery('.js-add-item-wrapper');
    let base_url = $item_link.attr('data-url').indexOf('?') ? $item_link.attr('data-url') + '&' : $item_link.attr('data-url') + '?';
    let url_props = '';

    if (first_name) {
        base_url += 'first_name=' + first_name
    }
    if (last_name) {
        if (first_name) {
            base_url += '&';
        }
        base_url += 'last_name=' + last_name
    }

    $item_link.attr('href', base_url);

}

function getRidOffAutocomplete() {
    var timer = window.setTimeout(function () {
        jQuery('.select2-search__field').prop('autocomplete', 'chrome-off');
        clearTimeout(timer);
    }, 800);
}

function init_home_about_sec_slider() {
    if( jQuery('.home_about_sec_slider').length) {
        jQuery('.home_about_sec_slider').slick({
            dots: false,
            infinite: true,
            speed: 300,
            autoplay: false,
            autoplaySpeed: 5000,
            slidesToShow: 1,
            slidesToScroll: 1,
            rtl: !!info.rtl,
            arrows: true
        });
    }
}

function is_rtl() {
    return $('body').hasClass('rtl');
}
