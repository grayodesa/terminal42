<div id="content">
        
        
            <?php get_template_part( 'include/page', 'title' ); ?>
            
            <?php if( is_page_template( 'template-contact.php' ) OR is_page_template( 'template-contact-split.php' ) ): if (have_posts()) : while (have_posts()) : the_post();
            
            $mapview = get_post_meta(get_the_ID(), 'semi_page_contact_hidemap', true);
            $mapheight = is_numeric( get_post_meta(get_the_ID(), 'semi_page_contact_mheight', true) ) ? get_post_meta(get_the_ID(), 'semi_page_contact_mheight', true) : 400;
            
            if( $mapview != 1 ):
            
            ?>
            
            <div id="slider" style="padding: 0; height: <?php echo $mapheight; ?>px;">
            
                <div class="slider-line" style="display: block !important;"></div>
                
                <div id="google-map" class="gmap" style="width:100%;"></div>
                
            </div>
            
            <?php get_template_part( 'include/contact/script' ); ?>
            
            <?php endif; endwhile; endif; endif; ?>
            
            <div class="content-wrap">
            
            
                <div class="container clearfix">