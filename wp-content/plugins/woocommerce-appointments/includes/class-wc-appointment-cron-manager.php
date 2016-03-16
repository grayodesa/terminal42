<?php
if ( ! defined( 'ABSPATH' ) )
	exit;

/**
 * Cron job handler
 */
class WC_Appointments_Cron_Manager {

	/**
	 * Constructor
	 */
	public function __construct() {
		add_action( 'wc-appointment-reminder', array( $this, 'send_appointment_reminder' ) );
		add_action( 'wc-appointment-complete', array( $this, 'mark_appointment_complete' ) );
		add_action( 'wc-appointment-remove-inactive-cart', array( $this, 'remove_inactive_appointment_from_cart' ) );
	}

	/**
	 * Send appointment reminder email
	 */
	public function send_appointment_reminder( $appointment_id ) {
		$mailer   = WC()->mailer();
		$reminder = $mailer->emails['WC_Email_Appointment_Reminder'];
		$reminder ->trigger( $appointment_id );
	}

	/**
	 * Change the appointment status
	 */
	public function mark_appointment_complete( $appointment_id ) {
		$appointment = get_wc_appointment( $appointment_id );
		$appointment->update_status( 'complete' );
	}

	/**
	 * Remove inactive appointment
	 */
	public function remove_inactive_appointment_from_cart( $appointment_id ) {
		if ( $appointment_id && ( $appointment = get_wc_appointment( $appointment_id ) ) && $appointment->has_status( 'in-cart' ) ) {
			wp_delete_post( $appointment_id );
		}
	}
}

$GLOBALS['wc_appointments_cron_manager'] = new WC_Appointments_Cron_Manager();
