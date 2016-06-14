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

if ( ! class_exists( 'WpssoProAdminMeta' ) ) {

	class WpssoProAdminMeta {

		public function __construct( &$plugin ) {
			$this->p =& $plugin;
			$this->p->util->add_plugin_filters( $this, array( 
				'meta_header_rows' => array(
					'user_header_rows' => 4,	// $table_rows, $form, $head, $mod
					'term_header_rows' => 4,	// $table_rows, $form, $head, $mod
				),
				'meta_media_rows' => array(
					'post_media_rows' => 4,		// $table_rows, $form, $head, $mod
					'user_media_rows' => 4,		// $table_rows, $form, $head, $mod
					'term_media_rows' => 4,	// $table_rows, $form, $head, $mod
				),
			) );
		}

		public function filter_meta_header_rows( $table_rows, $form, $head, $mod ) {
			if ( $this->p->debug->enabled )
				$this->p->debug->mark();

			$seo_desc_disabled = $this->p->options['add_meta_name_description'] ? false : true;
			$seo_desc_msg = __( 'The SEO description field is disabled - the "meta name description" option is disabled and/or a known SEO plugin has been detected.', 'wpsso' );

			$form_rows = array(
				'og_title' => array(
					'label' => _x( 'Default Title', 'option label', 'wpsso' ),
					'th_class' => 'medium', 'tooltip' => 'meta-og_title',
					'content' => $form->get_input( 'og_title', 'wide', $this->p->cf['lca'].'_og_title',
						$this->p->options['og_title_len'], $this->p->webpage->get_title( $this->p->options['og_title_len'],
							'...', $mod, true, false, true, 'none' ) ),	// $md_idx = 'none'
				),
				'og_desc' => array(
					'label' => _x( 'Default Description (Facebook / Open Graph, LinkedIn, Pinterest Rich Pin)', 'option label', 'wpsso' ),
					'th_class' => 'medium', 'tooltip' => 'meta-og_desc',
					'content' => $form->get_textarea( 'og_desc', '', $this->p->cf['lca'].'_og_desc',
						$this->p->options['og_desc_len'], $this->p->webpage->get_description( $this->p->options['og_desc_len'],
							'...', $mod, true, true, true, 'none' ) ),	// $md_idx = 'none'
				),
				'seo_desc' => array(
					'tr_class' => ( $seo_desc_disabled ? 'hide_in_basic' : '' ),
					'label' => _x( 'Google Search / SEO Description', 'option label', 'wpsso' ),
					'th_class' => 'medium', 'tooltip' => 'meta-seo_desc',
					'content' => $form->get_textarea( 'seo_desc', '', $this->p->cf['lca'].'_seo_desc',
						$this->p->options['seo_desc_len'], $this->p->webpage->get_description( $this->p->options['seo_desc_len'],
							'...', $mod, true, false ), $seo_desc_disabled ).		// $add_hashtags = false
						( $seo_desc_disabled ? '<p class="status-msg smaller">'.$seo_desc_msg.'</p>' : '' ),
				),
				'tc_desc' => array(
					'label' => _x( 'Twitter Card Description', 'option label', 'wpsso' ),
					'th_class' => 'medium', 'tooltip' => 'meta-tc_desc',
					'content' => $form->get_textarea( 'tc_desc', null, $this->p->cf['lca'].'_tc_desc',
						$this->p->options['tc_desc_len'], $this->p->webpage->get_description( $this->p->options['tc_desc_len'],
							'...', $mod ) ),
				),
				'sharing_url' => array(
					'tr_class' => 'hide_in_basic',
					'label' => _x( 'Sharing URL', 'option label', 'wpsso' ),
					'th_class' => 'medium', 'tooltip' => 'meta-sharing_url',
					'content' => $form->get_input( 'sharing_url', 'wide', '', '', $this->p->util->get_sharing_url( $mod, false ) ),
				),
				'subsection_schema' => array(
					'td_class' => 'subsection',
					'header' => 'h4',
					'label' => _x( 'Google Structured Data / Schema Markup', 'metabox title', 'wpsso' )
				),
				'schema_desc' => array(
					'label' => _x( 'Schema Description', 'option label', 'wpsso' ),
					'th_class' => 'medium', 'tooltip' => 'meta-schema_desc',
					'content' => $form->get_textarea( 'schema_desc', '', $this->p->cf['lca'].'_schema_desc',
						$this->p->options['schema_desc_len'], $this->p->webpage->get_description( $this->p->options['schema_desc_len'],
							'...', $mod ) ),
				),
			);

			return $form->get_md_form_rows( $table_rows, $form_rows, $head, $mod );
		}

		public function filter_meta_media_rows( $table_rows, $form, $head, $mod ) {
			if ( $this->p->debug->enabled )
				$this->p->debug->mark();

			if ( $mod['is_post'] && ( empty( $mod['post_status'] ) || $mod['post_status'] === 'auto-draft' ) ) {
				$table_rows[] = '<td><blockquote class="status-info"><p class="centered">'.
					sprintf( __( 'Save a draft version or publish the %s to display these options.',
						'wpsso' ), ucfirst( $mod['post_type'] ) ).'</p></td>';
				return $table_rows;	// abort
			}

			$media_info = $this->p->og->get_the_media_info( $this->p->cf['lca'].'-opengraph', 
				array( 'pid', 'img_url', 'vid_url', 'vid_title', 'vid_desc' ),
					$mod, 'none', 'og', $head );	// $md_pre = 'none'

			$form_rows['subsection_opengraph'] = array(
				'td_class' => 'subsection top',
				'header' => 'h4',
				'label' => _x( 'All Social Websites / Open Graph', 'metabox title', 'wpsso' )
			);
			$form_rows['subsection_priority_image'] = array(
				'header' => 'h5',
				'label' => _x( 'Priority Image Information', 'metabox title', 'wpsso' )
			);
			$form_rows['og_img_dimensions'] = array(
				'tr_class' => 'hide_in_basic',
				'label' => _x( 'Image Dimensions', 'option label', 'wpsso' ),
				'th_class' => 'medium', 'tooltip' => 'og_img_dimensions',
				'content' => $form->get_image_dimensions_input( 'og_img', true, false ),
			);
			$form_rows['og_img_id'] = array(
				'label' => _x( 'Image ID', 'option label', 'wpsso' ),
				'th_class' => 'medium', 'tooltip' => 'meta-og_img_id',
				'content' => $form->get_image_upload_input( 'og_img', $media_info['pid'] ),
			);
			$form_rows['og_img_url'] = array(
				'label' => _x( 'or an Image URL', 'option label', 'wpsso' ),
				'th_class' => 'medium', 'tooltip' => 'meta-og_img_url',
					'content' => $form->get_image_url_input( 'og_img', $media_info['img_url'] ),
			);
			if ( $mod['is_post'] ) {
				$form_rows['og_img_max'] = array(
					'tr_class' => 'hide_in_basic',
					'label' => _x( 'Maximum Images', 'option label', 'wpsso' ),
					'th_class' => 'medium', 'tooltip' => 'meta-og_img_max',
					'content' => $form->get_select( 'og_img_max', range( -1, 
						$this->p->cf['form']['max_media_items'] ), 'medium' ),
				);
			}
			$form_rows['subsection_priority_video'] = array(
				'header' => 'h5',
				'label' => _x( 'Priority Video Information', 'metabox title', 'wpsso' )
			);
			$form_rows['og_vid_embed'] = array(
				'label' => _x( 'Video Embed HTML', 'option label', 'wpsso' ),
				'th_class' => 'medium', 'tooltip' => 'meta-og_vid_embed',
				'content' => $form->get_textarea( 'og_vid_embed' ),
			);
			$form_rows['og_vid_url'] = array(
				'label' => _x( 'or a Video URL', 'option label', 'wpsso' ),
				'th_class' => 'medium', 'tooltip' => 'meta-og_vid_url',
				'content' => $form->get_video_url_input( 'og_vid', $media_info['vid_url'] ),
			);
			$form_rows['og_vid_title'] = array(
				'tr_class' => 'hide_in_basic',
				'label' => _x( 'Video Name / Title', 'option label', 'wpsso' ),
				'th_class' => 'medium', 'tooltip' => 'meta-og_vid_title',
				'content' => $form->get_input( 'og_vid_title', 'wide', $this->p->cf['lca'].'_og_vid_title',
					$this->p->options['og_title_len'], $media_info['vid_title'] ),
			);
			$form_rows['og_vid_desc'] = array(
				'tr_class' => 'hide_in_basic',
				'label' => _x( 'Video Description', 'option label', 'wpsso' ),
				'th_class' => 'medium', 'tooltip' => 'meta-og_vid_desc',
				'content' => $form->get_textarea( 'og_vid_desc', '', $this->p->cf['lca'].'_og_vid_desc', 
					$this->p->options['og_desc_len'], $media_info['vid_desc'] ),
			);
			if ( $mod['is_post'] ) {
				$form_rows['og_vid_max'] = array(
					'tr_class' => 'hide_in_basic',
					'label' => _x( 'Maximum Videos', 'option label', 'wpsso' ),
					'th_class' => 'medium', 'tooltip' => 'meta-og_vid_max',
					'content' => $form->get_select( 'og_vid_max', range( -1, 
						$this->p->cf['form']['max_media_items'] ), 'medium' ),
				);
			}
			$form_rows['og_vid_prev_img'] = array(
				'tr_class' => 'hide_in_basic',
				'label' => _x( 'Include Preview Image(s)', 'option label', 'wpsso' ),
				'th_class' => 'medium', 'tooltip' => 'meta-og_vid_prev_img',
				'content' => $form->get_checkbox( 'og_vid_prev_img' ),
			);

			if ( ! SucomUtil::get_const( 'WPSSO_RICH_PIN_DISABLE' ) ) {

				// the $head array should contain pinterest image meta tags (with a pinterest prefix)
				$media_info = $this->p->og->get_the_media_info( $this->p->cf['lca'].'-richpin', 
					array( 'pid', 'img_url' ), $mod, 'none', 'pinterest', $head );

				// show all options if a pinterest image has been defined
				$tr_class = $form->in_options( '/^rp_img_/', true ) ? '' : 'hide_in_basic';

				$form_rows['subsection_pinterest'] = array(
					'tr_class' => $tr_class,
					'td_class' => 'subsection',
					'header' => 'h4',
					'label' => _x( 'Pinterest / Rich Pin', 'metabox title', 'wpsso' )
				);
				$form_rows['rp_img_dimensions'] = array(
					'tr_class' => $tr_class,
					'label' => _x( 'Image Dimensions', 'option label', 'wpsso' ),
					'th_class' => 'medium', 'tooltip' => 'rp_img_dimensions',
					'content' => $form->get_image_dimensions_input( 'rp_img', true, false ),
				);
				$form_rows['rp_img_id'] = array(
					'tr_class' => $tr_class,
					'label' => _x( 'Image ID', 'option label', 'wpsso' ),
					'th_class' => 'medium', 'tooltip' => 'meta-rp_img_id',
					'content' => $form->get_image_upload_input( 'rp_img', $media_info['pid'] ),
				);
				$form_rows['rp_img_url'] = array(
					'tr_class' => $tr_class,
					'label' => _x( 'or an Image URL', 'option label', 'wpsso' ),
					'th_class' => 'medium', 'tooltip' => 'meta-rp_img_url',
					'content' => $form->get_image_url_input( 'rp_img', $media_info['img_url'] ),
				);
			}

			if ( ! SucomUtil::get_const( 'WPSSO_SCHEMA_DISABLE' ) ) {

				$media_info = $this->p->og->get_the_media_info( $this->p->cf['lca'].'-schema',
					array( 'pid', 'img_url' ), $mod, 'og' );

				// show all options if a schema image has been defined
				$tr_class = $form->in_options( '/^schema_img_/', true ) ? '' : 'hide_in_basic';
	
				$form_rows['subsection_schema'] = array(
					'tr_class' => $tr_class,
					'td_class' => 'subsection',
					'header' => 'h4',
					'label' => _x( 'Google Structured Data / Schema Markup', 'metabox title', 'wpsso' )
				);
				$form_rows['schema_img_dimensions'] = array(
					'tr_class' => $tr_class,
					'label' => _x( 'Image Dimensions', 'option label', 'wpsso' ),
					'th_class' => 'medium', 'tooltip' => 'schema_img_dimensions',
					'content' => $form->get_image_dimensions_input( 'schema_img', true, false ),
				);
				$form_rows['schema_img_id'] = array(
					'tr_class' => $tr_class,
					'label' => _x( 'Image ID', 'option label', 'wpsso' ),
					'th_class' => 'medium', 'tooltip' => 'meta-schema_img_id',
					'content' => $form->get_image_upload_input( 'schema_img', $media_info['pid'] ),
				);
				$form_rows['schema_img_url'] = array(
					'tr_class' => $tr_class,
					'label' => _x( 'or an Image URL', 'option label', 'wpsso' ),
					'th_class' => 'medium', 'tooltip' => 'meta-schema_img_url',
					'content' => $form->get_image_url_input( 'schema_img', $media_info['img_url'] ),
				);
				if ( $mod['is_post'] ) {
					$form_rows['schema_img_max'] = array(
						'tr_class' => 'hide_in_basic',
						'label' => _x( 'Maximum Images', 'option label', 'wpsso' ),
						'th_class' => 'medium', 'tooltip' => 'meta-schema_img_max',
						'content' => $form->get_select( 'schema_img_max', range( -1, 
							$this->p->cf['form']['max_media_items'] ), 'medium' ),
					);
				}
			}

			return $form->get_md_form_rows( $table_rows, $form_rows, $head, $mod );
		}
	}
}

?>
