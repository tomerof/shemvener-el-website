<?php

// SPDX-FileCopyrightText: 2018-2026 Ovation S.r.l. <help@dynamic.ooo>
// SPDX-License-Identifier: GPL-3.0-or-later
namespace DynamicContentForElementor\Includes\Skins;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Background;
use Elementor\Utils;
use DynamicContentForElementor\Helper;
if (!\defined('ABSPATH')) {
    exit;
    // Exit if accessed directly
}
class SkinDualCarousel extends \DynamicContentForElementor\Includes\Skins\SkinCarousel
{
    /**
     * Register Controls Actions
     *
     * @return void
     */
    protected function _register_controls_actions()
    {
        add_action('elementor/element/' . $this->get_parent()->get_name() . '/section_query/after_section_end', [$this, 'register_controls_layout']);
        add_action('elementor/element/' . $this->get_parent()->get_name() . '/section_dynamicposts/after_section_end', [$this, 'register_additional_dualcarousel_controls']);
        add_action('elementor/element/' . $this->get_parent()->get_name() . '/section_dynamicposts/after_section_end', [$this, 'register_additional_carousel_controls']);
    }
    public $depended_scripts = ['swiper', 'dce-dynamicPosts-carousel'];
    /**
     * Get Style Depends
     *
     * @return array<string>
     */
    public function get_style_depends()
    {
        return ['dce-dynamicPosts-carousel', 'dce-dynamicPosts-dualcarousel', 'swiper'];
    }
    public function get_id()
    {
        return 'dualcarousel';
    }
    public function get_title()
    {
        return esc_html__('Dual Carousel', 'dynamic-content-for-elementor');
    }
    public function register_additional_dualcarousel_controls(\DynamicContentForElementor\Widgets\DynamicPostsBase $widget)
    {
        $this->parent = $widget;
        $this->start_controls_section('section_dualcarousel', ['label' => esc_html__('Thumbnails', 'dynamic-content-for-elementor'), 'tab' => Controls_Manager::TAB_CONTENT]);
        // slides per row
        $this->add_responsive_control('thumbnails_slidesPerView', ['label' => esc_html__('Slides Per View', 'dynamic-content-for-elementor'), 'description' => esc_html__('Number of slides per view (slides visible at the same time on sliders container). If you use it with "auto" value and along with loop: true then you need to specify loopedSlides parameter with amount of slides to loop (duplicate). SlidesPerView: "auto"\'" is currently not compatible with multirow mode, when slidesPerColumn greater than 1', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::NUMBER, 'default' => '4', 'tablet_default' => '3', 'mobile_default' => '2', 'separator' => 'before', 'min' => 3, 'max' => 12, 'step' => 1, 'frontend_available' => \true]);
        // space
        $this->add_responsive_control('dualcarousel_space', ['label' => esc_html__('Space', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => ''], 'tablet_default' => ['size' => ''], 'mobile_default' => ['size' => ''], 'range' => ['px' => ['max' => 400, 'min' => 0, 'step' => 1]], 'selectors' => ['{{WRAPPER}} .dce-dualcarousel-thumbnails' => 'margin-top: {{SIZE}}{{UNIT}};']]);
        // gap
        $this->add_responsive_control('dualcarousel_gap', ['label' => esc_html__('Gap', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::NUMBER, 'default' => '', 'tablet_default' => '3', 'mobile_default' => '2', 'separator' => 'before', 'min' => 0, 'max' => 80, 'step' => 1, 'frontend_available' => \true]);
        $this->add_control('thumbnails_loop', ['label' => esc_html__('Loop', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => 'yes', 'frontend_available' => \true]);
        $this->add_control('thumbnails_centered', ['label' => esc_html__('Centered Slides', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => 'yes', 'frontend_available' => \true]);
        $this->add_responsive_control('dualcarousel_align', ['label' => esc_html__('Text Alignment', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CHOOSE, 'toggle' => \false, 'options' => ['left' => ['title' => esc_html__('Left', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-left'], 'center' => ['title' => esc_html__('Center', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-center'], 'right' => ['title' => esc_html__('Right', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-right']], 'default' => is_rtl() ? 'right' : 'left', 'prefix_class' => 'dce-align%s-', 'selectors' => ['{{WRAPPER}} .dce-dualcarousel-gallery-thumbs .swiper-slide' => 'text-align: {{VALUE}};'], 'separator' => 'before']);
        $this->add_control('dualcarousel_heading_status', ['type' => Controls_Manager::RAW_HTML, 'show_label' => \false, 'raw' => esc_html__('Status', 'dynamic-content-for-elementor'), 'label_block' => \false, 'content_classes' => 'dce-icon-heading', 'separator' => 'before']);
        $this->start_controls_tabs('dualcarousel_status');
        $this->start_controls_tab('tab_dualcarousel_normal', ['label' => esc_html__('Normal', 'dynamic-content-for-elementor')]);
        $this->add_control('dualcarousel_item_opacity', ['label' => esc_html__('Opacity', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => ''], 'range' => ['px' => ['max' => 1, 'min' => 0, 'step' => 0.01]], 'selectors' => ['{{WRAPPER}} .dce-dualcarousel-thumbnails .dce-dualcarousel-gallery-thumbs .swiper-slide:not(.swiper-slide-thumb-active) .dce-dualcarousel-wrap' => 'opacity: {{SIZE}};']]);
        // background text color
        $this->add_control('dualcarousel_title_background', ['label' => esc_html__('Title Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'default' => '', 'selectors' => ['{{WRAPPER}} .dce-dualcarousel-thumbnails .dce-dualcarousel-gallery-thumbs .swiper-slide:not(.swiper-slide-thumb-active) .dce-dualcarousel-wrap' => 'color: {{VALUE}};'], 'condition' => [$this->get_control_id('use_title') => 'yes']]);
        // Image background of overlay
        $this->add_control('dualcarousel_heading_normalimageoverlay', ['label' => esc_html__('Normal Image Overlay', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HEADING]);
        $this->add_group_control(Group_Control_Background::get_type(), ['name' => 'dualcarousel_image_background', 'types' => ['classic', 'gradient'], 'selector' => '{{WRAPPER}} .dce-dualcarousel-gallery-thumbs .swiper-slide:not(.swiper-slide-thumb-active) .dce-thumbnail-image:after']);
        $this->end_controls_tab();
        $this->start_controls_tab('tab_dualcarousel_active', ['label' => esc_html__('Active', 'dynamic-content-for-elementor')]);
        $this->add_control('dualcarousel_itemactive_opacity', ['label' => esc_html__('Active Opacity', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => ''], 'range' => ['px' => ['max' => 1, 'min' => 0.1, 'step' => 0.01]], 'selectors' => ['{{WRAPPER}} .dce-dualcarousel-thumbnails .dce-dualcarousel-gallery-thumbs .swiper-slide-thumb-active .dce-dualcarousel-wrap' => 'opacity: {{SIZE}};']]);
        $this->add_control('dualcarousel_titleactive_background', ['label' => esc_html__('Active Title Background', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'default' => '', 'selectors' => ['{{WRAPPER}} .dce-dualcarousel-thumbnails .dce-dualcarousel-gallery-thumbs .swiper-slide-thumb-active .dce-dualcarousel-wrap' => 'color: {{VALUE}};'], 'condition' => [$this->get_control_id('use_title') => 'yes']]);
        $this->add_control('dualcarousel_heading_activeimageoverlay', ['label' => esc_html__('Active Image Overlay', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HEADING]);
        $this->add_group_control(Group_Control_Background::get_type(), ['name' => 'dualcarousel_imageactive_background', 'label' => esc_html__('Active Image Overlay', 'dynamic-content-for-elementor'), 'types' => ['classic', 'gradient'], 'selector' => '{{WRAPPER}} .dce-dualcarousel-gallery-thumbs .swiper-slide-thumb-active .dce-thumbnail-image:after']);
        $this->end_controls_tab();
        $this->end_controls_tabs();
        $this->add_control('dualcarousel_heading_title', ['type' => Controls_Manager::RAW_HTML, 'show_label' => \false, 'raw' => esc_html__('Title', 'dynamic-content-for-elementor'), 'label_block' => \false, 'content_classes' => 'dce-icon-heading', 'separator' => 'before']);
        $this->add_control('use_title', ['label' => esc_html__('Show Title', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => 'yes']);
        $this->add_control('dualcarousel_html_tag', ['label' => esc_html__('HTML Tag', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'options' => Helper::get_html_tags(), 'default' => 'h3', 'condition' => [$this->get_control_id('use_title') => 'yes']]);
        // color
        $this->add_control('dualcarousel_title_color', ['label' => esc_html__('Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'default' => '', 'selectors' => ['{{WRAPPER}} .dce-thumbnail-title' => 'color: {{VALUE}};'], 'condition' => [$this->get_control_id('use_title') => 'yes']]);
        // typography
        $this->add_group_control(Group_Control_Typography::get_type(), ['name' => 'dualcarousel_title_typography', 'label' => esc_html__('Typography', 'dynamic-content-for-elementor'), 'selector' => '{{WRAPPER}} .dce-thumbnail-title', 'condition' => [$this->get_control_id('use_title') => 'yes']]);
        $this->add_control('dualcarousel_text_padding', ['label' => esc_html__('Padding', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => ['px', 'em', '%'], 'selectors' => ['{{WRAPPER}} .dce-dualcarousel-thumbnails .dce-thumbnail-title' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'], 'condition' => [$this->get_control_id('use_title') => 'yes']]);
        $this->add_control('dualcarousel_heading_image', ['type' => Controls_Manager::RAW_HTML, 'show_label' => \false, 'raw' => esc_html__('Image', 'dynamic-content-for-elementor'), 'label_block' => \false, 'content_classes' => 'dce-icon-heading', 'separator' => 'before']);
        $this->add_control('use_image', ['label' => esc_html__('Show Image', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => 'yes']);
        $this->add_group_control(Group_Control_Image_Size::get_type(), ['name' => 'thumbnailimage_size', 'label' => esc_html__('Image Format', 'dynamic-content-for-elementor'), 'default' => 'medium', 'condition' => [$this->get_control_id('use_image') => 'yes']]);
        $this->add_responsive_control('dualcarousel_image_height', ['label' => esc_html__('Height', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => ''], 'tablet_default' => ['size' => ''], 'mobile_default' => ['size' => ''], 'size_units' => ['px', '%', 'em'], 'range' => ['px' => ['max' => 400, 'min' => 0, 'step' => 1], '%' => ['max' => 100, 'min' => 0, 'step' => 1], 'em' => ['max' => 10, 'min' => 0, 'step' => 1]], 'selectors' => ['{{WRAPPER}} .dce-dualcarousel-thumbnails .dce-bgimage' => 'height: {{SIZE}}{{UNIT}};'], 'condition' => [$this->get_control_id('use_image') => 'yes']]);
        $this->end_controls_section();
    }
    /**
     * Render
     *
     * @return void
     */
    public function render()
    {
        parent::render();
        $this->get_parent()->query_posts();
        $query = $this->get_parent()->get_query();
        if ('' == $query || !$query->found_posts) {
            return;
        }
        $this->get_parent()->set_render_attribute('thumbnails', ['class' => ['dce-dualcarousel-thumbnails']]);
        $swiper_class = 'swiper';
        $this->get_parent()->set_render_attribute('swiper-container', ['class' => [$swiper_class, 'dce-dualcarousel-gallery-thumbs']]);
        $this->get_parent()->set_render_attribute('swiper-wrapper', ['class' => ['swiper-wrapper', 'dce-dualcarousel-wrapper']]);
        $this->add_direction('thumbnails');
        echo '<div ' . $this->get_parent()->get_render_attribute_string('thumbnails') . '>';
        echo '<div ' . $this->get_parent()->get_render_attribute_string('swiper-container') . '>';
        echo '<div ' . $this->get_parent()->get_render_attribute_string('swiper-wrapper') . '>';
        if ($query->in_the_loop) {
            $this->current_permalink = get_permalink();
            $this->current_id = get_the_ID();
            $this->render_thumbnail();
        } else {
            while ($query->have_posts()) {
                $query->the_post();
                $this->current_permalink = get_permalink();
                $this->current_id = get_the_ID();
                $this->render_thumbnail();
            }
        }
        wp_reset_postdata();
        echo '</div></div></div>';
    }
    /**
     * Render Thumbnail
     *
     * @return void
     */
    public function render_thumbnail()
    {
        echo '<div class="swiper-slide dce-dualcarousel-item no-transitio">';
        echo '<div class="dce-dualcarousel-wrap">';
        if ($this->get_instance_value('use_image')) {
            $this->render_thumb_image();
        }
        if ($this->get_instance_value('use_title')) {
            $this->render_thumb_title();
        }
        echo '</div>';
        echo '</div>';
    }
    /**
     * Render Thumbnail Title
     *
     * @return void
     */
    protected function render_thumb_title()
    {
        $html_tag = Helper::validate_html_tag($this->get_instance_value('dualcarousel_html_tag'));
        \printf('<%1$s class="dce-thumbnail-title">', $html_tag);
        echo wp_kses_post(get_the_title());
        \printf('</%s>', $html_tag);
    }
    /**
     * Render Thumbnail Image
     *
     * @return void
     */
    protected function render_thumb_image()
    {
        $setting_key = $this->get_instance_value('thumbnailimage_size_size');
        $image_url = wp_get_attachment_image_src(get_post_thumbnail_id(), $setting_key);
        if ($image_url) {
            echo '<div class="dce-thumbnail-image">';
            echo '<figure class="dce-img dce-bgimage" style="background: url(' . esc_url($image_url[0]) . ') no-repeat center; background-size: cover; display: block;"></figure>';
            echo '</div>';
        }
    }
    // Classes
    public function get_container_class()
    {
        return 'swiper dce-skin-' . $this->get_id() . ' dce-skin-' . parent::get_id();
    }
    public function get_wrapper_class()
    {
        return 'swiper-wrapper dce-wrapper-' . $this->get_id() . ' dce-wrapper-' . parent::get_id();
    }
    public function get_item_class()
    {
        return 'swiper-slide dce-item-' . $this->get_id() . ' dce-item-' . parent::get_id();
    }
}
