<?php
/**
 * Event Tickets Emails: Order Purchaser Details - Name
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/tickets/v2/emails/template-parts/body/order/purchaser-details/name.php
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
 * @var Module           $provider              [Global] The tickets provider instance.
 * @var string           $provider_id           [Global] The tickets provider class name.
 * @var array            $order                 [Global] The order object.
 * @var int              $order_id              [Global] The order ID.
 * @var bool             $is_tec_active         [Global] Whether `The Events Calendar` is active or not.
 */

$purchaser_name = empty( $order['purchaser'] ) || empty( $order['purchaser']['name'] ) ? '' : $order['purchaser']['name'];

?>
<td class="tec-tickets__email-table-content-order-purchaser-details-top tec-tickets__email-table-content-align-right" align="right">
	<?php esc_html_e( $purchaser_name ); ?>
</td>
