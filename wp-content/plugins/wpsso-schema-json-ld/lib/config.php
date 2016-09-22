<?php
/*
 * License: GPLv3
 * License URI: http://www.gnu.org/licenses/gpl.txt
 * Copyright 2014-2016 Jean-Sebastien Morisset (http://surniaulula.com/)
 */

if ( ! defined( 'ABSPATH' ) ) 
	die( 'These aren\'t the droids you\'re looking for...' );

if ( ! class_exists( 'WpssoJsonConfig' ) ) {

	class WpssoJsonConfig {

		public static $cf = array(
			'plugin' => array(
				'wpssojson' => array(
					'version' => '1.10.3-1',		// plugin version
					'opt_version' => '8',		// increment when changing default options
					'short' => 'WPSSO JSON',	// short plugin name
					'name' => 'WPSSO Schema JSON-LD Markup (WPSSO JSON)',
					'desc' => 'WPSSO extension to add complete Schema JSON-LD markup (Article, Event, Organization, Place / Business, Product, Recipe, and more).',
					'slug' => 'wpsso-schema-json-ld',
					'base' => 'wpsso-schema-json-ld/wpsso-schema-json-ld.php',
					'update_auth' => 'tid',
					'text_domain' => 'wpsso-schema-json-ld',
					'domain_path' => '/languages',
					'img' => array(
						'icon_small' => 'images/icon-128x128.png',
						'icon_medium' => 'images/icon-256x256.png',
					),
					'url' => array(
						// wordpress
						'download' => 'https://wordpress.org/plugins/wpsso-schema-json-ld/',
						'review' => 'https://wordpress.org/support/view/plugin-reviews/wpsso-schema-json-ld?filter=5&rate=5#postform',
						'readme' => 'https://plugins.svn.wordpress.org/wpsso-schema-json-ld/trunk/readme.txt',
						'wp_support' => 'https://wordpress.org/support/plugin/wpsso-schema-json-ld',
						// surniaulula
						'update' => 'http://wpsso.com/extend/plugins/wpsso-schema-json-ld/update/',
						'purchase' => 'http://wpsso.com/extend/plugins/wpsso-schema-json-ld/',
						'changelog' => 'http://wpsso.com/extend/plugins/wpsso-schema-json-ld/changelog/',
						'codex' => 'http://wpsso.com/codex/plugins/wpsso-schema-json-ld/',
						'faq' => 'http://wpsso.com/codex/plugins/wpsso-schema-json-ld/faq/',
						'notes' => '',
						'feed' => 'http://wpsso.com/category/application/wordpress/wp-plugins/wpsso-schema-json-ld/feed/',
						'pro_support' => 'http://wpsso-schema-json-ld.support.wpsso.com/',
					),
					'lib' => array(
						// submenu items must have unique keys
						'submenu' => array (
							'schema-json-ld' => 'Schema Markup',
						),
						'gpl' => array(
							'admin' => array(
								'post' => 'Post Settings',
							),
							'head' => array(
								'webpage' => '(code) Schema Type WebPage (webpage)',
								'webpage#blogposting:no_load' => '(code) Schema Type Blog Posting (blog.posting)',
							),
						),
						'pro' => array(
							'admin' => array(
								'post' => 'Post Settings',
							),
							'head' => array(
								'article' => '(code) Schema Type Article (article)',
								'creativework' => '(code) Schema Type Creative Work (creative.work)',
								'event' => '(code) Schema Type Event (event)',
								'foodestablishment' => '(code) Schema Type Food Establishment (food.establishment)',
								'localbusiness' => '(code) Schema Type Local Business (local.business)',
								'organization' => '(code) Schema Type Organization (organization)',
								'person' => '(code) Schema Type Person (person)',
								'place' => '(code) Schema Type Place (place)',
								'product' => '(code) Schema Type Product (product)',
								'recipe' => '(code) Schema Type Recipe (recipe)',
								'review' => '(code) Schema Type Review (review)',
								'website' => '(code) Schema Type Website (website)',
							),
							'prop' => array(
								'aggregaterating' => '(code) Property Aggregate Rating',
							),
						),
					),
				),
			),
			'schema' => array(
				'article' => array(
					'headline' => array(
						'max_len' => 110,
					),
				),
			),
		);

		public static function get_version() { 
			return self::$cf['plugin']['wpssojson']['version'];
		}

		public static function set_constants( $plugin_filepath ) { 
			define( 'WPSSOJSON_FILEPATH', $plugin_filepath );						
			define( 'WPSSOJSON_PLUGINDIR', trailingslashit( realpath( dirname( $plugin_filepath ) ) ) );
			define( 'WPSSOJSON_PLUGINSLUG', self::$cf['plugin']['wpssojson']['slug'] );		// wpsso-sp
			define( 'WPSSOJSON_PLUGINBASE', self::$cf['plugin']['wpssojson']['base'] );		// wpsso-sp/wpsso-sp.php
			define( 'WPSSOJSON_URLPATH', trailingslashit( plugins_url( '', $plugin_filepath ) ) );
		}

		public static function require_libs( $plugin_filepath ) {

			require_once( WPSSOJSON_PLUGINDIR.'lib/register.php' );
			require_once( WPSSOJSON_PLUGINDIR.'lib/filters.php' );
			require_once( WPSSOJSON_PLUGINDIR.'lib/schema.php' );

			add_filter( 'wpssojson_load_lib', array( 'WpssoJsonConfig', 'load_lib' ), 10, 3 );
		}

		public static function load_lib( $ret = false, $filespec = '', $classname = '' ) {
			if ( $ret === false && ! empty( $filespec ) ) {
				$filepath = WPSSOJSON_PLUGINDIR.'lib/'.$filespec.'.php';
				if ( file_exists( $filepath ) ) {
					require_once( $filepath );
					if ( empty( $classname ) )
						return SucomUtil::sanitize_classname( 'wpssojson'.$filespec, false );	// $underscore = false
					else return $classname;
				}
			}
			return $ret;
		}
	}
}

?>
