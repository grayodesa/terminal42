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

if ( ! class_exists( 'WpssoProSeoAutoDescription' ) ) {

	class WpssoProSeoAutoDescription {

		private $p;

		public function __construct( &$plugin ) {
			$this->p =& $plugin;

			$this->p->util->add_plugin_filters( $this, array( 
				'title_seed' => 2, 
				'description_seed' => 2, 
				'post_url' => 2,
			), WPSSO_SEO_FILTERS_PRIORITY );

			add_filter( 'the_seo_framework_current_object_id',			// since the SEO framework v2.6.2
				array( &$this, 'current_object_id' ), 10, 1 );

			add_filter( 'the_seo_framework_ldjson_scripts',
				'__return_empty_string' );
		}

		public function filter_title_seed( $title, $mod ) {
			$the_seo_framework = the_seo_framework();
			if ( is_admin() && $mod['is_post'] )
				SucomUtil::maybe_load_post( $mod['id'] );
			$title = $the_seo_framework->title();
			return $title;
		}

		public function filter_description_seed( $description, $mod ) {
			$the_seo_framework = the_seo_framework();
			if ( is_admin() && $mod['is_post'] )
				SucomUtil::maybe_load_post( $mod['id'] );
			$desc = $the_seo_framework->generate_description( '', array( 'social' => true ) );
			return $desc;
		}

		public function filter_post_url( $url, $mod ) {
			$the_seo_framework = the_seo_framework();
			if ( is_admin() && $mod['id'] )
				SucomUtil::maybe_load_post( $mod['id'] );
			$url = $the_seo_framework->the_url_from_cache();
			return $url;
		}

		public function current_object_id( $id ) {
			if ( empty( $id ) ) {
				$lca = $this->p->cf['lca'];
				$use_post = apply_filters( $lca.'_header_use_post', false );	// used by woocommerce with is_shop()
				$mod = $this->p->util->get_page_mod( $use_post );		// get post/user/term id, module name, and module object reference
				$id = $mod['id'];
			}
			return $id;
		}
	}
}

?>
