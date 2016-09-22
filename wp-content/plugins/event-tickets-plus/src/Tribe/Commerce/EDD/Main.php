<?php

if ( class_exists( 'Tribe__Tickets_Plus__Commerce__EDD__Main' ) || ! class_exists( 'Tribe__Tickets__Tickets' ) ) {
	return;
}

class Tribe__Tickets_Plus__Commerce__EDD__Main extends Tribe__Tickets_Plus__Tickets {
	/**
	 * Value indicating there is no limit on the number of tickets that can be sold.
	 */
	const UNLIMITED = '_unlimited';

	/**
	 * Current version of this plugin
	 */
	const VERSION = '3.12a1';
	/**
	 * Min required The Events Calendar version
	 */
	const REQUIRED_TEC_VERSION = '3.11';
	/**
	 * Min required Easy Digital Downloads version
	 */
	const REQUIRED_EDD_VERSION = '1.8.3';

	/**
	 * Label used to identify the false "downloadable product" used to
	 * facilitate printing tickets - this will be used as the download
	 * file URL.
	 */
	const TICKET_DOWNLOAD = 'tribe://edd.tickets/print';

	/**
	 * In previous versions of EDD Tickets the printable ticket download
	 * was identifiable by virtue of having an empty string as the file
	 * name.
	 *
	 * We've moved to a more reliable approach, but we still need to
	 * be cognizant of tickets set up with older versions of the plugin.
	 */
	const LEGACY_TICKET_DOWNLOAD = '';

	/**
	 * Name of the CPT that holds Attendees (tickets holders).
	 */
	const ATTENDEE_OBJECT = 'tribe_eddticket';

	/**
	 * Meta key that relates Attendees and Orders.
	 */
	const ATTENDEE_ORDER_KEY = '_tribe_eddticket_order';

	/**
	 * Meta key that relates Attendees and Events.
	 */
	const ATTENDEE_EVENT_KEY = '_tribe_eddticket_event';

	/**
	 * Meta key that relates Attendees and Products.
	 */
	const ATTENDEE_PRODUCT_KEY = '_tribe_eddticket_product';

	/**
	 * Name of the CPT that holds Attendees (tickets holders).
	 *
	 * @deprecated use of the ATTENDEE_OBJECT class constant is preferred
	 *
	 * @var string
	 */
	public static $attendee_object = 'tribe_eddticket';

	/**
	 * Meta key that relates Products and Events
	 * @var string
	 */
	public static $event_key = '_tribe_eddticket_for_event';

	/**
	 * Meta key that stores if an attendee has checked in to an event
	 * @var string
	 */
	public $checkin_key = '_tribe_eddticket_checkedin';

	/**
	 * Meta key that relates Attendees and Orders.
	 *
	 * @deprecated use of the ATTENDEE_OBJECT class constant is preferred
	 *
	 * @var string
	 */
	public static $attendee_order_key = '_tribe_eddticket_order';

	/**
	 * Meta key that relates Attendees and Events
	 *
	 * @deprecated use of the ATTENDEE_OBJECT class constant is preferred
	 *
	 * @var string
	 */
	public static $attendee_event_key = '_tribe_eddticket_event';

	/**
	 * Meta key that holds the security code that's printed in the tickets
	 * @var string
	 */
	public static $security_code = '_tribe_eddticket_security_code';

	/**
	 * Meta key that holds if an order has tickets (for performance)
	 * @var string
	 */
	public static $order_has_tickets = '_tribe_has_tickets';

	/**
	 * Meta key that holds the name of a ticket to be used in reports if the Product is deleted
	 * @var string
	 */
	public $deleted_product = '_tribe_deleted_product_name';

	/**
	 * Meta key that holds if the attendee has opted out of the front-end listing
	 * @var string
	 */
	const ATTENDEE_OPTOUT_KEY = '_tribe_eddticket_attendee_optout';

	/**
	 * Holds an instance of the Tribe__Tickets_Plus__Commerce__EDD__Email class
	 * @var Tribe__Tickets_Plus__Commerce__EDD__Email
	 */
	private $mailer = null;

	/**
	 * Helps to manage stock for EDD Tickets sales.
	 *
	 * @var Tribe__Tickets_Plus__Commerce__EDD__Stock_Control
	 */
	protected $stock_control;

	/**
	 * @var Tribe__Tickets_Plus__Commerce__EDD__Global_Stock
	 */
	protected static $global_stock;

	/**
	 * Instance of Tribe__Tickets_Plus__Commerce__EDD__Meta
	 */
	private static $meta;

	/**
	 * Class constructor
	 */
	public function __construct() {

		/* Set up some parent's vars */
		$this->pluginName = __( 'Easy Digital Downloads', 'edd' );
		$this->pluginSlug = 'eddtickets';
		$this->pluginPath = trailingslashit( EVENT_TICKETS_PLUS_DIR );
		$this->pluginDir  = trailingslashit( basename( $this->pluginPath ) );
		$this->pluginUrl  = trailingslashit( plugins_url( $this->pluginDir ) );
		$this->mailer     = new Tribe__Tickets_Plus__Commerce__EDD__Email;
		$this->stock_control = new Tribe__Tickets_Plus__Commerce__EDD__Stock_Control();

		parent::__construct();

		$this->hooks();
		$this->meta();
		$this->global_stock();
	}

