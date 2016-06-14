<?php


/*--------------------------------------------------------
    This is a Custom Functions File. Please do not Edit
    anything if you are not sure what you are upto..!!
--------------------------------------------------------*/


define( 'SEMICOLON_FUNCTIONS', get_template_directory() . '/include' );
define( 'SEMICOLON_ADMIN', get_template_directory() . '/admin' );
define( 'SEMICOLON_JS', get_template_directory_uri() . '/js' );
define( 'SEMICOLON_CSS', get_template_directory_uri() . '/css' );
define( 'SEMICOLON_IMAGES', get_template_directory_uri() . '/images' );
$theme_version = wp_get_theme();
define( 'SEMICOLON_VERSION', $theme_version->get( 'Version' ) );


/*--------------------------------------------------------
    Theme Setup after Activation
--------------------------------------------------------*/


if ( ! isset( $content_width ) )
	$content_width = 960;


add_action( 'after_setup_theme', 'semicolon_setup' );

if ( ! function_exists( 'semicolon_setup' ) ):

function semicolon_setup() {

    load_theme_textdomain( 'coworker', get_template_directory() . '/languages' );

    register_nav_menu( 'top', __( 'Top Menu', 'coworker' ) );

    register_nav_menu( 'primary', __( 'Primary Menu', 'coworker' ) );

    register_nav_menu( 'sticky', __( 'Sticky Menu', 'coworker' ) );

    add_theme_support( 'post-formats', array( 'image', 'gallery', 'video', 'audio' ) );

    add_theme_support( 'post-thumbnails' );

    add_theme_support( 'woocommerce' );

    if ( function_exists( 'add_image_size' ) ) {

        add_image_size( 'small', 150, 90, true );

        add_image_size( 'medium', 300, 200, true );

        add_image_size( 'large', 720, '', true );

        add_image_size( 'full-width', 960, '', true );

    }

    add_theme_support( 'automatic-feed-links' );

    require_once( SEMICOLON_FUNCTIONS . '/sidebars.php' ); // Theme Sidebars

    require_once( SEMICOLON_FUNCTIONS . '/widgets/twitter.php' ); // Twitter Widget

    require_once( SEMICOLON_FUNCTIONS . '/widgets/flickr.php' ); // Flickr Widget

    require_once( SEMICOLON_FUNCTIONS . '/widgets/dribbble.php' ); // Dribbble Widget

    require_once( SEMICOLON_FUNCTIONS . '/widgets/instagram.php' ); // Instagram Widget

    require_once( SEMICOLON_FUNCTIONS . '/widgets/posts.php' ); // Posts Widget

    require_once( SEMICOLON_FUNCTIONS . '/widgets/subnav.php' ); // Sub Navigation Widget

    require_once( SEMICOLON_FUNCTIONS . '/widgets/video.php' ); // Video Widget

    require_once( SEMICOLON_FUNCTIONS . '/widgets/portfolio.php' ); // Portfolio Widget

    require_once( SEMICOLON_FUNCTIONS . '/widgets/testimonials.php' ); // Testimonials Widget

    require_once( SEMICOLON_FUNCTIONS . '/widgets/tabbed.php' ); // Tabbed Posts Widget

    require_once( SEMICOLON_FUNCTIONS . '/widgets/quickcontact.php' ); // Footer Quick Contact Form Widget

    require_once( SEMICOLON_FUNCTIONS . '/sidebar_generator.php' ); // Sidebar Generator

    require_once( SEMICOLON_FUNCTIONS . '/shortcodes.php' ); // Shortcodes

    require_once( SEMICOLON_FUNCTIONS . '/shortcodes/shortcodes.php' ); // Shortcodes

}

endif;


require_once ( SEMICOLON_ADMIN . '/index.php'); // Theme Options

include( SEMICOLON_ADMIN .  '/theme-functions.php' ); // Theme Fucntions

remove_action( 'wp_head', 'wp_generator' );


if ( ! function_exists( 'semicolon_wp_title' ) ) {
    function semicolon_wp_title( $title, $sep ) {
    	global $paged, $page;

    	if ( is_feed() )
    		return $title;

    	// Add the site name.
    	$title .= get_bloginfo( 'name' );

        // Add the site description for the home/front page.
    	$site_description = get_bloginfo( 'description', 'display' );
    	if ( $site_description && ( is_front_page() ) )
    		$title = "$title $sep $site_description";

    	// Add a page number if necessary.
    	if ( $paged >= 2 || $page >= 2 ) {
    	   $ntitle = str_replace( ' | ' . get_bloginfo( 'name' ), '', $title );
           $title = "$ntitle - " . sprintf( __( 'Page %s', 'coworker' ), max( $paged, $page ) ) . " $sep " . get_bloginfo( 'name' );
    	}

    	return $title;
    }
}

add_filter( 'wp_title', 'semicolon_wp_title', 10, 2 );


/*--------------------------------------------------------
    External Plugins Setup
--------------------------------------------------------*/


include( SEMICOLON_FUNCTIONS . '/plugins/register_plugins.php' ); // Required Plugins

add_action('layerslider_ready', 'my_layerslider_overrides');

function my_layerslider_overrides() {
    // Disable auto-updates
    $GLOBALS['lsAutoUpdateBox'] = false;
}


/*--------------------------------------------------------
    WooCommerce Declarations
--------------------------------------------------------*/


if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
    define('SM_WOOCOMMERCE_ACTIVE', true);
} else {
    define('SM_WOOCOMMERCE_ACTIVE', false);
}

function sm_woocommerce_image_dimensions() {
    global $pagenow;

    if ( ! isset( $_GET['activated'] ) || $pagenow != 'themes.php' ) {
        return;
    }

    $semi_woo_catalog = array(
        'width' => '225', // px
        'height' => '250', // px
        'crop' => 1 // true
    );

    $semi_woo_single = array(
        'width' => '500', // px
        'height' => '500', // px
        'crop' => 1 // true
    );

    $semi_woo_thumbnail = array(
        'width' => '200', // px
        'height' => '225', // px
        'crop' => 1 // false
    );

    // Image sizes
    update_option( 'shop_catalog_image_size', $semi_woo_catalog ); // Product category thumbs
    update_option( 'shop_single_image_size', $semi_woo_single ); // Single product image
    update_option( 'shop_thumbnail_image_size', $semi_woo_thumbnail ); // Image gallery thumbs
}

