<?php

namespace TEC\Tickets\Commerce\Gateways\Stripe;

use TEC\Tickets\Commerce\Cart;

/**
 * Class Payment Intent Handler
 *
 * @since   TBD
 *
 * @package TEC\Tickets\Commerce\Gateways\Stripe
 */
class Payment_Intent_Handler {

	/**
	 * Base string to use when composing payment intent transient names.
	 *
	 * @since TBD
	 *
	 * @var string
	 */
	public $payment_intent_transient_prefix = 'paymentintent-';

	/**
	 * Transient name to store payment intents.
	 *
	 * @since TBD
	 *
	 * @var string
	 */
	public $payment_intent_transient_name;

	/**
	 * Counter for how many times we've re-tried creating a PaymentIntent.
	 *
	 * @since TBD
	 *
	 * @var int
	 */
	protected $payment_element_fallback_retries = 0;

	/**
	 * Max number of retries to create a PaymentIntent.
	 *
	 * @since TBD
	 *
	 * @var int
	 */
	protected $payment_intent_max_retries = 2;

	/**
	 * Increment the retry counter if under max_retries.
	 *
	 * @return bool True if incremented, false if no more retries are allowed.
	 */
	public function count_retries() {
		if ( $this->payment_intent_max_retries <= $this->payment_element_fallback_retries ) {
			return false;
		}

		$this->payment_element_fallback_retries ++;

		return true;
	}

	/**
	 * Calls the Stripe API and returns a new PaymentIntent object, used to authenticate
	 * front-end payment requests.
	 *
	 * @since TBD
	 *
	 * @param string $currency 3-letter ISO code for the desired currency. Not all currencies are supported.
	 * @param int    $value    The payment value in the smallest currency unit (e.g: cents, if the purchase is in USD).
	 */
	public function create_payment_intent_for_cart( $retry = false ) {
		$this->set_payment_intent_transient_name();
		$payment_intent = Payment_Intent::create_from_cart( tribe( Cart::class ), $retry );

		if ( ! isset( $payment_intent['id'] ) && ! empty( $payment_intent['errors'] ) ) {

			if ( $this->count_retries() ) {
				$this->delete_payment_intent_transient();

				return $this->create_payment_intent_for_cart( true );
			}

			// We're over the max retries, display an error to the end user and move on.
			$payment_intent['errors'][0] = [
				'et_could_not_create_stripe_order',
				__( 'There was an error enabling Stripe on your cart. More information is available in the Event Tickets settings dashboard. Please contact the site administrator for support.', 'event-tickets' ),
			];
		}

		return $this->store_payment_intent( $payment_intent );
	}

	/**
	 * Updates an existing payment intent to add any necessary data before confirming the purchase.
	 *
	 * @since TBD
	 *
	 * @param array $data The purchase data received from the front-end.
	 *
	 * @return array|\WP_Error|null
	 */
	public function update_payment_intent( $data ) {
		$payment_intent_id = $data['payment_intent']['id'];

		$stripe_receipt_emails = tribe_get_option( Settings::$option_stripe_receipt_emails );

		$body = [];

		// Currently this method is only used to add an email recipient for Stripe receipts. If this is not
		// required, only return the payment intent object to store.
		if ( ! $stripe_receipt_emails ) {
			return Payment_Intent::get( $payment_intent_id );
		}

		if ( $stripe_receipt_emails && ! empty( $data['billing_details']['email'] ) ) {
			$body['receipt_email'] = $data['billing_details']['email'];
		}

		return Payment_Intent::update( $payment_intent_id, $body );
	}

	/**
	 * Assembles basic data about the payment intent created at page-load to use in javascript.
	 *
	 * @since TBD
	 *
	 * @return array
	 */
	public function get_publishable_payment_intent_data() {
		$pi = $this->get_payment_intent_transient();

		if ( empty( $pi ) ) {
			return [];
		}

		if ( ! empty( $pi['errors'] ) ) {
			return $pi;
		}

		return [
			'id'   => $pi['id'],
			'key'  => $pi['client_secret'],
			'name' => $this->get_payment_intent_transient_name(),
		];
	}

	/**
	 * Compose the transient name used for payment intent transients.
	 *
	 * @since TBD
	 */
	public function set_payment_intent_transient_name() {
		$this->payment_intent_transient_name = $this->payment_intent_transient_prefix . md5( tribe( Cart::class )->get_cart_hash() );
	}

	/**
	 * Returns the transient name used for payment intent transients.
	 *
	 * @since TBD
	 *
	 * @return string
	 */
	public function get_payment_intent_transient_name() {

		if ( empty( $this->payment_intent_transient_name ) ) {
			$this->set_payment_intent_transient_name();
		}

		return $this->payment_intent_transient_name;
	}

	/**
	 * Retrieve a stored payment intent referring to the current cart.
	 *
	 * @since TBD
	 *
	 * @return array|false
	 */
	public function get_payment_intent_transient() {
		return get_transient( $this->get_payment_intent_transient_name() );
	}

	/**
	 * Delete the payment intent transient.
	 *
	 * @since TBD
	 *
	 * @return bool
	 */
	public function delete_payment_intent_transient() {
		return delete_transient( $this->get_payment_intent_transient_name() );
	}

	/**
	 * Store a payment intent array in a transient.
	 *
	 * @since TBD
	 *
	 * @param array $payment_intent Payment intent data from Stripe.
	 */
	public function store_payment_intent( $payment_intent ) {
		set_transient( $this->get_payment_intent_transient_name(), $payment_intent, 6 * HOUR_IN_SECONDS );
	}
}