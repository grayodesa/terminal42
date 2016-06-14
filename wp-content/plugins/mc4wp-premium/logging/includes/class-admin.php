<?php

class MC4WP_Logging_Admin {

	/**
	 * @var MC4WP_Plugin
	 */
	protected $plugin;

	/**
	 * @param MC4WP_Plugin $plugin
	 */
	public function __construct( MC4WP_Plugin $plugin ) {
		$this->plugin = $plugin;
	}

	/**
	 * Add hooks
	 */
	public function add_hooks() {
		add_action( 'admin_init', array( $this, 'init' ) );

		add_filter( 'mc4wp_admin_menu_items', array( $this, 'menu_items' ) );
		add_action( 'mc4wp_dashboard_setup', array( $this, 'register_dashboard_widget' ) );
		add_action( 'mc4wp_admin_log_export', array( $this, 'run_log_exporter' ) );
		add_action( 'mc4wp_admin_enqueue_assets', array( $this, 'enqueue_assets' ) );
		add_action( 'mc4wp_admin_after_integration_settings', array( $this, 'show_link_to_integration_log' ), 60 );
		add_filter( 'set-screen-option', array( $this, 'set_screen_option' ), 10, 3 );
	}

	/**
	 * Add a link to log overview to each integration
	 *
	 * TODO: Make this more pretty (UX)
	 */
	public function show_link_to_integration_log( $integration ) {
		echo sprintf( '<p><a href="%s">' . __( 'Show sign-ups that used this integration.', 'mailchimp-for-wp' ) .'</a></p>', admin_url( 'admin.php?page=mailchimp-for-wp-reports&tab=log&view=' . $integration->slug ) );
	}

	/**
	 * Init
	 *
	 * @hooked `init`
	 */
	public function init() {
		$this->run_upgrade_routines();
	}

	/**
	 * Maybe run upgrade routines
	 */
	protected function run_upgrade_routines() {
		$from_version = get_option( 'mc4wp_log_version', 0 );
		$to_version = $this->plugin->version();

		// we're at the specified version already
		if( version_compare( $from_version, $to_version, '>=' ) ) {
			return;
		}

		$upgrade_routines = new MC4WP_Upgrade_Routines( $from_version, $to_version, $this->plugin->dir( '/migrations' ) );
		$upgrade_routines->run();
		update_option( 'mc4wp_log_version', $to_version );
	}

	/**
	 * Enqueue assets for log pages.
	 *
	 * @param string $suffix
	 */
	public function enqueue_assets( $suffix = '' ) {

		$page = empty( $_GET['page'] ) ? '' : $_GET['page'];
		$tab = empty( $_GET['tab'] ) ? 'statistics' : $_GET['tab'];

		/* Reports page */
		if ( $page === 'mailchimp-for-wp-reports' && $tab === 'statistics' ) {

			// load flot
			wp_register_script( 'mc4wp-flot', $this->plugin->url( '/assets/js/jquery.flot.min.js'), array( 'jquery' ), $this->plugin->version(), true );
			wp_register_script( 'mc4wp-flot-time', $this->plugin->url( 'assets/js/jquery.flot.time.min.js' ), array( 'jquery' ), $this->plugin->version(), true );
			wp_register_script( 'mc4wp-statistics', $this->plugin->url( 'assets/js/admin-statistics' ) . $suffix .'.js', array( 'jquery', 'mc4wp-flot', 'mc4wp-flot-time' ), $this->plugin->version(), true );
			wp_enqueue_script( 'mc4wp-statistics' );

			wp_register_style( 'mc4wp-admin-reports', $this->plugin->url( 'assets/css/admin' . $suffix . '.css' ), array( 'mc4wp-admin' ), $this->plugin->version() );
			wp_enqueue_style( 'mc4wp-admin-reports' );

			// print ie excanvas script in footer
			add_action( 'admin_print_footer_scripts', array( $this, 'print_excanvas_script' ), 1 );
		}
	}

	/**
	 * @param array $items
	 *
	 * @return array
	 */
	public function menu_items( $items ) {

		$items[ 'reports' ] = array(
			'title' => __( 'Reports', 'mailchimp-for-wp' ),
			'text' => __( 'Reports', 'mailchimp-for-wp' ),
			'slug' => 'reports',
			'callback' => array( $this, 'show_reports' ),
			'load_callback' => array( $this, 'add_screen_options' )
		);

		return $items;
	}

