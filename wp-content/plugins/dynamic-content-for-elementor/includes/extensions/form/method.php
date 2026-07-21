<?php

// SPDX-FileCopyrightText: 2018-2026 Ovation S.r.l. <help@dynamic.ooo>
// SPDX-License-Identifier: GPL-3.0-or-later
namespace DynamicContentForElementor\Extensions;

use Elementor\Controls_Manager;
use Elementor\Controls_Stack;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;
use Elementor\Icons_Manager;
use DynamicContentForElementor\Helper;
if (!\defined('ABSPATH')) {
    exit;
    // Exit if accessed directly
}
class Method extends \DynamicContentForElementor\Extensions\ExtensionPrototype
{
    public $has_action = \false;
    /**
     * Get Name
     *
     * Return the action name
     *
     * @access public
     * @return string
     */
    public function get_name()
    {
        return 'dce_form_method';
    }
    /**
     * @return bool
     */
    public function is_common()
    {
        return \false;
    }
    /**
     * Get Label
     *
     * Returns the action label
     *
     * @access public
     * @return string
     */
    public function get_label()
    {
        return esc_html__('Method', 'dynamic-content-for-elementor');
    }
    /**
     * Add Actions
     *
     * @since 0.5.5
     *
     * @access private
     */
    protected function add_actions()
    {
        add_action('elementor/widget/render_content', array($this, '_render_form'), 10, 2);
        add_action('elementor/element/form/section_form_options/after_section_start', [$this, 'add_controls_to_form']);
        add_filter('elementor/widget/print_template', function ($template, $widget) {
            if ('form' === $widget->get_name()) {
                $template = \false;
            }
            return $template;
        }, 10, 2);
    }
    public function _render_form($content, $widget)
    {
        if ($widget->get_name() != 'form') {
            return $content;
        }
        $settings = $widget->get_settings_for_display();
        if (empty($settings['form_method']) || $settings['form_method'] === 'ajax') {
            return $content;
        }
        // Replace field names for non-AJAX forms
        foreach ($settings['form_fields'] as $field) {
            $custom_id_escaped = esc_attr($field['custom_id']);
            $content = \str_replace('form_fields[' . $custom_id_escaped . ']', $custom_id_escaped, $content);
        }
        // Change method to GET if specified
        if ($settings['form_method'] === 'get') {
            $method_escaped = esc_attr($settings['form_method']);
            $content = \str_replace('method="post"', 'method="' . $method_escaped . '"', $content);
        }
        // Add action URL
        if (!empty($settings['form_action']['url'])) {
            $url_escaped = esc_url($settings['form_action']['url']);
            $content = \str_replace('<form ', '<form action="' . $url_escaped . '" ', $content);
        } else {
            $content = \str_replace('<form ', '<form action="" ', $content);
        }
        // Add custom attributes
        if (!empty($settings['form_action']['custom_attributes'])) {
            $attr_str = '';
            $attrs = Helper::str_to_array(',', $settings['form_action']['custom_attributes']);
            if (!empty($attrs)) {
                foreach ($attrs as $anattr) {
                    $parts = \explode('|', $anattr, 2);
                    if (\count($parts) === 2) {
                        $attr = sanitize_key(\trim($parts[0]));
                        $value = esc_attr(\trim($parts[1]));
                        if ($attr && $value) {
                            $attr_str .= $attr . '="' . $value . '" ';
                        }
                    }
                }
            }
            if ($attr_str) {
                $content = \str_replace('<form ', '<form ' . $attr_str, $content);
            }
        }
        // Add target="_blank" if external
        if (!empty($settings['form_action']['is_external'])) {
            $content = \str_replace('<form ', '<form target="_blank" ', $content);
        }
        // Add rel="nofollow" if specified
        if (!empty($settings['form_action']['nofollow'])) {
            $content = \str_replace('<form ', '<form rel="nofollow" ', $content);
        }
        // Enqueue JS to prevent Elementor AJAX submit
        wp_enqueue_script('dce-method-form');
        $config = ['method' => $settings['form_method']];
        $widget->add_render_attribute('_wrapper', 'data-dce-form-method', wp_json_encode($config));
        return $content;
    }
    public function add_controls_to_form($widget)
    {
        if (!\DynamicContentForElementor\Helper::can_register_unsafe_controls()) {
            return;
        }
        $widget->add_control('form_method', ['label' => '<span class="color-dce icon-dce-logo-dce"></span> ' . esc_html__('Method', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'options' => ['ajax' => esc_html__('AJAX (Default)', 'dynamic-content-for-elementor'), 'post' => 'POST', 'get' => 'GET'], 'toggle' => \false, 'default' => 'ajax']);
        $widget->add_control('form_action_hide', ['type' => Controls_Manager::RAW_HTML, 'raw' => esc_html__('Using this method, all form Actions After Submit, validations, conditional fields and saving signature will not work!', 'dynamic-content-for-elementor'), 'content_classes' => 'elementor-panel-alert elementor-panel-alert-warning', 'condition' => ['form_method!' => 'ajax']]);
        $widget->add_control('form_action', ['label' => esc_html__('Action', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::URL, 'condition' => ['form_method!' => 'ajax']]);
    }
}
