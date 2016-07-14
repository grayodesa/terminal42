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
}