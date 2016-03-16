<?php
/**
 * Customer appointment confirmed email
 */
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

echo "= " . $email_heading . " =\n\n";

if ( $appointment->get_order() ) {
	echo sprintf( __( 'Hello %s', 'woocommerce-appointments' ), $appointment->get_order()->billing_first_name ) . "\n\n";
}

echo __(  'Your appointment for has been confirmed. The details of your appointment are shown below.', 'woocommerce-appointments' ) . "\n\n";

echo "=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=\n\n";

echo sprintf( __( 'Scheduled: %s', 'woocommerce-appointments'), $appointment->get_product()->get_title() ) . "\n";
echo sprintf( __( 'Appointment ID: %s', 'woocommerce-appointments'), $appointment->get_id() ) . "\n";

if ( $appointment->has_staff() && ( $staff = $appointment->get_staff_member() ) ) {
	echo sprintf( __( 'Appointment Provider: %s', 'woocommerce-appointments'), $staff->display_name ) . "\n";
}

echo sprintf( __( 'Appointment Date: %s', 'woocommerce-appointments'), $appointment->get_start_date( wc_date_format(), '' ) ) . "\n";
echo sprintf( __( 'Appointment Time: %s', 'woocommerce-appointments'), $appointment->get_start_date( '', get_option( 'time_format' ) ) . ' &mdash; ' . $appointment->get_end_date( '', get_option( 'time_format' ) ) ) . "\n";

echo "\n=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=\n\n";

if ( $order = $appointment->get_order() ) {
	if ( $order->status == 'pending' ) {
		echo sprintf( __( 'To pay for this appointment please use the following link: %s', 'woocommerce-appointments' ), $order->get_checkout_payment_url() ) . "\n\n";
	}

	do_action( 'woocommerce_email_before_order_table', $order, $sent_to_admin, $plain_text );

	echo sprintf( __( 'Order number: %s', 'woocommerce-appointments'), $order->get_order_number() ) . "\n";
	echo sprintf( __( 'Order date: %s', 'woocommerce-appointments'), date_i18n( wc_date_format(), strtotime( $order->order_date ) ) ) . "\n";

	do_action( 'woocommerce_email_order_meta', $order, $sent_to_admin, $plain_text );

	echo "\n";

	switch ( $order->status ) {
		case "completed" :
			echo $order->email_order_items_table( $order->is_download_permitted(), false, true, '', '', true );
		break;
		case "processing" :
			echo $order->email_order_items_table( $order->is_download_permitted(), true, true, '', '', true );
		break;
		default :
			echo $order->email_order_items_table( $order->is_download_permitted(), true, false, '', '', true );
		break;
	}

	echo "==========\n\n";

	if ( $totals = $order->get_order_item_totals() ) {
		foreach ( $totals as $total ) {
			echo $total['label'] . "\t " . $total['value'] . "\n";
		}
	}

	echo "\n=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=\n\n";

	do_action( 'woocommerce_email_after_order_table', $order, $sent_to_admin, $plain_text );
}

echo apply_filters( 'woocommerce_email_footer_text', get_option( 'woocommerce_email_footer_text' ) );