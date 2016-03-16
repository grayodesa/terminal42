<?php

/**
 * Class MC4WP_Form_Notifications_Admin
 *
 * @ignore
 * @access private
 */
class MC4WP_Form_Notifications_Admin {

	/**
	 * @var MC4WP_Plugin
	 */
	protected $plugin;

	/**
	 * MC4WP_Form_Notifications_Admin constructor.
	 *
	 * @param MC4WP_Plugin $plugin
	 */
	public function __construct( MC4WP_Plugin $plugin ) {
		$this->plugin = $plugin;
	}

	/**
	 * Add hooks
	 */
	public function add_hooks() {
		add_action( 'admin_init', array( $this, 'maybe_run_upgrade_routines' ) );
		add_action( 'mc4wp_admin_enqueue_assets', array( $this, 'enqueue_assets' ) );
		add_filter( 'mc4wp_admin_edit_form_tabs', array( $this, 'add_tab' ) );
		add_action( 'mc4wp_admin_edit_form_output_emails_tab', array( $this, 'output_settings' ), 10, 2 );
		add_filter( 'mc4wp_form_sanitized_data', array( $this, 'sanitize_data' ), 10, 2 );
	}

	/**
	 * @param        $suffix
	 * @param string $page
	 */
	public function enqueue_assets( $suffix, $page = '' ) {

		if( empty( $_GET['view'] ) || $_GET['view'] !== 'edit-form' ) {
			return;
		}

		wp_enqueue_script( 'mc4wp-email-notifications', $this->plugin->url( "/assets/js/admin{$suffix}.js" ), array( 'mc4wp-forms-admin' ), $this->plugin->version(), true );
	}

	/**
	 * Maybe run upgrade routines
	 */
	public function maybe_run_upgrade_routines() {
		$from_version = get_option( 'mc4wp_email_notifications_version', 0 );
		$to_version = $this->plugin->version();

		// we're at the specified version already
		if( version_compare( $from_version, $to_version, '>=' ) ) {
			return;
		}

		$upgrade_routines = new MC4WP_Upgrade_Routines( $from_version, $to_version, $this->plugin->dir( '/migrations' ) );
		$upgrade_routines->run();
		update_option( 'mc4wp_email_notifications_version', $to_version );
	}

	/**
	 * @param array $tabs
	 *
	 * @return array
	 */
	public function add_tab( $tabs ) {
		$tabs['emails'] = __( 'Emails', 'mailchimp-for-wp' );
		return $tabs;
	}

	/**
	 * @param array $opts
	 * @param MC4WP_Form $form
	 */
	public function output_settings( $opts, $form ) {
		$opts = $opts['email_notification'];
		include $this->plugin->dir( '/views/settings.php' );
	}

	/**
	 * @param array $data
	 * @param array $raw_data
	 *
	 * @return array
	 */
	public function sanitize_data( $data, $raw_data ) {

		if( isset( $data['settings']['email_copy_receiver'] ) ) {
			$data['settings']['email_copy_receiver'] = sanitize_text_field( $data['settings']['email_copy_receiver'] );
		}

		return $data;
	}

}