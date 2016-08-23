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

if ( ! class_exists( 'WpssoProMediaFacebook' ) ) {

	class WpssoProMediaFacebook {

		private $p;

		public function __construct( &$plugin ) {
			$this->p =& $plugin;
			$this->p->util->add_plugin_filters( $this, array( 
				'video_info' => 4,
			), 60 );
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
			} elseif ( strpos( $embed_url, 'facebook.com' ) === false )
				return $og_video;

			/*
			 * Facebook video API
			 */
			if ( preg_match( '/^.*(facebook\.com)\/plugins\/video.php\?href=([^\/\?\&\#<>]+).*$/', $embed_url, $match ) ) {

				$embed_url = $match[0];
				$video_url = urldecode( $match[2] );

				if ( ( $video_html = $this->p->cache->get( $embed_url, 'raw', 'transient' ) ) === false ) {
					if ( $this->p->debug->enabled )
						$this->p->debug->log( 'exiting early: error caching '.$embed_url );
					return $og_video;
				}

				if ( preg_match( '/"(hd|sd)_src_no_ratelimit":"([^"]+)"/', $video_html, $match ) ) {
					if ( $this->p->debug->enabled )
						$this->p->debug->log( 'decoding mp4 url = '.$match[2] );
					$mp4_url = str_replace( '\/', '/', SucomUtil::replace_unicode_escape( $match[2] ) );

					if ( strpos( $mp4_url, 'https:' ) === 0 )
						$og_video['og:video:secure_url'] = $mp4_url;
					$og_video['og:video:url'] = preg_replace( '/^https:/', 'http:', $mp4_url );
					$og_video['og:video:type'] = 'video/mp4';
					$og_video['og:video:embed_url'] = $embed_url;
				} else {
					if ( $this->p->debug->enabled )
						$this->p->debug->log( 'exiting early: hd|sd_src_no_ratelimit not in '.$embed_url );
					return $og_video;
				}
			
				// check for preview image
				if ( preg_match( '/<img .* src="([^"]+\.jpg[^"]*)"/', $video_html, $match ) ) {
					$img_url = html_entity_decode( $match[1] );

					$og_video['og:video:thumbnail_url'] = $img_url;
					$og_video['og:video:has_image'] = true;

					if ( strpos( $mp4_url, 'https:' ) === 0 )
						$og_video['og:image:secure_url'] = $img_url;
					$og_video['og:image'] = preg_replace( '/^https:/', 'http:', $img_url );
				}

				// check for video title
				if ( preg_match( '/<a .* href="'.str_replace( '/', '\/', $video_url ).'"[^>]*>'.
					'<span [^>]+>([^<]+)<\/span><\/a>/', $video_html, $match ) ) {
					$og_video['og:video:title'] = html_entity_decode( SucomUtil::decode_utf8( $match[1] ) );
				}

				if ( $this->p->debug->enabled )
					$this->p->debug->log( $og_video );
			}

			return $og_video;
		}
	}
}

?>
