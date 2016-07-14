<?php

if ( class_exists( 'Tribe__Tickets_Plus__Commerce__WPEC__Email' ) ) {
	return;
}

class Tribe__Tickets_Plus__Commerce__WPEC__Email extends WPSC_Purchase_Log_Customer_Notification {
	/**
	 * We will use this as a container for our well-formatted HTML message, in order to undo
	 * some filtering (through wpautop(), which mangles our CSS) conducted by WPEC that we can't
	 * otherwise easily circumvent.
	 *
	 * @var string
	 */
	protected $wpec_tickets_email_msg = '';

	/**
	 * Let the parent object(s) do the work, then restore our well-formatted email content.
	 *
	 * This avoids an issue whereby the CSS in our ticket email's style element is processed by
	 * wpautop().
	 */
	public function __construct( $purchase_log ) {
		parent::__construct( $purchase_log );
		$this->html_message = $this->wpec_tickets_email_msg;
	}

	public function get_subject() {
		return __( 'Your tickets', 'event-tickets-plus' );
	}

	public function get_raw_message() {
		global $wpdb;

		$wpectickets = Tribe__Tickets_Plus__Commerce__WPEC__Main::get_instance();
		$data        = $this->purchase_log->get_data();

		$args = array(
			'post_type'     => $wpectickets->attendee_object,
			'meta_key'       => $wpectickets->atendee_order_key,
			'meta_value'     => $data['id'],
			'posts_per_page' => -1,
		);

		$query = new WP_Query( $args );

		$attendees = array();

		foreach ( $query->posts as $post ) {

			$order_id = $data['id'];

			$usersql    = $wpdb->prepare( 'SELECT DISTINCT `' . WPSC_TABLE_SUBMITTED_FORM_DATA . '`.value, `' . WPSC_TABLE_CHECKOUT_FORMS . '`.* FROM `' . WPSC_TABLE_CHECKOUT_FORMS . '` LEFT JOIN `' . WPSC_TABLE_SUBMITTED_FORM_DATA . '` ON `' . WPSC_TABLE_CHECKOUT_FORMS . '`.id = `' . WPSC_TABLE_SUBMITTED_FORM_DATA . '`.`form_id` WHERE `' . WPSC_TABLE_SUBMITTED_FORM_DATA . "`.log_id=%d and unique_name in ('billingfirstname','billinglastname','billingemail') ORDER BY `" . WPSC_TABLE_CHECKOUT_FORMS . '`.`unique_name`', $order_id );
			$formfields = $wpdb->get_results( $usersql );

			$name    = array();
			$name[0] = ( ! empty( $formfields[1]->value ) ) ? $formfields[1]->value : '';
			$name[1] = ( ! empty( $formfields[2]->value ) ) ? $formfields[2]->value : '';
			$name    = join( $name, ' ' );

			$product = get_post( get_post_meta( $post->ID, $wpectickets->atendee_product_key, true ) );
			$ticket_unique_id = get_post_meta( $post->ID, '_unique_id', true );
			$ticket_unique_id = $ticket_unique_id === '' ? $post->ID : $ticket_unique_id;

			$attendees[] = array(
				'event_id'      => get_post_meta( $post->ID, $wpectickets->atendee_event_key, true ),
				'product_id'    => $product->ID,
				'ticket_name'   => $product->post_title,
				'holder_name'   => $name,
				'order_id'      => $order_id,
				'ticket_id'     => $ticket_unique_id,
				'qr_ticket_id'  => $post->ID,
				'security_code' => get_post_meta( $post->ID, $wpectickets->security_code, true ),
			);
		}

		$this->html_message = Tribe__Tickets_Plus__Commerce__WPEC__Main::get_instance()->generate_tickets_email_content( $attendees );
		$this->wpec_tickets_email_msg = $this->html_message;

		return $this->html_message;
	}
}
