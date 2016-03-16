<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Google Calendar Integration.
 */
class WC_Appointments_Integration_GCal extends WC_Settings_API {
	
	/**
	 * @var WC_Appointments_Integration_GCal The single instance of the class
	 */
	protected static $_instance = null;
	
	/**
	 * Main WC_Appointments_Integration_GCal Instance
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	/**
	 * Init and hook in the integration.
	 */
	public function __construct() {
		$this->plugin_id		= 'wc_appointments_';
		$this->id				= 'gcal';
		$this->method_title		= __( 'Google Calendar Sync', 'woocommerce-appointments' );

		//* API.
		$this->oauth_uri		= 'https://accounts.google.com/o/oauth2/';
		$this->calendars_uri	= 'https://www.googleapis.com/calendar/v3/calendars/';
		$this->api_scope		= 'https://www.googleapis.com/auth/calendar';
		$this->redirect_uri		= WC()->api_request_url( 'wc_appointments_oauth_redirect' );
		$this->callback_uri		= WC()->api_request_url( 'wc_appointments_callback_read' );

		//* User set variables.
		$this->client_id		= $this->get_option( 'client_id' );
		$this->client_secret	= $this->get_option( 'client_secret' );
		$this->debug			= $this->get_option( 'debug' );
		
		//* Load the settings.
		$this->init_form_fields();
		$this->init_settings();

		//* Actions.
		add_action( 'woocommerce_api_wc_appointments_oauth_redirect' , array( $this, 'oauth_redirect' ) );
		add_action( 'woocommerce_api_wc_appointments_callback_read' , array( $this, 'callback_read' ) );
		add_action( 'woocommerce_appointment_confirmed', array( $this, 'sync_calendar' ) );
		add_action( 'woocommerce_appointment_confirmed', array( $this, 'sync_appointment' ) );
		add_action( 'woocommerce_appointment_confirmed', array( $this, 'sync_callback' ) );
		add_action( 'woocommerce_appointment_paid', array( $this, 'sync_calendar' ) );
		add_action( 'woocommerce_appointment_paid', array( $this, 'sync_appointment' ) );
		add_action( 'woocommerce_appointment_paid', array( $this, 'sync_callback' ) );
		add_action( 'woocommerce_appointment_complete', array( $this, 'sync_calendar' ) );
		add_action( 'woocommerce_appointment_complete', array( $this, 'sync_appointment' ) );
		add_action( 'woocommerce_appointment_complete', array( $this, 'sync_callback' ) );
		add_action( 'woocommerce_appointment_cancelled', array( $this, 'remove_appointment' ) );
		// add_action( 'woocommerce_appointment_process_meta', array( $this, 'sync_edited' ) );
		add_action( 'trashed_post', array( $this, 'remove_appointment' ) );
		add_action( 'untrashed_post', array( $this, 'sync_edited' ) );

		if ( is_admin() ) {
			add_action( 'admin_notices', array( $this, 'admin_notices' ) );
		}

		//* Active logs.
		if ( 'yes' == $this->debug ) {
			if ( class_exists( 'WC_Logger' ) ) {
				$this->log = new WC_Logger();
			} else {
				$this->log = WC()->logger();
			}
		}
	}

	/**
	 * Initialize integration settings form fields.
	 *
	 * @return void
	 */
	public function init_form_fields() {
		$this->form_fields = array(
			'client_id' => array(
				'title'       => __( 'Client ID', 'woocommerce-appointments' ),
				'type'        => 'text',
				'description' => __( 'Enter with your Google Client ID.', 'woocommerce-appointments' ),
				'desc_tip'    => true,
				'default'     => ''
			),
			'client_secret' => array(
				'title'       => __( 'Client Secret', 'woocommerce-appointments' ),
				'type'        => 'text',
				'description' => __( 'Enter with your Google Client Secret.', 'woocommerce-appointments' ),
				'desc_tip'    => true,
				'default'     => ''
			),
			'authorization' => array(
				'title'       => __( 'Authorization', 'woocommerce-appointments' ),
				'type'        => 'gcal_authorization'
			),
			'testing' => array(
				'title'       => __( 'Testing', 'woocommerce-appointments' ),
				'type'        => 'title',
				'description' => ''
			),
			'debug' => array(
				'title'       => __( 'Debug Log', 'woocommerce-appointments' ),
				'type'        => 'checkbox',
				'label'       => __( 'Enable logging', 'woocommerce-appointments' ),
				'default'     => 'no',
				'description' => sprintf( __( 'Log Google Calendar events, such as API requests, inside %s', 'woocommerce-appointments' ), '<code>woocommerce/logs/' . $this->id . '-' . sanitize_file_name( wp_hash( $this->id ) ) . '.txt</code>' )
			)
		);
	}

	/**
	 * Validate the Google Calendar Authorization field.
	 *
	 * @param  mixed $key
	 *
	 * @return string
	 */
	public function validate_gcal_authorization_field( $key ) {
		return '';
	}

