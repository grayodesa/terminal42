<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class for the appointment product type
 */
class WC_Product_Appointment extends WC_Product {
	private $availability_rules = array();

	/**
	 * Constructor
	 */
	public function __construct( $product ) {
		if ( empty ( $this->product_type ) ) {
			$this->product_type = 'appointment';
		}
		parent::__construct( $product );
	}
	
	/**
	 * If this product class is a skelton/place holder class (used for appointment addons)
	 * @return boolean
	 */
	public function is_skeleton() {
		return false;
	}

	/**
	 * If this product class is an addon for appointments
	 * @return boolean
	 */
	public function is_appointments_addon() {
		return false;
	}

	/**
	 * Extension/plugin/add-on name for the appointment addon this product refers to
	 * @return string
	 */
	public function appointments_addon_title() {
		return '';
	}

	/**
	 * We want to sell appointments one at a time
	 * @return boolean
	 */
	/*
	public function is_sold_individually() {
		return true;
	}
	*/

	/**
	 * Appointments can always be purchased regardless of price.
	 * @return boolean
	 */
	public function is_purchasable() {
		$purchasable = true;

		// Products must exist of course
		if ( ! $this->exists() ) {
			$purchasable = false;

		// Check that the product is published
		} elseif ( $this->post->post_status !== 'publish' && ! current_user_can( 'edit_post', $this->id ) ) {
			$purchasable = false;
		}

		return apply_filters( 'woocommerce_is_purchasable', $purchasable, $this );
	}

	/**
	 * Get the qty available to schedule per slot.
	 * @return boolean
	 */
	public function get_qty() {
		return $this->wc_appointment_qty ? absint( $this->wc_appointment_qty ) : 1;
	}

	/**
	 * See if this appointment product has reasources enabled.
	 * @return boolean
	 */
	public function has_staff() {
		$count_staff = count( $this->get_staff() );
		return $count_staff ? $count_staff : false;
	}

	/**
	 * get duration
	 * @return string
	 */
	public function get_duration() {
		return $this->wc_appointment_duration;
	}

	/**
	 * get duration unit
	 * @return string
	 */
	public function get_duration_unit() {
		return apply_filters( 'woocommerce_appointments_get_duration_unit', $this->wc_appointment_duration_unit, $this );
	}
	
	/**
	 * get interval
	 * @return string
	 */
	public function get_interval() {
		return $this->wc_appointment_interval;
	}

	/**
	 * get interval unit
	 * @return string
	 */
	public function get_interval_unit() {
		return apply_filters( 'woocommerce_appointments_get_interval_unit', $this->wc_appointment_interval_unit, $this );
	}
	
	/**
	 * get padding duration
	 * @return string
	 */
	public function get_padding_duration() {
		return $this->wc_appointment_padding_duration;
	}

	/**
	 * get padding duration unit
	 * @return string
	 */
	public function get_padding_duration_unit() {
		return $this->wc_appointment_padding_duration_unit;
	}
	
	/**
	 * get padding duration when
	 * @return string
	 */
	public function get_padding_duration_when() {
		return $this->wc_appointment_padding_duration_when;
	}
	
	/**
	 * The base cost will either be the 'base' cost or the base cost + cheapest staff
	 * @return string
	 */
	public function get_base_cost() {
		
		//$base = $this->price;
		
		$base = $this->price;

		if ( $this->has_staff() ) {
			$staff = $this->get_staff();
			$cheapest  = null;

			foreach ( $staff as $staff_member ) {
				if ( is_null( $cheapest ) || $staff_member->get_base_cost() < $cheapest ) {
					$cheapest = $staff_member->get_base_cost();
				}
			}
			$base += $cheapest;
		}

		return $base;
	}

	/**
	 * Return if appointment has extra costs
	 * @return bool
	 */
	public function has_additional_costs() {
		$has_additional_costs = 'yes' === $this->has_additional_costs;

		return $has_additional_costs;
	}
	
	/**
	 * Return if appointment has label
	 * @return bool
	 */
	public function has_price_label() {
		$has_price_label = false;

		// Products must exist of course
		if ( get_post_meta( $this->id, '_wc_appointment_has_price_label', true ) ) {
			$price_label = get_post_meta( $this->id, '_wc_appointment_price_label', true );
			$has_price_label = $price_label ? $price_label : __( 'Price Varies', 'woocommerce-appointments' );
		}
		
		return $has_price_label;
	}

	/**
	 * Get product price
	 * @return string
	 */
	/*
	public function get_price() {
		return apply_filters( 'woocommerce_get_price', $this->get_base_cost(), $this );
	}
	*/

	/**
	 * Get price HTML
	 * @return string
	 */
	public function get_price_html( $price = '' ) {
		// $display_price          = $this->get_display_price();
		// $display_regular_price  = $this->get_display_price( $this->get_regular_price() );
		$display_price          = $this->get_display_price( $this->get_base_cost() );
		$display_regular_price  = $this->get_display_price( $this->regular_price );

		//* Price label
		if ( $this->has_price_label() ) {
			$price_html = $this->has_price_label();
		} elseif ( $display_price ) {
			if ( $this->has_additional_costs() ) {
				if ( $this->is_on_sale() && $this->get_regular_price() ) {
					$price_html = $this->get_price_html_from_to( $display_regular_price, $this->get_display_price( $this->get_base_cost() ) ) . $this->get_price_suffix();
				} else {
					$price_html = sprintf( __( '<small class="from">From </small>%s', 'woocommerce-appointments' ), wc_price( $display_price ) ) . $this->get_price_suffix();
				}
			} else {
				if ( $this->is_on_sale() && $this->get_regular_price() ) {
					$price_html = $this->get_price_html_from_to( $display_regular_price, $display_price ) . $this->get_price_suffix();
				} else {
					$price_html = wc_price( $display_price ) . $this->get_price_suffix();
				}
			}
		} elseif ( ! $this->has_additional_costs() ) {
			$price_html = '';
		} else {
			$price_html = '';
		}
		
		$price_html = apply_filters( 'woocommerce_return_price_html', $price_html, $this );
		
		//* Duration label
		if ( 'day' === $this->get_duration_unit() && $this->get_duration() ) {
			$duration_html = ' <small class="duration">' . sprintf( _n( '%s day', '%s days', $this->get_duration(), 'woocommerce-appointments' ), $this->get_duration() ) . '</small>';
		} else if ( 'minute' === $this->get_duration_unit() && $this->get_duration() ) {
			$duration_full = wc_appointment_convert_to_hours_and_minutes( $this->get_duration() );
			
			$duration_html = ' <small class="duration">' . $duration_full . '</small>';
			
		} else {
			$duration_html = ' <small class="duration">' . sprintf( _n( '%s hour', '%s hours', $this->get_duration(), 'woocommerce-appointments' ), $this->get_duration() ) . '</small>';
		}
		
		$duration_html = apply_filters( 'woocommerce_return_duration_html', $duration_html, $this );
		
		return apply_filters( 'woocommerce_get_price_html', $price_html . $duration_html, $this );
	}

