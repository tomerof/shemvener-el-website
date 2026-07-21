<?php

// SPDX-FileCopyrightText: 2018-2026 Ovation S.r.l. <help@dynamic.ooo>
// SPDX-License-Identifier: GPL-3.0-or-later
namespace DynamicContentForElementor\Widgets;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Repeater;
use DynamicContentForElementor\Helper;
if (!\defined('ABSPATH')) {
    exit;
    // Exit if accessed directly
}
class SinglePostsList extends \DynamicContentForElementor\Widgets\WidgetPrototype
{
    public function get_style_depends()
    {
        return ['dce-list'];
    }
    /**
     * Register controls after check if this feature is only for admin
     *
     * @return void
     */
    protected function safe_register_controls()
    {
        $this->start_controls_section('section_content', ['label' => esc_html__('Custom menu from single pages', 'dynamic-content-for-elementor')]);
        $this->add_control('singlepage_select', ['label' => esc_html__('Select Single Posts', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HIDDEN, 'multiple' => \true, 'label_block' => \true, 'options' => array()]);
        $repeater = new Repeater();
        $repeater->add_control('singlepage_select', ['label' => esc_html__('Select Single Posts', 'dynamic-content-for-elementor'), 'type' => 'ooo_query', 'placeholder' => esc_html__('Post Title', 'dynamic-content-for-elementor'), 'label_block' => \true, 'query_type' => 'posts']);
        $this->add_control('pages', ['label' => esc_html__('Pages', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::REPEATER, 'prevent_empty' => \false, 'default' => [], 'fields' => $repeater->get_controls()]);
        $this->add_control('menu_style', ['label' => esc_html__('Style', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'options' => ['horizontal' => esc_html__('Horizontal', 'dynamic-content-for-elementor'), 'vertical' => esc_html__('Vertical', 'dynamic-content-for-elementor')], 'default' => 'vertical']);
        $this->add_control('heading_options_menu', ['label' => esc_html__('Options', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HEADING, 'separator' => 'before']);
        $this->add_control('show_title', ['label' => esc_html__('Show Title', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => 'yes']);
        $this->add_control('title_text', ['label' => esc_html__('Title text', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'condition' => ['show_title!' => '']]);
        $this->add_control('show_childlist', ['label' => esc_html__('Show Child List', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => 'yes']);
        $this->add_control('show_publish', ['label' => esc_html__('Show only published', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => 'yes']);
        $this->add_control('show_border', ['label' => esc_html__('Show Border', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'toggle' => \false, 'options' => ['1' => esc_html__('Yes', 'dynamic-content-for-elementor'), '0' => esc_html__('No', 'dynamic-content-for-elementor'), '2' => esc_html__('Any', 'dynamic-content-for-elementor')], 'default' => '1', 'render_type' => 'template', 'prefix_class' => 'border-']);
        $this->add_control('blockwidth_enable', ['label' => esc_html__('Force Block width', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'separator' => 'before', 'condition' => ['show_border' => '2']]);
        $this->add_control('menu_width', ['label' => esc_html__('Box Width', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => 120], 'range' => ['px' => ['min' => 0, 'max' => 400]], 'condition' => ['blockwidth_enable' => 'yes', 'show_border' => '2'], 'selectors' => ['{{WRAPPER}} .dce-menu .box' => 'width: {{SIZE}}{{UNIT}};']]);
        $this->add_responsive_control('show_separators', ['label' => esc_html__('Show Separator', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CHOOSE, 'options' => ['solid' => ['title' => esc_html__('Yes', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-check'], 'none' => ['title' => esc_html__('No', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-ban']], 'toggle' => \true, 'default' => 'solid', 'separator' => 'before', 'selectors' => ['{{WRAPPER}} .dce-menu.horizontal li' => 'border-left-style: {{VALUE}};'], 'condition' => ['menu_style' => 'horizontal']]);
        $this->add_control('heading_spaces_menu', ['label' => esc_html__('Space', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HEADING, 'separator' => 'before', 'condition' => ['show_childlist!' => '']]);
        $this->add_responsive_control('menu_space', ['label' => esc_html__('Header Space', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => 0], 'range' => ['px' => ['min' => 0, 'max' => 100]], 'selectors' => ['{{WRAPPER}} .dce-menu .dce-parent-title' => 'margin-bottom: calc( {{SIZE}}{{UNIT}} / 2);', '{{WRAPPER}} .dce-menu hr' => 'margin-bottom: calc( {{SIZE}}{{UNIT}} / 2);', '{{WRAPPER}} .dce-menu div.box' => 'padding: {{SIZE}}{{UNIT}};'], 'condition' => ['show_title!' => '']]);
        $this->add_responsive_control('menu_list_space', ['label' => esc_html__('List Space', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => 0], 'range' => ['px' => ['min' => 0, 'max' => 100]], 'selectors' => ['{{WRAPPER}} .dce-menu ul.first-level > li' => 'margin-bottom: {{SIZE}}{{UNIT}};'], 'condition' => ['show_childlist!' => '']]);
        $this->add_responsive_control('menu_indent', ['label' => esc_html__('Indent', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'range' => ['px' => ['min' => 0, 'max' => 100]], 'selectors' => ['{{WRAPPER}} .dce-menu ul.first-level > li' => 'padding-left: {{SIZE}}{{UNIT}}; padding-right: {{SIZE}}{{UNIT}};'], 'condition' => ['show_childlist!' => '']]);
        $this->end_controls_section();
        $this->start_controls_section('section_style', ['label' => esc_html__('Style', 'dynamic-content-for-elementor'), 'tab' => Controls_Manager::TAB_STYLE]);
        $this->add_responsive_control('menu_align', ['label' => esc_html__('Text Alignment', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CHOOSE, 'options' => ['left' => ['title' => esc_html__('Left', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-left'], 'center' => ['title' => esc_html__('Center', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-center'], 'right' => ['title' => esc_html__('Right', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-right']], 'prefix_class' => 'menu-align-', 'default' => 'left', 'selectors' => ['{{WRAPPER}}' => 'text-align: {{VALUE}};']]);
        $this->add_control('heading_colors', ['label' => esc_html__('List items', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HEADING, 'separator' => 'before', 'condition' => ['show_childlist!' => '']]);
        $this->add_control('menu_color', ['label' => esc_html__('Text Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'condition' => ['show_childlist!' => ''], 'selectors' => ['{{WRAPPER}} .dce-menu a' => 'color: {{VALUE}};']]);
        $this->add_control('menu_color_hover', ['label' => esc_html__('Text Hover Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'condition' => ['show_childlist!' => ''], 'selectors' => ['{{WRAPPER}} .dce-menu a:hover' => 'color: {{VALUE}};']]);
        $this->add_control('menu_color_active', ['label' => esc_html__('Text Active Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'condition' => ['show_childlist!' => ''], 'selectors' => ['{{WRAPPER}} .dce-menu ul li a.active' => 'color: {{VALUE}};']]);
        $this->add_group_control(Group_Control_Typography::get_type(), ['name' => 'typography_list', 'selector' => '{{WRAPPER}} .dce-menu ul.first-level li', 'condition' => ['show_childlist!' => '']]);
        $this->add_control('heading_title', ['label' => esc_html__('Title', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HEADING, 'separator' => 'before', 'condition' => ['show_title!' => '']]);
        $this->add_control('menu_title_color', ['label' => esc_html__('Title Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'condition' => ['show_title!' => ''], 'selectors' => ['{{WRAPPER}} .dce-menu .dce-parent-title' => 'color: {{VALUE}};']]);
        $this->add_group_control(Group_Control_Typography::get_type(), ['name' => 'typography_tit', 'selector' => '{{WRAPPER}} .dce-menu .dce-parent-title', 'condition' => ['show_title!' => '']]);
        $this->add_control('heading_border', ['label' => esc_html__('Border', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HEADING, 'separator' => 'before', 'condition' => ['show_border' => ['1', '2']]]);
        $this->add_control('menu_border_color', ['label' => esc_html__('Border Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'toggle' => \false, 'condition' => ['show_border' => ['1', '2']], 'selectors' => ['{{WRAPPER}} .dce-menu hr' => 'border-color: {{VALUE}};', '{{WRAPPER}} .dce-menu.horizontal li' => 'border-left-color: {{VALUE}};', '{{WRAPPER}} .dce-menu .box' => 'border-color: {{VALUE}};']]);
        $this->add_control('menu_border_size', ['label' => esc_html__('Border weight', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'toggle' => \false, 'default' => ['size' => 1, 'unit' => 'px'], 'size_units' => ['px'], 'range' => ['px' => ['min' => 1, 'max' => 20]], 'selectors' => ['{{WRAPPER}} .dce-menu hr, {{WRAPPER}} .dce-menu .box' => 'border-width: {{SIZE}}{{UNIT}};'], 'condition' => ['show_border' => ['1', '2']]]);
        $this->add_control('menu_border_width', ['label' => esc_html__('Border width', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'toggle' => \false, 'size_units' => ['px', '%'], 'default' => ['size' => ''], 'range' => ['px' => ['min' => 1, 'max' => 1000], '%' => ['min' => 1, 'max' => 100]], 'selectors' => ['{{WRAPPER}} .dce-menu hr' => 'width: {{SIZE}}{{UNIT}};'], 'condition' => ['show_border' => ['1', '2']]]);
        $this->add_control('heading_separator', ['label' => esc_html__('Separator', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HEADING, 'separator' => 'before', 'condition' => ['show_separators' => 'solid', 'menu_style' => 'horizontal']]);
        $this->add_control('menu_color_separator', ['label' => esc_html__('Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'default' => '', 'condition' => ['show_separators' => 'solid', 'menu_style' => 'horizontal'], 'selectors' => ['{{WRAPPER}} .dce-menu.horizontal li' => 'border-left-color: {{VALUE}};']]);
        $this->add_responsive_control('menu_size_separator', ['label' => esc_html__('Weight', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => 1, 'unit' => 'px'], 'size_units' => ['px'], 'range' => ['px' => ['min' => 0, 'max' => 50]], 'selectors' => ['{{WRAPPER}} .dce-menu.horizontal li' => 'border-left-width: {{SIZE}}{{UNIT}};'], 'condition' => ['show_separators' => 'solid', 'menu_style' => 'horizontal']]);
        $this->end_controls_section();
    }
    protected function safe_render()
    {
        $settings = $this->get_settings_for_display();
        if (empty($settings)) {
            return;
        }
        $id_page = Helper::get_the_id();
        $styleMenu = $settings['menu_style'];
        $clssStyleMenu = $styleMenu;
        echo '<nav class="' . esc_attr('dce-menu ' . $clssStyleMenu) . '">';
        if ($settings['show_border'] == 2) {
            echo '<div class="box">';
        }
        if ($settings['show_title']) {
            echo '<h4 class="dce-parent-title">' . wp_kses_post($settings['title_text']) . '</h4>';
            if ($settings['show_border'] == 1) {
                echo '<hr />';
            }
        }
        if ($settings['show_childlist']) {
            echo '<ul class="first-level">';
            $pages = $settings['pages'];
            if (!empty($pages)) {
                foreach ($pages as $key => $item) {
                    $pageid = Helper::get_translated_post_id($item['singlepage_select'], get_post_type($item['singlepage_select']));
                    if ($id_page == $pageid) {
                        $linkActive = ' class="active"';
                    } else {
                        $linkActive = '';
                    }
                    if (get_post_status($pageid) == 'publish' && $settings['show_publish']) {
                        echo '<li class="' . esc_attr('item-' . $pageid) . '"><a href="' . esc_url((string) get_permalink($pageid)) . '"' . $linkActive . '>' . wp_kses_post(get_the_title($pageid)) . '</a>';
                        echo '</li>';
                    }
                }
            } else {
                $children = $settings['singlepage_select'];
                if (!empty($children)) {
                    foreach ($children as $pageid) {
                        if ($id_page == $pageid) {
                            $linkActive = ' class="active"';
                        } else {
                            $linkActive = '';
                        }
                        echo '<li class="' . esc_attr('item-' . $pageid) . '"><a href="' . esc_url((string) get_permalink($pageid)) . '"' . $linkActive . '>' . wp_kses_post(get_the_title($pageid)) . '</a>';
                        echo '</li>';
                    }
                }
            }
            echo '</ul>';
        }
        if ($settings['show_border'] == 2) {
            echo '</div>';
        }
        echo '</nav>';
    }
}
