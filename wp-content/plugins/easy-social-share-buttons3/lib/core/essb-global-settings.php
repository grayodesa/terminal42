<?php

/**
 * Global Plugin Setup
 *
 * @package   EasySocialShareButtons
 * @author    AppsCreo
 * @link      http://appscreo.com/
 * @copyright 2016 AppsCreo
 * @since 3.4.1
 *
 */

class ESSBGlobalSettings {
	
	public static $legacy_class = false;
	public static $counter_total_text = "";
	public static $button_counter_hidden_till = "";
	public static $mycred_group = "";
	public static $mycred_points = "";
	public static $more_button_icon = "";
	public static $comments_address = "";
	public static $use_rel_me = false;
	public static $essb_encode_text = false;
	public static $essb_encode_url = false;
	public static $essb_encode_text_plus = false;
	public static $print_use_printfriendly = false;
	public static $pinterest_sniff_disable = false;
	public static $facebookadvanced = false;
	public static $facebookadvancedappid = "";
	public static $activate_ga_campaign_tracking = "";
	public static $twitter_message_optimize = false;
	public static $sidebar_pos = "";
	
	public static $mobile_networks_active = false;
	public static $mobile_networks = array();
	public static $mobile_networks_order_active = false;
	public static $mobile_networks_order = array();
		
	public static $telegram_alternative = false;
	
	public static $cache_runtime = false;
	
	public static $subscribe_function = "";
	public static $subscribe_link = "";
	public static $subscribe_content = "";
	
	public static $use_minified_css = false;
	public static $use_minified_js = false;
	
	public static $cached_counters_cache_mode = false;
	
	/**
	 * load
	 * 
	 * Load global plugin settings for single call use
	 * 
	 * @param array $options
	 * @since 3.4.1
	 */
	public static function load($options = array()) {
		self::$legacy_class = ESSBOptionValuesHelper::options_bool_value ( $options, 'legacy_class' );
		self::$counter_total_text = ESSBOptionValuesHelper::options_value ( $options, 'counter_total_text' );
		self::$button_counter_hidden_till = ESSBOptionValuesHelper::options_value ( $options, 'button_counter_hidden_till' );
		self::$mycred_group = ESSBOptionValuesHelper::options_value ( $options, 'mycred_group', 'mycred_default' );
		self::$mycred_points = ESSBOptionValuesHelper::options_value ( $options, 'mycred_points', '1' );
		self::$more_button_icon = ESSBOptionValuesHelper::options_value ( $options, 'more_button_icon' );
		self::$comments_address = ESSBOptionValuesHelper::options_value ( $options, 'comments_address' );
		self::$use_rel_me = ESSBOptionValuesHelper::options_bool_value ( $options, 'use_rel_me' );
		self::$essb_encode_text = ESSBOptionValuesHelper::options_bool_value ( $options, 'essb_encode_text' );
		self::$essb_encode_url = ESSBOptionValuesHelper::options_bool_value ( $options, 'essb_encode_url' );
		self::$essb_encode_text_plus = ESSBOptionValuesHelper::options_bool_value ( $options, 'essb_encode_text_plus' );
		self::$print_use_printfriendly = ESSBOptionValuesHelper::options_bool_value ( $options, 'print_use_printfriendly' );
		self::$pinterest_sniff_disable = ESSBOptionValuesHelper::options_bool_value ( $options, 'pinterest_sniff_disable' );
		self::$facebookadvanced = ESSBOptionValuesHelper::options_bool_value ( $options, 'facebookadvanced' );
		self::$facebookadvancedappid = ESSBOptionValuesHelper::options_value ( $options, 'facebookadvancedappid' );
		self::$activate_ga_campaign_tracking = ESSBOptionValuesHelper::options_value ( $options, 'activate_ga_campaign_tracking' );
		self::$twitter_message_optimize = ESSBOptionValuesHelper::options_bool_value ( $options, 'twitter_message_optimize' );
		self::$sidebar_pos = ESSBOptionValuesHelper::options_value($options, 'sidebar_pos');
		
		self::$telegram_alternative = ESSBOptionValuesHelper::options_bool_value($options, 'telegram_alternative');
		
		// @since 3.5 - runtime cache via WordPress functions
		self::$cache_runtime = ESSBOptionValuesHelper::options_bool_value($options, 'essb_cache_runtime');
		
		$personalized_networks = ESSBOptionValuesHelper::get_active_social_networks_by_position('mobile');
		$personalized_network_order = ESSBOptionValuesHelper::get_order_of_social_networks_by_position('mobile');
		
		// added in @since 3.4.2
		if (is_array($personalized_networks) && count($personalized_networks) > 0) {
			self::$mobile_networks = $personalized_networks;
			self::$mobile_networks_active = true;
		}
		
		if (is_array($personalized_network_order) && count($personalized_network_order) > 0) {
			self::$mobile_networks_order = $personalized_network_order;
			self::$mobile_networks_order_active = true;
		}
		
		self::$subscribe_function = ESSBOptionValuesHelper::options_value ( $options, 'subscribe_function' );
		self::$subscribe_link = ESSBOptionValuesHelper::options_value ( $options, 'subscribe_link' );
		self::$subscribe_content = ESSBOptionValuesHelper::options_value ( $options, 'subscribe_content' );
		
		self::$use_minified_css = ESSBOptionValuesHelper::options_bool_value($options, 'use_minified_css');
		self::$use_minified_js = ESSBOptionValuesHelper::options_bool_value($options, 'use_minified_js');
		
		// demo mode subscribe function
		if (isset($_REQUEST['essb_subscribe']) && ESSB3_DEMO_MODE) {
			self::$subscribe_function = $_REQUEST['essb_subscribe'];
		}
		
		self::$cached_counters_cache_mode = ESSBOptionValuesHelper::options_bool_value($options, 'cache_counter_refresh_cache');
	}
	
}