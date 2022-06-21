<?php

namespace TEC\Tickets\Recurrence;

class Hooks extends \tad_DI52_ServiceProvider {

	/**
	 * @inheritDoc
	 */
	public function register() {
		$this->add_actions();
		$this->add_filters();
	}

	/**
	 * @since TBD
	 */
	public function add_filters() {
		add_filter( 'tribe_tickets_settings_post_type_ignore_list', [ $this, 'disallow_attaching_tickets_to_series' ] );
		add_filter( 'event_tickets_post_supports_tickets', [ $this, 'should_render_ticket_blocks' ], 10, 2 );
	}

	/**
	 * @since TBD
	 */
	public function add_actions() {
	}

	/**
	 * Attaching tickets to objects of the Series CPT is unsupported at the moment and will not work properly.
	 * Removes the option from the Settings > Tickets page.
	 *
	 * @since TBD
	 *
	 * @param array $post_types A list of restricted post types.
	 *
	 * @return array
	 */
	public function disallow_attaching_tickets_to_series( $post_types ) {
		return array_merge( $post_types, Compatibility::get_restricted_post_types() );
	}

	/**
	 * Removes the ticket blocks if the current event is part of a Series.
	 *
	 * @since TBD
	 *
	 * @param bool $can_have_tickets the post type slug
	 * @param int  $post_id          the post id
	 *
	 * @return bool
	 */
	public function should_render_ticket_blocks( $can_have_tickets, $post_id ) {

		if ( empty( $post_id ) ) {
			return $can_have_tickets;
		}

		if ( Compatibility::object_can_have_tickets( get_post( $post_id ) ) ) {
			return true;
		}

		return false;
	}

}