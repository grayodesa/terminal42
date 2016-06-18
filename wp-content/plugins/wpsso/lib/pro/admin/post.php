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

if ( ! class_exists( 'WpssoProAdminPost' ) ) {

	class WpssoProAdminPost {

		public function __construct( &$plugin ) {
			$this->p =& $plugin;
			$this->p->util->add_plugin_filters( $this, array( 
				'post_header_rows' => 4,		// $table_rows, $form, $head, $mod
			) );
		}

		public function filter_post_header_rows( $table_rows, $form, $head, $mod ) {
			if ( $this->p->debug->enabled )
				$this->p->debug->mark();

			$is_og_article = isset( $head['og:type'] ) && 
				$head['og:type'] === 'article' ? false : true;

			$seo_desc_msg = empty( $this->p->options['add_meta_name_description'] ) ? 
				'<p class="status-msg smaller">'.
					__( 'The SEO description field is disabled (the description meta tag is disabled and/or a known SEO plugin is active).',
						'wpsso' ).'</p>' : '';

			// wpsso json is available, but not active
			if ( ! empty( $this->p->cf['plugin']['wpssojson'] ) &&
				empty( $this->p->cf['plugin']['wpssojson']['version'] ) ) {
				$info = $this->p->cf['plugin']['wpssojson'];
				$schema_desc_msg = '<p class="status-msg smaller">'.
					sprintf( __( 'Activate the %s extension for additional Schema markup features and options.', 'wpsso' ),
					'<a href="'.$info['url']['download'].'" target="_blank">'.$info['short'].'</a>' ).'</p>';
			} else $schema_desc_msg = '';

			$form_rows = array(
				'og_art_section' => array(
					'tr_class' => ( $is_og_article ? '' : 'hide_in_basic' ),	// hide if not an article
					'label' => _x( 'Article Topic', 'option label', 'wpsso' ),
					'th_class' => 'medium', 'tooltip' => 'post-og_art_section',
					'content' => $form->get_select( 'og_art_section', array_merge( array( -1 ), 
						$this->p->util->get_topics() ), '', '', false, 
							( $is_og_article ? false : true ) ),		// disable if not an article
				),
				'og_title' => array(
					'label' => _x( 'Default Title', 'option label', 'wpsso' ),
					'th_class' => 'medium', 'tooltip' => 'meta-og_title',
					'no_auto_draft' => true,
					'content' => $form->get_input( 'og_title', 'wide', $this->p->cf['lca'].'_og_title',
						$this->p->options['og_title_len'], $this->p->webpage->get_title( $this->p->options['og_title_len'],
							'...', $mod, true, false, true, 'none' ) ),	// $md_idx = 'none'
				),
				'og_desc' => array(
					'label' => _x( 'Default Description (Facebook / Open Graph, LinkedIn, Pinterest Rich Pin)', 'option label', 'wpsso' ),
					'th_class' => 'medium', 'tooltip' => 'post-og_desc',
					'no_auto_draft' => true,
					'content' => $form->get_textarea( 'og_desc', '', $this->p->cf['lca'].'_og_desc',
						$this->p->options['og_desc_len'], $this->p->webpage->get_description( $this->p->options['og_desc_len'],
							'...', $mod, true, true, true, 'none' ) ),	// $md_idx = 'none'
				),
				'seo_desc' => array(
					'tr_class' => ( $seo_desc_msg ? 'hide_in_basic' : '' ),		// hide if seo description is disabled
					'label' => _x( 'Google Search / SEO Description', 'option label', 'wpsso' ),
					'th_class' => 'medium', 'tooltip' => 'meta-seo_desc',
					'no_auto_draft' => true,
					'content' => $form->get_textarea( 'seo_desc', '', $this->p->cf['lca'].'_seo_desc',
						$this->p->options['seo_desc_len'], $this->p->webpage->get_description( $this->p->options['seo_desc_len'],
							'...', $mod, true, false ), ( $seo_desc_msg ? true : false ) ).$seo_desc_msg,
				),
				'tc_desc' => array(
					'label' => _x( 'Twitter Card Description', 'option label', 'wpsso' ),
					'th_class' => 'medium', 'tooltip' => 'meta-tc_desc',
					'no_auto_draft' => true,
					'content' => $form->get_textarea( 'tc_desc', null, $this->p->cf['lca'].'_tc_desc',
						$this->p->options['tc_desc_len'], $this->p->webpage->get_description( $this->p->options['tc_desc_len'],
							'...', $mod ) ),
				),
				'sharing_url' => array(
					'tr_class' => 'hide_in_basic',
					'label' => _x( 'Sharing URL', 'option label', 'wpsso' ),
					'no_auto_draft' => ( $mod['post_type'] === 'attachment' ? false : true ),
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
					'no_auto_draft' => true,
					'content' => $form->get_textarea( 'schema_desc', '', $this->p->cf['lca'].'_schema_desc',
						$this->p->options['schema_desc_len'], $this->p->webpage->get_description( $this->p->options['schema_desc_len'],
							'...', $mod ) ).$schema_desc_msg,
				),
			);

			$auto_draft_msg = sprintf( __( 'Save a draft version or publish the %s to update this value.',
				'wpsso' ), ucfirst( $mod['post_type'] ) );

			return $form->get_md_form_rows( $table_rows, $form_rows, $head, $mod, $auto_draft_msg );
		}
	}
}

?>
