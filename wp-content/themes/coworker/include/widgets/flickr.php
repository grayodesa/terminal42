<?php

/*-------------------------------------------------

	Plugin Name: Flickr Photostream
	Plugin URI: http://themeforest.net/user/SemiColonWeb
	Description: Widget to show Flickr Photostream
	Version: 1.0
	Author: SemiColonWeb
	Author URI: http://themeforest.net/user/SemiColonWeb

---------------------------------------------------*/

class SEMI_Flickr extends WP_Widget {

	function SEMI_Flickr() {	
	$widget_ops = array( 'classname' => 'flickr-widget', 'description' => __( 'Show Off your Flickr Photostream', 'coworker' ) );
		$this->WP_Widget( 'flickr_widget', __( 'CoWorker: Flickr Photostream', 'coworker' ), $widget_ops);
	}
	
	function form($instance) {
	
		
		$instance = wp_parse_args( (array) $instance, array('title' => 'Flickr Photostream', 'flickr_id' => '52617155@N08', 'idtype' => 'user', 'number' => 9) );

        $title = esc_attr($instance['title']);
        $flickr_id = $instance['flickr_id'];
		$idtype = $instance['idtype'];
		$number = absint($instance['number']);

?>
		<p>
            <label for="<?php echo $this->get_field_id('title'); ?>">
               <?php _e('Title:', 'coworker'); ?>
            </label>
            <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
        </p>		

		<p>
            <label for="<?php echo $this->get_field_id('flickr_id'); ?>">
               <?php _e('Flickr ID:', 'coworker'); ?>
            </label>
                <input class="widefat" id="<?php echo $this->get_field_id('flickr_id'); ?>" name="<?php echo $this->get_field_name('flickr_id'); ?>" type="text" value="<?php echo $flickr_id; ?>" />
            <br />Get your <a href="http://idgettr.com/" target="_blank">Flickr ID</a>    
        </p>
        
        <p>
            <label for="<?php echo $this->get_field_id('idtype'); ?>">
               <?php _e('Flickr ID Type:', 'coworker'); ?>
            </label>
            <select id="<?php echo $this->get_field_id('idtype'); ?>" name="<?php echo $this->get_field_name('idtype'); ?>">
                <option value="user" <?php if ( $idtype == 'user' ) echo 'selected'; ?>>user</option>
                <option value="group" <?php if ( $idtype == 'group' ) echo 'selected'; ?>>group</option>
            </select>
        </p>

		<p>
            <label for="<?php echo $this->get_field_id('number'); ?>">
               <?php _e('No. of Photos:', 'coworker'); ?>
            </label>
            <input size="5" id="<?php echo $this->get_field_id('number'); ?>" name="<?php echo $this->get_field_name('number'); ?>" type="text" value="<?php echo $number; ?>" />
        </p>


<?php
    }

	function update($new_instance, $old_instance) {
        $instance=$old_instance;

        $instance['title'] = strip_tags($new_instance['title']);
        $instance['flickr_id'] = $new_instance['flickr_id'];
        $instance['idtype'] = $new_instance['idtype'];
        $instance['number'] = $new_instance['number'];
        return $instance;

    }

	function widget($args, $instance) {
	
		extract($args);

		$title = apply_filters('widget_title', $instance['title']);
		if ( empty($title) ) $title = false;

        $flickr_id = $instance['flickr_id'];
        $idtype = $instance['idtype'];
		$number = absint( $instance['number'] );
		
		if (!empty($flickr_id)) {
		
			
			echo $before_widget;
		
			if($title){
				echo $before_title;
				echo $title; 
				echo $after_title;
			}
			
            ?>
            
            <div id="flickr_widget" class="flickrfeed" data-id="<?php echo $flickr_id; ?>" data-count="<?php echo $number; ?>" data-type="<?php echo $idtype; ?>"></div>
            
            <?php
            		
			echo $after_widget;

		}
		
	}

}


add_action( 'widgets_init', 'semi_widget_flickr' );
function semi_widget_flickr() {
	register_widget('SEMI_Flickr');
}
    
?>