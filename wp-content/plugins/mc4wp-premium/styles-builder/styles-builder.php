<?php

defined( 'ABSPATH' ) or exit;

$plugin = new MC4WP_Plugin( __FILE__, MC4WP_PREMIUM_VERSION );

if( is_admin() && ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) ) {

	$admin = new MC4WP_Styles_Builder_Admin( $plugin );
	$admin->add_hooks();

}


$public = new MC4WP_Styles_Builder_Public( $plugin );
$public->add_hooks();


