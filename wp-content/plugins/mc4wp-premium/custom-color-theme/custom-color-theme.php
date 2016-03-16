<?php

defined( 'ABSPATH' ) or exit;

$plugin = new MC4WP_Plugin( __FILE__ , MC4WP_PREMIUM_VERSION );

$custom_color_theme = new MC4WP_Custom_Color_Theme( $plugin );
$custom_color_theme->add_hooks();

if( is_admin() && ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) ) {
	$custom_color_theme_admin = new MC4WP_Custom_Color_Theme_Admin( $plugin );
	$custom_color_theme_admin->add_hooks();
}
