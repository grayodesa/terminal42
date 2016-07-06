<?php
/**
 * POS Coupons
 *
 * Returns an array of strings
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

return array(
	100    => __( 'Coupon is not valid.', 'woocommerce' ),
	101    => __( 'Sorry, it seems the coupon "%s" is invalid - it has now been removed from your order.', 'woocommerce' ),
	102    => __( 'Sorry, it seems the coupon "%s" is not yours - it has now been removed from your order.', 'woocommerce' ),
	103    => __( 'Coupon code already applied!', 'woocommerce' ),
	104    => __( 'Sorry, coupon "%s" has already been applied and cannot be used in conjunction with other coupons.', 'woocommerce' ),
	105    => __( 'Coupon "%s" does not exist!', 'woocommerce' ),
	106    => __( 'Coupon usage limit has been reached.', 'woocommerce' ),
	107    => __( 'This coupon has expired.', 'woocommerce' ),
	108    => __( 'The minimum spend for this coupon is %s.', 'woocommerce' ),
	109    => __( 'Sorry, this coupon is not applicable to your cart contents.', 'woocommerce' ),
	110    => __( 'Sorry, this coupon is not valid for sale items.', 'woocommerce' ),
	111    => __( 'Please enter a coupon code.', 'woocommerce' ),
	112    => __( 'The maximum spend for this coupon is %s.', 'woocommerce' ),
	113    => __( 'Sorry, this coupon is not applicable to the products: %s.', 'woocommerce' ),
	114    => __( 'Sorry, this coupon is not applicable to the categories: %s.', 'woocommerce' ),
	200    => __( 'Coupon code applied successfully.', 'woocommerce' ),
	201    => __( 'Coupon code removed successfully.', 'woocommerce' ),
	202    => __( 'Discount added successfully.', 'woocommerce' ),
	203    => __( 'Discount updated successfully.', 'woocommerce' ),
);
