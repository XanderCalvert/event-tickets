<?php
/**
 * Block: Tickets
 * Extra column, available
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/tickets/blocks/tickets/extra-available.php
 *
 * See more documentation about our Blocks Editor templating system.
 *
 * @link {INSERT_ARTICLE_LINK_HERE}
 *
 * @since 4.9.3
 * @version 4.9.4
 *
 */

$ticket    = $this->get( 'ticket' );
if ( -1 === $ticket->available() ) {
	return;
}
$available = $ticket->available();
?>
<div
	class="tribe-common-b3 tribe-block__tickets__item__extra__available"
>
	<?php if ( -1 !== $ticket->available() ) : ?>
		<?php $this->template( 'blocks/tickets/extra-available-quantity', array( 'ticket' => $ticket, 'key' => $key ) ); ?>
	<?php endif; ?>
</div>
