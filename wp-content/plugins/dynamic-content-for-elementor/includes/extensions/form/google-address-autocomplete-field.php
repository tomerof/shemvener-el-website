<?php

namespace DynamicContentForElementor\Extensions;

use Elementor\Controls_Manager;
use ElementorPro\Plugin;
use DynamicContentForElementor\Helper;
if (!\defined('ABSPATH')) {
    exit;
    // Exit if accessed directly
}
class GoogleAddressAutocompleteField extends \ElementorPro\Modules\Forms\Fields\Field_Base
{
    /**
     * @return string
     */
    public function get_name()
    {
        return 'Google Address Autocomplete';
    }
    /**
     * @return string
     */
    public function get_label()
    {
        return esc_html__('Google Address Autocomplete', 'dynamic-content-for-elementor');
    }
    /**
     * @return string
     */
    public function get_type()
    {
        return 'dce_form_google_autocomplete';
    }
    public function __construct()
    {
        add_filter('elementor/widget/print_template', function ($template, $widget) {
            if ('form' === $widget->get_name()) {
                $template = \false;
            }
            return $template;
        }, 10, 2);
        // Add auto-fill controls to compatible field types (text, tel, textarea, hidden)
        add_action('elementor/element/form/section_form_fields/before_section_end', [$this, 'add_autofill_controls_to_fields']);
        add_filter('elementor_pro/forms/render/item', [$this, 'render_autofill_attributes'], 10, 3);
        parent::__construct();
    }
    /**
     * @return array<string>
     */
    public function get_script_depends() : array
    {
        return ['dce-google-address-autocomplete-field'];
    }
    /**
     * @return array<string>
     */
    public function get_style_depends() : array
    {
        return ['dce-google-address-autocomplete-field'];
    }
    /**
     * @param \Elementor\Widget_Base $widget
     * @return void
     */
    public function update_controls($widget)
    {
        $elementor = Plugin::elementor();
        $control_data = $elementor->controls_manager->get_control_from_stack($widget->get_unique_name(), 'form_fields');
        if (is_wp_error($control_data)) {
            return;
        }
        $field_controls = ['dce_google_autocomplete_country' => ['name' => 'dce_google_autocomplete_country', 'label' => esc_html__('Restrict Country', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT2, 'options' => Helper::get_iso_3166_1_alpha_2(), 'multiple' => \true, 'description' => esc_html__('Select countries to restrict the autocomplete results. Leave empty for no restriction.', 'dynamic-content-for-elementor'), 'tab' => 'content', 'inner_tab' => 'form_fields_content_tab', 'tabs_wrapper' => 'form_fields_tabs', 'condition' => ['field_type' => $this->get_type()]], 'dce_google_autocomplete_placeholder' => ['name' => 'dce_google_autocomplete_placeholder', 'label' => esc_html__('Placeholder', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'default' => esc_html__('Search for a location...', 'dynamic-content-for-elementor'), 'tab' => 'content', 'inner_tab' => 'form_fields_content_tab', 'tabs_wrapper' => 'form_fields_tabs', 'condition' => ['field_type' => $this->get_type()]], 'dce_google_autocomplete_color_scheme' => ['name' => 'dce_google_autocomplete_color_scheme', 'label' => esc_html__('Color Scheme', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'default' => 'light', 'options' => ['auto' => esc_html__('Auto (System)', 'dynamic-content-for-elementor'), 'light' => esc_html__('Light', 'dynamic-content-for-elementor'), 'dark' => esc_html__('Dark', 'dynamic-content-for-elementor')], 'tab' => 'content', 'inner_tab' => 'form_fields_content_tab', 'tabs_wrapper' => 'form_fields_tabs', 'condition' => ['field_type' => $this->get_type()]], 'dce_google_autocomplete_style_notice' => ['name' => 'dce_google_autocomplete_style_notice', 'type' => Controls_Manager::RAW_HTML, 'raw' => esc_html__('Style options are limited because this field uses an official Google component.', 'dynamic-content-for-elementor'), 'content_classes' => 'elementor-panel-alert elementor-panel-alert-info', 'tab' => 'content', 'inner_tab' => 'form_fields_content_tab', 'tabs_wrapper' => 'form_fields_tabs', 'condition' => ['field_type' => $this->get_type()]]];
        $control_data['fields'] = $this->inject_field_controls($control_data['fields'], $field_controls);
        $widget->update_control('form_fields', $control_data);
    }
    /**
     * @param array<mixed> $item
     * @param int $item_index
     * @param \ElementorPro\Modules\Forms\Widgets\Form $form
     * @return void
     */
    public function render($item, $item_index, $form)
    {
        $attr_key = 'input' . $item_index;
        // Remove any incorrect type
        $form->remove_render_attribute($attr_key, 'type');
        $form->add_render_attribute($attr_key, 'class', 'data-google-autocomplete-fields');
        if (!empty($item['dce_google_autocomplete_country'])) {
            $country_json = wp_json_encode($item['dce_google_autocomplete_country']);
            if (\false !== $country_json) {
                $form->add_render_attribute($attr_key, 'data-google-autocomplete-fields', $country_json);
            }
        }
        if (!empty($item['dce_google_autocomplete_placeholder'])) {
            $form->add_render_attribute($attr_key, 'placeholder', $item['dce_google_autocomplete_placeholder']);
        }
        $color_scheme = $item['dce_google_autocomplete_color_scheme'] ?? 'light';
        $form->add_render_attribute($attr_key, 'data-color-scheme', $color_scheme);
        echo '<input ' . $form->get_render_attribute_string($attr_key) . '>';
    }
    /**
     * Add auto-fill controls to compatible field types in the enchanted tab.
     *
     * @param \Elementor\Widget_Base $widget
     * @return void
     */
    public function add_autofill_controls_to_fields($widget)
    {
        $elementor = Plugin::elementor();
        $control_data = $elementor->controls_manager->get_control_from_stack($widget->get_unique_name(), 'form_fields');
        if (is_wp_error($control_data)) {
            return;
        }
        $compatible_fields = ['text', 'textarea', 'hidden'];
        $field_controls = ['dce_autofill_google_heading' => ['name' => 'dce_autofill_google_heading', 'label' => esc_html__('Google Address Autocomplete', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HEADING, 'separator' => 'before', 'tabs_wrapper' => 'form_fields_tabs', 'inner_tab' => 'form_fields_enchanted_tab', 'tab' => 'enchanted', 'conditions' => ['terms' => [['name' => 'field_type', 'operator' => 'in', 'value' => $compatible_fields]]]], 'dce_autofill_google' => ['name' => 'dce_autofill_google', 'label' => esc_html__('Auto-fill from Google Autocomplete', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'description' => esc_html__('Automatically fill this field with address data when user selects an address in a Google Address Autocomplete field.', 'dynamic-content-for-elementor'), 'tabs_wrapper' => 'form_fields_tabs', 'inner_tab' => 'form_fields_enchanted_tab', 'tab' => 'enchanted', 'conditions' => ['terms' => [['name' => 'field_type', 'operator' => 'in', 'value' => $compatible_fields]]]], 'dce_autofill_google_components' => ['name' => 'dce_autofill_google_components', 'label' => esc_html__('Fill With', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT2, 'multiple' => \true, 'label_block' => \true, 'options' => ['street' => esc_html__('Street', 'dynamic-content-for-elementor'), 'street_number' => esc_html__('Street Number', 'dynamic-content-for-elementor'), 'postal_code' => esc_html__('Postal Code', 'dynamic-content-for-elementor'), 'city' => esc_html__('City', 'dynamic-content-for-elementor'), 'province' => esc_html__('Province', 'dynamic-content-for-elementor'), 'region' => esc_html__('Region', 'dynamic-content-for-elementor'), 'country' => esc_html__('Country', 'dynamic-content-for-elementor')], 'description' => esc_html__('Select which address components to use. Multiple selections will be concatenated.', 'dynamic-content-for-elementor'), 'tabs_wrapper' => 'form_fields_tabs', 'inner_tab' => 'form_fields_enchanted_tab', 'tab' => 'enchanted', 'conditions' => ['terms' => [['name' => 'field_type', 'operator' => 'in', 'value' => $compatible_fields], ['name' => 'dce_autofill_google', 'value' => 'yes']]]], 'dce_autofill_google_separator' => ['name' => 'dce_autofill_google_separator', 'label' => esc_html__('Separator', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'default' => ', ', 'description' => esc_html__('Character(s) to use between multiple components.', 'dynamic-content-for-elementor'), 'tabs_wrapper' => 'form_fields_tabs', 'inner_tab' => 'form_fields_enchanted_tab', 'tab' => 'enchanted', 'conditions' => ['terms' => [['name' => 'field_type', 'operator' => 'in', 'value' => $compatible_fields], ['name' => 'dce_autofill_google', 'value' => 'yes']]]], 'dce_autofill_google_source' => ['name' => 'dce_autofill_google_source', 'label' => esc_html__('Source Field', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'default' => 'auto', 'options' => ['auto' => esc_html__('Automatic (first found)', 'dynamic-content-for-elementor'), 'select' => esc_html__('Select by ID', 'dynamic-content-for-elementor')], 'tabs_wrapper' => 'form_fields_tabs', 'inner_tab' => 'form_fields_enchanted_tab', 'tab' => 'enchanted', 'conditions' => ['terms' => [['name' => 'field_type', 'operator' => 'in', 'value' => $compatible_fields], ['name' => 'dce_autofill_google', 'value' => 'yes']]]], 'dce_autofill_google_source_id' => ['name' => 'dce_autofill_google_source_id', 'label' => esc_html__('Source Field ID', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'description' => esc_html__('Enter the ID of the Google Address Autocomplete field.', 'dynamic-content-for-elementor'), 'tabs_wrapper' => 'form_fields_tabs', 'inner_tab' => 'form_fields_enchanted_tab', 'tab' => 'enchanted', 'conditions' => ['terms' => [['name' => 'field_type', 'operator' => 'in', 'value' => $compatible_fields], ['name' => 'dce_autofill_google', 'value' => 'yes'], ['name' => 'dce_autofill_google_source', 'value' => 'select']]]]];
        foreach ($field_controls as $field_control) {
            $control_data['fields'][$field_control['name']] = $field_control;
        }
        $widget->update_control('form_fields', $control_data);
    }
    /**
     * Add data attributes to fields that should be auto-filled.
     *
     * @param array<string,mixed> $item
     * @param int $item_index
     * @param \ElementorPro\Modules\Forms\Widgets\Form $form
     * @return array<string,mixed>
     */
    public function render_autofill_attributes($item, $item_index, $form)
    {
        if (empty($item['dce_autofill_google']) || $item['dce_autofill_google'] !== 'yes') {
            return $item;
        }
        if (empty($item['dce_autofill_google_components']) || !\is_array($item['dce_autofill_google_components'])) {
            return $item;
        }
        $components = \implode(',', $item['dce_autofill_google_components']);
        $form->add_render_attribute('input' . $item_index, 'data-autofill-type', $components);
        $form->add_render_attribute('textarea' . $item_index, 'data-autofill-type', $components);
        // Add separator if specified
        $separator = $item['dce_autofill_google_separator'] ?? ', ';
        $form->add_render_attribute('input' . $item_index, 'data-autofill-separator', $separator);
        $form->add_render_attribute('textarea' . $item_index, 'data-autofill-separator', $separator);
        // If a specific source field is specified
        if (!empty($item['dce_autofill_google_source']) && $item['dce_autofill_google_source'] === 'select' && !empty($item['dce_autofill_google_source_id'])) {
            $source_id = 'form-field-' . $item['dce_autofill_google_source_id'];
            $form->add_render_attribute('input' . $item_index, 'data-trigger-selector', $source_id);
            $form->add_render_attribute('textarea' . $item_index, 'data-trigger-selector', $source_id);
        }
        return $item;
    }
}
