<?php

// SPDX-FileCopyrightText: 2018-2026 Ovation S.r.l. <help@dynamic.ooo>
// SPDX-License-Identifier: GPL-3.0-or-later
namespace DynamicContentForElementor;

if (!\defined('ABSPATH')) {
    exit;
}
class Widgets
{
    public function __construct()
    {
        add_action('elementor/elements/categories_registered', [$this, 'add_elementor_widget_categories'], 20);
    }
    public function on_widgets_registered()
    {
        $this->register_widgets();
    }
    public function register_widgets()
    {
        $widgets = \DynamicContentForElementor\Plugin::instance()->features->filter(['type' => 'widget', 'status' => 'active']);
        foreach ($widgets as $widget_info) {
            if (\DynamicContentForElementor\Helper::is_dependencies_satisfied($widget_info['plugin_depends']) && (!isset($widget_info['minimum_php']) || isset($widget_info['minimum_php']) && \version_compare(\phpversion(), $widget_info['minimum_php'], '>='))) {
                $widget_class = '\\DynamicContentForElementor\\' . $widget_info['class'];
                /**
                 * @var \Elementor\Widget_Base $widget_object;
                 */
                $widget_object = new $widget_class();
                if (\method_exists($widget_object, 'run_once')) {
                    $widget_object->run_once();
                }
                \Elementor\Plugin::instance()->widgets_manager->register($widget_object);
            }
        }
    }
    public function add_elementor_widget_categories($elements)
    {
        // Default category for widgets without a category
        $elements->add_category('dynamic-content-for-elementor', array('title' => DCE_BRAND));
        $groups = \DynamicContentForElementor\Plugin::instance()->features->get_widgets_groups();
        // Add categories
        foreach ($groups as $group_key => $group_name) {
            $elements->add_category('dynamic-content-for-elementor-' . \strtolower($group_key), array('title' => DCE_BRAND . ' - ' . $group_name));
        }
    }
}
