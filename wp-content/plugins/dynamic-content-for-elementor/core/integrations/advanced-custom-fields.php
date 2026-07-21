<?php

// SPDX-FileCopyrightText: 2018-2026 Ovation S.r.l. <help@dynamic.ooo>
// SPDX-License-Identifier: GPL-3.0-or-later
namespace DynamicContentForElementor\Integrations;

use DynamicContentForElementor\Helper;
use DynamicContentForElementor\Plugin;
if (!\defined('ABSPATH')) {
    exit;
    // Exit if accessed directly.
}
class AdvancedCustomFields
{
    /**
     * @var string
     */
    private $google_maps_api;
    public function __construct()
    {
        // ACF Form Head must be called before any output for redirect/message to work.
        // Register hook early, check for ACF in callback to avoid timing issues.
        add_action('template_redirect', [$this, 'maybe_acf_form_head'], 5);
        // Google Maps API integration (only if ACF is active)
        if (Helper::is_plugin_active('acf') || Helper::is_plugin_active('acf-pro')) {
            $this->google_maps_api = get_option(Plugin::instance()->prefix . '_google_maps_api');
            $google_maps_api_acf = get_option(Plugin::instance()->prefix . '_google_maps_api_acf');
            if (!empty($this->google_maps_api) && !empty($google_maps_api_acf)) {
                add_filter('acf/fields/google_map/api', [$this, 'set_google_maps_api']);
            }
        }
    }
    /**
     * Maybe call ACF Form Head
     *
     * Called on template_redirect to process ACF form submissions
     * before any HTML output, enabling redirect and message functionality.
     *
     * @return void
     */
    public function maybe_acf_form_head()
    {
        // phpcs:ignore WordPress.Security.NonceVerification.Missing -- ACF handles nonce verification
        if (!isset($_POST['_acf_form'])) {
            return;
        }
        // Check if ACF is available (function check is more reliable than class check at this point)
        if (!\function_exists('acf_form_head')) {
            return;
        }
        \acf_form_head();
    }
    /**
     * Set Google Maps API key for ACF fields
     *
     * @param array<string,mixed> $api
     * @return array<string,mixed>
     */
    public function set_google_maps_api($api)
    {
        $api['key'] = $this->google_maps_api;
        return $api;
    }
}
