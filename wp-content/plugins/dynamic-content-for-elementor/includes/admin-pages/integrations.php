<?php

// SPDX-FileCopyrightText: 2018-2026 Ovation S.r.l. <help@dynamic.ooo>
// SPDX-License-Identifier: GPL-3.0-or-later
namespace DynamicContentForElementor\AdminPages;

use DynamicContentForElementor\Helper;
use DynamicOOO\PluginUtils\AdminPages\Pages\Integrations as BaseIntegrations;
class Integrations extends BaseIntegrations
{
    protected function get_config()
    {
        $fields = [];
        $sections = [];
        // Google Maps
        $sections['google_maps'] = ['name' => __('Google Maps', 'dynamic-content-for-elementor'), 'description' => __('Enter your Google Maps API key to enable map features.', 'dynamic-content-for-elementor')];
        $fields['google_maps_api'] = ['label' => __('Google Maps JavaScript API Key', 'dynamic-content-for-elementor'), 'type' => 'text', 'section' => 'section_google_maps'];
        // Add ACF field if ACF is active
        if (Helper::is_plugin_active('acf') || Helper::is_plugin_active('acf-pro')) {
            $fields['google_maps_api_acf'] = ['label' => __('ACF', 'dynamic-content-for-elementor'), 'type' => 'checkbox', 'section' => 'section_google_maps', 'checkbox_label' => __('Set this API also in ACF Configuration', 'dynamic-content-for-elementor')];
        }
        // PayPal
        $sections['paypal'] = ['name' => __('PayPal', 'dynamic-content-for-elementor'), 'description' => __('Configure your PayPal credentials for handling payments.', 'dynamic-content-for-elementor')];
        $fields['paypal_api_mode'] = ['label' => __('PayPal Mode', 'dynamic-content-for-elementor'), 'type' => 'select', 'options' => ['sandbox' => 'Sandbox', 'live' => 'Live'], 'section' => 'section_paypal'];
        $fields['paypal_api_currency'] = ['label' => __('PayPal Currency Code', 'dynamic-content-for-elementor'), 'type' => 'text', 'section' => 'section_paypal'];
        $fields['paypal_sandbox_group'] = ['type' => 'group', 'section' => 'section_paypal', 'label' => __('PayPal Sandbox Credentials', 'dynamic-content-for-elementor'), 'fields' => ['paypal_api_client_id_sandbox' => ['label' => __('Sandbox Client ID', 'dynamic-content-for-elementor'), 'type' => 'text'], 'paypal_api_client_secret_sandbox' => ['label' => __('Sandbox Client Secret', 'dynamic-content-for-elementor'), 'type' => 'password']]];
        $fields['paypal_live_group'] = ['type' => 'group', 'section' => 'section_paypal', 'label' => __('PayPal Live Credentials', 'dynamic-content-for-elementor'), 'fields' => ['paypal_api_client_id_live' => ['label' => __('Live Client ID', 'dynamic-content-for-elementor'), 'type' => 'text'], 'paypal_api_client_secret_live' => ['label' => __('Live Client Secret', 'dynamic-content-for-elementor'), 'type' => 'password']]];
        // Stripe
        $sections['stripe'] = ['name' => __('Stripe', 'dynamic-content-for-elementor'), 'description' => __('Set your Stripe keys for secure online payments.', 'dynamic-content-for-elementor')];
        $fields['stripe_api_mode'] = ['label' => __('Stripe Mode', 'dynamic-content-for-elementor'), 'type' => 'select', 'options' => ['test' => 'Test', 'live' => 'Live'], 'section' => 'section_stripe'];
        $fields['stripe_test_group'] = ['type' => 'group', 'section' => 'section_stripe', 'label' => __('Stripe Test Keys', 'dynamic-content-for-elementor'), 'fields' => ['stripe_api_publishable_key_test' => ['label' => __('Test Publishable Key', 'dynamic-content-for-elementor'), 'type' => 'text'], 'stripe_api_secret_key_test' => ['label' => __('Test Secret Key', 'dynamic-content-for-elementor'), 'type' => 'password']]];
        $fields['stripe_live_group'] = ['type' => 'group', 'section' => 'section_stripe', 'label' => __('Stripe Live Keys', 'dynamic-content-for-elementor'), 'fields' => ['stripe_api_publishable_key_live' => ['label' => __('Live Publishable Key', 'dynamic-content-for-elementor'), 'type' => 'text'], 'stripe_api_secret_key_live' => ['label' => __('Live Secret Key', 'dynamic-content-for-elementor'), 'type' => 'password']]];
        // Coinmarketcap
        $sections['coinmarketcap'] = ['name' => __('Coinmarketcap', 'dynamic-content-for-elementor'), 'description' => __('Provide your Coinmarketcap API key to fetch cryptocurrency data.', 'dynamic-content-for-elementor')];
        $fields['coinmarketcap_key'] = ['label' => __('Coinmarketcap API Key', 'dynamic-content-for-elementor'), 'type' => 'text', 'section' => 'section_coinmarketcap'];
        return ['fields' => $fields, 'sections' => $sections];
    }
}
