<?php
/**
 * Admin functions for the appointments post type
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'WC_Appointments_Admin_CPT' ) ) :

/**
 * WC_Admin_CPT_Product Class
 */
class WC_Appointments_Admin_CPT {

	/**
	 * Constructor
	 */
	public function __construct() {		
		$this->type = 'wc_appointment';

		// Post title fields
		add_filter( 'enter_title_here', array( $this, 'enter_title_here' ), 1, 2 );

		// Admin Columns
		add_filter( 'manage_' . $this->type . '_posts_columns', array( $this, 'custom_columns_define' ) );
		add_action( 'manage_' . $this->type . '_posts_custom_column', array( $this, 'custom_columns_content' ), 2 );
		add_filter( 'manage_edit-' . $this->type . '_sortable_columns', array( $this, 'custom_columns_sort' ) );
		add_filter( 'request', array( $this, 'custom_columns_orderby' ) );
		add_filter( 'list_table_primary_column', array( $this, 'list_table_primary_column' ), 10, 2 );

		// Filtering
		add_action( 'restrict_manage_posts', array( $this, 'appointment_filters' ) );
		add_filter( 'parse_query', array( $this, 'appointment_filters_query' ) );
		add_filter( 'get_search_query', array( $this, 'search_label' ) );

		// Search
		add_filter( 'parse_query', array( $this, 'search_custom_fields' ) );

		// Actions
		add_filter( 'bulk_actions-edit-' . $this->type, array( $this, 'bulk_actions' ) );
		add_action( 'load-edit.php', array( $this, 'bulk_action' ) );
		add_action( 'admin_footer', array( $this, 'bulk_admin_footer' ), 10 );
		add_action( 'admin_notices', array( $this, 'bulk_admin_notices' ) );

		// Sync
		add_action( 'woocommerce_appointment_cancelled', array( $this, 'cancel_order' ) );
		add_action( 'before_delete_post', array( $this, 'delete_post' ) );
		add_action( 'wp_trash_post', array( $this, 'trash_post' ) );
		add_action( 'untrash_post', array( $this, 'untrash_post' ) );
		
		// Transient Clearing
		add_action( 'delete_appointment_transients', array( $this, 'delete_appointment_transients' ) );
		add_action( 'delete_appointment_dr_transients', array( $this, 'delete_appointment_dr_transients' ) );
	}
	
