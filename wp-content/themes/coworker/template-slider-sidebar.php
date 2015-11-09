<?php
/*
Template Name: Slider with Sidebar
*/
?>
<?php get_header(); ?>
        
        
        <?php get_template_part( 'include/slider/slider' ); ?>
        
        
        <?php get_template_part( 'include/content', 'head' ); ?>
                
                    <div class="postcontent entry_content nobottommargin clearfix<?php page_sidebar_align(); ?>">
                    
                    <?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
                    
                        <div class="entry_content clearfix">
                        
                            <?php the_content(); ?>
                        
                        </div>
                    
                    <?php endwhile; endif; ?>
                    
                    </div>
                    
                    <?php get_sidebar(); ?>
                
        <?php get_template_part( 'include/content', 'foot' ); ?>
        
        
<?php get_footer(); ?>