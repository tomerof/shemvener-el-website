<?php

// SPDX-FileCopyrightText: 2018-2026 Ovation S.r.l. <help@dynamic.ooo>
// SPDX-License-Identifier: GPL-3.0-or-later
namespace DynamicContentForElementor\Controls;

use Elementor\Group_Control_Base;
use Elementor\Controls_Manager;
use DynamicContentForElementor\Helper;
if (!\defined('ABSPATH')) {
    exit;
    // Exit if accessed directly.
}
/**
 * Custom Animate-element group control
 *
 */
class Group_Control_Animation_Element extends Group_Control_Base
{
    protected static $fields;
    public static function get_type()
    {
        return 'animation-element';
    }
    protected function init_fields()
    {
        $fields = [];
        $fields['controls'] = ['label' => '', 'type' => Controls_Manager::CHOOSE, 'default' => 'running', 'toggle' => \false, 'options' => ['running' => ['title' => esc_html__('Play', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-play'], 'paused' => ['title' => esc_html__('Pause', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-pause']], 'separator' => 'after', 'selectors' => ['{{SELECTOR}}' => 'animation-play-state: {{VALUE}}; -webkit-animation-play-state: {{VALUE}};']];
        $fields['animation'] = ['label' => _x('Animation Type', 'Animation Control', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'default' => 'galleggia', 'options' => ['galleggia' => _x('Float', 'Animation Control', 'dynamic-content-for-elementor'), 'attraversa' => _x('Pass through', 'Animation Control', 'dynamic-content-for-elementor'), 'pulsa' => _x('Pulse', 'Animation Control', 'dynamic-content-for-elementor'), 'dondola' => _x('Swing', 'Animation Control', 'dynamic-content-for-elementor'), 'cresci' => _x('Grow', 'Animation Control', 'dynamic-content-for-elementor'), 'esplodi' => _x('Explode', 'Animation Control', 'dynamic-content-for-elementor'), 'brilla' => _x('Shine', 'Animation Control', 'dynamic-content-for-elementor'), 'risali-o-affonda' => _x('Up or Sink', 'Animation Control', 'dynamic-content-for-elementor'), 'rotola' => _x('Spin', 'Animation Control', 'dynamic-content-for-elementor'), 'gira' => _x('Runs', 'Animation Control', 'dynamic-content-for-elementor'), 'saltella' => _x('Bounce', 'Animation Control', 'dynamic-content-for-elementor')], 'selectors' => ['{{SELECTOR}}' => 'animation-name: {{VALUE}}; -webkit-animation-name: {{VALUE}};']];
        $fields['animation_variation'] = ['label' => _x('Animation Variation', 'Animation Control', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'default' => '', 'options' => ['short' => _x('Short', 'Animation Control', 'dynamic-content-for-elementor'), '' => _x('Medium', 'Animation Control', 'dynamic-content-for-elementor'), 'long' => _x('Long', 'Animation Control', 'dynamic-content-for-elementor')], 'condition' => ['enabled_animations' => 'yes', 'animation!' => ['cresci', 'attraversa']], 'selectors' => ['{{SELECTOR}}' => 'animation-name: {{animation.VALUE}}{{VALUE}}; -webkit-animation-name: {{animation.VALUE}}{{VALUE}};']];
        $fields['transform_origin'] = ['label' => _x('Transform origin', 'Animation Control', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'default' => 'center center', 'options' => ['top left' => _x('Top Left', 'Animation Control', 'dynamic-content-for-elementor'), 'top center' => _x('Top Center', 'Animation Control', 'dynamic-content-for-elementor'), 'top right' => _x('Top Right', 'Animation Control', 'dynamic-content-for-elementor'), 'center left' => _x('Center Left', 'Animation Control', 'dynamic-content-for-elementor'), 'center center' => _x('Center Center', 'Animation Control', 'dynamic-content-for-elementor'), 'center right' => _x('Center Right', 'Animation Control', 'dynamic-content-for-elementor'), 'bottom left' => _x('Bottom Left', 'Animation Control', 'dynamic-content-for-elementor'), 'bottom center' => _x('Bottom Center', 'Animation Control', 'dynamic-content-for-elementor'), 'bottom right' => _x('Bottom Right', 'Animation Control', 'dynamic-content-for-elementor')], 'selectors' => ['{{SELECTOR}}' => 'transform-origin: {{VALUE}}; -webkit-transform-origin: {{VALUE}};']];
        $fields['iteration_mode'] = ['label' => esc_html__('Iteration Mode', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => 'infinite', 'label_on' => esc_html__('Infinite', 'dynamic-content-for-elementor'), 'label_off' => esc_html__('Count', 'dynamic-content-for-elementor'), 'return_value' => 'infinite', 'separator' => 'before', 'selectors' => ['{{SELECTOR}}' => 'animation-iteration-count: {{VALUE}}; -webkit-animation-iteration-count: {{VALUE}};']];
        $fields['iteration_count'] = ['label' => esc_html__('Iteration Count', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::NUMBER, 'default' => 1, 'min' => 1, 'max' => 100, 'step' => 1, 'selectors' => ['{{SELECTOR}}' => 'animation-iteration-count: {{VALUE}}; -webkit-animation-iteration-count: {{VALUE}};'], 'condition' => ['iteration_mode' => '']];
        $fields['duration'] = ['label' => _x('Duration', 'Animation Control', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['unit' => 's', 'size' => 1], 'range' => ['s' => ['min' => 0, 'max' => 20, 'step' => 0.1]], 'size_units' => ['s'], 'selectors' => ['{{SELECTOR}}' => 'animation-duration: {{SIZE}}{{UNIT}}; -webkit-animation-duration: {{SIZE}}{{UNIT}};']];
        $fields['delay'] = ['label' => _x('Delay', 'Animation Control', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['unit' => 's', 'size' => 0], 'range' => ['s' => ['min' => 0, 'max' => 20, 'step' => 0.1]], 'size_units' => ['s'], 'selectors' => ['{{SELECTOR}}' => 'animation-delay: {{SIZE}}{{UNIT}}; -webkit-animation-delay: {{SIZE}}{{UNIT}};']];
        $fields['timing_function'] = ['label' => _x('Timing Function', 'Animation Control', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'default' => 'linear', 'options' => Helper::get_anim_timing_functions(), 'selectors' => ['{{SELECTOR}}' => 'animation-timing-function: {{VALUE}}; -webkit-animation-timing-function: {{VALUE}};']];
        $fields['direction'] = ['label' => esc_html__('Direction', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CHOOSE, 'default' => 'normal', 'options' => ['normal' => ['title' => esc_html__('Normal', 'dynamic-content-for-elementor'), 'icon' => 'eicon-arrow-right'], 'reverse' => ['title' => esc_html__('Reverse', 'dynamic-content-for-elementor'), 'icon' => 'eicon-arrow-left'], 'alternate' => ['title' => esc_html__('Alternate', 'dynamic-content-for-elementor'), 'icon' => 'eicon-exchange'], 'alternate-reverse' => ['title' => esc_html__('Alternate Reverse', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-retweet']], 'selectors' => ['{{SELECTOR}}' => 'animation-direction: {{VALUE}}; -webkit-animation-direction: {{VALUE}};']];
        $fields['fill_mode'] = ['label' => esc_html__('Fill Mode', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CHOOSE, 'default' => 'none', 'options' => ['none' => ['title' => esc_html__('None', 'dynamic-content-for-elementor'), 'icon' => 'eicon-close'], 'backwards' => ['title' => esc_html__('Backwards', 'dynamic-content-for-elementor'), 'icon' => 'eicon-h-align-right'], 'both' => ['title' => esc_html__('Both', 'dynamic-content-for-elementor'), 'icon' => 'eicon-h-align-center'], 'forwards' => ['title' => esc_html__('Forwards', 'dynamic-content-for-elementor'), 'icon' => 'eicon-h-align-left']], 'selectors' => ['{{SELECTOR}}' => 'animation-fill-mode: {{VALUE}}; -webkit-animation-fill-mode: {{VALUE}};']];
        return $fields;
    }
    /**
     * @since 0.5.0
     * @access protected
     */
    protected function get_default_options()
    {
        return ['popover' => \false];
    }
}
