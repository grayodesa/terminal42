<?php

class Appointments_Admin_User_Profile {

	public function __construct() {
		add_action( 'show_user_profile', array( $this, 'show_profile') );
		add_action( 'edit_user_profile', array( $this, 'show_profile') );

		add_action( 'personal_options_update', array( $this, 'save_profile') );
		add_action( 'edit_user_profile_update', array( $this, 'save_profile') );
	}

	/**
	 * Saves working hours from user profile
	 */
	function save_profile( $profileuser_id ) {
		global $current_user, $wpdb, $appointments;

		// Only user himself can save his data
		if ( ( $current_user->ID == $profileuser_id ) || ( $current_user->ID != $profileuser_id && App_Roles::current_user_can( 'list_users', CTX_STAFF ) ) ) {


			// Save user meta
			if ( isset( $_POST['app_name'] ) ) {
				update_user_meta( $profileuser_id, 'app_name', $_POST['app_name'] );
			}
			if ( isset( $_POST['app_email'] ) ) {
				update_user_meta( $profileuser_id, 'app_email', $_POST['app_email'] );
			}
			if ( isset( $_POST['app_phone'] ) ) {
				update_user_meta( $profileuser_id, 'app_phone', $_POST['app_phone'] );
			}
			if ( isset( $_POST['app_address'] ) ) {
				update_user_meta( $profileuser_id, 'app_address', $_POST['app_address'] );
			}
			if ( isset( $_POST['app_city'] ) ) {
				update_user_meta( $profileuser_id, 'app_city', $_POST['app_city'] );
			}


			// Cancel appointment
			if ( isset( $appointments->options['allow_cancel'] ) && 'yes' == $appointments->options['allow_cancel'] &&
			     isset( $_POST['app_cancel'] ) && is_array( $_POST['app_cancel'] ) && ! empty( $_POST['app_cancel'] )
			) {
				foreach ( $_POST['app_cancel'] as $app_id => $value ) {
					if ( appointments_update_appointment_status( $app_id, 'removed' ) ) {
						$appointments->log( sprintf( __( 'Client %s cancelled appointment with ID: %s', 'appointments' ), $appointments->get_client_name( $app_id ), $app_id ) );
						appointments_send_cancel_notification( $app_id );
					}
				}
			}

			// Only user who is a worker can save the rest
			if ( ! appointments_is_worker( $profileuser_id ) ) {
				return;
			}

			// Confirm an appointment using profile page
			if ( isset( $_POST['app_confirm'] ) && is_array( $_POST['app_confirm'] ) && ! empty( $_POST['app_confirm'] ) ) {
				foreach ( $_POST['app_confirm'] as $app_id => $value ) {
					if ( appointments_update_appointment_status( $app_id, 'confirmed' ) ) {
						$appointments->log( sprintf( __( 'Service Provider %s manually confirmed appointment with ID: %s', 'appointments' ), appointments_get_worker_name( $current_user->ID ), $app_id ) );
					}
				}
			}

			// Save working hours table
			// Do not save these if we are coming from BuddyPress confirmation tab
			if ( isset( $appointments->options["allow_worker_wh"] ) && 'yes' == $appointments->options["allow_worker_wh"] && isset( $_POST['open'] ) && isset( $_POST['closed'] ) ) {
				$result   = $result2 = false;
				$location = 0;
				foreach ( array( 'closed', 'open' ) as $stat ) {
					$count = $wpdb->get_var( $wpdb->prepare(
						"SELECT COUNT(*) FROM {$appointments->wh_table} WHERE location=%d AND worker=%d AND status=%s",
						$location, $profileuser_id, $stat
					) );

					if ( $count > 0 ) {
						$result = $wpdb->update( $appointments->wh_table,
							array( 'hours' => serialize( $_POST[ $stat ] ), 'status' => $stat ),
							array( 'location' => $location, 'worker' => $profileuser_id, 'status' => $stat ),
							array( '%s', '%s' ),
							array( '%d', '%d', '%s' )
						);
					} else {
						$result = $wpdb->insert( $appointments->wh_table,
							array(
								'location' => $location,
								'worker'   => $profileuser_id,
								'hours'    => serialize( $_POST[ $stat ] ),
								'status'   => $stat
							),
							array( '%d', '%d', '%s', '%s' )
						);
					}
					// Save exceptions
					$count2 = $wpdb->get_var( $wpdb->prepare(
						"SELECT COUNT(*) FROM {$appointments->exceptions_table} WHERE location=%d AND worker=%d AND status=%s",
						$location, $profileuser_id, $stat
					) );

					if ( $count2 > 0 ) {
						$result2 = $wpdb->update( $appointments->exceptions_table,
							array(
								'days'   => $_POST[ $stat ]["exceptional_days"],
								'status' => $stat
							),
							array(
								'location' => $location,
								'worker'   => $profileuser_id,
								'status'   => $stat
							),
							array( '%s', '%s' ),
							array( '%d', '%d', '%s' )
						);
					} else {
						$result2 = $wpdb->insert( $appointments->exceptions_table,
							array(
								'location' => $location,
								'worker'   => $profileuser_id,
								'days'     => $_POST[ $stat ]["exceptional_days"],
								'status'   => $stat
							),
							array( '%d', '%d', '%s', '%s' )
						);
					}


				}
				if ( $result || $result2 ) {
					$message = sprintf( __( '%s edited his working hours.', 'appointments' ), appointments_get_worker_name( $profileuser_id ) );
					$appointments->log( $message );
					// Employer can be noticed here
					do_action( "app_working_hour_update", $message, $profileuser_id );
					// Also clear cache
					$appointments->flush_cache();
				}
			}
		}
	}

