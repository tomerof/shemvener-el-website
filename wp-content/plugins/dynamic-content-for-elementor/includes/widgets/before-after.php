<?php

// SPDX-FileCopyrightText: 2018-2026 Ovation S.r.l. <help@dynamic.ooo>
// SPDX-License-Identifier: GPL-3.0-or-later
namespace DynamicContentForElementor\Widgets;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use DynamicContentForElementor\Helper;
if (!\defined('ABSPATH')) {
    exit;
    // Exit if accessed directly
}
class BeforeAfter extends \DynamicContentForElementor\Widgets\WidgetPrototype
{
    public function get_script_depends()
    {
        return ['dce-jqueryeventmove-lib', 'dce-twentytwenty-lib', 'dce-before-after'];
    }
    public function get_style_depends()
    {
        return ['dce-before-after'];
    }
    /**
     * Register controls after check if this feature is only for admin
     *
     * @return void
     */
    protected function safe_register_controls()
    {
        $this->start_controls_section('section_before_after', ['label' => $this->get_title()]);
        $this->add_control('before_image', ['label' => esc_html__('Before Image', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::MEDIA, 'dynamic' => ['active' => \true], 'default' => ['url' => \Elementor\Utils::get_placeholder_image_src()]]);
        $this->add_control('after_image', ['label' => esc_html__('After Image', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::MEDIA, 'dynamic' => ['active' => \true], 'default' => ['url' => \Elementor\Utils::get_placeholder_image_src()]]);
        $this->add_control('orientation', ['label' => esc_html__('Orientation', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CHOOSE, 'default' => 'horizontal', 'options' => ['vertical' => ['title' => esc_html__('Vertical', 'dynamic-content-for-elementor'), 'icon' => 'eicon-v-align-middle'], 'horizontal' => ['title' => esc_html__('Horizontal', 'dynamic-content-for-elementor'), 'icon' => 'eicon-h-align-center']], 'frontend_available' => \true]);
        $this->add_control('offset_pict', ['label' => esc_html__('Offset', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => 50], 'range' => ['px' => ['max' => 100, 'min' => 10, 'step' => 1]], 'frontend_available' => \true]);
        $this->end_controls_section();
        $this->start_controls_section('section_settings', ['label' => esc_html__('Settings', 'dynamic-content-for-elementor')]);
        $this->add_control('move_slider_on_hover', ['label' => esc_html__('Move slider on mouse hover', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => '', 'frontend_available' => \true]);
        $this->add_control('move_with_handle_only', ['label' => esc_html__('Move with handle only', 'dynamic-content-for-elementor'), 'description' => esc_html__('Allow the user to swipe anywhere on the image to control slider movement', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => '', 'frontend_available' => \true]);
        $this->add_control('click_to_move', ['label' => esc_html__('Click to move', 'dynamic-content-for-elementor'), 'description' => esc_html__('Allow the user to click (or tap) anywhere on the image to move the slider to that location', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => 'yes', 'label_on' => esc_html__('Yes', 'dynamic-content-for-elementor'), 'label_off' => esc_html__('Other', 'dynamic-content-for-elementor'), 'return_value' => 'yes', 'frontend_available' => \true]);
        $this->end_controls_section();
        $this->start_controls_section('section_overlay', ['label' => esc_html__('Overlay and Labels', 'dynamic-content-for-elementor')]);
        $this->add_control('no_overlay', ['label' => esc_html__('No overlay', 'dynamic-content-for-elementor'), 'description' => esc_html__('Don\'t show the overlay with before and after', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => '', 'frontend_available' => \true]);
        $this->add_control('before_label', ['label' => esc_html__('Before', 'dynamic-content-for-elementor'), 'description' => esc_html__('Set a custom before label', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'default' => esc_html__('Before', 'dynamic-content-for-elementor'), 'dynamic' => ['active' => \true], 'frontend_available' => \true, 'condition' => ['no_overlay' => '']]);
        $this->add_control('after_label', ['label' => esc_html__('After', 'dynamic-content-for-elementor'), 'description' => esc_html__('Set a custom after label', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'default' => esc_html__('After', 'dynamic-content-for-elementor'), 'dynamic' => ['active' => \true], 'frontend_available' => \true, 'condition' => ['no_overlay' => '']]);
        $this->end_controls_section();
        $this->start_controls_section('section_handlestyle', ['label' => esc_html__('Handle Style', 'dynamic-content-for-elementor'), 'tab' => Controls_Manager::TAB_STYLE]);
        $this->add_control('handle_border_color', ['label' => esc_html__('Handle Border Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'default' => '#fff', 'selectors' => ['{{WRAPPER}} .twentytwenty-horizontal #container-afterbefore .twentytwenty-handle:before, {{WRAPPER}} .twentytwenty-horizontal #container-afterbefore .twentytwenty-handle:after, {{WRAPPER}} .twentytwenty-vertical #container-afterbefore .twentytwenty-handle:before, {{WRAPPER}} .twentytwenty-vertical #container-afterbefore .twentytwenty-handle:after' => 'background-color: {{VALUE}};', '{{WRAPPER}} #container-afterbefore .twentytwenty-handle' => 'border-color: {{VALUE}}']]);
        $this->add_control('handle_fill_color', ['label' => esc_html__('Handle Fill Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'default' => '', 'selectors' => ['{{WRAPPER}} #container-afterbefore .twentytwenty-handle' => 'background-color: {{VALUE}}']]);
        $this->add_responsive_control('handle_stroke', ['label' => esc_html__('Stroke', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => 3], 'range' => ['px' => ['max' => 30, 'min' => 0, 'step' => 1]], 'selectors' => ['{{WRAPPER}} .twentytwenty-horizontal #container-afterbefore .twentytwenty-handle:before, {{WRAPPER}} .twentytwenty-horizontal #container-afterbefore .twentytwenty-handle:after' => 'width: {{SIZE}}{{UNIT}}; margin-left: calc(-{{SIZE}}{{UNIT}} / 2);', '{{WRAPPER}} .twentytwenty-vertical #container-afterbefore .twentytwenty-handle:before, {{WRAPPER}} .twentytwenty-vertical #container-afterbefore .twentytwenty-handle:after' => 'height: {{SIZE}}{{UNIT}}; margin-top: calc(-{{SIZE}}{{UNIT}} / 2);', '{{WRAPPER}} #container-afterbefore .twentytwenty-handle' => 'border-width: {{SIZE}}{{UNIT}}']]);
        $this->add_responsive_control('circle_stroke', ['label' => esc_html__('Circle Stroke', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => ''], 'range' => ['px' => ['max' => 30, 'min' => 0, 'step' => 1]], 'selectors' => ['{{WRAPPER}} #container-afterbefore .twentytwenty-handle' => 'border-width: {{SIZE}}{{UNIT}}']]);
        $this->add_responsive_control('handle_circlewidth', ['label' => esc_html__('Circle Width', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => 38], 'range' => ['px' => ['max' => 100, 'min' => 15, 'step' => 1]], 'selectors' => ['{{WRAPPER}} #container-afterbefore .twentytwenty-handle' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};', '{{WRAPPER}} .twentytwenty-vertical #container-afterbefore .twentytwenty-handle::before' => 'margin-left: calc({{SIZE}}{{UNIT}} / 2 - 1px);', '{{WRAPPER}} .twentytwenty-vertical #container-afterbefore .twentytwenty-handle::after' => 'margin-right: calc({{SIZE}}{{UNIT}} / 2 - 1px);', '{{WRAPPER}} .twentytwenty-horizontal #container-afterbefore .twentytwenty-handle:before' => 'margin-bottom: calc({{SIZE}}{{UNIT}} / 2 - 1px);', '{{WRAPPER}} .twentytwenty-horizontal #container-afterbefore .twentytwenty-handle:after' => 'margin-top: calc({{SIZE}}{{UNIT}} / 2 - 1px);']]);
        $this->add_group_control(Group_Control_Box_Shadow::get_type(), ['name' => 'handle_boxshadow', 'selector' => '{{WRAPPER}} #container-afterbefore .twentytwenty-handle']);
        $this->add_control('handle_trianglecolor', ['label' => esc_html__('Triangle Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'default' => '#fff', 'selectors' => ['{{WRAPPER}} #container-afterbefore .twentytwenty-handle .twentytwenty-left-arrow' => 'border-right-color: {{VALUE}}', '{{WRAPPER}} #container-afterbefore .twentytwenty-handle .twentytwenty-right-arrow' => 'border-left-color: {{VALUE}}', '{{WRAPPER}} #container-afterbefore .twentytwenty-handle .twentytwenty-up-arrow' => 'border-bottom-color: {{VALUE}}', '{{WRAPPER}} #container-afterbefore .twentytwenty-handle .twentytwenty-down-arrow' => 'border-top-color: {{VALUE}}']]);
        $this->add_responsive_control('handle_trianglesize', ['label' => esc_html__('Triangle Size', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => 6], 'range' => ['px' => ['max' => 30, 'min' => 3, 'step' => 1]], 'selectors' => ['{{WRAPPER}} #container-afterbefore .twentytwenty-handle .twentytwenty-left-arrow, {{WRAPPER}}  #container-afterbefore .twentytwenty-handle .twentytwenty-right-arrow, {{WRAPPER}} #container-afterbefore .twentytwenty-handle .twentytwenty-up-arrow, {{WRAPPER}} #container-afterbefore .twentytwenty-handle .twentytwenty-down-arrow' => 'border-width: {{SIZE}}{{UNIT}}']]);
        $this->add_responsive_control('handle_triangleposition', ['label' => esc_html__('Triangle Position', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => -15], 'range' => ['px' => ['max' => 50, 'min' => -50, 'step' => 1]], 'selectors' => ['{{WRAPPER}} #container-afterbefore .twentytwenty-handle .twentytwenty-left-arrow' => 'margin-left: {{SIZE}}{{UNIT}}', '{{WRAPPER}} #container-afterbefore .twentytwenty-handle .twentytwenty-right-arrow' => 'margin-right: {{SIZE}}{{UNIT}}', '{{WRAPPER}} #container-afterbefore .twentytwenty-handle .twentytwenty-up-arrow' => 'margin-top: {{SIZE}}{{UNIT}}', '{{WRAPPER}} #container-afterbefore .twentytwenty-handle .twentytwenty-down-arrow' => 'margin-bottom: {{SIZE}}{{UNIT}}']]);
        $this->end_controls_section();
        $this->start_controls_section('section_style_overlayandlabels', ['label' => esc_html__('Overlay and Labels', 'dynamic-content-for-elementor'), 'tab' => Controls_Manager::TAB_STYLE, 'condition' => ['no_overlay' => '']]);
        $this->add_control('overlay_bg', ['label' => esc_html__('Overlay Background', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'default' => 'rgba(0, 0, 0, 0.5)', 'selectors' => ['{{WRAPPER}} #container-afterbefore .twentytwenty-overlay:hover' => 'background-color: {{VALUE}};']]);
        $this->add_control('label_color', ['label' => esc_html__('Labels Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'default' => '#fff', 'selectors' => ['{{WRAPPER}} #container-afterbefore .twentytwenty-overlay .twentytwenty-before-label:before, {{WRAPPER}} #container-afterbefore .twentytwenty-overlay .twentytwenty-after-label:before' => 'color: {{VALUE}};']]);
        $this->add_control('overlay_label_bg', ['label' => esc_html__('Overlay Label Background', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'default' => 'rgba(#fff, .2)', 'selectors' => ['{{WRAPPER}} #container-afterbefore .twentytwenty-overlay .twentytwenty-before-label:before, {{WRAPPER}} #container-afterbefore .twentytwenty-overlay .twentytwenty-after-label:before' => 'background-color: {{VALUE}};']]);
        $this->add_control('overlay_label_padding', ['label' => esc_html__('Overlay Label Padding', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => ['px', '%', 'em'], 'selectors' => ['{{WRAPPER}} #container-afterbefore .twentytwenty-overlay .twentytwenty-before-label:before, {{WRAPPER}} #container-afterbefore .twentytwenty-overlay .twentytwenty-after-label:before' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};']]);
        $this->add_responsive_control('label_radius', ['label' => esc_html__('Labels Radius', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => 2], 'range' => ['px' => ['max' => 100, 'min' => 0, 'step' => 1]], 'selectors' => ['{{WRAPPER}} #container-afterbefore .twentytwenty-overlay .twentytwenty-before-label:before, {{WRAPPER}} #container-afterbefore .twentytwenty-overlay .twentytwenty-after-label:before' => 'border-radius: {{SIZE}}{{UNIT}};']]);
        $this->add_group_control(Group_Control_Typography::get_type(), ['name' => 'labels_typography', 'label' => esc_html__('Labels Typography', 'dynamic-content-for-elementor'), 'selector' => '{{WRAPPER}} #container-afterbefore .twentytwenty-overlay .twentytwenty-before-label:before, {{WRAPPER}} #container-afterbefore .twentytwenty-overlay .twentytwenty-after-label:before']);
        $this->end_controls_section();
    }
    protected function safe_render()
    {
        $settings = $this->get_settings_for_display();
        if (empty($settings)) {
            return;
        }
        $this->add_render_attribute('container', 'id', 'container-afterbefore');
        // Set class twentytwenty-container to avoid Flash of unstyled content (FOUC)
        $this->add_render_attribute('container', 'class', 'twentytwenty-container');
        // Before
        $this->add_render_attribute('before', 'src', $settings['before_image']['url']);
        if (!empty($settings['before_image']['id'])) {
            $this->add_render_attribute('before', 'alt', Helper::get_image_alt($settings['before_image']['id']));
        }
        // After
        $this->add_render_attribute('after', 'src', $settings['after_image']['url']);
        if (!empty($settings['after_image']['id'])) {
            $this->add_render_attribute('after', 'alt', Helper::get_image_alt($settings['after_image']['id']));
        }
        ?>

		<div <?php 
        echo $this->get_render_attribute_string('container');
        ?>>
			<img <?php 
        echo $this->get_render_attribute_string('before');
        ?> />
			<img <?php 
        echo $this->get_render_attribute_string('after');
        ?> />
		</div>
		<?php 
    }
}
