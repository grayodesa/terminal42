<?php
/* Функции обратного вызова */

// создание класса
function woocommerce_paymaster_ua(){
	require_once __DIR__ .'/../classes/PaymasterUA.php';
}

// подключение класса к системе
function PaymasterUA_init($methods){
	$methods[] = 'PaymasterUA'; 
	return $methods;
}

// описание валюты
function paymaster_grn_currency($currencies){
	$currencies['UAH'] = 'Ukrainian Grivna';
	return $currencies;
}

// замена значка валюты на название в товарах на сайте
function grn_currency_symbol( $currency_symbol, $currency ){
	if($currency === 'UAH'){$currency_symbol = 'грн.';}
	return $currency_symbol;
}

?>