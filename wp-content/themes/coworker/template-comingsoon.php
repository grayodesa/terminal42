<?php
/*
Template Name: Coming Soon
*/
?>
<?php get_header(); ?>
        
        
        <div id="countdown-wrap">
            
            <?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
            
            <div class="container clearfix">
            
                <h1><?php echo get_post_meta( get_the_ID(), 'semi_page_comingsoon_message', TRUE ); ?></h1>
                
                <div id="countdown"></div>
                
                <script type="text/javascript">
                    
                    <?php if( get_post_meta( get_the_ID(), 'semi_page_comingsoon_custom', TRUE ) == '' ): ?>
                    
                    var countDownDate = new Date( <?php echo get_post_meta( get_the_ID(), 'semi_page_comingsoon_year', TRUE ); ?>, <?php echo get_post_meta( get_the_ID(), 'semi_page_comingsoon_month', TRUE ); ?>, <?php echo get_post_meta( get_the_ID(), 'semi_page_comingsoon_day', TRUE ); ?> );
                    
                    <?php else: ?>
                    
                    var countDownDate = <?php echo get_post_meta( get_the_ID(), 'semi_page_comingsoon_custom', TRUE ); ?>;
                    
                    <?php endif; ?>
                    
                    jQuery('#countdown').countdown({until: countDownDate});
                
                </script>
            
            </div>
            
            <?php endwhile; endif; ?>
        
        </div>
        
        
        <?php get_template_part( 'include/content', 'head' ); ?>
                
                    <?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
                    
                    <div class="entry_content clearfix">
                    
                        <?php the_content(); ?>
                    
                    </div>
                    
                    <?php endwhile; endif; ?>
                
        <?php get_template_part( 'include/content', 'foot' ); ?>
        
        
<?php get_footer(); ?>