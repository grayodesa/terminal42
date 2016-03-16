<?php

defined( 'ABSPATH' ) or exit;

// do nothing for frontend facing requests
if( ! is_admin() ) {
	return;
}

global $mc4wp_license_manager;

$product = new MC4WP_Product();
$license_manager = new DVK_Plugin_License_Manager( $product );
$license_manager->setup_hooks();

$mc4wp_license_manager = $license_manager;

/**
 * @ignore
 * @access private
 *
 * @param array $opts
 */
function __mc4wp_premium_show_license_form( ) {
	global $mc4wp_license_manager;
	echo '<div class="mc4wp-license-form medium-margin">';
	$mc4wp_license_manager->show_license_form( false );
	echo '</div>';
}

add_action( 'mc4wp_admin_before_other_settings', '__mc4wp_premium_show_license_form' );