<?php

if( semi_option( 'comingsoon' ) == 1 ):

    if( !is_page_template( 'template-comingsoon.php' ) AND !is_404() AND !current_user_can( 'edit_published_posts' ) AND ( semi_option( 'comingsoon_page' ) != '' AND semi_option( 'comingsoon_page' ) != 0 AND check_currenturl() != get_permalink( semi_option( 'comingsoon_page' ) ) ) ):

        wp_redirect( get_permalink( semi_option( 'comingsoon_page' ) ) );
        exit;

    endif;

endif;

?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>

<!-- ============================================
    Head
============================================= -->
<head>


    <meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php bloginfo('charset'); ?>" />
    <meta name="author" content="Терминал 42" />
<meta name="google-site-verification" content="wVkCnGXZpIixaBeYpgojEtYhkQpAECGbHF_Enix16JM" />
<link rel="apple-touch-icon" sizes="57x57" href="/apple-icon-57x57.png">
<link rel="apple-touch-icon" sizes="60x60" href="/apple-icon-60x60.png">
<link rel="apple-touch-icon" sizes="72x72" href="/apple-icon-72x72.png">
<link rel="apple-touch-icon" sizes="76x76" href="/apple-icon-76x76.png">
<link rel="apple-touch-icon" sizes="114x114" href="/apple-icon-114x114.png">
<link rel="apple-touch-icon" sizes="120x120" href="/apple-icon-120x120.png">
<link rel="apple-touch-icon" sizes="144x144" href="/apple-icon-144x144.png">
<link rel="apple-touch-icon" sizes="152x152" href="/apple-icon-152x152.png">
<link rel="apple-touch-icon" sizes="180x180" href="/apple-icon-180x180.png">
<link rel="icon" type="image/png" sizes="192x192"  href="/android-icon-192x192.png">
<link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
<link rel="icon" type="image/png" sizes="96x96" href="/favicon-96x96.png">
<link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
<link rel="manifest" href="/manifest.json">
<meta name="msapplication-TileColor" content="#ffffff">
<meta name="msapplication-TileImage" content="/ms-icon-144x144.png">
<meta name="theme-color" content="#ffffff">
<script src="//use.typekit.net/mqr5uxn.js"></script>
<script>try{Typekit.load({ async: true });}catch(e){}</script>

    <!-- ============================================
        Document Title
    ============================================= -->
    <title><?php wp_title('|', true, 'right'); ?></title>


    <link rel="profile" href="http://gmpg.org/xfn/11" />
    <link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>" />

    <?php if( semi_option( 'nonresponsive' ) != 1 ): ?>

    <!-- ============================================
        Responsive
    ============================================= -->
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />

    <!--[if lt IE 9]>
        <script src="http://css3-mediaqueries-js.googlecode.com/svn/trunk/css3-mediaqueries.js"></script>
    <![endif]-->

    <?php endif; ?>

    <!-- ============================================
        WP Head
    ============================================= -->
    <?php wp_head(); ?>
<script>(function() {
var _fbq = window._fbq || (window._fbq = []);
if (!_fbq.loaded) {
var fbds = document.createElement('script');
fbds.async = true;
fbds.src = '//connect.facebook.net/en_US/fbds.js';
var s = document.getElementsByTagName('script')[0];
s.parentNode.insertBefore(fbds, s);
_fbq.loaded = true;
}
_fbq.push(['addPixelId', '303301413200788']);
})();
window._fbq = window._fbq || [];
window._fbq.push(['track', 'PixelInitialized', {}]);
</script>
<noscript><img height="1" width="1" alt="" style="display:none" src="https://www.facebook.com/tr?id=303301413200788&amp;ev=PixelInitialized" /></noscript>
</head>

<!-- ============================================
    Body
============================================= -->
<body <?php if( semi_option( 'layout' ) == 'full' AND !is_page_template( 'template-comingsoon.php' ) ) { body_class( 'stretched' ); } else { body_class(); } ?>>

<?php if( is_singular() AND ( semi_option('blog_comments_type') == 'facebook' OR get_post_meta( get_queried_object_id(), 'semi_post_comments_system', TRUE ) == 'facebook' ) ): ?>

<div id="fb-root"></div>
<script>(function(d, s, id) {
  var js, fjs = d.getElementsByTagName(s)[0];
  if (d.getElementById(id)) return;
  js = d.createElement(s); js.id = id;
  js.src = "//connect.facebook.net/en_US/all.js#xfbml=1&appId=<?php echo semi_option('facebook_app'); ?>";
  fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));</script>

