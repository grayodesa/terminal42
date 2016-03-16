<?php

if ( ! defined( 'ABSPATH' ) )
	exit;

/**
 * Appointment admin
 */
class WC_Appointments_Admin_Ajax {

	/**
	 * Constructor
	 */
	public function __construct() {
		add_action( 'wp_ajax_wc-appointment-confirm', array( $this, 'mark_appointment_confirmed' ) );
		add_action( 'wp_ajax_wc_appointments_calculate_costs', array( $this, 'calculate_costs' ) );
		add_action( 'wp_ajax_nopriv_wc_appointments_calculate_costs', array( $this, 'calculate_costs' ) );
		add_action( 'wp_ajax_wc_appointments_get_slots', array( $this, 'get_time_slots_for_date' ) );
		add_action( 'wp_ajax_nopriv_wc_appointments_get_slots', array( $this, 'get_time_slots_for_date' ) );
		add_action( 'wp_ajax_wc_appointments_json_search_order', array( $this, 'json_search_order' ) );
	}

	/**
	 * Mark a appointment confirmed
	 */
	public function mark_appointment_confirmed() {
		if ( ! current_user_can( 'manage_appointments' ) ) {
			wp_die( __( 'You do not have sufficient permissions to access this page.', 'woocommerce-appointments' ) );
		}
		if ( ! check_admin_referer( 'wc-appointment-confirm' ) ) {
			wp_die( __( 'You have taken too long. Please go back and retry.', 'woocommerce-appointments' ) );
		}
		$appointment_id = isset( $_GET['appointment_id'] ) && (int) $_GET['appointment_id'] ? (int) $_GET['appointment_id'] : '';
		if ( ! $appointment_id ) {
			die;
		}

		$appointment = get_wc_appointment( $appointment_id );
    	if ( $appointment->get_status() !== 'confirmed' ) {
    		$appointment->update_status( 'confirmed' );
    	}

		wp_safe_redirect( wp_get_referer() );
	}

	/**
	 * Calculate costs
	 *
	 * Take posted appointment form values and then use these to quote a price for what has been chosen.
	 * Returns a string which is appended to the appointment form.
	 */
	public function calculate_costs() {

		$posted = array();

		parse_str( $_POST['form'], $posted );

		$appointment_id = $posted['add-to-cart'];
		$product    = get_product( $appointment_id );

		if ( ! $product ) {
			die( json_encode( array(
				'result' => 'ERROR',
				'html'   => '<span class="appointment-error">' . __( 'This appointment is unavailable.', 'woocommerce-appointments' ) . '</span>'
			) ) );
		}

		$appointment_form	= new WC_Appointment_Form( $product );
		$cost             	= $appointment_form->calculate_appointment_cost( $posted );

		if ( is_wp_error( $cost ) ) {
			die( json_encode( array(
				'result' => 'ERROR',
				'html'   => '<span class="appointment-error">' . $cost->get_error_message() . '</span>'
			) ) );
		}

		$tax_display_mode = get_option( 'woocommerce_tax_display_shop' );
		$display_price    = $tax_display_mode == 'incl' ? $product->get_price_including_tax( 1, $cost ) : $product->get_price_excluding_tax( 1, $cost );
		
		if ( version_compare( WC_VERSION, '2.4.0', '>=' ) ) {
			$price_suffix = $product->get_price_suffix( $cost, 1 );
		} else {
			$price_suffix = $product->get_price_suffix();
		}

		die( json_encode( array(
			'result' => 'SUCCESS',
			'html'   => '<dl><dt>' . apply_filters( 'woocommerce_appointments_appointment_cost_string', __( 'Appointment cost', 'woocommerce-appointments' ), $product ) . ':</dt><dd><strong>' . wc_price( $display_price ) . $price_suffix . '</strong></dd></dl>'
		) ) );
	}

