<?php

// SPDX-FileCopyrightText: 2018-2026 Ovation S.r.l. <help@dynamic.ooo>
// SPDX-License-Identifier: GPL-3.0-or-later
namespace DynamicContentForElementor\Extensions\DynamicVisibility\Triggers;

use Elementor\Controls_Manager;
use DynamicContentForElementor\Helper;
class Custom extends \DynamicContentForElementor\Extensions\DynamicVisibility\Triggers\Base
{
    //+exclude_start
    const CUSTOM_PHP_CONTROL_NAME = 'dce_visibility_custom_condition_php';
    //+exclude_end
    /**
     * @param \Elementor\Element_Base $element
     * @return void
     */
    public function register_controls($element)
    {
        if (!Helper::is_plugin_active('dynamic-content-for-elementor')) {
            //  Feature not available in FREE version
            $placeholders = ['%1$s' => '<strong>', '%2$s' => '</strong>'];
            $content = \strtr(__('%1$sUnlock 150+ powerful features%2$s including Custom PHP conditions, Dynamic Tags, Widgets, Extensions and more.', 'dynamic-content-for-elementor'), $placeholders) . '<br />';
            $content .= \strtr(__('Save 10&#37; with promo code %1$sVISIBILITY%2$s', 'dynamic-content-for-elementor'), $placeholders);
            $upgrade_url = 'https://www.dynamic.ooo/upgrade/visibility-to-premium?utm_source=wp-plugins&utm_campaign=custom-php&utm_medium=editor-notice';
            $content .= \sprintf('<br /><br /><a href="%s" target="_blank" style="font-weight: 500;">%s &rarr;</a>', esc_url($upgrade_url), esc_html__('Upgrade Now', 'dynamic-content-for-elementor'));
            $element->add_control('dce_visibility_custom_hide', ['type' => Controls_Manager::NOTICE, 'notice_type' => 'warning', 'heading' => esc_html__('This is a Premium Feature', 'dynamic-content-for-elementor'), 'content' => $content]);
        } elseif (Helper::can_register_unsafe_controls()) {
            $element->add_control(self::CUSTOM_PHP_CONTROL_NAME, ['label' => esc_html__('Custom PHP condition', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CODE, 'language' => 'php', 'default' => '', 'description' => esc_html__('Type here a function that returns a boolean value. You can use all WP variables and functions.', 'dynamic-content-for-elementor'), 'ai' => ['active' => \false]]);
        }
        //+exclude_end
    }
    /**
     * @param array<string,mixed> $settings
     * @param array<string,mixed> &$triggers
     * @param array<string,mixed> &$conditions
     * @param array<string,mixed> &$required
     * @param \Elementor\Element_Base $element
     * @return void
     */
    public function check_conditions($settings, &$triggers, &$conditions, &$required, $element)
    {
        //+exclude_start
        if (!isset($settings['dce_visibility_custom_condition']) || !$settings['dce_visibility_custom_condition']) {
            if (isset($settings[self::CUSTOM_PHP_CONTROL_NAME]) && \preg_match('/\\S/', $settings[self::CUSTOM_PHP_CONTROL_NAME])) {
                $triggers['custom'] = esc_html__('Custom Condition', 'dynamic-content-for-elementor');
                $required['custom'] = \true;
                $customhidden = $this->check_custom_condition($settings);
                if ($customhidden) {
                    $conditions['custom'] = esc_html__('Custom Condition', 'dynamic-content-for-elementor');
                }
            }
        }
        //+exclude_end
    }
    /**
     * @param array<string,mixed> $settings
     * @return boolean
     */
    protected function check_custom_condition($settings)
    {
        //+exclude_start
        if (!Helper::can_register_unsafe_controls()) {
            return \false;
        }
        $php_code = $settings[self::CUSTOM_PHP_CONTROL_NAME];
        // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Read-only debug flag, no state change
        if (current_user_can('manage_options') && !empty($_GET['dce_disable_visibility_custom_conditions'])) {
            echo esc_html__('Dynamic Visibility: Custom Condition found, but custom conditions are disabled.', 'dynamic-content-for-elementor');
            return \false;
        }
        if (\is_string($php_code)) {
            try {
                return eval($php_code);
            } catch (\ParseError $e) {
                if (current_user_can('administrator')) {
                    Helper::notice(\false, esc_html__('This message is visible only for Administrators', 'dynamic-content-for-elementor'));
                    echo '<pre>Dynamic Visibility - Custom Condition: ', esc_html($e->getMessage()), '</pre>';
                }
            } catch (\Throwable $e) {
                if (current_user_can('administrator')) {
                    Helper::notice(\false, esc_html__('This message is visible only for Administrators', 'dynamic-content-for-elementor'));
                    echo '<pre>Dynamic Visibility - Custom Condition: ', esc_html($e->getMessage()), '</pre>';
                }
            }
        }
        //+exclude_end
        return \false;
    }
}
