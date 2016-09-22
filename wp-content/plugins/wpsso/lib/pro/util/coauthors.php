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

if ( ! class_exists( 'WpssoProUtilCoAuthors' ) ) {

	class WpssoProUtilCoAuthors {

		private $p;

		public function __construct( &$plugin ) {
			$this->p =& $plugin;
			$this->p->util->add_plugin_filters( $this, array(
				'get_post_mod' => 2,
				'get_other_user_meta' => 2,
				'get_other_user_images' => 7,
				'get_author_meta' => array(
					'get_author_meta' => 4,
					'get_author_website' => 4,
				),
				'check_post_header' => 3,
				'description_seed' => 2,
			) );
			$this->p->util->add_plugin_filters( $this, array(
				'get_user_object' => 2,
			), 50, 'sucom' );
			add_filter( 'coauthors_guest_author_fields', 
				array( &$this, 'add_contact_methods' ), 20, 2 );
		}

		public function filter_get_post_mod( $mod, $mod_id ) {
			if ( $this->p->debug->enabled )
				$this->p->debug->mark();

			$coauthors = get_coauthors( $mod_id );
			if ( empty( $coauthors ) || ! is_array( $coauthors ) ) {
				if ( $this->p->debug->enabled )
					$this->p->debug->log( 'no coauthors found for post id '.$mod_id );
				return $mod;
			}

			// make sure the first (top) author listed is the post / page author
			$author = reset( $coauthors );
			if ( ! empty( $author->ID ) && (int) $author->ID !== $mod['post_author'] ) {
				if ( $this->p->debug->enabled )
					$this->p->debug->log( 'setting author id '.$author->ID.' as primary author' );
				$mod['post_author'] = (int) $author->ID;
			}

			foreach ( $coauthors as $author ) {
				if ( (int) $author->ID === $mod['post_author'] ) {
					if ( $this->p->debug->enabled )
						$this->p->debug->log( 'skipping coauthor id '.$author->ID.' (primary author)' );
				} else {
					$mod['post_coauthors'][] = (int) $author->ID;
					if ( $this->p->debug->enabled )
						$this->p->debug->log( 'adding coauthor id '.$author->ID );
				}
			}

			return $mod;
		}

		// hooked to 'sucom_get_user_object'
		public function filter_get_user_object( $user_obj, $user_id ) {
			global $coauthors_plus;

			if ( ! is_object( $user_obj ) && $user_id ) {
				if ( $this->p->debug->enabled )
					$this->p->debug->log( 'getting object for coauthor id '.$user_id );
				return $coauthors_plus->get_coauthor_by( 'id', $user_id );
			} else return $user_obj;
		}

		public function filter_get_other_user_images( $og_ret, $num, $size_name, $user_id, $check_dupes, $force_regen, $md_pre ) {

			if ( get_post_type( $user_id ) === 'guest-author' ) {
				if ( $this->p->debug->enabled )
					$this->p->debug->mark( 'guest author / post id '.$user_id.' images' );
				$og_ret = array_merge( $og_ret, $this->p->media->get_post_images( $num, 
					$size_name, $user_id, $check_dupes, $md_pre ) );
				if ( $this->p->debug->enabled )
					$this->p->debug->mark( 'guest author / post id '.$user_id.' images' );
			}
			return $og_ret;
		}

		// coauthor guest user meta is saved as a custom post type
		public function filter_get_other_user_meta( $opts, $user_id ) {

			if ( get_post_type( $user_id ) === 'guest-author' ) {
				if ( $this->p->debug->enabled )
					$this->p->debug->mark( 'guest author / post id '.$user_id.' meta' );
				$mod = $this->p->m['util']['post']->get_mod( $user_id );
				$opts = $mod['obj']->get_options( $user_id, false, false );
				if ( $this->p->debug->enabled )
					$this->p->debug->mark( 'guest author / post id '.$user_id.' meta' );
			}
			return $opts;
		}

		public function filter_get_author_meta( $value, $user_id, $field_id, $is_user ) {

			// abort if user_id is a valid wordpress user
			if ( $is_user )
				return $value;

			/*
			 * StdClass Object (
			 *	[ID] => 2606
			 *	[display_name] => Mr. John Doe
			 *	[first_name] => John
			 *	[last_name] => Doe
			 *	[user_login] => mr-john-doe
			 *	[user_email] => johndoe@someplace.com
			 *	[linked_account] =>
			 *	[website] => http://guest_website.com
			 *	[aim] =>
			 *	[yahooim] =>
			 *	[jabber] =>
			 *	[description] => Some Bio info for John Doe.
			 *	[user_nicename] => mr-john-doe
			 *	[type] => guest-author
			 * )
			 */
			$user_obj = $this->filter_get_user_object( false, $user_id );

			if ( isset( $user_obj->ID ) ) {
				if ( $this->p->debug->enabled )
					$this->p->debug->log( 'user_id '.$user_id.' coauthor object found' );
			} else return $value;

			switch ( $field_id ) {
				case 'fullname':
					return ( isset( $user_obj->first_name ) ?
						trim( $user_obj->first_name ) : '' ).' '.
							( isset( $user_obj->last_name ) ?
								trim( $user_obj->last_name ) : '' );
				case 'url':
					return isset( $user_obj->website ) ?
						trim( $user_obj->website ) : '';
				default:
					return isset( $user_obj->$field_id ) ?
						trim( $user_obj->$field_id ) : '';
			}

			return $value;
		}

		// don't check guest author custom post types (the permalink is not accessible)
		public function filter_check_post_header( $enabled, $post_id, $post_obj ) {
			if ( $enabled && isset( $post_obj->post_type ) && 
				$post_obj->post_type === 'guest-author' )
					return false;
			return $enabled;
		}

		// guest author custom post types don't have content
		// return the description author meta instead
		public function filter_description_seed( $value, $mod ) {
			if ( $mod['is_post'] && 
				$mod['post_type'] === 'guest-author' )
					return $this->filter_get_author_meta( $value, $mod['id'], 'description', false );
		}

		public function add_contact_methods( $fields = array(), $groups = null ) { 

			// use the same check as the coauthors plugin
			if ( ! in_array( 'contact-info', $groups ) && 
				'all' !== $groups[0] )
					return $fields;

			$lca = $this->p->cf['lca'];
			$aop = $this->p->check->aop( $lca, true, $this->p->is_avail['aop'] );

			// unset built-in contact fields and/or update their labels
			if ( ! empty( $this->p->cf['wp']['cm'] ) && 
				is_array( $this->p->cf['wp']['cm'] ) && $aop ) {

				foreach ( $fields as $num => $cm ) {
					if ( ! isset( $cm['key'] ) || 
						! isset( $cm['group'] ) || 
							$cm['group'] !== 'contact-info' )
								continue;
					// adjust for wp / coauthors key differences
					switch ( $cm['key'] ) {
						case 'yahooim':
							$cm_opt = 'wp_cm_yim_';
							break;
						default:
							$cm_opt = 'wp_cm_'.$cm['key'].'_';
							break;
					}
					if ( isset( $this->p->options[$cm_opt.'enabled'] ) ) {
						if ( ! empty( $this->p->options[$cm_opt.'enabled'] ) ) {
							if ( ! empty( $this->p->options[$cm_opt.'label'] ) )
								$fields[$num]['label'] = $this->p->options[$cm_opt.'label'];
						} else unset( $fields[$num] );
					}
				}
			}

			// loop through each social website option prefix
			if ( ! empty( $this->p->cf['opt']['pre'] ) && 
				is_array( $this->p->cf['opt']['pre'] ) ) {

				$sorted_pre = $this->p->cf['opt']['pre'];	// leave original as-is
				asort( $sorted_pre );				// sort associative array by value

				foreach ( $sorted_pre as $cm_id => $cm_pre ) {
					$cm_opt = 'plugin_cm_'.$cm_pre.'_';

					// not all social websites have a contact fields, so check
					if ( isset( $this->p->options[$cm_opt.'name'] ) ) {

						if ( ! empty( $this->p->options[$cm_opt.'enabled'] ) && 
							! empty( $this->p->options[$cm_opt.'name'] ) && 
							! empty( $this->p->options[$cm_opt.'label'] ) ) {

							$fields[] = array(
								'key' => $this->p->options[$cm_opt.'name'],
								'label' => $this->p->options[$cm_opt.'label'],
								'group' => 'contact-info',
							);
						}
					}
				}
			}

			return $fields;
		}
	}
}

?>
