<?php
/*
Template Name: Blog - Default Layout
*/
?>
<?php get_header(); ?>
        
        
        <?php get_template_part( 'include/content', 'head' ); ?>
                
                    <?php get_template_part( 'include/blog/default' ); ?>
                
        <?php get_template_part( 'include/content', 'foot' ); ?>
        
        
<?php get_footer(); ?>