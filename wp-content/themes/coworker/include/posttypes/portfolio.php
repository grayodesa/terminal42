<?php

require_once('posttypes.class.php');

$portfolio_posttype = new Add_post_types;

$options = array(
  'single' => __( 'Portfolio', 'coworker' ),
  'plural' => __( 'Portfolio', 'coworker' ),
  'singlealt' => __( 'Portfolio Item', 'coworker' ),
  'pluralalt' => __( 'Portfolio Items', 'coworker' ),
  'type' => 'portfolio',
  'support' => array('title','editor','thumbnail','comments'),
  'rewrite' => array( 'slug' => 'portfolio-item' ),
  'icon' => 'dashicons-camera'
);

$portfolio_posttype->init($options);

add_action('init', array(&$portfolio_posttype, 'add_post_type'));
add_filter('post_updated_messages', array(&$portfolio_posttype, 'add_messages'));



add_action( 'init', 'create_portfolio_taxonomies', 0 );

function create_portfolio_taxonomies() {

  $labels = array(
    'name' => _x( 'Groups', 'taxonomy general name', 'coworker' ),
    'singular_name' => _x( 'Group', 'taxonomy general name', 'coworker' ),
    'search_items' =>  __( 'Search Groups', 'coworker' ),
    'popular_items' => __( 'Popular Groups', 'coworker' ),
    'all_items' => __( 'All Groups', 'coworker' ),
    'parent_item' => null,
    'parent_item_colon' => null,
    'edit_item' => __( 'Edit Group', 'coworker' ), 
    'update_item' => __( 'Update Group', 'coworker' ),
    'add_new_item' => __( 'Add New Group', 'coworker' ),
    'new_item_name' => __( 'New Group Name', 'coworker' ),
    'separate_items_with_commas' => null,
    'add_or_remove_items' => null,
    'choose_from_most_used' => null,
    'menu_name' => __( 'Groups', 'coworker' ),
  ); 

  register_taxonomy('port-group','portfolio',array(
    'hierarchical' => true,
    'labels' => $labels,
    'show_ui' => true,
    'query_var' => true,
    'rewrite' => array( 'slug' => 'portfolio-group', 'hierarchical' => true )
  ));
  
}



function semi_columns_head_portfolio($defaults) {
    $defaults['portfolio_group'] = __( 'Group', 'coworker' );
    $defaults['portfolio_type'] = __( 'Type', 'coworker' );
    $defaults['portfolio_thumb'] = __( 'Thumbnail', 'coworker' );
    return $defaults;
}

function semi_columns_content_portfolio($column_name, $portfolio_ID) {
    if($column_name == 'portfolio_group') {
        
        $getterms = get_the_terms($portfolio_ID, 'port-group');
        
        if ($getterms) {
            
            $terms = array();
            
			foreach ($getterms as $getterm) {
                $terms[] = $getterm->name;
			}
            
            $terms = implode(", ", $terms);
        }
        
        echo $terms;
        
    } elseif($column_name == 'portfolio_type') {
        
        echo ucwords( get_post_meta( $portfolio_ID, 'semi_port_type', true ) );
        
    } elseif($column_name == 'portfolio_thumb'){
        $thumb = wp_get_attachment_image_src( get_post_thumbnail_id($portfolio_ID), 'thumbnail');
        echo '<img src="' . $thumb[0] . '" title="'. get_the_title($portfolio_ID) .'" alt="'. get_the_title($portfolio_ID) .'" />';
    }
}

add_filter('manage_portfolio_posts_columns', 'semi_columns_head_portfolio', 10);
add_action('manage_portfolio_posts_custom_column', 'semi_columns_content_portfolio', 10, 2);


function semi_enable_portfolio_sort() {
    add_submenu_page('edit.php?post_type=portfolio', __( 'Sort Portfolio Items', 'coworker' ), __( 'Sort Portfolio', 'coworker' ), 'edit_posts', 'sort_portfolio_items', 'semi_sort_portfolio');
}
add_action('admin_menu' , 'semi_enable_portfolio_sort');
 
 
function semi_sort_portfolio() {
	$portfolio = new WP_Query('post_type=portfolio&posts_per_page=-1&orderby=menu_order&order=ASC');
?>
	<div class="wrap">
	<h2><?php _e( 'Sort Portfolio Items', 'coworker' ) ?> <img src="<?php echo get_template_directory_uri(); ?>/images/preloader.gif" id="loading-animation" /></h2>
	<ul id="portfolio-item-list">
	<?php while ( $portfolio->have_posts() ) : $portfolio->the_post(); ?>
		<li id="<?php the_ID(); ?>"><?php the_title(); ?> <span><?php echo ucwords( get_post_meta( get_the_ID(), 'semi_port_type', true ) ); ?></span></li>			
	<?php endwhile; ?>
	</div><!-- End div#wrap //-->
 
<?php
}


function semi_portfoliosort_print_scripts() {
	global $pagenow;
 
	$pages = array('edit.php');
	if (in_array($pagenow, $pages)) {
		wp_enqueue_script('jquery-ui-sortable');
		wp_enqueue_script('portfoliosortJS', get_template_directory_uri() . '/include/posttypes/portfoliosort.js');
	}
}
add_action( 'admin_print_scripts', 'semi_portfoliosort_print_scripts' );


function semi_portfoliosort_print_styles() {
	global $pagenow;
 
	$pages = array('edit.php');
	if (in_array($pagenow, $pages))
		wp_enqueue_style('portfoliosortCSS', get_template_directory_uri() . '/include/posttypes/portfoliosort.css');
}
add_action( 'admin_print_styles', 'semi_portfoliosort_print_styles' );

 
function semi_save_portfoliosort_order() {
	global $wpdb;
 
	$order = explode(',', $_POST['portfoliosortorder']);
	$counter = 0;
 
	foreach ($order as $portfolio_id) {
		$wpdb->update($wpdb->posts, array( 'menu_order' => $counter ), array( 'ID' => $portfolio_id) );
		$counter++;
	}
	die(1);
}
add_action('wp_ajax_portfolio_sort', 'semi_save_portfoliosort_order');

?>