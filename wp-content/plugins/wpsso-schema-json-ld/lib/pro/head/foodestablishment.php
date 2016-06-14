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

if ( ! class_exists( 'WpssoJsonProHeadFoodEstablishment' ) ) {

	class WpssoJsonProHeadFoodEstablishment {

		protected $p;

		public function __construct( &$plugin ) {
			$this->p =& $plugin;
			if ( $this->p->debug->enabled )
				$this->p->debug->mark();

			$this->p->util->add_plugin_filters( $this, array(
				'json_data_http_schema_org_foodestablishment' => 5,	// $json_data, $use_post, $mod, $mt_og, $user_id
			) );
		}

		/*
		 * http://schema.org/Bakery
		 * http://schema.org/BarOrPub
		 * http://schema.org/Brewery
		 * http://schema.org/CafeOrCoffeeShop
		 * http://schema.org/FastFoodRestaurant
		 * http://schema.org/FoodEstablishment
		 * http://schema.org/IceCreamShop
		 * http://schema.org/Restaurant
		 * http://schema.org/Winery
		 */
		public function filter_json_data_http_schema_org_foodestablishment( $json_data, $use_post, $mod, $mt_og, $user_id ) {

			if ( $this->p->debug->enabled )
				$this->p->debug->mark();

			$lca = $this->p->cf['lca'];
			$ret = array();

			WpssoSchema::add_data_itemprop_from_og( $ret, $mt_og, array( 
				'menu' => 'place:business:menu_url',
				'acceptsReservations' => 'place:business:accepts_reservations',
			) );

			return WpssoSchema::return_data_from_filter( $json_data, $ret );
		}
	}
}

?>