	/**
	 * Register post type
	 */
	public function register_post_types() {
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
					'capability_type' 		=> 'product',
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
	 * Remove edit from the bulk actions.
	 *
	 * @access public
	 * @param mixed $actions
	 * @return array
	 */
	public function bulk_actions( $actions ) {

		if ( isset( $actions['edit'] ) ) {
			unset( $actions['edit'] );
		}

		return $actions;
	}

	/**
	 * Add extra bulk action options to mark orders as complete or processing
	 *
	 * Using Javascript until WordPress core fixes: http://core.trac.wordpress.org/ticket/16031
	 *
	 * @access public
	 * @return void
	 */
	public function bulk_admin_footer() {
		global $post_type;

		if ( $this->type == $post_type ) {
			?>
			<script type="text/javascript">
				jQuery( document ).ready( function ( $ ) {
					$( '<option value="confirm_appointments"><?php _e( 'Confirm appointments', 'woocommerce-appointments' )?></option>' ).appendTo( 'select[name="action"], select[name="action2"]' );

					$( '<option value="unconfirm_appointments"><?php _e( 'Unconfirm appointments', 'woocommerce-appointments' )?></option>' ).appendTo( 'select[name="action"], select[name="action2"]' );

					$( '<option value="cancel_appointments"><?php _e( 'Cancel appointments', 'woocommerce-appointments' )?></option>' ).appendTo( 'select[name="action"], select[name="action2"]' );

					$( '<option value="mark_paid_appointments"><?php _e( 'Mark appointments as paid', 'woocommerce-appointments' )?></option>' ).appendTo( 'select[name="action"], select[name="action2"]' );

					$( '<option value="mark_unpaid_appointments"><?php _e( 'Mark appointments as unpaid', 'woocommerce-appointments' )?></option>' ).appendTo( 'select[name="action"], select[name="action2"]' );
				});
			</script>
			<?php
		}
	}

	/**
	 * Process the new bulk actions for changing order status
	 *
	 * @access public
	 * @return void
	 */
	public function bulk_action() {
		$wp_list_table = _get_list_table( 'WP_Posts_List_Table' );
		$action = $wp_list_table->current_action();

		switch ( $action ) {
			case 'confirm_appointments' :
				$new_status = 'confirmed';
				$report_action = 'appointments_confirmed';
				break;
			case 'unconfirm_appointments' :
				$new_status = 'pending-confirmation';
				$report_action = 'appointments_unconfirmed';
				break;
			case 'mark_paid_appointments' :
				$new_status = 'paid';
				$report_action = 'appointments_marked_paid';
				break;
			case 'mark_unpaid_appointments' :
				$new_status = 'unpaid';
				$report_action = 'appointments_marked_unpaid';
				break;
			case 'cancel_appointments' :
				$new_status = 'cancelled';
				$report_action = 'appointments_cancelled';
				break;
			break;

			default:
				return;
		}

		$changed = 0;

		$post_ids = array_map( 'absint', (array) $_REQUEST['post'] );

		foreach ( $post_ids as $post_id ) {
			$appointment = get_wc_appointment( $post_id );
			if ( $appointment->get_status() !== $new_status ) {
				$appointment->update_status( $new_status );
			}
			$changed++;
		}

		$sendback = add_query_arg( array( 'post_type' => $this->type, $report_action => true, 'changed' => $changed, 'ids' => join( ',', $post_ids ) ), '' );
		wp_redirect( $sendback );
		exit();
	}

	/**
	 * Show confirmation message that order status changed for number of orders
	 *
	 * @access public
	 * @return void
	 */
	public function bulk_admin_notices() {
		global $post_type, $pagenow;

		if ( isset( $_REQUEST['appointments_confirmed'] ) || isset( $_REQUEST['appointments_marked_paid'] ) || isset( $_REQUEST['appointments_marked_unpaid'] ) || isset( $_REQUEST['appointments_unconfirmed'] ) || isset( $_REQUEST['appointments_cancelled'] ) ) {
			$number = isset( $_REQUEST['changed'] ) ? absint( $_REQUEST['changed'] ) : 0;

			if ( 'edit.php' == $pagenow && $this->type == $post_type ) {
				$message = sprintf( _n( 'Appointment status changed.', '%s appointment statuses changed.', $number, 'woocommerce-appointments' ), number_format_i18n( $number ) );
				echo '<div class="updated"><p>' . $message . '</p></div>';
			}
		}
	}

	/**
	 * Change title boxes in admin.
	 * @param  string $text
	 * @param  object $post
	 * @return string
	 */
	public function enter_title_here( $text, $post ) {
		if ( $post->post_type == 'wc_appointment' ) {
			return __( 'Appointment Title', 'woocommerce-appointments' );
		}

		return $text;
	}

	/**
	 * Change the columns shown in admin.
	 */
	public function custom_columns_define( $existing_columns ) {
		if ( empty( $existing_columns ) && ! is_array( $existing_columns ) ) {
			$existing_columns = array();
		}

		unset( $existing_columns['comments'], $existing_columns['title'], $existing_columns['date'] );

		$columns                    	= array();
		$columns["appointment_status"]  = '<span class="status_head tips" data-tip="' . esc_attr__( 'Status', 'woocommerce-appointments' ) . '">' . esc_attr__( 'Status', 'woocommerce-appointments' ) . '</span>';
		$columns["appointment_id"]      = __( 'ID', 'woocommerce-appointments' );
		$columns["order"]           	= __( 'Order', 'woocommerce-appointments' );
		$columns["scheduled_product"]  	= __( 'Scheduled Product', 'woocommerce-appointments' );
		$columns["customer"]        	= __( 'Scheduled By', 'woocommerce-appointments' );
		$columns["qty"]        			= __( 'Qty', 'woocommerce-appointments' );
		$columns["appointment_date"]    = __( 'Date', 'woocommerce-appointments' );
		$columns["appointment_time"]    = __( 'Time', 'woocommerce-appointments' );
		$columns["appointment_actions"] = __( 'Actions', 'woocommerce-appointments' );

		return array_merge( $existing_columns, $columns );
	}

	/**
	 * Define our custom columns shown in admin.
	 * @param  string $column
	 */
	public function custom_columns_content( $column ) {
		global $post, $appointment;

		if ( empty( $appointment ) || $appointment->id != $post->ID ) {
			$appointment = get_wc_appointment( $post->ID );
		}

		switch ( $column ) {
			case 'appointment_status' :
				echo $appointment->get_status( false );
				break;
			case 'appointment_id' :
				printf( '<a href="%s">' . __( 'Appointment #%d', 'woocommerce-appointments' ) . '</a>', admin_url( 'post.php?post=' . $post->ID . '&action=edit' ), $post->ID );
				break;
			case 'order' :
				$order = $appointment->get_order();

				if ( $order ) {
					echo '<a href="' . admin_url( 'post.php?post=' . $order->id . '&action=edit' ) . '">#' . $order->get_order_number() . '</a> - ' . esc_html( wc_get_order_status_name( $order->get_status() ) );
				} else {
					echo '-';
				}
				break;
			case 'customer' :
				$customer = $appointment->get_customer();

				if ( $customer && $customer->user_id ) {
					echo '<a href="' .  get_edit_user_link( $customer->user_id ) . '">' . $customer->name . '</a>';
				} else if ( $customer && ! $customer->user_id ) {
					echo $customer->name;
				} else {
					echo '-';
				}
				break;
			case 'scheduled_product' :
				$product  = $appointment->get_product();
				$staff = $appointment->get_staff_member();

				if ( $product ) {
					echo '<a href="' . admin_url( 'post.php?post=' . $product->id . '&action=edit' ) . '">' . $product->post->post_title . '</a>';
					if ( $staff ) {
						echo ' (<a href="' . get_edit_user_link( $staff->ID ) . '">' . $staff->display_name . '</a>)';
					}
				} else {
					echo '-';
				}
				break;
			case 'qty' :
				$order = $appointment->get_order();

				if ( $order ) {
					echo $order->get_item_count();
				} else {
					echo 1;
				}
				break;
			case 'appointment_date' :
				echo $appointment->get_start_date( wc_date_format(), '' );
				break;
			case 'appointment_time' :
				echo $appointment->get_start_date( '', get_option( 'time_format' ) ) . ' &mdash; ' . $appointment->get_end_date( '', get_option( 'time_format' ) );
				break;
			case 'appointment_actions' :
				echo '<p>';
				$actions = array();

				$actions['view'] = array(
					'url' 		=> admin_url( 'post.php?post=' . $post->ID . '&action=edit' ),
					'name' 		=> __( 'View', 'woocommerce-appointments' ),
					'action' 	=> "view"
				);

				if ( in_array( $appointment->get_status(), array( 'pending-confirmation' ) ) ) {
					$actions['confirm'] = array(
						'url' 		=> wp_nonce_url( admin_url( 'admin-ajax.php?action=wc-appointment-confirm&appointment_id=' . $post->ID ), 'wc-appointment-confirm' ),
						'name' 		=> __( 'Confirm', 'woocommerce-appointments' ),
						'action' 	=> "confirm"
					);
				}

				$actions = apply_filters( 'woocommerce_admin_appointment_actions', $actions, $appointment );

				foreach ( $actions as $action ) {
					printf( '<a class="button tips %s" href="%s" data-tip="%s">%s</a>', esc_attr( $action['action'] ), esc_url( $action['url'] ), esc_attr( $action['name'] ), esc_attr( $action['name'] ) );
				}
				echo '</p>';
				break;
		}
	}
	
	/**
	 * Make product columns sortable
	 *
	 * https://gist.github.com/906872
	 *
	 * @access public
	 * @param mixed $columns
	 * @return array
	 */
	public function custom_columns_sort( $columns ) {
		$custom = array(
			'appointment_id'		=> 'appointment_id',
			'scheduled_product'		=> 'scheduled_product',
			'appointment_status'	=> 'status',
			'appointment_date'		=> 'start_date',
		);
		unset( $columns['comments'] );
		
		return wp_parse_args( $custom, $columns );
	}

	/**
	 * Product column orderby
	 *
	 * http://scribu.net/wordpress/custom-sortable-columns.html#comment-4732
	 *
	 * @access public
	 * @param mixed $vars
	 * @return array
	 */
	public function custom_columns_orderby( $vars ) {
		if ( isset( $vars['orderby'] ) ) {
			if ( 'appointment_id' == $vars['orderby'] ) {
				$vars = array_merge( $vars, array(
					'orderby' 	=> 'ID'
				) );
			}

			if ( 'scheduled_product' == $vars['orderby'] ) {
				$vars = array_merge( $vars, array(
					'meta_key' 	=> '_appointment_product_id',
					'orderby' 	=> 'meta_value_num'
				) );
			}

			if ( 'status' == $vars['orderby'] ) {
				$vars = array_merge( $vars, array(
					'orderby' 	=> 'post_status'
				) );
			}

			if ( 'appointment_date' == $vars['orderby'] ) {
				$vars = array_merge( $vars, array(
					'meta_key' 	=> '_appointment_start',
					'orderby' 	=> 'meta_value_num'
				) );
			}

		}

		return $vars;
	}
	
	/**
	 * Set list table primary column for products and orders
	 * Support for WordPress 4.3
	 *
	 * @param  string $default
	 * @param  string $screen_id
	 *
	 * @return string
	 */
	public function list_table_primary_column( $default, $screen_id ) {		
		if ( 'edit-wc_appointment' === $screen_id ) {
			return 'appointment_id';
		}

		return $default;
	}

	/**
	 * Show a filter box
	 */
	public function appointment_filters() {
		global $typenow, $wp_query;

		if ( $typenow != $this->type ) {
			return;
		}

		$filters = array();

		$products = WC_Appointments_Admin::get_appointment_products();

		foreach ( $products as $product ) {
			$filters[ $product->ID ] = $product->post_title;

			$staff = wc_appointment_get_product_staff( $product->ID );

			foreach ( $staff as $staff ) {
				$filters[ $staff->ID ] = '&nbsp;&nbsp;&nbsp;' . $staff->display_name;
			}
		}

		$output = '';

		if ( $filters ) {
			$output .= '<select name="filter_appointments">';
			$output .= '<option value="">' . __( 'All Appointable Products', 'woocommerce-appointments' ) . '</option>';

			foreach ( $filters as $filter_id => $filter ) {
				$output .= '<option value="' . absint( $filter_id ) . '" ';

				if ( isset( $_REQUEST['filter_appointments'] ) ) {
					$output .= selected( $filter_id, $_REQUEST['filter_appointments'], false );
				}

				$output .= '>' . esc_html( $filter ) . '</option>';
			}

			$output .= '</select>';
		}

		echo $output;
	}

	/**
	 * Filter the products in admin based on options
	 *
	 * @param mixed $query
	 */
	public function appointment_filters_query( $query ) {
		global $typenow, $wp_query;

		if ( $typenow == $this->type ) {
			if ( ! empty( $_REQUEST['filter_appointments'] ) && empty( $query->query_vars['suppress_filters'] ) ) {				
				$query->query_vars['meta_query'] = array(
					array(
						'key'   => get_post_type( $_REQUEST['filter_appointments'] ) === 'appointable_staff' ? '_appointment_staff_id' : '_appointment_product_id',
						'value' => absint( $_REQUEST['filter_appointments'] )
					)
				);
			}
		}
	}

	/**
	 * Search custom fields
	 *
	 * @param mixed $wp
	 */
	public function search_custom_fields( $wp ) {
		global $pagenow, $wpdb;

		if ( 'edit.php' != $pagenow || empty( $wp->query_vars['s'] ) || $wp->query_vars['post_type'] != $this->type ) {
			return $wp;
		}

		$search_fields = array_map( 'wc_clean', array(
			'_billing_first_name',
			'_billing_last_name',
			'_billing_company',
			'_billing_address_1',
			'_billing_address_2',
			'_billing_city',
			'_billing_postcode',
			'_billing_country',
			'_billing_state',
			'_billing_email',
			'_billing_phone',
			'_shipping_first_name',
			'_shipping_last_name',
			'_shipping_address_1',
			'_shipping_address_2',
			'_shipping_city',
			'_shipping_postcode',
			'_shipping_country',
			'_shipping_state'
		) );

		// Search orders
		$order_ids = $wpdb->get_col(
			$wpdb->prepare( "
				SELECT post_id
				FROM {$wpdb->postmeta}
				WHERE meta_key IN ('" . implode( "','", $search_fields ) . "')
				AND meta_value LIKE '%%%s%%'",
				esc_attr( $_GET['s'] )
			)
		);

		// Remove s - we don't want to search order name
		unset( $wp->query_vars['s'] );

		// so we know we're doing this
		$appointment_ids = array_merge(
			$wpdb->get_col( "
				SELECT ID FROM {$wpdb->posts}
				WHERE post_parent IN (" . implode( ',', $order_ids ) . ");
			"),
			$wpdb->get_col(
				$wpdb->prepare( "
					SELECT ID
						FROM {$wpdb->posts}
						WHERE post_title LIKE '%%%s%%'
						OR ID = %d
					;",
					esc_attr( $_GET['s'] ),
					absint( $_GET['s'] )
				)
			),
			array( 0 ) // so we don't get back all results for incorrect search
		);

		// Search by found posts
		$wp->query_vars['post__in']       = $appointment_ids;
		$wp->query_vars['appointment_search'] = true;
	}

	/**
	 * Change the label when searching orders.
	 *
	 * @access public
	 * @param mixed $query
	 * @return string
	 */
	public function search_label( $query ) {
		global $pagenow, $typenow;

		if ( 'edit.php' != $pagenow ) {
			return $query;
		}

		if ( $typenow != $this->type ) {
			return $query;
		}

		if ( ! get_query_var( 'appointment_search' ) ) {
			return $query;
		}

		return $_GET['s'];
	}

	/**
	 * Cancel order with appointments
	 * @param  int $appointment_id
	 */
	public function cancel_order( $appointment_id ) {
		global $wpdb;

		// Prevents infinite loop during synchronization
		update_post_meta( $appointment_id, '_appointment_status_sync', true );

		$order_id = $wpdb->get_var( $wpdb->prepare( "SELECT post_parent FROM {$wpdb->posts} WHERE ID = %d", $appointment_id ) );

		$order = wc_get_order( $order_id );

		if ( '' != $order->id && false == get_post_meta( $order->id, '_appointment_status_sync', true ) ) {

			// Only cancel if the order has 1 appointment
			if ( 1 === count( $order->get_items() ) ) {
				$order->update_status( 'cancelled' );
			}
		}

		delete_post_meta( $appointment_id, '_appointment_status_sync' );
		
		do_action( 'delete_appointment_transients' );
		do_action( 'delete_appointment_dr_transients' );
	}

	/**
	 * Removes parent order to the appointment being deleted.
	 *
	 * @param mixed $appointment_id ID of post being deleted
	 */
	public function delete_post( $appointment_id ) {
		if ( ! current_user_can( 'delete_posts' ) ) {
			return;
		}

		if ( $appointment_id > 0 && $this->type == get_post_type( $appointment_id ) ) {
			global $wpdb;

			// Prevents infinite loop during synchronization
			update_post_meta( $appointment_id, '_appointment_delete_sync', true );

			$order_id = $wpdb->get_var( $wpdb->prepare( "SELECT post_parent FROM {$wpdb->posts} WHERE ID = %d", $appointment_id ) );

			if ( '' != $order_id && false == get_post_meta( $order_id, '_appointment_delete_sync', true ) ) {
				wp_delete_post( $order_id, true );
			}

			delete_post_meta( $appointment_id, '_appointment_delete_sync' );
			
			do_action( 'delete_appointment_transients' );
			do_action( 'delete_appointment_dr_transients' );
		}
	}

	/**
	 * Trash order with appointments
	 *
	 * @param mixed $appointment_id
	 */
	public function trash_post( $appointment_id ) {
		if ( $appointment_id > 0 && $this->type == get_post_type( $appointment_id ) ) {
			global $wpdb;

			// Prevents infinite loop during synchronization
			update_post_meta( $appointment_id, '_appointment_trash_sync', true );

			$order_id = $wpdb->get_var( $wpdb->prepare( "SELECT post_parent FROM {$wpdb->posts} WHERE ID = %d", $appointment_id ) );

			if ( '' != $order_id && false == get_post_meta( $order_id, '_appointment_trash_sync', true ) ) {
				wp_trash_post( $order_id );
			}

			delete_post_meta( $appointment_id, '_appointment_trash_sync' );
			
			do_action( 'delete_appointment_transients' );
			do_action( 'delete_appointment_dr_transients' );
		}
	}

	/**
	 * Untrash order with appointments
	 *
	 * @param mixed $appointment_id
	 */
	public function untrash_post( $appointment_id ) {
		if ( $appointment_id > 0 && $this->type == get_post_type( $appointment_id ) ) {
			global $wpdb;

			// Prevents infinite loop during synchronization
			update_post_meta( $appointment_id, '_appointment_untrash_sync', true );

			$order_id = $wpdb->get_var( $wpdb->prepare( "SELECT post_parent FROM {$wpdb->posts} WHERE ID = %d", $appointment_id ) );

			if ( '' != $order_id && false == get_post_meta( $order_id, '_appointment_trash_sync', true ) ) {
				wp_untrash_post( $order_id );
			}

			delete_post_meta( $appointment_id, '_appointment_untrash_sync' );
			
			do_action( 'delete_appointment_transients' );
			do_action( 'delete_appointment_dr_transients' );
		}
	}
	
	/**
	 * Delete Appointment Related Transients
	 */
	public function delete_appointment_transients() {
		global $wpdb;
		$limit = 1000;

		$affected_timeouts   = $wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->options} WHERE option_name LIKE %s LIMIT %d;", "_transient_timeout_schedule_fo_%", $limit ) );
		$affected_transients = $wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->options} WHERE option_name LIKE %s LIMIT %d;", "_transient_schedule_fo_%", $limit ) );

		// If affected rows is equal to limit, there are more rows to delete. Delete in 10 secs.
		if ( $affected_transients === $limit ) {
			wp_schedule_single_event( time() + 10, 'delete_appointment_transients', array( time() ) );
		}
	}

	/**
	 * Delete Appointment Date Range Related Transients
	 */
	public function delete_appointment_dr_transients() {
		global $wpdb;
		$limit = 1000;

		$affected_timeouts   = $wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->options} WHERE option_name LIKE %s LIMIT %d;", "_transient_timeout_schedule_dr_%", $limit ) );
		$affected_transients = $wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->options} WHERE option_name LIKE %s LIMIT %d;", "_transient_schedule_dr_%", $limit ) );

		// If affected rows is equal to limit, there are more rows to delete. Delete in 10 secs.
		if ( $affected_transients === $limit ) {
			wp_schedule_single_event( time() + 10, 'delete_appointment_dr_transients', array( time() ) );
		}
	}
}

endif;

return new WC_Appointments_Admin_CPT();
