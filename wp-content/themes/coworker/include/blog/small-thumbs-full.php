<div class="col_full nobottommargin clearfix">
                    
                    <?php
                    
                    $limit = get_option('posts_per_page');
                    
                    query_posts( array( 'showposts' => $limit, 'paged' => get_query_var('paged') ) );
                    
                    if (have_posts()) : ?>
                    
                        <div id="posts" class="small-posts clearfix">
                        
                    <?php while (have_posts()) : the_post(); ?>
                        
                            <div id="post-<?php the_ID(); ?>" <?php post_class('entry clearfix'); ?>>
                            
                                <?php
                                
                                $format = get_post_format();
                                
                                if( $format AND $format != '' ) {
                                    get_template_part( 'include/blog/small-full/post', $format );
                                } else {
                                    get_template_part( 'include/blog/small-full/post', 'standard' );
                                }
                                
                                ?>
                            
                            </div>
                        
                    <?php endwhile; ?>
                            
                            <?php get_template_part( 'include/blog/navigation' ); ?>
                        
                        </div>
                        
                    <?php else: ?>
                    
                        <?php get_template_part( 'include/blog/error' ); ?>
                    
                    <?php endif; wp_reset_query(); ?>
                    
                    </div>