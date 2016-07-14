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

if ( ! class_exists( 'WpssoProSocialBuddypress' ) ) {

	class WpssoProSocialBuddypress {

		private $p;

		public function __construct( &$plugin ) {
			$this->p =& $plugin;
			if ( $this->p->debug->enabled )
				$this->p->debug->mark();

			if ( is_admin() || bp_current_component() ) {
				if ( bp_current_component() ) {
					if ( $this->p->debug->enabled )
						$this->p->debug->log( 'bp_current_component() = '.bp_current_component() );

					$this->p->util->add_plugin_filters( $this, array( 
						'is_functions' => 1,
						'title_seed' => 4,
						'content_seed' => 3,
						'attached_images' => 5,
						'post_url' => 2,
						'user_image_urls' => 3,
						'user_object_description' => 2,
					), 20 );	// run before bbpress

					$this->p->util->add_plugin_filters( $this, array(
						'is_post_page' => 2,
						'is_user_page' => 1,
						'get_post_object' => 2,
						'get_user_object' => 1,
					), 100, 'sucom' );
				}

				// BuddyPress oEmbed needs an ID, so make sure we have one
				// see shortcode() in BP_Embed class (bp-core/classes/class-bp-embed.php)
				add_filter( 'embed_post_id', array( &$this, 'return_embed_post_id' ) );
			}
		}

		public function filter_is_functions( $arr ) {
			// from bp-core/bp-core-template.php
			return array_merge( $arr, array(
				'bp_is_single_item',
				'bp_is_item_admin',
				'bp_is_item_mod',
				'bp_is_directory',
				'bp_is_blog_page',
				'bp_is_members_component',
				'bp_is_profile_component',
				'bp_is_activity_component',
				'bp_is_blogs_component',
				'bp_is_messages_component',
				'bp_is_friends_component',
				'bp_is_groups_component',
				'bp_is_forums_component',
				'bp_is_notifications_component',
				'bp_is_settings_component',
				'bp_is_current_component_core',
				'bp_is_activity_directory',
				'bp_is_single_activity',
				'bp_is_members_directory',
				'bp_is_my_profile',
				'bp_is_user',
				'bp_is_user_activity',
				'bp_is_user_friends_activity',
				'bp_is_user_groups_activity',
				'bp_is_user_profile',
				'bp_is_user_profile_edit',
				'bp_is_user_change_avatar',
				'bp_is_user_change_cover_image',
				'bp_is_user_forums',
				'bp_is_user_forums_started',
				'bp_is_user_forums_replied_to',
				'bp_is_user_groups',
				'bp_is_user_blogs',
				'bp_is_user_recent_posts',
				'bp_is_user_recent_commments',
				'bp_is_user_friends',
				'bp_is_user_friend_requests',
				'bp_is_user_notifications',
				'bp_is_user_settings',
				'bp_is_user_settings_general',
				'bp_is_user_settings_notifications',
				'bp_is_user_settings_account_delete',
				'bp_is_user_settings_profile',
				'bp_is_groups_directory',
				'bp_is_group',
				'bp_is_group_home',
				'bp_is_group_create',
				'bp_is_group_admin_page',
				'bp_is_group_forum',
				'bp_is_group_activity',
				'bp_is_group_forum_topic',
				'bp_is_group_forum_topic_edit',
				'bp_is_group_members',
				'bp_is_group_invites',
				'bp_is_group_membership_request',
				'bp_is_group_leave',
				'bp_is_group',
				'bp_is_group_single',
				'bp_is_group_custom_front',
				'bp_is_create_blog',
				'bp_is_blogs_directory',
				'bp_is_user_messages',
				'bp_is_messages_inbox',
				'bp_is_messages_sentbox',
				'bp_is_messages_compose_screen',
				'bp_is_notices',
				'bp_is_messages_conversation',
				'bp_is_activation_page',
				'bp_is_register_page',
			) );
		}

		public function filter_title_seed( $title, $mod, $add_hashtags, $md_idx ) {

			if ( $this->p->debug->enabled ) {
				$this->p->debug->args( array( 
					'title strlen' => strlen( $title ),
					'mod' => $mod,
					'add_hashtags' => $add_hashtags,
					'md_idx' => $md_idx,
				) );
			}

			if ( in_the_loop() ) {
				if ( $this->p->debug->enabled )
					$this->p->debug->log( 'in_the_loop() = true' );

				$title = bp_get_activity_action( array( 'no_timestamp' => true ) );
				if ( $this->p->debug->enabled )
					$this->p->debug->log( 'title from bp_get_activity_action()' );

			} elseif ( bp_is_current_component( 'groups' ) ) {
				if ( bp_has_groups() ) {
					if ( bp_is_group_single() ) {
						if ( bp_is_group_forum_topic() ) {
							if ( $this->p->debug->enabled )
								$this->p->debug->log( 'bp_is_group_forum_topic() = true' );

							$separator = html_entity_decode( $this->p->options['og_title_sep'], 
								ENT_QUOTES, get_bloginfo( 'charset' ) );
							$title = $this->get_group_forum_topic_post_title();
							if ( $this->p->options['plugin_filter_title'] )
								$title = $title.' '.$separator.' '.get_bloginfo( 'name', 'display' );
						}
					}
				}
			}

			return $title;
		}

		public function filter_content_seed( $content, $mod, $md_idx ) {

			if ( $this->p->debug->enabled ) {
				$this->p->debug->args( array( 
					'content strlen' => strlen( $content ),
					'mod' => $mod,
					'md_idx' => $md_idx,
				) );
			}

			if ( bp_is_user() ) {
				if ( $this->p->debug->enabled )
					$this->p->debug->log( 'bp_is_user() = true' );

				if ( in_the_loop() ) {
					if ( $this->p->debug->enabled )
						$this->p->debug->log( 'in_the_loop() = true' );

					$content = bp_get_activity_content_body();
					if ( $this->p->debug->enabled )
						$this->p->debug->log( 'content from bp_get_activity_content_body()' );

				} elseif ( bp_is_current_component( 'activity' ) ) {
					global $bp;
					// single activity page
					if ( is_numeric( $bp->current_action ) ) {

						$activity = bp_activity_get_specific( array( 'activity_ids' => $bp->current_action ) );
						$content = apply_filters( 'bp_get_activity_content_body', $activity['activities'][0]->content );
						if ( $this->p->debug->enabled )
							$this->p->debug->log( 'content from bp_activity_get_specific()' );

						$content .= bp_get_member_avatar( array( 'type' => 'full' ) );
						if ( $this->p->debug->enabled )
							$this->p->debug->log( 'including member avatar html' );

					// index activity page
					} else {
						$user_id = bp_displayed_user_id();

						// prefer the custom user profile description
						if ( ! empty( $md_idx ) )
							$content = $this->p->m['util']['user'] ?
								$this->p->m['util']['user']->get_options_multi( $user_id, $md_idx ) : null;

						if ( empty( $content ) ) {
							$user_desc = get_user_meta( $user_id, 'description', true );
							$user_name = get_user_meta( $user_id, 'display_name', true );

							if ( empty( $user_name ) )
								$user_name = get_user_meta( $user_id, 'first_name', true ).
									' '.get_user_meta( $user_id, 'last_name', true );

							$content = empty( $user_desc ) ? 
								get_bloginfo( 'name', 'display' ).' Activity for '.$user_name : $user_desc;

							if ( $this->p->debug->enabled )
								$this->p->debug->log( 'description using bp_displayed_user_id()' );
						}
					}
				}
			} elseif ( bp_is_current_component( 'members' ) ) {
				if ( $this->p->debug->enabled )
					$this->p->debug->log( 'bp_is_current_component() = members' );

				if ( bp_has_members() )
					$content = bp_get_members_pagination_count();
				else $content = get_bloginfo( 'name', 'display' ).' Members.';

			} elseif ( bp_is_current_component( 'groups' ) ) {
				if ( $this->p->debug->enabled )
					$this->p->debug->log( 'bp_is_current_component() = groups' );

				if ( bp_has_groups() ) {
					if ( bp_is_group_single() ) {
						if ( bp_is_group_forum_topic() ) {
							if ( $this->p->debug->enabled )
								$this->p->debug->log( 'bp_is_group_forum_topic() = true' );
							$content = $this->get_group_forum_topic_post_content();
						} else {
							bp_the_group();
							$content = bp_get_group_description();
							if ( $this->p->debug->enabled )
								$this->p->debug->log( 'content from bp_get_group_description()' );
						}
					} else {
						$content = bp_get_groups_pagination_count();
						if ( $this->p->debug->enabled )
							$this->p->debug->log( 'content from bp_get_groups_pagination_count()' );
					}
				} else {
					$content = get_bloginfo( 'name', 'display' ).' Groups.';
					if ( $this->p->debug->enabled )
						$this->p->debug->log( 'content from get_bloginfo()' );
				}

			} elseif ( bp_is_current_component( 'activity' ) ) {
				if ( $this->p->debug->enabled )
					$this->p->debug->log( 'bp_is_current_component() = activity' );

				if ( in_the_loop() ) {
					if ( $this->p->debug->enabled )
						$this->p->debug->log( 'in_the_loop() = true' );

					$content = bp_get_activity_content_body();
					if ( $this->p->debug->enabled )
						$this->p->debug->log( 'content from bp_get_activity_content_body()' );

				} else {
					global $bp;
					if ( is_numeric( $bp->current_action ) ) {
						$activity = bp_activity_get_specific( array( 'activity_ids' => $bp->current_action ) );
						$content = apply_filters( 'bp_get_activity_content_body', $activity['activities'][0]->content );
						if ( $this->p->debug->enabled )
							$this->p->debug->log( 'content from bp_activity_get_specific()' );
					} else $content = get_bloginfo( 'name', 'display' ).' Activities.';
				}
			}

			return $content;
		}

		private function get_group_forum_topic_post_obj() {
			$topic_slug = bp_action_variable( 1 );
			$post_obj = get_posts( array(
				'name' => $topic_slug,
				'post_type' => bbp_get_topic_post_type(),
				'posts_per_page' => 1,
			) );
			if ( $this->p->debug->enabled )
				$this->p->debug->log( 'returning post_obj for '.$topic_slug );
			return $post_obj[0];
		}

		private function get_group_forum_topic_post_title() {
			if ( $this->p->debug->enabled )
				$this->p->debug->mark();

			$post_obj = $this->get_group_forum_topic_post_obj();

			if ( isset( $post_obj->post_title ) )
				return $post_obj->post_title;
			elseif ( $this->p->debug->enabled )
				$this->p->debug->log( 'no post_title for returned post_obj' );
		}

		private function get_group_forum_topic_post_content() {
			if ( $this->p->debug->enabled )
				$this->p->debug->mark();

			$post_obj = $this->get_group_forum_topic_post_obj();

			if ( isset( $post_obj->post_content ) )
				return $post_obj->post_content;
			elseif ( $this->p->debug->enabled )
				$this->p->debug->log( 'no post_content for returned post_obj' );
		}

		// include user or group avatar (first or as fallback to activity image)
		public function filter_attached_images( $og_ret = array(), $num = 0, $size_name = 'thumbnail', $post_id, $check_dupes = true ) {

			if ( $this->p->debug->enabled )
				$this->p->debug->mark();

			$content = '';
			if ( bp_is_user() ) {
				if ( $this->p->debug->enabled )
					$this->p->debug->log( 'bp_is_user() = true' );

				if ( bp_is_current_component( 'activity' ) ) {
					global $bp;
					// index activity page
					if ( is_numeric( $bp->current_action ) ) {
						if ( $this->p->debug->enabled )
							$this->p->debug->log( 'member avatar skipped: specific activity requested' );
					} else {
						$content .= bp_get_member_avatar( array( 'type' => 'full' ) );
						if ( $this->p->debug->enabled )
							$this->p->debug->log( 'including member avatar html' );
					}
				} else {
					$content .= bp_get_member_avatar( array( 'type' => 'full' ) );
					if ( $this->p->debug->enabled )
						$this->p->debug->log( 'including member avatar html' );
				}
			} elseif ( bp_is_current_component( 'groups' ) ) {
				if ( bp_has_groups() ) {
					if ( bp_is_group_single() ) {
						if ( bp_is_group_forum_topic() ) {
							// nothing to do
							if ( $this->p->debug->enabled )
								$this->p->debug->log( 'group avatar skipped: using forum topic instead' );
						} else {
							bp_the_group();
							$content .= bp_get_group_avatar( array( 'type' => 'full' ) );
							if ( $this->p->debug->enabled )
								$this->p->debug->log( 'including group avatar html' );
						}
					}
				}
			}

			// provide content to the get_content_images() method and extract its images
			if ( ! empty( $content ) ) {
				if ( $this->p->debug->enabled )
					$this->p->debug->log( 'filtering content using WpssoMedia get_content_images()' );
				return array_merge( $og_ret, $this->p->media->get_content_images( $num, $size_name, false, $check_dupes, $content ) );
			} else {
				if ( $this->p->debug->enabled )
					$this->p->debug->log( 'no content html to filter for images' );
				return $og_ret;
			}
		}

		public function filter_post_url( $url, $mod ) {
			if ( $this->p->debug->enabled )
				$this->p->debug->mark();

			$bp_url = bp_get_canonical_url();	// default value

			if ( in_the_loop() ) {
				if ( $this->p->debug->enabled )
					$this->p->debug->log( 'in_the_loop() = true' );

				$bp_url = bp_activity_get_permalink( bp_get_activity_id() );
				if ( $this->p->debug->enabled )
					$this->p->debug->log( 'url from bp_activity_get_permalink()' );
			}

			if ( ! empty( $bp_url ) ) {
				if ( $this->p->debug->enabled )
					$this->p->debug->log( 'returning bp_url = '.$bp_url );
				return $bp_url;
			} else return $url;
		}

		public function filter_is_post_page( $ret, $use_post ) {
			if ( bp_is_current_component( 'activity' ) ) {
				global $bp;
				if ( is_numeric( $bp->current_action ) ) {	// single activity page
					if ( $this->p->debug->enabled )
						$this->p->debug->log( 'current action is numeric activity '.$bp->current_action );
					return true;
				}
			} elseif ( bp_is_current_component( 'groups' ) ) {
				if ( bp_is_group_single() ) {
					if ( bp_is_group_forum_topic() ) {
						return true;
					}
				}
			}
			return $ret;
		}

		public function filter_get_post_object( $post_obj, $use_post ) {
			if ( $this->p->debug->enabled )
				$this->p->debug->mark();

			if ( bp_is_current_component( 'groups' ) ) {
				if ( bp_is_group_single() ) {
					if ( bp_is_group_forum_topic() ) {
						$post_obj = $this->get_group_forum_topic_post_obj();
					}
				}
			}
			return $post_obj;
		}

		public function filter_is_user_page( $ret ) {
			if ( bp_is_user() )
				return true;
			return $ret;
		}

		public function filter_get_user_object( $user_obj ) {
			if ( bp_is_user() )
				return get_user_by( 'id', bp_displayed_user_id() );
			return $user_obj;
		}

		// BuddyPress oEmbed needs an ID, so make sure we have one
		// see shortcode() in BP_Embed class (bp-core/classes/class-bp-embed.php)
		public function return_embed_post_id( $id ) {
			if ( empty( $id ) ) {
				if ( bp_is_current_component( 'activity' ) ) {
					global $bp;
					// single activity page
					if ( is_numeric( $bp->current_action ) ) {
						$id = $bp->current_action;
						if ( $this->p->debug->enabled )
							$this->p->debug->log( 'empty embed post id: returning activity id '.$id );
					}
				}
			}
			return $id;
		}

		public function filter_user_image_urls( $urls, $size_name, $user_id ) {
			if ( $this->p->debug->enabled )
				$this->p->debug->mark();

			$urls[] = bp_core_fetch_avatar( array(
				'item_id' => $user_id,
				'type' => 'full',
				'html'=> false
			) );

			return $urls;
		}

		public function filter_user_object_description( $desc, $user_obj ) {
			if ( $this->p->debug->enabled )
				$this->p->debug->mark();

			if ( ! bp_is_user_profile() ) {
				if ( $this->p->debug->enabled )
					$this->p->debug->log( 'exiting early: not a buddypress user profile' );
				return $desc;
			}

			$const_name = $this->p->cf['uca'].'_BP_MEMBER_BIOGRAPHICAL_FIELD';

			if ( $field_name = SucomUtil::get_const( $const_name ) ) {

				$field_value = bp_get_profile_field_data( array( 'field' => $field_name, 'user_id' => $user_obj->ID ) );

				if ( empty( $field_value ) ) {
					$desc = sprintf( '%s Member Profile', $user_obj->display_name );
					if ( $this->p->debug->enabled )
						$this->p->debug->log( 'the profile field "'.$field_name.'" value is empty' );
				} else {
					$desc = $field_value;
					if ( $this->p->debug->enabled )
						$this->p->debug->log( 'returning the "'.$field_name.'" profile field value' );
				}

			} elseif ( $this->p->debug->enabled )
				$this->p->debug->log( $const_name.' constant not defined' );

			return $desc;
		}
	}
}

?>
