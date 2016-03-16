<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Handles email sending
 */
class WC_Appointments_Email_Manager {

	/**
	 * Constructor sets up actions
	 */
	public function __construct() {
		add_filter( 'woocommerce_email_classes', array( $this, 'init_emails' ) );

		// Email Actions
		$email_actions = array(
			// New & Pending Confirmation
			'woocommerce_appointment_in-cart_to_paid',
			'woocommerce_appointment_in-cart_to_pending-confirmation',
			'woocommerce_appointment_unpaid_to_paid',
			'woocommerce_appointment_unpaid_to_pending-confirmation',
			'woocommerce_appointment_confirmed_to_paid',
			'woocommerce_new_appointment',
			'woocommerce_admin_new_appointment',

			// Confirmed
			'woocommerce_appointment_confirmed',

			// Cancelled
			'woocommerce_appointment_pending-confirmation_to_cancelled',
			'woocommerce_appointment_confirmed_to_cancelled',
			'woocommerce_appointment_paid_to_cancelled'
		);

		foreach ( $email_actions as $action ) {
			if ( version_compare( WC_VERSION, '2.3', '<' ) ) {
				add_action( $action, array( $GLOBALS['woocommerce'], 'send_transactional_email' ), 10, 10 );
			} else {
				add_action( $action, array( 'WC_Emails', 'send_transactional_email' ), 10, 10 );
			}
		}

		add_filter( 'woocommerce_email_attachments', array( $this, 'attach_ics_file' ), 10, 3 );

		add_filter( 'woocommerce_template_directory', array( $this, 'template_directory' ), 10, 2 );
	}

	/**
	 * Include our mail templates
	 *
	 * @param  array $emails
	 * @return array
	 */
	public function init_emails( $emails ) {
		if ( ! isset( $emails['WC_Email_New_Appointment'] ) ) {
			$emails['WC_Email_New_Appointment'] = include( 'emails/class-wc-email-new-appointment.php' );
		}

		if ( ! isset( $emails['WC_Email_Appointment_Reminder'] ) ) {
			$emails['WC_Email_Appointment_Reminder'] = include( 'emails/class-wc-email-appointment-reminder.php' );
		}

		if ( ! isset( $emails['WC_Email_Appointment_Confirmed'] ) ) {
			$emails['WC_Email_Appointment_Confirmed'] = include( 'emails/class-wc-email-appointment-confirmed.php' );
		}

		if ( ! isset( $emails['WC_Email_Appointment_Notification'] ) ) {
			$emails['WC_Email_Appointment_Notification'] = include( 'emails/class-wc-email-appointment-notification.php' );
		}

		if ( ! isset( $emails['WC_Email_Appointment_Cancelled'] ) ) {
			$emails['WC_Email_Appointment_Cancelled'] = include( 'emails/class-wc-email-appointment-cancelled.php' );
		}
		
		if ( ! isset( $emails['WC_Email_Admin_Appointment_Cancelled'] ) ) {
			$emails['WC_Email_Admin_Appointment_Cancelled'] = include( 'emails/class-wc-email-admin-appointment-cancelled.php' );
		}

		return $emails;
	}

	/**
	 * Attach the .ics files in the emails.
	 *
	 * @param  array  $attachments
	 * @param  string $email_id
	 * @param  mixed  $appointment
	 *
	 * @return array
	 */
	public function attach_ics_file( $attachments, $email_id, $appointment ) {
		$available = apply_filters( 'woocommerce_appointments_emails_ics', array( 'appointment_confirmed', 'appointment_reminder' ) );

		if ( in_array( $email_id, $available ) ) {
			$generate = new WC_Appointments_ICS_Exporter;
			$attachments[] = $generate->get_appointment_ics( $appointment );
		}

		return $attachments;
	}

	/**
	 * Custom template directory.
	 *
	 * @param  string $directory
	 * @param  string $template
	 *
	 * @return string
	 */
	public function template_directory( $directory, $template ) {
		if ( false !== strpos( $template, '-appointment' ) ) {
			return 'woocommerce-appointments';
		}

		return $directory;
	}
}

$GLOBALS['wc_appointments_email_manager'] = new WC_Appointments_Email_Manager();
