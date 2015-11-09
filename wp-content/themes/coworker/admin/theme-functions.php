<?php


/*--------------------------------------------------------
    Validation Checks
--------------------------------------------------------*/


function checkurl($url) {
    return preg_match('|^http(s)?://[a-z0-9-]+(.[a-z0-9-]+)*(:[0-9]+)?(/.*)?$|i', $url);
}


function checkhexcolor($color) {
    return preg_match('/^#[a-f0-9]{6}$/i', $color);
}


function checkemail($email){
	return eregi("^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$", $email);
}

function checkimage($image){
	return preg_match('#^http:\/\/(.*)\.(gif|png|jpg|bmp|jpeg)$#i', $image);
}

function check_currenturl() {
    
    $pageURL = 'http';
    
    if ( isset( $_SERVER["HTTPS"] ) AND $_SERVER["HTTPS"] == "on" ) {$pageURL .= "s";}
    
    $pageURL .= "://";
    
    if ($_SERVER["SERVER_PORT"] != "80") {
        $pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
    } else {
        $pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
    }
    
    return $pageURL;

}


/*--------------------------------------------------------
    Theme Options Function
--------------------------------------------------------*/


function semi_option( $optionid, $sanitize = false ) {
    
    global $smof_data;
    
    if( $sanitize == true ) {
        
        $output = isset( $smof_data[ $optionid ] ) ? stripslashes( $smof_data[ $optionid ] ) : false;
        
    } else {
        
        $output = isset( $smof_data[ $optionid ] ) ? $smof_data[ $optionid ] : false;
        
    }
    
    return $output;
    
}


/*--------------------------------------------------------
    Show Comment Count
--------------------------------------------------------*/


function show_comment_count() {
        
    if( get_post_meta( get_the_ID(), 'semi_post_comments_system', TRUE ) == 'themeoption' ):
    
        if ( semi_option('blog_comments_type') == 'facebook' OR semi_option('blog_comments_type') == 'disqus' OR semi_option('blog_comments_type') == 'gplus' ) :
        
        _e( 'View Comments', 'coworker' );
        
        else :
        
        comments_number( __( 'No Comments', 'coworker' ), __( '1 Comment', 'coworker' ), __( '% Comments', 'coworker' ) );
        
        endif;
        
    else:
    
        if ( get_post_meta( get_the_ID(), 'semi_post_comments_system', TRUE ) == 'facebook' OR get_post_meta( get_the_ID(), 'semi_post_comments_system', TRUE ) == 'disqus' OR get_post_meta( get_the_ID(), 'semi_post_comments_system', TRUE ) == 'gplus' ) :
        
        _e( 'View Comments', 'coworker' );
        
        else :
        
        comments_number( __( 'No Comments', 'coworker' ), __( '1 Comment', 'coworker' ), __( '% Comments', 'coworker' ) );
        
        endif;
    
    endif;
    
}


/*--------------------------------------------------------
    Get Attachment ID by Image Link
--------------------------------------------------------*/


function get_attachment_id_by_src($image_src) {

    global $wpdb;
    $query = "SELECT ID FROM {$wpdb->posts} WHERE guid='$image_src'";
    $id = $wpdb->get_var($query);
    return $id;

}


/*--------------------------------------------------------
    Get Page Sidebar Alignment
--------------------------------------------------------*/


function page_sidebar_align() {

    if( get_post_meta( get_the_ID(), 'semi_page_sidebar', TRUE ) == 'left' ) { echo ' col_last'; }

}


/*--------------------------------------------------------
    Custom Excerpt
--------------------------------------------------------*/


function custom_excerpt($limit) {
    
    $excerpt = explode(' ', get_the_excerpt(), $limit);
    
    if (count($excerpt)>=$limit) {
        array_pop($excerpt);
        $excerpt = implode(" ",$excerpt).'...';
    } else {
        $excerpt = implode(" ",$excerpt);
    }
    	
    $excerpt = preg_replace('`\[[^\]]*\]`','',$excerpt);
    return $excerpt;
    
}


/*--------------------------------------------------------
    Custom Text Limit
--------------------------------------------------------*/


function custom_textlimit($text,$limit) {
    
    $excerpt = explode(' ', $text, $limit);
    
    if (count($excerpt)>=$limit) {
        array_pop($excerpt);
        $excerpt = implode(" ",$excerpt).'...';
    } else {
        $excerpt = implode(" ",$excerpt);
    }
    	
    $excerpt = preg_replace('`\[[^\]]*\]`','',$excerpt);
    return $excerpt;
    
}


function get_header_class() {
    
    if( semi_option( 'header_style' ) == 'header2' ) {
        echo 'class="header2"';
    } elseif( semi_option( 'header_style' ) == 'header3' ) {
        echo 'class="header3"';
    } elseif( semi_option( 'header_style' ) == 'header4' ) {
        echo 'class="header3 header4"';
    } elseif( semi_option( 'header_style' ) == 'header5' ) {
        echo 'class="header3 header6"';
    } elseif( semi_option( 'header_style' ) == 'header6' ) {
        echo 'class="header7"';
    }

}


if ( ! function_exists( 'get_top_contacts' ) ) {
    function get_top_contacts() { ?>
        
    <ul id="lp-contacts">
                    
                        <?php if( semi_option( 'ts_phone' ) != '' ): ?><li><i class="icon-phone"></i><?php echo semi_option( 'ts_phone_text' ) ? semi_option( 'ts_phone_text' ) : __( 'Call Us', 'coworker' ); ?><span><?php echo semi_option( 'ts_phone' ); ?></span></li><?php endif; ?>
                        
                        <?php if( semi_option( 'ts_email' ) != '' ): ?><li><i class="icon-envelope-alt"></i><?php echo semi_option( 'ts_email_text' ); ?><span><?php echo semi_option( 'ts_email' ); ?></span></li><?php endif; ?>
                    
                    </ul>
        
    <?php }
}


function get_header_menu() {
    
    if( semi_option( 'header_style' ) == 'header6' ) {
            
        $menuargs = array(
                        'theme_location' => 'primary',
                        'container' => '',
                        'fallback_cb' => '',
                        'walker' => new pmenu_subtitle_walker2()
                    );
        
    } elseif( semi_option( 'header_style' ) == 'header1' ) {
        
        $menuargs = array(
                        'theme_location' => 'primary',
                        'container' => '',
                        'fallback_cb' => '',
                        'walker' => new pmenu_subtitle_walker()
                    );
        
    }
    
    if( semi_option( 'header_style' ) == 'header1' OR semi_option( 'header_style' ) == 'header6' ) {

?>

<div id="primary-menu">
            
            
                <?php wp_nav_menu( $menuargs ); ?>
            
            
            </div>

<?php }

}


function get_header_menu2() {
    
    if( semi_option( 'header_style' ) == 'header2' ) {
        
        $menuargs = array(
                        'theme_location' => 'primary',
                        'container' => '',
                        'fallback_cb' => '',
                        'walker' => new pmenu_subtitle_walker()
                    );
        
    } elseif( semi_option( 'header_style' ) == 'header3' ) {
        
        $menuargs = array(
                        'theme_location' => 'primary',
                        'container' => '',
                        'fallback_cb' => '',
                        'walker' => new pmenu_subtitle_walker2()
                    );
        
    } elseif( semi_option( 'header_style' ) == 'header4' ) {
        
        $menuargs = array(
                        'theme_location' => 'primary',
                        'container' => '',
                        'fallback_cb' => '',
                        'walker' => new pmenu_subtitle_walker2()
                    );
        
    } elseif( semi_option( 'header_style' ) == 'header5' ) {
        
        $menuargs = array(
                        'theme_location' => 'primary',
                        'container' => '',
                        'fallback_cb' => '',
                        'walker' => new pmenu_subtitle_walker2()
                    );
        
    }

    if( semi_option( 'header_style' ) == 'header2' OR semi_option( 'header_style' ) == 'header3' OR semi_option( 'header_style' ) == 'header4' OR semi_option( 'header_style' ) == 'header5' ) {

?>

<div id="primary-menu">
                
                    <div class="container">
                    
                        <?php wp_nav_menu( $menuargs ); ?>
                    
                    </div>
                
                </div>

<?php }

}


if ( ! function_exists( 'get_header_rightcontent' ) ) {
    function get_header_rightcontent() {
        
        if( semi_option( 'header_style' ) == 'header2' OR semi_option( 'header_style' ) == 'header3' OR semi_option( 'header_style' ) == 'header5' ) {
            
            if( semi_option( 'header_right' ) == 'contact' ) {
                get_top_contacts();
            } else { ?>
                <div id="top-search"><?php get_search_form(); ?></div>
            <?php }
        
        }
    
    }
}


