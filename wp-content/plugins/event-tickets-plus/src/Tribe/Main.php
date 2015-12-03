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
		const VERSION = '4.0';

		/**
		 * Min required Tickets Core version
		 */
		const REQUIRED_TICKETS_VERSION = '4.0';

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
		 * @var Tribe__Tickets_Plus__PUE
		 */
		public $pue;

		/**
		 * Holds an instance of Tribe__Tickets_Plus__Commerce__Loader
		 * @var
		 */
		protected static $commerce_loader;

		/**
		 * Holds an instance of Tribe__Tickets_Plus__QR
		 * @var
		 */
		protected static $qr;

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
		}

		public function init() {
			$this->commerce_loader();
			$this->qr();
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
	}
}
