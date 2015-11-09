<?php

$prefix = 'semi_page_';

global $pbgmeta_boxes;

$pbgmeta_boxes = array();


$pbgmeta_boxes[] = array(
	'id' => 'backgroundsettings',
	'title' => __( 'Background Settings', 'coworker' ),
	'pages' => array( 'page', 'post', 'portfolio' ),
	'context' => 'normal',
	'priority' => 'high',
	'fields' => array(
		
        array(
			'name'  => __( 'Background Image', 'coworker' ),
			'desc' => __( 'Add a Background Image', 'coworker' ),
			'id' => $prefix . 'bg_image',
			'type' => 'image_advanced',
			'max_file_uploads' => 1
		),
        array(
            'name' => __( 'Disable Background', 'coworker' ),
			'id' => $prefix . 'bg_image_disable',
			'type' => 'checkbox',
			'desc' => __( 'Check to Disable Background Image on this Page', 'coworker' ),
			'std' => 0
		)
        
    )
);


function semi_pagebg_register_meta_boxes() {

    global $pbgmeta_boxes;

	// Make sure there's no errors when the plugin is deactivated or during upgrade
	if ( class_exists( 'RW_Meta_Box' ) ) {
		foreach ( $pbgmeta_boxes as $pbgmeta_box ) {
			if ( isset( $pbgmeta_box['only_on'] ) && ! rw_maybe_include( $pbgmeta_box['only_on'] ) ) {
				continue;
			}

			new RW_Meta_Box( $pbgmeta_box );
		}
	}

}

add_action( 'admin_init', 'semi_pagebg_register_meta_boxes' );


?>