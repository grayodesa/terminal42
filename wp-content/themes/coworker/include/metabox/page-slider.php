<?php

$prefix = 'semi_page_slider_';

$easingops = get_easing_ops();

global $pageslidermeta_boxes;

$pageslidermeta_boxes = array();

$pageslidermeta_boxes[] = array(
	'id' => 'pageslider',
	'title' => __( 'Slider', 'coworker' ),
	'pages' => array( 'page' ),
	'context' => 'normal',
	'priority' => 'high',
	'fields' => array(
		
        array(
            'name' => __( 'Choose Slider', 'coworker' ),
			'id' => 'semi_page_slider',
            'type' => 'select',
			'options' => array(
				'flex' => 'Flex Slider',
                'nivo' => 'Nivo Slider',
                'refine' => 'Refine Slide',
                'elastic' => 'Elastic Slider',
                '3d' => '3D Slider',
                'accordion' => 'Accordion Slider',
                'camera' => 'Camera Slider',
                'layer' => 'Layer Slider',
                'revolution' => 'Revolution Slider',
                'image' => 'Static Image',
                'video' => 'Static Video'
			),
			'std'  => array( 'flex' ),
			'desc' => __( 'Select a Slider and <strong>Update</strong> the Page to see the Slider Settings.', 'coworker' )
		)
        
    ),
	'only_on'    => array(
		'template' => array( 'template-slider.php', 'template-slider-sidebar.php' )
	)
);


$pageslidermeta_boxes[] = array(
	'id' => 'flexslideroptions',
	'title' => __( 'Flex Slider Settings', 'coworker' ),
	'pages' => array( 'page' ),
	'context' => 'normal',
	'priority' => 'high',
	'fields' => array(
		
        array(
			'name' => __( 'Slider Category', 'coworker' ),
			'id'   => $prefix . 'category',
			'type'    => 'taxonomy',
			'options' => array(
				'taxonomy' => 'slider-group',
				'type' => 'select'
			),
			'desc' => __( 'Choose a Slider Group.', 'coworker' )
		),
        array(
            'name' => __( 'Slider Height', 'coworker' ),
			'id' => $prefix . 'height',
			'desc' => __( 'Set the Slider Height. Eg. 400', 'coworker' ),
			'type'  => 'text',
			'std' => '',
            'size' => '3'
		),
        array(
            'name' => __( 'No. of Slides', 'coworker' ),
			'id' => $prefix . 'items',
			'desc' => __( 'Enter the number of items you want to show in your Slider. If you enter "-1", all the slider items will be retrieved.', 'coworker' ),
			'type'  => 'text',
			'std' => '6',
            'size' => '2'
		),
        array(
            'name' => __( 'Slide Order', 'coworker' ),
			'id' => $prefix . 'order',
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
			'id' => $prefix . 'orderby',
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
			'id' => $prefix . 'animation',
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
			'id' => $prefix . 'direction',
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
			'id' => $prefix . 'easing',
            'type' => 'select',
			'options' => $easingops,
			'std'  => array( 'easeOutExpo' ),
			'desc' => __( 'Select the Slider Easing Animation.', 'coworker' )
		),
        array(
            'name' => __( 'Auto Slideshow', 'coworker' ),
			'id' => $prefix . 'auto',
			'type' => 'checkbox',
			'desc' => __( 'Check to enable auto Slideshow.', 'coworker' ),
			'std' => 1
		),
        array(
            'name' => __( 'Pause Time', 'coworker' ),
			'id' => $prefix . 'pause',
			'desc' => __( 'Enter the Pause Time between Slideshow in milliseconds. <strong>Eg.</strong> For 4 Seconds, enter <strong>4000</strong>', 'coworker' ),
			'type'  => 'text',
			'std' => '4000',
            'size' => '3'
		),
        array(
            'name' => __( 'Animation Speed', 'coworker' ),
			'id' => $prefix . 'speed',
			'desc' => __( 'Enter the Slider Animation Speed in milliseconds. <strong>Eg.</strong> For .50 Seconds, enter <strong>500</strong>', 'coworker' ),
			'type'  => 'text',
			'std' => '500',
            'size' => '3'
		),
        array(
            'name' => __( 'Pause on Hover', 'coworker' ),
			'id' => $prefix . 'hover',
			'type' => 'checkbox',
			'desc' => __( 'Check to Pause Animation on Mouse Hover.', 'coworker' ),
			'std' => 1
		),
        array(
            'name' => __( 'Navigation', 'coworker' ),
			'id' => $prefix . 'arrows',
			'type' => 'checkbox',
			'desc' => __( 'Check to show Navigation Arrows.', 'coworker' ),
			'std' => 1
		),
        array(
            'name' => __( 'Thumbs', 'coworker' ),
			'id' => $prefix . 'thumbs',
			'type' => 'checkbox',
			'desc' => __( 'Check to show Thumbnails.', 'coworker' ),
			'std' => 0
		)
        
    ),
	'only_on'    => array(
		'meta' => array( 'key' => 'semi_page_slider', 'value' => 'flex' )
	)
);


