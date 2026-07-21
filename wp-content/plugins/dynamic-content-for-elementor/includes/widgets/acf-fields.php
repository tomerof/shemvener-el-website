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
use DynamicContentForElementor\Controls\Group_Control_Transform_Element;
// Exit if accessed directly
if (!\defined('ABSPATH')) {
    exit;
}
class AcfFields extends \DynamicContentForElementor\Widgets\WidgetPrototype
{
    public function run_once()
    {
        parent::run_once();
        $save_guard = \DynamicContentForElementor\Plugin::instance()->save_guard;
        $save_guard->register_unsafe_control($this->get_name(), 'other_post_source');
    }
    public function get_script_depends()
    {
        return ['elementor-dialog', 'dce-acf'];
    }
    public function get_style_depends()
    {
        return ['dce-acf'];
    }
    /**
     * Register controls after check if this feature is only for admin
     *
     * @return void
     */
    protected function safe_register_controls()
    {
        $this->start_controls_section('section_content', ['label' => $this->get_title()]);
        $this->add_control('acf_field_list', ['label' => esc_html__('Select ACF Field', 'dynamic-content-for-elementor'), 'type' => 'ooo_query', 'placeholder' => esc_html__('Select the field...', 'dynamic-content-for-elementor'), 'label_block' => \true, 'query_type' => 'acf', 'dynamic' => ['active' => \false], 'frontend_available' => \true]);
        $this->add_control('acf_type', ['label' => esc_html__('ACF Type', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'options' => ['empty' => esc_html__('Empty', 'dynamic-content-for-elementor'), 'text' => esc_html__('Text', 'dynamic-content-for-elementor'), 'display' => esc_html__('ACF Display', 'dynamic-content-for-elementor'), 'wysiwyg' => esc_html__('WYSIWYG Editor', 'dynamic-content-for-elementor'), 'textarea' => esc_html__('Text Area', 'dynamic-content-for-elementor'), 'date' => esc_html__('Date', 'dynamic-content-for-elementor'), 'number' => esc_html__('Number', 'dynamic-content-for-elementor'), 'email' => esc_html__('Email', 'dynamic-content-for-elementor'), 'url' => esc_html__('Url', 'dynamic-content-for-elementor'), 'select' => esc_html__('Select', 'dynamic-content-for-elementor'), 'list' => esc_html__('List (Radio or Checkbox)', 'dynamic-content-for-elementor'), 'image' => esc_html__('Image', 'dynamic-content-for-elementor'), 'video' => esc_html__('Video oembed', 'dynamic-content-for-elementor')], 'default' => 'text', 'frontend_available' => \true]);
        $this->add_control('acf_dynamic', ['label' => esc_html__('Apply WordPress Shortcodes', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'condition' => ['acf_type' => ['text', 'textarea', 'wysiwyg']]]);
        $this->add_control('acf_currency_mode', ['label' => esc_html__('Currency Mode', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'frontend_available' => \true, 'condition' => ['acf_type' => 'number']]);
        $this->add_control('acf_currency_type', ['label' => esc_html__('Currency type', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'options' => Helper::number_format_currency(), 'default' => 'en-US', 'frontend_available' => \true, 'condition' => ['acf_currency_mode!' => '', 'acf_type' => 'number']]);
        $this->add_control('acf_settoDecimal', ['label' => esc_html__('Decimal Place', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'frontend_available' => \true, 'condition' => ['acf_type' => 'number']]);
        $this->add_control('acf_integerDecimalOpt', ['label' => esc_html__('Set upto decimal integer', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::NUMBER, 'min' => 1, 'max' => 15, 'default' => 2, 'frontend_available' => \true, 'condition' => ['acf_settoDecimal!' => '', 'acf_type' => 'number']]);
        $this->add_control('acf_text_before', ['label' => esc_html__('Text before', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'default' => '']);
        $this->add_responsive_control('acf_text_before_block', ['label' => esc_html__('Before - Inline or Block', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'label_off' => esc_html__('Inline', 'dynamic-content-for-elementor'), 'label_on' => esc_html__('Block', 'dynamic-content-for-elementor'), 'return_value' => 'block', 'selectors' => ['{{WRAPPER}} .dynamic-content-for-elementor-acf span.tx-before' => 'display: {{VALUE}};'], 'condition' => ['acf_text_before!' => '']]);
        $this->add_control('acf_text_after', ['label' => esc_html__('Text after', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'default' => '']);
        $this->add_responsive_control('acf_text_after_block', ['label' => esc_html__('After - List or Block', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'label_off' => esc_html__('List', 'dynamic-content-for-elementor'), 'label_on' => esc_html__('Block', 'dynamic-content-for-elementor'), 'return_value' => 'block', 'selectors' => ['{{WRAPPER}} .dynamic-content-for-elementor-acf span.tx-after' => 'display: {{VALUE}};'], 'condition' => ['acf_text_after!' => '']]);
        $this->add_control('drop_cap', ['label' => esc_html__('Drop Cap', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'label_off' => esc_html__('Off', 'dynamic-content-for-elementor'), 'label_on' => esc_html__('On', 'dynamic-content-for-elementor'), 'separator' => 'before', 'render_type' => 'template', 'prefix_class' => 'elementor-drop-cap-', 'frontend_available' => \true, 'condition' => ['acf_type' => ['text', 'textarea']]]);
        $this->end_controls_section();
        $this->start_controls_section('section_settings', ['label' => esc_html__('Settings', 'dynamic-content-for-elementor'), 'condition' => ['acf_type' => ['text', 'wysiwyg', 'textarea', 'date', 'image', 'empty', 'select', 'list']]]);
        $this->add_control('list_array_return', ['label' => esc_html__('List Return Value', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'options' => ['' => esc_html__('Label: Value', 'dynamic-content-for-elementor'), 'label' => esc_html__('Label', 'dynamic-content-for-elementor'), 'value' => esc_html__('Value', 'dynamic-content-for-elementor')], 'default' => '', 'condition' => ['acf_type' => 'list']]);
        $this->add_control('list_style', ['label' => esc_html__('List Style', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'options' => ['inline-block' => esc_html__('Horizontal', 'dynamic-content-for-elementor'), 'block' => esc_html__('Vertical', 'dynamic-content-for-elementor')], 'default' => 'inline-block', 'separator' => 'after', 'render_type' => 'template', 'condition' => ['acf_type' => 'list'], 'selectors' => ['{{WRAPPER}} .dce-acf-list' => 'display: {{VALUE}};']]);
        $this->add_control('list_separator', ['label' => esc_html__('Text before', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'default' => ' / ', 'condition' => ['list_style' => 'inline-block']]);
        $this->add_control('html_tag', ['label' => esc_html__('HTML Tag', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'options' => Helper::get_html_tags(['code']), 'default' => 'div', 'condition' => ['acf_type!' => 'empty']]);
        $this->add_control('link_to', ['label' => esc_html__('Link to', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'default' => 'none', 'options' => ['none' => esc_html__('None', 'dynamic-content-for-elementor'), 'home' => esc_html__('Home URL', 'dynamic-content-for-elementor'), 'post_url' => esc_html__('Post URL', 'dynamic-content-for-elementor'), 'acf_url' => esc_html__('ACF URL', 'dynamic-content-for-elementor'), 'custom' => esc_html__('Custom URL', 'dynamic-content-for-elementor')]]);
        $this->add_control('acf_field_url', ['label' => esc_html__('ACF Field URL', 'dynamic-content-for-elementor'), 'type' => 'ooo_query', 'query_type' => 'acf', 'label_block' => \true, 'dynamic' => ['active' => \false], 'object_type' => ['file', 'url'], 'default' => esc_html__('Select the field...', 'dynamic-content-for-elementor'), 'condition' => ['link_to' => 'acf_url']]);
        $this->add_control('acf_field_url_target', ['label' => esc_html__('Blank', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'condition' => ['link_to' => 'acf_url']]);
        $this->add_control('acf_field_url_nofollow', ['label' => esc_html__('NoFollow', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'condition' => ['link_to' => 'acf_url']]);
        $this->add_control('link', ['label' => esc_html__('Link', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::URL, 'placeholder' => esc_html__('https://your-link.com', 'dynamic-content-for-elementor'), 'default' => ['url' => ''], 'show_label' => \false, 'condition' => ['link_to' => 'custom']]);
        $this->add_group_control(Group_Control_Image_Size::get_type(), ['name' => 'size', 'label' => esc_html__('Image Size', 'dynamic-content-for-elementor'), 'default' => 'large', 'condition' => ['acf_type' => 'image']]);
        $this->add_control('use_bg', ['label' => esc_html__('Background', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CHOOSE, 'options' => ['1' => ['title' => esc_html__('Yes', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-check'], '0' => ['title' => esc_html__('No', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-ban']], 'render_type' => 'template', 'default' => '0', 'separator' => 'before', 'prefix_class' => 'use-bg', 'condition' => ['acf_type' => 'image']]);
        $this->add_control('bg_position', ['label' => esc_html__('Background position', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'default' => 'top center', 'options' => ['' => esc_html__('Default', 'dynamic-content-for-elementor'), 'top left' => esc_html__('Top Left', 'dynamic-content-for-elementor'), 'top center' => esc_html__('Top Center', 'dynamic-content-for-elementor'), 'top right' => esc_html__('Top Right', 'dynamic-content-for-elementor'), 'center left' => esc_html__('Center Left', 'dynamic-content-for-elementor'), 'center center' => esc_html__('Center Center', 'dynamic-content-for-elementor'), 'center right' => esc_html__('Center Right', 'dynamic-content-for-elementor'), 'bottom left' => esc_html__('Bottom Left', 'dynamic-content-for-elementor'), 'bottom center' => esc_html__('Bottom Center', 'dynamic-content-for-elementor'), 'bottom right' => esc_html__('Bottom Right', 'dynamic-content-for-elementor')], 'selectors' => ['{{WRAPPER}} .dynamic-content-for-elementor-acfimage-bg' => 'background-position: {{VALUE}};'], 'condition' => ['acf_type' => ['image'], 'use_bg' => '1']]);
        $this->add_responsive_control('height', ['label' => esc_html__('Background Minimum Height', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => 200, 'unit' => 'px'], 'tablet_default' => ['unit' => 'px'], 'mobile_default' => ['unit' => 'px'], 'size_units' => ['px', '%', 'vh'], 'range' => ['%' => ['min' => 1, 'max' => 100], 'px' => ['min' => 1, 'max' => 1000], 'vh' => ['min' => 1, 'max' => 100]], 'selectors' => ['{{WRAPPER}} .dynamic-content-for-elementor-acfimage-bg' => 'min-height: {{SIZE}}{{UNIT}};'], 'condition' => ['acf_type' => ['image'], 'use_bg' => '1']]);
        $this->add_responsive_control('bg_width', ['label' => esc_html__('Background Width', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => '', 'unit' => 'px'], 'tablet_default' => ['unit' => 'px'], 'mobile_default' => ['unit' => 'px'], 'size_units' => ['px', '%', 'vw'], 'range' => ['%' => ['min' => 1, 'max' => 100], 'px' => ['min' => 1, 'max' => 1000], 'vh' => ['min' => 1, 'max' => 100]], 'selectors' => ['{{WRAPPER}} .dynamic-content-for-elementor-acfimage-bg' => 'width: {{SIZE}}{{UNIT}};'], 'condition' => ['acf_type' => ['image'], 'use_bg' => '1']]);
        $this->add_control('bg_extend', ['label' => esc_html__('Extend Background', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => '', 'label_on' => esc_html__('Show', 'dynamic-content-for-elementor'), 'label_off' => esc_html__('Hide', 'dynamic-content-for-elementor'), 'return_value' => 'yes', 'prefix_class' => 'extendbg-', 'condition' => ['acf_type' => ['image'], 'use_bg' => '1']]);
        $this->end_controls_section();
        $this->start_controls_section('section_overlay', ['label' => esc_html__('Overlay Image', 'dynamic-content-for-elementor'), 'tab' => Controls_Manager::TAB_CONTENT, 'condition' => ['acf_type' => ['image']]]);
        $this->add_control('use_overlay', ['label' => esc_html__('Overlay Image', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => '', 'label_on' => esc_html__('Show', 'dynamic-content-for-elementor'), 'label_off' => esc_html__('Hide', 'dynamic-content-for-elementor'), 'return_value' => 'yes']);
        $this->add_group_control(Group_Control_Background::get_type(), ['name' => 'background_overlay', 'types' => ['classic', 'gradient'], 'selector' => '{{WRAPPER}} .dce-overlay', 'condition' => ['use_overlay' => 'yes']]);
        $this->end_controls_section();
        $this->start_controls_section('section_datetime', ['label' => esc_html__('Date Time', 'dynamic-content-for-elementor'), 'tab' => Controls_Manager::TAB_CONTENT, 'condition' => ['acf_type' => 'date']]);
        $this->add_control('date_format', ['label' => esc_html__('Date Format', 'dynamic-content-for-elementor'), 'description' => '<a target="_blank" href="https://www.php.net/manual/en/function.date.php">' . esc_html__('Use standard PHP format character', 'dynamic-content-for-elementor') . '</a>', 'type' => Controls_Manager::TEXT, 'default' => 'F j, Y, g:i a', 'label_block' => \true]);
        $this->end_controls_section();
        $this->start_controls_section('section_filters', ['label' => esc_html__('Filters Image', 'dynamic-content-for-elementor'), 'tab' => Controls_Manager::TAB_CONTENT, 'condition' => ['acf_type' => ['image']]]);
        $this->add_group_control(Group_Control_Filters_CSS::get_type(), ['name' => 'filters_image', 'label' => 'Filters image', 'selector' => '{{WRAPPER}} .wrap-filters']);
        $this->add_control('blend_mode', ['label' => esc_html__('Blend Mode', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'options' => ['' => esc_html__('Normal', 'dynamic-content-for-elementor'), 'multiply' => esc_html__('Multiply', 'dynamic-content-for-elementor'), 'screen' => esc_html__('Screen', 'dynamic-content-for-elementor'), 'overlay' => esc_html__('Overlay', 'dynamic-content-for-elementor'), 'darken' => esc_html__('Darken', 'dynamic-content-for-elementor'), 'lighten' => esc_html__('Lighten', 'dynamic-content-for-elementor'), 'color-dodge' => esc_html__('Color Dodge', 'dynamic-content-for-elementor'), 'saturation' => esc_html__('Saturation', 'dynamic-content-for-elementor'), 'color' => esc_html__('Color', 'dynamic-content-for-elementor'), 'difference' => esc_html__('Difference', 'dynamic-content-for-elementor'), 'exclusion' => esc_html__('Exclusion', 'dynamic-content-for-elementor'), 'hue' => esc_html__('Hue', 'dynamic-content-for-elementor'), 'luminosity' => esc_html__('Luminosity', 'dynamic-content-for-elementor')], 'selectors' => ['{{WRAPPER}} .acf-image' => 'mix-blend-mode: {{VALUE}}'], 'separator' => 'none']);
        $this->end_controls_section();
        $this->start_controls_section('section_source', ['label' => esc_html__('Source', 'dynamic-content-for-elementor'), 'tab' => Controls_Manager::TAB_CONTENT]);
        $this->add_control('data_source', ['label' => esc_html__('Source', 'dynamic-content-for-elementor'), 'description' => esc_html__('Select the data source', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => 'yes', 'label_on' => esc_html__('Same', 'dynamic-content-for-elementor'), 'label_off' => esc_html__('other', 'dynamic-content-for-elementor'), 'return_value' => 'yes']);
        $this->add_control('other_post_source', ['label' => esc_html__('Select from other source post', 'dynamic-content-for-elementor'), 'type' => 'ooo_query', 'placeholder' => esc_html__('Post Title', 'dynamic-content-for-elementor'), 'label_block' => \true, 'query_type' => 'posts', 'condition' => ['data_source' => '']]);
        $this->end_controls_section();
        $this->start_controls_section('section_fallback', ['label' => esc_html__('Fallback', 'dynamic-content-for-elementor')]);
        $this->add_control('fallback', ['label' => esc_html__('Fallback Content', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'description' => esc_html__('If you want to show something when the field is empty', 'dynamic-content-for-elementor')]);
        $this->add_control('fallback_text', ['label' => esc_html__('Text Fallback', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::WYSIWYG, 'default' => esc_html__('Empty field', 'dynamic-content-for-elementor'), 'condition' => ['fallback!' => '']]);
        $this->end_controls_section();
        $this->start_controls_section('section_style', ['label' => esc_html__('Style', 'dynamic-content-for-elementor'), 'tab' => Controls_Manager::TAB_STYLE]);
        $this->add_responsive_control('align', ['label' => esc_html__('Alignment', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CHOOSE, 'options' => ['left' => ['title' => esc_html__('Left', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-left'], 'center' => ['title' => esc_html__('Center', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-center'], 'right' => ['title' => esc_html__('Right', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-right'], 'justify' => ['title' => esc_html__('Justified', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-justify']], 'default' => '', 'prefix_class' => 'align-dce-', 'selectors' => ['{{WRAPPER}}' => 'text-align: {{VALUE}};']]);
        $this->add_control('hyphens', ['label' => esc_html__('Hyphens', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'prefix_class' => 'hyphens-', 'condition' => ['align' => 'justify']]);
        $this->end_controls_section();
        $this->start_controls_section('section_style_text', ['label' => esc_html__('Text', 'dynamic-content-for-elementor'), 'tab' => Controls_Manager::TAB_STYLE, 'condition' => ['acf_type' => ['text', 'date', 'textarea', 'select', 'list', 'wysiwyg', 'number', 'empty']]]);
        $this->add_control('color', ['label' => esc_html__('Text Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .dynamic-content-for-elementor-acf' => 'color: {{VALUE}};'], 'condition' => ['acf_type' => ['text', 'date', 'textarea', 'select', 'list', 'wysiwyg', 'number', 'empty']]]);
        $this->add_control('bg_color', ['label' => esc_html__('Background Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .dynamic-content-for-elementor-acf' => 'background-color: {{VALUE}};'], 'condition' => ['acf_type' => ['text', 'date', 'textarea', 'select', 'list', 'wysiwyg', 'number', 'empty']]]);
        $this->add_responsive_control('acf_padding', ['label' => esc_html__('Padding', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => ['px', '%'], 'selectors' => ['{{WRAPPER}} .dynamic-content-for-elementor-acf' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};']]);
        $this->add_responsive_control('acf_space', ['label' => esc_html__('Space', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => 0], 'range' => ['px' => ['max' => 100, 'min' => 0, 'step' => 1]], 'selectors' => ['{{WRAPPER}} .dynamic-content-for-elementor-acf' => 'margin-bottom: {{SIZE}}{{UNIT}};']]);
        $this->add_responsive_control('acf_shift', ['label' => esc_html__('Shift', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'range' => ['px' => ['max' => 180, 'min' => -180, 'step' => 1]], 'selectors' => ['{{WRAPPER}} .dynamic-content-for-elementor-acf' => 'left: {{SIZE}}{{UNIT}};']]);
        $this->add_group_control(Group_Control_Typography::get_type(), ['name' => 'typography_tx', 'label' => esc_html__('Typography', 'dynamic-content-for-elementor'), 'selector' => '{{WRAPPER}} .dynamic-content-for-elementor-acf', 'condition' => ['acf_type' => ['text', 'date', 'textarea', 'select', 'list', 'wysiwyg', 'number', 'empty']]]);
        $this->add_group_control(Group_Control_Text_Shadow::get_type(), ['name' => 'text_shadow', 'selector' => '{{WRAPPER}} .dynamic-content-for-elementor-acf']);
        $this->add_control('List_heading', ['label' => esc_html__('List', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HEADING, 'separator' => 'before', 'condition' => ['acf_type' => ['list']]]);
        $this->add_group_control(Group_Control_Typography::get_type(), ['name' => 'typography_list_label', 'label' => esc_html__('Label Typography', 'dynamic-content-for-elementor'), 'selector' => '{{WRAPPER}} .dce-acf-list-label', 'condition' => ['acf_type' => ['list'], 'list_array_return' => 'label']]);
        $this->add_responsive_control('list_space', ['label' => esc_html__('Space', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => ''], 'range' => ['px' => ['max' => 100, 'min' => 0, 'step' => 1]], 'selectors' => ['{{WRAPPER}} .dce-acf-list-inline-block .dce-acf-list:not(:first-child)' => 'margin-left: {{SIZE}}{{UNIT}};', '{{WRAPPER}} .dce-acf-list-inline-block .dce-acf-list:not(:last-child)' => 'margin-right: {{SIZE}}{{UNIT}};', '{{WRAPPER}} .dce-acf-list-block .dce-acf-list:not(:forst-child)' => 'margin-top: {{SIZE}}{{UNIT}};', '{{WRAPPER}} .dce-acf-list-block .dce-acf-list:not(:last-child)' => 'margin-bottom: {{SIZE}}{{UNIT}};'], 'condition' => ['acf_type' => ['list']]]);
        $this->add_responsive_control('list_width', ['label' => esc_html__('Label width', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => ''], 'range' => ['px' => ['max' => 100, 'min' => 0, 'step' => 1]], 'selectors' => ['{{WRAPPER}} .dce-acf-list-label' => 'width: {{SIZE}}{{UNIT}};'], 'condition' => ['acf_type' => ['list'], 'list_array_return' => 'label']]);
        $this->add_control('txbefore_heading', ['label' => esc_html__('Text before', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HEADING, 'separator' => 'before', 'condition' => ['acf_text_before!' => '']]);
        $this->add_control('tx_before_color', ['label' => esc_html__('Text Before Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .dynamic-content-for-elementor-acf span.tx-before' => 'color: {{VALUE}};', '{{WRAPPER}} .dynamic-content-for-elementor-acf a span.tx-before' => 'color: {{VALUE}};'], 'condition' => ['acf_text_before!' => '']]);
        $this->add_group_control(Group_Control_Typography::get_type(), ['name' => 'typography_tx_before', 'label' => esc_html__('Font Before', 'dynamic-content-for-elementor'), 'selector' => '{{WRAPPER}} .dynamic-content-for-elementor-acf span.tx-before', 'condition' => ['acf_text_before!' => '']]);
        $this->add_control('txafter_heading', ['label' => esc_html__('Text after', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HEADING, 'separator' => 'before', 'condition' => ['acf_text_after!' => '']]);
        $this->add_control('tx_after_color', ['label' => esc_html__('Text After Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .dynamic-content-for-elementor-acf span.tx-after' => 'color: {{VALUE}};', '{{WRAPPER}} .dynamic-content-for-elementor-acf a span.tx-after' => 'color: {{VALUE}};'], 'condition' => ['acf_text_after!' => '']]);
        $this->add_group_control(Group_Control_Typography::get_type(), ['name' => 'typography_tx_after', 'label' => esc_html__('Font After', 'dynamic-content-for-elementor'), 'selector' => '{{WRAPPER}} .dynamic-content-for-elementor-acf span.tx-after', 'condition' => ['acf_text_after!' => '']]);
        $this->end_controls_section();
        $this->start_controls_section('section_style_image', ['label' => esc_html__('Image', 'dynamic-content-for-elementor'), 'tab' => Controls_Manager::TAB_STYLE, 'condition' => ['acf_type' => ['image']]]);
        $this->add_responsive_control('space', ['label' => esc_html__('Size (%)', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['unit' => '%'], 'size_units' => ['%', 'px', 'vw'], 'range' => ['%' => ['min' => 1, 'max' => 100], 'px' => ['min' => 1, 'max' => 800], 'vw' => ['min' => 1, 'max' => 100]], 'selectors' => ['{{WRAPPER}} .acf-image' => 'max-width: {{SIZE}}{{UNIT}};'], 'condition' => ['acf_type' => ['image'], 'bg_extend' => '']]);
        $this->add_control('force_width', ['label' => esc_html__('Force Width', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'prefix_class' => 'forcewidth-', 'condition' => ['acf_type' => ['image'], 'bg_extend' => '']]);
        $this->add_group_control(Group_Control_Border::get_type(), ['name' => 'image_border', 'label' => esc_html__('Image Border', 'dynamic-content-for-elementor'), 'selector' => '{{WRAPPER}} .acf-image', 'condition' => ['acf_type' => ['image']]]);
        $this->add_control('image_border_radius', ['label' => esc_html__('Border Radius', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => ['px', '%'], 'selectors' => ['{{WRAPPER}} .acf-image, {{WRAPPER}} .acf-image img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'], 'condition' => ['acf_type' => ['image']]]);
        $this->add_group_control(Group_Control_Box_Shadow::get_type(), ['name' => 'image_box_shadow', 'selector' => '{{WRAPPER}} .acf-image', 'condition' => ['acf_type' => ['image']]]);
        $this->end_controls_section();
        $this->start_controls_section('section_hover_style', ['label' => 'Rollover', 'tab' => Controls_Manager::TAB_STYLE, 'condition' => ['link_to!' => 'none']]);
        $this->add_control('acf_color_hover', ['label' => esc_html__('Text Color Hover', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .dynamic-content-for-elementor-acf a:hover' => 'color: {{VALUE}};'], 'condition' => ['link_to!' => 'none']]);
        $this->add_control('tx_before_color_hover', ['label' => esc_html__('Text Before Color Hover', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .dynamic-content-for-elementor-acf span.tx-before' => 'color: {{VALUE}};', '{{WRAPPER}} .dynamic-content-for-elementor-acf a:hover span.tx-before' => 'color: {{VALUE}};'], 'condition' => ['acf_text_before!' => '']]);
        $this->add_control('tx_after_color_hover', ['label' => esc_html__('Text After Color Hover', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .dynamic-content-for-elementor-acf span.tx-after' => 'color: {{VALUE}};', '{{WRAPPER}} .dynamic-content-for-elementor-acf a:hover span.tx-after' => 'color: {{VALUE}};'], 'condition' => ['acf_text_after!' => '']]);
        $this->add_control('acf_bgcolor_hover', ['label' => esc_html__('Background Color Hover', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .dynamic-content-for-elementor-acf:hover' => 'background-color: {{VALUE}};'], 'condition' => ['link_to!' => 'none']]);
        $this->add_control('acf_bgcolor_overlay_hover', ['label' => esc_html__('Background Overlay Hover', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HEADING, 'separator' => 'before']);
        $this->add_group_control(Group_Control_Background::get_type(), ['name' => 'overlay_hover_color', 'label' => esc_html__('Background', 'dynamic-content-for-elementor'), 'types' => ['classic', 'gradient'], 'selector' => '{{WRAPPER}} .dce-overlay_hover', 'separator' => 'after', 'condition' => ['link_to!' => 'none', 'acf_type' => ['image']]]);
        $this->add_control('hover_animation', ['label' => esc_html__('Animation', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HOVER_ANIMATION, 'condition' => ['link_to!' => 'none', 'acf_type' => ['image']]]);
        $this->add_group_control(Group_Control_Filters_CSS::get_type(), ['name' => 'filters_image_hover', 'label' => esc_html__('Filters', 'dynamic-content-for-elementor'), 'selector' => '{{WRAPPER}} a:hover .wrap-filters', 'condition' => ['link_to!' => 'none', 'acf_type' => ['image']]]);
        $this->add_control('hover_effects', ['label' => esc_html__('Effects', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'options' => ['' => esc_html__('None', 'dynamic-content-for-elementor'), 'zoom' => esc_html__('Zoom', 'dynamic-content-for-elementor'), 'slow-zoom' => esc_html__('Slow Zoom', 'dynamic-content-for-elementor')], 'separator' => 'before', 'prefix_class' => 'hovereffect-', 'condition' => ['link_to!' => 'none']]);
        $this->end_controls_section();
        $this->start_controls_section('section_settings_media', ['label' => esc_html__('Media Settings', 'dynamic-content-for-elementor'), 'condition' => ['acf_type' => ['video', 'audio']]]);
        $this->add_control('video_type', ['label' => esc_html__('Video Type', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'default' => 'youtube', 'options' => ['youtube' => esc_html__('YouTube', 'dynamic-content-for-elementor'), 'vimeo' => esc_html__('Vimeo', 'dynamic-content-for-elementor')]]);
        $this->add_control('hosted_link', ['label' => esc_html__('Link', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'placeholder' => esc_html__('Enter your video link', 'dynamic-content-for-elementor'), 'default' => '', 'label_block' => \true, 'condition' => ['video_type' => 'hosted']]);
        $this->add_control('heading_youtube', ['label' => esc_html__('Video Options', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HEADING, 'separator' => 'before']);
        $this->add_control('yt_autoplay', ['label' => esc_html__('Autoplay', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'condition' => ['video_type' => 'youtube']]);
        $this->add_control('yt_loop', ['label' => esc_html__('Loop', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'condition' => ['video_type' => 'youtube']]);
        $this->add_control('yt_mute', ['label' => esc_html__('Mute', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'condition' => ['video_type' => 'youtube']]);
        $this->add_control('yt_rel', ['label' => esc_html__('Suggested Videos', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'label_off' => esc_html__('Hide', 'dynamic-content-for-elementor'), 'label_on' => esc_html__('Show', 'dynamic-content-for-elementor'), 'condition' => ['video_type' => 'youtube']]);
        $this->add_control('yt_controls', ['label' => esc_html__('Player Control', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'label_off' => esc_html__('Hide', 'dynamic-content-for-elementor'), 'label_on' => esc_html__('Show', 'dynamic-content-for-elementor'), 'default' => 'yes', 'condition' => ['video_type' => 'youtube']]);
        $this->add_control('yt_showinfo', ['label' => esc_html__('Player Title & Actions', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'label_off' => esc_html__('Hide', 'dynamic-content-for-elementor'), 'label_on' => esc_html__('Show', 'dynamic-content-for-elementor'), 'default' => 'yes', 'condition' => ['video_type' => 'youtube']]);
        $this->add_control('vimeo_autoplay', ['label' => esc_html__('Autoplay', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'condition' => ['video_type' => 'vimeo']]);
        $this->add_control('vimeo_loop', ['label' => esc_html__('Loop', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'condition' => ['video_type' => 'vimeo']]);
        $this->add_control('vimeo_title', ['label' => esc_html__('Intro Title', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'label_off' => esc_html__('Hide', 'dynamic-content-for-elementor'), 'label_on' => esc_html__('Show', 'dynamic-content-for-elementor'), 'default' => 'yes', 'condition' => ['video_type' => 'vimeo']]);
        $this->add_control('vimeo_portrait', ['label' => esc_html__('Intro Portrait', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'label_off' => esc_html__('Hide', 'dynamic-content-for-elementor'), 'label_on' => esc_html__('Show', 'dynamic-content-for-elementor'), 'default' => 'yes', 'condition' => ['video_type' => 'vimeo']]);
        $this->add_control('vimeo_byline', ['label' => esc_html__('Intro Byline', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'label_off' => esc_html__('Hide', 'dynamic-content-for-elementor'), 'label_on' => esc_html__('Show', 'dynamic-content-for-elementor'), 'default' => 'yes', 'condition' => ['video_type' => 'vimeo']]);
        $this->add_control('vimeo_color', ['label' => esc_html__('Controls Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'default' => '', 'condition' => ['video_type' => 'vimeo']]);
        $this->end_controls_section();
        $this->start_controls_section('section_image_overlay', ['label' => esc_html__('Image Overlay', 'dynamic-content-for-elementor'), 'condition' => ['acf_type' => 'video'], 'tab' => Controls_Manager::TAB_CONTENT]);
        $this->add_control('show_image_overlay', ['label' => esc_html__('Enable Image Overlay', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'label_off' => esc_html__('Hide', 'dynamic-content-for-elementor'), 'label_on' => esc_html__('Show', 'dynamic-content-for-elementor')]);
        $this->add_control('image_overlay_type', ['label' => esc_html__('Image Type', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'default' => 'custom', 'options' => ['custom' => esc_html__('Custom', 'dynamic-content-for-elementor'), 'acf' => esc_html__('ACF', 'dynamic-content-for-elementor')], 'condition' => ['show_image_overlay' => 'yes']]);
        $this->add_control('image_overlay_acf', ['label' => esc_html__('Field Image', 'dynamic-content-for-elementor'), 'type' => 'ooo_query', 'placeholder' => esc_html__('Select the field...', 'dynamic-content-for-elementor'), 'label_block' => \true, 'query_type' => 'acf', 'object_type' => 'image', 'condition' => ['image_overlay_type' => 'acf']]);
        $this->add_control('image_overlay', ['label' => esc_html__('Image', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::MEDIA, 'default' => ['url' => Utils::get_placeholder_image_src()], 'condition' => ['show_image_overlay' => 'yes', 'image_overlay_type' => 'custom']]);
        $this->add_control('show_play_icon', ['label' => esc_html__('Play Icon', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'default' => 'yes', 'options' => ['yes' => esc_html__('Yes', 'dynamic-content-for-elementor'), 'no' => esc_html__('No', 'dynamic-content-for-elementor')], 'condition' => ['show_image_overlay' => 'yes', 'image_overlay[url]!' => '']]);
        $this->add_control('lightbox', ['label' => esc_html__('Lightbox', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'frontend_available' => \true, 'condition' => ['show_image_overlay' => 'yes', 'image_overlay[url]!' => ''], 'separator' => 'before']);
        $this->add_control('lightbox_color', ['label' => esc_html__('Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['#elementor-video-modal-{{ID}}' => 'background-color: {{VALUE}};'], 'condition' => ['show_image_overlay' => 'yes', 'image_overlay[url]!' => '', 'lightbox' => 'yes']]);
        $this->add_control('lightbox_content_width', ['label' => esc_html__('Content Width', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'units' => ['%'], 'default' => ['unit' => '%'], 'range' => ['%' => ['min' => 50]], 'selectors' => ['#elementor-video-modal-{{ID}} .dialog-widget-content' => 'width: {{SIZE}}{{UNIT}};'], 'condition' => ['show_image_overlay' => 'yes', 'image_overlay[url]!' => '', 'lightbox' => 'yes']]);
        $this->add_control('lightbox_content_position', ['label' => esc_html__('Content Position', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'default' => 'center center', 'frontend_available' => \true, 'options' => ['center center' => esc_html__('Center', 'dynamic-content-for-elementor'), 'center top' => esc_html__('Top', 'dynamic-content-for-elementor')], 'condition' => ['show_image_overlay' => 'yes', 'image_overlay[url]!' => '', 'lightbox' => 'yes'], 'render_type' => 'none']);
        $this->add_control('lightbox_content_animation', ['label' => esc_html__('Entrance Animation', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::ANIMATION, 'default' => '', 'frontend_available' => \true, 'label_block' => \true, 'condition' => ['show_image_overlay' => 'yes', 'image_overlay[url]!' => '', 'lightbox' => 'yes'], 'render_type' => 'none']);
        $this->end_controls_section();
        $this->start_controls_section('lightbox_youtube_video_params', ['label' => esc_html__('YouTube Lightbox Controls', 'dynamic-content-for-elementor'), 'condition' => ['video_type' => 'youtube', 'lightbox' => 'yes']]);
        $this->add_control('youtube_loop', ['label' => esc_html__('Loop', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'frontend_available' => \true, 'separator' => 'before']);
        $this->add_control('youtube_controls', ['label' => esc_html__('Controls', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'frontend_available' => \true, 'separator' => 'before']);
        $this->add_control('youtube_mute', ['label' => esc_html__('Mute', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'frontend_available' => \true, 'separator' => 'before']);
        $this->end_controls_section();
        $this->start_controls_section('section_drop_cap', ['label' => esc_html__('Drop Cap', 'dynamic-content-for-elementor'), 'tab' => Controls_Manager::TAB_STYLE, 'condition' => ['drop_cap' => 'yes', 'acf_type' => ['text', 'textarea']]]);
        $this->add_control('drop_cap_view', ['label' => esc_html__('View', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'options' => ['default' => esc_html__('Default', 'dynamic-content-for-elementor'), 'stacked' => esc_html__('Stacked', 'dynamic-content-for-elementor'), 'framed' => esc_html__('Framed', 'dynamic-content-for-elementor')], 'default' => 'default', 'prefix_class' => 'elementor-drop-cap-view-', 'condition' => ['drop_cap' => 'yes']]);
        $this->add_control('drop_cap_primary_color', ['label' => esc_html__('Primary Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}}.elementor-drop-cap-view-stacked .elementor-drop-cap' => 'background-color: {{VALUE}};', '{{WRAPPER}}.elementor-drop-cap-view-framed .elementor-drop-cap, {{WRAPPER}}.elementor-drop-cap-view-default .elementor-drop-cap' => 'color: {{VALUE}}; border-color: {{VALUE}};'], 'condition' => ['drop_cap' => 'yes']]);
        $this->add_control('drop_cap_secondary_color', ['label' => esc_html__('Secondary Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}}.elementor-drop-cap-view-framed .elementor-drop-cap' => 'background-color: {{VALUE}};', '{{WRAPPER}}.elementor-drop-cap-view-stacked .elementor-drop-cap' => 'color: {{VALUE}};'], 'condition' => ['drop_cap_view!' => 'default']]);
        $this->add_control('drop_cap_size', ['label' => esc_html__('Size', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => 5], 'range' => ['px' => ['max' => 30]], 'selectors' => ['{{WRAPPER}} .elementor-drop-cap' => 'padding: {{SIZE}}{{UNIT}};'], 'condition' => ['drop_cap_view!' => 'default']]);
        $this->add_control('drop_cap_space', ['label' => esc_html__('Space', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => 10], 'range' => ['px' => ['max' => 50]], 'selectors' => ['body:not(.rtl) {{WRAPPER}} .elementor-drop-cap' => 'margin-right: {{SIZE}}{{UNIT}};', 'body.rtl {{WRAPPER}} .elementor-drop-cap' => 'margin-left: {{SIZE}}{{UNIT}};']]);
        $this->add_control('drop_cap_border_radius', ['label' => esc_html__('Border Radius', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'size_units' => ['%', 'px'], 'default' => ['unit' => '%'], 'range' => ['%' => ['max' => 50]], 'selectors' => ['{{WRAPPER}} .elementor-drop-cap' => 'border-radius: {{SIZE}}{{UNIT}};']]);
        $this->add_control('drop_cap_border_width', ['label' => esc_html__('Border Width', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::DIMENSIONS, 'selectors' => ['{{WRAPPER}} .elementor-drop-cap' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'], 'condition' => ['drop_cap_view' => 'framed']]);
        $this->add_group_control(Group_Control_Typography::get_type(), ['name' => 'drop_cap_typography', 'selector' => '{{WRAPPER}} .elementor-drop-cap-letter', 'exclude' => ['letter_spacing'], 'condition' => ['drop_cap' => 'yes']]);
        $this->end_controls_section();
    }
    protected function safe_render()
    {
        $settings = $this->get_settings_for_display();
        if (empty($settings)) {
            return;
        }
        $id_page = Helper::get_the_id($settings['other_post_source']);
        $type_page = get_post_type($id_page);
        $output = '';
        $field_id = $settings['acf_field_list'];
        $user_selected_field_type = $settings['acf_type'];
        $field_settings = \acf_get_field($field_id);
        $field_type_detected = $field_settings['type'] ?? '';
        $image_size = $settings['size_size'];
        $use_bg = $settings['use_bg'];
        $wrap_effect_start = '<div class="mask"><div class="wrap-filters">';
        $wrap_effect_end = '</div></div>';
        $overlay_block = '';
        if ($settings['use_overlay'] == 'yes') {
            $overlay_block = '<div class="dce-overlay"></div>';
        }
        $overlay_hover_block = '<div class="dce-overlay_hover"></div>';
        $acf_field_value = Helper::get_acf_field_value($field_id, $id_page);
        if ($user_selected_field_type === 'video') {
            // For video fields, keep the value unchanged as it's handled by oEmbed
            // and will be processed through wp_oembed_get() which provides sanitization
        } else {
            // For all other field types, apply sanitization
            if (!empty($acf_field_value)) {
                if ($user_selected_field_type === 'wysiwyg') {
                    // WYSIWYG with format=true runs do_shortcode via ACF, which may produce <style>
                    // tags from Elementor (e.g. Loop Grid CSS). Allow <style> to pass through so
                    // Elementor's CSS is applied correctly instead of leaking as visible text.
                    $allowed_html = \array_merge(wp_kses_allowed_html('post'), ['style' => ['id' => \true, 'type' => \true, 'media' => \true]]);
                    $acf_field_value = wp_kses($acf_field_value, $allowed_html, wp_allowed_protocols());
                } else {
                    $acf_field_value = wp_kses_post_deep($acf_field_value);
                }
            }
        }
        if (\in_array($user_selected_field_type, ['image', 'image_url'], \true)) {
            $imageField = $acf_field_value;
            if (\is_string($imageField)) {
                $user_selected_field_type = 'image_url';
            } elseif (\is_numeric($imageField)) {
                $user_selected_field_type = 'image';
            } elseif (\is_array($imageField)) {
                $user_selected_field_type = 'image_array';
            }
        }
        if ('list' === $user_selected_field_type) {
            $field_wrapper = 'div';
            if (empty($settings['acf_text_before_block'])) {
                $field_wrapper = 'span';
            }
            $output = '<' . $field_wrapper . ' class="' . esc_attr('dce-acf-list-' . $settings['list_style']) . '">';
            // l'elemento Item Wrap
            // Radio or Checkbox list
            switch ($field_type_detected) {
                case 'radio':
                    if (\is_array($acf_field_value)) {
                        if ($settings['list_array_return'] == '') {
                            $output .= '<span class="dce-acf-list"><span class="dce-acf-list-label">' . $acf_field_value['label'] . ': </span><span class="dce-acf-list-value">' . $acf_field_value['value'] . '</span></span>';
                        } elseif ($settings['list_array_return'] == 'label') {
                            $output .= '<span class="dce-acf-list dce-acf-list-label">' . $acf_field_value['label'] . '</span>';
                        } elseif ($settings['list_array_return'] == 'value') {
                            $output .= '<span class="dce-acf-list dce-acf-list-value">' . $acf_field_value['value'] . '</span>';
                        }
                    } else {
                        $output .= $acf_field_value;
                    }
                    break;
                case 'checkbox':
                    if (!empty($acf_field_value)) {
                        foreach ($acf_field_value as $akey => $field) {
                            if ($akey > 0 && !empty($settings['list_separator'])) {
                                $output .= '<span class="dce-acf-list-separator">' . wp_kses_post($settings['list_separator']) . '</span>';
                            }
                            if (\is_array($field)) {
                                if ($settings['list_array_return'] == '') {
                                    $output .= '<span class="dce-acf-list"><span class="dce-acf-list-label">' . $field['label'] . ': </span><span class="dce-acf-list-value">' . $field['value'] . '</span></span>';
                                } elseif ($settings['list_array_return'] == 'label') {
                                    $output .= '<span class="dce-acf-list dce-acf-list-label">' . $field['label'] . '</span>';
                                } elseif ($settings['list_array_return'] == 'value') {
                                    $output .= '<span class="dce-acf-list dce-acf-list-value">' . $field['value'] . '</span>';
                                }
                            } else {
                                $output .= '<span class="dce-acf-list dce-acf-list-value">' . $field . '</span>';
                            }
                        }
                    }
                    break;
                case 'taxonomy':
                default:
                    if (!empty($acf_field_value)) {
                        if (!\is_array($acf_field_value)) {
                            $acf_field_value = array($acf_field_value);
                        }
                        foreach ($acf_field_value as $akey => $field) {
                            $label = Helper::to_readable_string($field);
                            if ($akey > 0 && !empty($settings['list_separator'])) {
                                $output .= '<span class="dce-acf-list-separator">' . wp_kses_post($settings['list_separator']) . '</span>';
                            }
                            $output .= '<span class="dce-acf-list dce-acf-list-value">' . $label . '</span>';
                        }
                    }
            }
            $output .= '</' . $field_wrapper . '>';
            // end wrap
        } elseif (\in_array($user_selected_field_type, ['text', 'textarea', 'select', 'wysiwyg', 'number'], \true)) {
            $acf_field_value = Helper::to_readable_string($acf_field_value);
            if ($user_selected_field_type == 'select') {
                $output = $acf_field_value;
            } elseif ($user_selected_field_type == 'wysiwyg') {
                $output = $acf_field_value;
            } else {
                // text and textarea
                $output = $acf_field_value;
            }
        } elseif (\in_array($user_selected_field_type, ['url', 'file'], \true)) {
            $output = $acf_field_value;
            if ('elementor_library' === $type_page && $output == '') {
                $output = '#';
            }
        } elseif ($user_selected_field_type === 'display') {
            $field_value = \get_field($field_id, $id_page ? $id_page : null);
            if (!empty($field_value)) {
                $output .= wp_kses_post($field_value);
            }
        } elseif ($user_selected_field_type == 'email') {
            $email_value = $acf_field_value;
            if ($type_page == 'elementor_library' && empty($email_value)) {
                $email_value = 'your-email@my-domain.com';
            }
            $sanitized_email = sanitize_email($email_value);
            if (!empty($sanitized_email)) {
                $output = '<a href="mailto:' . esc_attr($sanitized_email) . '">' . esc_html($sanitized_email) . '</a>';
            } else {
                $output = esc_html($email_value);
            }
        } elseif ($user_selected_field_type == 'date') {
            $output = $acf_field_value;
            if ($output == '') {
                if (\Elementor\Plugin::$instance->editor->is_edit_mode()) {
                    Helper::notice(\false, esc_html__('Empty date field', 'dynamic-content-for-elementor'));
                }
            } else {
                $format_display = $settings['date_format'];
                $source_format = $field_settings['return_format'] ?? 'd/m/Y g:i a';
                $d = \DateTime::createFromFormat($source_format, $output);
                if ($d) {
                    $locale = get_locale();
                    if (!\strpos($locale, '.')) {
                        // avoid months being printed in other encodings
                        $locale = "{$locale}.UTF-8";
                    }
                    \setlocale(\LC_TIME, $locale);
                    $date_format = Helper::date_format_to_strftime_format($format_display);
                    $date_format = \DynamicContentForElementor\Helper::strftime_format_to_date_format($date_format);
                    $output = $d->format($date_format);
                } else {
                    $timestamp = \strtotime($output);
                    $output = date_i18n($format_display, $timestamp);
                }
            }
        } elseif ('image' === $user_selected_field_type) {
            $settings['html_tag'] = 'div';
            $imageSrc = Group_Control_Image_Size::get_attachment_image_src($imageField, 'size', $settings);
            $imageSrcUrl = $imageSrc;
            if ($type_page == 'elementor_library' && empty($imageSrcUrl)) {
                $imageSrcUrl = \Elementor\Utils::get_placeholder_image_src();
            }
            if (empty($imageSrcUrl)) {
                return;
            }
            if ($use_bg == 0) {
                $output = '<div class="acf-image">' . $wrap_effect_start . '<img src="' . esc_url($imageSrcUrl) . '" />' . $wrap_effect_end . $overlay_block . $overlay_hover_block . '</div>';
            } else {
                $bg_featured_image = '<div class="acf-image acf-bg-image">' . $wrap_effect_start . '<figure class="dynamic-content-for-elementor-acfimage-bg" style="background-image: url(&quot;' . esc_url($imageSrcUrl) . '&quot;); background-repeat: no-repeat; background-size: cover;"></figure>' . $wrap_effect_end . $overlay_block . $overlay_hover_block . '</div>';
                $output = $bg_featured_image;
            }
        } elseif ('image_url' === $user_selected_field_type) {
            if ($type_page == 'elementor_library' && $imageField == '') {
                $imageSrcUrl = \Elementor\Utils::get_placeholder_image_src();
            }
            if (empty($imageField)) {
                return;
            }
            $settings['html_tag'] = 'div';
            if (\is_numeric($imageField)) {
                $imageField = Group_Control_Image_Size::get_attachment_image_src($imageField, 'size', $settings);
            }
            if (!$use_bg) {
                $output = '<div class="acf-image">' . $wrap_effect_start . '<img src="' . esc_url($imageField) . '" />' . $wrap_effect_end . $overlay_block . $overlay_hover_block . '</div>';
            } else {
                $bg_featured_image = '<div class="acf-image acf-bg-image">' . $wrap_effect_start . '<figure class="dynamic-content-for-elementor-acfimage-bg" style="background-image: url(&quot;' . esc_url($imageField) . '&quot;); background-repeat: no-repeat; background-size: cover;"></figure>' . $wrap_effect_end . $overlay_block . $overlay_hover_block . '</div>';
                $output = $bg_featured_image;
            }
        } elseif ('image_array' === $user_selected_field_type) {
            $settings['html_tag'] = 'div';
            $imageAlt = isset($imageField['alt']) ? esc_attr($imageField['alt']) : '';
            $imageDesc = isset($imageField['description']) ? wp_kses_post($imageField['description']) : '';
            $imageCapt = isset($imageField['caption']) ? wp_kses_post($imageField['caption']) : '';
            $imageSrc = Group_Control_Image_Size::get_attachment_image_src($imageField['ID'], 'size', $settings);
            $imageAttach = '<img src="' . esc_url($imageSrc) . '" alt="' . $imageAlt . '" />';
            $imageSrcUrl = $imageSrc;
            if ($type_page == 'elementor_library' && $imageSrcUrl == '') {
                $imageAttach = '<img src="' . esc_url(\Elementor\Utils::get_placeholder_image_src()) . '" />';
            }
            if (empty($imageSrcUrl)) {
                return;
            }
            if (!$use_bg) {
                $output = '<div class="acf-image">' . $wrap_effect_start . $imageAttach . $wrap_effect_end . $overlay_block . $overlay_hover_block . '</div>';
            } else {
                $bg_featured_image = '<div class="acf-image acf-bg-image">' . $wrap_effect_start . '<figure class="dynamic-content-for-elementor-acfimage-bg" style="background-image: url(&quot;' . esc_url($imageSrcUrl) . '&quot;); background-repeat: no-repeat; background-size: cover;"></figure>' . $wrap_effect_end . $overlay_block . $overlay_hover_block . '</div>';
                $output = $bg_featured_image;
            }
        } elseif ('video' === $user_selected_field_type) {
            // Get formatted HTML
            $video_html = Helper::get_acf_field_value($field_id, $id_page, \true);
            if ($type_page == 'elementor_library' && empty($video_html)) {
                $video_field = 'https://www.youtube.com/watch?v=9uOETcuFjbE';
                $video_html = wp_oembed_get($video_field);
            }
            if (empty($video_html)) {
                if (\Elementor\Plugin::$instance->editor->is_edit_mode()) {
                    Helper::notice(\false, esc_html__('Empty or invalid video field', 'dynamic-content-for-elementor'));
                }
                return;
            }
            // Get raw URL only if needed for lightbox
            $video_field = '';
            if (!empty($settings['lightbox']) || $this->has_image_overlay()) {
                $video_field = Helper::get_acf_field_value($field_id, $id_page, \false);
                if (\is_string($video_field)) {
                    $video_field = esc_url_raw($video_field);
                }
            }
            // Apply custom video parameters (autoplay, loop, etc.) to iframe HTML
            $video_html = $this->filter_oembed_result($video_html);
            $this->add_render_attribute('video-wrapper', 'class', 'elementor-wrapper');
            if (!$settings['lightbox']) {
                $this->add_render_attribute('video-wrapper', 'class', 'elementor-video-wrapper');
            }
            $this->add_render_attribute('video-wrapper', 'class', 'elementor-open-' . (!empty($settings['lightbox']) ? 'lightbox' : 'inline'));
            ?>
			<div <?php 
            echo $this->get_render_attribute_string('video-wrapper');
            ?>>
				<?php 
            if (!$settings['lightbox']) {
                // Validate HTML to allow only safe iframe tags
                $allowed_video_html = ['iframe' => ['src' => \true, 'width' => \true, 'height' => \true, 'frameborder' => \true, 'allowfullscreen' => \true, 'allow' => \true, 'title' => \true, 'class' => \true, 'style' => \true, 'loading' => \true, 'referrerpolicy' => \true]];
                echo wp_kses($video_html, $allowed_video_html);
            } else {
                $video_url = $video_field;
                $embed_params = $this->get_lightbox_video_params($settings);
                $embed_options = ['privacy' => '', 'lazy_load' => \false];
                $lightbox_url = \Elementor\Embed::get_embed_url($video_url, $embed_params, $embed_options);
                $lightbox_options = $this->get_lightbox_options($lightbox_url);
                $this->add_render_attribute('image-overlay', ['data-elementor-open-lightbox' => 'yes', 'data-elementor-lightbox' => wp_json_encode($lightbox_options), 'data-e-action-hash' => \Elementor\Plugin::instance()->frontend->create_action_hash('lightbox', $lightbox_options)]);
            }
            if ($this->has_image_overlay()) {
                $this->add_render_attribute('image-overlay', 'class', 'elementor-custom-embed-image-overlay');
                if (!$settings['lightbox']) {
                    if ($settings['image_overlay_type'] === 'custom') {
                        $this->add_render_attribute('image-overlay', 'style', 'background-image: url(' . esc_url($settings['image_overlay']['url']) . ');');
                    } elseif ($settings['image_overlay_type'] === 'acf') {
                        $image_acf_overlay = $this->get_image_overlay($settings['image_overlay_acf'], $id_page);
                        $this->add_render_attribute('image-overlay', 'style', 'background-image: url(' . esc_url($image_acf_overlay) . ');');
                    }
                }
                ?>
					<div <?php 
                echo $this->get_render_attribute_string('image-overlay');
                ?>>

						<?php 
                if ($settings['lightbox']) {
                    ?>
							<?php 
                    if ($settings['image_overlay_type'] === 'custom') {
                        ?>
								<img src="<?php 
                        echo esc_url($settings['image_overlay']['url']);
                        ?>">
							<?php 
                    } elseif ($settings['image_overlay_type'] === 'acf') {
                        ?>
								<?php 
                        $image_acf_overlay = $this->get_image_overlay($settings['image_overlay_acf'], $id_page);
                        ?>
								<img src="<?php 
                        echo esc_url($image_acf_overlay);
                        ?>">
							<?php 
                    }
                    ?>

						<?php 
                }
                ?>
						<?php 
                if ('yes' === $settings['show_play_icon']) {
                    ?>
							<div class="elementor-custom-embed-play">
								<i class="fa fa-play-circle"></i>
							</div>
						<?php 
                }
                ?>
					</div>
					<?php 
            }
            ?>
			</div>

			<?php 
        } elseif ('empty' === $user_selected_field_type) {
            $output = '';
        }
        switch ($settings['link_to']) {
            case 'custom':
                if (!empty($settings['link']['url']) && \filter_var($settings['link']['url'], \FILTER_VALIDATE_URL)) {
                    $link = esc_url($settings['link']['url']);
                    $target = !empty($settings['link']['is_external']) ? ' target="_blank"' : '';
                } else {
                    $link = \false;
                }
                break;
            case 'acf_url':
                if (!empty($settings['acf_field_url'])) {
                    $link = Helper::get_acf_field_value($settings['acf_field_url'], $id_page);
                    $link = esc_url(\is_string($link) ? $link : '');
                    $target = !empty($settings['acf_field_url_target']) ? ' target="_blank"' : '';
                    $target .= !empty($settings['acf_field_url_nofollow']) ? ' rel="nofollow"' : '';
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
        $html = '';
        $animation_class = !empty($settings['hover_animation']) ? 'elementor-animation-' . $settings['hover_animation'] : '';
        if ($output != '' || $user_selected_field_type == 'empty') {
            if ($settings['acf_dynamic']) {
                $output = do_shortcode($output);
            }
            if ($settings['acf_currency_mode'] || $settings['acf_settoDecimal']) {
                $output = '<div id="' . esc_attr($settings['acf_field_list']) . '">' . $output . '</div>';
            }
            $html_tag = 'div';
            if ($settings['html_tag']) {
                $html_tag = \DynamicContentForElementor\Helper::validate_html_tag($settings['html_tag']);
            }
            $html = '<' . $html_tag . ' class="' . esc_attr('dynamic-content-for-elementor-acf ' . $animation_class) . '">';
            if (!empty($settings['acf_text_before'])) {
                $output = '<span class="tx-before">' . wp_kses_post($settings['acf_text_before']) . '</span>' . $output;
            }
            if (!empty($settings['acf_text_after'])) {
                $output .= '<span class="tx-after">' . wp_kses_post($settings['acf_text_after']) . '</span>';
            }
            if ($link) {
                $html .= '<a href="' . $link . '"' . $target . '>' . $output . '</a>';
            } else {
                $html .= $output;
            }
            $html .= '</' . $html_tag . '>';
        }
        if ('' !== $acf_field_value) {
            // Don't show if is '', show if it's 0
            echo $html;
        } elseif (!empty($this->get_settings_for_display('fallback'))) {
            $this->render_fallback();
        }
    }
    public function filter_oembed_result($html)
    {
        $settings = $this->get_settings_for_display();
        $params = [];
        if ('youtube' === $settings['video_type']) {
            $youtube_options = ['autoplay', 'loop', 'mute', 'rel', 'controls', 'showinfo'];
            foreach ($youtube_options as $option) {
                if ('autoplay' === $option && $this->has_image_overlay()) {
                    continue;
                }
                $value = 'yes' === $settings['yt_' . $option] ? '1' : '0';
                if ($settings['yt_loop'] == 'yes') {
                    $ytID = $this->youtube_id_from_url($html);
                    $params['playlist'] = $ytID;
                }
                $params[$option] = $value;
            }
            $params['wmode'] = 'opaque';
        }
        if ('vimeo' === $settings['video_type']) {
            $vimeo_options = ['autoplay', 'loop', 'title', 'portrait', 'byline'];
            foreach ($vimeo_options as $option) {
                if ('autoplay' === $option && $this->has_image_overlay()) {
                    continue;
                }
                $value = 'yes' === $settings['vimeo_' . $option] ? '1' : '0';
                $params[$option] = $value;
            }
            $params['color'] = \str_replace('#', '', $settings['vimeo_color']);
        }
        if (!empty($params)) {
            \preg_match('/<iframe.*src=\\"(.*)\\".*><\\/iframe>/isU', $html, $matches);
            if (isset($matches[1])) {
                $url = esc_url(add_query_arg($params, $matches[1]));
                $html = \str_replace($matches[1], $url, $html);
            }
        }
        return $html;
    }
    /**
     * @param string $lightbox_url
     * @return array<string,mixed>
     */
    protected function get_lightbox_options($lightbox_url)
    {
        return ['type' => 'video', 'videoType' => 'youtube', 'url' => $lightbox_url, 'modalOptions' => ['id' => 'elementor-lightbox-' . $this->get_id(), 'entranceAnimation' => '', 'entranceAnimation_tablet' => '', 'entranceAnimation_mobile' => '', 'videoAspectRatio' => '169']];
    }
    protected function get_lightbox_video_params($settings)
    {
        return ['start' => '', 'end' => '', 'wmode' => 'opaque', 'loop' => $settings['youtube_loop'] == 'yes' ? '1' : '0', 'controls' => $settings['youtube_controls'] == 'yes' ? '1' : '0', 'mute' => $settings['youtube_mute'] == 'yes' ? '1' : '0', 'rel' => '0', 'modestbranding' => '0'];
    }
    protected function youtube_id_from_url($html)
    {
        \preg_match('/<iframe.*src=\\"(.*)\\".*><\\/iframe>/isU', $html, $matches);
        $url = esc_url($matches[1]);
        $pattern = "/^(?:http(?:s)?:\\/\\/)?(?:www\\.)?(?:m\\.)?(?:youtu\\.be\\/|youtube\\.com\\/(?:(?:watch)?\\?(?:.*&)?v(?:i)?=|(?:embed|v|vi|user)\\/))([^\\?&\"'>]+)/";
        $result = \preg_match($pattern, $url, $matches);
        if ($result) {
            return $matches[1];
        }
        return \false;
    }
    /**
     * @param string|array<string,mixed> $acf_image_field
     * @param string $id_page
     * @return string
     */
    protected function get_image_overlay($acf_image_field, $id_page)
    {
        $image_acf_overlay = Helper::get_acf_field_value($acf_image_field, $id_page);
        if (\is_string($image_acf_overlay)) {
            $image_acf_overlay = $image_acf_overlay;
        } elseif (\is_numeric($image_acf_overlay)) {
            $imageSrc = wp_get_attachment_image_src($image_acf_overlay, 'full');
            $imageSrcUrl = $imageSrc[0];
            $image_acf_overlay = $imageSrcUrl;
        } elseif (\is_array($image_acf_overlay)) {
            $imageSrc = wp_get_attachment_image_src($image_acf_overlay['ID'], 'full');
            $imageSrcUrl = $imageSrc[0];
            $image_acf_overlay = $imageSrcUrl;
        }
        if ($image_acf_overlay === '') {
            $image_acf_overlay = \Elementor\Utils::get_placeholder_image_src();
        }
        return $image_acf_overlay;
    }
    protected function get_hosted_params()
    {
        $settings = $this->get_settings_for_display();
        $params = [];
        $params['src'] = $settings['hosted_link'];
        $hosted_options = ['autoplay', 'loop'];
        foreach ($hosted_options as $key => $option) {
            $value = 'yes' === $settings['hosted_' . $option] ? '1' : '0';
            $params[$option] = $value;
        }
        if (!empty($settings['hosted_width'])) {
            $params['width'] = $settings['hosted_width'];
        }
        if (!empty($settings['hosted_height'])) {
            $params['height'] = $settings['hosted_height'];
        }
        return $params;
    }
    protected function has_image_overlay()
    {
        $settings = $this->get_settings_for_display();
        return 'yes' === $settings['show_image_overlay'];
    }
    protected function render_fallback()
    {
        $html_tag = $this->get_settings_for_display('html_tag') ?? 'div';
        $this->set_render_attribute('fallback', 'class', ['dynamic-content-for-elementor-acf', 'dce-fallback']);
        if (!empty($this->get_settings_for_display('hover_animation'))) {
            $this->add_render_attribute('fallback', 'class', 'elementor-animation-' . $this->get_settings_for_display('hover_animation'));
        }
        echo '<' . Helper::validate_html_tag($html_tag) . ' ' . $this->get_render_attribute_string('fallback') . '>';
        echo $this->get_settings_for_display('fallback_text');
        echo '</' . Helper::validate_html_tag($html_tag) . '>';
    }
}