	/**
	 * Registers all actions/filters
	 */
	public function hooks() {

		add_action( 'init', array( $this, 'register_eddtickets_type' ), 1 );
		add_action( 'wp_loaded', array( $this, 'process_front_end_tickets_form' ), 50 );
		add_action( 'add_meta_boxes', array( $this, 'edd_meta_box' ) );
		add_action( 'before_delete_post', array( $this, 'handle_delete_post' ) );
		add_action( 'edd_complete_purchase', array( $this, 'generate_tickets' ), 12 );
		add_action( 'pre_get_posts', array( $this, 'hide_tickets_from_shop' ) );
		add_action( 'pre_get_posts', array( $this, 'filter_ticket_reports' ) );
		add_action( 'edd_cart_footer_buttons', '__return_true' );
		add_action( 'edd_before_checkout_cart', array( $this, 'pre_checkout_errors' ) );
		add_action( 'edd_checkout_error_checks', array( $this, 'checkout_errors' ) );
		add_action( 'template_redirect', array( $this, 'render_ticket_print_view' ), 10, 2 );

		add_filter( 'edd_url_token_allowed_params', array( $this, 'add_allowed_param' ) );
		add_filter( 'edd_purchase_receipt', array( $this, 'add_tickets_msg_to_email' ), 10, 3 );
		add_filter( 'post_type_link', array( $this, 'hijack_ticket_link' ), 10, 4 );
		add_filter( 'edd_item_quantities_enabled', '__return_true' );
		add_filter( 'edd_download_files', array( $this, 'ticket_downloads' ), 10, 2 );
		add_filter( 'edd_download_file_url_args', array( $this, 'print_ticket_url' ), 10 );

		add_filter( 'edd_add_to_cart_item', array( $this, 'set_attendee_optout_choice' ), 10 );
		add_filter( 'tribe_tickets_settings_post_types', array( $this, 'exclude_product_post_type' ) );
	}

	/**
	 * Provides a copy of the global stock integration object.
	 *
	 * @since 4.1
	 *
	 * @return Tribe__Tickets_Plus__Commerce__EDD__Global_Stock
	 */
	public function global_stock() {
		if ( ! self::$global_stock ) {
			self::$global_stock = new Tribe__Tickets_Plus__Commerce__EDD__Global_Stock;
		}

		return self::$global_stock;
	}

	/**
	 * Indicates if global stock support is enabled (for Easy Digital Downloads the
	 * default is true).
	 *
	 * @return bool
	 */
	public function supports_global_stock() {
		/**
		 * Allows the declaration of global stock support for Easy Digital Downloads
		 * tickets to be overridden.
		 *
		 * @param bool $enable_global_stock_support
		 */
		return (bool) apply_filters( 'tribe_tickets_edd_enable_global_stock', true );
	}

	/**
	 * Configure the option optout from attendees
	 *
	 * @param array $item Cart Item
	 */
	public function set_attendee_optout_choice( $item ) {
		$is_ticket = get_post_meta( $item['id'], self::$event_key, true );

		if ( ! $is_ticket ) {
			return $item;
		}

		$optout = isset( $_POST[ 'optout_' . $item['id'] ] ) ? (bool) $_POST[ 'optout_' . $item['id'] ] : false;
		$item['options'][ self::ATTENDEE_OPTOUT_KEY ] = $optout;

		return $item;
	}

