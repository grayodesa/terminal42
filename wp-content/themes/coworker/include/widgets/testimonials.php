<?php

/*-------------------------------------------------

	Plugin Name: Testimonials Scroller
	Plugin URI: http://themeforest.net/user/SemiColonWeb
	Description: Widget to show the Testimonials
	Version: 1.0
	Author: SemiColonWeb
	Author URI: http://themeforest.net/user/SemiColonWeb

---------------------------------------------------*/

class SEMI_Testimonials extends WP_Widget {

	function SEMI_Testimonials() {
	$widget_ops = array( 'classname' => 'testimonial-widget', 'description' => __( 'Show what your Clients say about you', 'coworker' ) );
		$this->WP_Widget( 'testimonials_widget', __( 'CoWorker: Testimonials', 'coworker' ), $widget_ops);
	}
	
	function form($instance) {
	
		
		$instance = wp_parse_args( (array) $instance, array('title' => 'Testimonials', 'number' => 5, 'display' => 'recent', 'auto' => 'false', 'speed' => '500', 'pause' => '8000', 'limit' => '') );

        $title = esc_attr($instance['title']);
		$number = absint($instance['number']);
        $display = $instance['display'];
        $auto = $instance['auto'];
        $speed = $instance['speed'];
        $pause = $instance['pause'];
        $limit = $instance['limit'];

?>
		<p>
            <label for="<?php echo $this->get_field_id('title'); ?>">
               <?php _e('Title:', 'coworker'); ?>
            </label>
            <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
        </p>

		<p>
            <label for="<?php echo $this->get_field_id('number'); ?>">
               <?php _e('No. of Items:', 'coworker'); ?>
            </label>
            <input class="widefat" id="<?php echo $this->get_field_id('number'); ?>" name="<?php echo $this->get_field_name('number'); ?>" type="text" value="<?php echo $number; ?>" />
        </p>
        
        <p>
            <label for="<?php echo $this->get_field_id('display'); ?>">
               <?php _e('Display Type:', 'coworker'); ?>
            </label>
            <select id="<?php echo $this->get_field_id('display'); ?>" name="<?php echo $this->get_field_name('display'); ?>">
                <option value="recent" <?php if ( $display == 'recent' ) echo 'selected'; ?>>Recent</option>
                <option value="random" <?php if ( $display == 'random' ) echo 'selected'; ?>>Random</option>
                <option value="menu_order" <?php if ( $display == 'menu_order' ) echo 'selected'; ?>>Sort Order</option>
            </select>
        </p>
        
        <p>
            <label for="<?php echo $this->get_field_id('auto'); ?>">
               <?php _e('Auto Rotate:', 'coworker'); ?>
            </label>
            <select id="<?php echo $this->get_field_id('auto'); ?>" name="<?php echo $this->get_field_name('auto'); ?>">
                <option value="true" <?php if ( $auto == 'true' ) echo 'selected'; ?>>True</option>
                <option value="false" <?php if ( $auto == 'false' ) echo 'selected'; ?>>False</option>
            </select>
        </p>
        
        <p>
            <label for="<?php echo $this->get_field_id('speed'); ?>">
               <?php _e('Animation Speed:', 'coworker'); ?>
            </label>
            <input id="<?php echo $this->get_field_id('speed'); ?>" name="<?php echo $this->get_field_name('speed'); ?>" type="text" value="<?php echo $speed; ?>" size="5" />
        </p>
        
        <p>
            <label for="<?php echo $this->get_field_id('pause'); ?>">
               <?php _e('Pause Time:', 'coworker'); ?>
            </label>
            <input id="<?php echo $this->get_field_id('pause'); ?>" name="<?php echo $this->get_field_name('pause'); ?>" type="text" value="<?php echo $pause; ?>" size="5" />
        </p>
        
        <p>
            <label for="<?php echo $this->get_field_id('limit'); ?>">
               <?php _e('Text Limit:', 'coworker'); ?>
            </label>
            <input class="widefat" id="<?php echo $this->get_field_id('limit'); ?>" name="<?php echo $this->get_field_name('limit'); ?>" type="text" value="<?php echo $limit; ?>" />
        </p>


<?php
    }

	function update($new_instance, $old_instance) {
        $instance=$old_instance;

        $instance['title'] = strip_tags($new_instance['title']);
        $instance['number'] = $new_instance['number'];
        $instance['display'] = $new_instance['display'];
        $instance['auto'] = $new_instance['auto'];
        $instance['speed'] = $new_instance['speed'];
        $instance['pause'] = $new_instance['pause'];
        $instance['limit'] = $new_instance['limit'];
        return $instance;

    }

	function widget($args, $instance) {
	
		extract($args);

		$title = apply_filters('widget_title', $instance['title']);
		if ( empty($title) ) $title = false;

		$number = absint( $instance['number'] );
        $display = $instance['display'];
        $auto = $instance['auto'];
        $speed = is_numeric( $instance['speed'] ) ? $instance['speed'] : 900;
        $pause = is_numeric( $instance['pause'] ) ? $instance['pause'] : 8000;
        $limit = is_numeric( $instance['limit'] ) ? $instance['limit'] : '';
			
		echo $before_widget;
	
		if($title){
			echo $before_title;
			echo $title; 
			echo $after_title;
		}
        
        echo do_shortcode( '[testimonials number="' . $number . '" display="' . $display . '" auto="' . $auto . '" speed="' . $speed . '" pause="' . $pause . '" tlimit="' . $limit . '"]' );
        		
		echo $after_widget;
		
	}

}


add_action( 'widgets_init', 'semi_widget_testimonials' );
function semi_widget_testimonials() {
	register_widget('SEMI_Testimonials');
}
    
?>