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

if ( ! class_exists( 'WpssoProUtilUser' ) && class_exists( 'WpssoUser' ) ) {

	class WpssoProUtilUser extends WpssoUser {

		public function __construct( &$plugin ) {
			$this->p =& $plugin;
			if ( $this->p->debug->enabled )
				$this->p->debug->mark();
			$this->add_actions();	// method from WpssoUser
		}

		public function get_og_image( $num, $size_name, $user_id, $check_dupes = true, $force_regen = false, $md_pre = 'og' ) {
			if ( $this->p->debug->enabled )
				$this->p->debug->mark();
			
			$lca = $this->p->cf['lca'];
			$mod = $this->get_mod( $user_id );
			$is_user = SucomUtil::user_exists( $user_id );	// is this a valid wordpress user

			if ( $is_user )
				return $this->get_md_image( $num, 
					$size_name, $mod, $check_dupes, $force_regen, $md_pre, 'og' );	// $mt_pre = og
			else return apply_filters( $lca.'_get_other_user_images', array(), $num,
				$size_name, $user_id, $check_dupes, $force_regen, $md_pre );
		}

		public function get_options( $user_id, $idx = false, $filter_options = true ) {
			if ( $this->p->debug->enabled ) {
				$this->p->debug->log_args( array( 
					'user_id' => $user_id, 
					'idx' => $idx, 
					'filter_options' => $filter_options, 
				) );
			}

			$lca = $this->p->cf['lca'];
			$user_id = $user_id === false ? 
				get_current_user_id() : $user_id;

			if ( empty( $user_id ) )
				return false;

			if ( empty( $this->opts[$user_id]['options_filtered'] ) ) {
				if ( $this->p->debug->enabled )
					$this->p->debug->log( 'options_filtered key is empty' );

				$renamed_keys = apply_filters( $lca.'_get_md_renamed_keys', array(
				) );

				if ( $this->p->debug->enabled )
					$this->p->debug->log( 'retrieving user_id '.$user_id.' meta' );

				$is_user = SucomUtil::user_exists( $user_id );	// is this a valid wordpress user

				if ( $is_user ) {
					$this->opts[$user_id] = get_user_meta( $user_id, WPSSO_META_NAME, true );
					if ( ! is_array( $this->opts[$user_id] ) ) {
						if ( SucomUtil::get_const( 'WPSSO_META_NAME_ALT' ) ) {	// look for alternate meta options name
							$this->opts[$user_id] = get_user_meta( $user_id, WPSSO_META_NAME_ALT, true );
							if ( is_array( $this->opts[$user_id] ) ) {
								update_user_meta( $user_id, WPSSO_META_NAME, $this->opts[$user_id] );
								delete_user_meta( $user_id, WPSSO_META_NAME_ALT );
							}
						}
					}
				} else $this->opts[$user_id] = apply_filters( $lca.'_get_other_user_meta', false, $user_id );

				if ( ! is_array( $this->opts[$user_id] ) )
					$this->opts[$user_id] = array();

				if ( ! empty( $this->opts[$user_id] ) && 
					( empty( $this->opts[$user_id]['options_version'] ) || 
						$this->opts[$user_id]['options_version'] !== $this->p->cf['opt']['version'] ) ) {

					if ( ! empty( $renamed_keys ) )
						$this->opts[$user_id] = SucomUtil::rename_keys( $this->opts[$user_id], $renamed_keys );

					$this->opts[$user_id]['options_version'] = $this->p->cf['opt']['version'];

					if ( $is_user )
						update_user_meta( $user_id, WPSSO_META_NAME, $this->opts[$user_id] );
					else apply_filters( $lca.'_update_other_user_meta', $this->opts[$user_id], $user_id );

					if ( $this->p->debug->enabled )
						$this->p->debug->log( 'user_id '.$user_id.' settings upgraded' );
				}

				if ( $filter_options ) {
					if ( $this->p->debug->enabled )
						$this->p->debug->log( 'applying filters for user_id '.$user_id.' meta' );

					$this->opts[$user_id] = apply_filters( $lca.'_get_user_options', $this->opts[$user_id], $user_id );
					$this->opts[$user_id]['options_filtered'] = true;

					if ( $this->p->debug->enabled )
						$this->p->debug->log( $this->opts[$user_id] );

				} elseif ( $this->p->debug->enabled )
					$this->p->debug->log( 'get_user_options filter skipped' );

			} elseif ( $this->p->debug->enabled )
				$this->p->debug->log( 'using cached options for user_id '.$user_id );

			if ( $idx !== false ) {
				if ( isset( $this->opts[$user_id][$idx] ) ) 
					return $this->opts[$user_id][$idx];
				else return null;
			} else return $this->opts[$user_id];
		}

		public function save_options( $user_id, $rel_id = false ) {
			if ( $this->p->debug->enabled )
				$this->p->debug->mark();

			if ( ! $this->verify_submit_nonce() ) {
				if ( $this->p->debug->enabled )
					$this->p->debug->log( 'exiting early: verify_submit_nonce failed' );
				return false;
			}

			if ( ! current_user_can( 'edit_user', $user_id ) ) {
				if ( $this->p->debug->enabled )
					$this->p->debug->log( 'insufficient privileges to save settings for user ID '.$user_id );
				$this->p->notice->err( 'You have insufficient privileges to save settings for user ID '.$user_id.'.' );
				return false;
			}

			$opts = $this->get_submit_opts( $user_id );

			if ( empty( $opts ) )
				delete_user_meta( $user_id, WPSSO_META_NAME );
			else update_user_meta( $user_id, WPSSO_META_NAME, $opts );

			return $user_id;
		}
	}
}

?>
