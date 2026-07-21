<?php

// SPDX-FileCopyrightText: 2018-2026 Ovation S.r.l. <help@dynamic.ooo>
// SPDX-License-Identifier: GPL-3.0-or-later
namespace DynamicContentForElementor\Widgets;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Css_Filter;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use DynamicContentForElementor\Helper;
if (!\defined('ABSPATH')) {
    exit;
    // Exit if accessed directly
}
class TaxonomyTermsList extends \DynamicContentForElementor\Widgets\WidgetPrototype
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
        $taxonomies = \DynamicContentForElementor\Helper::get_taxonomies();
        $this->start_controls_section('section_content', ['label' => $this->get_title()]);
        $this->add_control('taxonomy_select', ['label' => esc_html__('Select Taxonomy', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'options' => Helper::get_taxonomies(), 'default' => 'category']);
        foreach ($taxonomies as $tkey => $atax) {
            if ($tkey) {
                $this->add_control('prent_term_' . $tkey, ['label' => esc_html__('From parent term', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'options' => ['my_parent' => esc_html__('My parent', 'dynamic-content-for-elementor')] + Helper::get_parentterms($tkey), 'default' => '0', 'condition' => ['taxonomy_select' => $tkey], 'render_type' => 'template']);
            }
        }
        $this->add_control('menu_style', ['label' => esc_html__('Style', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'options' => ['horizontal' => esc_html__('Horizontal', 'dynamic-content-for-elementor'), 'vertical' => esc_html__('Vertical', 'dynamic-content-for-elementor')], 'default' => 'vertical']);
        $this->add_control('heading_settings_menu', ['label' => esc_html__('Settings', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HEADING, 'separator' => 'before']);
        $this->add_control('taxonomy_dynamic', ['label' => esc_html__('Dynamic', 'dynamic-content-for-elementor'), 'description' => esc_html__('Change depending on the page that displays it.', 'dynamic-content-for-elementor') . '<br>' . esc_html__('In the POST page will show all Terms associated to the current post.', 'dynamic-content-for-elementor') . '<br>' . esc_html__('In the TERM page will show all its Terms children.', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'sparator' => 'before']);
        $this->add_control('hide_empty', ['label' => esc_html__('Hide Empty', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => 'yes', 'sparator' => 'before']);
        $this->add_control('link_term', ['label' => esc_html__('Use Link', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => 'yes', 'sparator' => 'before']);
        $this->add_control('dce_tax_orderby', ['label' => esc_html__('Order by', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'options' => ['parent' => esc_html__('Parent', 'dynamic-content-for-elementor'), 'count' => esc_html__('Count (number of associated posts)', 'dynamic-content-for-elementor'), 'term_order' => esc_html__('Order', 'dynamic-content-for-elementor'), 'name' => esc_html__('Name', 'dynamic-content-for-elementor'), 'slug' => esc_html__('Slug', 'dynamic-content-for-elementor'), 'term_group' => esc_html__('Group', 'dynamic-content-for-elementor'), 'term_id' => 'ID'], 'default' => 'parent']);
        $this->add_control('dce_tax_order', ['label' => esc_html__('Sorting', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CHOOSE, 'options' => ['ASC' => ['title' => esc_html__('ASC', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-sort-up'], 'DESC' => ['title' => esc_html__('DESC', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-sort-down']], 'toggle' => \false, 'default' => 'ASC']);
        $this->add_control('heading_options_menu', ['label' => esc_html__('Options', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HEADING, 'separator' => 'before']);
        $this->add_control('show_taxonomy', ['label' => esc_html__('Show Taxonomy Name', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CHOOSE, 'toggle' => \false, 'label_block' => \false, 'options' => ['1' => ['title' => esc_html__('Yes', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-check'], '0' => ['title' => esc_html__('No', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-ban']], 'default' => '1']);
        $this->add_control('tax_text', ['label' => esc_html__('Custom Taxonomy Name', 'dynamic-content-for-elementor'), 'description' => esc_html__("If you don't want to use your native label", 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'default' => '', 'condition' => ['show_taxonomy' => '1']]);
        $this->add_control('tax_link', ['label' => esc_html__('Custom Link', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::URL, 'placeholder' => esc_html__('https://your-link.com', 'dynamic-content-for-elementor'), 'condition' => ['show_taxonomy' => '1', 'tax_text!' => ''], 'default' => ['url' => ''], 'show_label' => \false]);
        $this->add_control('show_childlist', ['label' => esc_html__('Show Child List', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CHOOSE, 'toggle' => \false, 'options' => ['1' => ['title' => esc_html__('Yes', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-check'], '0' => ['title' => esc_html__('No', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-ban']], 'default' => '1']);
        $this->add_control('show_childlist_depth', ['label' => esc_html__('Max Child Depth', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::NUMBER, 'min' => 0, 'condition' => ['show_childlist' => '1']]);
        $this->add_control('show_count', ['label' => esc_html__('Show Count', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER]);
        $this->add_responsive_control('show_border', ['label' => esc_html__('Show Border', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'toggle' => \false, 'options' => ['1' => esc_html__('Yes', 'dynamic-content-for-elementor'), '0' => esc_html__('No', 'dynamic-content-for-elementor'), '2' => esc_html__('Any', 'dynamic-content-for-elementor')], 'default' => '1', 'render_type' => 'template', 'prefix_class' => 'border-', 'condition' => ['show_taxonomy' => '1']]);
        $this->add_responsive_control('show_separators', ['label' => esc_html__('Show Separator', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CHOOSE, 'options' => ['solid' => ['title' => esc_html__('Yes', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-check'], 'hidden' => ['title' => esc_html__('Custom', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-edit'], 'none' => ['title' => esc_html__('No', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-ban']], 'condition' => ['menu_style' => 'horizontal'], 'toggle' => \true, 'default' => 'solid', 'selectors' => ['{{WRAPPER}} .dce-menu.horizontal li' => 'border-left-style: {{VALUE}};']]);
        $this->add_control('show_separators_custom', ['label' => esc_html__('Custom Separator', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'condition' => ['menu_style' => 'horizontal', 'show_separators' => 'hidden']]);
        $this->add_control('heading_spaces_menu', ['label' => esc_html__('Space', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HEADING, 'separator' => 'before']);
        $this->add_responsive_control('menu_space', ['label' => esc_html__('Header Space', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => 0], 'range' => ['px' => ['min' => 0, 'max' => 100]], 'selectors' => ['{{WRAPPER}} .dce-menu .dce-parent-title' => 'margin-bottom: calc( {{SIZE}}{{UNIT}} / 2);', '{{WRAPPER}} .dce-menu hr' => 'margin-bottom: calc( {{SIZE}}{{UNIT}} / 2);', '{{WRAPPER}} .dce-menu div.box' => 'padding: {{SIZE}}{{UNIT}};'], 'condition' => ['show_taxonomy' => '1']]);
        $this->add_responsive_control('item_width', ['label' => esc_html__('Items width', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => ''], 'size_units' => ['%', 'px'], 'range' => ['px' => ['min' => 0, 'max' => 300], '%' => ['min' => 0, 'max' => 100]], 'selectors' => ['{{WRAPPER}} .dce-menu.horizontal li' => 'width: {{SIZE}}{{UNIT}};'], 'condition' => ['menu_style' => 'horizontal']]);
        $this->add_responsive_control('menu_list_space', ['label' => esc_html__('List Space', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => 0], 'range' => ['px' => ['min' => 0, 'max' => 100]], 'selectors' => ['{{WRAPPER}} .dce-menu ul.first-level > li' => 'margin-bottom: {{SIZE}}{{UNIT}};'], 'condition' => ['show_childlist' => '1']]);
        $this->add_responsive_control('menu_indent', ['label' => esc_html__('Indent', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => 10], 'range' => ['px' => ['min' => 0, 'max' => 100]], 'selectors' => ['{{WRAPPER}} .dce-menu li' => 'padding-left: {{SIZE}}{{UNIT}}; padding-right: {{SIZE}}{{UNIT}};', '{{WRAPPER}} .dce-menu li .dce-term-separator' => 'margin-left: -{{SIZE}}{{UNIT}}; padding-right: {{SIZE}}{{UNIT}};']]);
        if (Helper::is_plugin_active('acf')) {
            $this->add_control('heading_image_acf', ['label' => esc_html__('Term Image', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HEADING, 'separator' => 'before']);
            $this->add_control('image_acf_enable', ['label' => esc_html__('Enable', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER]);
            $this->add_control('acf_field_image', ['label' => esc_html__('Image Field', 'dynamic-content-for-elementor'), 'type' => 'ooo_query', 'placeholder' => esc_html__('Select the field...', 'dynamic-content-for-elementor'), 'label_block' => \true, 'query_type' => 'metas', 'object_type' => 'term', 'condition' => ['image_acf_enable!' => '']]);
            $this->add_group_control(Group_Control_Image_Size::get_type(), ['name' => 'size', 'label' => esc_html__('Image Size', 'dynamic-content-for-elementor'), 'default' => 'large', 'render_type' => 'template', 'condition' => ['image_acf_enable!' => '']]);
            $this->add_control('block_enable', ['label' => esc_html__('Block', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'return_value' => 'block', 'selectors' => ['{{WRAPPER}} .dce-menu li img' => 'display: {{VALUE}};'], 'condition' => ['image_acf_enable!' => '']]);
            $this->add_control('image_acf_space', ['label' => esc_html__('Space', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => 0], 'range' => ['px' => ['min' => 0, 'max' => 100]], 'selectors' => ['{{WRAPPER}} .dce-menu li img' => 'margin-bottom: {{SIZE}}{{UNIT}};'], 'condition' => ['image_acf_enable!' => '', 'block_enable' => 'block']]);
            $this->add_control('image_acf_space_right', ['label' => esc_html__('Space', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => 5], 'range' => ['px' => ['min' => 0, 'max' => 100]], 'selectors' => ['{{WRAPPER}} .dce-menu li img' => 'margin-right: {{SIZE}}{{UNIT}};'], 'condition' => ['image_acf_enable!' => '', 'block_enable' => '']]);
            $this->add_responsive_control('space', ['label' => esc_html__('Size', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['unit' => '%'], 'size_units' => ['%', 'px'], 'range' => ['%' => ['min' => 1, 'max' => 100], 'px' => ['min' => 1, 'max' => 800]], 'selectors' => ['{{WRAPPER}} .dce-menu li img' => 'width: {{SIZE}}{{UNIT}};'], 'condition' => ['image_acf_enable!' => '']]);
        }
        $this->end_controls_section();
        $this->start_controls_section('section_style', ['label' => esc_html__('Style', 'dynamic-content-for-elementor'), 'tab' => Controls_Manager::TAB_STYLE]);
        $this->add_responsive_control('menu_align', ['label' => esc_html__('Text Alignment', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CHOOSE, 'toggle' => \false, 'options' => ['flex-start' => ['title' => esc_html__('Left', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-left'], 'center' => ['title' => esc_html__('Center', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-center'], 'flex-end' => ['title' => esc_html__('Right', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-right']], 'default' => 'flex-start', 'prefix_class' => 'menu-align-', 'selectors' => ['{{WRAPPER}} .dce-menu ul, {{WRAPPER}} .dce-parent-title' => 'align-items: {{VALUE}}; justify-content: {{VALUE}};']]);
        $this->add_control('heading_colors', ['label' => esc_html__('List items', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HEADING, 'separator' => 'before']);
        $this->add_control('menu_color', ['label' => esc_html__('Text Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'default' => '', 'selectors' => ['{{WRAPPER}} .dce-menu a, {{WRAPPER}} .dce-menu li' => 'color: {{VALUE}};']]);
        $this->add_control('menu_color_hover', ['label' => esc_html__('Text Hover Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'default' => '', 'selectors' => ['{{WRAPPER}} .dce-menu a:hover' => 'color: {{VALUE}};'], 'condition' => ['link_term!' => '']]);
        $this->add_control('menu_color_active', ['label' => esc_html__('Text Active Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'default' => '', 'selectors' => ['{{WRAPPER}} .dce-menu a.active' => 'color: {{VALUE}};']]);
        $this->add_group_control(Group_Control_Typography::get_type(), ['name' => 'typography_list', 'selector' => '{{WRAPPER}} .dce-menu li']);
        $this->add_control('heading_title', ['label' => esc_html__('Title', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HEADING, 'separator' => 'before', 'condition' => ['show_taxonomy' => '1']]);
        $this->add_control('menu_title_color', ['label' => esc_html__('Title Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'condition' => ['show_taxonomy' => '1'], 'default' => '', 'selectors' => ['{{WRAPPER}} .dce-menu .dce-parent-title a' => 'color: {{VALUE}};']]);
        $this->add_control('menu_title_color_hover', ['label' => esc_html__('Title Hover Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'default' => '', 'selectors' => ['{{WRAPPER}} .dce-menu .dce-parent-title a:hover' => 'color: {{VALUE}};'], 'condition' => ['show_taxonomy' => '1']]);
        $this->add_group_control(Group_Control_Typography::get_type(), ['name' => 'typography_tit', 'selector' => '{{WRAPPER}} .dce-menu .dce-parent-title', 'condition' => ['show_taxonomy' => '1']]);
        $this->add_control('heading_border', ['label' => esc_html__('Border', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HEADING, 'separator' => 'before', 'condition' => ['show_border' => ['1', '2']]]);
        $this->add_control('menu_border_color', ['label' => esc_html__('Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'toggle' => \false, 'label_block' => \false, 'default' => '', 'condition' => ['show_border' => ['1', '2']], 'selectors' => ['{{WRAPPER}} .dce-menu hr' => 'border-color: {{VALUE}};', '{{WRAPPER}} .dce-menu .box' => 'border-color: {{VALUE}};']]);
        $this->add_control('menu_border_size', ['label' => esc_html__('Weight', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'toggle' => \false, 'default' => ['size' => 1, 'unit' => 'px'], 'size_units' => ['px'], 'range' => ['px' => ['min' => 1, 'max' => 20]], 'selectors' => ['{{WRAPPER}} .dce-menu hr' => 'border-width: {{SIZE}}{{UNIT}};'], 'condition' => ['show_border' => ['1', '2']]]);
        $this->add_control('menu_border_width', ['label' => esc_html__('Width', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'toggle' => \false, 'size_units' => ['px', '%'], 'range' => ['px' => ['min' => 1, 'max' => 1000], '%' => ['min' => 1, 'max' => 100]], 'selectors' => ['{{WRAPPER}} .dce-menu hr' => 'width: {{SIZE}}{{UNIT}};'], 'condition' => ['show_border' => ['1', '2']]]);
        $this->add_control('heading_separator', ['label' => esc_html__('Separator', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HEADING, 'separator' => 'before', 'condition' => ['show_separators' => ['solid', 'hidden'], 'menu_style' => 'horizontal']]);
        $this->add_control('menu_color_separator', ['label' => esc_html__('Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'condition' => ['show_separators' => ['solid', 'hidden'], 'menu_style' => 'horizontal'], 'default' => '#999999', 'selectors' => ['{{WRAPPER}} .dce-menu.horizontal li' => 'border-left-color: {{VALUE}};', '{{WRAPPER}} .dce-menu.horizontal li .dce-term-separator' => 'color: {{VALUE}};']]);
        $this->add_responsive_control('menu_size_separator', ['label' => esc_html__('Weight', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => 1, 'unit' => 'px'], 'size_units' => ['px'], 'range' => ['px' => ['min' => 0, 'max' => 50]], 'selectors' => ['{{WRAPPER}} .dce-menu.horizontal li' => 'border-left-width: {{SIZE}}{{UNIT}};'], 'condition' => ['show_separators' => 'solid', 'menu_style' => 'horizontal']]);
        $this->end_controls_section();
        $this->start_controls_section('section_image_style', ['label' => esc_html__('Image', 'dynamic-content-for-elementor'), 'tab' => Controls_Manager::TAB_STYLE, 'condition' => ['image_acf_enable!' => '']]);
        $this->start_controls_tabs('dce_image_effects');
        $this->start_controls_tab('dce_image_normal', ['label' => esc_html__('Normal', 'dynamic-content-for-elementor')]);
        $this->add_control('dce_image_opacity', ['label' => esc_html__('Opacity', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'range' => ['px' => ['max' => 1, 'min' => 0.1, 'step' => 0.01]], 'selectors' => ['{{WRAPPER}} .dce-menu li img' => 'opacity: {{SIZE}};']]);
        $this->add_group_control(Group_Control_Css_Filter::get_type(), ['name' => 'dce_image_css_filters', 'selector' => '{{WRAPPER}} .dce-menu li img']);
        $this->end_controls_tab();
        $this->start_controls_tab('dce_image_hover', ['label' => esc_html__('Hover', 'dynamic-content-for-elementor')]);
        $this->add_control('dce_image_opacity_hover', ['label' => esc_html__('Opacity', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'range' => ['px' => ['max' => 1, 'min' => 0.1, 'step' => 0.01]], 'selectors' => ['{{WRAPPER}} .dce-menu li a:hover img' => 'opacity: {{SIZE}};']]);
        $this->add_group_control(Group_Control_Css_Filter::get_type(), ['name' => 'dce_image_css_filters_hover', 'selector' => '{{WRAPPER}} .dce-menu li a:hover img']);
        $this->add_control('dce_image_background_hover_transition', ['label' => esc_html__('Transition Duration', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'range' => ['px' => ['max' => 3, 'step' => 0.1]], 'selectors' => ['{{WRAPPER}} .dce-menu li img' => 'transition-duration: {{SIZE}}s']]);
        $this->end_controls_tab();
        $this->end_controls_tabs();
        $this->add_group_control(Group_Control_Border::get_type(), ['name' => 'dce_image_border', 'label' => esc_html__('Border', 'dynamic-content-for-elementor'), 'selector' => '{{WRAPPER}} .dce-menu li img', 'separator' => 'before']);
        $this->add_responsive_control('dce_image_border_radius', ['label' => esc_html__('Border Radius', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => ['px', '%'], 'selectors' => ['{{WRAPPER}} .dce-menu li img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};']]);
        $this->add_group_control(Group_Control_Box_Shadow::get_type(), ['name' => 'dce_image_box_shadow', 'selector' => '{{WRAPPER}} .dce-menu li img']);
        $this->add_responsive_control('dce_image_padding', ['label' => esc_html__('Padding', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::DIMENSIONS, 'selectors' => ['{{WRAPPER}} .dce-menu li img' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};']]);
        $this->end_controls_section();
    }
    protected function safe_render()
    {
        $settings = $this->get_settings_for_display();
        if (empty($settings)) {
            return;
        }
        $id_page = Helper::get_the_id();
        if ($settings['taxonomy_dynamic']) {
            $queried_object = get_queried_object();
            if ($queried_object) {
                if (\get_class($queried_object) == 'WP_Term') {
                    $term_ID = $queried_object->term_id;
                    $term_ID_parent = $queried_object->parent;
                    $terms_args = array('taxonomy' => $queried_object->taxonomy, 'hide_empty' => !empty($settings['hide_empty']) ? \true : \false, 'orderby' => $settings['dce_tax_orderby'], 'order' => $settings['dce_tax_order']);
                    $parentTerm = $settings['prent_term_' . $settings['taxonomy_select']];
                    if ($parentTerm == 'my_parent') {
                        $terms_args['parent'] = $term_ID_parent;
                    } else {
                        $terms_args['parent'] = $term_ID;
                    }
                    $terms = get_terms($terms_args);
                }
            }
            if ($queried_object && \get_class($queried_object) == 'WP_Post' || $id_page && in_the_loop()) {
                $terms = wp_get_post_terms($id_page, $settings['taxonomy_select'], array('hide_empty' => !empty($settings['hide_empty']) ? \true : \false, 'orderby' => $settings['dce_tax_orderby'], 'order' => $settings['dce_tax_order']));
                $parentTerm = $settings['prent_term_' . $settings['taxonomy_select']] ?? '0';
                if ($parentTerm && $parentTerm !== '0' && !is_wp_error($terms)) {
                    $terms = \array_values(\array_filter($terms, function ($term) use($parentTerm) {
                        return (int) $term->parent === (int) $parentTerm;
                    }));
                }
            }
        } else {
            // Taxonomy Not Dynamic
            $parentTerm = $settings['prent_term_' . $settings['taxonomy_select']];
            $terms_args = array('taxonomy' => $settings['taxonomy_select'], 'hide_empty' => !empty($settings['hide_empty']) ? \true : \false, 'orderby' => $settings['dce_tax_orderby'], 'order' => $settings['dce_tax_order']);
            if ($parentTerm) {
                $terms_args['parent'] = $parentTerm;
            } else {
                $terms_args['parent'] = 0;
            }
            $terms = get_terms($terms_args);
        }
        $styleMenu = $settings['menu_style'];
        $clssStyleMenu = $styleMenu;
        if (!empty($terms) && !is_wp_error($terms)) {
            echo '<nav class="' . esc_attr('dce-menu dce-flex-menu ' . $clssStyleMenu) . '">';
            if ($settings['show_border'] == 2) {
                echo '<div class="box">';
            }
            if ($settings['show_taxonomy'] != 0) {
                // From Parent
                if (isset($settings['prent_term_' . $settings['taxonomy_select']])) {
                    $parentTaxonomy = $settings['prent_term_' . $settings['taxonomy_select']];
                } else {
                    $parentTaxonomy = '';
                }
                if ($settings['tax_text'] != '') {
                    $taxtext = wp_kses_post($settings['tax_text']);
                } elseif ($parentTaxonomy) {
                    $taxtext = esc_html(get_term($parentTaxonomy)->name);
                } else {
                    $taxtext = esc_html($settings['taxonomy_select']);
                }
                if (!empty($settings['tax_link']) && $settings['tax_link']['url'] != '') {
                    $taxlink = $settings['tax_link']['url'];
                } elseif ($parentTaxonomy) {
                    $taxlink = get_term_link((int) $parentTaxonomy);
                    if (is_wp_error($taxlink)) {
                        $taxlink = '';
                    }
                } else {
                    $taxlink = get_post_type_archive_link($settings['taxonomy_select']);
                }
                $linkStart = '';
                $linkEnd = '';
                if ($taxlink != '') {
                    $linkStart = '<a href="' . esc_url($taxlink) . '">';
                    $linkEnd = '</a>';
                }
                echo '<h3 class="dce-parent-title">' . $linkStart . $taxtext . $linkEnd . '</h3>';
                if ($settings['show_border'] == 1) {
                    echo '<hr />';
                }
            }
            echo '<ul class="first-level">';
            $myID = get_queried_object_id();
            $queried_object = get_queried_object();
            // post terms
            if (is_singular()) {
                $myTerms = wp_get_post_terms(get_the_ID(), $settings['taxonomy_select']);
                if (!empty($myTerms)) {
                    $tmp_term = \reset($myTerms);
                }
            }
            // terms by id
            $terms_by_id = array();
            foreach ($terms as $tkey => $term) {
                $terms_by_id[$term->term_id] = $term;
            }
            $terms = $terms_by_id;
            $terms_count = \count($terms);
            $counter = 0;
            foreach ($terms as $tkey => $term) {
                if ($term->parent) {
                    if (isset($terms[$term->parent])) {
                        continue;
                    }
                }
                ++$counter;
                $term_link = get_term_link($term);
                if (is_wp_error($term_link)) {
                    $term_link = '';
                }
                $linkActive = '';
                $tmp_term = '';
                // term archive page
                if (is_tag() || is_category() || is_tax() && $queried_object && \get_class($queried_object) === 'WP_Term') {
                    $tmp_term = $queried_object;
                }
                if ($tmp_term && $tmp_term->term_id == $term->term_id) {
                    $linkActive = ' class="active"';
                }
                // ACF Image
                $image_acf = '';
                if (isset($settings['image_acf_enable']) && $settings['image_acf_enable']) {
                    $idFields = $settings['acf_field_image'];
                    $imageField = get_term_meta($term->term_id, $idFields, \true);
                    $typeField = '';
                    if ($imageField) {
                        $imageSrc = \false;
                        if (\is_numeric($imageField)) {
                            $typeField = 'image';
                            $imageSrc = Group_Control_Image_Size::get_attachment_image_src($imageField, 'size', $settings);
                        } elseif (\is_string($imageField)) {
                            $typeField = 'image_url';
                            $imageSrc = $imageField;
                        } elseif (\is_array($imageField)) {
                            $typeField = 'image_array';
                            $imageSrc = Group_Control_Image_Size::get_attachment_image_src($imageField['ID'], 'size', $settings);
                        }
                        if ($imageSrc) {
                            $image_acf = '<img src="' . esc_url($imageSrc) . '" alt="' . esc_attr($term->name) . '" />';
                        }
                    }
                }
                $a_start = '';
                $a_end = '';
                if ($settings['link_term']) {
                    $a_start = '<a href="' . esc_url($term_link) . '"' . $linkActive . '>';
                    $a_end = '</a>';
                }
                if ($tkey && $terms_count > 1) {
                    if ($settings['show_separators_custom'] && $counter < $terms_count) {
                        $a_end = '<span class="dce-term-separator">' . wp_kses_post($settings['show_separators_custom']) . '</span>' . $a_end;
                    }
                }
                $tcount = '';
                if ($settings['show_count']) {
                    $tcount = ' (' . $term->count . ')';
                }
                echo '<li class="' . esc_attr('dce-term-' . $term->term_id) . '">' . $a_start . $image_acf . '<span>' . esc_html($term->name) . esc_html($tcount) . '</span>' . $a_end;
                if ($settings['show_childlist']) {
                    $nterms = $this->child_terms_list($term->term_id, $settings, $terms);
                }
                if (empty($nterms) || $settings['menu_style'] != 'horizontal') {
                    echo '</li>';
                }
            }
            echo '</ul>';
            if ($settings['show_border'] == 2) {
                echo '</div>';
            }
            echo '</nav>';
        }
    }
    public function child_terms_list($parent, $settings, $terms, $level = 0)
    {
        ++$level;
        if ($settings['show_childlist_depth'] !== '' && $level > $settings['show_childlist_depth']) {
            return 0;
        }
        $terms_args = array('taxonomy' => $settings['taxonomy_select'], 'hide_empty' => !empty($settings['hide_empty']) ? \true : \false, 'orderby' => $settings['dce_tax_orderby'], 'order' => $settings['dce_tax_order'], 'parent' => $parent);
        if ($settings['taxonomy_dynamic']) {
            if (get_queried_object() instanceof \WP_Post) {
                $terms_args['include'] = \array_map('intval', \array_keys($terms));
            }
        }
        $sub_terms = get_terms($terms_args);
        if (is_wp_error($sub_terms)) {
            return 0;
        }
        $queried_object = get_queried_object();
        if (!empty($sub_terms)) {
            if ($settings['menu_style'] != 'horizontal') {
                echo '<ul class="' . esc_attr('child-level terms-parent-' . $parent) . '">';
            } else {
                echo '</li>';
            }
            foreach ($sub_terms as $tkey => $term) {
                $term_link = get_term_link($term);
                if (is_wp_error($term_link)) {
                    $term_link = '';
                }
                $tmp_term = $linkActive = '';
                // term archive page
                if (is_tag() || is_category() || is_tax()) {
                    if ($queried_object) {
                        if (\get_class($queried_object) == 'WP_Term') {
                            $tmp_term = $queried_object;
                        }
                    }
                }
                if ($tmp_term && $tmp_term->term_id == $term->term_id) {
                    $linkActive = ' class="active"';
                }
                // ACF Image
                $image_acf = '';
                if (isset($settings['image_acf_enable']) && $settings['image_acf_enable']) {
                    $idFields = $settings['acf_field_image'];
                    $imageField = \get_field($idFields, 'term_' . $term->term_id);
                    $typeField = '';
                    if ($imageField) {
                        $imageSrc = \false;
                        if (\is_string($imageField)) {
                            $typeField = 'image_url';
                            $imageSrc = $imageField;
                        } elseif (\is_numeric($imageField)) {
                            $typeField = 'image';
                            $imageSrc = Group_Control_Image_Size::get_attachment_image_src($imageField, 'size', $settings);
                        } elseif (\is_array($imageField)) {
                            $typeField = 'image_array';
                            $imageSrc = Group_Control_Image_Size::get_attachment_image_src($imageField['ID'], 'size', $settings);
                        }
                        if ($imageSrc) {
                            $image_acf = '<img src="' . esc_url($imageSrc) . '" alt="' . esc_attr($term->name) . '" />';
                        }
                    }
                }
                $a_start = '';
                $a_end = '';
                if ($settings['link_term']) {
                    $a_start = '<a href="' . esc_url($term_link) . '"' . $linkActive . '>';
                    $a_end = '</a>';
                }
                if ($tkey) {
                    if ($settings['show_separators_custom']) {
                        $a_start = '<span class="dce-term-separator">' . wp_kses_post($settings['show_separators_custom']) . '</span>' . $a_start;
                    }
                }
                $tcount = '';
                if ($settings['show_count']) {
                    $tcount = ' (' . $term->count . ')';
                }
                echo '<li class="' . esc_attr('dce-term-' . $term->term_id . ' dce-term-parent-' . $term->parent) . '">' . $a_start . $image_acf . '<span>' . esc_html($term->name) . esc_html($tcount) . '</span>' . $a_end;
                if ($settings['show_childlist']) {
                    $this->child_terms_list($term->term_id, $settings, $terms, $level);
                }
                echo '</li>';
            }
            if ($settings['menu_style'] != 'horizontal') {
                echo '</ul>';
            }
        }
        return \count($sub_terms);
    }
}