add_action( 'after_switch_theme', 'sm_woocommerce_image_dimensions', 1 );

if( SM_WOOCOMMERCE_ACTIVE ):

    if ( version_compare( WOOCOMMERCE_VERSION, "2.1" ) >= 0 ) {
        add_filter( 'woocommerce_enqueue_styles', '__return_false' );
    } else {
        define( 'WOOCOMMERCE_USE_CSS', false );
    }

    add_filter( 'loop_shop_per_page', create_function( '$cols', 'return ' . semi_option('shop_items') . ';' ), 20 );


    add_filter('woocommerce_page_title','sm_woo_title', 15);

    function sm_woo_title( $page_title ){
        if( is_post_type_archive('product') ) return false;
    }

    add_filter('woocommerce_show_page_title', 'sm_override_woo_page_title');

    function sm_override_woo_page_title() {
        return false;
    }


    // Ensure cart contents update when products are added to the cart via AJAX (place the following in functions.php)
    add_filter('add_to_cart_fragments', 'woocommerce_header_add_to_cart_fragment');

    function woocommerce_header_add_to_cart_fragment( $fragments ) {
    global $woocommerce;
    ob_start();
    ?>

    <div id="fshopping-cart" class="clearfix">

            <?php if ( sizeof( $woocommerce->cart->cart_contents ) > 0 ): ?>

            <div id="fshop-cart-trigger">
                <i class="icon-shopping-cart"></i>
                <div id="fshop-cart-qty"><?php echo $woocommerce->cart->cart_contents_count; ?></div>
            </div>

            <h3><?php printf( _n('%d item in Shopping Cart', '%d items in Shopping Cart', $woocommerce->cart->cart_contents_count, 'coworker'), $woocommerce->cart->cart_contents_count); ?></h3>

            <div id="mini-cart-items">

                <?php foreach( $woocommerce->cart->cart_contents as $cart_item_key => $cart_item ) {

                $product_item = $cart_item['data'];
    	        $product_title = $product_item->get_title();

                ?>

    	        <?php if ( $product_item->exists() && $cart_item['quantity'] > 0 ) { ?>

                <div class="mini-cart-item clearfix">
                    <div class="mini-cart-item-image">
                        <a href="<?php echo get_permalink( $cart_item['product_id'] ); ?>"><?php echo $product_item->get_image(); ?></a>
                    </div>
                    <div class="mini-cart-item-desc">
                        <a href="<?php echo get_permalink( $cart_item['product_id'] ); ?>"><?php echo apply_filters('woocommerce_cart_widget_product_title', $product_title, $product_item); ?></a>
                        <span class="mini-cart-item-price"><?php echo woocommerce_price( $product_item->get_price() ); ?></span>
                        <span class="mini-cart-item-quantity">x <?php echo $cart_item['quantity']; ?></span>
                    </div>
                </div>

                <?php } } ?>

            </div>

            <div class="mini-checkout-wrap">
                <span class="fleft mini-checkout-price"><?php echo $woocommerce->cart->get_cart_total(); ?></span>
                <a href="<?php echo $woocommerce->cart->get_cart_url(); ?>" title="<?php _e('View your Shopping Cart', 'coworker'); ?>" class="fright nomargin simple-button"><?php _e('View Cart', 'coworker'); ?></a>
            </div>

            <?php else: ?>

            <h3><?php _e( 'Shopping Cart Empty.!', 'coworker' ); ?></h3>

            <div id="mini-cart-items">

                <?php _e( 'Sorry, your Shopping Cart seems to be Empty.!', 'coworker' ); ?>

            </div>

            <div class="mini-checkout-wrap">
                <a href="<?php echo get_permalink( woocommerce_get_page_id( 'shop' ) ); ?>" title="<?php _e('Start Shopping', 'coworker'); ?>" class="divcenter nomargin simple-button"><?php _e('Start Shopping', 'coworker'); ?></a>
            </div>

            <?php endif; ?>

        </div>

        <script type="text/javascript">fshopCartTrigger();</script>

    <?php
    $fragments['#fshopping-cart'] = ob_get_clean();
    return $fragments;
    }


    remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_related_products', 20 );

    add_action( 'woocommerce_after_single_product', 'woocommerce_output_related_products', 10 );

    remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_sharing', 50 );

    add_action( 'woocommerce_after_single_product_summary', 'woocommerce_template_single_sharing', 20 );

    add_action('woocommerce_share','semi_wooshare');

    function semi_wooshare() {

        $pinterestthumb = wp_get_attachment_image_src( get_post_thumbnail_id( get_the_ID() ), 'large');

    ?>

    <div class="entry_share clearfix">

        <span><strong><?php _e( 'Share this:', 'coworker' ); ?></strong></span>

        <a href="http://www.facebook.com/sharer.php?u=<?php the_permalink(); ?>&amp;t=<?php the_title(); ?>" target="_blank" class="ntip" title="<?php _e( 'Share on Facebook', 'coworker' ); ?>"><img src="<?php echo get_stylesheet_directory_uri() ?>/images/icons/social/post/facebook.png" alt="Facebook" /></a>
        <a href="https://twitter.com/intent/tweet?source=coworkertheme&amp;text=<?php the_title(); ?>&amp;url=<?php the_permalink(); ?>" target="_blank" class="ntip" title="<?php _e( 'Tweet on Twitter', 'coworker' ); ?>"><img src="<?php echo get_stylesheet_directory_uri() ?>/images/icons/social/post/twitter.png" alt="Twitter" /></a>
        <a href="http://pinterest.com/pin/create/button/?url=<?php the_permalink();?>&amp;media=<?php echo $pinterestthumb[0]; ?>&amp;description=<?php echo get_the_excerpt(); ?>" target="_blank" class="ntip" title="<?php _e( 'Pin it on Pinterest', 'coworker' ); ?>"><img src="<?php echo get_stylesheet_directory_uri() ?>/images/icons/social/post/pinterest.png" alt="Pinterest" /></a>
        <a href="https://plus.google.com/share?url=<?php the_permalink();?>" target="_blank" class="ntip" title="<?php _e( 'Share on Google Plus', 'coworker' ); ?>"><img src="<?php echo get_stylesheet_directory_uri() ?>/images/icons/social/post/googleplus.png" alt="Google Plus" /></a>
        <a href="http://www.stumbleupon.com/submit?url=<?php the_permalink();?>&amp;title=<?php the_title();?>" target="_blank" class="ntip" title="<?php _e( 'Share on StumbleUpon', 'coworker' ); ?>"><img src="<?php echo get_stylesheet_directory_uri() ?>/images/icons/social/post/stumbleupon.png" alt="StumbleUpon" /></a>
        <a href="http://reddit.com/submit?url=<?php the_permalink();?>&amp;title=<?php the_title();?>" target="_blank" class="ntip" title="<?php _e( 'Share on Reddit', 'coworker' ); ?>"><img src="<?php echo get_stylesheet_directory_uri() ?>/images/icons/social/post/reddit.png" alt="Reddit" /></a>
        <a href="<?php echo get_post_comments_feed_link( get_the_ID(), 'rss2' ); ?>" target="_blank" class="ntip" title="<?php _e( 'RSS Feed', 'coworker' ); ?>"><img src="<?php echo get_stylesheet_directory_uri() ?>/images/icons/social/post/rss.png" alt="RSS" /></a>
        <a href="mailto:?subject=<?php the_title();?>&amp;body=<?php echo get_the_excerpt(); ?> <?php the_permalink();?>" target="_blank" class="ntip" title="<?php _e( 'Email this Post', 'coworker' ); ?>"><img src="<?php echo get_stylesheet_directory_uri() ?>/images/icons/social/post/email.png" alt="Email" /></a>

    </div>

    <?php }


    add_filter( 'wc_add_to_cart_message', 'custom_add_to_cart_message' );

    function custom_add_to_cart_message() {

        global $woocommerce;

        if ( get_option('woocommerce_cart_redirect_after_add') == 'yes' ) :

            $return_to = get_permalink(woocommerce_get_page_id('shop'));

            $message = sprintf('%s <a href="%s" class="fright">%s</a>', __('Product successfully added to your cart.', 'woocommerce'), $return_to, __('Continue Shopping &rarr;', 'woocommerce') );

        else :

            $message = sprintf('%s <a href="%s" class="fright">%s</a>', __('Product successfully added to your cart.', 'woocommerce'), get_permalink(woocommerce_get_page_id('cart')), __('View Cart &rarr;', 'woocommerce') );

        endif;

        return $message;

    }

    function semi_woo_postcontent_wrapper_start() {

        $sidebar_layout = semi_option('shop_archive_sidebar');
        $columns_layout = semi_option('shop_archive_layout');

        if( $columns_layout == '4' OR $columns_layout == '3' ) {
            $sidebar_layout = 'nosidebar';
        }

        if( $sidebar_layout != 'nosidebar' ):

        echo '<div class="postcontent nobottommargin' . ( $sidebar_layout == 'left' ) ? ' col_last' : '' . '">';

        endif;

    }

    function semi_woo_postcontent_wrapper_end() {

        $sidebar_layout = semi_option('shop_archive_sidebar');
        $columns_layout = semi_option('shop_archive_layout');

        if( $columns_layout == '4' OR $columns_layout == '3' ) {
            $sidebar_layout = 'nosidebar';
        }

        if( $sidebar_layout != 'nosidebar' ):

        echo '</div>';

        endif;

    }

