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

if ( ! class_exists( 'WpssoProSeoWpseo' ) ) {

	class WpssoProSeoWpseo {

		private $p;
		private $opts;

		public function __construct( &$plugin ) {
			$this->p =& $plugin;

			$this->p->util->add_plugin_filters( $this, array( 
				'title_seed' => 2, 
				'description_seed' => 2, 
				'post_url' => 2,
				'term_url' => 2, 
				'sharing_url' => 1,
			), WPSSO_SEO_FILTERS_PRIORITY );

			if ( class_exists( 'WPSEO_Options' ) && 
				method_exists( 'WPSEO_Options', 'get_all' ) )
					$this->opts = WPSEO_Options::get_all();
			elseif ( function_exists( 'get_wpseo_options' ) )
				$this->opts = get_wpseo_options();
			elseif ( $this->p->debug->enabled )
				$this->p->debug->log( 'WPSEO_Options::get_all() and get_wpseo_options() not found' );
		}

		public function filter_title_seed( $title, $mod ) {

			// skip if we're not using filtered / SEO titles
			if ( empty( $this->p->options['plugin_filter_title'] ) )
				return $title;

			// wpseo_front object does not exist on the admin side, so duplicate it's behavior
			if ( is_admin() ) {
				if ( $mod['is_post'] )
					$title = $this->get_post_meta_value( $mod['id'], 'title' );
				elseif ( $mod['is_term'] )
					$title = $this->get_term_meta_value( 'title' );
				elseif ( $mod['is_user'] )
					$title = $this->get_user_meta_value( 'title' );
			}

			return $title;
		}

		public function filter_description_seed( $desc, $mod ) {

			// wpseo_front object does not exist on the admin side, so duplicate it's behavior
			if ( is_admin() ) {
				if ( $mod['is_post'] )
					$desc = $this->get_post_meta_value( $mod['id'], 'metadesc' );
				elseif ( $mod['is_term'] )
					$desc = $this->get_term_meta_value( 'desc' );
				elseif ( $mod['is_user'] )
					$desc = $this->get_user_meta_value( 'metadesc' );
			} else {
				if ( class_exists( 'WPSEO_Frontend' ) && 
					method_exists( 'WPSEO_Frontend', 'get_instance' ) )
						$wpseo_front = WPSEO_Frontend::get_instance();
				else global $wpseo_front;

				if ( method_exists( $wpseo_front, 'metadesc' ) )
					$desc = $wpseo_front->metadesc( false );
				elseif ( $this->p->debug->enabled )
					$this->p->debug->log( 'WPSEO_Frontend::metadesc() not found' );
			}

			return $desc;
		}

		public function filter_post_url( $url, $mod ) {
			$canonical = '';
			if ( ! empty( $mod['id'] ) ) {
				if ( class_exists( 'WPSEO_Meta' ) && 
					method_exists( 'WPSEO_Meta', 'get_value' ) )
						$canonical = WPSEO_Meta::get_value( 'canonical', $mod['id'] );
				elseif ( function_exists( 'wpseo_get_value' ) )
					$canonical = wpseo_get_value( 'canonical', $mod['id'] );
				elseif ( $this->p->debug->enabled )
					$this->p->debug->log( 'WPSEO_Meta::get_value() and wpseo_get_value() not found' );
			}
			if ( empty( $canonical ) ) 
				return $url;
			else return $canonical;
		}

		public function filter_term_url( $url, $mod ) {
			if ( ! empty( $mod['id'] ) ) {
				if ( class_exists( 'WPSEO_Taxonomy_Meta' ) && 
					method_exists( 'WPSEO_Taxonomy_Meta', 'get_term_meta' ) )
						$canonical = WPSEO_Taxonomy_Meta::get_term_meta( $mod['id'], $mod['tax_slug'], 'canonical' );
				elseif ( function_exists( 'wpseo_get_term_meta' ) )
					$canonical = wpseo_get_term_meta( $mod['id'], $mod['tax_slug'], 'canonical' );
				elseif ( $this->p->debug->enabled )
					$this->p->debug->log( 'WPSEO_Taxonomy_Meta::get_term_meta() and wpseo_get_term_meta() not found' );
			}
			if ( empty( $canonical ) ) 
				return $url;
			else return $canonical;
		}

		// force the transport (if applicable) on sharing urls as well
		public function filter_sharing_url( $url ) {
			if ( ! empty( $url ) &&
				isset( $this->opts['force_transport'] ) && 
					$this->opts['force_transport'] !== 'default' ) {
				$url = preg_replace( '/^http[s]?/', $this->opts['force_transport'], $url );
				if ( $this->p->debug->enabled )
					$this->p->debug->log( $this->opts['force_transport'].' transport is forced' );
			}
			return $url;
		}

		private function get_post_meta_value( $post_id, $meta_key ) {

			$value = '';
			$post_obj = SucomUtil::get_post_object( $post_id );

			if ( empty( $post_obj ) ) {
				if ( $this->p->debug->enabled )
					$this->p->debug->log( 'exiting early: post object is empty' );
				return $value;
			}

			if ( class_exists( 'WPSEO_Meta' ) && 
				method_exists( 'WPSEO_Meta', 'get_value' ) )
					$value = WPSEO_Meta::get_value( $meta_key, $post_obj->ID );
			elseif ( function_exists( 'wpseo_get_value' ) )
				$value = wpseo_get_value( $meta_key, $post_obj->ID );
			elseif ( $this->p->debug->enabled )
				$this->p->debug->log( 'WPSEO_Meta::get_value() and wpseo_get_value() not found' );

			if ( ! empty( $value ) ) {
				if ( $this->p->debug->enabled )
					$this->p->debug->log( 'wpseo get_value: '.$value );
				return wpseo_replace_vars( $value, $post_obj );
			} else {
				$post_type = isset( $post_obj->post_type ) ?
					$post_obj->post_type : 
					$post_obj->query_var;

				// title
				if ( strpos( $meta_key, 'title' ) === 0 ) {
					if ( class_exists( 'WPSEO_Frontend' ) && 
						method_exists( 'WPSEO_Frontend', 'get_instance' ) )
							$wpseo_front = WPSEO_Frontend::get_instance();
					else global $wpseo_front;

					if ( method_exists( $wpseo_front, 'get_title_from_options' ) )
						$value = $wpseo_front->get_title_from_options( $meta_key.'-'.$post_type, $post_obj );

				// description
				} elseif ( ! empty( $this->opts[$meta_key.'-'.$post_type] ) )
					$value = wpseo_replace_vars( $this->opts[$meta_key.'-'.$post_type], $post_obj );

				$value = apply_filters( 'wpseo_'.$meta_key, trim( $value ) );
			}

			return $value;
		}

		private function get_term_meta_value( $meta_key ) {
			$value = '';
			$term_obj = SucomUtil::get_term_object();

			if ( ! empty( $term_obj->term_id ) ) {

				if ( class_exists( 'WPSEO_Taxonomy_Meta' ) && 
					method_exists( 'WPSEO_Taxonomy_Meta', 'get_term_meta' ) )
						$value = WPSEO_Taxonomy_Meta::get_term_meta( $term_obj, $term_obj->taxonomy, $meta_key );
				elseif ( $this->p->debug->enabled )
					$this->p->debug->log( 'WPSEO_Taxonomy_Meta::get_term_meta() not found' );

				if ( ! empty( $value ) ) {
					if ( $this->p->debug->enabled )
						$this->p->debug->log( 'wpseo get_value: '.$value );
					return wpseo_replace_vars( $value, $term_obj );

				} elseif ( ! empty( $term_obj->taxonomy ) ) {
					if ( strpos( $meta_key, 'title' ) === 0 )
						$opt_key = $meta_key.'-tax-'.$term_obj->taxonomy;
					else $opt_key = 'meta'.$meta_key.'-tax-'.$term_obj->taxonomy;

					if ( ! empty( $this->opts[$opt_key] ) )
						$value = wpseo_replace_vars( $this->opts[$opt_key], $term_obj );
				}
				$value = apply_filters( 'wpseo_'.$meta_key, trim( $value ) );

			} elseif ( $this->p->debug->enabled )
				$this->p->debug->log( 'term id is empty' );

			return $value;
		}

		private function get_user_meta_value( $meta_key ) {
			$value = '';
			$user_obj = SucomUtil::get_user_object();

			if ( ! empty( $user_obj->ID ) ) {
				$value = get_the_author_meta( 'wpseo_'.$meta_key, $user_obj->ID );
				if ( empty( $value ) &&
					! empty( $this->opts[$meta_key.'-author-wpseo'] ) )
						$value = wpseo_replace_vars( $this->opts[$meta_key.'-author-wpseo'], $user_obj );

			} elseif ( $this->p->debug->enabled )
				$this->p->debug->log( 'user_obj ID is empty' );

			return $value;
		}
	}
}

?>
