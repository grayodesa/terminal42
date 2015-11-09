<?php

$sliderops = array();
$sliderops['height'] = get_post_meta( get_the_ID(), 'semi_page_slider_sliderheight', TRUE ) ? get_post_meta( get_the_ID(), 'semi_page_slider_sliderheight', TRUE ) : '400px';
$sliderops['imgheight'] = is_numeric( get_post_meta( get_the_ID(), 'semi_page_slider_height', TRUE ) ) ? get_post_meta( get_the_ID(), 'semi_page_slider_height', TRUE ) : 400;
$sliderops['items'] = is_numeric( get_post_meta( get_the_ID(), 'semi_page_slider_items', TRUE ) ) ? get_post_meta( get_the_ID(), 'semi_page_slider_items', TRUE ) : 5;
$sliderops['order'] = get_post_meta( get_the_ID(), 'semi_page_slider_order', TRUE );
$sliderops['orderby'] = get_post_meta( get_the_ID(), 'semi_page_slider_orderby', TRUE );
$sliderops['animation'] = get_post_meta( get_the_ID(), 'semi_page_slider_animation', TRUE );
$sliderops['loader'] = get_post_meta( get_the_ID(), 'semi_page_slider_loader', TRUE );
$sliderops['easing'] = get_post_meta( get_the_ID(), 'semi_page_slider_easing', TRUE );
$sliderops['auto'] = ( get_post_meta( get_the_ID(), 'semi_page_slider_auto', TRUE ) == 1 ) ? 'true' : 'false';
$sliderops['pause'] = get_post_meta( get_the_ID(), 'semi_page_slider_pause', TRUE );
$sliderops['speed'] = get_post_meta( get_the_ID(), 'semi_page_slider_speed', TRUE );
$sliderops['hover'] = ( get_post_meta( get_the_ID(), 'semi_page_slider_hover', TRUE ) == 1 ) ? 'true' : 'false';
$sliderops['pagination'] = ( get_post_meta( get_the_ID(), 'semi_page_slider_pagination', TRUE ) == 1 ) ? 'true' : 'false';
$sliderops['arrows'] = ( get_post_meta( get_the_ID(), 'semi_page_slider_arrows', TRUE ) == 1 ) ? 'true' : 'false';
$sliderops['thumbs'] = ( get_post_meta( get_the_ID(), 'semi_page_slider_thumbs', TRUE ) == 1 ) ? 'true' : 'false';

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
<div id="slider" class="camera-slideshow">
        
            <div class="container clearfix">
            
            
                <div class="camera_wrap" id="camera_wrap_1">
                
                    <?php while ( $showslider->have_posts() ) : $showslider->the_post();
                    
                    $thumb = get_resized_image( 1920, $sliderops['imgheight'], false );
                    
                    $slideops = array();
                    $slideops['caption'] = get_post_meta( get_the_ID(), 'semi_slider_caption', TRUE );
                    $slideops['caption_type'] = get_post_meta( get_the_ID(), 'semi_slider_caption_type', TRUE );
                    $slideops['url'] = get_post_meta( get_the_ID(), 'semi_slider_url', TRUE );
                    $slideops['target'] = get_post_meta( get_the_ID(), 'semi_slider_target', TRUE );
                    
                    if( $thumb ):
                    
                    ?>
                    
                    <div data-thumb="<?php echo get_resized_image( 120, 55 ); ?>" data-src="<?php echo $thumb[0]; ?>"<?php if( checkurl( $slideops['url'] ) ): ?> data-link="<?php echo $slideops['url']; ?>" data-target="<?php echo $slideops['target']; ?>"<?php endif; ?>>
                        <?php if( $slideops['caption'] ): ?>
                        
                        <div class="camera_caption fadeFromBottom<?php if( $slideops['caption_type'] == 'chunky' ) { echo ' slide-caption2'; } ?>">
                        
                            <?php echo $slideops['caption']; ?>
                        
                        </div>
                        
                        <?php endif; ?>
                    </div>
                    
                    <?php endif; endwhile; ?>
                
                
                </div>
                
                
                <script type="text/javascript">
                
                    jQuery(document).ready(function($) {
                    
                        $('#camera_wrap_1').camera({
            				height: '<?php echo $sliderops['height']; ?>',
                            loader: '<?php echo $sliderops['loader']; ?>',
                            autoAdvance: <?php echo $sliderops['auto']; ?>,
                            easing: '<?php echo $sliderops['easing']; ?>',
                            fx: '<?php echo $sliderops['animation']; ?>',
                            hover: <?php echo $sliderops['hover']; ?>,
                            navigation: <?php echo $sliderops['arrows']; ?>,
                            pagination: <?php echo $sliderops['pagination']; ?>,
                            thumbnails: <?php echo $sliderops['thumbs']; ?>,
                            time: <?php echo $sliderops['pause']; ?>,
                            transPeriod: <?php echo $sliderops['speed']; ?>
            			});
                    
                    });
                
                </script>
            
            
            </div>
        
        
        </div>
        
<?php endif; wp_reset_postdata(); ?>