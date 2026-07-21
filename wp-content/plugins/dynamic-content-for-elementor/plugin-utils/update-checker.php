<?php

namespace DynamicOOO\PluginUtils;

use DynamicOOO\PluginUtils\Api;
if (!\defined('ABSPATH')) {
    exit;
    // Exit if accessed directly.
}
class UpdateChecker
{
    /**
     * Plugin configuration manager.
     *
     * @var Manager
     */
    protected $plugin_utils_manager;
    /**
     * Current plugin version.
     *
     * @var string
     */
    public $current_version;
    /**
     * Transient key for caching update information.
     *
     * @var string
     */
    public $transient_key;
    /**
     * Beta flag option name.
     *
     * @var string
     */
    public $beta_flag_option;
    /**
     * @param Manager $plugin_utils_manager
     */
    public function __construct($plugin_utils_manager)
    {
        $this->plugin_utils_manager = $plugin_utils_manager;
        $this->current_version = $this->plugin_utils_manager->get_config('version');
        $this->transient_key = $this->plugin_utils_manager->get_config('plugin_slug') . '_update_checker';
        $this->beta_flag_option = $this->plugin_utils_manager->get_config('prefix') . '_beta';
        add_filter('site_transient_update_plugins', [$this, 'check_update_availability']);
        add_filter('plugins_api', [$this, 'get_plugin_details'], 20, 3);
        add_action('upgrader_process_complete', [$this, 'clear_update_cache'], 10);
        add_filter('plugin_row_meta', [$this, 'add_manual_check_link'], 20, 4);
        add_action('wp_ajax_check_' . $this->plugin_utils_manager->get_config('prefix') . '_updates', [$this, 'process_manual_update_check']);
    }
    /**
     * Retrieves remote update data.
     * Uses transient caching to minimize repeated requests.
     *
     * @return false|array<string,mixed> Associative array of update data, or false on error.
     */
    public function fetch_remote_data()
    {
        $data = get_transient($this->transient_key);
        if (\is_array($data) && isset($data[0])) {
            return $data[0];
        }
        $license = $this->plugin_utils_manager->license;
        $params = [Api::PARAM_DOMAIN => $license->get_current_domain(), Api::PARAM_PLUGIN_VERSION => $this->plugin_utils_manager->get_config('version'), Api::PARAM_LICENSE_KEY => $license->get_license_key(), Api::PARAM_BETA => get_option($this->beta_flag_option, \false) ? 'true' : 'false'];
        $response = $this->plugin_utils_manager->api->get(Api::ENDPOINT_INFO, $params);
        if (is_wp_error($response)) {
            $response = \false;
            set_transient($this->transient_key, [$response], 3 * HOUR_IN_SECONDS);
        } else {
            set_transient($this->transient_key, [$response], 12 * HOUR_IN_SECONDS);
        }
        return $response;
    }
    /**
     * Provides detailed plugin information for the update screen.
     *
     * @param false|object|array<mixed> $result Initial result.
     * @param string $action Action requested (e.g. 'plugin_information').
     * @param object $args Arguments passed.
     * @return false|object|array<mixed> Plugin details.
     */
    public function get_plugin_details($result, $action, $args)
    {
        if ('plugin_information' !== $action) {
            return $result;
        }
        // Check that the request is for this plugin
        if (!isset($args->slug) || $this->plugin_utils_manager->get_config('plugin_base') !== $args->slug) {
            return $result;
        }
        $remote_data = $this->fetch_remote_data();
        if (!$remote_data) {
            return $result;
        }
        /**
         * @var array<string,mixed> $remote_data
         */
        $details = new \stdClass();
        $details->name = $remote_data['name'] ?? '';
        $details->slug = $remote_data['slug'] ?? '';
        $details->version = $remote_data['version'] ?? '';
        $details->tested = $remote_data['tested'] ?? '';
        $details->requires = $remote_data['requires'] ?? '';
        $details->author = $remote_data['author'] ?? '';
        $details->author_profile = $remote_data['author_profile'] ?? '';
        $details->download_link = $remote_data['download_url'] ?? '';
        $details->trunk = $remote_data['download_url'] ?? '';
        $details->requires_php = $remote_data['requires_php'] ?? '';
        $details->last_updated = $remote_data['last_updated'] ?? '';
        // Initialize sections as empty array and populate if data is available
        $details->sections = [];
        if (isset($remote_data['sections']) && \is_array($remote_data['sections'])) {
            $details->sections = ['description' => $remote_data['sections']['description'] ?? '', 'installation' => $remote_data['sections']['installation'] ?? '', 'changelog' => $remote_data['sections']['changelog'] ?? ''];
        }
        // Initialize banners as empty array and populate if data is available
        $details->banners = [];
        if (isset($remote_data['banners']) && \is_array($remote_data['banners'])) {
            $details->banners = ['low' => $remote_data['banners']['low'] ?? '', 'high' => $remote_data['banners']['high'] ?? ''];
        }
        return $details;
    }
    /**
     * Checks for available updates.
     * If an update is available and compatible, adds update data to the transient.
     *
     * @param mixed $updates Update data object.
     * @return mixed Modified update data.
     */
    public function check_update_availability($updates)
    {
        if (!\is_object($updates)) {
            return $updates;
        }
        /** @var \stdClass $updates */
        $remote_data = $this->fetch_remote_data();
        if (!$remote_data || !\is_array($remote_data)) {
            return $updates;
        }
        if (!isset($remote_data['version']) || !\is_string($remote_data['version']) || !isset($remote_data['requires']) || !\is_string($remote_data['requires']) || !isset($remote_data['requires_php']) || !\is_string($remote_data['requires_php'])) {
            return $updates;
        }
        // Check if a new version is available and if requirements are met.
        if (\version_compare($this->current_version, $remote_data['version'], '<') && \version_compare(get_bloginfo('version'), $remote_data['requires'], '>=') && \version_compare(\PHP_VERSION, $remote_data['requires_php'], '>=')) {
            $update_info = new \stdClass();
            $update_info->slug = $this->plugin_utils_manager->get_config('plugin_base');
            $update_info->plugin = $this->plugin_utils_manager->get_config('plugin_base');
            $update_info->new_version = $remote_data['version'];
            $update_info->tested = $remote_data['tested'] ?? '';
            $update_info->package = $remote_data['download_url'] ?? '';
            $updates->response[$update_info->plugin] = $update_info;
        }
        return $updates;
    }
    /**
     * Adds a manual update check link in the plugin row.
     *
     * @param array<string,string> $plugin_meta Existing plugin meta links.
     * @param string $plugin_file Plugin file.
     * @param array<string,string> $plugin_data Plugin data.
     * @param string $status Plugin status.
     * @return array<int|string,string> Modified plugin meta links.
     */
    public function add_manual_check_link($plugin_meta, $plugin_file, $plugin_data, $status)
    {
        $expected_base = $this->plugin_utils_manager->get_config('plugin_base');
        if ($plugin_file === $expected_base) {
            $url = admin_url('admin-ajax.php?action=check_' . $this->plugin_utils_manager->get_config('prefix') . '_updates');
            $plugin_meta[] = \sprintf('<a href="%s">%s</a>', esc_url($url), __('Check for updates', 'dynamic-ooo'));
        }
        return $plugin_meta;
    }
    /**
     * Processes the AJAX request for manual update checking.
     * Clears the cache, forces an update check, and redirects the user to the plugins page.
     *
     * @return void
     */
    public function process_manual_update_check()
    {
        $this->clear_update_cache();
        // Force update check by retrieving the update transient.
        $updates = get_site_transient('update_plugins');
        $this->check_update_availability($updates);
        wp_safe_redirect(admin_url('plugins.php'));
        exit;
    }
    /**
     * Clears the transient used for caching update information.
     *
     * @return void
     */
    public function clear_update_cache()
    {
        delete_transient($this->transient_key);
    }
}
