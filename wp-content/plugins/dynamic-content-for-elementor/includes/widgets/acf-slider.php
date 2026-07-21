<?php

// SPDX-FileCopyrightText: 2018-2026 Ovation S.r.l. <help@dynamic.ooo>
// SPDX-License-Identifier: GPL-3.0-or-later
namespace DynamicContentForElementor\Widgets;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Background;
use Elementor\Utils;
use Elementor\Icons_Manager;
use DynamicContentForElementor\Helper;
use DynamicContentForElementor\Controls\Group_Control_Filters_CSS;
// Exit if accessed directly
if (!\defined('ABSPATH')) {
    exit;
}
class AcfSlider extends \DynamicContentForElementor\Widgets\WidgetPrototype
{
    public function run_once()
    {
        parent::run_once();
        $save_guard = \DynamicContentForElementor\Plugin::instance()->save_guard;
        $save_guard->register_unsafe_control($this->get_name(), 'other_post_source');
    }
    public function get_script_depends()
    {
        return ['swiper', 'imagesloaded', 'dce-acfslider-js'];
    }
    public function get_style_depends()
    {
        return ['swiper', 'dce-acfslider', 'e-swiper'];
    }
    /**
     * Register controls after check if this feature is only for admin
     *
     * @return void
     */
    protected function safe_register_controls()
    {
        $swiper_class = 'swiper';
        $this->start_controls_section('section_content', ['label' => esc_html__('ACF Slider', 'dynamic-content-for-elementor')]);
        $this->add_control('acf_field_list', ['label' => esc_html__('ACF Gallery Field', 'dynamic-content-for-elementor'), 'type' => 'ooo_query', 'placeholder' => esc_html__('Select the field...', 'dynamic-content-for-elementor'), 'label_block' => \true, 'query_type' => 'acf', 'object_type' => 'gallery']);
        $this->add_control('acf_gallery_from', ['label' => esc_html__('Retrieve the field from', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'default' => 'current_post', 'options' => ['current_post' => esc_html__('Current Post', 'dynamic-content-for-elementor'), 'current_user' => esc_html__('Current User', 'dynamic-content-for-elementor'), 'current_author' => esc_html__('Current Author', 'dynamic-content-for-elementor'), 'current_term' => esc_html__('Current Term', 'dynamic-content-for-elementor'), 'options_page' => esc_html__('Options Page', 'dynamic-content-for-elementor')]]);
        $this->add_responsive_control('align', ['label' => esc_html__('Alignment', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CHOOSE, 'options' => ['left' => ['title' => esc_html__('Left', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-left'], 'center' => ['title' => esc_html__('Center', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-center'], 'right' => ['title' => esc_html__('Right', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-right']], 'default' => '', 'prefix_class' => 'align-', 'selectors' => ['{{WRAPPER}} .dynamic_acfslider' => 'text-align: {{VALUE}};']]);
        $this->add_control('mode_heading', ['label' => esc_html__('Mode', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HEADING, 'separator' => 'before']);
        $this->add_control('force_width', ['label' => esc_html__('Force Width', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'return_value' => 'yes', 'render_type' => 'template', 'condition' => ['force_height' => '', 'use_bg_image' => '']]);
        $this->add_responsive_control('size_img', ['label' => esc_html__('Size (%)', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'size_units' => ['%'], 'default' => ['unit' => '%', 'size' => 100], 'tablet_default' => ['unit' => '%'], 'mobile_default' => ['unit' => '%'], 'range' => ['%' => ['min' => 0, 'max' => 100]], 'selectors' => ['{{WRAPPER}} .wrap-item-acfslider' => 'width: {{SIZE}}{{UNIT}};'], 'condition' => ['force_width' => 'yes']]);
        $this->add_control('force_height', ['label' => esc_html__('Force Height', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => '', 'prefix_class' => 'forceheignt-', 'render_type' => 'template', 'condition' => ['force_width' => '', 'use_bg_image' => '']]);
        $this->add_responsive_control('height', ['label' => esc_html__('Height', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'description' => esc_html__('If the value is empty the height is automatic.', 'dynamic-content-for-elementor'), 'default' => ['size' => ''], 'size_units' => ['px', 'rem', 'vh'], 'range' => ['rem' => ['min' => 0, 'max' => 100], 'px' => ['min' => 0, 'max' => 1200], 'vw' => ['min' => 0, 'max' => 100]], 'selectors' => ['{{WRAPPER}} .dyncontel-swiper .' . $swiper_class => 'height: {{SIZE}}{{UNIT}};'], 'frontend_available' => \true, 'condition' => ['force_height' => 'yes']]);
        $this->add_control('use_bg_image', ['label' => esc_html__('Use as a background image', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => '', 'condition' => ['force_width' => '', 'force_height' => '']]);
        $this->add_control('bg_position', ['label' => esc_html__('Background position', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'default' => 'center center', 'options' => ['' => esc_html__('Default', 'dynamic-content-for-elementor'), 'top left' => esc_html__('Top Left', 'dynamic-content-for-elementor'), 'top center' => esc_html__('Top Center', 'dynamic-content-for-elementor'), 'top right' => esc_html__('Top Right', 'dynamic-content-for-elementor'), 'center left' => esc_html__('Center Left', 'dynamic-content-for-elementor'), 'center center' => esc_html__('Center Center', 'dynamic-content-for-elementor'), 'center right' => esc_html__('Center Right', 'dynamic-content-for-elementor'), 'bottom left' => esc_html__('Bottom Left', 'dynamic-content-for-elementor'), 'bottom center' => esc_html__('Bottom Center', 'dynamic-content-for-elementor'), 'bottom right' => esc_html__('Bottom Right', 'dynamic-content-for-elementor')], 'selectors' => ['{{WRAPPER}} .acfslider-bg-image' => 'background-position: {{VALUE}};'], 'condition' => ['use_bg_image' => 'yes']]);
        $this->add_responsive_control('height_bg_img', ['label' => esc_html__('Background Height', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'size_units' => ['px', 'vh'], 'default' => ['unit' => 'px', 'size' => 400], 'tablet_default' => ['unit' => 'px'], 'mobile_default' => ['unit' => 'px'], 'range' => ['px' => ['min' => 80, 'max' => 800, 'step' => 1], 'vh' => ['min' => 0, 'max' => 100, 'step' => 1]], 'selectors' => ['{{WRAPPER}} .swiper-slide' => 'height: {{SIZE}}{{UNIT}};'], 'condition' => ['use_bg_image' => 'yes']]);
        $this->add_control('space_heading', ['label' => esc_html__('Space', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HEADING, 'separator' => 'before']);
        $this->add_responsive_control('spaceV', ['label' => esc_html__('Vertical space', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => '', 'unit' => 'px'], 'size_units' => ['px', 'em', 'vh'], 'range' => ['em' => ['min' => 0, 'max' => 30], 'px' => ['min' => 0, 'max' => 150], 'vw' => ['min' => 0, 'max' => 50]], 'selectors' => ['{{WRAPPER}} .dyncontel-swiper .' . $swiper_class => 'padding-top: {{SIZE}}{{UNIT}}; padding-bottom: {{SIZE}}{{UNIT}};'], 'frontend_available' => \true]);
        $this->add_responsive_control('spaceH', ['label' => esc_html__('Horizontal space', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => '', 'unit' => 'px'], 'size_units' => ['px', 'em', 'vh'], 'range' => ['em' => ['min' => 0, 'max' => 30], 'px' => ['min' => 0, 'max' => 150], 'vw' => ['min' => 0, 'max' => 50]], 'selectors' => ['{{WRAPPER}} .dyncontel-swiper .' . $swiper_class => 'padding-left: {{SIZE}}{{UNIT}}; padding-right: {{SIZE}}{{UNIT}};'], 'frontend_available' => \true]);
        $this->end_controls_section();
        $this->start_controls_section('section_settings', ['label' => esc_html__('Image Settings', 'dynamic-content-for-elementor')]);
        $this->add_group_control(Group_Control_Image_Size::get_type(), ['name' => 'size', 'label' => esc_html__('Image Size', 'dynamic-content-for-elementor'), 'default' => 'large', 'condition' => []]);
        $this->add_control('use_desc', ['label' => esc_html__('Description', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'options' => ['' => esc_html__('None', 'dynamic-content-for-elementor'), 'caption' => esc_html__('Caption', 'dynamic-content-for-elementor'), 'description' => esc_html__('Description', 'dynamic-content-for-elementor')], 'default' => '']);
        $this->add_control('style_heading', ['label' => esc_html__('Style', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HEADING, 'separator' => 'before']);
        $this->add_control('enable_image_style', ['label' => esc_html__('Style', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => '']);
        $this->add_group_control(Group_Control_Border::get_type(), ['name' => 'image_border', 'label' => esc_html__('Image Border', 'dynamic-content-for-elementor'), 'selector' => '{{WRAPPER}} .wrap-item-acfslider img', 'condition' => ['enable_image_style' => 'yes']]);
        $this->add_control('image_border_radius', ['label' => esc_html__('Border Radius', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => ['px', '%'], 'selectors' => ['{{WRAPPER}} .wrap-item-acfslider img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'], 'condition' => ['enable_image_style' => 'yes']]);
        $this->add_group_control(Group_Control_Box_Shadow::get_type(), ['name' => 'image_box_shadow', 'selector' => '{{WRAPPER}} .acfslider-item img']);
        $this->add_group_control(Group_Control_Filters_CSS::get_type(), ['name' => 'filters_image', 'label' => esc_html__('Filters image', 'dynamic-content-for-elementor'), 'selector' => '{{WRAPPER}} .acfslider-item img']);
        $this->end_controls_section();
        $this->start_controls_section('section_swiper_settings', ['label' => esc_html__('Slider Settings', 'dynamic-content-for-elementor')]);
        $this->add_control('effects', ['label' => esc_html__('Transition effect', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'options' => ['slide' => esc_html__('Slide', 'dynamic-content-for-elementor'), 'fade' => esc_html__('Fade', 'dynamic-content-for-elementor'), 'cube' => esc_html__('Cube', 'dynamic-content-for-elementor'), 'coverflow' => esc_html__('Coverflow', 'dynamic-content-for-elementor'), 'flip' => esc_html__('Flip', 'dynamic-content-for-elementor')], 'default' => 'slide', 'frontend_available' => \true]);
        $this->add_control('speedSlide', ['label' => esc_html__('Speed', 'dynamic-content-for-elementor'), 'description' => esc_html__('Duration of transition between slides (in ms)', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::NUMBER, 'default' => 300, 'min' => 0, 'max' => 3000, 'step' => 10, 'frontend_available' => \true]);
        $this->add_control('centeredSlides', ['label' => esc_html__('Centered Slides', 'dynamic-content-for-elementor'), 'description' => esc_html__('If true, then active slide will be centered, not always on the left side.', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => '', 'frontend_available' => \true]);
        $this->add_responsive_control('spaceBetween', ['label' => esc_html__('Space Between', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => '0', 'unit' => 'px'], 'tablet_default' => ['unit' => 'px'], 'mobile_default' => ['unit' => 'px'], 'size_units' => ['px'], 'range' => ['px' => ['min' => 0, 'max' => 100, 'step' => 1]], 'frontend_available' => \true, 'separator' => 'before']);
        $this->add_control('more_options', ['label' => esc_html__('Slides Grid', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HEADING, 'separator' => 'before']);
        $this->add_responsive_control('slidesPerView', ['label' => esc_html__('Slides Per View', 'dynamic-content-for-elementor'), 'description' => esc_html__('Number of slides per view (slides visible at the same time on slider\'s container). If you use it with "auto" value and along with loop: true then you need to specify loopedSlides parameter with amount of slides to loop (duplicate). SlidesPerView: \'auto\' is currently not compatible with multirow mode, when slidesPerColumn greater than one', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::NUMBER, 'default' => '1', 'min' => 1, 'max' => 12, 'step' => 1, 'frontend_available' => \true]);
        $this->add_responsive_control('slidesColumn', ['label' => esc_html__('Rows', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::NUMBER, 'default' => '1', 'min' => 1, 'max' => 4, 'step' => 1, 'frontend_available' => \true]);
        $this->add_responsive_control('slidesPerGroup', ['label' => esc_html__('Slides Per Group', 'dynamic-content-for-elementor'), 'description' => esc_html__('Set numbers of slides to define and enable group sliding. Useful to use with slidesPerView > 1', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::NUMBER, 'default' => 1, 'tablet_default' => '', 'mobile_default' => '', 'min' => 1, 'max' => 12, 'step' => 1, 'frontend_available' => \true]);
        $this->end_controls_section();
        $this->start_controls_section('section_swiper_navigation', ['label' => esc_html__('Navigation', 'dynamic-content-for-elementor')]);
        $this->add_control('useNavigation', ['label' => esc_html__('Navigation', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => 'yes']);
        $this->add_control('arrows_heading', ['label' => esc_html__('Arrows', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HEADING, 'separator' => 'before', 'condition' => ['useNavigation' => 'yes']]);
        $this->add_control('arrows', ['label' => esc_html__('Arrows', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'options' => ['legacy' => esc_html__('Legacy', 'dynamic-content-for-elementor'), 'custom' => esc_html__('Custom', 'dynamic-content-for-elementor')], 'default' => 'legacy', 'condition' => ['useNavigation' => 'yes']]);
        $this->add_control('previous_arrow', ['label' => esc_html__('Left', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::ICONS, 'default' => ['value' => 'eicon-chevron-left', 'library' => 'eicons'], 'condition' => ['useNavigation' => 'yes', 'arrows' => 'custom']]);
        $this->add_control('next_arrow', ['label' => esc_html__('Right', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::ICONS, 'default' => ['value' => 'eicon-chevron-right', 'library' => 'eicons'], 'condition' => ['useNavigation' => 'yes', 'arrows' => 'custom']]);
        $this->add_responsive_control('navigation_size', ['label' => esc_html__('Navigation size', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => '48', 'unit' => 'px'], 'tablet_default' => ['unit' => 'px'], 'mobile_default' => ['unit' => 'px'], 'size_units' => ['px'], 'range' => ['px' => ['min' => 0, 'max' => 100, 'step' => 1]], 'selectors' => ['{{WRAPPER}} .dynamic_acfslider .swiper-button-prev, {{WRAPPER}} .dynamic_acfslider .swiper-button-next' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}}; margin-top: calc(-{{SIZE}}{{UNIT}} / 2);', '{{WRAPPER}} .dynamic_acfslider .swiper-button-prev i, {{WRAPPER}} .dynamic_acfslider .swiper-button-next i' => 'font-size: {{SIZE}}{{UNIT}};', '{{WRAPPER}} .dynamic_acfslider .swiper-button-prev svg, {{WRAPPER}} .dynamic_acfslider .swiper-button-next svg' => 'height: {{SIZE}}{{UNIT}}; width: 100%;'], 'condition' => ['useNavigation' => 'yes']]);
        $this->add_responsive_control('navigation_scale', ['label' => esc_html__('Arrows scale', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => '1', 'unit' => 'px'], 'tablet_default' => ['unit' => 'px'], 'mobile_default' => ['unit' => 'px'], 'size_units' => ['px'], 'range' => ['px' => ['min' => 0, 'max' => 1, 'step' => 0.1]], 'selectors' => ['{{WRAPPER}} .dynamic_acfslider .swiper-button-prev svg, {{WRAPPER}} .dynamic_acfslider .swiper-button-next svg' => '-webkit-transform: scale({{SIZE}}); -ms-transform: scale({{SIZE}}); transform: scale({{SIZE}});'], 'condition' => ['useNavigation' => 'yes', 'arrows' => 'legacy']]);
        $this->add_responsive_control('navigation_position', ['label' => esc_html__('Horizontal position', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => '10', 'unit' => 'px'], 'size_units' => ['px', '%'], 'range' => ['px' => ['max' => 100, 'min' => -100, 'step' => 1], '%' => ['max' => 100, 'min' => -100, 'step' => 1]], 'selectors' => ['{{WRAPPER}} .swiper-button-prev' => 'left: {{SIZE}}{{UNIT}};', '{{WRAPPER}} .swiper-button-next' => 'right: {{SIZE}}{{UNIT}};'], 'condition' => ['useNavigation' => 'yes']]);
        $this->add_responsive_control('vertical_navigation_position', ['label' => esc_html__('Vertical position', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => 50, 'unit' => '%'], 'size_units' => ['px', '%'], 'range' => ['px' => ['max' => 200, 'min' => -200, 'step' => 1], '%' => ['max' => 150, 'min' => -150, 'step' => 1]], 'selectors' => ['{{WRAPPER}} .swiper-button-prev, {{WRAPPER}} .swiper-button-next' => 'top: {{SIZE}}{{UNIT}};'], 'condition' => ['useNavigation' => 'yes']]);
        $this->add_control('navigation_arrow_color', ['label' => esc_html__('Arrows color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'default' => '#000000', 'selectors' => ['{{WRAPPER}} .swiper-button-next path, {{WRAPPER}} .swiper-button-prev path' => 'fill: {{VALUE}};', '{{WRAPPER}} .swiper-button-next line, {{WRAPPER}} .swiper-button-prev line, {{WRAPPER}} .swiper-button-next polyline, {{WRAPPER}} .swiper-button-prev polyline' => 'stroke: {{VALUE}};', '{{WRAPPER}} .dynamic_acfslider .swiper-button-prev svg, {{WRAPPER}} .dynamic_acfslider .swiper-button-next svg' => 'fill: {{VALUE}};', '{{WRAPPER}} .dynamic_acfslider .swiper-button-prev i, {{WRAPPER}} .dynamic_acfslider .swiper-button-next i' => 'color: {{VALUE}};'], 'condition' => ['useNavigation' => 'yes']]);
        $this->add_control('navigation_arrow_color_hover', ['label' => esc_html__('Arrow color hover', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'default' => '#007aff', 'selectors' => ['{{WRAPPER}} .swiper-button-next:hover path, {{WRAPPER}} .swiper-button-prev:hover path' => 'fill: {{VALUE}};', '{{WRAPPER}} .swiper-button-next:hover line, {{WRAPPER}} .swiper-button-prev:hover line, {{WRAPPER}} .swiper-button-next:hover polyline, {{WRAPPER}} .swiper-button-prev:hover polyline' => 'stroke: {{VALUE}};', '{{WRAPPER}} .dynamic_acfslider .swiper-button-prev:hover svg, {{WRAPPER}} .dynamic_acfslider .swiper-button-next:hover svg' => 'fill: {{VALUE}};', '{{WRAPPER}} .dynamic_acfslider .swiper-button-prev:hover i, {{WRAPPER}} .dynamic_acfslider .swiper-button-next:hover i' => 'color: {{VALUE}};'], 'condition' => ['useNavigation' => 'yes']]);
        $this->add_responsive_control('navigation_stroke_1', ['label' => esc_html__('Stroke Arrow', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => ''], 'tablet_default' => ['size' => ''], 'mobile_default' => ['size' => ''], 'range' => ['px' => ['max' => 50, 'min' => 0, 'step' => 1.0]], 'selectors' => ['{{WRAPPER}} .swiper-button-prev polyline, {{WRAPPER}} .swiper-button-next polyline' => 'stroke-width: {{SIZE}};'], 'condition' => ['useNavigation' => 'yes']]);
        $this->add_responsive_control('navigation_stroke_2', ['label' => esc_html__('Stroke Line', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => ''], 'tablet_default' => ['size' => ''], 'mobile_default' => ['size' => ''], 'range' => ['px' => ['max' => 50, 'min' => 0, 'step' => 1.0]], 'selectors' => ['{{WRAPPER}} .swiper-button-next line, {{WRAPPER}} .swiper-button-prev line' => 'stroke-width: {{SIZE}};'], 'condition' => ['useNavigation' => 'yes', 'arrows' => 'legacy']]);
        $this->add_control('navigation_tratteggio', ['label' => esc_html__('Dashed', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => '0'], 'range' => ['px' => ['max' => 50, 'min' => 0, 'step' => 1.0]], 'selectors' => ['{{WRAPPER}} .swiper-button-prev line, {{WRAPPER}} .swiper-button-next line, {{WRAPPER}} .swiper-button-prev polyline, {{WRAPPER}} .swiper-button-next polyline' => 'stroke-dasharray: {{SIZE}},{{SIZE}};'], 'condition' => ['useNavigation' => 'yes', 'arrows' => 'legacy']]);
        $this->end_controls_section();
        $this->start_controls_section('section_swiper_pagination', ['label' => esc_html__('Pagination', 'dynamic-content-for-elementor')]);
        $this->add_control('usePagination', ['label' => esc_html__('Pagination', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => 'yes']);
        $this->add_control('pagination_type', ['label' => esc_html__('Pagination Type', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'options' => ['bullets' => esc_html__('Bullets', 'dynamic-content-for-elementor'), 'fraction' => esc_html__('Fraction', 'dynamic-content-for-elementor'), 'progress' => esc_html__('Progress', 'dynamic-content-for-elementor')], 'default' => 'bullets', 'frontend_available' => \true, 'condition' => ['usePagination' => 'yes']]);
        $this->add_control('fraction_separator', ['label' => esc_html__('Fraction text separator', 'dynamic-content-for-elementor'), 'description' => esc_html__('The text separating the 2 numbers', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'frontend_available' => \true, 'default' => '/', 'condition' => ['pagination_type' => 'fraction', 'usePagination' => 'yes']]);
        $this->add_responsive_control('fraction_space', ['label' => esc_html__('Space', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => '4', 'unit' => 'px'], 'tablet_default' => ['unit' => 'px'], 'mobile_default' => ['unit' => 'px'], 'size_units' => ['px'], 'range' => ['px' => ['min' => -20, 'max' => 100]], 'selectors' => ['{{WRAPPER}} .swiper-pagination-fraction .separator' => 'margin: 0 {{SIZE}}{{UNIT}};'], 'condition' => ['pagination_type' => 'fraction', 'usePagination' => 'yes']]);
        $this->add_control('fraction_color', ['label' => esc_html__('Numbers color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'default' => '', 'selectors' => ['{{WRAPPER}} .swiper-pagination-fraction > *' => 'color: {{VALUE}};'], 'condition' => ['pagination_type' => 'fraction', 'usePagination' => 'yes']]);
        $this->add_group_control(Group_Control_Typography::get_type(), ['name' => 'fraction_typography', 'label' => esc_html__('Numbers Typography', 'dynamic-content-for-elementor'), 'selector' => '{{WRAPPER}} .swiper-pagination-fraction > *', 'condition' => ['pagination_type' => 'fraction', 'usePagination' => 'yes']]);
        $this->add_control('fraction_current_color', ['label' => esc_html__('The color of the current number', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'default' => '', 'selectors' => ['{{WRAPPER}} .swiper-pagination-fraction .swiper-pagination-current' => 'color: {{VALUE}};'], 'condition' => ['pagination_type' => 'fraction', 'usePagination' => 'yes']]);
        $this->add_group_control(Group_Control_Typography::get_type(), ['name' => 'fraction_typography_current', 'label' => esc_html__('Typography current number', 'dynamic-content-for-elementor'), 'default' => '', 'selector' => '{{WRAPPER}} .swiper-pagination-fraction .swiper-pagination-current', 'condition' => ['pagination_type' => 'fraction', 'usePagination' => 'yes']]);
        $this->add_control('fraction_separator_color', ['label' => esc_html__('Separator color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'default' => '', 'selectors' => ['{{WRAPPER}} .swiper-pagination-fraction .separator' => 'color: {{VALUE}};'], 'condition' => ['pagination_type' => 'fraction', 'usePagination' => 'yes']]);
        $this->add_group_control(Group_Control_Typography::get_type(), ['name' => esc_html__('fraction_typography_separator', 'dynamic-content-for-elementor'), 'label' => esc_html__('Separator Typography', 'dynamic-content-for-elementor'), 'default' => '', 'selector' => '{{WRAPPER}} .swiper-pagination-fraction .separator', 'condition' => ['pagination_type' => 'fraction', 'usePagination' => 'yes']]);
        $this->add_responsive_control('bullets_space', ['label' => esc_html__('Space', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => '5', 'unit' => 'px'], 'tablet_default' => ['unit' => 'px'], 'mobile_default' => ['unit' => 'px'], 'size_units' => ['px'], 'range' => ['px' => ['min' => 0, 'max' => 100]], 'selectors' => ['{{WRAPPER}} .swiper-pagination-bullets .swiper-pagination-bullet' => 'margin: 0 {{SIZE}}{{UNIT}};'], 'condition' => ['pagination_type' => 'bullets', 'usePagination' => 'yes']]);
        $this->add_responsive_control('vertical_pagination_position', ['label' => esc_html__('Vertical position', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => 0, 'unit' => '%'], 'size_units' => ['px', '%'], 'range' => ['px' => ['max' => 200, 'min' => -200, 'step' => 1], '%' => ['max' => 150, 'min' => -150, 'step' => 1]], 'selectors' => ['{{WRAPPER}} .swiper-pagination' => 'bottom: {{SIZE}}{{UNIT}};'], 'condition' => ['pagination_type' => ['bullets', 'fraction'], 'usePagination' => 'yes']]);
        $this->add_responsive_control('pagination_bullets', ['label' => esc_html__('Bullets size', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => '8', 'unit' => 'px'], 'tablet_default' => ['unit' => 'px'], 'mobile_default' => ['unit' => 'px'], 'size_units' => ['px'], 'range' => ['px' => ['min' => 0, 'max' => 100]], 'selectors' => ['{{WRAPPER}} .swiper-pagination-bullets .swiper-pagination-bullet' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};'], 'condition' => ['pagination_type' => 'bullets', 'usePagination' => 'yes']]);
        $this->add_control('bullets_color', ['label' => esc_html__('Bullets Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'default' => '', 'selectors' => ['{{WRAPPER}} .swiper-pagination-bullets .swiper-pagination-bullet' => 'background-color: {{VALUE}};'], 'condition' => ['pagination_type' => 'bullets', 'usePagination' => 'yes']]);
        $this->add_group_control(Group_Control_Border::get_type(), ['name' => 'border_bullet', 'label' => esc_html__('Bullets border', 'dynamic-content-for-elementor'), 'selector' => '{{WRAPPER}} .swiper-pagination-bullets .swiper-pagination-bullet', 'condition' => ['pagination_type' => 'bullets', 'usePagination' => 'yes']]);
        $this->add_control('current_bullet_color', ['label' => esc_html__('Active bullet color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'default' => '', 'selectors' => ['{{WRAPPER}} .swiper-pagination-bullets .swiper-pagination-bullet-active' => 'background-color: {{VALUE}};'], 'condition' => ['pagination_type' => 'bullets', 'usePagination' => 'yes']]);
        $this->add_group_control(Group_Control_Border::get_type(), ['name' => 'border_current_bullet', 'label' => esc_html__('Active bullet border', 'dynamic-content-for-elementor'), 'selector' => '{{WRAPPER}} .swiper-pagination-bullets .swiper-pagination-bullet-active', 'condition' => ['pagination_type' => 'bullets', 'usePagination' => 'yes']]);
        $this->add_control('progress_color', ['label' => esc_html__('Progress color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'default' => '', 'selectors' => ['{{WRAPPER}} .swiper-pagination-progress' => 'background-color: {{VALUE}};'], 'condition' => ['pagination_type' => 'progress', 'usePagination' => 'yes']]);
        $this->add_control('progressbar_color', ['label' => esc_html__('Progressbar color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'default' => '', 'selectors' => ['{{WRAPPER}} .swiper-pagination-progress .swiper-pagination-progressbar' => 'background-color: {{VALUE}};'], 'condition' => ['pagination_type' => 'progress', 'usePagination' => 'yes']]);
        $this->end_controls_section();
        $this->start_controls_section('section_swiper_scrollbar', ['label' => esc_html__('Scrollbar', 'dynamic-content-for-elementor')]);
        $this->add_control('useScrollbar', ['label' => esc_html__('Scrollbar', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => '']);
        $this->end_controls_section();
        $this->start_controls_section('section_swiper_autoplay', ['label' => esc_html__('Autoplay', 'dynamic-content-for-elementor')]);
        $this->add_control('useAutoplay', ['label' => esc_html__('Autoplay', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => '', 'return_value' => 'yes', 'frontend_available' => \true]);
        $this->add_control('autoplay', ['label' => esc_html__('Autoplay Delay (ms)', 'dynamic-content-for-elementor'), 'description' => esc_html__('Delay between transitions (in ms). If this parameter is not specified (by default), autoplay will be disabled', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::NUMBER, 'default' => '', 'min' => 0, 'max' => 15000, 'step' => 100, 'frontend_available' => \true, 'condition' => ['useAutoplay' => 'yes']]);
        $this->add_control('autoplayStopOnLast', ['label' => esc_html__('Autoplay stop on last slide', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => '', 'frontend_available' => \true, 'condition' => ['useAutoplay' => 'yes']]);
        $this->add_control('autoplayDisableOnInteraction', ['label' => esc_html__('Disable Autoplay on interaction', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => 'yes', 'frontend_available' => \true, 'condition' => ['useAutoplay' => 'yes']]);
        $this->end_controls_section();
        $this->start_controls_section('section_loop', ['label' => esc_html__('Loop', 'dynamic-content-for-elementor')]);
        $this->add_control('loop_notice', ['type' => Controls_Manager::NOTICE, 'notice_type' => 'info', 'content' => esc_html__('This feature is available when rows is set to 1', 'dynamic-content-for-elementor')]);
        $this->add_control('loop', ['label' => esc_html__('Loop', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => '', 'return_value' => 'yes', 'frontend_available' => \true]);
        $this->end_controls_section();
        $this->start_controls_section('section_swiper_progress', ['label' => esc_html__('Progress', 'dynamic-content-for-elementor')]);
        $this->add_control('watchSlidesProgress', ['label' => esc_html__('Watch Slides Progress', 'dynamic-content-for-elementor'), 'description' => esc_html__('Enable this feature to calculate each slides progress', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => '', 'frontend_available' => \true]);
        $this->add_control('watchSlidesVisibility', ['label' => esc_html__('Watch Slides Visibility', 'dynamic-content-for-elementor'), 'description' => esc_html__('WatchSlidesProgress should be enabled. Enable this option and slides that are in viewport will have additional visible classes', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => '', 'frontend_available' => \true, 'condition' => ['watchSlidesProgress' => 'yes']]);
        $this->end_controls_section();
        $this->start_controls_section('section_swiper_freemode', ['label' => esc_html__('Freemode', 'dynamic-content-for-elementor')]);
        $this->add_control('freeMode', ['label' => esc_html__('Free Mode', 'dynamic-content-for-elementor'), 'description' => esc_html__('The slides will not have fixed positions', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => '', 'frontend_available' => \true]);
        $this->add_control('freeModeMinimumVelocity', ['label' => esc_html__('Free Mode Momentum Velocity Ratio', 'dynamic-content-for-elementor'), 'description' => esc_html__('Higher value produces larger momentum bounce effect', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::NUMBER, 'default' => 0.02, 'min' => 0, 'max' => 1, 'step' => 0.01, 'frontend_available' => \true, 'condition' => ['freeMode' => 'yes']]);
        $this->add_control('freeModeMomentum', ['label' => esc_html__('Free Mode Momentum', 'dynamic-content-for-elementor'), 'description' => esc_html__('Slides will keep moving for a while after you release it', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => '', 'frontend_available' => \true, 'condition' => ['freeMode' => 'yes']]);
        $this->add_control('freeModeMomentumRatio', ['label' => esc_html__('Free Mode Momentum Ratio', 'dynamic-content-for-elementor'), 'description' => esc_html__('Higher value produces larger momentum distance after you release slider', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::NUMBER, 'default' => 1, 'min' => 0, 'max' => 10, 'step' => 0.1, 'frontend_available' => \true, 'condition' => ['freeMode' => 'yes', 'freeModeMomentum' => 'yes']]);
        $this->add_control('freeModeMomentumVelocityRatio', ['label' => esc_html__('Free Mode Momentum Velocity Ratio', 'dynamic-content-for-elementor'), 'description' => esc_html__('Higher value produces larger momentum speed after you release slider', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::NUMBER, 'default' => 1, 'min' => 0, 'max' => 10, 'step' => 0.1, 'frontend_available' => \true, 'condition' => ['freeMode' => 'yes', 'freeModeMomentum' => 'yes']]);
        $this->add_control('freeModeMomentumBounce', ['label' => esc_html__('Free Mode Momentum Bounce', 'dynamic-content-for-elementor'), 'description' => esc_html__('Set to false if you want to disable momentum bounce in free mode', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => 'yes', 'frontend_available' => \true, 'condition' => ['freeMode' => 'yes', 'freeModeMomentum' => 'yes']]);
        $this->add_control('freeModeMomentumBounceRatio', ['label' => esc_html__('Free Mode Momentum Bounce Ratio', 'dynamic-content-for-elementor'), 'description' => esc_html__('Higher value produces bigger rebound effect of the moment', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::NUMBER, 'default' => 1, 'min' => 0, 'max' => 10, 'step' => 0.1, 'frontend_available' => \true, 'condition' => ['freeMode' => 'yes', 'freeModeMomentumBounce' => 'yes']]);
        $this->add_control('freeModeSticky', ['label' => esc_html__('Free Mode Sticky', 'dynamic-content-for-elementor'), 'description' => esc_html__('Minimum touchmove-velocity required to trigger free mode momentum', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => '', 'frontend_available' => \true, 'condition' => ['freeMode' => 'yes']]);
        $this->end_controls_section();
        $this->start_controls_section('section_swiper_keyboardMousewheel', ['label' => esc_html__('Keyboard / Mousewheel', 'dynamic-content-for-elementor')]);
        $this->add_control('keyboardControl', ['label' => esc_html__('Keyboard Control', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => '', 'frontend_available' => \true]);
        $this->add_control('mousewheelControl', ['label' => esc_html__('Mousewheel Control', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => '', 'frontend_available' => \true]);
        $this->end_controls_section();
        $this->start_controls_section('section_swiper_special', ['label' => esc_html__('Other Options', 'dynamic-content-for-elementor')]);
        $this->add_control('setWrapperSize', ['label' => esc_html__('Set Wrapper Size', 'dynamic-content-for-elementor'), 'description' => esc_html__('Enable this option and plugin will set width/height on swiper wrapper equal to total size of all slides. Mostly should be used as compatibility fallback option for browser that don\'t support flexbox layout well', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => '', 'frontend_available' => \true]);
        $this->add_control('virtualTranslate', ['label' => esc_html__('Virtual Translate', 'dynamic-content-for-elementor'), 'description' => esc_html__('Enable this option and swiper will be operated as usual except it will not move, real translate values on wrapper will not be set. Useful when you may need to create custom slide transition', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => '', 'return_value' => 'yes', 'frontend_available' => \true]);
        $this->add_control('autoHeight', ['label' => esc_html__('Auto Height', 'dynamic-content-for-elementor'), 'description' => esc_html__('Set to true and slider wrapper will adopt its height to the height of the currently active slide', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => '', 'return_value' => 'yes', 'frontend_available' => \true]);
        $this->add_control('roundLengths', ['label' => esc_html__('Round Lengths', 'dynamic-content-for-elementor'), 'description' => esc_html__('Set to true to round values of slides width and height to prevent blurry texts on usual resolution screens (if you have such)', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => '', 'return_value' => 'yes', 'frontend_available' => \true]);
        $this->add_control('nested', ['label' => esc_html__('Nested', 'dynamic-content-for-elementor'), 'description' => esc_html__('Set to true on nested Swiper for correct touch events interception. Use only on nested swipers that use same direction as the parent one', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => '', 'return_value' => 'yes', 'frontend_available' => \true]);
        $this->add_control('grabCursor', ['label' => esc_html__('Grab Cursor', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => '', 'return_value' => 'yes', 'frontend_available' => \true]);
        $this->end_controls_section();
        $this->start_controls_section('section_lightbox_effects', ['label' => 'Lightbox Settings', 'dynamic-content-for-elementor']);
        $this->add_control('enable_lightbox', ['label' => esc_html__('LightBox', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => '', 'frontend_available' => \true]);
        $this->add_control('enable_overlay_hover', ['label' => esc_html__('Overlay Hover', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => '', 'condition' => ['enable_lightbox' => 'yes']]);
        $this->add_group_control(Group_Control_Background::get_type(), ['name' => 'background', 'types' => ['classic', 'gradient'], 'selector' => '{{WRAPPER}} .acfslider-overlay_hover', 'popover' => \true, 'condition' => ['enable_overlay_hover' => 'yes']]);
        $this->add_control('hover_effects', ['label' => esc_html__('Hover Effects', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'options' => ['' => esc_html__('None', 'dynamic-content-for-elementor'), 'zoom' => esc_html__('Zoom', 'dynamic-content-for-elementor')], 'default' => '', 'prefix_class' => 'hovereffect-', 'condition' => ['enable_lightbox' => 'yes']]);
        $this->end_controls_section();
        $this->start_controls_section('section_source', ['label' => esc_html__('Source', 'dynamic-content-for-elementor'), 'condition' => ['acf_gallery_from' => 'current_post']]);
        $this->add_control('data_source', ['label' => esc_html__('Source', 'dynamic-content-for-elementor'), 'description' => esc_html__('Select the data source', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => 'yes', 'label_on' => esc_html__('Same', 'dynamic-content-for-elementor'), 'label_off' => esc_html__('other', 'dynamic-content-for-elementor'), 'return_value' => 'yes']);
        $this->add_control('other_post_source', ['label' => esc_html__('Select from other source post', 'dynamic-content-for-elementor'), 'type' => 'ooo_query', 'placeholder' => esc_html__('Post Title', 'dynamic-content-for-elementor'), 'label_block' => \true, 'query_type' => 'posts', 'condition' => ['data_source' => '']]);
        $this->end_controls_section();
    }
    protected function safe_render()
    {
        $settings = $this->get_settings_for_display();
        if (empty($settings)) {
            return;
        }
        $id = Helper::get_acf_source_id($settings['acf_gallery_from'], $settings['other_post_source'] ?? \false);
        $acf_gallery = Helper::get_acf_field_value($settings['acf_field_list'], $id);
        if (!$acf_gallery) {
            return;
        }
        // Setup main container classes
        $main_classes = ['dynamic_acfslider'];
        if (!empty($settings['enable_lightbox'])) {
            $main_classes[] = 'is-lightbox';
            $main_classes[] = 'gallery';
            $main_classes[] = 'gallery-lightbox';
        }
        if ('yes' === $settings['enable_overlay_hover']) {
            $main_classes[] = 'is-overlay';
        }
        $this->add_render_attribute('main_container', 'class', $main_classes);
        // Setup swiper wrapper
        $swiper_wrapper_classes = ['dyncontel-swiper', 'dce-' . $settings['effects'], 'dce-direction-horizontal'];
        $this->add_render_attribute('swiper_wrapper', 'class', $swiper_wrapper_classes);
        // Setup swiper container
        $swiper_class = 'swiper';
        $this->add_render_attribute('swiper_container', 'class', $swiper_class);
        // Setup swiper wrapper inner
        $this->add_render_attribute('swiper_wrapper_inner', 'class', 'swiper-wrapper');
        ?>
		<div <?php 
        echo $this->get_render_attribute_string('main_container');
        ?>>
			<div <?php 
        echo $this->get_render_attribute_string('swiper_wrapper');
        ?>>
				<div <?php 
        echo $this->get_render_attribute_string('swiper_container');
        ?>>
					<div <?php 
        echo $this->get_render_attribute_string('swiper_wrapper_inner');
        ?>>
						<?php 
        $this->render_slides($acf_gallery, $settings);
        ?>
					</div>
					<?php 
        $this->render_scrollbar($settings, $acf_gallery);
        ?>
				</div>
				<?php 
        $this->render_navigation($settings, $acf_gallery);
        ?>
				<?php 
        $this->render_pagination($settings, $acf_gallery);
        ?>
			</div>
		</div>
		<?php 
    }
    /**
     * Render slides
     *
     * @param array<array<string|int|mixed>|int|string> $acf_gallery Array of gallery images with attachment data or IDs
     * @param array<string|mixed> $settings Widget settings
     * @return void
     */
    private function render_slides($acf_gallery, $settings)
    {
        $counter = 0;
        foreach ($acf_gallery as $image) {
            if (!\is_array($image) || !isset($image['id'])) {
                $img_id = (int) $image;
                $image = Helper::get_image_attachment($img_id);
            }
            if (!$image) {
                continue;
            }
            $img_id = $image['id'] ?? 0;
            $img_url = $image['url'] ?? '';
            $img_alt = $image['alt'] ?? '';
            $img_width = $image['width'] ?? 0;
            $img_height = $image['height'] ?? 0;
            $img_desc = \false;
            if (!empty($settings['use_desc'])) {
                $use_desc = $settings['use_desc'];
                $img_desc = $image[$use_desc] ?? '';
            }
            // Setup slide
            $this->add_render_attribute('slide_' . $counter, 'class', 'swiper-slide');
            // Setup figure
            $figure_classes = ['acfslider-item', 'grid-item'];
            if (!empty($settings['use_bg_image'])) {
                $figure_classes[] = 'acfslider-bg-image';
                $image_url = Group_Control_Image_Size::get_attachment_image_src($img_id, 'size', $settings);
                $this->add_render_attribute('figure_' . $counter, 'style', 'background-image: url(' . esc_url($image_url) . '); background-repeat: no-repeat; background-size: cover;');
            }
            $this->add_render_attribute('figure_' . $counter, 'class', $figure_classes);
            // Setup wrapper
            $wrapper_classes = ['wrap-item-acfslider'];
            $this->add_render_attribute('wrapper_' . $counter, 'class', $wrapper_classes);
            if (!empty($settings['use_bg_image'])) {
                $this->add_render_attribute('wrapper_' . $counter, 'style', 'height: 100%;');
            }
            // Setup lightbox link
            if (!empty($settings['enable_lightbox'])) {
                $link_classes = ['elementor-clickable'];
                $link_classes[] = 'is-lightbox';
                $link_classes[] = 'gallery-lightbox';
                $link_key = 'lightbox_link_' . $counter;
                $this->add_render_attribute($link_key, 'class', $link_classes);
                $this->add_render_attribute($link_key, 'href', esc_url($img_url));
                $this->add_lightbox_data_attributes($link_key, $img_id, 'yes', $this->get_id());
            }
            // Setup image
            if (empty($settings['use_bg_image'])) {
                $image_url = Group_Control_Image_Size::get_attachment_image_src($img_id, 'size', $settings);
                $this->add_render_attribute('image_' . $counter, ['src' => esc_url($image_url), 'alt' => esc_attr($img_alt), 'itemprop' => 'thumbnail']);
            }
            // Setup figcaption
            if ($img_desc) {
                $this->add_render_attribute('figcaption_' . $counter, 'itemprop', 'caption description');
            }
            // Render slide
            ?>
			<div <?php 
            echo $this->get_render_attribute_string('slide_' . $counter);
            ?>>
				<figure <?php 
            echo $this->get_render_attribute_string('figure_' . $counter);
            ?>>
					<?php 
            if (!empty($settings['enable_lightbox']) && !empty($settings['use_bg_image'])) {
                ?>
						<a <?php 
                echo $this->get_render_attribute_string('lightbox_link_' . $counter);
                ?>>
					<?php 
            }
            ?>
					
					<div <?php 
            echo $this->get_render_attribute_string('wrapper_' . $counter);
            ?>>
						<?php 
            if (!empty($settings['enable_lightbox']) && empty($settings['use_bg_image'])) {
                ?>
							<a <?php 
                echo $this->get_render_attribute_string('lightbox_link_' . $counter);
                ?>>
						<?php 
            }
            ?>
						
						<?php 
            if (empty($settings['use_bg_image'])) {
                ?>
							<img <?php 
                echo $this->get_render_attribute_string('image_' . $counter);
                ?> />
							<?php 
                if ('yes' === $settings['enable_overlay_hover']) {
                    ?>
								<span class="acfslider-overlay_hover"></span>
							<?php 
                }
                ?>
						<?php 
            }
            ?>
						
						<?php 
            if ($img_desc) {
                ?>
							<figcaption <?php 
                echo $this->get_render_attribute_string('figcaption_' . $counter);
                ?>><?php 
                echo esc_html($img_desc);
                ?></figcaption>
						<?php 
            }
            ?>
						
						<?php 
            if (!empty($settings['enable_lightbox']) && empty($settings['use_bg_image'])) {
                ?>
							</a>
						<?php 
            }
            ?>
					</div>
					
					<?php 
            if (!empty($settings['enable_lightbox']) && !empty($settings['use_bg_image'])) {
                ?>
						</a>
					<?php 
            }
            ?>
				</figure>
			</div>
			<?php 
            ++$counter;
        }
    }
    /**
     * Render scrollbar
     *
     * @param array<string|mixed> $settings Widget settings
     * @param array<array<string|int|mixed>> $acf_gallery Array of gallery images with attachment data
     * @return void
     */
    private function render_scrollbar($settings, $acf_gallery)
    {
        if (empty($settings['useScrollbar']) || \count($acf_gallery) <= 1) {
            return;
        }
        $this->add_render_attribute('scrollbar', 'class', 'swiper-scrollbar');
        ?>
		<div <?php 
        echo $this->get_render_attribute_string('scrollbar');
        ?>></div>
		<?php 
    }
    /**
     * Render navigation arrows
     *
     * @param array<string|mixed> $settings Widget settings
     * @param array<array<string|int|mixed>> $acf_gallery Array of gallery images with attachment data
     * @return void
     */
    private function render_navigation($settings, $acf_gallery)
    {
        if (empty($settings['useNavigation']) || \count($acf_gallery) <= 1) {
            return;
        }
        $this->add_render_attribute('nav_next', ['class' => ['swiper-button', 'swiper-button-next', 'next-' . $this->get_id()], 'role' => 'button', 'tabindex' => '0', 'aria-label' => esc_attr__('Next slide', 'dynamic-content-for-elementor')]);
        $this->add_render_attribute('nav_prev', ['class' => ['swiper-button', 'swiper-button-prev', 'prev-' . $this->get_id()], 'role' => 'button', 'tabindex' => '0', 'aria-label' => esc_attr__('Previous slide', 'dynamic-content-for-elementor')]);
        $left_arrow = $settings['previous_arrow'] ?? [];
        $right_arrow = $settings['next_arrow'] ?? [];
        $use_legacy = isset($settings['arrows']) && 'custom' !== $settings['arrows'];
        // legacy vs custom
        ?>
		<div <?php 
        echo $this->get_render_attribute_string('nav_next');
        ?>>
			<?php 
        if (!$use_legacy && !empty($right_arrow['value'])) {
            Icons_Manager::render_icon($right_arrow, ['aria-hidden' => 'true']);
        } else {
            ?>
				<svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" width="85.039px" height="85.039px" viewBox="378.426 255.12 85.039 85.039" enable-background="new 378.426 255.12 85.039 85.039" xml:space="preserve">
					<line fill="none" stroke="#C81517" stroke-width="1.3845" stroke-miterlimit="10" x1="458.375" y1="298.077" x2="382.456" y2="298.077"/>
					<polyline fill="none" stroke="#C81517" stroke-width="1.3845" stroke-miterlimit="10" points="424.543,264.245,458.375,298.077,424.543,331.909 "/>
				</svg>
				<?php 
        }
        ?>
		</div>
		<div <?php 
        echo $this->get_render_attribute_string('nav_prev');
        ?>>
			<?php 
        if (!$use_legacy && !empty($left_arrow['value'])) {
            Icons_Manager::render_icon($left_arrow, ['aria-hidden' => 'true']);
        } else {
            ?>
				<svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" width="85.039px" height="85.039px" viewBox="378.426 255.12 85.039 85.039" enable-background="new 378.426 255.12 85.039 85.039" xml:space="preserve">
					<line fill="none" stroke="#C81517" stroke-width="1.3845" stroke-dasharray="0,0" stroke-miterlimit="10" x1="382.456" y1="298.077" x2="458.375" y2="298.077"/>
					<polyline fill="none" stroke="#C81517" stroke-width="1.3845" stroke-dasharray="0,0" stroke-miterlimit="10" points="416.287,331.909,382.456,298.077,416.287,264.245 "/>
				</svg>
				<?php 
        }
        ?>
		</div>
		<?php 
    }
    /**
     * Render pagination
     *
     * @param array<string|mixed> $settings Widget settings
     * @param array<array<string|int|mixed>> $acf_gallery Array of gallery images with attachment data
     * @return void
     */
    private function render_pagination($settings, $acf_gallery)
    {
        if (empty($settings['usePagination']) || \count($acf_gallery) <= 1) {
            return;
        }
        $this->add_render_attribute('pagination_wrapper', 'class', 'swiper-container-horizontal');
        $this->add_render_attribute('pagination', ['class' => ['swiper-pagination', 'pagination-' . $this->get_id()]]);
        ?>
		<div <?php 
        echo $this->get_render_attribute_string('pagination_wrapper');
        ?>>
			<div <?php 
        echo $this->get_render_attribute_string('pagination');
        ?>></div>
		</div>
		<?php 
    }
}