	/**
	 * Custom meta integration object accessor method
	 *
	 * @since 4.1
	 *
	 * @return Tribe__Tickets_Plus__Commerce__EDD__Meta
	 */
	public function meta() {
		if ( ! self::$meta ) {
			self::$meta = new Tribe__Tickets_Plus__Commerce__EDD__Meta;
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
		if ( get_post_type( $post_to_delete ) !== 'download' ) {
			return;
		}

		// Bail if the product is not a Ticket
		$event = get_post_meta( $post_id, self::$event_key, true );
		if ( $event === false ) {
			return;
		}

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
	public function register_eddtickets_type() {

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
	 * @param $order_id
	 */
	public function generate_tickets( $order_id ) {
		// Bail if we already generated the info for this order
		$done = get_post_meta( $order_id, self::$order_has_tickets, true );
		if ( ! empty( $done ) ) {
			return;
		}

		$has_tickets = false;
		// Get the items purchased in this order


		$order_items = edd_get_payment_meta_cart_details( $order_id );

		// Bail if the order is empty
		if ( empty( $order_items ) ) {
			return;
		}

		// Iterate over each product
		foreach ( (array) $order_items as $item ) {
			$order_attendee_id = 0;

			$product_id = isset( $item['id'] ) ? $item['id'] : false;
			$optout = (bool) isset( $item['item_number']['options'][ self::ATTENDEE_OPTOUT_KEY ] ) ? $item['item_number']['options'][ self::ATTENDEE_OPTOUT_KEY ] : false;

			// Get the event this tickets is for
			$event_id = get_post_meta( $product_id, self::$event_key, true );

			if ( ! empty( $event_id ) ) {

				$has_tickets = true;

				// Iterate over all the amount of tickets purchased (for this product)
				$quantity = intval( $item['quantity'] );
				for ( $i = 0; $i < $quantity; $i ++ ) {

					$attendee = array(
						'post_status' => 'publish',
						'post_title'  => $order_id . ' | ' . $item['name'] . ' | ' . ( $i + 1 ),
						'post_type'   => self::ATTENDEE_OBJECT,
						'ping_status' => 'closed',
					);

					// Insert individual ticket purchased
					$attendee_id = wp_insert_post( $attendee );

					update_post_meta( $attendee_id, self::ATTENDEE_PRODUCT_KEY, $product_id );
					update_post_meta( $attendee_id, self::ATTENDEE_ORDER_KEY, $order_id );
					update_post_meta( $attendee_id, self::ATTENDEE_EVENT_KEY, $event_id );
					update_post_meta( $attendee_id, self::$security_code, $this->generate_security_code( $order_id, $attendee_id ) );
					update_post_meta( $attendee_id, self::ATTENDEE_OPTOUT_KEY, $optout );

					/**
					 * Easy Digital Downloads specific action fired when an EDD-driven attendee ticket for an event is generated
					 *
					 * @param $attendee_id ID of attendee ticket
					 * @param $event_id ID of event
					 * @param $order_id Easy Digital Downloads order ID
					 * @param $product_id Easy Digital Downloads product ID
					 */
					do_action( 'event_ticket_edd_attendee_created', $attendee_id, $event_id, $order_id, $product_id );

					/**
					 * Action fired when an attendee ticket is generated
					 *
					 * @param $attendee_id ID of attendee ticket
					 * @param $order EDD order ID
					 * @param $product_id Product ID attendee is "purchasing"
					 * @param $order_attendee_id Attendee # for order
					 */
					do_action( 'event_tickets_edd_ticket_created', $attendee_id, $order_id, $product_id, $order_attendee_id );

					$this->record_attendee_user_id( $attendee_id );
					$order_attendee_id++;
				}
			}
		}
		if ( $has_tickets ) {
			update_post_meta( $order_id, self::$order_has_tickets, '1' );

			// Send the email to the user
			do_action( 'eddtickets-send-tickets-email', $order_id );
		}

	}

	/**
	 * Generally speaking we want to hide the ticket products from the "storefront" and
	 * only expose them via the ticket form on single event pages.
	 *
	 * @param $query
	 */
	public function hide_tickets_from_shop( $query ) {
		// Exceptions: don't interfere in the admin environment, for EDD API requests, etc
		if ( is_admin() ) return;
		if ( defined( 'EDD_DOING_API' ) && EDD_DOING_API ) return;
		if ( empty( $query->query_vars['post_type'] ) || $query->query_vars['post_type'] != 'download' ) return;
		if ( ! empty( $query->query_vars['meta_key'] ) && $query->query_vars['meta_key'] == self::$event_key ) return;

		// Otherwise, build a list of post IDs representing tickets to ignore
		if ( ! $query->is_singular ) {
			$query->set( 'post__not_in', $this->get_all_tickets_ids() );
		}
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
	 * Adds a message to EDD's order email confirmation.
	 * @param $order
	 * @param $payment_id
	 * @param $unused_payment_data
	 */
	public function add_tickets_msg_to_email( $email_body, $payment_id, $unused_payment_data ) {

		//if( did_action( 'eddtickets-send-tickets-email' ) )
		//return $email_body;

		$order_items = edd_get_payment_meta_downloads( $payment_id );

		// Bail if the order is empty
		if ( empty( $order_items ) ) {
			return $email_body;
		}

		$has_tickets = false;

		// Iterate over each product
		foreach ( (array) $order_items as $item ) {

			$product_id = isset( $item['id'] ) ? $item['id'] : false;

			// Get the event this tickets is for
			$event_id = get_post_meta( $product_id, self::$event_key, true );

			if ( ! empty( $event_id ) ) {
				$has_tickets = true;
				break;
			}
		}
		if ( ! $has_tickets )
			return $email_body;

		$message = __( "You'll receive your tickets in another email.", 'event-tickets-plus' );
		return $email_body . '<br/>' . apply_filters( 'eddtickets_email_message', $message );

	}

	/**
	 * Saves a given ticket (EDDCommerce product)
	 *
	 * @param int                     $event_id
	 * @param Tribe__Tickets__Ticket_Object $ticket
	 * @param array                   $raw_data
	 *
	 * @return bool
	 */
	public function save_ticket( $event_id, $ticket, $raw_data = array() ) {
		// assume we are updating until we find out otherwise
		$save_type = 'update';

		if ( empty( $ticket->ID ) ) {
			$save_type = 'create';

			/* Create main product post */
			$args = array(
				'post_status'  => 'publish',
				'post_type'    => 'download',
				'post_author'  => get_current_user_id(),
				'post_content' => $ticket->description,
				'post_title'   => $ticket->name,
			);

			$ticket->ID = wp_insert_post( $args );

			// Relate event <---> ticket
			add_post_meta( $ticket->ID, self::$event_key, $event_id );

		} else {
			$args = array(
				'ID'           => $ticket->ID,
				'post_content' => $ticket->description,
				'post_title'   => $ticket->name,
			);

			$ticket->ID = wp_update_post( $args );
		}

		if ( ! $ticket->ID ) {
			return false;
		}

		update_post_meta( $ticket->ID, 'edd_price', $ticket->price );

		$global_stock_mode = isset( $raw_data['ticket_global_stock'] )
			? filter_var( $raw_data['ticket_global_stock'], FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH )
			: '';

		$global_stock_cap = isset( $raw_data['ticket_edd_global_stock_cap'] )
			? (int) $raw_data['ticket_edd_global_stock_cap']
			: 0;

		update_post_meta( $ticket->ID, '_global_stock_mode', $global_stock_mode );
		update_post_meta( $ticket->ID, '_global_stock_cap', $global_stock_cap );

		if ( 'global' === $global_stock_mode || 'capped' === $global_stock_mode ) {
			$global_stock = new Tribe__Tickets__Global_Stock( $event_id );
			$stock = $global_stock->get_stock_level();
		}
		else {
			$stock = trim( $raw_data['ticket_edd_stock'] );;
		}

		update_post_meta( $ticket->ID, '_stock', $stock );

		if ( isset( $raw_data['ticket_edd_sku'] ) )
			update_post_meta( $ticket->ID, '_sku', $raw_data['ticket_edd_sku'] );

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

		wp_set_object_terms( $ticket->ID, 'Ticket', 'download_category', true );

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
	 * Note that the total sales/purchases figure maintained by EDD is not adjusted on the
	 * basis that deleting an attendee does not mean the sale didn't go through; this is
	 * a change in behaviour from the 4.0.x releases.
	 *
	 * @param $event_id
	 * @param $ticket_id
	 *
	 * @return bool
	 */
	public function delete_ticket( $event_id, $ticket_id ) {
		// Ensure we know the event and product IDs (the event ID may not have been passed in)
		if ( empty( $event_id ) ) $event_id = get_post_meta( $ticket_id, self::ATTENDEE_EVENT_KEY, true );
		$product_id = get_post_meta( $ticket_id, self::ATTENDEE_PRODUCT_KEY, true );

		// Try to kill the actual ticket/attendee post
		$delete = wp_delete_post( $ticket_id, true );
		if ( is_wp_error( $delete ) ) {
			return false;
		}

		do_action( 'eddtickets_ticket_deleted', $ticket_id, $event_id, $product_id );
		return true;
	}

	/**
	 * Returns all the tickets for an event
	 *
	 * @param int $event_id
	 *
	 * @return array
	 */
	protected function get_tickets( $event_id ) {

		$ticket_ids = $this->get_tickets_ids( $event_id );

		if ( ! $ticket_ids ) {
			return array();
		}

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
	 * @param $post_link
	 * @param $post
	 * @param $unused_leavename
	 * @param $unused_sample
	 *
	 * @return string
	 */
	public function hijack_ticket_link( $post_link, $post, $unused_leavename, $unused_sample ) {

		if ( $post->post_type === 'download' ) {
			$event = get_post_meta( $post->ID, self::$event_key, true );
			if ( ! empty( $event ) ) {
				$post_link = get_permalink( $event );
			}
		}

		return $post_link;
	}

	/**
	 * Shows the tickets form in the front end
	 *
	 * @param $content
	 * @return void
	 */
	public function front_end_tickets_form( $content ) {
		$post = $GLOBALS['post'];

		// For recurring events (child instances only), default to loading tickets for the parent event
		if ( ! empty( $post->post_parent ) && function_exists( 'tribe_is_recurring_event' ) && tribe_is_recurring_event( $post->ID ) ) {
			$post = get_post( $post->post_parent );
		}

		$tickets = self::get_tickets( $post->ID );

		if ( empty( $tickets ) ) {
			return;
		}

		$global_stock = new Tribe__Tickets__Global_Stock( $post->ID );
		$global_stock_enabled = $global_stock->is_enabled();
		Tribe__Tickets__Tickets::add_frontend_stock_data( $tickets );

		$must_login = ! is_user_logged_in() && $this->login_required();
		include $this->getTemplateHierarchy( 'eddtickets/tickets' );
	}

	/**
	 * Grabs the submitted front end tickets form and adds the products to the cart.
	 */
	public function process_front_end_tickets_form() {
		parent::process_front_end_tickets_form();

		// We're only interested in EDD Tickets submissions
		if ( ! isset( $_GET['eddtickets_process'] ) || empty( $_POST['product_id'] ) ) {
			return;
		}

		// Add each ticket product to the cart
		foreach ( (array) $_POST['product_id'] as $product_id ) {
			$quantity = isset( $_POST[ 'quantity_' . $product_id ] ) ? (int) $_POST[ 'quantity_' . $product_id ] : 0;
			if ( $quantity > 0 ) $this->add_ticket_to_cart( $product_id, $quantity );
		}

		// To minimize accidental re-submissions, redirect back to self
		wp_redirect( edd_get_checkout_uri() );
		edd_die();
	}

	/**
	 * Handles the process of adding a ticket product to the cart.
	 *
	 * If the cart already contains a line item for the same product, simply increment the
	 * quantity for that item accordingly.
	 *
	 * @see bug #28917
	 * @param $product_id
	 * @param $quantity
	 */
	protected function add_ticket_to_cart( $product_id, $quantity ) {
		// Is the item in the cart already? Simply adjust the quantity if so
		if ( edd_item_in_cart( $product_id ) ) {
			$existing_quantity = edd_get_cart_item_quantity( $product_id );
			$quantity += $existing_quantity;
			edd_set_cart_item_quantity( $product_id, $quantity );
		}
		// Otherwise, add to cart as a new item
		else {
			$options = array( 'quantity' => $quantity );
			edd_add_to_cart( $product_id, $options );
		}
	}

	/**
	 * Get the URL to the ticket reports -- Does nothing at this time
	 *
	 * @param $event_id
	 * @param $ticket_id
	 *
	 * @return null|Tribe__Tickets__Ticket_Object
	 */
	public function get_ticket_reports_link( $event_id, $ticket_id ) {
		return null;
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
		$product = edd_get_download( $ticket_id );

		if ( ! $product ) {
			return null;
		}

		$return  = new Tribe__Tickets__Ticket_Object();

		$purchased = $this->stock_control->get_purchased_inventory( $ticket_id, array( 'publish' ) );
		$stock = ( '' === $product->_stock ) ? Tribe__Tickets__Ticket_Object::UNLIMITED_STOCK : $product->_stock;

		$return->description    = $product->post_content;
		$return->frontend_link  = get_permalink( $ticket_id );
		$return->ID             = $ticket_id;
		$return->name           = $product->post_title;
		$return->price          = $product->edd_price;
		$return->provider_class = get_class( $this );
		$return->admin_link     = admin_url( sprintf( get_post_type_object( $product->post_type )->_edit_link . '&action=edit', $ticket_id ) );
		$return->start_date     = get_post_meta( $ticket_id, '_ticket_start_date', true );
		$return->end_date       = get_post_meta( $ticket_id, '_ticket_end_date', true );

		$pending = $this->stock_control->count_incomplete_order_items( $ticket_id );

		// Ticket stock is a simple reflection of remaining inventory for this item...
		$stock = $stock - $purchased - $pending;

		// ...With some exceptions for global stock tickets
		$stock = $this->set_stock_level_for_global_stock_tickets( $stock, $event_id, $ticket_id );

		$return->manage_stock( (boolean) $stock );
		$return->global_stock_mode( get_post_meta( $ticket_id, '_global_stock_mode', true ) );
		$return->global_stock_cap( get_post_meta( $ticket_id, '_global_stock_cap', true ) );

		$return->stock( $stock );
		$return->qty_sold( $purchased );
		$return->qty_pending( $pending );

		return $return;
	}

	/**
	 * This method is used to lazily set and correct stock levels for tickets which
	 * draw on the global event inventory.
	 *
	 * It's required because, currently, there is a discrepancy between how individual
	 * tickets are created and saved (ie, via ajax) and how event-wide settings such as
	 * global stock are saved - which means a ticket may be saved before the global
	 * stock level and save_tickets() will set the ticket inventory to zero. To avoid
	 * the out-of-stock issues that might otherwise result, we lazily correct this
	 * once the global stock level is known.
	 *
	 * @param int $existing_stock
	 * @param int $event_id
	 * @param int $ticket_id
	 *
	 * @return int
	 */
	protected function set_stock_level_for_global_stock_tickets( $existing_stock, $event_id, $ticket_id ) {
		$global_stock = new Tribe__Tickets__Global_Stock( $event_id );

		// If this event does not have a global stock then do not modify the existing stock level
		if ( ! $global_stock->is_enabled() ) {
			return $existing_stock;
		}

		// If this specific ticket maintains its own independent stock then again do not interfere
		if ( Tribe__Tickets__Global_Stock::OWN_STOCK_MODE === get_post_meta( $ticket_id, '_global_stock_mode', true ) ) {
			return $existing_stock;
		}

		// Otherwise the ticket stock ought to match the current global stock
		$product_stock = edd_get_download( $ticket_id )->_stock;
		$actual_stock  = ( '' === $product_stock ) ? Tribe__Tickets__Ticket_Object::UNLIMITED_STOCK : $product_stock;
		$global_stock  = $global_stock->get_stock_level();

		// Look out for and correct discrepancies where the actual stock is zero but the global stock is non-zero
		if ( 0 == $actual_stock && 0 < $global_stock ) {
			update_post_meta( $ticket_id, '_stock', $global_stock );
			update_post_meta( $ticket_id, '_stock_status', 'instock' );
		}

		return $global_stock;
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

		if ( '' === ( $event = get_post_meta( $ticket_product, self::$event_key, true ) ) ) {
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

		$args = array(
			'posts_per_page' => - 1,
			'post_type'      => self::ATTENDEE_OBJECT,
			'meta_key'       => self::ATTENDEE_EVENT_KEY,
			'meta_value'     => $event_id,
			'orderby'        => 'ID',
			'order'          => 'DESC',
		);

		$attendees_query = new WP_Query( $args );

		if ( ! $attendees_query->have_posts() ) {
			return array();
		}

		$attendees = array();

		foreach ( $attendees_query->posts as $attendee ) {
			$order_id   = get_post_meta( $attendee->ID, self::ATTENDEE_ORDER_KEY, true );
			$checkin    = get_post_meta( $attendee->ID, $this->checkin_key, true );
			$security   = get_post_meta( $attendee->ID, self::$security_code, true );
			$product_id = get_post_meta( $attendee->ID, self::ATTENDEE_PRODUCT_KEY, true );
			$optout     = (bool) get_post_meta( $attendee->ID, self::ATTENDEE_OPTOUT_KEY, true );
			$user_id    = get_post_meta( $attendee->ID, self::ATTENDEE_USER_ID, true );

			if ( empty( $product_id ) ) {
				continue;
			}

			$product = get_post( $product_id );
			$product_title = ( ! empty( $product ) ) ? $product->post_title : get_post_meta( $attendee->ID, $this->deleted_product, true ) . ' ' . __( '(deleted)', 'eddtickets' );

			// Add the Attendee Data to the Order data
			$attendee_data = array_merge(
				$this->get_order_data( $order_id ),
				array(
					'ticket'      => $product_title,
					'attendee_id' => $attendee->ID,
					'optout'      => $optout,
					'security'    => $security,
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
			$attendee_data = apply_filters( 'tribe_tickets_attendee_data', $attendee_data, 'edd', $attendee, $event_id );

			$attendees[] = $attendee_data;
		}

		return $attendees;

	}

	/**
	 * Retreive only order related information
	 *
	 *     order_id
	 *     order_status
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
		$user_info     = edd_get_payment_meta_user_info( $order_id );
		$name          = $user_info['first_name'] . ' ' . $user_info['last_name'];
		$email         = $user_info['email'];
		$order_status  = get_post_field( 'post_status', $order_id );
		$status_label  = edd_get_payment_status( get_post( $order_id ), true );
		$order_warning = 'publish' !== $order_status;

		$data = array(
			'order_id'        => $order_id,
			'order_status'    => $status_label,
			'order_warning'   => $order_warning,
			'purchaser_name'  => $name,
			'purchaser_email' => $email,
			'provider'        => __CLASS__,
			'provider_slug'   => 'edd',
			'purchase_time'   => get_post_time( Tribe__Date_Utils::DBDATETIMEFORMAT, false, $order_id ),
		);

		/**
		 * Allow users to filter the Order Data
		 *
		 * @param array An associative array with the Information of the Order
		 * @param string What Provider is been used
		 * @param int Order ID
		 *
		 */
		$data = apply_filters( 'tribe_tickets_order_data', $data, 'edd', $order_id );

		return $data;
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
		do_action( 'eddtickets_checkin', $attendee_id, $qr );

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
		do_action( 'eddtickets_uncheckin', $attendee_id );

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
		$global_stock_mode   = '';
		$global_stock_cap    = 0;

		if ( ! empty( $ticket_id ) ) {
			$ticket = $this->get_ticket( $event_id, $ticket_id );

			if ( ! empty( $ticket ) ) {
				$stock = $ticket->managing_stock() ? $ticket->stock() : '';
				$sku   = get_post_meta( $ticket_id, '_sku', true );
				$global_stock_mode = $ticket->global_stock_mode();
				$global_stock_cap  = $ticket->global_stock_cap();
			}
		}

		include $this->pluginPath . 'src/admin-views/edd-metabox-advanced.php';
	}

	/**
	 * Insert a link to the report.
	 *
	 * @param $event_id
	 *
	 * @return string
	 */
	public function get_event_reports_link( $event_id ) {
		$ticket_ids = $this->get_tickets_ids( $event_id );
		if ( empty( $ticket_ids ) ) {
			return '';
		}

		$term = get_term_by( 'name', 'Ticket', 'download_category' );

		ob_start();
		?>

		<small>
			<a href="<?php echo esc_url( admin_url( 'edit.php?view=downloads&post_type=download&page=edd-reports&category=' . $term->term_id . '&event=' . $event_id ) ); ?>" id="eddtickets_event_reports"><?php esc_html_e( 'Event sales report', 'event-tickets-plus' );?></a>
		</small>

		<?php

		return ob_get_clean();
	}

	/**
	 * Filters the product reports to only show tickets for the specified event
	 *
	 * @param $query
	 *
	 * @return void
	 */
	public function filter_ticket_reports( $query ) {
		if ( ! is_admin() ) {
			return;
		}

		if ( ! isset( $_GET['page'] ) || 'edd-reports' != $_GET['page'] ) {
			return;
		}

		if ( ! isset( $_GET['category'] ) || ! isset( $_GET['event'] ) ) {
			return;
		}

		$query->set( 'meta_query', array(
			array(
				'key' => '_tribe_eddticket_for_event',
				'value' => absint( $_GET['event'] ),
			),
		) );
	}

	/**
	 * Registers a metabox in the EDD product edit screen
	 * with a link back to the product related Event.
	 *
	 */
	public function edd_meta_box() {
		$event_id = get_post_meta( get_the_ID(), self::$event_key, true );

		if ( ! empty( $event_id ) ) {
			add_meta_box( 'eddtickets-linkback', 'Event', array( $this,	'edd_meta_box_inside' ), 'download', 'normal', 'high' );
		}

	}

	/**
	 * Contents for the metabox in the EDD product edit screen
	 * with a link back to the product related Event.
	 */
	public function edd_meta_box_inside() {

		$event_id = get_post_meta( get_the_ID(), self::$event_key, true );
		if ( ! empty( $event_id ) )
			echo sprintf( '%s <a href="%s">%s</a>', __( 'This is a ticket for the event:', 'event-tickets-plus' ), esc_url( get_edit_post_link( $event_id ) ), esc_html( get_the_title( $event_id ) ) );

	}

	/**
	 * Get's the product price html
	 *
	 * @param int|object $product
	 *
	 * @return string
	 */
	public function get_price_html( $product ) {
		return edd_price( $product, false );
	}

	/**
	 * Retrieve the ID numbers of all tickets of an event
	 *
	 * @param int $event_id
	 *
	 * @return array
	 */
	public function get_tickets_ids( $event_id ) {

		if ( is_object( $event_id ) )
			$event_id = $event_id->ID;

		$query = new WP_Query( array(
			'post_type'      => 'download',
			'meta_key'       => self::$event_key,
			'meta_value'     => $event_id,
			'meta_compare'   => '=',
			'posts_per_page' => - 1,
			'fields'         => 'ids',
			'post_status'    => 'publish',
		) );

		return $query->posts;
	}

	/**
	 * Get an array of IDs of all tickets
	 *
	 * @return array
	 */
	public function get_all_tickets_ids() {
		global $wpdb;
		return $wpdb->get_col( "SELECT post_id FROM $wpdb->postmeta WHERE meta_key = '" . self::$event_key . "'" );
	}

	/**
	 * Inspects the cart in order to catch out-of-stock issues etc and display them to the customer
	 * before they go on to complete their personal and payment details, etc.
	 *
	 * If this is undesirable or different formatting etc is needed
	 */
	public function pre_checkout_errors() {
		ob_start();
		$this->checkout_errors();
		edd_print_errors();
		echo apply_filters( 'eddtickets_pre_checkout_errors', ob_get_clean() );
	}

	/**
	 * Ensure out of stock tickets cannot be purchased even if they manage to get added to the cart
	 */
	public function checkout_errors() {
		Tribe__Tickets_Plus__Commerce__EDD__Main::get_instance()->global_stock()->check_stock();

		foreach ( (array) edd_get_cart_contents() as $item ) {
			$remaining = $this->stock_control->available_units( $item['id'] );

			// We have to append the item IDs otherwise if we have multiple errors of the same type one will overwrite
			// the other
			if ( ! $remaining ) {
				edd_set_error( 'no_stock_' . $item['id'], sprintf( __( '%s ticket is sold out', 'event-tickets-plus' ), get_the_title( $item['id'] ) ) );
			}
			elseif ( self::UNLIMITED !== $remaining && $item['quantity'] > $remaining ) {
				edd_set_error( 'insufficient_stock_' . $item['id'], sprintf( __( 'Sorry! Only %d tickets remaining for %s', 'event-tickets-plus' ), $remaining, get_the_title( $item['id'] ) ) );
			}
		}
	}

	/**
	 * Returns true or false according to whether ticket stock is available.
	 *
	 * Left in place for legacy reasons (custom eddtickets/tickets.php views may call this
	 * method, even though it is now only a wrapper that uses the stock control object).
	 *
	 * @todo   remove 6-9 months after release 118
	 * @param  int $ticket_id
	 * @return bool
	 */
	public static function is_stock_left( $ticket_id ) {
		$stock_control = new Tribe__Tickets_Plus__Commerce__EDD__Stock_Control;
		return $stock_control->available_units( $ticket_id ) > 0;
	}

	/**
	 * Trick EDD into thinking the ticket has a download file. If one already exists, we need not add
	 * another.
	 *
	 * @param array $files
	 * @param int   $download_id
	 * @param int   $unused_price_id
	 *
	 * @return array
	 */
	public function ticket_downloads( $files = array(), $download_id = 0, $unused_price_id = null ) {
		// Determine if this is a ticket product or if it already has a download file
		if ( ! get_post_meta( $download_id, self::$event_key, true ) ) {
			return $files;
		}

		if ( ! empty( $files ) ) {
			return $files;
		}

		$files[] = array(
			'name' => __( 'Print Ticket', 'event-tickets-plus' ),
			'file'  => self::TICKET_DOWNLOAD,
		);

		return $files;
	}

	/**
	 * Setup the print ticket URL so that a print view is rendered instead of a download file
	 *
	 * @param array $args
	 *
	 * @return array
	 */
	public function print_ticket_url( $args = array() ) {
		// Determine if this is a ticket product
		if ( ! get_post_meta( $args['download_id'], self::$event_key, true ) ) {
			return $args;
		}

		// Interfere only with the tickets link (thus allowing additional files to be downloaded
		// as part of the purchase)
		if ( ! $this->is_print_ticket_item( $args ) ) {
			return $args;
		}

		$args = array(
			'edd_action'   => 'print_ticket',
			'download_id'  => $args['download_id'],
			'download_key' => $args['download_key'],
			'file'         => self::TICKET_DOWNLOAD,
		);

		return $args;
	}

	/**
	 * @param $item
	 * @return bool
	 */
	protected function is_print_ticket_item( $item ) {
		static $download_files = array();

		if ( empty( $download_files ) ) {
			$download_files = edd_get_download_files( $item['download_id'] );
		}

		foreach ( $download_files as $index => $download ) {
			if ( $item['file'] != $index ) continue;
			if ( self::TICKET_DOWNLOAD === $download['file'] ) return true;
			if ( self::LEGACY_TICKET_DOWNLOAD === $download['file'] ) return true;
		}

		return false;
	}

	/**
	 * Render the print ticket view, based on the email template
	 *
	 * @return void
	 */
	public function render_ticket_print_view() {

 		// Is this a print-ticket request?
		if ( ! isset( $_GET['eddfile'] ) || ! isset( $_GET['edd_action'] ) || $_GET['edd_action'] !== 'print_ticket' ) {
			return;
		}

		// As of EDD 2.3 a token should be available to help verify if the link is valid
		if ( ! $this->passed_token_validation( $_GET ) ) {
			return;
		}

		// Decompile the eddfile argument into its base components
		$order_parts = array_values( explode( ':', rawurldecode( $_GET['eddfile'] ) ) );

		// We expect there to be at least two components (payment and download IDs)
		if ( count( $order_parts ) < 2 ) {
			return;
		}

		$payment_id  = $order_parts[0];
		$download_id = $order_parts[1];
		$user_info   = edd_get_payment_meta_user_info( $payment_id );

		$args = array(
			'post_type'      => self::ATTENDEE_OBJECT,
			'meta_query'     => array(
				array(
					'key'    => self::ATTENDEE_ORDER_KEY,
					'value'  => $payment_id,
				),
				array(
					'key'    => self::ATTENDEE_PRODUCT_KEY,
					'value'  => $download_id,
				),
			),
			'posts_per_page' => -1,
			'fields'         => 'ids',
		);

		$query = new WP_Query( $args );

		$attendees = array();

		foreach ( $query->posts as $ticket_id ) {
			$product = get_post( get_post_meta( $ticket_id, self::ATTENDEE_PRODUCT_KEY, true ) );
			$ticket_unique_id = get_post_meta( $ticket_id, '_unique_id', true );
			$ticket_unique_id = $ticket_unique_id === '' ? $ticket_id : $ticket_unique_id;

			$attendees[] = array(
				'event_id'      => get_post_meta( $ticket_id, self::ATTENDEE_EVENT_KEY, true ),
				'product_id'    => $product->ID,
				'ticket_name'   => $product->post_title,
				'holder_name'   => $user_info['first_name'] . ' ' . $user_info['last_name'],
				'order_id'      => $payment_id,
				'ticket_id'     => $ticket_unique_id,
				'qr_ticket_id'  => $ticket_id,
				'security_code' => get_post_meta( $ticket_id, self::$security_code, true ),
			);
		}

		$content = self::get_instance()->generate_tickets_email_content( $attendees );
		$content .= '<script type="text/javascript">window.onload = function(){ window.print(); }</script>';
		echo $content;
		exit;
	}

	/**
	 * Add edd_action as a possible param in EDD print url
	 *
	 * @param array $params Arrat of allowed params
	 * @return array
	 */
	public function add_allowed_param( $params ) {
		$params[] = 'edd_action';
		return $params;
	}

	protected function passed_token_validation( array $url_query ) {
		$query  = array_map( 'urlencode', $url_query );

		$url = untrailingslashit( home_url() );
		$url = add_query_arg( $query, $url );

		$result = edd_validate_url_token( $url );

		return apply_filters( 'edd_tickets_passed_token_validation', $result );
	}

	/**
	 * @return Tribe__Tickets_Plus__Commerce__EDD__Stock_Control
	 */
	public function stock() {
		return $this->stock_control;
	}


	/**
	 * Excludes EDD product post types from the list of supported post types that Tickets can be attached to
	 *
	 * @since 4.0.5
	 *
	 * @param array $post_types Array of supported post types
	 *
	 * @return array
	 */
	public function exclude_product_post_type( $post_types ) {
		if ( isset( $post_types['download'] ) ) {
			unset( $post_types['download'] );
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
	 * @return Tribe__Tickets_Plus__Commerce__EDD__Main
	 */
	public static function get_instance() {
		if ( ! self::$instance instanceof self ) {
			self::$instance = new self();
		}

		return self::$instance;
	}
}
