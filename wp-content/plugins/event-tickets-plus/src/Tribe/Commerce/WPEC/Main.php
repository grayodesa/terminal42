<?php

if ( class_exists( 'Tribe__Tickets_Plus__Commerce__WPEC__Main' ) || ! class_exists( 'Tribe__Tickets__Tickets' ) ) {
	return;
}

class Tribe__Tickets_Plus__Commerce__WPEC__Main extends Tribe__Tickets_Plus__Tickets {
	/**
	 * Current version of this plugin
	 */
	const VERSION = '3.12a1';

	/**
	 * Min required The Events Calendar version
	 */
	const REQUIRED_TEC_VERSION = '3.11';

	/**
	 * Min required WPEC version
	 */
	const REQUIRED_WPEC_VERSION = '3.8.14';

	/**
	 * Name of the CPT that holds Attendees (tickets holders).
	 */
	const ATTENDEE_OBJECT = 'tribe_wpecticket';

	/**
	 * Meta key that relates Attendees and Products.
	 */
	const ATTENDEE_PRODUCT_KEY = '_tribe_wpecticket_product';

	/**
	 * Meta key that relates Attendees and Orders.
	 */
	const ATTENDEE_ORDER_KEY = '_tribe_wpecticket_order';

	/**
	 * Meta key that relates Attendees and Events.
	 */
	const ATTENDEE_EVENT_KEY = '_tribe_wpecticket_event';

	/**
	 * Name of the CPT that holds Attendees (tickets holders).
	 *
	 * @deprecated use of ATTENDEE_OBJECT class constant is preferred
	 *
	 * @var string
	 */
	public $attendee_object = 'tribe_wpecticket';

	/**
	 * Meta key that relates Products and Events
	 * @var string
	 */
	public $event_key = '_tribe_wpecticket_for_event';

	/**
	 * Meta key that stores if an attendee has checked in to an event
	 * @var string
	 */
	public $checkin_key = '_tribe_wpecticket_checkedin';

	/**
	 * Meta key that relates Attendees and Products.
	 *
	 * @deprecated use of ATTENDEE_PRODUCT_KEY class constant is preferred
	 *
	 * @var string
	 */
	public $atendee_product_key = '_tribe_wpecticket_product';

	/**
	 * Meta key that relates Attendees and Orders.
	 *
	 * @deprecated use of ATTENDEE_ORDER_KEY class constant is preferred
	 *
	 * @var string
	 */
	public $atendee_order_key = '_tribe_wpecticket_order';

	/**
	 * Meta key that relates Attendees and Events.
	 *
	 * @deprecated use of ATTENDEE_EVENT_KEY class constant is preferred
	 *
	 * @var string
	 */
	public $atendee_event_key = '_tribe_wpecticket_event';

	/**
	 * Meta key that holds the security code that's printed in the tickets
	 * @var string
	 */
	public $security_code = '_tribe_wpecticket_security_code';

	/**
	 * Meta key for the flag indicating if we already generated the attendes objects for an order
	 * @var string
	 */
	public $order_done = 'order_done';

	/**
	 * Meta key that holds the name of a ticket to be used in reports if the Product is deleted
	 * @var string
	 */
	public $deleted_product = '_tribe_deleted_product_name';

	/**
	 * Meta key that if this attendee wants to show on the attendee list
	 *
	 * @var string
	 */
	const ATTENDEE_OPTOUT_KEY = '_tribe_wpecticket_attendee_optout';

	/**
	 * Instance of Tribe__Tickets_Plus__Commerce__WPEC__Meta
	 */
	private static $meta;


	/**
	 * Class constructor
	 */
	public function __construct() {

		/* Set up some parent's vars */
		$this->pluginName = 'WPEC';
		$this->pluginSlug = 'wpec';
		$this->pluginPath = trailingslashit( EVENT_TICKETS_PLUS_DIR );
		$this->pluginDir  = trailingslashit( basename( $this->pluginPath ) );
		$this->pluginUrl  = trailingslashit( plugins_url( $this->pluginDir ) );

		parent::__construct();

		$this->hooks();
		$this->meta();
	}

	/**
	 * Registers all actions/filters
	 */
	public function hooks() {

		add_action( 'init', array( $this, 'process_front_end_tickets_form' ) );
		add_action( 'init', array( $this, 'register_types' ) );
		add_action( 'add_meta_boxes', array( $this, 'wpec_meta_box' ) );
		add_action( 'before_delete_post', array( $this, 'handle_delete_post' ) );
		add_action( 'wpsc_update_purchase_log_status', array( $this, 'generate_tickets' ), 10, 4 );
		add_action( 'wpectickets-send-tickets-email', array( $this, 'send_email' ), 10    );
		add_action( 'wpsc_purchlogitem_links_start', array( $this, 'add_resend_tickets_action' ) );

		add_filter( 'wpsc_cart_item_url', array( $this, 'hijack_ticket_link' ), 10, 4 );
		add_filter( 'tribe_tickets_settings_post_types', array( $this, 'exclude_product_post_type' ) );

		add_action( 'wpsc_set_cart_item', array( $this, 'set_attendee_optout_choice' ), 15, 4 );
	}

