<?php
/**
 * Gets appointments
 */
class WC_Appointments_Controller {

	/**
	 * Return all appointments for a product in a given range
	 * @param  timestamp $start_date
	 * @param  timestamp $end_date
	 * @param  int product_or_staff_id
	 * @return array of appointments
	 */
	public static function get_appointments_in_date_range( $start_date, $end_date, $product_or_staff_id = '', $check_in_cart = true ) {
		$transient_name = 'schedule_dr_' . md5( http_build_query( array( $start_date, $end_date, $product_or_staff_id, WC_Cache_Helper::get_transient_version( 'appointments' ) ) ) );
		
		if ( false === ( $appointment_ids = get_transient( $transient_name ) ) ) {
			$appointment_ids = self::get_appointments_in_date_range_query( $start_date, $end_date, $product_or_staff_id, $check_in_cart );
			set_transient( $transient_name, $appointment_ids, DAY_IN_SECONDS * 30 );
		}

		// Get objects
		$appointments = array();

		foreach ( $appointment_ids as $appointment_id ) {
			$appointments[] = get_wc_appointment( $appointment_id );
		}

		return $appointments;
	}
	
	/**
	 * Finds days which are partially scheduled & fully scheduled already
	 * @param  int $product_id
	 * @return array( 'partially_scheduled_days', 'fully_scheduled_days' )
	 */
	public static function find_scheduled_day_slots( $product_id ) {
		$product = wc_get_product( $product_id );

		// Bare existing appointments into consideration for datepicker
		$fully_scheduled_days     = array();
		$partially_scheduled_days = array();
		$remaining_scheduled_days = array();
		$find_appointments_for    = array( $product->id );
		$staff_count        = 0;

		if ( $product->has_staff() ) {
			foreach ( $product->get_staff() as $staff ) {
				$find_appointments_for[] = $staff->ID;
				$staff_count ++;
			}
		}

		$appointment_statuses = get_wc_appointment_statuses();
		$existing_appointments  = self::get_appointments_for_objects( $find_appointments_for, $appointment_statuses );
		
		// Is today fully scheduled/no longer available?
		$slots_in_range  = $product->get_slots_in_range( strtotime( 'midnight' ), strtotime( 'tomorrow midnight' ) );
		$available_slots = $product->get_available_slots( $slots_in_range );

		if ( sizeof( $available_slots ) < sizeof( $slots_in_range ) ) {
			$partially_scheduled_days[ date( 'Y-n-j' ) ][0] = true;
			$remaining_scheduled_days[ date( 'Y-n-j' ) ][0] = round((sizeof( $available_slots ) / sizeof( $slots_in_range )) * 10);
		}

		if ( ! $available_slots ) {
			$fully_scheduled_days[ date( 'Y-n-j' ) ][0] = true;
		}

		// Use the existing appointments to find days which are fully scheduled
		foreach ( $existing_appointments as $existing_appointment ) {
			if ( $existing_appointment->id === null ) {
				continue;
			}
			
			$start_date	= $existing_appointment->start;
			$end_date	= $existing_appointment->is_all_day() ? strtotime( 'tomorrow midnight', $existing_appointment->end ) : $existing_appointment->end;
			$staff_id 	= $existing_appointment->get_staff_id();
			$check_date	= $start_date; // Take it from the top

			// Loop over all scheduled days in this appointment
			while ( $check_date < $end_date ) {
				$js_date = date( 'Y-n-j', $check_date );

				if ( $check_date < current_time( 'timestamp' ) ) {
					$check_date = strtotime( "+1 day", $check_date );
					continue;
				}

				if ( $product->has_staff() ) {

					// Skip if we've already found this staff is unavailable
					if ( ! empty( $fully_scheduled_days[ $js_date ][ $staff_id ] ) ) {
						$check_date = strtotime( "+1 day", $check_date );
						continue;
					}

					$slots_in_range  = $product->get_slots_in_range( strtotime( 'midnight', $check_date ), strtotime( 'tomorrow midnight -1 min', $check_date ), array(), $staff_id );
					$available_slots = $product->get_available_slots( $slots_in_range, array(), $staff_id );
					
					if ( sizeof( $available_slots ) < sizeof( $slots_in_range ) ) {
						$partially_scheduled_days[ $js_date ][ $staff_id ] = true;
						$remaining_scheduled_days[ $js_date ][ $staff_id ] = round((sizeof( $available_slots ) / sizeof( $slots_in_range )) * 10);

						if ( 1 === $staff_count || sizeof( $partially_scheduled_days[ $js_date ] ) === $staff_count ) {
							$partially_scheduled_days[ $js_date ][0] = true;
							$remaining_scheduled_days[ $js_date ][0] = round((sizeof( $available_slots ) / sizeof( $slots_in_range )) * 10);
						}
					}

					if ( ! $available_slots ) {
						$fully_scheduled_days[ $js_date ][ $staff_id ] = true;

						if ( 1 === $staff_count || sizeof( $fully_scheduled_days[ $js_date ] ) === $staff_count ) {
							$fully_scheduled_days[ $js_date ][0] = true;
						}
					}
					
					if ( in_array( $product->get_duration_unit(), array( 'day' ) ) ) {
						foreach ( $slots_in_range as $date ) {
							$partially_scheduled_days[ date( 'Y-n-j', $date ) ][0] = true;
							$remaining_scheduled_days[ date( 'Y-n-j', $date ) ][0] = round((sizeof( $available_slots ) / sizeof( $slots_in_range )) * 10);
						}
					}

				} else {

					// Skip if we've already found this product is unavailable
					if ( ! empty( $fully_scheduled_days[ $js_date ] ) ) {
						$check_date = strtotime( "+1 day", $check_date );
						continue;
					}
					
					$slots_in_range  = $product->get_slots_in_range( strtotime( 'midnight', $check_date ), strtotime( 'tomorrow midnight -1 min', $check_date ) );
					$available_slots = $product->get_available_slots( $slots_in_range );
					
					if ( sizeof( $available_slots ) < sizeof( $slots_in_range ) ) {
						$partially_scheduled_days[ $js_date ][0] = true;
						$remaining_scheduled_days[ $js_date ][0] = round((sizeof( $available_slots ) / sizeof( $slots_in_range )) * 10);
					}

					if ( ! $available_slots ) {
						$fully_scheduled_days[ $js_date ][0] = true;
					}
					
					if ( in_array( $product->get_duration_unit(), array( 'day' ) ) ) {
						foreach ( $slots_in_range as $date ) {
							$partially_scheduled_days[ date( 'Y-n-j', $date ) ][0] = true;
							$remaining_scheduled_days[ date( 'Y-n-j', $date ) ][0] = round( ( sizeof( $available_slots ) / sizeof( $slots_in_range ) ) * 10 );
						}
					}
				}
				$check_date = strtotime( "+1 day", $check_date );
			}
		}
		
		return array(
			'partially_scheduled_days' => $partially_scheduled_days,
			'remaining_scheduled_days' => $remaining_scheduled_days,
			'fully_scheduled_days'     => $fully_scheduled_days,
		);
	}

