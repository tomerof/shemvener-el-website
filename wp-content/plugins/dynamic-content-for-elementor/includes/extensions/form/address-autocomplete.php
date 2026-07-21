<?php

// SPDX-FileCopyrightText: 2018-2026 Ovation S.r.l. <help@dynamic.ooo>
// SPDX-License-Identifier: GPL-3.0-or-later
namespace DynamicContentForElementor\Extensions;

use Elementor\Controls_Manager;
use DynamicContentForElementor\Helper;
if (!\defined('ABSPATH')) {
    exit;
    // Exit if accessed directly
}
class AddressAutocomplete extends \DynamicContentForElementor\Extensions\ExtensionPrototype
{
    public $has_action = \false;
    public $depended_scripts = ['dce-form-address-autocomplete'];
    public $depended_styles = [];
    public function get_name()
    {
        return 'dce_form_address_autocomplete';
    }
    /**
     * @return bool
     */
    public function is_common()
    {
        return \false;
    }
    protected function add_actions()
    {
        add_action('elementor/element/form/section_form_fields/before_section_end', [$this, 'update_fields_controls'], 99);
        add_action('elementor-pro/forms/pre_render', [$this, 'add_assets_depends'], 10, 2);
    }
    public function add_assets_depends($instance, $form)
    {
        $autocomplete_fields = [];
        // fetch all the settings data we need to pass to the JavaScript code:
        foreach ($instance['form_fields'] as $field) {
            if ($field['field_type'] == 'text' && !empty($field['field_address'])) {
                $new_field = ['id' => $field['custom_id']];
                if (!empty($field['field_address_restrict_country'])) {
                    $new_field['country'] = $field['field_address_restrict_country'];
                }
                $autocomplete_fields[] = $new_field;
            }
        }
        if (!empty($autocomplete_fields)) {
            $form->add_render_attribute('wrapper', 'data-autocomplete-fields', wp_json_encode($autocomplete_fields));
            foreach ($this->depended_scripts as $script) {
                wp_enqueue_script($script);
            }
            foreach ($this->depended_styles as $style) {
                wp_enqueue_style($style);
            }
        }
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
        $field_controls = [];
        $field_controls['field_address_heading'] = ['name' => 'field_address_heading', 'label' => esc_html__('Address Autocomplete (Legacy)', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HEADING, 'separator' => 'before', 'conditions' => ['terms' => [['name' => 'field_type', 'value' => 'text']]], 'tabs_wrapper' => 'form_fields_tabs', 'inner_tab' => 'form_fields_enchanted_tab', 'tab' => 'enchanted'];
        $field_controls['field_address_legacy_notice'] = ['name' => 'field_address_legacy_notice', 'type' => Controls_Manager::NOTICE, 'notice_type' => 'warning', 'content' => esc_html__('This feature is deprecated. Please use the new "Google Address Autocomplete" field type instead, which offers better functionality and is actively maintained.', 'dynamic-content-for-elementor'), 'conditions' => ['terms' => [['name' => 'field_type', 'value' => 'text']]], 'tabs_wrapper' => 'form_fields_tabs', 'inner_tab' => 'form_fields_enchanted_tab', 'tab' => 'enchanted'];
        if (!get_option('dce_google_maps_api')) {
            $field_controls['field_address_api_notice'] = ['name' => 'field_address_api_notice', 'type' => Controls_Manager::RAW_HTML, 'raw' => esc_html__('In order to use Address Autocomplete you should set Google Maps API, with Geocoding API enabled, on Integrations section', 'dynamic-content-for-elementor'), 'content_classes' => 'elementor-panel-alert elementor-panel-alert-warning', 'conditions' => ['terms' => [['name' => 'field_type', 'value' => 'text']]], 'tabs_wrapper' => 'form_fields_tabs', 'inner_tab' => 'form_fields_enchanted_tab', 'tab' => 'enchanted'];
        }
        $field_controls += ['field_address' => ['name' => 'field_address', 'label' => esc_html__('Address Autocomplete', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'return_value' => 'true', 'default' => '', 'conditions' => ['terms' => [['name' => 'field_type', 'value' => 'text']]], 'tabs_wrapper' => 'form_fields_tabs', 'inner_tab' => 'form_fields_enchanted_tab', 'tab' => 'enchanted'], 'field_address_restrict_country' => ['name' => 'field_address_restrict_country', 'label' => esc_html__('Restrict Country', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT2, 'options' => Helper::get_iso_3166_1_alpha_2(), 'multiple' => \true, 'conditions' => ['terms' => [['name' => 'field_type', 'value' => 'text'], ['name' => 'field_address', 'operator' => '!=', 'value' => '']]], 'tabs_wrapper' => 'form_fields_tabs', 'inner_tab' => 'form_fields_enchanted_tab', 'tab' => 'enchanted']];
        $control_data['fields'] = \array_merge($control_data['fields'], $field_controls);
        $widget->update_control('form_fields', $control_data);
    }
}
