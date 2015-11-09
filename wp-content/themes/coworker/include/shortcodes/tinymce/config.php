<?php

$team_members = array();

$team_members[''] = __( 'Select Team Member', 'coworker' );

$args = array( 'post_type' => 'team', 'posts_per_page' => -1 );

$team = new WP_Query( $args );

    if( $team->have_posts() ):

        while ( $team->have_posts() ) : $team->the_post();

            $team_members[get_the_ID()] = get_the_title();

        endwhile;

    endif;

wp_reset_postdata();


$individual_post = array();

$individual_post[''] = __( 'Select Post', 'coworker' );

$ipostargs = array( 'post_type' => 'post', 'posts_per_page' => -1 );

$indi_post = new WP_Query( $ipostargs );

    if( $indi_post->have_posts() ):

        while ( $indi_post->have_posts() ) : $indi_post->the_post();

            $individual_post[get_the_ID()] = get_the_title();

        endwhile;

    endif;

wp_reset_postdata();


/*-----------------------------------------------------------------------------------*/
/*	Button
/*-----------------------------------------------------------------------------------*/

$zilla_shortcodes['button'] = array(
	'no_preview' => true,
	'params' => array(
		'content' => array(
			'std' => 'Text',
			'type' => 'text',
			'label' => __('Text', 'coworker'),
			'desc' => __('Add Button text', 'coworker'),
		),
        'url' => array(
			'std' => 'http://',
			'type' => 'text',
			'label' => __('URL', 'coworker'),
			'desc' => __('Add Button url. Eg. http://example.com', 'coworker')
		),
        'scrollid' => array(
			'std' => '',
			'type' => 'text',
			'label' => __('Scroll ID', 'coworker'),
			'desc' => __('The ID of the element to which you want to scroll to when clicked on this button', 'coworker')
		),
		'icon' => array(
			'type' => 'select',
			'label' => __('Icon', 'coworker'),
			'desc' => __('Select Button icon.', 'coworker'),
			'options' => get_font_awesome( true )
		),
		'size' => array(
			'type' => 'select',
			'label' => __('Size', 'coworker'),
			'desc' => __('Select Button size', 'coworker'),
			'options' => array(
				'' => 'Normal',
				'large' => 'Large'
			)
		),
		'type' => array(
			'type' => 'select',
			'label' => __('Type', 'coworker'),
			'desc' => __('Select Button Type', 'coworker'),
			'options' => array(
				'' => 'Regular',
				'inverse' => 'Inverse'
			)
		),
		'target' => array(
			'type' => 'select',
			'label' => __('Target', 'coworker'),
			'desc' => __('Select where to open the Button Link', 'coworker'),
			'options' => array(
				'_self' => 'Same Window',
				'_blank' => 'New Window'
			)
		)
	),
	'shortcode' => '[button url="{{url}}" scrollid="{{scrollid}}" icon="{{icon}}" size="{{size}}" target="{{target}}" type="{{type}}"]{{content}}[/button]',
	'popup_title' => __('Insert Simple Button', 'coworker')
);


$zilla_shortcodes['borderbutton'] = array(
	'no_preview' => true,
	'params' => array(
		'content' => array(
			'std' => 'Text',
			'type' => 'text',
			'label' => __('Text', 'coworker'),
			'desc' => __('Add Button text', 'coworker'),
		),
        'url' => array(
			'std' => 'http://',
			'type' => 'text',
			'label' => __('URL', 'coworker'),
			'desc' => __('Add Button url. Eg. http://example.com', 'coworker')
		),
        'scrollid' => array(
			'std' => '',
			'type' => 'text',
			'label' => __('Scroll ID', 'coworker'),
			'desc' => __('The ID of the element to which you want to scroll to when clicked on this button', 'coworker')
		),
		'icon' => array(
			'type' => 'select',
			'label' => __('Icon', 'coworker'),
			'desc' => __('Select Button icon.', 'coworker'),
			'options' => get_font_awesome( true )
		),
		'size' => array(
			'type' => 'select',
			'label' => __('Size', 'coworker'),
			'desc' => __('Select Button size', 'coworker'),
			'options' => array(
				'' => 'Normal',
				'large' => 'Large'
			)
		),
		'type' => array(
			'type' => 'select',
			'label' => __('Type', 'coworker'),
			'desc' => __('Select Button Type', 'coworker'),
			'options' => array(
				'' => 'Regular',
				'inverse' => 'Inverse'
			)
		),
		'target' => array(
			'type' => 'select',
			'label' => __('Target', 'coworker'),
			'desc' => __('Select where to open the Button Link', 'coworker'),
			'options' => array(
				'_self' => 'Same Window',
				'_blank' => 'New Window'
			)
		)
	),
	'shortcode' => '[borderbutton url="{{url}}" scrollid="{{scrollid}}" icon="{{icon}}" size="{{size}}" target="{{target}}" type="{{type}}"]{{content}}[/borderbutton]',
	'popup_title' => __('Insert Border Button', 'coworker')
);


$zilla_shortcodes['altbutton'] = array(
	'no_preview' => true,
	'params' => array(
		'content' => array(
			'std' => 'Text',
			'type' => 'text',
			'label' => __('Text', 'coworker'),
			'desc' => __('Add Button text', 'coworker'),
		),
        'url' => array(
			'std' => 'http://',
			'type' => 'text',
			'label' => __('URL', 'coworker'),
			'desc' => __('Add Button url. Eg. http://example.com', 'coworker')
		),
        'scrollid' => array(
			'std' => '',
			'type' => 'text',
			'label' => __('Scroll ID', 'coworker'),
			'desc' => __('The ID of the element to which you want to scroll to when clicked on this button', 'coworker')
		),
		'icon' => array(
			'type' => 'select',
			'label' => __('Icon', 'coworker'),
			'desc' => __('Select Button icon.', 'coworker'),
			'options' => get_font_awesome( true )
		),
		'style' => array(
			'type' => 'select',
			'label' => __('Style', 'coworker'),
			'desc' => __('Select Button style', 'coworker'),
			'options' => array(
				'' => 'Green',
				'red' => 'Red',
				'blue' => 'Blue',
				'brown' => 'Brown',
				'white' => 'White',
				'yellow' => 'Yellow',
				'purple' => 'Purple',
				'black' => 'Black'
			)
		),
		'target' => array(
			'type' => 'select',
			'label' => __('Target', 'coworker'),
			'desc' => __('Select where to open the Button Link', 'coworker'),
			'options' => array(
				'_self' => 'Same Window',
				'_blank' => 'New Window'
			)
		)
	),
	'shortcode' => '[altbutton url="{{url}}" scrollid="{{scrollid}}" icon="{{icon}}" style="{{style}}" target="{{target}}"]{{content}}[/altbutton]',
	'popup_title' => __('Insert Alternate Button', 'coworker')
);


/*-----------------------------------------------------------------------------------*/
/*	Team Member
/*-----------------------------------------------------------------------------------*/


$zilla_shortcodes['team'] = array(
	'no_preview' => true,
	'params' => array(
		'id' => array(
			'type' => 'select',
			'label' => __('Team Member', 'coworker'),
			'desc' => __('Select Team Member to show details', 'coworker'),
			'options' => $team_members
		),
        'shape' => array(
			'type' => 'select',
			'label' => __('Shape', 'coworker'),
			'desc' => __('Select Team Member\'s Image Shape', 'coworker'),
			'options' => array(
				'' => 'Rectangle',
				'rounded' => 'Rounded'
			)
		),
        'tsclass' => array(
			'type' => 'select',
			'label' => __('Team Social Class', 'coworker'),
			'desc' => __('Select Team Social\'s Class for responsive displays', 'coworker'),
			'options' => array(
				'' => __( 'Select a Class', 'coworker' ),
                'visible-phone' => __( 'Visible only on Mobile Devices', 'coworker' ),
				'visible-tablet' => __( 'Visible only on Tablets', 'coworker' ),
				'visible-desktop' => __( 'Visible only on Desktops', 'coworker' ),
				'hidden-phone' => __( 'Hidden on Mobile Devices', 'coworker' ),
                'hidden-tablet' => __( 'Hidden on Tablets', 'coworker' ),
                'hidden-desktop' => __( 'Hidden on Desktops', 'coworker' )
			)
		)
	),
	'shortcode' => '[team id="{{id}}" shape="{{shape}}" tsclass="{{tsclass}}"]',
	'popup_title' => __('Insert Team Member', 'coworker')
);


/*-----------------------------------------------------------------------------------*/
/*	Individual Post
/*-----------------------------------------------------------------------------------*/


