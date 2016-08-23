<?php

class Tribe__Tickets_Plus__Attendees_List {

	/**
	 * Meta key to hold the if the Post has Attendees List hidden
	 * @var string
	 */
	const HIDE_META_KEY = '_tribe_hide_attendees_list';

	/**
	 * Get (and instantiate, if necessary) the instance of the class
	 *
	 * @static
	 * @return self
	 *
	 */
	public static function instance() {
		static $instance;

		if ( ! $instance instanceof self ) {
			$instance = new self;
		}

		return $instance;
	}

	/**
	 * Hook the necessary filters and Actions!
	 * @return void
	 */
	public static function hook() {
		$myself = self::instance();

		// This will include before the RSVP
		add_action( 'tribe_events_single_event_after_the_meta', array( $myself, 'render' ), 4 );

		// Add the Admin Option for removing the Attendees List
		add_action( 'tribe_events_tickets_metabox_pre', array( $myself, 'render_admin_options' ) );

		foreach ( Tribe__Tickets__Main::instance()->post_types() as $post_type ) {
			add_action( 'save_post_' . $post_type, array( $myself, 'save_attendees_list_option' ) );
		}

		// Create the ShortCode
		add_shortcode( 'tribe_attendees_list', array( $myself, 'shortcode' ) );

		// Purging the attendees cache on all the modules
		// @todo: make this a little bit more clean
		add_action( 'event_tickets_rsvp_ticket_created', array( $myself, 'purge_transient' ), 10, 3 );
		add_action( 'wootickets_generate_ticket_attendee', array( $myself, 'purge_transient' ), 10, 3 );
		add_action( 'event_tickets_shopp_ticket_created', array( $myself, 'shopp_purge_transient' ), 10, 2 );
		add_action( 'event_tickets_edd_ticket_created', array( $myself, 'edd_purge_transient' ), 10, 2 );
		add_action( 'event_tickets_wpec_ticket_created', array( $myself, 'wpec_purge_transient' ), 10, 5 );
	}

	/**
	 * Save the current meta option on if the Current Post Should have the attendees list hidden
	 *
	 * @param int $post_id
	 */
	public function save_attendees_list_option( $post_id ) {
		if ( ! ( isset( $_POST[ 'tribe-tickets-post-settings' ] ) && wp_verify_nonce( $_POST[ 'tribe-tickets-post-settings' ], 'tribe-tickets-meta-box' ) ) ) {
			return;
		}

		// Bail on autosaves/bulk updates
		if ( wp_is_post_autosave( $post_id ) || wp_is_post_revision( $post_id ) ) {
			return;
		}

		$is_hidden = (int) ( empty( $_POST['tribe-tickets-hide-attendees-list'] ) ? false : (bool) $_POST['tribe-tickets-hide-attendees-list'] );

		update_post_meta( $post_id, self::HIDE_META_KEY, $is_hidden );
	}

	/**
	 * Verify if users has the option to hide the Attendees list, applys a good filter
	 *
	 * @param  int|WP_Post  $post
	 * @return boolean
	 */
	public static function is_hidden_on( $post ) {
		if ( is_numeric( $post ) ) {
			$post = WP_Post::get_instance( $post );
		}

		if ( ! $post instanceof WP_Post ) {
			return false;
		}

		$is_hidden = get_post_meta( $post->ID, self::HIDE_META_KEY, true );

		// By default non-existent meta will be an empty string
		if ( '' === $is_hidden ) {
			$is_hidden = true;
		} else {
			$is_hidden = (bool) $is_hidden;
		}

		/**
		 * Use this to filter and hide the Attendees List for a specific post or all of them
		 *
		 * @param bool $is_hidden
		 * @param WP_Post $post
		 */
		return apply_filters( 'tribe_tickets_plus_hide_attendees_list', $is_hidden, $post );
	}

	/**
	 * Renders the Administration option to hide Attendees List
	 *
	 * @param  int $post_id
	 * @return void
	 */
	public function render_admin_options( $post_id = null ) {
		$is_attendees_list_hidden = self::is_hidden_on( $post_id );

		include_once Tribe__Tickets_Plus__Main::instance()->plugin_path . 'src/admin-views/attendees-list.php';
	}

	/**
	 * Wrapper to create the Shortcode with the Attendees List
	 *
	 * @param  array $atts
	 * @return string
	 */
	public function shortcode( $atts ) {
		$atts = (object) shortcode_atts( array(
			'event' => null,
			'limit' => 20,
		), $atts, 'tribe_attendees_list' );

		ob_start();
		$this->render( $atts->event, $atts->limit );
		$html = ob_get_clean();

		return $html;
	}

