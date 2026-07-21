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
class InlineAlign extends \DynamicContentForElementor\Extensions\ExtensionPrototype
{
    /**
     * @var bool
     */
    public $has_action = \false;
    /**
     * @return string
     */
    public function get_name()
    {
        return 'dce_form_inline_align';
    }
    /**
     * @return string
     */
    public function get_label()
    {
        return esc_html__('Inline align', 'dynamic-content-for-elementor');
    }
    /**
     * @return bool
     */
    public function is_common()
    {
        return \false;
    }
    /**
     * @return void
     */
    protected function add_actions()
    {
        add_action('elementor/frontend/widget/before_render', array($this, '_before_render_form'));
        add_action('elementor/element/form/section_form_fields/before_section_end', [$this, 'update_fields_controls']);
        add_filter('elementor/widget/print_template', function ($template, $widget) {
            if ('form' === $widget->get_name()) {
                $template = \false;
            }
            return $template;
        }, 10, 2);
    }
    /**
     * @return array<string>
     */
    public function get_script_depends()
    {
        return ['dce-inline-align'];
    }
    /**
     * @param \Elementor\Widget_Base $widget
     * @return void
     */
    public function _before_render_form($widget)
    {
        if ($widget->get_name() != 'form') {
            return;
        }
        $settings = $widget->get_settings_for_display();
        $inline_fields = [];
        foreach ($settings['form_fields'] as $field) {
            if (($field['field_type'] === 'radio' || $field['field_type'] === 'checkbox') && !empty($field['inline_align'])) {
                $inline_fields[] = ['custom_id' => sanitize_html_class($field['custom_id']), 'field_id' => sanitize_html_class($field['_id'])];
            }
        }
        if (empty($inline_fields)) {
            return;
        }
        // Add data attribute for JS to process
        $widget->add_render_attribute('_wrapper', 'data-dce-inline-align', wp_json_encode(['fields' => $inline_fields]) ?: '');
    }
    /**
     * @param \Elementor\Widget_Base $widget
     * @return void
     */
    public function update_fields_controls($widget)
    {
        $elementor = \ElementorPro\Plugin::elementor();
        $control_data = $elementor->controls_manager->get_control_from_stack($widget->get_unique_name(), 'form_fields');
        if (is_wp_error($control_data)) {
            return;
        }
        $field_controls = ['inline_align' => ['name' => 'inline_align', 'label' => esc_html__('Inline align', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CHOOSE, 'separator' => 'before', 'options' => ['flex-start' => ['title' => esc_html__('Left', 'dynamic-content-for-elementor'), 'icon' => 'eicon-text-align-left'], 'center' => ['title' => esc_html__('Center', 'dynamic-content-for-elementor'), 'icon' => 'eicon-text-align-center'], 'flex-end' => ['title' => esc_html__('Right', 'dynamic-content-for-elementor'), 'icon' => 'eicon-text-align-right'], 'space-around' => ['title' => esc_html__('Around', 'dynamic-content-for-elementor'), 'icon' => 'eicon-text-align-justify'], 'space-evenly' => ['title' => esc_html__('Evenly', 'dynamic-content-for-elementor'), 'icon' => 'eicon-text-align-justify'], 'space-between' => ['title' => esc_html__('Between', 'dynamic-content-for-elementor'), 'icon' => 'eicon-text-align-justify']], 'render_type' => 'template', 'condition' => ['field_type' => ['checkbox', 'radio'], 'inline_list!' => ''], 'tabs_wrapper' => 'form_fields_tabs', 'inner_tab' => 'form_fields_enchanted_tab', 'tab' => 'enchanted']];
        $control_data['fields'] = \array_merge($control_data['fields'], $field_controls);
        $widget->update_control('form_fields', $control_data);
    }
}
