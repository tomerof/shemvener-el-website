<?php
namespace MenoraPaymentIntegration;

use Elementor\Controls_Manager;
use ElementorPro\Modules\Forms\Classes\Action_Base;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * "Menora Payment" action for Elementor Pro forms.
 *
 * Add this action from the form's "Actions After Submit" list. On submit it
 * creates an order on Menora's payment API and passes the resulting payment
 * link back to the browser, where the front-end script (assets/js/menora-payment.js)
 * renders a "מעבר לתשלום באתר מנורה" button next to the success message.
 */
class Form_Action extends Action_Base {

	/**
	 * Custom IDs of the form fields we read from - consistent across both
	 * candle-order forms, so these aren't exposed as per-form settings.
	 */
	const NAME_FIELD_ID   = 'name';
	const LNAME_FIELD_ID  = 'lname';
	const PHONE_FIELD_ID  = 'tel';
	const EMAIL_FIELD_ID  = 'email';
	const AMOUNT_FIELD_ID = 'total_val';

	public function get_name() {
		return 'menora_payment';
	}

	public function get_label() {
		return esc_html__( 'Menora Payment', 'menora-payment-integration' );
	}

	public function register_settings_section( $widget ) {
		$widget->start_controls_section(
			'section_menora_payment',
			[
				'label'     => esc_html__( 'Menora Payment', 'menora-payment-integration' ),
				'condition' => [
					'submit_actions' => $this->get_name(),
				],
			]
		);

		$widget->add_control(
			'menora_order_id_prefix',
			[
				'label'   => esc_html__( 'Order ID Prefix', 'menora-payment-integration' ),
				'type'    => Controls_Manager::TEXT,
				'default' => 'SHEM',
			]
		);

		$widget->add_control(
			'menora_return_url',
			[
				'label'       => esc_html__( 'Return URL', 'menora-payment-integration' ),
				'type'        => Controls_Manager::TEXT,
				'placeholder' => esc_html__( 'https://your-site.com/thank-you', 'menora-payment-integration' ),
				'label_block' => true,
				'separator'   => 'before',
				'dynamic'     => [ 'active' => true ],
				'description' => esc_html__( 'Where Menora sends the customer back to after a successful payment. Defaults to the homepage if left empty.', 'menora-payment-integration' ),
			]
		);

		$widget->add_control(
			'menora_button_text',
			[
				'label'   => esc_html__( 'Button Text', 'menora-payment-integration' ),
				'type'    => Controls_Manager::TEXT,
				'default' => 'מעבר לתשלום באתר מנורה',
			]
		);

		$widget->add_control(
			'menora_condition_field_id',
			[
				'label'       => esc_html__( 'Condition Field ID', 'menora-payment-integration' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => 'paychoose',
				'separator'   => 'before',
				'description' => esc_html__( 'Only create a Menora payment when this field equals the value below (e.g. a payment-method field). Leave empty to always create a payment.', 'menora-payment-integration' ),
			]
		);

		$widget->add_control(
			'menora_condition_value',
			[
				'label'     => esc_html__( 'Condition Value', 'menora-payment-integration' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => 'אשראי/ביט',
				'condition' => [
					'menora_condition_field_id!' => '',
				],
			]
		);

		$widget->end_controls_section();
	}

	public function on_export( $element ) {
		return $element;
	}

	public function run( $record, $ajax_handler ) {
		$settings = $record->get( 'form_settings' );
		$fields   = $record->get( 'fields' );

		$condition_field = ! empty( $settings['menora_condition_field_id'] ) ? $settings['menora_condition_field_id'] : '';

		if ( $condition_field ) {
			$actual_value = isset( $fields[ $condition_field ] ) ? $fields[ $condition_field ]['value'] : '';

			if ( trim( (string) $actual_value ) !== trim( (string) $settings['menora_condition_value'] ) ) {
				return;
			}
		}

		$customer_name = isset( $fields[ self::NAME_FIELD_ID ] ) ? trim( (string) $fields[ self::NAME_FIELD_ID ]['value'] ) : '';

		if ( isset( $fields[ self::LNAME_FIELD_ID ] ) && '' !== trim( (string) $fields[ self::LNAME_FIELD_ID ]['value'] ) ) {
			$customer_name = trim( $customer_name . ' ' . $fields[ self::LNAME_FIELD_ID ]['value'] );
		}

		$phone      = isset( $fields[ self::PHONE_FIELD_ID ] ) ? (string) $fields[ self::PHONE_FIELD_ID ]['value'] : '';
		$email      = isset( $fields[ self::EMAIL_FIELD_ID ] ) ? (string) $fields[ self::EMAIL_FIELD_ID ]['value'] : '';
		$amount_raw = isset( $fields[ self::AMOUNT_FIELD_ID ] ) ? (string) $fields[ self::AMOUNT_FIELD_ID ]['value'] : '';
		$amount     = self::parse_amount( $amount_raw );

		if ( '' === $customer_name || ! is_email( $email ) || $amount <= 0 ) {
			$ajax_handler->add_admin_error_message(
				esc_html__( 'Menora Payment: missing/invalid name, email or amount - no payment link was created.', 'menora-payment-integration' )
			);

			return;
		}

		$return_url = '';

		if ( ! empty( $settings['menora_return_url'] ) ) {
			$return_url = $record->replace_setting_shortcodes( $settings['menora_return_url'], true );
			$return_url = esc_url_raw( $return_url );
		}

		if ( empty( $return_url ) ) {
			$return_url = home_url( '/' );
		}

		$prefix            = ! empty( $settings['menora_order_id_prefix'] ) ? $settings['menora_order_id_prefix'] : 'SHEM';
		$external_order_id = self::generate_order_id( $prefix );

		$result = Api_Client::create_order(
			[
				'customer_name'     => $customer_name,
				'phone'             => $phone,
				'email'             => $email,
				'external_order_id' => $external_order_id,
				'amount'            => $amount,
				'return_url'        => $return_url,
			]
		);

		if ( is_wp_error( $result ) ) {
			$ajax_handler->add_admin_error_message( 'Menora Payment: ' . $result->get_error_message() );

			return;
		}

		$button_text = ! empty( $settings['menora_button_text'] ) ? $settings['menora_button_text'] : 'מעבר לתשלום באתר מנורה';

		$ajax_handler->add_response_data(
			'menora_payment',
			[
				'payment_link'      => $result['payment_link'],
				'order_id'          => $result['order_id'],
				'external_order_id' => $external_order_id,
				'button_text'       => $button_text,
			]
		);
	}

	/**
	 * Strips currency symbols/thousands separators (e.g. "1,234.50 ₪") down to a float.
	 */
	private static function parse_amount( $raw ) {
		$clean = preg_replace( '/[^0-9.\-]/', '', (string) $raw );

		return (float) $clean;
	}

	private static function generate_order_id( $prefix ) {
		$prefix = preg_replace( '/[^A-Za-z0-9_-]/', '', (string) $prefix );

		if ( '' === $prefix ) {
			$prefix = 'SHEM';
		}

		return sprintf( '%s_%s%d', $prefix, gmdate( 'YmdHis' ), wp_rand( 100, 999 ) );
	}
}
