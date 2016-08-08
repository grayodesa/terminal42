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

if ( ! class_exists( 'WpssoProMediaUpscale' ) ) {

	class WpssoProMediaUpscale {

		private $p;

		public function __construct( &$plugin ) {
			$this->p =& $plugin;
			add_filter( 'image_resize_dimensions', 
				array( &$this, 'upscale_image_resize_dimensions' ), 1000, 6 );
		}

		/* This filter does not receive the image ID or size_name. get_attachment_image_src() 
		 * in the WpssoMedia class saves / sets the image information (pid, size_name, etc) before 
		 * calling the image_make_intermediate_size() function (and others), which eventually can 
		 * get us here. We can use WpssoMedia::get_image_src_info() to retrieve this image information
		 * and check for our own image sizes, for use in status and warning notices.
		 */
		public function upscale_image_resize_dimensions( $ret, $orig_w, $orig_h, $dst_w, $dst_h, $crop ) {

			if ( $this->p->debug->enabled ) {
				$this->p->debug->log_args( array(
					'ret' => $ret,
					'orig_w' => $orig_w,
					'orig_h' => $orig_h,
					'dst_w' => $dst_w,
					'dst_h' => $dst_h,
					'crop' => $crop,
				) );
			}

			/* Check input arguments:
			 *
			 * - The original image must have a width and height larger than 0.
			 * - If we're not cropping, at least one new side must be larger than 0.
			 * - If we're cropping, then both new sides must be larger than 0.
			 */
			if ( $orig_w <= 0 || $orig_h <= 0 || 
				( ! $crop && ( $dst_w <= 0 && $dst_h <= 0 ) ) ||
				( $crop && ( $dst_w <= 0 || $dst_h <= 0 ) ) ) {

				if ( $this->p->debug->enabled )
					$this->p->debug->log( 'upscale skipped: one or more input arguments is invalid' );
				return $ret;
			}

			/* Check if upscaling is required:
			 * 
			 * - If the original image is large enough to be downsized, then we don't need to upscale.
			 * - If we're not cropping, and one side is large enough, then we're ok to downsize.
			 * - If we're cropping, then both sides have to be large enough to downsize.
			 */
			$is_sufficient_w = $dst_w > 0 && $orig_w >= $dst_w ? true : false;
			$is_sufficient_h = $dst_h > 0 && $orig_h >= $dst_h ? true : false;

			if ( ( ! $crop && ( $is_sufficient_w || $is_sufficient_h ) ) ||
				( $crop && ( $is_sufficient_w && $is_sufficient_h ) ) ) {

				if ( $this->p->debug->enabled )
					$this->p->debug->log( 'upscale skipped: original image dimensions are sufficient' );
				return $ret;
			}

			/* get_attachment_image_src() in the WpssoMedia class saves / sets the image information (pid, 
			 * size_name, etc) before calling the image_make_intermediate_size() function (and others).
			 * Returns null if no image information was set (presumably because we arrived here without 
			 * passing through our own method).
			 */
			$img_info = (array) WpssoMedia::get_image_src_info();

			if ( $this->p->debug->enabled ) {
				if ( empty( $img_info ) )
					$this->p->debug->log( 'no image source information from media class' );
				else $this->p->debug->log_arr( '$img_info', $img_info );
			}

			/* By default, only upscale our own image sizes (ie. having passed through our own method).
			 * $img_info will be null (or empty) if WordPress or another plugin is requesting the resize.
			 * In that case, our own image sizes will not be upscaled until we request them ourselves.
			 * Set the WPSSO_IMAGE_UPSCALE_ALL constant to true in order to upscale all image sizes.
			 * SucomUtil::get_const() returns null if the constant is not defined.
			 */
			if ( ( empty( $img_info['size_name'] ) || 
				strpos( $img_info['size_name'], $this->p->cf['lca'].'-' ) !== 0 ) &&
					! apply_filters( $this->p->cf['lca'].'_image_upscale_all', 
						SucomUtil::get_const( 'WPSSO_IMAGE_UPSCALE_ALL' ) ) ) {

				if ( $this->p->debug->enabled )
					$this->p->debug->log( 'upscale skipped: not '.$this->p->cf['lca'].' size name and upscale all is false' );
				return $ret;
			}

			// check for pre-filtered / inherited resize values
			if ( is_array( $ret ) && count( $ret ) === 8 ) {

				list( $src_x, $src_y, $dst_w, $dst_h, $src_w, $src_h ) = $ret;

				$size_ratio = max( $dst_w / $orig_w, $dst_h / $orig_h );

			} elseif ( $crop ) {

				$size_ratio = max( $dst_w / $orig_w, $dst_h / $orig_h );

				$src_w = round( $dst_w / $size_ratio );
				$src_h = round( $dst_h / $size_ratio );

				if ( ! is_array( $crop ) || count( $crop ) !== 2 )
					$crop = array( 'center', 'center' );

				list( $crop_x, $crop_y ) = $crop;

				if ( $crop_x === 'left' )
					$src_x = 0;
				elseif ( $crop_x === 'right' )
					$src_x = $orig_w - $src_w;
				else $src_x = floor( ( $orig_w - $src_w ) / 2 );

				if ( $crop_y === 'top' )
					$src_y = 0;
					elseif ( $crop_y === 'bottom' )
					$src_y = $orig_h - $src_h;
				else $src_y = floor( ( $orig_h - $src_h ) / 2 );

			} else {

				$src_x = 0;
				$src_y = 0;

				$src_w = $orig_w;
				$src_h = $orig_h;

				/* Calculate width and height ratios between the new and original sizes.
				 * Calculate any missing width / height values for the new size - input 
				 * sanitation assures us that we have at least one positive value.
				 */
				if ( $dst_w > 0 )
					$w_ratio = $dst_w / $orig_w;

				if ( $dst_h > 0 ) {
					$h_ratio = $dst_h / $orig_h;
					if ( $dst_w <= 0 ) {
						$dst_w = $orig_w * $h_ratio;
						$w_ratio = $dst_w / $orig_w;
					}
				} else {
					$dst_h = $orig_h * $w_ratio;
					$h_ratio = $dst_h / $orig_h;
				}

				$min_ratio = min( $w_ratio, $h_ratio );
				$max_ratio = max( $w_ratio, $h_ratio ); 

				if ( (int) round( $orig_w * $max_ratio ) > $dst_w || 
					(int) round( $orig_h * $max_ratio ) > $dst_h )
						$ratio = $min_ratio;
				else $ratio = $max_ratio;

				$dst_w = max( 1, (int) round( $orig_w * $ratio ) );
				$dst_h = max( 1, (int) round( $orig_h * $ratio ) ); 

				$size_ratio = max( $dst_w / $orig_w, $dst_h / $orig_h );
			}

			$size_diff = round( ( $size_ratio * 100 ) - 100 );

			$max_diff = apply_filters( $this->p->cf['lca'].'_image_upscale_max',
				$this->p->options['plugin_upscale_img_max'], $img_info );

			if ( is_admin() && ! empty( $img_info['size_name'] ) ) {
				$size_label = $this->p->util->get_image_size_label( $img_info['size_name'] );

				if ( $size_diff > $max_diff ) {

					$msg_id = 'wp_'.$img_info['pid'].'_'.$orig_w.'x'.$orig_h.'_'.$img_info['size_name'].'_'.$dst_w.'x'.$dst_h.'_upscaled';
					$this->p->notice->warn( sprintf( __( 'Image ID %1$s of %2$s cannot be upscaled by %3$s from %4$s to %5$s for the %6$s image size (exceeds %7$s maximum).',
						'wpsso' ), $img_info['pid'], $orig_w.'x'.$orig_h, $size_diff.'%', $src_w.'x'.$src_h, $dst_w.'x'.$dst_h,
							'<b>'.$size_label.'</b>', $max_diff.'%' ), false, true, $msg_id, true );

				} else $this->p->notice->inf( sprintf( __( 'Image ID %1$s of %2$s has been upscaled by %3$s from %4$s to %5$s for the %6$s image size.',
					'wpsso' ), $img_info['pid'], $orig_w.'x'.$orig_h, $size_diff.'%', $src_w.'x'.$src_h, $dst_w.'x'.$dst_h,
						'<b>'.$size_label.'</b>' ) );
			}

			if ( $size_diff > $max_diff ) {
				if ( $this->p->debug->enabled )
					$this->p->debug->log( 'upscale skipped: '.$orig_w.'x'.$orig_h.' from '.$src_w.'x'.$src_h.
						' to '.$dst_w.'x'.$dst_h.' is '.$size_diff.'% diff and exceeds '.$max_diff.'% limit' );
				return $ret;
			}

			/* The WPSSO_IMAGE_UPSCALE_TEST constant and associated filter allows us to display 
			 * passed / failed notices without actually making any changes (saving the image).
			 * SucomUtil::get_const() returns null if the constant is not defined.
			 */
			if ( apply_filters( $this->p->cf['lca'].'_image_upscale_test',
				SucomUtil::get_const( 'WPSSO_IMAGE_UPSCALE_TEST' ), $img_info ) )
					return $ret;
			// return array( dst_x, dst_y, src_x, src_y, dst_w, dst_h, src_w, src_h );
			else return array( 0, 0, (int) $src_x, (int) $src_y, (int) $dst_w, (int) $dst_h, (int) $src_w, (int) $src_h );
		}
	}
}

?>