	/**
	 * Generate the Google Calendar Authorization field.
	 *
	 * @param  mixed $key
	 * @param  array $data
	 *
	 * @return string
	 */
	public function generate_gcal_authorization_html( $key, $data ) {
		$options       = $this->plugin_id . $this->id . '_';
		$id            = $options . $key;
		$client_id     = isset( $_POST[ $options . 'client_id' ] ) ? sanitize_text_field( $_POST[ $options . 'client_id' ] ) : $this->client_id;
		$client_secret = isset( $_POST[ $options . 'client_secret' ] ) ? sanitize_text_field( $_POST[ $options . 'client_secret' ] ) : $this->client_secret;
		$access_token  = $this->get_access_token();

		ob_start();
		?>
		<tr valign="top">
			<th scope="row" class="titledesc">
				<?php echo wp_kses_post( $data['title'] ); ?>
			</th>
			<td class="forminp">
				<?php
					if ( ! $access_token && ( $client_id && $client_secret ) ) :
						$oauth_url = add_query_arg(
							array(
								'scope'           => $this->api_scope,
								'redirect_uri'    => $this->redirect_uri,
								'response_type'   => 'code',
								'client_id'       => $client_id,
								'approval_prompt' => 'force',
								'access_type'     => 'offline',
							),
							$this->oauth_uri . 'auth'
						);
			 	?>
					<p class="submit"><a class="button button-primary" href="<?php echo esc_url( $oauth_url ); ?>"><?php _e( 'Connect with Google', 'woocommerce-appointments' ); ?></a></p>
				<?php elseif ( $access_token ) : ?>
					<p><?php _e( 'Successfully authenticated.', 'woocommerce-appointments' ); ?></p>
					<p class="submit"><a class="button button-primary" href="<?php echo esc_url( add_query_arg( array( 'logout' => 'true' ), $this->redirect_uri ) ); ?>"><?php _e( 'Disconnect', 'woocommerce-appointments' ); ?></a></p>
				<?php else : ?>
					<p><?php _e( 'Unable to authenticate, you must enter with your <strong>Client ID</strong>, <strong>Client Secret</strong> and <strong>Calendar ID</strong>.', 'woocommerce-appointments' ); ?></p>
				<?php endif; ?>
			</td>
		</tr>
		<?php
		return ob_get_clean();
	}

	/**
	 * Admin Options.
	 *
	 * @return string
	 */
	public function admin_options() {		
		echo '<h3>' . $this->method_title . '</h3>';
		echo '<p>' . sprintf( __( 'To use this integration you need create a project in %s:', 'woocommerce-appointments' ), '<a href="https://console.developers.google.com/project" target="_blank">' . __( 'Google Developers Console', 'woocommerce-appointments' ) . '</a>' ) . '</p>';
		
		echo '<ol>';
		echo '<li>' . __( 'Create Project.', 'woocommerce-appointments' ) . '</li>';
		echo '<li>' . __( 'Enable the <strong>Google Calendar API</strong> in <strong>APIs & auth > APIs</strong>.', 'woocommerce-appointments' ) . '</li>';
		echo '<li>' . sprintf( __( 'While in <strong>APIs & auth > Credentials</strong>, create an OAuth client for a <strong>Web application</strong> and set the <strong>Authorized redirect URI</strong> as <code>%s</code>.', 'woocommerce-appointments' ), $this->redirect_uri ) . '</li>';
		echo '<li>' . sprintf( __( 'While in <strong>APIs & auth > Push</strong>, Add your site root URL as <code>%s</code>. Your site must be under <strong>https://</strong> URL, otherwise two-way synchronization won\'t work.', 'woocommerce-appointments' ), network_site_url( '', 'https' ) ) . '</li>';
		echo '</ol>';
		
		echo '<table class="form-table">';
			$this->generate_settings_html();
		echo '</table>';
		
		echo '<div><input type="hidden" name="section" value="' . $this->id . '" /></div>';
	}

