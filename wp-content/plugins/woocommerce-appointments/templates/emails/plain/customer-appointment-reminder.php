<?php
/**
 * Customer appointment reminder email
 */
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

echo "= " . $email_heading . " =\n\n";

if ( $appointment->get_order() ) {
	echo sprintf( __( 'Hello %s', 'woocommerce-appointments' ), $appointment->get_order()->billing_first_name ) . "\n\n";
}

echo __(  'This is a reminder that your appointment will take place tomorrow. The details of your appointment are shown below.', 'woocommerce-appointments' ) . "\n\n";

echo "=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=\n\n";

echo sprintf( __( 'Scheduled: %s', 'woocommerce-appointments'), $appointment->get_product()->get_title() ) . "\n";
echo sprintf( __( 'Appointment ID: %s', 'woocommerce-appointments'), $appointment->get_id() ) . "\n";

if ( $appointment->has_staff() && ( $staff = $appointment->get_staff_member() ) ) {
	echo sprintf( __( 'Appointment Provider: %s', 'woocommerce-appointments'), $staff->display_name ) . "\n";
}

echo sprintf( __( 'Appointment Date: %s', 'woocommerce-appointments'), $appointment->get_start_date( wc_date_format(), '' ) ) . "\n";
echo sprintf( __( 'Appointment Time: %s', 'woocommerce-appointments'), $appointment->get_start_date( '', get_option( 'time_format' ) ) . ' &mdash; ' . $appointment->get_end_date( '', get_option( 'time_format' ) ) ) . "\n";

echo "\n=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=\n\n";

echo apply_filters( 'woocommerce_email_footer_text', get_option( 'woocommerce_email_footer_text' ) );