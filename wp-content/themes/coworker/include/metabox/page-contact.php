<?php

$prefix = 'semi_page_contact_';

global $pagecontactmeta_boxes;

$pagecontactmeta_boxes = array();


$pagecontactmeta_boxes[] = array(
	'id' => 'contactsettings',
	'title' => __( 'Contact Options', 'coworker' ),
	'pages' => array( 'page' ),
	'context' => 'normal',
	'priority' => 'high',
	'fields' => array(
		
        array(
            'name' => __( 'Form Title', 'coworker' ),
			'id' => $prefix . 'formtitle',
			'desc' => __( 'Title of the Contact Form.', 'coworker' ),
			'type'  => 'text',
			'std' => 'Send us an <span>Email</span>',
            'size' => '80'
		),
        array(
            'name' => __( 'Form Submit Button Text', 'coworker' ),
			'id' => $prefix . 'formbutton',
			'desc' => __( 'Contact Form\'s Submit Button Text', 'coworker' ),
			'type'  => 'text',
			'std' => 'Send Message',
            'size' => '30'
		),
        array(
			'name' => __( 'Services Options', 'coworker' ),
			'id' => $prefix . 'services',
			'desc' => __( 'Add your Custom Service Options.', 'coworker' ),
			'type'  => 'text',
			'std' => '',
			'clone' => true
		),
        array(
            'name' => __( 'Enable reCaptcha Protection', 'coworker' ),
			'id' => $prefix . 'recaptcha',
			'type' => 'checkbox',
			'desc' => __( 'Check to Enable reCaptcha Protection.', 'coworker' ),
			'std' => 0
		),
        array(
            'name' => __( 'Map Height', 'coworker' ),
			'id' => $prefix . 'mheight',
			'desc' => __( 'Height of the Map. Enter only a Number. Eg. 400', 'coworker' ),
			'type'  => 'text',
			'std' => '400',
            'size' => '3'
		),
        array(
            'name' => __( 'Latitude', 'coworker' ),
			'id' => $prefix . 'latitude',
			'desc' => __( 'Latitude Value for the Map. Enter a Float Number.', 'coworker' ),
			'type'  => 'text',
			'std' => '',
            'size' => '20'
		),
        array(
            'name' => __( 'Longitude', 'coworker' ),
			'id' => $prefix . 'longitude',
			'desc' => __( 'Longitude Value for the Map. Enter a Float Number.', 'coworker' ),
			'type'  => 'text',
			'std' => '',
            'size' => '20'
		),
        array(
            'name' => __( 'Address', 'coworker' ),
			'id' => $prefix . 'address',
			'desc' => __( 'Map Address in 3 to 6 Words. If you enter this, Latitude &amp; Longitude values will be ignored.', 'coworker' ),
			'type'  => 'text',
			'std' => '',
            'size' => '50'
		),
        array(
			'name' => __( 'Map Content', 'coworker' ),
			'desc' => __( 'Content to show in the Map Popup. HTML Supported.', 'coworker' ),
			'id'   => $prefix . 'html',
			'type' => 'textarea',
			'cols' => '40',
			'rows' => '3'
		),
        array(
            'name' => __( 'Zoom', 'coworker' ),
			'id' => $prefix . 'zoom',
            'type' => 'select',
			'options' => array(
				1 => 1,
				2 => 2,
				3 => 3,
				4 => 4,
				5 => 5,
				6 => 6,
				7 => 7,
				8 => 8,
				9 => 9,
				10 => 10,
				11 => 11,
				12 => 12,
				13 => 13,
				14 => 14,
				15 => 15,
				16 => 16
			),
			'std'  => array( 14 ),
			'desc' => __( 'Select the amount of Zoom for the Map.', 'coworker' )
		),
        array(
            'name' => __( 'Map Type', 'coworker' ),
			'id' => $prefix . 'maptype',
            'type' => 'select',
			'options' => array(
				'HYBRID' => 'HYBRID',
				'ROADMAP' => 'ROADMAP',
				'SATELLITE' => 'SATELLITE',
				'TERRAIN' => 'TERRAIN'
			),
			'std'  => array( 'ROADMAP' ),
			'desc' => __( 'Select the Map Type.', 'coworker' )
		),
        array(
            'name' => __( 'Scrollwheel', 'coworker' ),
			'id' => $prefix . 'scrollwheel',
			'type' => 'checkbox',
			'desc' => __( 'Check to use Scrollwheel on the Map.', 'coworker' ),
			'std' => 0
		),
        array(
            'name' => __( 'Pan Control', 'coworker' ),
			'id' => $prefix . 'pancontrol',
			'type' => 'checkbox',
			'desc' => __( 'Check to use Pan Control on the Map.', 'coworker' ),
			'std' => 1
		),
        array(
            'name' => __( 'Zoom Control', 'coworker' ),
			'id' => $prefix . 'zoomcontrol',
			'type' => 'checkbox',
			'desc' => __( 'Check to use Zoom Control on the Map.', 'coworker' ),
			'std' => 1
		),
        array(
            'name' => __( 'MapType Control', 'coworker' ),
			'id' => $prefix . 'maptypecontrol',
			'type' => 'checkbox',
			'desc' => __( 'Check to use MapType Control on the Map.', 'coworker' ),
			'std' => 1
		),
        array(
            'name' => __( 'Scale Control', 'coworker' ),
			'id' => $prefix . 'scalecontrol',
			'type' => 'checkbox',
			'desc' => __( 'Check to use Scale Control on the Map.', 'coworker' ),
			'std' => 0
		),
        array(
            'name' => __( 'Street View Control', 'coworker' ),
			'id' => $prefix . 'streetviewcontrol',
			'type' => 'checkbox',
			'desc' => __( 'Check to use Street View Control on the Map.', 'coworker' ),
			'std' => 0
		),
        array(
            'name' => __( 'Overview Map Control', 'coworker' ),
			'id' => $prefix . 'overviewmapcontrol',
			'type' => 'checkbox',
			'desc' => __( 'Check to use Overview Map Control on the Map.', 'coworker' ),
			'std' => 0
		)
        
    ),
	'only_on'    => array(
		'template' => array( 'template-contact.php', 'template-contact-half-left.php', 'template-contact-half-right.php', 'template-contact-sidebar.php', 'template-contact-split.php' )
	)
);


function semi_pagecontact_register_meta_boxes() {

    global $pagecontactmeta_boxes;

	// Make sure there's no errors when the plugin is deactivated or during upgrade
	if ( class_exists( 'RW_Meta_Box' ) ) {
		foreach ( $pagecontactmeta_boxes as $pagecontactmeta_box ) {
			if ( isset( $pagecontactmeta_box['only_on'] ) && ! rw_maybe_include( $pagecontactmeta_box['only_on'] ) ) {
				continue;
			}

			new RW_Meta_Box( $pagecontactmeta_box );
		}
	}

}

add_action( 'admin_init', 'semi_pagecontact_register_meta_boxes' );


?>