function get_header_icons() {
    
    if( semi_option( 'ts_customicons' ) == 1 ) {
    
        $customicons = semi_option('customsocialicons');
        
        if( $customicons ):
        
            echo '<div id="top-custom-social"><ul>';
            
            foreach( $customicons as $customicon ):
            
                if( $customicon['title'] != '' AND ( $customicon['url'] != '' AND checkimage( $customicon['url'] ) ) AND ( $customicon['link'] != '' AND checkurl( $customicon['link'] ) ) ) {
                    echo '<li><a target="_blank" href="' . $customicon['link'] . '" title="' . $customicon['title'] . '" class="stip"><img src="' . $customicon['url'] . '" alt="' . $customicon['title'] . '"></a></li>';
                }
            
            endforeach;
            
            echo '</ul></div>';
        
        endif;
    
    } else {
    
?>
    
        <div id="top-social">
        
            <ul>
            
                <?php if( semi_option( 'ts_facebook' ) != '' ): ?><li class="ts-facebook"><a target="_blank" href="<?php echo semi_option( 'ts_facebook' ); ?>"><div class="ts-icon"></div><div class="ts-text">Facebook</div></a></li><?php endif; ?>
                
                <?php if( semi_option( 'ts_twitter' ) != '' ): ?><li class="ts-twitter"><a target="_blank" href="<?php echo semi_option( 'ts_twitter' ); ?>"><div class="ts-icon"></div><div class="ts-text">Twitter</div></a></li><?php endif; ?>
                
                <?php if( semi_option( 'ts_gplus' ) != '' ): ?><li class="ts-gplus"><a target="_blank" href="<?php echo semi_option( 'ts_gplus' ); ?>"><div class="ts-icon"></div><div class="ts-text">Google+</div></a></li><?php endif; ?>
                
                <?php if( semi_option( 'ts_dribbble' ) != '' ): ?><li class="ts-dribbble"><a target="_blank" href="<?php echo semi_option( 'ts_dribbble' ); ?>"><div class="ts-icon"></div><div class="ts-text">Dribbble</div></a></li><?php endif; ?>

                <?php if( semi_option( 'ts_instagram' ) != '' ): ?><li class="ts-instagram"><a target="_blank" href="<?php echo semi_option( 'ts_instagram' ); ?>"><div class="ts-icon"></div><div class="ts-text">Instagram</div></a></li><?php endif; ?>
                
                <?php if( semi_option( 'ts_pinterest' ) != '' ): ?><li class="ts-pinterest"><a target="_blank" href="<?php echo semi_option( 'ts_pinterest' ); ?>"><div class="ts-icon"></div><div class="ts-text">Pinterest</div></a></li><?php endif; ?>
                
                <?php if( semi_option( 'ts_forrst' ) != '' ): ?><li class="ts-forrst"><a target="_blank" href="<?php echo semi_option( 'ts_forrst' ); ?>"><div class="ts-icon"></div><div class="ts-text">Forrst</div></a></li><?php endif; ?>
                
                <?php if( semi_option( 'ts_blogger' ) != '' ): ?><li class="ts-blogger"><a target="_blank" href="<?php echo semi_option( 'ts_blogger' ); ?>"><div class="ts-icon"></div><div class="ts-text">Blogger</div></a></li><?php endif; ?>
                
                <?php if( semi_option( 'ts_flickr' ) != '' ): ?><li class="ts-flickr"><a target="_blank" href="<?php echo semi_option( 'ts_flickr' ); ?>"><div class="ts-icon"></div><div class="ts-text">Flickr</div></a></li><?php endif; ?>
                
                <?php if( semi_option( 'ts_linkedin' ) != '' ): ?><li class="ts-linkedin"><a target="_blank" href="<?php echo semi_option( 'ts_linkedin' ); ?>"><div class="ts-icon"></div><div class="ts-text">LinkedIn</div></a></li><?php endif; ?>
                
                <?php if( semi_option( 'ts_rss' ) == 1 ): ?><li class="ts-rss"><a target="_blank" href="<?php bloginfo('rss2_url'); ?>"><div class="ts-icon"></div><div class="ts-text">RSS</div></a></li><?php endif; ?>
                
                <?php if( semi_option( 'ts_vimeo' ) != '' ): ?><li class="ts-vimeo"><a target="_blank" href="<?php echo semi_option( 'ts_vimeo' ); ?>"><div class="ts-icon"></div><div class="ts-text">Vimeo</div></a></li><?php endif; ?>
                
                <?php if( semi_option( 'ts_youtube' ) != '' ): ?><li class="ts-youtube"><a target="_blank" href="<?php echo semi_option( 'ts_youtube' ); ?>"><div class="ts-icon"></div><div class="ts-text">Youtube</div></a></li><?php endif; ?>
                
                <?php if( !is_page_template( 'template-comingsoon.php' ) ): ?>
                
                <?php if( semi_option( 'ts_phone' ) != '' ): ?><li class="ts-phone"><a target="_blank" href="tel:<?php echo str_replace( array( ' ', '.', '-' ), '', semi_option( 'ts_phone' ) ); ?>"><div class="ts-icon"></div><div class="ts-text"><?php echo str_replace( ' ', '&nbsp;', semi_option( 'ts_phone' ) ); ?></div></a></li><?php endif; ?>
                
                <?php if( semi_option( 'ts_email' ) != '' ): ?><li class="ts-mail"><a target="_blank" href="mailto:<?php echo semi_option( 'ts_email' ); ?>"><div class="ts-icon"></div><div class="ts-text"><?php echo str_replace( ' ', '&nbsp;', semi_option( 'ts_email_text' ) ); ?></div></a></li><?php endif; ?>
                
                <?php endif; ?>
            
            </ul>
        
        </div>
    
<?php }

}


function full_metabox_upload_image( $meta ) {
    
    $upload_meta = rwmb_meta( $meta, 'type=image&size=full' );
    
    if( $upload_meta ) {
        
        $uploadi = 0;
        
        foreach ( $upload_meta as $upload_metaimg ){
            
            if( $uploadi == 1 ) {
                break;
            } else {
                $upload_metaimg = $upload_metaimg['full_url'];
                $uploadi++;
            }
            
        }
        
        return $upload_metaimg;
        
    } else { return false; }
    
}


function get_native_audio_file( $meta ) {
    
    $upload_meta = rwmb_meta( $meta, 'type=file' );
    
    if( $upload_meta ) {
        
        $uploadi = 0;
        
        foreach ( $upload_meta as $upload_metafile ){
            
            if( $uploadi == 1 ) {
                break;
            } else {
                $upload_metafile = $upload_metafile['url'];
                $uploadi++;
            }
            
        }
        
        return $upload_metafile;
        
    } else { return false; }
    
}


/*--------------------------------------------------------
    Get Form Bot Protect
--------------------------------------------------------*/


function get_form_bot_protect() {

    $operators = array( 'p', 'i' );

    $number1 = rand(1,10);

    $number2 = rand(1,10);

    $output = "<label class='sm-fw'>\" $number1</label><input type='hidden' name='sm_ch_fw_number1' value='$number1' />";

    $op = array_rand( $operators, 1 );

    if( $operators[ $op ] == 'p' ) {
        $output .= "<label class='sm-fw'>+</label><input type='hidden' name='sm_ch_fw_operator' value='p' />";
    } elseif( $operators[ $op ] == 'i' ) {
        $output .= "<label class='sm-fw'>x</label><input type='hidden' name='sm_ch_fw_operator' value='i' />";
    }

    $output .= "<label class='sm-fw'>$number2 \"</label><input type='hidden' name='sm_ch_fw_number2' value='$number2' /><label>" . __( 'equals to', 'coworker' ) . "</label> <input type='text' name='sm_ch_fw_output' class='sm-fw required number' value='' maxlength='3' />";

    echo $output;

}


/*--------------------------------------------------------
    Get Post Icon
--------------------------------------------------------*/


function get_post_icon() {
    
    if( get_post_format() == 'image' ) {
        $posticon = 'camera';
    } elseif( get_post_format() == 'gallery' ) {
        $posticon = 'picture';
    } elseif( get_post_format() == 'video' ) {
        $posticon = 'film';
    } elseif( get_post_format() == 'audio' ) {
        $posticon = 'music';
    } else {
        $posticon = 'pencil';
    }
    
    return $posticon;
    
}


/*--------------------------------------------------------
    Get Post Thumb
--------------------------------------------------------*/


if( !function_exists( 'get_resized_image' ) ){
    function get_resized_image( $w, $h, $single = true ) {
    
        $image = wp_get_attachment_image_src( get_post_thumbnail_id( get_the_ID() ), 'full');
        
        return semi_resize( $image[0], $w, $h, true, $single );
    
    }
}

if( !function_exists( 'get_full_image' ) ){
    function get_full_image( $array = false ) {
        
        $image = wp_get_attachment_image_src( get_post_thumbnail_id( get_the_ID() ), 'full');
        
        if( $array ) { return $image; } else { return $image[0]; }
        
    }
}

if( !function_exists( 'get_sized_image' ) ){
    function get_sized_image( $size, $array = false ) {
        
        $image = wp_get_attachment_image_src( get_post_thumbnail_id( get_the_ID() ), $size);
        
        if( $array ) { return $image; } else { return $image[0]; }
        
    }
}


/*--------------------------------------------------------
    Get Portfolio Choosen Categories
--------------------------------------------------------*/


