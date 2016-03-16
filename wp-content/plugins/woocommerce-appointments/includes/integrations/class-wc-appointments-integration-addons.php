<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Addons integration class.
 */
class WC_Appointments_Integration_Addons {

	/**
	 * Constructor
	 */
	public function __construct() {
		add_filter( 'woocommerce_product_addons_show_grand_total', array( $this, 'addons_show_grand_total' ), 20, 2 );
		add_action( 'woocommerce_product_addons_panel_head_option', array( $this, 'addon_option_head' ), 10, 3 );
		add_action( 'woocommerce_product_addons_panel_body_option', array( $this, 'addon_option_body' ), 10, 4 );
		add_filter( 'woocommerce_product_addons_save_data', array( $this, 'save_addon_options' ), 20, 2 );
		add_action( 'woocommerce_appointments_create_appointment_page_add_order_item', array( $this, 'save_addon_options_in_admin' ), 10, 3 );
		add_filter( 'woocommerce_product_addons_adjust_price', array( $this, 'adjust_price' ), 20, 2 );
		add_filter( 'appointment_form_calculated_appointment_cost', array( $this, 'adjust_appointment_cost' ), 10, 3 );
		add_filter( 'woocommerce_product_addon_cart_item_data', array( $this, 'adjust_appointment_cart_data' ), 10, 4 );
		add_filter( 'appointment_form_posted_total_duration', array( $this, 'adjust_appointment_duration' ), 10, 3 );
	}
	
	/**
	 * Show grand total or not?
	 * @param  bool $show_grand_total
	 * @param  object $product
	 * @return bool
	 */
	public function addons_show_grand_total( $show_grand_total, $product ) {
		if ( $product->is_type( 'appointment' ) ) {
			$show_grand_total = false;
		}
		return $show_grand_total;
	}
	
	/**
	 * Show option head
	 */
	public function addon_option_head( $post, $addon, $loop ) {
		$product = get_product( $post->ID );
		$css_classes = 'duration_column show_if_appointment';
		if ( 'appointment' !== $product->product_type ) {
			$css_classes .= ' hide_initial_appointment_addon_options';
		}
		?>
		<th class="<?php echo esc_attr( $css_classes ); ?>"><?php _e( 'Duration (minutes)', 'woocommerce-appointments' ); ?></th>
		<?php
	}
	
	/**
	 * Show option body
	 */
	public function addon_option_body( $post, $addon, $loop = 0, $option = array() ) {
		$product = get_product( $post->ID );
		$css_classes = 'duration_column show_if_appointment';
		if ( 'appointment' !== $product->product_type ) {
			$css_classes .= ' hide_initial_appointment_addon_options';
		}
		?>
		<td class="<?php echo esc_attr( $css_classes ); ?>"><input type="number" name="product_addon_option_duration[<?php echo $loop; ?>][]" value="<?php echo ( isset( $option['duration'] ) ) ? esc_attr( $option['duration'] ) : ''; ?>" placeholder="N/A" min="0" step="any" /></td>
		<?php
	}

	/**
	 * Save options
	 */
	public function save_addon_options( $data, $i ) {
		$addon_option_duration = $_POST['product_addon_option_duration'][ $i ];
		$addon_option_label = $_POST['product_addon_option_label'][ $i ];
				
		for ( $ii = 0; $ii < sizeof( $addon_option_label ); $ii++ ) {
			$duration = sanitize_text_field( stripslashes( $addon_option_duration[ $ii ] ) );
			$data['options'][ $ii ]['duration'] = $duration;
		}

		return $data;
	}
	
	/**
	 * Save options in admin
	 */
	public function save_addon_options_in_admin( $order_id, $item_id, $product ) {		
		if ( ! $item_id ) {
			throw new Exception( __( 'Error: Could not create item', 'woocommerce-appointments' ) );
		}
					
		$addons = $GLOBALS['Product_Addon_Cart']->add_cart_item_data( '', $product->id, $_POST, true );
		
		if ( ! empty( $addons['addons'] ) ) {
			foreach ( $addons['addons'] as $addon ) {

				$name = $addon['name'];

				if ( $addon['price'] > 0 && apply_filters( 'woocommerce_addons_add_price_to_name', true ) ) {
					$name .= ' (' . strip_tags( wc_price( get_product_addon_price_for_display ( $addon['price'] ) ) ) . ')';
				}

				wc_add_order_item_meta( $item_id, $name, $addon['value'] );
			}
		}
	}

	/**
	 * Don't adjust price for appointments since the appointment form class adds the costs itself
	 * @return bool
	 */
	public function adjust_price( $bool, $cart_item ) {
		if ( $cart_item['data']->is_type( 'appointment' ) ) {
			return false;
		}
		return $bool;
	}

	/**
	 * Adjust the final appointment cost
	 */	
	public function adjust_appointment_cost( $appointment_cost, $appointment_form, $posted ) {
		//* Product add-ons
		$addons       = $GLOBALS['Product_Addon_Cart']->add_cart_item_data( array(), $appointment_form->product->id, $posted, true );
		$addon_costs  = 0;
		$appointment_data = $appointment_form->get_posted_data( $posted );

		if ( ! empty( $addons['addons'] ) ) {
			foreach ( $addons['addons'] as $addon ) {
				$addon_cost = 0;
				if ( ! empty( $appointment_data['_qty'] ) ) {
					$addon_cost += $addon['price'] * $appointment_data['_qty'];
				}
				if ( ! $addon_cost ) {
					$addon_cost += $addon['price'];
				}
				$addon_costs += $addon_cost;
			}
		}

		return $appointment_cost + $addon_costs;
	}
	
	/**
	 * Adjust the final appointment cart item data.
	 * 
	 * Insert missing duration data.
	 */	
	public function adjust_appointment_cart_data( $data, $addon, $product_id, $post_data ) {	
		//* Modify default data array
		$data_array = array();
		foreach ( $data as $data_key => $data_value ) {
			$data_array[ $data_key ] = $data_value;
			
			foreach ( $addon['options'] as $addon_key => $addon_value ) {
				if ( $data_value['value'] === $addon_value['label'] && $data_value['price'] === $addon_value['price'] && isset( $addon_value['duration'] ) ) {
					$data_array[ $data_key ]['duration'] = $addon_value['duration'];
				}
			}

		}
		
		return $data_array;
	}
	
	/**
	 * Adjust the final appointment duration
	 */	
	public function adjust_appointment_duration( $appointment_duration, $appointment_form, $posted ) {		
		if ( in_array( $appointment_form->product->get_duration_unit(), array( 'day' ) ) ) {
			return $appointment_duration;
		}
		
		//* Product add-ons
		$addons       	= $GLOBALS['Product_Addon_Cart']->add_cart_item_data( array(), $appointment_form->product->id, $posted, true );
		$addon_duration	= 0;

		if ( ! empty( $addons['addons'] ) ) {
			foreach ( $addons['addons'] as $addon ) {
				$addon_mins = 0;				
				if ( ! empty( $addon['duration'] ) ) {
					$addon_mins += $addon['duration'];
				}
				$addon_duration += $addon_mins;
			}
		}

		return $appointment_duration + $addon_duration;
	}
}

$GLOBALS['wc_appointments_integration_addons'] = new WC_Appointments_Integration_Addons();