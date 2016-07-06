<?php

class ESSBCachedCounters {
	
	public static function prepare_list_of_networks_with_counter($networks, $active_networks_list) {
		global $essb_options;
		
		$basic_network_list = "twitter,linkedin,facebook,pinterest,google,stumbleupon,vk,reddit,buffer,love,ok,mwp,xing,pocket,mail,print,comments,yummly";
		$extended_network_list = "del,digg,weibo,flattr,tumblr,whatsapp,meneame,blogger,amazon,yahoomail,gmail,aol,newsvine,hackernews,evernote,myspace,mailru,viadeo,line,flipboard,sms,viber,telegram";
		
		$internal_counters = ESSBOptionValuesHelper::options_bool_value($essb_options, 'active_internal_counters');
		$no_mail_print_counter = ESSBOptionValuesHelper::options_bool_value($essb_options, 'deactive_internal_counters_mail');
		$twitter_counter = ESSBOptionValuesHelper::options_value($essb_options, 'twitter_counters');
		
		if ($twitter_counter == "")  {$twitter_counter = "api"; }
		
		$basic_array = explode(",", $basic_network_list);
		$extended_array = explode(",", $extended_network_list);
		
		$count_networks = array();
		
		foreach ($networks as $k) {
			
			if (!in_array ( $k, $active_networks_list)) {
				continue;
			}
			
			if (in_array($k, $basic_array)) {
				if ($k == "print" || $k == "mail") {
					if (!$no_mail_print_counter) {
						$count_networks[] = $k;
					}
				}
				else {
					$count_networks[] = $k;
				}
 			}
 			
 			if (in_array($k, $extended_array) && $internal_counters) {
 				$count_networks[] = $k;
 			}
		}		
		
		return $count_networks;
	}
	
	public static function is_fresh_cache($post_id) {
		global $essb_options;
		
		$is_fresh = true;
		
		if (isset ( $_SERVER ['HTTP_USER_AGENT'] ) && preg_match ( '/bot|crawl|slurp|spider/i', $_SERVER ['HTTP_USER_AGENT'] )) {
			$is_fresh = true;
		}
		else {
			$expire_time = get_post_meta ( $post_id, 'essb_cache_expire', true );
			$now = time ();
			
			$is_alive = ($expire_time > $now);
			
			if (true == $is_alive) {
				$is_fresh = true;
			}
			else {
				$is_fresh = false;
			}
		}
				
		return $is_fresh;
	}
	
	/**
	 * Check post if cache requires to be updated
	 *
	 * @param $post_id
	 * @return boolean
	 */
	public static function is_fresh_cache_deprecated($post_id) {
		global $essb_options;
		
		$is_fresh = true;
		
		if (isset ( $_SERVER ['HTTP_USER_AGENT'] ) && preg_match ( '/bot|crawl|slurp|spider/i', $_SERVER ['HTTP_USER_AGENT'] )) {
			$is_fresh = true;
		} else {
			$hours = ESSBOptionValuesHelper::options_value ( $essb_options, 'cache_counter_refresh' );
			if (empty ( $hours )) {
				$hours = "12";
			}
			
			$hours = intval ( $hours );
			
			$lastChecked = get_post_meta ( $post_id, 'essb_cache_timestamp', true );
			
			$time = floor ( ((date ( 'U' ) / 60) / 60) );
			
			if (($lastChecked > ($time - $hours) && $lastChecked > 390000) || ! is_singular ()) {
				$is_fresh = true;
			} else {
				$is_fresh = false;
			}
		}
		
		// check for manual counter refresh with url parameter
		$user_call_refresh = isset ( $_REQUEST ['essb_counter_update'] ) ? $_REQUEST ['essb_counter_update'] : '';
		if ($user_call_refresh == 'true') {
			$is_fresh = false;
		}
		
		return $is_fresh;
	}
	
