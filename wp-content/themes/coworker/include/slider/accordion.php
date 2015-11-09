<?php

$sliderops = array();
$sliderops['height'] = is_numeric( get_post_meta( get_the_ID(), 'semi_page_slider_height', TRUE ) ) ? get_post_meta( get_the_ID(), 'semi_page_slider_height', TRUE ) : 400;
$sliderops['panels'] = is_numeric( get_post_meta( get_the_ID(), 'semi_page_slider_panels', TRUE ) ) ? get_post_meta( get_the_ID(), 'semi_page_slider_panels', TRUE ) : 5;
$sliderops['order'] = get_post_meta( get_the_ID(), 'semi_page_slider_order', TRUE );
$sliderops['orderby'] = get_post_meta( get_the_ID(), 'semi_page_slider_orderby', TRUE );
$sliderops['easing'] = get_post_meta( get_the_ID(), 'semi_page_slider_easing', TRUE );
$sliderops['auto'] = ( get_post_meta( get_the_ID(), 'semi_page_slider_auto', TRUE ) == 1 ) ? 'true' : 'false';
$sliderops['pause'] = get_post_meta( get_the_ID(), 'semi_page_slider_pause', TRUE );
$sliderops['speed'] = get_post_meta( get_the_ID(), 'semi_page_slider_speed', TRUE );

$fallbackthumb = rwmb_meta( 'semi_page_slider_fallback', 'type=image&size=full' );

if( $fallbackthumb ) {
    
    $fallbackcount = 0;
    
    foreach ( $fallbackthumb as $fallback ):
    
        $fallbackcount++;
        
        $fallbackurl = semi_resize( $fallback['full_url'], 1020, $sliderops['height'], true, false );
        
        if( $fallbackcount == 1 ) { break; }
    
    endforeach;
    
}

$slidercats = wp_get_object_terms( get_the_ID(), 'slider-group' );

$slidercatlist = array();

$args = array( 'post_type' => 'slider', 'posts_per_page' => $sliderops['panels'] );

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
<div id="slider" <?php if( $sliderops['panels'] != 5 ) { echo 'class="kwicks-panel-' . $sliderops['panels'] . '"'; } ?>>
        
            <div class="container clearfix">
            
            
                <ul id="kwicks-slider">
                    
                    <?php while ( $showslider->have_posts() ) : $showslider->the_post();
                    
                    $thumb = get_resized_image( 1020, $sliderops['height'], false );
                    
                    $slideops = array();
                    $slideops['caption'] = get_post_meta( get_the_ID(), 'semi_slider_caption', TRUE );
                    $slideops['caption_type'] = get_post_meta( get_the_ID(), 'semi_slider_caption_type', TRUE );
                    $slideops['caption_position'] = get_post_meta( get_the_ID(), 'semi_slider_caption_position', TRUE );
                    $slideops['url'] = get_post_meta( get_the_ID(), 'semi_slider_url', TRUE );
                    $slideops['target'] = get_post_meta( get_the_ID(), 'semi_slider_target', TRUE );
                    
                    if( $thumb ):
                    
                    ?>
                    
                    <li>
                        
                        <?php if( checkurl( $slideops['url'] ) ): ?>
                        
                        <a href="<?php echo $slideops['url']; ?>" target="<?php echo $slideops['target']; ?>"><img src="<?php echo $thumb[0]; ?>" width="<?php echo $thumb[1]; ?>" height="<?php echo $thumb[2]; ?>" alt="<?php the_title_attribute(); ?>" /></a>
                        
                        <?php else: ?>
                        
                        <img src="<?php echo $thumb[0]; ?>" width="<?php echo $thumb[1]; ?>" height="<?php echo $thumb[2]; ?>" alt="<?php the_title_attribute(); ?>" />
                        
                        <?php endif; ?>
                        
                        <?php if( $slideops['caption'] ): ?>
                        
                        <div class="slide-caption<?php if( $slideops['caption_type'] == 'chunky' ) { echo ' slide-caption2'; } ?><?php if( $slideops['caption_position'] == 'left' ) { echo ' slide-caption-left'; } ?>">
                        
                            <?php echo $slideops['caption']; ?>
                        
                        </div>
                        
                        <?php endif; ?>
                        
                    </li>
                    
                    <?php endif; endwhile; ?>
                
                </ul>
                
                
                <script type="text/javascript">
                
                    jQuery(document).ready(function($) {
                    
                        $('#kwicks-slider').kwicks({
                        
                            // *** Appearance ***
                            // The width or height of a fully expanded kwick element
                            max: 800,
                            // The width or height of a fully collapsed kwick element
                            min: null,
                            // The width (in pixels) separating each kwick element
                            spacing: 0,
                            
                            // Kwicks will align vertically if true
                            isVertical: false,
                            
                            // One kwick will always be expanded if true
                            sticky: false,
                            // The initially expanded kwick (if and only if sticky is true). zero based
                            defaultKwick: 0,
                            
                            // Class added to active (open) kwick
                            activeClass: 'active',
                            
                            // *** Interaction ***
                            // The event that triggers the expand effect
                            event: 'mouseenter',
                            // The event that triggers the collapse effect
                            eventClose: 'mouseleave',
                            
                            // *** Functionality ***
                            // The number of milliseconds required for each animation to complete
                            duration: <?php echo $sliderops['speed']; ?>,
                            // Custom animation easing (requires easing plugin if anything
                            // other than 'swing' or 'linear')
                            easing: '<?php echo $sliderops['easing']; ?>',
                            
                            // *** Slideshow ***
                            // Slideshow duration
                            showDuration: <?php echo $sliderops['pause']; ?>,
                            // set to 1 for left-to-right, -1 for right-to-left or 0 for a random slide
                            showNext: 1,
                            
                            // *** Callbacks ***
                            // event called when kwicks has been initialized
                            initialized  : function(kwick){ <?php if( $sliderops['auto'] == 'true' ) { ?>kwick.play();<?php } ?> },
                            // event called when the event occurs (click or mouseover)
                            init: function(kwick) {},
                            // event called before kwicks expanding animation begins
                            expanding: function(kwick) {
                                $('#kwicks-slider').find('.kwick-panel').find('.slide-caption').hide();
                            },
                            // event called before kwicks collapsing animation begins
                            collapsing: function(kwick) {
                                $('#kwicks-slider').find('.kwick-panel').find('.slide-caption').hide();
                            },
                            // event called when animation completes
                            completed: function(kwick) {
                                $('#kwicks-slider').find('.kwick-panel.active').find('.slide-caption').fadeIn();
                            },
                            // event called when slideshow starts
                            playing: function(kwick) {},
                            // event called when slideshow ends
                            paused: function(kwick) {}
                        
                        });
                    
                    });
                
                </script>
                
                <?php if( $fallbackurl ): ?>
                
                <div class="fallback-image">
                
                    <img src="<?php echo $fallbackurl[0]; ?>" width="<?php echo $fallbackurl[1]; ?>" height="<?php echo $fallbackurl[2]; ?>" alt="Slider" />
                
                </div>
                
                <?php endif; ?>
                
                
                <div class="slider-line"></div>
            
            
            </div>
        
        
        </div>
        
<?php endif; wp_reset_postdata(); ?>