	/**
	 * Save the Attendee choice, the only important param is $new_cart_item
	 *
	 * We set the a Meta on the Cart Item
	 */
	public function set_attendee_optout_choice( $product_id, $parameters, $cart, $new_cart_item ) {

		// If this option is not here just drop
		if ( ! isset( $_POST['wpec_tickets_attendees_optout'] ) ) {
			return;
		}
		$optout = (bool) reset( $_POST['wpec_tickets_attendees_optout'] );

		// Set the Cart Item meta
		$new_cart_item->update_meta( self::ATTENDEE_OPTOUT_KEY, $optout );
	}

	/**
	 * Custom meta integration object accessor method
	 *
	 * @since 4.1
	 *
	 * @return Tribe__Tickets_Plus__Commerce__WPEC__Meta
	 */
	public function meta() {
		if ( ! self::$meta ) {
			self::$meta = new Tribe__Tickets_Plus__Commerce__WPEC__Meta;
		}

		return self::$meta;
	}

	/**
	 * When a user deletes a ticket (product) we want to store
	 * a copy of the product name, so we can show it in the
	 * attendee list for an event.
	 *
	 * @param $post_id
	 */
	public function handle_delete_post( $post_id ) {

		$post_to_delete = get_post( $post_id );

		// Bail if it's not a Product
		if ( get_post_type( $post_to_delete ) !== 'wpsc-product' )
			return;

		// Bail if the product is not a Ticket
		$event = get_post_meta( $post_id, $this->event_key, true );
		if ( $event === false )
			return;

		$attendees = $this->get_attendees( $event );

		foreach ( (array) $attendees as $attendee ) {
			if ( $attendee['product_id'] == $post_id ) {
				update_post_meta( $attendee['attendee_id'], $this->deleted_product, esc_html( $post_to_delete->post_title ) );
			}
		}

	}

	/**
	 * Register our custom post type
	 */
	public function register_types() {

		$args = array(
			'label'           => 'Tickets',
			'public'          => false,
			'show_ui'         => false,
			'show_in_menu'    => false,
			'query_var'       => false,
			'rewrite'         => false,
			'capability_type' => 'post',
			'has_archive'     => false,
			'hierarchical'    => true,
		);

		register_post_type( self::ATTENDEE_OBJECT, $args );
	}

	/**
	 * Generate and store all the attendees information for a new order.
	 *
	 * @param int $id
	 * @param string $status (unused)
	 * @param string $old_status (unused)
	 * @param $purchase_log
	 */
	public function generate_tickets( $id, $status, $old_status, $purchase_log ) {
		if ( empty( $purchase_log ) ) {
			return;
		}

		// Do not generate tickets until payment has been accepted/job dispatched
		$complete = $purchase_log->is_accepted_payment() || $purchase_log->is_job_dispatched();
		apply_filters( 'wpectickets_order_is_complete', $complete, $purchase_log );
		if ( ! $complete ) {
			return;
		}

		// Bail if we already generated the info for this order
		$done = wpsc_get_meta( $id, $this->order_done, 'tribe_tickets' );
		if ( ! empty( $done ) ) {
			return;
		}

		$has_tickets = false;

		// Get the items purchased in this order
		$order_items = $purchase_log->get_cart_contents();

		// Bail if the order is empty
		if ( empty( $order_items ) ) {
			return;
		}

		// Iterate over each product
		foreach ( (array) $order_items as $item ) {
			$order_attendee_id = 0;

			$product_id = $item->prodid;

			// Get the event this tickets is for
			$event_id = get_post_meta( $product_id, $this->event_key, true );
			$optout = (bool) wpsc_get_cart_item_meta( $item->purchaseid, self::ATTENDEE_OPTOUT_KEY, true );

			if ( ! empty( $event_id ) ) {

				$has_tickets = true;

				// Iterate over all the amount of tickets purchased (for this product)
				$quantity = intval( $item->quantity );
				for ( $i = 0; $i < $quantity; $i ++ ) {

					$attendee = array(
						'post_status' => 'publish',
						'post_title'  => $id . ' | ' . $item->name . ' | ' . ( $i + 1 ),
						'post_type'   => self::ATTENDEE_OBJECT,
						'ping_status' => 'closed',
					);

					// Insert individual ticket purchased
					$attendee_id = wp_insert_post( $attendee );

					update_post_meta( $attendee_id, self::ATTENDEE_PRODUCT_KEY, $product_id );
					update_post_meta( $attendee_id, self::ATTENDEE_ORDER_KEY, $id );
					update_post_meta( $attendee_id, self::ATTENDEE_EVENT_KEY, $event_id );
					update_post_meta( $attendee_id, $this->security_code, $this->generate_security_code( $id, $attendee_id ) );
					update_post_meta( $attendee_id, self::ATTENDEE_OPTOUT_KEY, $optout );

					/**
					 * WPEC specific action fired when a WPEC-driven attendee ticket for an event is generated
					 *
					 * @param $attendee_id ID of attendee ticket
					 * @param $event_id ID of event
					 * @param $order_id WPEC order ID
					 * @param $product_id WPEC product ID
					 */
					do_action( 'event_tickets_wpec_attendee_created', $attendee_id, $event_id, $product_id );

					/**
					 * Action fired when an attendee ticket is generated
					 *
					 * @param $attendee_id ID of attendee ticket
					 * @param $purchase_log WPEC purchase log object
					 * @param $product_id Product ID attendee is "purchasing"
					 * @param $order_attendee_id Attendee # for order
					 * @param $event_id The Event which this ticket belongs
					 */
					do_action( 'event_tickets_wpec_ticket_created', $attendee_id, $purchase_log, $product_id, $order_attendee_id, $event_id );

					$this->record_attendee_user_id( $attendee_id );
					$order_attendee_id++;
				}
			}
		}

		if ( $has_tickets ) {
			wpsc_update_meta( $id, $this->order_done, '1', 'tribe_tickets' );

			// Send the email to the user
			do_action( 'wpectickets-send-tickets-email', $purchase_log );
		}
	}