$pageslidermeta_boxes[] = array(
	'id' => 'refineslideroptions',
	'title' => __( 'Refine Slider Settings', 'coworker' ),
	'pages' => array( 'page' ),
	'context' => 'normal',
	'priority' => 'high',
	'fields' => array(
		
        array(
			'name' => __( 'Slider Category', 'coworker' ),
			'id'   => $prefix . 'category',
			'type'    => 'taxonomy',
			'options' => array(
				'taxonomy' => 'slider-group',
				'type' => 'select'
			),
			'desc' => __( 'Choose a Slider Group.', 'coworker' )
		),
        array(
            'name' => __( 'Slider Height', 'coworker' ),
			'id' => $prefix . 'height',
			'desc' => __( 'Set the Slider Height. Eg. 400', 'coworker' ),
			'type'  => 'text',
			'std' => '',
            'size' => '3'
		),
        array(
            'name' => __( 'No. of Slides', 'coworker' ),
			'id' => $prefix . 'items',
			'desc' => __( 'Enter the number of items you want to show in your Slider. If you enter "-1", all the slider items will be retrieved.', 'coworker' ),
			'type'  => 'text',
			'std' => '6',
            'size' => '2'
		),
        array(
            'name' => __( 'Slide Order', 'coworker' ),
			'id' => $prefix . 'order',
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
			'id' => $prefix . 'orderby',
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
			'id' => $prefix . 'animation',
            'type' => 'select',
			'options' => array(
				'random' => 'random',
                'cubeH' => 'cubeH',
                'cubeV' => 'cubeV',
                'fade' => 'fade',
                'sliceH' => 'sliceH',
                'sliceV' => 'sliceV',
                'slideH' => 'slideH',
                'slideV' => 'slideV',
                'scale' => 'scale',
                'blockScale' => 'blockScale',
                'kaleidoscope' => 'kaleidoscope',
                'fan' => 'fan',
                'blindH' => 'blindH',
                'blindV' => 'blindV'
            ),
			'std'  => array( 'random' ),
			'desc' => __( 'Select the Slider Animation.', 'coworker' )
		),
        array(
            'name' => __( 'Auto Slideshow', 'coworker' ),
			'id' => $prefix . 'auto',
			'type' => 'checkbox',
			'desc' => __( 'Check to enable auto Slideshow.', 'coworker' ),
			'std' => 1
		),
        array(
            'name' => __( 'Pause Time', 'coworker' ),
			'id' => $prefix . 'pause',
			'desc' => __( 'Enter the Pause Time between Slideshow in milliseconds. <strong>Eg.</strong> For 4 Seconds, enter <strong>4000</strong>', 'coworker' ),
			'type'  => 'text',
			'std' => '4000',
            'size' => '3'
		),
        array(
            'name' => __( 'Animation Speed', 'coworker' ),
			'id' => $prefix . 'speed',
			'desc' => __( 'Enter the Slider Animation Speed in milliseconds. <strong>Eg.</strong> For .50 Seconds, enter <strong>500</strong>', 'coworker' ),
			'type'  => 'text',
			'std' => '500',
            'size' => '3'
		),
        array(
            'name' => __( 'Navigation', 'coworker' ),
			'id' => $prefix . 'arrows',
			'type' => 'checkbox',
			'desc' => __( 'Check to show Navigation Arrows.', 'coworker' ),
			'std' => 1
		),
        array(
            'name' => __( 'Thumbs', 'coworker' ),
			'id' => $prefix . 'thumbs',
			'type' => 'checkbox',
			'desc' => __( 'Check to show Thumbnails.', 'coworker' ),
			'std' => 0
		)
        
    ),
	'only_on'    => array(
		'meta' => array( 'key' => 'semi_page_slider', 'value' => 'refine' )
	)
);


