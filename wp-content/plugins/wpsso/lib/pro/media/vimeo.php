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

if ( ! class_exists( 'WpssoProMediaVimeo' ) ) {

	class WpssoProMediaVimeo {

		private $p;

		public function __construct( &$plugin ) {
			$this->p =& $plugin;
			$this->p->util->add_plugin_filters( $this, array( 
				'video_info' => 4,
			), 20 );
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
			} elseif ( strpos( $embed_url, 'vimeo.com' ) === false )
				return $og_video;

			/*
			 * Vimeo video API
			 */
			if ( preg_match( '/^.*(vimeo\.com)\/([^<>]+\/)?([^\/\?\&\#<>]+).*$/', $embed_url, $match ) ) {

				$vid_name = preg_replace( '/^.*\//', '', $match[3] );
				$vid_swf_uri = '//vimeo.com/moogaloop.swf?clip_id='.$vid_name.
					( empty( $this->p->options['og_vid_autoplay'] ) ? '' : '&autoplay=1' );	// force autoplay

				$og_video['og:video:secure_url'] = 'https:'.$vid_swf_uri;
				$og_video['og:video:url'] = 'http:'.$vid_swf_uri;
				$og_video['og:video:embed_url'] = 'https://player.vimeo.com/video/'.$vid_name.
					( empty( $this->p->options['og_vid_autoplay'] ) ? '' : '?autoplay=1' );	// force autoplay

				$api_url = ( empty( $this->p->options['og_vid_https'] ) ? 'http:' : 'https:' ).
					'//vimeo.com/api/oembed.xml?url=http%3A//vimeo.com/'.$vid_name;

				if ( function_exists( 'simplexml_load_string' ) ) {

					$xml = @simplexml_load_string( $this->p->cache->get( $api_url, 'raw', 'transient' ) );

					if ( ! empty( $xml->title ) )
						$og_video['og:video:title'] = (string) $xml->title;

					if ( ! empty( $xml->description[0] ) )
						$og_video['og:video:description'] = $this->p->util->limit_text_length( (string) $xml->description[0],
							$this->p->options['og_desc_len'], '...', true );

					if ( ! empty( $xml->duration ) )
						$og_video['og:video:duration'] = 'PT'.(string) $xml->duration.'S';

					if ( ! empty( $xml->upload_date ) ) {
						if ( function_exists( 'date_format' ) )	// available since PHP v5.2
							$og_video['og:video:upload_date'] = date_format( date_create( (string) $xml->upload_date ), 'c' );
					}

					if ( ! empty( $xml->thumbnail_url ) ) {

						if ( $this->p->debug->enabled )
							$this->p->debug->log( 'setting og:video and og:image from vimeo api xml' );

						$img_url = (string) $xml->thumbnail_url;
						$img_width = (string) $xml->thumbnail_width;
						$img_height = (string) $xml->thumbnail_height;

						$og_video['og:video:width'] = $img_width;
						$og_video['og:video:height'] = $img_height;
						$og_video['og:video:thumbnail_url'] = $img_url;
						$og_video['og:video:has_image'] = true;

						if ( strpos( $img_url, 'https:' ) === 0 )
							$og_video['og:image:secure_url'] = $img_url;
						$og_video['og:image'] = preg_replace( '/^https:/', 'http:', $img_url );
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
