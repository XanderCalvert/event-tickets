<?php
/**
 * Class that handles interfacing with core Site Health.
 *
 * @since   5.6.0.1
 *
 * @package TEC\Tickets\Site_Health
 */

namespace TEC\Tickets\Site_Health;

use Tribe__Utils__Array as Arr;
use TEC\Tickets\QR\Settings as QR_Settings;
use Tribe__Tickets__Query;

/**
 * Class Plugin_Data_Subsection
 *
 * @since   TBD
 * @package TEC\Tickets\Site_Health
 */
class Plugin_Data_Subsection extends Abstract_Info_Subsection {

	/**
	 * @inheritDoc
	 */
	protected function is_subsection_enabled(): bool {
		return true;
	}

	/**
	 * @inheritDoc
	 */
	protected function generate_subsection(): array {
		return [
			[
				'id'       => 'plugin_activation_date',
				'title'    => esc_html__(
					'Plugin Activation Date',
					'event-tickets'
				),
				'value'    => $this->get_plugin_activation_date(),
				'priority' => 10,
			],
			[
				'id'       => 'previous_versions',
				'title'    => esc_html__(
					'Previous ET versions',
					'event-tickets'
				),
				'value'    => $this->get_previous_versions(),
				'priority' => 20,
			],
			[
				'id'       => 'ticketed_posts',
				'title'    => esc_html__(
					'Total ticketed posts',
					'event-tickets'
				),
				'value'    => $this->get_ticketed_post_count(),
				'priority' => 30,
			],
			[
				'id'       => 'rsvp_posts',
				'title'    => esc_html__(
					'Total posts with RSVPs',
					'event-tickets'
				),
				'value'    => $this->get_rsvp_post_count(),
				'priority' => 40,
			],
			[
				'id'       => 'last_ticket_creation_date',
				'title'    => esc_html__(
					'Last Ticket Creation Date',
					'event-tickets'
				),
				'value'    => $this->get_last_ticket_creation_date(),
				'priority' => 50,
			],
			[
				'id'       => 'last_rsvp_creation_date',
				'title'    => esc_html__(
					'Last RSVP Creation Date',
					'event-tickets'
				),
				'value'    => $this->get_last_rsvp_creation_date(),
				'priority' => 30,
			],
			[
				'id'       => 'last_attendee_creation_date',
				'title'    => esc_html__(
					'Last Attendee Creation Date',
					'event-tickets'
				),
				'value'    => $this->get_last_attendee_creation_date(),
				'priority' => 70,
			],
			[
				'id'       => 'last_app_check_in_date',
				'title'    => esc_html__(
					'Last App Check-in Date',
					'event-tickets'
				),
				'value'    => $this->get_last_app_check_in_date(),
				'priority' => 80,
			],
			[
				'id'       => 'number_of_tickets',
				'title'    => esc_html__(
					'Number of Tickets',
					'event-tickets'
				),
				'value'    => $this->get_number_of_tickets(),
				'priority' => 110,
			],
			[
				'id'       => 'number_of_rsvps',
				'title'    => esc_html__(
					'Number of RSVPs',
					'event-tickets'
				),
				'value'    => $this->get_number_of_rsvps(),
				'priority' => 120,
			],
			[
				'id'       => 'number_of_attendees',
				'title'    => esc_html__(
					'Number of Attendees',
					'event-tickets'
				),
				'value'    => $this->get_number_of_attendees(),
				'priority' => 130,
			],
			[
				'id'       => 'average_number_of_attendees_per_event',
				'title'    => esc_html__(
					'Average Number of Attendees per Event',
					'event-tickets'
				),
				'value'    => $this->get_average_attendees_per_event(),
				'priority' => 140,
			],
			[
				'id'       => 'average_ticket_price',
				'title'    => esc_html__(
					'Average Ticket Price',
					'event-tickets'
				),
				'value'    => $this->get_formatted_prices()['formatted_average_price'],
				'priority' => 150,
			],
			[
				'id'       => 'maximum_ticket_price',
				'title'    => esc_html__(
					'Maximum Ticket Price',
					'event-tickets'
				),
				'value'    => $this->get_formatted_prices()['formatted_max_price'],
				'priority' => 160,
			],
			[
				'id'       => 'minimum_ticket_price',
				'title'    => esc_html__(
					'Minimum Ticket Price',
					'event-tickets'
				),
				'value'    => $this->get_formatted_prices()['formatted_min_price'],
				'priority' => 170,
			],
			[
				'id'       => 'post_types_with_tickets',
				'title'    => esc_html__(
					'Post Types with Tickets',
					'event-tickets'
				),
				'value'    => $this->get_post_types_with_tickets(),
				'priority' => 190,
			],
			[
				'id'       => 'login_required_for_purchasing_tickets',
				'title'    => esc_html__(
					'Login Required for Purchasing Tickets',
					'event-tickets'
				),
				'value'    => $this->is_login_required_for_purchasing_tickets(),
				'priority' => 200,
			],
			[
				'id'       => 'login_required_for_rsvp',
				'title'    => esc_html__(
					'Login Required for RSVP',
					'event-tickets'
				),
				'value'    => $this->is_login_required_for_rsvp(),
				'priority' => 210,
			],
			[
				'id'       => 'tickets_emails_enabled',
				'title'    => esc_html__(
					'Tickets Emails Enabled',
					'event-tickets'
				),
				'value'    => $this->are_tickets_emails_enabled(),
				'priority' => 220,
			],
			[
				'id'       => 'tickets_views_v2_enabled',
				'title'    => esc_html__(
					'Tickets Views V2 Enabled',
					'event-tickets'
				),
				'value'    => $this->are_tickets_views_v2_enabled(),
				'priority' => 230,
			],
			[
				'id'       => 'rsvp_views_v2_enabled',
				'title'    => esc_html__(
					'RSVP Views V2 Enabled',
					'event-tickets'
				),
				'value'    => $this->are_rsvp_views_v2_enabled(),
				'priority' => 240,
			],
			[
				'id'       => 'tickets_commerce_enabled',
				'title'    => 'Tickets Commerce Enabled',
				'value'    => $this->is_tickets_commerce_enabled(),
				'priority' => 250,
			],
		];
	}