$pageslidermeta_boxes[] = array(
	'id' => 'nivoslideroptions',
	'title' => __( 'Nivo Slider Settings', 'coworker' ),
	'pages' => array( 'page' ),
	'context' => 'normal',
	'priority' => 'high',
	'fields' => array(
		
        array(
			'name' => __( 'Slider Category', 'coworker' ),
			'id'   => $prefix . 'category',
			'type'    => 'taxonomy',
			'options' => array(
				'taxonomy' => 'slider-group',
				'type' => 'select'
			),
			'desc' => __( 'Choose a Slider Group.', 'coworker' )
		),
        array(
            'name' => __( 'Slider Height', 'coworker' ),
			'id' => $prefix . 'height',
			'desc' => __( 'Set the Slider Height. Eg. 400', 'coworker' ),
			'type'  => 'text',
			'std' => '',
            'size' => '3'
		),
        array(
            'name' => __( 'No. of Slides', 'coworker' ),
			'id' => $prefix . 'items',
			'desc' => __( 'Enter the number of items you want to show in your Slider. If you enter "-1", all the slider items will be retrieved.', 'coworker' ),
			'type'  => 'text',
			'std' => '6',
            'size' => '2'
		),
        array(
            'name' => __( 'Slide Order', 'coworker' ),
			'id' => $prefix . 'order',
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
			'id' => $prefix . 'orderby',
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
			'id' => $prefix . 'animation',
            'type' => 'select',
			'options' => array(
				'sliceDown' => 'sliceDown',
                'sliceDownLeft' => 'sliceDownLeft',
                'sliceUp' => 'sliceUp',
                'sliceUpLeft' => 'sliceUpLeft',
                'sliceUpDown' => 'sliceUpDown',
                'sliceUpDownLeft' => 'sliceUpDownLeft',
                'fold' => 'fold',
                'fade' => 'fade',
                'random' => 'random',
                'slideInRight' => 'slideInRight',
                'slideInLeft' => 'slideInLeft',
                'boxRandom' => 'boxRandom',
                'boxRain' => 'boxRain',
                'boxRainReverse' => 'boxRainReverse',
                'boxRainGrow' => 'boxRainGrow',
                'boxRainGrowReverse' => 'boxRainGrowReverse'
			),
			'std'  => array( 'random' ),
			'desc' => __( 'Select the Slider Animation.', 'coworker' )
		),
        array(
            'name' => __( 'Pause Time', 'coworker' ),
			'id' => $prefix . 'pause',
			'desc' => __( 'Enter the Pause Time between Slideshow in milliseconds. <strong>Eg.</strong> For 4 Seconds, enter <strong>4000</strong>', 'coworker' ),
			'type'  => 'text',
			'std' => '4000',
            'size' => '3'
		),
        array(
            'name' => __( 'Animation Speed', 'coworker' ),
			'id' => $prefix . 'speed',
			'desc' => __( 'Enter the Slider Animation Speed in milliseconds. <strong>Eg.</strong> For .50 Seconds, enter <strong>500</strong>', 'coworker' ),
			'type'  => 'text',
			'std' => '500',
            'size' => '3'
		),
        array(
            'name' => __( 'Pause on Hover', 'coworker' ),
			'id' => $prefix . 'hover',
			'type' => 'checkbox',
			'desc' => __( 'Check to Pause Animation on Mouse Hover.', 'coworker' ),
			'std' => 1
		),
        array(
            'name' => __( 'Navigation', 'coworker' ),
			'id' => $prefix . 'arrows',
			'type' => 'checkbox',
			'desc' => __( 'Check to show Navigation Arrows.', 'coworker' ),
			'std' => 1
		)
        
    ),
	'only_on'    => array(
		'meta' => array( 'key' => 'semi_page_slider', 'value' => 'nivo' )
	)
);


