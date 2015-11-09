<?php

require_once('posttypes.class.php');

$client_posttype = new Add_post_types;

$options = array(
  'single' => __( 'Clients', 'coworker' ),
  'plural' => __( 'Clients', 'coworker' ),
  'singlealt' => __( 'Client', 'coworker' ),
  'pluralalt' => __( 'Clients', 'coworker' ),
  'type' => 'clients',
  'support' => array('title','thumbnail'),
  'rewrite' => false,
  'icon' => 'dashicons-businessman'
);

$client_posttype->init($options);

add_action('init', array(&$client_posttype, 'add_post_type'));
add_filter('post_updated_messages', array(&$client_posttype, 'add_messages'));


function semi_columns_head_clients($defaults) {
    $defaults['clients_logo_image'] = __( 'Client\'s Logo', 'coworker' );
    return $defaults;
}

function semi_columns_content_clients($column_name, $clients_ID) {
    if ($column_name == 'clients_logo_image') {
        $thumb = wp_get_attachment_image_src( get_post_thumbnail_id($clients_ID), 'full');
        echo '<img src="' . semi_resize( $thumb[0], 200, 163, true ) . '" title="'. get_the_title($clients_ID) .'" alt="'. get_the_title($clients_ID) .'" />';
    }
}

add_filter('manage_clients_posts_columns', 'semi_columns_head_clients', 10);
add_action('manage_clients_posts_custom_column', 'semi_columns_content_clients', 10, 2);


?>