<?php
/**
 * Appointments Uninstall
 */
if( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit();
}

global $wpdb;

// Tables
$wpdb->query( "DROP TABLE IF EXISTS " . $wpdb->prefix . "wc_appointment_relationships" );