	/**
	 * Displays appointment schedule on the user profile
	 *
	 * @param WP_User $profileuser
	 */
	function show_profile( $profileuser ) {
		global $current_user, $appointments;

		// Only user or admin can see his data
		if ( $current_user->ID != $profileuser->ID && ! App_Roles::current_user_can( 'list_users', 'staff' ) ) {
			return;
		}

		$this->personal_data( $profileuser );

		if ( appointments_is_worker( $profileuser->ID ) ) {
			$this->worker_appointments( $profileuser );

			$options = appointments_get_options();
			if ( ! empty( $options["allow_worker_wh"] ) && 'yes' == $options["allow_worker_wh"] ) {
				$this->my_working_hours( $profileuser );
			}

		}
		else {
			$this->my_appointments( $profileuser );
		}
	}

	private function personal_data( $profileuser ) {
		$current_user = wp_get_current_user();

		// For other than user himself, display data as readonly
		$is_readonly = ! disabled( $current_user->ID, $profileuser->ID, false );
		$is_readonly = apply_filters( 'app_show_profile_readonly', $is_readonly, $profileuser );

		include_once( appointments_plugin_dir() . 'admin/views/user-profile-personal-data.php' );
	}

	private function my_appointments( $profileuser ) {
		$options = appointments_get_options();

		if ( isset( $options["gcal"] ) && 'yes' == $options["gcal"] ) {
			$gcal = '';
		} // Default is already enabled
		else {
			$gcal = ' gcal="0"';
		}

		$allow_cancel = isset( $options['allow_cancel'] ) && 'yes' == $options['allow_cancel'];

		include_once( appointments_plugin_dir() . 'admin/views/user-profile-my-appointments.php' );
	}

	private function worker_appointments( $profileuser ) {
		$options = appointments_get_options();

		if ( isset( $options["gcal"] ) && 'yes' == $options["gcal"] ) {
			$gcal = '';
		} // Default is already enabled
		else {
			$gcal = ' gcal="0"';
		}

		$allow_worker_confirm = isset( $options['allow_worker_confirm'] ) && 'yes' == $options['allow_worker_confirm'];

		include_once( appointments_plugin_dir() . 'admin/views/user-profile-worker-appointments.php' );
	}

	private function my_working_hours( $profileuser ) {
		$appointments = appointments();

		// A little trick to pass correct lsw variables to the related function
		$_REQUEST["app_location_id"] = 0;
		$_REQUEST["app_provider_id"] = $profileuser->ID;

		$appointments->get_lsw();

		$result = array();
		$result_open = $appointments->get_exception( $appointments->location, $appointments->worker, 'open' );
		if ( $result_open ) {
			$result["open"] = $result_open->days;
		} else {
			$result["open"] = null;
		}

		$result_closed = $appointments->get_exception( $appointments->location, $appointments->worker, 'closed' );
		if ( $result_closed ) {
			$result["closed"] = $result_closed->days;
		} else {
			$result["closed"] = null;
		}

		include_once( appointments_plugin_dir() . 'admin/views/user-profile-working-hours.php' );
	}
}