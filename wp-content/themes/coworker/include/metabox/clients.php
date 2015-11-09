<?php

$prefix = 'semi_clients_';

global $cmeta_boxes;

$cmeta_boxes = array();

$cmeta_boxes[] = array(
	'id' => 'clientoptions',
	'title' => __( 'Client Options', 'coworker' ),
	'pages' => array( 'clients' ),
	'context' => 'normal',
	'priority' => 'high',
	'fields' => array(
		
        array(
			'name' => __( 'URL', 'coworker' ),
			'id' => $prefix . 'url',
			'desc' => __( 'The Client Link Address. Include http://', 'coworker' ),
			'type'  => 'text',
			'std' => 'http://',
            'size' => '100'
		)
        
    )
);

function semi_clients_register_meta_boxes() {

    global $cmeta_boxes;
    
	if ( class_exists( 'RW_Meta_Box' ) ) {
	
        foreach ( $cmeta_boxes as $cmeta_box ) {
			new RW_Meta_Box( $cmeta_box );
		}
    
	}

}

add_action( 'admin_init', 'semi_clients_register_meta_boxes' );

?>