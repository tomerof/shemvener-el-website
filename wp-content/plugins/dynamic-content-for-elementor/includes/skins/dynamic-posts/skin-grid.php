<?php

// SPDX-FileCopyrightText: 2018-2026 Ovation S.r.l. <help@dynamic.ooo>
// SPDX-License-Identifier: GPL-3.0-or-later
namespace DynamicContentForElementor\Includes\Skins;

use Elementor\Controls_Manager;
use DynamicContentForElementor\Helper;
if (!\defined('ABSPATH')) {
    exit;
    // Exit if accessed directly
}
class SkinGrid extends \DynamicContentForElementor\Includes\Skins\SkinBase
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
    }
    public $depended_scripts = ['imagesloaded', 'dce-dynamicPosts-grid', 'jquery-masonry', 'dce-infinitescroll', 'dce-isotope', 'dce-jquery-match-height'];
    public $depended_styles = ['dce-dynamicPosts-grid'];
    public function get_id()
    {
        return 'grid';
    }
    public function get_title()
    {
        return esc_html__('Grid', 'dynamic-content-for-elementor');
    }
    public function register_additional_grid_controls(\DynamicContentForElementor\Widgets\DynamicPostsBase $widget)
    {
        $this->parent = $widget;
        $this->start_controls_section('section_grid', ['label' => esc_html__('Grid', 'dynamic-content-for-elementor'), 'tab' => Controls_Manager::TAB_CONTENT]);
        $this->add_control('grid_type', ['label' => esc_html__('Type', 'dynamic-content-for-elementor'), 'type' => 'images_selector', 'toggle' => \false, 'type_selector' => 'icon', 'columns_grid' => 3, 'options' => [
            'flex' => ['title' => esc_html__('Flex', 'dynamic-content-for-elementor'), 'return_val' => 'val', 'icon' => 'eicon-gallery-grid'],
            'masonry' => ['title' => esc_html__('Masonry', 'dynamic-content-for-elementor'), 'return_val' => 'val', 'icon' => 'eicon-gallery-masonry'],
            /*'justified' => [
            			'title' => esc_html__('Justified','dynamic-content-for-elementor'),
            			'return_val' => 'val',
            			'icon' => 'eicon-gallery-justified',
            		],*/
            'blog' => ['title' => esc_html__('Blog', 'dynamic-content-for-elementor'), 'return_val' => 'val', 'icon' => 'icon-dce-blog'],
        ], 'default' => 'flex', 'frontend_available' => \true]);
        $this->add_control('blog_template_id', ['label' => esc_html__('First item Template', 'dynamic-content-for-elementor'), 'type' => 'ooo_query', 'placeholder' => esc_html__('Template Name', 'dynamic-content-for-elementor'), 'label_block' => \true, 'query_type' => 'posts', 'object_type' => 'elementor_library', 'separator' => 'after', 'condition' => [$this->get_control_id('grid_type') => ['blog']]]);
        $this->add_responsive_control('column_blog', ['label' => esc_html__('First Item Column', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'default' => '1', 'tablet_default' => '3', 'mobile_default' => '1', 'options' => ['1' => '1/1', '2' => '1/2', '3' => '1/3', '1.5' => '2/3', '4' => '1/4', '1.34' => '3/4', '1.67' => '3/5', '1.25' => '4/5'], 'selectors' => ['{{WRAPPER}} .dce-posts-container.dce-skin-grid-blog .dce-post-item:nth-child(1)' => 'width: calc(100% / {{VALUE}}); flex-basis: calc( 100% / {{VALUE}} ); flex-grow: 0;'], 'condition' => [$this->get_control_id('grid_type') => ['blog']]]);
        $this->add_responsive_control('columns_grid', ['label' => esc_html__('Columns', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'default' => '4', 'tablet_default' => '3', 'mobile_default' => '1', 'options' => ['1' => '1', '2' => '2', '3' => '3', '4' => '4', '5' => '5', '6' => '6', '7' => '7', '8' => '8', '9' => '9', '10' => '10', '11' => '11', '12' => '12'], 'prefix_class' => 'dce-col%s-', 'selectors' => ['{{WRAPPER}} .dce-posts-container.dce-skin-grid .dce-post-item' => 'width: calc(100% / {{VALUE}}); flex: 0 1 calc( 100% / {{VALUE}} );']]);
        $this->add_responsive_control('grid_item_width', ['label' => esc_html__('Width', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'size_units' => ['px', '%', 'vh'], 'default' => ['size' => ''], 'range' => ['px' => ['max' => 800, 'min' => 0, 'step' => 1]], 'condition' => [$this->get_control_id('columns_grid') => '1', $this->get_control_id('grid_type') => 'flex'], 'selectors' => ['{{WRAPPER}} .dce-posts-container.dce-skin-grid' => 'margin: 0 auto; width: {{SIZE}}{{UNIT}};']]);
        $this->add_responsive_control('grid_alternate', ['label' => esc_html__('Alternate', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'size_units' => ['px', '%', 'vw'], 'default' => ['size' => ''], 'range' => ['px' => ['max' => 400, 'min' => 0, 'step' => 1]], 'condition' => [$this->get_control_id('columns_grid') => '1', $this->get_control_id('grid_type') => 'flex'], 'selectors' => ['{{WRAPPER}} .dce-posts-container.dce-skin-grid .dce-posts-wrapper .dce-post-item:nth-child(even)' => 'margin-right: {{SIZE}}{{UNIT}};', '{{WRAPPER}} .dce-posts-container.dce-skin-grid .dce-posts-wrapper .dce-post-item:nth-child(odd)' => 'margin-left: {{SIZE}}{{UNIT}};']]);
        $this->add_control('flex_grow', ['label' => esc_html__('Flex grow', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CHOOSE, 'toggle' => \false, 'label_block' => \false, 'options' => ['1' => ['title' => esc_html__('1', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-check'], '0' => ['title' => esc_html__('0', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-ban']], 'default' => 1, 'selectors' => ['{{WRAPPER}} .dce-posts-container.dce-skin-grid .dce-post-item' => 'flex-grow: {{VALUE}};'], 'condition' => [$this->get_control_id('grid_type!') => ['masonry']]]);
        $this->add_responsive_control('h_pos_postitems', ['label' => esc_html__('Horizontal position', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CHOOSE, 'options' => ['flex-start' => ['title' => esc_html__('Left', 'dynamic-content-for-elementor'), 'icon' => 'eicon-h-align-left'], 'center' => ['title' => esc_html__('Center', 'dynamic-content-for-elementor'), 'icon' => 'eicon-h-align-center'], 'flex-end' => ['title' => esc_html__('Right', 'dynamic-content-for-elementor'), 'icon' => 'eicon-h-align-right'], 'space-between' => ['title' => esc_html__('Space Between', 'dynamic-content-for-elementor'), 'icon' => 'eicon-h-align-stretch'], 'space-around' => ['title' => esc_html__('Space Around', 'dynamic-content-for-elementor'), 'icon' => 'eicon-h-align-stretch']], 'default' => '', 'selectors' => ['{{WRAPPER}} .dce-posts-container.dce-skin-grid .dce-posts-wrapper' => 'justify-content: {{VALUE}};'], 'condition' => [$this->get_control_id('flex_grow') => '0', $this->get_control_id('grid_type!') => ['masonry']]]);
        $this->add_responsive_control('v_pos_postitems', ['label' => esc_html__('Vertical position', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CHOOSE, 'options' => ['flex-start' => ['title' => esc_html__('Top', 'dynamic-content-for-elementor'), 'icon' => 'eicon-v-align-top'], 'center' => ['title' => esc_html__('Middle', 'dynamic-content-for-elementor'), 'icon' => 'eicon-v-align-middle'], 'flex-end' => ['title' => esc_html__('Down', 'dynamic-content-for-elementor'), 'icon' => 'eicon-v-align-bottom'], 'stretch' => ['title' => esc_html__('Stretch', 'dynamic-content-for-elementor'), 'icon' => 'eicon-v-align-stretch']], 'default' => '', 'selectors' => ['{{WRAPPER}} .dce-posts-container.dce-skin-grid .dce-posts-wrapper' => 'align-items: {{VALUE}};', '{{WRAPPER}} .dce-posts-container.dce-skin-grid .dce-item-area' => 'justify-content: {{VALUE}};'], 'condition' => [$this->get_control_id('grid_type!') => ['masonry']]]);
        $this->add_control('match_height', ['label' => esc_html__('Match Height', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'return_value' => 'yes', 'frontend_available' => \true, 'condition' => [$this->get_control_id('grid_type') => ['flex']]]);
        $this->add_control('match_height_by_row', ['label' => esc_html__('Match Height by Row', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'return_value' => 'true', 'frontend_available' => \true, 'condition' => [$this->get_control_id('match_height') => 'yes', $this->get_control_id('grid_type') => ['flex']]]);
        $this->end_controls_section();
        $this->start_controls_section('section_scrollreveal', ['label' => esc_html__('Scroll Reveal', 'dynamic-content-for-elementor'), 'tab' => Controls_Manager::TAB_CONTENT, 'condition' => ['infiniteScroll_enable' => '', '_skin!' => 'grid-filters']]);
        $this->add_control('scrollreveal_effect_type', ['label' => esc_html__('Effect', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'default' => '0', 'separator' => 'after', 'options' => ['0' => esc_html__('None', 'dynamic-content-for-elementor'), '1' => esc_html__('Opacity', 'dynamic-content-for-elementor'), '2' => esc_html__('Move Up', 'dynamic-content-for-elementor'), '3' => esc_html__('Scale Up', 'dynamic-content-for-elementor'), '4' => esc_html__('Fall Perspective', 'dynamic-content-for-elementor'), '5' => esc_html__('Fly', 'dynamic-content-for-elementor'), '6' => esc_html__('Flip', 'dynamic-content-for-elementor'), '7' => esc_html__('Helix', 'dynamic-content-for-elementor'), '8' => esc_html__('Bounce', 'dynamic-content-for-elementor')]]);
        $this->end_controls_section();
    }
    protected function register_style_controls()
    {
        parent::register_style_controls();
        $this->start_controls_section('section_style_grid', ['label' => esc_html__('Grid', 'dynamic-content-for-elementor'), 'tab' => Controls_Manager::TAB_STYLE]);
        $this->add_responsive_control('column_gap', ['label' => esc_html__('Columns Gap', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => 30], 'range' => ['px' => ['min' => 0, 'max' => 100]], 'selectors' => ['{{WRAPPER}} .dce-posts-container.dce-skin-grid .dce-post-item' => 'padding-right: calc( {{SIZE}}{{UNIT}}/2 ); padding-left: calc( {{SIZE}}{{UNIT}}/2 );', '{{WRAPPER}} .dce-posts-container.dce-skin-grid .dce-posts-wrapper' => 'margin-left: calc( -{{SIZE}}{{UNIT}}/2 ); margin-right: calc( -{{SIZE}}{{UNIT}}/2 );']]);
        $this->add_responsive_control('row_gap', ['label' => esc_html__('Rows Gap', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => 35], 'range' => ['px' => ['min' => 0, 'max' => 100]], 'selectors' => ['{{WRAPPER}} .dce-post-item' => 'padding-bottom: {{SIZE}}{{UNIT}};']]);
        $this->end_controls_section();
    }
    protected function render_post()
    {
        $style_items = $this->get_parent()->get_settings('style_items');
        $blog_template_id = $this->get_instance_value('blog_template_id');
        $grid_type = $this->get_instance_value('grid_type');
        $this->render_post_start();
        if (0 === $this->counter && $blog_template_id && 'blog' === $grid_type) {
            $this->render_template($blog_template_id);
        } elseif ('template' === $style_items) {
            $this->render_post_template();
        } elseif ('html_tokens' === $style_items) {
            $this->render_post_dynamic_html();
        } else {
            $this->render_post_items();
        }
        $this->render_post_end();
        ++$this->counter;
    }
    public function get_container_class()
    {
        return 'dce-skin-' . $this->get_id() . ' dce-skin-' . $this->get_id() . '-' . $this->get_instance_value('grid_type');
    }
    /**
     * @return string
     */
    public function get_scrollreveal_class()
    {
        if ($this->get_instance_value('scrollreveal_effect_type') && $this->get_parent()->get_settings('infiniteScroll_enable') !== 'yes') {
            return 'reveal-effect reveal-effect-' . $this->get_instance_value('scrollreveal_effect_type');
        }
        return '';
    }
}