	/**
	 * Find the minimum slot's timestamp based on settings
	 * @return int
	 */
	public function get_min_timestamp_for_date( $start_date ) {
		if ( $min = $this->get_min_date() ) {
			$today    = ( date( 'y-m-d', $start_date ) === date( 'y-m-d', current_time( 'timestamp' ) ) );
			$timestamp = strtotime( ( $today ? '' : 'midnight ' ) . "+{$min['value']} {$min['unit']}", current_time( 'timestamp' ) );
		} else {
			$timestamp = current_time( 'timestamp' );
		}
		
		return $timestamp;
	}

	/**
	 * Get Min date
	 * @return array|bool
	 */
	public function get_min_date() {
		$min_date['value'] = ! empty( $this->wc_appointment_min_date ) ? apply_filters( 'woocommerce_appointments_min_date_value', absint( $this->wc_appointment_min_date ), $this->id ) : 0;
		$min_date['unit']  = ! empty( $this->wc_appointment_min_date_unit ) ? apply_filters( 'woocommerce_appointments_min_date_unit', $this->wc_appointment_min_date_unit, $this->id ) : 'month';
		
		if ( $min_date['value'] ) {
			return $min_date;
		}
		
		return false;
	}

	/**
	 * Get max date
	 * @return array
	 */
	public function get_max_date() {
		$max_date['value'] = ! empty( $this->wc_appointment_max_date ) ? apply_filters( 'woocommerce_appointments_max_date_value', absint( $this->wc_appointment_max_date ), $this->id ) : 1;
		$max_date['unit']  = ! empty( $this->wc_appointment_max_date_unit ) ? apply_filters( 'woocommerce_appointments_max_date_unit', $this->wc_appointment_max_date_unit, $this->id ) : 'month';
		
		if ( $max_date['value'] ) {
			return $max_date;
		}
		
		return false;
	}

	/**
	 * Get max year
	 * @return string
	 */
	private function get_max_year() {
		// Find max to get first
		$max_date = $this->get_max_date();
		$max_date_timestamp = strtotime( "+{$max_date['value']} {$max_date['unit']}" );
		$max_year = date( 'Y', $max_date_timestamp );
		if ( ! $max_year ) {
			$max_year = date( 'Y' );
		}
		
		return $max_year;
	}

	/**
	 * Get staff by ID
	 * @param  int $id
	 * @return WC_Product_Appointment_Staff object
	 */
	public function get_staff_member( $id ) {
		global $wpdb;

		$id = absint( $id );

		if ( $id ) {		
			$staff = get_user_by( 'id', $id );
			$relationship_id = $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM {$wpdb->prefix}wc_appointment_relationships WHERE product_id = %d AND staff_id = %d", $this->id, $id ) );

			if ( is_object( $staff ) && 0 < $relationship_id ) {
				return new WC_Product_Appointment_Staff( $staff, $this->id );
			}
		}

		return false;
	}

	/**
	 * How staff is assigned
	 * @return string customer or automatic
	 */
	public function is_staff_assignment_type( $type ) {
		return $this->wc_appointment_staff_assignment === $type;
	}

	/**
	 * Get all staff
	 * @return array of WP_Post objects
	 */
	public function get_staff() {
		return wc_appointment_get_product_staff( $this->id );
	}
	
	/**
	 * Get array of costs
	 *
	 * @return array
	 */
	public function get_costs() {
		return WC_Product_Appointment_Rule_Manager::process_pricing_rules( $this->wc_appointment_pricing );
	}
	
	/**
	 * See if dates are by default appointable
	 * @return bool
	 */
	public function get_default_availability() {
		return apply_filters( 'woocommerce_appointment_default_availability', false, $this );
	}

	/**
	 * Checks if a product requires confirmation.
	 *
	 * @return bool
	 */
	public function requires_confirmation() {
		return apply_filters( 'woocommerce_appointment_requires_confirmation', 'yes' === $this->wc_appointment_requires_confirmation, $this );
	}

	/**
	 * See if the appointment can be cancelled.
	 *
	 * @return boolean
	 */
	public function can_be_cancelled() {
		return apply_filters( 'woocommerce_appointment_user_can_canel', 'yes' === $this->wc_appointment_user_can_cancel, $this );
	}
	
	/**
	 * Get the add to cart button text
	 *
	 * @return string
	 */
	public function add_to_cart_text() {
		return apply_filters( 'woocommerce_appointment_add_to_cart_text', __( 'Book Now', 'woocommerce-appointments' ), $this );
	}

	/**
	 * Get the add to cart button text for the single page
	 *
	 * @return string
	 */
	public function single_add_to_cart_text() {
		return 'yes' === $this->wc_appointment_requires_confirmation ? apply_filters( 'woocommerce_appointment_single_check_availability_text', __( 'Check Availability', 'woocommerce-appointments' ), $this ) : apply_filters( 'woocommerce_appointment_single_add_to_cart_text', __( 'Book Appointment', 'woocommerce-appointments' ), $this );
	}

