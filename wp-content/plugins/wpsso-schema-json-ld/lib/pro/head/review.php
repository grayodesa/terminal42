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

if ( ! class_exists( 'WpssoJsonProHeadReview' ) ) {

	class WpssoJsonProHeadReview {

		protected $p;

		public function __construct( &$plugin ) {
			$this->p =& $plugin;
			if ( $this->p->debug->enabled )
				$this->p->debug->mark();

			$this->p->util->add_plugin_filters( $this, array(
				'json_data_http_schema_org_review' => 4,	// $json_data, $mod, $mt_og, $user_id
			) );
		}

		public function filter_json_data_http_schema_org_review( $json_data, $mod, $mt_og, $user_id ) {

			if ( $this->p->debug->enabled )
				$this->p->debug->mark();

			$ret = array();

			if ( is_object( $mod['obj'] ) ) {	// just in case
				$md_opts = SucomUtil::keys_start_with( 'schema_review_', array_merge( 
					(array) $mod['obj']->get_defaults( $mod['id'] ), 
					(array) $mod['obj']->get_options( $mod['id'] )
				) );
			} else $md_opts = array();

			/*
			 * Property:
			 * 	itemReviewed
			 */
			if ( ! empty( $md_opts['schema_review_item_type'] ) &&
				$md_opts['schema_review_item_type'] !== 'none' ) {

				$item_type_url = $this->p->schema->get_schema_type_url( $md_opts['schema_review_item_type'] );
				$ret['itemReviewed'] = WpssoSchema::get_item_type_context( $item_type_url );

				WpssoSchema::add_data_itemprop_from_assoc( $ret['itemReviewed'], $md_opts, array(
					'url' => 'schema_review_item_url',
				) );
			}

			/*
			 * Property:
			 * 	reviewRating
			 */
			if ( ! empty( $md_opts['schema_review_rating'] ) ) {

				$ret['reviewRating'] = WpssoSchema::get_item_type_context( 'http://schema.org/Rating',
					array( 'ratingValue' => $md_opts['schema_review_rating'] ) );

				WpssoSchema::add_data_itemprop_from_assoc( $ret['reviewRating'], $md_opts, array(
					'worstRating' => 'schema_review_rating_from',
					'bestRating' => 'schema_review_rating_to',
				) );

			}

			return WpssoSchema::return_data_from_filter( $json_data, $ret );
		}
	}
}

?>