	public function send_email( $purchase_log_object ) {
		if ( ! is_object( $purchase_log_object ) )
			$purchase_log_object = new WPSC_Purchase_Log( $purchase_log_object );

		$notification = new Tribe__Tickets_Plus__Commerce__WPEC__Email( $purchase_log_object );
		$notification->send();

	}

		/**
	 * Generates the validation code that will be printed in the ticket.
	 * It purpose is to be used to validate the ticket at the door of an event.
	 *
	 * @param int $order_id
	 * @param int $attendee_id
	 *
	 * @return string
	 */
	private function generate_security_code( $order_id, $attendee_id ) {
		return substr( md5( $order_id . '_' . $attendee_id ), 0, 10 );
	}

	/**
	 * Saves a given ticket (WPEC product)
	 *
	 * @param int                     $event_id
	 * @param Tribe__Tickets__Ticket_Object $ticket
	 * @param array                   $raw_data
	 *
	 * @return bool
	 */
	public function save_ticket( $event_id, $ticket, $raw_data = array() ) {
		$save_type = 'update';

		if ( empty( $ticket->ID ) ) {
			$save_type = 'create';

			/* Create main product post */
			$args = array(
				'post_status'  => 'publish',
				'post_type'    => 'wpsc-product',
				'post_author'  => get_current_user_id(),
				'post_excerpt' => $ticket->description,
				'post_title'   => $ticket->name,
			);

			$ticket->ID = wp_insert_post( $args );

			update_post_meta( $ticket->ID, '_wpsc_currency', array() );
			update_post_meta( $ticket->ID, '_wpsc_is_donation', 0 );

			// Relate event <---> ticket
			add_post_meta( $ticket->ID, $this->event_key, $event_id );

		} else {
			$args = array(
				'ID'           => $ticket->ID,
				'post_excerpt' => $ticket->description,
				'post_title'   => $ticket->name,
			);

			$ticket->ID = wp_update_post( $args );
		}

		if ( ! $ticket->ID )
			return false;

		update_post_meta( $ticket->ID, '_wpsc_sku', $ticket->price );
		update_post_meta( $ticket->ID, '_wpsc_price', $ticket->price );

		if ( trim( $raw_data['ticket_wpec_stock'] ) !== '' ) {
			update_post_meta( $ticket->ID, '_wpsc_stock', $raw_data['ticket_wpec_stock'] );
		} else {
			update_post_meta( $ticket->ID, '_manage_stock', 'no' );
		}

		if ( isset( $raw_data['ticket_wpec_sku'] ) )
			update_post_meta( $ticket->ID, '_wpsc_sku', $raw_data['ticket_wpec_sku'] );

		if ( isset( $ticket->start_date ) ) {
			update_post_meta( $ticket->ID, '_ticket_start_date', $ticket->start_date );
		} else {
			delete_post_meta( $ticket->ID, '_ticket_start_date' );
		}

		if ( isset( $ticket->end_date ) ) {
			update_post_meta( $ticket->ID, '_ticket_end_date', $ticket->end_date );
		} else {
			delete_post_meta( $ticket->ID, '_ticket_end_date' );
		}

		/**
		 * Generic action fired after saving a ticket (by type)
		 *
		 * @param int Post ID of post the ticket is tied to
		 * @param Tribe__Tickets__Ticket_Object Ticket that was just saved
		 * @param array Ticket data
		 * @param string Commerce engine class
		 */
		do_action( 'event_tickets_after_' . $save_type . '_ticket', $event_id, $ticket, $raw_data, __CLASS__ );

		/**
		 * Generic action fired after saving a ticket
		 *
		 * @param int Post ID of post the ticket is tied to
		 * @param Tribe__Tickets__Ticket_Object Ticket that was just saved
		 * @param array Ticket data
		 * @param string Commerce engine class
		 */
		do_action( 'event_tickets_after_save_ticket', $event_id, $ticket, $raw_data, __CLASS__ );

		return true;
	}

