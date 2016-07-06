<?php
if (!function_exists('essb_rs_js_build_generate_popup_mailform')) {
	function essb_rs_js_build_generate_popup_mailform() {
		global $essb_options;
		$options = $essb_options;
			
		$salt = mt_rand ();
		$mailform_id = 'essb_mail_from_'.$salt;
		
		$mail_salt_check = get_option(ESSB3_MAIL_SALT);
		
		$translate_mail_title = isset($options['translate_mail_title']) ? $options['translate_mail_title'] : '';
		$translate_mail_email = isset($options['translate_mail_email']) ? $options['translate_mail_email'] : '';
		$translate_mail_recipient = isset($options['translate_mail_recipient']) ? $options['translate_mail_recipient'] : '';
		$translate_mail_subject = isset($options['translate_mail_subject']) ? $options['translate_mail_subject'] : '';
		$translate_mail_message = isset($options['translate_mail_message']) ? $options['translate_mail_message'] : '';
		$translate_mail_cancel = isset($options['translate_mail_cancel']) ? $options['translate_mail_cancel'] : '';
		$translate_mail_send = isset($options['translate_mail_send']) ? $options['translate_mail_send'] : '';
		
		$mail_disable_editmessage = isset($options['mail_disable_editmessage']) ? $options['mail_disable_editmessage'] : 'false';
		
		$mail_edit_readonly = "";
		if ($mail_disable_editmessage == "true") {
			$mail_edit_readonly = ' readonly="readonly"';
		}
		
		$mail_captcha = isset($options['mail_captcha']) ? $options['mail_captcha'] : '';
		$mail_captcha_answer = isset($options['mail_captcha_answer']) ? $options['mail_captcha_answer'] : '';
		
		$captcha_html = '';
		if ($mail_captcha != '' && $mail_captcha_answer != '') {
			$captcha_html = '\'<div class="vex-custom-field-wrapper"><strong>'.$mail_captcha.'</strong></div><input name="captchacode" type="text" placeholder="Captcha Code" />\'+';
		}
		
		
		$siteurl = ESSB3_PLUGIN_URL. '/';
		
		$html = 'function essb_mailer(oTitle, oMessage, oSiteTitle, oUrl, oImage, oPermalink) {
		vex.defaultOptions.className = \'vex-theme-os\';
		vex.dialog.open({
		message: \''.($translate_mail_title != '' ? $translate_mail_title : 'Share this with a friend').'\',
		input: \'\' +
		\'<div class="vex-custom-field-wrapper"><strong>'. ($translate_mail_email != '' ? $translate_mail_email : 'Your Email').'</strong></div>\'+
		\'<input name="emailfrom" type="text" placeholder="'. ($translate_mail_email != '' ? $translate_mail_email : 'Your Email').'" required />\' +
		\'<div class="vex-custom-field-wrapper"><strong>'.($translate_mail_recipient != '' ? $translate_mail_recipient : 'Recipient Email'). '</strong></div>\'+
		\'<input name="emailto" type="text" placeholder="'.($translate_mail_recipient != '' ? $translate_mail_recipient : 'Recipient Email'). '" required />\' +
		\'<div class="vex-custom-field-wrapper" style="border-bottom: 1px solid #aaa !important; margin-top: 10px;"><h3></h3></div>\'+
		\'<div class="vex-custom-field-wrapper" style="margin-top: 10px;"><strong>'.($translate_mail_subject != '' ? $translate_mail_subject : 'Subject').'</strong></div>\'+
		\'<input name="emailsubject" type="text" placeholder="Subject" required value="\'+oTitle+\'" />\' +
		\'<div class="vex-custom-field-wrapper" style="margin-top: 10px;"><strong>'.($translate_mail_message != '' ? $translate_mail_message : 'Message').'</strong></div>\'+
		\'<textarea name="emailmessage" placeholder="Message" required" rows="6" '.$mail_edit_readonly.'>\'+oMessage+\'</textarea>\' +
		'.$captcha_html. '
		\'\',
		buttons: [
		jQuery.extend({}, vex.dialog.buttons.YES, { text: \''.($translate_mail_send != '' ? $translate_mail_send : 'Send').'\' }),
		jQuery.extend({}, vex.dialog.buttons.NO, { text: \''.($translate_mail_cancel != '' ? $translate_mail_cancel : 'Cancel').'\' })
		],
		callback: function (data) {
		if (data.emailfrom && typeof(data.emailfrom) != "undefined") {
		var c = typeof(data.captchacode) != "undefined" ? data.captchacode : "";
		essb_sendmail_ajax'.$salt.'(data.emailfrom, data.emailto, data.emailsubject, data.emailmessage, c, oSiteTitle, oUrl, oImage, oPermalink);
		}
		}
		
		});
		};
		function essb_sendmail_ajax'.$salt.'(emailfrom, emailto, emailsub, emailmessage, c, oSiteTitle, oUrl, oImage, oPermalink) {
		jQuery.post(\'' . ESSB3_PLUGIN_URL . '/public/essb-mail.php\', {
		\'from\': emailfrom,
		\'to\': emailto,
		\'sub\': emailsub,
		\'message\': emailmessage,
		\'t\': oSiteTitle,
		\'u\': oUrl,
		\'img\': oImage,
		\'p\': oPermalink,
		\'c\': c,
		\'salt\': \''.$mail_salt_check.'\'
		}, function (data) {
		console.log(data);
		if (data) {
		alert(data.message);
		}},\'json\');
		};
		';
		
		return $html;
	}
}