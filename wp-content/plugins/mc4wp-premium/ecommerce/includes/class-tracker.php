<?php

class MC4WP_Ecommerce_Tracker {

	/**
	 * Add hooks
	 */
	public function hook() {
		add_action( 'init', array( $this, 'listen' ) );
	}

	/**
	 * Listen for "mc_cid" and "mc_eid" in the URL.
	 */
	public function listen() {

		$keys = array( 'mc_cid', 'mc_eid' );
		$cookie_time = 7 * 3600;

		foreach( $keys as $key ) {
			if( ! empty( $_GET[ $key] ) ) {
				setcookie( $key, sanitize_text_field( $_GET[ $key ] ), time() + $cookie_time, '/' );
			}
		}
	}

	/**
	 * @return string
	 */
	public function get_campaign_id() {
		return $this->get_cookie( 'mc_cid' );
	}

	/**
	 * @return string
	 */
	public function get_email_id() {
		return $this->get_cookie( 'mc_eid' );
	}

	/**
	 * @param $key
	 *
	 * @return string
	 */
	protected function get_cookie( $key ) {
		if( empty( $_COOKIE[ $key ] ) ) {
			return '';
		}

		return sanitize_text_field( $_COOKIE[ $key ] );
	}
}