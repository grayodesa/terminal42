<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Handle frontend forms
 */
class WC_Appointment_Form_Handler {

	/**
	 * Hook in methods
	 */
	public static function init() {
		add_action( 'init', array( __CLASS__, 'cancel_appointment' ) );
	}

	/**
	 * Cancel a appointment.
	 */
	public static function cancel_appointment() {
		if ( isset( $_GET['cancel_appointment'] ) && isset( $_GET['appointment_id'] ) ) {

			$appointment_id         = absint( $_GET['appointment_id'] );
			$appointment            = get_wc_appointment( $appointment_id );
			$appointment_can_cancel = $appointment->has_status( get_wc_appointment_statuses( 'cancel' ) );
			$redirect           	= $_GET['redirect'];

			if ( $appointment->has_status( 'cancelled' ) ) {
				// Already cancelled - take no action
			} elseif ( $appointment_can_cancel && $appointment->id == $appointment_id && isset( $_GET['_wpnonce'] ) && wp_verify_nonce( $_GET['_wpnonce'], 'woocommerce-appointments-cancel_appointment' ) ) {
				// Cancel the appointment
				$appointment->update_status( 'cancelled' );
				WC_Cache_Helper::get_transient_version( 'appointments', true );

				// Message
				wc_add_notice( apply_filters( 'woocommerce_appointment_cancelled_notice', __( 'Your appointment has been cancelled.', 'woocommerce-appointments' ) ), apply_filters( 'woocommerce_appointment_cancelled_notice_type', 'notice' ) );

				do_action( 'woocommerce_appointments_cancelled_appointment', $appointment->id );
			} elseif ( ! $appointment_can_cancel ) {
				wc_add_notice( __( 'Your appointment can no longer be cancelled. Please contact us if you need assistance.', 'woocommerce-appointments' ), 'error' );
			} else {
				wc_add_notice( __( 'Invalid appointment.', 'woocommerce-appointments' ), 'error' );
			}

			if ( $redirect ) {
				wp_safe_redirect( $redirect );
				exit;
			}
		}
	}
}

WC_Appointment_Form_Handler::init();
