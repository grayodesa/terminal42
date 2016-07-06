<?php if( !isset($_GET['print_pos_receipt']) ): ?>
<?php
// don't load directly
if ( !defined('ABSPATH') )
  die('-1');
?>

<div class="clear"></div></div><!-- wpbody-content -->
<div class="clear"></div></div><!-- wpbody -->
<div class="clear"></div></div><!-- wpcontent -->

<?php

$full_keypad      = get_option('woocommerce_pos_register_instant_quantity_keypad');
$instant_quantity = get_option('woocommerce_pos_register_instant_quantity');
?>

<div class="clear"></div></div><!-- wpwrap -->
<div class="md-modal md-dynamicmodal <?php echo $full_keypad == 'yes' && $instant_quantity == 'yes' ? 'full_keypad': ''; ?>" id="modal-missing-attributes">
	<div class="md-content">
		<h1><?php _e( 'Select Variation', 'wc_point_of_sale' ); ?></h1>
		<div>
			<div id="missing-attributes-select">
			</div>
			<?php if ($instant_quantity == 'yes') { ?>
				<div class="enter-quantity">
					<div class="inline_quantity"></div>
				</div>
				<?php 
			} ?>
			<div class="clear"></div>
		</div>
		<div class="wrap-button wrap-button-center">
				<button class="md-close button wp-button-large cancel-add-product"><?php _e( 'Cancel', 'wc_point_of_sale' ); ?></button>
				<button class="button button-primary wp-button-large product-add-btn" ><?php _e( 'Add Product', 'wc_point_of_sale' ); ?></button>
			</div>
	</div>
</div>
<?php if ($instant_quantity == 'yes') { ?>
<div class="md-modal md-dynamicmodal <?php echo $full_keypad == 'yes' ? 'full_keypad': ''; ?>" id="modal-qt-product">
	<div class="md-content">
		<h1><?php _e( 'Enter Quantity', 'wc_point_of_sale' ); ?></h1>
		<div>
			<div class="enter-quantity">
				<div class="inline_quantity"></div>
			</div>
			<div class="clear"></div>
			</div>
		<div class="wrap-button wrap-button-center">
				<button class="md-close button wp-button-large cancel-add-product"><?php _e( 'Cancel', 'wc_point_of_sale' ); ?></button>
				<button class="button button-primary wp-button-large product-add-btn"><?php _e( 'Add Product', 'wc_point_of_sale' ); ?></button>
		</div>
	</div>
</div>
<?php } ?>
<?php require_once( 'modal/html-modal-payments.php' ); ?>    
<?php require_once( 'modal/html-modal-comments.php' ); ?>    
<?php require_once( 'modal/html-modal-discount.php' ); ?>    
<?php require_once( 'modal/html-modal-add-shipping.php' ); ?>
<?php require_once( 'modal/html-modal-custom-product.php' ); ?>
<?php require_once( 'modal/html-modal-product-custom-meta.php' ); ?>
<?php require_once( 'modal/html-modal-retrieve-sales.php' ); ?>    
<?php require_once( 'modal/html-modal-add-new-customer.php' ); ?>
<?php require_once( 'modal/html-modal-printing-receipt.php' ); ?>
<?php require_once( 'modal/html-modal-redirect.php' ); ?>
<?php require_once( 'modal/html-modal-confirm.php' ); ?>
<?php require_once( 'modal/html-modal-offline.php' ); ?>
<?php require_once( 'modal/html-modal-lock-screen.php' ); ?>
<?php require_once( 'modal/html-modal-clone-window.php' ); ?>
<div class="md-overlay"></div>
<div class="md-overlay-prompt"></div>
<script type="text/javascript">if(typeof wpOnload=='function')wpOnload();</script>

<div id="printable"></div>
<?php endif; ?>

<?php do_action( 'admin_print_footer_scripts' ); ?>
<?php 
if( !isset($_GET['print_pos_receipt']) ){
	$this->footer(); 	
	do_action('wc_pos_footer', $this); 
}
?>
</body>
</html>
