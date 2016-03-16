<?php
/**
 * Appointment form class
 */
class WC_Appointment_Form {

	/**
	 * Appointment product data.
	 * @var WC_Product_Appointment
	 */
	public $product;

	/**
	 * Appointment fields.
	 * @var array
	 */
	private $fields;

	/**
	 * Constructor
	 * @param $product WC_Product_Appointment
	 */
	public function __construct( $product ) {
		$this->product = $product;
	}

	/**
	 * Appointment form scripts
	 */
	public function scripts() {
		global $wp_locale;

		$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

		wp_register_script( 'wc-appointments-appointment-form', WC_APPOINTMENTS_PLUGIN_URL . '/assets/js/appointment-form' . $suffix . '.js', array( 'jquery', 'jquery-blockui' ), WC_APPOINTMENTS_VERSION, true );
		wp_register_script( 'wc-appointments-date-picker', WC_APPOINTMENTS_PLUGIN_URL . '/assets/js/date-picker' . $suffix . '.js', array( 'wc-appointments-appointment-form', 'jquery-ui-datepicker' ), WC_APPOINTMENTS_VERSION, true );
		wp_register_script( 'wc-appointments-time-picker', WC_APPOINTMENTS_PLUGIN_URL . '/assets/js/time-picker' . $suffix . '.js', array( 'wc-appointments-appointment-form' ), WC_APPOINTMENTS_VERSION, true );
		wp_register_script( 'wc-appointments-staff-picker', WC_APPOINTMENTS_PLUGIN_URL . '/assets/js/staff-picker' . $suffix . '.js', array( 'wc-appointments-appointment-form' ), WC_APPOINTMENTS_VERSION, true );
		wp_register_script( 'wc-appointments-select2', WC_APPOINTMENTS_PLUGIN_URL . '/assets/js/select2' . $suffix . '.js', array( 'wc-appointments-appointment-form' ), WC_APPOINTMENTS_VERSION, true );
		
		// Variables for JS scripts
		$appointment_form_params = array(
			'closeText'						=> __( 'Close', 'woocommerce-appointments' ),
			'currentText'					=> __( 'Today', 'woocommerce-appointments' ),
			'prevText'						=> __( 'Previous', 'woocommerce-appointments' ),
			'nextText'						=> __( 'Next', 'woocommerce-appointments' ),
			'monthNames'					=> array_values( $wp_locale->month ),
			'monthNamesShort'				=> array_values( $wp_locale->month_abbrev ),
			'dayNames'						=> array_values( $wp_locale->weekday ),
			'dayNamesShort'					=> array_values( $wp_locale->weekday_abbrev ),
			'dayNamesMin'					=> array_values( $wp_locale->weekday_initial ),
			'firstDay'						=> get_option( 'start_of_week' ),
			'current_time'					=> date( 'Ymd', current_time( 'timestamp' ) ),
			'availability_span' 			=> $this->product->wc_appointment_availability_span,
			'duration_unit'					=> $this->product->wc_appointment_duration_unit,
			'nonce_staff_html'				=> wp_create_nonce( 'appointable-staff-html' ),
			'ajax_url'						=> WC()->ajax_url(),
			'i18n_date_unavailable'			=> __( 'This date is unavailable', 'woocommerce-appointments' ),
			'i18n_date_fully_scheduled'		=> __( 'This date is fully scheduled and unavailable', 'woocommerce-appointments' ),
			'i18n_date_partially_scheduled'	=> __( 'This date is partially scheduled - but appointments still remain', 'woocommerce-appointments' ),
			'i18n_date_available'			=> __( 'This date is available', 'woocommerce-appointments' ),
			'i18n_start_date'				=> __( 'Choose a Start Date', 'woocommerce-appointments' ),
			'i18n_end_date'					=> __( 'Choose an End Date', 'woocommerce-appointments' ),
			'i18n_dates'					=> __( 'Dates', 'woocommerce-appointments' ),
			'i18n_choose_options'			=> __( 'Please select the options for your appointment above first', 'woocommerce-appointments' )
		);
		
		if ( in_array( $this->product->wc_appointment_duration_unit, array( 'minute', 'hour' ) ) ) {
			$appointment_form_params['appointment_duration'] = 1;
		} else {
			$appointment_form_params['appointment_duration'] = $this->product->wc_appointment_duration;
		}

		wp_localize_script( 'wc-appointments-appointment-form', 'wc_appointment_form_params', apply_filters( 'wc_appointment_form_params', $appointment_form_params ) );
		
		wp_enqueue_script( 'wc-appointments-appointment-form' );
		
	}

