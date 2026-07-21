var add_name_content;

function is_person_view() {
    return window.location.href.indexOf("person_id");
}

function addAnotherRecordButton() {
    $('.js-add-new-name').on('click', function (e) {
        e.preventDefault();
        $.magnificPopup.close();
        $("#age-input").prop("checked", false);
        resetStickerForm();
        $('textarea#notes-input').val('');
    });
}

function resetStickerForm() {
    $('.pre-inputs-wrapper input,.create-deceased input,.create-deceased select').val('');
    $('.create-deceased input,.create-deceased select').addClass('non-active');
    $('.add_name_database select').val(null).trigger('change.select2'); // clear the select values
    var $canvas = $('.canvas-wrap canvas');
    clear_sticker_canvas($canvas);
}

var print_flag = false;
function printCanvas2( $myCanvas) {
    if( print_flag === false) {
        var dataUrl = $myCanvas.getCanvasImage();
        print_flag = true;

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
        if( printWin) {
            printWin.document.open();
            printWin.document.write( windowContent);
            printWin.document.close();

            setTimeout( function() {
                printWin.focus();
                printWin.print();
                printWin.close();
            }, 250);
        }
    }
}

function sectionCreateLabelForm() {
    var section = $('.section-create-label-form-main-wrapper');
    var url = window.location.href.split('?')[0];
    var address = url;
    var urlPath = window.location.href.split('?')[1];

    section.each(function (index, el) {
        var section = $(el);
        var preForm = section.find('.create-deceased');
        var preFormInputs = preForm.find('input,select');

        if (window.location.href.indexOf("first_empty") == -1 && window.location.href.indexOf("last_empty") == -1) {
            if (is_person_view() == -1 && window.location.href.indexOf("&t=") == -1) {
                preFormInputs.val('');
            }
        }

        var infoCols = section.find('.col-info .info');

        section.find('.fields-wrap label >.name, .equal').matchHeight();

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

        infoCols.on('mouseenter focus', 'a.open', function (event) {
            event.preventDefault();
            /* Act on the event */

            var infoWrap = $(this).closest('.info');
            var infoBox = infoWrap.find('.content');
            infoBox.toggleClass('active current');
            section.find('.info .content.active:not(.current)').removeClass('active');
            infoBox.toggleClass('current');
        });

        infoCols.on('click', 'a.open', function (event) {
            event.preventDefault();
        });

        infoCols.on('focusout mouseleave', 'a.open', function (event) {
            event.preventDefault();
            /* Act on the event */

            var infoWrap = $(this).closest('.info');
            var infoBox = infoWrap.find('.content');
            infoBox.removeClass('active current');
        });

        init_occupation_select(section);
        init_country_select(section);
        init_sticker_canvas(section);

        section.on('keyup change', '.input', function (event) {
            update_sticker_canvas(section, event);
        });

        section.on('change', '[name=birth_country]', function (event) {
            update_sticker_canvas(section, event);
        });

        section.on('click', '.open-popup-link', function (event) {
            event.preventDefault();

            if (!$('[name="accept_terms"]').is(':checked')) {
                $('[name="accept_terms"]').addClass('error');
                return;
            } else {
                $('[name="accept_terms"]').removeClass('error')
            }

            var genderSelect = $('[name="gender"]').val();

            if (genderSelect == 'male' || genderSelect == 'female') {
                $('.create-deceased').removeClass('gender-not-valid');
                $('.section-create-label-form').removeClass('show-gender-req-icon');
            } else {
                $('.create-deceased').addClass('gender-not-valid');
                $('.section-create-label-form').addClass('show-gender-req-icon');
            }
            if ($('.create-deceased').hasClass('birth-not-valid')) {
                var aTag = $('#dates-validation');
                $('html,body').animate({
                    scrollTop: aTag.offset().top - 150
                }, 'slow');
            }

            if ($('.create-deceased').hasClass('gender-not-valid')) {
                var aTag = $('#gender-validation');
                $('html,body').animate({
                    scrollTop: aTag.offset().top - 250
                }, 'slow');
            }

            if ($('.create-deceased').hasClass('gender-not-valid') || $('.create-deceased').hasClass('birth-not-valid')) {
                //
            } else {
                var person = get_person_details(section);
                var infoData = save_user_new_details(section, person);
            }
        });

        section.on('blur', '[name=more_info]', function () {
            var $element = jQuery(this);

            $element.removeClass('err');

            if ($element.val() && !isUrlValid($element.val())) {
                var message = $('body').hasClass('rtl') ? 'הקישור אינו תקין' : 'The link is inccorect';

                $element.addClass('err');

                $element.after("<span class='error-message'>" + message + "</span>");
                disable_action_buttons();
            } else {

                $('.error-message').remove();

                enable_action_buttons();
            }
        });

        section.on('click', '.open-popup-print', function (event) {
            event.preventDefault();
            var genderSelect = $('[name="gender"]').val();

            if (genderSelect == 'male' || genderSelect == 'female') {
                $('.create-deceased').removeClass('gender-not-valid');
                $('.section-create-label-form').removeClass('show-gender-req-icon');
            } else {
                $('.create-deceased').addClass('gender-not-valid');
                $('.section-create-label-form').addClass('show-gender-req-icon');
            }

            if ($('.create-deceased').hasClass('birth-not-valid')) {
                var aTag = $('#dates-validation');
                $('html,body').animate({
                    scrollTop: aTag.offset().top - 150
                }, 'slow');
            }

            if ($('.create-deceased').hasClass('gender-not-valid')) {
                var aTag = $('#gender-validation');
                $('html,body').animate({
                    scrollTop: aTag.offset().top - 150
                }, 'slow');
            }

            if ($('.create-deceased').hasClass('gender-not-valid') || $('.create-deceased').hasClass('birth-not-valid')) {
                //
            } else {
                setTimeout(function () {
                    event.preventDefault();

                    if (is_person_view() > 0) {
                        printCanvas($myCanvas);
                    } else {
                        $.magnificPopup.open({
                            items: {
                                src: '#pop-print',
                                type: 'inline'
                            }
                        });
                    }
                }, 0);
            }
        });

        $('body').on('click', '.print-and-save', function (event) {
            $('.open-popup-link').first().click();
            event.preventDefault();
        });

        $('body').on('click', '.fb-share-btn', function (event) {
            event.preventDefault();
            var site_url = $(this).attr('data-href');
            var deceased_ID = $('#create-deceased').attr('data-deceased-id');
            var $myCanvas = section.find('canvas');
            var dataUrl = $myCanvas.getCanvasImage(); //attempt to save base64 string to server using this var

            if (deceased_ID.length && dataUrl) {
                $('div.loader').fadeIn();
                $.ajax({
                    url: info.ajaxUrl,
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        deceased_ID: deceased_ID,
                        action: 'upload_label_image_from_canvas',
                        label: dataUrl,

                    }
                }).done(function (res) {
                    $('div.loader').fadeOut();

                    if (res.success) {
                        window.open('https://www.facebook.com/sharer/sharer.php?u=' + site_url + '/?person_id=' + deceased_ID);
                    }
                });
            }

            // shareCanvasFB(dataUrl, site_url);

        });

        $('body:not(.page-template-tpl-yadvashem) .print-button').on('click', function (event) {
            event.preventDefault();
            var $myCanvas = section.find('canvas');
            /* Act on the event */
            printCanvas($myCanvas);
        });

        $('body.page-template-tpl-yadvashem .print-button').on('click', function(e) {
            e.preventDefault();
            var $myCanvas = section.find('canvas');
            printCanvas2( $myCanvas);
        });

        $('.save-button').on('click', function (event) {
            event.preventDefault();
            /* Act on the event */
            var section = $('.section-create-label-form-main-wrapper');

            var person = get_person_details(section);

            appendBlockLoader(jQuery('.form-popup'), true);

            save_user_new_details(section, person);
        });

        $('.close-button').on('click', function (event) {
            event.preventDefault();
            /* Act on the event */

            $.magnificPopup.close();
        });

        $('.share-button').on('click', function (event) {
            event.preventDefault();
            /* Act on the event */
            // sortAddthisLink();

            $.magnificPopup.open({
                items: {
                    src: '#share-popup',
                    type: 'inline'
                }
            });
        });

        $('.addthis_button_email_new').on('click', function (event) {// event.preventDefault();
        });

        jQuery(document).on('click', '.js-show-share-popup', function (e) {
            e.preventDefault();
            jQuery.magnificPopup.open({
                items: {
                    src: '#share-popup',
                    type: 'inline'
                },
                callbacks: {
                    open: function () {
                        $.magnificPopup.instance.close = function () {
                            window.location.assign(jQuery('#pop-success .js-show-share-popup').attr('href'));
                        }
                    },
                    close: function () {
                        window.location.assign(jQuery('#pop-success .js-show-share-popup').attr('href'));
                    }
                }
            });
        });

        $('[name="year_of_death"], [name="year_of_birth"]').on('focusout', function (event) {
            var birthDate = $('[name="year_of_birth"]');
            var deathDate = $('[name="year_of_death"]');

            if (birthDate.val() && deathDate.val()) {
                var birthDateVal = birthDate.val();
                var deathDateVal = deathDate.val();

                if ($.isNumeric(birthDateVal) && $.isNumeric(deathDateVal)) {
                    if (birthDateVal > deathDateVal) {
                        $('#dates-validation').show();
                        $('.create-deceased').addClass('birth-not-valid');
                    } else {
                        $('#dates-validation').hide();
                        $('.create-deceased').removeClass('birth-not-valid');
                    }
                } else {
                    $('.create-deceased').removeClass('birth-not-valid');
                    $('#dates-validation').hide();
                }
            }
        }); ////////////////////// Family popup and variables

        var familyInputTrigger = section.find('#family-input-trigger');
        var familyPopup = section.find('.family-popup');
        var closeFamilyPopup = section.find('.close-popup-form');
        var continueButton = section.find('.continue-family'); ////////////////////// Open family popup
        var data = [];

        familyInputTrigger.on('click', function () {
            var stat = $('#personal_status').val();

            $('.input').each(function(){
                data[$(this).attr('name')] = $(this).val();
            });
            if(data['family_details'] == ''){
                $('.adult').removeClass('validateps').hide();
                $('.partner').hide();
                $('.kids').hide();
                $('#personal_status').val('');
                $('#kids-input').val('');
            }

            var yob = data['year_of_birth'];
            var yod = data['year_of_death'];
            if($('.ageinput input').is(":checked")){
                $('.adult').addClass('validateps').show();
                $('.kids').show();
            }
            if(yod && yob){
                var age = yod-yob;
                $('.ageinput').hide();
                if(age >16){
                    $('.adult').addClass('validateps').show();
                    $('.kids').show();
                    $('.ageinput').hide();
                }else{
                    $('.adult').removeClass('validateps').hide();
                    $('.kids').hide();
                    $('.ageinput').hide();
                }
            }else{
                if(yob){
                    if(yob >= 1930){
                        $('.ageinput').hide();
                    }else{
                        $('.ageinput').show();
                    }
                }else{
                    $('.ageinput').show();
                }
            }
            if(data['gender'] == ''){
                $('.continue-family').hide();
                $('.notes-attention').hide();
                $('.gendererrmessage').show();
            }else{
                $('.continue-family').show();
                $('.notes-attention').show();
                $('.gendererrmessage').hide();
            }
            familyPopup.addClass('open');
            $('body.fixed-header').addClass('formopen');

        }); ////////////////////// Close family popup
        $('#age-input').on('change',function(){
            if(this.checked) {
                $('.adult').show();
                $('.kids').show();
            }else{
                $('.adult').hide();
                $('.kids').hide();
                $('#personal_status').val('');
                $('#kids-input').val('');
                $('#partner-name-input').val('');
            }
        });

        $('#personal_status').on('change',function(){
            var stat = $(this).val();
            $('.partner').hide();
            $('.kids').hide();
            if(stat != 'רווק'){
                $('.kids').show();
            }
             if(stat == 'נשוי' || stat == 'אלמן'){
                $('.partner').show();
            }
        });
        closeFamilyPopup.on('click', function (e) {
            familyPopup.removeClass('open');
            $('body.formopen').removeClass('formopen');
        }); ///////////////////////Gender trigger family popup click

        $(window).on('load', function() {
            var status = $('#record-status').val();
            var fatherName = $('#father-name-input').val();
            var motherName = $('#mother-name-input').val();
            var partnerName = $('#partner-name-input').val();
            var kids = $('#kids-input').val();
            var notes = $('#notes-input').val();
            var $myCanvas = section.find('canvas');

            if( notes) {
                notes = $.trim( notes);
                notes = notes.replace(/\.$/, "");
            }

            var family_details = section.find('[name="family_details"]').val();
            if(( ! fatherName && ! motherName && ! partnerName && ! kids) || ! $('#family-input-trigger').val().length) {
                if( notes) {
                    $('#family-input-trigger').val("" + notes + "");
                    $('#notes-input').val("" + notes + "");
                }

                var family_text = ( family_details) ? family_details : notes;
                if( family_text) {
                    $myCanvas.setLayer('family_details', {
                        text: family_text
                    }).drawLayers();
                }
            }
 
            if( section.find('[name="year_of_birth"]').length && section.find('[name="year_of_birth"]').val()
                && section.find('[name="year_of_death"]').length && section.find('[name="year_of_death"]').val()) {
                $myCanvas.setLayer('year_of_birth', {
                    text: section.find('[name="year_of_birth"]').val()
                }).drawLayers();

                $myCanvas.setLayer('year_of_death', {
                    text: section.find('[name="year_of_death"]').val()
                }).drawLayers();
            }

            if( $('[name="born_city"]').length && $('[name="birth_country"]').val()) {
                $myCanvas.setLayer('birth_country_city', {
                    text: section.find('[name="born_city"]').val() + ', ' + section.find('[name="birth_country"]').val()
                }).drawLayers();
            }

            if( $('[name="first_name"]').length && $('[name="last_name"]').val()) {
                if( $('[name="first_name"]').val().length > 1 || $('[name="last_name"]').val().length > 1) {
                    $('.continue').trigger('click');
                    $('.continue-family').trigger('click');
                }
            }

            if( is_person_view() && 'publish' !== status) {
                add_not_approved_popup( $myCanvas);
            }
        });

        //on family members update
        continueButton.on('click', function (event) {
            event.preventDefault();
            $('input#kids-input').removeClass('err');

            var gender = get_selected_gender();
            var fatherName = get_father_name();
            var motherName = get_mother_name();
            var partnerName = get_partner_name();
            var personal_status = get_personal_status();
            var kids = get_number_of_kids();
            var sonOf = get_son_of_text(gender, fatherName, motherName);
            var marriedTo = get_married_to_text(gender, partnerName, personal_status);
            var fatherTo = get_parent_to(gender, kids);
            var notice = get_notice_value();

            if (kids < 0 || kids > 20) {
                $('input#kids-input').addClass('err');
                return false;
            }
            if(personal_status == '' && $('.adult').hasClass('validateps')){
                $('select#personal_status').addClass('err');
                return false;
            }

            if (gender == 'male' || gender == 'female') {
                $('.create-deceased').removeClass('gender-not-valid');
                $('.section-create-label-form').removeClass('show-gender-req-icon');
            } else {
                $('.create-deceased').addClass('gender-not-valid');
                $('.section-create-label-form').addClass('show-gender-req-icon');
            }

            if (!fatherName && !motherName && !partnerName && !kids && $('[name="family"]').val()) {
                family = $('[name="family"]').val() ? $('[name="family"]').val() : notice;
                familyInputTrigger.val( family);
                $('#sonOf,#marriedTofatherTo').val('').trigger('change');

            } else {
                var sep = marriedTo && fatherTo ? ', ' : ' ';
                //family = build_family_string(sonOf, marriedTo, fatherTo, notice);
                $('.input').each(function(){
                    data[$(this).attr('name')] = $(this).val();
                });
                var yob = data['year_of_birth'];
                var yod = data['year_of_death'];
                var age = 0;
                if(yod && yob){
                    var age = yod-yob;
                }

                family = build_fstring_dyn('summary',age,gender,fatherName,motherName,partnerName,personal_status,kids,notice);
                familylab = build_fstring_dyn('label',age,gender,fatherName,motherName,partnerName,personal_status,kids,notice);

                var myArray = familylab.split("##lb##");
                familyInputTrigger.val(family);
                $('#sonOf').val(myArray[0]).trigger('change');
                $('#marriedTofatherTo').val(myArray[1]).trigger('change');
            }

            $('.change').first().trigger('change');
            familyInputTrigger.trigger('change');

            if ($(event.target).parents('.family-popup-wrapper').length) {
                closeFamilyPopup.trigger('click');
            }
        });
    });
}

