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

if ( ! class_exists( 'WpssoProEcomEdd' ) ) {

	class WpssoProEcomEdd {

		private $p;
		private $opts = array();
		private $price_amount = 0.00;
		private $price_high = 0.00;
		private $price_fmt = '0.00';
		private $has_setup = false;

		public function __construct( &$plugin ) {
			$this->p =& $plugin;
			$this->p->util->add_plugin_filters( $this, array( 
				'tags' => 2,
				'description_seed' => 1,
				'og_seed' => 3, 
				'og_prefix_ns' => 1,
			) );
		}

		public function filter_tags( $tags, $post_id ) {
			$terms = get_the_terms( $post_id, 'download_tag' );
			if ( is_array( $terms ) )
				foreach( $terms as $term )
					$tags[] = $term->name;
			return $tags;
		}

		public function filter_description_seed( $desc ) {
			if ( is_page() ) {
				if ( edd_is_checkout() ) {
					$desc = 'Checkout Page';
				}
			}
			return $desc;
		}

		public function filter_og_seed( $og, $use_post, $mod ) {
			if ( $this->p->debug->enabled )
				$this->p->debug->mark();

			// sanity checks
			if ( ! $mod['is_post'] ) {
				if ( $this->p->debug->enabled )
					$this->p->debug->log( 'exiting early: module name is not post' );
				return $og;	// abort

			} elseif ( $mod['post_type'] !== 'download' ) {
				if ( $this->p->debug->enabled )
					$this->p->debug->log( 'exiting early: post ID '.$mod['id'].' is not a download' );
				return $og;	// abort
			}

			if ( ! $this->has_setup )
				$this->set_post_properties( $mod );

			$og_ecom = array();
			$og_ecom['og:type'] = 'product';
			$og_ecom['product:price:amount'] = $this->price_amount;
			$og_ecom['product:price:currency'] = edd_get_currency();

			$terms = get_the_terms( $mod['id'], 'download_category' );
			if ( is_array( $terms ) ) {
				$cats = array();
				foreach( $terms as $term )
					$cats[] = $term->name;
				if ( ! empty( $cats ) )
					$og_ecom['product:category'] = implode( ' > ', $cats );
			}

			$terms = get_the_terms( $mod['id'], 'download_tag' );
			if ( is_array( $terms ) ) {
				foreach( $terms as $term )
					$og_ecom['product:tag'][] = $term->name;
			}

			$og_ecom = apply_filters( $this->p->cf['lca'].'_og_edd', $og_ecom, $use_post, $mod );

			return array_merge( $og, $og_ecom );
		}

		public function filter_og_prefix_ns( $ns ) {
			$ns['product'] = 'http://ogp.me/ns/product#';
			return $ns;
		}

		private function set_post_properties( $mod ) {
			if ( ! edd_has_variable_prices( $mod['id'] ) ) {
				$this->price_amount = edd_get_download_price( $mod['id'] );
				$this->price_fmt = edd_currency_filter( edd_format_amount( $this->price_amount ) );
			} else {
				$this->price_high = edd_get_highest_price_option( $mod['id'] );
				$this->price_amount = edd_get_lowest_price_option( $mod['id'] );
				$this->price_fmt = edd_currency_filter( edd_format_amount( $this->price_amount ) ).
					' - '.edd_currency_filter( edd_format_amount( $this->price_high ) );
			}
			$this->has_setup = true;
		}
	}
}

?>
