<?php if( is_single() ): ?>
<div class="entry_content">
                            
                                <?php the_content(); ?>
                                
                                
                                <?php get_template_part( 'include/blog/single/extra' ); ?>
                            
                            
                            </div>
<?php else: ?>
<div class="entry_content">
                            
                                <?php the_excerpt(); ?>
                            
                            </div>
<?php endif; ?>