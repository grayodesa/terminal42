<?php

/**
* Main model class for all appointments, this handles all the data
*/
class WC_Appointment {

	/** @public int */
	public $id;

	/** @public string */
	public $appointment_date;

	/** @public string */
	public $start;

	/** @public string */
	public $end;
	
	/** @public string */
	public $qty;

	/** @public bool */
	public $all_day;

	/** @public string */
	public $modified_date;

	/** @public object */
	public $post;

	/** @public int */
	public $product_id;

	/** @public object */
	public $product;

	/** @public int */
	public $order_id;

	/** @public object */
	public $order;

	/** @public int */
	public $customer_id;

	/** @public string */
	public $status;

	/** @public array - contains all post meta values for this appointment */
	public $custom_fields;

	/** @public bool */
	public $populated;

	/** @private array - used to temporarily hold order data for new appointments */
	private $order_data;

	/**
	 * Constructor, possibly sets up with post or id belonging to existing appointment
	 * or supplied with an array to construct a new appointment
	 * @param int/array/obj $appointment_data
	 */
	public function __construct( $appointment_data = false ) {
		$populated = false;

		if ( is_array( $appointment_data ) ) {
			$this->order_data = $appointment_data;
			$populated = false;
		} else if ( is_int( intval( $appointment_data ) ) && 0 < $appointment_data ) {
			$populated = $this->populate_data( $appointment_data );
		} else if ( is_object( $appointment_data ) && isset( $appointment_data->ID ) ) {
			$this->post = $appointment_data;
			$populated = $this->populate_data( $appointment_data->ID );
		}

		$this->populated = $populated;
	}

	/**
	 * Actual create for the new appointment belonging to an order
	 * @param string Status for new order
	 */
	public function create( $status = 'unpaid' ) {
		$this->new_appointment( $status, $this->order_data );
		$this->schedule_events();
	}

	/**
	 * Schedule events for this appointment
	 */
	public function schedule_events() {
		if ( in_array( get_post_status( $this->id ), get_wc_appointment_statuses( 'scheduled' ) ) ) {
			if ( $this->start && $this->get_order() ) {
				$order_status = $this->get_order()->get_status();
				if ( ! in_array( $order_status, array( 'cancelled', 'refunded', 'pending', 'on-hold' ) ) ) {
					wp_schedule_single_event( strtotime( '-' . absint( apply_filters( 'woocommerce_appointments_remind_before_days', 1 ) ) . ' day', $this->start ), 'wc-appointment-reminder', array( $this->id ) );
				}
			}
			if ( $this->end ) {
				wp_schedule_single_event( $this->end, 'wc-appointment-complete', array( $this->id ) );
			}
		} else {
			wp_clear_scheduled_hook( 'wc-appointment-reminder', array( $this->id ) );
			wp_clear_scheduled_hook( 'wc-appointment-complete', array( $this->id ) );
		}
	}

	/**
	 * Makes the new appointment belonging to an order
	 * @param string $status The status for this new appointment
	 * @param array $order_data Array with all the new order data
	 */
	private function new_appointment( $status, $order_data ) {
		global $wpdb;

		$order_data = wp_parse_args( $order_data, array(
			'user_id'           => 0,
			'staff_id'       	=> '',
			'product_id'        => '',
			'order_item_id'     => '',
			'summary' 			=> '',
			'cost'              => '',
			'start_date'        => '',
			'end_date'          => '',
			'all_day'           => 0,
			'parent_id'         => 0,
			'qty'				=> 1,
		) );

		// Get parent data
		if ( $order_data['parent_id'] ) {
			if ( ! $order_data['order_item_id'] ) {
				$order_data['order_item_id'] = get_post_meta( $order_data['parent_id'], '_appointment_order_item_id', true );
			}

			if ( ! $order_data['user_id'] ) {
				$order_data['user_id'] = get_post_meta( $order_data['parent_id'], '_appointment_customer_id', true );
			}
		}

		// Get order ID from order item
		if ( $order_data['order_item_id'] ) {
			$order_id = $wpdb->get_var( $wpdb->prepare( "SELECT order_id FROM {$wpdb->prefix}woocommerce_order_items WHERE order_item_id = %d", $order_data['order_item_id'] ) );
		} else {
			$order_id = 0;
		}

		$appointment_data = array(
			'post_type'   => 'wc_appointment',
			'post_title'  => sprintf( __( 'Appointment &ndash; %s', 'woocommerce-appointments' ), strftime( _x( '%b %d, %Y @ %I:%M %p', 'Appointment date parsed by strftime', 'woocommerce-appointments' ) ) ) . $order_data['summary'],
			'post_status' => $status,
			'ping_status' => 'closed',
			'post_parent' => $order_id,
		);
		
		//* Make sure staff is set as author
		if ( $order_data['staff_id'] ) {
			$appointment_data['post_author'] = absint( $order_data['staff_id'] );
		}

		$this->id = wp_insert_post( $appointment_data );

		// Setup the required data for the current user
		if ( ! $order_data['user_id'] ) {
			if ( is_user_logged_in() ) {
				$order_data['user_id'] = get_current_user_id();
			} else {
				$order_data['user_id'] = 0;
			}
		}

		// Convert appointment start and end to requried format
		if ( is_numeric( $order_data['start_date'] ) ) {
			// Convert timestamp
			$order_data['start_date'] = date( 'YmdHis', $order_data['start_date'] );
			$order_data['end_date']   = date( 'YmdHis', $order_data['end_date'] );
		} else {
			$order_data['start_date'] = date( 'YmdHis', strtotime( $order_data['start_date'] ) );
			$order_data['end_date']   = date( 'YmdHis', strtotime( $order_data['end_date'] ) );
		}

		$meta_args = array(
			'_appointment_order_item_id' => $order_data['order_item_id'],
			'_appointment_product_id'    => $order_data['product_id'],
			'_appointment_staff_id'   	 => $order_data['staff_id'],
			'_appointment_cost'          => $order_data['cost'],
			'_appointment_start'         => $order_data['start_date'],
			'_appointment_end'           => $order_data['end_date'],
			'_appointment_all_day'       => intval( $order_data['all_day'] ),
			'_appointment_parent_id'     => $order_data['parent_id'],
			'_appointment_customer_id'   => $order_data['user_id'],
			'_appointment_qty'   		 =>  absint( $order_data['qty'] ),
		);
		
		foreach ( $meta_args as $key => $value ) {
			update_post_meta( $this->id, $key, $value );
		}

		WC_Cache_Helper::get_transient_version( 'appointments', true );

		do_action( 'woocommerce_appointment_' . $status, $this->id );
		do_action( 'woocommerce_new_appointment', $this->id );
	}

