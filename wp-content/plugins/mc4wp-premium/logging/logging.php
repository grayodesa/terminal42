<?php

defined( 'ABSPATH' ) or exit;

$plugin = new MC4WP_Plugin( __FILE__, MC4WP_PREMIUM_VERSION );

if( is_admin() && ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) ) {
	$logging_admin = new MC4WP_Logging_Admin( $plugin );
	$logging_admin->add_hooks();
}

$logger = new MC4WP_Logger();
$logger->add_hooks();

