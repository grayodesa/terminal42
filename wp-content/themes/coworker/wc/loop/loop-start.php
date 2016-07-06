<?php
/**
 * Product Loop Start
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     2.0.0
 */

$columns_layout = semi_option('shop_archive_layout');

if( $columns_layout == '3' OR $columns_layout == '3s' ) {
	$shop_col_class = 'shop-3 ';
} elseif( $columns_layout == '2s' ) {
	$shop_col_class = 'shop-2 ';
} else {
	$shop_col_class = '';
}

?>
<div id="shop" class="<?php echo $shop_col_class; ?>clearfix">