<?php
/*
Plugin Name: Paymaster[UA] WooCommerce
Plugin URI: 
Description: Набор платежных инструментов
Version: 1.0
Author: Marat Company
Author URI: https://themarat.com
*/

require_once 'callback_function/functions.php';
add_action('plugins_loaded', 'woocommerce_paymaster_ua'); // создаем новый платежный процессор
add_filter('woocommerce_payment_gateways', 'PaymasterUA_init'); // подключаем к WooCom
add_filter('woocommerce_currency_symbol', 'grn_currency_symbol', 10, 2); // название валюты в товарах
?>