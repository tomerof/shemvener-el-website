<?php

// SPDX-FileCopyrightText: 2018-2026 Ovation S.r.l. <help@dynamic.ooo>
// SPDX-License-Identifier: GPL-3.0-or-later
namespace DynamicContentForElementor\Widgets;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use DynamicContentForElementor\Helper;
// Exit if accessed directly
if (!\defined('ABSPATH')) {
    exit;
}
class TaxonomyTitle extends \DynamicContentForElementor\Widgets\WidgetPrototype
{
    public function get_style_depends()
    {
        return ['dce-title'];
    }
    /**
     * Register controls after check if this feature is only for admin
     *
     * @return void
     */
    protected function safe_register_controls()
    {
        $post_type_object = get_post_type_object(get_post_type());
        $this->start_controls_section('section_titleTaxonomy', ['label' => esc_html__('Taxonomy Title', 'dynamic-content-for-elementor')]);
        $this->add_control('titleTaxonomy_text_before', ['label' => esc_html__('Text Before', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'default' => '']);
        $this->add_control('html_tag', ['label' => esc_html__('HTML Tag', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'options' => Helper::get_html_tags(), 'default' => 'h2']);
        $this->add_responsive_control('align', ['label' => esc_html__('Alignment', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CHOOSE, 'options' => ['left' => ['title' => esc_html__('Left', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-left'], 'center' => ['title' => esc_html__('Center', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-center'], 'right' => ['title' => esc_html__('Right', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-right'], 'justify' => ['title' => esc_html__('Justified', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-justify']], 'default' => '', 'selectors' => ['{{WRAPPER}}' => 'text-align: {{VALUE}};']]);
        $this->add_control('link_to', ['label' => esc_html__('Link to', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'default' => 'none', 'options' => ['none' => esc_html__('None', 'dynamic-content-for-elementor'), 'home' => esc_html__('Home URL', 'dynamic-content-for-elementor'), 'archive' => 'Archive URL', 'custom' => esc_html__('Custom URL', 'dynamic-content-for-elementor')]]);
        $this->add_control('link', ['label' => esc_html__('Link', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::URL, 'placeholder' => esc_html__('https://your-link.com', 'dynamic-content-for-elementor'), 'condition' => ['link_to' => 'custom'], 'default' => ['url' => ''], 'show_label' => \false]);
        $this->end_controls_section();
        $this->start_controls_section('section_style', ['label' => esc_html__('Title', 'dynamic-content-for-elementor'), 'tab' => Controls_Manager::TAB_STYLE]);
        $this->add_control('color', ['label' => esc_html__('Text Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .dynamic-content-for-elementor-title' => 'color: {{VALUE}};', '{{WRAPPER}} .dynamic-content-for-elementor-title a' => 'color: {{VALUE}};']]);
        $this->add_group_control(Group_Control_Typography::get_type(), ['name' => 'typography', 'selector' => '{{WRAPPER}} .dynamic-content-for-elementor-title']);
        $this->add_control('hover_animation', ['label' => esc_html__('Hover Animation', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HOVER_ANIMATION]);
        $this->end_controls_section();
    }
    protected function safe_render()
    {
        $settings = $this->get_settings_for_display();
        if (empty($settings)) {
            return;
        }
        $titolo = '';
        $title = '';
        if (is_single() || is_page()) {
            $titolo = wp_kses_post(get_the_title());
        } elseif (is_post_type_archive() || is_tax() || is_category() || is_tag() || is_author()) {
            // Archives
            $queried_object = get_queried_object();
            $tax = get_taxonomy($queried_object->taxonomy);
            $tax_labels = get_taxonomy_labels($tax);
            $titolo = wp_kses_post($tax_labels->singular_name ?? '');
        } else {
            // All other Taxonomies
            $titolo = wp_kses_post(get_the_title());
        }
        if ($settings['titleTaxonomy_text_before'] != '') {
            $title .= '<span>' . wp_kses_post($settings['titleTaxonomy_text_before']) . '</span>';
        }
        $title .= $titolo;
        if (empty($title)) {
            return;
        }
        switch ($settings['link_to']) {
            case 'custom':
                if (!empty($settings['link']['url'])) {
                    $link = esc_url($settings['link']['url']);
                } else {
                    $link = \false;
                }
                break;
            case 'archive':
                $link = '';
                break;
            case 'home':
                $link = esc_url(get_home_url());
                break;
            case 'none':
            default:
                $link = \false;
                break;
        }
        $target = !empty($settings['link']['is_external']) ? 'target="_blank"' : '';
        $animation_class = !empty($settings['hover_animation']) ? 'elementor-animation-' . $settings['hover_animation'] : '';
        $html = \sprintf('<%1$s class="dynamic-content-for-elementor-title %2$s">', \DynamicContentForElementor\Helper::validate_html_tag($settings['html_tag']), esc_attr($animation_class));
        if ($link) {
            $html .= \sprintf('<a href="%1$s" %2$s>%3$s</a>', esc_url($link), $target, $title);
        } else {
            $html .= $title;
        }
        $html .= \sprintf('</%s>', \DynamicContentForElementor\Helper::validate_html_tag($settings['html_tag']));
        echo $html;
    }
}
