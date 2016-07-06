<?php
if (!class_exists('EasySocialMetricsUpdater')) {
class EasySocialMetricsUpdater {

	private $options;
	private $update_provider;

	public function __construct($options = false) {
		global $essb_options;
		// Set options
		$this->options = ($options) ? $options : $essb_options;

		// Import adapters for 3rd party services
		if (class_exists('EasySocialMetricsLiteSharedCountUpdater')) {
			$this->update_provider = new EasySocialMetricsLiteSharedCountUpdater();
		}

		// Check post on each page load
		add_action( 'wp_head', array($this, 'checkThisPost'));

		// Set up event hooks
		add_action( 'easy_social_metrics_lite_automatic_update', array( $this, 'scheduleFullDataSync' ) );
		add_action( 'easy_social_metrics_update_single_post', array( $this, 'updatePostStats' ), 10, 1 );

	} // end constructor

	public function syncURLTest ($url) {
		if (class_exists('EasySocialMetricsLiteSharedCountUpdater')) {
			$SharedCountUpdater = new EasySocialMetricsLiteSharedCountUpdater();
			$SharedCountUpdater->syncURLTest($url);
		}
	}
	
	/**
	* Check to see if this post requires an update, and if so schedule it.
	*
	* @param int $post_id the post id to check. Defaults to current ID.
	* @return
	*/
	public function checkThisPost($post_id = 0) {

		global $post;

		if (!isset($post)) {
			return false;
		}
		// If no post ID specified, use current page
		if ($post_id <= 0) $post_id = $post->ID;

		// Validation
		if (is_admin()) 							return false;
		if ($post_id <= 0) 							return false; 
		if ($post->post_status != 'publish') 		return false; // Allow only published posts
		if (!is_singular($this->get_post_types()))	return false; // Allow singular view of enabled post types

		// Check TTL timeout
		$last_updated = get_post_meta($post_id, "esml_socialcount_LAST_UPDATED", true);
		$ttl = $this->options['esml_ttl'] * 3600;
		//if (ESSB_ESML_DEBUG) {
		//	$ttl = 0;
		//}

		// If no timeout
		$temp = time() - $ttl;
		if ($last_updated < $temp) {

			// Schedule an update
			wp_schedule_single_event( time(), 'easy_social_metrics_update_single_post', array( $post_id ) );

		} 

		return;
	} // end checkThisPost()

	// Return an array of post types we currently track
	public function get_post_types() {

		$types_to_track = array();

		/*$smt_post_types = get_post_types( array('public'=>true), 'names' ); 
		unset($smt_post_types['attachment']);

		foreach ($smt_post_types as $type) {
			if ($this->options['smt_options_post_types_'.$type] == $type) $types_to_track[] = $type;
		}*/
		$pts = get_post_types ( array ('public' => true, 'show_ui' => true, '_builtin' => true ) );
		$cpts = get_post_types ( array ('public' => true, 'show_ui' => true, '_builtin' => false ) );
		$options = $this->options;
		
		if (is_array($options)) {
			if (!isset($options['esml_monitor_types'])) {
				$options['esml_monitor_types'] = array();
			}
		}
		
		if (is_array ( $options ) && isset ( $options ['esml_monitor_types'] ) && is_array ( $options ['esml_monitor_types'] )) {
		
			global $wp_post_types;
			// classical post type listing
			foreach ( $pts as $pt ) {
		
				$selected = in_array ( $pt, $options ['esml_monitor_types'] ) ? '1' : '0';
		
				if ($selected == '1') {
					$types_to_track[] = $pt;
				}
				
			}
		
			// custom post types listing
			if (is_array ( $cpts ) && ! empty ( $cpts )) {
				foreach ( $cpts as $cpt ) {

					$selected = in_array ( $cpt, $options ['esml_monitor_types'] ) ? '1' : '0';
					
					if ($selected == '1') {
						$types_to_track[] = $cpt;
					}
						
					$selected = in_array ( $cpt, $options ['esml_monitor_types'] ) ? 'checked="checked"' : '';
				}
			}
		}

		return $types_to_track;

	}

	/**
	* Fetch new stats from remote services and update post social score.
	*
	* @param  int    $post_id  The ID of the post to update
	* @return
	*/
	public function updatePostStats($post_id) {

		// Data validation
		if ($post_id <= 0) return false;

		// Remove secure protocol from URL
		//$permalink = str_replace("https://", "http://", get_permalink($post_id));
		$permalink = get_permalink($post_id);

		// Retrieve 3rd party data updates
		do_action('easy_social_metrics_data_sync', $post_id, $permalink);

		// Last updated time
		update_post_meta($post_id, "esml_socialcount_LAST_UPDATED", time());

		// Get comment count from DB
		$post = get_post($post_id);

		// Calculate aggregate score.
		/*$social_aggregate_score_detail = $this->calculateScoreAggregate(
																	get_post_meta($post_id, 'esml_socialcount_TOTAL', true),
																	get_post_meta($post_id, 'esml_ga_pageviews', true),
																	$post->comment_count
																	);
		
		// Calculate decayed score.
		$social_aggregate_score_decayed = $this->calculateScoreDecay($social_aggregate_score_detail['total'], $post->post_date);

		update_post_meta($post_id, "esml_social_aggregate_score", $social_aggregate_score_detail['total']);
		update_post_meta($post_id, "esml_social_aggregate_score_detail", $social_aggregate_score_detail);
		update_post_meta($post_id, "esml_social_aggregate_score_decayed", $social_aggregate_score_decayed);
		update_post_meta($post_id, "esml_social_aggregate_score_decayed_last_updated", time());

		$smt_stats['esml_social_aggregate_score'] = $social_aggregate_score_detail['total'];
		$smt_stats['esml_social_aggregate_score_decayed'] = $social_aggregate_score_decayed;

		// Custom action hook allows us to extend this function.
		do_action('easy_social_metrics_data_sync_complete', $post_id, $smt_stats);
*/
		return;
	} // end updatePostStats()

	/**
	* Combine Social, Views, and Comments into one aggregate value
	*
	* @param The input values for social, views, and comments
	* @return An array representing the weighted score of all three input values
	*/
	public function calculateScoreAggregate($social_num = 0, $views_num = 0, $comment_num = 0) {

		// Configuration
		$social_weight 	= 1;
		$view_weight	= 0.1;
		$comment_weight	= 1;

		// Calculate weighted points
		$social_points 	= $social_num	* $social_weight;
		$view_points 	= $views_num 	* $view_weight;
		$comment_points = $comment_num 	* $comment_weight;

		$data = array(
			'total' 			=> $social_points + $view_points + $comment_points,
			'social_points'		=> $social_points,
			'view_points'		=> $view_points,
			'comment_points'	=> $comment_points
		);

		return $data;
	} // end calculateScoreAggregate()


	/**
	* Reduces a number over time based on how much time has elapsed since inception.
	*
	* Purpose: To lower the score of posts over time so that older posts do not display on top.
	*
	* @param  int  		$score  The original number
	* @param  string  	$datePublished The date string of when the content was published; parsed with strtotime();
	* @return float The decayed score
	*/
	public function calculateScoreDecay($score, $datePublished) {

		// Config
		$GRACE_PERIOD = 10.5;
		$SECONDS_PER_DAY = 60*60*24;
		$BOOST_PERIOD = 5;

		// Data validation
		if (!$score) return false;
		if (!$datePublished) return false;
		if (($timestamp = strtotime($datePublished)) === false) return false;
		if (!$timestamp) return false;
		if ($score < 0 || $timestamp <= 0) return false;

		$daysActive = (time() - $timestamp) / $SECONDS_PER_DAY;

		// If newer than 5 days, boost.
		if ($daysActive < 5) {

			$k = $score / ($BOOST_PERIOD*$BOOST_PERIOD);
			$new_score = $k*($daysActive - $BOOST_PERIOD)*($daysActive - $BOOST_PERIOD) + $score;

		// If older than 5 days, decay.
		} else {
			$new_score = $score / (1.0 + pow(M_E,($daysActive - $GRACE_PERIOD)));
		}

		return  $new_score;
	} // end calculateScoreDecay()


	/**
	* Recalculate the score aggregate and decay values.
	*
	* Purpose: This only needs to be run when the parameters are changed for how to calculate scores. No new data is fetched and it is only used to recalculate based on the data in the DB.
	*
	* @param bool $print_output - If true, progress will be echoed while this function runs.
	* @return int the number of posts updated.
	*/
	public function recalculateAllScores($print_output = false) {

		/*$post_types = $this->get_post_types();
		
		// Get all posts which have social data
		$querydata = query_posts(array(
			'order'=>'DESC',
			'orderby'=>'post_date',
			'posts_per_page'=>-1,
			'post_status'   => 'publish',
			'post_type'		=> $post_types,
			'meta_query' => array(
				array(
				 'key' => 'esml_socialcount_LAST_UPDATED',
				 'compare' => '>=', // works!
				 'value' => '0' // This is ignored, but is necessary...
				)
			)
		));

		$total = array(
			'count' 		=> 0,
			'socialscore'	=> 0,
			'views'			=> 0,
			'comments'		=> 0
		);

		foreach ($querydata as $post ) {

			$socialcount_TOTAL = get_post_meta( $post->ID, 'esml_socialcount_TOTAL', true );
			$ga_pageviews = get_post_meta( $post->ID, 'esml_ga_pageviews', true );

			// Update aggregate score.
			$social_aggregate_score_detail = $this->calculateScoreAggregate($socialcount_TOTAL, $ga_pageviews, $post->comment_count);
			update_post_meta($post->ID, "esml_social_aggregate_score", $social_aggregate_score_detail['total']);
			update_post_meta($post->ID, "esml_social_aggregate_score_detail", $social_aggregate_score_detail);

			// Update decayed score.
			$social_aggregate_score_decayed = $this->calculateScoreDecay($social_aggregate_score_detail['total'], $post->post_date);
			update_post_meta($post->ID, "esml_social_aggregate_score_decayed", $social_aggregate_score_decayed);
			update_post_meta($post->ID, "esml_social_aggregate_score_decayed_last_updated", time());

			if ($print_output) {
				echo "Updated ".$post->post_title.", total: <b>".$social_aggregate_score_detail['total'] . "</b> decayed: ".$social_aggregate_score_decayed."<br>";
				flush();
			}

			$total['count']++;
			$total['socialscore'] += $socialcount_TOTAL;
			$total['views'] += $ga_pageviews;
			$total['comments'] += $post->comment_count;

		}

		if ($print_output) {
			echo "<hr><b>Update complete! ".$total['count']." posts updated.</b><hr>";

			echo "Average social score: ".round($total['socialscore'] / $total['count'], 2)."<br>";
			echo "Average views: ".round($total['views'] / $total['count'], 2)."<br>";
			echo "Average comments: ".round($total['comments'] / $total['count'], 2)."<br>";
			echo "<hr>";
		}
		wp_reset_query();
		return $total['count'];*/
		return 0;
	} // end recalculateAllScores()


	/**
	* Run a complete sync of all data. Download new stats for every single post in the DB.
	*
	* This should only be run when the plugin is first installed, or if data syncing was interrupted.
	*
	*/
	public static function scheduleFullDataSync() {
		global $essb_options;
		
		// We are going to stagger the updates so we do not overload the Wordpress cron.
		$nextTime = time();
		$interval = 5; // in seconds

		$post_types = EasySocialMetricsUpdater::get_post_types_static();
		$options = $essb_options;

		
		$nextTime = time();
		$interval = 5; // in seconds
		// In case the function does not finish, we want to start with posts that have NO data yet.
		/*$querydata = query_posts(array(
				'order'			=>'DESC',
				'orderby'		=>'post_date',
				'posts_per_page'=>-1,
				'post_status'   => 'publish',
				'post_type'		=> $post_types
		));*/
		
		$querydata1 = new WP_Query ( array ('posts_per_page' => - 1, 'post_status' => 'publish', 'post_type' => $post_types ) );
		//print_r($querydata);
		if ($querydata1->have_posts ()) :
			while ( $querydata1->have_posts () ) :
				$querydata1->the_post();
				global $post;
								
				wp_schedule_single_event( $nextTime, 'easy_social_metrics_update_single_post', array( $post->ID ) );
				$nextTime = $nextTime + $interval;
			endwhile;
			
		endif;
		//foreach ($querydata as $querydatum ) {
		//	wp_schedule_single_event( $nextTime, 'easy_social_metrics_update_single_post', array( $querydatum->ID ) );
		//	$nextTime = $nextTime + $interval;
		//}
		
		wp_reset_postdata();
		return;
	} // end scheduleFullDataSync()

	// Remove all queued updates from cron.
	public static function removeAllQueuedUpdates() {
		$crons = _get_cron_array();
		if ( !empty( $crons ) ) {
			foreach( $crons as $timestamp => $cron ) {
				// Remove single post updates
				if ( ! empty( $cron['easy_social_metrics_update_single_post'] ) )  {
					unset( $crons[$timestamp]['easy_social_metrics_update_single_post'] );
				}

				// Remove full post updates
				if ( ! empty( $cron['easy_social_metrics_lite_automatic_update'] ) )  {
					unset( $crons[$timestamp]['easy_social_metrics_lite_automatic_update'] );
				}
			}
			_set_cron_array( $crons );
			
			wp_clear_scheduled_hook('easy_social_metrics_update_single_post');
			wp_clear_scheduled_hook('easy_social_metrics_lite_automatic_update');
		}

		return;
	} // end removeAllQueuedUpdates()

	public static function printQueueLength() {
		$queue = array();
		$cron = _get_cron_array();
		foreach ( $cron as $timestamp => $cronhooks ) {
			foreach ( (array) $cronhooks as $hook => $events ) {
				foreach ( (array) $events as $key => $event ) {
					if ($hook == 'easy_social_metrics_update_single_post') {
						array_push($queue, $cron[$timestamp][$hook][$key]['args'][0]);
					}
				}
			}
		}

		$count = count($queue);
		if ($count >= 1) {
			$label = ($count >=2) ? ' items' : ' item';
			printf( '<div class="updated"> <p> %s </p> </div>',  'Currently updating <b>'.$count . $label.'</b> with the most recent social and analytics data... <a href="'.admin_url('admin.php?page=easy-social-metrics-lite&esml_sync_cancel=true').'" class="button">Cancel pending updates</a>');
		}
	} // end printQueueLength()
	
	public static function get_post_types_static() {
		global  $essb_options;
		$options = $essb_options;
		
		$types_to_track = array();
		
		
		if (is_array($options)) {
			if (!isset($options['esml_monitor_types'])) {
				$options['esml_monitor_types'] = array();
			}
		}
		
		
		if (is_array ( $options ) && isset ( $options ['esml_monitor_types'] ) && is_array ( $options ['esml_monitor_types'] )) {

			$types_to_track = $options ['esml_monitor_types'];
			
		}
		
		return $types_to_track;
	}	
	

} // END CLASS
}