$zilla_shortcodes['ipost'] = array(
	'no_preview' => true,
	'params' => array(
		'id' => array(
			'type' => 'select',
			'label' => __('Post', 'coworker'),
			'desc' => __('Select Post', 'coworker'),
			'options' => $individual_post
		),
        'layout' => array(
			'type' => 'select',
			'label' => __('Layout', 'coworker'),
			'desc' => __('Select Post Layout', 'coworker'),
			'options' => array(
				'full' => 'Full Image by Full Content',
				'half' => 'Half Image by Half Content'
			)
		),
        'media' => array(
			'type' => 'select',
			'label' => __('Show Media', 'coworker'),
			'desc' => __('Select whether to show media or not', 'coworker'),
			'options' => array(
				'true' => 'True',
				'false' => 'False'
			)
		),
        'meta' => array(
			'type' => 'select',
			'label' => __('Show Meta', 'coworker'),
			'desc' => __('Select whether to show meta or not', 'coworker'),
			'options' => array(
				'true' => 'True',
				'false' => 'False'
			)
		),
        'desc' => array(
			'type' => 'select',
			'label' => __('Show Description', 'coworker'),
			'desc' => __('Select whether to show description or not', 'coworker'),
			'options' => array(
				'true' => 'True',
				'false' => 'False'
			)
		),
        'tlimit' => array(
			'type' => 'text',
			'label' => __('Text Limit', 'coworker'),
			'desc' => __('Enter Decription Text Limit. Only Number', 'coworker'),
			'std' => ''
		)
	),
	'shortcode' => '[ipost id="{{id}}" layout="{{layout}}" media="{{media}}" meta="{{meta}}" desc="{{desc}}" tlimit="{{tlimit}}"]',
	'popup_title' => __('Insert Post', 'coworker')
);


/*-----------------------------------------------------------------------------------*/
/*	Posts Block
/*-----------------------------------------------------------------------------------*/


$zilla_shortcodes['posts'] = array(
	'no_preview' => true,
	'params' => array(
		'layout' => array(
			'type' => 'select',
			'label' => __('Layout', 'coworker'),
			'desc' => __('Select Posts Block Layout. The "Full" layout should be used only on "Full Width Template" and the other Layouts should be used on Sidebar Templates.', 'coworker'),
			'options' => array(
				'default' => 'Default',
				'alt' => 'Alternate',
				'full' => 'Full',
				'full-alt' => 'Full Alternate',
				'small' => 'Small Thumbs',
				'small-full' => 'Small Thumbs Full'
			)
		),
        'number' => array(
			'type' => 'text',
			'label' => __('Number', 'coworker'),
			'desc' => __('Number of Posts to be displayed. Only Number', 'coworker'),
			'std' => '5'
		),
        'pagination' => array(
			'type' => 'select',
			'label' => __('Pagination', 'coworker'),
			'desc' => __('Select to Enable/Disable Pagination.', 'coworker'),
			'options' => array(
				'true' => 'True',
				'false' => 'False'
			)
		),
        'paginationtype' => array(
			'type' => 'select',
			'label' => __('Pagination Type', 'coworker'),
			'desc' => __('Select Pagination Type.', 'coworker'),
			'options' => array(
				'' => 'Pagers',
				'numbers' => 'Numbers'
			)
		),
        'include' => array(
			'type' => 'text',
			'label' => __('Include', 'coworker'),
			'desc' => __('Enter the Post IDs separated by commas(,) to retrieve specific Posts. You can also enter only one ID. Leave Blank to retrieve all.', 'coworker'),
			'std' => ''
		),
        'category' => array(
			'type' => 'text',
			'label' => __('Category', 'coworker'),
			'desc' => __('Enter the Categories IDs separated by commas(,) to retrieve Posts only from specific Categories. You can also enter only one ID. Leave Blank to retrieve all.', 'coworker'),
			'std' => ''
		),
        'order' => array(
			'type' => 'select',
			'label' => __('Order', 'coworker'),
			'desc' => __('Select Posts Order.', 'coworker'),
			'options' => array(
				'ASC' => 'ASC',
				'DESC' => 'DESC'
			)
		),
        'orderby' => array(
			'type' => 'select',
			'label' => __('Order By', 'coworker'),
			'desc' => __('Select Posts Order By parameter.', 'coworker'),
			'options' => array(
                '' => 'Select Order By Parameter',
				'none' => 'None',
                'ID' => 'ID',
                'author' => 'Author',
                'title' => 'Title',
                'name' => 'Name',
                'date' => 'Date',
                'modified' => 'Last Modified',
                'parent' => 'Post Parent',
                'rand' => 'Random',
                'comment_count' => 'Comment Count',
                'post__in' => 'Include Field'
			)
		),
        'author' => array(
			'type' => 'text',
			'label' => __('Author', 'coworker'),
			'desc' => __('Enter the Author Nickname to retrieve Posts posted by the specific Author. Leave Blank to retrieve all.', 'coworker'),
			'std' => ''
		),
        'tag' => array(
			'type' => 'text',
			'label' => __('Tag', 'coworker'),
			'desc' => __('Enter the Tag Slugs separated by commas(,) to retrieve Posts only from specific Tags. You can also enter only one Tag Slug. Leave Blank to retrieve all.', 'coworker'),
			'std' => ''
		),
        'search' => array(
			'type' => 'text',
			'label' => __('Search', 'coworker'),
			'desc' => __('Enter a Search Keyword to get Posts from your Keyword Results. Leave Blank to retrieve all.', 'coworker'),
			'std' => ''
		),
        'postformat' => array(
			'type' => 'select',
			'label' => __('Post Format', 'coworker'),
			'desc' => __('Select Post Format.', 'coworker'),
			'options' => array(
				'' => 'Select Post Format',
                'standard' => 'Standard',
				'image' => 'Image',
				'gallery' => 'Gallery',
				'video' => 'Video',
				'audio' => 'Audio'
			)
		)
	),
	'shortcode' => '[posts layout="{{layout}}" number="{{number}}" pagination="{{pagination}}" paginationtype="{{paginationtype}}" include="{{include}}" category="{{category}}" order="{{order}}" orderby="{{orderby}}" author="{{author}}" tag="{{tag}}" search="{{search}}" postformat="{{postformat}}"]',
	'popup_title' => __('Insert Posts Block', 'coworker')
);


/*-----------------------------------------------------------------------------------*/
/*	FAQs Block
/*-----------------------------------------------------------------------------------*/


$zilla_shortcodes['faqs'] = array(
	'no_preview' => true,
	'params' => array(
		'number' => array(
			'type' => 'text',
			'label' => __('Number', 'coworker'),
			'desc' => __('Number of FAQs to be displayed. Only Number', 'coworker'),
			'std' => '5'
		),
        'include' => array(
			'type' => 'text',
			'label' => __('Include', 'coworker'),
			'desc' => __('Enter the FAQ IDs separated by commas(,) to retrieve specific FAQs. You can also enter only one ID. Leave Blank to retrieve all.', 'coworker'),
			'std' => ''
		),
        'category' => array(
			'type' => 'text',
			'label' => __('Category', 'coworker'),
			'desc' => __('Enter the FAQ Categories IDs separated by commas(,) to retrieve FAQs only from specific FAQ Categories. You can also enter only one ID. Leave Blank to retrieve all.', 'coworker'),
			'std' => ''
		),
        'order' => array(
			'type' => 'select',
			'label' => __('Order', 'coworker'),
			'desc' => __('Select FAQs Order.', 'coworker'),
			'options' => array(
				'ASC' => 'ASC',
				'DESC' => 'DESC'
			)
		),
        'orderby' => array(
			'type' => 'select',
			'label' => __('Order By', 'coworker'),
			'desc' => __('Select FAQs Order By parameter.', 'coworker'),
			'options' => array(
                '' => 'Select Order By Parameter',
				'none' => 'None',
                'ID' => 'ID',
                'title' => 'Title',
                'name' => 'Name',
                'date' => 'Date',
                'modified' => 'Last Modified',
                'rand' => 'Random',
                'post__in' => 'Include Field'
			)
		)
	),
	'shortcode' => '[faqs number="{{number}}" include="{{include}}" category="{{category}}" order="{{order}}" orderby="{{orderby}}"]',
	'popup_title' => __('Insert FAQs', 'coworker')
);


/*-----------------------------------------------------------------------------------*/
/*	Portfolio Carousel
/*-----------------------------------------------------------------------------------*/


