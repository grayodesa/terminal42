<?php

global $essb_options;
$plugin_options = $essb_options;
global $updater_instance;

if (!defined('ESSB_ESML_LOG')) {
	define('ESSB_ESML_LOG', 'easy_social_metrics_lite_log');
}
if (!defined('ESSB_ESML_DEBUG')) {
	define('ESSB_ESML_DEBUG', false);
}
//$wp_rewrite = new WP_Rewrite();
$update_provider = isset($plugin_options['esml_provider']) ? $plugin_options['esml_provider'] : '';

if ($update_provider == 'self') {
	require_once(ESSB3_PLUGIN_ROOT .'lib/modules/social-metrics-lite/data-sources/selfcount.php');	
}
else {
	require_once(ESSB3_PLUGIN_ROOT .'lib/modules/social-metrics-lite/data-sources/sharedcount.com.php');
}
require_once(ESSB3_PLUGIN_ROOT .'lib/modules/social-metrics-lite/esml-metricsupdater-class.php');
//include_once('SocialMetricsTrackerWidget.class.php');

if (!class_exists('EasySocialMetricsLite')) {
class EasySocialMetricsLite {

	private $version = '2.0'; // for db upgrade comparison
	private $updater;
	private $options;

	public function __construct() {
		global $plugin_options, $updater_instance;
		
		$this->options = $plugin_options;
		
		// activate automated update call
		if (is_array($this->options)) {
			$this->updater = $updater_instance;
		}
		
		
		if (is_admin()) {
			add_action('admin_init', array($this, 'activate'));
			add_action('admin_menu', array($this,'adminMenuSetup'));
			add_action('admin_enqueue_scripts', array($this, 'adminHeaderScripts'));
			add_action('admin_init', array($this, 'handle_user_commands'));
				
		}
		
	} // end constructor

	public function handle_user_commands() {
		global $updater_instance;
		if (!$this->updater) {
			$this->updater = $updater_instance;
		}
		// Manual data update for a post
		if (is_admin() && $this->updater && isset($_REQUEST['esml_sync_now'])) {
		
			$this->updater->updatePostStats($_REQUEST['esml_sync_now']);
			//header("Location: ".remove_query_arg('esml_sync_now'));
		}
		
		if (is_admin() && isset($_REQUEST['esml_sync_cancel'])) {
			EasySocialMetricsUpdater::removeAllQueuedUpdates();
			//header("Location: ".remove_query_arg('esml_sync_cancel'));
		}
		
		if (is_admin() && isset($_REQUEST['esml_sync_all'])) {
			//add_action('admin_init', array($this, 'fullDataUpdate'));
			EasySocialMetricsUpdater::scheduleFullDataSync();
			//header("Location: ".remove_query_arg('esml_sync_all'));
		}
		
		if (is_admin() && $this->updater && isset($_REQUEST['esml_test'])) {
			$this->updater->syncURLTest($_REQUEST['esml_test']);
			//header("Location: ".remove_query_arg('esml_sync_now'));
		}		
	}
	
	public function fullDataUpdate() {
		EasySocialMetricsUpdater::scheduleFullDataSync();
	}

	public static function regsiterUpdateCronPeriods($schedules) {
		
		$schedules['esml_1'] = array(
 			'interval' => 120,
 			'display' => __( 'Every 1 hour' )
	 	);
		$schedules['esml_2'] = array(
				'interval' => 7200,
				'display' => __( 'Every 2 hours' )
		);
		$schedules['esml_4'] = array(
				'interval' => 14400,
				'display' => __( 'Every 4 hours' )
		);
		$schedules['esml_8'] = array(
				'interval' => 28800,
				'display' => __( 'Every 8 hours' )
		);
		$schedules['esml_12'] = array(
				'interval' => 43200,
				'display' => __( 'Every 12 hours' )
		);
		$schedules['esml_24'] = array(
				'interval' => 86400,
				'display' => __( 'Every 24 hours' )
		);
		$schedules['esml_36'] = array(
				'interval' => 129600,
				'display' => __( 'Every 36 hours' )
		);
		$schedules['esml_48'] = array(
				'interval' => 172800,
				'display' => __( 'Every 48 hours' )
		);
		$schedules['esml_72'] = array(
				'interval' => 259200,
				'display' => __( 'Every 3 days' )
		);
		$schedules['esml_96'] = array(
				'interval' => 345600,
				'display' => __( 'Every 4 days' )
		);
		$schedules['esml_120'] = array(
				'interval' => 432000,
				'display' => __( 'Every 5 days' )
		);
		$schedules['esml_168'] = array(
				'interval' => 604800,
				'display' => __( 'Every 7 days' )
		);
		
		//print_r($schedules);
		
		return $schedules;	
	}
	
	function setupAutomatedFullUpdate() {
		global $essb_options;
		
		$ttl_period = isset($essb_options['esml_ttl']) ? $essb_options['esml_ttl'] : '';
		if ($ttl_period == '') {
			$ttl_period = '24';
		}
		
		$schedule_ttl = 'esml_'.$ttl_period;	
		
		if ( ! wp_next_scheduled( 'easy_social_metrics_lite_automatic_update' ) ) {
			wp_schedule_event( time(), $schedule_ttl,  'easy_social_metrics_lite_automatic_update');
		}
	}
	
	public function adminHeaderScripts() {
	} // end adminHeaderScripts()

	public function adminMenuSetup() {
		global $essb_options;

		// Add Social Metrics Tracker menu
		$visibility = ESSBOptionValuesHelper::options_value($essb_options, 'esml_access', 'manage_options');
		add_menu_page( 'Easy Social Metrics Lite', 'Easy Social Metrics Lite', $visibility, 'easy-social-metrics-lite', array($this, 'render_view'), 'dashicons-chart-bar' );

		//new SocialMetricsTrackerWidget();

	} // end adminMenuSetup()

	public function render_view() {
		require(ESSB3_PLUGIN_ROOT .'lib/modules/social-metrics-lite/esml-render-results-helper.php');
		require(ESSB3_PLUGIN_ROOT .'lib/modules/social-metrics-lite/esml-render-results.php');
		esml_render_dashboard_view($this->options);
	} 

	public static function timeago($time) {
		$periods = array("second", "minute", "hour", "day", "week", "month", "year", "decade");
		$lengths = array("60","60","24","7","4.35","12","10");

		$now = time();

			$difference     = $now - $time;
			$tense         = "ago";

		for($j = 0; $difference >= $lengths[$j] && $j < count($lengths)-1; $j++) {
			$difference /= $lengths[$j];
		}

		$difference = round($difference);

		if($difference != 1) {
			$periods[$j].= "s";
		}

		return "$difference $periods[$j] ago";
	}

	/***************************************************
	* Check the version of the plugin and perform upgrade tasks if necessary 
	***************************************************/
	public function version_check() {
		//$installed_version = get_option( "esml_version" );

		//if( $installed_version != $this->version ) {
		//	update_option( "esml_version", $this->version );

			// 
			// Do upgrade tasks
			//$this->db_setup();
			//EasySocialMetricsUpdater::scheduleFullDataSync();
		//$this->setupAutomatedFullUpdate();
		//}
		//$installed_version = get_option( "esml_version" );

		//if( $installed_version != $this->version ) {
			//update_option( "esml_version", $this->version );

			// 
			// Do upgrade tasks
			//$this->db_setup();
			//EasySocialMetricsUpdater::scheduleFullDataSync();
		//}
	}

	public function activate() {		
		//if (defined('WP_ENV') && strtolower(WP_ENV) != 'production' || $_SERVER['REMOTE_ADDR'] == '127.0.0.1') {
			// Do not schedule update
		//} else {
			// Sync all data
		//}
		
		
		//$this->version_check();
		$installed_version = get_option( "esml_version" );
		if( $installed_version != $this->version ) {
			update_option( "esml_version", $this->version );
			EasySocialMetricsUpdater::scheduleFullDataSync();
			$this->setupAutomatedFullUpdate();
		}
	}

	public function deactivate() {

		// Remove Queued Updates
		EasySocialMetricsUpdater::removeAllQueuedUpdates();

	}

} // END SocialMetricsTracker
}
// Run plugin
if (defined('ESSB3_ESML_ACTIVE')) {
	//add_action( 'plugins_loaded', 'esml_register_custom_cron_jobs' );
	
	//function esml_register_custom_cron_jobs() {
	add_filter( 'cron_schedules', 'ESMLregsiterUpdateCronPeriods3');
	//}
	$updater_instance = new EasySocialMetricsUpdater($plugin_options);
	$SocialMetricsTracker = new EasySocialMetricsLite();
}


