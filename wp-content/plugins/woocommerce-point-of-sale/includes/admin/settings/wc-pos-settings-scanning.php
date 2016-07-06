<?php
/**
 * WooCommerce POS General Settings
 *
 * @author    Actuality Extensions
 * @package   WoocommercePointOfSale/Classes/settings
 * @category	Class
 * @since     0.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'WC_POS_Admin_Settings_Scanning' ) ) :

/**
 * WC_POS_Admin_Settings_Layout
 */
class WC_POS_Admin_Settings_Scanning extends WC_Settings_Page {

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->id    = 'scanning_pos';
		$this->label = __( 'Scanning', 'woocommerce' );

		add_filter( 'wc_pos_settings_tabs_array', array( $this, 'add_settings_page' ), 20 );
		add_action( 'wc_pos_settings_' . $this->id, array( $this, 'output' ) );
		add_action( 'wc_pos_settings_save_' . $this->id, array( $this, 'save' ) );

	}

	/**
	 * Get settings array
	 *
	 * @return array
	 */
	public function get_settings() {
		global $woocommerce;

		return apply_filters( 'woocommerce_point_of_sale_general_settings_fields', array(
			
		array( 'title' => __( 'Scanning Options', 'wc_point_of_sale' ), 'desc' => __( 'The following options affect the use of scanning hardware such as barcode scanners and magnetic card readers.', 'wc_point_of_sale' ), 'type' => 'title', 'id' => 'scanning_options' ),
			
			array(
					'title' => __( 'Barcode Scanning', 'wc_point_of_sale' ),
					'id'   => 'woocommerce_pos_register_ready_to_scan',
					'std'  => '',
					'type' => 'checkbox',
					'desc' => __( 'Enable barcode scanning', 'wc_point_of_sale' ),
					'desc_tip' => __( 'Listens to barcode scanners and adds item to basket. Carriage return in scanner recommended.', 'wc_point_of_sale' ),
					'default'	=> 'no',
					'autoload'  => false					
				),

			array(
					'name' => __( 'Credit/Debit Card Scanning', 'wc_point_of_sale' ),
					'id'   => 'woocommerce_pos_register_cc_scanning',
					'std'  => '',
					'type' => 'checkbox',
					'desc' => __( 'Enable credit/debit card scanning', 'wc_point_of_sale' ),
					'desc_tip' => sprintf(__( 'Allows magnetic card readers to parse scanned output into checkout fields. Supported payment gateways can be found here %shere%s.', 'wc_point_of_sale' ), 
				'<a href="http://actualityextensions.com/supported-payment-gateways/" target="_blank">', '</a>'),
					'default'	=> 'no',
					'autoload'  => false					
				),
			array( 'type' => 'sectionend', 'id' => 'scanning_options'),
		
		)); // End general settings

	}

	/**
	 * Save settings
	 */
	public function save() {
		$settings = $this->get_settings();
		WC_POS_Admin_Settings::save_fields( $settings );
	}

}

endif;

return new WC_POS_Admin_Settings_Scanning();