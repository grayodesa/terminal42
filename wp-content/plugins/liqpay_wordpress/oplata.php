<?php
if(!isset($wpdb))
{
    include_once('../../../wp-config.php');
} 

 function kama_drussify_months( $date, $req_format ){
	// в формате есть "строковые" неделя или месяц
	if( ! preg_match('~[FMlS]~', $req_format ) ) return $date;
	
	$replace = array ( 
		"январь" => "января", "Февраль" => "февраля", "Март" => "марта", "Апрель" => "апреля", "Май" => "мая", "Июнь" => "июня", "Июль" => "июля", "Август" => "августа", "Сентябрь" => "сентября", "Октябрь" => "октября", "Ноябрь" => "ноября", "Декабрь" => "декабря", 
		
		"January" => "января", "February" => "февраля", "March" => "марта", "April" => "апреля", "May" => "мая", "June" => "июня", "July" => "июля", "August" => "августа", "September" => "сентября", "October" => "октября", "November" => "ноября", "December" => "декабря",	

		"Jan" => "янв.", "Feb" => "фев.", "Mar" => "март.", "Apr" => "апр.", "May" => "мая", "Jun" => "июня", "Jul" => "июля", "Aug" => "авг.", "Sep" => "сен.", "Oct" => "окт.", "Nov" => "нояб.", "Dec" => "дек.",	

		"Sunday" => "воскресенье", "Monday" => "понедельник", "Tuesday" => "вторник", "Wednesday" => "среда", "Thursday" => "четверг", "Friday" => "пятница", "Saturday" => "суббота",

		"Sun" => "вос.", "Mon" => "пон.", "Tue" => "вт.", "Wed" => "ср.", "Thu" => "чет.", "Fri" => "пят.", "Sat" => "суб.", "th" => "", "st" => "", "nd" => "", "rd" => "",		
	);
	
   	return strtr( $date, $replace );
   
}


function wc_ip_adress( ) {
	$ip_adress = wc_GetRealIp();
	return $ip_adress;
}

function wc_GetRealIp()
{
 if (!empty($_SERVER['HTTP_CLIENT_IP']))
 {
   $ip=$_SERVER['HTTP_CLIENT_IP'];
 }
 elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))
 {
  $ip=$_SERVER['HTTP_X_FORWARDED_FOR'];
 }
 else
 {
   $ip=$_SERVER['REMOTE_ADDR'];
 }
 return $ip;
}
?>
<?php $currency =  get_woocommerce_currency();?>
<section style="display:none" class="container" role="document">
	<div class="row padding_top">
	<div class="small-12 large-12 columns" role="main">

				<article class="post-48 page type-page status-publish hentry" id="post-48">
			<header>
				<h1 class="entry-title">Оплата</h1>
			</header>
			<div class="entry-content">
				<div class="woocommerce">					<ul class="order_details">
						<li class="order">
							Заказ:							<strong>№ <?php  echo $_GET['order'];?></strong>
						</li>
						<li class="date">
							Дата:							<strong><?php  print_r(kama_drussify_months (date('j F  Y '), 'j F Y')); ?></strong>
						</li>
						<li class="total">
							К оплате:							<strong><span class="amount"><?php $order = new WC_Order($_GET['order']); echo $order->order_total. ' ' .$currency ; ?></span></strong>
						</li>
												<li class="method">
							Метод оплаты:							<strong>Liqpay (Оплата картами Visa/MasterCard)</strong>
						</li>
											</ul>

					<p>Спасибо за Ваш заказ, нажмите пожалуйста на кнопку Оплатить или.</p>

 <form  name="myForm" action= <?php echo plugins_url().'/liqpay_wordpress/liqpay-form.php';?> method='POST' >	
 	<input type='hidden' name='date' value='".date("d.m.Y H:i:s" )."' required/>
 	<input type='hidden' name='ip'  value='<?php echo wc_ip_adress();?>'/>
 	<input type='hidden' name='fio' value='<?php echo $order->billing_first_name. ' '. $order->billing_last_name ; ?>' required/>
	<input type='hidden' id='order_id' name='order_id'  value='<?php  echo $_GET['order'];?>'. />
	<input type='hidden' id='key' name='key'  value='<?php  echo $_GET['key'];?>'. />
	<input type='hidden' name='mail' value='<?php echo $order->billing_email;?>'  placeholder='Email' required/>
	<input type='hidden' id='plata' name='plata'  value='Order №<?php  echo $_GET['order'];?>'. />
	<input type='hidden' id='paid' name='paid'  value='<?php echo $order->order_total;?>' required/> 
	<input type='hidden' name='menu' value='<?php  if ($currency == 'EUR') echo 'EUR';elseif ($currency == 'UAH') echo 'UAH'; elseif ($currency == 'USD') echo 'USD'; elseif ($currency == 'RUB') echo 'RUB'; else echo 'UAH';?>'> 
	<input  class="button-alt button"  type='submit' value='Оплатить' > 

  </form>
   				<script>
                document.myForm.submit();
              </script>
		 <!-- <a class="button cancel" style="float: left;" href="<?php //echo $woocommerce->cart->get_cart_url();?>?cancel_order=true&amp;order=<?php //echo $_GET['key'];?>&amp;order_id=<?php //echo $_GET['order'];?>&amp;redirect">Отменить оплату и обнулить корзину</a> -->
		<?php echo  '<a class="button cancel" href="'.$order->get_cancel_order_url().'">'.__('Отменить оплату и обнулить корзину', 'liqpay').'</a>'; ?>
					<div class="clear"></div>
					</div>
			</div>
			<footer>
								<p></p>
			</footer>
			
		</article>
	
	</div>
</div>

</section>