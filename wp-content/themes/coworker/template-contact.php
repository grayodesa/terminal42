<?php
/*
Template Name: Contact - Full Layout
*/
?>
<?php get_header(); ?>
        
        
        <?php get_template_part( 'include/content', 'head' ); if (have_posts()) : while (have_posts()) : the_post(); $recaptchatrue = get_post_meta(get_the_ID(), 'semi_page_contact_recaptcha', true); ?>
                
                    <div class="col_two_third nobottommargin">
                    
                    
                        <!-- ============================================
                            AJAX Contact Form
                        ============================================= -->
                        <div id="contact-form-container">
                        
                            <h2><?php echo get_post_meta(get_the_ID(), 'semi_page_contact_formtitle', true) ? get_post_meta(get_the_ID(), 'semi_page_contact_formtitle', true) : __( 'Send us an <span>Email</span>', 'coworker' ); ?></h2>
                            
                            <div id="contact-form-result"></div>
                        
                            <form class="nobottommargin" id="template-contactform" name="template-contactform" action="<?php echo get_template_directory_uri(); ?>/include/sendemail.php<?php if(  $recaptchatrue == 1 ) { echo '?recaptcha=yes'; } ?>" method="post">
                            
                                <div class="col_one_third nobottommargin">
                                    <label for="template-contactform-name"><?php _e( 'Name', 'coworker' ); ?> <small>*</small></label>
                                    <input type="text" id="template-contactform-name" name="template-contactform-name" value="" class="required input-block-level" />
                                </div>
                                
                                <div class="col_one_third nobottommargin">
                                    <label for="template-contactform-email"><?php _e( 'Email', 'coworker' ); ?> <small>*</small></label>
                                    <input type="text" id="template-contactform-email" name="template-contactform-email" value="" class="required email input-block-level" />
                                </div>
                                
                                <div class="col_one_third nobottommargin col_last">
                                    <label for="template-contactform-service"><?php _e( 'Services', 'coworker' ); ?></label>
                                    <select id="template-contactform-service" name="template-contactform-service" class="input-block-level">
                                        <option value="">-- <?php _e( 'Select One', 'coworker' ); ?> --</option>
                                        <?php
                                        $getservices = get_post_meta(get_the_ID(), 'semi_page_contact_services', true);
                                        if( is_array( $getservices ) AND implode( '', $getservices ) != '' ):
                                            foreach( $getservices as $servicevalue ) {
                                                echo '<option value="' . $servicevalue . '">' . $servicevalue . '</option>';
                                            }
                                        else: ?>
                                        <option value="Wordpress">Wordpress</option>
                                        <option value="PHP / MySQL">PHP / MySQL</option>
                                        <option value="HTML5 / CSS3">HTML5 / CSS3</option>
                                        <option value="Graphic Design">Graphic Design</option>
                                        <?php endif; ?>
                                    </select>
                                </div>
                                
                                <div class="clear"></div>
                                
                                <div class="col_full nobottommargin">
                                    <label for="template-contactform-subject"><?php _e( 'Subject', 'coworker' ); ?> <small>*</small></label>
                                    <input type="text" id="template-contactform-subject" name="template-contactform-subject" value="" class="required input-block-level" />
                                </div>
                                
                                <div class="col_full nobottommargin">
                                    <label for="template-contactform-message"><?php _e( 'Message', 'coworker' ); ?> <small>*</small></label>
                                    <textarea class="required input-block-level" id="template-contactform-message" name="template-contactform-message" rows="10" cols="30"></textarea>
                                </div>
                                
                                <div class="col_full nobottommargin hidden">
                                    <label for="template-contactform-botcheck"><?php _e( 'Botcheck', 'coworker' ); ?></label>
                                    <input type="text" id="template-contactform-botcheck" name="template-contactform-botcheck" value="" class="input-block-level" />
                                </div>
                                
                                <?php if( $recaptchatrue == 1 ): ?>
                                
                                <div class="col_full nobottommargin">
                                
                                    <div id="recaptcha_widget" style="display:none" class="recaptcha_widget clearfix">
                                		
                                        <div class="col_half nobottommargin">
                                        
                                            <div id="recaptcha_image"></div>
                                    		<div class="recaptcha_only_if_incorrect_sol" style="color:red"><?php _e( 'Incorrect. Please try again.', 'coworker' ); ?></div>
                                        
                                        </div>
                                        
                                        <div class="col_half col_last nobottommargin">
                                        
                                    		<div class="recaptcha_input nomargin">
                                    			<label class="recaptcha_only_if_image nomargin" for="recaptcha_response_field"><?php _e( 'Enter the words in the Image:', 'coworker' ); ?></label>
                                    			<label class="recaptcha_only_if_audio nomargin" for="recaptcha_response_field"><?php _e( 'Enter the numbers you hear:', 'coworker' ); ?></label>
                                                
                                    			<input type="text" class="required input-block-level nomargin" id="recaptcha_response_field" name="recaptcha_response_field" />
                                    		</div>
                                            
                                    		<ul class="recaptcha_options">
                                    			<li>
                                    				<a href="javascript:Recaptcha.reload()">
                                    					<i class="icon-refresh"></i>
                                    					<span class="captcha_hide"><?php _e( 'Get another CAPTCHA', 'coworker' ); ?></span>
                                    				</a>
                                    			</li>
                                    			<li class="recaptcha_only_if_image">
                                    				<a href="javascript:Recaptcha.switch_type('audio')">
                                    					<i class="icon-volume-up"></i><span class="captcha_hide"> <?php _e( 'Get an audio CAPTCHA', 'coworker' ); ?></span>
                                    				</a>
                                    			</li>
                                    			<li class="recaptcha_only_if_audio">
                                    				<a href="javascript:Recaptcha.switch_type('image')">
                                    					<i class="icon-picture"></i><span class="captcha_hide"> <?php _e( 'Get an image CAPTCHA', 'coworker' ); ?></span>
                                    				</a>
                                    			</li>
                                    			<li>
                                    				<a href="javascript:Recaptcha.showhelp()">
                                    					<i class="icon-question-sign"></i><span class="captcha_hide"> <?php _e( 'Help', 'coworker' ); ?></span>
                                    				</a>
                                    			</li>
                                    		</ul>
                                        
                                        </div>
                                        
                                        <div class="clear"></div>
                                        
                                	</div>
                                
                                </div>
                                
                                <?php endif; ?>
                                
                                <div class="col_full nobottommargin">
                                    <button class="btn" type="submit" id="template-contactform-submit" name="template-contactform-submit" value="submit"><?php echo get_post_meta(get_the_ID(), 'semi_page_contact_formbutton', true) ? get_post_meta(get_the_ID(), 'semi_page_contact_formbutton', true) : __( 'Send Message', 'coworker' ); ?></button>
                                </div>
                                
                            </form>
                        
                        </div>
                        
                        <script type="text/javascript">
                        
                            <?php if( $recaptchatrue == 1 ) { ?>
                            
                            var RecaptchaOptions = {
                        		theme : 'custom',
                        		custom_theme_widget: 'recaptcha_widget'
                        	};
                            
                            <?php } ?>
                            
                            jQuery("#template-contactform").validate({
                        		submitHandler: function(form) {
                        		    
                                    jQuery(form).find('.btn').prepend('<i class="icon-spinner icon-spin"></i>').addClass('disabled').attr('disabled', 'disabled');
                                    
                        			jQuery(form).ajaxSubmit({
                        				target: '#contact-form-result',
                                        success: function() {
                                            jQuery("#template-contactform").fadeOut(500, function(){
                                                jQuery('#contact-form-result').fadeIn(500);
                                            });
                                        },
                                        error: function() {
                                            jQuery('#contact-form-result').fadeIn(500);
                                            jQuery("#template-contactform").find('.btn').remove('<i class="icon-spinner icon-spin"></i>').removeClass('disabled').removeAttr('disabled');
                                        }
                        			});
                                    
                        		}
                        	});
                        
                        </script>
                        
                        <?php if( $recaptchatrue == 1 ) { ?>
                        
                        <script type="text/javascript" src="http://www.google.com/recaptcha/api/challenge?k=<?php echo semi_option( 'contact_rc_pubkey' ); ?>"></script>
                        
                        <?php } ?>
                        
                    </div>
                    
                    
                    <div class="col_one_third entry_content col_last nobottommargin">
                    
                        <?php the_content(); ?>
                    
                    </div>
                
        <?php endwhile; endif; get_template_part( 'include/content', 'foot' ); ?>
        
        
<?php get_footer(); ?>