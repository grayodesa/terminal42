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

if ( ! class_exists( 'WpssoJsonProHeadLocalBusiness' ) ) {

	class WpssoJsonProHeadLocalBusiness {

		protected $p;

		public function __construct( &$plugin ) {
			$this->p =& $plugin;
			if ( $this->p->debug->enabled )
				$this->p->debug->mark();

			$this->p->util->add_plugin_filters( $this, array(
				'json_data_http_schema_org_localbusiness' => 5,	// $json_data, $use_post, $mod, $mt_og, $user_id
			) );
		}

		public function filter_json_data_http_schema_org_localbusiness( $json_data, $use_post, $mod, $mt_og, $user_id ) {

			if ( $this->p->debug->enabled )
				$this->p->debug->mark();

			$lca = $this->p->cf['lca'];
			$ret = array();
			$opening_hours = array();

			// save time and check for prefix in meta tags
			if ( ! preg_grep( '/^place:business:day:/', array_keys( $mt_og ) ) )
				return $json_data;

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
			foreach ( $this->p->cf['form']['weekdays'] as $day => $label ) {
				if ( ! empty( $mt_og['place:business:day:'.$day.':open'] ) &&
					! empty( $mt_og['place:business:day:'.$day.':close'] ) ) {

					$dayofweek = array(
						'@context' => 'http://schema.org',
						'@type' => 'openingHoursSpecification',
						'dayOfWeek' => $label,
					);
					foreach ( array(
						'place:business:day:'.$day.':open' => 'opens',
						'place:business:day:'.$day.':close' => 'closes',
						'place:business:season:from' => 'validFrom',
						'place:business:season:to' => 'validThrough',
					) as $mt_key => $prop_name )
						if ( isset( $mt_og[$mt_key] ) )
							$dayofweek[$prop_name] = $mt_og[$mt_key];
					$opening_hours[] = $dayofweek;
				}
			}

			if ( ! empty( $opening_hours ) )
				$ret['openingHoursSpecification'] = $opening_hours;

			return WpssoSchema::return_data_from_filter( $json_data, $ret );
		}
	}
}

?>