	/**
	 * Deletes a ticket.
	 *
	 * Note that the total sales/purchases figure maintained by WPEC is not adjusted on the
	 * basis that deleting an attendee does not mean the sale didn't go through; this is
	 * a change in behaviour from the 4.0.x releases.
	 *
	 * @param $event_id
	 * @param $ticket_id
	 *
	 * @return bool
	 */
	public function delete_ticket( $event_id, $ticket_id ) {
		// Ensure we know the event, order and product IDs (the event ID may not have been passed in)
		if ( empty( $event_id ) ) $event_id = get_post_meta( $ticket_id, self::ATTENDEE_EVENT_KEY, true );
		$order_id = get_post_meta( $ticket_id, self::ATTENDEE_ORDER_KEY, true );
		$product_id = get_post_meta( $ticket_id, self::ATTENDEE_PRODUCT_KEY, true );

		// Try to kill the actual ticket/attendee post
		$delete = wp_delete_post( $ticket_id, true );
		if ( is_wp_error( $delete ) ) return false;

		/* Class exists check exists to avoid bumping Tribe__Tickets_Plus__Main::REQUIRED_TICKETS_VERSION
		 * during a minor release; as soon as we are able to do that though we can remove this safeguard.
		 *
		 * @todo remove class_exists() check once REQUIRED_TICKETS_VERSION >= 4.2
		 */
		if ( class_exists( 'Tribe__Tickets__Attendance' ) ) {
			Tribe__Tickets__Attendance::instance( $event_id )->increment_deleted_attendees_count();
		}

		do_action( 'eddtickets_ticket_deleted', $ticket_id, $event_id, $product_id, $order_id );
		return true;
	}

	/**
	 * Reduce the value of the quanitity field in the cart_contents table for the
	 * specified entry.
	 *
	 * @param $order_id
	 * @param $product_id
	 */
	protected function reduce_sales_qty( $order_id, $product_id ) {
		global $wpdb;
		$cart_table = $wpdb->prefix . 'wpsc_cart_contents';
		$sql = "UPDATE $cart_table SET quantity = ( quantity - 1 ) WHERE purchaseid = %d AND prodid = %d;";
		$wpdb->query( $wpdb->prepare( $sql, $order_id, $product_id ) );
	}

	/**
	 * Returns all the tickets for an event
	 *
	 * @param int $event_id
	 *
	 * @return array
	 */
	protected function get_tickets( $event_id ) {

		$ticket_ids = $this->_get_tickets_ids( $event_id );

		if ( ! $ticket_ids )
			return array();

		$tickets = array();

		foreach ( $ticket_ids as $post ) {
			$tickets[] = $this->get_ticket( $event_id, $post );
		}

		return $tickets;
	}

	/**
	 * Replaces the link to the product with a link to the Event in the
	 * order confirmation page.
	 *
	 * @param $url
	 * @param $product_id
	 *
	 * @return string
	 */
	public function hijack_ticket_link( $url, $product_id ) {
		$event = get_post_meta( $product_id, $this->event_key, true );

		if ( ! empty( $event ) )
			$url = tribe_get_event_link( $event );

		return $url;
	}

	/**
	 * Shows the tickets form in the front end
	 *
	 * @param $content
	 * @return void
	 */
	public function front_end_tickets_form( $content ) {
		$post = $GLOBALS['post'];

		if ( ! empty( $post->post_parent ) ) {
			$post = get_post( $post->post_parent );
		}

		$tickets = self::get_tickets( $post->ID );

		if ( empty( $tickets ) )
			return;

		$must_login = ! is_user_logged_in() && $this->login_required();
		include $this->getTemplateHierarchy( 'wpectickets/tickets' );

	}

