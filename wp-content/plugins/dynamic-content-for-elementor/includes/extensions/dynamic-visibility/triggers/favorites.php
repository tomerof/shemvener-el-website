<?php

// SPDX-FileCopyrightText: 2018-2026 Ovation S.r.l. <help@dynamic.ooo>
// SPDX-License-Identifier: GPL-3.0-or-later
namespace DynamicContentForElementor\Extensions\DynamicVisibility\Triggers;

use Elementor\Controls_Manager;
use DynamicContentForElementor\Favorites as FavoritesClass;
class Favorites extends \DynamicContentForElementor\Extensions\DynamicVisibility\Triggers\Base
{
    /**
     * @param \Elementor\Element_Base $element
     * @return void
     */
    public function register_controls($element)
    {
        $element->add_control('dce_visibility_favorites_enable', ['label' => __('Check if a page/post is in favorites', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => '']);
        $element->add_control('dce_visibility_favorites_post_source', ['label' => __('Page/post to check', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'options' => ['current' => __('Current page/post', 'dynamic-content-for-elementor'), 'other' => __('Another page/post', 'dynamic-content-for-elementor')], 'default' => 'current', 'condition' => ['dce_visibility_favorites_enable!' => '']]);
        $element->add_control('dce_visibility_favorites_post_id', ['label' => __('Select page/post', 'dynamic-content-for-elementor'), 'type' => 'ooo_query', 'placeholder' => __('Title', 'dynamic-content-for-elementor'), 'label_block' => \true, 'query_type' => 'posts', 'condition' => ['dce_visibility_favorites_enable!' => '', 'dce_visibility_favorites_post_source' => 'other']]);
        $element->add_control('dce_visibility_favorites_scope', ['label' => esc_html__('Scope', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CHOOSE, 'options' => ['cookie' => ['title' => esc_html__('Cookie', 'dynamic-content-for-elementor'), 'icon' => 'icon-dce-cookie'], 'user' => ['title' => esc_html__('User', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-user']], 'toggle' => \false, 'default' => 'user', 'condition' => ['dce_visibility_favorites_enable!' => '']]);
        $element->add_control('dce_visibility_favorites_key', ['label' => __('Favorite Key', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'default' => 'my_favorites', 'description' => __('Enter the key used to store favorites', 'dynamic-content-for-elementor'), 'ai' => ['active' => \false], 'condition' => ['dce_visibility_favorites_enable!' => '']]);
    }
    /**
     * @param array<string,mixed> $settings
     * @param array<string,mixed> &$triggers
     * @param array<string,mixed> &$conditions
     * @param array<string,mixed> &$required
     * @param \Elementor\Element_Base $element
     * @return void
     */
    public function check_conditions($settings, &$triggers, &$conditions, &$required, $element)
    {
        if (empty($settings['dce_visibility_favorites_enable']) || empty($settings['dce_visibility_favorites_key'])) {
            return;
        }
        $post_id = null;
        if ($settings['dce_visibility_favorites_post_source'] === 'current') {
            $post_id = get_the_ID();
        } else {
            $post_id = $settings['dce_visibility_favorites_post_id'] ?? null;
        }
        if (!$post_id) {
            return;
        }
        $scope = $settings['dce_visibility_favorites_scope'] ?? 'user';
        $key = $settings['dce_visibility_favorites_key'];
        $triggers['dce_visibility_favorites'] = __('Is Favorited', 'dynamic-content-for-elementor');
        $required['dce_visibility_favorites'] = \true;
        if (FavoritesClass::is_favorited($key, $scope, $post_id)) {
            $conditions['dce_visibility_favorites'] = __('Is Favorited', 'dynamic-content-for-elementor');
        }
    }
}