endif;


/*--------------------------------------------------------
    Excerpts Setup
--------------------------------------------------------*/


if ( ! function_exists( 'semicolon_excerpt_length' ) ) {
    function semicolon_excerpt_length( $length ) {

        if( is_numeric( semi_option( 'blog_excerpt' ) ) ) {
            return semi_option( 'blog_excerpt' );
        } else { return 70; }

    }
}

add_filter( 'excerpt_length', 'semicolon_excerpt_length' );


if ( ! function_exists( 'semicolon_continue_reading_link' ) ) {
    function semicolon_continue_reading_link() {
    	return ' <a href="'. esc_url( get_permalink() ) . '">' . __( 'Continue reading &rarr;', 'semicolon' ) . '</a>';
    }
}


if ( ! function_exists( 'semicolon_auto_excerpt_more' ) ) {
    function semicolon_auto_excerpt_more( $more ) {
    	return ' &hellip;' . semicolon_continue_reading_link();
    }
}


if ( ! function_exists( 'semicolon_custom_excerpt_more' ) ) {
    function semicolon_custom_excerpt_more( $output ) {
    	if ( has_excerpt() && ! is_attachment() ) {
    		$output .= '';
    	}
    	return $output;
    }
}

add_filter( 'get_the_excerpt', 'semicolon_custom_excerpt_more' );


/*--------------------------------------------------------
    User Contact Fields
--------------------------------------------------------*/


if ( ! function_exists( 'semicolon_hide_profile_fields' ) ) {
    function semicolon_hide_profile_fields( $contactmethods ) {
        unset($contactmethods['aim']);
        unset($contactmethods['jabber']);
        unset($contactmethods['yim']);
        return $contactmethods;
    }
}

add_filter('user_contactmethods','semicolon_hide_profile_fields',10,1);


if ( ! function_exists( 'semicolon_new_contactmethods' ) ) {
    function semicolon_new_contactmethods( $contactmethods ) {
        $contactmethods['twitter'] = 'Twitter';
        $contactmethods['facebook'] = 'Facebook';
        $contactmethods['dribbble'] = 'Dribbble';
        return $contactmethods;
    }
}

add_filter('user_contactmethods','semicolon_new_contactmethods',10,1);


/*--------------------------------------------------------
    Login Page Logo
--------------------------------------------------------*/


