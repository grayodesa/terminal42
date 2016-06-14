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

if ( ! class_exists( 'WpssoProSeoHeadspace2' ) ) {

	class WpssoProSeoHeadspace2 {

		private $p;
		private $hs;
		private $hs_it;
		private $opts = null;

		public function __construct( &$plugin ) {
			$this->p =& $plugin;

			$this->p->util->add_plugin_filters( $this, array( 
				'title_seed' => 1, 
				'description_seed' => 1, 
			), WPSSO_SEO_FILTERS_PRIORITY );
		}

		public function get_options( $idx = false ) {
			if ( ! is_array( $this->opts ) ) {
				$this->hs = new HeadSpace2;
				$this->hs_it = new HS_InlineTags;
				$this->opts = $this->hs->get_current_settings();
			}
			if ( $idx !== false ) {
				if ( isset( $this->opts[$idx] ) ) {
					if ( is_admin() && strpos( $this->opts[$idx], '%%' ) !== false ) {
						$post_obj = SucomUtil::get_post_object();
						return $this->hs_it->replace( $this->opts[$idx], $post_obj );
					} else return $this->opts[$idx];
				} else return null;
			} else return $this->opts;
		}

		public function filter_title_seed( $title ) {
			// skip if we're not using filtered / SEO titles
			if ( empty( $this->p->options['plugin_filter_title'] ) )
				return $title;
			return $this->get_options( 'page_title' );
		}

		public function filter_description_seed( $desc ) {
			return $this->get_options( 'description' );
		}
	}
}

?>
