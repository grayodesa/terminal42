<div class="postcontent nobottommargin clearfix<?php if( get_post_meta( get_query_var('page_id'), 'semi_page_sidebar', true ) == 'left' ) { echo ' col_last'; } ?>">
                        
                            <?php get_template_part( 'include/portfolio/filter' ); ?>
                            
                            <?php
                            
                            $args = array( 'post_type' => 'portfolio', 'orderby' => 'menu_order', 'order' => 'ASC', 'posts_per_page' => -1 );
                            
                            if( choosen_port_cat() != false ) {
                                $args['tax_query'] = array( array( 'taxonomy' => 'port-group', 'field' => 'id', 'terms' => choosen_port_cat() ) );
                            }
                            
                			$portfolio = new WP_Query( $args );
                            
                            if( $portfolio->have_posts() ):
                            
                            ?>
                            
                            <div id="portfolio" class="clearfix">
                            
                                <?php while ( $portfolio->have_posts() ) : $portfolio->the_post();
                                
                                get_template_part( 'include/portfolio/items', 's' );
                                
                                endwhile; ?>
                            
                            </div>
                            
                            <?php get_template_part( 'include/portfolio/script' ); ?>
                            
                            <?php endif; wp_reset_postdata(); ?>
                        
                        </div>
                        
                        <?php get_sidebar(); ?>