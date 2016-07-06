<?php

class ESSBSocialProfiles {
	private static $instance = null;
	
	public static function get_instance() {
	
		if ( null == self::$instance ) {
			self::$instance = new self;
		}
	
		return self::$instance;
	
	} // end get_instance;
	
	function __construct() {
		global $essb_options;
		
		$is_active = false;
		
		if (ESSBOptionValuesHelper::options_bool_value($essb_options, 'profiles_display')) {
			$profiles_display_position = ESSBOptionValuesHelper::options_value($essb_options, 'profiles_display_position');
			
			if ($profiles_display_position != 'widget') {
				$is_active = true;
				
				if (ESSBOptionValuesHelper::options_bool_value($essb_options, 'profiles_mobile_deactivate')) {
					$mobile = new ESSB_Mobile_Detect();
					if ($mobile->isMobile()) {
						$is_active = false;
					}
				}
			}
		}
		
		
		if ($is_active) {
			add_action('wp_footer', array($this, 'display_profiles'));
		}
	}	
	
	function display_profiles() {
		global $essb_options;
		
		if (ESSBCoreHelper::is_module_deactivate_on('profiles')) {
			return "";
		}
		
		$profiles_display_position = ESSBOptionValuesHelper::options_value($essb_options, 'profiles_display_position');
		$profiles_button_type = ESSBOptionValuesHelper::options_value($essb_options, 'profiles_button_type');
		$profiles_button_size = ESSBOptionValuesHelper::options_value($essb_options, 'profiles_button_size');
		$profiles_nospace = ESSBOptionValuesHelper::options_bool_value($essb_options, 'profiles_nospace');
		$profiles_button_fill = ESSBOptionValuesHelper::options_value($essb_options, 'profiles_button_fill');
		
		$profiles_order = ESSBOptionValuesHelper::options_value($essb_options, 'profiles_order');
		
		// @new version 3.0.4
		$profiles_allowtext = ESSBOptionValuesHelper::options_bool_value($essb_options, 'profiles_allowtext');
		$profiles_width = ESSBOptionValuesHelper::options_value($essb_options, 'profiles_width');
		
		if (!is_array($profiles_order)) {
			$profiles_order = array();
			foreach (essb_available_social_profiles() as $network => $text) {
				$profiles_order[] = $network;
			}
		}
		
		$profiles = array();
		foreach ($profiles_order as $network) {
			$value_address = ESSBOptionValuesHelper::options_value($essb_options, 'profile_'.$network);
			
			if (!empty($value_address)) {
				$profiles[$network] = $value_address;
			}
		}		
		
		$profiles_texts = array ();
		if ($profiles_allowtext) {
			foreach ( $profiles_order as $network ) {
				$value_address = ESSBOptionValuesHelper::options_value ( $essb_options, 'profile_text_' . $network );
				
				if (! empty ( $value_address )) {
					$profiles_texts [$network] = $value_address;
				}
			}
		}
		
		echo $this->generate_social_profile_icons($profiles, $profiles_button_type, $profiles_button_size, $profiles_button_fill,
				$profiles_nospace, $profiles_display_position, $profiles_allowtext, $profiles_texts, $profiles_width);
	}
	
	public static function generate_social_profile_icons($profiles = array(), $button_type = 'square', 
			$button_size = 'small', $button_fill = 'colored', $nospace = true, $position = '', $profiles_text = false, 
			$profiles_texts = array(), $button_width = '') {
		
		$output = "";
		
		
		$nospace_class = ($nospace) ? " essb-profiles-nospace" : "";
		$position_classs = (!empty($position)) ? " essb-profiles-".$position : "";
		
		if (!empty($position)) {
			if ($position != "left" && $position != "right") {
				$position_classs .= " essb-profiles-horizontal";
			}
 		}
 		
 		$single_width = "";
 		// @since 3.0.4
 		if (!$profiles_text) {
 			$button_width = "";
 		}
 		
 		if (!empty($button_width)) {
 			if (strpos($button_width, 'px') === false && strpos($button_width, '%') === false) {
 				$button_width .= 'px';
 			}
 			
 			$button_width = ' style="width:'.$button_width.'; display: inline-block;"';
 			$single_width = ' style="width:100%"';
 		}
		
		$output .= sprintf('<div class="essb-profiles essb-profiles-%1$s essb-profiles-size-%2$s%3$s%4$s">', $button_type, $button_size,
				$nospace_class, $position_classs);
		
		$output .= '<ul class="essb-profile">';
				
		
		foreach ($profiles as $network => $address) {
			
			if ($profiles_text) {
				$text = isset($profiles_texts[$network]) ? $profiles_texts[$network] : '';
				
				if (!empty($text)) {
					$text = '<span class="essb-profile-text">'.$text.'</span>';
				}
				
				$output .= sprintf('<li class="essb-single-profile" %6$s><a href="%1$s" target="_blank" rel="nofollow" class="essb-profile-all essb-profile-%2$s-%3$s" %5$s><span class="essb-profile-icon essb-profile-%2$s"></span>%4$s</a></li>', $address, $network, $button_fill, $text, $single_width, $button_width);
			}
			else {
				$output .= sprintf('<li class="essb-single-profile"><a href="%1$s" target="_blank" rel="nofollow" class="essb-profile-all essb-profile-%2$s-%3$s"><span class="essb-profile-icon essb-profile-%2$s"></span></a></li>', $address, $network, $button_fill);
			}
		}
		
		$output .= '</ul>';
		$output .= "</div>";

		return $output;
	}
	
}

?>