<?php

$sliderops = array();
$sliderops['height'] = is_numeric( get_post_meta( get_the_ID(), 'semi_page_slider_height', TRUE ) ) ? get_post_meta( get_the_ID(), 'semi_page_slider_height', TRUE ) : 400;
$sliderops['items'] = is_numeric( get_post_meta( get_the_ID(), 'semi_page_slider_items', TRUE ) ) ? get_post_meta( get_the_ID(), 'semi_page_slider_items', TRUE ) : 5;
$sliderops['order'] = get_post_meta( get_the_ID(), 'semi_page_slider_order', TRUE );
$sliderops['orderby'] = get_post_meta( get_the_ID(), 'semi_page_slider_orderby', TRUE );
$sliderops['animation'] = get_post_meta( get_the_ID(), 'semi_page_slider_animation', TRUE );
$sliderops['pause'] = get_post_meta( get_the_ID(), 'semi_page_slider_pause', TRUE );
$sliderops['speed'] = get_post_meta( get_the_ID(), 'semi_page_slider_speed', TRUE );
$sliderops['hover'] = ( get_post_meta( get_the_ID(), 'semi_page_slider_hover', TRUE ) == 1 ) ? 'true' : 'false';
$sliderops['arrows'] = ( get_post_meta( get_the_ID(), 'semi_page_slider_arrows', TRUE ) == 1 ) ? 'true' : 'false';

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
<div id="slider" class="slider-nivo">
        
            <div class="container clearfix">
            
            
                <div class="nivoSlider">
                    
                    <?php while ( $showslider->have_posts() ) : $showslider->the_post();
                    
                    $thumb = get_resized_image( 1020, $sliderops['height'], false );
                    
                    $slideops = array();
                    $slideops['caption'] = get_post_meta( get_the_ID(), 'semi_slider_caption', TRUE );
                    $slideops['url'] = get_post_meta( get_the_ID(), 'semi_slider_url', TRUE );
                    $slideops['target'] = get_post_meta( get_the_ID(), 'semi_slider_target', TRUE );
                    $slideops['video'] = get_post_meta( get_the_ID(), 'semi_slider_video', TRUE );
                    
                    if( $thumb ):
                    
                    ?>
                    
                    <?php if( checkurl( $slideops['url'] ) ): ?>
                    
                    <a href="<?php echo $slideops['url']; ?>" target="<?php echo $slideops['target']; ?>"><img src="<?php echo $thumb[0]; ?>" alt="<?php the_title_attribute(); ?>"<?php if( $slideops['caption'] ) { echo ' title="' . strip_tags( $slideops['caption'], '<a>' ) . '"'; } ?> /></a>
                    
                    <?php else: ?>
                    
                    <img src="<?php echo $thumb[0]; ?>" alt="<?php the_title_attribute(); ?>"<?php if( $slideops['caption'] ) { echo ' title="' . strip_tags( $slideops['caption'], '<a>' ) . '"'; } ?> />
                    
                    <?php endif; endif; endwhile; ?>
                    
                </div>
                
                
                <script type="text/javascript">
                
                    jQuery(document).ready(function ($) {
                        
                        $('.nivoSlider').nivoSlider({
                            
                            effect: '<?php echo $sliderops['animation']; ?>',
                            slices: 15,
                            boxCols: 12,
                            boxRows: 6,
                            animSpeed: <?php echo $sliderops['speed']; ?>,
                            pauseTime: <?php echo $sliderops['pause']; ?>,
                            directionNav: <?php echo $sliderops['arrows']; ?>,
                            controlNav: false,
                            pauseOnHover: <?php echo $sliderops['hover']; ?>
                        
                        });
                    
                    });
                
                </script>
            
            
            </div>
        
        
        </div>
        
<?php endif; wp_reset_postdata(); ?>