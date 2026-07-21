<?php

// SPDX-FileCopyrightText: 2018-2026 Ovation S.r.l. <help@dynamic.ooo>
// SPDX-License-Identifier: GPL-3.0-or-later
namespace DynamicContentForElementor\Widgets;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Typography;
use Elementor\Repeater;
use Elementor\Utils;
use Elementor\Icons_Manager;
use DynamicContentForElementor\Helper;
use DynamicContentForElementor\Controls\Group_Control_Transform_Element;
use DynamicContentForElementor\Controls\Group_Control_Filters_CSS;
if (!\defined('ABSPATH')) {
    exit;
}
// Exit if accessed directly
class DynamicPostsOldVersion extends \DynamicContentForElementor\Widgets\WidgetPrototype
{
    /**
     * @return array<string>
     */
    public function get_script_depends()
    {
        return ['imagesloaded', 'swiper', 'dce-jquery-slick', 'dce-isotope', 'dce-infinitescroll', 'dce-wow', 'dce-dynamic-posts-old-version'];
    }
    /**
     * @return array<string>
     */
    public function get_style_depends()
    {
        return ['animatecss', 'swiper', 'dce-dynamicPosts_slick', 'dce-dynamicPosts_timeline', 'dce-dynamic-posts-old-version'];
    }
    /**
     * @return void
     */
    protected function safe_register_controls()
    {
        $taxonomies = Helper::get_taxonomies();
        $types = Helper::get_public_post_types();
        $this->start_controls_section('section_cpt', ['label' => esc_html__('Post Type Query', 'dynamic-content-for-elementor')]);
        $this->add_control('deprecated', ['raw' => esc_html__('This widget is deprecated. You can continue to use it but we recommend that you use Dynamic Posts instead.', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::RAW_HTML, 'content_classes' => 'elementor-panel-alert elementor-panel-alert-warning']);
        $this->add_control('query_type', ['label' => esc_html__('Query Type', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CHOOSE, 'toggle' => \false, 'options' => ['get_cpt' => ['title' => esc_html__('Custom Post Type', 'dynamic-content-for-elementor'), 'icon' => 'eicon-post-content'], 'dynamic_mode' => ['title' => esc_html__('Dynamic', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-cogs'], 'acf_relations' => ['title' => esc_html__('ACF Relations', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-american-sign-language-interpreting'], 'specific_posts' => ['title' => esc_html__('From Specific Post', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-list-ul']], 'default' => 'get_cpt']);
        // --------------------------------- [ Specific Pages ]
        foreach ($types as $t => $tname) {
            $object_t = get_post_type_object($t)->labels;
            $label_t = $object_t->name;
            $this->add_control('specific_pages' . $t, ['label' => $label_t, 'type' => 'ooo_query', 'placeholder' => esc_html__('Post Title', 'dynamic-content-for-elementor'), 'label_block' => \true, 'query_type' => 'posts', 'object_type' => $t, 'multiple' => \true, 'condition' => ['query_type' => 'specific_posts']]);
        }
        $this->add_control('acf_relationship', ['label' => esc_html__('Relations (ACF)', 'dynamic-content-for-elementor'), 'type' => 'ooo_query', 'placeholder' => esc_html__('Select the field...', 'dynamic-content-for-elementor'), 'label_block' => \true, 'query_type' => 'acf', 'object_type' => ['post_object', 'relationship'], 'condition' => ['query_type' => 'acf_relations']]);
        $this->add_control('acf_relationship_invert', ['label' => esc_html__('Invert direction', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'description' => esc_html__('For bidirectional relationships, retrieve all posts that are associated to a current post', 'dynamic-content-for-elementor'), 'condition' => ['query_type' => 'acf_relations']]);
        // --------------------------------- [ Custom Post Type ]
        $this->add_control('post_type', ['label' => esc_html__('Post Type', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT2, 'options' => $types, 'multiple' => \true, 'label_block' => \true, 'default' => 'post', 'condition' => ['query_type' => 'get_cpt']]);
        $this->add_control('exclude_io', ['label' => esc_html__('Exclude Current Post', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => 'yes', 'condition' => ['query_type' => ['get_cpt', 'dynamic_mode']]]);
        $this->add_control('exclude_page_parent', ['label' => esc_html__('Exclude page parent', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'condition' => ['query_type' => ['get_cpt', 'dynamic_mode']]]);
        $this->add_control('exclude_posts', ['label' => esc_html__('Exclude posts', 'dynamic-content-for-elementor'), 'type' => 'ooo_query', 'placeholder' => esc_html__('Post Title', 'dynamic-content-for-elementor'), 'label_block' => \true, 'query_type' => 'posts', 'separator' => 'before', 'multiple' => \true, 'condition' => ['query_type' => ['get_cpt', 'dynamic_mode']]]);
        $this->add_control('num_posts', ['label' => esc_html__('Number of Posts', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::NUMBER, 'default' => '10', 'separator' => 'before', 'condition' => ['query_type' => ['get_cpt', 'dynamic_mode', 'acf_relations']]]);
        $this->add_control('post_offset', ['label' => esc_html__('Posts Offset', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::NUMBER, 'default' => '0', 'condition' => ['query_type' => ['get_cpt', 'dynamic_mode'], 'num_posts!' => '-1']]);
        $this->add_control('orderby', ['label' => esc_html__('Order By', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'options' => Helper::get_post_orderby_options(), 'default' => 'date']);
        $this->add_control('acf_metakey', ['label' => esc_html__('Meta Field', 'dynamic-content-for-elementor'), 'type' => 'ooo_query', 'placeholder' => esc_html__('Meta key', 'dynamic-content-for-elementor'), 'label_block' => \true, 'query_type' => 'acfposts', 'separator' => 'after', 'condition' => ['orderby' => ['meta_value_date', 'meta_value_num', 'meta_value']]]);
        $this->add_control('order', ['label' => esc_html__('Order', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'options' => ['ASC' => 'Ascending', 'DESC' => 'Descending'], 'default' => 'DESC', 'condition' => ['orderby!' => ['random']]]);
        $this->end_controls_section();
        $this->start_controls_section('section_queryopt', ['label' => esc_html__('Parent Query', 'dynamic-content-for-elementor'), 'condition' => ['query_type' => ['get_cpt', 'dynamic_mode', 'acf_relations']]]);
        $this->add_control('page_parent', ['label' => esc_html__('Enable ParentChild Options', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'separator' => 'before']);
        $this->add_control('specific_page_parent', ['label' => esc_html__('Show children from this parent-page', 'dynamic-content-for-elementor'), 'type' => 'ooo_query', 'placeholder' => esc_html__('Page Title', 'dynamic-content-for-elementor'), 'label_block' => \true, 'query_type' => 'posts', 'separator' => 'after', 'condition' => ['page_parent' => 'yes', 'parent_source' => '', 'child_source' => '']]);
        $this->add_control('parent_source', ['label' => esc_html__('Get from Parent (for template)', 'dynamic-content-for-elementor'), 'description' => esc_html__('I take the post parent and I get my siblings out of myself.', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'label_on' => esc_html__('Same', 'dynamic-content-for-elementor'), 'label_off' => esc_html__('other', 'dynamic-content-for-elementor'), 'condition' => ['page_parent' => 'yes']]);
        $this->add_control('child_source', ['label' => esc_html__('Get from Children (for template)', 'dynamic-content-for-elementor'), 'description' => esc_html__('Compared to myself-I take my children.', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'label_on' => esc_html__('Same', 'dynamic-content-for-elementor'), 'label_off' => esc_html__('other', 'dynamic-content-for-elementor'), 'condition' => ['page_parent' => 'yes', 'parent_source' => '']]);
        $this->end_controls_section();
        $this->start_controls_section('section_querytax', ['label' => esc_html__('Taxonomy Query Filter', 'dynamic-content-for-elementor'), 'condition' => ['query_type' => ['get_cpt', 'dynamic_mode'], 'page_parent' => '']]);
        $this->add_control('taxonomy', ['label' => esc_html__('Select Taxonomy', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'options' => ['' => esc_html__('All', 'dynamic-content-for-elementor')] + $taxonomies, 'default' => '', 'description' => esc_html__('Filter results by selected taxonomy', 'dynamic-content-for-elementor'), 'label_block' => \true, 'condition' => []]);
        foreach ($taxonomies as $tkey => $atax) {
            if ($tkey) {
                $this->add_control('terms_' . $tkey, ['label' => '<b>Include</b> Terms of ' . $tkey, 'type' => 'ooo_query', 'placeholder' => esc_html__('All terms', 'dynamic-content-for-elementor'), 'label_block' => \true, 'query_type' => 'terms', 'object_type' => $tkey, 'render_type' => 'template', 'multiple' => \true, 'condition' => ['taxonomy' => $tkey, 'terms_current_post' => '']]);
            }
        }
        $this->add_control('combination_taxonomy', ['label' => esc_html__('Include Combination', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'options' => ['AND' => 'AND', 'OR' => 'OR'], 'toggle' => \false, 'default' => 'OR', 'condition' => ['taxonomy!' => '', 'terms_current_post' => '']]);
        foreach ($taxonomies as $tkey => $atax) {
            if ($tkey) {
                $this->add_control('terms_' . $tkey . '_excluse', ['label' => esc_html__('Exclude Terms of ', 'dynamic-content-for-elementor') . $tkey, 'type' => 'ooo_query', 'placeholder' => esc_html__('No terms', 'dynamic-content-for-elementor'), 'label_block' => \true, 'query_type' => 'terms', 'object_type' => $tkey, 'render_type' => 'template', 'multiple' => \true, 'condition' => ['taxonomy' => $tkey, 'terms_current_post' => '']]);
            }
        }
        $this->add_control('combination_taxonomy_excluse', ['label' => esc_html__('Exclude Combination', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'options' => ['AND' => 'AND', 'OR' => 'OR'], 'toggle' => \false, 'default' => 'OR', 'condition' => ['taxonomy!' => '', 'terms_current_post' => '']]);
        $this->add_control('terms_current_post', ['label' => esc_html__('Dynamic Current Post Terms', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'description' => esc_html__('Filter results by taxonomy terms associated to current post', 'dynamic-content-for-elementor'), 'separator' => 'before', 'condition' => ['taxonomy!' => '', 'terms_from_acf' => '']]);
        $this->add_control('terms_from_acf', ['label' => esc_html__('Use ACF Taxonomy', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'description' => esc_html__('The results of the filter for taxonomy terms indicated in the ACF Taxonomy type field. If the ACF Taxonomy value in the post is set, it will ignore the previously defined include / exclude filters.', 'dynamic-content-for-elementor'), 'separator' => 'before', 'condition' => ['terms_current_post' => '']]);
        $this->add_control('acf_taxonomy', ['label' => esc_html__('Taxonomy (ACF)', 'dynamic-content-for-elementor'), 'type' => 'ooo_query', 'placeholder' => esc_html__('Select the Taxonomy', 'dynamic-content-for-elementor'), 'label_block' => \true, 'query_type' => 'acf', 'object_type' => 'taxonomy', 'condition' => ['terms_from_acf!' => '']]);
        $this->add_control('category', ['label' => esc_html__('Terms ID', 'dynamic-content-for-elementor'), 'description' => esc_html__('Commas separate list of category ids', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HIDDEN, 'default' => '', 'label_block' => \true, 'separator' => 'before', 'condition' => ['taxonomy!' => '']]);
        $this->end_controls_section();
        $this->start_controls_section('section_querydate', ['label' => esc_html__('Date Query Filter', 'dynamic-content-for-elementor'), 'condition' => ['query_type' => ['get_cpt', 'dynamic_mode'], 'page_parent' => '']]);
        $this->add_control('querydate_mode', ['label' => esc_html__('Date Filter', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'default' => '', 'label_block' => \true, 'options' => ['' => esc_html__('No Filter', 'dynamic-content-for-elementor'), 'past' => esc_html__('Past', 'dynamic-content-for-elementor'), 'future' => esc_html__('Future', 'dynamic-content-for-elementor'), 'today' => esc_html__('Today', 'dynamic-content-for-elementor'), 'yesterday' => esc_html__('Yesterday', 'dynamic-content-for-elementor'), 'days' => esc_html__('Past Days', 'dynamic-content-for-elementor'), 'weeks' => esc_html__('Past Weeks', 'dynamic-content-for-elementor'), 'months' => esc_html__('Past Months', 'dynamic-content-for-elementor'), 'years' => esc_html__('Past Years', 'dynamic-content-for-elementor'), 'period' => esc_html__('Period', 'dynamic-content-for-elementor')]]);
        $this->add_control('querydate_field', ['label' => esc_html__('Date Field', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CHOOSE, 'label_block' => \true, 'options' => ['post_date' => ['title' => esc_html__('Publish Date', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-calendar'], 'post_meta' => ['title' => esc_html__('Post Meta', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-square']], 'default' => 'post_date', 'toggle' => \false, 'condition' => ['querydate_mode!' => ['', 'future']]]);
        $this->add_control('querydate_field_meta', ['label' => esc_html__('Meta Field', 'dynamic-content-for-elementor'), 'type' => 'ooo_query', 'placeholder' => esc_html__('Meta key or Name', 'dynamic-content-for-elementor'), 'label_block' => \true, 'query_type' => 'metas', 'object_type' => 'post', 'separator' => 'before', 'condition' => ['querydate_mode!' => ['', 'future'], 'querydate_field' => 'post_meta']]);
        $this->add_control('querydate_field_meta_format', ['label' => esc_html__('Meta Date Format', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'placeholder' => esc_html__('Y-m-d', 'dynamic-content-for-elementor'), 'label_block' => \true, 'default' => esc_html__('Ymd', 'dynamic-content-for-elementor'), 'condition' => ['querydate_mode' => 'past', 'querydate_field' => 'post_meta']]);
        $this->add_control('querydate_field_meta_future', ['label' => esc_html__('Meta Field', 'dynamic-content-for-elementor'), 'type' => 'ooo_query', 'placeholder' => esc_html__('Meta key or Name', 'dynamic-content-for-elementor'), 'label_block' => \true, 'query_type' => 'metas', 'object_type' => 'post', 'description' => esc_html__('Selected Post Meta value must be stored if format "Ymd", like ACF Date', 'dynamic-content-for-elementor'), 'separator' => 'before', 'condition' => ['querydate_mode' => 'future']]);
        $this->add_control('querydate_field_meta_future_format', ['label' => esc_html__('Meta Date Format', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'placeholder' => esc_html__('Y-m-d', 'dynamic-content-for-elementor'), 'label_block' => \true, 'default' => esc_html__('Ymd', 'dynamic-content-for-elementor'), 'condition' => ['querydate_mode' => 'future']]);
        $this->add_control('querydate_range', ['label' => esc_html__('Number of (days/months/years) elapsed', 'dynamic-content-for-elementor'), 'label_block' => \true, 'type' => Controls_Manager::NUMBER, 'default' => 1, 'condition' => ['querydate_mode' => ['days', 'weeks', 'months', 'years']]]);
        $this->add_control('querydate_date_type', ['label' => esc_html__('Date Input Mode', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CHOOSE, 'label_block' => \true, 'options' => ['' => ['title' => esc_html__('Static', 'dynamic-content-for-elementor'), 'icon' => 'eicon-pencil'], '_dynamic' => ['title' => esc_html__('Dynamic', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-cogs']], 'default' => '_dynamic', 'toggle' => \false, 'separator' => 'before', 'condition' => ['querydate_mode' => 'period']]);
        $this->add_control('querydate_date_from', ['label' => esc_html__('Date from', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::DATE_TIME, 'condition' => ['querydate_mode' => 'period', 'querydate_date_type' => '']]);
        $this->add_control('querydate_date_to', ['label' => esc_html__('Date to', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::DATE_TIME, 'condition' => ['querydate_mode' => 'period', 'querydate_date_type' => '']]);
        $this->add_control('querydate_date_from_dynamic', ['label' => esc_html__('Date from', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'condition' => ['querydate_mode' => 'period', 'querydate_date_type' => '_dynamic']]);
        $this->add_control('querydate_date_to_dynamic', ['label' => esc_html__('Date to', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'condition' => ['querydate_mode' => 'period', 'querydate_date_type' => '_dynamic']]);
        $this->end_controls_section();
        $this->start_controls_section('section_queryuser', ['label' => esc_html__('Author Query Filter', 'dynamic-content-for-elementor'), 'condition' => ['query_type' => ['get_cpt', 'dynamic_mode'], 'page_parent' => '']]);
        $this->add_control('by_users', ['label' => esc_html__('Include Author', 'dynamic-content-for-elementor'), 'type' => 'ooo_query', 'placeholder' => esc_html__('Select author', 'dynamic-content-for-elementor'), 'label_block' => \true, 'multiple' => \true, 'query_type' => 'users', 'description' => esc_html__('Filter posts by selected Authors', 'dynamic-content-for-elementor'), 'condition' => ['by_author' => '']]);
        $this->add_control('exclude_users', ['label' => esc_html__('Exclude Author', 'dynamic-content-for-elementor'), 'type' => 'ooo_query', 'placeholder' => esc_html__('No', 'dynamic-content-for-elementor'), 'label_block' => \true, 'multiple' => \true, 'query_type' => 'users', 'description' => esc_html__('Filter posts by selected Authors', 'dynamic-content-for-elementor'), 'separator' => 'after', 'condition' => ['by_author' => '']]);
        $this->add_control('byauthor_options', ['label' => esc_html__('Author (Archive)', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HEADING]);
        $this->add_control('by_author', ['label' => esc_html__('From current Author', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'description' => esc_html__('From current Post Author or Author in Archive Author page', 'dynamic-content-for-elementor')]);
        $this->end_controls_section();
        // ----------------------------------------------------- [SECTION Layout]
        $this->start_controls_section('section_layout', ['label' => esc_html__('Layout', 'dynamic-content-for-elementor'), 'tab' => Controls_Manager::TAB_CONTENT, 'condition' => ['templatemode_enable' => '', 'native_templatemode_enable' => '']]);
        $this->add_control('order_image', ['label' => esc_html__('Order image', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => '', 'frontend_available' => \true]);
        $this->add_responsive_control('image_position', ['label' => esc_html__('Image Position', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'default' => 'top', 'tablet_default' => '', 'mobile_default' => '', 'options' => ['' => esc_html__('Default', 'dynamic-content-for-elementor'), 'top' => esc_html__('Top', 'dynamic-content-for-elementor'), 'left' => esc_html__('Left', 'dynamic-content-for-elementor'), 'right' => esc_html__('Right', 'dynamic-content-for-elementor'), 'alternate' => esc_html__('Alternate', 'dynamic-content-for-elementor')], 'prefix_class' => 'image-acfposts%s-position-', 'condition' => ['posts_style!' => 'timeline', 'order_image' => '']]);
        $this->add_responsive_control('image_rate', ['label' => esc_html__('Distribution (left, right, alternate)', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => '', 'unit' => '%'], 'tablet_default' => ['size' => '', 'unit' => '%'], 'mobile_default' => ['size' => '', 'unit' => '%'], 'size_units' => ['%'], 'range' => ['%' => ['min' => 1, 'max' => 100]], 'selectors' => ['{{WRAPPER}} .dce-acfposts_image' => 'width: {{SIZE}}% !important;', '{{WRAPPER}} .dce-acfposts_content' => 'width: calc( 100% - {{SIZE}}% ) !important;'], 'condition' => ['image_position!' => '', 'order_image' => '', 'posts_style!' => 'timeline']]);
        $this->add_control('text_position', ['label' => esc_html__('Text Zone Position', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'options' => ['outside' => esc_html__('Natural', 'dynamic-content-for-elementor'), 'inside' => esc_html__('Floats in front', 'dynamic-content-for-elementor')], 'default' => 'outside', 'prefix_class' => 'text-acfposts-position-', 'condition' => ['posts_style!' => 'timeline', 'image_position' => 'top', 'order_image' => '']]);
        $this->add_responsive_control('text_space', ['label' => esc_html__('Vertical Text Movement', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['unit' => '%'], 'tablet_default' => ['unit' => '%'], 'mobile_default' => ['unit' => '%'], 'size_units' => ['%', 'px'], 'range' => ['%' => ['min' => -100, 'max' => 100], 'px' => ['min' => -200, 'max' => 200]], 'selectors' => ['{{WRAPPER}} .dce-acfposts_content' => 'top: {{SIZE}}{{UNIT}};'], 'condition' => ['image_position' => 'top', 'text_position' => 'inside', 'posts_style!' => 'timeline', 'order_image' => '']]);
        //
        $this->add_control('dysplay_items', ['label' => esc_html__('Display', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HEADING, 'separator' => 'before']);
        $this->add_control('show_title', ['label' => esc_html__('Show Title', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CHOOSE, 'label_block' => \false, 'toggle' => \false, 'options' => ['1' => ['title' => esc_html__('Yes', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-check'], '0' => ['title' => esc_html__('No', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-ban']], 'default' => '1']);
        $this->add_control('show_image', ['label' => esc_html__('Show Image', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CHOOSE, 'toggle' => \false, 'label_block' => \false, 'options' => ['1' => ['title' => esc_html__('Yes', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-check'], '0' => ['title' => esc_html__('No', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-ban']], 'default' => '1']);
        $this->add_control('show_textcontent', ['label' => esc_html__('Show Content', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CHOOSE, 'toggle' => \false, 'label_block' => \false, 'options' => ['1' => ['title' => esc_html__('Yes', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-check'], '0' => ['title' => esc_html__('No', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-ban'], '2' => ['title' => esc_html__('Excerpt', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-left']], 'default' => '0']);
        $this->add_control('show_type', ['label' => esc_html__('Show Type', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CHOOSE, 'toggle' => \false, 'label_block' => \false, 'options' => ['1' => ['title' => esc_html__('Yes', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-check'], '0' => ['title' => esc_html__('No', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-ban']], 'default' => '0']);
        $this->add_control('show_metadata', ['label' => esc_html__('Show Meta Data', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CHOOSE, 'toggle' => \false, 'label_block' => \false, 'options' => ['1' => ['title' => esc_html__('Yes', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-check'], '0' => ['title' => esc_html__('No', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-ban']], 'default' => '0']);
        $this->add_control('show_author', ['label' => esc_html__('Show Author', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CHOOSE, 'toggle' => \false, 'label_block' => \false, 'options' => ['1' => ['title' => esc_html__('Yes', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-check'], '0' => ['title' => esc_html__('No', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-ban']], 'default' => '0']);
        $this->add_control('show_date', ['label' => esc_html__('Show Date', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CHOOSE, 'toggle' => \false, 'label_block' => \false, 'options' => ['1' => ['title' => esc_html__('Yes', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-check'], '0' => ['title' => esc_html__('No', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-ban']], 'default' => '0']);
        $this->add_control('show_readmore', ['label' => esc_html__('Show Read More', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CHOOSE, 'toggle' => \false, 'label_block' => \false, 'options' => ['1' => ['title' => esc_html__('Yes', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-check'], '0' => ['title' => esc_html__('No', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-ban']], 'default' => '0']);
        $this->add_control('show_acfitems', ['label' => esc_html__('Show ACF Items', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CHOOSE, 'toggle' => \false, 'label_block' => \false, 'options' => ['1' => ['title' => esc_html__('Yes', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-check'], '0' => ['title' => esc_html__('No', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-ban']], 'default' => '0']);
        $repeater = new Repeater();
        $chid = $repeater->get_name();
        $repeater->add_control('list_name', ['label' => esc_html__('Name', 'dynamic-content-for-elementor'), 'type' => \Elementor\Controls_Manager::HIDDEN, 'default' => esc_html__('List Name', 'dynamic-content-for-elementor'), 'label_block' => \true]);
        $this->add_control('list_layout_posts', ['label' => esc_html__('Ordering', 'dynamic-content-for-elementor'), 'separator' => 'before', 'type' => Controls_Manager::REPEATER, 'fields' => $repeater->get_controls(), 'item_actions' => ['add' => \false, 'duplicate' => \false, 'remove' => \false, 'sort' => \true], 'default' => [['list_name' => esc_html__('Image', 'dynamic-content-for-elementor'), 'list_html_content' => esc_html__('Image', 'dynamic-content-for-elementor'), '_id' => 'sortimg'], ['list_name' => esc_html__('Date', 'dynamic-content-for-elementor'), 'list_html_content' => esc_html__('Date', 'dynamic-content-for-elementor'), '_id' => 'sortdate'], ['list_name' => esc_html__('Title', 'dynamic-content-for-elementor'), '_id' => 'sorttit'], ['list_name' => esc_html__('Meta Data', 'dynamic-content-for-elementor'), 'list_html_content' => esc_html__('Meta data', 'dynamic-content-for-elementor'), '_id' => 'sortdata'], ['list_name' => esc_html__('Content', 'dynamic-content-for-elementor'), 'list_html_content' => esc_html__('Content', 'dynamic-content-for-elementor'), '_id' => 'sortcont'], ['list_name' => esc_html__('Author', 'dynamic-content-for-elementor'), 'list_html_content' => esc_html__('Auhtor', 'dynamic-content-for-elementor'), '_id' => 'sortauth'], ['list_name' => esc_html__('Type', 'dynamic-content-for-elementor'), 'list_html_content' => esc_html__('ACF Items', 'dynamic-content-for-elementor'), '_id' => 'sorttype'], ['list_name' => esc_html__('ACF items', 'dynamic-content-for-elementor'), 'list_html_content' => esc_html__('ACF Items', 'dynamic-content-for-elementor'), '_id' => 'sortacf'], ['list_name' => esc_html__('Read More', 'dynamic-content-for-elementor'), 'list_html_content' => esc_html__('Read More', 'dynamic-content-for-elementor'), '_id' => 'sortrem']], 'title_field' => '{{{ list_name }}}']);
        $this->end_controls_section();
        // ------------------------------------------------------ [SECTION Style of List]
        $this->start_controls_section('section_render', ['label' => esc_html__('Render', 'dynamic-content-for-elementor'), 'tab' => Controls_Manager::TAB_CONTENT]);
        $this->add_control('posts_style', ['label' => esc_html__('Render as ', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'default' => 'flexgrid', 'options' => ['simplegrid' => esc_html__('Row', 'dynamic-content-for-elementor'), 'flexgrid' => esc_html__('Grid (Flex)', 'dynamic-content-for-elementor'), 'grid' => esc_html__('Grid Masonry (filters)', 'dynamic-content-for-elementor'), 'carousel' => esc_html__('Slick (carousel)', 'dynamic-content-for-elementor'), 'swiper' => esc_html__('Swiper (carousel)', 'dynamic-content-for-elementor'), 'timeline' => esc_html__('Timeline', 'dynamic-content-for-elementor')]]);
        $this->add_control('filters_enable', ['label' => esc_html__('Show Filters', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'frontend_available' => \true, 'condition' => ['posts_style' => 'grid']]);
        $this->add_control('pagination_enable', ['label' => esc_html__('Show Pagination', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'condition' => ['posts_style' => ['simplegrid', 'flexgrid', 'grid'], 'infiniteScroll_enable' => '']]);
        $this->add_control('infiniteScroll_enable', ['label' => esc_html__('Infinite Scroll', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'frontend_available' => \true, 'condition' => ['posts_style' => ['simplegrid', 'flexgrid', 'grid'], 'pagination_enable' => '']]);
        $this->add_control('unic_date', ['label' => esc_html__('Use the years above the block', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'separator' => 'before', 'condition' => ['posts_style' => ['flexgrid', 'grid']]]);
        $this->add_control('masking_enable', ['label' => esc_html__('Remove Masking', 'dynamic-content-for-elementor'), 'description' => esc_html__('Remove the mask on the carousel to allow the display of the elements outside', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'separator' => 'before', 'prefix_class' => 'no-masking-', 'frontend_available' => \true, 'default' => '', 'condition' => ['posts_style' => ['swiper', 'carousel']]]);
        $this->end_controls_section();
        /////////////////////////////////////////////////////////////////// [ SECTION InfiniteScroll ]
        $this->start_controls_section('section_infinitescroll', ['label' => esc_html__('Infinite Scroll', 'dynamic-content-for-elementor'), 'tab' => Controls_Manager::TAB_CONTENT, 'condition' => ['infiniteScroll_enable' => 'yes']]);
        $this->add_control('infiniteScroll_trigger', ['label' => esc_html__('Trigger', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'default' => 'scroll', 'frontend_available' => \true, 'options' => ['scroll' => esc_html__('On Scroll Page', 'dynamic-content-for-elementor'), 'button' => esc_html__('On Click Button', 'dynamic-content-for-elementor')]]);
        $this->add_control('infiniteScroll_label_button', ['label' => esc_html__('Label Button', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'default' => esc_html__('View more', 'dynamic-content-for-elementor'), 'condition' => ['infiniteScroll_trigger' => 'button']]);
        $this->add_control('infiniteScroll_enable_status', ['label' => esc_html__('Enable Status', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => 'yes', 'separator' => 'before']);
        $this->add_control('infiniteScroll_show_preview', ['label' => esc_html__('Show Status PREVIEW in Editor Mode', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'separator' => 'before', 'condition' => ['infiniteScroll_enable_status' => 'yes']]);
        $this->add_control('infiniteScroll_loading_type', ['label' => esc_html__('Loading Type', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CHOOSE, 'toggle' => \false, 'options' => ['ellips' => ['title' => esc_html__('Ellips', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-ellipsis-h'], 'text' => ['title' => esc_html__('Label Text', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-font']], 'default' => 'ellips', 'separator' => 'before', 'condition' => ['infiniteScroll_enable_status' => 'yes']]);
        $this->add_control('infiniteScroll_label_loading', ['label' => esc_html__('Label Loading', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'default' => esc_html__('Loading...', 'dynamic-content-for-elementor'), 'condition' => ['infiniteScroll_enable_status' => 'yes', 'infiniteScroll_loading_type' => 'text']]);
        $this->add_control('infiniteScroll_label_last', ['label' => esc_html__('Label Last', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'default' => esc_html__('End of content', 'dynamic-content-for-elementor'), 'condition' => ['infiniteScroll_enable_status' => 'yes']]);
        $this->add_control('infiniteScroll_label_error', ['label' => esc_html__('Label Error', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'default' => esc_html__('No more articles to load', 'dynamic-content-for-elementor'), 'condition' => ['infiniteScroll_enable_status' => 'yes']]);
        $this->add_control('infiniteScroll_enable_history', ['label' => esc_html__('Enable History', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'separator' => 'before', 'frontend_available' => \true]);
        $this->end_controls_section();
        /////////////////////////////////////////////////////////////////// [ SECTION Pagination ]
        $this->start_controls_section('section_pagination', ['label' => esc_html__('Pagination', 'dynamic-content-for-elementor'), 'tab' => Controls_Manager::TAB_CONTENT, 'condition' => ['pagination_enable' => 'yes']]);
        $this->add_control('pagination_show_numbers', ['label' => esc_html__('Show Numbers', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => 'yes']);
        $this->add_control('pagination_range', ['label' => esc_html__('Range of numbers', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::NUMBER, 'default' => 4, 'condition' => ['pagination_show_numbers' => 'yes']]);
        $this->add_control('pagination_show_prevnext', ['label' => esc_html__('Show Prev/Next', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => 'yes', 'separator' => 'before']);
        $this->add_control('selected_pagination_icon_prevnext', ['label' => esc_html__('Icon Prev/Next', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::ICONS, 'fa4compatibility' => 'pagination_icon_prevnext', 'default' => ['value' => 'fas fa-long-arrow-alt-right', 'library' => 'fa-solid'], 'recommended' => ['fa-solid' => ['arrow-right', 'angle-right', 'long-arrow-alt-right', 'arrow-alt-circle-right', 'arrow-circle-right', 'caret-right', 'caret-square-right', 'chevron-circle-right', 'chevron-right', 'hand-point-right']], 'condition' => ['pagination_show_prevnext' => 'yes']]);
        $this->add_control('pagination_prev_label', ['label' => esc_html__('Previous Label', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'default' => esc_html__('Previous', 'dynamic-content-for-elementor'), 'condition' => ['pagination_show_prevnext' => 'yes']]);
        $this->add_control('pagination_next_label', ['label' => esc_html__('Next Label', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'default' => esc_html__('Next', 'dynamic-content-for-elementor'), 'condition' => ['pagination_show_prevnext' => 'yes']]);
        $this->add_control('pagination_show_firstlast', ['label' => esc_html__('Show First/Last', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => 'yes', 'separator' => 'before']);
        $this->add_control('selected_pagination_icon_firstlast', ['label' => esc_html__('Icon First/Last', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::ICONS, 'fa4compatibility' => 'pagination_icon_firstlast', 'default' => ['value' => 'fas fa-long-arrow-alt-right', 'library' => 'fa-solid'], 'recommended' => ['fa-solid' => ['arrow-right', 'angle-right', 'long-arrow-alt-right', 'arrow-alt-circle-right', 'arrow-circle-right', 'caret-right', 'caret-square-right', 'chevron-circle-right', 'chevron-right', 'hand-point-right']], 'condition' => ['pagination_show_firstlast' => 'yes']]);
        $this->add_control('pagination_first_label', ['label' => esc_html__('Previous Label', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'default' => esc_html__('First', 'dynamic-content-for-elementor'), 'condition' => ['pagination_show_firstlast' => 'yes']]);
        $this->add_control('pagination_last_label', ['label' => esc_html__('Next Label', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'default' => esc_html__('Last', 'dynamic-content-for-elementor'), 'condition' => ['pagination_show_firstlast' => 'yes']]);
        $this->add_control('pagination_show_progression', ['label' => esc_html__('Show Progression', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => 'yes', 'separator' => 'before']);
        $this->end_controls_section();
        /////////////////////////////////////////////////////////////////// [ SECTION Carosello ]
        $this->start_controls_section('section_carousel', ['label' => esc_html__('Carousel', 'dynamic-content-for-elementor'), 'tab' => Controls_Manager::TAB_CONTENT, 'condition' => ['posts_style' => ['carousel', 'dualslider']]]);
        $slides_to_show = \range(1, 10);
        $slides_to_show = \array_combine($slides_to_show, $slides_to_show);
        $this->add_responsive_control('slides_to_show', ['label' => esc_html__('Slides to Show', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'options' => $slides_to_show, 'default' => '4', 'tablet_default' => '2', 'mobile_default' => '1', 'frontend_available' => \true]);
        $slides_to_scroll = \range(1, 10);
        $slides_to_scroll = \array_combine($slides_to_scroll, $slides_to_scroll);
        $this->add_responsive_control('slides_to_scroll', ['label' => esc_html__('Slides to Scroll', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'default' => '1', 'tablet_default' => '1', 'mobile_default' => '1', 'options' => $slides_to_scroll, 'frontend_available' => \true]);
        $this->add_responsive_control('carousel_arrow_enable', ['label' => esc_html__('Arrows', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => 'yes', 'frontend_available' => \true]);
        $this->add_control('carousel_arrow_style', ['label' => esc_html__('Arrow Style', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'default' => 'inside', 'options' => ['inside' => esc_html__('Inside', 'dynamic-content-for-elementor'), 'outside' => esc_html__('Outside', 'dynamic-content-for-elementor')], 'condition' => ['carousel_arrow_enable' => 'yes'], 'prefix_class' => 'arrows-acfposts-position-']);
        $this->add_responsive_control('carousel_dots_enable', ['label' => esc_html__('Dots', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'frontend_available' => \true]);
        $this->add_control('carousel_infinite_enable', ['label' => esc_html__('Loop', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'frontend_available' => \true]);
        $this->add_control('carousel_speed', ['label' => esc_html__('Animation Speed', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::NUMBER, 'default' => 500, 'frontend_available' => \true]);
        $this->add_control('carousel_autoplay_enable', ['label' => esc_html__('Autoplay', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'frontend_available' => \true]);
        $this->add_control('carousel_autoplayspeed', ['label' => esc_html__('Autoplay Delay (ms)', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::NUMBER, 'default' => 5000, 'frontend_available' => \true, 'condition' => ['carousel_autoplay_enable!' => '']]);
        $this->add_control('carousel_center_enable', ['label' => esc_html__('Center Mode', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'frontend_available' => \true]);
        $this->add_control('carousel_effect', ['label' => esc_html__('Effect', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'default' => 'slide', 'options' => ['slide' => esc_html__('Slide', 'dynamic-content-for-elementor'), 'fade' => esc_html__('Fade', 'dynamic-content-for-elementor')], 'condition' => ['slides_to_show' => '1'], 'frontend_available' => \true]);
        $this->end_controls_section();
        ///////////////////////////////////////////////////////////// [ SECTION Swiper ]
        // ------------------------------------------------------------------------------- Base Settings, Slides grid, Grab Cursor
        $this->start_controls_section('section_swiper_settings', ['label' => esc_html__('Swiper Settings', 'dynamic-content-for-elementor'), 'condition' => ['posts_style' => 'swiper']]);
        $this->add_control('direction_slider', ['label' => esc_html__('Direction', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HIDDEN, 'options' => ['horizontal' => esc_html__('Horizontal', 'dynamic-content-for-elementor'), 'vertical' => esc_html__('Vertical', 'dynamic-content-for-elementor')], 'default' => 'horizontal', 'frontend_available' => \true]);
        $this->add_control('speed_slider', ['label' => esc_html__('Speed', 'dynamic-content-for-elementor'), 'description' => esc_html__('Duration of transition between slides (in ms)', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::NUMBER, 'default' => 300, 'min' => 0, 'max' => 3000, 'step' => 10, 'frontend_available' => \true]);
        $this->add_control('effects', ['label' => esc_html__('Effect of transition', 'dynamic-content-for-elementor'), 'description' => esc_html__('Transition effect between slides', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'options' => ['slide' => esc_html__('Slide', 'dynamic-content-for-elementor'), 'fade' => esc_html__('Fade', 'dynamic-content-for-elementor'), 'cube' => esc_html__('Cube', 'dynamic-content-for-elementor'), 'coverflow' => esc_html__('Coverflow', 'dynamic-content-for-elementor'), 'flip' => esc_html__('Flip', 'dynamic-content-for-elementor')], 'default' => 'slide', 'frontend_available' => \true]);
        $this->add_control('centeredSlides', ['label' => esc_html__('Centered Slides', 'dynamic-content-for-elementor'), 'description' => esc_html__('If true, then active slide will be centered, not always on the left side.', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'frontend_available' => \true]);
        // -------------------------------- Progressione ------
        $this->add_control('slideperview_options', ['label' => esc_html__('Slide per view', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HEADING, 'separator' => 'before']);
        $this->add_responsive_control('spaceBetween', ['label' => esc_html__('Space Between', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::NUMBER, 'default' => 0, 'tablet_default' => '', 'mobile_default' => '', 'min' => 0, 'max' => 100, 'step' => 1, 'frontend_available' => \true]);
        $this->add_responsive_control('slidesPerView', ['label' => esc_html__('Slides Per View', 'dynamic-content-for-elementor'), 'description' => esc_html__('Number of slides per view (slides visible at the same time on sliders container). If you use it with "auto" value and along with loop: true then you need to specify loopedSlides parameter with amount of slides to loop (duplicate). SlidesPerView: "auto"\'" is currently not compatible with multirow mode, when slidesPerColumn greater than 1', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::NUMBER, 'default' => '1', 'min' => 1, 'max' => 12, 'step' => 1, 'frontend_available' => \true]);
        $this->add_responsive_control('slidesColumn', ['label' => esc_html__('Slides Column', 'dynamic-content-for-elementor'), 'description' => esc_html__('Number of slides per column, for multirow layout.', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::NUMBER, 'default' => '1', 'min' => 1, 'max' => 4, 'step' => 1, 'frontend_available' => \true]);
        $this->add_responsive_control('slidesPerGroup', ['label' => esc_html__('Slides Per Group', 'dynamic-content-for-elementor'), 'description' => esc_html__('Set numbers of slides to define and enable group sliding. Useful to use with slidesPerView > 1', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::NUMBER, 'default' => 1, 'tablet_default' => '', 'mobile_default' => '', 'min' => 1, 'max' => 12, 'step' => 1, 'frontend_available' => \true]);
        // -------------------------------- Free Mode ------
        $this->add_control('freemode_options', ['label' => esc_html__('Free Mode', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HEADING, 'separator' => 'before']);
        $this->add_control('freeMode', ['label' => esc_html__('Free Mode', 'dynamic-content-for-elementor'), 'description' => esc_html__('If true then slides will not have fixed positions', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'frontend_available' => \true]);
        $this->add_control('freeModeMomentum', ['label' => esc_html__('Free Mode Momentum', 'dynamic-content-for-elementor'), 'description' => esc_html__('If true, then slide will keep moving for a while after you release it', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'frontend_available' => \true, 'condition' => ['freeMode' => 'yes']]);
        $this->add_control('freeModeMomentumRatio', ['label' => esc_html__('Free Mode Momentum Ratio', 'dynamic-content-for-elementor'), 'description' => esc_html__('Higher value produces larger momentum distance after you release slider', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::NUMBER, 'default' => 1, 'min' => 0, 'max' => 10, 'step' => 0.1, 'frontend_available' => \true, 'condition' => ['freeMode' => 'yes', 'freeModeMomentum' => 'yes']]);
        $this->add_control('freeModeMomentumVelocityRatio', ['label' => esc_html__('Free Mode Momentum Velocity Ratio', 'dynamic-content-for-elementor'), 'description' => esc_html__('Higher value produces larger momentum speed after you release slider', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::NUMBER, 'default' => 1, 'min' => 0, 'max' => 10, 'step' => 0.1, 'frontend_available' => \true, 'condition' => ['freeMode' => 'yes', 'freeModeMomentum' => 'yes']]);
        $this->add_control('freeModeMomentumBounce', ['label' => esc_html__('Free Mode Momentum Bounce', 'dynamic-content-for-elementor'), 'description' => esc_html__('Set to false if you want to disable momentum bounce in free mode', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => 'yes', 'frontend_available' => \true, 'condition' => ['freeMode' => 'yes']]);
        $this->add_control('freeModeMomentumBounceRatio', ['label' => esc_html__('Free Mode Momentum Bounce Ratio', 'dynamic-content-for-elementor'), 'description' => esc_html__('Higher value produces larger momentum bounce effect', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::NUMBER, 'default' => 1, 'min' => 0, 'max' => 10, 'step' => 0.1, 'frontend_available' => \true, 'condition' => ['freeMode' => 'yes', 'freeModeMomentumBounce' => 'yes']]);
        $this->add_control('freeModeMinimumVelocity', ['label' => esc_html__('Free Mode Momentum Velocity Ratio', 'dynamic-content-for-elementor'), 'description' => esc_html__('Minimum touchmove-velocity required to trigger free mode momentum', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::NUMBER, 'default' => 0.02, 'min' => 0, 'max' => 1, 'step' => 0.01, 'frontend_available' => \true, 'condition' => ['freeMode' => 'yes']]);
        $this->add_control('freeModeSticky', ['label' => esc_html__('Free Mode Sticky', 'dynamic-content-for-elementor'), 'description' => esc_html__('Set \'yes\' to enable snap to slides positioned in free mode', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'frontend_available' => \true, 'condition' => ['freeMode' => 'yes']]);
        // -------------------------------- Navigation options ------
        $this->add_control('navigation_options', ['label' => esc_html__('Navigation options', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HEADING, 'separator' => 'before']);
        $this->add_control('useNavigation', ['label' => esc_html__('Use Navigation', 'dynamic-content-for-elementor'), 'description' => esc_html__('Set "yes", you will use the navigation arrows.', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => 'yes', 'frontend_available' => \true]);
        // ------------------------------------------------- Navigations Arrow Options
        $this->add_control('navigation_arrow_color', ['label' => esc_html__('Arrows color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'default' => '', 'selectors' => ['{{WRAPPER}} .swiper-button-next path, {{WRAPPER}} .swiper-button-prev path, ' => 'fill: {{VALUE}};', '{{WRAPPER}} .swiper-button-next line, {{WRAPPER}} .swiper-button-prev line, {{WRAPPER}} .swiper-button-next polyline, {{WRAPPER}} .swiper-button-prev polyline' => 'stroke: {{VALUE}};'], 'condition' => ['useNavigation' => 'yes']]);
        $this->add_control('useNavigation_animationHover', ['label' => esc_html__('Use animation in rollover', 'dynamic-content-for-elementor'), 'description' => esc_html__('A short animation will take place at the rollover.', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => 'yes', 'prefix_class' => 'hoveranim-', 'separator' => 'before', 'condition' => ['useNavigation' => 'yes']]);
        $this->add_control('navigation_arrow_color_hover', ['label' => esc_html__('Hover color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'default' => '', 'selectors' => ['{{WRAPPER}} .swiper-button-next:hover path, {{WRAPPER}} .swiper-button-prev:hover path, ' => 'fill: {{VALUE}};', '{{WRAPPER}} .swiper-button-next:hover line, {{WRAPPER}} .swiper-button-prev:hover line, {{WRAPPER}} .swiper-button-next:hover polyline, {{WRAPPER}} .swiper-button-prev:hover polyline' => 'stroke: {{VALUE}};'], 'condition' => ['useNavigation' => 'yes']]);
        $this->add_responsive_control('pagination_stroke_1', ['label' => esc_html__('Stroke Arrow', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => ''], 'tablet_default' => ['size' => ''], 'mobile_default' => ['size' => ''], 'range' => ['px' => ['max' => 50, 'min' => 0, 'step' => 1.0]], 'selectors' => ['{{WRAPPER}} .swiper-button-prev polyline, {{WRAPPER}} .swiper-button-next polyline' => 'stroke-width: {{SIZE}};'], 'condition' => ['useNavigation' => 'yes']]);
        $this->add_responsive_control('pagination_stroke_2', ['label' => esc_html__('Stroke Line', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => ''], 'tablet_default' => ['size' => ''], 'mobile_default' => ['size' => ''], 'range' => ['px' => ['max' => 50, 'min' => 0, 'step' => 1.0]], 'selectors' => ['{{WRAPPER}} .swiper-button-next line, {{WRAPPER}} .swiper-button-prev line' => 'stroke-width: {{SIZE}};'], 'condition' => ['useNavigation' => 'yes']]);
        $this->add_control('pagination_tratteggio', ['label' => esc_html__('Dashed', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => '0'], 'range' => ['px' => ['max' => 50, 'min' => 0, 'step' => 1.0]], 'selectors' => ['{{WRAPPER}} .swiper-button-prev line, {{WRAPPER}} .swiper-button-next line, {{WRAPPER}} .swiper-button-prev polyline, {{WRAPPER}} .swiper-button-next polyline' => 'stroke-dasharray: {{SIZE}},{{SIZE}};'], 'condition' => ['useNavigation' => 'yes']]);
        $this->add_responsive_control('pagination_scale', ['label' => esc_html__('Scale', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => ''], 'tablet_default' => ['size' => ''], 'mobile_default' => ['size' => ''], 'range' => ['px' => ['max' => 2, 'min' => 0.1, 'step' => 0.01]], 'selectors' => ['{{WRAPPER}} .swiper-button-next, {{WRAPPER}} .swiper-button-prev' => 'transform: scale({{SIZE}});'], 'condition' => ['useNavigation' => 'yes']]);
        $this->add_responsive_control('pagination_position', ['label' => esc_html__('Horizontal Position', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => ''], 'tablet_default' => ['size' => ''], 'mobile_default' => ['size' => ''], 'size_units' => ['px'], 'range' => ['px' => ['max' => 100, 'min' => -100, 'step' => 1]], 'selectors' => ['{{WRAPPER}} .swiper-button-prev' => 'left: {{SIZE}}{{UNIT}};', '{{WRAPPER}} .swiper-button-next' => 'right: {{SIZE}}{{UNIT}};'], 'condition' => ['useNavigation' => 'yes']]);
        $this->add_responsive_control('pagination_position_v', ['label' => esc_html__('Vertical Position', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => 50, 'unit' => '%'], 'size_units' => ['%', 'px'], 'range' => ['%' => ['max' => 120, 'min' => -20, 'step' => 1], 'px' => ['max' => 200, 'min' => -200, 'step' => 1]], 'selectors' => ['{{WRAPPER}} .swiper-button-prev, {{WRAPPER}} .swiper-button-next' => 'top: {{SIZE}}{{UNIT}};'], 'condition' => ['useNavigation' => 'yes']]);
        // --------------------------------------------------- Pagination options ------
        $this->add_control('pagination_options', ['label' => esc_html__('Pagination options', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HEADING, 'separator' => 'before']);
        $this->add_control('usePagination', ['label' => esc_html__('Pagination', 'dynamic-content-for-elementor'), 'description' => esc_html__('Use the slide progression display system ("bullets", "fraction", "progress").', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => 'yes', 'frontend_available' => \true]);
        $this->add_control('pagination_type', ['label' => esc_html__('Pagination Type', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'options' => ['bullets' => esc_html__('Bullets', 'dynamic-content-for-elementor'), 'fraction' => esc_html__('Fraction', 'dynamic-content-for-elementor'), 'progress' => esc_html__('Progress', 'dynamic-content-for-elementor')], 'default' => 'bullets', 'frontend_available' => \true, 'condition' => ['usePagination' => 'yes']]);
        // ------------------------------------------------- Pagination Fraction Options
        $this->add_control('fraction_separator', ['label' => esc_html__('Fraction text separator', 'dynamic-content-for-elementor'), 'description' => esc_html__('The text separating the 2 numbers', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'frontend_available' => \true, 'default' => '/', 'condition' => ['usePagination' => 'yes', 'pagination_type' => 'fraction']]);
        $this->add_responsive_control('fraction_space', ['label' => esc_html__('Spacing', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => '4', 'unit' => 'px'], 'tablet_default' => ['unit' => 'px'], 'mobile_default' => ['unit' => 'px'], 'size_units' => ['px'], 'range' => ['px' => ['min' => -20, 'max' => 100]], 'selectors' => ['{{WRAPPER}} .swiper-pagination-fraction .separator' => 'margin: 0 {{SIZE}}{{UNIT}};'], 'condition' => ['usePagination' => 'yes', 'pagination_type' => 'fraction']]);
        $this->add_control('fraction_color', ['label' => esc_html__('Numbers color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'default' => '', 'selectors' => ['{{WRAPPER}} .swiper-pagination-fraction > *' => 'color: {{VALUE}};'], 'condition' => ['usePagination' => 'yes', 'pagination_type' => 'fraction']]);
        $this->add_group_control(Group_Control_Typography::get_type(), ['name' => 'fraction_typography', 'label' => esc_html__('Numbers Typography', 'dynamic-content-for-elementor'), 'selector' => '{{WRAPPER}} .swiper-pagination-fraction > *', 'condition' => ['usePagination' => 'yes', 'pagination_type' => 'fraction']]);
        $this->add_control('fraction_current_color', ['label' => esc_html__('Color of the current number', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'default' => '', 'selectors' => ['{{WRAPPER}} .swiper-pagination-fraction .swiper-pagination-current' => 'color: {{VALUE}};'], 'condition' => ['usePagination' => 'yes', 'pagination_type' => 'fraction']]);
        $this->add_group_control(Group_Control_Typography::get_type(), ['name' => 'fraction_typography_current', 'label' => esc_html__('Current number typography', 'dynamic-content-for-elementor'), 'default' => '', 'selector' => '{{WRAPPER}} .swiper-pagination-fraction .swiper-pagination-current', 'condition' => ['usePagination' => 'yes', 'pagination_type' => 'fraction']]);
        $this->add_control('fraction_separator_color', ['label' => esc_html__('The color of the separator', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'default' => '', 'selectors' => ['{{WRAPPER}} .swiper-pagination-fraction .separator' => 'color: {{VALUE}};'], 'condition' => ['usePagination' => 'yes', 'pagination_type' => 'fraction']]);
        $this->add_group_control(Group_Control_Typography::get_type(), ['name' => esc_html__('fraction_typography_separator', 'dynamic-content-for-elementor'), 'label' => 'Typography separator', 'default' => '', 'selector' => '{{WRAPPER}} .swiper-pagination-fraction .separator', 'condition' => ['usePagination' => 'yes', 'pagination_type' => 'fraction']]);
        // ------------------------------------------------- Pagination Bullets Options
        $this->add_responsive_control('bullets_space', ['label' => esc_html__('Space', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => '5', 'unit' => 'px'], 'tablet_default' => ['unit' => 'px'], 'mobile_default' => ['unit' => 'px'], 'size_units' => ['px'], 'range' => ['px' => ['min' => 0, 'max' => 100]], 'selectors' => ['{{WRAPPER}} .swiper-pagination-bullets .swiper-pagination-bullet' => 'margin: 0 {{SIZE}}{{UNIT}};'], 'condition' => ['usePagination' => 'yes', 'pagination_type' => 'bullets']]);
        $this->add_responsive_control('pagination_bullets', ['label' => esc_html__('Bullets dimension', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => '8', 'unit' => 'px'], 'tablet_default' => ['unit' => 'px'], 'mobile_default' => ['unit' => 'px'], 'size_units' => ['px'], 'range' => ['px' => ['min' => 0, 'max' => 100]], 'selectors' => ['{{WRAPPER}} .swiper-pagination-bullets .swiper-pagination-bullet' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};'], 'condition' => ['usePagination' => 'yes', 'pagination_type' => 'bullets']]);
        $this->add_responsive_control('pagination_bullets_posy', ['label' => esc_html__('Shift', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => '10', 'unit' => 'px'], 'tablet_default' => ['unit' => 'px'], 'mobile_default' => ['unit' => 'px'], 'size_units' => ['px'], 'range' => ['px' => ['min' => -160, 'max' => 160]], 'selectors' => ['{{WRAPPER}} .swiper-pagination-bullets' => ' bottom: {{SIZE}}{{UNIT}};'], 'condition' => ['usePagination' => 'yes', 'pagination_type' => 'bullets']]);
        $this->add_control('bullets_color', ['label' => esc_html__('Bullets Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'default' => '', 'selectors' => ['{{WRAPPER}} .swiper-pagination-bullets .swiper-pagination-bullet' => 'background-color: {{VALUE}};'], 'condition' => ['usePagination' => 'yes', 'pagination_type' => 'bullets']]);
        $this->add_group_control(Group_Control_Border::get_type(), ['name' => 'border_bullet', 'label' => esc_html__('Dullets border', 'dynamic-content-for-elementor'), 'selector' => '{{WRAPPER}} .swiper-pagination-bullets .swiper-pagination-bullet', 'condition' => ['usePagination' => 'yes', 'pagination_type' => 'bullets']]);
        $this->add_responsive_control('current_bullet', ['label' => esc_html__('Dimension of active bullet', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => '', 'unit' => 'px'], 'tablet_default' => ['unit' => 'px'], 'mobile_default' => ['unit' => 'px'], 'size_units' => ['px'], 'range' => ['px' => ['min' => 0, 'max' => 100]], 'selectors' => ['{{WRAPPER}} .swiper-pagination-bullets .swiper-pagination-bullet-active' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};', '{{WRAPPER}} .swiper-pagination.swiper-pagination-bullets' => 'height: {{SIZE}}{{UNIT}}'], 'condition' => ['usePagination' => 'yes', 'pagination_type' => 'bullets']]);
        $this->add_control('current_bullet_color', ['label' => esc_html__('Active bullet color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'default' => '', 'selectors' => ['{{WRAPPER}} .swiper-pagination-bullets .swiper-pagination-bullet-active' => 'background-color: {{VALUE}};'], 'condition' => ['usePagination' => 'yes', 'pagination_type' => 'bullets']]);
        $this->add_group_control(Group_Control_Border::get_type(), ['name' => 'border_current_bullet', 'label' => esc_html__('Active bullet border', 'dynamic-content-for-elementor'), 'selector' => '{{WRAPPER}} .swiper-pagination-bullets .swiper-pagination-bullet-active', 'condition' => ['usePagination' => 'yes', 'pagination_type' => 'bullets']]);
        $this->add_control('progress_color', ['label' => esc_html__('Progress color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'default' => '', 'selectors' => ['{{WRAPPER}} .swiper-pagination-progress' => 'background-color: {{VALUE}};'], 'condition' => ['usePagination' => 'yes', 'pagination_type' => 'progress']]);
        $this->add_control('progressbar_color', ['label' => esc_html__('Progressbar color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'default' => '', 'selectors' => ['{{WRAPPER}} .swiper-pagination-progress .swiper-pagination-progressbar' => 'background-color: {{VALUE}};'], 'condition' => ['usePagination' => 'yes', 'pagination_type' => 'progress']]);
        // -------------------------------- Scrollbar options ------
        $this->add_control('scrollbar_options', ['label' => esc_html__('Scrollbar options', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HEADING, 'separator' => 'before']);
        $this->add_control('useScrollbar', ['label' => esc_html__('Scrollbar', 'dynamic-content-for-elementor'), 'description' => esc_html__('You will use a scrollbar that displays navigation', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => '']);
        // -------------------------------- Autoplay ------
        $this->add_control('autoplay_options', ['label' => esc_html__('Autoplay options', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HEADING, 'separator' => 'before']);
        $this->add_control('useAutoplay', ['label' => esc_html__('Use Autoplay', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'frontend_available' => \true]);
        $this->add_control('autoplay', ['label' => esc_html__('Autoplay Delay (ms)', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::NUMBER, 'default' => '', 'min' => 0, 'max' => 15000, 'step' => 100, 'frontend_available' => \true, 'condition' => ['useAutoplay' => 'yes']]);
        $this->add_control('autoplayStopOnLast', ['label' => esc_html__('Autoplay stop on last slide', 'dynamic-content-for-elementor'), 'description' => esc_html__('Enable this parameter and autoplay will be stopped when it reaches the last slide (has no effect in loop mode)', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'frontend_available' => \true, 'condition' => ['useAutoplay' => 'yes']]);
        $this->add_control('autoplayDisableOnInteraction', ['label' => esc_html__('Autoplay Disable on interaction', 'dynamic-content-for-elementor'), 'description' => esc_html__('Set to "false" and autoplay will not be disabled after user interactions (swipes), it will be restarted every time after interaction', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => 'yes', 'frontend_available' => \true, 'condition' => ['useAutoplay' => 'yes']]);
        // -------------------------------- Keyboard ------
        $this->add_control('keyboard_options', ['label' => esc_html__('Keyboard / Mousewheel', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HEADING, 'separator' => 'before']);
        $this->add_control('keyboardControl', ['label' => esc_html__('Keyboard Control', 'dynamic-content-for-elementor'), 'description' => esc_html__('Set to true to enable keyboard control', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'frontend_available' => \true]);
        $this->add_control('mousewheelControl', ['label' => esc_html__('Mousewheel Control', 'dynamic-content-for-elementor'), 'description' => esc_html__('Enables navigation through slides using mouse wheel', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'frontend_available' => \true]);
        // -------------------------------- Ciclo ------
        $this->add_control('cicleloop_options', ['label' => esc_html__('Cicle / Loop', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HEADING, 'separator' => 'before']);
        $this->add_control('loop', ['label' => esc_html__('Loop', 'dynamic-content-for-elementor'), 'description' => esc_html__('Set to true to enable continuous loop mode', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'frontend_available' => \true]);
        $this->end_controls_section();
        // ------------------------------------------------------------------------------- OPTIONS
        $this->start_controls_section('section_swiper_special', ['label' => esc_html__('Swiper Special Options', 'dynamic-content-for-elementor'), 'condition' => ['posts_style' => 'swiper']]);
        // -------------------------------- Special options ---------
        $this->add_control('special_options', ['label' => esc_html__('Specials options', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HEADING, 'separator' => 'before']);
        $this->add_control('setWrapperSize', ['label' => esc_html__('Set Wrapper Size', 'dynamic-content-for-elementor'), 'description' => esc_html__('Enabled this option and plugin will set width/height on swiper wrapper equal to total size of all slides. Mostly should be used as compatibility fallback option for browser that don\'t support flexbox layout well', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'frontend_available' => \true]);
        $this->add_control('virtualTranslate', ['label' => esc_html__('Virtual Translate', 'dynamic-content-for-elementor'), 'description' => esc_html__('Enabled this option and swiper will be operated as usual except it will not move, real translate values on wrapper will not be set. Useful when you may need to create custom slide transition', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'frontend_available' => \true]);
        $this->add_control('autoHeight', ['label' => esc_html__('Auto Height', 'dynamic-content-for-elementor'), 'description' => esc_html__('Set to true and slider wrapper will adopt its height to the height of the currently active slide', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'frontend_available' => \true]);
        $this->add_control('roundLengths', ['label' => esc_html__('Round Lengths', 'dynamic-content-for-elementor'), 'description' => esc_html__('Set to true to round values of slides width and height to prevent blurry texts on usual resolution screens (if you have such)', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'frontend_available' => \true]);
        $this->add_control('nested', ['label' => esc_html__('Nested', 'dynamic-content-for-elementor'), 'description' => esc_html__('Set to true on nested Swiper for correct touch events interception. Use only on nested swipers that use same direction as the parent one', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'frontend_available' => \true]);
        $this->add_control('grabCursor', ['label' => esc_html__('Grab Cursor', 'dynamic-content-for-elementor'), 'description' => esc_html__('This option may improve desktop usability. If true, user will see the “grab” cursor when hover on Swiper', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'frontend_available' => \true]);
        // -------------------------------- Special options ---------
        $this->add_control('progress_options', ['label' => esc_html__('Progress', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HEADING, 'separator' => 'before']);
        $this->add_control('watchSlidesProgress', ['label' => esc_html__('Watch Slides Progress', 'dynamic-content-for-elementor'), 'description' => esc_html__('Enable this feature to calculate each slides progress', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'frontend_available' => \true]);
        $this->add_control('watchSlidesVisibility', ['label' => esc_html__('Watch Slides Visibility', 'dynamic-content-for-elementor'), 'description' => esc_html__('WatchSlidesProgress should be enabled. Enable this option and slides that are in viewport will have additional visible classes', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'frontend_available' => \true, 'condition' => ['watchSlidesProgress' => 'yes']]);
        $this->end_controls_section();
        //////////////////////////////////////////////////////////////////////////// [ SECTION Grid ]
        $this->start_controls_section('section_grid', ['label' => esc_html__('Grid', 'dynamic-content-for-elementor'), 'tab' => Controls_Manager::TAB_CONTENT, 'condition' => ['posts_style' => ['grid', 'flexgrid']]]);
        $this->add_responsive_control('columns_grid', ['label' => esc_html__('Columns', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'default' => '5', 'tablet_default' => '3', 'mobile_default' => '1', 'options' => ['1' => '1', '2' => '2', '3' => '3', '4' => '4', '5' => '5', '6' => '6', '7' => '7'], 'render_type' => 'template', 'selectors' => ['{{WRAPPER}} .dce-post-item' => 'width: calc( 100% / {{VALUE}} );', '{{WRAPPER}} .dce-post-item.equalHMR' => 'flex: 0 1 calc( 100% / {{VALUE}} );'], 'condition' => ['posts_style' => ['grid', 'flexgrid']]]);
        $this->add_control('fitrow_enable', ['label' => esc_html__('Fit Row', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'condition' => ['posts_style' => 'grid']]);
        $this->add_control('sameheight_enable', ['label' => esc_html__('Flex', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => 'yes', 'condition' => ['posts_style' => 'flexgrid']]);
        $this->add_control('flex_grow', ['label' => esc_html__('Flex grow', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CHOOSE, 'toggle' => \false, 'label_block' => \false, 'options' => ['1' => ['title' => esc_html__('1', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-check'], '0' => ['title' => esc_html__('0', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-ban']], 'default' => 1, 'selectors' => ['{{WRAPPER}} .dce-post-item.equalHMR' => 'flex-grow: {{VALUE}};'], 'condition' => ['posts_style' => 'flexgrid', 'sameheight_enable' => 'yes']]);
        $this->add_responsive_control('flexgrid_mode', ['label' => esc_html__('Alignment grid', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'default' => 'flex-start', 'label_block' => \true, 'options' => ['flex-start' => 'Flex start', 'flex-end' => 'Flex end', 'center' => 'Center', 'space-between' => 'Space Between', 'space-around' => 'Space Around'], 'selectors' => ['{{WRAPPER}} .equalHMRWrap' => 'justify-content: {{VALUE}};'], 'condition' => ['posts_style' => 'flexgrid', 'sameheight_enable' => 'yes', 'flex_grow' => '0']]);
        $this->add_responsive_control('v_align_items', ['label' => esc_html__('Vertical Alignment', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CHOOSE, 'options' => ['flex-start' => ['title' => esc_html__('Top', 'dynamic-content-for-elementor'), 'icon' => 'eicon-v-align-top'], 'center' => ['title' => esc_html__('Middle', 'dynamic-content-for-elementor'), 'icon' => 'eicon-v-align-middle'], 'flex-end' => ['title' => esc_html__('Down', 'dynamic-content-for-elementor'), 'icon' => 'eicon-v-align-bottom'], 'stretch' => ['title' => esc_html__('Stretch', 'dynamic-content-for-elementor'), 'icon' => 'eicon-v-align-stretch']], 'default' => 'top', 'selectors' => ['{{WRAPPER}} .equalHMR' => 'align-self: {{VALUE}};'], 'condition' => ['posts_style' => 'flexgrid', 'sameheight_enable' => 'yes', 'flex_grow' => '0']]);
        $this->end_controls_section();
        // ------------------------------------------------------------------------------------ [ SECTION Title ]
        $this->start_controls_section('section_title', ['label' => esc_html__('Title', 'dynamic-content-for-elementor'), 'tab' => Controls_Manager::TAB_CONTENT, 'condition' => ['show_title' => '1', 'templatemode_enable' => '', 'native_templatemode_enable' => '']]);
        $this->add_control('title_inout', ['label' => esc_html__('Title inside or outside of box', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HIDDEN, 'options' => ['in' => esc_html__('In', 'dynamic-content-for-elementor'), 'out' => esc_html__('Out', 'dynamic-content-for-elementor')], 'default' => 'out', 'condition' => ['show_title' => '1']]);
        $this->add_control('html_tag', ['label' => esc_html__('HTML Tag', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'options' => Helper::get_html_tags(), 'default' => 'h3', 'condition' => ['show_title' => '1']]);
        $this->add_control('title_link', ['label' => esc_html__('Use link', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => 'yes', 'condition' => ['show_title' => '1']]);
        $this->end_controls_section();
        // --------------------------------------------------------- [ SECTION TextContent ]
        $this->start_controls_section('section_textcontent', ['label' => esc_html__('Content', 'dynamic-content-for-elementor'), 'tab' => Controls_Manager::TAB_CONTENT, 'condition' => ['show_textcontent!' => '0', 'templatemode_enable' => '', 'native_templatemode_enable' => '']]);
        $this->add_control('textcontent_limit', ['label' => esc_html__('Number of characters', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::NUMBER, 'default' => '', 'condition' => ['show_textcontent' => '1']]);
        $this->add_control('textcontent_position', ['label' => esc_html__('Text Position', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HIDDEN, 'options' => ['bottom_in' => esc_html__('Bottom (in)', 'dynamic-content-for-elementor'), 'bottom_out' => esc_html__('Bottom (out)', 'dynamic-content-for-elementor'), 'top_out' => esc_html__('Top (out)', 'dynamic-content-for-elementor')], 'default' => 'bottom_in', 'prefix_class' => 'textcontent-position-', 'frontend_available' => \true, 'condition' => ['show_textcontent!' => '0']]);
        $this->add_control('nuvoletta_enable', ['label' => esc_html__('Enable Tooltip', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'options' => ['no' => esc_html__('No', 'dynamic-content-for-elementor'), 'yes' => esc_html__('Yes', 'dynamic-content-for-elementor')], 'default' => '', 'prefix_class' => 'nuvoletta-', 'frontend_available' => \true, 'condition' => ['show_textcontent' => '1', 'posts_style' => ['simplegrid', 'flexgrid', 'grid']]]);
        $this->add_responsive_control('nuvolatta_move', ['label' => esc_html__('Horizontal Offset Arrow', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => 50, 'unit' => '%'], 'tablet_default' => ['unit' => '%'], 'mobile_default' => ['unit' => '%'], 'size_units' => ['%'], 'range' => ['%' => ['min' => 0, 'max' => 100]], 'selectors' => ['{{WRAPPER}} .dce_textcontent:after' => 'left: {{SIZE}}{{UNIT}};'], 'condition' => ['nuvoletta_enable' => 'yes']]);
        $this->add_responsive_control('nuvolatta_size', ['label' => esc_html__('Arrow size', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => 15, 'unit' => 'px'], 'tablet_default' => ['unit' => 'px'], 'mobile_default' => ['unit' => 'px'], 'size_units' => ['px'], 'range' => ['%' => ['min' => 1, 'max' => 100]], 'selectors' => ['{{WRAPPER}} .dce_textcontent:after' => 'border-width: {{SIZE}}{{UNIT}}; margin-left: -{{SIZE}}{{UNIT}};'], 'condition' => ['nuvoletta_enable' => 'yes']]);
        $this->end_controls_section();
        // ------------------------------------------------------------------------------------ [ SECTION Type ]
        $this->start_controls_section('section_type', ['label' => esc_html__('Type', 'dynamic-content-for-elementor'), 'tab' => Controls_Manager::TAB_CONTENT, 'condition' => ['show_type' => '1', 'templatemode_enable' => '', 'native_templatemode_enable' => '']]);
        $this->add_control('html_tag_type', ['label' => esc_html__('HTML Tag', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'options' => ['h1' => esc_html__('H1', 'dynamic-content-for-elementor'), 'h2' => esc_html__('H2', 'dynamic-content-for-elementor'), 'h3' => esc_html__('H3', 'dynamic-content-for-elementor'), 'h4' => esc_html__('H4', 'dynamic-content-for-elementor'), 'h5' => esc_html__('H5', 'dynamic-content-for-elementor'), 'h6' => esc_html__('H6', 'dynamic-content-for-elementor'), 'p' => esc_html__('p', 'dynamic-content-for-elementor'), 'div' => esc_html__('div', 'dynamic-content-for-elementor'), 'span' => esc_html__('span', 'dynamic-content-for-elementor')], 'default' => 'h5', 'condition' => ['show_type' => '1']]);
        $this->add_control('type_label', ['label' => esc_html__('Label', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CHOOSE, 'toggle' => \false, 'label_block' => \false, 'options' => ['singular' => ['title' => esc_html__('Singular', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-user'], 'plural' => ['title' => esc_html__('Plural', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-users']], 'default' => 'singular', 'condition' => ['show_type' => '1']]);
        $this->add_control('type_link', ['label' => esc_html__('Use link', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'condition' => ['show_type' => '1']]);
        $this->end_controls_section();
        // ------------------------------------------------------------------ [ SECTION Image ]
        $this->start_controls_section('section_image', ['label' => esc_html__('Image', 'dynamic-content-for-elementor'), 'tab' => Controls_Manager::TAB_CONTENT, 'condition' => ['show_image' => '1', 'templatemode_enable' => '', 'native_templatemode_enable' => '']]);
        $this->add_control('use_bgimage', ['label' => esc_html__('Background Image', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'prefix_class' => 'bgimage-', 'render_type' => 'template', 'condition' => ['show_image' => '1']]);
        $this->add_responsive_control('height_bgimage', ['label' => esc_html__('Height', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => 120, 'unit' => 'px'], 'size_units' => ['px'], 'range' => ['px' => ['min' => 10, 'max' => 1000]], 'selectors' => ['{{WRAPPER}} .acfposts-image' => 'min-height: {{SIZE}}{{UNIT}};', '{{WRAPPER}} .dce-overlay_hover, {{WRAPPER}} .dce-overlay' => 'min-height: {{SIZE}}{{UNIT}};'], 'condition' => ['use_bgimage' => 'yes']]);
        $this->add_group_control(Group_Control_Image_Size::get_type(), ['name' => 'size', 'label' => esc_html__('Image Size', 'dynamic-content-for-elementor'), 'default' => 'large', 'condition' => ['show_image' => '1']]);
        $this->add_responsive_control('size_image', ['label' => esc_html__('Size', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'size_units' => ['%', 'px', 'vw'], 'range' => ['%' => ['min' => 1, 'max' => 100, 'step' => 1], 'vw' => ['min' => 1, 'max' => 100, 'step' => 1], 'px' => ['min' => 1, 'max' => 800, 'step' => 1]], 'selectors' => ['{{WRAPPER}} .dce-acfposts_image > *' => 'max-width: {{SIZE}}{{UNIT}};'], 'condition' => ['show_image' => '1']]);
        // Link Image
        $this->add_control('image_link', ['label' => esc_html__('Use link', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => 'yes', 'condition' => ['show_image' => '1']]);
        $this->add_control('use_overlay', ['label' => esc_html__('Overlay', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CHOOSE, 'toggle' => \false, 'label_block' => \false, 'separator' => 'before', 'options' => ['1' => ['title' => esc_html__('Yes', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-check'], '0' => ['title' => esc_html__('No', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-ban']], 'default' => '0', 'condition' => ['show_image' => '1']]);
        $this->add_group_control(Group_Control_Background::get_type(), ['name' => 'overlay_color', 'label' => esc_html__('Background', 'dynamic-content-for-elementor'), 'types' => ['classic', 'gradient'], 'selector' => '{{WRAPPER}} .dce-overlay', 'condition' => ['show_image' => '1', 'use_overlay' => '1']]);
        $this->add_responsive_control('overlay_opacity', ['label' => esc_html__('Opacity (%)', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => 0.7], 'range' => ['px' => ['max' => 1, 'min' => 0.1, 'step' => 0.01]], 'selectors' => ['{{WRAPPER}} .dce-overlay' => 'opacity: {{SIZE}};'], 'condition' => ['show_image' => '1', 'use_overlay' => '1']]);
        $this->end_controls_section();
        // --------------------------------------------------- [ SECTION Metadata (Terms) ]
        $this->start_controls_section('section_metadata', ['label' => esc_html__('Meta Data', 'dynamic-content-for-elementor'), 'tab' => Controls_Manager::TAB_CONTENT, 'condition' => ['show_metadata' => '1', 'templatemode_enable' => '', 'native_templatemode_enable' => '']]);
        $this->add_control('taxonomy_metadata_filter', ['label' => esc_html__('Filter Taxonomy', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT2, 'multiple' => \true, 'options' => $taxonomies, 'placeholder' => esc_html__('All', 'dynamic-content-for-elementor'), 'description' => esc_html__('Use only terms in selected taxonomies. If empty all terms will be used.', 'dynamic-content-for-elementor'), 'condition' => ['show_metadata' => '1']]);
        $this->add_control('metadata_inout', ['label' => esc_html__('Metadata inside or outside of box', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HIDDEN, 'options' => ['in' => esc_html__('In', 'dynamic-content-for-elementor'), 'out' => esc_html__('Out', 'dynamic-content-for-elementor')], 'default' => 'out', 'condition' => ['show_metadata' => '1']]);
        $this->add_control('separator_metadata', ['label' => esc_html__('Separator', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'default' => ', ', 'condition' => ['show_metadata' => '1']]);
        $this->add_control('only_parent_metadata', ['label' => esc_html__('Only parent items', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'condition' => ['show_metadata' => '1']]);
        $this->add_control('metadata_link', ['label' => esc_html__('Use link', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => 'yes', 'condition' => ['show_metadata' => '1']]);
        $this->add_control('metadata_block_enable', ['label' => esc_html__('Block', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'condition' => ['show_metadata' => '1']]);
        $this->add_control('metadata_icon_enable', ['label' => esc_html__('Icon', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'condition' => ['show_metadata' => '1']]);
        $this->end_controls_section();
        // ------------------------------------------------------ [ SECTION Metadata (Terms) ]
        $this->start_controls_section('section_author', ['label' => esc_html__('Author', 'dynamic-content-for-elementor'), 'tab' => Controls_Manager::TAB_CONTENT, 'condition' => ['show_author' => '1', 'templatemode_enable' => '', 'native_templatemode_enable' => '']]);
        $this->add_control('enable_author_image', ['label' => esc_html__('Show image', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => 'yes', 'condition' => ['show_author' => '1']]);
        $this->add_control('enable_author_bio', ['label' => esc_html__('Show biography', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => '', 'condition' => ['show_author' => '1']]);
        $this->add_control('author_inout', ['label' => esc_html__('Author inside or outside of box', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HIDDEN, 'options' => ['in' => esc_html__('In', 'dynamic-content-for-elementor'), 'out' => esc_html__('Out', 'dynamic-content-for-elementor')], 'default' => 'out']);
        $this->end_controls_section();
        // --------------------------------------------------------- [ SECTION Date ]
        $this->start_controls_section('section_date', ['label' => esc_html__('Date', 'dynamic-content-for-elementor'), 'tab' => Controls_Manager::TAB_CONTENT, 'condition' => ['show_date' => '1', 'templatemode_enable' => '', 'native_templatemode_enable' => '']]);
        $this->add_control('date_type', ['label' => esc_html__('Date Type', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'options' => ['publish' => esc_html__('Publish Date', 'dynamic-content-for-elementor'), 'modified' => esc_html__('Last Modified Date', 'dynamic-content-for-elementor')], 'default' => 'publish', 'condition' => ['show_date' => '1']]);
        $this->add_control('date_format', ['label' => esc_html__('Date Format', 'dynamic-content-for-elementor'), 'description' => esc_html__('The format of date.', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'default' => 'j.F.Y', 'condition' => ['show_date' => '1']]);
        $this->add_control('date_format_2', ['label' => esc_html__('Date Format 2', 'dynamic-content-for-elementor'), 'description' => esc_html__('The format of date 2.', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'default' => '', 'condition' => ['show_date' => '1']]);
        $this->add_control('date_format_3', ['label' => esc_html__('Date Format 3', 'dynamic-content-for-elementor'), 'description' => esc_html__('The format of date 3.', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'default' => '', 'condition' => ['show_date' => '1']]);
        $this->end_controls_section();
        // ---------------------------------------------- [ SECTION Read More Button ]
        $this->start_controls_section('section_readmore', ['label' => esc_html__('Read More Button', 'dynamic-content-for-elementor'), 'tab' => Controls_Manager::TAB_CONTENT, 'condition' => ['show_readmore' => '1', 'templatemode_enable' => '', 'native_templatemode_enable' => '']]);
        $this->add_control('readmore_text', ['label' => esc_html__('Text button', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'default' => esc_html__('Read More', 'dynamic-content-for-elementor'), 'condition' => ['show_readmore' => '1']]);
        $this->add_control('readmore_inout', ['label' => esc_html__('Read More button inside or outside of box', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HIDDEN, 'options' => ['in' => esc_html__('In', 'dynamic-content-for-elementor'), 'out' => esc_html__('Out', 'dynamic-content-for-elementor')], 'default' => 'out', 'condition' => ['show_readmore' => '1']]);
        $this->end_controls_section();
        // ---------------------------------------------------- [ ACF repeater START ]
        $this->start_controls_section('section_acfitems', ['label' => esc_html__('Custom Fields', 'dynamic-content-for-elementor'), 'tab' => Controls_Manager::TAB_CONTENT, 'condition' => ['templatemode_enable' => '', 'native_templatemode_enable' => '', 'show_acfitems' => '1']]);
        $repeater = new Repeater();
        $repeater->start_controls_tabs('acfitems_repeater');
        $repeater->start_controls_tab('tab_content', ['label' => esc_html__('Item', 'dynamic-content-for-elementor')]);
        $repeater->add_control('acf_field_item', ['label' => esc_html__('Fields', 'dynamic-content-for-elementor'), 'type' => 'ooo_query', 'placeholder' => esc_html__('Select the field...', 'dynamic-content-for-elementor'), 'label_block' => \true, 'query_type' => 'acfposts']);
        $repeater->add_control('acf_field_type', ['label' => esc_html__('Field type', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'default' => 'text', 'options' => ['text' => esc_html__('Text', 'dynamic-content-for-elementor'), 'image' => esc_html__('Image', 'dynamic-content-for-elementor'), 'date' => esc_html__('Date', 'dynamic-content-for-elementor')]]);
        $repeater->add_control('acf_date_format', ['label' => esc_html__('Date Format', 'dynamic-content-for-elementor'), 'description' => esc_html__('The format of date.', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'default' => 'F j, Y, g:i a', 'condition' => ['acf_field_type' => 'date']]);
        $repeater->add_control('html_tag_item', ['label' => esc_html__('HTML Tag', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'options' => Helper::get_html_tags([], \true), 'condition' => ['acf_field_type' => 'text'], 'default' => '']);
        $repeater->add_control('link_to', ['label' => esc_html__('Link to', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'default' => 'none', 'options' => ['none' => esc_html__('None', 'dynamic-content-for-elementor'), 'home' => esc_html__('Home URL', 'dynamic-content-for-elementor'), 'post' => 'Post URL', 'custom' => esc_html__('Custom URL', 'dynamic-content-for-elementor')]]);
        $repeater->add_control('link', ['label' => esc_html__('Link', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::URL, 'placeholder' => esc_html__('https://your-link.com', 'dynamic-content-for-elementor'), 'condition' => ['link_to' => 'custom'], 'default' => ['url' => ''], 'show_label' => \false]);
        $repeater->add_control('taxonomy_metadata', ['label' => esc_html__('Taxonomy', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'options' => $taxonomies, 'default' => 'category', 'condition' => ['acf_field_item' => 'taxonomy']]);
        $repeater->end_controls_tab();
        $repeater->start_controls_tab('tab_style', ['label' => esc_html__('Style', 'dynamic-content-for-elementor')]);
        $repeater->add_control('block_enable', ['label' => esc_html__('Block', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'frontend_available' => \true]);
        $repeater->add_control('padding_item', ['label' => esc_html__('Padding', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => ['px', '%'], 'selectors' => ['{{WRAPPER}} .dce-acfposts_content {{CURRENT_ITEM}}' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'], 'condition' => ['block_enable' => 'yes']]);
        $repeater->add_control('color_item', ['label' => esc_html__('Text Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .dce-acfposts_content {{CURRENT_ITEM}}, {{WRAPPER}} .dce-acfposts_content {{CURRENT_ITEM}} > *' => 'color: {{VALUE}};', '{{WRAPPER}} .dce-acfposts_content {{CURRENT_ITEM}} a' => 'color: {{VALUE}};'], 'condition' => ['acf_field_type' => ['text', 'date']]]);
        $repeater->add_control('hover_color_item', ['label' => esc_html__('Hover Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .dce-acfposts_content {{CURRENT_ITEM}} > * a:hover' => 'color: {{VALUE}};'], 'condition' => ['acf_field_type' => ['text', 'date']]]);
        $repeater->add_group_control(Group_Control_Typography::get_type(), ['name' => 'typography_item', 'selector' => '{{WRAPPER}} .dce-acfposts_content {{CURRENT_ITEM}}, {{WRAPPER}} .dce-acfposts_content {{CURRENT_ITEM}} > *', 'render_type' => 'ui', 'condition' => ['acf_field_type' => ['text', 'date']]]);
        $repeater->end_controls_tab();
        $repeater->end_controls_tabs();
        $this->add_control('acf_items', ['label' => esc_html__('Meta Items', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::REPEATER, 'fields' => \array_values($repeater->get_controls()), 'title_field' => '{{{ acf_field_item }}}', 'prevent_empty' => \false, 'condition' => ['show_acfitems' => '1']]);
        $this->end_controls_section();
        // ----------------------------------------------- [ SECTION Filters ]
        $this->start_controls_section('section_filters', ['label' => esc_html__('Filters', 'dynamic-content-for-elementor'), 'tab' => Controls_Manager::TAB_CONTENT, 'condition' => ['posts_style' => 'grid', 'filters_enable' => 'yes']]);
        $this->add_control('filters_taxonomy', ['label' => esc_html__('Data Filters (Taxonomy)', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'options' => ['' => esc_html__('None', 'dynamic-content-for-elementor')] + $taxonomies, 'default' => 'category', 'label_block' => \true]);
        $this->add_control('filters_taxonomy_first_level_terms', ['label' => esc_html__('Use first level Terms', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => '', 'description' => esc_html__('Use all First level Terms of the selected taxonomy', 'dynamic-content-for-elementor'), 'condition' => ['filters_taxonomy!' => '']]);
        foreach ($taxonomies as $tkey => $atax) {
            if ($tkey) {
                $this->add_control('filters_taxonomy_terms_' . $tkey, ['label' => esc_html__('Data Filters (Selected Terms)', 'dynamic-content-for-elementor'), 'type' => 'ooo_query', 'placeholder' => esc_html__('Term Name', 'dynamic-content-for-elementor'), 'label_block' => \true, 'query_type' => 'terms', 'object_type' => $tkey, 'description' => esc_html__('Use only Selected taxonomy terms or leave empty to use All terms of this taxonomy', 'dynamic-content-for-elementor'), 'multiple' => \true, 'condition' => ['filters_taxonomy' => $tkey, 'filters_taxonomy_first_level_terms' => '']]);
            }
        }
        $this->add_control('orderby_filters', ['label' => esc_html__('Order By', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'options' => Helper::get_post_orderby_options(), 'default' => 'date']);
        $this->add_control('filters_acf', ['label' => esc_html__('Data Filters', 'dynamic-content-for-elementor'), 'type' => 'ooo_query', 'placeholder' => esc_html__('Select the field...', 'dynamic-content-for-elementor'), 'label_block' => \true, 'query_type' => 'acf', 'object_type' => ['text', 'textarea', 'select', 'number', 'date_time_picker', 'date_picker', 'oembed', 'file', 'url', 'image', 'wysiwyg']]);
        $this->add_control('all_filter', ['label' => esc_html__('Add "All" filter', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => 'yes']);
        $this->add_control('alltext_filter', ['label' => esc_html__('All text', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'default' => 'All', 'condition' => ['all_filter!' => '']]);
        $this->add_control('separator_filter', ['label' => esc_html__('Separator', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'default' => ' / ']);
        $this->add_responsive_control('filters_align', ['label' => esc_html__('Filters Alignment', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CHOOSE, 'toggle' => \false, 'options' => ['left' => ['title' => esc_html__('Left', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-left'], 'center' => ['title' => esc_html__('Center', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-center'], 'right' => ['title' => esc_html__('Right', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-right']], 'default' => 'left', 'selectors' => ['{{WRAPPER}} .dce-filters' => 'text-align: {{VALUE}};']]);
        $this->add_control('filter_hide_empty', ['label' => esc_html__('Show/Hide empty terms', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => 'yes', 'label_on' => esc_html__('Show', 'dynamic-content-for-elementor'), 'label_off' => esc_html__('Hide', 'dynamic-content-for-elementor')]);
        $this->end_controls_section();
        // ------------------------------------- [SECTION Hover Effects]
        $this->start_controls_section('section_hover_effect', ['label' => esc_html__('Hover effect', 'dynamic-content-for-elementor'), 'tab' => Controls_Manager::TAB_CONTENT]);
        $this->add_responsive_control('hover_opacity', ['label' => esc_html__('Block Hover Opacity (%)', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'range' => ['px' => ['max' => 1, 'min' => 0.1, 'step' => 0.01]], 'selectors' => ['{{WRAPPER}} .dce-post-item:hover' => 'opacity: {{SIZE}};'], 'condition' => ['image_position' => 'top']]);
        $this->add_control('hover_text_effect', ['label' => esc_html__('TextZone Effect', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'default' => '', 'options' => ['' => esc_html__('None', 'dynamic-content-for-elementor'), 'fade' => 'Fade', 'slidebottom' => 'Slide bottom', 'slidetop' => 'Slide top', 'slideleft' => 'Slide left', 'slideright' => 'Slide right', 'cssanimations' => 'Css Animations'], 'render_type' => 'template', 'separator' => 'before', 'prefix_class' => 'hovertexteffect-', 'condition' => ['image_position' => 'top']]);
        $this->add_control('hover_text_effect_timingFunction', ['label' => esc_html__('Effect Timing function', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'groups' => Helper::get_anim_timing_functions(), 'default' => 'ease-in-out', 'selectors' => ['{{WRAPPER}} .dce-post-item .dce-hover-effect-content' => 'transition-timing-function: {{VALUE}}; -webkit-transition-timing-function: {{VALUE}};'], 'condition' => ['hover_text_effect!' => ['', 'cssanimations'], 'image_position' => 'top']]);
        $this->add_control('hover_text_effect_animation_in', ['label' => esc_html__('IN Animation effect', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'groups' => Helper::get_anim_in(), 'default' => 'fadeIn', 'frontend_available' => \true, 'render_type' => 'template', 'condition' => ['hover_text_effect' => 'cssanimations', 'image_position' => 'top'], 'selectors' => ['{{WRAPPER}} .dce-post-item .dce-hover-effect-content.dce-open' => 'animation-name: {{VALUE}}; -webkit-animation-name: {{VALUE}};']]);
        $this->add_control('hover_text_effect_timingFunction_in', ['label' => esc_html__('IN Effect Timing function', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'groups' => Helper::get_anim_timing_functions(), 'default' => 'ease-in-out', 'selectors' => ['{{WRAPPER}} .dce-post-item:hover .dce-hover-effect-content.dce-open' => 'animation-timing-function: {{VALUE}}; -webkit-animation-timing-function: {{VALUE}};'], 'condition' => ['hover_text_effect' => 'cssanimations', 'image_position' => 'top']]);
        $this->add_control('hover_text_effect_animation_out', ['label' => esc_html__('OUT Animation effect', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'groups' => Helper::get_anim_out(), 'default' => 'fadeOut', 'frontend_available' => \true, 'render_type' => 'template', 'condition' => ['hover_text_effect' => 'cssanimations', 'image_position' => 'top'], 'selectors' => ['{{WRAPPER}} .dce-post-item .dce-hover-effect-content.dce-close' => 'animation-name: {{VALUE}}; -webkit-animation-name: {{VALUE}};']]);
        $this->add_control('hover_text_effect_timingFunction_out', ['label' => esc_html__('OUT Effect Timing function', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'groups' => Helper::get_anim_timing_functions(), 'default' => 'ease-in-out', 'selectors' => ['{{WRAPPER}} .dce-post-item .dce-hover-effect-content.dce-close' => 'animation-timing-function: {{VALUE}}; -webkit-animation-timing-function: {{VALUE}};'], 'condition' => ['hover_text_effect' => 'cssanimations', 'image_position' => 'top']]);
        $this->add_control('hr', ['type' => Controls_Manager::DIVIDER, 'style' => 'thick']);
        $this->add_group_control(Group_Control_Filters_CSS::get_type(), ['name' => 'hover_filters_image', 'label' => esc_html__('Filters Image', 'dynamic-content-for-elementor'), 'selector' => '{{WRAPPER}} .dce-acfposts_image a:hover .acfposts-image']);
        $this->add_control('hover_animation', ['label' => esc_html__('Hover Animation', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HOVER_ANIMATION, 'separator' => 'before']);
        $this->add_control('use_overlay_hover', ['label' => esc_html__('Overlay', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CHOOSE, 'toggle' => \false, 'label_block' => \false, 'separator' => 'before', 'options' => ['1' => ['title' => esc_html__('Yes', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-check'], '0' => ['title' => esc_html__('No', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-ban']], 'default' => '0', 'condition' => ['image_link!' => '']]);
        $this->add_group_control(Group_Control_Background::get_type(), ['name' => 'overlay_color_hover', 'label' => esc_html__('Background', 'dynamic-content-for-elementor'), 'types' => ['classic', 'gradient'], 'selector' => '{{WRAPPER}} .dce-overlay_hover', 'condition' => ['use_overlay_hover' => '1', 'image_link!' => '']]);
        $this->end_controls_section();
        // ------------------------------------------- [SECTION DYNAMIC CONTENT - DCE ]
        $this->start_controls_section('section_template', ['label' => esc_html__('Template', 'dynamic-content-for-elementor')]);
        $this->add_control('templatemode_enable', ['label' => esc_html__('Enable Template', 'dynamic-content-for-elementor'), 'description' => esc_html__('Enable a template to manage the appearance of individual grid elements ', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'render_type' => 'template', 'prefix_class' => 'templatemode-', 'condition' => ['native_templatemode_enable' => '']]);
        $this->add_control('templatemode_template', ['label' => esc_html__('Template', 'dynamic-content-for-elementor'), 'type' => 'ooo_query', 'placeholder' => esc_html__('Template Name', 'dynamic-content-for-elementor'), 'label_block' => \true, 'query_type' => 'posts', 'object_type' => 'elementor_library', 'condition' => ['templatemode_enable!' => '', 'native_templatemode_enable' => '']]);
        $this->add_control('templatemode_enable_2', ['label' => esc_html__('Enable Template 2', 'dynamic-content-for-elementor'), 'description' => esc_html__('Enable a template to manage the appearance of the odd elements of the grid ', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'render_type' => 'template', 'prefix_class' => 'templatemode-', 'condition' => ['native_templatemode_enable' => '']]);
        $this->add_control('templatemode_template_2', ['label' => esc_html__('Template for odd posts', 'dynamic-content-for-elementor'), 'type' => 'ooo_query', 'placeholder' => esc_html__('Template Name', 'dynamic-content-for-elementor'), 'label_block' => \true, 'query_type' => 'posts', 'object_type' => 'elementor_library', 'condition' => ['templatemode_enable!' => '', 'templatemode_enable_2!' => '', 'native_templatemode_enable' => '']]);
        $this->add_control('native_templatemode_enable', ['label' => esc_html__('Enable Native Template', 'dynamic-content-for-elementor'), 'description' => esc_html__('Use the template associated with the type (Menu: Elementor> Dynamic Content) to manage the appearance of the individual elements of the grid ', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'prefix_class' => 'templatemode-', 'render_type' => 'template', 'condition' => ['templatemode_enable' => '']]);
        $this->add_control('templatemode_linkable', ['label' => esc_html__('Linkable', 'dynamic-content-for-elementor'), 'description' => esc_html__('Use the extended link on the template block.', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'render_type' => 'template']);
        $this->end_controls_section();
        // --------------------------------------------- [SECTION WOW animations ]
        $this->start_controls_section('section_wow', ['label' => esc_html__('Wow Animation', 'dynamic-content-for-elementor')]);
        $this->add_control('enabled_wow', ['label' => esc_html__('Enable WOW Animation', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'frontend_available' => \true]);
        // coefficiente per default
        $this->add_control('wow_coef', ['label' => esc_html__('Delay coefficient', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::NUMBER, 'default' => 0, 'min' => 0.01, 'max' => 1, 'step' => 0.01, 'condition' => ['enabled_wow' => 'yes']]);
        $this->add_control('wow_animations', [
            'label' => esc_html__('Wow Animation Effect', 'dynamic-content-for-elementor'),
            'type' => Controls_Manager::SELECT,
            'options' => ['fadeIn' => 'Fade In', 'fadeInDown' => 'Fade In Down', 'fadeInLeft' => 'Fade In Left', 'fadeInRight' => 'Fade In Right', 'fadeInUp' => 'Fade In Up', 'zoomIn' => 'Zoom In', 'zoomInDown' => 'Zoom In Down', 'zoomInLeft' => 'Zoom In Left', 'zoomInRight' => 'Zoom In Right', 'zoomInUp' => 'Zoom In Up', 'bounceIn' => 'Bounce In', 'bounceInDown' => 'Bounce In Down', 'bounceInLeft' => 'Bounce In Left', 'bounceInRight' => 'Bounce In Right', 'bounceInUp' => 'Bounce In Up', 'slideInDown' => 'Slide In Down', 'slideInLeft' => 'Slide In Left', 'slideInRight' => 'Slide In Right', 'slideInUp' => 'Slide In Up', 'rotateIn' => 'Rotate In', 'rotateInDownLeft' => 'Rotate In Down Left', 'rotateInDownRight' => 'Rotate In Down Right', 'rotateInUpLeft' => 'Rotate In Up Left', 'rotateInUpRight' => 'Rotate In Up Right', 'bounce' => 'Bounce', 'flash' => 'Flash', 'pulse' => 'Pulse', 'rubberBand' => 'Rubber Band', 'shake' => 'Shake', 'headShake' => 'Head Shake', 'swing' => 'Swing', 'tada' => 'Tada', 'wobble' => 'Wobble', 'jello' => 'Jello', 'lightSpeedIn' => 'Light Speed In', 'rollIn' => 'Roll In'],
            'default' => 'fadeInUp',
            //'frontend_available' => true,
            'condition' => ['enabled_wow' => 'yes'],
        ]);
        $this->end_controls_section();
        ////////////////////////////////////////////////////////////////////////////////////////// STYLE TAB
        // -------------------------------------------------------------------------- [ section Style - Timeline ]
        $this->start_controls_section('section_style_timeline', ['label' => esc_html__('Timeline', 'dynamic-content-for-elementor'), 'tab' => Controls_Manager::TAB_STYLE, 'condition' => ['posts_style' => 'timeline']]);
        $this->add_control('timeline_bg_color_content', ['label' => esc_html__('Content Background Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .cd-timeline__content' => 'background-color: {{VALUE}};', '{{WRAPPER}} .cd-timeline__block:nth-child(odd) .cd-timeline__content::before' => 'border-left-color: {{VALUE}}; border-right-color: {{VALUE}};', '{{WRAPPER}} .cd-timeline__block:nth-child(even) .cd-timeline__content::before' => 'border-right-color: {{VALUE}}; border-right-color: {{VALUE}};']]);
        $this->add_control('timleline_line_color', ['label' => esc_html__('Line Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .cd-timeline__container:before' => 'background-color: {{VALUE}};']]);
        $this->add_control('timeline_line_size', ['label' => esc_html__('Line size', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => 4, 'unit' => 'px'], 'size_units' => ['px'], 'range' => ['px' => ['min' => 0, 'max' => 100]], 'selectors' => ['{{WRAPPER}} .cd-timeline__container:before' => 'width: {{SIZE}}{{UNIT}};']]);
        $this->add_control('timeline_arrow_size', ['label' => esc_html__('Row size', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => 4, 'unit' => 'px'], 'size_units' => ['px', 'em'], 'range' => ['px' => ['min' => 0, 'max' => 100]], 'selectors' => ['{{WRAPPER}} .cd-timeline__container .cd-timeline__block' => 'margin: {{SIZE}}{{UNIT}} 0;']]);
        $this->add_responsive_control('content_width', ['label' => esc_html__('Content Width', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => 45, 'unit' => '%'], 'size_units' => ['px', '%'], 'range' => ['%' => ['min' => 1, 'max' => 100, 'step' => 0.1], 'px' => ['min' => 1, 'max' => 600]], 'selectors' => ['{{WRAPPER}} .cd-timeline__content' => 'width: {{SIZE}}{{UNIT}};']]);
        $this->add_responsive_control('date_width', ['label' => esc_html__('Date Width', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => 130, 'unit' => '%'], 'size_units' => ['px', '%'], 'range' => ['%' => ['min' => 1, 'max' => 200, 'step' => 1], 'px' => ['min' => 1, 'max' => 600]], 'selectors' => ['{{WRAPPER}} .cd-timeline__date' => 'width: {{SIZE}}{{UNIT}};']]);
        $this->add_responsive_control('date_pos_x', ['label' => esc_html__('Date Position X', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => 0, 'unit' => '%'], 'size_units' => ['px', '%'], 'range' => ['%' => ['min' => 0, 'max' => 100, 'step' => 1], 'px' => ['min' => 1, 'max' => 800]], 'selectors' => ['{{WRAPPER}} .cd-timeline__block:nth-child(odd) .cd-timeline__date' => 'left: {{SIZE}}{{UNIT}};', '{{WRAPPER}} .cd-timeline__block:nth-child(even) .cd-timeline__date' => 'right: {{SIZE}}{{UNIT}};']]);
        $this->add_control('timeline_radius_content', ['label' => esc_html__('Content Border Radius', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => 10, 'unit' => 'px'], 'size_units' => ['px', '%'], 'range' => ['%' => ['min' => 0, 'max' => 50], 'px' => ['min' => 0, 'max' => 100]], 'selectors' => ['{{WRAPPER}} .cd-timeline__content' => 'border-radius: {{SIZE}}{{UNIT}};']]);
        $this->add_control('timeline_arrows_size', ['label' => esc_html__('Content arrows size', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => 7, 'unit' => 'px'], 'size_units' => ['px'], 'range' => ['px' => ['min' => 0, 'max' => 100]], 'selectors' => ['{{WRAPPER}} .cd-timeline__content::before, {{WRAPPER}} .cd-timeline__content::before' => 'border-width: {{SIZE}}{{UNIT}};']]);
        $this->end_controls_section();
        // -------------------------------------------------------------------------- [ section Style - Block ]
        $this->start_controls_section('section_style_template', ['label' => esc_html__('Style Info', 'dynamic-content-for-elementor'), 'tab' => Controls_Manager::TAB_STYLE, 'condition' => ['templatemode_enable!' => '']]);
        $this->add_control('templatemode_info', ['type' => Controls_Manager::RAW_HTML, 'show_label' => \false, 'raw' => esc_html__('Styles are managed in the template.', 'dynamic-content-for-elementor'), 'content_classes' => 'elementor-panel-alert elementor-panel-alert-warning', 'separator' => 'after']);
        $this->end_controls_section();
        $this->start_controls_section('section_style_textzone', ['label' => esc_html__('Blocks', 'dynamic-content-for-elementor'), 'tab' => Controls_Manager::TAB_STYLE]);
        $this->add_control('textzone_bgcolor', ['label' => esc_html__('Blocks Background Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .dce-post-item .dce-wrapper' => 'background-color: {{VALUE}};']]);
        $this->add_group_control(Group_Control_Box_Shadow::get_type(), ['name' => 'textzone_boxshadow', 'selector' => '{{WRAPPER}} .dce-post-item .dce-wrapper']);
        $this->add_responsive_control('layout_align', ['label' => esc_html__('Text Alignment', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CHOOSE, 'toggle' => \false, 'options' => ['left' => ['title' => esc_html__('Left', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-left'], 'center' => ['title' => esc_html__('Center', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-center'], 'right' => ['title' => esc_html__('Right', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-right']], 'default' => 'left', 'selectors' => ['{{WRAPPER}} .dce-post-item' => 'text-align: {{VALUE}};'], 'prefix_class' => 'acfposts%s-align-', 'condition' => ['posts_style!' => 'timeline', 'templatemode_enable' => ''], 'separator' => 'before']);
        $this->add_responsive_control('h_align_blocks', ['label' => esc_html__('Horizontal Alignment (Flex)', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HIDDEN, 'options' => ['flex-start' => ['title' => esc_html__('Left', 'dynamic-content-for-elementor'), 'icon' => 'eicon-h-align-left'], 'center' => ['title' => esc_html__('Center', 'dynamic-content-for-elementor'), 'icon' => 'eicon-h-align-center'], 'flex-end' => ['title' => esc_html__('Right', 'dynamic-content-for-elementor'), 'icon' => 'eicon-h-align-right']], 'selectors' => ['{{WRAPPER}} .dce-wrapper' => 'justify-content: {{VALUE}};'], 'condition' => ['posts_style' => 'flexgrid', 'image_position!' => 'alternate', 'templatemode_enable' => '']]);
        $this->add_responsive_control('v_align_blocks', ['label' => esc_html__('Vertical Alignment (Flex)', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CHOOSE, 'options' => ['flex-start' => ['title' => esc_html__('Top', 'dynamic-content-for-elementor'), 'icon' => 'eicon-v-align-top'], 'center' => ['title' => esc_html__('Middle', 'dynamic-content-for-elementor'), 'icon' => 'eicon-v-align-middle'], 'flex-end' => ['title' => esc_html__('Down', 'dynamic-content-for-elementor'), 'icon' => 'eicon-v-align-bottom']], 'separator' => 'after', 'selectors' => ['{{WRAPPER}} .dce-wrapper' => 'display: flex; flex-direction: row; align-items: {{VALUE}};'], 'condition' => ['posts_style' => 'flexgrid', 'image_position!' => 'alternate', 'templatemode_enable' => '']]);
        $this->add_responsive_control('blocks_v_alternate', ['label' => esc_html__('Vertical alternate', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'size_units' => ['px'], 'range' => ['px' => ['max' => 100, 'min' => 0, 'step' => 1]], 'render_type' => 'template', 'selectors' => ['{{WRAPPER}} .dce-post-item.column-3:nth-child(3n+2) .dce-wrapper, {{WRAPPER}} .dce-post-item:not(.column-3):nth-child(even) .dce-wrapper' => 'margin-top: {{SIZE}}{{UNIT}};'], 'condition' => ['posts_style!' => 'timeline']]);
        $this->add_group_control(Group_Control_Border::get_type(), ['name' => 'border_textzone', 'label' => esc_html__('Image Border', 'dynamic-content-for-elementor'), 'separator' => 'before', 'selector' => '{{WRAPPER}} .dce-post-item > .dce-wrapper']);
        $this->add_responsive_control('text_padding_item', ['label' => esc_html__('Blocks Padding', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::DIMENSIONS, 'separator' => 'before', 'size_units' => ['px', '%', 'em'], 'selectors' => ['{{WRAPPER}} .dce-post-item .dce-wrapper' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};']]);
        $this->add_responsive_control('textzone_margin', ['label' => esc_html__('Blocks Margin', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => ['px', '%', 'em'], 'selectors' => ['{{WRAPPER}} .dce-post-item' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};']]);
        $this->add_control('border_radius_textzone', ['label' => esc_html__('Block Border Radius', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => ['px', '%', 'em'], 'selectors' => ['{{WRAPPER}} .dce-post-item .dce-wrapper' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};']]);
        $this->add_control('radius_masking_enable', ['label' => esc_html__('BorderRadius add Masking', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => '', 'separator' => 'after', 'prefix_class' => 'add-radius-masking-', 'condition' => ['border_radius_textzone[left]!' => '']]);
        $this->end_controls_section();
        // --------------------------------------------------------- [ section Content Text Style - TextContent ]
        $this->start_controls_section('section_style_contenttextzone', ['label' => esc_html__('Content Zone', 'dynamic-content-for-elementor'), 'tab' => Controls_Manager::TAB_STYLE, 'condition' => ['templatemode_enable' => '', 'posts_style!' => 'timeline']]);
        $this->add_control('textzone_heading', ['label' => esc_html__('Text Zone', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HEADING, 'separator' => 'before']);
        $this->add_control('textarea_bgcolor', ['label' => esc_html__('Background Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'default' => '', 'selectors' => ['{{WRAPPER}} .dce-acfposts_content' => 'background-color: {{VALUE}};']]);
        $this->add_responsive_control('textzone_padding', ['label' => esc_html__('Padding', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => ['px', '%'], 'selectors' => ['{{WRAPPER}} .dce-acfposts_content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};']]);
        $this->add_responsive_control('textzone_movement', ['label' => esc_html__('Movement (%)', 'dynamic-content-for-elementor'), 'type' => 'xy_movement', 'separator' => 'before', 'default' => ['x' => '', 'y' => ''], 'responsive' => \true, 'render_type' => 'ui', 'selectors' => ['{{WRAPPER}} .dce-acfposts_content' => 'transform: translate({{X}}%, {{Y}}%); -webkit-transform: translate({{X}}%, {{Y}}%);']]);
        $this->add_responsive_control('textzone_width', ['label' => esc_html__('Width (%)', 'dynamic-content-for-elementor'), 'description' => esc_html__('Available only with [Layout > Position Top]', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'size_units' => ['%'], 'range' => ['%' => ['min' => 1, 'max' => 100, 'step' => 0.1]], 'selectors' => ['{{WRAPPER}}.image-acfposts-position-top .dce-acfposts_content' => 'width: {{SIZE}}% !important;'], 'condition' => ['posts_style!' => 'timeline']]);
        $this->add_control('border_radius_textzone_2', ['label' => esc_html__('Border Radius', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::DIMENSIONS, 'separator' => 'before', 'size_units' => ['px', '%', 'em'], 'selectors' => ['{{WRAPPER}} .dce-acfposts_content' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};']]);
        $this->add_control('overflow_textzone_2', ['label' => esc_html__('Overflow Hidden', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'return_value' => 'hidden', 'selectors' => ['{{WRAPPER}} .dce-acfposts_content' => 'overflow: {{VALUE}};']]);
        $this->add_group_control(Group_Control_Box_Shadow::get_type(), ['name' => 'box_shadow_textzone', 'selector' => '{{WRAPPER}} .dce-acfposts_content']);
        $this->end_controls_section();
        // --------------------------------------------------------- [ section Style - Title ]
        $this->start_controls_section('section_style_text', ['label' => esc_html__('Title', 'dynamic-content-for-elementor'), 'tab' => Controls_Manager::TAB_STYLE, 'condition' => ['show_title' => '1', 'templatemode_enable' => '', 'native_templatemode_enable' => '']]);
        $this->add_control('color', ['label' => esc_html__('Text Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .dce-post-title ' => 'color: {{VALUE}};', '{{WRAPPER}} .dce-post-title a' => 'color: {{VALUE}};'], 'condition' => ['show_title' => '1']]);
        $this->add_control('hover_color', ['label' => esc_html__('Hover Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .dce-post-title a:hover' => 'color: {{VALUE}};'], 'condition' => ['show_title' => '1']]);
        $this->add_group_control(Group_Control_Typography::get_type(), ['name' => 'typography', 'selector' => '{{WRAPPER}} .dce-post-title', 'condition' => ['show_title' => '1']]);
        $this->add_responsive_control('text_padding', ['label' => esc_html__('Padding', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => ['px', '%'], 'selectors' => ['{{WRAPPER}} .dce-post-title' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'], 'condition' => ['show_title' => '1']]);
        $this->add_responsive_control('title_space', ['label' => esc_html__('Space', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'range' => ['px' => ['max' => 100, 'min' => 0, 'step' => 1]], 'selectors' => ['{{WRAPPER}} .dce-post-title' => 'margin-bottom: {{SIZE}}{{UNIT}};'], 'condition' => ['show_title' => '1']]);
        $this->end_controls_section();
        // --------------------------------------------------------- [ section Style - Type ]
        $this->start_controls_section('section_style_type', ['label' => esc_html__('Type', 'dynamic-content-for-elementor'), 'tab' => Controls_Manager::TAB_STYLE, 'condition' => ['show_type' => '1', 'templatemode_enable' => '', 'native_templatemode_enable' => '']]);
        $this->add_control('type_color', ['label' => esc_html__('Text Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .dce-post-type ' => 'color: {{VALUE}};', '{{WRAPPER}} .dce-post-type a' => 'color: {{VALUE}};']]);
        $this->add_control('type_hover_color', ['label' => esc_html__('Hover Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .dce-post-type a:hover' => 'color: {{VALUE}};']]);
        $this->add_group_control(Group_Control_Typography::get_type(), ['name' => 'type_typography', 'selector' => '{{WRAPPER}} .dce-post-type']);
        $this->add_responsive_control('type_padding', ['label' => esc_html__('Padding', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => ['px', '%'], 'selectors' => ['{{WRAPPER}} .dce-post-type' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};']]);
        $this->add_responsive_control('type_space', ['label' => esc_html__('Space', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'range' => ['px' => ['max' => 100, 'min' => 0, 'step' => 1]], 'selectors' => ['{{WRAPPER}} .dce-post-type' => 'margin-bottom: {{SIZE}}{{UNIT}};']]);
        $this->end_controls_section();
        // ----------------------------------------------- [ section Style - AUTHOR ]
        $this->start_controls_section('section_style_author', ['label' => esc_html__('Author', 'dynamic-content-for-elementor'), 'tab' => Controls_Manager::TAB_STYLE, 'condition' => ['show_author' => '1', 'templatemode_enable' => '', 'native_templatemode_enable' => '']]);
        $this->add_responsive_control('authot_space', ['label' => esc_html__('Space', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'range' => ['px' => ['max' => 100, 'min' => 0, 'step' => 1]], 'selectors' => ['{{WRAPPER}} .dce_author-wrap' => 'padding-top: {{SIZE}}{{UNIT}}; padding-bottom: {{SIZE}}{{UNIT}};']]);
        $this->add_control('author_avatar', ['label' => esc_html__('Avatar image', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HEADING, 'separator' => 'before', 'condition' => ['show_author' => '1', 'enable_author_image' => 'yes']]);
        $this->add_responsive_control('author_avatar_size', ['label' => esc_html__('Size (px)', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'range' => ['px' => ['max' => 150, 'min' => 0, 'step' => 1]], 'selectors' => ['{{WRAPPER}} .dce_author-avatar' => 'width: {{SIZE}}{{UNIT}};'], 'condition' => ['show_author' => '1', 'enable_author_image' => 'yes']]);
        $this->add_control('author_name', ['label' => esc_html__('Name', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HEADING, 'separator' => 'before', 'condition' => ['show_author' => '1']]);
        $this->add_control('color_author', ['label' => esc_html__('Text Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .dce_author-name' => 'color: {{VALUE}};'], 'condition' => ['show_author' => '1']]);
        $this->add_group_control(Group_Control_Typography::get_type(), ['name' => 'typography_author', 'selector' => '{{WRAPPER}} .dce_author-name', 'condition' => ['show_author' => '1']]);
        $this->add_responsive_control('authot_name_space', ['label' => esc_html__('Space', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'range' => ['px' => ['max' => 100, 'min' => 0, 'step' => 1]], 'selectors' => ['{{WRAPPER}} .dce_author-name' => 'padding-bottom: {{SIZE}}{{UNIT}};']]);
        $this->add_control('author_bio', ['label' => esc_html__('Biography', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HEADING, 'separator' => 'before', 'condition' => ['show_author' => '1', 'enable_author_bio' => 'yes']]);
        $this->add_control('color_author_bio', ['label' => esc_html__('Text Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .dce_author-bio' => 'color: {{VALUE}};'], 'condition' => ['show_author' => '1', 'enable_author_bio' => 'yes']]);
        $this->add_group_control(Group_Control_Typography::get_type(), ['name' => 'typography_author_bio', 'selector' => '{{WRAPPER}} .dce_author-bio', 'condition' => ['show_author' => '1', 'enable_author_bio' => 'yes']]);
        $this->add_responsive_control('authot_bio_space', ['label' => esc_html__('Space', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'range' => ['px' => ['max' => 100, 'min' => 0, 'step' => 1]], 'selectors' => ['{{WRAPPER}} .dce_author-bio' => 'padding-bottom: {{SIZE}}{{UNIT}};'], 'condition' => ['show_author' => '1', 'enable_author_bio' => 'yes']]);
        $this->end_controls_section();
        // -------------------------------------------------- [ section Style - UnicDate ]
        $this->start_controls_section('section_style_unicdate', ['label' => esc_html__('Year over the block', 'dynamic-content-for-elementor'), 'tab' => Controls_Manager::TAB_STYLE, 'condition' => ['unic_date' => 'yes']]);
        $this->add_control('color_unicdate', ['label' => esc_html__('Text Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .dce-acfposts_date-year' => 'color: {{VALUE}};'], 'condition' => ['unic_date' => 'yes']]);
        $this->add_group_control(Group_Control_Typography::get_type(), ['name' => 'typography_unicdate', 'selector' => '{{WRAPPER}} .dce-acfposts_date-year', 'condition' => ['unic_date' => 'yes']]);
        $this->end_controls_section();
        // ------------------------------------------------ [ section Style - Image ]
        $this->start_controls_section('section_style_image', ['label' => esc_html__('Image', 'dynamic-content-for-elementor'), 'tab' => Controls_Manager::TAB_STYLE, 'condition' => ['show_image' => '1', 'templatemode_enable' => '', 'native_templatemode_enable' => '']]);
        $this->add_responsive_control('img_space', ['label' => esc_html__('Space', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'size_units' => ['px', '%'], 'range' => ['px' => ['max' => 100, 'min' => -100, 'step' => 1], '%' => ['max' => 100, 'min' => -100, 'step' => 1]], 'selectors' => ['{{WRAPPER}} .dce-acfposts_image' => 'margin-bottom: {{SIZE}}{{UNIT}};'], 'condition' => ['show_image' => '1']]);
        $this->add_control('popover-toggle', ['label' => esc_html__('Transform image', 'dynamic-content-for-elementor'), 'type' => \Elementor\Controls_Manager::POPOVER_TOGGLE, 'label_off' => esc_html__('Default', 'dynamic-content-for-elementor'), 'label_on' => esc_html__('Custom', 'dynamic-content-for-elementor'), 'return_value' => 'yes']);
        $this->start_popover();
        $this->add_group_control(Group_Control_Transform_Element::get_type(), ['name' => 'transform_image', 'label' => 'Transform image', 'selector' => '{{WRAPPER}} .dce-acfposts_image', 'condition' => ['show_image' => '1']]);
        $this->end_popover();
        $this->add_group_control(Group_Control_Filters_CSS::get_type(), ['name' => 'filters_image', 'label' => 'Filters image', 'selector' => '{{WRAPPER}} .dce-acfposts_image .acfposts-image']);
        $this->add_control('blend_mode', ['label' => esc_html__('Blend Mode', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'options' => ['' => esc_html__('Normal', 'dynamic-content-for-elementor'), 'multiply' => esc_html__('Multiply', 'dynamic-content-for-elementor'), 'screen' => esc_html__('Screen', 'dynamic-content-for-elementor'), 'overlay' => esc_html__('Overlay', 'dynamic-content-for-elementor'), 'darken' => esc_html__('Darken', 'dynamic-content-for-elementor'), 'lighten' => esc_html__('Lighten', 'dynamic-content-for-elementor'), 'color-dodge' => esc_html__('Color Dodge', 'dynamic-content-for-elementor'), 'saturation' => esc_html__('Saturation', 'dynamic-content-for-elementor'), 'color' => esc_html__('Color', 'dynamic-content-for-elementor'), 'difference' => esc_html__('Difference', 'dynamic-content-for-elementor'), 'exclusion' => esc_html__('Exclusion', 'dynamic-content-for-elementor'), 'hue' => esc_html__('Hue', 'dynamic-content-for-elementor'), 'luminosity' => esc_html__('Luminosity', 'dynamic-content-for-elementor')], 'selectors' => ['{{WRAPPER}} .dce-acfposts_image' => 'mix-blend-mode: {{VALUE}}'], 'separator' => 'none']);
        $this->add_group_control(Group_Control_Border::get_type(), ['name' => 'border_image', 'label' => esc_html__('Image Border', 'dynamic-content-for-elementor'), 'selector' => '{{WRAPPER}} .acfposts-image', 'condition' => ['show_image' => '1']]);
        $this->add_control('border_radius_image', ['label' => esc_html__('Border Radius', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => ['px', '%'], 'selectors' => ['{{WRAPPER}} .acfposts-image, {{WRAPPER}} .dce-overlay_hover, {{WRAPPER}} .dce-overlay' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'], 'condition' => ['show_image' => '1']]);
        $this->add_control('padding_image', ['label' => esc_html__('Padding', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => ['px', '%'], 'selectors' => ['{{WRAPPER}} .acfposts-image, {{WRAPPER}} .dce-overlay_hover, {{WRAPPER}} .dce-overlay' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'], 'condition' => ['show_image' => '1']]);
        $this->add_group_control(Group_Control_Box_Shadow::get_type(), ['name' => 'box_shadow_image', 'selector' => '{{WRAPPER}} .acfposts-image', 'condition' => ['show_image' => '1']]);
        $this->end_controls_section();
        // ---------------------------------------------- [ section Style - Content ]
        $this->start_controls_section('section_style_content', ['label' => esc_html__('Content', 'dynamic-content-for-elementor'), 'tab' => Controls_Manager::TAB_STYLE, 'condition' => ['show_textcontent!' => '0', 'templatemode_enable' => '', 'native_templatemode_enable' => '']]);
        $this->add_control('content_color', ['label' => esc_html__('Text Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .dce_textcontent' => 'color: {{VALUE}};'], 'condition' => ['show_textcontent!' => '0']]);
        $this->add_control('content_bgcolor', ['label' => esc_html__('Background Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .dce_textcontent' => 'background-color: {{VALUE}};', '{{WRAPPER}} .dce_textcontent:after' => 'border-bottom-color: {{VALUE}}; border-top-color: {{VALUE}};'], 'condition' => ['show_textcontent!' => '0']]);
        $this->add_group_control(Group_Control_Typography::get_type(), ['name' => 'typography_content', 'selector' => '{{WRAPPER}} .dce_textcontent', 'condition' => ['show_textcontent!' => '0']]);
        $this->add_responsive_control('content_padding', ['label' => esc_html__('Padding', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => ['px', '%', 'rem'], 'selectors' => ['{{WRAPPER}} .dce_textcontent' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'], 'condition' => ['show_textcontent!' => '0']]);
        $this->add_responsive_control('content_margin', ['label' => esc_html__('Margin', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => ['px', '%'], 'selectors' => ['{{WRAPPER}} .dce_textcontent' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'], 'condition' => ['show_textcontent!' => '0']]);
        $this->add_control('content_border_radius', ['label' => esc_html__('Border Radius', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => ['px', '%'], 'selectors' => ['{{WRAPPER}} .dce_textcontent' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'], 'condition' => ['show_textcontent!' => '0']]);
        $this->end_controls_section();
        // ------------------------------------------- [ section Style - Read More Button ]
        $this->start_controls_section('section_style_readmore_button', ['label' => esc_html__('Read More Button', 'dynamic-content-for-elementor'), 'tab' => Controls_Manager::TAB_STYLE, 'condition' => ['show_readmore' => '1', 'templatemode_enable' => '', 'native_templatemode_enable' => '']]);
        $this->start_controls_tabs('readmore_colors');
        $this->start_controls_tab('readmore_colors_normal', ['label' => esc_html__('Normal', 'dynamic-content-for-elementor'), 'condition' => ['show_readmore' => '1']]);
        $this->add_control('readmore_color', ['label' => esc_html__('Text Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .dce_readmore_btn' => 'color: {{VALUE}};'], 'condition' => ['show_readmore' => '1']]);
        $this->add_control('readmore_bgcolor', ['label' => esc_html__('Background Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .dce_readmore_btn' => 'background-color: {{VALUE}};'], 'condition' => ['show_readmore' => '1']]);
        $this->add_group_control(Group_Control_Border::get_type(), ['name' => 'readmore_border', 'label' => esc_html__('Border', 'dynamic-content-for-elementor'), 'selector' => '{{WRAPPER}}  .dce_readmore_btn']);
        $this->end_controls_tab();
        $this->start_controls_tab('readmore_colors_hover', ['label' => esc_html__('Hover', 'dynamic-content-for-elementor'), 'condition' => ['show_readmore' => '1']]);
        $this->add_control('readmore_color_hover', ['label' => esc_html__('Text Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .dce_readmore_btn:hover' => 'color: {{VALUE}};'], 'condition' => ['show_readmore' => '1']]);
        $this->add_control('readmore_bgcolor_hover', ['label' => esc_html__('Background Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .dce_readmore_btn:hover' => 'background-color: {{VALUE}};'], 'condition' => ['show_readmore' => '1']]);
        $this->add_control('readmore_hover_border_color', ['label' => esc_html__('Border Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'condition' => ['show_readmore' => '1', 'readmore_border_border!' => ''], 'selectors' => ['{{WRAPPER}} .dce_readmore_btn:hover' => 'border-color: {{VALUE}};']]);
        $this->end_controls_tab();
        $this->end_controls_tabs();
        $this->add_group_control(Group_Control_Typography::get_type(), ['name' => 'typography_readmore', 'selector' => '{{WRAPPER}} .dce_readmore_btn', 'condition' => ['show_readmore' => '1']]);
        $this->add_responsive_control('readmore_align', ['label' => esc_html__('Alignment', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CHOOSE, 'toggle' => \true, 'options' => ['left' => ['title' => esc_html__('Left', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-left'], 'center' => ['title' => esc_html__('Center', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-center'], 'right' => ['title' => esc_html__('Right', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-right']], 'default' => 'left', 'selectors' => ['{{WRAPPER}} .dce_readmore_wrapper' => 'text-align: {{VALUE}};']]);
        $this->add_responsive_control('readmore_padding', ['label' => esc_html__('Padding', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => ['px', '%'], 'selectors' => ['{{WRAPPER}} .dce_readmore_btn' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'], 'condition' => ['show_readmore' => '1']]);
        $this->add_responsive_control('readmore_margin', ['label' => esc_html__('Margin', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => ['px', '%'], 'selectors' => ['{{WRAPPER}} .dce_readmore_wrapper' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'], 'condition' => ['show_readmore' => '1']]);
        $this->add_control('readmore_border_radius', ['label' => esc_html__('Border Radius', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => ['px', '%'], 'selectors' => ['{{WRAPPER}} .dce_readmore_btn' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'], 'condition' => ['show_readmore' => '1']]);
        $this->add_group_control(Group_Control_Box_Shadow::get_type(), ['name' => 'box_shadow_readmore', 'selector' => '{{WRAPPER}} .dce_readmore_btn', 'condition' => ['show_readmore' => '1']]);
        $this->end_controls_section();
        // ----------------------------------------------------- [ section Style - Metadata ]
        $this->start_controls_section('section_style_metadata', ['label' => esc_html__('Meta Data', 'dynamic-content-for-elementor'), 'tab' => Controls_Manager::TAB_STYLE, 'condition' => ['show_metadata' => '1', 'templatemode_enable' => '', 'native_templatemode_enable' => '']]);
        $this->add_control('metadata_color', ['label' => esc_html__('Text Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .dce_metadata-wrap, {{WRAPPER}} .dce_metadata-wrap a' => 'color: {{VALUE}};']]);
        $this->add_control('metadata_color_hover', ['label' => esc_html__('Text Color Hover', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .dce_metadata a:hover' => 'color: {{VALUE}};']]);
        $this->add_control('metadata_color_separator', ['label' => esc_html__('Separator Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .dce_metadata-wrap .dce_metadata-separator' => 'color: {{VALUE}};']]);
        $this->add_group_control(Group_Control_Typography::get_type(), ['name' => 'typography_metadata', 'selector' => '{{WRAPPER}} .dce_metadata']);
        $this->add_responsive_control('metadata_align', ['label' => esc_html__('Metadata Alignment', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CHOOSE, 'toggle' => \false, 'options' => ['left' => ['title' => esc_html__('Left', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-left'], 'center' => ['title' => esc_html__('Center', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-center'], 'right' => ['title' => esc_html__('Right', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-right']], 'default' => '', 'selectors' => ['{{WRAPPER}} .dce_metadata-wrap' => 'text-align: {{VALUE}};']]);
        $this->add_control('metadata_padding', ['label' => esc_html__('Items Padding', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => ['px', '%'], 'selectors' => ['{{WRAPPER}} .dce_metadata' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};']]);
        $this->add_responsive_control('metadata_space', ['label' => esc_html__('Space', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'size_units' => ['px', '%'], 'range' => ['px' => ['max' => 100, 'min' => -100, 'step' => 1], '%' => ['max' => 100, 'min' => -100, 'step' => 1]], 'selectors' => ['{{WRAPPER}} .dce_metadata-wrap' => 'margin-bottom: {{SIZE}}{{UNIT}};']]);
        $this->end_controls_section();
        // ---------------------------------------- [ section Style - Date ]
        $this->start_controls_section('section_style_date', ['label' => esc_html__('Date', 'dynamic-content-for-elementor'), 'tab' => Controls_Manager::TAB_STYLE, 'condition' => ['show_date' => '1', 'templatemode_enable' => '', 'native_templatemode_enable' => '']]);
        $this->add_responsive_control('date_align', ['label' => esc_html__('Metadata Alignment', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CHOOSE, 'toggle' => \false, 'options' => ['left' => ['title' => esc_html__('Left', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-left'], 'center' => ['title' => esc_html__('Center', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-center'], 'right' => ['title' => esc_html__('Right', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-right']], 'default' => '', 'selectors' => ['{{WRAPPER}} .dce-post-date' => 'text-align: {{VALUE}};']]);
        $this->add_control('date_color', ['label' => esc_html__('Date Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .dce-post-date ' => 'color: {{VALUE}};', '{{WRAPPER}} .dce-post-date a' => 'color: {{VALUE}};'], 'condition' => ['show_date' => '1']]);
        $this->add_group_control(Group_Control_Typography::get_type(), ['name' => 'date_typography', 'label' => 'Date Typography', 'selector' => '{{WRAPPER}} .dce-post-date', 'condition' => ['show_date' => '1']]);
        $this->add_control('date_padding', ['label' => esc_html__('Date Padding', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => ['px', '%'], 'selectors' => ['{{WRAPPER}} .dce-post-date' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'], 'condition' => ['show_date' => '1']]);
        /* Date 2 */
        $this->add_control('date2_color', ['label' => esc_html__('Date2 Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .dce-post-date .d2 ' => 'color: {{VALUE}};', '{{WRAPPER}} .dce-post-date .d2 a' => 'color: {{VALUE}};'], 'condition' => ['show_date' => '1', 'date_format_2!' => '']]);
        $this->add_group_control(Group_Control_Typography::get_type(), ['name' => 'date2_typography', 'label' => 'Date2 Typography', 'selector' => '{{WRAPPER}} .dce-post-date .d2', 'condition' => ['show_date' => '1', 'date_format_2!' => '']]);
        $this->add_control('date2_padding', ['label' => esc_html__('Date2 Padding', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => ['px', '%'], 'selectors' => ['{{WRAPPER}} .dce-post-date .d2' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'], 'condition' => ['show_date' => '1', 'date_format_2!' => '']]);
        /* Date 3 */
        $this->add_control('date3_color', ['label' => esc_html__('Date3 Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .dce-post-date .d3 ' => 'color: {{VALUE}};', '{{WRAPPER}} .dce-post-date .d3 a' => 'color: {{VALUE}};'], 'condition' => ['show_date' => '1', 'date_format_3!' => '']]);
        $this->add_group_control(Group_Control_Typography::get_type(), ['name' => 'date3_typography', 'label' => 'Date3 Typography', 'selector' => '{{WRAPPER}} .dce-post-date .d3', 'condition' => ['show_date' => '1', 'date_format_3!' => '']]);
        $this->add_control('date3_padding', ['label' => esc_html__('Date3 Padding', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => ['px', '%'], 'selectors' => ['{{WRAPPER}} .dce-post-date .d3' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'], 'condition' => ['show_date' => '1', 'date_format_3!' => '']]);
        $this->end_controls_section();
        // -------------------------------------------------------------------------- [ section Style - InfiniteScroll ]
        $this->start_controls_section('section_style_infiniteScroll', ['label' => esc_html__('Infinite Scroll', 'dynamic-content-for-elementor'), 'tab' => Controls_Manager::TAB_STYLE, 'condition' => ['infiniteScroll_enable' => 'yes']]);
        $this->add_responsive_control('infiniteScroll_spacing', ['label' => esc_html__('Spacing status', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => 1], 'range' => ['px' => ['max' => 100, 'min' => 0, 'step' => 1]], 'selectors' => ['{{WRAPPER}} .infiniteScroll' => 'margin-top: {{SIZE}}{{UNIT}};']]);
        $this->add_control('infiniteScroll_heading_button_style', ['label' => esc_html__('Button', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HEADING, 'separator' => 'before', 'condition' => ['infiniteScroll_trigger' => 'button']]);
        $this->add_responsive_control('infiniteScroll_button_align', ['label' => esc_html__('Alignment', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CHOOSE, 'toggle' => \false, 'options' => ['flex-start' => ['title' => esc_html__('Left', 'dynamic-content-for-elementor'), 'icon' => 'eicon-h-align-left'], 'center' => ['title' => esc_html__('Center', 'dynamic-content-for-elementor'), 'icon' => 'eicon-h-align-center'], 'flex-end' => ['title' => esc_html__('Right', 'dynamic-content-for-elementor'), 'icon' => 'eicon-h-align-right']], 'default' => 'center', 'selectors' => ['{{WRAPPER}} div.infiniteScroll' => 'justify-content: {{VALUE}};'], 'condition' => ['infiniteScroll_trigger' => 'button']]);
        $this->start_controls_tabs('infiniteScroll_button_colors');
        $this->start_controls_tab('infiniteScroll_button_text_colors', ['label' => esc_html__('Normal', 'dynamic-content-for-elementor'), 'condition' => ['infiniteScroll_trigger' => 'button']]);
        $this->add_control('infiniteScroll_button_text_color', ['label' => esc_html__('Text Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'default' => '', 'selectors' => ['{{WRAPPER}} .infiniteScroll button' => 'color: {{VALUE}};'], 'condition' => ['infiniteScroll_trigger' => 'button']]);
        $this->add_control('infiniteScroll_button_background_color', ['label' => esc_html__('Background Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'default' => '', 'selectors' => ['{{WRAPPER}} .infiniteScroll button' => 'background-color: {{VALUE}};'], 'condition' => ['infiniteScroll_trigger' => 'button']]);
        $this->end_controls_tab();
        $this->start_controls_tab('infiniteScroll_button_text_colors_hover', ['label' => esc_html__('Hover', 'dynamic-content-for-elementor'), 'condition' => ['infiniteScroll_trigger' => 'button']]);
        $this->add_control('infiniteScroll_button_hover_color', ['label' => esc_html__('Text Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .infiniteScroll button:hover' => 'color: {{VALUE}};'], 'condition' => ['infiniteScroll_trigger' => 'button']]);
        $this->add_control('infiniteScroll_button_background_hover_color', ['label' => esc_html__('Background Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .infiniteScroll button:hover' => 'background-color: {{VALUE}};'], 'condition' => ['infiniteScroll_trigger' => 'button']]);
        $this->add_control('infiniteScroll_button_hover_border_color', ['label' => esc_html__('Border Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .infiniteScroll button:hover' => 'border-color: {{VALUE}};'], 'condition' => ['infiniteScroll_trigger' => 'button']]);
        $this->end_controls_tab();
        $this->end_controls_tabs();
        $this->add_control('infiniteScroll_button_padding', ['label' => esc_html__('Padding', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => ['px', 'em', '%'], 'selectors' => ['{{WRAPPER}} .infiniteScroll button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'], 'condition' => ['infiniteScroll_trigger' => 'button'], 'separator' => 'before']);
        $this->add_control('infiniteScroll_button_radius', ['label' => esc_html__('Border Radius', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => ['px', '%', 'em'], 'selectors' => ['{{WRAPPER}} .infiniteScroll button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'], 'condition' => ['infiniteScroll_trigger' => 'button']]);
        $this->end_controls_section();
        // -------------------------------------------------------------------------- [ section Style - Pagination ]
        $this->start_controls_section('section_style_pagination', ['label' => esc_html__('Pagination', 'dynamic-content-for-elementor'), 'tab' => Controls_Manager::TAB_STYLE, 'condition' => ['pagination_enable' => 'yes']]);
        $this->add_control('pagination_heading_style', ['label' => esc_html__('Pagination', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HEADING, 'separator' => 'before']);
        $this->add_responsive_control('pagination_align', ['label' => esc_html__('Alignment', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CHOOSE, 'toggle' => \false, 'options' => ['flex-start' => ['title' => esc_html__('Left', 'dynamic-content-for-elementor'), 'icon' => 'eicon-h-align-left'], 'center' => ['title' => esc_html__('Center', 'dynamic-content-for-elementor'), 'icon' => 'eicon-h-align-center'], 'flex-end' => ['title' => esc_html__('Right', 'dynamic-content-for-elementor'), 'icon' => 'eicon-h-align-right']], 'default' => 'center', 'selectors' => ['{{WRAPPER}} .dce-pagination' => 'justify-content: {{VALUE}};']]);
        $this->add_group_control(Group_Control_Typography::get_type(), ['name' => 'pagination_typography', 'label' => esc_html__('Typography', 'dynamic-content-for-elementor'), 'selector' => '{{WRAPPER}} .dce-pagination']);
        $this->add_responsive_control('pagination_space', ['label' => esc_html__('Space', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => 10], 'range' => ['px' => ['max' => 100, 'min' => 0, 'step' => 1]], 'selectors' => ['{{WRAPPER}} .dce-pagination' => 'padding-top: {{SIZE}}{{UNIT}};']]);
        $this->add_responsive_control('pagination_spacing', ['label' => esc_html__('Horizontal Spacing', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => 1], 'range' => ['px' => ['max' => 100, 'min' => 0, 'step' => 1]], 'selectors' => ['{{WRAPPER}} .dce-pagination span, {{WRAPPER}} .dce-pagination a' => 'margin-right: {{SIZE}}{{UNIT}};']]);
        $this->add_control('pagination_padding', ['label' => esc_html__('Padding', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => ['px', 'em', '%'], 'selectors' => ['{{WRAPPER}} .dce-pagination span, {{WRAPPER}} .dce-pagination a' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};']]);
        $this->add_control('pagination_radius', ['label' => esc_html__('Border Radius', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => ['px', '%', 'em'], 'selectors' => ['{{WRAPPER}} .dce-pagination span, {{WRAPPER}} .dce-pagination a' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};']]);
        $this->add_control('pagination_heading_colors', ['label' => esc_html__('Colors', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HEADING, 'separator' => 'before']);
        $this->start_controls_tabs('pagination_colors');
        $this->start_controls_tab('pagination_text_colors', ['label' => esc_html__('Normal', 'dynamic-content-for-elementor')]);
        $this->add_control('pagination_text_color', ['label' => esc_html__('Text Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'default' => '', 'selectors' => ['{{WRAPPER}} .dce-pagination span, {{WRAPPER}} .dce-pagination a' => 'color: {{VALUE}};']]);
        $this->add_control('pagination_background_color', ['label' => esc_html__('Background Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .dce-pagination span, {{WRAPPER}} .dce-pagination a' => 'background-color: {{VALUE}};']]);
        $this->add_group_control(Group_Control_Border::get_type(), ['name' => 'pagination_border', 'label' => esc_html__('Border', 'dynamic-content-for-elementor'), 'selector' => '{{WRAPPER}} .dce-pagination span, {{WRAPPER}} .dce-pagination a']);
        $this->end_controls_tab();
        $this->start_controls_tab('pagination_text_colors_hover', ['label' => esc_html__('Hover', 'dynamic-content-for-elementor')]);
        $this->add_control('pagination_hover_color', ['label' => esc_html__('Text Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .dce-pagination a:hover' => 'color: {{VALUE}};']]);
        $this->add_control('pagination_background_hover_color', ['label' => esc_html__('Background Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .dce-pagination a:hover' => 'background-color: {{VALUE}};']]);
        $this->add_control('pagination_hover_border_color', ['label' => esc_html__('Border Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'condition' => ['pagination_border_border!' => ''], 'selectors' => ['{{WRAPPER}} .dce-pagination a:hover' => 'border-color: {{VALUE}};']]);
        $this->end_controls_tab();
        $this->start_controls_tab('pagination_text_colors_current', ['label' => esc_html__('Current', 'dynamic-content-for-elementor')]);
        $this->add_control('pagination_current_color', ['label' => esc_html__('Text Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .dce-pagination span.current' => 'color: {{VALUE}};']]);
        $this->add_control('pagination_background_current_color', ['label' => esc_html__('Background Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .dce-pagination span.current' => 'background-color: {{VALUE}};']]);
        $this->add_control('pagination_current_border_color', ['label' => esc_html__('Border Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'condition' => ['pagination_border_border!' => ''], 'selectors' => ['{{WRAPPER}} .dce-pagination span.current' => 'border-color: {{VALUE}};']]);
        $this->end_controls_tab();
        $this->end_controls_tabs();
        // PrevNext
        $this->add_control('pagination_heading_prevnext', ['label' => esc_html__('Prev/Next', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HEADING, 'separator' => 'before', 'condition' => ['pagination_show_prevnext' => 'yes']]);
        $this->add_responsive_control('pagination_spacing_prevnext', ['label' => esc_html__('Spacing', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => ''], 'range' => ['px' => ['max' => 100, 'min' => 0, 'step' => 1]], 'selectors' => ['{{WRAPPER}} .dce-pagination .pageprev' => 'margin-right: {{SIZE}}{{UNIT}};', '{{WRAPPER}} .dce-pagination .pagenext' => 'margin-left: {{SIZE}}{{UNIT}};'], 'condition' => ['pagination_show_prevnext' => 'yes']]);
        $this->add_responsive_control('pagination_icon_spacing_prevnext', ['label' => esc_html__('Icon Spacing', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => ''], 'range' => ['px' => ['max' => 50, 'min' => 0, 'step' => 1]], 'selectors' => ['{{WRAPPER}} .dce-pagination .pageprev .fa' => 'margin-right: {{SIZE}}{{UNIT}};', '{{WRAPPER}} .dce-pagination .pagenext .fa' => 'margin-left: {{SIZE}}{{UNIT}};'], 'condition' => ['pagination_show_prevnext' => 'yes']]);
        $this->add_responsive_control('pagination_icon_size_prevnext', ['label' => esc_html__('Icon Size', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => ''], 'range' => ['px' => ['max' => 100, 'min' => 0, 'step' => 1]], 'selectors' => ['{{WRAPPER}} .dce-pagination .pageprev .fa' => 'font-size: {{SIZE}}{{UNIT}};', '{{WRAPPER}} .dce-pagination .pagenext .fa' => 'font-size: {{SIZE}}{{UNIT}};'], 'condition' => ['pagination_show_prevnext' => 'yes']]);
        $this->start_controls_tabs('pagination_prevnext_colors');
        $this->start_controls_tab('pagination_prevnext_text_colors', ['label' => esc_html__('Normal', 'dynamic-content-for-elementor'), 'condition' => ['pagination_show_prevnext' => 'yes']]);
        $this->add_control('pagination_prevnext_text_color', ['label' => esc_html__('Text Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'default' => '', 'selectors' => ['{{WRAPPER}} .dce-pagination .pageprev, {{WRAPPER}} .dce-pagination .pagenext' => 'color: {{VALUE}};'], 'condition' => ['pagination_show_prevnext' => 'yes']]);
        $this->add_control('pagination_prevnext_background_color', ['label' => esc_html__('Background Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'default' => '', 'selectors' => ['{{WRAPPER}} .dce-pagination .pageprev, {{WRAPPER}} .dce-pagination .pagenext' => 'background-color: {{VALUE}};'], 'condition' => ['pagination_show_prevnext' => 'yes']]);
        $this->add_group_control(Group_Control_Border::get_type(), ['name' => 'pagination_prevnext_border', 'label' => esc_html__('Border', 'dynamic-content-for-elementor'), 'selector' => '{{WRAPPER}} .dce-pagination .pageprev, {{WRAPPER}} .dce-pagination .pagenext', 'condition' => ['pagination_show_prevnext' => 'yes']]);
        $this->add_control('pagination_prevnext_radius', ['label' => esc_html__('Border Radius', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => ['px', '%', 'em'], 'selectors' => ['{{WRAPPER}} .dce-pagination .pageprev, {{WRAPPER}} .dce-pagination .pagenext' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'], 'condition' => ['pagination_show_prevnext' => 'yes']]);
        $this->end_controls_tab();
        $this->start_controls_tab('pagination_prevnext_text_colors_hover', ['label' => esc_html__('Hover', 'dynamic-content-for-elementor'), 'condition' => ['pagination_show_prevnext' => 'yes']]);
        $this->add_control('pagination_prevnext_hover_color', ['label' => esc_html__('Text Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .dce-pagination .pageprev:hover, {{WRAPPER}} .dce-pagination .pagenext:hover' => 'color: {{VALUE}};'], 'condition' => ['pagination_show_prevnext' => 'yes']]);
        $this->add_control('pagination_prevnext_background_hover_color', ['label' => esc_html__('Background Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .dce-pagination .pageprev:hover, {{WRAPPER}} .dce-pagination .pagenext:hover' => 'background-color: {{VALUE}};'], 'condition' => ['pagination_show_prevnext' => 'yes']]);
        $this->add_control('pagination_prevnext_hover_border_color', ['label' => esc_html__('Border Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .dce-pagination .pageprev:hover, {{WRAPPER}} .dce-pagination .pagenext:hover' => 'border-color: {{VALUE}};'], 'condition' => ['pagination_show_prevnext' => 'yes', 'pagination_prevnext_border_border!' => '']]);
        $this->end_controls_tab();
        $this->end_controls_tabs();
        // FirstLast
        $this->add_control('pagination_heading_firstlast', ['label' => esc_html__('First/last', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HEADING, 'separator' => 'before', 'condition' => ['pagination_show_firstlast' => 'yes']]);
        $this->add_responsive_control('pagination_spacing_firstlast', ['label' => esc_html__('Spacing', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => ''], 'range' => ['px' => ['max' => 100, 'min' => 0, 'step' => 1]], 'selectors' => ['{{WRAPPER}} .dce-pagination .pagefirst' => 'margin-right: {{SIZE}}{{UNIT}};', '{{WRAPPER}} .dce-pagination .pagelast' => 'margin-left: {{SIZE}}{{UNIT}};'], 'condition' => ['pagination_show_firstlast' => 'yes']]);
        $this->start_controls_tabs('pagination_firstlast_colors');
        $this->start_controls_tab('pagination_firstlast_text_colors', ['label' => esc_html__('Normal', 'dynamic-content-for-elementor'), 'condition' => ['pagination_show_firstlast' => 'yes']]);
        $this->add_control('pagination_firstlast_text_color', ['label' => esc_html__('Text Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'default' => '', 'selectors' => ['{{WRAPPER}} .dce-pagination .pagefirst, {{WRAPPER}} .dce-pagination .pagelast' => 'color: {{VALUE}};'], 'condition' => ['pagination_show_firstlast' => 'yes']]);
        $this->add_control('pagination_firstlast_background_color', ['label' => esc_html__('Background Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'default' => '', 'selectors' => ['{{WRAPPER}} .dce-pagination .pagefirst, {{WRAPPER}} .dce-pagination .pagelast' => 'background-color: {{VALUE}};'], 'condition' => ['pagination_show_firstlast' => 'yes']]);
        $this->add_group_control(Group_Control_Border::get_type(), ['name' => 'pagination_firstlast_border', 'label' => esc_html__('Border', 'dynamic-content-for-elementor'), 'selector' => '{{WRAPPER}} .dce-pagination .pagefirst, {{WRAPPER}} .dce-pagination .pagelast', 'condition' => ['pagination_show_firstlast' => 'yes']]);
        $this->add_control('pagination_firstlast_radius', ['label' => esc_html__('Border Radius', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => ['px', '%', 'em'], 'selectors' => ['{{WRAPPER}} .dce-pagination .pagefirst, {{WRAPPER}} .dce-pagination .pagelast' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'], 'condition' => ['pagination_show_firstlast' => 'yes']]);
        $this->end_controls_tab();
        $this->start_controls_tab('pagination_firstlast_text_colors_hover', ['label' => esc_html__('Hover', 'dynamic-content-for-elementor'), 'condition' => ['pagination_show_firstlast' => 'yes']]);
        $this->add_control('pagination_firstlast_hover_color', ['label' => esc_html__('Text Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .dce-pagination .pagefirst:hover, {{WRAPPER}} .dce-pagination .pagelast:hover' => 'color: {{VALUE}};'], 'condition' => ['pagination_show_firstlast' => 'yes']]);
        $this->add_control('pagination_firstlast_background_hover_color', ['label' => esc_html__('Background Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .dce-pagination .pagefirst:hover, {{WRAPPER}} .dce-pagination .pagelast:hover' => 'background-color: {{VALUE}};'], 'condition' => ['pagination_show_firstlast' => 'yes']]);
        $this->add_control('pagination_firstlast_hover_border_color', ['label' => esc_html__('Border Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .dce-pagination .pagefirst:hover, {{WRAPPER}} .dce-pagination .pagelast:hover' => 'border-color: {{VALUE}};'], 'condition' => ['pagination_show_firstlast' => 'yes', 'pagination_firstlast_border_border!' => '']]);
        $this->end_controls_tab();
        $this->end_controls_tabs();
        // Progression
        $this->add_control('pagination_heading_progression', ['label' => esc_html__('Progression', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HEADING, 'separator' => 'before', 'condition' => ['pagination_show_progression' => 'yes']]);
        $this->add_responsive_control('pagination_spacing_progression', ['label' => esc_html__('Spacing', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => ''], 'range' => ['px' => ['max' => 100, 'min' => 0, 'step' => 1]], 'selectors' => ['{{WRAPPER}} .dce-pagination .progression' => 'margin-right: {{SIZE}}{{UNIT}};'], 'condition' => ['pagination_show_progression' => 'yes']]);
        $this->start_controls_tabs('pagination_progression_colors');
        $this->start_controls_tab('pagination_progression_text_colors', ['label' => esc_html__('Normal', 'dynamic-content-for-elementor'), 'condition' => ['pagination_show_progression' => 'yes']]);
        $this->add_control('pagination_progression_text_color', ['label' => esc_html__('Text Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'default' => '', 'selectors' => ['{{WRAPPER}} .dce-pagination .progression' => 'color: {{VALUE}};'], 'condition' => ['pagination_show_progression' => 'yes']]);
        $this->add_control('pagination_progression_background_color', ['label' => esc_html__('Background Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'default' => '', 'selectors' => ['{{WRAPPER}} .dce-pagination .progression' => 'background-color: {{VALUE}};'], 'condition' => ['pagination_show_progression' => 'yes']]);
        $this->add_group_control(Group_Control_Border::get_type(), ['name' => 'pagination_progression_border', 'label' => esc_html__('Border', 'dynamic-content-for-elementor'), 'selector' => '{{WRAPPER}} .dce-pagination .progression', 'condition' => ['pagination_show_progression' => 'yes']]);
        $this->add_control('pagination_progression_radius', ['label' => esc_html__('Border Radius', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => ['px', '%', 'em'], 'selectors' => ['{{WRAPPER}} .dce-pagination .progression' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'], 'condition' => ['pagination_show_progression' => 'yes']]);
        $this->end_controls_tab();
        $this->start_controls_tab('pagination_progression_text_colors_hover', ['label' => esc_html__('Hover', 'dynamic-content-for-elementor'), 'condition' => ['pagination_show_progression' => 'yes']]);
        $this->add_control('pagination_progression_hover_color', ['label' => esc_html__('Text Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .dce-pagination .progression' => 'color: {{VALUE}};'], 'condition' => ['pagination_show_progression' => 'yes']]);
        $this->add_control('pagination_progression_background_hover_color', ['label' => esc_html__('Background Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .dce-pagination .progression' => 'background-color: {{VALUE}};'], 'condition' => ['pagination_show_progression' => 'yes']]);
        $this->add_control('pagination_progression_hover_border_color', ['label' => esc_html__('Border Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .dce-pagination .progression' => 'border-color: {{VALUE}};'], 'condition' => ['pagination_show_progression' => 'yes', 'pagination_firstlast_border_border!' => '']]);
        $this->end_controls_tab();
        $this->end_controls_tabs();
        $this->end_controls_section();
        // -------------------------------------------------------------------------- [ section Style - Carosello/Slider ]
        $this->start_controls_section('section_style_carousel', ['label' => esc_html__('Carousel', 'dynamic-content-for-elementor'), 'tab' => Controls_Manager::TAB_STYLE, 'condition' => ['posts_style' => ['carousel', 'dualslider']]]);
        $this->add_control('carousel_arrows_options', ['label' => esc_html__('Arrows', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HEADING, 'separator' => 'before']);
        $this->start_controls_tabs('carousel_arrows_colors');
        $this->start_controls_tab('carousel_arrows_colors_normal', ['label' => esc_html__('Normal', 'dynamic-content-for-elementor')]);
        $this->add_control('arrows_color', ['label' => esc_html__('Arrows Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .slick-arrow .fa' => 'color: {{VALUE}};']]);
        $this->add_control('arrows_bgcolor', ['label' => esc_html__('Arrows Background Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .slick-arrow' => 'background-color: {{VALUE}};']]);
        $this->end_controls_tab();
        $this->start_controls_tab('carousel_arrows_colors_hover', ['label' => esc_html__('Hover', 'dynamic-content-for-elementor')]);
        $this->add_control('arrows_color_hover', ['label' => esc_html__('Arrows Color Hover', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .slick-arrow:hover .fa' => 'color: {{VALUE}};']]);
        $this->add_control('arrows_bgcolor_hover', ['label' => esc_html__('Arrows Background Color Hover', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .slick-arrow:hover' => 'background-color: {{VALUE}};']]);
        $this->end_controls_tab();
        $this->end_controls_tabs();
        $this->add_responsive_control('arrows_size', ['label' => esc_html__('Arrows size', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => 30], 'range' => ['px' => ['max' => 100, 'min' => 10, 'step' => 1]], 'selectors' => ['{{WRAPPER}} .slick-arrow .fa' => 'font-size: {{SIZE}}{{UNIT}};', '{{WRAPPER}}.arrows-acfposts-position-outside' => 'padding-left: {{SIZE}}{{UNIT}}; padding-right: {{SIZE}}{{UNIT}};', '{{WRAPPER}}.arrows-acfposts-position-outside .slick-prev' => 'left: -{{SIZE}}{{UNIT}};', '{{WRAPPER}}.arrows-acfposts-position-outside .slick-next' => 'right: -{{SIZE}}{{UNIT}};']]);
        $this->add_responsive_control('arrows_topspace', ['label' => esc_html__('Vertical Shift Arrows', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => '', 'unit' => '%'], 'tablet_default' => ['unit' => '%'], 'mobile_default' => ['unit' => '%'], 'size_units' => ['px', '%'], 'range' => ['%' => ['min' => -120, 'max' => 120], 'px' => ['min' => -120, 'max' => 120]], 'selectors' => ['{{WRAPPER}} .slick-arrow' => 'bottom: {{SIZE}}{{UNIT}};']]);
        $this->add_responsive_control('arrows_horizontalspace', ['label' => esc_html__('Horizontal Shift Arrows', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => '', 'unit' => '%'], 'tablet_default' => ['unit' => '%'], 'mobile_default' => ['unit' => '%'], 'size_units' => ['px', '%'], 'range' => ['%' => ['min' => -100, 'max' => 100], 'px' => ['min' => -100, 'max' => 100]], 'selectors' => ['{{WRAPPER}} .slick-prev:not(.slick-disabled)' => 'left: {{SIZE}}{{UNIT}};', '{{WRAPPER}} .slick-next:not(.slick-disabled)' => 'right: {{SIZE}}{{UNIT}};']]);
        $this->add_control('arrows_padding', ['label' => esc_html__('Padding', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => ['px', '%'], 'selectors' => ['{{WRAPPER}} .slick-arrow' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};']]);
        $this->add_control('arrows_border_radius', ['label' => esc_html__('Border Radius', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => ['px', '%'], 'selectors' => ['{{WRAPPER}} .slick-arrow' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};']]);
        $this->add_control('carousel_dots_options', ['label' => esc_html__('Dots', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HEADING, 'separator' => 'before', 'condition' => ['carousel_dots_enable' => 'yes']]);
        $this->start_controls_tabs('carousel_dots_colors');
        $this->start_controls_tab('carousel_dots_colors_normal', ['label' => esc_html__('Normal', 'dynamic-content-for-elementor'), 'condition' => ['carousel_dots_enable' => 'yes']]);
        $this->add_control('dots_color', ['label' => esc_html__('Dots Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .slick-dots li button:before' => 'background-color: {{VALUE}};'], 'condition' => ['carousel_dots_enable' => 'yes']]);
        $this->end_controls_tab();
        $this->start_controls_tab('carousel_dots_colors_hover', ['label' => esc_html__('Hover', 'dynamic-content-for-elementor'), 'condition' => ['carousel_dots_enable' => 'yes']]);
        $this->add_control('dots_color_hover', ['label' => esc_html__('Dots Color Hover', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .slick-dots li button:hover:before' => 'background-color: {{VALUE}};'], 'condition' => ['carousel_dots_enable' => 'yes']]);
        $this->end_controls_tab();
        $this->start_controls_tab('carousel_dots_colors_sctive', ['label' => esc_html__('Active', 'dynamic-content-for-elementor'), 'condition' => ['carousel_dots_enable' => 'yes']]);
        $this->add_control('dots_color_active', ['label' => esc_html__('Dots Color Active', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .slick-dots li.slick-active button:before' => 'background-color: {{VALUE}};'], 'condition' => ['carousel_dots_enable' => 'yes']]);
        $this->end_controls_tab();
        $this->end_controls_tabs();
        $this->add_responsive_control('dots_size', ['label' => esc_html__('Dots size', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => 10], 'range' => ['px' => ['max' => 50, 'min' => 2, 'step' => 1]], 'selectors' => ['{{WRAPPER}} .slick-dots li button:before, {{WRAPPER}} .slick-dots li button' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};'], 'condition' => ['carousel_dots_enable' => 'yes']]);
        $this->add_responsive_control('dots_space', ['label' => esc_html__('Dots space', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => 10], 'range' => ['px' => ['max' => 50, 'min' => 2, 'step' => 1]], 'selectors' => ['{{WRAPPER}} .slick-dots li' => 'margin: 0 {{SIZE}}{{UNIT}};'], 'condition' => ['carousel_dots_enable' => 'yes']]);
        $this->end_controls_section();
        // ------------------------------------------------- [ section Style - Filters ]
        $this->start_controls_section('section_style_filters', ['label' => esc_html__('Filters', 'dynamic-content-for-elementor'), 'tab' => Controls_Manager::TAB_STYLE, 'condition' => ['posts_style' => 'grid', 'filters_enable' => 'yes']]);
        $this->add_control('filters_color', ['label' => esc_html__('Filters Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .dce-filters .filters-item a' => 'color: {{VALUE}};']]);
        $this->add_control('filters_color_hover', ['label' => esc_html__('Filters Color Hover', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .dce-filters .filters-item a:hover' => 'color: {{VALUE}};']]);
        $this->add_control('filters_color_active', ['label' => esc_html__('Filters Color Active', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'default' => '#990000', 'selectors' => ['{{WRAPPER}} .dce-filters .filters-item.filter-active a' => 'color: {{VALUE}};']]);
        $this->add_control('filters_color_divisore', ['label' => esc_html__('Divider Filters Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .dce-filters .filters-divider' => 'color: {{VALUE}};']]);
        $this->add_group_control(Group_Control_Typography::get_type(), ['name' => 'typography_filters', 'label' => esc_html__('Typography Filters', 'dynamic-content-for-elementor'), 'selector' => '{{WRAPPER}} .dce-filters']);
        $this->add_group_control(Group_Control_Typography::get_type(), ['name' => 'typography_filters_divider', 'label' => esc_html__('Typography Divider', 'dynamic-content-for-elementor'), 'selector' => '{{WRAPPER}} .dce-filters .filters-divider']);
        $this->add_responsive_control('filters_padding_items', ['label' => esc_html__('Filters spacing', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => 5, 'unit' => 'px'], 'tablet_default' => ['unit' => 'px'], 'mobile_default' => ['unit' => 'px'], 'size_units' => ['px', '%'], 'range' => ['%' => ['min' => 0, 'max' => 100], 'px' => ['min' => 0, 'max' => 100]], 'selectors' => ['{{WRAPPER}} .dce-filters .filters-divider' => 'padding-left: {{SIZE}}{{UNIT}}; padding-right: {{SIZE}}{{UNIT}};']]);
        $this->add_control('filters_padding', ['label' => esc_html__('Padding', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => ['px', '%'], 'selectors' => ['{{WRAPPER}} .dce-filters' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};']]);
        $this->add_responsive_control('filters_move_divider', ['label' => esc_html__('Vertical Shift Divider', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => 0, 'unit' => 'px'], 'tablet_default' => ['unit' => 'px'], 'mobile_default' => ['unit' => 'px'], 'size_units' => ['px', '%'], 'range' => ['%' => ['min' => -100, 'max' => 100], 'px' => ['min' => -100, 'max' => 100]], 'selectors' => ['{{WRAPPER}} .dce-filters .filters-divider' => 'top: {{SIZE}}{{UNIT}}; position: relative;']]);
        $this->end_controls_section();
    }
    protected function safe_render()
    {
        $settings = $this->get_settings_for_display();
        if (empty($settings)) {
            return;
        }
        $id_page = Helper::get_the_id();
        $current_post_type = get_post_type();
        $type_page = '';
        if (\false !== $current_post_type) {
            // phpstan
            $type_page = \DynamicContentForElementor\Helper::validate_post_types($current_post_type);
        }
        $default_posts_per_page = get_option('posts_per_page');
        if ($settings['num_posts'] == 0 || $settings['num_posts'] == '') {
            $settings['num_posts'] = $default_posts_per_page;
        }
        // NEW ARCHIVE (in caso ci trovassimo in un archivio)
        if (is_archive()) {
            global $wp_taxonomies;
            $queried_object = get_queried_object();
            if (is_tax() || is_category() || is_tag()) {
                if ($queried_object->name == 'product') {
                    // WOOCOMMERCE
                    $taxonomy = \reset($queried_object->taxonomies);
                    if (isset($wp_taxonomies[$taxonomy])) {
                        $type_page = $wp_taxonomies[$taxonomy]->object_type;
                    }
                } else {
                    $taxonomy = $queried_object->taxonomy;
                    if (isset($wp_taxonomies[$taxonomy])) {
                        $type_page = $wp_taxonomies[$taxonomy]->object_type;
                    }
                }
            }
        }
        // ------- QUERY -----------------------------------------
        $args = [];
        $taxquery = [];
        $exclude_io = [];
        $posts_excluded = [];
        $terms_query_exclued = [];
        if (is_singular()) {
            if ($settings['exclude_io']) {
                $exclude_io = [$id_page];
            }
        } elseif (is_home() || is_archive()) {
            $exclude_io = [];
        }
        if ($settings['exclude_posts']) {
            $posts_excluded = $settings['exclude_posts'];
        }
        if ($settings['exclude_page_parent']) {
            $use_parent_page = [0];
        } else {
            $use_parent_page = [];
        }
        $terms_query = 'all';
        if ($settings['taxonomy']) {
            /* INCLUDED */
            if (!empty($settings['terms_' . $settings['taxonomy']])) {
                $terms_query = $settings['terms_' . $settings['taxonomy']];
            }
            if (\is_array($terms_query) && !empty($terms_query) || $settings['terms_current_post']) {
                // metodo per recuperare i termini
                $terms_query = $this->get_terms_query($settings, $id_page);
                $taxquery = [];
                if (\is_array($terms_query) && !empty($terms_query)) {
                    if (\count($terms_query) > 1) {
                        $taxquery['relation'] = $settings['combination_taxonomy'];
                    }
                    foreach ($terms_query as $term_query) {
                        $taxquery[] = ['taxonomy' => $settings['taxonomy'], 'terms' => $term_query];
                    }
                }
            } else {
                $taxquery = [['taxonomy' => $settings['taxonomy'], 'terms' => $terms_query]];
            }
            /* EXCLUDED */
            $terms_query_exclued = $settings['terms_' . $settings['taxonomy'] . '_excluse'];
            if (!empty($terms_query_exclued)) {
                $taxquery_excluded = [];
                if (\count($terms_query_exclued) > 1) {
                    $taxquery_excluded['relation'] = $settings['combination_taxonomy_excluse'];
                }
                foreach ($terms_query_exclued as $term_query) {
                    $taxquery_excluded[] = ['taxonomy' => $settings['taxonomy'], 'terms' => $term_query, 'operator' => 'NOT IN'];
                }
                if (empty($taxquery)) {
                    $taxquery = $taxquery_excluded;
                } else {
                    $taxquery = ['relation' => 'AND', $taxquery, $taxquery_excluded];
                }
            }
        }
        // Qui voglio elaborare la cosa in base alla ACF taxonomy che usa la lista dei termini associati nel post specifico
        if ($settings['terms_from_acf'] && $settings['acf_taxonomy']) {
            $acfterm = \false;
            if (\function_exists('get_field')) {
                $acfterm = \get_field($settings['acf_taxonomy'], $id_page);
            }
            if (!$acfterm) {
                $acfterm = get_post_meta($id_page, $settings['acf_taxonomy']);
            }
            $terms_query = [];
            if (!empty($acfterm)) {
                foreach ($acfterm as $term) {
                    $terms_query[] = $term;
                }
                $taxquery = [['taxonomy' => $settings['taxonomy'], 'terms' => $terms_query]];
            }
        }
        if ($settings['query_type'] == 'specific_posts') {
            $types = Helper::get_public_post_types();
            $specific_posts = [0];
            foreach ($types as $t => $tname) {
                if (isset($settings['specific_pages' . $t])) {
                    $t_array = $settings['specific_pages' . $t];
                    if (\is_array($t_array) || \is_object($t_array)) {
                        $specific_posts = \array_merge($specific_posts, $t_array);
                    }
                }
            }
            $args = ['post_type' => 'any', 'post__in' => $specific_posts, 'order' => $settings['order'], 'orderby' => $settings['orderby'], 'meta_key' => $settings['acf_metakey'], 'post_status' => 'publish'];
        } elseif ($settings['query_type'] == 'dynamic_mode') {
            $array_taxquery = [];
            $taxonomy_list = [];
            // DYNAMIC MODE:
            if (is_archive()) {
                // Considero se sono in un archivio (Term)
                $queried_object = get_queried_object();
                if (is_tax() || is_category() || is_tag()) {
                    $taxonomy_list[0] = $queried_object->taxonomy;
                }
            } elseif (is_single()) {
                // Considero se sono in un single-post (Correlati)
                $taxonomy_list = get_post_taxonomies($id_page);
            }
            if (!empty($taxonomy_list)) {
                foreach ($taxonomy_list as $tax) {
                    $terms_list = [];
                    $lista_dei_termini = [];
                    if (is_single()) {
                        // Considero se sono in un single-post (Correlati)
                        if ($settings['taxonomy'] == $tax) {
                            $terms_list = wp_get_post_terms($id_page, $tax, ['orderby' => 'name', 'order' => 'ASC', 'fields' => 'all', 'hide_empty' => \true]);
                        }
                        foreach ($terms_list as $term) {
                            $lista_dei_termini[] = $term->term_id;
                        }
                    } elseif (is_archive()) {
                        // Considero se sono in un archivio (Term)
                        $lista_dei_termini[0] = $queried_object->term_id;
                    }
                    if (\count($lista_dei_termini) > 0) {
                        $array_taxquery = [];
                        if (\count($lista_dei_termini) > 1) {
                            $array_taxquery['relation'] = $settings['combination_taxonomy'];
                        }
                        foreach ($lista_dei_termini as $termine) {
                            $array_taxquery[] = ['taxonomy' => $tax, 'field' => 'id', 'terms' => $termine];
                        }
                    }
                    /* EXCLUDED */
                    $terms_query_exclued = $settings['terms_' . $tax . '_excluse'];
                    if (!empty($terms_query_exclued)) {
                        $array_taxquery_excluded = [];
                        if (\count($terms_query_exclued) > 1) {
                            $array_taxquery_excluded['relation'] = $settings['combination_taxonomy_excluse'];
                        }
                        foreach ($terms_query_exclued as $term_query) {
                            $array_taxquery_excluded[] = ['taxonomy' => $tax, 'field' => 'term_id', 'terms' => $term_query, 'operator' => 'NOT IN'];
                        }
                        if (empty($array_taxquery)) {
                            $array_taxquery = $array_taxquery_excluded;
                        } else {
                            $array_taxquery = ['relation' => 'AND', $array_taxquery, $array_taxquery_excluded];
                        }
                    }
                }
            }
            // Se la taxQuery dynamica non da risultati uso quella statica.
            if (!$array_taxquery) {
                $array_taxquery = $taxquery;
            }
            if (\is_array($type_page)) {
                if ($cptkey = \array_search('elementor_library', $type_page)) {
                    $type_page[$cptkey] = 'post';
                }
            } elseif ('elementor_library' == $type_page) {
                $type_page = 'post';
            }
            $args = ['post_type' => $type_page, 'posts_per_page' => $settings['num_posts'], 'order' => $settings['order'], 'orderby' => $settings['orderby'], 'meta_key' => $settings['acf_metakey'], 'post__not_in' => \array_merge($posts_excluded, $exclude_io), 'post_parent__not_in' => $use_parent_page, 'tax_query' => $array_taxquery, 'post_status' => 'publish'];
            if (is_date()) {
                global $wp_query;
                $args['year'] = $wp_query->query_vars['year'];
                $args['monthnum'] = $wp_query->query_vars['monthnum'];
                $args['day'] = $wp_query->query_vars['day'];
            }
            // ----------------------------------------------------------
            if ($settings['page_parent']) {
                if ($settings['parent_source']) {
                    // rispetto a me-stesso prendo il post genitore
                    $args['post_parent'] = wp_get_post_parent_id($id_page);
                } elseif ($settings['child_source']) {
                    $args['post_parent'] = $id_page;
                } else {
                    $args['post_parent'] = Helper::get_translated_post_id($settings['specific_page_parent'], $type_page);
                }
            }
            if ($settings['post_offset']) {
                $args['offset'] = $settings['post_offset'];
            }
            // ----------------------------------------------------------
        } elseif ($settings['query_type'] == 'acf_relations') {
            if ($settings['acf_relationship_invert']) {
                $relations_ids = Helper::get_acf_field_value_relationship_invert($settings['acf_relationship'], $id_page);
            } else {
                $relations_ids = get_post_meta($id_page, $settings['acf_relationship'], \true);
            }
            if (!empty($relations_ids) && !\is_array($relations_ids)) {
                // for single Post Object field
                $relations_ids = [$relations_ids];
            }
            if (empty($relations_ids)) {
                $relations_ids = ['0'];
            }
            if (!empty($relations_ids)) {
                $ordinamentoRelationship = 'post__in';
                if ($settings['orderby'] != 'menu_order') {
                    $ordinamentoRelationship = $settings['orderby'];
                }
                $args = ['post_type' => 'any', 'posts_per_page' => $settings['num_posts'], 'post__in' => $relations_ids, 'post_status' => 'publish', 'orderby' => $ordinamentoRelationship, 'order' => $settings['order']];
            }
        } elseif ($settings['query_type'] == 'get_cpt') {
            $args = ['post_type' => \DynamicContentForElementor\Helper::validate_post_types($settings['post_type']), 'posts_per_page' => $settings['num_posts'], 'order' => $settings['order'], 'orderby' => $settings['orderby'], 'post_status' => 'publish'];
            if ($taxquery) {
                $args['tax_query'] = $taxquery;
            }
            if ($settings['acf_metakey']) {
                $args['meta_key'] = $settings['acf_metakey'];
            }
            $post__not_in = \array_merge($posts_excluded, $exclude_io);
            if (!empty($post__not_in)) {
                $args['post__not_in'] = $post__not_in;
            }
            if (!empty($use_parent_page)) {
                $args['post_parent__not_in'] = $use_parent_page;
            }
            if ($settings['page_parent']) {
                if ($settings['parent_source']) {
                    // rispetto a me-stesso prendo il post genitore
                    $args['post_parent'] = wp_get_post_parent_id($id_page);
                } elseif ($settings['child_source']) {
                    $args['post_parent'] = $id_page;
                } else {
                    $args['post_parent'] = Helper::get_translated_post_id($settings['specific_page_parent'], $type_page);
                }
            }
            if ($settings['post_offset']) {
                $args['offset'] = $settings['post_offset'];
            }
        }
        global $paged;
        $paged = $this->get_current_page();
        $args['paged'] = $paged;
        $per_page = $settings['num_posts'];
        $offset = ($paged - 1) * $per_page;
        // - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
        // da implementare in base all'autore della pagina utile nella pagina utente
        if ($settings['by_author']) {
            $author_id = get_the_author_meta('ID');
            if (!is_singular() || $author_id) {
                $queried_object = get_queried_object();
                if ($queried_object) {
                    if (\get_class($queried_object) == 'WP_User') {
                        $author_id = get_queried_object_id();
                    }
                }
            }
            $args['author__in'] = $author_id;
            $args['posts_per_archive_page'] = $settings['num_posts'];
        }
        if ($settings['by_users']) {
            $args['author__in'] = $settings['by_users'];
        }
        if ($settings['exclude_users']) {
            $args['author__not_in'] = $settings['exclude_users'];
        }
        // - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
        if ($settings['querydate_mode']) {
            $querydate_field_meta_format = 'Ymd';
            // get the field to compare
            $date_field = $settings['querydate_field'];
            if ($settings['querydate_mode'] == 'past' && $settings['querydate_field'] == 'post_meta') {
                $date_field = $settings['querydate_field_meta'];
                $querydate_field_meta_format = $settings['querydate_field_meta_format'];
            }
            if ($settings['querydate_mode'] == 'future') {
                $date_field = $settings['querydate_field_meta_future'];
                if (!$settings['querydate_field_meta_future_format'] && $settings['querydate_field_meta_format']) {
                    $querydate_field_meta_format = $settings['querydate_field_meta_format'];
                } else {
                    $querydate_field_meta_format = $settings['querydate_field_meta_future_format'];
                }
            }
            if ($date_field) {
                $date_before = \false;
                $date_after = $date_before;
                switch ($settings['querydate_mode']) {
                    case 'past':
                        $date_before = \date('Y-m-d H:i:s');
                        break;
                    case 'future':
                        $date_after = \date('Y-m-d H:i:s');
                        break;
                    case 'today':
                        $date_after = \date('Y-m-d 00:00:00');
                        $date_before = \date('Y-m-d 23:59:59');
                        break;
                    case 'yesterday':
                        $date_after = \date('Y-m-d 00:00:00', \strtotime('-1 day'));
                        $date_before = \date('Y-m-d 23:59:59', \strtotime('-1 day'));
                        break;
                    case 'days':
                    case 'weeks':
                    case 'months':
                    case 'years':
                        $date_after = '-' . $settings['querydate_range'] . ' ' . $settings['querydate_mode'];
                        $date_before = 'now';
                        break;
                    case 'period':
                        $date_after = $settings['querydate_date_from' . $settings['querydate_date_type']];
                        $date_before = $settings['querydate_date_to' . $settings['querydate_date_type']];
                        break;
                }
                if ($date_field == 'post_date') {
                    // compare by post publish date
                    $args['date_query'] = [['after' => $date_after, 'before' => $date_before, 'inclusive' => \true]];
                } else {
                    // compare by post meta
                    if ($date_after) {
                        $date_after = \date($querydate_field_meta_format, \strtotime($date_after));
                    }
                    if ($date_before) {
                        $date_before = \date($querydate_field_meta_format, \strtotime($date_before));
                    }
                    if ($date_before && $date_after) {
                        $args['meta_query'] = [['key' => $date_field, 'value' => [$date_after, $date_before], 'meta_type' => 'DATETIME', 'compare' => 'BETWEEN']];
                    } elseif ($date_after) {
                        $args['meta_query'] = [['key' => $date_field, 'value' => $date_after, 'meta_type' => 'DATETIME', 'compare' => '>=']];
                    } else {
                        $args['meta_query'] = [['key' => $date_field, 'value' => $date_before, 'meta_type' => 'DATETIME', 'compare' => '<=']];
                    }
                }
            }
        }
        // Build the WordPress query
        $p_query = new \WP_Query($args);
        if ($settings['filters_enable'] && $settings['posts_style'] == 'grid') {
            $include_terms = 'all';
            $tag_filter = 'span';
            $divisore_f = '';
            if ($settings['filters_taxonomy'] || $settings['taxonomy']) {
                // Now I check whether $settings['taxonomy'] or $settings['filters_taxonomy'] ... if the isotope filter taxonomy is empty I use the taxonomy from the Query filters
                $term_filter = $settings['taxonomy'];
                if ($settings['filters_taxonomy']) {
                    $term_filter = $settings['filters_taxonomy'];
                }
                $args_filters = [];
                $args_filters['taxonomy'] = $term_filter;
                $args_filters['hide_empty'] = !empty($settings['filter_hide_empty']) ? \false : \true;
                // This part was added to handle filters based on the returned posts and avoid empty filters.
                $args_posts = $args;
                $args_posts['fields'] = 'ids';
                // This is used to include only the posts that are present and calculated for the view
                $someposts = get_posts($args_posts);
                $args_filters['object_ids'] = $someposts;
                // I consider only first level items ....
                $include_terms = [];
                //'all';
                if ($settings['filters_taxonomy_first_level_terms']) {
                    $terms = get_terms($args_filters);
                    // ..Get all the terms
                    foreach ($terms as $term) {
                        //Cycle through terms, one at a time
                        if ($term->parent == '0') {
                            $include_terms[] = $term->term_id;
                        }
                    }
                } elseif (isset($settings['filters_taxonomy_terms_' . $term_filter]) && !empty($settings['filters_taxonomy_terms_' . $term_filter])) {
                    $include_terms = $settings['filters_taxonomy_terms_' . $term_filter];
                }
                $args_filters['include'] = $include_terms;
                $args_filters['orderby'] = $settings['orderby_filters'];
                $term_list_filters = get_terms($args_filters);
            }
            // ACF relation filter
            if ($settings['filters_acf']) {
                $term_list_filters = [];
                $counter = 0;
                if ($p_query->have_posts()) {
                    while ($p_query->have_posts()) {
                        $p_query->the_post();
                        $id_page = get_the_ID();
                        $acffield = \false;
                        if (\function_exists('get_field')) {
                            $acffield = \get_field($settings['filters_acf'], $id_page);
                        }
                        if (!$acffield) {
                            $acffield = get_post_meta($id_page, $settings['filters_acf'], \true);
                        }
                        $acfslug = $this->create_slug($acffield);
                        $obj = (object) ['name' => $acffield, 'slug' => $acfslug];
                        if ($acffield) {
                            $term_list_filters[$counter] = $obj;
                        }
                        ++$counter;
                    }
                    // Reset the post data to prevent conflicts with WP globals
                    wp_reset_query();
                    wp_reset_postdata();
                    ?>

						<?php 
                    // End post check
                }
            }
            // end acf filters
            echo '<div class="dce-filters">';
            $cont_f = 0;
            if (!empty($term_list_filters)) {
                $divisore_f = '<span class="filters-divider">' . wp_kses_post($settings['separator_filter']) . '</span>';
                if ($settings['all_filter']) {
                    $alltext = wp_kses_post($settings['alltext_filter']);
                    echo '<' . $tag_filter . ' class="filters-item filter-active"><a href="#" data-filter="*">' . $alltext . '</a></' . $tag_filter . '>' . $divisore_f;
                } else {
                    $widget_id_js = esc_js($this->get_id());
                    echo '<script>jQuery(window).load(function(){jQuery(".elementor-element-' . $widget_id_js . ' .filters-item.filter-active > a").click();});</script>';
                }
                foreach ($term_list_filters as $fkey => $filter) {
                    if ($filter instanceof \WP_Term || $settings['filters_acf']) {
                        if ($fkey) {
                            echo $divisore_f;
                        }
                        $term_link = $filter instanceof \WP_Term ? get_term_link($filter->term_id) : '#';
                        $term_url = is_wp_error($term_link) ? '#' : esc_url($term_link);
                        $filter_slug = esc_attr($filter->slug);
                        $filter_name = esc_html($filter->name);
                        echo '<' . $tag_filter . ' class="filters-item' . (!$fkey && (bool) empty($settings['all_filter']) ? ' filter-active' : '') . '"><a href="' . $term_url . '" data-filter=".' . $filter_slug . '">' . $filter_name . '</a></' . $tag_filter . '>';
                    }
                }
            }
            echo '</div>';
        }
        // **************************** Grid/Carousel ************************
        $dataStyle = '';
        $styleClass = '';
        $dataGrid = '';
        // ------- Simple data ----------------------------------------------
        if ($settings['posts_style'] == 'simplegrid') {
            $dataStyle = ' data-style="simple"';
            $styleClass = ' simple-style';
        } elseif ($settings['posts_style'] == 'flexgrid') {
            $dataStyle = ' data-style="flexgrid"';
            $styleClass = ' flexgrid-style';
        } elseif ($settings['posts_style'] == 'carousel' || $settings['posts_style'] == 'dualslider') {
            $dataStyle = ' data-style="carousel"';
            $styleClass = ' carowsel-style';
        } elseif ($settings['posts_style'] == 'grid') {
            $dataStyle = ' data-style="grid"';
            $dataGrid = ' data-fitrow="' . esc_attr($settings['fitrow_enable']) . '"';
            $styleClass = ' grid-style';
        } elseif ($settings['posts_style'] == 'swiper') {
            $dataStyle = ' data-style="swiper"';
            $dataGrid = ' data-fitrow="' . esc_attr($settings['fitrow_enable']) . '"';
            $styleClass = ' swiper-style';
        } elseif ($settings['posts_style'] == 'timeline') {
            $dataStyle = ' data-style="timeline"';
            $styleClass = ' timeline-style';
        }
        // ---------------------------------------------------------------------
        $stringInfiniteScroll = '';
        if ($settings['infiniteScroll_enable']) {
            $stringInfiniteScroll = ' is_infiniteScroll';
        }
        // dce-posts-wrap
        // dce-post-item
        $stringSameHeightWrap = '';
        $stringSameHeightItem = '';
        if ($settings['sameheight_enable'] && $settings['posts_style'] == 'flexgrid') {
            $stringSameHeightWrap . ($stringInfiniteScroll = ' equalHMRWrap eqWrap');
            $stringSameHeightItem = ' equalHMR eq';
        }
        $counter = 0;
        // here I define the variables for creating the single date above the block/post
        $data_unica_old = '';
        $data_unica_new = '';
        $classContainer = '';
        $classWrap = '';
        $classItem = '';
        $classItemImage = '';
        $classItemContent = '';
        $classItemDate = '';
        $classItemReadMore = '';
        if ($settings['posts_style'] == 'swiper') {
            $swiper_class = 'swiper';
            $classContainer = $swiper_class . ' swiper-container-' . esc_attr($settings['direction_slider']);
            $classWrap = ' swiper-wrapper';
            $classItem = ' swiper-slide';
            echo '<div class="' . esc_attr($classContainer) . '">';
        }
        if ($settings['posts_style'] == 'timeline') {
            $classContainer = 'cd-timeline js-cd-timeline';
            $classWrap = ' cd-timeline__container';
            $classItem = ' cd-timeline__block js-cd-block';
            $classItemImage = ' cd-timeline__img cd-timeline__img--picture js-cd-img';
            $classItemContent = ' cd-timeline__content js-cd-content';
            $classItemDate = ' cd-timeline__date';
            $classItemReadMore = ' cd-timeline__read-more';
            echo '<div class="' . esc_attr($classContainer) . '">';
        }
        // Output posts
        // ////////////////////////////////////////// Query POST ///////////////////////////////////////////
        if ($p_query->have_posts()) {
            $animation_class = !empty($settings['hover_animation']) ? 'elementor-animation-' . $settings['hover_animation'] : '';
            $postlength = $p_query->post_count;
            // DualSlider: Sopra al grid le immagini grandi
            ?>
			<div class="acfposts-grid dce-posts-wrap<?php 
            echo esc_attr($styleClass . $classWrap . $stringSameHeightWrap . $stringInfiniteScroll);
            ?>"<?php 
            echo $dataStyle . $dataGrid;
            // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- pre-built safe data attributes
            ?>>

				<?php 
            global $wp_query;
            $original_post = $wp_query->queried_object;
            $original_post_id = $wp_query->queried_object_id;
            $original_in_the_loop = $wp_query->in_the_loop;
            $wp_query->in_the_loop = \true;
            // Start loop
            while ($p_query->have_posts()) {
                $p_query->the_post();
                // if num_post is -1 I still consider the offset
                if ($settings['post_offset'] > $counter && $settings['num_posts'] == -1) {
                    ++$counter;
                    continue;
                }
                $wp_query->queried_object = get_post();
                $wp_query->queried_object_id = $id_page = get_the_ID();
                $wow_enable = $settings['enabled_wow'];
                if ($wow_enable) {
                    $wow_coeff = (float) $settings['wow_coef'];
                    $wow_delay_value = (string) ($counter * $wow_coeff);
                    $wow_delay = ' data-wow-delay="' . esc_attr($wow_delay_value) . 's"';
                    $wow_animations = esc_attr($settings['wow_animations']);
                    $wow_string = ' wow ' . $wow_animations;
                } else {
                    $wow_string = '';
                    $wow_delay = '';
                }
                $hoverEffects_enable = $settings['hover_text_effect'];
                $hoverEffectsClass = '';
                if ($hoverEffects_enable) {
                    $hoverEffectsClass = ' dce-hover-effects';
                }
                // --------------------------------------------
                // 0 - if filters are enabled
                $filters_string_class = '';
                if ($settings['filters_enable']) {
                    if (!empty($term_filter)) {
                        // 1 - I need to know which terms are associated with the post
                        $terms = get_the_terms($id_page, $term_filter);
                        if ($terms && !is_wp_error($terms)) {
                            $draught_links = [];
                            foreach ($terms as $term) {
                                $draught_links[] = sanitize_html_class($term->slug);
                            }
                            $filters_string_class .= ' ' . \implode(' ', $draught_links);
                        }
                        // end if terms exist
                    }
                    // end if in filters_Taxonomy
                    if ($settings['filters_acf'] != '0') {
                        // if .. $settings['filters_acf'] ..
                        $acf_field = $settings['filters_acf'];
                        $slug = \false;
                        if (\function_exists('get_field')) {
                            $slug = \get_field($acf_field, $id_page);
                        }
                        if (!$slug) {
                            $slug = get_post_meta($id_page, $acf_field, \true);
                        }
                        $slug_acf_field = sanitize_html_class($this->create_slug($slug));
                        $filters_string_class .= ' ' . $slug_acf_field;
                    }
                    // end se sono in filters_acf
                }
                // end if filters_enable
                // 2 - I write them into a string separated by space
                // 3 - I put the string into the item's class
                $id_page_attr = esc_attr((string) $id_page);
                $columns_grid_attr = esc_attr($settings['columns_grid']);
                ?>

					<div data-dce-post-id="<?php 
                echo $id_page_attr;
                ?>" class="dce-post-item dce-post-item-<?php 
                echo $id_page_attr;
                ?> dce-elementor-<?php 
                echo $id_page_attr;
                ?> column-<?php 
                echo esc_attr($columns_grid_attr . $classItem);
                ?>  <?php 
                echo esc_attr($filters_string_class . $stringSameHeightItem . $wow_string . $hoverEffectsClass);
                ?>"<?php 
                echo $wow_delay;
                // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- pre-escaped with esc_attr
                ?>>

						<?php 
                if ($settings['unic_date']) {
                    // The single date above the block
                    if ($counter > 0) {
                        $data_unica = '';
                        $data_unica_old = $data_unica_new;
                        $data_unica_new = get_the_date('Y');
                        //
                        if ($data_unica_new != $data_unica_old) {
                            $data_unica = $data_unica_new;
                        }
                        if (!empty($data_unica)) {
                            ?>
									<span class="dce-acfposts_date-year"><?php 
                            echo esc_html((string) $data_unica);
                            ?></span>
								<?php 
                        }
                    }
                }
                ?>

						<div class="dce-wrapper <?php 
                echo esc_attr($animation_class);
                ?>">

							<?php 
                if ($settings['templatemode_enable'] == '' && $settings['native_templatemode_enable'] == '') {
                    ?>

								<?php 
                    // deprecated: last version
                    if ($settings['textcontent_position'] == 'top_out') {
                        $this->generate_content($settings, $id_page);
                    }
                    // IMAGE
                    if (has_post_thumbnail() && $settings['show_image'] != 0 && $settings['order_image'] == '') {
                        $this->generate_image($settings, $classItemImage);
                    }
                    if ($hoverEffects_enable) {
                        echo '<div class="dce-hover-effect dce-hover-effect-content dce-close">';
                    }
                    ?>
								<div class="dce-acfposts_content<?php 
                    echo esc_attr($classItemContent);
                    ?>">
									<?php 
                    $counter_item = 1;
                    if (isset($settings['list_layout_posts']) && !empty($settings['list_layout_posts'])) {
                        $items_ordering = $settings['list_layout_posts'];
                        foreach ($items_ordering as $listitem) {
                            $item_name = $listitem['list_name'];
                            if ($item_name == 'Image' && $settings['order_image'] == 'yes') {
                                if (has_post_thumbnail() && $settings['show_image'] != 0) {
                                    $this->generate_image($settings, $classItemImage);
                                }
                            }
                            if ($item_name == 'Date') {
                                if ($settings['show_date'] != 0) {
                                    $this->generate_date($settings, $classItemDate, $id_page);
                                }
                            }
                            if ($item_name == 'Title') {
                                if ($settings['show_title'] == 1 && $settings['title_inout'] == 'out') {
                                    $this->generate_title($settings);
                                }
                            }
                            if ($item_name == 'Meta Data') {
                                if ($settings['show_metadata'] == 1 && $settings['metadata_inout'] == 'out') {
                                    $this->generate_meta($settings, $id_page);
                                }
                            }
                            if ($item_name == 'Type') {
                                if ($settings['show_type'] == 1) {
                                    $this->generate_type($settings, $id_page);
                                }
                            }
                            if ($item_name == 'Content') {
                                if ($settings['textcontent_position'] == 'bottom_in') {
                                    $this->generate_content($settings, $id_page);
                                }
                            }
                            if ($item_name == 'Author') {
                                if ($settings['show_author'] == 1 && $settings['author_inout'] == 'out') {
                                    $this->generate_author($settings);
                                }
                            }
                            if ($item_name == 'ACF items') {
                                if ($settings['show_acfitems'] == '1') {
                                    $this->generate_acfitems($settings, $id_page);
                                }
                            }
                            if ($item_name == 'Read More') {
                                if ($settings['show_readmore'] == 1 && $settings['readmore_inout'] == 'out') {
                                    $this->generate_readmore($settings, $classItemReadMore);
                                }
                            }
                            ++$counter_item;
                        }
                    } else {
                        // ************************************************
                        if ($settings['show_date'] != 0) {
                            $this->generate_date($settings, $classItemDate, $id_page);
                        }
                        // ************************************************
                        if ($settings['show_title'] == 1 && $settings['title_inout'] == 'out') {
                            $this->generate_title($settings);
                        }
                        // ***********************************************************************************
                        if ($settings['show_metadata'] == 1 && $settings['metadata_inout'] == 'out') {
                            $this->generate_meta($settings, $id_page);
                        }
                        // ************************************************
                        if ($settings['show_type'] == 1) {
                            $this->generate_type($settings, $id_page);
                        }
                        // ***********************************************************************************
                        if ($settings['show_author'] == 1 && $settings['author_inout'] == 'out') {
                            $this->generate_author($settings);
                        }
                        // **************************************************
                        if ($settings['show_acfitems'] == '1') {
                            $this->generate_acfitems($settings, $id_page);
                        }
                        // now by default the content is only this, the others are deprecated
                        if ($settings['textcontent_position'] == 'bottom_in') {
                            $this->generate_content($settings, $id_page);
                        }
                        if ($settings['show_readmore'] == 1 && $settings['readmore_inout'] == 'out') {
                            $this->generate_readmore($settings, $classItemReadMore);
                        }
                    }
                    ?>

								</div>
								<?php 
                    if ($hoverEffects_enable) {
                        echo '</div>';
                    }
                    ?>
								<?php 
                    // deprecated: last version
                    if ($settings['textcontent_position'] == 'bottom_out') {
                        echo '<div style="clear: both"></div>';
                        $this->generate_content($settings, $id_page);
                    }
                } else {
                    // end if templatemode_enable NO .. qui comincia l'utilizzo dei template
                    if (\Elementor\Plugin::$instance->editor->is_edit_mode()) {
                        $inlinecss = 'inlinecss="true"';
                    } else {
                        $inlinecss = '';
                    }
                    // template DCE
                    if ($settings['templatemode_enable'] && $settings['templatemode_enable_2'] == '') {
                        $atts = ['id' => $settings['templatemode_template'], 'post_id' => get_the_id(), 'inlinecss' => \Elementor\Plugin::$instance->editor->is_edit_mode()];
                        $template_system = \DynamicContentForElementor\Plugin::instance()->template_system;
                        echo $template_system->build_elementor_template_special($atts);
                    } elseif ($settings['native_templatemode_enable']) {
                        $type_of_posts = get_post_type(get_the_ID());
                        $cptaxonomy = get_post_taxonomies(get_the_ID());
                        $taxonomy_objects = get_object_taxonomies('post', 'objects');
                        $options = get_option(DCE_TEMPLATE_SYSTEM_OPTION);
                        $dce_elementor_templates = 'dyncontel_field_archive' . $type_of_posts;
                        // *********
                        $dce_default_template = $options[$dce_elementor_templates];
                        if (isset($cptaxonomy) && \count($cptaxonomy) > 0) {
                            //foreach ($cptaxonomy as $chiave) {
                            $chiave = $cptaxonomy[0];
                            // 3 - Taxonomy
                            if (isset($options['dyncontel_field_archive_taxonomy_' . $chiave])) {
                                //
                                $dce_default_template_taxo = $options['dyncontel_field_archive_taxonomy_' . $chiave];
                                if (!empty($dce_default_template_taxo) && $dce_default_template_taxo > 0) {
                                    $dce_elementor_templates = 'dyncontel_field_archive_taxonomy_' . $chiave;
                                }
                            }
                            // *********
                            $dce_default_template = $options[$dce_elementor_templates];
                            //foreach ($cptaxonomy as $chiave) {
                            // 4 - Termine
                            $cptaxonomyterm = get_the_terms(get_the_ID(), $cptaxonomy[0]);
                            if (isset($cptaxonomyterm) && $cptaxonomyterm) {
                                foreach ($cptaxonomyterm as $cpterm) {
                                    $termine_id = $cpterm->term_id;
                                    $dce_default_template_term = get_term_meta($termine_id, 'dynamic_content_block', \true);
                                    if (!empty($dce_default_template_term)) {
                                        // *********
                                        $dce_default_template = $dce_default_template_term;
                                    }
                                }
                            }
                        }
                        $atts = ['id' => $dce_default_template, 'post_id' => get_the_id(), 'inlinecss' => \Elementor\Plugin::$instance->editor->is_edit_mode()];
                        $template_system = \DynamicContentForElementor\Plugin::instance()->template_system;
                        echo $template_system->build_elementor_template_special($atts);
                    } elseif ($settings['templatemode_enable'] && $settings['templatemode_enable_2']) {
                        if ($counter % 2 == 0) {
                            $atts = ['id' => $settings['templatemode_template'], 'post_id' => get_the_id(), 'inlinecss' => \Elementor\Plugin::$instance->editor->is_edit_mode()];
                        } else {
                            $atts = ['id' => $settings['templatemode_template_2'], 'post_id' => get_the_id(), 'inlinecss' => \Elementor\Plugin::$instance->editor->is_edit_mode()];
                        }
                        $template_system = \DynamicContentForElementor\Plugin::instance()->template_system;
                        echo $template_system->build_elementor_template_special($atts);
                    }
                }
                // end Template mode..
                // this option spreads the link over the entire block and ignores individual links
                if ($settings['templatemode_linkable']) {
                    echo '<a style="cursor: pointer; position: absolute; left: 0; top: 0; right: 0; bottom: 0; z-index: 10;" href="' . esc_url((string) get_the_permalink()) . '">';
                    echo '</a>';
                }
                ?>
						</div><!-- end wrapper -->

					</div><!-- end item -->

					<?php 
                ++$counter;
            }
            $wp_query->queried_object = $original_post;
            $wp_query->queried_object_id = $original_post_id;
            ?>

			</div><!-- end grid -->

			<?php 
            // Reset the post data to prevent conflicts with WP globals
            // Restore original Query & Post Data
            wp_reset_query();
            wp_reset_postdata();
            // end grid container
            // Numeric pagination ........
            if ($settings['pagination_enable']) {
                $this->numeric_query_pagination($p_query->max_num_pages, $settings);
            }
            // Infinitescroll pagination ...
            // If infiniteScroll is enabled and the generated posts exceed the displayed posts
            if ($settings['infiniteScroll_enable'] && $postlength >= $settings['num_posts'] && $settings['num_posts'] >= 0 || \Elementor\Plugin::$instance->editor->is_edit_mode()) {
                $preview_mode = '';
                if (\Elementor\Plugin::$instance->editor->is_edit_mode() && $settings['infiniteScroll_show_preview']) {
                    $preview_mode = ' visible';
                }
                if ($settings['infiniteScroll_enable_status']) {
                    ?>
					<nav class="infiniteScroll">
						<div class="page-load-status<?php 
                    echo esc_attr($preview_mode);
                    ?>">

							<?php 
                    if ($settings['infiniteScroll_loading_type'] == 'text') {
                        ?>
								<div class="infinite-scroll-request status-text"><?php 
                        echo wp_kses_post($settings['infiniteScroll_label_loading']);
                        ?></div>
								<?php 
                    } elseif ($settings['infiniteScroll_loading_type'] == 'ellips') {
                        ?>
								<div class="loader-ellips infinite-scroll-request">
									<span class="loader-ellips__dot"></span>
									<span class="loader-ellips__dot"></span>
									<span class="loader-ellips__dot"></span>
									<span class="loader-ellips__dot"></span>
								</div>
								<?php 
                    }
                    ?>
							<div class="infinite-scroll-last status-text"><?php 
                    echo wp_kses_post($settings['infiniteScroll_label_last']);
                    ?></div>
							<div class="infinite-scroll-error status-text"><?php 
                    echo wp_kses_post($settings['infiniteScroll_label_error']);
                    ?></div>

							<div class="pagination" role="navigation">
								<a class="pagination__next" href="<?php 
                    echo esc_url(Helper::get_next_pagination());
                    ?>"></a>
							</div>
						</div>
					</nav>
						<?php 
                }
                // Infinite scroll Button version ...
                if ($settings['infiniteScroll_trigger'] == 'button') {
                    ?>
					<div class="infiniteScroll">
						<button class="view-more-button"><?php 
                    echo wp_kses_post($settings['infiniteScroll_label_button']);
                    ?></button>
					</div>
					<?php 
                }
            }
            ?>

			<?php 
            // End post check
        }
        // *********************************************************************** end Query POST
        if ($settings['posts_style'] == 'swiper') {
            // previously, before opening the wp_query, I created the container for the swiper
            echo '</div> <!-- swiper-wrapper -->';
            // NOTE: the pagination and navigation for the swiper are outside its container so I can move the elements as I like, since the container is overflow: hidden, and if they were inside (as by default) they would be hidden outside the area.
            $widget_id_attr = esc_attr($this->get_id());
            $direction_attr = esc_attr($settings['direction_slider']);
            if ($settings['usePagination']) {
                // Add Pagination
                echo '<div class="swiper-container-' . $direction_attr . '"><div class="swiper-pagination pagination-' . $widget_id_attr . '"></div></div>';
            }
            if ($settings['useNavigation']) {
                // Add Arrows
                echo '<div class="swiper-button-prev prev-' . $widget_id_attr . '"><svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="-10px" y="-10px"
                width="85.039px" height="85.039px" viewBox="378.426 255.12 85.039 85.039" enable-background="new 378.426 255.12 85.039 85.039"
                xml:space="preserve">
                <line fill="none" stroke="#000000" stroke-width="1.3845" stroke-dasharray="0,0" stroke-miterlimit="10" x1="382.456" y1="298.077" x2="458.375" y2="298.077"/>
                <polyline fill="none" stroke="#000000" stroke-width="1.3845" stroke-dasharray="0,0" stroke-miterlimit="10" points="416.287,331.909,382.456,298.077,416.287,264.245 "/>
                </svg></div>';
                echo '<div class="swiper-button-next next-' . $widget_id_attr . '"><svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
                width="85.039px" height="85.039px" viewBox="378.426 255.12 85.039 85.039" enable-background="new 378.426 255.12 85.039 85.039"
                xml:space="preserve">
                <line fill="none" stroke="#000000" stroke-width="1.3845" stroke-miterlimit="10" x1="458.375" y1="298.077" x2="382.456" y2="298.077"/>
                <polyline fill="none" stroke="#000000" stroke-width="1.3845" stroke-miterlimit="10" points="424.543,264.245 458.375,298.077,424.543,331.909 "/>
                </svg></div>';
            }
        }
        // end Swiper chiusura
        // end Render .....
    }
    public function get_current_page()
    {
        if ('' === $this->get_settings('pagination_enable') && '' === $this->get_settings('infiniteScroll_enable')) {
            return 1;
        }
        return \max(1, get_query_var('paged'), get_query_var('page'));
    }
    /**
     * @param mixed $str
     * @param string $delimiter
     * @return string
     */
    private function create_slug($str, $delimiter = '-')
    {
        if (\is_string($str)) {
            $slug = \strtolower(\trim(\preg_replace('/[\\s-]+/', $delimiter, \preg_replace('/[^A-Za-z0-9-]+/', $delimiter, \preg_replace('/[&]/', 'and', \preg_replace('/[\']/', '', \iconv('UTF-8', 'ASCII//TRANSLIT', $str))))), $delimiter));
            return $slug;
        }
        return '';
    }
    /**
     * @param array<string,mixed> $settings
     * @return void
     */
    private function generate_author($settings)
    {
        $author = [];
        $avatar_args['size'] = $settings['author_avatar_size'] ?? 150;
        $user_id = get_the_author_meta('ID');
        $author['avatar'] = get_avatar_url($user_id, $avatar_args);
        $author['display_name'] = get_the_author_meta('display_name');
        $author['website'] = get_the_author_meta('user_url');
        $author['bio'] = get_the_author_meta('description');
        $author['posts_url'] = get_author_posts_url($user_id);
        echo '<div class="dce_author-wrap">';
        if ($settings['enable_author_image']) {
            echo '<div class="dce_author-avatar"><a href="' . esc_url($author['posts_url']) . '">' . '<img src="' . esc_url($author['avatar']) . '" alt="' . esc_attr($author['display_name']) . '" />' . '</a></div>';
        }
        echo '<div class="dce_author-text">';
        echo '<div class="dce_author-name">' . esc_html($author['display_name']) . '</div>';
        if ($settings['enable_author_bio']) {
            echo '<div class="dce_author-bio">' . wp_kses_post($author['bio']) . '</div>';
        }
        echo '</div>';
        echo '</div>';
    }
    /**
     * @param array<string,mixed> $settings
     * @return void
     */
    private function generate_title($settings)
    {
        \printf('<%1$s class="dce-post-title">', Helper::validate_html_tag($settings['html_tag']));
        ?>
		<?php 
        if ($settings['title_link']) {
            ?><a href="<?php 
            the_permalink();
            ?>"><?php 
        }
        echo wp_kses_post(get_the_title());
        if ($settings['title_link']) {
            ?></a><?php 
        }
        ?>
		<?php 
        \printf('</%s>', Helper::validate_html_tag($settings['html_tag']));
    }
    /**
     * @param array<string,mixed> $settings
     * @param string $clss
     * @return void
     */
    private function generate_readmore($settings, $clss)
    {
        echo '<div class="dce_readmore_wrapper' . esc_attr((string) $clss) . '"><a href="' . esc_url((string) get_the_permalink()) . '" class="dce_readmore_btn">' . wp_kses_post($settings['readmore_text']) . '</a></div>';
    }
    /**
     * @param array<string,mixed> $settings
     * @param string $id_page
     * @return void
     */
    private function generate_meta($settings, $id_page = null)
    {
        if (!$id_page) {
            $id_page = get_the_ID();
        }
        $term_list = [];
        $divisore = '';
        $tag_metadata = 'span';
        if ($settings['metadata_block_enable']) {
            $tag_metadata = 'div';
        }
        $taxonomyAuto = get_post_taxonomies($id_page);
        echo '<div class="dce_metadata-wrap">';
        foreach ($taxonomyAuto as $tax) {
            if (isset($settings['taxonomy_metadata_filter']) && !empty($settings['taxonomy_metadata_filter'])) {
                if (!\in_array($tax, $settings['taxonomy_metadata_filter'])) {
                    continue;
                }
            }
            $term_list = Helper::get_the_terms_ordered($id_page, $tax);
            if ($term_list && \is_array($term_list)) {
                // l'icona
                if ($settings['metadata_icon_enable']) {
                    $icon_metadata = '';
                    if (is_taxonomy_hierarchical($tax)) {
                        $icon_metadata = '<i class="fa fa-folder-open" aria-hidden="true"></i> ';
                    } else {
                        $icon_metadata = '<i class="fa fa-tags" aria-hidden="true"></i> ';
                    }
                    echo $icon_metadata;
                }
                // ------- Ciclo i termini
                $cont = 1;
                foreach ($term_list as $term) {
                    $termparent = \true;
                    if ($settings['only_parent_metadata']) {
                        $termparent = $term->parent;
                    }
                    if (!$termparent || !$settings['only_parent_metadata']) {
                        echo '<' . $tag_metadata . '>';
                        $term_link = get_term_link($term);
                        $term_url = is_wp_error($term_link) ? '' : esc_url(trailingslashit($term_link));
                        $linkOpen = '';
                        $linkClose = '';
                        if ($settings['metadata_link']) {
                            $linkOpen = '<a href="' . $term_url . '">';
                            $linkClose = '</a>';
                        }
                        if ($cont > 1 && !$settings['metadata_block_enable']) {
                            $divisore = '<span class="dce_metadata-separator">' . wp_kses_post($settings['separator_metadata']) . '</span>';
                        } else {
                            $divisore = '';
                        }
                        $term_order = esc_attr($term->term_order);
                        $term_name = esc_html($term->name);
                        echo $divisore . '<span class="dce_metadata" data-dce-order="' . $term_order . '">' . $linkOpen . $term_name . $linkClose . '</span>';
                        ++$cont;
                        echo '</' . $tag_metadata . '>';
                    }
                }
            }
        }
        echo '</div>';
    }
    /**
     * @param array<string,mixed> $settings
     * @param string $id_page
     * @return void
     */
    private function generate_content($settings, $id_page = null)
    {
        if (!$id_page) {
            $id_page = get_the_ID();
        }
        ?>
		<div class="dce_textcontent">
			<?php 
        // deprecated: last version
        if ($settings['show_metadata'] == 1 && $settings['metadata_inout'] == 'in') {
            $this->generate_meta($settings, $id_page);
        }
        // deprecated: last version
        if ($settings['show_title'] == 1 && $settings['title_inout'] == 'in') {
            $this->generate_title($settings);
        }
        ?>
			<?php 
        if ($settings['show_textcontent'] == 1) {
            if ($settings['textcontent_limit'] == '') {
                echo wpautop(wp_kses_post(get_the_content()));
            } else {
                echo esc_html($this->limit_content($settings['textcontent_limit']));
            }
        }
        if ($settings['show_textcontent'] == 2) {
            $mypost = get_post($id_page);
            echo wp_kses_post($mypost->post_excerpt);
        }
        // deprecated: last version
        if ($settings['show_author'] == 1 && $settings['author_inout'] == 'in') {
            $this->generate_author($settings);
        }
        // deprecated: last version
        if ($settings['show_readmore'] == 1 && $settings['readmore_inout'] == 'in') {
            $this->generate_readmore($settings, $classItemReadMore);
        }
        ?>
		</div>
		<?php 
    }
    /**
     * @param array<string,mixed> $settings
     * @param string $clss
     * @param string $id_page
     * @return void
     */
    private function generate_date($settings, $clss = null, $id_page = null)
    {
        $date = '';
        $date2 = '';
        $date3 = '';
        if ($settings['date_type']) {
            $date_type = $settings['date_type'];
        } else {
            $date_type = 'publish';
        }
        switch ($date_type) {
            case 'modified':
                $date = '<span class="d1">' . wp_kses_post((string) get_the_modified_date($settings['date_format'])) . '</span>';
                if ($settings['date_format_2'] != '') {
                    $date2 = '<span class="d2">' . wp_kses_post((string) get_the_modified_date($settings['date_format_2'])) . '</span>';
                }
                if ($settings['date_format_3'] != '') {
                    $date3 = '<span class="d3">' . wp_kses_post((string) get_the_modified_date($settings['date_format_3'])) . '</span>';
                }
                break;
            case 'publish':
            default:
                $date = '<span class="d1">' . wp_kses_post((string) get_the_date($settings['date_format'])) . '</span>';
                if ($settings['date_format_2'] != '') {
                    $date2 = '<span class="d2">' . wp_kses_post((string) get_the_date($settings['date_format_2'])) . '</span>';
                }
                if ($settings['date_format_3'] != '') {
                    $date3 = '<span class="d3">' . wp_kses_post((string) get_the_date($settings['date_format_3'])) . '</span>';
                }
                break;
        }
        echo '<div class="dce-post-date' . esc_attr((string) $clss) . '">' . $date . $date2 . $date3 . '</div>';
    }
    /**
     * @param array<string,mixed> $settings
     * @param int|null $id_page
     * @return void
     */
    private function generate_acfitems($settings, $id_page = null)
    {
        $counter_item = 1;
        $ACFitems = $settings['acf_items'];
        if (!empty($ACFitems)) {
            foreach ($ACFitems as $acfkey => $acfitem) {
                $spazio = '';
                $tag_item = 'span';
                $tag_subitem = Helper::validate_html_tag($acfitem['html_tag_item']);
                $tag_subitem_start = '';
                $tag_subitem_end = '';
                $acf_i = $acfitem['acf_field_item'];
                if (empty($acf_i)) {
                    continue;
                }
                $acf_type_i = $acfitem['acf_field_type'];
                $spazio = ' ';
                if ($acfitem['block_enable']) {
                    $tag_item = 'div';
                }
                $link = \false;
                switch ($acfitem['link_to']) {
                    case 'custom':
                        if (!empty($acfitem['link']['url'])) {
                            $link = esc_url($acfitem['link']['url']);
                        }
                        break;
                    case 'post':
                        $link = esc_url((string) get_the_permalink($id_page));
                        break;
                    case 'parent':
                        $id_page_parent = wp_get_post_parent_id($id_page);
                        $link = get_the_permalink($id_page_parent) ? esc_url(get_the_permalink($id_page_parent)) : '';
                        break;
                    case 'home':
                        $link = esc_url(get_home_url());
                        break;
                }
                $target = !empty($acfitem['link']['is_external']) ? ' target="_blank"' : '';
                if ($link) {
                    $tag_subitem_start = '<a href="' . $link . '"' . $target . '>';
                    $tag_subitem_end = '</a>';
                }
                if ($tag_subitem) {
                    $tag_subitem_start = '<' . $tag_subitem . '>' . $tag_subitem_start;
                    $tag_subitem_end = $tag_subitem_end . '</' . $tag_subitem . '>';
                }
                if ($acf_i == 'title') {
                    $item_id = esc_attr($acfitem['_id']);
                    echo '<' . $tag_item . ' class="acf-acfpost-item elementor-repeater-item-' . $item_id . '">' . $tag_subitem_start . wp_kses_post(get_the_title()) . $tag_subitem_end . '</' . $tag_item . '>' . $spazio;
                } elseif ($acf_i == 'content') {
                    $contentacf = get_post($id_page);
                    $item_id = esc_attr($acfitem['_id']);
                    echo '<' . $tag_item . ' class="acf-acfpost-item elementor-repeater-item-' . $item_id . '">' . $tag_subitem_start . wpautop(get_the_content()) . $tag_subitem_end . '</' . $tag_item . '>' . $spazio;
                } elseif ($acf_i == 'taxonomy') {
                    $divisore_i = '';
                    $term_list_item = get_the_terms($id_page, $acfitem['taxonomy_metadata']);
                    $item_id = esc_attr($acfitem['_id']);
                    echo '<' . $tag_item . ' class="acf-acfpost-item elementor-repeater-item-' . $item_id . '">' . $tag_subitem_start;
                    $cont_i = 1;
                    if (!empty($term_list_item)) {
                        foreach ($term_list_item as $term) {
                            if ($cont_i > 1) {
                                $divisore_i = wp_kses_post($settings['separator_metadata']);
                            }
                            if ($acfitem['link_to'] == 'custom') {
                                if (empty($acfitem['link']['url'])) {
                                    $term_link = get_term_link($term->term_id, $acfitem['taxonomy_metadata']);
                                    $link = is_wp_error($term_link) ? '#' : esc_url($term_link);
                                }
                                echo '<a href="' . $link . '">';
                            }
                            echo '<span>' . $divisore_i . esc_html($term->name) . '</span>';
                            if ($acfitem['link_to'] == 'custom') {
                                echo '</a>';
                            }
                            ++$cont_i;
                        }
                    }
                    echo $tag_subitem_end . '</' . $tag_item . '>' . $spazio;
                } elseif ($acf_i == 'date') {
                    $item_id = esc_attr($acfitem['_id']);
                    $date_value = get_the_date($acfitem['acf_date_format']);
                    $date_formatted = $date_value ? esc_html((string) $date_value) : '';
                    echo '<' . $tag_item . ' class="acf-acfpost-item elementor-repeater-item-' . $item_id . '">' . $tag_subitem_start . $date_formatted . $tag_subitem_end . '</' . $tag_item . '>' . $spazio;
                } else {
                    $acf_i_val = \false;
                    if (\function_exists('get_field')) {
                        $acf_i_val = \get_field($acf_i, $id_page);
                    }
                    if (!$acf_i_val) {
                        $acf_i_val = get_post_meta($id_page, $acf_i, \true);
                    }
                    if ($acf_type_i == 'image') {
                        if (\is_string($acf_i_val)) {
                            if (\is_numeric($acf_i_val)) {
                                $imageSrc = wp_get_attachment_image_src($acf_i_val, 'full');
                                $imageSrcUrl = $imageSrc[0];
                                $immagine_acf = $imageSrcUrl;
                            } else {
                                $immagine_acf = $acf_i_val;
                            }
                        } elseif (\is_numeric($acf_i_val)) {
                            $imageSrc = wp_get_attachment_image_src($acf_i_val, 'full');
                            $imageSrcUrl = $imageSrc[0];
                            $immagine_acf = $imageSrcUrl;
                        } elseif (\is_array($acf_i_val)) {
                            $imageSrc = wp_get_attachment_image_src($acf_i_val['ID'], 'full');
                            $imageSrcUrl = $imageSrc[0];
                            $immagine_acf = $imageSrcUrl;
                        }
                        if (isset($immagine_acf)) {
                            echo '<img src="' . esc_url($immagine_acf) . '" />';
                        }
                    } elseif ($acf_type_i == 'date') {
                        $dataDate = get_field_object($acf_i);
                        if ($acf_i_val) {
                            $d = \DateTime::createFromFormat($dataDate['return_format'], $acf_i_val);
                            $item_id = esc_attr($acfitem['_id']);
                            $date_formatted = esc_html(date_i18n($acfitem['acf_date_format'], $d->format('U')));
                            echo '<' . $tag_item . ' class="acf-acfpost-item elementor-repeater-item-' . $item_id . '">' . $date_formatted . '</' . $tag_item . '>' . $spazio;
                        }
                    } else {
                        $acf_i_val = Helper::to_readable_string($acf_i_val);
                        $item_id = esc_attr($acfitem['_id']);
                        echo '<' . $tag_item . ' class="acf-acfpost-item elementor-repeater-item-' . $item_id . '">' . $tag_subitem_start . wp_kses_post($acf_i_val) . $tag_subitem_end . '</' . $tag_item . '>' . $spazio;
                    }
                }
                ++$counter_item;
            }
        }
    }
    /**
     * @param int $limit
     * @return string
     */
    protected function limit_content($limit)
    {
        $post = get_post();
        if (!$post) {
            return '';
        }
        $content = $post->post_content;
        $charset = get_bloginfo('charset');
        $content = \mb_substr(wp_strip_all_tags($content), 0, $limit, $charset) . '&hellip;';
        return $content;
    }
    /**
     * @param int $limit
     * @return string
     */
    protected function limit_excerpt($limit)
    {
        $excerpt = \explode(' ', get_the_excerpt(), $limit);
        if (\count($excerpt) >= $limit) {
            \array_pop($excerpt);
            $excerpt = \implode(' ', $excerpt) . '...';
        } else {
            $excerpt = \implode(' ', $excerpt);
        }
        $excerpt = \preg_replace('`[[^]]*]`', '', $excerpt);
        return $excerpt ? wp_kses_post($excerpt) : '';
    }
    /**
     * @param array<string,mixed> $settings
     * @param string $clss
     * @return void
     */
    protected function generate_image($settings, $clss = null)
    {
        $image_url = Group_Control_Image_Size::get_attachment_image_src(get_post_thumbnail_id(), 'size', $settings);
        ?>
		<div class="dce-acfposts_image<?php 
        echo esc_attr((string) $clss);
        ?>">
			<?php 
        if (!$settings['use_bgimage']) {
            echo '<div class="dce-acfposts_imagewrap">';
        }
        ?>
			<?php 
        if ($settings['image_link']) {
            echo '<a href="' . esc_url((string) get_the_permalink()) . '">';
        }
        ?>
			<?php 
        // in caso di background image
        if ($settings['use_bgimage']) {
            ?>
				<figure class="acfposts-image" style="background: url(&quot;<?php 
            echo esc_url($image_url);
            ?>&quot;) no-repeat center; background-size: cover; display: block;"></figure>
				<?php 
        } else {
            // in caso di img
            ?>
				<img src="<?php 
            echo esc_url($image_url);
            ?>" title="<?php 
            echo wp_kses_post(get_the_title());
            ?>" class="acfposts-image" />
				<?php 
        }
        ?>

			<?php 
        if ($settings['use_overlay']) {
            ?>
				<div class="dce-overlay"></div>
			<?php 
        }
        ?>
			<?php 
        if ($settings['use_overlay_hover']) {
            ?>
				<div class="dce-overlay_hover"></div>
			<?php 
        }
        ?>
			<?php 
        if ($settings['image_link']) {
            echo '</a>';
        }
        ?>
			<?php 
        if (!$settings['use_bgimage']) {
            echo '</div>';
        }
        ?>
		</div>

		<?php 
    }
    // ADVANCED CUSTOM FIELDS
    /**
     * @param array<string,mixed> $settings
     * @param string $id_page
     * @return string|array<string>
     */
    public function get_terms_query($settings = null, $id_page = null)
    {
        $terms_query = 'all';
        if (!$settings) {
            $settings = $this->get_settings_for_display();
        }
        if (!$id_page) {
            $id_page = get_the_ID();
        }
        if ($settings['taxonomy']) {
            // for backward compatibility with the old category
            if ($settings['category'] != '') {
                $terms_query = \explode(',', $settings['category']);
            }
            if ($settings['terms_current_post']) {
                // Da implementare oR & AND tems ...
                if (is_singular()) {
                    $terms_list = wp_get_post_terms($id_page, $settings['taxonomy'], ['orderby' => 'name', 'order' => 'ASC', 'fields' => 'all', 'hide_empty' => \true]);
                    if (!empty($terms_list)) {
                        $terms_query = [];
                        foreach ($terms_list as $akey => $aterm) {
                            if (!\in_array($aterm->term_id, $terms_query)) {
                                $terms_query[] = $aterm->term_id;
                            }
                        }
                    }
                }
                if (is_archive()) {
                    if (is_tax() || is_category() || is_tag()) {
                        $queried_object = get_queried_object();
                        $terms_query = [$queried_object->term_id];
                    }
                }
            }
            if (isset($settings['terms_' . $settings['taxonomy']]) && !empty($settings['terms_' . $settings['taxonomy']])) {
                $terms_query = $settings['terms_' . $settings['taxonomy']];
                // add current post terms id
                $dce_key = \array_search('dce_current_post_terms', $terms_query);
                if ($dce_key !== \false) {
                    unset($terms_query[$dce_key]);
                    $terms_list = wp_get_post_terms($id_page, $settings['taxonomy'], ['orderby' => 'name', 'order' => 'ASC', 'fields' => 'all', 'hide_empty' => \true]);
                    if (!empty($terms_list)) {
                        $terms_query = [];
                        foreach ($terms_list as $akey => $aterm) {
                            if (!\in_array($aterm->term_id, $terms_query)) {
                                $terms_query[] = $aterm->term_id;
                            }
                        }
                    }
                }
            }
        }
        return $terms_query;
    }
    /**
     * @param array<string,mixed> $settings
     * @param int $post_id
     * @return void
     */
    public function generate_type($settings, $post_id)
    {
        $post = get_post($post_id);
        $post_type = get_post_type(get_post($post_id));
        $type = get_post_type_object($post_type);
        echo '<' . Helper::validate_html_tag($settings['html_tag_type']) . ' class="dce-post-type">';
        if ($settings['type_link']) {
            echo '<a href="' . esc_url((string) get_post_type_archive_link($post_type)) . '">';
        }
        if ($settings['type_label'] == 'singular') {
            echo esc_html($type->labels->singular_name);
        } else {
            echo esc_html($type->label);
        }
        if ($settings['type_link']) {
            echo '</a>';
        }
        echo '</' . Helper::validate_html_tag($settings['html_tag_type']) . '>';
    }
    /**
     * @param array<string,mixed> $settings
     * @param string $key
     * @return array{left: string|false, right: string|false}
     */
    protected function get_leftright_icon($settings, $key)
    {
        $old_key = $key;
        $new_key = 'selected_' . $key;
        $migration_allowed = Icons_Manager::is_migration_allowed();
        // old default
        if (!isset($settings[$old_key]) && !$migration_allowed) {
            $settings[$old_key] = 'fa fa-long-arrow-right';
        }
        $migrated = isset($settings['__fa4_migrated'][$new_key]);
        $is_new = empty($settings[$old_key]) && $migration_allowed;
        $icon = ['right' => '', 'left' => ''];
        if ($migrated || $is_new) {
            \ob_start();
            Icons_Manager::render_icon($settings[$new_key] ?? '', ['aria-hidden' => 'true']);
            $icon['right'] = \ob_get_clean();
            $left = \str_replace('right', 'left', $settings[$new_key] ?? '');
            \ob_start();
            Icons_Manager::render_icon($left, ['aria-hidden' => 'true']);
            $icon['left'] = \ob_get_clean();
        } else {
            $prefix = \str_replace('right', '', $settings[$old_key]);
            $icon['left'] = '<i class="' . esc_attr($prefix . 'left') . '"></i>';
            $icon['right'] = '<i class="' . esc_attr($prefix . 'right') . '"></i>';
        }
        return $icon;
    }
    /**
     *  Numeric Query Pagination
     *
     * @param int|string $pages
     * @param array<mixed> $settings
     * @return void
     */
    protected function numeric_query_pagination($pages, $settings)
    {
        // Inline SVG icons not supported
        $icon_prevnext = self::get_leftright_icon($settings, 'pagination_icon_prevnext');
        $icon_firstlast = self::get_leftright_icon($settings, 'pagination_icon_firstlast');
        $range = (int) $settings['pagination_range'] - 1;
        // The numbers displayed at a time
        $showitems = $range * 2 + 1;
        $paged = \max(1, get_query_var('paged'), get_query_var('page'));
        if (empty($paged)) {
            $paged = 1;
        }
        if ($pages == '') {
            global $wp_query;
            $pages = $wp_query->max_num_pages;
            if (!$pages) {
                $pages = 1;
            }
        }
        if ($pages !== 1) {
            echo '<div class="dce-pagination">';
            // Progression
            if ($settings['pagination_show_progression']) {
                echo '<span class="progression">' . esc_html((string) $paged) . ' / ' . esc_html((string) $pages) . '</span>';
            }
            // First
            if ($settings['pagination_show_firstlast']) {
                if ($paged > 2 && $paged > $range + 1 && $showitems < $pages) {
                    $link = Helper::get_wp_link_page(1);
                    echo '<a href="' . esc_url($link) . '" class="pagefirst">' . $icon_firstlast['left'] . ' ' . wp_kses_post($settings['pagination_first_label']) . '</a>';
                }
            }
            // Prev
            if ($settings['pagination_show_prevnext']) {
                if ($paged > 1 && $showitems < $pages) {
                    $link = Helper::get_wp_link_page($paged - 1);
                    echo '<a href="' . esc_url($link) . '" class="pageprev">' . $icon_prevnext['left'] . ' ' . wp_kses_post($settings['pagination_prev_label']) . '</a>';
                }
            }
            // Numbers
            if ($settings['pagination_show_numbers']) {
                for ($i = 1; $i <= $pages; $i++) {
                    if (1 != $pages && (!($i >= $paged + $range + 1 || $i <= $paged - $range - 1) || $pages <= $showitems)) {
                        $link = Helper::get_wp_link_page($i);
                        echo $paged == $i ? '<span class="current">' . esc_html((string) $i) . '</span>' : '<a href="' . esc_url($link) . '" class="inactive">' . esc_html((string) $i) . '</a>';
                    }
                }
            }
            // Next
            if ($settings['pagination_show_prevnext']) {
                if ($paged < $pages && $showitems < $pages) {
                    $link = Helper::get_wp_link_page($paged + 1);
                    echo '<a href="' . esc_url($link) . '" class="pagenext">' . wp_kses_post($settings['pagination_next_label']) . ' ' . $icon_prevnext['right'] . '</a>';
                }
            }
            // Last
            if ($settings['pagination_show_firstlast']) {
                if ($paged < $pages - 1 && $paged + $range - 1 < $pages && $showitems < $pages) {
                    $link = Helper::get_wp_link_page($pages);
                    echo '<a href="' . esc_url($link) . '" class="pagelast">' . wp_kses_post($settings['pagination_last_label']) . ' ' . $icon_firstlast['right'] . '</a>';
                }
            }
            echo '</div>';
        }
    }
}
