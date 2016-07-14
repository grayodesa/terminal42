<?php

if ( class_exists( 'Tribe__Tickets_Plus__Commerce__Shopp__Main' ) || ! class_exists( 'Tribe__Tickets__Tickets' ) ) {
	return;
}

class Tribe__Tickets_Plus__Commerce__Shopp__Main extends Tribe__Tickets_Plus__Tickets {
	/**
	 * Name of the CPT that holds Attendees (tickets holders).
	 */
	const ATTENDEE_OBJECT = 'tribe_shoppticket';

	/**
	 * Meta key that relates Attendees and Products.
	 */
	const ATTENDEE_PRODUCT_KEY = '_tribe_shoppticket_product';

	/**
	 * Meta key that relates Attendees and Orders.
	 */
	const ATTENDEE_ORDER_KEY = '_tribe_shoppticket_order';

	/**
	 * Meta key that relates Attendees and Events.
	 */
	const ATTENDEE_EVENT_KEY = '_tribe_shoppticket_event';

	/**
	 * Instance of this class for use as singleton
	 */
	private static $instance;

	/**
	 * Instance of Tribe__Tickets_Plus__Commerce__Shopp__Meta
	 */
	private static $meta;

	/**
	 * ShoppTickets will attempt to generate tickets when this action fires. It can be
	 * adjusted to run earlier or later (for instance on the equivalent captured_order
	 * hook, depending on payment methods, how quickly tickets should be dispatched etc).
	 *
	 * @var string
	 */
	protected $generate_tickets_action = 'shopp_invoiced_order_event';

	/**
	 * Whether ticket products should be assigned to a specific product category.
	 *
	 * @var boolean
	 */
	protected $assign_to_category = true;

	/**
	 * Name of the category to which ticket products are assigned (assuming we assign to a
	 * category at all with respect to the assign_to_category property).
	 *
	 * @var string
	 */
	protected $category_name = 'Ticket';

	/**
	 * Product names can change over their lifetime. When this property evaluates to
	 * true the historical product name (from the time the purchase was made) will be used,
	 * otherwise the current product name will be used where available.
	 *
	 * @var bool
	 */
	protected $use_historical_ticket_name = false;

	/**
	 * Name of the action used to trigger a resending of the tickets email from the order
	 * admin screen.
	 *
	 * @var string
	 */
	protected $resend_tickets_action = 'shopptickets-resend-tickets';

	/**
	 * Name of the CPT that holds Attendees (tickets holders).
	 *
	 * @deprecated use of the ATTENDEE_OBJECT class constant is preferred
	 *
	 * @var string
	 */
	public $attendee_object = 'tribe_shoppticket';

	/**
	 * Meta key that relates Products and Events
	 *
	 * @var string
	 */
	public $event_key = 'shopptickets_event';

	/**
	 * Meta key that stores if an attendee has checked in to an event
	 *
	 * @var string
	 */
	public $checkin_key = '_tribe_shoppticket_checkedin';

	/**
	 * Meta key that relates Attendees and Products.
	 *
	 * @deprecated use of the ATTENDEE_PRODUCT_KEY class constant is preferred
	 *
	 * @var string
	 */
	public $attendee_product_key = '_tribe_shoppticket_product';

	/**
	 * Meta key that relates Attendees and Orders.
	 *
	 * @deprecated use of the ATTENDEE_ORDER_KEY class constant is preferred
	 *
	 * @var string
	 */
	public $attendee_order_key = '_tribe_shoppticket_order';

	/**
	 * Meta key that relates Attendees and Events.
	 *
	 * @deprecated use of the ATTENDEE_EVENT_KEY class constant is preferred
	 *
	 * @var string
	 */
	public $attendee_event_key = '_tribe_shoppticket_event';

	/**
	 * Meta key that holds the security code that's printed in the tickets
	 *
	 * @var string
	 */
	public $security_code = '_tribe_shoppticket_security_code';

	/**
	 * Meta key that holds if an order has tickets (for performance)
	 *
	 * @var string
	 */
	public $order_has_tickets = 'tribe_tickets';

	/**
	 * Meta key that holds the name of a ticket to be used in reports if the Product is deleted
	 *
	 * @var string
	 */
	public $deleted_product = '_tribe_deleted_product_name';

	/**
	 * Meta key that holds if the attendee has opted out of the front-end listing
	 * @var string
	 */
	const ATTENDEE_OPTOUT_KEY = '_tribe_shoppticket_attendee_optout';

	/**
	 * Current version of this plugin
	 */
	const VERSION = '3.12a1';

	/**
	 * Min required The Events Calendar version
	 */
	const REQUIRED_TEC_VERSION = '3.11';

	/**
	 * Min required Shopp version
	 */
	const REQUIRED_SHOPP_VERSION = '1.2.9';

	/**
	 * Class constructor
	 */
	public function __construct() {
		$this->pluginName = 'Shopp'; // This string is used for form a link to the product editor
		$this->pluginSlug = 'shopptickets';
		$this->pluginPath = trailingslashit( EVENT_TICKETS_PLUS_DIR );
		$this->pluginDir  = trailingslashit( basename( $this->pluginPath ) );
		$this->pluginUrl  = trailingslashit( plugins_url( $this->pluginDir ) );

		parent::__construct();
		$this->setup();
		$this->hooks();
		$this->meta();
	}

