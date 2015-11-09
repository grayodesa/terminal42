<?php
/*
Template Name: Slider
*/
?>
<?php get_header(); ?>
        
        
        <?php get_template_part( 'include/slider/slider' ); ?>
        
        
        <?php get_template_part( 'include/content', 'head' ); ?>
                
                    <?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
                    
                    <div class="entry_content clearfix">
                    
                        <?php the_content(); ?>
                    
                    </div>
                    
                    <?php endwhile; endif; ?>
                
        <?php get_template_part( 'include/content', 'foot' ); ?>
        
        
<?php get_footer(); ?>