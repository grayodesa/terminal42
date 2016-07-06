<?php

class ESSBAdminActivate {
	public static function is_activated() {
		global $essb_options;
		
		$purchase_code = ESSBOptionValuesHelper::options_value($essb_options, 'purchase_code');
		
		if (!empty($purchase_code)) {
			return true;
		}
		else {
			return false;
		}
	}
	
	public static function should_display_notice() {
		$notice_dismissed = get_option('essb3-activate-notice');
		
		if (empty($notice_dismissed)) {
			return true;
		}
		else {
			return false;
		}
	}
	
	public static function dismiss_notice() {
		update_option('essb3-activate-notice', 'true');
	}
	
	public static function notice_activate() {
		$dismiss_url = esc_url_raw(add_query_arg(array('dismissactivate' => 'true'), admin_url ("admin.php?page=essb_options")));
		$update_url = esc_url_raw(admin_url ("admin.php?page=essb_redirect_update&tab=update"));
		
		$dismiss_addons_button = '<a href="'.$dismiss_url.'"  text="' . __ ( 'Close this message', ESSB3_TEXT_DOMAIN ) . '" class="button button-orange float_right" style="margin-right: 5px;"><i class="fa fa-close"></i>&nbsp;' . __ ( 'Close this message', ESSB3_TEXT_DOMAIN ) . '</a>';
		
		return sprintf ( '<div class="essb-information-box fade"><div class="icon red"><i class="fa fa-refresh"></i></div><div class="inner">Hello! Please <a href="%1$s"><b>activate your copy</b></a> of Easy Social Share Buttons for WordPress to receive automatic updates. %2$s</div></div>', $update_url, $dismiss_addons_button );		
	}
}

?>