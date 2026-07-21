<?php

// SPDX-FileCopyrightText: 2018-2026 Ovation S.r.l. <help@dynamic.ooo>
// SPDX-License-Identifier: GPL-3.0-or-later
namespace DynamicContentForElementor\Widgets;

use Elementor\Repeater;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Typography;
use DynamicContentForElementor\Helper;
use DynamicContentForElementor\Plugin;
if (!\defined('ABSPATH')) {
    exit;
    // Exit if accessed directly
}
class DynamicGoogleMaps extends \DynamicContentForElementor\Widgets\WidgetPrototype
{
    public function get_script_depends()
    {
        return ['dce-google-maps'];
    }
    public function get_style_depends()
    {
        return ['dce-google-maps'];
    }
    /**
     * Run Once
     *
     * @return void
     */
    public function run_once()
    {
        parent::run_once();
        $save_guard = \DynamicContentForElementor\Plugin::instance()->save_guard;
        $save_guard->register_unsafe_control($this->get_name(), 'infoWindow_query_html');
        $save_guard->register_unsafe_control($this->get_name(), 'style_map');
        $save_guard->register_unsafe_control($this->get_name(), 'other_post_source');
    }
    protected $positions = [];
    /**
     * Register controls after check if this feature is only for admin
     *
     * @return void
     */
    protected function safe_register_controls()
    {
        $taxonomies = Helper::get_taxonomies();
        $this->start_controls_section('section_map', ['label' => $this->get_title()]);
        if (!get_option('dce_google_maps_api')) {
            $this->add_control('api_notice', ['type' => Controls_Manager::RAW_HTML, 'raw' => esc_html__('In order to use this feature you should set Google Maps API, with Geocoding API enabled, on Integrations section', 'dynamic-content-for-elementor'), 'content_classes' => 'elementor-panel-alert elementor-panel-alert-warning']);
        }
        $this->add_control('map_data_type', ['label' => esc_html__('Data Type', 'dynamic-content-for-elementor'), 'type' => 'images_selector', 'toggle' => \false, 'type_selector' => 'icon', 'columns_grid' => 5, 'default' => 'address', 'options' => ['address' => ['title' => esc_html__('Address', 'dynamic-content-for-elementor'), 'return_val' => 'val', 'icon' => 'fa fa-map-marker-alt'], 'latlng' => ['title' => esc_html__('Latitude and longitude', 'dynamic-content-for-elementor'), 'return_val' => 'val', 'icon' => 'fa fa-globe-europe'], 'acfmap' => ['title' => esc_html__('ACF Google Map Field', 'dynamic-content-for-elementor'), 'return_val' => 'val', 'icon' => 'fa fa-map'], 'metabox_google_maps' => ['title' => esc_html__('Meta Box Google Map Field', 'dynamic-content-for-elementor'), 'return_val' => 'val', 'icon' => 'icon-dce-metabox-google-maps']], 'frontend_available' => \true]);
        $this->add_control('address', ['label' => esc_html__('Address', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'default' => 'Venice, Italy', 'label_block' => \true, 'condition' => ['map_data_type' => 'address']]);
        $this->add_control('latitudine', ['label' => esc_html__('Latitude', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'default' => '45.4371908', 'condition' => ['map_data_type' => 'latlng']]);
        $this->add_control('longitudine', ['label' => esc_html__('Longitude', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'default' => '12.3345898', 'condition' => ['map_data_type' => 'latlng']]);
        if (Helper::is_plugin_active('acf')) {
            $this->add_control('acf_mapfield', ['label' => esc_html__('ACF Google Map field', 'dynamic-content-for-elementor'), 'type' => 'ooo_query', 'placeholder' => esc_html__('Select the field...', 'dynamic-content-for-elementor'), 'label_block' => \true, 'dynamic' => ['active' => \false], 'query_type' => 'acf', 'object_type' => 'google_map', 'frontend_available' => \true, 'condition' => ['map_data_type' => 'acfmap']]);
        } else {
            $this->add_control('acf_notice', ['type' => Controls_Manager::RAW_HTML, 'raw' => esc_html__('In order to use this feature you need Advanced Custom Fields.', 'dynamic-content-for-elementor'), 'content_classes' => 'elementor-panel-alert elementor-panel-alert-warning', 'condition' => ['map_data_type' => 'acfmap']]);
        }
        if (Helper::is_plugin_active('metabox')) {
            $this->add_control('metabox_google_maps_field', ['label' => esc_html__('Meta Box Google Maps field', 'dynamic-content-for-elementor'), 'type' => 'ooo_query', 'placeholder' => esc_html__('Select the field...', 'dynamic-content-for-elementor'), 'label_block' => \true, 'dynamic' => ['active' => \false], 'query_type' => 'metabox', 'object_type' => 'map', 'frontend_available' => \true, 'condition' => ['map_data_type' => 'metabox_google_maps']]);
        } else {
            $this->add_control('metabox_notice', ['type' => Controls_Manager::RAW_HTML, 'raw' => esc_html__('In order to use this feature you need Meta Box.', 'dynamic-content-for-elementor'), 'content_classes' => 'elementor-panel-alert elementor-panel-alert-warning', 'condition' => ['map_data_type' => 'metabox_google_maps']]);
        }
        $this->end_controls_section();
        $this->start_controls_section('section_query', ['label' => esc_html__('Multiple Locations Query', 'dynamic-content-for-elementor'), 'condition' => ['map_data_type' => ['acfmap', 'metabox_google_maps']]]);
        $this->add_control('use_query', ['label' => esc_html__('Multiple Locations Query', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'frontend_available' => \true, 'condition' => ['map_data_type' => ['acfmap', 'metabox_google_maps']]]);
        $query_types_for_acf = [];
        if (Helper::is_plugin_active('acf') || Helper::is_plugin_active('acf-pro')) {
            $query_types_for_acf = ['acf_relations' => ['title' => 'ACF Relationship', 'return_val' => 'val', 'icon' => 'fa fa-american-sign-language-interpreting'], 'acf_repeater' => ['title' => 'ACF Repeater', 'return_val' => 'val', 'icon' => 'fa fa-ellipsis-v']];
        }
        $this->add_control('query_type', ['label' => esc_html__('Query Type', 'dynamic-content-for-elementor'), 'type' => 'images_selector', 'toggle' => \false, 'type_selector' => 'icon', 'columns_grid' => 5, 'options' => ['get_cpt' => ['title' => esc_html__('Custom Post Type', 'dynamic-content-for-elementor'), 'return_val' => 'val', 'icon' => 'eicon-post-content'], 'dynamic_mode' => ['title' => esc_html__('Dynamic - Current Query', 'dynamic-content-for-elementor'), 'return_val' => 'val', 'icon' => 'fa fa-cogs'], 'search_filter' => ['title' => 'Search & Filter Pro', 'return_val' => 'val', 'icon' => 'icon-dce-search-filter'], 'specific_posts' => ['title' => esc_html__('From Specific Posts', 'dynamic-content-for-elementor'), 'return_val' => 'val', 'icon' => 'fa fa-list-ul']] + $query_types_for_acf, 'default' => 'get_cpt', 'condition' => ['use_query' => 'yes', 'map_data_type' => ['acfmap', 'metabox_google_maps']]]);
        $this->add_control('ignore_pagination', ['label' => esc_html__('Ignore pagination', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => 'no', 'frontend_available' => \true, 'condition' => ['map_data_type' => ['acfmap', 'metabox_google_maps'], 'use_query' => 'yes', 'query_type' => ['dynamic_mode']]]);
        if (Helper::is_plugin_active('search-filter-pro')) {
            if (\DynamicContentForElementor\Integrations\SearchAndFilterPro::is_version(2)) {
                if (\version_compare(SEARCH_FILTER_VERSION, '2.5.5', '>=')) {
                    $this->add_control('search_filter_id', ['label' => esc_html__('Filter', 'dynamic-content-for-elementor'), 'type' => 'ooo_query', 'label_block' => \true, 'placeholder' => esc_html__('Select the filter', 'dynamic-content-for-elementor'), 'query_type' => 'posts', 'object_type' => 'search-filter-widget', 'condition' => ['query_type' => 'search_filter', 'map_data_type' => ['acfmap', 'metabox_google_maps']]]);
                } else {
                    $this->add_control('search_filter_notice', ['type' => Controls_Manager::RAW_HTML, 'raw' => esc_html__('In order to use this feature you need Search & Filter Pro version >= 2.5.5', 'dynamic-content-for-elementor'), 'content_classes' => 'elementor-panel-alert elementor-panel-alert-warning', 'condition' => ['query_type' => 'search_filter', 'map_data_type' => ['acfmap', 'metabox_google_maps']]]);
                }
            } elseif (\DynamicContentForElementor\Integrations\SearchAndFilterPro::is_version(3.1)) {
                $this->add_control('search_filter_v3_id', ['label' => esc_html__('Query', 'dynamic-content-for-elementor'), 'type' => 'ooo_query', 'label_block' => \true, 'placeholder' => esc_html__('Select the query', 'dynamic-content-for-elementor'), 'query_type' => 'search_and_filter_v3_query_ids', 'dynamic' => ['active' => \false], 'condition' => ['query_type' => 'search_filter', 'map_data_type' => ['acfmap', 'metabox_google_maps']]]);
            }
        } else {
            $this->add_control('search_filter_notice', ['type' => Controls_Manager::RAW_HTML, 'raw' => \sprintf(
                /* translators: %1$s: opening tag for the link, %2$s: closing tag for the link */
                esc_html__('Combine the power of Search & Filter Pro front end filters with Dynamic Google Maps! Note: In order to use this feature you need install Search & Filter Pro. Search & Filter Pro is a premium product - you can %1$sget it here%2$s.', 'dynamic-content-for-elementor'),
                '<a href="https://searchandfilter.com">',
                '</a>'
            ), 'content_classes' => 'elementor-panel-alert elementor-panel-alert-warning', 'condition' => ['query_type' => 'search_filter', 'map_data_type' => ['acfmap', 'metabox_google_maps']]]);
        }
        $this->add_control('post_type', ['label' => esc_html__('Post Type', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT2, 'options' => Helper::get_public_post_types(), 'multiple' => \true, 'label_block' => \true, 'default' => 'post', 'condition' => ['use_query' => 'yes', 'map_data_type' => ['acfmap', 'metabox_google_maps'], 'query_type' => ['get_cpt']]]);
        $this->add_control('taxonomy', ['label' => esc_html__('Taxonomy', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'options' => ['' => esc_html__('None', 'dynamic-content-for-elementor')] + get_taxonomies(['public' => \true]), 'condition' => ['use_query' => 'yes', 'map_data_type' => ['acfmap', 'metabox_google_maps'], 'query_type' => ['get_cpt']]]);
        $this->add_control('prevent_marker_overlapping', ['label' => esc_html__('Prevent Marker Overlapping', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => '', 'frontend_available' => \true, 'description' => esc_html__('Offset markers with the same position to make them all visible. Without this option, overlapping markers would be hidden behind each other', 'dynamic-content-for-elementor'), 'condition' => ['map_data_type' => ['acfmap', 'metabox_google_maps'], 'use_query' => 'yes', 'query_type' => ['get_cpt', 'search_filter', 'dynamic_mode']]]);
        $this->add_control('terms_current_post', ['label' => esc_html__('Dynamic Current Post Terms', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'description' => esc_html__('Filter results by taxonomy terms associated to the current post', 'dynamic-content-for-elementor'), 'condition' => ['taxonomy!' => '', 'query_type' => ['get_cpt', 'dynamic_mode'], 'use_query' => 'yes', 'map_data_type' => ['acfmap', 'metabox_google_maps']]]);
        foreach ($taxonomies as $tkey => $atax) {
            if ($tkey) {
                $this->add_control('terms_' . $tkey, ['label' => esc_html__('Terms', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT2, 'options' => ['' => esc_html__('All', 'dynamic-content-for-elementor')] + \DynamicContentForElementor\Helper::get_taxonomy_terms($tkey), 'description' => esc_html__('Filter results by selected taxonomy terms', 'dynamic-content-for-elementor'), 'multiple' => \true, 'label_block' => \true, 'condition' => ['use_query' => 'yes', 'query_type' => ['get_cpt', 'dynamic_mode'], 'taxonomy' => $tkey, 'terms_current_post' => '', 'map_data_type' => ['acfmap', 'metabox_google_maps']], 'render_type' => 'template']);
            }
        }
        $this->add_control('acf_relationship', ['label' => esc_html__('ACF Relationship field', 'dynamic-content-for-elementor'), 'type' => 'ooo_query', 'placeholder' => esc_html__('Select the field...', 'dynamic-content-for-elementor'), 'label_block' => \true, 'query_type' => 'acf', 'object_type' => 'relationship', 'condition' => ['use_query' => 'yes', 'query_type' => 'acf_relations', 'map_data_type' => ['acfmap', 'metabox_google_maps']]]);
        $this->add_control('acf_repeater', ['label' => esc_html__('ACF Repeater field', 'dynamic-content-for-elementor'), 'type' => 'ooo_query', 'placeholder' => esc_html__('Select the field...', 'dynamic-content-for-elementor'), 'label_block' => \true, 'query_type' => 'acf', 'object_type' => 'repeater', 'condition' => ['use_query' => 'yes', 'query_type' => 'acf_repeater', 'map_data_type' => ['acfmap', 'metabox_google_maps']]]);
        $this->add_control('acf_from', ['label' => esc_html__('Retrieve the field from', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'default' => 'current_post', 'options' => ['current_post' => esc_html__('Current Post', 'dynamic-content-for-elementor'), 'current_user' => esc_html__('Current User', 'dynamic-content-for-elementor'), 'current_author' => esc_html__('Current Author', 'dynamic-content-for-elementor'), 'current_term' => esc_html__('Current Term', 'dynamic-content-for-elementor'), 'options_page' => esc_html__('Options Page', 'dynamic-content-for-elementor')], 'condition' => ['use_query' => 'yes', 'query_type' => ['acf_relations', 'acf_repeater'], 'map_data_type' => ['acfmap', 'metabox_google_maps']]]);
        $this->add_control('specific_pages', ['label' => esc_html__('Posts', 'dynamic-content-for-elementor'), 'type' => 'ooo_query', 'placeholder' => esc_html__('Post Title', 'dynamic-content-for-elementor'), 'query_type' => 'posts', 'multiple' => \true, 'label_block' => \true, 'condition' => ['use_query' => 'yes', 'query_type' => 'specific_posts', 'map_data_type' => ['acfmap', 'metabox_google_maps']]]);
        $this->end_controls_section();
        $this->start_controls_section('section_fallback', ['label' => esc_html__('No Results Behaviour', 'dynamic-content-for-elementor'), 'condition' => ['query_type' => 'search_filter', 'map_data_type' => ['acfmap', 'metabox_google_maps']]]);
        $this->add_control('fallback_type', ['label' => esc_html__('Content type', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CHOOSE, 'options' => ['text' => ['title' => esc_html__('Text', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-left'], 'template' => ['title' => esc_html__('Template', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-th-large']], 'toggle' => \false, 'default' => 'text']);
        $this->add_control('fallback_template', ['label' => esc_html__('Render Template', 'dynamic-content-for-elementor'), 'type' => 'ooo_query', 'placeholder' => esc_html__('Template Name', 'dynamic-content-for-elementor'), 'label_block' => \true, 'query_type' => 'posts', 'object_type' => 'elementor_library', 'condition' => ['fallback_type' => 'template']]);
        $this->add_control('fallback_text', ['label' => esc_html__('Text Fallback', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::WYSIWYG, 'default' => esc_html__('No positions found.', 'dynamic-content-for-elementor'), 'condition' => ['fallback_type' => 'text']]);
        $this->end_controls_section();
        $this->start_controls_section('section_maps_controlling', ['label' => esc_html__('Controlling', 'dynamic-content-for-elementor')]);
        $this->add_control('geolocation', ['label' => esc_html__('Geolocation', 'dynamic-content-for-elementor'), 'description' => esc_html__('Display the geographic location of the user on the map, using browser\'s HTML5 Geolocation', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => 'no', 'frontend_available' => \true]);
        $this->add_control('geolocation_button_text', ['label' => esc_html__('Text for Geolocation button', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'default' => esc_html__('Pan to Current Location', 'dynamic-content-for-elementor'), 'label_block' => 'true', 'frontend_available' => \true, 'condition' => ['geolocation' => 'yes']]);
        $this->add_control('geolocation_change_zoom', ['label' => esc_html__('Change Zoom after Geolocation', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'frontend_available' => \true, 'condition' => ['geolocation' => 'yes']]);
        $this->add_control('geolocation_zoom', ['label' => esc_html__('Zoom Level after Geolocation', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => 10], 'range' => ['px' => ['min' => 1, 'max' => 20]], 'frontend_available' => \true, 'condition' => ['geolocation' => 'yes', 'geolocation_change_zoom' => 'yes']]);
        $this->add_control('zoom_heading', ['label' => esc_html__('Zoom', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HEADING, 'separator' => 'before']);
        $this->add_control('auto_zoom', ['label' => esc_html__('Force automatic Zoom', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => 'yes', 'frontend_available' => \true, 'separator' => 'before', 'condition' => ['map_data_type' => ['acfmap', 'metabox_google_maps'], 'acf_mapfield!' => ['', null]]]);
        $this->add_control('zoom', ['label' => esc_html__('Zoom Level', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => 10], 'range' => ['px' => ['min' => 1, 'max' => 20]], 'conditions' => ['relation' => 'or', 'terms' => [['terms' => [['name' => 'map_data_type', 'operator' => 'in', 'value' => ['latlng', 'address']]]], ['terms' => [['name' => 'map_data_type', 'operator' => 'in', 'value' => ['acfmap', 'metabox_google_maps']], ['name' => 'auto_zoom', 'operator' => '==', 'value' => '']]]]]]);
        $this->add_control('zoom_custom', ['label' => esc_html__('Set a minimum and maximum zoom level', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'frontend_available' => \true]);
        $this->add_control('zoom_minimum', ['label' => esc_html__('Zoom Minimum', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'frontend_available' => \true, 'default' => ['size' => 0], 'range' => ['px' => ['min' => 1, 'max' => 20]], 'condition' => ['zoom_custom!' => '']]);
        $this->add_control('zoom_maximum', ['label' => esc_html__('Zoom Maximum', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'frontend_available' => \true, 'default' => ['size' => 20], 'range' => ['px' => ['min' => 1, 'max' => 20]], 'condition' => ['zoom_custom!' => '']]);
        $this->add_control('prevent_scroll', ['label' => esc_html__('Scroll', 'dynamic-content-for-elementor'), 'description' => esc_html__('When a user scrolls a page that contains a map, the scrolling action can unintentionally cause the map to zoom. This behavior can be controlled using this option.', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => 'yes', 'render_type' => 'template', 'separator' => 'before', 'frontend_available' => \true]);
        $this->end_controls_section();
        $this->start_controls_section('section_mapStyles', ['label' => esc_html__('Map Type', 'dynamic-content-for-elementor')]);
        $this->add_control('new_api', ['label' => esc_html__('Uses new Google Maps API', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => 'yes', 'frontend_available' => \true]);
        $this->add_control('notice_new_google_api', ['content' => esc_html__('Enabling the new Google Maps API activates the Vector Map Experience (MapID). In this mode, custom styles via Snazzy Maps or JSON are not supported anymore. To apply custom map styles, you must configure them directly from Google Cloud Console using Map Styling.', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::NOTICE, 'notice_type' => 'warning', 'condition' => ['new_api' => 'yes']]);
        $this->add_control('map_type', ['label' => esc_html__('Map Type', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'default' => 'roadmap', 'options' => ['roadmap' => esc_html__('Roadmap', 'dynamic-content-for-elementor'), 'satellite' => esc_html__('Satellite', 'dynamic-content-for-elementor'), 'hybrid' => esc_html__('Hybrid', 'dynamic-content-for-elementor'), 'terrain' => esc_html__('Terrain', 'dynamic-content-for-elementor')], 'frontend_available' => \true]);
        $this->add_control('style_select', ['label' => esc_html__('Style', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'options' => ['' => esc_html__('None', 'dynamic-content-for-elementor'), 'custom' => esc_html__('Custom', 'dynamic-content-for-elementor'), 'prestyle' => esc_html__('Snazzy Style', 'dynamic-content-for-elementor')], 'frontend_available' => \true, 'condition' => ['map_type' => 'roadmap']]);
        $this->add_control('dce_url', ['label' => 'Snazzy Maps', 'type' => Controls_Manager::HIDDEN, 'default' => DCE_URL, 'frontend_available' => \true, 'condition' => ['map_type' => 'roadmap', 'style_select' => 'prestyle']]);
        $this->add_control('snazzy_select', ['label' => 'Snazzy Maps', 'type' => Controls_Manager::SELECT2, 'options' => $this->get_snazzy_maps_list(), 'frontend_available' => \true, 'default' => 'red', 'condition' => ['map_type' => 'roadmap', 'style_select' => 'prestyle']]);
        $this->add_control('style_map', ['label' => esc_html__('Snazzy JSON Style Map', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXTAREA, 'description' => \sprintf(
            /* translators: %1$s: opening tag for the link, %2$s: closing tag for the link */
            esc_html__('To better manage the graphic styles of the map go to: %1$ssnazzymaps.com%2$s', 'dynamic-content-for-elementor'),
            '<a href="https://snazzymaps.com/" target="_blank">',
            '</a>'
        ), 'frontend_available' => \true, 'condition' => ['map_type' => 'roadmap', 'style_select' => 'custom']]);
        $this->add_control('notice_style_deprecated', ['content' => esc_html__('Due to changes in the Google Maps APIs, the style is no longer supported. Google no longer allows the insertion of styles for security reasons, and we are awaiting further information from them to provide a solution.', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::NOTICE, 'notice_type' => 'danger', 'condition' => ['style_select' => ['prestyle', 'custom'], 'new_api!' => '']]);
        $this->end_controls_section();
        $this->start_controls_section('section_mapInfoWIndow', ['label' => esc_html__('InfoWindow', 'dynamic-content-for-elementor')]);
        $this->add_control('enable_infoWindow', ['label' => esc_html__('InfoWindow', 'dynamic-content-for-elementor'), 'description' => esc_html__('The InfoWindow displays content in a popup window above the location', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => 'yes', 'separator' => 'before', 'render_type' => 'template', 'frontend_available' => \true]);
        $this->add_control('infoWindow_click_outside', ['label' => esc_html__('Close InfoWindow on mouse click outside of the map', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'frontend_available' => \true, 'condition' => ['enable_infoWindow' => 'yes']]);
        $this->add_control('infoWindow_one_at_the_time', ['label' => esc_html__('Open one InfoWindow at a time', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'frontend_available' => \true, 'condition' => ['enable_infoWindow' => 'yes']]);
        $this->add_control('infoWindow_click_to_post', ['label' => esc_html__('Link to post', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'frontend_available' => \true, 'condition' => ['map_data_type' => ['acfmap', 'metabox_google_maps'], 'acf_mapfield!' => ['', null], 'use_query!' => '', 'enable_infoWindow' => 'yes']]);
        $this->add_control('infoWindow_click_to_url', ['label' => esc_html__('Link to URL', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'frontend_available' => \true, 'condition' => ['map_data_type!' => ['acfmap', 'metabox_google_maps'], 'enable_infoWindow' => 'yes']]);
        $this->add_control('infoWindow_url', ['label' => esc_html__('URL', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::URL, 'frontend_available' => \true, 'condition' => ['map_data_type!' => ['acfmap', 'metabox_google_maps'], 'enable_infoWindow' => 'yes', 'infoWindow_click_to_url!' => '']]);
        $this->add_control('custom_infoWindow_render', ['label' => esc_html__('Render', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CHOOSE, 'options' => ['simple' => ['title' => esc_html__('Simple mode', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-left'], 'html' => ['title' => esc_html__('Dynamic HTML', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-code'], 'template' => ['title' => esc_html__('Template', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-th-large']], 'toggle' => \false, 'default' => 'simple', 'condition' => ['infoWindow_click_to_post' => '', 'enable_infoWindow' => 'yes']]);
        $simple_render_condition_use_query = [['name' => 'custom_infoWindow_render', 'value' => 'simple'], ['name' => 'enable_infoWindow', 'value' => 'yes'], ['name' => 'infoWindow_click_to_post', 'value' => ''], ['name' => 'use_query', 'value' => 'yes'], ['name' => 'map_data_type', 'operator' => 'in', 'value' => ['acfmap', 'metabox_google_maps']]];
        $this->add_control('infoWindow_heading_style_image', ['label' => esc_html__('Image', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HEADING, 'conditions' => ['terms' => $simple_render_condition_use_query]]);
        $this->add_control('infowindow_query_show_image', ['label' => esc_html__('Image', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'label_on' => esc_html__('Show', 'dynamic-content-for-elementor'), 'label_off' => esc_html__('Hide', 'dynamic-content-for-elementor'), 'default' => 'yes', 'frontend_available' => \true, 'conditions' => ['terms' => $simple_render_condition_use_query]]);
        $this->add_control('acf_repeater_iwimage', ['label' => esc_html__('ACF Image Field', 'dynamic-content-for-elementor'), 'type' => 'ooo_query', 'placeholder' => esc_html__('Select the field...', 'dynamic-content-for-elementor'), 'label_block' => \true, 'query_type' => 'acf', 'object_type' => 'image', 'condition' => ['use_query' => 'yes', 'query_type' => 'acf_repeater', 'enable_infoWindow!' => '', 'custom_infoWindow_render' => 'simple', 'map_data_type' => 'acfmap', 'enable_infoWindow' => 'yes', 'infowindow_query_show_image!' => '']]);
        $this->add_control('infowindow_query_extendimage', ['label' => esc_html__('Background Mode', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'frontend_available' => \true, 'conditions' => ['terms' => \array_merge($simple_render_condition_use_query, [['name' => 'infowindow_query_show_image', 'operator' => '!=', 'value' => '']])]]);
        $this->add_responsive_control('infowindow_query_bgimage_height', ['label' => esc_html__('Height', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['unit' => 'px', 'size' => 100], 'sizes_unit' => ['px', '%'], 'range' => ['px' => ['min' => 10, 'max' => 360], '%' => ['min' => 1, 'max' => 100]], 'selectors' => ['{{WRAPPER}} .gm-style .gm-style-iw-c .dce-iw-image-bg' => 'height: {{SIZE}}{{UNIT}};'], 'conditions' => ['terms' => \array_merge($simple_render_condition_use_query, [['name' => 'infowindow_query_extendimage', 'operator' => '!=', 'value' => '']])]]);
        $this->add_control('infoWindow_query_image_float', ['label' => esc_html__('Float', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'conditions' => ['terms' => \array_merge($simple_render_condition_use_query, [['name' => 'infowindow_query_show_image', 'operator' => '!=', 'value' => '']])], 'selectors' => ['{{WRAPPER}} .gm-style .gm-style-iw-c .dce-iw-image, {{WRAPPER}} .gm-style .gm-style-iw-c .dce-iw-textzone' => 'float: left;']]);
        $this->add_responsive_control('infowindow_query_image_size', ['label' => esc_html__('Distribution Size (%)', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => 50], 'size_units' => ['%'], 'range' => ['%' => ['min' => 10, 'max' => 100]], 'selectors' => ['{{WRAPPER}} .gm-style .gm-style-iw-c .dce-iw-image' => 'width: {{SIZE}}%;', '{{WRAPPER}} .gm-style .gm-style-iw-c .dce-iw-textzone' => 'width: calc( 100% - {{SIZE}}% );'], 'conditions' => ['terms' => \array_merge($simple_render_condition_use_query, [['name' => 'infowindow_query_show_image', 'operator' => '!=', 'value' => ''], ['name' => 'infoWindow_query_image_float', 'operator' => '!=', 'value' => '']])]]);
        $this->add_control('infoWindow_heading_style_title', ['label' => esc_html__('Title', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HEADING, 'separator' => 'before', 'conditions' => ['terms' => $simple_render_condition_use_query]]);
        $this->add_control('infowindow_query_show_title', ['label' => esc_html__('Title', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'label_on' => esc_html__('Show', 'dynamic-content-for-elementor'), 'label_off' => esc_html__('Hide', 'dynamic-content-for-elementor'), 'default' => 'yes', 'frontend_available' => \true, 'conditions' => ['terms' => $simple_render_condition_use_query]]);
        $this->add_control('acf_repeater_iwtitle', ['label' => esc_html__('ACF Field for Title', 'dynamic-content-for-elementor'), 'type' => 'ooo_query', 'placeholder' => esc_html__('Select the field...', 'dynamic-content-for-elementor'), 'label_block' => \true, 'query_type' => 'acf', 'object_type' => 'text', 'condition' => ['use_query' => 'yes', 'query_type' => 'acf_repeater', 'enable_infoWindow!' => '', 'custom_infoWindow_render' => 'simple', 'map_data_type' => 'acfmap', 'infowindow_query_show_title!' => '']]);
        $this->add_group_control(Group_Control_Typography::get_type(), ['name' => 'infowindow_query_typography_title', 'label' => esc_html__('Typography', 'dynamic-content-for-elementor'), 'selector' => '{{WRAPPER}} .gm-style .gm-style-iw-c .dce-iw-title', 'separator' => 'before', 'conditions' => ['terms' => \array_merge($simple_render_condition_use_query, [['name' => 'infowindow_query_show_title', 'operator' => '!=', 'value' => '']])]]);
        $this->add_control('infowindow_query_color_title', ['label' => esc_html__('Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .gm-style .gm-style-iw-c .dce-iw-title' => 'color: {{VALUE}};'], 'conditions' => ['terms' => \array_merge($simple_render_condition_use_query, [['name' => 'infowindow_query_show_title', 'operator' => '!=', 'value' => '']])]]);
        $this->add_control('infowindow_query_bgcolor_title', ['label' => esc_html__('Background Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .gm-style .gm-style-iw-c .dce-iw-title' => 'background-color: {{VALUE}};'], 'conditions' => ['terms' => \array_merge($simple_render_condition_use_query, [['name' => 'infowindow_query_show_title', 'operator' => '!=', 'value' => '']])]]);
        $this->add_control('infowindow_query_padding_title', ['label' => esc_html__('Padding', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => ['px', 'em'], 'selectors' => ['{{WRAPPER}} .gm-style .gm-style-iw-c .dce-iw-title' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'], 'conditions' => ['terms' => \array_merge($simple_render_condition_use_query, [['name' => 'infowindow_query_show_title', 'operator' => '!=', 'value' => '']])]]);
        // --------- CONTENT
        $this->add_control('infoWindow_heading_style_content', ['label' => esc_html__('Content', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HEADING, 'separator' => 'before', 'conditions' => ['terms' => $simple_render_condition_use_query]]);
        $this->add_control('infowindow_query_show_content', ['label' => esc_html__('Content', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'label_on' => esc_html__('Show', 'dynamic-content-for-elementor'), 'label_off' => esc_html__('Hide', 'dynamic-content-for-elementor'), 'default' => 'yes', 'frontend_available' => \true, 'conditions' => ['terms' => $simple_render_condition_use_query]]);
        $this->add_control('acf_repeater_iwcontent', ['label' => esc_html__('ACF Field for Content', 'dynamic-content-for-elementor'), 'type' => 'ooo_query', 'placeholder' => esc_html__('Select the field...', 'dynamic-content-for-elementor'), 'label_block' => \true, 'query_type' => 'acf', 'object_type' => ['text', 'textarea', 'wysiwyg'], 'condition' => ['use_query' => 'yes', 'query_type' => 'acf_repeater', 'enable_infoWindow!' => '', 'custom_infoWindow_render' => 'simple', 'map_data_type' => 'acfmap', 'infowindow_query_show_content!' => '']]);
        $this->add_group_control(Group_Control_Typography::get_type(), ['name' => 'infowindow_query_typography_content', 'label' => esc_html__('Typography', 'dynamic-content-for-elementor'), 'selector' => '{{WRAPPER}} .gm-style .gm-style-iw-c .dce-iw-content', 'separator' => 'before', 'conditions' => ['terms' => \array_merge($simple_render_condition_use_query, [['name' => 'infowindow_query_show_content', 'operator' => '!=', 'value' => '']])]]);
        $this->add_control('infowindow_query_color_content', ['label' => esc_html__('Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .gm-style .gm-style-iw-c .dce-iw-content' => 'color: {{VALUE}};'], 'conditions' => ['terms' => \array_merge($simple_render_condition_use_query, [['name' => 'infowindow_query_show_content', 'operator' => '!=', 'value' => '']])]]);
        $this->add_control('infowindow_query_padding_content', ['label' => esc_html__('Padding', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => ['px', 'em'], 'selectors' => ['{{WRAPPER}} .gm-style .gm-style-iw-c .dce-iw-content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'], 'conditions' => ['terms' => \array_merge($simple_render_condition_use_query, [['name' => 'infowindow_query_show_content', 'operator' => '!=', 'value' => '']])]]);
        // --------- READMORE
        $this->add_control('infoWindow_heading_style_readmore', ['label' => esc_html__('Read More', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HEADING, 'separator' => 'before', 'conditions' => ['terms' => $simple_render_condition_use_query]]);
        $this->add_control('infowindow_query_show_readmore', ['label' => esc_html__('Read More', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'label_on' => esc_html__('Show', 'dynamic-content-for-elementor'), 'label_off' => esc_html__('Hide', 'dynamic-content-for-elementor'), 'default' => 'yes', 'frontend_available' => \true, 'conditions' => ['terms' => $simple_render_condition_use_query]]);
        $this->add_control('acf_repeater_iwlink', ['label' => esc_html__('ACF URL Field for Read More Link', 'dynamic-content-for-elementor'), 'type' => 'ooo_query', 'placeholder' => esc_html__('Select the field...', 'dynamic-content-for-elementor'), 'label_block' => \true, 'query_type' => 'acf', 'object_type' => ['url', 'link', 'page_link'], 'condition' => ['use_query' => 'yes', 'query_type' => 'acf_repeater', 'enable_infoWindow!' => '', 'custom_infoWindow_render' => 'simple', 'map_data_type' => 'acfmap', 'infowindow_query_show_readmore!' => '']]);
        $this->add_control('infowindow_query_readmore_text', ['label' => esc_html__('Text button', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'default' => esc_html__('Read More', 'dynamic-content-for-elementor'), 'conditions' => ['terms' => \array_merge($simple_render_condition_use_query, [['name' => 'infowindow_query_show_readmore', 'operator' => '!=', 'value' => '']])]]);
        $this->start_controls_tabs('readmore_colors');
        $this->start_controls_tab('infowindow_query_readmore_colors_normal', ['label' => esc_html__('Normal', 'dynamic-content-for-elementor'), 'conditions' => ['terms' => \array_merge($simple_render_condition_use_query, [['name' => 'infowindow_query_show_readmore', 'operator' => '!=', 'value' => '']])]]);
        $this->add_control('infowindow_query_readmore_color', ['label' => esc_html__('Text Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .gm-style .gm-style-iw-c .dce-iw-readmore-btn' => 'color: {{VALUE}};'], 'conditions' => ['terms' => \array_merge($simple_render_condition_use_query, [['name' => 'infowindow_query_show_readmore', 'operator' => '!=', 'value' => '']])]]);
        $this->add_control('infowindow_query_readmore_bgcolor', ['label' => esc_html__('Background Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .gm-style .gm-style-iw-c .dce-iw-readmore-btn' => 'background-color: {{VALUE}};'], 'conditions' => ['terms' => \array_merge($simple_render_condition_use_query, [['name' => 'infowindow_query_show_readmore', 'operator' => '!=', 'value' => '']])]]);
        $this->add_group_control(Group_Control_Border::get_type(), ['name' => 'infowindow_query_readmore_border', 'label' => esc_html__('Border', 'dynamic-content-for-elementor'), 'selector' => '{{WRAPPER}} .gm-style .gm-style-iw-c .dce-iw-readmore-btn', 'conditions' => ['terms' => \array_merge($simple_render_condition_use_query, [['name' => 'infowindow_query_show_readmore', 'operator' => '!=', 'value' => '']])]]);
        $this->end_controls_tab();
        $this->start_controls_tab('infowindow_query_readmore_colors_hover', ['label' => esc_html__('Hover', 'dynamic-content-for-elementor'), 'conditions' => ['terms' => \array_merge($simple_render_condition_use_query, [['name' => 'infowindow_query_show_readmore', 'operator' => '!=', 'value' => '']])]]);
        $this->add_control('infowindow_query_readmore_color_hover', ['label' => esc_html__('Text Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .gm-style .gm-style-iw-c .dce-iw-readmore-btn:hover' => 'color: {{VALUE}};'], 'conditions' => ['terms' => \array_merge($simple_render_condition_use_query, [['name' => 'infowindow_query_show_readmore', 'operator' => '!=', 'value' => '']])]]);
        $this->add_control('infowindow_query_readmore_bgcolor_hover', ['label' => esc_html__('Background Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .gm-style .gm-style-iw-c .dce-iw-readmore-btn:hover' => 'background-color: {{VALUE}};'], 'conditions' => ['terms' => \array_merge($simple_render_condition_use_query, [['name' => 'infowindow_query_show_readmore', 'operator' => '!=', 'value' => '']])]]);
        $this->add_control('infowindow_query_readmore_hover_border_color', ['label' => esc_html__('Border Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'conditions' => ['terms' => \array_merge($simple_render_condition_use_query, [['name' => 'infowindow_query_show_readmore', 'operator' => '!=', 'value' => ''], ['name' => 'infowindow_query_readmore_border_border', 'operator' => '!=', 'value' => '']])], 'selectors' => ['{{WRAPPER}} .gm-style .gm-style-iw-c .dce-iw-readmore-btn:hover' => 'border-color: {{VALUE}};']]);
        $this->end_controls_tab();
        $this->end_controls_tabs();
        $this->add_group_control(Group_Control_Typography::get_type(), ['name' => 'infowindow_query_typography_readmore', 'label' => esc_html__('Typography', 'dynamic-content-for-elementor'), 'selector' => '{{WRAPPER}} .dce-iw-readmore-btn', 'conditions' => ['terms' => \array_merge($simple_render_condition_use_query, [['name' => 'infowindow_query_show_readmore', 'operator' => '!=', 'value' => '']])]]);
        $this->add_responsive_control('infowindow_query_readmore_align', ['label' => esc_html__('Alignment', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CHOOSE, 'toggle' => \true, 'options' => ['left' => ['title' => esc_html__('Left', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-left'], 'center' => ['title' => esc_html__('Center', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-center'], 'right' => ['title' => esc_html__('Right', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-right']], 'default' => 'left', 'selectors' => ['{{WRAPPER}} .gm-style .gm-style-iw-c .dce-iw-readmore-wrapper' => 'text-align: {{VALUE}};'], 'conditions' => ['terms' => \array_merge($simple_render_condition_use_query, [['name' => 'infowindow_query_show_readmore', 'operator' => '!=', 'value' => '']])]]);
        $this->add_responsive_control('infowindow_query_readmore_padding', ['label' => esc_html__('Padding', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => ['px', '%'], 'selectors' => ['{{WRAPPER}} .gm-style .gm-style-iw-c .dce-iw-readmore-btn' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'], 'conditions' => ['terms' => \array_merge($simple_render_condition_use_query, [['name' => 'infowindow_query_show_readmore', 'operator' => '!=', 'value' => '']])]]);
        $this->add_responsive_control('infowindow_query_readmore_margin', ['label' => esc_html__('Margin', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => ['px', '%'], 'selectors' => ['{{WRAPPER}} .gm-style .gm-style-iw-c .dce-iw-readmore-btn' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'], 'conditions' => ['terms' => \array_merge($simple_render_condition_use_query, [['name' => 'infowindow_query_show_readmore', 'operator' => '!=', 'value' => '']])]]);
        $this->add_control('infowindow_query_readmore_border_radius', ['label' => esc_html__('Border Radius', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => ['px', '%'], 'selectors' => ['{{WRAPPER}} .gm-style .gm-style-iw-c .dce-iw-readmore-btn' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'], 'conditions' => ['terms' => \array_merge($simple_render_condition_use_query, [['name' => 'infowindow_query_show_readmore', 'operator' => '!=', 'value' => '']])]]);
        $this->add_group_control(Group_Control_Box_Shadow::get_type(), ['name' => 'infowindow_query_box_shadow_readmore', 'label' => esc_html__('Box Shadow', 'dynamic-content-for-elementor'), 'selector' => '{{WRAPPER}} .gm-style .gm-style-iw-c .dce-iw-readmore-btn', 'conditions' => ['terms' => \array_merge($simple_render_condition_use_query, [['name' => 'infowindow_query_show_readmore', 'operator' => '!=', 'value' => '']])]]);
        $this->add_control('infoWindow_template', ['label' => esc_html__('Template', 'dynamic-content-for-elementor'), 'type' => 'ooo_query', 'placeholder' => esc_html__('Template Name', 'dynamic-content-for-elementor'), 'label_block' => \true, 'query_type' => 'posts', 'object_type' => 'elementor_library', 'condition' => ['infoWindow_click_to_post' => '', 'custom_infoWindow_render' => 'template', 'enable_infoWindow' => 'yes']]);
        $this->add_control('infoWindow_loading_text', ['label' => esc_html__('Loading Text', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'default' => esc_html__('Loading...', 'dynamic-content-for-elementor'), 'frontend_available' => \true, 'condition' => ['infoWindow_click_to_post' => '', 'custom_infoWindow_render' => 'template', 'enable_infoWindow' => 'yes', 'infoWindow_template!' => '', 'use_query!' => '']]);
        Plugin::instance()->text_templates->maybe_add_notice($this, 'infoWindow', ['infoWindow_click_to_post' => '', 'custom_infoWindow_render' => 'html', 'enable_infoWindow' => 'yes']);
        $read_more = esc_html__('Read more', 'dynamic-content-for-elementor');
        $this->add_control('infoWindow_query_html', ['label' => esc_html__('Dynamic HTML', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CODE, 'default' => \DynamicContentForElementor\Plugin::instance()->text_templates->get_default_value(['dynamic-shortcodes' => <<<EOF
{if:{post:featured-image-id} [{media:image @ID={post:featured-image-id}}]}
<h4>{post:title}</h4>
{post:excerpt}<br />
<a href="{post:permalink}">{$read_more}</a>
EOF
, 'tokens' => '[post:ID|get_the_post_thumbnail(thumbnail)]<h4>[post:title|esc_html]</h4>[post:excerpt]<br><a href="[post:permalink]">' . esc_html__('Read More', 'dynamic-content-for-elementor') . '</a>']), 'description' => \DynamicContentForElementor\Plugin::instance()->text_templates->get_default_value(['dynamic-shortcodes' => esc_html__('You can use Dynamic Shortcodes and HTML.', 'dynamic-content-for-elementor'), 'tokens' => esc_html__('You can use HTML and Tokens.', 'dynamic-content-for-elementor')]), 'ai' => ['active' => \false], 'dynamic' => ['active' => \false], 'condition' => ['infoWindow_click_to_post' => '', 'custom_infoWindow_render' => 'html', 'enable_infoWindow' => 'yes']]);
        $this->add_control('custom_infoWindow_wysiwig', ['label' => esc_html__('Custom Text', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::WYSIWYG, 'frontend_available' => \true, 'label_block' => \true, 'conditions' => ['relation' => 'or', 'terms' => [['relation' => 'and', 'terms' => [['name' => 'custom_infoWindow_render', 'value' => 'simple'], ['name' => 'enable_infoWindow', 'value' => 'yes'], ['name' => 'map_data_type', 'operator' => 'in', 'value' => ['address', 'latlng']]]], ['relation' => 'and', 'terms' => [['name' => 'custom_infoWindow_render', 'value' => 'simple'], ['name' => 'enable_infoWindow', 'value' => 'yes'], ['name' => 'use_query', 'value' => ''], ['name' => 'map_data_type', 'operator' => 'in', 'value' => ['acfmap', 'metabox_google_maps']]]]]]]);
        $this->end_controls_section();
        $this->start_controls_section('section_mapMarker', ['label' => esc_html__('Marker', 'dynamic-content-for-elementor')]);
        if (Helper::is_plugin_active('acf') || Helper::is_plugin_active('acf-pro')) {
            $this->add_control('acf_markerfield', ['label' => esc_html__('Marker from an ACF Image field', 'dynamic-content-for-elementor'), 'type' => 'ooo_query', 'placeholder' => esc_html__('Select the field...', 'dynamic-content-for-elementor'), 'label_block' => \true, 'query_type' => 'acf', 'object_type' => 'image']);
        }
        $this->add_control('imageMarker', ['label' => esc_html__('Marker Image', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::MEDIA, 'default' => ['url' => ''], 'frontend_available' => \true, 'condition' => ['acf_markerfield' => ['', null]]]);
        $this->add_control('marker_width', ['label' => esc_html__('Force Width', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::NUMBER, 'default' => 20, 'frontend_available' => \true]);
        $this->add_control('marker_height', ['label' => esc_html__('Force Height', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::NUMBER, 'default' => 20, 'frontend_available' => \true]);
        $this->end_controls_section();
        $this->start_controls_section('section_mapControls', ['label' => esc_html__('Controls', 'dynamic-content-for-elementor')]);
        $this->add_control('maptypecontrol', ['label' => esc_html__('Map Type Control', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => 'yes', 'frontend_available' => \true]);
        $this->add_control('pancontrol', ['label' => esc_html__('Pan Control', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => 'yes', 'frontend_available' => \true]);
        $this->add_control('rotatecontrol', ['label' => esc_html__('Rotate Control', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => 'yes', 'frontend_available' => \true]);
        $this->add_control('scalecontrol', ['label' => esc_html__('Scale Control', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => 'yes', 'frontend_available' => \true]);
        $this->add_control('streetviewcontrol', ['label' => esc_html__('Street View Control', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => 'yes', 'frontend_available' => \true]);
        $this->add_control('zoomcontrol', ['label' => esc_html__('Zoom Control', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => 'yes', 'frontend_available' => \true]);
        $this->add_control('fullscreenControl', ['label' => esc_html__('Full Screen Control', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => 'yes', 'frontend_available' => \true]);
        $this->add_control('markerclustererControl', ['label' => esc_html__('Marker Clusterer', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => 'yes', 'frontend_available' => \true]);
        $this->end_controls_section();
        $this->start_controls_section('section_source', ['label' => esc_html__('Source', 'dynamic-content-for-elementor')]);
        $this->add_control('data_source', ['label' => esc_html__('Source', 'dynamic-content-for-elementor'), 'description' => esc_html__('Select the data source', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => 'yes', 'label_on' => esc_html__('Same', 'dynamic-content-for-elementor'), 'label_off' => esc_html__('Other', 'dynamic-content-for-elementor'), 'return_value' => 'yes']);
        $this->add_control('other_post_source', ['label' => esc_html__('Select from other source post', 'dynamic-content-for-elementor'), 'type' => 'ooo_query', 'placeholder' => esc_html__('Post Title', 'dynamic-content-for-elementor'), 'label_block' => \true, 'query_type' => 'posts', 'condition' => ['data_source' => '']]);
        $this->end_controls_section();
        $this->start_controls_section('section_style', ['label' => esc_html__('Map', 'dynamic-content-for-elementor'), 'tab' => Controls_Manager::TAB_STYLE]);
        $this->add_responsive_control('height', ['label' => esc_html__('Height', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['unit' => 'px', 'size' => 300], 'range' => ['px' => ['min' => 40, 'max' => 1440]], 'selectors' => ['{{WRAPPER}} .map' => 'height: {{SIZE}}{{UNIT}};']]);
        $this->end_controls_section();
        $this->start_controls_section('section_infowindow_style', ['label' => esc_html__('InfoWindow', 'dynamic-content-for-elementor'), 'tab' => Controls_Manager::TAB_STYLE]);
        $this->add_responsive_control('infoWindow_align', ['label' => esc_html__('Alignment', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CHOOSE, 'options' => ['left' => ['title' => esc_html__('Left', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-left'], 'center' => ['title' => esc_html__('Center', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-center'], 'right' => ['title' => esc_html__('Right', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-right'], 'justify' => ['title' => esc_html__('Justified', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-justify']], 'prefix_class' => 'align-dce-', 'selectors' => ['{{WRAPPER}} .gm-style .gm-style-iw-c' => 'text-align: {{VALUE}};'], 'condition' => ['infoWindow_click_to_post' => '']]);
        $this->add_group_control(Group_Control_Typography::get_type(), ['name' => 'infowindow_typography', 'label' => esc_html__('Typography', 'dynamic-content-for-elementor'), 'selector' => '{{WRAPPER}} .gm-style .gm-style-iw-c', 'condition' => ['infoWindow_click_to_post' => '', 'use_query' => '']]);
        $this->add_control('infoWindow_textColor', ['label' => esc_html__('Text Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .gm-style .gm-style-iw-c, {{WRAPPER}} .gm-style .gm-style-iw-t::after' => 'color: {{VALUE}};'], 'condition' => ['infoWindow_click_to_post' => '', 'use_query' => '']]);
        // --------- PANEL
        $this->add_control('infoWindow_heading_style_panel', ['label' => esc_html__('Panel', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HEADING, 'separator' => 'before', 'condition' => ['infoWindow_click_to_post' => '']]);
        $this->add_responsive_control('infoWindow_panel_maxwidth', ['label' => esc_html__('Max Width', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'size_units' => ['px'], 'default' => ['unit' => 'px', 'size' => ''], 'range' => ['px' => ['min' => 40, 'max' => 1440]], 'condition' => ['infoWindow_click_to_post' => ''], 'frontend_available' => \true]);
        $this->add_control('infoWindow_bgColor', ['label' => esc_html__('Background Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .gm-style .gm-style-iw-c, {{WRAPPER}} .gm-style .gm-style-iw-t::after' => 'background: {{VALUE}} !important;'], 'condition' => ['infoWindow_click_to_post' => '']]);
        $this->add_group_control(Group_Control_Border::get_type(), ['name' => 'infoWindow_border', 'label' => esc_html__('Border', 'dynamic-content-for-elementor'), 'selector' => '{{WRAPPER}} .gm-style .gm-style-iw-c, {{WRAPPER}} .gm-style .gm-style-iw-t::after', 'condition' => ['infoWindow_click_to_post' => '']]);
        $this->add_control('infoWindow_padding', ['label' => esc_html__('Padding panel', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => ['px', 'em'], 'default' => ['unit' => 'px'], 'selectors' => ['{{WRAPPER}} .gm-style .gm-style-iw-c' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'], 'condition' => ['infoWindow_click_to_post' => '']]);
        $this->add_control('infoWindow_padding_wrap', ['label' => esc_html__('Padding wrapper', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => ['px', 'em'], 'default' => ['unit' => 'px'], 'selectors' => ['{{WRAPPER}} .gm-style .gm-style-iw-d' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'], 'condition' => ['infoWindow_click_to_post' => '']]);
        $this->add_control('infoWindow_border_radius', ['label' => esc_html__('Border Radius', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => ['px', '%'], 'selectors' => ['{{WRAPPER}} .gm-style .gm-style-iw-c' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'], 'condition' => ['infoWindow_click_to_post' => '']]);
        $this->add_group_control(Group_Control_Box_Shadow::get_type(), ['name' => 'infoWindow_box_shadow', 'selector' => '{{WRAPPER}} .gm-style .gm-style-iw-c', 'condition' => ['infoWindow_click_to_post' => '']]);
        $this->end_controls_section();
    }
    protected function safe_render()
    {
        $settings = $this->get_settings_for_display();
        if (empty($settings)) {
            return;
        }
        if (\Elementor\Plugin::$instance->editor->is_edit_mode()) {
            if (!empty($settings['new_api']) && isset($settings['style_select']) && \in_array($settings['style_select'], ['prestyle', 'custom'], \true)) {
                Helper::notice(\false, esc_html__('Due to changes in the Google Maps APIs, the style is no longer supported. Google no longer allows the insertion of styles for security reasons, and we are awaiting further information from them to provide a solution.', 'dynamic-content-for-elementor'), 'danger');
            }
            if ('acfmap' === $settings['map_data_type'] && empty($settings['acf_mapfield'])) {
                Helper::notice(\false, esc_html__('Select an ACF Google Map Field', 'dynamic-content-for-elementor'));
                return;
            }
            if ('metabox_google_maps' === $settings['map_data_type'] && empty($settings['metabox_google_maps_field'])) {
                Helper::notice(\false, esc_html__('Select a Meta Box Google Maps Field', 'dynamic-content-for-elementor'));
                return;
            }
        }
        // Don't render if ACF is selected but ACF is not active
        if ('acfmap' === $settings['map_data_type'] && !(Helper::is_plugin_active('acf') || Helper::is_plugin_active('acf-pro'))) {
            return;
        }
        // Don't render if Meta Box Google Maps is selected but Meta Box is not active
        if ('metabox_google_maps' === $settings['map_data_type'] && !Helper::is_plugin_active('metabox')) {
            return;
        }
        $id_page = Helper::get_the_id($settings['other_post_source'] ?? \false);
        $is_repeater = \false;
        // Zoom
        $zoom = 10;
        if (isset($settings['zoom'])) {
            $zoom = $settings['zoom']['size'];
        }
        $this->add_render_attribute('map', 'data-zoom', $zoom);
        if (empty($settings['use_query'])) {
            // Static Address
            if ('address' === $settings['map_data_type']) {
                $map_data_type = $settings['map_data_type'];
                $address = $settings['address'];
                $lat = $settings['latitudine'];
                $lng = $settings['longitudine'];
                $this->add_position('single-position', $address, $lat, $lng, null);
            } elseif ('latlng' === $settings['map_data_type']) {
                $map_data_type = $settings['map_data_type'];
                $lat = $settings['latitudine'];
                $lng = $settings['longitudine'];
                $this->add_position('single-position', null, $lat, $lng, null);
            } elseif ('acfmap' === $settings['map_data_type']) {
                if (!empty($settings['acf_mapfield'])) {
                    // Single Address from ACF
                    $location = \get_field($settings['acf_mapfield'], $id_page);
                    if (!$location) {
                        $location = get_sub_field($settings['acf_mapfield']);
                    }
                    if (!empty($location)) {
                        $address = $location['address'];
                        $lat = $location['lat'];
                        $lng = $location['lng'];
                        $link = null;
                        if ($this->should_create_link('single-position')) {
                            $link = $settings['infoWindow_url'] ?? null;
                        }
                        $this->add_position('single-position', $address, $lat, $lng, $link);
                    }
                }
            } elseif ('metabox_google_maps' === $settings['map_data_type']) {
                if (!empty($settings['metabox_google_maps_field'])) {
                    // Single Address from Meta Box
                    $location = rwmb_get_value($settings['metabox_google_maps_field'], [], $id_page);
                    if (!empty($location)) {
                        $lat = $location['latitude'];
                        $lng = $location['longitude'];
                        $link = null;
                        if ($this->should_create_link('single-position')) {
                            $link = $settings['infoWindow_url'] ?? null;
                        }
                        $this->add_position('single-position', null, $lat, $lng, $link);
                    }
                }
            }
        } else {
            // Query
            if ('acf_repeater' === $settings['query_type']) {
                $id_page = Helper::get_acf_source_id($settings['acf_from'], $settings['other_post_source'] ?? \false);
                if (have_rows($settings['acf_repeater'], $id_page)) {
                    while (have_rows($settings['acf_repeater'], $id_page)) {
                        the_row();
                        $fieldsMap = get_sub_field($settings['acf_mapfield']);
                        if (!isset($fieldsMap['lat'], $fieldsMap['lng']) || !\is_numeric($fieldsMap['lat']) || !\is_numeric($fieldsMap['lng'])) {
                            continue;
                        }
                        $lat = $fieldsMap['lat'];
                        $lng = $fieldsMap['lng'];
                        $address = $fieldsMap['address'];
                        $link = $this->should_create_link('repeater-item') ? get_sub_field($settings['acf_repeater_iwlink']) : null;
                        $this->add_position('repeater-item', $address, \strval($lat), \strval($lng), $link);
                    }
                }
            } elseif ('specific_posts' === $settings['query_type']) {
                $args = ['post_type' => 'any', 'post__in' => $settings['specific_pages'], 'post_status' => 'publish', 'order_by' => 'post__in', 'posts_per_page' => -1];
            } elseif ('dynamic_mode' === $settings['query_type']) {
                global $wp_query;
                $args = $wp_query->query_vars;
                if ($settings['ignore_pagination']) {
                    $args['nopaging'] = \true;
                }
            } elseif ('search_filter' === $settings['query_type']) {
                if (Helper::is_plugin_active('search-filter-pro')) {
                    $sfid = null;
                    if (\DynamicContentForElementor\Integrations\SearchAndFilterPro::is_version(2) && \version_compare(SEARCH_FILTER_VERSION, '2.5.5', '>=')) {
                        $sfid = \intval($settings['search_filter_id']);
                        $args = ['search_filter_id' => $sfid];
                    } elseif (\DynamicContentForElementor\Integrations\SearchAndFilterPro::is_version(3.1)) {
                        $sfid = \intval($settings['search_filter_v3_id']);
                        $args = ['search_filter_query_id' => $sfid, 'integration' => 'dynamicooo/dynamic-content-for-elementor', 'paged' => $this->get_current_page()];
                    }
                    if (empty($sfid)) {
                        return;
                    }
                }
            } elseif ('acf_relations' === $settings['query_type']) {
                $id_page = Helper::get_acf_source_id($settings['acf_from'], $settings['other_post_source'] ?? \false);
                $relations = \get_field($settings['acf_relationship'], $id_page, \false);
                if (!$relations) {
                    $relations = get_sub_field($settings['acf_relationship_field'], \false);
                }
                if (!empty($relations)) {
                    $args = ['post_type' => 'any', 'posts_per_page' => -1, 'post__in' => $relations, 'post_status' => 'publish', 'orderby' => 'menu_order'];
                } else {
                    $this->render_fallback();
                    return;
                }
            } elseif ($settings['query_type'] == 'get_cpt') {
                $terms_query = 'all';
                $taxquery = array();
                if ($settings['terms_current_post']) {
                    if (is_single()) {
                        $terms_list = wp_get_post_terms($id_page, $settings['taxonomy'], ['orderby' => 'name', 'order' => 'ASC', 'fields' => 'all', 'hide_empty' => \true]);
                        if (!empty($terms_list)) {
                            $terms_query = array();
                            foreach ($terms_list as $akey => $aterm) {
                                if (!\in_array($aterm->term_id, $terms_query)) {
                                    $terms_query[] = $aterm->term_id;
                                }
                            }
                        }
                    }
                    if (is_archive() && is_tax()) {
                        $queried_object = get_queried_object();
                        $terms_query = array($queried_object->term_id);
                    }
                }
                if (isset($settings['terms_' . $settings['taxonomy']]) && !empty($settings['terms_' . $settings['taxonomy']])) {
                    $terms_query = $settings['terms_' . $settings['taxonomy']];
                    // add current post terms id
                    $dce_key = \array_search('dce_current_post_terms', $terms_query);
                    if ($dce_key !== \false) {
                        unset($terms_query[$dce_key]);
                        $terms_list = wp_get_post_terms($id_page, $settings['taxonomy'], array('orderby' => 'name', 'order' => 'ASC', 'fields' => 'all', 'hide_empty' => \true));
                        if (!empty($terms_list)) {
                            $terms_query = array();
                            foreach ($terms_list as $akey => $aterm) {
                                if (!\in_array($aterm->term_id, $terms_query)) {
                                    $terms_query[] = $aterm->term_id;
                                }
                            }
                        }
                    }
                }
                if ($settings['taxonomy'] != '') {
                    $taxquery[] = ['taxonomy' => $settings['taxonomy'], 'field' => 'id', 'terms' => $terms_query];
                }
                $args = ['post_type' => Helper::validate_post_types($settings['post_type']), 'posts_per_page' => -1, 'post_status' => 'publish', 'tax_query' => $taxquery];
            }
            if ('acf_repeater' !== $settings['query_type']) {
                // Query
                if (!empty($args)) {
                    $p_query = new \WP_Query($args);
                    if ($p_query->have_posts()) {
                        while ($p_query->have_posts()) {
                            $p_query->the_post();
                            $id_page = get_the_ID();
                            $map_field = Helper::get_acf_field_value($settings['acf_mapfield'], $id_page);
                            if (!\is_array($map_field) || empty($map_field) || !\is_numeric($map_field['lat']) || !\is_numeric($map_field['lng'])) {
                                continue;
                            }
                            $address = $map_field['address'];
                            $lat = $map_field['lat'];
                            $lng = $map_field['lng'];
                            $postlink = $this->should_create_link('single-post') ? get_the_permalink($id_page) : null;
                            $postlink = apply_filters('dynamicooo/google-maps/post-link', $postlink, $id_page);
                            $this->add_position('single-post', $address, \strval($lat), \strval($lng), $postlink);
                        }
                        wp_reset_postdata();
                    }
                }
            }
        }
        Plugin::instance()->integrations->search_and_filter_pro->maybe_add_search_filter_class($this, ['class_prefix_v3' => 'search-filter-dynamic-google-maps-results-']);
        if (!empty($this->get_positions())) {
            $this->render_map();
        } else {
            $this->render_fallback();
        }
    }
    /**
     * Render the map
     *
     * @return void
     */
    protected function render_map()
    {
        $this->add_render_attribute('map', ['class' => 'map', 'style' => 'width: 100%;']);
        $encoded_positions = wp_json_encode($this->get_positions());
        if (\false !== $encoded_positions) {
            $this->add_render_attribute('map', 'data-positions', \htmlspecialchars($encoded_positions, \ENT_QUOTES, 'UTF-8'));
        }
        echo '<div ' . $this->get_render_attribute_string('map') . '></div>';
    }
    /**
     * Get the current page for pagination.
     *
     * If pagination or infinite scroll is disabled, return 1.
     * Otherwise, return the maximum of the current paged query variable,
     * the current page query variable, or 1.
     *
     * @return int The current page number.
     */
    public function get_current_page()
    {
        return \max(1, get_query_var('paged'), get_query_var('page'));
    }
    /**
     * Get Positions
     *
     * @return array<int,array<string,mixed>>
     */
    protected function get_positions()
    {
        return $this->positions;
    }
    /**
     * Add a position to positions list
     *
     * @param string $type
     * @param ?string $address
     * @param ?string $lat
     * @param ?string $lng
     * @param ?string $link
     * @return void
     */
    protected function add_position($type, $address, $lat, $lng, $link)
    {
        $settings = $this->get_settings_for_display();
        if (!$address && (!$lat || !$lng)) {
            return;
        }
        $marker_image_id = $this->get_marker_image($type);
        $custom_marker_image = wp_attachment_is_image($marker_image_id) ? wp_get_attachment_url($marker_image_id) : null;
        $position = ['address' => sanitize_text_field($address ?? ''), 'lat' => \floatval($lat), 'lng' => \floatval($lng), 'custom_marker_image' => $custom_marker_image, 'link' => $link ? esc_url($link) : null];
        if (!empty($settings['enable_infoWindow'])) {
            $position['infowindow'] = $this->create_info_window($type);
        }
        $this->positions[] = $position;
    }
    /**
     * @param string $type
     * @return boolean
     */
    protected function should_create_link(string $type)
    {
        if (!$type) {
            return \false;
        }
        $settings = $this->get_settings_for_display();
        switch ($type) {
            case 'single-post':
            case 'repeater-item':
                return !empty($settings['infoWindow_click_to_post']);
            case 'single-position':
                return !empty($settings['infoWindow_click_to_url']);
        }
        return \false;
    }
    /**
     * @param string $type
     * @return ?string
     */
    protected function get_marker_image(string $type)
    {
        $settings = $this->get_settings_for_display();
        if ((Helper::is_plugin_active('acf') || Helper::is_plugin_active('acf-pro')) && !empty($settings['acf_markerfield'])) {
            switch ($type) {
                case 'single-post':
                case 'single-position':
                    $field = \get_field($settings['acf_markerfield'], \false, \false);
                    if (\is_array($field) && isset($field['id'])) {
                        return $field['id'];
                    } else {
                        return $field;
                    }
                    break;
                case 'repeater-item':
                    return get_sub_field($settings['acf_markerfield'], \false);
            }
        }
        if (!empty($settings['imageMarker'])) {
            return $settings['imageMarker']['id'] ?? '';
        }
        return null;
    }
    /**
     * @param string $type
     * @return array<string,string>
     */
    protected function create_info_window($type)
    {
        $infoWindow = ['type' => 'text', 'content' => ''];
        $settings = $this->get_settings_for_display();
        switch ($settings['custom_infoWindow_render']) {
            case 'simple':
                if (\in_array($settings['map_data_type'], ['address', 'latlng'], \true)) {
                    // Custom Text - used only on single marker
                    $infoWindow['content'] = '<div class="dce-iw-textzone">' . $settings['custom_infoWindow_wysiwig'] . '</div>';
                } else {
                    $this->handle_simple_render($infoWindow, $type);
                }
                break;
            case 'html':
                // Dynamic HTML
                $infoWindow['content'] = '<div class="dce-iw-textzone">';
                $infoWindow['content'] .= Plugin::instance()->text_templates->expand_shortcodes_or_callback($settings['infoWindow_query_html'], [], function ($str) {
                    return Helper::get_dynamic_value($str);
                });
                $infoWindow['content'] .= '</div>';
                $infoWindow['content'] = \preg_replace("/\r|\n/", '', $infoWindow['content']);
                break;
            case 'template':
                if ('repeater-item' === $type) {
                    // Render template server-side while inside the ACF repeater
                    // loop, so that get_sub_field() and dynamic shortcodes work.
                    $template_system = Plugin::instance()->template_system;
                    $content = $template_system->build_elementor_template_special(['id' => \intval($settings['infoWindow_template'])]);
                    $infoWindow = ['type' => 'text', 'content' => '<div class="dce-iw-textzone">' . \preg_replace("/\r|\n/", '', $content) . '</div>'];
                } else {
                    // AJAX loading for single-post and other types.
                    $infoWindow = ['type' => 'template', 'template-id' => \intval($settings['infoWindow_template'])];
                    if ('single-post' === $type) {
                        $infoWindow['post-id'] = get_the_ID();
                    }
                }
                break;
        }
        return $infoWindow;
    }
    /**
     * @param array<string,mixed> $infoWindow
     * @param string $type
     * @return void
     */
    protected function handle_simple_render(&$infoWindow, $type)
    {
        $settings = $this->get_settings_for_display();
        switch ($type) {
            case 'repeater-item':
                $fields = [
                    'title' => get_sub_field($settings['acf_repeater_iwtitle']),
                    'image' => get_sub_field($settings['acf_repeater_iwimage']),
                    'image_url' => get_sub_field($settings['acf_repeater_iwimage']),
                    // same as image
                    'content' => get_sub_field($settings['acf_repeater_iwcontent']),
                    'url' => get_sub_field($settings['acf_repeater_iwlink']),
                ];
                break;
            default:
                $fields = ['title' => get_the_title(), 'image' => get_the_post_thumbnail(), 'image_url' => get_the_post_thumbnail_url(), 'content' => get_the_content(), 'url' => get_the_permalink()];
        }
        $infoWindow['type'] = 'text';
        $infoWindow['content'] = '';
        if ($settings['infowindow_query_show_image'] && !empty($fields['image'])) {
            $image_area = '';
            $this->set_render_attribute('image-container', 'class', 'dce-iw-image');
            // Image as Background
            if ($settings['infowindow_query_extendimage']) {
                $this->add_render_attribute('image-container', 'class', 'dce-iw-image-bg');
                $this->set_render_attribute('image-container', 'style', 'background: url(' . esc_url($fields['image_url']) . ') no-repeat center center; background-size: cover;');
            }
            if (!$settings['infowindow_query_extendimage']) {
                if ('single-post' === $type || 'single-position' === $type) {
                    $image_area .= get_the_post_thumbnail();
                } else {
                    $image_area .= '<img src="' . esc_url($fields['image']) . '" alt="">';
                }
            }
            if (!empty($image_area)) {
                $infoWindow['content'] .= '<div ' . $this->get_render_attribute_string('image-container') . '>' . $image_area . '</div>';
            }
        }
        $content_area = '';
        if ($settings['infowindow_query_show_title'] && !empty($fields['title'])) {
            $content_area .= '<div class="dce-iw-title">' . esc_html($fields['title']) . '</div>';
        }
        if ($settings['infowindow_query_show_content'] && !empty($fields['content'])) {
            $content_area .= '<div class="dce-iw-content">' . \preg_replace("/\r|\n/", '', wp_kses_post($fields['content'])) . '</div>';
        }
        if (!empty($settings['infowindow_query_show_readmore']) && !empty($fields['url'])) {
            $content_area .= '<div class="dce-iw-readmore-wrapper"><a href="' . esc_url($fields['url']) . '" class="dce-iw-readmore-btn">' . wp_kses_post($settings['infowindow_query_readmore_text']) . '</a></div>';
        }
        if (!empty($content_area)) {
            $infoWindow['content'] .= '<div class="dce-iw-textzone">';
            $infoWindow['content'] .= $content_area;
            $infoWindow['content'] .= '</div>';
        }
    }
    /**
     * Render the fallback when the map is without results. Useful when Search and Filter Pro is used
     * to return a message instead of a blank map
     *
     * @return void
     */
    protected function render_fallback()
    {
        $settings = $this->get_settings_for_display();
        if (!isset($settings['query_type']) || 'search_filter' !== $settings['query_type']) {
            return;
        }
        $fallback_type = $settings['fallback_type'];
        $fallback_text = wp_kses_post($settings['fallback_text']);
        $fallback_template = $settings['fallback_template'];
        $this->add_render_attribute('container', 'class', 'dce-dynamic-google-maps-fallback');
        ?>

		<div <?php 
        echo $this->get_render_attribute_string('container');
        ?>>
			<?php 
        if (isset($fallback_type) && $fallback_type === 'template') {
            $template_system = \DynamicContentForElementor\Plugin::instance()->template_system;
            echo $template_system->build_elementor_template_special(['id' => $fallback_template]);
        } else {
            $fallback_content = '<p>' . $fallback_text . '</p>';
            echo do_shortcode($fallback_content);
        }
        ?>
		</div>

		<?php 
    }
    /**
     * Get Snazzy Maps List
     *
     * @return array<string,string>
     */
    protected function get_snazzy_maps_list()
    {
        return ['antiqued_gold' => 'Antiqued Gold', 'bates_green' => 'Bates Green', 'baykoclar_red' => 'Baykoclar Red', 'beaglecat_yellow_dark_gray' => 'Beaglecat Yellow Dark Gray', 'black_and_white' => 'Black And White', 'blue_essence' => 'Blue Essence', 'blue_ish' => 'Blue Ish', 'blue_water' => 'Blue Water', 'bluish' => 'Bluish', 'bobbys_world' => 'Bobbys World', 'bright_&_bubbly' => 'Bright & Bubbly', 'bright_dessert' => 'Bright Dessert', 'brownie' => 'Brownie', 'clean_cut' => 'Clean Cut', 'clr_map_brown' => 'Clr Map Brown', 'cobalt_v2_black_blue' => 'Cobalt V2 Black Blue', 'dark_figure_ground_dark_grey' => 'Dark Figure Ground Dark Grey', 'devvela_pms_white_orange' => 'Devvela Pms White Orange', 'extra_black' => 'Extra Black', 'extra_light' => 'Extra Light', 'grass_is_greener_water_is_bluer' => 'Grass Is Greener Water Is Bluer', 'jazzygreen' => 'Jazzygreen', 'light_and_dark' => 'Light And Dark', 'Light_gray' => 'Light Gray', 'mint' => 'Mint', 'mrad_architecture_map' => 'Mrad Architecture Map', 'muted_blue' => 'Muted Blue', 'muted_monotone_gray' => 'Muted Monotone Gray', 'n5_black' => 'N5 Black', 'nature' => 'Nature', 'navigation_gray_white_black' => 'Navigation Gray White Black', 'neutral_blue' => 'Neutral Blue', 'octopvs_bar_3' => 'Octopvs Bar 3', 'ohana72_red_turquise' => 'Ohana72 Red Turquoise', 'old_dry_mud_orange_yellow' => 'Old Dry Mud Orange Yellow', 'old_map' => 'Old Map', 'openform_dark_gray_white' => 'Openform Dark Gray White', 'purple_rain' => 'Purple Rain', 'red_darkness' => 'Red Darkness', 'red_hues' => 'Red Hues', 'red' => 'Red', 'redmapdarck' => 'Redmapdarck', 'seq7_black_white' => 'Seq7 Black White', 'shadow_agent_dark_blue_light_grey' => 'Shadow Agent Dark Blue Light Grey', 'sin_city_black_red' => 'Sin City Black Red', 'snazzy_maps_black_green' => 'Snazzy Maps Black Green', 'two_tone_red_blue' => 'Two Tone Red Blue', 'unsaturated_browns' => 'Unsaturated Browns', 'youthup_verde_blue' => 'Youthup Verde Blue'];
    }
}
