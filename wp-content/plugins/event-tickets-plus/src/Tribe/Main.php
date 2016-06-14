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
		const VERSION = '4.1.2';

		/**
		 * Min required Tickets Core version
		 */
		const REQUIRED_TICKETS_VERSION = '4.1';

		/**
		 * Directory of the plugin
		 * @var
		 */
		public $plugin_dir;

		/**
		 * Path of the plugin
		 * @var
		 */
		public $plugin_path;

		/**
		 * Holds an instance of Tribe__Tickets_Plus__PUE
		 * @var Tribe__Tickets_Plus__PUE
		 */
		public $pue;

		/**
		 * Holds an instance of Tribe__Tickets_Plus__Commerce__Loader
		 * @var Tribe__Tickets_Plus__Commerce__Loader
		 */
		protected static $commerce_loader;

		/**
		 * Holds an instance of Tribe__Tickets_Plus__QR
		 * @var Tribe__Tickets_Plus__QR
		 */
		protected static $qr;

		/**
		 * @var Tribe__Tickets_Plus__Meta
		 */
		protected static $meta;

		/**
		 * Holds an instance of Tribe__Tickets_Plus__APM
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
			$this->plugin_dir = trailingslashit( basename( $this->plugin_path ) );
			$this->pue = new Tribe__Tickets_Plus__PUE;

			add_action( 'init', array( $this, 'init' ), 9 );

			$this->apm_filters();

			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
			add_filter( 'tribe_support_registered_template_systems', array( $this, 'add_template_updates_check' ) );
		}

		public function init() {
			$this->register_resources();
			$this->commerce_loader();
			$this->meta();
			$this->qr();
			$this->attendees_list();
		}

		public function register_resources() {
			wp_register_style(
				'event-tickets-plus-tickets',
				plugins_url( 'resources/css/tickets.css', dirname( __FILE__ ) ),
				array( 'dashicons' ),
				Tribe__Tickets__Main::instance()->css_version()
			);
			wp_register_script(
				'event-tickets-plus-attendees-list',
				plugins_url( 'resources/js/attendees-list.js', dirname( __FILE__ ) ),
				array( 'jquery' ),
				Tribe__Tickets__Main::instance()->js_version(),
				true
			);
		}

		public function enqueue_scripts() {
			wp_enqueue_style( 'event-tickets-plus-tickets' );
			wp_enqueue_script( 'event-tickets-plus-attendees-list' );
			$post_types = Tribe__Tickets__Main::instance()->post_types();
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
		public function apm_filters(  ) {
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
	}
}
