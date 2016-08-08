<?php

if ( class_exists( 'Tribe__Tickets_Plus__Commerce__WooCommerce__Main' ) || ! class_exists( 'Tribe__Tickets__Tickets' ) ) {
	return;
}

class Tribe__Tickets_Plus__Commerce__WooCommerce__Main extends Tribe__Tickets_Plus__Tickets {
	/**
	 * Name of the CPT that holds Attendees (tickets holders).
	 */
	const ATTENDEE_OBJECT = 'tribe_wooticket';

	/**
	 * Meta key that relates Attendees and Products.
	 */
	const ATTENDEE_PRODUCT_KEY = '_tribe_wooticket_product';

	/**
	 * Meta key that relates Attendees and Orders.
	 */
	const ATTENDEE_ORDER_KEY = '_tribe_wooticket_order';

	/**
	 * Meta key that relates Attendees and Events.
	 */
	const ATTENDEE_EVENT_KEY = '_tribe_wooticket_event';

	/**
	 * Name of the CPT that holds Attendees (tickets holders).
	 *
	 * @deprecated use of the ATTENDEE_OBJECT class constant is preferred
	 *
	 * @var string
	 */
	public $attendee_object = 'tribe_wooticket';

	/**
	 * Meta key that relates Products and Events
	 * @var string
	 */
	public $event_key = '_tribe_wooticket_for_event';

	/**
	 * Meta key that stores if an attendee has checked in to an event
	 * @var string
	 */
	public $checkin_key = '_tribe_wooticket_checkedin';

	/**
	 * Meta key that relates Attendees and Products.
	 *
	 * @deprecated use of the ATTENDEE_PRODUCT_KEY class constant is preferred
	 *
	 * @var string
	 */
	public $atendee_product_key = '_tribe_wooticket_product';

	/**
	 * Meta key that relates Attendees and Orders.
	 *
	 * @deprecated use of the ATTENDEE_ORDER_KEY class constant is preferred
	 *
	 * @var string
	 */
	public $atendee_order_key = '_tribe_wooticket_order';

	/**
	 * Meta key that relates Attendees and Events.
	 *
	 * @deprecated use of the ATTENDEE_EVENT_KEY class constant is preferred
	 *
	 * @var string
	 */
	public $atendee_event_key = '_tribe_wooticket_event';

	/**
	 * Meta key that holds the security code that's printed in the tickets
	 * @var string
	 */
	public $security_code = '_tribe_wooticket_security_code';

	/**
	 * Meta key that holds if an order has tickets (for performance)
	 * @var string
	 */
	public $order_has_tickets = '_tribe_has_tickets';

	/**
	 * Meta key that will keep track of whether the confirmation mail for a ticket has been sent to the user or not.
	 * @var string
	 */
	public $mail_sent_meta_key = '_tribe_mail_sent';

	/**
	 * Meta key that holds the name of a ticket to be used in reports if the Product is deleted
	 * @var string
	 */
	public $deleted_product = '_tribe_deleted_product_name';

	/**
	 * Name of the ticket objects CPT.
	 * @var string
	 */
	public $ticket_object = 'product';

	/**
	 * Meta key that holds if the attendee has opted out of the front-end listing
	 * @var string
	 */
	const ATTENDEE_OPTOUT_KEY = '_tribe_wooticket_attendee_optout';

	/**
	 * Holds an instance of the Tribe__Tickets_Plus__Commerce__WooCommerce__Email class
	 * @var Tribe__Tickets_Plus__Commerce__WooCommerce__Email
	 */
	private $mailer = null;

	/** @var Tribe__Tickets_Plus__Commerce__WooCommerce__Settings */
	private $settings;

	/**
	 * Instance of this class for use as singleton
	 */
	private static $instance;

	/**
	 * Instance of Tribe__Tickets_Plus__Commerce__WooCommerce__Meta
	 */
	private static $meta;

	/**
	 * @var Tribe__Tickets_Plus__Commerce__WooCommerce__Global_Stock
	 */
	private static $global_stock;

	/**
	 * @var Tribe__Tickets_Plus__Commerce__WooCommerce__CheckIn_Stati
	 */
	protected $checkin_stati;

	/**
	 * Current version of this plugin
	 */
	const VERSION = '3.12a1';

	/**
	 * Min required The Events Calendar version
	 */
	const REQUIRED_TEC_VERSION = '3.11';

	/**
	 * Min required WooCommerce version
	 */
	const REQUIRED_WC_VERSION = '2.1';

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
	 * @return Tribe__Tickets_Plus__Commerce__WooCommerce__Main
	 */
	public static function get_instance() {
		if ( ! self::$instance instanceof self ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Class constructor
	 */
	public function __construct() {
		/* Set up some parent's vars */
		$this->pluginName = 'WooCommerce';
		$this->pluginSlug = 'wootickets';
		$this->pluginPath = trailingslashit( EVENT_TICKETS_PLUS_DIR );
		$this->pluginDir  = trailingslashit( basename( $this->pluginPath ) );
		$this->pluginUrl  = trailingslashit( plugins_url( $this->pluginDir ) );

		parent::__construct();

		$this->hooks();
		$this->orders_report();
		$this->global_stock();
		$this->meta();
		$this->settings();
	}

	/**
	 * Registers all actions/filters
	 */
	public function hooks() {
		add_action( 'wp_loaded', array( $this, 'process_front_end_tickets_form' ), 50 );
		add_action( 'init', array( $this, 'register_wootickets_type' ) );
		add_action( 'init', array( $this, 'register_resources' ) );
		add_action( 'add_meta_boxes', array( $this, 'woocommerce_meta_box' ) );
		add_action( 'before_delete_post', array( $this, 'handle_delete_post' ) );
		add_action( 'woocommerce_order_status_changed', array( $this, 'generate_tickets' ), 12, 3 );
		add_action( 'woocommerce_order_status_completed', array( $this, 'on_complete_order' ), 12 );
		add_action( 'woocommerce_payment_successful_result', array( $this, 'maybe_complete_order' ), 10, 2 );
		add_action( 'woocommerce_email_after_order_table', array( $this, 'add_tickets_msg_to_email' ), 10, 2  );
		add_action( 'woocommerce_add_order_item_meta', array( $this, 'set_attendee_optout_choice' ), 15, 2 );
		add_filter( 'woocommerce_hidden_order_itemmeta', array( $this, 'hide_attendee_optout_choice' ), 15 );

		if ( class_exists( 'Tribe__Events__API' ) ) {
			add_action( 'woocommerce_product_quick_edit_save', array( $this, 'syncronize_product_editor_changes' ) );
			add_action( 'woocommerce_process_product_meta_simple', array( $this, 'syncronize_product_editor_changes' ) );
		}

		// Enqueue styles
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles' ), 11 );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_styles' ), 11 );

		add_filter( 'post_type_link', array( $this, 'hijack_ticket_link' ), 10, 4  );
		add_filter( 'woocommerce_email_classes', array( $this, 'add_email_class_to_woocommerce' ) );

		add_action( 'woocommerce_resend_order_emails_available', array( $this, 'add_resend_tickets_action' ) );

		add_filter( 'event_tickets_attendees_woo_checkin_stati', array( $this->checkin_stati(), 'filter_attendee_ticket_checkin_stati' ), 10 );
		add_filter( 'tribe_tickets_settings_post_types', array( $this, 'exclude_product_post_type' ) );
	}

