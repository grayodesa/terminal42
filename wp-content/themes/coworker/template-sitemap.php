<?php
/*
Template Name: Sitemap
*/
?>
<?php get_header(); ?>
        
        
        <?php get_template_part( 'include/content', 'head' ); ?>
                
                    <?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
                    
                    <div class="col_one_third nobottommargin">
                    
                        <h3><?php _e( 'Pages', 'coworker' ); ?></h3>
                        
                        <ul class="sitemap">
                            <?php wp_list_pages("title_li=&echo=1&sort_column=menu_order&depth=0"); ?>
                        </ul>
                        
                    </div>
                    
                    <div class="col_one_third nobottommargin">
                    
                        <h3><?php _e( 'Posts', 'coworker' ); ?></h3>
                        
                        <?php
                                
                        $argsposts = array( 'post_type' => 'post' , 'posts_per_page' => 25 );
                        
            			$sitemap_posts = new WP_Query( $argsposts );
                        
                        if ($sitemap_posts->have_posts()) :
                        
                        ?>
                        
                        <ul class="sitemap bottommargin">
                        
                        <?php
                        
                        while ( $sitemap_posts->have_posts() ) : $sitemap_posts->the_post();
                        
                        ?>
                            <li>
                                <a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>" rel="bookmark"><?php the_title(); ?></a>
                                <span><i class="icon-calendar"></i> <?php the_time( __( 'j M, Y', 'coworker' ) ); ?> &middot; <a href="<?php echo get_permalink() . '#comments'; ?>"><i class="icon-comments-alt"></i> <?php comments_number(__('No Comments', 'coworker'), __('1 Comment', 'coworker'), __('% Comments', 'coworker')); ?></a></span>
                            </li>
                        <?php
                
                        endwhile;
                        
                        ?>
                        
                        </ul>
                        
                        <?php
                        
                        endif;
        
                        wp_reset_postdata();
                        
                        ?>
                        
                        <h3><?php _e( 'Categories', 'coworker' ); ?></h3>
                        
                        <?php
                        
                        $args = array( 'parent' => 0 );
                        
                        $categories = get_categories( $args );
                        
                        if( $categories ):
                        
                        ?>
                        
                        <ul class="sitemap">
                        
                        <?php foreach ( $categories as $category ) { ?>
                        
                            <li><a href="<?php echo get_category_link( $category->term_id ); ?>"><?php echo $category->name; ?></a> (<?php echo $category->count; ?>)</li>
                        
                        <?php } ?>
                        
                        </ul>
                        
                        <?php endif; ?>
                    
                    </div>
                    
                    <div class="col_one_third col_last nobottommargin">
                    
                        <h3><?php _e('Portfolio', 'coworker'); ?></h3>
                        
                        <?php
                        
                        $argsportfolio = array( 'post_type' => 'portfolio' , 'posts_per_page' => 25 );
                        
            			$sitemap_portfolio = new WP_Query( $argsportfolio );
                        
                        if ($sitemap_portfolio->have_posts()) :
                        
                        ?>
                        
                        <ul class="sitemap">
                        
                        <?php while ( $sitemap_portfolio->have_posts() ) : $sitemap_portfolio->the_post(); ?>
                        
                            <li><a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>"><?php the_title(); ?></a></li>
                        
                        <?php endwhile; ?>
                        
                        </ul>
                        
                        <?php endif; wp_reset_postdata(); ?>
                    
                    </div>
                    
                    <?php endwhile; endif; ?>
                
        <?php get_template_part( 'include/content', 'foot' ); ?>
        
        
<?php get_footer(); ?>