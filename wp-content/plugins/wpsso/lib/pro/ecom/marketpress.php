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

if ( ! class_exists( 'WpssoProEcomMarketpress' ) ) {

	class WpssoProEcomMarketpress {

		private $p;
		private $has_setup = false;
		private $metas = array();
		private $settings = array();
		private $names = array();
		private $prices = array();
		private $sales = array();
		private $stock = array();
		private $track = array();
		private $decimals = 2;

		public function __construct( &$plugin ) {
			$this->p =& $plugin;
			$this->p->util->add_plugin_filters( $this, array( 
				'og_seed' => 3, 
				'og_prefix_ns' => 1,
			) );
		}

		public function filter_og_seed( $og, $use_post, $mod ) {
			if ( $this->p->debug->enabled )
				$this->p->debug->mark();

			// sanity checks
			if ( $mod['name'] !== 'post' ) {
				if ( $this->p->debug->enabled )
					$this->p->debug->log( 'exiting early: module name is not post' );
				return $og;	// abort

			} elseif ( get_post_type( $mod['id'] ) !== 'product' ) {
				if ( $this->p->debug->enabled )
					$this->p->debug->log( 'exiting early: post ID '.$mod['id'].' is not a product' );
				return $og;	// abort
			}

			if ( ! $this->has_setup )
				$this->set_post_properties( $mod );

			$max = $this->p->util->get_max_nums( $mod );
			$size_name = $this->p->cf['lca'].'-opengraph';
			$og_ecom = array();
			$og_ecom['og:type'] = 'product';

			// open graph only supports one product price (albeit in multiple currencies)
			foreach  ( $this->prices as $key => $value ) {
				$price = isset( $this->sales[$key] ) ?
					$this->sales[$key] : $this->prices[$key];

				$og_ecom['product:price:amount'] = number_format( $price, $this->decimals );
				$og_ecom['product:price:currency'] = $this->settings['currency'];

				if ( ! empty( $this->track ) )
					/*
					 * Possible values (see http://schema.org/ItemAvailability):
					 *	Discontinued
					 *	InStock
					 *	InStoreOnly
					 *	LimitedAvailability
					 *	OnlineOnly
					 *	OutOfStock
					 *	PreOrder
					 *	SoldOut 
					 */
					$og_ecom['product:availability'] = isset( $this->stock[$key] ) && 
						$this->stock[$key] > 0 ? 'InStock' : 'OutOfStock';

				if ( ! empty( $this->names[$key] ) )
					$og_ecom['og:title'] = wp_title( $this->p->options['og_title_sep'], false, 'right' ).
						' ('.$this->names[$key].')';
				break;
			}

			$og_ecom['og:image'] = $this->p->media->get_post_images( $max['og_img_max'], $size_name, $mod['id'] );

			$og_ecom = apply_filters( $this->p->cf['lca'].'_og_marketpress', $og_ecom, $mod );

			return array_merge( $og, $og_ecom );
		}

		public function filter_og_prefix_ns( $ns ) {
			$ns['product'] = 'http://ogp.me/ns/product#';
			return $ns;
		}

		private function set_post_properties( $mod ) {
			$this->metas = get_post_custom( $mod['id'] );
			$this->settings = get_option('mp_settings');
			$this->names = maybe_unserialize( $this->metas['mp_var_name'][0] );
			$this->prices = maybe_unserialize( $this->metas['mp_price'][0] );
			$this->sales = maybe_unserialize( $this->metas['mp_sale_price'][0] );
			$this->stock = maybe_unserialize( $this->metas['mp_inventory'][0] );
			$this->track = maybe_unserialize( $this->metas['mp_track_inventory'][0] );
			$this->decimals = $this->settings['curr_decimal'] === '0' ? 0 : 2;
			$this->has_setup = true;
		}
	}
}

?>
