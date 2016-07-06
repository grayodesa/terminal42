<?php

class ESSBOptionValuesHelper {
	public static function options_value($optionsContainer, $param, $default = '') {
		return isset ( $optionsContainer [$param] ) ? $optionsContainer [$param]  : $default;
	}
	
	public static function options_bool_value($optionsContainer, $param) {
		$value = isset ( $optionsContainer [$param] ) ? $optionsContainer [$param]  : 'false';
	
		if ($value == "true") {
			return true;
		}
		else {
			return false;
		}
	
	}
	
	public static function is_active_module($module = '') {
		global $essb_options;
		
		$is_active = false;
		
		switch ($module) {
			case "sso":
				if (ESSBOptionValuesHelper::options_bool_value($essb_options, 'opengraph_tags') ||
				ESSBOptionValuesHelper::options_bool_value($essb_options, 'twitter_card') ||
				ESSBOptionValuesHelper::options_bool_value($essb_options, 'sso_google_author') ||
				ESSBOptionValuesHelper::options_bool_value($essb_options, 'sso_google_markup')) {
					$is_active = true;
				}
				break;
			case "ssanalytics":
				if (ESSBOptionValuesHelper::options_bool_value($essb_options, 'stats_active')) {
					$is_active = true;
				}
				break;
			case "mycred":
				if (ESSBOptionValuesHelper::options_bool_value($essb_options, 'mycred_activate')) {
					$is_active = true;
				}
				break;
			case "mycred_hook":
				if (ESSBOptionValuesHelper::options_bool_value($essb_options, 'mycred_activate_custom')) {
					$is_active = true;
				}
				break;
			case "aftershare":
				if (ESSBOptionValuesHelper::options_bool_value($essb_options, 'afterclose_active')) {
					$is_active = true;
				}
				break;
			case "imageshare":
				$positions = ESSBOptionValuesHelper::options_value($essb_options, 'button_position');
				
				if (is_array($positions)) {
					if (in_array('onmedia', $positions)) {
						$is_active = true;
					}
				}
				break;
			case "loveyou":
				$is_active = true;
				if (ESSBOptionValuesHelper::options_bool_value($essb_options, 'module_off_lv')) {
					$is_active = false;
				}
				break;
			case "socialprofiles":
				if (ESSBOptionValuesHelper::options_bool_value($essb_options, 'profiles_display')) {
					$is_active = true;
				}					
				break;
			case "socialfans":
				if (ESSBOptionValuesHelper::options_bool_value($essb_options, 'fanscounter_active')) {
					$is_active = true;
				}
				break;
			case "native":
				if (ESSBOptionValuesHelper::options_bool_value($essb_options, 'native_active')) {
					$is_active = true;
				}
				break;
			case "cachedynamic":
				if (ESSBOptionValuesHelper::options_bool_value($essb_options, 'essb_cache')) {
					$is_active = true;
				}
				break;
			case "cachestatic":
				if (ESSBOptionValuesHelper::options_bool_value($essb_options, 'essb_cache_static') || ESSBOptionValuesHelper::options_bool_value($essb_options, 'essb_cache_static_js')) {
					$is_active = true;
				}	
				break;
			case "precompiled":
				if (ESSBOptionValuesHelper::options_bool_value($essb_options, 'precompiled_resources')) {
					$is_active = true;
				}	
				break;
			case "metricslite":
				if (ESSBOptionValuesHelper::options_bool_value($essb_options, 'esml_active')) {
					$is_active = true;
				}
				break;
			case "ctt":
				$is_active = true;
				
				if (ESSBOptionValuesHelper::options_bool_value($essb_options, 'deactivate_ctt')) {
					$is_active = false;
				}
				break;
			case "topsocialposts":
				if (ESSBOptionValuesHelper::options_bool_value($essb_options, 'esml_top_posts_widget')) {
					$is_active = true;
				}
				break;
			case "cachedcounters":
				$counter_mode = ESSBOptionValuesHelper::options_value($essb_options, 'counter_mode');
				
				if ($counter_mode == "cached") {
					$is_active = true;
				}
				break;
			case "counterrecovery":
				if (ESSBOptionValuesHelper::options_bool_value($essb_options, 'counter_recover_active')) {
					$is_active = true;
				}
				break;
		}
		
		return $is_active;
	}
	
	
	public static function is_active_position_settings ($position = '') {
		global $essb_options;
		
		$result = false;

		$key = $position.'_activate';
		if (ESSBOptionValuesHelper::options_bool_value($essb_options, $key)) {
			$result = true;
		}
		
		return $result;
	
	}
	