$zilla_shortcodes['portfoliocarousel'] = array(
	'no_preview' => true,
	'params' => array(
		'number' => array(
			'type' => 'text',
			'label' => __('Number', 'coworker'),
			'desc' => __('Number of Portfolio Items to be displayed. Only Number', 'coworker'),
			'std' => '12'
		),
        'include' => array(
			'type' => 'text',
			'label' => __('Include', 'coworker'),
			'desc' => __('Enter the Portfolio Item IDs separated by commas(,) to retrieve specific Portfolio Items. You can also enter only one ID. Leave Blank to retrieve all.', 'coworker'),
			'std' => ''
		),
        'group' => array(
			'type' => 'text',
			'label' => __('Group', 'coworker'),
			'desc' => __('Enter the Portfolio Item Group IDs separated by commas(,) to retrieve Portfolio Items only from specific Groups. You can also enter only one ID. Leave Blank to retrieve all.', 'coworker'),
			'std' => ''
		),
        'order' => array(
			'type' => 'select',
			'label' => __('Order', 'coworker'),
			'desc' => __('Select Portfolio Items Order.', 'coworker'),
			'options' => array(
				'ASC' => 'ASC',
				'DESC' => 'DESC'
			)
		),
        'orderby' => array(
			'type' => 'select',
			'label' => __('Order By', 'coworker'),
			'desc' => __('Select Portfolio Items Order By parameter.', 'coworker'),
			'options' => array(
                '' => 'Select Order By Parameter',
				'none' => 'None',
                'ID' => 'ID',
                'title' => 'Title',
                'name' => 'Name',
                'date' => 'Date',
                'menu_order' => 'Menu Order',
                'modified' => 'Last Modified',
                'rand' => 'Random',
                'post__in' => 'Include Field'
			)
		),
        'type' => array(
			'type' => 'select',
			'label' => __('Type', 'coworker'),
			'desc' => __('Select Type of Portfolio Items to be shown.', 'coworker'),
			'options' => array(
                '' => 'Select Item Type',
				'image' => 'Image',
				'gallery' => 'Gallery',
				'video' => 'Video'
			)
		)
	),
	'shortcode' => '[portfoliocarousel number="{{number}}" include="{{include}}" group="{{group}}" order="{{order}}" orderby="{{orderby}}" type="{{type}}"]',
	'popup_title' => __('Insert Portfolio Carousel', 'coworker')
);


/*-----------------------------------------------------------------------------------*/
/*	Portfolio Carousel
/*-----------------------------------------------------------------------------------*/


$zilla_shortcodes['portfolio'] = array(
	'no_preview' => true,
	'params' => array(
		'columns' => array(
			'type' => 'select',
			'label' => __('Columns', 'coworker'),
			'desc' => __('Select Portfolio Block Columns. Select the "Sidebar" Option only on Sidebar Layouts.', 'coworker'),
			'options' => array(
				'4' => '4 Columns',
				'4s' => '4 Columns (Sidebar)',
				'3' => '3 Columns',
				'3s' => '3 Columns (Sidebar)',
				'2' => '2 Columns',
				'2s' => '2 Columns (Sidebar)',
				'5' => '5 Columns'
			)
		),
        'number' => array(
			'type' => 'text',
			'label' => __('Number', 'coworker'),
			'desc' => __('Number of Portfolio Items to be displayed. Only Number', 'coworker'),
			'std' => '12'
		),
        'filter' => array(
			'type' => 'select',
			'label' => __('Filter', 'coworker'),
			'desc' => __('Select to Enable/Disable Filter.', 'coworker'),
			'options' => array(
                '' => 'Select Option',
				'true' => 'True',
				'false' => 'False'
			)
		),
        'pagination' => array(
			'type' => 'select',
			'label' => __('Pagination', 'coworker'),
			'desc' => __('Select to Enable/Disable Pagination.', 'coworker'),
			'options' => array(
				'' => 'Select Option',
                'true' => 'True',
				'false' => 'False'
			)
		),
        'include' => array(
			'type' => 'text',
			'label' => __('Include', 'coworker'),
			'desc' => __('Enter the Portfolio Item IDs separated by commas(,) to retrieve specific Portfolio Items. You can also enter only one ID. Leave Blank to retrieve all.', 'coworker'),
			'std' => ''
		),
        'group' => array(
			'type' => 'text',
			'label' => __('Group', 'coworker'),
			'desc' => __('Enter the Portfolio Item Group IDs separated by commas(,) to retrieve Portfolio Items only from specific Groups. You can also enter only one ID. Leave Blank to retrieve all.', 'coworker'),
			'std' => ''
		),
        'order' => array(
			'type' => 'select',
			'label' => __('Order', 'coworker'),
			'desc' => __('Select Portfolio Items Order.', 'coworker'),
			'options' => array(
				'ASC' => 'ASC',
				'DESC' => 'DESC'
			)
		),
        'orderby' => array(
			'type' => 'select',
			'label' => __('Order By', 'coworker'),
			'desc' => __('Select Portfolio Items Order By parameter.', 'coworker'),
			'options' => array(
                '' => 'Select Order By Parameter',
				'none' => 'None',
                'ID' => 'ID',
                'title' => 'Title',
                'name' => 'Name',
                'date' => 'Date',
                'menu_order' => 'Menu Order',
                'modified' => 'Last Modified',
                'rand' => 'Random',
                'post__in' => 'Include Field'
			)
		),
        'type' => array(
			'type' => 'select',
			'label' => __('Type', 'coworker'),
			'desc' => __('Select Type of Portfolio Items to be shown.', 'coworker'),
			'options' => array(
                '' => 'Select Item Type',
				'image' => 'Image',
				'gallery' => 'Gallery',
				'video' => 'Video'
			)
		)
	),
	'shortcode' => '[portfolio columns="{{columns}}" number="{{number}}" filter="{{filter}}" pagination="{{pagination}}" include="{{include}}" group="{{group}}" order="{{order}}" orderby="{{orderby}}" type="{{type}}"]',
	'popup_title' => __('Insert Portfolio Block', 'coworker')
);


/*-----------------------------------------------------------------------------------*/
/*	Slider
/*-----------------------------------------------------------------------------------*/


