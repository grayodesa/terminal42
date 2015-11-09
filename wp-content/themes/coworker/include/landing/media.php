<?php if( get_post_meta( get_the_ID(), 'semi_page_landing_media', TRUE ) == 'image' ):

$landingimage = rwmb_meta( 'semi_page_landing_media_image', 'type=image&size=full' );

if( $landingimage ) {
    
    $landingimagecount = 0;
    
    foreach ( $landingimage as $landingmedia ):
    
        $landingimagecount++;
        
        $landingimageurl = semi_resize( $landingmedia['full_url'], 650, null, true, true );
        
        if( $landingimagecount == 1 ) { break; }
    
    endforeach;
    
} ?>

<img src="<?php echo $landingimageurl; ?>" alt="<?php the_title_attribute(); ?>" title="<?php the_title_attribute(); ?>" />

<?php elseif( get_post_meta( get_the_ID(), 'semi_page_landing_media', TRUE ) == 'video' ):

echo stripslashes( htmlspecialchars_decode( get_post_meta( get_the_ID(), 'semi_page_landing_media_video', TRUE ) ) );

elseif( get_post_meta( get_the_ID(), 'semi_page_landing_media', TRUE ) == 'slider' ):

$sliderops = array();
$sliderops['items'] = is_numeric( get_post_meta( get_the_ID(), 'semi_page_landing_media_sitems', TRUE ) ) ? get_post_meta( get_the_ID(), 'semi_page_landing_media_sitems', TRUE ) : 5;
$sliderops['order'] = get_post_meta( get_the_ID(), 'semi_page_landing_media_sorder', TRUE );
$sliderops['orderby'] = get_post_meta( get_the_ID(), 'semi_page_landing_media_sorderby', TRUE );
$sliderops['animation'] = get_post_meta( get_the_ID(), 'semi_page_landing_media_sanimation', TRUE ) ? ' data-animate="' . get_post_meta( get_the_ID(), 'semi_page_landing_media_sanimation', TRUE ) . '"' : '';
$sliderops['direction'] = get_post_meta( get_the_ID(), 'semi_page_landing_media_sdirection', TRUE ) ? ' data-direction="' . get_post_meta( get_the_ID(), 'semi_page_landing_media_sdirection', TRUE ) . '"' : '';
$sliderops['easing'] = get_post_meta( get_the_ID(), 'semi_page_landing_media_seasing', TRUE ) != 'swing' ? ' data-easing="' . get_post_meta( get_the_ID(), 'semi_page_landing_media_seasing', TRUE ) . '"' : '';
$sliderops['auto'] = ( get_post_meta( get_the_ID(), 'semi_page_landing_media_sauto', TRUE ) != 1 ) ? ' data-slideshow="false"' : '';
$sliderops['pause'] = is_numeric( get_post_meta( get_the_ID(), 'semi_page_landing_media_spause', TRUE ) ) ? ' data-pause="' . get_post_meta( get_the_ID(), 'semi_page_landing_media_spause', TRUE ) . '"' : '';
$sliderops['speed'] = is_numeric( get_post_meta( get_the_ID(), 'semi_page_landing_media_sspeed', TRUE ) ) ? ' data-speed="' . get_post_meta( get_the_ID(), 'semi_page_landing_media_sspeed', TRUE ) . '"' : '';

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

<div class="fslider" data-video="true"<?php echo $sliderops['animation'] . $sliderops['direction'] . $sliderops['easing'] . $sliderops['auto'] . $sliderops['pause'] . $sliderops['speed']; ?>>
                    
                        <div class="flexslider">
                        
                        
                            <div class="slider-wrap">
                            
                            <?php while ( $showslider->have_posts() ) : $showslider->the_post();
                            
                            $thumb = get_resized_image( 650, null, false );
                            
                            $slideops = array();
                            $slideops['url'] = get_post_meta( get_the_ID(), 'semi_slider_url', TRUE );
                            $slideops['target'] = get_post_meta( get_the_ID(), 'semi_slider_target', TRUE );
                            $slideops['video'] = get_post_meta( get_the_ID(), 'semi_slider_video', TRUE );
                            
                            ?>
                            
                                <div class="slide" <?php if( $sliderops['thumbs'] == '"thumbnails"' ) { echo 'data-thumb="' . get_resized_image( 102, 55 ) . '"'; } ?>>
                                
                                <?php if( $slideops['video'] ): ?>
                                
                                    <?php echo stripslashes( htmlspecialchars_decode( $slideops['video'] ) ); ?>
                                
                                <?php else: ?>
                                    
                                    <?php if( checkurl( $slideops['url'] ) ): ?>
                                    
                                    <a href="<?php echo $slideops['url']; ?>" target="<?php echo $slideops['target']; ?>"><img src="<?php echo $thumb[0]; ?>" width="<?php echo $thumb[1]; ?>" height="<?php echo $thumb[2]; ?>" alt="<?php the_title_attribute(); ?>" /></a>
                                    
                                    <?php else: ?>
                                    
                                    <img src="<?php echo $thumb[0]; ?>" width="<?php echo $thumb[1]; ?>" height="<?php echo $thumb[2]; ?>" alt="<?php the_title_attribute(); ?>" />
                                    
                                    <?php endif; ?>
                                    
                                <?php endif; ?>
                                
                                </div>
                            
                            <?php endwhile; ?>
                            
                            </div>
                        
                        
                        </div>
                    
                    </div>

<?php

endif; wp_reset_postdata();

elseif( get_post_meta( get_the_ID(), 'semi_page_landing_media', TRUE ) == 'html' ):

echo do_shortcode( stripslashes( htmlspecialchars_decode( get_post_meta( get_the_ID(), 'semi_page_landing_media_html', TRUE ) ) ) );

?>

<?php endif; ?>