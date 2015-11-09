<?php

if( semi_option( 'blog_single_related' ) == 1 ):

$exclude = get_the_ID();
$categories = array();
$getcategories = get_the_category();

if( $getcategories ) {
    
    foreach( $getcategories as $category ):
        $categories[] = $category->term_id;
    endforeach;
    
}

$relatedposts = new WP_Query( array( 'post_type' => 'post', 'posts_per_page' => 10, 'category__in' => $categories, 'post__not_in' => array( $exclude ) ) );
if( $relatedposts->have_posts() ):

?>
<div style="position: relative;">
                        
                            <h4><?php _e( '<span>Related</span> Posts', 'coworker' ); ?></h4>
                            
                            <ul id="related-posts-scroller-<?php echo $exclude; ?>" class="related-posts clearfix">
                            
                                <?php while ( $relatedposts->have_posts() ) : $relatedposts->the_post(); ?>
                                
                                <li>
                                
                                    <div class="rpost-image">
                                    
                                        <a href="<?php the_permalink(); ?>" rel="bookmark" title="<?php the_title_attribute(); ?>">
                                            <img class="image_fade" src="<?php echo get_resized_image( 176, 128 ); ?>" alt="<?php the_title_attribute(); ?>" />
                                        </a>
                                        
                                        <div class="post-overlay icon-<?php echo get_post_icon(); ?>"></div>
                                    
                                    </div>
                                    
                                    <div class="rpost-title"><a href="<?php the_permalink(); ?>" rel="bookmark" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a></div>
                                
                                </li>
                                
                                <?php
                                
                                endwhile;
                                
                                ?>
                            
                            </ul>
                            
                            <div class="widget-scroll-prev" id="relatedposts_prev-<?php echo $exclude; ?>"></div>
                            <div class="widget-scroll-next" id="relatedposts_next-<?php echo $exclude; ?>"></div>
                            
                            <script type="text/javascript">
                            
                                var rPostsCarousel = jQuery("#related-posts-scroller-<?php echo $exclude; ?>");
                                
                                rPostsCarousel.carouFredSel({
                                    width : "100%",
                                    height : "auto",
                                    circular : false,
                                    responsive : true,
                                    infinite : false,
                                    auto : false,
                                    items : {
                                        width : 180,
                                        visible: {
                                            min: 2,
                                            max: 5
                                        }
                                    },
                                    scroll : {
                                        wipe: true
                                    },
                                    prev : {
                                        button : "#relatedposts_prev-<?php echo $exclude; ?>",
                                        key : "left"
                                    },
                                    next : {
                                        button : "#relatedposts_next-<?php echo $exclude; ?>",
                                        key : "right"
                                    },
                                    onCreate : function () {
                                        jQuery(window).on('resize', function(){
                                            rPostsCarousel.parent().add(rPostsCarousel).css('height', rPostsCarousel.children().first().outerHeight() + 'px');
                                        }).trigger('resize');
                                    }
                                });
                            
                            </script>
                            
                        
                        </div>
                        
                        
                        <div class="double-line"></div>
                        
                        <?php
                        
                        endif;
                        wp_reset_postdata();
                        
                        endif;
                        
                        ?>