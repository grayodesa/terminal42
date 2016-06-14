<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * WC_Appointment_Cart_Manager class.
 */
class WC_Appointment_Cart_Manager {

	/**
	 * The class id used for identification in logging.
	 *
	 * @var $id
	 */
	public $id;
	
	/**
	 * Constructor
	 */
	public function __construct() {
		add_action( 'woocommerce_appointment_add_to_cart', array( $this, 'add_to_cart' ), 30 );
		add_filter( 'woocommerce_add_to_cart_validation', array( $this, 'validate_add_cart_item' ), 10, 3 );
		add_filter( 'woocommerce_cart_item_quantity', array( $this, 'cart_item_quantity' ), 10, 3 );
		add_filter( 'woocommerce_add_cart_item', array( $this, 'add_cart_item' ), 10, 1 );
		add_filter( 'woocommerce_get_cart_item_from_session', array( $this, 'get_cart_item_from_session' ), 10, 3 );
		add_action( 'woocommerce_cart_loaded_from_session', array( $this, 'cart_loaded_from_session' ), 10, 3 );
		add_action( 'woocommerce_before_cart_item_quantity_zero', array( $this, 'before_cart_item_quantity_zero' ), 10, 1 );
		add_filter( 'woocommerce_get_item_data', array( $this, 'get_item_data' ), 10, 2 );
		add_filter( 'woocommerce_add_cart_item_data', array( $this, 'add_cart_item_data' ), 10, 2 );
		add_action( 'woocommerce_add_order_item_meta', array( $this, 'order_item_meta' ), 50, 2 );
		add_filter( 'woocommerce_add_to_cart_validation', array( $this, 'validate_appointment_requires_confirmation' ), 20, 2 );
		add_action( 'woocommerce_cart_item_removed', array( $this, 'cart_item_removed' ), 20 );
		add_action( 'woocommerce_cart_item_restored', array( $this, 'cart_item_restored' ), 20 );

		if ( get_option( 'woocommerce_cart_redirect_after_add' ) === 'yes' ) {
			if ( version_compare( WC_VERSION, '2.3', '<' ) ) {
				add_filter( 'add_to_cart_redirect', array( $this, 'add_to_cart_redirect' ) );
			} else {
				add_filter( 'woocommerce_add_to_cart_redirect', array( $this, 'add_to_cart_redirect' ) );
			}
		}
		
		$this->id = 'wc_appointment_cart_manager';

		// Active logs.
		if ( class_exists( 'WC_Logger' ) ) {
			$this->log = new WC_Logger();
		}

	}

	/**
	 * Add to cart for appointments
	 */
	public function add_to_cart() {
		global $product;

		// Prepare form
		$appointment_form = new WC_Appointment_Form( $product );

		// Get template
		wc_get_template( 'single-product/add-to-cart/appointment.php', array( 'appointment_form' => $appointment_form ), 'woocommerce-appointments', WC_APPOINTMENTS_TEMPLATE_PATH );
	}

	/**
	 * When a appointment is added to the cart, validate it
	 *
	 * @param mixed $passed
	 * @param mixed $product_id
	 * @param mixed $qty
	 * @return bool
	 */
	public function validate_add_cart_item( $passed, $product_id, $qty ) {
		$product = get_product( $product_id );

		if ( ! is_wc_appointment_product( $product ) ) {
			return $passed;
		}

		$appointment_form = new WC_Appointment_Form( $product );
		$data         = $appointment_form->get_posted_data();
		$validate     = $appointment_form->is_appointable( $data );

		if ( is_wp_error( $validate ) ) {
			wc_add_notice( $validate->get_error_message(), 'error' );
			return false;
		}

		return $passed;
	}
	