function build_fstring_dyn( dtype, age, gender, fatherName, motherName, partnerName, personal_status, kids, notice) {
    if( ! gender && ! fatherName && ! motherName && ! partnerName && ! personal_status && ! kids && ! age ) {
        return ( personal_status) ? personal_status : '';
    }

    var strtemplatedata = sticker[ dtype];
    var strtemplate;
    var keybuild = '';

    if( fatherName != '') {
        keybuild = 'father';
    }

    if( motherName != '') {
        if( keybuild == '') {
            keybuild = 'mother';
        } else {
            keybuild += '_mother';
        }
    }

    if( partnerName != '') {
        if( keybuild == '') {
            keybuild = 'spouse';
        } else {
            keybuild += '_spouse';
        }
    }

    if( kids != '') {
        if( keybuild == '') {
            keybuild = 'children';
        } else {
            keybuild += '_children';
        }
    }

    if( fatherName == '' && motherName == '' && partnerName == '' && personal_status == '' && kids != '') {
        strtemplate = strtemplatedata[gender+'_'+dtype]['children_only'];
    } else {
        strtemplate = strtemplatedata[gender+'_'+dtype]['blank'][keybuild];
    }
    if( age > 16) {
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

    } else {
        if( personal_status != '' && personal_status != null) {
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
        } else {
            strtemplate = strtemplatedata[gender+'_'+dtype]['blank'][keybuild];
        }
    }

    if( strtemplate) {
        strtemplate = strtemplate.replace('%%father_name%%', fatherName);
        strtemplate = strtemplate.replace('%%mother_name%%', motherName);
        strtemplate = strtemplate.replace('%%partner_name%%', partnerName);
        strtemplate = strtemplate.replace('%%number_of_kids%%', kids);
        return strtemplate;

    } else {
        return ( personal_status && ! notice) ? male_female_personal_status( personal_status, gender) : notice;
    }
}
function male_female_personal_status( personal_status, gender) {
    if( ! personal_status) {
        return '';
    }

    if( ! gender) {
        gender = $('[name="gender"]').val();
    }

    var language = get_lang();

    var options = {
        'male': {
            'he': {
                'רווק': is_underage() ? '' : 'רווק',
                'נשוי': 'נשוי',
                'גרוש': 'גרוש'
            },
            'en': {
                'רווק': is_underage() ? '' : 'Single',
                'נשוי': 'Married',
                'גרוש': 'Divorcee'
            }
        },
        'female': {
            'he': {
                'רווק': is_underage() ? '' : 'רווקה',
                'נשוי': 'נשואה',
                'גרוש': 'גרושה'
            },
            'en': {
                'רווק': is_underage() ? '' : 'Single',
                'נשוי': 'Married',
                'גרוש': 'Divorcee'
            }
        }
    }

    return options[gender][language][personal_status];
}
function build_family_string(sonOf, marriedTo, fatherTo, notice, sep = ',') {
    if (sonOf && marriedTo) {
        sonOf += sep + " ";
    }
    if (marriedTo && fatherTo) {
        marriedTo += ", ";
    }

    family = sonOf + marriedTo + fatherTo;
    family = family ? family : notice;

    return family;
}
function get_selected_gender() {
    return $.trim($('[name="gender"]').val());
}

