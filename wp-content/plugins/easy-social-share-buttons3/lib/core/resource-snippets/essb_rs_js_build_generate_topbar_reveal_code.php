<?php
if (!function_exists('essb_rs_js_build_generate_topbar_reveal_code')) {
	function essb_rs_js_build_generate_topbar_reveal_code(){
		global $essb_options;
		
		
		$output = '';
		
		$appear_pos = ESSBOptionValuesHelper::options_value($essb_options, 'topbar_top_onscroll');
		$topbar_hide = ESSBOptionValuesHelper::options_value($essb_options, 'topbar_hide');
		
		
		if ($appear_pos != '' || $topbar_hide != '') {
			$output .= '
			jQuery(document).ready(function($){
		
			$(window).scroll(essb_topbar_onscroll);
		
			function essb_topbar_onscroll() {
			var current_pos = $(window).scrollTop();
			var height = $(document).height()-$(window).height();
			var percentage = current_pos/height*100;
		
			var value_appear = "'.$appear_pos.'";
			var value_disappear = "'.$topbar_hide.'";
		
			var element;
			if ($(".essb_topbar").length) {
			element = $(".essb_topbar");
		}
		
		if (!element || typeof(element) == "undefined") { return; }
		
		
		value_appear = parseInt(value_appear);
		value_disappear = parseInt(value_disappear);
		
		if (value_appear > 0 ) {
		if (percentage >= value_appear && !element.hasClass("essb_active_topbar")) {
		
		
		element.addClass("essb_active_topbar");
		return;
		}
			
		if (percentage < value_appear && element.hasClass("essb_active_topbar")) {
		
		element.removeClass("essb_active_topbar");
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