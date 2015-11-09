<?php

$prefix = 'semi_feature_';

global $featuremeta_boxes;

$featuremeta_boxes = array();

$featuremeta_boxes[] = array(
	'id' => 'featuresettings',
	'title' => __( 'Feature Options', 'coworker' ),
	'pages' => array( 'features' ),
	'context' => 'normal',
	'priority' => 'high',
	'fields' => array(
		
        array(
            'name' => __( 'Icon', 'coworker' ),
			'id' => $prefix . 'icon',
            'type' => 'select',
			'options' => get_font_awesome( true ),
			'std'  => array( 'none' ),
			'desc' => __( 'Select your Feature Icon to show in the Side Navigation.', 'coworker' )
		)
        
    )
);


function semi_feature_register_meta_boxes() {

    global $featuremeta_boxes;

	// Make sure there's no errors when the plugin is deactivated or during upgrade
	if ( class_exists( 'RW_Meta_Box' ) ) {
		foreach ( $featuremeta_boxes as $featuremeta_box ) {
			if ( isset( $featuremeta_box['only_on'] ) && ! rw_maybe_include( $featuremeta_box['only_on'] ) ) {
				continue;
			}

			new RW_Meta_Box( $featuremeta_box );
		}
	}

}

add_action( 'admin_init', 'semi_feature_register_meta_boxes' );


?>