$zilla_shortcodes['slider'] = array(
	'no_preview' => true,
	'params' => array(
		'width' => array(
			'type' => 'text',
			'label' => __('Width', 'coworker'),
			'desc' => __('Width of the Image. Only Number', 'coworker'),
			'std' => ''
		),
        'height' => array(
			'type' => 'text',
			'label' => __('Height', 'coworker'),
			'desc' => __('Height of the Image. Only Number', 'coworker'),
			'std' => ''
		),
        'number' => array(
			'type' => 'text',
			'label' => __('Number', 'coworker'),
			'desc' => __('Number of Portfolio Items to be displayed. Only Number', 'coworker'),
			'std' => '5'
		),
        'include' => array(
			'type' => 'text',
			'label' => __('Include', 'coworker'),
			'desc' => __('Enter the Portfolio Item IDs separated by commas(,) to retrieve specific Portfolio Items. You can also enter only one ID. Leave Blank to retrieve all.', 'coworker'),
			'std' => ''
		),
        'group' => array(
			'type' => 'text',
			'label' => __('Group', 'coworker'),
			'desc' => __('Enter the Portfolio Item Group IDs separated by commas(,) to retrieve Portfolio Items only from specific Groups. You can also enter only one ID. Leave Blank to retrieve all.', 'coworker'),
			'std' => ''
		),
        'order' => array(
			'type' => 'select',
			'label' => __('Order', 'coworker'),
			'desc' => __('Select Portfolio Items Order.', 'coworker'),
			'options' => array(
				'ASC' => 'ASC',
				'DESC' => 'DESC'
			)
		),
        'orderby' => array(
			'type' => 'select',
			'label' => __('Order By', 'coworker'),
			'desc' => __('Select Portfolio Items Order By parameter.', 'coworker'),
			'options' => array(
                '' => 'Select Order By Parameter',
				'none' => 'None',
                'ID' => 'ID',
                'title' => 'Title',
                'name' => 'Name',
                'date' => 'Date',
                'modified' => 'Last Modified',
                'rand' => 'Random',
                'post__in' => 'Include Field'
			)
		),
        'caption' => array(
			'type' => 'select',
			'label' => __('Caption', 'coworker'),
			'desc' => __('Select to Enable/Disable Caption.', 'coworker'),
			'options' => array(
				'true' => 'True',
				'false' => 'False'
			)
		),
        'animate' => array(
			'type' => 'select',
			'label' => __('Animate', 'coworker'),
			'desc' => __('Select Slider Animation.', 'coworker'),
			'options' => array(
				'slide' => 'Slide',
				'fade' => 'Fade'
			)
		),
        'easing' => array(
			'type' => 'select',
			'label' => __('Easing', 'coworker'),
			'desc' => __('Select Slider Animation Easing.', 'coworker'),
			'options' => get_easing_ops()
		),
        'direction' => array(
			'type' => 'select',
			'label' => __('Direction', 'coworker'),
			'desc' => __('Select Slider Animation Direction.', 'coworker'),
			'options' => array(
				'horizontal' => 'Horizontal',
				'vertical' => 'Vertical'
			)
		),
        'slideshow' => array(
			'type' => 'select',
			'label' => __('Auto Slideshow', 'coworker'),
			'desc' => __('Select to Enable/Disable Auto Slideshow.', 'coworker'),
			'options' => array(
				'true' => 'True',
				'false' => 'False'
			)
		),
        'pause' => array(
			'type' => 'text',
			'label' => __('Pause Time', 'coworker'),
			'desc' => __('Pause Time between animation in milliseconds. Eg. <strong>5000</strong> for 5 Seconds. Only Number', 'coworker'),
			'std' => '5000'
		),
        'speed' => array(
			'type' => 'text',
			'label' => __('Speed', 'coworker'),
			'desc' => __('Animation Speed in milliseconds. Eg. <strong>500</strong> for 0.5 Seconds. Only Number', 'coworker'),
			'std' => '500'
		),
        'video' => array(
			'type' => 'select',
			'label' => __('Video', 'coworker'),
			'desc' => __('Select to Enable/Disable Embedded Videos.', 'coworker'),
			'options' => array(
				'true' => 'True',
				'false' => 'False'
			)
		),
        'class' => array(
			'type' => 'text',
			'label' => __('CSS Class', 'coworker'),
			'desc' => __('Add CSS Class to the Slider Container. Optional', 'coworker'),
			'std' => ''
		),
        'style' => array(
			'type' => 'text',
			'label' => __('Style', 'coworker'),
			'desc' => __('Add CSS Styles to the Slider Container. Optional', 'coworker'),
			'std' => ''
		)
	),
	'shortcode' => '[slider width="{{width}}" height="{{height}}" number="{{number}}" include="{{include}}" group="{{group}}" order="{{order}}" orderby="{{orderby}}" caption="{{caption}}" animate="{{animate}}" easing="{{easing}}" direction="{{direction}}" slideshow="{{slideshow}}" pause="{{pause}}" speed="{{speed}}" video="{{video}}" class="{{class}}" style="{{style}}"]',
	'popup_title' => __('Insert Slider', 'coworker')
);


/*-----------------------------------------------------------------------------------*/
/*	Stylebox
/*-----------------------------------------------------------------------------------*/

$zilla_shortcodes['stylebox'] = array(
	'no_preview' => true,
	'params' => array(
		'type' => array(
			'type' => 'select',
			'label' => __('Type', 'coworker'),
			'desc' => __('Select the Stylebox\'s type', 'coworker'),
			'options' => array(
				'success' => 'Success',
				'error' => 'Error',
				'alert' => 'Alert',
				'info' => 'Info'
			)
		),
		'content' => array(
			'std' => 'Your Message',
			'type' => 'textarea',
			'label' => __('Text', 'coworker'),
			'desc' => __('Add the Stylebox\'s text', 'coworker'),
		)

	),
	'shortcode' => '[stylebox type="{{type}}"]{{content}}[/stylebox]',
	'popup_title' => __('Insert Stylebox', 'coworker')
);


/*-----------------------------------------------------------------------------------*/
/*	Stylebox2
/*-----------------------------------------------------------------------------------*/

$zilla_shortcodes['stylebox2'] = array(
	'no_preview' => true,
	'params' => array(
		'title' => array(
			'type' => 'text',
			'label' => __('Title', 'coworker'),
			'desc' => __('The Title of the Extended Stylebox', 'coworker'),
			'std' => 'Title'
		),
        'type' => array(
			'type' => 'select',
			'label' => __('Type', 'coworker'),
			'desc' => __('Select Stylebox type', 'coworker'),
			'options' => array(
				'success' => 'Success',
				'error' => 'Error',
				'alert' => 'Alert',
				'info' => 'Info'
			)
		),
		'content' => array(
			'std' => 'Your Message',
			'type' => 'textarea',
			'label' => __('Text', 'coworker'),
			'desc' => __('Enter Stylebox text', 'coworker'),
		)

	),
	'shortcode' => '[stylebox2 title="{{title}}" type="{{type}}"]{{content}}[/stylebox2]',
	'popup_title' => __('Insert Stylebox2', 'coworker')
);


/*-----------------------------------------------------------------------------------*/
/*	Alerts
/*-----------------------------------------------------------------------------------*/

$zilla_shortcodes['alert'] = array(
	'no_preview' => true,
	'params' => array(
		'close' => array(
			'type' => 'select',
			'label' => __('Closable', 'coworker'),
			'desc' => __('Make the Alert closable', 'coworker'),
			'options' => array(
				'true' => 'True',
				'false' => 'False'
			)
		),
        'type' => array(
			'type' => 'select',
			'label' => __('Type', 'coworker'),
			'desc' => __('Select the Alert type', 'coworker'),
			'options' => array(
				'success' => 'Success',
				'error' => 'Error',
				'' => 'Alert',
				'info' => 'Info'
			)
		),
		'content' => array(
			'std' => 'Your Message',
			'type' => 'textarea',
			'label' => __('Text', 'coworker'),
			'desc' => __('Enter Alert Text', 'coworker')
		)

	),
	'shortcode' => '[alert close="{{close}}" type="{{type}}"]{{content}}[/alert]',
	'popup_title' => __('Insert Alert', 'coworker')
);


/*-----------------------------------------------------------------------------------*/
/*	Subscribe Form
/*-----------------------------------------------------------------------------------*/

$zilla_shortcodes['subscribe'] = array(
	'no_preview' => true,
	'params' => array(
		'listid' => array(
			'type' => 'text',
			'label' => __('List ID', 'coworker'),
			'desc' => __('Enter your MailChimp List ID.', 'coworker'),
			'std' => ''
		),
        'inputtext' => array(
			'type' => 'text',
			'label' => __('Input Text', 'coworker'),
			'desc' => __('Enter default text to be shown in the Input Area.', 'coworker'),
			'std' => 'Enter your Email to get notified..'
		),
        'buttontext' => array(
			'type' => 'text',
			'label' => __('Button Text', 'coworker'),
			'desc' => __('Enter default text to be shown on the Subscribe Button.', 'coworker'),
			'std' => 'Subscribe Now'
		)
	),
	'shortcode' => '[subscribe listid="{{listid}}" inputtext="{{inputtext}}" buttontext="{{buttontext}}"]',
	'popup_title' => __('Insert Newsletter Form', 'coworker')
);


/*-----------------------------------------------------------------------------------*/
/*	BlockQuote
/*-----------------------------------------------------------------------------------*/

$zilla_shortcodes['blockquote'] = array(
	'no_preview' => true,
	'params' => array(
		'style' => array(
			'type' => 'select',
			'label' => __('Style', 'coworker'),
			'desc' => __('Select the Blockquote Style', 'coworker'),
			'options' => array(
				'' => 'Normal',
				'quote' => 'quote'
			)
		),
        'align' => array(
			'type' => 'select',
			'label' => __('Align', 'coworker'),
			'desc' => __('Select the Blockquote Alignment', 'coworker'),
			'options' => array(
				'' => 'None',
				'left' => 'Left',
				'right' => 'Right'
			)
		),
		'content' => array(
			'std' => '',
			'type' => 'textarea',
			'label' => __('Blockquote Text', 'coworker'),
			'desc' => __('Add the Blockquote text', 'coworker'),
		)

	),
	'shortcode' => '[blockquote style="{{style}}" align="{{align}}"]{{content}}[/blockquote]',
	'popup_title' => __('Insert Blockquote', 'coworker')
);


/*-----------------------------------------------------------------------------------*/
/*	Dividers
/*-----------------------------------------------------------------------------------*/


$zilla_shortcodes['divider'] = array(
	'no_preview' => true,
	'params' => array(
		'type' => array(
			'type' => 'select',
			'label' => __('Divider Type', 'coworker'),
			'desc' => __('Select the Divider Type', 'coworker'),
			'options' => array(
				'line' => 'Line',
				'doubleline' => 'Double Line',
				'dottedline' => 'Dotted Line',
				'clear' => 'Clear'
			)
		)
	),
	'shortcode' => '[divider type="{{type}}"]',
	'popup_title' => __('Insert a Divider', 'coworker')
);


