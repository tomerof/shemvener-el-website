<?php

namespace DynamicOOO\PluginUtils\AdminPages;

use DynamicOOO\PluginUtils;
class Manager
{
    /**
     * @var Pages\LicensePage
     */
    public $license_page;
    /**
     * @var AdminNotices
     */
    public $admin_notices;
    /**
     * @var \DynamicOOO\PluginUtils\Manager
     */
    public $plugin_utils_manager;
    /**
     * @param \DynamicOOO\PluginUtils\Manager $plugin_utils_manager
     */
    public function __construct($plugin_utils_manager)
    {
        $this->plugin_utils_manager = $plugin_utils_manager;
        $this->admin_notices = new \DynamicOOO\PluginUtils\AdminPages\AdminNotices($plugin_utils_manager);
        $this->license_page = new \DynamicOOO\PluginUtils\AdminPages\Pages\LicensePage($plugin_utils_manager);
        add_action('admin_enqueue_scripts', [$this, 'enqueue_scripts']);
    }
    /**
     * @param class-string<Pages\Base> $page
     * @return Pages\Base
     */
    public function add_new_page($page)
    {
        return new $page($this->plugin_utils_manager);
    }
    /**
     * @return void
     */
    public function enqueue_scripts()
    {
        if (!empty($_GET['page']) && $_GET['page'] === $this->plugin_utils_manager->get_config('prefix') . '_license') {
            wp_enqueue_script($this->plugin_utils_manager->get_config('prefix') . '-rollback', $this->plugin_utils_manager->assets->get_assets_url('js/rollback.js'), ['jquery'], $this->plugin_utils_manager->get_config('version'), \true);
            wp_localize_script($this->plugin_utils_manager->get_config('prefix') . '-rollback', 'oooRollback', ['action' => $this->plugin_utils_manager->get_config('prefix') . '_rollback_plugin', 'confirmMessage' => __('Are you sure you want to rollback?', 'dynamic-ooo'), 'rollingBackMessage' => __('Rolling back...', 'dynamic-ooo'), 'unknownError' => __('Unknown error', 'dynamic-ooo'), 'connectionError' => __('Connection error', 'dynamic-ooo')]);
        }
    }
}