	/**
	 * Allows aspects of ShoppTickets behaviour to be tweaked.
	 *
	 * Tickets will be generated and dispatched when a new invoiced order event is raised
	 * (as the contract to sell has effectively been created by that point). However, for the
	 * cautious it may be preferable to wait until payment capture or some other event. This
	 * can be accomplished with something like:
	 *
	 *     // Use the captured event to trigger ticket generation
	 *     add_filter( 'shopptickets_generate_tickets_hook', 'shopptickets_set_generator_event' );
	 *     function shopptickets_set_generator_event() { return 'shopp_captured_order_event'; }
	 *
	 * Also, by default all new ticket products will be assigned to a Tickets category (which
	 * will be created if it does not already exist). Assignment can be disabled altogether
	 * with the shopptickets_assign_to_category or else the name of the category to be used can
	 * be changed to something other than "Tickets". Examples:
	 *
	 *     // Turn off assignment
	 *     add_filter( 'shopptickets_assign_to_category', '__return_false' );
	 *
	 *     // Use something other than the "Tickets" category
	 *     add_filter( 'shopptickets_ticket_category', 'shopptickets_change_category' );
	 *     function shopptickets_change_category() { return 'Awesome Events'; }
	 *
	 * The shopptickets_historical_purchase_names filter can be used to dictate whether the attendee
	 * list shows the current product name (a product name can be changed multiple times over its
	 * lifetime) or the name of the product at time of purchase. See the comments for the
	 * retrieve_product_name() method for more detail.
	 */
	protected function setup() {
		$this->assign_to_category = (bool) apply_filters( 'shopptickets_assign_to_category', $this->assign_to_category );
		$this->category_name = (string) apply_filters( 'shopptickets_ticket_category', $this->category_name );
		$this->generate_tickets_action = (string) apply_filters( 'shopptickets_generate_tickets_hook', $this->generate_tickets_action );
		$this->use_historical_ticket_name = (bool) apply_filters( 'shopptickets_historical_purchase_names', $this->use_historical_ticket_name );
	}

	/**
	 * Registers all actions/filters.
	 */
	public function hooks() {
		add_action( 'init', array( $this, 'register_attendee_type' ) );
		add_action( 'add_meta_boxes_' . Product::$posttype, array( $this, 'product_editor_meta_box' ) );
		add_action( $this->generate_tickets_action, array( $this, 'generate_tickets' ) );
		add_action( 'shopp_service_init', array( $this, 'resend_tickets_email' ) );

		add_filter( 'shopp_order_management_controls', array( $this, 'add_resend_email_btn' ) );
		add_filter( 'shopp_tag_cartitem_url', array( $this, 'change_product_links' ), 10, 3 );
		add_filter( 'shopp_tag_product_url', array( $this, 'change_product_links' ), 10, 3 );
		add_filter( 'tribe_tickets_settings_post_types', array( $this, 'exclude_product_post_type' ) );
		add_filter( 'event_tickets_attendees_shopp_checkin_stati', array( $this, 'checkin_statuses' ), 10, 2 );

		add_action( 'shopp_invoiced_order_event', array( $this, 'save_attendee_optout_choice_to_order' ), 5 );
		add_filter( 'shopp_cartitem_data', array( $this, 'set_attendee_optout_choice' ), 10, 2 );
	}


	/**
	 * Sets attendee optout choice on order posts
	 *
	 * @since 4.1
	 *
	 * @param OrderEventMessage $order_event Shopp order event
	 */
	public function save_attendee_optout_choice_to_order( OrderEventMessage $order_event ) {
		$order = shopp_order( $order_event->order );
		$order_items = $order->purchased;

		// Bail if the order is empty
		if ( empty( $order_items ) ) {
			return;
		}

		$cookie_key = 'tribe-event-tickets-shopp-attendee-optout';
		if ( empty( $_COOKIE[ $cookie_key ] ) ) {
			return;
		}

		$data = $_COOKIE[ $cookie_key ];
		$data = urldecode( $data );
		parse_str( $data, $data );

		$optout = false;

		// gather product ids
		foreach ( (array) $order_items as $item ) {
			if ( true === $optout ) {
				continue;
			}

			if ( empty( $item->product ) ) {
				continue;
			}

			if ( ! isset( $data[ $item->product ] ) ) {
				continue;
			}

			$optout = (bool) $data[ $item->product ];
		}

		// store the custom meta on the order
		$status = shopp_set_meta( $order->id, 'purchase', self::ATTENDEE_OPTOUT_KEY, $optout, 'meta' );
	}

