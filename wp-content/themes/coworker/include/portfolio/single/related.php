<?php

$exclude = get_the_ID();
$categories = array();
$getcategories = get_the_terms( get_the_ID(), 'port-group' );

if( $getcategories ) {
    
    foreach( $getcategories as $category ):
        $categories[] = $category->term_id;
    endforeach;
    
}

$relatedportfolio = new WP_Query( array( 'post_type' => 'portfolio', 'posts_per_page' => 10, 'post__not_in' => array( $exclude ), 'tax_query' => array( array( 'taxonomy' => 'port-group', 'field' => 'id', 'terms' => $categories ) ) ) );

if( $relatedportfolio->have_posts() ):

?>
<div class="clear"></div>
                        
                        
                        <div class="dotted-divider"></div>
                        
                        
                        <div id="portfolio-related">
                        
                            <h4><?php _e( 'Related Projects:', 'coworker' ); ?></h4>
                            
                            <ul id="portfolio-related-items" class="clearfix">
                            
                                <?php while ( $relatedportfolio->have_posts() ) : $relatedportfolio->the_post(); ?>
                                
                                <li>
                                
                                    <a href="<?php the_permalink(); ?>" rel="bookmark" title="<?php the_title_attribute(); ?>">
                                        <img src="<?php echo get_resized_image( 188, 146 ); ?>" alt="<?php the_title_attribute(); ?>" />
                                    </a>
                                    
                                    <div class="portfolio-overlay">
                                        
                                        <div class="p-overlay-icons clearfix"><a href="<?php the_permalink(); ?>" class="p-o-<?php echo get_post_meta( get_the_ID(), 'semi_port_type', TRUE ); ?>"></a></div>
                                    
                                    </div>
                                
                                </li>
                                
                                <?php endwhile; ?>
                            
                            </ul>
                            
                            <div id="related-portfolio-prev" class="widget-scroll-prev"></div>
                            <div id="related-portfolio-next" class="widget-scroll-next"></div>
                            
                            <script type="text/javascript">
                            
                                var portfolioRelatedItems = jQuery("#portfolio-related-items");
                                
                                portfolioRelatedItems.carouFredSel({
                                    width: "100%",
                                    height: "auto",
                                    circular : false,
                                	infinite : false,
                                    responsive: true,
                                	auto : false,
                                    items : {
                                        width  : 200,
                                		visible: {
                                			min: 2,
                                			max: 5
                                		}
                                	},
                                    scroll : {
                                        wipe : true
                                    },
                                    prev : {
                                		button : "#related-portfolio-prev",
                                		key : "left"
                                	},
                                	next : {
                                		button : "#related-portfolio-next",
                                		key : "right"
                                	},
                                    onCreate : function () {
                                        jQuery(window).on('resize', function(){
                                            portfolioRelatedItems.parent().add(portfolioRelatedItems).css('height', portfolioRelatedItems.children().first().outerHeight() + 'px');
                                        }).trigger('resize');
                                    }
                                });
                            
                            </script>
                        
                        </div>
                        
                        <?php endif; wp_reset_postdata(); ?>