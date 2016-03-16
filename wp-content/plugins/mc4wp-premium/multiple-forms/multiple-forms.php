<?php

defined( 'ABSPATH' ) or exit;

$widget_enhancements = new MC4WP_Form_Widget_Enhancements();
$widget_enhancements->add_hooks();

if( is_admin() ) {
	$plugin = new MC4WP_Plugin( __FILE__, MC4WP_PREMIUM_VERSION );
	$admin = new MC4WP_Multiple_Forms_Admin( $plugin );
	$admin->add_hooks();
}
