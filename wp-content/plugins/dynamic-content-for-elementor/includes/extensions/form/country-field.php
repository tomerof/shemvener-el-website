<?php

// SPDX-FileCopyrightText: 2018-2026 Ovation S.r.l. <help@dynamic.ooo>
// SPDX-License-Identifier: GPL-3.0-or-later
namespace DynamicContentForElementor\Extensions;

use Elementor\Controls_Manager;
use ElementorPro\Plugin;
use DynamicContentForElementor\Helper;
if (!\defined('ABSPATH')) {
    exit;
}
class CountryField extends \ElementorPro\Modules\Forms\Fields\Field_Base
{
    /**
     * @var bool
     */
    public $has_action = \false;
    /**
     * @var array<string>
     */
    public $depended_scripts = ['dce-country-field'];
    /**
     * @var array<string>
     */
    public $depended_styles = ['dce-select2'];
    /**
     * @return array<string>
     */
    public function get_script_depends() : array
    {
        return $this->depended_scripts;
    }
    /**
     * @return array<string>
     */
    public function get_style_depends() : array
    {
        return $this->depended_styles;
    }
    /**
     * @return string
     */
    public function get_name()
    {
        return esc_html__('Country', 'dynamic-content-for-elementor');
    }
    /**
     * @return string
     */
    public function get_type()
    {
        return 'dce_form_country';
    }
    /**
     * @param \ElementorPro\Modules\Forms\Widgets\Form $widget
     * @return void
     */
    public function update_controls($widget)
    {
        $elementor = Plugin::elementor();
        $control_data = $elementor->controls_manager->get_control_from_stack($widget->get_unique_name(), 'form_fields');
        if (is_wp_error($control_data)) {
            return;
        }
        $countries = Helper::get_iso_3166_1_alpha_2();
        $field_controls = ['dce_country_default' => ['name' => 'dce_country_default', 'label' => esc_html__('Default Country', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'options' => ['' => esc_html__('None', 'dynamic-content-for-elementor')] + $countries, 'default' => '', 'conditions' => ['terms' => [['name' => 'field_type', 'value' => $this->get_type()]]], 'tabs_wrapper' => 'form_fields_tabs', 'tab' => 'content', 'inner_tab' => 'form_fields_content_tab'], 'dce_country_placeholder' => ['name' => 'dce_country_placeholder', 'label' => esc_html__('Placeholder', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'default' => esc_html__('Select a country', 'dynamic-content-for-elementor'), 'conditions' => ['terms' => [['name' => 'field_type', 'value' => $this->get_type()]]], 'tabs_wrapper' => 'form_fields_tabs', 'tab' => 'content', 'inner_tab' => 'form_fields_content_tab'], 'dce_country_blacklist' => ['name' => 'dce_country_blacklist', 'label' => esc_html__('Blacklist Countries', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT2, 'options' => $countries, 'multiple' => \true, 'description' => esc_html__('Select countries to exclude from the list', 'dynamic-content-for-elementor'), 'conditions' => ['terms' => [['name' => 'field_type', 'value' => $this->get_type()]]], 'tabs_wrapper' => 'form_fields_tabs', 'tab' => 'content', 'inner_tab' => 'form_fields_content_tab'], 'dce_country_exclude_territories' => ['name' => 'dce_country_exclude_territories', 'label' => esc_html__('Exclude Territories', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'description' => esc_html__('Exclude non-country territories like Antarctica (AQ), Bouvet Island (BV), etc.', 'dynamic-content-for-elementor'), 'conditions' => ['terms' => [['name' => 'field_type', 'value' => $this->get_type()]]], 'tabs_wrapper' => 'form_fields_tabs', 'tab' => 'content', 'inner_tab' => 'form_fields_content_tab'], 'dce_country_pin' => ['name' => 'dce_country_pin', 'label' => esc_html__('Pin Countries', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT2, 'options' => $countries, 'multiple' => \true, 'description' => esc_html__('Select countries to show at the top of the list', 'dynamic-content-for-elementor'), 'conditions' => ['terms' => [['name' => 'field_type', 'value' => $this->get_type()]]], 'tabs_wrapper' => 'form_fields_tabs', 'tab' => 'content', 'inner_tab' => 'form_fields_content_tab'], 'dce_country_allow_multiple' => ['name' => 'dce_country_allow_multiple', 'label' => esc_html__('Multiple Selection', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'return_value' => 'true', 'conditions' => ['terms' => [['name' => 'field_type', 'value' => $this->get_type()]]], 'tabs_wrapper' => 'form_fields_tabs', 'tab' => 'content', 'inner_tab' => 'form_fields_content_tab'], 'dce_country_use_select2' => ['name' => 'dce_country_use_select2', 'label' => esc_html__('Use Select2', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'description' => esc_html__('Enable Select2 for enhanced search and styling', 'dynamic-content-for-elementor'), 'conditions' => ['terms' => [['name' => 'field_type', 'value' => $this->get_type()]]], 'tabs_wrapper' => 'form_fields_tabs', 'tab' => 'content', 'inner_tab' => 'form_fields_content_tab']];
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
        $countries = Helper::get_iso_3166_1_alpha_2();
        if (!empty($item['dce_country_blacklist'])) {
            $blacklist_codes = \is_array($item['dce_country_blacklist']) ? $item['dce_country_blacklist'] : [];
            $countries = \array_diff_key($countries, \array_flip($blacklist_codes));
        }
        if (!empty($item['dce_country_exclude_territories'])) {
            $territories = $this->get_excluded_territories();
            $countries = \array_diff_key($countries, \array_flip($territories));
        }
        $pinned_countries = [];
        if (!empty($item['dce_country_pin'])) {
            $pin_codes = \is_array($item['dce_country_pin']) ? $item['dce_country_pin'] : [];
            foreach ($pin_codes as $code) {
                if (isset($countries[$code])) {
                    $pinned_countries[$code] = $countries[$code];
                    unset($countries[$code]);
                }
            }
        }
        $countries = $pinned_countries + $countries;
        $placeholder = !empty($item['dce_country_placeholder']) ? $item['dce_country_placeholder'] : '';
        $is_multiple = !empty($item['dce_country_allow_multiple']);
        $field_name = $form->get_attribute_name($item) . ($is_multiple ? '[]' : '');
        $form->add_render_attribute(['select-wrapper' . $item_index => ['class' => ['elementor-field', 'elementor-select-wrapper', esc_attr($item['css_classes'])]], 'select' . $item_index => ['name' => $field_name, 'id' => $form->get_attribute_id($item), 'class' => ['elementor-field-textual', 'elementor-size-' . $item['input_size']], 'data-dce-country-field' => 'true', 'data-dce-country-placeholder' => $placeholder]]);
        // Add attribute only when Select2 is disabled
        if (empty($item['dce_country_use_select2'])) {
            $form->add_render_attribute('select' . $item_index, 'data-dce-no-select2', '');
        }
        if ($is_multiple) {
            $form->add_render_attribute('select' . $item_index, 'multiple', 'multiple');
        }
        if ($item['required']) {
            $form->add_render_attribute('select' . $item_index, 'required', 'required');
            $form->add_render_attribute('select' . $item_index, 'aria-required', 'true');
        }
        $default_country = !empty($item['dce_country_default']) ? $item['dce_country_default'] : '';
        $selected_values = !empty($item['field_value']) ? (array) $item['field_value'] : [];
        if (empty($selected_values) && !empty($default_country)) {
            $selected_values = [$default_country];
        }
        ?>
		<div <?php 
        echo $form->get_render_attribute_string('select-wrapper' . $item_index);
        ?>>
			<select <?php 
        echo $form->get_render_attribute_string('select' . $item_index);
        ?>>
			<?php 
        if ($placeholder && !$is_multiple) {
            ?>
				<option value=""><?php 
            echo esc_html($placeholder);
            ?></option>
			<?php 
        }
        ?>
				<?php 
        foreach ($countries as $code => $name) {
            ?>
					<option value="<?php 
            echo esc_attr($code);
            ?>" <?php 
            selected(\in_array($code, $selected_values, \true), \true);
            ?>>
						<?php 
            echo esc_html($name);
            ?>
					</option>
				<?php 
        }
        ?>
			</select>
		</div>
		<?php 
    }
    /**
     * @param string|array<string> $value
     * @param array<mixed> $field
     * @return string|array<string>
     */
    public function sanitize_field($value, $field)
    {
        if (\is_array($value)) {
            return \array_map('sanitize_text_field', $value);
        }
        return sanitize_text_field($value);
    }
    /**
     * Get territory codes to exclude
     * @return array<string>
     */
    private function get_excluded_territories()
    {
        return ['AQ', 'BV', 'HM', 'GS', 'UM', 'TF', 'IO'];
    }
}
