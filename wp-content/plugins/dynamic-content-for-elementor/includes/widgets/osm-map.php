<?php

// SPDX-FileCopyrightText: 2018-2026 Ovation S.r.l. <help@dynamic.ooo>
// SPDX-License-Identifier: GPL-3.0-or-later
namespace DynamicContentForElementor\Widgets;

if (!\defined('ABSPATH')) {
    exit;
    // Exit if accessed directly.
}
use Elementor\Modules\DynamicTags\Module as TagsModule;
use Elementor\Controls_Manager;
class OsmMap extends \DynamicContentForElementor\Widgets\WidgetPrototype
{
    public function get_script_depends()
    {
        return ['dce-osm-map'];
    }
    public function get_style_depends()
    {
        return ['dce-osm-map'];
    }
    /**
     * Register controls after check if this feature is only for admin
     *
     * @return void
     */
    protected function safe_register_controls()
    {
        $this->start_controls_section('section_map', ['label' => esc_html__('Map', 'dynamic-content-for-elementor')]);
        $default_address = esc_html__('Piazza San Marco, Venice, Italy', 'dynamic-content-for-elementor');
        $this->add_control('address', [
            'label' => esc_html__('Location', 'dynamic-content-for-elementor'),
            'type' => Controls_Manager::TEXT,
            'frontend_available' => \true,
            'dynamic' => ['active' => \true, 'categories' => [TagsModule::POST_META_CATEGORY]],
            'selectors' => ['' => ''],
            // avoid reinitialization of the widget.
            'placeholder' => $default_address,
            'default' => $default_address,
            'label_block' => \true,
        ]);
        $this->add_control('zoom', [
            'label' => esc_html__('Zoom', 'dynamic-content-for-elementor'),
            'type' => Controls_Manager::SLIDER,
            'frontend_available' => \true,
            'selectors' => ['' => ''],
            // avoid reinitialization of the widget.
            'default' => ['size' => 14],
            'range' => ['px' => ['min' => 1, 'max' => 20]],
            'separator' => 'before',
        ]);
        $this->add_control('scroll_wheel_zoom', ['label' => esc_html__('Scroll Wheel Zoom', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => 'yes', 'frontend_available' => \true]);
        $this->add_responsive_control('height', ['label' => esc_html__('Height', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'range' => ['px' => ['min' => 40, 'max' => 1440], 'vh' => ['min' => 0, 'max' => 100]], 'default' => ['size' => 500], 'size_units' => ['px', 'vh'], 'selectors' => ['{{WRAPPER}} .dce-osm-wrapper' => 'height: {{SIZE}}{{UNIT}};']]);
        $this->end_controls_section();
        $this->start_controls_section('section_map_style', ['label' => esc_html__('Map', 'dynamic-content-for-elementor'), 'tab' => Controls_Manager::TAB_STYLE]);
        $this->start_controls_tabs('map_filter');
        $this->start_controls_tab('normal', ['label' => esc_html__('Normal', 'dynamic-content-for-elementor')]);
        $this->add_group_control(\Elementor\Group_Control_Css_Filter::get_type(), ['name' => 'css_filters', 'selector' => '{{WRAPPER}} .dce-osm-wrapper']);
        $this->end_controls_tab();
        $this->start_controls_tab('hover', ['label' => esc_html__('Hover', 'dynamic-content-for-elementor')]);
        $this->add_group_control(\Elementor\Group_Control_Css_Filter::get_type(), ['name' => 'css_filters_hover', 'selector' => '{{WRAPPER}}:hover .dce-osm-wrapper']);
        $this->add_control('hover_transition', ['label' => esc_html__('Transition Duration', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'range' => ['px' => ['max' => 3, 'step' => 0.1]], 'selectors' => ['{{WRAPPER}} .dce-osm-wrapper' => 'transition-duration: {{SIZE}}s']]);
        $this->end_controls_tab();
        $this->end_controls_tabs();
        $this->end_controls_section();
    }
    protected function safe_render()
    {
        $settings = $this->get_settings_for_display();
        $this->add_render_attribute('div', 'class', 'dce-osm-wrapper');
        // without setting width 100%, if elementor-widget-container is flex the map will disappear:
        $this->add_render_attribute('div', 'style', 'width: 100%;');
        echo '<div ' . $this->get_render_attribute_string('div') . '></div>';
    }
    protected function content_template()
    {
        echo '<div class="dce-osm-wrapper"></div>';
    }
}
