<?php
/**
 * EasySocialShareButtons CoreExtender: Shortcode Profiles
 *
 * @package   EasySocialShareButtons
 * @author    AppsCreo
 * @link      http://appscreo.com/
 * @copyright 2016 AppsCreo
 * @since 3.6
 *
 */

class ESSBCoreExtenderShortcodeProfiles {
	
	public static function parse_shortcode($atts, $options) {
		
		$sc_networks = isset($atts['networks']) ? $atts['networks'] : '';
		$sc_button_type = isset($atts['type']) ? $atts['type'] : 'square';
		$sc_button_size = isset($atts['size']) ? $atts['size'] : 'small';
		$sc_button_fill = isset($atts['style']) ? $atts['style'] : 'fill';
		$sc_nospace = isset($atts['nospace']) ? $atts['nospace'] : 'false';
		
		$sc_usetexts = isset($atts['allowtext']) ? $atts['allowtext'] : 'false';
		$sc_width = isset($atts['width']) ? $atts['width'] : '';
		
		$sc_nospace = ESSBOptionValuesHelper::unified_true($sc_nospace);
		$sc_usetexts = ESSBOptionValuesHelper::unified_true($sc_usetexts);
		
		$profile_networks = array();
		if ($sc_networks != '') {
			$profile_networks = explode(',', $sc_networks);
		}
		else {
			$profile_networks = ESSBOptionValuesHelper::advanced_array_to_simple_array(essb_available_social_profiles());
		}
		
		
		// prepare network values
		$sc_network_address = array();
		foreach ($profile_networks as $network) {
			$value = isset($atts[$network]) ? $atts[$network] : '';
				
			if (empty($value)) {
				$value = isset($atts['profile_'.$network]) ? $atts['profile_'.$network] : '';
			}
				
			if (empty($value)) {
				$value = ESSBOptionValuesHelper::options_value($options, 'profile_'.$network);
			}
				
			if (!empty($value)) {
				$sc_network_address[$network] = $value;
			}
		}
		
		$sc_network_texts = array();
		if ($sc_usetexts) {
			foreach ($profile_networks as $network) {
				$value = isset($atts[$network]) ? $atts[$network] : '';
					
				if (empty($value)) {
					$value = isset($atts['profile_text_'.$network]) ? $atts['profile_text_'.$network] : '';
				}
					
				if (empty($value)) {
					$value = ESSBOptionValuesHelper::options_value($options, 'profile_text_'.$network);
				}
					
				if (!empty($value)) {
					$sc_network_texts[$network] = $value;
				}
			}
		}
		
		// check if module is not activated yet
		if (!defined('ESSB3_SOCIALPROFILES_ACTIVE')) {
			include_once (ESSB3_PLUGIN_ROOT . 'lib/modules/social-profiles/essb-social-profiles.php');
			define('ESSB3_SOCIALPROFILES_ACTIVE', 'true');
			$template_url = ESSB3_PLUGIN_URL.'/assets/css/essb-profiles.css';
			essb_resource_builder()->add_static_footer_css($template_url, 'easy-social-share-buttons-profiles');
		}
		
		
		return ESSBSocialProfiles::generate_social_profile_icons($sc_network_address, $sc_button_type, $sc_button_size, $sc_button_fill,
				$sc_nospace, '', $sc_usetexts, $sc_network_texts, $sc_width);
	}
	
}