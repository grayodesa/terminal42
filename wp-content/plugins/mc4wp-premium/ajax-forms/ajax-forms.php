<?php

defined( 'ABSPATH' ) or exit;


$plugin = new MC4WP_Plugin( __FILE__, MC4WP_PREMIUM_VERSION );

// main functionality
require_once dirname( __FILE__ ) . '/includes/class-ajax-forms.php';
$ajax_forms = new MC4WP_AJAX_Forms( $plugin );
$ajax_forms->add_hooks();

if( is_admin() ) {
	require_once dirname( __FILE__ ) . '/includes/class-admin.php';
	$admin = new MC4WP_AJAX_Forms_Admin( $plugin );
	$admin->add_hooks();
}