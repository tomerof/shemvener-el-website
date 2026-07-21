<?php

// SPDX-FileCopyrightText: 2018-2026 Ovation S.r.l. <help@dynamic.ooo>
// SPDX-License-Identifier: GPL-3.0-or-later
namespace DynamicContentForElementor\AdminPages;

use DynamicContentForElementor\Plugin;
use DynamicOOO\PluginUtils\AdminPages\Pages\Base;
use DynamicOOO\PluginUtils\AdminPages\AdminNotices;
class Manager
{
    /**
     * @var Base
     */
    public $features_page;
    /**
     * @var AdminNotices
     */
    public $notices;
    /**
     * @var Base
     */
    public $integrations;
    public function __construct()
    {
        add_action('init', function () {
            $this->features_page = Plugin::instance()->plugin_utils->admin_pages->add_new_page(\DynamicContentForElementor\AdminPages\Features\FeaturesPage::class);
            $this->integrations = Plugin::instance()->plugin_utils->admin_pages->add_new_page(\DynamicContentForElementor\AdminPages\Integrations::class);
        });
        $this->notices = Plugin::instance()->plugin_utils->admin_pages->admin_notices;
        add_action('admin_init', [$this, 'maybe_redirect_to_wizard_on_activation']);
        add_action('admin_menu', [$this, 'add_menu_pages'], 200);
        add_action('admin_notices', [$this, 'warning_lazyload']);
        add_action('admin_notices', [$this, 'warning_features_bloat']);
        add_filter('elementor/admin-top-bar/is-active', [$this, 'deactivate_elementor_top_bar'], 10, 2);
    }
    /**
     * Deactivates the Elementor top bar for Dynamic Content for Elementor pages.
     *
     * @param bool $is_active Whether the Elementor top bar is active.
     * @param \WP_Screen $current_screen The current screen.
     * @return bool Whether the Elementor top bar should be active.
     */
    public function deactivate_elementor_top_bar($is_active, $current_screen)
    {
        if ($current_screen && \false !== \strpos($current_screen->id, 'dynamic-content-for-elementor')) {
            return \false;
        }
        return $is_active;
    }
    /**
     * @return void
     */
    public function maybe_redirect_to_wizard_on_activation()
    {
        if (!get_transient('dce_activation_redirect')) {
            return;
        }
        if (wp_doing_ajax()) {
            return;
        }
        delete_transient('dce_activation_redirect');
        if (is_network_admin() || isset($_GET['activate-multi'])) {
            return;
        }
        if (get_option('dce_done_activation_redirection')) {
            return;
        }
        update_option('dce_done_activation_redirection', \true);
        wp_safe_redirect(admin_url('admin.php?page=dce-features'));
        exit;
    }
    /**
     * @return void
     */
    public function add_menu_pages()
    {
        $features_admin_page = $this->middleware_for_requirements([$this->features_page, 'render_page']);
        // Menu
        add_menu_page(DCE_PRODUCT_NAME, DCE_PRODUCT_NAME, 'manage_options', 'dce-features', $features_admin_page, Plugin::instance()->plugin_utils->assets->get_logo_url_svg_onecolor_base64(), 58.6);
        // Features
        add_submenu_page('dce-features', DCE_PRODUCT_NAME . ' - ' . esc_html__('Features', 'dynamic-content-for-elementor'), esc_html__('Features', 'dynamic-content-for-elementor'), 'manage_options', 'dce-features', $features_admin_page);
        // HTML Templates (only for PDF Generator for Elementor Pro Form or PDF Button)
        if (Plugin::instance()->features->is_feature_active('ext_form_pdf') || Plugin::instance()->features->is_feature_active('wdg_pdf')) {
            add_submenu_page('dce-features', DCE_PRODUCT_NAME . ' - ' . esc_html__('HTML Templates', 'dynamic-content-for-elementor'), esc_html__('HTML Templates', 'dynamic-content-for-elementor'), 'manage_options', 'edit.php?post_type=' . \DynamicContentForElementor\PdfHtmlTemplates::CPT);
        }
        // Integrations
        add_submenu_page('dce-features', DCE_PRODUCT_NAME . ' - ' . esc_html__('Integrations', 'dynamic-content-for-elementor'), esc_html__('Integrations', 'dynamic-content-for-elementor'), 'manage_options', 'dce-integrations', $this->middleware_for_requirements([$this->integrations, 'render_page']));
        // License
        add_submenu_page('dce-features', DCE_PRODUCT_NAME . ' - ' . esc_html__('License', 'dynamic-content-for-elementor'), esc_html__('License', 'dynamic-content-for-elementor'), 'administrator', 'dce-license', $this->middleware_for_requirements([Plugin::instance()->plugin_utils->admin_pages->license_page, 'render_page']));
    }
    /**
     * @return void
     */
    public function warning_lazyload()
    {
        $lazyload = \Elementor\Plugin::instance()->experiments->is_feature_active('e_lazyload');
        if ($lazyload) {
            $msg = \sprintf(
                /* translators: %s: plugin name */
                esc_html__('The Elementor Experiment Lazy Load is not currently compatible with all %s features, in particular it causes problems with background images inside a loop.', 'dynamic-content-for-elementor'),
                DCE_PRODUCT_NAME
            );
            if (current_user_can('manage_options')) {
                $this->notices->warning($msg, 'lazyload');
            }
        }
    }
    /**
     * @return void
     */
    public function warning_features_bloat()
    {
        if (isset($_POST['save-dce-feature'])) {
            return;
            // settings are being saved, we can't be sure of the feature status.
        }
        $features = \DynamicContentForElementor\Plugin::instance()->features->filter(['legacy' => \true], 'NOT');
        $active = \array_filter($features, function ($f) {
            return $f['status'] === 'active';
        });
        $ratio = \count($active) / \count($features);
        if ($ratio > 0.95) {
            $msg = esc_html__('Most features are currently active. This could slow down the Elementor Editor. It is recommended that you disable the features you don\'t need. This can be done on the ', 'dynamic-content-for-elementor');
            $url = admin_url('admin.php?page=dce-features');
            $msg .= "<a href='{$url}'>" . esc_html__('Features Page', 'dynamic-content-for-elementor') . '</a>.';
            $this->notices->warning($msg, 'features_bloat3');
        }
    }
    /**
     * @param array<string,string> $args
     * @return void
     */
    public function show_message($args)
    {
        $args = wp_parse_args($args, ['class' => 'dce-message', 'title' => '', 'message' => '']);
        echo '<div class="' . esc_attr($args['class']) . '">';
        if (!empty($args['title'])) {
            echo '<h2>' . esc_html($args['title']) . '</h2>';
        }
        echo '<p>' . esc_html($args['message']) . '</p>';
        echo '</div>';
    }
    /**
     * @param string $message
     * @return void
     */
    public function show_error($message)
    {
        $this->show_message(['class' => 'dce-error-content', 'title' => __('Error', 'dynamic-content-for-elementor'), 'message' => $message]);
    }
    /**
     * @return ?string
     */
    public function error_requirements()
    {
        if (\version_compare(\phpversion(), DCE_MINIMUM_PHP_VERSION, '<')) {
            return \sprintf(__('Your PHP version (%1$s) is below the minimum required version (%2$s).', 'dynamic-content-for-elementor'), \phpversion(), DCE_MINIMUM_PHP_VERSION);
        }
        if (\version_compare(ELEMENTOR_VERSION, DCE_MINIMUM_ELEMENTOR_VERSION, '<')) {
            return \sprintf(__('Your Elementor version (%1$s) is below the minimum required version (%2$s).', 'dynamic-content-for-elementor'), ELEMENTOR_VERSION, DCE_MINIMUM_ELEMENTOR_VERSION);
        }
        if (\defined('ELEMENTOR_PRO_VERSION') && \version_compare(ELEMENTOR_PRO_VERSION, DCE_MINIMUM_ELEMENTOR_PRO_VERSION, '<')) {
            return \sprintf(__('Your Elementor Pro version (%1$s) is below the minimum required version (%2$s).', 'dynamic-content-for-elementor'), ELEMENTOR_PRO_VERSION, DCE_MINIMUM_ELEMENTOR_PRO_VERSION);
        }
        return null;
    }
    /**
     * @param callable $callback
     * @return callable
     */
    protected function middleware_for_requirements($callback)
    {
        $error_req = $this->error_requirements();
        return function () use($callback, $error_req) {
            if (!$error_req) {
                $callback();
            } else {
                $this->show_error($error_req);
            }
        };
    }
}