	/**
	 * Return an array of staff which can be scheduled for a defined start/end date
	 * @param  string $start_date
	 * @param  string $end_date
	 * @param  int $staff_id
	 * @param  integer $qty being scheduled
	 * @return bool|WP_ERROR if no slots available, or int count of appointments that can be made, or array of available staff
	 */
	public function get_available_appointments( $start_date, $end_date, $staff_id = '', $qty = 1 ) {
		// Check the date is not in the past
		if ( date( 'Ymd', $start_date ) < date( 'Ymd', current_time( 'timestamp' ) ) ) {
			return false;
		}

		// Check we have a staff if needed
		$appointment_staff = $staff_id ? $this->get_staff_member( $staff_id ) : null;

		if ( $this->has_staff() && ! is_numeric( $staff_id ) ) {
			return false;
		}

		$min_date   = $this->get_min_date();
		$max_date   = $this->get_max_date();
		$check_from = strtotime( "midnight +{$min_date['value']} {$min_date['unit']}", current_time('timestamp') );
		$check_to   = strtotime( "+{$max_date['value']} {$max_date['unit']}", current_time('timestamp') );

		// Min max checks
		if ( $end_date < $check_from || $start_date > $check_to ) {
			return false;
		}

		// Get availability of each staff - no staff has been chosen yet
		if ( $this->has_staff() && ! $staff_id ) {
			return $this->get_all_staff_availability( $start_date, $end_date, $qty );
		// If we are checking for appointments for a specific staff, or have none...
		} else {
			$check_date = $start_date;

			while ( $check_date < $end_date ) {
				if ( ! $this->check_availability_rules_against_date( $check_date, $staff_id ) ) {
					return false;
				}
				if ( 'start' === $this->wc_appointment_availability_span ) {
					break; // Only need to check first day
				}
				$check_date = strtotime( "+1 day", $check_date );
			}

			if ( in_array( $this->get_duration_unit(), array( 'minute', 'hour' ) ) && ! $this->check_availability_rules_against_time( $start_date, $end_date, $staff_id ) ) {
				return false;
			}

			// Get slots availability
			return $this->get_slots_availability( $start_date, $end_date, $qty, $staff_id, $appointment_staff );
		}
	}

	/**
	 * Get the availability of all staff
	 *
	 * @param string $start_date
	 * @param string $end_date
	 * @return array|WP_Error
	 */
	public function get_all_staff_availability( $start_date, $end_date, $qty ) {
		$staff           = $this->get_staff();
		$available_staff = array();

		foreach ( $staff as $staff_member ) {
			$availability = $this->get_available_appointments( $start_date, $end_date, $staff_member->ID, $qty );

			if ( $availability && ! is_wp_error( $availability ) ) {
				$available_staff[ $staff_member->ID ] = $availability;
			}
		}

		if ( empty( $available_staff ) ) {
			return new WP_Error( 'Error', __( 'This slot cannot be scheduled.', 'woocommerce-appointments' ) );
		}

		return $available_staff;
	}

	/**
	 * Check the staff availability against all the slots.
	 *
	 * @param  string $start_date
	 * @param  string $end_date
	 * @param  int    $qty
	 * @param  int    $staff_id
	 * @param  object $appointment_staff
	 * @return string|WP_Error
	 */
	public function get_slots_availability( $start_date, $end_date, $qty, $staff_id, $appointment_staff ) {
		$slots   = $this->get_slots_in_range( $start_date, $end_date, '', $staff_id );
		$interval = 'hour' === $this->get_duration_unit() ? $this->get_duration() * 60 : $this->get_duration();
		$interval = 'day' === $this->get_duration_unit() ? $this->get_duration() * 60 * 24 : $interval;

		if ( ! $slots ) {
			return false;
		}

		/**
		 * Grab all existing appointments for the date range
		 * @var array
		 */
		$existing_appointments = $this->get_appointments_in_date_range( $start_date, $end_date, $staff_id );
		$available_qtys    = array();

		// Check all slots availability
		foreach ( $slots as $slot ) {
			$qty_scheduled_in_slot = 0;
			
			// Check capacity based on duration unit
			if ( in_array( $this->get_duration_unit(), array( 'hour', 'minute' ) ) ) {
				$slot_qty = $this->check_availability_rules_against_time( $slot, $slot, $staff_id, true );
			} else {
				$slot_qty = $this->check_availability_rules_against_date( $slot, $staff_id, true );
			}

			foreach ( $existing_appointments as $existing_appointment ) {
				if ( $existing_appointment->is_scheduled_on_day( $slot, strtotime( "+{$interval} minutes", $slot ) ) ) {
					$qty_to_add = isset( $existing_appointment->qty ) ? $existing_appointment->qty : 1;
					if ( $this->has_staff() && $existing_appointment->get_staff_id() ) {
						//$qty_scheduled_in_slot += $slot_qty;
						$qty_scheduled_in_slot += $qty_to_add; #revert back if it doesn't work for customers
					} else {
						$qty_scheduled_in_slot += $qty_to_add;
					}
				}
			}
			
			//* Only available capacity is used
			/*
			if ( $this->has_staff() && ( $this->has_staff() < $this->get_qty() ) && ! $staff_id ) {
				$slot_qty = $this->has_staff();
			} elseif ( $this->has_staff() && ( $this->has_staff() < $this->get_qty() ) && $staff_id ) {
				$slot_qty = 1; # if staff capacity gets introduced, add it here
			} else {
				$slot_qty = $this->check_availability_rules_against_time( $slot, $slot, $staff_id, true );
			}
			*/
			
			//* Multiple available staff by number of staff, when staff isn't selected
			if ( $this->has_staff() && ! $staff_id ) {
				$slot_qty = $slot_qty * $this->has_staff();
			}
			
			// var_dump( $available_qty .' = '. $slot_qty .' - '. $qty_scheduled_in_slot );
			
			//* Calculate availably capacity
			$available_qty = max( $slot_qty - $qty_scheduled_in_slot, 0 );
												
			// Remaining places are less than requested qty, return an error.
			if ( $available_qty < $qty ) {
				if ( in_array( $this->get_duration_unit(), array( 'hour', 'minute' ) ) ) {
					return new WP_Error( 'Error', sprintf(
						_n( 'There is only %d place remaining on %s at %s.', 'There are %d places remaining on %s at %s.', $available_qty, 'woocommerce-appointments' ),
						max( $available_qty, 0 ),
						date_i18n( wc_date_format(), $slot ),
						date_i18n( get_option( 'time_format' ), $start_date )
					) );
				} elseif ( ! $available_qtys ) {
					return new WP_Error( 'Error', sprintf(
						_n( 'There is only %d place remaining on %s', 'There are %d places remaining on %s', $available_qty , 'woocommerce-appointments' ),
						$available_qty,
						date_i18n( wc_date_format(), $slot )
					) );
				} else {
					return new WP_Error( 'Error', sprintf(
						_n( 'There is only %d place remaining on %s.', 'There are %d places remaining on %s.', $available_qty, 'woocommerce-appointments' ),
						max( $available_qtys ),
						date_i18n( wc_date_format(), $slot )
					) );
				}
			}

			$available_qtys[] = $available_qty;
		}

		return min( $available_qtys );
	}

