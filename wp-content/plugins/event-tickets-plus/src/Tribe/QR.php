<?php
/**
 * Class Tribe__Tickets_Plus__QR
 */
class Tribe__Tickets_Plus__QR {

	public function __construct() {
		add_filter( 'init', array( $this, 'handle_redirects' ), 10    );
		add_filter( 'admin_notices', array( $this, 'admin_notice' ), 10    );
		add_action( 'tribe_tickets_ticket_email_ticket_bottom', array( $this, 'inject_qr' ) );
	}

	/**
	 * Procesess the links coming from QR codes and decides what to do:
	 *   - If the user is logged in and has proper permissions, it will redirect
	 *     to the attendees screen for the event, and will automatically check in the user.
	 *
	 *   - If the user is not logged in and/or not have proper permissions, it'll redirect
	 *     to the homepage of the event (front end)
	 */
	public function handle_redirects() {

		// Check if it's our time to shine.
		// Not as fancy as a custom permalink handler, but way less likely to fail depending on setup and settings
		if ( ! isset( $_GET['event_qr_code'] ) ) {
			return;
		}

		// Check all the data we need is there
		if ( empty( $_GET['ticket_id'] ) || empty( $_GET['event_id'] ) ) {
			return;
		}

		// Make sure we don't fail too hard
		if ( ! class_exists( 'Tribe__Tickets__Tickets_Handler' ) ) {
			return;
		}

		// If the user is the site owner (or similar), Check in the user to the event
		if ( is_user_logged_in() && current_user_can( 'edit_posts' ) ) {

			$this->_check_in( $_GET['ticket_id'] );

			$post = get_post( $_GET['event_id'] );

			if ( empty( $post ) ) {
				return;
			}

			$url = add_query_arg( array(
				'post_type'     => $post->post_type,
				'page'          => Tribe__Tickets__Tickets_Handler::$attendees_slug,
				'event_id'      => $_GET['event_id'],
				'qr_checked_in' => $_GET['ticket_id'],
			), admin_url( 'edit.php' ) );
		} else { // Probably just the ticket holder, redirect to the event front end single
			$url = get_permalink( $_GET['event_id'] );
		}

		wp_redirect( $url );
		exit;
	}

	/**
	 * Show a notice so the user knows the ticket was checked in
	 */
	public function admin_notice() {
		if ( empty( $_GET['qr_checked_in'] ) ) {
			return;
		}

		//Use Human Readable ID Where Available for QR Check in Message
		$ticket_id = absint( $_GET['qr_checked_in'] );
		$checked_status = get_post_meta( $ticket_id, '_tribe_qr_status', true );
		$ticket_unique_id = get_post_meta( $ticket_id, '_unique_id', true );
		$ticket_id = $ticket_unique_id === '' ? $ticket_id : $ticket_unique_id;

		//if status is qr then display already checked in warning
		if ( $checked_status ) {
			echo '<div class="error"><p>';
			printf( esc_html__( 'The ticket with ID %s has already been checked in.', 'event-tickets-plus' ), esc_html( $ticket_id ) );
			echo '</p></div>';
		} else {
			echo '<div class="updated"><p>';
			printf( esc_html__( 'The ticket with ID %s was checked in.', 'event-tickets-plus' ), esc_html( $ticket_id ) );
			echo '</p></div>';
			//update the checked in status when using the qr code here
			update_post_meta( absint( $_GET['qr_checked_in'] ), '_tribe_qr_status', 1 );
		}

	}

	/**
	 * Generates the QR image, stores is locally and injects it into the tickets email
	 *
	 * @param $ticket array
	 *
	 * @return string
	 */
	public function inject_qr( $ticket ) {

		$link = $this->_get_link( $ticket['qr_ticket_id'], $ticket['event_id'] );
		$qr   = $this->_get_image( $link );

		if ( ! $qr ) {
			return;
		}
		?>
		<table class="content" align="center" width="620" cellspacing="0" cellpadding="0" border="0" bgcolor="#ffffff" style="margin:15px auto 0; padding:0;">
			<tr>
				<td align="center" valign="top" class="wrapper" width="620">
					<table class="inner-wrapper" border="0" cellpadding="0" cellspacing="0" width="620" bgcolor="#f7f7f7" style="margin:0 auto !important; width:620px; padding:0;">
						<tr>
							<td valign="top" class="ticket-content" align="left" width="140" border="0" cellpadding="20" cellspacing="0" style="padding:20px; background:#f7f7f7;">
								<img src="<?php echo esc_url( $qr ); ?>" width="140" height="140" alt="QR Code Image" style="border:0; outline:none; height:auto; max-width:100%; display:block;"/>
							</td>
							<td valign="top" class="ticket-content" align="left" border="0" cellpadding="20" cellspacing="0" style="padding:20px; background:#f7f7f7;">
								<h3 style="color:#0a0a0e; margin:0 0 10px 0 !important; font-family: 'Helvetica Neue', Helvetica, sans-serif; font-style:normal; font-weight:700; font-size:28px; letter-spacing:normal; text-align:left;line-height: 100%;">
									<span style="color:#0a0a0e !important"><?php esc_html_e( 'Check in for this event', 'event-tickets-plus' ); ?></span>
								</h3>
								<p>
									<?php esc_html_e( 'Scan this QR code at the event to check in.', 'event-tickets-plus' ); ?>
								</p>
							</td>
						</tr>
					</table>
				</td>
			</tr>
		</table>
		<?php
	}


	/**
	 * Generates the link for the QR image
	 *
	 * @param $ticket_id
	 * @param $event_id
	 *
	 * @return string
	 */
	private function _get_link( $ticket_id, $event_id ) {

		$url = add_query_arg( 'event_qr_code', 1, home_url() );
		$url = add_query_arg( 'ticket_id', $ticket_id, $url );
		$url = add_query_arg( 'event_id', $event_id, $url );

		return $url;
	}

	/**
	 * Generates the QR image for a given link and stores it in /wp-content/uploads.
	 * Returns the link to the new image.
	 *
	 * @param $link
	 *
	 * @return string
	 */
	private function _get_image( $link ) {
		if ( ! function_exists( 'ImageCreate' ) ) {
			// The phpqrcode library requires GD but doesn't actually check if it is available
			return null;
		}
		if ( ! class_exists( 'QRencode' ) ) {
			include_once( EVENT_TICKETS_PLUS_DIR . '/vendor/phpqrcode/qrlib.php' );
		}

		$uploads   = wp_upload_dir();
		$file_name = 'qr_' . md5( $link ) . '.png';
		$path      = trailingslashit( $uploads['path'] ) . $file_name;
		$url       = trailingslashit( $uploads['url'] ) . $file_name;

		if ( ! file_exists( $path ) ) {
			QRcode::png( $link, $path, QR_ECLEVEL_L, 3 );
		}

		return $url;
	}

	/**
	 * Checks the user in, for all the *Tickets modules running.
	 *
	 * @param $ticket_id
	 */
	private function _check_in( $ticket_id ) {
		$modules = Tribe__Tickets__Tickets::modules();

		foreach ( $modules as $class => $module ) {
			if ( ! is_callable( array( $class, 'get_instance' ) ) ) {
				continue;
			}
			$obj = call_user_func( array( $class, 'get_instance' ) );
			$obj->checkin( $ticket_id, false );
		}
	}
}
