<?php
/**
 * Shipping Calculator
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     2.0.8
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $woocommerce;

if ( get_option( 'woocommerce_enable_shipping_calc' ) === 'no' || ! WC()->cart->needs_shipping() )
	return;
?>

<?php do_action( 'woocommerce_before_shipping_calculator' ); ?>

<form class="shipping_calculator" action="<?php echo esc_url( WC()->cart->get_cart_url() ); ?>" method="post">

	<h3><?php _e( 'Calculate Shipping', 'woocommerce' ); ?></h3>

	<p class="form-row form-row-wide col_full nobottommargin notopmargin">
		<select name="calc_shipping_country" id="calc_shipping_country" class="country_to_state input-block-level" rel="calc_shipping_state">
			<option value=""><?php _e( 'Select a country&hellip;', 'woocommerce' ); ?></option>
			<?php
				foreach( WC()->countries->get_shipping_countries() as $key => $value )
					echo '<option value="' . esc_attr( $key ) . '"' . selected( WC()->customer->get_shipping_country(), esc_attr( $key ), false ) . '>' . esc_html( $value ) . '</option>';
			?>
		</select>
	</p>

	<p class="form-row form-row-wide col_two_fifth nobottommargin notopmargin">
		<?php
			$current_cc = WC()->customer->get_shipping_country();
			$current_r  = WC()->customer->get_shipping_state();
			$states     = WC()->countries->get_states( $current_cc );

			// Hidden Input
			if ( is_array( $states ) && empty( $states ) ) {

				?><input type="hidden" name="calc_shipping_state" id="calc_shipping_state" placeholder="<?php _e( 'State / county', 'woocommerce' ); ?>" /><?php

			// Dropdown Input
			} elseif ( is_array( $states ) ) {

				?><span>
					<select name="calc_shipping_state" id="calc_shipping_state" class="input-block-level" placeholder="<?php _e( 'State / county', 'woocommerce' ); ?>">
						<option value=""><?php _e( 'Select a state&hellip;', 'woocommerce' ); ?></option>
						<?php
							foreach ( $states as $ckey => $cvalue )
								echo '<option value="' . esc_attr( $ckey ) . '" ' . selected( $current_r, $ckey, false ) . '>' . __( esc_html( $cvalue ), 'woocommerce' ) .'</option>';
						?>
					</select>
				</span><?php

			// Standard Input
			} else {

				?><input type="text" class="input-text input-block-level" value="<?php echo esc_attr( $current_r ); ?>" placeholder="<?php _e( 'State / county', 'woocommerce' ); ?>" name="calc_shipping_state" id="calc_shipping_state" /><?php

			}
		?>
	</p>

	<?php if ( apply_filters( 'woocommerce_shipping_calculator_enable_city', false ) ) : ?>

		<p class="form-row form-row-wide col_two_fifth nobottommargin notopmargin">
			<input type="text" class="input-text input-block-level" value="<?php echo esc_attr( WC()->customer->get_shipping_city() ); ?>" placeholder="<?php _e( 'City', 'woocommerce' ); ?>" name="calc_shipping_city" id="calc_shipping_city" />
		</p>

	<?php endif; ?>

	<?php if ( apply_filters( 'woocommerce_shipping_calculator_enable_postcode', true ) ) : ?>

		<p class="form-row form-row-wide col_two_fifth nobottommargin notopmargin">
			<input type="text" class="input-text input-block-level" value="<?php echo esc_attr( WC()->customer->get_shipping_postcode() ); ?>" placeholder="<?php _e( 'Postcode / Zip', 'woocommerce' ); ?>" name="calc_shipping_postcode" id="calc_shipping_postcode" />
		</p>

	<?php endif; ?>

	<p class="col_one_fifth col_last nobottommargin notopmargin"><button type="submit" name="calc_shipping" value="1" class="btn input-block-level" style="position: relative; top: 3px;"><?php _e( 'Update', 'woocommerce' ); ?></button></p>

	<?php wp_nonce_field( 'woocommerce-cart' ); ?>
</form>

<?php do_action( 'woocommerce_after_shipping_calculator' ); ?>