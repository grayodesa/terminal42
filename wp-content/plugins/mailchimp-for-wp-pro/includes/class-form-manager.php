<?php

if( ! defined( 'MC4WP_VERSION' ) ) {
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	exit;
}

class MC4WP_Form_Manager
{
	/**
	 * @var array
	 */
	private $options = array();

	/**
	* @var int
	*/
	private $outputted_forms_count = 0;

	/**
	* @var boolean
	*/
	private $loaded_ajax_scripts = false;

	/**
	 * @var bool Is the inline CSS printed already?
	 */
	private $inline_css_printed = false;

	/**
	 * @var bool
	 */
	private $inline_js_printed = false;

	/**
	 * @var bool
	 */
	private $print_date_fallback = false;

	/**
	* Constructor
	*/
	public function __construct() {
		$this->options = mc4wp_get_options( 'form' );
	}

	/**
	* Initialize form stuff
	*
	* - Registers post type
	* - Registers scripts
	*/
	public function init() {

		$this->register_post_type();
		$this->register_shortcodes();
		$this->register_scripts();
		$this->add_hooks();
	}

	/**
	 * Adds the necessary hooks
	 */
	protected function add_hooks() {
		add_action( 'wp_enqueue_scripts', array( $this, 'load_stylesheet' ) );

		// enable shortcodes in text widgets
		add_filter( 'widget_text', 'shortcode_unautop' );
		add_filter( 'widget_text', 'do_shortcode', 11 );
		add_action( 'template_redirect', array( $this, 'show_form_preview' ) );

		// enable shortcodes in form content
		add_filter( 'mc4wp_form_content', 'do_shortcode' );
	}

	/**
	 * Registers the mc4wp-form post type
	 */
	protected function register_post_type() {
		// register post type
		register_post_type( 'mc4wp-form', array(
				'labels' => array(
					'name' => 'MailChimp Sign-up Forms',
					'singular_name' => 'Sign-up Form',
					'add_new_item' => 'Add New Form',
					'edit_item' => 'Edit Form',
					'new_item' => 'New Form',
					'all_items' => 'All Forms',
					'view_item' => null
				),
				'public' => false,
				'show_ui' => true,
				'show_in_menu' => false
			)
		);
	}

	/**
	 * Registers the [mc4wp_form] shortcode
	 */
	protected function register_shortcodes() {
		// register shortcodes
		add_shortcode( 'mc4wp_form', array( $this, 'output_form' ) );

		// @deprecated, use [mc4wp_form] instead
		add_shortcode( 'mc4wp-form', array( $this, 'output_form' ) );
	}

	/**
	 * Register the various JS files used by the plugin
	 */
	protected function register_scripts() {

		// should we load the minified script version?
		$suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '.js' : '.min.js';

		// register placeholder script, which will later be enqueued for IE only
		wp_register_script( 'mc4wp-placeholders', MC4WP_PLUGIN_URL . 'assets/js/placeholders.min.js', array(), MC4WP_VERSION, true );

		// register ajax script
		wp_register_script( 'mc4wp-ajax-forms', MC4WP_PLUGIN_URL . 'assets/js/ajax-forms' . $suffix, array( 'jquery' ), MC4WP_VERSION );

		// register non-AJAX script (that handles form submissions)
		wp_register_script( 'mc4wp-form-request', MC4WP_PLUGIN_URL . 'assets/js/form-request' . $suffix, array(), MC4WP_VERSION, true );

		// Load AJAX scripts on all pages if lazy load is disabled
		$lazy_load_ajax = apply_filters( 'mc4wp_lazy_load_ajax_scripts', true );
		if( true !== $lazy_load_ajax ) {
			$this->load_ajax_scripts();
		}
	}

	/**
	* Loads a basic HTML template to preview forms
	* @return boolean
	*/
	public function show_form_preview() {

		// make sure form_id is set and current user has required capabilities
		if( ! isset( $_GET['_mc4wp_css_preview'] ) || ! isset( $_GET['form_id'] ) || ! current_user_can( 'edit_posts' ) ) {
			return;
		}

		require MC4WP_PLUGIN_DIR . 'includes/views/pages/form-preview.php';
		die();
	}

	/**
	* Tells the plugin which shipped stylesheets to load.
	*
	* @return bool True if a stylesheet was enqueued
	*/
	public function load_stylesheet( ) {

		if( ! $this->options['css'] || isset( $_GET['_mc4wp_css_preview'] ) ) {
			return false;
		}

		$suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';

		switch( $this->options['css'] ) {
			case 'custom':
				return $this->load_custom_stylesheet();
				break;

			case 'custom-color':
				return $this->load_custom_color_stylesheet();
				break;

			case 'blue':
			case 'red':
			case 'green':
			case 'dark':
			case 'light':
				return $this->load_theme_stylesheet( $suffix );
				break;

			case true:
			case 'default':
				// load just the basic form reset
				wp_enqueue_style( 'mailchimp-for-wp-form', MC4WP_PLUGIN_URL . 'assets/css/form'. $suffix .'.css', array(), MC4WP_VERSION, 'all' );
				break;
		}

		return true;
	}

	/**
	 * @return bool
	 */
	public function load_custom_stylesheet() {
		// load the custom stylesheet
		$custom_stylesheet = (string) get_option( 'mc4wp_custom_css_file', '' );

		// load stylesheet
		if( '' === $custom_stylesheet ) {
			return false;
		}

		wp_enqueue_style( 'mc4wp-custom-form-css', $custom_stylesheet, array(), null, 'all' );
		return true;
	}