	/**
	 * Make appointment quantity in cart readonly
	 *
	 * @param mixed $product_quantity
	 * @param mixed $cart_item_key
	 * @param mixed $cart_item
	 * @return string
	 */
	public function cart_item_quantity( $product_quantity, $cart_item_key, $cart_item = array() ) {
		if ( ! empty( $cart_item['appointment'] ) && ! empty( $cart_item['appointment']['_qty'] ) ) {
			$product_quantity = sprintf( '%1$s <input type="hidden" name="cart[%2$s][qty]" value="%1$s" />', $cart_item['quantity'], $cart_item_key );
		}
		return $product_quantity;
	}

	/**
	 * Adjust the price of the appointment product based on appointment properties
	 *
	 * @param mixed $cart_item
	 * @return array cart item
	 */
	public function add_cart_item( $cart_item ) {
		if ( ! empty( $cart_item['appointment'] ) && ! empty( $cart_item['appointment']['_cost'] ) ) {
			$cart_item['data']->set_price( $cart_item['appointment']['_cost'] / $cart_item['appointment']['_qty'] );
		}
		return $cart_item;
	}

	/**
	 * Get data from the session and add to the cart item's meta
	 *
	 * @param mixed $cart_item
	 * @param mixed $values
	 * @return array cart item
	 */
	public function get_cart_item_from_session( $cart_item, $values, $cart_item_key ) {
		if ( ! empty( $values['appointment'] ) ) {
			$cart_item['appointment'] 	= $values['appointment'];
			$cart_item            		= $this->add_cart_item( $cart_item );
		}
		return $cart_item;
	}

	/**
	 * Before delete
	 */
	public function before_cart_item_quantity_zero( $cart_item_key ) {
		$cart       = WC()->cart->get_cart();
		$cart_item  = $cart[ $cart_item_key ];
		$appointment_id = isset( $cart_item['appointment'] ) && ! empty( $cart_item['appointment']['_appointment_id'] ) ? absint( $cart_item['appointment']['_appointment_id'] ) : '';

		if ( $appointment_id ) {
			$appointment = get_wc_appointment( $appointment_id );
			if ( $appointment->has_status( array( 'was-in-cart', 'in-cart' ) ) ) {
				wp_delete_post( $appointment_id );
				wp_clear_scheduled_hook( 'wc-appointment-remove-inactive-cart', array( $appointment_id ) );
			}
		}
	}

	/**
	 * Before delete
	 *
	 * @param string $cart_item_key identifying which item in cart.
	 */
	public function cart_item_removed( $cart_item_key ) {
		$cart_item = WC()->cart->removed_cart_contents[ $cart_item_key ];

		if ( isset( $cart_item['appointment'] ) ) {
			$appointment_id = $cart_item['appointment']['_appointment_id'];
			$appointment    = get_wc_appointment( $appointment_id );
			if ( $appointment->has_status( 'in-cart' ) ) {
				$appointment->update_status( 'was-in-cart' );
				WC_Cache_Helper::get_transient_version( 'appointments', true );
				wp_clear_scheduled_hook( 'wc-appointment-remove-inactive-cart', array( $appointment_id ) );

				if ( isset( $this->log ) ) {
					$message = sprintf( 'Appointment ID: %s removed from cart by user ID: %s ', $appointment->id, get_current_user_id() );
					$this->log->add( $this->id, $message );
				}
			}
		}
	}

	/**
	 * Restore item
	 *
	 * @param string $cart_item_key identifying which item in cart.
	 */
	public function cart_item_restored( $cart_item_key ) {
		$cart      = WC()->cart->get_cart();
		$cart_item = $cart[ $cart_item_key ];

		if ( isset( $cart_item['appointment'] ) ) {
			$appointment_id = $cart_item['appointment']['_appointment_id'];
			$appointment    = get_wc_appointment( $appointment_id );
			if ( $appointment->has_status( 'was-in-cart' ) ) {
				$appointment->update_status( 'in-cart' );
				WC_Cache_Helper::get_transient_version( 'appointments', true );
				$this->schedule_cart_removal( $appointment_id );

				if ( isset( $this->log ) ) {
					$message = sprintf( 'Appointment ID: %s was restored to cart by user ID: %s ', $appointment->id, get_current_user_id() );
					$this->log->add( $this->id, $message );
				}
			}
		}
	}