if ( ! function_exists( 'semicolon_custom_login_logo' ) ) {
    function semicolon_custom_login_logo() {

        if( semi_option( 'loginlogo' ) != '' ):

            $width = is_numeric( semi_option( 'loginlogo_width' ) ) ? semi_option( 'loginlogo_width' ) : '202';

            $height = is_numeric( semi_option( 'loginlogo_height' ) ) ? semi_option( 'loginlogo_height' ) : '120';

            echo '<style type="text/css">
                #login h1 a { background-image:url("'. semi_option( 'loginlogo' ) .'") !important; background-size: ' . $width . 'px ' . $height . 'px !important; width: auto !important; height: ' . $height . 'px !important; }
            </style>';

        endif;

    }
}

add_filter('login_head', 'semicolon_custom_login_logo');


/*--------------------------------------------------------
    Favicons
--------------------------------------------------------*/


if ( ! function_exists( 'semicolon_headericons' ) ) {
    function semicolon_headericons() {

        if( semi_option( 'favicon' ) != '' ) { echo '<link rel="shortcut icon" href="'. semi_option( 'favicon' ) . '" />' . "\n"; }

        if( semi_option( 'iphoneicon' ) != '' ) { echo '<link rel="apple-touch-icon" href="'. semi_option( 'iphoneicon' ) . '" />' . "\n"; }

        if( semi_option( 'iphoneretinaicon' ) != '' ) { echo '<link rel="apple-touch-icon" sizes="114x114" href="'. semi_option( 'iphoneretinaicon' ) . '" />' . "\n"; }

        if( semi_option( 'ipadicon' ) != '' ) { echo '<link rel="apple-touch-icon" sizes="72x72" href="'. semi_option( 'ipadicon' ) . '" />' . "\n"; }

        if( semi_option( 'ipadretinaicon' ) != '' ) { echo '<link rel="apple-touch-icon" sizes="144x144" href="'. semi_option( 'ipadretinaicon' ) . '" />' . "\n"; }

    }
}

add_action('wp_head', 'semicolon_headericons');


/*--------------------------------------------------------
    Password Protected Form
--------------------------------------------------------*/


if ( ! function_exists( 'custom_password_form' ) ) {
    function custom_password_form() {
    	global $post;
    	$label = 'pwbox-'.( empty( $post->ID ) ? rand() : $post->ID );
    	$o = '<form action="' . esc_url( site_url( 'wp-login.php?action=postpass', 'login_post' ) ) . '" method="post">
    	' . __( "<p>To view the Content, Please enter the Password below:</p>", 'coworker' ) . '
    	<label class="pass-label" style="display: none;" for="' . $label . '">' . __( "Password: ", 'coworker' ) . ' </label><input name="post_password" id="' . $label . '" type="password" style="display: inline-block; width: 250px; text-align: center; margin-right: 20px;" size="20" /><button style="display: inline-block;" type="submit" name="Submit" class="button" value="' . __( "Submit", 'coworker' ) . '"><span>' . __( 'Get Access', 'coworker' ) . '</span></button>
    	</form>
    	';
    	return $o;
    }
}

add_filter( 'the_password_form', 'custom_password_form' );


/*--------------------------------------------------------
    Check Shortcode in Content
--------------------------------------------------------*/


if ( ! function_exists( 'content_has_shortcode' ) ) {
    function content_has_shortcode( $shortcode = '' ) {

        $post_to_check = get_post( get_the_ID() );

        $found = false;

        if ( !$shortcode ) {
        	return $found;
        }

        if ( isset( $post_to_check ) AND stripos($post_to_check->post_content, '[' . $shortcode) !== false ) {
        	$found = true;
        }

        return $found;

    }
}

add_filter( 'no_texturize_shortcodes', 'sm_shortcodes_to_exempt_from_wptexturize' );
function sm_shortcodes_to_exempt_from_wptexturize($shortcodes){
    $shortcodes[] = 'tabs';
    return $shortcodes;
}


/*--------------------------------------------------------
    Theme CSS Queueing
--------------------------------------------------------*/


function semicolon_css_queueing() {
	if (!is_admin()) {

       $protocol = is_ssl() ? 'https' : 'http';

        if( semi_option( 'bodyfont' ) != 'none' ) {
            wp_enqueue_style( 'semi-body-font', "$protocol://fonts.googleapis.com/css?family=" . urlencode( semi_option( 'bodyfont' ) ) . ":400,400italic,700,700italic", '', SEMICOLON_VERSION );
        }

        if( semi_option( 'primaryfont' ) != 'none' AND semi_option( 'primaryfont' ) != 'Open Sans' ) {
            wp_enqueue_style( 'semi-primary-font', "$protocol://fonts.googleapis.com/css?family=" . urlencode( semi_option( 'primaryfont' ) ) . ":400,400italic,700,700italic", '', SEMICOLON_VERSION );
        } else {
            wp_enqueue_style( 'semi-primary-font', "$protocol://fonts.googleapis.com/css?family=Open+Sans:400,300,600,700", '', SEMICOLON_VERSION );
        }

        if( semi_option( 'secondaryfont' ) != 'none' AND semi_option( 'secondaryfont' ) != 'Droid Serif' ) {
            wp_enqueue_style( 'semi-secondary-font', "$protocol://fonts.googleapis.com/css?family=" . urlencode( semi_option( 'secondaryfont' ) ) . ":400,400italic,700,700italic", '', SEMICOLON_VERSION );
        } else {
            wp_enqueue_style( 'semi-secondary-font', "$protocol://fonts.googleapis.com/css?family=Droid+Serif:400,400italic", '', SEMICOLON_VERSION );
        }

        wp_enqueue_style( 'coworker-stylesheet', get_stylesheet_uri(), '', SEMICOLON_VERSION );

       if( semi_option('responsive') != 1 ):

        wp_register_style('retinaCss', get_template_directory_uri() . '/css/retina.css', '', SEMICOLON_VERSION);
        wp_enqueue_style('retinaCss');

       endif;

        wp_register_style('tipsy', get_template_directory_uri() . '/css/tipsy.css', '', SEMICOLON_VERSION);
        wp_enqueue_style('tipsy');

        wp_register_style('bootstrap', get_template_directory_uri() . '/css/bootstrap.css', '', SEMICOLON_VERSION);
        wp_enqueue_style('bootstrap');

        wp_register_style('fontAwesome', get_template_directory_uri() . '/css/font-awesome.css', '', SEMICOLON_VERSION);
        wp_enqueue_style('fontAwesome');

        wp_register_style('magnificPopup', get_template_directory_uri() . '/css/magnific-popup.css', '', SEMICOLON_VERSION);
        wp_enqueue_style('magnificPopup');

    }
}

