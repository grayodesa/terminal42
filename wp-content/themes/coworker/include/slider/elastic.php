<?php

$sliderops = array();
$sliderops['height'] = is_numeric( get_post_meta( get_the_ID(), 'semi_page_slider_height', TRUE ) ) ? get_post_meta( get_the_ID(), 'semi_page_slider_height', TRUE ) : 400;
$sliderops['items'] = is_numeric( get_post_meta( get_the_ID(), 'semi_page_slider_items', TRUE ) ) ? get_post_meta( get_the_ID(), 'semi_page_slider_items', TRUE ) : 5;
$sliderops['order'] = get_post_meta( get_the_ID(), 'semi_page_slider_order', TRUE );
$sliderops['orderby'] = get_post_meta( get_the_ID(), 'semi_page_slider_orderby', TRUE );
$sliderops['animation'] = get_post_meta( get_the_ID(), 'semi_page_slider_animation', TRUE );
$sliderops['easing'] = get_post_meta( get_the_ID(), 'semi_page_slider_easing', TRUE );
$sliderops['auto'] = ( get_post_meta( get_the_ID(), 'semi_page_slider_auto', TRUE ) == 1 ) ? 'true' : 'false';
$sliderops['pause'] = get_post_meta( get_the_ID(), 'semi_page_slider_pause', TRUE );
$sliderops['speed'] = get_post_meta( get_the_ID(), 'semi_page_slider_speed', TRUE );

$slidercats = wp_get_object_terms( get_the_ID(), 'slider-group' );

$slidercatlist = array();

$args = array( 'post_type' => 'slider', 'posts_per_page' => $sliderops['items'] );

if( count( $slidercats ) > 0 ) {
    
    foreach ( $slidercats as $slidercat) {
        $slidercatlist[] = $slidercat->slug;
    }
    
    $args['tax_query'] = array( array( 'taxonomy' => 'slider-group', 'field' => 'slug', 'terms' => $slidercatlist ) );

}

$args['order'] = $sliderops['order'];

$args['orderby'] = $sliderops['orderby'];

$showslider = new WP_Query( $args );

if( $showslider->have_posts() ):

$smallthumbs = '';

?>
<div id="slider" class="elastic-slideshow">
        
            <div class="container clearfix">
            
            
                <div id="ei-slider" class="ei-slider">
                    
                    <ul class="ei-slider-large">
                        
                        <?php while ( $showslider->have_posts() ) : $showslider->the_post();
                        
                        $thumb = get_resized_image( 1920, $sliderops['height'], false );
                        
                        $slideops = array();
                        $slideops['caption'] = get_post_meta( get_the_ID(), 'semi_slider_caption', TRUE );
                        $slideops['caption_type'] = get_post_meta( get_the_ID(), 'semi_slider_caption_type', TRUE );
                        $slideops['caption_position'] = get_post_meta( get_the_ID(), 'semi_slider_caption_position', TRUE );
                        $slideops['url'] = get_post_meta( get_the_ID(), 'semi_slider_url', TRUE );
                        $slideops['target'] = get_post_meta( get_the_ID(), 'semi_slider_target', TRUE );
                        
                        if( $thumb ):
                        
                        $smallthumb = get_resized_image( 120, 50 );
                        
                        $smallthumbs .= '<li><a href="#">' . the_title_attribute( 'echo=0' ) . '</a><img src="' . $smallthumb . '" width="120" height="50" alt="' . the_title_attribute( 'echo=0' ) . '" /></li>';
                        
                        ?>
                        
                        <li>
                            
                            <?php if( checkurl( $slideops['url'] ) ): ?>
                            
                            <a href="<?php echo $slideops['url']; ?>" target="<?php echo $slideops['target']; ?>"><img src="<?php echo $thumb[0]; ?>" width="<?php echo $thumb[1]; ?>" height="<?php echo $thumb[2]; ?>" alt="<?php the_title_attribute(); ?>" /></a>
                            
                            <?php else: ?>
                            
                            <img src="<?php echo $thumb[0]; ?>" width="<?php echo $thumb[1]; ?>" height="<?php echo $thumb[2]; ?>" alt="<?php the_title_attribute(); ?>" />
                            
                            <?php endif; ?>
                            
                            <div class="ei-title">
                            
                                <h2><span><?php the_title_attribute(); ?></span></h2>
                                <?php if( $slideops['caption'] ) { ?><h3><span><?php echo strip_tags( $slideops['caption'] ); ?></span></h3><?php } ?>
                            
                            </div>
                            
                        </li>
                        
                        <?php endif; endwhile; ?>
                        
                    </ul>
                    
                    <ul class="ei-slider-thumbs">
                        
                        <li class="ei-slider-element">Current</li>
                        
                        <?php echo $smallthumbs; ?>
                        
                    </ul>
                
                </div>
                
                
                <script type="text/javascript">
                
                    jQuery(document).ready(function ($) {
                        
                        $('#ei-slider').eislideshow({
        					animation : '<?php echo $sliderops['animation']; ?>',
                            autoplay : <?php echo $sliderops['auto']; ?>,
                            slideshow_interval : <?php echo $sliderops['pause']; ?>,
                            speed : <?php echo $sliderops['speed']; ?>,
                            easing : '<?php echo $sliderops['easing']; ?>',
        					titleeasing : '<?php echo $sliderops['easing']; ?>',
        					titlespeed : <?php echo $sliderops['speed']; ?>,
                            thumbMaxWidth : 120
                        });
                    
                    });
                
                </script>
            
            
            </div>
        
        
        </div>
        
<?php endif; wp_reset_postdata(); ?>