function choosen_port_cat() {
    
    $portcats = wp_get_object_terms( get_the_ID(), 'port-group' );
    
    $portcatids = array();
    
    if( count( $portcats ) > 0 ) {
        
        foreach ( $portcats as $portcat) {
            $portcatids[] = $portcat->term_id;
        }
        
        return $portcatids;
    
    } else { return false; }
    
}


/*--------------------------------------------------------
    Get Slider Options
--------------------------------------------------------*/


function get_fslider_ops( $prefix = 'semi_port_' ) {
    
    $sliderops = array();
    
    if( get_post_meta( get_the_ID(), $prefix . 'g_animation', TRUE ) ) { $sliderops[] = 'data-animate="' . get_post_meta( get_the_ID(), $prefix . 'g_animation', TRUE ) . '"'; }
    if( get_post_meta( get_the_ID(), $prefix . 'g_easing', TRUE ) ) { $sliderops[] = 'data-easing="' . get_post_meta( get_the_ID(), $prefix . 'g_easing', TRUE ) . '"'; }
    if( get_post_meta( get_the_ID(), $prefix . 'g_direction', TRUE ) ) { $sliderops[] = 'data-direction="' . get_post_meta( get_the_ID(), $prefix . 'g_direction', TRUE ) . '"'; }
    if( get_post_meta( get_the_ID(), $prefix . 'g_slideshow', TRUE ) ) { $sliderops[] = 'data-slideshow="' . get_post_meta( get_the_ID(), $prefix . 'g_slideshow', TRUE ) . '"'; }
    if( get_post_meta( get_the_ID(), $prefix . 'g_pause', TRUE ) ) { $sliderops[] = 'data-pause="' . get_post_meta( get_the_ID(), $prefix . 'g_pause', TRUE ) . '"'; }
    if( get_post_meta( get_the_ID(), $prefix . 'g_speed', TRUE ) ) { $sliderops[] = 'data-speed="' . get_post_meta( get_the_ID(), $prefix . 'g_speed', TRUE ) . '"'; }
    
    $sliderops = implode( ' ', $sliderops );
    
    return $sliderops;
    
}


/*--------------------------------------------------------
    Portfolio Items
--------------------------------------------------------*/


if( !function_exists( 'get_portfolio_items' ) ){

    function get_portfolio_items( $width, $height, $column = 0, $description = true, $autoheight = false, $carousel = false ) {
        
        $getterms = get_the_terms( get_the_ID(), 'port-group' );
        
        if ( $getterms ) {
        
            $portterms = array();
            
            $porttermnames = array();
            
            foreach ($getterms as $getterm) {
            	$portterms[] = 'pf-' . $getterm->slug;
                $porttermnames[] = '<a href="' . get_term_link( $getterm->slug, 'port-group' ) . '" title="' . sprintf(__('View all post filed under %s', 'coworker'), $getterm->name) . '">' . $getterm->name . '</a>';
            }
            
            $portterms = implode( " ", $portterms );
            $porttermnames = implode( " &middot; ", $porttermnames );
        
        }
        
        if( $column == 5 AND $autoheight == true ) { $height = is_numeric( get_post_meta( get_the_ID(), 'semi_port_height', TRUE ) ) ? get_post_meta( get_the_ID(), 'semi_port_height', TRUE ) : 200 ; }
        
        $thumb = get_sized_image( 'full', true );
        
        $thumb = semi_resize( $thumb[0], $width, $height, true, false );
        
        $fullthumb = get_full_image();
        
        $portfolio_type = get_post_meta( get_the_ID(), 'semi_port_type', TRUE );
        
        if( $column != 5 ) {
        
            $sliderops = get_fslider_ops();
        
        }

        $portgalleryimages = '';
        
        if( $portfolio_type == 'video' ) {
            if( get_post_meta( get_the_ID(), 'semi_port_vyurl', TRUE ) != '' ) {
                $item_link = '<a href="' . get_post_meta( get_the_ID(), 'semi_port_vyurl', TRUE ) . '" class="p-o-video" data-lightbox="iframe" title="' . the_title_attribute( 'echo=0' ) . '"></a>';
            } else {
                $item_link = '<a href="' . get_permalink() . '" class="p-o-video"></a>';
            }
        } elseif( $portfolio_type == 'gallery' ) {
            
            $item_link = '<a href="' . $fullthumb . '" class="p-o-gallery" data-lightbox="gallery-item" title="' . the_title_attribute( 'echo=0' ) . '"></a>';

            $portgallery = rwmb_meta( 'semi_port_gallery', 'type=image&size=full' );
            
            foreach ( $portgallery as $portgallery_image ):
            
            $portgalleryimages .= '<a class="hidden" href="' . $portgallery_image['full_url'] . '" data-lightbox="gallery-item"></a>';
            
            endforeach;

        } else {
            $item_link = '<a href="' . $fullthumb . '" class="p-o-image" data-lightbox="image" title="' . the_title_attribute( 'echo=0' ) . '"></a>';
        }
    
    ?>
                        <div id="portfolio-<?php the_ID(); ?>" class="portfolio-item <?php echo $portterms; ?>">
                            
                                <div class="portfolio-image<?php if( $portfolio_type == 'gallery' AND $column != 5 ) { echo ' port-gallery'; } ?>">
                                
                                    <?php if( $portfolio_type == 'gallery' AND $column != 5 ) {
                                    
                                    $portgallery = rwmb_meta( 'semi_port_gallery', 'type=image&size=full' );
                                        
                                    ?>
                                    
                                    <div class="fslider" <?php echo $sliderops; ?>>
                                    
                                        <div class="flexslider">
                                        
                                            <div class="slider-wrap">
                                            
                                                <div class="slide">
                                                
                                                    <img src="<?php echo $thumb[0]; ?>" width="<?php echo $thumb[1]; ?>" height="<?php echo $thumb[2]; ?>" alt="<?php the_title_attribute(); ?>" title="<?php the_title_attribute(); ?>" />
                                                
                                                </div>
                                                
                                            <?php
                                            
                                            foreach ( $portgallery as $portgallery_image ): ?>
                                                
                                                <div class="slide">
                                                
                                                    <a href="<?php echo $portgallery_image['full_url']; ?>" title="<?php echo $portgallery_image['alt']; ?>">
                                                    
                                                        <img src="<?php echo semi_resize( $portgallery_image['url'], $width, $height, true ); ?>" width="<?php echo $width; ?>" height="<?php echo $height; ?>" alt="<?php echo $portgallery_image['alt']; ?>" />
                                                    
                                                    </a>
                                                
                                                </div>
                                            
                                            <?php endforeach; ?>
                                            
                                            </div>
                                        
                                        </div>
                                    
                                    </div>
                                    
                                    <?php } else { ?>
                                    
                                    <a href="<?php the_permalink(); ?>"><img src="<?php echo $thumb[0]; ?>" width="<?php echo $thumb[1]; ?>" height="<?php echo $thumb[2]; ?>" alt="<?php the_title_attribute(); ?>" title="<?php the_title_attribute(); ?>" /></a>
                                    
                                    <?php } ?>
                                    
                                    <div class="portfolio-overlay">
                                    
                                        <?php if( $portfolio_type != 'gallery' ) { if( $column != 5 ) { if( $description == true ) { ?>
                                        
                                        <div class="portfolio-overlay-wrap">
                                        
                                            <p><?php echo custom_textlimit( get_the_excerpt(), 15 ); ?></p>
                                            
                                            <span><span></span></span>
                                            
                                            <?php echo $porttermnames; ?>
                                        
                                        </div>
                                        
                                        <?php } } } ?>
                                        
                                        <?php if( $carousel == true ): ?>
                                        
                                        <div class="portfolio-overlay-wrap">
                                        
                                            <p><?php echo custom_textlimit( get_the_excerpt(), 15 ); ?></p>
                                            
                                            <span><span></span></span>
                                            
                                            <?php echo $porttermnames; ?>
                                        
                                        </div>
                                        
                                        <?php endif; ?>
                                        
                                        <div class="p-overlay-icons clearfix"<?php if( $portfolio_type == 'gallery' ) echo ' data-lightbox="gallery"'; ?>>
                                        
                                            <?php echo $item_link . $portgalleryimages; ?>
                                            <a href="<?php the_permalink(); ?>" class="p-o-link"></a>
                                        
                                        </div>
                                    
                                    </div>
                                
                                </div>
                                
                                <?php if( $column != 5 ) { ?>
                                
                                <div class="portfolio-title">
                                
                                    <h3><a rel="bookmark" title="<?php the_title_attribute(); ?>" href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
                                
                                </div>
                                
                                <?php }
                                
                                if( $carousel == true ) { ?>
                                
                                <div class="portfolio-title">
                                
                                    <h3><a rel="bookmark" title="<?php the_title_attribute(); ?>" href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
                                
                                </div>
                                
                                <?php } ?>
                            
                            </div>
                            
        <?php
    
    }

}


/*--------------------------------------------------------
    Get Portfolio Details
--------------------------------------------------------*/