add_action('wp_enqueue_scripts', 'semicolon_css_queueing');


/*--------------------------------------------------------
    Conditional Theme CSS Queueing
--------------------------------------------------------*/


function load_pagespecific_styles() {

    if (!is_admin()) {

        global $post;

        if( is_object( $post ) ):

            $slider = get_post_meta( $post->ID, 'semi_page_slider', true );

            if ( ( is_page_template('template-slider.php') OR is_page_template('template-slider-sidebar.php') ) AND $slider == 'refine' ){

                wp_register_style('refineSliderCSS', get_template_directory_uri() . '/css/refineslide.css', '', SEMICOLON_VERSION);
        		wp_enqueue_style('refineSliderCSS');

            }

            if ( ( is_page_template('template-slider.php') OR is_page_template('template-slider-sidebar.php') ) AND $slider == 'nivo' ){

                wp_register_style('nivoSliderCSS', get_template_directory_uri() . '/css/nivo-slider.css', '', SEMICOLON_VERSION);
        		wp_enqueue_style('nivoSliderCSS');

            }

            if ( ( is_page_template('template-slider.php') OR is_page_template('template-slider-sidebar.php') ) AND $slider == 'accordion' ){

                wp_register_style('kwicksSliderCSS', get_template_directory_uri() . '/css/kwicks.css', '', SEMICOLON_VERSION);
        		wp_enqueue_style('kwicksSliderCSS');

            }

            if ( ( is_page_template('template-slider.php') OR is_page_template('template-slider-sidebar.php') ) AND $slider == 'elastic' ){

                wp_register_style('elasticSliderCSS', get_template_directory_uri() . '/css/elastic.css', '', SEMICOLON_VERSION);
        		wp_enqueue_style('elasticSliderCSS');

            }

            if ( ( is_page_template('template-slider.php') OR is_page_template('template-slider-sidebar.php') ) AND $slider == 'camera' ){

                wp_register_style('cameraSliderCSS', get_template_directory_uri() . '/css/camera.css', '', SEMICOLON_VERSION);
        		wp_enqueue_style('cameraSliderCSS');

            }

        endif;

        if( semi_option('nonresponsive') != 1 ){

            wp_register_style('responsiveCss', get_template_directory_uri() . '/css/responsive.css', '', SEMICOLON_VERSION);
            wp_enqueue_style('responsiveCss');

        }

        if( SM_WOOCOMMERCE_ACTIVE ):

            wp_register_style('coworkerWoocommerceCss', get_template_directory_uri() . '/css/coworker-woocommerce.css', '', SEMICOLON_VERSION);
            wp_enqueue_style('coworkerWoocommerceCss');

        endif;

    }

}
add_action('wp_enqueue_scripts', 'load_pagespecific_styles');


/*--------------------------------------------------------
    Theme Javascripts Queueing
--------------------------------------------------------*/


function semicolon_js_queueing() {
	if ( !is_admin() ) {

		wp_enqueue_script('jquery');

		wp_register_script('jqueryPlugins', get_template_directory_uri() . '/js/plugins.js', 'jquery', SEMICOLON_VERSION);
        wp_enqueue_script('jqueryPlugins');

        wp_register_script('semicolon_custom', get_template_directory_uri() . '/js/custom.js', 'jquery', SEMICOLON_VERSION, TRUE);
		wp_enqueue_script('semicolon_custom');

	}
}
add_action('wp_enqueue_scripts', 'semicolon_js_queueing');


/*--------------------------------------------------------
    Conditional Theme Javascripts Queueing
--------------------------------------------------------*/


function load_pagespecific_scripts() {

    if (!is_admin()) {

        global $post;

        if( is_singular() ) {

            wp_enqueue_script( 'comment-reply' );

        }

        wp_deregister_script('hoverIntent');

        if( is_object( $post ) ):

            $slider = get_post_meta( $post->ID, 'semi_page_slider', true );

            if ( ( is_page_template('template-slider.php') OR is_page_template('template-slider-sidebar.php') ) AND $slider == 'refine' ){

                wp_register_script('refineSlider', get_template_directory_uri() . '/js/jquery.refine.js', 'jquery', SEMICOLON_VERSION);
        		wp_enqueue_script('refineSlider');

            }

            if ( ( is_page_template('template-slider.php') OR is_page_template('template-slider-sidebar.php') ) AND $slider == 'nivo' ){

                wp_register_script('nivoSlider', get_template_directory_uri() . '/js/jquery.nivo.js', 'jquery', SEMICOLON_VERSION);
        		wp_enqueue_script('nivoSlider');

            }

            if ( ( is_page_template('template-slider.php') OR is_page_template('template-slider-sidebar.php') ) AND $slider == 'accordion' ){

                wp_register_script('kwicksSlider', get_template_directory_uri() . '/js/jquery.kwicks.js', 'jquery', SEMICOLON_VERSION);
        		wp_enqueue_script('kwicksSlider');

            }

            if ( ( is_page_template('template-slider.php') OR is_page_template('template-slider-sidebar.php') ) AND $slider == 'elastic' ){

                wp_register_script('elasticSlider', get_template_directory_uri() . '/js/jquery.elastic.js', 'jquery', SEMICOLON_VERSION);
        		wp_enqueue_script('elasticSlider');

            }

            if ( ( is_page_template('template-slider.php') OR is_page_template('template-slider-sidebar.php') ) AND $slider == 'camera' ){

                wp_register_script('cameraSlider', get_template_directory_uri() . '/js/jquery.camera.js', 'jquery', SEMICOLON_VERSION);
        		wp_enqueue_script('cameraSlider');

            }

            if ( ( is_page_template('template-slider.php') OR is_page_template('template-slider-sidebar.php') ) AND $slider == '3d' ){

                wp_register_script('slider3djs', get_template_directory_uri() . '/include/piecemaker/swfobject.js', '', SEMICOLON_VERSION);
        		wp_enqueue_script('slider3djs');

            }

            if ( is_page_template('template-contact.php') OR is_page_template('template-contact-half-left.php') OR is_page_template('template-contact-half-right.php') OR is_page_template('template-contact-sidebar.php') OR is_page_template('template-contact-split.php') OR content_has_shortcode( 'gmap' ) ){

                wp_register_script('gmapsAPI', '//maps.google.com/maps/api/js?sensor=false', '', SEMICOLON_VERSION);
                wp_enqueue_script('gmapsAPI');

                wp_register_script('gmapsScript', get_template_directory_uri() . '/js/jquery.gmap.js', 'jquery', SEMICOLON_VERSION);
                wp_enqueue_script('gmapsScript');

            }

            if ( is_page_template('template-comingsoon.php') ){

                wp_register_script('countDown', get_template_directory_uri() . '/js/jquery.countdown.js', '', SEMICOLON_VERSION);
                wp_enqueue_script('countDown');

            }

        endif;

    }

}
add_action('wp_enqueue_scripts', 'load_pagespecific_scripts');


