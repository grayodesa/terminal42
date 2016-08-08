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

if ( ! class_exists( 'WpssoProAdminAdvanced' ) ) {

	class WpssoProAdminAdvanced {

		public function __construct( &$plugin ) {
			$this->p =& $plugin;
			$this->p->util->add_plugin_filters( $this, array( 
				'plugin_content_rows' => 2,	// $table_rows, $form
				'plugin_social_rows' => 2,	// $table_rows, $form
				'plugin_integration_rows' => 2,	// $table_rows, $form
				'plugin_cache_rows' => 3,	// $table_rows, $form, $network
				'plugin_apikeys_rows' => 2,	// $table_rows, $form
				'cm_custom_rows' => 2,		// $table_rows, $form
				'cm_builtin_rows' => 2,		// $table_rows, $form
				'taglist_og_rows' => 3,		// $table_rows, $form, $network
				'taglist_schema_rows' => 3,	// $table_rows, $form, $network
				'taglist_twitter_rows' => 3,	// $table_rows, $form, $network
				'taglist_other_rows' => 3,	// $table_rows, $form, $network
			), 20 );
		}

		public function filter_plugin_content_rows( $table_rows, $form ) {

			$table_rows[] = $form->get_th_html( _x( 'Use Filtered (SEO) Title',
				'option label', 'wpsso' ), null, 'plugin_filter_title' ).
			'<td>'.$form->get_checkbox( 'plugin_filter_title' ).'</td>';

			$table_rows[] = $form->get_th_html( _x( 'Apply WordPress Content Filters',
				'option label', 'wpsso' ), null, 'plugin_filter_content' ).
			'<td>'.$form->get_checkbox( 'plugin_filter_content' ).'</td>';

			$table_rows[] = '<tr class="hide_in_basic">'.
			$form->get_th_html( _x( 'Apply WordPress Excerpt Filters',
				'option label', 'wpsso' ), null, 'plugin_filter_excerpt' ).
			'<td>'.$form->get_checkbox( 'plugin_filter_excerpt' ).'</td>';

			$table_rows[] = '<tr class="hide_in_basic">'.
			$form->get_th_html( _x( 'Content Starts at 1st Paragraph',
				'option label', 'wpsso' ), null, 'plugin_p_strip' ).
			'<td>'.$form->get_checkbox( 'plugin_p_strip' ).'</td>';

			$table_rows[] = '<tr class="hide_in_basic">'.
			$form->get_th_html( _x( 'Use Image Alt if No Content',
				'option label', 'wpsso' ), null, 'plugin_use_img_alt' ).
			'<td>'.$form->get_checkbox( 'plugin_use_img_alt' ).'</td>';

			$table_rows['plugin_img_alt_prefix'] = $form->get_th_html( _x( 'Image Alt Text Prefix',
				'option label', 'wpsso' ), null, 'plugin_img_alt_prefix', array( 'is_locale' => true ) ).
			'<td>'.$form->get_input( SucomUtil::get_key_locale( 'plugin_img_alt_prefix', $this->p->options ), 'medium' ).'</td>';

			$table_rows['plugin_p_cap_prefix'] = $form->get_th_html( _x( 'WP Caption Prefix',
				'option label', 'wpsso' ), null, 'plugin_p_cap_prefix', array( 'is_locale' => true ) ).
			'<td>'.$form->get_input( SucomUtil::get_key_locale( 'plugin_p_cap_prefix', $this->p->options ), 'medium' ).'</td>';

			$table_rows[] = '<tr class="hide_in_basic">'.
			$form->get_th_html( _x( 'Maximum Images from Content',
				'option label', 'wpsso' ), null, 'plugin_content_img_max' ).
			'<td colspan="2">'.$form->get_input( 'plugin_content_img_max', 'short' ).'</td>';

			$table_rows[] = '<tr class="hide_in_basic">'.
			$form->get_th_html( _x( 'Maximum Videos from Content',
				'option label', 'wpsso' ), null, 'plugin_content_vid_max' ).
			'<td colspan="2">'.$form->get_input( 'plugin_content_vid_max', 'short' ).'</td>';

			$table_rows[] = '<tr class="hide_in_basic">'.
			$form->get_th_html( _x( 'Check for Embedded Media from',
				'option label', 'wpsso' ), null, 'plugin_embedded_media' ).
			'<td>'.
			'<p>'.$form->get_checkbox( 'plugin_slideshare_api' ).' Slideshare Presentations</p>'.
			'<p>'.$form->get_checkbox( 'plugin_vimeo_api' ).' Vimeo Videos</p>'.
			'<p>'.$form->get_checkbox( 'plugin_wistia_api' ).' Wistia Videos</p>'.
			'<p>'.$form->get_checkbox( 'plugin_youtube_api' ).' YouTube Videos and Playlists</p>'.
			'</td>';

			return $table_rows;
		}

		public function filter_plugin_social_rows( $table_rows, $form, $network = false ) {

			foreach ( array( 
				'og_img' => sprintf( _x( 'Add "%s" Column for', 'option label', 'wpsso' ), 
					sprintf( _x( '%s Img', 'column title', 'wpsso' ), $this->p->cf['menu'] ) ),
				'og_desc' => sprintf( _x( 'Add "%s" Column for', 'option label', 'wpsso' ), 
					sprintf( _x( '%s Desc', 'column title', 'wpsso' ), $this->p->cf['menu'] ) ),
			) as $key => $label ) {

				if ( $network ) {
					$table_rows[] = $form->get_th_html( $label, null, 'plugin_'.$key.'_col', array( 'th_rowspan' => 3 ) ).
					'<td>'.$form->get_checkbox( 'plugin_'.$key.'_col_post' ).' '.__( 'Posts, Pages, and Custom Post Types', 'wpsso' ).'</td>'.
					$this->p->admin->get_site_use( $form, $network, 'plugin_'.$key.'_col_post' );
	
					$table_rows[] = '<tr class="hide_in_basic">'.
					'<td>'.$form->get_checkbox( 'plugin_'.$key.'_col_term' ).' '.__( 'Terms (Categories and Tags)', 'wpsso' ).'</td>'.
					$this->p->admin->get_site_use( $form, $network, 'plugin_'.$key.'_col_term' );
	
					$table_rows[] = '<tr class="hide_in_basic">'.
					'<td>'.$form->get_checkbox( 'plugin_'.$key.'_col_user' ).' '.__( 'Users' ).'</td>'.
					$this->p->admin->get_site_use( $form, $network, 'plugin_'.$key.'_col_user' );
				} else {
					$table_rows[] = $form->get_th_html( $label, null, 'plugin_'.$key.'_col' ).
					'<td>'.
					'<p>'.$form->get_checkbox( 'plugin_'.$key.'_col_post' ).' '.__( 'Posts, Pages, and Custom Post Types', 'wpsso' ).'</p>'.
					'<p>'.$form->get_checkbox( 'plugin_'.$key.'_col_term' ).' '.__( 'Terms (Categories and Tags)', 'wpsso' ).'</p>'.
					'<p>'.$form->get_checkbox( 'plugin_'.$key.'_col_user' ).' '.__( 'Users' ).'</p>'.
					'</td>';
				}
			}
	
			$add_to_checkboxes = '';
			foreach ( $this->p->util->get_post_types() as $post_type )
				$add_to_checkboxes .= '<p>'.$form->get_checkbox( 'plugin_add_to_'.$post_type->name ).' '.
					$post_type->label.'</p>';

			$add_to_checkboxes .= '<p>'.$form->get_checkbox( 'plugin_add_to_term' ).
				' '.__( 'Terms (Categories and Tags)', 'wpsso' ).'</p>';

			$add_to_checkboxes .= '<p>'.$form->get_checkbox( 'plugin_add_to_user' ).
				' '.__( 'User Profile', 'wpsso' ).'</p>';

			$table_rows[] = '<tr class="hide_in_basic">'.
			$form->get_th_html( _x( 'Include Social Metaboxes on',
				'option label', 'wpsso' ), null, 'plugin_add_to' ).
			'<td>'.$add_to_checkboxes.'</td>';

			$table_rows['plugin_cf_img_url'] = '<tr class="hide_in_basic">'.
			$form->get_th_html( _x( 'Image URL Custom Field',
				'option label', 'wpsso' ), null, 'plugin_cf_img_url' ).
			'<td>'.$form->get_input( 'plugin_cf_img_url' ).'</td>';

			$table_rows['plugin_cf_vid_url'] = '<tr class="hide_in_basic">'.
			$form->get_th_html( _x( 'Video URL Custom Field',
				'option label', 'wpsso' ), null, 'plugin_cf_vid_url' ).
			'<td>'.$form->get_input( 'plugin_cf_vid_url' ).'</td>';

			$table_rows['plugin_cf_vid_embed'] = '<tr class="hide_in_basic">'.
			$form->get_th_html( _x( 'Video Embed HTML Custom Field',
				'option label', 'wpsso' ), null, 'plugin_cf_vid_embed' ).
			'<td>'.$form->get_input( 'plugin_cf_vid_embed' ).'</td>';

			$table_rows['plugin_cf_recipe_ingredients'] = '<tr class="hide_in_basic">'.
			$form->get_th_html( _x( 'Recipe Ingredients Custom Field',
				'option label', 'wpsso' ), null, 'plugin_cf_recipe_ingredients' ).
			'<td>'.$form->get_input( 'plugin_cf_recipe_ingredients' ).'</td>';

			return $table_rows;
		}

		public function filter_plugin_integration_rows( $table_rows, $form ) {

			$table_rows['plugin_html_attr_filter'] = '<tr class="hide_in_basic">'.
			$form->get_th_html( _x( '&lt;html&gt; Attributes Filter Hook',
				'option label', 'wpsso' ), null, 'plugin_html_attr_filter' ).
			'<td>Name:&nbsp;'.$form->get_input( 'plugin_html_attr_filter_name' ).'</td><td>'.
			'Priority:&nbsp;'.$form->get_input( 'plugin_html_attr_filter_prio', 'short' ).'</td>';

			$table_rows['plugin_head_attr_filter'] = '<tr class="hide_in_basic">'.
			$form->get_th_html( _x( '&lt;head&gt; Attributes Filter Hook',
				'option label', 'wpsso' ), null, 'plugin_head_attr_filter' ).
			'<td>Name:&nbsp;'.$form->get_input( 'plugin_head_attr_filter_name' ).'</td><td>'.
			'Priority:&nbsp;'.$form->get_input( 'plugin_head_attr_filter_prio', 'short' ).'</td>';

			$table_rows['plugin_check_head'] = $form->get_th_html( _x( 'Check for Duplicate Meta Tags',
				'option label', 'wpsso' ), null, 'plugin_check_head' ).
			'<td colspan="2">'.$form->get_checkbox( 'plugin_check_head' ).'</td>';

			$table_rows['plugin_filter_lang'] = '<tr class="hide_in_basic">'.
			$form->get_th_html( _x( 'Use WP Locale for Language',
				'option label', 'wpsso' ), null, 'plugin_filter_lang' ).
			'<td colspan="2">'.$form->get_checkbox( 'plugin_filter_lang' ).'</td>';

			$table_rows['plugin_auto_img_resize'] = '<tr class="hide_in_basic">'.
			$form->get_th_html( _x( 'Recreate Missing WP Media Sizes',
				'option label', 'wpsso' ), null, 'plugin_auto_img_resize' ).
			'<td colspan="2">'.$form->get_checkbox( 'plugin_auto_img_resize' ).'</td>';

			$table_rows['plugin_check_img_dims'] = $form->get_th_html( _x( 'Enforce Image Dimensions Check',
				'option label', 'wpsso' ), null, 'plugin_check_img_dims' ).
			'<td colspan="2">'.$form->get_checkbox( 'plugin_check_img_dims' ).
				' <em>'._x( 'recommended', 'option comment', 'wpsso' ).'</em></td>';

			$table_rows['plugin_upscale_images'] = $form->get_th_html( _x( 'Allow Upscaling of WP Media Images',
				'option label', 'wpsso' ), null, 'plugin_upscale_images' ).
			'<td colspan="2">'.$form->get_checkbox( 'plugin_upscale_images' ).'</td>';

			$table_rows['plugin_upscale_img_max'] = '<tr class="hide_in_basic">'.
			$form->get_th_html( _x( 'Maximum Image Upscale Percentage',
				'option label', 'wpsso' ), null, 'plugin_upscale_img_max' ).
			'<td colspan="2">'.$form->get_input( 'plugin_upscale_img_max', 'short' ).' %</td>';

			if ( ! empty( $this->p->cf['*']['lib']['shortcode'] ) ) {
				$table_rows[] = '<tr class="hide_in_basic">'.
				$form->get_th_html( _x( 'Enable Plugin Shortcode(s)',
					'option label', 'wpsso' ), null, 'plugin_shortcodes' ).
				'<td colspan="2">'.$form->get_checkbox( 'plugin_shortcodes' ).'</td>';
			}

			if ( ! empty( $this->p->cf['*']['lib']['widget'] ) ) {
				$table_rows[] = '<tr class="hide_in_basic">'.
				$form->get_th_html( _x( 'Enable Plugin Widget(s)',
					'option label', 'wpsso' ), null, 'plugin_widgets' ).
				'<td colspan="2">'.$form->get_checkbox( 'plugin_widgets' ).'</td>';
			}

			$table_rows[] = $form->get_th_html( _x( 'Enable WP Excerpt for Pages',
				'option label', 'wpsso' ), null, 'plugin_page_excerpt' ).
			'<td colspan="2">'.$form->get_checkbox( 'plugin_page_excerpt' ).'</td>';

			$table_rows[] = $form->get_th_html( _x( 'Enable WP Tags for Pages',
				'option label', 'wpsso' ), null, 'plugin_page_tags' ).
			'<td colspan="2">'.$form->get_checkbox( 'plugin_page_tags' ).'</td>';

			return $table_rows;
		}

		public function filter_plugin_cache_rows( $table_rows, $form, $network = false ) {

			$table_rows['plugin_object_cache_exp'] = $form->get_th_html( _x( 'Object Cache Expiry',
				'option label', 'wpsso' ), null, 'plugin_object_cache_exp' ).
			'<td nowrap>'.$form->get_input( 'plugin_object_cache_exp', 'medium' ).' seconds</td>'.
			$this->p->admin->get_site_use( $form, $network, 'plugin_object_cache_exp' );

			$table_rows['plugin_verify_certs'] = '<tr class="hide_in_basic">'.
			$form->get_th_html( _x( 'Verify Peer SSL Certificate',
				'option label', 'wpsso' ), null, 'plugin_verify_certs' ).
			'<td>'.$form->get_checkbox( 'plugin_verify_certs' ).'</td>'.
			$this->p->admin->get_site_use( $form, $network, 'plugin_verify_certs' );

			$table_rows[] = '<tr class="hide_in_basic">'.
			$form->get_th_html( _x( 'Report Cache Purge Count',
				'option label', 'wpsso' ), null, 'plugin_cache_info' ).
			'<td>'.$form->get_checkbox( 'plugin_cache_info' ).'</td>'.
			$this->p->admin->get_site_use( $form, $network, 'plugin_cache_info' );

			return $table_rows;
		}

		public function filter_plugin_apikeys_rows( $table_rows, $form ) {

			$table_rows['plugin_shortener'] = $form->get_th_html( _x( 'Preferred URL Shortening Service',
				'option label', 'wpsso' ), null, 'plugin_shortener' ).
			'<td>'.$form->get_select( 'plugin_shortener', $this->p->cf['form']['shorteners'] ).'</td>';

			$table_rows['plugin_shortlink'] = $form->get_th_html( _x( '<em>Get Shortlink</em> Gives Shortened URL',
				'option label', 'wpsso' ), null, 'plugin_shortlink' ).
			'<td>'.$form->get_checkbox( 'plugin_shortlink' ).'</td>';

			$table_rows['plugin_min_shorten'] = '<tr class="hide_in_basic">'.
			$form->get_th_html( _x( 'Minimum URL Length to Shorten',
				'option label', 'wpsso' ), null, 'plugin_min_shorten' ). 
			'<td>'.$form->get_input( 'plugin_min_shorten', 'short' ).' '.
				_x( 'characters', 'option comment', 'wpsso' ).'</td>';

			$table_rows['subsection_plugin_bitly'] = '<tr class="hide_in_basic">'.
				'<td></td><td class="subsection"><h4>'.
				_x( 'Bitly URL Shortener', 'metabox title', 'wpsso' ).'</h4></td>';

			$table_rows['plugin_bitly_login'] = $form->get_th_html( _x( 'Bitly Username',
				'option label', 'wpsso' ), null, 'plugin_bitly_login' ).
			'<td>'.$form->get_input( 'plugin_bitly_login', 'mono' ).'</td>';

			$table_rows['plugin_bitly_token'] = $form->get_th_html( '<a href="https://bitly.com/a/oauth_apps" target="_blank">'.
				_x( 'Bitly Generic Access Token', 'option label', 'wpsso' ).'</a>', null, 'plugin_bitly_token' ).
			'<td>'.$form->get_input( 'plugin_bitly_token', 'api_key mono' ).'</td>';

			$table_rows['plugin_bitly_api_key'] = '<tr class="hide_in_basic">'.
			$form->get_th_html( '<a href="http://bitly.com/a/your_api_key" target="_blank">'.
				_x( 'or Bitly API Key (deprecated)', 'option label', 'wpsso' ).'</a>', null, 'plugin_bitly_api_key' ).
			'<td>'.$form->get_input( 'plugin_bitly_api_key', 'api_key mono' ).' <em>'.
				_x( 'api key authentication is deprecated', 'option comment', 'wpsso' ).'</em></td>';

			$table_rows['subsection_plugin_google'] = '<tr class="hide_in_basic">'.
				'<td></td><td class="subsection"><h4>'.
				_x( 'Google APIs', 'metabox title', 'wpsso' ).'</h4></td>';

			$table_rows['plugin_google_api_key'] = '<tr class="hide_in_basic">'.
			$form->get_th_html( _x( 'Google Project App BrowserKey',
				'option label', 'wpsso' ), null, 'plugin_google_api_key' ).
			'<td>'.$form->get_input( 'plugin_google_api_key', 'api_key mono' ).'</td>';

			$table_rows['plugin_google_shorten'] = '<tr class="hide_in_basic">'.
			$form->get_th_html( _x( 'Google URL Shortener API is ON',
				'option label', 'wpsso' ), null, 'plugin_google_shorten' ).
			'<td>'.$form->get_radio( 'plugin_google_shorten',
				$this->p->cf['form']['yes_no'], null, null, true ).'</td>';

			$table_rows['subsection_plugin_owly'] = '<tr class="hide_in_basic">'.
				'<td></td><td class="subsection"><h4>'.
				_x( 'Ow.ly URL Shortener', 'metabox title', 'wpsso' ).'</h4></td>';

			$table_rows['plugin_owly_api_key'] = '<tr class="hide_in_basic">'.
			$form->get_th_html( _x( 'Ow.ly API Key',
				'option label', 'wpsso' ), null, 'plugin_owly_api_key' ).
			'<td>'.$form->get_input( 'plugin_owly_api_key', 'api_key mono' ).'</td>';

			$table_rows['subsection_plugin_yourls'] = '<tr class="hide_in_basic">'.
				'<td></td><td class="subsection"><h4>'.
				_x( 'Your Own URL Shortener (YOURLS)', 'metabox title', 'wpsso' ).'</h4></td>';

			$table_rows['plugin_yourls_api_url'] = '<tr class="hide_in_basic">'.
			$form->get_th_html( _x( 'YOURLS API URL',
				'option label', 'wpsso' ), null, 'plugin_yourls_api_url' ).
			'<td>'.$form->get_input( 'plugin_yourls_api_url', 'api_key mono wide' ).'</td>';

			$table_rows['plugin_yourls_username'] = '<tr class="hide_in_basic">'.
			$form->get_th_html( _x( 'YOURLS Username',
				'option label', 'wpsso' ), null, 'plugin_yourls_username' ).
			'<td>'.$form->get_input( 'plugin_yourls_username', 'api_key mono' ).'</td>';

			$table_rows['plugin_yourls_password'] = '<tr class="hide_in_basic">'.
			$form->get_th_html( _x( 'YOURLS Password',
				'option label', 'wpsso' ), null, 'plugin_yourls_password' ).
			'<td>'.$form->get_input( 'plugin_yourls_password', 'api_key mono' ).'</td>';

			$table_rows['plugin_yourls_token'] = '<tr class="hide_in_basic">'.
			$form->get_th_html( _x( 'YOURLS Token',
				'option label', 'wpsso' ), null, 'plugin_yourls_token' ).
			'<td>'.$form->get_input( 'plugin_yourls_token', 'api_key mono' ).'</td>';

			return $table_rows;
		}

		public function filter_cm_custom_rows( $table_rows, $form ) {

			$table_rows[] = '<td></td>'.
			$form->get_th_html( _x( 'Show',
				'column title', 'wpsso' ), 'left checkbox' ).
			$form->get_th_html( _x( 'Contact Field Name',
				'column title', 'wpsso' ), 'left medium', 'custom-cm-field-name' ).
			$form->get_th_html( _x( 'Profile Contact Label',
				'column title', 'wpsso' ), 'left wide' );

			$sorted_opt_pre = $this->p->cf['opt']['pre'];
			ksort( $sorted_opt_pre );

			foreach ( $sorted_opt_pre as $id => $pre ) {

				$cm_enabled = 'plugin_cm_'.$pre.'_enabled';
				$cm_name = 'plugin_cm_'.$pre.'_name';
				$cm_label = 'plugin_cm_'.$pre.'_label';

				// not all social websites have a contact method field
				if ( isset( $this->p->options[$cm_enabled] ) ) {

					switch ( $id ) {
						case 'facebook':
						case 'gplus':
						case 'twitter':
							$tr = '';
							break;
						default:
							$tr = '<tr class="hide_in_basic">';
							break;
					}

					$name = empty( $this->p->cf['*']['lib']['website'][$id] ) ? 
						ucfirst( $id ) : $this->p->cf['*']['lib']['website'][$id];
					$name = $name == 'GooglePlus' ? 'Google+' : $name;

					$table_rows[] = $tr.$form->get_th_html( $name, 'medium' ).
					'<td class="checkbox">'.$form->get_checkbox( $cm_enabled ).'</td>'.
					'<td>'.$form->get_input( $cm_name, 'medium' ).'</td>'.
					'<td>'.$form->get_input( $cm_label ).'</td>';
				}
			}

			return $table_rows;
		}

		public function filter_cm_builtin_rows( $table_rows, $form ) {

			$table_rows[] = '<td></td>'.
			$form->get_th_html( _x( 'Show',
				'column title', 'wpsso' ), 'left checkbox' ).
			$form->get_th_html( _x( 'Contact Field Name',
				'column title', 'wpsso' ), 'left medium', 'custom-cm-field-name' ).
			$form->get_th_html( _x( 'Profile Contact Label',
				'column title', 'wpsso' ), 'left wide' );

			$sorted_wp_cm = $this->p->cf['wp']['cm'];
			ksort( $sorted_wp_cm );

			foreach ( $sorted_wp_cm as $id => $name ) {

				$cm_enabled = 'wp_cm_'.$id.'_enabled';
				$cm_name = 'wp_cm_'.$id.'_name';
				$cm_label = 'wp_cm_'.$id.'_label';

				if ( array_key_exists( $cm_enabled, $this->p->options ) ) {
					$table_rows[] = $form->get_th_html( $name, 'medium' ).
					'<td class="checkbox">'.$form->get_checkbox( $cm_enabled ).'</td>'.
					'<td>'.$form->get_no_input( $cm_name, 'medium' ).'</td>'.
					'<td>'.$form->get_input( $cm_label ).'</td>';
				}
			}

			return $table_rows;
		}

		public function filter_taglist_og_rows( $table_rows, $form, $network = false ) {
			return $this->get_taglist_rows( $table_rows, $form, $network,
				array( '/^add_(meta)_(property)_(.+)$/' ) );
		}

		public function filter_taglist_schema_rows( $table_rows, $form, $network = false ) {
			return $this->get_taglist_rows( $table_rows, $form, $network,
				array( '/^add_(meta)_(itemprop)_(.+)$/' ) );
		}

		public function filter_taglist_twitter_rows( $table_rows, $form, $network = false ) {
			return $this->get_taglist_rows( $table_rows, $form, $network,
				array( '/^add_(meta)_(name)_(twitter:.+)$/' ) );
		}

		public function filter_taglist_other_rows( $table_rows, $form, $network = false ) {
			return $this->get_taglist_rows( $table_rows, $form, $network,
				array( '/^add_(link)_([^_]+)_(.+)$/', '/^add_(meta)_(name)_(.+)$/' ) );
		}

		private function get_taglist_rows( &$table_rows, &$form, &$network, array $opt_preg ) {
			$table_cells = array();
			$opt_defs = $this->p->opt->get_defaults();
			foreach ( $opt_preg as $preg ) {
				foreach ( $opt_defs as $opt => $val ) {
					if ( strpos( $opt, 'add_' ) !== 0 ||			// optimize
						isset( $this->taglist_opts[$opt] ) ||		// check cache for tags already shown
							! preg_match( $preg, $opt, $match ) )	// check option name for a match
								continue;
					$highlight = '';
					$this->taglist_opts[$opt] = $val;
					switch ( $opt ) {
						// disable with a constant instead
						case 'add_meta_name_generator':
							continue 2;
						// highlight important meta tags
						case 'add_meta_name_canonical':
						case 'add_meta_name_description':
							$highlight = ' highlight';
							break;
					}
					$table_cells[] = '<!-- '.( implode( ' ', $match ) ).' -->'.	// required for sorting
						'<td class="checkbox">'.$form->get_checkbox( $opt ).'</td>'.
						'<td class="xshort'.$highlight.'">'.$match[1].'</td>'.
						'<td class="taglist'.$highlight.'">'.$match[2].'</td>'.
						'<th class="taglist'.$highlight.'">'.$match[3].'</th>';
				}
			}
			return array_merge( $table_rows, SucomUtil::get_column_rows( $table_cells, 2 ) );
		}
	}
}

?>