	/**
	 * Retrieves a list of previous Event Tickets versions.
	 *
	 * @return string List of previous versions.
	 */
	private function get_previous_versions(): string {
		return Arr::to_list(
			array_filter(
				(array) tribe_get_option(
					'previous_event_tickets_versions',
					[]
				)
			),
			', '
		);
	}

	/**
	 * Counts the total number of posts with tickets.
	 *
	 * @return int Count of ticketed posts.
	 */
	private function get_ticketed_post_count(): int {
		return tribe( 'tickets.post-repository' )->per_page( -1 )->where( 'has_tickets' )->count();
	}

	/**
	 * Counts the total number of posts with RSVPs.
	 *
	 * @return int Count of posts with RSVPs.
	 */
	private function get_rsvp_post_count(): int {
		return tribe( 'tickets.post-repository' )->per_page( -1 )->where( 'has_rsvp' )->count();
	}

	/**
	 * Retrieves the plugin activation date.
	 *
	 * @return string Activation date in 'Y-m-d' format.
	 */
	private function get_plugin_activation_date(): string {
		return tribe_format_date(
			tribe_get_option( 'tec_tickets_activation_time' ),
			false,
			'Y-m-d'
		);
	}

	/**
	 * Gets the creation date of the latest ticket.
	 *
	 * @return string Date of the last ticket creation.
	 */
	private function get_last_ticket_creation_date(): string {
		// Fetch the latest ticket's creation date.
		$latest_ticket_date = tribe( 'tickets.ticket-repository' )
			->per_page( 1 )
			->order_by(
				'date',
				'DESC'
			)
			->pluck( 'post_date' );

		// Check if we have any ticket date.
		if ( ! empty( $latest_ticket_date ) ) {
			$last_ticket_creation_date = tribe_format_date(
				$latest_ticket_date[0],
				true,
				'Y-m-d'
			);
		} else {
			$last_ticket_creation_date = 'No tickets found';
		}

		return $last_ticket_creation_date;
	}

