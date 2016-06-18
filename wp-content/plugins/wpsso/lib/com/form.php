<?php
/*
 * License: GPLv3
 * License URI: http://www.gnu.org/licenses/gpl.txt
 * Copyright 2012-2016 Jean-Sebastien Morisset (http://surniaulula.com/)
 */

if ( ! defined( 'ABSPATH' ) ) 
	die( 'These aren\'t the droids you\'re looking for...' );

if ( ! class_exists( 'SucomForm' ) ) {

	class SucomForm {

		private $p;
		private $menu_ext = null;
		private $text_domain = false;

		public $options = array();
		public $defaults = array();
		public $options_name = null;

		public function __construct( &$plugin, $opts_name, &$opts, &$def_opts, $menu_ext = '' ) {
			$this->p =& $plugin;

			if ( $this->p->debug->enabled )
				$this->p->debug->mark();

			$this->options_name =& $opts_name;
			$this->options =& $opts;
			$this->defaults =& $def_opts;
			$this->menu_ext = empty( $menu_ext ) ? $this->p->cf['lca'] : $menu_ext;
			$this->text_domain = isset( $this->p->cf['plugin'][$this->menu_ext]['text_domain'] ) ?
				$this->p->cf['plugin'][$this->menu_ext]['text_domain'] : false;
		}

		public function get_hidden( $name, $value = '' ) {
			if ( empty( $name ) )
				return;	// just in case

			// hide the current options value, unless one is given as an argument to the method
			$value = empty( $value ) && 
				$this->in_options( $name ) ?
					$this->options[$name] : $value;

			return '<input type="hidden" name="'.esc_attr( $this->options_name.'['.$name.']' ).'" value="'.esc_attr( $value ).'" />';
		}

		public function get_checkbox( $name, $class = '', $id = '', $disabled = false, $force = null ) {
			if ( empty( $name ) )
				return;	// just in case

			if ( $this->in_options( $name.':is' ) && 
				$this->options[$name.':is'] === 'disabled' )
					$disabled = true;

			if ( $force !== null )
				$checked = checked( $force, 1, false );
			elseif ( ! $disabled && $this->in_options( $name ) )
				$checked = checked( $this->options[$name], 1, false );
			elseif ( $this->in_defaults( $name ) )
				$checked = checked( $this->defaults[$name], 1, false );
			else $checked = '';

			$html = ( $disabled ? '' : $this->get_hidden( 'is_checkbox_'.$name, 1 ) ).
				'<input type="checkbox"'.
				( $disabled ? ' disabled="disabled"' : ' name="'.esc_attr( $this->options_name.'['.$name.']' ).'" value="1"' ).
				( empty( $class ) ? '' : ' class="'.esc_attr( $class ).'"' ).
				( empty( $id ) ? '' : ' id="checkbox_'.esc_attr( $id ).'"' ).
				$checked.' title="default is '.
				( $this->in_defaults( $name ) && ! empty( $this->defaults[$name] ) ? 'checked' : 'unchecked' ).
				( $disabled ? ' '._x( '(option disabled)', 'option value', $this->text_domain ) : '' ).'" />';

			return $html;
		}

		public function get_no_checkbox( $name, $class = '', $id = '', $force = null ) {
			return $this->get_checkbox( $name, $class, $id, true, $force );
		}

		public function get_radio( $name, $values = array(), $class = '', $id = '', $is_assoc = null, $disabled = false ) {

			if ( empty( $name ) || 
				! is_array( $values ) )
					return;

			if ( $is_assoc === null ) 
				$is_assoc = SucomUtil::is_assoc( $values );

			if ( $this->in_options( $name.':is' ) && 
				$this->options[$name.':is'] === 'disabled' )
					$disabled = true;

			$html = '';

			foreach ( $values as $val => $desc ) {

				// if the array is NOT associative (so regular numbered array), 
				// then the description is used as the saved value as well
				if ( $is_assoc === false )
					$val = $desc;

				if ( $this->text_domain )
					$desc = _x( $desc, 'option value', $this->text_domain );

				$html .= '<input type="radio"'.
					( $disabled ? ' disabled="disabled"' : ' name="'.
						esc_attr( $this->options_name.'['.$name.']' ).'" value="'.esc_attr( $val ).'"' ).
					( empty( $class ) ? '' : ' class="'.esc_attr( $class ).'"' ).
					( empty( $id ) ? '' : ' id="radio_'.esc_attr( $id ).'"' ).
					( $this->in_options( $name ) ? checked( $this->options[$name], $val, false ) : '' ).
					( $this->in_defaults( $name ) ? ' title="default is '.$values[$this->defaults[$name]].'"' : '' ).
					'/> '.$desc.'&nbsp;&nbsp;';
			}

			return $html;
		}

		public function get_no_radio( $name, $values = array(), $class = '', $id = '', $is_assoc = null ) {
			return $this->get_radio( $name, $values, $class, $id, $is_assoc, true );
		}

		public function get_select( $name, $values = array(), $class = '', $id = '', 
			$is_assoc = null, $disabled = false, $selected = false, $on_change = false ) {

			if ( empty( $name ) || 
				! is_array( $values ) ) 
					return;

			if ( $is_assoc === null ) 
				$is_assoc = SucomUtil::is_assoc( $values );

			$html = '';
			$select_id = empty( $id ) ?
				'select_'.$name :
				'select_'.$id;

			if ( is_string( $on_change ) ) {
				switch ( $on_change ) {
					case 'redirect':
						$redirect_url = add_query_arg( array( $name => '%%'.$name.'%%' ), 
							SucomUtil::get_prot().'://'.$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'] );
						$html .= '<script type="text/javascript">'.
							'jQuery( function(){ jQuery("#'.esc_js( $select_id ).'").change( function(){ '.
								'sucomSelectChangeRedirect("'.esc_js( $name ).'", '.
									'this.value, "'.esc_url( $redirect_url ).'"); }); });</script>'."\n";
						break;

					case 'unhide_rows':
						$html .= '<script type="text/javascript">'.
							'jQuery( function(){ jQuery("#'.esc_js( $select_id ).'").change( function(){ '.
								'sucomSelectChangeUnhideRows("'.esc_js( $name ).'", '.
									'this.value); }); });</script>'."\n";

						// if we have an option selected, unhide those rows
						if ( $selected !== false ) {
							if ( $selected === true ) {
								if ( $this->in_options( $name ) )
									$unhide = $this->options[$name];
								elseif ( $this->in_defaults( $name ) )
									$unhide = $this->defaults[$name];
								else $unhide = false;
							} else $unhide = $selected;
							if ( $unhide !== true ) {	// just in case
								$html .= '<script type="text/javascript">'.
									'jQuery(document).ready( function(){ '.
										'sucomSelectChangeUnhideRows("'.esc_js( $name ).'", '.
											'"'.esc_js( $unhide ).'"); });</script>'."\n";
							}
						}
						break;
				}
			}

			$html .= '<select '.
				( $disabled ? ' disabled="disabled"' : ' name="'.esc_attr( $this->options_name.'['.$name.']' ).'"' ).
				( empty( $class ) ? '' : ' class="'.esc_attr( $class ).'"' ).' id="'.esc_attr( $select_id ).'"'.'>'."\n";

			$option_count = 0;
			foreach ( $values as $val => $desc ) {
				// if the array is NOT associative (so regular numered array), 
				// then the description is used as the saved value as well
				if ( $is_assoc === false ) 
					$val = $desc;
				if ( $val === -1 || $val === '-1' ) 
					$desc = _x( '(settings value)', 'option value', $this->text_domain );
				else {
					if ( $this->text_domain )
						$desc = _x( $desc, 'option value', $this->text_domain );

					switch ( $name ) {
						case 'og_img_max': 
							if ( $desc === 0 ) 
								$desc .= ' '._x( '(no images)', 'option value', $this->text_domain );
							break;
						case 'og_vid_max': 
							if ( $desc === 0 ) 
								$desc .= ' '._x( '(no videos)', 'option value', $this->text_domain );
							break;
						default: 
							if ( $desc === '' || $desc === 'none' ) 
								$desc = _x( '[None]', 'option value', $this->text_domain ); 
							break;
					}
					if ( $this->in_defaults( $name ) && 
						$val === $this->defaults[$name] )
							$desc .= ' '._x( '(default)', 'option value', $this->text_domain );
				}

				if ( ! is_bool( $selected ) )
					$is_selected_html = selected( $selected, $val, false );
				elseif ( $this->in_options( $name ) )
					$is_selected_html = selected( $this->options[$name], $val, false );
				elseif ( $this->in_defaults( $name ) )
					$is_selected_html = selected( $this->defaults[$name], $val, false );
				else $is_selected_html = '';

				// for disabled selects, only include the first and/or selected option
				if ( ! $disabled || $option_count === 0 || $is_selected_html )
					$html .= '<option value="'.esc_attr( $val ).'"'.$is_selected_html.'>'.$desc.'</option>'."\n";

				$option_count++;
			}

			$html .= '</select>'."\n";

			return $html;
		}

		public function get_no_select( $name, $values = array(), $class = '', $id = '', $is_assoc = null, $selected = false, $on_change = false ) {
			return $this->get_select( $name, $values, $class, $id, $is_assoc, true, $selected, $on_change );
		}

		public function get_no_select_country( $name, $class = '', $id = '', $selected = false ) {
			return $this->get_select_country( $name, $class, $id, true, $selected );
		}

		public function get_select_country( $name, $class = '', $id = '', $disabled = false, $selected = false ) {
			if ( empty( $name ) || 
				! isset( $this->defaults[$name] ) )
					$this->defaults[$name] = 'none';

			// sanity check for possibly older input field values
			if ( $selected === false ) {
				if ( empty( $this->options[$name] ) ||
					( $this->options[$name] !== 'none' && strlen( $this->options[$name] ) !== 2 ) )
						$selected = $this->defaults[$name];
			}

			return $this->get_select( $name, array_merge( array( 'none' => '[None]' ),
				SucomUtil::get_alpha2_countries() ), $class, $id, null, $disabled, $selected );
		}

		public function get_select_img_size( $name, $name_preg = '//', $invert = false ) {
			if ( empty( $name ) ) 
				return;	// just in case
			$invert = $invert == false ? 
				null : PREG_GREP_INVERT;
			$size_names = preg_grep( $name_preg, get_intermediate_image_sizes(), $invert );
			natsort( $size_names );
			$html = '<select name="'.
				esc_attr( $this->options_name.'['.$name.']' ).'">';
			foreach ( $size_names as $size_name ) {
				if ( ! is_string( $size_name ) ) 
					continue;
				$size = $this->p->media->get_size_info( $size_name );
				$html .= '<option value="'.esc_attr( $size_name ).'" ';
				if ( $this->in_options( $name ) )
					$html .= selected( $this->options[$name], $size_name, false );
				$html .= '>'.esc_html( $size_name.' [ '.$size['width'].'x'.$size['height'].
					( $size['crop'] ? ' cropped' : '' ).' ]' );
				if ( $this->in_defaults( $name ) && $size_name == $this->defaults[$name] ) 
					$html .= ' '._x( '(default)', 'option value', $this->text_domain );
				$html .= '</option>';
			}
			$html .= '</select>';
			return $html;
		}

		public function get_input( $name, $class = '', $id = '', $len = 0, $placeholder = '', $disabled = false ) {
			if ( empty( $name ) ) 
				return;	// just in case

			if ( $disabled || ( $this->in_options( $name.':is' ) && $this->options[$name.':is'] === 'disabled' ) )
				return $this->get_no_input( $name, $class, $id, $placeholder );

			$value = $this->in_options( $name ) ?
				$this->options[$name] : '';

			if ( empty( $placeholder ) && ( $pos = strpos( $name, '#' ) ) > 0 ) {
				$key_default = SucomUtil::get_key_locale( substr( $name, 0, $pos ), $this->options, 'default' );
				if ( $name !== $key_default )
					$placeholder = isset( $this->options[$key_default] ) ?
						$this->options[$key_default] : '';
			}

			$html = '';
			if ( ! empty( $len ) && ! empty( $id ) )
				$html .= $this->get_text_len_js( 'text_'.$id );
			$html .= '<input type="text" name="'.esc_attr( $this->options_name.'['.$name.']' ).'"'.
				( empty( $class ) ? '' : ' class="'.esc_attr( $class ).'"' ).
				( empty( $id ) ? ' id="text_'.esc_attr( $name ).'"' : ' id="text_'.esc_attr( $id ).'"' ).
				( empty( $len ) ? '' : ' maxLength="'.esc_attr( $len ).'"' ).
				( $this->get_placeholder_events( 'input', $placeholder ) ).' value="'.esc_attr( $value ).'" />'.
				( empty( $len ) ? '' : ' <div id="text_'.esc_attr( $id ).'-lenMsg"></div>' );

			return $html;
		}

		public function get_input_date( $name = '', $class = '', $id = '', $min = '', $max = '', $disabled = false ) {

			if ( empty( $name ) ) {
				$value = '';
				$disabled = true;
			} else {
				$value = $this->in_options( $name ) ?
					$this->options[$name] : '';
				if ( $this->in_options( $name.':is' ) && $this->options[$name.':is'] === 'disabled' )
					$disabled = true;
			}

			return '<input type="text" '.
				( $disabled ? ' disabled="disabled"' : ' name="'.esc_attr( $this->options_name.'['.$name.']' ).'"' ).
				( empty( $class ) ? ' class="datepicker"' : ' class="datepicker '.esc_attr( $class ).'"' ).
				( empty( $id ) ? ' id="text_'.esc_attr( $name ).'"' : ' id="text_'.esc_attr( $id ).'"' ).
				( empty( $min ) ? '' : ' min="'.esc_attr( $min ).'"' ).
				( empty( $max ) ? '' : ' min="'.esc_attr( $max ).'"' ).
				' placeholder="yyyy-mm-dd" value="'.esc_attr( $value ).'" />';
		}

		public function get_no_input_date( $name = '' ) {
			return $this->get_input_date( $name, '', '', '', '', true );
		}

		public function get_no_input_date_options( $name, &$opts ) {
			$value = isset( $opts[$name] ) ? $opts[$name] : '';
			return $this->get_no_input_value( $value, 'datepicker', '', 'yyyy-mm-dd' );
		}

		public function get_no_input( $name = '', $class = '', $id = '', $placeholder = '' ) {
			if ( empty( $name ) ) 
				return $this->get_no_input_value( '', $class, $id, $placeholder );
			else return $this->get_hidden( $name ).
					$this->get_no_input_value( ( $this->in_options( $name ) ?
						$this->options[$name] : '' ), $class, $id, $placeholder );
		}

		public function get_no_input_options( $name, &$opts, $class = '', $id = '', $placeholder = '' ) {
			$value = isset( $opts[$name] ) ? $opts[$name] : '';
			return $this->get_no_input_value( $value, $class, $id, $placeholder );
		}

		public function get_no_input_value( $value = '', $class = '', $id = '', $placeholder = '' ) {
			return '<input type="text" disabled="disabled"'.
				( empty( $class ) ? '' : ' class="'.esc_attr( $class ).'"' ).
				( empty( $id ) ? '' : ' id="text_'.esc_attr( $id ).'"' ).
				( empty( $placeholder ) ? '' : ' placeholder="'.esc_attr( $placeholder ).'"' ).
				' value="'.esc_attr( $value ).'" />';
		}

		public function get_image_upload_input( $opt_prefix, $placeholder = '', $disabled = false ) {
			$select_lib = 'wp';
			$media_libs = array( 'wp' => 'Media Library' );

			if ( $this->p->is_avail['media']['ngg'] === true ) 
				$media_libs['ngg'] = 'NextGEN Gallery';

			if ( strpos( $placeholder, 'ngg-' ) === 0 ) {
				$select_lib = 'ngg';
				$placeholder = preg_replace( '/^ngg-/', '', $placeholder );
			}

			return '<div class="img_upload">'.$this->get_input( $opt_prefix.'_id', 'short', '', 0, $placeholder, $disabled ).'&nbsp;in&nbsp;'.
				$this->get_select( $opt_prefix.'_id_pre', $media_libs, '', '', true, $disabled, $select_lib ).'&nbsp;'.
				( function_exists( 'wp_enqueue_media' ) ? $this->get_button( 'Select or Upload Image', 
					'sucom_image_upload_button button', $opt_prefix, '', false, $disabled ) : '' ).'</div>';

		}

		public function get_no_image_upload_input( $opt_prefix, $placeholder = '' ) {
			return $this->get_image_upload_input( $opt_prefix, $placeholder, true );
		}

		// $use_opts = true when used for post / user meta forms (to show default values)
		public function get_image_dimensions_input( $name, $use_opts = false, $narrow = false, $disabled = false ) {
			$def_width = '';
			$def_height = '';
			$crop_area_select = '';

			if ( $use_opts === true ) {
				$def_width = empty( $this->p->options[$name.'_width'] ) ?
					'' : $this->p->options[$name.'_width'];
				$def_height = empty( $this->p->options[$name.'_height'] ) ?
					'' : $this->p->options[$name.'_height'];
				foreach ( array( 'crop', 'crop_x', 'crop_y' ) as $key )
					if ( ! $this->in_options( $name.'_'.$key ) && 
						$this->in_defaults( $name.'_'.$key ) )
							$this->options[$name.'_'.$key] = $this->defaults[$name.'_'.$key];
			}

			global $wp_version;
			if ( ! version_compare( $wp_version, 3.9, '<' ) ) {
				$crop_area_select .= $narrow === true ? 
					' <div class="img_crop_from is_narrow">' :
					' <div class="img_crop_from">from';
				foreach ( array( 'crop_x', 'crop_y' ) as $key ) {
					$pos_vals = $this->options[$name.'_'.$key] === -1 ? 
						array_merge( array( -1 => _x( '(settings value)', 'option value', $this->text_domain ) ),
							$this->p->cf['form']['position_'.$key] ) : 
						$this->p->cf['form']['position_'.$key];
					$crop_area_select .= ' '.$this->get_select( $name.'_'.$key,
						$pos_vals, 'medium', '', true, $disabled );
				}
				$crop_area_select .= '</div>';
			}

			return $this->get_input( $name.'_width', 'short', '', 0, $def_width, $disabled ).'x'.
				$this->get_input( $name.'_height', 'short', '', 0, $def_height, $disabled ).
				'px &nbsp; cropped '.$this->get_checkbox( $name.'_crop', '', '', $disabled ).$crop_area_select;
		}

		public function get_no_image_dimensions_input( $name, $use_opts = false, $narrow = false ) {
			return $this->get_image_dimensions_input( $name, $use_opts, $narrow, true );
		}

		public function get_image_dimensions_text( $name, $use_opts = false ) {
			if ( ! empty( $this->options[$name.'_width'] ) && 
				! empty( $this->options[$name.'_height'] ) ) {
				return $this->options[$name.'_width'].' x '.
					$this->options[$name.'_height'].
					( $this->options[$name.'_crop'] ? ', cropped' : '' );
			} elseif ( $use_opts === true ) {
				if ( ! empty( $this->p->options[$name.'_width'] ) &&
					! empty( $this->p->options[$name.'_height'] ) ) {
					return $this->p->options[$name.'_width'].' x '.
						$this->p->options[$name.'_height'].
						( $this->p->options[$name.'_crop'] ? ', cropped' : '' );
				}
			}
			return;
		}

		public function get_image_url_input( $opt_prefix, $url = '' ) {
			// disable if we have a custom image id
			$disabled = empty( $this->options[$opt_prefix.'_id'] ) ? false : true;
			return $this->get_input( $opt_prefix.'_url', 'wide', '', 0,
				SucomUtil::esc_url_encode( $url ), $disabled );
		}

		public function get_video_url_input( $opt_prefix, $url = '' ) {
			// disable if we have a custom video embed
			$disabled = empty( $this->options[$opt_prefix.'_embed'] ) ? false : true;
			return $this->get_input( $opt_prefix.'_url', 'wide', '', 0, 
				SucomUtil::esc_url_encode( $url ), $disabled );
		}

		public function get_copy_input( $value, $class = 'wide', $id = '' ) {
			if ( empty( $id ) )
				$id = uniqid();
			$input = '<input type="text"'.
				( empty( $class ) ? '' : ' class="'.esc_attr( $class ).'"' ).
				( empty( $id ) ? '' : ' id="text_'.esc_attr( $id ).'"' ).
				' value="'.esc_attr( $value ).'" readonly'.
				' onFocus="this.select(); document.execCommand( \'Copy\', false, null );"'.
				' onMouseUp="return false;">';
			if ( ! empty( $id ) ) {
				global $wp_version;
				// version 3.8 is required to have the dashicons
				if ( version_compare( $wp_version, 3.8, '>=' ) )
					$html = '<div class="clipboard"><div class="copy_button">'.
						'<a class="outline" href="" title="Copy to clipboard"'.
						' onClick="return sucomCopyInputId( \'text_'.esc_js( $id ).'\');">'.
						'<span class="dashicons dashicons-clipboard"></span></a>'.
						'</div><div class="copy_text">'.$input.'</div></div>';
			} else $html = $input;
			return $html;
		}

		public function get_textarea( $name, $class = '', $id = '', $len = 0, $placeholder = '', $disabled = false ) {
			if ( empty( $name ) ) 
				return;	// just in case
			if ( $this->in_options( $name.':is' ) && 
				$this->options[$name.':is'] === 'disabled' )
					$disabled = true;
			$html = '';
			$value = $this->in_options( $name ) ? $this->options[$name] : '';
			if ( ! empty( $len ) && ! empty( $id ) )
				$html .= $this->get_text_len_js( 'textarea_'.$id );
			$html .= '<textarea '.
				( $disabled ? ' disabled="disabled"' : ' name="'.esc_attr( $this->options_name.'['.$name.']' ).'"' ).
				( empty( $class ) ? '' : ' class="'.esc_attr( $class ).'"' ).
				( empty( $id ) ? ' id="textarea_'.esc_attr( $name ).'"' : ' id="textarea_'.esc_attr( $id ).'"' ).
				( empty( $len ) || $disabled ? '' : ' maxLength="'.esc_attr( $len ).'"' ).
				( empty( $len ) ? '' : ' rows="'.( round( $len / 100 ) + 1 ).'"' ).
				( $this->get_placeholder_events( 'textarea', $placeholder ) ).'>'.stripslashes( esc_attr( $value ) ).'</textarea>'.
				( empty( $len ) || $disabled ? '' : ' <div id="textarea_'.esc_attr( $id ).'-lenMsg"></div>' );
			return $html;
		}

		public function get_no_textarea( $name, $class = '', $id = '', $len = 0, $placeholder = '' ) {
			return $this->get_textarea( $name, $class, $id, $len, $placeholder, true );
		}

		public function get_no_textarea_value( $value = '', $class = '', $id = '', $len = 0, $placeholder = '' ) {
			return '<textarea disabled="disabled"'.
				( empty( $class ) ? '' : ' class="'.esc_attr( $class ).'"' ).
				( empty( $id ) ? '' : ' id="textarea_'.esc_attr( $id ).'"' ).
				( empty( $len ) ? '' : ' rows="'.( round( $len / 100 ) + 1 ).'"' ).
				'>'.stripslashes( esc_attr( $value ) ).'</textarea>';
		}

		public function get_button( $value, $class = '', $id = '', $url = '', $newtab = false, $disabled = false ) {
			$js = $newtab === true ? 
				'window.open(\''.esc_url( $url ).'\', \'_blank\');' :
				'location.href=\''.esc_url( $url ).'\';';
			$html = '<input type="button" '.
				( $disabled ? ' disabled="disabled"' : '' ).
				( empty( $class ) ? '' : ' class="'.esc_attr( $class ).'"' ).
				( empty( $id ) ? '' : ' id="button_'.esc_attr( $id ).'"' ).
				( empty( $url ) || $disabled ? '' : ' onClick="'.$js.'"' ).
				' value="'.esc_attr( $value ).'" />';
			return $html;
		}

		public function get_options( $idx = false, $def_val = null ) {
			if ( $idx !== false ) {
				if ( isset( $this->options[$idx] ) )
					return $this->options[$idx];
				else return $def_val;
			} else return $this->options;
		}

		public function in_options( $idx, $is_preg = false ) {
			if ( $is_preg ) {
				if ( ! is_array( $this->options ) ) return false;
				$opts = SucomUtil::preg_grep_keys( $idx, $this->options );
				return ( ! empty( $opts ) ) ? true : false;
			} else return isset( $this->options[$idx] ) ? true : false;
		}

		public function in_defaults( $idx ) {
			return isset( $this->defaults[$idx] ) ? true : false;
		}

		private function get_text_len_js( $id ) {
			return ( empty( $id ) ? '' : '<script type="text/javascript">
				jQuery(document).ready(function(){
					jQuery(\'#'.esc_js( $id ).'\').focus(function(){ sucomTextLen(\''.esc_js( $id ).'\'); });
					jQuery(\'#'.esc_js( $id ).'\').keyup(function(){ sucomTextLen(\''.esc_js( $id ).'\'); });
				});</script>' );
		}

		private function get_placeholder_events( $type = 'input', $placeholder ) {
			if ( empty( $placeholder ) )
				return '';

			$js_if_empty = 'if ( this.value == \'\' ) this.value = \''.esc_js( $placeholder ).'\';';
			$js_if_same = 'if ( this.value == \''.esc_js( $placeholder ).'\' ) this.value = \'\';';

			$html = ' placeholder="'.esc_attr( $placeholder ).'"'.
				' onFocus="'.$js_if_empty.'"'.
				' onBlur="'.$js_if_same.'"';

			if ( $type === 'input' )
				$html .= ' onKeyPress="if ( event.keyCode === 13 ){ '.$js_if_same.' }"';
			elseif ( $type === 'textarea' )
				$html .= ' onMouseOut="'.$js_if_same.'"';

			return $html;
		}

		public function get_md_form_rows( &$table_rows, &$form_rows, &$head, &$mod,
			&$auto_draft_msg = 'Save a draft version or publish to update this value.' ) {

			foreach ( $form_rows as $key => $val ) {
				if ( empty( $val ) ) {
					$table_rows[$key] = '';	// placeholder
					continue;
				}

				if ( ! empty( $val['no_auto_draft'] ) &&
					( empty( $mod['post_status'] ) || $mod['post_status'] === 'auto-draft' ) ) {
					$is_auto_draft = true;
					$val['td_class'] = empty( $val['td_class'] ) ?
						'blank' : $val['td_class'].' blank';
				} else $is_auto_draft = false;

				if ( ! empty( $val['header'] ) )
					$table_rows[$key] = ( ! empty( $val['tr_class'] ) ? '<tr class="'.$val['tr_class'].'">' : '' ).
						'<td></td><td'.( ! empty( $val['td_class'] ) ? ' class="'.$val['td_class'].'"' : '' ).
						'><'.$val['header'].'>'.$val['label'].'</'.$val['header'].'></td>';
				else $table_rows[$key] = ( ! empty( $val['tr_class'] ) ? '<tr class="'.$val['tr_class'].'">' : '' ).
					$this->get_th_html( $val['label'], ( ! empty( $val['th_class'] ) ? $val['th_class'] : '' ), $val['tooltip'] ).
					'<td'.( ! empty( $val['td_class'] ) ? ' class="'.$val['td_class'].'"' : '' ).'>'.
					( $is_auto_draft ? '<em>'.$auto_draft_msg.'</em>' : $val['content'] ).'</td>';
			}

			return $table_rows;
		}

		public function get_th_html( $title = '', $class = '', $css_id = '', $atts = array() ) {

			if ( isset( $this->p->msgs ) ) {
				if ( empty( $css_id ) ) 
					$tooltip_index = 'tooltip-'.$title;
				else $tooltip_index = 'tooltip-'.$css_id;
				$tooltip_text = $this->p->msgs->get( $tooltip_index, $atts );	// text is esc_attr()
			} else $tooltip_text = '';

			if ( isset( $atts['is_locale'] ) )
				$title .= ' <span style="font-weight:normal;">('.SucomUtil::get_locale().')</span>';

			return '<th'.
				( empty( $atts['th_colspan'] ) ? '' : ' colspan="'.$atts['th_colspan'].'"' ).
				( empty( $atts['th_rowspan'] ) ? '' : ' rowspan="'.$atts['th_rowspan'].'"' ).
				( empty( $class ) ? '' : ' class="'.$class.'"' ).
				( empty( $css_id ) ? '' : ' id="th_'.$css_id.'"' ).'><p>'.$title.
				( empty( $tooltip_text ) ? '' : $tooltip_text ).'</p></th>';
		}
	}
}

?>
