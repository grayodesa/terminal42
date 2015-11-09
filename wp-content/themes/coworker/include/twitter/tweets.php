<?php
session_start();

$parse_uri = explode('wp-content', $_SERVER['SCRIPT_FILENAME']);
$wp_load = $parse_uri[0].'wp-load.php';
require_once( $wp_load );

if( isset( $_GET['username'] ) AND $_GET['username'] != '' ):

    require_once('oauth/twitteroauth.php'); //Path to twitteroauth library
    
    $username = $_GET['username'];
    $limit = ( isset( $_GET['count'] ) AND $_GET['count'] != '' AND is_numeric( $_GET['count'] ) ) ? $_GET['count'] : 2;
    $consumerkey = semi_option( 'api_twitter_consumer' );
    $consumersecret = semi_option( 'api_twitter_consumer_secret' );
    $accesstoken = semi_option( 'api_twitter_access' );
    $accesstokensecret = semi_option( 'api_twitter_access_secret' );
    
    function getConnectionWithAccessToken($cons_key, $cons_secret, $oauth_token, $oauth_token_secret) {
      $connection = new TwitterOAuth($cons_key, $cons_secret, $oauth_token, $oauth_token_secret);
      return $connection;
    }
    
    $interval = 600;

    $feed_id = 'sm_twitter_feed_' . $username . '_' . $limit;
    
    $get_feed = get_transient( $feed_id );
    
	if ( false === ( $get_feed = get_transient( $feed_id ) ) ) {
    
		$connection = getConnectionWithAccessToken($consumerkey, $consumersecret, $accesstoken, $accesstokensecret);
        $twitter_feed = $connection->get( "https://api.twitter.com/1.1/statuses/user_timeline.json?screen_name=" . $username . "&count=" . $limit );
        
		$cache_rss = serialize( $twitter_feed );
        
		if (!empty($cache_rss)) {
			set_transient( $feed_id , $cache_rss, 600 );
		}
        
		$rss = @unserialize( get_transient( $feed_id ) );

	} else {

        $rss = @unserialize( $get_feed );
	
	}
    
    echo json_encode( $rss );

endif;

?>