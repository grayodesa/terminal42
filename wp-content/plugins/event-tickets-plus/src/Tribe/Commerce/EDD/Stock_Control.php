<?php

if ( class_exists( 'Tribe__Tickets_Plus__Commerce__EDD__Stock_Control' ) ) {
	return;
}

/**
 * Helps to manage ticket stock (as EDD itself has no native concept of inventory).
 *
 * Responsibility for stock management involving global stock is mostly delegated to
 * the Tribe__Tickets_Plus__Commerce__EDD__Global_Stock class.
 *
 * @see Tribe__Tickets_Plus__Commerce__EDD__Global_Stock
 */
class Tribe__Tickets_Plus__Commerce__EDD__Stock_Control {
	const PURCHASED_TICKETS  = '_edd_tickets_qty_';
	const COMPUTED_INVENTORY = '_edd_tickets_computed';


	public function __construct() {
		add_action( 'edd_insert_payment', array( $this, 'record_purchased_inventory' ), 10, 2 );
		add_filter( 'edd_edd_update_payment_meta__edd_payment_meta', array( $this, 'recalculate_purchased_inventory' ), 100, 2 );
	}

	/**
	 * Returns the amount of inventory available for the specified ticket.
	 *
	 * @param  int $ticket_id
	 * @return int
	 */
	public function available_units( $ticket_id ) {
		// Do we have a limit on the number of tickets?
		$limit = get_post_meta( $ticket_id, '_stock', true );
		if ( empty( $limit ) ) return Tribe__Tickets_Plus__Commerce__EDD__Main::UNLIMITED;

		// If so, calculate the number still available
		$sold = $this->get_purchased_inventory( $ticket_id );
		return $limit - $sold;
	}

	/**
	 * Increments the inventory of the specified product by 1 (or by the optional
	 * $increment_by value if provided).
	 *
	 * @param int $product_id
	 * @param int $increment_by
	 *
	 * @return bool true|false according to whether the update was successful or not
	 */
	public function increment_units( $product_id, $increment_by = 1 ) {
		$ticket = Tribe__Tickets_Plus__Commerce__EDD__Main::get_instance()->get_ticket( null, $product_id );

		if ( ! $ticket || ! $ticket->managing_stock() ) {
			return false;
		}

		$stock = get_post_meta( $product_id, '_stock', true );

		if ( Tribe__Tickets_Plus__Commerce__EDD__Main::UNLIMITED === $stock  ) {
			return false;
		}

		return (bool) update_post_meta( $product_id, '_stock', (int) $stock + $increment_by );
	}

	/**
	 * For each payment, generates a record of the ticket stock purchased for any ticket items.
	 *
	 * @param int   $payment
	 * @param array $payment_data
	 */
	public function record_purchased_inventory( $payment, $payment_data ) {
		$quantity = array();

		// Look through the list of purchased downloads: for any that relate to tickets,
		// determine how much inventory was purchased
		foreach ( $payment_data['downloads'] as $purchase ) {
			if ( ! get_post_meta( $purchase['id'], Tribe__Tickets_Plus__Commerce__EDD__Main::$event_key ) ) {
				continue;
			}

			$ticket_payments[] = $purchase;
			$existing_quantity = isset( $quantity[ $purchase['id'] ] ) ? $quantity[ $purchase['id'] ] : 0;
			$quantity[ $purchase['id'] ] = $existing_quantity + $purchase['quantity'];
		}

		// For each purchased ticket, record the level of inventory purchased
		foreach ( $quantity as $purchase_id => $amount ) {
			update_post_meta( $payment, self::PURCHASED_TICKETS . $purchase_id, absint( $quantity[ $purchase_id ] ) );
		}

		if ( ! empty( $quantity ) ) {
			/**
			 * Fires once the EDD provider has recorded inventory levels following an
			 * order that includes ticket products.
			 *
			 * @var array $quantities amount of stock bought for each ticket, indexed by the product ID
			 */
			do_action( 'event_tickets_edd_tickets_purchased_inventory_recorded', $quantity );
		}
	}

	/**
	 * Fires whenever the _edd_payment_meta record is updated: recalculates the amount of
	 * ticket inventory that has been purchased.
	 *
	 * @param  array $payment_data
	 * @param  int   $payment
	 * @return array
	 */
	public function recalculate_purchased_inventory( $payment_data, $payment ) {
		$this->record_purchased_inventory( $payment, $payment_data );
		return $payment_data;
	}

