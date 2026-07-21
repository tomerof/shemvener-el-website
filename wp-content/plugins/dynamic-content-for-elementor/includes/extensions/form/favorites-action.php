<?php

// SPDX-FileCopyrightText: 2018-2026 Ovation S.r.l. <help@dynamic.ooo>
// SPDX-License-Identifier: GPL-3.0-or-later
namespace DynamicContentForElementor\Extensions;

use Elementor\Controls_Manager;
use DynamicContentForElementor\Helper;
use DynamicContentForElementor\Tokens;
use DynamicContentForElementor\Plugin;
use ElementorPro\Plugin as ElementorPro;
use ElementorPro\Modules\QueryControl\Module as QueryModule;
use DynamicContentForElementor\Favorites;
if (!\defined('ABSPATH')) {
    exit;
}
// Exit if accessed directly
class FavoritesAction extends \ElementorPro\Modules\Forms\Classes\Action_Base
{
    /**
     * Get Scripts Depends
     *
     * @return array<string>
     */
    public function get_script_depends()
    {
        return [];
    }
    /**
     * Get Style Depends
     *
     * @return array<string>
     */
    public function get_style_depends()
    {
        return [];
    }
    /**
     * @return void
     */
    public function run_once()
    {
        $save_guard = \DynamicContentForElementor\Plugin::instance()->save_guard;
        $save_guard->register_unsafe_control('form', 'dce_form_favorites::dce_form_favorite_action');
        $save_guard->register_unsafe_control('form', 'dce_form_favorites::dce_form_favorite_scope');
        $save_guard->register_unsafe_control('form', 'dce_form_favorites::dce_form_favorite_key');
        $save_guard->register_unsafe_control('form', 'dce_form_favorites::dce_form_favorite_post_id');
        $save_guard->register_unsafe_control('form', 'dce_form_favorites::dce_form_favorite_cookie_expiration');
    }
    /**
     * Has Action
     *
     * @var boolean
     */
    public $has_action = \true;
    /**
     * Get Name
     *
     * Return the action name
     *
     * @access public
     * @return string
     */
    public function get_name()
    {
        return 'dce_form_favorites';
    }
    /**
     * Get Label
     *
     * Returns the action label
     *
     * @access public
     * @return string
     */
    public function get_label()
    {
        return esc_html__('Favorites', 'dynamic-content-for-elementor');
    }
    /**
     * Register Settings Section
     *
     * Registers the Action controls
     *
     * @access public
     * @param \Elementor\Widget_Base $widget
     * @return void
     */
    public function register_settings_section($widget)
    {
        $widget->start_controls_section('section_dce_form_favorites', ['label' => Helper::dce_logo() . $this->get_label(), 'condition' => ['submit_actions' => $this->get_name()]]);
        if (!\DynamicContentForElementor\Helper::can_register_unsafe_controls()) {
            $widget->add_control('admin_notice', ['name' => 'admin_notice', 'type' => Controls_Manager::RAW_HTML, 'raw' => '<div class="elementor-panel-alert elementor-panel-alert-warning">' . esc_html__('You will need administrator capabilities to edit these settings.', 'dynamic-content-for-elementor') . '</div>']);
            $widget->end_controls_section();
            return;
        }
        Plugin::instance()->text_templates->maybe_add_notice($widget, 'favorites');
        $repeater = new \Elementor\Repeater();
        $repeater->add_control('dce_form_favorite_action', ['label' => esc_html__('Action', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'options' => ['add' => esc_html__('Add', 'dynamic-content-for-elementor'), 'remove' => esc_html__('Remove', 'dynamic-content-for-elementor'), 'clear' => esc_html__('Clear All', 'dynamic-content-for-elementor')], 'default' => 'add']);
        $repeater->add_control('dce_form_favorite_scope', ['label' => esc_html__('Scope', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CHOOSE, 'options' => ['cookie' => ['title' => esc_html__('Cookie', 'dynamic-content-for-elementor'), 'icon' => 'icon-dce-cookie'], 'user' => ['title' => esc_html__('User', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-user']], 'toggle' => \false, 'default' => 'user']);
        $repeater->add_control('dce_form_favorite_key', ['label' => esc_html__('Key', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'default' => 'my_favorites', 'description' => esc_html__('The unique name that identifies the favorites in user meta or cookies', 'dynamic-content-for-elementor')]);
        $repeater->add_control('dce_form_favorite_post_id', ['label' => esc_html__('Post ID', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::NUMBER, 'description' => esc_html__('In favorites you need to save a post ID', 'dynamic-content-for-elementor'), 'condition' => ['dce_form_favorite_action!' => 'clear']]);
        $repeater->add_control('dce_form_favorite_cookie_expiration', ['label' => esc_html__('Cookie expiration', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::NUMBER, 'default' => 30, 'min' => 0, 'description' => esc_html__('Value is in days. Set 0 or empty for session duration', 'dynamic-content-for-elementor'), 'condition' => ['dce_form_favorite_scope' => 'cookie']]);
        $widget->add_control('dce_form_favorites', ['label' => esc_html__('Favorites', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::REPEATER, 'title_field' => '{{{ dce_form_favorite_key }}} ({{{ dce_form_favorite_action }}})', 'fields' => $repeater->get_controls()]);
        $widget->end_controls_section();
    }
    /**
     * Run
     *
     * Runs the action after submit
     *
     * @access public
     * @param \ElementorPro\Modules\Forms\Classes\Form_Record $record
     * @param \ElementorPro\Modules\Forms\Classes\Ajax_Handler $ajax_handler
     * @return void
     */
    public function run($record, $ajax_handler)
    {
        $fields = Helper::get_form_data($record);
        $settings = $record->get('form_settings');
        if (isset($settings['dce_form_favorites'])) {
            foreach ($settings['dce_form_favorites'] as $i => $s) {
                $settings['dce_form_favorites'][$i]['dce_form_favorite_key'] = Plugin::instance()->text_templates->expand_shortcodes_or_callback($settings['dce_form_favorites'][$i]['dce_form_favorite_key'], ['form-fields' => $record->get('fields')], function ($str) use($fields) {
                    return Helper::get_dynamic_value($str, $fields);
                });
            }
        }
        $favorites = $settings['dce_form_favorites'];
        if (empty($favorites)) {
            return;
        }
        foreach ($favorites as $favorite) {
            if (empty($favorite['dce_form_favorite_key'])) {
                continue;
            }
            $key = $favorite['dce_form_favorite_key'];
            $scope = $favorite['dce_form_favorite_scope'];
            if ('clear' === $favorite['dce_form_favorite_action']) {
                // Clear All Action
                Favorites::clear_favorites($scope, $key);
            } else {
                // Add or Remove Actions
                if (empty($favorite['dce_form_favorite_post_id'])) {
                    continue;
                }
                $id = Plugin::instance()->text_templates->dce_shortcodes->expand_with_data($favorite['dce_form_favorite_post_id'], ['form-fields' => $record->get('fields')]);
                if ('add' === $favorite['dce_form_favorite_action']) {
                    // Add Action
                    if ('cookie' === $scope) {
                        $cookie_expiration = $favorite['dce_form_favorite_cookie_expiration'] ?? 0;
                        Favorites::add_favorite('cookie', $key, (int) $id, \time() + $cookie_expiration * 86400);
                    } elseif ('user' === $scope) {
                        Favorites::add_favorite('user', $key, (int) $id);
                    }
                } else {
                    // Remove Action
                    if ('cookie' === $scope) {
                        $cookie_expiration = $favorite['dce_form_favorite_cookie_expiration'] ?? 0;
                        Favorites::remove_favorite('cookie', $key, (int) $id, \time() + $cookie_expiration * 86400);
                    } elseif ('user' === $scope) {
                        Favorites::remove_favorite('user', $key, (int) $id);
                    }
                }
            }
        }
    }
    /**
     * @param array<mixed> $element
     * @return array<mixed>
     */
    public function on_export($element)
    {
        return $element;
    }
}
