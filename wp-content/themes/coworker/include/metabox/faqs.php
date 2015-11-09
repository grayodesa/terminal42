<?php

$prefix = 'semi_faq_';

global $faqmeta_boxes;

$faqmeta_boxes = array();

$faqmeta_boxes[] = array(
	'id' => 'faqsettings',
	'title' => __( 'FAQ Options', 'coworker' ),
	'pages' => array( 'faqs' ),
	'context' => 'normal',
	'priority' => 'high',
	'fields' => array(
		
        array(
            'name' => __( 'Icon', 'coworker' ),
			'id' => $prefix . 'icon',
            'type' => 'select',
			'options' => get_font_awesome(),
			'std'  => array( 'icon-question-sign' ),
			'desc' => __( 'Select your FAQ Icon.', 'coworker' )
		)
        
    )
);


function semi_faq_register_meta_boxes() {

    global $faqmeta_boxes;

	// Make sure there's no errors when the plugin is deactivated or during upgrade
	if ( class_exists( 'RW_Meta_Box' ) ) {
		foreach ( $faqmeta_boxes as $faqmeta_box ) {
			if ( isset( $faqmeta_box['only_on'] ) && ! rw_maybe_include( $faqmeta_box['only_on'] ) ) {
				continue;
			}

			new RW_Meta_Box( $faqmeta_box );
		}
	}

}

add_action( 'admin_init', 'semi_faq_register_meta_boxes' );


?>