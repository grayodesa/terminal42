<?php $gallery = rwmb_meta( 'semi_port_gallery', 'type=image&size=full-width' ); ?>
<div id="slider" class="fslider preloader2" <?php echo get_fslider_ops(); ?>>
                        
                        
                            <div class="flexslider">
                        
                        
                                <div class="slider-wrap" data-lightbox="gallery">
                                
                                
                                <?php $thumb = get_sized_image( 'full-width', true ); ?>
                                    
                                    <div class="slide">
                                    
                                        <a href="<?php echo get_full_image(); ?>" data-lightbox="gallery-item">
                                        
                                            <img src="<?php echo $thumb[0]; ?>" width="<?php echo $thumb[1]; ?>" height="<?php echo $thumb[2]; ?>" alt="<?php the_title_attribute(); ?>" />
                                        
                                        </a>
                                    
                                    </div>
                                    
                                <?php
                                
                                foreach ( $gallery as $gallery_image ):
                                
                                ?>
                                    
                                    <div class="slide">
                                    
                                        <a href="<?php echo $gallery_image['full_url']; ?>" data-lightbox="gallery-item">
                                        
                                            <img src="<?php echo $gallery_image['url']; ?>" width="<?php echo $gallery_image['width']; ?>" height="<?php echo $gallery_image['height']; ?>" alt="<?php echo $gallery_image['alt']; ?>" />
                                        
                                        </a>
                                    
                                    </div>
                                
                                <?php endforeach; ?>
                                
                                
                                </div>
                            
                            
                            </div>
                        
                        
                        </div>