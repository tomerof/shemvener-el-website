<?php

// SPDX-FileCopyrightText: 2018-2026 Ovation S.r.l. <help@dynamic.ooo>
// SPDX-License-Identifier: GPL-3.0-or-later
namespace DynamicContentForElementor\Extensions;

use ElementorPro\Plugin;
if (!\defined('ABSPATH')) {
    exit;
    // Exit if accessed directly
}
class FormattedNumber extends \ElementorPro\Modules\Forms\Fields\Field_Base
{
    public $depended_scripts = ['dce-formatted-number'];
    /**
     * @return array<string>
     */
    public function get_script_depends() : array
    {
        return $this->depended_scripts;
    }
    public function get_name()
    {
        return esc_html__('Formatted Number', 'dynamic-content-for-elementor');
    }
    public function get_type()
    {
        return 'dce_formatted_number';
    }
    /**
     * @return array<string>
     */
    public function get_style_depends() : array
    {
        return [];
    }
    public function update_controls($widget)
    {
        $elementor = Plugin::elementor();
        $control_data = $elementor->controls_manager->get_control_from_stack($widget->get_unique_name(), 'form_fields');
        if (is_wp_error($control_data)) {
            return;
        }
        $field_controls = ['dce_formatted_number_locale' => ['name' => 'dce_formatted_number_locale', 'label' => esc_html__('Locale', 'dynamic-content-for-elementor'), 'type' => \Elementor\Controls_Manager::TEXT, 'default' => 'auto', 'description' => esc_html__('The language to use for formatting options, auto means use the language of the browser. Otherwise it’s an BCP 47 language tag, like en-US for english as used in the USA, or it for italian.', 'dynamic-content-for-elementor'), 'tab' => 'content', 'inner_tab' => 'form_fields_content_tab', 'tabs_wrapper' => 'form_fields_tabs', 'condition' => ['field_type' => $this->get_type()]], 'dce_formatted_number_style' => ['name' => 'dce_formatted_number_style', 'label' => esc_html__('Style', 'dynamic-content-for-elementor'), 'type' => \Elementor\Controls_Manager::SELECT, 'default' => 'currency', 'options' => ['currency' => 'Currency', 'decimal' => 'Decimal', 'percent' => 'Percent'], 'tab' => 'content', 'inner_tab' => 'form_fields_content_tab', 'tabs_wrapper' => 'form_fields_tabs', 'condition' => ['field_type' => $this->get_type()]], 'dce_formatted_number_currency' => ['name' => 'dce_formatted_number_currency', 'label' => esc_html__('Currency', 'dynamic-content-for-elementor'), 'type' => \Elementor\Controls_Manager::TEXT, 'default' => 'USD', 'description' => esc_html__('ISO 4217 code like USD or EUR', 'dynamic-content-for-elementor'), 'tab' => 'content', 'inner_tab' => 'form_fields_content_tab', 'tabs_wrapper' => 'form_fields_tabs', 'condition' => ['field_type' => $this->get_type(), 'dce_formatted_number_style' => 'currency']]];
        $control_data['fields'] = $this->inject_field_controls($control_data['fields'], $field_controls);
        $widget->update_control('form_fields', $control_data);
    }
    public function render($item, $item_index, $form)
    {
        $method = $form->get_settings('form_method');
        if ($method === 'post' || $method === 'get') {
            echo '<p><span class="elementor-message elementor-message-danger elementor-help-inline elementor-form-help-inline" role="alert">';
            echo esc_html__('Formatted Number is not compatible with the Method Extension Post and Get options.', 'dynamic-content-for-elementor');
            echo '</span></p>';
            return;
        }
        $form->add_render_attribute('input' . $item_index, 'data-style', $item['dce_formatted_number_style']);
        $form->add_render_attribute('input' . $item_index, 'data-locale', $item['dce_formatted_number_locale']);
        $form->add_render_attribute('input' . $item_index, 'data-currency', $item['dce_formatted_number_currency']);
        $form->set_render_attribute('input' . $item_index, 'type', 'hidden');
        $form->add_render_attribute('input' . $item_index, 'class', 'dce-format-real-input');
        $form->add_render_attribute('input-interactive' . $item_index, 'class', 'elementor-field');
        $form->add_render_attribute('input-interactive' . $item_index, 'class', 'elementor-field-textual');
        $form->add_render_attribute('input-interactive' . $item_index, 'class', 'dce-format-interactive-input');
        echo '<input ' . $form->get_render_attribute_string('input' . $item_index) . '>';
        echo '<input ' . $form->get_render_attribute_string('input-interactive' . $item_index) . '>';
    }
}