function portfolio_meta_info() {
    
    $metas = array();
    $metaoutput = '';
    
    $client = ( semi_option( 'portfolio_title_meta_clients' ) != '' ) ? semi_option( 'portfolio_title_meta_clients' ) : __( 'Client', 'coworker' );
    $author = ( semi_option( 'portfolio_title_meta_author' ) != '' ) ? semi_option( 'portfolio_title_meta_author' ) : __( 'Author', 'coworker' );
    $date = ( semi_option( 'portfolio_title_meta_date' ) != '' ) ? semi_option( 'portfolio_title_meta_date' ) : __( 'Date', 'coworker' );
    $skills = ( semi_option( 'portfolio_title_meta_skills' ) != '' ) ? semi_option( 'portfolio_title_meta_skills' ) : __( 'Skills', 'coworker' );
    $categories = ( semi_option( 'portfolio_title_meta_categories' ) != '' ) ? semi_option( 'portfolio_title_meta_categories' ) : __( 'Categories', 'coworker' );
    $url = ( semi_option( 'portfolio_title_meta_url' ) != '' ) ? semi_option( 'portfolio_title_meta_url' ) : __( 'URL', 'coworker' );
    $copyrights = ( semi_option( 'portfolio_title_meta_copyrights' ) != '' ) ? semi_option( 'portfolio_title_meta_copyrights' ) : __( 'Copyrights', 'coworker' );
    
    $getskills = get_post_meta( get_the_ID(), 'semi_port_skills', TRUE );
    
    if( get_post_meta( get_the_ID(), 'semi_port_show_cats', TRUE ) == 'links' ) {
    
        $getterms = get_the_terms( get_the_ID(), 'port-group' );
        
        if ( $getterms ) {
        
            $portterms = array();
            
            $porttermnames = array();
            
            foreach ($getterms as $getterm) {
                $porttermnames[] = '<a href="' . get_term_link( $getterm->slug, 'port-group' ) . '" title="' . sprintf(__('View all post filed under %s', 'coworker'), $getterm->name) . '">' . $getterm->name . '</a>';
            }
            
            $porttermnames = implode( "<br>", $porttermnames );
        
        }
    
    } elseif( get_post_meta( get_the_ID(), 'semi_port_show_cats', TRUE ) == 'nolinks' ) {
    
        $getterms = get_the_terms( get_the_ID(), 'port-group' );
        
        if ( $getterms ) {
        
            $portterms = array();
            
            $porttermnames = array();
            
            foreach ($getterms as $getterm) {
                $porttermnames[] = $getterm->name;
            }
            
            $porttermnames = implode( "<br>", $porttermnames );
        
        }
    
    }
    
    if( get_post_meta( get_the_ID(), 'semi_port_date', TRUE ) ) { $metas[] = array( '<h5>' . $date . '</h5>', '<span>' . get_post_meta( get_the_ID(), 'semi_port_date', TRUE ) . '</span>' ); }
    if( get_post_meta( get_the_ID(), 'semi_port_client', TRUE ) ) { $metas[] = array( '<h5>' . $client . '</h5>', '<span>' . get_post_meta( get_the_ID(), 'semi_port_client', TRUE ) . '</span>' ); }
    if( get_post_meta( get_the_ID(), 'semi_port_show_cats', TRUE ) != 'noshow' ) { $metas[] = array( '<h5>' . $categories . '</h5>', '<span>' . $porttermnames . '</span>' ); }
    if( is_array( $getskills ) AND implode( '', $getskills ) != '' ) {
        $metas[] = array( '<h5>' . $skills . '</h5>', '<span>' . implode( '<br>', get_post_meta( get_the_ID(), 'semi_port_skills', TRUE ) ) . '</span>' );
    }
    if( get_post_meta( get_the_ID(), 'semi_port_author', TRUE ) ) { $metas[] = array( '<h5>' . $author . '</h5>', '<span>' . get_post_meta( get_the_ID(), 'semi_port_author', TRUE ) . '</span>' ); }
    if( checkurl( get_post_meta( get_the_ID(), 'semi_port_url', TRUE ) ) AND get_post_meta( get_the_ID(), 'semi_port_launch_btn', TRUE ) == 0 ) { $metas[] = array( '<h5>' . $url . '</h5>', '<span class="word-wrap"><a href="' . get_post_meta( get_the_ID(), 'semi_port_url', TRUE ) . '" target="_blank">' . get_post_meta( get_the_ID(), 'semi_port_url', TRUE ) . '</a></span>' ); }
    if( get_post_meta( get_the_ID(), 'semi_port_copyrights', TRUE ) ) { $metas[] = array( '<h5>' . $copyrights . '</h5>', '<span>' . get_post_meta( get_the_ID(), 'semi_port_copyrights', TRUE ) . '</span>' ); }
    
    foreach( $metas as $meta ):
    
        $metaoutput .= '<div class="port-terms">' . $meta[0] . $meta[1] . '</div>';
    
    endforeach;
    
    if( get_post_meta( get_the_ID(), 'semi_port_extra_fields', TRUE ) ) { $metaoutput .= do_shortcode( get_post_meta( get_the_ID(), 'semi_port_extra_fields', TRUE ) ); }
    
    if( checkurl( get_post_meta( get_the_ID(), 'semi_port_url', TRUE ) ) AND get_post_meta( get_the_ID(), 'semi_port_launch_btn', TRUE ) == 1 ) {
        $metaoutput .= '<div class="port-terms nobottomborder"><a href="' . get_post_meta( get_the_ID(), 'semi_port_url', TRUE ) . '" target="_blank" class="btn btn-small">' . ( get_post_meta( get_the_ID(), 'semi_port_launch_text', TRUE ) != '' ? get_post_meta( get_the_ID(), 'semi_port_launch_text', TRUE ) : __( 'Launch Project', 'coworker' ) ) . '</a></div>';
    }
    
    echo $metaoutput;
    
}