	public static function apply_position_style_settings($postion, $basic_style) {
		global $essb_options;
		
		// global variables in pro mode that can be applied for position
		if (!defined('ESSB3_LIGHTMODE')) {
			if (ESSBOptionValuesHelper::options_value($essb_options, $postion.'_template') != "") {
				$basic_style['template'] = ESSBOptionValuesHelper::options_value($essb_options, $postion.'_template');
			}

			$basic_style['button_align'] = ESSBOptionValuesHelper::options_value($essb_options, $postion.'_button_pos');
			$basic_style['button_width'] = ESSBOptionValuesHelper::options_value($essb_options, $postion.'_button_width');
			$basic_style['button_width_fixed_value'] = ESSBOptionValuesHelper::options_value($essb_options, $postion.'_fixed_width_value');
			$basic_style['button_width_fixed_align'] = ESSBOptionValuesHelper::options_value($essb_options, $postion.'_fixed_width_align');
			$basic_style['button_width_full_container'] = ESSBOptionValuesHelper::options_value($essb_options, $postion.'_fullwidth_share_buttons_container');
			$basic_style['button_width_full_button'] = ESSBOptionValuesHelper::options_value($essb_options, $postion.'_fullwidth_share_buttons_correction');
			$basic_style['button_width_full_button_mobile'] = ESSBOptionValuesHelper::options_value($essb_options, $postion.'_fullwidth_share_buttons_correction_mobile');
			$basic_style['button_width_columns'] = ESSBOptionValuesHelper::options_value($essb_options, $postion.'_fullwidth_share_buttons_columns');

			$basic_style['fullwidth_align'] = ESSBOptionValuesHelper::options_value($essb_options, $postion.'_fullwidth_align');
			$basic_style['fullwidth_share_buttons_columns_align'] = ESSBOptionValuesHelper::options_value($essb_options, $postion.'_fullwidth_share_buttons_columns_align');
			
			// @since 3.0.3
			$more_button_icon = ESSBOptionValuesHelper::options_value($essb_options, $postion.'_more_button_icon');
			if ($more_button_icon != '') {
				$basic_style['more_button_icon'] = $more_button_icon;
			}
			
			// @since 3.3
			$more_button_func = ESSBOptionValuesHelper::options_value($essb_options, $postion.'_more_button_func');
			if ($more_button_func != '') {
				$basic_style['location_more_button_func'] = $more_button_func;
			}
			
			if (intval($basic_style['button_width_full_container']) == 0) {
				$basic_style['button_width_full_container'] = "100";
			}
			
			// @since 3.5 we add animations
			$position_animation = ESSBOptionValuesHelper::options_value($essb_options, $postion.'_css_animations');
			if (!empty($position_animation)) {
				$basic_style['button_animation'] = $position_animation;
			}
		}
		
		$basic_style['button_style'] = ESSBOptionValuesHelper::options_value($essb_options, $postion.'_button_style');
		$basic_style['nospace'] = ESSBOptionValuesHelper::options_value($essb_options, $postion.'_nospace');
		
		$basic_style['show_counter'] = ESSBOptionValuesHelper::options_bool_value($essb_options, $postion.'_show_counter');
		$basic_style['counter_pos'] = ESSBOptionValuesHelper::options_value($essb_options, $postion.'_counter_pos');
		$basic_style['total_counter_pos'] = ESSBOptionValuesHelper::options_value($essb_options, $postion.'_total_counter_pos');
				
		return $basic_style;
	}
	
	public static function apply_mobile_position_style_settings($postion, $basic_style) {
		global $essb_options;
	
		if (ESSBOptionValuesHelper::options_value($essb_options, $postion.'_template') != "") {
			$basic_style['template'] = ESSBOptionValuesHelper::options_value($essb_options, $postion.'_template');
		}
		
		if ($postion != 'sharebottom') {
			$basic_style['nospace'] = ESSBOptionValuesHelper::options_value($essb_options, $postion.'_nospace');
			$basic_style['show_counter'] = ESSBOptionValuesHelper::options_bool_value($essb_options, $postion.'_show_counter');
			$basic_style['counter_pos'] = ESSBOptionValuesHelper::options_value($essb_options, $postion.'_counter_pos');
			$basic_style['total_counter_pos'] = ESSBOptionValuesHelper::options_value($essb_options, $postion.'_total_counter_pos');
		}
		return $basic_style;
	}
	
