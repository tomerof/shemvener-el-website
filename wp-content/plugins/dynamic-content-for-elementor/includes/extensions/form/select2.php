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
class Select2 extends \DynamicContentForElementor\Extensions\ExtensionPrototype
{
    public $has_action = \false;
    /**
     * @return string
     */
    public function get_name()
    {
        return 'dce_form_select2';
    }
    /**
     * @return string
     */
    public function get_label()
    {
        return esc_html__('Select2', 'dynamic-content-for-elementor');
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
        add_action('elementor/frontend/before_render', array($this, 'render_form'));
        add_action('elementor/element/form/section_form_fields/before_section_end', [$this, 'update_fields_controls']);
        add_action('elementor/element/form/section_field_style/before_section_end', [$this, 'update_style_controls']);
        add_filter('elementor/widget/print_template', function ($template, $widget) {
            if ('form' === $widget->get_name()) {
                $template = \false;
            }
            return $template;
        }, 10, 2);
    }
    /**
     * @param \Elementor\Widget_Base $widget
     * @return void
     */
    public function render_form($widget)
    {
        if ('form' !== $widget->get_name()) {
            return;
        }
        $settings = $widget->get_settings_for_display();
        if (empty($settings['form_fields'])) {
            return;
        }
        $fields_data = [];
        foreach ($settings['form_fields'] as $field) {
            if ('select' !== $field['field_type'] || empty($field['field_select2'])) {
                continue;
            }
            $field_data = ['custom_id' => esc_attr($field['custom_id'])];
            if (!empty($field['field_select2_placeholder'])) {
                $field_data['placeholder'] = $field['field_select2_placeholder'];
            }
            $fields_data[] = $field_data;
        }
        if (empty($fields_data)) {
            return;
        }
        wp_enqueue_script('dce-select2');
        wp_enqueue_style('dce-select2');
        $config = ['fields' => $fields_data];
        $widget->add_render_attribute('_wrapper', 'data-dce-select2', wp_json_encode($config) ?: '');
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
        $control_data['fields']['field_select2'] = array('name' => 'field_select2', 'label' => esc_html__('Select2', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'separator' => 'before', 'return_value' => 'true', 'conditions' => ['terms' => [['name' => 'field_type', 'value' => 'select']]], 'tabs_wrapper' => 'form_fields_tabs', 'inner_tab' => 'form_fields_enchanted_tab', 'tab' => 'enchanted');
        $control_data['fields']['field_select2_placeholder'] = array('name' => 'field_select2_placeholder', 'label' => esc_html__('Placeholder', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'ai' => ['active' => \false], 'conditions' => ['terms' => [['name' => 'field_type', 'value' => 'select'], ['name' => 'field_select2', 'value' => 'true']]], 'tabs_wrapper' => 'form_fields_tabs', 'inner_tab' => 'form_fields_enchanted_tab', 'tab' => 'enchanted');
        $widget->update_control('form_fields', $control_data);
    }
    /**
     * @param \Elementor\Widget_Base $widget
     * @return void
     */
    public function update_style_controls($widget)
    {
        Helper::update_elementor_control($widget, 'field_background_color', function ($control_data) {
            $control_data['selectors']['{{WRAPPER}} .elementor-field-group .elementor-select-wrapper .select2'] = 'background-color: {{VALUE}};';
            $control_data['selectors']['{{WRAPPER}} .elementor-field-group .elementor-select-wrapper .select2 .elementor-field-textual'] = 'background-color: {{VALUE}};';
            $control_data['selectors']['{{WRAPPER}} .mce-panel'] = 'background-color: {{VALUE}};';
            return $control_data;
        });
        Helper::update_elementor_control($widget, 'field_text_color', function ($control_data) {
            $control_data['selectors']['{{WRAPPER}} .select2-container--default .select2-selection--single .select2-selection__rendered'] = 'color: {{VALUE}};';
            $control_data['selectors']['{{WRAPPER}} ..select2-container--default .select2-selection--multiple .select2-selection__rendered'] = 'color: {{VALUE}};';
            return $control_data;
        });
        Helper::update_elementor_control($widget, 'field_typography', function ($control_data) {
            if (!empty($control_data['selectors'])) {
                $values = \reset($control_data['selectors']);
                $control_data['selectors']['{{WRAPPER}} .select2-container--default .select2-selection--single .select2-selection__rendered'] = $values;
                $control_data['selectors']['{{WRAPPER}} .select2-container--default .select2-selection--single .select2-selection__rendered'] = $values;
                $control_data['selectors']['{{WRAPPER}} .select2-container--default .select2-selection--single, {{WRAPPER}} .select2-container--default .select2-selection--multiple'] = 'height: auto;';
            }
            return $control_data;
        });
        Helper::update_elementor_control($widget, 'field_border_color', function ($control_data) {
            $control_data['selectors']['{{WRAPPER}} .elementor-field-group .elementor-select-wrapper .select2'] = 'border-color: {{VALUE}};';
            $control_data['selectors']['{{WRAPPER}} .elementor-field-group .elementor-select-wrapper .select2 .elementor-field-textual'] = 'border-color: {{VALUE}};';
            $control_data['selectors']['{{WRAPPER}} .elementor-field-group .mce-panel'] = 'border-color: {{VALUE}};';
            return $control_data;
        });
        Helper::update_elementor_control($widget, 'field_border_width', function ($control_data) {
            $control_data['selectors']['{{WRAPPER}} .elementor-field-group .elementor-select-wrapper .select2'] = 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};';
            $control_data['selectors']['{{WRAPPER}} .elementor-field-group .elementor-select-wrapper .select2 .elementor-field-textual'] = 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};';
            $control_data['selectors']['{{WRAPPER}} .elementor-field-group .mce-panel'] = 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};';
            return $control_data;
        });
        Helper::update_elementor_control($widget, 'field_border_radius', function ($control_data) {
            $control_data['selectors']['{{WRAPPER}} .elementor-field-group .elementor-select-wrapper .select2'] = 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};';
            $control_data['selectors']['{{WRAPPER}} .elementor-field-group .elementor-select-wrapper .select2 .elementor-field-textual'] = 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};';
            $control_data['selectors']['{{WRAPPER}} .elementor-field-group .mce-panel'] = 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};';
            return $control_data;
        });
    }
}