function get_font_awesome( $blank = false ) {
    
    if( $blank == true ) {
        
        $icons = array( 'none' => '-- Select Icon --',
                        'icon-adjust' => 'Adjust',
                        'icon-asterisk' => 'Asterisk',
                        'icon-ban-circle' => 'Ban Circle',
                        'icon-bar-chart' => 'Bar Chart',
                        'icon-barcode' => 'Barcode',
                        'icon-beaker' => 'Beaker',
                        'icon-beer' => 'Beer',
                        'icon-bell' => 'Bell',
                        'icon-bell-alt' => 'Bell alt',
                        'icon-bolt' => 'Bolt',
                        'icon-book' => 'Book',
                        'icon-bookmark' => 'Bookmark',
                        'icon-bookmark-empty' => 'Bookmark Empty',
                        'icon-briefcase' => 'Briefcase',
                        'icon-bullhorn' => 'Bullhorn',
                        'icon-calendar' => 'Calendar',
                        'icon-camera' => 'Camera',
                        'icon-camera-retro' => 'Camera Retro',
                        'icon-certificate' => 'Certificate',
                        'icon-check' => 'Check',
                        'icon-check-empty' => 'Check Empty',
                        'icon-circle' => 'Circle',
                        'icon-circle-blank' => 'Circle Blank',
                        'icon-cloud' => 'Cloud',
                        'icon-cloud-download' => 'Cloud Download',
                        'icon-cloud-upload' => 'Cloud Upload',
                        'icon-coffee' => 'Coffee',
                        'icon-cog' => 'Cog',
                        'icon-cogs' => 'Cogs',
                        'icon-comment' => 'Comment',
                        'icon-comment-alt' => 'Comment Alt',
                        'icon-comments' => 'Comments',
                        'icon-comments-alt' => 'Comments Alt',
                        'icon-credit-card' => 'Credit Card',
                        'icon-dashboard' => 'Dashboard',
                        'icon-desktop' => 'Desktop',
                        'icon-download' => 'Download',
                        'icon-download-alt' => 'Download Alt',
                        'icon-edit' => 'Edit',
                        'icon-envelope' => 'Envelope',
                        'icon-envelope-alt' => 'Envelope Alt',
                        'icon-exchange' => 'Exchange',
                        'icon-exclamation-sign' => 'Exclamation Sign',
                        'icon-external-link' => 'External Link',
                        'icon-eye-close' => 'Eye Close',
                        'icon-eye-open' => 'Eye Open',
                        'icon-facetime-video' => 'Facetime Video',
                        'icon-fighter-jet' => 'Fighter Jet',
                        'icon-film' => 'Film',
                        'icon-filter' => 'Filter',
                        'icon-fire' => 'Fire',
                        'icon-flag' => 'Flag',
                        'icon-folder-close' => 'Folder Close',
                        'icon-folder-open' => 'Folder Open',
                        'icon-folder-close-alt' => 'Folder Close Alt',
                        'icon-folder-open-alt' => 'Folder Open Alt',
                        'icon-food' => 'Food',
                        'icon-gift' => 'Gift',
                        'icon-glass' => 'Glass',
                        'icon-globe' => 'Globe',
                        'icon-group' => 'Group',
                        'icon-hdd' => 'Hdd',
                        'icon-headphones' => 'Headephones',
                        'icon-heart' => 'Heart',
                        'icon-heart-empty' => 'Heart Empty',
                        'icon-home' => 'Home',
                        'icon-inbox' => 'Inbox',
                        'icon-info-sign' => 'Info Sign',
                        'icon-key' => 'Key',
                        'icon-leaf' => 'Leaf',
                        'icon-laptop' => 'Laptop',
                        'icon-legal' => 'Legal',
                        'icon-lemon' => 'Lemon',
                        'icon-lightbulb' => 'Lightbulb',
                        'icon-lock' => 'Lock',
                        'icon-unlock' => 'Unlock',
                        'icon-magic' => 'Magic',
                        'icon-magnet' => 'Magnet',
                        'icon-map-marker' => 'Map Marker',
                        'icon-minus' => 'Minus',
                        'icon-minus-sign' => 'Minus Sign',
                        'icon-mobile-phone' => 'Mobile Phone',
                        'icon-money' => 'Money',
                        'icon-move' => 'Move',
                        'icon-music' => 'Music',
                        'icon-off' => 'Off',
                        'icon-ok' => 'Ok',
                        'icon-ok-circle' => 'Ok Circle',
                        'icon-ok-sign' => 'Ok Sign',
                        'icon-pencil' => 'Pencil',
                        'icon-picture' => 'Picture',
                        'icon-plane' => 'Plane',
                        'icon-plus' => 'Plus',
                        'icon-plus-sign' => 'Plus Sign',
                        'icon-print' => 'Print',
                        'icon-pushpin' => 'Pushpin',
                        'icon-qrcode' => 'Qrcode',
                        'icon-question-sign' => 'Question Sign',
                        'icon-quote-left' => 'Quote Left',
                        'icon-quote-right' => 'Quote Right',
                        'icon-random' => 'Random',
                        'icon-refresh' => 'Refresh',
                        'icon-remove' => 'Remove',
                        'icon-remove-circle' => 'Remove Circle',
                        'icon-remove-sign' => 'Remove Sign',
                        'icon-reorder' => 'Reorder',
                        'icon-reply' => 'Reply',
                        'icon-resize-horizontal' => 'Resize Horizontal',
                        'icon-resize-vertical' => 'Resize Vertical',
                        'icon-retweet' => 'Retweet',
                        'icon-road' => 'Road',
                        'icon-rss' => 'Rss',
                        'icon-screenshot' => 'Screenshot',
                        'icon-search' => 'Search',
                        'icon-share' => 'Share',
                        'icon-share-alt' => 'Share Alt',
                        'icon-shopping-cart' => 'Shopping Cart',
                        'icon-signal' => 'Signal',
                        'icon-signin' => 'Signin',
                        'icon-signout' => 'Signout',
                        'icon-sitemap' => 'Sitemap',
                        'icon-sort' => 'Sort',
                        'icon-sort-down' => 'Sort Down',
                        'icon-sort-up' => 'Sort Up',
                        'icon-spinner' => 'Spinner',
                        'icon-star' => 'Star',
                        'icon-star-empty' => 'Star Empty',
                        'icon-star-half' => 'Star Half',
                        'icon-tablet' => 'Tablet',
                        'icon-tag' => 'Tag',
                        'icon-tags' => 'Tags',
                        'icon-tasks' => 'Tasks',
                        'icon-thumbs-down' => 'Thumbs Down',
                        'icon-thumbs-up' => 'Thumbs Up',
                        'icon-time' => 'Time',
                        'icon-tint' => 'Tint',
                        'icon-trash' => 'Trash',
                        'icon-trophy' => 'Trophy',
                        'icon-truck' => 'Truck',
                        'icon-umbrella' => 'Umbrella',
                        'icon-upload' => 'Upload',
                        'icon-upload-alt' => 'Upload Alt',
                        'icon-user' => 'User',
                        'icon-user-md' => 'User Md',
                        'icon-volume-off' => 'Volume Off',
                        'icon-volume-down' => 'Volume Down',
                        'icon-volume-up' => 'Volume Up',
                        'icon-warning-sign' => 'Warning Sign',
                        'icon-wrench' => 'Wrench',
                        'icon-zoom-in' => 'Zoom In',
                        'icon-zoom-out' => 'Zoom Out',
                        'icon-file' => 'File',
                        'icon-file-alt' => 'File Alt',
                        'icon-cut' => 'Cut',
                        'icon-copy' => 'Copy',
                        'icon-paste' => 'Paste',
                        'icon-save' => 'Save',
                        'icon-undo' => 'Undo',
                        'icon-repeat' => 'Repeat',
                        'icon-text-height' => 'Text Height',
                        'icon-text-width' => 'Text Width',
                        'icon-align-left' => 'Align Left',
                        'icon-align-center' => 'Align Center',
                        'icon-align-right' => 'Align Right',
                        'icon-align-justify' => 'Align Justify',
                        'icon-indent-left' => 'Indent Left',
                        'icon-indent-right' => 'Indent Right',
                        'icon-font' => 'Font',
                        'icon-bold' => 'Bold',
                        'icon-italic' => 'Italic',
                        'icon-strikethrough' => 'Strikethrough',
                        'icon-underline' => 'Underline',
                        'icon-link' => 'Link',
                        'icon-paper-clip' => 'Paper Clip',
                        'icon-columns' => 'Columns',
                        'icon-table' => 'Table',
                        'icon-th-large' => 'Th Large',
                        'icon-th' => 'Th',
                        'icon-th-list' => 'Th List',
                        'icon-list' => 'List',
                        'icon-list-ol' => 'List Ol',
                        'icon-list-ul' => 'List Ul',
                        'icon-list-alt' => 'List Alt',
                        'icon-angle-left' => 'Angle Left',
                        'icon-angle-right' => 'Angle Right',
                        'icon-angle-up' => 'Angle Up',
                        'icon-angle-down' => 'Angle Down',
                        'icon-arrow-down' => 'Arrow Down',
                        'icon-arrow-left' => 'Arrow Left',
                        'icon-arrow-right' => 'Arrow Right',
                        'icon-arrow-up' => 'Arrow Up',
                        'icon-caret-down' => 'Caret Down',
                        'icon-caret-left' => 'Caret Left',
                        'icon-caret-right' => 'Caret Right',
                        'icon-caret-up' => 'Caret Up',
                        'icon-chevron-down' => 'Chevron Down',
                        'icon-chevron-left' => 'Chevron Left',
                        'icon-chevron-right' => 'Chevron  Right',
                        'icon-chevron-up' => 'Chevron Up',
                        'icon-circle-arrow-down' => 'Circle Arrow Down',
                        'icon-circle-arrow-left' => 'Circle Arrow Left',
                        'icon-circle-arrow-right' => 'Circle Arrow Right',
                        'icon-circle-arrow-up' => 'Circle Arrow Up',
                        'icon-double-angle-left' => 'Double Angle Left',
                        'icon-double-angle-right' => 'Double Angle Right',
                        'icon-double-angle-up' => 'Double Angle Up',
                        'icon-double-angle-down' => 'Double Angle Down',
                        'icon-hand-down' => 'Hand Down',
                        'icon-hand-left' => 'Hand Left',
                        'icon-hand-right' => 'Hand Right',
                        'icon-hand-up' => 'Hand Up',
                        'icon-circle' => 'Circle',
                        'icon-circle-blank' => 'Circle Blank',
                        'icon-play-circle' => 'Play Circle',
                        'icon-play' => 'Play',
                        'icon-pause' => 'Pause',
                        'icon-stop' => 'Stop',
                        'icon-step-backward' => 'Step Backward',
                        'icon-fast-backward' => 'Fast Backward',
                        'icon-backward' => 'Backward',
                        'icon-forward' => 'Forward',
                        'icon-fast-forward' => 'Fast Forward',
                        'icon-step-forward' => 'Step Forward',
                        'icon-eject' => 'Eject',
                        'icon-fullscreen' => 'Fullscreen',
                        'icon-resize-full' => 'Resize Full',
                        'icon-resize-small' => 'Resize Small',
                        'icon-phone' => 'Phone',
                        'icon-phone-sign' => 'Phone Sign',
                        'icon-facebook' => 'Facebook',
                        'icon-facebook-sign' => 'Facebook Sign',
                        'icon-twitter' => 'Twitter',
                        'icon-twitter-sign' => 'Twitter Sign',
                        'icon-github' => 'Github',
                        'icon-github-alt' => 'Github Alt',
                        'icon-github-sign' => 'Github Sign',
                        'icon-linkedin' => 'Linkedin',
                        'icon-linkedin-sign' => 'Linkedin Sign',
                        'icon-pinterest' => 'Pinterest',
                        'icon-pinterest-sign' => 'Pinterest Sign',
                        'icon-google-plus' => 'Google Plus',
                        'icon-google-plus-sign' => 'Google Plus Sign',
                        'icon-sign-blank' => 'Sign Blank',
                        'icon-ambulance' => 'Ambulance',
                        'icon-beaker' => 'Beaker',
                        'icon-h-sign' => 'H Sign',
                        'icon-hospital' => 'Hospital',
                        'icon-medkit' => 'Medkit',
                        'icon-plus-sign-alt' => 'Plus Sign Alt',
                        'icon-stethoscope' => 'Stethoscope',
                        'icon-user-md' => 'User Md' );
        
    } else {
        
        $icons = array( 'icon-adjust' => 'Adjust',
                        'icon-asterisk' => 'Asterisk',
                        'icon-ban-circle' => 'Ban Circle',
                        'icon-bar-chart' => 'Bar Chart',
                        'icon-barcode' => 'Barcode',
                        'icon-beaker' => 'Beaker',
                        'icon-beer' => 'Beer',
                        'icon-bell' => 'Bell',
                        'icon-bell-alt' => 'Bell alt',
                        'icon-bolt' => 'Bolt',
                        'icon-book' => 'Book',
                        'icon-bookmark' => 'Bookmark',
                        'icon-bookmark-empty' => 'Bookmark Empty',
                        'icon-briefcase' => 'Briefcase',
                        'icon-bullhorn' => 'Bullhorn',
                        'icon-calendar' => 'Calendar',
                        'icon-camera' => 'Camera',
                        'icon-camera-retro' => 'Camera Retro',
                        'icon-certificate' => 'Certificate',
                        'icon-check' => 'Check',
                        'icon-check-empty' => 'Check Empty',
                        'icon-circle' => 'Circle',
                        'icon-circle-blank' => 'Circle Blank',
                        'icon-cloud' => 'Cloud',
                        'icon-cloud-download' => 'Cloud Download',
                        'icon-cloud-upload' => 'Cloud Upload',
                        'icon-coffee' => 'Coffee',
                        'icon-cog' => 'Cog',
                        'icon-cogs' => 'Cogs',
                        'icon-comment' => 'Comment',
                        'icon-comment-alt' => 'Comment Alt',
                        'icon-comments' => 'Comments',
                        'icon-comments-alt' => 'Comments Alt',
                        'icon-credit-card' => 'Credit Card',
                        'icon-dashboard' => 'Dashboard',
                        'icon-desktop' => 'Desktop',
                        'icon-download' => 'Download',
                        'icon-download-alt' => 'Download Alt',
                        'icon-edit' => 'Edit',
                        'icon-envelope' => 'Envelope',
                        'icon-envelope-alt' => 'Envelope Alt',
                        'icon-exchange' => 'Exchange',
                        'icon-exclamation-sign' => 'Exclamation Sign',
                        'icon-external-link' => 'External Link',
                        'icon-eye-close' => 'Eye Close',
                        'icon-eye-open' => 'Eye Open',
                        'icon-facetime-video' => 'Facetime Video',
                        'icon-fighter-jet' => 'Fighter Jet',
                        'icon-film' => 'Film',
                        'icon-filter' => 'Filter',
                        'icon-fire' => 'Fire',
                        'icon-flag' => 'Flag',
                        'icon-folder-close' => 'Folder Close',
                        'icon-folder-open' => 'Folder Open',
                        'icon-folder-close-alt' => 'Folder Close Alt',
                        'icon-folder-open-alt' => 'Folder Open Alt',
                        'icon-food' => 'Food',
                        'icon-gift' => 'Gift',
                        'icon-glass' => 'Glass',
                        'icon-globe' => 'Globe',
                        'icon-group' => 'Group',
                        'icon-hdd' => 'Hdd',
                        'icon-headphones' => 'Headephones',
                        'icon-heart' => 'Heart',
                        'icon-heart-empty' => 'Heart Empty',
                        'icon-home' => 'Home',
                        'icon-inbox' => 'Inbox',
                        'icon-info-sign' => 'Info Sign',
                        'icon-key' => 'Key',
                        'icon-leaf' => 'Leaf',
                        'icon-laptop' => 'Laptop',
                        'icon-legal' => 'Legal',
                        'icon-lemon' => 'Lemon',
                        'icon-lightbulb' => 'Lightbulb',
                        'icon-lock' => 'Lock',
                        'icon-unlock' => 'Unlock',
                        'icon-magic' => 'Magic',
                        'icon-magnet' => 'Magnet',
                        'icon-map-marker' => 'Map Marker',
                        'icon-minus' => 'Minus',
                        'icon-minus-sign' => 'Minus Sign',
                        'icon-mobile-phone' => 'Mobile Phone',
                        'icon-money' => 'Money',
                        'icon-move' => 'Move',
                        'icon-music' => 'Music',
                        'icon-off' => 'Off',
                        'icon-ok' => 'Ok',
                        'icon-ok-circle' => 'Ok Circle',
                        'icon-ok-sign' => 'Ok Sign',
                        'icon-pencil' => 'Pencil',
                        'icon-picture' => 'Picture',
                        'icon-plane' => 'Plane',
                        'icon-plus' => 'Plus',
                        'icon-plus-sign' => 'Plus Sign',
                        'icon-print' => 'Print',
                        'icon-pushpin' => 'Pushpin',
                        'icon-qrcode' => 'Qrcode',
                        'icon-question-sign' => 'Question Sign',
                        'icon-quote-left' => 'Quote Left',
                        'icon-quote-right' => 'Quote Right',
                        'icon-random' => 'Random',
                        'icon-refresh' => 'Refresh',
                        'icon-remove' => 'Remove',
                        'icon-remove-circle' => 'Remove Circle',
                        'icon-remove-sign' => 'Remove Sign',
                        'icon-reorder' => 'Reorder',
                        'icon-reply' => 'Reply',
                        'icon-resize-horizontal' => 'Resize Horizontal',
                        'icon-resize-vertical' => 'Resize Vertical',
                        'icon-retweet' => 'Retweet',
                        'icon-road' => 'Road',
                        'icon-rss' => 'Rss',
                        'icon-screenshot' => 'Screenshot',
                        'icon-search' => 'Search',
                        'icon-share' => 'Share',
                        'icon-share-alt' => 'Share Alt',
                        'icon-shopping-cart' => 'Shopping Cart',
                        'icon-signal' => 'Signal',
                        'icon-signin' => 'Signin',
                        'icon-signout' => 'Signout',
                        'icon-sitemap' => 'Sitemap',
                        'icon-sort' => 'Sort',
                        'icon-sort-down' => 'Sort Down',
                        'icon-sort-up' => 'Sort Up',
                        'icon-spinner' => 'Spinner',
                        'icon-star' => 'Star',
                        'icon-star-empty' => 'Star Empty',
                        'icon-star-half' => 'Star Half',
                        'icon-tablet' => 'Tablet',
                        'icon-tag' => 'Tag',
                        'icon-tags' => 'Tags',
                        'icon-tasks' => 'Tasks',
                        'icon-thumbs-down' => 'Thumbs Down',
                        'icon-thumbs-up' => 'Thumbs Up',
                        'icon-time' => 'Time',
                        'icon-tint' => 'Tint',
                        'icon-trash' => 'Trash',
                        'icon-trophy' => 'Trophy',
                        'icon-truck' => 'Truck',
                        'icon-umbrella' => 'Umbrella',
                        'icon-upload' => 'Upload',
                        'icon-upload-alt' => 'Upload Alt',
                        'icon-user' => 'User',
                        'icon-user-md' => 'User Md',
                        'icon-volume-off' => 'Volume Off',
                        'icon-volume-down' => 'Volume Down',
                        'icon-volume-up' => 'Volume Up',
                        'icon-warning-sign' => 'Warning Sign',
                        'icon-wrench' => 'Wrench',
                        'icon-zoom-in' => 'Zoom In',
                        'icon-zoom-out' => 'Zoom Out',
                        'icon-file' => 'File',
                        'icon-file-alt' => 'File Alt',
                        'icon-cut' => 'Cut',
                        'icon-copy' => 'Copy',
                        'icon-paste' => 'Paste',
                        'icon-save' => 'Save',
                        'icon-undo' => 'Undo',
                        'icon-repeat' => 'Repeat',
                        'icon-text-height' => 'Text Height',
                        'icon-text-width' => 'Text Width',
                        'icon-align-left' => 'Align Left',
                        'icon-align-center' => 'Align Center',
                        'icon-align-right' => 'Align Right',
                        'icon-align-justify' => 'Align Justify',
                        'icon-indent-left' => 'Indent Left',
                        'icon-indent-right' => 'Indent Right',
                        'icon-font' => 'Font',
                        'icon-bold' => 'Bold',
                        'icon-italic' => 'Italic',
                        'icon-strikethrough' => 'Strikethrough',
                        'icon-underline' => 'Underline',
                        'icon-link' => 'Link',
                        'icon-paper-clip' => 'Paper Clip',
                        'icon-columns' => 'Columns',
                        'icon-table' => 'Table',
                        'icon-th-large' => 'Th Large',
                        'icon-th' => 'Th',
                        'icon-th-list' => 'Th List',
                        'icon-list' => 'List',
                        'icon-list-ol' => 'List Ol',
                        'icon-list-ul' => 'List Ul',
                        'icon-list-alt' => 'List Alt',
                        'icon-angle-left' => 'Angle Left',
                        'icon-angle-right' => 'Angle Right',
                        'icon-angle-up' => 'Angle Up',
                        'icon-angle-down' => 'Angle Down',
                        'icon-arrow-down' => 'Arrow Down',
                        'icon-arrow-left' => 'Arrow Left',
                        'icon-arrow-right' => 'Arrow Right',
                        'icon-arrow-up' => 'Arrow Up',
                        'icon-caret-down' => 'Caret Down',
                        'icon-caret-left' => 'Caret Left',
                        'icon-caret-right' => 'Caret Right',
                        'icon-caret-up' => 'Caret Up',
                        'icon-chevron-down' => 'Chevron Down',
                        'icon-chevron-left' => 'Chevron Left',
                        'icon-chevron-right' => 'Chevron  Right',
                        'icon-chevron-up' => 'Chevron Up',
                        'icon-circle-arrow-down' => 'Circle Arrow Down',
                        'icon-circle-arrow-left' => 'Circle Arrow Left',
                        'icon-circle-arrow-right' => 'Circle Arrow Right',
                        'icon-circle-arrow-up' => 'Circle Arrow Up',
                        'icon-double-angle-left' => 'Double Angle Left',
                        'icon-double-angle-right' => 'Double Angle Right',
                        'icon-double-angle-up' => 'Double Angle Up',
                        'icon-double-angle-down' => 'Double Angle Down',
                        'icon-hand-down' => 'Hand Down',
                        'icon-hand-left' => 'Hand Left',
                        'icon-hand-right' => 'Hand Right',
                        'icon-hand-up' => 'Hand Up',
                        'icon-circle' => 'Circle',
                        'icon-circle-blank' => 'Circle Blank',
                        'icon-play-circle' => 'Play Circle',
                        'icon-play' => 'Play',
                        'icon-pause' => 'Pause',
                        'icon-stop' => 'Stop',
                        'icon-step-backward' => 'Step Backward',
                        'icon-fast-backward' => 'Fast Backward',
                        'icon-backward' => 'Backward',
                        'icon-forward' => 'Forward',
                        'icon-fast-forward' => 'Fast Forward',
                        'icon-step-forward' => 'Step Forward',
                        'icon-eject' => 'Eject',
                        'icon-fullscreen' => 'Fullscreen',
                        'icon-resize-full' => 'Resize Full',
                        'icon-resize-small' => 'Resize Small',
                        'icon-phone' => 'Phone',
                        'icon-phone-sign' => 'Phone Sign',
                        'icon-facebook' => 'Facebook',
                        'icon-facebook-sign' => 'Facebook Sign',
                        'icon-twitter' => 'Twitter',
                        'icon-twitter-sign' => 'Twitter Sign',
                        'icon-github' => 'Github',
                        'icon-github-alt' => 'Github Alt',
                        'icon-github-sign' => 'Github Sign',
                        'icon-linkedin' => 'Linkedin',
                        'icon-linkedin-sign' => 'Linkedin Sign',
                        'icon-pinterest' => 'Pinterest',
                        'icon-pinterest-sign' => 'Pinterest Sign',
                        'icon-google-plus' => 'Google Plus',
                        'icon-google-plus-sign' => 'Google Plus Sign',
                        'icon-sign-blank' => 'Sign Blank',
                        'icon-ambulance' => 'Ambulance',
                        'icon-beaker' => 'Beaker',
                        'icon-h-sign' => 'H Sign',
                        'icon-hospital' => 'Hospital',
                        'icon-medkit' => 'Medkit',
                        'icon-plus-sign-alt' => 'Plus Sign Alt',
                        'icon-stethoscope' => 'Stethoscope',
                        'icon-user-md' => 'User Md' );
        
    }
                    
    return $icons;
    
}


