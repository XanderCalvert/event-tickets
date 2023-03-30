<?php
/**
 * The main service provider for the Tickets Admin Attendees page.
 *
 * @since   TBD
 * @package TEC\Tickets\Admin
 */

namespace TEC\Tickets\Admin\Attendees;

/**
 * Service provider for the Tickets Admin Attendees
 *
 * @since   TBD
 * @package TEC\Tickets\Admin
 */
class Provider extends \tad_DI52_ServiceProvider {

	/**
	 * Register the provider singletons.
	 *
	 * @since TBD
	 */
	public function register() {
		if (
			! tribe( 'tickets.attendees' )->user_can_manage_attendees()
			|| ! tec_tickets_attendees_page_is_enabled()
		) {
			return;
		}

		$this->register_hooks();

		// Register the SP on the container.
		$this->container->singleton( static::class, $this );
	}

	/**
	 * Registers the provider handling all the 1st level filters and actions for the Tickets Admin area.
	 *
	 * @since TBD
	 */
	protected function register_hooks() {
		$hooks = new Hooks( $this->container );
		$hooks->register();

		// Allow Hooks to be removed, by having the them registered to the container.
		$this->container->singleton( Hooks::class, $hooks );
	}
}
