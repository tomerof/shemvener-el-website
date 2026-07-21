<?php

// SPDX-FileCopyrightText: 2018-2026 Ovation S.r.l. <help@dynamic.ooo>
// SPDX-License-Identifier: GPL-3.0-or-later
/**
 *
 * @copyright Copyright (C) 2018-2026, Ovation S.r.l. - help@dynamic.ooo
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License, version 3 or higher
 *
 * @wordpress-plugin
 * Plugin Name: Dynamic.ooo - Dynamic Content for Elementor
 * Plugin URI: https://www.dynamic.ooo/dynamic-content-for-elementor?utm_source=wp-plugins&utm_campaign=plugin-uri&utm_medium=wp-dash
 * Description: Building powerful websites by extending Elementor. We give you over 150 features that will save you time and money on achieving complex results. The only limit is your imagination.
 * Version: 3.4.12
 * Requires at least: 6.3
 * Requires PHP: 7.4
 * Requires Plugins: elementor
 * Author: Dynamic.ooo
 * Author URI: https://www.dynamic.ooo/?utm_source=wp-plugins&utm_campaign=plugin-uri&utm_medium=wp-dash
 * Text Domain: dynamic-content-for-elementor
 * Domain Path: /languages
 * License: GPL-3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.txt
 * Elementor tested up to: 4.1.4
 * Elementor Pro tested up to: 4.1.2
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

define( 'DCE_PLUGIN_BASE', plugin_basename( __FILE__ ) ); // {dce-folder}/{current-file}
define( 'DCE__FILE__', __FILE__ ); // {path}/wp-content/plugins/{dce-folder}/{current-file}
define( 'DCE_URL', plugins_url( '/', __FILE__ ) ); // {site}/wp-content/plugins/{dce-folder}
define( 'DCE_PATH', plugin_dir_path( __FILE__ ) ); // {path}/wp-content/plugins/{dce-folder}

require_once __DIR__ . '/constants.php';

if ( ! class_exists( '\DynamicOOO\PluginUtils\AdminPages\AdminNotices' ) ) {
	require_once __DIR__ . '/plugin-utils/admin-pages/admin-notices.php';
}

// Admin Style - Load it now because we could display a notice for PHP version and the plugin is not loaded
add_action( 'admin_enqueue_scripts', function () {
	wp_register_style( 'dce-admin', DCE_URL . 'assets/css/admin.css', [], DCE_VERSION );
	wp_enqueue_style( 'dce-admin' );
});

// Check PHP version
if ( version_compare( phpversion(), DCE_MINIMUM_PHP_VERSION, '<' ) ) {
	add_action( 'admin_notices', 'dce_admin_notice_minimum_php_version' );
	return;
} elseif ( version_compare( phpversion(), DCE_SUGGESTED_PHP_VERSION, '<' ) ) {
	add_action( 'admin_notices', 'dce_admin_notice_suggest_new_php_version' );
} elseif ( version_compare( phpversion(), strval( DCE_MAXIMUM_PHP_VERSION + 0.1 ), '>=' ) ) {
	add_action( 'admin_notices', 'dce_admin_notice_maximum_php_version' );
}

require_once DCE_PATH . 'vendor/autoload.php';

// Fix the str_contains function in php 8.0 polyfill by Symfony. Its
// behaviour is actually different from that of the function in php 8.0.
// In php 8.0 there are no errors on wrong type:
if ( ! function_exists( 'str_contains' ) ) {
	function str_contains( $haystack, $needle ) {
		if ( ! is_string( $haystack ) || ! is_string( $needle ) ) {
			return false;
		}
		/** @phpstan-ignore class.notFound */
		return \DynamicOOOS\Symfony\Polyfill\Php80\Php80::str_contains( $haystack, $needle );
	}
}

require_once __DIR__ . '/vendor/symfony/polyfill-php80/bootstrap.php';

register_activation_hook( DCE_PLUGIN_BASE, function () {
	set_transient( 'dce_activation_redirect', true, MINUTE_IN_SECONDS );
});

