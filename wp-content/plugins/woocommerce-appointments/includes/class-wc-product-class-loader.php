<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Appointments supports optional addons.
 * If these optional addons get removed but the data still exists, fatal errors can occur.
 * This class loader will load a dummy class for these appointments if the real one is not found.
 * This prevents purchasing of these products and prevents errors.
 * A notice will also be displayed to the user.
 */

/**
 * Dummy class for our 'addon' classes to load from.
 */
class WC_Product_Skeleton_Appointment extends WC_Product_Appointment {

	public function __construct( $product ) {
		parent::__construct( $product );
	}

	public function is_purchasable() {
		return false;
	}

	public function is_skeleton() {
		return true;
	}

	public function is_appointments_addon() {
		return true;
	}

}
