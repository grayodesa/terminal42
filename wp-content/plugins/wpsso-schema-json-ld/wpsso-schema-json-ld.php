<?php
/*
 * Plugin Name: WPSSO Schema JSON-LD Markup (WPSSO JSON) Pro
 * Plugin Slug: wpsso-schema-json-ld
 * Text Domain: wpsso-schema-json-ld
 * Domain Path: /languages
 * Plugin URI: http://surniaulula.com/extend/plugins/wpsso-schema-json-ld/
 * Author: JS Morisset
 * Author URI: http://surniaulula.com/
 * License: GPLv3
 * License URI: http://www.gnu.org/licenses/gpl.txt
 * Description: WPSSO extension to add complete Schema JSON-LD markup (BlogPosting, Article, Place, Product, etc.) for Google and Pinterest.
 * Requires At Least: 3.1
 * Tested Up To: 4.5.2
 * Version: 1.7.5-1
 * 
 * Version Numbers: {major}.{minor}.{bugfix}-{stage}{level}
 *
 *	{major}		Major code changes and/or significant feature changes.
 *	{minor}		New features added and/or improvements included.
 *	{bugfix}	Bugfixes and/or very minor improvements.
 *	{stage}{level}	dev# (development), rc# (release candidate), # (production release)
 * 
 * Copyright 2014-2016 Jean-Sebastien Morisset (http://surniaulula.com/)
 */

if ( ! defined( 'ABSPATH' ) ) 
	die( 'These aren\'t the droids you\'re looking for...' );

if ( ! class_exists( 'WpssoJson' ) ) {

	class WpssoJson {

		public $p;			// Wpsso
		public $reg;			// WpssoJsonRegister
		public $filters;		// WpssoJsonFilters

		private static $instance = null;
		private static $wpsso_short = 'WPSSO';
		private static $wpsso_name = 'WordPress Social Sharing Optimization (WPSSO)';
		private static $wpsso_min_version = '3.32.4-1';
		private static $wpsso_has_min_ver = true;

		public static function &get_instance() {
			if ( self::$instance === null )
				self::$instance = new self;
			return self::$instance;
		}

		public function __construct() {

			require_once ( dirname( __FILE__ ).'/lib/config.php' );
			WpssoJsonConfig::set_constants( __FILE__ );
			WpssoJsonConfig::require_libs( __FILE__ );
			$this->reg = new WpssoJsonRegister();		// activate, deactivate, uninstall hooks

			if ( is_admin() ) {
				load_plugin_textdomain( 'wpsso-schema-json-ld', false, 'wpsso-schema-json-ld/languages/' );
				add_action( 'admin_init', array( &$this, 'check_for_wpsso' ) );
			}

			add_filter( 'wpsso_get_config', array( &$this, 'wpsso_get_config' ), 20, 1 );
			add_action( 'wpsso_init_options', array( &$this, 'wpsso_init_options' ), 100 );
			add_action( 'wpsso_init_objects', array( &$this, 'wpsso_init_objects' ), 100 );
			add_action( 'wpsso_init_plugin', array( &$this, 'wpsso_init_plugin' ), 100 );
		}

		public function check_for_wpsso() {
			if ( ! class_exists( 'Wpsso' ) )
				add_action( 'all_admin_notices', array( &$this, 'wpsso_missing_notice' ) );
		}

		public static function wpsso_missing_notice( $deactivate = false ) {
			$info = WpssoJsonConfig::$cf['plugin']['wpssojson'];

			if ( $deactivate === true ) {
				require_once( ABSPATH.'wp-admin/includes/plugin.php' );
				deactivate_plugins( $info['base'] );

				wp_die( '<p>'.sprintf( __( 'The %1$s extension requires the %2$s plugin &mdash; please install and activate the %3$s plugin before trying to re-activate the %4$s extension.', 'wpsso-schema-json-ld' ), $info['name'], self::$wpsso_name, self::$wpsso_short, $info['short'] ).'</p>' );

			} else echo '<div class="error"><p>'.sprintf( __( 'The %1$s extension requires the %2$s plugin &mdash; please install and activate the %3$s plugin.', 'wpsso-schema-json-ld' ), $info['name'], self::$wpsso_name, self::$wpsso_short ).'</p></div>';
		}

		public function wpsso_get_config( $cf ) {
			if ( version_compare( $cf['plugin']['wpsso']['version'], self::$wpsso_min_version, '<' ) ) {
				self::$wpsso_has_min_ver = false;
				return $cf;
			}
			return SucomUtil::array_merge_recursive_distinct( $cf, WpssoJsonConfig::$cf );
		}

		public function wpsso_init_options() {
			if ( method_exists( 'Wpsso', 'get_instance' ) )
				$this->p =& Wpsso::get_instance();
			else $this->p =& $GLOBALS['wpsso'];

			if ( self::$wpsso_has_min_ver === false )
				return;		// stop here

			$this->p->is_avail['json'] = true;

			foreach ( array( 'gpl', 'pro' ) as $lib ) {
				foreach ( array( 'head', 'prop' ) as $sub ) {
					if ( ! isset( WpssoJsonConfig::$cf['plugin']['wpssojson']['lib'][$lib][$sub] ) ||
						! is_array( WpssoJsonConfig::$cf['plugin']['wpssojson']['lib'][$lib][$sub] ) )
							continue;
					foreach ( WpssoJsonConfig::$cf['plugin']['wpssojson']['lib'][$lib][$sub] as $id_key => $label ) {
						list( $id, $stub, $action ) = SucomUtil::get_lib_stub_action( $id_key );
						$this->p->is_avail[$sub][$id] = true;
					}
				}
			}
		}

		public function wpsso_init_objects() {
			if ( self::$wpsso_has_min_ver === false )
				return;		// stop here

			$this->filters = new WpssoJsonFilters( $this->p );
			$this->schema = new WpssoJsonSchema( $this->p );
		}

		public function wpsso_init_plugin() {
			if ( self::$wpsso_has_min_ver === false )
				return $this->warning_wpsso_version();
		}

		private function warning_wpsso_version() {
			$info = WpssoJsonConfig::$cf['plugin']['wpssojson'];
			$wpsso_version = $this->p->cf['plugin']['wpsso']['version'];

			if ( $this->p->debug->enabled )
				$this->p->debug->log( $info['name'].' requires '.self::$wpsso_short.' version '.
					self::$wpsso_min_version.' or newer ('.$wpsso_version.' installed)' );

			if ( is_admin() )
				$this->p->notice->err( sprintf( __( 'The %1$s extension version %2$s requires the use of %3$s version %4$s or newer (version %5$s is currently installed).', 'wpsso-schema-json-ld' ), $info['name'], $info['version'], self::$wpsso_short, self::$wpsso_min_version, $wpsso_version ), true );
		}
	}

        global $wpssojson;
	$wpssojson =& WpssoJson::get_instance();
}

?>
