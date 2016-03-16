<?php

/**
 * Get a appointment object
 * @param  int $id
 * @return object
 */
function get_wc_appointment( $id ) {
	return new WC_Appointment( $id );
}

/**
 * Santiize and format a string into a valid 24 hour time
 * @return string
 */
function wc_appointment_sanitize_time( $raw_time ) {
	$time = wc_clean( $raw_time );
	$time = date( 'H:i', strtotime( $time ) );
	return $time;
}

/**
 * Returns true if the product is a appointment product, false if not
 * @return bool
 */
function is_wc_appointment_product( $product ) {
	if ( empty( $product->product_type ) ) {
		return false;
	}

	$appointment_product_types = apply_filters( 'woocommerce_appointments_product_types', array( 'appointment' ) );
	if ( in_array( $product->product_type, $appointment_product_types ) ) {
		return true;
	}

	return false;
}

/**
 * Convert key to a nice readable label
 * @param  string $key
 * @return string
 */
function get_wc_appointment_data_label( $key, $product ) {
	$labels = apply_filters( 'woocommerce_appointments_data_labels', array(
		'staff'    => ( $product->wc_appointment_staff_label ? $product->wc_appointment_staff_label : __( 'Provider', 'woocommerce-appointments' ) ),
		'date'     => __( 'Date', 'woocommerce-appointments' ),
		'time'     => __( 'Time', 'woocommerce-appointments' ),
		'duration' => __( 'Duration', 'woocommerce-appointments' )
	) );

	if ( ! array_key_exists( $key, $labels ) ) {
		return $key;
	}

	return $labels[ $key ];
}

/**
 * Returns a list of appointment statuses.
 * @param  string $context An optional context (filters) for user or cancel statuses
 * @return array           Statuses
 */
function get_wc_appointment_statuses( $context = 'fully_scheduled' ) {
	if ( 'user' === $context ) {
		return apply_filters( 'woocommerce_appointments_for_user_statuses', array(
			'unpaid',
			'pending-confirmation',
			'confirmed',
			'paid',
			'cancelled',
			'complete',
		) );
	} else if ( 'cancel' === $context ) {
		return apply_filters( 'woocommerce_valid_appointment_statuses_for_cancel', array(
			'unpaid',
			'pending-confirmation',
			'confirmed',
			'paid',
		) );
	} else if ( 'scheduled' === $context ) {
		return apply_filters( 'woocommerce_appointments_scheduled_statuses', array(
			'paid',
		) );
	} else {
		return apply_filters( 'woocommerce_appointments_fully_scheduled_statuses', array(
			'unpaid',
			'pending-confirmation',
			'confirmed',
			'paid',
			'complete',
			'in-cart',
		) );
	}
}

/**
 * Validate and create a new appointment manually.
 *
 * @see WC_Appointment::new_appointment() for available $new_appointment_data args
 * @param  int $product_id you are appointment
 * @param  array $new_appointment_data
 * @param  string $status
 * @param  boolean $exact If false, the function will look for the next available slot after your start date if the date is unavailable.
 * @return mixed WC_Appointment object on success or false on fail
 */
function create_wc_appointment( $product_id, $new_appointment_data = array(), $status = 'confirmed', $exact = false ) {
	// Merge appointment data
	$defaults = array(
		'product_id'  => $product_id, // Appointment ID
		'start_date'  => '',
		'end_date'    => '',
		'staff_id' => '',
	);

	$new_appointment_data = wp_parse_args( $new_appointment_data, $defaults );
	$product          = get_product( $product_id );
	$start_date       = $new_appointment_data['start_date'];
	$end_date         = $new_appointment_data['end_date'];
	$max_date         = $product->get_max_date();
	$qty 			  = 1;

	// If not set, use next available
	if ( ! $start_date ) {
		$min_date   = $product->get_min_date();
		$start_date = strtotime( "+{$min_date['value']} {$min_date['unit']}", current_time( 'timestamp' ) );
	}

	// If not set, use next available + slot duration
	if ( ! $end_date ) {
		$end_date = strtotime( "+{$product->wc_appointment_duration} {$product->wc_appointment_duration_unit}", $start_date );
	}

	$searching = true;
	$date_diff = $end_date - $start_date;

	while( $searching ) {

		$available_appointments = $product->get_available_appointments( $start_date, $end_date, $new_appointment_data['staff_id'], $qty );

		if ( $available_appointments && ! is_wp_error( $available_appointments ) ) {

			if ( ! $new_appointment_data['staff_id'] && is_array( $available_appointments ) ) {
				$new_appointment_data['staff_id'] = current( array_keys( $available_appointments ) );
			}

			$searching = false;

		} else {
			if ( $exact )
				return false;

			$start_date += $date_diff;
			$end_date   += $date_diff;

			if ( $end_date > strtotime( "+{$max_date['value']} {$max_date['unit']}" ) )
				return false;
		}
	}

	// Set dates
	$new_appointment_data['start_date'] = $start_date;
	$new_appointment_data['end_date']   = $end_date;

	// Create it
	$new_appointment = get_wc_appointment( $new_appointment_data );
	$new_appointment->create( $status );

	return $new_appointment;
}

/**
 * Check if product/appointment requires confirmation.
 *
 * @param  int $id Product ID.
 *
 * @return bool
 */
