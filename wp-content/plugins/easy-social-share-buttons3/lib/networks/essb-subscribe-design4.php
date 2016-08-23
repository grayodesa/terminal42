<?php
if (!function_exists('essb_subscribe_form_design4')) {
	function essb_subscribe_form_design4($salt, $is_widget = false) {
		global $essb_options;
		
		$subscribe_mc_namefield = ESSBOptionValuesHelper::options_bool_value($essb_options, 'subscribe_mc_namefield4');
		
		// demo mode using name field
		$demo_mode_name = isset($_REQUEST['usename']) ? $_REQUEST['usename'] : '';
		if ($demo_mode_name == 'true') {
			$subscribe_mc_namefield = true;
		}
		
		$default_texts = array(
				"title" => __('Join our list', 'essb'),
				"text" => __('Subscribe to our mailing list and get interesting stuff and updates to your email inbox.', 'essb'),
				"email" => __('Enter your email here', 'essb'),
				"name" => __('Enter your name here', 'essb'),
				"button" => __('Sign Up Now', 'essb'),
				"footer" => __('We respect your privacy and take protecting it seriously', 'essb'),
				"success" => __('Thank you for subscribing.', 'essb'),
				"error" => __('Something went wrong.', 'essb')
		);
		
		$subscribe_mc_title = ESSBOptionValuesHelper::options_value($essb_options, 'subscribe_mc_title4');
		$subscribe_mc_text = ESSBOptionValuesHelper::options_value($essb_options, 'subscribe_mc_text4');
		$subscribe_mc_email = ESSBOptionValuesHelper::options_value($essb_options, 'subscribe_mc_email4');
		$subscribe_mc_name = ESSBOptionValuesHelper::options_value($essb_options, 'subscribe_mc_name4');
		$subscribe_mc_button = ESSBOptionValuesHelper::options_value($essb_options, 'subscribe_mc_button4');
		$subscribe_mc_footer = ESSBOptionValuesHelper::options_value($essb_options, 'subscribe_mc_footer4');
		$subscribe_mc_success = ESSBOptionValuesHelper::options_value($essb_options, 'subscribe_mc_success4');
		$subscribe_mc_error = ESSBOptionValuesHelper::options_value($essb_options, 'subscribe_mc_error4');
		
		$subscribe_mc_image3 = ESSBOptionValuesHelper::options_value($essb_options, 'subscribe_mc_image4');
		$subscribe_mc_imagealign3 = ESSBOptionValuesHelper::options_value($essb_options, 'subscribe_mc_imagealign4');
		
		if (empty($subscribe_mc_title)) $subscribe_mc_title = $default_texts['title'];
		if (empty($subscribe_mc_text)) $subscribe_mc_text = $default_texts['text'];
		if (empty($subscribe_mc_email)) $subscribe_mc_email = $default_texts['email'];
		if (empty($subscribe_mc_name)) $subscribe_mc_name = $default_texts['name'];
		if (empty($subscribe_mc_button)) $subscribe_mc_button = $default_texts['button'];
		if (empty($subscribe_mc_footer)) $subscribe_mc_footer = $default_texts['footer'];
		if (empty($subscribe_mc_success)) $subscribe_mc_success = $default_texts['success'];
		if (empty($subscribe_mc_error)) $subscribe_mc_error = $default_texts['error'];
		
		global $wp;
		$current_url = add_query_arg( $wp->query_string, '', home_url( $wp->request ) );
		
		$secure_nonce = wp_create_nonce('essb3_subscribe_nonce');
		$current_url = add_query_arg('essb3_subscribe_nonce', $secure_nonce, $current_url);
		
		$output = '<div class="essb-subscribe-form-content essb-subscribe-from-design4'.($is_widget ? " essb-subscribe-form-inwidget" :"").($subscribe_mc_imagealign3 == 'right' ? " essb-subscribe-from-left" : " essb-subscribe-form-right").'">';
		
		if ($subscribe_mc_imagealign3 == '' || $subscribe_mc_imagealign3 == 'left') {
			$output .= '<div class="essb-subscribe-form-content-subscribeholder">';
			// generating form output
			$output .= '<form action="'.add_query_arg('essb-malchimp-signup', '1', $current_url).'" method="post" class="essb-subscribe-from-content-form" id="essb-subscribe-from-content-form-mailchimp">';
				
			if ($subscribe_mc_namefield) {
				$output .= '<input class="essb-subscribe-form-content-name-field" type="text" value="" placeholder="'.$subscribe_mc_name.'" name="mailchimp_name">';
			}
				
			$output .= '<input class="essb-subscribe-form-content-email-field" type="text" value="" placeholder="'.$subscribe_mc_email.'" name="mailchimp_email">';
			$output .= '<input class="submit" name="submit" type="submit" value="'.$subscribe_mc_button.'" onclick="essb_ajax_subscribe(\''.$salt.'\');">';
			$output .= '</form>';
				
			$output .= '<div class="essb-subscribe-loader">
			<svg version="1.1" id="loader-1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" width="40px" height="40px" viewBox="0 0 50 50" style="enable-background:new 0 0 50 50;" xml:space="preserve">
			<path fill="#000" d="M43.935,25.145c0-10.318-8.364-18.683-18.683-18.683c-10.318,0-18.683,8.365-18.683,18.683h4.068c0-8.071,6.543-14.615,14.615-14.615c8.072,0,14.615,6.543,14.615,14.615H43.935z">
			<animateTransform attributeType="xml"
			attributeName="transform"
			type="rotate"
			from="0 25 25"
			to="360 25 25"
			dur="0.6s"
			repeatCount="indefinite"/>
			</path>
			</svg>
			</div>';
				
			$output .= '<p class="essb-subscribe-form-content-success essb-subscribe-form-result-message">'.$subscribe_mc_success.'</p>';
			$output .= '<p class="essb-subscribe-form-content-error essb-subscribe-form-result-message">'.$subscribe_mc_error.'</p>';
				
			$output .= '<div class="clear"></div>';
				
			$output .= '</div>';
		}
		
		$output .= '<div class="essb-subscribe-form-content-contentholder">';
		if ($subscribe_mc_image3 != '') {
			$output .= '<div class="essb-subscribe-form-content-imageholder"><img src="'.$subscribe_mc_image3.'" class="essb-subscribe-form-content-top-image-left"/></div>';
		}
		
		$output .= '<h4 class="essb-subscribe-form-content-title">'.$subscribe_mc_title.'</h4>';
		$output .= '<p class="essb-subscribe-form-content-text">'.$subscribe_mc_text.'</p>';
		
		$output .= '<p class="essb-subscribe-form-content-footer">'.$subscribe_mc_footer.'</p>';
		
		$output .= '</div>';
		
		
		if ($subscribe_mc_imagealign3 == 'right') {
			$output .= '<div class="essb-subscribe-form-content-subscribeholder">';
			// generating form output
			$output .= '<form action="'.add_query_arg('essb-malchimp-signup', '1', $current_url).'" method="post" class="essb-subscribe-from-content-form" id="essb-subscribe-from-content-form-mailchimp">';
		
			if ($subscribe_mc_namefield) {
				$output .= '<input class="essb-subscribe-form-content-name-field" type="text" value="" placeholder="'.$subscribe_mc_name.'" name="mailchimp_name">';
			}
		
			$output .= '<input class="essb-subscribe-form-content-email-field" type="text" value="" placeholder="'.$subscribe_mc_email.'" name="mailchimp_email">';
			$output .= '<input class="submit" name="submit" type="submit" value="'.$subscribe_mc_button.'" onclick="essb_ajax_subscribe(\''.$salt.'\');">';
			$output .= '</form>';
		
			$output .= '<div class="essb-subscribe-loader">
			<svg version="1.1" id="loader-1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" width="40px" height="40px" viewBox="0 0 50 50" style="enable-background:new 0 0 50 50;" xml:space="preserve">
			<path fill="#000" d="M43.935,25.145c0-10.318-8.364-18.683-18.683-18.683c-10.318,0-18.683,8.365-18.683,18.683h4.068c0-8.071,6.543-14.615,14.615-14.615c8.072,0,14.615,6.543,14.615,14.615H43.935z">
			<animateTransform attributeType="xml"
			attributeName="transform"
			type="rotate"
			from="0 25 25"
			to="360 25 25"
			dur="0.6s"
			repeatCount="indefinite"/>
			</path>
			</svg>
			</div>';
		
			$output .= '<p class="essb-subscribe-form-content-success essb-subscribe-form-result-message">'.$subscribe_mc_success.'</p>';
			$output .= '<p class="essb-subscribe-form-content-error essb-subscribe-form-result-message">'.$subscribe_mc_error.'</p>';
		
			$output .= '<div class="clear"></div>';
		
			$output .= '</div>';
		}
		
		$output .= '</div>';
		
		return $output;
	}
}