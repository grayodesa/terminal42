<?php
/*
Plugin Name: Event Tickets Plus
Description: Event Tickets Plus allows you to sell tickets to events
Version: 4.1.2
Author: Modern Tribe, Inc.
Author URI: http://m.tri.be/28
License: GPLv2 or later
Text Domain: event-tickets-plus
Domain Path: /lang/
 */

/*
 Copyright 2010-2012 by Modern Tribe Inc and the contributors

 This program is free software; you can redistribute it and/or
 modify it under the terms of the GNU General Public License
 as published by the Free Software Foundation; either version 2
 of the License, or (at your option) any later version.

 This program is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 GNU General Public License for more details.

 You should have received a copy of the GNU General Public License
 along with this program; if not, write to the Free Software
 Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 */

if ( ! defined( 'ABSPATH' ) ) die( '-1' );

define( 'EVENT_TICKETS_PLUS_DIR', dirname( __FILE__ ) );
define( 'EVENT_TICKETS_PLUS_FILE', __FILE__ );

add_action( 'plugins_loaded', 'event_tickets_plus_init', 9 );

function event_tickets_plus_init() {
	tribe_init_tickets_plus_autoloading();

	$langpath = trailingslashit( basename( dirname( __FILE__ ) ) ) . 'lang/';
	load_plugin_textdomain( 'event-tickets-plus', false, $langpath );

	if ( event_tickets_plus_is_incompatible_tickets_core_installed() ) {
		add_action( 'admin_notices', 'event_tickets_plus_show_fail_message' );
		return;
	}

	Tribe__Tickets_Plus__Main::instance();
}

/**
 * Requires the autoloader class from the main plugin class and sets up
 * autoloading.
 */
function tribe_init_tickets_plus_autoloading() {
	if ( ! class_exists( 'Tribe__Autoloader' ) ) {
		return;
	}

	$autoloader = Tribe__Autoloader::instance();
	$autoloader->register_prefix( 'Tribe__Tickets_Plus__', dirname( __FILE__ ) . '/src/Tribe' );
	$autoloader->register_autoloader();
}

/**
 * Whether the current version is incompatible with the installed and active The Events Calendar
 * @return bool
 */
function event_tickets_plus_is_incompatible_tickets_core_installed () {
	if ( ! class_exists( 'Tribe__Tickets__Main' ) ) {
		return true;
	}

	if ( ! class_exists( 'Tribe__Tickets_Plus__Main' ) ) {
		return true;
	}

	if ( ! version_compare( Tribe__Tickets__Main::VERSION, Tribe__Tickets_Plus__Main::REQUIRED_TICKETS_VERSION, '>=' ) ) {
		return true;
	}

	return false;
}


/**
 * Shows an admin_notices message explaining why it couldn't be activated.
 */
function event_tickets_plus_show_fail_message() {
	if ( ! current_user_can( 'activate_plugins' ) )
		return;

	$url = add_query_arg( array(
		'tab'       => 'plugin-information',
		'plugin'    => 'event-tickets',
		'TB_iframe' => 'true',
	), admin_url( 'plugin-install.php' ) );

	$title = __( 'Event Tickets', 'event-tickets-plus' );

	echo '<div class="error"><p>';

	printf( __( 'To begin using Event Tickets Plus, please install and activate the latest version of <a href="%s" class="thickbox" title="%s">%s</a>.', 'event-tickets-plus' ), esc_url( $url ), $title, $title );

	echo '</p></div>';
}