	/**
	 * Grabs the submitted front end tickets form and adds the products
	 * to the cart
	 */
	public function process_front_end_tickets_form() {

		if ( empty( $_POST['wpec_tickets_quantity'] ) )
			return;

		if ( empty( $_POST['wpec_tickets_product_id'] ) )
			return;

		$products   = (array) $_POST['wpec_tickets_product_id'];
		$quantities = (array) $_POST['wpec_tickets_quantity'];
		$shortfall  = 0;

		global $wpsc_cart;

		// We're adding new products, not updating
		foreach ( $products as $product_key => $product_id ) {

			if ( absint( $quantities[ $product_key ] ) == 0 )
				continue;

			$parameters['quantity']         = absint( $quantities[ $product_key ] );
			$parameters['variation_values'] = '';
			$parameters['is_customisable']  = false;
			$parameters['custom_message']   = ''; // We *must* set this to avoid a db error @see wpsc_cart_item::save_to_db()

			// Check stock levels
			$stock_remaining = wpsc_get_remaining_quantity( $product_id );
			$shortfall = -1 * ( $stock_remaining - $parameters['quantity'] );

			// Insufficient stock, but enough to partially fulfill the request?
			if ( $shortfall > 0 && $stock_remaining > 0 ) {
				$parameters['quantity'] = $stock_remaining;
			}

			$wpsc_cart->set_item( (int) $product_id, $parameters );
		}

		// If there was a shortfall we need to communicate this to the user
		if ( $shortfall > 0 ) {
			add_action( 'wpsc_before_shopping_cart_page', array( $this, 'fulfilment_error' ) );
		}
	}

	/**
	 * Displays an error message to alert users to a possible shortfall in ticket
	 * availability.
	 */
	public function fulfilment_error() {
		$error = __( "We're sorry but there was insufficient inventory to completely fulfill your order! We have added as many tickets as we could, but we recommend reviewing your order (below) in case you need to make some further adjustments.", 'event-tickets-plus' );
		$error = apply_filters( 'wpecticket_fulfilment_error_message', $error );
		echo '<p class="wpec_tickets error fulfilment_error">' . esc_html( $error ) . '</p>';
	}

	/**
	 * Gets an individual ticket
	 *
	 * @param $event_id
	 * @param $ticket_id
	 *
	 * @return null|Tribe__Tickets__Ticket_Object
	 */
	public function get_ticket( $event_id, $ticket_id ) {
		global $wpdb;

		$return       = new Tribe__Tickets__Ticket_Object();
		$product_data = get_post( $ticket_id );

		$product      = new WPSC_Product( $ticket_id );

		$return->description    = $product_data->post_excerpt;
		$return->frontend_link  = get_permalink( $ticket_id );
		$return->ID             = $ticket_id;
		$return->name           = $product_data->post_title;
		$return->on_sale        = (bool) $product->is_on_sale;
		$return->regular_price  = $product->price;
		$return->price          = $return->on_sale ? $product->sale_price : $product->price;
		$return->provider_class = get_class( $this );
		$return->admin_link     = admin_url( sprintf( get_post_type_object( $product_data->post_type )->_edit_link . '&action=edit', $ticket_id ) );
		$return->start_date     = get_post_meta( $ticket_id, '_ticket_start_date', true );
		$return->end_date       = get_post_meta( $ticket_id, '_ticket_end_date', true );

		$cart_table = $cart_table = $wpdb->prefix . 'wpsc_cart_contents';
		$qty_sql          = $wpdb->prepare( "SELECT sum(quantity) FROM $cart_table WHERE prodid=%d", $ticket_id );
		$qty              = max( (int) $wpdb->get_var( $qty_sql ), 0 );
		$pending = $qty ? $this->count_incomplete_order_items( $ticket_id ) : 0;

		$manage_stock = get_post_meta( $ticket_id, '_manage_stock', true );
		if ( 'no' === $manage_stock ) {
			$return->stock = null;
		}

		$return->manage_stock( ! is_null( $product->stock ) );
		$return->stock( is_null( $product->stock ) ? Tribe__Tickets__Ticket_Object::UNLIMITED_STOCK : $product->stock );
		$return->qty_sold( $qty );
		$return->qty_pending( $pending );

		return $return;
	}

	/**
	 * Accepts a reference to a product (either an object or a numeric ID) and
	 * tests to see if it functions as a ticket: if so, the corresponding event
	 * object is returned. If not, boolean false is returned.
	 *
	 * @param $ticket_product
	 *
	 * @return bool|WP_Post
	 */
	public function get_event_for_ticket( $ticket_product ) {
		if ( is_object( $ticket_product ) && isset( $ticket_product->ID ) ) {
			$ticket_product = $ticket_product->ID;
		}

		if ( null === ( $product = get_post( $ticket_product ) ) ) {
			return false;
		}

		if ( '' === ( $event = get_post_meta( $ticket_product, $this->event_key, true ) ) ) {
			return false;
		}

		if ( in_array( get_post_type( $event ), Tribe__Tickets__Main::instance()->post_types() ) ) {
			return get_post( $event );
		}

		return false;
	}

