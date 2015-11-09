<?php

require_once('posttypes.class.php');

$slider_posttype = new Add_post_types;

$options = array(
  'single' => __( 'Slider', 'coworker' ),
  'plural' => __( 'Slider', 'coworker' ),
  'singlealt' => __( 'Slider Item', 'coworker' ),
  'pluralalt' => __( 'Slider Items', 'coworker' ),
  'type' => 'slider',
  'support' => array('title','thumbnail'),
  'rewrite' => false,
  'icon' => 'dashicons-images-alt2'
);

$slider_posttype->init($options);

add_action('init', array(&$slider_posttype, 'add_post_type'));
add_filter('post_updated_messages', array(&$slider_posttype, 'add_messages'));



add_action( 'init', 'create_slider_taxonomies', 0 );

function create_slider_taxonomies() {

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

  register_taxonomy('slider-group','slider',array(
    'hierarchical' => true,
    'labels' => $labels,
    'show_ui' => true,
    'query_var' => true,
    'rewrite' => false
  ));
  
}



function semi_columns_head_slider($defaults) {
    $defaults['slider_group'] = __( 'Group', 'coworker' );
    $defaults['slider_thumb'] = __( 'Thumbnail', 'coworker' );
    return $defaults;
}

function semi_columns_content_slider($column_name, $slider_ID) {
    if($column_name == 'slider_thumb') {
        if( !get_post_meta($slider_ID, 'semi_slider_video', true) ) {
            $thumb = wp_get_attachment_image_src( get_post_thumbnail_id($slider_ID), 'thumbnail');
            echo '<img src="' . $thumb[0] . '" title="'. get_the_title($slider_ID) .'" alt="'. get_the_title($slider_ID) .'" />';
        } else { _e( 'Video Slide', 'coworker' ); }
    } elseif($column_name == 'slider_group') {
        
        $getterms = get_the_terms($slider_ID, 'slider-group');
        
        if ($getterms) {
            
            $terms = array();
            
			foreach ($getterms as $getterm) {
                $terms[] = $getterm->name;
			}
            
            $terms = implode(", ", $terms);
        }
        
        echo $terms;
        
    }
}

add_filter('manage_slider_posts_columns', 'semi_columns_head_slider', 10);
add_action('manage_slider_posts_custom_column', 'semi_columns_content_slider', 10, 2);


?>