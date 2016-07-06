/* ------------------------------------------------------------------------
 * Float from top
 * ------------------------------------------------------------------------
 */

jQuery(document).ready(
		function($) {

			function exist_element(oID) {
				return jQuery(oID).length > 0;
			}

			if (!exist_element(".essb_displayed_float")) {
				return;
			}

			var top = $('.essb_displayed_float').offset().top
					- parseFloat($('.essb_displayed_float').css('marginTop')
							.replace(/auto/, 0));
			var basicElementWidth = '';
			
			var hide_float_percent = (typeof(essb_settings['hide_float']) != "undefined") ? essb_settings['hide_float'] : '';
			var custom_top_postion = (typeof(essb_settings['float_top']) != "undefined") ? essb_settings['float_top'] : '';
			var hide_float_active = false;
			if (hide_float_percent != '') {
				if (Number(hide_float_percent)) {
					hide_float_percent = parseInt(hide_float_percent);
					hide_float_active = true;
				}
			}
			var active_custom_top = false;
			if (custom_top_postion != '') {
				if (Number(custom_top_postion)) {
					custom_top_postion = parseInt(custom_top_postion);
					active_custom_top = true;
				}
			}
			
			$(window).scroll(
					function(event) {
						// what the y position of the scroll is
						var y = $(this).scrollTop();
						
						if (active_custom_top) {
							y -= custom_top_postion;
						}
						
						var height = $(document).height()-$(window).height();
						var percentage = y/height*100;
						// whether that's below the form
						if (y >= top) {
							// if so, ad the fixed class
							if (basicElementWidth == '') {
								var widthOfContainer = $('.essb_displayed_float').width();
								basicElementWidth = widthOfContainer;
								$('.essb_displayed_float').width(widthOfContainer);
							}
							$('.essb_displayed_float').addClass('essb_fixed');

						} else {
							// otherwise remove it
							$('.essb_displayed_float').removeClass('essb_fixed');
							if (basicElementWidth != '') {
								$('.essb_displayed_float').width(basicElementWidth);
							}
						}
						
						if (hide_float_active) {
							if (percentage >= hide_float_percent && !$('.essb_displayed_float').hasClass('hidden-float')) {
								$('.essb_displayed_float').addClass('hidden-float');
								$('.essb_displayed_float').fadeOut(100);
								return;
							}
							if (percentage < hide_float_percent && $('.essb_displayed_float').hasClass('hidden-float')) {
								$('.essb_displayed_float').removeClass('hidden-float');
								$('.essb_displayed_float').fadeIn(100);
								return;
							}
						}
					});

		});

/* ------------------------------------------------------------------------
 * Post Bar
 * ------------------------------------------------------------------------
 */

(function( $ ) {
	$(document).ready(function() {
		
        // Define Variables
        var ttr_start = $(".essb_postbar_start"),
            ttr_end = $(".essb_postbar_end");
        if (ttr_start.length) {
            var docOffset = ttr_start.offset().top,
        	docEndOffset = ttr_end.offset().top,
            elmHeight = docEndOffset - docOffset,
            progressBar = $('.essb-postbar-progress-bar'),
            winHeight = $(window).height(),
            docScroll,viewedPortion;

        
	        $(".essb-postbar-prev-post a").on('mouseenter touchstart', function(){
	            $(this).next('div').css("top","-162px");
	        });
	        $(".essb-postbar-close-prev").on('click', function(){
	        	$(".essb-postbar-prev-post a").next('div').css("top","46px");
	        });
	        $(".essb-postbar-next-post a").on('mouseenter touchstart', function(){
	            $(this).next('div').css("top","-162px");
	        });
	        $(".essb-postbar-close-next").on('click', function(){
	        	$(".essb-postbar-next-post a").next('div').css("top","46px");
	        });
	        
	        $(window).load(function(){
	            docOffset = ttr_start.offset().top,
	            docEndOffset = ttr_end.offset().top,
	            elmHeight = docEndOffset - docOffset;
	        });
	
	        $(window).on('scroll', function() {
	
				docScroll = $(window).scrollTop(),
	            viewedPortion = winHeight + docScroll - docOffset;
	
				if(viewedPortion < 0) { 
					viewedPortion = 0; 
				}
	            if(viewedPortion > elmHeight) { 
	            	viewedPortion = elmHeight;
	            }
	            var viewedPercentage = (viewedPortion / elmHeight) * 100;
				progressBar.css({ width: viewedPercentage + '%' });
	
			});
	
			$(window).on('resize', function() {
				docOffset = ttr_start.offset().top;
				docEndOffset = ttr_end.offset().top;
				elmHeight = docEndOffset - docOffset;
				winHeight = $(window).height();
				$(window).trigger('scroll');
			});
			
			$(window).trigger('scroll');
        }

	});

})( jQuery );

