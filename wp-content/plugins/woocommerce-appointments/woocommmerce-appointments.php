<?php
/*
Plugin Name: WooCommerce Appointments
Plugin URI: http://www.bizzthemes.com/plugins/woocommerce-appointments/
Description: Setup appointable products for WooCommerce
Version: 1.7.7
Author: BizzThemes
Author URI: https://bizzthemes.com
Text Domain: woocommerce-appointments
Domain Path: /languages

Copyright: © BizzThemes.com
License: GNU General Public License v3.0
License URI: http://www.gnu.org/licenses/gpl-3.0.html
*/

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * LICENSE
 *
 * @since 1.0.0
 */
add_action( 'plugins_loaded', 'EDD_WCA_Plugin_License' );
function EDD_WCA_Plugin_License() {
    
	//* load our custom updater if it doesn't already exist
	if( ! class_exists( 'EDD_SL_Plugin_License' ) ) {
		require_once( 'dependencies/EDD_SL_Plugin_License.php' );
	}
	
	//* Handle licensing
	if( class_exists( 'EDD_SL_Plugin_License' ) ) {
		if ( ! function_exists( 'get_plugins' ) ) {
			require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
		}
		$plugin_data = get_plugin_data( __FILE__ );
		$license = new EDD_SL_Plugin_License( __FILE__, $plugin_data['Name'], $plugin_data['Version'], $plugin_data['Author'] );
	}
	
}

/**
 * Required functions
 */
if ( ! function_exists( 'bizzthemes_queue_update' ) ) {
	require_once( 'dependencies/wc-functions.php' );
}

