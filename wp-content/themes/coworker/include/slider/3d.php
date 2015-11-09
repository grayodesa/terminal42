<?php

$sliderpageid = get_the_ID();

$sliderops = array();
$sliderops['items'] = is_numeric( get_post_meta( get_the_ID(), 'semi_page_slider_items', TRUE ) ) ? get_post_meta( get_the_ID(), 'semi_page_slider_items', TRUE ) : 5;
$sliderops['order'] = get_post_meta( get_the_ID(), 'semi_page_slider_order', TRUE );
$sliderops['orderby'] = get_post_meta( get_the_ID(), 'semi_page_slider_orderby', TRUE );
$sliderops['auto'] = ( get_post_meta( get_the_ID(), 'semi_page_slider_auto', TRUE ) == 1 ) ? 'true' : 'false';
$sliderops['menu'] = ( get_post_meta( get_the_ID(), 'semi_page_slider_menu', TRUE ) == 1 ) ? 'true' : 'false';

$fallbackthumb = rwmb_meta( 'semi_page_slider_fallback', 'type=image&size=full' );

if( $fallbackthumb ) {
    
    $fallbackcount = 0;
    
    foreach ( $fallbackthumb as $fallback ):
    
        $fallbackcount++;
        
        $fallbackurl = semi_resize( $fallback['full_url'], 1020, 400, true, false );
        
        if( $fallbackcount == 1 ) { break; }
    
    endforeach;
    
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
<div id="slider" class="piecemaker-slider">
        
            <div class="container clearfix">
            
            
                <div class="slider-3d">
                
                    <div id="piecemaker">
                      <p><?php _e( 'Put your alternative Non Flash content here.', 'coworker' ); ?></p>
                    </div>
                
                </div>
                
                <div class="slider-line"></div>
                
                <script type="text/javascript">
                
                    jQuery(document).ready(function ($) {
                
                        var flashvars = {};
                        flashvars.cssSource = "<?php echo get_template_directory_uri(); ?>/include/piecemaker/piecemaker.css";
                        flashvars.xmlSource = "<?php echo get_template_directory_uri(); ?>/include/piecemaker/piecemaker.php?id=<?php echo $sliderpageid; ?>";
                        
                        var params = {};
                        params.play = "<?php echo $sliderops['auto']; ?>";
                        params.menu = "<?php echo $sliderops['menu']; ?>";
                        params.scale = "showall";
                        params.wmode = "transparent";
                        params.allowfullscreen = "true";
                        params.allowscriptaccess = "always";
                        params.allownetworking = "all";
                        
                        swfobject.embedSWF('<?php echo get_template_directory_uri(); ?>/include/piecemaker/piecemaker.swf', 'piecemaker', '1200', '500', '0', null, flashvars, params, null);
                    
                    });
                
                </script>
                
                <?php if( $fallbackurl ): ?>
                
                <div class="fallback-image">
                
                    <img src="<?php echo $fallbackurl[0]; ?>" width="<?php echo $fallbackurl[1]; ?>" height="<?php echo $fallbackurl[2]; ?>" alt="Slider" />
                
                </div>
                
                <?php endif; ?>
            
            
            </div>
        
        
        </div>
        
<?php endif; wp_reset_postdata(); ?>