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
use Elementor\Group_Control_Css_Filter;
use DynamicContentForElementor\Helper;
if (!\defined('ABSPATH')) {
    exit;
    // Exit if accessed directly
}
class SkinCrossroadsSlideshow extends \DynamicContentForElementor\Includes\Skins\SkinBase
{
    /**
     * @var array<string>
     */
    public $depended_scripts = ['swiper', 'dce-dynamicPosts-crossroadsslideshow'];
    /**
     * @var array<string>
     */
    public $depended_styles = ['dce-dynamicPosts-crossroadsslideshow', 'swiper'];
    /**
     * @return void
     */
    protected function _register_controls_actions()
    {
        add_action('elementor/element/' . $this->get_parent()->get_name() . '/section_query/after_section_end', [$this, 'register_controls_layout']);
        add_action('elementor/element/' . $this->get_parent()->get_name() . '/section_dynamicposts/after_section_end', [$this, 'register_additional_crossroadsslideshow_controls']);
    }
    /**
     * @return string
     */
    public function get_id()
    {
        return 'crossroadsslideshow';
    }
    /**
     * @return string
     */
    public function get_title()
    {
        return esc_html__('Crossroads Slideshow', 'dynamic-content-for-elementor');
    }
    /**
     * @param \DynamicContentForElementor\Widgets\DynamicPostsBase $widget
     * @return void
     */
    public function register_additional_crossroadsslideshow_controls(\DynamicContentForElementor\Widgets\DynamicPostsBase $widget)
    {
        $this->parent = $widget;
        $this->start_controls_section('section_crossroadsslideshow', ['label' => $this->get_title(), 'tab' => Controls_Manager::TAB_CONTENT]);
        $this->add_control('title_html_tag', ['label' => __('Title HTML Tag', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'options' => ['h1' => 'H1', 'h2' => 'H2', 'h3' => 'H3', 'h4' => 'H4', 'h5' => 'H5', 'h6' => 'H6', 'div' => 'div', 'span' => 'span', 'p' => 'p'], 'default' => 'h3']);
        $this->add_control('featured_image_fallback', ['label' => __('Featured Image - Fallback', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::MEDIA, 'default' => ['url' => \Elementor\Utils::get_placeholder_image_src()]]);
        $this->add_control('slideshow_layout_heading_item_image', ['label' => __('Item Image Overlay', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HEADING, 'separator' => 'before']);
        $this->start_controls_tabs('tabs_slideshow_background_overlay');
        $this->start_controls_tab('tab_slideshow_background_overlay_center', ['label' => __('Center', 'dynamic-content-for-elementor')]);
        $this->add_group_control(Group_Control_Background::get_type(), ['name' => 'slideshow_background_overlay_center', 'selector' => '{{WRAPPER}} .dce-grid__item--center .dce-post-block .dce-img-wrap .dce-img-background-overlay']);
        $this->add_control('slideshow_background_overlay_opacity_center', ['label' => __('Opacity', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => 0.5], 'range' => ['px' => ['max' => 1, 'step' => 0.01]], 'selectors' => ['{{WRAPPER}} .dce-grid__item--center .dce-post-block .dce-img-wrap .dce-img-background-overlay' => 'opacity: {{SIZE}};'], 'condition' => [$this->get_control_id('slideshow_background_overlay_center_background') => ['classic', 'gradient']]]);
        $this->add_group_control(Group_Control_Css_Filter::get_type(), ['name' => 'slideshow_css_filters_center', 'selector' => '{{WRAPPER}} .dce-grid__item--center .dce-post-block .dce-img-wrap .dce-img-background-overlay']);
        $this->end_controls_tab();
        $this->start_controls_tab('tab_slideshow_background_overlay_normal', ['label' => __('Normal', 'dynamic-content-for-elementor')]);
        $this->add_group_control(Group_Control_Background::get_type(), ['name' => 'slideshow_background_overlay_normal', 'selector' => '{{WRAPPER}} .dce-grid__item:not(.dce-grid__item--center) .dce-post-block .dce-img-wrap .dce-img-background-overlay']);
        $this->add_control('slideshow_background_overlay_opacity_normal', ['label' => __('Opacity', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => 0.5], 'range' => ['px' => ['max' => 1, 'step' => 0.01]], 'selectors' => ['{{WRAPPER}} .dce-grid__item:not(.dce-grid__item--center) .dce-post-block .dce-img-wrap .dce-img-background-overlay' => 'opacity: {{SIZE}};'], 'condition' => [$this->get_control_id('slideshow_background_overlay_normal_background') => ['classic', 'gradient']]]);
        $this->add_group_control(Group_Control_Css_Filter::get_type(), ['name' => 'slideshow_css_filters_normal', 'selector' => '{{WRAPPER}} .dce-grid__item:not(.dce-grid__item--center) .dce-post-block .dce-img-wrap .dce-img-background-overlay']);
        $this->add_control('slideshow_overlay_blend_mode_normal', ['label' => __('Blend Mode', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'options' => ['' => __('Normal', 'dynamic-content-for-elementor'), 'multiply' => __('Multiply', 'dynamic-content-for-elementor'), 'screen' => __('Screen', 'dynamic-content-for-elementor'), 'overlay' => __('Overlay', 'dynamic-content-for-elementor'), 'darken' => __('Darken', 'dynamic-content-for-elementor'), 'lighten' => __('Lighten', 'dynamic-content-for-elementor'), 'color-dodge' => __('Color Dodge', 'dynamic-content-for-elementor'), 'saturation' => __('Saturation', 'dynamic-content-for-elementor'), 'color' => __('Color', 'dynamic-content-for-elementor'), 'luminosity' => __('Luminosity', 'dynamic-content-for-elementor')], 'selectors' => ['{{WRAPPER}} .dce-grid__item:not(.dce-grid__item--center) .dce-post-block .dce-img-wrap .dce-img-background-overlay' => 'mix-blend-mode: {{VALUE}}']]);
        $this->end_controls_tab();
        $this->end_controls_tabs();
        $this->end_controls_section();
    }
    /**
     * @return void
     */
    protected function register_style_controls()
    {
        parent::register_style_controls();
        $this->start_controls_section('section_style_crossroadsslideshow_grid', ['label' => $this->get_title(), 'tab' => Controls_Manager::TAB_STYLE]);
        $this->add_control('slideshow_style_background_color', ['label' => esc_html__('Background Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'default' => '#F4F4F4', 'selectors' => ['{{WRAPPER}} .dce-skin-crossroadsslideshow' => 'background-color: {{VALUE}};']]);
        $this->add_control('slideshow_style_heading_item_title', ['label' => esc_html__('Item Title', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HEADING, 'separator' => 'before']);
        $this->add_group_control(Group_Control_Typography::get_type(), ['name' => 'slideshow_item_title_typography', 'label' => esc_html__('Typography', 'dynamic-content-for-elementor'), 'default' => '', 'selector' => '{{WRAPPER}} .dce-post-title']);
        $this->add_control('slideshow_item_title_color', ['label' => esc_html__('Title Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'default' => '#000000', 'selectors' => ['{{WRAPPER}} .dce-post-title' => 'color: {{VALUE}};']]);
        $this->add_control('slideshow_style_heading_item_number', ['label' => __('Item Number', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HEADING, 'separator' => 'before']);
        $this->add_group_control(Group_Control_Typography::get_type(), ['name' => 'slideshow_item_number_typography', 'label' => __('Typography', 'dynamic-content-for-elementor'), 'default' => '', 'selector' => '{{WRAPPER}} .dce-grid__item .dce-number']);
        $this->add_control('slideshow_item_number_text_stroke_color', ['label' => __('Text Stroke Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'default' => '', 'selectors' => ['{{WRAPPER}} .dce-grid__item .dce-number' => '-webkit-text-stroke-color: {{VALUE}};']]);
        $this->add_responsive_control('slideshow_item_number_text_stroke_width', ['label' => __('Text Stroke Width', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'range' => ['px' => ['min' => 0, 'max' => 10, 'step' => 0.1]], 'default' => ['size' => '', 'unit' => 'px'], 'tablet_default' => ['size' => '', 'unit' => 'px'], 'mobile_default' => ['size' => '', 'unit' => 'px'], 'size_units' => ['px'], 'selectors' => ['{{WRAPPER}} .dce-grid__item .dce-number' => '-webkit-text-stroke-width: {{SIZE}}{{UNIT}};']]);
        $this->add_control('slideshow_item_number_text_fill_color', ['label' => __('Text Fill Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'default' => '', 'selectors' => ['{{WRAPPER}} .dce-grid__item .dce-number' => 'color: {{VALUE}}; -webkit-text-fill-color: {{VALUE}};']]);
        $this->end_controls_section();
    }
    /**
     * @return void
     */
    protected function render_post()
    {
        $this->render_post_items();
    }
    /**
     * @return void
     */
    protected function render_posts_before()
    {
        ?>
		<div class="dce-skin-<?php 
        echo $this->get_id();
        ?>">
			<div class="swiper">
				<div class="swiper-wrapper">
				<?php 
        $this->counter = -1;
        $this->get_parent()->query_posts();
        $query = $this->get_parent()->get_query();
        if (!$query->found_posts) {
            return;
        }
        if ($query->in_the_loop) {
            $this->current_permalink = get_permalink();
            $this->current_id = get_the_ID();
            ++$this->counter;
            $this->render_post_items();
        } else {
            while ($query->have_posts()) {
                $query->the_post();
                $this->current_permalink = get_permalink();
                $this->current_id = get_the_ID();
                ++$this->counter;
                $this->render_post_items();
            }
        }
        wp_reset_postdata();
        ?>
				</div>
			</div>
			</div>
		<?php 
    }
    /**
     * @return void
     */
    protected function render_post_items()
    {
        $prefix0 = '0';
        $numberlabel = $this->counter + 1;
        if ($numberlabel > 9) {
            $prefix0 = '';
        }
        $image_url = wp_get_attachment_image_src(get_post_thumbnail_id(), 'full');
        $featured_image_fallback = $this->get_instance_value('featured_image_fallback');
        $display_image_url = $image_url[0] ?? $featured_image_fallback['url'] ?? \Elementor\Utils::get_placeholder_image_src();
        $title_tag = Helper::validate_html_tag($this->get_instance_value('title_html_tag'));
        ?>
		<div class="swiper-slide">
			<div class="dce-grid__item">
				<a href="<?php 
        echo esc_url($this->current_permalink);
        ?>" class="dce-post-link">
					<span class="dce-number"><?php 
        echo $prefix0 . $numberlabel;
        ?></span>
					<div class="dce-post-block">
						<div class="dce-post-image">
							<div class="dce-img-wrap">
								<div class="dce-img-background-overlay"></div>
								<div class="dce-img-el" style="background-image: url(<?php 
        echo esc_url($display_image_url);
        ?>);"></div>
							</div>
						</div>
						<<?php 
        echo $title_tag;
        ?> class="dce-post-title">
							<?php 
        echo wp_kses_post(get_the_title());
        ?>
						</<?php 
        echo $title_tag;
        ?>>
					</div>
				</a>
			</div>
		</div>
		<?php 
    }
    /**
     * @return void
     */
    protected function render_loop_end()
    {
        $this->render_posts_after();
    }
    /**
     * @return void
     */
    public function render_title_item()
    {
        ?>
		<h3 class="dce-grid__item dce-grid__item--title elementor-repeater-item-item_title">
			<?php 
        echo wp_kses_post(get_the_title());
        ?>
		</h3>
		<?php 
    }
    /**
     * @return string
     */
    public function get_container_class()
    {
        return 'dce-skin-' . $this->get_id();
    }
    /**
     * @return string
     */
    public function get_wrapper_class()
    {
        return 'dce-grid-crossroadsslideshow dce-grid--slideshow dce-crossroadsslideshow-wrapper dce-wrapper-' . $this->get_id();
    }
    /**
     * @return string
     */
    public function get_item_class()
    {
        return 'dce-grid__item dce-grid__item--slide dce-crossroadsslideshow-item dce-item-' . $this->get_id();
    }
    /**
     * @return string
     */
    public function get_image_class()
    {
        return 'dce-img-el';
    }
    /**
     * @return void
     */
    public function render()
    {
        $this->render_posts_before();
        $this->render_loop_end();
    }
}