	public function register_resources() {
		$stylesheet_url = $this->pluginUrl . 'src/resources/css/wootickets.css';

		// Get minified CSS if it exists
		$stylesheet_url = Tribe__Template_Factory::getMinFile( $stylesheet_url, true );

		// apply filters
		$stylesheet_url = apply_filters( 'tribe_wootickets_stylesheet_url', $stylesheet_url );

		wp_register_style( 'TribeEventsWooTickets', $stylesheet_url, array(), apply_filters( 'tribe_events_wootickets_css_version', self::VERSION ) );

		//Check for override stylesheet
		$user_stylesheet_url = Tribe__Tickets__Templates::locate_stylesheet( 'tribe-events/wootickets/wootickets.css' );
		$user_stylesheet_url = apply_filters( 'tribe_events_wootickets_stylesheet_url', $user_stylesheet_url );

		//If override stylesheet exists, then enqueue it
		if ( $user_stylesheet_url ) {
			wp_register_style( 'tribe-events-wootickets-override-style', $user_stylesheet_url );
		}
	}

	/**
	 * After placing the Order make sure we store the users option to show the Attendee Optout
	 * @param int $item_id
	 * @param array $item
	 */
	public function set_attendee_optout_choice( $item_id, $item ) {
		// If this option is not here just drop
		if ( ! isset( $item['attendee_optout'] ) ) {
			return;
		}
		wc_add_order_item_meta( $item_id, self::ATTENDEE_OPTOUT_KEY, $item['attendee_optout'] );
	}


	/**
	 * Hide the Attendee Output Choice in the Order Page
	 *
	 * @param $order_items
	 *
	 * @return array
	 */
	public function hide_attendee_optout_choice( $order_items ) {
		$order_items[] = self::ATTENDEE_OPTOUT_KEY;

		return $order_items;
	}

	/**
	 * Orders report object accessor method
	 *
	 * @return Tribe__Tickets_Plus__Commerce__WooCommerce__Orders__Report
	 */
	public function orders_report() {
		static $report;

		if ( ! $report instanceof self ) {
			$report = new Tribe__Tickets_Plus__Commerce__WooCommerce__Orders__Report;
		}

		return $report;
	}

	/**
	 * Custom meta integration object accessor method
	 *
	 * @since 4.1
	 *
	 * @return Tribe__Tickets_Plus__Commerce__WooCommerce__Meta
	 */
	public function meta() {
		if ( ! self::$meta ) {
			self::$meta = new Tribe__Tickets_Plus__Commerce__WooCommerce__Meta;
		}

		return self::$meta;
	}

	/**
	 * Provides a copy of the global stock integration object.
	 *
	 * @since 4.1
	 *
	 * @return Tribe__Tickets_Plus__Commerce__WooCommerce__Global_Stock
	 */
	public function global_stock() {
		if ( ! self::$global_stock ) {
			self::$global_stock = new Tribe__Tickets_Plus__Commerce__WooCommerce__Global_Stock;
		}

		return self::$global_stock;
	}

	public function settings() {
		if ( empty( $this->settings ) ) {
			$this->settings = new Tribe__Tickets_Plus__Commerce__WooCommerce__Settings;
		}

		return $this->settings;
	}

	/**
	 * Enqueue the plugin stylesheet(s).
	 *
	 * @author caseypicker
	 * @since 3.9
	 * @return void
	 */
	public function enqueue_styles() {
		//Only enqueue wootickets styles on singular event page
		if ( is_singular( Tribe__Tickets__Main::instance()->post_types() ) ) {
			wp_enqueue_style( 'TribeEventsWooTickets' );
			wp_enqueue_style( 'tribe-events-wootickets-override-style' );
		}
	}

	public function admin_enqueue_styles() {
		wp_enqueue_style( 'TribeEventsWooTickets' );
		wp_enqueue_style( 'tribe-events-wootickets-override-style' );
	}