$pageslidermeta_boxes[] = array(
	'id' => 'accordionslideroptions',
	'title' => __( 'Accordion Slider Settings', 'coworker' ),
	'pages' => array( 'page' ),
	'context' => 'normal',
	'priority' => 'high',
	'fields' => array(
		
        array(
			'name' => __( 'Slider Category', 'coworker' ),
			'id'   => $prefix . 'category',
			'type'    => 'taxonomy',
			'options' => array(
				'taxonomy' => 'slider-group',
				'type' => 'select'
			),
			'desc' => __( 'Choose a Slider Group.', 'coworker' )
		),
        array(
            'name' => __( 'Slider Height', 'coworker' ),
			'id' => $prefix . 'height',
			'desc' => __( 'Set the Slider Height. Eg. 400', 'coworker' ),
			'type'  => 'text',
			'std' => '',
            'size' => '3'
		),
        array(
            'name' => __( 'No. of Panels', 'coworker' ),
			'id' => $prefix . 'panels',
            'type' => 'select',
			'options' => array(
				'2' => '2',
				'3' => '3',
				'4' => '4',
				'5' => '5'
			),
			'std'  => array( '5' ),
			'desc' => __( 'Select the number of Accordion Panels.', 'coworker' )
		),
        array(
            'name' => __( 'Slide Order', 'coworker' ),
			'id' => $prefix . 'order',
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
			'id' => $prefix . 'orderby',
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
            'name' => __( 'Easing', 'coworker' ),
			'id' => $prefix . 'easing',
            'type' => 'select',
			'options' => $easingops,
			'std'  => array( 'easeOutExpo' ),
			'desc' => __( 'Select the Slider Easing Animation.', 'coworker' )
		),
        array(
            'name' => __( 'Auto Slideshow', 'coworker' ),
			'id' => $prefix . 'auto',
			'type' => 'checkbox',
			'desc' => __( 'Check to enable auto Slideshow.', 'coworker' ),
			'std' => 1
		),
        array(
            'name' => __( 'Pause Time', 'coworker' ),
			'id' => $prefix . 'pause',
			'desc' => __( 'Enter the Pause Time between Slideshow in milliseconds. <strong>Eg.</strong> For 4 Seconds, enter <strong>4000</strong>', 'coworker' ),
			'type'  => 'text',
			'std' => '4000',
            'size' => '3'
		),
        array(
            'name' => __( 'Animation Speed', 'coworker' ),
			'id' => $prefix . 'speed',
			'desc' => __( 'Enter the Slider Animation Speed in milliseconds. <strong>Eg.</strong> For .50 Seconds, enter <strong>500</strong>', 'coworker' ),
			'type'  => 'text',
			'std' => '500',
            'size' => '3'
		),
        array(
			'name' => 'Fallback Image',
			'id' => $prefix . 'fallback',
			'type' => 'image_advanced',
			'max_file_uploads' => 1,
			'desc' => __( 'Enter a Fallback Image for your Accordion Slider for the Responsive Version as it is not supported in the Responsive Layout.', 'coworker' )
		)
        
    ),
	'only_on'    => array(
		'meta' => array( 'key' => 'semi_page_slider', 'value' => 'accordion' )
	)
);


