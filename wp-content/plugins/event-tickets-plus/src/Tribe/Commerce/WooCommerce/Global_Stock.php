<?php
/**
 * Facilitates deeper integration of Event Tickets global stock capabilities and
 * the WooCommerce implementation provided by Event Tickets Plus.
 *
 * @internal
 */
class Tribe__Tickets_Plus__Commerce__WooCommerce__Global_Stock {
	/**
	 * Container used to store the tickets for various events.
	 *
	 * This allows us to cache the results of the get-tickets-for-an-event queries
	 * which can produce savings where multiple tickets for the same event are
	 * added to the cart.
	 *
	 * The array is structured as follows:
	 *
	 *     [
	 *         event_id =>
	 *         [
	 *             ticket_id => ticket_object,
	 *             ...
	 *         ],
	 *         ...
	 *     ]
	 *
	 * Since the inner arrays are indexed by ticket ID (which always matches
	 * the product ID) looks ups are nice and fast.
	 *
	 * @var array
	 */
	protected $event_tickets = array();


	public function __construct() {
		add_action( 'woocommerce_check_cart_items', array( $this, 'cart_check_stock' ) );
		add_action( 'woocommerce_reduce_order_stock', array( $this, 'stock_equalize' ) );
		add_action( 'tribe_tickets_global_stock_level_changed', array( $this, 'stock_update_global_tickets' ), 10, 2 );
	}

	/**
	 * Looks at the item quantities in the cart and ensures that they are not
	 * "out of bounds" in the case of global stock tickets.
	 *
	 * Amongst other things, this should properly support scenarios where the cart contains:
	 *
	 *     A) Tickets which belong to an event with global stock, but do not themselves
	 *        draw on the global stock
	 *
	 *     B) Multiple tickets utilizing global stock, but belonging to different events
	 *        (ie, some tickets from Event A and some tickets from Event B)
	 *
	 * Expects to be called when the woocommerce_check_cart_items action fires, which
	 * typically occurs both when the cart is updated and when the cart is submitted via
	 * the checkout page.
	 */
	public function cart_check_stock() {
		$insufficient_stock = array();

		// Look at the requested totals for each globally stocked event we're interested in and ensure
		// the quantities don't exceed
		foreach ( $this->cart_get_global_stock_quantities() as $event_id => $quantity ) {
			$global_stock = new Tribe__Tickets__Global_Stock( $event_id );

			if ( $quantity > $global_stock->get_stock_level() ) {
				$insufficient_stock[] = get_the_title( $event_id );
			}
		}

		// If we detect out-of-stock scenarios re globally stocked tickets, flag a warning
		if ( ! empty( $insufficient_stock ) ) {
			$this->cart_flag_global_stock_error( $insufficient_stock );
		}
	}

	/**
	 * Gets the total number of tickets requested *per event* (of course, we're only
	 * interested in events that maintain global stock where tickets for those events
	 * that utilize global stock are in the cart).
	 *
	 * @return array
	 */
	protected function cart_get_global_stock_quantities() {
		$cart        = WC()->cart;
		$current     = $cart->get_cart_item_quantities();
		$woo_tickets = Tribe__Tickets_Plus__Commerce__WooCommerce__Main::get_instance();
		$quantities  = array();

		foreach ( $cart->get_cart() as $cart_item ) {
			$product = $cart_item['data'];
			$event   = $woo_tickets->get_event_for_ticket( $product->id );

			// Skip non-tickets or tickets that do not utilize global stock
			if ( ! $event || ! $woo_tickets->uses_global_stock( $event->ID ) || ! $product->managing_stock() ) {
				continue;
			}

			$tickets = $this->get_event_tickets( $event->ID );

			if ( ! isset( $tickets[ $product->id ] ) ) {
				continue;
			}

			// We only need to accumulate the stock quantities of tickets using *global* stock
			if ( Tribe__Tickets__Global_Stock::OWN_STOCK_MODE === $tickets[ $product->id ]->global_stock_mode() ) {
				continue;
			}

			// Make sure ticket caps haven't been exceeded
			if ( Tribe__Tickets__Global_Stock::CAPPED_STOCK_MODE === $tickets[ $product->id ]->global_stock_mode() ) {
				if ( $current[ $product->id ] > $tickets[ $product->id ]->global_stock_cap() ) {
					$this->cart_flag_capped_stock_error( $product->id );
				}
			}

			if ( ! isset( $quantities[ $event->ID ] ) ) {
				$quantities[ $event->ID ] = 0;
			}

			$quantities[ $event->ID ] += $current[ $product->id ];
		}

		return $quantities;
	}

	/**
	 * Returns an array of ticket objects for the specified event. The array is
	 * indexed by ticket ID.
	 *
	 * @param int $event_id
	 *
	 * @return array
	 */
	protected function get_event_tickets( $event_id ) {
		if ( ! isset( $this->event_tickets[ $event_id ] ) ) {
			$tickets = array();

			foreach ( Tribe__Tickets_Plus__Commerce__WooCommerce__Main::get_instance()->get_all_event_tickets( $event_id ) as $ticket_object ) {
				/**
				 * @var Tribe__Tickets__Ticket_Object $ticket_object
				 */
				$tickets[ $ticket_object->ID ] = $ticket_object;
			}

			$this->event_tickets[ $event_id ] = $tickets;
		}

		return $this->event_tickets[ $event_id ];
	}