	/**
	 * Get existing appointments in a given date range
	 *
	 * @param string $start-date
	 * @param string $end_date
	 * @param int    $staff_id
	 * @return array
	 */
	public function get_appointments_in_date_range( $start_date, $end_date, $staff_id = null ) {
		if ( $this->has_staff() && $staff_id ) {
			return WC_Appointments_Controller::get_appointments_in_date_range( $start_date, $end_date, $staff_id );
		} else {
			return WC_Appointments_Controller::get_appointments_in_date_range( $start_date, $end_date, $this->id );
		}
	}

	/**
	 * Get array of rules.
	 * @return array
	 */
	public function get_availability_rules( $for_staff = 0 ) {
		if ( empty( $this->availability_rules[ $for_staff ] ) ) {
			$this->availability_rules[ $for_staff ] = array();

			// Rule types
			$staff_rules = array();
			$product_rules  = $this->wc_appointment_availability;
			$global_rules   = get_option( 'wc_global_appointment_availability', array() );

			// Get availability of each staff - no staff has been chosen yet
			if ( $this->has_staff() && ! $for_staff ) {
				$staff_rules = array();
				
				if ( $this->get_default_availability() ) {
					// If all slotss are available by default, we should not hide days if we don't know which staff is going to be used.
				} else {
					$staff = $this->get_staff();
					foreach ( $staff as $staff_member ) {
						$staff_rule = (array) get_user_meta( $staff_member->ID, '_wc_appointment_availability', true );
						$staff_rules = array_merge( $staff_rules, $staff_rule );
					}
				}

			// Standard handling
			} elseif ( $for_staff ) {
				$staff_rules = (array) get_user_meta( $for_staff, '_wc_appointment_availability', true );
			}

			// Merge and reverse order so lower rules are evaluated first			
			$availability_rules = array_filter( array_reverse( array_merge( WC_Product_Appointment_Rule_Manager::process_availability_rules( $global_rules, 'global' ), WC_Product_Appointment_Rule_Manager::process_availability_rules( $product_rules, 'product' ), WC_Product_Appointment_Rule_Manager::process_availability_rules( $staff_rules, 'staff' ) ) ) );
			//usort( $availability_rules, array( $this, 'priority_sort' ) );

			$this->availability_rules[ $for_staff ] = $availability_rules;
			
		}
		
		return apply_filters( 'woocommerce_appointment_get_availability_rules', $this->availability_rules[ $for_staff ], $for_staff, $this );
	}
	
	/**
	 * Sort rules based on their priority
	 * which is array index '2' of each rule. Lower number should be more important/parsed first
	 * If priority is the same, it goes global < product < staff. Staff take priority
	 */
	 
	/* staff < product < global */
	public function priority_sort( $rule_1, $rule_2 ) {
		if ( $rule_1[2] === $rule_2[2] ) {
			
			if ( $rule_1[3] === $rule_2[3] ) {
				return 0;
			}

			if ( 'global' === $rule_2[3] && 'product' === $rule_1[3] ) {
				return -1;
			}

			if ( 'global' === $rule_2[3] && 'staff' === $rule_1[3] ) {
				return -1;
			}

			if ( 'product' === $rule_2[3] && 'global' === $rule_1[3] ) {
				return 1;
			}

			if ( 'product' === $rule_2[3] && 'staff' === $rule_1[3] ) {
				return -1;
			}
			
			if ( 'staff' === $rule_2[3] && 'product' === $rule_1[3] ) {
				return 1;
			}

			if ( 'staff' === $rule_2[3] &&  'global' === $rule_1[3] ) {
				return 1;
			}

		}
		return ( $rule_1[2] < $rule_2[2] ) ? -1 : 1;
	}

	/**
	 * Check a date against the availability rules
	 * @param  string $check_date date to check
	 * @return bool available or not
	 */
	public function check_availability_rules_against_date( $check_date, $staff_id, $get_capacity = false ) {
		$year        = date( 'Y', $check_date );
		$month       = absint( date( 'm', $check_date ) );
		$day         = absint( date( 'd', $check_date ) );
		$day_of_week = absint( date( 'N', $check_date ) );
		$week        = absint( date( 'W', $check_date ) );
		$day_format  = date( 'Y-m-d', $check_date );
		$hour_format = absint( date( 'H:i', $check_date ) );
		$appointable = $default_availability = $this->get_default_availability();
		$capacity    = $this->get_qty();
		
		// var_dump($day);
		
		foreach ( $this->get_availability_rules( $staff_id ) as $rule ) {
			$type  = $rule[0];
			$rules = $rule[1];
			$qty   = $rule[4] ? $rule[4] : $capacity;
			
			switch ( $type ) {
				case 'months' :
					if ( isset( $rules[ $month ] ) ) {
						$appointable = $rules[ $month ];
						$capacity = $qty;
						break 2;
					}
				break;
				case 'weeks':
					if ( isset( $rules[ $week ] ) ) {
						$appointable = $rules[ $week ];
						$capacity = $qty;
						break 2;
					}
				break;
				case 'days' :
					if ( isset( $rules[ $day_of_week ] ) ) {
						$appointable = $rules[ $day_of_week ];
						$capacity = $qty;
						break 2;
					}
				break;
				case 'custom' :
					if ( isset( $rules[ $year ][ $month ][ $day ] ) ) {
						$appointable = $rules[ $year ][ $month ][ $day ];
						$capacity = $qty;
						break 2;
					}
				break;
				/* DEPRECATED
				case 'time_date' :
					if ( $rules['date'] === $day_format && $rules['from'] <= $hour_format && $rules['to'] >= $hour_format ) {
						$appointable = true;
						$capacity = $qty;
						break 2;
					}
				break;
				*/
				/*
				case 'time':
				case 'time:1':
				case 'time:2':
				case 'time:3':
				case 'time:4':
				case 'time:5':
				case 'time:6':
				case 'time:7':
					if ( false === $default_availability && ( $day_of_week === $rules['day'] || 0 === $rules['day'] ) ) {
						$appointable = $rules['rule'];
						$capacity = $qty;
						break 2;
					}
				break;
				case 'time:range':
					if ( false === $default_availability && ( isset( $rules[ $year ][ $month ][ $day ] ) ) ) {
						$appointable = $rules[ $year ][ $month ][ $day ]['rule'];
						$capacity = $qty;
						break 2;
					}
				break;
				*/
			}
		}
		
		//* Return rule type capacity
		if ( $get_capacity ) {
			return absint( $capacity );
		}

		return $appointable;
	}

