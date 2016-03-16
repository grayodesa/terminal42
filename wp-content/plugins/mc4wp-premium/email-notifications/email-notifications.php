<?php

defined( 'ABSPATH' ) or exit;

$plugin = new MC4WP_Plugin( __FILE__, MC4WP_PREMIUM_VERSION );

$factory = new MC4WP_Form_Notification_Factory( $plugin );
$factory->add_hooks();

if( is_admin() ) {
	$admin = new MC4WP_Form_Notifications_Admin( $plugin );
	$admin->add_hooks();
}