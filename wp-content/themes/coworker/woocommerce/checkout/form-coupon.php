<?php
/**
 * Checkout coupon form
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     2.2
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! WC()->cart->coupons_enabled() ) {
	return;
}

$info_message = apply_filters('woocommerce_checkout_coupon_message', __( 'Have a coupon?', 'woocommerce' ));
?>

<div class="col_half col_last nobottommargin">

	<p class="woocommerce-info"><i class="icon-gift"></i><?php echo $info_message; ?> <a href="#" class="showcoupon"><?php _e( 'Click here to enter your code', 'woocommerce' ); ?></a></p>

	<form class="checkout_coupon" method="post" style="display:none">

		<p class="form-row form-row-first">
			<input type="text" name="coupon_code" class="input-text" placeholder="<?php _e( 'Coupon code', 'woocommerce' ); ?>" id="coupon_code" value="" style="display: inline-block;" />
			<input type="submit" class="btn" name="apply_coupon" value="<?php _e( 'Apply Coupon', 'woocommerce' ); ?>" style="position: relative; top: -3px;" />
		</p>

		<div class="clear"></div>
	</form>

</div>

<div class="clear"></div><div class="line"></div>