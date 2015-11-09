<?php
/*
Template Name: Landing Page
*/
?>
<?php get_header(); ?>
        
        
        <?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
        
        <div id="landing-area">
        
            <div class="container clearfix">
            
                <?php
                
                $landinglayout = get_post_meta( get_the_ID(), 'semi_page_landing_layout', TRUE );
                
                if( $landinglayout == 'rmlt' ): ?>
                
                <div class="landing-offer-text">
                
                    <?php get_template_part( 'include/landing/text' ); ?>
                
                </div>
                
                <div class="landing-offer-media col_last">
                
                    <?php get_template_part( 'include/landing/media' ); ?>
                
                </div>
                
                <?php elseif( $landinglayout == 'hlmrt' ): ?>
                
                <div class="landing-offer-half">
                
                    <?php get_template_part( 'include/landing/media' ); ?>
                
                </div>
                
                <div class="landing-offer-half col_last">
                
                    <?php get_template_part( 'include/landing/text' ); ?>
                
                </div>
                
                <?php elseif( $landinglayout == 'hrmlt' ): ?>
                
                <div class="landing-offer-half">
                
                    <?php get_template_part( 'include/landing/text' ); ?>
                
                </div>
                
                <div class="landing-offer-half col_last">
                
                    <?php get_template_part( 'include/landing/media' ); ?>
                
                </div>
                
                <?php elseif( $landinglayout == 'lmrc' ): ?>
                
                <div class="landing-offer-media">
                
                    <?php get_template_part( 'include/landing/media' ); ?>
                
                </div>
                
                <div class="landing-offer-text col_last">
                
                    <?php echo do_shortcode( get_post_meta( get_the_ID(), 'semi_page_landing_custom', TRUE ) ); ?>
                
                </div>
                
                <?php elseif( $landinglayout == 'rmlc' ): ?>
                
                <div class="landing-offer-text">
                
                    <?php echo do_shortcode( get_post_meta( get_the_ID(), 'semi_page_landing_custom', TRUE ) ); ?>
                
                </div>
                
                <div class="landing-offer-media col_last">
                
                    <?php get_template_part( 'include/landing/media' ); ?>
                
                </div>
                
                <?php elseif( $landinglayout == 'hlmrc' ): ?>
                
                <div class="landing-offer-half">
                
                    <?php get_template_part( 'include/landing/media' ); ?>
                
                </div>
                
                <div class="landing-offer-half col_last">
                
                    <?php echo do_shortcode( get_post_meta( get_the_ID(), 'semi_page_landing_custom', TRUE ) ); ?>
                
                </div>
                
                <?php elseif( $landinglayout == 'hrmlc' ): ?>
                
                <div class="landing-offer-half">
                
                    <?php echo do_shortcode( get_post_meta( get_the_ID(), 'semi_page_landing_custom', TRUE ) ); ?>
                
                </div>
                
                <div class="landing-offer-half col_last">
                
                    <?php get_template_part( 'include/landing/media' ); ?>
                
                </div>
                
                <?php else: ?>
                
                <div class="landing-offer-media">
                
                    <?php get_template_part( 'include/landing/media' ); ?>
                
                </div>
                
                <div class="landing-offer-text col_last">
                
                    <?php get_template_part( 'include/landing/text' ); ?>
                
                </div>
                
                <?php endif; ?>
            
            </div>
        
        
        </div>
        
        <?php endwhile; endif; ?>
        
        <?php get_template_part( 'include/content', 'head' ); ?>
                
                    <?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
                    
                    <div class="entry_content clearfix">
                    
                        <?php the_content(); ?>
                    
                    </div>
                    
                    <?php endwhile; endif; ?>
                
        <?php get_template_part( 'include/content', 'foot' ); ?>
        
        
<?php get_footer(); ?>