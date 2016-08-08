<?php
/**
 * Bitly class
 *
 * Original API source from:
 *
 * 	@source		https://github.com/tijsverkoyen/Bitly
 * 	@author		Tijs Verkoyen <php-bitly@verkoyen.eu>
 * 	@version	2.0.2
 * 	@copyright	Copyright (c)2010, Tijs Verkoyen. All rights reserved.
 * 	@license	BSD License
 *
 * Additional oAuth modifications by:
 *
 * 	@author		Jean-Sebastien Morisset <jsm@surniaulula.com>
 * 	@copyright	Copyright (c)2016, Jean-Sebastien Morisset. All rights reserved.
 */

if ( ! defined( 'ABSPATH' ) ) 
	die( 'These aren\'t the droids you\'re looking for...' );

if ( ! class_exists( 'SuextBitly' ) ) {

	class SuextBitly {
	
		const VERSION = '20160715';
		const API_URL = 'http://api.bit.ly/v3/';
		const OAUTH_API_URL = 'https://api-ssl.bit.ly/v3/';
		const OAUTH_TOKEN_URL = 'https://api-ssl.bit.ly/oauth/';
	
		private $login;
		private $accessToken;
		private $apiKey;
		private $timeOut = 60;
		private $userAgent;
		private $debug;
	
		/**
		 * Default constructor
		 *
		 * @return	void
		 * @param	string $login	The login (username) that has to be used for authenticating.
		 * @param	string $apiKey	The API-key that has to be used for authentication (see http://bit.ly/account).
		 */
		public function __construct( $login, $accessToken = '', $apiKey = '', &$debug = '' ) {

			$this->setLogin( $login );
			$this->setAccessToken( $accessToken );
			$this->setApiKey( $apiKey );

			if ( is_object( $debug ) ) {
				$this->debug = $debug;
				if ( $this->debug->enabled )
					$this->debug->mark();
			} else $this->debug = new SuextBitlyNoDebug();
		}
	
		/**
		 * Make the call
		 *
		 * @return	string
		 * @param	string $url			The url to call.
		 * @param	array[optional] $aParameters	The parameters to pass.
		 */
		private function doCall( $url, $aParameters = array() ) {

			// redefine
			$url = (string) $url;
			$aParameters = (array) $aParameters;
	
			// add required parameters
			$aParameters['format'] = 'json';
			$aParameters['login'] = $this->getLogin();
			$aParameters['access_token'] = $this->getAccessToken();
			$aParameters['apiKey'] = $this->getApiKey();
	
			// init var
			$queryString = '';
	
			// loop parameters and add them to the queryString
			foreach ($aParameters as $key => $value )
				$queryString .= '&'.$key.'='.urlencode(utf8_encode($value));
	
			// cleanup querystring
			$queryString = trim($queryString, '&');
	
			// append to url
			$url .= '?' . $queryString;
	
			// prepend
			if ( empty( $aParameters['access_token'] ) )
				$url = self::API_URL.$url;
			else $url = self::OAUTH_API_URL.$url;
	
			// set options
			$options[CURLOPT_URL] = $url;
			$options[CURLOPT_USERAGENT] = $this->getUserAgent();

			if ( ! ini_get('safe_mode') && ! ini_get('open_basedir') ) {
				$options[CURLOPT_MAXREDIRS] = 10;
				$options[CURLOPT_FOLLOWLOCATION] = 1;
			}

			$options[CURLOPT_RETURNTRANSFER] = true;
			$options[CURLOPT_TIMEOUT] = (int) $this->getTimeOut();
	
			// init
			$curl = curl_init();
	
			// set options
			curl_setopt_array( $curl, $options );
	
			// execute
			$response = curl_exec( $curl );
			$headers = curl_getinfo( $curl );
	
			// fetch errors
			$errorNumber = curl_errno($curl);
			$errorMessage = curl_error($curl);
	
			// close
			curl_close($curl);
	
			// invalid headers
			if ( ! in_array( $headers['http_code'], array( 0, 200 ) ) ) {
				if ( $this->debug->enabled )
					$this->debug->log( 'invalid headers ('.$headers['http_code'].')' );
				return false;
			}
	
			// error?
			if($errorNumber != '') {
				if ( $this->debug->enabled )
					$this->debug->log( $errorMessage );
				return false;
			}
	
			// we expect JSON so decode it
			$json = @json_decode($response, true);
	
			// validate json
			if($json === false) {
				if ( $this->debug->enabled )
					$this->debug->log('invalid json-response');
				return false;
			}
	
			// is error?
			if(!isset($json['status_txt']) || (string) $json['status_txt'] != 'OK')
			{
				if ( $this->debug->enabled ) {
					if(isset($json['status_code']) && isset($json['status_txt']))	// bitly-error?
						$this->debug->log((string) $json['status_txt']);
					else $this->debug->log('invalid json-response');		// invalid json?
				}
				return false;
			}
	
			// return
			return $json;
		}


		/**
		 * Get the login
		 *
		 * @return	string
		 */
		private function getLogin() {
			return (string) $this->login;
		}


		/**
		 * Get the APIkey
		 *
		 * @return	string
		 */
		private function getAccessToken() {
			return (string) $this->accessToken;
		}


		/**
		 * Get the APIkey
		 *
		 * @return	string
		 */
		private function getApiKey() {
			return (string) $this->apiKey;
		}


		/**
		 * Get the timeout that will be used
		 *
		 * @return	int
		 */
		public function getTimeOut() {
			return (int) $this->timeOut;
		}
	
	
		/**
		 * Get the useragent that will be used. Our version will be prepended to yours.
		 * It will look like: "PHP Bitly/<version> <your-user-agent>"
		 *
		 * @return	string
		 */
		public function getUserAgent() {
			return (string) 'PHP '.__CLASS__.'/'.self::VERSION.
				( empty( $this->userAgent ) ? '' : ' '.$this->userAgent );
		}


		/**
		 * Set the login username to be used.
		 *
		 * @return	void
		 * @param	string $login		The login username to use.
		 */
		private function setLogin( $login ) {
			$this->login = (string) $login;
		}


		/**
		 * Set the Access Token to be used.
		 *
		 * @return	void
		 * @param	string $accessToken	The access token to set.
		 */
		private function setAccessToken( $accessToken ) {
			$this->accessToken = (string) $accessToken;
		}


		/**
		 * Set the API Key to be used.
		 *
		 * @return	void
		 * @param	string $apiKey		The api key to set.
		 */
		private function setApiKey( $apiKey ) {
			$this->apiKey = (string) $apiKey;
		}


		/**
		 * Set the timeout
		 * After this time the request will stop. You should handle any errors triggered by this.
		 *
		 * @return	void
		 * @param	int $seconds	The timeout in seconds.
		 */
		public function setTimeOut($seconds) {
			$this->timeOut = (int) $seconds;
		}
	
	
		/**
		 * Set the user-agent for you application
		 * It will be appended to ours, the result will look like: "PHP Bitly/<version> <your-user-agent>"
		 *
		 * @return	void
		 * @param	string $userAgent	Your user-agent, it should look like <app-name>/<app-version>.
		 */
		public function setUserAgent($userAgent)
		{
			$this->userAgent = (string) $userAgent;
		}
	
	
		/**
		 * Given a bit.ly URL or hash, expand decodes it and returns back the target URL.
		 *
		 * @return	array
		 * @param	string[optional] $shortURL	Refers to a bit.ly URL eg: http://bit.ly/1RmnUT.
		 * @param	string[optional] $hash		Refers to a bit.ly hash eg: 1RmnUT.
		 */
		public function expand( $shortURL = null, $hash = null ) {

			// redefine
			$shortURL = (string) $shortURL;
			$hash = (string) $hash;
	
			// validate
			if($shortURL == '' && $hash == '') {
				if ( $this->debug->enabled )
					$this->debug->log('shortURL or hash should contain a value');
				return false;
			}
	
			// make the call
			if($shortURL !== '') $parameters['shortUrl'] = $shortURL;
			if($hash !== '') $parameters['hash'] = $hash;
	
			// make the call
			$response = $this->doCall( 'expand', $parameters );
	
			// validate
			if(isset($response['data']['expand'][0]))
			{
				// init var
				$return = array();
	
				$return['url'] = $response['data']['expand'][0]['short_url'];
				$return['long_url'] = $response['data']['expand'][0]['long_url'];
				$return['hash'] = $response['data']['expand'][0]['user_hash'];
				$return['global_hash'] = $response['data']['expand'][0]['global_hash'];
	
				// return
				return $return;
			}
	
			// fallback
			return false;
		}
	
	
		/**
		 * For a long URL, shorten encodes a URL and returns a short one.
		 *
		 * @return	array
		 * @param	string $url	    			A long URL to shorten, eg: http://betaworks.com.
		 * @param	string[optional] $domain		Refers to a preferred domain, possible values are: bit.ly or j.mp.
		 * @param	string[optional] $endUserLogin		The end users login.
		 * @param	string[optional] $endUserApiKey		The end users apiKey.
		 */
		public function shorten( $url, $domain = null, $endUserLogin = null, $endUserApiKey = null ) {

			// domain specified
			if( $domain !== null && ! in_array((string) $domain, array('bit.ly', 'j.mp') ) ) {
				if ( $this->debug->enabled )
					$this->debug->log('invalid domain: custom domains are handled by using a correct login.');
				return false;
			}
	
			// redefine
			$parameters['longUrl'] = (string) $url;
			if( $domain !== null ) $parameters['domain'] = (string) $domain;
			if( $endUserLogin !== null ) $parameters['x_login'] = (string) $endUserLogin;
			if( $endUserApiKey !== null ) $parameters['x_apiKey'] = (string) $endUserApiKey;
	
			// make the call
			$response = $this->doCall( 'shorten', $parameters );
	
			// validate
			if(isset($response['data']))
			{
				// init var
				$result = array();
				$result['url'] = $response['data']['url'];
				$result['long_url'] = $response['data']['long_url'];
				$result['hash'] = $response['data']['hash'];
				$result['global_hash'] = $response['data']['global_hash'];
				$result['is_new_hash'] = ($response['data']['new_hash'] == 1);
	
				return $result;
			}
	
			// fallback
			return false;
		}
	
	}
	
}
	
if ( ! class_exists( 'SuextBitlyNoDebug' ) ) {
	class SuextBitlyNoDebug {
		public $enabled = false;
		public function mark() { return; }
		public function log() { return; }
	}
}	

?>