/*-----------------------------------------------------------------------------------*/
/*	Icons
/*-----------------------------------------------------------------------------------*/


$zilla_shortcodes['soloicon'] = array(
	'no_preview' => true,
	'params' => array(
		'icon' => array(
			'std' => '',
			'type' => 'select',
			'label' => __('Icon', 'coworker'),
			'desc' => __('Select Icon', 'coworker'),
			'options' => get_font_awesome( true )
		),
        'style' => array(
			'std' => '',
			'type' => 'select',
			'label' => __('Style', 'coworker'),
			'desc' => __('Select Icon Style', 'coworker'),
			'options' => array(
				'' => 'Dark',
				'light' => 'Light'
			)
		),
        'type' => array(
			'std' => '',
			'type' => 'select',
			'label' => __('Type', 'coworker'),
			'desc' => __('Select Icon Type', 'coworker'),
			'options' => array(
				'' => 'Rounded',
				'circle' => 'Circle',
				'plain' => 'Plain'
			)
		),
        'url' => array(
			'std' => 'http://',
			'type' => 'text',
			'label' => __('URL', 'coworker'),
			'desc' => __('Add Icon url. Eg. http://example.com', 'coworker')
		),
		'target' => array(
			'std' => '',
			'type' => 'select',
			'label' => __('Target', 'coworker'),
			'desc' => __('Select where to open the Icon Link', 'coworker'),
			'options' => array(
				'_self' => 'Same Window',
				'_blank' => 'New Window'
			)
		),
		'color' => array(
			'std' => '',
			'type' => 'color',
			'label' => __('Background Color', 'coworker'),
			'desc' => __('Select Background color', 'coworker')
		),
        'class' => array(
			'type' => 'text',
			'label' => __('Class', 'coworker'),
			'desc' => __('Add classes to your Icon. Optional', 'coworker'),
			'std' => ''
		)
	),
	'shortcode' => '[icon style="{{style}}" icon="{{icon}}" type="{{type}}" url="{{url}}" target="{{target}}" color="{{color}}" class="{{class}}"]',
	'popup_title' => __('Insert Icon', 'coworker')
);


/*-----------------------------------------------------------------------------------*/
/*	Featured Content
/*-----------------------------------------------------------------------------------*/


$zilla_shortcodes['feature'] = array(
	'no_preview' => true,
	'params' => array(
        'title' => array(
			'type' => 'text',
			'label' => __('Title', 'coworker'),
			'desc' => __('Title of the Feature Box', 'coworker'),
			'std' => ''
		),
        'url' => array(
			'std' => 'http://',
			'type' => 'text',
			'label' => __('URL', 'coworker'),
			'desc' => __('Enter a URL to link your Feature Box.', 'coworker')
		),
        'style' => array(
			'type' => 'select',
			'label' => __('Style', 'coworker'),
			'desc' => __('Select Feature Box Style', 'coworker'),
			'options' => array(
				'' => '1',
				'2' => '2',
				'3' => '3'
			)
		),
        'icon' => array(
			'type' => 'select',
			'label' => __('Icon', 'coworker'),
			'desc' => __('Select Icon', 'coworker'),
			'options' => get_font_awesome( true )
		),
        'iconurl' => array(
			'std' => 'http://',
			'type' => 'text',
			'label' => __('Icon URL', 'coworker'),
			'desc' => __('Enter Image Icon URL.', 'coworker')
		),
		'content' => array(
			'std' => 'Content',
			'type' => 'textarea',
			'label' => __('Featured Content', 'coworker'),
			'desc' => __('Content of Featured Box', 'coworker'),
		)
	),
	'shortcode' => '[feature style="{{style}}" icon="{{icon}}" title="{{title}}" iconurl="{{iconurl}}" url={{url}}]{{content}}[/feature]',
	'popup_title' => __('Insert Feature Box', 'coworker')
);


/*-----------------------------------------------------------------------------------*/
/*	Responsive Content
/*-----------------------------------------------------------------------------------*/


$zilla_shortcodes['responsive'] = array(
	'no_preview' => true,
	'params' => array(
        'layout' => array(
			'type' => 'select',
			'label' => __('Utility Class', 'coworker'),
			'desc' => __('Select Responsive Utility Class', 'coworker'),
			'options' => array(
				'visible-phone' => 'Visible only on Mobile Devices',
				'visible-tablet' => 'Visible only on Tablets',
				'visible-desktop' => 'Visible only on Desktops',
				'hidden-phone' => 'Hidden on Mobile Devices',
                'hidden-tablet' => 'Hidden on Tablets',
                'hidden-desktop' => 'Hidden on Desktops'
			)
		),
		'content' => array(
			'std' => 'Content',
			'type' => 'textarea',
			'label' => __('Content', 'coworker'),
			'desc' => __('Content of Responsive Area', 'coworker')
		)
	),
	'shortcode' => '[responsive layout="{{layout}}"]{{content}}[/responsive]',
	'popup_title' => __('Insert Responsive Content', 'coworker')
);


/*-----------------------------------------------------------------------------------*/
/*	DropCap
/*-----------------------------------------------------------------------------------*/


$zilla_shortcodes['dropcap'] = array(
	'no_preview' => true,
	'params' => array(
		'style' => array(
			'type' => 'select',
			'label' => __('Style', 'coworker'),
			'desc' => __('Select the Dropcap Style', 'coworker'),
			'options' => array(
				'' => '1',
				'2' => '2',
				'3' => '3',
				'4' => '4'
			)
		),
		'color' => array(
			'type' => 'color',
			'label' => __('Background Color', 'coworker'),
			'desc' => __('Select the Dropcap\'s Background color', 'coworker')
		),
        'text' => array(
			'type' => 'text',
			'label' => __('Letter', 'coworker'),
			'desc' => __('Add the Dropcap Letter', 'coworker'),
			'std' => ''
		)
	),
	'shortcode' => '[dropcap style="{{style}}" color="{{color}}" text="{{text}}"]',
	'popup_title' => __('Insert a Dropcap', 'coworker')
);


$zilla_shortcodes['highlight'] = array(
	'no_preview' => true,
	'params' => array(
		'color' => array(
			'type' => 'color',
			'label' => __('Background Color', 'coworker'),
			'desc' => __('Select the Highlights\'s Background color', 'coworker')
		),
		'content' => array(
			'std' => 'Content',
			'type' => 'textarea',
			'label' => __('Highlight Content', 'coworker'),
			'desc' => __('The Content of the Highlighted Area.', 'coworker'),
		)
	),
	'shortcode' => '[highlight color="{{color}}"]{{content}}[/highlight]',
	'popup_title' => __('Insert a Highlighted Content', 'coworker')
);


/*-----------------------------------------------------------------------------------*/
/*	Pricing
/*-----------------------------------------------------------------------------------*/

