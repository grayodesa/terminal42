<?php get_header(); ?>
        
        
        <?php get_template_part( 'include/content', 'head' ); ?>
                
                    <?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
                    
                    <div class="entry_content clearfix">
                    
                        <?php the_content(); ?>
                    
                    </div>
                    
                    <?php wp_link_pages('before=<div id="page-links">&after=</div>'); endwhile; endif; ?>
                
        <?php get_template_part( 'include/content', 'foot' ); ?>
        
        
<?php get_footer(); ?>