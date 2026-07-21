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
class FieldDescription extends \DynamicContentForElementor\Extensions\ExtensionPrototype
{
    public $has_action = \false;
    public $depended_scripts = [];
    public $depended_styles = ['dce-tooltip'];
    public function get_name()
    {
        return 'dce_form_description';
    }
    public function get_label()
    {
        return esc_html__('Description', 'dynamic-content-for-elementor');
    }
    /**
     * @return bool
     */
    public function is_common()
    {
        return \false;
    }
    public function add_assets_depends($form)
    {
        foreach ($this->depended_scripts as $script) {
            wp_enqueue_script($script);
        }
        foreach ($this->depended_styles as $style) {
            wp_enqueue_style($style);
        }
    }
    protected function add_actions()
    {
        add_action('elementor/widget/render_content', [$this, '_render_form'], 10, 2);
        add_action('elementor/element/form/section_form_fields/before_section_end', [$this, 'update_fields_controls']);
        add_action('elementor/element/form/section_field_style/before_section_end', [$this, 'update_style_controls']);
        add_action('elementor/element/form/section_button_style/after_section_end', array($this, 'add_form_description_style'));
        add_action('elementor/preview/enqueue_scripts', [$this, 'add_preview_depends']);
        add_filter('elementor/widget/print_template', function ($template, $widget) {
            if ('form' === $widget->get_name()) {
                $template = \false;
            }
            return $template;
        }, 10, 2);
        add_filter('wpml_elementor_widgets_to_translate', [$this, 'wpml_widgets_to_translate_filter'], 50, 1);
    }
    public function add_preview_depends()
    {
        foreach ($this->depended_scripts as $script) {
            wp_enqueue_script($script);
        }
        foreach ($this->depended_styles as $style) {
            wp_enqueue_style($style);
        }
    }
    public function _render_form($content, $widget)
    {
        if ($widget->get_name() != 'form') {
            return $content;
        }
        $settings = $widget->get_settings_for_display();
        $add_css = '<style>.elementor-element.elementor-element-' . esc_attr($widget->get_id()) . ' .elementor-field-group { align-self: flex-start; }</style>';
        $description_fields = [];
        foreach ($settings['form_fields'] as $field) {
            if (empty($field['field_description']) || $field['field_description_position'] === 'no-description') {
                continue;
            }
            $field_data = ['custom_id' => $field['custom_id'], 'position' => $field['field_description_position'], 'description' => wp_kses_post($field['field_description']), 'description_text' => wp_strip_all_tags($field['field_description'])];
            if ($field['field_description_position'] === 'elementor-field-label') {
                $field_data['tooltip'] = !empty($field['field_description_tooltip']);
                if ($field_data['tooltip']) {
                    $field_data['tooltip_position'] = !empty($field['field_description_tooltip_position']) ? $field['field_description_tooltip_position'] : 'top';
                }
            }
            $description_fields[] = $field_data;
        }
        if (empty($description_fields)) {
            return $content;
        }
        $this->add_assets_depends($widget);
        wp_enqueue_script('dce-field-description');
        $config = ['fields' => $description_fields];
        wp_add_inline_script('dce-field-description', 'window.dceFieldDescriptions=window.dceFieldDescriptions||{};' . 'window.dceFieldDescriptions[' . wp_json_encode($widget->get_id()) . ']=' . wp_json_encode($config, \JSON_HEX_TAG) . ';', 'before');
        return $content . $add_css;
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
        $excluded_field_types = ['dce_google_address_autocomplete'];
        $field_controls = ['field_description_position' => ['name' => 'field_description_position', 'label' => esc_html__('Description', 'dynamic-content-for-elementor'), 'separator' => 'before', 'type' => Controls_Manager::CHOOSE, 'options' => ['no-description' => ['title' => esc_html__('No Description', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-times'], 'elementor-field-label' => ['title' => esc_html__('On Label', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-tag'], 'elementor-field' => ['title' => esc_html__('Below Input', 'dynamic-content-for-elementor'), 'icon' => 'eicon-download-button']], 'toggle' => \false, 'default' => 'no-description', 'tabs_wrapper' => 'form_fields_tabs', 'inner_tab' => 'form_fields_enchanted_tab', 'tab' => 'enchanted', 'condition' => ['field_type!' => $excluded_field_types]], 'field_description_tooltip' => ['name' => 'field_description_tooltip', 'label' => esc_html__('Display as Tooltip', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'condition' => ['field_type!' => $excluded_field_types, 'field_description_position' => 'elementor-field-label'], 'tabs_wrapper' => 'form_fields_tabs', 'inner_tab' => 'form_fields_enchanted_tab', 'tab' => 'enchanted'], 'field_description_tooltip_position' => ['name' => 'field_description_tooltip_position', 'label' => esc_html__('Tooltip Position', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CHOOSE, 'options' => ['top' => ['title' => esc_html__('Top', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-angle-up'], 'left' => ['title' => esc_html__('Left', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-angle-left'], 'bottom' => ['title' => esc_html__('Bottom', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-angle-down'], 'right' => ['title' => esc_html__('Right', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-angle-right']], 'toggle' => \false, 'default' => 'top', 'condition' => ['field_type!' => $excluded_field_types, 'field_description_position' => 'elementor-field-label', 'field_description_tooltip!' => ''], 'tabs_wrapper' => 'form_fields_tabs', 'inner_tab' => 'form_fields_enchanted_tab', 'tab' => 'enchanted'], 'field_description' => ['name' => 'field_description', 'label' => esc_html__('Description HTML', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXTAREA, 'label_block' => \true, 'fa4compatibility' => 'icon', 'condition' => ['field_type!' => $excluded_field_types, 'field_description_position!' => 'no-description'], 'tabs_wrapper' => 'form_fields_tabs', 'inner_tab' => 'form_fields_enchanted_tab', 'tab' => 'enchanted']];
        $control_data['fields'] = \array_merge($control_data['fields'], $field_controls);
        $widget->update_control('form_fields', $control_data);
    }
    public function update_style_controls($widget)
    {
        Helper::update_elementor_control($widget, 'label_spacing', function ($control_data) {
            $control_data['selectors']['body.rtl {{WRAPPER}} .elementor-labels-inline .elementor-field-group > abbr'] = 'padding-left: {{SIZE}}{{UNIT}};';
            // for the label position = inline option
            $control_data['selectors']['body:not(.rtl) {{WRAPPER}} .elementor-labels-inline .elementor-field-group > abbr'] = 'padding-right: {{SIZE}}{{UNIT}};';
            // for the label position = inline option
            $control_data['selectors']['body {{WRAPPER}} .elementor-labels-above .elementor-field-group > abbr'] = 'padding-bottom: {{SIZE}}{{UNIT}};';
            // for the label position = above option
            return $control_data;
        });
    }
    public function add_form_description_style($widget)
    {
        $widget->start_controls_section('section_field_description_style', ['label' => '<span class="color-dce icon-dce-logo-dce pull-right ml-1"></span> ' . esc_html__('Field Description', 'dynamic-content-for-elementor'), 'tab' => Controls_Manager::TAB_STYLE]);
        $widget->add_control('field_description_color', ['label' => esc_html__('Description Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .elementor-field-input-description' => 'color: {{VALUE}};'], 'separator' => 'before']);
        $widget->add_group_control(Group_Control_Typography::get_type(), ['name' => 'field_description_typography', 'label' => esc_html__('Typography', 'dynamic-content-for-elementor'), 'selector' => '{{WRAPPER}} .elementor-field-input-description']);
        $widget->add_control('label_description_color', ['label' => esc_html__('Label Description Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .elementor-field-label-description .elementor-field-label' => 'display: inline-block;', '{{WRAPPER}} .elementor-field-label-description:after' => "\n\t\t\t\t\t\tcontent: '?';\n\t\t\t\t\t\tdisplay: inline-block;\n\t\t\t\t\t\tborder-radius: 50%;\n\t\t\t\t\t\tpadding: 2px 0;\n\t\t\t\t\t\theight: 1.2em;\n\t\t\t\t\t\tline-height: 1;\n\t\t\t\t\t\tfont-size: 80%;\n\t\t\t\t\t\twidth: 1.2em;\n\t\t\t\t\t\ttext-align: center;\n\t\t\t\t\t\tmargin-left: 0.2em;\n\t\t\t\t\t\tcolor: {{VALUE}};"], 'default' => '#ffffff']);
        $widget->add_control('label_description_bgcolor', ['label' => esc_html__('Label Description Background Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .elementor-field-label-description:after' => 'background-color: {{VALUE}};'], 'default' => '#777777']);
        $widget->end_controls_section();
    }
    /**
     * @param array<mixed> $widgets
     * @return array<mixed>
     */
    public function wpml_widgets_to_translate_filter($widgets)
    {
        if (!isset($widgets['form'])) {
            return $widgets;
        }
        $widgets['form']['fields_in_item']['form_fields'][] = ['field' => 'field_description', 'type' => esc_html__('Description', 'dynamic-content-for-elementor'), 'editor_type' => 'AREA'];
        return $widgets;
    }
}
