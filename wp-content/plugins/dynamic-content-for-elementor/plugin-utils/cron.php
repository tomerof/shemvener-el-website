<?php

namespace DynamicOOO\PluginUtils;

if (!\defined('ABSPATH')) {
    exit;
}
class Cron
{
    /**
     * @var array<string,array<string,string>>
     */
    protected $tasks = [];
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
        $this->tasks = ['check_license' => ['interval' => 'daily'], 'check_updates' => ['interval' => 'twicedaily']];
        $this->schedule_all_tasks();
    }
    /**
     * @return array<string,array<string,string>>
     */
    protected function get_tasks()
    {
        return $this->tasks;
    }
    /**
     * @return void
     */
    protected function schedule_all_tasks()
    {
        foreach ($this->get_tasks() as $method_name => $info) {
            if (empty($info['interval']) || !\method_exists($this, $method_name)) {
                continue;
            }
            $hook = $this->get_hook_name($method_name);
            if (!wp_next_scheduled($hook)) {
                wp_schedule_event(\time(), $info['interval'], $hook);
            }
            $callback = [$this, $method_name];
            if (\is_callable($callback)) {
                add_action($hook, $callback);
            }
        }
    }
    /**
     * @return void
     */
    public function clear_all_tasks()
    {
        foreach ($this->get_tasks() as $method_name => $info) {
            $hook = $this->get_hook_name($method_name);
            wp_clear_scheduled_hook($hook);
        }
    }
    /**
     * @param string $method_name
     * @return string
     */
    protected function get_hook_name(string $method_name)
    {
        return $this->plugin_utils_manager->get_config('prefix') . '_' . $method_name . '_cron';
    }
    /**
     * @return void
     */
    public function check_license()
    {
        $this->plugin_utils_manager->license->refresh_and_repair_license_status();
    }
    /**
     * @return void
     */
    public function check_updates()
    {
        $update_checker = $this->plugin_utils_manager->update_checker;
        $update_checker->clear_update_cache();
        $update_checker->check_update_availability(get_site_transient('update_plugins'));
    }
}
