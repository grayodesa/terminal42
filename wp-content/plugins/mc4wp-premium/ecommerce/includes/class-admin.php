<?php

/**
 * Class MC4WP_Ecommerce_Admin
 *
 * @ignore
 */
class MC4WP_Ecommerce_Admin {

	/**
	 * @var MC4WP_Plugin
	 */
	protected $plugin;

	/**
	 * @var boolean
	 */
	protected $enabled;

	/**
	 * MC4WP_Ecommerce_Admin constructor.
	 *
	 * @param MC4WP_Plugin $plugin
	 * @param boolean $enabled
	 */
	public function __construct( MC4WP_Plugin $plugin, $enabled = false ) {
		$this->plugin = $plugin;
		$this->enabled = $enabled;
	}

	/**
	 * Add hooks
	 */
	public function add_hooks() {
		# Pages
		add_action( 'mc4wp_admin_other_settings', array( $this, 'show_settings_page' ) );

		# Add notice when eCommerce is enabled
		add_action( 'mc4wp_save_settings', array( $this, 'maybe_show_notice' ), 10, 2 );

		# AJAX hooks
		add_action( 'wp_ajax_mc4wp_ecommerce_add_untracked_orders', array( $this, 'add_untracked_orders' ) );
		add_action( 'wp_ajax_mc4wp_ecommerce_get_untracked_orders_count', array( $this, 'get_untracked_orders_count' ) );

		# Hook into regular form submit (non-AJAX)
		add_action( 'mc4wp_admin_ecommerce_add_untracked_orders', array( $this, 'add_untracked_orders' ) );

		add_action( 'admin_menu', array( $this, 'register_hidden_pages' ) );

		if( $this->enabled ) {
			// add new WooCommerce order action to manually add / delete order from MailChimp
			add_filter( 'woocommerce_order_actions', array( $this, 'add_woocommerce_order_action' ) );
			add_action( 'woocommerce_order_action_mailchimp_ecommerce', array( $this, 'run_woocommerce_order_action' ) );
		}
	}

	/**
	 * @param array $actions
	 * @return array
	 */
	public function add_woocommerce_order_action( $actions ) {
		global $theorder;
		$tracked = !! get_post_meta( $theorder->id, '_mc4wp_ecommerce_tracked', true );
		$actions['mailchimp_ecommerce'] = $tracked ? __( 'Delete from MailChimp', 'mailchimp-for-wp' ) : __( 'Add to MailChimp', 'mailchimp-for-wp' );
		return $actions;
	}

	/**
	 * @param WC_Order $order
	 */
	public function run_woocommerce_order_action( $order ) {
		$tracked = !! get_post_meta( $order->id, '_mc4wp_ecommerce_tracked', true );
		$ecommerce = $this->get_ecommerce();

		if( $tracked ) {
			$ecommerce->delete_order( $order->id );
		} else {
			$ecommerce->add_woocommerce_order( $order->id );
		}
	}

	/**
	 * Registers menu page without a parent item.
	 */
	public function register_hidden_pages() {
		add_submenu_page( null, 'Record orders', '', 'manage_options', 'mailchimp-for-wp-ecommerce', array( $this, 'show_track_orders_page' ) );
	}

	/**
	 * Register menu pages
	 *
	 * @param array $items
	 *
	 * @return array
	 */
	public function add_menu_item( array $items ) {

		$item = array(
			'title' => __( 'MailChimp E-Commerce', 'mc4wp-ecommerce' ),
			'text' => __( 'E-Commerce', 'mc4wp-ecommerce' ),
			'slug' => 'ecommerce',
			'callback' => array( $this, 'show_settings_page' )
		);


		$items[] = $item;

		return $items;
	}

	/**
	 * @param array $opts
	 */
	public function show_settings_page( $opts ) {
		require __DIR__ . '/views/settings.php';
	}

	/**
	 * Shows the wizard
	 */
	public function show_track_orders_page() {
		$helper = new MC4WP_Ecommerce_Helper();
		$untracked_order_count = $helper->get_untracked_order_count();

		$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
		wp_enqueue_script( 'mc4wp-ecommerce-admin', $this->plugin->url( '/assets/js/admin' . $suffix . '.js' ), array(), $this->plugin->version(), true );
		wp_localize_script( 'mc4wp-ecommerce-admin', 'mc4wp_ecommerce', array(
			'untracked_order_count' => $untracked_order_count
		));

		require __DIR__ . '/views/track-previous-orders.php';
	}

	/**
	 * Starts adding untracked orders to MailChimp. This can take a while..
	 */
	public function add_untracked_orders() {

		// don't lock session (because we poll for progress)
		@session_write_close();

		// no time limit
		@set_time_limit(0);

		$helper = new MC4WP_Ecommerce_Helper();
		$ecommerce = $this->get_ecommerce();

		$offset = isset( $_REQUEST['offset'] ) ? (int) $_REQUEST['offset'] : 0;
		$limit = isset( $_REQUEST['limit'] ) ? (int) $_REQUEST['limit'] : 100;

		// loop through order id's
		$order_ids = $helper->get_untracked_order_ids( $offset, $limit );
		foreach( $order_ids as $order_id ) {
			$success = $ecommerce->add_order( $order_id );
		}

		// respond to request
		if( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
			$this->get_untracked_orders_count();
		}
	}

	/**
	 * Gets the number of untracked orders and outputs it
	 */
	public function get_untracked_orders_count() {
		@session_write_close();
		$helper = new MC4WP_Ecommerce_Helper();
		$count = $helper->get_untracked_order_count();
		echo (string) $count;
		exit;
	}

	/**
	 * This asks the user to record previous orders after eCommerce360 tracking was enabled
	 *
	 * @param array $settings
	 * @param array $old_settings
	 */
	public function maybe_show_notice( $settings, $old_settings ) {
		if( $settings['ecommerce'] && ! $old_settings['ecommerce'] ) {
			$text = __( 'You just enabled eCommerce360 - do you want to <a href="%s">add all past orders to MailChimp</a>?', 'mc4wp-ecommerce' );
			$this->get_admin_messages()->flash( sprintf( $text, admin_url( 'admin.php?page=mailchimp-for-wp-ecommerce' ) ) );
		}
	}

	/**
	 * @return MC4WP_Ecommerce
	 */
	private function get_ecommerce() {
		return mc4wp('ecommerce');
	}

	/**
	 * @return MC4WP_Admin_Messages
	 */
	private function get_admin_messages() {
		return mc4wp('admin.messages');
	}
}