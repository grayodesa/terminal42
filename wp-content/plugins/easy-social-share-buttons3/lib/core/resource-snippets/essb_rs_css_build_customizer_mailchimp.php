<?php
if (! function_exists ( 'essb_rs_css_build_customizer_mailchimp' )) {
	function essb_rs_css_build_customizer_mailchimp() {
		global $essb_options;
		
		$global_bgcolor = isset ( $essb_options ['customizer_subscribe_bgcolor'] ) ? $essb_options ['customizer_subscribe_bgcolor'] : '';
		$global_textcolor = isset ( $essb_options ['customizer_subscribe_textcolor'] ) ? $essb_options ['customizer_subscribe_textcolor'] : '';
		$global_hovercolor = isset ( $essb_options ['customizer_subscribe_hovercolor'] ) ? $essb_options ['customizer_subscribe_hovercolor'] : '';
		$global_hovertextcolor = isset ( $essb_options ['customizer_subscribe_hovertextcolor'] ) ? $essb_options ['customizer_subscribe_hovertextcolor'] : '';
		$customizer_subscribe_emailcolor = isset($essb_options['customizer_subscribe_emailcolor']) ? $essb_options['customizer_subscribe_emailcolor'] : '';
		$customizer_subscribe_noborder = isset($essb_options['customizer_subscribe_noborder']) ? $essb_options['customizer_subscribe_noborder'] : '';
		
		$snippet = '';
		
		if ($global_bgcolor != '') {
			$snippet .= '.essb-subscribe-form-content { background-color: ' . $global_bgcolor . '!important;}';
		}
		if ($global_textcolor != '') {
			$snippet .= '.essb-subscribe-form-content { color: ' . $global_textcolor . '!important;}';
		}
		
		if ($global_hovercolor != '') {
			$snippet .= '.essb-subscribe-form-content { border-top: 3px solid ' . $global_hovercolor . '!important; }';
			$snippet .= '.essb-subscribe-form-content-title:after { background: ' . $global_hovercolor . '!important;}';
			$snippet .= '.essb-subscribe-form-content input.submit { background: ' . $global_hovercolor . '!important;}';
			$snippet .= '.essb-subscribe-form-content input.submit:hover { background: ' . essb_rs_adjust_brightness($global_hovercolor, essb_rs_light_or_dark($global_hovercolor)) . '!important;}';
			$snippet .= '.essb-subscribe-form-content input.submit { border-bottom: 3px solid ' . essb_rs_adjust_brightness($global_hovercolor, essb_rs_light_or_dark($global_hovercolor, 50, -50)) . '!important;}';
			$snippet .= '.essb-subscribe-loader svg path, .essb-subscribe-loader svg rect { fill: ' . $global_hovercolor . '!important; }';
		}
		
		if ($global_hovertextcolor != '') {
			$snippet .= '.essb-subscribe-form-content input.submit, .essb-subscribe-form-content input.submit:hover { color: ' . $global_hovertextcolor . '!important;}';
		}
		
		if ($customizer_subscribe_emailcolor != '') {
			$snippet .= '.essb-subscribe-form-content input.essb-subscribe-form-content-email-field { background: '.$customizer_subscribe_emailcolor.'!important; color: '.essb_rs_adjust_brightness($customizer_subscribe_emailcolor, essb_rs_light_or_dark($customizer_subscribe_emailcolor, 150, -150)).'!important;}';
			$snippet .= '.essb-subscribe-form-content .essb-subscribe-form-content-email-field:focus { border-bottom: 3px solid '.essb_rs_adjust_brightness($customizer_subscribe_emailcolor, essb_rs_light_or_dark($customizer_subscribe_emailcolor, 50, -50)).'!important;}';
		}
		
		if ($customizer_subscribe_noborder == 'true') {
			$snippet .= '.essb-subscribe-form-content { border-top: 0px solid ' . $global_hovercolor . '!important; }';
				
		}
		
		return $snippet;
	}
	
	function essb_rs_adjust_brightness($hex, $steps) {
		// Steps should be between -255 and 255. Negative = darker, positive =
		// lighter
		$steps = max ( - 255, min ( 255, $steps ) );
		
		// Normalize into a six character long hex string
		$hex = str_replace ( '#', '', $hex );
		if (strlen ( $hex ) == 3) {
			$hex = str_repeat ( substr ( $hex, 0, 1 ), 2 ) . str_repeat ( substr ( $hex, 1, 1 ), 2 ) . str_repeat ( substr ( $hex, 2, 1 ), 2 );
		}
		
		// Split into three parts: R, G and B
		$color_parts = str_split ( $hex, 2 );
		$return = '#';
		
		foreach ( $color_parts as $color ) {
			$color = hexdec ( $color ); // Convert to decimal
			$color = max ( 0, min ( 255, $color + $steps ) ); // Adjust color
			$return .= str_pad ( dechex ( $color ), 2, '0', STR_PAD_LEFT ); // Make two
			                                                          // char hex code
		}
		
		return $return;
	}
	
	function essb_rs_light_or_dark($color, $steps_light = 30, $steps_dark = -30) {
		$hex = str_replace( '#', '', $color );
		
		$c_r = hexdec( substr( $hex, 0, 2 ) );
		$c_g = hexdec( substr( $hex, 2, 2 ) );
		$c_b = hexdec( substr( $hex, 4, 2 ) );
		
		$brightness = ( ( $c_r * 299 ) + ( $c_g * 587 ) + ( $c_b * 114 ) ) / 1000;
		
		return $brightness > 155 ? $steps_dark : $steps_light;
	}
}