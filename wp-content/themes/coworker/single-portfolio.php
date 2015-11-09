<?php get_header(); ?>


        <?php get_template_part( 'include/content', 'head' ); ?>
                
                    <?php if ( have_posts() ) : while ( have_posts() ) : the_post();
                    
                    $layout = get_post_meta( get_the_ID(), 'semi_port_single_layout', TRUE );
                    
                    if( $layout == 'half-left' ) {
                        get_template_part( 'include/portfolio/single/half', 'left' );
                    } elseif( $layout == 'full-right' ) {
                        get_template_part( 'include/portfolio/single/full', 'right' );
                    } elseif( $layout == 'full-left' ) {
                        get_template_part( 'include/portfolio/single/full', 'left' );
                    } elseif( $layout == 'rs-right' ) {
                        get_template_part( 'include/portfolio/single/rs', 'right' );
                    } elseif( $layout == 'rs-left' ) {
                        get_template_part( 'include/portfolio/single/rs', 'left' );
                    } elseif( $layout == 'ls-right' ) {
                        get_template_part( 'include/portfolio/single/ls', 'right' );
                    } elseif( $layout == 'ls-left' ) {
                        get_template_part( 'include/portfolio/single/ls', 'left' );
                    } else {
                        get_template_part( 'include/portfolio/single/default' );
                    }
                    
                    endwhile; endif; ?>
                
        <?php get_template_part( 'include/content', 'foot' ); ?>


<?php get_footer(); ?>