$pageslidermeta_boxes[] = array(
	'id' => 'elasticslideroptions',
	'title' => __( 'Elastic Slider Settings', 'coworker' ),
	'pages' => array( 'page' ),
	'context' => 'normal',
	'priority' => 'high',
	'fields' => array(
		
        array(
			'name' => __( 'Slider Category', 'coworker' ),
			'id'   => $prefix . 'category',
			'type'    => 'taxonomy',
			'options' => array(
				'taxonomy' => 'slider-group',
				'type' => 'select'
			),
			'desc' => __( 'Choose a Slider Group.', 'coworker' )
		),
        array(
            'name' => __( 'Slider Height', 'coworker' ),
			'id' => $prefix . 'height',
			'desc' => __( 'Set the Slider Height. Eg. 400', 'coworker' ),
			'type'  => 'text',
			'std' => '',
            'size' => '3'
		),
        array(
            'name' => __( 'No. of Slides', 'coworker' ),
			'id' => $prefix . 'items',
			'desc' => __( 'Enter the number of items you want to show in your Slider. If you enter "-1", all the slider items will be retrieved.', 'coworker' ),
			'type'  => 'text',
			'std' => '6',
            'size' => '2'
		),
        array(
            'name' => __( 'Slide Order', 'coworker' ),
			'id' => $prefix . 'order',
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
			'id' => $prefix . 'orderby',
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
			'id' => $prefix . 'animation',
            'type' => 'select',
			'options' => array(
				'sides' => 'Sides',
                'center' => 'Center'
            ),
			'std'  => array( 'sides' ),
			'desc' => __( 'Select the Slider Animation.', 'coworker' )
		),
        array(
            'name' => __( 'Easing', 'coworker' ),
			'id' => $prefix . 'easing',
            'type' => 'select',
			'options' => $easingops,
			'std'  => array( 'easeOutExpo' ),
			'desc' => __( 'Select the Slider Easing Animation.', 'coworker' )
		),
        array(
            'name' => __( 'Auto Slideshow', 'coworker' ),
			'id' => $prefix . 'auto',
			'type' => 'checkbox',
			'desc' => __( 'Check to enable auto Slideshow.', 'coworker' ),
			'std' => 1
		),
        array(
            'name' => __( 'Pause Time', 'coworker' ),
			'id' => $prefix . 'pause',
			'desc' => __( 'Enter the Pause Time between Slideshow in milliseconds. <strong>Eg.</strong> For 4 Seconds, enter <strong>4000</strong>', 'coworker' ),
			'type'  => 'text',
			'std' => '4000',
            'size' => '3'
		),
        array(
            'name' => __( 'Animation Speed', 'coworker' ),
			'id' => $prefix . 'speed',
			'desc' => __( 'Enter the Slider Animation Speed in milliseconds. <strong>Eg.</strong> For .50 Seconds, enter <strong>500</strong>', 'coworker' ),
			'type'  => 'text',
			'std' => '500',
            'size' => '3'
		)
        
    ),
	'only_on'    => array(
		'meta' => array( 'key' => 'semi_page_slider', 'value' => 'elastic' )
	)
);


