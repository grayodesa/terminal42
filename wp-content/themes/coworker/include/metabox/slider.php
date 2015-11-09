<?php

$prefix = 'semi_slider_';

global $smeta_boxes;

$smeta_boxes = array();

$smeta_boxes[] = array(
	'id' => 'sliderdetails',
	'title' => __( 'Slide Details', 'coworker' ),
	'pages' => array( 'slider' ),
	'context' => 'normal',
	'priority' => 'high',
	'fields' => array(
		
        array(
			'name' => __( 'Caption', 'coworker' ),
			'desc' => __( 'Your Caption Content. Can be either Simple Text or an HTML. Keep your HTML simple.<br>Eg.<br>&lt;h3&gt;InBuilt Power Features&lt;/h3&gt;<br>&lt;p&gt;Nam ultricies dolor eu velit varius scelerisque. Vestibulum in lacus in felis pretium feugiat non sed elit.&lt;/p&gt;', 'coworker' ),
			'id'   => $prefix . 'caption',
			'type' => 'textarea',
			'std'  => '',
			'cols' => '40',
			'rows' => '4'
		),
        array(
			'name' => __( 'Caption Type', 'coworker' ),
			'id'   => $prefix . 'caption_type',
			'type' => 'select',
			'options' => array(
				'simple' => __( 'Simple', 'coworker' ),
				'chunky' => __( 'Chunky', 'coworker' )
			),
			'std'  => array( 'simple' ),
			'desc' => __( 'Select the Caption Type.', 'coworker' )
		),
        array(
			'name' => __( 'Caption Position', 'coworker' ),
			'id'   => $prefix . 'caption_position',
			'type' => 'select',
			'options' => array(
				'left' => __( 'Left', 'coworker' ),
				'right' => __( 'Right', 'coworker' )
			),
			'std'  => array( 'right' ),
			'desc' => __( 'Select the Caption Position.', 'coworker' )
		),
        array(
			'name' => __( 'URL', 'coworker' ),
			'id' => $prefix . 'url',
			'desc' => __( 'The Slide\'s Link Address. Include http://', 'coworker' ),
			'type'  => 'text',
			'std' => 'http://',
            'size' => '100'
		),
        array(
			'name' => __( 'URL Target', 'coworker' ),
			'id'   => $prefix . 'target',
			'type' => 'select',
			'options' => array(
				'_self' => __( 'Same Window', 'coworker' ),
				'_blank' => __( 'New Window', 'coworker' )
			),
			'std'  => array( '' ),
			'desc' => __( 'Select the URL Target.', 'coworker' )
		)
        
    )
);


$smeta_boxes[] = array(
	'id' => 'sliderembed',
	'title' => __( 'Video', 'coworker' ),
	'pages' => array( 'slider' ),
	'context' => 'normal',
	'priority' => 'high',
	'fields' => array(
		
        array(
			'name' => __( 'Video Embed Code', 'coworker' ),
			'desc' => __( 'Please Enter your Video Embed Code. Make sure your Video\'s Width and Height matches the Slider\'s Dimensions.', 'coworker' ),
			'id'   => $prefix . 'video',
			'type' => 'textarea',
			'std'  => '',
			'cols' => '40',
			'rows' => '6'
		)
        
    )
);


function semi_slider_register_meta_boxes() {

    global $smeta_boxes;
    
	if ( class_exists( 'RW_Meta_Box' ) ) {
	
        foreach ( $smeta_boxes as $smeta_box ) {
			new RW_Meta_Box( $smeta_box );
		}
    
	}

}

add_action( 'admin_init', 'semi_slider_register_meta_boxes' );

?>