	/**
	 * Returns the amount of inventory purchased for the specified ticket.
	 *
	 * By default this is calculated only for orders with "valid" order statuses (pending and completed),
	 * but the optional param $order_statuses can be used to pass in an alternative list if the calculation
	 * should be restricted to pending orders only (for example).
	 *
	 * @see    $this->get_valid_payment_statuses()
	 * @param  int   $ticket_id
	 * @param  array $order_statuses
	 * @return int
	 */
	public function get_purchased_inventory( $ticket_id, array $order_statuses = null ) {
		global $wpdb;

		$this->update_ticket_inventory_counts( $ticket_id );

		if ( null === $order_statuses )
			$order_statuses = $this->get_valid_payment_statuses();

		$order_statuses = $this->escape_fields( $order_statuses );

		$sql = "
			SELECT
			    SUM( $wpdb->postmeta.meta_value)
			FROM
			    $wpdb->posts
			        JOIN
			    $wpdb->postmeta ON $wpdb->postmeta.post_id = $wpdb->posts.ID
			WHERE
			    post_status IN ( $order_statuses )
			        AND post_type = 'edd_payment'
			        AND meta_key = '_edd_tickets_qty_%d';
        ";

		return (int) $wpdb->get_var( $wpdb->prepare( $sql, $ticket_id ) );
	}

	/**
	 * Tries to ensure purchased inventory calculations for a given ticket are correct.
	 *
	 * Releases 3.9.1 and earlier did not record the amount of purchased inventory reliably,
	 * this method looks for a marker that indicates it has already been recomputed or else
	 * recomputes purchased inventory levels and *then* sets the marker so the process does
	 * not have to repeat.
	 *
	 * @todo  remove 6 months or more from release 118
	 * @param int $ticket_id
	 */
	protected function update_ticket_inventory_counts( $ticket_id ) {
		// If this process was already completed let's do nothing more
		if ( get_post_meta( $ticket_id, self::COMPUTED_INVENTORY, true ) ) {
			return;
		}

		// For each EDD payment that looks like it might contain a purchase of $ticket_id,
		// re-establish the total amount of $ticket_id stock purchased
		foreach ( $this->possible_purchases_of( $ticket_id ) as $payment_id ) {
			$payment_data = edd_get_payment_meta( $payment_id );
			$this->record_purchased_inventory( $payment_id, $payment_data );
		}

		// Typically we will not want to re-determine this: set a flag accordingly
		update_post_meta( $ticket_id, self::COMPUTED_INVENTORY, true );
	}

	/**
	 * Returns a (possibly empty) list of edd_payments that might have included purchases
	 * of the specified ticket ID.
	 *
	 * @todo   remove when $this->compute_ticket_purchases() is also removed
	 * @param  int $ticket_id
	 * @return array
	 */
	protected function possible_purchases_of( $ticket_id ) {
		global $wpdb;

		$sql = "
			SELECT
			    $wpdb->postmeta.post_id
			FROM
			    $wpdb->posts
			        JOIN
			    $wpdb->postmeta ON $wpdb->postmeta.post_id = $wpdb->posts.ID
			WHERE
			    meta_key = '_edd_payment_meta'
			        AND LOCATE( '%d', meta_value) > 0;
        ";

		return $wpdb->get_col( $wpdb->prepare( $sql, $ticket_id ) );
	}

	/**
	 * @param  int $ticket_id
	 * @return int
	 */
	public function count_incomplete_order_items( $ticket_id ) {
		return $this->get_purchased_inventory( $ticket_id, $this->get_pending_payment_statuses() );
	}

	/**
	 * Returns a comma separated, escaped list of fields.
	 *
	 * @return string
	 */
	protected function escape_fields( array $fields ) {
		global $wpdb;
		$list = array();

		foreach ( $fields as $field ) {
			$list[] = $wpdb->prepare( '%s', $field );
		}

		return join( ',', $list );
	}

	/**
	 * Returns a filterable list of post statuses considered valid (ie, pending or complete but not
	 * cancelled/refunded, etc) in relation to EDD payments.
	 *
	 * @return array
	 */
	protected function get_valid_payment_statuses() {
		return (array) apply_filters( 'eddtickets_valid_payment_statuses', array( 'pending', 'publish' ) );
	}

	/**
	 * Returns a filterable list of post statuses considered "pending" in relation to
	 * EDD payments.
	 *
	 * @return array
	 */
	protected function get_pending_payment_statuses() {
		return (array) apply_filters( 'eddtickets_pending_payment_statuses', array( 'pending' ) );
	}
}