	/**
	 * Get all the attendees for an event. It returns an array with the
	 * following fields:
	 *  'order_id'
	 *  'order_status'
	 *  'purchaser_name'
	 *  'purchaser_email'
	 *  'ticket'
	 *  'attendee_id'
	 *  'security'
	 *  'product_id'
	 *  'check_in'
	 *  'provider'
	 *
	 * @param $event_id
	 *
	 * @return array
	 */
	protected function get_attendees( $event_id ) {
		global $wpdb;

		$args = array(
			'posts_per_page' => - 1,
			'post_type'      => self::ATTENDEE_OBJECT,
			'meta_key'       => self::ATTENDEE_EVENT_KEY,
			'meta_value'     => $event_id,
			'orderby'        => 'ID',
			'order'          => 'DESC',
		);

		$attendees = array();
		$attendees_query = new WP_Query( $args );

		if ( ! $attendees_query->have_posts() ) {
			return array();
		}

		foreach ( $attendees_query->posts as $attendee ) {
			$order_id   = get_post_meta( $attendee->ID, self::ATTENDEE_ORDER_KEY, true );
			$checkin    = get_post_meta( $attendee->ID, $this->checkin_key, true );
			$security   = get_post_meta( $attendee->ID, $this->security_code, true );
			$optout     = (bool) get_post_meta( $attendee->ID, self::ATTENDEE_OPTOUT_KEY, true );
			$product_id = get_post_meta( $attendee->ID, self::ATTENDEE_PRODUCT_KEY, true );
			$user_id    = get_post_meta( $attendee->ID, self::ATTENDEE_USER_ID, true );

			if ( empty( $product_id ) ) {
				continue;
			}

			$product = get_post( $product_id );
			$product_title = ( ! empty( $product ) ) ? $product->post_title : get_post_meta( $attendee->ID, $this->deleted_product, true ) . ' ' . __( '(deleted)', 'event-tickets-plus' );

			// Add the Attendee Data to the Order data
			$attendee_data = array_merge(
				$this->get_order_data( $order_id ),
				array(
					'ticket'      => $product_title,
					'attendee_id' => $attendee->ID,
					'security'    => $security,
					'optout'      => $optout,
					'product_id'  => $product_id,
					'check_in'    => $checkin,
					'user_id'     => $user_id,
				)
			);

			/**
			 * Allow users to filter the Attendee Data
			 *
			 * @param array An associative array with the Information of the Attendee
			 * @param string What Provider is been used
			 * @param WP_Post Attendee Object
			 * @param int Event ID
			 *
			 */
			$attendee_data = apply_filters( 'tribe_tickets_attendee_data', $attendee_data, 'wpec', $attendee, $event_id );

			$attendees[] = $attendee_data;
		}

		return $attendees;

	}

	/**
	 * Retreive only order related information
	 *
	 *     order_id
	 *     order_id_link
	 *     order_id_link_src
	 *     order_status
	 *     order_status_label
	 *     order_warning
	 *     purchaser_name
	 *     purchaser_email
	 *     provider
	 *     provider_slug
	 *
	 * @param int $order_id
	 * @return array
	 */
	public function get_order_data( $order_id ) {
		$order_warning = false;

		// Obtain order information
		list( $email, $name ) = $this->customer_details( $order_id );
		list( $order_status, $order_status_label ) = $this->order_status( $order_id );

		// Warn where the transaction failed
		if ( 'incomplete_sale' === $order_status || 'declined_payment' === $order_status )
			$order_warning = true;

		// Warn if the order has been trashed
		if ( ! empty( $order_status ) && get_post_status( $order_id ) == 'trash' ) {
			$order_status = sprintf( __( 'In trash (was %s)', 'event-tickets-plus' ), $order_status );
			$order_warning = true;
		}

		// Warn if the order was outright deleted
		if ( empty( $order_status ) && ! get_post( $order_id ) ) {
			$order_status = __( 'Deleted', 'event-tickets-plus' );
			$order_warning = true;
		}

		$admin_url  = admin_url( sprintf( 'index.php?page=wpsc-purchase-logs&c=item_details&id=%d', $order_id ) );
		$admin_link = sprintf( '<a href="%s">%d</a>', esc_url( $admin_url ), $order_id );

		$data = array(
			'order_id'           => $order_id,
			'order_id_link'      => $admin_link,
			'order_id_link_src'  => $admin_url,
			'order_status'       => $order_status,
			'order_status_label' => $order_status_label,
			'order_warning'      => $order_warning,
			'purchaser_name'     => $name,
			'purchaser_email'    => $email,
			'provider'           => __CLASS__,
			'provider_slug'      => 'wpec',
			'purchase_time'      => get_post_time( Tribe__Date_Utils::DBDATETIMEFORMAT, false, $order_id ),
		);

		/**
		 * Allow users to filter the Order Data
		 *
		 * @param array An associative array with the Information of the Order
		 * @param string What Provider is been used
		 * @param int Order ID
		 *
		 */
		$data = apply_filters( 'tribe_tickets_order_data', $data, 'wpec', $order_id );

		return $data;
	}