	/**
	 * Register dashboard widgets
	 */
	public function register_dashboard_widget() {

		// only show widget to people with required capability
		// @todo use real cap
		if( ! current_user_can( 'manage_options' ) ) {
			return false;
		}

		wp_add_dashboard_widget(
			'mc4wp_log_widget',         // Widget slug.
			'MailChimp Sign-Ups',         // Title.
			array( 'MC4WP_Dashboard_Log_Widget', 'make' ) // Display function.
		);
	}

	/**
	 * Run the log exporter
	 */
	public function run_log_exporter() {
		$args = array();
		$request = array_merge( $_POST, $_GET );

		if( ! empty( $request['start_year'] ) ) {
			$start_year = absint( $request['start_year'] );
			$start_month = ( isset( $request['start_month'] ) ) ? absint( $request['start_month'] ) : 1;
			$timestring = sprintf( '%s-%s', $start_year, $start_month );
			$args['datetime_after'] = date( 'Y-m-d 00:00:00', strtotime( $timestring ) );
		}

		if( ! empty( $request['end_year'] ) ) {
			$end_year = absint( $request['end_year'] );
			$end_month = ( isset( $request['end_month'] ) ) ? absint( $request['end_month'] ) : 12;
			$timestring = sprintf( '%s-%s', $end_year, $end_month );
			$args['datetime_before'] = date( 'Y-m-t 23:59:59', strtotime( $timestring ) );
		}

		$exporter = new MC4WP_Log_Exporter();
		$exporter->filter( $args );
		$exporter->output();
	}

	/**
	 * Show reports page
	 */
	public function show_reports() {
		$current_tab = ! empty( $_GET['tab'] ) ? $_GET['tab'] : 'statistics';
		$tab_method = 'show_' . $current_tab . '_page';

		if( method_exists( $this, $tab_method ) ) {
			call_user_func( array( $this, $tab_method ) );
		}
	}

	/**
	 * Show log page
	 */
	public function show_export_page() {
		$current_tab = 'export';
		include $this->plugin->dir( '/views/admin-reports.php' );
	}

	/**
	 * Show log page
	 */
	public function show_log_page() {
		$table = new MC4WP_Log_Table( new MC4WP_MailChimp() );
		$current_tab = 'log';
		include $this->plugin->dir( '/views/admin-reports.php' );
	}

	/**
	 * Show reports (stats) page
	 */
	public function show_statistics_page() {
		$current_tab = 'statistics';
		$graph = new MC4WP_Graph( $_GET );
		$graph->init();
		$settings = array( 'ticksize' => array( 1, $graph->step_size ) );

		// add scripts
		wp_localize_script( 'mc4wp-statistics', 'mc4wp_statistics_data', $graph->datasets );
		wp_localize_script( 'mc4wp-statistics', 'mc4wp_statistics_settings', $settings );

		$start_day = ( isset( $_GET['start_day'] ) ) ? $_GET['start_day'] : 0;
		$start_month = ( isset( $_GET['start_month'] ) ) ? $_GET['start_month'] : 0;
		$start_year = ( isset( $_GET['start_year'] ) ) ? $_GET['start_year'] : 0;
		$end_day = ( isset( $_GET['end_day'] ) ) ? $_GET['end_day'] : date('d');
		$end_month = ( isset( $_GET['end_month'] ) ) ? $_GET['end_month'] : date('m');
		$end_year = ( isset( $_GET['end_year'] ) ) ? $_GET['end_year'] : date('Y');

		include $this->plugin->dir( '/views/admin-reports.php' );
	}

	/**
	 * Print the IE canvas fallback script in the footer on statistics pages
	 */
	public function print_excanvas_script() {
		printf( '<!--[if lte IE 8]><script language="javascript" type="text/javascript" src="%s"></script><![endif]-->',  $this->plugin->url( '/assets/js/excanvas.min.js' ) );
	}

	/**
	 * Add screen options
	 */
	public function add_screen_options() {
		// do nothing if not on log page
		if( empty( $_GET['tab'] ) || $_GET['tab'] !== 'log' ) {
			return;
		}

		add_screen_option( 'per_page', array( 'default' => 20, 'option' => 'mc4wp_log_per_page' ) );
	}

	/**
	 * @param $status
	 * @param $option
	 * @param $value
	 *
	 * @return int
	 */
	public function set_screen_option( $status, $option, $value ) {
		if ( 'mc4wp_log_per_page' === $option ) {
			return $value;
		}
	}
}