	public static function get_counters($post_id, $share = array(), $networks) {
		global $essb_options;
		
		$cached_counters = array();
		$cached_counters['total'] = 0;
		
		if (!ESSBCachedCounters::is_fresh_cache($post_id)) {
			$cached_counters = ESSBCachedCounters::update_counters($post_id, $share['url'], $share['full_url'], $networks);
			
			if (defined('ESSB3_SHARED_COUNTER_RECOVERY')) {
				
				$recovery_till_date = ESSBOptionValuesHelper::options_value($essb_options, 'counter_recover_date');
				$is_applying_for_recovery = true;
				
				// @since 3.4 - apply recovery till provided date only
				if (!empty($recovery_till_date)) {
					$is_applying_for_recovery = ESSBCachedCounters::is_matching_recovery_date($post_id, $recovery_till_date);
				}
				
				if ($is_applying_for_recovery) {
					$current_url = $share['full_url'];
					// get post meta recovery value
					// essb_activate_sharerecovery - post meta recovery address
					$post_essb_activate_sharerecovery = get_post_meta($post_id, 'essb_activate_sharerecovery', true);
					if (!empty($post_essb_activate_sharerecovery)) {
						$current_url = $post_essb_activate_sharerecovery;
					}
					else {
						$current_url = ESSBCachedCounters::get_alternate_permalink($current_url, $post_id);
					}					
					
					$recovery_counters = ESSBCachedCounters::update_counters($post_id, $current_url, $current_url, $networks, true);
					
					
					$cached_counters = ESSBCachedCounters::consolidate_results($cached_counters, $recovery_counters, $networks);
				}
			}
			
			$total_saved = false;
			foreach ($networks as $k) {
				
				if ($k == 'total') $total_saved = true;
				
				$single = isset($cached_counters[$k]) ? $cached_counters[$k] : '0';
				update_post_meta($post_id, 'essb_c_'.$k, $single);
			}
			
			if (!$total_saved) {
				$k = 'total';
				$single = isset($cached_counters[$k]) ? $cached_counters[$k] : '0';
				update_post_meta($post_id, 'essb_c_'.$k, $single);
			}
		}		
		else {
			foreach ($networks as $k) {
				$cached_counters[$k] = get_post_meta($post_id, 'essb_c_'.$k, true);
				$cached_counters['total'] += intval($cached_counters[$k]);
			}
		}		
		
		
		return $cached_counters;
	}
	
	public static function update_counters($post_id, $url, $full_url, $networks = array(), $recover_mode = false) {
		global $essb_options;
		
		$twitter_counter = ESSBOptionValuesHelper::options_value($essb_options, 'twitter_counters');
		
		if ($twitter_counter == "")  {
			$twitter_counter = "api";
		}
		
		$cached_counters = array();
		$cached_counters['total'] = 0;
		
		
		if (!class_exists('ESSBCounterHelper')) {
			include_once (ESSB3_PLUGIN_ROOT . 'lib/core/essb-counters-helper.php');
		}
		
		foreach ( $networks as $k ) {
			switch ($k) {
				case "facebook" :
					$cached_counters [$k] = ESSBCountersHelper::get_facebook_count ( $url );
					break;
				case "twitter" :
					if ($twitter_counter == "api") {
						$cached_counters [$k] = ESSBCountersHelper::get_tweets ( $full_url );
					}
					else if ($twitter_counter == "newsc") {
						$cached_counters [$k] = ESSBCountersHelper::get_tweets_newsc ( $full_url );
					}
					else {
						if ($twitter_counter == "self") {
							if (!$recover_mode) {
								$cached_counters [$k] = ESSBCountersHelper::getSelfPostCount ( $post_id, $k );
							}
							else {
								$cached_counters[$k] = 0;
							}
						}
					}
					break;
				case "linkedin" :
					$cached_counters [$k] = ESSBCountersHelper::get_linkedin ( $url );
					break;
				case "pinterest" :
					$cached_counters [$k] = ESSBCountersHelper::get_pinterest( $url );
					break;
				case "google" :
					$cached_counters [$k] = ESSBCountersHelper::getGplusShares($url);
					break;
				case "stumbleupon" :
					$cached_counters [$k] = ESSBCountersHelper::get_stumbleupon($url);
					break;
				case "vk" :
					$cached_counters [$k] = ESSBCountersHelper::get_counter_number__vk($url);
					break;
				case "reddit" :
					$cached_counters [$k] = ESSBCountersHelper::getRedditScore($url);
					break;
				case "buffer" :
					$cached_counters [$k] = ESSBCountersHelper::get_buffer($url);
					break;
				case "love" :
					if (!$recover_mode) {
						$cached_counters [$k] = ESSBCountersHelper::getLoveCount($post_id);
					}
					else {
						$cached_counters[$k] = 0;
					}
					break;
				case "ok":
					$cached_counters [$k] = ESSBCountersHelper::get_counter_number_odnoklassniki ( $url );
					break;
				case "mwp" :
					$cached_counters [$k] = ESSBCountersHelper::getManagedWPUpVote ( $url );
					break;
				case "xing" :
					$cached_counters [$k] = ESSBCountersHelper::getXingCount($url);
					break;
				case "pocket" :
					$cached_counters [$k] = ESSBCountersHelper::getPocketCount($url);
					break;
				case "comments" :
					if (!$recover_mode) {
						$cached_counters [$k] = ESSBCountersHelper::get_comments_count($post_id);
					}
					else {
						$cached_counters[$k] = 0;
					}
					break;
				case "yummly" :
					$cached_counters [$k] = ESSBCountersHelper::get_yummly($url);
					break;
				default:
					if (!$recover_mode) {
						$cached_counters [$k] = ESSBCountersHelper::getSelfPostCount($post_id, $k);
					}
					else {
						$cached_counters[$k] = 0;					
					}
					break;
				
			}
			
			$cached_counters ['total'] += intval ( isset($cached_counters [$k]) ? $cached_counters [$k] : 0 );
		}
		
		if (!$recover_mode) {
			//$time = floor(((date('U')/60)/60));
			//update_post_meta($post_id, 'essb_cache_timestamp', $time);
			$expire_time = ESSBOptionValuesHelper::options_value($essb_options, 'cache_counter_refresh_new');
			if ($expire_time == '') { $expire_time = 60; }
			update_post_meta ( $post_id, 'essb_cache_expire', (time () + ($expire_time * 60)) );
		}
		
		return $cached_counters;
	}
	
