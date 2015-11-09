<?php

/*-------------------------------------------------

	Plugin Name: Quick Contact Form
	Plugin URI: http://themeforest.net/user/SemiColonWeb
	Description: Widget for a Quick Contact Form
	Version: 1.0
	Author: SemiColonWeb
	Author URI: http://themeforest.net/user/SemiColonWeb

---------------------------------------------------*/

class SEMI_Contact_Form extends WP_Widget {

	function SEMI_Contact_Form() {
	$widget_ops = array( 'classname' => 'contact-form-widget', 'description' => __( 'Quick Contact Form in the Footer for your Customers.', 'coworker' ) );
		$this->WP_Widget( 'contactform_widget', __( 'CoWorker: Footer Quick Contact', 'coworker' ), $widget_ops);
	}
	
	function form($instance) {
	
		
		$instance = wp_parse_args( (array) $instance, array('title' => 'Quick Contact', 'submit' => 'Send Message') );

        $title = esc_attr($instance['title']);
        $submit = $instance['submit'];

?>
		<p>
            <label for="<?php echo $this->get_field_id('title'); ?>">
               <?php _e('Title:', 'chthemes'); ?>
            </label>
            <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
        </p>
        
        <p>
            <label for="<?php echo $this->get_field_id('submit'); ?>">
               <?php _e('Submit Button Text:', 'chthemes'); ?>
            </label>
            <input class="widefat" id="<?php echo $this->get_field_id('submit'); ?>" name="<?php echo $this->get_field_name('submit'); ?>" type="text" value="<?php echo $submit; ?>" />
        </p>


<?php
    }

	function update($new_instance, $old_instance) {
        $instance=$old_instance;

        $instance['title'] = strip_tags($new_instance['title']);
        $instance['submit'] = $new_instance['submit'];
        return $instance;

    }

	function widget($args, $instance) {
	
		extract($args);

		$title = apply_filters('widget_title', $instance['title']);
		if ( empty($title) ) $title = false;

		$submit = $instance['submit'] ? $instance['submit'] : 'Send Message';

		echo $before_widget;

		if($title){
			echo $before_title;
			echo $title; 
			echo $after_title;
		}

        ?>
                
        <div id="quick-contact-form-result"></div>
        
        <form id="quick-contact-form" name="quick-contact-form" action="<?php echo get_template_directory_uri(); ?>/include/footeremail.php" method="post" class="quick-contact-form nobottommargin">
        
        
            <input type="text" class="required input-block-level" id="quick-contact-form-name" name="quick-contact-form-name" value="" placeholder="<?php _e( 'Full Name', 'coworker' ); ?>" />
            
            <input type="text" class="required email input-block-level" id="quick-contact-form-email" name="quick-contact-form-email" value="" placeholder="<?php _e( 'Email Address', 'coworker' ); ?>" />
            
            <textarea class="required input-block-level short-textarea" id="quick-contact-form-message" name="quick-contact-form-message" rows="30" cols="10" placeholder="<?php _e( 'Message', 'coworker' ); ?>"></textarea>
            
            <?php get_form_bot_protect(); ?>

            <div class="clear"></div>
            
            <button type="submit" id="quick-contact-form-submit" name="quick-contact-form-submit" class="btn btn-small btn-inverse nomargin" value="submit"><?php echo $submit; ?></button>
        
        
        </form>
        
        <script type="text/javascript">                            
        
            jQuery("#quick-contact-form").validate({
                messages: {
                    'quick-contact-form-name': '',
                    'quick-contact-form-email': '',
                    'quick-contact-form-message': '',
                    'sm_ch_fw_output': ''
                },
        		submitHandler: function(form) {
        			
                    jQuery(form).find('.btn').prepend('<i class="icon-spinner icon-spin"></i>').addClass('disabled').attr('disabled', 'disabled');
                    
        			jQuery(form).ajaxSubmit({
        				target: '#quick-contact-form-result',
                        success: function() {
                            jQuery("#quick-contact-form").fadeOut(500, function(){
                                jQuery('#quick-contact-form-result').fadeIn(500);
                            });
                        },
                        error: function() {
                            jQuery('#quick-contact-form-result').fadeIn(500);
                            jQuery("#quick-contact-form").find('.btn').remove('<i class="icon-spinner icon-spin"></i>').removeClass('disabled').removeAttr('disabled');
                        }
        			});
                    
        		}
        	});

        </script>
        
        <?php

		echo $after_widget;
		
	}

}


add_action( 'widgets_init', 'semi_widget_contactform' );
function semi_widget_contactform() {
	register_widget('SEMI_Contact_Form');
}
    
?>