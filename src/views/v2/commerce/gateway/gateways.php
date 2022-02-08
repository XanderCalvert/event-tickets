<?php
/**
 * Tickets Commerce: Checkout Page Footer > Gateway error
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/tickets/v2/commerce/checkout/footer/gateway-error.php
 *
 * See more documentation about our views templating system.
 *
 * @link    https://evnt.is/1amp Help article for RSVP & Ticket template files.
 *
 * @since   5.1.10
 *
 * @version 5.1.10
 *
 * @var \Tribe__Template   $this               [Global] Template object.
 * @var Module             $provider           [Global] The tickets provider instance.
 * @var string             $provider_id        [Global] The tickets provider class name.
 * @var array[]            $items              [Global] List of Items on the cart to be checked out.
 * @var bool               $must_login         [Global] Whether login is required to buy tickets or not.
 * @var string             $login_url          [Global] The site's login URL.
 * @var string             $registration_url   [Global] The site's registration URL.
 * @var bool               $is_tec_active      [Global] Whether `The Events Calendar` is active or not.
 * @var Abstract_Gateway[] $gateways           [Global] An array with the gateways.
 * @var int                $gateways_active    [Global] The number of active gateways.
 * @var int                $gateways_connected [Global] The number of connected gateways.
 * @var int                $gateways_enabled   [Global] The number of enabled gateways.
 */

// Bail if user needs to login, the cart is empty or if there are no active gateways.
if ( $must_login || empty( $items ) || ! tribe_is_truthy( $gateways_active ) || ! tribe_is_truthy( $gateways_enabled ) ) {
	return;
}

?>
<div class="tribe-tickets__commerce-checkout-gateways">
    <h4 class="tribe-tickets__commerce-checkout-section-header">
		<?php esc_html_e( 'Choose Payment', 'event-tickets' ); ?>
    </h4>
<?php
foreach ( $gateways as $gateway ) {
    if ( ! $gateway->is_enabled() || ! $gateway->is_active() ) {
        continue;
    }
    $template_path = 'gateway/' . $gateway->get_key() . '/' . $gateway->get_checkout_container_template_name();
    $this->template( $template_path, $gateway->get_checkout_template_vars() );
}
?>
</div>