$pageslidermeta_boxes[] = array(
	'id' => 'cameraslideroptions',
	'title' => __( 'Camera Slider Settings', 'coworker' ),
	'pages' => array( 'page' ),
	'context' => 'normal',
	'priority' => 'high',
	'fields' => array(
		
        array(
			'name' => __( 'Slider Category', 'coworker' ),
			'id'   => $prefix . 'category',
			'type'    => 'taxonomy',
			'options' => array(
				'taxonomy' => 'slider-group',
				'type' => 'select'
			),
			'desc' => __( 'Choose a Slider Group.', 'coworker' )
		),
        array(
            'name' => __( 'Slider Height', 'coworker' ),
			'id' => $prefix . 'sliderheight',
			'desc' => __( 'Set the Slider Height or Percentage. Eg. 400px or 40%. Include "px" or "%"', 'coworker' ),
			'type'  => 'text',
			'std' => '',
            'size' => '3'
		),
        array(
            'name' => __( 'Image Height', 'coworker' ),
			'id' => $prefix . 'height',
			'desc' => __( 'Set the Image Height. Eg. 400', 'coworker' ),
			'type'  => 'text',
			'std' => '',
            'size' => '3'
		),
        array(
            'name' => __( 'No. of Slides', 'coworker' ),
			'id' => $prefix . 'items',
			'desc' => __( 'Enter the number of items you want to show in your Slider. If you enter "-1", all the slider items will be retrieved.', 'coworker' ),
			'type'  => 'text',
			'std' => '6',
            'size' => '2'
		),
        array(
            'name' => __( 'Slide Order', 'coworker' ),
			'id' => $prefix . 'order',
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
			'id' => $prefix . 'orderby',
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
			'id' => $prefix . 'animation',
            'type' => 'select',
			'options' => array(
				'random' => __( 'Random', 'coworker' ),
				'simpleFade' => 'simpleFade',
                'curtainTopLeft' => 'curtainTopLeft',
                'curtainTopRight' => 'curtainTopRight',
                'curtainBottomLeft' => 'curtainBottomLeft',
                'curtainBottomRight' => 'curtainBottomRight',
                'curtainSliceLeft' => 'curtainSliceLeft',
                'curtainSliceRight' => 'curtainSliceRight',
                'blindCurtainTopLeft' => 'blindCurtainTopLeft',
                'blindCurtainTopRight' => 'blindCurtainTopRight',
                'blindCurtainBottomLeft' => 'blindCurtainBottomLeft',
                'blindCurtainBottomRight' => 'blindCurtainBottomRight',
                'blindCurtainSliceBottom' => 'blindCurtainSliceBottom',
                'blindCurtainSliceTop' => 'blindCurtainSliceTop',
                'stampede' => 'stampede',
                'mosaic' => 'mosaic',
                'mosaicReverse' => 'mosaicReverse',
                'mosaicRandom' => 'mosaicRandom',
                'mosaicSpiral' => 'mosaicSpiral',
                'mosaicSpiralReverse' => 'mosaicSpiralReverse',
                'topLeftBottomRight' => 'topLeftBottomRight',
                'bottomRightTopLeft' => 'bottomRightTopLeft',
                'bottomLeftTopRight' => 'bottomLeftTopRight',
                'scrollLeft' => 'scrollLeft',
                'scrollRight' => 'scrollRight',
                'scrollHorz' => 'scrollHorz',
                'scrollBottom' => 'scrollBottom',
                'scrollTop' => 'scrollTop'
			),
			'std'  => array( 'slide' ),
			'desc' => __( 'Select the Slider Animation.', 'coworker' )
		),
        array(
            'name' => __( 'Easing', 'coworker' ),
			'id' => $prefix . 'easing',
            'type' => 'select',
			'options' => $easingops,
			'std'  => array( 'easeOutExpo' ),
			'desc' => __( 'Select the Slider Easing Animation.', 'coworker' )
		),
        array(
            'name' => __( 'Auto Slideshow', 'coworker' ),
			'id' => $prefix . 'auto',
			'type' => 'checkbox',
			'desc' => __( 'Check to enable auto Slideshow.', 'coworker' ),
			'std' => 1
		),
        array(
            'name' => __( 'Pause Time', 'coworker' ),
			'id' => $prefix . 'pause',
			'desc' => __( 'Enter the Pause Time between Slideshow in milliseconds. <strong>Eg.</strong> For 4 Seconds, enter <strong>4000</strong>', 'coworker' ),
			'type'  => 'text',
			'std' => '4000',
            'size' => '3'
		),
        array(
            'name' => __( 'Animation Speed', 'coworker' ),
			'id' => $prefix . 'speed',
			'desc' => __( 'Enter the Slider Animation Speed in milliseconds. <strong>Eg.</strong> For .50 Seconds, enter <strong>500</strong>', 'coworker' ),
			'type'  => 'text',
			'std' => '500',
            'size' => '3'
		),
        array(
            'name' => __( 'Pause on Hover', 'coworker' ),
			'id' => $prefix . 'hover',
			'type' => 'checkbox',
			'desc' => __( 'Check to Pause Animation on Mouse Hover.', 'coworker' ),
			'std' => 1
		),
        array(
            'name' => __( 'Loader Style', 'coworker' ),
			'id' => $prefix . 'loader',
            'type' => 'select',
			'options' => array(
				'pie' => __( 'Pie', 'coworker' ),
				'bar' => __( 'Bar', 'coworker' ),
				'none' => __( 'None', 'coworker' )
			),
			'std'  => array( 'bar' ),
			'desc' => __( 'Select the Slider\'s Loader Style.', 'coworker' )
		),
        array(
            'name' => __( 'Navigation', 'coworker' ),
			'id' => $prefix . 'arrows',
			'type' => 'checkbox',
			'desc' => __( 'Check to show Navigation Arrows.', 'coworker' ),
			'std' => 1
		),
        array(
            'name' => __( 'Thumbs', 'coworker' ),
			'id' => $prefix . 'thumbs',
			'type' => 'checkbox',
			'desc' => __( 'Check to show Thumbnails.', 'coworker' ),
			'std' => 0
		),
        array(
            'name' => __( 'Pagination', 'coworker' ),
			'id' => $prefix . 'pagination',
			'type' => 'checkbox',
			'desc' => __( 'Check to show Pagination.', 'coworker' ),
			'std' => 0
		)
        
    ),
	'only_on'    => array(
		'meta' => array( 'key' => 'semi_page_slider', 'value' => 'camera' )
	)
);


