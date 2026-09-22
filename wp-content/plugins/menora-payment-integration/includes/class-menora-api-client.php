<?php
namespace MenoraPaymentIntegration;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Thin client for Menora's "create external order" endpoint.
 *
 * Spec (confirmed 2026-09-09):
 * POST https://menora-glow-pro.base44.app/functions/createExternalOrder
 * { customer_name, phone, email, external_order_id, amount, return_url }
 * -> { order_id, payment_link }
 * Only these fields are accepted - city/notes/items were removed from the API.
 */
class Api_Client {

	const API_URL = 'https://menora-glow-pro.base44.app/functions/createExternalOrder';

	/**
	 * @param array $order {
	 *     @type string $customer_name
	 *     @type string $phone
	 *     @type string $email
	 *     @type string $external_order_id
	 *     @type float  $amount
	 *     @type string $return_url
	 * }
	 *
	 * @return array|\WP_Error Array with 'order_id' and 'payment_link' on success.
	 */
	public static function create_order( array $order ) {
		$payload = [
			'customer_name'     => (string) $order['customer_name'],
			'phone'             => (string) $order['phone'],
			'email'             => (string) $order['email'],
			'external_order_id' => (string) $order['external_order_id'],
			'amount'            => (float) $order['amount'],
			'return_url'        => (string) $order['return_url'],
		];

		$response = wp_remote_post(
			self::API_URL,
			[
				'timeout' => 20,
				'headers' => [
					'Content-Type' => 'application/json',
					'Accept'       => 'application/json',
				],
				'body'    => wp_json_encode( $payload ),
			]
		);

		if ( is_wp_error( $response ) ) {
			return $response;
		}

		$status = (int) wp_remote_retrieve_response_code( $response );
		$body   = json_decode( wp_remote_retrieve_body( $response ), true );

		if ( $status < 200 || $status >= 300 || empty( $body['payment_link'] ) ) {
			$message = ( is_array( $body ) && ! empty( $body['message'] ) )
				? $body['message']
				: sprintf( 'Menora API returned HTTP %d', $status );

			return new \WP_Error(
				'menora_api_error',
				$message,
				[
					'status' => $status,
					'body'   => $body,
				]
			);
		}

		return [
			'order_id'     => isset( $body['order_id'] ) ? (string) $body['order_id'] : '',
			'payment_link' => (string) $body['payment_link'],
		];
	}
}
