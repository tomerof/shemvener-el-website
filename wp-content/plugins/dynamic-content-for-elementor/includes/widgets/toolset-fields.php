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
class ToolsetFields extends \DynamicContentForElementor\Widgets\WidgetPrototype
{
    public function get_script_depends()
    {
        return ['elementor-dialog'];
    }
    public function get_style_depends()
    {
        return ['dce-toolset'];
    }
    /**
     * Register controls after check if this feature is only for admin
     *
     * @return void
     */
    protected function safe_register_controls()
    {
        $this->start_controls_section('section_content', ['label' => esc_html__('Toolset', 'dynamic-content-for-elementor')]);
        $this->add_control('toolset_field_list', ['label' => esc_html__('Fields list', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'default' => 'empty', 'groups' => $this->get_toolset_fields()]);
        $this->add_control('toolset_field_type', ['label' => esc_html__('Field type', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'default' => 0, 'options' => ['empty' => esc_html__('Select for options', 'dynamic-content-for-elementor'), 'textfield' => esc_html__('Textfield', 'dynamic-content-for-elementor'), 'url' => esc_html__('URL', 'dynamic-content-for-elementor'), 'phone' => esc_html__('Phone', 'dynamic-content-for-elementor'), 'email' => esc_html__('Email', 'dynamic-content-for-elementor'), 'textarea' => esc_html__('Textarea', 'dynamic-content-for-elementor'), 'wysiwyg' => esc_html__('WYSIWYG', 'dynamic-content-for-elementor'), 'image' => esc_html__('Image', 'dynamic-content-for-elementor'), 'date' => esc_html__('Date', 'dynamic-content-for-elementor'), 'numeric' => esc_html__('Numeric', 'dynamic-content-for-elementor'), 'video' => esc_html__('Video', 'dynamic-content-for-elementor')]]);
        $this->add_control('toolset_field_hide', ['label' => esc_html__('Hide if empty', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => 'yes', 'description' => esc_html__('Hide the field in front end layer', 'dynamic-content-for-elementor')]);
        $this->end_controls_section();
        $this->start_controls_section('section_settings', ['label' => esc_html__('Settings', 'dynamic-content-for-elementor'), 'condition' => ['toolset_field_type!' => 'video']]);
        $this->add_control('toolset_text_before', ['label' => esc_html__('Text before', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'default' => '', 'condition' => ['toolset_field_type!' => 'video']]);
        $this->add_control('toolset_text_after', ['label' => esc_html__('Text after', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'default' => '', 'condition' => ['toolset_field_type!' => 'video']]);
        $this->add_control('toolset_url_enable', ['label' => esc_html__('Link', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'condition' => ['toolset_field_type' => 'url']]);
        $this->add_control('toolset_url_custom_text', ['label' => esc_html__('Custom URL text', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'default' => '', 'condition' => ['toolset_field_type' => 'url']]);
        $this->add_control('toolset_url_target', ['label' => esc_html__('Target type', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'options' => ['_self' => '_self', '_blank' => '_blank', '_parent' => '_parent', '_top' => '_top'], 'default' => '_self', 'condition' => ['toolset_field_type' => 'url']]);
        $this->add_control('toolset_date_format', ['label' => esc_html__('Format', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'default' => 0, 'options' => ['default' => esc_html__('Default from WordPress settings', 'dynamic-content-for-elementor'), '%Y%m%d' => esc_html__('YYYYMMDD', 'dynamic-content-for-elementor'), '%Y-%m-%d' => esc_html__('YYYY-MM-DD', 'dynamic-content-for-elementor'), '%d/%m/%Y' => esc_html__('DD/MM/YYYY', 'dynamic-content-for-elementor'), '%d-%m-%Y' => esc_html__('DD-MM-YYYY', 'dynamic-content-for-elementor'), '%Y-%m-%d %H:%M:%S' => esc_html__('YYYY-MM-DD H:M:S', 'dynamic-content-for-elementor'), '%d/%m/%Y %H:%M:%S' => esc_html__('DD/MM/YY H:M:S', 'dynamic-content-for-elementor'), '%d/%m/%y' => esc_html__('D/M/Y', 'dynamic-content-for-elementor'), '%d-%m-%y' => esc_html__('D-M-Y', 'dynamic-content-for-elementor'), '%I:%M %p' => esc_html__('H:M (12 hours)', 'dynamic-content-for-elementor'), '%A %m %B %Y' => esc_html__('Full date', 'dynamic-content-for-elementor'), '%A %m %B %Y at %H:%M' => esc_html__('Full date with hours', 'dynamic-content-for-elementor'), 'timestamp' => esc_html__('Timestamp', 'dynamic-content-for-elementor'), 'custom' => esc_html__('Custom', 'dynamic-content-for-elementor')], 'condition' => ['toolset_field_type' => 'date']]);
        $this->add_control('toolset_date_custom_format', ['label' => esc_html__('Custom date format', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'default' => '', 'condition' => ['toolset_date_format' => 'custom'], 'description' => esc_html__('See PHP strftime() function reference', 'dynamic-content-for-elementor')]);
        $this->add_group_control(Group_Control_Image_Size::get_type(), ['name' => 'size', 'label' => esc_html__('Image Size', 'dynamic-content-for-elementor'), 'default' => 'large', 'condition' => ['toolset_field_type' => 'image']]);
        $this->add_responsive_control('align', ['label' => esc_html__('Alignment', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CHOOSE, 'options' => ['left' => ['title' => esc_html__('Left', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-left'], 'center' => ['title' => esc_html__('Center', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-center'], 'right' => ['title' => esc_html__('Right', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-right'], 'justify' => ['title' => esc_html__('Justified', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-justify']], 'default' => '', 'prefix_class' => 'align-dce-', 'selectors' => ['{{WRAPPER}}' => 'text-align: {{VALUE}};'], 'condition' => ['toolset_field_type' => ['textfield', 'url', 'image', 'phone', 'email', 'textarea', 'wysiwyg', 'date', 'numeric']]]);
        $this->add_control('use_bg', ['label' => esc_html__('Background', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CHOOSE, 'options' => ['1' => ['title' => esc_html__('Yes', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-check'], '0' => ['title' => esc_html__('No', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-ban']], 'default' => '0', 'condition' => ['toolset_field_type' => 'image']]);
        $this->add_control('bg_position', ['label' => esc_html__('Background position', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'default' => 'top center', 'options' => ['' => esc_html__('Default', 'dynamic-content-for-elementor'), 'top left' => esc_html__('Top Left', 'dynamic-content-for-elementor'), 'top center' => esc_html__('Top Center', 'dynamic-content-for-elementor'), 'top right' => esc_html__('Top Right', 'dynamic-content-for-elementor'), 'center left' => esc_html__('Center Left', 'dynamic-content-for-elementor'), 'center center' => esc_html__('Center Center', 'dynamic-content-for-elementor'), 'center right' => esc_html__('Center Right', 'dynamic-content-for-elementor'), 'bottom left' => esc_html__('Bottom Left', 'dynamic-content-for-elementor'), 'bottom center' => esc_html__('Bottom Center', 'dynamic-content-for-elementor'), 'bottom right' => esc_html__('Bottom Right', 'dynamic-content-for-elementor')], 'selectors' => ['{{WRAPPER}} .dynamic-content-for-elementor-toolset-bg' => 'background-position: {{VALUE}};'], 'condition' => ['toolset_field_type' => 'image', 'use_bg' => '1']]);
        $this->add_control('bg_extend', ['label' => esc_html__('Extend background', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => '', 'prefix_class' => 'extendbg-', 'condition' => ['toolset_field_type' => 'image', 'use_bg' => '1']]);
        $this->add_responsive_control('height', ['label' => esc_html__('Minimum height', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => 200, 'unit' => 'px'], 'tablet_default' => ['unit' => 'px'], 'mobile_default' => ['unit' => 'px'], 'size_units' => ['px', '%', 'vh'], 'range' => ['%' => ['min' => 1, 'max' => 100], 'px' => ['min' => 1, 'max' => 1000], 'vh' => ['min' => 1, 'max' => 100]], 'selectors' => ['{{WRAPPER}} .dynamic-content-for-elementor-toolset-bg' => 'min-height: {{SIZE}}{{UNIT}};'], 'condition' => ['toolset_field_type' => 'image', 'use_bg' => '1']]);
        $this->add_control('toolset_phone_number_enable', ['label' => esc_html__('Link', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'label_off' => esc_html__('No', 'dynamic-content-for-elementor'), 'label_on' => esc_html__('Yes', 'dynamic-content-for-elementor'), 'default' => 'yes', 'condition' => ['toolset_field_type' => 'phone']]);
        $this->add_control('toolset_phone_number_custom_text', ['label' => esc_html__('Custom phone number', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'default' => '', 'condition' => ['toolset_field_type' => 'phone']]);
        $this->add_control('toolset_email_target', ['label' => esc_html__('Link mailto', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'label_off' => esc_html__('Off', 'dynamic-content-for-elementor'), 'label_on' => esc_html__('On', 'dynamic-content-for-elementor'), 'condition' => ['toolset_field_type' => 'email']]);
        $this->add_control('toolset_numeric_currency', ['label' => esc_html__('Currency', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => '', 'condition' => ['toolset_field_type' => 'numeric']]);
        $this->add_control('toolset_currency_symbol', ['label' => esc_html__('Currency symbol', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'default' => '', 'condition' => ['toolset_field_type' => 'numeric', 'toolset_numeric_currency' => 'yes']]);
        $this->add_control('toolset_currency_symbol_position', ['label' => esc_html__('Symbol position', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CHOOSE, 'options' => ['before' => ['title' => esc_html__('Before', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-arrow-left'], 'after' => ['title' => esc_html__('After', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-arrow-right']], 'default' => 'before', 'toggle' => \true, 'condition' => ['toolset_field_type' => 'numeric', 'toolset_numeric_currency' => 'yes']]);
        $this->end_controls_section();
        // ------------------------------------------------------------ [ OVERLAY Image ]
        $this->start_controls_section('section_overlay', ['label' => esc_html__('Overlay Image', 'dynamic-content-for-elementor'), 'tab' => Controls_Manager::TAB_CONTENT, 'condition' => ['toolset_field_type' => 'image']]);
        $this->add_control('overlay_heading', ['label' => esc_html__('Overlay', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HEADING, 'separator' => 'before', 'condition' => ['toolset_field_type' => ['textfield', 'url', 'image', 'phone', 'email', 'textarea', 'wysiwyg', 'date', 'numeric']]]);
        $this->add_control('use_overlay', ['label' => esc_html__('Overlay Image', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => '', 'label_on' => esc_html__('Show', 'dynamic-content-for-elementor'), 'label_off' => esc_html__('Hide', 'dynamic-content-for-elementor'), 'return_value' => 'yes']);
        $this->add_group_control(Group_Control_Background::get_type(), ['name' => 'background_overlay', 'types' => ['classic', 'gradient'], 'selector' => '{{WRAPPER}} .dce-overlay', 'condition' => ['use_overlay' => 'yes']]);
        $this->end_controls_section();
        // ********************** Section STYLE **********************
        $this->start_controls_section('section_style', ['label' => esc_html__('Style', 'dynamic-content-for-elementor'), 'tab' => Controls_Manager::TAB_STYLE, 'condition' => ['toolset_field_type' => ['textfield', 'url', 'image', 'phone', 'email', 'textarea', 'wysiwyg', 'date', 'numeric']]]);
        $this->add_control('tx_heading', ['label' => esc_html__('Text', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HEADING, 'separator' => 'before', 'condition' => ['toolset_field_type' => ['textfield', 'url', 'image', 'phone', 'email', 'textarea', 'wysiwyg', 'date', 'numeric']]]);
        $this->add_control('color', ['label' => esc_html__('Text Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .dynamic-content-for-elementor-toolset .edc-toolset' => 'color: {{VALUE}};'], 'condition' => ['toolset_field_type' => ['textfield', 'url', 'image', 'phone', 'email', 'textarea', 'wysiwyg', 'date', 'numeric']]]);
        $this->add_control('bg_color', ['label' => esc_html__('Background Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .dynamic-content-for-elementor-toolset' => 'background-color: {{VALUE}};'], 'condition' => ['toolset_field_type' => ['textfield', 'url', 'image', 'phone', 'email', 'textarea', 'wysiwyg', 'date', 'numeric']]]);
        $this->add_responsive_control('toolset_padding', ['label' => esc_html__('Padding', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => ['px', '%'], 'selectors' => ['{{WRAPPER}} .dynamic-content-for-elementor-toolset' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};']]);
        $this->add_responsive_control('toolset_space', ['label' => esc_html__('Space', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => 0], 'range' => ['px' => ['max' => 100, 'min' => 0, 'step' => 1]], 'selectors' => ['{{WRAPPER}} .dynamic-content-for-elementor-toolset' => 'margin-bottom: {{SIZE}}{{UNIT}};']]);
        $this->add_responsive_control('toolset_shift', ['label' => esc_html__('Shift', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'range' => ['px' => ['max' => 180, 'min' => -180, 'step' => 1]], 'selectors' => ['{{WRAPPER}} .dynamic-content-for-elementor-toolset' => 'left: {{SIZE}}{{UNIT}};']]);
        $this->add_group_control(Group_Control_Typography::get_type(), ['name' => 'typography_tx', 'label' => esc_html__('Typography', 'dynamic-content-for-elementor'), 'selector' => '{{WRAPPER}} .dynamic-content-for-elementor-toolset', 'condition' => ['toolset_field_type' => ['textfield', 'url', 'image', 'phone', 'email', 'textarea', 'wysiwyg', 'date', 'numeric']]]);
        $this->add_group_control(Group_Control_Text_Shadow::get_type(), ['name' => 'text_shadow', 'label' => esc_html__('Text shadow', 'dynamic-content-for-elementor'), 'selector' => '{{WRAPPER}} .dynamic-content-for-elementor-toolset', 'condition' => ['toolset_field_type' => ['textfield', 'url', 'image', 'phone', 'email', 'textarea', 'wysiwyg', 'date', 'numeric']]]);
        $this->end_controls_section();
    }
    protected function get_toolset_fields()
    {
        $fieldList = array();
        $fieldList[0] = esc_html__('Select the field...', 'dynamic-content-for-elementor');
        if (Helper::is_plugin_active('types')) {
            $toolset_groups = wpcf_admin_fields_get_groups();
            foreach ($toolset_groups as $group) {
                $options = array();
                $fields = wpcf_admin_fields_get_fields_by_group($group['id']);
                if (!\is_array($fields)) {
                    continue;
                }
                foreach ($fields as $field_key => $field) {
                    //
                    if (!empty($field['type'])) {
                        $a = array();
                        $a['group'] = $group['slug'];
                        $a['field'] = $field_key;
                        $a['type'] = $field['type'];
                        $index = wp_json_encode($a);
                        $options[wp_json_encode($a)] = $field['name'] . ' (' . $field['type'] . ')';
                    }
                    if (empty($options)) {
                        continue;
                    }
                }
                \array_push($fieldList, ['label' => $group['name'], 'options' => $options]);
            }
        }
        return $fieldList;
    }
    protected function safe_render()
    {
        $settings = $this->get_settings_for_display();
        if (empty($settings)) {
            return;
        }
        $post_id = get_the_ID();
        $use_bg = $settings['use_bg'];
        $wrap_effect_start = '<div class="mask"><div class="wrap-filters">';
        $wrap_effect_end = '</div></div>';
        $overlay_block = '';
        if ($settings['use_overlay'] == 'yes') {
            $overlay_block = '<div class="dce-overlay"></div>';
        }
        $overlay_hover_block = '<div class="dce-overlay_hover"></div>';
        $f = \json_decode($settings['toolset_field_list']);
        $html = '';
        switch ($f->type) {
            case 'textfield':
            case 'textarea':
                $f->value = types_render_field($f->field);
                $html = '<span class="edc-toolset">' . esc_html($f->value) . '</span>';
                if ($settings['toolset_text_before'] != '' || $settings['toolset_text_after'] != '') {
                    $html = '<span class="tx-before">' . wp_kses_post($settings['toolset_text_before']) . '</span>' . $html . '<span class="tx-after">' . wp_kses_post($settings['toolset_text_after']) . '</span>';
                }
                break;
            case 'wysiwyg':
                $f->value = types_render_field($f->field, array('suppress_filters' => \true));
                $html = $f->value;
                break;
            case 'url':
                $f->value = types_render_field($f->field);
                $text_url = '';
                if (\preg_match('/href="(.*?)" /', $f->value, $match) == 1) {
                    $url = $match[1];
                }
                if (isset($url)) {
                    $url = esc_url($url);
                }
                if (!empty($settings['toolset_url_custom_text'])) {
                    $text_url = wp_kses_post($settings['toolset_url_custom_text']);
                }
                if ($settings['toolset_url_enable'] && isset($url)) {
                    $html = '<a href="' . esc_url($url) . '" target="' . esc_attr($settings['toolset_url_target']) . '"> ' . $text_url . '</a>';
                } else {
                    $html = $text_url;
                }
                if ($settings['toolset_text_before'] != '' || $settings['toolset_text_after'] != '') {
                    $html = '<span class="tx-before">' . wp_kses_post($settings['toolset_text_before']) . '</span>' . $html . '<span class="tx-after">' . wp_kses_post($settings['toolset_text_after']) . '</span>';
                }
                break;
            case 'phone':
                $f->value = types_render_field($f->field);
                $text_number = $f->value;
                if (!empty($settings['toolset_phone_number_custom_text'])) {
                    $text_number = $settings['toolset_phone_number_custom_text'];
                }
                if (!empty($settings['toolset_phone_number_enable'])) {
                    // Preserve + in phone number for international format
                    $phone_number = \trim($f->value);
                    $phone_link = $phone_number;
                    if (\strpos($phone_number, '+') !== 0) {
                        // Remove all non-digit characters except +
                        $phone_link = \preg_replace('/[^0-9+]/', '', $phone_number);
                    }
                    $html = '<a href="tel:' . esc_attr($phone_link) . '"> ' . wp_kses_post($text_number) . '</a>';
                } else {
                    $html = wp_kses_post($text_number);
                }
                if ($settings['toolset_text_before'] != '' || $settings['toolset_text_after'] != '') {
                    $html = '<span class="tx-before">' . wp_kses_post($settings['toolset_text_before']) . '</span>' . $html . '<span class="tx-after">' . wp_kses_post($settings['toolset_text_after']) . '</span>';
                }
                break;
            case 'email':
                $f->value = types_render_field($f->field);
                if ($settings['toolset_email_target']) {
                    $html = wp_kses_post($f->value);
                } elseif (\preg_match('/href="mailto:(.*?)" /', $f->value, $match) == 1) {
                    $html = esc_html($match[1]);
                }
                if ($settings['toolset_text_before'] != '' || $settings['toolset_text_after'] != '') {
                    $html = '<span class="tx-before">' . wp_kses_post($settings['toolset_text_before']) . '</span>' . $html . '<span class="tx-after">' . wp_kses_post($settings['toolset_text_after']) . '</span>';
                }
                break;
            case 'image':
                $img_size = $settings['size_size'];
                $f->value = types_render_field($f->field);
                if (\preg_match('/src="(.*?)" /', $f->value, $match) == 1) {
                    $imgSrc = $match[1];
                }
                $img_id = Helper::get_image_id($imgSrc);
                $img_url = Group_Control_Image_Size::get_attachment_image_src($img_id, 'size', $settings);
                if (!$use_bg) {
                    $html = '<div class="toolset-image">' . $wrap_effect_start . '<img src="' . esc_url($img_url) . '" />' . $wrap_effect_end . $overlay_block . $overlay_hover_block . '</div>';
                } else {
                    $bg_featured_image = '<div class="toolset-image toolset-bg-image">' . $wrap_effect_start . '<figure class="dynamic-content-for-elementor-toolset-bg" style="background-image: url(\'' . esc_url($img_url) . '\'); background-repeat: no-repeat; background-size: cover;"></figure>' . $wrap_effect_end . $overlay_block . $overlay_hover_block . '</div>';
                    $html = $bg_featured_image;
                }
                break;
            case 'date':
                $f->value = types_render_field($f->field);
                if ($timestamp = types_render_field($f->field, array('format' => 'U', 'style' => 'text'))) {
                    switch ($settings['toolset_date_format']) {
                        case 'default':
                            $data = $f->value;
                            break;
                        case 'timestamp':
                            $data = $timestamp;
                            break;
                        case 'custom':
                            $data = \strftime($settings['toolset_date_custom_format'], $timestamp);
                            break;
                        default:
                            $data = \strftime($settings['toolset_date_format'], $timestamp);
                            break;
                    }
                    $html = '<span class="edc-toolset">' . wp_kses_post($data) . '</span>';
                    if ($settings['toolset_text_before'] != '' || $settings['toolset_text_after'] != '') {
                        $html = '<span class="tx-before">' . wp_kses_post($settings['toolset_text_before']) . '</span>' . $html . '<span class="tx-after">' . wp_kses_post($settings['toolset_text_after']) . '</span>';
                    }
                }
                break;
            case 'numeric':
                $f->value = types_render_field($f->field);
                $number = $f->value;
                if ($settings['toolset_numeric_currency'] && $settings['toolset_currency_symbol'] != '') {
                    if ($settings['toolset_currency_symbol_position'] == 'before') {
                        $number = $settings['toolset_currency_symbol'] . $number;
                    } else {
                        $number .= $settings['toolset_currency_symbol'];
                    }
                }
                $html = '<span class="edc-toolset">' . wp_kses_post($number) . '</span>';
                if ($settings['toolset_text_before'] != '' || $settings['toolset_text_after'] != '') {
                    $html = '<span class="tx-before">' . wp_kses_post($settings['toolset_text_before']) . '</span>' . $html . '<span class="tx-after">' . wp_kses_post($settings['toolset_text_after']) . '</span>';
                }
                break;
        }
        if ($settings['toolset_field_hide'] && empty($f->value) && !\Elementor\Plugin::$instance->editor->is_edit_mode()) {
            $html = '<style>' . $this->get_unique_selector() . '{display:none !important;} </style>';
        }
        switch ($f->type) {
            case 'code':
                $settings['html_tag'] = 'code';
                break;
            case 'image':
                $settings['html_tag'] = 'div';
                break;
            default:
                $settings['html_tag'] = 'div';
                break;
        }
        $animation_class = !empty($settings['hover_animation']) ? 'elementor-animation-' . $settings['hover_animation'] : '';
        $render = \sprintf('<%1$s class="dynamic-content-for-elementor-toolset %2$s">', \DynamicContentForElementor\Helper::validate_html_tag($settings['html_tag']), esc_attr($animation_class));
        $render .= $html;
        $render .= \sprintf('</%s>', \DynamicContentForElementor\Helper::validate_html_tag($settings['html_tag']));
        echo $render;
    }
}
