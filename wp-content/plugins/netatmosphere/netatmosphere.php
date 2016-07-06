<?php defined( 'ABSPATH' ) or die( 'No script kiddies please!' ); 
/*
 *	Plugin Name: NetAtmoSphere
 *	Plugin URI: http://www.teni.at/
 *	Description: Fetch periodically and display weather data coming from www.netatmo.com
 *	Version: 2.0.16
 *	Author: Martin Teni
 *	Author URI: http://www.teni.at/
 *	License: GPL2
 */

if (!defined('NAS_PLUGIN_ROOT')) {
	define('NAS_PLUGIN_ROOT', dirname(__FILE__) . '/');
	define('NAS_PLUGIN_NAME', basename(dirname(__FILE__)));
	define('NAS_PLUGIN_VERSION', '2.0.16');
	define('NAS_DB_VERSION', '0.8');
    
    // related plugin: display fancy charts
    define('NAS_WPBI_PLUGIN_FILE', 'wp-business-intelligence-lite/wp-business-intelligence-lite.php');
}

require_once (NAS_PLUGIN_ROOT . 'lib/Netatmo-API/src/Netatmo/autoload.php');
require_once (NAS_PLUGIN_ROOT . 'autoloader.php');

/**
 * De-/Activation / Uninstall must be global function 
 */
function nas_plugin_activation() {
	NAS_Plugin::pluginActivation();
}
function nas_plugin_deactivation() {
	NAS_Plugin::pluginDeactivation();
}
function nas_plugin_uninstall() {
	NAS_Plugin::pluginUninstall();
}

/** 
 * Same for cron callbacks, must be global functions
 */
require_once( NAS_PLUGIN_ROOT . 'inc/class-nas-cron.php' );

// activation and deactivation hooks for this plugin (need to stay in this file, doesnt work in nas-plugin.php)
register_activation_hook   ( __FILE__, 'nas_plugin_activation');
register_deactivation_hook ( __FILE__, 'nas_plugin_deactivation');
// or better use this approach with uninstall.php?
// https://codex.wordpress.org/Function_Reference/register_uninstall_hook 
register_uninstall_hook    ( __FILE__, 'nas_plugin_uninstall');

// instantiate the class
NAS_Plugin::getInstance();

?>