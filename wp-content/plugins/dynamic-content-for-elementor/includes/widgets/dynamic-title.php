<?php

// SPDX-FileCopyrightText: 2018-2026 Ovation S.r.l. <help@dynamic.ooo>
// SPDX-License-Identifier: GPL-3.0-or-later
namespace DynamicContentForElementor\Widgets;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Group_Control_Background;
use DynamicContentForElementor\Helper;
// Exit if accessed directly
if (!\defined('ABSPATH')) {
    exit;
}
class DynamicTitle extends \DynamicContentForElementor\Widgets\WidgetPrototype
{
    public function run_once()
    {
        parent::run_once();
        $save_guard = \DynamicContentForElementor\Plugin::instance()->save_guard;
        $save_guard->register_unsafe_control($this->get_name(), 'other_post_source');
    }
    /**
     * Get Style Depends
     *
     * @return array<string>
     */
    public function get_style_depends()
    {
        return ['dce-dynamic-title'];
    }
    /**
     * Register controls after check if this feature is only for admin
     *
     * @return void
     */
    protected function safe_register_controls()
    {
        $this->start_controls_section('section_title', ['label' => $this->get_title()]);
        $this->add_control('html_tag', ['label' => esc_html__('HTML Tag', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'options' => Helper::get_html_tags(), 'default' => 'h3', 'separator' => 'before']);
        $this->add_responsive_control('align', ['label' => esc_html__('Alignment', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CHOOSE, 'options' => ['left' => ['title' => esc_html__('Left', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-left'], 'center' => ['title' => esc_html__('Center', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-center'], 'right' => ['title' => esc_html__('Right', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-right']], 'default' => '', 'selectors' => ['{{WRAPPER}}' => 'text-align: {{VALUE}};'], 'condition' => ['enable_divider' => '']]);
        $this->add_control('link_to', ['label' => esc_html__('Link to', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'default' => 'none', 'options' => ['none' => esc_html__('None', 'dynamic-content-for-elementor'), 'home' => esc_html__('Home URL', 'dynamic-content-for-elementor'), 'post' => 'Post URL', 'parent' => 'Parent Page', 'custom' => esc_html__('Custom URL', 'dynamic-content-for-elementor')]]);
        $this->add_control('link', ['label' => esc_html__('Link', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::URL, 'placeholder' => esc_html__('https://your-link.com', 'dynamic-content-for-elementor'), 'condition' => ['link_to' => 'custom'], 'default' => ['url' => ''], 'show_label' => \false]);
        $this->add_control('enable_divider', ['label' => esc_html__('Dividers', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => '', 'label_on' => esc_html__('Show', 'dynamic-content-for-elementor'), 'label_off' => esc_html__('Hide', 'dynamic-content-for-elementor'), 'return_value' => 'yes', 'render_type' => 'template', 'prefix_class' => 'dce-title-divider-']);
        $this->add_control('enable_masking', ['label' => esc_html__('Masking', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => '', 'label_on' => esc_html__('Show', 'dynamic-content-for-elementor'), 'label_off' => esc_html__('Hide', 'dynamic-content-for-elementor'), 'return_value' => 'yes', 'render_type' => 'template', 'prefix_class' => 'dce-title-mask-']);
        $this->end_controls_section();
        $this->start_controls_section('section_title_dividers', ['label' => esc_html__('Dividers', 'dynamic-content-for-elementor'), 'condition' => ['enable_divider' => 'yes']]);
        $this->add_control('style_dividers', ['label' => esc_html__('Style', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'options' => ['solid' => esc_html__('Solid', 'dynamic-content-for-elementor'), 'double' => esc_html__('Double', 'dynamic-content-for-elementor'), 'dotted' => esc_html__('Dotted', 'dynamic-content-for-elementor'), 'dashed' => esc_html__('Dashed', 'dynamic-content-for-elementor')], 'default' => 'solid', 'selectors' => ['{{WRAPPER}} .dce-divider:after, {{WRAPPER}} .dce-divider:before' => 'border-top-style: {{VALUE}};']]);
        $this->add_control('weight_dividers', ['label' => esc_html__('Weight', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => 1], 'range' => ['px' => ['min' => 1, 'max' => 50]], 'selectors' => ['{{WRAPPER}} .dce-divider:after, {{WRAPPER}} .dce-divider:before' => 'border-top-width: {{SIZE}}{{UNIT}};']]);
        $this->add_control('color_dividers', ['label' => esc_html__('Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'default' => '', 'selectors' => ['{{WRAPPER}} .dce-divider:after, {{WRAPPER}} .dce-divider:before' => 'border-top-color: {{VALUE}};']]);
        $this->add_responsive_control('width_dividers', ['label' => esc_html__('Width', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'size_units' => ['%', 'px'], 'range' => ['px' => ['max' => 600, 'min' => 10, 'step' => 1]], 'default' => ['size' => 25, 'unit' => '%'], 'tablet_default' => ['unit' => '%'], 'mobile_default' => ['unit' => '%'], 'selectors' => ['{{WRAPPER}} .dce-divider:after, {{WRAPPER}} .dce-divider:before' => 'width: {{SIZE}}{{UNIT}};']]);
        $this->add_responsive_control('gap_dividers', ['label' => esc_html__('Gap', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => 15], 'range' => ['px' => ['min' => 2, 'max' => 80]], 'selectors' => ['{{WRAPPER}} .dce-title-divider' => 'padding-right: {{SIZE}}{{UNIT}}; padding-left: {{SIZE}}{{UNIT}};']]);
        $this->add_responsive_control('space_dividers', ['label' => esc_html__('Space', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => 15], 'range' => ['px' => ['min' => -100, 'max' => 100]], 'selectors' => ['{{WRAPPER}} .dce-title-divider' => 'margin-top: {{SIZE}}{{UNIT}}; margin-bottom: {{SIZE}}{{UNIT}};']]);
        $this->add_responsive_control('divider_position', ['label' => esc_html__('Position', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CHOOSE, 'default' => 'center', 'options' => ['left' => ['title' => esc_html__('Left', 'dynamic-content-for-elementor'), 'icon' => 'eicon-h-align-left'], 'center' => ['title' => esc_html__('Center', 'dynamic-content-for-elementor'), 'icon' => 'eicon-h-align-center'], 'right' => ['title' => esc_html__('Right', 'dynamic-content-for-elementor'), 'icon' => 'eicon-h-align-right'], 'top' => ['title' => esc_html__('Top', 'dynamic-content-for-elementor'), 'icon' => 'eicon-v-align-top'], 'bottom' => ['title' => esc_html__('Bottom', 'dynamic-content-for-elementor'), 'icon' => 'eicon-v-align-bottom']], 'prefix_class' => 'dce-divider-position%s-']);
        $this->add_responsive_control('divider_align', ['label' => esc_html__('Alignment', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CHOOSE, 'options' => ['left' => ['title' => esc_html__('Left', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-left'], 'center' => ['title' => esc_html__('Center', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-center'], 'right' => ['title' => esc_html__('Right', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-right']], 'default' => '', 'prefix_class' => 'dce-title-align%s-', 'selectors' => ['{{WRAPPER}} .dce-title-divider' => 'text-align: {{VALUE}};'], 'condition' => ['divider_position' => ['top', 'bottom']]]);
        $this->end_controls_section();
        $this->start_controls_section('section_title_masking', ['label' => esc_html__('Masking', 'dynamic-content-for-elementor'), 'condition' => ['enable_masking' => 'yes']]);
        $this->add_group_control(Group_Control_Background::get_type(), ['name' => 'background_masking', 'types' => ['classic', 'gradient'], 'fields_options' => ['background' => ['frontend_available' => \true], 'video_link' => ['frontend_available' => \true]], 'selector' => '{{WRAPPER}} span']);
        $this->end_controls_section();
        $this->start_controls_section('section_source', ['label' => esc_html__('Source', 'dynamic-content-for-elementor')]);
        $this->add_control('data_source', ['label' => esc_html__('Source', 'dynamic-content-for-elementor'), 'description' => esc_html__('Select the data source', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => 'yes', 'label_on' => esc_html__('Same', 'dynamic-content-for-elementor'), 'label_off' => esc_html__('other', 'dynamic-content-for-elementor'), 'return_value' => 'yes']);
        $this->add_control('other_post_source', ['label' => esc_html__('Select from other source post', 'dynamic-content-for-elementor'), 'type' => 'ooo_query', 'placeholder' => esc_html__('Post Title', 'dynamic-content-for-elementor'), 'label_block' => \true, 'query_type' => 'posts', 'condition' => ['data_source' => '']]);
        $this->add_control('other_post_parent', ['label' => esc_html__('From post parent', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'condition' => ['data_source' => '']]);
        $this->end_controls_section();
        $this->start_controls_section('section_style', ['label' => esc_html__('Title', 'dynamic-content-for-elementor'), 'tab' => Controls_Manager::TAB_STYLE]);
        $this->add_control('color', ['label' => esc_html__('Text Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .dce-title' => 'color: {{VALUE}};', '{{WRAPPER}} a' => 'color: {{VALUE}};']]);
        $this->add_group_control(Group_Control_Typography::get_type(), ['name' => 'typography', 'selector' => '{{WRAPPER}} .dce-title']);
        $this->add_group_control(Group_Control_Text_Shadow::get_type(), ['name' => 'text_shadow', 'selector' => '{{WRAPPER}} .dce-title']);
        $this->add_control('blend_mode', ['label' => esc_html__('Blend Mode', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'options' => ['' => esc_html__('Normal', 'dynamic-content-for-elementor'), 'multiply' => esc_html__('Multiply', 'dynamic-content-for-elementor'), 'screen' => esc_html__('Screen', 'dynamic-content-for-elementor'), 'overlay' => esc_html__('Overlay', 'dynamic-content-for-elementor'), 'darken' => esc_html__('Darken', 'dynamic-content-for-elementor'), 'lighten' => esc_html__('Lighten', 'dynamic-content-for-elementor'), 'color-dodge' => esc_html__('Color Dodge', 'dynamic-content-for-elementor'), 'saturation' => esc_html__('Saturation', 'dynamic-content-for-elementor'), 'color' => esc_html__('Color', 'dynamic-content-for-elementor'), 'difference' => esc_html__('Difference', 'dynamic-content-for-elementor'), 'exclusion' => esc_html__('Exclusion', 'dynamic-content-for-elementor'), 'hue' => esc_html__('Hue', 'dynamic-content-for-elementor'), 'luminosity' => esc_html__('Luminosity', 'dynamic-content-for-elementor')], 'selectors' => ['{{WRAPPER}}' => 'mix-blend-mode: {{VALUE}}'], 'separator' => 'before']);
        $this->end_controls_section();
        $this->start_controls_section('section_rollhover', ['label' => esc_html__('Rollover', 'dynamic-content-for-elementor'), 'tab' => Controls_Manager::TAB_STYLE, 'condition' => ['link_to!' => 'none']]);
        $this->add_control('hover_color', ['label' => esc_html__('Hover Text Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} a:hover' => 'color: {{VALUE}};'], 'condition' => ['link_to!' => 'none']]);
        $this->add_control('hover_animation', ['label' => esc_html__('Hover Animation', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HOVER_ANIMATION, 'condition' => ['link_to!' => 'none']]);
        $this->end_controls_section();
    }
    /**
     * Safe Render
     *
     * @return void
     */
    protected function safe_render()
    {
        $settings = $this->get_settings_for_display();
        if (empty($settings)) {
            return;
        }
        $id_page = Helper::get_the_id($settings['other_post_source'] ?? \false, $settings['other_post_parent']);
        if (!empty($settings['other_post_source']) || !empty($settings['other_post_parent'])) {
            $title = wp_kses_post(get_the_title($id_page));
        } else {
            $queried_object = get_queried_object();
            switch (\true) {
                case $queried_object instanceof \WP_Post:
                    $title = wp_kses_post(get_the_title());
                    break;
                case $queried_object instanceof \WP_Term:
                    $title = wp_kses_post(single_term_title('', \false));
                    break;
                case $queried_object instanceof \WP_Post_Type:
                    $title = wp_kses_post(post_type_archive_title('', \false));
                    break;
                case $queried_object instanceof \WP_User:
                    $title = wp_kses_post($queried_object->display_name);
                    break;
                default:
                    return;
            }
        }
        if (empty($title)) {
            return;
        }
        $this->set_render_attribute('title', 'class', 'dce-title');
        // Link
        switch ($settings['link_to']) {
            case 'custom':
                $link = !empty($settings['link']['url']) ? esc_url_raw($settings['link']['url']) : \false;
                break;
            case 'post':
                $link = get_the_permalink($id_page);
                break;
            case 'parent':
                $id_page_parent = wp_get_post_parent_id($id_page);
                $permalink = get_the_permalink($id_page_parent);
                $link = $id_page_parent && $permalink ? esc_url_raw($permalink) : \false;
                break;
            case 'home':
                $link = esc_url_raw(get_home_url());
                break;
            default:
                $link = \false;
        }
        if ($link) {
            $this->set_render_attribute('link', 'href', $link);
            if (!empty($settings['link']) && $settings['link']['is_external']) {
                $this->set_render_attribute('link', 'target', '_blank');
            }
            if (!empty($settings['link']['nofollow'])) {
                $this->set_render_attribute('link', 'rel', 'nofollow');
            }
        }
        if (!empty($settings['hover_animation'])) {
            $this->add_render_attribute('title', 'class', 'elementor-animation-' . $settings['hover_animation']);
        }
        echo '<' . Helper::validate_html_tag($settings['html_tag']) . ' ' . $this->get_render_attribute_string('title') . '>';
        if (!empty($settings['enable_divider'])) {
            echo '<div class="dce-divider"><span class="dce-title-divider">';
        }
        if ($link) {
            echo '<a ' . $this->get_render_attribute_string('link') . '>';
        }
        echo $title;
        if ($link) {
            echo '</a>';
        }
        if (!empty($settings['enable_divider'])) {
            echo '</span></div>';
        }
        echo '</' . Helper::validate_html_tag($settings['html_tag']) . '>';
    }
}
