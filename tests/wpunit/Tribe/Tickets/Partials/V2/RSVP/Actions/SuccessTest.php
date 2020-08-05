<?php

namespace Tribe\Tickets\Partials\V2\RSVP\Actions;

use Codeception\TestCase\WPTestCase;
use Spatie\Snapshots\MatchesSnapshots;
use tad\WP\Snapshots\WPHtmlOutputDriver;
use Tribe\Test\PHPUnit\Traits\With_Post_Remapping;
use Tribe\Tickets\Test\Commerce\RSVP\Ticket_Maker as RSVP_Ticket_Maker;

class SuccessTest extends WPTestCase {

	use MatchesSnapshots;
	use With_Post_Remapping;

	use RSVP_Ticket_Maker;

	protected $partial_path = 'v2/rsvp/actions/success';

	/**
	 * @test
	 */
	public function test_should_render_success_message() {
		$template     = tribe( 'tickets.editor.template' );
		$_GET['step'] = 'success';
		$event        = $this->get_mock_event( 'events/single/1.json' );
		$event_id     = $event->ID;
		$ticket_id    = $this->create_rsvp_ticket(
			$event_id,
			[
				'meta_input' => [
					'_capacity' => 5,
				],
			]
		);


		// Get ticket.
		$ticket = tribe( 'tickets.rsvp' )->get_ticket( $event_id, $ticket_id );

		$args = [
			'rsvp'                => $ticket,
			'post_id'             => $event_id,
			'must_login'          => false,
			'opt_in_checked'      => false,
			'opt_in_attendee_ids' => '',
			'opt_in_nonce'        => '',
		];

		$html   = $template->template( $this->partial_path, $args, false );
		$driver = new WPHtmlOutputDriver( home_url(), 'http://test.tribe.dev' );

		$driver->setTolerableDifferences(
			[
				$ticket_id,
				$event_id,
			]
		);
		$driver->setTolerableDifferencesPrefixes(
			[
				'toggle-rsvp-',
				'rsvp-',
				'tribe-tickets-tooltip-content-',
			]
		);
		$driver->setTimeDependentAttributes(
			[
				'data-rsvp-id',
			]
		);

		$this->assertMatchesSnapshot( $html, $driver );
	}

}
