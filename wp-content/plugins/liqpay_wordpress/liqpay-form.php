<?php
  // функция превода текста с кириллицы в траскрипт
//echo "<meta http-equiv='Content-Type' content='text/html; charset=UTF-8'>";
if(!isset($wpdb))
{
    require_once('../../../wp-config.php');
} 
include_once "api.php";
/*$subject = "Отчет по оплате ";
  $status = "платеж отклонен";
$text =  "Извините но Ваш платеж не прошел по техническим причинам ПС Liqpay. Попробуйте пожалуйста еще раз...\n";
$headers = "From: ". "<dsqwared@ukr.net>" . "\r\n";
wp_mail( "info@lacoccinelle.com.ua", $subject, $text, $headers, $attachments );
wp_mail( "dsqwared@ukr.net", $subject, $text, $headers, $attachments );
*/
$merchant_id=get_option('liqpay_merchant_id');
$signature=get_option('liqpay_signature_id');
$url="https://liqpay.com/?do=clickNbuy&button=".$signature;
$hidden_content = '';
if (isset($_POST['hidden_content'])) 
   $hidden_content = $_POST['hidden_content'];
  
if ($hidden_content) 
$result_url = $_POST['url_page'];
else 
$result_url = get_option('liqpay_result_url');
  $url  = @( $_SERVER["HTTPS"] != 'on' ) ? 'http://'.$_SERVER["SERVER_NAME"] :  'https://'.$_SERVER["SERVER_NAME"];
  $url .= ( $_SERVER["SERVER_PORT"] != 80 ) ? ":".$_SERVER["SERVER_PORT"] : "";
  $url .= $_SERVER["REQUEST_URI"];
  $url = explode('/', $url);
$url1 = '';
for ($i=0; $i < count($url)-1; $i++) { 
  $url1 .= $url[$i]."/";  
}
$server_url = $url1."liqpay-answer.php";
$method='';
$phone=get_option('liqpay_phone');
$ord_id = rand (10000,99999);
$description = $_POST['fio'];
global $user_identity, $current_user;  
get_currentuserinfo();
//$to = $current_user->user_email;  
update_option('liqpay_current_user', $current_user->ID);
if ($_POST['mail'] !== "")
update_option('liqpay_mail_buyer', $_POST['mail']);
else
update_option('liqpay_mail_buyer', $current_user->user_email);
$mail = get_option('liqpay_mail_buyer');
$lang = get_option('liqpay_lang');
$plata = $_POST['plata'];
$liq_order_id = $_POST['order_id'];
update_option('liq_order_id', $liq_order_id);
$liq_key = $_POST['key'];
update_option('liq_key', $liq_key);
update_option('hidden_content', $hidden_content);
$day = '';
if (isset($_POST['day'])) 
   $day = $_POST['day']; 
$secure_day = $day;
update_option('secure_day', $secure_day);
$url_page = '';
if (isset($_POST['url_page'])) 
   $url_page = $_POST['url_page']; 
update_option('url_page', $url_page);
$ip_adress = $_POST['ip'];
update_option('ip_adress', $ip_adress);
$skidka = vivod_skidki2();
if ($skidka > 0)
{ $amount = $_POST['paid'];
if (get_option('liqpay_komissiya') > 0)
$amount = $amount + $amount/100*get_option('liqpay_komissiya');
$amount = $amount-$amount/100*$skidka;
$amount = round($amount,'2');
}
else
{
$amount = $_POST['paid'];
if (get_option('liqpay_komissiya') > 0)
$amount = $amount + $amount/100*get_option('liqpay_komissiya');
$amount = round($amount,'2');
}
$valuta = $_POST['menu'];
$liqpay_product_id = '';
if (isset($_POST['liqpay_product_id'])) 
$liqpay_product_id = $_POST['liqpay_product_id'];
update_option('liqpay_product_id', $liqpay_product_id);  
$product_id = $liqpay_product_id;
$plata = str_replace (',' , '.' , $plata );
$description .= "   " .$plata;
//echo vivod_skidki2(); exit; 
$lqsignature = base64_encode( sha1($signature.$amount.$valuta.$merchant_id.$ord_id.'buy'.$description.$result_url.$server_url, 1 ));
$testmode = get_option('liqpay_check_testmode');
//'subscribe' => '0', 'subscribe_date_start' => '2015-03-31 00:00:00', 'subscribe_periodicity' => 'month',
/////////////////****************************************************************** API 3.0
$json_string = array('version' => '3', 'public_key' => $merchant_id, 'amount' => $amount, 'currency' => $valuta, 'description' => $description, 
                      'order_id' => $ord_id, 'type' => 'buy',  
                      'server_url' => $server_url, 'result_url' => $result_url, 'pay_way' => 'card,liqpay,delayed,invoice,privat24', 
                      'language' => $lang, 'sandbox' => $testmode);
$data = base64_encode(json_encode($json_string));
//$data = base64_encode(json_encode(array_merge(compact($merchant_id), $json_string)));
//$signature = base64_encode( sha1( $signature . $data . $signature) );
$liqpay = new LiqPay($merchant_id, $signature);
$html = $liqpay->cnb_form(array(
 'version' => '3',
 'amount' => $amount,
 'currency' => $valuta,     //Можно менять  'EUR','UAH','USD','RUB','RUR'
 'description' => $description,  //Или изменить на $desc
 'order_id' => $ord_id,
 'server_url' => $server_url,
 'result_url' => $result_url,
 'type' => 'buy', 
 'pay_way' => 'card,liqpay,delayed,invoice,privat24', 
 'language' => $lang,
 'sandbox' => $testmode
 ));
 echo $html;

?>