$zilla_shortcodes['pricing'] = array(
    'params' => array(
		'col' => array(
			'type' => 'select',
			'label' => __('Columns', 'coworker'),
			'desc' => __('Select the No. of Pricing Columns', 'coworker'),
			'options' => array(
				'3' => '3',
				'' => '4',
				'5' => '5'
			)
		),
        'style' => array(
			'type' => 'select',
			'label' => __('Style', 'coworker'),
			'desc' => __('Select Pricing Table Style', 'coworker'),
			'options' => array(
				'' => 'Style 1',
				'2' => 'Style 2'
			)
		),
        'class' => array(
            'std' => '',
            'type' => 'text',
            'label' => __('Class', 'coworker'),
            'desc' => __('Add a Class to the Pricing Table. Optional', 'coworker'),
        ),
        'cssstyle' => array(
            'std' => '',
            'type' => 'text',
            'label' => __('CSS Style', 'coworker'),
            'desc' => __('Add Style CSS to the Pricing Table. Optional', 'coworker'),
        )
	),
    'no_preview' => true,
    'shortcode' => '[pricing col="{{col}}" style="{{style}}" cssstyle="{{cssstyle}}" class="{{class}}"]{{child_shortcode}}[/pricing]',
    'popup_title' => __('Insert Pricing Shortcode', 'coworker'),

    'child_shortcode' => array(
        'params' => array(
            'define' => array(
    			'type' => 'select',
    			'label' => __('Definition Box', 'coworker'),
    			'desc' => __('Select if this is the Pricing Definition Box. Remember this should be the First Box. There can only be "1" Definition Box per Pricing Table. Only Enter the "Pricing Features" if you select "Yes".', 'coworker'),
    			'options' => array(
    				'' => 'No',
    				'true' => 'Yes'
    			)
    		),
            'title' => array(
                'std' => 'Title',
                'type' => 'text',
                'label' => __('Pricing Box Title', 'coworker'),
                'desc' => __('Title of Pricing Box', 'coworker'),
            ),
            'subtitle' => array(
                'std' => 'Sub Title',
                'type' => 'text',
                'label' => __('Pricing Box Sub Title', 'coworker'),
                'desc' => __('Sub Title of Pricing Box', 'coworker'),
            ),
            'price' => array(
                'std' => '',
                'type' => 'text',
                'label' => __('Price', 'coworker'),
                'desc' => __('Price of Pricing Box. Eg. $9', 'coworker'),
            ),
            'pricesub' => array(
                'std' => '',
                'type' => 'text',
                'label' => __('Sub Price', 'coworker'),
                'desc' => __('Sub Price of Pricing Box. Eg. 99', 'coworker'),
            ),
            'tenure' => array(
                'std' => '',
                'type' => 'text',
                'label' => __('Tenure', 'coworker'),
                'desc' => __('The pricing tenure to be show below the Price. Eg. Monthly, Yearly, Hourly etc.', 'coworker'),
            ),
            'button' => array(
                'std' => '',
                'type' => 'text',
                'label' => __('Button Text', 'coworker'),
                'desc' => __('The Button Text of the Pricing Box', 'coworker'),
            ),
            'url' => array(
                'std' => '',
                'type' => 'text',
                'label' => __('Button URL', 'coworker'),
                'desc' => __('The Button URL of the Pricing Box. Include http://', 'coworker'),
            ),
            'icon' => array(
    			'type' => 'select',
    			'label' => __('Button Icon', 'coworker'),
    			'desc' => __('Select the Button\'s icon', 'coworker'),
    			'options' => get_font_awesome( true )
    		),
            'best' => array(
    			'type' => 'select',
    			'label' => __('Featured Box', 'coworker'),
    			'desc' => __('Select if you want to make this Pricing Box featured', 'coworker'),
    			'options' => array(
    				'' => 'No',
    				'true' => 'Yes'
    			)
    		),
            'content' => array(
                'std' => '',
                'type' => 'textarea',
                'label' => __('Pricing Features', 'coworker'),
                'desc' => __('Add the Pricing Features. Eg.<br>&lt;li&gt;Feature 1&lt;/li&gt;<br>&lt;li&gt;Feature 2&lt;/li&gt;<br>and so on... If this is the Definition Box, then you can enter.Eg.<br>&lt;li&gt;Feature 1[pricingfaq text="Feature Description Text"]&lt;/li&gt;', 'coworker')
            )
        ),
        'shortcode' => '[price define="{{define}}" title="{{title}}" subtitle="{{subtitle}}" price="{{price}}" pricesub="{{pricesub}}" tenure="{{tenure}}" button="{{button}}" url="{{url}}" icon="{{icon}}" best="{{best}}"]{{content}}[/price]',
        'clone_button' => __('Add Pricing Box', 'coworker')
    )
);


/*-----------------------------------------------------------------------------------*/
/*	Pricing
/*-----------------------------------------------------------------------------------*/

$zilla_shortcodes['gmap'] = array(
    'params' => array(
        'height' => array(
            'std' => '',
            'type' => 'text',
            'label' => __('Map Height', 'coworker'),
            'desc' => __('Enter your Map Height in px. Only Number. Eg. 200', 'coworker'),
        ),
        'latitude' => array(
            'std' => '',
            'type' => 'text',
            'label' => __('Latitude', 'coworker'),
            'desc' => __('Add a Latitude. Optional. If you enter Latitude, you should enter Longitude too and vice versa', 'coworker'),
        ),
        'longitude' => array(
            'std' => '',
            'type' => 'text',
            'label' => __('Longitude', 'coworker'),
            'desc' => __('Add a Longitude. Optional. If you enter Longitude, you should enter Latitude too and vice versa', 'coworker'),
        ),
        'address' => array(
            'std' => '',
            'type' => 'text',
            'label' => __('Address', 'coworker'),
            'desc' => __('Add an Address. Optional. If you enter Address, both Latitude and Longitude will not be considered', 'coworker'),
        ),
        'zoom' => array(
            'std' => '12',
            'type' => 'text',
            'label' => __('Zoom', 'coworker'),
            'desc' => __('Enter a Map Zoom. Numbers Only. 2 to 16', 'coworker'),
        ),
        'type' => array(
			'type' => 'select',
			'label' => __('Map Type', 'coworker'),
			'desc' => __('Select the Map Type', 'coworker'),
			'options' => array(
				'ROADMAP' => 'ROADMAP',
				'HYBRID' => 'HYBRID',
				'TERRAIN' => 'TERRAIN',
				'SATELLITE' => 'SATELLITE'
			)
		),
        'scrollwheel' => array(
			'type' => 'select',
			'label' => __('Scrollwheel', 'coworker'),
			'desc' => __('Select to use Scrollwheel', 'coworker'),
			'options' => array(
				'false' => 'No',
				'true' => 'Yes'
			)
		),
        'pan' => array(
			'type' => 'select',
			'label' => __('Pan Control', 'coworker'),
			'desc' => __('Select to use Pan Control', 'coworker'),
			'options' => array(
				'false' => 'No',
				'true' => 'Yes'
			)
		),
        'zoomc' => array(
			'type' => 'select',
			'label' => __('Zoom Control', 'coworker'),
			'desc' => __('Select to use Zoom Control', 'coworker'),
			'options' => array(
				'false' => 'No',
				'true' => 'Yes'
			)
		),
        'maptypec' => array(
			'type' => 'select',
			'label' => __('MapType Control', 'coworker'),
			'desc' => __('Select to use MapType Control', 'coworker'),
			'options' => array(
				'false' => 'No',
				'true' => 'Yes'
			)
		),
        'scale' => array(
			'type' => 'select',
			'label' => __('Scale Control', 'coworker'),
			'desc' => __('Select to use Scale Control', 'coworker'),
			'options' => array(
				'false' => 'No',
				'true' => 'Yes'
			)
		),
        'streetview' => array(
			'type' => 'select',
			'label' => __('Street View Control', 'coworker'),
			'desc' => __('Select to use Street View Control', 'coworker'),
			'options' => array(
				'false' => 'No',
				'true' => 'Yes'
			)
		),
        'overviewmap' => array(
			'type' => 'select',
			'label' => __('Overview Map Control', 'coworker'),
			'desc' => __('Select to use Overview Map Control', 'coworker'),
			'options' => array(
				'false' => 'No',
				'true' => 'Yes'
			)
		),
        'class' => array(
            'std' => '',
            'type' => 'text',
            'label' => __('CSS Class', 'coworker'),
            'desc' => __('Add CSS Class to Google Maps. Optional', 'coworker'),
        ),
        'style' => array(
            'std' => '',
            'type' => 'text',
            'label' => __('Style', 'coworker'),
            'desc' => __('Add Style CSS to the Google Maps. Optional', 'coworker'),
        )
	),
    'no_preview' => true,
    'shortcode' => '[gmap height="{{height}}" latitude="{{latitude}}" longitude="{{longitude}}" address="{{address}}" zoom="{{zoom}}" type="{{type}}" scrollwheel="{{scrollwheel}}" pan="{{pan}}" zoomc="{{zoomc}}" maptypec="{{maptypec}}" scale="{{scale}}" streetview="{{streetview}}" overviewmap="{{overviewmap}}" markers="{{child_shortcode}}" class="{{class}}" style="{{style}}"]',
    'popup_title' => __('Insert Google Maps', 'coworker'),

    'child_shortcode' => array(
        'params' => array(
            'markerlatitude' => array(
                'std' => '',
                'type' => 'text',
                'label' => __('Marker\'s Latitude', 'coworker'),
                'desc' => __('Add the Marker\'s Latitude. Optional. If you enter Latitude, you should enter Longitude too and vice versa', 'coworker'),
            ),
            'markerlongitude' => array(
                'std' => '',
                'type' => 'text',
                'label' => __('Marker\'s Longitude', 'coworker'),
                'desc' => __('Add the Marker\'s Longitude. Optional. If you enter Longitude, you should enter Latitude too and vice versa', 'coworker'),
            ),
            'markeraddress' => array(
                'std' => '',
                'type' => 'text',
                'label' => __('Marker\'s Address', 'coworker'),
                'desc' => __('Add the Marker\'s Address. Optional. If you enter Address, both Latitude and Longitude will not be considered', 'coworker'),
            ),
            'markerhtml' => array(
                'std' => '',
                'type' => 'textarea',
                'label' => __('Marker\'s Popup Content', 'coworker'),
                'desc' => __('The Content of the Marker\'s Popup Box', 'coworker'),
            )
        ),
        'shortcode' => '{{markerlatitude}}|{{markerlongitude}}|{{markeraddress}}|{{markerhtml}};',
        'clone_button' => __('Add Marker', 'coworker')
    )
);