function ESMLregsiterUpdateCronPeriods3($schedules) {

	$schedules['esml_1'] = array(
			'interval' => 3600,
			'display' => __( 'Every 1 hour' )
	);
	$schedules['esml_2'] = array(
			'interval' => 7200,
			'display' => __( 'Every 2 hours' )
	);
	$schedules['esml_4'] = array(
			'interval' => 14400,
			'display' => __( 'Every 4 hours' )
	);
	$schedules['esml_8'] = array(
			'interval' => 28800,
			'display' => __( 'Every 8 hours' )
	);
	$schedules['esml_12'] = array(
			'interval' => 43200,
			'display' => __( 'Every 12 hours' )
	);
	$schedules['esml_24'] = array(
			'interval' => 86400,
			'display' => __( 'Every 24 hours' )
	);
	$schedules['esml_36'] = array(
			'interval' => 129600,
			'display' => __( 'Every 36 hours' )
	);
	$schedules['esml_48'] = array(
			'interval' => 172800,
			'display' => __( 'Every 48 hours' )
	);
	$schedules['esml_72'] = array(
			'interval' => 259200,
			'display' => __( 'Every 3 days' )
	);
	$schedules['esml_96'] = array(
			'interval' => 345600,
			'display' => __( 'Every 4 days' )
	);
	$schedules['esml_120'] = array(
			'interval' => 432000,
			'display' => __( 'Every 5 days' )
	);
	$schedules['esml_168'] = array(
			'interval' => 604800,
			'display' => __( 'Every 7 days' )
	);

	return $schedules;
}