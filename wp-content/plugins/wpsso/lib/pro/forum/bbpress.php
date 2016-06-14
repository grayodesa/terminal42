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

if ( ! class_exists( 'WpssoProForumBbpress' ) ) {

	class WpssoProForumBbpress {

		private $p;
		private $has_setup = false;
		private $bbp_setup = array(
			'post_id' => 0,
			'post_type' => '',
			'topic_type' => 'topic',
			'forum_type' => 'forum',
			'reply_type' => 'reply',
		);

		public function __construct( &$plugin ) {
			$this->p =& $plugin;

			if ( $this->p->debug->enabled )
				$this->p->debug->mark();

			if ( class_exists( 'bbpress' ) ) {	// is_bbpress() is not available here
				$this->p->util->add_plugin_filters( $this, array( 
					'title_seed' => 1, 
					'description_seed' => 1, 
					'tags_seed' => 1,
					'text_filter_has_changes_before' => 2,
					'text_filter_has_changes_after' => 2,
				), 30 );	// run after buddypress
			}
		}

		private function set_properties() {
			if ( $this->has_setup === true )
				return;

			if ( $this->p->debug->enabled )
				$this->p->debug->log( 'bbpress properties setup' );

			$this->bbp_setup['post_id'] = SucomUtil::get_post_object( true, 'id' );
			$this->bbp_setup['post_type'] = isset( $post->post_type ) ? $post->post_type : '';
			$this->bbp_setup['topic_type'] = bbp_get_topic_post_type();
			$this->bbp_setup['forum_type'] = bbp_get_forum_post_type();
			$this->bbp_setup['reply_type'] = bbp_get_reply_post_type();

			if ( bbp_is_user_home() )
				$this->bbp_setup['post_type'] = 'user_home';
			elseif ( bbp_is_topic_tag() )
				$this->bbp_setup['post_type'] = 'topic_tag';

			if ( $this->p->debug->enabled )
				$this->p->debug->log( $this->bbp_setup );

			$this->has_setup = true;
		}

		public function filter_title_seed( $text = '' ) {
			if ( ! is_admin() && ! is_bbpress() )
				return $text;

			$this->set_properties();

			switch ( $this->bbp_setup['post_type'] ) {
				case 'user_home':
					if ( ! $this->p->is_avail['social']['buddypress'] )
						$text = sprintf( esc_attr_x( "%s's Profile",
							'bbpress single user description', 'bbpress' ),
								bbp_get_displayed_user_field( 'display_name' ) );
					break;
			}
			return $text;
		}

		public function filter_description_seed( $text = '' ) {
			if ( ! is_admin() && ! is_bbpress() )
				return $text;

			$this->set_properties();

			switch ( $this->bbp_setup['post_type'] ) {

				case $this->bbp_setup['forum_type']:

					if ( bbp_is_forum_archive() ) {
						$parent_id = bbp_is_forum_archive() ? 0 : bbp_get_forum_id();
						$forums = get_children( array( 'post_parent' => $parent_id,
							'post_type' => $this->bbp_setup['forum_type'] ) );
						$text = 'Forum Archive: '.count( $forums ).' Forums';
					} else {
						$text = bbp_get_forum_content();
						if ( empty( $text ) )
							$text = 'No Forum Description';
					}
					break;

				case $this->bbp_setup['topic_type']:

					$topic_id = bbp_get_topic_id( $this->bbp_setup['post_id'] );
					$text = bbp_get_topic_excerpt( $topic_id, 0 );
					if ( empty( $text ) )
						$text = 'No Topic Excerpt';
					break;

				case 'topic_tag':

					$text = bbp_get_topic_tag_description();
					if ( empty( $text ) )
						$text = 'No Topic Tag Description';
					// this calls the filter_tags_seed() method indirectly
					if ( ! empty( $this->p->options['og_desc_hashtags'] ) )
						$text .= ' '.$this->p->webpage->get_hashtags( $this->bbp_setup['post_id'] );
					break;

				case $this->bbp_setup['reply_type']:

					$reply_id = bbp_get_reply_id( $this->bbp_setup['post_id'] );
					$text = bbp_get_reply_excerpt( $reply_id, 0 );
					$text = preg_replace( '/\s*This reply was modified .*$/', '', $text );
					break;

				case 'single_user':

					if ( bbp_is_single_user_profile() ) {
						$user_id = bbp_get_displayed_user_id();
						$author = get_userdata( $user_id );
						if ( empty( $author->description ) )
							$text = sprintf( esc_attr_x( "%s's Profile",
								'bbpress single user description', 'bbpress' ),
									bbp_get_displayed_user_field( 'display_name' ) );
						else $text = $author->description;
					}
					elseif ( bbp_is_single_user_topics() )
						$text = sprintf( esc_attr_x( "%s's Topics Started",
							'bbpress single user description', 'bbpress' ),
								bbp_get_displayed_user_field( 'display_name' ) );
					elseif ( bbp_is_single_user_replies() )
						$text = sprintf( esc_attr_x( "%s's Replies Created",
							'bbpress single user description', 'bbpress' ),
								bbp_get_displayed_user_field( 'display_name' ) );
					elseif ( bbp_is_favorites_active() )
						$text = sprintf( esc_attr_x( "%s's Favorites",
							'bbpress single user description', 'bbpress' ),
								bbp_get_displayed_user_field( 'display_name' ) );
					elseif ( bbp_is_subscriptions_active() )
						$text = sprintf( esc_attr_x( "%s's Subscriptions",
							'bbpress single user description', 'bbpress' ),
								bbp_get_displayed_user_field( 'display_name' ) );
					elseif ( bbp_is_single_user_edit() )
						$text = sprintf( esc_attr_x( "Edit %s's Profile",
							'bbpress single user description', 'bbpress' ),
								bbp_get_displayed_user_field( 'display_name' ) );
					break;

				default:
					if ( $this->p->debug->enabled )
						$this->p->debug->log( 'unknown bbpress post_type' );
					break;
			}
			return $text;
		}

		public function filter_tags_seed( $tags = array() ) {
			if ( ! is_admin() && ! is_bbpress() )
				return $tags;

			$this->set_properties();

			switch ( $this->bbp_setup['post_type'] ) {

				case $this->bbp_setup['topic_type']:

					$topic_id = bbp_get_topic_id( $this->bbp_setup['post_id'] );
					$terms = array_filter( (array) get_the_terms( $topic_id, bbp_get_topic_tag_tax_id() ) );
					foreach ( $terms as $term ) 
						$tags[] = $term->name;
					break;

				case 'topic_tag':

					$tags[] = bbp_get_topic_tag_name();
					break;
			}

			if ( ! empty( $tags ) )
				$tags = array_map( array( 'SucomUtil', 'sanitize_tag' ), $tags );

			return $tags;
		}

		public function filter_text_filter_has_changes_before( $ret, $filter_name ) {
			add_filter( 'bbp_use_wp_editor', '__return_false', 1100 );
			return true;
		}

		public function filter_text_filter_has_changes_after( $ret, $filter_name ) {
			remove_filter( 'bbp_use_wp_editor', '__return_false', 1100 );
			return true;
		}
	}
}

?>
