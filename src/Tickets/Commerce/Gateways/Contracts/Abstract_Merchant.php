<?php

namespace TEC\Tickets\Commerce\Gateways\Contracts;

use TEC\Tickets\Commerce\Traits\Has_Mode;

abstract class Abstract_Merchant implements Merchant_Interface {

	use Has_Mode;

	/**
	 * Handle initial setup for the object singleton.
	 *
	 * @since 5.1.9
	 */
	public function init() {
		$this->set_mode( tec_tickets_commerce_is_sandbox_mode() ? 'sandbox' : 'live' );
		$this->from_array( $this->get_details_data(), false );
	}

	/**
	 * Make Merchant object from array.
	 *
	 * @since 5.1.9
	 *
	 * @param array   $data       Which values need to .
	 * @param boolean $needs_save Determines if the proprieties saved need to save to the DB.
	 *
	 * @return boolean
	 */
	public function from_array( array $data, $needs_save = true ) {
		if ( ! $this->validate( $data ) ) {
			return false;
		}

		$this->setup_properties( $data, $needs_save );

		return true;
	}

	/**
	 * Gets the value stored for the Client ID.
	 *
	 * @since TBD moved to Abstract_Merchant
	 * @since 5.1.9
	 *
	 * @return string
	 */
	public function get_client_id() {
		return $this->client_id;
	}

	/**
	 * Save merchant details.
	 *
	 * @since TBD moved to Abstract_Merchant
	 * @since 5.1.9
	 *
	 * @return bool
	 */
	public function save() {
		if ( false === $this->needs_save() ) {
			return false;
		}

		$saved = update_option( $this->get_account_key(), $this->to_array() );

		// If we were able to save, we reset the needs save.
		if ( $saved ) {
			$this->needs_save = false;
		}

		return $saved;
	}
}