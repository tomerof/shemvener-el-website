<?php

// SPDX-FileCopyrightText: 2018-2026 Ovation S.r.l. <help@dynamic.ooo>
// SPDX-License-Identifier: GPL-3.0-or-later
namespace DynamicContentForElementor\Widgets;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Text_Shadow;
use DynamicContentForElementor\Helper;
if (!\defined('ABSPATH')) {
    exit;
}
// Exit if accessed directly
class AdvancedFilteringSearchAndFilterPro extends \DynamicContentForElementor\Widgets\WidgetPrototype
{
    /**
     * Register controls after check if this feature is only for admin
     *
     * @return void
     */
    protected function safe_register_controls()
    {
        $this->start_controls_section('section_searchfilter', ['label' => $this->get_title()]);
        if (\DynamicContentForElementor\Integrations\SearchAndFilterPro::is_version(3) || \DynamicContentForElementor\Integrations\SearchAndFilterPro::is_version(3.1)) {
            $this->add_control('version_notice', ['type' => Controls_Manager::NOTICE, 'notice_type' => 'warning', 'content' => esc_html__('This widget is only compatible with Search & Filter Pro version 2. You are using version 3. Please enable the Elementor extension in Search & Filter Pro settings and use the Search & Filter Field widget instead.', 'dynamic-content-for-elementor')]);
        } elseif (\DynamicContentForElementor\Integrations\SearchAndFilterPro::is_version(2)) {
            $this->add_control('search_filter_id', ['label' => esc_html__('Filter', 'dynamic-content-for-elementor'), 'type' => 'ooo_query', 'label_block' => \true, 'placeholder' => esc_html__('Select the filter', 'dynamic-content-for-elementor'), 'query_type' => 'posts', 'object_type' => 'search-filter-widget', 'dynamic' => ['active' => \true]]);
            $this->add_responsive_control('style_align', ['label' => esc_html__('Alignment', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CHOOSE, 'options' => ['left' => ['title' => esc_html__('Left', 'dynamic-content-for-elementor'), 'icon' => 'eicon-text-align-left'], 'center' => ['title' => esc_html__('Center', 'dynamic-content-for-elementor'), 'icon' => 'eicon-text-align-center'], 'right' => ['title' => esc_html__('Right', 'dynamic-content-for-elementor'), 'icon' => 'eicon-text-align-right']], 'selectors' => ['{{WRAPPER}} .searchandfilter > ul > li' => 'text-align: {{VALUE}};'], 'default' => '']);
            $this->add_control('ul_padding', ['label' => esc_html__('ul Padding', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HIDDEN, 'default' => '0', 'selectors' => ['{{WRAPPER}} .searchandfilter > ul' => 'padding: {{VALUE}}; margin: 0']]);
        }
        $this->end_controls_section();
    }
    protected function safe_render()
    {
        $settings = $this->get_settings_for_display();
        if (empty($settings)) {
            return;
        }
        if (!\DynamicContentForElementor\Integrations\SearchAndFilterPro::is_version(2)) {
            return;
        }
        if (is_admin()) {
            require_once plugin_dir_path(SEARCH_FILTER_PRO_BASE_PATH) . 'public/class-search-filter.php';
            // @phpstan-ignore constant.notFound
            \Search_Filter::get_instance();
            // @phpstan-ignore class.notFound
        }
        $search_filter_id = $this->get_settings_for_display('search_filter_id');
        $shortcode = '[searchandfilter id="' . $search_filter_id . '"]';
        echo do_shortcode(shortcode_unautop($shortcode));
    }
}
