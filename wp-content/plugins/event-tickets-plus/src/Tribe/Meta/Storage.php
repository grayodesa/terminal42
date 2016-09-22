<?php


/**
 * Class Tribe__Tickets_Plus__Meta__Storage
 *
 * Handles CRUD operations of attendee meta temporary storage.
 *
 * @since 4.2.6
 */
class Tribe__Tickets_Plus__Meta__Storage {

	/**
	 * The index used to store attendee meta information in the $_POST global.
	 */
	const META_DATA_KEY = 'tribe-tickets-meta';

	/**
	 * The prefix prepended to the transient created to store the ticket meta
	 * information; an hash will be appended to it.
	 */
	const TRANSIENT_PREFIX = 'tribe_tickets_meta_';

	/**
	 * The name of the cookie storing the hash of the transient storing the ticket meta.
	 */
	const HASH_COOKIE_KEY = 'tribe-event-tickets-plus-meta-hash';

	/**
	 * @var array
	 */
	protected $data_cache = array();

	/**
	 * The time in seconds after which the ticket meta transient will expire.
	 *
	 * Defaults to a day.
	 *
	 * @var int
	 */
	protected $ticket_meta_expire_time = 86400;

	/**
	 * @return bool
	 */
	public function maybe_set_attendee_meta_cookie() {
		$empty_or_wrong_format = empty( $_POST[ self::META_DATA_KEY ] ) || ! is_array( $_POST[ self::META_DATA_KEY ] );
		if ( $empty_or_wrong_format ) {
			return false;
		}

		$cookie_set = ! empty( $_COOKIE[ self::HASH_COOKIE_KEY ] );
		if ( $cookie_set ) {
			$set = $this->maybe_update_ticket_meta_cookie();
		} else {
			$set = $this->set_ticket_meta_cookie();
		}

		return $set;
	}

	/**
	 * Sets the ticket meta cookie.
	 *
	 * @return string|bool The transient hash or `false` if the transient setting
	 *                     failed.
	 */
	protected function set_ticket_meta_cookie() {
		$id          = uniqid();
		$transient   = self::TRANSIENT_PREFIX . $id;
		$ticket_meta = $_POST[ self::META_DATA_KEY ];
		$set         = set_transient( $transient, $ticket_meta, $this->ticket_meta_expire_time );

		if ( ! $set ) {
			return false;
		}

		$this->set_hash_cookie( $id );

		return $id;
	}

	/**
	 * Create a transient to store the attendee meta information if not set already.
	 *
	 * @return string|bool The transient hash or `false` if the cookie setting
	 *                     was not needed or failed.
	 */
	private function maybe_update_ticket_meta_cookie() {
		$id          = $_COOKIE[ self::HASH_COOKIE_KEY ];
		$transient   = self::TRANSIENT_PREFIX . $id;
		$ticket_meta = $_POST[ self::META_DATA_KEY ];

		$stored_ticket_meta = get_transient( $transient );

		delete_transient( $transient );
		$ticket_meta = tribe_array_merge_recursive( $stored_ticket_meta, $ticket_meta );
		$set         = set_transient( $transient, $ticket_meta, $this->ticket_meta_expire_time );

		if ( ! $set ) {
			return false;
		}

		return $id;
	}

	/**
	 * Sets the transient hash in a cookie.
	 *
	 * @param $transient
	 */
	protected function set_hash_cookie( $transient ) {
		setcookie( self::HASH_COOKIE_KEY, $transient, 0, COOKIEPATH ? COOKIEPATH : '/', COOKIE_DOMAIN, is_ssl() );
		$_COOKIE[ self::HASH_COOKIE_KEY ] = $transient;
	}

	/**
	 * Gets the ticket data associated to a specified ticket.
	 *
	 * @param int $id
	 *
	 * @return array|mixed Either the data stored for the specified id
	 *                     or an empty array.
	 */
	public function get_meta_data_for( $id ) {
		if ( isset( $this->data_cache[ $id ] ) ) {
			return $this->data_cache[ $id ];
		}

		if ( ! isset( $_COOKIE[ self::HASH_COOKIE_KEY ] ) ) {
			return array();
		}

		$transient = self::TRANSIENT_PREFIX . $_COOKIE[ self::HASH_COOKIE_KEY ];

		$data = get_transient( $transient );

		if ( ! isset( $data[ intval( $id ) ] ) ) {
			return array();
		}

		$data = array( $id => $data[ $id ] );

		$this->data_cache[ $id ] = $data;

		return $data;
	}

	/**
	 * Clears the stored data associated with a ticket.
	 *
	 * @param int $id A ticket ID
	 *
	 * @return bool Whether the data for the specified ID was stored and cleared; `false`
	 *              otherwise.
	 */
	public function clear_meta_data_for( $id ) {
		if ( empty( $_COOKIE[ self::HASH_COOKIE_KEY ] ) ) {
			return false;
		}

		$transient = self::TRANSIENT_PREFIX . $_COOKIE[ self::HASH_COOKIE_KEY ];
		$data      = get_transient( $transient );

		if ( empty( $data ) ) {
			return false;
		}

		if ( ! isset( $data[ $id ] ) ) {
			return false;
		}

		unset( $data[ $id ] );

		if ( empty( $data ) ) {
			delete_transient( $transient );
			$this->delete_cookie();
		} else {
			set_transient( $transient, $data, $this->ticket_meta_expire_time );
		}

		return true;
	}

	/**
	 * Deletes the cookie storing the transient hash
	 */
	protected function delete_cookie() {
		setcookie( self::HASH_COOKIE_KEY, '', time() - 3600, COOKIEPATH ? COOKIEPATH : '/', COOKIE_DOMAIN, is_ssl() );
		unset( $_COOKIE[ self::HASH_COOKIE_KEY ] );
	}
}