	/**
	 * Prepare fields for the appointment form
	 */
	public function prepare_fields() {
		// Destroy existing fields
		$this->reset_fields();

		// Add fields in order
		$this->staff_field();
		$this->date_field();

		$this->fields = apply_filters( 'appointment_form_fields', $this->fields );
	}

	/**
	 * Reset fields array
	 */
	public function reset_fields() {
		$this->fields = array();
	}

	/**
	 * Add staff field
	 */
	private function staff_field() {
		
		// Staff field
		if ( $this->product->has_staff() && 'customer' == $this->product->wc_appointment_staff_assignment ) {

			$staff          = $this->product->get_staff();
			$staff_options  = array();
			$data           = array();

			foreach ( $staff as $staff ) {
				$additional_cost = '';
				$cost_plus_base  = ( $staff->get_base_cost() + $this->product->price );
				$additional_cost = array();

				if ( $staff->get_base_cost() && $this->product->get_base_cost() < $cost_plus_base ) {
					$additional_cost[] = ' + ' . wc_price( $cost_plus_base - $this->product->get_base_cost() );
				}

				if ( $additional_cost ) {
					$additional_cost_string = implode( ', ', $additional_cost );
				} else {
					$additional_cost_string = '';
				}
				
				$staff_options[ $staff->ID ] = $staff->display_name . apply_filters( 'woocommerce_appointments_staff_additional_cost_string', $additional_cost_string, $staff );
			}

			$this->add_field( array(
				'type'    => 'select_staff',
				'name'    => 'staff',
				'label'   => $this->product->wc_appointment_staff_label ? $this->product->wc_appointment_staff_label : __( 'Provider', 'woocommerce-appointments' ),
				'class'   => array( 'wc_appointment_field_' . sanitize_title( $this->product->wc_appointment_staff_label ) ),
				'options' => $staff_options
			) );
		}
	}

	/**
	 * Add the date field to the appointment form
	 */
	private function date_field() {
		$picker = null;
		
		// Get date picker specific to the duration unit for this product
		switch ( $this->product->get_duration_unit() ) {
			case 'day' :
			case 'night' :
				include_once( 'class-wc-appointment-form-date-picker.php' );
				$picker = new WC_Appointment_Form_Date_Picker( $this );
				break;
			case 'minute' :
			case 'hour' :
				include_once( 'class-wc-appointment-form-datetime-picker.php' );
				$picker = new WC_Appointment_Form_Datetime_Picker( $this );
			default :
				break;
		}

		if ( ! is_null( $picker ) ) {
			$this->add_field( $picker->get_args() );
		}
	}

	/**
	 * Add Field
	 * @param  array $field
	 * @return void
	 */
	public function add_field( $field ) {
		$default = array(
			'name'  => '',
			'class' => array(),
			'label' => '',
			'type'  => 'text'
		);

		$field = wp_parse_args( $field, $default );

		if ( ! $field['name'] || ! $field['type'] ) {
			return;
		}

		$nicename = 'wc_appointments_field_' . sanitize_title( $field['name'] );

		$field['name']    = $nicename;
		$field['class'][] = $nicename;

		$this->fields[ sanitize_title( $field['name'] ) ] = $field;
	}

	/**
	 * Output the form - called from the add to cart templates
	 */
	public function output() {
		$this->scripts();
		$this->prepare_fields();

		foreach ( $this->fields as $key => $field ) {
			wc_get_template( 'appointment-form/' . $field['type'] . '.php', array( 'field' => $field ), 'woocommerce-appointments', WC_APPOINTMENTS_TEMPLATE_PATH );
		}
	}

