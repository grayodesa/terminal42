<?php

/*-------------------------------------------------

	Plugin Name: Instagram Photos
	Plugin URI: http://themeforest.net/user/SemiColonWeb
	Description: Widget to show Instagram Photos
	Version: 1.0
	Author: SemiColonWeb
	Author URI: http://themeforest.net/user/SemiColonWeb

---------------------------------------------------*/

class SEMI_Instagram extends WP_Widget {

	function SEMI_Instagram() {	
	$widget_ops = array( 'classname' => 'flickr-widget', 'description' => __( 'Show Off your Instagram Photos', 'coworker' ) );
		$this->WP_Widget( 'instagram_widget', __( 'CoWorker: Instagram Photos', 'coworker' ), $widget_ops);
	}
	
	function form($instance) {
	
		
		$instance = wp_parse_args( (array) $instance, array('title' => 'Instagram Photos', 'userid' => '', 'tag' => '', 'idtype' => 'user', 'number' => 9) );

        $title = esc_attr($instance['title']);
        $userid = $instance['userid'];
        $tag = $instance['tag'];
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
            <label for="<?php echo $this->get_field_id('userid'); ?>">
               <?php _e('Username:', 'coworker'); ?>
            </label>
            <input class="widefat" id="<?php echo $this->get_field_id('userid'); ?>" name="<?php echo $this->get_field_name('userid'); ?>" type="text" value="<?php echo $userid; ?>" />
        </p>		

		<p>
            <label for="<?php echo $this->get_field_id('tag'); ?>">
               <?php _e('Tag:', 'coworker'); ?>
            </label>
            <input class="widefat" id="<?php echo $this->get_field_id('tag'); ?>" name="<?php echo $this->get_field_name('tag'); ?>" type="text" value="<?php echo $tag; ?>" />
        </p>
        
        <p>
            <label for="<?php echo $this->get_field_id('idtype'); ?>">
               <?php _e('Retrieve Type:', 'coworker'); ?>
            </label>
            <select id="<?php echo $this->get_field_id('idtype'); ?>" name="<?php echo $this->get_field_name('idtype'); ?>">
                <option value="user" <?php if ( $idtype == 'user' ) echo 'selected'; ?>>user</option>
                <option value="tag" <?php if ( $idtype == 'tag' ) echo 'selected'; ?>>tag</option>
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
        $instance['userid'] = $new_instance['userid'];
        $instance['tag'] = $new_instance['tag'];
        $instance['idtype'] = $new_instance['idtype'];
        $instance['number'] = $new_instance['number'];
        return $instance;

    }

	function widget($args, $instance) {
	
		extract($args);

		$title = apply_filters('widget_title', $instance['title']);
		if ( empty($title) ) $title = false;

        $userid = $instance['userid'];
        $tag = $instance['tag'];
        $idtype = $instance['idtype'];
		$number = absint( $instance['number'] );
		
		
		echo $before_widget;
	
		if($title){
			echo $before_title;
			echo $title; 
			echo $after_title;
		}
		
        ?>
        
        <div class="instagram" data-user="<?php echo $userid; ?>" data-tag="<?php echo $tag; ?>" data-count="<?php echo $number; ?>" data-type="<?php echo $idtype; ?>"></div>
        
        <?php
        		
		echo $after_widget;
		
	}

}


add_action( 'widgets_init', 'semi_widget_instagram' );
function semi_widget_instagram() {
	register_widget('SEMI_Instagram');
}
    
?>