function get_easing_ops() {
    
    $easing = array('swing' => 'jswing',
        			'easeInQuad' => 'easeInQuad',
        			'easeOutQuad' => 'easeOutQuad',
        			'easeInOutQuad' => 'easeInOutQuad',
        			'easeInCubic' => 'easeInCubic',
        			'easeOutCubic' => 'easeOutCubic',
        			'easeInOutCubic' => 'easeInOutCubic',
        			'easeInQuart' => 'easeInQuart',
        			'easeOutQuart' => 'easeOutQuart',
        			'easeInOutQuart' => 'easeInOutQuart',
        			'easeInQuint' => 'easeInQuint',
        			'easeOutQuint' => 'easeOutQuint',
        			'easeInOutQuint' => 'easeInOutQuint',
        			'easeInSine' => 'easeInSine',
        			'easeOutSine' => 'easeOutSine',
        			'easeInOutSine' => 'easeInOutSine',
        			'easeInExpo' => 'easeInExpo',
        			'easeOutExpo' => 'easeOutExpo',
        			'easeInOutExpo' => 'easeInOutExpo',
        			'easeInCirc' => 'easeInCirc',
        			'easeOutCirc' => 'easeOutCirc',
        			'easeInOutCirc' => 'easeInOutCirc',
        			'easeInElastic' => 'easeInElastic',
        			'easeOutElastic' => 'easeOutElastic',
        			'easeInOutElastic' => 'easeInOutElastic',
        			'easeInBack' => 'easeInBack',
        			'easeOutBack' => 'easeOutBack',
        			'easeInOutBack' => 'easeInOutBack',
        			'easeInBounce' => 'easeInBounce',
        			'easeOutBounce' => 'easeOutBounce',
        			'easeInOutBounce' => 'easeInOutBounce' );
                    
    return $easing;
    
}


