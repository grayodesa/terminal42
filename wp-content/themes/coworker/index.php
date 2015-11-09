<?php get_header(); ?>
        
        
        <?php get_template_part( 'include/content', 'head' ); ?>
                
                    <?php
                    
                    $layout = semi_option( 'blog_layout' );
                    
                    if( $layout == 'alt' OR $layout == 'full' OR $layout == 'full-alt' OR $layout == 'small' OR $layout == 'small-full' ) {
                        $layout = $layout;
                    } else {
                        $layout = 'default';
                    }
                    
                    if( $layout == 'small' OR $layout == 'small-full' ) {
                        $layoutcss = 'small-posts ';
                    } else { $layoutcss = ''; }
                    
                    if( $layout == 'full' OR $layout == 'full-alt' OR $layout == 'small-full' ) {
                        $showsidebar = 'no';
                        $fullcontainer = 'yes';
                    } else {
                        $showsidebar = 'yes';
                        $fullcontainer = 'no';
                    }
                    
                    if( $fullcontainer == 'yes' ) { ?>
                    
                    <div class="col_full nobottommargin clearfix">
                    
                    <?php } else { ?>
                    
                    <div class="postcontent nobottommargin<?php if( semi_option( 'blog_sidebar' ) == 'left' ) { echo ' col_last'; } ?> clearfix">
                    
                    <?php }
                    
                    if (have_posts()) : ?>
                    
                        <div id="posts" class="<?php echo $layoutcss; ?>clearfix">
                        
                    <?php while (have_posts()) : the_post(); ?>
                        
                            <div id="post-<?php the_ID(); ?>" <?php post_class('entry clearfix'); ?>>
                            
                                <?php
                                
                                $format = get_post_format();
                                
                                if( $format AND $format != '' ) {
                                    get_template_part( 'include/blog/' . $layout . '/post', $format );
                                } else {
                                    get_template_part( 'include/blog/' . $layout . '/post', 'standard' );
                                }
                                
                                ?>
                            
                            </div>
                        
                    <?php endwhile; ?>
                            
                            <?php get_template_part( 'include/blog/navigation' ); ?>
                        
                        </div>
                        
                    <?php else: ?>
                    
                        <?php get_template_part( 'include/blog/error' ); ?>
                    
                    <?php endif; ?>
                    
                    </div>
                    
                    
                    <?php if( $showsidebar != 'no' ) { ?>
                    
                    <?php if( semi_option( 'blog_sidebar' ) == 'left' ) { get_sidebar( 'left' ); } else { get_sidebar(); } ?>
                    
                    <?php } ?>
                
        <?php get_template_part( 'include/content', 'foot' ); ?>
        
        
<?php get_footer(); ?>