	/**
	 * Get Access Token.
	 *
	 * @param  string $code Authorization code.
	 *
	 * @return string       Access token.
	 */
	protected function get_access_token( $code = '' ) {

		if ( 'yes' == $this->debug ) {
			$this->log->add( $this->id, 'Getting Google API Access Token...' );
		}

		$access_token = get_transient( 'wc_appointments_gcal_access_token' );

		if ( ! $code && false !== $access_token ) {
			if ( 'yes' == $this->debug ) {
				$this->log->add( $this->id, 'Access Token recovered by transients: ' . print_r( $access_token, true ) );
			}

			return $access_token;
		}

		$refresh_token = get_option( 'wc_appointments_gcal_refresh_token' );

		if ( ! $code && $refresh_token ) {

			if ( 'yes' == $this->debug ) {
				$this->log->add( $this->id, 'Generating a new Access Token...' );
			}

			$data = array(
				'client_id'     => $this->client_id,
				'client_secret' => $this->client_secret,
				'refresh_token' => $refresh_token,
				'grant_type'    => 'refresh_token'
			);

			$params = array(
				'body'      => http_build_query( $data ),
				'sslverify' => false,
				'timeout'   => 60,
				'headers'   => array(
					'Content-Type' => 'application/x-www-form-urlencoded'
				)
			);

			$response = wp_remote_post( $this->oauth_uri . 'token', $params );

			if ( ! is_wp_error( $response ) && 200 == $response['response']['code'] && 'OK' == $response['response']['message'] ) {
				$response_data = json_decode( $response['body'] );
				$access_token  = sanitize_text_field( $response_data->access_token );

				if ( 'yes' == $this->debug ) {
					$this->log->add( $this->id, 'Google API Access Token generated successfully: ' . print_r( $access_token, true ) );
				}

				// Set the transient.
				set_transient( 'wc_appointments_gcal_access_token', $access_token, 3500 );

				return $access_token;
			} else {
				if ( 'yes' == $this->debug ) {
					$this->log->add( $this->id, 'Error while generating the Access Token: ' . print_r( $response, true ) );
				}
			}
		} else if ( '' != $code ) {

			if ( 'yes' == $this->debug ) {
				$this->log->add( $this->id, 'Renewing the Access Token...' );
			}

			$data = array(
				'code'          => $code,
				'client_id'     => $this->client_id,
				'client_secret' => $this->client_secret,
				'redirect_uri'  => $this->redirect_uri,
				'grant_type'    => 'authorization_code'
			);

			$params = array(
				'body'      => http_build_query( $data ),
				'sslverify' => false,
				'timeout'   => 60,
				'headers'   => array(
					'Content-Type' => 'application/x-www-form-urlencoded'
				)
			);

			$response = wp_remote_post( $this->oauth_uri . 'token', $params );

			if ( ! is_wp_error( $response ) && 200 == $response['response']['code'] && 'OK' == $response['response']['message'] ) {
				$response_data = json_decode( $response['body'] );
				$access_token  = sanitize_text_field( $response_data->access_token );

				// Add refresh token.
				update_option( 'wc_appointments_gcal_refresh_token', $response_data->refresh_token );

				// Set the transient.
				set_transient( 'wc_appointments_gcal_access_token', $access_token, 3500 );

				if ( 'yes' == $this->debug ) {
					$this->log->add( $this->id, 'Google API Access Token renewed successfully: ' . print_r( $access_token, true ) );
				}

				return $access_token;
			} else {
				if ( 'yes' == $this->debug ) {
					$this->log->add( $this->id, 'Error while renewing the Access Token: ' . print_r( $response, true ) );
				}
			}
		}

		if ( 'yes' == $this->debug ) {
			$this->log->add( $this->id, 'Failed to retrieve and generate the Access Token' );
		}

		return '';
	}

	/**
	 * OAuth Logout.
	 *
	 * @return bool
	 */
	protected function oauth_logout() {
		if ( 'yes' == $this->debug ) {
			$this->log->add( $this->id, 'Leaving the Google Calendar app...' );
		}

		$refresh_token = get_option( 'wc_appointments_gcal_refresh_token' );

		if ( $refresh_token ) {
			$params = array(
				'sslverify' => false,
				'timeout'   => 60,
				'headers'   => array(
					'Content-Type' => 'application/x-www-form-urlencoded'
				)
			);

			$response = wp_remote_get( $this->oauth_uri . 'revoke?token=' . $refresh_token, $params );

			if ( ! is_wp_error( $response ) && 200 == $response['response']['code'] && 'OK' == $response['response']['message'] ) {
				delete_option( 'wc_appointments_gcal_refresh_token' );
				delete_transient( 'wc_appointments_gcal_access_token' );

				if ( 'yes' == $this->debug ) {
					$this->log->add( $this->id, 'Leave the Google Calendar app successfully' );
				}

				return true;
			} else {
				if ( 'yes' == $this->debug ) {
					$this->log->add( $this->id, 'Error when leaving the Google Calendar app: ' . print_r( $response, true ) );
				}
			}
		}

		if ( 'yes' == $this->debug ) {
			$this->log->add( $this->id, 'Failed to leave the Google Calendar app' );
		}

		return false;
	}

