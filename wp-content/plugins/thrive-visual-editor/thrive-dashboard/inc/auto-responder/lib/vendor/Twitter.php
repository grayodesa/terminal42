<?php
/**
 * Thrive Themes - https://thrivethemes.com
 *
 * @package thrive-dashboard
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Silence is golden
}

/**
 * Twitter-API-PHP : Simple PHP wrapper for the v1.1 API
 *
 * PHP version 5.3.10
 *
 * @category Awesomeness
 * @package  Twitter-API-PHP
 * @author   James Mallison <me@j7mbo.co.uk>
 * @license  MIT License
 * @version  1.0.4
 * @link     http://github.com/j7mbo/twitter-api-php
 */
class Thrive_Dash_Api_Twitter {
	/**
	 * @var string
	 */
	private $oauth_access_token;

	/**
	 * @var string
	 */
	private $oauth_access_token_secret;

	/**
	 * @var string
	 */
	private $consumer_key;

	/**
	 * @var string
	 */
	private $consumer_secret;

	/**
	 * @var array
	 */
	private $postfields;

	/**
	 * @var string
	 */
	private $getfield;

	/**
	 * @var mixed
	 */
	protected $oauth;

	/**
	 * @var string
	 */
	public $url;

	/**
	 * @var string
	 */
	public $requestMethod;

	/**
	 * Create the API access object. Requires an array of settings::
	 * oauth access token, oauth access token secret, consumer key, consumer secret
	 * These are all available by creating your own application on dev.twitter.com
	 * Requires the cURL library
	 * @throws Exception
	 *
	 * @param array $settings
	 */
	public function __construct( array $settings ) {


		if ( ! isset( $settings['oauth_access_token'] )
		     || ! isset( $settings['oauth_access_token_secret'] )
		     || ! isset( $settings['consumer_key'] )
		     || ! isset( $settings['consumer_secret'] )
		) {
			throw new Exception( 'Make sure you are passing in the correct parameters' );
		}

		$this->oauth_access_token        = $settings['oauth_access_token'];
		$this->oauth_access_token_secret = $settings['oauth_access_token_secret'];
		$this->consumer_key              = $settings['consumer_key'];
		$this->consumer_secret           = $settings['consumer_secret'];
	}

	/**
	 * Set postfields array, example: array('screen_name' => 'J7mbo')
	 *
	 * @param array $array Array of parameters to send to API
	 *
	 * @throws Exception
	 *
	 * @return Thrive_Dash_Api_Twitter Instance of self for method chaining
	 */
	public function setPostfields( array $array ) {
		if ( ! is_null( $this->getGetfield() ) ) {
			throw new Exception( 'You can only choose get OR post fields.' );
		}

		if ( isset( $array['status'] ) && substr( $array['status'], 0, 1 ) === '@' ) {
			$array['status'] = sprintf( "\0%s", $array['status'] );
		}

		foreach ( $array as $key => &$value ) {
			if ( is_bool( $value ) ) {
				$value = ( $value === true ) ? 'true' : 'false';
			}
		}

		$this->postfields = $array;

		// rebuild oAuth
		if ( isset( $this->oauth['oauth_signature'] ) ) {
			$this->buildOauth( $this->url, $this->requestMethod );
		}

		return $this;
	}

	/**
	 * Set getfield string, example: '?screen_name=J7mbo'
	 *
	 * @param string $string Get key and value pairs as string
	 *
	 * @throws Exception
	 *
	 * @return \Thrive_Dash_Api_Twitter Instance of self for method chaining
	 */
	public function setGetfield( $string ) {
		if ( ! is_null( $this->getPostfields() ) ) {
			throw new Exception( 'You can only choose get OR post fields.' );
		}

		$getfields = preg_replace( '/^\?/', '', explode( '&', $string ) );
		$params    = array();

		foreach ( $getfields as $field ) {
			if ( $field !== '' ) {
				list( $key, $value ) = explode( '=', $field );
				$params[ $key ] = $value;
			}
		}

		$this->getfield = '?' . http_build_query( $params );

		return $this;
	}

	/**
	 * Get getfield string (simple getter)
	 *
	 * @return string $this->getfields
	 */
	public function getGetfield() {
		return $this->getfield;
	}

	/**
	 * Get postfields array (simple getter)
	 *
	 * @return array $this->postfields
	 */
	public function getPostfields() {
		return $this->postfields;
	}