	/**
	 * Assign this appointment to an order and order item by ID
	 * @param int $order_id
	 * @param int $order_item_id
	 */
	public function set_order_id( $order_id, $order_item_id ) {
		$this->order_id = $order_id;
		wp_update_post( array( 'ID' => $this->id, 'post_parent' => $this->order_id ) );
		update_post_meta( $this->id, '_appointment_order_item_id', $order_item_id );
	}

	/**
	 * Populate the data with the id of the appointment provided
	 * Will query for the post belonging to this appointment and store it
	 * @param int $appointment_id
	 */
	public function populate_data( $appointment_id ) {
		if ( ! isset( $this->post ) ) {
			$post = get_post( $appointment_id );
		}

		if ( is_object( $post ) ) {
			// We have the post object belonging to this appointment, now let's populate
			$this->id				= $post->ID;
			$this->appointment_date	= $post->post_date;
			$this->modified_date	= $post->post_modified;
			$this->customer_id		= $post->post_author;
			$this->custom_fields	= get_post_meta( $this->id );
			$this->status			= $post->post_status;
			$this->order_id			= $post->post_parent;

			// Define the data we're going to load: Key => Default value
			$load_data = array(
				'product_id'	=> '',
				'staff_id' 		=> '',
				'cost'			=> '',
				'start'			=> '',
				'customer_id'	=> '',
				'end'			=> '',
				'all_day'		=> 0,
				'parent_id'		=> 0,
				'qty'		    => 1,
			);

			// Load the data from the custom fields (with prefix for this plugin)
			$meta_prefix = '_appointment_';

			foreach ( $load_data as $key => $default ) {
				if ( isset( $this->custom_fields[ $meta_prefix . $key ][0] ) && $this->custom_fields[ $meta_prefix . $key ][0] !== '' ) {
					$this->$key = maybe_unserialize( $this->custom_fields[ $meta_prefix . $key ][0] );
				} else {
					$this->$key = $default;
				}
			}

			// Start and end date converted to timestamp
			$this->start = strtotime( $this->start );
			$this->end   = strtotime( $this->end );

			// Save the post object itself for future reference
			$this->post = $post;
			return true;
		}

		return false;
	}

	/**
	 * Will change the appointment status once the order is paid for
	 * @return bool
	 */
	public function paid() {
		$current_status = $this->status;
		$event          = wp_get_schedule( 'wc-appointment-reminder', array( $this->id ) );

		if ( $this->populated && in_array( $current_status, array( 'unpaid', 'confirmed' ) ) ) {
			$this->update_status( 'paid' );

			if ( ! empty( $event ) ) {
				$this->schedule_events();
			}

			return true;
		}

		return false;
	}

