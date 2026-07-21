<?php

// SPDX-FileCopyrightText: 2018-2026 Ovation S.r.l. <help@dynamic.ooo>
// SPDX-License-Identifier: GPL-3.0-or-later
namespace DynamicContentForElementor;

use DynamicContentForElementor\Helper;
use DynamicContentForElementor\Features;
use DynamicContentForElementor\Core\Upgrade\Manager as UpgradeManager;
use DynamicContentForElementor\Core\Settings\UpdateChecker;
use DynamicContentForElementor\Integrations;
if (!\defined('ABSPATH')) {
    exit;
    // Exit if accessed directly.
}
/**
 * Main Plugin Class
 *
 * @since 0.0.1
 */
class Plugin
{
    /**
     * @var string
     */
    public $prefix;
    /**
     * @var \DynamicContentForElementor\Controls
     */
    public $controls;
    /**
     * @var \DynamicContentForElementor\Widgets
     */
    public $widgets;
    /**
     * @var \DynamicOOO\PluginUtils\Manager
     */
    public $plugin_utils;
    /**
     * @var \DynamicContentForElementor\Features
     */
    public $features;
    /**
     * @var \DynamicContentForElementor\SaveGuard
     */
    public $save_guard;
    /**
     * @var \DynamicContentForElementor\TextTemplates\Manager
     */
    public $text_templates;
    /**
     * @var \DynamicContentForElementor\Cryptocurrency
     */
    public $cryptocurrency;
    /**
     * @var \DynamicContentForElementor\PdfHtmlTemplates
     */
    public $pdf_html_templates;
    /**
     * @var \DynamicContentForElementor\TemplateSystem
     */
    public $template_system;
    /**
     * @var \DynamicContentForElementor\Wpml
     */
    public $wpml;
    /**
     * @var \DynamicContentForElementor\Stripe
     */
    public $stripe;
    /**
     * @var AdminPages\Manager
     */
    public $admin_pages;
    /**
     * @var \DynamicContentForElementor\PageSettings
     */
    public $page_settings;
    /**
     * @var \DynamicContentForElementor\Extensions
     */
    public $extensions;
    /**
     * @var \DynamicOOO\PluginUtils\LicenseInterface
     */
    public $license_system;
    /**
     * @var \DynamicContentForElementor\Integrations\Manager
     */
    public $integrations;
    public $assets;
    protected static $instance;
    /**
     * @var UpgradeManager
     */
    public $upgrade;
    public function __construct()
    {
        self::$instance = $this;
        $this->init();
    }
    /**
     * @return Plugin
     */
    public static function instance()
    {
        if (\is_null(self::$instance)) {
            // Ensure Elementor Free is active
            if (!did_action('elementor/loaded')) {
                add_action('admin_notices', 'dce_fail_load');
                // having a function return ?Plugin would put too much burden on the code:
                // @phpstan-ignore return.type
                return null;
            }
            // Check Elementor version
            if (\version_compare(ELEMENTOR_VERSION, DCE_MINIMUM_ELEMENTOR_VERSION, '<')) {
                add_action('admin_notices', 'dce_admin_notice_minimum_elementor_version');
                // @phpstan-ignore return.type
                return null;
            }
            // Check Elementor Pro version
            if (\defined('ELEMENTOR_PRO_VERSION') && \version_compare(ELEMENTOR_PRO_VERSION, DCE_MINIMUM_ELEMENTOR_PRO_VERSION, '<')) {
                add_action('admin_notices', 'dce_admin_notice_minimum_elementor_pro_version');
                // @phpstan-ignore return.type
                return null;
            }
            new self();
        }
        return self::$instance;
    }
    /**
     * @return void
     */
    public function init()
    {
        if (!\class_exists('\\DynamicOOO\\PluginUtils\\Manager')) {
            require_once __DIR__ . '/../plugin-utils/manager.php';
        }
        $this->init_managers();
        add_action('init', function () {
            load_plugin_textdomain('dynamic-content-for-elementor');
            // Enchanted Tab for Elementor Pro Form
            $features_enchanted = self::instance()->features->filter_by_tag('enchanted');
            $active_features_enchanted = \array_filter($features_enchanted, function ($e) {
                return 'active' === $e['status'];
            });
            if (!empty($active_features_enchanted)) {
                add_action('elementor/element/form/section_form_fields/before_section_end', [$this, 'add_form_fields_enchanted_tab']);
            }
        });
        add_action('elementor/init', [$this, 'add_dce_to_elementor'], 10);
        add_filter('pre_handle_404', '\\DynamicContentForElementor\\Helper::maybe_allow_posts_pagination', 1, 2);
    }
    /**
     * @return void
     */
    public function init_managers()
    {
        $this->prefix = DCE_ID;
        $this->plugin_utils = new \DynamicOOO\PluginUtils\Manager(['plugin_base' => DCE_PLUGIN_BASE, 'plugin_slug' => DCE_SLUG, 'version' => DCE_VERSION, 'plugin_file' => DCE__FILE__, 'plugin_name_underscored' => 'dynamic_content_for_elementor', 'license_url' => DCE_LICENSE_URL, 'prefix' => DCE_ID, 'product_unique_id' => DCE_PRODUCT_UNIQUE_ID, 'product_name_long' => DCE_PRODUCT_NAME_LONG, 'pricing_url' => DCE_PRICING_URL, 'supports_beta' => \true, 'supports_rollback' => \true, 'action_links' => ['features' => ['label' => 'Features', 'url' => 'admin.php?page=dce-features']]]);
        $this->license_system = $this->plugin_utils->license;
        $this->save_guard = new \DynamicContentForElementor\SaveGuard();
        $this->features = new Features();
        $this->controls = new \DynamicContentForElementor\Controls();
        $this->widgets = new \DynamicContentForElementor\Widgets();
        $this->stripe = new \DynamicContentForElementor\Stripe();
        $this->pdf_html_templates = new \DynamicContentForElementor\PdfHtmlTemplates();
        $this->admin_pages = new \DynamicContentForElementor\AdminPages\Manager($this->plugin_utils);
        $this->cryptocurrency = new \DynamicContentForElementor\Cryptocurrency();
        $this->text_templates = new \DynamicContentForElementor\TextTemplates\Manager();
        new \DynamicContentForElementor\Ajax();
        $this->assets = new \DynamicContentForElementor\Assets();
        $this->wpml = new \DynamicContentForElementor\Wpml();
        $this->template_system = new \DynamicContentForElementor\TemplateSystem();
        $this->integrations = new Integrations\Manager();
        // Init hook
        do_action('dynamic_content_for_elementor/init');
    }
    /**
     * Activation fired by 'register_activation_hook'
     *
     * @return void
     */
    public static function activation()
    {
        set_transient('dce_activation_redirect', \true, MINUTE_IN_SECONDS);
    }
    /**
     * Uninstall fired by 'register_uninstall_hook'
     *
     * @return void
     */
    public static function uninstall()
    {
        self::instance()->license_system->deactivate_license();
        // If the deactivation request returns an error the license key is not removed, so it's better to remove the key manually
        delete_option('dce_license_key');
        self::instance()->plugin_utils->cron->clear_all_tasks();
        if (\defined('DCE_REMOVE_ALL_DATA') && DCE_REMOVE_ALL_DATA) {
            delete_option(DCE_TEMPLATE_SYSTEM_OPTION);
            delete_option(Features::FEATURES_STATUS_OPTION);
        }
    }
    /**
     * @return void
     */
    public function add_dce_to_elementor()
    {
        // Global Settings Panel
        \DynamicContentForElementor\GlobalSettings::init();
        $this->upgrade = UpgradeManager::instance();
        // Controls
        add_action('elementor/controls/controls_registered', [$this->controls, 'on_controls_registered']);
        // Force Dynamic Tags
        if (!\defined('DCE_NO_CM_OVERRIDE') || !DCE_NO_CM_OVERRIDE) {
            \Elementor\Plugin::$instance->controls_manager = new \DynamicContentForElementor\ForceDynamicTags();
        }
        // Extensions
        $this->extensions = new \DynamicContentForElementor\Extensions();
        // Page Settings
        $this->page_settings = new \DynamicContentForElementor\PageSettings();
        $this->page_settings->on_page_settings_registered();
        // Widgets
        add_action('elementor/widgets/register', [$this->widgets, 'on_widgets_registered']);
        do_action('dynamic-content-for-elementor/elementor-init', $this);
    }
    /**
     * Add Enchanted Tab for Form Fields
     *
     * This form tab is used for many extensions. We put it here avoiding repetition
     *
     * @param \Elementor\Widget_Base $widget
     * @return void
     */
    public function add_form_fields_enchanted_tab(\Elementor\Widget_Base $widget)
    {
        $elementor = \ElementorPro\Plugin::elementor();
        $control_data = $elementor->controls_manager->get_control_from_stack($widget->get_unique_name(), 'form_fields');
        if (is_wp_error($control_data)) {
            return;
        }
        $field_controls = ['form_fields_enchanted_tab' => ['type' => 'tab', 'tab' => 'enchanted', 'label' => '<i class="dynicon icon-dce-logo-dce" aria-hidden="true"></i>', 'tabs_wrapper' => 'form_fields_tabs', 'name' => 'form_fields_enchanted_tab', 'condition' => ['field_type!' => 'step']]];
        $control_data['fields'] = \array_merge($control_data['fields'], $field_controls);
        $widget->update_control('form_fields', $control_data);
    }
}
\DynamicContentForElementor\Plugin::instance();
