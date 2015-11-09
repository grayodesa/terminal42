<?php

$prefix = 'semi_page_';

global $pmeta_boxes;

$pmeta_boxes = array();


$pmeta_boxes[] = array(
	'id' => 'pagetitleoptions',
	'title' => __( 'Page Title Options', 'coworker' ),
	'pages' => array( 'page' ),
	'context' => 'normal',
	'priority' => 'high',
	'fields' => array(
		
        array(
            'name' => __( 'Hide Title', 'coworker' ),
			'id' => $prefix . 'hidetitle',
			'type' => 'checkbox',
			'desc' => __( 'Check to Hide the Title from the Page', 'coworker' ),
			'std' => 0
		),
        array(
            'name' => __( 'Caption', 'coworker' ),
			'id' => $prefix . 'caption',
			'desc' => __( 'Set your Page Caption to be displayed beside the Main Page Title', 'coworker' ),
			'type'  => 'text',
			'std' => '',
            'size' => '150'
		)
        
    )
);


$pmeta_boxes[] = array(
	'id' => 'titlestyling',
	'title' => __( 'Title Styling', 'coworker' ),
	'pages' => array( 'page', 'post', 'portfolio' ),
	'context' => 'normal',
	'priority' => 'high',
	'fields' => array(
		
        array(
			'name'  => __( 'Background Image', 'coworker' ),
			'desc' => __( 'Add a Background Image for your Page Title', 'coworker' ),
			'id' => $prefix . 'ptitle_bg_image',
			'type' => 'image_advanced',
			'max_file_uploads' => 1
		),
        array(
			'name'  => __( 'Background Pattern', 'coworker' ),
			'desc' => __( 'Add a Background Pattern for your Page Title', 'coworker' ),
			'id' => $prefix . 'ptitle_bg_pattern',
			'type' => 'image_advanced',
			'max_file_uploads' => 1
		),
        array(
            'name' => __( 'Background Color', 'coworker' ),
			'id' => $prefix . 'ptitle_bg_color',
			'type' => 'color',
			'desc' => __( 'Choose your Page Title\'s Background Color', 'coworker' )
		),
        array(
            'name' => __( 'Font Color', 'coworker' ),
			'id' => $prefix . 'ptitle_font_color',
			'type' => 'color',
			'desc' => __( 'Choose your Page Title\'s Font Color', 'coworker' )
		)
        
    )
);


$pmeta_boxes[] = array(
	'id' => 'sidebarsettings',
	'title' => __( 'Sidebar Settings', 'coworker' ),
	'pages' => array( 'page' ),
	'context' => 'normal',
	'priority' => 'high',
	'fields' => array(
		
        array(
            'name' => __( 'Sidebar Position', 'coworker' ),
			'id' => $prefix . 'sidebar',
			'type' => 'select',
			'options' => array(
				'right' => __( 'Right Sidebar', 'coworker' ),
				'left' => __( 'Left Sidebar', 'coworker' )
			),
			'std'  => array( 'right' ),
			'desc' => __( 'Select the position of the Sidebar on this page', 'coworker' )
		)
        
    ),
	'only_on'    => array(
		'template' => array( 'template-contact-sidebar.php', 'template-contact-split.php', 'template-faqs.php', 'template-blog-alt.php', 'template-blog-full.php', 'template-blog-full-alt.php', 'template-blog-small.php', 'template-blog-small-full.php', 'template-slider-sidebar.php' )
	)
);


function semi_page_register_meta_boxes() {

    global $pmeta_boxes;

	// Make sure there's no errors when the plugin is deactivated or during upgrade
	if ( class_exists( 'RW_Meta_Box' ) ) {
		foreach ( $pmeta_boxes as $pmeta_box ) {
			if ( isset( $pmeta_box['only_on'] ) && ! rw_maybe_include( $pmeta_box['only_on'] ) ) {
				continue;
			}

			new RW_Meta_Box( $pmeta_box );
		}
	}

}

add_action( 'admin_init', 'semi_page_register_meta_boxes' );


function rw_maybe_include( $conditions ) {
	// Include in back-end only
	if ( ! defined( 'WP_ADMIN' ) || ! WP_ADMIN ) {
		return false;
	}

	// Always include for ajax
	if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
		return true;
	}

	if ( isset( $_GET['post'] ) ) {
		$post_id = $_GET['post'];
	}
	elseif ( isset( $_POST['post_ID'] ) ) {
		$post_id = $_POST['post_ID'];
	}
	else {
		$post_id = false;
	}

	$post_id = (int) $post_id;
	$post = get_post( $post_id );

	foreach ( $conditions as $cond => $v ) {
		// Catch non-arrays too
		if ( ! is_array( $v ) ) {
			$v = array( $v );
		}

		switch ( $cond ) {
			case 'id':
				if ( in_array( $post_id, $v ) ) {
					return true;
				}
			break;
			case 'parent':
				$post_parent = $post->post_parent;
				if ( in_array( $post_parent, $v ) ) {
					return true;
				}
			break;
			case 'slug':
				$post_slug = $post->post_name;
				if ( in_array( $post_slug, $v ) ) {
					return true;
				}
			break;
			case 'template':
				$template = get_post_meta( $post_id, '_wp_page_template', true );
				if ( in_array( $template, $v ) ) {
					return true;
				}
			break;
            case 'meta':
				if ( $v['value'] == get_post_meta( $post_id, $v['key'], true ) ) {
					return true;
				}
			break;
		}
	}

	// If no condition matched
	return false;
}


?>