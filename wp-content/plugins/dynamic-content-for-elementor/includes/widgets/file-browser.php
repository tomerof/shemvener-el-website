<?php

// SPDX-FileCopyrightText: 2018-2026 Ovation S.r.l. <help@dynamic.ooo>
// SPDX-License-Identifier: GPL-3.0-or-later
namespace DynamicContentForElementor\Widgets;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Box_Shadow;
use DynamicContentForElementor\Controls\Group_Control_Filters_HSB;
use DynamicContentForElementor\Helper;
if (!\defined('ABSPATH')) {
    exit;
    // Exit if accessed directly
}
class FileBrowser extends \DynamicContentForElementor\Widgets\WidgetPrototype
{
    /**
     * @var array<string,mixed>
     */
    public $file_metadata = [];
    // save it in a hidden field in json, values only for this post
    /**
     * @var bool|null
     */
    protected $is_authorized = null;
    /**
     * @return array<string>
     */
    public function get_style_depends()
    {
        return ['dce-filebrowser', 'dce-file-icon'];
    }
    /**
     * @return array<string>
     */
    public function get_script_depends()
    {
        return ['dce-file-browser'];
    }
    /**
     * Register controls after check if this feature is only for admin
     *
     * @return void
     */
    protected function safe_register_controls()
    {
        $this->start_controls_section('section_filebrowser', ['label' => esc_html__('FileBrowser', 'dynamic-content-for-elementor')]);
        $this->add_control('path_selection', ['label' => esc_html__('Select path', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CHOOSE, 'options' => ['custom' => ['title' => esc_html__('Custom', 'dynamic-content-for-elementor'), 'icon' => 'eicon-custom'], 'media' => ['title' => esc_html__('Media Library', 'dynamic-content-for-elementor'), 'icon' => 'eicon-image'], 'taxonomy' => ['title' => esc_html__('Taxonomy', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-tags'], 'post' => ['title' => esc_html__('Post Medias', 'dynamic-content-for-elementor'), 'icon' => 'eicon-post']], 'default' => 'custom', 'toggle' => \false]);
        $this->add_control('folder_custom', ['label' => esc_html__('Custom Path', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'placeholder' => 'myfolder/docs', 'description' => esc_html__('A custom path from site root', 'dynamic-content-for-elementor'), 'default' => 'wp-content/uploads', 'condition' => ['path_selection' => ['custom']]]);
        $this->add_control('medias', ['label' => esc_html__('Choose Files', 'dynamic-content-for-elementor'), 'type' => \Elementor\Controls_Manager::WYSIWYG, 'condition' => ['path_selection' => 'media']]);
        $this->add_control('remove_media', ['label' => esc_html__('Remove All Files', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::BUTTON, 'event' => 'dceFileBrowser:removeMedia', 'text' => esc_html__('Remove', 'dynamic-content-for-elementor'), 'description' => '', 'condition' => ['path_selection' => 'media']]);
        $taxonomies = Helper::get_taxonomies(\false, 'attachment');
        $this->add_control('taxonomy', ['label' => esc_html__('Select Taxonomy', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'options' => ['' => esc_html__('None', 'dynamic-content-for-elementor')] + $taxonomies, 'description' => esc_html__('Use selected taxonomy as folder', 'dynamic-content-for-elementor'), 'label_block' => \true, 'condition' => ['path_selection' => 'taxonomy']]);
        if (!empty($taxonomies)) {
            foreach ($taxonomies as $tkey => $atax) {
                if ($tkey) {
                    $terms = Helper::get_taxonomy_terms($tkey, \true);
                    $this->add_control('terms_' . $tkey, ['label' => esc_html__('Terms', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT2, 'options' => ['' => esc_html__('All', 'dynamic-content-for-elementor')] + $terms, 'description' => esc_html__('Filter results by selected taxonomy terms', 'dynamic-content-for-elementor'), 'label_block' => \true, 'condition' => ['taxonomy' => $tkey, 'path_selection' => 'taxonomy']]);
                }
            }
        }
        $this->add_control('private_access', ['label' => esc_html__('Restrict downloads through this widget', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'separator' => 'before', 'description' => esc_html__('Restricts download links via this widget. It does not hide the file list and does not change server permissions. Note: Files remain directly accessible via URL if users know the path.', 'dynamic-content-for-elementor'), 'condition' => ['path_selection' => ['custom']]]);
        $this->add_control('user_role', ['label' => esc_html__('Roles', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT2, 'options' => wp_roles()->get_names() + ['visitor' => 'Visitor (User not logged in)'], 'placeholder' => esc_html__('Roles', 'dynamic-content-for-elementor'), 'label_block' => \true, 'multiple' => \true, 'description' => esc_html__('Restricts downloads via this widget to selected roles. It does not hide the file list.', 'dynamic-content-for-elementor'), 'condition' => ['private_access!' => '', 'path_selection' => ['custom']]]);
        $this->add_control('user_redirect', ['label' => esc_html__('Redirect', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::URL, 'placeholder' => esc_html__('/subscribe-plan-page', 'dynamic-content-for-elementor'), 'description' => esc_html__('Redirect unauthorized download attempts via this widget.', 'dynamic-content-for-elementor'), 'condition' => ['private_access!' => '', 'path_selection' => ['custom']]]);
        $this->add_control('title', ['label' => esc_html__('Show folder title', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'separator' => 'before', 'condition' => ['path_selection!' => 'media']]);
        $this->add_control('title_size', ['label' => esc_html__('Title HTML Tag', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'options' => Helper::get_html_tags(), 'default' => 'h4', 'condition' => ['path_selection!' => 'media', 'title!' => '']]);
        $this->add_control('empty', ['label' => esc_html__('Show empty folders', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'condition' => ['path_selection' => ['custom']]]);
        $this->add_control('resized', ['label' => esc_html__('Show resized images', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'description' => esc_html__('WordPress automatically create for every uploaded image many resized version (e.g.:my-image-150x150.png, another-img-310x250.jpg), if you want to view them, enable this setting', 'dynamic-content-for-elementor'), 'condition' => ['path_selection' => ['custom']]]);
        $this->add_control('order', ['label' => esc_html__('Order', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT2, 'options' => array(\SCANDIR_SORT_NONE => esc_html__('None', 'dynamic-content-for-elementor'), 0 => esc_html__('Ascending', 'dynamic-content-for-elementor'), 1 => esc_html__('Descending', 'dynamic-content-for-elementor')), 'default' => '0', 'description' => esc_html__('Select file order', 'dynamic-content-for-elementor'), 'condition' => ['path_selection' => ['custom']]]);
        $this->add_control('file_type', ['label' => esc_html__('Filter by file extension', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'placeholder' => 'gif, jpg, png', 'description' => esc_html__('Show only specific file types. Separate each extension by comma', 'dynamic-content-for-elementor'), 'condition' => ['path_selection!' => 'media']]);
        $this->add_control('file_type_show', ['label' => esc_html__('Show/Hide specified file types', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'label_off' => esc_html__('Hide', 'dynamic-content-for-elementor'), 'label_on' => esc_html__('Show', 'dynamic-content-for-elementor'), 'default' => 'yes', 'condition' => ['file_type!' => '']]);
        $this->add_control('img_icon', ['label' => esc_html__('Use thumbnail for images', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'description' => esc_html__('If the file is an image then use it\'s thumb as icon', 'dynamic-content-for-elementor'), 'condition' => ['path_selection' => 'media']]);
        $this->add_control('search', ['label' => esc_html__('Quick search form', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'separator' => 'before']);
        $this->add_control('enable_metadata', ['separator' => 'before', 'label' => esc_html__('Metadata info', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER]);
        $this->end_controls_section();
        $this->start_controls_section('section_file_form', ['label' => esc_html__('Search Form', 'dynamic-content-for-elementor'), 'condition' => ['search!' => '']]);
        $this->add_control('search_text', ['label' => esc_html__('Search Text', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'default' => esc_html__('Quick Search', 'dynamic-content-for-elementor')]);
        $this->add_control('search_text_size', ['label' => esc_html__('Form Title HTML Tag', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'options' => Helper::get_html_tags(), 'default' => 'h4', 'condition' => ['search_text!' => '']]);
        $this->add_control('search_notice', ['label' => esc_html__('Search Notice', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'default' => esc_html__('* at least 3 characters', 'dynamic-content-for-elementor'), 'placeholder' => esc_html__('* at least 3 characters', 'dynamic-content-for-elementor')]);
        $this->add_control('search_quick', ['label' => esc_html__('Quick Search', 'dynamic-content-for-elementor'), 'description' => esc_html__('Search on input change, no buttons needed', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER]);
        $this->add_control('search_find_text', ['label' => esc_html__('Find Text', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'default' => esc_html__('Find', 'dynamic-content-for-elementor'), 'condition' => ['search_quick' => '']]);
        $this->add_control('search_reset', ['label' => esc_html__('Reset Button', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'condition' => ['search_quick' => '']]);
        $this->add_control('search_reset_text', ['label' => esc_html__('Reset Text', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'default' => esc_html__('Reset', 'dynamic-content-for-elementor'), 'condition' => ['search_quick' => '', 'search_reset!' => '']]);
        $this->end_controls_section();
        $this->start_controls_section('section_file_metadata', ['label' => esc_html__('Metadata', 'dynamic-content-for-elementor'), 'condition' => ['enable_metadata!' => '']]);
        $this->add_control('extension', ['label' => esc_html__('Show file extension', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'condition' => ['enable_metadata' => 'yes']]);
        $this->add_control('enable_metadata_size', ['label' => esc_html__('Show file size', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'condition' => ['enable_metadata' => 'yes']]);
        $this->add_control('enable_metadata_hits', ['label' => esc_html__('Add a download counter for statistics', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'condition' => ['enable_metadata' => 'yes']]);
        $this->add_control('enable_metadata_wp_title', ['label' => esc_html__('Show WP Title', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'description' => esc_html__('Display Media Title instead of filename for files in WordPress Media Library', 'dynamic-content-for-elementor'), 'condition' => ['path_selection' => ['media', 'custom']]]);
        $this->add_control('enable_metadata_wp_description', ['label' => esc_html__('Show WP Caption', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'description' => esc_html__('Display Media Caption as description for files managed by WordPress Media Library', 'dynamic-content-for-elementor'), 'condition' => ['enable_metadata' => 'yes', 'path_selection' => ['media', 'post']]]);
        $this->add_control('manage_custom_titles', ['type' => Controls_Manager::BUTTON, 'text' => esc_html__('Manage Custom Titles', 'dynamic-content-for-elementor'), 'button_type' => 'default', 'event' => 'dce:manageFileBrowserTitles', 'separator' => 'before', 'description' => esc_html__('Set custom display titles for files and folders', 'dynamic-content-for-elementor'), 'condition' => ['path_selection' => 'custom']]);
        $this->end_controls_section();
        $this->start_controls_section('section_style_title', ['label' => esc_html__('Title', 'dynamic-content-for-elementor'), 'tab' => Controls_Manager::TAB_STYLE, 'condition' => ['title' => 'yes']]);
        $this->add_responsive_control('title_align', ['label' => esc_html__('Alignment', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CHOOSE, 'options' => ['left' => ['title' => esc_html__('Left', 'dynamic-content-for-elementor'), 'icon' => 'eicon-text-align-left'], 'center' => ['title' => esc_html__('Center', 'dynamic-content-for-elementor'), 'icon' => 'eicon-text-align-center'], 'right' => ['title' => esc_html__('Right', 'dynamic-content-for-elementor'), 'icon' => 'eicon-text-align-right'], 'justify' => ['title' => esc_html__('Justified', 'dynamic-content-for-elementor'), 'icon' => 'eicon-text-align-justify']], 'default' => '', 'selectors' => ['{{WRAPPER}} .dce-filebrowser-title' => 'text-align: {{VALUE}};']]);
        $this->add_control('title_color', ['label' => esc_html__('Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .dce-filebrowser-title' => 'color: {{VALUE}};']]);
        $this->add_group_control(Group_Control_Typography::get_type(), ['name' => 'title_typography', 'label' => esc_html__('Typography', 'dynamic-content-for-elementor'), 'selector' => '{{WRAPPER}} .dce-filebrowser-title']);
        $this->add_group_control(Group_Control_Text_Shadow::get_type(), ['name' => 'title_text_shadow', 'selector' => '{{WRAPPER}} .dce-filebrowser-title']);
        $this->add_control('title_space', ['label' => esc_html__('Space', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => 0], 'range' => ['px' => ['min' => 0, 'max' => 100]], 'selectors' => ['{{WRAPPER}} .dce-filebrowser-title' => 'padding-bottom: {{SIZE}}{{UNIT}};']]);
        $this->end_controls_section();
        $this->start_controls_section('section_style_folders', ['label' => esc_html__('Folders', 'dynamic-content-for-elementor'), 'tab' => Controls_Manager::TAB_STYLE]);
        $this->add_control('foldername_color', ['label' => esc_html__('Name Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} a .dce-dir-title' => 'color: {{VALUE}};']]);
        $this->add_control('foldername_color_hover', ['label' => esc_html__('Name Hover Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} a:hover .dce-dir-title' => 'color: {{VALUE}};']]);
        $this->add_group_control(Group_Control_Typography::get_type(), ['name' => 'foldername_typography', 'label' => esc_html__('Typography', 'dynamic-content-for-elementor'), 'selector' => '{{WRAPPER}} a .dce-dir-title']);
        $this->add_group_control(Group_Control_Text_Shadow::get_type(), ['name' => 'foldername_text_shadow', 'selector' => '{{WRAPPER}} a .dce-dir-title']);
        $this->add_control('heading_folders_border', ['label' => esc_html__('Border', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HEADING, 'separator' => 'before']);
        $this->add_control('folder_border_type', ['label' => esc_html__('Border type', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'default' => 'solid', 'options' => ['solid' => esc_html__('Solid', 'dynamic-content-for-elementor'), 'dashed' => esc_html__('Dashed', 'dynamic-content-for-elementor'), 'dotted' => esc_html__('Dotted', 'dynamic-content-for-elementor'), 'double' => esc_html__('Double', 'dynamic-content-for-elementor'), 'none' => esc_html__('None', 'dynamic-content-for-elementor')], 'selectors' => ['{{WRAPPER}} .dce-list li.dir' => 'border-bottom-style: {{VALUE}}']]);
        $this->add_control('folder_border_color', ['label' => esc_html__('Border color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'condition' => ['folder_border_type!' => 'none'], 'selectors' => ['{{WRAPPER}} .dce-list li.dir' => 'border-bottom-color: {{VALUE}};']]);
        $this->add_control('folder_border_stroke', ['label' => esc_html__('Border weight', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => 1], 'range' => ['px' => ['min' => 0, 'max' => 10]], 'condition' => ['folder_border_type!' => 'none'], 'selectors' => ['{{WRAPPER}} .dce-list li.dir' => 'border-bottom-width: {{SIZE}}{{UNIT}};']]);
        $this->add_control('folder_list_space', ['label' => esc_html__('Row Space', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => 10], 'range' => ['px' => ['min' => 3, 'max' => 100]], 'separator' => 'before', 'selectors' => ['{{WRAPPER}} .dce-list li.dir' => 'padding-top: {{SIZE}}{{UNIT}}; padding-bottom: {{SIZE}}{{UNIT}};']]);
        $this->add_responsive_control('folder_list_padding', ['label' => esc_html__('Space around', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => ['px', '%'], 'separator' => 'after', 'selectors' => ['{{WRAPPER}} .dce-list-root' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};']]);
        // Icons
        $this->add_control('heading_folders_icon', ['label' => esc_html__('Icons', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HEADING, 'separator' => 'before']);
        $this->add_responsive_control('folder_icon_size', ['label' => esc_html__('Size', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => 40], 'range' => ['px' => ['min' => 0, 'max' => 180]], 'selectors' => ['{{WRAPPER}} .dce-list .fiv-icon-folder' => 'font-size: {{SIZE}}{{UNIT}};']]);
        $this->add_responsive_control('folder_icon_space', ['label' => esc_html__('Space', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => 10], 'range' => ['px' => ['min' => -50, 'max' => 100]], 'selectors' => ['{{WRAPPER}} .dce-list .fiv-icon-folder' => 'margin-right: {{SIZE}}{{UNIT}};']]);
        $this->add_group_control(Group_Control_Filters_HSB::get_type(), ['name' => 'icon_hue_filters', 'label' => esc_html__('Color (HSB)', 'dynamic-content-for-elementor'), 'selector' => '{{WRAPPER}} .dce-list .fiv-icon-folder']);
        $this->end_controls_section();
        $this->start_controls_section('section_style_subfolders', ['label' => esc_html__('Sub Folders', 'dynamic-content-for-elementor'), 'tab' => Controls_Manager::TAB_STYLE]);
        $this->add_control('subfoldername_color', ['label' => esc_html__('Name Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} li.dir li.dir a .dce-dir-title' => 'color: {{VALUE}};']]);
        $this->add_control('subfoldername_color_hover', ['label' => esc_html__('Name Hover Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} li.dir li.dir a:hover .dce-dir-title' => 'color: {{VALUE}};']]);
        $this->add_group_control(Group_Control_Typography::get_type(), ['name' => 'subfoldername_typography', 'label' => esc_html__('Typography', 'dynamic-content-for-elementor'), 'selector' => '{{WRAPPER}} li.dir li.dir a .dce-dir-title']);
        $this->add_group_control(Group_Control_Text_Shadow::get_type(), ['name' => 'subfoldername_text_shadow', 'selector' => '{{WRAPPER}} li.dir li.dir a .dce-dir-title']);
        $this->add_control('heading_subfolders_border', ['label' => esc_html__('Border', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HEADING, 'separator' => 'before']);
        $this->add_control('subfolder_border_type', ['label' => esc_html__('Border type', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'default' => 'solid', 'options' => ['solid' => esc_html__('Solid', 'dynamic-content-for-elementor'), 'dashed' => esc_html__('Dashed', 'dynamic-content-for-elementor'), 'dotted' => esc_html__('Dotted', 'dynamic-content-for-elementor'), 'double' => esc_html__('Double', 'dynamic-content-for-elementor'), 'none' => esc_html__('None', 'dynamic-content-for-elementor')], 'selectors' => ['{{WRAPPER}} .dce-list li.dir li.dir' => 'border-bottom-style: {{VALUE}}']]);
        $this->add_control('subfolder_border_color', ['label' => esc_html__('Border color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'condition' => ['subfolder_border_type!' => 'none'], 'selectors' => ['{{WRAPPER}} .dce-list li.dir li.dir' => 'border-bottom-color: {{VALUE}};']]);
        $this->add_control('subfolder_border_stroke', ['label' => esc_html__('Border weight', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => 1], 'range' => ['px' => ['min' => 0, 'max' => 10]], 'condition' => ['subfolder_border_type!' => 'none'], 'selectors' => ['{{WRAPPER}} .dce-list li.dir li.dir' => 'border-bottom-width: {{SIZE}}{{UNIT}};']]);
        $this->add_control('subfolder_list_space', ['label' => esc_html__('Row Space', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => 10], 'range' => ['px' => ['min' => 3, 'max' => 100]], 'separator' => 'before', 'selectors' => ['{{WRAPPER}} .dce-list li.dir li.dir' => 'padding-top: {{SIZE}}{{UNIT}}; padding-bottom: {{SIZE}}{{UNIT}};']]);
        $this->add_control('heading_subfolders_icon', ['label' => esc_html__('Icons', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HEADING, 'separator' => 'before']);
        $this->add_control('subfolder_icon_size', ['label' => esc_html__('Size', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => 40], 'range' => ['px' => ['min' => 0, 'max' => 180]], 'selectors' => ['{{WRAPPER}} .dce-list li.dir li.dir .fiv-icon-folder' => 'font-size: {{SIZE}}{{UNIT}};']]);
        $this->add_control('subfolder_icon_space', ['label' => esc_html__('Space', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => 10], 'range' => ['px' => ['min' => -50, 'max' => 100]], 'selectors' => ['{{WRAPPER}} .dce-list li.dir li.dir .fiv-icon-subfolder' => 'margin-right: {{SIZE}}{{UNIT}};']]);
        $this->add_group_control(Group_Control_Filters_HSB::get_type(), ['name' => 'subf_icon_hue_filters', 'label' => esc_html__('Color (HSB)', 'dynamic-content-for-elementor'), 'selector' => '{{WRAPPER}} .dce-list li.dir li.dir .fiv-icon-folder']);
        $this->end_controls_section();
        $this->start_controls_section('section_style_files', ['label' => esc_html__('Files', 'dynamic-content-for-elementor'), 'tab' => Controls_Manager::TAB_STYLE]);
        $this->add_control('filename_color', ['label' => esc_html__('Name Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} a.dce-file-download' => 'color: {{VALUE}};']]);
        $this->add_control('filename_color_hover', ['label' => esc_html__('Name Hover Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} a.dce-file-download:hover' => 'color: {{VALUE}};']]);
        $this->add_group_control(Group_Control_Typography::get_type(), ['name' => 'filename_typography', 'label' => esc_html__('Typography', 'dynamic-content-for-elementor'), 'selector' => '{{WRAPPER}} a .dce-file-title']);
        $this->add_group_control(Group_Control_Text_Shadow::get_type(), ['name' => 'filename_text_shadow', 'selector' => '{{WRAPPER}} a .dce-file-title']);
        $this->add_control('heading_files_border', ['label' => esc_html__('Border', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HEADING, 'separator' => 'before']);
        $this->add_control('file_border_type', ['label' => esc_html__('Border type', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'default' => 'solid', 'options' => ['solid' => esc_html__('Solid', 'dynamic-content-for-elementor'), 'dashed' => esc_html__('Dashed', 'dynamic-content-for-elementor'), 'dotted' => esc_html__('Dotted', 'dynamic-content-for-elementor'), 'double' => esc_html__('Double', 'dynamic-content-for-elementor'), 'none' => esc_html__('None', 'dynamic-content-for-elementor')], 'selectors' => ['{{WRAPPER}} .dce-list li.file' => 'border-bottom-style: {{VALUE}}']]);
        $this->add_control('file_border_color', ['label' => esc_html__('Border color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'condition' => ['file_border_type!' => 'none'], 'selectors' => ['{{WRAPPER}} .dce-list li.file' => 'border-bottom-color: {{VALUE}};']]);
        $this->add_control('file_border_stroke', ['label' => esc_html__('Border weight', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => 1], 'range' => ['px' => ['min' => 0, 'max' => 10]], 'condition' => ['file_border_type!' => 'none'], 'selectors' => ['{{WRAPPER}} .dce-list li.file' => 'border-bottom-width: {{SIZE}}{{UNIT}};']]);
        $this->add_control('file_list_space', ['label' => esc_html__('Row Space', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => 10], 'range' => ['px' => ['min' => 3, 'max' => 100]], 'separator' => 'before', 'selectors' => ['{{WRAPPER}} .dce-list li.file' => 'padding-top: {{SIZE}}{{UNIT}}; padding-bottom: {{SIZE}}{{UNIT}};']]);
        $this->add_control('heading_files_icon', ['label' => esc_html__('Icons', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HEADING, 'separator' => 'before']);
        $this->add_control('file_icon_size', ['label' => esc_html__('Size', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => 40], 'range' => ['px' => ['min' => 10, 'max' => 180]], 'selectors' => ['{{WRAPPER}} .dce-list .dce-file-download .fiv-viv' => 'font-size: {{SIZE}}{{UNIT}};', '{{WRAPPER}} .dce-list .dce-file-download .dce-img-icon' => 'width: {{SIZE}}{{UNIT}}; height: auto;', '{{WRAPPER}} .dce-list .dce-file-description' => 'margin-left: {{SIZE}}{{UNIT}};']]);
        $this->add_control('file_icon_space', ['label' => esc_html__('Space', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => 10], 'range' => ['px' => ['min' => 0, 'max' => 100]], 'selectors' => ['{{WRAPPER}} .dce-list .dce-file-download .fiv-viv' => 'margin-right: {{SIZE}}{{UNIT}};', '{{WRAPPER}} .dce-list .dce-file-download .dce-img-icon' => 'margin-right: {{SIZE}}{{UNIT}};']]);
        $this->add_group_control(Group_Control_Filters_HSB::get_type(), ['name' => 'fileicon_hue_filters', 'label' => esc_html__('Color (HSB)', 'dynamic-content-for-elementor'), 'selector' => '{{WRAPPER}} .dce-list .dce-file-download .fiv-viv']);
        $this->add_control('heading_files_size', ['label' => esc_html__('Label Size', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HEADING, 'separator' => 'before', 'condition' => ['enable_metadata_size' => 'yes']]);
        $this->add_control('filesizes_color', ['label' => esc_html__('Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'condition' => ['enable_metadata_size' => 'yes'], 'selectors' => ['{{WRAPPER}} .dce-list .dce-file-download .dce-file-size-label' => 'color: {{VALUE}};']]);
        $this->add_control('filesize_icon_size', ['label' => esc_html__('Size', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => ''], 'range' => ['px' => ['min' => 10, 'max' => 180]], 'condition' => ['enable_metadata_size' => 'yes'], 'selectors' => ['{{WRAPPER}} .dce-list .dce-file-download .dce-file-size-label' => 'font-size: {{SIZE}}{{UNIT}};']]);
        $this->add_control('heading_files_hits', ['label' => esc_html__('Label Hits', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HEADING, 'separator' => 'before', 'condition' => ['enable_metadata_hits' => 'yes']]);
        $this->add_control('filehits_color', ['label' => esc_html__('Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'condition' => ['enable_metadata_hits' => 'yes'], 'selectors' => ['{{WRAPPER}} .dce-list .dce-file-hits-label' => 'color: {{VALUE}};']]);
        $this->add_control('filehits_icon_size', ['label' => esc_html__('Size', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => ''], 'range' => ['px' => ['min' => 10, 'max' => 180]], 'condition' => ['enable_metadata_hits' => 'yes'], 'selectors' => ['{{WRAPPER}} .dce-list .dce-file-hits-label' => 'font-size: {{SIZE}}{{UNIT}};']]);
        $this->add_control('filehits_icon_space', ['label' => esc_html__('Space', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => ''], 'range' => ['px' => ['min' => 10, 'max' => 180]], 'condition' => ['enable_metadata_hits' => 'yes'], 'selectors' => ['{{WRAPPER}} .dce-list .dce-file-hits-label' => 'margin-left: {{SIZE}}{{UNIT}};']]);
        $this->end_controls_section();
        $this->start_controls_section('section_style_search', ['label' => esc_html__('Search', 'dynamic-content-for-elementor'), 'tab' => Controls_Manager::TAB_STYLE, 'condition' => ['search' => 'yes']]);
        $this->add_control('align_search', ['label' => esc_html__('Alignment', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CHOOSE, 'options' => ['left' => ['title' => esc_html__('Left', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-left'], 'center' => ['title' => esc_html__('Center', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-center'], 'right' => ['title' => esc_html__('Right', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-right']], 'selectors' => ['{{WRAPPER}} .dce-file-search-form' => 'text-align: {{VALUE}};']]);
        $this->add_control('heading_search_border', ['label' => esc_html__('Border', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HEADING, 'separator' => 'before']);
        $this->add_control('search_border_type', ['label' => esc_html__('Border type', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'default' => 'solid', 'options' => ['solid' => esc_html__('Solid', 'dynamic-content-for-elementor'), 'dashed' => esc_html__('Dashed', 'dynamic-content-for-elementor'), 'dotted' => esc_html__('Dotted', 'dynamic-content-for-elementor'), 'double' => esc_html__('Double', 'dynamic-content-for-elementor'), 'none' => esc_html__('None', 'dynamic-content-for-elementor')], 'selectors' => ['{{WRAPPER}} .dce-file-search-form' => 'border-style: {{VALUE}}']]);
        $this->add_control('search_border_color', ['label' => esc_html__('Border color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'condition' => ['search_border_type!' => 'none'], 'selectors' => ['{{WRAPPER}} .dce-file-search-form' => 'border-color: {{VALUE}};']]);
        $this->add_control('search_border_stroke', ['label' => esc_html__('Border weight', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => 1], 'range' => ['px' => ['min' => 0, 'max' => 10]], 'condition' => ['search_border_type!' => 'none'], 'selectors' => ['{{WRAPPER}} .dce-file-search-form' => 'border-width: {{SIZE}}{{UNIT}};']]);
        $this->add_control('search_border_radius', ['label' => esc_html__('Border Radius', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => 1], 'range' => ['px' => ['min' => 0, 'max' => 50]], 'size_units' => ['px', '%'], 'selectors' => ['{{WRAPPER}} .dce-file-search-form' => 'border-radius: {{SIZE}}{{UNIT}};']]);
        $this->add_control('heading_search_background', ['label' => esc_html__('Background', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HEADING, 'separator' => 'before']);
        $this->add_group_control(Group_Control_Background::get_type(), ['name' => 'background_search', 'types' => ['classic', 'gradient'], 'selector' => '{{WRAPPER}} .dce-file-search-form']);
        $this->add_control('heading_search_title', ['label' => esc_html__('Title', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HEADING, 'separator' => 'before', 'condition' => ['search_text!' => '']]);
        $this->add_control('search_title_color', ['label' => esc_html__('Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .dce-file-search-form-title' => 'color: {{VALUE}};'], 'condition' => ['search_text!' => '']]);
        $this->add_group_control(Group_Control_Typography::get_type(), ['name' => 'search_title_typography', 'label' => esc_html__('Typography', 'dynamic-content-for-elementor'), 'selector' => '{{WRAPPER}} .dce-file-search-form-title', 'condition' => ['search_text!' => '']]);
        $this->add_control('search_title_space', ['label' => esc_html__('Space', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => 10], 'range' => ['px' => ['min' => -50, 'max' => 100]], 'selectors' => ['{{WRAPPER}} .dce-file-search-form-title' => 'margin-bottom: {{SIZE}}{{UNIT}};']]);
        $this->add_group_control(Group_Control_Text_Shadow::get_type(), ['name' => 'search_title_text_shadow', 'selector' => '{{WRAPPER}} .dce-file-search-form-title', 'condition' => ['search_text!' => '']]);
        $this->add_control('heading_search_field', ['label' => esc_html__('Field search', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HEADING, 'separator' => 'before']);
        $this->add_control('search_field_txcolor', ['label' => esc_html__('Text Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} input.filetxt' => 'color: {{VALUE}};']]);
        $this->add_control('search_field_bgcolor', ['label' => esc_html__('Background Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} input.filetxt' => 'background-color: {{VALUE}};']]);
        $this->add_group_control(Group_Control_Typography::get_type(), ['name' => 'search_field_typography', 'label' => esc_html__('Typography', 'dynamic-content-for-elementor'), 'selector' => '{{WRAPPER}} input.filetxt']);
        $this->add_control('search_field_border_radius', ['label' => esc_html__('Border Radius', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => 1], 'range' => ['px' => ['min' => 0, 'max' => 50]], 'size_units' => ['px', '%'], 'selectors' => ['{{WRAPPER}} input.filetxt' => 'border-radius: {{SIZE}}{{UNIT}};']]);
        $this->add_responsive_control('search_field_padding', ['label' => esc_html__('Padding', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => ['px', '%'], 'selectors' => ['{{WRAPPER}} input.filetxt' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};']]);
        $this->add_control('search_field_border_type', ['label' => esc_html__('Border type', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'default' => 'solid', 'options' => ['solid' => esc_html__('Solid', 'dynamic-content-for-elementor'), 'dashed' => esc_html__('Dashed', 'dynamic-content-for-elementor'), 'dotted' => esc_html__('Dotted', 'dynamic-content-for-elementor'), 'double' => esc_html__('Double', 'dynamic-content-for-elementor'), 'none' => esc_html__('None', 'dynamic-content-for-elementor')], 'selectors' => ['{{WRAPPER}} input.filetxt' => 'border-style: {{VALUE}}']]);
        $this->add_control('search_field_border_color', ['label' => esc_html__('Border color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'condition' => ['search_field_border_type!' => 'none'], 'selectors' => ['{{WRAPPER}} input.filetxt' => 'border-color: {{VALUE}};']]);
        $this->add_responsive_control('search_field_border_stroke', ['label' => esc_html__('Borders', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::DIMENSIONS, 'default' => ['top' => '', 'right' => '', 'bottom' => '', 'left' => '', 'isLinked' => \true], 'condition' => ['search_field_border_type!' => 'none'], 'selectors' => ['{{WRAPPER}} input.filetxt' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};']]);
        $this->add_control('search_field_space', ['label' => esc_html__('Space', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => 10], 'range' => ['px' => ['min' => -50, 'max' => 100]], 'selectors' => ['{{WRAPPER}} input.filetxt' => 'margin-bottom: {{SIZE}}{{UNIT}};']]);
        $this->add_group_control(Group_Control_Box_Shadow::get_type(), ['name' => 'search_field_box_shadow', 'exclude' => ['box_shadow_position'], 'selector' => '{{WRAPPER}} input.filetxt']);
        $this->add_control('heading_desc_field', ['label' => esc_html__('Small description', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HEADING, 'separator' => 'before']);
        $this->add_control('search_desc_color', ['label' => esc_html__('Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .dce-search-desc small' => 'color: {{VALUE}};']]);
        $this->add_group_control(Group_Control_Typography::get_type(), ['name' => 'search_desc_typography', 'label' => esc_html__('Typography', 'dynamic-content-for-elementor'), 'selector' => '{{WRAPPER}} .dce-search-desc small']);
        $this->add_control('heading_search_buttons', ['label' => esc_html__('Buttons', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HEADING, 'separator' => 'before']);
        $this->add_group_control(Group_Control_Typography::get_type(), ['name' => 'buttons_typography', 'label' => esc_html__('Typography', 'dynamic-content-for-elementor'), 'selector' => '{{WRAPPER}} .dce-search-buttons input']);
        $this->add_control('buttons_border_radius', ['label' => esc_html__('Border Radius', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => 1], 'range' => ['px' => ['min' => 0, 'max' => 50]], 'size_units' => ['px', '%'], 'selectors' => ['{{WRAPPER}} .dce-search-buttons input' => 'border-radius: {{SIZE}}{{UNIT}};']]);
        $this->add_responsive_control('buttons_padding', ['label' => esc_html__('Padding', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => ['px', '%'], 'selectors' => ['{{WRAPPER}} .dce-search-buttons input' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};']]);
        $this->add_responsive_control('buttons_border_stroke', ['label' => esc_html__('Borders', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::DIMENSIONS, 'default' => ['top' => '', 'right' => '', 'bottom' => '', 'left' => '', 'isLinked' => \true], 'selectors' => ['{{WRAPPER}} .dce-search-buttons input' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};']]);
        $this->add_control('buttons_v_space', ['label' => esc_html__('Vertical Space', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => 0], 'range' => ['px' => ['min' => 0, 'max' => 100]], 'selectors' => ['{{WRAPPER}} .dce-search-buttons input' => 'margin-top: {{SIZE}}{{UNIT}}; margin-bottom: {{SIZE}}{{UNIT}};']]);
        $this->add_control('buttons_h_space', ['label' => esc_html__('Horizontal Space', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => 0], 'range' => ['px' => ['min' => 0, 'max' => 100]], 'selectors' => ['{{WRAPPER}} .dce-search-buttons input' => 'margin-left: {{SIZE}}{{UNIT}}; margin-right: {{SIZE}}{{UNIT}};']]);
        $this->add_control('heading_search_buttonReset', ['label' => esc_html__('Button Reset', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HEADING, 'separator' => 'before', 'condition' => ['search_reset!' => '']]);
        $this->add_control('buttonreset_txcolor', ['label' => esc_html__('Text Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'condition' => ['search_reset!' => ''], 'selectors' => ['{{WRAPPER}} .dce-search-buttons input.reset' => 'color: {{VALUE}};']]);
        $this->add_control('buttonreset_bgcolor', ['label' => esc_html__('Background Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'condition' => ['search_reset!' => ''], 'selectors' => ['{{WRAPPER}} .dce-search-buttons input.reset' => 'background-color: {{VALUE}};']]);
        $this->add_control('buttonreset_border_color', ['label' => esc_html__('Border color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'condition' => ['search_reset!' => ''], 'selectors' => ['{{WRAPPER}} .dce-search-buttons input.reset' => 'border-color: {{VALUE}};']]);
        $this->add_control('buttonreset_txcolor_hover', ['label' => esc_html__('Hover Text Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'condition' => ['search_reset!' => ''], 'selectors' => ['{{WRAPPER}} .dce-search-buttons input.reset:hover' => 'color: {{VALUE}};']]);
        $this->add_control('buttonreset_bgcolor_hover', ['label' => esc_html__('Hover Background Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'condition' => ['search_reset!' => ''], 'selectors' => ['{{WRAPPER}} .dce-search-buttons input.reset:hover' => 'background-color: {{VALUE}};']]);
        $this->add_control('buttonreset_border_color_hover', ['label' => esc_html__('Hover Border color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'condition' => ['search_reset!' => ''], 'selectors' => ['{{WRAPPER}} .dce-search-buttons input.reset:hover' => 'border-color: {{VALUE}};']]);
        $this->add_control('heading_search_buttonFind', ['label' => esc_html__('Button Find', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HEADING, 'separator' => 'before']);
        $this->add_control('buttonfind_txcolor', ['label' => esc_html__('Text Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .dce-search-buttons input.find' => 'color: {{VALUE}};']]);
        $this->add_control('buttonfind_bgcolor', ['label' => esc_html__('Background Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .dce-search-buttons input.find' => 'background-color: {{VALUE}};']]);
        $this->add_control('buttonfind_border_color', ['label' => esc_html__('Border color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .dce-search-buttons input.find' => 'border-color: {{VALUE}};']]);
        $this->add_control('buttonfind_txcolor_hover', ['label' => esc_html__('Hover Text Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .dce-search-buttons input.find:hover' => 'color: {{VALUE}};']]);
        $this->add_control('buttonfind_bgcolor_hover', ['label' => esc_html__('Hover Background Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .dce-search-buttons input.find:hover' => 'background-color: {{VALUE}};']]);
        $this->add_control('buttonfind_border_color_hover', ['label' => esc_html__('Hover Border color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .dce-search-buttons input.reset:hover' => 'border-color: {{VALUE}};']]);
        $this->end_controls_section();
        $this->start_controls_section('section_style_files_disabled', ['label' => esc_html__('Files - Disabled State', 'dynamic-content-for-elementor'), 'tab' => Controls_Manager::TAB_STYLE, 'condition' => ['private_access' => 'yes']]);
        $this->add_control('heading_files_disabled', ['label' => esc_html__('Disabled State', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HEADING, 'separator' => 'before']);
        $this->add_control('filename_color_disabled', ['label' => esc_html__('Name Color (Disabled)', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .dce-file-download--disabled .dce-file-title' => 'color: {{VALUE}};']]);
        $this->add_control('filename_disabled_opacity', ['label' => esc_html__('Disabled Opacity', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'range' => ['' => ['min' => 0.1, 'max' => 1, 'step' => 0.1]], 'default' => ['size' => 0.6], 'selectors' => ['{{WRAPPER}} .dce-file-download--disabled' => 'opacity: {{SIZE}};']]);
        $this->add_control('filename_disabled_cursor', ['label' => esc_html__('Disable Pointer Cursor', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => 'yes', 'selectors' => ['{{WRAPPER}} .dce-file-download--disabled' => 'cursor: default;']]);
        $this->end_controls_section();
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
        $is_editor = \Elementor\Plugin::$instance->editor->is_edit_mode();
        $this->is_authorized = $this->current_user_can_download($settings);
        $baseDir = \false;
        $files = $dirs = array();
        switch ($settings['path_selection']) {
            case 'custom':
                $baseDir = $settings['folder_custom'];
                $tmpTit = \explode('/', $baseDir);
                $baseTitle = \end($tmpTit);
                break;
            case 'media':
                $baseTitle = \false;
                $baseDir = 'wp-content/uploads';
                $medias = $settings['medias'];
                $src_identifier = 'http';
                $tmp = \explode($src_identifier, $medias);
                foreach ($tmp as $fkey => $afile) {
                    if ($fkey) {
                        list($furl, $other) = \explode('"', $afile, 2);
                        $resized = Helper::is_resized_image($furl);
                        if ($resized) {
                            $furl = $resized;
                        }
                        list($other, $fpath) = \explode($baseDir, $furl, 2);
                        $files[] = \substr($fpath, 1);
                    }
                }
                $files = \array_filter($files, static function ($file) {
                    return '' !== $file;
                });
                $files = \array_unique($files);
                if (empty($files)) {
                    $baseDir = \false;
                }
                break;
            case 'taxonomy':
                $baseTitle = \false;
                $baseDir = 'wp-content/uploads';
                if ($settings['taxonomy']) {
                    $term_id = \intval($settings['terms_' . $settings['taxonomy']]);
                    if ($term_id) {
                        $taxonomy = get_taxonomy($settings['taxonomy']);
                        if ($taxonomy) {
                            $baseTitle = $taxonomy->label;
                            $term = get_term($term_id, $settings['taxonomy']);
                            if ($term && !is_wp_error($term)) {
                                $baseTitle = $term->name;
                            }
                        }
                        $medias = Helper::get_term_posts($term_id, 'attachment');
                        if (!empty($medias)) {
                            foreach ($medias as $amedia) {
                                list($other, $fpath) = \explode($baseDir, $amedia->guid, 2);
                                $files[] = \substr($fpath, 1);
                            }
                        }
                    }
                }
                // TODO - subfolder
                $files = \array_filter($files, static function ($file) {
                    return '' !== $file;
                });
                if (empty($files)) {
                    $baseDir = \false;
                }
                break;
            case 'post':
                $baseTitle = wp_kses_post(get_the_title());
                $baseDir = 'wp-content/uploads';
                $medias = get_attached_media('', get_the_ID());
                if (!empty($medias)) {
                    foreach ($medias as $amedia) {
                        list($other, $fpath) = \explode($baseDir, $amedia->guid, 2);
                        $files[] = \substr($fpath, 1);
                    }
                }
                $files = \array_filter($files, static function ($file) {
                    return '' !== $file;
                });
                if (empty($files)) {
                    $baseDir = \false;
                }
        }
        if ($baseDir) {
            if (\is_dir(self::get_root_directory($baseDir))) {
                if (isset($settings['enable_metadata']) && $settings['enable_metadata']) {
                    $this->file_metadata = get_option('dce-file-browser', array());
                }
                if (isset($settings['search']) && $settings['search']) {
                    $this->render_search($settings);
                }
                // Browser title
                if (!empty($settings['title']) && $baseTitle) {
                    $title_size = Helper::validate_html_tag($settings['title_size']);
                    $this->add_render_attribute('browser_title', 'class', 'dce-filebrowser-title');
                    echo '<' . esc_attr($title_size) . ' ' . $this->get_render_attribute_string('browser_title') . '>' . esc_html($baseTitle) . '</' . esc_attr($title_size) . '>';
                }
                // Root list attributes
                $this->add_render_attribute('root_list', 'class', ['list-unstyled', 'dce-list', 'dce-list-root']);
                // Backward compatibility: preserve data-hide-reverse attribute if previously configured
                if (!empty($settings['enable_metadata']) && !empty($settings['enable_metadata_hide']) && !empty($settings['enable_metadata_hide_reverse'])) {
                    $this->add_render_attribute('root_list', 'data-hide-reverse', '1');
                }
                echo '<ul ' . $this->get_render_attribute_string('root_list') . '>';
                $this->render_directory(self::get_root_directory($baseDir), $files, $dirs);
                echo '</ul>';
            } elseif (\Elementor\Plugin::$instance->editor->is_edit_mode()) {
                Helper::notice(\false, esc_html__('Root folder not found', 'dynamic-content-for-elementor'));
            }
        } elseif (\Elementor\Plugin::$instance->editor->is_edit_mode()) {
            Helper::notice(\false, esc_html__('Select root folder or files', 'dynamic-content-for-elementor'));
        }
    }
    /**
     *
     * @param string|false|null $folder
     * @return string
     */
    public static function get_root_directory($folder = \false)
    {
        if (!$folder || $folder === \DIRECTORY_SEPARATOR) {
            return ABSPATH;
        }
        return ABSPATH . $folder;
    }
    /**
     * @param array<mixed> $settings
     * @return boolean
     */
    protected function current_user_can_download($settings)
    {
        if (empty($settings['private_access'])) {
            return \true;
        }
        if (empty($settings['user_role']) || !\is_array($settings['user_role'])) {
            return \false;
        }
        if (is_user_logged_in()) {
            $current_user = wp_get_current_user();
            return !empty(\array_intersect($current_user->roles, $settings['user_role']));
        } elseif (\in_array('visitor', $settings['user_role'], \true)) {
            return \true;
        }
        return \false;
    }
    /**
     * Render directory tree as HTML recursively
     *
     * Converts a directory structure into nested HTML list items,
     * handling both directories and files with their metadata.
     * Outputs HTML directly via echo.
     *
     * @param string $dir Base directory path to render
     * @param mixed $files Optional array of specific files to display, or mixed value from scandir
     * @param mixed $dirs Optional array of specific directories to display, or mixed value from scandir
     * @return void
     */
    protected function render_directory($dir, $files = array(), $dirs = array())
    {
        $image_exts = array('jpg', 'jpeg', 'jpe', 'gif', 'png');
        $settings = $this->get_settings_for_display();
        $force_icon_only = !$this->is_authorized && !\Elementor\Plugin::$instance->editor->is_edit_mode();
        if (isset($settings['file_type']) && $settings['file_type']) {
            $file_type = \strtolower($settings['file_type']);
            $file_type = \str_replace(array('.', ','), ' ', $file_type);
            $extensions = \explode(' ', $file_type);
            $extensions = \array_filter($extensions);
        }
        if (!empty($dirs) && \is_array($dirs)) {
            $cdir = $dirs;
        }
        if (!empty($files) && \is_array($files)) {
            $cdir = $files;
        }
        if (empty($cdir)) {
            $cdir = \scandir($dir, isset($settings['order']) ? $settings['order'] : null);
        }
        $rootDir = \realpath(self::get_root_directory(null));
        foreach ($cdir as $key => $value) {
            if (!\is_array($value) && \substr($value, 0, 1) == '.') {
                // hidden file
                continue;
            }
            $title = \false;
            if (\is_array($value)) {
                $fulldir = $dir . \DIRECTORY_SEPARATOR . $key;
            } elseif (\substr($dir, -1, 1) == '/') {
                $fulldir = $dir . $value;
            } else {
                $fulldir = $dir . \DIRECTORY_SEPARATOR . $value;
            }
            $fulldir = \realpath($fulldir);
            if ($fulldir === \false || !\is_string($rootDir)) {
                continue;
            }
            if (\strpos($fulldir, $rootDir) !== 0) {
                continue;
            }
            $rdir = \str_replace($rootDir, '', $fulldir);
            if (\is_array($value) || \is_dir($fulldir)) {
                $kdir = sanitize_file_name($rdir);
                // Use short hash for HTML IDs to avoid exposing directory structure
                $kdir_id = 'dce-dir-' . \substr(\md5($rdir), 0, 8);
                // Backward compatibility: read hide metadata if previously configured
                $hide = \false;
                if (!empty($settings['enable_metadata']) && !empty($settings['enable_metadata_hide'])) {
                    $hide = $this->get_dir_meta($kdir, 'hidden');
                }
                // Backward compatibility: support reverse hide logic if previously configured
                if (!empty($settings['enable_metadata']) && !empty($settings['enable_metadata_hide']) && !empty($settings['enable_metadata_hide_reverse'])) {
                    $hide = !$hide;
                }
                if (!Helper::is_empty_dir($fulldir) || $settings['empty']) {
                    if (!$hide || \Elementor\Plugin::$instance->editor->is_edit_mode()) {
                        // Get folder title: custom title or folder name
                        $folder_name = \is_array($value) ? $key : $value;
                        $custom_title = $this->get_dir_meta($kdir, 'title');
                        $title = $custom_title ? $custom_title : $folder_name;
                        // Directory attributes
                        $dir_key = 'dir_' . $kdir_id;
                        $this->add_render_attribute($dir_key . '_li', 'class', 'dir');
                        $this->add_render_attribute($dir_key . '_link', ['class' => ['block', 'folder-dir'], 'data-toggle' => 'collapse', 'data-dir' => $kdir, 'data-original-name' => $folder_name, 'id' => $kdir_id, 'data-target' => '#' . $kdir_id . '-ul', 'href' => '#' . $kdir_id]);
                        $this->add_render_attribute($dir_key . '_icon', 'class', ['middle', 'fiv-viv', 'fiv-icon-folder']);
                        $this->add_render_attribute($dir_key . '_title', 'class', 'dce-dir-title');
                        $this->add_render_attribute($dir_key . '_ul', ['class' => ['dce-hidden', 'collapse', 'list-unstyled', 'dce-list'], 'id' => $kdir_id . '-ul']);
                        ?>
						<li <?php 
                        echo $this->get_render_attribute_string($dir_key . '_li');
                        ?>>
							<a <?php 
                        echo $this->get_render_attribute_string($dir_key . '_link');
                        ?>>
								<span <?php 
                        echo $this->get_render_attribute_string($dir_key . '_icon');
                        ?>></span>
								<strong <?php 
                        echo $this->get_render_attribute_string($dir_key . '_title');
                        ?>><?php 
                        echo esc_html($title);
                        ?></strong>
							</a>
							<ul <?php 
                        echo $this->get_render_attribute_string($dir_key . '_ul');
                        ?>>
								<?php 
                        $this->render_directory($fulldir, $files, $value);
                        ?>
							</ul>
						</li>
							<?php 
                    }
                }
            } else {
                $filename = \basename($value);
                $parts = \explode('.', $filename);
                if (\count($parts) > 1) {
                    $ext = sanitize_html_class(\strtolower(\end($parts)));
                } else {
                    $ext = 'blank';
                }
                // Filter files by extension if configured
                if (!empty($extensions)) {
                    $is_whitelisting = !empty($settings['file_type_show']);
                    $is_in_list = \in_array($ext, $extensions, \true);
                    // Skip if whitelisting and not in list, or blacklisting and in list
                    if ($is_whitelisting && !$is_in_list) {
                        continue;
                    }
                    if (!$is_whitelisting && $is_in_list) {
                        continue;
                    }
                }
                // Handle resized images
                if (\in_array($ext, $image_exts, \true)) {
                    $original_image = Helper::is_resized_image($value);
                    if ($original_image) {
                        $is_media_library = $settings['path_selection'] === 'media';
                        // For media library: use original image instead of resized
                        if ($is_media_library) {
                            $value = $original_image;
                            $fulldir = $dir . \DIRECTORY_SEPARATOR . $value;
                            $rdir = \str_replace(self::get_root_directory(null), '', $fulldir);
                        } elseif (empty($settings['resized'])) {
                            continue;
                        }
                    }
                }
                $md5 = \md5($fulldir);
                // Always try to get post_id for Media Library files
                $post_id = Helper::get_image_id($rdir);
                // Backward compatibility: read hide metadata if previously configured
                $hide = \false;
                if (!empty($settings['enable_metadata']) && !empty($settings['enable_metadata_hide'])) {
                    $hide = $this->get_file_meta($post_id ? $post_id : $md5, 'hidden');
                }
                // Backward compatibility: support reverse hide logic if previously configured
                if (!empty($settings['enable_metadata']) && !empty($settings['enable_metadata_hide']) && !empty($settings['enable_metadata_hide_reverse'])) {
                    $hide = !$hide;
                }
                if (\Elementor\Plugin::$instance->editor->is_edit_mode() || !$hide) {
                    if (!\file_exists(DCE_PATH . '/assets/node/file-icon-vectors/icons/vivid/' . $ext . '.svg')) {
                        $ext = 'blank';
                    }
                    // Prepare unique key for this file
                    $file_key = 'file_' . $md5;
                    // File list item attributes
                    $this->add_render_attribute($file_key . '_li', 'class', ['file', 'ext-' . $ext]);
                    $direct_link = $this->path_to_url($fulldir);
                    $is_editor = \Elementor\Plugin::$instance->editor->is_edit_mode();
                    $link_enabled = $this->is_authorized || $is_editor;
                    // File download link/wrapper attributes
                    $this->add_render_attribute($file_key . '_link', ['class' => ['block', 'btn-block', 'dce-file-download']]);
                    if ($link_enabled) {
                        $this->add_render_attribute($file_key . '_link', ['href' => $direct_link, 'target' => '_blank']);
                    } else {
                        $this->add_render_attribute($file_key . '_link', 'class', 'dce-file-download--disabled');
                    }
                    // Add data attributes for authorized users or in editor mode
                    if ($this->is_authorized || $is_editor) {
                        $this->add_render_attribute($file_key . '_link', 'data-md5', $md5);
                        $this->add_render_attribute($file_key . '_link', 'data-original-name', $filename);
                        if ($post_id) {
                            $this->add_render_attribute($file_key . '_link', 'data-post-id', $post_id);
                        }
                    }
                    echo '<li ' . $this->get_render_attribute_string($file_key . '_li') . '>';
                    $tag_name = $link_enabled ? 'a' : 'div';
                    echo '<' . $tag_name . ' ' . $this->get_render_attribute_string($file_key . '_link') . '>';
                    // File icon
                    $show_image_preview = $this->is_authorized && !$force_icon_only && !empty($settings['img_icon']) && \in_array($ext, $image_exts, \true) && $post_id;
                    if ($show_image_preview) {
                        echo wp_get_attachment_image($post_id, 'thumbnail', \true, array('class' => 'middle dce-img-icon'));
                    } else {
                        $this->add_render_attribute($file_key . '_icon', 'class', ['middle', 'fiv-viv', 'fiv-icon-' . $ext]);
                        echo '<span ' . $this->get_render_attribute_string($file_key . '_icon') . '></span>';
                    }
                    // Prepare title
                    if (!$settings['extension'] && $ext != 'blank') {
                        $value = \substr($value, 0, -(\strlen($ext) + 1));
                    }
                    // Get file title - priority: custom title > WP title (if enabled) > filename
                    $custom_title = $this->get_file_meta($md5, 'title');
                    $use_wp_title = !empty($settings['enable_metadata_wp_title']);
                    if ($custom_title) {
                        // Custom title set by user
                        $title = esc_html($custom_title);
                    } elseif ($post_id && $use_wp_title) {
                        // Media Library file: use WP title (if enabled)
                        $title = esc_html(get_the_title($post_id));
                    } else {
                        // Use filename
                        $title = esc_html(\basename($value));
                    }
                    // File text wrapper
                    $this->add_render_attribute($file_key . '_text', 'class', 'dce-file-text');
                    $this->add_render_attribute($file_key . '_title', 'class', 'dce-file-title');
                    echo '<span ' . $this->get_render_attribute_string($file_key . '_text') . '>';
                    echo '<strong ' . $this->get_render_attribute_string($file_key . '_title') . '>' . $title . '</strong>';
                    if (!empty($settings['enable_metadata']) && $settings['enable_metadata_size']) {
                        $this->add_render_attribute($file_key . '_size', 'class', ['label', 'label-default', 'dce-file-size-label']);
                        echo ' <small ' . $this->get_render_attribute_string($file_key . '_size') . '>(' . size_format(\filesize($fulldir), 0) . ')</small>';
                    }
                    if (!empty($settings['enable_metadata']) && $settings['enable_metadata_hits']) {
                        $this->add_render_attribute($file_key . '_hits', 'class', ['label', 'label-default', 'dce-file-hits-label']);
                        $this->add_render_attribute($file_key . '_hits_icon', ['class' => ['fa', 'fa-download'], 'aria-hidden' => 'true']);
                        $hits_count = absint($this->get_file_meta($post_id ? $post_id : $md5, 'hits', 0));
                        echo ' <small ' . $this->get_render_attribute_string($file_key . '_hits') . '>';
                        echo '<i ' . $this->get_render_attribute_string($file_key . '_hits_icon') . '></i> ';
                        echo '<b>' . $hits_count . '</b>';
                        echo '</small>';
                    }
                    echo '</span>';
                    echo '</' . $tag_name . '>';
                    // File description (WP Caption)
                    if (!empty($settings['enable_metadata']) && !empty($settings['enable_metadata_wp_description']) && $post_id) {
                        $description = wp_get_attachment_caption($post_id);
                        if (\is_string($description) && \trim($description)) {
                            $this->add_render_attribute($file_key . '_description', 'class', ['dce-file-description', 'block']);
                            echo '<div ' . $this->get_render_attribute_string($file_key . '_description') . '>' . wp_kses_post($description) . '</div>';
                        }
                    }
                    echo '</li>';
                }
            }
        }
    }
    protected function get_file_meta($file_id, $meta = '', $fallback = '')
    {
        if (\is_numeric($file_id)) {
            $ret = get_post_meta(absint($file_id), 'dce-file', \true);
        } else {
            $ret = get_option('dce-file-' . $file_id);
        }
        if ($ret) {
            if (isset($ret[$meta])) {
                return $ret[$meta];
            }
        }
        if (isset($this->file_metadata[$file_id])) {
            return $this->file_metadata[$file_id][$meta];
        }
        return $fallback;
    }
    /**
     * Path to URL
     *
     * @param string $dir
     * @return string
     */
    protected function path_to_url($dir)
    {
        $dir = wp_normalize_path($dir);
        $upload_dirs = wp_upload_dir();
        $basedir = wp_normalize_path($upload_dirs['basedir']);
        $baseurl = $upload_dirs['baseurl'];
        $abspath = wp_normalize_path(ABSPATH);
        $site_url = site_url();
        if (\strpos($dir, $basedir) === 0) {
            $relative_path = \ltrim(\substr($dir, \strlen($basedir)), '/');
            $url = trailingslashit($baseurl) . $relative_path;
        } elseif (\strpos($dir, $abspath) === 0) {
            $relative_path = \ltrim(\substr($dir, \strlen($abspath)), '/');
            $url = trailingslashit($site_url) . $relative_path;
        } else {
            $url = esc_url_raw($dir);
        }
        return esc_url($url);
    }
    protected function get_dir_meta($dir_id, $meta = '', $fallback = '')
    {
        $ret = get_option('dce-dir-' . $dir_id);
        if ($ret && isset($ret[$meta])) {
            return $ret[$meta];
        }
        if (isset($this->file_metadata[$dir_id])) {
            return $this->file_metadata[$dir_id][$meta];
        }
        return $fallback;
    }
    /**
     * @param array<mixed> $settings
     * @return void
     */
    protected function render_search($settings)
    {
        $this->add_render_attribute('form', 'class', 'dce-file-search-form');
        $this->add_render_attribute('form', 'action', '');
        // Add data attribute for quick search functionality
        if (!empty($settings['search_quick'])) {
            $this->add_render_attribute('form', 'data-quick-search', 'true');
        }
        ?>
		<form <?php 
        echo $this->get_render_attribute_string('form');
        ?>>
			<?php 
        if (!empty($settings['search_text'])) {
            $search_text_size = Helper::validate_html_tag($settings['search_text_size']);
            $this->add_render_attribute('search_text', 'class', 'dce-file-search-form-title');
            ?>
				<<?php 
            echo esc_attr($search_text_size);
            ?> <?php 
            echo $this->get_render_attribute_string('search_text');
            ?>>
					<?php 
            echo esc_html($settings['search_text']);
            ?>
				</<?php 
            echo esc_attr($search_text_size);
            ?>>
				<?php 
        }
        $this->add_render_attribute('input_container', 'class', 'form-control');
        $this->add_render_attribute('input_text', ['type' => 'text', 'class' => 'filetxt', 'name' => 'filetxt', 'value' => '']);
        ?>
			<div <?php 
        echo $this->get_render_attribute_string('input_container');
        ?>>
				<input <?php 
        echo $this->get_render_attribute_string('input_text');
        ?>>
			</div>
			<?php 
        if (!empty($settings['search_notice'])) {
            $this->add_render_attribute('search_notice', 'class', 'dce-search-desc');
            ?>
				<div <?php 
            echo $this->get_render_attribute_string('search_notice');
            ?>><small>
					<?php 
            echo esc_html($settings['search_notice']);
            ?>
				</small></div>
				<?php 
        }
        if (empty($settings['search_quick'])) {
            $this->add_render_attribute('buttons_container', 'class', 'text-right dce-search-buttons');
            ?>
				<div <?php 
            echo $this->get_render_attribute_string('buttons_container');
            ?>>
					<?php 
            if (!empty($settings['search_reset'])) {
                $this->add_render_attribute('reset_button', ['class' => 'reset', 'type' => 'reset', 'value' => $settings['search_reset_text']]);
                ?>
						<input <?php 
                echo $this->get_render_attribute_string('reset_button');
                ?>>
						<?php 
            }
            $this->add_render_attribute('find_button', ['class' => 'find', 'type' => 'submit', 'value' => $settings['search_find_text']]);
            ?>
					<input <?php 
            echo $this->get_render_attribute_string('find_button');
            ?>>
				</div>
				<?php 
        }
        ?>
		</form>
		<br />
		<?php 
    }
}
