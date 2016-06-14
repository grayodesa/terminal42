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

if ( ! class_exists( 'WpssoJsonProPropRating' ) ) {

	class WpssoJsonProPropRating {

		protected $p;

		public function __construct( &$plugin ) {
			$this->p =& $plugin;
			if ( $this->p->debug->enabled )
				$this->p->debug->mark();

			$this->p->util->add_plugin_filters( $this, array(
				'json_data_http_schema_org' => 5,	// $json_data, $use_post, $mod, $mt_og, $user_id
			) );
		}

		public function filter_json_data_http_schema_org( $json_data, $use_post, $mod, $mt_og, $user_id ) {

			if ( $this->p->debug->enabled )
				$this->p->debug->mark();

			$lca = $this->p->cf['lca'];
			$og_type = $mt_og['og:type'];
			$ret = array();

			// check for at least two essential meta tags
			if ( ! empty( $mt_og[$og_type.':rating:average'] ) &&
				( ! empty( $mt_og[$og_type.':rating:count'] ) || 
					! empty( $mt_og[$og_type.':review:count'] ) ) ) {

				$ret['aggregateRating'] = array(
					'@context' => 'http://schema.org',
					'@type' => 'AggregateRating',
				);

				foreach ( array(
					'ratingvalue' => 'rating:average',
					'ratingcount' => 'rating:count',
					'worstrating' => 'rating:worst',
					'bestrating' => 'rating:best',
					'reviewcount' => 'review:count',
				) as $prop_name => $og_key )
					if ( isset( $mt_og[$og_type.':'.$og_key] ) )
						$ret['aggregateRating'][$prop_name] = $mt_og[$og_type.':'.$og_key];
			}

			return WpssoSchema::return_data_from_filter( $json_data, $ret );
		}
	}
}

?>
