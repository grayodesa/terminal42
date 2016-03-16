<?php
echo '<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">';
echo ' <link rel="stylesheet" id="liqpay-css" href="./css/liqpay.css?ver=3.5.1" type="text/css" media="all">';
if(!isset($wpdb))
    require_once('../../../wp-config.php');




function insertdb($order_id1, $xdate, $transaction_id1,$status1,$summa1,$datas1,$sender_phone1,$code1,$valuta1, $email1, $ip1)	 {
global $wpdb, $table_prefix;
$table_liqpay = $table_prefix.'liqpay';
if(!isset($wpdb))
    require_once('../../../wp-config.php');
	//$wpdb = new wpdb(DB_USER,DB_PASSWORD,DB_NAME,DB_HOST);	
	$wpdb->insert( 	$table_liqpay,	array('id' => $order_id1,'xdate' => $xdate, 'transaction_id' => $transaction_id1, 'status' => $status1, 
										  'err_code' => $code1, 'summa' => $summa1, 'valuta' => $valuta1, 'sender_phone' => $sender_phone1, 
										  'comments' => $datas1,'email' => $email1, 'ip' => $ip1), 
	array( '%d', '%s','%d', '%s','%d','%s','%s','%s','%s','%s','%s') 
	);
}
$json =  base64_decode( $_POST['data']);
$obj = json_decode($json);
$message = $obj->{'amount'};
$summa =  $obj->{'amount'};
$valuta =  $obj->{'currency'};
$public_key =  $obj->{'public_key'};
$datas = $obj->{'description'};
$order_id = $obj->{'order_id'};
$type = $obj->{'type'};
$status = $obj->{'status'};
$transaction_id = $obj->{'transaction_id'};
$sender_phone = $obj->{'sender_phone'};
$xdate = date( "Y.m.d H:i:s" );
global $wpdb, $table_prefix;		
//$wpdb = new wpdb(DB_USER,DB_PASSWORD,DB_NAME,DB_HOST);	
$table_answer_code = $table_prefix.'liqpay_answer_code';
$answer_code = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_answer_code WHERE code = %d",$order_id));
	if(!$answer_code)
	{	
		$new_code = 1;
		$wpdb->insert
					(	$table_answer_code,  
						array( 'code' => $order_id, 'status' => $status),  
						array( '%s', '%s')
					);
$testmode = get_option('liqpay_check_testmode');
$commission =  get_option('liqpay_komissiya');
$ip_adress = get_option('ip_adress');
$hidden_content = get_option('hidden_content');
$secure_day = get_option('secure_day'); 
$url_page = get_option('url_page');
$liq_order_id = get_option('liq_order_id');
$liq_key = get_option('liq_key');
global $user_identity, $current_user;  
get_currentuserinfo();
$to = get_option('liqpay_mail_buyer');
if (!$to)
$to = $current_user->user_email;
if (($current_user->user_firstname) || ($current_user->user_lastname) || ($current_user->user_login))
$fio = $current_user->user_firstname . " " . $current_user->user_lastname . " " . $current_user->user_login;
insertdb($order_id, $xdate, $transaction_id,$status,$summa,$datas,$sender_phone,'',$valuta,  $to, $ip_adress);	
	}
	else $new_code = 0;
if ($testmode)
$subject = "Отчет по оплате (TEST) ";
else
$subject = "Отчет по оплате ";
$liqpay_magazin_tmp = get_option('liqpay_magazin');
$liqpay_mail_sender_tmp = " <".get_option('liqpay_mail_sender').">";
$headers = "From: ". $liqpay_magazin_tmp . $liqpay_mail_sender_tmp. "\r\n";


