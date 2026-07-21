<?php

namespace DynamicOOO\PluginUtils;

if (!\defined('ABSPATH')) {
    exit;
}
class License implements \DynamicOOO\PluginUtils\LicenseInterface
{
    /**
     * @var Manager
     */
    protected $plugin_utils_manager;
    /**
     * @var string
     */
    const LICENSE_STATUS_OPTION = '_license_status';
    /**
     * @var string
     */
    const LICENSE_ERROR_OPTION = '_license_error';
    /**
     * @var string
     */
    const LICENSE_DOMAIN_OPTION = '_license_domain';
    /**
     * @var string
     */
    const LICENSE_KEY_OPTION = '_license_key';
    /**
     * @var bool
     */
    private $should_attempt_auto_activation = \false;
    /**
     * @var bool
     */
    private $is_staging = \false;
    /**
     * @var string
     */
    private $license_page;
    /**
     * @var string
     */
    private $beta_option;
    /**
     * @param Manager $plugin_utils_manager
     */
    public function __construct($plugin_utils_manager)
    {
        $this->plugin_utils_manager = $plugin_utils_manager;
        $this->license_page = $this->plugin_utils_manager->get_license_admin_page();
        $this->beta_option = $this->plugin_utils_manager->get_config('prefix') . '_beta';
    }
    /**
     * @return void
     */
    public function init()
    {
        if ($this->plugin_utils_manager->get_config('activation_advisor') === 'auto' && !$this->is_license_active(\false)) {
            $this->activation_advisor();
        }
    }
    /**
     * @return string
     */
    public function get_plugin()
    {
        return $this->plugin_utils_manager->get_config('prefix');
    }
    /**
     * @return void
     */
    public function activation_advisor()
    {
        //phpcs:ignore WordPress.Security.NonceVerification.Recommended
        $tab_license = isset($_GET['page']) && $_GET['page'] === $this->license_page;
        if (current_user_can('manage_options') && is_admin() && !$tab_license) {
            // translators: 1: opening HTML anchor tag for activating the license, 2: closing HTML anchor tag, 3: opening HTML anchor tag for purchasing the license, 4: closing HTML anchor tag
            $message = \sprintf(__('It seems that your copy is not activated, please %1$sactivate it%2$s or %3$sbuy a new license%4$s.', 'dynamic-ooo'), '<a href="' . admin_url() . 'admin.php?page=' . $this->license_page . '">', '</a>', '<a href="' . $this->plugin_utils_manager->get_config('pricing_url') . '" target="blank">', '</a>');
            $this->plugin_utils_manager->admin_pages->admin_notices->error($message);
            add_action('in_plugin_update_message-' . $this->plugin_utils_manager->get_config('plugin_base'), [$this, 'error_message_update'], 10, 2);
        }
    }
    /**
     * @param bool $fresh
     * @return bool
     */
    public function is_license_active($fresh = \false)
    {
        if ($fresh) {
            $this->refresh_license_status();
        }
        return get_option($this->plugin_utils_manager->get_config('prefix') . self::LICENSE_STATUS_OPTION, '') === 'active';
    }
    /**
     * @param string $status either 'active' or 'inactive'
     * @return void
     */
    private function set_license_status($status)
    {
        update_option($this->plugin_utils_manager->get_config('prefix') . self::LICENSE_STATUS_OPTION, $status);
    }
    /**
     * @return string
     */
    public function get_license_error()
    {
        /**
         * @var string
         */
        return get_option($this->plugin_utils_manager->get_config('prefix') . self::LICENSE_ERROR_OPTION, '');
    }
    /**
     * @param string $error
     * @return void
     */
    private function set_license_error($error)
    {
        $this->set_license_status('inactive');
        update_option($this->plugin_utils_manager->get_config('prefix') . self::LICENSE_ERROR_OPTION, $error);
    }
    /**
     * @param string $key
     * @return void
     */
    private function set_license_key($key)
    {
        update_option($this->plugin_utils_manager->get_config('prefix') . self::LICENSE_KEY_OPTION, \strtolower(\trim($key)));
    }
    /**
     * @return string
     */
    public function get_license_key()
    {
        /**
         * @var string
         */
        $key = get_option($this->plugin_utils_manager->get_config('prefix') . self::LICENSE_KEY_OPTION, '');
        return \strtolower(\trim($key));
    }
    /**
     * @return string
     */
    public function get_license_key_last_4_digits()
    {
        return \substr($this->get_license_key(), -4);
    }
    /**
     * @param string $key
     * @return array{0:bool,1:string}
     */
    public function activate_new_license_key($key)
    {
        // TODO: check if valid.
        $this->set_license_key($key);
        return $this->activate_license();
    }
    /**
     * @return string
     */
    public function get_last_active_domain()
    {
        /**
         * @var string
         */
        return get_option($this->plugin_utils_manager->get_config('prefix') . self::LICENSE_DOMAIN_OPTION);
    }
    /**
     * @param string $domain
     * @return void
     */
    public function set_last_active_domain($domain)
    {
        update_option($this->plugin_utils_manager->get_config('prefix') . self::LICENSE_DOMAIN_OPTION, $domain);
    }
    /**
     * @return string
     */
    public function get_current_domain()
    {
        $domain = get_bloginfo('wpurl');
        $domain = \str_replace('https://', '', $domain);
        $domain = \str_replace('http://', '', $domain);
        return $domain;
    }
    /**
     * @param array<string,mixed>|false $response
     * @param string $domain
     * @return void
     */
    protected function handle_status_check_response($response, $domain)
    {
        $this->should_attempt_auto_activation = \false;
        $this->is_staging = \false;
        if (!$response) {
            return;
        }
        /**
         * @var string
         */
        $message = $response['message'] ?? esc_html__('Unknown', 'dynamic-ooo');
        if (($response['staging'] ?? '') === 'yes') {
            $this->is_staging = \true;
        }
        $status_code = $response['status_code'] ?? '';
        if ('e002' === $status_code) {
            // key is invalid:
            $this->set_license_error($message);
            return;
        }
        if (\in_array($status_code, ['s203', 'e204'], \true)) {
            // key is not active for current domain, we should not attempt activation:
            $this->set_license_error($message . " (domain: {$domain})");
            return;
        }
        if (\in_array($status_code, ['s205', 's215'], \true)) {
            // if license is valid and active for domain:
            if (($response['license_status'] ?? '') === 'expired') {
                // But expired:
                $this->set_license_error($message);
                $this->should_attempt_auto_activation = \true;
                return;
            }
            $this->set_license_status('active');
            $this->set_last_active_domain($this->get_current_domain());
            return;
        }
        // other cases, just set the error with message:
        $this->set_license_error($message);
    }
    /**
     * @param string $domain
     * @return array<string,mixed>|false
     */
    protected function remote_status_check($domain)
    {
        global $wp_version;
        $response = $this->plugin_utils_manager->api->get(\DynamicOOO\PluginUtils\Api::ENDPOINT_LICENSE, [\DynamicOOO\PluginUtils\Api::PARAM_WOO_ACTION => \DynamicOOO\PluginUtils\Api::ACTION_STATUS, \DynamicOOO\PluginUtils\Api::PARAM_LICENSE_KEY => $this->get_license_key(), \DynamicOOO\PluginUtils\Api::PARAM_PRODUCT_ID => $this->plugin_utils_manager->get_config('product_unique_id'), \DynamicOOO\PluginUtils\Api::PARAM_DOMAIN => $domain, \DynamicOOO\PluginUtils\Api::PARAM_API_VERSION => '1.1', \DynamicOOO\PluginUtils\Api::PARAM_WP_VERSION => $wp_version, \DynamicOOO\PluginUtils\Api::PARAM_PLUGIN_VERSION => $this->plugin_utils_manager->get_config('version'), \DynamicOOO\PluginUtils\Api::PARAM_MULTISITE => is_multisite(), \DynamicOOO\PluginUtils\Api::PARAM_PHP_VERSION => \PHP_VERSION]);
        if (is_wp_error($response)) {
            return \false;
        }
        /**
         * @var array<string,mixed>|false
         */
        return $response[0] ?? \false;
    }
    /**
     * @return void
     */
    public function refresh_license_status()
    {
        if (!$this->get_license_key()) {
            $this->set_license_error(esc_html__('No license present', 'dynamic-ooo'));
            return;
        }
        $domain = $this->get_current_domain();
        $response = $this->remote_status_check($domain);
        $this->handle_status_check_response($response, $domain);
    }
    /**
     * Refresh license status. If license was not deliberately deactivated try
     * to reactivate the license for this domain.
     *
     * @return void
     */
    public function refresh_and_repair_license_status()
    {
        $this->refresh_license_status();
        if ($this->should_attempt_auto_activation) {
            $this->activate_license();
            // TODO: refresh again?
        }
    }
    /**
     * Ask to the server to activate the license
     *
     * @return string activation message
     */
    private function activate_license_request()
    {
        global $wp_version;
        $response = $this->plugin_utils_manager->api->get(\DynamicOOO\PluginUtils\Api::ENDPOINT_LICENSE, [\DynamicOOO\PluginUtils\Api::PARAM_WOO_ACTION => \DynamicOOO\PluginUtils\Api::ACTION_ACTIVATE, \DynamicOOO\PluginUtils\Api::PARAM_LICENSE_KEY => $this->get_license_key(), \DynamicOOO\PluginUtils\Api::PARAM_PRODUCT_ID => $this->plugin_utils_manager->get_config('product_unique_id'), \DynamicOOO\PluginUtils\Api::PARAM_DOMAIN => $this->get_current_domain(), \DynamicOOO\PluginUtils\Api::PARAM_API_VERSION => '1.1', \DynamicOOO\PluginUtils\Api::PARAM_WP_VERSION => $wp_version, \DynamicOOO\PluginUtils\Api::PARAM_PLUGIN_VERSION => $this->plugin_utils_manager->get_config('version'), \DynamicOOO\PluginUtils\Api::PARAM_MULTISITE => is_multisite(), \DynamicOOO\PluginUtils\Api::PARAM_PHP_VERSION => \PHP_VERSION]);
        if (is_wp_error($response)) {
            return esc_html__('Problem contacting the server, try again in a few minutes.', 'dynamic-ooo');
        }
        /**
         * @var array<string,mixed>
         */
        $data = \reset($response);
        /**
         * @var string
         */
        return $data['message'] ?? esc_html__('Unknown response from server', 'dynamic-ooo');
    }
    /**
     * Ask the server to deactivate the license
     *
     * @return string activation message
     */
    private function deactivate_license_request()
    {
        global $wp_version;
        $response = $this->plugin_utils_manager->api->get(\DynamicOOO\PluginUtils\Api::ENDPOINT_LICENSE, [\DynamicOOO\PluginUtils\Api::PARAM_WOO_ACTION => \DynamicOOO\PluginUtils\Api::ACTION_DEACTIVATE, \DynamicOOO\PluginUtils\Api::PARAM_LICENSE_KEY => $this->get_license_key(), \DynamicOOO\PluginUtils\Api::PARAM_PRODUCT_ID => $this->plugin_utils_manager->get_config('product_unique_id'), \DynamicOOO\PluginUtils\Api::PARAM_DOMAIN => $this->get_current_domain(), \DynamicOOO\PluginUtils\Api::PARAM_API_VERSION => '1.1', \DynamicOOO\PluginUtils\Api::PARAM_WP_VERSION => $wp_version, \DynamicOOO\PluginUtils\Api::PARAM_PLUGIN_VERSION => $this->plugin_utils_manager->get_config('version'), \DynamicOOO\PluginUtils\Api::PARAM_MULTISITE => is_multisite(), \DynamicOOO\PluginUtils\Api::PARAM_PHP_VERSION => \PHP_VERSION]);
        if (is_wp_error($response)) {
            return esc_html__('Problem contacting the server, try again in a few minutes.', 'dynamic-ooo');
        }
        /**
         * @var array<string,mixed>
         */
        $data = \reset($response);
        /**
         * @var string
         */
        return $data['message'] ?? esc_html__('Unknown response from server', 'dynamic-ooo');
    }
    /**
     * Ask the server to deactivate the license. Refresh license status.
     * Delete the key for staging sites.
     *
     * @return array{0:bool,1:string}
     */
    public function deactivate_license()
    {
        $msg = $this->deactivate_license_request();
        $success = !$this->is_license_active(\true);
        if ($this->is_staging) {
            $this->set_license_key('');
            $this->refresh_license_status();
            return [\true, esc_html__('Success', 'dynamic-ooo')];
        }
        return [$success, $msg];
    }
    /**
     * Ask the server to activate the license. Refresh license status.
     *
     * @return array{0:bool,1:string}
     */
    public function activate_license()
    {
        $msg = $this->activate_license_request();
        $success = $this->is_license_active(\true);
        return [$success, $msg];
    }
    /**
     * @return void
     */
    public function activate_beta_releases()
    {
        update_option($this->beta_option, \true);
    }
    /**
     * @return void
     */
    public function deactivate_beta_releases()
    {
        update_option($this->beta_option, \false);
    }
    /**
     * @return bool
     */
    public function is_beta_releases_activated()
    {
        return (bool) get_option($this->beta_option, \false);
    }
    /**
     * Error Message on Update
     *
     * @param array<mixed> $plugin_data
     * @param object $response
     * @return void
     */
    //phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.FoundAfterLastUsed
    public function error_message_update($plugin_data, $response)
    {
        \printf('&nbsp;<strong>%1$s</strong>', esc_html__('The license is not active.', 'dynamic-ooo'));
    }
    /**
     * @return void
     */
    public function domain_mismatch_check()
    {
        if ($this->get_license_key() && !$this->is_license_active() && $this->get_last_active_domain() && $this->get_last_active_domain() !== $this->get_current_domain()) {
            $this->plugin_utils_manager->admin_pages->admin_notices->warning(\sprintf(esc_html__('License Mismatch. Your license key doesn\'t match your current domain. This is likely due to a change in the domain URL. You can reactivate your license now. Remember to deactivate the one for the old domain from your license area on Dynamic.ooo\'s site', 'dynamic-ooo'), '<a class="btn button" href="' . admin_url() . 'admin.php?page=' . $this->license_page . '">', '</a>'));
        }
    }
}
