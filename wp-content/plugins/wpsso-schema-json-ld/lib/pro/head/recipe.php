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

if ( ! class_exists( 'WpssoJsonProHeadRecipe' ) ) {

	class WpssoJsonProHeadRecipe {

		protected $p;

		public function __construct( &$plugin ) {
			$this->p =& $plugin;
			if ( $this->p->debug->enabled )
				$this->p->debug->mark();

			$this->p->util->add_plugin_filters( $this, array(
				'schema_meta_itemprop' => 4,			// $mt_schema, $mod, $mt_og, $head_type_url
				'json_data_http_schema_org_recipe' => 4,	// $json_data, $mod, $mt_og, $user_id
			) );
		}

		public function filter_schema_meta_itemprop( $mt_schema, $mod, $mt_og, $head_type_url ) {

			if ( $head_type_url !== 'http://schema.org/Recipe' )
				return $mt_schema;

			if ( $this->p->debug->enabled )
				$this->p->debug->mark();

			$json_data = $this->filter_json_data_http_schema_org_recipe( array(), $mod, $mt_og, false );

			WpssoSchema::add_data_itemprop_from_assoc( $mt_schema, $json_data, array(
				'prepTime' => 'prepTime',
				'cookTime' => 'cookTime',
				'totalTime' => 'totalTime',
				'recipeYield' => 'recipeYield',
				'ingredients' => 'recipeIngredient',
			) );

			return $mt_schema;
		}

		public function filter_json_data_http_schema_org_recipe( $json_data, $mod, $mt_og, $user_id ) {

			if ( $this->p->debug->enabled )
				$this->p->debug->mark();

			$ret = array();

			if ( is_object( $mod['obj'] ) ) {	// just in case
				$md_opts = SucomUtil::keys_start_with( 'schema_recipe_', array_merge( 
					(array) $mod['obj']->get_defaults( $mod['id'] ), 
					(array) $mod['obj']->get_options( $mod['id'] )
				) );
			} else $md_opts = array();

			/*
			 * Property:
			 * 	prepTime
			 * 	cookTime
			 * 	totalTime
			 */
			foreach ( array( 'prep', 'cook', 'total' ) as $stage ) {
				$t = array();
				foreach ( array( 'days', 'hours', 'mins', 'secs' ) as $period )
					$t[$period] = isset( $md_opts['schema_recipe_'.$stage.'_'.$period] ) ?
						(int) $md_opts['schema_recipe_'.$stage.'_'.$period] : 0;
				if ( $t['days'].$t['hours'].$t['mins'].$t['secs'] > 0 )
					$ret[$stage.'Time'] = 'P'.$t['days'].'DT'.$t['hours'].'H'.$t['mins'].'M'.$t['secs'].'S';
			}

			/*
			 * Property:
			 * 	nutrition
			 */
			$ret['nutrition'] = WpssoSchema::get_item_type_context( 'http://schema.org/NutritionInformation', 
				array( 'calories' => isset( $md_opts['schema_recipe_calories'] ) ? 
					(int) $md_opts['schema_recipe_calories'] : 0 ) );

			/*
			 * Property:
			 * 	recipeYield
			 */
			$ret['recipeYield'] = isset( $md_opts['schema_recipe_yield'] ) ?
				(string) $md_opts['schema_recipe_yield'] : '';

			/*
			 * Property:
			 * 	ingredients
			 */
			foreach ( SucomUtil::preg_grep_keys( '/^schema_recipe_ingredient_[0-9]+$/',	// exclude ':is' option suffix
				$md_opts ) as $key => $value )
					$ret['recipeIngredient'][] = $value;

			return WpssoSchema::return_data_from_filter( $json_data, $ret );
		}
	}
}

?>
