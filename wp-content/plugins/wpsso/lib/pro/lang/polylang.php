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

if ( ! class_exists( 'WpssoProLangPolylang' ) ) {

	class WpssoProLangPolylang {

		private $p;

		public function __construct( &$plugin ) {
			$this->p =& $plugin;
			$this->p->util->add_plugin_filters( $this, array( 'home_url' => 1 ) );
			$this->p->util->add_plugin_filters( $this, array( 'locale' => 2 ), 10, 'sucom' );
		}

		public function filter_home_url( $url ) {
			if ( function_exists( 'pll_home_url' ) )
				return pll_home_url();
			else return $url;
		}

		// argument can also be a numeric post ID, to return the language of that post
		public function filter_locale( $wp_locale, $mixed = 'current' ) {
			$pll_locale = '';
			switch ( true ) {
				case ( is_array( $mixed ) ):
					if ( $mixed['is_post'] )
						$pll_locale = $this->get_post_language( $mixed['id'] );
					elseif ( $mixed['is_term'] )
						$pll_locale = $this->get_term_language( $mixed['id'] );

					if ( $this->p->debug->enabled )
						$this->p->debug->log( 'pll_locale for '.$mixed['name'].' ID '.
							$mixed['id'].' = '.$pll_locale );
					break;

				case ( is_int( $mixed ) && $mixed > 0 ):
					$pll_locale = $this->get_post_language( $mixed );

					if ( $this->p->debug->enabled )
						$this->p->debug->log( 'pll_locale for post integer '.
							$mixed.' = '.$pll_locale );
					break;

				case ( $mixed === 'default' ):
					if ( function_exists( 'pll_default_language' ) )
						$pll_locale = pll_default_language( 'locale' );
					elseif ( $this->p->debug->enabled )
						$this->p->debug->log( 'pll_default_language function not found' );
					break;

				default:
					if ( function_exists( 'pll_current_language' ) )
						$pll_locale = pll_current_language( 'locale' );
					elseif ( $this->p->debug->enabled )
						$this->p->debug->log( 'pll_current_language function not found' );
					break;
			}

			if ( ! empty( $pll_locale ) )
				return $pll_locale;
			else return $wp_locale;
		}

		private function get_post_language( $id ) {
			$pll_locale = '';
			if ( function_exists( 'pll_get_post_language' ) )	// since v1.5.4
				$pll_locale = pll_get_post_language( $id, 'locale' );
			else {
				global $polylang;
				$pll_lang = $polylang->model->get_post_language( $id );
				if ( ! empty( $pll_lang ) )
					$pll_locale = $pll_lang->locale;
			}
			return $pll_locale;
		}

		private function get_term_language( $id ) {
			$pll_locale = '';
			if ( function_exists( 'pll_get_term_language' ) )	// since v1.5.4
				$pll_locale = pll_get_term_language( $id, 'locale' );
			else {
				global $polylang;
				$pll_lang = $polylang->model->get_term_language( $id );
				if ( ! empty( $pll_lang ) )
					$pll_locale = $pll_lang->locale;
			}
			return $pll_locale;
		}
	}
}

?>
