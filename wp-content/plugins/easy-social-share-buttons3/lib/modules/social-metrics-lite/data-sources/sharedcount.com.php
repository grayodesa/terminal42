<?php
if (!class_exists('EasySocialMetricsLiteSharedCountUpdater')) {
class EasySocialMetricsLiteSharedCountUpdater {
	public function __construct() {
		// hook into post updater
		add_action('easy_social_metrics_data_sync', array($this, 'syncSharedCountData'), 10, 2);
	}

	public function syncURLTest($post_url) {
	
		global $essb_options;
		$apikey = ESSBOptionValuesHelper::options_value($essb_options, 'esml_sharedcount_api');
	
		// get social data from api.sharedcount.com
		$curl_handle = curl_init();
	
		//if (ESSB_ESML_DEBUG) {
		//	$post_url = 'http://www.google.com';
		//}
	
		curl_setopt($curl_handle, CURLOPT_URL, 'http://free.sharedcount.com/?url='.rawurlencode($post_url).'&apikey='.$apikey);
		curl_setopt($curl_handle, CURLOPT_CONNECTTIMEOUT, 3);
		curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, 1);
	
		$json = curl_exec($curl_handle);
		//print 'http://free.sharedcount.com/?url='.rawurlencode($post_url).'&apikey='.$apikey;
		//print $post_url;
		//print $json;
		curl_close($curl_handle);
	
		// reject if no response
		if (!strlen($json)) return;
	
		// decode social data from JSON
		$shared_count_service_data = json_decode($json, true);
	
		// prepare stats array
		$stats = array();
	
		// Stats we want to include in total
		$stats['facebook']    		= $shared_count_service_data['Facebook']['total_count'];
		$stats['twitter']     		= $shared_count_service_data['Twitter'];
		$stats['googleplus']  		= $shared_count_service_data['GooglePlusOne'];
		$stats['linkedin']    		= $shared_count_service_data['LinkedIn'];
		$stats['pinterest']   		= $shared_count_service_data['Pinterest'];
		$stats['diggs']       		= $shared_count_service_data['Diggs'];
		$stats['delicious']   		= $shared_count_service_data['Delicious'];
		$stats['reddit']      		= $shared_count_service_data['Reddit'];
		$stats['stumbleupon'] 		= $shared_count_service_data['StumbleUpon'];
	
		// Calculate total
		$stats['TOTAL'] = array_sum($stats);
	
		// Additional stats
		$stats['facebook_shares']   = $shared_count_service_data['Facebook']['share_count'];
		$stats['facebook_comments'] = $shared_count_service_data['Facebook']['comment_count'];
		$stats['facebook_likes']    = $shared_count_service_data['Facebook']['like_count'];
	
		// Calculate change since last update
		print "source: sharedcount.com<br/>";
		print_r($stats);
		print "";
	
	}
	
	public function syncSharedCountData($post_id, $post_url) {
		global $essb_options;
		$apikey = ESSBOptionValuesHelper::options_value($essb_options, 'esml_sharedcount_api');
		
		// reject if missing arguments
		if (!isset($post_id) || !isset($post_url))  return;

		// get social data from api.sharedcount.com
		$curl_handle = curl_init();
		
		//if (ESSB_ESML_DEBUG) {
		//	$post_url = 'http://www.google.com';
		//}

		//curl_setopt($curl_handle, CURLOPT_URL, 'http://api.sharedcount.com/?url='.rawurlencode($post_url));
		curl_setopt($curl_handle, CURLOPT_URL, 'http://free.sharedcount.com/?url='.rawurlencode($post_url).'&apikey='.$apikey);
		curl_setopt($curl_handle, CURLOPT_CONNECTTIMEOUT, 3);
		curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, 1);

		$json = curl_exec($curl_handle);

		curl_close($curl_handle);
		//print $json;
		// reject if no response
		if (!strlen($json)) return;

		
		// decode social data from JSON
		$shared_count_service_data = json_decode($json, true);

		// prepare stats array
		$stats = array();

		// Stats we want to include in total
		$stats['facebook']    		= $shared_count_service_data['Facebook']['total_count'];
		$stats['twitter']     		= $shared_count_service_data['Twitter'];
		$stats['googleplus']  		= $shared_count_service_data['GooglePlusOne'];
		$stats['linkedin']    		= $shared_count_service_data['LinkedIn'];
		$stats['pinterest']   		= $shared_count_service_data['Pinterest'];
		$stats['diggs']       		= $shared_count_service_data['Diggs'];
		$stats['delicious']   		= $shared_count_service_data['Delicious'];
		$stats['reddit']      		= $shared_count_service_data['Reddit'];
		$stats['stumbleupon'] 		= $shared_count_service_data['StumbleUpon'];

		// Calculate total
		$stats['TOTAL'] = array_sum($stats);

		// Additional stats
		$stats['facebook_shares']   = $shared_count_service_data['Facebook']['share_count'];
		$stats['facebook_comments'] = $shared_count_service_data['Facebook']['comment_count'];
		$stats['facebook_likes']    = $shared_count_service_data['Facebook']['like_count'];

		// Calculate change since last update
		$delta = array();
		$old_meta = get_post_custom($post_id);
		//foreach ($stats as $key => $value) if (is_int($value) && is_int($old_meta['esml_socialcount_'.$key][0])) $delta[$key] = $value - $old_meta['esml_socialcount_'.$key][0];

		// update post with populated stats
		foreach ($stats as $key => $value) if ($value) update_post_meta($post_id, 'esml_socialcount_'.$key, $value);

		//$this->saveToDB($post_id, $delta);

	}

	// Save only the change value to the DB
	private function saveToDB($post_id, $delta) {
		global $wpdb;

		$reset = date_default_timezone_get();
		date_default_timezone_set(get_option('timezone_string'));

		$args = array(
			'post_id' 	=> $post_id,
			'day_retrieved' => date("Y-m-d H:i:s", strtotime('today'))
		);

		date_default_timezone_set($reset);

		$existing = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix . "easy_social_metrics_log WHERE post_id = ".$args['post_id']." AND day_retrieved = '".$args['day_retrieved']."'", ARRAY_A);

		if ($existing === null) {

			// Create new entry
			$wpdb->insert( $wpdb->prefix . "easy_social_metrics_log", array_merge($args, $delta) );

		} else {

			// Add the existing values to the delta array
			foreach ($delta as $key => $val) if ($existing[$key] > 0) $delta[$key] = $existing[$key] + $val;

			// Update existing entry
			$wpdb->update($wpdb->prefix . "easy_social_metrics_log", $delta, $args);
			
		}
	}
}
}