	/**
	 * Returns an order status array for the specified order, containing the internal order status
	 * name at index 0 and the localized human friend label at index 1, ie:
	 *
	 *     [ 0 => 'internal_status',
	 *       1 => 'Human Friendly Status' ]
	 *
	 * @param $order_id
	 * @return array
	 */
	protected function order_status( $order_id ) {
		global $wpdb, $wpsc_purchlog_statuses;
		$state = 'unknown';
		$label = __( 'Unknown', 'event-tickets-plus' );

		$statussql    = $wpdb->prepare( 'Select processed AS status from ' . WPSC_TABLE_PURCHASE_LOGS . ' where id=%s', $order_id );
		$status       = $wpdb->get_results( $statussql );

		if ( ! empty( $status ) && isset( $wpsc_purchlog_statuses ) ) {
			$state = $wpsc_purchlog_statuses[ $status[0]->status - 1 ]['internalname'];
			$label = $wpsc_purchlog_statuses[ $status[0]->status - 1 ]['label'];
		}

		return array( $state, $label );
	}

	/**
	 * Returns the customer details (email, name) as an array in the form:
	 *
	 *     [ 0 => 'customer@address.com',
	 *       1 => 'Cornelius Weatherbottom' ]
	 *
	 * @param $order_id
	 * @return array
	 */
	protected function customer_details( $order_id ) {
		global $wpdb;

		$usersql    = $wpdb->prepare( 'SELECT DISTINCT `' . WPSC_TABLE_SUBMITTED_FORM_DATA . '`.value, `' . WPSC_TABLE_CHECKOUT_FORMS . '`.* FROM `' . WPSC_TABLE_CHECKOUT_FORMS . '` LEFT JOIN `' . WPSC_TABLE_SUBMITTED_FORM_DATA . '` ON `' . WPSC_TABLE_CHECKOUT_FORMS . '`.id = `' . WPSC_TABLE_SUBMITTED_FORM_DATA . '`.`form_id` WHERE `' . WPSC_TABLE_SUBMITTED_FORM_DATA . "`.log_id=%d and unique_name in ('billingfirstname','billinglastname','billingemail') ORDER BY `" . WPSC_TABLE_CHECKOUT_FORMS . '`.`unique_name`', $order_id );
		$formfields = $wpdb->get_results( $usersql );

		$email = ( ! empty( $formfields[0]->value ) ) ? $formfields[0]->value : '';

		$name    = array();
		$name[0] = ( ! empty( $formfields[1]->value ) ) ? $formfields[1]->value : '';
		$name[1] = ( ! empty( $formfields[2]->value ) ) ? $formfields[2]->value : '';
		$name    = join( $name, ' ' );

		return array( $email, $name );
	}

	/**
	 * Marks an attendee as checked in for an event
	 *
	 * Because we must still support our legacy ticket plugins, we cannot change the abstract
	 * checkin() method's signature. However, the QR checkin process needs to move forward
	 * so we get around that problem by leveraging func_get_arg() to pass a second argument.
	 *
	 * It is hacky, but we'll aim to resolve this issue when we end-of-life our legacy ticket plugins
	 * OR write around it in a future major release
	 *
	 * @param $attendee_id
	 * @param $qr true if from QR checkin process (NOTE: this is a param-less parameter for backward compatibility)
	 *
	 * @return bool
	 */
	public function checkin( $attendee_id ) {
		$qr = null;

		update_post_meta( $attendee_id, $this->checkin_key, 1 );

		if ( func_num_args() > 1 && $qr = func_get_arg( 1 ) ) {
			update_post_meta( $attendee_id, '_tribe_qr_status', 1 );
		}

		/**
		 * Fires a checkin action
		 *
		 * @param int $attendee_id
		 * @param bool|null $qr
		 */
		do_action( 'wpectickets_checkin', $attendee_id, $qr );

		return true;
	}

	/**
	 * Marks an attendee as not checked in for an event
	 *
	 * @param $attendee_id
	 *
	 * @return bool
	 */
	public function uncheckin( $attendee_id ) {
		delete_post_meta( $attendee_id, $this->checkin_key );
		delete_post_meta( $attendee_id, '_tribe_qr_status' );
		do_action( 'wpectickets_uncheckin', $attendee_id );

		return true;
	}

	/**
	 * Add the extra options in the admin's new/edit ticket metabox
	 *
	 * @param $event_id
	 * @param $ticket_id
	 * @return void
	 */
	public function do_metabox_advanced_options( $event_id, $ticket_id ) {

		$url = $stock = $sku = '';

		if ( ! empty( $ticket_id ) ) {
			$ticket = $this->get_ticket( $event_id, $ticket_id );
			if ( ! empty( $ticket ) ) {
				$stock = $ticket->managing_stock() ? $ticket->stock() : '';
				$sku   = get_post_meta( $ticket_id, '_wpsc_sku', true );
			}
		}

		include $this->pluginPath . 'src/admin-views/wpec-metabox-advanced.php';
	}

	/**
	 * WPEC doesn't support reports
	 * @param $event_id
	 *
	 * @return string
	 */
	public function get_event_reports_link( $event_id ) {
		return null;

	}

	/**
	 * WPEC doesn't support reports
	 * @param $event_id
	 * @param $ticket_id
	 *
	 * @return string
	 */
	public function get_ticket_reports_link( $event_id, $ticket_id ) {
		return null;

	}

