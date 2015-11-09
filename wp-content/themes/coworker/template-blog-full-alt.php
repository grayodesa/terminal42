<?php
/*
Template Name: Blog - Full Alternate Layout
*/
?>
<?php get_header(); ?>
        
        
        <?php get_template_part( 'include/content', 'head' ); ?>
                
                    <?php get_template_part( 'include/blog/full', 'layout-alt' ); ?>
                
        <?php get_template_part( 'include/content', 'foot' ); ?>
        
        
<?php get_footer(); ?>