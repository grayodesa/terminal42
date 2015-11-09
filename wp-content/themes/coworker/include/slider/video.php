<?php if( get_post_meta( get_the_ID(), 'semi_page_slider_static_video', TRUE ) ): ?>
<div id="slider" class="thumb-slider preloader2">
        
            <div class="container clearfix">
            
            
                <?php echo stripslashes( htmlspecialchars_decode( get_post_meta( get_the_ID(), 'semi_page_slider_static_video', TRUE ) ) ); ?>
            
            
            </div>
        
        
        </div>
        
<?php endif; ?>