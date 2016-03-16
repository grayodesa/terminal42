<?php

if ( ! defined( 'ABSPATH' ) )
	exit;

/**
 * WC_Appointments_Admin_Settings
 */
class WC_Appointments_Admin_Settings extends WC_Settings_Page {
	

	/**
	 * Setup settings class
	 *
	 * @since  1.0
	 */
	public function __construct() {

		//* Appointments Settings ID.
		$this->id    = 'appointments';
		$this->label = __( 'Appointments', 'woocommerce-appointments' );
		
		//* Appointments Settings setup.
		add_filter( 'woocommerce_settings_tabs_array', array( $this, 'add_settings_page' ), 20 );
		add_action( 'woocommerce_settings_' . $this->id, array( $this, 'output' ) );
		add_action( 'woocommerce_settings_save_' . $this->id, array( $this, 'save' ) );
		add_action( 'woocommerce_sections_' . $this->id, array( $this, 'output_sections' ) );
		
	}


	/**
	 * Get sections
	 *
	 * @return array
	 */
	public function get_sections() {

		$sections = array(
			'' 			=> __( 'Global Availablity', 'woocommerce-appointments' ),
			// 'notify' 	=> __( 'Send Notification', 'woocommerce-appointments' ),
			'gcal'		=> __( 'Google Calendar', 'woocommerce-appointments' )
		);

		return apply_filters( 'woocommerce_get_sections_' . $this->id, $sections );
	}


	/**
	 * Output the settings
	 *
	 * @since 1.0
	 */
	public function output() {
		global $current_section;
		
		if ( $current_section == '' ) {
			include( 'views/html-settings-global-availability.php' );
		} else if ( $current_section == 'notify' ) {
			include( 'views/html-settings-send-notifications.php' );
		} else if ( $current_section == 'gcal' ) {
			integration_gcal()->admin_options();
		} else {
			$settings = $this->get_settings( $current_section );
			WC_Admin_Settings::output_fields( $settings );
		}
	}


