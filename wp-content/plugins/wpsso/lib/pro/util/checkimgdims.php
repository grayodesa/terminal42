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

if ( ! class_exists( 'WpssoProUtilCheckImgDims' ) ) {

	class WpssoProUtilCheckImgDims {

		private $p;

		public function __construct( &$plugin ) {
			$this->p =& $plugin;
			$this->p->util->add_plugin_filters( $this, array(
				'attached_accept_img_dims' => 6,
				'content_accept_img_dims' => 6,
			) );
		}

		public function filter_attached_accept_img_dims( $bool, $img_url, $img_width, $img_height, $size_name, $pid ) {
			if ( ! $bool )	// don't recheck already rejected images
				return false;

			$lca = $this->p->cf['lca'];
			if ( strpos( $size_name, $lca.'-' ) !== 0 ) {
				if ( $this->p->debug->enabled )
					$this->p->debug->log( 'exiting early: '.$size_name.' not a '.$lca.' custom image size' );
				return $bool;
			}
			$size_info = SucomUtil::get_size_info( $size_name );
			$is_cropped = empty( $size_info['crop'] ) ? false : true;	// get_size_info() returns false, true, or an array
			$is_sufficient_w = $img_width >= $size_info['width'] ? true : false;
			$is_sufficient_h = $img_height >= $size_info['height'] ? true : false;

			if ( ( ! $is_cropped && ( ! $is_sufficient_w && ! $is_sufficient_h ) ) ||
				( $is_cropped && ( ! $is_sufficient_w || ! $is_sufficient_h ) ) ) {

				$img_meta = wp_get_attachment_metadata( $pid );

				if ( isset( $img_meta['width'] ) && isset( $img_meta['height'] ) &&
					$img_meta['width'] < $size_info['width'] && $img_meta['height'] < $size_info['height'] )
						$size_text = $img_meta['width'].'x'.$img_meta['height'].
							' ('.__( 'full size original', 'wpsso' ).')';
				else $size_text = $img_width.'x'.$img_height;

				if ( $this->p->debug->enabled )
					$this->p->debug->log( 'exiting early: image ID '.$pid.' rejected - '.$size_text.' too small for the '.$size_name.
						' ('.$size_info['width'].'x'.$size_info['height'].( $is_cropped ? ' cropped' : '' ).') image size' );

				if ( is_admin() ) {
					$media_lib = __( 'Media Library', 'wpsso' );
					$size_label = $this->p->util->get_image_size_label( $size_name );
					$dismiss_id = 'wp_'.$pid.'_'.$img_width.'x'.$img_height.'_'.$size_name.'_'.$size_info['width'].'x'.$size_info['height'].'_rejected';
					$required_text = '<b>'.$size_label.'</b> ('.$size_info['width'].'x'.$size_info['height'].
						( $is_cropped ? ' <i>'.__( 'cropped', 'wpsso' ).'</i>' : '' ).')';
					$reject_notice = $this->p->msgs->get( 'notice-image-rejected', array( 'size_label' => $size_label ) );
					$this->p->notice->warn( sprintf( __( '%1$s image ID %2$s ignored &mdash; the resulting image of %3$s is too small for the required %4$s image dimensions.', 'wpsso' ), $media_lib, $pid, $size_text, $required_text ).' '.$reject_notice, false, true, $dismiss_id, true );
				}

				return false;	// exit early
			}

			if ( $this->p->debug->enabled )
				$this->p->debug->log( 'image ID '.$pid.' dimensions ('.$img_width.'x'.$img_height.') are sufficient' );

			return true;	// image dimensions are good
		}

		public function filter_content_accept_img_dims( $bool, $og_image, $size_name, $attr_name, $content_passed ) {
			if ( ! $bool )	// don't recheck already rejected images
				return false;

			$lca = $this->p->cf['lca'];
			if ( strpos( $size_name, $lca.'-' ) !== 0 ) {
				if ( $this->p->debug->enabled )
					$this->p->debug->log( 'exiting early: '.$size_name.' not a '.$lca.' custom image size' );
				return $bool;
			}
			$size_info = SucomUtil::get_size_info( $size_name );
			$is_cropped = empty( $size_info['crop'] ) ? false : true;	// get_size_info() returns false, true, or an array
			$is_sufficient_w = $og_image['og:image:width'] >= $size_info['width'] ? true : false;
			$is_sufficient_h = $og_image['og:image:height'] >= $size_info['height'] ? true : false;
			$og_image_url = SucomUtil::get_mt_media_url( $og_image, 'og:image' );

			if ( ( $attr_name == 'src' && $is_cropped && ( $is_sufficient_w && $is_sufficient_h ) ) ||
				( $attr_name == 'src' && ! $is_cropped && ( $is_sufficient_w || $is_sufficient_h ) ) ||
					$attr_name == 'data-share-src' ) {

				if ( $this->p->debug->enabled )
					$this->p->debug->log( $og_image_url.' dimensions ('.$og_image['og:image:width'].'x'.
						$og_image['og:image:height'].') are sufficient' );

				return true;	// image dimensions are good
			}

			if ( $this->p->debug->enabled )
				$this->p->debug->log( 'content image rejected: width / height missing or too small for '.$size_name );

			if ( is_admin() ) {
				$size_label = $this->p->util->get_image_size_label( $size_name );
				$dismiss_id = 'content_'.$og_image_url.'_'.$size_name.'_rejected';
				$required_text = '<b>'.$size_label.'</b> ('.$size_name.')';
				$data_wp_pid_msg = $content_passed ? '' : ' '.sprintf( __( '%1$s includes an additional \'data-wp-pid\' attribute for Media Library images &mdash; if this image was selected from the Media Library before %1$s was activated, try removing and adding the image back to your content.', 'wpsso' ), $this->p->cf['plugin'][$lca]['short'] );
				$this->p->notice->warn( sprintf( __( 'Image %1$s in content ignored &mdash; the image width / height is too small for the required %2$s image dimensions.', 'wpsso' ), $og_image_url, $required_text ).$data_wp_pid_msg, false, true, $dismiss_id, true );
			}

			return false;
		}
	}
}

?>
