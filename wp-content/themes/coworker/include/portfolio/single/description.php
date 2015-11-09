<div class="port-desc clearfix">
                            
                                <h3><?php if( semi_option( 'portfolio_title_desc' ) != '' ) { echo semi_option( 'portfolio_title_desc' ); } else { _e( 'Project Description', 'coworker' ); } ?></h3>
                                
                                <?php the_content(); ?>
                                
                            </div>