	/**
	 * Schedule appointment to be deleted if inactive
	 */
	public function schedule_cart_removal( $appointment_id ) {
		wp_clear_scheduled_hook( 'wc-appointment-remove-inactive-cart', array( $appointment_id ) );
		wp_schedule_single_event( apply_filters( 'woocommerce_appointments_remove_inactive_cart_time', time() + ( 60 * 15 ) ), 'wc-appointment-remove-inactive-cart', array( $appointment_id ) );
	
	}

	/**
	 * Check for invalid appointments
	 */
	public function cart_loaded_from_session() {
		foreach( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
			if ( isset( $cart_item['appointment'] ) ) {
				// If the appointment is gone, remove from cart!
				$appointment_id = $cart_item['appointment']['_appointment_id'];
				$appointment    = get_wc_appointment( $appointment_id );

				if ( ! $appointment || ! $appointment->has_status( array( 'was-in-cart', 'in-cart', 'unpaid', 'paid' ) ) ) {
					unset( WC()->cart->cart_contents[ $cart_item_key ] );

					WC()->cart->calculate_totals();

					wc_add_notice( sprintf( __( 'A appointment for %s has been removed from your cart due to inactivity.', 'woocommerce-appointments' ), '<a href="' . get_permalink( $cart_item['product_id'] ) . '">' . get_the_title( $cart_item['product_id'] ) . '</a>' ), 'notice' );
				} elseif ( $appointment->has_status( 'in-cart' ) ) {
					$this->schedule_cart_removal( $cart_item['appointment']['_appointment_id'] );
				}
			}
		}
	}

	/**
	 * Add posted data to the cart item
	 *
	 * @param mixed $cart_item_meta
	 * @param mixed $product_id
	 * @return void
	 */
	public function add_cart_item_data( $cart_item_meta, $product_id ) {
		$product = get_product( $product_id );

		if ( ! is_wc_appointment_product( $product ) ) {
			return $cart_item_meta;
		}

		$appointment_form                       = new WC_Appointment_Form( $product );
		$cart_item_meta['appointment']          = $appointment_form->get_posted_data( $_POST );
		$cart_item_meta['appointment']['_cost'] = $appointment_form->calculate_appointment_cost( $_POST );

		// Create the new appointment
		$new_appointment = $this->add_appointment_from_cart_data( $cart_item_meta, $product_id );

		// Store in cart
		$cart_item_meta['appointment']['_appointment_id'] = $new_appointment->id;

		// Schedule this item to be removed from the cart if the user is inactive
		$this->schedule_cart_removal( $new_appointment->id );

		return $cart_item_meta;
	}

	/**
	 * Create appointment from cart data
	 */
	private function add_appointment_from_cart_data( $cart_item_meta, $product_id, $status = 'in-cart' ) {
		// Create the new appointment
		$new_appointment_data = array(
			'product_id'    => $product_id, // Appointment ID
			'cost'          => $cart_item_meta['appointment']['_cost'], // Cost of this appointment
			'start_date'    => $cart_item_meta['appointment']['_start_date'],
			'end_date'      => $cart_item_meta['appointment']['_end_date'],
			'all_day'       => $cart_item_meta['appointment']['_all_day'],
			'qty'       	=> $cart_item_meta['appointment']['_qty'],
		);

		// Check if the appointment has staff
		if ( isset( $cart_item_meta['appointment']['_staff_id'] ) ) {
			$new_appointment_data['staff_id'] = $cart_item_meta['appointment']['_staff_id']; // ID of the staff
		}

		$new_appointment = get_wc_appointment( $new_appointment_data );
		$new_appointment->create( $status );

		return $new_appointment;
	}

