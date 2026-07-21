<?php

// SPDX-FileCopyrightText: 2018-2026 Ovation S.r.l. <help@dynamic.ooo>
// SPDX-License-Identifier: GPL-3.0-or-later
namespace DynamicContentForElementor\Widgets;

use Elementor\Controls_Manager;
use Elementor\Icons_Manager;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Css_Filter;
use Elementor\Group_Control_Typography;
use Elementor\Repeater;
use DynamicContentForElementor\Helper;
use DynamicContentForElementor\Tokens;
use DynamicContentForElementor\Plugin;
if (!\defined('ABSPATH')) {
    exit;
    // Exit if accessed directly
}
class AcfRepeater extends \DynamicContentForElementor\Widgets\WidgetPrototype
{
    public function run_once()
    {
        parent::run_once();
        $save_guard = \DynamicContentForElementor\Plugin::instance()->save_guard;
        $save_guard->register_unsafe_control($this->get_name(), 'other_post_source');
        $save_guard->register_unsafe_control($this->get_name(), 'dce_acf_repeater_thead_custom_html');
        $save_guard->register_unsafe_control($this->get_name(), 'dce_acf_repeater_subfields::dce_acf_repeater_acfield_link');
        $save_guard->register_unsafe_control($this->get_name(), 'dce_acf_repeater_html');
        $save_guard->register_unsafe_control($this->get_name(), 'dce_acf_repeater_accordion_heading');
    }
    public function get_script_depends()
    {
        return ['swiper', 'dce-acf-repeater'];
    }
    public function get_style_depends()
    {
        return ['dce-acf-repeater', 'dce-datatables', 'dce-accordionjs', 'swiper'];
    }
    /**
     * Register controls after check if this feature is only for admin
     *
     * @return void
     */
    protected function safe_register_controls()
    {
        $this->start_controls_section('section_acf_repeater', ['label' => $this->get_title()]);
        $this->add_control('dce_acf_repeater', ['label' => esc_html__('ACF Repeater field', 'dynamic-content-for-elementor'), 'type' => 'ooo_query', 'placeholder' => esc_html__('Select the field...', 'dynamic-content-for-elementor'), 'label_block' => \true, 'query_type' => 'acf', 'object_type' => 'repeater', 'dynamic' => ['active' => \false]]);
        $this->add_control('acf_repeater_from', ['label' => esc_html__('Retrieve the field from', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'default' => 'current_post', 'options' => ['current_post' => esc_html__('Current Post', 'dynamic-content-for-elementor'), 'current_user' => esc_html__('Current User', 'dynamic-content-for-elementor'), 'current_author' => esc_html__('Current Author', 'dynamic-content-for-elementor'), 'current_term' => esc_html__('Current Term', 'dynamic-content-for-elementor'), 'options_page' => esc_html__('Options Page', 'dynamic-content-for-elementor')]]);
        $this->add_control('dce_acf_repeater_mode', ['label' => esc_html__('Display mode', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CHOOSE, 'options' => ['subfields' => ['title' => esc_html__('Sub fields', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-list-ul'], 'html' => ['title' => esc_html__('Dynamic HTML', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-code'], 'template' => ['title' => esc_html__('Template', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-th-large']], 'toggle' => \false, 'default' => 'subfields']);
        $repeater_subfields = new \Elementor\Repeater();
        $repeater_subfields->start_controls_tabs('acfitems_repeater');
        $repeater_subfields->start_controls_tab('tab_content', ['label' => esc_html__('Item', 'dynamic-content-for-elementor')]);
        $repeater_subfields->add_control('dce_acf_repeater_field_name', ['type' => 'ooo_query', 'placeholder' => esc_html__('Select the field...', 'dynamic-content-for-elementor'), 'label_block' => \true, 'query_type' => 'acf', 'dynamic' => ['active' => \false]]);
        $repeater_subfields->add_control('is_image', ['label' => esc_html__('Image Type', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => '']);
        $repeater_subfields->add_control('dce_acf_repeater_field_tag', ['label' => esc_html__('HTML Tag', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'options' => Helper::get_html_tags([], \true), 'default' => 'span']);
        $repeater_subfields->add_group_control(Group_Control_Image_Size::get_type(), ['name' => 'image', 'default' => 'full', 'condition' => ['is_image!' => '']]);
        $repeater_subfields->add_control('image_size_notice', ['type' => Controls_Manager::NOTICE, 'notice_type' => 'info', 'dismissible' => \false, 'content' => esc_html__('Image Resolution apply only when the ACF image returns ID or Array, not URL.', 'dynamic-content-for-elementor'), 'condition' => ['is_image!' => '']]);
        $repeater_subfields->end_controls_tab();
        $repeater_subfields->start_controls_tab('tab_style', ['label' => esc_html__('Style', 'dynamic-content-for-elementor')]);
        $repeater_subfields->add_responsive_control('dce_acf_repeater_field_no_tag', ['type' => Controls_Manager::RAW_HTML, 'raw' => esc_html__('Please select an HTML Tag to choose the style.', 'dynamic-content-for-elementor'), 'content_classes' => 'elementor-panel-alert elementor-panel-alert-warning', 'condition' => ['dce_acf_repeater_field_tag' => '']]);
        $repeater_subfields->add_control('dce_acf_repeater_field_padding', ['label' => esc_html__('Padding', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => ['px', '%'], 'selectors' => ['{{WRAPPER}} {{CURRENT_ITEM}}' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'], 'separator' => 'after', 'condition' => ['dce_acf_repeater_field_tag!' => '']]);
        $repeater_subfields->add_control('dce_acf_repeater_h_texts', ['label' => esc_html__('Text', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HEADING, 'separator' => 'before', 'condition' => ['is_image' => '', 'dce_acf_repeater_field_tag!' => '']]);
        $repeater_subfields->add_control('dce_acf_repeater_field_color', ['label' => esc_html__('Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} {{CURRENT_ITEM}}' => 'color: {{VALUE}};', '{{WRAPPER}} {{CURRENT_ITEM}} a' => 'color: {{VALUE}};'], 'condition' => ['is_image' => '', 'dce_acf_repeater_field_tag!' => '']]);
        $repeater_subfields->add_control('dce_acf_repeater_field_hover_color', ['label' => esc_html__('Hover Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} {{CURRENT_ITEM}} a:hover' => 'color: {{VALUE}};'], 'condition' => ['is_image' => '', 'dce_acf_repeater_enable_link!' => '', 'dce_acf_repeater_field_tag!' => '']]);
        $repeater_subfields->add_group_control(Group_Control_Typography::get_type(), ['name' => 'dce_acf_repeater_field_typography', 'selector' => '{{WRAPPER}} {{CURRENT_ITEM}}', 'pop_hover' => \true, 'condition' => ['is_image' => '', 'dce_acf_repeater_field_tag!' => '']]);
        $repeater_subfields->add_control('dce_acf_repeater_h_image', ['label' => esc_html__('Image', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HEADING, 'separator' => 'before', 'condition' => ['is_image!' => '', 'dce_acf_repeater_field_tag!' => '']]);
        $repeater_subfields->add_responsive_control('dce_acf_repeater_field_size_image', ['label' => esc_html__('Max-Width', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => '', 'unit' => '%'], 'size_units' => ['%', 'px'], 'range' => ['%' => ['min' => 1, 'max' => 100, 'step' => 1], 'px' => ['min' => 1, 'max' => 800, 'step' => 1]], 'selectors' => ['{{WRAPPER}} {{CURRENT_ITEM}} img' => 'max-width: {{SIZE}}{{UNIT}};'], 'condition' => ['is_image!' => '', 'dce_acf_repeater_field_tag!' => '']]);
        $repeater_subfields->add_group_control(Group_Control_Border::get_type(), ['name' => 'image_border', 'label' => esc_html__('Image Border', 'dynamic-content-for-elementor'), 'selector' => '{{WRAPPER}} {{CURRENT_ITEM}} img', 'condition' => ['is_image!' => '', 'dce_acf_repeater_field_tag!' => '']]);
        $repeater_subfields->add_control('image_border_radius', ['label' => esc_html__('Border Radius', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => ['px', '%'], 'selectors' => ['{{WRAPPER}} {{CURRENT_ITEM}} img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'], 'condition' => ['is_image!' => '', 'dce_acf_repeater_field_tag!' => '']]);
        $repeater_subfields->add_group_control(Group_Control_Box_Shadow::get_type(), ['name' => 'image_box_shadow', 'selector' => '{{WRAPPER}} {{CURRENT_ITEM}} img', 'condition' => ['is_image!' => '', 'dce_acf_repeater_field_tag!' => '']]);
        $repeater_subfields->add_group_control(Group_Control_Css_Filter::get_type(), ['name' => 'filters_image', 'label' => esc_html__('Filters image', 'dynamic-content-for-elementor'), 'selector' => '{{WRAPPER}} {{CURRENT_ITEM}} img', 'condition' => ['is_image!' => '', 'dce_acf_repeater_field_tag!' => '']]);
        $repeater_subfields->end_controls_tab();
        $repeater_subfields->start_controls_tab('tab_link', ['label' => esc_html__('Link', 'dynamic-content-for-elementor')]);
        $repeater_subfields->add_control('dce_acf_repeater_enable_link', ['label' => esc_html__('Link', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER]);
        $repeater_subfields->add_control('dce_acf_repeater_acfield_link', ['label' => esc_html__('URL Field', 'dynamic-content-for-elementor'), 'type' => 'ooo_query', 'label_block' => \true, 'query_type' => 'acf', 'object_type' => ['url', 'image', 'file', 'link', 'post_object', 'page_link', 'taxonomy', 'user'], 'dynamic' => ['active' => \false], 'frontend_available' => \true, 'condition' => ['dce_acf_repeater_enable_link!' => '']]);
        $repeater_subfields->add_control('dce_acf_repeater_target_link', ['label' => esc_html__('Open in a new window', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'condition' => ['dce_acf_repeater_enable_link!' => '', 'dce_acf_repeater_acfield_link!' => '']]);
        $repeater_subfields->add_control('dce_acf_repeater_nofollow_link', ['label' => esc_html__('Add nofollow', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'condition' => ['dce_acf_repeater_enable_link!' => '', 'dce_acf_repeater_acfield_link!' => '']]);
        $repeater_subfields->end_controls_tab();
        $repeater_subfields->end_controls_tabs();
        $this->add_control('dce_acf_repeater_subfields', ['label' => esc_html__('Sub fields', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::REPEATER, 'fields' => $repeater_subfields->get_controls(), 'title_field' => '{{{dce_acf_repeater_field_name}}}', 'prevent_empty' => \true, 'item_actions' => ['add' => \true, 'duplicate' => \true, 'remove' => \true, 'sort' => \true], 'condition' => ['dce_acf_repeater_mode' => 'subfields']]);
        Plugin::instance()->text_templates->maybe_add_notice($this, '', ['dce_acf_repeater_mode' => 'html']);
        $this->add_control('dce_acf_repeater_html', ['label' => esc_html__('Dynamic HTML', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CODE, 'default' => \DynamicContentForElementor\Plugin::instance()->text_templates->get_default_value(['dynamic-shortcodes' => "{for:item {data:row} {get:item} @sep=', '}", 'tokens' => '[ROW]']), 'ai' => ['active' => \false], 'dynamic' => ['active' => \false], 'description' => \DynamicContentForElementor\Plugin::instance()->text_templates->get_default_value(['dynamic-shortcodes' => \sprintf(esc_html__('Available Dynamic Shortcodes include: %1$s You can also use all other Dynamic Shortcodes to further customize the content.', 'dynamic-content-for-elementor'), '<ul>' . '<li>' . \sprintf(esc_html__("Use %s to fetch an array of the current row's data.", 'dynamic-content-for-elementor'), '<code>{data:row}</code>') . '</li>' . '<li>' . \sprintf(esc_html__("Use %s to display specific field of the current row's data.", 'dynamic-content-for-elementor'), '<code>{data:row||sub-field-name}</code>') . '</li>' . '<li>' . \sprintf(esc_html__("Use %s to fetch a comma-separated string of the current row's data.", 'dynamic-content-for-elementor'), "<code>{for:item {data:row} {get:item} @sep=', '}</code>") . '</li>' . '<li>' . \sprintf(esc_html__('Use %s to fetch the index of the current row.', 'dynamic-content-for-elementor'), '<code>{data:row-index}</code>') . '</li>' . '</ul>'), 'tokens' => esc_html__('You can use HTML and Tokens.', 'dynamic-content-for-elementor')]), 'condition' => ['dce_acf_repeater_mode' => 'html']]);
        $this->add_control('dce_acf_repeater_template', ['label' => esc_html__('Template', 'dynamic-content-for-elementor'), 'type' => 'ooo_query', 'placeholder' => esc_html__('Select Template', 'dynamic-content-for-elementor'), 'label_block' => \true, 'query_type' => 'posts', 'dynamic' => ['active' => \false], 'object_type' => 'elementor_library', 'description' => esc_html__('Use an Elementor Template as content of repeater.', 'dynamic-content-for-elementor'), 'condition' => ['dce_acf_repeater_mode' => 'template']]);
        $this->add_control('dce_acf_repeater_template_2_enable', ['label' => esc_html__('Different Template for odd rows', 'dynamic-content-for-elementor'), 'description' => esc_html__('Manage the appearance of the odd elements.', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => '', 'render_type' => 'template', 'condition' => ['dce_acf_repeater_mode' => 'template']]);
        $this->add_control('dce_acf_repeater_template_2_id', ['label' => esc_html__('Template for odd rows', 'dynamic-content-for-elementor'), 'type' => 'ooo_query', 'placeholder' => esc_html__('Select Template', 'dynamic-content-for-elementor'), 'label_block' => \true, 'show_label' => \false, 'query_type' => 'posts', 'object_type' => 'elementor_library', 'render_type' => 'template', 'condition' => ['dce_acf_repeater_mode' => 'template', 'dce_acf_repeater_template_2_enable!' => '']]);
        $this->add_control('dce_acf_repeater_pagination', ['label' => esc_html__('Show only these rows', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'label_block' => \true, 'placeholder' => 'first, last, 1, 3-4, 2n, even', 'description' => esc_html__('Leave empty to print all rows, otherwise write their number. Use "first" and "last" to indicate the first and last element. Insert multiple values with a comma.', 'dynamic-content-for-elementor')]);
        $this->end_controls_section();
        $this->start_controls_section('dce_acf_repeater_h_render', ['label' => esc_html__('Skin', 'dynamic-content-for-elementor')]);
        $this->add_control('dce_acf_repeater_format', ['label' => esc_html__('Skin', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'frontend_available' => \true, 'options' => ['' => esc_html__('Text', 'dynamic-content-for-elementor'), 'grid' => esc_html__('Grid', 'dynamic-content-for-elementor'), 'masonry' => esc_html__('Masonry', 'dynamic-content-for-elementor'), 'slider_carousel' => esc_html__('Carousel', 'dynamic-content-for-elementor'), 'table' => esc_html__('Table', 'dynamic-content-for-elementor'), 'list' => esc_html__('List', 'dynamic-content-for-elementor'), 'accordion' => esc_html__('Accordion', 'dynamic-content-for-elementor')], 'default' => 'grid']);
        $this->add_control('dce_acf_repeater_separator', ['label' => esc_html__('Separator', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'default' => ', ', 'condition' => ['dce_acf_repeater_format' => '']]);
        Plugin::instance()->text_templates->maybe_add_notice($this, 'accordion_heading', ['dce_acf_repeater_format' => 'accordion']);
        $this->add_control('dce_acf_repeater_accordion_heading', ['label' => esc_html__('Heading Text', 'dynamic-content-for-elementor'), 'default' => \DynamicContentForElementor\Plugin::instance()->text_templates->get_default_value(['dynamic-shortcodes' => '{data:row-index}', 'tokens' => '[ROW:id]']), 'type' => Controls_Manager::TEXT, 'condition' => ['dce_acf_repeater_format' => 'accordion']]);
        $this->add_control('selected_icon', ['label' => esc_html__('Icon', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::ICONS, 'separator' => 'before', 'default' => ['value' => 'fas fa-plus', 'library' => 'fa-solid'], 'skin' => 'inline', 'label_block' => \false, 'condition' => ['dce_acf_repeater_format' => 'accordion']]);
        $this->add_control('selected_active_icon', ['label' => esc_html__('Active Icon', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::ICONS, 'default' => ['value' => 'fas fa-minus', 'library' => 'fa-solid'], 'skin' => 'inline', 'label_block' => \false, 'condition' => ['dce_acf_repeater_format' => 'accordion', 'selected_icon[value]!' => '']]);
        $this->add_control('dce_acf_repeater_accordion_heading_size', ['label' => esc_html__('Heading HTML Tag', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'options' => Helper::get_html_tags(), 'default' => 'h4', 'condition' => ['dce_acf_repeater_format' => 'accordion']]);
        $this->add_control('dce_acf_repeater_accordion_start', ['label' => esc_html__('Initially open', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'options' => ['none' => esc_html__('None', 'dynamic-content-for-elementor'), 'first' => esc_html__('First', 'dynamic-content-for-elementor'), 'custom' => esc_html__('Custom Index', 'dynamic-content-for-elementor'), 'all' => esc_html__('All', 'dynamic-content-for-elementor')], 'inline' => \true, 'default' => 'none', 'frontend_available' => \true, 'condition' => ['dce_acf_repeater_format' => 'accordion']]);
        $this->add_control('accordion_start_custom', ['label' => esc_html__('Active Index', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::NUMBER, 'frontend_available' => \true, 'default' => 1, 'condition' => ['dce_acf_repeater_accordion_start' => 'custom', 'dce_acf_repeater_format' => 'accordion']]);
        $this->add_control('dce_acf_repeater_accordion_close', ['label' => esc_html__('Automatically close other tabs', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'frontend_available' => \true, 'condition' => ['dce_acf_repeater_format' => 'accordion', 'dce_acf_repeater_accordion_start' => ['none', 'first']]]);
        $this->add_control('accordion_speed', ['label' => esc_html__('Speed (ms)', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => 300, 'unit' => 'ms'], 'size_units' => ['ms'], 'range' => ['ms' => ['min' => 0, 'max' => 500, 'step' => 50]], 'render_type' => 'template', 'frontend_available' => \true, 'condition' => ['dce_acf_repeater_format' => 'accordion']]);
        $this->add_control('dce_acf_repeater_list', ['label' => esc_html__('List type', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CHOOSE, 'options' => ['ul' => ['title' => esc_html__('Unordered List', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-list-ul'], 'ol' => ['title' => esc_html__('Ordered List', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-list-ol']], 'toggle' => \false, 'default' => 'ul', 'condition' => ['dce_acf_repeater_format' => 'list']]);
        $this->add_control('selected_icon_ul', ['label' => esc_html__('Icon', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::ICONS, 'separator' => 'before', 'skin' => 'inline', 'label_block' => \false, 'condition' => ['dce_acf_repeater_format' => 'list', 'dce_acf_repeater_list' => 'ul'], 'selectors' => ['{{WRAPPER}} ul > li > .elementor-icon' => 'float: left; clear: both; font-size: inherit;']]);
        $this->add_control('dce_acf_repeater_list_flex', ['label' => esc_html__('Flex list', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'condition' => ['dce_acf_repeater_format' => 'list', 'dce_acf_repeater_list' => 'ul', 'selected_icon_ul[value]!' => ''], 'selectors' => ['{{WRAPPER}} ul > li' => 'display: flex;']]);
        $this->add_responsive_control('dce_acf_repeater_col', ['label' => esc_html__('Columns', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'default' => '5', 'tablet_default' => '3', 'mobile_default' => '1', 'options' => ['1' => '1', '2' => '2', '3' => '3', '4' => '4', '5' => '5', '6' => '6', '7' => '7'], 'render_type' => 'template', 'selectors' => ['{{WRAPPER}} .dce-acf-repeater-grid' => 'display: flex; flex-wrap: wrap;', '{{WRAPPER}} .dce-acf-repeater-masonry .dce-acf-repeater-item' => 'width: calc( 100% / {{VALUE}} );', '{{WRAPPER}} .dce-acf-repeater-grid .dce-acf-repeater-item' => 'flex: 0 1 calc( 100% / {{VALUE}} ); min-width: 0;'], 'condition' => ['dce_acf_repeater_format' => ['grid', 'masonry']]]);
        $this->add_control('flex_grow', ['label' => esc_html__('Flex grow', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CHOOSE, 'toggle' => \false, 'label_block' => \false, 'options' => ['1' => ['title' => '', 'icon' => 'fa fa-check'], '0' => ['title' => esc_html__('0', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-ban']], 'default' => 1, 'selectors' => ['{{WRAPPER}} .dce-acf-repeater-grid .dce-acf-repeater-item' => 'flex-grow: {{VALUE}};'], 'condition' => ['dce_acf_repeater_format' => 'grid']]);
        $this->add_responsive_control('flexgrid_mode', ['label' => esc_html__('Alignment grid', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'default' => 'flex-start', 'tablet_default' => '3', 'mobile_default' => '1', 'label_block' => \true, 'options' => ['flex-start' => 'Flex start', 'flex-end' => 'Flex end', 'center' => 'Center', 'space-between' => 'Space Between', 'space-around' => 'Space Around'], 'selectors' => ['{{WRAPPER}} .dce-acf-repeater-grid' => 'justify-content: {{VALUE}};'], 'condition' => ['dce_acf_repeater_format' => 'grid', 'flex_grow' => '0']]);
        $this->add_responsive_control('v_align_items', ['label' => esc_html__('Vertical Alignment', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CHOOSE, 'options' => ['flex-start' => ['title' => esc_html__('Top', 'dynamic-content-for-elementor'), 'icon' => 'eicon-v-align-top'], 'center' => ['title' => esc_html__('Middle', 'dynamic-content-for-elementor'), 'icon' => 'eicon-v-align-middle'], 'flex-end' => ['title' => esc_html__('Down', 'dynamic-content-for-elementor'), 'icon' => 'eicon-v-align-bottom'], 'stretch' => ['title' => esc_html__('Stretch', 'dynamic-content-for-elementor'), 'icon' => 'eicon-v-align-stretch']], 'default' => 'top', 'selectors' => ['{{WRAPPER}} .dce-acf-repeater-item' => 'align-self: {{VALUE}};'], 'condition' => ['dce_acf_repeater_format' => 'grid', 'flex_grow' => '0']]);
        $this->add_control('dce_acf_repeater_thead', ['label' => esc_html__('Table Heading', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'condition' => ['dce_acf_repeater_format' => 'table']]);
        $this->add_control('dce_acf_repeater_thead_custom', ['label' => esc_html__('Custom Table Heading', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'condition' => ['dce_acf_repeater_thead!' => '', 'dce_acf_repeater_format' => 'table', 'dce_acf_repeater_mode' => 'html']]);
        $this->add_control('dce_acf_repeater_thead_custom_html', [
            'label' => esc_html__('Table Heading', 'dynamic-content-for-elementor'),
            'type' => Controls_Manager::CODE,
            /* translators: %1$s is the HTML tag 'thead' */
            'description' => \sprintf(esc_html__('Remember to use the HTML %1$s element to define a set of rows defining the head of the columns of the table.', 'dynamic-content-for-elementor'), '<strong>thead</strong>'),
            'condition' => ['dce_acf_repeater_thead!' => '', 'dce_acf_repeater_format' => 'table', 'dce_acf_repeater_thead_custom!' => '', 'dce_acf_repeater_mode' => 'html'],
        ]);
        $this->add_control('dce_acf_repeater_datatables', ['label' => esc_html__('Use DataTables', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'description' => esc_html__('Add advanced interaction controls to your HTML tables.', 'dynamic-content-for-elementor') . '<br><small>' . esc_html__('Read more on ', 'dynamic-content-for-elementor') . ' <a href="https://datatables.net/" target="_blank">DataTables</a></small>', 'condition' => ['dce_acf_repeater_format' => 'table'], 'frontend_available' => \true]);
        $this->end_controls_section();
        $this->start_controls_section('dce_acf_repeater_datatables_section', ['label' => esc_html__('DataTables', 'dynamic-content-for-elementor'), 'condition' => ['dce_acf_repeater_format' => 'table', 'dce_acf_repeater_datatables!' => '']]);
        $this->add_control('heading_acf_repeater_datatables_extensions', ['label' => esc_html__('DataTables Extensions', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::RAW_HTML, 'raw' => '<br><small>' . esc_html__('Read more on ', 'dynamic-content-for-elementor') . ' <a href="https://datatables.net/extensions/index" target="_blank">DataTables Extensions</a></small>', 'separator' => 'before']);
        $this->add_control('dce_acf_repeater_style_table_data_autofill', ['label' => esc_html__('Autofill', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'description' => esc_html__('Excel-like click and drag copying and filling of data.', 'dynamic-content-for-elementor'), 'frontend_available' => \true]);
        $this->add_control('dce_acf_repeater_style_table_data_buttons', ['label' => esc_html__('Buttons', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'description' => esc_html__('A common framework for user interaction buttons. Like Export and Print.', 'dynamic-content-for-elementor'), 'frontend_available' => \true]);
        $this->add_control('dce_acf_repeater_style_table_data_colreorder', ['label' => esc_html__('ColReorder', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'description' => esc_html__('Click-and-drag column reordering.', 'dynamic-content-for-elementor'), 'frontend_available' => \true]);
        $this->add_control('dce_acf_repeater_style_table_data_fixedcolumns', ['label' => esc_html__('FixedColumns', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'description' => esc_html__('Fix one or more columns to the left or right of a scrolling table.', 'dynamic-content-for-elementor'), 'frontend_available' => \true]);
        $this->add_control('dce_acf_repeater_style_table_data_fixedheader', ['label' => esc_html__('FixedHeader', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'description' => esc_html__('Sticky header and / or footer for the table.', 'dynamic-content-for-elementor'), 'frontend_available' => \true]);
        $this->add_control('dce_acf_repeater_style_table_data_keytable', ['label' => esc_html__('KeyTable', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'description' => esc_html__('Keyboard navigation of cells in a table, just like a spreadsheet.', 'dynamic-content-for-elementor'), 'frontend_available' => \true]);
        $this->add_control('dce_acf_repeater_style_table_data_responsive', ['label' => esc_html__('Responsive', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'description' => esc_html__('Dynamically show and hide columns based on the browser size.', 'dynamic-content-for-elementor'), 'frontend_available' => \true]);
        $this->add_control('dce_acf_repeater_style_table_data_rowgroup', ['label' => esc_html__('RowGroup', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'description' => esc_html__('Show similar data grouped together by a custom data point.', 'dynamic-content-for-elementor'), 'frontend_available' => \true]);
        $this->add_control('dce_acf_repeater_style_table_data_rowreorder', ['label' => esc_html__('RowReorder', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'description' => esc_html__('Click-and-drag reordering of rows.', 'dynamic-content-for-elementor'), 'frontend_available' => \true]);
        $this->add_control('dce_acf_repeater_style_table_data_scroller', ['label' => esc_html__('Scroller', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'description' => esc_html__('Virtual rendering of a scrolling table for large data sets.', 'dynamic-content-for-elementor'), 'frontend_available' => \true]);
        $this->add_control('dce_acf_repeater_style_table_data_scroller_y', ['label' => esc_html__('Scroller Height', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::NUMBER, 'min' => 0, 'default' => '200', 'description' => esc_html__('Height of virtual scroller.', 'dynamic-content-for-elementor'), 'frontend_available' => \true]);
        $this->add_control('dce_acf_repeater_style_table_data_select', ['label' => esc_html__('Select', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'description' => esc_html__('Adds row, column and cell selection abilities to a table.', 'dynamic-content-for-elementor'), 'frontend_available' => \true]);
        $this->add_control('dce_acf_repeater_style_table_data_ui_options', ['label' => esc_html__('UI Options', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HEADING, 'separator' => 'before']);
        $this->add_control('dce_acf_repeater_style_table_data_search', ['label' => esc_html__('Search Box', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'description' => esc_html__('Show the search box above the table.', 'dynamic-content-for-elementor'), 'default' => 'yes', 'frontend_available' => \true]);
        $this->add_control('dce_acf_repeater_style_table_data_pagination', ['label' => esc_html__('Pagination', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'description' => esc_html__('Show pagination controls below the table.', 'dynamic-content-for-elementor'), 'default' => 'yes', 'frontend_available' => \true]);
        $this->add_control('dce_acf_repeater_style_table_data_info', ['label' => esc_html__('Info Text', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'description' => esc_html__('Show the "Showing X to Y of Z entries" text.', 'dynamic-content-for-elementor'), 'default' => 'yes', 'frontend_available' => \true]);
        $this->add_control('dce_acf_repeater_style_table_data_length_menu', ['label' => esc_html__('Length Menu', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'description' => esc_html__('Show the "Show X entries" dropdown.', 'dynamic-content-for-elementor'), 'default' => 'yes', 'frontend_available' => \true]);
        $this->add_control('dce_acf_repeater_style_table_data_ordering', ['label' => esc_html__('Column Sorting', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'description' => esc_html__('Enable sorting by clicking on column headers.', 'dynamic-content-for-elementor'), 'default' => 'yes', 'frontend_available' => \true]);
        $this->end_controls_section();
        $this->start_controls_section('section_filter', ['label' => esc_html__('Filter', 'dynamic-content-for-elementor')]);
        $this->add_control('filter', ['label' => esc_html__('Filter', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER]);
        $repeater_filter = new \Elementor\Repeater();
        $repeater_filter->add_control('filter_field', ['type' => 'ooo_query', 'placeholder' => esc_html__('Select the field...', 'dynamic-content-for-elementor'), 'label_block' => \true, 'query_type' => 'acf', 'dynamic' => ['active' => \false]]);
        $repeater_filter->add_control('filter_operator', ['label' => esc_html__('Status', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'options' => Helper::get_compare_options(), 'default' => 'isset']);
        $repeater_filter->add_control('filter_value', ['type' => Controls_Manager::TEXT, 'label' => esc_html__('Value', 'dynamic-content-for-elementor'), 'condition' => ['filter_operator!' => ['not', 'isset']]]);
        $this->add_control('filters', ['label' => esc_html__('Conditions', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::REPEATER, 'fields' => $repeater_filter->get_controls(), 'title_field' => '{{{filter_field}}}', 'prevent_empty' => \false, 'item_actions' => ['add' => \true, 'duplicate' => \true, 'remove' => \true, 'sort' => \true], 'condition' => ['filter!' => '']]);
        $this->add_control('filters_relationship', ['label' => esc_html__('Logical Relationship', 'dynamic-content-for-elementor'), 'description' => esc_html__('The logical relationship when there is more than one', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'options' => ['AND' => 'AND', 'OR' => 'OR'], 'toggle' => \false, 'default' => 'AND', 'condition' => ['filter!' => '']]);
        $this->end_controls_section();
        $this->start_controls_section('section_source', ['label' => esc_html__('Source', 'dynamic-content-for-elementor'), 'condition' => ['acf_repeater_from' => 'current_post']]);
        $this->add_control('data_source', ['label' => esc_html__('Source', 'dynamic-content-for-elementor'), 'description' => esc_html__('Select the data source', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => 'yes', 'label_on' => esc_html__('Same', 'dynamic-content-for-elementor'), 'label_off' => esc_html__('other', 'dynamic-content-for-elementor'), 'return_value' => 'yes']);
        $this->add_control('other_post_source', ['label' => esc_html__('Select from other source post', 'dynamic-content-for-elementor'), 'type' => 'ooo_query', 'placeholder' => esc_html__('Post Title', 'dynamic-content-for-elementor'), 'label_block' => \true, 'query_type' => 'posts', 'condition' => ['data_source' => '']]);
        $this->end_controls_section();
        //
        //////////////////////////////////////////////////////////// [ SECTION Slider & Carousel ]
        //
        // ------------------------------ Base Settings, Slides grid, Grab Cursor
        $this->start_controls_section('section_slidercarousel_mode', ['label' => esc_html__('Carousel', 'dynamic-content-for-elementor'), 'condition' => ['dce_acf_repeater_format' => 'slider_carousel']]);
        $this->add_control('remove_masking', ['label' => esc_html__('Remove Masking', 'dynamic-content-for-elementor'), 'description' => esc_html__('Remove the mask on the carousel to allow the display of the elements outside', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'prefix_class' => 'carousel-', 'return_value' => 'unmasked', 'frontend_available' => \true, 'default' => '']);
        // -------------------------------- Progressione ------
        // da valutare ....
        $this->add_control('effects', ['label' => esc_html__('Transition Effect', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'options' => ['slide' => esc_html__('Slide', 'dynamic-content-for-elementor'), 'fade' => esc_html__('Fade', 'dynamic-content-for-elementor'), 'cube' => esc_html__('Cube', 'dynamic-content-for-elementor'), 'coverflow' => esc_html__('Coverflow', 'dynamic-content-for-elementor'), 'flip' => esc_html__('Flip', 'dynamic-content-for-elementor')], 'default' => 'slide', 'frontend_available' => \true]);
        $this->add_responsive_control('slidesPerView', ['label' => esc_html__('Slides Per View', 'dynamic-content-for-elementor'), 'description' => esc_html__('Number of slides visible at the same time on slider\'s container).', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::NUMBER, 'default' => '1', 'min' => 1, 'max' => 12, 'step' => 1, 'frontend_available' => \true]);
        $this->add_responsive_control('slidesColumn', ['label' => esc_html__('Slides Column', 'dynamic-content-for-elementor'), 'description' => esc_html__('Number of slides per column, for multirow layout.', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::NUMBER, 'default' => '1', 'min' => 1, 'max' => 4, 'step' => 1, 'frontend_available' => \true]);
        $this->add_responsive_control('slidesPerGroup', ['label' => esc_html__('Slides Per Group', 'dynamic-content-for-elementor'), 'description' => esc_html__('Set numbers of slides to define and enable group sliding. Useful to use with Slides Per View > 1', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::NUMBER, 'default' => 1, 'tablet_default' => '', 'mobile_default' => '', 'min' => 1, 'max' => 12, 'step' => 1, 'frontend_available' => \true]);
        $this->add_control('speed_slider', ['label' => esc_html__('Speed', 'dynamic-content-for-elementor'), 'description' => esc_html__('Duration of transition between slides (in ms)', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::NUMBER, 'default' => 300, 'min' => 0, 'max' => 3000, 'step' => 10, 'frontend_available' => \true]);
        $this->add_responsive_control('spaceBetween', ['label' => esc_html__('Space Between', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::NUMBER, 'default' => 0, 'tablet_default' => '', 'mobile_default' => '', 'min' => 0, 'max' => 100, 'step' => 1, 'frontend_available' => \true]);
        $this->add_control(
            // Added a 2 because we did not want the previous broken setting
            // value to influence the result:
            'centeredSlides2',
            ['label' => esc_html__('Centered Slides', 'dynamic-content-for-elementor'), 'description' => esc_html__('If true, then active slide will be centered, not always on the left side.', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'frontend_available' => \true]
        );
        $this->add_control('centerInsufficientSlides', ['label' => esc_html__('Center Insufficient Slides', 'dynamic-content-for-elementor'), 'description' => esc_html__('When enabled it center slides if the amount of slides less than slidesPerView', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => 'yes', 'frontend_available' => \true]);
        // -------------------------------- Free Mode ------
        $this->add_control('freemode_options', ['label' => esc_html__('Free Mode', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HEADING, 'separator' => 'before']);
        $this->add_control('freeMode', ['label' => esc_html__('Free Mode', 'dynamic-content-for-elementor'), 'description' => esc_html__('If true then slides will not have fixed positions', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'frontend_available' => \true]);
        $this->add_control('freeModeMomentum', ['label' => esc_html__('Free Mode Momentum', 'dynamic-content-for-elementor'), 'description' => esc_html__('If true, then slide will keep moving for a while after you release it', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'frontend_available' => \true, 'condition' => ['freeMode' => 'yes']]);
        $this->add_control('freeModeMomentumRatio', ['label' => esc_html__('Free Mode Momentum Ratio', 'dynamic-content-for-elementor'), 'description' => esc_html__('Higher value produces larger momentum distance after you release slider', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::NUMBER, 'default' => 1, 'min' => 0, 'max' => 10, 'step' => 0.1, 'frontend_available' => \true, 'condition' => ['freeMode' => 'yes', 'freeModeMomentum' => 'yes']]);
        $this->add_control('freeModeMomentumVelocityRatio', ['label' => esc_html__('Free Mode Momentum Velocity Ratio', 'dynamic-content-for-elementor'), 'description' => esc_html__('Higher value produces larger momentum speed after you release slider', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::NUMBER, 'default' => 1, 'min' => 0, 'max' => 10, 'step' => 0.1, 'frontend_available' => \true, 'condition' => ['freeMode' => 'yes', 'freeModeMomentum' => 'yes']]);
        $this->add_control('freeModeMomentumBounce', ['label' => esc_html__('Free Mode Momentum Bounce', 'dynamic-content-for-elementor'), 'description' => esc_html__('Set to false if you want to disable momentum bounce in free mode', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => 'yes', 'frontend_available' => \true, 'condition' => ['freeMode' => 'yes']]);
        $this->add_control('freeModeMomentumBounceRatio', ['label' => esc_html__('Free Mode Momentum Bounce Ratio', 'dynamic-content-for-elementor'), 'description' => esc_html__('Higher value produces larger momentum bounce effect', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::NUMBER, 'default' => 1, 'min' => 0, 'max' => 10, 'step' => 0.1, 'frontend_available' => \true, 'condition' => ['freeMode' => 'yes', 'freeModeMomentumBounce' => 'yes']]);
        $this->add_control('freeModeMinimumVelocity', ['label' => esc_html__('Free Mode Momentum Velocity Ratio', 'dynamic-content-for-elementor'), 'description' => esc_html__('Minimum touchmove-velocity required to trigger free mode momentum', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::NUMBER, 'default' => 0.02, 'min' => 0, 'max' => 1, 'step' => 0.01, 'frontend_available' => \true, 'condition' => ['freeMode' => 'yes']]);
        $this->add_control('freeModeSticky', ['label' => esc_html__('Free Mode Sticky', 'dynamic-content-for-elementor'), 'description' => esc_html__('Set \'yes\' to enable snap to slides positioned in free mode', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'frontend_available' => \true, 'condition' => ['freeMode' => 'yes']]);
        // -------------------------------- Navigation options ------
        $this->add_control('navigation_options', ['label' => esc_html__('Navigation options', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HEADING, 'separator' => 'before']);
        $this->add_control('useNavigation', ['label' => esc_html__('Use Navigation', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => 'yes', 'frontend_available' => \true]);
        // ------------------------------------------------- Navigations Arrow Options
        $this->add_control('navigation_arrow_color', ['label' => esc_html__('Arrows color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'default' => '', 'selectors' => ['{{WRAPPER}} .swiper-button-next path, {{WRAPPER}} .swiper-button-prev path, ' => 'fill: {{VALUE}};', '{{WRAPPER}} .swiper-button-next line, {{WRAPPER}} .swiper-button-prev line, {{WRAPPER}} .swiper-button-next polyline, {{WRAPPER}} .swiper-button-prev polyline' => 'stroke: {{VALUE}};'], 'condition' => ['useNavigation' => 'yes']]);
        $this->add_control('useNavigation_animationHover', ['label' => esc_html__('Use animation in rollover', 'dynamic-content-for-elementor'), 'description' => esc_html__('A short animation will take place at the rollover.', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => 'yes', 'prefix_class' => 'hoveranim-', 'separator' => 'before', 'condition' => ['useNavigation' => 'yes']]);
        $this->add_control('navigation_arrow_color_hover', ['label' => esc_html__('Hover color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'default' => '', 'selectors' => ['{{WRAPPER}} .swiper-button-next:hover path, {{WRAPPER}} .swiper-button-prev:hover path, ' => 'fill: {{VALUE}};', '{{WRAPPER}} .swiper-button-next:hover line, {{WRAPPER}} .swiper-button-prev:hover line, {{WRAPPER}} .swiper-button-next:hover polyline, {{WRAPPER}} .swiper-button-prev:hover polyline' => 'stroke: {{VALUE}};'], 'condition' => ['useNavigation' => 'yes'], 'separator' => 'after']);
        $this->add_responsive_control('pagination_stroke_1', ['label' => esc_html__('Stroke Arrow', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => ''], 'tablet_default' => ['size' => ''], 'mobile_default' => ['size' => ''], 'range' => ['px' => ['max' => 50, 'min' => 0, 'step' => 1.0]], 'selectors' => ['{{WRAPPER}} .swiper-button-prev polyline, {{WRAPPER}} .swiper-button-next polyline' => 'stroke-width: {{SIZE}};'], 'condition' => ['useNavigation' => 'yes']]);
        $this->add_responsive_control('pagination_stroke_2', ['label' => esc_html__('Stroke Line', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => ''], 'tablet_default' => ['size' => ''], 'mobile_default' => ['size' => ''], 'range' => ['px' => ['max' => 50, 'min' => 0, 'step' => 1.0]], 'selectors' => ['{{WRAPPER}} .swiper-button-next line, {{WRAPPER}} .swiper-button-prev line' => 'stroke-width: {{SIZE}};'], 'condition' => ['useNavigation' => 'yes']]);
        $this->add_control('pagination_tratteggio', ['label' => esc_html__('Dashed', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => '0'], 'range' => ['px' => ['max' => 50, 'min' => 0, 'step' => 1.0]], 'selectors' => ['{{WRAPPER}} .swiper-button-prev line, {{WRAPPER}} .swiper-button-next line, {{WRAPPER}} .swiper-button-prev polyline, {{WRAPPER}} .swiper-button-next polyline' => 'stroke-dasharray: {{SIZE}},{{SIZE}};'], 'condition' => ['useNavigation' => 'yes']]);
        $this->add_responsive_control('pagination_scale', ['label' => esc_html__('Scale', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => ''], 'tablet_default' => ['size' => ''], 'mobile_default' => ['size' => ''], 'range' => ['px' => ['max' => 2, 'min' => 0.1, 'step' => 0.01]], 'selectors' => ['{{WRAPPER}} .swiper-button-next, {{WRAPPER}} .swiper-button-prev' => 'transform: scale({{SIZE}});'], 'condition' => ['useNavigation' => 'yes']]);
        $this->add_responsive_control('pagination_position', ['label' => esc_html__('Horizontal Position', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => ''], 'tablet_default' => ['size' => ''], 'mobile_default' => ['size' => ''], 'size_units' => ['px'], 'range' => ['px' => ['max' => 100, 'min' => -100, 'step' => 1]], 'selectors' => ['{{WRAPPER}} .swiper-button-prev' => 'left: {{SIZE}}{{UNIT}};', '{{WRAPPER}} .swiper-button-next' => 'right: {{SIZE}}{{UNIT}};'], 'condition' => ['useNavigation' => 'yes']]);
        $this->add_responsive_control('pagination_position_v', ['label' => esc_html__('Vertical Position', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => 50, 'unit' => '%'], 'size_units' => ['%', 'px'], 'range' => ['%' => ['max' => 120, 'min' => -20, 'step' => 1], 'px' => ['max' => 200, 'min' => -200, 'step' => 1]], 'selectors' => ['{{WRAPPER}} .swiper-button-prev, {{WRAPPER}} .swiper-button-next' => 'top: {{SIZE}}{{UNIT}};'], 'condition' => ['useNavigation' => 'yes']]);
        // --------------------------------------------------- Pagination options ------
        $this->add_control('pagination_options', ['label' => esc_html__('Pagination options', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HEADING, 'separator' => 'before']);
        $this->add_responsive_control('usePagination', ['label' => esc_html__('Pagination', 'dynamic-content-for-elementor'), 'description' => esc_html__('Use the slide progression display system ("bullets", "fraction", "progress").', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => 'yes', 'frontend_available' => \true]);
        $this->add_control('pagination_type', ['label' => esc_html__('Pagination Type', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'options' => ['bullets' => esc_html__('Bullets', 'dynamic-content-for-elementor'), 'fraction' => esc_html__('Fraction', 'dynamic-content-for-elementor'), 'progress' => esc_html__('Progress', 'dynamic-content-for-elementor')], 'default' => 'bullets', 'frontend_available' => \true, 'condition' => ['usePagination' => 'yes']]);
        // ------------------------------------------------- Pagination Fraction Options
        $this->add_control('fraction_separator', ['label' => esc_html__('Fraction text separator', 'dynamic-content-for-elementor'), 'description' => esc_html__('The text separating the 2 numbers', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'frontend_available' => \true, 'default' => '/', 'condition' => ['usePagination' => 'yes', 'pagination_type' => 'fraction']]);
        $this->add_responsive_control('fraction_space', ['label' => esc_html__('Spacing', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => '4', 'unit' => 'px'], 'tablet_default' => ['unit' => 'px'], 'mobile_default' => ['unit' => 'px'], 'size_units' => ['px'], 'range' => ['px' => ['min' => -20, 'max' => 100]], 'selectors' => ['{{WRAPPER}} .swiper-pagination-fraction .separator' => 'margin: 0 {{SIZE}}{{UNIT}};'], 'condition' => ['usePagination' => 'yes', 'pagination_type' => 'fraction']]);
        $this->add_control('fraction_color', ['label' => esc_html__('Numbers color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'default' => '', 'selectors' => ['{{WRAPPER}} .swiper-pagination-fraction > *' => 'color: {{VALUE}};'], 'condition' => ['usePagination' => 'yes', 'pagination_type' => 'fraction']]);
        $this->add_group_control(Group_Control_Typography::get_type(), ['name' => 'fraction_typography', 'label' => esc_html__('Numbers Typography', 'dynamic-content-for-elementor'), 'selector' => '{{WRAPPER}} .swiper-pagination-fraction > *', 'condition' => ['usePagination' => 'yes', 'pagination_type' => 'fraction']]);
        $this->add_control('fraction_current_color', ['label' => esc_html__('Current number color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'default' => '', 'selectors' => ['{{WRAPPER}} .swiper-pagination-fraction .swiper-pagination-current' => 'color: {{VALUE}};'], 'condition' => ['usePagination' => 'yes', 'pagination_type' => 'fraction']]);
        $this->add_group_control(Group_Control_Typography::get_type(), ['name' => 'fraction_typography_current', 'label' => esc_html__('Current number typography', 'dynamic-content-for-elementor'), 'default' => '', 'selector' => '{{WRAPPER}} .swiper-pagination-fraction .swiper-pagination-current', 'condition' => ['usePagination' => 'yes', 'pagination_type' => 'fraction']]);
        $this->add_control('fraction_separator_color', ['label' => esc_html__('Separator color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'default' => '', 'selectors' => ['{{WRAPPER}} .swiper-pagination-fraction .separator' => 'color: {{VALUE}};'], 'condition' => ['usePagination' => 'yes', 'pagination_type' => 'fraction']]);
        $this->add_group_control(Group_Control_Typography::get_type(), ['name' => 'fraction_typography_separator', 'label' => esc_html__('Typography separator', 'dynamic-content-for-elementor'), 'default' => '', 'selector' => '{{WRAPPER}} .swiper-pagination-fraction .separator', 'condition' => ['usePagination' => 'yes', 'pagination_type' => 'fraction']]);
        // ------------------------------------------------- Pagination Bullets Options
        $this->add_responsive_control('bullets_space', ['label' => esc_html__('Space', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => '5', 'unit' => 'px'], 'tablet_default' => ['unit' => 'px'], 'mobile_default' => ['unit' => 'px'], 'size_units' => ['px'], 'range' => ['px' => ['min' => 0, 'max' => 100]], 'selectors' => ['{{WRAPPER}} .swiper-pagination-bullets .swiper-pagination-bullet' => 'margin: 0 {{SIZE}}{{UNIT}};'], 'condition' => ['usePagination' => 'yes', 'pagination_type' => 'bullets']]);
        $this->add_responsive_control('pagination_bullets', ['label' => esc_html__('Bullets size', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => '8', 'unit' => 'px'], 'tablet_default' => ['unit' => 'px'], 'mobile_default' => ['unit' => 'px'], 'size_units' => ['px'], 'range' => ['px' => ['min' => 0, 'max' => 100]], 'selectors' => ['{{WRAPPER}} .swiper-pagination-bullets .swiper-pagination-bullet' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};'], 'condition' => ['usePagination' => 'yes', 'pagination_type' => 'bullets']]);
        $this->add_responsive_control('pagination_posy', ['label' => esc_html__('Shift', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => '10', 'unit' => 'px'], 'tablet_default' => ['unit' => 'px'], 'mobile_default' => ['unit' => 'px'], 'size_units' => ['px'], 'range' => ['px' => ['min' => -160, 'max' => 160]], 'selectors' => ['{{WRAPPER}} .swiper-pagination' => ' bottom: {{SIZE}}{{UNIT}};'], 'condition' => ['usePagination' => 'yes', 'pagination_type' => ['bullets', 'fraction']]]);
        $this->add_responsive_control('pagination_space', ['label' => esc_html__('Space', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => '10', 'unit' => 'px'], 'tablet_default' => ['unit' => 'px'], 'mobile_default' => ['unit' => 'px'], 'size_units' => ['px'], 'range' => ['px' => ['min' => 0, 'max' => 60]], 'selectors' => ['{{WRAPPER}} .swiper-pagination' => 'padding-right: {{SIZE}}{{UNIT}}; padding-left: {{SIZE}}{{UNIT}};'], 'condition' => ['usePagination' => 'yes', 'pagination_type' => ['bullets', 'fraction']]]);
        $this->add_control('bullets_color', ['label' => esc_html__('Bullets Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'default' => '', 'selectors' => ['{{WRAPPER}} .swiper-pagination-bullets .swiper-pagination-bullet' => 'background-color: {{VALUE}};'], 'condition' => ['usePagination' => 'yes', 'pagination_type' => 'bullets']]);
        $this->add_group_control(Group_Control_Border::get_type(), ['name' => 'border_bullet', 'label' => esc_html__('Bullets border', 'dynamic-content-for-elementor'), 'selector' => '{{WRAPPER}} .swiper-pagination-bullets .swiper-pagination-bullet', 'condition' => ['usePagination' => 'yes', 'pagination_type' => 'bullets']]);
        $this->add_responsive_control('current_bullet', ['label' => esc_html__('Active bullet size', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => '', 'unit' => 'px'], 'tablet_default' => ['unit' => 'px'], 'mobile_default' => ['unit' => 'px'], 'size_units' => ['px'], 'range' => ['px' => ['min' => 0, 'max' => 100]], 'selectors' => ['{{WRAPPER}} .swiper-pagination-bullets .swiper-pagination-bullet-active' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};', '{{WRAPPER}} .swiper-pagination.swiper-pagination-bullets' => 'height: {{SIZE}}{{UNIT}}'], 'condition' => ['usePagination' => 'yes', 'pagination_type' => 'bullets']]);
        $this->add_control('current_bullet_color', ['label' => esc_html__('Active bullet color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'default' => '', 'selectors' => ['{{WRAPPER}} .swiper-pagination-bullets .swiper-pagination-bullet-active' => 'background-color: {{VALUE}};'], 'condition' => ['usePagination' => 'yes', 'pagination_type' => 'bullets']]);
        $this->add_group_control(Group_Control_Border::get_type(), ['name' => 'border_current_bullet', 'label' => esc_html__('Active bullet border', 'dynamic-content-for-elementor'), 'selector' => '{{WRAPPER}} .swiper-pagination-bullets .swiper-pagination-bullet-active', 'condition' => ['usePagination' => 'yes', 'pagination_type' => 'bullets']]);
        $this->add_control('progress_color', ['label' => esc_html__('Progress color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'default' => '', 'selectors' => ['{{WRAPPER}} .swiper-pagination-progress' => 'background-color: {{VALUE}};'], 'condition' => ['usePagination' => 'yes', 'pagination_type' => 'progress']]);
        $this->add_control('progressbar_color', ['label' => esc_html__('Progressbar color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'default' => '', 'selectors' => ['{{WRAPPER}} .swiper-pagination-progress .swiper-pagination-progressbar' => 'background-color: {{VALUE}};'], 'condition' => ['usePagination' => 'yes', 'pagination_type' => 'progress']]);
        $this->add_responsive_control('h_align_pagination', ['label' => esc_html__('Horizontal Alignment', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CHOOSE, 'options' => ['left' => ['title' => esc_html__('Left', 'dynamic-content-for-elementor'), 'icon' => 'eicon-h-align-left'], 'center' => ['title' => esc_html__('Center', 'dynamic-content-for-elementor'), 'icon' => 'eicon-h-align-center'], 'right' => ['title' => esc_html__('Right', 'dynamic-content-for-elementor'), 'icon' => 'eicon-h-align-right']], 'selectors' => ['{{WRAPPER}} .swiper-pagination' => 'text-align: {{VALUE}};'], 'condition' => ['usePagination' => 'yes', 'pagination_type' => ['bullets', 'fraction']]]);
        // -------------------------------- Autoplay ------
        $this->add_control('autoplay_options', ['label' => esc_html__('Autoplay options', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HEADING, 'separator' => 'before']);
        $this->add_control('useAutoplay', ['label' => esc_html__('Use Autoplay', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'frontend_available' => \true]);
        $this->add_control('autoplay', ['label' => esc_html__('Autoplay Delay (ms)', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::NUMBER, 'default' => 3000, 'min' => 0, 'max' => 7000, 'step' => 100, 'frontend_available' => \true, 'condition' => ['useAutoplay' => 'yes']]);
        $this->add_control('autoplayStopOnLast', ['label' => esc_html__('Autoplay stop on last slide', 'dynamic-content-for-elementor'), 'description' => esc_html__('Enable this parameter and autoplay will be stopped when it reaches the last slide (has no effect in loop mode)', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'frontend_available' => \true, 'condition' => ['autoplay!' => '']]);
        $this->add_control('autoplayDisableOnInteraction', ['label' => esc_html__('Autoplay Disable on interaction', 'dynamic-content-for-elementor'), 'description' => esc_html__('Set to "false" and autoplay will not be disabled after user interactions (swipes), it will be restarted every time after interaction', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => 'yes', 'frontend_available' => \true, 'condition' => ['autoplay!' => '']]);
        // -------------------------------- Keyboard ------
        $this->add_control('keyboard_options', ['label' => esc_html__('Keyboard / Mousewheel', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HEADING, 'separator' => 'before']);
        $this->add_control('keyboardControl', ['label' => esc_html__('Keyboard Control', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'frontend_available' => \true]);
        $this->add_control('mousewheelControl', ['label' => esc_html__('Mousewheel Control', 'dynamic-content-for-elementor'), 'description' => esc_html__('Enables navigation through slides using mouse wheel', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'frontend_available' => \true]);
        // -------------------------------- Ciclo ------
        $this->add_control('cicleloop_options', ['label' => esc_html__('Cicle / Loop', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HEADING, 'separator' => 'before']);
        $this->add_control('loop', ['label' => esc_html__('Loop', 'dynamic-content-for-elementor'), 'description' => esc_html__('Set it to true to enable continuous loop mode', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'frontend_available' => \true]);
        // -------------------------------- Special options ---------
        $this->add_control('special_options', ['label' => esc_html__('Specials options', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HEADING, 'separator' => 'before']);
        $this->add_control('autoHeight', ['label' => esc_html__('Auto Height', 'dynamic-content-for-elementor'), 'description' => esc_html__('Set to true and slider wrapper will adopt its height to the height of the currently active slide', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'frontend_available' => \true]);
        $this->add_control('grabCursor', ['label' => esc_html__('Grab Cursor', 'dynamic-content-for-elementor'), 'description' => esc_html__('This option may improve desktop usability. If true, user will see the "grab" cursor when hover on Swiper', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'frontend_available' => \true]);
        $this->end_controls_section();
        // ACCORDION
        $this->start_controls_section('section_toggle_style', ['label' => $this->get_title(), 'tab' => Controls_Manager::TAB_STYLE]);
        $this->add_group_control(Group_Control_Typography::get_type(), ['name' => 'typography', 'selector' => '{{WRAPPER}} ']);
        $this->add_control('color', ['label' => esc_html__('Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}}' => 'color: {{VALUE}};']]);
        $this->add_group_control(Group_Control_Image_Size::get_type(), ['name' => 'imgsize', 'label' => esc_html__('Image Size', 'dynamic-content-for-elementor'), 'default' => 'large', 'render_type' => 'template', 'condition' => ['dce_acf_repeater_mode' => 'subfields']]);
        $this->add_responsive_control('alignment', ['label' => esc_html__('Global Alignment', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CHOOSE, 'toggle' => \true, 'options' => ['left' => ['title' => esc_html__('Left', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-left'], 'center' => ['title' => esc_html__('Center', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-center'], 'right' => ['title' => esc_html__('Right', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-right']], 'selectors' => ['{{WRAPPER}}' => 'text-align: {{VALUE}};'], 'condition' => ['dce_acf_repeater_mode!' => 'template']]);
        $this->add_responsive_control('th_alignment', ['label' => esc_html__('Table - Heading Alignment', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CHOOSE, 'toggle' => \true, 'options' => ['left' => ['title' => esc_html__('Left', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-left'], 'center' => ['title' => esc_html__('Center', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-center'], 'right' => ['title' => esc_html__('Right', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-right']], 'selectors' => ['{{WRAPPER}} th' => 'text-align: {{VALUE}};'], 'condition' => ['dce_acf_repeater_format' => 'table']]);
        $this->add_responsive_control('td_alignment', ['label' => esc_html__('Table - Rows Alignment', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CHOOSE, 'toggle' => \true, 'options' => ['left' => ['title' => esc_html__('Left', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-left'], 'center' => ['title' => esc_html__('Center', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-center'], 'right' => ['title' => esc_html__('Right', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-right']], 'selectors' => ['{{WRAPPER}} td' => 'text-align: {{VALUE}};'], 'condition' => ['dce_acf_repeater_format' => 'table']]);
        $this->add_responsive_control('dce_acf_repeater_field_col', ['label' => esc_html__('Col space', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'range' => ['px' => ['max' => 50, 'min' => 0, 'step' => 1.0]], 'selectors' => ['{{WRAPPER}} .dce-acf-repeater-item' => 'padding-right: calc( {{SIZE}}{{UNIT}}/2 ); padding-left: calc( {{SIZE}}{{UNIT}}/2 );', '{{WRAPPER}} .dce-acf-repeater' => 'margin-left: calc( -{{SIZE}}{{UNIT}}/2 ); margin-right: calc( -{{SIZE}}{{UNIT}}/2 );'], 'condition' => ['dce_acf_repeater_format' => ['grid', 'masonry']]]);
        $this->add_responsive_control('dce_acf_repeater_field_row', ['label' => esc_html__('Row space', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => ''], 'tablet_default' => ['size' => ''], 'mobile_default' => ['size' => ''], 'range' => ['px' => ['max' => 50, 'min' => 0, 'step' => 1.0]], 'selectors' => ['{{WRAPPER}} .dce-acf-repeater-item' => 'padding-bottom: {{SIZE}}{{UNIT}};'], 'condition' => ['dce_acf_repeater_format' => ['grid', 'masonry']]]);
        $this->end_controls_section();
        $this->start_controls_section('section_title_style', ['label' => esc_html__('Accordion', 'dynamic-content-for-elementor'), 'tab' => Controls_Manager::TAB_STYLE, 'condition' => ['dce_acf_repeater_format' => 'accordion']]);
        $this->add_control('section_toggle_style_title', ['label' => esc_html__('Heading', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HEADING, 'separator' => 'before']);
        $this->add_control('title_background', ['label' => esc_html__('Background', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .acc_section > *:not(.acc_content)' => 'background-color: {{VALUE}};']]);
        $this->add_control('title_active_background', ['label' => esc_html__('Active Background', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .acc_active > *:not(.acc_content)' => 'background-color: {{VALUE}};']]);
        $this->add_control('title_color', ['label' => esc_html__('Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .acc_button *' => 'color: {{VALUE}};']]);
        $this->add_control('tab_active_color', ['label' => esc_html__('Active Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .acc_active .acc_button *' => 'color: {{VALUE}};']]);
        $this->add_group_control(Group_Control_Typography::get_type(), ['name' => 'title_typography', 'selector' => '{{WRAPPER}} .acc_button *']);
        $this->add_responsive_control('title_padding', ['label' => esc_html__('Padding', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => ['px', 'em', '%'], 'selectors' => ['{{WRAPPER}} .acc_button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};']]);
        $this->add_control('tab_heading', ['label' => esc_html__('Tab', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HEADING, 'separator' => 'before']);
        $this->add_group_control(Group_Control_Border::get_type(), ['name' => 'border_width', 'label' => esc_html__('Border Width', 'dynamic-content-for-elementor'), 'selector' => '{{WRAPPER}} .acc_section']);
        $this->add_responsive_control('tab_space', ['label' => esc_html__('Spacing', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'range' => ['px' => ['min' => 0, 'max' => 100]], 'default' => ['unit' => 'px', 'size' => 0], 'selectors' => ['{{WRAPPER}} .acc_section:not(:last-child)' => 'margin-bottom: {{SIZE}}{{UNIT}};']]);
        $this->add_control('section_toggle_style_icon', ['label' => esc_html__('Icons', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HEADING, 'separator' => 'before']);
        $this->add_control('icon_align', ['label' => esc_html__('Alignment', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CHOOSE, 'options' => ['left' => ['title' => esc_html__('Start', 'dynamic-content-for-elementor'), 'icon' => 'eicon-h-align-left'], 'right' => ['title' => esc_html__('End', 'dynamic-content-for-elementor'), 'icon' => 'eicon-h-align-right']], 'default' => is_rtl() ? 'right' : 'left', 'toggle' => \false]);
        $this->add_control('icon_color', ['label' => esc_html__('Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .acc_button i:before' => 'color: {{VALUE}};', '{{WRAPPER}} .acc_button svg' => 'fill: {{VALUE}};']]);
        $this->add_control('icon_active_color', ['label' => esc_html__('Active Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .acc_active .acc_button i:before' => 'color: {{VALUE}};', '{{WRAPPER}} .acc_active .acc_button svg' => 'fill: {{VALUE}};']]);
        $this->add_responsive_control('icon_margin', ['label' => esc_html__('Margin', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => ['px', '%', 'em'], 'selectors' => ['{{WRAPPER}} .acc_button .icon' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};', '{{WRAPPER}} .acc_button .icon-active' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};']]);
        $this->add_responsive_control('icon_space', ['label' => esc_html__('Spacing', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'range' => ['px' => ['min' => 0, 'max' => 100]], 'selectors' => ['{{WRAPPER}} .acc_button .icon' => 'margin-right: {{SIZE}}{{UNIT}}; margin-left: {{SIZE}}{{UNIT}};', '{{WRAPPER}} .acc_button .icon-active' => 'margin-right: {{SIZE}}{{UNIT}}; margin-left: {{SIZE}}{{UNIT}};']]);
        $this->add_control('focus_heading', ['label' => esc_html__('Focus', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HEADING, 'separator' => 'before']);
        $this->add_control('focus_background_color', ['label' => esc_html__('Background', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .acc_section > *:not(.acc_content):has(.acc_button:focus)' => 'background-color: {{VALUE}};', '{{WRAPPER}} .acc_button:focus' => 'outline: none;']]);
        $this->add_control('focus_border_color', ['label' => esc_html__('Border Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .acc_button:focus' => 'border-left: 3px solid {{VALUE}};']]);
        $this->add_responsive_control('focus_border_width', ['label' => esc_html__('Border Width', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'range' => ['px' => ['min' => 0, 'max' => 10]], 'selectors' => ['{{WRAPPER}} .acc_button:focus' => 'border-left-width: {{SIZE}}{{UNIT}};']]);
        $this->add_control('section_toggle_style_content', ['label' => esc_html__('Content', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HEADING, 'separator' => 'before']);
        $this->add_control('content_background_color', ['label' => esc_html__('Background', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .acc_content' => 'background-color: {{VALUE}};']]);
        $this->add_control('content_color', ['label' => esc_html__('Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .acc_content' => 'color: {{VALUE}};']]);
        $this->add_group_control(Group_Control_Typography::get_type(), ['name' => 'content_typography', 'selector' => '{{WRAPPER}} .acc_content']);
        $this->add_responsive_control('content_padding', ['label' => esc_html__('Padding', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => ['px', 'em', '%'], 'selectors' => ['{{WRAPPER}} .acc_content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};']]);
        $this->add_responsive_control('content_margin', ['label' => esc_html__('Margin', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => ['px', 'em', '%'], 'selectors' => ['{{WRAPPER}} .acc_content' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};']]);
        $this->add_group_control(Group_Control_Border::get_type(), ['name' => 'content_border', 'label' => esc_html__('Border', 'dynamic-content-for-elementor'), 'selector' => '{{WRAPPER}} .acc_content']);
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
        if (empty($settings['dce_acf_repeater'])) {
            if (\Elementor\Plugin::$instance->editor->is_edit_mode()) {
                Helper::notice(\false, esc_html__('Select an ACF Repeater field', 'dynamic-content-for-elementor'));
            }
            return;
        }
        $id_page = Helper::get_acf_source_id($settings['acf_repeater_from'], $settings['other_post_source'] ?? \false);
        global $repeater_counter;
        if (empty($repeater_counter)) {
            $repeater_counter = 1;
        } else {
            ++$repeater_counter;
        }
        $repeater = get_field_object($settings['dce_acf_repeater'], $id_page);
        // If my_fields is empty probably we are on a nested repeater, so get the object from the subfield
        if (empty($repeater)) {
            $repeater = get_sub_field_object($settings['dce_acf_repeater'], $id_page);
        }
        $labels = [];
        if (!empty($repeater['sub_fields'])) {
            // If in subfields mode, only include labels for selected subfields
            if ($settings['dce_acf_repeater_mode'] === 'subfields' && !empty($settings['dce_acf_repeater_subfields'])) {
                foreach ($settings['dce_acf_repeater_subfields'] as $selected_subfield) {
                    $field_name = $selected_subfield['dce_acf_repeater_field_name'];
                    foreach ($repeater['sub_fields'] as $subfield) {
                        if ($subfield['name'] === $field_name) {
                            $labels[] = $subfield['label'];
                            break;
                        }
                    }
                }
            } else {
                // For other modes (html, template), include all subfield labels
                foreach ($repeater['sub_fields'] as $subfield) {
                    $labels[] = $subfield['label'];
                }
            }
        }
        $repeater_count = 0;
        if (isset($repeater['value']) && (\is_array($repeater['value']) || \is_object($repeater['value']))) {
            $repeater_count = \count($repeater['value']);
        }
        $paginations = $this->get_pagination($settings['dce_acf_repeater_pagination'], $repeater_count);
        // Inizialize layout
        $this->add_render_attribute('container', 'class', 'dce-acf-repeater');
        echo '<div ' . $this->get_render_attribute_string('container') . '>';
        if ($settings['dce_acf_repeater_mode'] == 'subfields' && empty($settings['dce_acf_repeater_subfields']) && \Elementor\Plugin::$instance->editor->is_edit_mode()) {
            Helper::notice(\false, esc_html__('Select at least one sub field', 'dynamic-content-for-elementor'));
        }
        if (\Elementor\Plugin::$instance->editor->is_edit_mode() && $settings['dce_acf_repeater_format'] === 'table' && \in_array($settings['dce_acf_repeater_mode'], ['html', 'template'], \true)) {
            Helper::notice(\false, esc_html__('Table skin is not recommended with Dynamic HTML or Template mode. Consider using Subfields mode for better table structure.', 'dynamic-content-for-elementor'));
        }
        $this->add_render_attribute('wrapper', 'class', 'dce-acf-repeater-' . $settings['dce_acf_repeater_format']);
        if ($settings['dce_acf_repeater_format'] == 'slider_carousel') {
            $swiper_class = 'swiper';
            $this->add_render_attribute('wrapper', 'class', $swiper_class);
            $this->add_render_attribute('wrapper', 'class', 'swiper-container-horizontal');
            $this->add_render_attribute('wrapper', 'counter-id', $repeater_counter);
        }
        if ($settings['dce_acf_repeater_format'] == 'list' && $settings['dce_acf_repeater_list'] == 'ul' && !empty($settings['selected_icon_ul']['value'])) {
            $this->add_render_attribute('wrapper', 'class', 'dce-no-list');
        }
        if ($settings['dce_acf_repeater_format'] == 'table' && !empty($settings['dce_acf_repeater_datatables'])) {
            $this->add_render_attribute('wrapper', 'class', 'dce-datatable');
        }
        switch ($settings['dce_acf_repeater_format']) {
            case 'list':
                echo '<' . Helper::validate_html_tag($settings['dce_acf_repeater_list']) . ' ' . $this->get_render_attribute_string('wrapper') . '>';
                break;
            case 'grid':
            case 'masonry':
                echo '<div ' . $this->get_render_attribute_string('wrapper') . '>';
                break;
            case 'slider_carousel':
                echo '<div ' . $this->get_render_attribute_string('wrapper') . '><div class="swiper-wrapper">';
                break;
            case 'table':
                echo '<table ' . $this->get_render_attribute_string('wrapper') . '>';
                if ($settings['dce_acf_repeater_thead']) {
                    echo '<thead>';
                    if ($settings['dce_acf_repeater_mode'] !== 'subfields' && !empty($settings['dce_acf_repeater_thead_custom'])) {
                        echo Plugin::instance()->text_templates->expand_shortcodes_or_callback($settings['dce_acf_repeater_thead_custom_html'], [], function ($str) {
                            return do_shortcode(Helper::get_dynamic_value($str));
                        });
                    } else {
                        foreach ($labels as $label) {
                            echo '<th>' . esc_html($label) . '</th>';
                        }
                    }
                    echo '</thead>';
                }
                break;
            case 'accordion':
                ?>
				<ul class="dce-acf-repeater-wrapper">
				<?php 
                break;
        }
        // Iterate all rows
        while (have_rows($settings['dce_acf_repeater'], $id_page)) {
            the_row();
            // Build row data from ACF's loop state to stay in sync with have_rows().
            // Using $repeater['value'][index] can desync with the loop when WPML
            // translates the post, causing offset data in carousels.
            $sub_fields_row = [];
            if (!empty($repeater['sub_fields'])) {
                foreach ($repeater['sub_fields'] as $sub_field_def) {
                    $sub_fields_row[$sub_field_def['name']] = get_sub_field($sub_field_def['name']) ?? '';
                }
            }
            // Filter
            $sub_fields_to_filter = $sub_fields_row ?: '';
            if (!empty($settings['filter']) && \is_array($sub_fields_to_filter) && !$this->is_filter_satisfied($sub_fields_to_filter)) {
                continue;
            }
            // Render a single sub-field
            if (empty($paginations) || \in_array(get_row_index(), $paginations)) {
                switch ($settings['dce_acf_repeater_format']) {
                    case 'list':
                        echo '<li class="dce-acf-repeater-item">';
                        if ($settings['dce_acf_repeater_list'] == 'ul' && !empty($settings['selected_icon_ul']['value'])) {
                            Icons_Manager::render_icon($settings['selected_icon_ul'], ['aria-hidden' => 'true']);
                        }
                        break;
                    case 'grid':
                        echo '<div class="dce-acf-repeater-item">';
                        break;
                    case 'masonry':
                        echo '<div class="dce-acf-repeater-item">';
                        break;
                    case 'slider_carousel':
                        echo '<div class="dce-acf-repeater-item swiper-slide">';
                        break;
                    case 'table':
                        echo '<tr>';
                        break;
                    case 'accordion':
                        $row_index = get_row_index();
                        $is_active = $this->get_is_active($row_index);
                        $this->add_render_attribute(['accordion_section' . $row_index => ['class' => ['dce-acf-repeater-item', 'acc_section', 'item'], 'id' => $this->get_section_id($row_index)]]);
                        if ($is_active) {
                            $this->add_render_attribute('accordion_section' . $row_index, 'class', 'acc_active');
                        }
                        $this->add_render_attribute(['accordion_content' . $row_index => ['class' => 'acc_content', 'id' => $this->get_content_id($row_index), 'aria-labelledby' => $this->get_button_id($row_index), 'aria-hidden' => $is_active ? 'false' : 'true']]);
                        ?>
						<li <?php 
                        echo $this->get_render_attribute_string('accordion_section' . $row_index);
                        ?>>
							<?php 
                        $this->render_accordion_heading($sub_fields_row);
                        ?>
							<div <?php 
                        echo $this->get_render_attribute_string('accordion_content' . $row_index);
                        ?>>
						<?php 
                }
                // Dynamic HTML
                if ($settings['dce_acf_repeater_mode'] == 'html') {
                    $text = $settings['dce_acf_repeater_html'];
                    // Tokens [ROW]
                    // Dynamic Shortcodes {data:row}
                    echo Plugin::instance()->text_templates->expand_shortcodes_or_callback($text, ['row' => $sub_fields_row, 'row-index' => get_row_index()], function ($str) use($sub_fields_row) {
                        if ('[ROW:id]' === $str) {
                            return get_row_index();
                        }
                        return do_shortcode(Tokens::replace_var_tokens($str, 'ROW', $sub_fields_row));
                    });
                } elseif ($settings['dce_acf_repeater_mode'] == 'template') {
                    $template_id = $settings['dce_acf_repeater_template'];
                    if ($settings['dce_acf_repeater_template_2_enable'] && get_row_index() % 2) {
                        $template_id = $settings['dce_acf_repeater_template_2_id'];
                    }
                    $template_system = \DynamicContentForElementor\Plugin::instance()->template_system;
                    echo $template_system->build_elementor_template_special(['id' => $template_id, 'acf_row' => get_row_index(), 'inlinecss' => \Elementor\Plugin::$instance->editor->is_edit_mode()]);
                } else {
                    foreach ($settings['dce_acf_repeater_subfields'] as $key => $value) {
                        $sub_field = get_sub_field($value['dce_acf_repeater_field_name']);
                        $sub_field_settings = Helper::get_acf_field_settings($value['dce_acf_repeater_field_name']);
                        $sub_field_link = '';
                        if ($value['dce_acf_repeater_acfield_link']) {
                            // ACF Subfield
                            $sub_field_link = get_sub_field($value['dce_acf_repeater_acfield_link']);
                            // ACF Field
                            if (!$sub_field_link) {
                                $sub_field_link = \get_field($value['dce_acf_repeater_acfield_link']);
                            }
                        }
                        if (!empty($sub_field) || $sub_field === '0' || $sub_field === 0) {
                            $field_type = '';
                            if (\is_array($sub_field_settings)) {
                                $field_type = $sub_field_settings['type'];
                            }
                            $subfield_value = '';
                            // phpstan
                            switch ($field_type) {
                                case 'wysiwyg':
                                    $subfield_value = wpautop($sub_field);
                                    break;
                                case 'image':
                                    $image_src = '';
                                    $image_alt = '';
                                    if (\is_numeric($sub_field)) {
                                        $image_src = Group_Control_Image_Size::get_attachment_image_src($sub_field, 'image', $value);
                                        $image_alt = esc_attr(get_post_meta($sub_field, '_wp_attachment_image_alt', \true));
                                    } elseif (\is_array($sub_field)) {
                                        $image_src = Group_Control_Image_Size::get_attachment_image_src($sub_field['ID'], 'image', $value);
                                        $image_alt = esc_attr($sub_field['alt']);
                                    } elseif (\is_string($sub_field)) {
                                        $image_src = $sub_field;
                                        $image_alt = '';
                                    }
                                    if ($image_src) {
                                        $subfield_value = '<img src="' . esc_url($image_src) . '" alt="' . esc_attr($image_alt) . '" />';
                                    }
                                    break;
                                case 'date_time_picker':
                                case 'date_picker':
                                case 'url':
                                case 'text':
                                case 'textarea':
                                default:
                                    $subfield_value = Helper::to_readable_string($sub_field, \true);
                            }
                            if (!empty($sub_field_link) && !empty($value['dce_acf_repeater_enable_link'])) {
                                $targetLink = '';
                                if (!empty($value['dce_acf_repeater_target_link'])) {
                                    $targetLink .= ' target="_blank"';
                                }
                                if (!empty($value['dce_acf_repeater_nofollow_link'])) {
                                    $targetLink .= ' rel="nofollow"';
                                }
                                // Determine the correct URL from the link field
                                $link_url = '';
                                if ($sub_field_link instanceof \WP_Post) {
                                    // Post Object field returning a post object
                                    $link_url = get_permalink($sub_field_link);
                                } elseif ($sub_field_link instanceof \WP_Term) {
                                    // Taxonomy field returning a term object
                                    $link_url = get_term_link($sub_field_link);
                                    if (is_wp_error($link_url)) {
                                        $link_url = '';
                                    }
                                } elseif ($sub_field_link instanceof \WP_User) {
                                    // User field returning a user object
                                    $link_url = get_author_posts_url($sub_field_link->ID);
                                } elseif (\is_array($sub_field_link)) {
                                    // Link field returns array, or image/file field returns array
                                    if (isset($sub_field_link['url'])) {
                                        $link_url = $sub_field_link['url'];
                                        // For Link field, also handle target
                                        if (isset($sub_field_link['target']) && '_blank' === $sub_field_link['target'] && empty($value['dce_acf_repeater_target_link'])) {
                                            $targetLink .= ' target="_blank"';
                                        }
                                    }
                                } elseif (\is_string($sub_field_link)) {
                                    // URL field, page_link, or direct URL string
                                    $link_url = $sub_field_link;
                                } elseif (\is_numeric($sub_field_link)) {
                                    // Post Object/Taxonomy returning just an ID
                                    $post = get_post((int) $sub_field_link);
                                    if ($post) {
                                        $link_url = get_permalink($post);
                                    } else {
                                        $term = get_term((int) $sub_field_link);
                                        if ($term && !is_wp_error($term)) {
                                            $term_link = get_term_link($term);
                                            if (!is_wp_error($term_link)) {
                                                $link_url = $term_link;
                                            }
                                        }
                                    }
                                }
                                // Only build the link if we successfully got a URL and have text to display
                                if (!empty($link_url) && !empty($subfield_value)) {
                                    $subfield_value = '<a href="' . esc_url($link_url) . '"' . $targetLink . '>' . $subfield_value . '</a>';
                                }
                            }
                            $subfield_value = Helper::to_readable_string($subfield_value);
                            if ($value['dce_acf_repeater_field_tag']) {
                                $subfield_value = '<' . \DynamicContentForElementor\Helper::validate_html_tag($value['dce_acf_repeater_field_tag']) . ' class="' . esc_attr('repeater-item elementor-repeater-item-' . $value['_id']) . '">' . $subfield_value . '</' . \DynamicContentForElementor\Helper::validate_html_tag($value['dce_acf_repeater_field_tag']) . '>';
                            }
                            if ($settings['dce_acf_repeater_format'] == 'table') {
                                echo '<td>';
                            }
                            echo $subfield_value;
                            if ($settings['dce_acf_repeater_format'] == 'table') {
                                echo '</td>';
                            }
                        } elseif (empty($sub_field) && $settings['dce_acf_repeater_format'] == 'table') {
                            echo '<td></td>';
                        }
                    }
                }
                switch ($settings['dce_acf_repeater_format']) {
                    case '':
                        if (get_row_index() < $repeater_count) {
                            echo wp_kses_post($settings['dce_acf_repeater_separator']);
                        }
                        break;
                    case 'list':
                        echo '</li>';
                        break;
                    case 'grid':
                    case 'masonry':
                    case 'slider_carousel':
                        echo '</div>';
                        break;
                    case 'accordion':
                        echo '</div></li>';
                        break;
                    case 'table':
                        echo '</tr>';
                }
            }
        }
        switch ($settings['dce_acf_repeater_format']) {
            case 'list':
                echo '</' . Helper::validate_html_tag($settings['dce_acf_repeater_list']) . '>';
                break;
            case 'grid':
            case 'masonry':
                echo '</div>';
                break;
            case 'accordion':
                echo '</ul>';
                break;
            case 'slider_carousel':
                echo '</div></div>';
                break;
            case 'table':
                echo '</table>';
        }
        // NOTE the pagination and navigation for the swiper is outside its container so I can move the elements around as I please, since the container is in overflow: hidden, and if they were inside (as by default) they would hide outside the area.
        if ($settings['usePagination']) {
            // Add Pagination
            echo '<div class="swiper-container-horizontal"><div class="' . esc_attr('swiper-pagination pagination-' . $this->get_id() . ' pagination-' . $repeater_counter) . '"></div></div>';
        }
        if ($settings['useNavigation']) {
            // Add Arrows
            echo '<div class="' . esc_attr('swiper-button-prev prev-' . $this->get_id() . ' prev-' . $repeater_counter) . '" role="button" tabindex="0" aria-label="' . esc_attr__('Previous slide', 'dynamic-content-for-elementor') . '"><svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
										width="85.039px" height="85.039px" viewBox="378.426 255.12 85.039 85.039" enable-background="new 378.426 255.12 85.039 85.039"
										xml:space="preserve">
										<line fill="none" stroke="#000000" stroke-width="1.3845" stroke-dasharray="0,0" stroke-miterlimit="10" x1="382.456" y1="298.077" x2="458.375" y2="298.077"/>
										<polyline fill="none" stroke="#000000" stroke-width="1.3845" stroke-dasharray="0,0" stroke-miterlimit="10" points="416.287,331.909,382.456,298.077
										416.287,264.245 "/>
										</svg></div>';
            echo '<div class="' . esc_attr('swiper-button-next next-' . $this->get_id() . ' next-' . $repeater_counter) . '" role="button" tabindex="0" aria-label="' . esc_attr__('Next slide', 'dynamic-content-for-elementor') . '"><svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
										width="85.039px" height="85.039px" viewBox="378.426 255.12 85.039 85.039" enable-background="new 378.426 255.12 85.039 85.039"
										xml:space="preserve">
										<line fill="none" stroke="#000000" stroke-width="1.3845" stroke-miterlimit="10" x1="458.375" y1="298.077" x2="382.456" y2="298.077"/>
										<polyline fill="none" stroke="#000000" stroke-width="1.3845" stroke-miterlimit="10" points="424.543,264.245,458.375,298.077
										424.543,331.909 "/>
										</svg></div>';
        }
        echo '</div>';
    }
    public function get_pagination($pages, $count = 0)
    {
        $pages = Helper::str_to_array(',', $pages);
        $ret = array();
        if (!empty($pages)) {
            foreach ($pages as $key => $value) {
                switch ($value) {
                    case 'first':
                        $ret[] = 1;
                        break;
                    case 'last':
                        $ret[] = $count;
                        break;
                    case 'odd':
                        for ($i = 1; $i <= $count; $i++) {
                            if ($i % 2) {
                                $ret[] = $i;
                            }
                        }
                        break;
                    case 'even':
                        for ($i = 1; $i <= $count; $i++) {
                            if (!($i % 2)) {
                                $ret[] = $i;
                            }
                        }
                        break;
                    default:
                        if (\preg_match('/[1-9][n]/', $value)) {
                            for ($i = 1; $i <= $count; $i++) {
                                if (!($i % \intval($value))) {
                                    $ret[] = $i;
                                }
                            }
                        } else {
                            $range = \explode('-', $value, 2);
                            if (\count($range) == 2) {
                                $start = \intval(\reset($range));
                                $end = \intval(\end($range));
                                if ($start <= $end) {
                                    while ($start <= $end) {
                                        $ret[] = $start;
                                        ++$start;
                                    }
                                }
                            } else {
                                $ret[] = \intval($value);
                            }
                        }
                }
            }
        }
        return $ret;
    }
    /**
     * Get Section ID
     *
     * @param int $index
     * @return string
     */
    protected function get_section_id($index)
    {
        return 'dce-acf-repeater-section-' . $this->get_id() . '-' . $index;
    }
    /**
     * Get Button ID
     *
     * @param int $index
     * @return string
     */
    protected function get_button_id($index)
    {
        return 'dce-acf-repeater-button-' . $this->get_id() . '-' . $index;
    }
    /**
     * Get Content ID
     *
     * @param int $index
     * @return string
     */
    protected function get_content_id($index)
    {
        return 'dce-acf-repeater-content-' . $this->get_id() . '-' . $index;
    }
    /**
     * @param array<string,mixed> $sub_fields_row
     * @return void
     */
    protected function render_accordion_heading($sub_fields_row)
    {
        $settings = $this->get_settings_for_display();
        $row_index = get_row_index();
        $data_heading = $sub_fields_row;
        $title = Plugin::instance()->text_templates->expand_shortcodes_or_callback($settings['dce_acf_repeater_accordion_heading'], ['row' => $data_heading, 'row-index' => $row_index], function ($str) use($data_heading) {
            if ('[ROW:id]' === $str) {
                return get_row_index();
            }
            return Tokens::replace_var_tokens($str, 'ROW', $data_heading);
        });
        $html_tag = !empty($settings['dce_acf_repeater_accordion_heading_size']) ? Helper::validate_html_tag($settings['dce_acf_repeater_accordion_heading_size']) : 'h4';
        $this->add_render_attribute(['accordion_button' . $row_index => ['class' => 'acc_button', 'type' => 'button', 'id' => $this->get_button_id($row_index), 'aria-expanded' => $this->get_is_active($row_index) ? 'true' : 'false', 'aria-controls' => $this->get_content_id($row_index)]]);
        $this->add_render_attribute(['accordion_title' . $row_index => ['class' => 'accordion-title']]);
        ?>
		<?php 
        \printf('<%1$s>', $html_tag);
        ?>
		<button <?php 
        echo $this->get_render_attribute_string('accordion_button' . $row_index);
        ?>>
			<?php 
        $settings = $this->get_settings_for_display();
        $icon_align = $settings['icon_align'] ?? 'left';
        if ('right' !== $icon_align) {
            $this->render_heading_icon();
        }
        ?>
			<span <?php 
        echo $this->get_render_attribute_string('accordion_title' . $row_index);
        ?>>
				<?php 
        echo $title;
        ?>
			</span>
			<?php 
        if ('right' === $icon_align) {
            $this->render_heading_icon();
        }
        ?>
		</button>
		<?php 
        \printf('</%s>', $html_tag);
        ?>
		<?php 
    }
    /**
     * Check if accordion item should be active/open initially
     *
     * @param int $index
     * @return boolean
     */
    protected function get_is_active($index)
    {
        $settings = $this->get_settings_for_display();
        $accordion_start = $settings['dce_acf_repeater_accordion_start'] ?? 'none';
        switch ($accordion_start) {
            case 'first':
                return $index === 1;
            case 'custom':
                $custom_index = $settings['accordion_start_custom'] ?? 1;
                return $index === (int) $custom_index;
            case 'all':
                return \true;
            default:
                return \false;
        }
    }
    /**
     * Is Filter Satisfied
     *
     * @param array<string,mixed> $sub_fields
     * @return boolean
     */
    protected function is_filter_satisfied(array $sub_fields)
    {
        $settings = $this->get_settings_for_display();
        foreach ($settings['filters'] as $key => $filter) {
            if (!isset($sub_fields[$filter['filter_field']])) {
                /* translators: %1$s: name of the subfield that does not exist */
                Helper::notice(\false, \sprintf(esc_html__('Filter Error: the subfield %1$s doesn\'t exist', 'dynamic-content-for-elementor'), '<strong>' . $filter['filter_field'] . '</strong>'));
            }
            $field = $sub_fields[$filter['filter_field']] ?? '';
            $condition_satisfied = Helper::is_condition_satisfied($field, $filter['filter_operator'], $filter['filter_value'] ?? '');
            if ('AND' === $settings['filters_relationship'] && !$condition_satisfied) {
                return \false;
            }
            if ('OR' === $settings['filters_relationship'] && $condition_satisfied) {
                return \true;
            }
        }
        return 'AND' === $settings['filters_relationship'];
    }
    /**
     * @return void
     */
    public function print_element()
    {
        parent::print_element();
        $settings = $this->get_settings_for_display();
        $format = $settings['dce_acf_repeater_format'] ?? '';
        $scripts_to_dequeue = [
            'imagesloaded' => !\in_array($format, ['grid', 'masonry']),
            // 'swiper' => $format !== 'slider_carousel',
            'jquery-masonry' => $format !== 'masonry',
            'dce-accordionjs' => $format !== 'accordion',
            'dce-datatables' => !($format === 'table' && !empty($settings['dce_acf_repeater_datatables'])),
        ];
        $styles_to_dequeue = ['dce-datatables' => !($format === 'table' && !empty($settings['dce_acf_repeater_datatables'])), 'dce-accordionjs' => $format !== 'accordion'];
        foreach ($scripts_to_dequeue as $script => $should_dequeue) {
            if ($should_dequeue) {
                wp_dequeue_script($script);
            }
        }
        foreach ($styles_to_dequeue as $style => $should_dequeue) {
            if ($should_dequeue) {
                wp_dequeue_style($style);
            }
        }
    }
    /**
     * Render Heading Icon
     *
     * @return void
     */
    protected function render_heading_icon()
    {
        $settings = $this->get_settings_for_display();
        $icon = $settings['selected_icon'];
        $icon_active = $settings['selected_active_icon'];
        $icon_align = $settings['icon_align'] ?? 'left';
        if (!empty($icon)) {
            echo '<span class="' . esc_attr('icon dce-accordion-icon accordion-icon-' . $icon_align) . '">';
            Icons_Manager::render_icon($icon, ['aria-hidden' => 'true']);
            echo '</span>';
        }
        if (!empty($icon_active)) {
            echo '<span class="' . esc_attr('icon-active dce-accordion-icon accordion-icon-' . $icon_align) . '">';
            Icons_Manager::render_icon($icon_active, ['aria-hidden' => 'true']);
            echo '</span>';
        }
    }
}
