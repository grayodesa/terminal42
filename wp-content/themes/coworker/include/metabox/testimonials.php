<?php

$prefix = 'semi_testimonials_';

global $tesmeta_boxes;

$tesmeta_boxes = array();

$tesmeta_boxes[] = array(
	'id' => 'metabox',
	'title' => __( 'Testimonial Data', 'coworker' ),
	'pages' => array( 'testimonials' ),
	'context' => 'normal',
	'priority' => 'high',
	'fields' => array(
		
        array(
			'name' => __( 'Author', 'coworker' ),
			'id' => $prefix . 'author',
			'desc' => __( 'The name of the Testimonial Author.', 'coworker' ),
			'type'  => 'text',
			'std' => '',
            'size' => '50'
		),
        array(
			'name' => __( 'URL', 'coworker' ),
			'id' => $prefix . 'url',
			'desc' => __( 'The Testimonial Author\'s Link Address. Include http://', 'coworker' ),
			'type'  => 'text',
			'std' => 'http://',
            'size' => '100'
		),
        array(
			'name' => __( 'Company', 'coworker' ),
			'id' => $prefix . 'company',
			'desc' => __( 'The Testimonial Author\'s Company Name.', 'coworker' ),
			'type'  => 'text',
			'std' => '',
            'size' => '50'
		),
        array(
			'name' => __( 'Text', 'coworker' ),
			'desc' => __( 'Write your Testimonial\'s here. HTML not Supported.', 'coworker' ),
			'id'   => $prefix . 'text',
			'type' => 'textarea',
			'std'  => '',
			'cols' => '30',
			'rows' => '6',
		)
        
    )
);

function semi_testimonials_register_meta_boxes() {

    global $tesmeta_boxes;
    
	if ( class_exists( 'RW_Meta_Box' ) ) {
	
        foreach ( $tesmeta_boxes as $tesmeta_box ) {
			new RW_Meta_Box( $tesmeta_box );
		}
    
	}

}

add_action( 'admin_init', 'semi_testimonials_register_meta_boxes' );

?>