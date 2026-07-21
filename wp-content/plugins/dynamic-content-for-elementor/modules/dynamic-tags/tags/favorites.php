<?php

// SPDX-FileCopyrightText: 2018-2026 Ovation S.r.l. <help@dynamic.ooo>
// SPDX-License-Identifier: GPL-3.0-or-later
namespace DynamicContentForElementor\Modules\DynamicTags\Tags;

use Elementor\Core\DynamicTags\Tag;
use Elementor\Controls_Manager;
use DynamicContentForElementor\Helper;
use DynamicContentForElementor\Favorites as FavoritesClass;
if (!\defined('ABSPATH')) {
    exit;
    // Exit if accessed directly
}
class Favorites extends Tag
{
    /**
     * @return string
     */
    public function get_name()
    {
        return 'dce-favorites';
    }
    /**
     * @return string
     */
    public function get_title()
    {
        return esc_html__('Favorites', 'dynamic-content-for-elementor');
    }
    /**
     * @return string
     */
    public function get_group()
    {
        return 'dce';
    }
    /**
     * @return array<string>
     */
    public function get_categories()
    {
        return ['base', 'text'];
    }
    /**
     * @return void
     */
    protected function register_controls()
    {
        $this->add_control('favorites_scope', ['label' => esc_html__('Favorites from', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CHOOSE, 'options' => ['cookie' => ['title' => esc_html__('Cookie', 'dynamic-content-for-elementor'), 'icon' => 'icon-dce-cookie'], 'user' => ['title' => esc_html__('User', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-user']], 'toggle' => \false, 'default' => 'user']);
        $this->add_control('favorites_key', ['label' => esc_html__('Favorites Key', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'default' => 'my_favorites']);
        $this->add_control('favorites_separator', ['label' => esc_html__('Separator', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'options' => ['new_line' => esc_html__('New Line', 'dynamic-content-for-elementor'), 'line_break' => esc_html__('Line Break', 'dynamic-content-for-elementor'), 'comma' => esc_html__('Comma', 'dynamic-content-for-elementor')], 'default' => 'line_break', 'multiple' => \true]);
        $this->add_control('favorites_link', ['label' => esc_html__('Link to Favorite', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'condition' => ['favorites_separator!' => 'new_line']]);
        $this->add_control('favorites_post_type', ['label' => esc_html__('Post Type', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT2, 'options' => Helper::get_public_post_types(), 'multiple' => \true, 'label_block' => \true]);
        $this->add_control('favorites_post_status', ['label' => esc_html__('Post Status', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT2, 'options' => get_post_statuses(), 'multiple' => \true, 'label_block' => \true, 'default' => ['publish']]);
        $this->add_control('favorites_orderby', ['label' => esc_html__('Order By', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'options' => Helper::get_post_orderby_options(), 'default' => 'date']);
        $this->add_control('favorites_order', ['label' => esc_html__('Order', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'options' => ['ASC' => esc_html__('Ascending', 'dynamic-content-for-elementor'), 'DESC' => esc_html__('Descending', 'dynamic-content-for-elementor')], 'default' => 'DESC']);
        $this->add_control('favorites_posts', ['label' => esc_html__('Results', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::NUMBER, 'default' => '10']);
        $this->add_control('return_format', ['label' => esc_html__('Return Format', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'options' => ['title' => esc_html__('Title', 'dynamic-content-for-elementor'), 'title_id' => esc_html__('Title | ID', 'dynamic-content-for-elementor'), 'id' => esc_html__('ID', 'dynamic-content-for-elementor')], 'default' => 'title']);
        $this->add_control('favorites_fallback', ['label' => esc_html__('Fallback Content', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'default' => esc_html__('No favorites found', 'dynamic-content-for-elementor')]);
    }
    /**
     * Get Post By Format
     *
     * @param int $post_id
     * @return string|int|void
     */
    protected function get_post_by_format($post_id = null)
    {
        if (!$post_id) {
            return;
        }
        $return_format = $this->get_settings('return_format');
        switch ($return_format) {
            case 'title_id':
                return esc_html(get_the_title($post_id)) . '|' . $post_id;
            case 'id':
                return $post_id;
            default:
                return esc_html(get_the_title($post_id));
        }
    }
    public function render()
    {
        $settings = $this->get_settings_for_display();
        if (empty($settings)) {
            return;
        }
        $favorites_post_in = FavoritesClass::get($settings['favorites_key'], $settings['favorites_scope']);
        if (!empty($favorites_post_in)) {
            if ('dce_wishlist' !== $settings['favorites_key']) {
                // Favorites
                $post_types = \DynamicContentForElementor\Helper::validate_post_types($settings['favorites_post_type']);
                if (empty($post_types)) {
                    return;
                }
                $post_ids = \array_map('intval', $favorites_post_in);
                $args = ['post_type' => $post_types, 'post__in' => $post_ids, 'posts_per_page' => (int) $settings['favorites_posts'], 'order' => $settings['favorites_order'], 'orderby' => $settings['favorites_orderby'], 'post_status' => $settings['favorites_post_status'], 'suppress_filters' => \false];
            } else {
                // Woo Wishlist
                if (!is_user_logged_in()) {
                    return;
                }
                $wishlist = [];
                foreach ($favorites_post_in as $product) {
                    if ('product' === get_post_type((int) $product) && !wc_customer_bought_product('', get_current_user_id(), (int) $product)) {
                        $wishlist[] = (int) $product;
                    }
                }
                // If nothing survives the filtering, show the fallback. An empty
                // post__in would otherwise be ignored by the query and every
                // product would be listed.
                if (empty($wishlist)) {
                    self::render_fallback($settings['favorites_fallback']);
                    return;
                }
                $args = ['post_type' => 'product', 'post__in' => $wishlist, 'posts_per_page' => (int) $settings['favorites_posts'], 'order' => $settings['favorites_order'], 'orderby' => $settings['favorites_orderby'], 'post_status' => $settings['favorites_post_status']];
            }
            $posts = get_posts($args);
            if (!empty($posts)) {
                foreach ($posts as $post) {
                    setup_postdata($post);
                    if ('new_line' === $settings['favorites_separator'] || empty($settings['favorites_link'])) {
                        echo $this->get_post_by_format($post->ID);
                    } else {
                        echo '<a href="' . esc_url(get_the_permalink($post)) . '">' . $this->get_post_by_format($post->ID) . '</a>';
                    }
                    if ($post !== \end($posts)) {
                        echo self::separator($settings['favorites_separator']);
                    }
                }
                wp_reset_postdata();
            } else {
                self::render_fallback($settings['favorites_fallback']);
            }
        } else {
            self::render_fallback($settings['favorites_fallback']);
        }
    }
    public function render_fallback(string $fallback = '')
    {
        if (!$fallback) {
            return;
        }
        echo wp_kses_post($fallback);
    }
    public function separator(string $choice)
    {
        switch ($choice) {
            case 'line_break':
                return '<br />';
            case 'new_line':
                return "\n";
            case 'comma':
                return ', ';
        }
    }
}
