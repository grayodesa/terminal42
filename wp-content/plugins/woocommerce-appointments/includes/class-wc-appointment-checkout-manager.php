<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * WC_Appointment_Checkout_Manager class.
 */
class WC_Appointments_Checkout_Manager {

	/**
	 * Constructor
	 */
	public function __construct() {
		add_filter( 'woocommerce_available_payment_gateways', array( $this, 'remove_payment_methods' ) );
		add_filter( 'woocommerce_cart_needs_payment', array( $this, 'appointment_requires_confirmation' ), 10, 2 );
	}

	/**
	 * Removes all payment methods when cart has a appointment that requires confirmation.
	 *
	 * @param  array $available_gateways
	 * @return array
	 */
	public function remove_payment_methods( $available_gateways ) {

		if ( wc_appointment_cart_requires_confirmation() ) {
			unset( $available_gateways );

			$available_gateways = array();
			$available_gateways['wc-appointment-gateway'] = new WC_Appointments_Gateway();
		}

		return $available_gateways;
	}

	/**
	 * Always require payment if the order have a appointment that requires confirmation.
	 *
	 * @param  bool $needs_payment
	 * @param  WC_Cart $cart
	 *
	 * @return bool
	 */
	public function appointment_requires_confirmation( $needs_payment, $cart ) {
		if ( ! $needs_payment ) {
			foreach ( $cart->cart_contents as $cart_item ) {
				if ( wc_appointment_requires_confirmation( $cart_item['product_id'] ) ) {
					$needs_payment = true;
					break;
				}
			}
		}

		return $needs_payment;
	}
}

$GLOBALS['wc_appointments_checkout_manager'] = new WC_Appointments_Checkout_Manager();
