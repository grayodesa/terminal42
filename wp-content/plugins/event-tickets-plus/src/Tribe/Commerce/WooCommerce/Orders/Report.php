<?php

class Tribe__Tickets_Plus__Commerce__WooCommerce__Orders__Report {
	/**
	 * Slug of the admin page for orders
	 * @var string
	 */
	public static $orders_slug = 'tickets-orders';

	/**
	 * Constructor!
	 */
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'orders_page_register' ) );
		add_filter( 'post_row_actions', array( $this, 'orders_row_action' ) );
	}

	/**
	 * Registers the Orders admin page
	 */
	public function orders_page_register() {
		// the orders table only works with WooCommerce
		if ( ! class_exists( 'WooCommerce' ) ) {
			return;
		}

		$this->orders_page = add_submenu_page(
			null, 'Order list', 'Order list', 'edit_posts', self::$orders_slug, array(
				$this,
				'orders_page_inside',
			)
		);

		add_action( 'admin_enqueue_scripts', array( Tribe__Tickets__Tickets_Handler::instance(), 'attendees_page_load_css_js' ) );
		add_action( 'admin_enqueue_scripts', array( Tribe__Tickets__Tickets_Handler::instance(), 'attendees_page_load_pointers' ) );
		add_action( "load-$this->orders_page", array( $this, 'orders_page_screen_setup' ) );

	}

	/**
	 * Adds the "orders" link in the admin list row actions for each event.
	 *
	 * @param $actions
	 *
	 * @return array
	 */
	public function orders_row_action( $actions ) {
		global $post;

		// the orders table only works with WooCommerce
		if ( ! class_exists( 'WooCommerce' ) ) {
			return $actions;
		}

		if ( ! in_array( $post->post_type, Tribe__Tickets__Main::instance()->post_types() ) ) {
			return $actions;
		}

		$url = add_query_arg(
			array(
				'post_type' => $post->post_type,
				'page'      => self::$orders_slug,
				'event_id'  => $post->ID,
			),
			admin_url( 'edit.php' )
		);

		$actions['tickets_orders'] = sprintf(
			'<a title="%s" href="%s">%s</a>',
			esc_html__( 'See purchases for this event', 'event-tickets' ),
			esc_url( $url ),
			esc_html__( 'Orders', 'event-tickets' )
		);

		return $actions;
	}

	/**
	 * Setups the Orders screen data.
	 */
	public function orders_page_screen_setup() {
		$this->orders_table = new Tribe__Tickets_Plus__Commerce__WooCommerce__Orders__Table;
		wp_enqueue_script( 'jquery-ui-dialog' );

		add_filter( 'admin_title', array( $this, 'orders_admin_title' ), 10, 2 );
	}

	/**
	 * Sets the browser title for the Orders admin page.
	 * Uses the event title.
	 *
	 * @param $admin_title
	 * @param $title
	 *
	 * @return string
	 */
	public function orders_admin_title( $admin_title, $title ) {
		if ( ! empty( $_GET['event_id'] ) ) {
			$event       = get_post( $_GET['event_id'] );
			$admin_title = sprintf( esc_html__( '%s - Order list', 'event-tickets' ), $event->post_title );
		}

		return $admin_title;
	}

	/**
	 * Renders the Orders page
	 */
	public function orders_page_inside() {
		$this->orders_table->prepare_items();

		$event_id = isset( $_GET['event_id'] ) ? intval( $_GET['event_id'] ) : 0;
		$event = get_post( $event_id );
		$tickets = Tribe__Tickets__Tickets::get_event_tickets( $event_id );

		/**
		 * Filters whether or not fees are being passed to the end user (purchaser)
		 *
		 * @var boolean $pass_fees Whether or not to pass fees to user
		 * @var int $event_id Event post ID
		 */
		Tribe__Tickets_Plus__Commerce__WooCommerce__Orders__Table::$pass_fees_to_user = apply_filters( 'tribe_tickets_pass_fees_to_user', true, $event_id );

		/**
		 * Filters the fee percentage to apply to a ticket/order
		 *
		 * @var float $fee_percent Fee percentage
		 */
		Tribe__Tickets_Plus__Commerce__WooCommerce__Orders__Table::$fee_percent = apply_filters( 'tribe_tickets_fee_percent', 0, $event_id );

		/**
		 * Filters the flat fee to apply to a ticket/order
		 *
		 * @var float $fee_flat Flat fee
		 */
		Tribe__Tickets_Plus__Commerce__WooCommerce__Orders__Table::$fee_flat = apply_filters( 'tribe_tickets_fee_flat', 0, $event_id );

		ob_start();
		$this->orders_table->display();
		$table = ob_get_clean();

		$organizer = get_user_by( 'id', $event->post_author );

		$event_revenue = Tribe__Tickets_Plus__Commerce__WooCommerce__Orders__Table::event_revenue( $event_id );
		$event_sales = Tribe__Tickets_Plus__Commerce__WooCommerce__Orders__Table::event_sales( $event_id );
		$event_fees = Tribe__Tickets_Plus__Commerce__WooCommerce__Orders__Table::event_fees( $event_id );

		$tickets_sold = array();
		$total_sold = 0;
		$total_pending = 0;
		$total_profit = 0;
		$total_completed = 0;

		foreach ( $tickets as $ticket ) {
			if ( empty( $tickets_sold[ $ticket->name ] ) ) {
				$tickets_sold[ $ticket->name ] = array(
					'ticket' => $ticket,
					'has_stock' => ! $ticket->stock(),
					'sku' => get_post_meta( $ticket->ID, '_sku', true ),
					'sold' => 0,
					'pending' => 0,
					'completed' => 0,
				);
			}
			$stock = $ticket->stock();
			$sold = $ticket->qty_sold();
			$cancelled = $ticket->qty_cancelled();

			$net_sold = $sold - $cancelled;
			if ( $net_sold < 0 ) {
				$net_sold = 0;
			}

			$tickets_sold[ $ticket->name ]['sold'] += $net_sold;
			$tickets_sold[ $ticket->name ]['pending'] += absint( $ticket->qty_pending() );
			$tickets_sold[ $ticket->name ]['completed'] += absint( $tickets_sold[ $ticket->name ]['sold'] ) - absint( $tickets_sold[ $ticket->name ]['pending'] );


			$total_sold += $net_sold;
			$total_pending += absint( $ticket->qty_pending() );
		}

		$total_completed += absint( $total_sold ) - absint( $total_pending );

		include Tribe__Tickets_Plus__Main::instance()->plugin_path . 'src/admin-views/woocommerce-orders.php';
	}
}
