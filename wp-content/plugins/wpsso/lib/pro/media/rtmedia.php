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

if ( ! class_exists( 'WpssoProMediaRtmedia' ) ) {

	class WpssoProMediaRtmedia {

		private $p;

		public function __construct( &$plugin ) {
			$this->p =& $plugin;

			add_filter( 'bp_activity_allowed_tags', 
				array( &$this, 'add_activity_allowed_tags' ), 10, 1 );
			if ( $this->p->debug->enabled )
				$this->p->debug->log( 'added filter for bp_activity_allowed_tags' );

			add_filter( 'rtmedia_single_activity_filter', 
				array( &$this, 'add_image_id_attribute' ), 10, 2 );
			if ( $this->p->debug->enabled )
				$this->p->debug->log( 'added filter for rtmedia_single_activity_filter' );
		}

		public function add_activity_allowed_tags( $allow ) {
			$allow['img']['data-wp-pid'] = array();
			return $allow;
		}

		public function add_image_id_attribute( $html, $media ) {
			if ( isset( $media->media_type ) ) {
				global $rtmedia;
				if ( $media->media_type === 'photo' ) {
					$thumbnail_id = isset( $media->media_id ) ?
						$media->media_id : 0;
					if ( $thumbnail_id ) {
						$html = preg_replace( '/<img /', '<img data-wp-pid="'.$thumbnail_id.'" ', $html );
					} elseif ( $this->p->debug->enabled )
						$this->p->debug->log( 'rtmedia media_id is empty or missing' );
				} elseif ( $this->p->debug->enabled )
					$this->p->debug->log( 'rtmedia type is not photo' );
			} elseif ( $this->p->debug->enabled )
				$this->p->debug->log( 'rtmedia type not set' );
			return $html;
		}
	}
}

?>
