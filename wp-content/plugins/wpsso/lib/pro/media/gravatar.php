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

if ( ! class_exists( 'WpssoProMediaGravatar' ) ) {

	class WpssoProMediaGravatar {

		private $p;

		public function __construct( &$plugin ) {
			$this->p =& $plugin;
			if ( $this->p->debug->enabled )
				$this->p->debug->mark();
			$this->add_actions();
		}

		protected function add_actions() {
			$this->p->util->add_plugin_filters( $this, array( 
				'get_user_options' => 2,
				'user_image_urls' => 3,
			), 1000 );	// hook after everything else
		}

		public function filter_get_user_options( $opts = array(), $user_id = 0 ) {
			if ( $this->p->debug->enabled )
				$this->p->debug->mark();

			$gravatar_url = '.gravatar.com/avatar/';

			// remove the gravatar image url from the user meta options in favor 
			// of adding it back with the filter_user_image_urls() filter
			if ( isset( $opts['og_img_url'] ) &&
				strpos( $opts['og_img_url'], $gravatar_url ) !== false ) {

				if ( $this->p->debug->enabled )
					$this->p->debug->log( 'removing gravatar image url from og_img_url option' );
				$opts['og_img_url'] = '';
			}

			return $opts;
		}

		public function filter_user_image_urls( $urls, $size_name, $user_id ) {
			if ( $this->p->debug->enabled )
				$this->p->debug->mark();

			$user_email = get_the_author_meta( 'user_email', $user_id );
			$size_info = SucomUtil::get_size_info( $size_name );
			$img_size = $size_info['width'] > 2048 ? 2048 : $size_info['width'];

			if ( empty( $user_email ) ) {
				if ( $this->p->debug->enabled )
					$this->p->debug->log( 'exiting early: empty user email' );
				return $urls;
			}

			$gravatar_url = ( SucomUtil::is_https() ? 'https://secure' : 'http://www' ).
				'.gravatar.com/avatar/'.md5( strtolower( trim( $user_email ) ) ).'?s='.$img_size;

			if ( $this->p->debug->enabled )
				$this->p->debug->log( 'fetching default image url for gravatar fallback' );
			$def_img = WpssoOpengraph::get_first_media_info( 'og:image',
				$this->p->media->get_default_image( 1, $size_name ) );

			// if we have a default image, use it as the fallback
			if ( ! empty( $def_img ) ) {
				if ( $this->p->debug->enabled )
					$this->p->debug->log( 'fallback default image: '.$def_img );
				$gravatar_url .= '&d='.urlencode( $def_img );

			// if a transient cache is available, check for a valid gravatar image
			} elseif ( ! SucomUtil::get_const( 'WPSSO_TRANSIENT_CACHE_DISABLE' ) ) {

				$head = wp_remote_head( $gravatar_url.'&d=404' );

				if ( is_wp_error( $head ) ) {
					if ( $this->p->debug->enabled )
						$this->p->debug->log( 'gravatar check error: '.$head->get_error_message() );
					$gravatar_url = '';
				} elseif ( isset( $head['response']['code'] ) && $head['response']['code'] === 404 ) {
					if ( $this->p->debug->enabled )
						$this->p->debug->log( 'gravatar check returned a 404 response code' );
					$gravatar_url = '';
				} else $gravatar_url .= '&d=mm';

			// fallback with the 'mystery man' image
			} else $gravatar_url .= '&d=mm';

			if ( ! empty( $gravatar_url ) )
				$urls[] = $gravatar_url;

			return $urls;
		}
	}
}

?>
