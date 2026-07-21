<?php

// SPDX-FileCopyrightText: 2018-2026 Ovation S.r.l. <help@dynamic.ooo>
// SPDX-License-Identifier: GPL-3.0-or-later
namespace DynamicContentForElementor\TextTemplates\Timber;

use DynamicContentForElementor\Helper;
class Manager
{
    /**
     * @param string $template
     * @param array<string,mixed> $var_bindings
     * @return string|false
     */
    public function expand($template, $var_bindings)
    {
        if (!Helper::is_plugin_active('timber')) {
            return $template;
        }
        // We don't need timber templates inside files, and they can cause
        // permission problems, so remove them:
        $fixpaths = function ($paths) {
            return [];
        };
        add_filter('timber/loader/paths', $fixpaths);
        $context = \Timber\Timber::get_context();
        $context += $var_bindings;
        $context['post'] = new \Timber\Post();
        $context['current_user'] = new \Timber\User();
        \ob_start();
        \Timber\Timber::render_string($template, $context);
        remove_filter('timber/loader/paths', $fixpaths);
        return \ob_get_clean();
    }
}
