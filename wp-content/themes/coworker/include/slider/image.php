<?php

$sliderops = array();
$sliderops['height'] = is_numeric( get_post_meta( get_the_ID(), 'semi_page_slider_height', TRUE ) ) ? get_post_meta( get_the_ID(), 'semi_page_slider_height', TRUE ) : 400;
$sliderops['url'] = get_post_meta( get_the_ID(), 'semi_page_slider_static_img_url', TRUE );
$sliderops['target'] = get_post_meta( get_the_ID(), 'semi_page_slider_static_img_target', TRUE );

$thumb = get_resized_image( 1020, $sliderops['height'], false );

if( $thumb ):

?>
<div id="slider" class="thumb-slider">
        
            <div class="container clearfix">
            
            
                <?php if( checkurl( $sliderops['url'] ) ): ?>
                
                <a href="<?php echo $sliderops['url']; ?>" target="<?php echo $sliderops['target']; ?>"><img src="<?php echo $thumb[0]; ?>" width="<?php echo $thumb[1]; ?>" height="<?php echo $thumb[2]; ?>" alt="<?php the_title_attribute(); ?>" /></a>
                
                <?php else: ?>
                
                <img src="<?php echo $thumb[0]; ?>" width="<?php echo $thumb[1]; ?>" height="<?php echo $thumb[2]; ?>" alt="<?php the_title_attribute(); ?>" />
                
                <?php endif; ?>
            
            
            </div>
        
        
        </div>
        
<?php endif; ?>