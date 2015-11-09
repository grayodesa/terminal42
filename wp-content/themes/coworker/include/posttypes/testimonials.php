<?php

require_once('posttypes.class.php');

$testimonial_posttype = new Add_post_types;

$options = array(
  'single' => __( 'Testimonials', 'coworker' ),
  'plural' => __( 'Testimonials', 'coworker' ),
  'singlealt' => __( 'Testimonial', 'coworker' ),
  'pluralalt' => __( 'Testimonials', 'coworker' ),
  'type' => 'testimonials',
  'support' => array('title','thumbnail'),
  'rewrite' => false,
  'icon' => 'dashicons-format-quote'
);

$testimonial_posttype->init($options);

add_action('init', array(&$testimonial_posttype, 'add_post_type'));
add_filter('post_updated_messages', array(&$testimonial_posttype, 'add_messages'));


function semi_columns_head_testimonials($defaults) {
    $defaults['testimonials_author'] = __( 'Author', 'coworker' );
    $defaults['testimonials_text'] = __( 'Testimonial', 'coworker' );
    return $defaults;
}

function semi_columns_content_testimonials($column_name, $testimonials_ID) {
    if ($column_name == 'testimonials_author') {
        echo get_post_meta($testimonials_ID, 'semi_testimonials_author', true);
    } elseif ($column_name == 'testimonials_text') {
        echo get_post_meta($testimonials_ID, 'semi_testimonials_text', true);
    }
}

add_filter('manage_testimonials_posts_columns', 'semi_columns_head_testimonials', 10);
add_action('manage_testimonials_posts_custom_column', 'semi_columns_content_testimonials', 10, 2);


?>