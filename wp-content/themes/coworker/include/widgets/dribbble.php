<?php

/*-------------------------------------------------

	Plugin Name: Dribbble Shots
	Plugin URI: http://themeforest.net/user/SemiColonWeb
	Description: Widget to show Dribbble Shots
	Version: 1.0
	Author: SemiColonWeb
	Author URI: http://themeforest.net/user/SemiColonWeb

---------------------------------------------------*/

class SEMI_Dribbble extends WP_Widget {

	function SEMI_Dribbble() {	
	$widget_ops = array( 'classname' => 'flickr-widget', 'description' => __( 'Show Off your Dribbble Shots', 'coworker' ) );
		$this->WP_Widget( 'dribbble_widget', __( 'CoWorker: Dribbble Shots', 'coworker' ), $widget_ops);
	}
	
	function form($instance) {
	
		
		$instance = wp_parse_args( (array) $instance, array('title' => 'Dribbble Shots', 'userid' => 'envato', 'list' => 'popular', 'idtype' => 'user', 'number' => 9) );

        $title = esc_attr($instance['title']);
        $userid = $instance['userid'];
        $list = $instance['list'];
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
            <label for="<?php echo $this->get_field_id('list'); ?>">
               <?php _e('List Type:', 'coworker'); ?>
            </label>
            <select id="<?php echo $this->get_field_id('list'); ?>" name="<?php echo $this->get_field_name('list'); ?>">
                <option value="popular" <?php if ( $list == 'popular' ) echo 'selected'; ?>>popular</option>
                <option value="everyone" <?php if ( $list == 'everyone' ) echo 'selected'; ?>>everyone</option>
                <option value="debuts" <?php if ( $list == 'debuts' ) echo 'selected'; ?>>debuts</option>
            </select>
        </p>
        
        <p>
            <label for="<?php echo $this->get_field_id('idtype'); ?>">
               <?php _e('Retrieve Type:', 'coworker'); ?>
            </label>
            <select id="<?php echo $this->get_field_id('idtype'); ?>" name="<?php echo $this->get_field_name('idtype'); ?>">
                <option value="user" <?php if ( $idtype == 'user' ) echo 'selected'; ?>>user</option>
                <option value="follows" <?php if ( $idtype == 'follows' ) echo 'selected'; ?>>follows</option>
                <option value="list" <?php if ( $idtype == 'list' ) echo 'selected'; ?>>list</option>
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
        $instance['list'] = $new_instance['list'];
        $instance['idtype'] = $new_instance['idtype'];
        $instance['number'] = $new_instance['number'];
        return $instance;

    }

	function widget($args, $instance) {
	
		extract($args);

		$title = apply_filters('widget_title', $instance['title']);
		if ( empty($title) ) $title = false;

        $userid = $instance['userid'];
        $list = $instance['list'];
        $idtype = $instance['idtype'];
		$number = absint( $instance['number'] );
		
		
		echo $before_widget;
	
		if($title){
			echo $before_title;
			echo $title; 
			echo $after_title;
		}
		
        ?>
        
        <div class="dribbble" data-user="<?php echo $userid; ?>" data-list="<?php echo $list; ?>" data-count="<?php echo $number; ?>" data-type="<?php echo $idtype; ?>"></div>
        
        <?php
        		
		echo $after_widget;
		
	}

}


add_action( 'widgets_init', 'semi_widget_dribbble' );
function semi_widget_dribbble() {
	register_widget('SEMI_Dribbble');
}
    
?>