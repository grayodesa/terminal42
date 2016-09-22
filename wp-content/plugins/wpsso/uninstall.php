<?php
/*
 * License: GPLv3
 * License URI: http://www.gnu.org/licenses/gpl.txt
 * Copyright 2012-2016 Jean-Sebastien Morisset (http://surniaulula.com/)
 */

if ( ! defined( 'ABSPATH' ) || 
	! defined( 'WP_UNINSTALL_PLUGIN' ) )
		die( 'These aren\'t the droids you\'re looking for...' );

$plugin_filepath = dirname( __FILE__ ).'/wpsso.php';

require_once( dirname( __FILE__ ).'/lib/config.php' );

WpssoConfig::set_constants( $plugin_filepath );
WpssoConfig::require_libs( $plugin_filepath );	// includes the register.php class library

WpssoRegister::network_uninstall();

?>