	/**
	 * Executed multi curl request async
	 * 
	 * @param $request
	 * @return multitype:string 
	 */
	public static function essb_multi_curl_request($request) {
		global $essb_options;
		
		$counter_curl_fix = isset($essb_options['counter_curl_fix']) ? $essb_options['counter_curl_fix'] : 'false';
		
		$curly = array();
		$result = array();
		
		$mh = curl_multi_init();
		
		foreach ($request as $network => $url) {
			$curly[$network] = curl_init();

			$options = array(
					CURLOPT_RETURNTRANSFER	=> true, 	// return web page
					CURLOPT_HEADER 			=> false, 	// don't return headers
					CURLOPT_FAILONERROR	=> 0, 	// follow redirects
					CURLOPT_FOLLOWLOCATION => 0,
					CURLOPT_NOSIGNAL => 1,
					CURLOPT_ENCODING	 	=> "", 		// handle all encodings
					CURLOPT_USERAGENT	 	=> $_SERVER['HTTP_USER_AGENT'], 	// who am i
					CURLOPT_AUTOREFERER 	=> true, 	// set referer on redirect
					CURLOPT_CONNECTTIMEOUT 	=> 5, 		// timeout on connect
					CURLOPT_TIMEOUT 		=> 10, 		// timeout on response
					CURLOPT_MAXREDIRS 		=> 3, 		// stop after 3 redirects
					CURLOPT_SSL_VERIFYHOST 	=> 0,
					CURLOPT_SSL_VERIFYPEER 	=> false,
			);
			
			if (ini_get('open_basedir') == '' && ini_get('safe_mode' == 'Off')) {
				$options[CURLOPT_FOLLOWLOCATION] = true;
			}
			
			$options[CURLOPT_URL] = $url;
			
			curl_setopt_array($curly[$network], $options);
			
			try {
				//print 'curl state = '.$counter_curl_fix;
				if ($counter_curl_fix != 'true') {
					curl_setopt( $curly[$network], CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
				}
			}
			catch (Exception $e) {
			
			}
			
			curl_multi_add_handle($mh, $curly[$network]);
		}
		
		$running = NULL;
		
		do {
			curl_multi_exec($mh, $running);
		} while($running > 0);
		
		
		foreach($curly as $id => $c) {
			$result[$id] = curl_multi_getcontent($c);
			curl_multi_remove_handle($mh, $c);
		}
		
		// all done
		curl_multi_close($mh);
		
		return $result;
	}
	
	public static function generate_alternative_url($url) {
		global $essb_options;
		
		$recover_mode = ESSBOptionValuesHelper::options_bool_value($essb_options, 'counter_recover_mode');
		$recover_from_other_domain = ESSBOptionValuesHelper::options_bool_value($essb_options, 'counter_recover_domain');
		
		if ($recover_mode == "http2https") {
			$url = str_replace('http://','https://',$url);
		}
		if ($recover_mode == "https2http") {
			$url = str_replace('https://','http://',$url);
		}
		if ($recover_mode == "domain" && !empty($recover_from_other_domain)) {
			$current_site_url = get_site_url();

			
			$url = str_replace($current_site_url,$recover_from_other_domain,$url);
		}
		
		return $url;
	}
	
	public static function consolidate_results($share_values, $additional_values, $networks) {
		$new_result = array();
		$new_result['total'] = 0;
		
		foreach ($networks as $k) {
			$one_share = isset($share_values[$k]) ? $share_values[$k] : 0;
			$two_share = isset($additional_values[$k]) ? $additional_values[$k] : 0;
			
			$new_result[$k] = intval($one_share) + intval($two_share);
			
			$new_result['total'] += intval($one_share) + intval($two_share);
		}
		
		return $new_result;
	}
	
	public static function get_alternate_permalink($url, $id) {

		global $essb_options;		
		
		$new_url = $url;
		
		$recover_mode = ESSBOptionValuesHelper::options_value($essb_options, 'counter_recover_mode');
		$recover_protocol = ESSBOptionValuesHelper::options_value($essb_options, 'counter_recover_protocol');
		$recover_from_other_domain = ESSBOptionValuesHelper::options_value($essb_options, 'counter_recover_domain');
		$recover_from_new_domain = ESSBOptionValuesHelper::options_value($essb_options, 'counter_recover_newdomain');
		$counter_recover_slash = ESSBOptionValuesHelper::options_bool_value($essb_options, 'counter_recover_slash');
		
		if (empty($recover_from_new_domain) && $recover_mode == "domain") {
			$recover_from_new_domain = get_site_url();
		}
		
		// Setup the Default Permalink Structure
		if($recover_mode == 'default') {
			$domain = get_site_url();
			$new_url = $domain.'/?p='.$id;
		}
	
		// Setup the "Day and name" Permalink Structure
		if ($recover_mode == 'dayname') {
			$domain = get_site_url();
			$date = get_the_date('Y/m/d',$id);
			$slug = basename(get_permalink($id));
			$new_url = $domain.'/'.$date.'/'.$slug.'/';
		}
		// Setup the "Month and name" Permalink Structure
		if ($recover_mode == 'monthname') {
			$domain = get_site_url();
			$date = get_the_date('Y/m',$id);
			$slug = basename(get_permalink($id));
			$new_url = $domain.'/'.$date.'/'.$slug.'/';
		}
		// Setup the "Numeric" Permalink Structure
		if ($recover_mode == 'numeric') {
			$domain = get_site_url();
			$new_url = $domain.'/archives/'.$id.'/';
		}
		// Setup the "Post name" Permalink Structure
		if ($recover_mode == 'postname') {
			$domain = get_site_url();
			$post_data = get_post($id, ARRAY_A);
			$slug = $post_data['post_name'];
			$new_url = $domain.'/'.$slug.'/';
		}
		
		if ($recover_mode == "domain" && !empty($recover_from_other_domain)) {
			$current_site_url = get_site_url();
			if (!empty($recover_from_new_domain)) {
				$current_site_url = $recover_from_new_domain;
			}
			$new_url = str_replace($current_site_url, $recover_from_other_domain, $url);
		}
		
		
		if ($recover_protocol == "http2https") {
			$new_url = str_replace('https://','http://',$new_url);
		}
		
		if ($recover_protocol == "https2http") {
			$new_url = str_replace('http://','https://',$new_url);
		}
		
		if ($counter_recover_slash) {
			$new_url = rtrim($new_url,"/");
		}
		
		return $new_url;
	
	}
	
	public static function is_matching_recovery_date($post_id, $recover_till_date) {
		$is_matching = true;
		
		$post_publish_date = get_the_date("Y-m-d", $post_id);
		
		if (!empty($post_publish_date)) {
			$recover_till_time = strtotime($recover_till_date);
			$post_publish_time = strtotime($post_publish_date);
			
			if ($post_publish_time < $recover_till_time) {
				$is_matching = true;
			}
			else {
				$is_matching = false;
			}
		}
		
		return $is_matching;
	}
}

?>