	/**
	 * Process the oauth redirect.
	 *
	 * @return void
	 */
	public function oauth_redirect() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( __( 'Permission denied!', 'woocommerce-appointments' ) );
		}

		$redirect_args = array(
			'page'    => 'wc-settings',
			'tab'     => 'appointments',
			'section' => $this->id
		);

		// OAuth.
		if ( isset( $_GET['code'] ) ) {
			$code         = sanitize_text_field( $_GET['code'] );
			$access_token = $this->get_access_token( $code );

			if ( '' != $access_token ) {
				$redirect_args['wc_gcal_oauth'] = 'success';

				wp_redirect( add_query_arg( $redirect_args, admin_url( 'admin.php' ) ), 301 );
				exit;
			}
		}
		if ( isset( $_GET['error'] ) ) {

			$redirect_args['wc_gcal_oauth'] = 'fail';

			wp_redirect( add_query_arg( $redirect_args, admin_url( 'admin.php' ) ), 301 );
			exit;
		}

		// Logout.
		if ( isset( $_GET['logout'] ) ) {
			$logout = $this->oauth_logout();
			$redirect_args['wc_gcal_logout'] = ( $logout ) ? 'success' : 'fail';

			wp_redirect( add_query_arg( $redirect_args, admin_url( 'admin.php' ) ), 301 );
			exit;
		}

		wp_die( __( 'Invalid request!', 'woocommerce-appointments' ) );
	}

	/**
	 * Display admin screen notices.
	 *
	 * @return string
	 */
	public function admin_notices() {
		$screen = get_current_screen();

		if ( 'woocommerce_page_wc-settings' == $screen->id && isset( $_GET['wc_gcal_oauth'] ) ) {
			if ( 'success' == $_GET['wc_gcal_oauth'] ) {
				echo '<div class="updated fade"><p><strong>' . __( 'Google Calendar', 'woocommerce-appointments' ) . '</strong> ' . __( 'Account connected successfully!', 'woocommerce-appointments' ) . '</p></div>';
			} else {
				echo '<div class="error fade"><p><strong>' . __( 'Google Calendar', 'woocommerce-appointments' ) . '</strong> ' . __( 'Failed to connect to your account, please try again, if the problem persists, turn on Debug Log option and see what is happening.', 'woocommerce-appointments' ) . '</p></div>';
			}
		}

		if ( 'woocommerce_page_wc-settings' == $screen->id && isset( $_GET['wc_gcal_logout'] ) ) {
			if ( 'success' == $_GET['wc_gcal_logout'] ) {
				echo '<div class="updated fade"><p><strong>' . __( 'Google Calendar', 'woocommerce-appointments' ) . '</strong> ' . __( 'Account disconnected successfully!', 'woocommerce-appointments' ) . '</p></div>';
			} else {
				echo '<div class="error fade"><p><strong>' . __( 'Google Calendar', 'woocommerce-appointments' ) . '</strong> ' . __( 'Failed to disconnect to your account, please try again, if the problem persists, turn on Debug Log option and see what is happening.', 'woocommerce-appointments' ) . '</p></div>';
			}
		}
	}
	
	/**
	 * Sync Bookable product with Google Calendar
	 *
	 * Create a new google calendar ID for each appointable product 
	 *
	 * @param  int $appointment_id Appointment ID
	 *
	 * @return void
	 */
	public function sync_calendar( $appointment_id ) {
		$appointment  = get_wc_appointment( $appointment_id );
		$product  	  = $appointment->get_product();
		$calendar_id  = get_post_meta( $product->id, '_wc_appointments_gcal_calendar_id', true );
		$order        = $appointment->get_order();
		$summary      = '#' . $appointment->id;
		$description  = '';
		
		//* Need an order.
		if ( ! $order ) {
			return;
		}
		
		//* Create calendar ID if it doesn't exist yet
		$data = array(
			'summary'     => wp_kses_post( '#' . $product->id . ' ' . $product->post->post_title ),
			'description' => wp_kses_post( utf8_encode( $description ) )
		);
		
		$params = array(
			'method'    => 'POST',
			'body'      => json_encode( $data ),
			'sslverify' => false,
			'timeout'   => 60,
			'headers'   => array(
				'Content-Type'  => 'application/json',
				'Authorization' => 'Bearer ' . $this->get_access_token()
			)
		);

		//* Update calendar
		if ( $calendar_id ) {
			$params['method'] = 'PUT';
		}

		if ( 'yes' == $this->debug ) {
			$this->log->add( $this->id, 'Synchronizing product #' . $product->id . ' with Google Calendar...' );
		}
		
		$response = wp_remote_post( $this->calendars_uri, $params );
		
		if ( ! is_wp_error( $response ) && 200 == $response['response']['code'] && 'OK' == $response['response']['message'] ) {
			if ( 'yes' == $this->debug ) {
				$this->log->add( $this->id, 'Calendar synchronized successfully!' );
			}

			//* Update the Google Calendar ID
			$response_data = json_decode( $response['body'], true );
			update_post_meta( $product->id, '_wc_appointments_gcal_calendar_id', $response_data['id'] );

		} else {
			if ( 'yes' == $this->debug ) {
				$this->log->add( $this->id, 'Error while synchronizing the product #' . $product->id . ': ' . print_r( $response, true ) );
			}
		}
	}

	/**
	 * Sync Appointment with Google Calendar
	 *
	 * @param  int $appointment_id Appointment ID
	 *
	 * @return void
	 */
	public function sync_appointment( $appointment_id ) {
		$event_id     = get_post_meta( $appointment_id, '_wc_appointments_gcal_event_id', true );
		$appointment  = get_wc_appointment( $appointment_id );
		$product  	  = $appointment->get_product();
		$calendar_id  = get_post_meta( $product->id, '_wc_appointments_gcal_calendar_id', true );
		$order        = $appointment->get_order();
		$order_id  	  = get_post_meta( $appointment_id, '_appointment_order_item_id', true );
		$timezone     = wc_appointment_get_timezone_string();
		$summary      = sprintf( __( 'Appointment #%s', 'woocommerce-appointments' ), $appointment_id );
		$description  = '';

		//* If order exists for this appointment, fill the data.		
		if ( $order ) {
			
			$order_items  = $order->get_items();
			
			// Need order items.
			if ( $order_items ) {
				foreach ( $order->get_items() as $item_id => $item ) {
					if ( 'line_item' != $item['type'] ) {
						continue;
					}
					
					//* Prevent duplicates when multiple appointments are added to same order
					if ( $item_id != $order_id ) {
						continue;
					}

					// $summary .= ' - ' . $item['name'];

					if ( $metadata = $order->has_meta( $item_id ) ) {
						foreach ( $metadata as $meta ) {

							// Skip hidden core fields
							if ( in_array( $meta['meta_key'], apply_filters( 'woocommerce_hidden_order_itemmeta', array(
								'_qty',
								'_tax_class',
								'_product_id',
								'_variation_id',
								'_line_subtotal',
								'_line_subtotal_tax',
								'_line_total',
								'_line_tax',
							) ) ) ) {
								continue;
							}

							// Appointment fields.
							if ( in_array( $meta['meta_key'], array( __( 'Appointment Date', 'woocommerce-appointments' ), __( 'Appointment Time', 'woocommerce-appointments' ) ) ) ) {
								continue;
							}

							$meta_value = $meta['meta_value'];

							// Skip serialised meta
							if ( is_serialized( $meta_value ) ) {
								continue;
							}

							// Get attribute data
							if ( taxonomy_exists( $meta['meta_key'] ) ) {
								$term           = get_term_by( 'slug', $meta['meta_value'], $meta['meta_key'] );
								$attribute_name = str_replace( 'pa_', '', wc_clean( $meta['meta_key'] ) );
								$attribute      = $wpdb->get_var(
									$wpdb->prepare( "
											SELECT attribute_label
											FROM {$wpdb->prefix}woocommerce_attribute_taxonomies
											WHERE attribute_name = %s;
										",
										$attribute_name
									)
								);

								$meta['meta_key']   = ( ! is_wp_error( $attribute ) && $attribute ) ? $attribute : $attribute_name;
								$meta['meta_value'] = ( isset( $term->name ) ) ? $term->name : $meta['meta_value'];
							}

							$description .= sprintf( __( '%s: %s', 'woocommerce-appointments' ), rawurldecode( $meta['meta_key'] ), rawurldecode( $meta_value ) ) . PHP_EOL;
		 				}
					}
				}
			}
		} else {
			// there is no order -- just pull what we can from the appointment
			$product_id = $appointment->product_id;
			$product = wc_get_product( $product_id );
			// $summary .= ' - ' . $product->post->post_title;
		}

		// Set the event data
		$data = array(
			'summary'     => wp_kses_post( $summary ),
			'description' => wp_kses_post( utf8_encode( $description ) )
		);

		// Set the event start and end dates
		if ( $appointment->is_all_day() ) {
			$data['end'] = array(
				'date' => date( 'Y-m-d', ( $appointment->end + 1440 ) ),
			);

			$data['start'] = array(
				'date' => date( 'Y-m-d', $appointment->start ),
			);
		} else {
			$data['end'] = array(
				'dateTime' => date( 'Y-m-d\TH:i:s', $appointment->end ),
				'timeZone' => $timezone
			);

			$data['start'] = array(
				'dateTime' => date( 'Y-m-d\TH:i:s', $appointment->start ),
				'timeZone' => $timezone
			);
		}

		$data = apply_filters( 'woocommerce_appointments_gcal_sync', $data, $appointment );
		
		// Make sure calendar has correct ID, move if necessary
		$old_product_id_exists = get_post_meta( $appointment_id, '_appointment_product_id_orig', true );
		
		if ( $old_product_id_exists ) {
			
			$old_calendar_id  = get_post_meta( $old_product_id_exists, '_wc_appointments_gcal_calendar_id', true );
			
			$params_move = array(
				'method'    => 'POST',
				'sslverify' => false,
				'timeout'   => 60,
				'headers'   => array(
					'Content-Type'  => 'application/json',
					'Authorization' => 'Bearer ' . $this->get_access_token()
				)
			);
			
			$response_move = wp_remote_post( $this->calendars_uri . $old_calendar_id . '/events/' . $event_id . '/move?destination=' . $calendar_id, $params_move );
			
			// update_option( 'iamtesting_move', $response_move['body'] );
			
			if ( ! is_wp_error( $response_move ) && 200 == $response_move['response']['code'] && 'OK' == $response_move['response']['message'] ) {
				if ( 'yes' == $this->debug ) {
					$this->log->add( $this->id, 'Appointment successfully moved to another product!' );
				}

				// Delete old move token
				delete_post_meta( $appointment_id, '_appointment_product_id_orig' );

			} else {
				if ( 'yes' == $this->debug ) {
					$this->log->add( $this->id, 'Error while moving the appointment #' . $appointment_id . ': ' . print_r( $response, true ) );
				}
			}
		
		}

		// Connection params
		$params = array(
			'method'    => 'POST',
			'body'      => json_encode( $data ),
			'sslverify' => false,
			'timeout'   => 60,
			'headers'   => array(
				'Content-Type'  => 'application/json',
				'Authorization' => 'Bearer ' . $this->get_access_token()
			)
		);

		// Update event
		if ( $event_id ) {
			$params['method'] = 'PUT';
		}

		if ( 'yes' == $this->debug ) {
			$this->log->add( $this->id, 'Synchronizing appointment #' . $appointment->id . ' with Google Calendar...' );
		}

		$response = wp_remote_post( $this->calendars_uri . $calendar_id . '/events/' . $event_id, $params );

		if ( ! is_wp_error( $response ) && 200 == $response['response']['code'] && 'OK' == $response['response']['message'] ) {
			if ( 'yes' == $this->debug ) {
				$this->log->add( $this->id, 'Appointment synchronized successfully!' );
			}

			// Updated the Google Calendar event ID
			$response_data = json_decode( $response['body'], true );
			update_post_meta( $appointment->id, '_wc_appointments_gcal_event_id', $response_data['id'] );

		} else {
			if ( 'yes' == $this->debug ) {
				$this->log->add( $this->id, 'Error while synchronizing the appointment #' . $appointment->id . ': ' . print_r( $response, true ) );
			}
		}
	}
	
	/**
	 * Read Google Calendar callbacks in 2-way sync
	 *
	 * @return void
	 */
	public function sync_callback( $appointment_id ) {
		$appointment  = get_wc_appointment( $appointment_id );
		$product  	  = $appointment->get_product();
		$calendar_id  = get_post_meta( $product->id, '_wc_appointments_gcal_calendar_id', true );
		$callback_id  = get_post_meta( $product->id, '_wc_appointments_gcal_callback_id', true );
		$callback_rid = get_post_meta( $product->id, '_wc_appointments_gcal_callback_resourceid', true );
		$order        = $appointment->get_order();

		//* Need callback ID's.
		if ( $callback_id && $callback_rid ) {
			return;
		}
		
		//* Random ID
		$generate_rand_id = wp_generate_password( 12, false );
		
		//* Create callback ID if it doesn't exist yet
		$data = array(
			'id' => $generate_rand_id,
			'type' => 'web_hook',
			'address' => $this->callback_uri,
		);
		
		$params = array(
			'method'    => 'POST',
			'body'      => json_encode( $data ),
			'sslverify' => false,
			'timeout'   => 60,
			'headers'   => array(
				'Content-Type'  => 'application/json',
				'Authorization' => 'Bearer ' . $this->get_access_token()
			)
		);

		if ( 'yes' == $this->debug ) {
			$this->log->add( $this->id, 'Synchronizing product #' . $product->id . ' callback with Google Calendar...' );
		}
		
		$response = wp_remote_post( $this->calendars_uri . $calendar_id . '/events/watch', $params );
		
		if ( ! is_wp_error( $response ) && 200 == $response['response']['code'] && 'OK' == $response['response']['message'] ) {
			if ( 'yes' == $this->debug ) {
				$this->log->add( $this->id, 'product callback synchronized successfully!' );
			}

			// Updated the Google Calendar event ID
			$response_data = json_decode( $response['body'], true );
			update_post_meta( $product->id, '_wc_appointments_gcal_callback_id', $response_data['id'] );
			update_post_meta( $product->id, '_wc_appointments_gcal_callback_resourceid', $response_data['resourceId'] );

		} else {
			if ( 'yes' == $this->debug ) {
				$this->log->add( $this->id, 'Error while synchronizing callback for the product #' . $product->id . ': ' . print_r( $response, true ) );
			}
		}

	}

	/**
	 * Process the oauth redirect.
	 *
	 * @return void
	 */
	public function callback_read() {
		//* Leave if callback not registered.
		if ( ! isset( $_SERVER['HTTP_X_GOOG_RESOURCE_ID'] ) ) {
			return;
		}

		//* TimeZone get
		$wp_appintments_timezone = wc_appointment_get_timezone_string();

		//* Get product ID from callback resource ID
		$args = array(
	        'meta_query'        => array(
	            array(
	                'key'       => '_wc_appointments_gcal_callback_resourceid',
	                'value'     => $_SERVER['HTTP_X_GOOG_RESOURCE_ID']
	            )
	        ),
			'no_found_rows'     => true,
			'update_post_term_cache' => false,
	        'post_type'         => 'product',
	        'posts_per_page'    => '1'
	    );

	    $get_posts = new WP_Query();
		$posts = $get_posts->query( $args );
	    $product_id = ( isset( $posts[0]->ID ) ) ? $posts[0]->ID : '';
		$product = get_product( $product_id );

	    //* Leave if product ID doesn't exist.
		if ( ! $product_id ) {
			return;
		}

		//* General params for listing events
		$params = array(
			'method'    => 'GET',
			'sslverify' => false,
			'timeout'   => 60,
			'headers'   => array(
				'Content-Type'  => 'application/json',
				'Authorization' => 'Bearer ' . $this->get_access_token()
			)
		);

		if ( 'yes' == $this->debug ) {
			$this->log->add( $this->id, 'Synchronizing appointments from Google Calendar...' );
		}

		$calendar_sync_token  = get_post_meta( $product_id, '_wc_appointments_gcal_sync_token', true );

		//* Apply sync token from previous update
		if ( $calendar_sync_token ) {
			$response = wp_remote_post( $_SERVER['HTTP_X_GOOG_RESOURCE_URI'].'&singleEvents=true&syncToken='.$calendar_sync_token, $params );
		}
		else {
			$response = wp_remote_post( $_SERVER['HTTP_X_GOOG_RESOURCE_URI'], $params );
		}

		//* Decode response body
		$response_data = json_decode( $response['body'], true );
		
		if ( ! is_wp_error( $response ) && 200 == $response['response']['code'] && 'OK' == $response['response']['message'] ) {

			if ( 'yes' == $this->debug ) {
				$this->log->add( $this->id, 'Appointment #' . $response['body']['summary'] . ' synchronized successfully!' );
			}

			//* Set sync token for the first time
			if ( ! $calendar_sync_token ) {
				update_post_meta( $product_id, '_wc_appointments_gcal_sync_token', $response_data['nextSyncToken'] );
			}

			//* Sync appointments
			if ( $calendar_sync_token && is_array( $response_data['items'] ) && ! empty( $response_data['items'] ) ) {
				
				// update_post_meta( '999', '_wc_appointments_gcal_sync_test', $response['body'] );

				//* Sync all events in loop
				foreach ( $response_data['items'] as $data ) {

					//* Get appointment ID from callback event ID
					$args = array(
				        'meta_query'        => array(
				            array(
				                'key'       => '_wc_appointments_gcal_event_id',
				                'value'     => $data['id']
				            )
				        ),
						'no_found_rows'     => true,
						'update_post_meta_cache' => false,
				        'post_type'         => 'wc_appointment',
				        'posts_per_page'    => '1'
				    );

				    $get_posts = new WP_Query();
				    $posts = $get_posts->query( $args );
				    $appointment_id = ( isset( $posts[0]->ID ) ) ? $posts[0]->ID : '';
					
					//* When event is deleted inside GCal
					if ( $data['status'] == 'cancelled' ) {
						global $wpdb;
						$wpdb->update( $wpdb->posts, array( 'post_status' => 'cancelled' ), array( 'ID' => $appointment_id ) );
						continue;
					}
					
					// Fixed duration
					$appointment_duration		= $product->get_duration();
					$appointment_duration_unit	= $product->get_duration_unit();
					$total_duration				= $appointment_duration;
					if ( 'minute' === $product->get_duration_unit() ) {
						$total_duration_n = sprintf( _n( '%s minute', '%s minutes', $product->get_duration(), 'woocommerce-appointments' ), $product->get_duration() );
					} else {
						$total_duration_n = sprintf( _n( '%s hour', '%s hours', $product->get_duration(), 'woocommerce-appointments' ), $product->get_duration() );
					}

				    //* Update start time
				    if ( isset( $data['start']['dateTime'] ) ) {
				    	$start_date = new DateTime( $data['start']['dateTime'] );
						$start_date->setTimezone( new DateTimeZone( $wp_appintments_timezone ) );
						$sync_data['start'] = date( 'YmdHis', strtotime( $start_date->format( 'Y-m-d\TH:i:s\Z' ) ) );
						$sync_data['start_raw'] = strtotime( $start_date->format( 'Y-m-d\TH:i:s\Z' ) );
						$sync_data['_start_date'] = strtotime( $start_date->format( 'Y-m-d H:i:s' ) );
				    } else {
				    	$sync_data['start'] = date( 'YmdHis', strtotime( $data['start']['date'] ) );
						$sync_data['start_raw'] = strtotime( $data['start']['date'] );
						$sync_data['_start_date'] = strtotime( $start_date->format( 'Y-m-d' ) );
				    }

				    //* Update end time
				    if ( isset( $data['end']['dateTime'] ) ) {
					    $end_date = new DateTime( $data['end']['dateTime'] );
						$end_date->setTimezone( new DateTimeZone( $wp_appintments_timezone ) );
						$sync_data['end'] = date( 'YmdHis', strtotime( $end_date->format( 'Y-m-d\TH:i:s\Z' ) ) );
						$sync_data['end_raw'] = strtotime( $end_date->format( 'Y-m-d\TH:i:s\Z' ) );
						$sync_data['_end_date'] = strtotime( "+{$total_duration} {$appointment_duration_unit} - 1 second", $sync_data['_start_date'] );
					} else {
						$sync_data['end'] = date( 'YmdHis', strtotime( $data['end']['date'] ) );
						$sync_data['end_raw'] = strtotime( $data['end']['date'] );
						$sync_data['_end_date'] = strtotime( "+{$total_duration} {$appointment_duration_unit}", $sync_data['_start_date'] );
					}

					//* Update all day
					$sync_data['all_day'] = ( isset( $data['start']['date'] ) && isset( $data['end']['date'] ) ) ? 1 : 0;
					
					//* Update existing appointment data.
					if ( $appointment_id ) {
						
						//* prepare meta for updating
						$meta_args = apply_filters( 'wc_appointments_gcal_sync_order_itemmeta', array(
							'_appointment_start'         => $sync_data['start'],
							'_appointment_end'           => $sync_data['end'],
							'_appointment_all_day'       => intval( $sync_data['all_day'] ),
							'_appointment_product_id'    => $product_id,
						), $appointment_id, $data );

						//* Apply update from Google Calendar
						foreach ( $meta_args as $key => $value ) {
							update_post_meta( $appointment_id, $key, $value );
						}
					
					//* Add new appointment if it doesn't exist yet.
					} else {
												
						//* Calculate appointment cost
						$appointment_cost		= max( 0, $product->get_price() );

						if ( 'yes' === get_option( 'woocommerce_prices_include_tax' ) ) {
							if ( version_compare( WOOCOMMERCE_VERSION, '2.3', '<' ) ) {
								$base_tax_rates = WC_Tax::get_shop_base_rate( $product->tax_class );
							} else {
								$base_tax_rates = WC_Tax::get_base_tax_rates( $product->tax_class );
							}
							$base_taxes			= WC_Tax::calc_tax( $appointment_cost, $base_tax_rates, true );
							$appointment_cost   = round( $appointment_cost - array_sum( $base_taxes ), absint( get_option( 'woocommerce_price_num_decimals' ) ) );
						}
						
						//* Data to go into the appointment
						$new_appointment_data = array(
							'user_id'			=> '',
							'product_id'		=> $product_id,
							'summary'			=> ' &mdash; ' . $data['summary'],
							'cost'				=> $appointment_cost,
							'start_date'		=> $sync_data['start_raw'],
							'end_date'			=> $sync_data['end_raw'],
							'all_day'			=> $sync_data['all_day']
						);
						
						//* Assign an available staff automatically
						if ( $product->has_staff() ) {
							$available_appointments = $product->get_available_appointments( $sync_data['_start_date'], $sync_data['_end_date'], 0, 1 );
							
							if ( is_array( $available_appointments ) ) {								
								$shuffleKeys = array_keys( $available_appointments );
								shuffle( $shuffleKeys ); # randomize
								$staff = get_user_by( 'id', current( $shuffleKeys ) );
								$new_appointment_data['_staff_id'] = current( $shuffleKeys );
								$new_appointment_data['staff']     = $staff->display_name;
							}
						}
				
						//* Create the appointment itself
						//$new_appointment = create_wc_appointment( $product_id, $new_appointment_data, 'pending-confirmation', false );
						$new_appointment = get_wc_appointment( $new_appointment_data );
						$new_appointment->create( 'pending-confirmation' );
						
						//* Register new appointment ID
						$appointment_id = $new_appointment->id;
						
						// Sync appointment with GCal
						update_post_meta( $appointment_id, '_wc_appointments_gcal_event_id', $data['id'] );
						
					}

					// update_post_meta( $appointment_id, '_wc_appointments_gcal_sync_test', $response['body'] );

				}

				//* Save sync token for next update
				update_post_meta( $product_id, '_wc_appointments_gcal_sync_token', $response_data['nextSyncToken'] );

			}

		} else {

			if ( 'yes' == $this->debug ) {
				$this->log->add( $this->id, 'Error while synchronizing appointments' );
			}

		}

	}

	/**
	 * Remove/cancel the appointment in Google Calendar
	 *
	 * @param  int $appointment_id Appointment ID
	 *
	 * @return void
	 */
	public function remove_appointment( $appointment_id ) {
		$appointment	= get_wc_appointment( $appointment_id );
		$product		= $appointment->get_product();
		$event_id 		= get_post_meta( $appointment_id, '_wc_appointments_gcal_event_id', true );
		$calendar_id	= is_object( $product ) ? get_post_meta( $product->id, '_wc_appointments_gcal_calendar_id', true ) : '';

		if ( $event_id ) {
			$params = array(
				'method'    => 'DELETE',
				'sslverify' => false,
				'timeout'   => 60,
				'headers'   => array(
					'Content-Type'  => 'application/json',
					'Authorization' => 'Bearer ' . $this->get_access_token()
				)
			);

			if ( 'yes' == $this->debug ) {
				$this->log->add( $this->id, 'Removing appointment #' . $appointment_id . ' with Google Calendar...' );
			}

			$response = wp_remote_post( $this->calendars_uri . $calendar_id . '/events/' . $event_id, $params );

			if ( ! is_wp_error( $response ) && 204 == $response['response']['code'] ) {
				if ( 'yes' == $this->debug ) {
					$this->log->add( $this->id, 'Appointment removed successfully!' );
				}

				// Remove event ID
				delete_post_meta( $appointment_id, '_wc_appointments_gcal_event_id' );

			} else {
				if ( 'yes' == $this->debug ) {
					$this->log->add( $this->id, 'Error while removing the appointment #' . $appointment_id . ': ' . print_r( $response, true ) );
				}
			}
		}
	}

	/**
	 * Sync Appointment with Google Calendar when appointment is edited
	 *
	 * @param  int $appointment_id Appointment ID
	 *
	 * @return void
	 */
	public function sync_edited( $appointment_id ) {
		global $wpdb;

		$status = $wpdb->get_var( $wpdb->prepare( "SELECT post_status FROM $wpdb->posts WHERE post_type = 'wc_appointment' AND ID = %d", $appointment_id ) );
		
		if ( 'cancelled' == $status ) {
			$this->remove_appointment( $appointment_id );
		} else if ( in_array( $status, apply_filters( 'woocommerce_appointments_gcal_sync_statuses', array( 'confirmed', 'paid', 'complete' ) ) ) ) {
			$this->sync_appointment( $appointment_id );
		}
	}
}

/**
 * Returns the main instance of WC_Appointments_Integration_GCal to prevent the need to use globals.
 *
 * @return WC_Appointments_Integration_GCal
 */
function wc_appointments_integration_gcal() {
	return WC_Appointments_Integration_GCal::instance();
}

add_action( 'init', 'integration_gcal' );
function integration_gcal() {
	return wc_appointments_integration_gcal();
}