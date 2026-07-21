<?php

// SPDX-FileCopyrightText: 2018-2026 Ovation S.r.l. <help@dynamic.ooo>
// SPDX-License-Identifier: GPL-3.0-or-later
namespace DynamicContentForElementor\Widgets;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Background;
use Elementor\Utils;
use DynamicContentForElementor\Helper;
use DynamicContentForElementor\Controls\Group_Control_Filters_CSS;
// Exit if accessed directly
if (!\defined('ABSPATH')) {
    exit;
}
class PodsFields extends \DynamicContentForElementor\Widgets\WidgetPrototype
{
    /**
     * @return array<string>
     */
    public function get_style_depends()
    {
        return ['dce-pods'];
    }
    /**
     * @return void
     */
    protected function safe_register_controls()
    {
        $this->start_controls_section('section_content', ['label' => esc_html__('Pods', 'dynamic-content-for-elementor')]);
        $this->add_control('pods_field_list', ['label' => esc_html__('Fields list', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'options' => $this->get_pods_fields(), 'default' => esc_html__('Select the field...', 'dynamic-content-for-elementor')]);
        $this->add_control('pods_field_type', ['label' => esc_html__('Fields type', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'options' => ['empty' => esc_html__('Empty', 'dynamic-content-for-elementor'), 'text' => esc_html__('Text', 'dynamic-content-for-elementor'), 'url' => esc_html__('URL', 'dynamic-content-for-elementor'), 'phone' => esc_html__('Phone number', 'dynamic-content-for-elementor'), 'email' => esc_html__('Email', 'dynamic-content-for-elementor'), 'paragraph' => esc_html__('Paragraph', 'dynamic-content-for-elementor'), 'wysiwyg' => esc_html__('WYSIWYG editor', 'dynamic-content-for-elementor'), 'code' => esc_html__('Code', 'dynamic-content-for-elementor'), 'datetime' => esc_html__('Datetime', 'dynamic-content-for-elementor'), 'date' => esc_html__('Date', 'dynamic-content-for-elementor'), 'time' => esc_html__('Time', 'dynamic-content-for-elementor'), 'image' => esc_html__('Image', 'dynamic-content-for-elementor')], 'default' => 'text']);
        $this->end_controls_section();
        $this->start_controls_section('section_settings', ['label' => esc_html__('Settings', 'dynamic-content-for-elementor'), 'condition' => ['pods_field_type' => ['email', 'text', 'image', 'phone']]]);
        $this->add_control('pods_text_before', ['label' => esc_html__('Text before', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'default' => '', 'condition' => ['pods_field_type' => ['email', 'text', 'url', 'phone', 'code', 'datetime', 'date', 'time']]]);
        $this->add_control('pods_text_after', ['label' => esc_html__('Text after', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'default' => '', 'condition' => ['pods_field_type' => ['email', 'text', 'url', 'phone', 'code', 'datetime', 'date', 'time']]]);
        $this->add_control('pods_field_email_target', ['label' => esc_html__('Link mailto', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'label_off' => esc_html__('Off', 'dynamic-content-for-elementor'), 'label_on' => esc_html__('On', 'dynamic-content-for-elementor'), 'condition' => ['pods_field_type' => 'email']]);
        $this->add_control('pods_url_enable', ['label' => esc_html__('Link', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => 'yes', 'condition' => ['pods_field_type' => 'url']]);
        $this->add_control('pods_url_custom_text', ['label' => esc_html__('Custom URL text', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'default' => '', 'condition' => ['pods_field_type' => 'url']]);
        $this->add_control('pods_url_target', ['label' => esc_html__('Target type', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'options' => ['_self' => '_self', '_blank' => '_blank', '_parent' => '_parent', '_top' => '_top'], 'default' => '_self', 'condition' => ['pods_field_type' => 'url']]);
        $this->add_control('pods_phone_number_enable', ['label' => esc_html__('Link', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => 'yes', 'condition' => ['pods_field_type' => 'phone']]);
        $this->add_control('pods_phone_number_custom_text', ['label' => esc_html__('Custom phone number', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'condition' => ['pods_field_type' => 'phone']]);
        $this->add_group_control(Group_Control_Image_Size::get_type(), ['name' => 'size', 'label' => esc_html__('Image Size', 'dynamic-content-for-elementor'), 'default' => 'large', 'condition' => ['pods_field_type' => 'image']]);
        $this->add_control('html_tag', ['label' => esc_html__('HTML Tag', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'options' => Helper::get_html_tags(), 'default' => 'div', 'condition' => ['pods_field_type' => ['email', 'text']]]);
        // Link
        $this->add_control('link_to', ['label' => esc_html__('Link to', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'options' => ['none' => esc_html__('None', 'dynamic-content-for-elementor'), 'home' => esc_html__('Home URL', 'dynamic-content-for-elementor'), 'post_url' => esc_html__('Post URL', 'dynamic-content-for-elementor'), 'pods_url' => esc_html__('Pods URL', 'dynamic-content-for-elementor'), 'custom' => esc_html__('Custom URL', 'dynamic-content-for-elementor')], 'condition' => ['pods_field_type!' => 'empty']]);
        $this->add_control('pods_field_url', ['label' => esc_html__('Pods Field Url', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'groups' => Helper::get_pods_fields('website'), 'default' => esc_html__('Select the field...', 'dynamic-content-for-elementor'), 'condition' => ['link_to' => 'pods_url']]);
        $this->add_control('pods_field_url_target', ['label' => esc_html__('Blank', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'condition' => ['link_to' => 'pods_url']]);
        $this->add_control('link', ['label' => esc_html__('Link', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::URL, 'placeholder' => esc_html__('https://your-link.com', 'dynamic-content-for-elementor'), 'default' => ['url' => ''], 'show_label' => \false, 'condition' => ['pods_field_type!' => 'empty', 'link_to' => 'custom']]);
        $this->add_responsive_control('align', ['label' => esc_html__('Alignment', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CHOOSE, 'options' => ['left' => ['title' => esc_html__('Left', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-left'], 'center' => ['title' => esc_html__('Center', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-center'], 'right' => ['title' => esc_html__('Right', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-right'], 'justify' => ['title' => esc_html__('Justified', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-justify']], 'prefix_class' => 'align-dce-', 'selectors' => ['{{WRAPPER}}' => 'text-align: {{VALUE}};'], 'condition' => ['pods_field_type' => ['email', 'text', 'email', 'text', 'url', 'phone', 'paragraph', 'wysiwyg', 'code', 'datetime', 'date', 'time', 'image']]]);
        $this->add_control('use_bg', ['label' => esc_html__('Background', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CHOOSE, 'options' => ['1' => ['title' => esc_html__('Yes', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-check'], '0' => ['title' => esc_html__('No', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-ban']], 'default' => '0', 'condition' => ['pods_field_type' => 'image']]);
        $this->add_control('bg_position', ['label' => esc_html__('Background position', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'default' => 'top center', 'options' => ['' => esc_html__('Default', 'dynamic-content-for-elementor'), 'top left' => esc_html__('Top Left', 'dynamic-content-for-elementor'), 'top center' => esc_html__('Top Center', 'dynamic-content-for-elementor'), 'top right' => esc_html__('Top Right', 'dynamic-content-for-elementor'), 'center left' => esc_html__('Center Left', 'dynamic-content-for-elementor'), 'center center' => esc_html__('Center Center', 'dynamic-content-for-elementor'), 'center right' => esc_html__('Center Right', 'dynamic-content-for-elementor'), 'bottom left' => esc_html__('Bottom Left', 'dynamic-content-for-elementor'), 'bottom center' => esc_html__('Bottom Center', 'dynamic-content-for-elementor'), 'bottom right' => esc_html__('Bottom Right', 'dynamic-content-for-elementor')], 'selectors' => ['{{WRAPPER}} .pods-image-bg' => 'background-position: {{VALUE}};'], 'condition' => ['pods_field_type' => 'image', 'use_bg' => '1']]);
        $this->add_control('bg_extend', ['label' => esc_html__('Extend Background', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => '', 'label_on' => esc_html__('Show', 'dynamic-content-for-elementor'), 'label_off' => esc_html__('Hide', 'dynamic-content-for-elementor'), 'prefix_class' => 'extendbg-', 'condition' => ['pods_field_type' => 'image', 'use_bg' => '1']]);
        $this->add_responsive_control('height', ['label' => esc_html__('Minimum Height', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => 200, 'unit' => 'px'], 'tablet_default' => ['unit' => 'px'], 'mobile_default' => ['unit' => 'px'], 'size_units' => ['px', '%', 'vh'], 'range' => ['%' => ['min' => 1, 'max' => 100], 'px' => ['min' => 1, 'max' => 1000], 'vh' => ['min' => 1, 'max' => 100]], 'selectors' => ['{{WRAPPER}} .dynamic-content-for-elementor-pods-bg' => 'min-height: {{SIZE}}{{UNIT}};'], 'condition' => ['pods_field_type' => 'image', 'use_bg' => '1']]);
        $this->end_controls_section();
        $this->start_controls_section('section_overlay', ['label' => esc_html__('Overlay Image', 'dynamic-content-for-elementor'), 'condition' => ['pods_field_type' => 'image']]);
        $this->add_control('use_overlay', ['label' => esc_html__('Overlay Image', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => '', 'label_on' => esc_html__('Show', 'dynamic-content-for-elementor'), 'label_off' => esc_html__('Hide', 'dynamic-content-for-elementor')]);
        $this->add_group_control(Group_Control_Background::get_type(), ['name' => 'background_overlay', 'types' => ['classic', 'gradient'], 'selector' => '{{WRAPPER}} .dce-overlay', 'condition' => ['use_overlay' => 'yes']]);
        $this->end_controls_section();
        $this->start_controls_section('section_style', ['label' => esc_html__('Style', 'dynamic-content-for-elementor'), 'tab' => Controls_Manager::TAB_STYLE, 'condition' => ['pods_field_type' => ['email', 'text', 'url', 'phone', 'paragraph', 'wysiwyg', 'code', 'datetime', 'date', 'time']]]);
        $this->add_control('tx_heading', ['label' => esc_html__('Text', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HEADING, 'separator' => 'before', 'condition' => ['pods_field_type' => ['email', 'text', 'url', 'phone', 'paragraph', 'wysiwyg', 'code', 'datetime', 'date', 'time']]]);
        $this->add_control('color', ['label' => esc_html__('Text Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .dynamic-content-for-elementor-pods .edc-pods' => 'color: {{VALUE}};'], 'condition' => ['pods_field_type' => ['email', 'text', 'url', 'phone', 'paragraph', 'wysiwyg', 'code', 'datetime', 'date', 'time']]]);
        $this->add_control('bg_color', ['label' => esc_html__('Background Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .dynamic-content-for-elementor-pods' => 'background-color: {{VALUE}};'], 'condition' => ['pods_field_type' => ['email', 'text', 'url', 'phone', 'paragraph', 'wysiwyg', 'code', 'datetime', 'date', 'time']]]);
        $this->add_responsive_control('pods_padding', ['label' => esc_html__('Padding', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => ['px', '%'], 'selectors' => ['{{WRAPPER}} .dynamic-content-for-elementor-pods' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};']]);
        $this->add_responsive_control('pods_space', ['label' => esc_html__('Space', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => 0], 'range' => ['px' => ['max' => 100, 'min' => 0, 'step' => 1]], 'selectors' => ['{{WRAPPER}} .dynamic-content-for-elementor-pods' => 'margin-bottom: {{SIZE}}{{UNIT}};']]);
        $this->add_responsive_control('pods_shift', ['label' => esc_html__('Shift', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'range' => ['px' => ['max' => 180, 'min' => -180, 'step' => 1]], 'selectors' => ['{{WRAPPER}} .dynamic-content-for-elementor-pods' => 'left: {{SIZE}}{{UNIT}};']]);
        $this->add_group_control(Group_Control_Typography::get_type(), ['name' => 'typography_tx', 'label' => esc_html__('Typography', 'dynamic-content-for-elementor'), 'selector' => '{{WRAPPER}} .dynamic-content-for-elementor-pods', 'condition' => ['pods_field_type' => ['email', 'text', 'url', 'phone', 'paragraph', 'wysiwyg', 'code', 'datetime', 'date', 'time']]]);
        $this->add_group_control(Group_Control_Text_Shadow::get_type(), ['name' => 'text_shadow', 'label' => esc_html__('Text shadow', 'dynamic-content-for-elementor'), 'selector' => '{{WRAPPER}} .dynamic-content-for-elementor-pods', 'condition' => ['pods_field_type' => ['email', 'text', 'url', 'phone', 'paragraph', 'wysiwyg', 'code', 'datetime', 'date', 'time']]]);
    }
    protected function get_pods_fields()
    {
        $option_list = array();
        $option_list = ['Select the field...'];
        if (Helper::is_plugin_active('pods')) {
            $pods = \pods_api()->load_pods();
            $post_type = get_post_type();
            foreach ($pods as $pod) {
                foreach ($pod['fields'] as $f) {
                    $option_list[$f['name']] = $f['label'] . '&nbsp;[' . $f['type'] . ']';
                }
            }
        }
        return $option_list;
    }
    /**
     * @return void
     */
    protected function safe_render()
    {
        $settings = $this->get_settings_for_display();
        if (empty($settings)) {
            return;
        }
        $id_page = Helper::get_the_id();
        $idFields = $settings['pods_field_list'];
        if (!$idFields) {
            return;
        }
        $use_bg = $settings['use_bg'];
        // Overlay attributes (optional + hover)
        $overlay_block = '';
        if ($settings['use_overlay'] == 'yes') {
            $this->add_render_attribute('overlay', 'class', 'dce-overlay');
            $overlay_block = '<div ' . $this->get_render_attribute_string('overlay') . '></div>';
        }
        $this->add_render_attribute('overlay_hover', 'class', 'dce-overlay_hover');
        $overlay_hover_block = '<div ' . $this->get_render_attribute_string('overlay_hover') . '></div>';
        $pod_value = wp_kses_post(\pods_field_display($idFields));
        $pod_type = $settings['pods_field_type'];
        switch ($settings['link_to']) {
            case 'custom':
                if (!empty($settings['link']['url'])) {
                    $link = esc_url($settings['link']['url']);
                    $target = !empty($settings['link']['is_external']) ? 'target="_blank"' : '';
                } else {
                    $link = \false;
                }
                break;
            case 'pods_url':
                if (!empty($settings['pods_field_url'])) {
                    $link = get_post_meta($id_page, $settings['pods_field_url'], \true);
                    $target = !empty($settings['pods_field_url_target']) ? 'target="_blank"' : '';
                } else {
                    $link = \false;
                }
                break;
            case 'post_url':
                $link = esc_url(get_permalink($id_page));
                $target = '';
                break;
            case 'home':
                $link = esc_url(get_home_url());
                $target = '';
                break;
            case 'none':
            default:
                $link = \false;
                $target = '';
                break;
        }
        switch ($pod_type) {
            case 'code':
                $settings['html_tag'] = 'code';
                break;
            case 'image':
                $settings['html_tag'] = 'div';
                break;
        }
        // Wrapper attributes
        $this->add_render_attribute('wrapper', 'class', 'dynamic-content-for-elementor-pods');
        if (!empty($settings['hover_animation'])) {
            $this->add_render_attribute('wrapper', 'class', $settings['hover_animation']);
        }
        $tag = \DynamicContentForElementor\Helper::validate_html_tag($settings['html_tag']);
        if ($link) {
            $this->add_render_attribute('link', 'href', $link);
            if ('target="_blank"' === $target) {
                $this->add_render_attribute('link', 'target', '_blank');
            }
            echo '<a ' . $this->get_render_attribute_string('link') . '>';
        }
        echo '<' . $tag . ' ' . $this->get_render_attribute_string('wrapper') . '>';
        $inner_content = $pod_value;
        if ($pod_type === 'email') {
            if ($settings['pods_field_email_target']) {
                $this->add_render_attribute('email_link', 'href', 'mailto:' . $pod_value);
                $inner_content = '<a ' . $this->get_render_attribute_string('email_link') . '>' . $pod_value . '</a>';
            }
        } elseif ($pod_type === 'url') {
            $text_url = $pod_value;
            if (!empty($settings['pods_url_custom_text'])) {
                $text_url = wp_kses_post($settings['pods_url_custom_text']);
            }
            if ($settings['pods_url_enable']) {
                $this->add_render_attribute('url_link', 'href', $pod_value);
                $this->add_render_attribute('url_link', 'target', $settings['pods_url_target']);
                $inner_content = '<a ' . $this->get_render_attribute_string('url_link') . '>' . $text_url . '</a>';
            }
        } elseif ($pod_type === 'phone') {
            $text_number = $pod_value;
            if (!empty($settings['pods_phone_number_custom_text'])) {
                $text_number = wp_kses_post($settings['pods_phone_number_custom_text']);
            }
            if ($settings['pods_phone_number_enable']) {
                $phone_href = 'tel:' . \preg_replace('/[^0-9]/', '', $pod_value);
                $this->add_render_attribute('phone_link', 'href', $phone_href);
                $inner_content = '<a ' . $this->get_render_attribute_string('phone_link') . '>' . $text_number . '</a>';
            }
        }
        // Image
        if ($pod_type === 'image') {
            $this->add_render_attribute('mask', 'class', 'mask');
            $this->add_render_attribute('wrap_filters', 'class', 'wrap-filters');
            if (!$use_bg) {
                $this->add_render_attribute('image', 'src', $pod_value);
                echo '<div ' . $this->get_render_attribute_string('image_container') . '>';
                echo '<div ' . $this->get_render_attribute_string('mask') . '><div ' . $this->get_render_attribute_string('wrap_filters') . '>';
                echo '<img ' . $this->get_render_attribute_string('image') . ' />';
                echo '</div></div>' . $overlay_block . $overlay_hover_block . '</div>';
            } else {
                $this->add_render_attribute('bg_figure', 'class', 'pods-image-bg');
                $this->add_render_attribute('bg_figure', 'style', 'background-image: url(' . esc_url($pod_value) . '); background-repeat: no-repeat; background-size: cover;');
                echo '<div ' . $this->get_render_attribute_string('image_container') . '>';
                echo '<div ' . $this->get_render_attribute_string('mask') . '><div ' . $this->get_render_attribute_string('wrap_filters') . '>';
                echo '<figure ' . $this->get_render_attribute_string('bg_figure') . '></figure>';
                echo '</div></div>' . $overlay_block . $overlay_hover_block . '</div>';
            }
        } else {
            // Wrap value and before/after spans
            $this->add_render_attribute('value_span', 'class', 'edc-pods');
            echo '<span ' . $this->get_render_attribute_string('value_span') . '>' . $inner_content . '</span>';
            if ($settings['pods_text_before'] != '' || $settings['pods_text_after'] != '') {
                $this->add_render_attribute('tx_before', 'class', 'tx-before');
                $this->add_render_attribute('tx_after', 'class', 'tx-after');
                echo '<span ' . $this->get_render_attribute_string('tx_before') . '>' . wp_kses_post($settings['pods_text_before']) . '</span>';
                echo '<span ' . $this->get_render_attribute_string('tx_after') . '>' . wp_kses_post($settings['pods_text_after']) . '</span>';
            }
        }
        echo '</' . $tag . '>';
        if ($link) {
            echo '</a>';
        }
    }
}
