<?php
/*
 * License: GPLv3
 * License URI: http://www.gnu.org/licenses/gpl.txt
 * Copyright 2014-2016 Jean-Sebastien Morisset (http://surniaulula.com/)
 */

if ( ! defined( 'ABSPATH' ) ) 
	die( 'These aren\'t the droids you\'re looking for...' );

if ( ! class_exists( 'WpssoPlmConfig' ) ) {

	class WpssoPlmConfig {

		public static $cf = array(
			'plugin' => array(
				'wpssoplm' => array(
					'version' => '2.1.0-1',		// plugin version
					'opt_version' => '11',		// increment when changing default options
					'short' => 'WPSSO PLM',		// short plugin name
					'name' => 'WPSSO Place / Location and Local Business Meta (WPSSO PLM)',
					'desc' => 'WPSSO extension to provide Pinterest Place, Facebook / Open Graph Location, Schema Local Business + Local SEO meta tags.',
					'slug' => 'wpsso-plm',
					'base' => 'wpsso-plm/wpsso-plm.php',
					'update_auth' => 'tid',
					'text_domain' => 'wpsso-plm',
					'domain_path' => '/languages',
					'img' => array(
						'icon_small' => 'images/icon-128x128.png',
						'icon_medium' => 'images/icon-256x256.png',
					),
					'url' => array(
						// wordpress
						'download' => 'https://wordpress.org/plugins/wpsso-plm/',
						'review' => 'https://wordpress.org/support/view/plugin-reviews/wpsso-plm?filter=5&rate=5#postform',
						'readme' => 'https://plugins.svn.wordpress.org/wpsso-plm/trunk/readme.txt',
						'wp_support' => 'https://wordpress.org/support/plugin/wpsso-plm',
						// surniaulula
						'update' => 'http://wpsso.com/extend/plugins/wpsso-plm/update/',
						'purchase' => 'http://wpsso.com/extend/plugins/wpsso-plm/',
						'changelog' => 'http://wpsso.com/extend/plugins/wpsso-plm/changelog/',
						'codex' => 'http://wpsso.com/codex/plugins/wpsso-plm/',
						'faq' => 'http://wpsso.com/codex/plugins/wpsso-plm/faq/',
						'notes' => '',
						'feed' => 'http://wpsso.com/category/application/wordpress/wp-plugins/wpsso-plm/feed/',
						'pro_support' => 'http://wpsso-plm.support.wpsso.com/',
					),
					'lib' => array(
						// submenu items must have unique keys
						'submenu' => array (
							'plm-general' => 'Place / Location',	// general settings
						),
						'gpl' => array(
							'admin' => array(
								'post' => 'Post Settings',
							),
						),
						'pro' => array(
							'admin' => array(
								'post' => 'Post Settings',
							),
						),
					),
				),
			),
			'form' => array(
				'plm_addr_select' => array(
					'none' => '[None]',
					'custom' => '[Custom Address]',
					'new' => '[New Address]',
				),
				'plm_addr_type' => array(
					'geo' => 'Geographic',
					'postal' => 'Postal Address',
				),
				'plm_addr_opts' => array(
					'plm_addr_name' => '',				// Name
					'plm_addr_streetaddr' => '',			// Street Address
					'plm_addr_po_box_number' => '',			// P.O. Box Number
					'plm_addr_city' => '',				// City
					'plm_addr_state' => '',				// State / Province
					'plm_addr_zipcode' => '',			// Zip / Postal Code
					'plm_addr_country' => '',			// Country
					'plm_addr_latitude' => '',			// Latitude
					'plm_addr_longitude' => '',			// Longitude
					'plm_addr_altitude' => '',			// Altitude
					'plm_addr_business_type' => 'local.business',
					'plm_addr_day_sunday' => 0,
					'plm_addr_day_sunday_open' => '09:00',
					'plm_addr_day_sunday_close' => '17:00',
					'plm_addr_day_monday' => 0,
					'plm_addr_day_monday_open' => '09:00',
					'plm_addr_day_monday_close' => '17:00',
					'plm_addr_day_tuesday' => 0,
					'plm_addr_day_tuesday_open' => '09:00',
					'plm_addr_day_tuesday_close' => '17:00',
					'plm_addr_day_wednesday' => 0,
					'plm_addr_day_wednesday_open' => '09:00',
					'plm_addr_day_wednesday_close' => '17:00',
					'plm_addr_day_thursday' => 0,
					'plm_addr_day_thursday_open' => '09:00',
					'plm_addr_day_thursday_close' => '17:00',
					'plm_addr_day_friday' => 0,
					'plm_addr_day_friday_open' => '09:00',
					'plm_addr_day_friday_close' => '17:00',
					'plm_addr_day_saturday' => 0,
					'plm_addr_day_saturday_open' => '09:00',
					'plm_addr_day_saturday_close' => '17:00',
					'plm_addr_day_publicholidays' => 0,
					'plm_addr_day_publicholidays_open' => '09:00',
					'plm_addr_day_publicholidays_close' => '17:00',
					'plm_addr_service_radius' => '',
					'plm_addr_accept_res' => '',
					'plm_addr_menu_url' => '',
				),
			),
		);

		public static function get_version() { 
			return self::$cf['plugin']['wpssoplm']['version'];
		}

		public static function set_constants( $plugin_filepath ) { 
			define( 'WPSSOPLM_FILEPATH', $plugin_filepath );						
			define( 'WPSSOPLM_PLUGINDIR', trailingslashit( realpath( dirname( $plugin_filepath ) ) ) );
			define( 'WPSSOPLM_PLUGINSLUG', self::$cf['plugin']['wpssoplm']['slug'] );	// wpsso-plm
			define( 'WPSSOPLM_PLUGINBASE', self::$cf['plugin']['wpssoplm']['base'] );	// wpsso-plm/wpsso-plm.php
			define( 'WPSSOPLM_URLPATH', trailingslashit( plugins_url( '', $plugin_filepath ) ) );
		}

		public static function require_libs( $plugin_filepath ) {

			require_once( WPSSOPLM_PLUGINDIR.'lib/register.php' );
			require_once( WPSSOPLM_PLUGINDIR.'lib/filters.php' );
			require_once( WPSSOPLM_PLUGINDIR.'lib/address.php' );

			add_filter( 'wpssoplm_load_lib', array( 'WpssoPlmConfig', 'load_lib' ), 10, 3 );
		}

		public static function load_lib( $ret = false, $filespec = '', $classname = '' ) {
			if ( $ret === false && ! empty( $filespec ) ) {
				$filepath = WPSSOPLM_PLUGINDIR.'lib/'.$filespec.'.php';
				if ( file_exists( $filepath ) ) {
					require_once( $filepath );
					if ( empty( $classname ) )
						return SucomUtil::sanitize_classname( 'wpssoplm'.$filespec, false );	// $underscore = false
					else return $classname;
				}
			}
			return $ret;
		}
	}
}

?>
