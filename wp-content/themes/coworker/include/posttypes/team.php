<?php

require_once('posttypes.class.php');

$team_posttype = new Add_post_types;

$options = array(
  'single' => __( 'Team', 'coworker' ),
  'plural' => __( 'Team Members', 'coworker' ),
  'singlealt' => __( 'Team', 'coworker' ),
  'pluralalt' => __( 'Team Members', 'coworker' ),
  'type' => 'team',
  'support' => array('title','thumbnail'),
  'rewrite' => false,
  'icon' => 'dashicons-groups'
);

$team_posttype->init($options);

add_action('init', array(&$team_posttype, 'add_post_type'));
add_filter('post_updated_messages', array(&$team_posttype, 'add_messages'));


function semi_columns_head_team($defaults) {
    $defaults['team_designation'] = __( 'Designation', 'coworker' );
    $defaults['team_pic'] = __( 'Picture', 'coworker' );
    return $defaults;
}

function semi_columns_content_team($column_name, $team_ID) {
    if ($column_name == 'team_designation') {
        echo get_post_meta($team_ID, 'semi_team_designation', true);
    } elseif ($column_name == 'team_pic') {
        $thumb = wp_get_attachment_image_src( get_post_thumbnail_id($team_ID), 'thumbnail');
        echo '<img src="' . $thumb[0] . '" title="'. get_the_title($team_ID) .'" alt="'. get_the_title($team_ID) .'" />';
    }
}

add_filter('manage_team_posts_columns', 'semi_columns_head_team', 10);
add_action('manage_team_posts_custom_column', 'semi_columns_content_team', 10, 2);


?>