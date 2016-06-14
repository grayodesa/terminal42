<?php

/**
 * Class MC4WP_Form_Notification_Factory
 *
 * @ignore
 */
class MC4WP_Form_Notification_Factory {

	/**
	 * MC4WP_Form_Notification_Factory constructor.
	 *
	 * @param MC4WP_Plugin $plugin
	 */
	public function __construct( MC4WP_Plugin $plugin ) {
		$this->plugin = $plugin;
	}

	public function add_hooks() {
		add_filter( 'mc4wp_form_settings', array( $this, 'settings' ) );
		add_action( 'mc4wp_form_subscribed',array( $this, 'send_form_notification' ), 10, 4 );
	}

	/**
	 * @param array $settings
	 *
	 * @return array
	 */
	public function settings( $settings ) {

		static $defaults;

		// load defaults
		if( ! $defaults ) {
			$defaults = include $this->plugin->dir( '/config/default-settings.php' );
		}

		// make sure container is an array
		if( empty( $settings['email_notification'] ) ) {
			$settings['email_notification'] = array();
		}

		// merge with default settings
		$settings['email_notification'] = array_merge( $defaults, $settings['email_notification'] );

		return $settings;
	}


	/**
	 * @param MC4WP_Form $form
	 * @param string $email
	 * @param array $merge_vars Merge vars that were sent to MailChimo
	 * @param array $pretty_data Pretty representation of data that was sent to MailChimp
	 *
	 * @return bool
	 */
	public function send_form_notification( MC4WP_Form $form, $email, $merge_vars = array(), $pretty_data = array() ) {

		if ( ! $form->settings['email_notification']['enabled'] ) {
			return false;
		}

		// for BC with MailChimp for WordPress < 3.1.6
		if( is_array( $email ) ) {
			$pretty_data = $merge_vars;
			$merge_vars = $email;
		}

		$email = new MC4WP_Email_Notification(
			$form->settings['email_notification']['recipients'],
			$form->settings['email_notification']['subject'],
			$form->settings['email_notification']['message_body'],
			$form->settings['email_notification']['content_type'],
			$form,
			$merge_vars,
			$pretty_data
		);

		// TODO: Move this into a queue which is processed in the background?
		$email->send();
	}

}