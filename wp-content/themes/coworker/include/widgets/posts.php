<?php

/*-------------------------------------------------

	Plugin Name: Post List
	Plugin URI: http://themeforest.net/user/SemiColonWeb
	Description: Widget to show Popular or Recent Posts
	Version: 1.0
	Author: SemiColonWeb
	Author URI: http://themeforest.net/user/SemiColonWeb

---------------------------------------------------*/

class SEMI_Posts extends WP_Widget {

	function SEMI_Posts() {
	$widget_ops = array( 'classname' => 'posts-widget', 'description' => __( 'Displays your Recent or Popular Posts', 'coworker' ) );
		$this->WP_Widget( 'posts_widget', __( 'CoWorker: Posts List', 'coworker' ), $widget_ops);
	}
	
	function form($instance) {
	
		
		$instance = wp_parse_args( (array) $instance, array('title' => 'Recent Posts', 'number' => 3, 'display' => 'recent') );

        $title = esc_attr($instance['title']);
		$number = absint($instance['number']);
        $display = $instance['display'];

?>
		<p>
            <label for="<?php echo $this->get_field_id('title'); ?>">
               <?php _e('Title:', 'coworker'); ?>
            </label>
            <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
        </p>

		<p>
            <label for="<?php echo $this->get_field_id('number'); ?>">
               <?php _e('No. of Posts:', 'coworker'); ?>
            </label>
            <input class="widefat" id="<?php echo $this->get_field_id('number'); ?>" name="<?php echo $this->get_field_name('number'); ?>" type="text" value="<?php echo $number; ?>" />
        </p>
        
        <p>
            <label for="<?php echo $this->get_field_id('display'); ?>">
               <?php _e('Display Type:', 'coworker'); ?>
            </label>
            <select id="<?php echo $this->get_field_id('display'); ?>" name="<?php echo $this->get_field_name('display'); ?>">
                <option value="recent" <?php if ( $display == 'recent' ) echo 'selected'; ?>>recent</option>
                <option value="popular" <?php if ( $display == 'popular' ) echo 'selected'; ?>>popular</option>
                <option value="random" <?php if ( $display == 'random' ) echo 'selected'; ?>>random</option>
            </select>
        </p>


<?php
    }

	function update($new_instance, $old_instance) {
        $instance=$old_instance;

        $instance['title'] = strip_tags($new_instance['title']);
        $instance['number'] = $new_instance['number'];
        $instance['display'] = $new_instance['display'];
        return $instance;

    }

	function widget($args, $instance) {
	
		extract($args);

		$title = apply_filters('widget_title', $instance['title']);
		if ( empty($title) ) $title = false;

		$number = absint( $instance['number'] );
        $display = $instance['display'];
		
			
		echo $before_widget;
	
		if($title){
			echo $before_title;
			echo $title; 
			echo $after_title;
		}
        
        
        if( $display == 'recent' ) {
            
            $args = array( 'post_type' => 'post', 'posts_per_page' => $number, 'orderby' => 'date' );
            
        } elseif( $display == 'popular' ) {
            
            $args = array( 'post_type' => 'post', 'posts_per_page' => $number, 'orderby' => 'comment_count' );
            
        } elseif( $display == 'random' ) {
            
            $args = array( 'post_type' => 'post', 'posts_per_page' => $number, 'orderby' => 'rand' );
            
        } else {
            
            $args = array( 'post_type' => 'post', 'posts_per_page' => $number, 'orderby' => 'date' );
            
        }
        
		
        $postswidget = new WP_Query( $args );
        
        if( $postswidget->have_posts() ):
        
        echo '<ul class="sposts-list clearfix">';
        
        while ( $postswidget->have_posts() ) : $postswidget->the_post();
        
        $thumb = wp_get_attachment_image_src( get_post_thumbnail_id( get_the_ID() ), 'full');
        
        $thumb = semi_resize( $thumb[0], 64, 64, true, false );
		
        ?>

			<li class="clearfix">

                <?php if ( $thumb[0] ) { ?>
                
                <div class="spost-image">
                    <a href="<?php the_permalink(); ?>"><img src="<?php echo $thumb[0]; ?>" width="<?php echo $thumb[1]; ?>" height="<?php echo $thumb[2]; ?>" alt="<?php the_title_attribute(); ?>" /></a>
                </div>
                
                <?php } ?>
            
                <div class="spost-content clearfix">
                    <div class="spost-title"><a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a></div>
                    <div class="spost-meta clearfix">
                        <ul>
                            <li><i class="icon-calendar"></i> <?php the_time( __( 'jS M', 'coworker' ) ); ?></li>
                            <li><span>&middot;</span><a href="<?php echo get_permalink().'#comments'; ?>"><i class="icon-comments-alt"></i> <?php comments_number( '0', '1', '%'); ?></a></li>
                        </ul>
                    </div>
                </div>

            </li>
        
        <?php
        
        endwhile;
        
        echo '</ul>';
        
        endif;
            
        wp_reset_postdata();
        		
		echo $after_widget;
		
	}

}


add_action( 'widgets_init', 'semi_widget_posts' );
function semi_widget_posts() {
	register_widget('SEMI_Posts');
}
    
?>