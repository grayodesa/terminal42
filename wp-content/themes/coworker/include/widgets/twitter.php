<?php

/*-------------------------------------------------

	Plugin Name: Custom Latest Tweets
	Plugin URI: http://themeforest.net/user/SemiColonWeb
	Description: Widget to show Latest Twitter Feed
	Version: 1.0
	Author: SemiColonWeb
	Author URI: http://themeforest.net/user/SemiColonWeb

---------------------------------------------------*/


class SEMI_Twitter extends WP_Widget {

	function SEMI_Twitter() {	
	$widget_ops = array( 'classname' => 'twitter-widget', 'description' => __( 'Get a List of Tweets from a Username', 'coworker' ) );
		$this->WP_Widget( 'twitter_widget', __( 'CoWorker: Twitter Feed', 'coworker' ), $widget_ops);
	}
	
	function form($instance) {
	
		
		$instance = wp_parse_args( (array) $instance, array('title' => 'Twitter', 'number' => 3, 'username' => '', 'followtext' => '') );

        $title = esc_attr($instance['title']);
        $username = $instance['username'];
		$number = absint($instance['number']);
        $followtext = $instance['followtext'];

?>
		<p>
            <label for="<?php echo $this->get_field_id('title'); ?>">
               <?php _e('Title:', 'coworker'); ?>
            </label>
                <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
        </p>

		<p>
            <label for="<?php echo $this->get_field_id('username'); ?>">
               <?php _e('Twitter username:', 'coworker'); ?>
            </label>
                <input class="widefat" id="<?php echo $this->get_field_id('username'); ?>" name="<?php echo $this->get_field_name('username'); ?>" type="text" value="<?php echo $username; ?>" />
                
        </p>

		<p>
            <label for="<?php echo $this->get_field_id('number'); ?>">
               <?php _e('No. of Tweets:', 'coworker'); ?>
            </label>
                <input class="widefat" id="<?php echo $this->get_field_id('number'); ?>" name="<?php echo $this->get_field_name('number'); ?>" type="text" value="<?php echo $number; ?>" />
        </p>
        
        <p>
            <label for="<?php echo $this->get_field_id('followtext'); ?>">
               <?php _e('Follow Text:', 'coworker'); ?>
            </label>
                <input class="widefat" id="<?php echo $this->get_field_id('followtext'); ?>" name="<?php echo $this->get_field_name('followtext'); ?>" type="text" value="<?php echo $followtext; ?>" />
        </p>


<?php
    }

	function update($new_instance, $old_instance) {
        $instance=$old_instance;

        $instance['title'] = strip_tags($new_instance['title']);
        $instance['username'] = $new_instance['username'];
        $instance['number'] = $new_instance['number'];
        $instance['followtext'] = $new_instance['followtext'];
        return $instance;

    }

	function widget($args, $instance) {
	
		extract($args);

		$title = apply_filters('widget_title', $instance['title']);
		if ( empty($title) ) $title = false;

        $username = $instance['username'];
		$number = absint( $instance['number'] );
        $followtext = $instance['followtext'];
        
        $getmrand = mt_rand(1,1000);
		
		if (!empty($username)) {
		
			
			echo $before_widget;
		
			if($title){
				echo $before_title;
				echo $title;
				echo $after_title;
			}
			
            ?>
            
            <script type="text/javascript">
    			
                jQuery(document).ready(function($){
    				$.getJSON('<?php echo get_template_directory_uri(); ?>/include/twitter/tweets.php?username=<?php echo $username; ?>&count=<?php echo $number; ?>', function(tweets){
    					$("#twitter_list_<?php echo $getmrand; ?>").html( sm_format_twitter(tweets) );
    				});
    			});
                
    		</script>
            
            <ul id="twitter_list_<?php echo $getmrand; ?>">
            
                <li></li>
                
            </ul>
    
            <?php if( $followtext ) { ?><a class="twitter-follow-me ntip" href="http://www.twitter.com/<?php echo $username; ?>" title="<?php echo __( 'Follow @', 'coworker' ) . $username; ?>"><?php echo $followtext; ?></a><?php } ?>
    
            <div class="clear"></div>
            
            <?php
            		
			echo $after_widget;

		}
		
	}

}


add_action( 'widgets_init', 'semi_widget_twitter' );
function semi_widget_twitter() {
	register_widget('SEMI_Twitter');
}
    
?>