function wc_appointment_requires_confirmation( $id ) {
	$product = get_product( $id );

	if (
		is_object( $product )
		&& is_wc_appointment_product( $product )
		&& $product->requires_confirmation()
	) {
		return true;
	}

	return false;
}

/**
 * Check if the cart has appointment that requires confirmation.
 *
 * @return bool
 */
function wc_appointment_cart_requires_confirmation() {
	$requires = false;

	if ( ! empty ( WC()->cart->cart_contents ) ) {
		foreach ( WC()->cart->cart_contents as $item ) {
			if ( wc_appointment_requires_confirmation( $item['product_id'] ) ) {
				$requires = true;
				break;
			}
		}
	}

	return $requires;
}

/**
 * Check if the order has appointment that requires confirmation.
 *
 * @param  WC_Order $order
 *
 * @return bool
 */
function wc_appointment_order_requires_confirmation( $order ) {
	$requires = false;

	if ( $order ) {
		foreach ( $order->get_items() as $item ) {
			if ( wc_appointment_requires_confirmation( $item['product_id'] ) ) {
				$requires = true;
				break;
			}
		}
	}

	return $requires;
}

/**
 * Get timezone string.
 *
 * inspired by https://wordpress.org/plugins/event-organiser/
 *
 * @return string
 */
function wc_appointment_get_timezone_string() {
	$timezone = wp_cache_get( 'wc_appointments_timezone_string' );

	if ( false === $timezone ) {
		$timezone   = get_option( 'timezone_string' );
		$gmt_offset = get_option( 'gmt_offset' );

		// Remove old Etc mappings. Fallback to gmt_offset.
		if ( ! empty( $timezone ) && false !== strpos( $timezone, 'Etc/GMT' ) ) {
			$timezone = '';
		}

		if ( empty( $timezone ) && 0 != $gmt_offset ) {
			// Use gmt_offset
			$gmt_offset   *= 3600; // convert hour offset to seconds
			$allowed_zones = timezone_abbreviations_list();

			foreach ( $allowed_zones as $abbr ) {
				foreach ( $abbr as $city ) {
					if ( $city['offset'] == $gmt_offset ) {
						$timezone = $city['timezone_id'];
						break 2;
					}
				}
			}
		}

		// Issue with the timezone selected, set to 'UTC'
		if ( empty( $timezone ) ) {
			$timezone = 'UTC';
		}

		// Cache the timezone string.
		wp_cache_set( 'wc_appointments_timezone_string', $timezone );
	}

	return $timezone;
}

/**
 * Get appointable product staff.
 *
 * @param int $product_id product ID.
 *
 * @return array Staff objects list.
 */
function wc_appointment_get_product_staff( $product_id ) {
	global $wpdb;

	$staff = array();
	$users     = $wpdb->get_results(
		$wpdb->prepare( "
			SELECT users.ID, users.display_name
			FROM {$wpdb->prefix}wc_appointment_relationships AS relationships
				LEFT JOIN $wpdb->users AS users
				ON users.ID = relationships.staff_id
			WHERE relationships.product_id = %d
			ORDER BY sort_order ASC
		", $product_id )
	);

	foreach ( $users as $staff_member ) {
		$staff[] = new WC_Product_Appointment_Staff( $staff_member, $product_id );
	}

	return $staff;
}

/**
 * Convert time in minutes to hours and minutes
 *
 * @return string
 */
function wc_appointment_convert_to_hours_and_minutes( $time ) {
	$return = sprintf( _n( '%s minute', '%s minutes', $time, 'woocommerce-appointments' ), $time );
	
	//* Duration longer than 120 minutes
	if ( $time > apply_filters( 'woocommerce_appointments_duration_break', 120 ) ) {
	
		$hours = floor( $time / 60 );
		$return = sprintf( _n( '%s hour', '%s hours', $hours, 'woocommerce-appointments' ), $hours );
		$minutes = ( $time % 60 );
		if ( $minutes > 0 ) {
			$return .= '&nbsp;'; #empty space 
			$return .= sprintf( _n( '%s minute', '%s minutes', $minutes, 'woocommerce-appointments' ), $minutes );
		}
	
	}
	
	return $return;
}

/**
 * Add body classes for WC Appointments pages
 *
 * @param  array $classes
 * @return array
 */
// add_filter( 'body_class', 'wc_appointment_body_class' );
function wc_appointment_body_class( $classes ) {
	$classes = (array) $classes;

	if ( is_order_received_page() ) {	
		global $wp;
		
		$order_id = absint( $wp->query_vars['order-received'] );
		$order = wc_get_order( $order_id );
		
		if ( isset( $order ) && $order->get_total() === 0 ) {
			$classes[] = 'woocommerce-zero-order';
		}
	}
	
	elseif ( is_checkout_pay_page() ) {	
		global $wp;
		
		$order_id = absint( $wp->query_vars['order-pay'] );
		$order = wc_get_order( $order_id );
		
		if ( isset( $order ) && $order->get_total() === 0 ) {
			$classes[] = 'woocommerce-zero-order';
		}
	}
	
	elseif ( is_checkout() || is_cart() ) {
		global $woocommerce;
		
		if ( absint( $woocommerce->cart->total ) === 0 ) {
			$classes[] = 'woocommerce-zero-order';
		}
	}

	return array_unique( $classes );
}
