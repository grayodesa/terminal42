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

if ( ! class_exists( 'WpssoProUtilShorten' ) ) {

	class WpssoProUtilShorten {

		private $p;
		private $svc = array();

		public function __construct( &$plugin ) {
			$this->p =& $plugin;

			if ( $this->p->debug->enabled )
				$this->p->debug->mark();

			if ( extension_loaded( 'curl' ) ) {
				if ( ! empty( $this->p->options['plugin_shortener'] ) && 
					$this->p->options['plugin_shortener'] !== 'none' ) {

					$this->p->util->add_plugin_filters( $this, array( 
						'shorten_url' => 2,
						'post_cache_transients' => 4,
					) );
				}
			} elseif ( $this->p->debug->enabled )
				$this->p->debug->log( 'curl extension not available: shortening disabled' );
		}

		public function filter_shorten_url( $long_url, $service = '' ) {

			$short_url = $this->get_short( $long_url, $service );

			if ( empty( $short_url ) )
				return $long_url;
			else return $short_url;
		}

		public function filter_post_cache_transients( $transients, $post_id, $locale, $sharing_url ) {
			foreach( $this->p->cf['form']['shorteners'] as $service => $name )
				$transients[__CLASS__.'::get_short'][] = 'service:'.$service.'_url:'.$sharing_url;
			return $transients;
		}

		private function set_objects( $service ) {

			$this->svc[$service] = false;	// make sure a service key exists
			$lib_file = WPSSO_PLUGINDIR.'lib/pro/ext/'.$service.'.php';

			if ( file_exists( $lib_file ) )
				require_once( $lib_file );	// load the class library
			elseif ( $this->p->debug->enabled )
				$this->p->debug->log( 'missing shortening library file: '.$lib_file );

			switch ( $service ) {
				case 'bitly':
					if ( empty( $this->p->options['plugin_bitly_login' ] ) ) {
						if ( $this->p->debug->enabled )
							$this->p->debug->log( 'bitly login option is empty' );
						break;
					} elseif ( empty( $this->p->options['plugin_bitly_token' ] ) && 
						empty( $this->p->options['plugin_bitly_api_key' ] ) ) {
						if ( $this->p->debug->enabled )
							$this->p->debug->log( 'bitly token and api_key options are empty' );
						break;
					} elseif ( class_exists( 'SuextBitly' ) ) {
						$this->svc[$service] = new SuextBitly( 
							$this->p->options['plugin_bitly_login'],
							$this->p->options['plugin_bitly_token'], 
							$this->p->options['plugin_bitly_api_key'], 
							$this->p->debug
						);
					} elseif ( $this->p->debug->enabled ) {
						$this->p->debug->log( 'SuextBitly class is missing' );
					}
					break;

				case 'googl':
					if ( empty( $this->p->options['plugin_google_api_key'] ) ||
						empty( $this->p->options['plugin_google_shorten'] ) ) {
						if ( $this->p->debug->enabled )
							$this->p->debug->log( 'google api_key and/or shorten options empty' );
						break;
					} elseif ( class_exists( 'SuextGoogl' ) ) {
						$this->svc[$service] = new SuextGoogl( $this->p->options['plugin_google_api_key'],
							$this->p->debug );
					} elseif ( $this->p->debug->enabled ) {
						$this->p->debug->log( 'SuextGoogl class missing' );
					}
					break;

				case 'owly':
					if ( empty( $this->p->options['plugin_owly_api_key'] ) ) {
						if ( $this->p->debug->enabled )
							$this->p->debug->log( 'owly api_key option empty' );
						break;
					} elseif ( class_exists( 'SuextOwly' ) ) {
						$this->svc[$service] = SuextOwly::factory( array(
							'key' => $this->p->options['plugin_owly_api_key'],
							'protocol' => 'https:',
						) );
					} elseif ( $this->p->debug->enabled ) {
						$this->p->debug->log( 'SuextOwly class missing' );
					}
					break;

				case 'tinyurl':
					if ( class_exists( 'SuextTinyUrl' ) ) {
						$this->svc[$service] = new SuextTinyUrl();
						$this->svc[$service]->setTimeOut( 15 );
						$this->svc[$service]->setUserAgent( $this->p->cf['plugin'][$this->p->cf['lca']]['short'].'/'.
							$this->p->cf['plugin'][$this->p->cf['lca']]['version'] );
					} elseif ( $this->p->debug->enabled ) {
						$this->p->debug->log( 'SuextTinyUrl class missing' );
					}
					break;

				case 'yourls':
					// current url without the query string
					$current_url = strtok( SucomUtil::get_prot().'://'.$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'], '?' );
					if ( empty( $this->p->options['plugin_yourls_api_url'] ) ) {
						if ( $this->p->debug->enabled )
							$this->p->debug->log( 'yourls api_url option missing' );
						break;
					} elseif ( $this->p->options['plugin_yourls_api_url'] === $current_url ) {
						if ( $this->p->debug->enabled )
							$this->p->debug->log( 'loop detected: service URL is current URL ('.$current_url.')' );
						break;
					// username, password, and token are optional for public shortening configs
					} elseif ( class_exists( 'SuextYourls' ) ) {
						$this->svc[$service] = new SuextYourls( $this->p->options['plugin_yourls_api_url'],
							$this->p->options['plugin_yourls_username'],
							$this->p->options['plugin_yourls_password'],
							$this->p->options['plugin_yourls_token'] );
					} elseif ( $this->p->debug->enabled ) {
						$this->p->debug->log( 'SuextYourls class missing' );
					}
					break;

				default:
					if ( $this->p->debug->enabled )
						$this->p->debug->log( 'unknown shortening service name: '.$service );
					break;
			}

			if ( $this->svc[$service] === false ) {
				if ( is_admin() )
					$this->p->notice->err( sprintf( __( 'Incomplete and/or missing %s service API credentials for URL shortening.',
						'wpsso' ), $this->p->cf['form']['shorteners'][$service] ), true );
				if ( $this->p->debug->enabled )
					$this->p->debug->log( 'incomplete / missing '.$service.' credentials for shortening' );
			}
		}

		public function get_short( $long_url, $service = '' ) {

			if ( strlen( $long_url ) < $this->p->options['plugin_min_shorten'] ) {
				if ( $this->p->debug->enabled )
					$this->p->debug->log( 'exiting early: url is shorter than '.$this->p->options['plugin_min_shorten'].' chars' );
				return $long_url;

			} elseif ( empty( $service ) || $service === 'none' ) {
				if ( $this->p->debug->enabled )
					$this->p->debug->log( 'exiting early: service name is empty' );
				return $long_url;

			} elseif ( ! extension_loaded( 'curl' ) ) {
				if ( $this->p->debug->enabled )
					$this->p->debug->log( 'exiting early: curl extension not available' );
				return $long_url;

			} elseif ( SucomUtil::get_const( $this->p->cf['uca'].'_PHP_CURL_DISABLE' ) )  {
				if ( $this->p->debug->enabled )
					$this->p->debug->log( 'exiting early: curl has been disabled' );
				return $long_url;
			}

			// cache the shortened url to minimize the number of lookups
			$cache_salt = __METHOD__.'(service:'.$service.'_url:'.$long_url.')';
			$cache_id = $this->p->cf['lca'].'_'.md5( $cache_salt );
			$cache_type = 'object cache';

			if ( $this->p->debug->enabled )
				$this->p->debug->log( $cache_type.': transient salt '.$cache_salt );

			$short_url = get_transient( $cache_id );

			if ( ! empty( $short_url ) ) {
				if ( $this->p->debug->enabled )
					$this->p->debug->log( $cache_type.': short_url retrieved from cache '.$cache_id );
				return $short_url;
			}

			if ( ! isset( $this->svc[$service] ) )
				$this->set_objects( $service );

			if ( ! is_object( $this->svc[$service] ) ) {
				if ( $this->p->debug->enabled )
					$this->p->debug->log( 'svc property for '.$service.' is not a valid object' );
				return $short_url;
			}

			if ( $this->p->debug->enabled )
				$this->p->debug->log( 'long url to shorten with '.$service.' = '.$long_url );

			switch ( $service ) {
				case 'bitly':
					$api_ret = $this->svc[$service]->shorten( $long_url );
					$short_url = empty( $api_ret['url'] ) ?
						false : $api_ret['url'];
					break;
				case 'googl':
				case 'owly':
				case 'yourls':
					$short_url = $this->svc[$service]->shorten( $long_url );
					break;
				case 'tinyurl':
					$short_url = $this->svc[$service]->create( $long_url );
					break;
				default:
					if ( $this->p->debug->enabled )
						$this->p->debug->log( 'unknown shortening service: '.$service );
					break;
			}

			if ( empty( $short_url ) ) {
				if ( $this->p->debug->enabled )
					$this->p->debug->log( 'failed to shorten long url = '.$long_url );
				$short_url = false;
			} else {
				if ( $this->p->debug->enabled )
					$this->p->debug->log( 'url successfully shortened = '.$short_url );

				set_transient( $cache_id, $short_url, $this->p->options['plugin_object_cache_exp'] );

				if ( $this->p->debug->enabled )
					$this->p->debug->log( $cache_type.': short_url saved to cache '.
						$cache_id.' ('.$this->p->options['plugin_object_cache_exp'].' seconds)' );
			}

			return $short_url;
		}
	}
}

?>
