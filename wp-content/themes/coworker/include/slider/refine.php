<?php

$sliderops = array();
$sliderops['height'] = is_numeric( get_post_meta( get_the_ID(), 'semi_page_slider_height', TRUE ) ) ? get_post_meta( get_the_ID(), 'semi_page_slider_height', TRUE ) : 400;
$sliderops['items'] = is_numeric( get_post_meta( get_the_ID(), 'semi_page_slider_items', TRUE ) ) ? get_post_meta( get_the_ID(), 'semi_page_slider_items', TRUE ) : 5;
$sliderops['order'] = get_post_meta( get_the_ID(), 'semi_page_slider_order', TRUE );
$sliderops['orderby'] = get_post_meta( get_the_ID(), 'semi_page_slider_orderby', TRUE );
$sliderops['animation'] = get_post_meta( get_the_ID(), 'semi_page_slider_animation', TRUE );
$sliderops['auto'] = ( get_post_meta( get_the_ID(), 'semi_page_slider_auto', TRUE ) == 1 ) ? 'true' : 'false';
$sliderops['pause'] = get_post_meta( get_the_ID(), 'semi_page_slider_pause', TRUE );
$sliderops['speed'] = get_post_meta( get_the_ID(), 'semi_page_slider_speed', TRUE );

if( get_post_meta( get_the_ID(), 'semi_page_slider_thumbs', TRUE ) == 1 ) {
    $sliderops['control'] = '"thumbs"';
} elseif( get_post_meta( get_the_ID(), 'semi_page_slider_arrows', TRUE ) == 1 ) {
    $sliderops['control'] = '"arrows"';
} else {
    $sliderops['control'] = 'null';
}

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

?>
<div id="slider" <?php if( $sliderops['control'] == '"thumbs"' ) { echo 'class="thumb-slider"'; } ?>>
        
            <div class="container clearfix">
            
            
                <ul class="rs-slider">
                    
                <?php while ( $showslider->have_posts() ) : $showslider->the_post();
                
                $thumb = get_resized_image( 1020, $sliderops['height'], false );
                
                $slideops = array();
                $slideops['caption'] = get_post_meta( get_the_ID(), 'semi_slider_caption', TRUE );
                $slideops['caption_type'] = get_post_meta( get_the_ID(), 'semi_slider_caption_type', TRUE );
                $slideops['caption_position'] = get_post_meta( get_the_ID(), 'semi_slider_caption_position', TRUE );
                $slideops['url'] = get_post_meta( get_the_ID(), 'semi_slider_url', TRUE );
                $slideops['target'] = get_post_meta( get_the_ID(), 'semi_slider_target', TRUE );
                $slideops['video'] = get_post_meta( get_the_ID(), 'semi_slider_video', TRUE );
                
                if( $thumb ):
                
                ?>
                    
                    <li>
                        
                        <?php if( checkurl( $slideops['url'] ) ): ?>
                        
                        <a href="<?php echo $slideops['url']; ?>" target="<?php echo $slideops['target']; ?>"><img src="<?php echo $thumb[0]; ?>" width="<?php echo $thumb[1]; ?>" height="<?php echo $thumb[2]; ?>" alt="<?php the_title_attribute(); ?>" /></a>
                        
                        <?php else: ?>
                        
                        <img src="<?php echo $thumb[0]; ?>" width="<?php echo $thumb[1]; ?>" height="<?php echo $thumb[2]; ?>" alt="<?php the_title_attribute(); ?>" />
                        
                        <?php endif; ?>
                        
                        <?php if( $slideops['caption'] ): ?>
                        
                        <div class="rs-caption<?php if( $slideops['caption_type'] == 'chunky' ) { echo ' slide-caption2'; } ?><?php if( $slideops['caption_position'] == 'left' ) { echo ' slide-caption-left'; } ?>">
                        
                            <?php echo $slideops['caption']; ?>
                        
                        </div>
                        
                        <?php endif; ?>
                        
                    </li>
                    
                    <?php endif; endwhile; ?>
                
                </ul>
                
                
                <script type="text/javascript">
                
                    jQuery(document).ready(function ($) {
                        
                        $('.rs-slider').refineSlide({
                            
                            transition : '<?php echo $sliderops['animation']; ?>',
                            transitionDuration : <?php echo $sliderops['speed']; ?>,
                            autoPlay : <?php echo $sliderops['auto']; ?>,
                            keyNav : false,
                            delay : <?php echo $sliderops['pause']; ?>,
                            controls : <?php echo $sliderops['control']; ?>
                        
                        });
                    
                    });
                
                </script>
            
            
            </div>
        
        
        </div>
        
<?php endif; wp_reset_postdata(); ?>