/*--------------------------------------------------------
    Add Body Class
--------------------------------------------------------*/


add_filter('body_class','semi_body_class_names');

function semi_body_class_names($classes) {

    global $post;

    if( is_object( $post ) ):

        $slider = get_post_meta( $post->ID, 'semi_page_slider', true );

        if ( ( is_page_template('template-slider.php') OR is_page_template('template-slider-sidebar.php') ) AND $slider == '3d' ){ $classes[] = 'body-slider-3d'; }

    endif;

	return $classes;
}


/*--------------------------------------------------------
    WP Head Styles
--------------------------------------------------------*/


function semicolon_head_option_styles() { ?>

<!-- Custom CSS Styles
============================================= -->
<style type="text/css">

<?php include( SEMICOLON_FUNCTIONS . '/headercss/bgimage.php' ); ?>

<?php include( SEMICOLON_FUNCTIONS . '/headercss/fontsizes.php' ); ?>

<?php if( semi_option( 'layout' ) != 'full' ): ?>#wrapper { margin: <?php echo ( is_numeric( semi_option( 'boxedmargin' ) ) ? semi_option( 'boxedmargin' ) . 'px' : '50px' ); ?> auto; }
#fshopping-cart-wrap { top: <?php echo ( is_numeric( semi_option( 'boxedmargin' ) ) ? semi_option( 'boxedmargin' ) . 'px' : '50px' ); ?>; }
<?php endif; ?>

<?php include( SEMICOLON_FUNCTIONS . '/headercss/slider.php' ); ?>

<?php include( SEMICOLON_FUNCTIONS . '/headercss/pagetitle.php' ); ?>

<?php if( semi_option( 'submenu_width' ) != '' AND semi_option( 'submenu_width' ) != 200 ): ?>

#primary-menu ul ul { width: <?php echo semi_option( 'submenu_width' ); ?>px; }

#primary-menu ul ul ul { left: <?php echo semi_option( 'submenu_width' ); ?>px !important; }

<?php endif; ?>

<?php include( SEMICOLON_FUNCTIONS . '/headercss/colors.php' ); ?>

<?php include( SEMICOLON_FUNCTIONS . '/headercss/fonts.php' ); ?>

<?php echo semi_option( 'customcss', true ); ?>

</style>

<!-- Google Analytics
============================================= -->
<?php echo semi_option( 'ganalytics', true ); ?>

<?php if( semi_option( 'api_instagram_access' ) != '' AND semi_option( 'api_instagram_client' ) != '' ): ?>

<script type="text/javascript"> if( jQuery().spectragram ) { jQuery.fn.spectragram.accessData = { accessToken: '<?php echo semi_option( 'api_instagram_access' ); ?>', clientID: '<?php echo semi_option( 'api_instagram_client' ); ?>' }; } </script>

<?php endif; ?>

<?php }

add_action('wp_head', 'semicolon_head_option_styles');


/*--------------------------------------------------------
    WP Footer
--------------------------------------------------------*/


function custom_footer_codes() {
    echo semi_option( 'footercode', true );
}

add_action('wp_footer', 'custom_footer_codes', 100);


/*--------------------------------------------------------
    Text Widget Formatting
--------------------------------------------------------*/


add_filter('widget_text', 'shortcode_unautop');
add_filter('widget_text', 'do_shortcode');


/*--------------------------------------------------------
    Primary Menu with Sub Title
--------------------------------------------------------*/


class pmenu_subtitle_walker extends Walker_Nav_Menu {

    function start_el(&$output, $item, $depth = 0, $args = array(), $id = 0) {

        global $wp_query;

        $indent = ( $depth ) ? str_repeat( "\t", $depth ) : '';

        $class_names = $value = '';

        $classes = empty( $item->classes ) ? array() : (array) $item->classes;

        $class_names = join( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item ) );
        $class_names = ' class="'. esc_attr( $class_names ) . '"';

        $output .= $indent . '<li id="menu-item-'. $item->ID . '"' . $value . $class_names .'>';

        $attributes  = ! empty( $item->attr_title ) ? ' title="'  . esc_attr( $item->attr_title ) .'"' : '';
        $attributes .= ! empty( $item->target )     ? ' target="' . esc_attr( $item->target     ) .'"' : '';
        $attributes .= ! empty( $item->xfn )        ? ' rel="'    . esc_attr( $item->xfn        ) .'"' : '';
        $attributes .= ! empty( $item->url )        ? ' href="'   . esc_attr( $item->url        ) .'"' : '';

        $prepend = ! empty( $item->description ) ? '<div>' : '<div style="margin-top: 10px;">';
        $append = '</div>';
        $description  = ! empty( $item->description ) ? '<span>'.esc_attr( $item->description ).'</span>' : '';