	/**
	 * Get posted form data into a neat array
	 * @param  array $posted
	 * @return array
	 */
	public function get_posted_data( $posted = array() ) {
		if ( empty( $posted ) ) {
			$posted = $_POST;
		}

		$data = array(
			'_year'    => '',
			'_month'   => '',
			'_day'     => ''
		);

		// Get date fields (y, m, d)
		if ( ! empty( $posted['wc_appointments_field_start_date_year'] ) && ! empty( $posted['wc_appointments_field_start_date_month'] ) && ! empty( $posted['wc_appointments_field_start_date_day'] ) ) {
			$data['_year']  = absint( $posted['wc_appointments_field_start_date_year'] );
			$data['_year']  = $data['_year'] ? $data['_year'] : date('Y');
			$data['_month'] = absint( $posted['wc_appointments_field_start_date_month'] );
			$data['_day']   = absint( $posted['wc_appointments_field_start_date_day'] );
			$data['_date']  = $data['_year'] . '-' . $data['_month'] . '-' . $data['_day'];
			$data['date']   = date_i18n( wc_date_format(), strtotime( $data['_date'] ) );
		}

		// Get year month field
		if ( ! empty( $posted['wc_appointments_field_start_date_yearmonth'] ) ) {
			$yearmonth      = strtotime( $posted['wc_appointments_field_start_date_yearmonth'] . '-01' );
			$data['_year']  = absint( date( 'Y', $yearmonth ) );
			$data['_month'] = absint( date( 'm', $yearmonth ) );
			$data['_day']   = 1;
			$data['_date']  = $data['_year'] . '-' . $data['_month'] . '-' . $data['_day'];
			$data['date']   = date_i18n( 'F Y', $yearmonth );
		}

		// Get time field
		if ( ! empty( $posted['wc_appointments_field_start_date_time'] ) ) {
			$data['_time'] = wc_clean( $posted['wc_appointments_field_start_date_time'] );

			$data['time']  = date_i18n( get_option( 'time_format' ), strtotime( "{$data['_year']}-{$data['_month']}-{$data['_day']} {$data['_time']}" ) );
		} else {
			$data['_time'] = '';
		}

		// Quantity being scheduled
		$data['_qty'] = 1;

		if ( isset( $posted[ 'quantity' ] ) ) {
			$data['_qty'] = absint( $posted[ 'quantity' ] );
		}
		
		// Fixed duration
		$duration_unit				= in_array( $this->product->get_duration_unit(), array( 'minute', 'hour' ) ) ? 'minute' : $this->product->get_duration_unit();
		$duration_in_mins 			= 'hour' === $this->product->get_duration_unit() ? $this->product->get_duration() * 60 : $this->product->get_duration();
		$duration_in_total 			= 'day' === $this->product->get_duration_unit() ? $this->product->get_duration() : $duration_in_mins;
		$duration_total				= apply_filters( 'appointment_form_posted_total_duration', $duration_in_total, $this, $posted );
		$duration_total_hours 		= floor( $duration_total / 60 );
		$duration_total_minutes 	= ( $duration_total % 60 );
		
		//* Display hours and minutes in a readable form
		if ( 'day' === $this->product->get_duration_unit() ) {
			$total_duration_n			= sprintf( _n( '%s day', '%s days', $this->product->get_duration(), 'woocommerce-appointments' ), $this->product->get_duration() );
		} else if ( '60' < $duration_total && '0' == $duration_total_minutes ) {
			$total_duration_n			= sprintf( _n( '%s hour', '%s hours', $duration_total_hours, 'woocommerce-appointments' ), $duration_total_hours );
		} elseif ( '90' < $duration_total && '0' != $duration_total_minutes ) {
			$total_duration_n			= sprintf( _n( '%s hour', '%s hours', $duration_total_hours, 'woocommerce-appointments' ), $duration_total_hours );
			$total_duration_n			.= ' ';
			$total_duration_n			.= sprintf( _n( '%s minute', '%s minutes', $duration_total_minutes, 'woocommerce-appointments' ), $duration_total_minutes );
		} else {
			$total_duration_n			= sprintf( _n( '%s minute', '%s minutes', $duration_total, 'woocommerce-appointments' ), $duration_total );
		}
		
		// Work out start and end dates/times
		if ( ! empty( $data['_time'] ) ) {
			$data['_start_date'] 	= strtotime( "{$data['_year']}-{$data['_month']}-{$data['_day']} {$data['_time']}" );
			$data['_end_date']   	= strtotime( "+{$duration_total} {$duration_unit}", $data['_start_date'] );
			$data['_all_day']    	= 0;
			$data['_duration'] 		= $duration_total;
			$data['duration'] 		= $total_duration_n;
		} else if ( 'night' === $this->product->get_duration_unit() ) {
			$data['_start_date'] 	= strtotime( "{$data['_year']}-{$data['_month']}-{$data['_day']}" );
			$data['_end_date']   	= strtotime( "+{$duration_total} day", $data['_start_date'] );
			$data['_all_day']    	= 0;
		} else {
			$data['_start_date'] 	= strtotime( "{$data['_year']}-{$data['_month']}-{$data['_day']}" );
			$data['_end_date']   	= strtotime( "+{$duration_total} {$duration_unit} - 1 second", $data['_start_date'] );
			$data['_all_day']    	= 1;
			$data['_duration'] 		= $duration_total;
			$data['duration'] 		= $total_duration_n;
		}

		//* Get posted staff or assign one for the date range
		if ( $this->product->has_staff() ) {
			if ( $this->product->is_staff_assignment_type( 'customer' ) && ! empty( $posted['wc_appointments_field_staff'] ) ) {
				if ( $staff = $this->product->get_staff_member( absint( $posted['wc_appointments_field_staff'] ) ) ) {
					$data['_staff_id'] = $staff->ID;
					$data['staff']     = $staff->display_name;
				} else {
					$data['_staff_id'] = 0;
				}
			} else {
				// Assign an available staff automatically
				$available_appointments = $this->product->get_available_appointments( $data['_start_date'], $data['_end_date'], 0, $data['_qty'] );

				if ( is_array( $available_appointments ) ) {
					$shuffleKeys = array_keys( $available_appointments );
					shuffle( $shuffleKeys ); # randomize
					$staff = get_user_by( 'id', current( $shuffleKeys ) );
					$data['_staff_id'] = current( $shuffleKeys );
					$data['staff']     = $staff->display_name;
				}
			}
		}

		return apply_filters( 'woocommerce_appointments_get_posted_data', $data );
	}

