<?php
/**
 * Tickets Commerce: Checkout Buttons for Stripe
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/tickets/v2/commerce/gateway/stripe/buttons.php
 *
 * See more documentation about our views templating system.
 *
 * @link    https://evnt.is/1amp Help article for RSVP & Ticket template files.
 *
 * @since   TBD
 *
 * @version TBD
 * @var bool $must_login [Global] Whether login is required to buy tickets or not.
 * @var bool $payment_element [Global] Whether to load the Stripe Payment Element.
 */

if ( $must_login ) {
	return;
}
?>
<form id="payment-form">

	<?php if ( $payment_element ) : ?>
		<?php $this->template( 'gateway/stripe/payment_element' ); ?>
	<?php else: ?>
		<?php $this->template( 'gateway/stripe/card_element' ); ?>
	<?php endif; ?>

</form>