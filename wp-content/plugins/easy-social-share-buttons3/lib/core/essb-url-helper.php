<?php
class ESSBUrlHelper {
	
	public static function get_current_url($mode = 'base') {
	
		$url = 'http' . (is_ssl () ? 's' : '') . '://' . $_SERVER ['HTTP_HOST'] . $_SERVER ['REQUEST_URI'];
	
		switch ($mode) {
			case 'raw' :
				return $url;
				break;
			case 'base' :
				return reset ( explode ( '?', $url ) );
				break;
			case 'uri' :
				$exp = explode ( '?', $url );
				return trim ( str_replace ( home_url (), '', reset ( $exp ) ), '/' );
				break;
			default :
				return false;
		}
	}
	
	public static function get_current_page_url() {
		$pageURL = 'http';
		if(isset($_SERVER["HTTPS"]))
			if ($_SERVER["HTTPS"] == "on") {
			$pageURL .= "s";
		}
		$pageURL .= "://";
		if ($_SERVER["SERVER_PORT"] != "80") {
			$pageURL .= $_SERVER["SERVER_NAME"] . ":" . $_SERVER["SERVER_PORT"] . $_SERVER["REQUEST_URI"];
		} else {
			$pageURL .= $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"];
		}
		return $pageURL;
	}
	
	public static function short_googl($url, $post_id = '', $deactivate_cache = false, $api_key = '') {
		if (!empty($post_id) && !$deactivate_cache) {
			$exist_shorturl = get_post_meta($post_id, 'essb_shorturl_googl', true);
			
			if (!empty($exist_shorturl)) {
				return $exist_shorturl;
			}
		}
		
		//$encoded_url = urlencode($url);
		$encoded_url = $url;
		if (!empty($api_key)) {
			$result = wp_remote_post ( 'https://www.googleapis.com/urlshortener/v1/url?key='.($api_key), array ('body' => json_encode ( array ('longUrl' => esc_url_raw ( $encoded_url ) ) ), 'headers' => array ('Content-Type' => 'application/json' ) ) );
		}
		else {
			$result = wp_remote_post ( 'https://www.googleapis.com/urlshortener/v1/url', array ('body' => json_encode ( array ('longUrl' => esc_url_raw ( $encoded_url ) ) ), 'headers' => array ('Content-Type' => 'application/json' ) ) );
		}
				
		// Return the URL if the request got an error.
		if (is_wp_error ( $result ))
			return $url;
	
		$result = json_decode ( $result ['body'] );
		$shortlink = $result->id;
		if ($shortlink) {
			if ($post_id != '') {
				update_post_meta ( $post_id, 'essb_shorturl_googl', $shortlink );
	
			}
	
			return $shortlink;
		}
	
		return $url;
	}
	
	public static function short_bitly($url, $user = '', $api = '', $post_id = '', $deactivate_cache = false, $bitly_api_version = '') {
		//print "calling short url for =".$url;
		
		// testing mode
		//$url = "http://creoworx.com/fb/?utm_source={network}%26utm_medium=post%26utm_campaign=calvendo";
		
		if (!empty($post_id) && !$deactivate_cache) {
			$exist_shorturl = get_post_meta($post_id, 'essb_shorturl_bitly', true);
				
			if (!empty($exist_shorturl)) {
				return $exist_shorturl;
			}
		}
		
		//$encoded_url = urlencode($url);
		$encoded_url = ($url);
		
		if ($bitly_api_version == 'new') {
			$params = http_build_query(
					array(							
							'access_token' => $api,
							'uri' => urlencode($encoded_url),
							'format' => 'json',
					)
			);
				
		}
		else {
			$params = http_build_query(
					array(
							'login' => $user,
							'apiKey' => $api,
							'longUrl' => $encoded_url,
							'format' => 'json',
					)
			);
		}
	
		/*if ($jmp == 'true') {
			$params['domain'] = "j.mp";
		}*/
			
		$result = $url;
	
		$rest_url = 'https://api-ssl.bitly.com/v3/shorten?' . $params;
			
		$response = wp_remote_get( $rest_url );
		// if we get a valid response, save the url as meta data for this post
		if( !is_wp_error( $response ) ) {
	
			$json = json_decode( wp_remote_retrieve_body( $response ) );
			
			if( isset( $json->data->url ) ) {
	
				$result = $json->data->url;
				update_post_meta ( $post_id, 'essb_shorturl_bitly', $result );
			}
		}
	
		return $result;
	}
	
