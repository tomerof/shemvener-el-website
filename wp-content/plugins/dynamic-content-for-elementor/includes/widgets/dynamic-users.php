<?php

// SPDX-FileCopyrightText: 2018-2026 Ovation S.r.l. <help@dynamic.ooo>
// SPDX-License-Identifier: GPL-3.0-or-later
namespace DynamicContentForElementor\Widgets;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Image_Size;
use Elementor\Repeater;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Icons_Manager;
use DynamicContentForElementor\Helper;
use DynamicContentForElementor\Controls\Group_Control_Filters_CSS;
if (!\defined('ABSPATH')) {
    exit;
    // Exit if accessed directly
}
class DynamicUsers extends \DynamicContentForElementor\Widgets\WidgetPrototype
{
    public function get_script_depends()
    {
        return ['imagesloaded', 'dce-isotope', 'dce-dynamic_users'];
    }
    public function get_style_depends()
    {
        return ['dce-dynamicUsers'];
    }
    /**
     * Register controls after check if this feature is only for admin
     *
     * @return void
     */
    protected function safe_register_controls()
    {
        $this->start_controls_section('options_users', ['label' => $this->get_title()]);
        $this->add_control('single_autor', ['label' => esc_html__('Single Author', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HEADING]);
        $this->add_control('author_user', ['label' => esc_html__('Show only the author of the current post', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER]);
        $this->add_control('results_per_page', ['label' => esc_html__('Results per page', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::NUMBER, 'default' => '-1', 'separator' => 'before', 'condition' => ['author_user' => '']]);
        $this->add_control('pagination_enable', ['label' => esc_html__('Pagination', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'condition' => ['author_user' => '']]);
        $this->add_control('users_orderby', ['label' => esc_html__('Order by', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'options' => ['ID' => esc_html__('ID', 'dynamic-content-for-elementor'), 'login' => esc_html__('Login', 'dynamic-content-for-elementor'), 'nicename' => esc_html__('Nicename', 'dynamic-content-for-elementor'), 'email' => esc_html__('Email', 'dynamic-content-for-elementor'), 'url' => esc_html__('Url', 'dynamic-content-for-elementor'), 'registered' => esc_html__('Registered', 'dynamic-content-for-elementor'), 'display_name' => esc_html__('Display Name', 'dynamic-content-for-elementor'), 'post_count' => esc_html__('Post Count', 'dynamic-content-for-elementor'), 'meta_value' => esc_html__('Meta Value (String)', 'dynamic-content-for-elementor'), 'meta_value_num' => esc_html__('Meta Value (Number)', 'dynamic-content-for-elementor')], 'default' => 'ID', 'condition' => ['author_user' => '']]);
        $this->add_control('users_orderby_meta', ['label' => esc_html__('Meta value', 'dynamic-content-for-elementor'), 'type' => 'ooo_query', 'placeholder' => esc_html__('Field key or Name', 'dynamic-content-for-elementor'), 'query_type' => 'fields', 'dynamic' => ['active' => \false], 'label_block' => \true, 'object_type' => 'user', 'default' => 'nickname', 'condition' => ['users_orderby' => ['meta_value', 'meta_value_num'], 'author_user' => '']]);
        $this->add_control('users_order', ['label' => esc_html__('Order', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'options' => ['ASC' => esc_html__('Ascending', 'dynamic-content-for-elementor'), 'DESC' => esc_html__('Descending', 'dynamic-content-for-elementor')], 'toggle' => \false, 'default' => 'ASC', 'condition' => ['author_user' => '']]);
        $this->add_control('filters_heading', ['label' => esc_html__('Filters', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HEADING, 'separator' => 'before', 'condition' => ['author_user' => '']]);
        $this->add_control('roles', ['label' => esc_html__('Roles', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT2, 'options' => Helper::get_roles(), 'multiple' => \true, 'condition' => ['author_user' => '']]);
        $this->add_control('metaFilter', ['label' => esc_html__('User Field Filter', 'dynamic-content-for-elementor'), 'type' => 'ooo_query', 'placeholder' => esc_html__('Field key or Name', 'dynamic-content-for-elementor'), 'query_type' => 'fields', 'dynamic' => ['active' => \false], 'label_block' => \true, 'object_type' => 'user', 'default' => 'none', 'condition' => ['author_user' => '']]);
        $this->add_control('metaValue_filter', ['label' => esc_html__('Meta value', 'dynamic-content-for-elementor'), 'description' => esc_html__('The value of the filter. Use comma as separator for multiple values. Use :empty: if the value should be empty.', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'default' => '', 'condition' => ['metaFilter!' => '', 'author_user' => '']]);
        $this->add_control('exclude_heading', ['label' => esc_html__('Exclude', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HEADING, 'separator' => 'before', 'condition' => ['author_user' => '']]);
        $this->add_control('exclude_user', ['label' => esc_html__('Exclude selected users', 'dynamic-content-for-elementor'), 'type' => 'ooo_query', 'placeholder' => esc_html__('Select user', 'dynamic-content-for-elementor'), 'label_block' => \true, 'multiple' => \true, 'query_type' => 'users', 'condition' => ['author_user' => '']]);
        $this->add_control('exclude_author_post', ['label' => esc_html__('Exclude users without articles', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'condition' => ['author_user' => '']]);
        $this->end_controls_section();
        $this->start_controls_section('section_render', ['label' => esc_html__('Render', 'dynamic-content-for-elementor'), 'tab' => Controls_Manager::TAB_CONTENT]);
        $this->add_control('users_render', ['label' => esc_html__('Render mode', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CHOOSE, 'options' => ['byitems' => ['title' => esc_html__('Users', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-list'], 'template' => ['title' => esc_html__('Template', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-th-large']], 'toggle' => \false, 'default' => 'byitems', 'separator' => 'before']);
        $this->add_control('users_render_template', ['label' => esc_html__('Template', 'dynamic-content-for-elementor'), 'type' => 'ooo_query', 'placeholder' => esc_html__('Template Name', 'dynamic-content-for-elementor'), 'label_block' => \true, 'query_type' => 'posts', 'object_type' => 'elementor_library', 'condition' => ['users_render' => 'template']]);
        $this->add_control('layout_position', ['label' => esc_html__('Layout', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'default' => '', 'tablet_default' => '', 'mobile_default' => '', 'render_type' => 'template', 'options' => ['' => esc_html__('None', 'dynamic-content-for-elementor'), 'top' => esc_html__('Top', 'dynamic-content-for-elementor'), 'left' => esc_html__('Left', 'dynamic-content-for-elementor'), 'right' => esc_html__('Right', 'dynamic-content-for-elementor'), 'alternate' => esc_html__('Alternate', 'dynamic-content-for-elementor')], 'prefix_class' => 'layout-user-position-', 'condition' => ['users_render' => 'byitems']]);
        $this->add_responsive_control('image_rate', ['label' => esc_html__('Rate', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => 50, 'unit' => '%'], 'size_units' => ['px', '%'], 'range' => ['%' => ['min' => 1, 'max' => 100], 'px' => ['min' => 1, 'max' => 600]], 'selectors' => ['{{WRAPPER}} .dce-user_image' => 'width: {{SIZE}}{{UNIT}};', '{{WRAPPER}} .dce-user_content' => 'width: calc( 100% - {{SIZE}}{{UNIT}} );'], 'condition' => ['layout_position' => ['left', 'right', 'alternate']]]);
        $this->add_responsive_control('content_padding', ['label' => esc_html__('Content Padding', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => ['px', '%', 'em'], 'selectors' => ['{{WRAPPER}} .dce-user_content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'], 'condition' => ['layout_position!' => '']]);
        $repeater = new Repeater();
        $repeater->start_controls_tabs('tabs_repeater');
        $repeater->start_controls_tab('tab_content', ['label' => esc_html__('Meta', 'dynamic-content-for-elementor')]);
        $repeater->add_control('meta', ['label' => esc_html__('Meta', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'default' => 'userlogin', 'options' => ['userlogin' => esc_html__('User Login', 'dynamic-content-for-elementor'), 'email' => esc_html__('Email', 'dynamic-content-for-elementor'), 'nickname' => esc_html__('Nick Name', 'dynamic-content-for-elementor'), 'displayname' => esc_html__('Display Name', 'dynamic-content-for-elementor'), 'lastname' => esc_html__('Last Name', 'dynamic-content-for-elementor'), 'firstname' => esc_html__('First Name', 'dynamic-content-for-elementor'), 'description' => esc_html__('Description', 'dynamic-content-for-elementor'), 'avatar' => esc_html__('Avatar', 'dynamic-content-for-elementor'), 'website' => esc_html__('WebSite', 'dynamic-content-for-elementor'), 'role' => esc_html__('Role', 'dynamic-content-for-elementor'), 'custommeta' => esc_html__('Custom Meta', 'dynamic-content-for-elementor'), 'ID' => esc_html__('ID', 'dynamic-content-for-elementor'), 'attachments' => esc_html__('Attachments', 'dynamic-content-for-elementor'), 'articles' => esc_html__('Posts', 'dynamic-content-for-elementor'), 'button' => esc_html__('Button', 'dynamic-content-for-elementor')]]);
        $repeater->add_control('text_before', ['label' => esc_html__('Text before', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'default' => '']);
        $repeater->add_control('text_button', ['label' => esc_html__('Text before', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'default' => esc_html__('Read more', 'dynamic-content-for-elementor'), 'condition' => ['meta' => 'button']]);
        $repeater->add_control('meta_key', ['label' => esc_html__('All Meta', 'dynamic-content-for-elementor'), 'type' => 'ooo_query', 'placeholder' => esc_html__('Field key or Name', 'dynamic-content-for-elementor'), 'label_block' => \true, 'query_type' => 'fields', 'object_type' => 'user', 'default' => 'nickname', 'condition' => ['meta' => 'custommeta']]);
        $repeater->add_control('article_post_type', ['label' => esc_html__('Post Type', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT2, 'options' => Helper::get_public_post_types(), 'multiple' => \true, 'label_block' => \true, 'default' => 'post', 'condition' => ['meta' => 'articles']]);
        $repeater->add_control('attachment_url', ['label' => esc_html__('Add Link to Attachment', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'condition' => ['meta' => 'attachments']]);
        $repeater->add_control('articles_url', ['label' => esc_html__('Add Link to Post', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'condition' => ['meta' => 'articles']]);
        $repeater->add_control('link_to_page', ['label' => esc_html__('Link to page', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'condition' => ['meta!' => ['attachments', 'articles']]]);
        $repeater->add_control('link_to', ['label' => esc_html__('Link to', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'default' => 'user_page', 'options' => ['user_page' => esc_html__('User page', 'dynamic-content-for-elementor'), 'other_url' => esc_html__('Meta URL', 'dynamic-content-for-elementor'), 'custom' => esc_html__('Custom URL', 'dynamic-content-for-elementor')], 'condition' => ['link_to_page' => 'yes', 'meta!' => ['attachments', 'articles']]]);
        $repeater->add_control('custom_link', ['label' => esc_html__('Link url', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::URL, 'placeholder' => esc_html__('https://your-link.com', 'dynamic-content-for-elementor'), 'condition' => ['link_to' => 'custom'], 'default' => ['url' => ''], 'show_label' => \false]);
        $repeater->add_control('meta_field_url', ['label' => esc_html__('Meta Field Url', 'dynamic-content-for-elementor'), 'type' => 'ooo_query', 'query_type' => 'acf', 'label_block' => \true, 'dynamic' => ['active' => \false], 'object_type' => ['file', 'url'], 'condition' => ['link_to_page' => 'yes', 'link_to' => 'other_url', 'meta!' => ['attachments', 'articles']]]);
        $repeater->add_control('meta_field_url_target_blank', ['label' => esc_html__('Target blank', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'condition' => ['link_to_page' => 'yes', 'link_to' => 'other_url', 'meta!' => ['attachments', 'articles'], 'meta_field_url!' => '']]);
        $repeater->add_control('inline_item', ['label' => esc_html__('Inline', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'condition' => ['meta!' => ['attachments', 'articles']]]);
        $repeater->add_control('hide_item', ['label' => esc_html__('Hide item', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'condition' => ['meta!' => ['attachments', 'articles', 'button']]]);
        $repeater->end_controls_tab();
        $repeater->start_controls_tab('tab_style', ['label' => esc_html__('Style', 'dynamic-content-for-elementor')]);
        $repeater->add_responsive_control('align_item', ['label' => esc_html__('Alignment', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CHOOSE, 'toggle' => \true, 'options' => ['left' => ['title' => esc_html__('Left', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-left'], 'center' => ['title' => esc_html__('Center', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-center'], 'right' => ['title' => esc_html__('Right', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-right']], 'default' => '', 'selectors' => ['{{WRAPPER}} .dce-grid-users {{CURRENT_ITEM}}' => 'text-align: {{VALUE}};']]);
        $repeater->add_control('padding_item', ['label' => esc_html__('Padding', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => ['px', '%'], 'selectors' => ['{{WRAPPER}} .dce-grid-users {{CURRENT_ITEM}}' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};']]);
        $repeater->add_control('color_item', ['label' => esc_html__('Text color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .dce-grid-users {{CURRENT_ITEM}}.tx-el, {{WRAPPER}} .dce-grid-users {{CURRENT_ITEM}}.tx-el a' => 'color: {{VALUE}};'], 'condition' => ['meta!' => ['attachments', 'articles', 'avatar']]]);
        $repeater->add_control('hover_color_item', ['label' => esc_html__('Hover color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .dce-grid-users {{CURRENT_ITEM}}.tx-el:hover a' => 'color: {{VALUE}};'], 'condition' => ['meta!' => ['attachments', 'articles', 'avatar'], 'link_to_page' => 'yes']]);
        $repeater->add_control('bgcolor_item', ['label' => esc_html__('Background color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .dce-grid-users {{CURRENT_ITEM}}' => 'background-color: {{VALUE}};'], 'condition' => ['meta!' => ['attachments', 'articles', 'avatar']]]);
        $repeater->add_control('hover_bgcolor_item', ['label' => esc_html__('Background hover color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .dce-grid-users {{CURRENT_ITEM}}:hover' => 'background-color: {{VALUE}};'], 'condition' => ['meta!' => ['attachments', 'articles', 'avatar']]]);
        $repeater->add_group_control(Group_Control_Typography::get_type(), ['name' => 'typography_item', 'label' => esc_html__('Typography item', 'dynamic-content-for-elementor'), 'selector' => '{{WRAPPER}} .dce-grid-users {{CURRENT_ITEM}}.tx-el', 'condition' => ['meta!' => ['attachments', 'avatar']]]);
        $repeater->add_responsive_control('columns_grid_attachments', ['label' => esc_html__('Columns', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'default' => '5', 'tablet_default' => '3', 'mobile_default' => '1', 'options' => ['1' => '1', '2' => '2', '3' => '3', '4' => '4', '5' => '5', '6' => '6', '7' => '7', '8' => '8', '9' => '9', '10' => '10'], 'render_type' => 'template', 'selectors' => ['{{WRAPPER}} .dce-item-user .item_attach' => 'flex: 0 1 calc( 100% / {{VALUE}} );'], 'condition' => ['meta' => 'attachments']]);
        $repeater->add_control('flex_grow_attachments', ['label' => esc_html__('Flex grow', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CHOOSE, 'options' => ['1' => ['title' => esc_html__('Yes', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-check'], '0' => ['title' => esc_html__('No', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-ban']], 'default' => 1, 'selectors' => ['{{WRAPPER}} .dce-item-user .item_attach' => 'flex-grow: {{VALUE}};'], 'condition' => ['meta' => 'attachments']]);
        $repeater->add_responsive_control('flexgrid_mode_attachments', ['label' => esc_html__('Alignment grid', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'default' => 'flex-start', 'tablet_default' => '3', 'mobile_default' => '1', 'options' => ['flex-start' => esc_html__('Flex start', 'dynamic-content-for-elementor'), 'flex-end' => esc_html__('Flex end', 'dynamic-content-for-elementor'), 'center' => esc_html__('Center', 'dynamic-content-for-elementor'), 'space-between' => esc_html__('Space Between', 'dynamic-content-for-elementor'), 'space-around' => esc_html__('Space Around', 'dynamic-content-for-elementor')], 'selectors' => ['{{WRAPPER}} .dce-item-user .grid-attach' => 'justify-content: {{VALUE}};'], 'condition' => ['meta' => 'attachments']]);
        $repeater->add_group_control(Group_Control_Image_Size::get_type(), ['name' => 'size_attachment', 'label' => esc_html__('Image Size', 'dynamic-content-for-elementor'), 'default' => 'thumbnail', 'condition' => ['meta' => 'attachments']]);
        $repeater->add_responsive_control('columns_grid_articles', ['label' => esc_html__('Columns', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'default' => '5', 'tablet_default' => '3', 'mobile_default' => '1', 'options' => ['1' => '1', '2' => '2', '3' => '3', '4' => '4', '5' => '5', '6' => '6', '7' => '7', '8' => '8', '9' => '9', '10' => '10'], 'render_type' => 'template', 'selectors' => ['{{WRAPPER}} .dce-item-user .item_article' => 'flex: 0 1 calc( 100% / {{VALUE}} );'], 'condition' => ['meta' => 'articles']]);
        $repeater->add_control('flex_grow_articles', ['label' => esc_html__('Flex grow', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CHOOSE, 'options' => ['1' => ['title' => esc_html__('Yes', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-check'], '0' => ['title' => esc_html__('No', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-ban']], 'default' => 1, 'selectors' => ['{{WRAPPER}} .dce-item-user .item_article' => 'flex-grow: {{VALUE}};'], 'condition' => ['meta' => 'articles']]);
        $repeater->add_responsive_control('flexgrid_mode_articles', ['label' => esc_html__('Alignment grid', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'default' => 'flex-start', 'tablet_default' => '3', 'mobile_default' => '1', 'options' => ['flex-start' => esc_html__('Flex start', 'dynamic-content-for-elementor'), 'flex-end' => esc_html__('Flex end', 'dynamic-content-for-elementor'), 'center' => esc_html__('Center', 'dynamic-content-for-elementor'), 'space-between' => esc_html__('Space Between', 'dynamic-content-for-elementor'), 'space-around' => esc_html__('Space Around', 'dynamic-content-for-elementor')], 'selectors' => ['{{WRAPPER}} .dce-item-user .grid-articles' => 'justify-content: {{VALUE}};'], 'condition' => ['meta' => 'articles']]);
        $repeater->add_group_control(Group_Control_Image_Size::get_type(), ['name' => 'size_articles', 'label' => esc_html__('Image Size', 'dynamic-content-for-elementor'), 'default' => 'thumbnail', 'condition' => ['meta' => 'articles']]);
        $repeater->add_control('txbefore_heading', ['label' => esc_html__('Text Before', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HEADING, 'separator' => 'before', 'condition' => ['text_before!' => '']]);
        $repeater->add_control('color_txbefore', ['label' => esc_html__('Text before Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .dce-grid-users {{CURRENT_ITEM}} .tx-before' => 'color: {{VALUE}};', '{{WRAPPER}} .dce-grid-users {{CURRENT_ITEM}} a .tx-before' => 'color: {{VALUE}};'], 'condition' => ['text_before!' => '']]);
        $repeater->add_group_control(Group_Control_Typography::get_type(), ['name' => 'typography_txbefore', 'label' => 'Typography text before', 'selector' => '{{WRAPPER}} .dce-grid-users {{CURRENT_ITEM}} .tx-before', 'popover' => \true, 'condition' => ['text_before!' => '']]);
        $repeater->end_controls_tab();
        $repeater->end_controls_tabs();
        $this->add_control('user_meta_items', ['label' => esc_html__('User Meta Items', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::REPEATER, 'default' => [['meta' => 'avatar'], ['meta' => 'userlogin'], ['meta' => 'email'], ['meta' => 'nickname']], 'fields' => $repeater->get_controls(), 'title_field' => '{{{ meta }}}', 'render_type' => 'template', 'condition' => ['users_render' => 'byitems']]);
        $this->end_controls_section();
        $this->start_controls_section('section_pagination', ['label' => esc_html__('Pagination', 'dynamic-content-for-elementor'), 'tab' => Controls_Manager::TAB_CONTENT, 'condition' => ['pagination_enable' => 'yes']]);
        $this->add_control('pagination_show_numbers', ['label' => esc_html__('Show Numbers', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => 'yes']);
        $this->add_control('pagination_range', ['label' => esc_html__('Range of numbers', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::NUMBER, 'default' => 4, 'condition' => ['pagination_show_numbers' => 'yes']]);
        $this->add_control('pagination_show_prevnext', ['label' => esc_html__('Show Prev/Next', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => 'yes', 'separator' => 'before']);
        $this->add_control('selected_pagination_icon_prevnext', ['label' => esc_html__('Icon Prev/Next', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::ICONS, 'fa4compatibility' => 'pagination_icon_prevnext', 'default' => ['value' => 'fas fa-long-arrow-alt-right', 'library' => 'fa-solid'], 'recommended' => ['fa-solid' => ['arrow-right', 'angle-right', 'long-arrow-alt-right', 'arrow-alt-circle-right', 'arrow-circle-right', 'caret-right', 'caret-square-right', 'chevron-circle-right', 'chevron-right', 'hand-point-right']], 'condition' => ['pagination_show_prevnext' => 'yes']]);
        $this->add_control('pagination_prev_label', ['label' => esc_html__('Previous Label', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'default' => esc_html__('Previous', 'dynamic-content-for-elementor'), 'condition' => ['pagination_show_prevnext' => 'yes']]);
        $this->add_control('pagination_next_label', ['label' => esc_html__('Next Label', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'default' => esc_html__('Next', 'dynamic-content-for-elementor'), 'condition' => ['pagination_show_prevnext' => 'yes']]);
        $this->add_control('pagination_show_firstlast', ['label' => esc_html__('Show First/Last', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => 'yes', 'separator' => 'before']);
        $this->add_control('selected_pagination_icon_firstlast', ['label' => esc_html__('Icon First/Last', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::ICONS, 'fa4compatibility' => 'pagination_icon_firstlast', 'default' => ['value' => 'fas fa-long-arrow-alt-right', 'library' => 'fa-solid'], 'recommended' => ['fa-solid' => ['arrow-right', 'angle-right', 'long-arrow-alt-right', 'arrow-alt-circle-right', 'arrow-circle-right', 'caret-right', 'caret-square-right', 'chevron-circle-right', 'chevron-right', 'hand-point-right']], 'condition' => ['pagination_show_firstlast' => 'yes']]);
        $this->add_control('pagination_first_label', ['label' => esc_html__('Previous Label', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'default' => esc_html__('First', 'dynamic-content-for-elementor'), 'condition' => ['pagination_show_firstlast' => 'yes']]);
        $this->add_control('pagination_last_label', ['label' => esc_html__('Next Label', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'default' => esc_html__('Last', 'dynamic-content-for-elementor'), 'condition' => ['pagination_show_firstlast' => 'yes']]);
        $this->add_control('pagination_show_progression', ['label' => esc_html__('Show Progression', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => 'yes', 'separator' => 'before']);
        $this->end_controls_section();
        $this->start_controls_section('section_grid', ['label' => esc_html__('Skin', 'dynamic-content-for-elementor'), 'tab' => Controls_Manager::TAB_CONTENT]);
        $this->add_control('posts_style', ['label' => esc_html__('Skin', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'default' => 'flexgrid', 'frontend_available' => \true, 'render_type' => 'template', 'options' => ['simplegrid' => esc_html__('Row', 'dynamic-content-for-elementor'), 'flexgrid' => esc_html__('Grid', 'dynamic-content-for-elementor'), 'grid' => esc_html__('Masonry', 'dynamic-content-for-elementor')]]);
        $this->add_responsive_control('columns_grid_flex', ['label' => esc_html__('Columns', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'render_type' => 'template', 'default' => '5', 'tablet_default' => '3', 'mobile_default' => '1', 'options' => ['1' => '1', '2' => '2', '3' => '3', '4' => '4', '5' => '5', '6' => '6', '7' => '7'], 'selectors' => ['{{WRAPPER}} .dce-grid-users.flexgrid .dce-item-user' => 'flex: 0 1 calc( 100% / {{VALUE}} );', '{{WRAPPER}} .dce-grid-users.grid .dce-item-user' => 'width: calc( 100% / {{VALUE}} ); display: inline-block;'], 'condition' => ['posts_style' => ['grid', 'flexgrid']]]);
        $this->add_control('flex_grow', ['label' => esc_html__('Flex grow', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CHOOSE, 'options' => ['1' => ['title' => esc_html__('1', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-check'], '0' => ['title' => esc_html__('0', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-ban']], 'default' => 0, 'selectors' => ['{{WRAPPER}} .dce-item-user' => 'flex-grow: {{VALUE}};'], 'condition' => ['posts_style' => 'flexgrid']]);
        $this->add_responsive_control('flexgrid_mode', ['label' => esc_html__('Alignment grid', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'default' => 'flex-start', 'tablet_default' => '3', 'mobile_default' => '1', 'options' => ['flex-start' => esc_html__('Flex start', 'dynamic-content-for-elementor'), 'flex-end' => esc_html__('Flex end', 'dynamic-content-for-elementor'), 'center' => esc_html__('Center', 'dynamic-content-for-elementor'), 'space-between' => esc_html__('Space Between', 'dynamic-content-for-elementor'), 'space-around' => esc_html__('Space Around', 'dynamic-content-for-elementor')], 'selectors' => ['{{WRAPPER}} .dce-grid-users' => 'justify-content: {{VALUE}};'], 'condition' => ['posts_style' => 'flexgrid', 'flex_grow' => '0']]);
        $this->add_control('filters_enable', ['label' => esc_html__('Show Filters', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'frontend_available' => \true, 'condition' => ['posts_style' => 'grid']]);
        $this->add_responsive_control('grid_space', ['label' => esc_html__('Column Space', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => 15, 'unit' => 'px'], 'tablet_default' => ['unit' => 'px'], 'mobile_default' => ['unit' => 'px'], 'size_units' => ['px', 'rem'], 'range' => ['rem' => ['min' => 0, 'max' => 10], 'px' => ['min' => 0, 'max' => 100]], 'selectors' => ['{{WRAPPER}} .dce-item-user' => 'padding-left: {{SIZE}}{{UNIT}}; padding-right: {{SIZE}}{{UNIT}};']]);
        $this->add_responsive_control('row_grid_space', ['label' => esc_html__('Row Space', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => 15, 'unit' => 'px'], 'tablet_default' => ['unit' => 'px'], 'mobile_default' => ['unit' => 'px'], 'size_units' => ['px', 'rem'], 'range' => ['rem' => ['min' => 0, 'max' => 10], 'px' => ['min' => 0, 'max' => 100]], 'selectors' => ['{{WRAPPER}} .dce-item-user' => 'padding-bottom: {{SIZE}}{{UNIT}};']]);
        $this->add_responsive_control('layout_align', ['label' => esc_html__('Alignment', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CHOOSE, 'options' => ['none' => ['title' => esc_html__('None', 'dynamic-content-for-elementor'), 'icon' => 'eicon-close'], 'left' => ['title' => esc_html__('Left', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-left'], 'center' => ['title' => esc_html__('Center', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-center'], 'right' => ['title' => esc_html__('Right', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-right']], 'default' => 'none', 'toggle' => \false, 'selectors' => ['{{WRAPPER}} .dce-grid-users' => 'text-align: {{VALUE}};']]);
        $this->end_controls_section();
        $this->start_controls_section('section_filters', ['label' => esc_html__('Filters', 'dynamic-content-for-elementor'), 'tab' => Controls_Manager::TAB_CONTENT, 'condition' => ['filters_enable' => 'yes']]);
        $this->add_control('filters_meta', ['label' => esc_html__('Filters Meta', 'dynamic-content-for-elementor'), 'type' => 'ooo_query', 'placeholder' => esc_html__('Field key or Name', 'dynamic-content-for-elementor'), 'label_block' => \true, 'query_type' => 'fields', 'object_type' => 'user', 'default' => 'none']);
        $this->add_control('separator_filter', ['label' => esc_html__('Separator', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'default' => ' / ', 'condition' => ['filters_enable' => 'yes']]);
        $this->add_responsive_control('filters_align', ['label' => esc_html__('Filters Alignment', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CHOOSE, 'options' => ['left' => ['title' => esc_html__('Left', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-left'], 'center' => ['title' => esc_html__('Center', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-center'], 'right' => ['title' => esc_html__('Right', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-right']], 'default' => 'left', 'selectors' => ['{{WRAPPER}} .dce-users-filters' => 'text-align: {{VALUE}};'], 'condition' => ['filters_enable' => 'yes']]);
        $this->add_control('filters_color', ['label' => esc_html__('Filters Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .dce-users-filters .users-filters-item a' => 'color: {{VALUE}};']]);
        $this->add_control('filters_color_hover', ['label' => esc_html__('Filters Color Hover', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .dce-users-filters .users-filters-item a:hover' => 'color: {{VALUE}};']]);
        $this->add_control('filters_color_active', ['label' => esc_html__('Filters Color Active', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'default' => '#990000', 'selectors' => ['{{WRAPPER}} .dce-users-filters .users-filters-item.filter-active a' => 'color: {{VALUE}};']]);
        $this->add_control('filters_color_divisore', ['label' => esc_html__('Divider Filters Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .dce-users-filters .filters-divider' => 'color: {{VALUE}};']]);
        $this->add_group_control(Group_Control_Typography::get_type(), ['name' => 'typography_filters', 'label' => esc_html__('Typography Filters', 'dynamic-content-for-elementor'), 'selector' => '{{WRAPPER}} .dce-users-filters']);
        $this->add_group_control(Group_Control_Typography::get_type(), ['name' => 'typography_filters_divider', 'label' => esc_html__('Typography Divider', 'dynamic-content-for-elementor'), 'selector' => '{{WRAPPER}} .dce-users-filters .filters-divider']);
        $this->add_responsive_control('filters_padding_items', ['label' => esc_html__('Space between filters', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => 5, 'unit' => 'px'], 'tablet_default' => ['unit' => 'px'], 'mobile_default' => ['unit' => 'px'], 'size_units' => ['px', '%'], 'range' => ['%' => ['min' => 0, 'max' => 100], 'px' => ['min' => 0, 'max' => 100]], 'selectors' => ['{{WRAPPER}} .dce-users-filters .filters-divider' => 'padding-left: {{SIZE}}{{UNIT}}; padding-right: {{SIZE}}{{UNIT}};']]);
        $this->add_control('filters_padding', ['label' => esc_html__('Padding', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => ['px', '%'], 'selectors' => ['{{WRAPPER}} .dce-users-filters' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};']]);
        $this->add_responsive_control('filters_move_divider', ['label' => esc_html__('Vertical Shift Divider', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => 0, 'unit' => 'px'], 'tablet_default' => ['unit' => 'px'], 'mobile_default' => ['unit' => 'px'], 'size_units' => ['px', '%'], 'range' => ['%' => ['min' => -100, 'max' => 100], 'px' => ['min' => -100, 'max' => 100]], 'selectors' => ['{{WRAPPER}} .dce-users-filters .filters-divider' => 'top: {{SIZE}}{{UNIT}}; position: relative;']]);
        $this->end_controls_section();
        $this->start_controls_section('section_avatar', ['label' => esc_html__('Avatar', 'dynamic-content-for-elementor'), 'tab' => Controls_Manager::TAB_STYLE, 'condition' => ['users_render' => 'byitems']]);
        $this->add_control('avatar_size', ['label' => esc_html__('Avatar Size', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => 80], 'range' => ['px' => ['min' => 10, 'max' => 1200, 'step' => 1]], 'size_units' => ['px', '%']]);
        $this->add_group_control(Group_Control_Border::get_type(), ['name' => 'border_avatar', 'label' => esc_html__('Border', 'dynamic-content-for-elementor'), 'selector' => '{{WRAPPER}} .dce-item-user .user-avatar img']);
        $this->add_responsive_control('border_radius_avatar', ['label' => esc_html__('Border Radius', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => ['px', '%'], 'selectors' => ['{{WRAPPER}} .dce-item-user .user-avatar, {{WRAPPER}} .dce-item-user .user-avatar img, {{WRAPPER}} .dce-overlay_hover, {{WRAPPER}} .dce-overlay' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};']]);
        $this->add_control('padding_avatar', ['label' => esc_html__('Padding', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => ['px', '%'], 'selectors' => ['{{WRAPPER}} .dce-item-user .user-avatar, {{WRAPPER}} .dce-overlay_hover, {{WRAPPER}} .dce-overlay' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};']]);
        $this->add_group_control(Group_Control_Box_Shadow::get_type(), ['name' => 'box_shadow_avatar', 'selector' => '{{WRAPPER}} .dce-item-user .user-avatar']);
        $this->add_group_control(Group_Control_Filters_CSS::get_type(), ['name' => 'filters_avatar', 'label' => esc_html__('Filters', 'dynamic-content-for-elementor'), 'selector' => '{{WRAPPER}} .dce-item-user .user-avatar']);
        $this->end_controls_section();
        $this->start_controls_section('section_style_pagination', ['label' => esc_html__('Pagination', 'dynamic-content-for-elementor'), 'tab' => Controls_Manager::TAB_STYLE, 'condition' => ['pagination_enable' => 'yes']]);
        $this->add_responsive_control('pagination_align', ['label' => esc_html__('Alignment', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CHOOSE, 'toggle' => \false, 'options' => ['flex-start' => ['title' => esc_html__('Left', 'dynamic-content-for-elementor'), 'icon' => 'eicon-h-align-left'], 'center' => ['title' => esc_html__('Center', 'dynamic-content-for-elementor'), 'icon' => 'eicon-h-align-center'], 'flex-end' => ['title' => esc_html__('Right', 'dynamic-content-for-elementor'), 'icon' => 'eicon-h-align-right']], 'default' => 'center', 'selectors' => ['{{WRAPPER}} .dce-pagination' => 'text-align: {{VALUE}};']]);
        $this->add_group_control(Group_Control_Typography::get_type(), ['name' => 'pagination_typography', 'label' => esc_html__('Typography', 'dynamic-content-for-elementor'), 'selector' => '{{WRAPPER}} .dce-pagination']);
        $this->add_responsive_control('pagination_space', ['label' => esc_html__('Space', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => 10], 'range' => ['px' => ['max' => 100, 'min' => 0, 'step' => 1]], 'selectors' => ['{{WRAPPER}} .dce-pagination' => 'padding-top: {{SIZE}}{{UNIT}};']]);
        $this->add_responsive_control('pagination_spacing', ['label' => esc_html__('Horizontal Spacing', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => 1], 'range' => ['px' => ['max' => 100, 'min' => 0, 'step' => 1]], 'selectors' => ['{{WRAPPER}} .dce-pagination span, {{WRAPPER}} .dce-pagination a' => 'margin-right: {{SIZE}}{{UNIT}};']]);
        $this->add_control('pagination_padding', ['label' => esc_html__('Padding', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => ['px', 'em', '%'], 'selectors' => ['{{WRAPPER}} .dce-pagination span, {{WRAPPER}} .dce-pagination a' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};']]);
        $this->add_control('pagination_radius', ['label' => esc_html__('Border Radius', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => ['px', '%', 'em'], 'selectors' => ['{{WRAPPER}} .dce-pagination span, {{WRAPPER}} .dce-pagination a' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};']]);
        $this->add_control('pagination_heading_colors', ['label' => esc_html__('Colors', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HEADING, 'separator' => 'before']);
        $this->start_controls_tabs('pagination_colors');
        $this->start_controls_tab('pagination_text_colors', ['label' => esc_html__('Normal', 'dynamic-content-for-elementor')]);
        $this->add_control('pagination_text_color', ['label' => esc_html__('Text Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'default' => '', 'selectors' => ['{{WRAPPER}} .dce-pagination span, {{WRAPPER}} .dce-pagination a' => 'color: {{VALUE}};']]);
        $this->add_control('pagination_background_color', ['label' => esc_html__('Background Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .dce-pagination span, {{WRAPPER}} .dce-pagination a' => 'background-color: {{VALUE}};']]);
        $this->add_group_control(Group_Control_Border::get_type(), ['name' => 'pagination_border', 'label' => esc_html__('Border', 'dynamic-content-for-elementor'), 'selector' => '{{WRAPPER}} .dce-pagination span, {{WRAPPER}} .dce-pagination a']);
        $this->end_controls_tab();
        $this->start_controls_tab('pagination_text_colors_hover', ['label' => esc_html__('Hover', 'dynamic-content-for-elementor')]);
        $this->add_control('pagination_hover_color', ['label' => esc_html__('Text Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .dce-pagination a:hover' => 'color: {{VALUE}};']]);
        $this->add_control('pagination_background_hover_color', ['label' => esc_html__('Background Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .dce-pagination a:hover' => 'background-color: {{VALUE}};']]);
        $this->add_control('pagination_hover_border_color', ['label' => esc_html__('Border Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'condition' => ['pagination_border_border!' => ''], 'selectors' => ['{{WRAPPER}} .dce-pagination a:hover' => 'border-color: {{VALUE}};']]);
        $this->end_controls_tab();
        $this->start_controls_tab('pagination_text_colors_current', ['label' => esc_html__('Current', 'dynamic-content-for-elementor')]);
        $this->add_control('pagination_current_color', ['label' => esc_html__('Text Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .dce-pagination span.current' => 'color: {{VALUE}};']]);
        $this->add_control('pagination_background_current_color', ['label' => esc_html__('Background Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .dce-pagination span.current' => 'background-color: {{VALUE}};']]);
        $this->end_controls_tab();
        $this->end_controls_tabs();
        $this->add_control('pagination_heading_prevnext', ['label' => esc_html__('Prev/Next', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HEADING, 'separator' => 'before', 'condition' => ['pagination_show_prevnext' => 'yes']]);
        $this->add_responsive_control('pagination_spacing_prevnext', ['label' => esc_html__('Spacing', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => ''], 'range' => ['px' => ['max' => 100, 'min' => 0, 'step' => 1]], 'selectors' => ['{{WRAPPER}} .dce-pagination .pageprev' => 'margin-right: {{SIZE}}{{UNIT}};', '{{WRAPPER}} .dce-pagination .pagenext' => 'margin-left: {{SIZE}}{{UNIT}};'], 'condition' => ['pagination_show_prevnext' => 'yes']]);
        $this->add_responsive_control('pagination_icon_spacing_prevnext', ['label' => esc_html__('Icon Spacing', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => ''], 'range' => ['px' => ['max' => 50, 'min' => 0, 'step' => 1]], 'selectors' => ['{{WRAPPER}} .dce-pagination .pageprev .fa' => 'margin-right: {{SIZE}}{{UNIT}};', '{{WRAPPER}} .dce-pagination .pagenext .fa' => 'margin-left: {{SIZE}}{{UNIT}};'], 'condition' => ['pagination_show_prevnext' => 'yes']]);
        $this->add_responsive_control('pagination_icon_size_prevnext', ['label' => esc_html__('Icon Size', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'size_units' => ['px', 'em', 'rem', 'vw', 'custom'], 'range' => ['px' => ['max' => 100, 'min' => 0, 'step' => 1], 'em' => ['min' => 0, 'max' => 10], 'rem' => ['min' => 0, 'max' => 10]], 'selectors' => ['{{WRAPPER}} .dce-pagination .pageprev .fa' => 'font-size: {{SIZE}}{{UNIT}};', '{{WRAPPER}} .dce-pagination .pagenext .fa' => 'font-size: {{SIZE}}{{UNIT}};', '{{WRAPPER}} .dce-pagination .pageprev svg' => 'width: {{SIZE}}{{UNIT}}; height: auto;', '{{WRAPPER}} .dce-pagination .pagenext svg' => 'width: {{SIZE}}{{UNIT}}; height: auto;'], 'condition' => ['pagination_show_prevnext' => 'yes']]);
        $this->start_controls_tabs('pagination_prevnext_colors');
        $this->start_controls_tab('pagination_prevnext_text_colors', ['label' => esc_html__('Normal', 'dynamic-content-for-elementor'), 'condition' => ['pagination_show_prevnext' => 'yes']]);
        $this->add_control('pagination_prevnext_text_color', ['label' => esc_html__('Text Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'default' => '', 'selectors' => ['{{WRAPPER}} .dce-pagination .pageprev, {{WRAPPER}} .dce-pagination .pagenext' => 'color: {{VALUE}};'], 'condition' => ['pagination_show_prevnext' => 'yes']]);
        $this->add_control('pagination_prevnext_background_color', ['label' => esc_html__('Background Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'default' => '', 'selectors' => ['{{WRAPPER}} .dce-pagination .pageprev, {{WRAPPER}} .dce-pagination .pagenext' => 'background-color: {{VALUE}};'], 'condition' => ['pagination_show_prevnext' => 'yes']]);
        $this->add_group_control(Group_Control_Border::get_type(), ['name' => 'pagination_prevnext_border', 'label' => esc_html__('Border', 'dynamic-content-for-elementor'), 'selector' => '{{WRAPPER}} .dce-pagination .pageprev, {{WRAPPER}} .dce-pagination .pagenext', 'condition' => ['pagination_show_prevnext' => 'yes']]);
        $this->add_control('pagination_prevnext_radius', ['label' => esc_html__('Border Radius', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => ['px', '%', 'em'], 'selectors' => ['{{WRAPPER}} .dce-pagination .pageprev, {{WRAPPER}} .dce-pagination .pagenext' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'], 'condition' => ['pagination_show_prevnext' => 'yes']]);
        $this->end_controls_tab();
        $this->start_controls_tab('pagination_prevnext_text_colors_hover', ['label' => esc_html__('Hover', 'dynamic-content-for-elementor'), 'condition' => ['pagination_show_prevnext' => 'yes']]);
        $this->add_control('pagination_prevnext_hover_color', ['label' => esc_html__('Text Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .dce-pagination .pageprev:hover, {{WRAPPER}} .dce-pagination .pagenext:hover' => 'color: {{VALUE}};'], 'condition' => ['pagination_show_prevnext' => 'yes']]);
        $this->add_control('pagination_prevnext_background_hover_color', ['label' => esc_html__('Background Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .dce-pagination .pageprev:hover, {{WRAPPER}} .dce-pagination .pagenext:hover' => 'background-color: {{VALUE}};'], 'condition' => ['pagination_show_prevnext' => 'yes']]);
        $this->add_control('pagination_prevnext_hover_border_color', ['label' => esc_html__('Border Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .dce-pagination .pageprev:hover, {{WRAPPER}} .dce-pagination .pagenext:hover' => 'border-color: {{VALUE}};'], 'condition' => ['pagination_show_prevnext' => 'yes', 'pagination_prevnext_border_border!' => '']]);
        $this->end_controls_tab();
        $this->end_controls_tabs();
        $this->add_control('pagination_heading_firstlast', ['label' => esc_html__('First/last', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HEADING, 'separator' => 'before', 'condition' => ['pagination_show_firstlast' => 'yes']]);
        $this->add_responsive_control('pagination_spacing_firstlast', ['label' => esc_html__('Spacing', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => ''], 'range' => ['px' => ['max' => 100, 'min' => 0, 'step' => 1]], 'selectors' => ['{{WRAPPER}} .dce-pagination .pagefirst' => 'margin-right: {{SIZE}}{{UNIT}};', '{{WRAPPER}} .dce-pagination .pagelast' => 'margin-left: {{SIZE}}{{UNIT}};'], 'condition' => ['pagination_show_firstlast' => 'yes']]);
        $this->start_controls_tabs('pagination_firstlast_colors');
        $this->start_controls_tab('pagination_firstlast_text_colors', ['label' => esc_html__('Normal', 'dynamic-content-for-elementor'), 'condition' => ['pagination_show_firstlast' => 'yes']]);
        $this->add_control('pagination_firstlast_text_color', ['label' => esc_html__('Text Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'default' => '', 'selectors' => ['{{WRAPPER}} .dce-pagination .pagefirst, {{WRAPPER}} .dce-pagination .pagelast' => 'color: {{VALUE}};'], 'condition' => ['pagination_show_firstlast' => 'yes']]);
        $this->add_control('pagination_firstlast_background_color', ['label' => esc_html__('Background Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'default' => '', 'selectors' => ['{{WRAPPER}} .dce-pagination .pagefirst, {{WRAPPER}} .dce-pagination .pagelast' => 'background-color: {{VALUE}};'], 'condition' => ['pagination_show_firstlast' => 'yes']]);
        $this->add_group_control(Group_Control_Border::get_type(), ['name' => 'pagination_firstlast_border', 'label' => esc_html__('Border', 'dynamic-content-for-elementor'), 'selector' => '{{WRAPPER}} .dce-pagination .pagefirst, {{WRAPPER}} .dce-pagination .pagelast', 'condition' => ['pagination_show_firstlast' => 'yes']]);
        $this->add_control('pagination_firstlast_radius', ['label' => esc_html__('Border Radius', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => ['px', '%', 'em'], 'selectors' => ['{{WRAPPER}} .dce-pagination .pagefirst, {{WRAPPER}} .dce-pagination .pagelast' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'], 'condition' => ['pagination_show_firstlast' => 'yes']]);
        $this->end_controls_tab();
        $this->start_controls_tab('pagination_firstlast_text_colors_hover', ['label' => esc_html__('Hover', 'dynamic-content-for-elementor'), 'condition' => ['pagination_show_firstlast' => 'yes']]);
        $this->add_control('pagination_firstlast_hover_color', ['label' => esc_html__('Text Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .dce-pagination .pagefirst:hover, {{WRAPPER}} .dce-pagination .pagelast:hover' => 'color: {{VALUE}};'], 'condition' => ['pagination_show_firstlast' => 'yes']]);
        $this->add_control('pagination_firstlast_background_hover_color', ['label' => esc_html__('Background Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .dce-pagination .pagefirst:hover, {{WRAPPER}} .dce-pagination .pagelast:hover' => 'background-color: {{VALUE}};'], 'condition' => ['pagination_show_firstlast' => 'yes']]);
        $this->add_control('pagination_firstlast_hover_border_color', ['label' => esc_html__('Border Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .dce-pagination .pagefirst:hover, {{WRAPPER}} .dce-pagination .pagelast:hover' => 'border-color: {{VALUE}};'], 'condition' => ['pagination_show_firstlast' => 'yes', 'pagination_firstlast_border_border!' => '']]);
        $this->end_controls_tab();
        $this->end_controls_tabs();
        $this->add_control('pagination_heading_progression', ['label' => esc_html__('Progression', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HEADING, 'separator' => 'before', 'condition' => ['pagination_show_progression' => 'yes']]);
        $this->add_responsive_control('pagination_spacing_progression', ['label' => esc_html__('Spacing', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => ''], 'range' => ['px' => ['max' => 100, 'min' => 0, 'step' => 1]], 'selectors' => ['{{WRAPPER}} .dce-pagination .progression' => 'margin-right: {{SIZE}}{{UNIT}};'], 'condition' => ['pagination_show_progression' => 'yes']]);
        $this->start_controls_tabs('pagination_progression_colors');
        $this->start_controls_tab('pagination_progression_text_colors', ['label' => esc_html__('Normal', 'dynamic-content-for-elementor'), 'condition' => ['pagination_show_progression' => 'yes']]);
        $this->add_control('pagination_progression_text_color', ['label' => esc_html__('Text Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'default' => '', 'selectors' => ['{{WRAPPER}} .dce-pagination .progression' => 'color: {{VALUE}};'], 'condition' => ['pagination_show_progression' => 'yes']]);
        $this->add_control('pagination_progression_background_color', ['label' => esc_html__('Background Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'default' => '', 'selectors' => ['{{WRAPPER}} .dce-pagination .progression' => 'background-color: {{VALUE}};'], 'condition' => ['pagination_show_progression' => 'yes']]);
        $this->add_group_control(Group_Control_Border::get_type(), ['name' => 'pagination_progression_border', 'label' => esc_html__('Border', 'dynamic-content-for-elementor'), 'selector' => '{{WRAPPER}} .dce-pagination .progression', 'condition' => ['pagination_show_progression' => 'yes']]);
        $this->add_control('pagination_progression_radius', ['label' => esc_html__('Border Radius', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => ['px', '%', 'em'], 'selectors' => ['{{WRAPPER}} .dce-pagination .progression' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'], 'condition' => ['pagination_show_progression' => 'yes']]);
        $this->end_controls_tab();
        $this->start_controls_tab('pagination_progression_text_colors_hover', ['label' => esc_html__('Hover', 'dynamic-content-for-elementor'), 'condition' => ['pagination_show_progression' => 'yes']]);
        $this->add_control('pagination_progression_hover_color', ['label' => esc_html__('Text Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .dce-pagination .progression' => 'color: {{VALUE}};'], 'condition' => ['pagination_show_progression' => 'yes']]);
        $this->add_control('pagination_progression_background_hover_color', ['label' => esc_html__('Background Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .dce-pagination .progression' => 'background-color: {{VALUE}};'], 'condition' => ['pagination_show_progression' => 'yes']]);
        $this->add_control('pagination_progression_hover_border_color', ['label' => esc_html__('Border Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .dce-pagination .progression' => 'border-color: {{VALUE}};'], 'condition' => ['pagination_show_progression' => 'yes', 'pagination_firstlast_border_border!' => '']]);
        $this->end_controls_tab();
        $this->end_controls_tabs();
        $this->end_controls_section();
    }
    protected function safe_render()
    {
        $settings = $this->get_settings_for_display();
        if (empty($settings)) {
            return;
        }
        if ($settings['filters_enable'] && $settings['posts_style'] == 'grid') {
            $tag_filter = 'span';
            $divider = '';
            // Retrieve the list of elements that belong to a given user_meta
            $list_filters = array();
            $filter_user_ids = get_users(['fields' => 'ID']);
            update_meta_cache('user', $filter_user_ids);
            $list_isotope_filters = [];
            foreach ($filter_user_ids as $user_id) {
                $filter_value = get_user_meta($user_id, $settings['filters_meta'], \true);
                $valore_meta = esc_html($filter_value);
                if ($valore_meta != '') {
                    $list_isotope_filters[$valore_meta] = $filter_value;
                }
            }
            echo '<div class="dce-users-filters">';
            $cont_f = 1;
            if (!empty($list_isotope_filters)) {
                echo '<' . $tag_filter . ' class="users-filters-item filter-active">' . wp_kses_post($divider) . '<a href="#" data-filter="*">' . esc_html__('All', 'dynamic-content-for-elementor') . '</a></' . $tag_filter . '>';
                foreach ($list_isotope_filters as $filter) {
                    // L'etichetta del filtro
                    $filternome = $filter;
                    // Lo slug del filtro
                    $filterslug = sanitize_title($filter);
                    // then I will convert it into a simplified dashed and lowercase string
                    $divider = '<span class="filters-divider">' . wp_kses_post($settings['separator_filter']) . '</span>';
                    if ($filternome != '') {
                        echo '<' . $tag_filter . ' class="users-filters-item">' . $divider . '<a href="#" data-filter=".' . $filterslug . '">' . wp_kses_post($filternome) . '</a></' . $tag_filter . '>';
                    }
                    ++$cont_f;
                }
            }
            echo '</div>';
        }
        $styleClass = '';
        if ($settings['posts_style'] == 'simplegrid') {
            $styleClass = ' simplegrid';
        } elseif ($settings['posts_style'] == 'flexgrid') {
            $styleClass = ' flexgrid';
        } elseif ($settings['posts_style'] == 'grid') {
            $styleClass = ' grid';
        }
        if ($settings['metaFilter'] == 'none') {
            $settings['metaFilter'] = array();
        }
        $userargs = array('blog_id' => get_current_blog_id(), 'number' => $settings['results_per_page']);
        global $paged;
        $paged = $this->get_current_page();
        $userargs['paged'] = $paged;
        if (!empty($settings['roles'])) {
            $userargs['role__in'] = $settings['roles'];
        }
        if (!empty($settings['exclude_user'])) {
            $userargs['exclude'] = $settings['exclude_user'];
        }
        if (!empty($settings['users_orderby_meta'])) {
            $userargs['meta_key'] = $settings['users_orderby_meta'];
        }
        if (!empty($settings['users_orderby'])) {
            $userargs['orderby'] = $settings['users_orderby'];
        }
        if (!empty($settings['users_order'])) {
            $userargs['order'] = $settings['users_order'];
        }
        if (\trim($settings['metaValue_filter']) === ':empty:') {
            $array_value = [''];
        } else {
            $array_value = Helper::str_to_array(',', $settings['metaValue_filter']);
        }
        if ($settings['metaFilter'] && !empty($array_value)) {
            if (Helper::is_validated_user_meta($settings['metaFilter'])) {
                $metaFilter = array('relation' => 'OR');
                foreach ($array_value as $key => $value) {
                    $metaFilter[] = array('key' => $settings['metaFilter'], 'value' => $value, 'compare' => '=');
                }
                $userargs['meta_query'] = $metaFilter;
            } else {
                $user_ids = array();
                foreach ($array_value as $key => $value) {
                    $user = get_user_by($settings['metaFilter'], $value);
                    if ($user) {
                        $user_ids[] = $user->ID;
                    }
                }
                $userargs['include'] = $user_ids;
            }
        }
        if ($settings['author_user'] == 'yes') {
            $author = get_the_author_meta('ID');
            $userargs['include'] = array($author);
        }
        $userargs = apply_filters('dynamicooo/dynamic-users/query-args', $userargs, $this->get_id());
        $userargs['count_total'] = \true;
        $user_query = new \WP_User_Query($userargs);
        $users = $user_query->get_results();
        $number_of_users = $user_query->get_total();
        echo '<div class="dce-grid-users' . $styleClass . '">';
        foreach ($users as $user) {
            $user_meta_items = $settings['user_meta_items'];
            $filters_string_class = '';
            if ($settings['filters_enable']) {
                $filters_string_class = $user->{$settings['filters_meta']};
                $filters_string_class = ' ' . sanitize_title($filters_string_class);
            }
            $exclude_author_post = 1;
            if ($settings['exclude_author_post']) {
                $exclude_author_post = $this->have_articles($user->ID);
            }
            if ($exclude_author_post) {
                echo '<div class="dce-item-user' . $filters_string_class . '">';
                if ($settings['users_render'] == 'byitems') {
                    if (!empty($user_meta_items)) {
                        if ($settings['layout_position']) {
                            echo '<div class="dce-user_image">';
                            foreach ($settings['user_meta_items'] as $item) {
                                $classElItem = esc_attr(' elementor-repeater-item-' . $item['_id']);
                                $openLink = $this->get_link_a($item, $user);
                                $closeLink = $openLink ? '</a>' : '';
                                if ($item['meta'] == 'avatar') {
                                    echo '<div class="user-avatar' . $classElItem . '">' . $openLink . get_avatar($user->user_email, $settings['avatar_size']['size']) . $closeLink . '</div>';
                                }
                            }
                            echo '</div>';
                        }
                        echo '<div class="dce-user_content">';
                        foreach ($settings['user_meta_items'] as $item) {
                            $classElItem = ' elementor-repeater-item-' . $item['_id'];
                            $inlineItem = '';
                            if ($item['inline_item'] == 'yes') {
                                $inlineItem = ' inline-useritem';
                                $classElItem .= $inlineItem;
                            }
                            $classElItemEscaped = esc_attr($classElItem);
                            $openLink = $this->get_link_a($item, $user);
                            $closeLink = $openLink ? '</a>' : '';
                            $show_item = \true;
                            if ($item['hide_item']) {
                                $show_item = \false;
                            }
                            $textBefore = '';
                            if ($item['text_before'] != '' && $show_item) {
                                $textBefore = '<span class="tx-before">' . wp_kses_post($item['text_before']) . '</span>';
                            }
                            $user_data_view = '';
                            if ($item['meta'] == 'ID' && $show_item) {
                                $user_data_view = $user->ID;
                            } elseif ($item['meta'] == 'userlogin' && $show_item) {
                                $user_data_view = $user->user_login;
                            } elseif ($item['meta'] == 'nickname' && $show_item) {
                                $user_data_view = $user->nickname;
                            } elseif ($item['meta'] == 'displayname' && $show_item) {
                                $user_data_view = $user->display_name;
                            } elseif ($item['meta'] == 'firstname' && $show_item) {
                                $user_data_view = $user->first_name;
                            } elseif ($item['meta'] == 'lastname' && $show_item) {
                                $user_data_view = $user->last_name;
                            } elseif ($item['meta'] == 'description' && $show_item) {
                                $user_data_view = $user->description;
                            } elseif ($item['meta'] == 'email' && $show_item) {
                                $user_data_view = $user->user_email;
                            } elseif ($item['meta'] == 'website' && $show_item) {
                                $user_data_view = $user->url;
                            } elseif ($item['meta'] == 'avatar' && $show_item) {
                                $user_data_view = get_avatar($user->user_email, $settings['avatar_size']['size']);
                            } elseif ($item['meta'] == 'role' && $show_item) {
                                $user_data_view = $user->roles[0];
                            } elseif ($item['meta'] == 'custommeta' && $show_item) {
                                $user_data_view = $item['meta_key'];
                            } elseif ($item['meta'] == 'button' && $show_item) {
                                $user_data_view = $item['text_button'];
                            }
                            if ($item['meta'] == 'ID') {
                                echo '<div class="user-id tx-el' . $classElItemEscaped . '">' . $openLink . $textBefore . esc_html($user_data_view) . $closeLink . '</div>';
                            } elseif ($item['meta'] == 'userlogin') {
                                echo '<div class="user-userlogin tx-el' . $classElItemEscaped . '">' . $openLink . $textBefore . esc_html($user_data_view) . $closeLink . '</div>';
                            } elseif ($item['meta'] == 'nickname' && !empty($user_data_view)) {
                                echo '<div class="user-nickname tx-el' . $classElItemEscaped . '">' . $openLink . $textBefore . esc_html($user_data_view) . $closeLink . '</div>';
                            } elseif ($item['meta'] == 'displayname' && !empty($user_data_view)) {
                                echo '<div class="user-displayname tx-el' . $classElItemEscaped . '">' . $openLink . $textBefore . esc_html($user_data_view) . $closeLink . '</div>';
                            } elseif ($item['meta'] == 'firstname' && !empty($user_data_view)) {
                                echo '<div class="user-firstname tx-el' . $classElItemEscaped . '">' . $openLink . $textBefore . esc_html($user_data_view) . $closeLink . '</div>';
                            } elseif ($item['meta'] == 'lastname' && !empty($user_data_view)) {
                                echo '<div class="user-lastname tx-el' . $classElItemEscaped . '">' . $openLink . $textBefore . esc_html($user_data_view) . $closeLink . '</div>';
                            } elseif ($item['meta'] == 'description' && !empty($user_data_view)) {
                                echo '<div class="user-description tx-el' . $classElItemEscaped . '">' . $openLink . $textBefore . esc_html($user_data_view) . $closeLink . '</div>';
                            } elseif ($item['meta'] == 'email') {
                                echo '<div class="user-email tx-el' . $classElItemEscaped . '">' . $openLink . $textBefore . esc_html(antispambot($user_data_view)) . $closeLink . '</div>';
                            } elseif ($item['meta'] == 'website' && !empty($user_data_view)) {
                                echo '<div class="user-website tx-el' . $classElItemEscaped . '">' . $openLink . $textBefore . '<a href="' . esc_url($user_data_view) . '">' . esc_html($user_data_view) . '</a>' . $closeLink . '</div>';
                            } elseif ($item['meta'] == 'avatar' && $settings['layout_position'] == '') {
                                echo '<div class="user-avatar' . $classElItemEscaped . '">' . $openLink . $textBefore . wp_kses_post($user_data_view) . $closeLink . '</div>';
                            } elseif ($item['meta'] == 'role') {
                                echo '<div class="user-role tx-el' . $classElItemEscaped . '">' . $openLink . $textBefore . esc_html($user_data_view) . $closeLink . '</div>';
                            } elseif ($item['meta'] == 'button') {
                                echo '<div class="user-button tx-el' . $classElItemEscaped . '">' . $openLink . $textBefore . esc_html($user_data_view) . $closeLink . '</div>';
                            } elseif ($item['meta'] == 'custommeta') {
                                $metak = $item['meta_key'];
                                if (!empty($metak)) {
                                    echo '<div class="user-custommeta tx-el' . $classElItemEscaped . '">' . $openLink . $textBefore;
                                    if (!empty($user_data_view)) {
                                        echo esc_html(get_user_meta($user->ID, $metak, \true));
                                    }
                                    echo $closeLink . '</div>';
                                }
                            } elseif ($item['meta'] == 'attachments') {
                                $user_ids_att = array();
                                $user_ids_att[] = $user->ID;
                                echo '<div class="user-attachments' . $classElItemEscaped . '">';
                                $this->show_attachments($textBefore, $user_ids_att, $item['size_attachment_size'], $item['attachment_url']);
                                echo '</div>';
                            } elseif ($item['meta'] == 'articles') {
                                $user_ids_att = array();
                                $user_ids_att[] = $user->ID;
                                echo '<div class="user-articles' . $classElItemEscaped . '">';
                                $this->show_posts($textBefore, $user_ids_att, $item['size_articles_size'], $item['article_post_type'], $item['articles_url']);
                                echo '</div>';
                            }
                        }
                        echo '</div>';
                    }
                } else {
                    global $wp_query;
                    $original_queried_object = $wp_query->queried_object;
                    $original_queried_object_id = $wp_query->queried_object_id;
                    global $current_user;
                    $original_user = $current_user;
                    $current_user = $user;
                    global $authordata;
                    $original_author = $authordata;
                    $authordata = $current_user;
                    if ($authordata) {
                        $wp_query->queried_object = $authordata;
                        $wp_query->queried_object_id = $authordata->ID;
                    }
                    $template_system = \DynamicContentForElementor\Plugin::instance()->template_system;
                    echo $template_system->build_elementor_template_special(['id' => $settings['users_render_template'], 'inlinecss' => \Elementor\Plugin::$instance->editor->is_edit_mode()]);
                    $authordata = $original_author;
                    $current_user = $original_user;
                    $wp_query->queried_object = $original_queried_object;
                    $wp_query->queried_object_id = $original_queried_object_id;
                }
                echo '</div>';
            }
        }
        echo '</div>';
        if ($settings['pagination_enable'] && $settings['results_per_page'] != '-1') {
            $this->numeric_query_pagination(\intval(\ceil($number_of_users / $settings['results_per_page'])), $settings);
        }
    }
    /**
     * Show Attachments
     *
     * @param string $text_before
     * @param array<int,int> $users
     * @param string $size_attach
     * @param string $is_attachment_url
     * @return void
     */
    protected function show_attachments($text_before, $users, $size_attach, $is_attachment_url)
    {
        $attachments = get_posts(['author__in' => $users, 'post_type' => 'attachment', 'numberposts' => -1, 'post_status' => 'inherit', 'orderby' => 'title']);
        if (empty($attachments)) {
            return;
        }
        echo '<div class="grid-attach">';
        echo $text_before;
        foreach ($attachments as $media) {
            echo '<div class="item_attach">';
            if ($is_attachment_url == 'yes') {
                echo '<a href="' . esc_url((string) get_permalink($media->ID)) . '">';
            }
            echo wp_get_attachment_image($media->ID, $size_attach);
            if ($is_attachment_url == 'yes') {
                echo '</a>';
            }
            echo '</div>';
        }
        echo '</div>';
    }
    protected function have_articles($userId)
    {
        $get_articles = get_posts(['author__in' => $userId, 'post_type' => 'any', 'numberposts' => 1, 'post_status' => 'publish', 'public' => \true, 'fields' => 'ids', 'no_found_rows' => \true, 'update_post_meta_cache' => \false, 'update_post_term_cache' => \false]);
        return \count($get_articles);
    }
    /**
     * Show Posts
     *
     * @param string $text_before
     * @param array<int,int> $users
     * @param string $size_art
     * @param string $post_type
     * @param string $is_article_url
     * @return void
     */
    protected function show_posts($text_before, $users, $size_art, $post_type, $is_article_url)
    {
        $post_type = \DynamicContentForElementor\Helper::validate_post_types($post_type);
        if (!$post_type) {
            return;
        }
        $get_articles = get_posts(['author__in' => $users, 'post_type' => $post_type, 'numberposts' => -1, 'post_status' => 'publish', 'public' => \true, 'orderby' => 'title']);
        echo '<div class="grid-articles">';
        if (!empty($get_articles)) {
            echo $text_before;
            foreach ($get_articles as $art) {
                $featured_image_id = get_post_thumbnail_id($art->ID);
                echo '<div class="item_article">';
                if ($is_article_url == 'yes') {
                    echo '<a href="' . esc_url((string) get_permalink($art->ID)) . '">';
                }
                if ($featured_image_id) {
                    echo wp_get_attachment_image($featured_image_id, $size_art);
                } else {
                    echo '<img src="' . esc_url(\Elementor\Utils::get_placeholder_image_src()) . '" />';
                }
                echo '<div class="tit-art tx-el">' . wp_kses_post(get_the_title($art->ID)) . '</div>';
                if ($is_article_url == 'yes') {
                    echo '</a>';
                }
                echo '</div>';
            }
        }
        echo '</div>';
    }
    public function get_link_a($item, $user)
    {
        $urlToPage = '';
        $target = '';
        $openLink = '';
        if ($item['link_to_page']) {
            if ($item['link_to'] == 'other_url' && $item['meta_field_url']) {
                $urlToPage = esc_url($user->{$item['meta_field_url']});
                if (isset($item['meta_field_url_target_blank']) && $item['meta_field_url_target_blank']) {
                    $target = 'target="_blank"';
                }
            } elseif ($item['link_to'] == 'user_page') {
                $urlToPage = get_author_posts_url($user->ID);
            } elseif ($item['link_to'] == 'custom') {
                if (!empty($item['custom_link']['url'])) {
                    $urlToPage = esc_url($item['custom_link']['url']);
                } else {
                    $urlToPage = \false;
                }
                $target = !empty($item['custom_link']['is_external']) ? 'target="_blank"' : '';
            }
            if ($item['link_to_page'] == 'yes' && $urlToPage != '') {
                $openLink = '<a data-dnc="layout_position" href="' . esc_url($urlToPage) . '" ' . $target . '>';
            }
        }
        return $openLink;
    }
    protected function get_current_page()
    {
        if ('' === $this->get_settings('pagination_enable')) {
            return 1;
        }
        return \max(1, get_query_var('paged'), get_query_var('page'));
    }
    /**
     * @param array<string,mixed> $settings
     * @param string $key
     * @return array{left: string|false, right: string|false}
     */
    protected function get_leftright_icon($settings, $key)
    {
        $old_key = $key;
        $new_key = 'selected_' . $key;
        $migration_allowed = Icons_Manager::is_migration_allowed();
        // old default
        if (!isset($settings[$old_key]) && !$migration_allowed) {
            $settings[$old_key] = 'fa fa-long-arrow-right';
        }
        $migrated = isset($settings['__fa4_migrated'][$new_key]);
        $is_new = empty($settings[$old_key]) && $migration_allowed;
        $icon = ['right' => '', 'left' => ''];
        if ($migrated || $is_new) {
            \ob_start();
            Icons_Manager::render_icon($settings[$new_key] ?? '', ['aria-hidden' => 'true']);
            $icon['right'] = \ob_get_clean();
            $left = \str_replace('right', 'left', $settings[$new_key] ?? '');
            \ob_start();
            Icons_Manager::render_icon($left, ['aria-hidden' => 'true']);
            $icon['left'] = \ob_get_clean();
        } else {
            $prefix = \str_replace('right', '', $settings[$old_key]);
            $icon['left'] = '<i class="' . esc_attr($prefix . 'left') . '"></i>';
            $icon['right'] = '<i class="' . esc_attr($prefix . 'right') . '"></i>';
        }
        return $icon;
    }
    /**
     *  Numeric Query Pagination
     *
     * @param int|string $pages
     * @param array<mixed> $settings
     * @return void
     */
    protected function numeric_query_pagination($pages, $settings)
    {
        // Inline SVG icons not supported
        $icon_prevnext = self::get_leftright_icon($settings, 'pagination_icon_prevnext');
        $icon_firstlast = self::get_leftright_icon($settings, 'pagination_icon_firstlast');
        $range = (int) $settings['pagination_range'] - 1;
        // The numbers displayed at a time
        $showitems = $range * 2 + 1;
        $paged = \max(1, get_query_var('paged'), get_query_var('page'));
        if (empty($paged)) {
            $paged = 1;
        }
        if ($pages == '') {
            global $wp_query;
            $pages = $wp_query->max_num_pages;
            if (!$pages) {
                $pages = 1;
            }
        }
        if ($pages !== 1) {
            echo '<div class="dce-pagination">';
            // Progression
            if ($settings['pagination_show_progression']) {
                echo '<span class="progression">' . esc_html($paged) . ' / ' . esc_html($pages) . '</span>';
            }
            // First
            if ($settings['pagination_show_firstlast']) {
                if ($paged > 2 && $paged > $range + 1 && $showitems < $pages) {
                    $link = Helper::get_wp_link_page(1);
                    echo '<a href="' . esc_url($link) . '" class="pagefirst">' . $icon_firstlast['left'] . ' ' . wp_kses_post($settings['pagination_first_label']) . '</a>';
                }
            }
            // Prev
            if ($settings['pagination_show_prevnext']) {
                if ($paged > 1 && $showitems < $pages) {
                    $link = Helper::get_wp_link_page($paged - 1);
                    echo '<a href="' . esc_url($link) . '" class="pageprev">' . $icon_prevnext['left'] . ' ' . wp_kses_post($settings['pagination_prev_label']) . '</a>';
                }
            }
            // Numbers
            if ($settings['pagination_show_numbers']) {
                for ($i = 1; $i <= $pages; $i++) {
                    if (1 != $pages && (!($i >= $paged + $range + 1 || $i <= $paged - $range - 1) || $pages <= $showitems)) {
                        $link = Helper::get_wp_link_page($i);
                        echo $paged == $i ? '<span class="current">' . $i . '</span>' : '<a href="' . esc_url($link) . '" class="inactive">' . $i . '</a>';
                    }
                }
            }
            // Next
            if ($settings['pagination_show_prevnext']) {
                if ($paged < $pages && $showitems < $pages) {
                    $link = Helper::get_wp_link_page($paged + 1);
                    echo '<a href="' . esc_url($link) . '" class="pagenext">' . wp_kses_post($settings['pagination_next_label']) . ' ' . $icon_prevnext['right'] . '</a>';
                }
            }
            // Last
            if ($settings['pagination_show_firstlast']) {
                if ($paged < $pages - 1 && $paged + $range - 1 < $pages && $showitems < $pages) {
                    $link = Helper::get_wp_link_page($pages);
                    echo '<a href="' . esc_url($link) . '" class="pagelast">' . wp_kses_post($settings['pagination_last_label']) . ' ' . $icon_firstlast['right'] . '</a>';
                }
            }
            echo '</div>';
        }
    }
}
