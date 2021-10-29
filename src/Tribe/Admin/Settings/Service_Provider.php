<?php

namespace Tribe\Tickets\Admin\Settings;

use tad_DI52_ServiceProvider;

/**
 * Class Manager
 *
 * @package Tribe\Tickets\Admin\Settings
 *
 * @since   5.1.2
 */
class Service_Provider extends tad_DI52_ServiceProvider {
	/**
	 * Register the provider singletons.
	 *
	 * @since 5.1.2
	 */
	public function register() {
		$this->container->singleton( 'tickets.admin.settings', self::class );

		$this->hooks();
	}

	/**
	 * Add actions and filters.
	 *
	 * @since 5.1.2
	 */
	protected function hooks() {
		if ( ! is_admin() ) {
			return;
		}

		add_action( 'tribe_settings_before_content_tab_event-tickets', [ $this, 'render_settings_banner' ] );

		add_filter( 'tec_tickets_commerce_settings', [ $this, 'maybe_render_tickets_commerce_upgrade_banner' ] );
	}

	/**
	 * Render the Help banner for the Ticket Settings Tab.
	 *
	 * @since 5.1.2
	 *
	 * @return string The help banner HTML content.
	 */
	public function render_settings_banner() {
		$et_resource_links = [
			[
				'label' => __( 'Getting Started Guide', 'event-tickets' ),
				'href'  => 'https://evnt.is/1aot',
			],
			[
				'label' => __( 'Event Tickets Manual', 'event-tickets' ),
				'href'  => 'https://evnt.is/1aoz',
			],
			[
				'label' => __( 'What is Tickets Commerce?', 'event-tickets' ),
				'href'  => 'https://evnt.is/1axs',
				'new'   => true,
			],
			[
				'label' => __( 'Configuring Tickets Commerce', 'event-tickets' ),
				'href'  => 'https://evnt.is/1axt',
				'new'   => true,
			],
			[
				'label' => __( 'Using RSVPs', 'event-tickets' ),
				'href'  => 'https://evnt.is/1aox',
			],
			[
				'label' => __( 'Managing Orders and Attendees', 'event-tickets' ),
				'href'  => 'https://evnt.is/1aoy',
			],
		];

		$etp_resource_links = [
			[
				'label' => __( 'Switching from Tribe Commerce to WooCommerce', 'event-tickets' ),
				'href'  => 'https://evnt.is/1ao-',
			],
			[
				'label' => __( 'Setting Up E-Commerce Plugins for Selling Tickets', 'event-tickets' ),
				'href'  => 'https://evnt.is/1ap0',
			],
			[
				'label' => __( 'Tickets & WooCommerce', 'event-tickets' ),
				'href'  => 'https://evnt.is/1ap1',
			],
			[
				'label' => __( 'Creating Tickets', 'event-tickets' ),
				'href'  => 'https://evnt.is/1ap2',
			],
			[
				'label' => __( 'Event Tickets and Event Tickets Plus Settings Overview', 'event-tickets' ),
				'href'  => 'https://evnt.is/1ap3',
			],
			[
				'label' => __( 'Event Tickets Plus Manual', 'event-tickets' ),
				'href'  => 'https://evnt.is/1ap4',
			],
		];

		$context = [
			'etp_enabled'        => class_exists( 'Tribe__Tickets_Plus__Main' ),
			'et_resource_links'  => $et_resource_links,
			'etp_resource_links' => $etp_resource_links,
		];

		/** @var Tribe__Tickets__Admin__Views $admin_views */
		$admin_views = tribe( 'tickets.admin.views' );

		return $admin_views->template( 'settings/getting-started', $context );
	}

	/**
	 * Render the Tickets Commerce Upgrade banner for the Ticket Settings Tab.
	 *
	 * @since TBD
	 *
	 * @return string The help banner HTML content.
	 */
	public function maybe_render_tickets_commerce_upgrade_banner( $commerce_fields ) {

		// Check if Tribe Commerce tickets are active.
		$has_active_tickets = tec_tribe_commerce_has_active_tickets( true );
		$available          = tec_tribe_commerce_is_available();

		if ( ! $has_active_tickets || ! $available ) {
			return $commerce_fields;
		}

		/** @var Tribe__Tickets__Admin__Views $admin_views */
		$admin_views = tribe( 'tickets.admin.views' );
		$banner_html = $admin_views->template( 'settings/tickets-commerce/banner', [
			'banner_title'   => __( 'Upgrade to Tickets Commerce', 'event-tickets' ),
			'banner_content' => __( 'Try our new Tickets Commerce payment system! It’s fast and simple to set up and offers a better experience and features. Best of all, <i>it’s free!</i>', 'event-tickets' ),
			'button_text'    => __( 'Click here', 'event-tickets' ),
			'button_url'     => \Tribe__Settings::instance()->get_url( [ 'tab' => 'payments' ] ),
			'link_text'      => __( 'Learn more', 'event-tickets' ),
			'link_url'       => 'https://evnt.is/1axt',
			'show_new'       => true,
		], false );

		// Add the banner html after the Tribe Commerce settings header.
		$commerce_fields['ticket-paypal-heading']['html'] .= $banner_html;

		return $commerce_fields;
	}
}
