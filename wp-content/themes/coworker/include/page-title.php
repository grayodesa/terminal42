            <?php if( is_home() OR is_singular( 'post' ) ): ?>
            
            <div id="page-title">
            
                <div class="container clearfix">
                
                    <h1><?php if( semi_option( 'blog_title' ) != '' ) { echo semi_option( 'blog_title' ); } else { _e( 'The Blog', 'coworker' ); } if( semi_option( 'blog_tagline' ) != '' ) { echo '<span>' . semi_option( 'blog_tagline' ) . '</span>'; } ?></h1>
                    
                    <?php page_title_right(); ?>
                
                </div>
            
            </div>
                
            <?php elseif( is_page() ):
            
                if( get_post_meta( get_the_ID(), 'semi_page_hidetitle', true ) != 1 ):
            
            ?>
            
            <div id="page-title">
            
                <div class="container clearfix">
                
                    <h1><?php the_title(); ?><?php if( get_query_var('paged') != '' ) { echo __( ' - Page ', 'coworker' ) . get_query_var('paged'); } ?><?php echo get_post_meta( get_the_ID(), 'semi_page_caption', TRUE ) ? '<span>' . get_post_meta( get_the_ID(), 'semi_page_caption', TRUE ) . '</span>' : ''; ?></h1>
                    
                    <?php page_title_right(); ?>
                
                </div>
            
            </div>
                
            <?php
            
                endif;
            
            elseif( is_singular( 'portfolio' ) ):
            
            $prev_post = get_previous_post();
            
            $next_post = get_next_post();
            
            ?>
            
            <div id="page-title">
            
                <div class="container clearfix">
                
                    <h1><?php the_title(); ?></h1>
                    
                    <div id="portfolio-navigation">
                    
                        <?php if( $prev_post ) { ?><a href="<?php echo $prev_post->guid; ?>" class="port-nav-prev"><?php _e('Previous', 'coworker'); ?></a><?php } ?>
                        
                        <a href="<?php echo ( semi_option( 'portfolio_page' ) != 0 ) ? get_permalink( semi_option( 'portfolio_page' ) ) : get_post_type_archive_link( get_post_type() ); ?>" class="port-nav-list"><?php _e('Portfolio List', 'coworker'); ?></a>
                        
                        <?php if( $next_post ) { ?><a href="<?php echo $next_post->guid; ?>" class="port-nav-next"><?php _e('Next', 'coworker'); ?></a><?php } ?>
                    
                    </div>
                
                </div>
            
            </div>
            
            <?php
            
            elseif( is_singular( 'product' ) ):
            
            ?>
            
            <div id="page-title">
            
                <div class="container clearfix">
                
                    <h1><?php if( semi_option( 'shop_title' ) != '' ) { echo semi_option( 'shop_title' ); } else { _e( 'Shop', 'coworker' ); } if( semi_option( 'shop_tagline' ) != '' ) { echo '<span>' . semi_option( 'shop_tagline' ) . '</span>'; } ?></h1>
                    
                    <?php page_title_right(); ?>
                
                </div>
            
            </div>
            
            <?php elseif( is_singular( 'features' ) ): ?>
            
            <div id="page-title">
            
                <div class="container clearfix">
                
                    <h1><?php the_title(); ?></h1>
                    
                    <?php page_title_right(); ?>
                
                </div>
            
            </div>
            
            <?php elseif( is_404() ): ?>
            
            <div id="page-title">
            
                <div class="container clearfix">
                
                    <h1><?php if( semi_option( 'error_title' ) != '' ) { echo semi_option( 'error_title' ); } else { _e( 'Page Not Found', 'coworker' ); } if( semi_option( 'error_tagline' ) != '' ) { echo '<span>' . semi_option( 'error_tagline' ) . '</span>'; } ?></h1>
                
                </div>
            
            </div>
            
            <?php elseif( is_search() ): ?>
            
            <div id="page-title">
            
                <div class="container clearfix">
                
                    <h1><?php echo __( 'Search Results: ', 'coworker' ) . '"' . get_search_query() . '"'; ?><?php if( get_query_var('paged') != '' ) { echo __( ' - Page ', 'coworker' ) . get_query_var('paged'); } ?></h1>
                    
                    <?php page_title_right(); ?>
                
                </div>
            
            </div>
            
            <?php elseif( is_tax( 'port-group' ) ): ?>
            
            <div id="page-title">
            
                <div class="container clearfix">
                
                    <h1><?php single_term_title(); ?><?php echo ( strip_tags( term_description() ) != "" ) ? '<span>/ ' . strip_tags( term_description() ) . '</span>' : '' ; ?></h1>
                    
                    <?php page_title_right(); ?>
                
                </div>
            
            </div>
            
            <?php elseif( is_post_type_archive( 'portfolio' ) ): ?>
            
            <div id="page-title">
            
                <div class="container clearfix">
                
                    <h1><?php if( semi_option( 'portfolio_archive_title' ) != '' ) { echo semi_option( 'portfolio_archive_title' ); } else { post_type_archive_title(); } if( semi_option( 'portfolio_archive_tagline' ) != '' ) { echo '<span>' . semi_option( 'portfolio_archive_tagline' ) . '</span>'; } ?></h1>
                    
                    <?php page_title_right(); ?>
                
                </div>
            
            </div>
            
            <?php elseif( is_archive() ): ?>
            
            <div id="page-title">
            
                <div class="container clearfix">
                
                    <h1><?php echo str_replace( get_bloginfo( 'name' ), '', wp_title( '', false ) ); ?></h1>
                    
                    <?php page_title_right(); ?>
                
                </div>
            
            </div>
            
            <?php endif; ?>