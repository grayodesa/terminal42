<?php

/**
 * Class MC4WP_Multiple_Forms_Admin
 *
 * @ignore
 */
class MC4WP_Multiple_Forms_Admin {

	/**
	 * @var MC4WP_Plugin
	 */
	protected $plugin;

	/**
	 * Constructor
	 *
	 * @param MC4WP_Plugin $plugin
	 */
	public function __construct( MC4WP_Plugin $plugin ) {
		$this->plugin = $plugin;
	}

	/**
	 * Add general hooks
	 */
	public function add_hooks() {
		add_filter( 'mc4wp_admin_menu_items', array( $this, 'remove_form_action_redirect' ) );
		add_action( 'mc4wp_admin_show_forms_page', array( $this, 'show_forms_overview_page' ) );
		add_action( 'mc4wp_admin_edit_form_after_title', array( $this, 'show_new_form_button' ) );
	}

	public function remove_form_action_redirect( $items ) {
		unset( $items['forms']['load_callback'] );
		return $items;
	}

	public function show_forms_overview_page() {

		if( ! empty( $_GET['view'] ) ) {
			return;
		}

		$table = new MC4WP_Forms_Table( new MC4WP_MailChimp() );
		include $this->plugin->dir( '/views/forms-overview.php' );
	}

	public function show_new_form_button() {
		?>
		<a href="<?php echo mc4wp_get_add_form_url(); ?>" class="page-title-action">
			<span class="dashicons dashicons-plus-alt" style=""></span>
			<?php _e( 'Add new form', 'mailchimp-for-wp' ); ?>
		</a>
	<?php
	}


}