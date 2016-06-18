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
					'get_md_defaults' => 2,				// $def_opts, $mod
					'pub_google_rows' => 2,				// $table_rows, $form
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
				$this->p->notice->err( '<em>'.__( 'This notice is only shown to users with Administrative privileges.',
					'wpsso-schema-json-ld' ).'</em><p>'.$message.'</p>', true, true, $dismiss_id, true );
		}

		public function filter_get_md_defaults( $def_opts, $mod ) {
			return array_merge( $def_opts, array(
				'schema_is_main' => 1,
				'schema_type' => $this->p->schema->get_head_item_type( $mod, true, false ),	// $return_id = true, $use_mod_opts = false
				'schema_title' => '',
				'schema_headline' => '',
				'schema_pub_org_id' => 'site',
				'schema_desc' => '',
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
					$classname = SucomUtil::sanitize_classname( 'wpssojsongpl'.$sub.$id );
					$features[$label] = array( 'status' => class_exists( $classname ) ? 'on' : 'off' );
				}
			}
			return $this->filter_common_status_features( $features, $lca, $info );
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
