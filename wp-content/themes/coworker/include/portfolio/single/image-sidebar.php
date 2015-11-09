<?php $thumb = get_sized_image( 'large', true ); ?>
<div id="slider">
                        
                        
                            <a href="<?php echo get_full_image(); ?>" data-lightbox="image" class="image_fade"><img src="<?php echo $thumb[0]; ?>" width="<?php echo $thumb[1]; ?>" height="<?php echo $thumb[2]; ?>" alt="<?php the_title_attribute(); ?>" /></a>
                        
                        
                        </div>