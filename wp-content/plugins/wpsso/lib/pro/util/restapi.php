<?php
/*
 * IMPORTANT: READ THE LICENSE AGREEMENT CAREFULLY.
 *
 * BY INSTALLING, COPYING, RUNNING, OR OTHERWISE USING THE 
 * WORDPRESS SOCIAL SHARING OPTIMIZATION (WPSSO) PRO APPLICATION, YOU AGREE 
 * TO BE BOUND BY THE TERMS OF ITS LICENSE AGREEMENT.
 * 
 * License: Nontransferable License for a WordPress Site Address URL
 * License URI: http://surniaulula.com/wp-content/plugins/wpsso/license/pro.txt
 *
 * IF YOU DO NOT AGREE TO THE TERMS OF ITS LICENSE AGREEMENT,
 * PLEASE DO NOT INSTALL, RUN, COPY, OR OTHERWISE USE THE
 * WORDPRESS SOCIAL SHARING OPTIMIZATION (WPSSO) PRO APPLICATION.
 * 
 * Copyright 2012-2016 Jean-Sebastien Morisset (http://surniaulula.com/)
 */

if ( ! defined( 'ABSPATH' ) ) 
	die( 'These aren\'t the droids you\'re looking for...' );

if ( ! class_exists( 'WpssoProUtilRestAPI' ) ) {

	class WpssoProUtilRestAPI {

		private $p;
		private $api_obj;
		private $has_filters = false;

		public function __construct( &$plugin ) {
			$this->p =& $plugin;
			add_action( 'rest_api_init', array( &$this, 'register_api' ) );
		}

		public function register_api() {
			$add_to = array( 'term', 'user' );

			foreach ( $this->p->util->get_post_types() as $post_type )
				$add_to[] = $post_type->name;

			register_api_field( $add_to, 'head', array(
				'get_callback' => array( &$this, 'get_head' ),
				'update_callback' => null,
				'schema' => null,
			) );
		}

		public function get_head( $api_obj, $field_name = 'head', WP_REST_Request $request ) {

			if ( ! defined( 'SUCOM_DOING_API' ) )
				define( 'SUCOM_DOING_API', true );

			$ret = array();
			$this->api_obj = $api_obj;		// save the object for filters
			$mod['id'] = $this->api_obj['id'];	// save the post/term/user ID
			$params = $request->get_url_params();

			if ( strpos( $params[0], '/term' ) ) {		// term object
				$mod['name'] = 'term';
				if ( $this->has_filters === false ) {
					add_filter( 'sucom_is_term_page', '__return_true' );
					add_filter( 'sucom_get_term_object', array( &$this, 'filter_get_term_object' ) );
					$this->has_filters = true;
				}
				$head = $this->p->head->get_header_array( false, $mod );

			} elseif ( strpos( $params[0], '/user' ) ) {	// user object
				$mod['name'] = 'user';
				if ( $this->has_filters === false ) {
					add_filter( 'sucom_is_user_page', '__return_true' );
					add_filter( 'sucom_get_user_object', array( &$this, 'filter_get_user_object' ) );
					$this->has_filters = true;
				}
				$head = $this->p->head->get_header_array( false, $mod );

			} else {					// post object
				$mod['name'] = 'post';
				$head = $this->p->head->get_header_array( $mod['id'], $mod );
			}

			// save existing html and parts array values
			foreach ( array( 'html', 'parts' ) as $sub )
				if ( isset( $this->api_obj['head'][$sub] ) &&
					is_array( $this->api_obj['head'][$sub] ) )
						$ret[$sub] = $this->api_obj['head'][$sub];

			foreach ( $head as $meta ) {
				if ( ! empty( $meta[0] ) )
					$ret['html'][] = $meta[0];

				unset( $meta[0] );			// remove the html, including json script blocks

				if ( ! empty( $meta ) )
					$ret['parts'][] = $meta;	// save the meta tag array, without the html element
			}

			return $ret;
		}

		public function filter_get_term_object( $term_obj ) {
			return get_term_by( 'term_taxonomy_id', $this->api_obj['id'], $this->api_obj['taxonomy'], OBJECT, 'raw' );
		}

		public function filter_get_user_object( $user_obj ) {
			return get_userdata( $this->api_obj['id'] );
		}
	}
}

?>