	/**
	 * Trigger an error and add an insufficient stock warning notice in relation to globally
	 * stocked tickets.
	 *
	 * @param array $insufficient_stock_items
	 */
	protected function cart_flag_global_stock_error( array $insufficient_stock_items ) {
		$error = new WP_Error;

		$message = _n(
			'Sorry, there is insufficient stock to fulfill your order with respect to the tickets you selected in relation to this event: %s',
			'Sorry, there is insufficient stock to fulfill your order with respect to the tickets you selected in relation to these events: %s',
			count( $insufficient_stock_items ),
			'event-tickets-plus'
		);

		$ticket_list = '<i>' . join( ', ', $insufficient_stock_items ) . '</i>';

		/**
		 * Error message generated when an insufficiency of global stock is discovered during
		 * validation of cart item quantities.
		 *
		 * @param string $message
		 * @param array  $insufficient_stock_items
		 */
		$message = apply_filters( 'tribe_tickets_plus_woo_global_stock_cart_error', sprintf( $message, $ticket_list ), $insufficient_stock_items );

		$error->add( 'out-of-global-stock', $message );
		wc_add_notice( $error->get_error_message(), 'error' );
	}

	/**
	 * Trigger an error if the quantity for a capped ticket is exceeded.
	 *
	 * @param int $product_id
	 */
	protected function cart_flag_capped_stock_error( $product_id ) {
		$error = new WP_Error;
		$ticket_name = wc_get_product( $product_id )->get_title();

		$message = sprintf(
			__( 'Sorry, there is insufficient stock to fulfill your order with respect to %s', 'event-tickets-plus' ),
			'<i>' . $ticket_name . '</i>'
		);

		/**
		 * Error message generated when an insufficiency of stock for a capped-sales ticket
		 * is discovered during validation of cart item quantities.
		 *
		 * @param string $message
		 * @param int    $product_ud
		 */
		$message = apply_filters( 'tribe_tickets_plus_woo_global_stock_cart_error', $message, $product_id );

		$error->add( 'out-of-capped-stock-' . $product_id, $message );
		wc_add_notice( $error->get_error_message(), 'error' );
	}

	/**
	 * When WooCommerce reduces stock levels during order processing we need to look
	 * for global stock tickets and ensure we "equalize" the total stock levels/sales
	 * caps as appropriate.
	 *
	 * @param WC_Order $order
	 */
	public function stock_equalize( WC_Order $order ) {
		$woo_tickets    = Tribe__Tickets_Plus__Commerce__WooCommerce__Main::get_instance();
		$total_ordered  = array();
		$capped_tickets = array();

		// Get the total quantity of global stock ordered per event
		foreach ( $order->get_items() as $item ) {
			$product      = $order->get_product_from_item( $item );
			$event        = $woo_tickets->get_event_for_ticket( $product->id );
			$global_stock = new Tribe__Tickets__Global_Stock( $event->ID );

			// Skip non-tickets or tickets that do not utilize global stock
			if ( ! $event || ! $global_stock->is_enabled() || ! $product->managing_stock() ) {
				continue;
			}

			$ticket = $woo_tickets->get_ticket( $event->ID, $product->id );

			switch ( $ticket->global_stock_mode() ) {
				case Tribe__Tickets__Global_Stock::CAPPED_STOCK_MODE:
					$capped_tickets[ $product->id ] = (int) $item['qty'];

				// Deliberate fallthrough - $total_ordered should accumulate capped *and* global quantities
				case Tribe__Tickets__Global_Stock::GLOBAL_STOCK_MODE:
					$total_ordered[ $event->ID ] += (int) $item['qty'];
			}
		}

		// For each ticket product that utilizes global stock, adjust the product inventory
		foreach ( $total_ordered as $event_id => $quantity_ordered ) {
			$global_stock = new Tribe__Tickets__Global_Stock( $event_id );
			$level = $global_stock->get_stock_level();
			$new_level = $level - $quantity_ordered;

			$global_stock->set_stock_level( $new_level );
			$this->stock_update_global_tickets( $event_id, $new_level );
		}

		// Now adjust sale caps
		foreach ( $capped_tickets as $ticket_id => $reduce_by ) {
			$event   = $woo_tickets->get_event_for_ticket( $ticket_id );
			$ticket  = $woo_tickets->get_ticket( $event->ID, $ticket_id );
			$current = $ticket->global_stock_cap();
			$new_cap = $current - $reduce_by;

			$ticket->global_stock_cap( $new_cap );
			update_post_meta( $ticket_id, '_global_stock_cap', $new_cap );
		}
	}

	/**
	 * Updates the global stock level and individual product inventories for any ticket products
	 * that utilize global stock.
	 *
	 * @param int $event_id
	 * @param int $stock_level
	 */
	public function stock_update_global_tickets( $event_id, $stock_level ) {
		$woo_tickets = Tribe__Tickets_Plus__Commerce__WooCommerce__Main::get_instance();

		foreach ( $woo_tickets->get_tickets( $event_id ) as $ticket ) {
			/**
			 * @var Tribe__Tickets__Ticket_Object $ticket
			 */
			if ( Tribe__Tickets__Global_Stock::OWN_STOCK_MODE === $ticket->global_stock_mode() ) {
				continue;
			}

			wc_update_product_stock( $ticket->ID, $stock_level );
		}
	}
}