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

if ( ! class_exists( 'WpssoProMediaNgg' ) ) {

	class WpssoProMediaNgg {

		private $p;

		public $ngg_options = array();	// nextgen gallery options
		public $ngg_version = 0;	// nextgen gallery version

		public function __construct( &$plugin ) {
			$this->p =& $plugin;
			$this->ngg_options = get_option( 'ngg_options' );

			if ( defined( 'NEXTGEN_GALLERY_PLUGIN_VERSION' ) && 
				NEXTGEN_GALLERY_PLUGIN_VERSION )
					$this->ngg_version = NEXTGEN_GALLERY_PLUGIN_VERSION;

			$this->p->util->add_plugin_filters( $this, array( 
				'content_image_preg_html_tag' => 1,	// add the 'a' for thumbnails
				'content_image_preg_pid_attr' => 1,	// add the 'data-image-pid' attribute
				'get_content_a_data_image_id' => 4,	// hook the <a data-image-id="#"> value
				'get_content_img_data_image_id' => 4,	// hook the <img data-image-id="#"> value
				'ngg_accept_img_dims' => 6,
			) );
		}

		public function filter_content_image_preg_html_tag( $html_tag ) {
			return $html_tag.'|a';
		}

		public function filter_content_image_preg_pid_attr( $pid_attr ) {
			return $pid_attr.'|data-image-id';
		}

		public function filter_get_content_a_data_image_id( $og_ret, $pid, $size_name, $check_dupes ) {
			return $this->get_image_src( 'ngg-'.$pid, $size_name, $check_dupes );
		}

		public function filter_get_content_img_data_image_id( $og_ret, $pid, $size_name, $check_dupes ) {
			return $this->get_image_src( 'ngg-'.$pid, $size_name, $check_dupes );
		}

		// called to get an image url from an ngg picture id and a media size name (the pid must be formatted as 'ngg-#')
		public function get_image_src( $pid, $size_name = 'thumbnail', $check_dupes = true ) {

			if ( $this->p->debug->enabled ) {
				$this->p->debug->log_args( array(
					'pid' => $pid,
					'size_name' => $size_name,
					'check_dupes' => $check_dupes,
				) );
			}

			if ( $this->p->is_avail['media']['ngg'] !== true || strpos( $pid, 'ngg-' ) !== 0 )
				return array( null, null, null, null, null );

			$size_info = SucomUtil::get_size_info( $size_name );
			$pid = substr( $pid, 4 );
			$img_url = '';
			$img_width = -1;
			$img_height = -1;
			$img_cropped = empty( $size_info['crop'] ) ? 0 : 1;	// get_size_info() returns false, true, or an array
			$ret_empty = array( null, null, null, null, null );

			if ( version_compare( $this->ngg_version, '2.0.0', '<' ) ) {

				global $nggdb;
				$image = $nggdb->find_image( $pid );	// returns an nggImage object

				if ( ! empty( $image ) ) {
					$crop_arg = $img_cropped ? 'crop' : '';
					$img_url = $image->cached_singlepic_file( $size_info['width'], $size_info['height'], $crop_arg ); 
					// if the image file doesn't exist, use the dynamic image url
					if ( empty( $img_url ) ) {
						$img_url = trailingslashit( site_url() ).
							'index.php?callback=image&amp;pid='.$pid.
							'&amp;width='.$size_info['width'].
							'&amp;height='.$size_info['height'].
							'&amp;mode='.$crop_arg;
						$img_width = $size_info['width'];
						$img_height = $size_info['height'];
					} else {
						// get the real image width and height (for ngg pre-v2.0)
						$cachename = $image->pid.'_'.$crop_arg.'_'. $size_info['width'].'x'.$size_info['height'].'_'.$image->filename;
						$cachefolder = WINABSPATH.$this->ngg_options['gallerypath'].'cache/';
						$cached_file = $cachefolder.$cachename;
						if ( file_exists( $cached_file ) ) {
							$file_info = @getimagesize( $cached_file );
							if ( ! empty( $file_info[0] ) && ! empty( $file_info[1] ) ) {
								$img_width = $file_info[0];
								$img_height = $file_info[1];
							}
						} elseif ( $this->p->debug->enabled )
							$this->p->debug->log( $cached_file.' not found' );
					}
				}

			} else {	// NGG version 2.0.0+

				$mapper = C_Image_Mapper::get_instance();
				$image = $mapper->find( $pid );
				$storage = C_Gallery_Storage::get_instance();
				$dynthumbs = C_Dynamic_Thumbnails_Manager::get_instance();
				$img_meta = $image->meta_data;

				// protect against missing array or array elements
				$full_width = empty( $img_meta['full']['width'] ) ? 0 : $img_meta['full']['width'];
				$full_height = empty( $img_meta['full']['height'] ) ? 0 : $img_meta['full']['height'];

				// check to see that the full size image is large enough for our requirements
				$is_sufficient_w = $full_width >= $size_info['width'] ? true : false;
				$is_sufficient_h = $full_height >= $size_info['height'] ? true : false;

				if ( empty( $full_width ) || empty( $full_height ) ) {
					if ( $this->p->debug->enabled )
						$this->p->debug->log( 'ngg image meta_data missing full width and/or height elements' );

				// if the full size is too small, get the full size image URL instead
				} elseif ( ( ! $img_cropped && ( ! $is_sufficient_w && ! $is_sufficient_h ) ) ||
					( $img_cropped && ( ! $is_sufficient_w || ! $is_sufficient_h ) ) ) {

					if ( $this->p->debug->enabled )
						$this->p->debug->log( 'full meta sizes '.$full_width.'x'.$full_height.' smaller than '.
							$size_name.' ('.$size_info['width'].'x'.$size_info['height'].
							( $img_cropped ? ' cropped' : '' ).') - fetching full size image url instead' );

					$img_url = $storage->get_image_url( $image, 'full' );
					$img_width = $full_width;
					$img_height = $full_height;

				} else {

					$named_size = $dynthumbs->get_image_name( $image, array(
						'width' => $size_info['width'], 
						'height' => $size_info['height'], 
						'crop' => $size_info['crop']
					) );

					$img_url = $storage->get_image_url( $image, $named_size );

					// determine "accurate" sizes using a ratio
					if ( $img_cropped ) {
						$img_width = $size_info['width'];
						$img_height = $size_info['height'];
					} else {
						$ratio = $full_width / $size_info['width'];
						$img_width = $size_info['width'];
						$img_height = (int) round( $full_height / $ratio );
					}
				}
			}

			if ( empty( $img_url ) ) {
				if ( $this->p->debug->enabled )
					$this->p->debug->log( 'exiting early: returned img_url is empty' );
				return $ret_empty;
			}

			// check if image exceeds hard-coded limits (dimensions, ratio, etc.)
			$img_size_within_limits = $this->p->media->img_size_within_limits( $pid, $size_name, $img_width, $img_height, 
				__( 'NextGEN Gallery', 'wpsso' ) );

			if ( apply_filters( $this->p->cf['lca'].'_ngg_accept_img_dims', $img_size_within_limits,
				$img_url, $img_width, $img_height, $size_name, $pid ) ) {

				if ( ! $check_dupes || $this->p->util->is_uniq_url( $img_url, $size_name ) ) {

					if ( $this->p->debug->enabled )
						$this->p->debug->log( 'applying rewrite_url filter for '.$img_url );

					return array( apply_filters( $this->p->cf['lca'].'_rewrite_url', $img_url ), 
						$img_width, $img_height, $img_cropped, 'ngg-'.$pid );
				}
			}

			return $ret_empty;
		}

		public function filter_ngg_accept_img_dims( $bool, $img_url, $img_width, $img_height, $size_name, $pid ) {

			if ( empty( $this->p->options['plugin_check_img_dims'] ) )
				return $bool;

			if ( ! $bool )	// don't recheck already rejected images
				return false;

			$size_info = SucomUtil::get_size_info( $size_name );
			$is_cropped = empty( $size_info['crop'] ) ? false : true;	// get_size_info() returns false, true, or an array
			$is_sufficient_w = $img_width >= $size_info['width'] ? true : false;
			$is_sufficient_h = $img_height >= $size_info['height'] ? true : false;

			if ( ( ! $is_cropped && ( ! $is_sufficient_w && ! $is_sufficient_h ) ) ||
				( $is_cropped && ( ! $is_sufficient_w || ! $is_sufficient_h ) ) ) {

				if ( $this->p->debug->enabled )
					$this->p->debug->log( 'exiting early: image ID '.$pid.' rejected - '.$img_width.'x'.$img_height.' too small for the '.
						$size_name.' ('.$size_info['width'].'x'.$size_info['height'].( $img_cropped ? ' cropped' : '' ).') image size' );

				if ( $this->p->notice->is_admin_pre_notices() ) {	// skip if notices already shown
					$media_lib = __( 'NextGEN Gallery', 'wpsso' );
					$size_label = $this->p->util->get_image_size_label( $size_name );
					$dismiss_id = 'ngg_'.$pid.'_'.$img_width.'x'.$img_height.'_'.$size_name.'_'.$size_info['width'].'x'.$size_info['height'].'_rejected';
					$required_text = '<b>'.$size_label.'</b> ('.$size_info['width'].'x'.$size_info['height'].
						( $img_cropped ? ' <i>'.__( 'cropped', 'wpsso' ).'</i>' : '' ).')';
					$reject_notice = $this->p->msgs->get( 'notice-image-rejected', array( 'size_label' => $size_label ) );
					$this->p->notice->warn( sprintf( __( '%1$s image ID %2$s ignored &mdash; the resulting image of %3$s is too small for the required %4$s image dimensions.', 'wpsso' ), $media_lib, $pid, $img_width.'x'.$img_height, $required_text ).' '.$reject_notice, true, $dismiss_id, true );
				}

				return  false;	// exit early
			}

			if ( $this->p->debug->enabled )
				$this->p->debug->log( 'image ID '.$pid.' dimensions ('.$img_width.'x'.$img_height.') are sufficient' );

			return true;	// image dimensions are good
		}

		// parse ngg pre-v2 query arguments
		public function get_query_images( $num = 0, $size_name = 'thumbnail', $post_id, $check_dupes = true ) {

			if ( $this->p->debug->enabled ) {
				$this->p->debug->log_args( array( 
					'num' => $num,
					'size_name' => $size_name,
					'post_id' => $post_id,
					'check_dupes' => $check_dupes,
				) );
			}

			// exit if ngg is not active, or version is 2.0.0 or greater
			if ( $this->p->is_avail['media']['ngg'] !== true || 
				version_compare( $this->ngg_version, '2.0.0', '>=' ) )
					return array();

			global $wp_query, $nggdb;
			$og_ret = array();
			$og_image = SucomUtil::get_mt_prop_image( 'og' );

			if ( ( $post_obj = SucomUtil::get_post_object( $post_id ) ) === false || empty( $post_obj->post_type ) ) {
				if ( $this->p->debug->enabled )
					$this->p->debug->log( 'exiting early: object without post type' );
				return $og_ret; 
			} elseif ( empty( $post_obj->post_content ) ) { 
				if ( $this->p->debug->enabled )
					$this->p->debug->log( 'exiting early: post without content' ); 
				return $og_ret; 
			}

			// sanitize possible query values
			$ngg_album = empty( $wp_query->query['album'] ) ?
				'' : preg_replace( '/[^0-9]/', '', $wp_query->query['album'] );
			$ngg_gallery = empty( $wp_query->query['gallery'] ) ?
				'' : preg_replace( '/[^0-9]/', '', $wp_query->query['gallery'] );
			$ngg_pageid = empty( $wp_query->query['pageid'] ) ?
				'' : preg_replace( '/[^0-9]/', '', $wp_query->query['pageid'] );
			$ngg_pid = empty( $wp_query->query['pid'] ) ?
				'' : preg_replace( '/[^0-9]/', '', $wp_query->query['pid'] );

			if ( empty( $ngg_album ) && empty( $ngg_gallery ) && empty( $ngg_pid ) ) {
				if ( $this->p->debug->enabled )
					$this->p->debug->log( 'exiting early: no ngg query values' ); 
				return $og_ret;
			} else {
				if ( $this->p->debug->enabled )
					$this->p->debug->log( 'ngg query found = pageid:'.$ngg_pageid.' album:'.$ngg_album.
						' gallery:'.$ngg_gallery.' pid:'.$ngg_pid );
			}

			if ( preg_match( '/\[(nggalbum|album|nggallery)(| [^\]]*id=[\'"]*([0-9]+)[\'"]*[^\]]*| [^\]]*)\]/im',
				$post_obj->post_content, $all_matches ) ) {

				$shortcode_type = $all_matches[1];
				$shortcode_id = ! empty( $all_matches[3] ) ? $all_matches[3] : 0;
				if ( $this->p->debug->enabled )
					$this->p->debug->log( 'ngg query with ['.$shortcode_type.'] shortcode (id:'.$shortcode_id.')' );

				// always trust hard-coded shortcode ID more than query arguments
				$ngg_album = $shortcode_type == 'nggalbum' || $shortcode_type == 'album' ? $shortcode_id : $ngg_album;
				$ngg_gallery = $shortcode_type == 'nggallery' ? $shortcode_id : $ngg_gallery;

				// security checks
				if ( $ngg_gallery > 0 && $ngg_album > 0 ) {
					$nggAlbum = $nggdb->find_album( $ngg_album );
					if ( in_array( $ngg_gallery, $nggAlbum->gallery_ids, true ) ) {
						if ( $this->p->debug->enabled )
							$this->p->debug->log( 'security check passed = gallery:'.
								$ngg_gallery.' is in album:'.$ngg_album );
					} else {
						if ( $this->p->debug->enabled )
							$this->p->debug->log( 'security check failed = gallery:'.
								$ngg_gallery.' is not in album:'.$ngg_album );
						return $og_ret;
					}
				}
				if ( $ngg_pid > 0 && $ngg_gallery > 0 ) {
					$pids = $nggdb->get_ids_from_gallery( $ngg_gallery );
					if ( in_array( $ngg_pid, $pids, true ) ) {
						if ( $this->p->debug->enabled )
							$this->p->debug->log( 'security check passed = pid:'.
								$ngg_pid.' is in gallery:'.$ngg_gallery );
					} else {
						if ( $this->p->debug->enabled )
							$this->p->debug->log( 'security check failed = pid:'.
								$ngg_pid.' is not in gallery:'.$ngg_gallery );
						return $og_ret;
					}
				}
				if ( $ngg_pid > 0 ) {
					if ( $this->p->debug->enabled )
						$this->p->debug->log( 'getting image for ngg query pid:'.$ngg_pid );
					list(
						$og_image['og:image'],
						$og_image['og:image:width'],
						$og_image['og:image:height'],
						$og_image['og:image:cropped'],
						$og_image['og:image:id']
					) = $this->get_image_src( 'ngg-'.$ngg_pid, $size_name, $check_dupes );
					if ( ! empty( $og_image['og:image'] ) && 
						$this->p->util->push_max( $og_ret, $og_image, $num ) ) 
							return $og_ret;
				} elseif ( $ngg_gallery > 0 ) {
					$gallery = $nggdb->find_gallery( $ngg_gallery );
					if ( ! empty( $gallery ) ) {
						if ( ! empty( $gallery->previewpic ) ) {
							if ( $this->p->debug->enabled )
								$this->p->debug->log( 'getting previewpic:'.
									$gallery->previewpic.' for gallery:'.$ngg_gallery );
							list(
								$og_image['og:image'],
								$og_image['og:image:width'],
								$og_image['og:image:height'],
								$og_image['og:image:cropped'],
								$og_image['og:image:id']
							) = $this->get_image_src( 'ngg-'.$gallery->previewpic, $size_name, $check_dupes );
							if ( ! empty( $og_image['og:image'] ) && 
								$this->p->util->push_max( $og_ret, $og_image, $num ) ) 
									return $og_ret;
						} elseif ( $this->p->debug->enabled )
							$this->p->debug->log( 'no previewpic for gallery:'.$ngg_gallery );
					} elseif ( $this->p->debug->enabled )
						$this->p->debug->log( 'no gallery:'.$ngg_gallery.' found' );
				} elseif ( $ngg_album > 0 ) {
					$album = $nggfb->find_album( $ngg_album );
					if ( ! empty( $albums ) ) {
						if ( ! empty( $album->previewpic ) ) {
							if ( $this->p->debug->enabled )
								$this->p->debug->log( 'getting previewpic:'.
									$album->previewpic.' for album:'.$ngg_album );
							list(
								$og_image['og:image'],
								$og_image['og:image:width'],
								$og_image['og:image:height'],
								$og_image['og:image:cropped'],
								$og_image['og:image:id']
							) = $this->get_image_src( 'ngg-'.$album->previewpic, $size_name, $check_dupes );
							if ( ! empty( $og_image['og:image'] ) && 
								$this->p->util->push_max( $og_ret, $og_image, $num ) ) 
									return $og_ret;
						} elseif ( $this->p->debug->enabled )
							$this->p->debug->log( 'no previewpic for album:'.$ngg_album );
					} elseif ( $this->p->debug->enabled )
						$this->p->debug->log( 'no album:'.$ngg_album.' found' );
				}
			} elseif ( $this->p->debug->enabled )
				$this->p->debug->log( 'ngg query without [nggalbum|album|nggallery] shortcode' );

			$this->p->util->slice_max( $og_ret, $num );
			return $og_ret;
		}

		public function get_shortcode_images( $num = 0, $size_name = 'thumbnail', $post_id, $check_dupes = true ) {

			if ( $this->p->debug->enabled ) {
				$this->p->debug->log_args( array( 
					'num' => $num,
					'size_name' => $size_name,
					'post_id' => $post_id,
					'check_dupes' => $check_dupes,
				) );
			}

			if ( $this->p->is_avail['media']['ngg'] !== true ) 
				return array();

			global $wpdb;
			$og_ret = array();
			$og_image = SucomUtil::get_mt_prop_image( 'og' );

			if ( ( $post_obj = SucomUtil::get_post_object( $post_id ) ) === false || empty( $post_obj->post_type ) ) {
				if ( $this->p->debug->enabled )
					$this->p->debug->log( 'exiting early: object without post type' );
				return $og_ret; 
			} elseif ( empty( $post_obj->post_content ) ) { 
				if ( $this->p->debug->enabled )
					$this->p->debug->log( 'exiting early: post without content' ); 
				return $og_ret; 
			}

			if ( preg_match_all( '/\[(nggalbum|album)(| [^\]]*id=[\'"]*([0-9]+)[\'"]*[^\]]*| [^\]]*)\]/im',
				$post_obj->post_content, $all_matches, PREG_SET_ORDER ) ) {

				foreach ( $all_matches as $album ) {

					if ( empty( $album[3] ) ) {
						$ngg_album = 0;
						if ( $this->p->debug->enabled )
							$this->p->debug->log( 'album id zero or not found - setting album id to 0 (all)' );
					} else $ngg_album = $album[3];

					if ( $this->p->debug->enabled )
						$this->p->debug->log( '['.$album[1].'] shortcode found (id:'.$ngg_album.')' );

					if ( $ngg_album > 0 ) 
						$albums = $wpdb->get_results( 'SELECT * FROM '.$wpdb->nggalbum.' WHERE id IN (\''.$ngg_album.'\')', OBJECT_K );
					else $albums = $wpdb->get_results( 'SELECT * FROM '.$wpdb->nggalbum, OBJECT_K );

					if ( is_array( $albums ) ) {
						foreach ( $albums as $row ) {
							if ( ! empty( $row->previewpic ) ) {
								if ( $this->p->debug->enabled )
									$this->p->debug->log( 'getting previewpic:'.
										$row->previewpic.' for album:'.$row->id );
								list(
									$og_image['og:image'],
									$og_image['og:image:width'],
									$og_image['og:image:height'],
									$og_image['og:image:cropped'],
									$og_image['og:image:id']
								) = $this->get_image_src( 'ngg-'.$row->previewpic, $size_name, $check_dupes );
								if ( ! empty( $og_image['og:image'] ) && 
									$this->p->util->push_max( $og_ret, $og_image, $num ) ) 
										return $og_ret;
							} elseif ( $this->p->debug->enabled )
								$this->p->debug->log( 'no previewpic for album:'.$row->id );
						}
					} elseif ( $this->p->debug->enabled )
						$this->p->debug->log( 'no album(s) found' );
				}
			} elseif ( $this->p->debug->enabled )
				$this->p->debug->log( 'no [nggalbum|album] shortcode found' );

			if ( preg_match_all( '/\[(nggallery) [^\]]*id=[\'"]*([0-9]+)[\'"]*[^\]]*\]/im', 
				$post_obj->post_content, $all_matches, PREG_SET_ORDER ) ) {

				foreach ( $all_matches as $gallery ) {
					if ( $this->p->debug->enabled )
						$this->p->debug->log( '['.$gallery[1].'] shortcode found (id:'.$gallery[2].')' );
					$ngg_gallery = $gallery[2];
					$galleries = $wpdb->get_results( 'SELECT * FROM '.$wpdb->nggallery.' WHERE gid IN (\''.$ngg_gallery.'\')', OBJECT_K );
					if ( is_array( $galleries ) ) {
						foreach ( $galleries as $row ) {
							if ( ! empty( $row->previewpic ) ) {
								if ( $this->p->debug->enabled )
									$this->p->debug->log( 'getting previewpic:'.
										$row->previewpic.' for gallery:'.$row->gid );
								list(
									$og_image['og:image'],
									$og_image['og:image:width'],
									$og_image['og:image:height'],
									$og_image['og:image:cropped'],
									$og_image['og:image:id']
								) = $this->get_image_src( 'ngg-'.$row->previewpic, $size_name, $check_dupes );
								if ( ! empty( $og_image['og:image'] ) && 
									$this->p->util->push_max( $og_ret, $og_image, $num ) ) 
										return $og_ret;
							} elseif ( $this->p->debug->enabled )
								$this->p->debug->log( 'no previewpic for gallery:'.$row->gid );
						}
					} elseif ( $this->p->debug->enabled )
						$this->p->debug->log( 'no gallery:'.$ngg_gallery.' found' );
				}
			} elseif ( $this->p->debug->enabled )
				$this->p->debug->log( 'no [nggallery] shortcode found' );

			if ( preg_match_all( '/\[(singlepic) [^\]]*id=[\'"]*([0-9]+)[\'"]*[^\]]*\]/im', 
				$post_obj->post_content, $all_matches, PREG_SET_ORDER ) ) {

				foreach ( $all_matches as $singlepic ) {
					if ( $this->p->debug->enabled )
						$this->p->debug->log( '['.$singlepic[1].'] shortcode found (id:'.$singlepic[2].')' );
					$pid = $singlepic[2];
					if ( $this->p->debug->enabled )
						$this->p->debug->log( 'getting image for singlepic:'.$pid );
					list(
						$og_image['og:image'],
						$og_image['og:image:width'],
						$og_image['og:image:height'],
						$og_image['og:image:cropped'],
						$og_image['og:image:id']
					) = $this->get_image_src( 'ngg-'.$pid, $size_name, $check_dupes );
					if ( ! empty( $og_image['og:image'] ) && 
						$this->p->util->push_max( $og_ret, $og_image, $num ) ) 
							return $og_ret;
				}
			} elseif ( $this->p->debug->enabled )
				$this->p->debug->log( 'no [singlepic] shortcode found' );

			$this->p->util->slice_max( $og_ret, $num );
			return $og_ret;
		}

		public function get_singlepic_images( $num = 0, $size_name = 'thumbnail', $post_id, $check_dupes = false ) {

			if ( $this->p->debug->enabled ) {
				$this->p->debug->log_args( array( 
					'num' => $num,
					'size_name' => $size_name,
					'post_id' => $post_id,
					'check_dupes' => $check_dupes,
				) );
			}

			if ( $this->p->is_avail['media']['ngg'] !== true ) 
				return array();

			$og_ret = array();
			$og_image = SucomUtil::get_mt_prop_image( 'og' );

			if ( ( $post_obj = SucomUtil::get_post_object( $post_id ) ) === false || empty( $post_obj->post_type ) ) {
				if ( $this->p->debug->enabled )
					$this->p->debug->log( 'exiting early: object without post type' );
				return $og_ret; 
			} elseif ( empty( $post_obj->post_content ) ) { 
				if ( $this->p->debug->enabled )
					$this->p->debug->log( 'exiting early: post without content' ); 
				return $og_ret; 
			}

			if ( preg_match_all( '/\[(singlepic) [^\]]*id=[\'"]*([0-9]+)[\'"]*[^\]]*\]/im', 
				$post_obj->post_content, $all_matches, PREG_SET_ORDER ) ) {

				foreach ( $all_matches as $singlepic ) {
					if ( $this->p->debug->enabled )
						$this->p->debug->log( '['.$singlepic[1].'] shortcode found (id:'.$singlepic[2].')' );
					$pid = $singlepic[2];
					if ( $this->p->debug->enabled )
						$this->p->debug->log( 'getting image for singlepic:'.$pid );
					list(
						$og_image['og:image'],
						$og_image['og:image:width'],
						$og_image['og:image:height'],
						$og_image['og:image:cropped'],
						$og_image['og:image:id']
					) = $this->get_image_src( 'ngg-'.$pid, $size_name, $check_dupes );
					if ( ! empty( $og_image['og:image'] ) && 
						$this->p->util->push_max( $og_ret, $og_image, $num ) ) 
							return $og_ret;
				}
			} elseif ( $this->p->debug->enabled )
				$this->p->debug->log( 'no [singlepic] shortcode found' );

			$this->p->util->slice_max( $og_ret, $num );
			return $og_ret;
		}

		// called from the view/gallery-meta.php template
		public function get_from_images( $num = 0, $size_name = 'thumbnail', $ngg_images = array() ) {

			if ( $this->p->is_avail['media']['ngg'] !== true ) 
				return array();

			$og_ret = array();
			$og_image = SucomUtil::get_mt_prop_image( 'og' );

			if ( is_array( $ngg_images ) ) {
				foreach ( $ngg_images as $image ) {
					if ( ! empty( $image->pid ) ) {
						list(
							$og_image['og:image'],
							$og_image['og:image:width'],
							$og_image['og:image:height'],
							$og_image['og:image:cropped'],
							$og_image['og:image:id']
						) = $this->get_image_src( 'ngg-'.$image->pid, $size_name );
						if ( ! empty( $og_image['og:image'] ) && 
							$this->p->util->push_max( $og_ret, $og_image, $num ) )
								return $og_ret;
					}
				}
			}
			return $og_ret;
		}

		// called from the view/gallery-meta.php template
		public function get_tags( $pid ) {
			$tags = apply_filters( $this->p->cf['lca'].'_ngg_tags_seed', array(), $pid );
			if ( ! empty( $tags ) ) {
				if ( $this->p->debug->enabled )
					$this->p->debug->log( 'ngg tags seed = "'.implode( ',', $tags ).'"' );
			} else {
				if ( $this->p->is_avail['media']['ngg'] == true && is_string( $pid ) && substr( $pid, 0, 4 ) == 'ngg-' )
					$tags = wp_get_object_terms( substr( $pid, 4 ), 'ngg_tag', 'fields=names' );
				$tags = array_map( array( 'SucomUtil', 'sanitize_tag' ), $tags );
			}
			return apply_filters( $this->p->cf['lca'].'_ngg_tags', $tags, $pid );
		}
	}
}

?>