	/**
	 * Save settings
	 */
	public function save() {
		global $current_section;
		
		if ( $current_section == '' ) {
			$this->save_global_availability();
		} else if ( $current_section == 'notify' ) {
			$this->send_notification_action();
		} else if ( $current_section == 'gcal' ) {
			integration_gcal()->process_admin_options();
		} else {
			$settings = $this->get_settings( $current_section );
			WC_Admin_Settings::save_fields( $settings );
		}
		
		if ( $current_section ) {
			do_action( 'woocommerce_update_options_' . $this->id . '_' . $current_section );
		}

	}
	
	
	/**
	 * Save global availability
	 */
	public function save_global_availability() {
		// Save the field values
		if ( ! empty( $_POST['appointments_availability_submitted'] ) ) {
			$availability = array();
			$row_size     = isset( $_POST[ 'wc_appointment_availability_type' ] ) ? sizeof( $_POST[ 'wc_appointment_availability_type' ] ) : 0;
			for ( $i = 0; $i < $row_size; $i ++ ) {
				$availability[ $i ]['type']     = wc_clean( $_POST[ 'wc_appointment_availability_type' ][ $i ] );
				$availability[ $i ]['appointable'] = wc_clean( $_POST[ 'wc_appointment_availability_appointable' ][ $i ] );
				$availability[ $i ]['qty'] = wc_clean( $_POST[ 'wc_appointment_availability_qty' ][ $i ] );

				switch ( $availability[ $i ]['type'] ) {
					case 'custom' :
						$availability[ $i ]['from'] = wc_clean( $_POST[ 'wc_appointment_availability_from_date' ][ $i ] );
						$availability[ $i ]['to']   = wc_clean( $_POST[ 'wc_appointment_availability_to_date' ][ $i ] );
					break;
					case 'months' :
						$availability[ $i ]['from'] = wc_clean( $_POST[ 'wc_appointment_availability_from_month' ][ $i ] );
						$availability[ $i ]['to']   = wc_clean( $_POST[ 'wc_appointment_availability_to_month' ][ $i ] );
					break;
					case 'weeks' :
						$availability[ $i ]['from'] = wc_clean( $_POST[ 'wc_appointment_availability_from_week' ][ $i ] );
						$availability[ $i ]['to']   = wc_clean( $_POST[ 'wc_appointment_availability_to_week' ][ $i ] );
					break;
					case 'days' :
						$availability[ $i ]['from'] = wc_clean( $_POST[ 'wc_appointment_availability_from_day_of_week' ][ $i ] );
						$availability[ $i ]['to']   = wc_clean( $_POST[ 'wc_appointment_availability_to_day_of_week' ][ $i ] );
					break;
					/* DEPRECATED
					case 'time_date' :
						$availability[ $i ]['from'] = wc_appointment_sanitize_time( $_POST[ 'wc_appointment_availability_from_time' ][ $i ] );
						$availability[ $i ]['to']   = wc_appointment_sanitize_time( $_POST[ 'wc_appointment_availability_to_time' ][ $i ] );
						$availability[ $i ]['on'] 	= wc_clean( $_POST[ 'wc_appointment_availability_on_date' ][ $i ] );
					break;
					*/
					case 'time' :
					case 'time:1' :
					case 'time:2' :
					case 'time:3' :
					case 'time:4' :
					case 'time:5' :
					case 'time:6' :
					case 'time:7' :
						$availability[ $i ]['from'] = wc_appointment_sanitize_time( $_POST[ 'wc_appointment_availability_from_time' ][ $i ] );
						$availability[ $i ]['to']   = wc_appointment_sanitize_time( $_POST[ 'wc_appointment_availability_to_time' ][ $i ] );
					break;
					case 'time:range' :
						$availability[ $i ]['from'] = wc_appointment_sanitize_time( $_POST[ "wc_appointment_availability_from_time" ][ $i ] );
						$availability[ $i ]['to']   = wc_appointment_sanitize_time( $_POST[ "wc_appointment_availability_to_time" ][ $i ] );

						$availability[ $i ]['from_date'] = wc_clean( $_POST[ 'wc_appointment_availability_from_date' ][ $i ] );
						$availability[ $i ]['to_date']   = wc_clean( $_POST[ 'wc_appointment_availability_to_date' ][ $i ] );
					break;
				}
			}
			update_option( 'wc_global_appointment_availability', $availability );
		}
	}
	
	/**
	 * Send notification
	 */
	public function send_notification_action() {
		// Send notification
		if ( ! empty( $_POST ) && check_admin_referer( 'send_appointment_notification' ) ) {
			$notification_product_id = absint( $_POST['notification_product_id'] );
			$notification_subject    = wc_clean( stripslashes( $_POST['notification_subject'] ) );
			$notification_message    = wp_kses_post( stripslashes( $_POST['notification_message'] ) );

			try {

				if ( ! $notification_product_id )
					throw new Exception( __( 'Please choose a product', 'woocommerce-appointments' ) );

				if ( ! $notification_message )
					throw new Exception( __( 'Please enter a message', 'woocommerce-appointments' ) );

				if ( ! $notification_subject )
					throw new Exception( __( 'Please enter a subject', 'woocommerce-appointments' ) );

				$appointments        = WC_Appointments_Controller::get_appointments_for_product( $notification_product_id );
				$mailer              = WC()->mailer();
				$notification        = $mailer->emails['WC_Email_Appointment_Notification'];
				$attachments         = array();

				foreach ( $appointments as $appointment ) {
					// Add .ics file
					if ( isset( $_POST['notification_ics'] ) ) {
						$generate = new WC_Appointments_ICS_Exporter;
						$attachments[] = $generate->get_appointment_ics( $appointment );
					}

					$notification->trigger( $appointment->id, $notification_subject, $notification_message, $attachments );
				}

				echo '<div class="updated fade"><p>' . __( 'Notification sent successfully', 'woocommerce-appointments' ) . '</p></div>';

			} catch( Exception $e ) {
				echo '<div class="error"><p>' . $e->getMessage() . '</p></div>';
			}
		}
	}

}

$GLOBALS['wc_appointments_admin_settings'] = new WC_Appointments_Admin_Settings();