	/**
	 * After placing the Order make sure we store the users option to show the Attendee Optout
	 *
	 * This method on shopp doesn't do anything to the variable filtered, we use this to set Cookies
	 */
	public function set_attendee_optout_choice( $data ) {
		if ( empty( $_POST['product_id'] ) || ! is_array( $_POST['product_id'] ) ) {
			return $data;
		}
		$product_ids = (array) $_POST['product_id'];
		$cookie_key = 'tribe-event-tickets-shopp-attendee-optout';

		$optout = isset( $_POST['tribe_shopp_optout'] ) ? (bool) $_POST['tribe_shopp_optout'] : false;

		if ( ! empty( $_COOKIE[ $cookie_key ] ) ) {
			$defaults = $_COOKIE[ $cookie_key ];
		} else {
			$defaults = array();
		}

		foreach ( $product_ids as $product_id ) {
			$key = (string) absint( $product_id );
			$query[ $key ] = $optout;
		}

		// Merge Cookies
		$query = wp_parse_args( $query, $defaults );

		// Build new Str
		$query_str = build_query( $query );

		// Set the query string on a Cookie
		setcookie( $cookie_key, $query_str, time() + ( 7 * DAY_IN_SECONDS ), '/' );

		return $data;
	}

	/**
	 * Custom meta integration object accessor method
	 *
	 * @since 4.1
	 *
	 * @return Tribe__Tickets_Plus__Commerce__Shopp__Meta
	 */
	public function meta() {
		if ( ! self::$meta ) {
			self::$meta = new Tribe__Tickets_Plus__Commerce__Shopp__Meta;
		}

		return self::$meta;
	}

	/**
	 * Tries to ensure product URLs point to the related event page rather than the actual
	 * Shopp product page.
	 */
	public function change_product_links( $result, $unused_options, $object ) {
		// Get the product id
		if ( $object instanceof Item ) {
			$id = $object->product;
		} elseif ( $object instanceof Product ) {
			$id = $object->id;
		}

		// Do nothing unless we have a product or cart item and it is associated with an event
		if ( ! isset( $id ) ) {
			return $result;
		}

		if ( 0 === ( $event_id = $this->get_related_event( $id ) ) ) {
			return $result;
		}

		// Replace with the single event URL
		return tribe_get_event_link( $event_id );
	}

	/**
	 * Register our custom post type for storing attendee details.
	 */
	public function register_attendee_type() {
		register_post_type( self::ATTENDEE_OBJECT, array(
			'label' => 'Tickets',
			'public' => false,
			'show_ui' => false,
			'show_in_menu' => false,
			'query_var' => false,
			'rewrite' => false,
			'capability_type' => 'post',
			'has_archive' => false,
			'hierarchical' => true,
		) );
	}

	/**
	 * Generate and store all the attendees information for a new order.
	 *
	 * @param OrderEventMessage $order_event
	 */
	public function generate_tickets( OrderEventMessage $order_event ) {
		$order = shopp_order( $order_event->order );
		$has_tickets = false;

		$optout = (bool) shopp_meta( $order->id, 'purchase', self::ATTENDEE_OPTOUT_KEY );

		// Iterate over each product
		foreach ( $order->purchased as $item ) {
			$order_attendee_id = 0;

			$event_id = $this->get_related_event( $item->product );
			if ( empty( $event_id ) ) {
				continue;
			}

			// Iterate over all the amount of tickets purchased (for this product)
			$quantity = intval( $item->quantity );
			for ( $i = 0; $i < $quantity; $i ++ ) {
				$attendee = array(
					'post_status' => 'publish',
					'post_title' => $order->id . ' | ' . $item->name . ' | ' . ( $i + 1 ),
					'post_type' => self::ATTENDEE_OBJECT,
					'ping_status' => 'closed',
				);

				// Insert individual ticket purchased
				$attendee_id = wp_insert_post( $attendee );

				update_post_meta( $attendee_id, self::ATTENDEE_PRODUCT_KEY, $item->product );
				update_post_meta( $attendee_id, self::ATTENDEE_ORDER_KEY, $order->id );
				update_post_meta( $attendee_id, self::ATTENDEE_EVENT_KEY, $event_id );
				update_post_meta( $attendee_id, $this->security_code, $this->generate_security_code( $order->id, $attendee_id ) );
				update_post_meta( $attendee_id, self::ATTENDEE_OPTOUT_KEY, $optout );

				/**
				 * Shopp specific action fired when a Shopp-driven attendee ticket for an event is generated
				 *
				 * @param $attendee_id ID of attendee ticket
				 * @param $event_id ID of event
				 * @param $order_id Shopp order ID
				 * @param $product_id Shopp product ID
				 */
				do_action( 'event_tickets_shopp_attendee_created', $attendee_id, $event_id, $order, $item->product );

				/**
				 * Action fired when an attendee ticket is generated
				 *
				 * @param $attendee_id ID of attendee ticket
				 * @param $order_id ID of order
				 * @param $product_id Product ID attendee is "purchasing"
				 * @param $order_attendee_id Attendee # for order
				 */
				do_action( 'event_tickets_shopp_ticket_created', $attendee_id, $order->id, $item->product, $order_attendee_id );

				$this->record_attendee_user_id( $attendee_id );
				$order_attendee_id++;
			}
			$has_tickets = true;
		}
		if ( $has_tickets ) {
			shopp_set_meta( $order->id, 'purchase', $this->order_has_tickets, true );
			$this->send_tickets( $order );
		}
	}

