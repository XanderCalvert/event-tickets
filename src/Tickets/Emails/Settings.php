<?php

/**
 * Tickets Emails Settings class
 *
 * @since   TBD
 *
 * @package TEC\Tickets\Emails
 *
 */

namespace TEC\Tickets\Emails;

use Tribe__Template;
use Tribe__Tickets__Main;

class Settings {

	/**
	 * The option key for email sender's name.
	 *
	 * @since TBD
	 *
	 * @var string
	 */
	static $option_sender_name = 'tec-tickets-emails-sender-name';

	/**
	 * The option key for email sender's email.
	 *
	 * @since TBD
	 *
	 * @var string
	 */
	static $option_sender_email = 'tec-tickets-emails-sender-email';

	/**
	 * The option key for the email header image url.
	 *
	 * @since TBD
	 *
	 * @var string
	 */
	static $option_header_image_url = 'tec-tickets-emails-header-image-url';

	/**
	 * The option key for the email header image alignment.
	 *
	 * @since TBD
	 *
	 * @var string
	 */
	static $option_header_image_alignment = 'tec-tickets-emails-header-image-alignment';

	/**
	 * The option key for the email header background color.
	 *
	 * @since TBD
	 *
	 * @var string
	 */
	static $option_header_bg_color = 'tec-tickets-emails-header-bg-color';

	/**
	 * The option key for the email ticket background color.
	 *
	 * @since TBD
	 *
	 * @var string
	 */
	static $option_ticket_bg_color = 'tec-tickets-emails-ticket-bg-color';

	/**
	 * The option key for the email footer content.
	 *
	 * @since TBD
	 *
	 * @var string
	 */
	static $option_footer_content = 'tec-tickets-emails-footer-content';

	/**
	 * The option key for the email footer credit.
	 *
	 * @since TBD
	 *
	 * @var string
	 */
	static $option_footer_credit = 'tec-tickets-emails-footer-credit';

	/**
	 * Gets the template instance used to setup the rendering html.
	 *
	 * @since TBD
	 *
	 * @return Tribe__Template
	 */
	public function get_template() {
		if ( empty( $this->template ) ) {
			$this->template = new Tribe__Template();
			$this->template->set_template_origin( Tribe__Tickets__Main::instance() );
			$this->template->set_template_folder( 'src/admin-views/settings/tickets-emails' );
			$this->template->set_template_context_extract( true );
		}

		return $this->template;
	}

	/**
	 * Adds list of Templates to the Tickets Emails settings tab.
	 * 
	 * @param  [] $fields Current array of Tickets Emails settings fields.
	 * 
	 * @return [] $fields Filtered array of Tickets Emails settings fields.
	 */
	public function add_template_list( $fields ) {

		$template = $this->get_template();

		// @todo Replace this with array of actual Message Template objects that do not yet exist.
		$templates = [
			[
				'title'     => 'Ticket Email',
				'enabled'   => true,
				'recipient' => 'Purchaser',
			],
			[
				'title'     => 'RSVP Email',
				'enabled'   => true,
				'recipient' => 'Attendee',
			],
			[
				'title'     => 'Order Notification',
				'enabled'   => false,
				'recipient' => 'Site Admin',
			],
			[
				'title'     => 'Order Failure',
				'enabled'   => true,
				'recipient' => 'Site Admin',
			],
		];

		$new_fields = [
			[
				'type' => 'html',
				'html' => $template->template( 'message-templates', [ 'templates' => $templates ], false ),
			],
		];

		$new_fields = apply_filters( 'tec_tickets_emails_settings_template_list', $new_fields );

		return array_merge( $fields, $new_fields );
	}

