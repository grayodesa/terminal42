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

if ( ! class_exists( 'WpssoJsonProHeadArticle' ) ) {

	class WpssoJsonProHeadArticle {

		protected $p;

		public function __construct( &$plugin ) {
			$this->p =& $plugin;
			if ( $this->p->debug->enabled )
				$this->p->debug->mark();

			$this->p->util->add_plugin_filters( $this, array(
				'json_data_http_schema_org_article' => 5,	// $json_data, $use_post, $mod, $mt_og, $user_id
			) );
		}

		/*
		 * http://schema.org/Article
		 * http://schema.org/NewsArticle
		 * http://schema.org/TechArticle
		 * http://schema.org/ScholarlyArticle
		 * http://schema.org/Report
		 * http://schema.org/SocialMediaPosting
		 */
		public function filter_json_data_http_schema_org_article( $json_data, $use_post, $mod, $mt_og, $user_id ) {

			if ( $this->p->debug->enabled )
				$this->p->debug->mark();

			$ret = array();

			/*
			 * Property:
			 * 	headline
			 */
			if ( is_object( $mod['obj'] ) )	// just in case
				$ret['headline'] = $mod['obj']->get_options( $mod['id'], 'schema_headline' );
			if ( empty( $ret['headline'] ) ) {
				$headline_max_len = WpssoJsonConfig::$cf['schema']['article']['headline']['max_len'];
				$ret['headline'] = $this->p->webpage->get_title( $headline_max_len, '...', $mod );
			}

			/*
			 * Property:
			 *	publisher as http://schema.org/Organization
			 *
			 * Uses the 'org_banner_url' image instead of 'org_logo_url' for Google's Article markup.
			 * See https://developers.google.com/structured-data/rich-snippets/articles for more info.
			 *
			 * $org_id can be null, false, 'none', 'site', or number (including 0) -- null and false are the same as 'site'
			 */
			$org_id = is_object( $mod['obj'] ) ?
				$mod['obj']->get_options( $mod['id'], 'schema_pub_org_id' ) : 'site';

			if ( $this->p->debug->enabled )
				$this->p->debug->log( 'publisher id is '.
					( empty( $org_id ) ? 'empty' : $org_id ) );

			WpssoSchema::add_single_organization_data( $ret['publisher'], $mod, $org_id, 'org_banner_url', false );	// $list_element = false

			return WpssoSchema::return_data_from_filter( $json_data, $ret );
		}
	}
}

?>
