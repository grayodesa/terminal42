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

if ( ! class_exists( 'WpssoProMediaSlideshare' ) ) {

	class WpssoProMediaSlideshare {

		private $p;

		public function __construct( &$plugin ) {
			$this->p =& $plugin;
			$this->p->util->add_plugin_filters( $this, array( 
				'content_videos' => 2, 
				'video_info' => 4,
			), 40 );
		}

		// receives an array of arrays
		public function filter_content_videos( $videos = false, $content ) {
			/*
			 * example: <object type='application/x-shockwave-flash' wmode='opaque' 
			 *	data='http://static.slideshare.net/swf/ssplayer2.swf?id=29776875&doc=album-design-part-3-visuals-140107132112-phpapp01' 
			 *	width='650' height='533'>
			 */
			if ( preg_match_all( '/<object[^<>]*? data=[\'"]([^\'"<>]+\.slideshare.net\/swf[^\'"]+)[\'"][^<>]*>/i',
				$content, $all_matches, PREG_SET_ORDER )  ) {

				// we receive a false if we're the first filter in line
				if ( ! is_array( $videos ) ) 
					$videos = array();

				if ( $this->p->debug->enabled )
					$this->p->debug->log( count( $all_matches ).' x video <object/> slideshare found' );

				foreach ( $all_matches as $media ) {
					$url = $media[1];
					$width = preg_match( '/width=[\'"]([0-9]+)[\'"]/i', $media[0], $match) ? $match[1] : -1;
					$height = preg_match( '/height=[\'"]([0-9]+)[\'"]/i', $media[0], $match) ? $match[1] : -1;

					if ( $this->p->debug->enabled )
						$this->p->debug->log( 'found: '.$url.' ('.$width.'x'.$height.')' );

					$videos[] = array( $url, $width, $height );	// return an array of arrays
				}
			} elseif ( $this->p->debug->enabled )
				$this->p->debug->log( 'no <object/> slideshare html tag(s) found' );

			return $videos;
		}

		public function filter_video_info( $og_video, $embed_url, $embed_width = 0, $embed_height = 0 ) {

			// if there's already a video defined (youtube or vimeo, for example), then go with that
			if ( empty( $embed_url ) ||
				! empty( $og_video['og:video:secure_url'] ) || 
					! empty( $og_video['og:video:url'] ) ) {
						if ( $this->p->debug->enabled )
							$this->p->debug->log( 'exiting early: previous video information found' );
						return $og_video;
			}

			/*
			 * Slideshare API
			 */
			// this matches both the iframe and object urls
			if ( preg_match( '/^.*(slideshare\.net)\/.*(\/([0-9]+)|\?id=([0-9]+).*)$/i', $embed_url, $match ) ) {

				$vid_name = $match[3] ? $match[3] : $match[4];
				$og_video['og:video:embed_url'] = 'https://www.slideshare.net/slideshow/embed_code/'.$vid_name;

				$api_url = ( empty( $this->p->options['og_vid_https'] ) ? 'http:' : 'https:' ).
					'//www.slideshare.net/api/oembed/2?url='.$og_video['og:video:embed_url'].'&format=xml';

				if ( function_exists( 'simplexml_load_string' ) ) {

					$xml = @simplexml_load_string( $this->p->cache->get( $api_url, 'raw', 'transient' ) );

					if ( ! empty( $xml->html ) ) {

						$og_video['og:video:secure_url'] = 'https://static.slideshare.net/swf/ssplayer2.swf?id='.$vid_name;
						$og_video['og:video:url'] = 'http://static.slideshare.net/swf/ssplayer2.swf?id='.$vid_name;
						$og_video['og:video:width'] = (int) $xml->{'width'};
						$og_video['og:video:height'] = (int) $xml->{'height'};

					} elseif ( $this->p->debug->enabled )
						$this->p->debug->log( 'html missing from returned xml' );

					if ( ! empty( $xml->{'slide-image-baseurl'} ) ) {

						$og_video['og:video:thumbnail_url'] = 'https:'.(string) $xml->{'slide-image-baseurl'}.'1'.
							(string) $xml->{'slide-image-baseurl-suffix'};
						$og_video['og:video:has_image'] = true;

						$og_video['og:image'] = 'https:'.(string) $xml->{'slide-image-baseurl'}.'1'.
							(string) $xml->{'slide-image-baseurl-suffix'};
						$og_video['og:image:width'] = preg_replace( '/[^0-9]/', '',
							(string) $xml->{'slide-image-baseurl-suffix'} );

					} elseif ( $this->p->debug->enabled )
						$this->p->debug->log( 'slide-image-baseurl missing from returned xml' );

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
