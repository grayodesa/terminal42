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

if ( ! class_exists( 'WpssoProMediaYoutube' ) ) {

	class WpssoProMediaYoutube {

		private $p;

		public function __construct( &$plugin ) {
			$this->p =& $plugin;
			$this->p->util->add_plugin_filters( $this, array( 
				'video_info' => 4,
			), 10 );
		}

		public function filter_video_info( $og_video, $embed_url, $embed_width = 0, $embed_height = 0 ) {
			if ( $this->p->debug->enabled )
				$this->p->debug->mark();

			// if there's already a video defined (youtube or vimeo, for example), then go with that
			if ( empty( $embed_url ) ||
				! empty( $og_video['og:video:secure_url'] ) || 
					! empty( $og_video['og:video:url'] ) ) {
						if ( $this->p->debug->enabled )
							$this->p->debug->log( 'exiting early: previous video information found' );
						return $og_video;
			}

			/*
			 * YouTube video API
			 */
			if ( preg_match( '/^.*(youtube\.com|youtube-nocookie\.com|youtu\.be)\/(watch\?v=)?([^\?\&\#<>]+)(\?(list)=([^\?\&\#<>]+)|.*)$/',
				$embed_url, $match ) ) {

				$vid_name = false;
				$list_name = false;

				if ( ! empty( $match[6] ) && $match[5] === 'list' ) {
					$list_name = $match[6];
					if ( $match[3] !== 'videoseries' ) {
						$og_video['og:video:secure_url'] = 'https://www.youtube.com/v/'.$vid_name.'?list='.$list_name;
						$og_video['og:video:url'] = 'http://www.youtube.com/v/'.$vid_name.'?list='.$list_name;
						$og_video['og:video:thumbnail_url'] = 'https://i.ytimg.com/vi/'.$match[3].'/0.jpg';
						$og_video['og:video:embed_url'] = 'https://www.youtube.com/embed/'.$vid_name.'?list='.$list_name;
						$og_video['og:video:has_image'] = true;
						$og_video['og:image:secure_url'] = 'https://i.ytimg.com/vi/'.$match[3].'/0.jpg';
						$og_video['og:image'] = 'http://i.ytimg.com/vi/'.$match[3].'/0.jpg';
					}
				} elseif ( ! empty( $match[3] ) ) {
					$vid_name = preg_replace( '/^.*\//', '', $match[3] );
					$og_video['og:video:secure_url'] = 'https://www.youtube.com/v/'.$vid_name.'?version=3&autohide=1';
					$og_video['og:video:url'] = 'http://www.youtube.com/v/'.$vid_name.'?version=3&autohide=1';
					$og_video['og:video:thumbnail_url'] = 'https://i.ytimg.com/vi/'.$vid_name.'/0.jpg';
					$og_video['og:video:embed_url'] = 'https://www.youtube.com/embed/'.$vid_name;
					$og_video['og:video:has_image'] = true;
					$og_video['og:image:secure_url'] = 'https://i.ytimg.com/vi/'.$vid_name.'/0.jpg';
					$og_video['og:image'] = 'http://i.ytimg.com/vi/'.$vid_name.'/0.jpg';
				}

				if ( $vid_name !== false && 
					! empty( $og_video['og:video:url'] ) ) {

					$url = ( empty( $this->p->options['og_vid_https'] ) ? 'http:' : 'https:' ).
						'//www.youtube.com/watch?v='.$vid_name;

					if ( $this->p->debug->enabled )
						$this->p->debug->log( 'fetching missing video width / height from '.$url );

					$metas = $this->p->util->get_head_meta( $url, '//meta' );

					if ( isset( $metas['meta'] ) ) {
						foreach ( $metas as $m ) {		// loop through all meta tags
							foreach ( $m as $a ) {		// loop through all attributes for that meta tag
								$meta_name = key( $a );
								$meta_value = reset( $a );
								switch ( $meta_name.'-'.$meta_value ) {
									case 'property-og:image':
										if ( ! empty( $a['content'] ) ) {
											if ( strpos( $a['content'], 'https:' ) === 0 ) {
												$og_video[$a['property'].':secure_url'] = $a['content'];
												$og_video[$a['property']] = preg_replace( '/^https:/',
													'http:', $a['content'] );
											} else $og_video[$a['property']] = $a['content'];
											$og_video['og:video:thumbnail_url'] = $a['content'];
										}
										break;
									case 'property-og:image:secure_url':
										if ( ! empty( $a['content'] ) ) {
											$og_video[$a['property']] = $a['content'];
											$og_video['og:video:thumbnail_url'] = $a['content'];
										}
										break;
									case 'property-og:video:width':
									case 'property-og:video:height':
										if ( ! empty( $a['content'] ) )
											$og_video[$a['property']] = $a['content'];
										break;
									case 'property-og:video:tag':
										if ( ! empty( $a['content'] ) )
											$og_video[$a['property']][] = $a['content'];	// array of tags
										break;
									case 'property-og:title':
									case 'property-og:description':
										// add additional, non-standard properties
										// like og:video:title and og:video:description
										if ( ! empty( $a['content'] ) ) {
											$og_key = 'og:video:'.substr( $a['property'], 3 );
											$og_video[$og_key] = $a['content'];
											if ( $this->p->debug->enabled )
												$this->p->debug->log( 'adding '.$og_key.' = '.
													$og_video[$og_key] );
										}
										break;
									case 'itemprop-datePublished':
										if ( ! empty( $a['content'] ) )
											$og_video['og:video:upload_date'] = date( 'c', strtotime( $a['content'] ) );
										break;
								}
							}
						}
					}
				}

				if ( $this->p->debug->enabled )
					$this->p->debug->log( $og_video );
			}
			return $og_video;
		}
	}
}

?>