	public static function short_ssu($url, $post_id, $deactivate_cache = false) {
		$result = $url;
		
		if (!empty($post_id) && !$deactivate_cache) {
			$exist_shorturl = get_post_meta($post_id, 'essb_shorturl_ssu', true);
		
			if (!empty($exist_shorturl)) {
				return $exist_shorturl;
			}
		}
		
		if (defined('ESSB3_SSU_VERSION')) {
			if (class_exists('ESSBSelfShortUrlHelper')) {
				$short_url = ESSBSelfShortUrlHelper::get_external_short_url ( $url );
				
				if (!empty($short_url)) {
					$result = ESSBSelfShortUrlHelper::get_base_path () . $short_url;
					update_post_meta ( $post_id, 'essb_shorturl_ssu', $result );
				}
			}
		}
		
		return $result;
	}
	
	public static function short_url($url, $provider, $post_id = '', $bitly_user = '', $bitly_api = '') {
		global $essb_options;
				
		$deactivate_cache = ESSBOptionValuesHelper::options_bool_value($essb_options, 'deactivate_shorturl_cache');
		$shorturl_googlapi = ESSBOptionValuesHelper::options_value($essb_options, 'shorturl_googlapi');
		
		$bitly_api_version = ESSBOptionValuesHelper::options_value($essb_options, 'shorturl_bitlyapi_version');
		
		$short_url = "";
		
		if ($provider == "ssu") {
			if (!defined('ESSB3_SSU_VERSION')) {
				$provider = "wp";
			}
		}		
		
		switch ($provider) {
			case "wp" :
				$short_url = wp_get_shortlink($post_id);
				
				$url_parts = parse_url($url);
				if (isset($url_parts['query'])) {
					$short_url = self::attach_tracking_code($short_url, $url_parts['query']);
				}
				
				break;			
			case "goo.gl" :
				$short_url = self::short_googl($url, $post_id, $deactivate_cache, $shorturl_googlapi);
				break;
			case "bit.ly" :
				$short_url = self::short_bitly($url, $bitly_user, $bitly_api, $post_id, $deactivate_cache, $bitly_api_version);
				break;
			case "ssu":
				$short_url = self::short_ssu($url, $post_id, $deactivate_cache);
				break;
		}
		
		// @since 3.4 affiliate intergration with wp shorturl
		$affwp_active = ESSBOptionValuesHelper::options_bool_value($essb_options, 'affwp_active');
		if ($affwp_active) {
			$short_url = ESSBUrlHelper::generate_affiliatewp_referral_link($short_url);
		}
		
		$affs_active = ESSBOptionValuesHelper::options_bool_value($essb_options, 'affs_active');
		if ($affs_active) {
			$short_url = do_shortcode('[affiliates_url]'.$short_url.'[/affiliates_url]');
		}
				
		return $short_url;
	}
 	
	public static function attach_tracking_code($url, $code = '') {
		$posParamSymbol = strpos($url, '?');
		
		$code = str_replace('&', '%26', $code);
	
		if ($posParamSymbol === false) {
			$url .= '?';
		}
		else {
			$url .= "%26";
		}
	
		$url .= $code;
			
		return $url;
	}

	public static function esc_tracking_url($url) {
		$url = str_replace('&', '%26', $url);
		//$url = str_replace('?', '%3F', $url);
		
		return $url;
	}
	
	public static function generate_affiliatewp_referral_link ($permalink) {
		global $essb_options;
		
		if ( ! ( is_user_logged_in() && affwp_is_affiliate() ) ) {
			return $permalink;
		}
		
		$affwp_active_mode = ESSBOptionValuesHelper::options_value($essb_options, 'affwp_active_mode');
		$affwp_active_pretty = ESSBOptionValuesHelper::options_bool_value($essb_options, 'affwp_active_pretty');
		
		// append referral variable and affiliate ID to sharing links in ESSB
		if ($affwp_active_mode == 'name') {
			if ($affwp_active_pretty) {
				$permalink .= affiliate_wp()->tracking->get_referral_var().'/'.affwp_get_affiliate_username();
			}
			else {
				$permalink = add_query_arg( affiliate_wp()->tracking->get_referral_var(), affwp_get_affiliate_username(), $permalink );
			}
		}
		else {
			if ($affwp_active_pretty) {
				$permalink .= affiliate_wp()->tracking->get_referral_var().'/'.affwp_get_affiliate_id();
			}
			else {
				$permalink = add_query_arg( affiliate_wp()->tracking->get_referral_var(), affwp_get_affiliate_id(), $permalink );
			}
		}
		return $permalink;
	}
} 

?>