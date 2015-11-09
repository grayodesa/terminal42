<?php
header("HTTP/1.1 404 Not Found");
header("Status: 404 Not Found");
?>
<?php get_header(); ?>
        
        
        <?php get_template_part( 'include/content', 'head' ); ?>
                
                
                    <div class="error-404">
                                        
                        <?php if( semi_option( 'error_message' ) != '' ) { echo '<span>' . semi_option( 'error_message' ) . '</span>'; } else { ?><span>ooopss..! error</span><?php } ?>404
                                        
                    </div>
                    
                    <div class="error-404-meta">
                    
                        <form role="search" method="get" action="<?php echo home_url( '/' ); ?>" method="post">
                    
                            <input type="text" id="error404-search" name="s" value="" placeholder="Search here &amp; Find yourself a Way..." />
                            
                            <input type="submit" id="error404-search-submit" name="error404-search-submit" value="submit" />
                        
                        </form>
                    
                    </div>
                
        <?php get_template_part( 'include/content', 'foot' ); ?>
        
        
<?php get_footer(); ?>