	/**
	 * Checks appointment data is correctly set, and that the chosen slots are indeed available.
	 *
	 * @param  array $data
	 * @return WP_Error on failure, true on success
	 */
	public function is_appointable( $data ) {
		// Validate staff are set
		if ( $this->product->has_staff() && $this->product->is_staff_assignment_type( 'customer' ) ) {
			if ( empty( $data['_staff_id'] ) ) {
				// return new WP_Error( 'Error', sprintf( __( 'Please choose the %s.', 'woocommerce-appointments' ), $this->product->wc_appointment_staff_label ? $this->product->wc_appointment_staff_label : __( 'Provider', 'woocommerce-appointments' ) ) );
				$data['_staff_id'] = 0;
			}
		} elseif ( $this->product->has_staff() && $this->product->is_staff_assignment_type( 'automatic' ) ) {
			$data['_staff_id'] = 0;
		} else {
			$data['_staff_id'] = '';
		}

		// Validate date and time
		if ( empty( $data['date'] ) ) {
			return new WP_Error( 'Error', __( 'Date is required - please choose one above', 'woocommerce-appointments' ) );
		}
		if ( in_array( $this->product->get_duration_unit(), array( 'minute', 'hour' ) ) && empty( $data['time'] ) ) {
			return new WP_Error( 'Error', __( 'Time is required - please choose one above', 'woocommerce-appointments' ) );
		}
		if ( $data['_date'] && date( 'Ymd', strtotime( $data['_date'] ) ) < date( 'Ymd', current_time( 'timestamp' ) ) ) {
			return new WP_Error( 'Error', __( 'You must choose a future date and time.', 'woocommerce-appointments' ) );
		}
		if ( $data['_date'] && ! empty( $data['_time'] ) && date( 'YmdHi', strtotime( $data['_date'] . ' ' . $data['_time'] ) ) < date( 'YmdHi', current_time( 'timestamp' ) ) ) {
			return new WP_Error( 'Error', __( 'You must choose a future date and time.', 'woocommerce-appointments' ) );
		}

		// Validate min date and max date
		if ( in_array( $this->product->get_duration_unit(), array( 'minute', 'hour' ) ) ) {
			$now = current_time( 'timestamp' );
		} elseif ( 'month' === $this->product->get_duration_unit() ) {
			$now = strtotime( 'midnight first day of this month', current_time( 'timestamp' ) );
		} else {
			$now = strtotime( 'midnight', current_time( 'timestamp' ) );
		}
		if ( $min = $this->product->get_min_date() ) {
			$min_date = $this->product->get_min_timestamp_for_date( strtotime( $data['date'] ) );

			if ( strtotime( $data['_date'] . ' ' . $data['_time'] ) < $min_date ) {
				return new WP_Error( 'Error', sprintf( __( 'The earliest appointment possible is currently %s.', 'woocommerce-appointments' ), date_i18n( wc_date_format() . ' ' . get_option( 'time_format' ), $min_date ) ) );
			}
		}
		if ( $max = $this->product->get_max_date() ) {
			$max_date = strtotime( "+{$max['value']} {$max['unit']}", $now );
			if ( strtotime( $data['_date'] . ' ' . $data['_time'] ) > $max_date ) {
				return new WP_Error( 'Error', sprintf( __( 'The latest appointment possible is currently %s.', 'woocommerce-appointments' ), date_i18n( wc_date_format() . ' ' . get_option( 'time_format' ), $max_date ) ) );
			}
		}

		// Get availability for the dates
		$available_appointments = $this->product->get_available_appointments( $data['_start_date'], $data['_end_date'], $data['_staff_id'], $data['_qty'] );

		if ( is_array( $available_appointments ) ) {
			$this->auto_assigned_staff_id = current( array_keys( $available_appointments ) );
		}

		if ( is_wp_error( $available_appointments ) ) {
			return $available_appointments;
		} elseif ( ! $available_appointments ) {
			return new WP_Error( 'Error', __( 'Sorry, the selected slot is not available.', 'woocommerce-appointments' ) );
		}

		return true;
	}

