<?php

namespace TEC\Tickets\Commerce\Status;

/**
 * Class Pending.
 *
 * This is a payment that has begun, but is not complete.  An example of this is someone who has filled out the checkout
 * form and then gone to Gateway for payment.  We have the record of sale, but they haven't completed their payment yet.
 *
 * @since   5.1.9
 *
 * @package TEC\Tickets\Commerce\Status
 */
class Action_Required extends Status_Abstract {
	/**
	 * Slug for this Status.
	 *
	 * @since 5.1.9
	 *
	 * @var string
	 */
	const SLUG = 'action-required';

	/**
	 * {@inheritdoc}
	 */
	public function get_name() {
		return __( 'Action Required', 'event-tickets' );
	}

	/**
	 * {@inheritdoc}
	 */
	protected $flags = [
		'incomplete',
		'trigger_option',
		'attendee_generation',
		'stock_reduced',
		'count_attendee',
		'count_incomplete',
		'count_sales',
	];

	/**
	 * {@inheritdoc}
	 */
	protected $wp_arguments = [
		'public'                    => true,
		'exclude_from_search'       => false,
		'show_in_admin_all_list'    => true,
		'show_in_admin_status_list' => true,
	];
}