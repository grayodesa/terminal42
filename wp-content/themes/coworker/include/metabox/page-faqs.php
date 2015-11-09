<?php

$prefix = 'semi_page_faqs_';

global $pagefaqsmeta_boxes;

$pagefaqsmeta_boxes = array();

$pagefaqsmeta_boxes[] = array(
	'id' => 'faqsettings',
	'title' => __( 'FAQs Options', 'coworker' ),
	'pages' => array( 'page' ),
	'context' => 'normal',
	'priority' => 'high',
	'fields' => array(
		
        array(
			'name' => __( 'FAQs Categories', 'coworker' ),
			'id'   => $prefix . 'category',
			'type'    => 'taxonomy',
			'options' => array(
				'taxonomy' => 'faqs-group',
				'type' => 'checkbox_list'
			),
			'desc' => __( 'Choose the Categories you want to display on this FAQs Page. Leave Blank if you want to show faqs from all categories.', 'coworker' )
		),
        array(
            'name' => __( 'No. of FAQs', 'coworker' ),
			'id' => $prefix . 'items',
			'desc' => __( 'Enter the number of faqs you want to show. If you enter "-1", all the faqs will be retrieved.', 'coworker' ),
			'type'  => 'text',
			'std' => '-1',
            'size' => '2'
		),
        array(
            'name' => __( 'FAQs Order', 'coworker' ),
			'id' => $prefix . 'order',
            'type' => 'select',
			'options' => array(
				'ASC' => 'ASC',
				'DESC' => 'DESC'
			),
			'std'  => array( 'DESC' ),
			'desc' => __( 'Select the FAQs Order.', 'coworker' )
		),
        array(
            'name' => __( 'FAQs Order by', 'coworker' ),
			'id' => $prefix . 'orderby',
            'type' => 'select',
			'options' => array(
				'ID' => 'ID',
				'title' => __( 'Title', 'coworker' ),
				'date' => __( 'Date', 'coworker' ),
				'rand' => __( 'Random', 'coworker' ),
				'menu_order' => __( 'Menu Order', 'coworker' )
			),
			'std'  => array( 'date' ),
			'desc' => __( 'Select the FAQs Order by condition.', 'coworker' )
		)
        
    ),
	'only_on'    => array(
		'template' => array( 'template-faqs.php' )
	)
);


function semi_pagefaqs_register_meta_boxes() {

    global $pagefaqsmeta_boxes;

	// Make sure there's no errors when the plugin is deactivated or during upgrade
	if ( class_exists( 'RW_Meta_Box' ) ) {
		foreach ( $pagefaqsmeta_boxes as $pagefaqsmeta_box ) {
			if ( isset( $pagefaqsmeta_box['only_on'] ) && ! rw_maybe_include( $pagefaqsmeta_box['only_on'] ) ) {
				continue;
			}

			new RW_Meta_Box( $pagefaqsmeta_box );
		}
	}

}

add_action( 'admin_init', 'semi_pagefaqs_register_meta_boxes' );


?>