<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
                    
                        <div id="post-<?php the_ID(); ?>" <?php post_class('entry post-single clearfix nobottomborder nobottompadding nobottommargin'); ?>>
                        
                            <div class="col_full clearfix" style="margin-bottom: 10px;">
                            
                                <?php get_template_part( 'include/blog/title' ); ?>
                                
                                <?php get_template_part( 'include/blog/default/meta' ); ?>
                                
                                <?php
                                
                                if( get_post_meta( get_the_ID(), 'semi_post_disable_thumb', TRUE ) != 1 ) {
                                
                                    $format = get_post_format();
                                    
                                    if( $format == 'audio' OR $format == 'video' ) {
                                        get_template_part( 'include/blog/default/embed' );
                                    } elseif( $format == 'gallery' ) {
                                        get_template_part( 'include/blog/full/gallery' );
                                    } else {
                                        get_template_part( 'include/blog/full/thumbnail' );
                                    }
                                
                                }
                                
                                ?>
                            
                            </div>
                            
                            <div class="postcontent nobottommargin clearfix<?php if( get_post_meta( get_the_ID(), 'semi_sidebar', TRUE ) == 'left' ) { echo ' col_last'; } ?>">
                            
                                <?php get_template_part( 'include/blog/content' ); ?>
                                
                                
                                <div class="double-line"></div>
                                
                                
                                <?php get_template_part( 'include/blog/single/related' ); ?>
                                
                                
                                <?php get_template_part( 'include/blog/single/navigation' ); ?>
                                
                                
                                <div class="clear"></div>
                                
                                
                                <?php get_template_part( 'include/blog/single/comments' ); ?>
                            
                            </div>
                            
                            <?php get_sidebar(); ?>
                        
                        </div>
                        
                    <?php endwhile; else: ?>
                        
                        <div class="alert alert-error">
                            <?php _e( "<strong>Sorry!</strong> There are no posts to show at the moment. Please come back later.", "coworker" ); ?>
                        </div>
                        
                    <?php endif; ?>