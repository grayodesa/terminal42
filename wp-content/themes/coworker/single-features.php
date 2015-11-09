<?php get_header(); ?>


        <?php get_template_part( 'include/content', 'head' ); if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
                
                    <div class="col_one_third nobottommargin">
                    
                        <?php
                        
                        $thisfeature = get_the_ID();
                        
                        $sidenav = new WP_Query( array( 'post_type' => 'features', 'posts_per_page' => -1, 'order' => 'ASC', 'orderby' => 'menu_order' ) );
                        
                        if( $sidenav->have_posts() ):
                        
                        ?>
                        
                        <ul class="sidenav nomargin">
                        
                        <?php while ( $sidenav->have_posts() ) : $sidenav->the_post();
                        
                        $featureicon = ( get_post_meta( get_the_ID(), 'semi_feature_icon', TRUE ) != 'none' ) ? '<i class="' . get_post_meta( get_the_ID(), 'semi_feature_icon', TRUE ) . '"></i>' : '';
                        
                        ?>
                        
                            <li<?php if( get_the_ID() == $thisfeature ) { echo ' class="active"'; } ?>><a href="<?php the_permalink(); ?>"><?php echo $featureicon; ?><i class="icon-chevron-right"></i> <?php the_title_attribute(); ?></a></li>
                        
                        <?php endwhile; ?>
                        
                        </ul>
                        
                        <?php endif; wp_reset_postdata(); ?>
                    
                    </div>
                    
                    
                    <div class="col_two_third col_last nobottommargin">
                    
                    
                        <?php if( has_post_thumbnail() ):
                        
                        $thumb = get_sized_image( 'large', true );
                        
                        ?>
                        
                        <img class="aligncenter notopmargin bottommargin" src="<?php echo $thumb[0]; ?>" alt="<?php the_title_attribute(); ?>" title="<?php the_title_attribute(); ?>" />
                        
                        <?php endif; ?>
                        
                        <div class="entry_content clearfix">
                        
                            <?php the_content(); ?>
                        
                        </div>
                    
                    
                    </div>
                
        <?php endwhile; endif; get_template_part( 'include/content', 'foot' ); ?>


<?php get_footer(); ?>