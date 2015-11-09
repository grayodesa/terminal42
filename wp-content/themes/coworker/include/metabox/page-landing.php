<?php

$prefix = 'semi_page_landing_';

global $pagelandingmeta_boxes;

$pagelandingmeta_boxes = array();


$pagelandingmeta_boxes[] = array(
	'id' => 'landinglayoutsettings',
	'title' => __( 'Landing Area Layout', 'coworker' ),
	'pages' => array( 'page' ),
	'context' => 'normal',
	'priority' => 'high',
	'fields' => array(
		
        array(
            'name' => __( 'Layout', 'coworker' ),
			'id' => $prefix . 'layout',
            'type' => 'select',
			'options' => array(
				'lmrt' => 'Left Media / Right Text',
				'rmlt' => 'Right Media / Left Text',
				'hlmrt' => 'Half - Left Media / Right Text',
				'hrmlt' => 'Half - Right Media / Left Text',
				'lmrc' => 'Left Media / Right Custom',
				'rmlc' => 'Right Media / Left Custom',
				'hlmrc' => 'Half - Left Media / Right Custom',
				'hrmlc' => 'Half - Right Media / Left Custom'
                
			),
			'std'  => array( 'lmrt' ),
			'desc' => __( 'Choose the Landing Area Layout.', 'coworker' )
		)
        
    ),
	'only_on'    => array(
		'template' => array( 'template-landing.php' )
	)
);


$pagelandingmeta_boxes[] = array(
	'id' => 'landingtextsettings',
	'title' => __( 'Landing Area Text', 'coworker' ),
	'pages' => array( 'page' ),
	'context' => 'normal',
	'priority' => 'high',
	'fields' => array(
		
        array(
            'name' => __( 'Landing Title', 'coworker' ),
			'id' => $prefix . 'title',
			'desc' => __( 'Landing Area Title.', 'coworker' ),
			'type'  => 'text',
			'std' => '',
            'size' => '100'
		),
        array(
			'name' => __( 'Landing Description', 'coworker' ),
			'desc' => __( 'Short Description for your Offer. Preferably 15-20 Words', 'coworker' ),
			'id'   => $prefix . 'description',
			'type' => 'textarea',
			'cols' => '40',
			'rows' => '3',
		),
        array(
			'name' => __( 'Features', 'coworker' ),
			'desc' => __( 'Features of your Offer.<br><strong>Eg.</strong><br>&lt;li&gt;Feature 1&lt;/li&gt;<br>&lt;li&gt;Feature 2&lt;/li&gt;<br>&lt;li&gt;Feature 3&lt;/li&gt;<br>and so on... <br>You can also Enter Icons.<br><strong>Eg.</strong><br>&lt;li&gt;&lt;i class="icon-map-marker"&gt;&lt;/i&gt; Feature 1&lt;/li&gt;<br>For full List of Icons, please refer to Documentation.', 'coworker' ),
			'id'   => $prefix . 'features',
			'type' => 'textarea',
			'cols' => '40',
			'rows' => '8',
		),
        array(
			'name' => __( 'Action Area', 'coworker' ),
			'desc' => __( 'Action Area of the Landing Page. You can Add Button Shortcodes or Links here.', 'coworker' ),
			'id'   => $prefix . 'action',
			'type' => 'textarea',
			'cols' => '40',
			'rows' => '8',
		)
        
    ),
	'only_on'    => array(
		'template' => array( 'template-landing.php' )
	)
);


$pagelandingmeta_boxes[] = array(
	'id' => 'landingmediasettings',
	'title' => __( 'Landing Area Media', 'coworker' ),
	'pages' => array( 'page' ),
	'context' => 'normal',
	'priority' => 'high',
	'fields' => array(
		
        array(
            'name' => __( 'Media Type', 'coworker' ),
			'id' => $prefix . 'media',
            'type' => 'select',
			'options' => array(
				'image' => 'Image',
				'video' => 'Embed Video',
				'slider' => 'Slider',
				'html' => 'HTML Content'
			),
			'std'  => array( 'image' ),
			'desc' => __( 'Select the Media Type. Please <strong>Update</strong> the Page after selecting your Option to display the required settings.', 'coworker' )
		)
        
    ),
	'only_on'    => array(
		'template' => array( 'template-landing.php' )
	)
);


