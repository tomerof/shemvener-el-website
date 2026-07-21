<?php

// SPDX-FileCopyrightText: 2018-2026 Ovation S.r.l. <help@dynamic.ooo>
// SPDX-License-Identifier: GPL-3.0-or-later
namespace DynamicContentForElementor\Modules\DynamicTags\Tags;

use Elementor\Core\DynamicTags\Data_Tag;
use Elementor\Controls_Manager;
use DynamicContentForElementor\CryptocurrencyApiError;
if (!\defined('ABSPATH')) {
    exit;
    // Exit if accessed directly
}
class Cryptocurrency extends Data_Tag
{
    public function get_name()
    {
        return 'dce-cryptocurrency';
    }
    public function get_title()
    {
        return esc_html__('Cryptocurrency', 'dynamic-content-for-elementor');
    }
    public function get_group()
    {
        return 'dce';
    }
    public function get_categories()
    {
        return ['base', 'text', 'number'];
    }
    public function get_single_quote()
    {
        $coin_id = $this->get_settings('coin_id');
        $convert_id = $this->get_settings('convert_id');
        $cache_age = $this->get_settings('cache_age');
        $precision = $this->get_settings('precision');
        $should_format = $this->get_settings('should_format');
        $sign = $this->get_settings('sign');
        $data = $this->get_settings('data');
        $crypto = \DynamicContentForElementor\Plugin::instance()->cryptocurrency;
        try {
            $response = $crypto->get_coin_quote($coin_id, $convert_id, $cache_age);
        } catch (CryptocurrencyApiError $e) {
            if (\Elementor\Plugin::$instance->editor->is_edit_mode()) {
                return $e->getMessage();
            }
            // phpcs:ignore WordPress.PHP.DevelopmentFunctions
            \error_log($e->getMessage());
            return 'NA';
        }
        $req_data = $response[$data] ?? 'NA';
        if ($should_format === 'yes') {
            $formatted = \number_format_i18n($req_data, $precision);
            if ('yes' === $sign) {
                if ($data === 'price' || $data === 'market_cap') {
                    return $crypto->get_sign($convert_id) . $formatted;
                } else {
                    return $formatted . '%';
                }
            }
        } else {
            $formatted = \round($req_data, $precision);
        }
        return $formatted;
    }
    public function get_price_history()
    {
        $coin_id = $this->get_settings('coin_id');
        $convert_id = $this->get_settings('convert_id');
        $cache_age = $this->get_settings('cache_age');
        $count = $this->get_settings('history_count');
        $interval = $this->get_settings('history_interval');
        $crypto = \DynamicContentForElementor\Plugin::instance()->cryptocurrency;
        try {
            $response = $crypto->get_coin_historical_quote($coin_id, $convert_id, $count, $interval, $cache_age);
        } catch (CryptocurrencyApiError $e) {
            if (\Elementor\Plugin::$instance->editor->is_edit_mode()) {
                return $e->getMessage();
            }
            // phpcs:ignore WordPress.PHP.DevelopmentFunctions
            \error_log($e->getMessage());
            return 'NA';
        }
        $csv = '';
        foreach ($response as $quote) {
            $quote = $quote['quote'][$convert_id];
            $timestamp = \strtotime($quote['timestamp']);
            $price = $quote['price'];
            $csv .= "{$timestamp},{$price}\n";
        }
        return $csv;
    }
    public function get_value(array $options = [])
    {
        $crypto = \DynamicContentForElementor\Plugin::instance()->cryptocurrency;
        // Return early if no API key
        if (!$crypto->has_api_key()) {
            return '';
        }
        $data = $this->get_settings('data');
        if ($data === 'price_history') {
            return $this->get_price_history();
        }
        return $this->get_single_quote();
    }
    protected function register_controls()
    {
        $crypto = \DynamicContentForElementor\Plugin::instance()->cryptocurrency;
        if (!$crypto->has_api_key()) {
            $this->add_control('api_key_required', ['type' => Controls_Manager::NOTICE, 'notice_type' => 'danger', 'content' => \sprintf(esc_html__('In order to use this feature you should set %1$sCoinMarketCap API key%2$s on Integrations section', 'dynamic-content-for-elementor'), '<a href="' . admin_url('admin.php?page=dce-integrations') . '" target="_blank">', '</a>')]);
            return;
            // Stop here - no other controls if no API key
        }
        $this->add_control('coin_id', ['label' => esc_html__('Coin', 'dynamic-content-for-elementor'), 'type' => 'ooo_query', 'placeholder' => esc_html__('Select cryptocurrency...', 'dynamic-content-for-elementor'), 'label_block' => \true, 'query_type' => 'cryptocurrency_coins', 'default' => 1]);
        $this->add_control('convert_id', ['label' => esc_html__('Convert to', 'dynamic-content-for-elementor'), 'type' => 'ooo_query', 'placeholder' => esc_html__('Select currency...', 'dynamic-content-for-elementor'), 'label_block' => \true, 'query_type' => 'cryptocurrency_convert', 'default' => 2781]);
        $this->add_control('data', ['label' => esc_html__('Get', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'options' => ['price' => esc_html__('Latest Price', 'dynamic-content-for-elementor'), 'percent_change_1h' => esc_html__('Percent Change 1 Hour', 'dynamic-content-for-elementor'), 'percent_change_24h' => esc_html__('Percent Change 24 Hour', 'dynamic-content-for-elementor'), 'percent_change_7d' => esc_html__('Percent Change 7 Days', 'dynamic-content-for-elementor'), 'percent_change_30d' => esc_html__('Percent Change 30 Days', 'dynamic-content-for-elementor'), 'market_cap' => esc_html__('Market Cap', 'dynamic-content-for-elementor'), 'price_history' => esc_html__('Price History', 'dynamic-content-for-elementor')], 'default' => 'price']);
        $this->add_control('cache_age', ['label' => esc_html__('Store in cache for', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'options' => $crypto->get_cache_age_options(), 'default' => '5m', 'label_block' => \true]);
        $this->add_control('precision', ['label' => esc_html__('Decimal Precision', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::NUMBER, 'default' => '2', 'condition' => ['data!' => 'price_history']]);
        $this->add_control('should_format', ['label' => esc_html__('Format Number', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'condition' => ['data!' => 'price_history']]);
        $this->add_control('sign', ['label' => esc_html__('Include Sign', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'condition' => ['should_format' => 'yes', 'data!' => 'price_history']]);
        $options = ['yearly' => esc_html__('yearly', 'dynamic-content-for-elementor'), 'monthly' => esc_html__('monthly', 'dynamic-content-for-elementor'), 'weekly' => esc_html__('weekly', 'dynamic-content-for-elementor'), 'daily' => esc_html__('daily', 'dynamic-content-for-elementor'), 'hourly' => esc_html__('hourly', 'dynamic-content-for-elementor'), '5m' => esc_html__('5 minutes', 'dynamic-content-for-elementor'), '10m' => esc_html__('10 minutes', 'dynamic-content-for-elementor'), '15m' => esc_html__('15 minutes', 'dynamic-content-for-elementor'), '30m' => esc_html__('30 minutes', 'dynamic-content-for-elementor'), '45m' => esc_html__('45 minutes', 'dynamic-content-for-elementor'), '1h' => esc_html__('1 hour', 'dynamic-content-for-elementor'), '2h' => esc_html__('2 hours', 'dynamic-content-for-elementor'), '3h' => esc_html__('3 hours', 'dynamic-content-for-elementor'), '4h' => esc_html__('4 hours', 'dynamic-content-for-elementor'), '6h' => esc_html__('6 hours', 'dynamic-content-for-elementor'), '12h' => esc_html__('12 hours', 'dynamic-content-for-elementor'), '24h' => esc_html__('24 hours', 'dynamic-content-for-elementor'), '1d' => esc_html__('1 day', 'dynamic-content-for-elementor'), '2d' => esc_html__('2 days', 'dynamic-content-for-elementor'), '3d' => esc_html__('3 days', 'dynamic-content-for-elementor'), '7d' => esc_html__('7 days', 'dynamic-content-for-elementor'), '14d' => esc_html__('14 days', 'dynamic-content-for-elementor'), '15d' => esc_html__('15 days', 'dynamic-content-for-elementor'), '30d' => esc_html__('30 days', 'dynamic-content-for-elementor'), '60d' => esc_html__('60 days', 'dynamic-content-for-elementor'), '90d' => esc_html__('90 days', 'dynamic-content-for-elementor'), '365d' => esc_html__('365 days', 'dynamic-content-for-elementor')];
        $this->add_control('history_interval', ['label' => esc_html__('History Interval', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'default' => 'hourly', 'options' => $options, 'condition' => ['data' => 'price_history']]);
        $this->add_control('history_count', ['label' => esc_html__('History Count', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::NUMBER, 'default' => 10, 'min' => 1, 'max' => 10000, 'condition' => ['data' => 'price_history']]);
    }
}