	/**
	 * Set the new status for this appointment
	 * @param string $status
	 * @return bool
	 */
	public function update_status( $status ) {
		$current_status   = $this->get_status( true );
		$allowed_statuses = array_unique( array_merge( get_wc_appointment_statuses(), get_wc_appointment_statuses( 'user' ), get_wc_appointment_statuses( 'cancel' ) ) );
		$allowed_statuses[] = 'was-in-cart';
		$allowed_statuses = array_values( $allowed_statuses );

		if ( $this->populated ) {
			if ( in_array( $status, $allowed_statuses ) ) {
				wp_update_post( array( 'ID' => $this->id, 'post_status' => $status ) );

				// Reschedule cron
				$this->schedule_events();

				// Trigger actions
				do_action( 'woocommerce_appointment_' . $current_status . '_to_' . $status, $this->id );
				do_action( 'woocommerce_appointment_' . $status, $this->id );

				// Note in the order
				if ( $order = $this->get_order() ) {
					$order->add_order_note( sprintf( __( 'Appointment #%d status changed from "%s" to "%s"', 'woocommerce-appointments' ), $this->id, $current_status, $status ) );
				}

				return true;
			}
		}

		return false;
	}

	/**
	 * Checks the appointment status against a passed in status.
	 *
	 * @return bool
	 */
	public function has_status( $status ) {
		return apply_filters( 'woocommerce_appointment_has_status', ( is_array( $status ) && in_array( $this->get_status(), $status ) ) || $this->get_status() === $status ? true : false, $this, $status );
	}

	/**
	 * Returns the status of this appointment
	 * @param Bool to ask for pretty status name (if false)
	 * @return String of the appointment status
	 */
	public function get_status( $raw = true ) {
		if ( $this->populated ) {
			if ( $raw ) {
				return $this->status;
			} else {
				$status_object = get_post_status_object( $this->status );
				return $status_object->label;
			}
		}

		return false;
	}

	/**
	 * Returns the id of this appointment
	 * @return Id of the appointment or false if appointment is not populated
	 */
	public function get_id() {
		if ( $this->populated ) {
			return $this->id;
		}

		return false;
	}

	/**
	 * Get the product ID for the appointment
	 * @return int or false if appointment is not populated
	 */
	public function get_product_id() {
		if ( $this->populated ) {
			return $this->product_id;
		}

		return false;
	}

	/**
	 * Returns the object of the order corresponding to this appointment
	 * @return Product object or false if appointment is not populated
	 */
	public function get_product() {
		if ( empty( $this->product ) ) {
			if ( $this->populated && $this->product_id ) {
				$this->product = get_product( $this->product_id );
			} else {
				return false;
			}
		}

		return $this->product;
	}

	/**
	 * Returns the object of the order corresponding to this appointment
	 * @return Order object or false if appointment is not populated
	 */
	public function get_order() {
		if ( empty( $this->order ) ) {
			if ( $this->populated && ! empty( $this->order_id ) && 'shop_order' === get_post_type( $this->order_id ) ) {
				$this->order = wc_get_order( $this->order_id );
			} else {
				return false;
			}
		}

		return $this->order;
	}

	/**
	 * Returns the cancel URL for a appointment
	 *
	 * @param string $redirect
	 * @return string
	 */
	public function get_cancel_url( $redirect = '' ) {
		$cancel_page = get_permalink( wc_get_page_id( 'myaccount' ) );

		if ( ! $cancel_page ) {
			$cancel_page = home_url();
		}

		return apply_filters( 'appointments_cancel_appointment_url', wp_nonce_url( add_query_arg( array( 'cancel_appointment' => 'true', 'appointment_id' => $this->id, 'redirect' => $redirect ), $cancel_page ), 'woocommerce-appointments-cancel_appointment' ) );
	}

	/**
	 * Return if all day event
	 * @return boolean
	 */
	public function is_all_day() {
		if ( $this->populated ) {
			if ( $this->all_day ) {
				return true;
			} else {
				return false;
			}
		}
		return false;
	}
	
	/**
	 * See if this appointment is within a block
	 * @return boolean
	 */
	public function is_within_slot( $slot_start, $slot_end ) {
		if ( $this->populated ) {
			if ( $this->start >= $slot_end ) {
				return false; // Appointment starts after block ends
			}

			if ( $this->end <= $slot_start ) {
				return false; // Appointment ends before block starts
			}

			return true;
		}
		return false;
	}

	/**
	 * See if this appointment is scheduled on said date
	 * @return boolean
	 */
	public function is_scheduled_on_day( $slot_start, $slot_end ) {
		if ( $this->populated ) {
			$loop_date        = $this->start;
			$multiday_appointment = date( 'Y-m-d', $this->start ) < date( 'Y-m-d', $this->end );

			if ( $multiday_appointment ) {
				if ( date( 'YmdHi', $slot_end ) > date( 'YmdHi', $this->start ) || date( 'YmdHi', $slot_start ) < date( 'YmdHi', $this->end ) ) {
					return true;
				}
				return false;
			}

			while ( $loop_date <= $this->end ) {
				if ( date( 'Y-m-d', $loop_date ) === date( 'Y-m-d', $slot_start ) ) {
					return true;
				}
				$loop_date = strtotime( "+1 day", $loop_date );
			}
		}
		return false;
	}

