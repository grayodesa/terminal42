<?php

class MC4WP_DB_Upgrader {

	/**
	 * @var int
	 */
	protected $database_version = 0;

	/**
	 * @var
	 */
	protected $code_version = 0;

	/**
	 * @var bool
	 */
	protected $installing = false;

	/**
	 * @param string $code_version The version we're upgrading to
	 * @param string $database_version The version the current database data is at
	 */
	public function __construct( $code_version, $database_version ) {
		$this->database_version = $database_version;
		$this->code_version = $code_version;
		$this->installing = ( $database_version === 0 );
	}

	/**
	 * Run the various upgrade routines, all the way up to the latest version
	 */
	public function run() {
		define( 'MC4WP_DOING_UPGRADE', true );

		// upgrade to 2.4
		if( ! $this->installing && version_compare( $this->database_version, '2.4', '<' ) ) {
			$this->upgrade_to_individual_form_styles();
		}

		// upgrade to 2.4.5
		if( ! $this->installing && version_compare( $this->database_version, '2.4.5', '<' ) ) {
			$this->set_new_custom_stylesheet_url();
		}

		// upgrade to 2.7
		if( ! $this->installing && version_compare( $this->database_version, '2.7', '<' ) ) {
			$this->change_success_message_key();
		}

		// upgrade to 2.7.24
		if( ! $this->installing && version_compare( $this->database_version, '2.7.24', '<' ) ) {
			$this->reschedule_usage_tracking();
		}

		update_option( 'mc4wp_version', MC4WP_VERSION );
	}

	/**
	 * Reschedule usage tracking (using the new interval)
	 */
	protected function reschedule_usage_tracking() {

		$options = get_option( 'mc4wp', array() );
		if( isset( $options['allow_usage_tracking'] ) && $options['allow_usage_tracking'] ) {
			$usage_tracking = MC4WP_Usage_Tracking::instance();
			$usage_tracking->disable();
			$usage_tracking->enable();
		}
	}

	/**
	 * @since v2.4.5
	 *
	 * Stylesheet URL's should be protocol relative
	 */
	protected function set_new_custom_stylesheet_url() {
		// get link to custom stylesheet
		$custom_stylesheet = get_option( 'mc4wp_custom_css_file', '' );

		// make sure link is protocol relative
		$custom_stylesheet = str_ireplace( array( 'http://', 'https://' ), '//', $custom_stylesheet );

		// update option again
		update_option( 'mc4wp_custom_css_file', $custom_stylesheet );
	}

	/**
	 * @since 2.4
	 *
	 * Instead of one global custom stylesheet, form styles are now stored per-form.
	 */
	protected function upgrade_to_individual_form_styles() {
		// upgrade custom form stylesheets
		$custom_form_styles = get_option( 'mc4wp_form_css', array() );

		// get all forms
		$forms = get_posts( 'post_type=mc4wp-form&posts_per_page=-1' );
		$form_styles = array();
		foreach( $forms as $form ) {
			$form_styles[ 'form-' . $form->ID ] = $custom_form_styles;
		}

		delete_option( 'mc4wp_form_css' );
		update_option( 'mc4wp_form_styles', $form_styles );
	}

	/**
	 * @since 2.7
	 *
	 * `text_success` becomes `text_subscribed`
	 */
	protected function change_success_message_key() {
		// set global text_subscribed message
		$options = get_option( 'mc4wp_form' );
		if( isset( $options['text_success'] ) ) {
			$options['text_subscribed'] = $options['text_success'];
			unset( $options['text_success'] );
		}

		update_option( 'mc4wp_form',$options );

		// set on a per-form basis
		$forms = get_posts(
			array(
				'post_type' => 'mc4wp-form',
				'post_status' => 'publish',
				'number' => -1
			)
		);
		if( $forms ) {
			foreach( $forms as $form ) {
				$meta = get_post_meta( $form->ID, '_mc4wp_settings', true );
				if( isset( $meta['text_success' ] ) && '' !== $meta['text_success'] ) {
					$meta['text_subscribed'] = $meta['text_success'];
				}
				update_post_meta( $form->ID, '_mc4wp_settings', $meta );
			}
		}

	}






}