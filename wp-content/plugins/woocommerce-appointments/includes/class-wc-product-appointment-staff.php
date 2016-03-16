<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class for a appointment product's staff type
 */
class WC_Product_Appointment_Staff {

	private $staff;
	private $product_id;

	/**
	 * Constructor
	 */
	public function __construct( $user, $product_id = 0 ) {
		$this->staff   = $user;
		$this->product_id = $product_id;
	}

	/**
	 * __isset function.
	 *
	 * @access public
	 * @param string $key
	 * @return bool
	 */
	public function __isset( $key ) {
		return isset( $this->staff->$key );
	}

	/**
	 * __get function.
	 *
	 * @access public
	 * @param string $key
	 * @return string
	 */
	public function __get( $key ) {
		return $this->staff->$key;
	}

	/**
	 * Return the ID
	 * @return int
	 */
	public function get_id() {
		return $this->staff->ID;
	}

	/**
	 * Get the title of the staff
	 * @return string
	 */
	public function get_title() {
		return $this->staff->display_name;
	}
	
	/**
	 * Return the base cost
	 * @return int|float
	 */
	public function get_base_cost() {
		$costs = get_post_meta( $this->product_id, '_staff_base_costs', true );
		$cost  = isset( $costs[ $this->get_id() ] ) ? $costs[ $this->get_id() ] : '';

		return $cost;
	}

}
