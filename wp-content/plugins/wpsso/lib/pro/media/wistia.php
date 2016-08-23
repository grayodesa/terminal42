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

if ( ! class_exists( 'WpssoProMediaWistia' ) ) {

	class WpssoProMediaWistia {

		private $p;

		public function __construct( &$plugin ) {
			$this->p =& $plugin;
			$this->p->util->add_plugin_filters( $this, array( 
				'content_videos' => 2, 
				'video_info' => 4,
			), 30 );
		}

		// receives an array of arrays
		public function filter_content_videos( $videos = false, $content = '' ) {
			if ( $this->p->debug->enabled )
				$this->p->debug->mark();
			/*
			 * examples:
			 *	<div id="wistia_wb36s0vwcg" class="wistia_embed" style="width:640px;height:360px;">&nbsp;</div>
			 * 	<div class="wistia_embed wistia_async_j38ihh83m5" style="height:349px;width:620px">
			 */
			if ( preg_match_all( '/<div[^<>]*? (id=[\'"]wistia_([^\'"<> ]+)[\'"][^<>]* class=[\'"]wistia_embed[\'"]|'.
				'class=[\'"]wistia_embed wistia_async_([^\'"<> ]+)[^\'"]*[\'"])[^<>]*>/i', $content, 
					$all_matches, PREG_SET_ORDER )  ) {

				// we receive a false if we're the first filter in line
				if ( ! is_array( $videos ) )
					$videos = array();

				if ( $this->p->debug->enabled )
					$this->p->debug->log( count( $all_matches ).' x video <div/> wistia_embed found' );

				foreach ( $all_matches as $media ) {
					$url = 'http://fast.wistia.net/embed/iframe/'.
						( empty( $match[2] ) ? $media[3] : $match[2] );
					$width = preg_match( '/width:([0-9]+)px/i', $media[0], $match) ? $match[1] : -1;
					$height = preg_match( '/height:([0-9]+)px/i', $media[0], $match) ? $match[1] : -1;
					if ( $this->p->debug->enabled )
						$this->p->debug->log( 'found: '.$url.' ('.$width.'x'.$height.')' );
					$videos[] = array( $url, $width, $height );	// return an array of arrays
				}
			} elseif ( $this->p->debug->enabled )
				$this->p->debug->log( 'no <div/> wistia_embed html tag(s) found' );

			/*
			 * example: <a href="//fast.wistia.net/embed/iframe/wb36s0vwcg?popover=true" class="wistia-popover[height=360,playerColor=7b796a,width=640]">
			 */
			if ( preg_match_all( '/<a[^<>]*? href=[\'"]([^\'"]+)[\'"][^<>]* class=[\'"]wistia-popover\[([^\]]+)\][\'"][^<>]*>/i',
				$content, $all_matches, PREG_SET_ORDER )  ) {

				// we receive a false if we're the first filter in line
				if ( ! is_array( $videos ) )
					$videos = array();

				if ( $this->p->debug->enabled )
					$this->p->debug->log( count( $all_matches ).' x video <a/> wistia-popover found' );

				foreach ( $all_matches as $media ) {
					$url = $media[1];
					$width = preg_match( '/width=([0-9]+)/i', $media[2], $match) ? $match[1] : -1;
					$height = preg_match( '/height=([0-9]+)/i', $media[2], $match) ? $match[1] : -1;
					$this->p->debug->log( 'found: '.$url.' ('.$width.'x'.$height.')' );
					$videos[] = array( $url, $width, $height );	// return an array of arrays
				}
			} elseif ( $this->p->debug->enabled )
				$this->p->debug->log( 'no <a/> wistia-popover html tag(s) found' );

			return $videos;
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
			 * Wistia video API
			 */
			if ( preg_match( '/^.*(wistia\.net|wistia\.com|wi\.st)\/([^\?\&\#<>]+).*$/i', $embed_url, $match ) ) {

				$vid_name = preg_replace( '/^.*\//', '', $match[2] );

				$api_url = ( empty( $this->p->options['og_vid_https'] ) ? 'http:' : 'https:' ).
					'//fast.wistia.com/oembed.xml?url=http%3A//home.wistia.com/medias/'.
						$vid_name.'%3FembedType=seo';	// embedType can be "seo" or "twitter_card_tags"

				if ( function_exists( 'simplexml_load_string' ) ) {

					$xml = @simplexml_load_string( $this->p->cache->get( $api_url, 'raw', 'transient' ) );

					if ( ! empty( $xml->title ) )
						$og_video['og:video:title'] = (string) $xml->title;

					if ( ! empty( $xml->html ) ) {

						$metas = $this->p->util->get_head_meta( (string) $xml->html, '//meta|//noscript' );

						if ( isset( $metas['meta'] ) ) {
							foreach ( $metas as $m ) {		// loop through all meta tags
								foreach ( $m as $a ) {		// loop through all attributes for that meta tag
									$meta_name = key( $a );
									$meta_value = reset( $a );
									switch ( $meta_name.'-'.$meta_value ) {
										case 'itemprop-description':
											if ( ! empty( $a['textContent'] ) )
												$og_video['og:video:description'] = $a['textContent'];
											break;
										case 'itemprop-duration':
											if ( ! empty( $a['content'] ) )
												$og_video['og:video:duration'] = $a['content'];
											break;
										case 'itemprop-embedUrl':
										case 'itemprop-embedURL':
											if ( ! empty( $a['content'] ) ) {
												if ( ! empty( $this->p->options['og_vid_autoplay'] ) ) {
													if ( strpos( $a['content'], 'autoPlay=false' ) !== false ) {
														$a['content'] = preg_replace( '/autoPlay=false/',
															'autoPlay=true', $a['content'] );
													}
												}
												$og_video['og:video:secure_url'] = preg_replace( '/^http:\/\/embed\./',
													'https://embed-ssl.', $a['content'] );

												$og_video['og:video:url'] = preg_replace( '/^https:\/\/embed-ssl\./',
													'http://embed.', $a['content'] );
											}
											break;
										case 'itemprop-thumbnailUrl':
										case 'itemprop-thumbnailURL':
											if ( ! empty( $a['content'] ) )
												$og_video['og:video:thumbnail_url'] = $a['content'];
											break;
										case 'itemprop-uploadDate':
											if ( ! empty( $a['content'] ) )
												$og_video['og:video:upload_date'] = $a['content'];
											break;
									}
								}
							}
						}

						$og_video['og:video:embed_url'] = 'https://fast.wistia.net/embed/iframe/'.
							$vid_name.'?plugin[socialbar-v1][on]=false&twitter=true';

					} elseif ( $this->p->debug->enabled )
						$this->p->debug->log( 'html missing from returned xml' );

					if ( ! empty( $xml->thumbnail_url ) ) {

						$img_url = (string) $xml->thumbnail_url;
						$img_width = (string) $xml->thumbnail_width;
						$img_height = (string) $xml->thumbnail_height;

						$og_video['og:video:width'] = $img_width;
						$og_video['og:video:height'] = $img_height;
						$og_video['og:video:has_image'] = true;

						$og_video['og:image:secure_url'] = preg_replace( '/^http:\/\/embed\./', 'https://embed-ssl.', $img_url );
						$og_video['og:image'] = preg_replace( '/^https:\/\/embed-ssl\./', 'http://embed.', $img_url );
						$og_video['og:image:width'] = $img_width;
						$og_video['og:image:height'] = $img_height;

					} elseif ( $this->p->debug->enabled )
						$this->p->debug->log( 'thumbnail_url missing from returned xml' );

				} elseif ( $this->p->debug->enabled )
					$this->p->debug->log( 'simplexml_load_string function is missing' );

				if ( $this->p->debug->enabled )
					$this->p->debug->log( $og_video );
			}

			return $og_video;
		}
	}
}

?>
