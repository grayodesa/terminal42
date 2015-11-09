        <?php if( ( semi_option( 'twitter_panel' ) == 1 ) AND ( semi_option( 'twitter_panel_username' ) != '' ) ): ?>

        <div id="twitter-panel">

            <div class="container clearfix">
            
                <div id="twitter-panel-icon">
                    <i class="icon-twitter"></i>
                </div>

                <div id="twitter-panel-content">

                    <div class="fslider" data-animate="fade" data-slideshow="true" data-pause="<?php echo semi_option( 'twitter_panel_speed' ) ? semi_option( 'twitter_panel_speed' ) : 5000; ?>" data-arrows="false">
                    
                        <div class="flexslider">
                        
                            <div class="slider-wrap">
                            
                                <?php echo semi_twitter_output( semi_option( 'twitter_panel_username' ), semi_option( 'twitter_panel_limit' ), '<div class="slide">', '</div>' ); ?>

                            </div>

                        </div>

                    </div>

                </div>

                <div id="twitter-panel-follow">
                    <a href="http://twitter.com/<?php echo semi_option( 'twitter_panel_username' ); ?>"><?php echo semi_option( 'twitter_panel_follow_text' ); ?></a>
                </div>

            </div>

        </div>

        <?php endif; ?>

        <?php if( !is_page_template( 'template-comingsoon.php' ) AND ( semi_option( 'footer' ) == 1 ) ): ?>
        
        <div id="footer"<?php if( semi_option( 'footer_color' ) == 'dark' ) { echo ' class="footer-dark"'; } ?>>
        
        
            <div class="container clearfix">
            
            
                <div class="footer-widgets-wrap clearfix">
                
                
                    <div class="col_one_fourth">
                    
                    
                        <?php if ( !function_exists( 'generated_dynamic_sidebar' ) || !generated_dynamic_sidebar( 'Footer 1' ) ) ?>
                    
                    
                    </div>
                    
                    
                    <div class="col_one_fourth">
                    
                    
                        <?php if ( !function_exists( 'generated_dynamic_sidebar' ) || !generated_dynamic_sidebar( 'Footer 2' ) ) ?>
                    
                    
                    </div>
                    
                    
                    <div class="col_one_fourth">
                    
                    
                        <?php if ( !function_exists( 'generated_dynamic_sidebar' ) || !generated_dynamic_sidebar( 'Footer 3' ) ) ?>
                    
                    
                    </div>
                    
                    
                    <div class="col_one_fourth">
                    
                    
                        <?php if ( !function_exists( 'generated_dynamic_sidebar' ) || !generated_dynamic_sidebar( 'Footer 4' ) ) ?>
                    
                    
                    </div>
                
                
                </div>
            
            
            </div>
        
        
        </div>
        
        <?php endif; ?>
        
        <div class="clear"></div>
        
        
        <?php if( semi_option( 'copyrights' ) == 1 ): ?>
        
        <div id="copyrights"<?php if( semi_option( 'copyrights_color' ) == 'dark' ) { echo ' class="copyrights-dark"'; } ?>>
        
            <div class="container clearfix">
            
            
                <div class="col_half">
                
                    <?php echo semi_option( 'copyrights_left' ); ?>
                
                </div>
                
                <div class="col_half col_last tright">
                
                    <?php echo semi_option( 'copyrights_right' ); ?>
                
                </div>
            
            
            </div>
        
        </div>
        
        <?php endif; ?>
    
    
    </div>
    
    
    <!-- Go To Top
    ============================================= -->
    <div id="gotoTop" class="icon-angle-up"></div>
    
    <!-- WP Footer
    ============================================= -->
    <?php wp_footer(); ?>
    
    
</body>
</html>