	public static function apply_postbar_position_style_settings($postion, $basic_style) {
		global $essb_options;

		if (ESSBOptionValuesHelper::options_value($essb_options, $postion.'_template') != "") {
			$basic_style['template'] = ESSBOptionValuesHelper::options_value($essb_options, $postion.'_template');
		}
		
		$basic_style['nospace'] = ESSBOptionValuesHelper::options_value($essb_options, $postion.'_nospace');
		$basic_style['show_counter'] = ESSBOptionValuesHelper::options_bool_value($essb_options, $postion.'_show_counter');
		$basic_style['counter_pos'] = ESSBOptionValuesHelper::options_value($essb_options, $postion.'_counter_pos');
		$basic_style['total_counter_pos'] = 'hidden';
		$basic_style['button_style'] = ESSBOptionValuesHelper::options_value($essb_options, $postion.'_button_style');
		
		if ($basic_style['button_style'] == 'recommended') {
			$basic_style['button_style'] = 'icon';
		}
		
		return $basic_style;
	}
	
	public static function apply_point_position_style_settings($postion, $basic_style) {
		global $essb_options;
		
		// point setup to select best display values
		$point_display_style = ESSBOptionValuesHelper::options_value($essb_options, 'point_style', 'simple');
		$is_demo_advanced = false;
		if (ESSB3_DEMO_MODE) {
			$demo_style = isset($_REQUEST['point_style']) ? $_REQUEST['point_style'] : '';
			if ($demo_style != '') {
				$point_display_style = $demo_style;
				$is_demo_advanced = true;
			}
		}
	
		if (ESSBOptionValuesHelper::options_value($essb_options, $postion.'_template') != "") {
			$basic_style['template'] = ESSBOptionValuesHelper::options_value($essb_options, $postion.'_template');
		}
	
		$basic_style['nospace'] = ESSBOptionValuesHelper::options_value($essb_options, $postion.'_nospace');
		$basic_style['show_counter'] = ESSBOptionValuesHelper::options_bool_value($essb_options, $postion.'_show_counter');
		$basic_style['counter_pos'] = ESSBOptionValuesHelper::options_value($essb_options, $postion.'_counter_pos');
		$basic_style['total_counter_pos'] = 'hidden';
		$basic_style['button_style'] = ESSBOptionValuesHelper::options_value($essb_options, $postion.'_button_style');
	
		if ($basic_style['button_style'] == 'recommended') {
			if ($point_display_style == 'simple') {
				$basic_style['button_style'] = 'icon';
			}
			else {
				$basic_style['button_style'] = 'button';
			}
		}
		
		$basic_style['button_width'] = "column";
		$basic_style['button_width_columns'] = "1";
		
		// specific display styling
		if ($point_display_style == 'simple') {
			if ($basic_style['counter_pos'] == 'insidename' || $basic_style['counter_pos'] == 'insidebeforename') {
				$basic_style['counter_pos'] = 'inside';
			}
			
			$basic_style['button_width'] = 'fixed';
			$basic_style['button_width_fixed_value'] = '30';
			$basic_style['button_width_fixed_align'] = 'center';
				
			
			if ($basic_style['show_counter'] && ($basic_style['counter_pos'] == 'inside' || $basic_style['counter_pos'] == 'bottom')) {
				$basic_style['button_style'] = 'button';
				
				if ($basic_style['counter_pos'] == 'inside') {
					$basic_style['button_width_fixed_value'] = '65';
					$basic_style['button_width_fixed_align'] = 'right';
				}
			}
		}
	
		if ($is_demo_advanced) {
			$basic_style['counter_pos'] = 'insidename';
		}
		
		return $basic_style;
	}
	
	public static function get_active_social_networks_by_position($position) {
		global $essb_options;
		
		$result = array();
		
		$result = ESSBOptionValuesHelper::options_value($essb_options, $position.'_networks');
		if (!is_array($result)) { $result = array(); }
		
		return $result;
	}
	
	public static function get_order_of_social_networks_by_position($position) {
		global $essb_options;
		
		$ordered_list = array();
		
		$result = ESSBOptionValuesHelper::options_value($essb_options, $position.'_networks_order');
		if (!is_array($result)) {
			$result = array();
		}
		
		foreach ($result as $text_values) {
			$key_array = explode('|', $text_values);
			$network_key = $key_array[0];
			
			$ordered_list[] = $network_key;
		}
		
		return $ordered_list;
		
	}
	
	public static function apply_position_network_names($position, $network_names) {
		global $essb_options, $essb_networks;

		foreach ($essb_networks as $key => $object) {
			$search_for = $position."_".$key."_name";
			$user_network_name = ESSBOptionValuesHelper::options_value($essb_options, $search_for);
			if ($user_network_name != '') {
				$network_names[$key] = $user_network_name;
			}
		}
		
		return $network_names;
	}
	
	public static function advanced_array_to_simple_array($values) {
		$new = array();
		
		foreach ($values as $key => $text) {
			$new[] = $key;
		}
		
		return $new;
	}
	
	public static function unified_true($value) {
		$result = '';
		
		if ($value == 'true' || $value == 'yes') {
			$result = true;
		}
		else {
			$result = false;		
		}
		
		return $result;
	}	
}

?>