	/**
	 * Check a time against the availability rules
	 * @param  string $start_time timestamp to check
	 * @param  string $end_time timestamp to check
	 * @return bool available or not
	 */
	public function check_availability_rules_against_time( $start_time, $end_time, $staff_id, $get_capacity = false ) {
		$appointable	= $this->get_default_availability();
		$start_time		= is_numeric( $start_time ) ? $start_time : strtotime( $start_time );
		$end_time		= is_numeric( $end_time ) ? $end_time : strtotime( $end_time );
		$capacity		= $this->get_qty();

		foreach ( $this->get_availability_rules( $staff_id ) as $rule ) {
			$type  = $rule[0];
			$rules = $rule[1];
			$qty   = $rule[4] && $rule[4] >= 1  ? $rule[4] : $capacity;

			if ( strrpos( $type, 'time' ) === 0 || 'time_date' === $type ) {			
				if ( 'time:range' === $type ) {
					$year = date( 'Y', $start_time );
					$month = date( 'n', $start_time );
					$day = date( 'j', $start_time );

					if ( ! isset( $rules[ $year ][ $month ][ $day ] ) ) {
						continue;
					}

					$rule_val = $rules[ $year ][ $month ][ $day ]['rule'];
					$from     = $rules[ $year ][ $month ][ $day ]['from'];
					$to       = $rules[ $year ][ $month ][ $day ]['to'];
				} else {
					if ( ! empty( $rules['day'] ) ) {
						if ( $rules['day'] != date( 'N', $start_time ) ) {
							continue;
						}
					} else if ( ! empty( $rules['date'] ) ) {
						if ( $rules['date'] != date( 'Y-m-d', $start_time ) ) {
							continue;
						}
					}

					$rule_val = $rules['rule'];
					$from     = $rules['from'];
					$to       = $rules['to'];
				}

				$start_time_hi      = date( 'YmdHis', $start_time );
				$end_time_hi        = date( 'YmdHis', $end_time );
				$rule_start_time_hi = date( 'YmdHis', strtotime( $from, $start_time ) );
				$rule_end_time_hi   = date( 'YmdHis', strtotime( $to, $start_time ) );

				// Reverse time rule - The end time is tomorrow e.g. 16:00 today - 12:00 tomorrow
				if ( $rule_end_time_hi <= $rule_start_time_hi ) {
					if ( $end_time_hi > $rule_start_time_hi ) {
						$appointable = $rule_val;
						$capacity = $qty;
						break;
					}
					if ( $start_time_hi >= $rule_start_time_hi && $end_time_hi >= $rule_end_time_hi ) {
						$appointable = $rule_val;
						$capacity = $qty;
						break;
					}
					if ( $start_time_hi <= $rule_start_time_hi && $end_time_hi <= $rule_end_time_hi ) {
						$appointable = $rule_val;
						$capacity = $qty;
						break;
					}

				// Normal rule
				} else {
					if ( $start_time_hi >= $rule_start_time_hi && $end_time_hi <= $rule_end_time_hi ) {
						$appointable = $rule_val;
						$capacity = $qty;
						break;
					}
				}
			}
		}
		
		//* Return rule type capacity
		if ( $get_capacity ) {
			return absint( $capacity );
		}

		return $appointable;
	}

