<?php

include "../../../../wp-load.php";

global $_POST, $_GET, $_REQUEST;

$options = get_option(  ESSB3_OPTIONS_NAME );

$exist_captcha = isset ( $options ['mail_captcha_answer'] ) ? $options ['mail_captcha_answer'] : '';
$mail_function_command = isset($options['mail_function_command']) ? $options['mail_function_command'] : '';
$mail_function_security = isset($options['mail_function_security']) ? $options['mail_function_security']:  '';
$mail_disable_editmessage = isset($options['mail_disable_editmessage']) ? $options['mail_disable_editmessage'] : '';

$from = isset($_REQUEST['from']) ? $_REQUEST['from'] : '';
if ($from == '') {
	$from = isset($_GET['from']) ? $_GET['from'] : '';
}
$to = isset($_REQUEST['to']) ? $_REQUEST['to'] : '';
if ($to == '') {
	$to = isset($_GET['to']) ? $_GET['to'] : '';
}

$sub = isset($_REQUEST['sub']) ? $_REQUEST['sub'] : '';
if ($sub== '') {
	$sub = isset($_GET['sub']) ? $_GET['sub'] : '';
}
$message = isset($_REQUEST['message']) ? $_REQUEST['message'] : '';
if ($message == '') {
	$message = isset($_GET['message']) ? $_GET['message'] : '';
}


$t = isset($_REQUEST['t']) ? $_REQUEST['t'] : '';
$u = isset($_REQUEST['u']) ? $_REQUEST['u'] : '';
$p = isset($_REQUEST['p']) ? $_REQUEST['p'] : '';
$img = isset($_REQUEST['img']) ? $_REQUEST['img'] : '';
$c = isset($_REQUEST['c']) ? $_REQUEST['c'] : '';



$valid_captcha = true;

if ($exist_captcha != '') {
	if ($c != $exist_captcha) {
		$valid_captcha = false;
	}
}

if (strlen($to) > 80) {
	$json = array("message" => "Incorrect recepient email");
	echo str_replace('\\/','/',json_encode($json));
	return;
}

$salt = isset($_REQUEST['salt']) ? $_REQUEST['salt'] : '';
$mail_salt_check = get_option(ESSB3_MAIL_SALT);

if ($mail_function_security == 'level2') {
	$salt = "salt";
	$mail_salt_check = "salt";
}

if ($salt != $mail_salt_check) {
	$json = array("message" => "Incorrect security key");
	echo str_replace('\\/','/',json_encode($json));
	return;
}


$message_subject = $options['mail_subject'];
$message_body = $options['mail_body'];

if ($sub != '') { $message_subject = $sub; }
if ($message != '') { $message_body = $message; }

//$message_body = esc_textarea (stripslashes ( $message_body ) );

if ($img != '') {
	$img = '<img src="'.$img.'" />';
}

$message_subject = preg_replace(array('#%%title%%#', '#%%siteurl%%#', '#%%permalink%%#', '#%%image%%#'), array($t, $u, $p, $img), $message_subject);
$message_body = preg_replace(array('#%%title%%#', '#%%siteurl%%#', '#%%permalink%%#', '#%%image%%#'), array($t, $u, $p, $img), $message_body);	

if ($sub != '') { $message_subject = $sub; }
if ($message != '') { $message_body = $message; }


// try to create post address as link

$message_body = str_replace($p, '<a href="'.$p.'">'.$p.'</a>', $message_body);
$message_body = str_replace($img, '<img src="'.$img.'"/>', $message_body);
// fixing the quotes issue
$message_body = stripslashes ( $message_body );

$message_body = str_replace('&', '&amp;', $message_body);


$copy_address = isset($options['mail_copyaddress']) ?  $options['mail_copyaddress'] : '';
$use_wpmandrill = isset($options['use_wpmandrill']) ? $options['use_wpmandrill'] : 'false';

$translate_mail_message_sent = isset($options['translate_mail_message_sent']) ? $options['translate_mail_message_sent'] : '';
$translate_mail_message_invalid_captcha = isset($options['translate_mail_message_invalid_captcha']) ? $options['translate_mail_message_invalid_captcha'] : '';
$translate_mail_message_error_send = isset($options['translate_mail_message_error_send']) ? $options['translate_mail_message_error_send'] : '';

/*$headers = "MIME-Version: 1.0" . "\r\n";
$headers .= "Content-type:text/html;charset=utf-8" . "\r\n";
$headers .= "From: <$from>\n";
if ($copy_address != '' ) {
	$headers .= "Bcc: $copy_address\n";
}*/

$headers  = 'MIME-Version: 1.0' . "\r\n";
$headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";

// Additional headers
// @1.3.2 - fix issue with double to address in mail headers
//$headers .= 'To: '.$to. '' . "\r\n";
$headers .= 'From: '.$from.'' . "\r\n";
if ($copy_address != '' ) { 
	$headers .= 'Bcc: '. $copy_address . "\r\n";
}

// Mail it
//$headers .= "Return-Path: <" . mysql_real_escape_string(trim($from)) . ">\n";
$message_body = str_replace("\r\n", "<br />", $message_body);

$json = array("message" => "");

if ($from != '' && $to != '' && $valid_captcha ){ 
//@wp_mail($to, $message_subject, $message_body, $headers);
	
	if ($use_wpmandrill == "true") {
		wpMandrill::mail($to, $message_subject, $message_body, $headers);
		
	}
	else {
		if ($mail_function_command == "wp") {
			wp_mail($to, $message_subject, $message_body, $headers);
		}
		else {
			mail($to, $message_subject, $message_body, $headers);
		}
	}
	
	
	$json['message'] = "Message sent!";
	if ($translate_mail_message_sent != '') {
		$json['message'] = $translate_mail_message_sent;
	}
}
else {
	$json ['message'] = "Error sending message!";
	if ($translate_mail_message_error_send != '') {
		$json['message'] = $translate_mail_message_error_send;
	}
	
	if (!$valid_captcha) {
		$json ['message'] = "Invalid Captcha code!";
		if ($translate_mail_message_invalid_captcha != '') {
			$json['message'] = $translate_mail_message_invalid_captcha;
		}
	}
}

echo str_replace('\\/','/',json_encode($json));
die();
?>