	/**
	 * Put meta data into format which can be displayed
	 *
	 * @param mixed $other_data
	 * @param mixed $cart_item
	 * @return array meta
	 */
	public function get_item_data( $other_data, $cart_item ) {
		if ( ! empty( $cart_item['appointment'] ) ) {
			foreach ( $cart_item['appointment'] as $key => $value ) {
				if ( substr( $key, 0, 1 ) !== '_' ) {
					$other_data[] = array(
						'name'    => get_wc_appointment_data_label( $key, $cart_item['data'] ),
						'value'   => $value,
						'display' => ''
					);
				}
			}
		}
		return $other_data;
	}

	/**
	 * order_item_meta function.
	 *
	 * @param mixed $item_id
	 * @param mixed $values
	 */
	public function order_item_meta( $item_id, $values ) {
		global $wpdb;

		if ( ! empty( $values['appointment'] ) ) {
			$product        	= $values['data'];
			$appointment_id     = $values['appointment']['_appointment_id'];
			$appointment        = get_wc_appointment( $appointment_id );
			$appointment_status = 'unpaid';
			$order_id       = $wpdb->get_var( $wpdb->prepare( "SELECT order_id FROM {$wpdb->prefix}woocommerce_order_items WHERE order_item_id = %d", $item_id ) );

			// Set as pending when the appointment requires confirmation
			if ( wc_appointment_requires_confirmation( $values['product_id'] ) ) {
				$appointment_status = 'pending-confirmation';
			}

			$appointment->set_order_id( $order_id, $item_id );
			
			// Add appointment ID
			wc_add_order_item_meta( $item_id, __( 'Appointment ID', 'woocommerce-appointments' ), $appointment_id );

			// Add summary of details to line item
			foreach ( $values['appointment'] as $key => $value ) {
				if ( strpos( $key, '_' ) !== 0 ) {
					wc_add_order_item_meta( $item_id, get_wc_appointment_data_label( $key, $product ), $value );
				}
			}
			
			$appointment_status = apply_filters( 'woocommerce_appointments_order_item_status', $appointment_status, $appointment, $product, $order_id );

			// Update status
			$appointment->update_status( $appointment_status );
		}
	}

	/**
	 * Redirects directly to the cart the products they need confirmation
	 *
	 * @param string $url
	 */
	public function add_to_cart_redirect( $url ) {
		if ( isset( $_REQUEST['add-to-cart'] ) && is_numeric( $_REQUEST['add-to-cart'] ) && wc_appointment_requires_confirmation( intval( $_REQUEST['add-to-cart'] ) ) ) {
			// Remove add to cart messages
			wc_clear_notices();

			// Go to checkout
			return WC()->cart->get_cart_url();
		}

		return $url;
	}

	/**
	 * Remove all appointments that require confirmation.
	 *
	 * @return void
	 */
	protected function remove_appointment_that_requires_confirmation() {
		foreach( WC()->cart->cart_contents as $item_key => $item ) {
			if ( wc_appointment_requires_confirmation( $item['product_id'] ) ) {
				WC()->cart->set_quantity( $item_key, 0 );
			}
		}
	}

	/**
	 * Removes all products when cart have a appointment which requires confirmation
	 *
	 * @param  bool $passed
	 * @param  int  $product_id
	 *
	 * @return bool
	 */
	public function validate_appointment_requires_confirmation( $passed, $product_id ) {
		if ( wc_appointment_requires_confirmation( $product_id ) ) {
			
			$items = WC()->cart->get_cart();

			foreach ( $items as $item_key => $item ) {
				if ( ! isset( $item['appointment'] ) || ! wc_appointment_requires_confirmation( $item['product_id'] ) ) {
					WC()->cart->remove_cart_item( $item_key );
				}
			}

		} elseif ( wc_appointment_cart_requires_confirmation() ) {
			// Remove appointment that requires confirmation.
			$this->remove_appointment_that_requires_confirmation();

			wc_add_notice( __( 'A appointment that requires confirmation has been removed from your cart. It is not possible to complete the purchased along with a appointment that doesn\'t require confirmation.', 'woocommerce-appointments' ), 'notice' );
		}

		return $passed;
	}
}

$GLOBALS['wc_appointment_cart_manager'] = new WC_Appointment_Cart_Manager();