	/**
	 * Get an array of slots within in a specified date range - might be days, might be slots within days, depending on settings.
	 * @return array
	 */
	public function get_slots_in_range( $start_date, $end_date, $intervals = array(), $staff_id = 0, $scheduled = array() ) {
		if ( empty( $intervals ) ) {
			$default_interval = 'hour' === $this->get_duration_unit() ? $this->wc_appointment_duration * 60 : $this->wc_appointment_duration;
			$intervals        = array( $default_interval, $default_interval );
		}
		
		list( $interval, $base_interval ) = $intervals;
		
		//* get padding duration
		$padding_duration	= 'hour' === $this->get_padding_duration_unit() ? $this->wc_appointment_padding_duration * 60 : $this->wc_appointment_padding_duration;
		//* double padding duration if padding is on 'both' ends
		$padding_interval	= 'both' === $this->get_padding_duration_when() ? $padding_duration * 2 : $padding_duration;
		//* adjust intervals according to padding
		$interval			= $interval + $padding_interval;
		$base_interval 		= $base_interval + $padding_interval;
		
		//* staff object
		//$appointment_staff = $staff_id ? $this->get_staff_member( $staff_id ) : null;

		$slots = array();

		// For day, minute and hour slots we need to loop through each day in the range
		if ( in_array( $this->get_duration_unit(), array( 'night', 'day', 'minute', 'hour' ) ) ) {
			$check_date = $start_date;
							
			while ( $check_date <= $end_date ) {
				if ( in_array( $this->get_duration_unit(), array( 'day', 'night' ) ) && ! $this->check_availability_rules_against_date( $check_date, $staff_id ) ) {
					$check_date = strtotime( "+1 day", $check_date );
					continue;
				}
				
				// For mins and hours find valid slots within THIS DAY ($check_date)
				if ( in_array( $this->get_duration_unit(), array( 'minute', 'hour' ) ) ) {
					$min_date               = $this->get_min_timestamp_for_date( $start_date );

					// Work out what minutes are actually appointable on this day
					$appointable_minutes	= $this->get_default_availability() ? range( 0, ( 1440 + $interval ) ) : array();
					$rules					= $this->get_availability_rules( $staff_id );

					// Since we evaluate all time rules and don't break out when one matches, reverse the array
					$rules            		= array_reverse( $rules );

					foreach ( $rules as $rule ) {
						$type  = $rule[0];
						$_rules = $rule[1];
						
						if ( strrpos( $type, 'time' ) === 0 || 'time_date' === $type ) {
							if ( 'time:range' === $type ) {
								$year = date( 'Y', $check_date );
								$month = date( 'n', $check_date );
								$day = date( 'j', $check_date );
								
								if ( ! isset( $_rules[ $year ][ $month ][ $day ] ) ) {
									continue;
								}

								$day_mod = 0;
								$from = $_rules[ $year ][ $month ][ $day ]['from'];
								$to   = $_rules[ $year ][ $month ][ $day ]['to'];
								$rule_val = $_rules[ $year ][ $month ][ $day ]['rule'];
							} else {
								$day_mod = 0;
								if ( ! empty( $_rules['day'] ) ) {
									if ( $_rules['day'] != date( 'N', $check_date ) ) {
										$day_mod = 1440 * ( $_rules['day'] - date( 'N', $check_date ) );
									}
								}
								// skip this rule for all dates, except selected one
								else if ( ! empty( $_rules['date'] ) ) {
									if ( $_rules['date'] != date( 'Y-m-d', $check_date ) ) {
										//$day_mod = 1440 * ( date( 'N', strtotime( $_rules['date'] ) ) - date( 'N', $check_date ) );
										continue;
									}
								}

								$from = $_rules['from'];
								$to   = $_rules['to'];
								$rule_val = $_rules['rule'];
							}

							$from_hour    = absint( date( 'H', strtotime( $from ) ) );
							$from_min     = absint( date( 'i', strtotime( $from ) ) );
							$to_hour      = absint( date( 'H', strtotime( $to ) ) );
							$to_min       = absint( date( 'i', strtotime( $to ) ) );
							
							/* If "to" is set to midnight, it is safe to assume they mean the end of the day php wraps 24 hours to "12AM the next day"
							 * (note) only works for specific day of week, not working for specific date yet
							 *
							 */
							if ( 0 === $to_hour && empty( $_rules['date'] ) ) {
								$to_hour = 24;
							}

							$minute_range = array( ( ( $from_hour * 60 ) + $from_min ) + $day_mod, ( ( $to_hour * 60 ) + $to_min ) + $day_mod );
							$merge_ranges = array();
							
							if ( $minute_range[0] > $minute_range[1] ) {
								$merge_ranges[] = array( $minute_range[0], 1440 ); #from
								$merge_ranges[] = array( 0, $minute_range[1] ); #to
							} else {
								$merge_ranges[] = array( $minute_range[0], $minute_range[1] ); #from, to
							}
							
							foreach ( $merge_ranges as $range ) {
								if ( $appointable = $rule_val ) {
									// If this time range is appointable, add to appointable minutes
									$appointable_minutes = array_merge( $appointable_minutes, range( $range[0], $range[1] ) );
								} else {
									// If this time range is not appointable, remove from appointable minutes
									$appointable_minutes = array_diff( $appointable_minutes, range( $range[0] + 1, $range[1] - 1 ) );
								}
							}
						}
					}
					
					//* Get unique array elements
					$appointable_minutes = array_unique( $appointable_minutes );

					//* Sort array
					sort( $appointable_minutes );

					// Break appointable minutes into sequences - appointments cannot have breaks
					$appointable_minute_slots     = array();
					$appointable_minute_slot_from = current( $appointable_minutes );

					foreach ( $appointable_minutes as $key => $minute ) {
						if ( isset( $appointable_minutes[ $key + 1 ] ) ) {
							if ( $appointable_minutes[ $key + 1 ] - 1 === $minute ) {
								continue;
							} else {
								// There was a break in the sequence
								$appointable_minute_slots[]   = array( $appointable_minute_slot_from, $minute );
								$appointable_minute_slot_from = $appointable_minutes[ $key + 1 ];
							}
						} else {
							// We're at the end of the appointable minutes
							$appointable_minute_slots[] = array( $appointable_minute_slot_from, $minute );
						}
					}
					
					/**
					 * Find slots that don't span any amount of time (same start + end)
					 */
					/*
					foreach ( $appointable_minute_slots as $key => $appointable_minute_slot ) {
						if ( $appointable_minute_slot[0] === $appointable_minute_slot[1] ) {
							$keys_to_remove[] = $key; // track which slots need removed
						}
					}
					// Remove all of our slots
					if ( ! empty ( $keys_to_remove ) ) {
						foreach ( $keys_to_remove as $key ) {
							unset( $appointable_minute_slots[ $key ] );
						}
					}
					*/

					// Create an array of already scheduled slots
					$scheduled_slots = array();

					foreach( $scheduled as $scheduled_slot ) {
						for ( $i = $scheduled_slot[0]; $i < $scheduled_slot[1]; $i += 60 ) {
							array_push( $scheduled_slots, $i );
						}
					}
					
					$scheduled_slots = array_count_values( $scheduled_slots );
										
					// Loop the slots of appointable minutes and add a slot if there is enough room to schedule
					foreach ( $appointable_minute_slots as $time_slot ) {
						
						//* postpone start time if padding is set to 'both' or 'before'
						$time_slot[0]		= in_array( $this->get_padding_duration_when(), array( 'both', 'before' ) ) ? $time_slot[0] + $padding_duration : $time_slot[0];
						
						//* postpone end time if padding is set to 'both' or 'before'
						$time_slot[1]		= in_array( $this->get_padding_duration_when(), array( 'both', 'before' ) ) ? $time_slot[1] + $padding_duration : $time_slot[1];
						
						$time_slot_start        = strtotime( "midnight +{$time_slot[0]} minutes", $check_date );
						$minutes_in_slot        = $time_slot[1] - $time_slot[0];
						$base_intervals_in_slot = floor( $minutes_in_slot / $base_interval );
						
						// Only need to check first hour
						if ( 'start' === $this->wc_appointment_availability_span ) {
							$base_interval = 1; #test
							$base_intervals_in_slot = 1; #test
						}
						
						for ( $i = 0; $i < $base_intervals_in_slot; $i ++ ) {
							$from_interval = $i * $base_interval;
							$start_time    = strtotime( "+{$from_interval} minutes", $time_slot_start );
							
							//* Ensure slot can fit the entire user set interval
							$to_interval         = $from_interval + $interval;
							$end_time            = strtotime( "+{$to_interval} minutes", $time_slot_start );
							$time_slot_end_time  = strtotime( "midnight +{$time_slot[1]} minutes", $check_date );
							$loop_time           = $start_time;
							
							//* change quantity if different for time slot
							$available_qty		 = $this->check_availability_rules_against_time( $loop_time, $end_time, $staff_id, true );
							// $available_qty		= $this->get_qty();

							// Break if start time is after the end date being calced
							if ( $start_time > $end_date && ( 'start' !== $this->wc_appointment_availability_span )  ) {
								break 2;
							}

							// Must be in the future
							if ( $start_time <= $min_date || $start_time <= current_time( 'timestamp' ) ) {
								continue;
							}

							if ( isset( $scheduled_slots[ $start_time ] ) && $scheduled_slots[ $start_time ] >= $available_qty ) {
								continue;
							}
							
							//* make sure minute & hour slots are not past minimum & max appointment settings
							$product_min_date = $this->get_min_date();
							$product_max_date = $this->get_max_date();
							$min_check_from   = strtotime( "+{$product_min_date['value']} {$product_min_date['unit']}", current_time( 'timestamp' ) );
							$max_check_to     = strtotime( "+{$product_max_date['value']} {$product_max_date['unit']}", current_time( 'timestamp' ) );

							if ( $end_date < $min_check_from || $start_time > $max_check_to ) {
								continue;
							}

							// This checks all minutes in slot for availability
							while ( $loop_time < $end_time ) {
								if ( isset( $scheduled_slots[ $loop_time ] ) && $scheduled_slots[ $loop_time ] >= $available_qty ) {
									continue 2;
								}
								$loop_time = $loop_time + 60;
							}

							if ( $end_time > $time_slot_end_time && ( 'start' !== $this->wc_appointment_availability_span ) ) {
								continue;
							}
							
							if ( ! in_array( $start_time, $slots ) ) {
								$slots[] = $start_time;
							}
						}
					}
				
				// For days, the day is the block so we can just count the already scheduled slots rather than check their block times
				} else {

					$available_qty = $this->check_availability_rules_against_date( $check_date, $staff_id, true );
					//$available_qty = $this->get_qty();
					
					$qty_scheduled_in_slot 	= 0;
					if ( is_array( $scheduled ) ) {
						foreach( $scheduled as $scheduled_slot ) {
							$qty_to_add = isset( $scheduled_slot[2] ) ? $scheduled_slot[2] : 1;
							$qty_scheduled_in_slot += $qty_to_add;
						}
					}
										
					if ( $qty_scheduled_in_slot < $available_qty ) {
						// $slots[] =  date( 'ymd', $check_date );
						$slots[] = $check_date;
					}
				}

				// Check next day
				$check_date = strtotime( "+1 day", $check_date );
			}
		
		}
		
		return $slots;
	}

