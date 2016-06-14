<?php

/**
 * Integration layer for WPEC and Custom Meta
 *
 * @since 4.1
 */
class Tribe__Tickets_Plus__Commerce__WPEC__Meta {
	public function __construct() {
		add_action( 'wpsc_submit_checkout', array( $this, 'save_attendee_meta_to_order' ), 10, 1 );
		add_action( 'event_tickets_wpec_ticket_created', array( $this, 'save_attendee_meta_to_ticket' ), 10, 4 );
	}

	/**
	 * Sets attendee data on order posts
	 *
	 * @since 4.1
	 *
	 * @param ShoppPurchase $purchase ShoppPurchase object
	 */
	public function save_attendee_meta_to_order( $data ) {

		$purchase_log = new WPSC_Purchase_Log( $data['purchase_log_id'] );
		$order_items = $purchase_log->get_cart_contents();

		// Bail if the order is empty
		if ( empty( $order_items ) ) {
			return;
		}

		$product_ids = array();

		// gather product ids
		foreach ( (array) $order_items as $item ) {
			if ( empty( $item->prodid ) ) {
				continue;
			}

			$product_ids[] = $item->prodid;
		}

		$meta_object = Tribe__Tickets_Plus__Main::instance()->meta();

		// build the custom meta data that will be stored in the order meta
		if ( ! $order_meta = $meta_object->build_order_meta( $product_ids ) ) {
			return;
		}

		// store the custom meta on the order
		$result = wpsc_add_purchase_meta( $purchase_log->get( 'id' ), Tribe__Tickets_Plus__Meta::META_KEY, $order_meta, true );

		// clear out product custom meta data cookies
		foreach ( $product_ids as $product_id ) {
			$meta_object->clear_meta_cookie_data( $product_id );
		}
	}

	/**
	 * Sets attendee data on attendee posts
	 *
	 * @since 4.1
	 *
	 * @param int $attendee_id Attendee Ticket Post ID
	 * @param WPSC_Purchase_Log $purchase_log WPEC purchase log object
	 * @param int $product_id WPEC Product ID
	 * @param int $order_attendee_id Attendee number in submitted order
	 */
	public function save_attendee_meta_to_ticket( $attendee_id, $purchase_log, $product_id, $order_attendee_id ) {

		$meta = wpsc_get_purchase_meta( $purchase_log->get( 'id' ), Tribe__Tickets_Plus__Meta::META_KEY, true );

		if ( ! isset( $meta[ $product_id ] ) ) {
			return;
		}

		if ( ! isset( $meta[ $product_id ][ $order_attendee_id ] ) ) {
			return;
		}

		update_post_meta( $attendee_id, Tribe__Tickets_Plus__Meta::META_KEY, $meta[ $product_id ][ $order_attendee_id ] );
	}
}
