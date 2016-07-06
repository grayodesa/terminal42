var essb_subscribe_opened = {};

var essb_subscribe_popup_close = function(key) {
	jQuery('.essb-subscribe-form-' + key).fadeOut(400);
	jQuery('.essb-subscribe-form-overlay-' + key).fadeOut(400);
}

var essb_toggle_subscribe = function(key) {
	// subsribe container do not exist
	if (!jQuery('.essb-subscribe-form-' + key).length) return;
	
	jQuery.fn.extend({
        center: function () {
            return this.each(function() {
                var top = (jQuery(window).height() - jQuery(this).outerHeight()) / 2;
                var left = (jQuery(window).width() - jQuery(this).outerWidth()) / 2;
                jQuery(this).css({position:'fixed', margin:0, top: (top > 0 ? top : 0)+'px', left: (left > 0 ? left : 0)+'px'});
            });
        }
    }); 
	
	var asPopup = jQuery('.essb-subscribe-form-' + key).attr("data-popup") || "";

	// it is not popup (in content methods is asPopup == "")
	if (asPopup != '1') {
		if (jQuery('.essb-subscribe-form-' + key).hasClass("essb-subscribe-opened")) {
			jQuery('.essb-subscribe-form-' + key).slideUp('fast');
			jQuery('.essb-subscribe-form-' + key).removeClass("essb-subscribe-opened");
		}
		else {
			jQuery('.essb-subscribe-form-' + key).slideDown('fast');
			jQuery('.essb-subscribe-form-' + key).addClass("essb-subscribe-opened");
			
			if (!essb_subscribe_opened[key]) {
				essb_subscribe_opened[key] = key;
				essb_tracking_only('', 'subscribe', key, true);
			}
		}
	}
	else {
		var win_width = jQuery( window ).width();
		var doc_height = jQuery('document').height();
		
		var base_width = 600;
		
		if (win_width < base_width) { base_width = win_width - 40; }
		
		
		jQuery('.essb-subscribe-form-' + key).css( { width: base_width+'px'});
		jQuery('.essb-subscribe-form-' + key).center();
		
		jQuery('.essb-subscribe-form-' + key).fadeIn(400);
		jQuery('.essb-subscribe-form-overlay-' + key).fadeIn(200);

	}
	
}

var essb_ajax_subscribe = function(key) {
	event.preventDefault();
	
	var formContainer = jQuery('.essb-subscribe-form-' + key + ' #essb-subscribe-from-content-form-mailchimp');	
	
	if (formContainer.length) {
		var user_mail = jQuery(formContainer).find('.essb-subscribe-form-content-email-field').val();
		jQuery(formContainer).find('.submit').prop('disabled', true);
		jQuery(formContainer).hide();
		jQuery('.essb-subscribe-form-' + key).find('.essb-subscribe-loader').show();
		jQuery.post(formContainer.attr('action'), { mailchimp_email: user_mail}, 
				function (data) { if (data) {
					if (data['code'] == '1') {
						jQuery('.essb-subscribe-form-' + key).find('.essb-subscribe-form-content-success').show();
					}
					else {
						jQuery('.essb-subscribe-form-' + key).find('.essb-subscribe-form-content-error').append(': <span>' + data['message']+'</span>');
						jQuery('.essb-subscribe-form-' + key).find('.essb-subscribe-form-content-error').show();						
					}
					jQuery('.essb-subscribe-form-' + key).find('.essb-subscribe-loader').hide();
					jQuery(formContainer).hide();
				}},
		'json');
	}
	
} 