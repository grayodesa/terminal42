<?php

class ESSBNetworks_LoveThis {
	private static $instance = null;
	
	public static function get_instance() {
	
		if ( null == self::$instance ) {
			self::$instance = new self;
		}
	
		return self::$instance;
	
	} // end get_instance;
	
	function __construct() {
		add_action ( 'wp_ajax_nopriv_essb_love_action', array ($this, 'log_love_click' ) );
		add_action ( 'wp_ajax_essb_love_action', array ($this, 'log_love_click' ) );
	}
	
	public function generate_js_code() {
		global $essb_options;
		
		// localization of messages;
		$message_loved = isset($essb_options['translate_love_loved']) ? $essb_options['translate_love_loved'] : '';
		$message_thanks = isset($essb_options['translate_love_thanks'])? $essb_options['translate_love_thanks'] : '';
	
		if ($message_loved == "") {
			$message_loved = __("You already love this today.", ESSB3_TEXT_DOMAIN);
		}
		if ($message_thanks == "") {
			$message_thanks = "Thank you for loving this.";
		}
	

		$output_code = '';
		
		$output_code .= '
var essb_clicked_lovethis = false;		
var essb_love_you_message_thanks = "'.$message_thanks.'";
var essb_love_you_message_loved = "'.$message_loved.'";	

var essb_lovethis = function(oInstance) {
	if (essb_clicked_lovethis) {
		alert(essb_love_you_message_loved);
		return;
	}
	
	var element = jQuery(\'.essb_\'+oInstance);

	if (!element.length) { return; }
	var instance_post_id = jQuery(element).attr("data-essb-postid") || "";
	
	var cookie_set = essb_get_lovecookie("essb_love_"+instance_post_id);
	if (cookie_set) {
		alert(essb_love_you_message_loved);
		return;
	}
	
	if (typeof(essb_settings) != "undefined") {
		jQuery.post(essb_settings.ajax_url, {
			\'action\': \'essb_love_action\',
			\'post_id\': instance_post_id,
			\'service\': \'love\',
			\'nonce\': essb_settings.essb3_nonce
		}, function (data) { if (data) {
			alert(essb_love_you_message_thanks);
		}},\'json\');
	}
	
	essb_tracking_only(\'\', \'love\', oInstance, true);
};

var essb_get_lovecookie = function(name) {
  var value = "; " + document.cookie;
  var parts = value.split("; " + name + "=");
  if (parts.length == 2) return parts.pop().split(";").shift();
};	
		';		
		
		return $output_code;
		
	}
	
	public function log_love_click() {
		global $wpdb, $blog_id;
	
		$post_id = isset ( $_POST ["post_id"] ) ? $_POST ["post_id"] : '';
		$service_id = isset ( $_POST ["service"] ) ? $_POST ["service"] : '';
	
		$love_count = get_post_meta($post_id, '_essb_love', true);
		if( isset($_COOKIE['essb_love_'. $post_id]) ) die( $love_count);
		if (!isset($love_count)) {
			$love_count = 0;
		}
		$love_count = intval($love_count);
		$love_count++;
		update_post_meta($post_id, '_essb_love', $love_count);
		//setcookie('essb_love_'. $post_id, $post_id . " - ". $love_count, time()*60*60*24, '/');
		$cookie_information = 'essb_love_'. $post_id.' = '.$love_count;
		setcookie('essb_love_'. $post_id, $cookie_information, time()+(3600 * 24), "/", "",  0);
	
		die ( json_encode ( array ("success" => 'Log handled - post_id = '.$post_id.' count = '.$love_count ) ) );
	}
}

?>