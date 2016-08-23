<?php
/*
 * IMPORTANT: READ THE LICENSE AGREEMENT CAREFULLY.
 *
 * BY INSTALLING, COPYING, RUNNING, OR OTHERWISE USING THE 
 * WPSSO SCHEMA JSON-LD (WPSSO JSON) PRO APPLICATION, YOU AGREE
 * TO BE BOUND BY THE TERMS OF ITS LICENSE AGREEMENT.
 * 
 * License: Nontransferable License for a WordPress Site Address URL
 * License URI: http://surniaulula.com/wp-content/plugins/wpsso-schema-json-ld/license/pro.txt
 *
 * IF YOU DO NOT AGREE TO THE TERMS OF ITS LICENSE AGREEMENT,
 * PLEASE DO NOT INSTALL, RUN, COPY, OR OTHERWISE USE THE
 * WORDPRESS SOCIAL SHARING OPTIMIZATION (WPSSO) PRO APPLICATION.
 * 
 * Copyright 2016 Jean-Sebastien Morisset (http://surniaulula.com/)
 */

if ( ! defined( 'ABSPATH' ) ) 
	die( 'These aren\'t the droids you\'re looking for...' );

if ( ! class_exists( 'WpssoJsonProHeadProduct' ) ) {

	class WpssoJsonProHeadProduct {

		protected $p;

		public function __construct( &$plugin ) {
			$this->p =& $plugin;
			if ( $this->p->debug->enabled )
				$this->p->debug->mark();

			$this->p->util->add_plugin_filters( $this, array(
				'og_add_product_mt_offer' => 1,			// $bool
				'json_data_http_schema_org_product' => 4,	// $json_data, $mod, $mt_og, $user_id
			) );
		}

		public function filter_og_add_product_mt_offer( $bool ) {
			return true;
		}

		public function filter_json_data_http_schema_org_product( $json_data, $mod, $mt_og, $user_id ) {

			if ( $this->p->debug->enabled )
				$this->p->debug->mark();

			$ret = array();

			/*
			 * Property:
			 * 	sku
			 */
			WpssoSchema::add_data_itemprop_from_assoc( $ret, $mt_og, array( 
				'sku' => 'product:sku',
				'color' => 'product:color',
				'category' => 'product:category',
			) );

			WpssoSchema::add_data_quant_from_assoc( $ret, $mt_og, array( 
				'width' => 'product:width',
				'height' => 'product:height',
				'length' => 'product:length',
				'weight' => 'product:weight',
			) );

			/*
			 * Property:
			 * 	offers as http://schema.org/Offer
			 */
			if ( empty( $mt_og['product:offer'] ) ) {

				if ( ( $offer = WpssoSchema::get_data_itemprop_from_assoc( $mt_og, array( 
					'price' => 'product:price:amount',
					'priceCurrency' => 'product:price:currency',
					'availability' => 'product:availability',
				) ) ) !== false )
					$ret['offers'][] = WpssoSchema::get_item_type_context( 'http://schema.org/Offer', $offer );

			} elseif ( is_array( $mt_og['product:offer'] ) ) {	// just in case
				foreach ( $mt_og['product:offer'] as $mt_offer ) {

					// setup the offer with basic itemprops
					if ( is_array( $mt_offer ) &&	// just in case
						( $offer = WpssoSchema::get_data_itemprop_from_assoc( $mt_offer, array( 
							'price' => 'product:offer:price:amount',
							'priceCurrency' => 'product:offer:price:currency',
							'availability' => 'product:offer:availability',
					) ) ) !== false ) {

						// add additional product information to the offer
						if ( ( $product = WpssoSchema::get_data_itemprop_from_assoc( $mt_offer, array( 
							'url' => 'product:offer:url',
							'name' => 'product:offer:title',
							'sku' => 'product:offer:sku',
							'color' => 'product:offer:color',
							'category' => 'product:offer:category',
							'description' => 'product:offer:description',
						) ) ) !== false ) {

							WpssoSchema::add_data_quant_from_assoc( $product, $mt_offer, array( 
								'width' => 'product:offer:width',
								'height' => 'product:offer:height',
								'length' => 'product:offer:length',
								'weight' => 'product:offer:weight',
							) );

							// add the product variation image
							if ( ! empty( $mt_offer['product:offer:image:id'] ) ) {
								$size_name = $this->p->cf['lca'].'-schema';
								$og_image = $this->p->media->get_attachment_image( 1, 
									$size_name, $mt_offer['product:offer:image:id'], false );

								if ( ! empty( $og_image ) ) {
									if ( ! WpssoSchema::add_image_list_data( $product['image'], $og_image, 'og:image' ) ) {
										unset( $product['image'] );	// prevent null assignment
									}
								}
							}

							// add the product variation to the offer
							$offer['itemOffered'] = WpssoSchema::get_item_type_context( 'http://schema.org/IndividualProduct', $product );
						}

						// add the complete offer
						$ret['offers'][] = WpssoSchema::get_item_type_context( 'http://schema.org/Offer', $offer );
					}
				}
			}

			/*
			 * Property:
			 *	image as http://schema.org/ImageObject
			 *	video as http://schema.org/VideoObject
			 */
			WpssoJsonSchema::add_media_data( $ret, $mod, $mt_og, $user_id );

			return WpssoSchema::return_data_from_filter( $json_data, $ret );
		}
	}
}

?>
