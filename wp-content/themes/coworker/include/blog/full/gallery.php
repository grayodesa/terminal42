<?php

$gallery = rwmb_meta( 'semi_post_gallery', 'type=image&size=full-width' );

?>
<div class="entry_image<?php if( is_single() AND get_post_meta( get_the_ID(), 'semi_post_single_layout', TRUE ) == 'split' ) echo 'nobottommargin'; ?>">
                                
                                    <div class="fslider" <?php echo get_fslider_ops( 'semi_post_' ); ?>>
                                    
                                        <div class="flexslider">
                                        
                                        
                                            <div class="slider-wrap" data-lightbox="gallery">
                                            
                                            <?php if( has_post_thumbnail() ):

                                            $thumb = get_sized_image( 'full-width', true );
                                            
                                            ?>
                                                
                                                <div class="slide">
                                                
                                                    <a href="<?php echo get_full_image(); ?>" data-lightbox="gallery-item">
                                                    
                                                        <img src="<?php echo $thumb[0]; ?>" width="<?php echo $thumb[1]; ?>" height="<?php echo $thumb[2]; ?>" alt="<?php the_title_attribute(); ?>" />
                                                    
                                                    </a>
                                                
                                                </div>
                                                
                                            <?php 
                                            
                                            endif;
                                            
                                            foreach ( $gallery as $gallery_image ): ?>
                                                
                                                <div class="slide">
                                                
                                                    <a href="<?php echo $gallery_image['full_url']; ?>" data-lightbox="gallery-item">
                                                    
                                                        <img src="<?php echo $gallery_image['url']; ?>" width="<?php echo $gallery_image['width']; ?>" height="<?php echo $gallery_image['height']; ?>" alt="<?php echo $gallery_image['alt']; ?>" />
                                                    
                                                    </a>
                                                
                                                </div>
                                            
                                            <?php endforeach; ?>
                                            
                                            </div>
                                        
                                        
                                        </div>
                                    
                                    </div>
                                
                                
                                </div>