$pagelandingmeta_boxes[] = array(
	'id' => 'landingmediasettingsimage',
	'title' => __( 'Landing Area Media - Image', 'coworker' ),
	'pages' => array( 'page' ),
	'context' => 'normal',
	'priority' => 'high',
	'fields' => array(
		
        array(
			'name'  => __( 'Upload Image', 'coworker' ),
			'desc' => __( 'Add an Image for your Landing Media Area.', 'coworker' ),
			'id' => $prefix . 'media_image',
			'type' => 'image_advanced',
			'max_file_uploads' => 1
		)
        
    ),
	'only_on'    => array(
		'meta' => array( 'key' => 'semi_page_landing_media', 'value' => 'image' )
	)
);


$pagelandingmeta_boxes[] = array(
	'id' => 'landingmediasettingsvideo',
	'title' => __( 'Landing Area Media - Video', 'coworker' ),
	'pages' => array( 'page' ),
	'context' => 'normal',
	'priority' => 'high',
	'fields' => array(
		
        array(
			'name' => __( 'Embed Video', 'coworker' ),
			'desc' => __( 'Enter your Embed Video for Landing Media Area.', 'coworker' ),
			'id'   => $prefix . 'media_video',
			'type' => 'textarea',
			'cols' => '40',
			'rows' => '4',
		)
        
    ),
	'only_on'    => array(
		'meta' => array( 'key' => 'semi_page_landing_media', 'value' => 'video' )
	)
);


$pagelandingmeta_boxes[] = array(
	'id' => 'landingmediasettingsslider',
	'title' => __( 'Landing Area Media - Slider', 'coworker' ),
	'pages' => array( 'page' ),
	'context' => 'normal',
	'priority' => 'high',
	'fields' => array(
		
        array(
			'name' => __( 'Slider Category', 'coworker' ),
			'id'   => $prefix . 'media_scategory',
			'type'    => 'taxonomy',
			'options' => array(
				'taxonomy' => 'slider-group',
				'type' => 'select'
			),
			'desc' => __( 'Choose a Slider Group.', 'coworker' )
		),
        array(
            'name' => __( 'No. of Slides', 'coworker' ),
			'id' => $prefix . 'media_sitems',
			'desc' => __( 'Enter the number of items you want to show in your Slider. If you enter "-1", all the slider items will be retrieved.', 'coworker' ),
			'type'  => 'text',
			'std' => '6',
            'size' => '2'
		),
        array(
            'name' => __( 'Slide Order', 'coworker' ),
			'id' => $prefix . 'media_sorder',
            'type' => 'select',
			'options' => array(
				'ASC' => 'ASC',
				'DESC' => 'DESC'
			),
			'std'  => array( 'DESC' ),
			'desc' => __( 'Select the Item Order.', 'coworker' )
		),
        array(
            'name' => __( 'Slide Order by', 'coworker' ),
			'id' => $prefix . 'media_sorderby',
            'type' => 'select',
			'options' => array(
				'ID' => 'ID',
				'title' => __( 'Title', 'coworker' ),
				'date' => __( 'Date', 'coworker' ),
				'rand' => __( 'Random', 'coworker' ),
				'menu_order' => __( 'Menu Order', 'coworker' )
			),
			'std'  => array( 'date' ),
			'desc' => __( 'Select the Item Order by condition.', 'coworker' )
		),
        array(
            'name' => __( 'Animation Type', 'coworker' ),
			'id' => $prefix . 'media_sanimation',
            'type' => 'select',
			'options' => array(
				'slide' => __( 'Slide', 'coworker' ),
				'fade' => __( 'Fade', 'coworker' )
			),
			'std'  => array( 'slide' ),
			'desc' => __( 'Select the Slider Animation.', 'coworker' )
		),
        array(
            'name' => __( 'Direction', 'coworker' ),
			'id' => $prefix . 'media_sdirection',
            'type' => 'select',
			'options' => array(
				'horizontal' => __( 'Horizontal', 'coworker' ),
				'vertical' => __( 'Vertical', 'coworker' )
			),
			'std'  => array( 'horizontal' ),
			'desc' => __( 'Select the Animation Direction.', 'coworker' )
		),
        array(
            'name' => __( 'Easing', 'coworker' ),
			'id' => $prefix . 'media_seasing',
            'type' => 'select',
			'options' => get_easing_ops(),
			'std'  => array( 'easeOutExpo' ),
			'desc' => __( 'Select the Slider Easing Animation.', 'coworker' )
		),
        array(
            'name' => __( 'Auto Slideshow', 'coworker' ),
			'id' => $prefix . 'media_sauto',
			'type' => 'checkbox',
			'desc' => __( 'Check to enable auto Slideshow.', 'coworker' ),
			'std' => 1
		),
        array(
            'name' => __( 'Pause Time', 'coworker' ),
			'id' => $prefix . 'media_spause',
			'desc' => __( 'Enter the Pause Time between Slideshow in milliseconds. <strong>Eg.</strong> For 4 Seconds, enter <strong>4000</strong>', 'coworker' ),
			'type'  => 'text',
			'std' => '4000',
            'size' => '3'
		),
        array(
            'name' => __( 'Animation Speed', 'coworker' ),
			'id' => $prefix . 'media_sspeed',
			'desc' => __( 'Enter the Slider Animation Speed in milliseconds. <strong>Eg.</strong> For .50 Seconds, enter <strong>500</strong>', 'coworker' ),
			'type'  => 'text',
			'std' => '500',
            'size' => '3'
		)
        
    ),
	'only_on'    => array(
		'meta' => array( 'key' => 'semi_page_landing_media', 'value' => 'slider' )
	)
);