	/**
	 * Build the Oauth object using params set in construct and additionals
	 * passed to this method. For v1.1, see: https://dev.twitter.com/docs/api/1.1
	 *
	 * @param string $url The API url to use. Example: https://api.twitter.com/1.1/search/tweets.json
	 * @param string $requestMethod Either POST or GET
	 *
	 * @throws \Exception
	 *
	 * @return \Thrive_Dash_Api_Twitter Instance of self for method chaining
	 */
	public function buildOauth( $url, $requestMethod ) {
		if ( ! in_array( strtolower( $requestMethod ), array( 'post', 'get' ) ) ) {
			throw new Exception( 'Request method must be either POST or GET' );
		}

		$consumer_key              = $this->consumer_key;
		$consumer_secret           = $this->consumer_secret;
		$oauth_access_token        = $this->oauth_access_token;
		$oauth_access_token_secret = $this->oauth_access_token_secret;

		$oauth = array(
			'oauth_consumer_key'     => $consumer_key,
			'oauth_nonce'            => time(),
			'oauth_signature_method' => 'HMAC-SHA1',
			'oauth_token'            => $oauth_access_token,
			'oauth_timestamp'        => time(),
			'oauth_version'          => '1.0'
		);

		$getfield = $this->getGetfield();

		if ( ! is_null( $getfield ) ) {
			$getfields = str_replace( '?', '', explode( '&', $getfield ) );

			foreach ( $getfields as $g ) {
				$split = explode( '=', $g );

				/** In case a null is passed through **/
				if ( isset( $split[1] ) ) {
					$oauth[ $split[0] ] = urldecode( $split[1] );
				}
			}
		}

		$postfields = $this->getPostfields();

		if ( ! is_null( $postfields ) ) {
			foreach ( $postfields as $key => $value ) {
				$oauth[ $key ] = $value;
			}
		}

		$base_info                = $this->buildBaseString( $url, $requestMethod, $oauth );
		$composite_key            = rawurlencode( $consumer_secret ) . '&' . rawurlencode( $oauth_access_token_secret );
		$oauth_signature          = base64_encode( hash_hmac( 'sha1', $base_info, $composite_key, true ) );
		$oauth['oauth_signature'] = $oauth_signature;

		$this->url           = $url;
		$this->requestMethod = $requestMethod;
		$this->oauth         = $oauth;

		return $this;
	}

	/**
	 * Perform the actual data retrieval from the API
	 *
	 * @param boolean $return If true, returns data. This is left in for backward compatibility reasons
	 * @param array $curlOptions Additional Curl options for this request
	 *
	 * @throws \Exception
	 *
	 * @return string json If $return param is true, returns json data.
	 */
	public function performRequest( $return = true, $curlOptions = array() ) {
		if ( ! is_bool( $return ) ) {
			throw new Exception( 'performRequest parameter must be true or false' );
		}

		$header = array( $this->buildAuthorizationHeader( $this->oauth ), 'Expect:' );

		$args = array(
			'method'      => 'GET',
			'timeout'     => 5,
			'redirection' => 5,
			'httpversion' => '1.0',
			'blocking'    => true,
			'headers'     => $header[0],
			'body'        => null,
			'cookies'     => array()
		);


		$getfield   = $this->getGetfield();
		$postfields = $this->getPostfields();

		$json = tve_dash_api_remote_get( $this->url . $getfield, $args );

		return $json['body'];
	}

	/**
	 * Private method to generate the base string used by cURL
	 *
	 * @param string $baseURI
	 * @param string $method
	 * @param array $params
	 *
	 * @return string Built base string
	 */
	private function buildBaseString( $baseURI, $method, $params ) {
		$return = array();
		ksort( $params );

		foreach ( $params as $key => $value ) {
			$return[] = rawurlencode( $key ) . '=' . rawurlencode( $value );
		}

		return $method . "&" . rawurlencode( $baseURI ) . '&' . rawurlencode( implode( '&', $return ) );
	}

	/**
	 * Private method to generate authorization header used by cURL
	 *
	 * @param array $oauth Array of oauth data generated by buildOauth()
	 *
	 * @return string $return Header used by cURL for request
	 */
	private function buildAuthorizationHeader( array $oauth ) {
		$return = 'Authorization: OAuth ';
		$values = array();

		foreach ( $oauth as $key => $value ) {
			if ( in_array( $key, array(
				'oauth_consumer_key',
				'oauth_nonce',
				'oauth_signature',
				'oauth_signature_method',
				'oauth_timestamp',
				'oauth_token',
				'oauth_version'
			) ) ) {
				$values[] = "$key=\"" . rawurlencode( $value ) . "\"";
			}
		}

		$return .= implode( ', ', $values );

		return $return;
	}

	/**
	 * Helper method to perform our request
	 *
	 * @param string $url
	 * @param string $method
	 * @param string $data
	 * @param array $curlOptions
	 *
	 * @throws \Exception
	 *
	 * @return string The json response from the server
	 */
	public function request( $url, $method = 'get', $data = null, $curlOptions = array() ) {
		if ( strtolower( $method ) === 'get' ) {
			$this->setGetfield( $data );
		} else {
			$this->setPostfields( $data );
		}

		return $this->buildOauth( $url, $method )->performRequest( true, $curlOptions );
	}
}
