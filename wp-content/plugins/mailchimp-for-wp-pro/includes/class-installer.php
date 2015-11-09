<?php

/**
 * Runs on plugin activations
 * - Transfer any settings which may have been set in the Lite version of the plugin
 * - Creates a post type 'mc4wp-form' and enters the form mark-up from the Lite version
 */
class MC4WP_Installer {

	/**
	 * @var
	 */
	protected $lite_options = array();

	/**
	 * Creates instance of installer class and then runs installation functions
	 */
	public static function run() {
		$installer = new self;
		$installer->transfer_options();
		$installer->create_default_forms();
	}

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->load_translations();
		$this->lite_options = $this->load_lite_options();
	}

	/**
	 * Load plugin translations (since we need 'em during installation)
	 */
	protected function load_translations() {
		// load the plugin text domain
		load_plugin_textdomain( 'mailchimp-for-wp', false, 'mailchimp-for-wp-pro/languages/' );
	}

	/**
	 * Returns the options set in the Lite version
	 *
	 * @return array
	 */
	protected function load_lite_options() {
		$options = array(
			'general' => (array) get_option( 'mc4wp_lite', array() ),
			'checkbox' => (array) get_option( 'mc4wp_lite_checkbox', array() ),
			'form' => (array) get_option( 'mc4wp_lite_form', array() )
		);

		return $options;
	}

	/**
	 * Transfer the options from the lite version, if set.
	 */
	public function transfer_options() {

		// check if PRO option exists and contains data entered by user
		$pro_options =  array(
			'general' => get_option( 'mc4wp', false ),
			'checkbox' => get_option( 'mc4wp_checkbox', false ),
			'form' => get_option( 'mc4wp_form', false )
		);

		// only bail if all three options have settings.
		if ( $pro_options['general'] !== false
		     && $pro_options['checkbox'] !== false
		     && $pro_options['form'] !== false )  {
			return false;
		}

		// create new settings array
		$settings = include dirname( __FILE__ ) . '/config/default-options.php';

		foreach ( $settings as $group_key => $options ) {
			foreach ( $options as $option_key => $option_value ) {
				if ( isset( $this->lite_options[$group_key][$option_key] ) ) {
					$settings[$group_key][$option_key] = $this->lite_options[$group_key][$option_key];
				}
			}
		}

		// store options
		update_option( 'mc4wp', $settings['general'] );
		update_option( 'mc4wp_checkbox', $settings['checkbox'] );
		update_option( 'mc4wp_form', $settings['form'] );
		return true;
	}

	/**
	 * Creates a few default forms
	 * - Default form (with paragraphs & labels)
	 * - Inline form (no paragraph)
	 */
	public function create_default_forms() {
		// Transfer form from Lite, but only if no Pro forms exist yet.
		$forms = get_posts(
			array(
				'post_type' => 'mc4wp-form',
				'post_status' => 'publish'
			)
		);

		if ( empty( $forms ) ) {

			// no Pro forms found, try to transfer from lite.
			// Transfer form settings too? Answer: no, they're inherited by default (from transferred options)
			$default_form_markup = include dirname( __FILE__ ) . '/config/default-form.php';
			$form_markup = ( isset( $this->lite_options['form']['markup'] ) ) ? $this->lite_options['form']['markup'] : $default_form_markup;
			$lists = isset( $this->lite_options['form']['lists'] ) ? $this->lite_options['form']['lists'] : array();

			// create default form
			$default_form_id = $this->create_form( "Default form", $form_markup, $lists );

			// set default form ID (for when no ID given in shortcode / function args)
			update_option( 'mc4wp_default_form_id', $default_form_id );

			// create inline form
			$inline_form_markup = include dirname( __FILE__ ) . '/config/inline-form.php';
			$this->create_form( "Short form", $inline_form_markup, $lists );
		}
	}

	/**
	 * Helper function, creates a form post-type.
	 *
	 * @param       $title
	 * @param       $content
	 * @param array $lists
	 * @return int $form_id
	 */
	protected function create_form( $title, $content, $lists = array() ) {
		$form_id = wp_insert_post(
			array(
				'post_type' => 'mc4wp-form',
				'post_title' => $title,
				'post_content' => $content,
				'post_status' => 'publish',
			)
		);

		update_post_meta( $form_id, '_mc4wp_settings', array( 'lists' => $lists ) );
		return $form_id;
	}

}