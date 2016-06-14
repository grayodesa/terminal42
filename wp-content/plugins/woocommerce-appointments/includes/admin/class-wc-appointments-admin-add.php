<?php
/**
 * Create new appointments page
 */
class WC_Appointments_Admin_Add {

	private $errors = array();

	/**
	 * Output the form
	 */
	public function output() {
		$this->errors = array();
		$step         = 1;

		try {

			if ( ! empty( $_POST ) && ! check_admin_referer( 'add_appointment_notification' ) ) {
				throw new Exception( __( 'Error - please try again', 'woocommerce-appointments' ) );
			}

			if ( ! empty( $_POST['add_appointment'] ) ) {

				$customer_id			= absint( $_POST['customer_id'] );
				$appointable_product_id = absint( $_POST['appointable_product_id'] );
				$appointment_order		= wc_clean( $_POST['appointment_order'] );

				if ( ! $appointable_product_id ) {
					throw new Exception( __( 'Please choose an appointable product', 'woocommerce-appointments' ) );
				}

				if ( $appointment_order === 'existing' ) {
					$order_id			= absint( $_POST['appointment_order_id'] );
					$appointment_order	= $order_id;

					if ( ! $appointment_order || get_post_type( $appointment_order ) !== 'shop_order' ) {
						throw new Exception( __( 'Invalid order ID provided', 'woocommerce-appointments' ) );
					}
				}

				$step++;
				$product				= get_product( $appointable_product_id );
				$appointment_form		= new WC_Appointment_Form( $product );

			} elseif ( ! empty( $_POST['add_appointment_2'] ) ) {

				$customer_id			= absint( $_POST['customer_id'] );
				$appointable_product_id = absint( $_POST['appointable_product_id'] );
				$appointment_order		= wc_clean( $_POST['appointment_order'] );
				$product				= get_product( $appointable_product_id );
				$appointment_form		= new WC_Appointment_Form( $product );
				$appointment_data		= $appointment_form->get_posted_data( $_POST );
				$appointment_cost		= ( $cost = $appointment_form->calculate_appointment_cost( $_POST ) ) && ! is_wp_error( $cost ) ? number_format( $cost, 2, '.', '' ) : 0;
				$create_order			= false;

				if ( 'yes' === get_option( 'woocommerce_prices_include_tax' ) ) {
					if ( version_compare( WOOCOMMERCE_VERSION, '2.3', '<' ) ) {
						$base_tax_rates = WC_Tax::get_shop_base_rate( $product->tax_class );
					} else {
						$base_tax_rates = WC_Tax::get_base_tax_rates( $product->tax_class );
					}
					$base_taxes			= WC_Tax::calc_tax( $appointment_cost, $base_tax_rates, true );
					$appointment_cost   = round( $appointment_cost - array_sum( $base_taxes ), absint( get_option( 'woocommerce_price_num_decimals' ) ) );
				}

				// Data to go into the appointment
				$new_appointment_data = array(
					'user_id'		=> $customer_id,
					'product_id'	=> $product->id,
					'staff_id'		=> isset( $appointment_data['_staff_id'] ) ? $appointment_data['_staff_id'] : '',
					'cost'			=> $appointment_cost,
					'start_date'	=> $appointment_data['_start_date'],
					'end_date'		=> $appointment_data['_end_date'],
					'all_day'		=> $appointment_data['_all_day'] ? 1 : 0,
					'qty'		    => $appointment_data['_qty'] ? $appointment_data['_qty'] : 1,
				);

				// Create order
				if ( $appointment_order === 'new' ) {
					$create_order = true;
					$order_id     = $this->create_order( $appointment_cost, $customer_id );

					if ( ! $order_id ) {
						throw new Exception( __( 'Error: Could not create order', 'woocommerce-appointments' ) );
					}
				} elseif ( $appointment_order > 0 ) {
					$order_id = absint( $appointment_order );

					if ( ! $order_id || get_post_type( $order_id ) !== 'shop_order' ) {
						throw new Exception( __( 'Invalid order ID provided', 'woocommerce-appointments' ) );
					}

					$order = wc_get_order( $order_id );

					update_post_meta( $order_id, '_order_total', $order->get_total() + $appointment_cost );
					update_post_meta( $order_id, '_appointment_order', '1' );				
				} else {
					$order_id = 0;
				}

				if ( $order_id ) {
		           	$item_id  = wc_add_order_item( $order_id, array(
				 		'order_item_name' 		=> $product->get_title(),
				 		'order_item_type' 		=> 'line_item'
				 	) );

				 	if ( ! $item_id ) {
						throw new Exception( __( 'Error: Could not create item', 'woocommerce-appointments' ) );
				 	}

				 	// Add line item meta
				 	wc_add_order_item_meta( $item_id, '_qty', $appointment_data['_qty'] ? $appointment_data['_qty'] : 1 );
				 	wc_add_order_item_meta( $item_id, '_tax_class', $product->get_tax_class() );
				 	wc_add_order_item_meta( $item_id, '_product_id', $product->id );
				 	wc_add_order_item_meta( $item_id, '_variation_id', '' );
				 	wc_add_order_item_meta( $item_id, '_line_subtotal', $appointment_cost );
				 	wc_add_order_item_meta( $item_id, '_line_total', $appointment_cost );
				 	wc_add_order_item_meta( $item_id, '_line_tax', 0 );
				 	wc_add_order_item_meta( $item_id, '_line_subtotal_tax', 0 );

				 	// We have an item id
					$new_appointment_data['order_item_id'] = $item_id;

					// Add line item data
					foreach ( $appointment_data as $key => $value ) {
						if ( strpos( $key, '_' ) !== 0 ) {
							wc_add_order_item_meta( $item_id, get_wc_appointment_data_label( $key, $product ), $value );
						}
					}
					
					do_action( 'woocommerce_appointments_create_appointment_page_add_order_item', $order_id, $item_id, $product );
				}

				// Create the appointment itself
				$new_appointment = get_wc_appointment( $new_appointment_data );
				$new_appointment->create( $create_order ? 'unpaid' : 'confirmed' );

				wp_safe_redirect( admin_url( 'post.php?post=' . ( $create_order ? $order_id : $new_appointment->id ) . '&action=edit' ) );
				exit;

			}
		} catch ( Exception $e ) {
			$this->errors[] = $e->getMessage();
		}

		switch ( $step ) {
			case 1 :
				include( 'views/html-add-appointment-page.php' );
			break;
			case 2 :
				include( 'views/html-add-appointment-page-2.php' );
			break;
		}
	}

