<?php

$prefix = 'semi_page_';

global $pageportmeta_boxes;

$pageportmeta_boxes = array();


$pageportmeta_boxes[] = array(
	'id' => 'sidebarsettings',
	'title' => __( 'Sidebar Settings', 'coworker' ),
	'pages' => array( 'page' ),
	'context' => 'normal',
	'priority' => 'high',
	'fields' => array(
		
        array(
            'name' => __( 'Sidebar', 'coworker' ),
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
		'template' => array( 'template-portfolios.php', 'template-portfoliosp.php', 'template-portfolio2s.php', 'template-portfolio2sp.php', 'template-portfolio3s.php', 'template-portfolio3sp.php' )
	)
);


$pageportmeta_boxes[] = array(
	'id' => 'pageportfolio',
	'title' => __( 'Portfolio Settings', 'coworker' ),
	'pages' => array( 'page' ),
	'context' => 'normal',
	'priority' => 'high',
	'fields' => array(
		
        array(
			'name' => __( 'Portfolio Categories', 'coworker' ),
			'id'   => $prefix . 'port_cats',
			'type'    => 'taxonomy',
			'options' => array(
				'taxonomy' => 'port-group',
				'type' => 'checkbox_list'
			),
			'desc' => __( 'Choose the Categories you want to display on this Portfolio Page. Leave Blank if you want to show items from all categories', 'coworker' )
		),
		array(
            'name' => __( 'Enable Hash History', 'coworker' ),
			'id' => $prefix . 'port_hash_history',
			'type' => 'checkbox',
			'desc' => __( 'Check to Enable Hash History for the Filter on this Portfolio Page', 'coworker' ),
			'std' => 0
		)
        
    ),
	'only_on'    => array(
		'template' => array( 'template-portfolio.php', 'template-portfolios.php', 'template-portfolio2.php', 'template-portfolio2s.php', 'template-portfolio3.php', 'template-portfolio3s.php', 'template-portfolio5.php' )
	)
);


$pageportmeta_boxes[] = array(
	'id' => 'pageportfolio',
	'title' => __( 'Portfolio Settings', 'coworker' ),
	'pages' => array( 'page' ),
	'context' => 'normal',
	'priority' => 'high',
	'fields' => array(
		
        array(
            'name' => __( 'No. of Items', 'coworker' ),
			'id' => $prefix . 'portfolio_itemcount',
			'desc' => __( 'Enter the No. of Items you want to show on this Portfolio Items Page. Enter a Number only', 'coworker' ),
			'type'  => 'text',
			'std' => '12',
            'size' => '2'
		),
        array(
			'name' => __( 'Portfolio Categories', 'coworker' ),
			'id'   => $prefix . 'port_cats',
			'type'    => 'taxonomy',
			'options' => array(
				'taxonomy' => 'port-group',
				'type' => 'checkbox_list'
			),
			'desc' => __( 'Choose the Categories you want to display on this Portfolio Page. Do not check anything if you want to show items from all categories', 'coworker' )
		),
		array(
            'name' => __( 'Enable jQuery Pagination', 'coworker' ),
			'id' => $prefix . 'port_jquery_pagi',
			'type' => 'checkbox',
			'desc' => __( 'Check to Enable jQuery Pagination on this Portfolio Page', 'coworker' ),
			'std' => 0
		)
        
    ),
	'only_on'    => array(
		'template' => array( 'template-portfoliop.php', 'template-portfoliosp.php', 'template-portfolio2p.php', 'template-portfolio2sp.php', 'template-portfolio3p.php', 'template-portfolio3sp.php' )
	)
);


function semi_pageport_register_meta_boxes() {

    global $pageportmeta_boxes;

	// Make sure there's no errors when the plugin is deactivated or during upgrade
	if ( class_exists( 'RW_Meta_Box' ) ) {
		foreach ( $pageportmeta_boxes as $pageportmeta_box ) {
			if ( isset( $pageportmeta_box['only_on'] ) && ! rw_maybe_include( $pageportmeta_box['only_on'] ) ) {
				continue;
			}

			new RW_Meta_Box( $pageportmeta_box );
		}
	}

}

add_action( 'admin_init', 'semi_pageport_register_meta_boxes' );


?>