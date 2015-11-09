<?php header('Content-Type: text/xml');

$parse_uri = explode('wp-content', $_SERVER['SCRIPT_FILENAME']);
$wp_load = $parse_uri[0].'wp-load.php';
require_once( $wp_load );

if( isset( $_GET['id'] ) AND $_GET['id'] != '' ) {

$sliderpage = new WP_Query( 'page_id=' . $_GET['id'] );

if ($sliderpage->have_posts()) : while ($sliderpage->have_posts()) : $sliderpage->the_post();
    
$sliderops = array();
$sliderops['items'] = is_numeric( get_post_meta( get_the_ID(), 'semi_page_slider_items', TRUE ) ) ? get_post_meta( get_the_ID(), 'semi_page_slider_items', TRUE ) : 5;
$sliderops['order'] = get_post_meta( get_the_ID(), 'semi_page_slider_order', TRUE );
$sliderops['orderby'] = get_post_meta( get_the_ID(), 'semi_page_slider_orderby', TRUE );

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

echo '<?xml version="1.0" encoding="utf-8" ?>';

?>
<Piecemaker>
  <Contents>
    <?php while ( $showslider->have_posts() ) : $showslider->the_post();
    
    $thumb = get_resized_image( 1020, 400, false );
    
    $slideops = array();
    $slideops['caption'] = get_post_meta( get_the_ID(), 'semi_slider_caption', TRUE );
    $slideops['url'] = get_post_meta( get_the_ID(), 'semi_slider_url', TRUE );
    $slideops['target'] = get_post_meta( get_the_ID(), 'semi_slider_target', TRUE );
    
    if( $thumb ):
    
    ?>
    
    <Image Source="<?php echo $thumb[0]; ?>" Title="<?php the_title_attribute(); ?>">
    <?php if( $slideops['caption'] ): ?>
        <Text>&lt;h1&gt;<?php the_title_attribute(); ?>&lt;/h1&gt;&lt;p&gt;<?php echo strip_tags( $slideops['caption'] ); ?>&lt;/p&gt;</Text>
    <?php endif; ?>
    <?php if( checkurl( $slideops['url'] ) ): ?>
        <Hyperlink URL="<?php echo $slideops['url']; ?>" Target="<?php echo $slideops['target']; ?>"></Hyperlink>
    <?php endif; ?>
    </Image>
    
    <?php endif; endwhile; ?>
    
  </Contents>
  <Settings ImageWidth="1020" ImageHeight="400" LoaderColor="0x333333" InnerSideColor="0x222222" SideShadowAlpha="0.8" DropShadowAlpha="0.7" DropShadowDistance="25" DropShadowScale="0.95" DropShadowBlurX="40" DropShadowBlurY="4" MenuDistanceX="20" MenuDistanceY="50" MenuColor1="0x999999" MenuColor2="0x333333" MenuColor3="0xFFFFFF" ControlSize="100" ControlDistance="20" ControlColor1="0x222222" ControlColor2="0xFFFFFF" ControlAlpha="0.8" ControlAlphaOver="0.95" ControlsX="510" ControlsY="280&#xD;&#xA;" ControlsAlign="center" TooltipHeight="30" TooltipColor="0x222222" TooltipTextY="5" TooltipTextStyle="P-Italic" TooltipTextColor="0xFFFFFF" TooltipMarginLeft="5" TooltipMarginRight="7" TooltipTextSharpness="50" TooltipTextThickness="-100" InfoWidth="400" InfoBackground="0xFFFFFF" InfoBackgroundAlpha="0.95" InfoMargin="15" InfoSharpness="0" InfoThickness="0" Autoplay="10" FieldOfView="45"></Settings>
  <Transitions>
    <Transition Pieces="9" Time="1.2" Transition="easeInOutBack" Delay="0.1" DepthOffset="300" CubeDistance="30"></Transition>
    <Transition Pieces="15" Time="3" Transition="easeInOutElastic" Delay="0.03" DepthOffset="200" CubeDistance="10"></Transition>
    <Transition Pieces="5" Time="1.3" Transition="easeInOutCubic" Delay="0.1" DepthOffset="500" CubeDistance="50"></Transition>
    <Transition Pieces="9" Time="1.25" Transition="easeInOutBack" Delay="0.1" DepthOffset="900" CubeDistance="5"></Transition>
  </Transitions>
</Piecemaker>

<?php endif; wp_reset_postdata(); endwhile; endif; wp_reset_postdata(); } ?>