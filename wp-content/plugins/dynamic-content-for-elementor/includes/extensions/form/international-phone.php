<?php

// SPDX-FileCopyrightText: 2018-2026 Ovation S.r.l. <help@dynamic.ooo>
// SPDX-License-Identifier: GPL-3.0-or-later
namespace DynamicContentForElementor\Extensions;

use ElementorPro\Plugin as ProPlugin;
use Elementor\Controls_Manager;
use DynamicContentForElementor\Helper;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Box_Shadow;
if (!\defined('ABSPATH')) {
    exit;
    // Exit if accessed directly
}
class InternationalPhone extends \ElementorPro\Modules\Forms\Fields\Field_Base
{
    /**
     * @var array<string>
     */
    public $depended_scripts = ['dce-international-phone-field'];
    /**
     * @var array<string>
     */
    public $depended_styles = ['dce-international-phone'];
    public function __construct()
    {
        add_action('elementor/element/form/section_steps_style/after_section_end', [$this, 'add_style_controls']);
        add_filter('elementor/widget/print_template', function ($template, $widget) {
            if ('form' === $widget->get_name()) {
                $template = \false;
            }
            return $template;
        }, 10, 2);
        add_filter('wpml_elementor_widgets_to_translate', [$this, 'wpml_widgets_to_translate_filter'], 50, 1);
        parent::__construct();
    }
    /**
     * @return array<string>
     */
    public function get_script_depends() : array
    {
        return $this->depended_scripts;
    }
    /**
     * Get the field display name.
     *
     * @return string
     */
    public function get_name()
    {
        return 'International Phone';
    }
    /**
     * Get the field type identifier.
     *
     * @return string
     */
    public function get_type()
    {
        return 'international_phone';
    }
    /**
     * @return array<string>
     */
    public function get_style_depends() : array
    {
        return $this->depended_styles;
    }
    /**
     * Register field-specific controls in the form fields repeater.
     *
     * @param \Elementor\Widget_Base $widget The form widget instance.
     * @return void
     */
    public function update_controls($widget)
    {
        $field_controlsor = ProPlugin::elementor();
        $control_data = $field_controlsor->controls_manager->get_control_from_stack($widget->get_unique_name(), 'form_fields');
        if (is_wp_error($control_data)) {
            return;
        }
        $field_controls = ['dce_int_phone_default_country' => ['name' => 'dce_int_phone_default_country', 'label' => esc_html__('Default Country', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'options' => Helper::get_iso_3166_1_alpha_2(), 'default' => 'US', 'conditions' => ['terms' => [['name' => 'field_type', 'value' => $this->get_type()]]], 'frontend_available' => \true, 'tabs_wrapper' => 'form_fields_tabs', 'tab' => 'content', 'inner_tab' => 'form_fields_content_tab'], 'dce_int_phone_validation' => ['name' => 'dce_int_phone_validation', 'label' => esc_html__('Enable Validation', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'description' => esc_html__('Validate the phone number format', 'dynamic-content-for-elementor'), 'default' => 'yes', 'conditions' => ['terms' => [['name' => 'field_type', 'value' => $this->get_type()]]], 'frontend_available' => \true, 'tabs_wrapper' => 'form_fields_tabs', 'tab' => 'content', 'inner_tab' => 'form_fields_content_tab'], 'dce_int_phone_prefix_label_mode' => ['name' => 'dce_int_phone_prefix_label_mode', 'label' => esc_html__('Prefix Display', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'description' => esc_html__('Choose how the country prefix is displayed in the select.', 'dynamic-content-for-elementor'), 'options' => ['name_prefix' => esc_html__('Country name + prefix (United States (+1))', 'dynamic-content-for-elementor'), 'code_prefix' => esc_html__('Country code + prefix (US (+1))', 'dynamic-content-for-elementor'), 'name_only' => esc_html__('Country name only (United States)', 'dynamic-content-for-elementor'), 'code_only' => esc_html__('Country code only (US)', 'dynamic-content-for-elementor'), 'prefix_name' => esc_html__('Prefix + name (+1 — United States)', 'dynamic-content-for-elementor')], 'default' => 'name_prefix', 'conditions' => ['terms' => [['name' => 'field_type', 'value' => $this->get_type()]]], 'frontend_available' => \true, 'tabs_wrapper' => 'form_fields_tabs', 'tab' => 'content', 'inner_tab' => 'form_fields_content_tab'], 'dce_int_phone_show_example' => ['name' => 'dce_int_phone_show_example', 'label' => esc_html__('Show Example Number', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'description' => esc_html__('Show an example phone number as placeholder', 'dynamic-content-for-elementor'), 'default' => 'yes', 'conditions' => ['terms' => [['name' => 'field_type', 'value' => $this->get_type()]]], 'frontend_available' => \true, 'tabs_wrapper' => 'form_fields_tabs', 'tab' => 'content', 'inner_tab' => 'form_fields_content_tab']];
        $control_data['fields'] = $this->inject_field_controls($control_data['fields'], $field_controls);
        $widget->update_control('form_fields', $control_data);
    }
    /**
     * Register style controls for the International Phone field.
     *
     * @param \Elementor\Widget_Base $widget The form widget instance.
     * @return void
     */
    public function add_style_controls($widget)
    {
        $widget->start_controls_section('dce_int_phone_section_style', ['label' => '<span class="color-dce icon-dce-logo-dce pull-right ml-1"></span> ' . esc_html__('International Phone', 'dynamic-content-for-elementor'), 'tab' => Controls_Manager::TAB_STYLE]);
        $widget->add_control('dce_int_phone_heading_wrapper', ['label' => esc_html__('Wrapper', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HEADING]);
        $widget->add_responsive_control('dce_int_phone_wrapper_gap', ['label' => esc_html__('Gap Between Fields', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'size_units' => ['px', 'em', 'rem'], 'range' => ['px' => ['min' => 0, 'max' => 50]], 'default' => ['size' => 10, 'unit' => 'px'], 'selectors' => ['{{WRAPPER}} .elementor-field-type-international_phone .dce-international-phone-wrapper' => 'gap: {{SIZE}}{{UNIT}};']]);
        $widget->add_control('dce_int_phone_heading_country_select', ['label' => esc_html__('Country Select', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HEADING, 'separator' => 'before']);
        $widget->add_responsive_control('dce_int_phone_country_width', ['label' => esc_html__('Width', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'size_units' => ['px', '%', 'em', 'rem'], 'range' => ['px' => ['min' => 50, 'max' => 400], '%' => ['min' => 10, 'max' => 50]], 'selectors' => ['{{WRAPPER}} .elementor-field-type-international_phone .dce-international-phone-country' => 'width: {{SIZE}}{{UNIT}};']]);
        $widget->add_group_control(Group_Control_Typography::get_type(), ['name' => 'dce_int_phone_country_typography', 'label' => esc_html__('Typography', 'dynamic-content-for-elementor'), 'selector' => '{{WRAPPER}} .elementor-field-type-international_phone .dce-international-phone-country']);
        $widget->add_responsive_control('dce_int_phone_country_padding', ['label' => esc_html__('Padding', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => ['px', '%', 'em'], 'selectors' => ['{{WRAPPER}} .elementor-field-type-international_phone .dce-international-phone-country' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};']]);
        $widget->add_control('dce_int_phone_country_color', ['label' => esc_html__('Text Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .elementor-field-type-international_phone .dce-international-phone-country' => 'color: {{VALUE}};']]);
        $widget->add_group_control(Group_Control_Background::get_type(), ['name' => 'dce_int_phone_country_background', 'types' => ['classic', 'gradient'], 'selector' => '{{WRAPPER}} .elementor-field-type-international_phone .dce-international-phone-country']);
        $widget->add_group_control(Group_Control_Border::get_type(), ['name' => 'dce_int_phone_country_border', 'label' => esc_html__('Border', 'dynamic-content-for-elementor'), 'selector' => '{{WRAPPER}} .elementor-field-type-international_phone .dce-international-phone-country']);
        $widget->add_control('dce_int_phone_country_border_radius', ['label' => esc_html__('Border Radius', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => ['px', '%'], 'selectors' => ['{{WRAPPER}} .elementor-field-type-international_phone .dce-international-phone-country' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};']]);
        $widget->add_group_control(Group_Control_Box_Shadow::get_type(), ['name' => 'dce_int_phone_country_box_shadow', 'selector' => '{{WRAPPER}} .elementor-field-type-international_phone .dce-international-phone-country']);
        $widget->add_control('dce_int_phone_heading_phone_input', ['label' => esc_html__('Phone Input', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HEADING, 'separator' => 'before']);
        $widget->add_group_control(Group_Control_Typography::get_type(), ['name' => 'dce_int_phone_input_typography', 'label' => esc_html__('Typography', 'dynamic-content-for-elementor'), 'selector' => '{{WRAPPER}} .elementor-field-type-international_phone .dce-international-phone-number']);
        $widget->add_responsive_control('dce_int_phone_input_padding', ['label' => esc_html__('Padding', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => ['px', '%', 'em'], 'selectors' => ['{{WRAPPER}} .elementor-field-type-international_phone .dce-international-phone-number' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};']]);
        $widget->add_control('dce_int_phone_input_color', ['label' => esc_html__('Text Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .elementor-field-type-international_phone .dce-international-phone-number' => 'color: {{VALUE}};']]);
        $widget->add_control('dce_int_phone_input_placeholder_color', ['label' => esc_html__('Placeholder Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .elementor-field-type-international_phone .dce-international-phone-number::placeholder' => 'color: {{VALUE}};']]);
        $widget->add_group_control(Group_Control_Background::get_type(), ['name' => 'dce_int_phone_input_background', 'types' => ['classic', 'gradient'], 'selector' => '{{WRAPPER}} .elementor-field-type-international_phone .dce-international-phone-number']);
        $widget->add_group_control(Group_Control_Border::get_type(), ['name' => 'dce_int_phone_input_border', 'label' => esc_html__('Border', 'dynamic-content-for-elementor'), 'selector' => '{{WRAPPER}} .elementor-field-type-international_phone .dce-international-phone-number']);
        $widget->add_control('dce_int_phone_input_border_radius', ['label' => esc_html__('Border Radius', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => ['px', '%'], 'selectors' => ['{{WRAPPER}} .elementor-field-type-international_phone .dce-international-phone-number' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};']]);
        $widget->add_group_control(Group_Control_Box_Shadow::get_type(), ['name' => 'dce_int_phone_input_box_shadow', 'selector' => '{{WRAPPER}} .elementor-field-type-international_phone .dce-international-phone-number']);
        $widget->end_controls_section();
    }
    /**
     * Render the International Phone field HTML.
     *
     * Outputs a wrapper containing a country select dropdown, a phone number
     * input, and a hidden field for the E.164 formatted value.
     *
     * @param array<string,mixed> $item Field settings from the repeater.
     * @param int $item_index Index of the field in the form.
     * @param \ElementorPro\Modules\Forms\Widgets\Form $form The form widget instance.
     * @return void
     */
    public function render($item, $item_index, $form)
    {
        // Get settings with defaults
        $default_country = $item['dce_int_phone_default_country'] ?? 'US';
        $validation = isset($item['dce_int_phone_validation']) ? $item['dce_int_phone_validation'] === 'yes' : \true;
        $prefix_label_mode = $item['dce_int_phone_prefix_label_mode'] ?? 'name_prefix';
        $show_example = isset($item['dce_int_phone_show_example']) ? $item['dce_int_phone_show_example'] === 'yes' : \true;
        // Create options array for JavaScript
        $options = ['defaultCountry' => $default_country, 'validation' => $validation, 'prefixLabelMode' => $prefix_label_mode, 'showExample' => $show_example];
        // Get country data
        $calling_codes = Helper::get_country_calling_codes();
        $country_names = Helper::get_iso_3166_1_alpha_2();
        // Build select options
        $select_options = [];
        foreach ($calling_codes as $code => $prefix) {
            $label = $this->get_country_option_label($code, $prefix, $prefix_label_mode, $country_names);
            $select_options[$code] = ['label' => $label, 'prefix' => $prefix];
        }
        // Sort by label
        \uasort($select_options, function ($a, $b) {
            return \strcmp($a['label'], $b['label']);
        });
        // Get field name and id for the hidden input
        $field_name = 'form_fields[' . $item['custom_id'] . ']';
        $field_id = 'form-field-' . $item['custom_id'];
        // Wrapper attributes
        $wrapper_key = 'wrapper' . $item_index;
        $form->add_render_attribute($wrapper_key, 'class', 'dce-international-phone-wrapper');
        $options_json = wp_json_encode($options);
        if (\false !== $options_json) {
            $form->add_render_attribute($wrapper_key, 'data-options', $options_json);
        }
        // Select attributes
        $select_key = 'select' . $item_index;
        $form->add_render_attribute($select_key, 'class', 'elementor-field dce-international-phone-country');
        // Phone input attributes
        $phone_key = 'phone' . $item_index;
        $form->add_render_attribute($phone_key, 'type', 'tel');
        $form->add_render_attribute($phone_key, 'class', 'elementor-field dce-international-phone-number');
        $form->add_render_attribute($phone_key, 'placeholder', esc_attr__('Phone number', 'dynamic-content-for-elementor'));
        // Render
        echo '<div ' . $form->get_render_attribute_string($wrapper_key) . '>';
        echo '<select ' . $form->get_render_attribute_string($select_key) . '>';
        foreach ($select_options as $code => $data) {
            $selected = $code === $default_country ? ' selected' : '';
            echo '<option value="' . esc_attr($code) . '" data-prefix="' . esc_attr($data['prefix']) . '"' . $selected . '>' . esc_html($data['label']) . '</option>';
        }
        echo '</select>';
        echo '<input ' . $form->get_render_attribute_string($phone_key) . '>';
        // Render hidden input manually to avoid Elementor's type attribute
        echo '<input type="hidden" name="' . esc_attr($field_name) . '" id="' . esc_attr($field_id) . '" class="dce-international-phone-value">';
        echo '</div>';
    }
    /**
     * Get the label for a country option based on the display mode.
     *
     * @param string $code Country code (e.g. 'IT')
     * @param string $prefix Calling code (e.g. '39')
     * @param string $mode Display mode
     * @param array<string,string> $country_names Map of country codes to names
     * @return string
     */
    private function get_country_option_label($code, $prefix, $mode, $country_names)
    {
        $name = $country_names[$code] ?? $code;
        switch ($mode) {
            case 'code_prefix':
                return $code . ' (+' . $prefix . ')';
            case 'name_only':
                return $name;
            case 'code_only':
                return $code;
            case 'prefix_name':
                return '+' . $prefix . ' — ' . $name;
            case 'name_prefix':
            default:
                return $name . ' (+' . $prefix . ')';
        }
    }
    /**
     * WPML compatibility - no translatable strings for this field type.
     *
     * @param array<mixed> $widgets
     * @return array<mixed>
     */
    public function wpml_widgets_to_translate_filter($widgets)
    {
        return $widgets;
    }
}