	/**
	 * Returns available slots from a range of slots by looking at existing appointments.
	 * @param  array   $slots      The slots we'll be checking availability for.
	 * @param  array   $intervals   Array containing 2 items; the interval of the slot (maybe user set), and the base interval for the slot/product.
	 * @param  integer $staff_id Staff we're getting slots for. Falls backs to product as a whole if 0.
	 * @param  string  $from        The starting date for the set of slots
	 * @return array The available slots array
	 */
	public function get_available_slots( $slots, $intervals = array(), $staff_id = 0, $from = '' ) {
		if ( empty( $intervals ) ) {
			$default_interval = 'hour' === $this->get_duration_unit() ? $this->wc_appointment_duration * 60 : $this->wc_appointment_duration;
			$intervals        = array( $default_interval, $default_interval );
		}

		list( $interval, $base_interval ) = $intervals;
				
		$available_slots   = array();
		$start_date = empty ( $from ) ? current( $slots ) : $from;
		$end_date = end( $slots );
		
		if ( ! empty( $slots ) ) {
			/**
			 * Grab all existing appointments for the date range
			 * @var array
			 */		
			$existing_appointments = $this->get_appointments_in_date_range( $start_date, $end_date + ( $base_interval * 60 ), $staff_id );

			// Staff scheduled array. Staff can be a "staff" but also just an appointment if it has no staff
			$staff_scheduled = array( 0 => array() );

			//* Get slot qty
			$available_qty = $this->check_availability_rules_against_time( $start_date, $end_date + ( $base_interval * 60 ), $staff_id, true );
			
			// Loop all existing appointments
			foreach ( $existing_appointments as $appointment ) {
				$appointment_staff_id = $staff_id ? $appointment->get_staff_id() : 0;
				
				// prepare staff array for staff id
				$staff_scheduled[ $appointment_staff_id ] = isset( $staff_scheduled[ $appointment_staff_id ] ) ? $staff_scheduled[ $appointment_staff_id ] : array();

				// we should disable stuff where nothing is available
				$repeat = max( 1, $appointment->qty );
				// $repeat = max( 1, $available_qty );
				// $repeat = max( 1, $this->has_staff() ? $available_qty : 1 );

				for ( $i = 0; $i < $repeat; $i++ ) {
					array_push( $staff_scheduled[ $appointment_staff_id ], array( $appointment->start, $appointment->end, intval( $available_qty ) ) );
				}
			}

			// Generate arrays that contain information about what slots to unset
			if ( $this->has_staff() && ! $staff_id ) {
				$staff = $this->get_staff();
				$available_times = array();

				// Loop all staff
				foreach ( $staff as $staff_member ) {
					$times           = $this->get_slots_in_range( $start_date, $end_date, array( $interval, $base_interval ), $staff_member->ID, isset( $staff_scheduled[ $staff_member->ID ] ) ? $staff_scheduled[ $staff_member->ID ] : array() );
					$available_times = array_merge( $available_times, $times );
				}
			} else {
				$available_times = $this->get_slots_in_range( $start_date, $end_date, array( $interval, $base_interval ), $staff_id, isset( $staff_scheduled[ $staff_id ] ) ? $staff_scheduled[ $staff_id ] : $staff_scheduled[ 0 ] );
			}

			//* Count scheduled times then loop the slots
			$available_times = array_count_values( $available_times );

			// Loop through all slots and unset if they are allready scheduled
			foreach ( $slots as $slot ) {
				if ( isset( $available_times[ $slot ] ) ) {
					$available_slots[] = $slot;
				}
			}
			
			/**
			 * Fake it filter
			 * display in calendar only if any slots are already scheduled
			 */
			if ( $this->wc_appointment_fakeit != 'off' && $this->wc_appointment_fakeit != '' && count( $available_slots ) > 0 ) {
				/**
				 * calculate faked slots to remove
				 */
				$fakeit = round( count( $available_slots ) * ( 1 - (int) $this->wc_appointment_fakeit / 100 ) );
				
				/**
				 * create and assign a 'seed' value that changes
				 * every $duration and assign to mt_srand
				 */
				$mins = date( 'dHi', $start_date );
				$seed = $mins - ( $mins % $interval );
				mt_srand( $seed );

				/**
				 * use mt_rand to build an 'order by'
				 * array that will change every $interval
				 */
				$orderBy = array_map( function() {
					return mt_rand();
				}, range( 1, count( $available_slots ) ) );

				/**
				 * sort $available_slots against $orderBy
				 */
				array_multisort( $orderBy, $available_slots );
				
				/**
				 * sort $available_slots lowest to highest strtotitme'd values
				 */
				sort( $available_slots );
				
				/**
				 * only show the first x slice of array
				 * sort by key: low to high
				 */
				$available_slots = array_slice( $available_slots, 0, $fakeit );
			}
			
		}
		
		// Even though we checked hours against other days/slots, make sure we only return slots for this date..
		if ( in_array( $this->get_duration_unit(), array( 'minute', 'hour' ) ) && ! empty ( $from ) ) {
			$time_slots = array();
			foreach ( $available_slots as $key => $slot_date ) {
				if ( date( 'ymd', $slot_date ) == date( 'ymd', $from ) ) {
					$time_slots[] = $slot_date;
				}
			}
			$available_slots = $time_slots;
		}

		return $available_slots;
	}

