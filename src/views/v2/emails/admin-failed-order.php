<?php
/**
 * Event Tickets Emails: Failed Order Template
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/tickets/v2/emails/admin-failed-order.php
 *
 * See more documentation about our views templating system.
 *
 * @link https://evnt.is/tickets-emails-tpl Help article for Tickets Emails template files.
 *
 * @version TBD
 *
 * @since TBD
 *
 * @var Tribe_Template  $this  Current template object.
 */


$this->template( 'template-parts/header' );
$this->template( 'admin-failed-order/body' );
$this->template( 'template-parts/footer' );
