<?php

$itemcount = is_numeric( get_post_meta( get_the_ID(), 'semi_page_portfolio_itemcount', true ) ) ? get_post_meta( get_the_ID(), 'semi_page_portfolio_itemcount', true ) : 12;

$jquery_pag = get_post_meta( get_the_ID(), 'semi_page_port_jquery_pagi', true );

if( $jquery_pag == 1 ):
	$itemcount_p = -1;
else:
	$itemcount_p = $itemcount;
endif;

$args = array( 'post_type' => 'portfolio', 'orderby' => 'menu_order', 'order' => 'ASC', 'posts_per_page' => $itemcount_p, 'paged' => get_query_var('paged') );

if( choosen_port_cat() != false ) {
    $args['tax_query'] = array( array( 'taxonomy' => 'port-group', 'field' => 'id', 'terms' => choosen_port_cat() ) );
}

$portfolio = new WP_Query( $args );

if( $portfolio->have_posts() ):

	if( $jquery_pag == 1 ):

?>
<div class="portfolio-container"><?php endif; ?>

	<div id="portfolio" class="portfolio-3 clearfix">
	                        
	                            <?php while ( $portfolio->have_posts() ) : $portfolio->the_post();
	                            
	                            get_template_part( 'include/portfolio/items', '3' );
	                            
	                            endwhile; ?>
	                        
	                        </div>

	                    <?php if( $jquery_pag == 1 ): ?>

							<div class="pagination pagination-right topmargin nobottommargin">
	                        
	                            <ul class="clearfix"></ul>
	                        
	                        </div>

						</div>

						<?php endif; ?>

                        <div class="clear"></div>

						<?php if( $jquery_pag == 1 ): ?>
                        
                        <script type="text/javascript">
	                    
	                        jQuery(document).ready(function($){
	                        
	                            $('.portfolio-container').pajinate({
	            					items_per_page : <?php echo $itemcount; ?>,
	            					item_container_id : '#portfolio',
	            					nav_panel_id : '.pagination ul',
	                                show_first_last: false
	            				});
	                            
	                            $( '.pagination a' ).click(function() {
	                                var t=setTimeout(function(){ $( '.flexslider .slide' ).resize(); },1000);
	                            });
	                        
	                        });
	                    
	                    </script>

						<?php else: ?>

						<?php if( $portfolio->found_posts > 1 ) { semi_pagination( $portfolio->max_num_pages ); } ?>
                        
                        <?php endif; endif; wp_reset_postdata(); ?>