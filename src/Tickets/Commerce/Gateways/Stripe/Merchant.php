<?php

namespace TEC\Tickets\Commerce\Gateways\Stripe;

use TEC\Tickets\Commerce\Gateways\Contracts\Abstract_Merchant;

/**
 * Class Merchant
 *
 * @since   TBD
 *
 * @package TEC\Tickets\Commerce\Gateways\Stripe
 */
class Merchant extends Abstract_Merchant {

	/**
	 * Determines if the Merchant is active.
	 *
	 * @since TBD
	 *
	 * @return bool
	 */
	public function is_active( $recheck = false ) {
		return true;
	}

	/**
	 * Determines if the Merchant is connected.
	 *
	 * @since TBD
	 *
	 * @return bool
	 */
	public function is_connected( $recheck = false ) {
		return true;
	}

	/**
	 * Returns the options key for the account in the merchant mode.
	 *
	 * @since TBD
	 *
	 * @return string
	 */
	public function get_account_key() {
		$gateway_key = Gateway::get_key();

		return "tec_tickets_commerce_{$gateway_key}_account";
	}

	/**
	 * Returns the data retrieved from the signup process.
	 *
	 * Uses normal WP options to be saved, instead of the normal tribe_update_option.
	 *
	 * @since TBD
	 *
	 * @return string
	 */
	public function get_signup_data_key() {
		$gateway_key = Gateway::get_key();

		return "tec_tickets_commerce_{$gateway_key}_signup_data";
	}

	/**
	 * Returns the stripe client secret stored for server-side transactions.
	 *
	 * @since TBD
	 *
	 * @return string
	 */
	public function get_client_secret() {
		$keys = get_option( $this->get_signup_data_key() );

		return $keys[ $this->get_mode() ]->access_token;
	}

	/**
	 * Fetch the Publishable key for the user.
	 *
	 * @since TBD
	 *
	 * @return string
	 */
	public function get_publishable_key() {
		$keys = get_option( $this->get_signup_data_key() );

		return $keys[ $this->get_mode() ]->publishable_key;
	}

	/**
	 * Returns the stripe client id stored for server-side transactions.
	 *
	 * @since TBD
	 *
	 * @return string
	 */
	public function get_client_id() {
		$keys = get_option( $this->get_signup_data_key() );

		return $keys['stripe_user_id'];
	}

	/**
	 * Return array of merchant details.
	 *
	 * @since TBD
	 *
	 * @return array
	 */
	public function to_array() {
		return [
			'client_id'       => $this->get_client_id(),
			'client_secret'   => $this->get_client_secret(),
			'publishable_key' => $this->get_client_id(),
		];
	}

	/**
	 * Saves signup data from the redirect into permanent option.
	 *
	 * @since TBD
	 *
	 * @param array $signup_data
	 *
	 * @return bool
	 */
	public function save_signup_data( array $signup_data ) {
		unset( $signup_data['whodat'] );
		unset( $signup_data['state'] );

		return update_option( $this->get_signup_data_key(), $signup_data );
	}
}