	/**
	 * Get an array of formatted time values
	 * @param  string $timestamp
	 * @return array
	 */
	public function get_formatted_times( $timestamp ) {
		return array(
			'timestamp'   => $timestamp,
			'year'        => date( 'Y', $timestamp ),
			'month'       => date( 'n', $timestamp ),
			'day'         => date( 'j', $timestamp ),
			'week'        => date( 'W', $timestamp ),
			'day_of_week' => date( 'N', $timestamp ),
			'time'        => date( 'YmdHi', $timestamp ),
		);
	}

	/**
	 * Calculate costs from posted values
	 * @param  array $posted
	 * @return string cost
	 */
	public function calculate_appointment_cost( $posted ) {
		if ( ! empty( $this->appointment_cost ) ) {
			return $this->appointment_cost;
		}
		
		// Get pricing rules
		$costs              = $this->product->get_costs();

		// Get posted data
		$data               = $this->get_posted_data( $posted );
		$validate           = $this->is_appointable( $data );

		if ( is_wp_error( $validate ) ) {
			return $validate;
		}
		
		/*
		// Sale date range
		$sale_date_from = get_post_meta( $this->product->id, '_sale_price_dates_from', true );
		$sale_date_to = get_post_meta( $this->product->id, '_sale_price_dates_to', true );
		
		// Sale price for selected date: if you wanted sale price to apply on appointment date
		if ( $sale_date_from <= $data['_start_date'] && $sale_date_to >= $data['_start_date'] ) {
			$base_cost      = max( 0, $this->product->get_sale_price() );
		} else {
			$base_cost      = max( 0, $this->product->get_regular_price() );
		}
		*/
		
		// Base price
		$base_cost					= max( 0, $this->product->price );
		$base_slot_cost				= 0;
		$total_slot_cost			= 0;
		
		// See if we have an auto_assigned_staff_id
		if ( isset( $this->auto_assigned_staff_id ) ) {
			$data['_staff_id'] = $this->auto_assigned_staff_id;
		}

		// Get staff cost
		if ( isset( $data['_staff_id'] ) ) {
			$staff        = $this->product->get_staff_member( $data['_staff_id'] );
			$base_cost   += $staff->get_base_cost();
		}
		
		// Slot data
		$this->applied_pricing_rules	= array();
		$slot_duration					= $this->product->get_duration();
		$slot_unit						= $this->product->get_duration_unit();
		// As we have converted the hourly duration earlier to minutes, convert back
		if ( isset( $data['_duration'] ) ) {
			$slots_scheduled			= 'hour' === $this->product->get_duration_unit() ? ceil( absint( $data['_duration'] ) / 60 ) : absint( $data['_duration'] );
		} else {
			$slots_scheduled			= $slot_duration;
		}
		$slots_scheduled 				= ceil( $slots_scheduled / $slot_duration );
		$slot_timestamp					= $data['_start_date'];
		
		$override_slots = array();
		
		// Evaluate pricing rules for each scheduled slot
		for ( $slot = 0; $slot < $slots_scheduled; $slot ++ ) {
			$slot_cost              = $base_slot_cost;
			$slot_start_time_offset = $slot * $slot_duration;
			$slot_end_time_offset   = ( $slot + 1 ) * $slot_duration;
			$slot_start_time        = $this->get_formatted_times( strtotime( "+{$slot_start_time_offset} {$slot_unit}", $slot_timestamp ) );
			$slot_end_time          = $this->get_formatted_times( strtotime( "+{$slot_end_time_offset} {$slot_unit}", $slot_timestamp ) );

			if ( in_array( $this->product->get_duration_unit(), array( 'night' ) ) ) {
				$slot_start_time = $this->get_formatted_times( strtotime( "+{$slot_start_time_offset} day", $slot_timestamp ) );
				$slot_end_time = $this->get_formatted_times( strtotime( "+{$slot_end_time_offset} day", $slot_timestamp ) );
			}

			foreach ( $costs as $rule_key => $rule ) {
				$type  = $rule[0];
				$rules = $rule[1];

				if ( strrpos( $type, 'time' ) === 0 ) {
					if ( ! in_array( $this->product->get_duration_unit(), array( 'minute', 'hour' ) ) ) {
						continue;
					}

					if ( 'time:range' === $type ) {
						$year = date( 'Y', $slot_start_time['timestamp'] );
						$month = date( 'n', $slot_start_time['timestamp'] );
						$day = date( 'j', $slot_start_time['timestamp'] );

						if ( ! isset( $rules[ $year ][ $month ][ $day ] ) ) {
							continue;
						}

						$rule_val = $rules[ $year ][ $month ][ $day ]['rule'];
						$from     = $rules[ $year ][ $month ][ $day ]['from'];
						$to       = $rules[ $year ][ $month ][ $day ]['to'];
					} else {
						if ( ! empty( $rules['day'] ) ) {
							if ( $rules['day'] != $slot_start_time['day_of_week'] ) {
								continue;
							}
						}

						$rule_val = $rules['rule'];
						$from     = $rules['from'];
						$to       = $rules['to'];
					}

					$rule_start_time_hi = date( "YmdHi", strtotime( str_replace( ':', '', $from ), $slot_start_time['timestamp'] ) );
					$rule_end_time_hi   = date( "YmdHi", strtotime( str_replace( ':', '', $to ), $slot_start_time['timestamp'] ) );
					$matched            = false;

					// Reverse time rule - The end time is tomorrow e.g. 16:00 today - 12:00 tomorrow
					if ( $rule_end_time_hi <= $rule_start_time_hi ) {

						if ( $slot_end_time['time'] > $rule_start_time_hi ) {
							$matched = true;
						}
						if ( $slot_start_time['time'] >= $rule_start_time_hi && $slot_end_time['time'] >= $rule_end_time_hi ) {
							$matched = true;
						}
						if ( $slot_start_time['time'] <= $rule_start_time_hi && $slot_end_time['time'] <= $rule_end_time_hi ) {
							$matched = true;
						}

					// Normal rule
					} else {
						if ( $slot_start_time['time'] >= $rule_start_time_hi && $slot_end_time['time'] <= $rule_end_time_hi ) {
							$matched = true;
						}
					}

					if ( $matched ) {
						$slot_cost = $this->apply_cost( $slot_cost, $rule_val['slot'][0], $rule_val['slot'][1] );
						$base_cost  = $this->apply_base_cost( $base_cost, $rule_val['base'][0], $rule_val['base'][1], $rule_key );
					}
				} else {
					switch ( $type ) {
						case 'months' :
						case 'weeks' :
						case 'days' :
							$check_date = $slot_start_time['timestamp'];

							while ( $check_date < $slot_end_time['timestamp'] ) {
								$checking_date = $this->get_formatted_times( $check_date );
								$date_key      = $type == 'days' ? 'day_of_week' : substr( $type, 0, -1 );

								if ( isset( $rules[ $checking_date[ $date_key ] ] ) ) {
									$rule       = $rules[ $checking_date[ $date_key ] ];
									$slot_cost = $this->apply_cost( $slot_cost, $rule['slot'][0], $rule['slot'][1] );
									$base_cost  = $this->apply_base_cost( $base_cost, $rule['base'][0], $rule['base'][1], $rule_key );
									if ( $rule['override'] && empty( $override_slots[ $check_date ] ) ) {
										$override_slots[ $check_date ] = $rule['override'];
									}
								}
								$check_date = strtotime( "+1 {$type}", $check_date );
							}
						break;
						case 'custom' :
							$check_date = $slot_start_time['timestamp'];

							while ( $check_date < $slot_end_time['timestamp'] ) {
								$checking_date = $this->get_formatted_times( $check_date );
								if ( isset( $rules[ $checking_date['year'] ][ $checking_date['month'] ][ $checking_date['day'] ] ) ) {
									$rule       = $rules[ $checking_date['year'] ][ $checking_date['month'] ][ $checking_date['day'] ];
									$slot_cost = $this->apply_cost( $slot_cost, $rule['slot'][0], $rule['slot'][1] );
									$base_cost  = $this->apply_base_cost( $base_cost, $rule['base'][0], $rule['base'][1], $rule_key );
									if ( $rule['override'] && empty( $override_slots[ $check_date ] ) ) {
										$override_slots[ $check_date ] = $rule['override'];
									}
								}
								$check_date = strtotime( "+1 day", $check_date );
							}
						break;
						case 'slots' :
							if ( ! empty( $data['_duration'] ) ) {
								if ( $rules['from'] <= $data['_duration'] && $rules['to'] >= $data['_duration'] ) {
									$slot_cost = $this->apply_cost( $slot_cost, $rules['rule']['slot'][0], $rules['rule']['slot'][1] );
									$base_cost  = $this->apply_base_cost( $base_cost, $rules['rule']['base'][0], $rules['rule']['base'][1], $rule_key );
								}
							}
						break;
					}
				}
			}
			$total_slot_cost += $slot_cost;
		}

		foreach ( $override_slots as $over_cost ) {
			$total_slot_cost = $total_slot_cost - $base_slot_cost;
			$total_slot_cost += $over_cost;
		}
		
		//* Calculate costs
		$this->appointment_cost = max( 0, $total_slot_cost + $base_cost );
		
		//* Multiply costs, when multiple qty scheduled
		if ( $data['_qty'] > 1 ) {
			$this->appointment_cost = $this->appointment_cost * absint( $data['_qty'] );
		}

		return apply_filters( 'appointment_form_calculated_appointment_cost', $this->appointment_cost, $this, $posted );
	}
	
