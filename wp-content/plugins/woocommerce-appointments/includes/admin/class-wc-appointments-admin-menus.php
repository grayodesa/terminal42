<?php

if ( ! defined( 'ABSPATH' ) )
	exit;

/**
 * WC_Appointments_Admin_Menus
 */
class WC_Appointments_Admin_Menus {

	/**
	 * Constructor
	 */
	public function __construct() {
		add_action( 'current_screen', array( $this, 'buffer' ) );
		add_filter( 'woocommerce_screen_ids', array( $this, 'woocommerce_screen_ids' ) );
		add_action( 'admin_menu', array( $this, 'remove_default_appointments_menu_links' ), 10 );
		add_action( 'admin_menu', array( $this, 'admin_menu' ), 49 );
		add_filter( 'menu_order', array( $this, 'menu_order' ), 20 );
		add_filter( 'admin_url', array( $this, 'add_new_appointment_url' ), 10, 2 );
	}

	/**
	 * output buffer
	 */
	public function buffer() {
		$screen = get_current_screen();
		if ( $screen->id == 'wc_appointment_page_add_appointment' ) {
			ob_start();
		}
	}

	/**
	 * Screen IDS
	 * @param  array  $ids
	 * @return array
	 */
	public function woocommerce_screen_ids( $ids ) {
		return array_merge( $ids, array(
			'edit-wc_appointment',
			'wc_appointment',
			'wc_appointment_page_appointment_calendar',
			'wc_appointment_page_appointment_notification',
			'wc_appointment_page_add_appointment',
			'wc_appointment_page_wc_appointments_global_availability',
		) );
	}

	/**
	 * Removes the default appointments menu links from the main admin menu
	 */
	public function remove_default_appointments_menu_links() {
		global $submenu;

		if ( isset( $submenu['edit.php?post_type=wc_appointment'] ) ) {
			foreach ( $submenu['edit.php?post_type=wc_appointment'] as $key => $value ) {
				if ( 'post-new.php?post_type=wc_appointment' == $value[2] ) {
					unset( $submenu['edit.php?post_type=wc_appointment'][ $key ] );
					return;
				}
			}
		}
	}

	/**
	 * Add a submenu for managing appointments pages
	 */
	public function admin_menu() {
		$add_appointment_page = add_submenu_page( 'edit.php?post_type=wc_appointment', __( 'Add Appointment', 'woocommerce-appointments' ), __( 'Add Appointment', 'woocommerce-appointments' ), 'manage_appointments', 'add_appointment', array( $this, 'add_appointment_page' ) );
		$calendar_page       = add_submenu_page( 'edit.php?post_type=wc_appointment', __( 'Calendar', 'woocommerce-appointments' ), __( 'Calendar', 'woocommerce-appointments' ), 'manage_appointments', 'appointment_calendar', array( $this, 'calendar_page' ) );

		// Add action for screen options on this new page
		add_action( 'admin_print_scripts-' . $add_appointment_page, array( $this, 'add_appointment_page_scripts' ) );
		add_action( 'admin_print_scripts-' . $calendar_page, array( $this, 'calendar_page_scripts' ) );
	}

	/**
	 * Create appointment scripts
	 */
	public function add_appointment_page_scripts() {
		global $wc_appointments;
		$wc_appointments->appointment_form_styles();
	}

	/**
	 * Create appointment page
	 */
	public function add_appointment_page() {
		require_once( 'class-wc-appointments-admin-add.php' );
		$page = new WC_Appointments_Admin_Add();
		$page->output();
	}

	/**
	 * calendar_page_scripts
	 */
	public function calendar_page_scripts() {
		wp_enqueue_script( 'jquery-ui-datepicker' );
	}

	/**
	 * Output the calendar page
	 */
	public function calendar_page() {
		require_once( 'class-wc-appointments-admin-calendar.php' );
		$page = new WC_Appointments_Admin_Calendar();
		$page->output();
	}

	/**
	 * Reorder the WC menu items in admin.
	 *
	 * @param mixed $menu_order
	 * @return array
	 */
	public function menu_order( $menu_order ) {
		// Initialize our custom order array
		$new_menu_order = array();

		// Get index of product menu
		$appointment_menu = array_search( 'edit.php?post_type=wc_appointment', $menu_order );

		// Loop through menu order and do some rearranging
		foreach ( $menu_order as $index => $item ) :
			if ( ( ( 'edit.php?post_type=product' ) == $item ) ) :
				$new_menu_order[] = $item;
				$new_menu_order[] = 'edit.php?post_type=wc_appointment';
				unset( $menu_order[ $appointment_menu ] );
			else :
				$new_menu_order[] = $item;
			endif;
		endforeach;

		// Return order
		return $new_menu_order;
	}

	/**
	 * Filters the add new appointment url to point to our custom page
	 * @param string $url original url
	 * @param string $path requested path that we can match against
	 * @return string new url
	 */
	public function add_new_appointment_url( $url, $path ) {
		if ( 'post-new.php?post_type=wc_appointment' == $path ) {
			return admin_url( 'edit.php?post_type=wc_appointment&page=add_appointment' );
		}

		return $url;
	}
}

$GLOBALS['wc_appointment_admin_menus'] = new WC_Appointments_Admin_Menus();
