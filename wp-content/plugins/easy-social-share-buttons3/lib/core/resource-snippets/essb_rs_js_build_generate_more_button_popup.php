<?php
if (!function_exists('essb_rs_js_build_generate_more_button_popup')) {
	function essb_rs_js_build_generate_more_button_popup() {
		$output = 'var essb_morepopup_opened = false;';
		
		$output .= 'function essb_toggle_more_popup(unique_id) {
		jQuery.fn.extend({
		center: function () {
		return this.each(function() {
		var top = (jQuery(window).height() - jQuery(this).outerHeight()) / 2;
		var left = (jQuery(window).width() - jQuery(this).outerWidth()) / 2;
		jQuery(this).css({position:\'fixed\', margin:0, top: (top > 0 ? top : 0)+\'px\', left: (left > 0 ? left : 0)+\'px\'});
		});
		}
		});
		
		if (essb_morepopup_opened) {
		essb_toggle_less_popup(unique_id);
		return;
		}
		
		var is_from_mobilebutton = false;
		var height_of_mobile_bar = 0;
		if (jQuery(".essb-mobile-sharebottom").length) {
		is_from_mobilebutton = true;
		height_of_mobile_bar = jQuery(".essb-mobile-sharebottom").outerHeight();
		}
		
		var win_width = jQuery( window ).width();
		var win_height = jQuery(window).height();
		var doc_height = jQuery(\'document\').height();
		
		var base_width = 550;
		if (!is_from_mobilebutton) {
		base_width = 660;
		}
		
		if (win_width < base_width) { base_width = win_width - 30; }
		
		var instance_mobile = false;
		
		var element_class = ".essb_morepopup_"+unique_id;
		var element_class_shadow = ".essb_morepopup_shadow_"+unique_id;
		
		jQuery(element_class).css( { width: base_width+\'px\'});
		
		jQuery(element_class).fadeIn(400);
		jQuery(element_class).center();
		if (is_from_mobilebutton) {
		jQuery(element_class).css( { top: \'5px\'});
		jQuery(element_class).css( { height: (win_height - height_of_mobile_bar - 10)+\'px\'});
		var element_content_class = ".essb_morepopup_content_"+unique_id;
		
		jQuery(element_content_class).css( { height: (win_height - height_of_mobile_bar - 40)+\'px\', "overflowY" :"auto"});
		jQuery(element_class_shadow).css( { height: (win_height - height_of_mobile_bar)+\'px\'});
		}
		jQuery(element_class_shadow).fadeIn(200);
		essb_morepopup_opened = true;
		};
		
		function essb_toggle_less_popup(unique_id) {
		var element_class = ".essb_morepopup_"+unique_id;
		var element_class_shadow = ".essb_morepopup_shadow_"+unique_id;
		jQuery(element_class).fadeOut(200);
		jQuery(element_class_shadow).fadeOut(200);
		essb_morepopup_opened = false;
		};';
		
		return $output;
	}
}