if ( is_woocommerce_active() ) {

/**
 * WC Appointments class
 */
class WC_Appointments {
	
	/**
	 * @var WC_Appointments The single instance of the class
	 */
	protected static $_instance = null;
	
	/**
	 * Main WooCommerce Appointments Instance
	 *
	 * Ensures only one instance of WooCommerce Appointments is loaded or can be loaded.
	 *
	 * @since 1.0.0
	 * @static
	 * @return WC_Appointments - Main instance
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	/**
	 * Constructor
	 */
	public function __construct() {
		define( 'WC_APPOINTMENTS_VERSION', '1.7.7' );
		define( 'WC_APPOINTMENTS_TEMPLATE_PATH', untrailingslashit( plugin_dir_path( __FILE__ ) ) . '/templates/' );
		define( 'WC_APPOINTMENTS_PLUGIN_URL', untrailingslashit( plugins_url( basename( plugin_dir_path( __FILE__ ) ), basename( __FILE__ ) ) ) );
		define( 'WC_APPOINTMENTS_MAIN_FILE', __FILE__ );

		add_action( 'init', 				array( $this, 'load_plugin_textdomain' ) );
		add_action( 'init', 				array( $this, 'init_cpt' ) );
		add_action( 'plugins_loaded', 		array( $this, 'includes' ) );
		add_action( 'wp_enqueue_scripts', 	array( $this, 'appointment_form_styles' ) );
		add_filter( 'plugin_row_meta', 		array( $this, 'plugin_row_meta' ), 10, 2 );
		add_action( 'default_product_type', array( $this, 'default_product_type' ) );
		add_filter( 'product_type_options',	array( $this, 'default_product_type_options' ) );

		//* Install
		register_activation_hook( __FILE__, array( $this, 'install' ) );

		if ( get_option( 'wc_appointments_version' ) !== WC_APPOINTMENTS_VERSION ) {
			add_action( 'shutdown', array( $this, 'delayed_install' ) );
		}

		//* Load payment gateway name.
		add_filter( 'woocommerce_payment_gateways', array( $this, 'include_gateway' ) );
		
		//* Add welcome notice
		add_action( 'admin_notices', array( $this, 'welcome_notice' ) );
	}
	
	/**
	 * Welcome notice
	 */
	public function welcome_notice() {
		if ( ! current_user_can( 'manage_appointments' ) && ! is_admin() ) {
			return;
		}
		
		//* Get user ID
		$user_id = get_current_user_id();
		
		//* Query for appointable products
		if ( ! get_user_meta( $user_id, 'nag_appointable_products_add') ) {

			$appointments = get_posts( array( 
				'post_type' 	 => 'product',
				'post_status'    => 'publish',
				'posts_per_page' => '1',
				'no_found_rows'  => true,
				'update_post_meta_cache' => false,
				'tax_query' 	 => array(
					array(
						'taxonomy' => 'product_type',
						'field'    => 'slug',
						'terms'    => 'appointment', 
					),
				),
			) );
			
			//* Notice if no appointable products are present
			if ( empty( $appointments ) ) {
				
				$message = sprintf( __( 'Welcome to WooCommerce Appointments. <a href="%s" class="button button-primary">Add Appointable Products</a>', 'woocommerce-appointments' ), admin_url( 'post-new.php?post_type=product' ) );
				
				echo "<div id=\"welcome-appointments-one\" class=\"notice-info woocommerce-message notice is-dismissible\"> <p>$message</p></div>";
			
			} else {
				
				add_user_meta( $user_id, 'nag_appointable_products_add', 'true', true);
				
			}
		
		}
		
		//* Notice if no appointable products are present
		if ( ! get_user_meta( $user_id, 'nag_global_availability') ) {
			
			add_user_meta( $user_id, 'nag_global_availability', 'true', true);
			
			$message = sprintf( __( 'Global availability has been configured from Monday to Friday, 9am to 5pm. <span class="dashicons dashicons-edit"></span> <a href="%s">Edit global availability here</a>.', 'woocommerce-appointments' ), admin_url( 'admin.php?page=wc-settings&tab=appointments' ) );
			
			echo "<div id=\"welcome-appointments-two\" class=\"notice-info notice is-dismissible\"> <p>$message</p></div>";
			
		}
	}

	/**
	 * Installer
	 */
	public function install() {
		add_action( 'shutdown', array( $this, 'delayed_install' ) );
	}

	/**
	 * Installer (delayed)
	 */
	public function delayed_install() {
		global $wpdb, $wp_roles;

		$wpdb->hide_errors();

		$collate = '';

		if ( $wpdb->has_cap( 'collation' ) ) {
			if ( ! empty( $wpdb->charset ) ) {
				$collate .= "DEFAULT CHARACTER SET $wpdb->charset";
			}
			if ( ! empty( $wpdb->collate ) ) {
				$collate .= " COLLATE $wpdb->collate";
			}
		}

		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

		dbDelta( "
CREATE TABLE {$wpdb->prefix}wc_appointment_relationships (
ID bigint(20) unsigned NOT NULL auto_increment,
product_id bigint(20) unsigned NOT NULL,
staff_id bigint(20) unsigned NOT NULL,
sort_order bigint(20) unsigned NOT NULL default 0,
PRIMARY KEY  (ID),
KEY product_id (product_id),
KEY staff_id (staff_id)
) $collate;
		" );

		//* Product type
		if ( ! get_term_by( 'slug', sanitize_title( 'appointment' ), 'product_type' ) ) {
			wp_insert_term( 'appointment', 'product_type' );
		}

		//* Capabilities
		if ( class_exists( 'WP_Roles' ) ) {
			if ( ! isset( $wp_roles ) ) {
				$wp_roles = new WP_Roles();
			}
		}
		
		//* Shop staff role
		add_role( 'shop_staff', __( 'Shop Staff', 'woocommerce-appointments' ), array(			
			'level_8'                => true,
			'level_7'                => true,
			'level_6'                => true,
			'level_5'                => true,
			'level_4'                => true,
			'level_3'                => true,
			'level_2'                => true,
			'level_1'                => true,
			'level_0'                => true,
			'read'                   => true,
			'read_private_pages'     => true,
			'read_private_posts'     => true,
			'edit_users'             => true,
			'edit_posts'             => true,
			'edit_pages'             => true,
			'edit_published_posts'   => true,
			'edit_published_pages'   => true,
			'edit_private_pages'     => true,
			'edit_private_posts'     => true,
			'edit_others_posts'      => true,
			'edit_others_pages'      => true,
			'publish_posts'          => true,
			'publish_pages'          => true,
			'delete_posts'           => true,
			'delete_pages'           => true,
			'delete_private_pages'   => true,
			'delete_private_posts'   => true,
			'delete_published_pages' => true,
			'delete_published_posts' => true,
			'delete_others_posts'    => true,
			'delete_others_pages'    => true,
			'manage_categories'      => true,
			'manage_links'           => true,
			'moderate_comments'      => true,
			'unfiltered_html'        => true,
			'upload_files'           => true,
			'export'                 => true,
			'import'                 => true,
			'list_users'             => true
		) );

		if ( is_object( $wp_roles ) ) {
			$wp_roles->add_cap( 'shop_manager', 'manage_appointments' );
			$wp_roles->add_cap( 'administrator', 'manage_appointments' );
			$wp_roles->add_cap( 'shop_staff', 'manage_appointments' );
		}

		//* Shop staff expand capabilities
		$capabilities = array();

		$capabilities['core'] = array(
			'view_woocommerce_reports'
		);

		$capability_types = array( 'appointment' );

		foreach ( $capability_types as $capability_type ) {

			$capabilities[ $capability_type ] = array(
				// Post type
				"edit_{$capability_type}",
				"read_{$capability_type}",
				"delete_{$capability_type}",
				"edit_{$capability_type}s",
				"edit_others_{$capability_type}s",
				"publish_{$capability_type}s",
				"read_private_{$capability_type}s",
				"delete_{$capability_type}s",
				"delete_private_{$capability_type}s",
				"delete_published_{$capability_type}s",
				"delete_others_{$capability_type}s",
				"edit_private_{$capability_type}s",
				"edit_published_{$capability_type}s",

				// Terms
				"manage_{$capability_type}_terms",
				"edit_{$capability_type}_terms",
				"delete_{$capability_type}_terms",
				"assign_{$capability_type}_terms"
			);
		}
		
		foreach ( $capabilities as $cap_group ) {
			foreach ( $cap_group as $cap ) {
				$wp_roles->add_cap( 'shop_staff', $cap );
				$wp_roles->add_cap( 'shop_manager', $cap );
				$wp_roles->add_cap( 'administrator', $cap );
			}
		}

		//* Update version
		update_option( 'wc_appointments_version', WC_APPOINTMENTS_VERSION );
		
		/**
		 * Set default availability
		 *
		 * from monday to friday
		 * fromt 9am to 5pm
		 */
		$default_global_availability = apply_filters( 'default_global_availability', array (
			array (
				'type' => 'days',
				'appointable' => 'yes',
				'from' => 1,
				'to' => 5
			),
			array (
				'type' => 'time',
				'appointable' => 'yes',
				'from' => '09:00',
				'to' => '17:00'
			)
		) );
		$add_global_availability = add_option( 'wc_global_appointment_availability', $default_global_availability );
		
	}

	/**
	 * Localization
	 *
	 * 		- WP_LANG_DIR/woocommerce-appointments/woocommerce-appointments-LOCALE.mo
	 * 	 	- woocommerce-appointments/languages/woocommerce-appointments-LOCALE.mo (which if not found falls back to:)
	 */
	public function load_plugin_textdomain() {
		$locale = apply_filters( 'plugin_locale', get_locale(), 'woocommerce-appointments' );

		load_textdomain( 'woocommerce-appointments', WP_LANG_DIR . '/woocommerce-appointments/woocommerce-appointments-' . $locale . '.mo' );
		load_plugin_textdomain( 'woocommerce-appointments', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
	}

	/**
	 * Load Classes
	 */
	public function includes() {
				
		//* Self
		include( 'includes/class-wc-appointment-email-manager.php' );
		include( 'includes/class-wc-appointment-cart-manager.php' );
		include( 'includes/class-wc-appointment-checkout-manager.php' );
		
		//* Admin
		if ( is_admin() ) {
			include( 'includes/admin/class-wc-appointments-admin.php' );
			include( 'includes/admin/class-wc-appointments-admin-ajax.php' );
			include( 'includes/admin/class-wc-appointments-admin-addons.php' );
		}
		
		//* Core
		include( 'includes/wc-appointments-functions.php' );
		include( 'includes/class-wc-appointment-form-handler.php' );
		include( 'includes/class-wc-appointment-order-manager.php' );
		include( 'includes/class-wc-appointment.php' );
		include( 'includes/class-wc-appointments-controller.php' );
		include( 'includes/class-wc-appointment-cron-manager.php' );
		include( 'includes/class-wc-appointments-ics-exporter.php' );
		include( 'includes/class-wc-appointments-shortcodes.php' );
		include( 'includes/gateways/class-wc-appointments-gateway.php' );
		include( 'includes/appointment-form/class-wc-appointment-form.php' );

		//* Products
		include( 'includes/class-wc-product-appointment.php' );
		include( 'includes/class-wc-product-class-loader.php' );
		include( 'includes/class-wc-product-appointment-staff.php' );
		include( 'includes/class-wc-product-appointment-rule-manager.php' );
		
		//* Integrations
		include( 'includes/integrations/woocommerce-product-addons/woocommerce-product-addons.php' ); # forked plugin with mods to suit Appointments
		if ( class_exists( 'WC_Product_Addons' ) ) {
			include( 'includes/integrations/class-wc-appointments-integration-addons.php' );
		}
		
		if ( class_exists( 'woocommerce_gravityforms' ) ) {
			include( 'includes/integrations/class-wc-appointments-integration-gf-addons.php' );
		}
		
		if ( class_exists( 'TM_Extra_Product_Options' ) ) {
			include( 'includes/integrations/class-wc-appointments-integration-tm-epo.php' );
		}
		
		if ( class_exists( 'WC_Subscriptions' ) ) {
			//include( 'includes/integrations/class-wc-appointments-integration-subscriptions.php' );
		}
		
		if ( class_exists( 'SitePress' ) && class_exists( 'woocommerce_wpml' ) ) {
			include( 'includes/integrations/class-wc-appointments-integration-wcml.php' );
		}
		
		if ( ! class_exists( 'WC_Appointments_Integration_GCal' ) ) {
			include( 'includes/integrations/class-wc-appointments-integration-gcal.php' );
		}
		
		if ( class_exists( 'Follow_Up_Emails' ) ) {
			include( 'includes/integrations/class-wc-appointments-integration-follow-ups.php' );
		}

	}

	/**
	 * Init CPT
	 */
	public function init_cpt() {
		register_post_type( 'wc_appointment',
			apply_filters( 'woocommerce_register_post_type_wc_appointment',
				array(
					'label'  => __( 'Appointment', 'woocommerce-appointments' ),
					'labels' => array(
							'name'               => __( 'Appointments', 'woocommerce-appointments' ),
							'singular_name'      => __( 'Appointment', 'woocommerce-appointments' ),
							'add_new'            => __( 'Add Appointment', 'woocommerce-appointments' ),
							'add_new_item'       => __( 'Add New Appointment', 'woocommerce-appointments' ),
							'edit'               => __( 'Edit', 'woocommerce-appointments' ),
							'edit_item'          => __( 'Edit Appointment', 'woocommerce-appointments' ),
							'new_item'           => __( 'New Appointment', 'woocommerce-appointments' ),
							'view'               => __( 'View Appointment', 'woocommerce-appointments' ),
							'view_item'          => __( 'View Appointment', 'woocommerce-appointments' ),
							'search_items'       => __( 'Search Appointments', 'woocommerce-appointments' ),
							'not_found'          => __( 'No Appointments found', 'woocommerce-appointments' ),
							'not_found_in_trash' => __( 'No Appointments found in trash', 'woocommerce-appointments' ),
							'parent'             => __( 'Parent Appointments', 'woocommerce-appointments' ),
							'menu_name'          => _x( 'Appointments', 'Admin menu name', 'woocommerce-appointments' ),
							'all_items'          => __( 'All Appointments', 'woocommerce-appointments' ),
						),
					'description' 			=> __( 'This is where appointments are stored.', 'woocommerce-appointments' ),
					'public' 				=> false,
					'show_ui' 				=> true,
					'capability_type' 		=> 'appointment',
					'menu_icon' 			=> 'dashicons-backup',
					'map_meta_cap'			=> true,
					'publicly_queryable' 	=> false,
					'exclude_from_search' 	=> true,
					'show_in_menu' 			=> true,
					'hierarchical' 			=> false,
					'show_in_nav_menus' 	=> false,
					'rewrite' 				=> false,
					'query_var' 			=> false,
					'supports' 				=> array( '' ),
					'has_archive' 			=> false,
				)
			)
		);

		/**
		 * Post status
		 */
		register_post_status( 'complete', array(
			'label'                     => '<span class="status-complete tips" data-tip="' . _x( 'Complete', 'woocommerce-appointments', 'woocommerce-appointments' ) . '">' . _x( 'Complete', 'woocommerce-appointments', 'woocommerce-appointments' ) . '</span>',
			'public'                    => true,
			'exclude_from_search'       => false,
			'show_in_admin_all_list'    => true,
			'show_in_admin_status_list' => true,
			'label_count'               => _n_noop( 'Complete <span class="count">(%s)</span>', 'Complete <span class="count">(%s)</span>', 'woocommerce-appointments' ),
		) );
		register_post_status( 'paid', array(
			'label'                     => '<span class="status-paid tips" data-tip="' . _x( 'Paid &amp; Confirmed', 'woocommerce-appointments', 'woocommerce-appointments' ) . '">' . _x( 'Paid &amp; Confirmed', 'woocommerce-appointments', 'woocommerce-appointments' ) . '</span>',
			'public'                    => true,
			'exclude_from_search'       => false,
			'show_in_admin_all_list'    => true,
			'show_in_admin_status_list' => true,
			'label_count'               => _n_noop( 'Paid &amp; Confirmed <span class="count">(%s)</span>', 'Paid &amp; Confirmed <span class="count">(%s)</span>', 'woocommerce-appointments' ),
		) );
		register_post_status( 'confirmed', array(
			'label'                     => '<span class="status-confirmed tips" data-tip="' . _x( 'Confirmed', 'woocommerce-appointments', 'woocommerce-appointments' ) . '">' . _x( 'Confirmed', 'woocommerce-appointments', 'woocommerce-appointments' ) . '</span>',
			'public'                    => true,
			'exclude_from_search'       => false,
			'show_in_admin_all_list'    => true,
			'show_in_admin_status_list' => true,
			'label_count'               => _n_noop( 'Confirmed <span class="count">(%s)</span>', 'Confirmed <span class="count">(%s)</span>', 'woocommerce-appointments' ),
		) );
		register_post_status( 'unpaid', array(
			'label'                     => '<span class="status-unpaid tips" data-tip="' . _x( 'Un-paid', 'woocommerce-appointments', 'woocommerce-appointments' ) . '">' . _x( 'Un-paid', 'woocommerce-appointments', 'woocommerce-appointments' ) . '</span>',
			'public'                    => true,
			'exclude_from_search'       => true,
			'show_in_admin_all_list'    => true,
			'show_in_admin_status_list' => true,
			'label_count'               => _n_noop( 'Un-paid <span class="count">(%s)</span>', 'Un-paid <span class="count">(%s)</span>', 'woocommerce-appointments' ),
		) );
		register_post_status( 'pending-confirmation', array(
			'label'                     => '<span class="status-pending tips" data-tip="' . _x( 'Pending Confirmation', 'woocommerce-appointments', 'woocommerce-appointments' ) . '">' . _x( 'Pending Confirmation', 'woocommerce-appointments', 'woocommerce-appointments' ) . '</span>',
			'public'                    => true,
			'exclude_from_search'       => false,
			'show_in_admin_all_list'    => true,
			'show_in_admin_status_list' => true,
			'label_count'               => _n_noop( 'Pending Confirmation <span class="count">(%s)</span>', 'Pending Confirmation <span class="count">(%s)</span>', 'woocommerce-appointments' ),
		) );
		register_post_status( 'cancelled', array(
			'label'                     => '<span class="status-cancelled tips" data-tip="' . _x( 'Cancelled', 'woocommerce-appointments', 'woocommerce-appointments' ) . '">' . _x( 'Cancelled', 'woocommerce-appointments', 'woocommerce-appointments' ) . '</span>',
			'public'                    => true,
			'exclude_from_search'       => false,
			'show_in_admin_all_list'    => true,
			'show_in_admin_status_list' => true,
			'label_count'               => _n_noop( 'Cancelled <span class="count">(%s)</span>', 'Cancelled <span class="count">(%s)</span>', 'woocommerce-appointments' ),
		) );
		register_post_status( 'in-cart', array(
			'label'                     => '<span class="status-incart tips" data-tip="' . _x( 'In Cart', 'woocommerce-appointments', 'woocommerce-appointments' ) . '">' . _x( 'In Cart', 'woocommerce-appointments', 'woocommerce-appointments' ) . '</span>',
			'public'                    => false,
			'exclude_from_search'       => false,
			'show_in_admin_all_list'    => false,
			'show_in_admin_status_list' => true,
			'label_count'               => _n_noop( 'In Cart <span class="count">(%s)</span>', 'In Cart <span class="count">(%s)</span>', 'woocommerce-appointments' ),
		) );
		register_post_status( 'was-in-cart', array(
			'label'                     => false,
			'public'                    => false,
			'exclude_from_search'       => false,
			'show_in_admin_all_list'    => false,
			'show_in_admin_status_list' => false,
			'label_count'               => false
		) );
	}

	/**
	 * Frontend appointment form scripts
	 */
	public function appointment_form_styles() {
		global $wp_scripts;

		$jquery_version = isset( $wp_scripts->registered['jquery-ui-core']->ver ) ? $wp_scripts->registered['jquery-ui-core']->ver : '1.9.2';

		wp_enqueue_style( 'jquery-ui-style', '//ajax.googleapis.com/ajax/libs/jqueryui/' . $jquery_version . '/themes/smoothness/jquery-ui.css' );
		wp_enqueue_style( 'wc-appointments-styles', WC_APPOINTMENTS_PLUGIN_URL . '/assets/css/frontend.css', null, WC_APPOINTMENTS_VERSION );
		
		wp_register_style( 'wc-appointments-select2', WC_APPOINTMENTS_PLUGIN_URL . '/assets/css/select2.css', null, WC_APPOINTMENTS_VERSION );
		wp_enqueue_style( 'wc-appointments-select2' );

	}

	/**
	 * Add a custom payment gateway
	 * This gateway works with appointment that requires confirmation
	 */
	public function include_gateway( $gateways ) {
		$gateways[] = 'WC_Appointments_Gateway';

		return $gateways;
	}

	/**
	 * Show row meta on the plugin screen.
	 *
	 * @access	public
	 * @param	mixed $links Plugin Row Meta
	 * @param	mixed $file  Plugin Base file
	 * @return	array
	 */
	public function plugin_row_meta( $links, $file ) {
		if ( $file == plugin_basename( WC_APPOINTMENTS_MAIN_FILE ) ) {
			$row_meta = array(
				'docs'		=>	'<a href="' . esc_url( apply_filters( 'woocommerce_appointments_docs_url', 'https://bizzthemes.com/help/setup/woocommerce-appointments/' ) ) . '" title="' . esc_attr( __( 'View Documentation', 'woocommerce-appointments' ) ) . '">' . __( 'Docs', 'woocommerce-appointments' ) . '</a>',
				'support'	=>	'<a href="' . esc_url( apply_filters( 'woocommerce_appointments_support_url', 'https://bizzthemes.com/forums/' ) ) . '" title="' . esc_attr( __( 'Visit Support Forum', 'woocommerce-appointments' ) ) . '">' . __( 'Premium Support', 'woocommerce-appointments' ) . '</a>',
			);

			return array_merge( $links, $row_meta );
		}

		return (array) $links;
	}
	
	/**
	 * Change default product type to appointment
	 */
	public function default_product_type(){
		return "appointment";
	}
	
	/**
	 * Change default product type options
	 */
	function default_product_type_options( $product_type_options ) {
		$product_type_options['virtual']['default'] = 'yes';
		
		return $product_type_options;
	}
}

/**
 * Returns the main instance of WC Appointments.
 *
 * @since  1.0.0
 * @return WooCommerce Appointments
 */
function wc_appointments() {
	return WC_Appointments::instance();
}

// fire it up!
$GLOBALS['wc_appointments'] = new WC_Appointments();

}
