<?php

// SPDX-FileCopyrightText: 2018-2026 Ovation S.r.l. <help@dynamic.ooo>
// SPDX-License-Identifier: GPL-3.0-or-later
namespace DynamicContentForElementor\Widgets;

use Elementor\Repeater;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Text_Shadow;
use DynamicContentForElementor\Helper;
if (!\defined('ABSPATH')) {
    exit;
}
// Exit if accessed directly
class AnimatedText extends \DynamicContentForElementor\Widgets\WidgetPrototype
{
    /**
     * @return array<string>
     */
    public function get_script_depends()
    {
        return ['dce-anime-lib', 'dce-animated-text'];
    }
    /**
     * @return array<string>
     */
    public function get_style_depends()
    {
        return ['dce-animated-text'];
    }
    /**
     * @return void
     */
    protected function safe_register_controls()
    {
        $this->start_controls_section('section_animateText', ['label' => esc_html__('Animated Text', 'dynamic-content-for-elementor')]);
        $this->add_control('animatetext_splittype', ['label' => esc_html__('Type', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'options' => ['chars' => esc_html__('Chars', 'dynamic-content-for-elementor'), 'words' => esc_html__('Words', 'dynamic-content-for-elementor'), 'lines' => esc_html__('Lines', 'dynamic-content-for-elementor')], 'frontend_available' => \true, 'default' => 'chars']);
        $this->add_control('animatetext_trigger', ['label' => esc_html__('Trigger', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'options' => ['animation' => esc_html__('Animation', 'dynamic-content-for-elementor'), 'scroll' => esc_html__('Scroll', 'dynamic-content-for-elementor')], 'frontend_available' => \true, 'default' => 'animation', 'render_type' => 'template', 'prefix_class' => 'animatetext-trigger-']);
        $repeater = new Repeater();
        $repeater->start_controls_tabs('tabs_repeater');
        $repeater->start_controls_tab('tab_content', ['label' => esc_html__('Content', 'dynamic-content-for-elementor')]);
        $repeater->add_control('text_word', ['label' => esc_html__('Word', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXTAREA, 'default' => '']);
        $repeater->end_controls_tab();
        $repeater->start_controls_tab('tab_style', ['label' => esc_html__('Style', 'dynamic-content-for-elementor')]);
        $repeater->add_control('color_item', ['label' => esc_html__('Text color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .dce-animated-text{{CURRENT_ITEM}}' => 'color: {{VALUE}};']]);
        $repeater->add_group_control(Group_Control_Typography::get_type(), ['name' => 'typography_item', 'label' => 'Typography item', 'selector' => '{{WRAPPER}} .dce-animated-text{{CURRENT_ITEM}}']);
        $repeater->end_controls_tab();
        $repeater->end_controls_tabs();
        $this->add_control('words', ['label' => esc_html__('Text', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::REPEATER, 'default' => [['text_word' => esc_html__('Type any word', 'dynamic-content-for-elementor')]], 'separator' => 'after', 'frontend_available' => \true, 'fields' => $repeater->get_controls(), 'title_field' => '{{{ text_word }}}']);
        $this->add_control('animatetext_repeat', ['label' => esc_html__('Repeat', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::NUMBER, 'label_block' => \false, 'separator' => 'before', 'frontend_available' => \true, 'description' => esc_html__('Infinite: -1, repeat it once and hide it: 0', 'dynamic-content-for-elementor'), 'default' => -1, 'min' => -1, 'max' => 25, 'step' => 1]);
        $this->end_controls_section();
        $this->start_controls_section('section_animateText_in', ['label' => esc_html__('IN', 'dynamic-content-for-elementor')]);
        $this->add_control('animatetext_animationstyle_in', ['label' => esc_html__('Animation style', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'options' => ['fading' => esc_html__('Fading', 'dynamic-content-for-elementor'), 'from_left' => esc_html__('From Left', 'dynamic-content-for-elementor'), 'from_right' => esc_html__('From Right', 'dynamic-content-for-elementor'), 'from_top' => esc_html__('From Top', 'dynamic-content-for-elementor'), 'from_bottom' => esc_html__('From Bottom', 'dynamic-content-for-elementor'), 'zoom_front' => esc_html__('Zoom Front', 'dynamic-content-for-elementor'), 'zoom_back' => esc_html__('Zoom Back', 'dynamic-content-for-elementor'), 'random_position' => esc_html__('Random position', 'dynamic-content-for-elementor')], 'frontend_available' => \true, 'default' => 'fading']);
        $this->add_control('animatetext_splitorigin_in', ['label' => esc_html__('Origin', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CHOOSE, 'options' => ['null' => ['title' => esc_html__('Start', 'dynamic-content-for-elementor'), 'icon' => 'eicon-h-align-left'], 'center' => ['title' => esc_html__('Center', 'dynamic-content-for-elementor'), 'icon' => 'eicon-h-align-center'], 'end' => ['title' => esc_html__('End', 'dynamic-content-for-elementor'), 'icon' => 'eicon-h-align-right']], 'default' => 'null', 'frontend_available' => \true]);
        $this->add_control('speed_animation_in', ['label' => esc_html__('Speed', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => 0.7], 'range' => ['px' => ['min' => 0.2, 'max' => 5, 'step' => 0.1]], 'frontend_available' => \true]);
        $this->add_control('amount_speed_in', ['label' => esc_html__('Amount', 'dynamic-content-for-elementor'), 'description' => esc_html__('Negative values produce a contrary effect of origin', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => 1], 'range' => ['px' => ['min' => -100, 'max' => 100, 'step' => 1]], 'frontend_available' => \true]);
        $this->add_control('delay_animation_in', ['label' => esc_html__('Delay', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => 0], 'range' => ['px' => ['min' => 0, 'max' => 30, 'step' => 0.1]], 'frontend_available' => \true]);
        $this->add_control('animFrom_easing_in', ['label' => esc_html__('Easing', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'options' => Helper::get_ease(), 'default' => 'easeInOut', 'frontend_available' => \true, 'label_block' => \false]);
        $this->add_control('animFrom_easing_ease_in', ['label' => esc_html__('Equation', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'options' => Helper::get_timing_functions(), 'default' => 'Power3', 'frontend_available' => \true, 'label_block' => \false]);
        $this->end_controls_section();
        // ---------------------------------------------------- OUT
        $this->start_controls_section('section_animateText_out', ['label' => esc_html__('OUT', 'dynamic-content-for-elementor')]);
        $this->add_control('animatetext_animationstyle_out', ['label' => esc_html__('Animation style', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'options' => ['fading' => esc_html__('Fading', 'dynamic-content-for-elementor'), 'to_left' => esc_html__('To Left', 'dynamic-content-for-elementor'), 'to_right' => esc_html__('To Right', 'dynamic-content-for-elementor'), 'to_top' => esc_html__('To Top', 'dynamic-content-for-elementor'), 'to_bottom' => esc_html__('To Bottom', 'dynamic-content-for-elementor'), 'zoom_front' => esc_html__('Zoom Front', 'dynamic-content-for-elementor'), 'zoom_back' => esc_html__('Zoom Back', 'dynamic-content-for-elementor'), 'random_position' => esc_html__('Random position', 'dynamic-content-for-elementor'), 'elastic' => esc_html__('Elastic', 'dynamic-content-for-elementor')], 'frontend_available' => \true, 'default' => 'fading']);
        $this->add_control('animatetext_splitorigin_out', ['label' => esc_html__('Origin', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CHOOSE, 'options' => ['null' => ['title' => esc_html__('Start', 'dynamic-content-for-elementor'), 'icon' => 'eicon-h-align-left'], 'center' => ['title' => esc_html__('Center', 'dynamic-content-for-elementor'), 'icon' => 'eicon-h-align-center'], 'end' => ['title' => esc_html__('End', 'dynamic-content-for-elementor'), 'icon' => 'eicon-h-align-right']], 'default' => 'null', 'frontend_available' => \true]);
        $this->add_control('speed_animation_out', ['label' => esc_html__('Speed', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => 0.7], 'range' => ['px' => ['min' => 0.2, 'max' => 5, 'step' => 0.1]], 'frontend_available' => \true]);
        $this->add_control('amount_speed_out', ['label' => esc_html__('Amount', 'dynamic-content-for-elementor'), 'description' => esc_html__('Negative values produce a contrary effect of origin', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => 1], 'range' => ['px' => ['min' => -100, 'max' => 100, 'step' => 1]], 'frontend_available' => \true]);
        $this->add_control('delay_animation_out', ['label' => esc_html__('Delay', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => 3], 'range' => ['px' => ['min' => 0, 'max' => 30, 'step' => 0.1]], 'frontend_available' => \true]);
        $this->add_control('animFrom_easing_out', ['label' => esc_html__('Easing', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'options' => Helper::get_ease(), 'default' => 'easeInOut', 'frontend_available' => \true, 'label_block' => \false]);
        $this->add_control('animFrom_easing_ease_out', ['label' => esc_html__('Equation', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'options' => Helper::get_timing_functions(), 'default' => 'Power3', 'frontend_available' => \true, 'label_block' => \false]);
        $this->end_controls_section();
        $this->start_controls_section('section_style', ['label' => esc_html__('Animate Text', 'dynamic-content-for-elementor'), 'tab' => Controls_Manager::TAB_STYLE]);
        $this->add_responsive_control('animatetext_align', ['label' => esc_html__('Alignment', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CHOOSE, 'options' => ['left' => ['title' => esc_html__('Left', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-left'], 'center' => ['title' => esc_html__('Center', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-center'], 'right' => ['title' => esc_html__('Right', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-right']], 'render_type' => 'template', 'default' => 'left', 'selectors' => ['{{WRAPPER}} .dce-animated-text' => 'text-align: {{VALUE}};']]);
        $this->add_control('color', ['label' => esc_html__('Text Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .dce-animated-text' => 'color: {{VALUE}};', '{{WRAPPER}} .dce-animated-text a' => 'color: {{VALUE}};']]);
        $this->add_group_control(Group_Control_Typography::get_type(), ['name' => 'typography', 'selector' => '{{WRAPPER}} .dce-animated-text']);
        $this->add_group_control(Group_Control_Text_Shadow::get_type(), ['name' => 'text_shadow', 'selector' => '{{WRAPPER}} .dce-animated-text']);
        $this->add_control('blend_mode', ['label' => esc_html__('Blend Mode', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'options' => ['' => esc_html__('Normal', 'dynamic-content-for-elementor'), 'multiply' => esc_html__('Multiply', 'dynamic-content-for-elementor'), 'screen' => esc_html__('Screen', 'dynamic-content-for-elementor'), 'overlay' => esc_html__('Overlay', 'dynamic-content-for-elementor'), 'darken' => esc_html__('Darken', 'dynamic-content-for-elementor'), 'lighten' => esc_html__('Lighten', 'dynamic-content-for-elementor'), 'color-dodge' => esc_html__('Color Dodge', 'dynamic-content-for-elementor'), 'saturation' => esc_html__('Saturation', 'dynamic-content-for-elementor'), 'color' => esc_html__('Color', 'dynamic-content-for-elementor'), 'difference' => esc_html__('Difference', 'dynamic-content-for-elementor'), 'exclusion' => esc_html__('Exclusion', 'dynamic-content-for-elementor'), 'hue' => esc_html__('Hue', 'dynamic-content-for-elementor'), 'luminosity' => esc_html__('Luminosity', 'dynamic-content-for-elementor')], 'selectors' => ['{{WRAPPER}} .dce-animated-text' => 'mix-blend-mode: {{VALUE}}'], 'separator' => 'before']);
        $this->end_controls_section();
    }
    /**
     * @return void
     */
    protected function safe_render()
    {
        $settings = $this->get_settings_for_display();
        if (empty($settings)) {
            return;
        }
        $effect = $settings['animatetext_animationstyle_in'];
        $wrapper_class = ['dce-animated-text'];
        if ($effect) {
            $wrapper_class[] = 'dce-animated-text-' . $effect;
        }
        $this->add_render_attribute('wrapper', ['class' => $wrapper_class]);
        $this->add_render_attribute('hidden-wrapper', ['style' => 'display: none;']);
        echo '<div ' . $this->get_render_attribute_string('wrapper') . '></div>';
        echo '<div ' . $this->get_render_attribute_string('hidden-wrapper') . '>';
        if (!empty($settings['words'])) {
            foreach ($settings['words'] as $key => $word) {
                $this->add_render_attribute('item-' . $key, ['class' => ['dce-animated-text-item', 'dce-animated-text-item-' . $key, $effect ? 'dce-animated-text-' . $effect : '']]);
                echo '<div ' . $this->get_render_attribute_string('item-' . $key) . '>';
                echo wp_kses_post($word['text_word']);
                echo '</div>';
            }
        }
        echo '</div>';
    }
}
