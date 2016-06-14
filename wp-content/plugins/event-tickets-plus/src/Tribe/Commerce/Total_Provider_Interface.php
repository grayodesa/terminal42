<?php


interface Tribe__Tickets_Plus__Commerce__Total_Provider_Interface {

	/**
	 * Gets the sum of all the sales for all the tickets associated to an event.
	 *
	 * @param int|string|WP_Post $event Either an event post `ID` or a `WP_Post` instance for an event.
	 */
	public function get_total_for( $event );
}