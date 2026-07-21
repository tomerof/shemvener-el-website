<?php

// SPDX-FileCopyrightText: 2018-2026 Ovation S.r.l. <help@dynamic.ooo>
// SPDX-License-Identifier: GPL-3.0-or-later
namespace DynamicContentForElementor\Includes\Skins;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Image_Size;
use DynamicContentForElementor\Helper;
if (!\defined('ABSPATH')) {
    exit;
    // Exit if accessed directly
}
class SkinGridToFullscreen3D extends \DynamicContentForElementor\Includes\Skins\SkinGrid
{
    /**
     * @return void
     */
    protected function _register_controls_actions()
    {
        add_action('elementor/element/' . $this->get_parent()->get_name() . '/section_query/after_section_end', [$this, 'register_controls_layout']);
        add_action('elementor/element/' . $this->get_parent()->get_name() . '/section_dynamicposts/after_section_end', [$this, 'register_additional_gridtofullscreen3d_controls']);
        add_action('elementor/element/' . $this->get_parent()->get_name() . '/section_dynamicposts/after_section_end', [$this, 'register_additional_grid_controls'], 20);
    }
    /**
     * @var array<string>
     */
    public $depended_scripts = ['dce-threejs-lib', 'dce-anime-lib', 'dce-dynamicPosts-gridtofullscreen3d', 'dce-threejs-gridtofullscreeneffect'];
    /**
     * @var array<string>
     */
    public $depended_styles = ['dce-dynamicPosts-gridtofullscreen3d'];
    /**
     * @return array<string>
     */
    public function get_script_depends()
    {
        return \array_merge(['imagesloaded', 'dce-dynamicPosts-grid', 'jquery-masonry', 'dce-infinitescroll', 'dce-isotope', 'dce-jquery-match-height'], $this->depended_scripts);
    }
    /**
     * @return array<string>
     */
    public function get_style_depends()
    {
        return \array_merge(['dce-dynamicPosts-grid'], $this->depended_styles);
    }
    /**
     * @return string
     */
    public function get_id()
    {
        return 'gridtofullscreen3d';
    }
    /**
     * @return string
     */
    public function get_title()
    {
        return esc_html__('Grid to Fullscreen 3D', 'dynamic-content-for-elementor');
    }
    /**
     * @param \DynamicContentForElementor\Widgets\DynamicPostsBase $widget
     * @return void
     */
    public function register_additional_gridtofullscreen3d_controls(\DynamicContentForElementor\Widgets\DynamicPostsBase $widget)
    {
        $this->parent = $widget;
        $this->start_controls_section('section_gridtofullscreen3d', ['label' => esc_html__('Grid to Fullscreen 3D', 'dynamic-content-for-elementor'), 'tab' => Controls_Manager::TAB_CONTENT]);
        $this->add_control('gridtofullscreen3d_effects', ['label' => esc_html__('Effect', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'default' => 'effect1', 'options' => ['effect1' => esc_html__('Effect 1', 'dynamic-content-for-elementor'), 'effect2' => esc_html__('Effect 2', 'dynamic-content-for-elementor'), 'effect3' => esc_html__('Effect 3', 'dynamic-content-for-elementor'), 'effect4' => esc_html__('Effect 4', 'dynamic-content-for-elementor'), 'effect5' => esc_html__('Effect 5', 'dynamic-content-for-elementor'), 'effect6' => esc_html__('Effect 6', 'dynamic-content-for-elementor'), 'custom_effect' => esc_html__('Custom effect', 'dynamic-content-for-elementor')], 'frontend_available' => \true, 'render_type' => 'template']);
        $this->add_responsive_control('gridtofullscreen3d_duration', ['label' => esc_html__('Duration (s)', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => 1.8], 'range' => ['px' => ['max' => 5, 'min' => 0.3, 'step' => 0.1]], 'frontend_available' => \true]);
        $this->add_control('gridtofullscreen3d_activations', ['label' => esc_html__('Activations', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'default' => 'corners', 'options' => ['corners' => esc_html__('Corners', 'dynamic-content-for-elementor'), 'topLeft' => esc_html__('Top Left', 'dynamic-content-for-elementor'), 'sides' => esc_html__('Sides', 'dynamic-content-for-elementor'), 'top' => esc_html__('Top', 'dynamic-content-for-elementor'), 'left' => esc_html__('Left', 'dynamic-content-for-elementor'), 'bottom' => esc_html__('Bottom', 'dynamic-content-for-elementor'), 'center' => esc_html__('Center', 'dynamic-content-for-elementor'), 'bottomStep' => esc_html__('Bottom Step', 'dynamic-content-for-elementor'), 'sinX' => esc_html__('Sine', 'dynamic-content-for-elementor'), 'mouse' => esc_html__('Mouse', 'dynamic-content-for-elementor'), 'closestCorner' => esc_html__('Closest Corner', 'dynamic-content-for-elementor'), 'closestSide' => esc_html__('Closest Side', 'dynamic-content-for-elementor')], 'frontend_available' => \true, 'render_type' => 'template', 'condition' => [$this->get_control_id('gridtofullscreen3d_effects') => 'custom_effect']]);
        $this->add_control('gridtofullscreen3d_transformation', ['label' => esc_html__('Transformation', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'default' => 'none', 'options' => ['none' => esc_html__('None', 'dynamic-content-for-elementor'), 'flipX' => esc_html__('Flip', 'dynamic-content-for-elementor'), 'simplex' => 'Simplex', 'wavy' => 'Wavy', 'circle' => esc_html__('Circle', 'dynamic-content-for-elementor')], 'frontend_available' => \true, 'render_type' => 'template', 'condition' => [$this->get_control_id('gridtofullscreen3d_effects') => 'custom_effect']]);
        $this->add_control('gridtofullscreen3d_easing_heading', ['label' => esc_html__('Timing equation Easing', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HEADING, 'separator' => 'before', 'condition' => [$this->get_control_id('gridtofullscreen3d_effects') => 'custom_effect']]);
        $this->add_control('gridtofullscreen3d_easing_to_fullscreen_popover', ['label' => esc_html__('To Fullscreen', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::POPOVER_TOGGLE, 'label_off' => esc_html__('Default', 'dynamic-content-for-elementor'), 'label_on' => esc_html__('Custom', 'dynamic-content-for-elementor'), 'condition' => [$this->get_control_id('gridtofullscreen3d_effects') => 'custom_effect']]);
        $this->get_parent()->start_popover();
        $this->add_control('gridtofullscreen3d_easing_morph_to_fullscreen', ['label' => esc_html__('To Fullscreen', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'options' => ['' => esc_html__('Default', 'dynamic-content-for-elementor')] + Helper::get_ease(), 'frontend_available' => \true, 'label_block' => \false, 'condition' => [$this->get_control_id('gridtofullscreen3d_effects') => 'custom_effect', $this->get_control_id('gridtofullscreen3d_easing_to_fullscreen_popover') => 'yes']]);
        $this->add_control('gridtofullscreen3d_easing_morph_ease_to_fullscreen', ['label' => esc_html__('Equation to fullscreen', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'options' => ['' => esc_html__('Default', 'dynamic-content-for-elementor')] + Helper::get_timing_functions(), 'frontend_available' => \true, 'label_block' => \false, 'condition' => [$this->get_control_id('gridtofullscreen3d_effects') => 'custom_effect', $this->get_control_id('gridtofullscreen3d_easing_to_fullscreen_popover') => 'yes']]);
        $this->get_parent()->end_popover();
        $this->add_control('gridtofullscreen3d_easing_to_grid_popover', ['label' => esc_html__('Timing function to Grid', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::POPOVER_TOGGLE, 'label_off' => esc_html__('Default', 'dynamic-content-for-elementor'), 'label_on' => esc_html__('Custom', 'dynamic-content-for-elementor'), 'condition' => [$this->get_control_id('gridtofullscreen3d_effects') => 'custom_effect']]);
        $this->get_parent()->start_popover();
        $this->add_control('gridtofullscreen3d_easing_morph_to_grid', ['label' => esc_html__('Easing to fullscreen', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'options' => ['' => esc_html__('Default', 'dynamic-content-for-elementor')] + Helper::get_ease(), 'frontend_available' => \true, 'label_block' => \false, 'condition' => [$this->get_control_id('gridtofullscreen3d_effects') => 'custom_effect', $this->get_control_id('gridtofullscreen3d_easing_to_grid_popover') => 'yes']]);
        $this->add_control('gridtofullscreen3d_easing_morph_ease_to_grid', ['label' => esc_html__('Equation to fullscreen', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'options' => ['' => esc_html__('Default', 'dynamic-content-for-elementor')] + Helper::get_timing_functions(), 'frontend_available' => \true, 'label_block' => \false, 'condition' => [$this->get_control_id('gridtofullscreen3d_effects') => 'custom_effect', $this->get_control_id('gridtofullscreen3d_easing_to_grid_popover') => 'yes']]);
        $this->get_parent()->end_popover();
        $this->add_control('gridtofullscreen3d_panel_heading', ['label' => esc_html__('Panel', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HEADING, 'separator' => 'before']);
        $this->add_responsive_control('gridtofullscreen3d_panel_position', ['label' => esc_html__('Position', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CHOOSE, 'options' => ['left' => ['title' => esc_html__('Left', 'dynamic-content-for-elementor'), 'icon' => 'eicon-h-align-left'], 'right' => ['title' => esc_html__('Right', 'dynamic-content-for-elementor'), 'icon' => 'eicon-h-align-right'], 'top' => ['title' => esc_html__('Top', 'dynamic-content-for-elementor'), 'icon' => 'eicon-v-align-top'], 'bottom' => ['title' => esc_html__('Bottom', 'dynamic-content-for-elementor'), 'icon' => 'eicon-v-align-bottom']], 'default' => is_rtl() ? 'left' : 'right', 'prefix_class' => 'dce-panel-position%s-', 'frontend_available' => \true, 'render_type' => 'template']);
        $this->add_responsive_control('gridtofullscreen3d_panel_width', ['label' => esc_html__('Width (%)', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'range' => ['%' => ['min' => 0, 'max' => 100]], 'devices' => Helper::get_active_devices_list(), 'desktop_default' => ['size' => 50, 'unit' => '%'], 'tablet_default' => ['size' => 50, 'unit' => '%'], 'mobile_default' => ['size' => 50, 'unit' => '%'], 'frontend_available' => \true, 'condition' => [$this->get_control_id('gridtofullscreen3d_panel_position') => ['left', 'right']]]);
        $this->add_responsive_control('gridtofullscreen3d_panel_height', ['label' => esc_html__('Height (%)', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'range' => ['%' => ['min' => 0, 'max' => 100]], 'devices' => Helper::get_active_devices_list(), 'desktop_default' => ['size' => 50, 'unit' => '%'], 'tablet_default' => ['size' => 50, 'unit' => '%'], 'mobile_default' => ['size' => 50, 'unit' => '%'], 'frontend_available' => \true, 'condition' => [$this->get_control_id('gridtofullscreen3d_panel_position') => ['top', 'bottom']]]);
        $this->add_control('gridtofullscreen3d_template', ['label' => esc_html__('Template', 'dynamic-content-for-elementor'), 'type' => 'ooo_query', 'placeholder' => esc_html__('Template Name', 'dynamic-content-for-elementor'), 'label_block' => \true, 'query_type' => 'posts', 'object_type' => 'elementor_library', 'frontend_available' => \true]);
        $this->add_control('gridtofullscreen3d_panel_background', ['label' => esc_html__('Background', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'default' => '', 'selectors' => ['{{WRAPPER}} .dce-gridtofullscreen3d-container .fullview__item-box' => 'background-color: {{VALUE}};'], 'condition' => [$this->get_control_id('gridtofullscreen3d_template!') => '']]);
        $this->add_control('gridtofullscreen3d_panel_title_heading', ['label' => esc_html__('Title', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HEADING, 'separator' => 'before']);
        $this->add_control('gridtofullscreen3d_panel_title_color', ['label' => esc_html__('Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'default' => '', 'selectors' => ['{{WRAPPER}} .dce-gridtofullscreen3d-container .fullview__item-title' => 'color: {{VALUE}};']]);
        $this->add_group_control(Group_Control_Typography::get_type(), ['name' => 'gridtofullscreen3d_panel_title_typography', 'label' => esc_html__('Typography', 'dynamic-content-for-elementor'), 'selector' => '{{WRAPPER}} .dce-gridtofullscreen3d-container .fullview__item-title']);
        $this->add_responsive_control('gridtofullscreen3d_panel_title_padding', ['label' => esc_html__('Padding', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => ['px', '%', 'em'], 'selectors' => ['{{WRAPPER}} .dce-gridtofullscreen3d-container .fullview__item-title' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};']]);
        $this->end_controls_section();
    }
    /**
     * @param array<string,mixed> $settings
     * @return void
     */
    protected function render_featured_image(array $settings)
    {
        $use_bgimage = $settings['use_bgimage'];
        $use_overlay = $settings['use_overlay'];
        $use_overlay_hover = $this->get_parent()->get_settings('use_overlay_hover');
        $use_link = $settings['use_link'];
        $setting_key = $settings['thumbnail_size_size'];
        $html_tag = $use_link ? 'a' : 'div';
        $image_attr = ['class' => $this->get_image_class()];
        $image_url = Group_Control_Image_Size::get_attachment_image_src(get_post_thumbnail_id(), 'thumbnail_size', $settings);
        $thumbnail_html = wp_get_attachment_image(get_post_thumbnail_id(), $setting_key, \false, $image_attr);
        if (empty($thumbnail_html) && !empty($settings['featured_image_fallback'])) {
            $thumbnail_html = wp_get_attachment_image($settings['featured_image_fallback']['id'], $setting_key, \false, $image_attr);
        }
        if (empty($thumbnail_html)) {
            echo '<div class="dce-post-image dce-no-image"></div>';
            return;
        }
        $this->get_parent()->remove_render_attribute('post_image');
        $this->get_parent()->add_render_attribute('post_image', ['class' => ['dce-post-image', $use_bgimage ? 'dce-post-bgimage' : '', $use_overlay ? 'dce-post-overlayimage' : '', $use_overlay_hover ? 'dce-post-overlayhover' : '']]);
        if ($use_link) {
            $this->get_parent()->add_render_attribute('post_image', 'href', esc_url($this->current_permalink));
        }
        echo '<' . $html_tag . ' ' . $this->get_parent()->get_render_attribute_string('post_image') . '>';
        echo $thumbnail_html;
        $this->render_image_large($settings);
        echo '</' . $html_tag . '>';
    }
    /**
     * @return void
     */
    protected function render_posts_before()
    {
        $this->get_parent()->remove_render_attribute('fullscreen_effect');
        $this->get_parent()->add_render_attribute('fullscreen_effect', ['id' => 'fullscreen-effect']);
        echo '<div ' . $this->get_parent()->get_render_attribute_string('fullscreen_effect') . '></div>';
    }
    /**
     * @return void
     */
    protected function render_posts_after()
    {
        $query = $this->get_parent()->get_query();
        if (!$query->found_posts) {
            return;
        }
        $this->get_parent()->remove_render_attribute('fullview');
        $this->get_parent()->add_render_attribute('fullview', ['class' => 'fullview']);
        $this->get_parent()->remove_render_attribute('fullview_close');
        $this->get_parent()->add_render_attribute('fullview_close', ['class' => 'fullview__close', 'aria-label' => esc_html__('Close preview', 'dynamic-content-for-elementor')]);
        echo '<div ' . $this->get_parent()->get_render_attribute_string('fullview') . '>';
        if ($query->in_the_loop) {
            $this->current_permalink = get_permalink();
            $this->current_id = get_the_ID();
            $this->render_fullview_item();
        } else {
            while ($query->have_posts()) {
                $query->the_post();
                $this->current_permalink = get_permalink();
                $this->current_id = get_the_ID();
                $this->render_fullview_item();
            }
        }
        wp_reset_postdata();
        echo '<button ' . $this->get_parent()->get_render_attribute_string('fullview_close') . '>
		    <svg aria-hidden="true" width="24" height="22px" viewBox="0 0 24 22">
		      <path d="M11 9.586L20.192.393l1.415 1.415L12.414 11l9.193 9.192-1.415 1.415L11 12.414l-9.192 9.193-1.415-1.415L9.586 11 .393 1.808 1.808.393 11 9.586z" />
		    </svg>
		  </button>';
        echo '</div>';
    }
    /**
     * @return void
     */
    public function render_fullview_item()
    {
        $panel_template_id = $this->get_instance_value('gridtofullscreen3d_template');
        $title = get_the_title() ? wp_kses_post(get_the_title()) : the_ID();
        $this->get_parent()->remove_render_attribute('fullview_item');
        $this->get_parent()->add_render_attribute('fullview_item', ['class' => 'fullview__item']);
        $this->get_parent()->remove_render_attribute('fullview_item_title');
        $this->get_parent()->add_render_attribute('fullview_item_title', ['class' => 'fullview__item-title']);
        $this->get_parent()->remove_render_attribute('fullview_item_box');
        $this->get_parent()->add_render_attribute('fullview_item_box', ['class' => 'fullview__item-box']);
        ?>
		<div <?php 
        echo $this->get_parent()->get_render_attribute_string('fullview_item');
        ?>>
			<h2 <?php 
        echo $this->get_parent()->get_render_attribute_string('fullview_item_title');
        ?>><?php 
        echo $title;
        ?></h2>
			<?php 
        if ($panel_template_id) {
            ?>
				<div <?php 
            echo $this->get_parent()->get_render_attribute_string('fullview_item_box');
            ?>>
					<?php 
            $this->render_template($panel_template_id);
            ?>
				</div>
			<?php 
        }
        ?>
		</div>
		<?php 
    }
    /**
     * @param array<string,mixed> $settings
     * @return void
     */
    public function render_image_large(array $settings)
    {
        $image_url = wp_get_attachment_image_src(get_post_thumbnail_id(), 'full');
        if (empty($image_url) && !empty($settings['featured_image_fallback'])) {
            $image_url = wp_get_attachment_image_src($settings['featured_image_fallback']['id'], 'full');
        }
        if (!empty($image_url[0])) {
            $this->get_parent()->remove_render_attribute('image_large');
            $this->get_parent()->add_render_attribute('image_large', ['class' => ['grid__item-img', 'grid__item-img--large'], 'src' => esc_url($image_url[0])]);
            echo '<img ' . $this->get_parent()->get_render_attribute_string('image_large') . ' />';
        }
    }
    /**
     * @return string
     */
    public function get_container_class()
    {
        $base_class = 'dce-gridtofullscreen3d-container dce-skin-' . $this->get_id() . ' dce-skin-' . parent::get_id() . ' dce-skin-' . parent::get_id() . '-' . $this->get_instance_value('grid_type');
        $panel_position = $this->get_instance_value('gridtofullscreen3d_gridtofullscreen3d_panel_position');
        if (!\in_array($panel_position, ['left', 'right', 'top', 'bottom'], \true)) {
            $panel_position = 'right';
        }
        $base_class .= ' dce-panel-position-' . $panel_position;
        return $base_class;
    }
    /**
     * @return string
     */
    public function get_wrapper_class()
    {
        return 'dce-gridtofullscreen3d-wrapper dce-wrapper-' . $this->get_id() . ' dce-wrapper-' . parent::get_id();
    }
    /**
     * @return string
     */
    public function get_item_class()
    {
        return 'dce-gridtofullscreen3d-item dce-item-' . $this->get_id() . ' dce-item-' . parent::get_id();
    }
    /**
     * @return string
     */
    public function get_image_class()
    {
        return 'grid__item-img';
    }
}
