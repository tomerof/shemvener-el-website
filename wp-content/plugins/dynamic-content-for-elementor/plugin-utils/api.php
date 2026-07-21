<?php

namespace DynamicOOO\PluginUtils;

if (!\defined('ABSPATH')) {
    exit;
    // early exit.
}
/**
 * API Client for Dynamic.ooo License Server
 *
 * Handles all communications with the Dynamic.ooo license server.
 * Main API calls are:
 *
 * 1. Plugin Info (ENDPOINT_INFO):
 *    - Endpoint: info.php
 *    - Required params: version, license, domain, beta
 *    - Used for: plugin information and updates
 *
 * 2. Version Management (ENDPOINT_VERSIONS):
 *    - Endpoint: versions.php
 *    - Available actions:
 *      a) list: get available versions
 *      b) download: download specific version
 *    - Required params: item_name, version, license, domain, action
 *
 * Standard Response Format:
 * - All responses are in JSON format
 * - On error: WP_Error with code and message
 * - On success: array with requested data
 */
class Api
{
    /**
     * Standard API endpoints
     *
     * @var string ENDPOINT_INFO     Endpoint for plugin information and updates
     * @var string ENDPOINT_VERSIONS Endpoint for version management and downloads
     * @var string ENDPOINT_LICENSE  Endpoint for license management check
     */
    const ENDPOINT_INFO = 'info.php';
    const ENDPOINT_VERSIONS = 'versions.php';
    const ENDPOINT_LICENSE = 'api.php';
    /**
     * Standard API actions
     *
     * @var string ACTION_LIST      Action to list available versions
     * @var string ACTION_DOWNLOAD  Action to download specific version
     * @var string ACTION_ACTIVATE  Action to activate license
     * @var string ACTION_DEACTIVATE Action to deactivate license
     * @var string ACTION_STATUS    Action to check license status
     */
    const ACTION_LIST = 'list';
    // List available versions
    const ACTION_DOWNLOAD = 'download';
    // Download specific version
    const ACTION_ACTIVATE = 'activate';
    const ACTION_DEACTIVATE = 'deactivate';
    const ACTION_STATUS = 'status-check';
    /**
     * Standard API parameters
     *
     * @var string PARAM_PLUGIN_VERSION    Plugin version
     * @var string PARAM_LICENSE_KEY    License key
     * @var string PARAM_DOMAIN     Site domain
     * @var string PARAM_ITEM       Plugin name
     * @var string PARAM_ACTION     Requested action
     * @var string PARAM_BETA       Beta version flag
     * @var string PARAM_WOO_ACTION WooCommerce action
     * @var string PARAM_PRODUCT_ID Product unique ID
     * @var string PARAM_API_VERSION API version
     * @var string PARAM_WP_VERSION WordPress version
     * @var string PARAM_MULTISITE Is multisite
     * @var string PARAM_PHP_VERSION PHP version
     */
    const PARAM_PLUGIN_VERSION = 'version';
    const PARAM_LICENSE_KEY = 'licence_key';
    const PARAM_DOMAIN = 'domain';
    const PARAM_ITEM = 'item_name';
    const PARAM_ACTION = 'action';
    const PARAM_BETA = 'beta';
    const PARAM_WOO_ACTION = 'woo_sl_action';
    const PARAM_PRODUCT_ID = 'product_unique_id';
    const PARAM_API_VERSION = 'api_version';
    const PARAM_WP_VERSION = 'wp-version';
    const PARAM_MULTISITE = 'is_multisite';
    const PARAM_PHP_VERSION = 'php';
    /**
     * @var Manager
     */
    protected $plugin_utils_manager;
    /**
     * @var array<string,string> Default request headers
     */
    protected $default_headers;
    /**
     * @var int Request timeout in seconds
     */
    protected $timeout = 10;
    /**
     * @param Manager $plugin_utils_manager
     */
    public function __construct($plugin_utils_manager)
    {
        $this->plugin_utils_manager = $plugin_utils_manager;
        $this->default_headers = ['Accept' => 'application/json'];
    }
    /**
     * Set request timeout
     *
     * @param int $seconds Timeout in seconds
     * @return void
     */
    public function set_timeout($seconds)
    {
        $this->timeout = $seconds;
    }
    /**
     * Add custom header
     *
     * @param string $key Header key
     * @param string $value Header value
     * @return void
     */
    public function add_header($key, $value)
    {
        $this->default_headers[$key] = $value;
    }
    /**
     * Handle API response
     *
     * @param array<mixed>|\WP_Error $response WordPress HTTP API response
     * @return array<mixed>|\WP_Error Processed response data or WP_Error
     */
    protected function handle_response($response)
    {
        if (is_wp_error($response)) {
            return $response;
        }
        $body = wp_remote_retrieve_body($response);
        $code = wp_remote_retrieve_response_code($response);
        if ($code >= 400) {
            return new \WP_Error('api_error', \sprintf('API request failed with code %d', $code), ['status' => $code, 'body' => $body]);
        }
        $data = \json_decode($body, \true);
        if (!\is_array($data)) {
            return new \WP_Error('api_error', esc_html__('Invalid JSON response from server: the response is not an array', 'dynamic-ooo'), ['body' => $body]);
        }
        return $data;
    }
    /**
     * Make a GET request
     *
     * @param string $endpoint API endpoint
     * @param array<string,string|bool|int> $params Query parameters
     * @return array<mixed>|\WP_Error Response data or WP_Error on failure
     */
    public function get($endpoint, $params = [])
    {
        $url = $this->build_url($endpoint, $params);
        $response = wp_remote_get($url, ['headers' => $this->default_headers, 'timeout' => $this->timeout]);
        return $this->handle_response($response);
    }
    /**
     * Make a POST request
     *
     * @param string $endpoint API endpoint
     * @param array<string,string|bool|int> $data Post data
     * @return array<mixed>|\WP_Error Response data or WP_Error on failure
     */
    public function post($endpoint, $data = [])
    {
        $url = $this->build_url($endpoint);
        $response = wp_remote_post($url, ['headers' => $this->default_headers, 'body' => wp_json_encode($data), 'timeout' => $this->timeout]);
        return $this->handle_response($response);
    }
    /**
     * Build full URL from endpoint and parameters
     *
     * @param string $endpoint API endpoint
     * @param array<string,string|bool|int> $params Query parameters
     * @return string
     */
    protected function build_url($endpoint, $params = [])
    {
        $url = trailingslashit($this->plugin_utils_manager->get_config('license_url')) . \ltrim($endpoint, '/');
        if (!empty($params)) {
            $url = add_query_arg($params, $url);
        }
        return $url;
    }
    /**
     * Get list of available versions
     *
     * @return array<string>|\WP_Error
     */
    public function get_available_versions()
    {
        /** @var array<string>|\WP_Error $versions */
        $versions = $this->get(self::ENDPOINT_VERSIONS, [self::PARAM_ITEM => $this->plugin_utils_manager->get_config('plugin_slug'), self::PARAM_LICENSE_KEY => $this->plugin_utils_manager->license->get_license_key(), self::PARAM_DOMAIN => $this->plugin_utils_manager->license->get_current_domain(), self::PARAM_ACTION => self::ACTION_LIST]);
        return $versions;
    }
    /**
     * Get package URL for a specific version
     *
     * @param string $version Version to get package for
     * @return string|\WP_Error
     */
    public function get_package_url($version)
    {
        /** @var array<string,string>|\WP_Error $response */
        $response = $this->get(self::ENDPOINT_VERSIONS, [self::PARAM_ITEM => $this->plugin_utils_manager->get_config('plugin_slug'), self::PARAM_PLUGIN_VERSION => $version, self::PARAM_LICENSE_KEY => $this->plugin_utils_manager->license->get_license_key(), self::PARAM_DOMAIN => $this->plugin_utils_manager->license->get_current_domain(), self::PARAM_ACTION => self::ACTION_DOWNLOAD]);
        if (is_wp_error($response)) {
            return $response;
        }
        if (empty($response['package_url'])) {
            return new \WP_Error('api_error', esc_html__('Package URL not found in response', 'dynamic-ooo'));
        }
        return $response['package_url'];
    }
}
