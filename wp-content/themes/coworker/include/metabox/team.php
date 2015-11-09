<?php

$prefix = 'semi_team_';

global $teammeta_boxes;

$teammeta_boxes = array();

$teammeta_boxes[] = array(
	'id' => 'teamoptions',
	'title' => __( 'Team Member Options', 'coworker' ),
	'pages' => array( 'team' ),
	'context' => 'normal',
	'priority' => 'high',
	'fields' => array(
		
        array(
			'name' => __( 'Designation', 'coworker' ),
			'id' => $prefix . 'designation',
			'desc' => __( 'Role of the Team Member.', 'coworker' ),
			'type'  => 'text',
			'std' => '',
            'size' => '50'
		),
        array(
			'name' => __( 'Description', 'coworker' ),
			'desc' => __( 'Describe about the Team Member. No HTML.', 'coworker' ),
			'id'   => $prefix . 'description',
			'type' => 'textarea',
			'std'  => '',
			'cols' => '30',
			'rows' => '6',
		),
        array(
			'name'  => 'Skills',
			'id'    => $prefix . 'skills',
			'desc'  => 'Team Member Skills',
			'type'  => 'text',
			'std'   => '',
			'clone' => true
		),
        array(
			'name' => __( 'Facebook', 'coworker' ),
			'id' => $prefix . 'facebook',
			'desc' => __( 'Facebook Profile URL. Include http://', 'coworker' ),
			'type'  => 'text',
			'std' => '',
            'size' => '100'
		),
        array(
			'name' => __( 'Twitter', 'coworker' ),
			'id' => $prefix . 'twitter',
			'desc' => __( 'Twitter Profile URL. Include http://', 'coworker' ),
			'type'  => 'text',
			'std' => '',
            'size' => '100'
		),
        array(
			'name' => __( 'Dribbble', 'coworker' ),
			'id' => $prefix . 'dribbble',
			'desc' => __( 'Dribbble Profile URL. Include http://', 'coworker' ),
			'type'  => 'text',
			'std' => '',
            'size' => '100'
		),
        array(
			'name' => __( 'Forrst', 'coworker' ),
			'id' => $prefix . 'forrst',
			'desc' => __( 'Forrst Profile URL. Include http://', 'coworker' ),
			'type'  => 'text',
			'std' => '',
            'size' => '100'
		),
        array(
			'name' => __( 'Flickr', 'coworker' ),
			'id' => $prefix . 'flickr',
			'desc' => __( 'Flickr Profile URL. Include http://', 'coworker' ),
			'type'  => 'text',
			'std' => '',
            'size' => '100'
		),
        array(
			'name' => __( 'Additional Icons', 'coworker' ),
			'desc' => __( 'Enter your Additional Icons. Use this:<br>&lt;a href="#"&gt;&lt;img src="iconurl.png" class="ntip" alt="Dribbble" title="Dribbble" /&gt;&lt;/a&gt;', 'coworker' ),
			'id'   => $prefix . 'addicons',
			'type' => 'textarea',
			'std'  => '',
			'cols' => '30',
			'rows' => '6',
		)
        
    )
);

function semi_team_register_meta_boxes() {

    global $teammeta_boxes;
    
	if ( class_exists( 'RW_Meta_Box' ) ) {
	
        foreach ( $teammeta_boxes as $teammeta_box ) {
			new RW_Meta_Box( $teammeta_box );
		}
    
	}

}

add_action( 'admin_init', 'semi_team_register_meta_boxes' );

?>