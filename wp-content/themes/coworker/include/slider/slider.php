<?php if ( have_posts() ) : while ( have_posts() ) : the_post();

    $slider = get_post_meta( get_the_ID(), 'semi_page_slider', TRUE );
    
    if( $slider != '' ) {
        get_template_part( 'include/slider/' . $slider );
    } else {
        get_template_part( 'include/slider/flex' );
    }

endwhile; endif; ?>