/*--------------------------------------------------------
    Breadcrumbs
--------------------------------------------------------*/


function get_breadcrumbs() {
    
    if ( semi_option( 'breadcrumbs' ) == 1 ){
        
        breadcrumbs_function();
    
    }

}


function breadcrumbs_function() {
 
  $delimiter = '<span class="divider">/</span>';
  $home = __('<i class="icon-home"></i>', 'coworker'); // text for the 'Home' link
  $before = '<li class="active">'; // tag before the current crumb
  $after = '</li>'; // tag after the current crumb
 
  if ( !is_front_page() || is_paged() ) {
 
    echo '<ul class="breadcrumb">';
 
    global $post;
    $homeLink = home_url( '/' );
    echo '<li><a href="' . $homeLink . '">' . $home . '</a>' . $delimiter . '</li>';
 
    if( is_home() ) {
        
        echo $before . ( get_option('page_for_posts') ? get_the_title( get_option('page_for_posts') ) : __('Blog', 'coworker') ) . $after;
        
    } elseif ( is_category() ) {
      global $wp_query;
      $cat_obj = $wp_query->get_queried_object();
      $thisCat = $cat_obj->term_id;
      $thisCat = get_category($thisCat);
      $parentCat = get_category($thisCat->parent);
      if ($thisCat->parent != 0) echo(get_category_parents($parentCat, TRUE, $delimiter . '</li>'));
      echo $before . single_cat_title('', false) . $after;
 
    } elseif ( is_tax() ) {
        
      $post_type = get_post_type_object(get_post_type());
      
      if( get_post_type() == 'faqs' ) {
          if( semi_option( 'faqs_page' ) != 0 ) {
              $post_type_blink = get_permalink( semi_option( 'faqs_page' ) );
          } else {
              $post_type_blink = get_post_type_archive_link( get_post_type() );
          }
      } else {
          $post_type_blink = get_post_type_archive_link( get_post_type() );
      }
      
      echo '<li><a href="' . $post_type_blink . '">' . $post_type->labels->singular_name . '</a>' . $delimiter . '</li>';
      echo $before . single_term_title("", false) . $after;
        
    } elseif ( is_day() ) {
      echo '<li><a href="' . get_year_link(get_the_time('Y')) . '">' . get_the_time('Y') . '</a>' . $delimiter . '</li>';
      echo '<li><a href="' . get_month_link(get_the_time('Y'),get_the_time('m')) . '">' . get_the_time('F') . '</a>' . $delimiter . '</li>';
      echo $before . get_the_time('d') . $after;
 
    } elseif ( is_month() ) {
      echo '<li><a href="' . get_year_link(get_the_time('Y')) . '">' . get_the_time('Y') . '</a>' . $delimiter . '</li>';
      echo $before . get_the_time('F') . $after;
 
    } elseif ( is_year() ) {
      echo $before . get_the_time('Y') . $after;
 
    } elseif ( is_single() && !is_attachment() ) {
      if ( get_post_type() != 'post' ) {
        $post_type = get_post_type_object(get_post_type());
        
        if( get_post_type() == 'features' ) {
            if( semi_option( 'features_page' ) != 0 ) {
                $post_type_blink = get_permalink( semi_option( 'features_page' ) );
            } else {
                $post_type_blink = get_post_type_archive_link( get_post_type() );
            }
        } elseif( get_post_type() == 'portfolio' ) {
            if( semi_option( 'portfolio_page' ) != 0 ) {
                $post_type_blink = get_permalink( semi_option( 'portfolio_page' ) );
            } else {
                $post_type_blink = get_post_type_archive_link( get_post_type() );
            }
        } else {
            $post_type_blink = get_post_type_archive_link( get_post_type() );
        }
        
        echo '<li><a href="' . $post_type_blink . '">' . $post_type->labels->singular_name . '</a>' . $delimiter . '</li>';
        echo $before . get_the_title() . $after;
      } else {
        echo ( get_option('page_for_posts') ? '<li><a href="' . get_permalink( get_option('page_for_posts') ) . '">' . get_the_title( get_option('page_for_posts') ) . '</a>' . $delimiter : '</li>' );
        echo $before . get_the_title() . $after;
      }
 
    } elseif ( !is_single() && !is_page() && get_post_type() != 'post' && !is_404() && !is_search() ) {
      $post_type = get_post_type_object(get_post_type());
      echo $before . $post_type->labels->singular_name . $after;
 
    } elseif ( is_attachment() ) {
      $parent = get_post($post->post_parent);
      $cat = get_the_category($parent->ID); $cat = $cat[0];
      if( $cat ) { echo '<li>' . get_category_parents($cat, TRUE, $delimiter . '</li>'); }
      echo '<li><a href="' . get_permalink($parent) . '">' . $parent->post_title . '</a>' . $delimiter . '</li>';
      echo $before . get_the_title() . $after;
 
    } elseif ( is_page() && !$post->post_parent ) {
      echo $before . get_the_title() . $after;
 
    } elseif ( is_page() && $post->post_parent ) {
      $parent_id  = $post->post_parent;
      $breadcrumbs = array();
      while ($parent_id) {
        $page = get_page($parent_id);
        $breadcrumbs[] = '<a href="' . get_permalink($page->ID) . '">' . get_the_title($page->ID) . '</a>';
        $parent_id  = $page->post_parent;
      }
      $breadcrumbs = array_reverse($breadcrumbs);
      foreach ($breadcrumbs as $crumb) echo '<li>' . $crumb . $delimiter . '</li>';
      echo $before . get_the_title() . $after;
 
    } elseif ( is_search() ) {
      echo $before . __('Search: ', 'coworker') . '"' . get_search_query() . '"' . $after;
 
    } elseif ( is_tag() ) {
      echo $before . __('Tagged with: ', 'coworker') . '"' . single_tag_title('', false) . '"' . $after;
 
    } elseif ( is_author() ) {
       global $author;
      $userdata = get_userdata($author);
      echo $before . __('Posts by ', 'coworker') . $userdata->display_name . $after;
 
    } elseif ( is_404() ) {
      echo $before . __('404 Error', 'coworker') . $after;
    }
 
    echo '</ul>';
 
  }
  
}


