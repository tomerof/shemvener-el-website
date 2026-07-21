<?php

// SPDX-Copyright: 2018-2025 Ovation S.r.l. <help@dynamic.ooo>
// SPDX-License-Identifier: GPL-3.0-or-later
namespace DynamicContentForElementor\Extensions;

use Elementor\Controls_Manager;
use ElementorPro\Plugin;
use Elementor\Icons_Manager;
if (!\defined('ABSPATH')) {
    exit;
}
class Rating extends \ElementorPro\Modules\Forms\Fields\Field_Base
{
    /**
     * @var array<string>
     */
    public $depended_scripts = ['dce-rating'];
    /**
     * @var array<string>
     */
    public $depended_styles = ['dce-rating'];
    /**
     * @return void
     */
    public function __construct()
    {
        add_action('elementor/element/form/section_steps_style/after_section_end', [$this, 'add_style_controls']);
        add_filter('elementor/widget/print_template', function ($template, $widget) {
            if ('form' === $widget->get_name()) {
                $template = \false;
            }
            return $template;
        }, 10, 2);
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
        return esc_html__('Rating', 'dynamic-content-for-elementor');
    }
    /**
     * @return string
     */
    public function get_type()
    {
        return 'dce_rating';
    }
    /**
     * Add custom controls to the form fields repeater
     *
     * @param \Elementor\Widget_Base $widget The Form widget instance
     * @return void
     */
    public function update_controls($widget)
    {
        $elementor = Plugin::elementor();
        $control_data = $elementor->controls_manager->get_control_from_stack($widget->get_unique_name(), 'form_fields');
        if (is_wp_error($control_data)) {
            return;
        }
        $field_controls = ['dce_rating_max' => ['name' => 'dce_rating_max', 'label' => esc_html__('Max Icons', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::NUMBER, 'step' => 1, 'min' => 1, 'default' => 5, 'conditions' => ['terms' => [['name' => 'field_type', 'value' => $this->get_type()]]], 'tabs_wrapper' => 'form_fields_tabs', 'tab' => 'content', 'inner_tab' => 'form_fields_content_tab'], 'dce_rating_allow_clear' => ['name' => 'dce_rating_allow_clear', 'label' => esc_html__('Allow Clear', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => '', 'frontend_available' => \true, 'conditions' => ['terms' => [['name' => 'field_type', 'value' => $this->get_type()]]], 'tabs_wrapper' => 'form_fields_tabs', 'tab' => 'content', 'inner_tab' => 'form_fields_content_tab'], 'dce_rating_half_selection' => ['name' => 'dce_rating_half_selection', 'label' => esc_html__('Half Selection', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => '', 'description' => esc_html__('Allow half ratings like 2.5 or 4.5 stars.', 'dynamic-content-for-elementor'), 'frontend_available' => \true, 'conditions' => ['terms' => [['name' => 'field_type', 'value' => $this->get_type()]]], 'tabs_wrapper' => 'form_fields_tabs', 'tab' => 'content', 'inner_tab' => 'form_fields_content_tab'], 'dce_rating_icon' => ['name' => 'dce_rating_icon', 'label' => esc_html__('Rating Icon', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::ICONS, 'default' => ['value' => 'fas fa-star', 'library' => 'fa-solid'], 'recommended' => ['fa-solid' => ['star', 'heart', 'thumbs-up', 'fire', 'gem'], 'fa-regular' => ['star', 'heart', 'thumbs-up']], 'conditions' => ['terms' => [['name' => 'field_type', 'value' => $this->get_type()]]], 'tabs_wrapper' => 'form_fields_tabs', 'tab' => 'content', 'inner_tab' => 'form_fields_content_tab']];
        $control_data['fields'] = $this->inject_field_controls($control_data['fields'], $field_controls);
        $widget->update_control('form_fields', $control_data);
    }
    /**
     * @param array<string,mixed> $item Field settings
     * @param int $item_index Field index in the form
     * @param \ElementorPro\Modules\Forms\Widgets\Form $form Form widget instance
     * @return void
     */
    public function render($item, $item_index, $form)
    {
        $max = isset($item['dce_rating_max']) && \is_numeric($item['dce_rating_max']) ? (int) $item['dce_rating_max'] : 5;
        if ($max < 1) {
            $max = 1;
        }
        // Store numeric rating in the main input; UI is enhanced by JS/CSS.
        $form->set_render_attribute('input' . $item_index, 'type', 'number');
        $form->add_render_attribute('input' . $item_index, 'class', 'dce-rating-value');
        $form->add_render_attribute('input' . $item_index, 'data-max', (string) $max);
        $form->add_render_attribute('input' . $item_index, 'data-allow-clear', !empty($item['dce_rating_allow_clear']) ? 'yes' : '');
        $form->add_render_attribute('input' . $item_index, 'data-half-selection', !empty($item['dce_rating_half_selection']) ? 'yes' : '');
        $form->add_render_attribute('input' . $item_index, 'step', '0.5');
        $form->add_render_attribute('input' . $item_index, 'min', '0');
        $form->add_render_attribute('input' . $item_index, 'max', (string) $max);
        $form->add_render_attribute('input' . $item_index, 'style', 'display: none;');
        $wrapper_key = 'rating-wrapper' . $item_index;
        $form->add_render_attribute($wrapper_key, 'class', 'dce-rating');
        $form->add_render_attribute($wrapper_key, 'data-max', (string) $max);
        // Get icon data
        $icon_data = isset($item['dce_rating_icon']) ? $item['dce_rating_icon'] : [];
        $icon_value = isset($icon_data['value']) ? $icon_data['value'] : '';
        $icon_library = isset($icon_data['library']) ? $icon_data['library'] : '';
        $form->add_render_attribute($wrapper_key, 'data-icon', $icon_value);
        $form->add_render_attribute($wrapper_key, 'data-icon-library', $icon_library);
        echo '<div ' . $form->get_render_attribute_string($wrapper_key) . '>';
        for ($i = 1; $i <= $max; $i++) {
            $icon_html = $this->render_icon_html($icon_value, $icon_library);
            echo '<span class="dce-rating-icon" data-value="' . esc_attr((string) $i) . '">' . $icon_html . '</span>';
        }
        echo '</div>';
        echo '<input ' . $form->get_render_attribute_string('input' . $item_index) . '>';
    }
    /**
     * Sanitize the submitted rating value
     *
     * Rounds the value based on half selection setting and clamps it within valid range.
     *
     * @param string $value Submitted value
     * @param array<string,mixed> $field Field settings
     * @return string Sanitized value
     */
    public function sanitize_field($value, $field)
    {
        $max = isset($field['dce_rating_max']) && \is_numeric($field['dce_rating_max']) ? (int) $field['dce_rating_max'] : 5;
        $allow_half = !empty($field['dce_rating_half_selection']);
        $val = \floatval($value);
        if ($allow_half) {
            // Round to nearest 0.5
            $val = \round($val * 2) / 2;
        } else {
            // Round to nearest whole number
            $val = \round($val);
        }
        if ($val < 0) {
            $val = 0;
        }
        if ($val > $max) {
            $val = $max;
        }
        return (string) $val;
    }
    /**
     * Render the HTML for a single icon
     *
     * Uses Elementor's Icons_Manager when available, falls back to
     * manual rendering or a star character.
     *
     * @param string $icon_value Icon class or SVG identifier
     * @param string $icon_library Icon library name
     * @return string Icon HTML
     */
    private function render_icon_html($icon_value, $icon_library)
    {
        if (empty($icon_value) || empty($icon_library)) {
            $icon_value = 'fas fa-star';
            $icon_library = 'fa-solid';
        }
        $icon_data = ['value' => $icon_value, 'library' => $icon_library];
        $icon_html = Icons_Manager::try_get_icon_html($icon_data, ['aria-hidden' => 'true']);
        if (empty($icon_html)) {
            return '<i class="' . esc_attr($icon_value) . '"></i>';
        }
        return $icon_html;
    }
    /**
     * Add style controls to the Form widget
     *
     * Adds a "Rating" section in the Style tab with controls for
     * icon size, spacing, colors, and alignment.
     *
     * @param \Elementor\Widget_Base $widget The Form widget instance
     * @return void
     */
    public function add_style_controls($widget)
    {
        $widget->start_controls_section('dce_rating_section_style', ['label' => '<span class="color-dce icon-dce-logo-dce pull-right ml-1"></span> ' . esc_html__('Rating', 'dynamic-content-for-elementor'), 'tab' => Controls_Manager::TAB_STYLE]);
        $widget->add_responsive_control('dce_rating_icon_size', ['label' => esc_html__('Icon Size', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'range' => ['px' => ['min' => 10, 'max' => 50, 'step' => 1]], 'default' => ['size' => 24], 'size_units' => ['px'], 'selectors' => ['{{WRAPPER}} .elementor-field-type-dce_rating .dce-rating-icon' => 'font-size: {{SIZE}}{{UNIT}};']]);
        $widget->add_responsive_control('dce_rating_icon_spacing', ['label' => esc_html__('Icon Spacing', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'range' => ['px' => ['min' => 0, 'max' => 20, 'step' => 1]], 'default' => ['size' => 5], 'size_units' => ['px'], 'selectors' => ['{{WRAPPER}} .elementor-field-type-dce_rating .dce-rating' => 'gap: {{SIZE}}{{UNIT}};']]);
        $widget->add_control('dce_rating_icon_color', ['label' => esc_html__('Icon Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'default' => '#ddd', 'selectors' => ['{{WRAPPER}} .elementor-field-type-dce_rating .dce-rating-icon' => 'color: {{VALUE}};', '{{WRAPPER}} .elementor-field-type-dce_rating .dce-rating-icon svg' => 'fill: {{VALUE}};']]);
        $widget->add_control('dce_rating_icon_active_color', ['label' => esc_html__('Active Icon Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'default' => '#ffc107', 'selectors' => ['{{WRAPPER}} .elementor-field-type-dce_rating .dce-rating-icon.is-active' => 'color: {{VALUE}};', '{{WRAPPER}} .elementor-field-type-dce_rating .dce-rating-icon.is-active svg' => 'fill: {{VALUE}};', '{{WRAPPER}} .elementor-field-type-dce_rating .dce-rating-icon .dce-rating-half-overlay' => 'color: {{VALUE}};', '{{WRAPPER}} .elementor-field-type-dce_rating .dce-rating-icon .dce-rating-half-overlay svg' => 'fill: {{VALUE}};']]);
        $widget->add_responsive_control('dce_rating_alignment', ['label' => esc_html__('Alignment', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CHOOSE, 'options' => ['flex-start' => ['title' => esc_html__('Left', 'dynamic-content-for-elementor'), 'icon' => 'eicon-align-start-h'], 'center' => ['title' => esc_html__('Center', 'dynamic-content-for-elementor'), 'icon' => 'eicon-align-center-h'], 'flex-end' => ['title' => esc_html__('Right', 'dynamic-content-for-elementor'), 'icon' => 'eicon-align-end-h']], 'selectors' => ['{{WRAPPER}} .elementor-field-type-dce_rating .dce-rating' => 'justify-content: {{VALUE}};']]);
        $widget->end_controls_section();
    }
}
