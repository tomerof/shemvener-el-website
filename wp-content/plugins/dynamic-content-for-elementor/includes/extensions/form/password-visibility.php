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
class PasswordVisibility extends \DynamicContentForElementor\Extensions\ExtensionPrototype
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
        return 'dce_form_password_visibility';
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
        return esc_html__('Password Visibility', 'dynamic-content-for-elementor');
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
        // TODO Change hook to render_field
        add_action('elementor/widget/render_content', array($this, '_render_form'), 10, 2);
        add_action('elementor/element/form/section_form_fields/before_section_end', [$this, 'update_fields_controls']);
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
        $password_fields = [];
        foreach ($settings['form_fields'] as $field) {
            if ($field['field_type'] === 'password' && !empty($field['field_psw_visiblity'])) {
                $password_fields[] = $field['custom_id'];
            }
        }
        if (empty($password_fields)) {
            return $content;
        }
        wp_enqueue_style('font-awesome');
        wp_enqueue_script('dce-password-visibility');
        $config = ['fields' => $password_fields];
        $widget->add_render_attribute('_wrapper', 'data-dce-password-visibility', wp_json_encode($config));
        return $content;
    }
    public function update_fields_controls($widget)
    {
        if (!\DynamicContentForElementor\Helper::can_register_unsafe_controls()) {
            return;
        }
        $elementor = \ElementorPro\Plugin::elementor();
        $control_data = $elementor->controls_manager->get_control_from_stack($widget->get_unique_name(), 'form_fields');
        if (is_wp_error($control_data)) {
            return;
        }
        $field_controls = ['field_psw_visiblity' => ['name' => 'field_psw_visiblity', 'label' => esc_html__('Password Visibility', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'return_value' => 'true', 'separator' => 'before', 'default' => 'true', 'conditions' => ['terms' => [['name' => 'field_type', 'value' => 'password']]], 'tabs_wrapper' => 'form_fields_tabs', 'inner_tab' => 'form_fields_enchanted_tab', 'tab' => 'enchanted']];
        $control_data['fields'] = \array_merge($control_data['fields'], $field_controls);
        $widget->update_control('form_fields', $control_data);
    }
}
