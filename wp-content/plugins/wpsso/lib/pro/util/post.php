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

if ( ! class_exists( 'WpssoProUtilPost' ) && class_exists( 'WpssoPost' ) ) {

	class WpssoProUtilPost extends WpssoPost {

		public function __construct( &$plugin ) {
			$this->p =& $plugin;
			if ( $this->p->debug->enabled )
				$this->p->debug->mark();
			$this->add_actions();	// method from WpssoPost
		}

		public function get_og_image( $num, $size_name, $post_id, $check_dupes = true, $force_regen = false, $md_pre = 'og' ) {
			if ( $this->p->debug->enabled )
				$this->p->debug->mark();
			$mod = $this->get_mod( $post_id );
			return $this->get_md_image( $num, $size_name, $mod, $check_dupes, $force_regen, $md_pre, 'og' );
		}

		public function get_options( $post_id, $idx = false, $filter_options = true ) {

			if ( ! isset( $this->opts[$post_id]['options_filtered'] ) || 
				$this->opts[$post_id]['options_filtered'] !== true ) {

				$renamed_keys = apply_filters( $this->p->cf['lca'].'_get_md_renamed_keys', array(
					'link_desc' => 'seo_desc',
					'meta_desc' => 'seo_desc',
				) );

				if ( $this->p->debug->enabled )
					$this->p->debug->log( 'retrieving post_id '.$post_id.' meta' );

				$this->opts[$post_id] = get_post_meta( $post_id, WPSSO_META_NAME, true );	// single = true

				// look for alternate meta options name
				if ( ! is_array( $this->opts[$post_id] ) ) {
					if ( SucomUtil::get_const( 'WPSSO_META_NAME_ALT' ) ) {
						$this->opts[$post_id] = get_post_meta( $post_id, WPSSO_META_NAME_ALT, true );
						if ( is_array( $this->opts[$post_id] ) ) {
							update_post_meta( $post_id, WPSSO_META_NAME, $this->opts[$post_id] );
							delete_post_meta( $post_id, WPSSO_META_NAME_ALT );
						}
					}
				}

				if ( ! is_array( $this->opts[$post_id] ) )
					$this->opts[$post_id] = array();

				if ( ! empty( $this->opts[$post_id] ) && 
					( empty( $this->opts[$post_id]['options_version'] ) || 
						$this->opts[$post_id]['options_version'] !== $this->p->cf['opt']['version'] ) ) {

					if ( ! empty( $renamed_keys ) )
						$this->opts[$post_id] = SucomUtil::rename_keys( $this->opts[$post_id], $renamed_keys );
					$this->opts[$post_id]['options_version'] = $this->p->cf['opt']['version'];
					update_post_meta( $post_id, WPSSO_META_NAME, $this->opts[$post_id] );
					if ( $this->p->debug->enabled )
						$this->p->debug->log( 'post_id '.$post_id.' settings upgraded' );
				}

				// allow certain 3rd-party custom field values to override those of our social settings
				$post_meta = get_post_meta( $post_id, null, false );

				if ( is_array( $post_meta ) && ! empty( $post_meta ) ) {
					$charset = get_bloginfo( 'charset' );	// required for html_entity_decode()
					foreach ( array( 
						'plugin_cf_img_url' => 'og_img_url',
						'plugin_cf_vid_url' => 'og_vid_url',
						'plugin_cf_vid_embed' => 'og_vid_embed',
					) as $cf_opt_name => $meta_opt_name ) {
						// check that a custom field name has been defined
						if ( ! empty( $this->p->options[$cf_opt_name] ) ) {
							$cf_name = $this->p->options[$cf_opt_name];
							// empty or not, if the array element is set, use it
							if ( isset( $post_meta[$cf_name] ) ) {
								if ( $this->p->debug->enabled )
									$this->p->debug->log( $cf_name.' custom field found - setting '.
										$meta_opt_name.' to '.$cf_name.' value' );
								$this->opts[$post_id][$meta_opt_name] = html_entity_decode( SucomUtil::decode_utf8( get_post_meta( $post_id, 
									$cf_name, true ) ), ENT_QUOTES, $charset );
								$this->opts[$post_id][$meta_opt_name.':is'] = 'disabled';
							}
						}
					}
				}

				if ( $filter_options ) {
					if ( $this->p->debug->enabled )
						$this->p->debug->log( 'applying filters for post_id '.$post_id.' meta' );

					$this->opts[$post_id] = apply_filters( $this->p->cf['lca'].'_get_post_options', $this->opts[$post_id], $post_id );
					$this->opts[$post_id]['options_filtered'] = true;

					if ( $this->p->debug->enabled )
						$this->p->debug->log( $this->opts[$post_id] );
				}
			}

			if ( $idx !== false ) {
				if ( isset( $this->opts[$post_id][$idx] ) ) 
					return $this->opts[$post_id][$idx];
				else return null;
			} else return $this->opts[$post_id];
		}

		public function save_options( $post_id, $rel_id = false ) {
			if ( $this->p->debug->enabled )
				$this->p->debug->mark();

			$user_can_edit = false;
			$post_type = empty( $_POST['post_type'] ) ?
				'post' : $_POST['post_type'];

			if ( ! $this->verify_submit_nonce() ) {
				if ( $this->p->debug->enabled )
					$this->p->debug->log( 'exiting early: verify_submit_nonce failed' );
				return false;
			}

			switch ( $post_type ) {
				case 'page' :
					if ( current_user_can( 'edit_page', $post_id ) )
						$user_can_edit = true;
					break;
				default :
					if ( current_user_can( 'edit_post', $post_id ) )
						$user_can_edit = true;
					break;
			}

			if ( $user_can_edit === false ) {
				if ( $this->p->debug->enabled )
					$this->p->debug->log( 'insufficient privileges to save settings for '.$post_type.' ID '.$post_id );
				$this->p->notice->err( 'You have insufficient privileges to save settings for '.$post_type.' ID '.$post_id.'.', true );
				return false;
			}

			$opts = $this->get_submit_opts( $post_id );

			if ( empty( $opts ) )
				delete_post_meta( $post_id, WPSSO_META_NAME );
			else update_post_meta( $post_id, WPSSO_META_NAME, $opts );

			return $post_id;
		}
	}
}

?>