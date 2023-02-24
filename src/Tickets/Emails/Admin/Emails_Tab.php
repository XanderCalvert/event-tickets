<?php
/**
 * Handles registering and setup for the Tickets Emails settings tab.
 *
 * @since 5.5.6
 *
 * @package TEC\Tickets\Emails
 */

namespace TEC\Tickets\Emails\Admin;

use TEC\Tickets\Emails\Email_Handler;
use Tribe\Tickets\Admin\Settings as Plugin_Settings;
use \Tribe__Template;
use Tribe__Tickets__Main;

/**
 * Class Emails_Tab
 *
 * @since   5.5.6
 *
 * @package TEC\Tickets\Emails
 */
class Emails_Tab {

	/**
	 * Slug for the tab.
	 *
	 * @since 5.5.6
	 *
	 * @var string
	 */
	public static $slug = 'emails';
	
	/**
	 * Holder for template object.
	 *
	 * @since TBD
	 *
	 * @var null|Tribe_Template
	 */
	private $template;

	/**
	 * Edit section slug.
	 * 
	 * @since TBD
	 * 
	 * @var string
	 */
	public static $edit_section_slug = 'edit_email';

	/**
	 * Editing id key.
	 * 
	 * @since TBD
	 * 
	 * @var string
	 */
	public static $edit_id_key = 'id';

	/**
	 * Create the Tickets Commerce Emails Settings Tab.
	 *
	 * @since  5.5.6
	 *
	 * @param $admin_page Page ID of current admin page.
	 */
	public function register_tab( $admin_page ) {
		if ( ! empty( $admin_page ) && Plugin_Settings::$settings_page_id !== $admin_page ) {
			return;
		}

		$tab_settings = [
			'priority'  => 25,
			'fields'    => $this->get_fields(),
			'show_save' => true,
		];

		$tab_settings = apply_filters( 'tec_tickets_commerce_emails_tab_settings', $tab_settings );

		new \Tribe__Settings_Tab( static::$slug, esc_html__( 'Emails', 'event-tickets' ), $tab_settings );
	}

	/**
	 * Add the emails tab to the list of tab ids for the Tickets settings.
	 *
	 * @since 5.5.6
	 *
	 * @param  array $tabs Current array of tabs ids.
	 *
	 * @return array $tabs Filtered array of tabs ids.
	 */
	public function settings_add_tab_id( array $tabs ): array {
		$tabs[] = static::$slug;

		return $tabs;
	}

	/**
	 * Gets the template instance used to setup the rendering html.
	 *
	 * @since 5.5.6
	 *
	 * @return Tribe__Template
	 */
	public function get_template(): Tribe__Template {
		if ( empty( $this->template ) ) {
			$this->template = new Tribe__Template();
			$this->template->set_template_origin( Tribe__Tickets__Main::instance() );
			$this->template->set_template_folder( 'src/admin-views/settings' );
			$this->template->set_template_context_extract( true );
		}

		return $this->template;
	}

	/**
	 * Gets the URL for the Emails Tab.
	 *
	 * @since 5.5.6
	 *
	 * @param array $args Which query args we are adding.
	 *
	 * @return string
	 */
	public function get_url( array $args = [] ): string {
		// Force the `emails` tab.
		$args['tab'] = static::$slug;

		// Use the settings page get_url to build the URL.
		return tribe( Plugin_Settings::class )->get_url( $args );
	}

	/**
	 * Determine if is on "tab".
	 *
	 * @since 5.5.7
	 *
	 * @return boolean True when on `emails` tab.
	 */
	public function is_on_tab(): bool {
		$settings = tribe( Plugin_Settings::class );

		return $settings->is_on_tab( self::$slug );
	}

	/**
	 * Determine if is on a "section" of the "emails" tab.
	 *
	 * @since TBD
	 *
	 * @param string $section The section.
	 *
	 * @return boolean True when on `emails` tab and on `section`
	 */
	public function is_on_section( $section = '' ): bool {
		$settings = tribe( Plugin_Settings::class );

		return $settings->is_on_tab_section( self::$slug, $section );
	}

	/**
	 * Gets the top level settings for Tickets Commerce.
	 *
	 * @since 5.5.6
	 *
	 * @return array[]
	 */
	public function get_fields(): array {

		// Check to see if we're editing an email, first.
		if ( $this->is_on_section( static::$edit_section_slug ) ) {
			return $this->get_email_settings();
		}

		$fields = [];
		$fields['tribe-form-content-start'] = [
			'type' => 'html',
			'html' => '<div class="tribe-settings-form-wrap">',
		];
		$fields['tribe-tickets-emails-header'] = [
			'type' => 'html',
			'html' => '<h2>' . esc_html__( 'Tickets Emails', 'event-tickets' ) . '</h2>',
		];
		$kb_link_html = sprintf( '<a href="%s" target="_blank" rel="nofollow">%s</a>',
			'https://www.theeventscalendar.com', // @todo Replace with correct KB URL.
			esc_html__( 'Knowledgebase', 'event-tickets' )
		);
		$description_text = sprintf(
			// Translators: %s Link to knowledgebase article.
			esc_html__( 'Customize your customer communications when tickets are purchased, RSVPs are submitted, and for Tickets Commerce order notifications.  Learn More about Tickets Commerce communications in our %s.', 'event-tickets' ),
			$kb_link_html
		);
		$fields['tribe-tickets-emails-description'] = [
			'type' => 'html',
			'html' => sprintf( '<p>%s</p>', $description_text ),
		];

		/**
		 * Hook to modify the settings fields for Tickets Emails.
		 *
		 * @since 5.5.6
		 *
		 * @param array[] $fields Top level settings.
		 */
		return apply_filters( 'tec_tickets_emails_settings_fields', $fields );
	}

	/**
	 * Check if currently editing email.
	 * 
	 * @since TBD
	 *
	 * @param Email_Abstract $email
	 * 
	 * @return boolean
	 */
	public function is_editing_email( $email = null ) {
		if ( ! $this->is_on_section( static::$edit_section_slug ) ) {
			return false;
		}

		// Get `tc-edit` query string from URL.
		$editing_email  = tribe_get_request_var( static::$edit_id_key );

		// If email wasn't passed, just return whether or not string is empty.
		if ( empty( $email ) ) {
			return ! empty( $editing_email );
		}

		// Otherwise, return whether or not supplied email is being edited.
		return $email->id === $editing_email;
	}

	/**
	 * Get email settings.
	 *
	 * @since TBD
	 * 
	 * @return array|null Settings array
	 */
	public function get_email_settings() {
		$email_id  = tribe_get_request_var( static::$edit_id_key );
		$email = tribe( Email_Handler::class )->get_email_by_id( $email_id );
		
		$back_link = [[
			'type' => 'html',
			'html' => $this->get_template()->template( 'back-link', [
				'text' => __( 'Back to Email Settings', 'event-tickets' ),
				'url'  => $this->get_url(),
			], false ),
		]];

		if ( ! $email ) {
			return array_merge( $back_link, [
				[
					'type' => 'html',
					'html' => '<p>' . esc_html__( 'Invalid email id selected.', 'event-tickets') . '</p>',
				]
			] );
		}

		$settings = $email->get_settings();

		return array_merge( $back_link, $settings );
	}
}