function get_number_of_kids() {
    return $.trim($('#kids-input').val());
}

function get_partner_name() {
    return $.trim($('#partner-name-input').val());
}

function get_mother_name() {
    return $.trim($('#mother-name-input').val());
}

function get_father_name() {
    return $.trim($('#father-name-input').val());
}
function get_personal_status() {
    return $.trim($('[name=personal_status]').val());
}

function get_personal_status_text(partnerName) {
    var status = get_personal_status();
    var gender = get_selected_gender();
    var language = get_lang();

    if (!gender) {
        return '';
    }

    var options = {
        'male': {
            'he': {
                'רווק': is_underage() ? '' : 'רווק',
                'נשוי': 'נשוי',
                'אלמן': partnerName ? 'התאלמן מ' + partnerName : 'אלמן',
                'גרוש': 'גרוש'
            },
            'en': {
                'רווק': is_underage() ? '' : 'Single',
                'נשוי': 'Married',
                'אלמן': partnerName ? 'widow of ' + partnerName : 'Widower',
                'גרוש': 'Divorcee'
            }
        },
        'female': {
            'he': {
                'רווק': is_underage() ? '' : 'רווקה',
                'נשוי': 'נשואה',
                'אלמן': partnerName ? 'התאלמנה מ' + partnerName : 'אלמנה',
                'גרוש': 'גרושה'
            },
            'en': {
                'רווק': is_underage() ? '' : 'Single',
                'נשוי': 'Married',
                'אלמן': partnerName ? 'widow of ' + partnerName : 'Widower',
                'גרוש': 'Divorcee'
            }
        }
    }

    return options[gender][language][status];
}

