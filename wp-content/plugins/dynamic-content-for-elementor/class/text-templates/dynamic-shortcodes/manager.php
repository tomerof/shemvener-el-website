<?php

// SPDX-FileCopyrightText: 2018-2026 Ovation S.r.l. <help@dynamic.ooo>
// SPDX-License-Identifier: GPL-3.0-or-later
namespace DynamicContentForElementor\TextTemplates\DynamicShortcodes;

use DynamicContentForElementor\Modules\DynamicTags\Tags;
use DynamicContentForElementor\Helper;
use DynamicContentForElementor\Tokens;
use DynamicContentForElementor\Plugin;
class Manager
{
    /**
     * @var \DynamicShortcodes\Core\Shortcodes\Manager|null
     */
    private $shortcodes_manager = null;
    /**
     * @var string
     */
    private $cache_dsh_key = 'dsh_plugin_info';
    /**
     * @var array<array<string,mixed>>
     */
    private $context_data_stack = [];
    /**
     * @var bool
     */
    private $do_not_delay_tag_expansion = \false;
    public function __construct()
    {
        add_action('dynamic-shortcodes/init', [$this, 'init_manager']);
        add_action('dynamic-shortcodes/types/register', [$this, 'register_dynamic_shortcodes']);
        add_action('elementor/dynamic_tags/register', [$this, 'register_dynamic_tags'], 20);
        add_action('admin_post_dce_install_dsh', [$this, 'post_dce_install_dsh']);
        add_filter('dynamic-shortcodes/elementor/dynamic-tag-text/early-return', [$this, 'should_delay_tag_expansion_in_forms']);
    }
    public function should_delay_tag_expansion_in_forms($content)
    {
        if ($this->do_not_delay_tag_expansion) {
            return \false;
        }
        // if the dynamic tag shortcode contains a form shortcode the tag should just return the code to be
        // expanded later by the extensions that need it. Otherwise form data is not available during expansion.
        if (!\class_exists('\\ElementorPro\\Modules\\Forms\\Classes\\Ajax_Handler')) {
            return \false;
        }
        if (!\ElementorPro\Modules\Forms\Classes\Ajax_Handler::is_form_submitted()) {
            return \false;
        }
        return \strpos($content, '{form:') !== \false;
    }
    /**
     * @return array<string,mixed>
     */
    public function get_context_data()
    {
        /**
         * @var array<string,mixed>
         */
        $call = $this->context_data_stack;
        return \end($call);
    }
    /**
     * @return boolean
     */
    public function is_dsh_active()
    {
        return $this->shortcodes_manager !== null;
    }
    /**
     * @return array<mixed>|false
     */
    public function get_plugin_info()
    {
        $remote = get_transient($this->cache_dsh_key);
        $plugin_utils_manager = \DynamicContentForElementor\Plugin::instance()->plugin_utils;
        if (\false === $remote) {
            $license_system = $plugin_utils_manager->license;
            $remote = wp_remote_get($plugin_utils_manager->get_config('license_url') . '/dynamic-shortcodes/info.php', ['timeout' => 10, 'headers' => ['Accept' => 'application/json'], 'body' => ['s' => $license_system->get_current_domain(), 'v' => DCE_VERSION, 'k' => $license_system->get_license_key(), 'beta' => \false, 'from_dce' => \true]]);
            if (is_wp_error($remote) || 200 !== wp_remote_retrieve_response_code($remote) || empty(wp_remote_retrieve_body($remote))) {
                return \false;
            }
            $remote = \json_decode(wp_remote_retrieve_body($remote), \true);
            set_transient($this->cache_dsh_key, $remote, 12 * HOUR_IN_SECONDS);
        }
        return $remote;
    }
    /**
     * @return void
     */
    public function post_dce_install_dsh()
    {
        if (!current_user_can('install_plugins')) {
            wp_die('Permission denied');
        }
        check_admin_referer('dce_install_dsh', 'dce_install_dsh_nonce');
        $plugin_info = $this->get_plugin_info();
        if (!\is_array($plugin_info) || !isset($plugin_info['download_url'])) {
            echo 'error getting the Dynamic Shortcode version info, is your license active?';
            wp_die();
        }
        $this->install_plugin($plugin_info['download_url']);
    }
    /**
     * @param string $plugin_url
     * @return void
     */
    public function install_plugin($plugin_url)
    {
        include_once ABSPATH . 'wp-admin/includes/plugin-install.php';
        include_once ABSPATH . 'wp-admin/includes/file.php';
        include_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
        include_once ABSPATH . 'wp-admin/includes/class-wp-ajax-upgrader-skin.php';
        include_once ABSPATH . 'wp-admin/includes/plugin.php';
        $tmp_file = download_url($plugin_url);
        if (is_wp_error($tmp_file)) {
            wp_die(esc_html($tmp_file->get_error_message()));
        }
        $upgrader = new \Plugin_Upgrader(new \WP_Ajax_Upgrader_Skin());
        $installed = $upgrader->install($tmp_file);
        @\unlink($tmp_file);
        if (is_wp_error($installed)) {
            wp_die(esc_html($installed->get_error_message()));
        }
        $info = $upgrader->plugin_info();
        if (\false !== $info) {
            activate_plugin(plugin_basename($info));
        }
        wp_safe_redirect(admin_url('admin.php?page=dce-features'));
        exit;
    }
    /**
     * @param \DynamicShortcodes\Plugin|null $dsh_plugin
     * @return void
     */
    public function init_manager($dsh_plugin)
    {
        if (!$dsh_plugin) {
            return;
        }
        $this->shortcodes_manager = $dsh_plugin->shortcodes_manager;
    }
    /**
     * @return void
     */
    public function register_dynamic_shortcodes()
    {
        if (!$this->shortcodes_manager) {
            // for older versions of dsh:
            return;
        }
        $context = ['dce-manager' => $this];
        $this->shortcodes_manager->register(\DynamicContentForElementor\TextTemplates\DynamicShortcodes\Types\Data::class, $context);
        $this->shortcodes_manager->register(\DynamicContentForElementor\TextTemplates\DynamicShortcodes\Types\Form::class, $context);
    }
    /**
     * @param Callable $callback
     * @param array<mixed> $args
     * @param array<string,mixed> $data
     * @return mixed
     */
    public function call_with_data($data, $callback, $args = [])
    {
        $this->context_data_stack[] = $data;
        // currently this function is always used for rendering elementor templates,
        // tag expansion delay does is problematic for this, so we prevent it.
        $this->do_not_delay_tag_expansion = \true;
        $res = \call_user_func_array($callback, $args);
        $this->do_not_delay_tag_expansion = \false;
        \array_pop($this->context_data_stack);
        return $res;
    }
    /**
     * @param string $str
     * @param array<string,mixed> $data
     * @return string
     */
    public function expand_with_data($str, $data)
    {
        if ($this->shortcodes_manager === null) {
            return $str;
        }
        $this->context_data_stack[] = $data;
        $res = $this->shortcodes_manager->expand_shortcodes($str, [], null);
        \array_pop($this->context_data_stack);
        return $res;
    }
    /**
     * @param string $str
     * @param array<string,mixed> $interpreter_env
     * @return string
     */
    public function expand($str, $interpreter_env = [])
    {
        if ($this->shortcodes_manager === null) {
            return $str;
        }
        return $this->shortcodes_manager->expand_shortcodes($str, $interpreter_env);
    }
    /**
     * @param \Elementor\Core\DynamicTags\Manager $dynamic_tags_manager
     * @return void
     */
    public function register_dynamic_tags($dynamic_tags_manager)
    {
        $dynamic_tags_manager->register(new Tags\DynamicShortcodesWizard\Text());
        $dynamic_tags_manager->register(new Tags\DynamicShortcodesWizard\Image());
        $dynamic_tags_manager->register(new Tags\DynamicShortcodesWizard\Gallery());
    }
    /**
     * @return string
     */
    public function get_notice_required()
    {
        return \sprintf(
            /* translators: 1: opening link tag, 2: closing link tag, 3: plugin name */
            esc_html__('Dynamic Shortcodes, which is included in your %3$s license, is not currently installed. %1$sInstall Dynamic Shortcodes%2$s to enhance your experience with Elementor.', 'dynamic-content-for-elementor'),
            '<a href="' . Plugin::instance()->text_templates::URL_DSH_INSTALLATION . '">',
            '</a>',
            DCE_PRODUCT_NAME
        );
    }
}
