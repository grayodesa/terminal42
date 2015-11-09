<?php get_header(); ?>


        <?php get_template_part( 'include/content', 'head' ); ?>
                
                    <?php
                    
                    $layout = get_post_meta( get_queried_object_id(), 'semi_post_layout', true );
                    
                    if( $layout != '' ):
                    
                    get_template_part( 'include/blog/single/' . $layout );
                    
                    else:
                    
                    get_template_part( 'include/blog/single/default' );
                    
                    endif;
                    
                    ?>
                
        <?php get_template_part( 'include/content', 'foot' ); ?>


<?php get_footer(); ?>