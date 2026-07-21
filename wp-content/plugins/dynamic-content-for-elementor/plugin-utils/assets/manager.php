<?php

namespace DynamicOOO\PluginUtils\Assets;

if (!\defined('ABSPATH')) {
    exit;
    // Exit if accessed directly.
}
class Manager
{
    /**
     * Base path for plugin utils
     */
    const PLUGIN_UTILS_PATH = 'plugin-utils';
    /**
     * Images directory relative to plugin utils
     */
    const IMAGES_DIR = 'assets/images';
    const ASSETS_DIR = 'assets';
    /**
     * @var \DynamicOOO\PluginUtils\Manager
     */
    protected $plugin_utils_manager;
    /**
     * @param \DynamicOOO\PluginUtils\Manager $plugin_utils_manager
     */
    public function __construct($plugin_utils_manager)
    {
        $this->plugin_utils_manager = $plugin_utils_manager;
    }
    /**
     * @return string URL to the plugin logo
     */
    public function get_logo_url()
    {
        return plugins_url(self::IMAGES_DIR . '/dynamicooo-icon.png', __DIR__);
    }
    /**
     * @return string URL to the plugin logo
     */
    public function get_logo_url_svg()
    {
        return plugins_url(self::IMAGES_DIR . '/dynamicooo-long-negative.svg', __DIR__);
    }
    /**
     * @return string Base64 encoded SVG logo
     */
    public function get_logo_url_svg_onecolor_base64()
    {
        $file_path = __DIR__ . '/images/dynamicooo-icon-onecolor.svg';
        $file_content = \file_get_contents($file_path);
        if (\false === $file_content) {
            return '';
        }
        return 'data:image/svg+xml;base64,' . \base64_encode($file_content);
    }
    /**
     * @param string $path Path relative to assets directory
     * @return string Full URL to the asset
     */
    public function get_assets_url($path)
    {
        return plugins_url(self::ASSETS_DIR . '/' . $path, __DIR__);
    }
    /**
     * Enqueue admin scripts
     *
     * @return void
     */
    public function enqueue_admin_scripts()
    {
        wp_enqueue_script($this->plugin_utils_manager->get_config('prefix') . '-admin', plugins_url('assets/admin.js', __DIR__), ['jquery'], $this->plugin_utils_manager->get_config('version'), \true);
    }
}
