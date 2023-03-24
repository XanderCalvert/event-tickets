<?php

namespace TEC\Tickets\Commerce\Flag_Actions;

use TEC\Tickets\Commerce\Order;
use TEC\Tickets\Commerce\Status\Status_Interface;

/**
 * Class Send_Email_Completed_Order, normally triggered when an order is completed.
 *
 * @since   TBD
 *
 * @package TEC\Tickets\Commerce\Flag_Actions
 */
class Send_Email_Completed_Order extends Flag_Action_Abstract {
	/**
	 * {@inheritDoc}
	 *
	 * @var array
	 */
	protected $flags = [
		'send_email_completed_order',
	];

	/**
	 * {@inheritDoc}
	 *
	 * @var array
	 */
	protected $post_types = [
		Order::POSTTYPE,
	];

	/**
	 * {@inheritDoc}
	 */
	public function handle( Status_Interface $new_status, $old_status, \WP_Post $order ) {
		if ( ! empty( $order->gateway ) && 'manual' === $order->gateway && empty( $order->events_in_order ) ) {
			$order->events_in_order[] = $order;
		}

		if ( empty( $order->events_in_order ) || ! is_array( $order->events_in_order ) ) {
			return;
		}

		// Bail if tickets emails is not enabled.
		if ( ! tec_tickets_emails_is_enabled() ) {
			return;
		}

		$email_class = tribe( \TEC\Tickets\Emails\Email\Completed_Order::class );

		// Bail if the `Completed Order` email is not enabled.
		if ( ! $email_class->is_enabled() ) {
			return false;
		}

		// @todo @juanfra: See if we handle the placeholders here or not.
		$placeholders = [
			'{order_number}' => $order->ID,
			'{order_id}'     => $order->ID,
		];

		$email_class->set_placeholders( $placeholders );

		$to          = $email_class->get_recipient();
		$subject     = $email_class->get_subject();
		$content     = $email_class->get_content( [] );
		$headers     = $email_class->get_headers();
		$attachments = $email_class->get_attachments();

		$sent = tribe( \TEC\Tickets\Emails\Email_Sender::class )->send( $to, $subject, $content, $headers, $attachments );
	}
}