	/**
	 * If a ticket is edited via the WooCommerce product editor (vs the ticket meta
	 * box exposed in the event editor) then we need to trigger an update to ensure
	 * cost meta in particular stays up-to-date on our side.
	 *
	 * @param $product_id
	 */
	public function syncronize_product_editor_changes( $product_id ) {
		$event = $this->get_event_for_ticket( $product_id );

		// This product is not connected with an event
		if ( ! $event ) {
			return;
		}

		// Trigger an update
		Tribe__Events__API::update_event_cost( $event->ID );
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
		if ( get_post_type( $post_to_delete ) !== 'product' ) {
			return;
		}

		// Bail if the product is not a Ticket
		$event = get_post_meta( $post_id, $this->event_key, true );
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
	 * Add a custom email handler to WooCommerce email system
	 *
	 * @param array $classes of WC_Email objects
	 *
	 * @return array of WC_Email objects
	 */
	public function add_email_class_to_woocommerce( $classes ) {
		$this->mailer                    = new Tribe__Tickets_Plus__Commerce__WooCommerce__Email();
		$classes['Tribe__Tickets__Woo__Email'] = $this->mailer;

		return $classes;
	}

	/**
	 * Register our custom post type
	 */
	public function register_wootickets_type() {
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
	 * If an order containing tickets has been placed and the complete-orders-automatically
	 * option is enabled, then modify the order status accordingly.
	 *
	 * @param array $result (is passed through unmodified)
	 * @param int   $order_id
	 *
	 * @return array
	 */
	public function maybe_complete_order( array $result, $order_id ) {
		// Only interfere if the autocomplete-option has been enabled
		if ( ! tribe_get_option( 'tickets-woo-autocomplete', false ) ) {
			return $result;
		}

		// Already completed? Go no further
		if ( 'wc-completed' === get_post_status( $order_id ) ) {
			return $result;
		}

		$order = wc_get_order( $order_id );
		$contains_ticket = false;

		// Search through order to see if it contains a ticket product
		foreach ( $order->get_items() as $product_id ) {
			if ( tribe_events_product_is_ticket( $product_id ) ) {
				$contains_ticket = true;
				break;
			}
		}

		// No ticket products found? Go no further
		if ( ! $contains_ticket ) {
			return $result;
		}

		// Set status to complete
		$order->update_status( 'completed' );
		return $result;
	}

	/**
	 * Checks if a Order has Tickets
	 * @param  int  $order_id
	 * @return boolean
	 */
	public function order_has_tickets( $order_id ) {
		$has_tickets = false;

		$done = get_post_meta( $order_id, $this->order_has_tickets, true );
		/**
		 * get_post_meta returns empty string when the meta doesn't exists
		 * in support 2 possible values:
		 * - Empty string which will do the logic using WC_Order below
		 * - Cast boolean the return of the get_post_meta
		 */
		if ( '' !== $done ) {
			return (bool) $done;
		}

		// Get the items purchased in this order
		$order       = new WC_Order( $order_id );
		$order_items = $order->get_items();

		// Bail if the order is empty
		if ( empty( $order_items ) ) {
			return $has_tickets;
		}

		// Iterate over each product
		foreach ( (array) $order_items as $item_id => $item ) {
			$product_id = isset( $item['product_id'] ) ? $item['product_id'] : $item['id'];
			// Get the event this tickets is for
			$event_id = get_post_meta( $product_id, $this->event_key, true );

			if ( ! empty( $event_id ) ) {

				$has_tickets = true;
				break;
			}
		}

		return $has_tickets;
	}

	/**
	 * Performs ticket order completion actions.
	 *
	 * @param int $order_id
	 */
	public function on_complete_order( $order_id ) {
		if ( ! $this->order_has_tickets( $order_id ) ) {
			return;
		}

		$this->generate_tickets( $order_id, 'completed', 'completed' );
		$this->complete_order( $order_id );
	}

	/**
	 * Generate and store all the attendees information for a new order when changing the status to one that will affect
	 * the stock amount.
	 *
	 * @param int $order_id
	 * @param string $old_status
	 * @param string $new_status
	 */
	public function generate_tickets( $order_id, $old_status, $new_status ) {
		// check that the new status is one that's affecting the stock, WooCommerce defaults
		$default_generating_order_stati = array( 'completed', 'on-hold', 'processing' );

		/**
		 * Filters the list of ticket order stati that should trigger the ticket generation.
		 *
		 * By default the WooCommerced default ones that will affect the ticket stock.
		 *
		 * @since 4.2
		 *
		 * @param array $default_generating_order_stati An array of the default WooCommerce order stati affecting the stock.
		 */
		$generating_order_stati = apply_filters( 'event_tickets_woo_ticket_generating_order_stati', $default_generating_order_stati );

		if ( ! in_array( $new_status, $generating_order_stati ) ) {
			return;
		}

		// Bail if we already generated the info for this order
		$done = get_post_meta( $order_id, $this->order_has_tickets, true );
		if ( ! empty( $done ) ) {
			return;
		}

		$has_tickets = false;
		// Get the items purchased in this order

		$order       = new WC_Order( $order_id );
		$order_items = $order->get_items();

		// Bail if the order is empty
		if ( empty( $order_items ) ) {
			return;
		}

		// Iterate over each product
		foreach ( (array) $order_items as $item_id => $item ) {
			$order_attendee_id = 0;
			$product_id = isset( $item['product_id'] ) ? $item['product_id'] : $item['id'];

			// Store the Optout in the Attendee, from the Order Item
			if ( isset( $item['item_meta'][ self::ATTENDEE_OPTOUT_KEY ] ) ) {
				$optout = (bool) reset( $item['item_meta'][ self::ATTENDEE_OPTOUT_KEY ] );
			} else {
				$optout = false;
			}

			// Get the event this tickets is for
			$event_id = get_post_meta( $product_id, $this->event_key, true );

			if ( ! empty( $event_id ) ) {

				$has_tickets = true;

				// Iterate over all the amount of tickets purchased (for this product)
				$quantity = intval( $item['qty'] );
				for ( $i = 0; $i < $quantity; $i ++ ) {

					$attendee = array(
						'post_status' => 'publish',
						'post_title'  => $order_id . ' | ' . $item['name'] . ' | ' . ( $i + 1 ),
						'post_type'   => self::ATTENDEE_OBJECT,
						'ping_status' => 'closed',
					);

					// Insert individual ticket purchased
					$attendee = apply_filters( 'wootickets_attendee_insert_args', $attendee, $order_id, $product_id, $event_id );

					if ( $attendee_id = wp_insert_post( $attendee ) ) {
						update_post_meta( $attendee_id, self::ATTENDEE_PRODUCT_KEY, $product_id );
						update_post_meta( $attendee_id, self::ATTENDEE_ORDER_KEY, $order_id );
						update_post_meta( $attendee_id, self::ATTENDEE_EVENT_KEY, $event_id );
						update_post_meta( $attendee_id, self::ATTENDEE_OPTOUT_KEY, $optout );
						update_post_meta( $attendee_id, $this->security_code, $this->generate_security_code( $order_id, $attendee_id ) );

						/**
						 * WooCommerce-specific action fired when a WooCommerce-driven attendee ticket is generated
						 *
						 * @deprecated 4.1
						 *
						 * @param $attendee_id ID of attendee ticket
						 * @param $event_id ID of event
						 * @param $order WooCommerce order
						 * @param $product_id WooCommerce product ID
						 */
						do_action( 'wootickets_generate_ticket_attendee', $attendee_id, $event_id, $order, $product_id );

						/**
						 * WooCommerce-specific action fired when a WooCommerce-driven attendee ticket for an event is generated
						 *
						 * @param $attendee_id ID of attendee ticket
						 * @param $event_id ID of event
						 * @param $order WooCommerce order
						 * @param $product_id WooCommerce product ID
						 */
						do_action( 'event_ticket_woo_attendee_created', $attendee_id, $event_id, $order, $product_id );

						/**
						 * Action fired when an attendee ticket is generated
						 *
						 * @param $attendee_id ID of attendee ticket
						 * @param $order_id WooCommerce order ID
						 * @param $product_id Product ID attendee is "purchasing"
						 * @param $order_attendee_id Attendee # for order
						 */
						do_action( 'event_tickets_woocommerce_ticket_created', $attendee_id, $order_id, $product_id, $order_attendee_id );

						$this->record_attendee_user_id( $attendee_id );
						$order_attendee_id++;
					}
				}
			}

			/**
			 * Action fired when a WooCommerce has had attendee tickets generated for it
			 *
			 * @param $product_id RSVP ticket post ID
			 * @param $order_id ID of the WooCommerce order
			 * @param $quantity Quantity ordered
			 */
			do_action( 'event_tickets_woocommerce_tickets_generated_for_product', $product_id, $order_id, $quantity );
		}

		if ( $has_tickets ) {
			update_post_meta( $order_id, $this->order_has_tickets, '1' );

			$default_complete_stati = array( 'completed' );

			/**
			 * Allows filtering the list of stati that mark a ticket order as complete
			 *
			 * @since 4.2
			 *
			 * @param array $default_complete_stati An array of default order stati WooCommerce will mark as completed.
			 */
			$complete_stati = apply_filters( 'event_tickets_woo_complete_order_stati', $default_complete_stati );

			$mail_sent         = get_post_meta( $order_id, $this->mail_sent_meta_key, true );
			if ( in_array( $new_status, $complete_stati ) && ! ( empty( $mail_sent ) ) ) {
				$this->complete_order( $order_id );
			}
		}

		/**
		 * Action fired when a WooCommerce attendee tickets have been generated
		 *
		 * @param $order_id ID of the WooCommerce order
		 */
		do_action( 'event_tickets_woocommerce_tickets_generated', $order_id );
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
	 * Adds a message to WooCommerce's order email confirmation.
	 * @param $order
	 */
	public function add_tickets_msg_to_email( $order ) {
		$order_items = $order->get_items();

		// Bail if the order is empty
		if ( empty( $order_items ) ) {
			return;
		}

		$has_tickets = false;

		// Iterate over each product
		foreach ( (array) $order_items as $item ) {

			$product_id = isset( $item['product_id'] ) ? $item['product_id'] : $item['id'];

			// Get the event this tickets is for
			$event_id = get_post_meta( $product_id, $this->event_key, true );

			if ( ! empty( $event_id ) ) {
				$has_tickets = true;
				break;
			}
		}

		if ( ! $has_tickets ) {
			return;
		}

		echo '<br/>' . apply_filters( 'wootickets_email_message', esc_html__( "You'll receive your tickets in another email.", 'event-tickets-plus' ) );
	}

	/**
	 * Saves a given ticket (WooCommerce product)
	 *
	 * @param int $event_id
	 * @param Tribe__Tickets__Ticket_Object $ticket
	 * @param array $raw_data
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
				'post_type'    => 'product',
				'post_author'  => get_current_user_id(),
				'post_excerpt' => $ticket->description,
				'post_title'   => $ticket->name,
			);

			$ticket->ID = wp_insert_post( $args );

			update_post_meta( $ticket->ID, '_visibility', 'hidden' );
			update_post_meta( $ticket->ID, '_tax_status', 'taxable' );
			update_post_meta( $ticket->ID, '_tax_class', '' );
			update_post_meta( $ticket->ID, '_purchase_note', '' );
			update_post_meta( $ticket->ID, '_weight', '' );
			update_post_meta( $ticket->ID, '_length', '' );
			update_post_meta( $ticket->ID, '_width', '' );
			update_post_meta( $ticket->ID, '_height', '' );
			update_post_meta( $ticket->ID, '_downloadable', 'no' );
			update_post_meta( $ticket->ID, '_virtual', 'yes' );
			update_post_meta( $ticket->ID, '_sale_price_dates_from', '' );
			update_post_meta( $ticket->ID, '_sale_price_dates_to', '' );
			update_post_meta( $ticket->ID, '_product_attributes', array() );
			update_post_meta( $ticket->ID, '_sale_price', '' );
			update_post_meta( $ticket->ID, 'total_sales', 0 );

			/**
			 * Toggle filter to allow skipping the automatic SKU generation.
			 *
			 * @param bool $should_default_ticket_sku
			 */
			$should_default_ticket_sku = apply_filters( 'event_tickets_woo_should_default_ticket_sku', true );
			if ( $should_default_ticket_sku ) {
				// make sure the SKU is set to the correct value
				if ( ! empty( $raw_data['ticket_woo_sku'] ) ) {
					$sku = $raw_data['ticket_woo_sku'];
				} else {
					$post_author                = get_post( $ticket->ID )->post_author;
					$sku                        = "{$ticket->ID}-{$post_author}-" . sanitize_title( $raw_data['ticket_name'] );
					$raw_data['ticket_woo_sku'] = $sku;
				}
				update_post_meta( $ticket->ID, '_sku', $sku );
			}

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

		if ( ! $ticket->ID ) {
			return false;
		}

		/**
		 * Allow for the prevention of updating ticket price on update.
		 *
		 * @var boolean
		 * @var WP_Post
		 */
		$can_update_ticket_price = apply_filters( 'tribe_tickets_can_update_ticket_price', true, $ticket );

		if ( $can_update_ticket_price ) {
			update_post_meta( $ticket->ID, '_regular_price', $ticket->price );

			// Do not update _price if the ticket is on sale: the user should edit this in the WC product editor
			if ( ! wc_get_product( $ticket->ID )->is_on_sale() || 'create' === $save_type ) {
				update_post_meta( $ticket->ID, '_price', $ticket->price );
			}
		}

		$global_stock_mode = isset( $raw_data['ticket_global_stock'] )
			? filter_var( $raw_data['ticket_global_stock'], FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH )
			: '';

		$global_stock_cap = isset( $raw_data['ticket_woo_global_stock_cap'] )
			? (int) $raw_data['ticket_woo_global_stock_cap']
			: 0;

		update_post_meta( $ticket->ID, '_global_stock_mode', $global_stock_mode );
		update_post_meta( $ticket->ID, '_global_stock_cap', $global_stock_cap );

		$stock_provided = trim( $raw_data['ticket_woo_stock'] ) !== '';
		$has_global_stock = ( 'global' === $global_stock_mode || 'capped' === $global_stock_mode );

		if ( $stock_provided || $has_global_stock ) {
			$stock = $has_global_stock
				? $this->global_stock_level( $event_id )
				: (int) $raw_data['ticket_woo_stock'];

			$status = ( 0 < $stock ) ? 'instock' : 'outofstock';

			update_post_meta( $ticket->ID, '_stock', $stock );
			update_post_meta( $ticket->ID, '_stock_status', $status );
			update_post_meta( $ticket->ID, '_backorders', 'no' );
			update_post_meta( $ticket->ID, '_manage_stock', 'yes' );
			delete_transient( 'wc_product_total_stock_' . $ticket->ID );
		} else {
			update_post_meta( $ticket->ID, '_manage_stock', 'no' );
		}

		if ( isset( $raw_data['ticket_woo_sku'] ) )
			update_post_meta( $ticket->ID, '_sku', $raw_data['ticket_woo_sku'] );

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

		if ( isset( $ticket->purchase_limit ) ) {
			update_post_meta( $ticket->ID, '_ticket_purchase_limit', absint( $ticket->purchase_limit ) );
		} else {
			delete_post_meta( $ticket->ID, '_ticket_purchase_limit' );
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

		/**
		 * Commerce-specific action fired after saving a ticket
		 *
		 * @param int Ticket ID
		 * @param int Post ID of post the ticket is tied to
		 * @param array Ticket data
		 */
		do_action( 'wootickets_after_' . $save_type . '_ticket', $ticket->ID, $event_id, $raw_data );

		/**
		 * Commerce-specific action fired after saving a ticket
		 *
		 * @param int Ticket ID
		 * @param int Post ID of post the ticket is tied to
		 * @param array Ticket data
		 */
		do_action( 'wootickets_after_save_ticket', $ticket->ID, $event_id, $raw_data );

		return $ticket->ID;
	}

	/**
	 * Deletes a ticket
	 *
	 * @param $event_id
	 * @param $ticket_id
	 *
	 * @return bool
	 */
	public function delete_ticket( $event_id, $ticket_id ) {
		// Ensure we know the event and product IDs (the event ID may not have been passed in)
		if ( empty( $event_id ) ) {
			$event_id = get_post_meta( $ticket_id, self::ATTENDEE_EVENT_KEY, true );
		}
		$product_id = get_post_meta( $ticket_id, self::ATTENDEE_PRODUCT_KEY, true );

		// Try to kill the actual ticket/attendee post
		$delete = wp_delete_post( $ticket_id, true );
		if ( is_wp_error( $delete ) ) {
			return false;
		}

		/* Class exists check exists to avoid bumping Tribe__Tickets_Plus__Main::REQUIRED_TICKETS_VERSION
		 * during a minor release; as soon as we are able to do that though we can remove this safeguard.
		 *
		 * @todo remove class_exists() check once REQUIRED_TICKETS_VERSION >= 4.2
		 */
		if ( class_exists( 'Tribe__Tickets__Attendance' ) ) {
			Tribe__Tickets__Attendance::instance( $event_id )->increment_deleted_attendees_count();
		}

		// Re-stock the product inventory (on the basis that a "seat" has just been freed)
		$this->increment_product_inventory( $product_id );

		do_action( 'wootickets_ticket_deleted', $ticket_id, $event_id, $product_id );
		return true;
	}

	/**
	 * Increments the inventory of the specified product by 1 (or by the optional
	 * $increment_by value).
	 *
	 * @param int $product_id
	 * @param int $increment_by
	 *
	 * @return bool
	 */
	protected function increment_product_inventory( $product_id, $increment_by = 1 ) {
		$product = wc_get_product( $product_id );

		if ( ! $product || ! $product->managing_stock() ) {
			return false;
		}

		// set_stock() returns the new inventory or null on failure
		return null !== $product->set_stock( (int) $product->stock + $increment_by );
	}

	/**
	 * Returns all the tickets for an event
	 *
	 * @param int $event_id
	 *
	 * @return array
	 */
	public function get_tickets( $event_id ) {
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
	 * Replaces the link to the WC product with a link to the Event in the
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
		if ( $post->post_type === 'product' ) {
			$event = get_post_meta( $post->ID, $this->event_key, true );
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

		if ( ! empty( $post->post_parent ) ) {
			$post = get_post( $post->post_parent );
		}

		$tickets = self::get_tickets( $post->ID );

		if ( empty( $tickets ) )
			return;

		$must_login = ! is_user_logged_in() && $this->login_required();
		$global_stock_enabled = $this->uses_global_stock( $post->ID );
		Tribe__Tickets__Tickets::add_frontend_stock_data( $tickets );

		include $this->getTemplateHierarchy( 'wootickets/tickets' );
	}

	/**
	 * Grabs the submitted front end tickets form and adds the products
	 * to the cart
	 */
	public function process_front_end_tickets_form() {
		global $woocommerce;

		if ( empty( $_REQUEST['wootickets_process'] ) || intval( $_REQUEST['wootickets_process'] ) !== 1 || empty( $_POST['product_id'] ) ) {
			return;
		}

		foreach ( (array) $_POST['product_id'] as $product_id ) {
			$quantity = isset( $_POST[ 'quantity_' . $product_id ] ) ? intval( $_POST[ 'quantity_' . $product_id ] ) : 0;
			$optout = isset( $_POST[ 'optout_' . $product_id ] ) ? (bool) $_POST[ 'optout_' . $product_id ] : false;
			$passed_validation = apply_filters( 'woocommerce_add_to_cart_validation', true, $product_id, $quantity );
			$cart_data = array(
				'attendee_optout' => $optout,
			);

			if ( $passed_validation && $quantity > 0 ) {
				$woocommerce->cart->add_to_cart( $product_id, $quantity, 0, array(), $cart_data );
			}
		}
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
		if ( class_exists( 'WC_Product_Simple' ) ) {
			$product = new WC_Product_Simple( $ticket_id );
		} else {
			$product = new WC_Product( $ticket_id );
		}

		if ( ! $product ) {
			return null;
		}

		$return       = new Tribe__Tickets__Ticket_Object();
		$product_data = $product->get_post_data();
		$qty          = get_post_meta( $ticket_id, 'total_sales', true );

		$return->description    = $product_data->post_excerpt;
		$return->frontend_link  = get_permalink( $ticket_id );
		$return->ID             = $ticket_id;
		$return->name           = $product->get_title();
		$return->price          = $product->get_price();
		$return->regular_price  = $product->get_regular_price();
		$return->on_sale        = (bool) $product->is_on_sale();
		$return->provider_class = get_class( $this );
		$return->admin_link     = admin_url( sprintf( get_post_type_object( $product_data->post_type )->_edit_link . '&action=edit', $ticket_id ) );
		$return->start_date     = get_post_meta( $ticket_id, '_ticket_start_date', true );
		$return->end_date       = get_post_meta( $ticket_id, '_ticket_end_date', true );
		$return->purchase_limit = get_post_meta( $ticket_id, '_ticket_purchase_limit', true );

		$complete_totals = $this->count_order_items_by_status( $ticket_id, 'complete' );
		$pending_totals = $this->count_order_items_by_status( $ticket_id, 'incomplete' );
		$qty = $qty ? $qty : 0;
		$pending = $pending_totals['total'] ? $pending_totals['total'] : 0;

		// Ticket stock is a simple reflection of remaining inventory for this item...
		$stock = $product->get_stock_quantity();

		// ...With some exceptions for global stock tickets
		$stock = $this->set_stock_level_for_global_stock_tickets( $stock, $event_id, $ticket_id );

		$return->manage_stock( $product->managing_stock() );
		$return->stock( $stock );
		$return->global_stock_mode( get_post_meta( $ticket_id, '_global_stock_mode', true ) );
		$return->global_stock_cap( get_post_meta( $ticket_id, '_global_stock_cap', true ) );
		$return->qty_sold( $qty );
		$return->qty_pending( $pending );
		$return->qty_cancelled( $this->get_cancelled( $ticket_id ) );

		if ( empty( $return->purchase_limit ) && 0 !== (int) $return->purchase_limit ) {
			/**
			 * Filter the default purchase limit for the ticket
			 *
			 * @var int
			 *
			 * @return int
			 */
			$return->purchase_limit = apply_filters( 'tribe_tickets_default_purchase_limit', 0 );
		}

		return apply_filters( 'wootickets_get_ticket', $return, $event_id, $ticket_id );
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
		// If this event does not have a global stock then do not modify the existing stock level
		if ( ! $this->uses_global_stock( $event_id ) ) {
			return $existing_stock;
		}

		// If this specific ticket maintains its own independent stock then again do not interfere
		if ( Tribe__Tickets__Global_Stock::OWN_STOCK_MODE === get_post_meta( $ticket_id, '_global_stock_mode', true ) ) {
			return $existing_stock;
		}

		// Otherwise the ticket stock ought to match the current global stock
		$actual_stock = wc_get_product( $ticket_id )->get_stock_quantity();
		$global_stock = $this->global_stock_level( $event_id );

		// Look out for and correct discrepancies where the actual stock is zero but the global stock is non-zero
		if ( 0 == $actual_stock && 0 < $global_stock ) {
			update_post_meta( $ticket_id, '_stock', $global_stock );
			update_post_meta( $ticket_id, '_stock_status', 'instock' );
		}

		return $global_stock;
	}

	/**
	 * Determine the total number of the specified ticket contained in orders which have
	 * progressed to a "completed" or "incomplete" status.
	 *
	 * Essentially this returns the total quantity of tickets held within orders that are
	 * complete or incomplete (incomplete are: "pending", "on hold" or "processing").
	 *
	 * @param int $ticket_id
	 * @param string $status Types of orders: incomplete or complete
	 * @return int
	 */
	protected function count_order_items_by_status( $ticket_id, $status = 'incomplete' ) {
		$totals = array(
			'total' => 0,
			'recorded_sales' => 0,
			'reduced_stock' => 0,
		);

		$incomplete_orders = version_compare( '2.2', WooCommerce::instance()->version, '<=' )
			? $this->get_orders_by_status( $ticket_id, $status ) : $this->backcompat_get_orders_by_status( $ticket_id, $status );

		foreach ( $incomplete_orders as $order_id ) {
			$order = new WC_Order( $order_id );

			$has_recorded_sales = 'yes' === get_post_meta( $order_id, '_recorded_sales', true );
			$has_reduced_stock = (bool) get_post_meta( $order_id, '_order_stock_reduced', true );

			foreach ( (array) $order->get_items() as $order_item ) {
				if ( $order_item['product_id'] == $ticket_id ) {
					$totals['total'] += (int) $order_item['qty'];
					if ( $has_recorded_sales ) {
						$totals['recorded_sales'] += (int) $order_item['qty'];
					}

					if ( $has_reduced_stock ) {
						$totals['reduced_stock'] += (int) $order_item['qty'];
					}
				}
			}
		}

		return $totals;
	}

	protected function get_orders_by_status( $ticket_id, $status = 'incomplete' ) {
		global $wpdb;

		$order_state_sql = '';
		$incomplete_states = $this->incomplete_order_states();

		if ( ! empty( $incomplete_states ) ) {
			if ( 'incomplete' === $status ) {
				$order_state_sql = "AND posts.post_status IN ($incomplete_states)";
			} else {
				$order_state_sql = "AND posts.post_status NOT IN ($incomplete_states)";
			}
		}

		$query = "
			SELECT
			    items.order_id
			FROM
			    {$wpdb->prefix}woocommerce_order_itemmeta AS meta
			        INNER JOIN
			    {$wpdb->prefix}woocommerce_order_items AS items ON meta.order_item_id = items.order_item_id
			        INNER JOIN
			    {$wpdb->prefix}posts AS posts ON items.order_id = posts.ID
			WHERE
			    (meta_key = '_product_id'
			        AND meta_value = %d
			        $order_state_sql );
		";

		return (array) $wpdb->get_col( $wpdb->prepare( $query, $ticket_id ) );
	}

	/**
	 * Returns a comma separated list of term IDs representing incomplete order
	 * states.
	 *
	 * @return string
	 */
	protected function incomplete_order_states() {
		$considered_incomplete = (array) apply_filters( 'wootickets_incomplete_order_states', array(
			'wc-on-hold',
			'wc-pending',
			'wc-processing',
		) );

		foreach ( $considered_incomplete as &$incomplete ) {
			$incomplete = '"' . $incomplete . '"';
		}

		return join( ',', $considered_incomplete );
	}

	/**
	 * Retrieves the IDs of any orders containing the specified product (ticket_id) so
	 * long as the order is considered incomplete.
	 *
	 * @deprecated remove in 4.0 (provides compatibility with pre-2.2 WC releases)
	 *
	 * @param $ticket_id
	 * @param string $status Types of orders: incomplete or complete
	 *
	 * @return array
	 */
	protected function backcompat_get_orders_by_status( $ticket_id, $status = 'incomplete' ) {
		global $wpdb;
		$total = 0;

		$incomplete_states = $this->backcompat_incomplete_order_states();
		if ( empty( $incomplete_states ) ) {
			return array();
		}

		$comparison = 'incomplete' === $status ? 'IN' : 'NOT IN';

		$query = "
			SELECT
			    items.order_id
			FROM
			    {$wpdb->prefix}woocommerce_order_itemmeta AS meta
			        INNER JOIN
			    {$wpdb->prefix}woocommerce_order_items AS items ON meta.order_item_id = items.order_item_id
			        INNER JOIN
			    {$wpdb->prefix}term_relationships AS relationships ON items.order_id = relationships.object_id
			WHERE
			    (meta_key = '_product_id'
			        AND meta_value = %d )
			        AND (relationships.term_taxonomy_id $comparison ( $incomplete_states ));
		";

		return (array) $wpdb->get_col( $wpdb->prepare( $query, $ticket_id ) );
	}

	/**
	 * Returns a comma separated list of term IDs representing incomplete order
	 * states.
	 *
	 * @deprecated remove in 4.0 (provides compatibility with pre-2.2 WC releases)
	 *
	 * @return string
	 */
	protected function backcompat_incomplete_order_states() {
		$considered_incomplete = (array) apply_filters( 'wootickets_incomplete_order_states', array(
			'pending',
			'on-hold',
			'processing',
		) );

		$incomplete_states = array();

		foreach ( $considered_incomplete as $term_slug ) {
			$term = get_term_by( 'slug', $term_slug, 'shop_order_status' );
			if ( false === $term ) continue;
			$incomplete_states[] = (int) $term->term_id;
		}

		return join( ',', $incomplete_states );
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

		$event = get_post_meta( $ticket_product, $this->event_key, true );

		if ( empty( $event ) ) {
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
	 *
	 *     order_id
	 *     order_status
	 *     purchaser_name
	 *     purchaser_email
	 *     ticket
	 *     attendee_id
	 *     security
	 *     product_id
	 *     check_in
	 *     provider
	 *
	 * @param $event_id
	 * @return array
	 */
	protected function get_attendees( $event_id ) {
		$attendees_query = new WP_Query( array(
			'posts_per_page' => - 1,
			'post_type'      => self::ATTENDEE_OBJECT,
			'meta_key'       => self::ATTENDEE_EVENT_KEY,
			'meta_value'     => $event_id,
			'orderby'        => 'ID',
			'order'          => 'DESC',
		) );

		if ( ! $attendees_query->have_posts() ) {
			return array();
		}
		$attendees = array();

		foreach ( $attendees_query->posts as $attendee ) {
			$order_id   = get_post_meta( $attendee->ID, self::ATTENDEE_ORDER_KEY, true );
			$checkin    = get_post_meta( $attendee->ID, $this->checkin_key, true );
			$optout     = (bool) get_post_meta( $attendee->ID, self::ATTENDEE_OPTOUT_KEY, true );
			$security   = get_post_meta( $attendee->ID, $this->security_code, true );
			$product_id = get_post_meta( $attendee->ID, self::ATTENDEE_PRODUCT_KEY, true );
			$user_id    = get_post_meta( $attendee->ID, self::ATTENDEE_USER_ID, true );

			if ( empty( $product_id ) ) {
				continue;
			}

			$product = get_post( $product_id );
			$product_title = ( ! empty( $product ) ) ? $product->post_title : get_post_meta( $attendee->ID, $this->deleted_product, true ) . ' ' . __( '(deleted)', 'wootickets' );

			// Add the Attendee Data to the Order data
			$attendee_data = array_merge(
				$this->get_order_data( $order_id ),
				array(
					'ticket'      => $product_title,
					'attendee_id' => $attendee->ID,
					'security'    => $security,
					'product_id'  => $product_id,
					'check_in'    => $checkin,
					'optout'      => $optout,
					'user_id'     => $user_id,
				)
			);

			/**
			 * Allow users to filter the Attendee Data
			 *
			 * @var array An associative array with the Information of the Attendee
			 * @var string What Provider is been used
			 * @var WP_Post Attendee Object
			 * @var int Event ID
			 *
			 */
			$attendee_data = apply_filters( 'tribe_tickets_attendee_data', $attendee_data, 'woo', $attendee, $event_id );

			$attendees[] = $attendee_data;
		}

		return $attendees;
	}

	/**
	 * Retreive only order related information
	 *
	 *     order_id
	 *     order_id_display
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
		$name       = get_post_meta( $order_id, '_billing_first_name', true ) . ' ' . get_post_meta( $order_id, '_billing_last_name', true );
		$email      = get_post_meta( $order_id, '_billing_email', true );

		$status = get_post_status( $order_id );
		$order_status   = 'wc-' === substr( $status, 0, 3 ) ? substr( $status, 3 ) : $status;
		$order_status_label = __( $order_status, 'woocommerce' );
		$order_warning = false;

		// Warning flag for refunded, cancelled and failed orders
		switch ( $order_status ) {
			case 'refunded': case 'cancelled': case 'failed':
				$order_warning = true;
			break;
		}

		// Warning flag where the order post was trashed
		if ( ! empty( $order_status ) && get_post_status( $order_id ) == 'trash' ) {
			$order_status_label = sprintf( __( 'In trash (was %s)', 'event-tickets-plus' ), $order_status_label );
			$order_warning = true;
		}

		// Warning flag where the order has been completely deleted
		if ( empty( $order_status ) && ! get_post( $order_id ) ) {
			$order_status_label = __( 'Deleted', 'event-tickets-plus' );
			$order_warning = true;
		}

		$order = wc_get_order( $order_id );
		$display_order_id = method_exists( $order, 'get_order_number' ) ? $order->get_order_number() : $order_id;
		$order_link_src = esc_url( get_edit_post_link( $order_id, true ) );
		$order_link = sprintf( '<a class="row-title" href="%s">%s</a>', $order_link_src, esc_html( $display_order_id ) );

		$data = array(
			'order_id'           => $order_id,
			'order_id_display'   => $display_order_id,
			'order_id_link'      => $order_link,
			'order_id_link_src'  => $order_link_src,
			'order_status'       => $order_status,
			'order_status_label' => $order_status_label,
			'order_warning'      => $order_warning,
			'purchaser_name'     => $name,
			'purchaser_email'    => $email,
			'provider'           => __CLASS__,
			'provider_slug'      => 'woo',
			'purchase_time'      => get_post_time( Tribe__Date_Utils::DBDATETIMEFORMAT, false, $order_id ),
		);

		/**
		 * Allow users to filter the Order Data
		 *
		 * @var array An associative array with the Information of the Order
		 * @var string What Provider is been used
		 * @var int Order ID
		 *
		 */
		$data = apply_filters( 'tribe_tickets_order_data', $data, 'woo', $order_id );

		return $data;
	}

	/**
	 * Returns the order status.
	 *
	 * @todo remove safety check against existence of wc_get_order_status_name() in future release
	 *       (exists for backward compatibility with versions of WC below 2.2)
	 *
	 * @param $order_id
	 * @return string
	 */
	protected function order_status( $order_id ) {
		if ( ! function_exists( 'wc_get_order_status_name' ) ) {
			return __( 'Unknown', 'event-tickets-plus' );
		}
		return wc_get_order_status_name( get_post_status( $order_id ) );
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
		do_action( 'wootickets_checkin', $attendee_id, $qr );

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
		do_action( 'rsvp_uncheckin', $attendee_id );

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

		/**
		 * Filter the default purchase limit for the ticket
		 *
		 * @var int
		 *
		 * @return int
		 */
		$purchase_limit = apply_filters( 'tribe_tickets_default_purchase_limit', 0 );

		if ( ! empty( $ticket_id ) ) {
			$ticket = $this->get_ticket( $event_id, $ticket_id );
			if ( ! empty( $ticket ) ) {
				$stock = $ticket->managing_stock() ? $ticket->stock() : '';
				$sku   = get_post_meta( $ticket_id, '_sku', true );
				$purchase_limit = $ticket->purchase_limit;
			}
		}

		$global_stock_mode = ( isset( $ticket ) && method_exists( $ticket, 'global_stock_mode' ) )
			? $ticket->global_stock_mode()
			: '';

		$global_stock_cap = ( isset( $ticket ) && method_exists( $ticket, 'global_stock_cap' ) )
			? $ticket->global_stock_cap()
			: 0;

		include $this->pluginPath . 'src/admin-views/woocommerce-metabox-advanced.php';
	}

	/**
	 * Links to sales report for all tickets for this event.
	 *
	 * @param $event_id
	 * @return string
	 */
	public function get_event_reports_link( $event_id ) {
		$ticket_ids = (array) $this->get_tickets_ids( $event_id );
		if ( empty( $ticket_ids ) ) {
			return '';
		}

		$query = array(
			'post_type' => 'tribe_events',
			'page' => 'tickets-orders',
			'event_id' => $event_id,
		);

		$report_url = add_query_arg( $query, admin_url( 'admin.php' ) );

		/**
		 * Filter the Event Ticket Orders (Sales) Report URL
		 *
		 * @var string Report URL
		 * @var int Event ID
		 * @var array Ticket IDs
		 *
		 * @return string
		 */
		$report_url = apply_filters( 'tribe_events_tickets_report_url', $report_url, $event_id, $ticket_ids );
		return '<small> <a href="' . esc_url( $report_url ) . '">' . esc_html__( 'Event sales report', 'event-tickets-plus' ) . '</a> </small>';
	}

	/**
	 * Links to the sales report for this product.
	 *
	 * @param $unused_event_id
	 * @param $ticket_id
	 * @return string
	 */
	public function get_ticket_reports_link( $unused_event_id, $ticket_id ) {
		if ( empty( $ticket_id ) ) {
			return '';
		}

		$query = array(
			'page' => 'wc-reports',
			'tab' => 'orders',
			'report' => 'sales_by_product',
			'product_ids' => $ticket_id,
		);

		$report_url = add_query_arg( $query, admin_url( 'admin.php' ) );
		return '<span><a href="' . esc_url( $report_url ) . '">' . __( 'Report', 'event-tickets-plus' ) . '</a></span>';
	}

	/**
	 * Registers a metabox in the WooCommerce product edit screen
	 * with a link back to the product related Event.
	 *
	 */
	public function woocommerce_meta_box() {
		$event_id = get_post_meta( get_the_ID(), $this->event_key, true );

		if ( ! empty( $event_id ) ) {
			add_meta_box( 'wootickets-linkback', 'Event', array( $this, 'woocommerce_meta_box_inside' ), 'product', 'normal', 'high' );
		}
	}

	/**
	 * Contents for the metabox in the WooCommerce product edit screen
	 * with a link back to the product related Event.
	 */
	public function woocommerce_meta_box_inside() {
		$event_id = get_post_meta( get_the_ID(), $this->event_key, true );
		if ( ! empty( $event_id ) ) {
			echo sprintf( '%s <a href="%s">%s</a>', esc_html__( 'This is a ticket for the event:', 'event-tickets-plus' ), esc_url( get_edit_post_link( $event_id ) ), esc_html( get_the_title( $event_id ) ) );
		}
	}

	/**
	 * Indicates if global stock support is enabled (for WooCommerce the default is
	 * true).
	 *
	 * @return bool
	 */
	public function supports_global_stock() {
		/**
		 * Allows the declaration of global stock support for WooCommerce tickets
		 * to be overridden.
		 *
		 * @var bool $enable_global_stock_support
		 */
		return (bool) apply_filters( 'tribe_tickets_woo_enable_global_stock', true );
	}

	/**
	 * Determine if the event is set to use global stock for its tickets.
	 *
	 * @param int $event_id
	 *
	 * @return bool
	 */
	public function uses_global_stock( $event_id ) {
		// In some cases (version mismatch with Event Tickets) the Global Stock class may not be available
		if ( ! class_exists( 'Tribe__Tickets__Global_Stock' ) ) {
			return false;
		}

		$global_stock = new Tribe__Tickets__Global_Stock( $event_id );
		return $global_stock->is_enabled();
	}

	/**
	 * Returns the amount of global stock set for the event.
	 *
	 * A positive value does not necessarily mean global stock is currently in effect;
	 * always combine a call to this method with a call to $this->uses_global_stock()!
	 *
	 * @param int $event_id
	 *
	 * @return int
	 */
	protected function global_stock_level( $event_id ) {
		// In some cases (version mismatch with Event Tickets) the Global Stock class may not be available
		if ( ! class_exists( 'Tribe__Tickets__Global_Stock' ) ) {
			return 0;
		}

		$global_stock = new Tribe__Tickets__Global_Stock( $event_id );
		return $global_stock->get_stock_level();
	}

	/**
	 * Get's the WC product price html
	 *
	 * @param int|object $product
	 *
	 * @return string
	 */
	public function get_price_html( $product ) {
		if ( is_numeric( $product ) ) {
			if ( class_exists( 'WC_Product_Simple' ) ) {
				$product = new WC_Product_Simple( $product );
			} else {
				$product = new WC_Product( $product );
			}
		}

		if ( ! method_exists( $product, 'get_price_html' ) )
			return '';

		return $product->get_price_html();
	}

	public function get_tickets_ids( $event_id ) {
		if ( is_object( $event_id ) ) {
			$event_id = $event_id->ID;
		}

		$query = new WP_Query( array(
			'post_type'      => 'product',
			'meta_key'       => $this->event_key,
			'meta_value'     => $event_id,
			'meta_compare'   => '=',
			'posts_per_page' => -1,
			'fields'         => 'ids',
			'post_status'    => 'publish',
		) );

		return $query->posts;
	}

	/**
	 * Adds an action to resend the tickets to the customer
	 * in the WooCommerce actions dropdown, in the order edit screen.
	 *
	 * @param $emails
	 *
	 * @return array
	 */
	public function add_resend_tickets_action( $emails ) {
		$order = get_the_ID();

		if ( empty( $order ) ) {
			return $emails;
		}

		$has_tickets = get_post_meta( $order, $this->order_has_tickets, true );

		if ( ! $has_tickets ) {
			return $emails;
		}

		$emails[] = 'wootickets';
		return $emails;
	}

	private function get_cancelled( $ticket_id ) {
		$cancelled = Tribe__Tickets_Plus__Commerce__WooCommerce__Orders__Cancelled::for_ticket( $ticket_id );

		return $cancelled->get_count();
	}

	/**
	 * @param $order_id
	 */
	protected function complete_order( $order_id ) {
		update_post_meta( $order_id, $this->mail_sent_meta_key, '1' );

		// Send the email to the user

		/**
		 * Fires when a ticket order is complete.
		 *
		 * Back-compatibility action hook.
		 *
		 * @since 4.1
		 *
		 * @param int $order_id The order post ID for the ticket.
		 */
		do_action( 'wootickets-send-tickets-email', $order_id );

		/**
		 * Fires when a ticket order is complete.
		 *
		 * @since 4.2
		 *
		 * @param int $order_id The order post ID for the ticket.
		 */
		do_action( 'event_tickets_woo_complete_order', $order_id );

	}

	/**
	 * Returns a ready to use instance of the `CheckIn_Stati` class.
	 *
	 * @return Tribe__Tickets_Plus__Commerce__WooCommerce__CheckIn_Stati
	 */
	protected function checkin_stati() {
		if ( empty( $this->checkin_stati ) ) {
			$this->checkin_stati = new Tribe__Tickets_Plus__Commerce__WooCommerce__CheckIn_Stati();
		}

		return $this->checkin_stati;
	}

	/*
	 * Excludes WooCommerce product post types from the list of supported post types that Tickets can be attached to
	 *
	 * @since 4.0.5
	 *
	 * @param array $post_types Array of supported post types
	 *
	 * @return array
	 */
	public function exclude_product_post_type( $post_types ) {
		if ( isset( $post_types['product'] ) ) {
			unset( $post_types['product'] );
		}

		return $post_types;
	}
}
