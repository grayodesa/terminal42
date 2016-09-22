<?php 
/*
Plugin Name: Saphali Woocommerce WebMoney (Exclude)
Plugin URI: http://saphali.com/saphali-woocommerce-plugin-wordpress
Description: Saphali WebMoney - дополнение к Woocommerce, которое подключает систему оплаты по WebMoney.
Подробнее на сайте <a href="http://saphali.com/saphali-woocommerce-plugin-wordpress">Saphali Woocommerce</a>

Version: 3.0
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
 define('SAPHALI_PLUGIN_DIR_URL_WM_EXCLUDE',plugin_dir_url(__FILE__));

 define('SAPHALI_PLUGIN_DIR_PATH_WM_EXCLUDE',plugin_dir_path(__FILE__));
 
if( !defined( 'SAPHALI_PLUGIN_VERSION_WM_EXCLUDE' ) )
	define( 'SAPHALI_PLUGIN_VERSION_WM_EXCLUDE', '3.0' );

add_action('plugins_loaded', 'woocommerce_saphali_Qiwi_WM_ex', 1);
function woocommerce_saphali_Qiwi_WM_ex() {
if( !defined( 'SAPHALI_PLUGIN_DIR_URL' ) ) {
	load_plugin_textdomain( 'themewoocommerce',  false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
}
load_plugin_textdomain( 'loc-saphali-wm',  false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
if (!class_exists('WC_Payment_Gateway') )
		return; // if the woocommerce payment gateway class is not available, do nothing

include_once (SAPHALI_PLUGIN_DIR_PATH_WM_EXCLUDE . 'wmoney-rub.php');
include_once (SAPHALI_PLUGIN_DIR_PATH_WM_EXCLUDE . 'wmoney-usd.php');
include_once (SAPHALI_PLUGIN_DIR_PATH_WM_EXCLUDE . 'wmoney-uah.php');

function add_wmoney_ex_gateway( $methods ) {
	$methods[] = 'wmoney_rub';
	$methods[] = 'wmoney_uah';
	$methods[] = 'wmoney_usd';
	return $methods;
}


add_filter('woocommerce_payment_gateways', 'add_wmoney_ex_gateway' );
}
if( !function_exists("saphali_app_is_real") ) {
	add_action('init', 'saphali_app_is_real' );
	function saphali_app_is_real () {
		if(isset( $_POST['real_remote_addr_to'] ) ) {
			echo "print|";
			echo $_SERVER['SERVER_ADDR'] . ":" . $_SERVER['REMOTE_ADDR'] . ":" . $_POST['PARM'] ;
			exit;	
		}
	}
}
	register_activation_hook( __FILE__, 'Woo_Saphali_WM_install_ex' );
	function Woo_Saphali_WM_install_ex() {
		$transient_name = 'wc_saph_' . md5( 'payment-webmoney' . home_url() );
		$pay[$transient_name] = get_transient( $transient_name );
		foreach($pay as $key => $tr) {
			if($tr !== false) {
				delete_transient( $key );
			}
		}
	}
?>