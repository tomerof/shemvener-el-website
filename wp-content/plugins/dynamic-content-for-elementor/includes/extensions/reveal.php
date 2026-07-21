<?php

// SPDX-FileCopyrightText: 2018-2026 Ovation S.r.l. <help@dynamic.ooo>
// SPDX-License-Identifier: GPL-3.0-or-later
namespace DynamicContentForElementor\Extensions;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Background;
if (!\defined('ABSPATH')) {
    exit;
    // Exit if accessed directly
}
class Reveal extends \DynamicContentForElementor\Extensions\ExtensionPrototype
{
    public $name = 'Reveal';
    public $has_controls = \true;
    public function get_style_depends()
    {
        return ['dce-reveal'];
    }
    public function get_script_depends()
    {
        return ['dce-anime-lib', 'dce-revealFx', 'dce-reveal'];
    }
    private function add_controls($element, $args)
    {
        $element_type = $element->get_type();
        $element->add_control('enabled_reveal', ['label' => esc_html__('Reveal', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'frontend_available' => \true]);
        $element->add_control('reveal_direction', ['label' => esc_html__('Direction', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'default' => 'c', 'options' => ['c' => esc_html__('Center', 'dynamic-content-for-elementor'), 'lr' => esc_html__('Left to Right', 'dynamic-content-for-elementor'), 'rl' => esc_html__('Right to Left', 'dynamic-content-for-elementor'), 'tb' => esc_html__('Top to Bottom', 'dynamic-content-for-elementor'), 'bt' => esc_html__('Bottom to top', 'dynamic-content-for-elementor')], 'frontend_available' => \true, 'condition' => ['enabled_reveal' => 'yes']]);
        $element->add_control('reveal_speed', ['label' => esc_html__('Speed', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'range' => ['s' => ['min' => 0, 'max' => 10, 'step' => 0.1]], 'default' => ['size' => 5, 'unit' => 's'], 'description' => esc_html__('In hundredths of a second', 'dynamic-content-for-elementor'), 'frontend_available' => \true, 'condition' => ['enabled_reveal' => 'yes']]);
        $element->add_control('reveal_delay', ['label' => esc_html__('Delay', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'range' => ['s' => ['min' => 0, 'max' => 20, 'step' => 0.1]], 'default' => ['size' => 0, 'unit' => 's'], 'description' => esc_html__('In hundredths of a second', 'dynamic-content-for-elementor'), 'frontend_available' => \true, 'condition' => ['enabled_reveal' => 'yes']]);
        $element->add_control('reveal_bgcolor', ['label' => esc_html__('Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'frontend_available' => \true, 'default' => '#ccc', 'condition' => ['enabled_reveal' => 'yes']]);
    }
    protected function add_actions()
    {
        // Activate controls for widgets
        add_action('elementor/element/common/dce_section_reveal_advanced/before_section_end', function ($element, $args) {
            $this->add_controls($element, $args);
        }, 10, 2);
        add_filter('elementor/widget/print_template', array($this, 'reveal_print_template'), 9, 2);
        add_action('elementor/widget/render_content', array($this, 'reveal_render_template'), 9, 2);
    }
    public function reveal_print_template($content, $widget)
    {
        if (!$content) {
            return '';
        }
        $id_item = $widget->get_id();
        $content = "<# if ( '' !== settings.enabled_reveal ) { #><div id=\"reveal-{{id}}\" class=\"reveal\">" . $content . '</div><# } else { #>' . $content . '<# } #>';
        return $content;
    }
    public function reveal_render_template($content, $widget)
    {
        $settings = $widget->get_settings_for_display();
        if (\Elementor\Plugin::$instance->editor->is_edit_mode() || !empty($settings['enabled_reveal'])) {
            $this->enqueue_all();
            $id_item = $widget->get_id();
            $content = '<div id="reveal-' . $id_item . '" class="revealFx">' . $content . '</div>';
        }
        return $content;
    }
}