	/**
	 * Return all appointments for a product in a given range - the query part (no cache)
	 * @param  int $product_id
	 * @param  timestamp $start_date
	 * @param  timestamp $end_date
	 * @param  int product_or_staff_id
	 * @return array of appointment ids
	 */
	private static function get_appointments_in_date_range_query( $start_date, $end_date, $product_or_staff_id = '', $check_in_cart = true ) {
		global $wpdb;
		
		if ( $product_or_staff_id ) {
			$user = get_user_by( 'id', $product_or_staff_id );
			if ( isset( $user ) && is_object( $user ) && in_array( 'shop_staff', (array) $user->roles ) ) {
				$product_meta_key_q    = ' AND idmeta.meta_key = "_appointment_staff_id" AND idmeta.meta_value = "' . absint( $product_or_staff_id ) . '" ';
				$product_meta_key_join = " LEFT JOIN {$wpdb->postmeta} as idmeta ON {$wpdb->posts}.ID = idmeta.post_id ";
			} else {
				$product_meta_key_q    = ' AND idmeta.meta_key = "_appointment_product_id" AND idmeta.meta_value = "' . absint( $product_or_staff_id ) . '" ';
				$product_meta_key_join = " LEFT JOIN {$wpdb->postmeta} as idmeta ON {$wpdb->posts}.ID = idmeta.post_id ";
			}
		} else {
			$product_meta_key_join = '';
			$product_meta_key_q    = '';
		}
		
		$appointment_statuses = get_wc_appointment_statuses();

		if ( ! $check_in_cart ) {
			$appointment_statuses = array_diff( $appointment_statuses, array( 'in-cart' ) );
		}

		$appointment_ids = $wpdb->get_col( $wpdb->prepare( "
			SELECT ID FROM {$wpdb->posts}
			LEFT JOIN {$wpdb->postmeta} as startmeta ON {$wpdb->posts}.ID = startmeta.post_id
			LEFT JOIN {$wpdb->postmeta} as endmeta ON {$wpdb->posts}.ID = endmeta.post_id
			LEFT JOIN {$wpdb->postmeta} as daymeta ON {$wpdb->posts}.ID = daymeta.post_id
			" . $product_meta_key_join . "

			WHERE post_type = 'wc_appointment'
			AND post_status IN ( '" . implode( "','", array_map( 'esc_sql', $appointment_statuses ) ) . "' )
			AND startmeta.meta_key = '_appointment_start'
			AND endmeta.meta_key   = '_appointment_end'
			AND daymeta.meta_key   = '_appointment_all_day'
			" . $product_meta_key_q . "
			AND (
				(
					startmeta.meta_value < %s
					AND endmeta.meta_value > %s
					AND daymeta.meta_value = '0'
				)
				OR
				(
					startmeta.meta_value <= %s
					AND endmeta.meta_value >= %s
					AND daymeta.meta_value = '1'
				)
			)
		", date( 'YmdHis', $end_date ), date( 'YmdHis', $start_date ), date( 'Ymd000000', $end_date ), date( 'Ymd000000', $start_date ) ) );

		return apply_filters( 'woocommerce_appointments_in_date_range_query', $appointment_ids );
	}

	/**
	 * Gets appointments for product ids and staff ids
	 * @param  array  $ids
	 * @param  array  $status
	 * @return array of WC_Appointment objects
	 */
	public static function get_appointments_for_objects( $ids = array(), $status = array( 'confirmed', 'paid' ) ) {
		$transient_name = 'schedule_fo_' . md5( http_build_query( array( $ids, $status, WC_Cache_Helper::get_transient_version( 'appointments' ) ) ) );

		if ( false === ( $appointment_ids = get_transient( $transient_name ) ) ) {
			$appointment_ids = self::get_appointments_for_objects_query( $ids, $status );
			set_transient( $transient_name, $appointment_ids, DAY_IN_SECONDS * 30 );
		}

		$appointments = array();

		foreach ( $appointment_ids as $appointment_id ) {
			$appointments[] = get_wc_appointment( $appointment_id );
		}

		return $appointments;
	}

	/**
	 * Gets appointments for product ids and staff ids
	 * @param  array  $ids
	 * @param  array  $status
	 * @return array of WC_Appointment objects
	 */
	public static function get_appointments_for_objects_query( $ids, $status ) {
		global $wpdb;

		$appointment_ids = $wpdb->get_col( "
			SELECT ID FROM {$wpdb->posts}
			LEFT JOIN {$wpdb->postmeta} as _appointment_product_id ON {$wpdb->posts}.ID = _appointment_product_id.post_id
			LEFT JOIN {$wpdb->postmeta} as _appointment_staff_id ON {$wpdb->posts}.ID = _appointment_staff_id.post_id
			WHERE post_type = 'wc_appointment'
			AND post_status IN ('" . implode( "','", $status ) . "')
			AND _appointment_product_id.meta_key = '_appointment_product_id'
			AND _appointment_staff_id.meta_key = '_appointment_staff_id'
			AND (
				_appointment_product_id.meta_value IN ('" . implode( "','", array_map( 'absint', $ids ) ) . "')
				OR _appointment_staff_id.meta_value IN ('" . implode( "','", array_map( 'absint', $ids ) ) . "')
			)
		" );

		return $appointment_ids;
	}

	/**
	 * Gets appointments for a staff
	 *
	 * @param  int $staff_id ID
	 * @param  array  $status
	 * @return array of WC_Appointment objects
	 */
	public static function get_appointments_for_staff( $staff_id, $status = array( 'confirmed', 'paid' ) ) {
		$appointment_ids = get_posts( array(
			'numberposts'   => 500,
			'offset'        => 0,
			'orderby'       => 'post_date',
			'order'         => 'DESC',
			'post_type'     => 'wc_appointment',
			'post_status'   => $status,
			'fields'        => 'ids',
			'no_found_rows' => true,
			'update_post_term_cache' => false,
			'meta_query' => array(
				array(
					'key'     => '_appointment_staff_id',
					'value'   => absint( $staff_id )
				)
			)
		) );

		$appointments    = array();

		foreach ( $appointment_ids as $appointment_id ) {
			$appointments[] = get_wc_appointment( $appointment_id );
		}

		return $appointments;
	}

	/**
	 * Gets appointments for a product by ID
	 *
	 * @param int $product_id The id of the product that we want appointments for
	 * @return array of WC_Appointment objects
	 */
	public static function get_appointments_for_product( $product_id, $status = array( 'confirmed', 'paid' ) ) {
		$appointment_ids = get_posts( array(
			'numberposts'   => 500,
			'offset'        => 0,
			'orderby'       => 'post_date',
			'order'         => 'DESC',
			'post_type'     => 'wc_appointment',
			'post_status'   => $status,
			'fields'        => 'ids',
			'no_found_rows' => true,
			'update_post_term_cache' => false,
			'meta_query' => array(
				array(
					'key'     => '_appointment_product_id',
					'value'   => absint( $product_id )
				)
			)
		) );

		$appointments    = array();

		foreach ( $appointment_ids as $appointment_id ) {
			$appointments[] = get_wc_appointment( $appointment_id );
		}

		return $appointments;
	}

	/**
	 * Get latest appointments
	 *
	 * @param int $numberitems Number of objects returned (default to unlimited)
	 * @param int $offset The number of objects to skip (as a query offset)
	 * @return array of WC_Appointment objects
	 */
	public static function get_latest_appointments( $numberitems = -1, $offset = 0 ) {
		$appointment_ids = get_posts( array(
			'numberposts' => $numberitems,
			'offset'      => $offset,
			'orderby'     => 'post_date',
			'order'       => 'DESC',
			'post_type'   => 'wc_appointment',
			'no_found_rows' => true,
			'update_post_meta_cache' => false,
			'update_post_term_cache' => false,
			'post_status' => get_wc_appointment_statuses(),
			'fields'      => 'ids',
		) );

		$appointments = array();

		foreach ( $appointment_ids as $appointment_id ) {
			$appointments[] = get_wc_appointment( $appointment_id );
		}

		return $appointments;
	}

	/**
	 * Gets appointments for a user by ID
	 *
	 * @param int $user_id The id of the user that we want appointments for
	 * @return array of WC_Appointment objects
	 */
	public static function get_appointments_for_user( $user_id ) {
		$appointment_statuses = get_wc_appointment_statuses( 'user' );

		$appointment_ids = get_posts( array(
			'numberposts'   => 500,
			'offset'        => 0,
			'orderby'       => 'post_date',
			'order'         => 'DESC',
			'post_type'     => 'wc_appointment',
			'post_status'   => $appointment_statuses,
			'fields'        => 'ids',
			'no_found_rows' => true,
			'update_post_term_cache' => false,
			'meta_query' => array(
				array(
					'key'     => '_appointment_customer_id',
					'value'   => absint( $user_id ),
					'compare' => 'IN',
				)
			)
		) );

		$appointments    = array();

		foreach ( $appointment_ids as $appointment_id ) {
			$appointments[] = get_wc_appointment( $appointment_id );
		}

		return $appointments;
	}
}