<?php
if (!function_exists('essb_rs_js_build_generate_bottombar_reveal_code')) {
	function essb_rs_js_build_generate_bottombar_reveal_code() {
		global $essb_options;
		
		
		$output = '';
		
		$appear_pos = ESSBOptionValuesHelper::options_value($essb_options, 'bottombar_top_onscroll');
		$bottombar_hide = ESSBOptionValuesHelper::options_value($essb_options, 'bottombar_hide');
		
		
		if ($appear_pos != '' || $bottombar_hide != '') {
			$output .= '
			jQuery(document).ready(function($){
		
			$(window).scroll(essb_bottombar_onscroll);
		
			function essb_bottombar_onscroll() {
			var current_pos = $(window).scrollTop();
			var height = $(document).height()-$(window).height();
			var percentage = current_pos/height*100;
		
			var value_appear = "'.$appear_pos.'";
			var value_disappear = "'.$bottombar_hide.'";
			var element;
			if ($(".essb_bottombar").length) {
			element = $(".essb_bottombar");
		}
		
		if (!element || typeof(element) == "undefined") { return; }
		
		
		value_appear = parseInt(value_appear);
		value_disappear = parseInt(value_disappear);
		if (value_appear > 0 ) {
		if (percentage >= value_appear && !element.hasClass("essb_active_bottombar")) {
		element.addClass("essb_active_bottombar");
		return;
		}
			
		if (percentage < value_appear && element.hasClass("essb_active_bottombar")) {
		
		element.removeClass("essb_active_bottombar");
		return;
		}
		}
		if (value_disappear > 0) {
		if (percentage >= value_disappear && !element.hasClass("hidden-float")) {
		element.addClass("hidden-float");
		element.css( {"opacity": "0"});
		return;
		}
		if (percentage < value_disappear && element.hasClass("hidden-float")) {
		element.removeClass("hidden-float");
		element.css( {"opacity": "1"});
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