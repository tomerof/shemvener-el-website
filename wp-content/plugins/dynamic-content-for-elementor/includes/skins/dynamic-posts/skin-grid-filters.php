<?php

// SPDX-FileCopyrightText: 2018-2026 Ovation S.r.l. <help@dynamic.ooo>
// SPDX-License-Identifier: GPL-3.0-or-later
namespace DynamicContentForElementor\Includes\Skins;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use DynamicContentForElementor\Helper;
if (!\defined('ABSPATH')) {
    exit;
    // Exit if accessed directly
}
class SkinGridFilters extends \DynamicContentForElementor\Includes\Skins\SkinGrid
{
    /**
     * Register Controls Actions
     *
     * @return void
     */
    protected function _register_controls_actions()
    {
        add_action('elementor/element/' . $this->get_parent()->get_name() . '/section_query/after_section_end', [$this, 'register_controls_layout']);
        add_action('elementor/element/' . $this->get_parent()->get_name() . '/section_dynamicposts/after_section_end', [$this, 'register_additional_grid_controls'], 20);
        add_action('elementor/element/' . $this->get_parent()->get_name() . '/section_dynamicposts/after_section_end', [$this, 'register_additional_filters_controls'], 11);
    }
    public $depended_scripts = ['dce-dynamicPosts-grid-filters'];
    public $depended_styles = [];
    public function get_script_depends()
    {
        return \array_merge(['imagesloaded', 'dce-dynamicPosts-grid', 'jquery-masonry', 'dce-infinitescroll', 'dce-isotope', 'dce-jquery-match-height'], $this->depended_scripts);
    }
    public function get_style_depends()
    {
        return \array_merge(['dce-dynamicPosts-grid'], $this->depended_styles);
    }
    public function get_id()
    {
        return 'grid-filters';
    }
    public function get_title()
    {
        return esc_html__('Grid with Filters', 'dynamic-content-for-elementor');
    }
    public function register_additional_grid_controls(\DynamicContentForElementor\Widgets\DynamicPostsBase $widget)
    {
        $this->parent = $widget;
        parent::register_additional_grid_controls($widget);
        // Remove controls from Skin Grid
        $this->remove_control('flex_grow');
        $this->remove_control('v_pos_postitems');
        $this->remove_control('h_pos_postitems');
    }
    public function register_additional_filters_controls(\DynamicContentForElementor\Widgets\DynamicPostsBase $widget)
    {
        $this->parent = $widget;
        $taxonomies = Helper::get_taxonomies();
        $this->start_controls_section('section_filters', ['label' => esc_html__('Filters', 'dynamic-content-for-elementor'), 'tab' => Controls_Manager::TAB_CONTENT]);
        $this->add_control('filters_skin', ['label' => esc_html__('Filters Style', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'options' => ['links-list' => esc_html__('Links List', 'dynamic-content-for-elementor'), 'select' => esc_html__('Select', 'dynamic-content-for-elementor')], 'default' => 'links-list', 'label_block' => \true]);
        $this->add_control('filters_taxonomy', ['label' => esc_html__('Data Filters (Taxonomy)', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'options' => ['' => esc_html__('None', 'dynamic-content-for-elementor')] + $taxonomies, 'default' => 'category', 'label_block' => \true]);
        $this->add_control('filters_taxonomy_first_level_terms', ['label' => esc_html__('Use first level Terms', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => '', 'condition' => [$this->get_control_id('filters_taxonomy!') => '']]);
        foreach ($taxonomies as $tkey => $atax) {
            if ($tkey) {
                $this->add_control('filters_taxonomy_terms_' . $tkey, ['label' => esc_html__('Data Filters (Selected Terms)', 'dynamic-content-for-elementor'), 'type' => 'ooo_query', 'placeholder' => esc_html__('Term Name', 'dynamic-content-for-elementor'), 'label_block' => \true, 'query_type' => 'terms', 'object_type' => $tkey, 'description' => esc_html__('Use only Selected taxonomy terms or leave empty to use All terms of this taxonomy', 'dynamic-content-for-elementor'), 'multiple' => \true, 'condition' => [$this->get_control_id('filters_taxonomy') => $tkey, $this->get_control_id('filters_taxonomy_first_level_terms') => '']]);
            }
        }
        $this->add_control('orderby_filters', ['label' => esc_html__('Order By', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'options' => ['parent' => esc_html__('Parent', 'dynamic-content-for-elementor'), 'count' => esc_html__('Count (number of associated posts)', 'dynamic-content-for-elementor'), 'term_order' => esc_html__('Order', 'dynamic-content-for-elementor'), 'name' => esc_html__('Name', 'dynamic-content-for-elementor'), 'slug' => esc_html__('Slug', 'dynamic-content-for-elementor'), 'term_group' => esc_html__('Group', 'dynamic-content-for-elementor'), 'term_id' => 'ID'], 'default' => 'name']);
        $this->add_control('order_filters', ['label' => esc_html__('Sorting', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CHOOSE, 'options' => ['ASC' => ['title' => esc_html__('ASC', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-sort-up'], 'DESC' => ['title' => esc_html__('DESC', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-sort-down']], 'toggle' => \false, 'default' => 'ASC']);
        $this->add_control('all_filter', ['label' => esc_html__('Add "All" filter', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => 'yes', 'frontend_available' => \true]);
        $this->add_control('all_default', ['label' => esc_html__('"All" filter is default', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => 'yes', 'frontend_available' => \true, 'condition' => [$this->get_control_id('all_filter!') => '']]);
        $this->add_control('alltext_filter', ['label' => esc_html__('All text', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'default' => esc_html__('All', 'dynamic-content-for-elementor'), 'condition' => [$this->get_control_id('all_filter!') => '']]);
        $this->add_control('separator_filter', ['label' => esc_html__('Separator', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'default' => ' / ', 'condition' => [$this->get_control_id('filters_skin') => 'links-list']]);
        $this->end_controls_section();
    }
    protected function register_style_controls()
    {
        parent::register_style_controls();
        $this->start_controls_section('section_style_filters', ['label' => esc_html__('Filters', 'dynamic-content-for-elementor'), 'tab' => Controls_Manager::TAB_STYLE, 'condition' => [$this->get_control_id('filters_skin') => 'links-list']]);
        $this->add_responsive_control('filters_align', ['label' => esc_html__('Alignment', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CHOOSE, 'toggle' => \false, 'options' => ['left' => ['title' => esc_html__('Left', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-left'], 'center' => ['title' => esc_html__('Center', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-center'], 'right' => ['title' => esc_html__('Right', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-right']], 'default' => is_rtl() ? 'right' : 'left']);
        $this->add_control('filters_color', ['label' => esc_html__('Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .dce-filters .filters-item a' => 'color: {{VALUE}};']]);
        $this->add_control('filters_color_hover', ['label' => esc_html__('Color Hover', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .dce-filters .filters-item a:hover' => 'color: {{VALUE}};']]);
        $this->add_control('filters_color_active', ['label' => esc_html__('Color Active', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'default' => '#990000', 'selectors' => ['{{WRAPPER}} .dce-filters .filters-item.filter-active a' => 'color: {{VALUE}};']]);
        $this->add_control('filters_color_separator', ['label' => esc_html__('Separator Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .dce-filters .filters-separator' => 'color: {{VALUE}};']]);
        $this->add_group_control(Group_Control_Typography::get_type(), ['name' => 'typography_filters', 'label' => esc_html__('Typography', 'dynamic-content-for-elementor'), 'selector' => '{{WRAPPER}} .dce-filters']);
        $this->add_group_control(Group_Control_Typography::get_type(), ['name' => 'typography_filters_separator', 'label' => esc_html__('Typography Separator', 'dynamic-content-for-elementor'), 'selector' => '{{WRAPPER}} .dce-filters .filters-separator']);
        $this->add_responsive_control('filters_padding_items', ['label' => esc_html__('Spacing', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => 5, 'unit' => 'px'], 'tablet_default' => ['unit' => 'px'], 'mobile_default' => ['unit' => 'px'], 'size_units' => ['px', '%'], 'range' => ['%' => ['min' => 0, 'max' => 100], 'px' => ['min' => 0, 'max' => 100]], 'selectors' => ['{{WRAPPER}} .dce-filters .filters-separator' => 'padding-left: {{SIZE}}{{UNIT}}; padding-right: {{SIZE}}{{UNIT}};']]);
        $this->add_control('filters_padding', ['label' => esc_html__('Padding', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => ['px', '%'], 'selectors' => ['{{WRAPPER}} .dce-filters' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};']]);
        $this->add_control('filters_margin', ['label' => esc_html__('Margin', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => ['px', '%'], 'default' => ['top' => '0', 'right' => '0', 'bottom' => '20', 'left' => '0', 'unit' => 'px', 'isLinked' => \false], 'selectors' => ['{{WRAPPER}} .dce-filters' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};']]);
        $this->add_responsive_control('filters_move_separator', ['label' => esc_html__('Vertical Shift Separator', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => 0, 'unit' => 'px'], 'tablet_default' => ['unit' => 'px'], 'mobile_default' => ['unit' => 'px'], 'size_units' => ['px', '%'], 'range' => ['%' => ['min' => -100, 'max' => 100], 'px' => ['min' => -100, 'max' => 100]], 'selectors' => ['{{WRAPPER}} .dce-filters .filters-separator' => 'top: {{SIZE}}{{UNIT}}; position: relative;']]);
        $this->end_controls_section();
    }
    /**
     * Render Grid Filters Bar
     *
     * @return void
     */
    protected function render_grid_filters_bar()
    {
        if (!$this->get_instance_value('filters_taxonomy')) {
            return;
        }
        $p_query = $this->get_parent()->get_query();
        $term_filter = esc_html($this->get_instance_value('filters_taxonomy'));
        $args_filters = ['taxonomy' => $term_filter, 'object_ids' => wp_list_pluck($p_query->posts, 'ID')];
        if (Helper::is_plugin_active('wpml')) {
            $args_filters['object_ids'] = Helper::wpml_translate_object_id($args_filters['object_ids']);
        }
        $include_terms = [];
        if ($this->get_instance_value('filters_taxonomy_first_level_terms')) {
            $args_filters['parent'] = 0;
        } elseif ($this->get_instance_value('filters_taxonomy_terms_' . $term_filter) && !empty($this->get_instance_value('filters_taxonomy_terms_' . $term_filter))) {
            $include_terms = $this->get_instance_value('filters_taxonomy_terms_' . $term_filter);
        }
        $args_filters['include'] = $include_terms;
        $args_filters['orderby'] = $this->get_instance_value('orderby_filters');
        $args_filters['order'] = $this->get_instance_value('order_filters');
        $term_list_filters = get_terms($args_filters);
        if (!is_wp_error($term_list_filters)) {
            $this->render_filter($term_list_filters);
        }
    }
    /**
     * Render Filter
     *
     * @param array<int,\WP_Term> $terms
     * @return void
     */
    protected function render_filter(array $terms)
    {
        if (empty($terms)) {
            return;
        }
        $filters_skin = $this->get_instance_value('filters_skin');
        $parent = $this->get_parent();
        $parent->set_render_attribute('filter', 'class', ['dce-filters', 'align-' . $this->get_instance_value('filters_align')]);
        $all_filter = $this->get_instance_value('all_filter') === 'yes';
        $all_default = $this->get_instance_value('all_default') === 'yes';
        $parent->set_render_attribute('separator', 'class', 'filters-separator');
        $this->add_direction('filter');
        echo '<div ' . $parent->get_render_attribute_string('filter') . '>';
        $separator = '';
        if ($filters_skin === 'select') {
            echo '<select ' . $parent->get_render_attribute_string('filter-select') . '>';
        } else {
            $separator = '<span ' . $parent->get_render_attribute_string('separator') . '>';
            $separator .= wp_kses_post($this->get_instance_value('separator_filter'));
            $separator .= '</span>';
        }
        if ($all_filter) {
            $this->render_all_text($all_default, $filters_skin);
            if ($filters_skin === 'links-list') {
                echo $separator;
            }
        }
        foreach ($terms as $key => $term) {
            if ($key) {
                echo $separator;
            }
            $term_link = get_term_link($term->term_id);
            $term_link = is_wp_error($term_link) ? '' : $term_link;
            $active = 0 === $key && !($all_filter & $all_default);
            // Filter Item
            $parent->set_render_attribute('filter-item', 'class', 'filters-item');
            if ($active) {
                $parent->add_render_attribute('filter-item', 'class', 'filter-active');
            }
            $parent->set_render_attribute('filter-item-link', 'href', '#');
            // Disable the Transition functionality for that specific link
            $parent->set_render_attribute('filter-item-link', 'data-e-disable-page-transition', 'false');
            // Taxonomy Class
            $taxonomy_class = sanitize_html_class($term->taxonomy);
            // 'post_tag' taxonomy uses the 'tag' prefix for backward compatibility
            if ('post_tag' === $term->taxonomy) {
                $taxonomy_class = 'tag';
            }
            // Term Class
            $term_class = sanitize_html_class($term->slug);
            if (\is_numeric($term_class) || !\trim($term_class, '-')) {
                $term_class = $term->term_id;
            }
            $css_filter = '.' . $taxonomy_class . '-' . $term_class;
            if ($filters_skin === 'select') {
                $option_key = "filter-option-{$key}";
                $parent->set_render_attribute($option_key, 'value', $css_filter);
                if ($active) {
                    $parent->set_render_attribute($option_key, 'selected', 'true');
                }
                echo '<option ' . $parent->get_render_attribute_string($option_key) . '">';
                echo esc_html($term->name);
                echo '</option>';
            } else {
                $parent->set_render_attribute('filter-item-link', 'data-filter', $css_filter);
                echo '<span ' . $parent->get_render_attribute_string('filter-item') . '>';
                echo '<a ' . $parent->get_render_attribute_string('filter-item-link') . '>';
                echo esc_html($term->name);
                echo '</a>';
                echo '</span>';
            }
        }
        echo '</select>';
        echo '</div>';
    }
    /**
     * Render All Text
     *
     * @return void
     */
    protected function render_all_text($default, $filters_skin)
    {
        $all_text = wp_kses_post($this->get_instance_value('alltext_filter'));
        if ($filters_skin === 'select') {
            echo '<option value="*"';
            if ($default) {
                echo ' selected';
            }
            echo '>';
            echo $all_text;
            echo '</option>';
        } else {
            $this->get_parent()->set_render_attribute('filter-item', 'class', 'filters-item');
            if ($default) {
                $this->get_parent()->add_render_attribute('filter-item', 'class', 'filter-active');
            }
            echo '<span ' . $this->get_parent()->get_render_attribute_string('filter-item') . '>';
            echo '<a href="#" data-filter="*">' . $all_text . '</a>';
            echo '</span>';
        }
    }
    protected function render_posts_before()
    {
        $this->render_grid_filters_bar();
    }
    public function get_container_class()
    {
        return 'dce-' . $this->get_id() . '-container dce-skin-' . $this->get_id() . ' dce-skin-' . parent::get_id() . ' dce-skin-' . parent::get_id() . '-' . $this->get_instance_value('grid_type');
    }
    public function get_wrapper_class()
    {
        return 'dce-' . $this->get_id() . '-wrapper dce-wrapper-' . $this->get_id() . ' dce-wrapper-' . parent::get_id();
    }
    public function get_item_class()
    {
        return 'dce-item-filterable dce-' . $this->get_id() . '-item dce-item-' . $this->get_id() . ' dce-item-' . parent::get_id();
    }
}
