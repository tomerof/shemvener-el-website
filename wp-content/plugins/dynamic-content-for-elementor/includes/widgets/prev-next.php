<?php

// SPDX-FileCopyrightText: 2018-2026 Ovation S.r.l. <help@dynamic.ooo>
// SPDX-License-Identifier: GPL-3.0-or-later
namespace DynamicContentForElementor\Widgets;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use DynamicContentForElementor\Helper;
// Exit if accessed directly
if (!\defined('ABSPATH')) {
    exit;
}
class PrevNext extends \DynamicContentForElementor\Widgets\WidgetPrototype
{
    public function get_style_depends()
    {
        return ['dce-nextPrev'];
    }
    /**
     * Register controls after check if this feature is only for admin
     *
     * @return void
     */
    protected function safe_register_controls()
    {
        $post_type_object = get_post_type_object(get_post_type());
        $this->start_controls_section('section_content', ['label' => $this->get_title()]);
        $this->add_control('style_postnav', ['label' => esc_html__('Style', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'options' => ['classic' => esc_html__('Classic', 'dynamic-content-for-elementor'), 'thumbflip' => esc_html__('Thumb Flip', 'dynamic-content-for-elementor')], 'default' => 'classic', 'prefix_class' => 'nav-', 'separator' => 'after', 'render_type' => 'template']);
        $this->add_control('show_title', ['label' => esc_html__('Show Title', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => 'yes', 'label_on' => esc_html__('Show', 'dynamic-content-for-elementor'), 'label_off' => esc_html__('Hide', 'dynamic-content-for-elementor'), 'return_value' => 'yes']);
        $this->add_control('show_prevnext', ['label' => esc_html__('Show PrevNext Text', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => 'yes', 'label_on' => esc_html__('Show', 'dynamic-content-for-elementor'), 'label_off' => esc_html__('Hide', 'dynamic-content-for-elementor'), 'return_value' => 'yes']);
        $this->add_control('new_icon_left', ['label' => esc_html__('Left Icon', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::ICONS, 'fa4compatibility' => 'icon_left', 'label_block' => \false, 'skin' => 'inline', 'default' => ['value' => 'fas fa-arrow-left', 'library' => 'solid']]);
        $this->add_control('new_icon_right', ['label' => esc_html__('Right Icon', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::ICONS, 'fa4compatibility' => 'icon_right', 'label_block' => \false, 'skin' => 'inline', 'default' => ['value' => 'fas fa-arrow-right', 'library' => 'solid']]);
        $this->add_control('prev_label', ['label' => esc_html__('Previous Label', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'default' => esc_html__('Previous', 'dynamic-content-for-elementor'), 'condition' => ['show_prevnext' => 'yes', 'style_postnav' => 'classic']]);
        $this->add_control('next_label', ['label' => esc_html__('Next Label', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'default' => esc_html__('Next', 'dynamic-content-for-elementor'), 'condition' => ['show_prevnext' => 'yes', 'style_postnav' => 'classic']]);
        $this->add_control('same_term', ['label' => esc_html__('Same term', 'dynamic-content-for-elementor'), 'description' => esc_html__('Navigate between posts in the same taxonomy term', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CHOOSE, 'options' => ['1' => ['title' => esc_html__('Yes', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-check'], '0' => ['title' => esc_html__('No', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-ban']], 'default' => '0']);
        $this->add_control('taxonomy_type', ['label' => esc_html__('Taxonomy Type', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'options' => Helper::get_taxonomies(), 'default' => '', 'condition' => ['same_term' => '1']]);
        $this->add_control('Navigation_heading', ['label' => esc_html__('Space', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HEADING, 'separator' => 'before', 'condition' => ['style_postnav' => 'classic']]);
        $this->add_control('navigation_space', ['label' => esc_html__('Navigation Space', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => 15], 'range' => ['px' => ['min' => 0, 'max' => 100]], 'selectors' => ['{{WRAPPER}} nav.post-navigation' => 'margin-left: {{SIZE}}{{UNIT}}; margin-right: {{SIZE}}{{UNIT}};'], 'condition' => ['fluttua' => '', 'style_postnav' => 'classic']]);
        $this->add_control('space', ['label' => esc_html__('Navigation Padding', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => ['px', '%'], 'selectors' => ['{{WRAPPER}} .nav-links a' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'], 'condition' => ['fluttua' => '', 'style_postnav' => 'classic']]);
        $this->add_control('custom_width', ['label' => esc_html__('Custom Width', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CHOOSE, 'options' => ['1' => ['title' => esc_html__('Custom Width', 'dynamic-content-for-elementor'), 'icon' => 'fas fa-tv'], '0' => ['title' => esc_html__('No', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-ban']], 'selectors' => ['{{WRAPPER}} .nav-links > div' => 'width: auto;'], 'condition' => ['style_postnav' => 'classic'], 'default' => '1']);
        $this->add_control('width', ['label' => esc_html__('Width', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'size_units' => ['%', 'px'], 'default' => ['size' => 50, 'unit' => '%'], 'range' => ['px' => ['min' => 10, 'max' => 300], '%' => ['min' => 0, 'max' => 100]], 'selectors' => ['{{WRAPPER}} .nav-links > div' => 'width: {{SIZE}}{{UNIT}};'], 'condition' => ['custom_width' => '1']]);
        $this->end_controls_section();
        $this->start_controls_section('section_position', ['label' => esc_html__('Position', 'dynamic-content-for-elementor')]);
        $this->add_control('fluttua', ['label' => esc_html__('Floating', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => '', 'prefix_class' => 'dce-float']);
        $this->add_control('verticale', ['label' => esc_html__('Vertical', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => '', 'prefix_class' => 'vertical']);
        $this->end_controls_section();
        $this->start_controls_section('section_style', ['label' => $this->get_title(), 'tab' => Controls_Manager::TAB_STYLE]);
        $this->add_control('color_1', ['label' => esc_html__('Color Navigation', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .nav-title' => 'color: {{VALUE}};', '{{WRAPPER}} a .nav-title' => 'color: {{VALUE}};'], 'condition' => ['style_postnav' => 'classic']]);
        $this->add_control('color_2', ['label' => esc_html__('Post Title Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .nav-post-title' => 'color: {{VALUE}};', '{{WRAPPER}} a .nav-post-title' => 'color: {{VALUE}};'], 'condition' => ['style_postnav' => 'classic']]);
        $this->add_group_control(Group_Control_Typography::get_type(), ['name' => 'typography_1', 'label' => $this->get_title() . ' ' . esc_html__('Typography', 'dynamic-content-for-elementor'), 'selector' => '{{WRAPPER}} .nav-title', 'condition' => ['style_postnav' => 'classic']]);
        $this->add_group_control(Group_Control_Typography::get_type(), ['name' => 'typography_2', 'label' => esc_html__('Post Title Typography', 'dynamic-content-for-elementor'), 'selector' => '{{WRAPPER}} .nav-post-title', 'condition' => ['style_postnav' => 'classic']]);
        /* ICON */
        $this->add_control('color_3', ['label' => esc_html__('Icon Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} i.fas, {{WRAPPER}} i.far, {{WRAPPER}} i.fab' => 'color: {{VALUE}};', '{{WRAPPER}} a i.fas, {{WRAPPER}} a i.fab, {{WRAPPER}} a i.far' => 'color: {{VALUE}};']]);
        $this->add_control('bgcolor_tf', ['label' => esc_html__('Background Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} a .icon-wrap' => 'background-color: {{VALUE}};'], 'condition' => ['style_postnav' => 'thumbflip']]);
        $this->add_control('rollhover_heading', ['label' => esc_html__('Rollover', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HEADING, 'separator' => 'before']);
        $this->add_control('hover_color', ['label' => esc_html__('Hover Text Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} a:hover span' => 'color: {{VALUE}};'], 'condition' => ['style_postnav' => 'classic']]);
        $this->add_control('hover_color_title', ['label' => esc_html__('Hover Title Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} a:hover .nav-post-title' => 'color: {{VALUE}};'], 'condition' => ['style_postnav' => 'classic']]);
        $this->add_control('hover_color_icon', ['label' => esc_html__('Hover Icon Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} a:hover i.fas, {{WRAPPER}} a:hover i.far, {{WRAPPER}} a:hover i.fab' => 'color: {{VALUE}};']]);
        $this->add_control('hover_bgcolor_tf', ['label' => esc_html__('Hover Background Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} a:hover .icon-wrap' => 'background-color: {{VALUE}};'], 'condition' => ['style_postnav' => 'thumbflip']]);
        $this->end_controls_section();
        $this->start_controls_section('section_icons', ['label' => esc_html__('Icons', 'dynamic-content-for-elementor'), Controls_Manager::TAB_STYLE]);
        $this->add_responsive_control('icon_size', ['label' => esc_html__('Icon Size', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'size_units' => ['px', '%', 'em', 'rem', 'vw', 'custom'], 'default' => ['size' => 30, 'unit' => 'px'], 'range' => ['px' => ['min' => 10, 'max' => 80], '%' => ['min' => 0, 'max' => 50], 'em' => ['min' => 0, 'max' => 10], 'rem' => ['min' => 0, 'max' => 10]], 'selectors' => ['{{WRAPPER}} .nav-links span .fas, {{WRAPPER}} .nav-links span .far, {{WRAPPER}} .nav-links span .fab' => 'font-size: {{SIZE}}{{UNIT}};']]);
        $this->add_responsive_control('icon_space', ['label' => esc_html__('Space', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => 15], 'range' => ['px' => ['min' => 0, 'max' => 100]], 'selectors' => ['{{WRAPPER}}.nav-classic nav.post-navigation .nav-next .fas, {{WRAPPER}}.nav-classic nav.post-navigation .nav-next .fab, {{WRAPPER}}.nav-classic nav.post-navigation .nav-next .far' => 'margin-left: {{SIZE}}{{UNIT}};', '{{WRAPPER}}.nav-classic nav.post-navigation .nav-previous .fas, {{WRAPPER}}.nav-classic nav.post-navigation .nav-previous .fab, {{WRAPPER}}.nav-classic nav.post-navigation .nav-previous .far' => 'margin-right: {{SIZE}}{{UNIT}};'], 'condition' => ['style_postnav' => 'classic']]);
        $this->add_responsive_control('icon_verticalalign', ['label' => esc_html__('Shift', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => 0], 'range' => ['px' => ['min' => -100, 'max' => 100]], 'selectors' => ['{{WRAPPER}}.nav-classic .nav-links .fas, {{WRAPPER}}.nav-classic .nav-links .fab, {{WRAPPER}}.nav-classic .nav-links .far' => 'top: {{SIZE}}{{UNIT}};'], 'condition' => ['style_postnav' => 'classic']]);
        $this->add_responsive_control('icon_space_tf', ['label' => esc_html__('Block Size', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => 40], 'range' => ['px' => ['min' => 0, 'max' => 100]], 'selectors' => ['{{WRAPPER}}.nav-thumbflip .icon-wrap' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};'], 'condition' => ['style_postnav' => 'thumbflip']]);
        $this->end_controls_section();
    }
    protected function safe_render()
    {
        $settings = $this->get_settings_for_display();
        if (empty($settings)) {
            return;
        }
        $id_page = Helper::get_the_id();
        $taxonomy_type = $settings['taxonomy_type'];
        $same_term = $settings['same_term'];
        $animation_class = !empty($settings['hover_animation']) ? 'elementor-animation-' . $settings['hover_animation'] : '';
        $title_nav = '';
        $prev_nav_tx = '';
        $next_nav_tx = '';
        if ($settings['show_title'] == 'yes') {
            $title_nav = '<span class="nav-post-title">%title</span>';
        }
        if ($settings['show_prevnext'] == 'yes') {
            if ($settings['prev_label'] != '') {
                $prev_nav_tx = wp_kses_post($settings['prev_label']);
            } else {
                $prev_nav_tx = esc_html__('Previous', 'dynamic-content-for-elementor');
            }
            if ($settings['next_label'] != '') {
                $next_nav_tx = wp_kses_post($settings['next_label']);
            } else {
                $next_nav_tx = esc_html__('Next', 'dynamic-content-for-elementor');
            }
            $prev_nav_tx = '<span class="nav-post-label">' . $prev_nav_tx . '</span>';
            $next_nav_tx = '<span class="nav-post-label">' . $next_nav_tx . '</span>';
        }
        $next_img = '';
        $previous_img = '';
        $prevText = '';
        $nextText = '';
        if ($settings['style_postnav'] == 'classic') {
            $prevText = '<span class="nav-title">' . Helper::get_icon($settings['new_icon_left']) . '<span>' . $prev_nav_tx . $title_nav . '</span>';
            $nextText = '<span class="nav-title"><span>' . $next_nav_tx . $title_nav . '</span>' . Helper::get_icon($settings['new_icon_right']) . '';
        } elseif ($settings['style_postnav'] == 'thumbflip') {
            $prevText = '<span class="icon-wrap">' . Helper::get_icon($settings['new_icon_left']) . '</span>' . $previous_img;
            $nextText = '<span class="icon-wrap">' . Helper::get_icon($settings['new_icon_right']) . '</span>' . $next_img;
        }
        $options_postnav = array('prev_text' => $prevText, 'next_text' => $nextText, 'screen_reader_text' => '');
        if ($taxonomy_type) {
            $options_postnav['taxonomy'] = $taxonomy_type;
        }
        if ($same_term) {
            $options_postnav['in_same_term'] = $same_term;
        }
        the_post_navigation($options_postnav);
    }
}
