<?php

// SPDX-FileCopyrightText: 2018-2026 Ovation S.r.l. <help@dynamic.ooo>
// SPDX-License-Identifier: GPL-3.0-or-later
namespace DynamicContentForElementor\Extensions;

use ElementorPro\Plugin as ProPlugin;
use Elementor\Controls_Manager;
use Elementor\Controls_Stack;
use DynamicContentForElementor\Helper;
use DynamicContentForElementor\Tokens;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Box_Shadow;
if (!\defined('ABSPATH')) {
    exit;
    // Exit if accessed directly
}
class Amount extends \ElementorPro\Modules\Forms\Fields\Field_Base
{
    public $depended_scripts = ['dce-amount-field'];
    /**
     * @var int
     */
    private static $field_index = 0;
    /**
     * @return int
     */
    private function get_field_index()
    {
        return self::$field_index++;
    }
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
    public function run_once()
    {
        $save_guard = \DynamicContentForElementor\Plugin::instance()->save_guard;
        $save_guard->register_unsafe_control('form', 'form_fields::dce_amount_expression');
        add_action('wp_enqueue_scripts', function () {
            wp_localize_script('dce-amount-field', 'amountFieldLocale', ['syntaxError' => esc_html__('Your formula in Amount Field contains errors. Check the field and correct the errors. You can find the errors in the console of your browser', 'dynamic-content-for-elementor')]);
        }, 100);
    }
    /**
     * @return array<string>
     */
    public function get_script_depends() : array
    {
        return $this->depended_scripts;
    }
    public function get_name()
    {
        return 'Calculator';
    }
    public function get_type()
    {
        return 'amount';
    }
    /**
     * @return array<string>
     */
    public function get_style_depends() : array
    {
        return $this->depended_styles;
    }
    public function update_controls($widget)
    {
        if (!\DynamicContentForElementor\Helper::can_register_unsafe_controls()) {
            return;
        }
        $field_controlsor = ProPlugin::elementor();
        $control_data = $field_controlsor->controls_manager->get_control_from_stack($widget->get_unique_name(), 'form_fields');
        if (is_wp_error($control_data)) {
            return;
        }
        $field_controls = ['dce_amount_expression' => ['name' => 'dce_amount_expression', 'label' => esc_html__('Calculator Expression', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'ai' => ['active' => \false], 'placeholder' => esc_html__('[form:field_1] * [form:field_2] + 1.4', 'dynamic-content-for-elementor'), 'label_block' => \true, 'conditions' => ['terms' => [['name' => 'field_type', 'value' => $this->get_type()]]], 'frontend_available' => \true, 'tabs_wrapper' => 'form_fields_tabs', 'tab' => 'content', 'inner_tab' => 'form_fields_content_tab'], 'dce_amount_text_before' => ['name' => 'dce_amount_text_before', 'label' => esc_html__('Text Before', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'ai' => ['active' => \false], 'conditions' => ['terms' => [['name' => 'field_type', 'value' => $this->get_type()]]], 'frontend_available' => \true, 'tabs_wrapper' => 'form_fields_tabs', 'tab' => 'content', 'inner_tab' => 'form_fields_content_tab'], 'dce_amount_text_after' => ['name' => 'dce_amount_text_after', 'label' => esc_html__('Text After', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'ai' => ['active' => \false], 'conditions' => ['terms' => [['name' => 'field_type', 'value' => $this->get_type()]]], 'frontend_available' => \true, 'tabs_wrapper' => 'form_fields_tabs', 'tab' => 'content', 'inner_tab' => 'form_fields_content_tab'], 'dce_amount_hide' => ['name' => 'dce_amount_hide', 'label' => esc_html__('Hide Amount', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'description' => esc_html__('Do not display Amount value in frontend form, use its value only in Actions (like Email)', 'dynamic-content-for-elementor'), 'conditions' => ['terms' => [['name' => 'field_type', 'value' => $this->get_type()]]], 'frontend_available' => \true, 'tabs_wrapper' => 'form_fields_tabs', 'tab' => 'content', 'inner_tab' => 'form_fields_content_tab'], 'dce_amount_should_round' => ['name' => 'dce_amount_should_round', 'label' => esc_html__('Round result', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'condition' => ['field_type' => $this->get_type()], 'tabs_wrapper' => 'form_fields_tabs', 'tab' => 'content', 'inner_tab' => 'form_fields_content_tab'], 'dce_amount_round_precision' => ['name' => 'dce_amount_round_precision', 'label' => esc_html__('Round Precision', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::NUMBER, 'default' => 2, 'condition' => ['field_type' => $this->get_type(), 'dce_amount_should_round' => 'yes'], 'tabs_wrapper' => 'form_fields_tabs', 'tab' => 'content', 'inner_tab' => 'form_fields_content_tab'], 'dce_amount_should_format' => ['name' => 'dce_amount_should_format', 'label' => esc_html__('Format Number', 'dynamic-content-for-elementor'), 'description' => esc_html__('Format depends on the user browser default language. English example: 10000.65 will be displayed as 10,000.65.', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'condition' => ['field_type' => $this->get_type()], 'tabs_wrapper' => 'form_fields_tabs', 'tab' => 'content', 'inner_tab' => 'form_fields_content_tab'], 'dce_amount_format_precision' => ['name' => 'dce_amount_format_precision', 'label' => esc_html__('Decimal Precision', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::NUMBER, 'default' => 2, 'condition' => ['field_type' => $this->get_type(), 'dce_amount_should_format' => 'yes'], 'tabs_wrapper' => 'form_fields_tabs', 'tab' => 'content', 'inner_tab' => 'form_fields_content_tab'], 'dce_amount_refresh_on' => ['name' => 'dce_amount_refresh_on', 'label' => esc_html__('Update on', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'options' => ['input' => esc_html__('Input', 'dynamic-content-for-elementor'), 'change' => esc_html__('Change', 'dynamic-content-for-elementor')], 'default' => 'input', 'description' => esc_html__('‘Input’ will update on every key pressed, ‘Change’ only when the field is blurred (use this if you have performance problems)', 'dynamic-content-for-elementor'), 'tab' => 'content', 'inner_tab' => 'form_fields_content_tab', 'tabs_wrapper' => 'form_fields_tabs', 'condition' => ['field_type' => $this->get_type()]]];
        $control_data['fields'] = $this->inject_field_controls($control_data['fields'], $field_controls);
        $widget->update_control('form_fields', $control_data);
    }
    public function add_style_controls($widget)
    {
        $widget->start_controls_section('dce_amount_section_style', ['label' => '<span class="color-dce icon-dce-logo-dce pull-right ml-1"></span> ' . esc_html__('Amount', 'dynamic-content-for-elementor'), 'tab' => Controls_Manager::TAB_STYLE]);
        $widget->add_control('dce_amount_heading_input', ['label' => esc_html__('Input', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HEADING]);
        $widget->add_responsive_control('dce_amount_align', ['label' => esc_html__('Alignment', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CHOOSE, 'options' => ['left' => ['title' => esc_html__('Left', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-left'], 'center' => ['title' => esc_html__('Center', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-center'], 'right' => ['title' => esc_html__('Right', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-right']], 'selectors' => ['{{WRAPPER}} .elementor-field-type-amount.elementor-field-group .dce-amount-visible' => 'text-align: {{VALUE}};']]);
        $widget->add_responsive_control('dce_amount_opacity', ['label' => esc_html__('Opacity (%)', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => 1], 'range' => ['px' => ['max' => 1, 'min' => 0.1, 'step' => 0.01]], 'selectors' => ['{{WRAPPER}} .elementor-field-type-amount.elementor-field-group .dce-amount-visible' => 'opacity: {{SIZE}};']]);
        $widget->add_responsive_control('dce_amount_padding', ['label' => esc_html__('Padding', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => ['px', '%', 'em'], 'selectors' => ['{{WRAPPER}} .elementor-field-type-amount.elementor-field-group .dce-amount-visible' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};']]);
        $widget->add_responsive_control('dce_amount_margin', ['label' => esc_html__('Margin', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => ['px', '%', 'em'], 'selectors' => ['{{WRAPPER}} .elementor-field-type-amount.elementor-field-group .dce-amount-visible' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};']]);
        $widget->add_control('dce_amount_color', ['label' => esc_html__('Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .elementor-field-type-amount.elementor-field-group .dce-amount-visible' => 'color: {{VALUE}};']]);
        $widget->add_group_control(Group_Control_Typography::get_type(), ['name' => 'dce_amount_typography', 'label' => esc_html__('Typography', 'dynamic-content-for-elementor'), 'selector' => '{{WRAPPER}} .elementor-field-type-amount.elementor-field-group .dce-amount-visible']);
        $widget->add_group_control(Group_Control_Border::get_type(), ['name' => 'dce_amount_border', 'label' => esc_html__('Border', 'dynamic-content-for-elementor'), 'selector' => '{{WRAPPER}} .elementor-field-type-amount.elementor-field-group .dce-amount-visible']);
        $widget->add_control('dce_amount_border_radius', ['label' => esc_html__('Border Radius', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => ['px', '%'], 'selectors' => ['{{WRAPPER}} .elementor-field-type-amount.elementor-field-group .dce-amount-visible' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};']]);
        $widget->add_group_control(Group_Control_Background::get_type(), ['name' => 'dce_amount_background', 'types' => ['classic', 'gradient'], 'selector' => '{{WRAPPER}} .elementor-field-type-amount.elementor-field-group .dce-amount-visible']);
        $widget->add_control('dce_amount_heading_title', ['label' => esc_html__('Label', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HEADING, 'separator' => 'before']);
        $widget->add_responsive_control('dce_amount_title_align', ['label' => esc_html__('Alignment', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CHOOSE, 'options' => ['left' => ['title' => esc_html__('Left', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-left'], 'center' => ['title' => esc_html__('Center', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-center'], 'right' => ['title' => esc_html__('Right', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-right']], 'selectors' => ['{{WRAPPER}} .elementor-field-group.elementor-field-type-amount > label.elementor-field-label' => 'width: 100%; text-align: {{VALUE}};']]);
        $widget->add_control('dce_amount_title_color', ['label' => esc_html__('Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .elementor-field-group.elementor-field-type-amount > label.elementor-field-label' => 'color: {{VALUE}};']]);
        $widget->add_group_control(Group_Control_Typography::get_type(), ['name' => 'dce_amount_title_typography', 'label' => esc_html__('Typography', 'dynamic-content-for-elementor'), 'selector' => '{{WRAPPER}} .elementor-field-group.elementor-field-type-amount > label.elementor-field-label']);
        $widget->add_group_control(Group_Control_Text_Shadow::get_type(), ['name' => 'dce_amount_text_shadow', 'selector' => '{{WRAPPER}} .elementor-field-group.elementor-field-type-amount > label.elementor-field-label']);
        $widget->end_controls_section();
    }
    public function render($item, $item_index, $form)
    {
        $method = $form->get_settings('form_method');
        if ($method === 'post' || $method === 'get') {
            echo '<p><span class="elementor-message elementor-message-danger elementor-help-inline elementor-form-help-inline" role="alert">';
            echo esc_html__('Amount is not compatible with the Method Extension POST and GET options.', 'dynamic-content-for-elementor');
            echo '</span></p>';
            return;
        }
        $expression = $item['dce_amount_expression'] ?? '';
        $expression = \preg_replace('/\\[form:([^\\]]+)\\]/', ' getField("\\1") ', $expression);
        $expression = \preg_replace('/\\[field\\s+id\\s*=\\s*"([^"]+)"\\s*\\]/', ' getField("\\1") ', $expression);
        $field_index = $this->get_field_index();
        /** @var string $expression */
        if ($item['dce_amount_hide'] === 'yes') {
            $form->add_render_attribute('input' . $item_index, 'data-hide', 'yes');
        }
        // Real input (hidden)
        $form->add_render_attribute('input' . $item_index, 'data-field-expression', $expression);
        $form->add_render_attribute('input' . $item_index, 'data-text-before', $item['dce_amount_text_before'] ?? '');
        $form->add_render_attribute('input' . $item_index, 'data-text-after', $item['dce_amount_text_after'] ?? '');
        $form->add_render_attribute('input' . $item_index, 'data-should-round', $item['dce_amount_should_round'] ?? '');
        $form->add_render_attribute('input' . $item_index, 'data-round-precision', $item['dce_amount_round_precision'] ?? '');
        $form->add_render_attribute('input' . $item_index, 'data-refresh-on', $item['dce_amount_refresh_on'] ?? 'input');
        $form->add_render_attribute('input' . $item_index, 'data-should-format', $item['dce_amount_should_format'] ?? '');
        $form->add_render_attribute('input' . $item_index, 'data-format-precision', $item['dce_amount_format_precision'] ?? '');
        $form->set_render_attribute('input' . $item_index, 'type', 'hidden');
        $form->add_render_attribute('input' . $item_index, 'class', 'dce-amount-hidden');
        $form->add_render_attribute('input' . $item_index, 'style', 'display: none;');
        // Visibile input, can have text before and after.
        $form->add_render_attribute('v-input' . $item_index, 'type', 'text');
        $form->add_render_attribute('v-input' . $item_index, 'class', 'dce-amount-visible');
        $form->add_render_attribute('v-input' . $item_index, 'class', 'elementor-field-textual');
        $form->add_render_attribute('v-input' . $item_index, 'readonly', '');
        echo '<input ' . $form->get_render_attribute_string('input' . $item_index) . '>';
        echo '<input size="1"' . $form->get_render_attribute_string('v-input' . $item_index) . '>';
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
        $widgets['form']['fields_in_item']['form_fields'][] = ['field' => 'dce_amount_text_before', 'type' => esc_html__('Amount Text Before', 'dynamic-content-for-elementor'), 'editor_type' => 'LINE'];
        $widgets['form']['fields_in_item']['form_fields'][] = ['field' => 'dce_amount_text_after', 'type' => esc_html__('Amount Text After', 'dynamic-content-for-elementor'), 'editor_type' => 'LINE'];
        return $widgets;
    }
}