	/**
	 * Adds Sender Info fields to Tickets Emails settings.
	 *
	 * @param  [] $fields Current array of Tickets Emails settings fields.
	 * 
	 * @return [] $fields Filtered array of Tickets Emails settings fields.
	 */
	public function sender_info_fields( $fields ) {

		$current_user = get_user_by( 'id', get_current_user_id() );

		$new_fields = [
			[
				'type' => 'html',
				'html' => '<h3>' . esc_html__( 'Sender Information', 'event-tickets' ) . '</h3>',
			],
			[
				'type' => 'html',
				'html' => '<p>' . esc_html__( 'If fields are empty, sender information will be from the site owner set in WordPress general settings.', 'event-tickets' ) . '</p>',
			],
			static::$option_sender_name  => [
				'type'                => 'text',
				'label'               => esc_html__( 'Sender Name', 'event-tickets' ),
				'size'                => 'medium',
				'default'             => $current_user->user_nicename,
				'validation_callback' => 'is_string',
				'validation_type'     => 'textarea',
			],
			static::$option_sender_email  => [
				'type'                => 'text',
				'label'               => esc_html__( 'Sender Email', 'event-tickets' ),
				'size'                => 'medium',
				'default'             => $current_user->user_email,
				'validation_callback' => 'is_string',
				'validation_type'     => 'email',
			],
		];

		$new_fields = apply_filters( 'tec_tickets_emails_settings_sender_info_fields', $new_fields );

		return array_merge( $fields, $new_fields );
	}

	/**
	 * Adds Sender Info fields to Tickets Emails settings.
	 *
	 * @param  [] $fields Current array of Tickets Emails settings fields.
	 * 
	 * @return [] $fields Filtered array of Tickets Emails settings fields.
	 */
	public function email_styling_fields( $fields ) {

		$new_fields = [
			[
				'type' => 'html',
				'html' => '<h3>' . esc_html__( 'Email Styling', 'event-tickets' ) . '</h3>',
			],
			[
				'type' => 'html',
				'html' => '<p>' . esc_html__( 'Add a logo and customize link colors and footer information to personalize your communications.  If you\'d like more granular control over email styling, you can override the email templates in your theme.  Learn More', 'event-tickets' ) . '</p>',
			],
			static::$option_header_image_url  => [
				'type'                => 'image',
				'label'               => esc_html__( 'Header Image', 'event-tickets' ),
				'size'                => 'medium',
				'default'             => '',
				'validation_callback' => 'is_string',
				'validation_type'     => 'url',
			],
			static::$option_header_image_alignment  => [
				'type'            => 'dropdown',
				'label'           => esc_html__( 'Image Alignment', 'event-tickets' ),
				'default'         => 'left',
				'validation_type' => 'options',
				'options'         => [
					'left'   => esc_html__( 'Left', 'event-tickets' ),
					'center' => esc_html__( 'Center', 'event-tickets' ),
					'right'  => esc_html__( 'Right', 'event-tickets' ),
				],
			],
			static::$option_header_bg_color  => [
				'type'                => 'color',
				'label'               => esc_html__( 'Header/Footer Background', 'event-tickets' ),
				'size'                => 'medium',
				'default'             => '#ffffff',
				'validation_callback' => 'is_string',
				'validation_type'     => 'color',
			],
			static::$option_ticket_bg_color  => [
				'type'                => 'color',
				'label'               => esc_html__( 'Ticket Color', 'event-tickets' ),
				'size'                => 'medium',
				'default'             => '#ffffff',
				'validation_callback' => 'is_string',
				'validation_type'     => 'color',
			],
			static::$option_footer_content  => [
				'type'                => 'wysiwyg',
				'label'               => esc_html__( 'Footer Content', 'event-tickets' ),
				'tooltip'             => esc_html__( 'Add custom links and instructions to the bottom of your emails.', 'event-tickets' ),
				'default'             => '',
				'validation_type'     => 'html',
				'settings'            => [
					'media_buttons' => false,
					'quicktags'     => false,
					'editor_height' => 200,
					'buttons'       => [
						'bold',
						'italic',
						'underline',
						'strikethrough',
					],
				]
			],
			static::$option_footer_credit => [
				'type'            => 'checkbox_bool',
				'label'           => esc_html__( 'Footer Credit', 'event-tickets' ),
				'tooltip'         => esc_html__( 'Include "Ticket powered by Event Tickets Plus" in the footer', 'event-tickets' ),
				'default'         => true,
				'validation_type' => 'boolean',
			],
		];

		$new_fields = apply_filters( 'tec_tickets_emails_settings_email_styling_fields', $new_fields );

		return array_merge( $fields, $new_fields );
	}
}