<?php
/*
 * License: GPLv3
 * License URI: http://www.gnu.org/licenses/gpl.txt
 * Copyright 2012-2016 Jean-Sebastien Morisset (http://surniaulula.com/)
 */

if ( ! defined( 'ABSPATH' ) ) 
	die( 'These aren\'t the droids you\'re looking for...' );

if ( ! class_exists( 'WpssoUtil' ) && class_exists( 'SucomUtil' ) ) {

	class WpssoUtil extends SucomUtil {

		protected $uniq_urls = array();			// array to detect duplicate images, etc.
		protected $size_labels = array();		// reference array for image size labels
		protected $sanitize_error_msgs = null;		// translated error messages for sanitize_option_value()
		protected $cleared_all_cache = false;

		public function __construct( &$plugin ) {
			$this->p =& $plugin;
			if ( $this->p->debug->enabled )
				$this->p->debug->mark();

			add_action( 'wp', array( &$this, 'add_plugin_image_sizes' ), -100 );	// runs everytime a posts query is triggered from a url
			add_action( 'current_screen', array( &$this, 'add_plugin_image_sizes' ), -100 );
			add_action( 'wp_scheduled_delete', array( &$this, 'delete_expired_db_transients' ) );
			add_action( 'wp_scheduled_delete', array( &$this, 'delete_expired_file_cache' ) );

			// the "current_screen" action hook is not called when editing / saving an image
			// hook the "image_editor_save_pre" filter as to add image sizes for that attachment / post
			add_filter( 'image_save_pre', array( &$this, 'image_editor_save_pre_image_sizes' ), -100, 2 );	// filter deprecated in wp 3.5
			add_filter( 'image_editor_save_pre', array( &$this, 'image_editor_save_pre_image_sizes' ), -100, 2 );
		}

		// called from several class __construct() methods to hook their filters
		public function add_plugin_filters( &$class, $filters, $prio = 10, $lca = '' ) {
			$this->add_plugin_hooks( 'filter', $class, $filters, $prio, $lca );
		}

		public function add_plugin_actions( &$class, $actions, $prio = 10, $lca = '' ) {
			$this->add_plugin_hooks( 'action', $class, $actions, $prio, $lca );
		}

		protected function add_plugin_hooks( $type, &$class, &$hooks, &$prio, &$lca ) {
			$lca = $lca === '' ?
				$this->p->cf['lca'] : $lca;

			foreach ( $hooks as $name => $val ) {
				if ( ! is_string( $name ) ) {
					if ( $this->p->debug->enabled )
						$this->p->debug->log( $name.' => '.$val.' '.$type.
							' skipped: filter name must be a string' );
					continue;
				}
				/*
				 * example:
				 * 	'json_data_http_schema_org_website' => 6
				 */
				if ( is_int( $val ) ) {
					$arg_nums = $val;
					$hook_name = self::sanitize_hookname( $lca.'_'.$name );
					$method_name = self::sanitize_hookname( $type.'_'.$name );

					call_user_func( 'add_'.$type, $hook_name, 
						array( &$class, $method_name ), $prio, $arg_nums );

					if ( $this->p->debug->enabled )
						$this->p->debug->log( 'added '.$method_name.' (method) '.$type, 3 );
				/*
				 * example:
				 * 	'add_schema_meta_array' => '__return_false'
				 */
				} elseif ( is_string( $val ) ) {
					$arg_nums = 1;
					$hook_name = self::sanitize_hookname( $lca.'_'.$name );
					$function_name = self::sanitize_hookname( $val );

					call_user_func( 'add_'.$type, $hook_name, 
						$function_name, $prio, $arg_nums );

					if ( $this->p->debug->enabled )
						$this->p->debug->log( 'added '.$function_name.' (function) '.$type, 3 );
				/*
				 * example:
				 * 	'json_data_http_schema_org_article' => array(
				 *		'json_data_http_schema_org_article' => 6,
				 *		'json_data_http_schema_org_newsarticle' => 6,
				 *		'json_data_http_schema_org_techarticle' => 6,
				 *	)
				 */
				} elseif ( is_array( $val ) ) {
					$method_name = self::sanitize_hookname( $type.'_'.$name );
					foreach ( $val as $hook_name => $arg_nums ) {
						$hook_name = self::sanitize_hookname( $lca.'_'.$hook_name );

						call_user_func( 'add_'.$type, $hook_name, 
							array( &$class, $method_name ), $prio, $arg_nums );

						if ( $this->p->debug->enabled )
							$this->p->debug->log( 'added '.$method_name.' (method) '.$type.' to '.$hook_name, 3 );
					}
				}
			}
		}

		public function get_image_size_label( $size_name ) {	// wpsso-opengraph
			if ( ! empty( $this->size_labels[$size_name] ) )
				return $this->size_labels[$size_name];
			else return $size_name;
		}

		public function image_editor_save_pre_image_sizes( $image, $post_id = false ) {
			if ( empty( $post_id ) )
				return $image;

			// get post/user/term id, module name, and module object reference
			$mod = $this->get_page_mod( $post_id, array( 'id' => $post_id, 'name' => 'post' ) );

			$this->add_plugin_image_sizes( false, array(), $mod, true );

			return $image;
		}

		// can be called directly and from the "wp" and "current_screen" actions
		// this method does not return a value, so do not use as a filter
		public function add_plugin_image_sizes( $wp_obj = false, $sizes = array(), &$mod = false, $filter = true ) {
			/*
			 * Allow various plugin extensions to provide their image names, labels, etc.
			 * The first dimension array key is the option name prefix by default.
			 * You can also include the width, height, crop, crop_x, and crop_y values.
			 *
			 *	Array (
			 *		[rp_img] => Array (
			 *			[name] => richpin
			 *			[label] => Rich Pin Image Dimensions
			 *		) 
			 *		[og_img] => Array (
			 *			[name] => opengraph
			 *			[label] => Open Graph Image Dimensions
			 *		)
			 *	)
			 */
			if ( $this->p->debug->enabled )
				$this->p->debug->mark( 'define image sizes' );	// begin timer

			$use_post = false;
			$lca = $this->p->cf['lca'];
			$aop = $this->p->check->aop( $lca, true, $this->p->is_avail['aop'] );
			if ( ! is_array( $mod ) )
				$mod = $this->get_page_mod( $use_post, $mod, $wp_obj );
			$meta_opts = array();

			if ( $filter === true ) {
				$sizes = apply_filters( $this->p->cf['lca'].'_plugin_image_sizes',
					$sizes, $mod, SucomUtil::crawler_name() );
			}

			if ( empty( $mod['id'] ) ) {
				if ( $this->p->debug->enabled )
					$this->p->debug->log( 'module id is unknown' );
			} elseif ( empty( $mod['name'] ) ) {
				if ( $this->p->debug->enabled )
					$this->p->debug->log( 'module name is unknown' );
			// custom filters may use image sizes, so don't filter/cache the meta options
			} elseif ( ! empty( $mod['id'] ) && is_object( $mod['obj'] ) && $aop )
				$meta_opts = $mod['obj']->get_options( $mod['id'], false, false );	// $filter_options = false

			foreach( $sizes as $opt_prefix => $size_info ) {

				if ( ! is_array( $size_info ) ) {
					$save_name = empty( $size_info ) ? 
						$opt_prefix : $size_info;
					$size_info = array( 
						'name' => $save_name,
						'label' => $save_name
					);
				} elseif ( ! empty( $size_info['prefix'] ) )				// allow for alternate option prefix
					$opt_prefix = $size_info['prefix'];

				foreach ( array( 'width', 'height', 'crop', 'crop_x', 'crop_y' ) as $key ) {
					if ( isset( $size_info[$key] ) )				// prefer existing info from filters
						continue;
					elseif ( isset( $meta_opts[$opt_prefix.'_'.$key] ) )		// use post meta if available
						$size_info[$key] = $meta_opts[$opt_prefix.'_'.$key];
					elseif ( isset( $this->p->options[$opt_prefix.'_'.$key] ) )	// current plugin settings
						$size_info[$key] = $this->p->options[$opt_prefix.'_'.$key];
					else {
						if ( ! isset( $def_opts ) ) {				// only read once if necessary
							if ( $this->p->debug->enabled )
								$this->p->debug->log( 'getting default option values' );
							$def_opts = $this->p->opt->get_defaults();
						}
						$size_info[$key] = $def_opts[$opt_prefix.'_'.$key];	// fallback to default value
					}
					if ( $key === 'crop' )						// make sure crop is true or false
						$size_info[$key] = empty( $size_info[$key] ) ?
							false : true;
				}

				if ( $size_info['width'] > 0 && $size_info['height'] > 0 ) {

					// preserve compatibility with older wordpress versions, use true or false when possible
					if ( $size_info['crop'] === true && 
						( $size_info['crop_x'] !== 'center' || $size_info['crop_y'] !== 'center' ) ) {

						global $wp_version;
						if ( ! version_compare( $wp_version, 3.9, '<' ) )
							$size_info['crop'] = array( $size_info['crop_x'], $size_info['crop_y'] );
					}

					// allow custom function hooks to make changes
					if ( $filter === true )
						$size_info = apply_filters( $this->p->cf['lca'].'_size_info_'.$size_info['name'], 
							$size_info, $mod['id'], $mod['name'] );

					// a lookup array for image size labels, used in image size error messages
					$this->size_labels[$this->p->cf['lca'].'-'.$size_info['name']] = $size_info['label'];

					add_image_size( $this->p->cf['lca'].'-'.$size_info['name'], 
						$size_info['width'], $size_info['height'], $size_info['crop'] );

					if ( $this->p->debug->enabled )
						$this->p->debug->log( 'image size '.$this->p->cf['lca'].'-'.$size_info['name'].' '.
							$size_info['width'].'x'.$size_info['height'].
							( empty( $size_info['crop'] ) ? '' : ' crop '.
								$size_info['crop_x'].'/'.$size_info['crop_y'] ).' added' );
				}
			}
			if ( $this->p->debug->enabled ) {
				$this->p->debug->mark( 'define image sizes' );	// end timer
				$this->p->debug->log_arr( 'get_all_image_sizes', SucomUtil::get_image_sizes() );
			}
		}

		public function add_ptns_to_opts( &$opts = array(), $prefixes, $default = 1 ) {
			if ( ! is_array( $prefixes ) )
				$prefixes = array( $prefixes => $default );
			foreach ( $prefixes as $opt_pre => $def_val ) {
				foreach ( $this->get_post_types() as $post_type ) {
					$key = $opt_pre.'_'.$post_type->name;
					if ( ! isset( $opts[$key] ) )
						$opts[$key] = $def_val;
				}
			}
			return $opts;
		}

		public function get_post_types( $output = 'objects' ) {
			return apply_filters( $this->p->cf['lca'].'_post_types', 
				get_post_types( array( 'public' => true ), $output ), $output );
		}

		public function clear_all_cache( $clear_external = true, $msg_id = false, $dismiss = false ) {

			if ( $this->cleared_all_cache )	// already run once
				return 0;
			else $this->cleared_all_cache = true;

			wp_cache_flush();					// clear non-database transients as well

			$lca = $this->p->cf['lca'];
			$short = $this->p->cf['plugin'][$lca]['short'];
			$del_files = $this->delete_expired_file_cache( true );
			$del_transients = $this->delete_expired_db_transients( true );
			$ext_cache_msg = __( 'The cache for %s has also been cleared.', 'wpsso' );
			$clear_all_msg = sprintf( __( '%s cached files, transient cache, and the WordPress object cache have been cleared.',
				'wpsso' ), $short );

			if ( $clear_external ) {
				if ( function_exists( 'w3tc_pgcache_flush' ) ) {	// w3 total cache
					w3tc_pgcache_flush();
					w3tc_objectcache_flush();
					$clear_all_msg .= ' '.sprintf( $ext_cache_msg, 'W3 Total Cache' );
				}

				if ( function_exists( 'wp_cache_clear_cache' ) ) {	// wp super cache
					wp_cache_clear_cache();
					$clear_all_msg .= ' '.sprintf( $ext_cache_msg, 'WP Super Cache' );
				}

				if ( isset( $GLOBALS['comet_cache'] ) ) {		// comet cache
					$GLOBALS['comet_cache']->wipe_cache();
					$clear_all_msg .= ' '.sprintf( $ext_cache_msg, 'Comet Cache' );
				} elseif ( isset( $GLOBALS['zencache'] ) ) {		// zencache
					$GLOBALS['zencache']->wipe_cache();
					$clear_all_msg .= ' '.sprintf( $ext_cache_msg, 'ZenCache' );
				}
			}

			$this->p->notice->inf( $clear_all_msg, true, true, $msg_id, $dismiss );

			return $del_files + $del_transients;
		}

		public function clear_cache_objects( &$transients = array(), &$wp_objects = array() ) {
			$deleted = 0;
			foreach ( $transients as $group => $arr ) {
				foreach ( $arr as $val ) {
					if ( ! empty( $val ) ) {
						$cache_salt = $group.'('.$val.')';
						$cache_id = $this->p->cf['lca'].'_'.md5( $cache_salt );
						if ( delete_transient( $cache_id ) ) {
							if ( $this->p->debug->enabled )
								$this->p->debug->log( 'cleared transient cache salt: '.$cache_salt );
							$deleted++;
						}
					}
				}
			}
			foreach ( $wp_objects as $group => $arr ) {
				foreach ( $arr as $val ) {
					if ( ! empty( $val ) ) {
						$cache_salt = $group.'('.$val.')';
						$cache_id = $this->p->cf['lca'].'_'.md5( $cache_salt );
						if ( wp_cache_delete( $cache_id, $group ) ) {
							if ( $this->p->debug->enabled )
								$this->p->debug->log( 'cleared object cache salt: '.$cache_salt );
							$deleted++;
						}
					}
				}
			}
			return $deleted;
		}

		public function get_topics() {
			if ( $this->p->is_avail['cache']['transient'] ) {
				$cache_salt = __METHOD__.'('.WPSSO_TOPICS_LIST.')';
				$cache_id = $this->p->cf['lca'].'_'.md5( $cache_salt );
				$cache_type = 'object cache';
				if ( $this->p->debug->enabled )
					$this->p->debug->log( $cache_type.': transient salt '.$cache_salt );
				$topics = get_transient( $cache_id );
				if ( is_array( $topics ) ) {
					if ( $this->p->debug->enabled )
						$this->p->debug->log( $cache_type.': topics array retrieved from transient '.$cache_id );
					return $topics;
				}
			}
			if ( ( $topics = file( WPSSO_TOPICS_LIST, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES ) ) === false ) {
				if ( $this->p->debug->enabled )
					$this->p->debug->log( 'error reading %s topic list file' );
				if ( is_admin() )
					$this->p->notice->err( sprintf( __( 'Error reading %s topic list file.', 
						'wpsso' ), WPSSO_TOPICS_LIST ) );
				return $topics;
			}
			$topics = apply_filters( $this->p->cf['lca'].'_topics', $topics );
			natsort( $topics );
			$topics = array_merge( array( 'none' ), $topics );	// after sorting the array, put 'none' first

			if ( ! empty( $cache_id ) ) {
				set_transient( $cache_id, $topics, $this->p->options['plugin_object_cache_exp'] );
				if ( $this->p->debug->enabled )
					$this->p->debug->log( $cache_type.': topics array saved to transient '.
						$cache_id.' ('.$this->p->options['plugin_object_cache_exp'].' seconds)');
			}
			return $topics;
		}

		public function sanitize_option_value( $key, $val, $def_val, $network = false, &$mod = false ) {

			// remove multiples, localization, and status for more generic match
			$option_key = preg_replace( '/(_[0-9]+)?(#.*|:[0-9]+)?$/', '', $key );

			// hooked by the sharing class
			$option_type = apply_filters( $this->p->cf['lca'].'_option_type', false, $option_key, $network, $mod );

			// translate error messages only once
			if ( $this->sanitize_error_msgs === null ) {
				$this->sanitize_error_msgs = array(
					'url' => __( 'The value of option \'%s\' must be a URL - resetting the option to its default value.',
						'wpsso' ),
					'pos_num' => __( 'The value of option \'%s\' must be equal to or greather than %s - resetting the option to its default value.',
						'wpsso' ),
					'blank_num' => __( 'The value of option \'%s\' must be numeric - resetting the option to its default value.',
						'wpsso' ),
					'numeric' => __( 'The value of option \'%s\' must be numeric - resetting the option to its default value.',
						'wpsso' ),
					'auth_id' => __( '\'%1$s\' is not an acceptable value for option \'%2$s\' - resetting the option to its default value.',
						'wpsso' ),
					'api_key' => __( 'The value of option \'%s\' must be alpha-numeric - resetting the option to its default value.',
						'wpsso' ),
					'date' => __( 'The value of option \'%s\' must be a yyyy-mm-dd date - resetting the option to its default value.',
						'wpsso' ),
					'time' => __( 'The value of option \'%s\' must be a hh:mm time - resetting the option to its default value.',
						'wpsso' ),
					'html' => __( 'The value of option \'%s\' must be HTML code - resetting the option to its default value.',
						'wpsso' ),
					'not_blank' => __( 'The value of option \'%s\' cannot be empty - resetting the option to its default value.',
						'wpsso' ),
				);
			}

			// pre-filter most values to remove html
			switch ( $option_type ) {
				case 'ignore':
					return $val;	// stop here
					break;
				case 'html':		// leave html and css / javascript code blocks as-is
				case 'code':
					$val = stripslashes( $val );
					break;
				default:
					$val = stripslashes( $val );
					$val = wp_filter_nohtml_kses( $val );
					$val = self::encode_emoji( htmlentities( $val, 
						ENT_QUOTES, get_bloginfo( 'charset' ), false ) );	// double_encode = false
					break;
			}

			switch ( $option_type ) {
				// must be empty or texturized 
				case 'textured':
					if ( $val !== '' )
						$val = trim( wptexturize( ' '.$val.' ' ) );
					break;

				// must be empty or a url
				case 'url':
					if ( $val !== '' ) {
						$val = $this->cleanup_html_tags( $val );
						if ( strpos( $val, '//' ) === false ) {
							$this->p->notice->err( sprintf( $this->sanitize_error_msgs[$option_type], $key ), true );
							$val = $def_val;
						}
					}
					break;

				// strip leading urls off facebook usernames
				case 'url_base':
					if ( $val !== '' ) {
						$val = $this->cleanup_html_tags( $val );
						$val = preg_replace( '/(http|https):\/\/[^\/]*?\//', '', $val );
					}
					break;

				// twitter-style usernames (prepend with an @ character)
				case 'at_name':
					if ( $val !== '' ) {
						$val = substr( preg_replace( array( '/^.*\//', '/[^a-zA-Z0-9_]/' ), '', $val ), 0, 15 );
						if ( ! empty( $val ) ) 
							$val = '@'.$val;
					}
					break;

				case 'pos_num':		// integer options that must be 1 or more (not zero)
				case 'img_width':	// image height, subject to minimum value (typically, at least 200px)
				case 'img_height':	// image height, subject to minimum value (typically, at least 200px)

					if ( $option_type == 'img_width' )
						$min_int = $this->p->cf['head']['limit_min']['og_img_width'];
					elseif ( $option_type == 'img_height' )
						$min_int = $this->p->cf['head']['limit_min']['og_img_height'];
					else $min_int = 1;

					// custom meta options are allowed to be empty
					if ( $val === '' && ! empty( $mod['name'] ) )
						break;	// abort
					elseif ( ! is_numeric( $val ) || $val < $min_int ) {
						$this->p->notice->err( sprintf( $this->sanitize_error_msgs['pos_num'], $key, $min_int ), true );
						$val = $def_val;
					}
					break;

				// must be blank or numeric
				case 'blank_num':
					if ( $val !== '' ) {
						if ( ! is_numeric( $val ) ) {
							$this->p->notice->err( sprintf( $this->sanitize_error_msgs[$option_type], $key ), true );
							$val = $def_val;
						}
					}
					break;

				// must be numeric
				case 'numeric':
					if ( ! is_numeric( $val ) ) {
						$this->p->notice->err( sprintf( $this->sanitize_error_msgs[$option_type], $key ), true );
						$val = $def_val;
					}
					break;

				// empty of alpha-numeric uppercase (hyphens are allowed as well)
				case 'auth_id':
					$val = preg_replace( array( '/&(shy|ndash|mdash);/', '/(--+)/' ), '-', trim( $val ) );
					if ( $val !== '' && preg_match( '/[^A-Z0-9\-]/', $val ) ) {
						$this->p->notice->err( sprintf( $this->sanitize_error_msgs[$option_type], $val, $key ), true );
						$val = $def_val;
					}
					break;

				// empty or alpha-numeric (upper or lower case), plus underscores
				case 'api_key':
					$val = trim( $val );
					if ( $val !== '' && preg_match( '/[^a-zA-Z0-9_]/', $val ) ) {
						$this->p->notice->err( sprintf( $this->sanitize_error_msgs[$option_type], $key ), true );
						$val = $def_val;
					}
					break;

				case 'date':
				case 'time':
					$val = trim( $val );
					$fmt = $option_type === 'date' ? '/^[0-9]{4,4}-[0-9]{2,2}-[0-9]{2,2}$/' : '/^[0-9]{2,2}:[0-9]{2,2}$/';
					if ( $val !== '' && ! preg_match( $fmt, $val ) ) {
						$this->p->notice->err( sprintf( $this->sanitize_error_msgs[$option_type], $key ), true );
						$val = $def_val;
					}
					break;

				// text strings that can be blank
				case 'ok_blank':
					if ( $val !== '' )
						$val = trim( $val );
					break;

				// text strings that can be blank (line breaks are removed)
				case 'desc':
				case 'one_line':
					if ( $val !== '' )
						$val = trim( preg_replace( '/[\s\n\r]+/s', ' ', $val ) );
					break;

				// empty string or must include at least one HTML tag
				case 'html':
					if ( $val !== '' ) {
						$val = trim( $val );
						if ( ! preg_match( '/<.*>/', $val ) ) {
							$this->p->notice->err( sprintf( $this->sanitize_error_msgs['html'], $key ), true );
							$val = $def_val;
						}
					}
					break;

				// options that cannot be blank (aka empty string)
				case 'code':
				case 'not_blank':
					if ( $val === '' ) {
						$this->p->notice->err( sprintf( $this->sanitize_error_msgs['not_blank'], $key ), true );
						$val = $def_val;
					}
					break;

				// everything else is a 1 or 0 checkbox option 
				case 'checkbox':
				default:
					if ( $def_val === 0 || $def_val === 1 )	// make sure the default option is also a 1 or 0, just in case
						$val = empty( $val ) ? 0 : 1;
					break;
			}

			return $val;
		}

		// query examples:
		//	/html/head/link|/html/head/meta
		//	/html/head/meta[starts-with(@property, 'og:video:')]
		public function get_head_meta( $request, $query = '/html/head/meta', $remove_self = false ) {

			if ( empty( $query ) )
				return false;

			if ( strpos( $request, '<' ) === 0 ) {	// check for HTML content
				if ( $this->p->debug->enabled )
					$this->p->debug->log( 'using html submitted in the request argument' );
				$html = $request;
			} elseif ( strpos( $request, '://' ) === false ) {
				if ( $this->p->debug->enabled )
					$this->p->debug->log( 'exiting early: request argument is not html or valid url' );
				return false;
			} elseif ( ( $html = $this->p->cache->get( $request, 'raw', 'transient' ) ) === false ) {
				if ( $this->p->debug->enabled )
					$this->p->debug->log( 'exiting early: error caching '.$request );
				return false;
			}

			$ret = array();
			$cmt = $this->p->cf['lca'].' meta tags ';

			if ( $remove_self === true && strpos( $html, $cmt.'begin' ) !== false ) {
				if ( $this->p->debug->enabled )
					$this->p->debug->log( 'removing self meta tags' );
				$pre = '<(!--[\s\n\r]+|meta[\s\n\r]+name="'.$this->p->cf['lca'].':mark"[\s\n\r]+content=")';
				$post = '([\s\n\r]+--|"[\s\n\r]*\/?)>';	// make space and slash optional for html optimizers
				$html = preg_replace( '/'.$pre.$cmt.'begin'.$post.'.*'.$pre.$cmt.'end'.$post.'/ms',
					'<!-- '.$this->p->cf['lca'].' meta tags removed -->', $html );
			}

			if ( class_exists( 'DOMDocument' ) ) {
				$doc = new DOMDocument();		// since PHP v4.1.0
				@$doc->loadHTML( $html );		// suppress parsing errors
				$xpath = new DOMXPath( $doc );
				$metas = $xpath->query( $query );
				foreach ( $metas as $m ) {
					$m_atts = array();		// put all attributes in a single array
					foreach ( $m->attributes as $a )
						$m_atts[$a->name] = $a->value;
					if ( isset( $m->textContent ) )
						$m_atts['textContent'] = $m->textContent;
					$ret[$m->tagName][] = $m_atts;
				}
			} else $this->missing_php_class_error( 'DOMDocument' );

			return empty( $ret ) ? 
				false : $ret;
		}

		public function missing_php_class_error( $classname ) {
			if ( $this->p->debug->enabled )
				$this->p->debug->log( $classname.' PHP class is missing' );
			if ( is_admin() )
				$this->p->notice->err( sprintf( __( 'The %1$s PHP class is missing - please contact your hosting provider to install the missing %1$s PHP class.', 'wpsso' ), $classname ), true );
		}

		public function get_body_html( $request, $remove_script = true ) {
			$html = '';

			if ( strpos( $request, '//' ) === 0 )
				$request = SucomUtil::get_prot().':'.$request;

			if ( strpos( $request, '<' ) === 0 ) {	// check for HTML content
				if ( $this->p->debug->enabled )
					$this->p->debug->log( 'using html submitted in the request argument' );
				$html = $request;
			} elseif ( empty( $request ) ) {
				if ( $this->p->debug->enabled )
					$this->p->debug->log( 'exiting early: request argument is empty' );
				return false;
			} elseif ( strpos( $request, 'data:' ) === 0 ) {
				if ( $this->p->debug->enabled )
					$this->p->debug->log( 'exiting early: request argument is inline data' );
				return false;
			} elseif ( strpos( $request, '://' ) === false ) {
				if ( $this->p->debug->enabled )
					$this->p->debug->log( 'exiting early: request argument is not html or valid url' );
				return false;
			} else {
				if ( $this->p->debug->enabled )
					$this->p->debug->log( 'fetching body html for '.$request );
				if ( ( $html = $this->p->cache->get( $request, 'raw', 'transient' ) ) === false ) {
					if ( $this->p->debug->enabled )
						$this->p->debug->log( 'exiting early: error caching '.$request );
					return false;
				}
			}

			$html = preg_replace( '/^.*<body[^>]*>(.*)<\/body>.*$/Ums', '$1', $html );

			if ( $remove_script )
				$html = preg_replace( '/<script[^>]*>.*<\/script>/Ums', '', $html );

			return $html;
		}

		public function log_is_functions() {
			$is_functions = array( 
				'is_ajax',
				'is_archive',
				'is_attachment',
				'is_author',
				'is_category',
				'is_front_page',
				'is_home',
				'is_multisite',
				'is_page',
				'is_search',
				'is_single',
				'is_singular',
				'is_ssl',
				'is_tag',
				'is_tax',
				/*
				 * common e-commerce / woocommerce functions
				 */
				'is_account_page',
				'is_cart',
				'is_checkout',
				'is_checkout_pay_page',
				'is_product',
				'is_product_category',
				'is_product_tag',
				'is_shop',
				/*
				 *
				 */
				'is_amp_endpoint',
			);
			$is_functions = apply_filters( $this->p->cf['lca'].'_is_functions', $is_functions );
			foreach ( $is_functions as $function ) 
				if ( function_exists( $function ) && $function() )
					$this->p->debug->log( $function.'() = true' );
		}

		public function get_default_author_id( $opt_pre = 'og' ) {
			if ( SucomUtil::get_const( 'WPSSO_DEFAULT_AUTHOR_OPTIONS' ) ) {
				$user_id = isset( $this->p->options[$opt_pre.'_def_author_id'] ) ? 
					$this->p->options[$opt_pre.'_def_author_id'] : null;
				return apply_filters( $this->p->cf['lca'].'_'.$opt_pre.'_default_author_id', $user_id );
			} else return null;
		}

		// returns an author id if the default author is forced
		public function force_default_author( array &$mod, $opt_pre = 'og' ) {
			if ( SucomUtil::get_const( 'WPSSO_DEFAULT_AUTHOR_OPTIONS' ) ) {
				$author_id = $this->get_default_author_id( $opt_pre );
				return $author_id && $this->force_default( 'author', $mod, $opt_pre ) ? $author_id : false;
			} else return false;
		}

		// returns true if the default image is forced
		public function force_default_image( array &$mod, $opt_pre = 'og' ) {
			return $this->force_default( 'img', $mod, $opt_pre );
		}

		// returns true if the default video is forced
		public function force_default_video( array &$mod, $opt_pre = 'og' ) {
			return $this->force_default( 'vid', $mod, $opt_pre );
		}

		// $type = author | img | vid
		public function force_default( $type, array &$mod, $opt_pre = 'og') {

			$lca = $this->p->cf['lca'];
			$def = array();

			// setup default true / false values
			foreach ( array( 'id', 'url', 'on_index', 'on_search' ) as $key )
				$def[$key] = apply_filters( $lca.'_'.$opt_pre.'_default_'.$type.'_'.$key, 
					( isset( $this->p->options[$opt_pre.'_def_'.$type.'_'.$key] ) ? 
						$this->p->options[$opt_pre.'_def_'.$type.'_'.$key] : null ) );

			// save some time - if no default media, then return false
			if ( empty( $def['id'] ) && 
				empty( $def['url'] ) )
					$ret = false;

			// check for singular pages first
			elseif ( $mod['is_post'] )
				$ret = false;

			// check for user pages first
			elseif ( $mod['is_user'] )
				$ret = false;

			elseif ( ! empty( $def['on_index'] ) &&
				( is_home() || is_archive() || $mod['is_term'] ) )
					$ret = true;

			elseif ( ! empty( $def['on_search'] ) &&
				is_search() )
					$ret = true;

			else $ret = false;

			// 'wpsso_force_default_img' is hooked by the woocommerce module (false for product category and tag pages)
			$ret = apply_filters( $this->p->cf['lca'].'_force_default_'.$type, $ret, $mod, $opt_pre );

			if ( $ret && $this->p->debug->enabled )
				$this->p->debug->log( 'default '.$type.' is forced' );

			return $ret;
		}

		public function get_cache_file_url( $url, $url_ext = '' ) {

			if ( empty( $this->p->options['plugin_file_cache_exp'] ) ||
				! isset( $this->p->cache->base_dir ) )	// check for cache attribute, just in case
					return $url;

			return ( apply_filters( $this->p->cf['lca'].'_rewrite_url',
				$this->p->cache->get( $url, 'url', 'file', $this->p->options['plugin_file_cache_exp'], false, $url_ext ) ) );
		}

		public static function save_all_times( $lca, $version ) {
			self::save_time( $lca, $version, 'update', $version );	// $protect only if same version
			self::save_time( $lca, $version, 'install', true );	// $protect = true
			self::save_time( $lca, $version, 'activate' );		// always update timestamp
		}

		// $protect = true/false/version
		public static function save_time( $lca, $version, $type, $protect = false ) {
			if ( ! is_bool( $protect ) ) {
				if ( ! empty( $protect ) ) {
					if ( ( $ts_version = self::get_option_key( WPSSO_TS_NAME, $lca.'_'.$type.'_version' ) ) !== null &&
						version_compare( $ts_version, $protect, '==' ) )
							$protect = true;
					else $protect = false;
				} else $protect = true;	// just in case
			}
			if ( ! empty( $version ) )
				self::update_option_key( WPSSO_TS_NAME, $lca.'_'.$type.'_version', $version, $protect );
			self::update_option_key( WPSSO_TS_NAME, $lca.'_'.$type.'_time', time(), $protect );
		}

		// get the timestamp array and perform a quick sanity check
		public function get_all_times() {
			$has_changed = false;
			$ts = get_option( WPSSO_TS_NAME, array() );
			foreach ( $this->p->cf['plugin'] as $lca => $info ) {
				if ( empty( $info['version'] ) )
					continue;
				foreach ( array( 'update', 'install', 'activate' ) as $type ) {
					if ( empty( $ts[$lca.'_'.$type.'_time'] ) ||
						( $type === 'update' && ( empty( $ts[$lca.'_'.$type.'_version'] ) || 
							version_compare( $ts[$lca.'_'.$type.'_version'], $info['version'], '!=' ) ) ) )
								$has_changed = self::save_time( $lca, $info['version'], $type );
				}
			}
			return $has_changed === false ?
				$ts : get_option( WPSSO_TS_NAME, array() );
		}

		public function get_inline_vars() {
			return array(
				'%%request_url%%',
				'%%sharing_url%%',
				'%%short_url%%',
			);
		}

		public function get_inline_vals( $mod = false, &$atts = array() ) {

			// allow compatibility with $use_post as first argument
			// $mod = true | false | post_id | $mod array
			if ( ! is_array( $mod ) )
				$mod = $this->get_page_mod( $mod );

			if ( isset( $atts['url'] ) )
				$sharing_url = $atts['url'];
			else $sharing_url = $this->get_sharing_url( $mod, 
				( isset( $atts['add_page'] ) ? 
					$atts['add_page'] : true ) );

			if ( is_admin() )
				$request_url = $sharing_url;
			else $request_url = self::get_prot().'://'.$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];

			$short_url = empty( $atts['short_url'] ) ?
				apply_filters( $this->p->cf['lca'].'_shorten_url', 
					$sharing_url, $this->p->options['plugin_shortener'] ) :
				$atts['short_url'];

			return array(
				$request_url,		// %%request_url%%
				$sharing_url,		// %%sharing_url%%
				$short_url,		// %%short_url%%
			);
		}

		// allow the variables and values array to be extended
		// $ext must be an associative array with key/value pairs to be replaced
		public function replace_inline_vars( $text, $mod = false, $atts = array(), $extra = array() ) {

			if ( strpos( $text, '%%' ) === false ) {
				if ( $this->p->debug->enabled )
					$this->p->debug->log( 'exiting early: no inline vars' );
				return $text;
			}

			// allow compatibility with $use_post as first argument
			// $mod = true | false | post_id | $mod array
			if ( ! is_array( $mod ) )
				$mod = $this->get_page_mod( $mod );

			$vars = $this->get_inline_vars();
			$vals = $this->get_inline_vals( $mod, $atts );

			if ( ! empty( $extra ) && self::is_assoc( $extra ) ) {
				foreach ( $extra as $match => $replace ) {
					$vars[] = '%%'.$match.'%%';
					$vals[] = $replace;
				}
			}

			ksort( $vars );
			ksort( $vals );

			return str_replace( $vars, $vals, $text );
		}

		public function add_image_url_sizes( $keys, array &$opts ) {
			if ( self::get_const( 'WPSSO_PHP_GETIMGSIZE_DISABLE' ) )
				return $opts;

			if ( ! is_array( $keys ) )
				$keys = array( $keys );

			foreach ( $keys as $prefix ) {
				$media_url = self::get_mt_media_url( $opts, $prefix );

				if ( ! empty( $media_url ) && strpos( $media_url, '://' ) !== false ) {
					list( 
						$opts[$prefix.':width'],
						$opts[$prefix.':height'],
						$image_type,
						$image_attr 
					) = @getimagesize( $media_url );

					if ( $this->p->debug->enabled )
						$this->p->debug->log( 'getimagesize() for '.$media_url.' returned '.
							$opts[$prefix.':width'].'x'.$opts[$prefix.':height'] );
				} else {
					foreach ( array( 'width', 'height' ) as $attr )
						if ( isset( $opts[$prefix.':'.$attr] ) )
							$opts[$prefix.':'.$attr] = -1;
				}
			}
			return $opts;
		}

		// accepts json script or json array
		public function json_format( $json, $options = 0, $depth = 32 ) {

			$do_pretty_print = self::get_const( 'WPSSO_JSON_PRETTY_PRINT' );
			$ext_json_disable = self::get_const( 'WPSSO_EXT_JSON_DISABLE', false );
			$do_ext_pretty = false;

			if ( $options === 0 &&
				defined( 'JSON_UNESCAPED_SLASHES' ) )
					$options = JSON_UNESCAPED_SLASHES;	// PHP v5.4

			// decide if the encoded json will be minimized or not
			if ( is_admin() || $this->p->debug->enabled || $do_pretty_print ) {
				if ( defined( 'JSON_PRETTY_PRINT' ) )		// PHP v5.4
					$options = $options|JSON_PRETTY_PRINT;
				else $do_ext_pretty = true;			// use the SuextJsonFormat lib
			}

			// encode the json
			if ( ! is_string( $json ) )
				$json = self::json_encode_array( $json, $options, $depth );	// prefers wp_json_encode() to json_encode()

			// use the pretty print external library for older PHP versions
			// define WPSSO_EXT_JSON_DISABLE as true to prevent external json formatting 
			if ( ! $ext_json_disable && $do_ext_pretty ) {
				$classname = WpssoConfig::load_lib( false, 'ext/json-format', 'suextjsonformat' );
				if ( $classname !== false && class_exists( $classname ) )
					$json = SuextJsonFormat::get( $json, $options, $depth );
			}

			return $json;
		}

		/*
		 * Return the post / user / term id, module name, and the module object reference.
		 */
		public function get_page_mod( $use_post = false, $mod = false, &$wp_obj = false ) {

			if ( ! is_array( $mod ) ) {
				$mod = array();
			} elseif ( ! empty( $mod['obj'] ) ) {
				if ( $this->p->debug->enabled )
					$this->p->debug->log( 'exiting early: module object is defined' );
				return $mod;
			}

			if ( $this->p->debug->enabled )
				$this->p->debug->mark();

			// check for a recognized object
			if ( is_object( $wp_obj ) ) {
				if ( $this->p->debug->enabled )
					$this->p->debug->log( 'wp_obj is '.get_class( $wp_obj ) );
				switch ( get_class( $wp_obj ) ) {
					case 'WP_Post':
						$mod['name'] = 'post';
						$mod['id'] = $wp_obj->ID;
						break;
					case 'WP_Term':
						$mod['name'] = 'term';
						$mod['id'] = $wp_obj->term_id;
						break;
					case 'WP_User':
						$mod['name'] = 'user';
						$mod['id'] = $wp_obj->ID;
						break;
				}
			}

			// we need a module name to get the id and object
			if ( empty( $mod['name'] ) ) {
				if ( self::is_post_page( $use_post ) )	// $use_post = true | false | post_id 
					$mod['name'] = 'post';
				elseif ( self::is_term_page() )
					$mod['name'] = 'term';
				elseif ( self::is_user_page() )
					$mod['name'] = 'user';
				else $mod['name'] = false;
			}

			if ( empty( $mod['id'] ) ) {
				if ( $mod['name'] === 'post' )
					$mod['id'] = self::get_post_object( $use_post, 'id' );	// $use_post = true | false | post_id 
				elseif ( $mod['name'] === 'term' )
					$mod['id'] = self::get_term_object( false, '', 'id' );
				elseif ( $mod['name'] === 'user' )
					$mod['id'] = self::get_user_object( false, 'id' );
				else $mod['id'] = false;
			}

			// make sure we have a complete $mod array
			if ( isset( $this->p->m['util'][$mod['name']] ) )
				$mod = $this->p->m['util'][$mod['name']]->get_mod( $mod['id'] );
			else $mod = array_merge( WpssoMeta::$mod_array, $mod );

			$mod['use_post'] = $use_post;
			$mod['is_home_index'] = is_home() && ! $mod['is_home_page'] ? true : false;	// blog index page (archive)
			$mod['is_home'] = $mod['is_home_page'] || $mod['is_home_index'] ? true : false;	// home page (any)

			if ( $this->p->debug->enabled )
				$this->p->debug->log_arr( '$mod ', $mod );

			return $mod;
		}

		// $mod is false when used for open graph meta tags and buttons in widget
		// $mod is true when buttons are added to individual posts on an index webpage
		public function get_sharing_url( $mod = false, $add_page = true ) {

			if ( $this->p->debug->enabled ) {
				$this->p->debug->log_args( array( 
					'mod' => $mod,
					'add_page' => $add_page,
				) );
			}

			$lca = $this->p->cf['lca'];
			$url = false;

			// allow compatibility with $use_post as first argument
			// $mod = true | false | post_id | $mod array
			if ( ! is_array( $mod ) )
				$mod = $this->get_page_mod( $mod );

			if ( $mod['is_post'] ) {
				if ( ! empty( $mod['id'] ) ) {
					if ( is_object( $mod['obj'] ) )
						$url = $mod['obj']->get_options( $mod['id'], 'sharing_url' );

					if ( ! empty( $url ) ) {
						if ( $this->p->debug->enabled )
							$this->p->debug->log( 'custom post sharing_url = '.$url );
					} else $url = $this->check_sharing_url( get_permalink( $mod['id'] ), 'post permalink' );

					if ( ! empty( $url ) && $add_page && get_query_var( 'page' ) > 1 ) {
						global $wp_rewrite;
						$post_obj = self::get_post_object( $mod['id'] );
						$numpages = substr_count( $post_obj->post_content, '<!--nextpage-->' ) + 1;

						if ( $numpages && get_query_var( 'page' ) <= $numpages ) {
							if ( ! $wp_rewrite->using_permalinks() || strpos( $url, '?' ) !== false )
								$url = add_query_arg( 'page', get_query_var( 'page' ), $url );
							else $url = user_trailingslashit( trailingslashit( $url ).get_query_var( 'page' ) );
						}
						if ( $this->p->debug->enabled )
							$this->p->debug->log( 'add page query url = '.$url );
					}
				}
				$url = apply_filters( $lca.'_post_url', $url, $mod, $add_page );

			} else {
				if ( $mod['is_home'] ) {
					if ( 'page' === get_option( 'show_on_front' ) ) {	// show_on_front = posts | page
						$url = $this->check_sharing_url( get_permalink( get_option( 'page_for_posts' ) ), 'page for posts' );
					} else {
						$url = apply_filters( $lca.'_home_url', home_url( '/' ) );
						if ( $this->p->debug->enabled )
							$this->p->debug->log( 'home url = '.$url );
					}
				} elseif ( $mod['is_term'] ) {
					if ( ! empty( $mod['id'] ) ) {
						if ( is_object( $mod['obj'] ) )
							$url = $mod['obj']->get_options( $mod['id'], 'sharing_url' );

						if ( ! empty( $url ) ) {
							if ( $this->p->debug->enabled )
								$this->p->debug->log( 'custom term sharing_url = '.$url );
						} else $url = $this->check_sharing_url( get_term_link( $mod['id'], $mod['tax_slug'] ), 'term link' );
					} 
					$url = apply_filters( $lca.'_term_url', $url, $mod, $add_page );

				} elseif ( $mod['is_user'] ) {
					if ( ! empty( $mod['id'] ) ) {
						if ( is_object( $mod['obj'] ) )
							$url = $mod['obj']->get_options( $mod['id'], 'sharing_url' );

						if ( ! empty( $url ) ) {
							if ( $this->p->debug->enabled )
								$this->p->debug->log( 'custom user sharing_url = '.$url );
						} else $url = $this->check_sharing_url( get_author_posts_url( $mod['id'] ), 'author posts' );
					}
					$url = apply_filters( $lca.'_author_url', $url, $mod, $add_page );

				} elseif ( is_search() ) {
					$url = $this->check_sharing_url( get_search_link(), 'search link' );
					$url = apply_filters( $lca.'_search_url', $url, $mod, $add_page );

				} elseif ( function_exists( 'get_post_type_archive_link' ) && is_post_type_archive() ) {
					$url = $this->check_sharing_url( get_post_type_archive_link( get_query_var( 'post_type' ) ), 'post type archive' );

				} elseif ( is_archive() ) {
					if ( is_date() ) {
						if ( is_day() )
							$url = $this->check_sharing_url( get_day_link( get_query_var( 'year' ), 
								get_query_var( 'monthnum' ), get_query_var( 'day' ) ), 'day link' );
						elseif ( is_month() )
							$url = $this->check_sharing_url( get_month_link( get_query_var( 'year' ), 
								get_query_var( 'monthnum' ) ), 'month link' );
						elseif ( is_year() )
							$url = $this->check_sharing_url( get_year_link( get_query_var( 'year' ) ), 'year link' );
					}
					$url = apply_filters( $lca.'_archive_url', $url, $mod, $add_page );
				}

				if ( ! empty( $url ) && $add_page && get_query_var( 'paged' ) > 1 ) {
					global $wp_rewrite;
					if ( ! $wp_rewrite->using_permalinks() )
						$url = add_query_arg( 'paged', get_query_var( 'paged' ), $url );
					else {
						if ( $mod['is_home_page'] ) {	// static home page (have post id)
							$base = $GLOBALS['wp_rewrite']->using_index_permalinks() ? 'index.php/' : '/';
							$url = home_url( $base );
							if ( $this->p->debug->enabled )
								$this->p->debug->log( 'home_url for '.$base.' = '.$url );
						}
						$url = user_trailingslashit( trailingslashit( $url ).
							trailingslashit( $wp_rewrite->pagination_base ).get_query_var( 'paged' ) );
					}
					if ( $this->p->debug->enabled )
						$this->p->debug->log( 'add paged query url = '.$url );
				}
			}

			// fallback for themes and plugins that don't use the standard wordpress functions/variables
			if ( empty ( $url ) ) {
				// strip out tracking query arguments by facebook, google, etc.
				$url = preg_replace( '/([\?&])(fb_action_ids|fb_action_types|fb_source|fb_aggregation_id|'.
					'utm_source|utm_medium|utm_campaign|utm_term|gclid|pk_campaign|pk_kwd)=[^&]*&?/i',
						'$1', self::get_prot().'://'.$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'] );
				if ( $this->p->debug->enabled )
					$this->p->debug->log( 'server request url = '.$url );
			}

			return apply_filters( $lca.'_sharing_url', $url, $mod, $add_page );
		}

		public function check_sharing_url( $url, $source = 'sharing' ) {
			if ( is_string( $url ) ) {
				if ( $this->p->debug->enabled )
					$this->p->debug->log( $source.' url = '.$url );
				return $url;
			} else {
				if ( $this->p->debug->enabled ) {
					$this->p->debug->log( $source.' url is '.gettype( $url ) );
					if ( is_wp_error( $url ) )
						$this->p->debug->log( $source.' url error: '.$url->get_error_message() );
				}
				return false;
			}
		}

		public function fix_relative_url( $url ) {
			if ( ! empty( $url ) && strpos( $url, '://' ) === false ) {

				if ( $this->p->debug->enabled )
					$this->p->debug->log( 'relative url found = '.$url );

				if ( strpos( $url, '//' ) === 0 )
					$url = self::get_prot().':'.$url;
				elseif ( strpos( $url, '/' ) === 0 ) 
					$url = home_url( $url );
				else {
					$base = self::get_prot().'://'.$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];
					if ( strpos( $base, '?' ) !== false ) {
						$base_parts = explode( '?', $base );
						$base = reset( $base_parts );
					}
					$url = trailingslashit( $base, false ).$url;
				}

				if ( $this->p->debug->enabled )
					$this->p->debug->log( 'relative url fixed = '.$url );
			}
			return $url;
		}

		public function clear_uniq_urls( $context = 'default' ) {
			$cleared = isset( $this->uniq_urls[$context] ) ?
				count( $this->uniq_urls[$context] ) : 0;
			$this->uniq_urls[$context] = array();
			if ( $this->p->debug->enabled )
				$this->p->debug->log( 'cleared uniq url cache for context '.$context ); 
			return $cleared;
		}

		public function is_dupe_url( $url, $context = 'default' ) {
			return $this->is_uniq_url( $url, $context ) ? false : true;
		}

		public function is_uniq_url( $url, $context = 'default' ) {
			if ( empty( $url ) ) 
				return false;

			// complete the url with a protocol name
			if ( strpos( $url, '//' ) === 0 )
				$url = self::get_prot().'//'.$url;

			if ( $this->p->debug->enabled && 
				strpos( $url, '://' ) === false )
					$this->p->debug->log( 'incomplete url given for context '.$context.': '.$url );

			if ( ! isset( $this->uniq_urls[$context][$url] ) ) {
				$this->uniq_urls[$context][$url] = 1;
				return true;
			} else {
				if ( $this->p->debug->enabled )
					$this->p->debug->log( 'duplicate url rejected for context '.$context.': '.$url ); 
				return false;
			}
		}

		public function is_maxed( &$arr, $num = 0 ) {
			if ( ! is_array( $arr ) ) 
				return false;
			if ( $num > 0 && count( $arr ) >= $num ) 
				return true;
			return false;
		}

		public function push_max( &$dst, &$src, $num = 0 ) {
			if ( ! is_array( $dst ) || 
				! is_array( $src ) ) 
					return false;

			// if the array is not empty, or contains some non-empty values, then push it
			if ( ! empty( $src ) && 
				array_filter( $src ) ) 
					array_push( $dst, $src );

			return $this->slice_max( $dst, $num );	// returns true or false
		}

		public function slice_max( &$arr, $num = 0 ) {
			if ( ! is_array( $arr ) )
				return false;

			$has = count( $arr );

			if ( $num > 0 ) {
				if ( $has == $num ) {
					if ( $this->p->debug->enabled )
						$this->p->debug->log( 'max values reached ('.$has.' == '.$num.')' );
					return true;
				} elseif ( $has > $num ) {
					if ( $this->p->debug->enabled )
						$this->p->debug->log( 'max values reached ('.$has.' > '.$num.') - slicing array' );
					$arr = array_slice( $arr, 0, $num );
					return true;
				}
			}

			return false;
		}

		// get maximum media values from custom meta or plugin settings
		public function get_max_nums( array &$mod, $opt_pre = 'og' ) {
			$max = array();
			$opt_keys = array( $opt_pre.'_vid_max', $opt_pre.'_img_max' );

			foreach ( $opt_keys as $max_key ) {

				if ( ! empty( $mod['id'] ) && is_object( $mod['obj'] ) )
					$max_val = $mod['obj']->get_options( $mod['id'], $max_key );
				else $max_val = null;	// default value returned by get_options() if index key is missing

				// quick sanitation of returned value - ignore -1 values
				if ( $max_val !== null & is_numeric( $max_val ) && $max_val >= 0 ) {
					$max[$max_key] = $max_val;
					if ( $this->p->debug->enabled )
						$this->p->debug->log( 'found custom meta '.$max_key.' = '.$max_val );
				} else $max[$max_key] = isset( $this->p->options[$max_key] ) ?	// fallback to options
					$this->p->options[$max_key] : 0;
			}

			return $max;
		}

		public function delete_expired_db_transients( $all = false ) { 
			global $wpdb;
			$deleted = 0;
			$time = isset ( $_SERVER['REQUEST_TIME'] ) ?
				(int) $_SERVER['REQUEST_TIME'] : time() ; 
			$dbquery = 'SELECT option_name FROM '.$wpdb->options.
				' WHERE option_name LIKE \'_transient_timeout_'.$this->p->cf['lca'].'_%\'';
			$dbquery .= $all === true ? ';' : ' AND option_value < '.$time.';';
			$expired = $wpdb->get_col( $dbquery ); 
			foreach( $expired as $transient ) { 
				$key = str_replace( '_transient_timeout_', '', $transient );
				if ( delete_transient( $key ) )
					$deleted++;
			}
			return $deleted;
		}

		public function delete_expired_file_cache( $all = false ) {
			$deleted = 0;
			$time = isset ( $_SERVER['REQUEST_TIME'] ) ? (int) $_SERVER['REQUEST_TIME'] : time() ; 
			$time = empty( $this->p->options['plugin_file_cache_exp'] ) ? 
				$time : $time - $this->p->options['plugin_file_cache_exp'];
			$cachedir = constant( $this->p->cf['uca'].'_CACHEDIR' );
			if ( ! $dh = @opendir( $cachedir ) )
				$this->p->notice->err( 'Failed to open directory '.$cachedir.' for reading.', true );
			else {
				while ( $fn = @readdir( $dh ) ) {
					$filepath = $cachedir.$fn;
					if ( ! preg_match( '/^(\..*|index\.php)$/', $fn ) && is_file( $filepath ) && 
						( $all === true || filemtime( $filepath ) < $time ) ) {
						if ( ! @unlink( $filepath ) ) 
							$this->p->notice->err( 'Error removing file '.$filepath, true );
						else $deleted++;
					}
				}
				closedir( $dh );
			}
			return $deleted;
		}

		public function get_remote_content( $url = '', $file = '', $version = '', $expire_secs = 86400 ) {
			$content = false;
			$get_remote = empty( $url ) ? false : true;

			if ( $this->p->is_avail['cache']['transient'] ) {
				$cache_salt = __METHOD__.'(url:'.$url.'_file:'.$file.'_version:'.$version.')';
				$cache_id = $this->p->cf['lca'].'_'.md5( $cache_salt );
				$cache_type = 'object cache';
				$content = get_transient( $cache_id );
				if ( $content !== false )
					return $content;	// no need to save, return now
			} else $get_remote = false;

			if ( $get_remote === true && $expire_secs > 0 ) {
				$content = $this->p->cache->get( $url, 'raw', 'file', $expire_secs );
				if ( empty( $content ) )
					$get_remote = false;
			} else $get_remote = false;

			if ( $get_remote === false && ! empty( $file ) && $fh = @fopen( $file, 'rb' ) ) {
				$content = fread( $fh, filesize( $file ) );
				fclose( $fh );
			}

			if ( $this->p->is_avail['cache']['transient'] )
				set_transient( $cache_id, $content, $this->p->options['plugin_object_cache_exp'] );

			return $content;
		}

		public function parse_readme( $ext, $expire_secs = 86400, $use_cache = true ) {

			if ( $this->p->debug->enabled ) {
				$this->p->debug->log_args( array( 
					'ext' => $ext,
					'expire_secs' => $expire_secs,
					'use_cache' => $use_cache,
				) );
			}

			$plugin_info = array();

			if ( ! defined( strtoupper( $ext ).'_PLUGINDIR' ) ) {
				if ( $this->p->debug->enabled )
					$this->p->debug->log( strtoupper( $ext ).'_PLUGINDIR is undefined and required for readme.txt path' );
				return $plugin_info;
			}

			$readme_txt = constant( strtoupper( $ext ).'_PLUGINDIR' ).'readme.txt';
			$readme_url = isset( $this->p->cf['plugin'][$ext]['url']['readme'] ) ? 
				$this->p->cf['plugin'][$ext]['url']['readme'] : '';
			$get_remote = empty( $readme_url ) ? false : true;	// fetch readme from wordpress.org by default
			$content = '';

			if ( $this->p->is_avail['cache']['transient'] ) {
				$cache_salt = __METHOD__.'(url:'.$readme_url.'_txt:'.$readme_txt.')';
				$cache_id = $ext.'_'.md5( $cache_salt );
				$cache_type = 'object cache';
				if ( $this->p->debug->enabled )
					$this->p->debug->log( $cache_type.': transient salt '.$cache_salt );
				$plugin_info = $use_cache ? get_transient( $cache_id ) : false;
				if ( is_array( $plugin_info ) ) {
					if ( $this->p->debug->enabled )
						$this->p->debug->log( $cache_type.': plugin_info retrieved from transient '.$cache_id );
					return $plugin_info;
				}
			} else $get_remote = false;	// use local if transient cache is disabled

			// get remote readme.txt file
			if ( $get_remote === true && $expire_secs > 0 ) {
				if ( ! $use_cache )
					$this->p->cache->clear( $readme_url );	// clear the wp object, transient, and file cache
				$content = $this->p->cache->get( $readme_url, 'raw', 'file', $expire_secs );
			}

			// fallback to local readme.txt file
			if ( empty( $content ) && $fh = @fopen( $readme_txt, 'rb' ) ) {
				$get_remote = false;
				$content = fread( $fh, filesize( $readme_txt ) );
				fclose( $fh );
			}

			if ( ! empty( $content ) ) {
				$parser = new SuextParseReadme( $this->p->debug );
				$plugin_info = $parser->parse_readme_contents( $content );

				// remove possibly inaccurate information from local file
				if ( $get_remote !== true ) {
					foreach ( array( 'stable_tag', 'upgrade_notice' ) as $key )
						if ( array_key_exists( $key, $plugin_info ) )
							unset ( $plugin_info[$key] );
				}
			}

			// save the parsed readme (aka $plugin_info) to the transient cache
			if ( $this->p->is_avail['cache']['transient'] ) {
				set_transient( $cache_id, $plugin_info, $this->p->options['plugin_object_cache_exp'] );
				if ( $this->p->debug->enabled )
					$this->p->debug->log( $cache_type.': plugin_info saved to transient '.
						$cache_id.' ('.$this->p->options['plugin_object_cache_exp'].' seconds)');
			}

			return $plugin_info;
		}

		public function get_admin_url( $menu_id = '', $link_text = '', $menu_lib = '' ) {

			$hash = '';
			$query = '';
			$admin_url = '';
			$lca = $this->p->cf['lca'];

			// $menu_id may start with a hash or query, so parse before checking its value
			if ( strpos( $menu_id, '#' ) !== false )
				list( $menu_id, $hash ) = explode( '#', $menu_id );

			if ( strpos( $menu_id, '?' ) !== false )
				list( $menu_id, $query ) = explode( '?', $menu_id );

			if ( empty( $menu_id ) ) {
				$current = $_SERVER['REQUEST_URI'];
				if ( preg_match( '/^.*\?page='.$lca.'-([^&]*).*$/', $current, $match ) )
					$menu_id = $match[1];
				else $menu_id = key( $this->p->cf['*']['lib']['submenu'] );	// default to first submenu
			}

			// find the menu_lib value for this menu_id
			if ( empty( $menu_lib ) ) {
				foreach ( $this->p->cf['*']['lib'] as $menu_lib => $menu ) {
					if ( isset( $menu[$menu_id] ) )
						break;
					else $menu_lib = '';
				}
			}

			if ( empty( $menu_lib ) ||
				empty( $this->p->cf['wp']['admin'][$menu_lib]['page'] ) )
					return;

			$parent_slug = $this->p->cf['wp']['admin'][$menu_lib]['page'].'?page='.$lca.'-'.$menu_id;

			switch ( $menu_lib ) {
				case 'sitesubmenu':
					$admin_url = network_admin_url( $parent_slug );
					break;
				default:
					$admin_url = admin_url( $parent_slug );
					break;
			}

			if ( ! empty( $query ) ) 
				$admin_url .= '&'.$query;

			if ( ! empty( $hash ) ) 
				$admin_url .= '#'.$hash;

			if ( empty( $link_text ) ) 
				return $admin_url;
			else return '<a href="'.$admin_url.'">'.$link_text.'</a>';
		}

		public function do_metabox_tabs( $metabox = '', $tabs = array(), $table_rows = array(), $args = array() ) {

			$tab_keys = array_keys( $tabs );
			$default_tab = '_'.reset( $tab_keys );		// must start with an underscore
			$class_metabox_tabs = 'sucom-metabox-tabs';
			$class_link = 'sucom-tablink';
			$class_tabset = 'sucom-tabset';

			if ( ! empty( $metabox ) ) {
				$metabox = '_'.$metabox;		// must start with an underscore
				$class_metabox_tabs .= ' '.$class_metabox_tabs.$metabox;
				$class_link .= ' '.$class_link.$metabox;
			}

			// allow a css ID to be passed as a query argument
			extract( array_merge( array(
				'scroll_to' => isset( $_GET['scroll_to'] ) ? 
					'#'.self::sanitize_key( $_GET['scroll_to'] ) : '',
			), $args ) );

			echo "\n".'<script type="text/javascript">jQuery(document).ready(function(){ '.
				'sucomTabs(\''.$metabox.'\', \''.$default_tab.'\', \''.$scroll_to.'\'); });</script>'."\n";
			echo '<div class="'.$class_metabox_tabs.'">'."\n";
			echo '<ul class="'.$class_metabox_tabs.'">'."\n";
			foreach ( $tabs as $tab => $title ) {
				$class_href_key = $class_tabset.$metabox.'-tab_'.$tab;
				echo '<div class="tab_left">&nbsp;</div><li class="'.
					$class_href_key.'"><a class="'.$class_link.'" href="#'.
					$class_href_key.'">'.$title.'</a></li>'."\n";
			}
			echo '</ul><!-- .'.$class_metabox_tabs.' -->'."\n";

			foreach ( $tabs as $tab => $title ) {
				$class_href_key = $class_tabset.$metabox.'-tab_'.$tab;
				$this->do_table_rows( $table_rows[$tab], $class_href_key, ( empty( $metabox ) ?
					'' : $class_tabset.$metabox ), $class_tabset );
			}
			echo '</div><!-- .'.$class_metabox_tabs.' -->'."\n\n";
		}

		public function do_table_rows( $table_rows, $class_href_key = '', $class_tabset_mb = '', $class_tabset = '' ) {

			if ( ! is_array( $table_rows ) )	// just in case
				return;

			$lca = empty( $this->p->cf['lca'] ) ? 
				'sucom' : $this->p->cf['lca'];
			$total_rows = count( $table_rows );
			$count_rows = 0;
			$hidden_opts = 0;
			$hidden_rows = 0;

			// use call_user_func() instead of $classname::show_opts() for PHP 5.2
			$show_opts = class_exists( $lca.'user' ) ? 
				call_user_func( array( $lca.'user', 'show_opts' ) ) : 'basic';

			foreach ( $table_rows as $key => $row ) {
				if ( empty( $row ) )	// just in case
					continue;

				// default row class and id attribute values
				$tr = array(
					'class' => 'sucom_alt'.( $count_rows % 2 ).
						( $count_rows === 0 ? ' first_row' : '' ).
						( $count_rows === ( $total_rows - 1 ) ? ' last_row' : '' ),
					'id' => ( is_int( $key ) ? '' : 'tr_'.$key )
				);

				// if we don't already have a table row tag, then add one
				if ( strpos( $row, '<tr ' ) === false )
					$row = '<tr class="'.$tr['class'].'"'.
						( empty( $tr['id'] ) ? '' : ' id="'.$tr['id'].'"' ).'>'.$row;
				else {
					foreach ( $tr as $att => $val ) {
						if ( empty( $tr[$att] ) )
							continue;

						// if we're here, then we have a table row tag already
						// count the number of rows and options that are hidden
						if ( $att === 'class' && ! empty( $show_opts ) && 
							( $matched = preg_match( '/<tr [^>]*class="[^"]*hide(_row)?_in_'.$show_opts.'[" ]/', $row, $m ) > 0 ) ) {
							if ( ! isset( $m[1] ) )
								$hidden_opts += preg_match_all( '/(<th|<tr[^>]*><td)/', $row, $all_matches );
							$hidden_rows += $matched;
						}

						// add the attribute value
						$row = preg_replace( '/(<tr [^>]*'.$att.'=")([^"]*)(")/', '$1$2 '.$tr[$att].'$3', $row, -1, $cnt );

						// if one hasn't been added, then add both the attribute and its value
						if ( $cnt < 1 )
							$row = preg_replace( '/(<tr )/', '$1'.$att.'="'.$tr[$att].'" ', $row, -1, $cnt );
					}
				}

				// add a closing table row tag if we don't already have one
				if ( strpos( $row, '</tr>' ) === false )
					$row .= '</tr>'."\n";

				// update the table row array element with the new value
				$table_rows[$key] = $row;

				$count_rows++;
			}

			if ( $count_rows === 0 ) {
				$table_rows[] = '<tr><td align="center"><p><em>'.__( 'No options available.',
					'wpsso' ).'</em></p></td></tr>';
				$count_rows++;
			}

			echo '<div class="'.
				( empty( $show_opts ) ? '' : 'sucom-show_'.$show_opts ).
				( empty( $class_tabset ) ? '' : ' '.$class_tabset ).
				( empty( $class_tabset_mb ) ? '' : ' '.$class_tabset_mb ).
				( empty( $class_href_key ) ? '' : ' '.$class_href_key ).
			'">'."\n";

			echo '<table class="sucom-setting '.$lca.
				( empty( $class_href_key ) ? '' : ' '.$class_href_key ).
				( $hidden_rows > 0 && $hidden_rows === $count_rows ?	// if all rows hidden, then hide the whole table
					' hide_in_'.$show_opts : '' ).'">'."\n";

			foreach ( $table_rows as $row )
				echo $row;

			echo '</table>'."\n";
			echo '</div>'."\n";

			$show_opts_label = $this->p->cf['form']['show_options'][$show_opts];
			if ( $hidden_opts > 0 ) {
				echo '<div class="hidden_opts_msg '.$class_tabset.'-msg '.$class_tabset_mb.'-msg '.$class_href_key.'-msg">'.
					sprintf( _x( '%1$d additional options not shown in %2$s view', 'option comment', 'wpsso' ), 
						$hidden_opts, _x( $show_opts_label, 'option value', 'wpsso' ) ).
					' (<a href="javascript:void(0);"'.
					' onClick="sucomViewUnhideRows( \''.$class_href_key.'\', \''.$show_opts.'\' );">'.
					_x( 'unhide these options', 'option comment', 'wpsso' ).'</a>)</div>'."\n";
			} elseif ( $hidden_rows > 0 ) {
				echo '<div class="hidden_opts_msg '.$class_tabset.'-msg '.$class_tabset_mb.'-msg '.$class_href_key.'-msg">'.
					sprintf( _x( '%1$d additional rows not shown in %2$s view', 'option comment', 'wpsso' ), 
						$hidden_rows, _x( $show_opts_label, 'option value', 'wpsso' ) ).
					' (<a href="javascript:void(0);"'.
					' onClick="sucomViewUnhideRows( \''.$class_href_key.'\', \''.$show_opts.'\', \'hide_row_in\' );">'.
					_x( 'unhide these rows', 'option comment', 'wpsso' ).'</a>)</div>'."\n";
			}
		}
	}
}

?>
