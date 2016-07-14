<?php
/*
 * License: GPLv3
 * License URI: http://www.gnu.org/licenses/gpl.txt
 * Copyright 2014-2016 Jean-Sebastien Morisset (http://surniaulula.com/)
 */

if ( ! defined( 'ABSPATH' ) ) 
	die( 'These aren\'t the droids you\'re looking for...' );

if ( ! class_exists( 'WpssoJsonFilters' ) ) {

	class WpssoJsonFilters {

		protected $p;

		public function __construct( &$plugin ) {
			$this->p =& $plugin;

			if ( $this->p->debug->enabled )
				$this->p->debug->mark();

			add_filter( 'amp_post_template_metadata', 
				array( &$this, 'filter_amp_post_template_metadata' ), 9000, 2 );

			$this->p->util->add_plugin_filters( $this, array(
				'add_schema_head_attributes' => '__return_false',
				'add_schema_meta_array' => '__return_false',
				'add_schema_noscript_array' => '__return_false',
				'json_data_http_schema_org' => 6,			// $json_data, $use_post, $mod, $mt_og, $user_id, $is_main
			), -100 );	// make sure we run first

			if ( is_admin() ) {
				$this->p->util->add_plugin_actions( $this, array(
					'admin_post_header' => 1,			// $mod
				) );
				$this->p->util->add_plugin_filters( $this, array(
					'option_type' => 2,
					'save_post_options' => 4,			// $opts, $post_id, $rel_id, $mod
					'get_md_defaults' => 2,				// $def_opts, $mod
					'pub_google_rows' => 2,				// $table_rows, $form
					'messages_tooltip_meta' => 2,			// tooltip messages for post social settings
				) );
				$this->p->util->add_plugin_filters( $this, array(
					'status_gpl_features' => 3,			// $features, $lca, $info
					'status_pro_features' => 3,			// $features, $lca, $info
				), 10, 'wpssojson' );
			}
		}

		public function filter_amp_post_template_metadata( $metadata, $post_obj ) {
			return array();	// remove the AMP json data to prevent duplicate JSON-LD blocks
		}

		/*
		 * Common filter for all Schema types.
		 *
		 * Adds the url, name, description, and if true, the main entity property. 
		 * Does not add images, videos, author or organization markup since this will
		 * depend on the Schema type (Article, Product, Place, etc.).
		 */
		public function filter_json_data_http_schema_org( $json_data, $use_post, $mod, $mt_og, $user_id, $is_main ) {
			if ( $this->p->debug->enabled )
				$this->p->debug->mark();

			$lca = $this->p->cf['lca'];
			$ret = WpssoSchema::get_item_type_context( $mt_og['schema:type:url'] );

			/*
			 * Property:
			 *	url
			 */
			WpssoSchema::add_data_itemprop_from_assoc( $ret, $mt_og, array( 'url' => 'og:url' ) );

			/*
			 * Property:
			 *	name
			 *
			 * get_title( $textlen = 70, $trailing = '', $use_post = false, $use_cache = true,
			 *	$add_hashtags = false, $encode = true, $md_idx = 'og_title' ) {
			 */
			$ret['name'] = $this->p->webpage->get_title( $this->p->options['og_title_len'], 
				'...', $mod, true, false, true, 'schema_title' );

			/*
			 * Property:
			 *	description
			 *
			 * get_description( $textlen = 156, $trailing = '...', $use_post = false, $use_cache = true,
			 *	$add_hashtags = true, $encode = true, $md_idx = 'og_desc' )
			 */
			$ret['description'] = $this->p->webpage->get_description( $this->p->options['schema_desc_len'], 
				'...', $mod, true, false, true, 'schema_desc' );

			return WpssoSchema::return_data_from_filter( $json_data, $ret, $is_main );
		}

		public function action_admin_post_header( $mod ) {

			if ( ! current_user_can( 'manage_options' ) )
				return;

			$urls = $this->p->cf['plugin']['wpssojson']['url'];	// for purchase and pro_support urls
			$type_id = $this->p->schema->get_head_item_type( $mod, true );
			$type_url = $this->p->schema->get_schema_type_url( $type_id );
			$filter_name = $this->p->schema->get_json_data_filter( $mod, $type_url );
			$message = '';

			if ( has_filter( $filter_name ) )
				return;

			if ( ! $this->p->check->aop( 'wpssojson', true, $this->p->is_avail['aop'] ) ) {
				$dismiss_id = 'filter_in_pro_'.$filter_name.'_'.$mod['name'].'_'.$mod['id'];
				$message = sprintf( __( 'The Free / Basic version of WPSSO JSON does not include support for the Schema type <a href="%1$s">%1$s</a> &mdash; only the basic Schema properties <em>url</em>, <em>name</em>, and <em>description</em> will be included in the Schema JSON-LD markup.', 'wpsso-schema-json-ld' ), $type_url ).' '.sprintf( __( 'The <a href="%1$s">Pro version of WPSSO JSON</a> includes a wide selection of supported Schema types, including the Schema type <a href="%2$s">%2$s</a>.', 'wpsso-schema-json-ld' ), $urls['purchase'], $type_url ).' '.sprintf( __( 'If this Schema is an important classification for your content, you should consider purchasing the Pro version.', 'wpsso-schema-json-ld' ), $type_url );
			}

			if ( ! empty( $message ) )
				$this->p->notice->warn( '<em>'.__( 'This notice is only shown to users with Administrative privileges.',
					'wpsso-schema-json-ld' ).'</em><p>'.$message.'</p>', true, true, $dismiss_id, true );
		}

		public function filter_option_type( $type, $key ) {

			if ( ! empty( $type ) )
				return $type;
			elseif ( strpos( $key, 'schema_' ) !== 0 )
				return $type;

			switch ( $key ) {
				case 'schema_type':
				case 'schema_review_item_type':
					return 'not_blank';
					break;
				case 'schema_review_rating':
				case 'schema_review_rating_from':
				case 'schema_review_rating_to':
					return 'blank_num';	// must be numeric (blank or zero is ok)
					break;
				case 'schema_review_item_url':
					return 'url';
					break;
			}
			return $type;
		}

		public function filter_save_post_options( $opts, $post_id, $rel_id, $mod ) {

			$defs = $this->filter_get_md_defaults( array(), $mod );	// only get the schema event options

			if ( empty( $opts['schema_review_rating'] ) ) {
				foreach ( array( 
					'schema_review_rating',
					'schema_review_rating_from',
					'schema_review_rating_to',
				) as $key )
					unset( $opts[$key] );
			} else {
				foreach ( array( 
					'schema_review_rating_from',
					'schema_review_rating_to',
				) as $key )
					if ( empty( $opts[$key] ) && 
						isset( $defs[$key] ) )
							$opts[$key] = $defs[$key];
			}

			return $opts;
		}

		public function filter_get_md_defaults( $def_opts, $mod ) {

			return array_merge( $def_opts, array(
				'schema_is_main' => 1,
				'schema_type' => $this->p->schema->get_head_item_type( $mod, true, false ),	// $return_id = true, $use_mod_opts = false
				'schema_title' => '',
				'schema_desc' => '',
				'schema_pub_org_id' => 'site',		// Article Publisher
				'schema_headline' => '',		// Article Headline
				'schema_event_org_id' => 'none',	// Event Organizer
				'schema_event_perf_id' => 'none',	// Event Performer
				'schema_review_item_type' => 'none',	// Reviewed Item Type
				'schema_review_item_url' => '',		// Reviewed Item URL
				'schema_review_rating' => '0.0',	// Reviewed Item Rating
				'schema_review_rating_from' => '1',	// Reviewed Item Rating (from)
				'schema_review_rating_to' => '5',	// Reviewed Item Rating (to)
			) );
		}

		public function filter_pub_google_rows( $table_rows, $form ) {
			foreach ( array_keys( $table_rows ) as $key ) {
				switch ( $key ) {
					case 'schema_add_noscript':
					case 'schema_social_json':
						break;
					case 'subsection_google_schema':
					case ( strpos( $key, 'schema_' ) === 0 ? true : false ):
						unset( $table_rows[$key] );
						break;
				}
			}
			return $table_rows;
		}

		// hooked to 'wpssojson_status_gpl_features'
		public function filter_status_gpl_features( $features, $lca, $info ) {
			foreach ( $info['lib']['gpl'] as $sub => $libs ) {
				if ( $sub === 'admin' ) // skip status for admin menus and tabs
					continue;
				foreach ( $libs as $id_key => $label ) {
					list( $id, $stub, $action ) = SucomUtil::get_lib_stub_action( $id_key );
					$classname = SucomUtil::sanitize_classname( 'wpssojsongpl'.$sub.$id, false );	// $underscore = false
					$features[$label] = array( 'status' => class_exists( $classname ) ? 'on' : 'off' );
				}
			}
			return $this->filter_common_status_features( $features, $lca, $info );
		}

		public function filter_messages_tooltip_meta( $text, $idx ) {
			if ( strpos( $idx, 'tooltip-meta-schema_' ) !== 0 )
				return $text;

			switch ( $idx ) {
				case 'tooltip-meta-schema_is_main':
					$text = __( 'Check this option if the Schema markup describes the main content (aka "main entity") of this webpage.', 'wpsso-schema-json-ld' );
				 	break;
				case 'tooltip-meta-schema_type':
					$text = __( 'Select a Schema item type that best describes the main content of this webpage.', 'wpsso-schema-json-ld' );
				 	break;
				case 'tooltip-meta-schema_pub_org_id':
					$text = __( 'Select a publisher for the Schema Article item type and/or its sub-type (NewsArticle, TechArticle, etc).', 'wpsso-schema-json-ld' );
				 	break;
				case 'tooltip-meta-schema_headline':
					$text = __( 'A custom headline for the Schema Article item type and/or its sub-type. The headline Schema property is not added for non-Article item types.', 'wpsso-schema-json-ld' );
				 	break;
				case 'tooltip-meta-schema_event_org_id':
					$text = __( 'Select an organizer for the Schema Event item type and/or its sub-type (Festival, MusicEvent, etc).', 'wpsso-schema-json-ld' );
				 	break;
				case 'tooltip-meta-schema_event_perf_id':
					$text = __( 'Select a performer for the Schema Event item type and/or its sub-type (Festival, MusicEvent, etc).', 'wpsso-schema-json-ld' );
				 	break;
				case 'tooltip-meta-schema_review_item_type':
					$text = __( 'Select a Schema item type that best describes the item being reviewed.', 'wpsso-schema-json-ld' );
				 	break;
				case 'tooltip-meta-schema_review_item_url':
					$text = __( 'A URL for the item being reviewed.', 'wpsso-schema-json-ld' );
				 	break;
				case 'tooltip-meta-schema_review_rating':
					$text = __( 'A rating for the item being reviewed, along with the low / high rating scale (defaults are 1 to 5).', 'wpsso-schema-json-ld' );
				 	break;
			}
			return $text;
		}

		// hooked to 'wpssojson_status_pro_features'
		public function filter_status_pro_features( $features, $lca, $info ) {
			return $this->filter_common_status_features( $features, $lca, $info );
		}

		private function filter_common_status_features( $features, $lca, $info ) {
			foreach ( $features as $key => $arr )
				if ( preg_match( '/^\(([a-z\-]+)\) (Schema Type .+) \((.+)\)$/', $key, $match ) )
					$features[$key]['label'] = $match[2].' ('.$this->p->schema->count_schema_type_children( $match[3] ).')';
			return $features;
		}
	}
}

?>
