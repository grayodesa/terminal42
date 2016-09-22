<?php
/*
 * License: GPLv3
 * License URI: http://www.gnu.org/licenses/gpl.txt
 * Copyright 2012-2016 Jean-Sebastien Morisset (http://surniaulula.com/)
 */

if ( ! defined( 'ABSPATH' ) ) 
	die( 'These aren\'t the droids you\'re looking for...' );

if ( ! class_exists( 'SucomException' ) ) {

	class SucomException extends Exception {

		protected $p;

		protected $statusCodes = array(
			100 => 'Continue',
			101 => 'Switching Protocols',
			200 => 'OK',
			201 => 'Created',
			202 => 'Accepted',
			203 => 'Non-Authoritative Information',
			204 => 'No Content',
			205 => 'Reset Content',
			206 => 'Partial Content',
			300 => 'Multiple Choices',
			301 => 'Moved Permanently',
			302 => 'Found',
			303 => 'See Other',
			304 => 'Not Modified',
			305 => 'Use Proxy',
			306 => '(Unused)',
			307 => 'Temporary Redirect',
			400 => 'Bad Request',
			401 => 'Unauthorized',
			402 => 'Payment Required',
			403 => 'Forbidden',
			404 => 'Not Found',
			405 => 'Method Not Allowed',
			406 => 'Not Acceptable',
			407 => 'Proxy Authentication Required',
			408 => 'Request Timeout',
			409 => 'Conflict',
			411 => 'Length Required',
			412 => 'Precondition Failed',
			413 => 'Request Entity Too Large',
			414 => 'Request-URI Too Long',
			415 => 'Unsupported Media Type',
			416 => 'Requested Range Not Satisfiable',
			417 => 'Expectation Failed',
			500 => 'Internal Server Error',
			501 => 'Not Implemented',
			502 => 'Bad Gateway',
			503 => 'Service Unavailable',
			504 => 'Gateway Timeout',
			505 => 'HTTP Version Not Supported'
		);
	
		public function __construct( $message = null, $code = null, Exception $previous = null ) {

			if ( class_exists( 'Wpsso' ) )
				$this->p =& Wpsso::get_instance();
			elseif ( class_exists( 'Ngfb' ) )
				$this->p =& Ngfb::get_instance();

			if ( is_object( $this->p ) ) {
				if ( $this->p->debug->enabled )
					$this->p->debug->mark();
			}

			if ( $message === null && isset( $this->statusCodes[(int) $code] ) )
				$message = $this->statusCodes[(int) $code];

			parent::__construct( $message, $code, $previous );
		}

		public function errorMessage( $ret = false ) {
			/*
			 * getMessage();        // message of exception
			 * getCode();           // code of exception
			 * getFile();           // source filename
			 * getLine();           // source line
			 * getTrace();          // an array of the backtrace()
			 * getPrevious();       // previous exception
			 * getTraceAsString();  // formatted string of trace
			 */
			if ( is_object( $this->p ) ) {
				$err_msg = $this->getMessage();
				if ( $this->p->debug->enabled )
					$this->p->debug->log( $err_msg );
				$this->p->notice->err( $err_msg );
			}
			return  $ret;
		}
	}
}

?>
