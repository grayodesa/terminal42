<?php
/**
* This file is part of googl-php
*
* https://github.com/sebi/googl-php
*
* googl-php is free software: you can redistribute it and/or modify
* it under the terms of the GNU General Public License as published by
* the Free Software Foundation, either version 3 of the License, or
* (at your option) any later version.
*
* This program is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
* GNU General Public License for more details.
*
* You should have received a copy of the GNU General Public License
* along with this program. If not, see <http://www.gnu.org/licenses/>.
*/

if ( ! defined( 'ABSPATH' ) ) 
	die( 'These aren\'t the droids you\'re looking for...' );

if ( ! class_exists( 'SuextGoogl' ) ) {

	class SuextGoogl {

		private $target;
		private $apiKey;
		private $ch;
		private $debug;

		private static $buffer = array();
	
		public $extended;

		function __construct( $apiKey = null ) {

			$extended = false;
			$this->target = 'https://www.googleapis.com/urlshortener/v1/url?';
	
			if ( $apiKey != null ) {
				$this->apiKey = $apiKey;
				$this->target .= 'key='.$apiKey.'&';
			}
	
			$this->ch = curl_init();
			curl_setopt( $this->ch, CURLOPT_URL, $this->target );
			curl_setopt( $this->ch, CURLOPT_RETURNTRANSFER, true );
		}
	
		public function shorten($url, $extended = false) {
			
			if ( !$extended && !$this->extended && !empty(self::$buffer[$url]) )
				return self::$buffer[$url];
			
			$data = array( 'longUrl' => $url );
			$data_string = '{ "longUrl": "'.$url.'" }';
	
			curl_setopt($this->ch, CURLOPT_POST, count($data));
			curl_setopt($this->ch, CURLOPT_POSTFIELDS, $data_string);
			curl_setopt($this->ch, CURLOPT_HTTPHEADER, Array('Content-Type: application/json'));

			$decoded = json_decode(curl_exec($this->ch));
			if ($extended || $this->extended) {
				return $decoded;
			} elseif ( !empty( $decoded->id ) ) {
				$ret = $decoded->id;
				self::$buffer[$url] = $ret;
				return $ret;
			} else return false;
		}
	
		public function expand($url, $extended = false) {

			curl_setopt($this->ch, CURLOPT_HTTPGET, true);
			curl_setopt($this->ch, CURLOPT_URL, $this->target.'shortUrl='.$url);
			
			if ($extended || $this->extended)
				return json_decode(curl_exec($this->ch));
			else return json_decode(curl_exec($this->ch))->longUrl;
		}
	
		function __destruct() {
			curl_close( $this->ch );
			$this->ch = null;
		}
	}
}

?>
