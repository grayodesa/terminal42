<?php
/**
 * Empty cart page
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

?>

<div class="alert alert-error"><?php _e( 'Your cart is currently empty.', 'woocommerce' ) ?></div>

<?php do_action('woocommerce_cart_is_empty'); ?>

<p class="nobottommargin"><a class="border-button nomargin" href="<?php echo get_permalink(woocommerce_get_page_id('shop')); ?>"><?php _e( '&larr; Return To Shop', 'woocommerce' ) ?></a></p>