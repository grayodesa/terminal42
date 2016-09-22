<?php


/**
 * Provides functionality shared by all Event Tickets Plus ticketing providers.
 */
abstract class Tribe__Tickets_Plus__Tickets extends Tribe__Tickets__Tickets {


	/**
	 * Indicates if we currently require users to be logged in before they can obtain
	 * tickets.
	 *
	 * @return bool
	 */
	protected function login_required() {
		$requirements = (array) tribe_get_option( 'ticket-authentication-requirements', array() );

		return in_array( 'event-tickets-plus_all', $requirements );
	}

	/**
	 * Processes the front-end tickets form data to handle requests common to all type of tickets.
	 *
	 * Children classes should call this method when overriding.
	 */
	public function process_front_end_tickets_form() {
		$meta_store = new Tribe__Tickets_Plus__Meta__Storage();
		$meta_store->maybe_set_attendee_meta_cookie();
	}
}
