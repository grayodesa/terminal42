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

if ( ! class_exists( 'WpssoProEcomWpecommerce' ) ) {

	class WpssoProEcomWpecommerce {

		private $p;
		private $has_setup = false;
		private $currency;
		private $decimals = 2;
		private $amount;
		private $price;
		private $meta;
		private $is_product = false;
		private $is_category = false;
		private $cat_has_img = false;
		private $cat_id;
		private $cat_img;
		private $cat_data;

		public function __construct( &$plugin ) {
			$this->p =& $plugin;
			$this->p->util->add_plugin_filters( $this, array( 
				'description_seed' => 4, 
				'schema_types' => 1,
				'og_seed' => 3, 
				'og_prefix_ns' => 1,
			) );
		}

		public function filter_description_seed( $desc, $mod, $add_hashtags, $md_idx ) {
			if ( $mod['post_type'] === 'wpsc-product' ) {
				if ( ! $this->has_setup )
					$this->set_post_properties( $mod['id'] );
				if ( $this->is_category == true ) {
					if ( ! empty( $this->cat_data->description ) )
						$desc = $this->cat_data->description;
				}
			}
			return $desc;
		}

		public function filter_schema_types( $types ) {
			$types['wpsc-product'] = 'http://schema.org/Product';
			return $types;
		}

		public function filter_og_seed( $og, $use_post, $mod ) {
			if ( $this->p->debug->enabled )
				$this->p->debug->mark();

			// sanity checks
			if ( $mod['name'] !== 'post' ) {
				if ( $this->p->debug->enabled )
					$this->p->debug->log( 'exiting early: module name is not post' );
				return $og;	// abort

			} elseif ( get_post_type( $mod['id'] ) !== 'wpsc-product' ) {
				if ( $this->p->debug->enabled )
					$this->p->debug->log( 'exiting early: post ID '.$mod['id'].' is not a wpsc-product' );
				return $og;	// abort
			}

			if ( ! $this->has_setup )
				$this->set_post_properties( $mod );

			$max = $this->p->util->get_max_nums( $mod );
			$size_name = $this->p->cf['lca'].'-opengraph';
			$og_ecom = array();
			$og_ecom['og:type'] = 'product';
			$og_ecom['og:image'] = array();

			if ( $this->is_product === true ) {
				$og_ecom['product:price:currency'] = $this->currency;
				$og_ecom['product:price:amount'] = $this->amount;

				if ( get_option( 'list_view_quantity' ) )
					$og_ecom['product:availability'] = wpsc_product_has_stock( $mod['id'] ) ? 'InStock' : 'OutOfStock';

				$og_ecom['og:image'] = $this->p->media->get_post_images( $max['og_img_max'], $size_name, $mod['id'] );

			} elseif ( $this->is_category == true ) {
				if ( ! empty( $this->cat_img ) )
					$og_ecom['og:image'] = $this->cat_img;

			} else {
				if ( $this->p->debug->enabled )
					$this->p->debug->log( 'exiting early: post ID '.$mod['id'].' is not a product or category' );
				return $og;	// abort
			}

			$og_ecom = apply_filters( $this->p->cf['lca'].'_og_wpecommerce', $og_ecom, $mod );

			return array_merge( $og, $og_ecom );
		}

		public function filter_og_prefix_ns( $ns ) {
			$ns['product'] = 'http://ogp.me/ns/product#';
			return $ns;
		}

		private function set_post_properties( $mod ) {
			// test for single product first, as wpsc_is_in_category() will match singular and index pages as well
			if ( wpsc_is_single_product() ) {
				global $wpdb;
				$this->is_product = true;

				$this->currency = $wpdb->get_var( "SELECT `code` FROM `".WPSC_TABLE_CURRENCY_LIST."` 
					WHERE `id`='".get_option( 'currency_type' )."' LIMIT 1" );

				$this->decimals = apply_filters( 'wpsc_modify_decimals', $this->decimals );

				// lowest price of product (without variants or currency symbol)
				$this->amount = number_format( wpsc_calculate_price( $mod['id'] ), $this->decimals );

				// lowest price of variants with currency symbol and 'from' text
				$this->price = wpsc_the_product_price( false, false, $mod['id'] );

				$this->meta = new wpsc_custom_meta( $mod['id'] );

			} elseif ( wpsc_is_in_category() ) {
				$this->is_category = true;
				$this->cat_id = wpsc_category_id();
				$this->cat_img = wpsc_category_image( $this->cat_id );
				$this->cat_data = get_term_by( 'id', $this->cat_id, 'wpsc_product_category' );
			}
			$this->has_setup = true;
		}
	}
}

?>
