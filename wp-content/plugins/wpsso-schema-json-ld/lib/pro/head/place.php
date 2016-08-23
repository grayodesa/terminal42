<?php
/*
 * IMPORTANT: READ THE LICENSE AGREEMENT CAREFULLY.
 *
 * BY INSTALLING, COPYING, RUNNING, OR OTHERWISE USING THE 
 * WPSSO SCHEMA JSON-LD (WPSSO JSON) PRO APPLICATION, YOU AGREE
 * TO BE BOUND BY THE TERMS OF ITS LICENSE AGREEMENT.
 * 
 * License: Nontransferable License for a WordPress Site Address URL
 * License URI: http://surniaulula.com/wp-content/plugins/wpsso-schema-json-ld/license/pro.txt
 *
 * IF YOU DO NOT AGREE TO THE TERMS OF ITS LICENSE AGREEMENT,
 * PLEASE DO NOT INSTALL, RUN, COPY, OR OTHERWISE USE THE
 * WORDPRESS SOCIAL SHARING OPTIMIZATION (WPSSO) PRO APPLICATION.
 * 
 * Copyright 2016 Jean-Sebastien Morisset (http://surniaulula.com/)
 */

if ( ! defined( 'ABSPATH' ) ) 
	die( 'These aren\'t the droids you\'re looking for...' );

if ( ! class_exists( 'WpssoJsonProHeadPlace' ) ) {

	class WpssoJsonProHeadPlace {

		protected $p;

		public function __construct( &$plugin ) {
			$this->p =& $plugin;
			if ( $this->p->debug->enabled )
				$this->p->debug->mark();

			$this->p->util->add_plugin_filters( $this, array(
				'json_data_http_schema_org_place' => 4,	// $json_data, $mod, $mt_og, $user_id
			) );
		}

		public function filter_json_data_http_schema_org_place( $json_data, $mod, $mt_og, $user_id ) {

			if ( $this->p->debug->enabled )
				$this->p->debug->mark();

			$ret = array();
			$address = array();
			$geo = array();

			/*
			 * Property:
			 *	image as http://schema.org/ImageObject
			 *	video as http://schema.org/VideoObject
			 */
			WpssoJsonSchema::add_media_data( $ret, $mod, $mt_og, $user_id );

			// save time and check for prefix in meta tags
			if ( ! preg_grep( '/^place:/', array_keys( $mt_og ) ) )
				return $json_data;

			/*
			 * Property:
			 *	address as http://schema.org/PostalAddress
			 *
			 * <meta property="place:street_address" content="1234 Some Road"/>
			 * <meta property="place:po_box_number" content=""/>
			 * <meta property="place:locality" content="In A City"/>
			 * <meta property="place:region" content="State Name"/>
			 * <meta property="place:postal_code" content="123456789"/>
			 * <meta property="place:country_name" content="USA"/>
			 */
			foreach ( array(
				'name' => 'name', 
				'streetAddress' => 'street_address', 
				'postOfficeBoxNumber' => 'po_box_number', 
				'addressLocality' => 'locality',
				'addressRegion' => 'region',
				'postalCode' => 'postal_code',
				'addressCountry' => 'country_name',
			) as $prop_name => $og_key ) {
				if ( isset( $mt_og['place:'.$og_key] ) )
					$address[$prop_name] = $mt_og['place:'.$og_key];
			}

			if ( ! empty( $address ) )
				$ret['address'] = WpssoSchema::get_item_type_context( 'http://schema.org/PostalAddress', $address );

			/*
			 * Property:
			 *	geo as http://schema.org/GeoCoordinates
			 *
			 * <meta property="place:location:altitude" content="2,200"/>
			 * <meta property="place:location:latitude" content="45"/>
			 * <meta property="place:location:longitude" content="-73"/>
			 * <meta property="og:altitude" content="2,200"/>
			 * <meta property="og:latitude" content="45"/>
			 * <meta property="og:longitude" content="-73"/>
			 */
			foreach ( array(
				'elevation' => 'altitude', 
				'latitude' => 'latitude',
				'longitude' => 'longitude',
			) as $prop_name => $og_key ) {
				if ( isset( $mt_og['place:location:'.$og_key] ) )	// prefer the place location meta tags
					$geo[$prop_name] = $mt_og['place:location:'.$og_key];
				elseif ( isset( $mt_og['og:'.$og_key] ) )
					$geo[$prop_name] = $mt_og['og:'.$og_key];
			}

			if ( ! empty( $geo ) )
				$ret['geo'] = WpssoSchema::get_item_type_context( 'http://schema.org/GeoCoordinates', $geo ); 

			return WpssoSchema::return_data_from_filter( $json_data, $ret );
		}
	}
}

?>