/*-----------------------------------------------------------------------------------*/
/*	Toggle
/*-----------------------------------------------------------------------------------*/

$zilla_shortcodes['toggle'] = array(
	'no_preview' => true,
	'params' => array(
		'title' => array(
			'type' => 'text',
			'label' => __('Toggle Title', 'coworker'),
			'desc' => __('Title of Toggle Content', 'coworker'),
			'std' => 'Title'
		),
		'content' => array(
			'std' => 'Content',
			'type' => 'textarea',
			'label' => __('Toggle Content', 'coworker'),
			'desc' => __('Content of Toggle. HTML Supported', 'coworker'),
		)
	),
	'shortcode' => '[toggle title="{{title}}"]{{content}}[/toggle]',
	'popup_title' => __('Insert Toggle', 'coworker')
);


/*-----------------------------------------------------------------------------------*/
/*	Callout
/*-----------------------------------------------------------------------------------*/

$zilla_shortcodes['promo'] = array(
	'no_preview' => true,
	'params' => array(
		'title' => array(
			'type' => 'text',
			'label' => __('Title', 'coworker'),
			'desc' => __('The Title of Promo. Preferably 8-12 Words', 'coworker'),
			'std' => 'Title'
		),
		'content' => array(
			'type' => 'text',
			'label' => __('Sub Title', 'coworker'),
			'desc' => __('The Sub Title of Promo. Optional. Preferably 12-15 Words', 'coworker'),
			'std' => ''
		),
        'button' => array(
			'type' => 'text',
			'label' => __('Button Text', 'coworker'),
			'desc' => __('The Text of Promo Button', 'coworker'),
			'std' => ''
		),
        'url' => array(
			'type' => 'text',
			'label' => __('Button URL', 'coworker'),
			'desc' => __('The URL of Promo Button', 'coworker'),
			'std' => 'http://'
		),
		'icon' => array(
			'type' => 'select',
			'label' => __('Button Icon', 'coworker'),
			'desc' => __('Select Promo Button icon', 'coworker'),
			'options' => get_font_awesome( true )
		),
		'target' => array(
			'type' => 'select',
			'label' => __('Button Target', 'coworker'),
			'desc' => __('Target of Button Link', 'coworker'),
			'options' => array(
				'_self' => 'Same Window',
				'_blank' => 'New Window'
			)
		),
        'style' => array(
			'type' => 'text',
			'label' => __('CSS Style', 'coworker'),
			'desc' => __('Add you CSS for the Style Attibute. Optional', 'coworker'),
			'std' => ''
		),
        'class' => array(
			'type' => 'text',
			'label' => __('CSS Class', 'coworker'),
			'desc' => __('Add CSS Classes. Optional', 'coworker'),
			'std' => ''
		)
	),
	'shortcode' => '[promo title="{{title}}" button="{{button}}" url="{{url}}" icon="{{icon}}" target="{{target}}" style="{{style}}" class="{{class}}"]{{content}}[/promo]',
	'popup_title' => __('Insert Promo Shortcode', 'coworker')
);


/*-----------------------------------------------------------------------------------*/
/*	Tabs
/*-----------------------------------------------------------------------------------*/

$zilla_shortcodes['tabs'] = array(
    'params' => array(
		'type' => array(
			'type' => 'select',
			'label' => __('Type', 'coworker'),
			'desc' => __('Select Tab Type', 'coworker'),
			'options' => array(
				'' => 'Normal',
				'tour' => 'Tour'
			)
		),
        'class' => array(
            'std' => '',
            'type' => 'text',
            'label' => __('Class', 'coworker'),
            'desc' => __('Add a CSS Class to the Tab. Optional', 'coworker'),
        ),
        'style' => array(
            'std' => '',
            'type' => 'text',
            'label' => __('Style', 'coworker'),
            'desc' => __('Add Style CSS to the Tab. Optional', 'coworker'),
        )
	),
    'no_preview' => true,
    'shortcode' => '[tabs type="{{type}}" style="{{style}}" class="{{class}}"]{{child_shortcode}}[/tabs]',
    'popup_title' => __('Insert Tab', 'coworker'),

    'child_shortcode' => array(
        'params' => array(
            'title' => array(
                'std' => 'Title',
                'type' => 'text',
                'label' => __('Title', 'coworker'),
                'desc' => __('Title of Tab', 'coworker'),
            ),
            'id' => array(
                'std' => '',
                'type' => 'text',
                'label' => __('ID', 'coworker'),
                'desc' => __('ID of Tab. No "#" or space', 'coworker'),
            ),
            'content' => array(
                'std' => 'Content',
                'type' => 'textarea',
                'label' => __('Content', 'coworker'),
                'desc' => __('Add Tab content', 'coworker')
            )
        ),
        'shortcode' => '[tab title="{{title}}" id="{{id}}"]{{content}}[/tab]',
        'clone_button' => __('Add Tab', 'coworker')
    )
);


/*-----------------------------------------------------------------------------------*/
/*	Tabs with Icons
/*-----------------------------------------------------------------------------------*/

$zilla_shortcodes['icontabs'] = array(
    'params' => array(
		'type' => array(
			'type' => 'select',
			'label' => __('Type', 'coworker'),
			'desc' => __('Select Tab Type', 'coworker'),
			'options' => array(
				'' => 'Normal',
				'tour' => 'Tour'
			)
		),
        'titletype' => array(
			'type' => 'select',
			'label' => __('Title Type', 'coworker'),
			'desc' => __('Select Tab Title Type', 'coworker'),
			'options' => array(
				'' => 'Icon + Title',
				'icon' => 'Only Icon'
			)
		),
        'class' => array(
            'std' => '',
            'type' => 'text',
            'label' => __('Class', 'coworker'),
            'desc' => __('Add a CSS Class to the Tab. Optional', 'coworker'),
        ),
        'style' => array(
            'std' => '',
            'type' => 'text',
            'label' => __('Style', 'coworker'),
            'desc' => __('Add Style CSS to the Tab. Optional', 'coworker'),
        )
	),
    'no_preview' => true,
    'shortcode' => '[tabs type="{{type}}" titletype="{{titletype}}" style="{{style}}" class="{{class}}" icons="true"]{{child_shortcode}}[/tabs]',
    'popup_title' => __('Insert Tab Shortcode', 'coworker'),

    'child_shortcode' => array(
        'params' => array(
            'title' => array(
                'std' => 'Title',
                'type' => 'text',
                'label' => __('Tab Title', 'coworker'),
                'desc' => __('Title of Tab', 'coworker'),
            ),
            'id' => array(
                'std' => '',
                'type' => 'text',
                'label' => __('ID', 'coworker'),
                'desc' => __('ID of Tab. No "#" or space', 'coworker'),
            ),
            'icon' => array(
    			'type' => 'select',
    			'label' => __('Tab Icon', 'coworker'),
    			'desc' => __('Select Tab icon', 'coworker'),
    			'options' => get_font_awesome( true )
    		),
            'content' => array(
                'std' => 'Tab Content',
                'type' => 'textarea',
                'label' => __('Tab Content', 'coworker'),
                'desc' => __('Add Tab content', 'coworker')
            )
        ),
        'shortcode' => '[tab title="{{title}}" icon="{{icon}}" id="{{id}}"]{{content}}[/tab]',
        'clone_button' => __('Add Tab', 'coworker')
    )
);


/*-----------------------------------------------------------------------------------*/
/*	Accordion
/*-----------------------------------------------------------------------------------*/

$zilla_shortcodes['accordion'] = array(
	'params' => array(
        'class' => array(
            'std' => '',
            'type' => 'text',
            'label' => __('Class', 'coworker'),
            'desc' => __('Add a CSS Class to the Accordion. Optional', 'coworker'),
        )
    ),
    'no_preview' => true,
    'shortcode' => '[accordions class="{{class}}"]{{child_shortcode}}[/accordions]',
    'popup_title' => __('Insert Accordion', 'coworker'),

    'child_shortcode' => array(
        'params' => array(
            'title' => array(
                'std' => 'Title',
                'type' => 'text',
                'label' => __('Title', 'coworker'),
                'desc' => __('Title of Accordion', 'coworker'),
            ),
            'content' => array(
                'std' => 'Content',
                'type' => 'textarea',
                'label' => __('Content', 'coworker'),
                'desc' => __('Content of Accordion', 'coworker')
            )
        ),
        'shortcode' => '[accordion title="{{title}}"]{{content}}[/accordion]',
        'clone_button' => __('Add Accordion', 'coworker')
    )
);