	/**
	 * @return bool
	 */
	protected function load_custom_color_stylesheet() {
		$custom_color = urlencode( $this->options['custom_theme_color'] );
		wp_enqueue_style( 'mailchimp-for-wp-form-theme-' . $this->options['css'], MC4WP_PLUGIN_URL . 'assets/css/form-theme-custom.php?custom-color=' . $custom_color, array(), MC4WP_VERSION, 'all' );
		return true;
	}

	/**
	 * @param string $suffix
	 *
	 * @return bool
	 */
	protected function load_theme_stylesheet( $suffix = '' ) {
		// load one of the default form themes
		$theme = $this->options['css'];
		if( in_array( $theme, array( 'blue', 'green', 'dark', 'light', 'red' ) ) ) {
			wp_enqueue_style( 'mailchimp-for-wp-form-theme-' . $theme, MC4WP_PLUGIN_URL . 'assets/css/form-theme-' . $theme . $suffix . '.css', array(), MC4WP_VERSION, 'all' );
			return true;
		}

		return false;
	}


	/**
	* Outputs a form with the given ID
	*
	* @param array $attributes
	* @param string $content
	* @return string
	*/
	public function output_form( $attributes = array(), $content = '' ) {
		global $is_IE;

		// increase count of outputted forms
		$this->outputted_forms_count++;

		$attributes = shortcode_atts(
			array(
				'id' => 0,
				'element_id' => 'mc4wp-form-' . $this->outputted_forms_count
			),
			$attributes,
			'mc4wp_form'
		);

		// try to get default form ID if it wasn't specified in the shortcode atts
		if( ! $attributes['id'] ) {

			// try to get default form id
			$attributes['id'] = absint( get_option( 'mc4wp_default_form_id', 0 ) );
			if( empty( $attributes['id'] ) ) {

				// return error message or empty form
				if( current_user_can( 'manage_options' ) ) {
					return '<p>'. sprintf( __( '<strong>Error:</strong> Please specify a form ID. Example: %s.', 'mailchimp-for-wp' ), '<code>[mc4wp_form id="321"]</code>' ) .'</p>';
				} else {
					return '';
				}

			}
		}

		// Get the form with the specified ID
		$form = MC4WP_Form::get( $attributes['id'] );

		// did we find a valid form with this ID?
		if( ! $form ) {

			if( current_user_can( 'manage_options' ) ) {
				return '<p>'. __( '<strong>Error:</strong> Sign-up form not found. Please check if you used the correct form ID.', 'mailchimp-for-wp' ) .'</p>';
			}

			return '';
		}

		// make sure to print date fallback later on if form contains a date field
		if( $form->contains_field_type( 'date' ) ) {
			$this->print_date_fallback = true;
		}

		// does this form have AJAX enabled?
		if( $form->settings['ajax'] ) {
			$this->load_ajax_scripts();
		}

		// was form submited?
		if( $form->is_submitted( $attributes['element_id'] ) ) {

			// enqueue scripts (in footer) if form was submited
			$animate_scroll = apply_filters( 'mc4wp_form_animate_scroll', true );

			wp_enqueue_script( 'mc4wp-form-request' );
			wp_localize_script( 'mc4wp-form-request', 'mc4wpFormRequestData', array(
					'success' => ( $form->request->success ) ? 1 : 0,
					'formElementId' => $form->request->form_element_id,
					'data' => $form->request->user_data,
					'animate_scroll' => $animate_scroll
				)
			);

		}

		// make sure scripts are enqueued later
		if( isset( $is_IE ) && $is_IE ) {
			wp_enqueue_script( 'mc4wp-placeholders' );
		}

		// Print small JS snippet later on in the footer.
		add_action( 'wp_footer', array( $this, 'print_js' ), 99 );

		// output form
		return $form->output( $attributes['element_id'], $attributes, false );
	}

	/**
	 * Prints some inline JavaScript to enhance the form functionality
	 *
	 * This is only printed on pages that actually contain a form.
	 * Uses jQuery if its loaded, otherwise falls back to vanilla JS.
	 */
	public function print_js() {

		if( $this->inline_js_printed === true ) {
			return false;
		}

		// Print vanilla JavaScript
		echo '<script type="text/javascript">';

		// include general form enhancements
		require_once MC4WP_PLUGIN_DIR . 'includes/views/parts/form-enhancements.js';

		// include date polyfill?
		if( $this->print_date_fallback ) {
			include MC4WP_PLUGIN_DIR . 'includes/views/parts/date-polyfill.js';
		 }

		echo '</script>';

		// make sure this function only runs once
		$this->inline_js_printed = true;
		return true;
	}

	/**
	 * Load the necessary AJAX scripts
	 *
	 * @return bool
	 */
	public function load_ajax_scripts() {

		if( $this->loaded_ajax_scripts ) {
			return false;
		}

		// get ajax scripts to load in the footer
		wp_enqueue_script( 'mc4wp-ajax-forms' );

		// Print vars required by AJAX script
		$scheme = ( is_ssl() ) ? 'https' : 'http';
		wp_localize_script( 'mc4wp-ajax-forms', 'mc4wp_vars', array(
				'ajaxurl' => add_query_arg( array( 'mc4wp_action' => 'subscribe' ), admin_url( 'admin-ajax.php', $scheme ) ),
				'ajaxloader' => array(
					'enabled' => apply_filters( 'mc4wp_print_ajax_loader_styles', true ),
					'imgurl' => MC4WP_PLUGIN_URL . 'assets/img/ajax-loader.gif'
				)
			)
		);

		// set flag to ensure ajax scripts are only loaded once
		$this->loaded_ajax_scripts = true;

		return true;
	}

}