<?php endif; ?>

    <?php

    if( SM_WOOCOMMERCE_ACTIVE ):

    global $woocommerce; ?>

    <div id="fshopping-cart-wrap">

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

                <div class="mini-cart-item clearfix"><?php _e( 'Sorry, your Shopping Cart seems to be Empty.!', 'coworker' ); ?></div>

            </div>

            <div class="mini-checkout-wrap">
                <a href="<?php echo get_permalink( woocommerce_get_page_id( 'shop' ) ); ?>" title="<?php _e('Start Shopping', 'coworker'); ?>" class="divcenter nomargin simple-button"><?php _e('Start Shopping', 'coworker'); ?></a>
            </div>

            <?php endif; ?>

        </div>

    </div>

    <script type="text/javascript">

        jQuery(window).load(function(){

            fshopCartTrigger();

        });

    </script>

    <?php endif; ?>

    <div id="wrapper" class="clearfix">

        <?php if( semi_option( 'sticky_menu' ) == 1 AND !is_page_template( 'template-comingsoon.php' ) ): ?>

        <div id="sticky-menu" class="clearfix">

            <div class="container clearfix">

                <div class="sticky-logo">

                    <a href="<?php echo home_url('/'); ?>"><img src="<?php echo semi_option( 'sticky_logo' ) ? semi_option( 'sticky_logo' ) : semi_option( 'logo' ); ?>" alt="<?php echo get_bloginfo('name'); ?>" title="<?php echo get_bloginfo('name'); ?>" /></a>

                </div>

                <div class="sticky-search-trigger">

                    <a href="#"><i class="icon-search"></i></a>

                </div>

                <div class="sticky-menu-wrap">

                    <?php

                        if( has_nav_menu( 'sticky' ) ) {
                            $stickymenu = 'sticky';
                        } else {
                            $stickymenu = 'primary';
                        }

                        wp_nav_menu( array(
                            'theme_location' => $stickymenu,
                            'container' => '',
                            'fallback_cb' => '',
                            'walker' => new pmenu_subtitle_walker2()
                        ) );
                    ?>

                </div>

                <div class="sticky-search-area">

                    <form action="<?php echo home_url( '/' ); ?>" method="get" role="search" id="sticky-search">

                        <input type="text" id="sticky-search-input" name="s" placeholder="<?php _e( 'Type &amp; Hit Enter', 'coworker' ); ?>" value="" />

                    </form>

                    <div class="sticky-search-area-close">

                        <a href="#"><i class="icon-remove"></i></a>

                    </div>

                </div>

            </div>

        </div>

        <?php endif; ?>

        <?php if( semi_option( 'topbar' ) == 1 AND semi_option( 'header_style' ) != 'header2' ): ?>

        <div id="top-bar" <?php if( semi_option( 'topbar_content' ) == 'social-menu' ) { echo 'class="top-bar2"'; } ?>>

            <div class="container clearfix">

                <div id="top-menu">

                    <?php
                        wp_nav_menu( array(
                            'theme_location' => 'top',
                            'container' => '',
                            'fallback_cb' => '',
                            'walker' => new topmenu_walker()
                        ) );
                    ?>

                </div>

                <?php get_header_icons(); ?>

            </div>

        </div>

        <?php endif; ?>

        <div id="header" <?php get_header_class(); ?>>


            <div class="container clearfix">


                <div id="logo">

                    <a href="<?php echo home_url('/'); ?>" class="standard-logo"><img src="<?php echo semi_option( 'logo' ); ?>" alt="<?php echo get_bloginfo('name'); ?>" title="<?php echo get_bloginfo('name'); ?>" /></a>
                    <a href="<?php echo home_url('/'); ?>" class="retina-logo"><img src="<?php echo semi_option( 'retinalogo' ); ?>" alt="<?php echo get_bloginfo('name'); ?>" title="<?php echo get_bloginfo('name'); ?>" width="<?php echo semi_option( 'logo_width' ); ?>" height="<?php echo semi_option( 'logo_height' ); ?>" /></a>

                </div>

                <?php if( is_page_template( 'template-comingsoon.php' ) ):

                    get_top_contacts();

                else:

                    get_header_menu();

                    get_header_rightcontent();

                endif; ?>

            </div>


            <?php if( !is_page_template( 'template-comingsoon.php' ) ):

                get_header_menu2();

            endif; ?>

        </div>