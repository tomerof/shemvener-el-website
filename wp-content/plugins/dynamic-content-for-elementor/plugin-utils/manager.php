<?php

namespace DynamicOOO\PluginUtils;

class Manager
{
    /**
     * @var Manager|null
     */
    protected static $instance = null;
    /**
     * @var array<string,mixed>
     */
    protected $config = [];
    /**
     * @var Rollback
     */
    public $rollback;
    /**
     * @var LicenseInterface
     */
    public $license;
    /**
     * @var Cron
     */
    public $cron;
    /**
     * @var WpCli
     */
    public $wpcli;
    /**
     * @var UpdateChecker
     */
    public $update_checker;
    /**
     * @var AdminPages\Manager
     */
    public $admin_pages;
    /**
     * @var Api
     */
    public $api;
    /**
     * @var Assets\Manager
     */
    public $assets;
    /**
     * @var ActionLinks|null
     */
    public $action_links;
    /**
     * @param array<string,mixed> $config
     */
    public function __construct($config)
    {
        if ($config['autoload'] ?? \true) {
            \spl_autoload_register([$this, 'autoload']);
        }
        $this->set_config($config);
        $this->license = new \DynamicOOO\PluginUtils\License($this);
        if (!$this->get_delay_init()) {
            $this->init();
        }
    }
    /**
     * @param LicenseInterface|null $license_override
     * @return void
     */
    public function init($license_override = null)
    {
        $this->admin_pages = new \DynamicOOO\PluginUtils\AdminPages\Manager($this);
        $this->license->init();
        $this->api = new \DynamicOOO\PluginUtils\Api($this);
        $this->rollback = new \DynamicOOO\PluginUtils\Rollback($this);
        $this->cron = new \DynamicOOO\PluginUtils\Cron($this);
        if (\defined('WP_CLI') && WP_CLI) {
            new \DynamicOOO\PluginUtils\WpCli($this);
        }
        if ($license_override) {
            $this->license = $license_override;
        }
        $this->update_checker = new \DynamicOOO\PluginUtils\UpdateChecker($this);
        $this->assets = new \DynamicOOO\PluginUtils\Assets\Manager($this);
        $this->action_links = new \DynamicOOO\PluginUtils\ActionLinks($this);
    }
    /**
     * @param string $search_class
     * @return void
     */
    public function autoload($search_class)
    {
        if (0 !== \strpos($search_class, 'DynamicOOO\\PluginUtils')) {
            return;
        }
        if (!\class_exists($search_class)) {
            $filename = \strtolower(\preg_replace(['/^DynamicOOO\\\\PluginUtils\\\\/', '/([a-z])([A-Z])/', '/_/', '/\\\\/'], ['', '$1-$2', '-', \DIRECTORY_SEPARATOR], $search_class) ?? '');
            $filename = trailingslashit(__DIR__) . $filename . '.php';
            if (\is_readable($filename)) {
                include $filename;
            }
        }
    }
    /**
     * @param array<string,mixed> $config
     * @return self
     */
    public function set_config($config)
    {
        $required_keys = ['plugin_base', 'plugin_slug', 'version', 'plugin_file', 'plugin_name_underscored', 'license_url', 'prefix', 'product_unique_id', 'product_name_long', 'pricing_url'];
        foreach ($required_keys as $key) {
            if (!isset($config[$key])) {
                throw new \Exception("Missing required config key: {$key}");
            }
        }
        if (!isset($config['activation_advisor'])) {
            $config['activation_advisor'] = 'auto';
        }
        $this->config = $config;
        return $this;
    }
    /**
     * @param string $key
     * @return string
     */
    public function get_config($key = '')
    {
        $config = $this->config[$key] ?? '';
        if (!\is_string($config)) {
            throw new \Exception("Config key {$key} is not a string");
        }
        return $config;
    }
    /**
     * @return string
     */
    public function get_license_admin_page()
    {
        return $this->config['license-admin-page'] ?? $this->get_config('prefix') . '-license';
    }
    /**
     * @return bool
     */
    public function get_delay_init()
    {
        /**
         * @var bool
         */
        return $this->config['delay_init'] ?? \false;
    }
    /**
     * @return bool
     */
    public function get_supports_beta()
    {
        /**
         * @var bool
         */
        return $this->config['supports_beta'] ?? \false;
    }
    /**
     * @return bool
     */
    public function get_supports_rollback()
    {
        /**
         * @var bool
         */
        return $this->config['supports_rollback'] ?? \false;
    }
    /**
     * @return array<string,array<string,string>>
     */
    public function get_action_links()
    {
        /**
         * @var array<string,array<string,string>>
         */
        return $this->config['action_links'] ?? [];
    }
    /**
     * Enqueue admin styles
     * @return void
     */
    public function enqueue_admin_styles()
    {
        wp_enqueue_style($this->get_config('prefix') . '-admin-style', $this->assets->get_assets_url('css/admin-style.css'), [], $this->get_config('version'));
    }
}
