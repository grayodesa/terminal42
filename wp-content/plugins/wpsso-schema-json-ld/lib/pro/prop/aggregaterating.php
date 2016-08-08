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

if ( ! class_exists( 'WpssoJsonProPropAggregateRating' ) ) {

	class WpssoJsonProPropAggregateRating {

		protected $p;

		public function __construct( &$plugin ) {
			$this->p =& $plugin;
			if ( $this->p->debug->enabled )
				$this->p->debug->mark();

			$this->p->util->add_plugin_filters( $this, array(
				'json_data_http_schema_org' => 5,	// $json_data, $use_post, $mod, $mt_og, $user_id
			) );
		}

		// automatically include an aggregateRating property based on the Open Graph rating meta tags
		public function filter_json_data_http_schema_org( $json_data, $use_post, $mod, $mt_og, $user_id ) {

			if ( $this->p->debug->enabled )
				$this->p->debug->mark();

			$lca = $this->p->cf['lca'];
			$og_type = $mt_og['og:type'];
			$rating = array(
				'ratingValue' => null,
				'ratingCount' => null,
				'worstRating' => 1,
				'bestRating' => 5,
				'reviewCount' => null,
			);
			$ret = array();

			WpssoSchema::add_data_itemprop_from_assoc( $rating, $mt_og, array(
				'ratingValue' => $og_type.':rating:average',
				'ratingCount' => $og_type.':rating:count',
				'worstRating' => $og_type.':rating:worst',
				'bestRating' => $og_type.':rating:best',
				'reviewCount' => $og_type.':review:count',
			) );

			$rating = (array) apply_filters( $lca.'_json_prop_http_schema_org_aggregaterating', $rating, $mod );

			// remove all empty values
			foreach ( $rating as $key => $val )
				if ( empty( $val ) )
					unset( $rating[$key] );

			// check for at least two essential meta tags
			if ( isset( $rating['ratingValue'] ) &&
				( isset( $rating['ratingCount'] ) || 
					isset( $rating['reviewCount'] ) ) ) {
				$ret['aggregateRating'] = WpssoSchema::get_item_type_context( 'http://schema.org/AggregateRating', $rating );
			}

			return WpssoSchema::return_data_from_filter( $json_data, $ret );
		}
	}
}

?>
