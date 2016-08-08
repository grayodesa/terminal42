<?php
/*
Plugin Name: SumoMe
Plugin URI: http://sumome.com
Description: Free Tools to grow your email list from SumoMe.com
Version: 1.19
Author: SumoMe
Author URI: http://www.SumoMe.com
*/

define('SUMOME__PLUGIN_DIR', plugin_dir_path( __FILE__ ));
define('SUMOME__PLUGIN_FILE', __FILE__);

include 'classes/class_sumome.php';
$wp_plugin_sumome = new WP_Plugin_SumoMe();

register_activation_hook( __FILE__, array('WP_Plugin_SumoMe', 'activate_SumoMe_plugin'));
register_deactivation_hook(__FILE__, array('WP_Plugin_SumoMe', 'deactivate_SumoMe_plugin'));

function sumome_plugin_settings_link($links)
{
  $settings_link = '<a href="options-general.php?page=sumome">Settings</a>';
  array_unshift($links, $settings_link);
  return $links;
}

$plugin = plugin_basename(__FILE__);
add_filter('plugin_action_links_'.$plugin, 'sumome_plugin_settings_link');
