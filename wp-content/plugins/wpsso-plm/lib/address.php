<?php
/*
 * License: GPLv3
 * License URI: http://www.gnu.org/licenses/gpl.txt
 * Copyright 2014-2016 Jean-Sebastien Morisset (http://surniaulula.com/)
 */

if ( ! defined( 'ABSPATH' ) ) 
	die( 'These aren\'t the droids you\'re looking for...' );

if ( ! class_exists( 'WpssoPlmAddress' ) ) {

	class WpssoPlmAddress {

		private $p;

		private static $md_opts = array();	// meta data cache

		public static $place_mt = array(
			'plm_addr_name' => 'place:name',
			'plm_addr_streetaddr' => 'place:street_address',
			'plm_addr_po_box_number' => 'place:po_box_number',
			'plm_addr_city' => 'place:locality',
			'plm_addr_state' => 'place:region',
			'plm_addr_zipcode' => 'place:postal_code',
			'plm_addr_country' => 'place:country_name',
		);

		public function __construct( &$plugin ) {
			$this->p =& $plugin;
			if ( $this->p->debug->enabled )
				$this->p->debug->mark();
		}

		public static function has_place( array &$mod ) {
			$wpsso =& Wpsso::get_instance();
			if ( $wpsso->debug->enabled )
				$wpsso->debug->mark();

			$addr_opts = false;
			if ( $mod['is_home_index'] ) {
				if ( isset( $wpsso->options['plm_addr_for_home'] ) &&
					is_numeric( $wpsso->options['plm_addr_for_home'] ) ) {
					if ( ( $addr_opts = self::get_addr_id( $wpsso->options['plm_addr_for_home'], $wpsso->options ) ) === false ) {
						if ( $wpsso->debug->enabled )
							$wpsso->debug->log( 'no place options for address id '.$wpsso->options['plm_addr_for_home'] );
					}
				}
			} elseif ( is_object( $mod['obj'] ) ) {
				if ( ( $addr_opts = self::has_md_place( $mod ) ) === false ) {
					if ( $wpsso->debug->enabled )
						$wpsso->debug->log( 'no place options from module object' );
				}
			} elseif ( $wpsso->debug->enabled )
				$wpsso->debug->log( 'not home index and no module object' );

			if ( $wpsso->debug->enabled ) {
				if ( $addr_opts === false )
					$wpsso->debug->log( 'no place options found' );
				else $wpsso->debug->log( count( $addr_opts ).' place options found' );
			}

			return $addr_opts;
		}

		public static function has_md_place( array &$mod ) {
			if ( ! is_object( $mod['obj'] ) )	// just in case
				return false;
			$md_opts = WpssoPlmAddress::get_md_options( $mod );
			if ( is_array( $md_opts  ) ) {
				foreach ( self::$place_mt as $key => $mt_name ) {
					if ( ! empty( $md_opts[$key] ) )
						return $md_opts;
				}
			}
			return false;
		}

		public static function has_days( array &$mod ) {
			$wpsso =& Wpsso::get_instance();
			if ( $wpsso->debug->enabled )
				$wpsso->debug->mark();

			$addr_opts = false;
			if ( $mod['is_home_index'] ) {
				if ( isset( $wpsso->options['plm_addr_for_home'] ) &&
					is_numeric( $wpsso->options['plm_addr_for_home'] ) ) {
					if ( ( $addr_opts = self::get_addr_id( $wpsso->options['plm_addr_for_home'], $wpsso->options ) ) === false ) {
						if ( $wpsso->debug->enabled )
							$wpsso->debug->log( 'no business days for address id '.$wpsso->options['plm_addr_for_home'] );
					} else {
						foreach ( $wpsso->cf['form']['weekdays'] as $day => $label ) {
							if ( ! empty( $addr_opts['plm_addr_day_'.$day] ) )
								return $addr_opts;
						}
						return false;
					}
				}
			} elseif ( is_object( $mod['obj'] ) ) {
				if ( ( $addr_opts = self::has_md_days( $mod ) ) === false ) {
					if ( $wpsso->debug->enabled )
						$wpsso->debug->log( 'no business days from module object' );
				}
			} elseif ( $wpsso->debug->enabled )
				$wpsso->debug->log( 'not home index and no module object' );

			return $addr_opts;
		}

		public static function has_md_days( array &$mod ) {
			if ( ! is_object( $mod['obj'] ) )	// just in case
				return false;
			$wpsso =& Wpsso::get_instance();
			if ( $wpsso->debug->enabled )
				$wpsso->debug->mark();
			$md_opts = self::get_md_options( $mod );
			if ( is_array( $md_opts  ) ) {
				foreach ( $wpsso->cf['form']['weekdays'] as $day => $label ) {
					if ( ! empty( $md_opts['plm_addr_day_'.$day] ) )
						return $md_opts;
				}
			}
			return false;
		}

		public static function has_geo( array &$mod ) {
			$wpsso =& Wpsso::get_instance();
			if ( $wpsso->debug->enabled )
				$wpsso->debug->mark();

			$addr_opts = false;
			if ( $mod['is_home_index'] ) {
				if ( isset( $wpsso->options['plm_addr_for_home'] ) &&
					is_numeric( $wpsso->options['plm_addr_for_home'] ) ) {
					if ( ( $addr_opts = self::get_addr_id( $wpsso->options['plm_addr_for_home'], $wpsso->options ) ) === false ) {
						if ( $wpsso->debug->enabled )
							$wpsso->debug->log( 'no geo coordinates for address id '.$wpsso->options['plm_addr_for_home'] );
					} else {
						if ( ! empty( $addr_opts['plm_addr_latitude'] ) && 
							! empty( $addr_opts['plm_addr_longitude'] ) )
								return $addr_opts;
						return false;
					}

				}
			} elseif ( is_object( $mod['obj'] ) ) {
				if ( ( $addr_opts = self::has_md_days( $mod ) ) === false ) {
					if ( $wpsso->debug->enabled )
						$wpsso->debug->log( 'no geo coordinates from module object' );
				}
			} elseif ( $wpsso->debug->enabled )
				$wpsso->debug->log( 'not home index and no module object' );

			return $addr_opts;
		}

		public static function has_md_geo( array &$mod ) {
			if ( ! is_object( $mod['obj'] ) )	// just in case
				return false;
			$md_opts = self::get_md_options( $mod );
			if ( is_array( $md_opts  ) ) {
				if ( ! empty( $md_opts['plm_addr_latitude'] ) && 
					! empty( $md_opts['plm_addr_longitude'] ) )
						return $md_opts;
			}
			return false;
		}

		public static function get_md_options( array &$mod ) {
			if ( ! is_object( $mod['obj'] ) )	// just in case
				return array();

			$wpsso =& Wpsso::get_instance();
			if ( $wpsso->debug->enabled )
				$wpsso->debug->mark();

			if ( ! isset( self::$md_opts[$mod['name']][$mod['id']] ) )	// make sure a cache entry exists
				self::$md_opts[$mod['name']][$mod['id']] = array();
			else return self::$md_opts[$mod['name']][$mod['id']];		// return the cache entry

			$md_opts =& self::$md_opts[$mod['name']][$mod['id']];		// shortcut variable

			$md_opts = $mod['obj']->get_options( $mod['id'] );
			if ( is_array( $md_opts  ) ) {
				if ( isset( $md_opts['plm_addr_id'] ) && 		// allow for 0
					is_numeric( $md_opts['plm_addr_id'] ) ) {
					if ( ( $addr_opts = self::get_addr_id( $md_opts['plm_addr_id'], $wpsso->options ) ) !== false ) {
						if ( $wpsso->debug->enabled )
							$wpsso->debug->log( 'using address ID '.$md_opts['plm_addr_id'].' options' );
						$md_opts = array_merge( $md_opts, $addr_opts );
					}
				}
				$md_opts = SucomUtil::preg_grep_keys( '/^plm_/', $md_opts );	// only return plm options
			}
			return $md_opts;
		}

		// get a specific address id
		public static function get_addr_id( $id, array $opts ) {
			$addr_opts = SucomUtil::preg_grep_keys( '/^(plm_addr_.*)_'.$id.'$/', $opts, false, '$1' );
			if ( empty( $addr_opts ) )
				return false; 
			// just in case - make sure we have a complete array
			else return array_merge( WpssoPlmConfig::$cf['form']['plm_addr_opts'], $addr_opts );
		}

		// options may be provided when saving post meta data
		public static function get_names( array &$opts, $add_none = false ) {
			$names = SucomUtil::preg_grep_keys( '/^plm_addr_name_([0-9]+)$/', $opts, false, '$1' );
			asort( $names );	// sort values to display in select box
			if ( $add_none )
				return array_merge( array( 'none' => '[None]' ), $names );
			else return $names;
		}

		public static function get_first_next_ids( array &$names ) {
			ksort( $names );		// sort keys to find highest / lowest key integer
			$sorted_keys = array_keys( $names );
			$first_id = (int) reset( $sorted_keys );
			$last_id = (int) end( $sorted_keys );
			$next_id = isset( $names[0] ) ? $last_id + 1 : 0;
			natsort( $names );		// sort values to display in select box
			return array( $first_id, $next_id );
		}
	}
}

?>
