<?php

// SPDX-FileCopyrightText: 2018-2026 Ovation S.r.l. <help@dynamic.ooo>
// SPDX-License-Identifier: GPL-3.0-or-later
namespace DynamicContentForElementor\TextTemplates;

use DynamicContentForElementor\Helper;
use DynamicContentForElementor\Tokens;
class Manager
{
    /**
     * @var array<string,mixed>
     */
    public $expand_data = [];
    const URL_MIGRATE_TOKENS_TO_DSH = 'https://dnmc.ooo/dce-tokens-migration';
    const URL_DSH_INSTALLATION = 'https://dnmc.ooo/dsh-dce-install';
    /**
     * @var \DynamicContentForElementor\TextTemplates\DynamicShortcodes\Manager
     */
    public $dce_shortcodes;
    /**
     * @var \DynamicContentForElementor\TextTemplates\Timber\Manager
     */
    public $timber;
    public function __construct()
    {
        $this->dce_shortcodes = new \DynamicContentForElementor\TextTemplates\DynamicShortcodes\Manager();
        $this->timber = new \DynamicContentForElementor\TextTemplates\Timber\Manager();
    }
    /**
     * @return array<string,mixed>
     */
    public function get_notice_content()
    {
        $is_tokens_active = \DynamicContentForElementor\Tokens::is_active();
        $is_dynamic_shortcodes_installed = $this->dce_shortcodes->is_dsh_active();
        if ($is_tokens_active && $is_dynamic_shortcodes_installed) {
            return ['case' => 'both', 'notice_type' => 'warning', 'heading' => esc_html__('Use Dynamic Shortcodes Only!', 'dynamic-content-for-elementor'), 'content' => \strtr(esc_html__('Tokens is now deprecated. Enhance your site by using only Dynamic Shortcodes, which you already have active and deactivate Tokens. %1$sLearn how to migrate%2$s.', 'dynamic-content-for-elementor'), ['%1$s' => '<a href="' . self::URL_MIGRATE_TOKENS_TO_DSH . '">', '%2$s' => '</a>'])];
        } elseif ($is_tokens_active) {
            return ['case' => 'tokens_only', 'notice_type' => 'danger', 'heading' => esc_html__('Recommended Update', 'dynamic-content-for-elementor'), 'content' => \strtr(
                /* translators: 1: opening link tag, 2: closing link tag, 3: plugin name */
                esc_html__('Tokens is deprecated. Dynamic Shortcodes, included in your %3$s license, offers more advanced features and better efficiency. %1$sInstall Dynamic Shortcodes now%2$s.', 'dynamic-content-for-elementor'),
                ['%1$s' => '<a href="' . self::URL_DSH_INSTALLATION . '">', '%2$s' => '</a>', '%3$s' => DCE_PRODUCT_NAME]
            ) . $this->maybe_get_install_dsh()];
        } elseif ($is_dynamic_shortcodes_installed) {
            return [];
        } else {
            return ['case' => 'none', 'notice_type' => 'danger', 'heading' => esc_html__('Installation Required', 'dynamic-content-for-elementor'), 'content' => \strtr(
                /* translators: 1: opening link tag, 2: closing link tag, 3: plugin name */
                esc_html__('Dynamic Shortcodes, which is included in your %3$s license, is not currently installed. %1$sInstall Dynamic Shortcodes%2$s to enhance your experience with Elementor.', 'dynamic-content-for-elementor'),
                ['%1$s' => '<a href="' . self::URL_DSH_INSTALLATION . '">', '%2$s' => '</a>', '%3$s' => DCE_PRODUCT_NAME]
            ) . $this->maybe_get_install_dsh()];
        }
    }
    /**
     * @return void|array<string,mixed>
     */
    public function get_notice_html_templates()
    {
        $is_timber_installed = Helper::is_plugin_active('timber');
        $is_dynamic_shortcodes_installed = $this->dce_shortcodes->is_dsh_active();
        if ($is_timber_installed && $is_dynamic_shortcodes_installed) {
            return ['case' => 'both', 'notice_type' => 'warning', 'heading' => esc_html__('Use Dynamic Shortcodes Only!', 'dynamic-content-for-elementor'), 'content' => esc_html__('PDF creation with Timber plugin is deprecated, but we will not remove the integration. Please use only Dynamic Shortcodes, which you already have active and deactivate Timber.', 'dynamic-content-for-elementor'), 'required' => \false];
        } elseif ($is_timber_installed) {
            return ['case' => 'timber_only', 'notice_type' => 'danger', 'heading' => esc_html__('Recommended Update', 'dynamic-content-for-elementor'), 'content' => \strtr(
                /* translators: 1: opening link tag, 2: closing link tag, 3: plugin name */
                esc_html__('PDF creation with Timber plugin is deprecated, but we will not remove the integration. Please use Dynamic Shortcodes, included in your %3$s license. %1$sInstall Dynamic Shortcodes now%2$s.', 'dynamic-content-for-elementor'),
                ['%1$s' => '<a href="' . self::URL_DSH_INSTALLATION . '">', '%2$s' => '</a>', '%3$s' => DCE_PRODUCT_NAME]
            ), 'required' => \false];
        } elseif ($is_dynamic_shortcodes_installed) {
            return;
        } else {
            return ['case' => 'none', 'notice_type' => 'danger', 'heading' => esc_html__('Installation Required', 'dynamic-content-for-elementor'), 'content' => \strtr(
                /* translators: 1: opening link tag, 2: closing link tag, 3: plugin name */
                esc_html__('Dynamic Shortcodes, which is included in your %3$s license, is not currently installed. %1$sInstall Dynamic Shortcodes%2$s to create your PDF.', 'dynamic-content-for-elementor'),
                ['%1$s' => '<a href="' . self::URL_DSH_INSTALLATION . '">', '%2$s' => '</a>', '%3$s' => DCE_PRODUCT_NAME]
            ), 'required' => \true];
        }
    }
    /**
     * @return string
     */
    protected function maybe_get_install_dsh()
    {
        if (!\function_exists('get_plugins')) {
            require_once ABSPATH . 'wp-admin/includes/plugin.php';
        }
        if (isset(\get_plugins()['dynamic-shortcodes/dynamic-shortcodes.php'])) {
            return '';
        }
        $url = wp_nonce_url(admin_url('admin-post.php?action=dce_install_dsh'), 'dce_install_dsh', 'dce_install_dsh_nonce');
        $install_dsh = esc_html__('Install Dynamic Shortcodes', 'dynamic-content-for-elementor');
        return '<a href="' . esc_url($url) . '" target="_blank" class="e-btn e-info e-btn-1">' . $install_dsh . '</a>';
    }
    /**
     * @param \Elementor\Controls_Stack $widget
     * @param string $prefix
     * @param array<string,mixed> $condition
     * @return void
     */
    public function maybe_add_notice($widget, $prefix = '', $condition = [])
    {
        $notice = $this->get_notice_content();
        if (!empty($notice)) {
            $widget->add_control($prefix . 'dsh_notice', ['type' => \Elementor\Controls_Manager::NOTICE, 'notice_type' => $notice['notice_type'], 'dismissible' => $notice['dismissible'] ?? \false, 'heading' => $notice['heading'], 'content' => $notice['content'], 'condition' => $condition]);
        }
    }
    /**
     * @param array<string,string> $values
     * @return string
     */
    public function get_default_value($values)
    {
        if (Helper::is_plugin_active('dynamic-shortcodes')) {
            return $values['dynamic-shortcodes'] ?? '';
        }
        if (Tokens::is_active()) {
            return $values['tokens'] ?? '';
        }
        return '';
    }
    /**
     * @param array<string,mixed> $atts
     * @return string
     */
    public function field_shortcode($atts)
    {
        if (!isset($atts['id'])) {
            return '';
        }
        if (!isset($this->expand_data['form-fields'])) {
            return '';
        }
        $fields = $this->expand_data['form-fields'];
        if (!isset($fields[$atts['id']])) {
            return '';
        }
        return $fields[$atts['id']]['value'];
    }
    /**
     * @return void
     */
    public function ensure_wp_shortcodes()
    {
        if (shortcode_exists('field')) {
            return;
        }
        add_shortcode('field', [$this, 'field_shortcode']);
    }
    /**
     * @param string $str
     * @param array<mixed> $data
     * @param Callable|null $callback
     * @return mixed
     */
    public function expand_shortcodes_or_callback($str, $data, $callback)
    {
        // Track [form:...] token usage for detector (before expansion)
        // These tokens don't pass through do_tokens(), so track them here
        if (\is_string($str) && !empty($str) && \strpos($str, '[form:') !== \false) {
            \DynamicContentForElementor\Tokens::track_token_usage($str);
        }
        $original_str = $str;
        if (\is_string($str)) {
            $modified_str = $this->dce_shortcodes->expand_with_data($str, $data);
            if ($modified_str !== $original_str) {
                return $modified_str;
            }
        }
        $this->ensure_wp_shortcodes();
        $this->expand_data = $data;
        if (!\is_callable($callback)) {
            if (\is_string($str)) {
                $str = do_shortcode($str);
            }
        } else {
            $str = $callback($str, $data);
        }
        return $str;
    }
}