function is_underage() {
    if ($('[name=year_of_birth]').val() >= 1926) {
        return true;
    }

    if (!$('[name=year_of_death]').val() || !$('[name=year_of_birth]').val()) {
        return false;
    }

    return parseInt($('[name=year_of_death]').val()) - parseInt($('[name=year_of_birth]').val()) < 18;
}

function get_lang() {
    return $('body').hasClass('rtl') ? 'he' : 'en';
}

function is_lang(lang) {
    var language = get_lang();

    return language === lang;
}

function get_notice_value() {
    return $.trim($('[name=notice]').val());
}

function get_parent_to(gender, kids) {
    var prefix = "";

    if (kids) {
        if ('male' === gender) {
            prefix = is_lang('he') ? 'אב ל-' : 'father to ';
        } else {
            prefix = is_lang('he') ? 'אם ל-' : 'mother to ';
        }
    } else {
        return '';
    }

    return prefix + kids;
}

function get_married_to_text(gender, partnerName, personal_status) {
    var prefix = '', suffix = '';

    if( partnerName && personal_status === 'נשוי') {
        if( 'male' === gender) {
            prefix = is_lang('he') ? 'נשוי ל' : 'Married to ';
            suffix = is_lang('he') ? '' : '';
        } else {
            prefix = is_lang('he') ? 'נשואה ל' : 'Married to ';
            suffix = is_lang('he') ? '' : '';
        }

    } else if( personal_status && ! partnerName) {
        prefix = is_lang('he') ? 'נשוי' : 'Married';

    } else if( personal_status) {
        prefix = get_personal_status_text( partnerName);
        partnerName = '';

    } else {
        return '';
    }

    return prefix + partnerName + suffix;
}

