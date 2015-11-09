<?php
/*
Template Name: FAQs
*/
?>
<?php get_header(); ?>
        
        
        <?php get_template_part( 'include/content', 'head' ); if (have_posts()) : while (have_posts()) : the_post(); ?>
                
                    
                    <div class="postcontent nobottommargin clearfix<?php page_sidebar_align(); ?>">
                    
                    
                        <?php
                        
                            the_content();
                        
                            $faqscats = wp_get_object_terms( get_the_ID(), 'faqs-group' );
                            
                            $faqscatids = array();
                            
                            if( count( $faqscats ) > 0 ) {
                                
                                foreach ( $faqscats as $faqscat) {
                                    $faqscatids[] = $faqscat->term_id;
                                }
                            
                            }
                            
                            if( $faqscats == false OR count( $faqscats ) > 1 ) {
                            
                                echo '<ul id="portfolio-filter" class="bottommargin clearfix"><li class="activeFilter"><a href="#" data-filter="all">' . __('All', 'coworker') . '</a></li>';
                                
                                if( count( $faqscatids ) > 0 ) {
                                    $terms = get_terms( "faqs-group", array( 'include' => $faqscatids ) );
                                } else {
                                    $terms = get_terms( "faqs-group" );
                                }
                                
                                $count = count( $terms );
                                if ( $count > 0 ){
                                    foreach ( $terms as $term ) {
                                        echo '<li><a href="#" data-filter=".faq-' . $term->slug . '">' . $term->name . '</a></li>';
                                    }
                                }
                                
                                echo '</ul>';
                            
                            }
                            
                        ?>
                        
                        
                        <?php
                        
                        $faqscount = is_numeric( get_post_meta( get_the_ID(), 'semi_page_faqs_items', TRUE ) ) ? get_post_meta( get_the_ID(), 'semi_page_faqs_items', TRUE ) : -1;
                        
                        $faqsorder = get_post_meta( get_the_ID(), 'semi_page_faqs_order', TRUE );
                        
                        $faqsorderby = get_post_meta( get_the_ID(), 'semi_page_faqs_orderby', TRUE );
                        
                        $args = array( 'post_type' => 'faqs', 'posts_per_page' => $faqscount, 'order' => $faqsorder, 'orderby' => $faqsorderby );
                        
                        if( count( $faqscatids ) > 0 ) {
                            $args['tax_query'] = array( array( 'taxonomy' => 'faqs-group', 'field' => 'id', 'terms' => $faqscatids ) );
                        }
                        
                        $faqs = new WP_Query( $args );
                        
                        if( $faqs->have_posts() ):
                        
                        ?>
                        
                        <div id="faqs" class="clearfix">
                        
                            <?php while ( $faqs->have_posts() ) : $faqs->the_post();
                            
                            $getterms = get_the_terms( get_the_ID(), 'faqs-group' );
                            
                            if ( $getterms ) {
                            
                                $faqterms = array();
                                
                                foreach ($getterms as $getterm) {
                                	$faqterms[] = 'faq-' . $getterm->slug;
                                }
                                
                                $faqterms = implode( " ", $faqterms );
                            
                            }
                            
                            ?>
                            
                            <div class="toggle faq <?php echo $faqterms; ?> clearfix">
                                
                                <div class="togglet"><i class="<?php echo get_post_meta( get_the_ID(), 'semi_faq_icon', TRUE ); ?>"></i><?php the_title_attribute(); ?></div>
                                
                                <div class="togglec clearfix">
                                    
                                    <?php echo get_the_content(); ?>
                                    
                                </div>
                            
                            </div>
                            
                            <?php endwhile; ?>
                        
                        </div>
                        
                        <script type="text/javascript">
                        
                            jQuery(document).ready(function($){
                                
                                var $faqItems = $('#faqs .faq');
                                
                                $('#portfolio-filter a').click(function(){
                                
                                    $('#portfolio-filter li').removeClass('activeFilter');
                                    
                                    $(this).parent('li').addClass('activeFilter');
                                    
                                    var faqSelector = $(this).attr('data-filter');
                                    
                                    $faqItems.css('display', 'none');
                                    
                                    if( faqSelector != 'all' ) {
                                    
                                        $( faqSelector ).fadeIn(500);
                                    
                                    } else {
                                    
                                        $faqItems.fadeIn(500);
                                    
                                    }
                                    
                                    return false;
                                
                               });
                            
                            });
                        
                        </script>
                        
                        <?php endif; wp_reset_postdata(); ?>
                        
                        
                    </div>
                    
                    
                    <?php get_sidebar(); ?>
                    
                
        <?php endwhile; endif; get_template_part( 'include/content', 'foot' ); ?>
        
        
<?php get_footer(); ?>