<?php

// SPDX-FileCopyrightText: 2018-2026 Ovation S.r.l. <help@dynamic.ooo>
// SPDX-License-Identifier: GPL-3.0-or-later
namespace DynamicContentForElementor\JsLocalization;

if (!\defined('ABSPATH')) {
    exit;
}
/**
 * I18n Manager
 *
 * Centralizes the registration of localized strings for JavaScript components.
 */
class Manager
{
    /**
     * Localize all registered script strings
     *
     * Call this after scripts have been enqueued to register
     * localized strings for JavaScript components.
     *
     * @return void
     */
    public static function localize_scripts()
    {
        \DynamicContentForElementor\JsLocalization\CustomEditor::localize();
    }
}