function get_son_of_text(gender, fatherName, motherName) {
    var prefix = '', suffix = '', addon = '';

    if (!gender) {
        return '';
    }

    if ('male' === gender) {
        prefix = is_lang('he') ? 'בן ' : 'Son of ';
        suffix = is_lang('he') ? '' : "";
    } else {
        prefix = is_lang('he') ? 'בת ' : 'Daughter of ';
        suffix = is_lang('he') ? '' : "";
    }

    if (fatherName && motherName) {
        addon = is_lang('he') ? ' ו' : " and ";
    } else if (!fatherName && !motherName) {
        return '';
    }

    return prefix + fatherName + addon + motherName + suffix;
}
function getCanvasXposition(field) {
    let base = info.rtl ? 455 : 470;
    let viewMode = window.location.href.indexOf('?person_id=') != -1;
    switch (field) {
        case 'first_name':
            if (info.rtl) {
                extra_pos = viewMode ? 110 : 90;
            } else {
                extra_pos = viewMode ? 90 : 90;
            }
            break;
        case 'last_name':
            if (info.rtl) {
                extra_pos = 110;
            } else {
                extra_pos = viewMode ? 90 : 100;
            }
            break;
        default:
            extra_pos = 0;
            break;
    }
    return base + extra_pos;
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

async function init_sticker_canvas(section) {
    var $myCanvas = section.find('canvas');

    var backImg = $myCanvas.attr('data-back-img');
    var blankImg = '/wp-content/themes/shemvener-child/scss/images/blank.jpg';
    var print = getParameterByName('print');
    var _$myCanvas$addLayer$a;
    var qr_code = section.find('[name="qr_code"]').val();
    var top_position = 98;
    var large_text_size = 38;
    var small_text_size = info.rtl ? 20 : 18;
    var line_heights = {
        tall: 35,
        short: 30
    };

    $myCanvas.addLayer({
        type: 'image',
        name: 'back',
        source: backImg,
        x: 0,
        y: 0,
        imageSmoothing: false,
        fromCenter: false
    });

    // New QR code generator
    var skip_qr_code = false;
    var qr_file_path;
    if(qr_code) {
        try {
            qr_file_path = await getQrImageSource(qr_code)
            if(qr_file_path){
                skip_qr_code = false;
            }
        } catch(e) {
            console.warn(e)
            skip_qr_code = true;
        }
    }

    //place the main image on the sticker
    if( skip_qr_code === false && qr_code && qr_file_path.indexOf('<head>') <= -1) {
        $myCanvas.addLayer((_$myCanvas$addLayer$a = {
            type: 'image',
            name: 'more_info',
            source: blankImg,
            x: info.rtl ? 518 : 518,
            y: 310,
            width: 90,
            height: 90,
            imageSmoothing: false,
            fromCenter: false
        }, _defineProperty(_$myCanvas$addLayer$a, "source", qr_file_path),
            _defineProperty(_$myCanvas$addLayer$a, "text", section.find('[name="more_info"]').val()),
            _defineProperty(_$myCanvas$addLayer$a, "crossOrigin", "Anonymous"), _$myCanvas$addLayer$a));
    }

    // place the first name on the sticker
    $myCanvas.addLayer({
        name: 'first_name',
        type: 'text',
        groups: ['texts'],
        x: getCanvasXposition('first_name'),
        y: top_position,
        fontSize: large_text_size,
        fontStyle: 'bold',
        text: section.find('[name="first_name"]').val()
    });

    top_position += line_heights.tall;
    //place the last name on the sticker
    $myCanvas.addLayer({
        name: 'last_name',
        type: 'text',
        groups: ['texts'],
        x: getCanvasXposition('last_name'),
        y: top_position,
        fontSize: large_text_size,
        fontStyle: 'bold',
        text: section.find('[name="last_name"]').val()
    });

    top_position += line_heights.tall;

    //place the year of birth
    $myCanvas.addLayer({
        name: 'year_of_birth',
        type: 'text',
        groups: ['texts'],
        x: info.rtl ? 510 : 510,
        y: top_position,
        fontSize: small_text_size,
        text: section.find('[name="year_of_birth"]').val() == 'unknown' || 'לא ידוע' ? '?' : section.find('[name="year_of_birth"]').val()
    });

    top_position -= 3;
    //place the dash between year of birth and year of death
    $myCanvas.addLayer({
        name: 'dash',
        type: 'text',
        groups: ['texts'],
        x: info.rtl ? 560 : 555,
        y: top_position,
        fontSize: 30,
        fontStyle: 'bold',
        text: section.find('[name="year_of_birth"]').val() != '' && section.find('[name="year_of_death"]').val() != '' ? '-' : '-'
    });

    top_position += 3;
    //place the year of death
    $myCanvas.addLayer({
        name: 'year_of_death',
        type: 'text',
        groups: ['texts'],
        x: info.rtl ? 610 : 610,
        y: top_position,
        fontSize: small_text_size,
        text: section.find('[name="year_of_death"]').val() == 'unknown' || 'לא ידוע' ? '?' : section.find('[name="year_of_death"]').val()
    });

    top_position += 80;

    set_deseased_description($myCanvas, section, top_position, small_text_size);

    $myCanvas.addLayer({
        type: 'rectangle',
        name: 'notapprovedbackground',
        fillStyle: 'transparent',
        fromCenter: false,
        x: info.rtl ? 55 : 95,
        y: 370,
        width: 200,
        height: 50
    });

    $myCanvas.addLayer({
        type: 'text',
        name: 'notapproved',
        groups: ['texts'],
        x: info.rtl ? 155 : 195,
        y: 390,
        fromCenter: false,
        fontSize: info.rtl ? 22 : 18,
        text: '',
        index: 99
    });

    //place the gender
    $myCanvas.addLayer({
        name: 'gender',
        type: 'text',
        groups: ['texts'],
        x: info.rtl ? 700 : 555,
        y: -315 - 10,
        fontSize: small_text_size,
        text: section.find('[name="gender"]').val(),
        change: function change(layer, props) {
            if (print == 1) {
                print = 0;
                setTimeout(function () {
                    printCanvas($myCanvas);
                }, 1200);
            }
        }
    }).setLayerGroup('texts', {
        fillStyle: '#000',
        fromCenter: true,
        fontFamily: 'Assistant, sans-serif',
        // align: info.rtl ? 'right' : 'left',
        align: 'center',
        respectAlign: true
    }).drawLayers();
}

function upodate_deseased_description($myCanvas, section) {
    var text = get_desceased_description(section);

    $myCanvas.setLayer('description', {
        text: text
    }).drawLayers();
}

function set_deseased_description($myCanvas, section, top_position, small_text_size) {
    var text = get_desceased_description(section);

    $myCanvas.addLayer({
        name: 'description',
        type: 'text',
        groups: ['texts'],
        x: info.rtl ? 560 : 555,
        y: top_position,
        fontSize: small_text_size,
        lineHeight: 1.2,
        text: text,
        maxWidth: 320,
        fromCenter: false,
    });
}

function get_desceased_description(section) {
    var occupation_text = '';
    var family_details = '';
    var description = "";
    var birth_country = countrycity = section.find('#birth_country_hidden').val() ? section.find('#birth_country_hidden').val() : section.find('[name="birth_country"]').val();
    var born_city = section.find('[name="born_city"]').val();
    var sonOf = $.trim(section.find('[name="sonOf"]').val());
    var marriedTofatherTo = $.trim(section.find('[name="marriedTofatherTo"]').val());

    if (section.find('#occupationMulty').length > 0) {
        occupation_text = occupation_array_to_string(section.find('#occupationMulty').select2('data'));
    } else {
        occupation_text = section.find('[name="occupation"]').val();
    }

    if (sonOf || marriedTofatherTo) {
        family_details = "";
    } else {
        family_details = section.find('[name="family_details"]').val();
        if( ! family_details) family_details = section.find('[name="family"]').val();
    }

    family_details = family_details.replace(/\.$/, "");
    var fsuff = '';
    if(section.find('[name="gender"]').val() == 'female'){
        fsuff = '_female';
    }

    if (birth_country && born_city) {
        countrycity = sticker.label['city_country_lbl'+fsuff];
    } else if (born_city) {
        countrycity = sticker.label['city_lbl'+fsuff];
    } else if (birth_country) {
        countrycity = sticker.label['country_lbl'+fsuff];
    }

    if( countrycity != '' && ( born_city != '' || birth_country != '')) {
        if( countrycity) {
            countrycity = countrycity.replace('%%city%%', born_city);
            countrycity = countrycity.replace('%%country%%', birth_country);
        }
    }

    if(occupation_text != '') {
        occupation_label = sticker.label.occupation_lbl;
        occupation_text = occupation_label.replace('%%occupation%%',occupation_text);
    }

    var pod = section.find('[name="place_of_death"]').val();
    if(pod != ''){
        pod_label = sticker.label['pod_lbl'+fsuff];
        pod = pod_label.replace('%%place_of_death%%',pod);
    }

    var textSections = [
        countrycity,
        sonOf,
        family_details,
        marriedTofatherTo,
        occupation_text,
        pod
    ];

    $.each(textSections, function (key, textsection) {
        description += $.trim(textsection) ? "\n" + textsection : '';
    });

    return description;
}

function clear_sticker_canvas($myCanvas) {
    $myCanvas.setLayer('first_name', {
        text: ''
    }).drawLayers();

    $myCanvas.setLayer('last_name', {
        text: ''
    }).drawLayers();

    $myCanvas.setLayer('year_of_birth', {
        text: ''
    }).drawLayers();

    $myCanvas.setLayer('year_of_death', {
        text: ''
    }).drawLayers();
}

function update_sticker_canvas(section, event) {
    var value = $(event.target).val();
    var name = $(event.target).attr('name');
    var $myCanvas = section.find('canvas');

    if (name == 'year_of_birth') {
        var birthDateValue = section.find('[name="year_of_birth"]').val();

        if (isNaN(birthDateValue) || birthDateValue == '') {
            value = '?';
        } else {
            value = value;
        }

        $myCanvas.setLayer('year_of_birth', {
            text: value
        }).drawLayers();

    } else if (name == 'year_of_death') {
        var birthDateValue = section.find('[name="year_of_death"]').val();

        if (isNaN(birthDateValue) || birthDateValue == '') {
            value = '?';
        } else {
            value = value;
        }

        $myCanvas.setLayer('year_of_death', {
            text: value
        }).drawLayers();

    } else if (name == 'first_name' || name == 'last_name') {
        $myCanvas.setLayer(name, {
            text: value
        }).drawLayers();
    } if ('personal_status' === name) {
        personal_status_changed(value);
    }

    //change the marital status to single if the deceased is underaged
    if (is_underage() && (name == 'year_of_birth' || name == 'year_of_death') && $('[name=year_of_death]').val()) {
        set_personal_status('רווק');
        trigger_update_family();
    }

    upodate_deseased_description($myCanvas, section);
}

function add_not_approved_popup($myCanvas) {
    $myCanvas.setLayer('notapprovedbackground', {
        fillStyle: "#fff"
    }).drawLayers();

    $myCanvas.setLayer('notapproved', {
        text: is_rtl() ? 'טרם אושר לפרסום' : 'Pending Review'
    }).drawLayers();
}

function trigger_update_family() {
    $('.continue-family').click();
}

function set_personal_status(value) {
    $('[name=personal_status]').val(value).change();
}

function personal_status_changed(value) {
    var $partner_name = $('#partner-name-input');

    var block_options = ['רווק', 'גרוש'];

    var block = block_options.indexOf(value) > -1;

    $partner_name.prop('disabled', block);

    if (block) {
        $partner_name.val('');
    }
}

function setParametersToUrl() {

    var labelInfo = $myCanvas.getLayerGroup('texts');
    var url = window.location.href.split('?')[0];
    address = url;
    setNameAndLastName($myCanvas);

    for (var i = 0; i < labelInfo.length; i++) {
        var el = labelInfo[i];

        if (el.text.length > 0) {
            address = addQueryArg(address, el.name, el.text);
        }
    }
    var person = {
        name: name,
        last_name: lastName
    };

    window.history.pushState('', '', address);
    addthis.update('share', 'url', address);

    var title = info.og.title.replaceArray(['{name}', '{last_name}'], [name, lastName]);
    var description = info.og.description.replaceArray(['{name}', '{last_name}'], [name, lastName]);

    addthis.update('share', 'title', title);
    addthis.update('share', 'description', description);

    return person;
}

function sortAddthisLink() {
    var shorturl = address;
    $.ajax({
        url: info.ajaxUrl,
        type: 'POST',
        dataType: 'json',
        data: {
            url: shorturl,
            action: 'get_shortlink'
        }
    }).done(function (res) {
        addthis.update('share', 'url', res['id']);
    });
}

function addQueryArg(uri, key, value) {
    var re = new RegExp("([?&])" + key + "=.*?(&|$)", "i");
    var separator = uri.indexOf('?') !== -1 ? "&" : "?";

    if (uri.match(re)) {
        return uri.replace(re, '$1' + key + "=" + value + '$2');
    } else {
        return uri + separator + key + "=" + value;
    }
}

function replaceTexts(where) {
    setNameAndLastName($myCanvas);
    var content = $(where).html().replaceArray(['{name}', '{last_name}'], [name, lastName]);
    $(where).html(content);
}

function setNameAndLastName($myCanvas) {
    var labelInfo = $myCanvas.getLayerGroup('texts');

    for (var i = 0; i < labelInfo.length; i++) {
        var el = labelInfo[i];

        if (el.text.length > 0) {
            if (el.name == 'first_name') {
                name = el.text;
            }

            if (el.name == 'last_name') {
                lastName = el.text;
            }
        }
    }
} ////////////////////// Check birth and death dates validation

function init_country_select(select) {
    if( $('.select2-allow-add').length) {
        $('.select2-allow-add').select2({
            tags: true,
            allowClear: true,
            placeholder: $(".select2-allow-add").attr('data-placeholder') ? $(".select2-allow-add").attr('data-placeholder') : '',
        });
    }
}

function init_occupation_select(section) {
    var $myCanvas = section.find('canvas');

    if( $('#occupationMulty:not(.select2-hidden-accessible)').length) {
        var select2 = $('#occupationMulty:not(.select2-hidden-accessible)').select2({
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

        select2.on("change", function (e) {
            $myCanvas.setLayer('occupation', {
                text: occupation_array_to_string(section.find('#occupationMulty').select2('data'))
            }).drawLayers();
        });
    }
}

function get_person_details(section) {
    var first_name = section.find('[name="first_name"]').val();
    var last_name = section.find('[name="last_name"]').val();
    var gender = section.find('[name="gender"]').val();
    var year_of_birth = section.find('[name="year_of_birth"]').val();
    var year_of_death = section.find('[name="year_of_death"]').val();
    var family_details = section.find('[name="family_details"]').val();
    var father_name = section.find('[name="father_name"]').val();
    var mother_name = section.find('[name="mother_name"]').val();
    var partner_name = section.find('[name="partner_name"]').val();
    var number_of_kids = section.find('[name="number_of_kids"]').val();
    var occupation = section.find('#occupationMulty').length > 0 ? occupation_array_to_string(section.find('#occupationMulty').select2('data')) : section.find('[name="occupation"]').val();
    var birth_country = section.find('[name="birth_country"]').val();
    var born_city = section.find('[name="born_city"]').val();
    var place_of_death = section.find('[name="place_of_death"]').val();
    var more_info = section.find('[name="more_info"]').val();
    var uploader_name = $('.save-label-popup [name="uploader_name"]').val();
    var uploader_phone = $('.save-label-popup [name="uploader_phone"]').val();
    var personal_status = $('[name="personal_status"]').val();
    var remark = $('[name="family"]').val();

    if (section.find('[name="deceased_uploader_name"]').val()) {
        uploader_name = section.find('[name="deceased_uploader_name"]').val();
        $('.save-label-popup [name="uploader_name"]').val(uploader_name);
    }

    var uploader_email = $('.save-label-popup [name="uploader_email"]').val();

    if (section.find('[name="deceased_uploader_email"]').val()) {
        uploader_email = section.find('[name="deceased_uploader_email"]').val();
        $('.save-label-popup [name="uploader_email"]').val(uploader_email);
    }

    if (section.find('[name="deceased_ID"]').val()) {
        var person_id = section.find('[name="deceased_ID"]').val();
    }

    if (section.find('[name="original_publish"]').val()) {
        var original_publish = section.find('[name="original_publish"]').val();
    }

    if ($('.save-label-popup [name="confirm"]').is(':checked')) {
        var confirm = true;
    }

    if ($('.save-label-popup [name="confirm"]').is(':checked')) {
        var confirm = true;
    }

    if (section.find('[name="deceased_uploader_email"]').val()) {
        confirm = true;
        $('.save-label-popup [name="confirm"]').prop('checked', true);
    }

    var person = {};

    if (first_name.length > 0) {
        person.first_name = first_name;
    }

    if (section.find('[name="deceased_ID"]').val()) {
        if (person_id.length > 0) {
            person.ID = person_id;
        }
    }

    if (section.find('[name="original_publish"]').val()) {
        if (original_publish.length > 0) {
            person.original_publish = original_publish;
        }
    }

    if (last_name.length > 0) {
        person.last_name = last_name;
    }

    if (gender.length > 0) {
        person.gender = gender;
    }
    if (personal_status.length > 0) {
        person.personal_status = personal_status;
    }
    if (year_of_birth.length > 0) {
        person.year_of_birth = year_of_birth;
    }

    if (year_of_death.length > 0) {
        person.year_of_death = year_of_death;
    }

    if (family_details.length > 0) {
        person.family_details = family_details;
    }

    if (father_name.length > 0) {
        person.father_name = father_name;
    }

    if (mother_name.length > 0) {
        person.mother_name = mother_name;
    }

    if (partner_name.length > 0) {
        person.partner_name = partner_name;
    }

    if (number_of_kids.length > 0) {
        person.number_of_kids = number_of_kids;
    }

    if (occupation.length > 0) {
        person.occupation = occupation;
    }

    if (birth_country.length > 0) {
        person.birth_country = birth_country;
    }

    if (born_city.length > 0) {
        person.born_city = born_city;
    }

    if (place_of_death.length > 0) {
        person.place_of_death = place_of_death;
    }

    if (more_info.length > 0) {
        person.more_info = more_info;
    }

    if (uploader_name.length > 0) {
        person.uploader_name = uploader_name;
    }

    if (uploader_email.length > 0) {
        person.uploader_email = uploader_email;
    }

    if (uploader_phone.length > 0) {
        person.uploader_phone = uploader_phone;
    }

    if (confirm == true) {
        person.confirm = true;
    }

    person.remark = remark;

    return person;
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

function validate_concent_form() {
    var $confirm = $('[name=confirm]');
    var inputs = [
        'uploader_name',
        'uploader_email',
        'uploader_phone'
    ];
	var regex = /^\+?\d+$/;
  
    $.each(inputs, function (key, inputName) {
        var $element = $('[name=' + inputName + ']');

        if (!$element.val()) {
            $element.addClass('error');
        } else if ('uploader_email' === inputName && !isEmail($element.val())) {
            $element.addClass('error');
        } else if ('uploader_phone' === inputName && !regex.test($element.val())) {
            $element.addClass('error');
        } else {
            $element.removeClass('error');
        }


    });


    if (!$confirm.is(':checked')) {
        $confirm.addClass('error');
    } else {
        $confirm.removeClass('error');
    }

    return $('.save-label-popup .error').length ? false : true;
}

function save_user_new_details(section, person) {
    var $saveInfoStage1 = section.find('.save-info-stage-1');
    var $saveInfoStage2 = section.find('.save-info-stage-2');
    var $myCanvas = section.find('canvas');
    add_name_content = add_name_content ? add_name_content : jQuery('#stage2 .terms').html();

    $saveInfoStage1.fadeOut(1000);
    $saveInfoStage2.fadeOut(1000);

    var data = {
        nonce: info.nonce_save_new_user,
        action: 'add_user_to_new_db',
        lang: info.lang,
        label: '',
        token: getUrlParameter('t')
    };

    data = $.extend(data, person);

    $('.error').removeClass('error');

    //show the add name popup
    if (($.magnificPopup.instance.isOpen == undefined || $.magnificPopup.instance.isOpen == false)) {
        $.magnificPopup.open({
            items: {
                src: '#stage2',
                // can be a HTML string, jQuery object, or CSS selector
                type: 'inline'
            },
            callbacks: {
                open: function () {
                    var fullname = jQuery('[name="first_name"]').val() + ' ' + jQuery('[name="last_name"]').val();
                    var fullnameHmtl = add_name_content.replace('%full_name%', fullname);
                    jQuery('#stage2 .terms').html(fullnameHmtl);
                }
            }
        });
        return false;
    } else {
        if (!validate_concent_form()) {
            unBlockLoader(jQuery('.form-popup'));
            return false;
        }
    }

    $.ajax({
        url: info.ajaxUrl + "?hash=" + makeid(5),
        type: 'POST',
        dataType: 'json',
        data: data
    }).done(function (res) {
        var errors = [];
        var $error = $('<div class="error-box"></div>');
        unBlockLoader(jQuery('.form-popup'));

        if (res.errors != undefined && res.errors.error_fields != undefined) {
            for (var i = 0; i < res.errors.error_fields.length; i++) {
                if (res.errors.error_fields[i] == 'last_name' || res.errors.error_fields[i] == 'first_name' || res.errors.error_fields[i] == 'gender' || res.errors.error_fields[i] == 'year_of_death' || res.errors.error_fields[i] == 'year_of_birth' || res.errors.error_fields[i] == 'born_city' || res.errors.error_fields[i] == 'birth_country' || res.errors.error_fields[i] == 'place_of_death' || res.errors.error_fields[i] == 'more_info' || res.errors.error_fields[i] == 'occupation') {
                    errors.push(res.errors.messages[i]);
                    $error.append('<div class="message">' + res.errors.messages[i] + '</div>');
                    var fieldName = res.errors.error_fields[i];

                    if (fieldName == 'gender') {
                        fieldName = 'sex';
                    }
                    $('[name="' + fieldName + '"]').addClass('error');
                    $('select[name="' + fieldName + '"]').parent().find('.select2.select2-container').addClass('error');
                }
            }

            if (errors.length == 0) {
                // get the name and last name for str replace
                setNameAndLastName($myCanvas); // set stage2 texts

                var title = info.popups.stage2.title.replaceArray(['{name}', '{last_name}'], [name, lastName]);
                var sub_title = info.popups.stage2.sub_title.replaceArray(['{name}', '{last_name}'], [name, lastName]);
                $('#stage2 .content .info .title').html(title);
                $('#stage2 .content .info .sub-title').html(sub_title); //open popup on stage2

                $.magnificPopup.open({
                    items: {
                        src: '#stage2',
                        // can be a HTML string, jQuery object, or CSS selector
                        type: 'inline'
                    }
                });
            } else {
                $saveInfoStage1.html($error);
                $saveInfoStage1.stop().fadeIn(500);
            }

            return false;
        } else {
            // if on stage 2
            $saveInfoStage2.stop().fadeOut(1000);
            $saveInfoStage1.stop().hide(1000);

            if (res.status == 'fail') {
                // if save fail
                for (var i = 0; i < res.errors.error_fields.length; i++) {
                    $('[name="' + res.errors.error_fields[i] + '"]').addClass('error');
                    errors.push(res.errors.messages[i]);
                    $error.append('<div class="message">' + res.errors.messages[i] + '</div>');
                }

                $saveInfoStage2.html($error);
                $saveInfoStage2.stop().fadeIn(500);
            } else {
                // if save success
                setNameAndLastName($myCanvas); //replace texts in success popup

                var title = info.popups.sucess.title.replaceArray(['{name}', '{last_name}'], [name, lastName]);
                var question = info.popups.sucess.question.replaceArray(['{name}', '{last_name}'], [name, lastName]);
                $('#pop-success .content .info .title').html(title);
                $('#pop-success .content .info .question').html(question); // add label img to success popup

                var dataUrl = $myCanvas.getCanvasImage();
                $('#pop-success .label').html('<img src="' + dataUrl + '" alt="" />');

                if ($('.create-deceased [name="deceased_uploader_email"]').val()) {
                    $.magnificPopup.open({
                        items: {
                            src: '#pop-success'
                        },
                    });
                } //change the content of popup form stage2 to success popup

                $.magnificPopup.instance.items[0].src = "#pop-success";
                $.magnificPopup.instance.updateItemHTML();
                if (res.deceased_id) {
                    jQuery('#pop-success .js-show-share-popup').attr('data-person-id', res.deceased_id);
                    let href = jQuery('#pop-success .js-show-share-popup').attr('href');
                    jQuery('#pop-success .js-show-share-popup').attr('href', href + '?person_id=' + res.deceased_id + '&details=1');

                    if( jQuery('#pop-success .button-maagar[data-person-id]').length && ! jQuery('#pop-success .button-maagar[data-person-id]').attr('data-person-id')) {
                        let href2 = jQuery('#pop-success .button-maagar[data-person-id]').attr('href') + '?person_id=' + res.deceased_id + '&details=1';
                        jQuery('#pop-success .button-maagar[data-person-id]').attr('data-person-id', res.deceased_id);
                        jQuery('#pop-success .button-maagar[data-person-id]').attr('href', href2);
                    }
                }
                if (res.share_buttons) {
                    jQuery('#share-popup .content').html(res.share_buttons);
                }

            }
        }
    }).fail(function (res) {
        unBlockLoader(jQuery('.form-popup'));
    }).always(function (res) {
        unBlockLoader(jQuery('.form-popup'));
    });
}
function fixMobileGenderSelect() {
    jQuery('body').on('change', 'select[name="gender"]', function () {
        let genderSelect = jQuery(this).val();
        if (genderSelect == 'male' || genderSelect == 'female') {
            $('.create-deceased').removeClass('gender-not-valid');
            $('.section-create-label-form').removeClass('show-gender-req-icon');
        } else {
            $('.create-deceased').addClass('gender-not-valid');
            $('.section-create-label-form').addClass('show-gender-req-icon');
        }
        if (jQuery(window).width() <= 480) {
            jQuery(this).trigger('click');
        }
    });
}
jQuery(function ($) {
    fixMobileGenderSelect();
});

function disable_action_buttons() {
    jQuery('.buttons button').attr('disabled', true);
}

function enable_action_buttons() {
    jQuery('.buttons button').removeAttr('disabled');
}

function isUrlValid(url) {
    return /^(https?|s?ftp):\/\/(((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:)*@)?(((\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5]))|((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.?)(:\d*)?)(\/((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)+(\/(([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)*)*)?)?(\?((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)|[\uE000-\uF8FF]|\/|\?)*)?(#((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)|\/|\?)*)?$/i.test(url);
}