	/**
	 * Get a list of time slots available on a date
	 */
	public function get_time_slots_for_date() {
		$posted = array();

		parse_str( $_POST['form'], $posted );

		if ( empty( $posted['add-to-cart'] ) ) {
			return false;
		}

		$appointment_id		= $posted['add-to-cart'];
		$product			= get_product( $appointment_id );
		$appointment_form	= new WC_Appointment_Form( $product );

		if ( ! empty( $posted['wc_appointments_field_start_date_year'] ) && ! empty( $posted['wc_appointments_field_start_date_month'] ) && ! empty( $posted['wc_appointments_field_start_date_day'] ) ) {
			$year      = max( date('Y'), absint( $posted['wc_appointments_field_start_date_year'] ) );
			$month     = absint( $posted['wc_appointments_field_start_date_month'] );
			$day       = absint( $posted['wc_appointments_field_start_date_day'] );
			$timestamp = strtotime( "{$year}-{$month}-{$day}" );
		}

		if ( ! $product || empty( $timestamp ) ) {
			return false;
		}
		
		if ( ! $product ) {
			return false;
		}

		if ( empty( $timestamp ) ) {
			die( esc_html__( 'Please enter a valid date.', 'woocommerce-appointments' ) );
		}

		//* Intervals
		$interval = 'hour' === $product->get_duration_unit() ? $product->wc_appointment_duration * 60 : $product->wc_appointment_duration;
		$base_interval = 'hour' === $product->get_duration_unit() ? $product->wc_appointment_duration * 60 : $product->wc_appointment_duration;
		if ( $product->get_interval_unit() && $product->wc_appointment_interval ) {
			$base_interval = 'hour' === $product->get_interval_unit() ? $product->wc_appointment_interval * 60 : $product->wc_appointment_interval;
		}
		
		
		//* Filters for the intervals
		$interval = apply_filters( 'woocommerce_appointments_interval', $interval, $product );
		$base_interval = apply_filters( 'woocommerce_appointments_base_interval', $base_interval, $product );

		$from				= $time_from = strtotime( 'midnight', $timestamp );
		$to					= strtotime( "tomorrow midnight", $timestamp ) + $interval;
		$time_to_check		= ! empty( $posted['wc_appointments_field_start_date_time'] ) ? strtotime( $posted['wc_appointments_field_start_date_time'] ) : 0;
		$staff_id_to_check	= ! empty( $posted['wc_appointments_field_staff'] ) ? $posted['wc_appointments_field_staff'] : 0;

		if ( $staff_id_to_check && $staff_member = $product->get_staff_member( absint( $staff_id_to_check ) ) ) {
			$staff_id_to_check = $staff_member->ID;
		} elseif ( $product->has_staff() && ( $staff = $product->get_staff() ) && sizeof( $staff ) === 1 ) {
			$staff_id_to_check = current( $staff )->ID;
		} else {
			$staff_id_to_check = 0;
		}

		$slots     = $product->get_slots_in_range( $from, $to, array( $interval, $base_interval ), $staff_id_to_check );
		$slot_html = $product->get_available_slots_html( $slots, array( $interval, $base_interval ), $time_to_check, $staff_id_to_check, $from );

		if ( empty( $slot_html ) ) {
			$slot_html .= __( 'No slots available.', 'woocommerce-appointments' );
		}

		die( $slot_html );
	}

	/**
	 * Search for customers and return json
	 */
	public function json_search_order() {
		global $wpdb;

		check_ajax_referer( 'search-appointment-order', 'security' );

		header( 'Content-Type: application/json; charset=utf-8' );

		$term = wc_clean( stripslashes( $_GET['term'] ) );

		if ( empty( $term ) ) {
			die();
		}

		$found_orders = array();

		$term = apply_filters( 'woocommerce_appointment_json_search_order_number', $term );

		$query_orders = $wpdb->get_results( $wpdb->prepare( "
			SELECT ID, post_title FROM {$wpdb->posts} AS posts
			WHERE posts.post_type = 'shop_order'
			AND posts.ID LIKE %s
		", '%' . $term . '%' ) );

		if ( $query_orders ) {
			foreach ( $query_orders as $item ) {
				$order_number = apply_filters( 'woocommerce_order_number', _x( '#', 'hash before order number', 'woocommerce-appointments' ) . $item->ID, $item->ID );
				$found_orders[ $item->ID ] = $order_number . ' &ndash; ' . esc_html( $item->post_title );
			}
		}

		echo json_encode( $found_orders );
		die();
	}
}

$GLOBALS['wc_appointments_admin_ajax'] = new WC_Appointments_Admin_Ajax();
