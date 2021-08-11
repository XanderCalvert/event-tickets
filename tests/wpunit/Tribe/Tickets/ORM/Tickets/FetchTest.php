<?php

namespace Tribe\Tickets\ORM\Tickets;

use Tribe\Tickets\Test\Commerce\RSVP\Ticket_Maker as RSVP_Ticket_Maker;
use Tribe\Tickets\Test\Commerce\PayPal\Ticket_Maker as PayPal_Ticket_Maker;
use Tribe__Tickets__Ticket_Repository as Ticket_Repository;
use Tribe__Tickets__Data_API as Data_API;

class FetchTest extends \Codeception\TestCase\WPTestCase {

	use RSVP_Ticket_Maker;
	use PayPal_Ticket_Maker;

	/**
	 * {@inheritdoc}
	 */
	public function setUp() {
		parent::setUp();

		// Enable post as ticket type.
		add_filter( 'tribe_tickets_post_types', function () {
			return [ 'post' ];
		} );

		// Enable Tribe Commerce.
		add_filter( 'tribe_tickets_commerce_paypal_is_active', '__return_true' );
		add_filter( 'tribe_tickets_get_modules', function ( $modules ) {
			$modules['Tribe__Tickets__Commerce__PayPal__Main'] = tribe( 'tickets.commerce.paypal' )->plugin_name;

			return $modules;
		} );

		// Reset Data_API object so it sees Tribe Commerce.
		tribe_singleton( 'tickets.data_api', new Data_API );
	}

	/**
	 * It should allow fetching tickets.
	 *
	 * @test
	 */
	public function should_allow_fetching_tickets() {
		/** @var Ticket_Repository $tickets */
		$tickets = tribe_tickets();

		$post_id = $this->factory->post->create();

		$paypal_ticket_ids = $this->create_many_paypal_tickets_basic( 5, $post_id );
		$rsvp_ticket_ids   = $this->create_many_rsvp_tickets( 5, $post_id );

		$ticket_ids = $tickets
			->order_by( 'post_id' )
			->order( 'DESC' )
			->per_page( 10 )
			->get_ids();

		$this->assertEqualSets( array_merge( $paypal_ticket_ids, $rsvp_ticket_ids ), $ticket_ids );
	}

	/**
	 * It should allow fetching tickets from the rsvp context.
	 *
	 * @test
	 */
	public function should_allow_fetching_tickets_from_rsvp_context() {
		/** @var Ticket_Repository $tickets */
		$tickets = tribe_tickets( 'rsvp' );

		$post_id = $this->factory->post->create();

		$rsvp_ticket_ids   = $this->create_many_rsvp_tickets( 5, $post_id );
		$paypal_ticket_ids = $this->create_many_paypal_tickets_basic( 5, $post_id );

		$ticket_ids = $tickets
			->order_by( 'post_id' )
			->order( 'DESC' )
			->per_page( 5 )
			->get_ids();

		$this->assertEqualSets( $rsvp_ticket_ids, $ticket_ids );
	}

	/**
	 * It should allow fetching tickets from the tribe-commerce context.
	 *
	 * @test
	 */
	public function should_allow_fetching_tickets_from_tribe_commerce_context() {
		/** @var Ticket_Repository $tickets */
		$tickets = tribe_tickets( 'tribe-commerce' );

		$post_id = $this->factory->post->create();

		$paypal_ticket_ids = $this->create_many_paypal_tickets_basic( 5, $post_id );
		$rsvp_ticket_ids   = $this->create_many_rsvp_tickets( 5, $post_id );

		$ticket_ids = $tickets
			->order_by( 'post_id' )
			->order( 'DESC' )
			->per_page( 5 )
			->get_ids();

		$this->assertEqualSets( $paypal_ticket_ids, $ticket_ids );
	}

}
