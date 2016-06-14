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

if ( ! class_exists( 'WpssoProAdminGeneral' ) ) {

	class WpssoProAdminGeneral {

		public function __construct( &$plugin ) {
			$this->p =& $plugin;
			$this->p->util->add_plugin_filters( $this, array( 
				'og_author_rows' => 2,	// $table_rows, $form
				'og_videos_rows' => 2,	// $table_rows, $form
			) );
		}

		public function filter_og_author_rows( $table_rows, $form ) {

			$table_rows[] = $form->get_th_html( _x( 'Include Author Gravatar Image',
				'option label', 'wpsso' ), null, 'og_author_gravatar' ).
			'<td>'.$form->get_checkbox( 'plugin_gravatar_api' ).'</td>';

			return $table_rows;
		}

		public function filter_og_videos_rows( $table_rows, $form ) {

			$table_rows['og_vid_max'] = $form->get_th_html( _x( 'Maximum Videos to Include',
				'option label', 'wpsso' ), null, 'og_vid_max' ).
			'<td>'.$form->get_select( 'og_vid_max', 
				range( 0, $this->p->cf['form']['max_media_items'] ), 'short', null, true ).'</td>';

			$table_rows['og_vid_https'] = '<tr class="hide_in_basic">'.
			$form->get_th_html( _x( 'Use HTTPS for Video API',
				'option label', 'wpsso' ), null, 'og_vid_https' ).
			'<td>'.$form->get_checkbox( 'og_vid_https' ).' '.
				sprintf( _x( 'uses %s', 'option comment', 'wpsso' ),
					str_replace( WPSSO_PLUGINDIR, WPSSO_PLUGINSLUG.'/', WPSSO_CURL_CAINFO ) ).'</td>';

			$table_rows['og_vid_prev_img'] = $form->get_th_html( _x( 'Include Video Preview Image(s)',
				'option label', 'wpsso' ), null, 'og_vid_prev_img' ).
			'<td>'.$form->get_checkbox( 'og_vid_prev_img' ).' '.
				_x( 'video preview images are included first',
					'option comment', 'wpsso' ).'</td>';

			$table_rows['og_vid_html_type'] = $form->get_th_html( _x( 'Include Embed text/html Type',
				'option label', 'wpsso' ), null, 'og_vid_html_type' ).
			'<td>'.$form->get_checkbox( 'og_vid_html_type' ).'</td>';

			$table_rows['og_vid_autoplay'] = $form->get_th_html( _x( 'Force Autoplay when Possible',
				'option label', 'wpsso' ), null, 'og_vid_autoplay' ).
			'<td>'.$form->get_checkbox( 'og_vid_autoplay' ).'</td>';

			$table_rows['og_def_vid_url'] = '<tr class="hide_in_basic">'.
			$form->get_th_html( _x( 'Default / Fallback Video URL',
				'option label', 'wpsso' ), null, 'og_def_vid_url' ).
			'<td>'.$form->get_input( 'og_def_vid_url', 'wide' ).'</td>';

			$table_rows['og_def_vid_on_index'] = '<tr class="hide_in_basic">'.
			$form->get_th_html( _x( 'Use Default Video on Indexes',
				'option label', 'wpsso' ), null, 'og_def_vid_on_index' ).
			'<td>'.$form->get_checkbox( 'og_def_vid_on_index' ).'</td>';

			$table_rows['og_def_vid_on_search'] = '<tr class="hide_in_basic">'.
			$form->get_th_html( _x( 'Use Default Video on Search Results',
				'option label', 'wpsso' ), null, 'og_def_vid_on_search' ).
			'<td>'.$form->get_checkbox( 'og_def_vid_on_search' ).'</td>';

			return $table_rows;
		}
	}
}

?>
