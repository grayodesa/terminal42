<?php
session_start();

$parse_uri = explode('wp-content', $_SERVER['SCRIPT_FILENAME']);
$wp_load = $parse_uri[0].'wp-load.php';
require_once( $wp_load );

if( isset( $_GET['username'] ) AND $_GET['username'] != '' ):

    require_once('oauth/twitteroauth.php'); //Path to twitteroauth library
    
    $username = $_GET['username'];
    $limit = ( isset( $_GET['count'] ) AND $_GET['count'] != '' ) ? $_GET['count'] : 2;
    $consumerkey = "LAxZjcBtFOw1wYhJ2Tqm8w";
    $consumersecret = "0SyeOk44o70ya2EVweHgJtwqBkr6DaoO0CETToFKsI";
    $accesstoken = "329620819-0tbQsQ7yx9BV5E6SzPaP39UAlDBuUokwRqgw075H";
    $accesstokensecret = "iL0divFR8xjTWQj9de511YxBpKkduE8dcOCRRlMrM";
    
    function getConnectionWithAccessToken($cons_key, $cons_secret, $oauth_token, $oauth_token_secret) {
      $connection = new TwitterOAuth($cons_key, $cons_secret, $oauth_token, $oauth_token_secret);
      return $connection;
    }
    
    $interval = 600;
    
    $twitterfeedop = 'coworker_twitterfeed_' . $username . '_' . $limit;
    
    $twitterfeedoption = get_option( $twitterfeedop );
    
	if ( $twitterfeedoption ) {
		$last = $twitterfeedoption['date'];
	} else { $last = false; }
    
	$now = time();
    
    $savetwitterfeed = array();
    
	if ( !$last || (( $now - $last ) > $interval) ) {
    
		$connection = getConnectionWithAccessToken($consumerkey, $consumersecret, $accesstoken, $accesstokensecret);
        $twitter_feed = $connection->get("https://api.twitter.com/1.1/statuses/user_timeline.json?screen_name=".$username."&count=".$limit);
        
		$cache_rss = serialize($twitter_feed);
        
		if (!empty($cache_rss)) {
			
            $savetwitterfeed['date'] = $now;
            $savetwitterfeed['feed'] = $cache_rss;
            
            update_option( $twitterfeedop, $savetwitterfeed );
            
		}
        
        $twitterfeedoption = get_option( $twitterfeedop );
        
		$rss = @unserialize( $twitterfeedoption['feed'] );
	} else {
        $rss = @unserialize( $twitterfeedoption['feed'] );
	}
    
    echo json_encode($rss);

endif;

?>