<?php
if (!function_exists('essb_rs_js_build_generate_postfloat_reveal_code')) {
	function essb_rs_js_build_generate_postfloat_reveal_code() {
		global $essb_options;
		
		
		$output = '';
		
		$appear_pos = ESSBOptionValuesHelper::options_value($essb_options, 'postfloat_percent');
		if (empty($appear_pos)) {
			$appear_pos = "0";
		}
		
		
		if ($appear_pos != '') {
			$output .= '
			jQuery(document).ready(function($){
		
			$(window).scroll(essb_postfloat_onscroll);
		
			function essb_postfloat_onscroll() {
			var current_pos = $(window).scrollTop();
			var height = $(document).height()-$(window).height();
			var percentage = current_pos/height*100;
		
			var value_appear = "'.$appear_pos.'";
		
			var element;
			if ($(".essb_displayed_postfloat").length) {
			element = $(".essb_displayed_postfloat");
		}
		
		if (!element || typeof(element) == "undefined") { return; }
		
		
		value_appear = parseInt(value_appear);
		
		if (value_appear > 0 ) {
		if (percentage >= value_appear && !element.hasClass("essb_active_postfloat")) {
		
		
		element.addClass("essb_active_postfloat");
		return;
		}
			
		if (percentage < value_appear && element.hasClass("essb_active_postfloat")) {
		
		element.removeClass("essb_active_postfloat");
		return;
		}
		}
		
		}
		});
		';
		}
		return $output;
	}
}