/* ------------------------------------------------------------------------
 * Post Float
 * ------------------------------------------------------------------------
 */

jQuery(document).ready(function($){

	function exist_element(oID) {
		return jQuery(oID).length > 0;
	}
		 
	var essb_postfloat_height_break = 0;
	if ($('.essb_break_scroll').length) {
		var break_position = $('.essb_break_scroll').position();
		var break_top = break_position.top;
		
	}

	if (!exist_element(".essb_displayed_postfloat")) { return; }

	var top = $('.essb_displayed_postfloat').offset().top - parseFloat($('.essb_displayed_postfloat').css('marginTop').replace(/auto/, 0));
	var basicElementWidth = '';
	var postfloat_always_onscreen = false;
	if (typeof(essb_settings) != "undefined") {
		postfloat_always_onscreen = essb_settings.essb3_postfloat_stay;
	}
	var custom_user_top = 0;
	if (typeof(essb_settings) != "undefined") {
		if (typeof(essb_settings['postfloat_top']) != "undefined") {
			custom_user_top = essb_settings["postfloat_top"];
			custom_user_top = parseInt(custom_user_top);
			
			top -= custom_user_top;
		}
	}
	
	$(window).scroll(function (event) {
    // what the y position of the scroll is
		var y = $(this).scrollTop();

    // whether that's below the form
		if (y >= top) {
      // if so, ad the fixed class
			$('.essb_displayed_postfloat').addClass('essb_postfloat_fixed');
      
			var element_position = $('.essb_displayed_postfloat').offset();
			var element_height = $('.essb_displayed_postfloat').outerHeight();
			var element_top = parseInt(element_position.top) + parseInt(element_height);
			
			if (!postfloat_always_onscreen) {
			if (element_top > break_top) {
				if (!$('.essb_displayed_postfloat').hasClass("essb_postfloat_breakscroll")) {
					$('.essb_displayed_postfloat').addClass("essb_postfloat_breakscroll");
				}
			}
			else {
				if ($('.essb_displayed_postfloat').hasClass("essb_postfloat_breakscroll")) {
					$('.essb_displayed_postfloat').removeClass("essb_postfloat_breakscroll");
				}
			}
			}
		} 
		else {
      // otherwise remove it
      $('.essb_displayed_postfloat').removeClass('essb_postfloat_fixed');
    }
  });
});

/* ------------------------------------------------------------------------
 * Share Point
 * ------------------------------------------------------------------------
 */

(function( $ ) {
	$(document).ready(function() {
		
		var essb_point_triggered = false;
		var essb_point_trigger_mode = "";
		
		var essb_point_trigger_open_onscroll = function() {
			var current_pos = $(window).scrollTop() + $(window).height() - 200;
			
			var top = $('.essb_break_scroll').offset().top - parseFloat($('.essb_break_scroll').css('marginTop').replace(/auto/, 0));
			
			if (essb_point_trigger_mode == 'end') {
				if (current_pos >= top && !essb_point_triggered) {
					if (!$('.essb-point-share-buttons').hasClass('essb-point-share-buttons-active')) {
						$('.essb-point-share-buttons').addClass('essb-point-share-buttons-active');
						if (essb_point_mode != 'simple') $('.essb-point').toggleClass('essb-point-open');
						essb_point_triggered = true;
					}
				}
			}
			if (essb_point_trigger_mode == 'middle') {
				var percentage = current_pos * 100 / top;
				if (percentage > 49 && !essb_point_triggered) {
					if (!$('.essb-point-share-buttons').hasClass('essb-point-share-buttons-active')) {
						$('.essb-point-share-buttons').addClass('essb-point-share-buttons-active');
						if (essb_point_mode != 'simple') $('.essb-point').toggleClass('essb-point-open');
						essb_point_triggered = true;
					}
				}
			}
		}
		
		var essb_point_onscroll = $('.essb-point').attr('data-trigger-scroll') || "";
		var essb_point_mode = $('.essb-point').attr('data-point-type') || "simple";
		
		if (essb_point_onscroll == 'end' || essb_point_onscroll == 'middle') {
			essb_point_trigger_mode = essb_point_onscroll;
			$(window).scroll(essb_point_trigger_open_onscroll);
		}
		
		$(".essb-point").on('click', function(){

			$('.essb-point-share-buttons').toggleClass('essb-point-share-buttons-active');
			
			if (essb_point_mode != 'simple') $('.essb-point').toggleClass('essb-point-open');
        });
	});

})( jQuery );
