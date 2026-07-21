<?php

// SPDX-FileCopyrightText: 2018-2026 Ovation S.r.l. <help@dynamic.ooo>
// SPDX-License-Identifier: GPL-3.0-or-later
namespace DynamicContentForElementor;

use Elementor\Core\Base\Document;
use Elementor\Core\Settings\Page\Manager as PageManager;
if (!\defined('ABSPATH')) {
    exit;
}
class SaveGuard
{
    /**
     * @var bool
     */
    private $doing_save = \false;
    /**
     * @var array<string>
     */
    private $unsafe_widgets = [];
    /**
     * @var array<string,mixed>
     */
    private $unsafe_controls = [];
    /**
     * @var array<string>
     */
    private $unsafe_dynamic_tags = ['dce-dynamic-tag-php', 'dce-dynamic-tag-image-token', 'dce-token', 'dce-acf-relationship', 'dce-favorites', 'dce-metabox-relationship', 'dce-my-posts', 'dce-posts', 'dce-sticky-posts', 'dce-woo-products', 'dce-wishlist', 'dce-user-field', 'dce-author-field', 'dce-template', 'dce-dsh-wizard', 'dce-dsh-wizard-image', 'dce-dsh-wizard-gallery'];
    /**
     * @var array<string,mixed>
     */
    private $saved_data;
    /**
     * @param string $type
     *
     * @return void
     */
    public function register_unsafe_widget($type)
    {
        $this->unsafe_widgets[] = $type;
    }
    /**
     * @param string $widget_type
     * @param string $control_path
     *
     * @return void
     */
    public function register_unsafe_control($widget_type, $control_path)
    {
        if (!isset($this->unsafe_controls[$widget_type])) {
            $this->unsafe_controls[$widget_type] = [];
        }
        $this->unsafe_controls[$widget_type][$control_path] = \true;
    }
    /**
     * @return never
     */
    private function denied()
    {
        $msg = DCE_BRAND . ' ' . esc_html__('Only administrators can edit this Elementor Page', 'dynamic-content-for-elementor');
        throw new \Exception($msg);
        //phpcs:ignore WordPress.Security.EscapeOutput
    }
    /**
     * @param array<mixed> $elements
     * @param string $_id
     *
     * @return array<mixed>|false
     */
    private function find_element_by__id($elements, $_id)
    {
        $res = \array_filter($elements, function ($e) use($_id) {
            return \is_array($e) && isset($e['_id']) && $e['_id'] === $_id;
        });
        return \current($res);
    }
    /**
     * @param string $id
     *
     * @return array<string,mixed>|false
     */
    private function find_saved_element($id)
    {
        return \DynamicContentForElementor\Helper::find_element_recursive($this->saved_data['elements'], $id);
    }
    /**
     * @param string $element_id
     * @param string $repeater_name
     * @param string $field__id
     *
     * @return array<string,mixed>|false
     */
    private function find_saved_repeater_field($element_id, $repeater_name, $field__id)
    {
        $sel = $this->find_saved_element($element_id);
        if (!$sel || !isset($sel['settings'][$repeater_name]) || !\is_array($sel['settings'][$repeater_name])) {
            return \false;
        }
        return $this->find_element_by__id($sel['settings'][$repeater_name], $field__id);
    }
    /**
     * @param string $id
     * @return array<string,mixed>|never
     */
    private function find_saved_element_or_deny($id)
    {
        if ($id === '') {
            return $this->saved_data;
        }
        $el = $this->find_saved_element($id);
        if (!$el) {
            $this->denied();
        }
        return $el;
    }
    /**
     * @param array<string,mixed> $settings
     * @param Callable $saved_settings_callback
     *
     * @return array<string,mixed>
     */
    private function filter_dynamic_tags_flat($settings, $saved_settings_callback)
    {
        foreach ($settings as $key => $val) {
            if ('__dynamic__' === $key) {
                foreach ($val as $dt_key => $dt_value) {
                    foreach ($this->unsafe_dynamic_tags as $unsafe_dt) {
                        if (\strpos($dt_value, $unsafe_dt)) {
                            $saved_settings = $saved_settings_callback();
                            if (isset($saved_settings['__dynamic__'][$dt_key])) {
                                $settings['__dynamic__'][$dt_key] = $saved_settings['__dynamic__'][$dt_key];
                            } else {
                                unset($settings['__dynamic__'][$dt_key]);
                            }
                        }
                    }
                }
            }
        }
        return $settings;
    }
    /**
     * @param string $element_id
     * @param array<string,mixed> $settings
     *
     * @return array<string,mixed>
     */
    private function filter_dynamic_tags($element_id, $settings)
    {
        $settings = $this->filter_dynamic_tags_flat($settings, function () use($element_id) {
            $sel = $this->find_saved_element($element_id);
            return $sel ? $sel['settings'] : \false;
        });
        // Descend into repeaters and any nested arrays. We must not trust the
        // presence of '_id' to decide whether to inspect a row: the save data
        // is the raw client payload, so an attacker can omit '_id' to make a
        // row look like "not a field" and slip an unsafe dynamic tag past the
        // filter (it would also never be a top-level '__dynamic__' key, so the
        // flat filter above does not catch it either).
        foreach ($settings as $key => $val) {
            if ('__dynamic__' !== $key && \is_array($val)) {
                $settings[$key] = $this->filter_dynamic_tags_recursive($element_id, $key, $val, \true);
            }
        }
        return $settings;
    }
    /**
     * Recursively filter unsafe dynamic tags inside repeater rows (and any
     * nested arrays). Every '__dynamic__' map found at any depth is filtered.
     *
     * A row is matched to its previously saved counterpart by '_id' so that a
     * non-admin save preserves dynamic tags an administrator legitimately
     * stored. When a row has no '_id' (or its '_id' has no saved counterpart,
     * or it is nested deeper than a top-level repeater), it cannot be safely
     * restored, so any unsafe dynamic tag it contains is removed (fail-safe)
     * rather than skipped. Safe dynamic tags are always left untouched.
     *
     * @param string $element_id
     * @param string $repeater_name
     * @param array<mixed> $rows
     * @param bool $top_level Whether $rows is a top-level repeater of the element, so saved rows can be matched by '_id'.
     *
     * @return array<mixed>
     */
    private function filter_dynamic_tags_recursive($element_id, $repeater_name, $rows, $top_level)
    {
        foreach ($rows as $index => $field) {
            if (!\is_array($field)) {
                continue;
            }
            $field__id = $top_level && isset($field['_id']) ? $field['_id'] : null;
            $rows[$index] = $this->filter_dynamic_tags_flat($rows[$index], function () use($element_id, $repeater_name, $field__id) {
                if (null === $field__id) {
                    return \false;
                }
                return $this->find_saved_repeater_field($element_id, $repeater_name, $field__id);
            });
            foreach ($rows[$index] as $sub_key => $sub_val) {
                if ('__dynamic__' !== $sub_key && \is_array($sub_val)) {
                    $rows[$index][$sub_key] = $this->filter_dynamic_tags_recursive($element_id, $sub_key, $sub_val, \false);
                }
            }
        }
        return $rows;
    }
    /**
     * @param string $element_id
     * @param array<string,mixed> $settings
     * @param string $widget_type
     *
     * @return array<string,mixed>
     */
    private function filter_unsafe_controls($element_id, $settings, $widget_type)
    {
        $controls = $this->unsafe_controls[$widget_type] ?? [];
        $controls += $this->unsafe_controls['any'] ?? [];
        foreach (\array_keys($controls) as $key) {
            // if the control is inside a repeater:
            if (\strpos($key, '::')) {
                list($repeater, $subkey) = \explode('::', $key);
                // look through all the repeater fields:
                foreach ($settings[$repeater] ?? [] as $index => $field) {
                    if (isset($field[$subkey])) {
                        $saved_field = $this->find_saved_repeater_field($element_id, $repeater, $field['_id']);
                        if ($saved_field && isset($saved_field[$subkey])) {
                            $settings[$repeater][$index][$subkey] = $saved_field[$subkey];
                        } else {
                            unset($settings[$repeater][$index][$subkey]);
                        }
                    }
                }
            }
            if (isset($settings[$key])) {
                $sel = $this->find_saved_element($element_id);
                if ($sel && isset($sel['settings'][$key])) {
                    $settings[$key] = $sel['settings'][$key];
                } else {
                    unset($settings[$key]);
                }
            }
        }
        return $settings;
    }
    /**
     * @param string $element_id
     * @param array<string,mixed> $settings
     * @param string $widget_type
     *
     * @return array<string,mixed>
     */
    private function filter_settings($element_id, $settings, $widget_type)
    {
        $settings = $this->filter_dynamic_tags($element_id, $settings);
        return $this->filter_unsafe_controls($element_id, $settings, $widget_type);
    }
    /**
     * @param array<string,mixed> $element
     *
     * @return array<string,mixed>
     */
    private function filter_element($element)
    {
        $type = $element['widgetType'] ?? \false;
        if ($type && \in_array($type, $this->unsafe_widgets, \true)) {
            $saved_element = $this->find_saved_element_or_deny($element['id']);
            return $saved_element;
        }
        if (isset($element['settings'])) {
            $element['settings'] = $this->filter_settings($element['id'] ?? '', $element['settings'], $type);
        }
        foreach ($element['elements'] ?? [] as $index => $el) {
            $element['elements'][$index] = $this->filter_element($el);
        }
        return $element;
    }
    /**
     * @param Document $document
     *
     * @return array<string,mixed>
     */
    public function get_saved_data($document)
    {
        $elements = $document->get_elements_raw_data();
        $page_settings_manager = \Elementor\Core\Settings\Manager::get_settings_managers('page');
        if (\is_array($page_settings_manager)) {
            throw new \Error();
        }
        $model = $page_settings_manager->get_model($document->get_post()->ID);
        $settings = $model->get_settings();
        return ['elements' => $elements, 'settings' => $settings];
    }
    /**
     * @param array<string,mixed> $data
     * @param Document $document
     *
     * @return array<string,mixed>
     */
    public function filter_save_data($data, $document)
    {
        if (empty($data) || $this->doing_save) {
            // needed to avoid infinite recursion when getting saved data of a
            // new elementor post and potentially other situations.
            return $data;
        }
        if (\DynamicContentForElementor\Helper::current_user_is_admin()) {
            return $data;
        }
        $this->doing_save = \true;
        $this->saved_data = $this->get_saved_data($document);
        $filtered = $this->filter_element($data);
        $this->doing_save = \false;
        return $filtered;
    }
    /**
     * @var array<string,mixed>|null
     */
    private $pre_restore_data = null;
    /**
     * @param int $post_id
     * @param int $revision_id
     * @return void
     */
    public function capture_pre_restore_data($post_id, $revision_id)
    {
        if (\DynamicContentForElementor\Helper::current_user_is_admin()) {
            return;
        }
        $raw = get_post_meta($post_id, '_elementor_data', \true);
        $elements = \is_string($raw) && '' !== $raw ? \json_decode($raw, \true) : $raw;
        $page = get_post_meta($post_id, '_elementor_page_settings', \true);
        $this->pre_restore_data = ['elements' => \is_array($elements) ? $elements : [], 'page_settings' => \is_array($page) ? $page : []];
    }
    /**
     * @param int $post_id
     * @param int $revision_id
     * @return void
     */
    public function filter_restored_revision($post_id, $revision_id)
    {
        if (\DynamicContentForElementor\Helper::current_user_is_admin() || $this->doing_save) {
            return;
        }
        $baseline = \is_array($this->pre_restore_data) ? $this->pre_restore_data : [];
        $base_elements = \is_array($baseline['elements'] ?? null) ? $baseline['elements'] : [];
        $base_page = \is_array($baseline['page_settings'] ?? null) ? $baseline['page_settings'] : [];
        $this->pre_restore_data = null;
        $this->doing_save = \true;
        $this->saved_data = ['elements' => $base_elements, 'settings' => []];
        try {
            if (isset(\Elementor\Plugin::$instance->widgets_manager)) {
                \Elementor\Plugin::$instance->widgets_manager->get_widget_types();
            }
            $raw = get_post_meta($post_id, '_elementor_data', \true);
            $elements = \is_string($raw) && '' !== $raw ? \json_decode($raw, \true) : $raw;
            if (\is_array($elements) && !empty($elements) && $elements !== $base_elements) {
                try {
                    $elements = $this->filter_element(['id' => '', 'settings' => [], 'elements' => $elements])['elements'];
                } catch (\Throwable $e) {
                    $elements = $base_elements;
                }
                $json = wp_json_encode($elements);
                if (\false === $json) {
                    $json = wp_json_encode($base_elements);
                }
                update_metadata('post', $post_id, '_elementor_data', wp_slash(\false === $json ? '[]' : $json));
                delete_post_meta($post_id, '_elementor_element_cache');
                delete_post_meta($post_id, '_elementor_css');
            }
            $page = get_post_meta($post_id, '_elementor_page_settings', \true);
            $page = \is_array($page) ? $page : [];
            if ($page !== $base_page) {
                $page = empty($page) ? [] : $this->filter_settings('', $page, '');
                if (empty($page)) {
                    delete_post_meta($post_id, '_elementor_page_settings');
                } else {
                    update_metadata('post', $post_id, '_elementor_page_settings', wp_slash($page));
                }
                delete_post_meta($post_id, '_elementor_element_cache');
                delete_post_meta($post_id, '_elementor_css');
            }
        } finally {
            $this->doing_save = \false;
        }
    }
    public function __construct()
    {
        add_filter('elementor/document/save/data', [$this, 'filter_save_data'], 10, 2);
        add_action('wp_restore_post_revision', [$this, 'capture_pre_restore_data'], 1, 2);
        add_action('wp_restore_post_revision', [$this, 'filter_restored_revision'], 20, 2);
    }
}
