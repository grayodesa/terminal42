<?php

/**
 * @package Better Analytics
 */
/*
Plugin Name: Better Analytics
Plugin URI: https://marketplace.digitalpoint.com/better-analytics.3354/item
Description: Adds Google Universal Analytics code to your WordPress site.  Options to track most everything (social button interactions, advertising clicks, emails sent/opened, YouTube video engagement, custom dimension tracking of authors/categories, etc.)  Integrates with API for reports/charts on dashboard, heat maps and real-time traffic tracking.
Version: 1.1.4
Author: Digital Point
Author URI: https://www.digitalpoint.com/
License: GPLv2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Text Domain: better-analytics
Domain Path: /languages
*/


if (!function_exists('add_action'))
{
	echo 'What the what?';
	exit;
}

define('BETTER_ANALYTICS_VERSION', '1.1.4');
define('BETTER_ANALYTICS_MINIMUM_WP_VERSION', '3.8');  // Dashicons: https://codex.wordpress.org/Function_Reference/add_menu_page
define('BETTER_ANALYTICS_PRODUCT_URL', 'https://marketplace.digitalpoint.com/better-analytics.3354/item');
define('BETTER_ANALYTICS_PRO_PRODUCT_URL', 'https://marketplace.digitalpoint.com/better-analytics-pro.3355/item');
define('BETTER_ANALYTICS_SUPPORT_URL', 'https://forums.digitalpoint.com/forums/better-analytics.31/');

define('BETTER_ANALYTICS_PLUGIN_URL', plugin_dir_url(__FILE__));
define('BETTER_ANALYTICS_PLUGIN_DIR', plugin_dir_path(__FILE__));

load_plugin_textdomain('better-analytics', false, dirname(plugin_basename(__FILE__)) . '/languages');

require_once(BETTER_ANALYTICS_PLUGIN_DIR . '/library/DigitalPointBetterAnalytics/Base/Public.php');

$publicClass = 'DigitalPointBetterAnalytics_Base_Public';

spl_autoload_register(array($publicClass, 'autoload'));

// Need to add before activation hooks
add_filter( 'cron_schedules', array($publicClass, 'filter_cron_schedules' ));

register_activation_hook( __FILE__, array($publicClass, 'plugin_activation' ) );
register_deactivation_hook( __FILE__, array($publicClass, 'plugin_deactivation' ) );
register_uninstall_hook(__FILE__, array('DigitalPointBetterAnalytics_Install', 'uninstall'));

DigitalPointBetterAnalytics_Base_Public::getInstance();

add_action('widgets_init', array('DigitalPointBetterAnalytics_Widget_PopularPosts', 'register_widget'));
add_action('widgets_init', array('DigitalPointBetterAnalytics_Widget_Stats', 'register_widget'));

if (is_admin())
{
	DigitalPointBetterAnalytics_Base_Admin::getInstance();
}