<?php

class MC4WP_Admin {

	/**
	 * @var bool True if the BWS Captcha plugin is activated.
	 */
	protected $has_captcha_plugin = false;

	/**
	 * @var string The relative path to the main plugin file from the plugins dir
	 */
	protected $plugin_file = 'mailchimp-for-wp-pro/mailchimp-for-wp-pro.php';

	/**
	* @var DVK_Plugin_License_Manager
	*/
	protected $license_manager;

	/**
	 * @var string
	 */
	protected $current_page = '';

	/**
	 * @var MC4WP_MailChimp
	 */
	protected $mailchimp;

	/**
	* Constructor
	*/
	public function __construct() {

		// store current page
		global $pagenow;
		$this->current_page = isset( $pagenow ) ? $pagenow : '';

		// set plugin slug
		$this->plugin_file = plugin_basename( MC4WP_PLUGIN_FILE );

		// load plugin translations
		$this->load_translations();

		// load license manager
		$this->license_manager = $this->load_license_manager();

		// setup mailchimp instance
		$this->mailchimp = new MC4WP_MailChimp();

		// setup hooks
		$this->setup_hooks();
	}

	/**
	* The upgrade routine
	* Only runs after updating plugin files (if version was bumped)
	*
	* @return boolean Boolean indication whether the upgrade routine ran
	*/
	public function load_upgrader() {

		$db_version = get_option( 'mc4wp_version', 0 );
		if( version_compare( MC4WP_VERSION, $db_version, '<=' ) ) {
			return false;
		}

		// create instance of the upgrader and run it
		$upgrader = new MC4WP_DB_Upgrader( MC4WP_VERSION, $db_version );
		$upgrader->run();
		return true;
	}

	/**
	* Loads the plugin license manager
	*
	* @return DVK_Plugin_License_Manager An instance of the Plugin_License_Manager class
	*/
	private function load_license_manager() {
		$product = new MC4WP_Product();
		$license_manager = new DVK_Plugin_License_Manager( $product );
		$license_manager->setup_hooks();
		return $license_manager;
	}

	/**
	* Registers all the hooks
	*/
	private function setup_hooks() {

		// Actions used globally throughout WP Admin
		add_action( 'admin_init', array( $this, 'initialize' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'load_assets' ) );
		add_action( 'admin_menu', array( $this, 'build_menu' ) );
		add_action( 'wp_dashboard_setup', array( $this, 'register_dashboard_widgets' ) );
		add_filter( 'wp_insert_post_data', array( $this, 'filter_form_content' ), 10, 2 );
		add_action( 'save_post', array( $this, 'save_form_data' ), 10, 2 );

		if( $this->license_manager->license_is_valid() ) {
			$three_o = new MC4WP_Three_O_Installer( MC4WP_PLUGIN_DIR . '..', $this->license_manager->get_license_key() );
			$three_o->add_hooks();
		}
	}

	/**
	 * Register dashboard widgets
	 */
	public function register_dashboard_widgets() {

		if( ! current_user_can( $this->get_required_user_capability() ) ) {
			return false;
		}

		wp_add_dashboard_widget(
			'mc4wp_log_widget',         // Widget slug.
			'MailChimp Sign-Ups',         // Title.
			array( 'MC4WP_Dashboard_Log_Widget', 'make' ) // Display function.
		);

		return true;
	}


	/**
	 * Initializes the plugin
	 *
	 * - Registers settings
	 * - Runs the upgrade routine
	 * - Checks if the Captcha plugin is activated
	 */
	public function initialize() {

		// is Captcha plugin running?
		$this->has_captcha_plugin = function_exists( 'cptch_display_captcha_custom' );

		// register settings
		$this->register_settings();

		// run upgrade routine
		$this->load_upgrader();

		// listen for custom actions
		$this->listen();

		// Hooks for Plugins overview
		if( $this->current_page === 'plugins.php' ) {
			add_filter( 'plugin_action_links_' . $this->plugin_file, array( $this, 'add_plugin_settings_link' ), 10, 2 );
			add_filter( 'plugin_row_meta', array( $this, 'add_plugin_meta_links'), 10, 2 );
		}

		// Hooks for "edit form" pages
		if( $this->on_edit_form_page() ) {
			add_action( 'do_meta_boxes', array( $this, 'remove_meta_boxes' ) );
			add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ), 25 );
			add_action( 'admin_head', array( $this, 'unload_conflicting_assets' ), 99 );