	/**
	 * Returns the event ID associated with the product/ticket ID. If no event is associated with
	 * the product it will return 0.
	 *
	 * @param $product_id
	 * @return int
	 */
	protected function get_related_event( $product_id ) {
		$related_event = shopp_product_meta( $product_id, $this->event_key );
		if ( ! is_array( $related_event ) ) {
			return (int) $related_event;
		}

		// If an array was returned they will all relate to the same event and we need only inspect one of them
		$related_event = array_shift( $related_event );
		return (int) $related_event->parent;
	}

	/**
	 * Generates the validation code that will be printed in the ticket.
	 * It purpose is to be used to validate the ticket at the door of an event.
	 *
	 * @param int $order_id
	 * @param int $attendee_id
	 * @return string
	 */
	private function generate_security_code( $order_id, $attendee_id ) {
		return substr( md5( $order_id . '_' . $attendee_id ), 0, 10 );
	}

	/**
	 * Saves or updates a ticket.
	 *
	 * @param int $event_id
	 * @param Tribe__Tickets__Ticket_Object $ticket
	 * @param array $raw_data
	 * @return bool
	 */
	public function save_ticket( $event_id, $ticket, $raw_data = array() ) {
		$stock = isset( $raw_data['ticket_shopp_stock'] ) ? $raw_data['ticket_shopp_stock'] : '';
		$sku = isset( $raw_data['ticket_shopp_sku'] ) ? $raw_data['ticket_shopp_sku'] : '';

		$ticket->name = stripslashes( $ticket->name );
		$ticket->description = stripslashes( $ticket->description );

		if ( empty( $ticket->ID ) ) {
			$this->create_product( $ticket, $event_id );
		}

		$this->update_product( $ticket, $stock, $sku );

		if ( ! $ticket->ID ) {
			return false;
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
	 * Creates a new Shopp product in relation to the provided $ticket object.
	 *
	 * @param Tribe__Tickets__Ticket_Object $ticket
	 * @param $event_id
	 */
	protected function create_product( Tribe__Tickets__Ticket_Object $ticket, $event_id ) {
		$product = shopp_add_product( array(
			'name' => $ticket->name,
			'description' => $ticket->description,
			'publish' => array( 'flag' => true ),
			'single' => array(
				'type' => 'Virtual',
				'price' => $ticket->price,
			),
		) );

		shopp_set_product_meta( $product->id, $this->event_key, $event_id );
		$ticket->ID = $product->id;
	}

	/**
	 * Updates the product data.
	 *
	 * Note: in future when the REQUIRED_SHOPP_VERSION increments to 1.3 we will be able to use a dev API
	 * function to handle name/description updates.
	 *
	 * @param Tribe__Tickets__Ticket_Object $ticket
	 * @param $stock
	 * @param $sku
	 */
	protected function update_product( Tribe__Tickets__Ticket_Object $ticket, $stock, $sku ) {
		// Load the product object or return false
		if ( false === ( $product = shopp_product( $ticket->ID ) ) ) {
			$ticket->ID = false;
			return;
		}

		// Maybe update name and description
		if ( $product->name != $ticket->name || $product->description != $ticket->description ) {
			$product->name = $ticket->name;
			$product->description = $ticket->description;
			$product->save();
		}

		// Update inventory
		$stock = trim( $stock );
		$stock = ( empty( $stock ) && '0' !== $stock )? false : $stock;
		$stock_control = ( false === $stock ) ? false : true;

		shopp_product_set_inventory( $ticket->ID, $stock_control, array(
			'stock' => $stock,
			'sku' => $sku,
		) );

		// Update sale dates and price and possibly the category
		$this->set_start_end_dates( $ticket );
		shopp_product_set_price( $ticket->ID, $ticket->price );
		if ( $this->assign_to_category ) {
			$this->assign_to_category( $ticket->ID );
		}
	}


	protected function set_start_end_dates ( Tribe__Tickets__Ticket_Object $ticket ) {
		if ( isset( $ticket->start_date ) ) {
			shopp_set_product_meta( $ticket->ID, 'shopptickets_start_date', $ticket->start_date );
		} else {
			shopp_rmv_product_meta( $ticket->ID, 'shopptickets_start_date' );
		}

		if ( isset( $ticket->end_date ) ) {
			shopp_set_product_meta( $ticket->ID, 'shopptickets_end_date', $ticket->end_date );
		} else {
			shopp_rmv_product_meta( $ticket->ID, 'shopptickets_end_date' );
		}
	}

	/**
	 * Deletes a ticket.
	 *
	 * @param $unused_event_id
	 * @param $ticket_id
	 * @return bool
	 */
	public function delete_ticket( $unused_event_id, $ticket_id ) {
		$delete = wp_delete_post( $ticket_id, true );

		/* Class exists check exists to avoid bumping Tribe__Tickets_Plus__Main::REQUIRED_TICKETS_VERSION
		 * during a minor release; as soon as we are able to do that though we can remove this safeguard.
		 *
		 * @todo remove class_exists() check once REQUIRED_TICKETS_VERSION >= 4.2
		 */
		if ( class_exists( 'Tribe__Tickets__Attendance' ) ) {
			Tribe__Tickets__Attendance::instance( $event_id )->increment_deleted_attendees_count();
		}

		return ( ! is_wp_error( $delete ) );
	}

	/**
	 * Assigns the specified ticket to a Shopp product category.
	 *
	 * @param $ticket_id
	 */
	protected function assign_to_category( $ticket_id ) {
		$category_id = $this->get_ticket_category();
		if ( false === $category_id ) {
			return;
		}
		shopp_product_add_categories( $ticket_id, array( $category_id ) );
	}

	/**
	 * Returns the ID of the category used for ticket assignments. If the ticket category does not exist
	 * then it will be created,
	 * @return bool|int
	 */
	protected function get_ticket_category() {
		$available = shopp_product_categories( array( 'index' => 'name' ) );

		// Do we need to create the category?
		if ( empty( $available ) || ! isset( $available[ $this->category_name ] ) ) {
			$description = apply_filters( 'shopptickets_ticket_category_description', '' );
			$parent = apply_filters( 'shopptickets_ticket_category_parent', false );
			$id = shopp_add_product_category( $this->category_name, $description, $parent );
		}
		// Otherwise obtain the ID of the existing category
		else {
			$id = $available[ $this->category_name ]->id;
		}

		return $id;
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

		$tickets = $this->get_tickets( $post->ID );
		if ( empty( $tickets ) ) {
			return;
		}

		$must_login = ! is_user_logged_in() && $this->login_required();
		include $this->getTemplateHierarchy( 'shopptickets/tickets' );
	}

	/**
	 * Returns all the tickets for an event
	 *
	 * @param int $event_id
	 * @return array
	 */
	protected function get_tickets( $event_id ) {
		$ticket_refs = $this->get_tickets_references( $event_id );

		if ( empty( $ticket_refs ) ) {
			return array();
		}

		$tickets = array();

		foreach ( $ticket_refs as $ref ) {
			$ticket = $this->get_ticket( $event_id, $ref->parent );
			if ( is_object( $ticket ) ) {
				$tickets[] = $ticket;
			}
		}

		return $tickets;
	}

	/**
	 * Gets an individual ticket
	 *
	 * @param $unused_event_id
	 * @param $ticket_id
	 * @return null|Tribe__Tickets__Ticket_Object
	 */
	public function get_ticket( $unused_event_id, $ticket_id ) {
		$product = shopp_product( $ticket_id );

		if ( ! $product ) {
			return null;
		}

		// We can't take it for granted that Shopp will have populated the product's price data
		$product->load_data( array( 'prices' ) );

		$return = new Tribe__Tickets__Ticket_Object();
		list( $start_date, $end_date ) = $this->ticket_sale_dates( $ticket_id );

		$return->description    = $product->description;
		$return->frontend_link  = get_permalink( $ticket_id );
		$return->ID             = $ticket_id;
		$return->name           = $product->name;
		$return->on_sale        = Shopp::str_true( $product->sale );
		$return->price          = $return->on_sale ? $this->get_sale_price( $product ) : $this->get_single_price( $product );
		$return->regular_price  = $this->get_single_price( $product );
		$return->provider_class = get_class( $this );
		$return->admin_link     = admin_url( sprintf( get_post_type_object( Product::$posttype )->_edit_link . '&action=edit', $ticket_id ) );
		$return->start_date     = empty( $start_date ) ? '' : $start_date;
		$return->end_date       = empty( $end_date ) ? '' : $end_date;

		$sold = (int) $product->sold;
		$pending = $product->sold ? $this->count_incomplete_order_items( $ticket_id ) : 0;

		$return->manage_stock( 'off' !== $product->inventory );
		$return->stock( ( 'off' === $product->inventory ) ? Tribe__Tickets__Ticket_Object::UNLIMITED_STOCK : ( $product->stock - $sold ) );
		$return->qty_sold( $sold - $pending );
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

		if ( '' === ( $event = shopp_product_meta( $ticket_product, $this->event_key ) ) ) {
			return false;
		}

		if ( in_array( get_post_type( $event ), Tribe__Tickets__Main::instance()->post_types() ) ) {
			return get_post( $event );
		}

		return false;
	}

	/**
	 * Returns an array of two elements, the first being the ticket start date and the second the
	 * ticket end date. If a date is not set / cannot be retrieved an empty string will be provided
	 * for that element.
	 *
	 * @param $ticket_id
	 * @return array
	 */
	protected function ticket_sale_dates( $ticket_id ) {
		$start_date = shopp_product_meta( $ticket_id, 'shopptickets_start_date' );
		$end_date = shopp_product_meta( $ticket_id, 'shopptickets_end_date' );

		if ( is_array( $start_date ) ) {
			$start_date = array_shift( $start_date );
		}

		if ( is_array( $end_date ) ) {
			$end_date = array_shift( $end_date );
		}

		return array(
			$start_date !== null ? $start_date : '',
			$end_date !== null ? $end_date : '',
		);
	}

	/**
	 * Get all the attendees for an event. It returns an array with the following fields:
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
	 *     attendee_id
	 *     security
	 *     optout
	 *     product_id
	 *     check_in
	 *     ticket
	 *
	 * @param int $event_id
	 * @return array
	 */
	protected function get_attendees( $event_id ) {
		$attendees_query = new WP_Query( array(
			'posts_per_page' => - 1,
			'post_type' => self::ATTENDEE_OBJECT,
			'meta_key' => self::ATTENDEE_EVENT_KEY,
			'meta_value' => $event_id,
			'orderby' => 'ID',
			'order' => 'DESC',
		) );

		if ( ! $attendees_query->have_posts() ) {
			return array();
		}

		$attendees = array();

		foreach ( $attendees_query->posts as $attendee ) {
			$order_id   = get_post_meta( $attendee->ID, self::ATTENDEE_ORDER_KEY, true );
			$checkin    = get_post_meta( $attendee->ID, $this->checkin_key, true );
			$security   = get_post_meta( $attendee->ID, $this->security_code, true );
			$optout     = (bool) get_post_meta( $attendee->ID, self::ATTENDEE_OPTOUT_KEY, true );
			$product_id = get_post_meta( $attendee->ID, self::ATTENDEE_PRODUCT_KEY, true );
			$user_id    = get_post_meta( $attendee->ID, self::ATTENDEE_USER_ID, true );

			$order_data = $this->get_order_data( $order_id );

			if ( false === $order_data ) {
				continue;
			}

			// Add the Attendee Data to the Order data
			$attendee_data = array_merge(
				$order_data,
				array(
					'attendee_id' => $attendee->ID,
					'security'    => $security,
					'optout'      => $optout,
					'product_id'  => $product_id,
					'check_in'    => $checkin,
					'ticket'      => $this->retrieve_product_name( $product_id, $order->purchased ),
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
			$attendee_data = apply_filters( 'tribe_tickets_attendee_data', $attendee_data, 'shopp', $attendee, $event_id );

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
		$order = shopp_order( $order_id );
		$customer = shopp_customer( $order->customer );


		if ( false === $order || false === $customer ) {
			return false;
		}

		$admin_url  = admin_url( sprintf( 'admin.php?page=shopp-orders&id=%d', $order_id ) );
		$admin_link = sprintf( '<a href="%s">%d</a>', $admin_url, $order_id );

		// Set warning flag for refunded, voided or declined transactions
		switch ( $order->txnstatus ) {
			case 'refunded':
			case 'voided':
			case 'auth-failed':
				$order_warning = true;
			break;

			default:
				$order_warning = false;
			break;
		}

		$data = array(
			'order_id'           => $order_id,
			'order_id_link'      => $admin_link,
			'order_id_link_src'  => $admin_url,
			'order_status'       => $order->txnstatus,
			'order_status_label' => $this->order_status_label( $order->txnstatus ),
			'order_warning'      => $order_warning,
			'purchaser_name'     => $customer->firstname . ' ' . $customer->lastname,
			'purchaser_email'    => $customer->email,
			'provider'           => __CLASS__,
			'provider_slug'      => 'shopp',
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
		$data = apply_filters( 'tribe_tickets_order_data', $data, 'shopp', $order_id );

		return $data;
	}

	/**
	 * Retrieves the name of the product based on the supplied ID and potentially the supplied
	 * array of purchases.
	 *
	 * In the first instance we'll simply load the product object. However, if it has been deleted
	 * then we still need to know the name of the purchased ticket - in that situation we will
	 * traverse the list of purchased items and retrieve the name from available historical data.
	 *
	 * If $this->use_historical_ticket_name evaluates as true then the name will always be pulled
	 * from the purchase data: this may be useful if the same product is used but the product name
	 * changes over time to reflect different states/offers and so on ("TribeConf - Early Bird
	 * Pricing", "TribeConf - Standard Admission", "TribeConf - Last Chance!", etc).
	 *
	 * @param $product_id
	 * @param array $purchases
	 * @return string
	 */
	protected function retrieve_product_name( $product_id, array $purchases ) {
		// If the product as alive and well try to return the current product name
		$product = shopp_product( $product_id );
		if ( false !== $product && ! $this->use_historical_ticket_name ) {
			return $product->name;
		}

		// If the product was deleted or use of the historic/time-of-purchase name is preferred...
		foreach ( $purchases as $purchased_item ) {
			if ( $purchased_item->product == $product_id ) {
				return $purchased_item->name;
			}
		}

		return __( 'Unknown item', 'event-tickets-plus' ); // Fallback for the unforeseen
	}

	/**
	 * Returns a human readable and translated order status label.
	 *
	 * We may wish to expand this to take account of mapped status labels in Shopp (where merchants
	 * can map their own label like "Completed" to an actual status of "Captured", etc).
	 *
	 * @param $status
	 * @return string
	 */
	protected function order_status_label( $status ) {
		$labels = Lookup::txnstatus_labels();
		return ( isset( $labels[ $status ] ) ) ? $labels[ $status ] : '';
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
		do_action( 'shopptickets_checkin', $attendee_id, $qr );

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
		do_action( 'shopptickets_uncheckin', $attendee_id );

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
				$sku   = get_post_meta( $ticket_id, '_sku', true );
			}
		}

		include $this->pluginPath . 'src/admin-views/shopp-metabox-advanced.php';
	}

	/**
	 * Shopp 1.3 will include reporting. Shopp 1.2.9 does not have a direct analogue we can use for
	 * this, however.
	 *
	 * @param $event_id
	 * @return string
	 */
	public function get_event_reports_link( $event_id ) {
		return '';
	}

	/**
	 * Shopp 1.3 will include reporting. Shopp 1.2.9 does not have a direct analogue we can use for
	 * this, however.
	 *
	 * @param $event_id
	 * @param $ticket_id
	 * @return string
	 */
	public function get_ticket_reports_link( $event_id, $ticket_id ) {
		return '';
	}

	/**
	 * Registers a metabox in the Shopp product editor with a link back to the product related Event.
	 */
	public function product_editor_meta_box() {
		add_meta_box( 'shopptickets_link_to_event_editor', __( 'Event', 'event-tickets-plus' ),
			array( $this, 'editor_meta_box_inside' ), Product::$posttype, 'side', 'low' );
	}

	/**
	 * Generates the actual product editor meta box content (linking users back to the event editor).
	 */
	public function editor_meta_box_inside() {
		$event_id = $this->get_related_event( get_the_ID() );
		if ( ! empty( $event_id ) ) {
			echo sprintf( '%s <a href="%s">%s</a>', esc_html__( 'This is a ticket for the event:', 'event-tickets-plus' ), esc_url( get_edit_post_link( $event_id ) ), esc_html( get_the_title( $event_id ) ) );
		}
	}

	/**
	 * Gets the product price in the correct money format per store settings.
	 *
	 * @param int|object $product
	 * @return string
	 */
	public function get_price_html( $product ) {
		$product = shopp_product( $product );
		if ( false === $product ) {
			return '';
		}

		return Shopp::money( $this->get_single_price( $product ) );
	}

	protected function get_single_price( ShoppProduct $product ) {
		if ( ! is_array( $product->prices ) ) {
			return 0;
		}

		reset( $product->prices );
		$single_price = current( $product->prices );

		if ( isset( $single_price->sale ) && 'on' === $single_price->sale ) {
			return $single_price->saleprice;
		}

		return $single_price->price;
	}

	protected function get_sale_price( ShoppProduct $product ) {
		if ( ! is_array( $product->prices ) ) {
			return 0;
		}

		reset( $product->prices );
		$single_price = current( $product->prices );
		return $single_price->saleprice;
	}

	public function get_tickets_references( $event_id ) {
		if ( is_object( $event_id ) ) {
			$event_id = $event_id->ID;
		}

		$meta_query = new ObjectMeta();
		$meta_query->load( array(
			'context' => 'product',
			'type' => 'meta',
			'name' => $this->event_key,
			'value' => $event_id,
		) );

		return (array) $meta_query->meta;
	}


	/**
	 * Adds an action to resend the tickets to the customer in the Shopp order screen.
	 *
	 * @param string $controls
	 * @return string
	 */
	public function add_resend_email_btn( $controls ) {
		$action = $this->resend_tickets_action;
		return $controls . '<div class="alignleft">
		      <button class="button button-secondary" id="' . $action . '" name="' . $action . '">'
			. __( 'Resend Tickets', 'event-tickets-plus' )
			. '</button> </div> ';
	}

	/**
	 * Listen for and handle requests to resend the tickets.
	 */
	public function resend_tickets_email() {
		// Sanity checks
		if ( ! isset( $_GET['id'] ) || ! isset( $_POST[ $this->resend_tickets_action ] ) || ! current_user_can( 'shopp_orders' ) ) {
			return;
		}

		check_admin_referer( 'meta-box-order', 'meta-box-order-nonce' );

		// Try to load the current order
		if ( false === ( $order = shopp_order( $_GET['id'] ) ) ) {
			return;
		}

		// Resend and add a notice event to record the fact
		$this->send_tickets( $order );
		$this->add_tickets_resent_note( $order->id );
	}

	/**
	 * Adds an order note to record that the tickets email was resent.
	 *
	 * @param Purchase $order
	 */
	protected function add_tickets_resent_note( $order_id ) {
		$Note = new MetaObject();
		$Note->parent = $order_id;
		$Note->context = 'purchase';
		$Note->type = 'order_note';
		$Note->name = 'note';
		$Note->value = new stdClass();
		$Note->value->author = wp_get_current_user()->ID;
		$Note->value->message = __( 'Email containing tickets (sent by ShoppTickets)', 'event-tickets-plus' );
		$Note->value->sent = true;
		$Note->save();
	}

	/**
	 * Helper to display a product quantity selector.
	 *
	 * Working directly with shopp('product.quantity') will fail if the correct context is not set up, so we
	 * will work directly with the underlying theme API methods.
	 *
	 * @param Product $product
	 * @return string
	 */
	public function quantity_selector( Product $product ) {
		$quantity_options = apply_filters( 'shopptickets_quantity_selector_options', array(
			'class' => 'selectall',
			'input' => 'menu',
			'options' => '0-15,20,25,30,40,50,75,100',
			'value' => 0,
		) );

		$product_id = esc_attr( $product->id );
		$product_ref = '<input type="hidden" name="products[' . $product_id . '][product]" value="' . $product_id . '" />';
		$selector = ShoppProductThemeAPI::quantity( '', $quantity_options, $product );

		return $product_ref . $selector;
	}

	/**
	 * Create and send an email containing the events tickets to the lucky recipient.
	 *
	 * @param Purchase $order
	 * @return string
	 */
	protected function send_tickets( Purchase $order ) {
		$attendees   = $this->get_attendees_by_order( $order );
		$content     = apply_filters( 'shopptickets_ticket_email_content', $this->generate_tickets_email_content( $attendees ) );
		$headers     = apply_filters( 'shopptickets_ticket_email_headers', array( 'Content-type: text/html' ) );
		$attachments = apply_filters( 'shopptickets_ticket_email_attachments', array() );
		$to          = apply_filters( 'shopptickets_ticket_email_recipient', $order->email );
		$subject     = apply_filters( 'shopptickets_ticket_email_subject',
			sprintf( __( 'Your tickets from %s', 'event-tickets-plus' ), shopp_setting( 'business_name' ) ) );

		wp_mail( $to, $subject, $content, $headers, $attachments );
	}

	/**
	 * Builds a list of all attendees for a given order.
	 *
	 * @param Purchase $order
	 * @return array
	 */
	protected function get_attendees_by_order( Purchase $order ) {
		$attendees = array();
		$query     = new WP_Query( array(
			'post_type'      => self::ATTENDEE_OBJECT,
			'meta_key'       => self::ATTENDEE_ORDER_KEY,
			'meta_value'     => $order->id,
			'posts_per_page' => -1,
		) );

		foreach ( $query->posts as $post ) {
			$product = get_post( get_post_meta( $post->ID, self::ATTENDEE_PRODUCT_KEY, true ) );
			$ticket_unique_id = get_post_meta( $post->ID, '_unique_id', true );
			$ticket_unique_id = $ticket_unique_id === '' ? $post->ID : $ticket_unique_id;

			$attendees[] = array(
				'event_id'      => get_post_meta( $post->ID, self::ATTENDEE_EVENT_KEY, true ),
				'product_id'    => $product->ID,
				'ticket_name'   => $product->post_title,
				'holder_name'   => $order->firstname . ' ' . $order->lastname,
				'order_id'      => $order->id,
				'ticket_id'     => $ticket_unique_id,
				'qr_ticket_id'  => $post->ID,
				'security_code' => get_post_meta( $post->ID, $this->security_code, true ),
			);
		}

		return $attendees;
	}

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
	 * @return Tribe__Tickets_Plus__Commerce__Shopp__Main
	 */
	public static function get_instance() {
		if ( ! self::$instance instanceof self ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	private function count_incomplete_order_items( $ticket_id ) {
		global $wpdb;
		$purchased_table_name   = $wpdb->prefix . 'shopp_purchased';
		$purchase_table_name    = $wpdb->prefix . 'shopp_purchase';
		$shopp_pending_statuses = implode( ',', $this->get_shopp_pending_statuses() );
		$q                      = sprintf( 'SELECT sum(p1.quantity) FROM %s p1 JOIN %s p2 ON p2.id = p1.purchase WHERE p1.product = %d AND p2.status in (%s);', $purchased_table_name, $purchase_table_name, $ticket_id, $shopp_pending_statuses );
		$count                  = $wpdb->get_col( $q );

		return ( is_array( $count ) && count( $count ) === 1 ) ? $count[0] : 0;
	}

	private function get_shopp_pending_statuses() {
		return array( 0 );
	}

	/**
	 * Excludes Shopp product post types from the list of supported post types that Tickets can be attached to
	 *
	 * @since 4.0.5
	 *
	 * @param array $post_types Array of supported post types
	 *
	 * @return array
	 */
	public function exclude_product_post_type( $post_types ) {
		if ( isset( $post_types['shopp_product'] ) ) {
			unset( $post_types['shopp_product'] );
		}

		return $post_types;
	}

	/**
	 * Ensures that tickets belonging to completed orders, or where the payment has been authorized
	 * or captured, are flagged as suitable for checkin.
	 *
	 * In Shopp, the order's transaction status (invoiced, authorized, captured etc) is divorced from
	 * the overall order status (completed or pending). While we still wish to accurately reflect the
	 * transaction status in the attendee list - because it communicates useful information - it
	 * generally shouldn't alone be used to dictate whether check in should be allowed or not.
	 *
	 * The role this filter plays is to check if the order itself has been marked as "complete" and,
	 * if so, it allows checkin by adding the transaction status to the list of statuses for which
	 * checkin facilities should be provided. It will also default to treating the "authed" and
	 * "captured" statuses as indicating the order is effectively complete.
	 *
	 * This filter executes on the "event_tickets_attendees_shopp_checkin_stati" hook and so
	 * further modifications are possible by adding an additional filter(s) at a higher-than-default
	 * priority.
	 *
	 * @param array $statuses
	 * @param int   $order_id
	 *
	 * @return array
	 */
	public function checkin_statuses( $statuses, $order_id ) {
		$order = shopp_order( $order_id );

		$allow_checkin = array(
			'authed',
			'captured',
		);

		// Orders with a status of 1 are complete, regardless of the actual transaction status
		if ( 1 === (int) $order->status && ! in_array( $order->txnstatus, $allow_checkin ) ) {
			$allow_checkin[] = $order->txnstatus;
		}

		return array_merge( $statuses, $allow_checkin );
	}
}
