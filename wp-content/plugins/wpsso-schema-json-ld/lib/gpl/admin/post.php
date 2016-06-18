<?php
/*
 * License: GPLv3
 * License URI: http://www.gnu.org/licenses/gpl.txt
 * Copyright 2012-2016 Jean-Sebastien Morisset (http://surniaulula.com/)
 */

if ( ! defined( 'ABSPATH' ) ) 
	die( 'These aren\'t the droids you\'re looking for...' );

if ( ! class_exists( 'WpssoJsonGplAdminPost' ) ) {

	class WpssoJsonGplAdminPost {

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
			$auto_draft_msg = sprintf( __( 'Save a draft version or publish the %s to update this value.',
				'wpsso-schema-json-ld' ), ucfirst( $mod['post_type'] ) );

			if ( ! empty( $this->p->cf['plugin']['wpssoorg'] ) &&
				empty( $this->p->cf['plugin']['wpssoorg']['version'] ) ) {

				$info = $this->p->cf['plugin']['wpssoorg'];
				$org_req_msg = ' <em><a href="'.$info['url']['download'].'" target="_blank">'.
					sprintf( _x( '%s extension required', 'option comment', 'wpsso-schema-json-ld' ),
						$info['short'] ).'</a></em>';
			} else $org_req_msg = '';

			$org_names = array( 'site' => _x( 'Website', 'option value', 'wpsso-schema-json-ld' ) );

			// javascript hide/show classes for schema type rows
			$tr_class = array(
				'article' => $this->p->schema->get_schema_type_css_classes( 'article' ),
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
					'th_class' => 'medium', 'tooltip' => 'meta-schema_is_main', 'td_class' => 'blank',
					'content' => $form->get_no_checkbox( 'schema_is_main' ),
				),
				'schema_type' => array(
					'label' => _x( 'Schema Item Type', 'option label', 'wpsso-schema-json-ld' ),
					'th_class' => 'medium', 'tooltip' => 'meta-schema_type', 'td_class' => 'blank',
					'no_auto_draft' => true,
					'content' => $form->get_no_select( 'schema_type', $schema_types,
						'long_name', '', true, $form->defaults['schema_type'], 'unhide_rows' ),
				),
				'schema_title' => array(
					'label' => _x( 'Schema Item Name', 'option label', 'wpsso-schema-json-ld' ),
					'th_class' => 'medium', 'tooltip' => 'meta-schema_title', 'td_class' => 'blank',
					'no_auto_draft' => true,
					'content' => $form->get_no_input_value( $this->p->webpage->get_title( $title_max_len,
						'...', $mod ), 'wide' ),
				),
				'schema_headline' => array(
					'tr_class' => 'schema_type '.$tr_class['article'],
					'label' => _x( 'Article Headline', 'option label', 'wpsso-schema-json-ld' ),
					'th_class' => 'medium', 'tooltip' => 'meta-schema_headline', 'td_class' => 'blank',
					'no_auto_draft' => true,
					'content' => $form->get_no_input_value( $this->p->webpage->get_title( $headline_max_len, '...', $mod ), 'wide' ),
				),
				'schema_pub_org_id' => array(
					'tr_class' => 'schema_type '.$tr_class['article'],
					'label' => _x( 'Article Publisher', 'option label', 'wpsso-schema-json-ld' ),
					'th_class' => 'medium', 'tooltip' => 'meta-schema_pub_org_id', 'td_class' => 'blank',
					'no_auto_draft' => true,
					'content' => $form->get_no_select( 'schema_pub_org_id', $org_names, 'long_name' ).$org_req_msg,
				),
				'schema_desc' => array(
					'label' => _x( 'Schema Description', 'option label', 'wpsso-schema-json-ld' ),
					'th_class' => 'medium', 'tooltip' => 'meta-schema_desc', 'td_class' => 'blank',
					'no_auto_draft' => true,
					'content' => $form->get_no_textarea_value( $this->p->webpage->get_description( $desc_max_len, 
						'...', $mod ), '', '', $desc_max_len ),
				),
			);

			$table_rows = $form->get_md_form_rows( $table_rows, $form_rows, $head, $mod, $auto_draft_msg );

			return SucomUtil::after_key( $table_rows, 'subsection_schema',
				'', '<td colspan="2">'.$this->p->msgs->get( 'pro-feature-msg', 
					array( 'lca' => 'wpssojson' ) ).'</td>' );
		}
	}
}

?>
