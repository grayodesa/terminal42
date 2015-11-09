<?php

class Add_post_types {

    var $single;
    var $plural;
    var $singlealt;
    var $pluralalt;
    var $type;
    var $support;
    var $rewrite; // array( 'slug' => 'slug' )
    var $icon;

    function init($options){
        foreach($options as $key => $value){
            $this->$key = $value;
        }
    }

    function add_post_type() {
    
        $labels = array(
            'name' => _x($this->plural, 'post type general name'),
            'singular_name' => _x($this->single, 'post type singular name'),
            'add_new' => _x('Add New', $this->type),
            'add_new_item' => __('Add New ' . $this->singlealt),
            'edit_item' => __('Edit ' . $this->singlealt),
            'new_item' => __('New ' . $this->singlealt),
            'all_items' => __('All ' . $this->pluralalt),
            'view_item' => __('View ' . $this->singlealt),
            'search_items' => __('Search ' . $this->pluralalt),
            'not_found' =>  __('No ' . $this->pluralalt . ' Found'),
            'not_found_in_trash' => __('No ' . $this->pluralalt . ' found in Trash'), 
            'parent_item_colon' => '',
            'menu_name' => $this->single
        );
        
        $options = array(
            'labels' => $labels,
            'public' => true,
            'exclude_from_search' => false,
            'publicly_queryable' => true,
            'show_ui' => true,
            'show_in_menu' => true,
            'query_var' => true,
            'rewrite' => $this->rewrite,
            'capability_type' => 'post',
            'has_archive' => true,
            'hierarchical' => false,
            'menu_position' => null,
            'supports' => $this->support,
            'menu_icon' => $this->icon
          );
          
        register_post_type($this->type, $options);
    
    }

    function add_messages ( $messages ) {
        
        global $post;

        $messages[$this->type] = array(
        0 => '', 
        1 => sprintf( __($this->singlealt . ' updated. <a href="%s">View ' . $this->singlealt . '</a>'), esc_url( get_permalink( get_the_ID() ) ) ),
        2 => __('Custom field updated.', 'coworker'),
        3 => __('Custom field deleted.', 'coworker'),
        4 => __($this->singlealt . ' updated.'),
        5 => isset($_GET['revision']) ? sprintf( __($this->singlealt .' restored to revision from %s'), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
        6 => sprintf( __($this->singlealt . ' published. <a href="%s">View ' . $this->singlealt . '</a>'), esc_url( get_permalink( get_the_ID() ) ) ),
        7 => __($this->singlealt . ' saved.'),
        8 => sprintf( __($this->singlealt . ' submitted. <a target="_blank" href="%s">Preview ' . $this->singlealt . '</a>'), esc_url( add_query_arg( 'preview', 'true', get_permalink( get_the_ID() ) ) ) ),
        9 => sprintf( __($this->singlealt . ' scheduled for: <strong>%1$s</strong>. <a target="_blank" href="%2$s">Preview ' . $this->singlealt . '</a>'),
          date_i18n( __( 'M j, Y @ G:i', 'coworker' ), strtotime( $post->post_date ) ), esc_url( get_permalink( get_the_ID() ) ) ),
        10 => sprintf( __($this->singlealt . ' draft updated. <a target="_blank" href="%s">Preview ' . $this->singlealt . '</a>'), esc_url( add_query_arg( 'preview', 'true', get_permalink( get_the_ID() ) ) ) ),
        );

        return $messages;
    }

}