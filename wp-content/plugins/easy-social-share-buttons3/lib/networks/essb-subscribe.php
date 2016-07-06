<?php

/**
 * Subsctibe Button Class
 *
 * @since 3.6
 *
 * @package EasySocialShareButtons
 * @author  appscreo <http://codecanyon.net/user/appscreo/portfolio>
 */

class ESSBNetworks_Subscribe {
	private static $version = "1.0";	
	public static $assets_registered = false;
	
	
	public static function register_assets() { 
		
		if (!self::$assets_registered) {
			essb_resource_builder()->add_static_resource_footer(ESSB3_PLUGIN_URL .'/assets/js/essb-subscribe'.(ESSBGlobalSettings::$use_minified_js ? ".min": "").'.js', 'easy-social-share-buttons-subscribe', 'js');
			essb_resource_builder()->add_static_resource_footer(ESSB3_PLUGIN_URL .'/assets/css/essb-subscribe'.(ESSBGlobalSettings::$use_minified_css ? ".min": "").'.css', 'easy-social-share-buttons-subscribe', 'css');
			self::$assets_registered = true;
		}
	}
	
	public static function draw_subscribe_form($position, $salt) {
		$output = '';
		$popup_mode = ($position != 'top' && $position != 'bottom' && $position != 'shortcode') ? true : false;
		
		$output .= '<div class="essb-subscribe-form essb-subscribe-form-'.$salt.($popup_mode ? " essb-subscribe-form-popup": " essb-subscribe-form-inline").'" data-popup="'.$popup_mode.'" style="display: none;">';
				
		if (ESSBGlobalSettings::$subscribe_function == "form") {
			$output .= do_shortcode(ESSBGlobalSettings::$subscribe_content);
		}
		else {
			$output .= self::draw_mailchimp_subscribe($salt);
		}
		
		if ($popup_mode) {
			$output .= '<button type="button" class="essb-subscribe-form-close" onclick="essb_subscribe_popup_close(\''.$salt.'\');">x</button>';
		}
		
		$output .= '</div>';
		
		if ($popup_mode) {
			$output .= '<div class="essb-subscribe-form-overlay essb-subscribe-form-overlay-'.$salt.'" onclick="essb_subscribe_popup_close(\''.$salt.'\');"></div>';
		}
		
		if (!self::$assets_registered) {
			self::register_assets();
		}
		
		return $output;
	}
	
	public static function draw_inline_subscribe_form($mode = '') {
		if (empty($mode)) $mode = ESSBGlobalSettings::$subscribe_function;
		$salt = mt_rand();
		
		$output = '<div class="essb-subscribe-form essb-subscribe-form-'.$salt.' essb-subscribe-form-inline">';
				
		if ($mode == "form") {
			$output .= do_shortcode(ESSBGlobalSettings::$subscribe_content);
		}
		else {
			$output .= self::draw_mailchimp_subscribe($salt);
		}
		
		$output .= '</div>';
		
		if (!self::$assets_registered) {
			self::register_assets();
		}
		
		return $output;
	}
	
	public static function draw_mailchimp_subscribe($salt) {
		global $essb_options;
		
		$default_texts = array(
				"title" => __('Join our list', 'essb'),
				"text" => __('Subscribe to our mailing list and get interesting stuff and updates to your email inbox.', 'essb'),
				"email" => __('Enter your email here', 'essb'),
				"button" => __('Sign Up Now', 'essb'),
				"footer" => __('We respect your privacy and take protecting it seriously', 'essb'),
				"success" => __('Thank you for subscribing.', 'essb'),
				"error" => __('Something went wrong.', 'essb')
				);
		
		$subscribe_mc_title = ESSBOptionValuesHelper::options_value($essb_options, 'subscribe_mc_title');
		$subscribe_mc_text = ESSBOptionValuesHelper::options_value($essb_options, 'subscribe_mc_text');
		$subscribe_mc_email = ESSBOptionValuesHelper::options_value($essb_options, 'subscribe_mc_email');
		$subscribe_mc_button = ESSBOptionValuesHelper::options_value($essb_options, 'subscribe_mc_button');
		$subscribe_mc_footer = ESSBOptionValuesHelper::options_value($essb_options, 'subscribe_mc_footer');
		$subscribe_mc_success = ESSBOptionValuesHelper::options_value($essb_options, 'subscribe_mc_success');
		$subscribe_mc_error = ESSBOptionValuesHelper::options_value($essb_options, 'subscribe_mc_error');
		
		if (empty($subscribe_mc_title)) $subscribe_mc_title = $default_texts['title'];
		if (empty($subscribe_mc_text)) $subscribe_mc_text = $default_texts['text'];
		if (empty($subscribe_mc_email)) $subscribe_mc_email = $default_texts['email'];
		if (empty($subscribe_mc_button)) $subscribe_mc_button = $default_texts['button'];
		if (empty($subscribe_mc_footer)) $subscribe_mc_footer = $default_texts['footer'];
		if (empty($subscribe_mc_success)) $subscribe_mc_success = $default_texts['success'];
		if (empty($subscribe_mc_error)) $subscribe_mc_error = $default_texts['error'];
		
		global $wp;
		$current_url = add_query_arg( $wp->query_string, '', home_url( $wp->request ) );
		
		$secure_nonce = wp_create_nonce('essb3_subscribe_nonce');
		$current_url = add_query_arg('essb3_subscribe_nonce', $secure_nonce, $current_url);
		
		$output = '<div class="essb-subscribe-form-content">';
		$output .= '<h4 class="essb-subscribe-form-content-title">'.$subscribe_mc_title.'</h4>';
		$output .= '<p class="essb-subscribe-form-content-text">'.$subscribe_mc_text.'</p>';
		
		// generating form output
		$output .= '<form action="'.add_query_arg('essb-malchimp-signup', '1', $current_url).'" method="post" class="essb-subscribe-from-content-form" id="essb-subscribe-from-content-form-mailchimp">';
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
		$output .= '<p class="essb-subscribe-form-content-footer">'.$subscribe_mc_footer.'</p>';
		
		$output .= '</div>';
		
		return $output;
	}
}