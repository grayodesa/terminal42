<?php
/*
 * License: GPLv3
 * License URI: http://www.gnu.org/licenses/gpl.txt
 * Copyright 2014-2016 Jean-Sebastien Morisset (http://surniaulula.com/)
 */

if ( ! defined( 'ABSPATH' ) ) 
	die( 'These aren\'t the droids you\'re looking for...' );

if ( ! class_exists( 'WpssoPlmFilters' ) ) {

	class WpssoPlmFilters {

		protected $p;

		public static $cf = array(
			'opt' => array(				// options
				'defaults' => array(
					'plm_addr_id' => 0,	// Edit an Address
					'plm_add_to_post' => 0,
					'plm_add_to_page' => 1,
					'plm_add_to_attachment' => 0,
					'plm_addr_for_home' => 'none',
					'plm_addr_def_country' => 'none',	// alpha2 country code
				),
			),
		);

		public function __construct( &$plugin ) {
			$this->p =& $plugin;

			$this->p->util->add_plugin_filters( $this, array( 
				'get_defaults' => 1,			// option defaults
				'get_md_defaults' => 1,			// meta data defaults
				'get_post_options' => 1,		// meta data post options
				'og_prefix_ns' => 1,			// open graph namespace
				'og_seed' => 3,				// open graph meta tags
				'json_array_type_ids' => 2,		// $type_ids, $mod
				'schema_head_type' => 3,		// $type_id, $mod
				'schema_meta_itemprop' => 2,		// $mt_schema, $mod
				'schema_noscript_array' => 3,		// $ret, $mod, $mt_og
				'get_place_options' => 3,		// $opts, $mod, $place_id
			) );

			if ( is_admin() ) {
				$this->p->util->add_plugin_filters( $this, array( 
					'save_options' => 3,
					'option_type' => 2,
					'post_social_settings_tabs' => 2,	// $tabs, $mod
					'messages_tooltip_post' => 3,
					'messages_tooltip' => 2,
				) );
				$this->p->util->add_plugin_filters( $this, array( 
					'status_gpl_features' => 3,
					'status_pro_features' => 3,
				), 10, 'wpssoplm' );			// hook into our own filters
			}
		}

		public function filter_get_defaults( $def_opts ) {
			$def_opts = array_merge( $def_opts, self::$cf['opt']['defaults'] );
			$def_opts = $this->p->util->add_ptns_to_opts( $def_opts, 'pm_add_to' );
			return $def_opts;
		}

		public function filter_get_md_defaults( $def_opts ) {
			return array_merge( $def_opts, WpssoPlmConfig::$cf['form']['plm_addr_opts'],
				array(
					'plm_addr_id' => 'custom',						// Select an Address
					'plm_addr_country' => $this->p->options['plm_addr_def_country'],	// Country
				)
			);
		}

		public function filter_get_post_options( $opts ) {
			$opts_version = empty( $opts['plugin_wpssoplm_opt_version'] ) ?
				0 : $opts['plugin_wpssoplm_opt_version'];

			if ( $opts_version <= 8 ) {
				$opts = SucomUtil::rename_keys( $opts, array(
					'plm_streetaddr' => 'plm_addr_streetaddr',
					'plm_po_box_number' => 'plm_addr_po_box_number',
					'plm_city' => 'plm_addr_city',
					'plm_state' => 'plm_addr_state',
					'plm_zipcode' => 'plm_addr_zipcode',
					'plm_country' => 'plm_addr_country',
					'plm_latitude' => 'plm_addr_latitude',
					'plm_longitude' => 'plm_addr_longitude',
					'plm_altitude' => 'plm_addr_altitude',
				) );
			}
			return $opts;
		}

		public function filter_og_prefix_ns( $ns ) {
			$ns['place'] = 'http://ogp.me/ns/place#';
			return $ns;
		}

		public function filter_og_seed( $og, $use_post, $mod ) {
			if ( $this->p->debug->enabled )
				$this->p->debug->mark();

			if ( ! is_array( $mod ) )
				$mod = $this->p->util->get_page_mod( $use_post );	// get post/user/term id, module name, and module object reference

			if ( ( $addr_opts = WpssoPlmAddress::has_place( $mod ) ) === false )
				return $og;     // abort

			/*
			 * og:type
			 */
			$og['og:type'] = 'place';

			/*
			 * place:street_address
			 * place:po_box_number
			 * place:locality
			 * place:region
			 * place:postal_code
			 * place:country_name
			 */
			foreach ( WpssoPlmAddress::$place_mt as $key => $mt_name )
				$og[$mt_name] = isset( $addr_opts[$key] ) && 
					$addr_opts[$key] !== 'none' ?
						$addr_opts[$key] : '';

			/*
			 * og:latitude
			 * og:longitude
			 * og:altitude
			 * place:location:latitude
			 * place:location:longitude
			 * place:location:altitude
			 */
			if ( ! empty( $addr_opts['plm_addr_latitude'] ) && 
				! empty( $addr_opts['plm_addr_longitude'] ) ) {

				foreach( array( 'place:location', 'og' ) as $mt_prefix ) {
					$og[$mt_prefix.':latitude'] = $addr_opts['plm_addr_latitude'];
					$og[$mt_prefix.':longitude'] = $addr_opts['plm_addr_longitude'];
					if ( ! empty( $addr_opts['plm_altitude'] ) )
						$og[$mt_prefix.':altitude'] = $addr_opts['plm_addr_altitude'];
				}
			}

			/*
			 * Non-standard meta tags for internal use (input to JSON-LD extension)
			 */
			$addr_defs = WpssoPlmConfig::$cf['form']['plm_addr_opts'];
			foreach ( $this->p->cf['form']['weekdays'] as $day => $label ) {
				if ( ! empty( $addr_opts['plm_addr_day_'.$day] ) ) {
					foreach ( array( 'open', 'close' ) as $hour ) {
						$key = 'plm_addr_day_'.$day.'_'.$hour;
						$og['place:business:day:'.$day.':'.$hour] = isset( $addr_opts[$key] ) ?
							$addr_opts[$key] : $addr_defs[$key];
					}
				}
			}

			foreach ( array(
				'plm_addr_season_from_date' => 'place:business:season:from',
				'plm_addr_season_to_date' => 'place:business:season:to',
				'plm_addr_service_radius' => 'place:business:service_radius',
				'plm_addr_accept_res' => 'place:business:accepts_reservations',
				'plm_addr_menu_url' => 'place:business:menu_url',
			) as $key => $mt_name ) {
				if ( $key === 'plm_addr_accept_res' )
					$og[$mt_name] = empty( $addr_opts[$key] ) ? 'false' : 'true';
				else $og[$mt_name] = isset( $addr_opts[$key] ) ? $addr_opts[$key] : '';
			}

			return $og;
		}

		public function filter_json_array_type_ids( $type_ids, $mod ) {
			/*
			 * Array (
			 *	[local.business] => 1
			 *	[website] => 1
			 *	[organization] => 1
			 *	[person] => 1
			 * )
			 */
			if ( WpssoPlmAddress::has_place( $mod ) !== false ) {
				if ( ( $addr_opts = WpssoPlmAddress::has_days( $mod ) ) !== false ) {
					$business_type_id = empty( $addr_opts['plm_addr_business_type'] ) ?
						'local.business' : $addr_opts['plm_addr_business_type'];
					$type_ids[$business_type_id] = true;
				} else $type_ids['place'] = true;
			} elseif ( $this->p->debug->enabled )
				$this->p->debug->log( 'not a schema place: no place options found' );

			return $type_ids;
		}

		public function filter_schema_head_type( $type_id, $mod, $is_md_type ) {
			if ( $this->p->debug->enabled )
				$this->p->debug->mark();

			// return a default - don't override custom head types
			if ( empty( $is_md_type ) ) {
				if ( WpssoPlmAddress::has_place( $mod ) !== false ) {
					if ( ( $addr_opts = WpssoPlmAddress::has_days( $mod ) ) !== false ) {
						$type_id = empty( $addr_opts['plm_addr_business_type'] ) ?
							'local.business' : $addr_opts['plm_addr_business_type'];
					} else $type_id = 'place';
				} elseif ( $this->p->debug->enabled )
					$this->p->debug->log( 'not a schema place: no place options found' );
			}

			return $type_id;
		}

		public function filter_schema_meta_itemprop( $mt_schema, $mod ) {
			if ( $this->p->debug->enabled )
				$this->p->debug->mark();

			if ( ( $addr_opts = WpssoPlmAddress::has_place( $mod ) ) !== false ) {
				$mt_schema['address'] = $addr_opts['plm_addr_streetaddr'].
					( empty( $addr_opts['plm_addr_po_box_number'] ) ?
						'' : ' #'.$addr_opts['plm_addr_po_box_number'] ).', '.
					$addr_opts['plm_addr_city'].', '.
					$addr_opts['plm_addr_state'].', '.
					$addr_opts['plm_addr_zipcode'].', '.
					$addr_opts['plm_addr_country'];

				foreach ( array(
					'plm_addr_menu_url' => 'menu',
					'plm_addr_accept_res' => 'acceptsreservations',
				) as $key => $mt_name ) {
					if ( $key === 'plm_addr_accept_res' )
						$mt_schema[$mt_name] = empty( $addr_opts[$key] ) ? 'false' : 'true';
					else $mt_schema[$mt_name] = isset( $addr_opts[$key] ) ? $addr_opts[$key] : '';
				}
			}

			return $mt_schema;
		}

		public function filter_schema_noscript_array( $ret, $mod, $mt_og ) {
			/*
			 * Array (
			 *	[place:business:day:monday:open] => 09:00
			 *	[place:business:day:monday:close] => 17:00
			 *	[place:business:day:publicholidays:open] => 09:00
			 *	[place:business:day:publicholidays:close] => 17:00
			 *	[place:business:season:from] => 2016-04-01
			 *	[place:business:season:to] => 2016-05-01
			 * )
			 */
			$mt_business = SucomUtil::preg_grep_keys( '/^place:business:(day|season):/', $mt_og );
			if ( ! empty( $mt_business ) ) {
				foreach ( $this->p->cf['form']['weekdays'] as $day => $label ) {
					$mt_day = array();
					if ( ! empty( $mt_business['place:business:day:'.$day.':open'] ) &&
						! empty( $mt_business['place:business:day:'.$day.':open'] ) ) {

						$mt_day[] = array( array( '<noscript itemprop="openingHoursSpecification" '.
							'itemscope itemtype="https://schema.org/OpeningHoursSpecification">'."\n" ) );
						$mt_day[] = $this->p->head->get_single_mt( 'meta', 'itemprop',
							'openinghoursspecification.dayofweek', $day, '', $mod );

						foreach ( array(
							'place:business:day:'.$day.':open' => 'openinghoursspecification.opens',
							'place:business:day:'.$day.':close' => 'openinghoursspecification.closes',
							'place:business:season:from' => 'openinghoursspecification.validfrom',
							'place:business:season:to' => 'openinghoursspecification.validthrough',
						) as $mt_key => $prop_name )
							if ( isset( $mt_business[$mt_key] ) )
								$mt_day[] = $this->p->head->get_single_mt( 'meta', 'itemprop',
									$prop_name, $mt_business[$mt_key], '', $mod );

						$mt_day[] = array( array( '</noscript>'."\n" ) );
					}
					foreach ( $mt_day as $arr )
						foreach ( $arr as $el )
							$ret[] = $el;
				}
			}
			return $ret;
		}

		public function filter_get_place_options( $opts, $mod, $place_id ) {
			if ( $opts !== false )    // first come, first served
				return $opts;
			elseif ( $place_id === 'custom' || is_numeric( $place_id ) ) {	// just in case
				$addr_opts = WpssoPlmAddress::get_addr_id( $place_id, $mod );
				return SucomUtil::preg_grep_keys( '/^plm_addr_/', $addr_opts, false, 'place_' );	// rename plm_addr to place
			} else return $opts;
		}

		public function filter_save_options( $opts, $options_name, $network ) {

			$address_names = SucomUtil::get_multi_key_locale( 'plm_addr_name', $opts, false );	// $add_none = false
			list( $first_num, $last_num, $next_num ) = SucomUtil::get_first_last_next_nums( $address_names );

			foreach ( $address_names as $num => $name ) {
				$name = trim( $name );

				if ( ! empty( $opts['plm_addr_delete_'.$num] ) ||
					( $name === '' && $num === $last_num ) ) {	// remove the empty "New Address"

					if ( isset( $opts['plm_addr_id'] ) &&
						$opts['plm_addr_id'] === $num )
							unset( $opts['plm_addr_id'] );

					// remove address id, including all localized keys
					$opts = SucomUtil::preg_grep_keys( '/^plm_addr_.*_'.$num.'(#.*)?$/', $opts, true );	// $invert = true

				} elseif ( $name === '' )	// just in case
					$opts['plm_addr_name_'.$num] = sprintf( _x( 'Address #%d',
						'option value', 'wpsso-plm' ), $num );

				else $opts['plm_addr_name_'.$num] = $name;
			}

			return $opts;
		}

		public function filter_option_type( $type, $key ) {

			if ( ! empty( $type ) )
				return $type;
			elseif ( strpos( $key, 'plm_' ) !== 0 )
				return $type;

			switch ( $key ) {
				case 'plm_addr_for_home':
				case 'plm_addr_def_country':
				case 'plm_addr_id':		// 'none', 'custom', or numeric (including 0)
				case 'plm_addr_business_type':
				case ( preg_match( '/^plm_addr_(country|type)$/', $key ) ? true : false ):
					return 'not_blank';
					break;
				case ( preg_match( '/^plm_addr_(streetaddr|city|state|zipcode)$/', $key ) ? true : false ):
					return 'ok_blank';	// text strings that can be blank
					break;
				case ( preg_match( '/^plm_addr_(latitude|longitude|altitude|service_radius|po_box_number)$/', $key ) ? true : false ):
					return 'blank_num';	// must be numeric (blank or zero is ok)
					break;
				case ( preg_match( '/^plm_addr_day_[a-z]+_(open|close)$/', $key ) ? true : false ):
					return 'time';
					break;
				case ( preg_match( '/^plm_addr_season_(from|to)_date$/', $key ) ? true : false ):
					return 'date';
					break;
				case 'plm_addr_menu_url':
					return 'url';
					break;
				case 'plm_addr_accept_res':
				case ( preg_match( '/^plm_addr_day_[a-z]+$/', $key ) ? true : false ):
					return 'checkbox';
					break;
			}
			return $type;
		}

		public function filter_post_social_settings_tabs( $tabs, $mod ) {
			if ( empty( $this->p->options['plm_add_to_'.$mod['post_type']] ) )
				return $tabs;
			else return SucomUtil::after_key( $tabs, 'header', 'plm',
				_x( 'Place / Location', 'metabox tab', 'wpsso-plm' ) );
		}

		public function filter_messages_tooltip_post( $text, $idx, $atts ) {
			if ( strpos( $idx, 'tooltip-post-plm_' ) !== 0 )
				return $text;

			switch ( $idx ) {
				case 'tooltip-post-plm_addr_id':
					$text = __( 'Select an address or enter a customized address bellow.', 'wpsso-plm' );
					break;
			}
			return $text;
		}

		public function filter_messages_tooltip( $text, $idx ) {
			if ( strpos( $idx, 'tooltip-plm_' ) !== 0 )
				return $text;

			switch ( $idx ) {
				case 'tooltip-plm_addr_for_home':
					$text = __( 'Select an address to include as a Schema <em>Place</em> or <em>Local Business</em> in your non-static home page.', 'wpsso-plm' ).' '.sprintf( __( 'An address for a static home page can be selected in the %1$s metabox when editing the static page.', 'wpsso-plm' ), _x( 'Social Settings', 'metabox title', 'wpsso-plm' ) );
					break;
				case 'tooltip-plm_addr_def_country':
					$text = __( 'A default country to use when creating a new address.', 'wpsso-plm' );
					break;
				case 'tooltip-plm_addr_id':
					$text = __( 'Select an address to edit. The address and business information is used for Open Graph meta tags and Schema markup.', 'wpsso-plm' );
					break;
				case 'tooltip-plm_addr_name':
					$text = __( 'Enter a descriptive name for this address. The address name appears in drop-down fields and the Schema Place name property.', 'wpsso-plm' );
					break;
				case 'tooltip-plm_addr_streetaddr':
					$text = __( 'An optional Street Address used for Pinterest Rich Pin / Schema <em>Place</em> meta tags and related markup.', 'wpsso-plm' );
					break;
				case 'tooltip-plm_addr_po_box_number':
					$text = __( 'An optional Post Office Box Number for the Pinterest Rich Pin / Schema <em>Place</em> meta tags and related markup.', 'wpsso-plm' );
					break;
				case 'tooltip-plm_addr_city':
					$text = __( 'An optional City name for the Pinterest Rich Pin / Schema <em>Place</em> meta tags and related markup.', 'wpsso-plm' );
					break;
				case 'tooltip-plm_addr_state':
					$text = __( 'An optional State or Province name for the Pinterest Rich Pin / Schema <em>Place</em> meta tags and related markup.', 'wpsso-plm' );
					break;
				case 'tooltip-plm_addr_zipcode':
					$text = __( 'An optional Zip or Postal Code for the Pinterest Rich Pin / Schema <em>Place</em> meta tags and related markup.', 'wpsso-plm' );
					break;
				case 'tooltip-plm_addr_country':
					$text = __( 'An optional Country for the Pinterest Rich Pin / Schema <em>Place</em> meta tags and related markup.', 'wpsso-plm' );
					break;
				case 'tooltip-plm_addr_latitude':
					$text = __( 'The numeric <em>decimal degrees</em> latitude for the main content of this webpage.', 'wpsso-plm' ).' '.__( 'You may use a service like <a href="http://www.gps-coordinates.net/">Google Maps GPS Coordinates</a> (as an example), to find the approximate GPS coordinates of a street address.', 'wpsso-plm' );
					break;
				case 'tooltip-plm_addr_longitude':
					$text = __( 'The numeric <em>decimal degrees</em> longitude for the main content of this webpage.', 'wpsso-plm' ).' '.__( 'You may use a service like <a href="http://www.gps-coordinates.net/">Google Maps GPS Coordinates</a> (as an example), to find the approximate GPS coordinates of a street address.', 'wpsso-plm' );
					break;
				case 'tooltip-plm_addr_altitude':
					$text = __( 'An optional numeric altitude (in meters above sea level) for the main content of this webpage.', 'wpsso-plm' );
					break;
				case 'tooltip-plm_addr_business_type':
					$text = __( 'A more descriptive Schema type for this local business. You must select a food establishment (fast food restaurant, ice cream shop, restaurant, etc.) to include Schema markup for a food menu URL and/or reservation information.', 'wpsso-plm' );
					break;
				case 'tooltip-plm_addr_days':
					$text = __( 'Select the days and hours this business is open.', 'wpsso-plm' );
					break;
				case 'tooltip-plm_addr_season_dates':
					$text = __( 'This business is only open for part of the year, between these two dates.', 'wpsso-plm' );
					break;
				case 'tooltip-plm_addr_service_radius':
					$text = __( 'The geographic area where a service is provided.', 'wpsso-plm' );
					break;
				case 'tooltip-plm_addr_accept_res':
					$text = __( 'This food establishment accepts reservations.', 'wpsso-plm' );
					break;
				case 'tooltip-plm_addr_menu_url':
					$text = __( 'The menu URL for this food establishment (fast food restaurant, ice cream shop, restaurant, etc.)', 'wpsso-plm' );
					break;
				case 'tooltip-plm_add_to':
					$text = sprintf( __( 'A <em>%1$s</em> tab can be added to the %2$s metabox on Posts, Pages, and custom post types, allowing you to enter specific address information for that webpage (ie. GPS coordinates and/or street address).', 'wpsso-plm' ), _x( 'Place / Location', 'metabox tab', 'wpsso-plm' ), _x( 'Social Settings', 'metabox title', 'wpsso' ) );
					break;
			}
			return $text;
		}

		public function filter_status_gpl_features( $features, $lca, $info ) {
			$has_addr_for_home = $this->p->options['plm_addr_for_home'] === '' ||
				$this->p->options['plm_addr_for_home'] === 'none' ? false : true;	// can be 0
			$features['(code) Place / Location for Non-static Homepage'] = array( 'status' => $has_addr_for_home ? 'on' : 'off' );
			return $features;
		}

		public function filter_status_pro_features( $features, $lca, $info ) {
			$aop = $this->p->check->aop( $lca, true, $this->p->is_avail['aop'] );
			$features['(tool) Custom Place / Location and Local Business Meta'] = array( 
				'status' => $aop ? 'on' : 'off',
				'td_class' => $aop ? '' : 'blank',
			);
			return $features;
		}
	}
}

?>
