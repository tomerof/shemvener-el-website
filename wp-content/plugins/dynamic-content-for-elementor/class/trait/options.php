<?php

// SPDX-FileCopyrightText: 2018-2026 Ovation S.r.l. <help@dynamic.ooo>
// SPDX-License-Identifier: GPL-3.0-or-later
namespace DynamicContentForElementor;

trait Options
{
    /**
     * @return array<string>
     */
    public static function get_dynamic_tags_categories()
    {
        return ['base', 'text', 'url', 'number', 'post_meta', 'date', 'datetime', 'media', 'image', 'gallery', 'color'];
    }
    /**
     * @return array<string,mixed>
     */
    public static function get_compare_options()
    {
        return [
            // Empty checks
            'isset' => esc_html__('Not empty', 'dynamic-content-for-elementor'),
            'not' => esc_html__('Empty or not set', 'dynamic-content-for-elementor'),
            // Equality comparisons
            'value' => esc_html__('Equal to', 'dynamic-content-for-elementor'),
            'value_i' => esc_html__('Equal to (ignore case)', 'dynamic-content-for-elementor'),
            'not_value' => esc_html__('Not equal to', 'dynamic-content-for-elementor'),
            'not_value_i' => esc_html__('Not equal to (ignore case)', 'dynamic-content-for-elementor'),
            // Numeric comparisons
            'lt' => esc_html__('Less than', 'dynamic-content-for-elementor'),
            'lte' => esc_html__('Less than or equal to', 'dynamic-content-for-elementor'),
            'gt' => esc_html__('Greater than', 'dynamic-content-for-elementor'),
            'gte' => esc_html__('Greater than or equal to', 'dynamic-content-for-elementor'),
            // String comparisons
            'contain' => esc_html__('Contains', 'dynamic-content-for-elementor'),
            'not_contain' => esc_html__('Does not contain', 'dynamic-content-for-elementor'),
            'starts_with' => esc_html__('Starts with', 'dynamic-content-for-elementor'),
            'ends_with' => esc_html__('Ends with', 'dynamic-content-for-elementor'),
            // Array operations
            'in_array' => esc_html__('Is one of', 'dynamic-content-for-elementor'),
            'not_in_array' => esc_html__('Is not one of', 'dynamic-content-for-elementor'),
        ];
    }
    /**
     * @return array<string,string>
     */
    public static function get_post_orderby_options()
    {
        return ['ID' => esc_html__('Post ID', 'dynamic-content-for-elementor'), 'author' => esc_html__('Post Author', 'dynamic-content-for-elementor'), 'title' => esc_html__('Title', 'dynamic-content-for-elementor'), 'date' => esc_html__('Date', 'dynamic-content-for-elementor'), 'modified' => esc_html__('Last Modified Date', 'dynamic-content-for-elementor'), 'parent' => esc_html__('Parent ID', 'dynamic-content-for-elementor'), 'rand' => esc_html__('Random', 'dynamic-content-for-elementor'), 'comment_count' => esc_html__('Comment Count', 'dynamic-content-for-elementor'), 'menu_order' => esc_html__('Menu Order', 'dynamic-content-for-elementor'), 'meta_value' => esc_html__('Meta Value', 'dynamic-content-for-elementor'), 'none' => esc_html__('None', 'dynamic-content-for-elementor'), 'name' => esc_html__('Name', 'dynamic-content-for-elementor'), 'type' => esc_html__('Type', 'dynamic-content-for-elementor'), 'relevance' => esc_html__('Relevance', 'dynamic-content-for-elementor'), 'post__in' => esc_html__('Preserve Post ID order given', 'dynamic-content-for-elementor')];
    }
    /**
     * @return array<string,string>
     */
    public static function get_post_orderby_meta_value_types()
    {
        return ['NUMERIC' => esc_html__('Numeric', 'dynamic-content-for-elementor'), 'BINARY' => esc_html__('Binary', 'dynamic-content-for-elementor'), 'CHAR' => esc_html__('Character', 'dynamic-content-for-elementor'), 'DATE' => esc_html__('Date', 'dynamic-content-for-elementor'), 'DATETIME' => esc_html__('DateTime', 'dynamic-content-for-elementor'), 'DECIMAL' => esc_html__('Decimal', 'dynamic-content-for-elementor'), 'SIGNED' => esc_html__('Signed', 'dynamic-content-for-elementor'), 'TIME' => esc_html__('Time', 'dynamic-content-for-elementor'), 'UNSIGNED' => esc_html__('Unsigned', 'dynamic-content-for-elementor')];
    }
    /**
     * @return array<string,string>
     */
    public static function get_woo_orderby_options()
    {
        return ['date' => esc_html__('Date', 'dynamic-content-for-elementor'), 'title' => esc_html__('Title', 'dynamic-content-for-elementor'), 'id' => esc_html__('ID', 'dynamic-content-for-elementor'), 'menu_order' => esc_html__('Menu Order', 'dynamic-content-for-elementor')];
    }
    /**
     * @return array<string,string>
     */
    public static function get_term_orderby_options()
    {
        return ['parent' => esc_html__('Parent', 'dynamic-content-for-elementor'), 'count' => esc_html__('Count (number of associated posts)', 'dynamic-content-for-elementor'), 'term_order' => esc_html__('Order', 'dynamic-content-for-elementor'), 'name' => esc_html__('Name', 'dynamic-content-for-elementor'), 'slug' => esc_html__('Slug', 'dynamic-content-for-elementor'), 'term_group' => esc_html__('Group', 'dynamic-content-for-elementor'), 'term_id' => 'ID'];
    }
    /**
     * @return array<string,string>
     */
    public static function get_public_taxonomies()
    {
        $taxonomies = get_taxonomies(['public' => \true]);
        $taxonomy_array = [];
        foreach ($taxonomies as $taxonomy) {
            $taxonomy_object = get_taxonomy($taxonomy);
            $taxonomy_array[$taxonomy] = sanitize_text_field($taxonomy_object->labels->name);
        }
        return $taxonomy_array;
    }
    /**
     * @return array<string,string>
     */
    public static function get_anim_timing_functions()
    {
        $tf_p = ['linear' => esc_html__('Linear', 'dynamic-content-for-elementor'), 'ease' => esc_html__('Ease', 'dynamic-content-for-elementor'), 'ease-in' => esc_html__('Ease In', 'dynamic-content-for-elementor'), 'ease-out' => esc_html__('Ease Out', 'dynamic-content-for-elementor'), 'ease-in-out' => esc_html__('Ease In Out', 'dynamic-content-for-elementor'), 'cubic-bezier(0.755, 0.05, 0.855, 0.06)' => esc_html__('easeInQuint', 'dynamic-content-for-elementor'), 'cubic-bezier(0.23, 1, 0.32, 1)' => esc_html__('easeOutQuint', 'dynamic-content-for-elementor'), 'cubic-bezier(0.86, 0, 0.07, 1)' => esc_html__('easeInOutQuint', 'dynamic-content-for-elementor'), 'cubic-bezier(0.6, 0.04, 0.98, 0.335)' => esc_html__('easeInCirc', 'dynamic-content-for-elementor'), 'cubic-bezier(0.075, 0.82, 0.165, 1)' => esc_html__('easeOutCirc', 'dynamic-content-for-elementor'), 'cubic-bezier(0.785, 0.135, 0.15, 0.86)' => esc_html__('easeInOutCirc', 'dynamic-content-for-elementor'), 'cubic-bezier(0.95, 0.05, 0.795, 0.035)' => esc_html__('easeInExpo', 'dynamic-content-for-elementor'), 'cubic-bezier(0.19, 1, 0.22, 1)' => esc_html__('easeOutExpo', 'dynamic-content-for-elementor'), 'cubic-bezier(1, 0, 0, 1)' => esc_html__('easeInOutExpo', 'dynamic-content-for-elementor'), 'cubic-bezier(0.6, -0.28, 0.735, 0.045)' => esc_html__('easeInBack', 'dynamic-content-for-elementor'), 'cubic-bezier(0.175, 0.885, 0.32, 1.275)' => esc_html__('easeOutBack', 'dynamic-content-for-elementor'), 'cubic-bezier(0.68, -0.55, 0.265, 1.55)' => esc_html__('easeInOutBack', 'dynamic-content-for-elementor')];
        return $tf_p;
    }
    /**
     * @return array<string,string>
     */
    public static function number_format_currency()
    {
        return ['en-US' => esc_html__('English (US)', 'dynamic-content-for-elementor'), 'af-ZA' => esc_html__('Afrikaans', 'dynamic-content-for-elementor'), 'sq-AL' => esc_html__('Albanian', 'dynamic-content-for-elementor'), 'ar-AR' => esc_html__('Arabic', 'dynamic-content-for-elementor'), 'hy-AM' => esc_html__('Armenian', 'dynamic-content-for-elementor'), 'ay-BO' => esc_html__('Aymara', 'dynamic-content-for-elementor'), 'az-AZ' => esc_html__('Azeri', 'dynamic-content-for-elementor'), 'eu-ES' => esc_html__('Basque', 'dynamic-content-for-elementor'), 'be-BY' => esc_html__('Belarusian', 'dynamic-content-for-elementor'), 'bn-IN' => esc_html__('Bengali', 'dynamic-content-for-elementor'), 'bs-BA' => esc_html__('Bosnian', 'dynamic-content-for-elementor'), 'en-GB' => esc_html__('British English', 'dynamic-content-for-elementor'), 'bg-BG' => esc_html__('Bulgarian', 'dynamic-content-for-elementor'), 'ca-ES' => esc_html__('Catalan', 'dynamic-content-for-elementor'), 'ck-US' => esc_html__('Cherokee', 'dynamic-content-for-elementor'), 'hr-HR' => esc_html__('Croatian', 'dynamic-content-for-elementor'), 'cs-CZ' => esc_html__('Czech', 'dynamic-content-for-elementor'), 'da-DK' => esc_html__('Danish', 'dynamic-content-for-elementor'), 'nl-NL' => esc_html__('Dutch', 'dynamic-content-for-elementor'), 'nl-BE' => esc_html__('Dutch (Belgi?)', 'dynamic-content-for-elementor'), 'en-UD' => esc_html__('English (Upside Down)', 'dynamic-content-for-elementor'), 'eo-EO' => esc_html__('Esperanto', 'dynamic-content-for-elementor'), 'et-EE' => esc_html__('Estonian', 'dynamic-content-for-elementor'), 'fo-FO' => esc_html__('Faroese', 'dynamic-content-for-elementor'), 'tl-PH' => esc_html__('Filipino', 'dynamic-content-for-elementor'), 'fi-FI' => esc_html__('Finland', 'dynamic-content-for-elementor'), 'fb-FI' => esc_html__('Finnish', 'dynamic-content-for-elementor'), 'fr-CA' => esc_html__('French (Canada)', 'dynamic-content-for-elementor'), 'fr-FR' => esc_html__('French (France)', 'dynamic-content-for-elementor'), 'gl-ES' => esc_html__('Galician', 'dynamic-content-for-elementor'), 'ka-GE' => esc_html__('Georgian', 'dynamic-content-for-elementor'), 'de-DE' => esc_html__('German', 'dynamic-content-for-elementor'), 'el-GR' => esc_html__('Greek', 'dynamic-content-for-elementor'), 'gn-PY' => esc_html__('Guaran?', 'dynamic-content-for-elementor'), 'gu-IN' => esc_html__('Gujarati', 'dynamic-content-for-elementor'), 'he-IL' => esc_html__('Hebrew', 'dynamic-content-for-elementor'), 'hi-IN' => esc_html__('Hindi', 'dynamic-content-for-elementor'), 'hu-HU' => esc_html__('Hungarian', 'dynamic-content-for-elementor'), 'is-IS' => esc_html__('Icelandic', 'dynamic-content-for-elementor'), 'id-ID' => esc_html__('Indonesian', 'dynamic-content-for-elementor'), 'ga-IE' => esc_html__('Irish', 'dynamic-content-for-elementor'), 'it-IT' => esc_html__('Italian', 'dynamic-content-for-elementor'), 'ja-JP' => esc_html__('Japanese', 'dynamic-content-for-elementor'), 'jv-ID' => esc_html__('Javanese', 'dynamic-content-for-elementor'), 'kn-IN' => esc_html__('Kannada', 'dynamic-content-for-elementor'), 'kk-KZ' => esc_html__('Kazakh', 'dynamic-content-for-elementor'), 'km-KH' => esc_html__('Khmer', 'dynamic-content-for-elementor'), 'tl-ST' => esc_html__('Klingon', 'dynamic-content-for-elementor'), 'ko-KR' => esc_html__('Korean', 'dynamic-content-for-elementor'), 'ku-TR' => esc_html__('Kurdish', 'dynamic-content-for-elementor'), 'la-VA' => esc_html__('Latin', 'dynamic-content-for-elementor'), 'lv-LV' => esc_html__('Latvian', 'dynamic-content-for-elementor'), 'fb-LT' => esc_html__('Leet Speak', 'dynamic-content-for-elementor'), 'li-NL' => esc_html__('Limburgish', 'dynamic-content-for-elementor'), 'lt-LT' => esc_html__('Lithuanian', 'dynamic-content-for-elementor'), 'mk-MK' => esc_html__('Macedonian', 'dynamic-content-for-elementor'), 'mg-MG' => esc_html__('Malagasy', 'dynamic-content-for-elementor'), 'ms-MY' => esc_html__('Malay', 'dynamic-content-for-elementor'), 'ml-IN' => esc_html__('Malayalam', 'dynamic-content-for-elementor'), 'mt-MT' => esc_html__('Maltese', 'dynamic-content-for-elementor'), 'mr-IN' => esc_html__('Marathi', 'dynamic-content-for-elementor'), 'mn-MN' => esc_html__('Mongolian', 'dynamic-content-for-elementor'), 'ne-NP' => esc_html__('Nepali', 'dynamic-content-for-elementor'), 'se-NO' => esc_html__('Northern S?mi', 'dynamic-content-for-elementor'), 'nb-NO' => esc_html__('Norwegian (bokmal)', 'dynamic-content-for-elementor'), 'nn-NO' => esc_html__('Norwegian (nynorsk)', 'dynamic-content-for-elementor'), 'ps-AF' => esc_html__('Pashto', 'dynamic-content-for-elementor'), 'fa-IR' => esc_html__('Persian', 'dynamic-content-for-elementor'), 'pl-PL' => esc_html__('Polish', 'dynamic-content-for-elementor'), 'pt-BR' => esc_html__('Portuguese (Brazil)', 'dynamic-content-for-elementor'), 'pt-PT' => esc_html__('Portuguese (Portugal)', 'dynamic-content-for-elementor'), 'pa-IN' => esc_html__('Punjabi', 'dynamic-content-for-elementor'), 'qu-PE' => esc_html__('Quechua', 'dynamic-content-for-elementor'), 'ro-RO' => esc_html__('Romanian', 'dynamic-content-for-elementor'), 'rm-CH' => esc_html__('Romansh', 'dynamic-content-for-elementor'), 'ru-RU' => esc_html__('Russian', 'dynamic-content-for-elementor'), 'sa-IN' => esc_html__('Sanskrit', 'dynamic-content-for-elementor'), 'sr-RS' => esc_html__('Serbian', 'dynamic-content-for-elementor'), 'zh-CN' => esc_html__('Simplified Chinese (China)', 'dynamic-content-for-elementor'), 'sk-SK' => esc_html__('Slovak', 'dynamic-content-for-elementor'), 'sl-SI' => esc_html__('Slovenian', 'dynamic-content-for-elementor'), 'so-SO' => esc_html__('Somali', 'dynamic-content-for-elementor'), 'es-LA' => esc_html__('Spanish', 'dynamic-content-for-elementor'), 'es-CL' => esc_html__('Spanish (Chile)', 'dynamic-content-for-elementor'), 'es-CO' => esc_html__('Spanish (Colombia)', 'dynamic-content-for-elementor'), 'es-MX' => esc_html__('Spanish (Mexico)', 'dynamic-content-for-elementor'), 'es-ES' => esc_html__('Spanish (Spain)', 'dynamic-content-for-elementor'), 'es-VE' => esc_html__('Spanish (Venezuela)', 'dynamic-content-for-elementor'), 'sw-KE' => esc_html__('Swahili', 'dynamic-content-for-elementor'), 'sv-SE' => esc_html__('Swedish', 'dynamic-content-for-elementor'), 'sy-SY' => esc_html__('Syriac', 'dynamic-content-for-elementor'), 'tg-TJ' => esc_html__('Tajik', 'dynamic-content-for-elementor'), 'ta-IN' => esc_html__('Tamil', 'dynamic-content-for-elementor'), 'tt-RU' => esc_html__('Tatar', 'dynamic-content-for-elementor'), 'te-IN' => esc_html__('Telugu', 'dynamic-content-for-elementor'), 'th-TH' => esc_html__('Thai', 'dynamic-content-for-elementor'), 'zh-HK' => esc_html__('Traditional Chinese (Hong Kong)', 'dynamic-content-for-elementor'), 'zh-TW' => esc_html__('Traditional Chinese (Taiwan)', 'dynamic-content-for-elementor'), 'tr-TR' => esc_html__('Turkish', 'dynamic-content-for-elementor'), 'uk-UA' => esc_html__('Ukrainian', 'dynamic-content-for-elementor'), 'ur-PK' => esc_html__('Urdu', 'dynamic-content-for-elementor'), 'uz-UZ' => esc_html__('Uzbek', 'dynamic-content-for-elementor'), 'vi-VN' => esc_html__('Vietnamese', 'dynamic-content-for-elementor'), 'cy-GB' => esc_html__('Welsh', 'dynamic-content-for-elementor'), 'xh-ZA' => esc_html__('Xhosa', 'dynamic-content-for-elementor'), 'yi-DE' => esc_html__('Yiddish', 'dynamic-content-for-elementor'), 'zu-ZA' => esc_html__('Zulu', 'dynamic-content-for-elementor')];
    }
    /**
     * @return array<string,string>
     */
    public static function get_ease()
    {
        return ['easeNone' => esc_html__('None', 'dynamic-content-for-elementor'), 'easeIn' => esc_html__('In', 'dynamic-content-for-elementor'), 'easeOut' => esc_html__('Out', 'dynamic-content-for-elementor'), 'easeInOut' => esc_html__('InOut', 'dynamic-content-for-elementor')];
    }
    /**
     * @return array<string,string>
     */
    public static function get_timing_functions()
    {
        return ['Power0' => esc_html__('Linear', 'dynamic-content-for-elementor'), 'Power1' => esc_html__('Power1', 'dynamic-content-for-elementor'), 'Power2' => esc_html__('Power2', 'dynamic-content-for-elementor'), 'Power3' => esc_html__('Power3', 'dynamic-content-for-elementor'), 'Power4' => esc_html__('Power4', 'dynamic-content-for-elementor'), 'SlowMo' => esc_html__('SlowMo', 'dynamic-content-for-elementor'), 'Back' => esc_html__('Back', 'dynamic-content-for-elementor'), 'Elastic' => esc_html__('Elastic', 'dynamic-content-for-elementor'), 'Bounce' => esc_html__('Bounce', 'dynamic-content-for-elementor'), 'Circ' => esc_html__('Circ', 'dynamic-content-for-elementor'), 'Expo' => esc_html__('Expo', 'dynamic-content-for-elementor'), 'Sine' => esc_html__('Sine', 'dynamic-content-for-elementor')];
    }
    /**
     * @return array<int,array<string,array<string,string>|string>>
     */
    public static function get_anim_in()
    {
        return [['label' => 'Fading', 'options' => ['fadeIn' => 'Fade In', 'fadeInDown' => 'Fade In Down', 'fadeInLeft' => 'Fade In Left', 'fadeInRight' => 'Fade In Right', 'fadeInUp' => 'Fade In Up']], ['label' => 'Zooming', 'options' => ['zoomIn' => 'Zoom In', 'zoomInDown' => 'Zoom In Down', 'zoomInLeft' => 'Zoom In Left', 'zoomInRight' => 'Zoom In Right', 'zoomInUp' => 'Zoom In Up']], ['label' => 'Bouncing', 'options' => ['bounceIn' => 'Bounce In', 'bounceInDown' => 'Bounce In Down', 'bounceInLeft' => 'Bounce In Left', 'bounceInRight' => 'Bounce In Right', 'bounceInUp' => 'Bounce In Up']], ['label' => 'Sliding', 'options' => ['slideInDown' => 'Slide In Down', 'slideInLeft' => 'Slide In Left', 'slideInRight' => 'Slide In Right', 'slideInUp' => 'Slide In Up']], ['label' => 'Rotating', 'options' => ['rotateIn' => 'Rotate In', 'rotateInDownLeft' => 'Rotate In Down Left', 'rotateInDownRight' => 'Rotate In Down Right', 'rotateInUpLeft' => 'Rotate In Up Left', 'rotateInUpRight' => 'Rotate In Up Right']], ['label' => 'Attention Seekers', 'options' => ['bounce' => 'Bounce', 'flash' => 'Flash', 'pulse' => 'Pulse', 'rubberBand' => 'Rubber Band', 'shake' => 'Shake', 'headShake' => 'Head Shake', 'swing' => 'Swing', 'tada' => 'Tada', 'wobble' => 'Wobble', 'jello' => 'Jello']], ['label' => 'Light Speed', 'options' => ['lightSpeedIn' => 'Light Speed In']], ['label' => 'Specials', 'options' => ['rollIn' => 'Roll In']]];
    }
    /**
     * @return array<int,array<string,array<string,string>|string>>
     */
    public static function get_anim_out()
    {
        return [['label' => 'Fading', 'options' => ['fadeOut' => 'Fade Out', 'fadeOutDown' => 'Fade Out Down', 'fadeOutLeft' => 'Fade Out Left', 'fadeOutRight' => 'Fade Out Right', 'fadeOutUp' => 'Fade Out Up']], ['label' => 'Zooming', 'options' => ['zoomOut' => 'Zoom Out', 'zoomOutDown' => 'Zoom Out Down', 'zoomOutLeft' => 'Zoom Out Left', 'zoomOutRight' => 'Zoom Out Right', 'zoomOutUp' => 'Zoom Out Up']], ['label' => 'Bouncing', 'options' => ['bounceOut' => 'Bounce Out', 'bounceOutDown' => 'Bounce Out Down', 'bounceOutLeft' => 'Bounce Out Left', 'bounceOutRight' => 'Bounce Out Right', 'bounceOutUp' => 'Bounce Out Up']], ['label' => 'Sliding', 'options' => ['slideOutDown' => 'Slide Out Down', 'slideOutLeft' => 'Slide Out Left', 'slideOutRight' => 'Slide Out Right', 'slideOutUp' => 'Slide Out Up']], ['label' => 'Rotating', 'options' => ['rotateOut' => 'Rotate Out', 'rotateOutDownLeft' => 'Rotate Out Down Left', 'rotateOutDownRight' => 'Rotate Out Down Right', 'rotateOutUpLeft' => 'Rotate Out Up Left', 'rotateOutUpRight' => 'Rotate Out Up Right']], ['label' => 'Attention Seekers', 'options' => ['bounce' => 'Bounce', 'flash' => 'Flash', 'pulse' => 'Pulse', 'rubberBand' => 'Rubber Band', 'shake' => 'Shake', 'headShake' => 'Head Shake', 'swing' => 'Swing', 'tada' => 'Tada', 'wobble' => 'Wobble', 'jello' => 'Jello']], ['label' => 'Light Speed', 'options' => ['lightSpeedOut' => 'Light Speed Out']], ['label' => 'Specials', 'options' => ['rollOut' => 'Roll Out']]];
    }
    /**
     * @return array<string,string>
     */
    public static function get_anim_open()
    {
        return ['noneIn' => _x('None', 'Ajax Page', 'dynamic-content-for-elementor'), 'enterFromFade' => _x('Fade', 'Ajax Page', 'dynamic-content-for-elementor'), 'enterFromLeft' => _x('Left', 'Ajax Page', 'dynamic-content-for-elementor'), 'enterFromRight' => _x('Right', 'Ajax Page', 'dynamic-content-for-elementor'), 'enterFromTop' => _x('Top', 'Ajax Page', 'dynamic-content-for-elementor'), 'enterFromBottom' => _x('Bottom', 'Ajax Page', 'dynamic-content-for-elementor'), 'enterFormScaleBack' => _x('Zoom Back', 'Ajax Page', 'dynamic-content-for-elementor'), 'enterFormScaleFront' => _x('Zoom Front', 'Ajax Page', 'dynamic-content-for-elementor'), 'flipInLeft' => _x('Flip Left', 'Ajax Page', 'dynamic-content-for-elementor'), 'flipInRight' => _x('Flip Right', 'Ajax Page', 'dynamic-content-for-elementor'), 'flipInTop' => _x('Flip Top', 'Ajax Page', 'dynamic-content-for-elementor'), 'flipInBottom' => _x('Flip Bottom', 'Ajax Page', 'dynamic-content-for-elementor')];
    }
    /**
     * @return array<string,string>
     */
    public static function get_anim_close()
    {
        return ['noneOut' => _x('None', 'Ajax Page', 'dynamic-content-for-elementor'), 'exitToFade' => _x('Fade', 'Ajax Page', 'dynamic-content-for-elementor'), 'exitToLeft' => _x('Left', 'Ajax Page', 'dynamic-content-for-elementor'), 'exitToRight' => _x('Right', 'Ajax Page', 'dynamic-content-for-elementor'), 'exitToTop' => _x('Top', 'Ajax Page', 'dynamic-content-for-elementor'), 'exitToBottom' => _x('Bottom', 'Ajax Page', 'dynamic-content-for-elementor'), 'exitToScaleBack' => _x('Zoom Back', 'Ajax Page', 'dynamic-content-for-elementor'), 'exitToScaleFront' => _x('Zoom Front', 'Ajax Page', 'dynamic-content-for-elementor'), 'flipOutLeft' => _x('Flip Left', 'Ajax Page', 'dynamic-content-for-elementor'), 'flipOutRight' => _x('Flip Right', 'Ajax Page', 'dynamic-content-for-elementor'), 'flipOutTop' => _x('Flip Top', 'Ajax Page', 'dynamic-content-for-elementor'), 'flipOutBottom' => _x('Flip Bottom', 'Ajax Page', 'dynamic-content-for-elementor')];
    }
    /**
     * @return array<string,string>
     */
    public static function bootstrap_button_sizes()
    {
        return ['xs' => esc_html__('Extra Small', 'dynamic-content-for-elementor'), 'sm' => esc_html__('Small', 'dynamic-content-for-elementor'), 'md' => esc_html__('Medium', 'dynamic-content-for-elementor'), 'lg' => esc_html__('Large', 'dynamic-content-for-elementor'), 'xl' => esc_html__('Extra Large', 'dynamic-content-for-elementor')];
    }
    /**
     * @return array<string,string>
     */
    public static function get_sql_operators()
    {
        $compare = self::get_wp_meta_compare();
        $compare['IS NULL'] = 'IS NULL';
        $compare['IS NOT NULL'] = 'IS NOT NULL';
        return $compare;
    }
    /**
     * @return array<string,string>
     */
    public static function get_wp_meta_compare()
    {
        return ['=' => '=', '>' => '&gt;', '>=' => '&gt;=', '<' => '&lt;', '<=' => '&lt;=', '!=' => '!=', 'LIKE' => 'LIKE', 'RLIKE' => 'RLIKE', 'NOT LIKE' => 'NOT LIKE', 'IN' => 'IN (...)', 'NOT IN' => 'NOT IN (...)', 'BETWEEN' => 'BETWEEN', 'NOT BETWEEN' => 'NOT BETWEEN', 'EXISTS' => 'EXISTS', 'NOT EXISTS' => 'NOT EXISTS', 'REGEXP' => 'REGEXP', 'NOT REGEXP' => 'NOT REGEXP'];
    }
    /**
     * @return array<string,string>
     */
    public static function get_post_formats()
    {
        return ['standard' => esc_html__('Standard', 'dynamic-content-for-elementor'), 'aside' => esc_html__('Aside', 'dynamic-content-for-elementor'), 'chat' => esc_html__('Chat', 'dynamic-content-for-elementor'), 'gallery' => esc_html__('Gallery', 'dynamic-content-for-elementor'), 'link' => esc_html__('Link', 'dynamic-content-for-elementor'), 'image' => esc_html__('Image', 'dynamic-content-for-elementor'), 'quote' => esc_html__('Quote', 'dynamic-content-for-elementor'), 'status' => esc_html__('Status', 'dynamic-content-for-elementor'), 'video' => esc_html__('Video', 'dynamic-content-for-elementor'), 'audio' => esc_html__('Audio', 'dynamic-content-for-elementor')];
    }
    /**
     * @return array<string,string>
     */
    public static function get_button_sizes()
    {
        return ['xs' => esc_html__('Extra Small', 'dynamic-content-for-elementor'), 'sm' => esc_html__('Small', 'dynamic-content-for-elementor'), 'md' => esc_html__('Medium', 'dynamic-content-for-elementor'), 'lg' => esc_html__('Large', 'dynamic-content-for-elementor'), 'xl' => esc_html__('Extra Large', 'dynamic-content-for-elementor')];
    }
    /**
     * @return array<string,string>
     */
    public static function get_jquery_display_mode()
    {
        return ['' => esc_html__('None', 'dynamic-content-for-elementor'), 'slide' => esc_html__('Slide', 'dynamic-content-for-elementor'), 'fade' => esc_html__('Fade', 'dynamic-content-for-elementor')];
    }
    /**
     * @param array<string> $tags_to_add
     * @param bool $add_none
     * @return array<string,string>
     */
    public static function get_html_tags(array $tags_to_add = [], bool $add_none = \false)
    {
        $default = ['h1' => 'H1', 'h2' => 'H2', 'h3' => 'H3', 'h4' => 'H4', 'h5' => 'H5', 'h6' => 'H6', 'div' => 'div', 'span' => 'span', 'p' => 'p'];
        if ($add_none) {
            $none = ['' => esc_html__('None', 'dynamic-content-for-elementor')];
            $default = \array_merge($none, $default);
        }
        $tags_to_add = \array_combine($tags_to_add, $tags_to_add);
        return \array_merge($default, $tags_to_add);
    }
}
