<?php
if (!function_exists('essb_rs_js_build_generate_sidebar_reveal_code')) {
	function essb_rs_js_build_generate_sidebar_reveal_code() {
		global $essb_options;
		
		$appear_pos = ESSBOptionValuesHelper::options_value($essb_options, 'sidebar_leftright_percent');
		$disappear_pos = ESSBOptionValuesHelper::options_value($essb_options, 'sidebar_leftright_percent_hide');
		
		if (empty($appear_pos)) {
			$appear_pos = "0";
		}
		if (empty($disappear_pos)) {
			$disappear_pos = "0";
		}
		
		$output = '';
		
		//$appear_pos = ESSBOptionValuesHelper::options_value($essb_options, 'sidebar_leftright_percent');
		
		if (ESSBOptionValuesHelper::options_bool_value($essb_options, 'sidebar_leftright_close')) {
			$output .= '
			jQuery(document).ready(function($){
				
			$(".essb_link_sidebar-close a").each(function() {
		
			$(this).click(function(event) {
			event.preventDefault();
			var links_list = $(this).parent().parent().get(0);
		
			if (!$(links_list).length) { return; }
		
			$(links_list).find(".essb_item").each(function(){
			if (!$(this).hasClass("essb_link_sidebar-close")) {
			$(this).toggleClass("essb-sidebar-closed-item");
		}
		else {
		$(this).toggleClass("essb-sidebar-closed-clicked");
		}
		});
		
		});
		
		});
		});
			
		';
		}
		
		if ($appear_pos != '' || $disappear_pos != '') {
			$output .= '
			jQuery(document).ready(function($){
		
			$(window).scroll(essb_sidebar_onscroll);
		
			function essb_sidebar_onscroll() {
			var current_pos = $(window).scrollTop();
			var height = $(document).height()-$(window).height();
			var percentage = current_pos/height*100;
		
			var value_disappear = "'.$disappear_pos.'";
			var value_appear = "'.$appear_pos.'";
		
			var element;
			if ($(".essb_displayed_sidebar").length) {
			element = $(".essb_displayed_sidebar");
		}
		if ($(".essb_displayed_sidebar_right").length) {
		element = $(".essb_displayed_sidebar_right");
		}
		
		if (!element || typeof(element) == "undefined") { return; }
		
		value_disappear = parseInt(value_disappear);
		value_appear = parseInt(value_appear);
		
		if (value_appear > 0 && value_disappear == 0) {
		if (percentage >= value_appear && !element.hasClass("active-sidebar")) {
		element.fadeIn(100);
		element.addClass("active-sidebar");
		return;
		}
			
		if (percentage < value_appear && element.hasClass("active-sidebar")) {
		element.fadeOut(100);
		element.removeClass("active-sidebar");
		return;
		}
		}
		
		if (value_disappear > 0 && value_appear == 0) {
		if (percentage >= value_disappear && !element.hasClass("hidden-sidebar")) {
		element.fadeOut(100);
		element.addClass("hidden-sidebar");
		return;
		}
			
		if (percentage < value_disappear && element.hasClass("hidden-sidebar")) {
		element.fadeIn(100);
		element.removeClass("hidden-sidebar");
		return;
		}
		}
		
		if (value_appear > 0 && value_disappear > 0) {
		if (percentage >= value_appear && percentage < value_disappear && !element.hasClass("active-sidebar")) {
		element.fadeIn(100);
		element.addClass("active-sidebar");
		return;
		}
			
		if ((percentage < value_appear || percentage >= value_disappear) && element.hasClass("active-sidebar")) {
		element.fadeOut(100);
		element.removeClass("active-sidebar");
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