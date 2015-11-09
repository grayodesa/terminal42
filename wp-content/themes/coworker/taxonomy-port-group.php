<?php get_header(); ?>
        
        
        <?php get_template_part( 'include/content', 'head' ); ?>
                
                    <?php
                    
                    $queried_object = get_queried_object();
                    
                    echo do_shortcode( '[portfolio filter="true" group="' . $queried_object->term_id . '"]' ); ?>
                
        <?php get_template_part( 'include/content', 'foot' ); ?>
        
        
<?php get_footer(); ?>