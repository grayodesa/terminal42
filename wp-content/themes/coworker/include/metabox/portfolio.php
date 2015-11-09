<?php

$prefix = 'semi_port_';

global $portmeta_boxes;

$portmeta_boxes = array();

$portmeta_boxes[] = array(
	'id' => 'portfoliodetails',
	'title' => __( 'Details', 'coworker' ),
	'pages' => array( 'portfolio' ),
	'context' => 'normal',
	'priority' => 'high',
	'fields' => array(
		
        array(
			'name' => __( 'Item Type', 'coworker' ),
			'id'   => $prefix . 'type',
			'type' => 'select',
			'options' => array(
				'image' => __( 'Image', 'coworker' ),
				'gallery' => __( 'Gallery', 'coworker' ),
				'video' => __( 'Video', 'coworker' )
			),
			'std'  => array( 'pic' ),
			'desc' => __( 'Select the Portfolio Item Type.', 'coworker' )
		),
        array(
			'name' => __( 'Height', 'coworker' ),
			'id' => $prefix . 'height',
			'desc' => __( 'The Portfolio Image Height (in px) for the 5-Column Masonry Layout. Just enter a number.', 'coworker' ),
			'type'  => 'text',
			'std' => '',
            'size' => '10'
		),
        array(
			'name' => __( 'Single Page Layout', 'coworker' ),
			'id'   => $prefix . 'single_layout',
			'type' => 'select',
			'options' => array(
				'half-right' => __( 'Half Layout - Right Meta', 'coworker' ),
				'half-left' => __( 'Half Layout - Left Meta', 'coworker' ),
				'full-right' => __( 'Full Layout - Right Meta', 'coworker' ),
				'full-left' => __( 'Full Layout - Left Meta', 'coworker' ),
				'rs-right' => __( 'Right Sidebar - Right Meta', 'coworker' ),
				'rs-left' => __( 'Right Sidebar - Left Meta', 'coworker' ),
				'ls-right' => __( 'Left Sidebar - Right Meta', 'coworker' ),
				'ls-left' => __( 'Left Sidebar - Left Meta', 'coworker' )
			),
			'half-right'  => array( 'pic' ),
			'desc' => __( 'Select the Project Details Page Layout.', 'coworker' )
		),
        array(
			'name' => __( 'Client\'s Name', 'coworker' ),
			'id' => $prefix . 'client',
			'desc' => __( 'Client\'s Name for whom you worked on this Project.', 'coworker' ),
			'type'  => 'text',
			'std' => '',
            'size' => '50'
		),
        array(
			'name' => __( 'Authors\'s Name', 'coworker' ),
			'id' => $prefix . 'author',
			'desc' => __( 'Enter the Project Author\'s Name.', 'coworker' ),
			'type'  => 'text',
			'std' => '',
            'size' => '50'
		),
        array(
			'name' => __( 'Completion Date', 'coworker' ),
			'id' => $prefix . 'date',
			'desc' => __( 'The Project Completion Date.', 'coworker' ),
			'type'  => 'date',
            'format' => 'd MM, yy'
		),
        array(
			'name' => __( 'Skills', 'coworker' ),
			'id' => $prefix . 'skills',
			'desc' => __( 'Skills required to complete this Project.', 'coworker' ),
			'type'  => 'text',
			'std' => '',
			'clone' => true
		),
        array(
			'name' => __( 'Show Groups', 'coworker' ),
			'id'   => $prefix . 'show_cats',
			'type' => 'select',
			'options' => array(
				'links' => __( 'with Links', 'coworker' ),
				'nolinks' => __( 'without Links', 'coworker' ),
				'noshow' => __( 'Dont Show', 'coworker' )
			),
			'std'  => array( 'links' ),
			'desc' => __( 'Select if you want to show the Portfolio Groups in the Project Details.', 'coworker' )
		),
        array(
			'name' => __( 'URL', 'coworker' ),
			'id' => $prefix . 'url',
			'desc' => __( 'The Project\'s Link Address. Include http://', 'coworker' ),
			'type'  => 'text',
			'std' => 'http://',
            'size' => '100'
		),
        array(
            'name' => __( 'Show Launch Button', 'coworker' ),
			'id' => $prefix . 'launch_btn',
			'type' => 'checkbox',
			'desc' => __( 'Check to show a Launch Button for your Project URL above. If not checked, the whole URL will be shown.', 'coworker' ),
			'std' => 0
		),
        array(
			'name' => __( 'Launch Button Text', 'coworker' ),
			'id' => $prefix . 'launch_text',
			'desc' => __( 'Enter the Text you want to show on the Launch Button.', 'coworker' ),
			'type'  => 'text',
			'std' => '',
            'size' => '30'
		),
        array(
			'name' => __( 'Copyrights', 'coworker' ),
			'id' => $prefix . 'copyrights',
			'desc' => __( 'Enter the Copyrights Owner\'s Name here. You can also include a Link with an &lt;a&gt; Tag.', 'coworker' ),
			'type'  => 'text',
			'std' => '',
            'size' => '100'
		),
        array(
			'name' => __( 'Extra Fields', 'coworker' ),
			'desc' => __( 'Enter your Extra Fields here. Eg. [portmeta title="Author"]John Doe[/portmeta]', 'coworker' ),
			'id'   => $prefix . 'extra_fields',
			'type' => 'textarea',
			'std'  => '',
			'cols' => '40',
			'rows' => '10',
		)
        
    )
);