	/**
	 * Registers a metabox in the WPEC product edit screen
	 * with a link back to the product related Event.
	 *
	 */
	public function wpec_meta_box() {
		$event_id = get_post_meta( get_the_ID(), $this->event_key, true );

		if ( ! empty( $event_id ) )
			add_meta_box( 'wpectickets-linkback', 'Event', array( $this, 'wpec_meta_box_inside' ), 'wpsc-product', 'normal', 'high' );

	}

	/**
	 * Contents for the metabox in the WPEC product edit screen
	 * with a link back to the product related Event.
	 */
	public function wpec_meta_box_inside() {

		$event_id = get_post_meta( get_the_ID(), $this->event_key, true );
		if ( ! empty( $event_id ) )
			echo sprintf( '%s <a href="%s">%s</a>', esc_html__( 'This is a ticket for the event:', 'event-tickets-plus' ), esc_url( get_edit_post_link( $event_id ) ), esc_html( get_the_title( $event_id ) ) );

	}

	/**
	 * Get's the product price html
	 *
	 * @param int|object $product
	 *
	 * @return string
	 */
	public function get_price_html( $product ) {
		if ( is_numeric( $product ) ) {
			$product = new WPSC_Product( $product );
		}

		if ( ! $product instanceof WPSC_Product ) {
			return null;
		}

		return $product->is_on_sale ? $product->sale_price : $product->price;
	}

	private function _get_tickets_ids( $event_id ) {

		if ( is_object( $event_id ) )
			$event_id = $event_id->ID;

		$query = new WP_Query( array(
			'post_type'      => 'wpsc-product',
			'meta_key'       => $this->event_key,
			'meta_value'     => $event_id,
			'meta_compare'   => '=',
			'posts_per_page' => - 1,
			'fields'         => 'ids',
			'post_status'    => 'publish',
		) );

		return $query->posts;
	}

	/**
	 * Adds a link to resend the tickets to the customer
	 * in the order edit screen.
	 */
	public function add_resend_tickets_action(  ) {

		if ( empty( $_GET['id'] ) )
			return;

		$order = $_GET['id'];

		$has_tickets = wpsc_get_meta( $order, $this->order_done, 'tribe_tickets' );

		if ( ! $has_tickets )
			return;

		$url = add_query_arg( 'tribe_resend_ticket', 1 );
		echo sprintf( "<img src='%s'>&nbsp;", esc_url( $this->pluginUrl . 'src/resources/images/ticket.png' ) );
		echo sprintf( "<a href='%s'>%s</a>", esc_url( $url ), esc_html__( 'Resend Tickets', 'event-tickets-plus' ) );


		if ( ! empty( $_GET['tribe_resend_ticket'] ) ) {
			$this->send_email( new WPSC_Purchase_Log( $order, 'id' ) );
			echo esc_html__( '&nbsp; [Sent]', 'event-tickets-plus' );
		}

		echo '<br/><br/>';
	}


	/**
	 * Excludes WPEC product post types from the list of supported post types that Tickets can be attached to
	 *
	 * @since 4.0.5
	 *
	 * @param array $post_types Array of supported post types
	 *
	 * @return array
	 */
	public function exclude_product_post_type( $post_types ) {
		if ( isset( $post_types['wpsc-product'] ) ) {
			unset( $post_types['wpsc-product'] );
		}

		return $post_types;
	}

	/********** SINGLETON FUNCTIONS **********/

	/**
	 * Instance of this class for use as singleton
	 */
	private static $instance;

	/**
	 * Creates the instance of the class
	 *
	 * @static
	 * @return void
	 */
	public static function init() {
		self::$instance = self::get_instance();
	}

	/**
	 * Get (and instantiate, if necessary) the instance of the class
	 *
	 * @static
	 * @return Tribe__Tickets_Plus__Commerce__WPEC__Main
	 */
	public static function get_instance() {
		if ( ! self::$instance instanceof self ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	private function count_incomplete_order_items( $ticket_id ) {
		global $wpdb;
		$cart_contents_table    = $wpdb->prefix . 'wpsc_cart_contents';
		$purchase_logs_table    = $wpdb->prefix . 'wpsc_purchase_logs';
		$pending_order_statuses = implode( ',', $this->get_wpec_pending_order_statuses() );
		$q                      = sprintf( 'SELECT sum(cc.quantity) FROM %s cc JOIN %s pl ON pl.id = cc.purchaseid WHERE cc.prodid = %d AND pl.processed IN (%s);', $cart_contents_table, $purchase_logs_table, $ticket_id, $pending_order_statuses );
		$count                  = $wpdb->get_col( $q );

		return ( is_array( $count ) && count( $count ) == 1 ) ? $count[0] : 0;
	}

	private function get_wpec_pending_order_statuses() {
		return array( 1, 2 );
	}

}