/*-----------------------------------------------------------------------------------*/
/*	Skills
/*-----------------------------------------------------------------------------------*/

$zilla_shortcodes['skills'] = array(
	'params' => array(),
    'no_preview' => true,
    'shortcode' => '[skills]{{child_shortcode}}[/skills]',
    'popup_title' => __('Insert Skills', 'coworker'),

    'child_shortcode' => array(
        'params' => array(
            'text' => array(
                'std' => 'Text',
                'type' => 'text',
                'label' => __('Text', 'coworker'),
                'desc' => __('Text of Skill', 'coworker'),
            ),
            'percent' => array(
                'std' => '100',
                'type' => 'text',
                'label' => __('Percent', 'coworker'),
                'desc' => __('Percent of Skill. Only Number.', 'coworker')
            ),
            'style' => array(
    			'type' => 'select',
    			'label' => __('Style', 'coworker'),
    			'desc' => __('Select Skill Style', 'coworker'),
    			'options' => array(
    				'' => 'Info',
    				'success' => 'Success',
    				'warning' => 'Warning',
    				'danger' => 'Danger'
    			)
    		)
        ),
        'shortcode' => '[skill text="{{text}}" percent="{{percent}}" style="{{style}}"]',
        'clone_button' => __('Add Skill', 'coworker')
    )
);


/*-----------------------------------------------------------------------------------*/
/*	Testimonials
/*-----------------------------------------------------------------------------------*/

$zilla_shortcodes['testimonials'] = array(
	'no_preview' => true,
	'params' => array(
		'number' => array(
			'type' => 'text',
			'label' => __('Number', 'coworker'),
			'desc' => __('Number of Testimonials. Enter "-1" to retrive all', 'coworker'),
			'std' => '5'
		),
        'display' => array(
			'type' => 'select',
			'label' => __('Display', 'coworker'),
			'desc' => __('Select Display Type', 'coworker'),
			'options' => array(
				'recent' => 'Recent',
				'menu_order' => 'Menu Order',
				'random' => 'Random'
			)
		),
        'auto' => array(
			'type' => 'select',
			'label' => __('Auto Scroll', 'coworker'),
			'desc' => __('Select whether you want to Auto Scroll the Testimonials', 'coworker'),
			'options' => array(
				'true' => 'True',
				'false' => 'False'
			)
		),
		'speed' => array(
			'std' => '500',
			'type' => 'text',
			'label' => __('Speed', 'coworker'),
			'desc' => __('Speed of the Testimonials Animation in milliseconds. Eg. 500 for 0.5 Seconds.', 'coworker'),
		),
		'pause' => array(
			'std' => '5000',
			'type' => 'text',
			'label' => __('Pause Time', 'coworker'),
			'desc' => __('Pause Time between the animation of Testimonials in milliseconds. Eg. 5000 for 5 Seconds.', 'coworker'),
		),
		'tlimit' => array(
			'std' => '',
			'type' => 'text',
			'label' => __('Text Limit', 'coworker'),
			'desc' => __('Limit the Number of Words in the Testimonial Text. Only Numbers', 'coworker'),
		),
		'include' => array(
			'std' => '',
			'type' => 'text',
			'label' => __('Include IDs', 'coworker'),
			'desc' => __('Enter the IDs of Testimonials to retrieve them only. Optional', 'coworker'),
		)
	),
	'shortcode' => '[testimonials number="{{number}}" display="{{display}}" auto="{{auto}}" speed="{{speed}}" pause="{{pause}}" tlimit="{{tlimit}}" include="{{include}}"]',
	'popup_title' => __('Insert Testimonials Scroller', 'coworker')
);


/*-----------------------------------------------------------------------------------*/
/*	Clients
/*-----------------------------------------------------------------------------------*/

$zilla_shortcodes['clients'] = array(
	'no_preview' => true,
	'params' => array(
		'number' => array(
			'type' => 'text',
			'label' => __('Number', 'coworker'),
			'desc' => __('Number of Clients. Enter "-1" to retrive all', 'coworker'),
			'std' => '12'
		),
        'display' => array(
			'type' => 'select',
			'label' => __('Display', 'coworker'),
			'desc' => __('Select Display Type', 'coworker'),
			'options' => array(
				'recent' => 'Recent',
				'menu_order' => 'Menu Order',
				'random' => 'Random'
			)
		),
		'include' => array(
			'std' => '',
			'type' => 'text',
			'label' => __('Include IDs', 'coworker'),
			'desc' => __('Enter the IDs of Clients to retrieve them only. Optional', 'coworker'),
		)
	),
	'shortcode' => '[clients number="{{number}}" display="{{display}}" include="{{include}}"]',
	'popup_title' => __('Insert Clients Scroller', 'coworker')
);


/*-----------------------------------------------------------------------------------*/
/*	Icon List
/*-----------------------------------------------------------------------------------*/

$zilla_shortcodes['iconlist'] = array(
	'no_preview' => true,
	'params' => array(
		'icon' => array(
			'type' => 'select',
			'label' => __('Icon', 'coworker'),
			'desc' => __('Select Button icon.', 'coworker'),
			'options' => get_font_awesome( true )
		),
		'class' => array(
			'std' => '',
			'type' => 'text',
			'label' => __('CSS Classes', 'coworker'),
			'desc' => __('Optional CSS Classes.', 'coworker')
		),
		'style' => array(
			'std' => '',
			'type' => 'text',
			'label' => __('Style', 'coworker'),
			'desc' => __('Optional CSS Styles.', 'coworker')
		),
        'content' => array(
            'std' => '<li>List Item</li>',
            'type' => 'textarea',
            'label' => __('List Items', 'coworker'),
            'desc' => __('Add your List Items here.', 'coworker')
        )
	),
	'shortcode' => '[iconlist icon="{{icon}}" class="{{class}}" style="{{style}}"]{{content}}[/iconlist]',
	'popup_title' => __('Insert Icon List', 'coworker')
);


/*-----------------------------------------------------------------------------------*/
/*	Columns
/*-----------------------------------------------------------------------------------*/

$zilla_shortcodes['columns'] = array(
	'params' => array(),
	'shortcode' => ' {{child_shortcode}} ', // as there is no wrapper shortcode
	'popup_title' => __('Insert Columns Shortcode', 'coworker'),
	'no_preview' => true,

	// child shortcode is clonable & sortable
	'child_shortcode' => array(
		'params' => array(
			'column' => array(
				'type' => 'select',
				'label' => __('Column Type', 'coworker'),
				'desc' => __('Select the type, ie width of the column.', 'coworker'),
				'options' => array(
                    'full' => 'Full',
					'half' => 'One Half',
					'half_last' => 'One Half Last',
					'one_third' => 'One Third',
					'one_third_last' => 'One Third Last',
					'two_third' => 'Two Thirds',
					'two_third_last' => 'Two Thirds Last',
					'one_fourth' => 'One Fourth',
					'one_fourth_last' => 'One Fourth Last',
					'three_fourth' => 'Three Fourth',
					'three_fourth_last' => 'Three Fourth Last',
					'one_fifth' => 'One Fifth',
					'one_fifth_last' => 'One Fifth Last',
					'two_fifth' => 'Two Fifth',
					'two_fifth_last' => 'Two Fifth Last',
					'three_fifth' => 'Three Fifth',
					'three_fifth_last' => 'Three Fifth Last',
					'four_fifth' => 'Four Fifth',
					'four_fifth_last' => 'Four Fifth Last',
					'one_sixth' => 'One Sixth',
					'one_sixth_last' => 'One Sixth Last',
					'five_sixth' => 'Five Sixth',
					'five_sixth_last' => 'Five Sixth Last'
				)
			),
			'content' => array(
				'std' => '',
				'type' => 'textarea',
				'label' => __('Column Content', 'coworker'),
				'desc' => __('Add the column content', 'coworker'),
			),
            'class' => array(
                'std' => '',
                'type' => 'text',
                'label' => __('Column Class', 'coworker'),
                'desc' => __('Add a CSS Class to the Column. Optional', 'coworker'),
            )
		),
		'shortcode' => '[{{column}} class="{{class}}"]{{content}}[/{{column}}]',
		'clone_button' => __('Add Column', 'coworker')
	)
);

?>