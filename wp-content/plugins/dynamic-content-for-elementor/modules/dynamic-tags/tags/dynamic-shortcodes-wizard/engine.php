<?php

// SPDX-FileCopyrightText: 2018-2026 Ovation S.r.l. <help@dynamic.ooo>
// SPDX-License-Identifier: GPL-3.0-or-later
namespace DynamicContentForElementor\Modules\DynamicTags\Tags\DynamicShortcodesWizard;

use Elementor\Core\DynamicTags\Tag;
use Elementor\Controls_Manager;
use DynamicContentForElementor\Helper;
use DynamicShortcodes\Core\Shortcodes\Composer;
if (!\defined('ABSPATH')) {
    exit;
    // Exit if accessed directly
}
class Engine
{
    /**
     * @var array<string,mixed>
     */
    protected static $composer = [];
    /**
     * @var array<string,mixed>
     */
    protected static $types = ['acf' => ['label' => 'Advanced Custom Fields', 'plugin_depends' => 'acf', 'fields' => ['required' => \true, 'query_type' => 'acf', 'object_type' => []], 'require_field' => \true, 'from' => ['post' => ['label' => 'Post', 'post_type' => 'any', 'explicit_object' => \false], 'user' => ['label' => 'User', 'explicit_object' => \true], 'term' => ['label' => 'Term', 'explicit_object' => \true], 'option' => ['label' => 'Option', 'explicit_object' => \true]], 'filter' => \true, 'fallback' => \true], 'author' => ['label' => 'Author', 'require_field' => \true, 'from' => ['user' => ['label' => 'Author', 'explicit_object' => \false]], 'filter' => \true, 'fallback' => \true], 'cookie' => ['label' => 'Cookie', 'require_field' => \true, 'filter' => \true, 'fallback' => \true], 'date' => ['label' => 'Date', 'filter' => \false, 'fallback' => \false], 'jet' => ['label' => 'JetEngine', 'plugin_depends' => 'jet-engine', 'require_field' => \true, 'from' => ['post' => ['label' => 'Post', 'post_type' => 'any', 'explicit_object' => \false]], 'filter' => \true, 'fallback' => \true], 'metabox' => ['label' => 'MetaBox', 'plugin_depends' => 'metabox', 'require_field' => \true, 'from' => ['post' => ['label' => 'Post', 'post_type' => 'any', 'explicit_object' => \false]], 'filter' => \true, 'fallback' => \true], 'option' => ['label' => 'Option', 'require_field' => \true, 'filter' => \true, 'fallback' => \true], 'pods' => ['label' => 'Pods', 'plugin_depends' => 'pods', 'require_field' => \true, 'from' => ['post' => ['label' => 'Post', 'post_type' => 'any', 'explicit_object' => \false]], 'filter' => \true, 'fallback' => \true], 'post' => ['label' => 'Post', 'require_field' => \true, 'from' => ['post' => ['label' => 'Post', 'post_type' => 'any', 'explicit_object' => \false]], 'filter' => \true, 'fallback' => \true], 'term' => ['label' => 'Term', 'require_field' => \true, 'from' => ['term' => ['label' => 'Term', 'explicit_object' => \false]], 'filter' => \true, 'fallback' => \true], 'toolset' => ['label' => 'Toolset', 'plugin_depends' => 'toolset', 'require_field' => \true, 'from' => ['post' => ['label' => 'Post', 'post_type' => 'any', 'explicit_object' => \false]], 'filter' => \true, 'fallback' => \true], 'user' => ['label' => 'User', 'require_field' => \true, 'from' => ['user' => ['label' => 'User', 'explicit_object' => \false]], 'filter' => \true, 'fallback' => \true], 'woo' => ['label' => 'Woo', 'plugin_depends' => 'woocommerce', 'require_field' => \true, 'from' => ['post' => ['label' => 'Product', 'post_type' => 'product', 'explicit_object' => \false]], 'filter' => \true, 'fallback' => \true]];
    /**
     * @return array<string,mixed>
     */
    public static function get_types()
    {
        return static::$types;
    }
    /**
     * @param array<mixed> $settings
     * @return array<string,mixed>
     */
    public static function process_settings($settings)
    {
        $type = $settings['dsh_type'];
        $types = static::$types;
        static::add_type($type);
        if ('date' === $type) {
            $date_modificator = $settings['date_modificator'];
            if ('custom' === $settings['date_modificator']) {
                $date_modificator = $settings['date_modificator_custom'];
            }
            static::add_arg($date_modificator);
            static::format_date($settings['date_format'], $settings['date_format_custom']);
        } elseif (\true === ($types[$type]['require_field'] ?? \false)) {
            if (empty($settings[$type . '_field'])) {
                return ['error' => 'Missing required field for ' . $type];
            }
            static::add_arg($settings[$type . '_field']);
        }
        if (!empty($types[$type]['from'])) {
            $parts = \explode('_', $settings[$type . '_from']);
            $current_object = \end($parts);
            if (\strpos($settings[$type . '_from'], 'another') === 0) {
                if (empty($settings[$type . '_source_' . $current_object])) {
                    return ['error' => 'Missing source ID for ' . $type];
                }
                static::add_id($settings[$type . '_source_' . $current_object]);
            }
            if (\true === ($types[$type]['from'][$current_object]['explicit_object'] ?? \false)) {
                // Add @type only if the type is not the same as the current object and !== 'post'
                static::add_keyarg_key($current_object);
            }
        }
        // Filter
        if (!empty($settings['use_filter']) && \true === $types[$type]['filter']) {
            switch ($settings['filter_type']) {
                case 'pipe_first':
                    if (empty($settings['function_name'])) {
                        return ['error' => 'You have selected a filter but have not entered the function'];
                    }
                    static::add_filter('|', $settings['function_name']);
                    break;
                case 'array_access':
                    if ('' === $settings['array_key']) {
                        return ['error' => 'You tried to access an array element without providing the corresponding key.'];
                    }
                    static::add_filter('||', $settings['array_key']);
                    break;
            }
        }
        // Fallback
        if (!empty($settings['fallback'])) {
            static::add_fallback($settings['fallback']);
        }
        // Format as date
        if (!empty($settings['format_as_date'])) {
            static::$composer = ['type' => 'date', 'args' => [['type' => 'shortcode', 'data' => static::$composer]]];
            static::format_date($settings['date_format'], $settings['date_format_custom']);
        }
        return ['result' => Composer::compose([static::$composer])];
    }
    /**
     * @param string $type
     * @return void
     */
    protected static function add_type($type)
    {
        static::$composer['type'] = $type;
    }
    /**
     * @param string|int $argument
     * @return void
     */
    protected static function add_arg($argument)
    {
        static::$composer['args'][] = $argument;
    }
    /**
     * @param string|int $key
     * @param string|int $argument
     * @return void
     */
    protected static function add_keyarg($key, $argument)
    {
        static::$composer['keyargs'][$key] = $argument;
    }
    /**
     * @param string|int $key
     * @return void
     */
    protected static function add_keyarg_key($key)
    {
        static::$composer['keyargs'][$key] = null;
    }
    /**
     * @param string $type
     * @param string|int $name
     * @return void
     */
    protected static function add_filter($type, $name)
    {
        if (0 !== $name && '0' !== $name && empty($name)) {
            return;
        }
        static::$composer['filters'] = [['type' => $type, 'name' => $name]];
    }
    /**
     * @param string $fallback
     * @return void
     */
    protected static function add_fallback($fallback)
    {
        static::$composer['fallback'] = $fallback;
    }
    /**
     * @param string|int $argument
     * @return void
     */
    protected static function add_id($argument)
    {
        static::add_keyarg('ID', \intval($argument));
    }
    /**
     * @param string $format
     * @param string $custom
     * @return void
     */
    protected static function format_date($format, $custom)
    {
        $date_format = 'custom' === $format ? $custom : $format;
        static::add_keyarg('format', $date_format);
    }
}
