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

if ( ! class_exists( 'WpssoProSeoAioseop' ) ) {

	class WpssoProSeoAioseop {

		private $p;
		private $opts;

		public function __construct( &$plugin ) {
			$this->p =& $plugin;

			$this->p->util->add_plugin_filters( $this, array( 
				'title_seed' => 2,
				'description_seed' => 2,
				'tags_seed' => 2,
			), WPSSO_SEO_FILTERS_PRIORITY );

			$this->opts = get_option( 'aioseop_options' );
		}

		public function filter_title_seed( $title, $mod ) {

			// skip if we're not using filtered / seo titles
			if ( empty( $this->p->options['plugin_filter_title'] ) )
				return $title;

			if ( $mod['is_home'] && 
				! empty( $this->opts['aiosp_home_title' ] ) )
					$title = $this->opts['aiosp_home_title'];
			elseif ( $mod['is_post'] )
				$title = get_post_meta( $mod['id'], '_aioseop_title', true );

			return $title;
		}

		public function filter_description_seed( $desc, $mod ) {

			if ( $mod['is_home'] && 
				! empty( $this->opts['aiosp_home_description' ] ) )
					$desc = $this->opts['aiosp_home_description'];
			elseif ( $mod['is_post'] )
				$desc = get_post_meta( $mod['id'], '_aioseop_description', true );

			return $desc;
		}

		public function filter_tags_seed( $tags, $post_id ) {
			$keywords = '';
			if ( is_home() || SucomUtil::is_home_page( $post_id ) && 
				! empty( $this->opts['aiosp_home_keywords' ] ) )
					$keywords = $this->opts['aiosp_home_keywords'];
			else {
				$post_obj = SucomUtil::get_post_object( $post_id );
				if ( ! empty( $post_obj->ID ) )
					$keywords = get_post_meta( $post_obj->ID, '_aioseop_keywords', true );
			}
			if ( ! empty( $keywords ) )
				$tags = explode( ',', $keywords );
			return $tags;
		}
	}
}

?>
