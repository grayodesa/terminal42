<?php

$thisbgimage = false;

if( semi_option( 'layout' ) != 'full' ):

if( get_post_meta( get_queried_object_id(), 'semi_page_bg_image_disable', true ) == 0 ) {

    $this_bgimage = rwmb_meta( 'semi_page_bg_image', 'type=image&size=full' );
    
    if( $this_bgimage ) {
        
        $bgii = 0;
        
        foreach ( $this_bgimage as $this_bgi ){
            
            if( $bgii == 1 ) {
                break;
            } else {
                $thisbgimage = $this_bgi['full_url'];
                $bgii++;
            }
            
        }
        
    } else {
        
        if( semi_option( 'bgimage_enable' ) == 1 AND semi_option( 'bgimage' ) != '' ) {
            
            $thisbgimage = semi_option( 'bgimage' );
            
        } else { $thisbgimage = false; }
        
    }

}

endif;

if( semi_option( 'bgcolor' ) != '' ) {
    
    echo 'body { background-color: ' . semi_option( 'bgcolor' ) . '; }';
    
}

if( semi_option( 'bgpattern_enable' ) == 1 AND $thisbgimage == false ) {
    
    if( semi_option( 'bgpattern_upload' ) != '' ) {
        echo 'body { background-image: url("' . semi_option( 'bgpattern_upload' ) . '"); }';
    } elseif( semi_option( 'bgpattern' ) != '' ) {
        echo 'body { background-image: url("' . get_template_directory_uri() . '/images/patterns/' . semi_option( 'bgpattern' ) . '"); }';
    }
    
}

if( semi_option( 'layout' ) != 'full' ):

    if( isset( $thisbgimage ) AND $thisbgimage != '' ) {
    
        echo 'body { 
        background: url(' . $thisbgimage . ') no-repeat center center fixed; 
        -webkit-background-size: cover;
        -moz-background-size: cover;
        -o-background-size: cover;
        background-size: cover;
    }';

}

endif;

if( semi_option( 'bgimage_enable' ) != 1 AND semi_option( 'bgpattern_enable' ) != 1 ) {

    echo 'body { background-image: none; }';
    
}

?>