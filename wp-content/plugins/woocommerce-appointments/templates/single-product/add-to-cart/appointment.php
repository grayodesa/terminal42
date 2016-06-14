<?php
/**
 * Appointment product add to cart
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $product;

if ( ! $product->is_purchasable() ) {
	return;
}

do_action( 'woocommerce_before_add_to_cart_form' ); ?>

<noscript><?php _e( 'Your browser must support JavaScript in order to schedule an appointment.', 'woocommerce-appointments' ); ?></noscript>

<form class="cart" method="post" enctype='multipart/form-data'>

 	<div id="wc-appointments-appointment-form" class="wc-appointments-appointment-form" style="display:none">

 		<?php do_action( 'woocommerce_before_appointment_form_output' ); ?>
		
		<?php $appointment_form->output(); ?>
		
		<div class="wc-appointments-appointment-hook"><?php do_action( 'woocommerce_before_add_to_cart_button' ); ?></div>
		
		<div class="wc-appointments-appointment-cost"></div>

	</div>
	
	<?php
		/* Show qunatity only when no Staff is assigned and qty is larger than 1 ... duuuuuuh
		 * Why hide if Staff is assigned? - because only 1 staff is available per appointment
		 */
		if ( $product->get_qty() > 1 ) {
			woocommerce_quantity_input( array(
				'min_value'   => apply_filters( 'woocommerce_quantity_input_min', 1, $product ),
				'max_value'   => apply_filters( 'woocommerce_quantity_input_max', $product->get_qty(), $product ),
				'input_value' => ( isset( $_POST['quantity'] ) ? wc_stock_amount( $_POST['quantity'] ) : 1 )
			) );
		}
	?>

	<input type="hidden" name="add-to-cart" value="<?php echo esc_attr( $product->id ); ?>" />

 	<button type="submit" class="wc-appointments-appointment-form-button single_add_to_cart_button button alt disabled" style="display:none"><?php echo $product->single_add_to_cart_text(); ?></button>

 	<?php do_action( 'woocommerce_after_add_to_cart_button' ); ?>

</form>

<?php do_action( 'woocommerce_after_add_to_cart_form' ); ?>
