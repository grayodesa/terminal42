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

if ( ! class_exists( 'WpssoJsonProHeadEvent' ) ) {

	class WpssoJsonProHeadEvent {

		protected $p;

		public function __construct( &$plugin ) {
			$this->p =& $plugin;
			if ( $this->p->debug->enabled )
				$this->p->debug->mark();

			$this->p->util->add_plugin_filters( $this, array(
				'json_data_http_schema_org_event' => 5,	// $json_data, $use_post, $mod, $mt_og, $user_id
			) );
		}

		public function filter_json_data_http_schema_org_event( $json_data, $use_post, $mod, $mt_og, $user_id ) {

			if ( $this->p->debug->enabled )
				$this->p->debug->mark();

			$ret = array();

			WpssoSchema::add_single_event_data( $ret, $mod, false, false );	// $event_id = false, $list_element = false

			if ( ! empty( $mod['obj'] ) ) {	// just in case
				if ( ! empty( $this->p->is_avail['org'] ) ) {	// check for custom organization id
					foreach ( array( 
						'organizer' => 'schema_event_org_id',
						'performer' => 'schema_event_perf_id',
					) as $itemprop => $key ) {
						$org_id = $mod['obj']->get_options( $mod['id'], $key );
						if ( is_numeric( $org_id ) )
							WpssoSchema::add_single_organization_data( $ret[$itemprop],
								$mod, $org_id, 'org_logo_url', false );	// $list_element = false
						if ( empty( $ret[$itemprop] ) )
							unset( $ret[$itemprop] );
					}
				}
			}

			/*
			 * Property:
			 *	inLanguage
			 */
			$ret['inLanguage'] = get_locale();

			/*
			 * Property:
			 *	image as http://schema.org/ImageObject
			 *	video as http://schema.org/VideoObject
			 */
			WpssoJsonSchema::add_media_data( $ret, $use_post, $mod, $mt_og, $user_id );

			return WpssoSchema::return_data_from_filter( $json_data, $ret );
		}
	}
}

?>