	/**
	 * See if this appointment can still be cancelled by the user or not
	 * @return boolean
	 */
	public function passed_cancel_day() {
		$appointment = $this->get_product();
		
		if ( ! $appointment || ! $appointment->can_be_cancelled() ) {
			return true;
		}

		if ( $appointment !== false ) {
			$cancel_limit      = $appointment->wc_appointment_cancel_limit;
			$cancel_limit_unit = $cancel_limit > 1 ? $appointment->wc_appointment_cancel_limit_unit . 's' : $appointment->wc_appointment_cancel_limit_unit;
			$cancel_string     = sprintf( '%s +%d %s', current_time( 'd F Y H:i:s' ), $cancel_limit, $cancel_limit_unit );

			if ( strtotime( $cancel_string ) >= $this->start ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Returns appointment start date
	 * @return string Date formatted via date_i18n
	 */
	public function get_start_date( $date_format = null, $time_format = null ) {
		if ( $this->populated && ! empty( $this->start ) ) {
			if ( is_null( $date_format ) ) {
				$date_format = apply_filters( 'woocommerce_appointments_date_format', wc_date_format() );
			}
			if ( is_null( $time_format ) ) {
				$time_format = apply_filters( 'woocommerce_appointments_time_format', ', ' . wc_time_format() );
			}
			if ( $this->is_all_day() ) {
				return date_i18n( $date_format, $this->start );
			} else {
				return apply_filters( 'woocommerce_appointments_get_start_date_with_time', date_i18n( $date_format . $time_format, $this->start ), $this );
			}
		}

		return false;
	}

	/**
	 * Returns appointment end date
	 * @return string Date formatted via date_i18n
	 */
	public function get_end_date( $date_format = null, $time_format = null ) {
		if ( $this->populated && ! empty( $this->end ) ) {
			if ( is_null( $date_format ) ) {
				$date_format = apply_filters( 'woocommerce_appointments_date_format', 'M jS Y' );
			}
			if ( is_null( $time_format ) ) {
				$time_format = apply_filters( 'woocommerce_appointments_time_format', ', g:ia' );
			}
			if ( $this->is_all_day() ) {
				return date_i18n( $date_format, $this->end );
			} else {
				return apply_filters( 'woocommerce_appointments_get_end_date_with_time', date_i18n( $date_format . $time_format, $this->end ), $this );
			}
		}

		return false;
	}

	/**
	 * Returns information about the customer of this order
	 * @return array containing customer information
	 */
	public function get_customer() {
		if ( $this->populated ) {
			$order			= $this->get_order();
			
			if ( $order ) {
				$user_id     = absint( $order->customer_user );
				$user        = get_user_by( 'id', $user_id );
				
				if ( is_object( $user ) ) {
					$user_string = esc_html( $user->display_name ) . ' (#' . absint( $user->ID ) . ' &ndash; ' . esc_html( $user->user_email ) . ')';
				
					return (object) array(
						'name'    => $user_string,
						'email'   => esc_html( $user->user_email ),
						'user_id' => absint( $user->ID ),
					);
				} else {
					return (object) array(
						'name'    => trim( $order->billing_first_name . ' ' . $order->billing_last_name ),
						'email'   => $order->billing_email,
						'user_id' => $user_id,
					);
				}
				
			} elseif ( $this->customer_id ) {
				$user_id     = absint( $this->customer_id );
				$user        = get_user_by( 'id', $user_id );
				$user_string = esc_html( $user->display_name ) . ' (#' . absint( $user->ID ) . ' &ndash; ' . esc_html( $user->user_email ) . ')';

				return (object) array(
					'name'    => $user_string,
					'email'   => esc_html( $user->user_email ),
					'user_id' => absint( $user->ID ),
				);
			}
			
		}

		return false;
	}

	/**
	 * Returns if staff are enabled/needed for the appointment product
	 * @return boolean
	 */
	public function has_staff() {
		return $this->get_product()->has_staff();
	}

	/**
	 * Get the staff id
	 * @return int
	 */
	public function get_staff_id() {
		if ( $this->populated ) {
			return absint( $this->staff_id );
		}
		return 0;
	}

	/**
	 * Get the staff/type for this appointment if applicable.
	 * @return bool|object WP_Post
	 */
	public function get_staff_member() {
		$staff_id = $this->get_staff_id();

		if ( ! $staff_id || ! ( $product = $this->get_product() ) || ! method_exists( $product, 'get_staff' ) ) {
			return false;
		}

		return $product->get_staff_member( $staff_id );
	}
}