register_uninstall_hook( DCE_PLUGIN_BASE, '\DynamicContentForElementor\Plugin::uninstall' );

add_action( 'plugins_loaded', 'dce_load' );

/**
 * Load Dynamic.ooo - Dynamic Content for Elementor
 *
 * Load the plugin after Elementor is loaded.
 *
 * @since 0.1.0
 */
function dce_load() {
	require_once DCE_PATH . '/core/plugin.php';
}

/**
 * Handles admin notice for non-active Elementor plugin situations
 *
 * @return void
 */
function dce_fail_load() {
	/* translators: %1$s: opening strong tag, %2$s: closing strong tag, %3$s: product name */
	$msg = sprintf( esc_html__( '%1$sElementor%2$s is required for the %1$s%3$s%2$s plugin to work.', 'dynamic-content-for-elementor' ), '<strong>', '</strong>', DCE_PRODUCT_NAME_LONG );
	\DynamicOOO\PluginUtils\AdminPages\AdminNotices::print_notice( $msg, 'notice-error', DCE_PRODUCT_NAME_LONG );
}

function dce_admin_notice_minimum_elementor_version() {
	/* translators: %1$s: product name, %2$s: minimum Elementor version */
	$msg = sprintf( esc_html__( '%1$s requires Elementor version %2$s or greater.', 'dynamic-content-for-elementor' ), DCE_PRODUCT_NAME_LONG, DCE_MINIMUM_ELEMENTOR_VERSION );
	\DynamicOOO\PluginUtils\AdminPages\AdminNotices::print_notice( $msg, 'notice-error', DCE_PRODUCT_NAME_LONG );
}

function dce_admin_notice_minimum_elementor_pro_version() {
	/* translators: %1$s: product name, %2$s: minimum Elementor Pro version */
	$msg = sprintf( esc_html__( 'If you want to use Elementor Pro with %1$s, it requires version %2$s or greater.', 'dynamic-content-for-elementor' ), DCE_PRODUCT_NAME_LONG, DCE_MINIMUM_ELEMENTOR_PRO_VERSION );
	\DynamicOOO\PluginUtils\AdminPages\AdminNotices::print_notice( $msg, 'notice-error', DCE_PRODUCT_NAME_LONG );
}

function dce_admin_notice_minimum_php_version() {
	/* translators: %1$s: current PHP version, %2$s: minimum required PHP version */
	$msg = sprintf( esc_html__( 'You are using PHP version %1$s. This version is not more supported. Ask your provider to use PHP version %2$s+.', 'dynamic-content-for-elementor' ), phpversion(), DCE_MINIMUM_PHP_VERSION );
	\DynamicOOO\PluginUtils\AdminPages\AdminNotices::print_notice( $msg, 'notice-error', DCE_PRODUCT_NAME_LONG );
}

function dce_admin_notice_maximum_php_version() {
	if ( isset( $_GET['page'] ) && 'dce-features' === $_GET['page'] ) {
		/* translators: %1$s: current PHP version, %2$s: maximum supported PHP version */
		$msg = sprintf( esc_html__( 'You are using PHP version %1$s and it\'s not yet fully supported. The maximum version supported is %2$s.', 'dynamic-content-for-elementor' ), phpversion(), DCE_MAXIMUM_PHP_VERSION );
		\DynamicOOO\PluginUtils\AdminPages\AdminNotices::print_notice( $msg, 'notice-error', DCE_PRODUCT_NAME_LONG );
	}
}

function dce_admin_notice_suggest_new_php_version() {
	if ( isset( $_GET['page'] ) && 'dce-features' === $_GET['page'] ) {
		/* translators: %1$s: current PHP version, %2$s: suggested PHP version */
		$msg = sprintf( esc_html__( 'You are using PHP version %1$s. It\'s suggested to use PHP version %2$s+.', 'dynamic-content-for-elementor' ), phpversion(), DCE_SUGGESTED_PHP_VERSION );
		\DynamicOOO\PluginUtils\AdminPages\AdminNotices::print_notice( $msg, 'notice-warning', DCE_PRODUCT_NAME_LONG );
	}
}
