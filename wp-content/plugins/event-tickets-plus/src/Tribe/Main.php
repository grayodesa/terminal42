<?php

// Don't load directly
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

if ( ! class_exists( 'Tribe__Tickets_Plus__Main' ) ) {

	class Tribe__Tickets_Plus__Main {

		/**
		 * Current version of this plugin
		 */
		const VERSION = '4.2.2';

		/**
		 * Min required Tickets Core version
		 */
		const REQUIRED_TICKETS_VERSION = '4.2.2';

		/**
		 * Directory of the plugin
		 *
		 * @var
		 */
		public $plugin_dir;

		/**
		 * Path of the plugin
		 *
		 * @var
		 */
		public $plugin_path;

		/**
		 * Holds an instance of Tribe__Tickets_Plus__PUE
		 *
		 * @var Tribe__Tickets_Plus__PUE
		 */
		public $pue;

		/**
		 * Holds an instance of Tribe__Tickets_Plus__Commerce__Loader
		 *
		 * @var Tribe__Tickets_Plus__Commerce__Loader
		 */
		protected static $commerce_loader;

		/**
		 * Holds an instance of Tribe__Tickets_Plus__QR
		 *
		 * @var Tribe__Tickets_Plus__QR
		 */
		protected static $qr;

		/**
		 * @var Tribe__Tickets_Plus__Meta
		 */
		protected static $meta;

		/**
		 * Holds an instance of Tribe__Tickets_Plus__APM
		 *
		 * @var Tribe__Tickets_Plus__APM
		 */
		protected static $apm_filters;

		/**
		 * Get (and instantiate, if necessary) the instance of the class
		 *
		 * @static
		 * @return Tribe__Tickets_Plus__Main
		 */
		public static function instance() {
			static $instance;

			if ( ! $instance instanceof self ) {
				$instance = new self;
			}

			return $instance;
		}

		public function __construct() {
			$this->plugin_path = trailingslashit( EVENT_TICKETS_PLUS_DIR );
			$this->plugin_dir  = trailingslashit( basename( $this->plugin_path ) );
			$this->pue         = new Tribe__Tickets_Plus__PUE;

			add_action( 'init', array( $this, 'init' ), 9 );

			$this->apm_filters();

			add_action( 'init', array( $this, 'csv_import_support' ) );
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
			add_filter( 'tribe_support_registered_template_systems', array( $this, 'add_template_updates_check' ) );
			add_filter( 'tribe_tickets_settings_systems_supporting_login_requirements', array( $this, 'register_login_setting' ) );

			// Unique ticket identifiers
			add_action( 'event_tickets_rsvp_attendee_created', array( Tribe__Tickets_Plus__Meta__Unique_ID::instance(), 'assign_unique_id' ), 10, 2 );
			add_action( 'event_ticket_woo_attendee_created', array( Tribe__Tickets_Plus__Meta__Unique_ID::instance(), 'assign_unique_id' ), 10, 2 );
			add_action( 'event_ticket_edd_attendee_created', array( Tribe__Tickets_Plus__Meta__Unique_ID::instance(), 'assign_unique_id' ), 10, 2 );
			add_action( 'event_tickets_shopp_attendee_created', array( Tribe__Tickets_Plus__Meta__Unique_ID::instance(), 'assign_unique_id' ), 10, 2 );
			add_action( 'event_tickets_wpec_attendee_created', array( Tribe__Tickets_Plus__Meta__Unique_ID::instance(), 'assign_unique_id' ), 10, 2 );
		}

		public function init() {
			$this->register_resources();
			$this->commerce_loader();
			$this->meta();
			$this->tickets_view();
			$this->qr();
			$this->attendees_list();
		}

		public function register_resources() {
			wp_register_style( 'event-tickets-plus-tickets', plugins_url( 'resources/css/tickets.css', dirname( __FILE__ ) ), array( 'dashicons' ),
				Tribe__Tickets__Main::instance()->css_version() );
			wp_register_script( 'event-tickets-plus-attendees-list', plugins_url( 'resources/js/attendees-list.js', dirname( __FILE__ ) ), array( 'jquery' ),
				Tribe__Tickets__Main::instance()->js_version(), true );
		}

		public function enqueue_scripts() {
			wp_enqueue_style( 'event-tickets-plus-tickets' );
			wp_enqueue_script( 'event-tickets-plus-attendees-list' );
			$post_types = Tribe__Tickets__Main::instance()->post_types();
		}

		/**
		 * Creates the Tickets FrontEnd facing View class
		 *
		 * This will happen on `plugins_loaded` by default
		 *
		 * @return Tribe__Tickets_Plus__Tickets_View
		 */
		public function tickets_view() {
			return Tribe__Tickets_Plus__Tickets_View::hook();
		}

		/**
		 * Object accessor method for the Commerce Loader
		 *
		 * @return Tribe__Tickets_Plus__Commerce__Loader
		 */
		public function commerce_loader() {
			if ( ! self::$commerce_loader ) {
				self::$commerce_loader = new Tribe__Tickets_Plus__Commerce__Loader;
			}

			return self::$commerce_loader;
		}

		/**
		 * Object accessor method for QR codes
		 *
		 * @return Tribe__Tickets_Plus__QR
		 */
		public function qr() {
			if ( ! self::$qr ) {
				self::$qr = new Tribe__Tickets_Plus__QR;
			}

			return self::$qr;
		}

		/**
		 * Object accessor method for APM filters.
		 *
		 * @return Tribe__Tickets_Plus__APM
		 */
		public function apm_filters() {
			if ( ! class_exists( 'Tribe_APM' ) ) {
				return null;
			}

			if ( ! self::$apm_filters ) {
				self::$apm_filters = new Tribe__Tickets_Plus__APM();
			}

			return self::$apm_filters;
		}

		/**
		 * Object accessor method for Ticket meta
		 *
		 * @return Tribe__Tickets_Plus__Meta
		 */
		public function meta() {
			if ( ! self::$meta ) {
				self::$meta = new Tribe__Tickets_Plus__Meta( $this->plugin_path );
			}

			return self::$meta;
		}

		protected static $attendees_list;

		public function attendees_list() {
			if ( ! self::$attendees_list ) {
				self::$attendees_list = Tribe__Tickets_Plus__Attendees_List::hook();
			}

			return self::$attendees_list;
		}

		/**
		 * Setup integration with The Events Calendar's CSV import facilities.
		 *
		 * Expects to run during the init action - we don't want to set this up
		 * too early otherwise the commerce loader may not be able to reliably
		 * determine the version numbers of any active ecommerce plugins.
		 */
		public function csv_import_support() {
			// CSV import is not a concern unless The Events Calendar is also running
			if ( ! class_exists( 'Tribe__Events__Main' ) ) {
				return;
			}

			$commerce_loader = $this->commerce_loader();

			if ( ! $commerce_loader->has_commerce_providers() ) {
				return;
			}

			$column_names_filter  = Tribe__Tickets_Plus__CSV_Importer__Column_Names::instance( $commerce_loader );
			$importer_rows_filter = Tribe__Tickets_Plus__CSV_Importer__Rows::instance( $commerce_loader );

			add_filter( 'tribe_events_import_options_rows', array( $importer_rows_filter, 'filter_import_options_rows' ) );

			if ( $commerce_loader->is_woocommerce_active() ) {
				add_filter( 'tribe_event_import_tickets_woo_column_names', array( $column_names_filter, 'filter_tickets_woo_column_names' ) );
				add_filter( 'tribe_events_import_tickets_woo_importer', array( 'Tribe__Tickets_Plus__CSV_Importer__Tickets_Importer', 'woo_instance' ), 10, 2 );
			}

			add_filter( 'tribe_events_import_type_titles_map', array( $column_names_filter, 'filter_import_type_titles_map' ) );
		}

		/**
		 * Register Event Tickets Plus with the template update checker.
		 *
		 * @param array $plugins
		 *
		 * @return array
		 */
		public function add_template_updates_check( $plugins ) {
			// ET+ views can be in one of a range of different subdirectories (eddtickets, shopptickets
			// etc) so we will tell the template checker to simply look in views/tribe-events and work
			// things out from there
			$plugins[ __( 'Event Tickets Plus', 'event-tickets-plus' ) ] = array(
				self::VERSION,
				$this->plugin_path . 'src/views',
				trailingslashit( get_stylesheet_directory() ) . 'tribe-events',
			);

			return $plugins;
		}

		/**
		 * Gets the view from the plugin's folder, or from the user's theme if found.
		 *
		 * @param $template
		 *
		 * @return mixed|void
		 */
		public function get_template_hierarchy( $template ) {

			if ( substr( $template, - 4 ) != '.php' ) {
				$template .= '.php';
			}

			if ( $theme_file = locate_template( array( 'tribe-events/' . $template ) ) ) {
				$file = $theme_file;
			} else {
				$file = $this->plugin_path . 'src/views/' . $template;
			}

			return apply_filters( 'tribe_events_tickets_template_' . $template, $file );
		}

		/**
		 * Filters the list of ticket login requirements, making it possible to require that users
		 * be logged in before purchasing tickets.
		 *
		 * @param array $options
		 *
		 * @return array
		 */
		public function register_login_setting( array $options ) {
			$options[ 'event-tickets-plus_all' ] = __( 'Require users to log in before they purchase tickets', 'event-tickets-plus' );
			return $options;
		}
	}
}
