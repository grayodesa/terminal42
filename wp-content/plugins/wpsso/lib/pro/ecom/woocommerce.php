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

if ( ! class_exists( 'WpssoProEcomWoocommerce' ) ) {

	class WpssoProEcomWoocommerce {

		private $p;

		public function __construct( &$plugin ) {
			$this->p =& $plugin;

			if ( $this->p->debug->enabled )
				$this->p->debug->mark();

			if ( SucomUtil::get_const( 'WPSSO_CHECK_PRODUCT_OBJECT' ) === false ) {
				if ( $this->p->debug->enabled )
					$this->p->debug->log( 'WPSSO_CHECK_PRODUCT_OBJECT is false' );
			} else add_action( 'wp', array( &$this, 'wp_check_product_object' ) );

			// load the missing woocommerce front-end libraries
			if ( is_admin() ) {
				$this->p->util->add_plugin_actions( $this, array( 
					'admin_post_header' => 1, 
				) );
			}

			$this->p->util->add_plugin_filters( $this, array( 
				'og_prefix_ns' => 1,
				'header_use_post' => 1,
				'force_default_img' => 1,
				'tags' => 2,
				'description_seed' => 2,
				'attached_image_ids' => 2,
				'term_image_ids' => 3,
				'og_seed' => 3, 
			) );
		}

		// make sure the global $product variable is an object, not a string / slug
		public function wp_check_product_object() {
			global $product, $post;
			if ( ! empty( $product ) && is_string( $product ) && is_product() && ! empty( $post->ID ) )
				$product = $this->get_product( $post->ID );
		}

		public function action_admin_post_header( $mod ) {
			if ( ! empty( $this->p->options['plugin_filter_content'] ) ) {
				global $woocommerce;
				$wc_plugindir = trailingslashit( realpath( dirname( WC_PLUGIN_FILE ) ) );
				foreach ( array(
					'includes/class-wc-shortcodes.php',
					'includes/wc-notice-functions.php',
					'includes/wc-template-functions.php',
					'includes/abstracts/abstract-wc-session.php',
					'includes/class-wc-session-handler.php',
				) as $wc_inc_file )
					if ( file_exists( $wc_plugindir.$wc_inc_file ) )
						include_once( $wc_plugindir.$wc_inc_file );
					elseif ( $this->p->debug->enabled )
						$this->p->debug->log( 'include file missing: '.$wc_plugindir.$wc_inc_file );

				$session_class = apply_filters( 'woocommerce_session_handler', 'WC_Session_Handler' );
				if ( class_exists( $session_class ) )
					$woocommerce->session  = new $session_class();
			}
		}

		public function filter_og_prefix_ns( $ns ) {
			$ns['product'] = 'http://ogp.me/ns/product#';
			return $ns;
		}

		public function filter_header_use_post( $use_post ) {
			if ( is_shop() ) {
				$use_post = wc_get_page_id( 'shop' );
				if ( $this->p->debug->enabled )
					$this->p->debug->log( 'returning woocommerce shop page id: '.$use_post );
			}
			return $use_post;
		}

		// don't force default images on woocommerce product category and tag pages
		public function filter_force_default_img( $ret ) {
			if ( $ret ) {
				if ( SucomUtil::is_term_page() ) {
					if ( SucomUtil::is_product_category() || 
						SucomUtil::is_product_tag() )
							return false;
				}
			}
			return $ret;
		}

		public function filter_tags( $tags, $post_id ) {
			$terms = get_the_terms( $post_id, 'product_tag' );
			if ( is_array( $terms ) )
				foreach( $terms as $term )
					$tags[] = $term->name;
			return $tags;
		}

		public function filter_description_seed( $desc, $mod ) {
			if ( $mod['is_term'] ) {
				if ( SucomUtil::is_product_category() || SucomUtil::is_product_tag() ) {
					$term_desc = $this->p->util->cleanup_html_tags( term_description() );
					if ( ! empty( $term_desc ) )
						$desc = $term_desc;
				}
			} elseif ( is_page() ) {
				if ( is_cart() )
					$desc = 'Shopping Cart';
				elseif ( is_checkout() )
					$desc = 'Checkout Page';
				elseif ( is_account_page() )
					$desc = 'Account Page';
			}
			return $desc;
		}

		// images can only be attached to a post ID
		public function filter_attached_image_ids( $ids, $post_id ) {
			if ( ! SucomUtil::is_product_page( $post_id ) )
				return $ids;

			if ( ( $product = $this->get_product( $post_id ) ) === false )
				return $ids;	// abort

			if ( method_exists( $product, 'get_gallery_attachment_ids' ) ) {	// WooCommerce v2.x
				$attach_ids = $product->get_gallery_attachment_ids();
				if ( is_array( $attach_ids ) )
					$ids = array_merge( $attach_ids, $ids );
			}

			return $ids;
		}

		public function filter_term_image_ids( $ids, $size_name, $term_id ) {
			if ( SucomUtil::is_product_category() || SucomUtil::is_product_tag() ) {
				$pid = get_woocommerce_term_meta( $term_id, 'thumbnail_id', true );
				if ( ! empty( $pid ) )
					$ids[] = $pid;
			}
			return $ids;
		}

		public function filter_og_seed( $og, $use_post, $mod ) {
			if ( $this->p->debug->enabled )
				$this->p->debug->mark();

			$size_name = $this->p->cf['lca'].'-opengraph';
			$og_ecom = array();

			if ( $mod['is_post'] ) {
				// support checks for both front-end and back-end
				if ( is_product() || $mod['post_type'] === 'product' ) {
					if ( $this->p->debug->enabled )
						$this->p->debug->log( 'object ID '.$mod['id'].' is a product' );

					if ( ( $product = $this->get_product( $mod['id'] ) ) === false )
						return $og;	// abort

					$og_ecom['og:type'] = 'product';

					$this->add_product_mt( $og_ecom, $product );

					if ( get_option('woocommerce_enable_review_rating') === 'yes' ) {
						$og_ecom['product:rating:average'] = $product->get_average_rating();
						$og_ecom['product:rating:count'] = $product->get_rating_count();
						$og_ecom['product:rating:worst'] = 1;
						$og_ecom['product:rating:best'] = 5;
						$og_ecom['product:review:count'] = $product->get_review_count();
					}

					if ( apply_filters( $this->p->cf['lca'].'_og_add_product_mt_offer', false ) &&
						isset( $product->product_type ) &&
							$product->product_type === 'variable' ) {

						$variations = $product->get_available_variations();

						if ( is_array( $variations ) ) {
							foreach( $variations as $num => $var ) {
								$og_offer = array();	// start with an empty array
								$this->add_product_mt( $og_offer, $var, 'product:offer' );
								if ( ! empty( $og_offer ) )
									$og_ecom['product:offer'][] = $og_offer;
							}
						}
					}
	
					// hooked by the yotpo module to provide product ratings
					$og_ecom = apply_filters( $this->p->cf['lca'].'_og_woocommerce_product_page', $og_ecom, $mod );
				} else {
					if ( $this->p->debug->enabled )
						$this->p->debug->log( 'exiting early: object ID '.$mod['id'].' is not a product' );
					return $og;	// abort
				}
			} elseif ( $mod['is_term'] ) {
				if ( SucomUtil::is_product_category() || SucomUtil::is_product_tag() ) {
					if ( $this->p->debug->enabled )
						$this->p->debug->log( 'setting og:type for product '.
							( SucomUtil::is_product_category() ? 'category' : 'tag' ) );
					$og_ecom['og:type'] = 'product';
				} else {
					if ( $this->p->debug->enabled )
						$this->p->debug->log( 'exiting early: term is not product category or tag' );
					return $og;	// abort
				}
			} else return $og;	// abort

			$og_ecom = apply_filters( $this->p->cf['lca'].'_og_woocommerce', $og_ecom, $mod );

			return array_merge( $og, $og_ecom );
		}

		private function get_product( $id ) {
			global $woocommerce;
			if ( ! empty( $woocommerce->product_factory ) && 
				method_exists( $woocommerce->product_factory, 'get_product' ) ) {		// WooCommerce v2.x
				return $woocommerce->product_factory->get_product( $id );

			} elseif ( class_exists( 'WC_Product' ) ) {						// WooCommerce v1.x
				return new WC_Product( $id );

			} else {
				if ( $this->p->debug->enabled )
					$this->p->debug->log( 'exiting early: failed to get product object' );
				return false;
			}
		}

		private function add_product_mt( array &$og, $mixed, $mt_pre = 'product' ) {

			$dim_unit = 'cm';	// 'in', 'm', 'cm', or 'mm' 
			$weight_unit = 'kg';	// 'lbs', 'g', or 'kg' 
			$is_variation = false;

			if ( is_array( $mixed ) ) {
				$var =& $mixed;
				// check for incomplete variations
				if ( empty( $var['variation_is_visible'] ) ||
					empty( $var['variation_is_active'] ) ||
					empty( $var['is_purchasable'] ) ||
					empty( $var['variation_id'] ) )
						return false;	// abort
				$is_variation = true;
				if ( ( $product = $this->get_product( $var['variation_id'] ) ) === false )
					return false;	// abort
			} elseif ( is_object( $mixed ) ) {
				$product =& $mixed;
			} elseif ( ( $product = $this->get_product( $mixed ) ) === false ) {
				return false;	// abort
			}

			$id = $product->get_id();
			$og[$mt_pre.':id'] = $id;
			$og[$mt_pre.':sku'] = $product->get_sku();

			if ( $is_variation ) {	// additional information for offers / variations
				$og[$mt_pre.':url'] = $product->get_permalink();
				$og[$mt_pre.':title'] = $product->get_title();
				$og[$mt_pre.':image:id'] = $product->get_image_id();
			}

			$og[$mt_pre.':price:amount'] = $product->get_price();
			$og[$mt_pre.':price:currency'] = get_woocommerce_currency();

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
			if ( $product->is_in_stock() )
				$og[$mt_pre.':availability'] = 'InStock';
			else $og[$mt_pre.':availability'] = 'OutOfStock';

			if ( $product->has_dimensions() ) {
				$og[$mt_pre.':dimensions'] = $product->get_dimensions();
				if ( function_exists( 'wc_get_dimension' ) ) {
					$og[$mt_pre.':width'] = wc_get_dimension( $product->get_width(), $dim_unit );
					$og[$mt_pre.':height'] = wc_get_dimension( $product->get_height(), $dim_unit );
					$og[$mt_pre.':length'] = wc_get_dimension( $product->get_length(), $dim_unit );
				}
			}

			if ( $product->has_weight() ) {
				if ( function_exists( 'wc_get_weight' ) ) {
					$og[$mt_pre.':weight'] = wc_get_weight( $product->get_weight(), $weight_unit );
				}
			}

			if ( ! $is_variation ) {
				$og['product:color'] = $product->get_attribute( 'color' );
				$og['product:size'] = $product->get_attribute( 'size' );
			} else {
				$og['product:offer:color'] = empty( $var['attributes']['attribute_color'] ) ?
					'' : $var['attributes']['attribute_color'];
				$og['product:offer:size'] = empty( $var['attributes']['attribute_size'] ) ?
					'' : $var['attributes']['attribute_size'];
				$og['product:offer:description'] = empty( $var['variation_description'] ) ?
					'' : $this->p->util->cleanup_html_tags( $var['variation_description'] );
			}

			$terms = get_the_terms( $id, 'product_cat' );
			if ( is_array( $terms ) ) {
				$cats = array();
				foreach( $terms as $term )
					$cats[] = $term->name;
				if ( ! empty( $cats ) )
					$og[$mt_pre.':category'] = implode( ' > ', $cats );
			}

			$terms = get_the_terms( $id, 'product_tag' );
			if ( is_array( $terms ) ) {
				foreach( $terms as $term )
					$og[$mt_pre.':tag'][] = $term->name;
			}
		}
	}
}

?>
