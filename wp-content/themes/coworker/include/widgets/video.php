<?php

/*-------------------------------------------------

	Plugin Name: Video Widget
	Plugin URI: http://themeforest.net/user/SemiColonWeb
	Description: Widget to show your Videos
	Version: 1.0
	Author: SemiColonWeb
	Author URI: http://themeforest.net/user/SemiColonWeb

---------------------------------------------------*/

class SEMI_Video extends WP_Widget {

	function SEMI_Video() {
	$widget_ops = array( 'classname' => 'video-widget', 'description' => __( 'Displays your Embedded Videos', 'coworker' ) );
		$this->WP_Widget( 'video_widget', __( 'CoWorker: Video Embed', 'coworker' ), $widget_ops);
	}
	
	function form($instance) {
	
		
		$instance = wp_parse_args( (array) $instance, array('title' => 'Featured Video', 'videoembed' => '') );

        $title = esc_attr($instance['title']);
		$videoembed = $instance['videoembed'];

?>
		<p>
            <label for="<?php echo $this->get_field_id('title'); ?>">
               <?php _e('Title:', 'coworker'); ?>
            </label>
            <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
        </p>

		<p>
            <label for="<?php echo $this->get_field_id('videoembed'); ?>">
               <?php _e('Video Embed Code:', 'coworker'); ?>
            </label>
            <textarea class="widefat" rows="10" cols="30" id="<?php echo $this->get_field_id('videoembed'); ?>" name="<?php echo $this->get_field_name('videoembed'); ?>"><?php echo stripslashes( htmlspecialchars( ( $videoembed ), ENT_QUOTES ) ); ?></textarea>
            <span style="display: block;color: #888;margin-top: 5px;"><?php _e( 'Enter your Video Embed Code from Youtube, Vimeo or any other Website.', 'coworker' ); ?></span>
        </p>

<?php
    }

	function update($new_instance, $old_instance) {
        $instance=$old_instance;

        $instance['title'] = strip_tags($new_instance['title']);
        $instance['videoembed'] = stripslashes( $new_instance['videoembed'] );
        return $instance;

    }

	function widget($args, $instance) {
	
		extract($args);

		$title = apply_filters('widget_title', $instance['title']);
		if ( empty($title) ) $title = false;

		$videoembed = $instance['videoembed'];
		
			
		echo $before_widget;
	
		if($title){
			echo $before_title;
			echo $title; 
			echo $after_title;
		}
        
        echo '<div>' . $videoembed . '</div>';
        		
		echo $after_widget;
		
	}

}


add_action( 'widgets_init', 'semi_widget_video' );
function semi_widget_video() {
	register_widget('SEMI_Video');
}
    
?>