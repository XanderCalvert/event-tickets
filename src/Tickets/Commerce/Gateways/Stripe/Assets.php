<?php

namespace TEC\Tickets\Commerce\Gateways\Stripe;

use TEC\Tickets\Commerce\Checkout;
use TEC\Tickets\Commerce\Gateways\Stripe\REST\Order_Endpoint;
use TEC\Tickets\Commerce\Gateways\Stripe\REST\Payment_Intent_Secret_Endpoint;

/**
 * Class Assets.
 *
 * @since   TBD
 *
 * @package TEC\Tickets\Commerce\Gateways\Stripe
 */
class Assets extends \tad_DI52_ServiceProvider {
	/**
	 * The nonce action to use when requesting the creation of a new order
	 *
	 * @since TBD
	 *
	 * @var string
	 */
	const ORDER_NONCE_ACTION = 'tec_stripe_order';

	/**
	 * @inheritDoc
	 */
	public function register() {
		$plugin = \Tribe__Tickets__Main::instance();

		tribe_asset(
			$plugin,
			'tec-tickets-commerce-gateway-stripe-base',
			'https://js.stripe.com/v3/',
			[],
			null,
			[
				'type' => 'js',
			]
		);

		tribe_asset(
			$plugin,
			'tec-tickets-commerce-gateway-stripe-checkout',
			'commerce/gateway/stripe/checkout.js',
			[
				'jquery',
				'tribe-common',
//				'tec-ky',
				'tribe-query-string',
				'tec-tickets-commerce-gateway-stripe-base',
				'tribe-tickets-loader',
				'tribe-tickets-commerce-js',
				'tribe-tickets-commerce-notice-js',
				'tribe-tickets-commerce-base-gateway-checkout-js',
			],
			'wp_enqueue_scripts',
			[
				'module'   => true,
				'groups'   => [
					'tec-tickets-commerce-gateway-stripe',
				],
				'conditionals' => [ $this, 'should_enqueue_assets' ],
				'localize' => [
					'name' => 'tecTicketsCommerceGatewayStripeCheckout',
					'data' => static function () {
						return [
							'nonce'          => wp_create_nonce( 'wp_rest' ),
							'orderEndpoint'  => tribe( Order_Endpoint::class )->get_route_url(),
							'paymentElement' => tribe( Stripe_Elements::class )->include_payment_element(),
							'paymentIntentEndpoint'  => tribe( Payment_Intent_Secret_Endpoint::class )->get_route_url(),
							'publishableKey' => tribe( Merchant::class )->get_publishable_key(),
						];
					},
				],
			]
		);
	}

	/**
	 * Define if the assets for `Stripe` should be enqueued or not.
	 *
	 * @since TBD
	 *
	 * @return bool If the `Stripe` assets should be enqueued or not.
	 */
	public function should_enqueue_assets() {
		return tribe( Gateway::class )->is_active() && tribe( Checkout::class )->is_current_page();
	}
}