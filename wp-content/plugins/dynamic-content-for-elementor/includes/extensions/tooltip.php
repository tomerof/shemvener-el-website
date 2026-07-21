<?php

// SPDX-FileCopyrightText: 2018-2026 Ovation S.r.l. <help@dynamic.ooo>
// SPDX-License-Identifier: GPL-3.0-or-later
namespace DynamicContentForElementor\Extensions;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Typography;
use DynamicContentForElementor\Helper;
if (!\defined('ABSPATH')) {
    exit;
    // Exit if accessed directly
}
class Tooltip extends \DynamicContentForElementor\Extensions\ExtensionPrototype
{
    public $name = 'Tooltip';
    public $has_controls = \true;
    public function get_script_depends()
    {
        return ['dce-tippy', 'dce-tooltip'];
    }
    public function get_style_depends()
    {
        return ['dce-tooltip'];
    }
    /**
     * Run Once
     *
     * @return void
     */
    public function run_once()
    {
        \DynamicContentForElementor\Plugin::instance()->wpml->add_extensions_fields(['dce_tooltip_content' => ['field' => 'dce_tooltip_content', 'type' => 'Tooltip Content', 'editor_type' => 'TEXT']]);
    }
    private function add_controls($element, $args)
    {
        $element->add_control('dce_enable_tooltip', ['label' => esc_html__('Tooltip', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'return_value' => 'yes', 'frontend_available' => \true]);
        $element->add_control('dce_tooltip_content', ['label' => esc_html__('Content', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'frontend_available' => \true, 'condition' => ['dce_enable_tooltip' => 'yes']]);
        $element->add_control('dce_tooltip_placement', ['label' => esc_html__('Placement', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'options' => ['top' => esc_html__('Top', 'dynamic-content-for-elementor'), 'top-start' => esc_html__('Top Start', 'dynamic-content-for-elementor'), 'top-end' => esc_html__('Top End', 'dynamic-content-for-elementor'), 'right' => esc_html__('Right', 'dynamic-content-for-elementor'), 'right-start' => esc_html__('Right Start', 'dynamic-content-for-elementor'), 'right-end' => esc_html__('Right End', 'dynamic-content-for-elementor'), 'bottom' => esc_html__('Bottom', 'dynamic-content-for-elementor'), 'bottom-start' => esc_html__('Bottom Start', 'dynamic-content-for-elementor'), 'bottom-end' => esc_html__('Bottom End', 'dynamic-content-for-elementor'), 'left' => esc_html__('Left', 'dynamic-content-for-elementor'), 'left-start' => esc_html__('Left Start', 'dynamic-content-for-elementor'), 'left-end' => esc_html__('Left End', 'dynamic-content-for-elementor'), 'auto' => esc_html__('Auto', 'dynamic-content-for-elementor'), 'auto-start' => esc_html__('Auto Start', 'dynamic-content-for-elementor'), 'auto-end' => esc_html__('Auto End', 'dynamic-content-for-elementor')], 'default' => 'top', 'frontend_available' => \true, 'condition' => ['dce_enable_tooltip' => 'yes']]);
        $element->add_control('dce_tooltip_arrow', ['label' => esc_html__('Arrow', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'frontend_available' => 'true', 'default' => 'yes', 'condition' => ['dce_enable_tooltip' => 'yes']]);
        $element->add_control('dce_tooltip_follow_cursor', ['label' => esc_html__('Follow Cursor', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'options' => ['false' => esc_html__('No', 'dynamic-content-for-elementor'), 'true' => esc_html__('Yes', 'dynamic-content-for-elementor'), 'horizontal' => esc_html__('Horizontal', 'dynamic-content-for-elementor'), 'vertical' => esc_html__('Vertical', 'dynamic-content-for-elementor'), 'initial' => esc_html__('Initial', 'dynamic-content-for-elementor')], 'default' => 'false', 'frontend_available' => \true, 'condition' => ['dce_enable_tooltip' => 'yes']]);
        $element->add_responsive_control('dce_tooltip_max_width', ['label' => esc_html__('Max Width', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'size_units' => ['px'], 'range' => ['px' => ['min' => 80, 'max' => 800, 'step' => 10]], 'devices' => Helper::get_active_devices_list(), 'desktop_default' => ['size' => 200, 'unit' => 'px'], 'label_block' => \true, 'frontend_available' => \true, 'condition' => ['dce_enable_tooltip' => 'yes']]);
        $element->add_control('dce_tooltip_touch', ['label' => esc_html__('Touch Devices', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'options' => ['true' => esc_html__('Enable', 'dynamic-content-for-elementor'), 'false' => esc_html__('Disable', 'dynamic-content-for-elementor'), 'hold' => esc_html__('Require pressing and holding the screen to show it', 'dynamic-content-for-elementor')], 'default' => 'true', 'frontend_available' => \true, 'condition' => ['dce_enable_tooltip' => 'yes']]);
        $element->add_control('dce_tooltip_background_color', ['label' => esc_html__('Background Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => [".tippy-box[data-theme~='theme_{{ID}}']" => 'background-color: {{VALUE}};', ".tippy-box[data-theme~='theme_{{ID}}'] > .tippy-arrow:before" => 'border-top-color: {{VALUE}} !important;'], 'condition' => ['dce_enable_tooltip' => 'yes']]);
        $element->add_group_control(Group_Control_Typography::get_type(), ['name' => 'dce_tooltip_typography', 'label' => esc_html__('Typography', 'dynamic-content-for-elementor'), 'selector' => ".tippy-box[data-theme~='theme_{{ID}}']", 'condition' => ['dce_enable_tooltip' => 'yes']]);
        $element->add_control('dce_tooltip_color', ['label' => esc_html__('Text Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => [".tippy-box[data-theme~='theme_{{ID}}']" => 'color: {{VALUE}};'], 'condition' => ['dce_enable_tooltip' => 'yes']]);
        $element->add_control('dce_tooltip_border_radius', ['label' => esc_html__('Border Radius', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => ['px', '%'], 'selectors' => [".tippy-box[data-theme~='theme_{{ID}}']" => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} !important;'], 'condition' => ['dce_enable_tooltip' => 'yes']]);
        $element->add_control('dce_tooltip_zindex', ['label' => esc_html__('Z-Index', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::NUMBER, 'default' => '9999', 'frontend_available' => \true, 'condition' => ['dce_enable_tooltip' => 'yes']]);
    }
    protected function add_actions()
    {
        add_action('elementor/element/common/dce_section_tooltip_advanced/before_section_end', function ($element, $args) {
            $this->add_controls($element, $args);
        }, 10, 2);
        add_action('elementor/widget/render_content', [$this, 'render_template'], 9, 2);
    }
    public function render_template($content, $widget)
    {
        $settings = $widget->get_settings_for_display();
        if (\Elementor\Plugin::$instance->editor->is_edit_mode() || !empty($settings['dce_enable_tooltip'])) {
            $this->enqueue_all();
        }
        return $content;
    }
}
