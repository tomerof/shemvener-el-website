<?php

namespace DynamicOOO\PluginUtils;

if (!\defined('ABSPATH')) {
    exit;
}
class WpCli extends \WP_CLI_Command
{
    /**
     * @var Manager
     */
    protected $plugin_utils_manager;
    /**
     * @param Manager $plugin_utils_manager
     */
    public function __construct($plugin_utils_manager)
    {
        $this->plugin_utils_manager = $plugin_utils_manager;
        \WP_CLI::add_command($this->plugin_utils_manager->get_config('prefix'), $this);
    }
    /**
     * @return void
     */
    public function version()
    {
        \WP_CLI::line('Version ' . $this->plugin_utils_manager->get_config('version'));
    }
    /**
     * @param array<int,string> $args
     * @return void
     */
    public function license($args)
    {
        $license_system = $this->plugin_utils_manager->license;
        switch ($args[0]) {
            case 'activate':
                if (empty($args[1])) {
                    \WP_CLI::line('Missing license key');
                    return;
                }
                /** @var array{0:bool,1:string} $response */
                $response = $license_system->activate_new_license_key($args[1]);
                \WP_CLI::line($response[1]);
                return;
            case 'deactivate':
                /** @var array{0:bool,1:string} $response */
                $response = $license_system->deactivate_license();
                \WP_CLI::line($response[1]);
                return;
            case 'check':
                if ($license_system->is_license_active()) {
                    \WP_CLI::line('The license is active');
                } else {
                    \WP_CLI::line($license_system->get_license_error());
                }
        }
    }
}
