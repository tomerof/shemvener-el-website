<?php
/**
 * Plugin Name: Menora Payment Integration
 * Description: Adds a "Menora Payment" action to Elementor Pro forms. After a form is submitted, it creates an external order on Menora's payment API and shows the customer a button to pay on Menora's site.
 * Version: 1.0.0
 * Author: Shemvener
 * Text Domain: menora-payment-integration
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'MENORA_PAYMENT_INTEGRATION_VERSION', '1.0.0' );
define( 'MENORA_PAYMENT_INTEGRATION_PATH', plugin_dir_path( __FILE__ ) );
define( 'MENORA_PAYMENT_INTEGRATION_URL', plugin_dir_url( __FILE__ ) );

/**
 * Register the "Menora Payment" action with Elementor Pro forms.
 *
 * The requires are deferred to inside this callback so the plugin never
 * fatals when Elementor Pro (and its Action_Base class) isn't active -
 * this hook simply never fires in that case.
 */
add_action(
	'elementor_pro/forms/actions/register',
	function ( $form_actions_registrar ) {
		require_once MENORA_PAYMENT_INTEGRATION_PATH . 'includes/class-menora-api-client.php';
		require_once MENORA_PAYMENT_INTEGRATION_PATH . 'includes/class-menora-form-action.php';

		$form_actions_registrar->register( new \MenoraPaymentIntegration\Form_Action() );
	}
);

/**
 * Admin notice when required plugins are missing.
 */
add_action(
	'admin_notices',
	function () {
		if ( ! current_user_can( 'activate_plugins' ) ) {
			return;
		}

		$missing = [];

		if ( ! did_action( 'elementor/loaded' ) ) {
			$missing[] = 'Elementor';
		}

		if ( ! class_exists( '\ElementorPro\Plugin' ) ) {
			$missing[] = 'Elementor Pro';
		}

		if ( empty( $missing ) ) {
			return;
		}

		printf(
			'<div class="notice notice-warning"><p>%s</p></div>',
			esc_html(
				sprintf(
					/* translators: %s: comma separated list of missing plugins */
					__( 'Menora Payment Integration requires %s to be installed and active.', 'menora-payment-integration' ),
					implode( ', ', $missing )
				)
			)
		);
	}
);

/**
 * Front-end assets: renders the payment button after a successful submission.
 */
add_action(
	'wp_enqueue_scripts',
	function () {
		if ( is_admin() ) {
			return;
		}

		$deps = [ 'jquery' ];

		if ( wp_script_is( 'elementor-pro-frontend', 'registered' ) ) {
			$deps[] = 'elementor-pro-frontend';
		}

		wp_enqueue_script(
			'menora-payment-integration',
			MENORA_PAYMENT_INTEGRATION_URL . 'assets/js/menora-payment.js',
			$deps,
			MENORA_PAYMENT_INTEGRATION_VERSION,
			true
		);

		wp_enqueue_style(
			'menora-payment-integration',
			MENORA_PAYMENT_INTEGRATION_URL . 'assets/css/menora-payment.css',
			[],
			MENORA_PAYMENT_INTEGRATION_VERSION
		);
	}
);