////////////////////////////////////////////////////////////////////\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\
////////////////////////////////////////////////////////////////////\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\
if ($status=="failure") {
	$status = "платеж отклонен";
$text =  "Извините но Ваш платеж не прошел...\n";
$text .=  "\n Дата/время  "  . $xdate ;
$text .=  "\n id заказа = " . $order_id;
$text .=  "\n id транзакции в системе LiqPay  = "  . $transaction_id ;
$text .=  "\n Статус транзакции = " .  $status ;
$text .=  "\n Стоимость = "  .  $summa . " " .$valuta;
$text .=  "\n Телефон оплативший заказ  = " .  $sender_phone;
$text .=  "\n Комментарий = " . $datas;
if ($fio)
$message = $text. " \r\n  Имя - ". $fio ;
else 
$message = $text;
$mail = get_option('liqpay_mail');
if ($new_code) {
   if ($liq_order_id) {
	global $woocommerce;
	$order = new WC_Order($liq_order_id);
	$order->add_order_note('Failed');
	$order->update_status('failed');
	$this->msg['class'] = 'woocommerce_error';
	$this->msg['message'] = "Thank you for shopping with us. However, the transaction has been declined.";
   }
    if (!$liq_order_id) {
	 wp_mail($to, $subject."(".$status.")", $message, $headers, $attachments);
	 wp_mail($mail, $subject."(".$status.")", $message, $headers, $attachments);
	}
}
exit;
}
elseif ($status=="success" || $status=="sandbox") { 
$flag = 1; 

if ($hidden_content == '1')
list  ($user_id, $user_login, $user_pass, $user_url) = liqpay_add_pass_by_pay ($url_page, 1);
$status = "платеж совершен";
$text =  "\n Дата/время  "  . $xdate;
$text .=  "\n id заказа = " . $order_id;
$text .=  "\n id транзакции в системе LiqPay  = "  . $transaction_id ;
$text .=  "\n Статус транзакции = " .  $status ;
$text .=  "\n Стоимость = "  .  $summa . " " .$valuta;
$text .=  "\n Телефон оплативший заказ  = " .  $sender_phone;
$text .=  "\n Комментарий " . $datas;
if ($hidden_content == '1') {
	$text .=  "\n -------Данные для входа------- ";
	/*$text .=  "\n Логин: " . $user_login;*/
	$text .=  "\n Пароль:  " . $user_pass;
	$text .=  "\n Аренда контента на :  " . $secure_day. get_name($secure_day,' День',' Дня',' Дней'). " (Время аренды начинает действовать после первого ввода пароля!)";
	$text .=  "\n Ссылка на страницу:  " . $user_url . "\n";
}
if ($fio)
$message = $text. " Имя - ". $fio ;
else 
$message = $text;
$mail = get_option('liqpay_mail');

/////////////////////////////////////////////////////////////////////////////
		global $code, $product_id;
		$code = liqpay_random_string(10);
		$ctime = time();
		$product_id = get_option('liqpay_product_id');
		global $wpdb, $table_prefix;		
		$table_downloadcode = $table_prefix.liqpay_downloadcodes;
		require_once('../../../wp-config.php');
		//$wpdb = new wpdb(DB_USER,DB_PASSWORD,DB_NAME,DB_HOST);	
			$wpdb->insert (	$table_downloadcode,  
							array( 'downloadcode' => $code, 'product_id' => $product_id, 'ctime' => $ctime),  
							array( '%s', '%d', '%d')
		  				  );
		$status_url = get_option('liqpay_status_url');
		//$url = liqpay_start_download($code,0);
		preg_match('/^http(s)?\:\/\/[^\/]+\/(.*)$/i', $status_url, $matches);
		$liqpay_download_url = $_SERVER['HTTP_HOST']."/".$matches[2];
		$table_products = $table_prefix.liqpay_products;
		$product = $wpdb->get_row( 	$wpdb->prepare 	(  "SELECT * FROM $table_products WHERE id = %d",$product_id)	);
		$url = $product->url;
		$table_skidki = $table_prefix."liqpay_skidki";
		$liqpay_current_user = get_option('liqpay_current_user');
		$users = $wpdb->get_row($wpdb->prepare("SELECT id, users_name, users_id, users_skidka from $table_skidki where users_id = %d",$liqpay_current_user));
			if($product->name) {	
							$skidka = $users->users_skidka;
							if ($skidka > 0) {
							$cost_cult = $product->cost - $product->cost/100*$skidka;
							if ($commission > 0)
							$cost_cult = $cost_cult + $cost_cult/100*$commission;
							$cost_cult = round($cost_cult,'2');
							}
							else
							{
							$cost_cult = $product->cost;
							if ($commission > 0)
							$cost_cult = $cost_cult + $cost_cult/100*$commission;
							$cost_cult = round($cost_cult,'2');
							}
							if (($hidden_content) && ($secure_day)) {
								$cost_cult = $cost_cult*$secure_day;
								$cost_cult = trim(number_format($cost_cult, 2, '.', ' '), '0.');
								if (substr($cost_cult, -1) == ",")
								$cost_cult = substr($cost_cult, 0, -1);
							}
							$code_cult = $product->id;
							$valuta_cult = $product->valuta;		
							
							$summa = trim(number_format($summa, 2, '.', ' '), '0.');
							if (substr($summa, -1) == ",")
							$summa = substr($summa, 0, -1);

					if (($cost_cult==$summa) and ($valuta_cult == $valuta)){
						echo("<form  name='download' action='../../../wp-admin/admin.php?page=liqpay' method='POST'>
							  <INPUT name='liqpay_code' type='hidden' value=".$code.">
							  <INPUT style='color: red; text-decoration: underline;' class='btn' name='liqpay_download' type='submit' value='Скачать'>
							  </form></br>");
					}
					else {echo "</br> <h3> <label style='color:red'> Ваша сумма не соответствует стоимости товара </br>  
							<b>Цена товара $cost_cult $valuta_cult Вы оплатили $summa $valuta  </b> </label></h3>";
						  echo "</br>Для того чтобы получить товар свяжтесь с администратором Email: ".get_option('liqpay_mail')." к письму прикрепите скриншот этого окна или отрпвьте в теле письма днные этой таблицы. ";
						  $flag = 0; }			
			}

/////////////////////////////////////////////////////////////////////////////
if ($new_code) {
$findme = "wp-content";
$pos = strpos($url,$findme);
$url_end = substr ($url, $pos+11);
$url_beg = WP_CONTENT_DIR;
$pos = strpos($url_beg,$findme);
$url_beg = substr ($url_beg, 0 ,$pos+11);
$file_url = $url_beg.'/'.$url_end;
if ($flag == 1) {
 if ($liq_order_id) {
	global $woocommerce;
	$order = new WC_Order($liq_order_id);
	$order->payment_complete();
	$order->update_status( 'completed' );
	//$order -> add_order_note('Liqpay payment successful<br/>Unnique Id from Liqpay: ');
	//$order -> add_order_note("<b>Отделение Новой Почты:".get_option( 'np_res')."</b>");
	$woocommerce->cart->empty_cart();	
	}

$attachments = array($file_url);	
$text_head =  "Поздравляем! Ваш платеж прошел успешно...\n";
$message = $text_head.$message;
}
else {
$attachments = '';	
$text_head =  "Поздравляем! Ваш платеж прошел успешно... \n";
$text_head .= $cost_cult. $valuta_cult. " Вы оплатили. ". $summa. $valuta." Но сумма не соответствует стоимости товара, за разъяснением вопроса, обратитесь к администратору ".get_option('liqpay_mail');
$message = $text_head.$message;
}
 if (!$liq_order_id) {
	wp_mail($to, $subject."(".$status.")", $message, $headers, $attachments);
	wp_mail($mail, $subject."(".$status.")", $message."   Email покупателя - ". $to, $headers, $attachments);
 }
}
exit;
}
elseif ($status=="wait_secure"){ $status = "платеж находится на проверке";
$text =  "Ваш платеж ожидает проверки...\n";
$text .=  "\n Дата/время  "  . $xdate;
$text .=  "\n id заказа = " . $order_id;
$text .=  "\n id транзакции в системе LiqPay  = "  . $transaction_id ;
$text .=  "\n Статус транзакции = " .  $status ;
$text .=  "\n Стоимость = "  .  $summa . " " .$valuta;
$text .=  "\n Телефон оплативший заказ  = " .  $sender_phone;
$text .=  "\n Комментарий " . $datas;
$text .=  "\n Если долгое время плятеж не проходит, Вам следует обратится в онлайн чат службы поддержки Liqpay, по адресу <a href='https://liqpay.com/' title='Liqpay'>Liqpay</a>";
if ($fio)
$message = $text. " Имя - ". $fio ;
else 
$message = $text;;
$mail = get_option('liqpay_mail');
if ($new_code) {
	if ($liq_order_id) {
		global $woocommerce;
		$order = new WC_Order($liq_order_id);
		$order -> add_order_note('Liqpay payment processing.');
		$order -> add_order_note('message');
		$order->update_status( 'processing' );
		$woocommerce->cart->empty_cart();	
	}
	if (!$liq_order_id) {
		wp_mail($to, $subject."(".$status.")", $message, $headers, $attachments);
		wp_mail($mail, $subject."(".$status.")", $message, $headers, $attachments);
	}
}
exit;
}
?>