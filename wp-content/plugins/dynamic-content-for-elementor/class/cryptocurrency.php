<?php

// SPDX-FileCopyrightText: 2018-2026 Ovation S.r.l. <help@dynamic.ooo>
// SPDX-License-Identifier: GPL-3.0-or-later
namespace DynamicContentForElementor;

if (!\defined('ABSPATH')) {
    exit;
}
class CryptocurrencyApiError extends \Error
{
}
class Cryptocurrency
{
    /**
     * @var string|false
     */
    private $api_key;
    /**
     * @var bool|array<string,mixed>
     */
    private $fiat_and_crypto_info = \false;
    /**
     * @var array<string,int>
     */
    const CACHE_MAX_AGES = ['1m' => 60, '5m' => 60 * 5, '15m' => 60 * 15, '1h' => 60 * 60];
    /**
     * @return void
     */
    public function __construct()
    {
        $this->api_key = get_option('dce_coinmarketcap_key');
    }
    /**
     * Check if API key is configured
     *
     * @return bool
     */
    public function has_api_key()
    {
        return !empty($this->api_key);
    }
    /**
     * @return string
     */
    public function get_api_key()
    {
        return $this->api_key ?: '';
    }
    /**
     * @return string
     */
    public function get_transient_prefix()
    {
        return 'dce_crypto_';
    }
    /**
     * @param string $endpoint
     * @param array<mixed> $parameters
     * @return array<mixed>
     */
    public function api_request($endpoint, $parameters = [])
    {
        if (!$this->has_api_key()) {
            return [];
        }
        $url = 'https://pro-api.coinmarketcap.com/v1' . $endpoint;
        $args = ['headers' => ['Accepts' => 'application/json', 'X-CMC_PRO_API_KEY' => $this->get_api_key()], 'timeout' => 15];
        $request_url = add_query_arg($parameters, $url);
        $response = wp_remote_get($request_url, $args);
        if (is_wp_error($response)) {
            throw new \DynamicContentForElementor\CryptocurrencyApiError('Coinmarketcap API Connection Error: ' . esc_html($response->get_error_message()));
        }
        $response_code = wp_remote_retrieve_response_code($response);
        if ($response_code !== 200) {
            throw new \DynamicContentForElementor\CryptocurrencyApiError(\sprintf('Coinmarketcap API Error: HTTP Status %d', $response_code));
        }
        $body = wp_remote_retrieve_body($response);
        $data = \json_decode($body, \true);
        if (($data['status']['error_code'] ?? 9999) === 0 && \is_array($data['data'])) {
            return $data['data'];
        }
        throw new \DynamicContentForElementor\CryptocurrencyApiError(esc_html($data['status']['error_message'] ?? 'Coinmarketcap API Connection Error'));
    }
    /**
     * @param string $coin_id
     * @param string $convert_id
     * @param string $max_age
     * @return array<mixed>
     */
    public function get_coin_quote($coin_id, $convert_id, $max_age = '5m')
    {
        $transient_key = $this->get_transient_prefix() . "quote_{$max_age}_{$coin_id}_{$convert_id}";
        $transient = get_transient($transient_key);
        if ($transient !== \false && \is_string($transient)) {
            $decoded = \json_decode($transient, \true);
            if ($decoded !== null) {
                return $decoded;
            }
        }
        $data = $this->api_request('/cryptocurrency/quotes/latest', ['id' => $coin_id, 'convert_id' => $convert_id]);
        if (empty($data) || !isset($data[$coin_id]['quote'][$convert_id])) {
            return [];
        }
        $quote = $data[$coin_id]['quote'][$convert_id];
        set_transient($transient_key, wp_json_encode($quote), self::CACHE_MAX_AGES[$max_age]);
        return $quote;
    }
    /**
     * @param string $coin_id
     * @param string $convert_id
     * @param int $count
     * @param string $interval
     * @param string $max_age
     * @return array<mixed>
     */
    public function get_coin_historical_quote($coin_id, $convert_id, $count, $interval, $max_age = '5m')
    {
        $transient_key = $this->get_transient_prefix() . "historical_quote_{$max_age}_{$count}_{$interval}_{$coin_id}_{$convert_id}";
        $transient = get_transient($transient_key);
        if ($transient !== \false && \is_string($transient)) {
            $decoded = \json_decode($transient, \true);
            if ($decoded !== null) {
                return $decoded;
            }
        }
        $data = $this->api_request('/cryptocurrency/quotes/historical', ['id' => $coin_id, 'convert_id' => $convert_id, 'count' => $count, 'interval' => $interval]);
        if (empty($data) || !isset($data[$coin_id]['quotes'])) {
            return [];
        }
        $quotes = $data[$coin_id]['quotes'];
        set_transient($transient_key, wp_json_encode($quotes), self::CACHE_MAX_AGES[$max_age]);
        return $quotes;
    }
    /**
     * @return array<string,string>
     */
    public function get_cache_age_options()
    {
        return ['1m' => esc_html__('1 Minute', 'dynamic-content-for-elementor'), '5m' => esc_html__('5 Minutes', 'dynamic-content-for-elementor'), '15m' => esc_html__('15 Minutes', 'dynamic-content-for-elementor'), '1h' => esc_html__('1 Hour', 'dynamic-content-for-elementor')];
    }
    /**
     * @param string $id
     * @return string
     */
    public function get_coin_logo($id)
    {
        $transient_key = $this->get_transient_prefix() . 'logo_' . $id;
        $transient = get_transient($transient_key);
        if ($transient !== \false && \is_string($transient)) {
            return $transient;
        }
        $info = $this->api_request('/cryptocurrency/info', ['id' => $id]);
        if (empty($info) || !isset($info[$id]['logo'])) {
            return '';
        }
        $logo = $info[$id]['logo'];
        set_transient($transient_key, $logo, DAY_IN_SECONDS);
        return $logo;
    }
    /**
     * @return array<string,string>
     */
    public function get_convert_options()
    {
        return $this->get_fiat_options() + $this->get_coins_options();
    }
    /**
     * @return array<string,string>
     */
    public function get_fiat_options()
    {
        $fiat = $this->get_fiat();
        $list = [];
        foreach ($fiat as $f) {
            $list[$f['id']] = "{$f['name']} ({$f['symbol']})";
        }
        return $list;
    }
    /**
     * @return array<string,string>
     */
    public function get_coins_options()
    {
        $coins = $this->get_available_coins();
        $list = [];
        foreach ($coins as $c) {
            $list[$c['id']] = "{$c['name']} ({$c['symbol']})";
        }
        return $list;
    }
    /**
     * @param string $coin_id
     * @return array<string,mixed>|false
     */
    public function get_crypto_info($coin_id)
    {
        return $this->get_fiat_and_crypto_info()[$coin_id] ?? \false;
    }
    /**
     * @param string|bool $coin_id
     * @return array<string,mixed>|bool
     */
    public function get_fiat_and_crypto_info($coin_id = \false)
    {
        if ($this->fiat_and_crypto_info === \false) {
            $all = [];
            $fiat = $this->get_fiat();
            $coins = $this->get_available_coins();
            foreach ($fiat as $f) {
                $all[$f['id']] = $f;
            }
            foreach ($coins as $c) {
                $all[$c['id']] = $c;
            }
            $this->fiat_and_crypto_info = $all;
        }
        if ($coin_id === \false) {
            return $this->fiat_and_crypto_info;
        } else {
            return $this->fiat_and_crypto_info[$coin_id] ?? \false;
        }
    }
    /**
     * @param string $id
     * @return string
     */
    public function get_sign($id)
    {
        $all = $this->get_fiat_and_crypto_info();
        if (isset($all[$id])) {
            if (isset($all[$id]['sign'])) {
                return $all[$id]['sign'];
            }
            return $all[$id]['symbol'] . ' ';
        }
        return '';
    }
    /**
     * @return array<string,mixed>
     */
    public function get_fiat()
    {
        $transient_key = $this->get_transient_prefix() . 'fiat_map';
        $transient = get_transient($transient_key);
        if ($transient !== \false && \is_string($transient)) {
            $decoded = \json_decode($transient, \true);
            if ($decoded !== null) {
                return $decoded;
            }
        }
        $fiat = $this->api_request('/fiat/map');
        if (empty($fiat)) {
            return [];
        }
        set_transient($transient_key, wp_json_encode($fiat), DAY_IN_SECONDS);
        return $fiat;
    }
    /**
     * @return array<string,mixed>
     */
    public function get_available_coins()
    {
        $transient_key = $this->get_transient_prefix() . 'crypto_map';
        $transient = get_transient($transient_key);
        if ($transient !== \false && \is_string($transient)) {
            $decoded = \json_decode($transient, \true);
            if ($decoded !== null) {
                return $decoded;
            }
        }
        $coins = $this->api_request('/cryptocurrency/map', []);
        if (empty($coins)) {
            return [];
        }
        set_transient($transient_key, wp_json_encode($coins), DAY_IN_SECONDS);
        return $coins;
    }
}
