<?php
if (!class_exists('EasySocialMetricsLiteSharedCountUpdater')) {
class EasySocialMetricsLiteSharedCountUpdater {
	public function __construct() {
		// hook into post updater
		add_action('easy_social_metrics_data_sync', array($this, 'syncSharedCountData'), 10, 2);
	}

	public function syncURLTest($post_url) {
		$shared_count_service_data = $this->getAll($post_url);
		
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
		print "source: self execute<br/>";
		print_r($stats);
		print "";
	}
	
	public function syncSharedCountData($post_id, $post_url) {

		// reject if missing arguments
		if (!isset($post_id) || !isset($post_url))  return;

		// decode social data from JSON
		$shared_count_service_data = $this->getAll($post_url);

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
		foreach ($stats as $key => $value) if (is_int($value) && is_int($old_meta['esml_socialcount_'.$key][0])) $delta[$key] = $value - $old_meta['esml_socialcount_'.$key][0];

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
	
	private function getAll($url) {
		$output = array();
		
		$fb = $this->get_facebook($url);
		
		$output['Facebook'] = array();
		$output['Facebook']['total_count'] = $fb['count'];
		$output['Facebook']['comment_count'] = $fb['comments'];
		
		$tw = $this->get_tweets($url);
		$output['Twitter'] = $tw['count'];
		
		$gplus = $this->get_plusones($url);
		$output['GooglePlusOne'] = $gplus['count'];
		
		$lin = $this->get_linkedin($url);
		$output['LinkedIn'] = $lin['count'];
		
		$pin = $this->get_pinterest($url);
		$output['Pinterest'] = $pin['count'];
		
		$st = $this->get_stumble($url);
		$output['StumbleUpon'] = $st['count'];
		$output['Diggs'] = 0;
		$output['Delicious'] = 0;
		$output['Reddit'] = 0;
		$output['Facebook']['share_count'] = $fb['count'];
		$output['Facebook']['like_count'] = $fb['count'];
		
		return $output;
	}
	
	public function get_facebook($url) {
		$parse_url = 'https://graph.facebook.com/fql?q=SELECT%20like_count,%20total_count,%20share_count,%20click_count,%20comment_count%20FROM%20link_stat%20WHERE%20url%20=%20%22' . $url . '%22';
		$content = $this->parse ( $parse_url );
	
		$result = 0;
		$result_comments = 0;
	
		if ($content != '') {
			$content = json_decode ( $content, true );
				
			$data_parsers = $content['data'];
			$result = isset ( $data_parsers [0] ['total_count'] ) ? intval ( $data_parsers [0] ['total_count'] ) : 0;
			$result_comments = isset ( $data_parsers [0] ['comment_count'] ) ? intval ( $data_parsers [0] ['comment_count'] ) : 0;
		}
	
		return array ('count' => $result, 'comments' => $result_comments );
	}
	
	function get_tweets($url) {
		$json_string = $this->parse ( 'http://urls.api.twitter.com/1/urls/count.json?url=' . $url );
		$json = json_decode ( $json_string, true );
		$result = isset ( $json ['count'] ) ? intval ( $json ['count'] ) : 0;
	
		return array('count' => $result);
	}
	function get_linkedin($url) {
		$json_string = $this->parse( "http://www.linkedin.com/countserv/count/share?url=$url&format=json" );
		$json = json_decode ( $json_string, true );
		$result = isset ( $json ['count'] ) ? intval ( $json ['count'] ) : 0;
		return array('count' => $result);
	}
	
	function get_plusones($url) {
		$buttonUrl = sprintf('https://plusone.google.com/u/0/_/+1/fastbutton?url=%s', urlencode($url));
		//$htmlData  = file_get_contents($buttonUrl);
		$htmlData  = $this->parse($buttonUrl);
			
		@preg_match_all('#{c: (.*?),#si', $htmlData, $matches);
		$ret = isset($matches[1][0]) && strlen($matches[1][0]) > 0 ? trim($matches[1][0]) : 0;
		if(0 != $ret) {
			$ret = str_replace('.0', '', $ret);
		}
	
		return array('count' => $ret);
	}
	function get_pinterest($url) {
		$return_data = $this->parse ( 'http://api.pinterest.com/v1/urls/count.json?url=' . $url );
		$json_string = preg_replace ( '/^receiveCount\((.*)\)$/', "\\1", $return_data );
		$json = json_decode ( $json_string, true );
		$result = isset ( $json ['count'] ) ? intval ( $json ['count'] ) : 0;
	
		return array('count' => $result);
	}
	
	function get_stumble($url) {
		$json_string = $this->parse('http://www.stumbleupon.com/services/1.01/badge.getinfo?url='.$url);
		$json = json_decode($json_string, true);
		$result = isset($json['result']['views'])?intval($json['result']['views']):0;
	
		return array('count' => $result);
	}
	
	function get_reddit($url) {
		$reddit_url = 'http://www.reddit.com/api/info.json?url='.$url;
		$format = "json";
		$score = $ups = $downs = 0; //initialize
	
		/* action */
		$content = $this->parse( $reddit_url );
		if($content) {
			if($format == 'json') {
				$json = json_decode($content,true);
				foreach($json['data']['children'] as $child) { // we want all children for this example
					$ups+= (int) $child['data']['ups'];
					$downs+= (int) $child['data']['downs'];
					//$score+= (int) $child['data']['score']; //if you just want to grab the score directly
				}
				$score = $ups - $downs;
			}
		}
	
		return array('count' => $score);
	}
	
	function parse($encUrl) {
	
		$options = array (CURLOPT_RETURNTRANSFER => true, 		// return web page
				CURLOPT_HEADER => false, 		// don't return headers
				// CURLOPT_FOLLOWLOCATION => true, // follow
				// redirects
				CURLOPT_ENCODING => "", 		// handle all encodings
				CURLOPT_USERAGENT => 'EasySocialMetricsLite', 		// who am i
				CURLOPT_AUTOREFERER => true, 		// set referer on redirect
				CURLOPT_CONNECTTIMEOUT => 5, 		// timeout on connect
				CURLOPT_TIMEOUT => 10, 		// timeout on response
				CURLOPT_MAXREDIRS => 3, 		// stop after 3 redirects
				CURLOPT_SSL_VERIFYHOST => 0, CURLOPT_SSL_VERIFYPEER => false );
		$ch = curl_init ();
	
		if (ini_get ( 'open_basedir' ) == '' && ini_get ( 'safe_mode' == 'Off' )) {
			$options [CURLOPT_FOLLOWLOCATION] = true;
		}
	
		$options [CURLOPT_URL] = $encUrl;
		curl_setopt_array ( $ch, $options );
	
		$content = curl_exec ( $ch );
		$err = curl_errno ( $ch );
		$errmsg = curl_error ( $ch );
	
		curl_close ( $ch );
	
		return $content;
	}
}
}