	/**
	 * Remove the Post Transients when a Shopp Ticket is bought
	 *
	 * @param  int $attendee_id
	 * @param  int $order_id
	 * @return void
	 */
	public function shopp_purge_transient( $attendee_id, $order_id ) {
		$event_id = Tribe__Tickets_Plus__Commerce__Shopp__Main::get_instance()->get_event_id_from_order_id( $order_id );
		Tribe__Post_Transient::instance()->delete( $event_id, Tribe__Tickets__Tickets::ATTENDEES_CACHE );
	}

	/**
	 * Remove the Post Transients when a EDD Ticket is bought
	 *
	 * @param  int $attendee_id
	 * @param  int $order_id
	 * @return void
	 */
	public function edd_purge_transient( $attendee_id, $order_id ) {
		$event_id = Tribe__Tickets_Plus__Commerce__EDD__Main::get_instance()->get_event_id_from_order_id( $order_id );
		Tribe__Post_Transient::instance()->delete( $event_id, Tribe__Tickets__Tickets::ATTENDEES_CACHE );
	}

	/**
	 * Remove the Post Transients when a WPEC Ticket is bought
	 *
	 * @param  int $attendee_id
	 * @param  int $purchase_log
	 * @param  int $product_id
	 * @param  int $order_attendee_id
	 * @param  int $event_id
	 * @return void
	 */
	public function wpec_purge_transient( $attendee_id, $purchase_log, $product_id, $order_attendee_id, $event_id ) {
		Tribe__Post_Transient::instance()->delete( $event_id, Tribe__Tickets__Tickets::ATTENDEES_CACHE );
	}

	/**
	 * Remove the Post Transients for the Tickets Attendees
	 *
	 * @param  int $attendee_id
	 * @param  int $event_id
	 * @param  int $product_id
	 * @return void
	 */
	public function purge_transient( $attendee_id, $event_id, $product_id ) {
		Tribe__Post_Transient::instance()->delete( $event_id, Tribe__Tickets__Tickets::ATTENDEES_CACHE );
	}

	/**
	 * Includes the Attendees List HTML
	 *
	 * @param  int|WP_Post $event
	 * @return void
	 */
	public function render( $event = null, $limit = 20 ) {
		$event = get_post( $event );
		if ( ! $event instanceof WP_Post ) {
			$event = get_post();
		}

		if (
			'tribe_events_single_event_after_the_meta' === current_filter() &&
			self::is_hidden_on( $event )
		) {
			return;
		}

		// using the Attendees as a variable here allows more template configuration if needed
		$attendees = Tribe__Tickets__Tickets::get_event_attendees( $event->ID );
		$attendees_total = count( $attendees );

		if ( 0 === $attendees_total ) {
			return;
		}
		$attendees_list = $this->get_attendees( $event->ID, $limit );

		include_once Tribe__Tickets_Plus__Main::instance()->get_template_hierarchy( 'attendees-list' );
	}

	/**
	 * Returns an Array ready for printing of the Attendees List
	 *
	 * @param  int|WP_Post  $event
	 * @param  int $limit
	 * @return array
	 */
	public function get_attendees( $event, $limit = 20 ) {
		$limit      = apply_filters( 'tribe_tickets_plus_attendees_list_limit', $limit );
		$attendees  = Tribe__Tickets__Tickets::get_event_attendees( $event );
		$total      = count( $attendees );
		$has_broken = false;
		$listing    = array();
		$emails     = array();

		foreach ( $attendees as $key => $attendee ) {
			$html = '';
			// Only Check for optout when It's there
			if ( isset( $attendee['optout'] ) && $attendee['optout'] !== false ) {
				continue;
			}

			// Skip when we already have another email like this one
			if ( in_array( $attendee['purchaser_email'], $emails ) ) {
				continue;
			}

			if ( is_numeric( $limit ) && $limit < $key + 1 ) {
				$has_broken = true;
			}

			if ( $has_broken ) {
				$html .= '<span class="tribe-attendees-list-hidden">';
			} else {
				$html .= '<span class="tribe-attendees-list-shown">';
			}

			$html .= get_avatar( $attendee['purchaser_email'], 40, '', $attendee['purchaser_name'] );
			$html .= '</span>';

			$emails[] = $attendee['purchaser_email'];
			$listing[ $attendee['attendee_id'] ] = $html;
		}

		if ( $has_broken ) {
			$listing['show-more'] = '<a href="#show-all-attendees" data-offset="' . esc_attr( $limit ) . '" title="' . esc_attr__( 'Load all attendees', 'event-tickets-plus' ) . '" class="tribe-attendees-list-showall avatar">' . get_avatar( '', 40, '', esc_attr__( 'Load all attendees', 'event-tickets-plus' ) ) . '</a>';
		}

		return $listing;
	}

}