$pagelandingmeta_boxes[] = array(
	'id' => 'landingmediasettingshtml',
	'title' => __( 'Landing Area Media - HTML Content', 'coworker' ),
	'pages' => array( 'page' ),
	'context' => 'normal',
	'priority' => 'high',
	'fields' => array(
		
        array(
			'name' => __( 'HTML Content', 'coworker' ),
			'desc' => __( 'Enter your HTML/Shortcode Content for Landing Media Area.', 'coworker' ),
			'id'   => $prefix . 'media_html',
			'type' => 'textarea',
			'cols' => '40',
			'rows' => '8',
		)
        
    ),
	'only_on'    => array(
		'meta' => array( 'key' => 'semi_page_landing_media', 'value' => 'html' )
	)
);


$pagelandingmeta_boxes[] = array(
	'id' => 'landingcustomsettings',
	'title' => __( 'Landing Area Custom Content', 'coworker' ),
	'pages' => array( 'page' ),
	'context' => 'normal',
	'priority' => 'high',
	'fields' => array(
		
        array(
			'name' => __( 'Content', 'coworker' ),
			'desc' => __( 'Enter your HTML/Shortcode Content for Landing Custom Content.', 'coworker' ),
			'id'   => $prefix . 'custom',
			'type' => 'textarea',
			'cols' => '40',
			'rows' => '8',
		)
        
    ),
	'only_on'    => array(
		'template' => array( 'template-landing.php' )
	)
);


function semi_pagelanding_register_meta_boxes() {

    global $pagelandingmeta_boxes;

	// Make sure there's no errors when the plugin is deactivated or during upgrade
	if ( class_exists( 'RW_Meta_Box' ) ) {
		foreach ( $pagelandingmeta_boxes as $pagelandingmeta_box ) {
			if ( isset( $pagelandingmeta_box['only_on'] ) && ! rw_maybe_include( $pagelandingmeta_box['only_on'] ) ) {
				continue;
			}

			new RW_Meta_Box( $pagelandingmeta_box );
		}
	}

}

add_action( 'admin_init', 'semi_pagelanding_register_meta_boxes' );


?>