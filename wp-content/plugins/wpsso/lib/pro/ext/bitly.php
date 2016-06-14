<?php
/**
 * Bitly class
 *
 * This source file can be used to communicate with Bit.ly (http://bit.ly)
 *
 * The class is documented in the file itself. If you find any bugs help me out
 * and report them. Reporting can be done by sending an email to
 * php-bitly-bugs[at]verkoyen[dot]eu.
 *
 * If you report a bug, make sure you give me enough information (include your
 * code).
 *
 * License
 * Copyright (c) 2010, Tijs Verkoyen. All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 *
 * 1. Redistributions of source code must retain the above copyright notice,
 * this list of conditions and the following disclaimer.
 * 2. Redistributions in binary form must reproduce the above copyright notice,
 * this list of conditions and the following disclaimer in the documentation
 * and/or other materials provided with the distribution.
 * 3. The name of the author may not be used to endorse or promote products
 * derived from this software without specific prior written permission.
 *
 * This software is provided by the author "as is" and any express or implied
 * warranties, including, but not limited to, the implied warranties of
 * merchantability and fitness for a particular purpose are disclaimed. In no
 * event shall the author be liable for any direct, indirect, incidental,
 * special, exemplary, or consequential damages (including, but not limited to,
 * procurement of substitute goods or services; loss of use, data, or profits;
 * or business interruption) however caused and on any theory of liability,
 * whether in contract, strict liability, or tort (including negligence or
 * otherwise) arising in any way out of the use of this software, even if
 * advised of the possibility of such damage.
 *
 * @author	Tijs Verkoyen <php-bitly@verkoyen.eu>
 * @version	2.0.2
 * @copyright	Copyright (c) 2010, Tijs Verkoyen. All rights reserved.
 * @license	BSD License
 */

if ( ! defined( 'ABSPATH' ) ) 
	die( 'These aren\'t the droids you\'re looking for...' );

if ( ! class_exists( 'SuextBitly' ) ) {

	class SuextBitly {
	
		// url for the bitly-api
		const API_URL = 'http://api.bit.ly/v3';
	
		// port for the bitly-API
		const API_PORT = 80;
	
		// bitly-API version
		const API_VERSION = '3.0';
	
		// current version
		const VERSION = '2.0.2';
	
	
		/**
		 * The API-key that will be used for authenticating
		 *
		 * @var	string
		 */
		private $apiKey;
	
	
		/**
		 * The login that will be used for authenticating
		 *
		 * @var	string
		 */
		private $login;
	
	
		/**
		 * The timeout
		 *
		 * @var	int
		 */
		private $timeOut = 60;
	
	
		/**
		 * The user agent
		 *
		 * @var	string
		 */
		private $userAgent;
	
		private $debug;
	
		/**
		 * Default constructor
		 *
		 * @return	void
		 * @param	string $login	The login (username) that has to be used for authenticating.
		 * @param	string $apiKey	The API-key that has to be used for authentication (see http://bit.ly/account).
		 */
		public function __construct( $login, $apiKey, &$debug = '' ) {

			$this->setLogin( $login );
			$this->setApiKey( $apiKey );

			if ( is_object( $debug ) )
				$this->debug = $debug;
			else $this->debug = new SuextBitlyNoDebug();

			if ( $this->debug->enabled )
				$this->debug->mark();
		}
	
		/**
		 * Make the call
		 *
		 * @return	string
		 * @param	string $url			The url to call.
		 * @param	array[optional] $aParameters	The parameters to pass.
		 */
		private function doCall($url, $aParameters = array())
		{
			// redefine
			$url = (string) $url;
			$aParameters = (array) $aParameters;
	
			// add required parameters
			$aParameters['format'] = 'json';
			$aParameters['login'] = $this->getLogin();
			$aParameters['apiKey'] = $this->getApiKey();
	
			// init var
			$queryString = '';
	
			// loop parameters and add them to the queryString
			foreach($aParameters as $key => $value) $queryString .= '&' . $key . '=' . urlencode(utf8_encode($value));
	
			// cleanup querystring
			$queryString = trim($queryString, '&');
	
			// append to url
			$url .= '?' . $queryString;
	
			// prepend
			$url = self::API_URL . '/' . $url;
	
			// set options
			$options[CURLOPT_URL] = $url;
			$options[CURLOPT_PORT] = self::API_PORT;
			$options[CURLOPT_USERAGENT] = $this->getUserAgent();
			if(ini_get('open_basedir') == '' && ini_get('safe_mode' == 'Off')) $options[CURLOPT_FOLLOWLOCATION] = true;
			$options[CURLOPT_RETURNTRANSFER] = true;
			$options[CURLOPT_TIMEOUT] = (int) $this->getTimeOut();
	
			// init
			$curl = curl_init();
	
			// set options
			curl_setopt_array($curl, $options);
	
			// execute
			$response = curl_exec($curl);
			$headers = curl_getinfo($curl);
	
			// fetch errors
			$errorNumber = curl_errno($curl);
			$errorMessage = curl_error($curl);
	
			// close
			curl_close($curl);
	
			// invalid headers
			if(!in_array($headers['http_code'], array(0, 200)))
			{
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
		 * Get the APIkey
		 *
		 * @return	string
		 */
		private function getApiKey()
		{
			return (string) $this->apiKey;
		}
	
	
		/**
		 * Get the login
		 *
		 * @return	string
		 */
		private function getLogin()
		{
			return (string) $this->login;
		}
	
	
		/**
		 * Get the timeout that will be used
		 *
		 * @return	int
		 */
		public function getTimeOut()
		{
			return (int) $this->timeOut;
		}
	
	
		/**
		 * Get the useragent that will be used. Our version will be prepended to yours.
		 * It will look like: "PHP Bitly/<version> <your-user-agent>"
		 *
		 * @return	string
		 */
		public function getUserAgent()
		{
			return (string) 'PHP Bitly/' . self::VERSION . ' ' . $this->userAgent;
		}
	
	
		/**
		 * Set the API-key that has to be used
		 *
		 * @return	void
		 * @param	string $apiKey		The key to set.
		 */
		private function setApiKey($apiKey)
		{
			$this->apiKey = (string) $apiKey;
		}
	
	
		/**
		 * Set the login that has to be used
		 *
		 * @return	void
		 * @param	string $login	The login to use.
		 */
		private function setLogin($login)
		{
			$this->login = (string) $login;
		}
	
	
		/**
		 * Set the timeout
		 * After this time the request will stop. You should handle any errors triggered by this.
		 *
		 * @return	void
		 * @param	int $seconds	The timeout in seconds.
		 */
		public function setTimeOut($seconds)
		{
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
		public function expand($shortURL = null, $hash = null)
		{
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
			$response = $this->doCall('expand', $parameters);
	
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
		public function shorten($url, $domain = null, $endUserLogin = null, $endUserApiKey = null)
		{
			// domain specified
			if($domain !== null && !in_array((string) $domain, array('bit.ly', 'j.mp'))) {
				if ( $this->debug->enabled )
					$this->debug->log('invalid domain -- only bit.ly or j.mp allowed -- '.
						'custom domains are handled by using a correct login.');
				return false;
			}
	
			// redefine
			$parameters['longUrl'] = (string) $url;
			if($domain !== null) $parameters['domain'] = (string) $domain;
			if($endUserLogin !== null) $parameters['x_login'] = (string) $endUserLogin;
			if($endUserApiKey !== null) $parameters['x_apiKey'] = (string) $endUserApiKey;
	
			// make the call
			$response = $this->doCall('shorten', $parameters);
	
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
