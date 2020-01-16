<?php
/**
 * Block: Tickets
 * Submit Button
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/tickets/blocks/tickets/submit-button.php
 *
 * See more documentation about our Blocks Editor templating system.
 *
 * @link {INSERT_ARTICLE_LINK_HERE}
 *
 * @since 4.9.3
 * @since TBD Updated the button to include a type - helps avoid submitting forms unintentionally.
 * @since TBD Allow filtering of the button classes.
 * @since TBD Added button ID for better JS targeting.
 *
 * @version TBD
 *
 */

/**
 * Allow filtering of the button name for the tickets block.
 *
 * @since 4.11.0
 *
 * @param string $button_name The button name. Set to cart-button to send to cart on submit, or set to checkout-button to send to checkout on submit.
 */
$button_name = apply_filters( 'tribe_tickets_ticket_block_submit', 'cart-button' );

/**
 * Allow filtering of the button classes for the tickets block.
 *
 * @since TBD
 *
 * @param array $button_name The button classes.
 */
$button_classes = apply_filters( 'tribe_tickets_ticket_block_submit_classes', [
	'tribe-common-c-btn',
	'tribe-common-c-btn--small',
	'tribe-tickets__buy',
] );

?>
<button
	<?php tribe_classes( $button_classes ) ?>
	id="tribe-tickets__buy"
	type="submit"
	<?php if ( $button_name ) : ?>
		name="<?php echo esc_html( $button_name ); ?>"
	<?php endif; ?>
	<?php tribe_disabled( true ); ?>
>
	<?php echo esc_html_x( 'Get Tickets', 'Add tickets to cart.', 'event-tickets' ); ?>
</button>