/*--------------------------------------------------------
    Pagination Function
--------------------------------------------------------*/


function semi_pagination( $pages = '', $range = 4 ) {
     
     $showitems = ($range * 2) + 1;  
 
     global $paged;
     
     if( empty( $paged ) ) $paged = 1;
 
     if( $pages == '' ) {
         
         global $wp_query;
         
         $pages = $wp_query->max_num_pages;
         
         if(!$pages) {
             
             $pages = 1;
             
         }
         
     }   
 
     if(1 != $pages) {
         
         echo "<div class=\"pagination pagination-centered clearfix nobottommargin topmargin\" style=\"font-size: 13px;\"><ul>";
         
         if( $paged > 2 && $paged > $range + 1 && $showitems < $pages ) echo "<li><a href='" . get_pagenum_link( 1 ) . "'>&lArr;</a></li>";
         
         if( $paged > 1 && $showitems < $pages ) echo "<li><a href='" . get_pagenum_link( $paged - 1 ) . "'>&laquo;</a></li>";
 
         for ( $i = 1; $i <= $pages; $i++ ) {
             
             if ( 1 != $pages && ( !( $i >= $paged + $range + 1 || $i <= $paged - $range - 1 ) || $pages <= $showitems )) {
                 
                 echo ( $paged == $i ) ? "<li><span class=\"active\">" . $i . "</span></li>" : "<li><a href='" . get_pagenum_link( $i ) . "'>" . $i . "</a></li>";
                 
             }
             
         }
 
         if ( $paged < $pages && $showitems < $pages ) echo "<li><a href=\"" . get_pagenum_link( $paged + 1 ) . "\">&raquo;</a></li>";
         
         if ( $paged < $pages-1 &&  $paged + $range - 1 < $pages && $showitems < $pages ) echo "<li><a href='" . get_pagenum_link( $pages ) . "'>&rArr;</a></li>";
         
         echo "</ul></div>\n";
         
     }
     
}


/*--------------------------------------------------------
    Get Page Title - Right Area
--------------------------------------------------------*/


function page_title_right() {
    
    if( semi_option( 'pagetitle_right' ) == 'search' ) {
        echo '<div id="top-search"><form role="search" method="get" id="topsearchform" action="' . home_url( '/' ) . '"><input type="text" value="" name="s" id="topsearch-s" placeholder="Type &amp; Hit Enter" /></form></div>';
    } elseif( semi_option( 'pagetitle_right' ) == 'breadcrumb' ){
        breadcrumbs_function();
    }
    
}


/*--------------------------------------------------------
    Twitter Output Function
--------------------------------------------------------*/


function semi_twitter_output( $username, $count = 2, $start = '<li>', $end = '</li>' ) {
    
    $tweets = json_decode( file_get_contents( get_template_directory_uri() . '/include/twitter/tweets.php?username=' . $username . '&count=' . $count ), true );

    $output = '';

    foreach( $tweets as $tweet ){
        
        $feed = $tweet['text'];

        $created_at = $tweet['created_at'];
        
        $feed = preg_replace( "/(http:\/\/)(.*?)\/([\w\.\/\&\=\?\-\,\:\;\#\_\~\%\+]*)/", "<a href=\"\\0\" target=\"_blank\">\\0</a>", $feed );
        
        $feed = preg_replace( "(@([a-zA-Z0-9\_]+))", "<a href=\"http://www.twitter.com/\\1\" target=\"_blank\">\\0</a>", $feed );
        
        $feed = preg_replace( '/(^|\s)#(\w+)/', '\1<a href="http://search.twitter.com/search?q=%23\2" target="_blank">#\2</a>', $feed );
        
        $output .= $start . '<span>' . $feed . '</span><small><a href="http://twitter.com/' . $username . '/statuses/' . $tweet['id_str'] . '" target="_blank">' . human_time_diff( strtotime( $created_at ), current_time('timestamp') ) . ' ago</a></small>' . $end;
    }

    return $output;
    
}


/*--------------------------------------------------------
    Dynamic Image Resizer ( https://github.com/sy4mil/Aqua-Resizer/ )
--------------------------------------------------------*/


function semi_resize( $url, $width, $height = null, $crop = null, $single = true ) {

	//validate inputs
	if(!$url OR !$width ) return false;

	//define upload path & dir
	$upload_info = wp_upload_dir();
	$upload_dir = $upload_info['basedir'];
	$upload_url = $upload_info['baseurl'];

	//check if $img_url is local
	if(strpos( $url, $upload_url ) === false) return false;

	//define path of image
	$rel_path = str_replace( $upload_url, '', $url);
	$img_path = $upload_dir . $rel_path;

	//check if img path exists, and is an image indeed
	if( !file_exists($img_path) OR !getimagesize($img_path) ) return false;

	//get image info
	$info = pathinfo($img_path);
	$ext = $info['extension'];
	list($orig_w,$orig_h) = getimagesize($img_path);

	//get image size after cropping
	$dims = image_resize_dimensions($orig_w, $orig_h, $width, $height, $crop);
	$dst_w = $dims[4];
	$dst_h = $dims[5];

	//use this to check if cropped image already exists, so we can return that instead
	$suffix = "{$dst_w}x{$dst_h}";
	$dst_rel_path = str_replace( '.'.$ext, '', $rel_path);
	$destfilename = "{$upload_dir}{$dst_rel_path}-{$suffix}.{$ext}";

	if(!$dst_h) {
		//can't resize, so return original url
		$img_url = $url;
		$dst_w = $orig_w;
		$dst_h = $orig_h;
	}
	//else check if cache exists
	elseif(file_exists($destfilename) && getimagesize($destfilename)) {
		$img_url = "{$upload_url}{$dst_rel_path}-{$suffix}.{$ext}";
	} 
	//else, we resize the image and return the new resized image url
	else {

		// Note: This pre-3.5 fallback check will edited out in subsequent version
		if(function_exists('wp_get_image_editor')) {

			$editor = wp_get_image_editor($img_path);

			if ( is_wp_error( $editor ) || is_wp_error( $editor->resize( $width, $height, $crop ) ) )
				return false;

			$resized_file = $editor->save();

			if(!is_wp_error($resized_file)) {
				$resized_rel_path = str_replace( $upload_dir, '', $resized_file['path']);
				$img_url = $upload_url . $resized_rel_path;
			} else {
				return false;
			}

		} else {

			$resized_img_path = image_resize( $img_path, $width, $height, $crop ); // Fallback foo
			if(!is_wp_error($resized_img_path)) {
				$resized_rel_path = str_replace( $upload_dir, '', $resized_img_path);
				$img_url = $upload_url . $resized_rel_path;
			} else {
				return false;
			}

		}

	}

	//return the output
	if($single) {
		//str return
		$image = $img_url;
	} else {
		//array return
		$image = array (
			0 => $img_url,
			1 => $dst_w,
			2 => $dst_h
		);
	}

	return $image;

}


?>