        if($depth != 0) {
            $prepend = '<div>';
            $description = "";
        }

        $item_output = $args->before;
        $item_output .= '<a'. $attributes .'>';
        $item_output .= $args->link_before .$prepend.apply_filters( 'the_title', $item->title, $item->ID ).$append;
        $item_output .= $description.$args->link_after;
        $item_output .= '</a>';
        $item_output .= $args->after;

        $output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );

    }

}


/*--------------------------------------------------------
    Primary Menu - Other Headers
--------------------------------------------------------*/


class pmenu_subtitle_walker2 extends Walker_Nav_Menu {

    function start_el(&$output, $item, $depth = 0, $args = array(), $id = 0) {

        global $wp_query;

        $indent = ( $depth ) ? str_repeat( "\t", $depth ) : '';

        $class_names = $value = '';

        $classes = empty( $item->classes ) ? array() : (array) $item->classes;

        $class_names = join( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item ) );
        $class_names = ' class="'. esc_attr( $class_names ) . '"';

        $output .= $indent . '<li id="menu-item-'. $item->ID . '"' . $value . $class_names .'>';

        $attributes  = ! empty( $item->attr_title ) ? ' title="'  . esc_attr( $item->attr_title ) .'"' : '';
        $attributes .= ! empty( $item->target )     ? ' target="' . esc_attr( $item->target     ) .'"' : '';
        $attributes .= ! empty( $item->xfn )        ? ' rel="'    . esc_attr( $item->xfn        ) .'"' : '';
        $attributes .= ! empty( $item->url )        ? ' href="'   . esc_attr( $item->url        ) .'"' : '';

        $prepend = '<div>';
        $append = '</div>';

        $item_output = $args->before;
        $item_output .= '<a'. $attributes .'>';
        $item_output .= $args->link_before .$prepend.apply_filters( 'the_title', $item->title, $item->ID ).$append;
        $item_output .= $args->link_after;
        $item_output .= '</a>';
        $item_output .= $args->after;

        $output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );

    }

}


/*--------------------------------------------------------
    Top Menu with Divider
--------------------------------------------------------*/


class topmenu_walker extends Walker_Nav_Menu {

    function start_el(&$output, $item, $depth = 0, $args = array(), $id = 0) {

        global $wp_query;

        $indent = ( $depth ) ? str_repeat( "\t", $depth ) : '';

        $class_names = $value = '';

        $classes = empty( $item->classes ) ? array() : (array) $item->classes;

        $class_names = join( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item ) );
        $class_names = ' class="'. esc_attr( $class_names ) . '"';

        $output .= $indent . '<li id="menu-item-'. $item->ID . '"' . $value . $class_names .'>';

        $attributes  = ! empty( $item->attr_title ) ? ' title="'  . esc_attr( $item->attr_title ) .'"' : '';
        $attributes .= ! empty( $item->target )     ? ' target="' . esc_attr( $item->target     ) .'"' : '';
        $attributes .= ! empty( $item->xfn )        ? ' rel="'    . esc_attr( $item->xfn        ) .'"' : '';
        $attributes .= ! empty( $item->url )        ? ' href="'   . esc_attr( $item->url        ) .'"' : '';

        if( $depth === 0 ) {
            $prepend = '<span>/</span>';
        } else {
            $prepend = '';
        }
        $append = '';

        $item_output = $args->before;
        $item_output .= $prepend.'<a'. $attributes .'>';
        $item_output .= $args->link_before.apply_filters( 'the_title', $item->title, $item->ID ).$append.$args->link_after;
        $item_output .= '</a>';
        $item_output .= $args->after;

        $output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );

    }

}


add_filter('nav_menu_css_class', 'current_type_nav_class', 10, 2 );

function current_type_nav_class($classes, $item) {

    if( is_404() OR get_post_type() == 'faqs' OR get_post_type() == 'portfolio' ):

        $classes = array_diff( $classes, array('current-menu-parent') );

        $classes = array_values( $classes );

    endif;

    return $classes;

}


add_filter('wp_nav_menu_top_items', 'sm_add_langwitcher_to_nav', 10, 2);

function sm_add_langwitcher_to_nav($items, $args) {

    if (function_exists('icl_get_languages')) {

        $languages = icl_get_languages('skip_missing=0&orderby=code');

        $language_output = '';

        if(!empty($languages)){

            foreach($languages as $l){

                if($l['country_flag_url']){

                    if(!$l['active']) {
                        $language_output .= '<li><a href="'.$l['url'].'"><img src="'.$l['country_flag_url'].'" height="12" alt="'.$l['language_code'].'" width="16" /> '.$l['translated_name'].'</a></li>'."\n";
                    } else {
                        $language_output_active_item = '<span>/</span><a href="'.$l['url'].'" style="text-transform: uppercase;">'.$l['language_code'].'</a>';
                    }

                }

            }

            $items .= '<li>' . $language_output_active_item . '<ul>' . $language_output . '</ul></li>';

        }
    }

    return $items;

}


/*--------------------------------------------------------
    Comments Processing
--------------------------------------------------------*/


if ( ! function_exists( 'coworker_comment' ) ) :

