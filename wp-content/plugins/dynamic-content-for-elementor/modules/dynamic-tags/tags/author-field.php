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
class AuthorField extends Tag
{
    /**
     * @return string
     */
    public function get_name()
    {
        return 'dce-author-field';
    }
    /**
     * @return string
     */
    public function get_title()
    {
        return esc_html__('Author Field', 'dynamic-content-for-elementor');
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
        $this->add_control('field', ['label' => esc_html__('Author Field', 'dynamic-content-for-elementor'), 'type' => 'ooo_query', 'placeholder' => esc_html__('Field Name', 'dynamic-content-for-elementor'), 'label_block' => \true, 'query_type' => 'fields', 'object_type' => 'user']);
    }
    /**
     * @return void
     */
    public function render()
    {
        /**
         * @var array{'field': string} $settings
         */
        $settings = $this->get_settings_for_display();
        if (empty($settings['field'])) {
            return;
        }
        $id = get_the_author_meta('ID');
        $field = $settings['field'];
        if (\in_array($field, ['user_pass', 'pass', 'user_activation_key', 'activation_key'], \true)) {
            return;
        }
        $attributes = ['display_name', 'id', 'user_login', 'user_email', 'user_nicename', 'first_name', 'last_name', 'display_name'];
        if (\in_array($field, $attributes, \true)) {
            $userdata = get_userdata($id);
            if (\false === $userdata) {
                return;
            }
            echo esc_html($userdata->{$field} ?? '');
        } else {
            echo wp_kses_post(get_user_meta($id, $field, \true));
        }
    }
}
