<?php get_header(); ?>
        
        
        <?php get_template_part( 'include/content', 'head' ); ?>
                
                    <?php echo do_shortcode( '[portfolio filter="true"]' ); ?>
                
        <?php get_template_part( 'include/content', 'foot' ); ?>
        
        
<?php get_footer(); ?>