<?php
/**
 * Displayed when no products are found matching the current query.
 *
 * Override this template by copying it to yourtheme/woocommerce/loop/no-products-found.php
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
?>
<div class="alert alert-error">
	<button type="button" class="close" data-dismiss="alert">×</button>
	<?php _e( 'No products found which match your selection.', 'woocommerce' ); ?>
</div>