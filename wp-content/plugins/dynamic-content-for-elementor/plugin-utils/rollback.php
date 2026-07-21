<?php

namespace DynamicOOO\PluginUtils;

if (!\defined('ABSPATH')) {
    exit;
    // Exit if accessed directly.
}
class Rollback
{
    /**
     * @var Manager
     */
    protected $plugin_utils_manager;
    /**
     * @var string
     */
    protected $transient_key;
    /**
     * @param Manager $plugin_utils_manager
     */
    public function __construct($plugin_utils_manager)
    {
        $this->plugin_utils_manager = $plugin_utils_manager;
        $this->transient_key = $this->plugin_utils_manager->get_config('prefix') . '_rollback_versions_' . $this->plugin_utils_manager->get_config('version');
        add_action('wp_ajax_' . $this->plugin_utils_manager->get_config('prefix') . '_rollback_plugin', [$this, 'handle_rollback_request']);
    }
    /**
     * Retrieves and filters available rollback versions.
     * @param int $max_versions Maximum number of versions to return
     * @return array<string> Array of valid version strings.
     */
    public function get_rollback_versions($max_versions = 30)
    {
        $prefix = $this->plugin_utils_manager->get_config('prefix');
        $plugin_version = $this->plugin_utils_manager->get_config('version');
        $transient_key = $prefix . '_rollback_versions_' . $plugin_version;
        $cached_versions = get_transient($transient_key);
        if (\is_array($cached_versions)) {
            /**
             * @var array<string>
             */
            return $cached_versions;
        }
        $versions = $this->plugin_utils_manager->api->get_available_versions();
        if (is_wp_error($versions)) {
            return [];
        }
        $valid_versions = \array_filter($versions, function ($version) use($plugin_version) {
            if (\preg_match('/(beta|rc)/i', $version)) {
                return \false;
            }
            return \version_compare($version, $plugin_version, '<');
        });
        $valid_versions = \array_unique($valid_versions);
        \usort($valid_versions, function ($a, $b) {
            return \version_compare($b, $a);
        });
        $valid_versions = \array_slice($valid_versions, 0, $max_versions);
        set_transient($transient_key, $valid_versions, WEEK_IN_SECONDS);
        return $valid_versions;
    }
    /**
     * Purge cached rollback versions
     *
     * @return void
     */
    public function purge_cached_rollback_versions()
    {
        $prefix = $this->plugin_utils_manager->get_config('prefix');
        $plugin_version = $this->plugin_utils_manager->get_config('version');
        $transient_key = $prefix . '_rollback_versions_' . $plugin_version;
        delete_transient($transient_key);
    }
    /**
     * Performs a rollback to the specified target version.
     *
     * @param string $target_version The version to roll back to.
     * @return bool True if rollback is successful
     */
    public function rollback_to_version($target_version)
    {
        $license_system = $this->plugin_utils_manager->license;
        if (!$license_system->is_license_active(\true)) {
            throw new \Exception(esc_html__('Cannot rollback without an active license. Please activate it.', 'dynamic-ooo'));
        }
        $package_url = $this->plugin_utils_manager->api->get_package_url($target_version);
        if (is_wp_error($package_url)) {
            throw new \Exception($package_url->get_error_message());
        }
        if (empty($package_url)) {
            throw new \Exception(esc_html__('Could not get package URL for rollback', 'dynamic-ooo'));
        }
        /** @var string $package_url */
        require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
        $plugin_file = $this->plugin_utils_manager->get_config('plugin_base');
        $upgrader = new \Plugin_Upgrader(new \WP_Ajax_Upgrader_Skin(['nonce' => $this->plugin_utils_manager->get_config('prefix') . '_plugin_rollback', 'url' => wp_nonce_url(admin_url('admin-ajax.php')), 'plugin' => $plugin_file, 'type' => 'plugin']));
        $result = $upgrader->run(['package' => $package_url, 'destination' => WP_PLUGIN_DIR . '/' . \dirname($plugin_file), 'clear_destination' => \true, 'clear_working' => \true, 'hook_extra' => ['plugin' => $plugin_file, 'type' => 'plugin', 'action' => 'update']]);
        if (is_wp_error($result)) {
            throw new \Exception($result->get_error_message());
        }
        if (!$result) {
            throw new \Exception(esc_html__('Rollback failed for unknown reason', 'dynamic-ooo'));
        }
        $this->refresh_update_plugins_transient_from_server($target_version, $package_url);
        return \true;
    }
    /**
     * Refreshes the update_plugins transient using the version returned by the server.
     *
     * @param string $target_version The version that was rolled back to
     * @param string $package_url The URL of the package that was rolled back to
     * @return void
     */
    public function refresh_update_plugins_transient_from_server($target_version, $package_url)
    {
        $update_plugins = get_site_transient('update_plugins');
        if (!\is_object($update_plugins)) {
            $update_plugins = new \stdClass();
        }
        /** @var \stdClass $update_plugins */
        if (!isset($update_plugins->response) || !\is_array($update_plugins->response)) {
            $update_plugins->response = [];
        }
        $plugin_info = new \stdClass();
        $plugin_info->new_version = $target_version;
        $plugin_info->slug = $this->plugin_utils_manager->get_config('plugin_slug');
        $plugin_info->package = $package_url;
        $plugin_info->url = 'https://www.dynamic.ooo/';
        $plugin_name = $this->plugin_utils_manager->get_config('plugin_name');
        $update_plugins->response[$plugin_name] = $plugin_info;
        set_site_transient('update_plugins', $update_plugins);
    }
    /**
     * @return void
     */
    public function handle_rollback_request()
    {
        if (!current_user_can('update_plugins')) {
            wp_send_json_error(esc_html__('Insufficient permissions.', 'dynamic-ooo'));
        }
        if (!check_ajax_referer($this->plugin_utils_manager->get_config('prefix') . '_plugin_rollback', 'nonce', \false)) {
            wp_send_json_error(esc_html__('Invalid nonce.', 'dynamic-ooo'));
        }
        if (empty($_POST['version'])) {
            wp_send_json_error(esc_html__('No version specified.', 'dynamic-ooo'));
        }
        $version = sanitize_text_field($_POST['version']);
        try {
            $result = $this->rollback_to_version($version);
            if ($result) {
                wp_send_json_success(esc_html__('Rollback completed successfully.', 'dynamic-ooo'));
            } else {
                wp_send_json_error(esc_html__('Rollback failed.', 'dynamic-ooo'));
            }
        } catch (\Exception $e) {
            wp_send_json_error(esc_html($e->getMessage()));
        }
    }
}
