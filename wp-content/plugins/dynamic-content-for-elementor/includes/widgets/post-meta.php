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
use Elementor\Group_Control_Css_Filter;
use Elementor\Utils;
use DynamicContentForElementor\Helper;
use DynamicContentForElementor\Tokens;
// Exit if accessed directly
if (!\defined('ABSPATH')) {
    exit;
}
class PostMeta extends \DynamicContentForElementor\Widgets\WidgetPrototype
{
    /**
     * Register controls after check if this feature is only for admin
     *
     * @return void
     */
    protected function safe_register_controls()
    {
        $this->start_controls_section('section_content', ['label' => esc_html__('Field', 'dynamic-content-for-elementor')]);
        $this->add_control('dce_meta_key', ['label' => esc_html__('Meta Field', 'dynamic-content-for-elementor'), 'type' => 'ooo_query', 'placeholder' => esc_html__('Meta key or Name', 'dynamic-content-for-elementor'), 'label_block' => \true, 'query_type' => 'metas', 'object_type' => 'post', 'default' => '_wp_page_template']);
        $this->add_control('dce_meta_array', ['label' => esc_html__('Multiple postmeta', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'description' => esc_html__('Enable if post has many postmeta with same meta_key.', 'dynamic-content-for-elementor')]);
        $this->add_control('dce_meta_array_filter', ['label' => esc_html__('Filter occurrences', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'options' => ['all' => esc_html__('All', 'dynamic-content-for-elementor'), 'first' => esc_html__('First', 'dynamic-content-for-elementor'), 'last' => esc_html__('Last', 'dynamic-content-for-elementor')], 'default' => 'all', 'toggle' => \false, 'condition' => ['dce_meta_array!' => '']]);
        $this->end_controls_section();
        $this->start_controls_section('dce_meta_render', ['label' => esc_html__('Render mode', 'dynamic-content-for-elementor'), 'condition' => ['dce_meta_key!' => '']]);
        $this->add_control('dce_meta_type', ['label' => esc_html__('Render as', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'options' => [
            'dynamic' => esc_html__('AUTO', 'dynamic-content-for-elementor'),
            'custom' => esc_html__('CUSTOM', 'dynamic-content-for-elementor'),
            'id' => esc_html__('ID', 'dynamic-content-for-elementor'),
            'text' => esc_html__('Text', 'dynamic-content-for-elementor'),
            //'number' => esc_html__('Number', 'dynamic-content-for-elementor'),
            //'url' => esc_html__('Url', 'dynamic-content-for-elementor'),
            'button' => esc_html__('Button', 'dynamic-content-for-elementor'),
            'date' => esc_html__('Date', 'dynamic-content-for-elementor'),
            'image' => esc_html__('Image', 'dynamic-content-for-elementor'),
            'map' => esc_html__('Map', 'dynamic-content-for-elementor'),
            //'video' => esc_html__('Video oembed', 'dynamic-content-for-elementor'),
            'multiple' => esc_html__('Multiple (like Relationship, Select, Checkboxes, etc)', 'dynamic-content-for-elementor'),
            'repeater' => esc_html__('Repeater', 'dynamic-content-for-elementor'),
        ], 'default' => 'dynamic']);
        $this->add_control('dce_meta_raw', ['label' => esc_html__('Use Raw data', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'description' => esc_html__('Use value stored in postmeta, without any plugin modification.', 'dynamic-content-for-elementor')]);
        $this->add_control('dce_meta_custom', ['label' => esc_html__('Custom HTML', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::WYSIWYG, 'default' => '[META_VALUE]', 'placeholder' => '[META_VALUE]', 'condition' => ['dce_meta_type' => 'custom']]);
        $this->add_control('dce_meta_tag', ['label' => esc_html__('HTML Tag', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'options' => Helper::get_html_tags(['ul', 'ol'], \true), 'default' => 'div']);
        $this->end_controls_section();
        // REPEATER
        $this->start_controls_section('dce_meta_section_repeater', ['label' => esc_html__('Repeater', 'dynamic-content-for-elementor'), 'condition' => ['dce_meta_type' => 'repeater']]);
        $this->add_control('dce_meta_repeater', ['label' => esc_html__('HTML and Tokens', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::WYSIWYG, 'default' => '[ROW]', 'placeholder' => '[ROW]']);
        $this->end_controls_section();
        // MULTIPLE
        $this->start_controls_section('dce_meta_section_multiple', ['label' => esc_html__('Multiple values', 'dynamic-content-for-elementor'), 'condition' => ['dce_meta_type' => 'multiple']]);
        $this->add_control('dce_meta_multiple_tag', ['label' => esc_html__('HTML Tag', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'options' => Helper::get_html_tags(['li', 'custom'], \true)]);
        $this->add_control('dce_meta_multiple_id', ['label' => esc_html__('ID', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CHOOSE, 'options' => ['' => ['title' => esc_html__('None', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-file-o'], 'post' => ['title' => esc_html__('Post', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-file'], 'term' => ['title' => esc_html__('Taxonomy Term', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-tags'], 'user' => ['title' => esc_html__('User', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-user']], 'toggle' => \false, 'description' => esc_html__('Obtain object data from ID of the selected type. Enable if Meta data is the ID', 'dynamic-content-for-elementor'), 'condition' => ['dce_meta_multiple_tag' => 'custom']]);
        $this->add_control('dce_meta_multiple_custom', ['label' => esc_html__('Custom HTML', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::WYSIWYG, 'default' => '[SINGLE]', 'placeholder' => '[SINGLE]', 'description' => esc_html__('Type here your content, you can use HTML and Tokens.', 'dynamic-content-for-elementor'), 'condition' => ['dce_meta_multiple_tag' => 'custom']]);
        $this->add_control('dce_meta_multiple_separator', ['label' => esc_html__('Separator', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'condition' => ['dce_meta_multiple_tag!' => 'custom']]);
        $this->add_control('dce_meta_multiple_separator_last', ['label' => esc_html__('Not on last item', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'condition' => ['dce_meta_multiple_tag!' => 'custom', 'dce_meta_multiple_separator!' => '']]);
        $this->end_controls_section();
        // MAP
        $this->start_controls_section('dce_meta_section_map', ['label' => esc_html__('Map', 'dynamic-content-for-elementor'), 'condition' => ['dce_meta_type' => 'map']]);
        $this->add_control('dce_meta_map_zoom', ['label' => esc_html__('Zoom', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => 10], 'range' => ['px' => ['min' => 1, 'max' => 20]], 'separator' => 'before']);
        $this->add_responsive_control('dce_meta_map_height', ['label' => esc_html__('Height', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'range' => ['px' => ['min' => 40, 'max' => 1440]], 'selectors' => ['{{WRAPPER}} iframe' => 'height: {{SIZE}}{{UNIT}};']]);
        $this->add_control('dce_meta_map_prevent_scroll', ['label' => esc_html__('Prevent Scroll', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => 'yes', 'selectors' => ['{{WRAPPER}} iframe' => 'pointer-events: none;']]);
        $this->end_controls_section();
        // DATE
        $this->start_controls_section('dce_meta_section_date', ['label' => esc_html__('Date', 'dynamic-content-for-elementor'), 'condition' => ['dce_meta_type' => 'date']]);
        $this->add_control('dce_meta_date_format_source', ['label' => esc_html__('Source Format', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'description' => '<a target="_blank" href="https://www.php.net/manual/en/function.date.php">' . esc_html__('Use standard PHP format character', 'dynamic-content-for-elementor') . '</a>' . esc_html__(', you can also use "timestamp"', 'dynamic-content-for-elementor'), 'placeholder' => esc_html__('YmdHis, d/m/Y, m-d-y', 'dynamic-content-for-elementor')]);
        $this->add_control('dce_meta_date_format_display', ['label' => esc_html__('Display Format', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'placeholder' => 'Y/m/d H:i:s, d/m/Y, m-d-y']);
        $this->end_controls_section();
        // ID
        $this->start_controls_section('dce_meta_section_id', ['label' => esc_html__('ID', 'dynamic-content-for-elementor'), 'condition' => ['dce_meta_type' => 'id']]);
        $this->add_control('dce_meta_id_type', ['label' => esc_html__('Object Type', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HIDDEN, 'default' => 'post']);
        $this->add_control('dce_meta_id_render_type', ['label' => esc_html__('Content type', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CHOOSE, 'options' => ['simple' => ['title' => esc_html__('Simple', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-link'], 'text' => ['title' => esc_html__('Text', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-left'], 'template' => ['title' => esc_html__('Template', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-th-large']], 'toggle' => \false, 'default' => 'simple']);
        $this->add_control('dce_meta_id_render_type_template', ['label' => esc_html__('Render Template', 'dynamic-content-for-elementor'), 'type' => 'ooo_query', 'placeholder' => esc_html__('Template Name', 'dynamic-content-for-elementor'), 'label_block' => \true, 'query_type' => 'posts', 'object_type' => 'elementor_library', 'condition' => ['dce_meta_id_render_type' => 'template']]);
        $this->add_control('dce_meta_id_render_type_text', ['label' => esc_html__('Object HTML and Tokens', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::WYSIWYG, 'default' => '[post:thumb]<h4>[post:title|esc_html]</h4><p>[post:excerpt]</p><a class="btn btn-primary" href="[post:permalink]">READ MORE</a>', 'condition' => ['dce_meta_id_render_type' => 'text']]);
        $this->end_controls_section();
        // TEXT
        $this->start_controls_section('dce_meta_section_text', ['label' => esc_html__('Text', 'dynamic-content-for-elementor'), 'condition' => ['dce_meta_type' => 'text']]);
        $this->add_control('dce_meta_text_length', ['label' => esc_html__('Text Length', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::NUMBER, 'min' => 1]);
        $this->add_control('dce_meta_text_length_type', ['label' => esc_html__('Length Unit', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'default' => 'words', 'options' => ['words' => esc_html__('Words', 'dynamic-content-for-elementor'), 'charachters' => esc_html__('Characters', 'dynamic-content-for-elementor')], 'condition' => ['dce_meta_text_length!' => '']]);
        $this->add_control('dce_meta_text_ellipsis', ['label' => esc_html__('Text Ellipsis', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'description' => esc_html__('Will substitute the part of the content that is omitted in text.', 'dynamic-content-for-elementor'), 'default' => '&hellip;', 'condition' => ['dce_meta_text_length!' => '']]);
        $this->add_control('dce_meta_text_finish', ['label' => esc_html__('Finish', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'default' => 'exact', 'options' => ['exact' => esc_html__('Exact', 'dynamic-content-for-elementor'), 'exact_w_spaces' => esc_html__('Exact (count spaces as well)', 'dynamic-content-for-elementor'), 'word' => esc_html__('Word', 'dynamic-content-for-elementor'), 'sentence' => esc_html__('Sentence', 'dynamic-content-for-elementor')], 'condition' => ['dce_meta_text_length!' => '']]);
        $this->add_control('dce_meta_text_no_shortcode', ['label' => esc_html__('Remove Shortcode', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER]);
        $this->add_control('dce_meta_text_strip_tags', ['label' => esc_html__('Strip Tags', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER]);
        $this->add_control('dce_meta_text_allowed_tags', ['label' => esc_html__('Remove all tags except the following', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'placeholder' => 'a,b,strong,i', 'description' => esc_html__('Type a list of HTML tag to maintain separated by comma', 'dynamic-content-for-elementor'), 'label_block' => \true, 'condition' => ['dce_meta_text_strip_tags!' => '']]);
        $this->add_control('dce_meta_link', ['label' => esc_html__('Link', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CHOOSE, 'options' => ['' => ['title' => esc_html__('None', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-times'], 'post' => ['title' => esc_html__('Post', 'dynamic-content-for-elementor'), 'icon' => 'eicon-post-content'], 'custom' => ['title' => esc_html__('Custom', 'dynamic-content-for-elementor'), 'icon' => 'eicon-pencil']], 'toggle' => \false, 'default' => '', 'condition' => ['dce_meta_type' => 'text']]);
        $this->add_control('dce_meta_link_custom', ['label' => esc_html__('Custom Link', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::URL, 'dynamic' => ['active' => \true], 'placeholder' => esc_html__('https://your-link.com', 'dynamic-content-for-elementor'), 'default' => ['url' => '[option:home]'], 'condition' => ['dce_meta_type' => 'text', 'dce_meta_link' => 'custom']]);
        $this->end_controls_section();
        // IMAGE
        $this->start_controls_section('dce_meta_section_image', ['label' => esc_html__('Image', 'dynamic-content-for-elementor'), 'condition' => ['dce_meta_type' => 'image']]);
        $this->add_group_control(Group_Control_Image_Size::get_type(), ['name' => 'dce_meta_image_size']);
        $this->add_control('dce_meta_image_caption_source', ['label' => esc_html__('Caption', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'options' => ['none' => esc_html__('None', 'dynamic-content-for-elementor'), 'attachment' => esc_html__('Attachment Caption', 'dynamic-content-for-elementor'), 'custom' => esc_html__('Custom Caption', 'dynamic-content-for-elementor')], 'default' => 'none']);
        $this->add_control('dce_meta_image_caption', ['label' => esc_html__('Custom Caption', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'default' => '', 'placeholder' => esc_html__('Enter your image caption', 'dynamic-content-for-elementor'), 'condition' => ['dce_meta_image_caption_source' => 'custom'], 'dynamic' => ['active' => \true]]);
        $this->add_control('dce_meta_image_link_to', ['label' => esc_html__('Link', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'default' => 'none', 'options' => ['' => esc_html__('None', 'dynamic-content-for-elementor'), 'file' => esc_html__('Media File', 'dynamic-content-for-elementor'), 'post' => esc_html__('Post', 'dynamic-content-for-elementor'), 'custom' => esc_html__('Custom URL', 'dynamic-content-for-elementor')]]);
        $this->add_control('dce_meta_image_link', ['label' => esc_html__('Link', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::URL, 'dynamic' => ['active' => \true], 'placeholder' => esc_html__('https://your-link.com', 'dynamic-content-for-elementor'), 'condition' => ['dce_meta_image_link_to' => 'custom'], 'show_label' => \false]);
        $this->end_controls_section();
        // BUTTON
        $this->start_controls_section('dce_meta_button_section_button', ['label' => esc_html__('Button', 'dynamic-content-for-elementor'), 'condition' => ['dce_meta_type' => 'button']]);
        $this->add_control('dce_meta_button_type', ['label' => esc_html__('Type', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'default' => '', 'options' => ['' => esc_html__('Default', 'dynamic-content-for-elementor'), 'info' => esc_html__('Info', 'dynamic-content-for-elementor'), 'success' => esc_html__('Success', 'dynamic-content-for-elementor'), 'warning' => esc_html__('Warning', 'dynamic-content-for-elementor'), 'danger' => esc_html__('Danger', 'dynamic-content-for-elementor')], 'prefix_class' => 'elementor-button-']);
        $this->add_control('dce_meta_button_text', ['label' => esc_html__('Text', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'dynamic' => ['active' => \true], 'default' => esc_html__('Click here', 'dynamic-content-for-elementor'), 'placeholder' => esc_html__('[META_VALUE], [META_VALUE:title], [META_VALUE:get_the_title]', 'dynamic-content-for-elementor'), 'description' => esc_html__('You can use a mix of text, Tokens and META_VALUE data', 'dynamic-content-for-elementor')]);
        $this->add_control('dce_meta_button_link', ['label' => esc_html__('Link', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::URL, 'dynamic' => ['active' => \true], 'placeholder' => '[META_VALUE], [META_VALUE:url], [META_VALUE|get_permalink]', 'default' => ['url' => '#'], 'description' => esc_html__('You can use a mix of text, Tokens and META_VALUE data', 'dynamic-content-for-elementor')]);
        $this->add_control('dce_meta_button_size', ['label' => esc_html__('Size', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'default' => 'sm', 'options' => Helper::get_button_sizes(), 'style_transfer' => \true]);
        $this->add_control('dce_meta_button_icon', ['label' => esc_html__('Icon', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::ICON, 'label_block' => \true, 'default' => '']);
        $this->add_control('dce_meta_button_icon_align', ['label' => esc_html__('Icon Position', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'default' => 'left', 'options' => ['left' => esc_html__('Before', 'dynamic-content-for-elementor'), 'right' => esc_html__('After', 'dynamic-content-for-elementor')], 'condition' => ['dce_meta_button_icon!' => '']]);
        $this->add_control('dce_meta_button_icon_indent', ['label' => esc_html__('Icon Spacing', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'range' => ['px' => ['max' => 50]], 'condition' => ['dce_meta_button_icon!' => ''], 'selectors' => ['{{WRAPPER}} .elementor-button .elementor-align-icon-right' => 'margin-left: {{SIZE}}{{UNIT}};', '{{WRAPPER}} .elementor-button .elementor-align-icon-left' => 'margin-right: {{SIZE}}{{UNIT}};']]);
        $this->add_control('dce_meta_button_view', ['label' => esc_html__('View', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HIDDEN, 'default' => 'traditional']);
        $this->add_control('dce_meta_button_css_id', ['label' => esc_html__('Button ID', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'dynamic' => ['active' => \true], 'title' => esc_html__('Add your custom id WITHOUT the Pound key. e.g: my-id', 'dynamic-content-for-elementor'), 'label_block' => \false, 'description' => \sprintf(
            /* translators: %1$s: opening <code> tag, %2$s: closing </code> tag */
            esc_html__('Please make sure the ID is unique and not used elsewhere on the page this form is displayed. This field allows %1$sA-z 0-9%2$s & underscore chars without spaces.', 'dynamic-content-for-elementor'),
            '<code>',
            '</code>'
        ), 'separator' => 'before']);
        $this->end_controls_section();
        //* FALLBACK for NO RESULTS *//
        $this->start_controls_section('dce_meta_section_fallback', ['label' => esc_html__('Fallback', 'dynamic-content-for-elementor')]);
        $this->add_control('dce_meta_fallback', ['label' => esc_html__('Fallback Content', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'description' => esc_html__('If you want to show something when the field is empty null, void, false, or 0', 'dynamic-content-for-elementor')]);
        $this->add_control('dce_meta_fallback_zero', ['label' => esc_html__('Consider 0 as empty', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'condition' => ['dce_meta_fallback!' => '']]);
        $this->add_control('dce_meta_fallback_type', ['label' => esc_html__('Content type', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CHOOSE, 'options' => ['text' => ['title' => esc_html__('Text', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-left'], 'template' => ['title' => esc_html__('Template', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-th-large']], 'toggle' => \false, 'default' => 'text', 'condition' => ['dce_meta_fallback!' => '']]);
        $this->add_control('dce_meta_fallback_template', ['label' => esc_html__('Render Template', 'dynamic-content-for-elementor'), 'type' => 'ooo_query', 'placeholder' => esc_html__('Template Name', 'dynamic-content-for-elementor'), 'label_block' => \true, 'query_type' => 'posts', 'object_type' => 'elementor_library', 'description' => esc_html__('Use an Elementor Template as content, useful for complex structure.', 'dynamic-content-for-elementor'), 'condition' => ['dce_meta_fallback!' => '', 'dce_meta_fallback_type' => 'template']]);
        $this->add_control('dce_meta_fallback_text', ['label' => esc_html__('Text Fallback', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::WYSIWYG, 'default' => esc_html__('This field is empty.', 'dynamic-content-for-elementor'), 'condition' => ['dce_meta_fallback!' => '', 'dce_meta_fallback_type' => 'text']]);
        $this->add_control('dce_meta_fallback_autop', ['label' => esc_html__('Remove auto paragraph', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'condition' => ['dce_meta_fallback!' => '', 'dce_meta_fallback_type' => 'text']]);
        $this->end_controls_section();
        $this->start_controls_section('dce_meta_array_section', ['label' => esc_html__('Multiple postmeta', 'dynamic-content-for-elementor'), 'condition' => ['dce_meta_array!' => '']]);
        $this->add_control('dce_meta_array_fallback', ['label' => esc_html__('Fallback Content', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'description' => esc_html__('If you want to show something when this post meta is not found.', 'dynamic-content-for-elementor')]);
        $this->add_control('dce_meta_array_fallback_type', ['label' => esc_html__('Content type', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CHOOSE, 'options' => ['text' => ['title' => esc_html__('Text', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-left'], 'template' => ['title' => esc_html__('Template', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-th-large']], 'toggle' => \false, 'default' => 'text', 'condition' => ['dce_meta_array_fallback!' => '']]);
        $this->add_control('dce_meta_array_fallback_template', ['label' => esc_html__('Render Template', 'dynamic-content-for-elementor'), 'type' => 'ooo_query', 'placeholder' => esc_html__('Template Name', 'dynamic-content-for-elementor'), 'label_block' => \true, 'query_type' => 'posts', 'object_type' => 'elementor_library', 'description' => esc_html__('Use an Elementor Template as content, useful for complex structure', 'dynamic-content-for-elementor'), 'condition' => ['dce_meta_array_fallback!' => '', 'dce_meta_array_fallback_type' => 'template']]);
        $this->add_control('dce_meta_array_fallback_text', ['label' => esc_html__('Text Fallback', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::WYSIWYG, 'default' => esc_html__('This field is empty.', 'dynamic-content-for-elementor'), 'condition' => ['dce_meta_array_fallback!' => '', 'dce_meta_array_fallback_type' => 'text']]);
        $this->add_control('dce_meta_array_fallback_autop', ['label' => esc_html__('Remove AutoP', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'condition' => ['dce_meta_array_fallback!' => '', 'dce_meta_array_fallback_type' => 'text']]);
        $this->end_controls_section();
        /****************************** STYLE *********************************/
        $this->start_controls_section('dce_meta_section_style', ['label' => esc_html__('Style', 'dynamic-content-for-elementor'), 'tab' => Controls_Manager::TAB_STYLE]);
        $this->add_responsive_control('dce_meta_align', ['label' => esc_html__('Alignment', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CHOOSE, 'options' => ['left' => ['title' => esc_html__('Left', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-left'], 'center' => ['title' => esc_html__('Center', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-center'], 'right' => ['title' => esc_html__('Right', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-right'], 'justify' => ['title' => esc_html__('Justified', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-justify']], 'prefix_class' => 'elementor%s-align-', 'selectors' => ['{{WRAPPER}}' => 'text-align: {{VALUE}};']]);
        $this->add_control('dce_meta_color', ['label' => esc_html__('Text Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => [
            // Stronger selector to avoid section style from overwriting
            '{{WRAPPER}}, {{WRAPPER}} *' => 'color: {{VALUE}};',
        ]]);
        $this->add_group_control(Group_Control_Typography::get_type(), ['name' => 'dce_meta_typography', 'selector' => '{{WRAPPER}}, {{WRAPPER}} *']);
        $this->add_group_control(Group_Control_Text_Shadow::get_type(), ['name' => 'dce_meta_text_shadow', 'selector' => '{{WRAPPER}}, {{WRAPPER}} *']);
        $this->end_controls_section();
        // IMAGE
        $this->start_controls_section('section_style_image', ['label' => esc_html__('Image', 'dynamic-content-for-elementor'), 'tab' => Controls_Manager::TAB_STYLE, 'condition' => ['dce_meta_type' => 'image']]);
        $this->add_control('dce_meta_image_margin', ['label' => esc_html__('Margin', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::DIMENSIONS, 'selectors' => ['{{WRAPPER}} .elementor-image' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};']]);
        $this->add_responsive_control('width', ['label' => esc_html__('Width', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['unit' => '%'], 'tablet_default' => ['unit' => '%'], 'mobile_default' => ['unit' => '%'], 'size_units' => ['%', 'px', 'vw'], 'range' => ['%' => ['min' => 1, 'max' => 100], 'px' => ['min' => 1, 'max' => 1000], 'vw' => ['min' => 1, 'max' => 100]], 'selectors' => ['{{WRAPPER}} .elementor-image img' => 'width: {{SIZE}}{{UNIT}};']]);
        $this->add_responsive_control('space', ['label' => esc_html__('Max Width', 'dynamic-content-for-elementor') . ' (%)', 'type' => Controls_Manager::SLIDER, 'default' => ['unit' => '%'], 'tablet_default' => ['unit' => '%'], 'mobile_default' => ['unit' => '%'], 'size_units' => ['%'], 'range' => ['%' => ['min' => 1, 'max' => 100]], 'selectors' => ['{{WRAPPER}} .elementor-image img' => 'max-width: {{SIZE}}{{UNIT}};']]);
        $this->add_control('separator_panel_style', ['type' => Controls_Manager::DIVIDER, 'style' => 'thick']);
        $this->start_controls_tabs('image_effects');
        $this->start_controls_tab('normal', ['label' => esc_html__('Normal', 'dynamic-content-for-elementor')]);
        $this->add_control('opacity', ['label' => esc_html__('Opacity', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'range' => ['px' => ['max' => 1, 'min' => 0.1, 'step' => 0.01]], 'selectors' => ['{{WRAPPER}} .elementor-image img' => 'opacity: {{SIZE}};']]);
        $this->add_group_control(Group_Control_Css_Filter::get_type(), ['name' => 'css_filters', 'selector' => '{{WRAPPER}} .elementor-image img']);
        $this->end_controls_tab();
        $this->start_controls_tab('hover', ['label' => esc_html__('Hover', 'dynamic-content-for-elementor')]);
        $this->add_control('opacity_hover', ['label' => esc_html__('Opacity', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'range' => ['px' => ['max' => 1, 'min' => 0.1, 'step' => 0.01]], 'selectors' => ['{{WRAPPER}} .elementor-image:hover img' => 'opacity: {{SIZE}};']]);
        $this->add_group_control(Group_Control_Css_Filter::get_type(), ['name' => 'css_filters_hover', 'selector' => '{{WRAPPER}} .elementor-image:hover img']);
        $this->add_control('background_hover_transition', ['label' => esc_html__('Transition Duration', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'range' => ['px' => ['max' => 3, 'step' => 0.1]], 'selectors' => ['{{WRAPPER}} .elementor-image img' => 'transition-duration: {{SIZE}}s']]);
        $this->add_control('hover_animation', ['label' => esc_html__('Hover Animation', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HOVER_ANIMATION]);
        $this->end_controls_tab();
        $this->end_controls_tabs();
        $this->add_group_control(Group_Control_Border::get_type(), ['name' => 'image_border', 'selector' => '{{WRAPPER}} .elementor-image img', 'separator' => 'before']);
        $this->add_responsive_control('image_border_radius', ['label' => esc_html__('Border Radius', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => ['px', '%'], 'selectors' => ['{{WRAPPER}} .elementor-image img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};']]);
        $this->add_group_control(Group_Control_Box_Shadow::get_type(), ['name' => 'image_box_shadow', 'exclude' => ['box_shadow_position'], 'selector' => '{{WRAPPER}} .elementor-image img']);
        $this->end_controls_section();
        $this->start_controls_section('section_style_caption', ['label' => esc_html__('Caption', 'dynamic-content-for-elementor'), 'tab' => Controls_Manager::TAB_STYLE, 'condition' => ['dce_meta_image_caption_source!' => 'none']]);
        $this->add_control('caption_align', ['label' => esc_html__('Alignment', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CHOOSE, 'options' => ['left' => ['title' => esc_html__('Left', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-left'], 'center' => ['title' => esc_html__('Center', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-center'], 'right' => ['title' => esc_html__('Right', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-right'], 'justify' => ['title' => esc_html__('Justified', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-justify']], 'default' => '', 'selectors' => ['{{WRAPPER}} .widget-image-caption' => 'text-align: {{VALUE}};']]);
        $this->add_control('text_color', ['label' => esc_html__('Text Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .widget-image-caption' => 'color: {{VALUE}};']]);
        $this->add_control('caption_background_color', ['label' => esc_html__('Background Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .widget-image-caption' => 'background-color: {{VALUE}};']]);
        $this->add_group_control(Group_Control_Typography::get_type(), ['name' => 'caption_typography', 'selector' => '{{WRAPPER}} .widget-image-caption']);
        $this->add_group_control(Group_Control_Text_Shadow::get_type(), ['name' => 'caption_text_shadow', 'selector' => '{{WRAPPER}} .widget-image-caption']);
        $this->add_responsive_control('caption_space', ['label' => esc_html__('Spacing', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'range' => ['px' => ['min' => 0, 'max' => 100]], 'selectors' => ['{{WRAPPER}} .widget-image-caption' => 'margin-top: {{SIZE}}{{UNIT}};']]);
        $this->end_controls_section();
        // MAP
        $this->start_controls_section('dce_meta_section_map_style', ['label' => esc_html__('Map', 'dynamic-content-for-elementor'), 'tab' => Controls_Manager::TAB_STYLE, 'condition' => ['dce_meta_type' => 'map']]);
        $this->start_controls_tabs('dce_meta_map_filter');
        $this->start_controls_tab('dce_meta_map_normal', ['label' => esc_html__('Normal', 'dynamic-content-for-elementor')]);
        $this->add_group_control(Group_Control_Css_Filter::get_type(), ['name' => 'dce_meta_map_css_filters', 'selector' => '{{WRAPPER}} iframe']);
        $this->end_controls_tab();
        $this->start_controls_tab('dce_meta_map_hover', ['label' => esc_html__('Hover', 'dynamic-content-for-elementor')]);
        $this->add_group_control(Group_Control_Css_Filter::get_type(), ['name' => 'dce_meta_map_css_filters_hover', 'selector' => '{{WRAPPER}}:hover iframe']);
        $this->add_control('dce_meta_map_hover_transition', ['label' => esc_html__('Transition Duration', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'range' => ['px' => ['max' => 3, 'step' => 0.1]], 'selectors' => ['{{WRAPPER}} iframe' => 'transition-duration: {{SIZE}}s']]);
        $this->end_controls_tab();
        $this->end_controls_tabs();
        $this->end_controls_section();
        // BUTTON
        $this->start_controls_section('dce_meta_button_section_style', ['label' => esc_html__('Button', 'dynamic-content-for-elementor'), 'tab' => Controls_Manager::TAB_STYLE, 'condition' => ['dce_meta_type' => 'button']]);
        $this->add_group_control(Group_Control_Typography::get_type(), ['name' => 'dce_meta_button_typography', 'selector' => '{{WRAPPER}} a.elementor-button, {{WRAPPER}} .elementor-button']);
        $this->start_controls_tabs('tabs_button_style');
        $this->start_controls_tab('tab_button_normal', ['label' => esc_html__('Normal', 'dynamic-content-for-elementor')]);
        $this->add_control('dce_meta_button_text_color', ['label' => esc_html__('Text Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'default' => '', 'selectors' => ['{{WRAPPER}} a.elementor-button, {{WRAPPER}} .elementor-button' => 'color: {{VALUE}};']]);
        $this->add_control('dce_meta_button_background_color', ['label' => esc_html__('Background Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} a.elementor-button, {{WRAPPER}} .elementor-button' => 'background-color: {{VALUE}};']]);
        $this->end_controls_tab();
        $this->start_controls_tab('tab_button_hover', ['label' => esc_html__('Hover', 'dynamic-content-for-elementor')]);
        $this->add_control('dce_meta_button_hover_color', ['label' => esc_html__('Text Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} a.elementor-button:hover, {{WRAPPER}} .elementor-button:hover, {{WRAPPER}} a.elementor-button:focus, {{WRAPPER}} .elementor-button:focus' => 'color: {{VALUE}};']]);
        $this->add_control('button_background_hover_color', ['label' => esc_html__('Background Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} a.elementor-button:hover, {{WRAPPER}} .elementor-button:hover, {{WRAPPER}} a.elementor-button:focus, {{WRAPPER}} .elementor-button:focus' => 'background-color: {{VALUE}};']]);
        $this->add_control('button_hover_border_color', ['label' => esc_html__('Border Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'condition' => ['border_border!' => ''], 'selectors' => ['{{WRAPPER}} a.elementor-button:hover, {{WRAPPER}} .elementor-button:hover, {{WRAPPER}} a.elementor-button:focus, {{WRAPPER}} .elementor-button:focus' => 'border-color: {{VALUE}};']]);
        $this->add_control('dce_meta_button_hover_animation', ['label' => esc_html__('Hover Animation', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HOVER_ANIMATION]);
        $this->end_controls_tab();
        $this->end_controls_tabs();
        $this->add_group_control(Group_Control_Border::get_type(), ['name' => 'dce_meta_button_border', 'selector' => '{{WRAPPER}} .elementor-button', 'separator' => 'before']);
        $this->add_control('dce_meta_button_border_radius', ['label' => esc_html__('Border Radius', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => ['px', '%'], 'selectors' => ['{{WRAPPER}} a.elementor-button, {{WRAPPER}} .elementor-button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};']]);
        $this->add_group_control(Group_Control_Box_Shadow::get_type(), ['name' => 'button_box_shadow', 'selector' => '{{WRAPPER}} .elementor-button']);
        $this->add_responsive_control('dce_meta_button_text_padding', ['label' => esc_html__('Padding', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => ['px', 'em', '%'], 'selectors' => ['{{WRAPPER}} a.elementor-button, {{WRAPPER}} .elementor-button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'], 'separator' => 'before']);
        $this->end_controls_section();
        // Multiple POSTMETA
        $this->start_controls_section('section_style_array', ['label' => esc_html__('Multiple PostMeta', 'dynamic-content-for-elementor'), 'tab' => Controls_Manager::TAB_STYLE, 'condition' => ['dce_meta_array!' => '']]);
        $this->add_responsive_control('dce_meta_array_margin', ['label' => esc_html__('Margin', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::DIMENSIONS, 'selectors' => ['{{WRAPPER}} .dce-meta-value' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};']]);
        $this->add_responsive_control('dce_meta_array_padding', ['label' => esc_html__('Padding', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::DIMENSIONS, 'selectors' => ['{{WRAPPER}} .dce-meta-value' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};']]);
        $this->add_group_control(Group_Control_Border::get_type(), ['name' => 'dce_meta_array_border', 'selector' => '{{WRAPPER}} .dce-meta-value', 'separator' => 'before']);
        $this->add_control('dce_meta_array_border_radius', ['label' => esc_html__('Border Radius', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => ['px', '%'], 'selectors' => ['{{WRAPPER}} .dce-meta-value' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};']]);
        $this->add_group_control(Group_Control_Box_Shadow::get_type(), ['name' => 'array_box_shadow', 'selector' => '{{WRAPPER}} .dce-meta-value']);
        $this->add_control('array_css_classes', ['label' => esc_html__('CSS Classes', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'dynamic' => ['active' => \true], 'title' => esc_html__('Add your custom class WITHOUT the dot. e.g: my-class', 'dynamic-content-for-elementor')]);
        $this->end_controls_section();
    }
    protected function safe_render()
    {
        $settings = $this->get_settings_for_display();
        if (empty($settings) || empty($settings['dce_meta_key'])) {
            return;
        }
        $post_id = Helper::get_the_id();
        $id_page = $post_id;
        $post_type = get_post_type($id_page);
        $meta_key = $settings['dce_meta_key'];
        $meta_name = Helper::get_post_meta_name($meta_key);
        $meta_values = get_post_meta($post_id, $meta_key);
        $meta_tag = Helper::validate_html_tag($settings['dce_meta_tag']);
        if (\count($meta_values) > 1 && !empty($settings['dce_meta_array'])) {
            if ($settings['dce_meta_array_filter'] && $settings['dce_meta_array_filter'] != 'all') {
                if ($settings['dce_meta_array_filter'] == 'first') {
                    $meta_values = array(\reset($meta_values));
                }
                if ($settings['dce_meta_array_filter'] == 'last') {
                    $meta_values = array(\end($meta_values));
                }
            }
        }
        $render_type = $settings['dce_meta_type'];
        if (!empty($meta_values)) {
            foreach ($meta_values as $mkey => $meta_value) {
                $original_type = $meta_type = Helper::get_meta_type($meta_key, $meta_value);
                if ('dynamic' === $render_type) {
                    $render_type = $original_type;
                }
                if ('elementor_library' === $post_type && !$meta_value) {
                    switch ($original_type) {
                        case 'text':
                            $meta_value = 'This is a ACF text';
                            break;
                        case 'textarea':
                            $meta_value = 'This is a textarea. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nulla placerat faucibus ultrices. Proin tristique augue turpis.';
                            break;
                        case 'select':
                            $meta_value = 'Select a field';
                            break;
                        case 'wysiwyg':
                            $meta_value = 'This is a wysiwyg text. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nulla placerat faucibus ultrices. Proin tristique augue turpis. Phasellus accumsan nunc dui, eget mollis nibh fringilla at. Aliquam ante enim, mattis vel mi porttitor, efficitur dapibus turpis. Donec quis ipsum nisl. Sed elit sem, lobortis id erat et, tristique dignissim nisi. Donec egestas nunc tellus, sed vestibulum ex finibus sed.';
                            break;
                        case 'number':
                            $meta_value = 23;
                            break;
                    }
                }
                if (!$settings['dce_meta_raw']) {
                    // ACF support
                    if (Helper::is_plugin_active('acf') && \function_exists('get_field')) {
                        $meta_value_enchanted = \get_field($meta_key, $post_id);
                        if ($meta_value_enchanted) {
                            $meta_value = $meta_value_enchanted;
                        }
                    }
                    // PODS support
                    if (Helper::is_plugin_active('pods') && \function_exists('pods_field_display')) {
                        $meta_value_enchanted = \pods_field_display($meta_key, $post_id);
                        if ($meta_value_enchanted) {
                            $meta_value = wp_kses_post($meta_value_enchanted);
                        }
                    }
                }
                if ($original_type == 'taxonomy' && $render_type == 'multiple') {
                    $terms = array();
                    if (\is_array($meta_value) && !empty($meta_value)) {
                        foreach ($meta_value as $atermid) {
                            $terms[] = get_term($atermid);
                        }
                        $meta_value = $terms;
                    }
                }
                if ($original_type == 'user' && \is_numeric($meta_value)) {
                    $meta_value = get_user_by('ID', $meta_value);
                }
                if ($original_type == 'page_link' && \is_numeric($meta_value)) {
                    $meta_value = get_permalink($meta_value);
                }
                if ($original_type == 'relationship') {
                    $posts = array();
                    if (\is_array($meta_value) && !empty($meta_value)) {
                        foreach ($meta_value as $apostid) {
                            if (\is_numeric($apostid)) {
                                $posts[] = get_post($apostid);
                            }
                        }
                        $meta_value = $posts;
                    }
                }
                if (\is_numeric($meta_value) && $render_type == 'id') {
                    $meta_value = \intval($meta_value);
                    if ($settings['dce_meta_id_type'] == 'post') {
                        $meta_value = get_post($meta_value);
                    }
                    if ($settings['dce_meta_id_type'] == 'term') {
                        $meta_value = get_term($meta_value);
                    }
                    if ($settings['dce_meta_id_type'] == 'user') {
                        $meta_value = get_user_by('ID', $meta_value);
                    }
                }
                switch ($render_type) {
                    case 'custom':
                        $txt = Tokens::do_tokens($settings['dce_meta_custom']);
                        $meta_html = Tokens::replace_var_tokens($txt, 'META_VALUE', $meta_value);
                        $meta_html = do_shortcode($meta_html);
                        break;
                    case 'boolean':
                    case 'true_false':
                        if ($meta_value) {
                            $meta_html = esc_html__('yes', 'dynamic-content-for-elementor');
                        } else {
                            $meta_html = esc_html__('no', 'dynamic-content-for-elementor');
                        }
                        break;
                    case 'multiple':
                    case 'checkboxes':
                    case 'radio':
                    case 'select':
                        $meta_html = '';
                        if (!empty($meta_value)) {
                            if (!\is_array($meta_value)) {
                                $meta_value = array($meta_value);
                            }
                            $i = 1;
                            foreach ($meta_value as $avalue) {
                                if ($settings['dce_meta_multiple_tag'] && $settings['dce_meta_multiple_tag'] != 'custom') {
                                    $meta_html .= '<' . Helper::validate_html_tag($settings['dce_meta_multiple_tag']) . ' class="dce-meta-multiple">';
                                }
                                if ($settings['dce_meta_multiple_tag'] == 'custom') {
                                    $txt = Tokens::do_tokens($settings['dce_meta_multiple_custom']);
                                    if ($settings['dce_meta_multiple_id']) {
                                        $avalue = \intval($avalue);
                                        if ($avalue) {
                                            $txt = \str_replace('[SINGLE', '[' . $settings['dce_meta_multiple_id'], $txt);
                                            $txt = \str_replace(']', '|' . $avalue . ']', $txt);
                                        }
                                    }
                                    $txt = Tokens::do_tokens($txt);
                                    $meta_html .= Tokens::replace_var_tokens($txt, 'SINGLE', $avalue);
                                } else {
                                    $meta_html .= Helper::to_readable_string($avalue);
                                    if ($i < \count($meta_value)) {
                                        $meta_html .= wp_kses_post($settings['dce_meta_multiple_separator']);
                                    }
                                }
                                if ($settings['dce_meta_multiple_tag'] && $settings['dce_meta_multiple_tag'] != 'custom') {
                                    $meta_html .= '</' . Helper::validate_html_tag($settings['dce_meta_multiple_tag']) . '>';
                                }
                                ++$i;
                            }
                        }
                        break;
                    case 'email':
                        $meta_html = '<a href="mailto:' . esc_attr($meta_value) . '">' . esc_html($meta_value) . '</a>';
                        break;
                    case 'tel':
                    case 'phone':
                        $meta_html = '<a href="tel:' . esc_attr($meta_value) . '">' . esc_html($meta_value) . '</a>';
                        break;
                    case 'skype':
                        $meta_html = '<a href="skype:' . esc_attr($meta_value['skypename']) . '">' . esc_html($meta_value['skypename']) . '</a>';
                        break;
                    case 'number':
                    case 'numeric':
                    case 'currency':
                        $meta_html = esc_html($meta_value);
                        break;
                    case 'audio':
                        $ext = \pathinfo($meta_value, \PATHINFO_EXTENSION);
                        if (\in_array($ext, array('mp3', 'm4a', 'ogg', 'wav', 'wma'))) {
                            $meta_html = do_shortcode('[audio src="' . $meta_value . '"]');
                        }
                        break;
                    case 'video':
                        $ext = \pathinfo($meta_value, \PATHINFO_EXTENSION);
                        if (\in_array($ext, array('mp4', 'm4v', 'webm', 'ogv', 'wmv', 'flv'))) {
                            $meta_html = do_shortcode('[video src="' . $meta_value . '"]');
                        }
                        break;
                    case 'gallery':
                    case 'image':
                        if (\is_array($meta_value)) {
                            $meta_html = '';
                            if (!empty($meta_value)) {
                                foreach ($meta_value as $aimg) {
                                    $meta_html .= $this->_image($aimg, $post_id, $settings);
                                }
                            }
                        } else {
                            $meta_html = $this->_image($meta_value, $post_id, $settings);
                        }
                        break;
                    case 'youtube':
                    case 'embed':
                        $meta_html = do_shortcode('[embed]' . $meta_value . '[/embed]');
                        break;
                    case 'date':
                    case 'date_picker':
                    case 'date_time_picker':
                    case 'datetime':
                    case 'time':
                    case 'time_picker':
                        $format_display = 'Y/m/d H:i:s';
                        if ($settings['dce_meta_date_format_display']) {
                            $format_display = $settings['dce_meta_date_format_display'];
                        }
                        if ($settings['dce_meta_date_format_source']) {
                            if ($settings['dce_meta_date_format_source'] == 'timestamp') {
                                $timestamp = $meta_value;
                            } else {
                                if (\is_array($meta_value)) {
                                    // ACF Repeater
                                    $meta_value = \reset($meta_value);
                                    if (\is_array($meta_value)) {
                                        $meta_value = \reset($meta_value);
                                    }
                                }
                                $d = \DateTime::createFromFormat($settings['dce_meta_date_format_source'], $meta_value);
                                if ($d) {
                                    $timestamp = $d->getTimestamp();
                                } else {
                                    $timestamp = \strtotime($meta_value);
                                }
                            }
                        } else {
                            $timestamp = \strtotime($meta_value);
                        }
                        $meta_html = date_i18n($format_display, $timestamp);
                        break;
                    case 'file':
                        if (\is_object($meta_value)) {
                            $meta_html = '<a href="' . esc_url($meta_value->guid) . '">' . esc_html($meta_value->post_title) . '</a>';
                        }
                        if (\is_array($meta_value) && isset($meta_value['guid']) && isset($meta_value['post_title'])) {
                            $meta_html = '<a href="' . esc_url($meta_value['guid']) . '">' . esc_html($meta_value['post_title']) . '</a>';
                        }
                        if (\is_array($meta_value) && isset($meta_value['url']) && isset($meta_value['name'])) {
                            $meta_html = '<a href="' . esc_url($meta_value['url']) . '">' . esc_html($meta_value['name']) . '</a>';
                        }
                        if (\is_string($meta_value)) {
                            $meta_html = '<a href="' . esc_url($meta_value) . '">' . esc_html(\basename($meta_value)) . '</a>';
                        }
                        break;
                    case 'color':
                    case 'color_picker':
                    case 'colorpicker':
                        $meta_html = $meta_value;
                        break;
                    case 'google_map':
                        $map_url = 'https://www.google.com/maps/@' . \rawurlencode($meta_value['lat']) . ',' . \rawurlencode($meta_value['lng']) . ',15z';
                        $map_label = !empty($meta_value['address']) ? $meta_value['address'] : $meta_value['lat'] . ',' . $meta_value['lng'];
                        $meta_html = '<a href="' . esc_url($map_url) . '">' . esc_html($map_label) . '</a>';
                        break;
                    case 'map':
                        $meta_html = $this->_map($meta_value, $settings);
                        break;
                    case 'post_object':
                        $rel_post = get_post($meta_value);
                        $meta_html = '<a href="' . esc_url((string) get_permalink($meta_value)) . '">' . esc_html($rel_post->post_title) . '</a>';
                        break;
                    case 'id':
                        $meta_html = $object_id = $meta_value;
                        if (!$settings['dce_meta_id_render_type'] || $settings['dce_meta_id_render_type'] == 'simple') {
                            // POST
                            if (\is_object($meta_value) && $settings['dce_meta_id_type'] == 'post') {
                                $meta_html = '<a href="' . esc_url((string) get_permalink($meta_value->ID)) . '">' . esc_html($meta_value->post_title) . '</a>';
                                $object_id = $meta_value->ID;
                            }
                            // TAX
                            if (\is_object($meta_value) && $settings['dce_meta_id_type'] == 'term') {
                                $term_link = get_term_link($meta_value->term_id);
                                $meta_html = '<a href="' . esc_url(is_wp_error($term_link) ? '' : $term_link) . '">' . esc_html($meta_value->name) . '</a>';
                                $object_id = $meta_value->term_id;
                            }
                            // USER
                            if (\is_object($meta_value) && $settings['dce_meta_id_type'] == 'user') {
                                $meta_html = '<a href="' . esc_url(get_author_posts_url($meta_value->ID)) . '">' . esc_html($meta_value->display_name) . '</a>';
                                $object_id = $meta_value->ID;
                            }
                        }
                        if ($settings['dce_meta_id_type'] == 'post') {
                            if (\is_object($meta_value)) {
                                $object_id = $meta_value->ID;
                            }
                            if ($settings['dce_meta_id_render_type'] == 'text') {
                                global $post;
                                $original_post = $post;
                                $post = get_post($object_id);
                                $meta_html = Tokens::do_tokens($settings['dce_meta_id_render_type_text']);
                                $post = $original_post;
                            }
                            if ($settings['dce_meta_id_render_type'] == 'template') {
                                $template_system = \DynamicContentForElementor\Plugin::instance()->template_system;
                                $meta_html = $template_system->build_elementor_template_special(['id' => $settings['dce_meta_id_render_type_template'], 'post_id' => $object_id]);
                            }
                        }
                        break;
                    case 'pick':
                        $meta_html = $meta_value;
                        // POST
                        if (\is_array($meta_value) && isset($meta_value['ID']) && isset($meta_value['post_title'])) {
                            $meta_html = '<a href="' . esc_url((string) get_permalink($meta_value['ID'])) . '">' . esc_html($meta_value['post_title']) . '</a>';
                        }
                        // TAX
                        if (\is_array($meta_value) && isset($meta_value['term_id']) && isset($meta_value['name'])) {
                            $term_link = get_term_link($meta_value['term_id']);
                            $meta_html .= '<a href="' . esc_url(is_wp_error($term_link) ? '' : $term_link) . '">' . esc_html($meta_value['name']) . '</a>';
                        }
                        // USER
                        if (\is_array($meta_value) && isset($meta_value['ID']) && isset($meta_value['display_name'])) {
                            $meta_html = '<a href="' . esc_url(get_author_posts_url($meta_value['ID'])) . '">' . esc_html($meta_value['display_name']) . '</a>';
                        }
                        break;
                    case 'link':
                        $meta_html = '<a href="' . esc_url($meta_value['url']) . '"' . (!empty($meta_value['target']) ? ' target="' . esc_attr($meta_value['target']) . '"' : '') . '>' . wp_kses_post($meta_value['title']) . '</a>';
                        break;
                    case 'url':
                    case 'website':
                        $parts = \explode('/', $meta_value);
                        if (isset($parts[2])) {
                            $label = $parts[2];
                        } else {
                            $parts = \explode(' [', $meta_name);
                            \array_pop($parts);
                            $label = \implode(' [', $parts);
                        }
                        $meta_html = '<a href="' . esc_url($meta_value) . '">' . esc_html($label) . '</a>';
                        break;
                    case 'button':
                        $meta_html = $this->_button($meta_value, $settings);
                        break;
                    case 'taxonomy':
                        $meta_html = '';
                        if (\is_array($meta_value) && !empty($meta_value)) {
                            foreach ($meta_value as $atermid) {
                                $aterm = get_term($atermid);
                                $term_link = get_term_link($aterm);
                                $meta_html .= '<a href="' . esc_url(is_wp_error($term_link) ? '' : $term_link) . '">' . esc_html($aterm->name) . '</a>';
                                if ($atermid !== \end($meta_value)) {
                                    $meta_html .= ', ';
                                }
                            }
                        }
                        break;
                    case 'relationship':
                        $meta_html = '';
                        if (\is_array($meta_value) && !empty($meta_value)) {
                            foreach ($meta_value as $apost) {
                                $meta_html .= '<a href="' . esc_url((string) get_permalink($apost)) . '">' . esc_html($apost->post_title) . '</a>';
                                if ($apost !== \end($meta_value)) {
                                    $meta_html .= ', ';
                                }
                            }
                        }
                        break;
                    case 'user':
                        if (\is_array($meta_value)) {
                            $meta_html = '<a href="' . esc_url(get_author_posts_url($meta_value['ID'])) . '">' . esc_html($meta_value['display_name']) . '</a>';
                        }
                        if (\is_object($meta_value)) {
                            $meta_html = '<a href="' . esc_url(get_author_posts_url($meta_value->ID)) . '">' . esc_html($meta_value->display_name) . '</a>';
                        }
                        break;
                    case 'repeater':
                        $meta_html = '';
                        if (\is_array($meta_value)) {
                            if (!empty($meta_value)) {
                                foreach ($meta_value as $arow) {
                                    $meta_html .= Tokens::replace_var_tokens($settings['dce_meta_repeater'], 'ROW', $arow);
                                }
                            }
                        }
                        break;
                    case 'code':
                        $meta_html = '<pre><code>' . \htmlentities($meta_value) . '</code></pre>';
                        break;
                    case 'text':
                        $meta_html = $meta_value;
                        // remove shortcodes
                        if ($settings['dce_meta_text_no_shortcode']) {
                            $meta_html = strip_shortcodes($meta_html);
                            $meta_html = Helper::vc_strip_shortcodes($meta_html);
                        }
                        // Strip HTML if $allowed_tags_option is set to 'remove_all_tags_except'
                        if ($settings['dce_meta_text_strip_tags']) {
                            $allowed_tags = Helper::str_to_array(',', $settings['dce_meta_text_allowed_tags'], 'strtolower');
                            if (!empty($allowed_tags)) {
                                $tag_string = '<' . \implode('><', $allowed_tags) . '>';
                            } else {
                                $tag_string = '';
                            }
                            $meta_html = \strip_tags($meta_html, $tag_string);
                        }
                        // Create the excerpt
                        if ($settings['dce_meta_text_length']) {
                            $meta_html = Helper::text_reduce($meta_html, $settings['dce_meta_text_length'], $settings['dce_meta_text_length_type'], $settings['dce_meta_text_finish']);
                            $meta_html = $meta_html . $settings['dce_meta_text_ellipsis'];
                        }
                        if ($settings['dce_meta_link']) {
                            if ($settings['dce_meta_link'] == 'post') {
                                $link = get_permalink($post_id);
                                $this->add_render_attribute('meta_link', 'href', $link);
                            }
                            if ($settings['dce_meta_link'] == 'custom') {
                                $link = $settings['dce_meta_link_custom']['url'];
                                if (!empty($settings['dce_meta_link_custom']['url'])) {
                                    $this->add_render_attribute('meta_link', 'href', $settings['dce_meta_link_custom']['url']);
                                    if ($settings['dce_meta_link_custom']['is_external']) {
                                        $this->add_render_attribute('meta_link', 'target', '_blank');
                                    }
                                    if ($settings['dce_meta_link_custom']['nofollow']) {
                                        $this->add_render_attribute('meta_link', 'rel', 'nofollow');
                                    }
                                }
                            }
                            $meta_html = '<a ' . $this->get_render_attribute_string('meta_link') . '>' . $meta_html . '</a>';
                        }
                        break;
                    case 'textarea':
                        $meta_html = \nl2br(wp_kses_post($meta_value));
                        break;
                    case 'textfield':
                    case 'wysiwyg':
                    case 'plugin':
                    default:
                        $meta_html = $meta_value;
                }
                if (isset($meta_html)) {
                    $meta_html = do_shortcode($meta_html);
                    // if text contain an extra shortcode
                }
                echo '<div class="' . esc_attr('dce-meta-value ' . $settings['array_css_classes'] . ' dce-meta-field__' . $settings['dce_meta_key']) . '">';
                if ($settings['dce_meta_tag']) {
                    echo '<' . $meta_tag . ' class="dce-meta-wrapper">';
                }
                // FALLBACK
                if ($meta_value == '' || $meta_value === \false || $meta_value == 'false' || $meta_value === null || $meta_value === 'NULL' || $settings['dce_meta_fallback_zero'] && ($meta_value == 0 || $meta_value == '0')) {
                    if (isset($settings['dce_meta_fallback']) && $settings['dce_meta_fallback']) {
                        if (isset($settings['dce_meta_fallback_type']) && $settings['dce_meta_fallback_type'] == 'template') {
                            $template_system = \DynamicContentForElementor\Plugin::instance()->template_system;
                            echo $template_system->build_elementor_template_special(['id' => $settings['dce_meta_fallback_template']]);
                        } else {
                            $fallback_content = $settings['dce_meta_fallback_text'];
                            if ($settings['dce_meta_fallback_autop']) {
                                $fallback_content = Helper::strip_tag($fallback_content, 'p');
                            }
                            echo do_shortcode($fallback_content);
                        }
                    }
                } else {
                    // PRINT RESULT HTML
                    echo Helper::to_readable_string($meta_html);
                }
                if ($settings['dce_meta_tag']) {
                    echo '</' . $meta_tag . '>';
                }
                echo '</div>';
            }
        } elseif ($settings['dce_meta_array']) {
            if (isset($settings['dce_meta_array_fallback']) && $settings['dce_meta_array_fallback']) {
                if (isset($settings['dce_meta_array_fallback_type']) && $settings['dce_meta_array_fallback_type'] == 'template') {
                    $template_system = \DynamicContentForElementor\Plugin::instance()->template_system;
                    echo $template_system->build_elementor_template_special(['id' => $settings['dce_meta_array_fallback_template']]);
                } else {
                    $fallback_content = $settings['dce_meta_array_fallback_text'];
                    if ($settings['dce_meta_array_fallback_autop']) {
                        $fallback_content = Helper::strip_tag($fallback_content, 'p');
                    }
                    echo do_shortcode($fallback_content);
                }
            }
        } elseif (isset($settings['dce_meta_fallback']) && $settings['dce_meta_fallback']) {
            if (isset($settings['dce_meta_fallback_type']) && $settings['dce_meta_fallback_type'] == 'template') {
                $template_system = \DynamicContentForElementor\Plugin::instance()->template_system;
                echo $template_system->build_elementor_template_special(['id' => $settings['dce_meta_fallback_template']]);
            } else {
                $fallback_content = $settings['dce_meta_fallback_text'];
                if ($settings['dce_meta_fallback_autop']) {
                    $fallback_content = Helper::strip_tag($fallback_content, 'p');
                }
                echo do_shortcode($fallback_content);
            }
        }
    }
    public function _map($meta_value, $settings = null)
    {
        $address = $meta_value;
        if (\is_array($meta_value)) {
            if (!empty($meta_value['address'])) {
                $address = $meta_value['address'];
                if (!empty($meta_value['lat']) && !empty($meta_value['lng'])) {
                    $address = $meta_value['lat'] . ',' . $meta_value['lng'];
                }
            }
        }
        if (0 === absint($settings['dce_meta_map_zoom']['size'])) {
            $settings['zoom']['size'] = 10;
        }
        return '<div class="elementor-custom-embed"><iframe frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="https://maps.google.com/maps?q=' . \rawurlencode($address) . '&amp;t=m&amp;z=' . absint($settings['dce_meta_map_zoom']['size']) . '&amp;output=embed&amp;iwloc=near" aria-label="' . esc_attr($address) . '"></iframe></div>';
    }
    public function _button($meta_value, $settings = null)
    {
        $this->add_render_attribute('wrapper', 'class', 'elementor-button-wrapper');
        if (!empty($settings['dce_meta_button_link']['url'])) {
            $url = $settings['dce_meta_button_link']['url'];
            $url = Tokens::replace_var_tokens($url, 'META_VALUE', $meta_value);
            $url = Tokens::do_tokens($url);
            $this->add_render_attribute('button', 'href', $url);
            $this->add_render_attribute('button', 'class', 'elementor-button-link');
            if ($settings['dce_meta_button_link']['is_external']) {
                $this->add_render_attribute('button', 'target', '_blank');
            }
            if ($settings['dce_meta_button_link']['nofollow']) {
                $this->add_render_attribute('button', 'rel', 'nofollow');
            }
        }
        $this->add_render_attribute('button', 'class', 'elementor-button');
        $this->add_render_attribute('button', 'role', 'button');
        if (!empty($settings['dce_meta_button_css_id'])) {
            $id = $settings['dce_meta_button_css_id'];
            $id = Tokens::replace_var_tokens($id, 'META_VALUE', $meta_value);
            $id = Tokens::do_tokens($id);
            $this->add_render_attribute('button', 'id', $id);
        }
        if (!empty($settings['dce_meta_button_size'])) {
            $this->add_render_attribute('button', 'class', 'elementor-size-' . $settings['dce_meta_button_size']);
        }
        if ($settings['dce_meta_button_hover_animation']) {
            $this->add_render_attribute('button', 'class', 'elementor-animation-' . $settings['dce_meta_button_hover_animation']);
        }
        $this->add_render_attribute(['content-wrapper' => ['class' => 'elementor-button-content-wrapper'], 'icon-align' => ['class' => ['elementor-button-icon', 'elementor-align-icon-' . $settings['dce_meta_button_icon_align']]], 'text' => ['class' => 'elementor-button-text']]);
        $this->add_inline_editing_attributes('text', 'none');
        $txt = $settings['dce_meta_button_text'];
        $txt = Tokens::replace_var_tokens($txt, 'META_VALUE', $meta_value);
        $txt = Tokens::do_tokens($txt);
        $meta_html = '<div ' . $this->get_render_attribute_string('wrapper') . '>';
        $meta_html .= '<a ' . $this->get_render_attribute_string('button') . '>';
        $meta_html .= '<span ' . $this->get_render_attribute_string('content-wrapper') . '>';
        if (!empty($settings['dce_meta_button_icon'])) {
            $meta_html .= '<span ' . $this->get_render_attribute_string('icon-align') . '>';
            $meta_html .= '<i class="' . esc_attr($settings['dce_meta_button_icon']) . '" aria-hidden="true"></i>';
            $meta_html .= '</span>';
        }
        $meta_html .= '<span ' . $this->get_render_attribute_string('text') . '>';
        $meta_html .= '</span>';
        $meta_html .= '</span>';
        $meta_html .= $txt;
        $meta_html .= '</a>';
        $meta_html .= '</div>';
        return $meta_html;
    }
    public function _image($meta_value, $post_id = null, $settings = null)
    {
        if (!$post_id) {
            $post_id = get_the_ID();
        }
        $meta_html = '';
        // URL
        $image_id = 0;
        if (\is_array($meta_value)) {
            // ACF
            if (isset($meta_value['ID'])) {
                $image_id = $meta_value['ID'];
            }
            if (isset($meta_value['id'])) {
                $image_id = $meta_value['id'];
            }
        }
        if (\is_numeric($meta_value) && \intval($meta_value)) {
            $post_img = get_post(\intval($meta_value));
            if (get_post_type($post_img) == 'attachment') {
                $image_id = \intval($meta_value);
            } else {
                $img_post_id = \intval($meta_value);
                $image_id = get_post_thumbnail_id($img_post_id);
            }
        }
        if (!$image_id) {
            $upload_dir = '/wp-content/uploads/';
            $parts = \explode($upload_dir, $meta_value, 2);
            if (\count($parts) == 2) {
                $tmp = Helper::get_image_id($upload_dir . \end($parts));
                if ($tmp) {
                    $image_id = $tmp;
                }
            }
        }
        if ($image_id) {
            $img = wp_get_attachment_image_src($image_id, 'full');
            $img_url_full = \reset($img);
            $img_post_id = attachment_url_to_postid($img_url_full);
            $img_url = Group_Control_Image_Size::get_attachment_image_src($image_id, 'dce_meta_image_size', $settings);
        } else {
            $img_url = $meta_value;
            $img_url_full = $img_url;
        }
        // CAPTION
        $caption = '';
        if ($settings['dce_meta_image_caption_source']) {
            switch ($settings['dce_meta_image_caption_source']) {
                case 'attachment':
                    $caption = wp_get_attachment_caption($image_id);
                    break;
                case 'custom':
                    $caption = !empty($settings['dce_meta_image_caption']) ? $settings['dce_meta_image_caption'] : '';
            }
        }
        // LINK
        switch ($settings['dce_meta_image_link_to']) {
            case 'custom':
                if (!isset($settings['dce_meta_image_link_to']['url'])) {
                    $link = \false;
                }
                $link = $settings['dce_meta_image_link'];
                break;
            case 'file':
                $link = array('url' => $img_url_full);
                break;
            case 'post':
                if ($img_post_id) {
                    $permalink = get_permalink($img_post_id);
                } else {
                    $permalink = get_permalink($post_id);
                }
                $link = array('url' => $permalink);
                break;
            default:
                $link = \false;
        }
        if ($link) {
            $this->add_render_attribute('link', ['href' => $link['url']], null, \true);
            if (!empty($link['is_external'])) {
                $this->add_render_attribute('link', 'target', '_blank', \true);
            }
            if (!empty($link['nofollow'])) {
                $this->add_render_attribute('link', 'rel', 'nofollow', \true);
            }
        }
        $meta_html .= '<div class="elementor-image">';
        if ($caption) {
            $meta_html .= '<figure class="wp-caption">';
        }
        if ($link) {
            $meta_html .= '<a ' . $this->get_render_attribute_string('link') . '>';
        }
        $meta_html .= '<img src="' . esc_url($img_url) . '">';
        if ($link) {
            $meta_html .= '</a>';
        }
        if ($caption) {
            $meta_html .= '<figcaption class="widget-image-caption wp-caption-text">' . wp_kses_post($caption) . '</figcaption>';
        }
        if ($caption) {
            $meta_html .= '</figure>';
        }
        $meta_html .= '</div>';
        return $meta_html;
    }
}