$portmeta_boxes[] = array(
	'id' => 'portfoliogallery',
	'title' => __( 'Gallery Settings', 'coworker' ),
	'pages' => array( 'portfolio' ),
	'context' => 'normal',
	'priority' => 'high',
	'fields' => array(
		
        array(
			'name'  => __( 'Gallery', 'coworker' ),
			'desc' => __( 'Additional Images for your Gallery. Maximum 10 Images.', 'coworker' ),
			'id' => $prefix . 'gallery',
			'type' => 'image_advanced',
			'max_file_uploads' => 10
		),
        array(
			'name' => __( 'Animation Type', 'coworker' ),
			'id'   => $prefix . 'g_animation',
			'type' => 'select',
			'options' => array(
				'slide' => __( 'Slide', 'coworker' ),
				'fade' => __( 'Fade', 'coworker' )
			),
			'std'  => array( 'slide' ),
			'desc' => __( 'Select the Gallery Slider Animation Type.', 'coworker' )
		),
        array(
			'name' => __( 'Easing', 'coworker' ),
			'id'   => $prefix . 'g_easing',
			'type' => 'select',
			'options' => get_easing_ops(),
			'std'  => array( 'swing' ),
			'desc' => __( 'Select the Gallery Slider Easing.', 'coworker' )
		),
        array(
			'name' => __( 'Slide Direction', 'coworker' ),
			'id'   => $prefix . 'g_direction',
			'type' => 'select',
			'options' => array(
				'horizontal' => __( 'Horizontal', 'coworker' ),
				'vertical' => __( 'Vertical', 'coworker' )
			),
			'std'  => array( 'horizontal' ),
			'desc' => __( 'Select the Gallery Slider\'s Slide Direction.', 'coworker' )
		),
        array(
			'name' => __( 'Slideshow', 'coworker' ),
			'id'   => $prefix . 'g_slideshow',
			'type' => 'select',
			'options' => array(
				'true' => __( 'Auto', 'coworker' ),
				'false' => __( 'Manual', 'coworker' )
			),
			'std'  => array( 'true' ),
			'desc' => __( 'Select if you want to autorun the Gallery Slider.', 'coworker' )
		),
        array(
			'name' => __( 'Pause Time', 'coworker' ),
			'id' => $prefix . 'g_pause',
			'desc' => __( 'Enter the Pause Time between Gallery Slider\'s Images in milliseconds. Just enter a number. Eg. 5000 for 5 Seconds.', 'coworker' ),
			'type'  => 'text',
			'std' => '',
            'size' => '10'
		),
        array(
			'name' => __( 'Speed', 'coworker' ),
			'id' => $prefix . 'g_speed',
			'desc' => __( 'Enter the Speed of the Gallery Slider\'s Animations in milliseconds. Just enter a number. Eg. 500 for 0.5 Seconds.', 'coworker' ),
			'type'  => 'text',
			'std' => '',
            'size' => '10'
		)
        
    )
);


$portmeta_boxes[] = array(
	'id' => 'portfolioembed',
	'title' => __( 'Video Embed', 'coworker' ),
	'pages' => array( 'portfolio' ),
	'context' => 'normal',
	'priority' => 'high',
	'fields' => array(
		
        array(
			'name' => __( 'Vimeo/Youtube URL', 'coworker' ),
			'id' => $prefix . 'vyurl',
			'desc' => __( 'Enter your Vimeo/Youtube Video URL only to open in the Lightbox.', 'coworker' ),
			'type'  => 'text',
			'std' => '',
            'size' => '100'
		),
        array(
			'name' => __( 'Video Embed Code', 'coworker' ),
			'desc' => __( 'Please Enter your Video Embed Code. If this is a Youtube/Vimeo Embed, make sure you enter the Video URL above to open the Video in the Lightbox.', 'coworker' ),
			'id'   => $prefix . 'video',
			'type' => 'textarea',
			'std'  => '',
			'cols' => '40',
			'rows' => '6'
		)
        
    )
);


function semi_port_register_meta_boxes() {

    global $portmeta_boxes;
    
	if ( class_exists( 'RW_Meta_Box' ) ) {
	
        foreach ( $portmeta_boxes as $portmeta_box ) {
			new RW_Meta_Box( $portmeta_box );
		}
    
	}

}

add_action( 'admin_init', 'semi_port_register_meta_boxes' );

?>