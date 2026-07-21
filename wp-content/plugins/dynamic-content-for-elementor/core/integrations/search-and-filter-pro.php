<?php

// SPDX-FileCopyrightText: 2018-2026 Ovation S.r.l. <help@dynamic.ooo>
// SPDX-License-Identifier: GPL-3.0-or-later
namespace DynamicContentForElementor\Integrations;

use DynamicContentForElementor\Helper;
if (!\defined('ABSPATH')) {
    exit;
    // Exit if accessed directly.
}
class SearchAndFilterPro
{
    const QUERY_INTEGRATION_NAME = 'dynamicooo/dynamic-content-for-elementor';
    public function __construct()
    {
        add_action('search-filter/settings/init', [$this, 'add_integration_v3'], 10);
        add_filter('search-filter/queries/query/get_attributes', [$this, 'add_query_attributes_v3'], 10, 2);
        add_action('search-filter/settings/init', [$this, 'hide_css_selector_options'], 10);
    }
    /**
     * @return void
     */
    public function add_integration_v3()
    {
        if (!self::is_version(3.1)) {
            return;
        }
        $integration_type_setting = \Search_Filter\Queries\Settings::get_setting('queryIntegration', 'query');
        if (!$integration_type_setting) {
            return;
        }
        $integration_type_setting->add_option(['label' => DCE_PRODUCT_NAME_LONG, 'value' => self::QUERY_INTEGRATION_NAME]);
    }
    /**
     * Automatically set the Ajax CSS selectors for the query container and pagination
     * so the user doesn't have to set it themselves.
     *
     * @param array<string,string> $attributes
     * @param \Search_Filter\Queries\Query $query
     * @return array<string,string>
     */
    public function add_query_attributes_v3($attributes, $query)
    {
        // We want `queryContainer` and `paginationSelector` to be set automatically.
        $id = $query->get_id();
        if (!isset($attributes['queryIntegration'])) {
            return $attributes;
        }
        $query_integration = $attributes['queryIntegration'];
        $integration_type = $attributes['integrationType'];
        if (self::QUERY_INTEGRATION_NAME === $query_integration) {
            // The query container is a CSS selector that contains the results and pagination.
            $attributes['queryContainer'] = '.search-filter-dynamic-posts-results-' . $query->get_id();
            $dynamic_sections = '.search-filter-dynamic-google-maps-results-' . $query->get_id();
            if (!empty($attributes['dynamicSections'])) {
                $attributes['dynamicSections'] .= ', ' . $dynamic_sections;
            } else {
                $attributes['dynamicSections'] = $dynamic_sections;
            }
            // We use the posts container for when we use the "load more" pagination type
            $attributes['queryPostsContainer'] = '.search-filter-dynamic-posts-results-' . $query->get_id() . ' .dce-posts-wrapper';
            // The pagination selector can be a full select - also changed the attribute from `paginationSelector` -> `queryPaginationSelector`
            $attributes['queryPaginationSelector'] = '.search-filter-dynamic-posts-results-' . $query->get_id() . ' .dce-pagination a';
        }
        return $attributes;
    }
    /**
     * Hide CSS selector options from query editor.
     *
     * @return void
     */
    public function hide_css_selector_options()
    {
        $depends_conditions = array('relation' => 'AND', 'action' => 'hide', 'rules' => [['option' => 'queryIntegration', 'compare' => '!=', 'value' => self::QUERY_INTEGRATION_NAME]]);
        $query_container = \Search_Filter\Queries\Settings::get_setting('queryContainer');
        if ($query_container) {
            $query_container->add_depends_condition($depends_conditions);
        }
        $query_posts_container = \Search_Filter\Queries\Settings::get_setting('queryPostsContainer');
        if ($query_posts_container) {
            $query_posts_container->add_depends_condition($depends_conditions);
        }
        $pagination_selector = \Search_Filter\Queries\Settings::get_setting('queryPaginationSelector');
        if ($pagination_selector) {
            $pagination_selector->add_depends_condition($depends_conditions);
        }
    }
    /**
     * @param \Elementor\Widget_Base $context
     * @param array<string,string> $overrides
     * @return void
     */
    public function maybe_add_search_filter_class($context, array $overrides = [])
    {
        $default_config = ['query_type' => 'query_type', 'id_setting_key_v2' => 'search_filter_id', 'id_setting_key_v3' => 'search_filter_v3_id', 'class_prefix_v2' => 'search-filter-results-', 'class_prefix_v3' => ''];
        $config = \array_merge($default_config, $overrides);
        if ('search_filter' !== $context->get_settings($config['query_type'])) {
            return;
        }
        $search_filter_id = null;
        $element_class = '';
        if (self::is_version(2) && \version_compare(SEARCH_FILTER_VERSION, '2.5.5', '>=')) {
            $search_filter_id = $context->get_settings_for_display($config['id_setting_key_v2']);
            $search_filter_id = apply_filters('wpml_object_id', $search_filter_id, 'search-filter-widget', \true);
            $element_class = $config['class_prefix_v2'];
        } elseif (self::is_version(3.1)) {
            $search_filter_id = $context->get_settings_for_display($config['id_setting_key_v3']);
            $element_class = $config['class_prefix_v3'];
        }
        if (!$search_filter_id) {
            return;
        }
        $context->add_render_attribute('_wrapper', ['class' => [$element_class . \intval($search_filter_id)]]);
    }
    /**
     * Check Search & Filter Pro version buckets.
     *
     * Allowed values: 2, 3, 3.1
     *  - 2: SEARCH_FILTER_VERSION in [2.0.0, 3.0.0)
     *  - 3: SEARCH_FILTER_PRO_VERSION in [3.0.0, 3.1.0)
     *  - 3.1: SEARCH_FILTER_PRO_VERSION >= 3.1.0
     *
     * @param int|float $version
     * @return bool
     */
    public static function is_version($version)
    {
        if (!\in_array($version, [2, 3, 3.1], \true)) {
            return \false;
        }
        if (2 === $version) {
            return \defined('SEARCH_FILTER_VERSION') && \version_compare(SEARCH_FILTER_VERSION, '2.0.0', '>=') && \version_compare(SEARCH_FILTER_VERSION, '3.0.0', '<');
        }
        $is_pro_defined = \defined('SEARCH_FILTER_PRO_VERSION');
        if (!$is_pro_defined) {
            return \false;
        }
        if (3 === $version) {
            return \version_compare(SEARCH_FILTER_PRO_VERSION, '3.0.0', '>=') && \version_compare(SEARCH_FILTER_PRO_VERSION, '3.1.0', '<');
        }
        // Version 3.1 or greater
        return \version_compare(SEARCH_FILTER_PRO_VERSION, '3.1.0', '>=');
    }
}
