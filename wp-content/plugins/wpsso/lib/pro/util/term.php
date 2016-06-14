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

if ( ! class_exists( 'WpssoProUtilTerm' ) && class_exists( 'WpssoTerm' ) ) {

	class WpssoProUtilTerm extends WpssoTerm {

		public function __construct( &$plugin ) {
			$this->p =& $plugin;
			if ( $this->p->debug->enabled )
				$this->p->debug->mark();
			$this->add_actions();	// method from WpssoTerm
		}

		public function get_og_image( $num, $size_name, $term_id, $check_dupes = true, $force_regen = false, $md_pre = 'og' ) {
			if ( $this->p->debug->enabled )
				$this->p->debug->mark();
			$mod = $this->get_mod( $term_id );
			return $this->get_md_image( $num, $size_name, $mod, $check_dupes, $force_regen, $md_pre, 'og' );
		}

		public function get_options( $term_id, $idx = false, $filter_options = true ) {

			if ( ! isset( $this->opts[$term_id]['options_filtered'] ) || 
				$this->opts[$term_id]['options_filtered'] !== true ) {

				$renamed_keys = apply_filters( $this->p->cf['lca'].'_get_md_renamed_keys', array(
				) );

				if ( $this->p->debug->enabled )
					$this->p->debug->log( 'retrieving term_id '.$term_id.' meta' );

				$this->opts[$term_id] = self::get_term_meta( $term_id, WPSSO_META_NAME, true );

				// look for alternate meta options name
				if ( ! is_array( $this->opts[$term_id] ) ) {
					if ( SucomUtil::get_const( 'WPSSO_META_NAME_ALT' ) ) {
						$this->opts[$term_id] = self::get_term_meta( $term_id, WPSSO_META_NAME_ALT, true );
						if ( is_array( $this->opts[$term_id] ) ) {
							self::update_term_meta( $term_id, WPSSO_META_NAME, $this->opts[$term_id] );
							self::delete_term_meta( $term_id, WPSSO_META_NAME_ALT );
						}
					}
				}

				if ( ! is_array( $this->opts[$term_id] ) )
					$this->opts[$term_id] = array();

				if ( ! empty( $this->opts[$term_id] ) && 
					( empty( $this->opts[$term_id]['options_version'] ) || 
						$this->opts[$term_id]['options_version'] !== $this->p->cf['opt']['version'] ) ) {

					if ( ! empty( $renamed_keys ) )
						$this->opts[$term_id] = SucomUtil::rename_keys( $this->opts[$term_id], $renamed_keys );
					$this->opts[$term_id]['options_version'] = $this->p->cf['opt']['version'];
					self::update_term_meta( $term_id, WPSSO_META_NAME, $this->opts[$term_id] );
					if ( $this->p->debug->enabled )
						$this->p->debug->log( 'term_id '.$term_id.' settings upgraded' );
				}

				if ( $filter_options ) {
					if ( $this->p->debug->enabled )
						$this->p->debug->log( 'applying filters for term_id '.$term_id.' meta' );

					$this->opts[$term_id] = apply_filters( $this->p->cf['lca'].'_get_term_options', $this->opts[$term_id], $term_id );
					$this->opts[$term_id]['options_filtered'] = true;

					if ( $this->p->debug->enabled )
						$this->p->debug->log( $this->opts[$term_id] );
				}
			}

			if ( $idx !== false ) {
				if ( isset( $this->opts[$term_id][$idx] ) ) 
					return $this->opts[$term_id][$idx];
				else return null;
			} else return $this->opts[$term_id];
		}

		public function save_options( $term_id, $term_tax_id = false ) {
			if ( $this->p->debug->enabled )
				$this->p->debug->mark();

			if ( ! $this->verify_submit_nonce() ) {
				if ( $this->p->debug->enabled )
					$this->p->debug->log( 'exiting early: verify_submit_nonce failed' );
				return false;
			}

			if ( ! current_user_can( $this->tax_obj->cap->edit_terms ) ) {
				if ( $this->p->debug->enabled )
					$this->p->debug->log( 'insufficient privileges to save settings for term ID '.$term_id );
				$this->p->notice->err( 'You have insufficient privileges to save settings for term ID '.$term_id.'.', true );
				return false;
			}

			$opts = $this->get_submit_opts( $term_id );

			if ( empty( $opts ) )
				self::delete_term_meta( $term_id, WPSSO_META_NAME );
			else self::update_term_meta( $term_id, WPSSO_META_NAME, $opts );

			return $term_id;
		}

		public function delete_options( $term_id, $term_tax_id = false ) {
			return self::delete_term_meta( $term_id, WPSSO_META_NAME );
		}
	}
}

?>
