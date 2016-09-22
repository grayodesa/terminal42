<?php
/*
 * License: GPLv3
 * License URI: http://www.gnu.org/licenses/gpl.txt
 * Copyright 2012-2016 Jean-Sebastien Morisset (http://surniaulula.com/)
 */

if ( ! defined( 'ABSPATH' ) ) 
	die( 'These aren\'t the droids you\'re looking for...' );

if ( ! class_exists( 'SucomNoNotice' ) ) {

	class SucomNoNotice {
		public $has_shown = false;
		public function __construct() {}
		public function nag() {}
		public function err() {}
		public function warn() {}
		public function upd() {}
		public function inf() {}
		public function log() {}
		public function trunc_id() {}
		public function trunc_all() {}
		public function trunc() {}
		public function is_admin_pre_notices() {}
	}
}

?>