$pageslidermeta_boxes[] = array(
	'id' => 'slider3doptions',
	'title' => __( '3D Slider Settings', 'coworker' ),
	'pages' => array( 'page' ),
	'context' => 'normal',
	'priority' => 'high',
	'fields' => array(
		
        array(
			'name' => __( 'Slider Category', 'coworker' ),
			'id'   => $prefix . 'category',
			'type'    => 'taxonomy',
			'options' => array(
				'taxonomy' => 'slider-group',
				'type' => 'select'
			),
			'desc' => __( 'Choose a Slider Group.', 'coworker' )
		),
        array(
            'name' => __( 'No. of Slides', 'coworker' ),
			'id' => $prefix . 'items',
			'desc' => __( 'Enter the number of items you want to show in your Slider. If you enter "-1", all the slider items will be retrieved.', 'coworker' ),
			'type'  => 'text',
			'std' => '6',
            'size' => '2'
		),
        array(
            'name' => __( 'Slide Order', 'coworker' ),
			'id' => $prefix . 'order',
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
			'id' => $prefix . 'orderby',
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
            'name' => __( 'Auto Slideshow', 'coworker' ),
			'id' => $prefix . 'auto',
			'type' => 'checkbox',
			'desc' => __( 'Check to enable auto Slideshow.', 'coworker' ),
			'std' => 1
		),
        array(
            'name' => __( 'Show Menu', 'coworker' ),
			'id' => $prefix . 'menu',
			'type' => 'checkbox',
			'desc' => __( 'Check to show Menu.', 'coworker' ),
			'std' => 1
		),
        array(
			'name' => 'Fallback Image',
			'id' => $prefix . 'fallback',
			'type' => 'image_advanced',
			'max_file_uploads' => 1,
			'desc' => __( 'Enter a Fallback Image for your 3D Slider for the Responsive Version as it is not supported in the Responsive Layout.', 'coworker' )
		)
        
    ),
	'only_on'    => array(
		'meta' => array( 'key' => 'semi_page_slider', 'value' => '3d' )
	)
);


$pageslidermeta_boxes[] = array(
	'id' => 'staticimageoptions',
	'title' => __( 'Static Image Settings', 'coworker' ),
	'pages' => array( 'page' ),
	'context' => 'normal',
	'priority' => 'high',
	'fields' => array(
		
        array(
            'name' => __( 'Image Height', 'coworker' ),
			'id' => $prefix . 'height',
			'desc' => __( 'Set the Image Height. Eg. 400', 'coworker' ),
			'type'  => 'text',
			'std' => '',
            'size' => '3'
		),
        array(
            'name' => __( 'URL', 'coworker' ),
			'id' => $prefix . 'static_img_url',
			'desc' => __( 'The Static Image\'s Link Address. Include http://', 'coworker' ),
			'type'  => 'text',
			'std' => 'http://',
            'size' => '100'
		),
        array(
			'name' => __( 'URL Target', 'coworker' ),
			'id'   => $prefix . 'static_img_target',
			'type' => 'select',
			'options' => array(
				'_self' => __( 'Same Window', 'coworker' ),
				'_blank' => __( 'New Window', 'coworker' )
			),
			'std'  => array( '' ),
			'desc' => __( 'Select the URL Target.', 'coworker' )
		)
        
    ),
	'only_on'    => array(
		'meta' => array( 'key' => 'semi_page_slider', 'value' => 'image' )
	)
);