	/**
	 * Gets the creation date of the latest RSVP.
	 *
	 * @return string Date of the last RSVP creation.
	 */
	private function get_last_rsvp_creation_date(): string {
		$latest_rsvp_date = tribe( 'tickets.ticket-repository.rsvp' )
			->per_page( 1 )
			->order_by(
				'date',
				'DESC'
			)
			->pluck( 'post_date' );

		// Check if we have any rsvp date.
		if ( ! empty( $latest_rsvp_date ) ) {
			$last_rsvp_creation_date = tribe_format_date(
				$latest_rsvp_date[0],
				true,
				'Y-m-d'
			);
		} else {
			$last_rsvp_creation_date = 'No rsvp found';
		}
		return $last_rsvp_creation_date;
	}

	/**
	 * Gets the creation date of the latest attendee.
	 *
	 * @return string Date of the last attendee creation.
	 */
	private function get_last_attendee_creation_date(): string {
		$latest_attendee_date = tribe( 'tickets.attendee-repository' )
			->per_page( 1 )
			->order_by(
				'date',
				'DESC'
			)
			->pluck( 'post_date' );

		// Check if we have any attendee creation date.
		if ( ! empty( $latest_attendee_date ) ) {
			$last_attendee_creation_date = tribe_format_date(
				$latest_attendee_date[0],
				true,
				'Y-m-d'
			);
		} else {
			// @ Todo redscar need to possibly add translation.
			$last_attendee_creation_date = "No Attendee's found";
		}

		return $last_attendee_creation_date;
	}

	/**
	 * Retrieves the last app check-in date.
	 *
	 * @return string Last app check-in date in 'Y-m-d' format.
	 */
	private function get_last_app_check_in_date(): string {
		return tribe_format_date(
			tribe_get_option( 'tec_tickets_plus_app_last_checkin_time' ),
			false,
			'Y-m-d'
		);
	}

	/**
	 * Counts the total number of tickets.
	 *
	 * @return int Total number of tickets.
	 */
	private function get_number_of_tickets(): int {
		// @todo redscar switch this to ORM?
		return tribe( Tribe__Tickets__Query::class )->get_ticketed_count( 'tribe_events' );
	}

	/**
	 * Counts the total number of RSVPs.
	 *
	 * @return int Total number of RSVPs.
	 */
	private function get_number_of_rsvps(): int {
		return tribe( 'tickets.ticket-repository.rsvp' )->count();
	}

	/**
	 * Counts the total number of attendees.
	 *
	 * @return int Total number of attendees.
	 */
	private function get_number_of_attendees(): int {
		return tribe( 'tickets.attendee-repository' )->count();
	}

	/**
	 * Calculates the average number of attendees per event.
	 *
	 * @return int Average number of attendees per event.
	 */
	private function get_average_attendees_per_event(): int {
		$attendee_count = (int) tribe( 'tickets.attendee-repository' )->count();
		// @todo redscar Is this the proper place to get the ticketed amount? Should it be from another spot if TEC isn't enabled?
		$ticketed_event_count = (int) tribe( 'tickets.event-repository' )->per_page( -1 )->where(
			'has_tickets'
		)->count();
		// @todo redscar Will need to add in logic for dividing by 0.
		$average_attendees_per_event = floor( $attendee_count / $ticketed_event_count );

		return $average_attendees_per_event;
	}

