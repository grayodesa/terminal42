<?php
function liqpay_skidki_page()	{
	//Добавление Скидки	
	echo "<h3>Скидки</h3>";	
	//Изменение информации о товаре	
	echo "<h3>Список пользователей</h3>";	
	liqpay_spisok_users();	
}
//Изменение информации об услуге
function liqpay_spisok_users() {
	global $wpdb,$table_prefix;

	$table_skidki = $table_prefix.'liqpay_skidki';	

	if ( isset($_POST['liqpay_skidki_update_btn']) ) {

	$users = $wpdb->get_results("SELECT ID,user_login, display_name, user_nicename from $wpdb->users ORDER BY user_login ASC");
	$n = count($users);
	for($i=0;$i<$n;$i++){
		$users_find = $wpdb->get_row($wpdb->prepare("SELECT id, users_name, users_id, users_skidka from $table_skidki where users_id = %s",$users[$i]->ID));
   if (!count($users_find))
   {
	$wpdb->insert( $table_skidki,  
			array( 'users_id' => $users[$i]->ID,  'users_name' => $users[$i]->display_name, 'users_skidka' => $_POST['liqpay_skidki_users_display_name1']),  
			array( '%d', '%s', '%d')  
			);
   }
	}
	}	if ( isset($_POST['liqpay_skidki_save_btn']) ) {
$table_skidki = $table_prefix.'liqpay_skidki';		
$skidka_users_id = $_POST['liqpay_skidki_users_id'];
$skidka = $_POST['liqpay_skidki_users_display_name1'];
	$users = $wpdb->get_row($wpdb->prepare("SELECT id, users_name, users_id, users_skidka from $table_skidki where users_id = %s",$skidka_users_id));
$id = $users->id;
$user_id = $users->users_id;
$user_name = $users->users_name;
$wpdb->update($table_skidki,array('users_id' => $user_id,  'users_name' => $user_name, 'users_skidka' => $skidka),array( 'id' =>  $id ),array( '%d', '%s', '%d'),array( '%d' ));
}
if ( isset($_POST['liqpay_skidki_delete_btn']) ) {
$skidka_users_id = $_POST['liqpay_skidki_users_id'];
$wpdb->query("DELETE FROM $table_skidki WHERE users_id = $skidka_users_id "); 			
}
	//Вывод формы информации по товарам
	$uslugi = $wpdb->get_results("SELECT users_id,users_name, users_skidka from $table_skidki ORDER BY users_name ASC");
$n = count($uslugi);
	 echo "	<table >	
			<tr>
				<td></td>				
				<td><input disabled class='textarea1' type='text' name='liqpay_skidki_users_id21' value='Имя пользователя' /></td>				
				<td><input  style='width: 50px;' disabled class='textarea1' type='text' name='liqpay_skidki_users_id31' value='Скидка' /></td>
				<td></td>
			</tr>	
		 </table>";
for($i=0;$i<$n;$i++){
echo " <form name='liqpay_skidki_setup' method='post' action='".$_SERVER['REQUEST_URI']."&amp;updated=true'>	";
	 echo "	<table >	
			<tr>
				<td><input style='width: 30px;' readonly class='textarea1' type='text' name='liqpay_skidki_users_id' value='".$uslugi[$i]->users_id."' /></td>				
				<td><input readonly class='textarea1' type='text' name='liqpay_skidki_users_display_name' value='".$uslugi[$i]->users_name."' /></td>				
				<td><input style='width: 50px;' class='textarea' type='text' name='liqpay_skidki_users_display_name1' value='".$uslugi[$i]->users_skidka."' /></td>
				<td><input class='textarea' type='submit' name='liqpay_skidki_save_btn' value='Записать скидку в БД' /></td>	
				<td><input class='textarea' type='submit' name='liqpay_skidki_delete_btn' value='Удалить пользователя из БД' /></td>					
			</tr>	
		 </table>
	 </form>	";
    }
echo " <form name='liqpay_skidki_setup' method='post' action='".$_SERVER['REQUEST_URI']."?page=liqpay_skidki_page&amp;updated=true'>	";
echo "<input class='btn' type='submit' name='liqpay_skidki_update_btn' value='Загрузить список пользователей' style='width:240px; height:25px'/>			
		</td>
 </form>	";	
}
function vivod_skidki(){
	global $wpdb,$table_prefix;
	global $user_identity, $current_user;  
	get_currentuserinfo();
	$table_skidki = $table_prefix.'liqpay_skidki';
	//Вывод формы информации по товарам
	$users = $wpdb->get_row($wpdb->prepare("SELECT id, users_name, users_id, users_skidka from $table_skidki where users_id = %d",$current_user->ID));
if ($users->users_skidka > 0){
	//echo "<span style='color: red;'>< Ваша скидка составляет ".$users->users_skidka."  % ></span>";
		$skidka =  "<p><span style='color: red;  display: inline-block;'>< Ваша скидка составляет ".$users->users_skidka."  % ></span></p>";
}
else 
    $skidka = '';
return $skidka;
}
function vivod_skidki2(){
	global $wpdb,$table_prefix;
	global $user_identity, $current_user;  
	get_currentuserinfo();
	$table_skidki = $table_prefix.'liqpay_skidki';
	//Вывод формы информации по товарам
	$users = $wpdb->get_row($wpdb->prepare("SELECT id, users_name, users_id, users_skidka from $table_skidki where users_id = %d",$current_user->ID));
	if ($users)
	 return $users->users_skidka;
	else
	 return false;
}
?>