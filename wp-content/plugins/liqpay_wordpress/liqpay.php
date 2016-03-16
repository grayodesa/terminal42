<?php
/* 
    Plugin Name: Liqpay&Wordpress
    Plugin URI: http://pfy.in.ua/liqpay/
    Description: Платежная система Liqpay for Wordpress
    Author: M.I. Simkin
    Version: 2.1
    Author URI: http://pfy.in.ua/liqpay/
*/
/*  Copyright 2013 Simkin Maksim  (email : dsqwared {at} ukr.net)
    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.
    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.
    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/
if(!function_exists('liqpay_load_resources')){
function liqpay_load_resources() {
    wp_register_script( 'liqpay_form_script', plugins_url( '/js/liqpay_form.js', __FILE__ ), array('jquery') );	
	wp_enqueue_script('liqpay_form_script');
 }
}
add_action('wp_enqueue_scripts', 'liqpay_load_resources',5);
require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
if (is_plugin_active('woocommerce/woocommerce.php')) 
{require_once "woocommerce.php"; }

function get_timezone() {
    $timezone = get_option( 'timezone_string' );
    if( empty( $timezone ) ) {
        $timezone = sprintf( '%+.4g', get_option( 'gmt_offset', 0 ) );
        $timezone = $timezone * -1;
        $timezone = sprintf( '%+.4g', $timezone );
    }
    return $timezone;
}

date_default_timezone_set('Etc/GMT'.get_timezone());
require_once( ABSPATH . "wp-includes/pluggable.php" );
function liqpay_options() {
//Добавляем меню 
	add_menu_page('Liqpay', 'Buy/Оплата', 'manage_options', 'liqpay_buy', 'liqpay_base');
	add_submenu_page('liqpay_buy', 'Liqpay', 'Журнал оплат', "manage_options", 'liqpay_list', 'liqpay_list_page');
	add_submenu_page('liqpay_buy', 'Liqpay', 'Генерация кнопки', "manage_options", 'liqpay_generation_button', 'liqpay_generation_button_page');
	add_submenu_page('liqpay_buy', 'Liqpay', 'Список товара', "manage_options", 'liqpay_product_page', 'liqpay_product_page');
	add_submenu_page('liqpay_buy', 'Liqpay', 'Скидки для пользоватлей', "manage_options", 'liqpay_skidki_page', 'liqpay_skidki_page');	
if (is_plugin_active('liqpay_wordpress_secure_content/liqpay.php')) {
  add_submenu_page('liqpay_buy', 'Liqpay', 'Продажа кодов/паролей', "manage_options", 'liqpay_secure_buy_pass_page', 'liqpay_secure_buy_pass_page');
} 
	add_submenu_page('liqpay_buy', 'Liqpay', 'Настройка', "manage_options", 'liqpay_options', 'liqpay_options_page');
}

if(!function_exists('admin_custom_js')){
function admin_custom_js ()
{  $url = plugins_url( '/js/liqpay_form.js', __FILE__ );
	wp_enqueue_script( 'javascript_file',  $url, array('jquery')) ;
}
}
add_action('admin_init', 'admin_custom_js' ); 
function func_vivod_skidki() {
	$code_vivod_skidki =  vivod_skidki();
	return $code_vivod_skidki;
}
add_shortcode( 'vivod_skidki', 'func_vivod_skidki' );
function func_vivod_uslugi() {
	$code_vivod_uslugi =  vivod_uslugi();
	return $code_vivod_uslugi;
}
add_shortcode( 'vivod_uslugi', 'func_vivod_uslugi' );
function liqpay_generation_button_page(){
	global $wpdb;  


	            echo '<div class="wrap"><div id="icon-options-general" class="icon32">
                        <br>
                </div>
                <h2>Генерация кнопки</h2>
				<h3>Заполните пожалуйста поля ниже и нажмите кнопку "Генерировать код кнопки", полученый код вставляйте в нужное место на сайте.';
if (isset($_POST['comment1'])) {
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
$liqpay_gen_button_check_summa = $_POST['liqpay_gen_button_check_summa'];
update_option('liqpay_gen_button_check_summa', $liqpay_gen_button_check_summa);	
$show_fio = $_POST['show_fio'];
update_option('show_fio', $show_fio);	
$show_skidka = $_POST['show_skidka'];
update_option('show_skidka', $show_skidka);	
$hidden_content = $_POST['hidden_content'];
update_option('hidden_content', $hidden_content);	 
$txt_vivod = "<form >";
$txt_tmp = " <form action='".WP_CONTENT_URL."/plugins/liqpay_wordpress/liqpay-form.php' method='POST' style='width: 197px;'";
if ($hidden_content)
$txt_tmp .= " target='_blank' ";
$txt_tmp .= " >";
$txt2= "	<input type='hidden' name='date' value='\".date(\"d.m.Y H:i:s\" ).\"' required/>";
$txt2.= "<input type='hidden' name='liqpay_product_id'  value='".$_POST['menu_products']."'/>";	
$txt2.= "<input type='hidden' name='hidden_content'  value='".$_POST['hidden_content']."'/>";
$txt2.= "<input type='hidden' name='url_page'  value=[url_page]/>";
$txt2.= "<input type='hidden' name='ip'  value=[ip]/>";
if (!$_POST['show_fio'])	
$txt2.=	"<input  class='textarea' type='text' name='fio' value=''  placeholder='ФИО' required/><br />";
$txt2.=	"<input  class='textarea' type='email' name='mail' value=''  placeholder='Email' required/> <br />";
if ($hidden_content)
$txt2.=	"<label for='change_day'>На сколько дней нужен доступ? </label> <br /><input  class='textarea' type='text' id='change_day' name='day' value='1'  required/> <br />";
$txt2.=	"<label for='plata'> Назначение платежа</label><br /><input  class='textarea'";  
	if (!$_POST['plata']) 
	$txt2.=  " "; 
	else $txt2.= " readonly "; 
	$txt2.= " type='text' id='plata' name='plata'  value='".$_POST['plata']."' /><br />";	
	$txt2.= "<div class='no_br' ><input style='float: left;'    class='textarea val' type='text' id='paid' name='paid'  value='";
	if (get_option('liqpay_gen_button_check_summa'))
	$txt2.= $_POST['liqpay_gen_button_check_summa']."' readonly required/> ";
	else 
	$txt2.= '\' required/> ';
	if (!$_POST['menu_valuta']) 
	{
	$txt2.= "<select class='textarea sel' style='float: left;' name='menu' size='1'> ";
    $txt2.= "<option value='EUR'>EUR</option>";	
	$txt2.= "<option selected='selected' value='UAH'>UAH</option>";
    $txt2.= "<option  value='USD'>USD</option>";	
    $txt2.= "<option  value='RUB'>RUB</option>";	
    $txt2.="</select> <div class='flag'></div>";}
   else $txt2.=	"<input  class='textarea' style='width:50px; float: left;' type='text' readonly name='menu'   value='".$_POST['menu_valuta']."' required/><div class='flag'></div>";
$txt_vivod.= $txt2."</div>";// . "[vivod_skidki] <br />"; // Сделать вывод ссылки по чекбоксу------------------------------------------------------------------
$txt2 = $txt_tmp.$txt2;
if ($_POST['show_skidka']){
$txt_vivod .= "[vivod_skidki]<br />";
$txt2 .= "[vivod_skidki] <br />";
}
else { 
$txt_vivod .= "<br />";
$txt2 .= "<br />";
}
$txt2.="	<input class='btn' type='submit' value='".$_POST['name_button_buy']."' > </div></form>";
$txt_vivod.= "<input class='btn' type='Button' value='".$_POST['name_button_buy']."' > </form>";
if (($_POST['plata']) and ($_POST['menu_valuta']) and (get_option('liqpay_gen_button_check_summa')) and ($_POST['show_fio'])) {
	$txt2 = " <form  action='".WP_CONTENT_URL."/plugins/liqpay_wordpress/liqpay-form.php' method='POST'>";
$txt2 .= "	<input type='hidden' name='date' value='\".date(\"d.m.Y H:i:s\" ).\"' required/>";	
$txt2.=	"<input type='hidden' name='fio' value='' required/>";
$txt2.=	"<input type='hidden' name='plata' value='".$_POST['plata']."'> ";	
$txt2.= "<input type='hidden' name='paid'  value='".$_POST['liqpay_gen_button_check_summa']."'/>";
$txt2.= "<input type='hidden' name='menu'  value='".$_POST['menu_valuta']."'/>";  
$txt2.= "<input type='hidden' name='liqpay_product_id'  value='".$_POST['menu_products']."'/>";
$txt2 .= "	<input class='btn' type='submit' value='".$_POST['name_button_buy']." ".$_POST['plata']." : ".get_option('liqpay_gen_button_check_summa')." ".$_POST['menu_valuta']."' > </form>";
$txt_vivod = "<form >";
$txt_vivod .=  "<input class='btn' type='Button' value='".$_POST['name_button_buy']." ".$_POST['plata']." : ".get_option('liqpay_gen_button_check_summa')." ".$_POST['menu_valuta']."' > </form>";
}
}
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
$loc = "self.location.href=\"javascript:window.location.reload()\"";
echo " <form class='textarea' method='POST'>
	<input class='textarea' type='text' name='plata' value='" .$_POST['plata']."'";
	echo "/> <span>Назначение платежа (если поле заполнено, то изменить поле 'Назначение платежа' клиент не сможет)</span> <br />
	<input class='textarea' style='width:50px' type='text' name='liqpay_gen_button_check_summa' value='" . $_POST['liqpay_gen_button_check_summa'] . "' '/> 
			<select class='textarea' name='menu_valuta' size='1'> 
	<option selected='selected' value=''></option>
	";
	if ($_POST['menu_valuta'] == 'EUR')
	echo "<option selected='selected' value='EUR'>EUR</option>";
	else
    echo "<option value='EUR'>EUR</option>";	
	if ($_POST['menu_valuta'] == 'UAH')
	echo "<option selected='selected' value='UAH'>UAH</option>";
	else
    echo "<option  value='UAH'>UAH</option>";		
	if ($_POST['menu_valuta'] == 'USD')
	echo "<option selected='selected' value='USD'>USD</option>";
	else
    echo "<option  value='USD'>USD</option>";	
	if ($_POST['menu_valuta'] == 'RUB')
	echo "<option selected='selected' value='RUB'>RUB</option>";
	else
    echo "<option  value='RUB'>RUB</option>";
	echo "</select> 
	<span>Сумма и валюта (если поле заполнено, то изменить сумму клиент не сможет) </span> 	<br />
	<input class='textarea' type='text' name='name_button_buy' value='" .$_POST['name_button_buy']."' required/> <span>Текст для кнопки \"Оплатить\" (*)</span> 
	<br />";
	$table_products = $wpdb->prefix.'liqpay_products';
	$products = $wpdb->get_results("SELECT id, name, cost, valuta FROM ". $table_products);
	echo "<select class='textarea' name='menu_products' size='1'> 
	<option selected='selected' value=''></option>";
	foreach ($products as $item) 	
	{
	echo "<option"; 
	if ($_POST['menu_products'] == $item->id)
	echo " selected='selected' ";
	echo " value='".$item->id."'>".$item->name." | ".$item->cost."</option>";	
	}
	echo "</select> <span>Выбирите товар</span> <br />";
	echo "<lable><input type='checkbox' name='show_fio' value='1'"; if (get_option('show_fio')) echo 'checked'; echo "> Скрыть поле ФИО?</lable> <br />";
	echo "<lable><input type='checkbox' name='show_skidka' value='1'"; if (get_option('show_skidka')) echo 'checked'; echo "> Показать поле скидка?</lable>
	<br />	";
if (is_plugin_active('liqpay_wordpress_secure_content/liqpay.php')) {
    echo "<lable><input type='checkbox' name='hidden_content' value='1'"; if (get_option('hidden_content')) echo 'checked'; echo "> Форма для скрытого контента.</lable>
	<br />";
} 
	echo "<input class='btn' type='submit' name='comment1' value='Генерировать код кнопки' >
	<input class='btn' type='button' value='Отчистить' onclick='".$loc."'  />
	</form>
	";	
echo '</div>';
echo "<form method='POST'>
	<textarea  class='textarea'  readonly name='comment' cols='60' rows='10'>".$txt2."</textarea><br /><span>Html код кнопки</span> 
	<br /><br />
";
echo "Так будет выглядеть Ваша форма <br />". $txt_vivod;
}
function liqpay_list_page(){
global $paid, $user_identity, $current_user, $wpdb;  
get_currentuserinfo();
if (isset($_POST['liqpay_reset_filter'])) {
	update_option('liqpay_check_disable_failure', '');
	update_option('liqpay_search_order_id', '');
	update_option('liqpay_search_date_begin', '');
	update_option('liqpay_search_date_end', '');
		}
if (isset($_POST['liqpay_filter'])) {
	$liqpay_check_disable_failure = $_POST['liqpay_check_disable_failure'];
	update_option('liqpay_check_disable_failure', $liqpay_check_disable_failure);
		}
	if (isset($_POST['liqpay_search'])) {
	$liqpay_search_order_id = $_POST['liqpay_search_order_id'];
	update_option('liqpay_search_order_id', $liqpay_search_order_id);
	$liqpay_search_date_begin  = $_POST['liqpay_search_date_begin'];
	$liqpay_search_date_end  = $_POST['liqpay_search_date_end'];
	update_option('liqpay_search_date_begin', $liqpay_search_date_begin);
	update_option('liqpay_search_date_end', $liqpay_search_date_end);
		}		
                echo '<div class="wrap"><div id="icon-options-general" class="icon32">
                        <br>
                </div>
                <h2>Журнал оплат</h2>';
echo "
<div  class='textarea' style=' text-align:left; font-size:14px; border: collapse; border: solid 1px #5A91B1;'>
<table width='100%' style=' text-align:left; font-size:14px; border: collapse; border: solid 1px #5A91B1; padding-left: 6px;'><tr><td><b>Поиск</b></td></tr></table> <br />
<form   method='post' id='serch' style='padding-left: 6px;'>  
Поиск:  
order_id <input  type='text' name='liqpay_search_order_id' size='30' maxlength='50' value='". get_option('liqpay_search_order_id') . "' >  &nbsp; &nbsp; &nbsp; &nbsp;
Дата с <input  class='textarea' type='date' name='liqpay_search_date_begin' size='30' maxlength='50' value='" . get_option('liqpay_search_date_begin'). "' > 
по <input  class='textarea' type='date' name='liqpay_search_date_end' size='30' maxlength='50' value='" . get_option('liqpay_search_date_end')  . "' > <br />
<INPUT class='btn' name='liqpay_search' type='submit' value='Искать'>
<br />	
</form>  
 <br /> 
</div>
<div   class='textarea' style=' text-align:left; font-size:14px; border: collapse; border: solid 1px #5A91B1;'>
<table width='100%'style=' text-align:left; font-size:14px; border: collapse; border: solid 1px #5A91B1; padding-left: 6px;'><tr><td><b>Фильтры</b></td></tr></table> <br />
<form  method='post' id='filter' style='padding-left: 6px; margin-bottom: -25px;'>  
<lable><input  class='textarea' type='checkbox' name='liqpay_check_disable_failure' id='liqpay_check_disable_failure' value='1'"; if (get_option('liqpay_check_disable_failure')) echo 'checked'; echo "> Не показывать неудавшиеся платежи</lable>
<INPUT class='btn' name='liqpay_filter' type='submit' value='Применить фильтр'>
<br />	
</form>  
 <br /> 
<form  method='post' id='reset_all' style='padding-left: 6px;'>  
<INPUT class='btn' name='liqpay_reset_filter' type='submit' value='Сбросить фильтры и поиск'>
<br />	
</form>   
<br />
</div>";
echo "<div id='res_check'></div>";
$text2 = get_option('liqpay_search_order_id'); 
global $post,$wpdb,$arr;
echo "<table style=' text-align:center; font-size:14px; border-collapse: collapse; width: 100%; border: solid 1px #BBC1C4;'>   
<tr>
 <td  style='border: solid 1px #BBC1C4;'>order_id</td>
 <td  style='border: solid 1px #BBC1C4;'>date</td>
 <td  style='border: solid 1px #BBC1C4;'>transaction_id</td>
 <td  style='border: solid 1px #BBC1C4;'>status</td>
 <td  style='border: solid 1px #BBC1C4;'>err_code</td>
 <td  style='border: solid 1px #BBC1C4;'>summa</td>
 <td  style='border: solid 1px #BBC1C4;'>valuta</td>
 <td  style='border: solid 1px #BBC1C4;'>sender_phone</td>
 <td  style=' border: solid 1px #BBC1C4;'>comments</td>        
 <td  style=' border: solid 1px #BBC1C4;'>email</td>        
 <td  style=' border: solid 1px #BBC1C4;'>IP</td>       
</tr>  ";			
	if  (get_option('liqpay_search_date_begin'))
	$datebegin = get_option('liqpay_search_date_begin') ;
	else 
	$datebegin = "1800-01-01";
	if  (get_option('liqpay_search_date_end') )
	$dateend = get_option('liqpay_search_date_end') ;
	else 
	$dateend = "2080-01-01";
	global $wpdb;
	$table_liqpay = $wpdb->prefix.'liqpay';
$query = " SELECT w.id, w.xdate, w.transaction_id,w.status, w.err_code, w.summa, w.valuta,  w.sender_phone,  w.comments, w.email, w.ip
    FROM $table_liqpay as w
	where 1=1 ";
	if (get_option('liqpay_check_disable_failure'))
	 $query .= "and w.status in ('success','wait_secure') ";
	if ($text2 <> '')
	 $query .= " and w.id in (".$text2 .") ";
	if  (get_option('liqpay_search_date_begin') or get_option('liqpay_search_date_end')) 
	$query .= " and DATE_FORMAT(w.xdate,'%Y-%m-%d') between '". $datebegin ."' and '". $dateend ."'";
 $query .= " order by w.xdate ASC";	
$result_sql = $wpdb->get_results($query);	
echo " Найдено: " . count($result_sql);
 $arr = array( array('', '')); 
$j=0;
$itog = 0;
foreach ( $result_sql as $i ) 
{
	$stat = $i->status;
	$datex=$i->xdate;
	$summax=$i->summa;
	$itog = $itog + $summax ;
$date_parts[0] = substr($datex,0,4);
$date_parts[1] = substr($datex,5,2);
$date_parts[2] = substr($datex,8,2);
$date_parts[3] = substr($datex,11,8);
$date_r = $date_parts[2].'-'.$date_parts[1].'-'.$date_parts[0];
$date_t = $date_r." ".$date_parts[3];
$arr[$j][0]=$date_r;
$arr[$j][1]=$summax;
$j++;
echo "   
<tr>
 <td  style='padding-left: 5px; padding-right: 5px;border: solid 1px #BBC1C4;'>" .$i->id. "</td>
 <td  style='padding-left: 5px; padding-right: 10px;border: solid 1px #BBC1C4;'>" .$date_t. "</td>
 <td style='padding-left: 5px; padding-right: 5px;border: solid 1px #BBC1C4;'>" .$i->transaction_id. "</td>
 "; if ($stat == 'failure') {
echo " <td style='color:#ff0000; padding-left: 5px;  padding-right: 5px;border: solid 1px #BBC1C4;'>" .$i->status. "</td>";
}
	elseif ($stat == 'delayed') 
	{
		echo " <td   style='color:#d8a903; padding-left: 5px; padding-right: 5px;border: solid 1px #BBC1C4;'>" .$i->status. "</td>";
	}	
	 else {
echo " <td   style='color:#00ff00; padding-left: 5px; padding-right: 5px;border: solid 1px #BBC1C4;'>" .$i->status. "</td>";
	 }
echo "  <td   style='padding-left: 5px; padding-right: 5px;border: solid 1px #BBC1C4;'>" .$i->err_code. "</td>
  <td   style='padding-left: 5px; padding-right: 5px;border: solid 1px #BBC1C4;'><b>" .$i->summa. "</b></td>
  <td   style='padding-left: 5px; padding-right: 5px;border: solid 1px #BBC1C4;'>" .$i->valuta. "</td>
  <td   style='padding-left: 5px; padding-right: 5px;border: solid 1px #BBC1C4;'>" .$i->sender_phone. "</td>
  <td align='left' width='60px' style=' padding-left: 5px; padding-right: 5px;border: solid 1px #BBC1C4;'>" .$i->comments. "</td>   
  <td align='left' style='padding-left: 5px; padding-right: 5px;border: solid 1px #BBC1C4;'>" .$i->email. "</td>     
  <td align='left' style='padding-left: 5px; padding-right: 5px;border: solid 1px #BBC1C4;'>" .$i->ip. "</td>     
</tr>  ";				
}
echo "   
<tr>
 <td style='padding-left: 5px; padding-right: 5px;border: solid 1px #BBC1C4;'> <b> ИТОГО: </b></td>
 <td > </td>
 <td > </td>
 <td > </td>
 <td >  </td>
 <td style='padding-left: 5px; padding-right: 5px;border: solid 1px #BBC1C4;'><b> "; echo $itog; echo "</b> </td>
 <td > </td>
 <td > </td>
 <td > </td>   
 <td > </td>  
 <td > </td>  
</tr>  ";
echo '</table> </div>';
if (function_exists('wp_corenavi')) wp_corenavi(); 
wp_reset_query(); 
echo "<div id='chart_div' style='width: 1200px; height: 500px;'></div>";
?>
<script type="text/javascript" src="https://www.google.com/jsapi"></script>
 <script type="text/javascript">
      google.load("visualization", "1", {packages:["corechart"]});
      google.setOnLoadCallback(drawChart);
      function drawChart() {
 arr = [ <?php echo json_encode($arr) ?> ] ;
var totalSize = arr.length * arr[0].length
if (totalSize == 1) return;
var empty = new Array()
empty[0]=['Дата', 'Сумма'];
for (i=0;  i<=totalSize-1; ++i) 
{
	empty[i+1] = [arr[0][i][0],eval(arr[0][i][1])];
}
        var data = google.visualization.arrayToDataTable(empty );
        var options = {
          title: 'График оплат'
        };
        var chart = new google.visualization.LineChart(document.getElementById('chart_div'));
        chart.draw(data, options);
      }
    </script>
<?php
}	
function liqpay_base(){
global $paid, $user_identity, $current_user, $wpdb;  
get_currentuserinfo();
echo '<div class="wrap"><div id="icon-options-general" class="icon32">
                        <br>
                </div>
                <h2>Buy/Оплата</h2>
				<h3>Заполните пожалуйста поля ниже и нажмите кнопку "Buy/Оплатить", далее вы перейдете на защищенный сайт LiqPay.com, в котором вам необходимо заполнить данные своей карты (Visa/MasterCard)</h3>';
$fio = $current_user->user_firstname . " " . $current_user->user_lastname;
if ($fio == " ") $fio = "";
echo(" <form action='".WP_CONTENT_URL."/plugins/liqpay_wordpress/liqpay-form.php' method='POST'>
	<input type='hidden' name='date' value='". date( "d.m.Y H:i:s" ) . "' required/>
	<input  class='textarea' type='text' name='fio' value='". $fio . "' required/> <span>Введите ФИО</span> 
	<br />
	<input  class='textarea' type='text' name='plata' value='' required/> <span>Назначение платежа</span><br />
	<input  class='textarea' type='text' name='paid'  value='' required/> <span>Введите сумму</span> 
	<select  class='textarea' name='menu' size='1'> 
		<option value='EUR'>EUR</option>
		<option selected='selected' value='UAH'>UAH</option>
		<option value='USD'>USD</option>
		<option value='RUB'>RUB</option>
	</select> <span>Выберите валюту</span>
	<br />
	<input class='btn' type='submit' value='Buy/Оплатить' >
	</form>
	");	
echo '</div>';
}	
function liqpay_options_page()
{
	//Если форма была отправлена, то применить изменения магазина
	if (isset($_POST['liqpay_base_setup_btn'])) 
	{   
	   if ( function_exists('current_user_can') && 
			!current_user_can('manage_options') )
				die ( _e('Hacker?', 'liqpay') );
		if (function_exists ('check_admin_referer') )
		{
			check_admin_referer('liqpay_base_setup_form_#@r@@t');
		}
		$liqpay_merchant_id = $_POST['liqpay_merchant_id'];
		$liqpay_signature_id = $_POST['liqpay_signature_id'];
		$liqpay_phone = $_POST['liqpay_phone'];
		$liqpay_mail = $_POST['liqpay_mail'];
		$liqpay_mail_sender = $_POST['liqpay_mail_sender'];		
		$liqpay_ip_buyer = $_POST['liqpay_ip_buyer'];		
		$liqpay_magazin = $_POST['liqpay_magazin'];	
		$liqpay_komissiya = $_POST['liqpay_komissiya'];	
		$liqpay_lang = $_POST['liqpay_lang'];	
		$liqpay_code_expiration = $_POST['liqpay_code_expiration'];
		$liqpay_check_testmode = $_POST['liqpay_check_testmode'];
		$liqpay_result_url  = $_POST['liqpay_result_url'];
		update_option('liqpay_code_expiration', $liqpay_code_expiration);
		update_option('liqpay_merchant_id', $liqpay_merchant_id);
		update_option('liqpay_signature_id', $liqpay_signature_id);
		update_option('liqpay_phone', $liqpay_phone);
		update_option('liqpay_mail', $liqpay_mail);		
		update_option('liqpay_ip_buyer', $liqpay_ip_buyer);		
		update_option('liqpay_mail_sender', $liqpay_mail_sender);
		update_option('liqpay_magazin', $liqpay_magazin);
		update_option('liqpay_check_testmode', $liqpay_check_testmode);
		update_option('liqpay_komissiya', $liqpay_komissiya);
		update_option('liqpay_lang', $liqpay_lang);		
		update_option('liqpay_result_url', $liqpay_result_url);	
}
	//Форма информации о магазине
   //<!--Название раздела настроек-->

   echo " <div class='wrap'>  <div class='icon32' id='icon-options-general'><br /></div><h2>Настройки для Liqpay&Wordpress!</h2>";
  echo 	"  		<form name='liqpay_base_setup' method='post' action='" . $_SERVER['REQUEST_URI']."'>
	";
 	if (function_exists ('wp_nonce_field') )
	{
		wp_nonce_field('liqpay_base_setup_form_#@r@@t'); 
	}
	echo "
		<table width=auto>
			<tr>
				<td style='text-align:right;'>Публичный ключ:</td>
				<td style='width: 165px;'><input  class='textarea' type='text' name='liqpay_merchant_id' value='".get_option('liqpay_merchant_id')."'/></td>
				  <td style='color:#666666;'><i>Публичный ключ(merchant_id) это уникальная запись мерчанта, которая выдается во время регистрации вашего магазина в системе liqpay.</i></td>
			</tr>
			<tr>
				<td style='text-align:right;'>Приватный ключ:</td>
				<td><input  class='textarea' type='text' name='liqpay_signature_id' value='".get_option('liqpay_signature_id')."'/></td>
				<td width='600px' style='color:#666666;'><i>Приватный ключ(signature_id) это уникальная подпись мерчанта, которая выдается во время регистрации вашего магазина в системе liqpay.</i></td>
			</tr>
			<tr>
				<td style='text-align:right;'>Ваш телефон:</td>
				<td><input  class='textarea' type='text' name='liqpay_phone' value='".get_option('liqpay_phone')."'/></td>
				<td style='color:#666666;'><i>Ваш номер телефона к которому подключен Ваш магазин, вводить в международном формате (380501234567).</i></td>
			</tr>
			<tr>
 			<td style='text-align:right;'>Ваш mail:</td>
			<td><input  class='textarea' type='text' name='liqpay_mail' value='".get_option('liqpay_mail')."'/></td>
			<td style='color:#666666;'><i>Ваш mail на который будут приходить отчеты по оплате.</i></td>
			</tr>
			<tr>
 			<td style='text-align:right;'>Название Вашего предприятия:</td>
			<td><input  class='textarea' type='text' name='liqpay_magazin' value='".get_option('liqpay_magazin')."'/></td>
			<td style='color:#666666;'><i>Введите название вашего предприятия (магазина). Этот текст увидит оплативший клиент в поле письма (от кого) выглядеть это будет примерно так (от кого: 'Название Вашего предприятия' /Mail из следующего поля)/ </i></td>
			</tr>
			<tr>			
 			<td style='text-align:right;'>Ваш mail отправитель:</td>
			<td><input  class='textarea' type='text' name='liqpay_mail_sender' value='".get_option('liqpay_mail_sender')."'/></td>
			<td style='color:#666666;'><i>Ваш mail с которого будут приходить письма Вашим клиентам, оплатившие Ваши услуги</i></td>
			</tr>
<tr bgcolor='#999999'><td></td> <td></td><td></td></tr>	
			<tr>
 			<td style='text-align:right;'>Комиссия :</td>
			<td><input  class='textarea' type='text' name='liqpay_komissiya' value='".get_option('liqpay_komissiya')."'/></td>
			<td style='color:#666666;'><i>Комиссия взымается с клиента, введите % комиссии </i></td>
			</tr>
			<tr>
				<td style='text-align:right;'>Язык:</td>
				<td><select class='textarea' style='width: 157px;' name='liqpay_lang' size='1'> 
	";

if (get_option('liqpay_lang') == 'ru')
	echo "<option selected='selected' value='ru'>RU</option>";
else
	echo "<option value='ru'>RU</option>";	
if (get_option('liqpay_lang') == 'en')	
	echo "<option selected='selected' value='en'>EN</option>";
else
	echo "<option value='en'>EN</option>";	
	echo "</select> 
				</td>
				<td style='color:#666666;'><i>Выбирите язык интерфейса.</i></td>			</tr>
			<tr>
				<td style='text-align:right;'>Ссылка на страницу:</td>
				<td><input  class='textarea' type='text' name='liqpay_result_url' value='".get_option('liqpay_result_url')."'/></td>
				<td style='color:#666666;'><i>Ссылка на страницу, на которую попадет пользователь после оплаты (до 510 символов).</i></td>			</tr>
<tr>	
			<tr>
				<td style='text-align:right;'>Срок хранения:</td>
				<td><input  class='textarea' type='text' name='liqpay_code_expiration' value='".get_option('liqpay_code_expiration')."'/></td>
				<td style='color:#666666;'><i>Срок хранения ссылок в минутах.</i></td>			</tr>
<tr>
		<td>
<b><lable><input  class='textarea' type='checkbox' name='liqpay_check_testmode' id='liqpay_check_testmode' value='1'"; if (get_option('liqpay_check_testmode')) echo 'checked'; echo "> Включить тестовый режим (В тестовом режиме, платеж проходит, но деньги не списываются) ИСПОЛЬЗУЙТЕ ТОЛЬКО ДЛЯ ПРОВЕРКИ РАБОТЫ НЕ ЗАБУДЬТЕ ОТКЛЮЧИТЬ ПОСЛЕ ТЕСТА.</lable></b></td> 
</tr>
			<td></td>
				<td style='text-align:center'>
					<input class='btn' type='submit' name='liqpay_base_setup_btn' value='Сохранить' style='width:140px; height:25px'/>
				</td>
				<td>&nbsp;</td>
			</tr>
		</table>
	</form>
	</div>";
}
function liqpay_product_page()	{
	//Добавление товара
	echo "<h3>Добавить товара</h3>";
	liqpay_add_product();	
	//Изменение информации о товаре
	echo "<h3>Список товаров</h3>";
	liqpay_change_product();	
}//Изменение информации о товаре
function liqpay_change_product()
{
	global $wpdb;
	$table_products = $wpdb->prefix.'liqpay_products';
	//Сохранение изменений товаров
	if ( isset($_POST['liqpay_products_setup_btn']) ) 
    {   
       if (function_exists('current_user_can') && 
            !current_user_can('manage_options') )
                die ( _e('Hacker?', 'liqpay') );
        if (function_exists ('check_admin_referer') )
        {
            check_admin_referer('liqpay_products_setup_form');
        }
		$liqpay_product_name = $_POST['liqpay_product_name'];
        $liqpay_product_cost = $_POST['liqpay_product_cost'];
		$liqpay_product_id = $_POST['liqpay_product_id'];
		$liqpay_product_valuta = $_POST['menu_product_valuta_add'];
		$liqpay_product_url = $_POST['liqpay_product_url'];
		$wpdb->update($table_products,array( 'name' => $liqpay_product_name, 'cost' => $liqpay_product_cost,  'valuta' => $liqpay_product_valuta, 'url' => $liqpay_product_url),array( 'id' => $liqpay_product_id ),array( '%s', '%s', '%s', '%s'),array( '%d' ));
    }
	//Удаление товара
	if ( isset($_POST['liqpay_products_delete_btn']) ) 
    {   
       if (function_exists('current_user_can') && 
            !current_user_can('manage_options') )
                die ( _e('Hacker?', 'liqpay') );
        if (function_exists ('check_admin_referer') )
        {
            check_admin_referer('liqpay_products_setup_form');
        }
		$liqpay_product_id = $_POST['liqpay_product_id'];
		$wpdb->query("DELETE FROM $table_products WHERE id = $liqpay_product_id order by id"); 
    }
	//Вывод формы информации по товарам
	$products = $wpdb->get_results("SELECT * FROM $table_products order by id");
	foreach ($products as $item) 	
	{
		echo
		"
			<form name='liqpay_products_setup' method='post' action='".$_SERVER['REQUEST_URI']."'>
		";
		if (function_exists ('wp_nonce_field') )
		{
			wp_nonce_field('liqpay_products_setup_form'); 
		}
		echo
		"
				<p style='padding-top:30px;'><b>Товар ID = ".$item->id."</b></p>
				<table>
					<tr>
						<td style='text-align:right;'>Название:</td>
						<td><input  class='textarea' type='text' name='liqpay_product_name' value='".$item->name."' style='width:300px;'/></td>
						<td>&nbsp;</td>
					</tr>
					<tr>
						<td style='text-align:right;'>Цена:</td>
						<td>
							<input  class='textarea' type='text' name='liqpay_product_cost' value='".$item->cost."'/>
							<input  class='textarea' type='hidden' name='liqpay_product_id' value='".$item->id."'/>
							<select class='textarea' name='menu_product_valuta_add' size='1'> ";
    if ($item->valuta == 'EUR')
	echo "<option selected='selected' value='EUR'>EUR</option>";
	else
    echo "<option value='EUR'>EUR</option>";	
	if ($item->valuta == 'UAH')
	echo "<option selected='selected' value='UAH'>UAH</option>";
	else
    echo "<option  value='UAH'>UAH</option>";		
	if ($item->valuta == 'USD')
	echo "<option selected='selected' value='USD'>USD</option>";
	else
    echo "<option  value='USD'>USD</option>";	
	if ($item->valuta == 'RUB')
	echo "<option selected='selected' value='RUB'>RUB</option>";
	else
    echo "<option  value='RUB'>RUB</option>";
	echo "</select> 
						</td>
						<td style='color: #666666;'><i>Соблюдайте формат поля - целая часть от дробной отделяется точкой, в дробной обязательно присутствуют два разряда.</i></td>
					</tr>
					<tr>
						<td>&nbsp;</td>
						<td style='padding-left:5px; font-size:10px; color:#666666'>Пример: 1.00</td>
						<td>&nbsp;</td>
					</tr>
					<tr>
						<td style='text-align:right;'>URL товара:</td>
						<td>
							<input  class='textarea' type='text' name='liqpay_product_url' value='".$item->url."' style='width:300px;'/>
						</td>
						<td style='color:#666666;'><i>Ссылка на товар, для его загрузки после успешной оплаты.</i></td>
					</tr>
					<tr>
						<td>&nbsp;</td>
						<td style='padding-left:5px; font-size:10px; color:#666666'>Пример: http://www.moysite.ru/uploads/product1.zip</td>
						<td>&nbsp;</td>
					</tr>
					<tr>
						<td>&nbsp;</td>
						<td>
							<input class='btn' type='submit' name='liqpay_products_setup_btn' value='Сохранить' style='width:140px; height:25px'/>
							<input class='btn' type='submit' name='liqpay_products_delete_btn' value='Удалить' style='width:140px; height:25px'/>
						</td>
					</tr>
				</table>
			</form>
		";
	}
}
//Добавление товара
function liqpay_add_product()
{
	global $wpdb;
	$table_products = $wpdb->prefix.'liqpay_products';
	//Сохранение добавленного товара в базу
	if ( isset($_POST['liqpay_add_product_btn']) ) 
    {   
       if (function_exists('current_user_can') && 
            !current_user_can('manage_options') )
                die ( _e('Hacker?', 'liqpay') );
        if (function_exists ('check_admin_referer') )
        {
            check_admin_referer('liqpay_add_product_form');
        }
		$liqpay_product_name = $_POST['liqpay_product_name'];
        $liqpay_product_cost = $_POST['liqpay_product_cost'];
        $liqpay_product_valuta = $_POST['menu_product_valuta'];
		$liqpay_product_url = $_POST['liqpay_product_url'];
		$wpdb->insert
					(
						$table_products,  
						array( 'name' => $liqpay_product_name, 'cost' => $liqpay_product_cost, 'valuta' => $liqpay_product_valuta, 'url' => $liqpay_product_url),  
						array( '%s', '%s', '%s', '%s')
					);
    }
	//Форма добавления товара
	echo
		"
			<form name='liqpay_add_product' method='post' action='". $_SERVER['REQUEST_URI']."'>
		";
		if (function_exists ('wp_nonce_field') )
		{
			wp_nonce_field('liqpay_add_product_form'); 
		}
	echo
	"
			<table>
				<tr>
					<td style='text-align:right;'>Название:</td>
					<td><input  class='textarea' type='text' name='liqpay_product_name' style='width:300px;'/></td>
					<td>&nbsp;</td>
				</tr>
				<tr>
					<td style='text-align:right;'>Цена:</td>
					<td>
						<input  class='textarea' type='text' name='liqpay_product_cost'/>
						<select class='textarea' name='menu_product_valuta' size='1'> 
     <option value='EUR'>EUR</option>
	 <option selected='selected' value='UAH'>UAH</option>
     <option  value='USD'>USD</option>	
     <option  value='RUB'>RUB</option>
	</select> 
					</td>
					<td style='color: #666666;'><i>Соблюдайте формат поля - целая часть от дробной отделяется точкой, в дробной обязательно присутствуют два разряда.</i></td>
					</tr>
				<tr>
					<td>&nbsp;</td>
					<td style='padding-left:5px; font-size:10px; color:#666666'>Пример: 1.00</td>
					<td>&nbsp;</td>
				</tr>
				<tr>
					<td style='text-align:right;'>URL товара:</td>
					<td>
						<input  class='textarea' type='text' name='liqpay_product_url' style='width:300px;'/>
					</td>
					<td style='color:#666666;'><i>Ссылка на товар, для его загрузки после успешной оплаты.</i></td>
				</tr>
				<tr>
					<td>&nbsp;</td>
					<td style='padding-left:5px; font-size:10px; color:#666666'>Пример: http://www.moysite.ru/uploads/product1.zip</td>
					<td>&nbsp;</td>
				</tr>
				<tr>
					<td>&nbsp;</td>
					<td>
						<input class='btn' type='submit' name='liqpay_add_product_btn' value='Добавить' style='width:140px; height:25px'/>
					</td>
				</tr>
			</table>
		</form>
	";
}
if (isset($_POST['liqpay_download'])){
		liqpay_start_download($_POST['liqpay_code'],1);
	}; function liqpay_delete_expired_codes()
{
	global $wpdb, $table_prefix;
	$final_time = time() - get_option('liqpay_code_expiration')*60;
	$table_downloadcode = $table_prefix.'liqpay_downloadcodes';
	$wpdb->query
	(  
		$wpdb->prepare
		(  
			"DELETE FROM $table_downloadcode WHERE ctime < %d", 
			$final_time
		)
	);  
}function getExtension4($filename) {
    return substr(strrchr($fileName, '.'), 1);
  }
function liqpay_start_download($dcode_ok, $dwnload_btn_ok)
{
	global $wpdb, $table_prefix, $code;
	liqpay_delete_expired_codes();
	//$dcode = $_POST['liqpay_code'];
	$dcode = $dcode_ok;
	$table_downloadcode = $table_prefix.'liqpay_downloadcodes';	
	$table_products = $table_prefix.'liqpay_products';
	$code_product = $wpdb->get_row(	$wpdb->prepare( "SELECT * FROM $table_downloadcode WHERE downloadcode = %d", $dcode));
	if($code_product)
	{	
		$product_code_id = $code_product->product_id;
		$product = $wpdb->get_row ($wpdb->prepare("SELECT * FROM $table_products WHERE id = %d",$product_code_id));
		$url = $product->url;
			if ($dwnload_btn_ok)
				liqpay_download_file($url);
			else 
				return $url;
	}
	else
 		echo "Ссылка не активна". "   ". $dcode;
}
function get_zip_originalsize($filename) {
    $size = 0;
    $resource = zip_open($filename);
    while ($dir_resource = zip_read($resource)) {
        $size += zip_entry_filesize($dir_resource);
    }
    zip_close($resource);
    return $size;
}
function liqpay_download_file($filename)
{	
preg_match('/^.+\/([^\/]+)$/i', $filename, $matches);
	header('Content-Disposition: attachment; filename='.$matches[1]);
	clearstatcache(); 
	$extent = substr(strrchr($filename, '.'), 1);
if ($extent = 'zip')
$size =  get_zip_originalsize($filename);
else
$size = filesize ($filename);
	header('Content-Length: '.$size);
	header('Keep-Alive: timeout=5, max=100');
	header('Connection: Keep-Alive');
	header('Content-Type: octet-stream');
	readfile($filename);
	exit;}
function liqpay_random_string($number)
{
	//$number - кол-во символов 
	$arr = array('a','b','c','d','e','f',
	'g','h','i','j','k','l',
	'm','n','o','p','r','s',
	't','u','v','x','y','z',
	'A','B','C','D','E','F',
	'G','H','I','J','K','L',
	'M','N','O','P','R','S',
	'T','U','V','X','Y','Z',
	'1','2','3','4','5','6',
	'7','8','9','0');
	// Генерируем 
	$code_gen = "";
	for($i = 0; $i < $number; $i++)
	{
	// Вычисляем случайный индекс массива
		$index = rand(0, count($arr) - 1);
		$code_gen .= $arr[$index];
	}
	return $code_gen;
}
function liqpay_activate(){
	 global $wpdb;
 	 $table_liqpay = $wpdb->prefix.'liqpay';
     $sql = "CREATE TABLE IF NOT EXISTS `" . $table_liqpay . "` (
      `id` INT(11) NOT NULL DEFAULT '0',
	`xdate` DATETIME NOT NULL,
	`transaction_id` INT(11) NOT NULL,
	`status` TINYTEXT NOT NULL,
	`err_code` INT(11) NULL DEFAULT NULL,
	`summa` FLOAT NOT NULL,
	`valuta` TINYTEXT NOT NULL,
	`sender_phone` TINYTEXT NOT NULL,
	`comments` TEXT NOT NULL,
	 UNIQUE INDEX `id` (`id`)
	) ENGINE=MyISAM DEFAULT CHARSET=utf8";
	$wpdb->query($sql);
$alter_sql = "ALTER TABLE $table_liqpay ADD COLUMN email TEXT AFTER comments";
$wpdb->query($alter_sql);
$alter_sql = "ALTER TABLE $table_liqpay ADD COLUMN ip TEXT AFTER email";
$wpdb->query($alter_sql);
	$table_products = $wpdb->prefix.'liqpay_products';
	$table_answer_code = $wpdb->prefix.'liqpay_answer_code';
	$table_downloadcodes = $wpdb->prefix.'liqpay_downloadcodes';
	$table_uslugi = $wpdb->prefix.'liqpay_uslugi';
	$table_skidki = $wpdb->prefix.'liqpay_skidki';
/*
	$table_pass = $wpdb->prefix.liqpay_pass;*/
    $sql1 = 
	"
		CREATE TABLE IF NOT EXISTS `".$table_downloadcodes."` (
		  `id` int(10) NOT NULL AUTO_INCREMENT,
		  `downloadcode` varchar(64) NOT NULL,
		  `product_id` int(11) NOT NULL,
		  `ctime` int(11) NOT NULL,
		  PRIMARY KEY (`id`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8;
	";
	$sql2 =
	"
		CREATE TABLE IF NOT EXISTS `".$table_products."` (
		  `id` int(10) NOT NULL AUTO_INCREMENT,
		  `name` varchar(250) NOT NULL,
		  `cost` varchar(250) NOT NULL,
  		  `valuta` varchar(250) NOT NULL,
		  `url` varchar(250) NOT NULL,
		  PRIMARY KEY (`id`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8;
	";	
	$sql3 =
	"
		CREATE TABLE IF NOT EXISTS `".$table_answer_code."` (
		  `id` int(10) NOT NULL AUTO_INCREMENT,
		  `code` varchar(20) NOT NULL,
		  `status` varchar(20) NOT NULL,
		  PRIMARY KEY (`id`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8;
	";
	$sql4 =
	"
		CREATE TABLE IF NOT EXISTS `".$table_uslugi."` (
		  `id` int(10) NOT NULL AUTO_INCREMENT,
		  `date_uslugi` DATE NOT NULL,
  		  `time_uslugi` TIME NOT NULL,
		  `uslugi_tema` varchar(100) NOT NULL,
  		  `uslugi_text` varchar(850) NOT NULL,
		  `cost` varchar(20) NOT NULL,
  		  `valuta` varchar(20) NOT NULL,		  PRIMARY KEY (`id`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8;
	";
$sql5 =
	"
		CREATE TABLE IF NOT EXISTS `".$table_skidki."` (
		  `id` int(10) NOT NULL AUTO_INCREMENT,
		  `users_id` int(10) NOT NULL,
		  `users_name` varchar(100) NOT NULL,
  		  `users_skidka` int(10) NOT NULL DEFAULT 0,
		  PRIMARY KEY (`id`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8;
	";
	   $wpdb->query($sql1);
	$wpdb->query($sql2);
	$wpdb->query($sql3);
	$wpdb->query($sql4);
	$wpdb->query($sql5);
	//Значения по умолчанию для настроек магазина
	add_option('liqpay_shop_id', 'Не задано');
	add_option('liqpay_secret_key', 'Не задано');
	add_option('liqpay_status_url', 'http://myblog.loc/status');
	add_option('liqpay_code_expiration', '10');
		add_option('liqpay_merchant_id','');
		add_option('liqpay_signature_id','');
		add_option('liqpay_phone','');
		add_option('liqpay_domain',$_SERVER['SERVER_NAME']);
		add_option('liqpay_mail','');
		add_option('liqpay_ip_buyer','');
		add_option('liqpay_mail_sender','');
		add_option('liqpay_magazin','');
		add_option('liqpay_product_id','');
		add_option('liqpay_mail_buyer','');
		add_option('liqpay_gen_button_check_summa','');
		add_option('liqpay_check_disable_failure','');
		add_option('liqpay_search_order_id','');
		add_option('liqpay_search_date_begin','');
		add_option('liqpay_search_date_end','');
		add_option('show_fio','');
		add_option('show_skidka','');
		add_option('liqpay_code_expiration', '');
		add_option('liqpay_check_testmode', '');
		add_option('liqpay_komissiya', '');
		add_option('liqpay_lang', 'RU');
		add_option('liqpay_current_user', '');
		add_option('liqpay_result_url', '');
		update_option('liqpay_code_expiration', '10');		
		update_option('liqpay_check_disable_failure', '0');
		update_option('liqpay_search_order_id', '');
		update_option('liqpay_check_testmode', '0');
		update_option('show_fio', '0');
		update_option('show_skidka', '0');
		update_option('liqpay_komissiya', '3');
		update_option('liqpay_lang', 'ru');
		update_option('liqpay_result_url',  $_SERVER['SERVER_NAME']);
		update_option('liqpay_domain',$_SERVER['SERVER_NAME']);
}
register_activation_hook( __FILE__, 'liqpay_activate' );
function ip_adress( $atts, $content = null ) {
	$ip_adress = GetRealIp();
	return $ip_adress;
}
add_shortcode( 'ip', 'ip_adress' );
function GetRealIp()
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
function liqpay_styles_with_the_lot_secure()
{	// Регистрируем стили для плагина:
	wp_register_style( 'liqpay_base', plugins_url( '/css/liqpay.css', __FILE__ ) );
}
function liqpay_head_secure()
{
  wp_enqueue_style('liqpay_base', plugins_url( '/css/liqpay.css', __FILE__ ));
}
add_action( 'init', 'liqpay_head_secure');
if(is_admin()){
	  add_action('admin_menu', 'liqpay_options');
}
include "skidki.php";

?>