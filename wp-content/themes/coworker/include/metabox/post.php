<?php

$prefix = 'semi_post_';

global $meta_boxes;

$pometa_boxes = array();

$pometa_boxes[] = array(
	'id' => 'postoptions',
	'title' => __( 'Post Options', 'coworker' ),
	'pages' => array( 'post' ),
	'context' => 'normal',
	'priority' => 'high',
	'fields' => array(
		
        array(
            'name' => __( 'Disable Post Thumbnail', 'coworker' ),
			'id' => $prefix . 'disable_thumb',
			'type' => 'checkbox',
			'desc' => __( 'Check to Hide the Post Thumbnail from on the Post Single Page', 'coworker' ),
			'std' => 0
		),
        array(
            'name' => __( 'Layout', 'coworker' ),
			'id' => $prefix . 'layout',
			'type' => 'select',
			'options' => array(
				'default' => __( 'Default Layout', 'coworker' ),
				'full' => __( 'Full Layout', 'coworker' ),
				'split' => __( 'Split Layout', 'coworker' )
			),
			'std'  => array( 'default' ),
			'desc' => __( 'Select the Layout of this post', 'coworker' )
		),
        array(
            'name' => __( 'Sidebar Position', 'coworker' ),
			'id' => 'semi_sidebar',
			'type' => 'select',
			'options' => array(
				'right' => __( 'Right Sidebar', 'coworker' ),
				'left' => __( 'Left Sidebar', 'coworker' )
			),
			'std'  => array( 'right' ),
			'desc' => __( 'Select the position of the Sidebar on this post', 'coworker' )
		),
        array(
            'name' => __( 'Comments System', 'coworker' ),
			'id' => $prefix . 'comments_system',
			'type' => 'select',
			'options' => array(
				'themeoption' => __( 'Theme Options', 'coworker' ),
				'wp' => __( 'Wordpress', 'coworker' ),
				'disqus' => __( 'Disqus', 'coworker' ),
				'facebook' => __( 'Facebook', 'coworker' ),
                'gplus' => __( 'Google+ Comments', 'coworker' )
			),
			'std'  => array( 'themeoption' ),
			'desc' => __( 'Select the Comment System for this Post', 'coworker' )
		)
        
    )
);

$pometa_boxes[] = array(
	'id' => 'postgallery',
	'title' => __( 'Gallery Settings', 'coworker' ),
	'pages' => array( 'post' ),
	'context' => 'normal',
	'priority' => 'high',
	'fields' => array(
		
        array(
			'name'  => __( 'Gallery', 'coworker' ),
			'desc' => __( 'Additional Images for your Gallery. Maximum 10 Images', 'coworker' ),
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


$pometa_boxes[] = array(
	'id' => 'postaudio',
	'title' => __( 'Audio Settings', 'coworker' ),
	'pages' => array( 'post' ),
	'context' => 'normal',
	'priority' => 'high',
	'fields' => array(
	
        array(
            'name' => __( 'MP3 File', 'coworker' ),
            'id' => $prefix . 'audio_mp3',
            'type' => 'file_advanced',
            'max_file_uploads' => 1,
            'mime_type' => 'audio',
            'desc' => __( 'Upload a MP3 File for your Native Audio', 'coworker' )
        ),
        array(
            'name' => __( 'OGG File', 'coworker' ),
            'id' => $prefix . 'audio_ogg',
            'type' => 'file_advanced',
            'max_file_uploads' => 1,
            'mime_type' => 'audio',
            'desc' => __( 'Upload an OGG File for your Native Audio', 'coworker' )
        ),
        array(
            'name' => __( 'WAV File', 'coworker' ),
            'id' => $prefix . 'audio_wav',
            'type' => 'file_advanced',
            'max_file_uploads' => 1,
            'mime_type' => 'audio',
            'desc' => __( 'Upload a WAV File for your Native Audio', 'coworker' )
        )
    
    )
);


$pometa_boxes[] = array(
	'id' => 'postmedia',
	'title' => __( 'Media Settings', 'coworker' ),
	'pages' => array( 'post' ),
	'context' => 'normal',
	'priority' => 'high',
	'fields' => array(
		
        array(
			'name' => __( 'Video/Audio Embed Code', 'chthemes' ),
			'desc' => __( 'Please Enter your Video/Audio Embed Code.', 'coworker' ),
			'id'   => $prefix . 'embed',
			'type' => 'textarea',
			'std'  => '',
			'cols' => '40',
			'rows' => '6'
		)
        
    )
);


function semi_post_register_meta_boxes() {

    global $pometa_boxes;
    
	if ( class_exists( 'RW_Meta_Box' ) ) {
	
        foreach ( $pometa_boxes as $pometa_box ) {
			new RW_Meta_Box( $pometa_box );
		}
    
	}

}

add_action( 'admin_init', 'semi_post_register_meta_boxes' );

?>