	/**
	 * Computes and formats ticket prices, including average, max, and min prices.
	 *
	 * @return array Associative array with formatted average, max, and min ticket prices.
	 */
	private function get_formatted_prices(): array {
		$ticket_prices = tribe( 'tickets.event-repository' )->per_page( -1 )->where( 'has_tickets' )->pluck( 'cost' );

		$total     = 0;
		$count     = 0;
		$max_price = 0;
		$min_price = PHP_FLOAT_MAX;

		foreach ( $ticket_prices as $price ) {
			if ( $price === 'Free' || $price === '' ) {
				$total += 0;
				++$count;
				$min_price = 0;
			} else {
				// Match the number with international currency format.
				preg_match(
					'/\d+([,.]\d+)?/',
					$price,
					$matches
				);
				if ( isset( $matches[0] ) ) {
					// Convert to a standard number format (replace comma with period).
					$number = floatval(
						str_replace(
							',',
							'.',
							$matches[0]
						)
					);
					$total  += $number;

					++$count;

					if ( $number > $max_price ) {
						$max_price = $number;
					}
					if ( $number < $min_price ) {
						$min_price = $number;
					}
				}
			}
		}

		$average_price = $count > 0 ? $total / $count : 0;

		//@todo redscar I'm sure there is something built to display this value correctly.

		return [
			'formatted_max_price'     => number_format(
				$max_price,
				2,
				'.',
				','
			),
			'formatted_min_price'     => number_format(
				$min_price,
				2,
				'.',
				','
			),
			'formatted_average_price' => number_format(
				$average_price,
				2,
				'.',
				','
			),
		];
	}

	/**
	 * Retrieves the list of post types that are enabled to have tickets.
	 *
	 * @return string A comma-separated list of post types.
	 */
	private function get_post_types_with_tickets(): string {
		return Arr::to_list(
			array_filter(
				(array) tribe_get_option(
					'ticket-enabled-post-types',
					[]
				)
			),
			', '
		);
	}

	/**
	 * Checks if login is required for purchasing tickets.
	 *
	 * @return string 'True' if login is required, 'False' otherwise.
	 */
	private function is_login_required_for_purchasing_tickets(): string {
		$login_requirements = tribe_get_option(
			'ticket-authentication-requirements',
			[]
		);

		if ( empty( $login_requirements ) ) {
			return 'False';
		}

		return in_array(
			'event-tickets_all',
			$login_requirements
		) ? 'True' : 'False';
	}

	/**
	 * Checks if login is required to RSVP.
	 *
	 * @return string 'True' if login is required, 'False' otherwise.
	 */
	private function is_login_required_for_rsvp(): string {
		$login_requirements = tribe_get_option(
			'ticket-authentication-requirements',
			[]
		);

		if ( empty( $login_requirements ) ) {
			return 'False';
		}

		return in_array(
			'event-tickets_rsvp',
			$login_requirements
		) ? 'True' : 'False';
	}

	/**
	 * Determines if ticket emails are enabled.
	 *
	 * @return string 'True' if ticket emails are enabled, 'False' otherwise.
	 */
	private function are_tickets_emails_enabled(): string {
		$email_enabled = tribe_get_option(
			'tec-tickets-emails-enabled',
			tec_tickets_emails_is_enabled()
		);

		return $email_enabled ? 'True' : 'False';
	}

	/**
	 * Checks if tickets views version 2 is enabled.
	 *
	 * @return string 'True' if tickets views v2 are enabled, 'False' otherwise.
	 */
	private function are_tickets_views_v2_enabled(): string {
		return tec_tickets_emails_is_enabled() ? 'True' : 'False';
	}

	/**
	 * Determines if RSVP views version 2 is enabled.
	 *
	 * @return string 'TBD' indicating the status is to be determined.
	 */
	private function are_rsvp_views_v2_enabled(): string {
		// @todo How do we check if RSVP v2 is enabled?
		return 'TBD';
	}

	/**
	 * Checks if Tickets Commerce is enabled.
	 *
	 * @return string 'True' if Tickets Commerce is enabled, 'False' otherwise.
	 */
	private function is_tickets_commerce_enabled(): string {
		return tribe_get_option(
			'tickets_commerce_enabled',
			false
		) ? 'True' : 'False';
	}
}
