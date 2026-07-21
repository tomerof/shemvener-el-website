<?php

// SPDX-FileCopyrightText: 2018-2026 Ovation S.r.l. <help@dynamic.ooo>
// SPDX-License-Identifier: GPL-3.0-or-later
namespace DynamicContentForElementor\Extensions;

use DynamicContentForElementor\Helper;
use DynamicContentForElementor\Plugin;
class CookieAction extends \ElementorPro\Modules\Forms\Classes\Action_Base
{
    /**
     * @var bool
     */
    public $has_action = \true;
    public function run_once()
    {
        $save_guard = \DynamicContentForElementor\Plugin::instance()->save_guard;
        $save_guard->register_unsafe_control('form', 'dce_cookie_value');
        $save_guard->register_unsafe_control('form', 'dce_cookie_name');
        $save_guard->register_unsafe_control('form', 'dce_cookie_expiration');
    }
    /**
     * @return array<string>
     */
    public function get_script_depends()
    {
        return [];
    }
    /**
     * @return array<string>
     */
    public function get_style_depends()
    {
        return [];
    }
    /**
     * @var array<string>
     */
    public static $depended_plugins = ['elementor-pro'];
    public function get_name()
    {
        return 'cookie_action';
    }
    public function get_label()
    {
        return esc_html__('Cookie', 'dynamic-content-for-elementor');
    }
    /**
     * time interval represented as '2s', '4d' etc. to amount of seconds
     */
    public static function interval_to_seconds($str)
    {
        $timeSuffixes = ['s' => 1, 'm' => 60, 'h' => 3600, 'd' => 86400, 'y' => 31536000];
        $str = \trim($str);
        if (!\preg_match('/^\\d+[smhdy]?$/', $str)) {
            return \false;
        }
        $lc = \substr($str, -1);
        foreach ($timeSuffixes as $suffix => $factor) {
            if ($lc === $suffix) {
                return \substr($str, 0, -1) * $factor;
            }
        }
        return (int) $str;
    }
    public function register_settings_section($widget)
    {
        $widget->start_controls_section('dce_section_cookie', ['label' => Helper::dce_logo() . $this->get_label(), 'condition' => ['submit_actions' => $this->get_name()]]);
        $widget->add_control('dce_cookie_name', ['label' => esc_html__('Cookie Name', 'dynamic-content-for-elementor'), 'type' => \Elementor\Controls_Manager::TEXT, 'ai' => ['active' => \false], 'placeholder' => esc_html__('Enter cookie name', 'dynamic-content-for-elementor')]);
        $widget->add_control('dce_cookie_unset', ['label' => esc_html__('Unset the cookie', 'dynamic-content-for-elementor'), 'type' => \Elementor\Controls_Manager::SWITCHER]);
        $widget->add_control('dce_cookie_value_has_field', ['label' => esc_html__('The Cookie value needs to include a Form Field', 'dynamic-content-for-elementor'), 'type' => \Elementor\Controls_Manager::SWITCHER, 'condition' => ['dce_cookie_unset!' => 'yes']]);
        $widget->add_control('dce_cookie_text_template_warning', ['type' => \Elementor\Controls_Manager::RAW_HTML, 'raw' => \DynamicContentForElementor\Plugin::instance()->text_templates->get_default_value(['dynamic-shortcodes' => esc_html__('Use Dynamic Shortcodes to fetch form fields {form:field-id}. Put the shortcode directly as text input, do not use Dynamic Tags.', 'dynamic-content-for-elementor'), 'tokens' => esc_html__('Use tokens to fetch form fields ([form:field_name]). Put the tokens directly as text input, do not use Dynamic Tags.', 'dynamic-content-for-elementor')]), 'placeholder' => esc_html__('Enter cookie value', 'dynamic-content-for-elementor'), 'condition' => ['dce_cookie_value_has_field' => 'yes', 'dce_cookie_unset!' => 'yes']]);
        $widget->add_control('dce_cookie_value', ['label' => esc_html__('Cookie Value', 'dynamic-content-for-elementor'), 'type' => \Elementor\Controls_Manager::TEXT, 'ai' => ['active' => \false], 'placeholder' => esc_html__('Enter cookie value', 'dynamic-content-for-elementor'), 'condition' => ['dce_cookie_unset!' => 'yes']]);
        $widget->add_control('dce_cookie_expiration', ['label' => esc_html__('Cookie Expiration', 'dynamic-content-for-elementor'), 'type' => \Elementor\Controls_Manager::TEXT, 'ai' => ['active' => \false], 'description' => esc_html__('Enter expiration time in seconds, or with time unit. For example "2d" is 2 days, "2y" is 2 years.', 'dynamic-content-for-elementor'), 'default' => '1d', 'condition' => ['dce_cookie_unset!' => 'yes']]);
        $widget->end_controls_section();
    }
    public function on_export($element)
    {
        unset($element['settings']['cookie_name'], $element['settings']['cookie_value'], $element['settings']['cookie_expiration']);
        return $element;
    }
    public function run($record, $ajax_handler)
    {
        $settings = $record->get('form_settings');
        $cookie_name = $settings['dce_cookie_name'];
        $cookie_value = $settings['dce_cookie_value'];
        $cookie_expiration = $settings['dce_cookie_expiration'];
        $cookie_unset = $settings['dce_cookie_unset'];
        if ($cookie_unset === 'yes') {
            \setcookie($cookie_name, '', \time() - 3600);
            return;
        }
        if ($settings['dce_cookie_value_has_field'] === 'yes') {
            $fields = Helper::get_form_data($record);
            $cookie_value = Plugin::instance()->text_templates->expand_shortcodes_or_callback($cookie_value, ['form-fields' => $record->get('fields')], function ($str) use($fields) {
                return Helper::get_dynamic_value($str, $fields);
            });
        }
        $cookie_expiration = self::interval_to_seconds($cookie_expiration);
        if ($cookie_expiration === \false) {
            $msg = esc_html__('Cookie Action: The expiration is not valid', 'dynamic-content-for-elementor');
            $ajax_handler->add_admin_error_message($msg);
        }
        \setcookie($cookie_name, $cookie_value, \time() + $cookie_expiration, '/');
    }
}
