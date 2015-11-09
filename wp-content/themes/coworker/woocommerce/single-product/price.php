<?php
/**
 * Single Product Price, including microdata for SEO
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     1.6.4
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $post, $product;
?>
<div itemprop="offers" itemscope itemtype="http://schema.org/Offer" class="product-meta clearfix">

	<div class="product-price"><?php echo $product->get_price_html(); ?></div>

	<meta itemprop="priceCurrency" content="<?php echo get_woocommerce_currency(); ?>" />
	<link itemprop="availability" href="http://schema.org/<?php echo $product->is_in_stock() ? 'InStock' : 'OutOfStock'; ?>" />
	<div class="product-availability"><small><?php echo $product->is_in_stock() ? __( 'In Stock', 'coworker' ) : __( 'Out of Stock', 'coworker' ); ?></small></div>

</div>