	/**
	 * Find available slots and return HTML for the user to choose a slot. Used in class-wc-appointments-admin-ajax.php
	 * @param  array  $slots
	 * @param  array  $intervals
	 * @param  integer $staff_id
	 * @return string
	 */
	public function get_available_slots_html( $slots, $intervals = array(), $time_to_check = 0, $staff_id = 0, $from = '' ) {
		if ( empty( $intervals ) ) {
			$default_interval = 'hour' === $this->get_duration_unit() ? $this->wc_appointment_duration * 60 : $this->wc_appointment_duration;
			$intervals        = array( $default_interval, $default_interval );
		}

		list( $interval, $base_interval ) = $intervals;
		
		$start_date = current( $slots );
		$end_date = end( $slots );
		
		$slots 					= $this->get_available_slots( $slots, $intervals, $staff_id, $from );
		$existing_appointments 	= $this->get_appointments_in_date_range( $start_date, ( $end_date + ( $base_interval * 60 ) ), $staff_id );
		$appointment_staff  	= $staff_id ? $this->get_staff_member( $staff_id ) : null;
		$slot_html 				= '';
		
		if ( $slots ) {
			
			// Split day into three parts
			$times = apply_filters( 'woocommerce_appointments_times_split', array( 
				"morning" => array(
					"name" => __( 'Morning', 'woocommerce-appointments' ),
					"from" => strtotime("00:00"),
					"to" => strtotime("12:00"),
				),
				"afternoon" => array(
					"name" => __( 'Afternoon', 'woocommerce-appointments' ),
					"from" => strtotime("12:00"),
					"to" => strtotime("17:00"),
				),
				"evening" => array(
					"name" => __( 'Evening', 'woocommerce-appointments' ),
					"from" => strtotime("17:00"),
					"to" => strtotime("24:00"),
				),
			));
			
			$slot_html .= "<div class=\"slot_row\">";
			foreach( $times as $k => $v ) {
				$slot_html .= "<ul class=\"slot_column $k\">";
				$slot_html .= '<li class="slot_heading">' . $v['name'] . '</li>';
				$count = 0;
				
				foreach ( $slots as $slot ) {
					$qty_scheduled_in_slot 	= 0;
					$slot_qty = $this->check_availability_rules_against_time( $slot, $slot, $staff_id, true );
					if ( $v['from'] <= strtotime( date( 'G:i', $slot ) ) && $v['to'] > strtotime( date( 'G:i', $slot ) ) ) {
						$selected = date( 'G:i', $slot ) == date( 'G:i', $time_to_check ) ? ' selected' : '';
						
						foreach ( $existing_appointments as $existing_appointment ) {
							if ( $existing_appointment->is_within_slot( $slot, strtotime( "+{$interval} minutes", $slot ) ) ) {
								if ( $this->has_staff() && $existing_appointment->get_staff_id() ) {
									$qty_scheduled_in_slot += $slot_qty;
								} else {
									$qty_to_add = isset( $existing_appointment->qty ) ? $existing_appointment->qty : 1;
									$qty_scheduled_in_slot += $qty_to_add;
								}
							}
						}

						//* Only available capacity is used
						/*
						if ( $this->has_staff() && ( $this->has_staff() < $this->get_qty() ) && ! $staff_id ) {
							$slot_qty = $this->has_staff();
						} elseif ( $this->has_staff() && ( $this->has_staff() < $this->get_qty() ) && $staff_id ) {
							$slot_qty = 1; # if staff capacity gets introduced, add it here
						} else {
							$slot_qty = $this->check_availability_rules_against_time( $slot, $slot, $staff_id, true );
						}
						*/
						
						//* Multiple available staff by number of staff, when staff isn't selected
						if ( $this->has_staff() && ! $staff_id ) {
							$slot_qty = $slot_qty * $this->has_staff();
						}
						
						//* Calculate availably capacity
						$available_qty = max( $slot_qty - $qty_scheduled_in_slot, 0 );

						//echo $available_qty . ' = ' . $slot_qty . ' - ' . $qty_scheduled_in_slot;
						
						if ( $available_qty > 0 ) {
							if ( $qty_scheduled_in_slot ) {
								$slot_html .= "<li class=\"slot$selected\" data-slot=\"" . esc_attr( date( 'Hi', $slot ) ) . "\"><a href=\"#\" data-value=\"" . date( 'G:i', $slot ) . "\">" . date_i18n( get_option( 'time_format' ), $slot ) . " <small class=\"spaces-left\">(" . sprintf( _n( '%d left', '%d left', $available_qty, 'woocommerce-appointments' ), absint( $available_qty ) ) . ")</small></a></li>";
							} else {
								$slot_html .= "<li class=\"slot$selected\" data-slot=\"" . esc_attr( date( 'Hi', $slot ) ) . "\"><a href=\"#\" data-value=\"" . date( 'G:i', $slot ) . "\">" . date_i18n( get_option( 'time_format' ), $slot ) . "</a></li>";
							}
						} else {
							continue;
						}

					} else {
						continue;
					}
					
					$count++;
				}
				
				if ( ! $count ) {
					$slot_html .= '<li class="slot slot_empty">' . __( '&#45;', 'woocommerce-appointments' ) . '</li>';
				}
				$slot_html .= "</ul>";
			}
			$slot_html .= "</div>";
			
		}

		return $slot_html;
	}
}