$pageslidermeta_boxes[] = array(
	'id' => 'staticvideooptions',
	'title' => __( 'Static Video Settings', 'coworker' ),
	'pages' => array( 'page' ),
	'context' => 'normal',
	'priority' => 'high',
	'fields' => array(
		
        array(
			'name' => __( 'Video Embed Code', 'coworker' ),
			'desc' => __( 'Please Enter your Video Embed Code. Make sure your Video\'s Width is 1020px. You can choose your desired Height.', 'coworker' ),
			'id'   => $prefix . 'static_video',
			'type' => 'textarea',
			'std'  => '',
			'cols' => '40',
			'rows' => '6'
		)
        
    ),
	'only_on'    => array(
		'meta' => array( 'key' => 'semi_page_slider', 'value' => 'video' )
	)
);


$pageslidermeta_boxes[] = array(
	'id' => 'revslideroptions',
	'title' => __( 'Revolution Slider Settings', 'coworker' ),
	'pages' => array( 'page' ),
	'context' => 'normal',
	'priority' => 'high',
	'fields' => array(
		
        array(
            'name' => __( 'Slider Shortcode', 'coworker' ),
			'id' => $prefix . 'rev_slider',
			'desc' => __( 'Enter your Revolution Slider Shortcode here.', 'coworker' ),
			'type'  => 'text',
			'std' => '',
            'size' => '100'
		)
        
    ),
	'only_on'    => array(
		'meta' => array( 'key' => 'semi_page_slider', 'value' => 'revolution' )
	)
);


$pageslidermeta_boxes[] = array(
	'id' => 'layerslideroptions',
	'title' => __( 'Layer Slider Settings', 'coworker' ),
	'pages' => array( 'page' ),
	'context' => 'normal',
	'priority' => 'high',
	'fields' => array(
		
        array(
            'name' => __( 'Slider Shortcode', 'coworker' ),
			'id' => $prefix . 'layer_slider',
			'desc' => __( 'Enter your Layer Slider Shortcode here.', 'coworker' ),
			'type'  => 'text',
			'std' => '',
            'size' => '100'
		)
        
    ),
	'only_on'    => array(
		'meta' => array( 'key' => 'semi_page_slider', 'value' => 'layer' )
	)
);


$pageslidermeta_boxes[] = array(
	'id' => 'sliderbackground',
	'title' => __( 'Slider Background Settings', 'coworker' ),
	'pages' => array( 'page' ),
	'context' => 'normal',
	'priority' => 'high',
	'fields' => array(
		
        array(
			'name'  => __( 'Background Pattern', 'coworker' ),
			'desc' => __( 'Add a Background Pattern for your Slider', 'coworker' ),
			'id' => $prefix . 'bg_pattern',
			'type' => 'image_advanced',
			'max_file_uploads' => 1
		),
        array(
            'name' => __( 'Background Color', 'coworker' ),
			'id' => $prefix . 'bg_color',
			'type' => 'color',
			'desc' => __( 'Choose your Slider\'s Background Color', 'coworker' )
		)
        
    ),
	'only_on'    => array(
		'template' => array( 'template-slider.php', 'template-slider-sidebar.php' )
	)
);


function semi_page_slider_register_meta_boxes() {

    global $pageslidermeta_boxes;

	// Make sure there's no errors when the plugin is deactivated or during upgrade
	if ( class_exists( 'RW_Meta_Box' ) ) {
		foreach ( $pageslidermeta_boxes as $pageslidermeta_box ) {
			if ( isset( $pageslidermeta_box['only_on'] ) && ! rw_maybe_include( $pageslidermeta_box['only_on'] ) ) {
				continue;
			}

			new RW_Meta_Box( $pageslidermeta_box );
		}
	}

}

add_action( 'admin_init', 'semi_page_slider_register_meta_boxes' );


?>