<?php if( is_page_template( 'template-slider.php' ) OR is_page_template( 'template-slider-sidebar.php' ) ):

if( get_post_meta( get_queried_object_id(), 'semi_page_slider_height', true ) != '' AND get_post_meta( get_queried_object_id(), 'semi_page_slider_height', true ) != 400 AND is_numeric( get_post_meta( get_queried_object_id(), 'semi_page_slider_height', true ) ) ):

$sliderheight = get_post_meta( get_queried_object_id(), 'semi_page_slider_height', true );

?>

#slider,
.slider-wrap,
.ei-slider,
.rs-slider,
.kwicks .kwick-panel { height: <?php echo $sliderheight; ?>px; }

#slider.piecemaker-slider,
#slider.slider-nivo,
.nivoSlider { height: auto; }

<?php if( semi_option( 'nonresponsive' ) == 1 ): ?>

#slider.thumb-slider,
#slider.revolution-slider,
#slider.layerslider-wrap,
#slider.slider-nivo,
.nivoSlider { height: auto; }

<?php endif; endif; endif; ?>

<?php if( semi_option( 'nonresponsive' ) != 1 ): ?>

@media only screen and (max-width: 979px) { #wrapper { margin: 0 auto; } }

<?php if( is_page_template( 'template-slider.php' ) OR is_page_template( 'template-slider-sidebar.php' ) ):

if( isset( $sliderheight ) ):

$sliderheight_tab = round( ( $sliderheight / 1020 ) * 768 );

$sliderheight_ml = round( ( $sliderheight / 1020 ) * 480 );

$sliderheight_mp = round( ( $sliderheight / 1020 ) * 320 );

?>

@media only screen and (min-width: 768px) and (max-width: 979px) {

    #slider,
    .slider-wrap,
    .ei-slider,
    .rs-slider,
    #slider.slider-nivo,
    .nivoSlider,
    .camera_wrap,
    #slider.piecemaker-slider { height: <?php echo $sliderheight_tab; ?>px; }

}

@media only screen and (min-width: 480px) and (max-width: 767px) {

    #slider,
    .slider-wrap,
    .ei-slider,
    .rs-slider,
    #slider.slider-nivo,
    .nivoSlider,
    .camera_wrap,
    #slider.piecemaker-slider { height: <?php echo $sliderheight_ml; ?>px; }

}

@media only screen and (max-width: 479px) {

    #slider,
    .slider-wrap,
    .ei-slider,
    .rs-slider,
    #slider.slider-nivo,
    .nivoSlider,
    .camera_wrap,
    #slider.piecemaker-slider { height: <?php echo $sliderheight_mp; ?>px; }

}

<?php endif; endif; ?>

#slider.thumb-slider,
#slider.revolution-slider,
#slider.layerslider-wrap,
#slider.slider-nivo,
.nivoSlider { height: auto; }

<?php if( get_post_meta( get_queried_object_id(), 'semi_page_slider_bg_color', true ) == 0 AND checkhexcolor( get_post_meta( get_queried_object_id(), 'semi_page_slider_bg_color', true ) ) ) {
    
    echo '#slider { background-color: ' . get_post_meta( get_queried_object_id(), 'semi_page_slider_bg_color', true ) . '; }';
    
} ?>

<?php 

    $sliderbgpattern = full_metabox_upload_image( 'semi_page_slider_bg_pattern' );
    
    if( isset( $sliderbgpattern ) AND checkimage( $sliderbgpattern ) ) {
        
        echo '#slider {
    background-image: url("' . $sliderbgpattern . '");
    background-repeat: repeat;
    background-attachment: fixed;
}';
        
    }

?>

<?php endif; ?>