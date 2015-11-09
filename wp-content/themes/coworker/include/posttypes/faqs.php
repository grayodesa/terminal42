<?php

require_once('posttypes.class.php');

$faqs_posttype = new Add_post_types;

$options = array(
  'single' => __( 'FAQs', 'coworker' ),
  'plural' => __( 'FAQs', 'coworker' ),
  'singlealt' => __( 'FAQ', 'coworker' ),
  'pluralalt' => __( 'FAQs', 'coworker' ),
  'type' => 'faqs',
  'support' => array('title','editor','comments'),
  'rewrite' => array( 'slug' => 'faqs-item' ),
  'icon' => 'dashicons-editor-help'
);

$faqs_posttype->init($options);

add_action('init', array(&$faqs_posttype, 'add_post_type'));
add_filter('post_updated_messages', array(&$faqs_posttype, 'add_messages'));


add_action( 'init', 'create_faqs_taxonomies', 0 );

function create_faqs_taxonomies() {

  $labels = array(
    'name' => _x( 'Categories', 'taxonomy general name', 'coworker' ),
    'singular_name' => _x( 'Category', 'taxonomy singular name', 'coworker' ),
    'search_items' =>  __( 'Search Categories', 'coworker' ),
    'popular_items' => __( 'Popular Categories', 'coworker' ),
    'all_items' => __( 'All Categories', 'coworker' ),
    'parent_item' => null,
    'parent_item_colon' => null,
    'edit_item' => __( 'Edit Category', 'coworker' ),
    'update_item' => __( 'Update Category', 'coworker' ),
    'add_new_item' => __( 'Add New Category', 'coworker' ),
    'new_item_name' => __( 'New Category Name', 'coworker' ),
    'separate_items_with_commas' => null,
    'add_or_remove_items' => null,
    'choose_from_most_used' => null,
    'menu_name' => __( 'Categories', 'coworker' )
  ); 

  register_taxonomy('faqs-group','faqs',array(
    'hierarchical' => true,
    'labels' => $labels,
    'show_ui' => true,
    'query_var' => true,
    'rewrite' => array( 'slug' => 'faqs-group', 'hierarchical' => true )
  ));
  
}


function semi_columns_head_faqs($defaults) {
    $defaults['faqs_group'] = __( 'Category', 'coworker' );
    return $defaults;
}

function semi_columns_content_faqs($column_name, $faqs_ID) {
    if ($column_name == 'faqs_group') {
        
        $getterms = get_the_terms($team_ID, 'faqs-group');
        
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

add_filter('manage_faqs_posts_columns', 'semi_columns_head_faqs', 10);
add_action('manage_faqs_posts_custom_column', 'semi_columns_content_faqs', 10, 2);


?>