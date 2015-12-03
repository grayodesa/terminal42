<?php

if( ! defined( 'MC4WP_VERSION' ) ) {
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	exit;
}

class MC4WP {

	/**
	* @var MC4WP_Form_Manager
	*/
	private $form_manager;

	/**
	* @var MC4WP_Checkbox_Manager
	*/
	private $checkbox_manager;

	/**
	* @var MC4WP_API
	*/
	private $api = null;

	/**
	* @var MC4WP_Logger
	*/
	private $log;

	/**
	 * @var
	 */
	private static $instance;

	/**
	 * @return MC4WP
	 */
	public static function instance() {
		return self::$instance;
	}

	/**
	 * Create an instance of the plugin
	 */
	public static function init() {

		if( self::$instance instanceof MC4WP ) {
			return false;
		}

		self::$instance = new MC4WP();
		return true;
	}

	/**
	* Constructor
	*/
	private function __construct() {

		// init checkboxes
		$this->checkbox_manager = new MC4WP_Checkbox_Manager();

		// forms
		add_action( 'init', array( $this, 'init_form_listener' ) );
		add_action( 'init', array( $this, 'init_form_manager' ) );

		// init logger, only if it's not disabled
		$disable_logging = apply_filters( 'mc4wp_disable_logging', false );
		if( false === $disable_logging ) {
			// initialize logging class
			$this->log = new MC4WP_Logger();
			$this->log->add_hooks();
		}

		// init widget
		add_action( 'widgets_init', array( $this, 'register_widget' ) );
	}


	/**
	 * Initialise the form listener
	 * @hooked `init`
	 */
	public function init_form_listener() {
		$listener = new MC4WP_Form_Listener();
		$listener->listen( array_merge( $_POST, $_GET ) );
	}

	/**
	 * Initialise the form manager
	 * @hooked `template_redirect`
	 */
	public function init_form_manager() {
		$this->form_manager = new MC4WP_Form_Manager();
		$this->form_manager->init();
	}

	/**
	* @return MC4WP_Form_Manager
	*/
	public function get_form_manager() {
		return $this->form_manager;
	}

	/**
	* @return MC4WP_Checkbox_Manager
	*/
	public function get_checkbox_manager() {
		return $this->checkbox_manager;
	}

	/**
	* Returns an instance of the MailChimp for WordPress API class
	*
	* @return MC4WP_API
	*/
	public function get_api() {

		if( $this->api === null ) {
			$opts = mc4wp_get_options( 'general' );
			$this->api = new MC4WP_API( $opts['api_key'] );
		}

		return $this->api;
	}

	/**
	 * @return MC4WP_Logger
	 */
	public function get_log() {
		return $this->log;
	}

	/**
	* Register the MC4WP_Widget
	*/
	public function register_widget() {
		register_widget( 'MC4WP_Widget' );
	}

}