	/**
	 * Apply a cost
	 * @param  float $base
	 * @param  string $multiplier
	 * @param  float $cost
	 * @return float
	 */
	private function apply_cost( $base, $multiplier, $cost) {
		switch ( $multiplier ) {
			case 'times' :
				$new_cost = $base * $cost;
				break;
			case 'divide' :
				$new_cost = $base / $cost;
				break;
			case 'minus' :
				$new_cost = $base - $cost;
				break;
			case 'equals':
				$new_cost = $cost;
				break;
			default :
				$new_cost = $base + $cost;
				break;
		}
		return $new_cost;
	}

	/**
	 * Apply a cost
	 * @param  float $base
	 * @param  string $multiplier
	 * @param  float $cost
	 * @param  float $apply_to Cost to apply the rule to - used for * and /
	 * @return float
	 */
	private function apply_base_cost( $base, $multiplier, $cost, $rule_key = '' ) {
		if ( in_array( $rule_key, $this->applied_pricing_rules ) ) {
			return $base;
		}
		switch ( $multiplier ) {
			case 'times' :
				$new_cost = $base * $cost;
				break;
			case 'divide' :
				$new_cost = $base / $cost;
				break;
			case 'minus' :
				$new_cost = $base - $cost;
				break;
			case 'equals' :
				$new_cost = $cost;
				break;
			default :
				$new_cost = $base + $cost;
				break;
		}
		$this->applied_pricing_rules[] = $rule_key;
		return $new_cost;
	}

}