			add_filter( 'user_can_richedit', '__return_false' );
			add_filter( 'gettext', array( $this, 'change_publish_button' ), 10, 2 );
			add_filter( 'default_content', array( $this, 'get_default_form_markup' ), 10, 2 );
			add_filter( 'post_updated_messages', array( $this, 'set_form_updated_messages' ) );
			add_filter( 'edit_form_after_title', array( $this, 'add_form_notice' ) );
			add_filter( 'quicktags_settings', array( $this, 'set_quicktags_buttons' ), 10, 2 );
		}

		add_action( 'admin_footer_text', array( $this, 'footer_text' ) );
	}

	/**
	 *
	 */
	protected function listen() {

		// listen for any action (if user is authorised)
		if( ! current_user_can( 'manage_options' ) || ! isset( $_REQUEST['_mc4wp_action'] ) ) {
			return false;
		}

		$action = $_REQUEST['_mc4wp_action'];

		if( $action === 'export_log' ) {
			return $this->run_log_exporter();
		}
	}

	/**
	 * Load plugin textdomain
	 */
	private function load_translations() {
		// load the plugin text domain
		load_plugin_textdomain( 'mailchimp-for-wp', false, basename( MC4WP_PLUGIN_DIR ) . '/languages/' );
	}

	/**
	 * Add settings link to Plugins page
	 *
	 *
	 * @param $links
	 * @param $file
	 *
	 * @return array
	 */
	public function add_plugin_settings_link( $links, $file ) {
		$settings_link = '<a href="' . admin_url( 'admin.php?page=mailchimp-for-wp' ) . '">' . __( 'Settings', 'mailchimp-for-wp' ) . '</a>';
		array_unshift( $links, $settings_link );
		return $links;
	}

	/**
	 * Adds meta links to the plugin in the WP Admin > Plugins screen
	 *
	 * @param array $links
	 * @param string $file
	 *
	 * @return array
	 */
	public function add_plugin_meta_links( $links, $file ) {

		if( $file !== $this->plugin_file ) {
			return $links;
		}

		$links[] = '<a href="https://mc4wp.com/kb/">' . __( 'Documentation', 'mailchimp-for-wp' ) . '</a>';
		return $links;
	}

	/**
	 * Returns a boolean indicating whether we're editing a MailChimp for WP form
	 *
	 * @return bool
	 */
	private function on_edit_form_page() {

		// use cheap string comparision if post_type is in request superglobal
		if( $this->current_page === 'post-new.php' && isset( $_GET['post_type'] ) && $_GET['post_type'] === 'mc4wp-form' ) {
			return true;
		}

		// query current post if post is in request superglobal
		if( $this->current_page === 'post.php' && isset( $_GET['post'] ) && ( $p = get_post( $_GET['post'] ) ) && $p->post_type === 'mc4wp-form' ) {
			return true;
		}

		return false;
	}

	/**
	 * Fix for MultiSite, where only superadmins can save unfiltered HTML in post_content.
	 *
	 * @param array $data
	 * @param array $post_array
	 *
	 * @return array
	 */
	public function filter_form_content( $data, $post_array ) {
		// only act on our own post type
		if( $post_array['post_type'] !== 'mc4wp-form' ) {
			return $data;
		}

		// if `content` index is set, use that one.
		// this fixes an issue with `post_content` already being kses stripped at this point
		if( isset( $post_array['content'] ) ) {
			$data['post_content'] = $post_array['content'];
		}

		// remove <form> tags from form content
		$data['post_content'] = preg_replace( '/<\/?form(.|\s)*?>/i', '', $data['post_content'] );

		// make sure filtered post content is the same
		$data['post_content_filtered'] = $data['post_content'];
		return $data;
	}

	/**
	 * Change the publish button to "Save Form" or "Update Form"
	 *
	 * @param $translation
	 * @param $text
	 *
	 * @return string
	 */
	public function change_publish_button( $translation, $text ) {

		switch( $text ) {
			case 'Publish':
				$translation = __( 'Save Form', 'mailchimp-for-wp' );
				break;

			case 'Update':
				$translation = __( 'Update Form', 'mailchimp-for-wp' );
				break;
		}

		return $translation;
	}

	/**
	* Set Quicktags buttons for MCWP Forms
	* @return array
	*/
	public function set_quicktags_buttons( $settings, $editor_id ) {
		$settings['buttons'] = 'strong,em,link,img,ul,li,close';
		return $settings;
	}

	/**
	* Register plugin settings
	*/
	public function register_settings() {
		register_setting( 'mc4wp_settings', 'mc4wp', array( $this, 'validate_settings' ) );
		register_setting( 'mc4wp_checkbox_settings', 'mc4wp_checkbox', array( $this, 'validate_settings' ) );
		register_setting( 'mc4wp_form_settings', 'mc4wp_form', array( $this, 'validate_settings' ) );
		register_setting( 'mc4wp_form_styles_settings', 'mc4wp_form_styles', array( 'MC4WP_Styles_Builder', 'build' ) );
	}

	/**
	* Set the default form mark-up
	* @return string
	*/
	public function get_default_form_markup( $content = '', $post = null ) {
		if ( is_object( $post ) && $post->post_type === 'mc4wp-form' ) {
			return include dirname( __FILE__ ) . '/../config/default-form.php';
		}

		return $content;
	}

	public function add_form_notice() {
		require MC4WP_PLUGIN_DIR . '/includes/views/parts/missing-fields-notice.php';
	}

	/**
	 * Set notices after saving a form
	 *
	 * @param $messages
	 *
	 * @return array
	 */
	public function set_form_updated_messages( $messages ) {

		$back_link = __( 'Back to general form settings', 'mailchimp-for-wp' );
		$messages['mc4wp-form'] = $messages['post'];
		$messages['mc4wp-form'][1] = __( 'Form updated.', 'mailchimp-for-wp' );
		$messages['mc4wp-form'][6] = __( 'Form saved.', 'mailchimp-for-wp' );

		// add back link and additional message to all messages
		foreach( $messages['mc4wp-form'] as $key => $message ) {
			$messages['mc4wp-form'][$key] .= '<br><br><a href="'. admin_url( 'admin.php?page=mailchimp-for-wp-form-settings' ) .'">&laquo; '. $back_link . '</a>';
		}

		return $messages;
	}

	/**
	 * @var int $post_ID
	 * @return bool
	 */
	public function save_form_data( $post_id, $post ) {

		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return false;
		}

		if( $post->post_type !== 'mc4wp-form' ) {
			return false;
		}

		if ( ! isset( $_POST['_mc4wp_nonce'] ) || ! wp_verify_nonce( $_POST['_mc4wp_nonce'], 'mc4wp_save_form' ) ) {
			return false;
		}

		if( ! isset( $_POST['mc4wp_form'] ) || ! is_array( $_POST['mc4wp_form'] ) ) {
			return false;
		}

		// fill array with user data
		$data = $this->validate_settings( $_POST['mc4wp_form'] );

		$meta = array(
			'lists' => $data['lists']
		);

		$optional_meta_keys = array( 'send_email_copy', 'email_copy_receiver', 'double_optin', 'update_existing', 'replace_interests', 'send_welcome', 'ajax', 'hide_after_success', 'redirect', 'text_subscribed', 'text_error', 'text_invalid_email', 'text_already_subscribed', 'text_invalid_captcha', 'text_required_field_missing', 'text_unsubscribed', 'text_not_subscribed' );
		foreach ( $optional_meta_keys as $meta_key ) {
			if ( isset( $data[ $meta_key ] ) ) {
				$meta[$meta_key] = $data[ $meta_key ];
			}
		}

		return update_post_meta( $post_id, '_mc4wp_settings', $meta );
	}

	/**
	* Adds meta boxes to the MCWP Forms screen
	*/
	public function add_meta_boxes() {
		add_meta_box( 'mc4wp-form-settings', __( 'Form Settings', 'mailchimp-for-wp' ), array( $this, 'show_required_form_settings_metabox' ), 'mc4wp-form', 'side', 'high' );
		add_meta_box( 'mc4wp-optional-settings', __( 'Optional Settings', 'mailchimp-for-wp' ), array( $this, 'show_optional_form_settings_metabox' ), 'mc4wp-form', 'normal', 'high' );
		add_meta_box( 'mc4wp-form-variables', __( 'Variables', 'mailchimp-for-wp' ), array( $this, 'show_form_variables_metabox' ), 'mc4wp-form', 'side' );
	}

	/**
	 * Remove all metaboxes except "submitdiv" and the mc4wp- meta boxes.
	 *
	 * Also removes all metaboxes added by other plugins..
	 */
	public function remove_meta_boxes() {
		global $wp_meta_boxes;
		if ( isset( $wp_meta_boxes['mc4wp-form'] ) && is_array( $wp_meta_boxes['mc4wp-form'] ) ) {
			$meta_boxes = $wp_meta_boxes['mc4wp-form'];
			$allowed_meta_boxes = array( 'submitdiv' );

			foreach ( $meta_boxes as $context => $context_boxes ) {

				if ( ! is_array( $context_boxes ) ) {
					continue;
				}

				foreach ( $context_boxes as $priority => $priority_boxes ) {
					if ( ! is_array( $priority_boxes ) ) {
						continue;
					}

					foreach ( $priority_boxes as $meta_box_id => $meta_box_args ) {
						if ( stristr( $meta_box_id, 'mc4wp' ) === false && ! in_array( $meta_box_id, $allowed_meta_boxes ) ) {
							unset( $wp_meta_boxes['mc4wp-form'][$context][$priority][$meta_box_id] );
						}
					}
				}
			}
		}
	}

	/**
	 * Outputs the form variables metabox
	 * @param WP_Post $post
	 */
	public function show_form_variables_metabox( $post ) {
		include MC4WP_PLUGIN_DIR . 'includes/views/parts/admin-text-variables.php';
	}

	/**
	 * Outputs the required form settings metabox
	 * @param WP_Post $post
	 */
	public function show_required_form_settings_metabox( $post ) {
		$lists = $this->mailchimp->get_lists();
		$form = MC4WP_Form::get( $post );
		$individual_form_settings = $form->load_settings(false);
		include MC4WP_PLUGIN_DIR . 'includes/views/metaboxes/required-form-settings.php';
	}

	/**
	 * Outputs the optional form settings metabox
	 * @param WP_Post $post
	 */
	public function show_optional_form_settings_metabox( $post ) {
		$form = MC4WP_Form::get($post);
		$individual_form_settings = $form->load_settings(false);
		$inherited_settings = mc4wp_get_options( 'form' );
		include MC4WP_PLUGIN_DIR . 'includes/views/metaboxes/optional-form-settings.php';
	}

	/**
	* Sanitize the plugin settings
	*
	* @var array $settings Raw input array of settings
	* @return array $settings Sanitized array of settings
	*/
	public function validate_settings( array $settings ) {

		$current = mc4wp_get_options();

		// Toggle usage tracking
		if( isset( $settings['allow_usage_tracking'] ) ) {
			MC4WP_Usage_Tracking::instance()->toggle( (bool) $settings['allow_usage_tracking'] );
		}

		// sanitize simple text fields (no HTML, just chars & numbers)
		$simple_text_fields = array( 'api_key', 'redirect', 'css' );
		foreach( $simple_text_fields as $field ) {
			if( isset( $settings[ $field ] ) ) {
				$settings[ $field ] = sanitize_text_field( $settings[ $field ] );
			}
		}

		// empty MailChimp lists cache when API key changed
		if( isset( $settings['api_key'] ) && $settings['api_key'] !== $current['general']['api_key'] ) {
			$this->mailchimp->empty_cache();
		}

		// validate woocommerce checkbox position
		if( isset( $settings['woocommerce_position'] ) ) {
			// make sure position is either 'order' or 'billing'
			if( ! in_array( $settings['woocommerce_position'], array( 'order', 'billing' ) ) ) {
				$settings['woocommerce_position'] = 'billing';
			}
		}

		// dynamic sanitization
		foreach( $settings as $setting => $value ) {
			// strip special tags from text settings
			if( substr( $setting, 0, 5 ) === 'text_' || $setting === 'label' ) {
				$value = trim( $value );
				$value = strip_tags( $value, '<a><b><strong><em><i><br><u><pre><script><span><abbr><strike>' );
				$settings[ $setting ] = $value;
			}
		}


		return $settings;
	}

	/**
	 * Gets the required user capability to access settings page / view dashboard widget
	 *
	 * @return string
	 */
	protected function get_required_user_capability() {

		/**
		 * @filter mc4wp_settings_cap
		 * @expects     string      A valid WP capability like 'manage_options' (default)
		 *
		 * Use to customize the required user capability to access the MC4WP settings pages
		 */
		$required_cap = (string) apply_filters( 'mc4wp_settings_cap', 'manage_options' );

		return $required_cap;
	}

	/**
	* Build the MCWP Admin Menu
	*/
	public function build_menu() {

		$required_cap = $this->get_required_user_capability();

		// add top menu
		add_menu_page( 'MailChimp for WP Pro', 'MailChimp for WP', $required_cap, 'mailchimp-for-wp', array( $this, 'show_general_settings' ), MC4WP_PLUGIN_URL . 'assets/img/icon.png', '99.68491' );

		// get submenu items to add
		$menu_items = array(
			array(
				'title' => __( 'MailChimp & Plugin License Settings', 'mailchimp-for-wp' ),
				'text' => __( 'MailChimp & License', 'mailchimp-for-wp' ),
				'slug' => '',
				'callback' => array( $this, 'show_general_settings' )
			),
			array(
				'title' => __( 'Checkbox Settings', 'mailchimp-for-wp' ),
				'text' => __( 'Checkboxes', 'mailchimp-for-wp' ),
				'slug' => 'checkbox-settings',
				'callback' => array( $this, 'show_checkbox_settings' )
			),
			array(
				'title' => __( 'Form Settings', 'mailchimp-for-wp' ),
				'text' => __( 'Forms', 'mailchimp-for-wp' ),
				'slug' => 'form-settings',
				'callback' => array( $this, 'show_form_settings' ) ),
			array(
				'title' => __( 'Reports', 'mailchimp-for-wp' ),
				'text' => __( 'Reports', 'mailchimp-for-wp' ),
				'slug' => 'reports',
				'callback' => array( $this, 'show_reports' )
			)
		);

		/**
		 * Allow other plugins to add to this top menu
		 */
		$menu_items = apply_filters( 'mc4wp_menu_items', $menu_items );

		foreach( $menu_items as $item ) {
			$slug = ( '' !== $item['slug'] ) ? "mailchimp-for-wp-{$item['slug']}" : 'mailchimp-for-wp';
			add_submenu_page( 'mailchimp-for-wp', $item['title'] . ' - MailChimp for WordPress Lite', $item['text'], $required_cap, $slug, $item['callback'] );
		}
	}

	/**
	 * Load scripts and stylesheet on MailChimp for WP Admin pages
	 */
	public function load_assets() {

		// should we load the minified version?
		$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

		if ( isset( $_GET['page'] ) && strpos( $_GET['page'], 'mailchimp-for-wp' ) === 0 ) {

			/*
                Any MailChimp for WP Settings Page
			*/

			// Styles
			wp_enqueue_style( 'wp-color-picker' );
			wp_enqueue_style( 'mc4wp-admin-styles', MC4WP_PLUGIN_URL . 'assets/css/admin-styles'. $suffix .'.css' );

			// Scripts
			wp_register_script( 'mc4wp-admin-settings',  MC4WP_PLUGIN_URL . 'assets/js/admin-settings'. $suffix .'.js', array( 'jquery', 'wp-color-picker' ), MC4WP_VERSION, true );
			wp_enqueue_script( array( 'jquery', 'mc4wp-admin-settings' ) );

			/* Reports page */
			if ( $_GET['page'] === 'mailchimp-for-wp-reports' && ( ! isset( $_GET['tab'] ) || $_GET['tab'] === 'statistics' ) ) {

				// load flot
				wp_register_script( 'mc4wp-flot', MC4WP_PLUGIN_URL . 'assets/js/jquery.flot.min.js', array( 'jquery' ), MC4WP_VERSION, true );
				wp_register_script( 'mc4wp-flot-time', MC4WP_PLUGIN_URL . 'assets/js/jquery.flot.time.min.js', array( 'jquery' ), MC4WP_VERSION, true );
				wp_register_script( 'mc4wp-statistics', MC4WP_PLUGIN_URL . 'assets/js/admin-statistics'. $suffix .'.js', array( 'mc4wp-flot-time' ), MC4WP_VERSION, true );

				wp_enqueue_script( array( 'jquery', 'mc4wp-flot', 'mc4wp-statistics' ) );

				// print ie excanvas script in footer
				add_action( 'admin_print_footer_scripts', array( $this, 'print_excanvas_script' ), 1 );
			}

			/* CSS Edit Page */
			if ( $_GET['page'] === 'mailchimp-for-wp-form-settings' && isset( $_GET['tab'] ) && $_GET['tab'] === 'css-builder' ) {

				// color picker
				wp_enqueue_style( 'wp-color-picker' );
				wp_enqueue_script( 'wp-color-picker' );

				// thickbox (for image upload)
				wp_enqueue_script( 'thickbox' );
				wp_enqueue_style('thickbox');

				// our own scripts
				wp_enqueue_script( 'mc4wp-form-css', MC4WP_PLUGIN_URL . 'assets/js/styles-builder'. $suffix .'.js', array( 'jquery' ), MC4WP_VERSION, true );
			}

		}

		if ( $this->on_edit_form_page() ) {

			/*
                Edit `mc4wp-form` pages
			*/

			wp_dequeue_script( 'ztranslate-script' );

			// Styles
			wp_enqueue_style( 'mc4wp-admin-styles', MC4WP_PLUGIN_URL . 'assets/css/admin-styles'. $suffix .'.css', array(), MC4WP_VERSION );

			// Scripts
			wp_register_script( 'mc4wp-beautifyhtml', MC4WP_PLUGIN_URL . 'assets/js/beautify-html.min.js', array( 'jquery' ), MC4WP_VERSION, true );
			wp_register_script( 'mc4wp-admin-formhelper',  MC4WP_PLUGIN_URL . 'assets/js/admin-formhelper'. $suffix .'.js', array( 'jquery', 'quicktags' ), MC4WP_VERSION, true );
			wp_enqueue_script( array( 'jquery', 'mc4wp-beautifyhtml', 'mc4wp-admin-formhelper' ) );
			wp_localize_script( 'mc4wp-admin-formhelper', 'mc4wp',
				array(
					'has_captcha_plugin' => $this->has_captcha_plugin,
					'mailchimpLists' => $this->mailchimp->get_lists(),
					'strings' => array(
						'fieldWizard' => array(
							'buttonText' => __( 'Button text', 'mailchimp-for-wp' ),
							'initialValue' => __( 'Initial value', 'mailchimp-for-wp' ),
							'optional' => __( '(optional)', 'mailchimp-for-wp' ),
							'labelFor' => __( 'Label for', 'mailchimp-for-wp' ),
							'orLeaveEmpty' => __( '(or leave empty)', 'mailchimp-for-wp' ),
							'subscribe' => __( 'Subscribe', 'mailchimp-for-wp' ),
							'unsubscribe' => __( 'Unsubscribe', 'mailchimp-for-wp' ),
						)
					)
				)
			);

			// we don't need the following scripts
			wp_dequeue_script( 'autosave', 'suggest' );

		}

	}

	/**
	 * Do not load conflicting scripts on edit form pages
	 *
	 * - zTranslate
	 * - ...
	 */
	public function unload_conflicting_assets() {
		wp_dequeue_script( 'ztranslate-script' );
	}

	/**
	* Get Checkbox integrations
	*
	* @return array
	*/
	public function get_checkbox_compatible_plugins() {

		static $checkbox_plugins;

		if( is_array( $checkbox_plugins ) ) {
			return $checkbox_plugins;
		}

		// build array of checkbox compatible plugins
		$checkbox_plugins = array(
			'comment_form' => __( 'Comment form', 'mailchimp-for-wp' ),
			'registration_form' => __( 'Registration form', 'mailchimp-for-wp' )
		);

		if( is_multisite() ) {
			$checkbox_plugins['multisite_form'] = __( 'MultiSite forms', 'mailchimp-for-wp' );
		}

		if( class_exists( 'BuddyPress' ) ) {
			$checkbox_plugins['buddypress_form'] = __( 'BuddyPress registration', 'mailchimp-for-wp' );
		}

		if( class_exists( 'bbPress' ) ) {
			$checkbox_plugins['bbpress_forms'] = 'bbPress';
		}

		if ( class_exists( 'Easy_Digital_Downloads' ) ) {
			$checkbox_plugins['edd_checkout'] = sprintf( __( '%s checkout', 'mailchimp-for-wp' ), 'Easy Digital Downloads' );
		}

		if ( class_exists( 'WooCommerce' ) ) {
			$checkbox_plugins['woocommerce_checkout'] = sprintf( __( '%s checkout', 'mailchimp-for-wp' ), 'WooCommerce' );
		}

		return $checkbox_plugins;
	}

	/**
	* Get selected Checkbox integrations
	* @return array
	*/
	public function get_selected_checkbox_hooks() {
		$checkbox_plugins = $this->get_checkbox_compatible_plugins();
		$selected_checkbox_hooks = array();
		$checkbox_opts = mc4wp_get_options( 'checkbox' );

		// check which checkbox hooks are selected
		foreach ( $checkbox_plugins as $code => $name ) {

			if ( isset( $checkbox_opts['show_at_'.$code] ) && $checkbox_opts['show_at_'.$code] ) {
				$selected_checkbox_hooks[$code] = $name;
			}
		}

		return $selected_checkbox_hooks;
	}

	/**
	* Show general settings page
	*/
	public function show_general_settings() {
		$opts = mc4wp_get_options( 'general' );
		$connected = mc4wp_get_api()->is_connected();

		// cache renewal triggered manually?
		$force_cache_refresh = isset( $_POST['mc4wp-renew-cache'] ) && $_POST['mc4wp-renew-cache'] == 1;
		$lists = $this->mailchimp->get_lists( $force_cache_refresh );

		if ( $force_cache_refresh ) {

			if( is_array( $lists ) ) {
				if( count( $lists ) === 100 ) {
					add_settings_error( 'mc4wp', 'mc4wp-lists-at-limit', __( 'The plugin can only fetch a maximum of 100 lists from MailChimp, only your first 100 lists are shown.', 'mailchimp-for-wp' ) );
				} else {
					add_settings_error( 'mc4wp', 'mc4wp-cache-success', __( 'Renewed MailChimp cache.', 'mailchimp-for-wp' ), 'updated' );
				}
			} else {
				add_settings_error( 'mc4wp', 'mc4wp-cache-error', __( 'Failed to renew MailChimp cache - please try again later.', 'mailchimp-for-wp' ) );
			}

		}

		require MC4WP_PLUGIN_DIR . 'includes/views/pages/admin-general-settings.php';
	}

	/**
	* Show checkbox settings page
	*/
	public function show_checkbox_settings() {
		$opts = mc4wp_get_options( 'checkbox' );
		$lists = $this->mailchimp->get_lists();

		$checkbox_plugins = $this->get_checkbox_compatible_plugins();
		$selected_checkbox_hooks = $this->get_selected_checkbox_hooks();

		require MC4WP_PLUGIN_DIR . 'includes/views/pages/admin-checkbox-settings.php';
	}

	/**
	* Show form settings page
	*/
	public function show_form_settings() {
		$tab = ( isset( $_GET['tab'] ) ) ? $_GET['tab'] : 'general-settings';
		$opts = mc4wp_get_options( 'form' );

		if ( $tab === 'general-settings' ) {
			$table = new MC4WP_Forms_Table( $this->mailchimp );
		} else {

			// get all forms
			$forms = get_posts(
				array(
					'post_type' => 'mc4wp-form',
					'post_status' => 'publish',
					'posts_per_page' => -1
				)
			);

			// get form to which styles should apply
			if( isset( $_GET['form_id'] ) ) {
				$form_id = absint( $_GET['form_id'] );
			} elseif( isset( $forms[0] ) ) {
				$form_id = $forms[0]->ID;
			} else {
				$form_id = 0;
			}

			// get css settings for this form (or 0)
			$builder = new MC4WP_Styles_Builder();
			$styles = $builder->get_form_styles( $form_id );

			// create preview url
			$preview_url = add_query_arg( array( 'form_id' => $form_id, '_mc4wp_css_preview' => 1 ), home_url() );
		}

		require MC4WP_PLUGIN_DIR . 'includes/views/pages/admin-form-settings.php';
	}

	/**
	 * Show log page
	 */
	public function show_export_page() {
		$tab = 'export';
		include_once MC4WP_PLUGIN_DIR . 'includes/views/pages/admin-reports.php';
	}

	/**
	* Show log page
	*/
	public function show_log_page() {
		$table = new MC4WP_Log_Table( $this->mailchimp );
		$tab = 'log';
		include_once MC4WP_PLUGIN_DIR . 'includes/views/pages/admin-reports.php';
	}

	/**
	* Show reports (stats) page
	*/
	public function show_stats_page() {
		$statistics = new MC4WP_Statistics( $_GET );
		$statistics_settings = array( 'ticksize' => array( 1, $statistics->step_size ) );
		$statistics_data = $statistics->get_statistics();

		// add scripts
		wp_localize_script( 'mc4wp-statistics', 'mc4wp_statistics_data', $statistics_data );
		wp_localize_script( 'mc4wp-statistics', 'mc4wp_statistics_settings', $statistics_settings );

		$start_day = ( isset( $_GET['start_day'] ) ) ? $_GET['start_day'] : 0;
		$start_month = ( isset( $_GET['start_month'] ) ) ? $_GET['start_month'] : 0;
		$start_year = ( isset( $_GET['start_year'] ) ) ? $_GET['start_year'] : 0;
		$end_day = ( isset( $_GET['end_day'] ) ) ? $_GET['end_day'] : 0;
		$end_month = ( isset( $_GET['end_month'] ) ) ? $_GET['end_month'] : 0;
		$end_year = ( isset( $_GET['end_year'] ) ) ? $_GET['end_year'] : 0;
		$tab = 'statistics';
		$range = $statistics->range;

		include_once MC4WP_PLUGIN_DIR . 'includes/views/pages/admin-reports.php';
	}

	/**
	* Show reports page
	*/
	public function show_reports() {
		$tab = ( isset( $_GET['tab'] ) ) ? $_GET['tab'] : 'stats';

		$disable_logging = apply_filters( 'mc4wp_disable_logging', false );

		if( $disable_logging ) {
			echo '<p>' . sprintf( __( 'You disabled logging using the %s filter. Re-enable it to use the Reports page.', 'mailchimp-for-wp' ), '<code>mc4wp_disable_logging</code>' ) . '</p>';
		} elseif ( $tab === 'log' ) {
			return $this->show_log_page();
		} elseif( $tab === 'export' ) {
			return $this->show_export_page();
		} else {
			return $this->show_stats_page();
		}
	}



	/**
	 * Print the IE canvas fallback script in the footer on statistics pages
	 */
	public function print_excanvas_script() {
		?><!--[if lte IE 8]><script language="javascript" type="text/javascript" src="<?php echo MC4WP_PLUGIN_URL . 'assets/js/excanvas.min.js'; ?>"></script><![endif]--><?php
	}

	/**
	 * Run the log exporter
	 */
	protected function run_log_exporter() {
		$args = array();

		if( isset( $_REQUEST['start_year'] ) ) {
			$start_year = absint( $_REQUEST['start_year'] );
			$start_month = ( isset( $_REQUEST['start_month'] ) ) ? absint( $_REQUEST['start_month'] ) : 1;
			$timestring = sprintf( 'first day of %s-%s', $start_year, $start_month );
			$args['datetime_after'] = date( 'Y-m-d 00:00:00', strtotime( $timestring ) );
		}

		if( isset( $_REQUEST['end_year'] ) ) {
			$end_year = absint( $_REQUEST['end_year'] );
			$end_month = ( isset( $_REQUEST['end_month'] ) ) ? absint( $_REQUEST['end_month'] ) : 12;
			$timestring = sprintf( 'last day of %s-%s', $end_year, $end_month );
			$args['datetime_before'] = date( 'Y-m-d 23:59:59', strtotime( $timestring ) );
		}

		if( isset( $_REQUEST['include_errors'] ) ) {
			$args['include_errors'] = 1;
		}

		$exporter = new MC4WP_Log_Exporter();
		$exporter->filter( $args );
		$exporter->build();
		$exporter->output();
	}

	/**
	 * Get array of months
	 */
	protected function get_months() {
		$months = array(
			1 => __( 'Jan', 'mailchimp-for-wp' ),
			2 => __( 'Feb', 'mailchimp-for-wp' ),
			3 => __( 'Mar', 'mailchimp-for-wp' ),
			4 => __( 'Apr', 'mailchimp-for-wp' ),
			5 => __( 'May', 'mailchimp-for-wp' ),
			6 => __( 'Jun', 'mailchimp-for-wp' ),
			7 => __( 'Jul', 'mailchimp-for-wp' ),
			8 => __( 'Aug', 'mailchimp-for-wp' ),
			9 => __( 'Sept', 'mailchimp-for-wp' ),
			10 => __( 'Okt', 'mailchimp-for-wp' ),
			11 => __( 'Nov', 'mailchimp-for-wp' ),
			12 => __( 'Dec', 'mailchimp-for-wp' )
		);

		return $months;
	}

	/**
	 * Ask for a plugin review in the WP Admin footer, if this is one of the plugin pages.
	 *
	 * @param $text
	 *
	 * @return string
	 */
	public function footer_text( $text ) {

		if( ( isset( $_GET['page'] ) && strpos( $_GET['page'], 'mailchimp-for-wp' ) === 0 ) || $this->on_edit_form_page() ) {
			$text = sprintf( 'If you enjoy using <strong>MailChimp for WordPress</strong>, please <a href="%s" target="_blank">leave us a ★★★★★ rating</a>. A <strong style="text-decoration: underline;">huge</strong> thank you in advance!', 'https://wordpress.org/support/view/plugin-reviews/mailchimp-for-wp?rate=5#postform' );
		}

		return $text;
	}

}
