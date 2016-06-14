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

if ( ! class_exists( 'WpssoProUtilLanguage' ) ) {

	class WpssoProUtilLanguage {

		private $p;

		public function __construct( &$plugin ) {
			$this->p =& $plugin;
			$this->p->util->add_plugin_filters( $this, array(
				'pub_lang' => 3,
			) );
		}

		public function filter_pub_lang( $ret_lang, $pub_lang, $mixed = 'current' ) {

			if ( is_string( $pub_lang ) ) {
				$pub_lang = SucomUtil::get_pub_lang( $pub_lang );
			} elseif ( ! is_array( $pub_lang ) )
				return $ret_lang;

			// returns the WP language as 'en' or 'en_US'
			$locale = $fb_lang = SucomUtil::get_locale( $mixed );
			if ( $this->p->debug->enabled )
				$this->p->debug->log( 'get_locale returned: '.$locale );

			// all facebook languages are formatted 'en_US', so correct known two letter locales
			if ( strlen( $fb_lang ) == 2 ) {
				switch ( $fb_lang ) {
					case 'el':
						$fb_lang = 'el_GR';
						break;
					default:
						// fr to fr_FR, for example
						$fb_lang = $fb_lang.'_'.strtoupper( $fb_lang );
						break;
				}
				if ( $this->p->debug->enabled )
					$this->p->debug->log( 'fb_lang changed to: '.$fb_lang );
			}

			// check for complete en_US format (facebook)
			if ( isset( $pub_lang[$fb_lang] ) ) {
				$ret_lang = $fb_lang;
				if ( $this->p->debug->enabled )
					$this->p->debug->log( 'underscore locale found: '.$ret_lang );
			}

			// hyphen instead of underscore (google)
			if ( ( $locale = preg_replace( '/_/', '-', $locale ) ) && isset( $pub_lang[$locale] ) ) {
				$ret_lang = $locale;
				if ( $this->p->debug->enabled )
					$this->p->debug->log( 'hyphen locale found: '.$ret_lang );
			}

			// lowercase with hyphen (twitter)
			if ( ( $locale = strtolower( $locale ) ) && isset( $pub_lang[$locale] ) ) {
				$ret_lang = $locale;
				if ( $this->p->debug->enabled )
					$this->p->debug->log( 'lowercase locale found: '.$ret_lang );
			}

			// two-letter lowercase format (google and twitter)
			if ( ( $locale = preg_replace( '/[_-].*$/', '', $locale ) ) && isset( $pub_lang[$locale] ) ) {
				$ret_lang = $locale;
				if ( $this->p->debug->enabled )
					$this->p->debug->log( 'two-letter locale found: '.$ret_lang );
			}

			return $ret_lang;
		}
	}
}

?>
