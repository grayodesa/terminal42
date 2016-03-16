<?php
/**
 * WC_Appointments_Shortcodes class.
 *
 * @class 		WC_Appointments_Shortcodes
 * @version		1.2.4
 */
class WC_Appointments_Shortcodes {

	/**
	 * Init shortcodes
	 */
	public static function init() {
		$shortcodes = array(
			'appointment_form'           => __CLASS__ . '::appointment_form',
		);

		foreach ( $shortcodes as $shortcode => $function ) {
			add_shortcode( apply_filters( "{$shortcode}_shortcode_tag", $shortcode ), $function );
		}
	}

	/**
	 * @param array $atts
	 * @return string
	 */
	public static function appointment_form( $atts ) {
		if ( empty( $atts ) ) {
			return '';
		}

		if ( ! isset( $atts['id'] ) && ! isset( $atts['sku'] ) ) {
			return '';
		}
		
		//* Attributes
		$atts = shortcode_atts( array(
			'id'					=> '',
			'sku'					=> '',
			'show_title'			=> 1,
			'show_rating'			=> 1,
			'show_price'			=> 1,
			'show_excerpt'			=> 1,
			'show_meta'				=> 1,
			'show_sharing'			=> 1
		), $atts );

		//* Query arguments
		$args = array(
			'posts_per_page'		=> 1,
			'post_type'				=> 'product',
			'post_status'			=> 'publish',
			'ignore_sticky_posts'	=> 1,
			'no_found_rows'			=> 1
		);

		//* SKU
		if ( isset( $atts['sku'] ) ) {
			$args['meta_query'][] = array(
				'key'     => '_sku',
				'value'   => sanitize_text_field( $atts['sku'] ),
				'compare' => '='
			);

			$args['post_type'] = array( 'product', 'product_variation' );
		}

		//* ID
		if ( isset( $atts['id'] ) ) {
			$args['p'] = absint( $atts['id'] );
		}

		$single_product = new WP_Query( $args );
		
		//* Get product object
		$single_product_obj  = get_product( $single_product->post->ID );
		
		//* Prepare appointment form
		$appointment_form = new WC_Appointment_Form( $single_product_obj );

		ob_start();
		
		//* Set up post object
		$single_product->the_post();
		
		//* Enqueue single product script
		wp_enqueue_script( 'wc-single-product' );
		
		/**
		 * woocommerce_before_single_product hook
		 *
		 * @hooked wc_print_notices - 10
		 */
		do_action( 'woocommerce_before_single_product' );
		
		/**
		 * woocommerce_single_product_summary chunks
		*/
		if ( $atts['show_title'] ) {
			woocommerce_template_single_title();
		}
		if ( $atts['show_rating'] ) {
			woocommerce_template_single_rating();
		}
		if ( $atts['show_price'] ) {
			woocommerce_template_single_price();
		}
		if ( $atts['show_excerpt'] ) {
			woocommerce_template_single_excerpt();
		}
		
		//* Get template
		wc_get_template( 'single-product/add-to-cart/appointment.php', array( 'appointment_form' => $appointment_form ), 'woocommerce-appointments', WC_APPOINTMENTS_TEMPLATE_PATH );
		
		if ( $atts['show_meta'] ) {
			woocommerce_template_single_meta();
		}
		if ( $atts['show_sharing'] ) {
			woocommerce_template_single_sharing();
		}
		
		//* Reset for integrity
		wp_reset_postdata();

		return '<div class="woocommerce"><div class="product">' . ob_get_clean() . '</div></div>';
	}
	
}

add_action( 'init', array( 'WC_Appointments_Shortcodes', 'init' ) );
