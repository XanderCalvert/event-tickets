<?php
/**
 * Tickets Commerce: Checkout Page Header Links > Back
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/tickets/v2/commerce/checkout/header/links/back.php
 *
 * See more documentation about our views templating system.
 *
 * @link    https://evnt.is/1amp Help article for RSVP & Ticket template files.
 *
 * @since   TBD
 *
 * @version TBD
 *
 * @var \Tribe__Template $this                  [Global] Template object.
 * @var Module           $provider              [Global] The tickets provider instance.
 * @var string           $provider_id           [Global] The tickets provider class name.
 * @var array[]          $items                 [Global] List of Items on the cart to be checked out.
 * @var string           $paypal_attribution_id [Global] What is our PayPal Attribution ID.
 * @var array[]          $sections              [Global] Which events we have tickets for.
 * @var bool             $must_login            [Global] Whether login is required to buy tickets or not.
 * @var string           $login_url             [Global] The site's login URL.
 * @var string           $registration_url      [Global] The site's registration URL.
 * @var bool             $is_tec_active         [Global] Whether `The Events Calendar` is active or not.
 */

if ( empty( $items ) ) {
	return;
}

$anchor_text = $is_tec_active ? __( 'back to event', 'event-tickets' ) : __( 'back', 'event-tickets' );
?>

<a
	class="tribe-common-anchor-alt tribe-tickets__commerce-checkout-header-link-back-to-event"
	href="<?php the_permalink( $sections[ key( $sections ) ] ); ?>"
><?php echo esc_html( $anchor_text ); ?></a>