function coworker_comment( $comment, $args, $depth ) {
	$GLOBALS['comment'] = $comment;
	switch ( $comment->comment_type ) :
		case 'pingback' :
		case 'trackback' :
	?>

                        <li class="pingback clearfix">
                    		<p><?php _e( '<strong>Pingback:</strong>', 'coworker' ); ?> <?php comment_author_link(); ?> <?php edit_comment_link( __( '{Edit}', 'coworker' ), '<small class="edit-link">', '</small>' ); ?></p>
	<?php
			break;
		default :
	?>

                        <li <?php comment_class(); ?> id="li-comment-<?php comment_ID(); ?>">

                            <div id="comment-<?php comment_ID(); ?>" class="comment-wrap clearfix">

                                <div class="comment-meta">

                                    <div class="comment-author vcard">

                                        <span class="comment-avatar clearfix"><?php echo ( $comment->comment_parent == 0 ) ? get_avatar( $comment, 60 ) : get_avatar( $comment, 40 ); ?></span>

                                    </div>

                                </div>

                                <div class="comment-content clearfix">

                                    <?php if ($comment->comment_approved == '0') : ?>
                                    <p class="comment-awaiting-moderation"><?php _e( 'This comment is awaiting moderation.', 'coworker' ); ?></p>
                                    <?php else:

                                    $commentstime = get_comment_date( __( 'F j, Y', 'coworker' ) ) . __( ' at ', 'coworker' ) . get_comment_time( __( 'g:i a', 'coworker' ) );

                                    ?>
                                    <div class="comment-author"><?php echo get_comment_author_link(); ?><span><a href="<?php echo esc_url( get_comment_link( $comment->comment_ID ) ) ?>" title="<?php _e('Permalink to this comment', 'coworker'); ?>"><?php echo $commentstime; ?></a><?php if ( !$comment->comment_approved == '0') : ?><?php comment_reply_link(array_merge( array( 'reply_text' => __( 'Reply', 'coworker' ), 'before' => ' &middot; ' ), array('depth' => $depth, 'max_depth' => $args['max_depth']))) ?><?php endif; ?></span></div>

                                	<?php comment_text(); ?>

                                    <?php endif; ?>

                                </div>

                                <div class="clear"></div>

                            </div>

	<?php
			break;
	endswitch;
}
endif;

function custom_widget_featured_image() {
	global $post;
 
	echo tribe_event_featured_image( $post->ID, array(50,50) );
}
add_action( 'tribe_events_list_widget_before_the_event_title', 'custom_widget_featured_image' );

/*--------------------------------------------------------
    Required Files
--------------------------------------------------------*/


define( 'RWMB_URL', trailingslashit( get_template_directory_uri() . '/include/metabox' ) );

define( 'RWMB_DIR', trailingslashit( SEMICOLON_FUNCTIONS . '/metabox' ) );

require_once RWMB_DIR . 'meta-box.php';

include( SEMICOLON_FUNCTIONS . '/posttypes.php' ); // Theme PostTypes

include( SEMICOLON_FUNCTIONS . '/metaboxes.php' ); // Theme MetaBoxes

/**
* Move RSVP Tickets form in events template
*/
if (class_exists('Tribe__Tickets__RSVP')) {
remove_action( 'tribe_events_single_event_after_the_meta', array( Tribe__Tickets__RSVP::get_instance(), 'front_end_tickets_form' ), 5 );
add_action( 'tribe_events_single_event_before_the_content', array( Tribe__Tickets__RSVP::get_instance(), 'front_end_tickets_form' ), 5 );
}

/*
 * Moves the front-end ticket purchase form, accepts WP action/hook and optional hook priority
 * 
 * @param $ticket_location_action WP Action/hook to display the ticket form at
 * @param $ticket_location_priority Priority for the WP Action
 */
function tribe_etp_move_tickets_purchase_form ( $ticket_location_action, $ticket_location_priority = 10 ) {
    $etp_classes = array(
//	'Tribe__Tickets_Plus__Commerce__EDD__Main',
    //	'Tribe__Tickets_Plus__Commerce__Shopp__Main', // As of ETP v4.0 Shopp will generate errors when referenced, if not active. Uncomment this line if you have Shopp Active
//	'Tribe__Tickets_Plus__Commerce__WPEC__Main',
	'Tribe__Tickets_Plus__Commerce__WooCommerce__Main'
    );
    foreach ( $etp_classes as $ticket_class ) {
	if ( ! class_exists( $ticket_class ) ) break;
	$form_display_function = array( $ticket_class::get_instance(), 'front_end_tickets_form' );
	if ( has_action ( 'tribe_events_single_event_after_the_meta', $form_display_function ) ) {
	    remove_action( 'tribe_events_single_event_after_the_meta', $form_display_function, 5 );
	    add_action( $ticket_location_action, $form_display_function, $ticket_location_priority );
	}
    }
}
/*
 * TO MOVE THE TICKET FORM UNCOMMENT ONE OF THE FOLLOWING BY REMOVING THE //
 */
/*
 * Uncomment to Move Ticket Form Below Related Events
 */
//tribe_etp_move_tickets_purchase_form( 'tribe_events_single_event_after_the_meta', 20 );
/*
 * Uncomment to Move Ticket Form Below the Event Description
 */
//tribe_etp_move_tickets_purchase_form( 'tribe_events_single_event_after_the_content', 5 );
/*
 * Uncomment to Move Ticket Form Above the Event Description
 */
tribe_etp_move_tickets_purchase_form( 'tribe_events_single_event_before_the_content' );
?>
<?php
/**
 * Example for adding event data to WooCommerce checkout for Events Calendar tickets.
 * @link http://theeventscalendar.com/support/forums/topic/event-title-and-date-in-cart/
 */
add_filter( 'woocommerce_cart_item_name', 'woocommerce_cart_item_name_event_title', 10, 3 );
function woocommerce_cart_item_name_event_title( $title, $values, $cart_item_key ) {
    $ticket_meta = get_post_meta( $values['product_id'] );
    $event_id = absint( $ticket_meta['_tribe_wooticket_for_event'][0] );
    if ( $event_id ) {
	$title = sprintf( '%s for <a href="%s" target="_blank"><strong>%s</strong></a>', $title, get_permalink( $event_id ), get_the_title( $event_id ) );
    }
    return $title;
}

/* add_action( 'wp_footer', 'tribe_limit_rsvps_to_one' );

function tribe_limit_rsvps_to_one() {
    wp_enqueue_script( 'jquery' );
?>
    <script>
	jQuery(document).ready(function($){
	    if ( $('.tribe-events-tickets' ).length ) {
		var $input = $('.tribe-events-tickets' ).find( 'input.tribe-ticket-quantity' );
		$input.attr( 'type', 'text' );
		$input.attr( 'disabled', 'disabled' );
		$input.attr( 'max', '1' );
		$input.val( '1' );
	    }
	});
    </script>
<?php 
} */
?>