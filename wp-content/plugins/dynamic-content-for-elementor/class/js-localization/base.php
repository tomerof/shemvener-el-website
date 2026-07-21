<?php

// SPDX-FileCopyrightText: 2018-2026 Ovation S.r.l. <help@dynamic.ooo>
// SPDX-License-Identifier: GPL-3.0-or-later
namespace DynamicContentForElementor\JsLocalization;

if (!\defined('ABSPATH')) {
    exit;
}
/**
 * Base class for JavaScript localized strings
 *
 * Extend this class to create localized strings for a JavaScript component.
 * Child classes must define $script_handle, $js_object_name properties
 * and implement the get_strings() method.
 */
abstract class Base
{
    /**
     * Script handle to localize
     *
     * @var string
     */
    protected static string $script_handle;
    /**
     * JavaScript object name for localized data
     *
     * @var string
     */
    protected static string $js_object_name;
    /**
     * Get all localized strings
     *
     * @return array<string,string>
     */
    protected static abstract function get_strings() : array;
    /**
     * Localize the script with translated strings
     *
     * @return void
     */
    public static function localize()
    {
        wp_localize_script(static::$script_handle, static::$js_object_name, static::get_strings());
    }
}
