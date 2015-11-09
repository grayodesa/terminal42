<?php
/*
Template Name: Blog - Alternate Layout
*/
?>
<?php get_header(); ?>
        
        
        <?php get_template_part( 'include/content', 'head' ); ?>
                
                    <?php get_template_part( 'include/blog/alt', 'layout' ); ?>
                
        <?php get_template_part( 'include/content', 'foot' ); ?>
        
        
<?php get_footer(); ?>