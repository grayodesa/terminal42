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

if ( ! class_exists( 'WpssoProEcomYotpoWc' ) ) {

	class WpssoProEcomYotpoWc {

		private $p;

		public function __construct( &$plugin ) {
			$this->p =& $plugin;
			if ( $this->p->debug->enabled )
				$this->p->debug->mark();

			$this->p->util->add_plugin_filters( $this, array( 
				'og_woocommerce_product_page' => 2,
			) );
		}

		public function filter_og_woocommerce_product_page( $og_ecom, $mod ) {
			if ( $this->p->debug->enabled )
				$this->p->debug->mark();

			$have_error = false;
			$plugin_dir = false;
			$slug = 'yotpo-social-reviews-for-woocommerce';

			$settings = get_option( 'yotpo_settings' );

			if ( empty( $settings['app_key'] ) ) {
				if ( $this->p->debug->enabled )
					$this->p->debug->log( 'error: app_key missing from yotpo settings' );
				$have_error = true;
			}

			if ( empty( $settings['secret'] ) ) {
				if ( $this->p->debug->enabled )
					$this->p->debug->log( 'error: secret missing from yotpo settings' );
				$have_error = true;
			}
			
			if ( ! wc_yotpo_compatible() ) {
				if ( $this->p->debug->enabled )
					$this->p->debug->log( 'error: wc_yotpo_compatible() returned false' );
				$have_error = true;
			}

			if ( defined( 'WPMU_PLUGIN_DIR' ) &&
				file_exists( WPMU_PLUGIN_DIR.'/'.$slug ) )
					$plugin_dir = WPMU_PLUGIN_DIR;
			elseif ( defined( 'WP_PLUGIN_DIR' ) &&
				file_exists( WP_PLUGIN_DIR.'/'.$slug ) )
					$plugin_dir = WP_PLUGIN_DIR;
			else {
				if ( $this->p->debug->enabled )
					$this->p->debug->log( 'error: '.$slug.' not found in plugin dirs' );
				$have_error = true;
			}

			if ( $plugin_dir !== false ) {
				$api_lib = $plugin_dir.'/'.$slug.'/lib/yotpo-api/Yotpo.php';
				if ( file_exists( $api_lib ) )
					require_once( $api_lib );
				else {
					if ( $this->p->debug->enabled )
						$this->p->debug->log( 'error: '.$api_lib. ' does not exist' );
					$have_error = true;
				}
			}

			// exit early if any error
			if ( $have_error )
				return $og_ecom;

			if ( class_exists( 'Yotpo' ) ) {
				$yotpo = new Yotpo( $settings['app_key'], $settings['secret'] );
				$resp = $yotpo->get_product_bottom_line( array( 'product_id' => $mod['id'] ) );

				if ( isset( $resp['response']['bottomline']['average_score'] ) &&
					isset( $resp['response']['bottomline']['total_reviews'] ) ) {

					$og_ecom['product:rating:average'] = $resp['response']['bottomline']['average_score'];
					$og_ecom['product:rating:worst'] = 1;
					$og_ecom['product:rating:best'] = 5;
					$og_ecom['product:review:count'] = $resp['response']['bottomline']['total_reviews'];
				} else {
					if ( $this->p->debug->enabled )
						$this->p->debug->log( 'error: average_score and/or total_reviews missing from response' );
				}
			} if ( $this->p->debug->enabled )
				$this->p->debug->log( 'error: yotpo class does not exist' );

			return $og_ecom;
		}
	}
}

?>
