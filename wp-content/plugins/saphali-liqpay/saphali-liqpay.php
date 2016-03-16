<?php 
/*
Plugin Name: Saphali Woocommerce LiqPay
Plugin URI: http://saphali.com/saphali-woocommerce-plugin-wordpress
Description: Saphali LiqPay - дополнение к Woocommerce, которое подключает систему оплаты по LiqPay (версия протокола 3.0).
Подробнее на сайте <a href="http://saphali.com/saphali-woocommerce-plugin-wordpress">Saphali Woocommerce</a>

Version: 3.1
Author: Saphali
Author URI: http://saphali.com/
*/


/*

 Продукт, которым вы владеете выдался вам лишь на один сайт,
 и исключает возможность выдачи другим лицам лицензий на 
 использование продукта интеллектуальной собственности 
 или использования данного продукта на других сайтах.

 */


/* Add a custom payment class to woocommerce
  ------------------------------------------------------------ */
  // Подключение валюты и локализации
 define('SAPHALI_PLUGIN_DIR_URL_LP',plugin_dir_url(__FILE__));
 $is_fu = 'wp_'.'ma'.'il';
 $func = 'wp_m'.'ail';
 define('SAPHALI_PLUGIN_DIR_PATH_LP',plugin_dir_path(__FILE__));

if( !defined( 'SAPHALI_PLUGIN_VERSION_LP' ) )
	define( 'SAPHALI_PLUGIN_VERSION_LP', '3.1' );
//END


add_action('plugins_loaded', 'woocommerce_saphali_LiqPay', 0);
function woocommerce_saphali_LiqPay() {
	add_action("woocommerce_order_status_completed", array('liqpay', 'acces_to_user_go') );
	add_action('wp_ajax_status_order_liq_pay', array('liqpay', 'after_order_table_ajax'));
	if( is_admin() )
	add_action( 'add_meta_boxes_shop_order', array( 'liqpay', 'add_meta_boxes' ) );
	load_plugin_textdomain( 'themewoocommerce',  false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
	if( class_exists('WooCommerce_Payment_Status') )
	add_filter( 'woocommerce_valid_order_statuses_for_payment', array( 'liqpay', 'valid_order_statuses_for_payment' ), 52, 2 );
	if (!class_exists('WC_Payment_Gateway') )
			return; // if the woocommerce payment gateway class is not available, do nothing

	include_once (SAPHALI_PLUGIN_DIR_PATH_LP . 'LiqPay-class.php');
	include_once (SAPHALI_PLUGIN_DIR_PATH_LP . 'liqpay.php');

	function add_liqpay_gateway( $methods ) {
		$methods[] = 'liqpay';
		return $methods;
	}
	add_filter('woocommerce_payment_gateways', 'add_liqpay_gateway' );
}

	register_activation_hook( __FILE__, 'Woo_Saphali_LiqPay_install' );
	function Woo_Saphali_LiqPay_install() {
		
		$transient_name = 'wc_saph_' . md5( 'payment-liqpay' . home_url() );
		$pay[$transient_name] = get_transient( $transient_name );
		
		foreach($pay as $key => $tr) {
			if($tr !== false) {
				delete_transient( $key );
			}
		}
	}
?>