<?php

// SPDX-FileCopyrightText: 2018-2026 Ovation S.r.l. <help@dynamic.ooo>
// SPDX-License-Identifier: GPL-3.0-or-later
namespace DynamicContentForElementor\Modules\DynamicTags\Tags;

use Elementor\Core\DynamicTags\Tag;
use Elementor\Controls_Manager;
use DynamicContentForElementor\Helper;
if (!\defined('ABSPATH')) {
    exit;
    // Exit if accessed directly
}
class Posts extends Tag
{
    /**
     * Get Name
     *
     * @return string
     */
    public function get_name()
    {
        return 'dce-posts';
    }
    /**
     * Get Title
     *
     * @return string
     */
    public function get_title()
    {
        return esc_html__('Posts', 'dynamic-content-for-elementor');
    }
    /**
     * Get Group
     *
     * @return string
     */
    public function get_group()
    {
        return 'dce';
    }
    /**
     * Get Categories
     *
     * @return array<string>
     */
    public function get_categories()
    {
        return ['base', 'text'];
    }
    /**
     * Register Controls
     *
     * @return void
     */
    protected function register_controls()
    {
        $this->add_control('separator', ['label' => esc_html__('Separator', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'options' => ['new_line' => esc_html__('New Line', 'dynamic-content-for-elementor'), 'line_break' => esc_html__('Line Break', 'dynamic-content-for-elementor'), 'comma' => esc_html__('Comma', 'dynamic-content-for-elementor')], 'default' => 'new_line', 'multiple' => \true]);
        $this->add_control('post_type', ['label' => esc_html__('Post Type', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT2, 'options' => Helper::get_public_post_types(), 'multiple' => \true, 'label_block' => \true]);
        $this->add_control('post_status', ['label' => esc_html__('Post Status', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT2, 'options' => get_post_statuses(), 'multiple' => \true, 'label_block' => \true, 'default' => ['publish']]);
        $this->add_control('orderby', ['label' => esc_html__('Order By', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'options' => Helper::get_post_orderby_options(), 'default' => 'date']);
        $this->add_control('meta_type', ['label' => esc_html__('Meta Type', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'options' => Helper::get_post_orderby_meta_value_types(), 'default' => 'CHAR', 'condition' => ['orderby' => 'meta_value']]);
        $this->add_control('order', ['label' => esc_html__('Order', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'options' => ['ASC' => esc_html__('Ascending', 'dynamic-content-for-elementor'), 'DESC' => esc_html__('Descending', 'dynamic-content-for-elementor')], 'default' => 'DESC']);
        $this->add_control('posts', ['label' => esc_html__('Results', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::NUMBER, 'default' => '10']);
        $this->add_control('return_format', ['label' => esc_html__('Return Format', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'options' => ['title' => esc_html__('Title', 'dynamic-content-for-elementor'), 'title_id' => esc_html__('Title | ID', 'dynamic-content-for-elementor'), 'id' => esc_html__('ID', 'dynamic-content-for-elementor')], 'default' => 'title']);
        $this->add_control('link', ['label' => esc_html__('Link', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER]);
        $this->add_control('open_in_new_window', ['label' => esc_html__('Open in a New Window', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'condition' => ['link' => 'yes']]);
    }
    /**
     * Render
     *
     * @return void
     */
    public function render()
    {
        $settings = $this->get_settings_for_display();
        if (empty($settings)) {
            return;
        }
        /**
         * @var array<int>
         */
        $post_ids = $this->get_query_items();
        if (empty($post_ids)) {
            return;
        }
        foreach ($post_ids as $post_id) {
            if ('yes' === $settings['link']) {
                $this->set_render_attribute('link', 'href', get_permalink($post_id) ?: '#');
                if ('yes' === $settings['open_in_new_window']) {
                    $this->set_render_attribute('link', 'target', '_blank');
                }
                echo '<a ' . $this->get_render_attribute_string('link') . '>' . $this->get_post_by_format($post_id) . '</a>';
            } else {
                echo $this->get_post_by_format($post_id);
            }
            if ($post_id !== \end($post_ids)) {
                echo $this->separator($settings['separator']);
            }
        }
    }
    /**
     * Get Items
     *
     * @return array<int>
     */
    protected function get_query_items()
    {
        $args = $this->get_args();
        if (empty($args)) {
            return [];
        }
        /** @var array<int> $post_ids */
        $post_ids = get_posts($args);
        return $post_ids;
    }
    /**
     * Get Post By Format
     *
     * @param int $post_id
     * @return string|int|false
     */
    protected function get_post_by_format($post_id)
    {
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
    /**
     * Get Args
     *
     * @return array<string,mixed>
     */
    protected function get_args()
    {
        $settings = $this->get_settings_for_display();
        $args = ['post_type' => \DynamicContentForElementor\Helper::validate_post_types($settings['post_type']), 'posts_per_page' => $settings['posts'], 'order' => $settings['order'], 'orderby' => $settings['orderby'], 'post_status' => $settings['post_status'], 'fields' => 'ids'];
        if ('meta_value' === $settings['orderby']) {
            $args['meta_type'] = $settings['meta_type'];
        }
        return $args;
    }
    /**
     * Separator
     *
     * @param string $choice
     * @return string
     */
    protected function separator(string $choice)
    {
        switch ($choice) {
            case 'line_break':
                return '<br />';
            case 'new_line':
                return "\n";
            case 'comma':
                return ', ';
        }
        return '';
    }
}
