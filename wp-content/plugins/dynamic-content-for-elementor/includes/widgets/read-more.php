<?php

// SPDX-FileCopyrightText: 2018-2026 Ovation S.r.l. <help@dynamic.ooo>
// SPDX-License-Identifier: GPL-3.0-or-later
namespace DynamicContentForElementor\Widgets;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Icons_Manager;
use DynamicContentForElementor\Helper;
// Exit if accessed directly
if (!\defined('ABSPATH')) {
    exit;
}
class ReadMore extends \DynamicContentForElementor\Widgets\WidgetPrototype
{
    public function run_once()
    {
        parent::run_once();
        $save_guard = \DynamicContentForElementor\Plugin::instance()->save_guard;
        $save_guard->register_unsafe_control($this->get_name(), 'other_post_source');
        $save_guard->register_unsafe_control($this->get_name(), 'button_html');
    }
    public function get_style_depends()
    {
        return ['dce-readmore'];
    }
    /**
     * Register controls after check if this feature is only for admin
     *
     * @return void
     */
    protected function safe_register_controls()
    {
        $post_type_object = get_post_type_object(get_post_type());
        $this->start_controls_section('section_readmore', ['label' => esc_html__('Button', 'dynamic-content-for-elementor')]);
        $this->add_control('html_tag', ['label' => esc_html__('HTML Tag', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'options' => Helper::get_html_tags(['button']), 'default' => 'div', 'condition' => ['link_to' => 'none']]);
        $this->add_control('type_of_button', ['label' => esc_html__('Button type', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'default' => 'html', 'options' => ['text' => esc_html__('Text', 'dynamic-content-for-elementor'), 'html' => esc_html__('HTML', 'dynamic-content-for-elementor'), 'image' => esc_html__('Image', 'dynamic-content-for-elementor')]]);
        $this->add_control('button_text', ['label' => esc_html__('Button Text', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'default' => esc_html__('Read More', 'dynamic-content-for-elementor'), 'placeholder' => esc_html__('Read More', 'dynamic-content-for-elementor'), 'label_block' => \true, 'dynamic' => ['active' => \true], 'condition' => ['type_of_button' => 'text']]);
        $this->add_control('button_html', ['label' => esc_html__('Button HTML', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CODE, 'language' => 'html', 'default' => esc_html__('Read More', 'dynamic-content-for-elementor'), 'condition' => ['type_of_button' => 'html']]);
        $this->add_control('button_image', ['label' => esc_html__('Button Image', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::MEDIA, 'default' => ['url' => ''], 'condition' => ['type_of_button' => 'image']]);
        $this->add_responsive_control('align', ['label' => esc_html__('Alignment', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CHOOSE, 'options' => ['left' => ['title' => esc_html__('Left', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-left'], 'center' => ['title' => esc_html__('Center', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-center'], 'right' => ['title' => esc_html__('Right', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-right'], 'justify' => ['title' => esc_html__('Justified', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-justify']], 'default' => '', 'prefix_class' => 'rmbtn-align-', 'selectors' => ['{{WRAPPER}}' => 'text-align: {{VALUE}};'], 'condition' => ['type_of_button' => 'html']]);
        $this->add_responsive_control('align_html', ['label' => esc_html__('Alignment', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CHOOSE, 'options' => ['left' => ['title' => esc_html__('Left', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-left'], 'center' => ['title' => esc_html__('Center', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-center'], 'right' => ['title' => esc_html__('Right', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-right']], 'default' => '', 'prefix_class' => 'rmbtn-align-', 'selectors' => ['{{WRAPPER}}' => 'text-align: {{VALUE}};'], 'condition' => ['type_of_button' => 'text']]);
        $this->add_responsive_control('align_justify', ['label' => esc_html__('Justify Alignment', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CHOOSE, 'options' => ['flex-start' => ['title' => esc_html__('Left', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-left'], 'center' => ['title' => esc_html__('Center', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-center'], 'flex-end' => ['title' => esc_html__('Right', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-right']], 'default' => '', 'selectors' => ['{{WRAPPER}} .dce-btn-readmore, {{WRAPPER}} .dce-btn-readmore:after, {{WRAPPER}} .dce-btn-readmore:before' => 'justify-content: {{VALUE}};'], 'condition' => ['align' => 'justify', 'type_of_button' => 'html']]);
        $this->add_responsive_control('rm_width', ['label' => esc_html__('Width', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HIDDEN, 'default' => ['size' => '', 'unit' => '%'], 'size_units' => ['%', 'px'], 'range' => ['%' => ['max' => 100, 'min' => 0, 'step' => 1], 'px' => ['max' => 300, 'min' => 0, 'step' => 1]], 'selectors' => ['{{WRAPPER}} .dce-btn-readmore' => 'width: {{SIZE}}{{UNIT}};'], 'condition' => ['align!' => 'justify', 'type_of_button' => 'html']]);
        $this->add_control('selected_icon_rm', ['label' => esc_html__('Icons', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::ICONS, 'default' => ['value' => '', 'library' => 'solid'], 'fa4compatibility' => 'icon_rm']);
        $this->add_control('icon_rm_position', ['label' => esc_html__('Icon Position', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'default' => 'left', 'options' => ['left' => esc_html__('Before', 'dynamic-content-for-elementor'), 'right' => esc_html__('After', 'dynamic-content-for-elementor')], 'render_type' => 'template', 'prefix_class' => 'icon-', 'condition' => ['selected_icon_rm[value]!' => '']]);
        $this->add_control('link_to', ['label' => esc_html__('Link to', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'default' => 'post', 'options' => ['none' => esc_html__('None', 'dynamic-content-for-elementor'), 'home' => esc_html__('Home URL', 'dynamic-content-for-elementor'), 'post' => esc_html__('Post URL', 'dynamic-content-for-elementor'), 'custom_field' => esc_html__('Custom field', 'dynamic-content-for-elementor'), 'custom' => esc_html__('Custom URL', 'dynamic-content-for-elementor')]]);
        $this->add_control('link', ['label' => esc_html__('Link', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::URL, 'placeholder' => esc_html__('https://your-link.com', 'dynamic-content-for-elementor'), 'condition' => ['link_to' => 'custom'], 'default' => ['url' => ''], 'show_label' => \false, 'dynamic' => ['active' => \true]]);
        $this->add_control('custom_field_id', ['label' => esc_html__('Meta Field', 'dynamic-content-for-elementor'), 'type' => 'ooo_query', 'placeholder' => esc_html__('Meta key or Field Name', 'dynamic-content-for-elementor'), 'label_block' => \true, 'query_type' => 'metas', 'object_type' => 'post', 'condition' => ['link_to' => 'custom_field']]);
        $this->add_control('custom_field_target', ['label' => esc_html__('Target', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => 'yes', 'label_on' => esc_html__('Blank', 'dynamic-content-for-elementor'), 'label_off' => esc_html__('Same', 'dynamic-content-for-elementor'), 'return_value' => 'yes', 'condition' => ['link_to' => 'custom_field']]);
        $this->add_control('data_source', ['label' => esc_html__('Source', 'dynamic-content-for-elementor'), 'description' => esc_html__('Select the data source', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => 'yes', 'label_on' => esc_html__('Same', 'dynamic-content-for-elementor'), 'label_off' => esc_html__('other', 'dynamic-content-for-elementor'), 'return_value' => 'yes', 'separator' => 'before', 'condition' => ['link_to' => 'post']]);
        $this->add_control('other_post_source', ['label' => esc_html__('Select from other source post', 'dynamic-content-for-elementor'), 'type' => 'ooo_query', 'placeholder' => esc_html__('Post Title', 'dynamic-content-for-elementor'), 'label_block' => \true, 'query_type' => 'posts', 'condition' => ['link_to' => 'post', 'data_source' => '']]);
        $this->end_controls_section();
        // ------------------------------------------- [SECTION STYLE]
        $this->start_controls_section('section_style', ['label' => esc_html__('Button', 'dynamic-content-for-elementor'), 'tab' => Controls_Manager::TAB_STYLE]);
        $this->add_control('readmore_color', ['label' => esc_html__('Text Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .dce-btn-readmore > span, {{WRAPPER}} .dce-btn-readmore .icon-rm,  {{WRAPPER}} .dce-btn-readmore:before,  {{WRAPPER}} .dce-btn-readmore:after' => 'color: {{VALUE}};', '{{WRAPPER}} .button--asolo:before, {{WRAPPER}} .button--asolo:after' => 'border-color: {{VALUE}};']]);
        $this->add_control('readmore_bgcolor', ['label' => esc_html__('Background Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .dce-btn-readmore:not(.button--pipaluk), {{WRAPPER}} .button--pipaluk:after, {{WRAPPER}} .button--tamaya:before, {{WRAPPER}} .button--tamaya:after' => 'background-color: {{VALUE}};']]);
        $this->add_group_control(Group_Control_Typography::get_type(), ['name' => 'typography', 'selector' => '{{WRAPPER}} .dce-btn-readmore']);
        $this->add_control('readmore_space_heading', ['label' => esc_html__('Space', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HEADING, 'separator' => 'before']);
        $this->add_responsive_control('readmore_padding', ['label' => esc_html__('Padding', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => ['px', '%'], 'default' => ['top' => 10, 'right' => 20, 'bottom' => 10, 'left' => 20], 'selectors' => ['{{WRAPPER}} .dce-btn-readmore > span, {{WRAPPER}} .dce-btn-readmore:after, {{WRAPPER}} .dce-btn-readmore:before' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};', '{{WRAPPER}} .dce-btn-readmore.icon_button .icon-rm' => 'padding-left: {{LEFT}}{{UNIT}}; padding-right: {{LEFT}}{{UNIT}};']]);
        $this->add_responsive_control('readmore_margin', ['label' => esc_html__('Margin', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => ['px', '%'], 'selectors' => ['{{WRAPPER}} .dce-btn-readmore' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};']]);
        $this->add_control('readmore_style_heading', ['label' => esc_html__('Style', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HEADING, 'separator' => 'before']);
        $this->add_group_control(Group_Control_Border::get_type(), ['name' => 'readmore_border', 'label' => esc_html__('Border', 'dynamic-content-for-elementor'), 'selector' => '{{WRAPPER}} .dce-btn-readmore, {{WRAPPER}} .button--asolo:after, {{WRAPPER}} .button--asolo:before']);
        $this->add_control('readmore_border_radius', ['label' => esc_html__('Border Radius', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => ['px', '%'], 'selectors' => ['{{WRAPPER}} .dce-btn-readmore, {{WRAPPER}} .dce-btn-readmore:before, {{WRAPPER}} .dce-btn-readmore:after' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};']]);
        $this->add_group_control(Group_Control_Text_Shadow::get_type(), ['name' => 'text_shadow', 'selector' => '{{WRAPPER}} .dce-btn-readmore']);
        $this->add_group_control(Group_Control_Box_Shadow::get_type(), ['name' => 'box_shadow_readmore', 'selector' => '{{WRAPPER}} .dce-btn-readmore']);
        $this->end_controls_section();
        // ------------------------------------------- [SECTION STYLE - ICON]
        $this->start_controls_section('section_icon_style', ['label' => esc_html__('Icon', 'dynamic-content-for-elementor'), 'tab' => Controls_Manager::TAB_STYLE, 'condition' => ['selected_icon_rm[value]!' => '']]);
        $this->add_control('readmore_icon_color', ['label' => esc_html__('Icon Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .dce-btn-readmore .icon-rm' => 'color: {{VALUE}};']]);
        $this->add_responsive_control('size_icon_rm', ['label' => esc_html__('Size', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'range' => ['px' => ['min' => 0, 'max' => 100]], 'selectors' => ['{{WRAPPER}} .dce-btn-readmore .icon-rm' => 'font-size: {{SIZE}}{{UNIT}};']]);
        $this->add_control('space_icon_rm', ['label' => esc_html__('Space', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => 7], 'range' => ['px' => ['min' => 0, 'max' => 100]], 'selectors' => ['{{WRAPPER}}.icon-left .icon-rm' => 'padding-right: {{SIZE}}{{UNIT}} !important;', '{{WRAPPER}}.icon-right .icon-rm' => 'padding-left: {{SIZE}}{{UNIT}} !important;'], 'condition' => ['selected_icon_rm[value]!' => '']]);
        $this->add_responsive_control('ypos_icon_rm', ['label' => esc_html__('Position Y', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'range' => ['px' => ['min' => -100, 'max' => 100]], 'default' => ['size' => ''], 'selectors' => ['{{WRAPPER}} .dce-btn-readmore .icon-rm' => 'top: {{SIZE}}{{UNIT}} !important;'], 'condition' => ['align!' => 'justify']]);
        $this->add_responsive_control('xpos_icon_rm', ['label' => esc_html__('Position X', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'range' => ['px' => ['min' => -100, 'max' => 100]], 'default' => ['size' => ''], 'selectors' => ['{{WRAPPER}} .dce-btn-readmore .icon-rm' => 'left: {{SIZE}}{{UNIT}} !important;'], 'condition' => ['align!' => 'justify']]);
        $this->end_controls_section();
        $this->start_controls_section('section_rolhover_style', ['label' => esc_html__('Rollover', 'dynamic-content-for-elementor'), 'tab' => Controls_Manager::TAB_STYLE, 'condition' => ['link_to!' => 'none']]);
        $this->add_control('readmore_hover_heading', ['label' => esc_html__('Rollover', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HEADING, 'separator' => 'before']);
        $this->add_control('readmore_color_hover', ['label' => esc_html__('Text Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'default' => '', 'selectors' => ['{{WRAPPER}} a.dce-btn-readmore:hover span, {{WRAPPER}} a.dce-btn-readmore:hover:after,  {{WRAPPER}} a.dce-btn-readmore:hover:before, {{WRAPPER}} a.dce-btn-readmore:hover .icon-rm' => 'color: {{VALUE}};']]);
        $this->add_control('readmore_bgcolor_hover', ['label' => esc_html__('Background Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'default' => '', 'selectors' => ['{{WRAPPER}} a.dce-btn-readmore:not(.button--pipaluk):not(.button--isi):not(.button--aylen):hover, {{WRAPPER}} a.dce-btn-readmore:not(.button--pipaluk):hover:after, {{WRAPPER}} a.dce-btn-readmore:not(.button--pipaluk):not(.button--wapasha):not(.button--nina):hover:before, {{WRAPPER}} .button--pipaluk:hover:after, {{WRAPPER}} .button--moema:before, {{WRAPPER}} .button--aylen:after, {{WRAPPER}} .button--aylen:before, {{WRAPPER}} .dce-type-html:hover' => 'background-color: {{VALUE}};', '{{WRAPPER}} .button--pipaluk:before, {{WRAPPER}} .button--wapasha:before, {{WRAPPER}} .button--antiman:before, {{WRAPPER}} .button--itzel:before' => 'border-color: {{VALUE}};']]);
        $this->add_control('readmore_icon_color_hover', ['label' => esc_html__('Icon Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'default' => '', 'selectors' => ['{{WRAPPER}} a.dce-btn-readmore:hover .icon-rm' => 'color: {{VALUE}};'], 'condition' => ['selected_icon_rm[value]!' => '']]);
        $this->add_control('readmore_bordercolor_hover', ['label' => esc_html__('Border Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} a.dce-btn-readmore:hover' => 'border-color: {{VALUE}};'], 'condition' => ['readmore_border_border!' => '']]);
        $this->add_control('style_effect', ['label' => esc_html__('Effect', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'separator' => 'before', 'options' => ['' => esc_html__('None', 'dynamic-content-for-elementor'), 'button--asolo' => esc_html__('Asolo', 'dynamic-content-for-elementor'), 'button--winona' => esc_html__('Winona', 'dynamic-content-for-elementor'), 'button--ujarak' => esc_html__('Ujarak', 'dynamic-content-for-elementor'), 'button--wayra' => esc_html__('Wayra', 'dynamic-content-for-elementor'), 'button--tamaya' => esc_html__('Tamaya', 'dynamic-content-for-elementor'), 'button--rayen' => esc_html__('Rayen', 'dynamic-content-for-elementor'), 'button--pipaluk' => esc_html__('Pipaluk', 'dynamic-content-for-elementor'), 'button--nuka' => esc_html__('Nuka', 'dynamic-content-for-elementor'), 'button--moema' => esc_html__('Moema', 'dynamic-content-for-elementor'), 'button--isi' => esc_html__('Isi', 'dynamic-content-for-elementor'), 'button--aylen' => esc_html__('Aylen', 'dynamic-content-for-elementor'), 'button--saqui' => esc_html__('Saqui', 'dynamic-content-for-elementor'), 'button--wapasha' => esc_html__('Wapasha', 'dynamic-content-for-elementor'), 'button--nina' => esc_html__('Nina', 'dynamic-content-for-elementor'), 'button--nanuk' => esc_html__('Nanuk', 'dynamic-content-for-elementor'), 'button--antiman' => esc_html__('Antiman', 'dynamic-content-for-elementor'), 'button--itzel' => esc_html__('Itzel', 'dynamic-content-for-elementor')], 'default' => '', 'condition' => ['link_to!' => 'none', 'hover_animation' => '', 'type_of_button' => 'text']]);
        $this->add_control('hover_animation', ['label' => esc_html__('Hover Animation', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HOVER_ANIMATION, 'condition' => ['link_to!' => 'none', 'style_effect' => ''], 'separator' => 'before']);
        $this->end_controls_section();
    }
    protected function safe_render()
    {
        $settings = $this->get_settings_for_display();
        if (empty($settings)) {
            return;
        }
        $id_page = Helper::get_the_id($settings['other_post_source'] ?? \false);
        $content_button_raw = '';
        if ('text' === $settings['type_of_button']) {
            $content_button_raw = wp_kses_post($settings['button_text']);
        } elseif ('html' === $settings['type_of_button']) {
            $content_button_raw = $settings['button_html'];
        } elseif ('image' === $settings['type_of_button']) {
            $content_button_raw = esc_url($settings['button_image']['url']);
        }
        $title = '<span>' . $content_button_raw . '</span>';
        switch ($settings['link_to']) {
            case 'custom':
                if ($settings['link']['url']) {
                    $link = esc_url($settings['link']['url']);
                } else {
                    $link = '#';
                }
                break;
            case 'post':
                $link = esc_url(get_the_permalink($id_page));
                break;
            case 'custom_field':
                if ($settings['custom_field_id']) {
                    $link = get_post_meta($id_page, $settings['custom_field_id'], \true);
                } else {
                    $link = \false;
                }
                if (\is_numeric($link)) {
                    $link = wp_get_attachment_url($link);
                }
                break;
            case 'home':
                $link = esc_url(get_home_url());
                break;
            default:
                $link = \false;
                break;
        }
        $target = !empty($settings['link']['is_external']) ? 'target="_blank"' : '';
        $nofollow = !empty($settings['link']['nofollow']) ? 'rel="nofollow"' : '';
        if (!empty($settings['custom_field_target'])) {
            $target = 'target="_blank"';
        }
        $animation_class = !empty($settings['hover_animation']) ? ' elementor-animation-' . esc_attr($settings['hover_animation']) : '';
        $effect_class = !empty($settings['style_effect']) && $link ? ' eff_button ' . esc_attr($settings['style_effect']) : '';
        // New icon system
        if (empty($settings['icon_rm']) && !Icons_Manager::is_migration_allowed()) {
            // add old default
            $settings['icon_rm'] = '';
            //this is the default value for the old method
        }
        $migrated = isset($settings['__fa4_migrated']['selected_icon_rm']);
        $is_new = empty($settings['icon_rm']) && Icons_Manager::is_migration_allowed();
        if ($is_new || $migrated) {
            $icon_class = !empty($settings['selected_icon_rm']['value']) ? ' icon_button' : '';
            if ($settings['icon_rm_position'] == 'left') {
                $title = '<i class="icon-rm ' . esc_attr($settings['selected_icon_rm']['value']) . '" aria-hidden="true"></i>' . $title;
            } elseif ($settings['icon_rm_position'] == 'right') {
                $title = $title . '<i class="icon-rm ' . esc_attr($settings['selected_icon_rm']['value']) . '" aria-hidden="true"></i>';
            }
        } else {
            $icon_class = !empty($settings['icon_rm']) ? ' icon_button' : '';
            if ($settings['icon_rm_position'] == 'left') {
                $title = '<i class="icon-rm ' . esc_attr($settings['icon_rm']) . '" aria-hidden="true"></i>' . $title;
            } elseif ($settings['icon_rm_position'] == 'right') {
                $title = $title . '<i class="icon-rm ' . esc_attr($settings['icon_rm']) . '" aria-hidden="true"></i>';
            }
        }
        if (empty($title)) {
            return;
        }
        $html = '';
        $data_text_effect = '';
        $class_typebutton = ' dce-type-html';
        if ($settings['style_effect'] != '' && $settings['type_of_button'] == 'text' && $link) {
            $data_text_effect = ' data-text="' . esc_attr($content_button_raw) . '"';
            $class_typebutton = ' dce-type-text';
        }
        if ($link) {
            $html .= \sprintf('<a id="dce-readmore-' . $this->get_id() . '" class="dce-btn-readmore%4$s%5$s%6$s" href="%1$s" %2$s%8$s%7$s>%3$s</a>', esc_url($link), $target, $title, $animation_class, $effect_class, $icon_class . $class_typebutton, $data_text_effect, $nofollow);
        } else {
            $html_tag = !empty($settings['html_tag']) ? \DynamicContentForElementor\Helper::validate_html_tag($settings['html_tag']) : 'span';
            $html .= \sprintf('<%1$s id="dce-readmore-' . $this->get_id() . '" class="dce-btn-readmore%2$s%3$s%4$s">%5$s</%s>', $html_tag, $animation_class, $effect_class, $icon_class . $class_typebutton, $title);
        }
        $scriptLetters = '';
        if ($settings['style_effect'] == 'button--nina' || $settings['style_effect'] == 'button--nanuk') {
            $scriptLetters = '<script>jQuery(".button--nina > span, .button--nanuk > span").each(function(){
                        jQuery(this).html(jQuery(this).text().replace(/([^\\x00-\\x80]|\\w)/g, "<span>$&</span>"));
                    });</script>';
        }
        $effScripts = '';
        $spaceI = \intval($settings['space_icon_rm']['size'] ?? 7) + \intval($settings['size_icon_rm']['size'] ?? 0) + \intval($settings['readmore_padding']['left'] ?? 20);
        $effStyle = '<style>.rmbtn-align-justify.icon-left #dce-readmore-' . $this->get_id() . '.icon_button:after, .rmbtn-align-justify.icon-left #dce-readmore-' . $this->get_id() . '.icon_button:before{padding-left: ' . $spaceI . 'px !important; }.rmbtn-align-justify.icon-right #dce-readmore-' . $this->get_id() . '.icon_button:after, .rmbtn-align-justify.icon-left #dce-readmore-' . $this->get_id() . '.icon_button:before{padding-right: ' . $spaceI . 'px !important; }</style>';
        if ($link || \Elementor\Plugin::$instance->editor->is_edit_mode()) {
            echo '<div class="dce-wrapper">';
            echo $html . $scriptLetters . $effStyle;
            echo '</div>';
        }
    }
    public function on_import($element)
    {
        return Icons_Manager::on_import_migration($element, 'icon_rm', 'selected_icon_rm', \true);
    }
}
