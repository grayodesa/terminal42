<?php $gallery = rwmb_meta( 'semi_port_gallery', 'type=image&size=full' ); ?>
<div id="slider" class="fslider preloader2" <?php echo get_fslider_ops(); ?>>
                        
                        
                            <div class="flexslider">
                        
                        
                                <div class="slider-wrap" data-lightbox="gallery">
                                
                                
                                <?php $thumb = get_resized_image( 680, null, false ); ?>
                                    
                                    <div class="slide">
                                    
                                        <a href="<?php echo get_full_image(); ?>" data-lightbox="gallery-item">
                                        
                                            <img src="<?php echo $thumb[0]; ?>" width="<?php echo $thumb[1]; ?>" height="<?php echo $thumb[2]; ?>" alt="<?php the_title_attribute(); ?>" />
                                        
                                        </a>
                                    
                                    </div>
                                    
                                <?php
                                
                                foreach ( $gallery as $gallery_image ):
                                
                                $gallery_thumb = semi_resize( $gallery_image['full_url'], 680, null, true, false );
                                
                                ?>
                                    
                                    <div class="slide">
                                    
                                        <a href="<?php echo $gallery_image['full_url']; ?>" data-lightbox="gallery-item">
                                        
                                            <img src="<?php echo $gallery_thumb[0]; ?>" width="<?php echo $gallery_thumb[1]; ?>" height="<?php echo $gallery_thumb[2]; ?>" alt="<?php echo $gallery_image['alt']; ?>" />
                                        
                                        </a>
                                    
                                    </div>
                                
                                <?php endforeach; ?>
                                
                                
                                </div>
                            
                            
                            </div>
                        
                        
                        </div>