	/**
	 * Create order
	 * @param  float $total
	 * @param  int $customer_id
	 * @return int
	 */
	public function create_order( $total, $customer_id ) {
		if ( function_exists( 'wc_create_order' ) ) {
			$order = wc_create_order( array(
				'customer_id' => absint( $customer_id )
			) );
			$order_id = $order->id;
			$order->set_total( $total );
			update_post_meta( $order->id, '_appointment_order', '1' );
		} else {
			$order_data = apply_filters( 'woocommerce_new_order_data', array(
				'post_type' 	=> 'shop_order',
				'post_title' 	=> sprintf( __( 'Order &ndash; %s', 'woocommerce-appointments' ), strftime( _x( '%b %d, %Y @ %I:%M %p', 'Order date parsed by strftime', 'woocommerce-appointments' ) ) ),
				'post_status' 	=> 'publish',
				'ping_status'	=> 'closed',
				'post_excerpt' 	=> '',
				'post_author' 	=> 1,
				'post_password'	=> uniqid( 'order_' )	// Protects the post just in case
			) );

			$order_id = wp_insert_post( $order_data, true );

			update_post_meta( $order_id, '_order_shipping', 0 );
			update_post_meta( $order_id, '_order_discount', 0 );
			update_post_meta( $order_id, '_cart_discount', 0 );
			update_post_meta( $order_id, '_order_tax', 0 );
			update_post_meta( $order_id, '_order_shipping_tax', 0 );
			update_post_meta( $order_id, '_order_total', $total );
			update_post_meta( $order_id, '_order_key', apply_filters('woocommerce_generate_order_key', uniqid('order_') ) );
			update_post_meta( $order_id, '_customer_user', absint( $customer_id ) );
			update_post_meta( $order_id, '_order_currency', get_woocommerce_currency() );
			update_post_meta( $order_id, '_prices_include_tax', get_option( 'woocommerce_prices_include_tax' ) );
			update_post_meta( $order_id, '_appointment_order', '1' );
			wp_set_object_terms( $order_id, 'pending', 'shop_order_status' );
		}

		do_action( 'woocommerce_new_appointment_order', $order_id );

		return $order_id;
	}

	/**
	 * Output any errors
	 */
	public function show_errors() {
		foreach ( $this->errors as $error )
			echo '<div class="error"><p>' . esc_html( $error ) . '</p></div>';
	}
}
