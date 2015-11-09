<?php

if( get_post_meta( get_queried_object_id(), 'semi_page_ptitle_bg_color', true ) AND checkhexcolor( get_post_meta( get_queried_object_id(), 'semi_page_ptitle_bg_color', true ) ) ) {
    
    echo '#page-title { background-color: ' . get_post_meta( get_queried_object_id(), 'semi_page_ptitle_bg_color', true ) . '; }
#page-title h1 { text-shadow: 0px 0px 0px #FFF; }';

}

if( get_post_meta( get_queried_object_id(), 'semi_page_ptitle_font_color', true ) AND checkhexcolor( get_post_meta( get_queried_object_id(), 'semi_page_ptitle_font_color', true ) ) ) {
    
    echo '#page-title h1 {
    color: ' . get_post_meta( get_queried_object_id(), 'semi_page_ptitle_font_color', true ) . ';
    text-shadow: 0px 0px 0px #FFF;
}';

}

$ptitlebgpattern = full_metabox_upload_image( 'semi_page_ptitle_bg_pattern' );

if( isset( $ptitlebgpattern ) AND checkimage( $ptitlebgpattern ) ) {
    
    echo '#page-title {
    background-image: url("' . $ptitlebgpattern . '");
    background-repeat: repeat;
    background-attachment: fixed;
    border: none;
}';
    
}

$ptitlebgimage = full_metabox_upload_image( 'semi_page_ptitle_bg_image' );

if( isset( $ptitlebgimage ) AND checkimage( $ptitlebgimage ) ) {
    
    echo '#page-title {
    background: url(' . $ptitlebgimage . ') no-repeat center center fixed; 
    -webkit-background-size: cover;
    -moz-background-size: cover;
    -o-background-size: cover;
    background-size: cover;
    border: none;
}

#page-title h1,
.breadcrumb {
    display: inline-block;
    background-color: rgba(0,0,0,0.3) !important;
    padding: 7px 10px 5px;
    color: #FFF !important;
    text-shadow: 1px 1px 1px rgba(0,0,0,0.4) !important;
    border-radius: 3px !important;
    -moz-border-radius: 3px !important;
    -webkit-border-radius: 3px !important;
}

#page-title h1 span {
    color: #DDD;
    font-size: 14px;
    line-height: 20px;
}

.breadcrumb {
    display: block;
    margin: -13px 0 0 0 !important;
    padding: 3px 7px !important;
}

.breadcrumb li { text-shadow: 1px 1px 1px rgba(0,0,0,0.4) !important; }

.breadcrumb a { color: #DDD !important; }

.breadcrumb .active,
.breadcrumb .divider,
.breadcrumb a:hover { color: #FFF !important; }';

}

?>