<?php

$prefix = 'semi_page_comingsoon_';

global $pagecomingsoonmeta_boxes;

$pagecomingsoonmeta_boxes = array();


$pagecomingsoonmeta_boxes[] = array(
	'id' => 'comingsoonsettings',
	'title' => __( 'Coming Soon Options', 'coworker' ),
	'pages' => array( 'page' ),
	'context' => 'normal',
	'priority' => 'high',
	'fields' => array(
		
        array(
			'name' => __( 'Message', 'coworker' ),
			'desc' => __( 'Message to show above the Countdown Counter.', 'coworker' ),
			'id'   => $prefix . 'message',
			'type' => 'text',
			'std' => '',
			'size' => '150'
		),
        array(
            'name' => __( 'Day', 'coworker' ),
			'id' => $prefix . 'day',
			'desc' => __( 'Enter a Day from 1 to 31', 'coworker' ),
			'type'  => 'text',
			'std' => '',
            'size' => '1'
		),
        array(
            'name' => __( 'Month', 'coworker' ),
			'id' => $prefix . 'month',
            'type' => 'select',
			'options' => array(
				0 => 'January',
				1 => 'February',
				2 => 'March',
				3 => 'April',
				4 => 'May',
				5 => 'June',
				6 => 'July',
				7 => 'August',
				8 => 'September',
				9 => 'October',
				10 => 'November',
				11 => 'December'
			),
			'std'  => array( 0 ),
			'desc' => __( 'Select the month of the Countdown.', 'coworker' )
		),
        array(
            'name' => __( 'Year', 'coworker' ),
			'id' => $prefix . 'year',
			'desc' => __( 'Enter a Year equal to or more than ' . date( 'Y' ) , 'coworker' ),
			'type'  => 'text',
			'std' => '',
            'size' => '4'
		),
        array(
            'name' => __( 'Custom Countdown', 'coworker' ),
			'id' => $prefix . 'custom',
			'desc' => __( 'Enter a Custom Countdown Timer. <strong>Enter in this format: new Date(...) or in string as mentioned</strong>. Refer to <a href="http://keith-wood.name/countdownRef.html" target="_blank">Countdown Documentation</a>. If you enter this then Day, Month and Year will be ignored.' , 'coworker' ),
			'type'  => 'text',
			'std' => '',
            'size' => '30'
		)
        
    ),
	'only_on'    => array(
		'template' => array( 'template-comingsoon.php' )
	)
);


function semi_pagecomingsoon_register_meta_boxes() {

    global $pagecomingsoonmeta_boxes;

	// Make sure there's no errors when the plugin is deactivated or during upgrade
	if ( class_exists( 'RW_Meta_Box' ) ) {
		foreach ( $pagecomingsoonmeta_boxes as $pagecomingsoonmeta_box ) {
			if ( isset( $pagecomingsoonmeta_box['only_on'] ) && ! rw_maybe_include( $pagecomingsoonmeta_box['only_on'] ) ) {
				continue;
			}

			new RW_Meta_Box( $pagecomingsoonmeta_box );
		}
	}

}

add_action( 'admin_init', 'semi_pagecomingsoon_register_meta_boxes' );


?>