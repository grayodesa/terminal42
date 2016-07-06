<?php
if (!function_exists('essb_rs_css_build_imageshare_customizer')) {
	function essb_rs_css_build_imageshare_customizer() {
		global $essb_options;
		
		$is_active = ESSBOptionValuesHelper::options_bool_value($essb_options, 'activate_imageshare_customizer');
		if (!$is_active) {
			return '';
		}
		$snippet = '';
		$listOfNetworksAdvanced = array( "facebook" => "Facebook", "twitter" => "Twitter", "google" => "Google", "linkedin" => "LinkedIn", "pinterest" => "Pinterest", "tumblr" => "Tumblr", "reddit" => "Reddit", "digg" => "Digg", "delicious" => "Delicious", "vkontakte" => "VKontakte", "odnoklassniki" => "Odnoklassniki");
		
		foreach ($listOfNetworksAdvanced as $network => $title) {
			$color_isset = ESSBOptionValuesHelper::options_value($essb_options, 'imagecustomizer_'.$network);
			if ($color_isset != '') {
				$snippet .= ('.essbis .essbis-'.$network.'-btn { background-color: '.$color_isset.' !important; }');
				$snippet .= ('.essbis .essbis-'.$network.'-btn:hover { background-color: '.$color_isset.' !important; }');
			}
		}
		return $snippet;
	}
}