<?php

/**
 * Class MC4WP_Styles_Builder_Admin
 *
 * @ignore
 * @access private
 */
class MC4WP_Styles_Builder_Admin {

	/**
	 * @var MC4WP_Plugin
	 */
	protected $plugin;

	/**
	 * @param MC4WP_Plugin $plugin
	 */
	public function __construct( MC4WP_Plugin $plugin ) {
		$this->plugin = $plugin;
	}

	/**
	 * Add necessary hooks
	 */
	public function add_hooks() {
		add_action( 'admin_init', array( $this, 'register_setting' ) );
		add_action( 'admin_init', array( $this, 'run_upgrade_routines' ) );
		add_action( 'mc4wp_admin_enqueue_assets', array( $this, 'enqueue_assets' ) );
		add_action( 'mc4wp_admin_form_after_appearance_settings_rows', array( $this, 'add_settings_row' ), 10, 2 );
		add_filter( 'mc4wp_admin_form_css_options', array( $this, 'add_css_option' ) );
		add_action( 'mc4wp_admin_show_forms_page-styles-builder', array( $this, 'show_page' ) );

		// re-create stylesheet every time a form is saved
		add_action( 'mc4wp_save_form', array( 'MC4WP_Styles_Builder', 'bundle_stylesheets' ) );
	}

	/**
	 * Run upgrade routines, if necessary.
	 */
	public function run_upgrade_routines() {
		$from_version = get_option( 'mc4wp_styles_builder_version', 0 );
		$to_version = $this->plugin->version();

		// we're at the specified version already
		if( version_compare( $from_version, $to_version, '>=' ) ) {
			return;
		}

		$upgrade_routines = new MC4WP_Upgrade_Routines( $from_version, $to_version, $this->plugin->dir( '/includes/migrations' ) );
		$upgrade_routines->run();
		update_option( 'mc4wp_styles_builder_version', $to_version );
	}

	/**
	 * Register setting
	 */
	public function register_setting() {
		register_setting( 'mc4wp_form_styles_settings', 'mc4wp_form_styles', array( 'MC4WP_Styles_Builder', 'build' ) );
	}

	/**
	 * @param $suffix
	 */
	public function enqueue_assets( $suffix ) {
		if( ! isset( $_GET['view'] ) || $_GET['view'] !== 'styles-builder' ) {
			return;
		}

		// color picker
		wp_enqueue_style( 'wp-color-picker' );
		wp_enqueue_script( 'wp-color-picker' );

		// thickbox (for image upload)
		wp_enqueue_script( 'thickbox' );
		wp_enqueue_style('thickbox');

		// our own scripts
		wp_enqueue_style( 'mc4wp-styles-builder', $this->plugin->url( '/assets/css/admin' . $suffix . '.css' ), array(), $this->plugin->version() );
		wp_enqueue_script( 'mc4wp-styles-builder', $this->plugin->url( '/assets/js/styles-builder' . $suffix . '.js' ), array( 'jquery' ), $this->plugin->version(), true );
	}

	/**
	 * Show Styles Builder page
	 */
	function show_page() {
		$forms = mc4wp_get_forms();
		$form_id = $forms[0]->ID;

		// get form to which styles should apply
		if( isset( $_GET['form_id'] ) ) {
			$form_id = absint( $_GET['form_id'] );
		}

		$form = mc4wp_get_form( $form_id );

		// get css settings for this form (or 0)
		$builder = new MC4WP_Styles_Builder();
		$styles = $builder->get_form_styles( $form_id );

		// create preview url
		$preview_url = add_query_arg( array( 'form_id' => $form_id, '_mc4wp_styles_builder_preview' => 1 ), home_url() );

		require dirname( __FILE__ ) . '/../views/styles-builder.php';
	}

	/**
	 * @param $opts
	 *
	 * @return mixed
	 */
	public function add_css_option( $opts ) {
		$opts['styles-builder'] = __( 'Use Styles Builder', 'mailchimp-for-wp' );
		return $opts;
	}

	/**
	 * @param array $opts
	 * @param MC4WP_Form $form
	 */
	public function add_settings_row ( $opts, MC4WP_Form $form ) {
		?>
		<tr valign="top">
			<td></td>
			<td>
				<p>
					<?php _e( 'Create custom appearance rules for this form using the Styles Builder.', 'mailchimp-for-wp' ); ?>
				</p>

				<p>
					<a class="button" href="<?php echo add_query_arg( array( 'view' => 'styles-builder', 'form_id' => $form->ID ) ); ?>">
						<?php _e( 'Open Styles Builder', 'mailchimp-for-wp' ); ?>
					</a>
				</p>
			</td>
		</tr>
	<?php
	}
}