<?php

// SPDX-FileCopyrightText: 2018-2026 Ovation S.r.l. <help@dynamic.ooo>
// SPDX-License-Identifier: GPL-3.0-or-later
namespace DynamicContentForElementor\Widgets;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Background;
use Elementor\Utils;
use DynamicContentForElementor\Helper;
use DynamicContentForElementor\Group_Control_Outline;
use DynamicContentForElementor\Controls\Group_Control_Filters_CSS;
if (!\defined('ABSPATH')) {
    exit;
    // Exit if accessed directly
}
class FeaturedImage extends \DynamicContentForElementor\Widgets\WidgetPrototype
{
    public function run_once()
    {
        parent::run_once();
        $save_guard = \DynamicContentForElementor\Plugin::instance()->save_guard;
        $save_guard->register_unsafe_control($this->get_name(), 'other_post_source');
    }
    public function get_style_depends()
    {
        return ['dce-featuredImage'];
    }
    /**
     * Register controls after check if this feature is only for admin
     *
     * @return void
     */
    protected function safe_register_controls()
    {
        $post_type_object = get_post_type_object(get_post_type());
        $this->start_controls_section('section_content', ['label' => esc_html__('Image settings', 'dynamic-content-for-elementor')]);
        $this->add_control('preview', ['type' => Controls_Manager::RAW_HTML, 'raw' => get_the_post_thumbnail(), 'separator' => 'none']);
        $this->add_group_control(Group_Control_Image_Size::get_type(), ['name' => 'size', 'label' => esc_html__('Image Size', 'dynamic-content-for-elementor'), 'default' => 'large']);
        $this->add_responsive_control('align', ['label' => esc_html__('Alignment', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CHOOSE, 'options' => ['left' => ['title' => esc_html__('Left', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-left'], 'center' => ['title' => esc_html__('Center', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-center'], 'right' => ['title' => esc_html__('Right', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-right']], 'default' => '', 'selectors' => ['{{WRAPPER}}' => 'text-align: {{VALUE}};', '' => '']]);
        $this->add_control('link_to', ['label' => esc_html__('Link to', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'default' => 'none', 'options' => ['none' => esc_html__('None', 'dynamic-content-for-elementor'), 'home' => esc_html__('Home URL', 'dynamic-content-for-elementor'), 'post' => 'Post URL', 'acf_url' => esc_html__('ACF URL', 'dynamic-content-for-elementor'), 'file' => esc_html__('Media File URL', 'dynamic-content-for-elementor'), 'custom' => esc_html__('Custom URL', 'dynamic-content-for-elementor')]]);
        $this->add_control('acf_field_url', ['label' => esc_html__('ACF Field Url', 'dynamic-content-for-elementor'), 'type' => 'ooo_query', 'query_type' => 'acf', 'label_block' => \true, 'dynamic' => ['active' => \false], 'object_type' => ['file', 'url'], 'condition' => ['link_to' => 'acf_url']]);
        $this->add_control('acf_field_url_target', ['label' => esc_html__('Blank', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'condition' => ['link_to' => 'acf_url']]);
        $this->add_control('link', ['label' => esc_html__('Link to', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::URL, 'placeholder' => esc_html__('https://your-link.com', 'dynamic-content-for-elementor'), 'condition' => ['link_to' => 'custom'], 'show_label' => \false]);
        $this->end_controls_section();
        /* -------------------- Background ------------------ */
        $post_type_object = get_post_type_object(get_post_type());
        $this->start_controls_section('section_backgroundimage', ['label' => esc_html__('Background', 'dynamic-content-for-elementor')]);
        $this->add_control('use_bg', ['label' => esc_html__('Background', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CHOOSE, 'toggle' => \false, 'options' => ['1' => ['title' => esc_html__('Yes', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-check'], '0' => ['title' => esc_html__('No', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-ban']], 'default' => '0']);
        $this->add_control('bg_position', ['label' => esc_html__('Background position', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'default' => 'top center', 'options' => ['' => esc_html__('Default', 'dynamic-content-for-elementor'), 'top left' => esc_html__('Top Left', 'dynamic-content-for-elementor'), 'top center' => esc_html__('Top Center', 'dynamic-content-for-elementor'), 'top right' => esc_html__('Top Right', 'dynamic-content-for-elementor'), 'center left' => esc_html__('Center Left', 'dynamic-content-for-elementor'), 'center center' => esc_html__('Center Center', 'dynamic-content-for-elementor'), 'center right' => esc_html__('Center Right', 'dynamic-content-for-elementor'), 'bottom left' => esc_html__('Bottom Left', 'dynamic-content-for-elementor'), 'bottom center' => esc_html__('Bottom Center', 'dynamic-content-for-elementor'), 'bottom right' => esc_html__('Bottom Right', 'dynamic-content-for-elementor')], 'selectors' => ['{{WRAPPER}} .dynamic-content-featuredimage-bg' => 'background-position: {{VALUE}};'], 'condition' => ['use_bg' => '1']]);
        $this->add_control('bg_extend', ['label' => esc_html__('Extend Background', 'dynamic-content-for-elementor'), 'description' => esc_html__('Absolutely position the image by spreading it over the entire column. Warning: the height of the image depends on the elements contained in the column.', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => '', 'condition' => ['use_bg' => '1'], 'prefix_class' => 'extendbg-']);
        $this->add_responsive_control('minimum_height', ['label' => esc_html__('Minimum Height', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => '', 'unit' => 'px'], 'tablet_default' => ['size' => '', 'unit' => 'px'], 'mobile_default' => ['size' => '', 'unit' => 'px'], 'size_units' => ['px', '%', 'vh'], 'range' => ['%' => ['min' => 1, 'max' => 100], 'px' => ['min' => 1, 'max' => 1000], 'vh' => ['min' => 1, 'max' => 100]], 'selectors' => ['{{WRAPPER}} .dynamic-content-featuredimage-bg' => 'min-height: {{SIZE}}{{UNIT}};'], 'condition' => ['use_bg' => '1', 'bg_extend' => 'yes']]);
        $this->add_responsive_control('height', ['label' => esc_html__('Height', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => 200, 'unit' => 'px'], 'tablet_default' => ['unit' => 'px'], 'mobile_default' => ['unit' => 'px'], 'size_units' => ['px', '%', 'vh'], 'range' => ['%' => ['min' => 1, 'max' => 100], 'px' => ['min' => 1, 'max' => 1000], 'vh' => ['min' => 1, 'max' => 100]], 'selectors' => ['{{WRAPPER}} .dynamic-content-featuredimage-bg' => 'height: {{SIZE}}{{UNIT}};'], 'condition' => ['use_bg' => '1', 'bg_extend' => '']]);
        $this->end_controls_section();
        // ------------------------------------------------------------- [ Overlay style ]
        $this->start_controls_section('section_overlay', ['label' => 'Overlay']);
        $this->add_group_control(Group_Control_Background::get_type(), ['name' => 'background_overlay', 'types' => ['classic', 'gradient'], 'selector' => '{{WRAPPER}} .dce-overlay']);
        $this->add_control('opacity_overlay', ['label' => esc_html__('Opacity', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'range' => ['px' => ['max' => 1, 'min' => 0, 'step' => 0.01]], 'selectors' => ['{{WRAPPER}} .dce-overlay' => 'opacity: {{SIZE}};'], 'condition' => ['background_overlay_background' => ['classic', 'gradient']]]);
        $this->end_controls_section();
        $this->start_controls_section('section_hover_style', ['label' => 'Rollover', 'condition' => ['link_to!' => 'none']]);
        $this->add_control('bghover_heading', ['label' => esc_html__('Background color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HEADING, 'separator' => 'before']);
        $this->add_group_control(Group_Control_Background::get_type(), ['name' => 'overlay_hover_color', 'label' => esc_html__('Background', 'dynamic-content-for-elementor'), 'description' => 'Background', 'types' => ['classic', 'gradient'], 'selector' => '{{WRAPPER}} .dce-overlay_hover', 'condition' => ['link_to!' => 'none']]);
        $this->add_control('bgoverlayhover_heading', ['label' => esc_html__('Change background color of overlay', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HEADING, 'separator' => 'before', 'condition' => ['background_overlay_background' => ['classic', 'gradient']]]);
        $this->add_group_control(Group_Control_Background::get_type(), ['name' => 'overlay_color_on_hover', 'label' => esc_html__('Background overlay', 'dynamic-content-for-elementor'), 'description' => 'Background color of overlay', 'types' => ['classic', 'gradient'], 'selector' => '{{WRAPPER}} a:hover .dce-overlay', 'condition' => ['background_overlay_background' => ['classic', 'gradient'], 'link_to!' => 'none']]);
        $this->add_control('opacity_overlay_on_hover', ['label' => esc_html__('Overlay Opacity', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'range' => ['px' => ['max' => 1, 'min' => 0, 'step' => 0.01]], 'selectors' => ['{{WRAPPER}} a:hover .dce-overlay' => 'opacity: {{SIZE}};'], 'condition' => ['background_overlay_background' => ['classic', 'gradient'], 'link_to!' => 'none']]);
        $this->add_control('imageanimations_heading', ['label' => esc_html__('Rollover Animations', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HEADING, 'separator' => 'before']);
        $this->add_control('hover_animation', ['label' => esc_html__('Animation', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HOVER_ANIMATION]);
        $this->add_control('imagefilters_heading', ['label' => esc_html__('Rollover Filters', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HEADING, 'separator' => 'before']);
        $this->add_group_control(Group_Control_Filters_CSS::get_type(), ['name' => 'filters_image_hover', 'label' => esc_html__('Filters', 'dynamic-content-for-elementor'), 'selector' => '{{WRAPPER}} a:hover .wrap-filters']);
        $this->add_control('imageeffects_heading', ['label' => esc_html__('Rollover Effects', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HEADING, 'separator' => 'before']);
        $this->add_control('hover_effects', ['label' => esc_html__('Effects', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'options' => ['' => esc_html__('None', 'dynamic-content-for-elementor'), 'zoom' => esc_html__('Zoom', 'dynamic-content-for-elementor')], 'default' => '', 'prefix_class' => 'hovereffect-', 'condition' => ['link_to!' => 'none']]);
        $this->end_controls_section();
        $this->start_controls_section('section_placeholder', ['label' => esc_html__('Placeholder', 'dynamic-content-for-elementor')]);
        $this->add_control('use_placeholter', ['label' => esc_html__('Use placeholder Image', 'dynamic-content-for-elementor'), 'description' => 'Use another image if the featured one does not exist.', 'type' => Controls_Manager::CHOOSE, 'toggle' => \false, 'options' => ['1' => ['title' => esc_html__('Yes', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-check'], '0' => ['title' => esc_html__('No', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-ban']], 'default' => '0']);
        $this->add_control('custom_placeholder_image', ['label' => esc_html__('Placeholder Image', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::MEDIA, 'default' => ['url' => \Elementor\Utils::get_placeholder_image_src()], 'condition' => ['use_placeholter' => '1']]);
        $this->end_controls_section();
        $this->start_controls_section('section_style', ['label' => esc_html__('Image', 'dynamic-content-for-elementor'), 'tab' => Controls_Manager::TAB_STYLE]);
        $this->add_responsive_control('space', ['label' => esc_html__('Size', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['unit' => '%'], 'size_units' => ['px', '%'], 'range' => ['%' => ['min' => 1, 'max' => 100], 'px' => ['min' => 1, 'max' => 500]], 'selectors' => ['{{WRAPPER}} .dce-featured-image' => 'width: {{SIZE}}{{UNIT}};', '{{WRAPPER}} .dce-featured-image.is-bg' => 'display: inline-block;'], 'condition' => ['bg_extend' => '']]);
        $this->add_responsive_control('maxwidth', ['label' => esc_html__('Max Width', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['unit' => 'px'], 'size_units' => ['px', '%'], 'range' => ['%' => ['min' => 1, 'max' => 100], 'px' => ['min' => 1, 'max' => 500]], 'selectors' => ['{{WRAPPER}} .dce-featured-image' => 'max-width: {{SIZE}}{{UNIT}};'], 'condition' => ['use_bg' => '0', 'bg_extend' => '']]);
        $this->add_responsive_control('maxheight', ['label' => esc_html__('Max Height', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['unit' => 'px'], 'size_units' => ['px'], 'range' => ['%' => ['min' => 1, 'max' => 100], 'px' => ['min' => 1, 'max' => 500]], 'selectors' => ['{{WRAPPER}} .dce-featured-image' => 'max-height: {{SIZE}}{{UNIT}};'], 'condition' => ['use_bg' => '0', 'bg_extend' => '']]);
        $this->add_group_control(Group_Control_Filters_CSS::get_type(), ['name' => 'filters_image', 'label' => 'Filters image', 'selector' => '{{WRAPPER}} .wrap-filters']);
        $this->add_control('blend_mode', ['label' => esc_html__('Blend Mode', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'options' => ['' => esc_html__('Normal', 'dynamic-content-for-elementor'), 'multiply' => esc_html__('Multiply', 'dynamic-content-for-elementor'), 'screen' => esc_html__('Screen', 'dynamic-content-for-elementor'), 'overlay' => esc_html__('Overlay', 'dynamic-content-for-elementor'), 'darken' => esc_html__('Darken', 'dynamic-content-for-elementor'), 'lighten' => esc_html__('Lighten', 'dynamic-content-for-elementor'), 'color-dodge' => esc_html__('Color Dodge', 'dynamic-content-for-elementor'), 'saturation' => esc_html__('Saturation', 'dynamic-content-for-elementor'), 'color' => esc_html__('Color', 'dynamic-content-for-elementor'), 'difference' => esc_html__('Difference', 'dynamic-content-for-elementor'), 'exclusion' => esc_html__('Exclusion', 'dynamic-content-for-elementor'), 'hue' => esc_html__('Hue', 'dynamic-content-for-elementor'), 'luminosity' => esc_html__('Luminosity', 'dynamic-content-for-elementor')], 'selectors' => ['{{WRAPPER}} .dce-featured-image' => 'mix-blend-mode: {{VALUE}}'], 'separator' => 'none']);
        $this->add_group_control(Group_Control_Border::get_type(), ['name' => 'image_border', 'label' => esc_html__('Image Border', 'dynamic-content-for-elementor'), 'selector' => '{{WRAPPER}} .dce-featured-image', 'condition' => ['use_bg' => '0', 'bg_extend' => '']]);
        $this->add_control('image_border_radius', ['label' => esc_html__('Border Radius', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => ['px', '%'], 'selectors' => ['{{WRAPPER}} .dce-featured-image, {{WRAPPER}} .dce-featured-image .dce-overlay_hover' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'], 'condition' => ['use_bg' => '0', 'bg_extend' => '']]);
        $this->add_control('image_padding', ['label' => esc_html__('Padding', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => ['px', '%'], 'selectors' => ['{{WRAPPER}} .dce-featured-image' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'], 'condition' => ['use_bg' => '0', 'bg_extend' => '']]);
        $this->add_group_control(Group_Control_Box_Shadow::get_type(), ['name' => 'image_box_shadow', 'selector' => '{{WRAPPER}} .dce-featured-image', 'condition' => ['use_bg' => '0', 'bg_extend' => '']]);
        $this->end_controls_section();
        $this->start_controls_section('section_source', ['label' => esc_html__('Source', 'dynamic-content-for-elementor'), 'tab' => Controls_Manager::TAB_CONTENT]);
        $this->add_control('data_source', ['label' => esc_html__('Source', 'dynamic-content-for-elementor'), 'description' => esc_html__('Select the data source', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => 'yes', 'label_on' => esc_html__('Same', 'dynamic-content-for-elementor'), 'label_off' => esc_html__('other', 'dynamic-content-for-elementor'), 'return_value' => 'yes']);
        $this->add_control('other_post_source', ['label' => esc_html__('Select from other source post', 'dynamic-content-for-elementor'), 'type' => 'ooo_query', 'placeholder' => esc_html__('Post Title', 'dynamic-content-for-elementor'), 'label_block' => \true, 'query_type' => 'posts', 'condition' => ['data_source' => '']]);
        $this->add_control('other_post_parent', ['label' => esc_html__('From post parent', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'return_value' => 'yes', 'condition' => ['data_source' => '']]);
        $this->end_controls_section();
    }
    protected function safe_render()
    {
        $settings = $this->get_settings_for_display();
        if (empty($settings)) {
            return;
        }
        $id_page = Helper::get_the_id($settings['other_post_source'] ?? \false, $settings['other_post_parent']);
        $type_page = get_post_type($id_page);
        $overlay_hover_block = '';
        if ($settings['link_to'] != 'none') {
            $overlay_hover_block = '<div class="dce-overlay_hover"></div>';
        }
        $overlay_block = '<div class="dce-overlay"></div>';
        $wrap_effect_start = '<div class="mask"><div class="wrap-filters">';
        $wrap_effect_end = '</div></div>';
        $image_size = $settings['size_size'];
        $featuredImageID = get_post_thumbnail_id($id_page);
        // se il post parent non ha un'immagine, uso uso l'immagine dello stesso
        if (!$featuredImageID && $settings['other_post_parent'] == 'yes') {
            $parent_id = wp_get_post_parent_id($id_page);
            if ($parent_id) {
                $featuredImageID = get_post_thumbnail_id($parent_id);
            }
        }
        if ($type_page == 'attachment') {
            $featuredImageID = get_the_ID();
        }
        $featured_img_url = '';
        $image_alt = '';
        if ($featuredImageID) {
            $image_url = Group_Control_Image_Size::get_attachment_image_src($featuredImageID, 'size', $settings);
            $image_alt = esc_attr(wp_strip_all_tags(get_post_meta($featuredImageID, '_wp_attachment_image_alt', \true), \true));
            $featured_img_url = $image_url;
        }
        if (!$featuredImageID && $settings['other_post_parent'] != 'yes') {
            if ($settings['use_placeholter'] && $settings['custom_placeholder_image'] != '') {
                $featured_img_url = $settings['custom_placeholder_image']['url'];
            }
        }
        $get_featured_img = '';
        if ($featured_img_url != '') {
            if ($image_alt) {
                $image_alt = ' alt="' . esc_attr($image_alt) . '"';
            }
            $get_featured_img = '<img src="' . esc_url($featured_img_url) . '"' . $image_alt . ' />';
        }
        $featured_image = '';
        if ($get_featured_img == '' && $settings['other_post_parent'] != 'yes') {
            $featured_image = $wrap_effect_start . '<img src="' . ($featured_img_url ? esc_url($featured_img_url) : '') . '" />' . $wrap_effect_end . $overlay_block . $overlay_hover_block;
        }
        if ($get_featured_img != '') {
            $featured_image = $wrap_effect_start . $get_featured_img . $wrap_effect_end . $overlay_block . $overlay_hover_block;
        }
        if (empty($featured_image)) {
            return;
        }
        $use_bg = $settings['use_bg'];
        $bg_class = '';
        if ($use_bg == '1') {
            $bg_class = 'is-bg ';
        }
        $target = !empty($settings['link']) && $settings['link']['is_external'] ? 'target="_blank"' : '';
        switch ($settings['link_to']) {
            case 'custom':
                if (!empty($settings['link']['url'])) {
                    $link = esc_url($settings['link']['url']);
                } else {
                    $link = \false;
                }
                break;
            case 'acf_url':
                if (!empty($settings['acf_field_url'])) {
                    $link = esc_url(\get_field($settings['acf_field_url'], $id_page));
                    $target = !empty($settings['acf_field_url_target']) ? 'target="_blank"' : '';
                } else {
                    $link = \false;
                }
                break;
            case 'file':
                $imageFull_url = wp_get_attachment_image_src($featuredImageID, 'full');
                $link = esc_url($imageFull_url[0]);
                break;
            case 'post':
                $link = esc_url(get_the_permalink($id_page));
                break;
            case 'home':
                $link = esc_url(get_home_url());
                break;
            case 'none':
            default:
                $link = \false;
                break;
        }
        if ($settings['hover_animation'] != '') {
            $animation_class = !empty($settings['hover_animation']) ? 'elementor-animation-' . $settings['hover_animation'] : '';
        } else {
            $animation_class = '';
        }
        $html = '<div class="' . esc_attr('dce-featured-image ' . $bg_class . $animation_class) . '">';
        if ($use_bg == 0) {
            if ($link) {
                $html .= \sprintf('<a href="%1$s" %2$s>%3$s</a>', $link, $target, $featured_image);
            } else {
                $html .= $featured_image;
            }
        } else {
            $bg_featured_image = $wrap_effect_start . '<figure class="dynamic-content-featuredimage-bg ' . esc_attr($animation_class) . '" style="background-image: url(&quot;' . esc_url($featured_img_url) . '&quot;); background-repeat: no-repeat; background-size: cover;">&nbsp;</figure>' . $wrap_effect_end . $overlay_block . $overlay_hover_block;
            if ($link) {
                $html .= \sprintf('<a href="%1$s" %2$s>%3$s</a>', $link, $target, $bg_featured_image);
            } else {
                $html .= $bg_featured_image;
            }
        }
        $html .= '</div>';
        echo $html;
    }
}
