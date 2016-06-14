<?php
/*
 * License: GPLv3
 * License URI: http://www.gnu.org/licenses/gpl.txt
 * Copyright 2012-2016 Jean-Sebastien Morisset (http://surniaulula.com/)
 */

if ( ! defined( 'ABSPATH' ) ) 
	die( 'These aren\'t the droids you\'re looking for...' );

if ( ! class_exists( 'SucomDebug' ) ) {

	class SucomDebug {

		public $enabled = false;	// true if at least one subsys is true

		private $p;
		private $display_name = '';
		private $log_prefix = '';
		private $buffer = array();	// accumulate text strings going to html output
		private $subsys = array();	// associative array to enable various outputs 
		private $start_stats = null;
		private $begin_marks = array();

		public function __construct( &$plugin, $subsys = array( 'html' => false, 'wp' => false ) ) {
			$this->p =& $plugin;
			$this->start_stats = array(
				'time' => microtime( true ),
				'mem' => memory_get_usage( true ),
			);
			$this->display_name = $this->p->cf['lca'];
			$this->log_prefix = $this->p->cf['uca'];
			$this->subsys = $subsys;
			$this->is_enabled();		// set $this->enabled
			$this->mark();
		}

		public function mark( $id = false, $comment = '' ) { 
			if ( $this->enabled !== true ) 
				return;

			$cur_stats = array(
				'time' => microtime( true ),
				'mem' => memory_get_usage( true ),
			);
			if ( $this->start_stats === null )
				$this->start_stats = $cur_stats;

			if ( $id !== false ) {
				$id_text = '- - - - - - '.$id;
				if ( isset( $this->begin_marks[$id] ) ) {
					$id_text .= ' end + ('.
						$this->get_time_text( $cur_stats['time'] - $this->begin_marks[$id]['time'] ).' / '.
						$this->get_mem_text( $cur_stats['mem'] - $this->begin_marks[$id]['mem'] ).')';
					unset( $this->begin_marks[$id] );
				} else {
					$id_text .= ' begin';
					$this->begin_marks[$id] = array(
						'time' => $cur_stats['time'],
						'mem' => $cur_stats['mem'],
					);
				}
			}
			$this->log( 'mark ('.
				$this->get_time_text( $cur_stats['time'] - $this->start_stats['time'] ).' / '.
				$this->get_mem_text( $cur_stats['mem'] - $this->start_stats['mem'] ).')'.
				( $comment ? ' '.$comment : '' ).
				( $id !== false ? "\n\t".$id_text : '' ), 2 );
		}

		private function get_time_text( $time ) {
			return sprintf( '%f secs', $time );
		}

		private function get_mem_text( $mem ) {
			if ( $mem < 1024 )
				return $mem.' bytes';
			elseif ( $mem < 1048576 )
				return round( $mem / 1024, 2).' kb';
			else return round( $mem / 1048576, 2).' mb'; 
		}

		public function args( $args = array(), $class_idx = 1, $function_idx = false ) { 
			if ( $this->enabled !== true ) 
				return;

			if ( is_int( $class_idx ) ) {
				if ( $function_idx === false )
					$function_idx = $class_idx;
				$class_idx++;
			}

			if ( is_int( $function_idx ) )
				$function_idx++;
			elseif ( $function_idx === false )
				$function_idx = 2;

			$this->log( 'args '.self::pretty_array( $args, true ), $class_idx, $function_idx ); 
		}

		public function log( $input = '', $class_idx = 1, $function_idx = false ) {
			if ( $this->enabled !== true ) 
				return;
			$log_msg = '';
			$stack = debug_backtrace();

			if ( is_int( $class_idx ) ) {
				if ( $function_idx === false )
					$function_idx = $class_idx;
				$log_msg .= sprintf( '%-36s:: ', 
					( empty( $stack[$class_idx]['class'] ) ? 
						'' : $stack[$class_idx]['class'] ) );
			} else {
				if ( $function_idx === false )
					$function_idx = 1;
				$log_msg .= sprintf( '%-36s:: ', $class_idx );
			}

			if ( is_int( $function_idx ) ) {
				$log_msg .= sprintf( '%-45s: ', 
					( empty( $stack[$function_idx]['function'] ) ? 
						'' : $stack[$function_idx]['function'] ) );
			} else $log_msg .= sprintf( '%-45s: ', $function_idx );

			if ( is_multisite() ) {
				global $blog_id; 
				$log_msg .= '[blog '.$blog_id.'] ';
			}

			if ( is_array( $input ) )
				$log_msg .= trim( print_r( $input, true ) );
			elseif ( is_object( $input ) )
				$log_msg .= print_r( 'object '.get_class( $input ), true );
			else $log_msg .= $input;

			if ( $this->subsys['html'] == true )
				$this->buffer[] = $log_msg;

			if ( $this->subsys['wp'] == true ) {
				$sid = session_id();
				error_log( ( $sid ? $sid : $_SERVER['REMOTE_ADDR'] ).
					' '.$this->log_prefix.' '.$log_msg );
			}
		}

		public function show_html( $data = null, $title = null ) {
			if ( $this->is_enabled( 'html' ) !== true ) 
				return;
			echo $this->get_html( $data, $title, 2 );
		}

		public function get_html( $data = null, $title = null, $class_idx = 1, $function_idx = false ) {
			if ( $this->is_enabled( 'html' ) !== true ) 
				return;
			if ( $function_idx === false )
				$function_idx = $class_idx;
			$from = '';
			$html = '<!-- '.$this->display_name.' debug';
			$stack = debug_backtrace();
			if ( ! empty( $stack[$class_idx]['class'] ) ) 
				$from .= $stack[$class_idx]['class'].'::';
			if ( ! empty( $stack[$function_idx]['function'] ) )
				$from .= $stack[$function_idx]['function'];
			if ( $data === null ) {
				$data = $this->buffer;
				$this->buffer = array();
			}
			if ( ! empty( $from ) ) 
				$html .= ' from '.$from.'()';
			if ( ! empty( $title ) )
				$html .= ' '.$title;
			if ( ! empty( $data ) ) {
				$html .= ' : ';
				if ( is_array( $data ) ) {
					$html .= "\n";
					$is_assoc = SucomUtil::is_assoc( $data );
					if ( $is_assoc ) 
						ksort( $data );
					foreach ( $data as $key => $val ) {
						// remove comments
						if ( strpos( $val, '<!--' ) !== false )
							$val = preg_replace( '/<!--.*-->/Ums', '', $val );
						$html .= $is_assoc ?
							"\t$key = $val\n" : "\t$val\n";
					}
				} else $html .= $data;
			}
			$html .= ' -->'."\n";
			return $html;
		}

		public function switch_on( $name ) {
			return $this->switch_to( $name, true );
		}

		public function switch_off( $name ) {
			return $this->switch_to( $name, false );
		}

		private function switch_to( $name, $state ) {
			if ( ! empty( $name ) )
				$this->subsys[$name] = $state;
			return $this->is_enabled();
		}

		public function is_enabled( $name = '' ) {
			if ( ! empty( $name ) )
				return isset( $this->subsys[$name] ) ? 
					$this->subsys[$name] : false;
			// return true if any sybsys is true (use strict checking)
			else $this->enabled = in_array( true, $this->subsys, true ) ?
				true : false;
			return $this->enabled;
		}

		public static function pretty_array( $mixed, $flatten = false ) {
			$ret = '';

			if ( is_array( $mixed ) ) {
				foreach ( $mixed as $key => $val ) {
					$val = self::pretty_array( $val, $flatten );
					if ( $flatten )
						$ret .= $key.'='.$val.', ';
					else {
						if ( is_object( $mixed[$key] ) )
							unset ( $mixed[$key] );	// dereference the object first
						$mixed[$key] = $val;
					}
				}
				if ( $flatten )
					$ret = '('.trim( $ret, ', ' ).')'; 
				else $ret = $mixed;
			} elseif ( $mixed === false )
				$ret = 'false';
			elseif ( $mixed === true )
				$ret = 'true';
			elseif ( $mixed === null )
				$ret = 'null';
			elseif ( $mixed === '' )
				$ret = '\'\'';
			elseif ( is_object( $mixed ) )
				$ret = 'object '.get_class( $mixed );
			else $ret = $mixed;

			return $ret;
		}

		public static function get_hooks( $hook = '' ) {
			global $wp_filter;

			$hooks = isset( $wp_filter[$hook] ) ?
				$wp_filter[$hook] : array();  
			$hooks = call_user_func_array( 'array_merge', $hooks );
		
			foreach( $hooks as &$item ) {
				// function name as string or static class method eg. 'Foo::Bar'
				if ( is_string( $item['function'] ) ) { 
					try {
					   $ref = strpos( $item['function'], '::' ) ? 
					   	new ReflectionClass( strstr( $item['function'], '::', true ) ) : 
						new ReflectionFunction( $item['function'] );

						$item['file'] = $ref->getFileName();
						$item['line'] = get_class( $ref ) == 'ReflectionFunction' ?
							$ref->getStartLine() : 
							$ref->getMethod( substr( $item['function'], 
								strpos( $item['function'], '::' ) + 2 ) )->getStartLine();
					} catch( Exception $e ) {
						$item['error'] = $e->getMessage();
					}
				// array( object, method ), array( string object, method ), array( string object, string 'parent::method' )
				} elseif ( is_array( $item['function'] ) ) {
					try {
						$ref = new ReflectionClass( $item['function'][0] );
						$item['file'] = $ref->getFileName();
						$item['line'] = strpos( $item['function'][1], '::' ) ?
							$ref->getParentClass()->getMethod( substr( $item['function'][1], 
								strpos( $item['function'][1], '::' ) + 2 ) )->getStartLine() :
							$ref->getMethod( $item['function'][1] )->getStartLine();
					} catch( Exception $e ) {
						$item['error'] = $e->getMessage();
					}
				// closures
				} elseif ( is_callable( $item['function'] ) ) {     
					try {
						$ref = new ReflectionFunction( $item['function'] );         
						$item['function'] = get_class( $item['function'] );
						$item['file'] = $ref->getFileName();
						$item['line'] = $ref->getStartLine();
					} catch( Exception $e ) {
			        		$item['error'] = $e->getMessage();
					}
				}       
			}
			return $hooks;
		}
	}
}

?>
