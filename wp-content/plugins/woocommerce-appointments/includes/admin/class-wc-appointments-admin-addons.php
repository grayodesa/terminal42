<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Appointment Addons Screen
 */
class WC_Appointments_Admin_Add_Ons {

	/**
	 * Constructor
	 */
	public function __construct() {
		add_filter( 'woocommerce_addons_sections', array( $this, 'add_section' )  );
	}

	/**
	 * Adds a new section for "appointments" add-ons
	 */
	public function add_section( $sections ) {
		$sections['appointments'] = new stdClass;
		$sections['appointments']->title = wc_clean( __( 'Appointments', 'woocommerce-appointments' ) );
		$sections['appointments']->endpoint = plugin_dir_url( __FILE__ ) . 'includes/admin/views/html-appointments-addons.json';
		
		return $sections;
	}

}

new WC_Appointments_Admin_Add_Ons();
