<?php
/*
 * IMPORTANT: READ THE LICENSE AGREEMENT CAREFULLY.
 *
 * BY INSTALLING, COPYING, RUNNING, OR OTHERWISE USING THE 
 * WPSSO SCHEMA JSON-LD (WPSSO JSON) PRO APPLICATION, YOU AGREE
 * TO BE BOUND BY THE TERMS OF ITS LICENSE AGREEMENT.
 * 
 * License: Nontransferable License for a WordPress Site Address URL
 * License URI: http://surniaulula.com/wp-content/plugins/wpsso-schema-json-ld/license/pro.txt
 *
 * IF YOU DO NOT AGREE TO THE TERMS OF ITS LICENSE AGREEMENT,
 * PLEASE DO NOT INSTALL, RUN, COPY, OR OTHERWISE USE THE
 * WORDPRESS SOCIAL SHARING OPTIMIZATION (WPSSO) PRO APPLICATION.
 * 
 * Copyright 2016 Jean-Sebastien Morisset (http://surniaulula.com/)
 */

if ( ! defined( 'ABSPATH' ) ) 
	die( 'These aren\'t the droids you\'re looking for...' );

if ( ! class_exists( 'WpssoJsonProAdminPost' ) ) {

	class WpssoJsonProAdminPost {

		public function __construct( &$plugin ) {
			$this->p =& $plugin;
			$this->p->util->add_plugin_filters( $this, array( 
				'post_header_rows' => 4,	// $table_rows, $form, $head, $mod
			) );
		}

		public function filter_post_header_rows( $table_rows, $form, $head, $mod ) {

			$schema_types = $this->p->schema->get_schema_types_select();
			$title_max_len = $this->p->options['og_title_len'];
			$desc_max_len = $this->p->options['schema_desc_len'];
			$headline_max_len = WpssoJsonConfig::$cf['schema']['article']['headline']['max_len'];

			// javascript hide/show classes for schema type rows
			$tr_class = array(
				'article' => 'schema_type_article schema_type_article_news schema_type_article_tech',
			);

			foreach ( array( 'schema_desc', 'subsection_schema' ) as $key )
				if ( isset( $table_rows[$key] ) )
					unset ( $table_rows[$key] );

			$form_rows = array(
				'subsection_schema' => array(
					'td_class' => 'subsection',
					'header' => 'h4',
					'label' => _x( 'Google Structured Data / Schema Markup', 'metabox title', 'wpsso-schema-json-ld' )
				),
				'schema_is_main' => array(
					'label' => _x( 'Main Entity of Page', 'option label', 'wpsso-schema-json-ld' ),
					'th_class' => 'medium', 'tooltip' => 'meta-schema_is_main',
					'content' => $form->get_checkbox( 'schema_is_main' ),
				),
				'schema_type' => array(
					'label' => _x( 'Schema Item Type', 'option label', 'wpsso-schema-json-ld' ),
					'th_class' => 'medium', 'tooltip' => 'meta-schema_type',
					'no_auto_draft' => true,
					'content' => $form->get_select( 'schema_type', $schema_types,
						'long_name', '', true, false, true, 'unhide_rows' ),
				),
				'schema_title' => array(
					'label' => _x( 'Schema Item Name', 'option label', 'wpsso-schema-json-ld' ),
					'th_class' => 'medium', 'tooltip' => 'meta-schema_title',
					'no_auto_draft' => true,
					'content' => $form->get_input( 'schema_title', 'wide', $this->p->cf['lca'].'_schema_title', 
						$title_max_len, $this->p->webpage->get_title( $title_max_len, '...', $mod ) ),
				),
				'schema_headline' => array(
					'tr_class' => 'schema_type '.$tr_class['article'],
					'label' => _x( 'Article Headline', 'option label', 'wpsso-schema-json-ld' ),
					'th_class' => 'medium', 'tooltip' => 'meta-schema_headline',
					'no_auto_draft' => true,
					'content' => $form->get_input( 'schema_headline', 'wide', $this->p->cf['lca'].'_schema_headline', 
						$headline_max_len, $this->p->webpage->get_title( $headline_max_len, '...', $mod ) ),
				),
				'schema_desc' => array(
					'label' => _x( 'Schema Description', 'option label', 'wpsso-schema-json-ld' ),
					'th_class' => 'medium', 'tooltip' => 'meta-schema_desc',
					'no_auto_draft' => true,
					'content' => $form->get_textarea( 'schema_desc', null, $this->p->cf['lca'].'_schema_desc', 
						$desc_max_len, $this->p->webpage->get_description( $desc_max_len, '...', $mod ) ),
				),
			);

			$auto_draft_msg = sprintf( __( 'Save a draft version or publish the %s to update this value.',
				'wpsso-schema-json-ld' ), ucfirst( $mod['post_type'] ) );

			return $form->get_md_form_rows( $table_rows, $form_rows, $head, $mod, $auto_draft_msg );
		}
	}
}

?>
